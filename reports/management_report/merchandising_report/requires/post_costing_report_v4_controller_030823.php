<?
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');

/*require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php');*/

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="order_no_search_popup")
{
		echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
	 ?>
     
	<script>
		
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
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	
    </script>

	</head>

<body>
 <div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'post_costing_report_v4_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

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

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$report_type=str_replace("'","",$report);
	
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="") $po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		else $po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
	}
	
	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0) $shipping_status_cond=" and b.shiping_status=$shipping_status";
	
	$po_ids_array=array();
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
		
	$sql="select a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field,a.job_quantity, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $po_id_cond $ref_cond $file_cond $shipping_status_cond $year_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
	// echo $sql; //die;
	$result=sql_select($sql);
	$all_po_id="";	$all_job_id="";
	foreach($result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		//if($all_job_id=="") $all_job_id="'".$row[csf("job_no")]."'"; else $all_job_id.=","."'".$row[csf("job_no")]."'";
		if($all_job_id=="") $all_job_id=$row[csf("job_id")]; else $all_job_id.=",".$row[csf("job_id")];
		$job_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		$job_arr2[$row[csf("job_no")]]['po_quantity']=$row[csf("po_quantity")];
		$job_arr2[$row[csf("job_no")]]['job_quantity']=$row[csf("job_quantity")];
	}
	if($all_po_id=="") 
	{ 
		echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
	}
	//	echo $all_job_id;die;
	$jobIds=chop($all_job_id,',');
	//echo $jobIds;die;
	$job_cond_for_in=""; $jobIdCond="";
	$job_ids=count(array_unique(explode(",",$all_job_id)));
	if($db_type==2 && $job_ids>1000)
	{
		$job_cond_for_in=" and (";
		$jobIdCond=" and (";
		$jobIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($jobIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$job_cond_for_in.=" a.job_id in($ids) or"; 
			$jobIdCond.=" a.job_no_id in($ids) or"; 
		}
		$job_cond_for_in=chop($job_cond_for_in,'or ');
		$job_cond_for_in.=")";
		$jobIdCond=chop($jobIdCond,'or ');
		$jobIdCond.=")";
	}
	else
	{
		$jobIds=implode(",",array_unique(explode(",",$all_job_id)));
		$job_cond_for_in=" and a.job_id in($jobIds)";
		$jobIdCond=" and a.job_no_id in($jobIds)";
	}
	//echo $job_cond_for_in;die;
	
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";$po_cond_for_in3=""; 
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$po_cond_for_in2=" and (";
		$po_cond_for_in3=" and (";
		$po_cond_for_in4=" and (";
		$po_cond_for_in5=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
			$po_cond_for_in2.=" po_id in($ids) or"; 
			$po_cond_for_in3.=" b.order_id in($ids) or";
			$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
			$po_cond_for_in5.=" b.po_id in($ids) or"; 
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
		
		$po_cond_for_in2=chop($po_cond_for_in2,'or ');
		$po_cond_for_in2.=")";
		$po_cond_for_in3=chop($po_cond_for_in3,'or ');
		$po_cond_for_in3.=")";
		$po_cond_for_in4=chop($po_cond_for_in4,'or ');
		$po_cond_for_in4.=")";
		$po_cond_for_in5=chop($po_cond_for_in5,'or ');
		$po_cond_for_in5.=")";
	}
	else
	{
		$poIds=implode(",",array_unique(explode(",",$all_po_id)));
		$po_cond_for_in=" and b.po_break_down_id in($poIds)";
		$po_cond_for_in2=" and po_id in($poIds)";
		$po_cond_for_in3=" and b.order_id in($poIds)";
		$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";
		$po_cond_for_in5=" and b.po_id in($poIds)";
	}
		
	if($poIds!='' || $poIds!=0) $po_cond_for_in=$po_cond_for_in; 
	else $po_cond_for_in="and b.po_break_down_id in(0)";
	
	$po_idArr=array_unique(explode(",",$all_po_id));
	$po_id_cond_for_in=where_con_using_array($po_idArr,0,"e.po_id"); 
	
	$ActualArray_sql=sql_select("select a.id,b.pr_date, b.man_power, b.target_per_hour, e.working_hour,e.operator,e.helper,c.from_date,c.to_date,c.capacity,e.po_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size e where a.id=c.mst_id and c.id=b.mast_dtl_id and e.mst_id=a.id and e.dtls_id=c.id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $po_id_cond_for_in");
	//echo "select a.id,b.pr_date, b.man_power, b.target_per_hour, e.working_hour,e.operator,e.helper,c.from_date,c.to_date,c.capacity,e.po_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size e where a.id=c.mst_id and c.id=b.mast_dtl_id and e.mst_id=a.id and e.dtls_id=c.id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $po_id_cond_for_in";
	 
	 
	
		foreach($ActualArray_sql as $val)
		{
			//$pr_date=date('m-Y',$val[csf('pr_date')]);
			$pr_date=date("Y-m",strtotime(($val[csf('pr_date')])));
			//echo $pr_date;
			$prod_resource_array[$val[csf('po_id')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('po_id')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('po_id')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('po_id')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('po_id')]]['operator_helper']=$val[csf('operator')]+$val[csf('helper')];
			$prod_resource_array[$val[csf('po_id')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('po_id')]]['pr_date']=$val[csf('pr_date')];
			//$prod_resource_array[$val[csf('po_id')]]['day_start']=$val[csf('from_date')];
			$actual_prod_id_arr[$val[csf('id')]]=$val[csf('id')];
			 
		}
		$act_id_cond_for_in=where_con_using_array($actual_prod_id_arr,0,"a.id"); 
		$ActualExtraArray_sql=sql_select("select a.id,b.pr_date, b.man_power, e.style_ref_no,e.number_of_emp, e.adjust_hour as working_hour,c.from_date,c.to_date,c.capacity,e.style_ref_no from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_smv_adj e where a.id=c.mst_id and c.id=b.mast_dtl_id and e.mst_id=a.id and e.dtl_id=b.id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $act_id_cond_for_in");
		//echo "select a.id,b.pr_date, b.man_power, e.style_ref_no,e.number_of_emp, e.adjust_hour as working_hour,c.from_date,c.to_date,c.capacity,e.style_ref_no from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_smv_adj e where a.id=c.mst_id and c.id=b.mast_dtl_id and e.mst_id=a.id and e.dtl_id=b.id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $act_id_cond_for_in";
	
		foreach($ActualExtraArray_sql as $val)
		{
			$pr_date=date("Y-m",strtotime(($val[csf('pr_date')])));
			$adj_resource_array[$val[csf('style_ref_no')]]['working_hour']+=$val[csf('working_hour')];
			$adj_resource_array[$val[csf('style_ref_no')]]['pr_date']=$val[csf('pr_date')];
			$adj_resource_array[$val[csf('style_ref_no')]]['number_of_emp']+=$val[csf('number_of_emp')];
		}
		unset($ActualExtraArray_sql);
		//print_r($prod_resource_array);
		$sql_cpm="select monthly_cm_expense,applying_period_date,applying_period_to_date, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn, interest_expense, income_tax from lib_standard_cm_entry where company_id=$company_name   and status_active=1 and is_deleted=0";
	 
	$lib_data_array=sql_select($sql_cpm);
	foreach ($lib_data_array as $row)
	{
	  $applying_period_date=$row[csf("applying_period_date")];
	  $from_cmp_date=date("Y-m",strtotime(($applying_period_date)));
	 $lib_cpm_array[$from_cmp_date]['cpm']=$row[csf('cost_per_minute')];
	}
	unset($lib_data_array);
	
	
	$ex_factory_arr=return_library_array( "select b.po_break_down_id, 
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id", "po_break_down_id", "qnty");	
	
	ob_start();
	?>
    <fieldset>
    	<table width="4730">
            <tr class="form_caption">
                <td colspan="36" align="center"><strong>Post Costing Report V4</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="36" align="center"><strong><? echo $company_arr[$company_name];?></strong>
                    <br>
                    <strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
                </td>
            </tr>
        </table>
        <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="4730" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
                <th width="60">Job Year</th>
                <th width="70">Job No</th>
                <th width="100">PO No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="110">Style Name</th>
                <th width="120">Garments Item</th>
                <th width="90">PO Quantity</th>
                <th width="50">UOM</th>
                <th width="70">Unit Price</th>
                <th width="110">PO Value</th>
                <th width="100">SMV</th>
                <th width="80">Shipment Date</th>
                <th width="100">Shipping Status</th>
                <th width="100">Cost Source</th>
                <th width="100">PO/Ex-Factory Qnty</th>
                <th width="110">PO/Ex-Factory Value</th>
                <th width="110">Grey Yarn Cost</th>
                <th width="100">Yarn Value Addition Cost</th>
                <th width="110">Knitting Cost</th>
                <th width="100">Grey Fabric Transfer Cost</th>
              
                <th width="100">Grey Fabric Cost</th>
                
                <th width="110">Dye & Fin Cost</th>
                <th width="110">AOP Cost</th>
                <th width="100">Fin. Fab. Transfer Cost</th>
                <th width="100">Fin.Fabric Purchase Cost</th>
                <th width="110">Grey Fabric Purchease Cost</th>
                <th width="100">Finished Fabric Cost</th>
                <th width="100" title="Fin.Fabric Purchase Cost+Finished Fabric Cost">Total Fabric Cost</th>
                <th width="110">Trims Cost</th>
                <th width="100">Printing Cost</th>
                <th width="100">Embroidery Cost</th>
				<th width="100">Others Cost</th>
                <th width="100">Gmt Dyeing Cost</th>
                <th width="100">Washing Cost</th>
               
                <th width="100">Commission Cost</th>
                <th width="100">Commercial Cost</th>
                <th width="100">Freight Cost</th>
                <th width="100">Testing Cost</th>
                <th width="100">Inspection Cost</th>
                <th width="100">Courier Cost</th>
                <th width="100">CM Cost</th>
                <th width="100">Total Cost</th>
                <th width="100">Margin/Loss</th>
                <th  width="100">% to Ex-Factory Value</th>
                <th width="100">CPA/Short Fab. Cost</th>
                <th width="">Net Margin/Loss</th>
            </thead>
        </table>
    	<div style="width:4750px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="4730" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
			$process_costing=return_field_value("process_costing_maintain", "variable_settings_production", "company_name=$company_name and variable_list=34 and is_deleted=0 and status_active=1");
		//	echo $process_costing.'ddd';die;
			
			$usd_id=2;
			$fabriccostArray=array(); $yarncostArray=array(); $trimsCostArray=array(); $prodcostArray=array(); $actualCostArray=array(); $actualTrimsCostArray=array(); 
			$subconCostArray=array(); $embellCostArray=array(); $washCostArray=array(); $aopCostArray=array(); $yarnTrimsCostArray=array(); 
			$yarncostDataArray=sql_select("select a.job_no, sum(a.amount) as amnt, sum(a.rate*a.avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls a where a.status_active=1 and a.is_deleted=0 $job_cond_for_in group by a.job_no");
			//echo "select a.job_no, sum(a.amount) as amnt, sum(a.rate*a.avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls a where a.status_active=1 and a.is_deleted=0 $job_cond_for_in group by a.job_no";die;
			foreach($yarncostDataArray as $yarnRow)
			{
			   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
			}
			unset($yarncostDataArray);
			$fabriccostDataArray=sql_select("select a.job_no, a.costing_per_id, a.embel_cost, a.wash_cost, a.cm_cost, a.commission, a.currier_pre_cost, a.lab_test, a.inspection, a.freight, a.comm_cost from wo_pre_cost_dtls a where a.status_active=1 and a.is_deleted=0 $job_cond_for_in");
			foreach($fabriccostDataArray as $fabRow)
			{
				 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
			}

			// echo "<pre>";
			// print_r($fabriccostArray);
			unset($fabriccostDataArray);
			$prodcostDataArray="select a.body_part_id,a.lib_yarn_count_deter_id as deter_id,b.po_break_down_id as po_id,
								  avg(CASE WHEN c.cons_process=1 THEN c.charge_unit END) AS knit_charge,
								  avg(CASE WHEN c.cons_process=30 THEN c.charge_unit END) AS yarn_dye_charge,
								  avg(CASE WHEN c.cons_process=35 THEN c.charge_unit END) AS aop_charge,
								  avg(CASE WHEN c.cons_process not in(1,2,30,35,134) THEN c.charge_unit END) AS dye_finish_charge
								  from wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls b where a.id=c.fabric_description and a.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=c.job_no and c.status_active=1 and c.is_deleted=0  $po_cond_for_in group by  a.lib_yarn_count_deter_id,b.po_break_down_id,a.body_part_id";
			$resultfab_arr = sql_select($prodcostDataArray);
			foreach($resultfab_arr as $prodRow)
			{
				//echo $prodRow[csf('knit_charge')].',';
				$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['knit_charge']=$prodRow[csf('knit_charge')];
				$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
				$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['aop_charge']=$prodRow[csf('aop_charge')];
				$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
			}	
			unset($resultfab_arr);
			//order_wise_pro_details
			//$finProd_grey_usedArr=return_library_array( "select b.po_breakdown_id, sum(b.grey_used_qty) as grey_used_qty from order_wise_pro_details b where  b.entry_form=7 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id", "po_breakdown_id", "grey_used_qty");
			if($poIds!='' || $poIds!=0)
			{
				$po_cond_for_in4=$po_cond_for_in4; 
			}
			else
			{
				$po_cond_for_in4="and b.po_breakdown_id in(0)";
			}
			$finProd_grey_used_sql = "select c.body_part_id,c.fabric_description_id as deter_id,b.po_breakdown_id as po_id, b.quantity as grey_used_qty from order_wise_pro_details b,pro_finish_fabric_rcv_dtls c where  b.dtls_id=c.id and b.entry_form=7 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 ";
			$fingrey_used_data =sql_select($finProd_grey_used_sql );
			foreach($fingrey_used_data as $row)
			{
				//echo $prodRow[csf('knit_charge')].',';
				//$finProd_grey_used=$finProd_grey_usedArr[$row[csf('po_id')]];
				$fin_dye_finish_charge=$prodcostArray[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
				$fin_grey_used_arr[$row[csf('po_id')]]['grey_used_amt']+=$row[csf('grey_used_qty')]*$fin_dye_finish_charge;
				//echo $row[csf('qnty')].'='.$dye_finish_charge.',';
			}
			unset($fingrey_used_data);
			
		
			$grey_used_sql = "select a.body_part_id,a.fabric_description_id as deter_id,b.qnty,b.po_breakdown_id as po_id from pro_finish_fabric_rcv_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=66 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4";
			$grey_used_data =sql_select($grey_used_sql );
			foreach($grey_used_data as $row)
			{
				//echo $prodRow[csf('knit_charge')].',';
				$finProd_grey_used=$finProd_grey_usedArr[$row[csf('po_id')]];
				$dye_finish_charge=$prodcostArray[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
				$grey_used_arr[$row[csf('po_id')]]['grey_used_amt']+=$row[csf('qnty')]*$dye_finish_charge;
				//echo $row[csf('qnty')].'='.$dye_finish_charge.',';
			}
			unset($grey_used_data);
			if($jobIds!='' || $jobIds!=0)
			{
				$job_cond_for_in=$job_cond_for_in; 
				$jobIdCond=$jobIdCond;
			}
			else
			{
				$job_cond_for_in="and a.job_no in('0')";
				$jobIdCond="and a.job_no in('0')";
			}
			$all_job_IdArr=array_unique(explode(",",$all_job_id));
			$jobId_cond_for_in=where_con_using_array($all_job_IdArr,0,"a.job_no_id"); 
			
			$yarn_dyeing_costArray=array(); //product_id
			 $yarndyeing_sql="select b.booking_date,b.id,b.currency,b.is_short,b.ydw_no,a.job_no,a.yarn_color,a.product_id,avg(a.dyeing_charge) as dyeing_charge,  sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a where  b.id=a.mst_id and b.entry_form in(41,94) and a.status_active=1 and a.is_deleted=0 $jobId_cond_for_in group by b.id,b.currency,b.is_short,a.job_no,a.product_id,b.ydw_no,a.yarn_color,b.booking_date";
			$yarndyeing_result = sql_select($yarndyeing_sql);
			foreach($yarndyeing_result as $yarnRow)
			{
				$yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job']=$yarnRow[csf('job_no')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['ydw_no']=$yarnRow[csf('ydw_no')];
				$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['booking_date']=$yarnRow[csf('booking_date')];
				$yarn_dyeing_curr_arr[$yarnRow[csf('ydw_no')]]['currency']=$yarnRow[csf('currency')];
				$yarn_dyeing_rate_arr[$yarnRow[csf('job_no')]][$yarnRow[csf('yarn_color')]]['rate']=$yarnRow[csf('dyeing_charge')];
				
				$yarn_dyeing_mst_idArr[$yarnRow[csf('id')]]=$yarnRow[csf('id')];
			}
			//print_r($yarn_dyeing_curr_arr);
			unset($resultyarnIssueData);	
				
			$yarnIssueData="select b.prod_id,sum(a.cons_amount) as cons_amount,sum(a.cons_quantity) as cons_quantity
				from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1) and a.transaction_type in(2,4,5,6) and b.entry_form ='3' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25)  $po_cond_for_in4  group by  b.prod_id";
			$resultyarnIssueData = sql_select($yarnIssueData);
			$all_prod_ids="";
			foreach($resultyarnIssueData as $row)
			{
				if($all_prod_ids=="") $all_prod_ids=$row[csf('prod_id')];else $all_prod_ids.=",".$row[csf('prod_id')];
				
				$yarn_issue_amt_arr[$row[csf('prod_id')]]['amt']+=$row[csf('cons_amount')];
				$yarn_issue_amt_arr[$row[csf('prod_id')]]['qty']+=$row[csf('cons_quantity')];
			}
			unset($resultyarnIssueData);
			//echo $all_prod_ids;
			//print_r($yarn_issue_amt_arr);
			$prodIds=chop($all_prod_ids,',');
			$prod_cond_for_in="";
			$prod_ids=count(array_unique(explode(",",$all_prod_ids)));
			if($db_type==2 && $prod_ids>1000)
			{
				$prod_cond_for_in=" and (";
				$prodIdsArr=array_chunk(explode(",",$prodIds),999);
				foreach($prodIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$prod_cond_for_in.=" a.prod_id in($ids) or"; 
				}
				$prod_cond_for_in=chop($prod_cond_for_in,'or ');
				$prod_cond_for_in.=")";
			}
			else
			{
				$prodIds=implode(",",array_unique(explode(",",$all_prod_ids)));
				$prod_cond_for_in=" and a.prod_id in($prodIds)";
			}
			
			if($prodIds!='' || $prodIds!=0)
			{
				$prod_cond_for_in=$prod_cond_for_in;
			}
			else
			{
				$prod_cond_for_in="and a.prod_id in(0)";
			}
					
			 $sql_receive_for_issue="select c.booking_id,a.job_no,a.prod_id,max(a.transaction_date) as transaction_date,c.currency_id,c.receive_purpose,b.lot,b.color, 
			 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt, sum(CASE WHEN c.receive_purpose in(2,15,38) THEN a.order_amount ELSE 0 END) AS order_amount_recv
			  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $prod_cond_for_in  group by c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.lot,b.color";
			$resultReceive_chek = sql_select($sql_receive_for_issue);
			
			foreach($resultReceive_chek as $row)
			{
				$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose']=$row[csf('receive_purpose')];
				$receive_date_array[$row[csf('prod_id')]]['last_trans_date']=$row[csf('transaction_date')];
				$avg_rate=$row[csf('amnt')]/$row[csf('qty')];
				$receive_array[$row[csf('prod_id')]]=$avg_rate;
				if($row[csf('prod_id')]!="")
				{
					$prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
				}
			}
			//print_r($prod_id_arr);die;
			unset($resultReceive_chek);
			
			//$jobIdArr=array_unique(explode(",",$all_job_id));
			$YdId_cond_for_in=where_con_using_array($yarn_dyeing_mst_idArr,0,"c.booking_id"); 
			
			
			$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit");
			//$receive_array=array();//$job_cond_for_in  $prod_cond_for_in
			 $sql_receive="select c.recv_number,c.booking_id,a.job_no,a.prod_id,max(a.transaction_date) as transaction_date,c.receive_purpose,b.lot,b.color,c.currency_id,
			 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt,
			 sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv,
			  sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.cons_quantity ELSE 0 END) AS cons_quantity
			  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id  and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1  $YdId_cond_for_in  and a.is_deleted=0    group by c.recv_number,c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.lot,b.color";
			$resultReceive = sql_select($sql_receive);$all_recv_prod_ids="";
			foreach($resultReceive as $invRow)
			{
				if($all_recv_prod_ids=="") $all_recv_prod_ids=$invRow[csf('prod_id')];else $all_recv_prod_ids.=",".$invRow[csf('prod_id')];
				$receive_curr_array[$invRow[csf('prod_id')]]=$invRow[csf('currency_id')];
				$jobNo=$yarn_dyeing_costArray2[$invRow[csf('booking_id')]]['job'];
				$ydw_no=$yarn_dyeing_costArray2[$invRow[csf('booking_id')]]['ydw_no'];
				$is_short=$yarn_dyeing_isshort_arr[$ydw_no]['is_short'];
				//echo $jobNo.'=='.$invRow[csf('booking_id')].'b';
				$yarn_rec_job_arr[$invRow[csf('recv_number')]]['job']=$jobNo;
				$yarn_rec_job_arr[$invRow[csf('recv_number')]]['ydw_no']=$ydw_no;
				$booking_date=$yarn_dyeing_isshort_arr[$ydw_no]['booking_date'];
				$dye_rate=$yarn_dyeing_rate_arr[$jobNo][$invRow[csf('color')]]['rate'];
					//echo $ydw_no.'d'.$dye_rate;
				$currency=$yarn_dyeing_curr_arr[$ydw_no]['currency'];
				//$po_quantity=$job_arr2[$jobNo]['po_quantity'];
				$order_amount_recv=$invRow[csf('order_amount_recv')];
				$cons_quantity=$invRow[csf('cons_quantity')];
				$transaction_date=$booking_date;
				if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
				
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
			
				if($currency==1) $avg_rate=$dye_rate/$currency_rate;//Taka
				else $avg_rate=$dye_rate;	
				//echo $is_short.'='.$avg_rate.'='.$cons_quantity.', ';
				if($is_short==2) $yarn_receive_amount_arr[$jobNo]['order_amount_recv']+=$avg_rate*$cons_quantity;//Main Booking..
				else $yarn_receive_amount_arr[$jobNo]['cpa_order_amount_recv']+=$avg_rate*$cons_quantity;	
			}
		//	print_r($yarn_receive_amount_arr);
			//echo $all_recv_prod_ids;
			unset($resultReceive);
			 
			if($all_recv_prod_ids!="")
			{
				$recvprodIds=chop($all_recv_prod_ids,',');
				$recv_prod_cond_for_in="";
				$recv_prod_ids=count(array_unique(explode(",",$all_recv_prod_ids)));
				if($db_type==2 && $recv_prod_ids>1000)
				{
					$recv_prod_cond_for_in=" and (";
					$recvprodIdsArr=array_chunk(explode(",",$recvprodIds),999);
					foreach($recvprodIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$recv_prod_cond_for_in.=" a.prod_id in($ids) or"; 
					}
					$recv_prod_cond_for_in=chop($recv_prod_cond_for_in,'or ');
					$recv_prod_cond_for_in.=")";
				}
				else
				{
					$recprodIds=implode(",",array_unique(explode(",",$all_recv_prod_ids)));
					$recv_prod_cond_for_in=" and a.prod_id in($recprodIds)";
				}
			}
			//echo $recv_prod_cond_for_in;
			$sql_receive_ret="select a.prod_id,b.lot,b.color,c.received_mrr_no,
			 sum(a.cons_quantity) as cons_quantity
			  from inv_transaction a,product_details_master b,inv_issue_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=8 and c.item_category=1 and a.transaction_type=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $recv_prod_cond_for_in  group by a.prod_id,b.lot,b.color,c.received_mrr_no";
			$resultReceive_ret = sql_select($sql_receive_ret);
			
			foreach($resultReceive_ret as $row)
			{
				$ydw_no=$yarn_rec_job_arr[$row[csf('received_mrr_no')]]['ydw_no'];
				$recv_job=$yarn_rec_job_arr[$row[csf('received_mrr_no')]]['job'];
				$is_short=$yarn_dyeing_isshort_arr[$ydw_no]['is_short'];
				$booking_date=$yarn_dyeing_isshort_arr[$ydw_no]['booking_date'];
				$ydye_rate=$yarn_dyeing_rate_arr[$jobNo][$row[csf('color')]]['rate'];
				$currency=$yarn_dyeing_curr_arr[$ydw_no]['currency'];
				//echo $recv_job.'d'.$currency;
				$transaction_date=$booking_date;
				if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
				
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				if($currency==1) $avg_rate=$ydye_rate/$currency_rate;//Taka
				else $avg_rate=$ydye_rate;	
				
				$ret_amt=$row[csf('cons_quantity')]*$avg_rate;
				//$receive_ret_arr[$row[csf('prod_id')]]['last_trans_date']=$row[csf('cons_quantity')];
				if($is_short==2) $receive_ret_arr[$recv_job]['recv_ret_amt']+=$ret_amt; //Main Booking
				else $receive_ret_arr[$recv_job]['cpa_recv_ret_amt']+=$ret_amt;
			}
			unset($resultReceive_ret);
			//print_r($receive_ret_arr);
			//booking_type,is_short
			if($poIds!='' || $poIds!=0) $po_cond_for_in=$po_cond_for_in; 
			else $po_cond_for_in="and b.po_break_down_id in(0)";
			
			$sql_wo_aop="select a.id,a.booking_date,a.currency_id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.po_break_down_id as po_id,
			(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty,
			(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount
			 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in";
			// echo  $sql_wo_aop;
			$result_aop_rate=sql_select( $sql_wo_aop );
			foreach ($result_aop_rate as $row)
			{
				if($row[csf('item_category')]==12)
				{
					$wo_qnty=$row[csf('wo_qnty')];
					$amount=$row[csf('amount')];
					$avg_wo_aop_rate=$amount/$wo_qnty;
					$aop_prod_array[$row[csf('po_id')]]['aop_rate']=$avg_wo_aop_rate;
					$aop_prod_array[$row[csf('po_id')]]['currency_id']=$row[csf('currency_id')];
					$aop_prod_array[$row[csf('po_id')]]['booking_date']=$row[csf('booking_date')];
				}
				else
				{
					$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
					$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
					$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
					$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
				}
			}
			unset($result_aop_rate);
			if($poIds!='' || $poIds!=0) $po_cond_for_in3=$po_cond_for_in3; else $po_cond_for_in3="and b.order_id in(0)";
			
			$sql_aop="select a.company_id, b.batch_issue_qty as issue_qty,b.prod_id,b.order_id as po_id,b.grey_used,b.rate from pro_grey_batch_dtls b,inv_receive_mas_batchroll a where a.id=b.mst_id  and a.entry_form=92  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.process_id=35 $po_cond_for_in3";

			// echo "<pre>";
			// print_r($aop_prod_array);
			$result_aop=sql_select( $sql_aop );
			foreach ($result_aop as $row) 
			{
				$aop_rate=$aop_prod_array[$row[csf('po_id')]]['aop_rate'];
				$booking_date=$aop_prod_array[$row[csf('po_id')]]['booking_date'];
				$currency_id=$aop_prod_array[$row[csf('po_id')]]['currency_id'];
				$booking_date=$booking_date;
				if($db_type==0) $conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
				
				$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
				if($currency_id==1) $avg_rate=$aop_rate/$exchange_rate;//TK
				else $avg_rate=$aop_rate;
					
				$aop_amt=$row[csf('grey_used')]*$avg_rate;
				$aop_prod_array[$row[csf('po_id')]]['aop_amt']+=$aop_amt;
			}
			//print_r($aop_prod_array);
			unset($result_aop);
			$sql_yarn="select c.id,a.requisition_no,a.receive_basis,a.prod_id,(a.cons_amount) as cons_amount,c.issue_basis,c.issue_number,c.booking_no from inv_transaction a, order_wise_pro_details b, inv_issue_master c where  a.id=b.trans_id and  c.id=a.mst_id and c.entry_form in(3) and b.entry_form in(3)  and  a.transaction_type=2 $po_cond_for_in4";
			$result_yarn=sql_select($sql_yarn);
			foreach($result_yarn as $invRow)
			{
				if($invRow[csf('issue_basis')]==1)// Booking
				{
					$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('booking_no')];
				}
				else if($invRow[csf('issue_basis')]==3)// Requesition
				{
					$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('requisition_no')];
				}
			}
			
			unset($result_yarn);
				//die;
			if($poIds!='' || $poIds!=0) $po_cond_for_in2=$po_cond_for_in2; 
			else $po_cond_for_in2="and po_id in(0)";
			
			$plan_details_array = return_library_array("select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where company_id=$company_name $po_cond_for_in2 group by dtls_id,booking_no", "dtls_id", "booking_no");
			//print_r($plan_details_array);
			$reqs_array = array();
			
			$reqs_sql = sql_select("select a.knit_id, a.requisition_no as reqs_no, sum(a.yarn_qnty) as yarn_req_qnty from ppl_planning_entry_plan_dtls b,ppl_yarn_requisition_entry a where b.dtls_id=a.knit_id and b.status_active=1 and b.is_deleted=0 $po_cond_for_in5 group by a.knit_id, a.requisition_no");
			foreach ($reqs_sql as $row)
			{
				$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
			}
			unset($reqs_sql);
			//and b.issue_purpose in(2,4,15,38)
		    $yarnissue_retData="select d.booking_no,a.item_category,b.po_breakdown_id, b.prod_id, a.mst_id,a.receive_basis, b.issue_purpose,c.lot,c.color,
			(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.transaction_date ELSE null END) AS transaction_date,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.order_amount ELSE 0 END) AS order_amount_ret,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.order_qnty ELSE 0 END) AS order_qnty_ret
			from inv_transaction a, order_wise_pro_details b,product_details_master c,inv_receive_master d where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id  and d.id=a.mst_id and a.item_category in(1) and a.transaction_type in(4) and b.entry_form in(9) and d.entry_form in(9) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $po_cond_for_in4  group by d.booking_no,b.po_breakdown_id, b.prod_id, a.item_category,a.transaction_date,a.receive_basis,a.mst_id,a.transaction_type,b.entry_form,b.trans_type,b.issue_purpose,c.lot,c.color";
			$yarnissueretDataArray=sql_select($yarnissue_retData); $yarnissueRetCostArray=array();
			foreach($yarnissueretDataArray as $invRow)
			{
				$issue_purpose=$invRow[csf('issue_purpose')];
				$receive_basis=$invRow[csf('receive_basis')];
				//echo $receive_basis.'ff';
				if($receive_basis==1) //Booking Basis
				{
					$booking_no=$invRow[csf('booking_no')];
				}
				
				else if($receive_basis==3) //Requisition Basis
				{
					$booking_req_no=$invRow[csf('booking_no')];
					$prog_no=$reqs_array[$booking_req_no]['knit_id'];
					$booking_no=$plan_details_array[$prog_no];
					//echo $booking_req_no.'req';
				}
				$booking_type=$booking_array[$booking_no]['btype'];
				$is_short=$booking_array[$booking_no]['is_short'];
				//echo $booking_type.'='.$is_short.'='.$booking_no.'<br>';
				$returnable_ret_qnty=$invRow[csf('returnable_qnty')];
				//echo $invRow[csf('yarn_iss_return_qty')].'<br>';
				 $issue_ret_qty=$invRow[csf('yarn_iss_return_qty')];
				  $order_qnty_ret=$invRow[csf('order_qnty_ret')];
				  $order_amount_ret=$invRow[csf('order_amount_ret')];
				 $avg_rate=$order_amount_ret/$order_qnty_ret;
				//echo $avg_rate.'<br>';
				//$rate='';
				$transaction_date=$invRow[csf('transaction_date')];
				if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
				
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				//echo $ret_avg_rate= $avg_rate/$conversion_date;
				$currency_id=$receive_curr_array[$invRow[csf('prod_id')]];
				//echo $currency_id.'dddddd';
				if($receive_array[$invRow[csf('prod_id')]]>0)
				{
					//echo $currency_id.'D';
					if($currency_id==1) $rate=$receive_array[$invRow[csf('prod_id')]]/$currency_rate;//Taka
					else $rate=$receive_array[$invRow[csf('prod_id')]];	
				}
				else $rate=$avg_rate_array[$invRow[csf('prod_id')]]/$currency_rate;
				
				$iss_ret_amnt=$issue_ret_qty*$rate;
				$retble_iss_ret_amnt=$returnable_ret_qnty*$rate;
				if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
				{
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($issue_purpose==1 || $issue_purpose==4) //Knit || Sample With Order
					{
						if($recv_purpose==16) //Grey Yarn
						{
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}
					else if($issue_purpose==2) //Yarn Dyeing
					{
						if($recv_purpose==16) //Grey Yarn
						{
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}
				}
				else //Short Fabric Booking
				{
					//echo $booking_type.'d';
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($issue_purpose==1 || $issue_purpose==4) // Knit || Sample With Order
					{
						if($recv_purpose==16) //Grey Yarn
						{
							//echo $iss_amnt.'cpa';
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}
					else  if($issue_purpose==2)
					{
						if($recv_purpose==16) //Grey Yarn
						{
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}	
				}
			}
			unset($yarnissueretDataArray);
				
			$yarnTrimsData="select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color
					from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25)  $po_cond_for_in4 ";
				
			$yarnTrimsDataArray=sql_select($yarnTrimsData); $yarnTrimsCostArray=array(); $i=0;
			foreach($yarnTrimsDataArray as $invRow)
			{
				if($invRow[csf('item_category')]==1)
				{
					$yarn_issue_amt=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['amt'];
					$yarn_issue_qty=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['qty'];
					$last_trans_date=$receive_date_array[$invRow[csf('prod_id')]]['last_trans_date'];
					
					if($invRow[csf('receive_basis')]==1)//Booking Basis
					{
						$booking_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						if($invRow[csf('issue_purpose')]==2) //Yarn Dyeing purpose
						{
							$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
							$booking_type=1;
							//echo $is_short.'= '.$booking_type.', ';
						}
						else
						{
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
					}
					else if($invRow[csf('receive_basis')]==3) //Requisition Basis
					{
						$booking_req_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						
						$prog_no=$reqs_array[$booking_req_no]['knit_id'];
						$booking_no=$plan_details_array[$prog_no];
						//echo $booking_no.'='.$prog_no.',';
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
					$transaction_date='';
					$transaction_date=$last_trans_date;
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					
					$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
					$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
					$avgrate=$issue_rate/$exchange_rate;
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($invRow[csf('entry_form')]==3 && $recv_purpose==16)//recv_purpose==16=Grey Yarn
					{
						if(($booking_type==1 || $booking_type==4) && $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
							{
								//echo $invRow[csf('mst_id')].'='.$recv_purpose.'='.$is_short.'='.$invRow[csf('quantity')].'-k<br>';
								$iss_amnt=$invRow[csf('quantity')]*$avgrate;
								//echo $iss_amnt.'m';
								$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_amt']+=$iss_amnt;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['yq']+=$invRow[csf('quantity')];
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_qty_retble']+=$invRow[csf('returnable_qnty')];
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_amt_retble']+=$retble_iss_amnt;
							}
						}
						else //Short Fabric Booking
						{
							$iss_amnt=$invRow[csf('quantity')]*$avgrate;
							//echo $is_short.'='.$iss_amnt.'='.$invRow[csf('mst_id')].'<br>';
							$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
							
							if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
							{
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_amt']+=$iss_amnt;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_qty_retble']+=$returnable_qnty;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_amt_retble']+=$retble_iss_amnt;
							}
						}
					}
				}
				else
				{
					$trimsAmt=0;
					$trimsAmt=$invRow[csf('quantity')]*$invRow[csf('cons_rate')];
					$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$trimsAmt;
				}
			}
			unset($yarnTrimsDataArray);
					
			$sql_garments="select b.po_break_down_id as po_id,b.embel_name,b.production_date,b.production_type,
				(CASE WHEN b.embel_name=1 THEN b.production_quantity ELSE 0 END) AS printing_qty,
				(CASE WHEN b.embel_name=2 THEN b.production_quantity ELSE 0 END) AS embro_qty,
				(CASE WHEN b.embel_name=3 THEN b.production_quantity ELSE 0 END) AS wash_qty,
				(CASE WHEN b.embel_name=5 THEN b.production_quantity ELSE 0 END) AS dyeing_qty,
				(CASE WHEN b.embel_name=99 THEN b.production_quantity ELSE 0 END) AS others
				 from pro_garments_production_mst b where  b.production_type=2    and b.is_deleted=0 and b.status_active=1 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in";
				$result_gar=sql_select( $sql_garments );
				foreach ($result_gar as $row)
				{
					$embl_issue_prod_array[$row[csf('po_id')]]['printing_qty']+=$row[csf('printing_qty')];
					$embl_issue_prod_array[$row[csf('po_id')]]['embro_qty']+=$row[csf('embro_qty')];
					$embl_issue_prod_array[$row[csf('po_id')]]['wash_qty']+=$row[csf('wash_qty')];
					$embl_issue_prod_array[$row[csf('po_id')]]['dyeing_qty']+=$row[csf('dyeing_qty')];
					$embl_issue_prod_array[$row[csf('po_id')]]['others']+=$row[csf('others')];
				}
				unset($result_gar);
			
				//print_r($yarnTrimsCostArray);
				$recvIssue_array=array(); 
				$sql_trans="select b.trans_type, b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(80,82,81,83,84,13,94) and a.item_category=13 and a.transaction_type in(5,6) and b.trans_type in(5,6) $po_cond_for_in4 group by b.trans_type, b.po_breakdown_id";
				$result_trans=sql_select( $sql_trans );
				foreach ($result_trans as $row)
				{
					$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_trans);
				
				$gen_trims_issue_array=array(); 
				 $sql_gen_trims_inv="select b.transaction_date,(b.cons_amount) as cons_amount,b.order_id as po_id
				  from inv_issue_master  a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(21)   and b.transaction_type in(2)   $po_cond_for_in3 ";
				$result_gen_trims=sql_select( $sql_gen_trims_inv );
				foreach ($result_gen_trims as $row)
				{
					$transaction_date=$row[csf('transaction_date')];
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$gen_trims_issue_array[$row[csf('po_id')]]['amt']+=$row[csf('cons_amount')]/$currency_rate;//
				}
				unset($result_gen_trims);
				$trims_trans_array=array(); 
				
				$sql_trims_inv="select a.transaction_date, a.cons_amount as cons_amount, b.po_breakdown_id as po_id, b.quantity as qnty, c.currency_id
				  from inv_transaction a, order_wise_pro_details b, inv_receive_master c  where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form in(24) and a.item_category=4 and a.transaction_type in(1) and b.trans_type in(1) $po_cond_for_in4";
				$result_trims=sql_select( $sql_trims_inv );
				foreach ($result_trims as $row)
				{
					$transaction_date=$row[csf('transaction_date')];
					$currency_id=$row[csf('currency_id')];
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($currency_id,$conversion_date );
					$amt=0;
					
					$amt=$row[csf('cons_amount')]/$currency_rate;
						//echo $currency_rate.'d';
					$trims_trans_array[$row[csf('po_id')]]['amt']+=$amt;
				}
				
				//die;
				unset($result_trims);
				$fin_fab_trans_array=array(); 
				 $sql_fin_trans="select b.trans_type, b.po_breakdown_id, sum(b.quantity) as qnty
				  from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(15,14) and a.item_category=2 and a.transaction_type in(5,6) and b.trans_type in(5,6) $po_cond_for_in4 group by b.trans_type, b.po_breakdown_id";
				$result_fin_trans=sql_select( $sql_fin_trans );
				foreach ($result_fin_trans as $row)
				{
					$fin_fab_trans_array[$row[csf('po_breakdown_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_fin_trans);
				
				if($poIds!='' || $poIds!=0)
				{
					$po_cond_for_in5=$po_cond_for_in5; 
				}
				else
				{
					$po_cond_for_in5="and b.po_id in(0)";
				}
				$batch_trans_array=array(); 
				$sql_batch="select a.id,a.booking_no,batch_weight as batch_weight
				  from  pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in5 group by a.id,batch_weight,a.booking_no";
				$result_batch=sql_select( $sql_batch );
				foreach ($result_batch as $row)
				{
					$batch_trans_array[$row[csf('id')]]['book']=$row[csf('booking_no')];
					$batch_weight_array[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_batch);
				
				$dye_fin_fab_array=array(); 
				 $sql_fin_trans="select a.receive_date, b.po_breakdown_id,c.mst_id,c.batch_id,c.body_part_id, sum(b.quantity) as finish_qnty  from inv_receive_master a,pro_finish_fabric_rcv_dtls c, order_wise_pro_details b where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(7) and a.item_category=2   and b.trans_type in(1) $po_cond_for_in4 group by a.receive_date, b.po_breakdown_id,c.batch_id,c.mst_id,c.body_part_id";
				$result_fin_trans=sql_select( $sql_fin_trans );
				foreach ($result_fin_trans as $row)
				{
						$receive_date=$row[csf('receive_date')];
						$booking_no=$batch_trans_array[$row[csf('batch_id')]]['book'];
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
						$batch_weight=$batch_weight_array[$row[csf('batch_id')]]['batch_weight'];
						$dye_finish_charge=$prodcostArray[$row[csf('po_breakdown_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
						//echo $batch_weight.'kk';
						if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							
							$finish_qnty=$row[csf('finish_qnty')]*$dye_finish_charge;
						}
						else //short Fab. booking 
						{
							$cpa_batch_weight=$batch_weight*$dye_finish_charge;
						}
						//echo $finish_qnty.'='.$cpa_finish_qnty;
						if($db_type==0)
						{
							$conversion_date=change_date_format($receive_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($receive_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						$dye_fin_fab_array[$row[csf('po_breakdown_id')]]['fin_amt']=$finish_qnty;
						$dye_fin_fab_array[$row[csf('po_breakdown_id')]]['cpa_batch_weight']+=$cpa_batch_weight;
						$dye_fin_fab_array[$row[csf('po_breakdown_id')]]['ex_rate']=$currency_rate;
				}
				unset($result_fin_trans);
				
				$grey_fab_array=array();  //Knitting Cost actual 
				  $sql_grey_trans="select  a.id,a.receive_basis,a.booking_no,a.receive_date,b.po_breakdown_id,c.febric_description_id as deter_id,c.body_part_id, c.kniting_charge,
				 (CASE WHEN b.entry_form =2 and b.trans_type in(1) and a.item_category=13  THEN  b.quantity ELSE 0 END) AS grey_qnty
				
				  from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and a.item_category in(2,13)  and b.trans_type in(1) $po_cond_for_in4";
				$result_grey=sql_select( $sql_grey_trans );
				$currency_id=1;
				foreach ($result_grey as $row)
				{
						$transaction_date=$row[csf('transaction_date')];
						$receive_basis=$row[csf('receive_basis')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($row[csf('receive_date')], "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($row[csf('receive_date')], "d-M-y", "-",1);
						}
						$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
						if($currency_id==1) //TK
						{
							$knit_charge=$row[csf('kniting_charge')]/$exchange_rate;
							
						}
						else
						{
							$knit_charge=$row[csf('kniting_charge')];
						}
						
						//$knit_charge=$row[csf('kniting_charge')];//$prodcostArray[$row[csf('po_breakdown_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['knit_charge'];
						//echo $knit_charge.'='.$row[csf('deter_id')].'ff'.$row[csf('po_breakdown_id')];
						if($receive_basis==1) //Booking Basis
						{
							 $booking_no=$row[csf('booking_no')];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							//echo $booking_type.'='.$is_short;
						}
						else if($receive_basis==2) //Knit plan Basis
						{
							 $prog_no=$grey_knit_array[$row[csf('mst_id')]];
							$booking_no=$plan_details_array[$row[csf('booking_no')]];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
						else if($receive_basis==4) //Salse Basis
						{
							 $prog_no=$row[csf('booking_no')];
							$booking_no=$plan_details_array[$prog_no];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							
						}
						
						if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							$grey_amt=$row[csf('grey_qnty')]*$knit_charge;
							//$finish_qnty=$row[csf('finish_qnty')];
							//echo $row[csf('grey_qnty')].'*'.$knit_charge.'<br>';
						}

						else //short Fab. booking 
						{
							$cpa_grey_amt=$row[csf('grey_qnty')]*$knit_charge;
							//$cpa_finish_qnty=$row[csf('finish_qnty')];
						}
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$grey_fab_array[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('grey_qnty')];
					$grey_fab_array[$row[csf('po_breakdown_id')]]['grey_amt']+=$grey_amt;
					$grey_fab_array[$row[csf('po_breakdown_id')]]['cpa_grey_amt']+=$cpa_grey_amt;
					
				}
				unset($result_grey);
				//print_r($grey_fab_array);
				$sql_fin_purchase="select b.po_breakdown_id,a.transaction_date, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id,a.transaction_date";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					$transaction_date=$finRow[csf('transaction_date')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase_amnt')]/$currency_rate;
				}
				unset($dataArrayFinPurchase);
				
				$sql_fin_purchase_wv="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in4 group by b.po_breakdown_id";
				$dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
				foreach($dataArrayFinPurchaseW as $finRow)
				{
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')]/$ex_rate;
				}
				unset($dataArrayFinPurchaseW);
				$sql_grey_purchase="select b.po_breakdown_id,a.transaction_date, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category in(13,14) and a.transaction_type=1 and b.entry_form in(22,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id,a.transaction_date";
				$dataArraygreyPurchase=sql_select($sql_grey_purchase);
				foreach($dataArraygreyPurchase as $gRow)
				{
					$transaction_date=$gRow[csf('transaction_date')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$grey_purchase_amnt_arr[$gRow[csf('po_breakdown_id')]]=$gRow[csf('finish_purchase_amnt')]/$currency_rate;
				}
					unset($dataArraygreyPurchase);
				 $actualCostArray=array();
				$actualCostDataArray="select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 $po_cond_for_in2 group by cost_head,po_id";
				$actual_results=sql_select($actualCostDataArray);
				foreach($actual_results as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
				unset($actual_results);
				
				$i=1; $tot_po_qnty=0; $tot_po_value=0; $tot_ex_factory_qnty=0; $tot_ex_factory_val=0; $tot_yarn_cost_mkt=0; $tot_knit_cost_mkt=0; $tot_dye_finish_cost_mkt=0; $tot_yarn_dye_cost_mkt=0; $tot_aop_cost_mkt=0; $tot_trims_cost_mkt=0; $tot_embell_cost_mkt=0; $tot_wash_cost_mkt=0; $tot_commission_cost_mkt=0; $tot_comm_cost_mkt=0; $tot_freight_cost_mkt=0; $tot_test_cost_mkt=0; $tot_inspection_cost_mkt=0; $tot_currier_cost_mkt=0; $tot_cm_cost_mkt=0; $tot_mkt_all_cost=0; $tot_mkt_margin=0; $tot_yarn_cost_actual=0; $tot_knit_cost_actual=0; $tot_dye_finish_cost_actual=0; $tot_yarn_dye_cost_actual=0; $tot_aop_cost_actual=0; $tot_trims_cost_actual=0; $tot_embell_cost_actual=0; $tot_wash_cost_actual=0; $tot_commission_cost_actual=0; $tot_comm_cost_actual=0; $tot_freight_cost_actual=0; $tot_test_cost_actual=0; $tot_inspection_cost_actual=0; $tot_currier_cost_actual=0; $tot_cm_cost_actual=0; $tot_actual_all_cost=0; $tot_actual_margin=0; $tot_fabric_purchase_cost_mkt=$total_fabric_cost_mkt=0; $tot_fabric_purchase_cost_actual=$tot_grey_fab_cost_actual=$tot_actual_net_margin=0;
				$tot_print_amount_mkt=$tot_embro_amount_mkt=$tot_dyeing_amount_mkt=$tot_wash_amount_mkt=$tot_print_amount_actual=$tot_embro_amount_actual=$tot_dyeing_amount_actual=$tot_wash_amount_actual=$tot_finished_fab_cost_actual=$tot_fin_fab_transfer_amt_actual=$tot_yarn_dyeing_twist_actual=$tot_grey_fab_transfer_amt_actual=$tot_yarn_qty_actual=$tot_yarn_amt_actual=$tot_grey_fab_cost_mkt=$tot_yarn_value_addition_cost_mkt=$total_fabric_cost=$tot_fabric_cost_actaul=$tot_yarn_qty_cpa_actual=$total_finish_fabric_cost_mkt=$tot_yarn_qty_cpa_actual=0;
				$buyer_name_array=array();
				$mkt_po_val_array = array(); $mkt_yarn_array = array(); $mkt_knit_array = array(); $mkt_dy_fin_array = array(); $mkt_yarn_dy_array = array();
				$mkt_aop_array = array(); $mkt_trims_array = array(); $mkt_emb_array = array(); $mkt_wash_array=array(); $mkt_commn_array=array(); $mkt_commercial_array=array();
				$mkt_freight_array = array(); $mkt_test_array = array(); $mkt_ins_array = array(); $mkt_courier_array = array(); $mkt_cm_array = array();
				$mkt_total_array = array(); $mkt_margin_array = array(); $mkt_fabric_purchase_array = array();
				
				$ex_factory_val_array= array(); $yarn_cost_array= array(); $knit_cost_array= array(); $dye_cost_array= array(); $yarn_dyeing_cost_array= array();
				$aop_n_others_cost_array= array(); $trims_cost_array= array(); $enbellishment_cost_array= array(); $wash_cost_array= array(); $commission_cost_array= array(); 
				$commercial_cost_array= array(); $freight_cost_array= array(); $testing_cost_array= array(); $inspection_cost_array= array(); $courier_cost_array= array();
				$cm_cost_array= array(); $total_cost_array= array(); $margin_array= array(); $ex_factory_array=array(); $actual_cm_amnt=array(); $actual_fabric_purchase_array = array();
								
				 $condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if(str_replace("'","",$txt_file_no) !=''){
					  $condition->file_no("='$txt_file_no'");
				 }
				 if(str_replace("'","",$txt_ref_no) !=''){
					  $condition->grouping("='$txt_ref_no'");
				 }
				 
				 if(str_replace("'","",$txt_job_no) !=''){
					  $condition->job_no_prefix_num("=$txt_job_no");
				 }
				if(trim(str_replace("'","",$txt_order_no))!='')
				 {
					//$condition->po_number(" like '%".trim(str_replace("'","",$txt_order_no))."%'"); 
					$condition->po_number("='".trim(str_replace("'","",$txt_order_no))."'"); 
				 }
				 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				 {
					 $start_date=str_replace("'","",$txt_date_from);
					 $end_date=str_replace("'","",$txt_date_to);
					 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					 //and b.po_received_date between '$start_date' and '$end_date' 
					// echo 'FFGG';
				 }
				 
				 $condition->init();
				
				 $yarn= new yarn($condition);
				//echo $yarn->getQuery();die;
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $yarn= new yarn($condition);
				 $yarn_costing_qty_arr=$yarn->getOrderWiseYarnQtyArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess(); 
				 $conversion= new conversion($condition);
				 
				 $conversion_costing_arr_process_qty=$conversion->getQtyArray_by_orderAndProcess();
			
				$trims= new trims($condition);
				$trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name_qty=$emblishment->getQtyArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash_qty=$wash->getQtyArray_by_orderAndEmbname();
				$fabric= new fabric($condition);
				 //echo $fabric->getQuery();
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				$usd_id=2;//===============Dollar========
				//print_r($fabric_costing_arr);die;
				$not_yarn_dyed_cost_arr=array(1,2,30,134,35);$job_ids='';

				foreach($result as $row)
				{
					$order_arr[$row[csf('id')]]=$row[csf('id')];
				}


	
	 //============================================knit inbound bill issue===============================================
	 $knit_in_bill_sql="select b.id as upd_id,  b.process_id,b.rate, b.amount, b.order_id, b.currency_id ,b.delivery_qty,a.bill_date from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.id=b.mst_id and   a.status_active=1 and a.is_deleted=0 ".where_con_using_array($order_arr,1,'b.order_id')."  order by b.challan_no ASC";
	 //echo  $knit_in_bill_sql;
	 $knit_in_bill_data=sql_select($knit_in_bill_sql);
	 foreach($knit_in_bill_data as $kival){
		if($kival[csf('process_id')]==2){
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
			
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
		}else{
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
		}
	 }
		
	  //============================================knit outbound bill issue/gross same soucre===============================================
	   $knit_out_bill_sql="SELECT id as dtls_id, receive_id,wo_num_id, receive_date, challan_no, order_id, roll_no, body_part_id, febric_description_id, item_id as prod_id, receive_qty, rec_qty_pcs, uom, rate, amount, remarks,currency_id,process_id FROM subcon_outbound_bill_dtls WHERE  process_id in (2,4) and status_active=1 and is_deleted=0 ".where_con_using_array($order_arr,1,'order_id')." order by id asc";
	  $knit_out_bill_data=sql_select($knit_out_bill_sql);
	  foreach($knit_out_bill_data as $koval){
			if($koval[csf('process_id')]==2){
				$order_wise_knit_bill_data[$koval[csf('order_id')]]['rate']=$koval[csf('rate')];
				$order_wise_dye_bill_rateArr[$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('rate')];
			}else{
				$order_wise_dye_bill_rate[$koval[csf('order_id')]]['rate']=$koval[csf('rate')];
				$order_wise_dye_bill_rateArr[$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('rate')];
			}
	  }



	  //=======================================Dye & Fin Cost=====================================================
	 /*$fin_fab_rec_data=sql_select("SELECT a.company_id,a.location_id,a.booking_no as non_order_booking, a.booking_id,a.receive_basis,b.prod_id, b.batch_id, b.receive_qnty, b.reject_qty,b.bin, b.order_id,b.grey_used_qty,b.job_no,b.booking_no,b.booking_id booking_no_id from inv_receive_master a, inv_transaction c,pro_finish_fabric_rcv_dtls b  where a.id=c.mst_id and c.id=b.trans_id  and a.item_category=2 and a.entry_form=37 and a.status_active=1 and c.status_active=1 and b.status_active=1 ".where_con_using_array($order_arr,1,'b.order_id')."");
	 
	

	 foreach($fin_fab_rec_data as $fval){
		$order_wise_fin_fab_rec[$fval[csf('order_id')]]['used_qty']=$fval[csf('grey_used_qty')];
	 }*/
	 
	  $finProd_grey_used_sql=sql_select("SELECT a.company_id,a.receive_purpose,a.recv_number,a.receive_date,a.location_id,a.booking_no as non_order_booking, a.booking_id,a.receive_basis,b.prod_id, b.batch_id,b.fabric_description_id as deter_id, b.receive_qnty, b.reject_qty,b.bin,e.po_breakdown_id as po_id,e.quantity, b.batch_id,b.order_id,b.grey_used_qty,b.job_no,b.booking_no,b.booking_id booking_no_id from inv_receive_master a, inv_transaction c,pro_finish_fabric_rcv_dtls b,order_wise_pro_details e  where a.id=c.mst_id and c.id=b.trans_id  and b.id=e.dtls_id and e.trans_id=c.id  and a.item_category=2 and a.entry_form=37 and e.entry_form=37 and a.status_active=1 and e.status_active=1 and c.status_active=1 and b.status_active=1 ".where_con_using_array($order_arr,0,'e.po_breakdown_id')." ");
	  
	   foreach($finProd_grey_used_sql as $fval){
		   $currency_id=$order_wise_knit_bill_data[$fval[csf('po_id')]]['currency_id'];
		  $dye_rate=$order_wise_dye_bill_rateArr[$fval[csf('po_id')]][$fval[csf('prod_id')]]['rate'];
					$usd_id=2;
					$bill_date=$order_wise_dye_bill_rate[$fval[csf('po_id')]]['bill_date'];
					if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					if($currency_id==2) $dye_rate=$dye_rate;//USD
					else $dye_rate=$dye_rate/$currency_rate;
					
		$order_wise_fin_fab_rec[$fval[csf('po_id')]]['used_qty']+=$fval[csf('grey_used_qty')]*$dye_rate;
		//echo $fval[csf('grey_used_qty')].'*'.$dye_rate.'*'.$currency_rate.'*'.$currency_id.',';
	 }
	 
	  



	  
	 //================================================================================================================
		$finis_fab_dat=sql_select("select a.id, a.from_order_id, c.po_number, a.batch_id, b.batch_no, a.from_store, a.to_store, a.from_prod_id, a.color_id,sum(a.transfer_qnty) transfer_qnty ,c.id as poid	from inv_item_transfer_dtls a, pro_batch_create_mst b , wo_po_break_down c where  a.batch_id = b.id and a.from_order_id = c.id and a.status_active = '1' and a.is_deleted = '0' and a.active_dtls_id_in_transfer=1  ".where_con_using_array($order_arr,1,'c.id')." group by a.id, a.from_order_id, c.po_number, a.batch_id, b.batch_no, a.from_store, a.to_store, a.from_prod_id, a.color_id,c.id");


				foreach($finis_fab_dat as $val){
					$order_wise_fin_fab_rec[$val[csf('poid')]]['transfer_qnty']+=$val[csf('transfer_qnty')];
				}



				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$po_ids_array[]=$row[csf('id')];
					 $job_ids.=$row[csf('job_no')].',';
					$gmts_item='';
					$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
					foreach($gmts_item_id as $item_id)
					{
						if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
					}
					

					$knit_rate=$order_wise_knit_bill_data[$row[csf('id')]]['amount']/$order_wise_knit_bill_data[$row[csf('id')]]['delivery_qty'];
					$bill_date=$order_wise_knit_bill_data[$row[csf('id')]]['bill_date'];

					$currency_id=$order_wise_knit_bill_data[$row[csf('id')]]['currency_id'];
					$usd_id=2;
					if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					if($currency_id==2) $knit_rate=$knit_rate;//USD
					else $knit_rate=$knit_rate/$currency_rate;



					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
					$po_value=$order_qnty_in_pcs*$unit_price;
					
					$tot_po_qnty+=$order_qnty_in_pcs; 
					$tot_po_value+=$po_value;
					//echo "DDD";die;
					$ex_factory_qty=$ex_factory_arr[$row[csf('id')]];
					$ex_factory_value=$ex_factory_qty*$unit_price;
					
					$tot_ex_factory_qnty+=$ex_factory_qty; 
					$tot_ex_factory_val+=$ex_factory_value; 
					
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					$yarn_cost_mkt=$yarn_costing_arr[$row[csf('id')]];
					 $yarn_cost_mkt_qty=$yarn_costing_qty_arr[$row[csf('id')]];
					$knit_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][1]);//($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					$knit_qty_mkt=array_sum($conversion_costing_arr_process_qty[$row[csf('id')]][1]);
					//$yarn_dyeing_cost_mkt+=array_sum($conversion_costing_arr_process[$row[csf('id')]][30]);
					$yarn_dyeing_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][30]);
					//if($yarn_dyeing_cost_mkt) $yarn_dyeing_cost_mkt=$yarn_dyeing_cost_mkt;else $yarn_dyeing_cost_mkt=0;
					$twisting_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][134]);
					//if($twisting_cost_mkt) $twisting_cost_mkt=$twisting_cost_mkt;else $twisting_cost_mkt=0;
					$yarn_value_addition_cost_mkt=$yarn_dyeing_cost_mkt+$twisting_cost_mkt;
					//echo $yarn_dyeing_cost_mkt.'=='.$twisting_cost_mkt.', ';
				
					if($knit_cost_mkt>0)
					{
					$knit_charge_mkt=$knit_cost_mkt/$knit_qty_mkt;//$dye_finish_cost_mkt_qty
					}
					
					$dye_finish_cost_mkt=0;$dye_finish_cost_mkt_qty=0;
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$row[csf('id')]][$process_id]);
							
							$dye_finish_cost_mkt_qty+=array_sum($conversion_costing_arr_process_qty[$row[csf('id')]][$process_id]);
						}
					}	
					if($dye_finish_cost_mkt>0)
					{			
					$knit_charge_mkt_fin=$dye_finish_cost_mkt/$dye_finish_cost_mkt_qty;
					}
					//echo $dye_finish_cost_mkt.'='.$dye_finish_cost_mkt_qty;
					//$yarn_dye_cost_mkt=$conversion_costing_arr_process[$row[csf('id')]][30];
					$aop_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][35]);//($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					$trims_cost_mkt=$trims_costing_arr[$row[csf('id')]];//($order_qnty_in_pcs/$dzn_qnty)*$trimsCostArray[$row[csf('id')]];
				
					
					$print_amount=$emblishment_costing_arr_name[$row[csf('id')]][1];
					$print_qty=$emblishment_costing_arr_name_qty[$row[csf('id')]][1];
					if($print_amount) $print_amount=$print_amount;else $print_amount=0;
					if($print_qty) $print_qty=$print_qty;else $print_qty=0;
					if($print_amount==0 || $print_qty==0) $print_avg_rate=0;
					else $print_avg_rate=$print_amount/$print_qty;
						
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('id')]][2];
					$embroidery_qty=$emblishment_costing_arr_name_qty[$row[csf('id')]][2];								
					if($embroidery_amount) $embroidery_amount=$embroidery_amount;else $embroidery_amount=0;
					if($embroidery_qty) $embroidery_qty=$embroidery_qty;else $embroidery_qty=0;
					if($embroidery_amount==0 || $embroidery_qty==0) $embro_avg_rate=0;
					else $embro_avg_rate=$embroidery_amount/$embroidery_qty;


				
					$others_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$others_qty=$emblishment_costing_arr_name_qty[$row[csf('id')]][5];
					if($other_amount) $others_amount=$others_amount;else $others_amount=0;
					if($others_qty) $others_qty=$others_qty;else $others_qty=0;
					if($others_amount==0 || $others_amount==0) $others_amount=0;
					else $others_avg_rate=$others_amount/$others_qty;



					//$special_amount=$emblishment_costing_arr_name[$row[csf('id')]][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('id')]][3];
					$wash_qty=$emblishment_costing_arr_name_wash_qty[$row[csf('id')]][3];
					if($wash_cost) $wash_cost=$wash_cost;else $wash_cost=0;
					if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
					if($wash_cost==0 || $wash_qty==0) $wash_avg_rate=0;
					else $wash_avg_rate=$wash_cost/$wash_qty;
					
					$other_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$dyeing_qty=$emblishment_costing_arr_name_qty[$row[csf('id')]][5];
					if($other_amount) $other_amount=$other_amount;else $other_amount=0;
					if($dyeing_qty) $dyeing_qty=$dyeing_qty;else $dyeing_qty=0;
					if($other_amount==0 || $dyeing_qty==0) $dyeing_avg_rate=0;
					else $dyeing_avg_rate=$other_amount/$dyeing_qty;
					
					$foreign_cost=$commission_costing_arr[$row[csf('id')]][1];
					$local_cost=$commission_costing_arr[$row[csf('id')]][2];
					
					$test_cost=$other_costing_arr[$row[csf('id')]]['lab_test'];
					$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];
					$inspection_cost=$other_costing_arr[$row[csf('id')]]['inspection'];
					$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];
					//$common_oh_cost=$other_costing_arr[$row[csf('id')]]['common_oh'];
					$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
					$cm_cost=$other_costing_arr[$row[csf('id')]]['cm_cost'];
					$embell_cost_mkt=$print_amount+$embroidery_amount+$wash_cost+$other_amount;
					
					
					$wash_cost_mkt=$wash_cost;
					$commission_cost_mkt=$foreign_cost+$local_cost;
					$comm_cost_mkt=$commercial_costing_arr[$row[csf('id')]];
					$freight_cost_mkt=$freight_cost;
					$test_cost_mkt=$test_cost;
					$inspection_cost_mkt=$inspection_cost;
					$currier_cost_mkt=$currier_cost;
					$cm_cost_mkt=$cm_cost;
						//echo "MM";die;
					
					$fabric_purchase_cost=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]]);
					//+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					$fabric_purchase_cost2=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					$fabric_purchase_cost_mkt=$fabric_purchase_cost+$fabric_purchase_cost2;
					$finish_fabric_purchase_cost=array_sum($fabric_costing_arr['knit']['finish'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['finish'][$row[csf('id')]]);
					$finish_fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['finish'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['finish'][$row[csf('id')]]);
					$grey_fab_cost_mkt=$yarn_cost_mkt+$yarn_value_addition_cost_mkt+$knit_cost_mkt;
					$mkt_all_cost=$yarn_cost_mkt+$yarn_value_addition_cost_mkt+$knit_cost_mkt+$dye_finish_cost_mkt+$aop_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$commission_cost_mkt+$comm_cost_mkt+$freight_cost_mkt+$test_cost_mkt+$inspection_cost_mkt+$currier_cost_mkt+$cm_cost_mkt+$fabric_purchase_cost_mkt;
					
					$mkt_margin=$po_value-$mkt_all_cost;
					$mkt_margin_perc=($mkt_margin/$po_value)*100;
					$tot_finish_fabric_cost_mkt=$grey_fab_cost_mkt+$dye_finish_cost_mkt+$aop_cost_mkt;
					
					//$yarn_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$yarncostArray[$row[csf('job_no')]];
					//$knit_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					//$dye_finish_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];
					//$yarn_dye_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge'];
					//$aop_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					//$trims_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$trimsCostArray[$row[csf('id')]];
					$grey_prod_ex_rate=$grey_fab_array[$row[csf('id')]]['ex_rate'];
					$dye_fin_ex_rate=$dye_fin_fab_array[$row[csf('id')]]['ex_rate'];
					
					//$fin_fab_prod_qnty=$grey_fab_array[$row[csf('id')]]['fin_qnty'];
					//echo $grey_fab_array[$row[csf('id')]]['qnty'].'*'.$knit_charge_mkt.'/'.$grey_prod_ex_rate;
					if($process_costing==1)
					{
						$grey_prod_cost=$grey_fab_array[$row[csf('id')]]['qnty']*$knit_charge_mkt;
					}
					else
					{
						$grey_prod_cost=$grey_fab_array[$row[csf('id')]]['grey_amt'];
					}

					
					// $knit_cost_actual=$grey_prod_cost;
					$knit_cost_actual=$grey_fab_array[$row[csf('id')]]['qnty']*$knit_rate;
					
					$cpa_grey_prod_cost=$grey_fab_array[$row[csf('id')]]['cpa_grey_amt'];
					$cpa_knit_cost_actual=$cpa_grey_prod_cost;
					// echo $dye_fin_fab_array[$row[csf('id')]]['cpa_batch_weight'].'/'.$knit_charge_mkt_fin.'=='.$dye_fin_ex_rate.', ';
					$cpa_dye_fin_fab_prod_cost=$dye_fin_fab_array[$row[csf('id')]]['cpa_batch_weight'];
					 $cpa_dye_finish_cost_actual=$cpa_dye_fin_fab_prod_cost;
					
					$fin_fab_prod_cost=$grey_used_arr[$row[csf('id')]]['grey_used_amt']+$fin_grey_used_arr[$row[csf('id')]]['grey_used_amt'];//$dye_fin_fab_array[$row[csf('id')]]['fin_amt'];
					//$dye_finish_cost_actual=$order_wise_fin_fab_rec[$row[csf('id')]]['used_qty']*$dye_rate;//$fin_fab_prod_cost;
					
					$cpa_recv_ret_amt=$receive_ret_arr[$row[csf('job_no')]]['cpa_recv_ret_amt'];
					$cpa_yarn_recv_amt_bal=$yarn_receive_amount_arr[$row[csf('job_no')]]['cpa_order_amount_recv']-$cpa_recv_ret_amt;
					if($cpa_yarn_recv_amt_bal==0) $cpa_yarn_recv_amt_bal_actual=0;
					else $cpa_yarn_recv_amt_bal_actual=($cpa_yarn_recv_amt_bal/$job_quantity)*$order_qnty_in_pcs;
					
					
					//$cpa_yarn_recv_amt_bal_actual=($cpa_yarn_recv_amt_bal/$job_quantity)*$order_qnty_in_pcs;
					
					//echo  $yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_yarn_amt'].'='.$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_amt'].'='.$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_amt'].'='.$cpa_knit_cost_actual.'='.$cpa_dye_finish_cost_actual.',';
					$yarn_cost_cpa_actual=$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_yarn_amt']+$cpa_yarn_recv_amt_bal_actual+$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_amt']+$cpa_knit_cost_actual+$cpa_dye_finish_cost_actual-($yarnissueRetCostArray[$row[csf('id')]]['cpa_grey_yarn_ret_amt']+$yarnissueRetCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_ret_amt']);
					//$yarn_dyeing_twist_cpa_actual=$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_amt'];
					$yarn_ret_qty_cpa_actual=$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_yarn_qty_retble'];
					$yarn_ret_amt_cpa_actual=$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_yarn_amt_retble'];
					$tot_yarn_qty_cpa_actual+=$yarn_cost_cpa_actual;
					//$tot_yarn_amt_cpa_actual+=$yarn_ret_qty_cpa_actual;
					//  echo $yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt'].'-'.$yarnissueRetCostArray[$row[csf('id')]]['grey_yarn_ret_amt'].'<BR>';
					$yarn_cost_actual=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt']-$yarnissueRetCostArray[$row[csf('id')]]['grey_yarn_ret_amt'];
					//echo $yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt'].'D';
					$yarn_ret_qty_actual=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_qty_retble'];
					$yarn_ret_amt_actual=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt_retble'];
					$tot_yarn_qty_actual+=$yarn_ret_qty_actual;
					$tot_yarn_amt_actual+=$yarn_ret_amt_actual;
					$job_quantity=$job_arr2[$row[csf("job_no")]]['job_quantity'];
					
					
					//echo $order_amount_recv_ret.', ';
					//$yarn_receive_amount_arr[$jobNo]['cpa_order_amount_recv']; 
					$order_amount_recv=$yarn_receive_amount_arr[$row[csf('job_no')]]['order_amount_recv'];
					$order_amount_recv_ret=$receive_ret_arr[$row[csf('job_no')]]['recv_ret_amt'];
					
					
					
					//echo $cpa_yarn_recv_amt_bal_actual.'/'.$job_quantity.'*'.$order_qnty_in_pcs.', ';
					$order_amount_recv_bal=$order_amount_recv-$order_amount_recv_ret;
					$avg_recv_amount_actual=($order_amount_recv_bal/$job_quantity)*$order_qnty_in_pcs;

					$yarn_dyeing_twist_actual=$avg_recv_amount_actual;//-$yarnissueRetCostArray[$row[csf('id')]]['grey_dyeing_twist_ret_amt'];
					
					
					$trans_in_amt=$recvIssue_array[$row[csf('id')]][5];
				    $trans_out_amt=$recvIssue_array[$row[csf('id')]][6];
					
					$fin_trans_in_amt=$fin_fab_trans_array[$row[csf('id')]][5];
				    $fin_trans_out_amt=$fin_fab_trans_array[$row[csf('id')]][6];
					
					$grey_fab_transfer=$trans_in_amt-$trans_out_amt;
					//echo $grey_fab_transfer.'D';
					$grey_fab_transfer_amt_actual=$grey_fab_transfer*$knit_charge_mkt;
					
					$fin_fab_transfer=$fin_trans_in_amt-$fin_trans_out_amt;
					//echo $fin_fab_transfer.'='.$knit_charge_mkt_fin;
					// $fin_fab_transfer_amt_actual=$fin_fab_transfer*$knit_charge_mkt_fin;
					//$dye_rate=$order_wise_dye_bill_rate[$row[csf('id')]]['amount']/$order_wise_dye_bill_rate[$row[csf('id')]]['delivery_qty'];
					
					if($order_wise_dye_bill_rate[$row[csf('id')]]['amount'])
					{
					//$dye_rate=$order_wise_dye_bill_rate[$row[csf('id')]]['amount']/$order_wise_dye_bill_rate[$row[csf('id')]]['delivery_qty'];
					//$dye_rate=$order_wise_dye_bill_rate[$row[csf('id')]]['rate'];
					}
					//else $dye_rate=0;
					
					//if($dye_rate=='' || $dye_rate==0)
					//{
					$dye_rate=$order_wise_dye_bill_rate[$row[csf('id')]]['rate'];
					//echo $dye_rate.'A';;
					//}
					



					$bill_date=$order_wise_dye_bill_rate[$row[csf('id')]]['bill_date'];

					$currency_id=$order_wise_dye_bill_rate[$row[csf('id')]]['currency_id'];
					$usd_id=2;
					if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					if($currency_id==2) $dye_rate=$dye_rate;//USD
					else $dye_rate=$dye_rate/$currency_rate;
					$dye_finish_cost_actual=$order_wise_fin_fab_rec[$row[csf('id')]]['used_qty'];



					
					// $fin_fab_transfer_amt_actual=$fin_fab_transfer*$dye_rate;
					$fin_fab_transfer_amt_actual=$order_wise_fin_fab_rec[$row[csf('id')]]['transfer_qnty']*$dye_rate;
					//echo $yarn_cost_actual.'<br>';
					//$trims_cost_actual=$yarnTrimsCostArray[$row[csf('id')]][4]/$exchange_rate;
					$trims_amount=$trims_trans_array[$row[csf('id')]]['amt'];
					$gen_trims_issue_amt=$gen_trims_issue_array[$row[csf('id')]]['amt'];
					$trims_cost_actual=$trims_amount+$gen_trims_issue_amt;
					//echo $trims_amount.'='.$gen_trims_issue_amt;
					$yarn_dye_cost_actual=0;
					$aop_prod_amt=$aop_prod_array[$row[csf('id')]]['aop_amt'];
					//$aop_avg_rate=$aop_prod_array[$row[csf('id')]]['aop_rate'];
					//echo $aop_prod_qty.'='.$aop_avg_rate;
					$aop_cost_actual=$aop_prod_amt;
					$embell_cost_actual=$embellCostArray[$row[csf('id')]];
					$wash_cost_actual=$washCostArray[$row[csf('id')]];
					
					//Finished Fabric Cost
					
					
					$printing_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['printing_qty']/$dzn_qnty)*$print_avg_rate;
					//echo $embl_issue_prod_array[$row[csf('id')]]['printing_qty'].'*'.$print_avg_rate;
					$embro_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['embro_qty']/$dzn_qnty)*$embro_avg_rate;
					$others_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['others']/$dzn_qnty)*$others_avg_rate;
					$dyeing_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['dyeing_qty']/$dzn_qnty)*$dyeing_avg_rate;
					$wash_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['wash_qty']/$dzn_qnty)*$wash_avg_rate;
					
					$commission_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['commission']/$dzn_qnty); 

					// $comm_cost_actual=$actualCostArray[6][$row[csf('id')]];
					$comm_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['comm_cost']/$dzn_qnty); 


					$freight_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty);

					$test_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty);
					$inspection_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty);;
					$currier_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty);
					
				//	$cm_cost_actual=$actualCostArray[5][$row[csf('id')]];
					$working_hour=$prod_resource_array[$row[csf('id')]]['working_hour'];
					$operator_helper=$prod_resource_array[$row[csf('id')]]['operator_helper'];
					$extra_working_hour=$adj_resource_array[$row[csf('style_ref_no')]]['working_hour'];
					$extra_number_of_emp=$adj_resource_array[$row[csf('style_ref_no')]]['number_of_emp'];
					$tot_number_of_employee=$operator_helper+$extra_number_of_emp;
					$tot_working_hr=$working_hour+$extra_working_hour;
					$prod_date=$prod_resource_array[$row[csf('id')]]['pr_date'];
					$from_cpm_date=date("Y-m",strtotime(($prod_date)));
					$cpm=$lib_cpm_array[$from_cpm_date]['cpm'];
					$exchange_rate=set_conversion_rate($usd_id,$prod_date );
				
					//$cm_cost_actual=$tot_working_hr*60*$cpm;
					//echo $working_hour.'='.$extra_number_of_emp.'ds';
					// $cm_cost_actual=($tot_working_hr*60*$tot_number_of_employee*$cpm)/$exchange_rate;
					$cm_cost_actual=$fabriccostArray[$row[csf('job_no')]]['cm_cost'];
					$fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$row[csf('id')]];
					$grey_fabric_purchase_cost_actual=$grey_purchase_amnt_arr[$row[csf('id')]];
					$grey_fab_cost_actual=$yarn_cost_actual+$yarn_dyeing_twist_actual+$knit_cost_actual+$grey_fab_transfer_amt_actual;
					$finished_fab_cost_actual=$grey_fab_cost_actual+$dye_finish_cost_actual+$aop_cost_actual+$fin_fab_transfer_amt_actual+$fabric_purchase_cost_actual;
					$tot_fabric_cost_act=$fabric_purchase_cost_actual+$finished_fab_cost_actual;
					$tot_fabric_cost_actaul+=$tot_fabric_cost_act;
					
					$actual_all_cost=$yarn_cost_actual+$knit_cost_actual+$yarn_dyeing_twist_actual+$grey_fab_transfer_amt_actual+$dye_finish_cost_actual+$yarn_dye_cost_actual+$aop_cost_actual+$trims_cost_actual+$printing_cost_actual+$embro_cost_actual+$others_cost_actual+$dyeing_cost_actual+$wash_cost_actual+$commission_cost_actual+$comm_cost_actual+$freight_cost_actual+$test_cost_actual+$inspection_cost_actual+$currier_cost_actual+$cm_cost_actual+$fabric_purchase_cost_actual+$grey_fabric_purchase_cost_actual;
					
					$actual_margin=$ex_factory_value-$actual_all_cost;
					$actual_margin_perc=($actual_margin/$ex_factory_value)*100;
					$tot_fabric_cost_mkt=$tot_finish_fabric_cost_mkt+$fabric_purchase_cost_mkt;
					$tot_yarn_cost_mkt+=$yarn_cost_mkt;
					$tot_yarn_value_addition_cost_mkt+=$yarn_value_addition_cost_mkt;
					$tot_grey_fab_cost_mkt+=$grey_fab_cost_mkt; 
					$tot_knit_cost_mkt+=$knit_cost_mkt; 
					$tot_dye_finish_cost_mkt+=$dye_finish_cost_mkt; 
					//$tot_yarn_dye_cost_mkt+=$yarn_dye_cost_mkt; 
					$tot_aop_cost_mkt+=$aop_cost_mkt; 
					$total_fabric_cost_mkt+=$tot_fabric_cost_mkt;
					$tot_trims_cost_mkt+=$trims_cost_mkt;
					$total_finish_fabric_cost_mkt+=$tot_finish_fabric_cost_mkt;
					$tot_print_amount_mkt+=$print_amount; 
					$tot_embro_amount_mkt+=$embroidery_amount; 
					$tot_others_amount_mkt+=$others_amount; 
					$tot_dyeing_amount_mkt+=$other_amount; 
					$tot_wash_amount_mkt+=$wash_cost; 
					
					$tot_embell_cost_mkt+=$embell_cost_mkt; 
					$tot_wash_cost_mkt+=$wash_cost_mkt; 
					$tot_commission_cost_mkt+=$commission_cost_mkt; 
					$tot_comm_cost_mkt+=$comm_cost_mkt; 
					$tot_freight_cost_mkt+=$freight_cost_mkt; 
					$tot_test_cost_mkt+=$test_cost_mkt; 
					$tot_inspection_cost_mkt+=$inspection_cost_mkt; 
					$tot_currier_cost_mkt+=$currier_cost_mkt; 
					$tot_cm_cost_mkt+=$cm_cost_mkt; 
					$tot_mkt_all_cost+=$mkt_all_cost; 
					$tot_mkt_margin+=$mkt_margin; 
					$tot_yarn_cost_actual+=$yarn_cost_actual; 
					$tot_yarn_dyeing_twist_actual+=$yarn_dyeing_twist_actual; 
					$tot_knit_cost_actual+=$knit_cost_actual; 
					$tot_dye_finish_cost_actual+=$dye_finish_cost_actual;
					$tot_yarn_dye_cost_actual+=$yarn_dye_cost_actual; 
					$tot_aop_cost_actual+=$aop_cost_actual; 
					$tot_trims_cost_actual+=$trims_cost_actual; 
					$tot_grey_fab_cost_actual+=$grey_fab_cost_actual; 
					$tot_grey_fab_transfer_amt_actual+=$grey_fab_transfer_amt_actual; 
					$tot_print_amount_actual+=$printing_cost_actual; 
					$tot_embro_amount_actual+=$embro_cost_actual; 
					$tot_others_amount_actual+=$others_cost_actual; 
					$tot_dyeing_amount_actual+=$dyeing_cost_actual; 
					$tot_wash_amount_actual+=$wash_cost_actual; 
					$tot_finished_fab_cost_actual+=$finished_fab_cost_actual;
					$tot_fin_fab_transfer_amt_actual+=$fin_fab_transfer_amt_actual;
					$tot_embell_cost_actual+=$embell_cost_actual;
					$tot_wash_cost_actual+=$wash_cost_actual; 
					$tot_commission_cost_actual+=$commission_cost_actual; 
					$tot_comm_cost_actual+=$comm_cost_actual; 
					$tot_freight_cost_actual+=$freight_cost_actual; 
					$tot_test_cost_actual+=$test_cost_actual; 
					$tot_inspection_cost_actual+=$inspection_cost_actual; 
					$tot_currier_cost_actual+=$currier_cost_actual; 
					$tot_cm_cost_actual+=$cm_cost_actual; 
					$tot_actual_all_cost+=$actual_all_cost; 
					$tot_actual_margin+=$actual_margin;
					$tot_actual_net_margin+=$actual_margin-$yarn_cost_cpa_actual; 
					
					$tot_fabric_purchase_cost_mkt+=$fabric_purchase_cost_mkt;
					$tot_fabric_purchase_cost_actual+=$fabric_purchase_cost_actual;
					$tot_grey_fabric_purchase_cost_actual+=$grey_fabric_purchase_cost_actual;
					
					$buyer_name_array[$row[csf('buyer_name')]]=$buyer_arr[$row[csf('buyer_name')]];
					$mkt_po_val_array[$row[csf('buyer_name')]]+=$po_value;
					$mkt_yarn_array[$row[csf('buyer_name')]]+=$yarn_cost_mkt;
					
					$mkt_knit_array[$row[csf('buyer_name')]]+=$knit_cost_mkt;
					$mkt_yarn_value_addition_array[$row[csf('buyer_name')]]+=$yarn_value_addition_cost_mkt;
					$mkt_dy_fin_array[$row[csf('buyer_name')]]+=$dye_finish_cost_mkt;
					$mkt_grey_fab_cost_array[$row[csf('buyer_name')]]+=$grey_fab_cost_mkt;
					$mkt_aop_array[$row[csf('buyer_name')]]+=$aop_cost_mkt;
					$mkt_trims_array[$row[csf('buyer_name')]]+=$trims_cost_mkt;
					$mkt_print_array[$row[csf('buyer_name')]]+=$print_amount;
					$mkt_embro_array[$row[csf('buyer_name')]]+=$embroidery_amount;
					$mkt_dyeing_array[$row[csf('buyer_name')]]+=$other_amount;
					//$mkt_wash_array[$row[csf('buyer_name')]]+=$wash_cost;
					
					$mkt_emb_array[$row[csf('buyer_name')]]+=$embell_cost_mkt;
					$mkt_wash_array[$row[csf('buyer_name')]]+=$wash_cost_mkt;
					$mkt_commn_array[$row[csf('buyer_name')]]+=$commission_cost_mkt;
					$mkt_commercial_array[$row[csf('buyer_name')]]+=$comm_cost_mkt;
					$mkt_freight_array[$row[csf('buyer_name')]]+=$freight_cost_mkt;
					$mkt_test_array[$row[csf('buyer_name')]]+=$test_cost_mkt;
					$mkt_ins_array[$row[csf('buyer_name')]]+=$inspection_cost_mkt;
					$mkt_courier_array[$row[csf('buyer_name')]]+=$currier_cost_mkt;
					$mkt_cm_array[$row[csf('buyer_name')]]+=$cm_cost_mkt;
					$mkt_total_array[$row[csf('buyer_name')]]+=$mkt_all_cost;
					$mkt_margin_array[$row[csf('buyer_name')]]+=$mkt_margin;
					$mkt_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_mkt;
					$mkt_fin_fabric_purchase_array[$row[csf('buyer_name')]]+=$finish_fabric_purchase_cost_mkt;
						
					$ex_factory_val_array[$row[csf('buyer_name')]]+=$ex_factory_value;
					$yarn_cost_array[$row[csf('buyer_name')]]+=$yarn_cost_actual;
					$yarn_dyeing_twist_array[$row[csf('buyer_name')]]+=$yarn_dyeing_twist_actual;
					$knit_cost_array[$row[csf('buyer_name')]]+=$knit_cost_actual;
					$dye_cost_array[$row[csf('buyer_name')]]+=$dye_finish_cost_actual;
					$grey_fab_cost_array[$row[csf('buyer_name')]]+=$grey_fab_transfer_amt_actual;//$grey_fab_cost_actual;
					$fin_fab_transfer_amt_actual_array[$row[csf('buyer_name')]]+=$fin_fab_transfer_amt_actual;
					$finished_fab_cost_actual_array[$row[csf('buyer_name')]]+=$finished_fab_cost_actual;
				
					$aop_n_others_cost_array[$row[csf('buyer_name')]]+=$aop_cost_actual;
					$trims_cost_array[$row[csf('buyer_name')]]+=$trims_cost_actual;
					
					$print_act_cost_array[$row[csf('buyer_name')]]+=$printing_cost_actual;
					$embro_act_cost_array[$row[csf('buyer_name')]]+=$embro_cost_actual;
					$others_act_cost_array[$row[csf('buyer_name')]]+=$others_cost_actual;
					$dyeing_act_cost_array[$row[csf('buyer_name')]]+=$dyeing_cost_actual;
					$wash_act_cost_array[$row[csf('buyer_name')]]+=$wash_cost_actual;
					
					$enbellishment_cost_array[$row[csf('buyer_name')]]+=$embell_cost_actual;
					$wash_cost_array[$row[csf('buyer_name')]]+=$wash_cost_actual;
					$commission_cost_array[$row[csf('buyer_name')]]+=$commission_cost_actual;
					$commercial_cost_array[$row[csf('buyer_name')]]+=$comm_cost_actual;
					$freight_cost_array[$row[csf('buyer_name')]]+=$freight_cost_actual;
					$testing_cost_array[$row[csf('buyer_name')]]+=$test_cost_actual;
					$inspection_cost_array[$row[csf('buyer_name')]]+=$inspection_cost_actual;
					$courier_cost_array[$row[csf('buyer_name')]]+=$currier_cost_actual;
					$cm_cost_array[$row[csf('buyer_name')]]+=$cm_cost_actual;
					$total_cost_array[$row[csf('buyer_name')]]+=$actual_all_cost;
					$margin_array[$row[csf('buyer_name')]]+=$actual_margin;
					$actual_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_actual;
					
					
					 
					 
			//$prod_resource_array[$val[csf('po_id')]]['pr_date']=$val[csf('pr_date')]
				 $button_po="<a href='#' onClick=\"generate_po_report('".$company_name."','".$row[csf('id')]."','".$row[csf('job_no')]."','show_po_detail_report','1')\" '> ".$row[csf('po_number')]."<a/>";
					

				 

			
				?>
                	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40" rowspan="4"><? echo $i; ?></td>
                        <td width="70" rowspan="4"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                        <td width="60" align="center" rowspan="4"><? echo $row[csf('year')]; ?></td>
                        <td width="70" align="center" rowspan="4"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="100" rowspan="4" style="word-break:break-all;"><? echo  $row[csf('po_number')];//$button_po; ?></td>
                        <td width="70" rowspan="4" style="word-break:break-all;"><? echo $row[csf('file_no')]; ?></td>
                        <td width="80" rowspan="4" style="word-break:break-all;"><? echo $row[csf('grouping')]; ?></td>
                        <td width="110" rowspan="4" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                        <td width="120" rowspan="4" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                        <td width="90" align="right"  rowspan="4"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="50" align="center" rowspan="4"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        <td width="70" align="right"  rowspan="4"><? echo number_format($unit_price,2,'.',''); ?></td>
                        <td width="110" align="right"  rowspan="4"><? echo number_format($po_value,2,'.',''); ?></td>
                        <td width="100" align="right"  rowspan="4"><? echo number_format($row[csf('set_smv')],2,'.',''); ?></td>
                        <td width="80" align="center" rowspan="4"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <td width="100" rowspan="4" style="word-break:break-all;"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                        <td width="100" align="left"><b>Pre Costing</b></td>
                        <td width="100" align="right" ><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
                        <td width="110" align="right" ><? echo number_format($po_value,2,'.',''); ?></td>
                        <td width="110"  align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $row[csf('id')]."**".$row[csf('job_no')]; ?>','mkt_yarn_cost','Grey Yarn Mkt. Cost Details')"><? echo number_format($yarn_cost_mkt,2); //$tot_yarn_qty_actual?></a><? //echo  number_format($yarn_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Yarn Value Addition Cost=Yarn Dyeing & Twisting"  align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $row[csf('id')]."**".$row[csf('job_no')]; ?>','mkt_yarn_dyeing_twisting_cost','Yarn Dyeing & Twisting Cost Details')"><? echo number_format($yarn_value_addition_cost_mkt,2); ?></a><? //echo number_format($yarn_value_addition_cost_mkt,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($knit_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Grey Fabric Transfer Cost"  align="right"><? //echo number_format($knit_cost_mkt,2,'.',''); ?></td>
                       
                        <td width="100" title="Tot Grey Fabric Cost"  align="right"><? echo number_format($grey_fab_cost_mkt,2,'.',''); ?></td>
                        <td width="110" title="Without Conv. Process Name: -Knitting,Twisting,All Over Printing,Yarn Dyeing,Weaving"  align="right"><? echo number_format($dye_finish_cost_mkt,2,'.',''); ?></td>
                       
                        <td width="110"  align="right"><? echo number_format($aop_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Finished Fabric Transfer Cost"  align="right"><? //echo number_format($aop_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Fabric Purchase Cost"  align="right"><? echo number_format($finish_fabric_purchase_cost_mkt,2,'.',''); ?></td>
                         <td width="110"  align="right"><? echo number_format($fabric_purchase_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Tot Finished Fabric Cost"  align="right"><? echo number_format($tot_finish_fabric_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><?
						
						 echo number_format($tot_fabric_cost_mkt,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($trims_cost_mkt,2,'.',''); ?></td>
                         <td width="100" title="Garment Printing Cost"  align="right"><? echo number_format($print_amount,2,'.',''); ?></td>
                         <td width="100"  align="right"><? echo number_format($embroidery_amount,2,'.',''); ?></td>
						 <td width="100"  align="right"><? echo number_format($others_amount,2,'.',''); ?></td>
                         <td width="100" title="Garment Dyeing Cost"  align="right"><? echo number_format($other_amount,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($wash_cost,2,'.',''); ?></td>
                       
                        <td width="100"  align="right"><? echo number_format($commission_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($comm_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($freight_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($test_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($inspection_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($currier_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($cm_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($mkt_all_cost,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($mkt_margin,2,'.',''); ?></td>
                        <td  width="100"  align="right"><? echo number_format($mkt_margin_perc,2,'.',''); ?></td>
                        <td width="100"  align="right"><? //echo number_format($mkt_margin,2,'.',''); ?></td>
                        <td  align="right"><? //echo number_format($mkt_margin,2,'.',''); ?></td> 
                    </tr>
                    <tr bgcolor="#F5F5F5" onClick="change_color('tr_a<? echo $i; ?>','#F5F5F5')" id="tr_a<? echo $i; ?>">
                    	<td width="100" align="left"><b>Actual</b></td>
                        <td width="100" align="right" >
						<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no_prefix_num')];?>','<? echo $row[csf('id')]; ?>','650px')"><? echo  number_format($ex_factory_qty,0,'.',''); ?></a>
						<? //echo number_format($ex_factory_qty,0,'.',''); ?></td>
                        <td width="110" align="right" ><? echo number_format($ex_factory_value,2,'.',''); ?></td>
                        <td width="110"  align="right"><a href="#report_details" onClick="openmypage_actual2('<? echo $row[csf('id')]; ?>','yarn_cost_actual','Actual Yarn Cost Details','1','1020px')"><? echo number_format($yarn_cost_actual,2,'.',''); ?></a></td>
                          <td width="100" title="Yarn Dying With Order, Yarn Service Work Order(Rate): Yarn Recv: Yarn Dyeing/Twisting/Re-Waxing Cost :<? echo $order_amount_recv.' - Recv Ret: '.$order_amount_recv_ret.'/ Job Qty :'.$job_quantity.'* Po Qty :'.$order_qnty_in_pcs;?>"  align="right">
						  <a href="#report_details"  onClick="openmypage_actual2('<? echo $row[csf('id')]; ?>','yarn_dye_twist_cost_actual','Actual Yarn Dyeing Twsit Cost Details','2','930px')"><? echo number_format($yarn_dyeing_twist_actual,2,'.',''); ?></a>
						  <? //echo number_format($yarn_dyeing_twist_actual,2,'.',''); ?></td>
                        <td width="110" title="<?=$grey_fab_array[$row[csf('id')]]['qnty'].'*'.$knit_rate;?>"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','knit_cost_actual','Actual Knitting Cost Details','900px')"><? echo number_format($grey_fab_array[$row[csf('id')]]['qnty']*$knit_rate,2,'.',''); ?></a><? //echo number_format($knit_cost_actual,2,'.',''); ?></td>
                         <td width="100" title="Grey Fabric Transfer Cost<? echo $knit_charge_mkt;?>"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','grey_fabric_transfer_cost_actual','Grey Fabric Transfer Cost Details','900px')"><? echo number_format($grey_fab_transfer_amt_actual,2,'.',''); ?></a><?// echo number_format($grey_fab_transfer_amt_actual,2,'.',''); ?></td>
                          
                        
                        <td width="100" title="Grey Yarn Cost+Yarn Dyeing/Twisting/Re-Waxing Cost+Knitting Cost+Grey Fabric Transfer Cost
  "  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','knit_cost_actual','Actual Knitting Cost Details','900px')"><? //echo number_format($grey_fab_cost_actual,2,'.',''); ?></a><? echo number_format($grey_fab_cost_actual,2,'.',''); ?></td>
                        <td width="110"  title="<?=$order_wise_fin_fab_rec[$row[csf('id')]]['used_qty'].'*'.$dye_rate;?>" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','dye_finish_cost_actual','Actual Dyeing & Finish Cost Details','900px')"><? echo number_format($order_wise_fin_fab_rec[$row[csf('id')]]['used_qty']*$dye_rate,2,'.',''); ?></a><? //echo number_format($dye_finish_cost_actual,2,'.',''); ?></td>
                       
                        <td width="110"  align="right" title="<? echo 'Fabric Service Receive'; ?>"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual','AOP Cost Details','800px')"><? echo number_format($aop_cost_actual,2,'.',''); ?></a><? //echo number_format($aop_cost_actual,2,'.',''); ?></td>
                          <td width="100" title="<? echo $order_wise_fin_fab_rec[$row[csf('id')]]['transfer_qnty']."*".$dye_rate;?>"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual','Fin Feb Transfer Cost Details','800px')"><? //echo number_format($fin_fab_transfer_amt_actual,2,'.',''); ?></a><? echo number_format($fin_fab_transfer_amt_actual,2,'.',''); ?></td>
                           <td width="100" title="From- Finish Fabric Receive Entry"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual','AOP Cost Details','800px')"><? //echo number_format($fabric_purchase_cost_actual,2,'.',''); ?></a><? echo number_format($fabric_purchase_cost_actual,2,'.',''); ?></td>
                           <td width="110" title="From: Knit Grey Fabric Receive and Woven Grey Fabric Receive"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','fabric_purchase_cost_actual','Fabric Purchase Cost Details','900px')"><? //echo number_format($grey_fabric_purchase_cost_actual,2,'.',''); ?></a><? echo number_format($grey_fabric_purchase_cost_actual,2,'.',''); ?></td>
                            <td width="100" title=""  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual','AOP Cost Details','800px')"><? //echo number_format($finished_fab_cost_actual,2,'.',''); ?></a><? echo number_format($finished_fab_cost_actual,2,'.',''); ?></td>
                             <td width="100" title="Tot Fab. Cost"  align="right"><? echo number_format($tot_fabric_cost_act,2,'.',''); ?></td>
                             
                        <td width="110"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','trims_cost_actual','Trims Cost Details','800px')"><? echo number_format($trims_cost_actual,2,'.',''); ?></a></td>
                        <td width="100"  title="Garment Printing Cost" align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','1','gmt_print_wash_dye_embell_cost_actual','Print Cost Details','800px')"><? echo number_format($printing_cost_actual,2,'.',''); ?></a></td>
                        <td width="100" title="Garment Embro Cost"  align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','2','gmt_print_wash_dye_embell_cost_actual','Embro Cost Details','800px')"><? echo number_format($embro_cost_actual,2,'.',''); ?></a></td>
						<td width="100" title="Garment Embro Cost"  align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','2','gmt_print_wash_dye_embell_cost_actual','others Cost Details','800px')"><? //echo number_format($embro_cost_actual,2,'.',''); ?></a><? echo number_format($others_cost_actual,2,'.',''); ?></td>
                         <td width="100" title="Garment Dyeing Cost"  align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','5','gmt_print_wash_dye_embell_cost_actual','Trims Cost Details','800px')"><? echo number_format($dyeing_cost_actual,2,'.',''); ?></a></td>
                          <td width="100" title="Garment Wash Cost"  align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','3','gmt_print_wash_dye_embell_cost_actual','Trims Cost Details','800px')"><? echo number_format($wash_cost_actual,2,'.',''); ?></a></td>
                         
                        <td width="100"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]."_".$row[csf('job_no')]."_".$ex_factory_qty."_".$dzn_qnty; ?>','commission_cost_actual','Commission Cost Details','600px')"><? echo number_format($commission_cost_actual,2,'.',''); ?></a></td>
                        <td width="100"  align="right"><? echo number_format($comm_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($freight_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($test_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($inspection_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($currier_cost_actual,2,'.',''); ?></td>
                        <?
                        //	$cm_cost_actual=($tot_working_hr*60*$tot_number_of_employee*$cpm)/$exchange_rate;
						?>
                        <td width="100"  title="Working Hr(<? echo $tot_working_hr;?>)*60*Number Of Employee(<? echo $tot_number_of_employee;?>)*CPM(<? echo $cpm;?>)/ExchangeRate(<? echo $exchange_rate;?>)" align="right"><? echo number_format($cm_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($actual_all_cost,2,'.',''); ?></td>
                        <td width="100"  align="right" title="Ex factory value-Actual all cost;"><? echo number_format($actual_margin,2,'.',''); ?></td>
                        <td  width="100" align="right"  title="Margin /Ex Fact Value*100"><? echo number_format($actual_margin_perc,2,'.',''); ?></td>
                        
                        <td width="100" title="Short Booking,yarn issue for Grey Yarn Amount"  align="right"><? echo number_format($yarn_cost_cpa_actual,2,'.',''); ?></td>
                        <td   align="right"><? echo number_format($actual_margin-$yarn_cost_cpa_actual,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9E9E9" onClick="change_color('tr_v<? echo $i; ?>','#E9E9E9')" id="tr_v<? echo $i; ?>">
                    	<td width="100" align="left"><b>Variance</b></td>
                    	<td width="100" align="right" >
							<? 
								 $variance_qnty=$order_qnty_in_pcs-$ex_factory_qty;
								 $variance_qnty_per=($variance_qnty/$order_qnty_in_pcs)*100; 
								 echo number_format($variance_qnty,0,'.',''); 
							?>
                        </td>
                    	<td width="110" align="right" >
                        	<? 
								$variance_po_value=$po_value-$ex_factory_value;
								$variance_po_value_per=($variance_po_value/$po_value)*100; 
								echo number_format($variance_po_value,2,'.',''); 
							?>
                        </td>
                        <td width="110"  align="right">
                        	<? 
								$variance_yarn_cost=($yarn_cost_mkt+$yarn_dye_cost_mkt)-$yarn_cost_actual;
								if($variance_yarn_cost==0)
								{
									$variance_yarn_cost_per=0;
								}
								else
								{
									$variance_yarn_cost_per=($variance_yarn_cost/($yarn_cost_mkt+$yarn_dye_cost_mkt))*100; 
								}
								echo number_format($variance_yarn_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100" title="Yarn Dyeing/Twisting/Re-Waxing Cost"  align="right">
                        	<? 
								$variance_yarn_value_addition_cost=$yarn_value_addition_cost_mkt-$yarn_dyeing_twist_actual;
								//$variance_yarn_value_addition_cost_per=($variance_yarn_value_addition_cost/$yarn_value_addition_cost_mkt)*100;
								if($variance_yarn_value_addition_cost==0 && $yarn_value_addition_cost_mkt==0)
								{
									$variance_yarn_value_addition_cost_per=0;
								}
								else
								{
									$variance_yarn_value_addition_cost_per=($variance_yarn_value_addition_cost/$yarn_value_addition_cost_mkt)*100;
								} 
								echo number_format($variance_yarn_value_addition_cost,2,'.',''); 
							?>
                        </td>
                        <td width="110"  align="right">
                        	<? 
								$variance_kint_cost=$knit_cost_mkt-$knit_cost_actual;
								if($variance_kint_cost==0)
								{
									$variance_knit_cost_per=0;
								}
								else
								{
									$variance_knit_cost_per=($variance_kint_cost/$knit_cost_mkt)*100; 
								}
								echo number_format($variance_kint_cost,2,'.',''); 
							?>
                        </td>
                         <td width="100" title="Grey Fabric Transfer Cost"  align="right">
                        	<? 
								//$variance_kint_cost=$knit_cost_mkt-$knit_cost_actual;
								//$variance_knit_cost_per=($variance_kint_cost/$knit_cost_mkt)*100; 
								//echo number_format($variance_kint_cost,2,'.',''); 
							?>
                        </td>
                        
                         <td width="100" title="Tot Grey Fabric Cost"  align="right">
                        	<? 
								$variance_grey_fab_cost=$grey_fab_cost_mkt-$grey_fab_cost_actual;
								if($variance_grey_fab_cost==0 )
								{
									$variance_grey_fab_cost_per=0;
								}
								else
								{
									$variance_grey_fab_cost_per=($variance_grey_fab_cost/$grey_fab_cost_mkt)*100; 
								}
								echo number_format($variance_grey_fab_cost,2,'.',''); 
							?>
                        </td>
                        <td width="110"  align="right">
                        	<? 
								$variance_dye_finish_cost=$dye_finish_cost_mkt-$order_wise_fin_fab_rec[$row[csf('id')]]['used_qty']*$dye_rate;
								if($variance_dye_finish_cost==0 && $dye_finish_cost_mkt==0)
								{
									$variance_dye_finish_cost_per=0;
								}
								else
								{
									$variance_dye_finish_cost_per=($variance_dye_finish_cost/$dye_finish_cost_mkt)*100; 
								}
								echo number_format($variance_dye_finish_cost,2,'.','');
							?>
                        </td>
                        <td width="110"  align="right">
                        	<? 
								$variance_aop_cost=$aop_cost_mkt-$aop_cost_actual;
								if($variance_aop_cost==0)
								{
									$variance_aop_cost_per=0;
								}
								else
								{
									$variance_aop_cost_per=($variance_aop_cost/$aop_cost_mkt)*100; 
								}
								echo number_format($variance_aop_cost,2,'.',''); 
							?>
                        </td>
                          <td width="100" title="Finished Fabric Transfer Cost"  align="right">
                        	<? 
								//$variance_aop_cost=$aop_cost_mkt-$aop_cost_actual;
								//$variance_aop_cost_per=($variance_aop_cost/$aop_cost_mkt)*100; 
								//echo number_format($variance_aop_cost,2,'.',''); 
							?>
                        </td>
                         <td width="100" title="Finished Fabric Purchase Cost"  align="right">
                        	<? 
								$variance_fin_fabric_purchase_cost=$finish_fabric_purchase_cost_mkt-$fabric_purchase_cost_actual;
								//$variance_fin_fabric_cost_per=($variance_fin_fabric_purchase_cost/$finish_fabric_purchase_cost_mkt)*100;
								if($variance_fin_fabric_purchase_cost==0)
								{
									$variance_fin_fabric_cost_per=0;
								}
								else
								{
									$variance_fin_fabric_cost_per=($variance_fin_fabric_purchase_cost/$finish_fabric_purchase_cost_mkt)*100; 
								} 
								echo number_format($variance_fin_fabric_purchase_cost,2,'.',''); 
							?>
                        </td>
                         <td width="110"  align="right">
                        	<? 
								$variance_finish_purchase_cost=$fabric_purchase_cost_mkt-$fabric_purchase_cost_actual;
								//$variance_finish_purchase_cost_per=($variance_finish_purchase_cost/$fabric_purchase_cost_mkt)*100; 
								if($variance_finish_purchase_cost==0 && $fabric_purchase_cost_mkt==0)
								{
									$variance_finish_purchase_cost_per=0;
								}
								else
								{
									$variance_finish_purchase_cost_per=($variance_finish_purchase_cost/$fabric_purchase_cost_mkt)*100; 
								}
								echo number_format($variance_finish_purchase_cost,2,'.','');
							?>
                        </td>
                         <td width="100" title="Finished Fabric Cost"  align="right">
                        	<? 
								//$variance_finished_fab_cost=$tot_finish_fabric_cost_mkt-$finished_fab_cost_actual;
								//$variance_finished_fab_cost_per=($variance_finished_fab_cost/$finished_fab_cost_mkt)*100;
								$variance_finished_fab_cost=$tot_finish_fabric_cost_mkt-$finished_fab_cost_actual;
							//	echo $variance_finished_fab_cost.'='.$finished_fab_cost_mkt;
								if($variance_finished_fab_cost==0)
								{
									$variance_finished_fab_cost_per=0;
								}
								else
								{
									$variance_finished_fab_cost_per=($variance_finished_fab_cost/$tot_finish_fabric_cost_mkt)*100; 
								} 
								echo number_format($variance_finished_fab_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100" title="Total Fabric Cost"  align="right">
                        	<? 
								$variance_tot_fabric_cost=$tot_fabric_cost_mkt-$tot_fabric_cost_act;
								//$variance_tot_fabric_cost_per=($variance_tot_fabric_cost/$tot_fabric_cost_mkt)*100; 
								if($variance_tot_fabric_cost==0 && $tot_fabric_cost_mkt==0)
								{
									$variance_tot_fabric_cost_per=0;
								}
								else
								{
									$variance_tot_fabric_cost_per=($variance_tot_fabric_cost/$tot_fabric_cost_mkt)*100; 
								}
								echo number_format($variance_tot_fabric_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_trims_cost=$trims_cost_mkt-$trims_cost_actual;
								if($variance_trims_cost==0)
								{
									$variance_trims_cost_per=0;
								}
								else
								{
									$variance_trims_cost_per=($variance_trims_cost/$trims_cost_mkt)*100; 
								}
								echo number_format($variance_trims_cost,2,'.','');
							?>
                        </td>
                         <td width="100" title="Garment Printing Cost"  align="right">
                        	<? 
								$variance_print_cost=$print_amount-$printing_cost_actual;
								if($variance_print_cost==0)
								{
									$variance_print_cost_per=0;
								}
								else
								{
									$variance_print_cost_per=($variance_print_cost/$print_amount)*100; 
								}
								echo number_format($variance_print_cost,2,'.','');
							?>
                        </td>
                       
						<td width="100" title="Garment Embro Cost"  align="right">
                        	<? 
								$variance_embro_cost=$embroidery_amount-$embro_cost_actual;
								if($variance_embro_cost==0 && $embroidery_amount==0)
								{
									$variance_embro_cost_per=0;
								}
								else
								{
									$variance_embro_cost_per=($variance_embro_cost/$embroidery_amount)*100; 
								}
								echo number_format($variance_embro_cost,2,'.','');
							?>
                        </td>
						<td width="100" title="Garment Embro Cost"  align="right">
                        	<? 
								$variance_others_cost=$others_amount-$others_cost_actual;
								if($variance_others_cost==0 && $others_amount==0)
								{
									$variance_others_cost_per=0;
								}
								else
								{
									$variance_others_cost_per=($variance_others_cost/$others_amount)*100; 
								}
								echo number_format($variance_others_cost,2,'.','');
							?>
                        </td>
                         <td width="100" title="Garment Dyeing Cost"  align="right">
                        	<? 
								$variance_dyeing_cost=$other_amount-$dyeing_cost_actual;
								if($variance_dyeing_cost==0 && $other_amount==0)
								{
									$variance_dyeing_cost_per=0;
								}
								else
								{
									$variance_dyeing_cost_per=($variance_dyeing_cost/$other_amount)*100; 
								}
								echo number_format($variance_dyeing_cost,2,'.','');
							?>
                        </td>
                         <td width="100" title="Garment Wasing Cost"  align="right">
                        	<? 
								$variance_wash_cost=$wash_cost-$wash_cost_actual;
							if($variance_wash_cost==0 )
								{
									$variance_wash_cost_per=0;
								}
								else
								{
									$variance_wash_cost_per=($variance_wash_cost/$wash_cost)*100; 
								}
								echo number_format($variance_wash_cost_per,2,'.','');
							?>
                        </td>
                     
                        <td width="100"  align="right">
                        	<? 
								$variance_commission_cost=$commission_cost_mkt-$commission_cost_actual;
								if($variance_commission_cost==0 && $commission_cost_mkt==0)
								{
									$variance_commission_cost_per=0;
								}
								else
								{
									$variance_commission_cost_per=($variance_commission_cost/$commission_cost_mkt)*100; 
								}
								echo number_format($variance_commission_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_comm_cost=$comm_cost_mkt-$comm_cost_actual;
								if($variance_comm_cost==0 )
								{
									$variance_commission_cost_per=0;
								}
								else
								{
									$variance_comm_cost_per=($variance_comm_cost/$comm_cost_mkt)*100; 
								}
								echo number_format($variance_comm_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_freight_cost=$freight_cost_mkt-$freight_cost_actual;
								if($variance_freight_cost==0 && $freight_cost_mkt==0)
								{
									$variance_freight_cost_per=0;
								}
								else
								{
									$variance_freight_cost_per=($variance_freight_cost/$freight_cost_mkt)*100; 
								}
								echo number_format($variance_freight_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_test_cost=$test_cost_mkt-$test_cost_actual;
								if($variance_test_cost==0 && $test_cost_mkt==0)
								{
									$variance_test_cost_per=0;
								}
								else
								{
									$variance_test_cost_per=($variance_test_cost/$test_cost_mkt)*100; 
								}
								echo number_format($variance_test_cost,2,'.','');
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_inspection_cost=$inspection_cost_mkt-$inspection_cost_actual;
								if($variance_inspection_cost==0 && $inspection_cost_mkt==0)
								{
									$variance_inspection_cost_per=0;
								}
								else
								{
									$variance_inspection_cost_per=($variance_inspection_cost/$inspection_cost_mkt)*100; 
								}
								echo number_format($variance_inspection_cost,2,'.','');
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_currier_cost=$currier_cost_mkt-$currier_cost_actual;
								if($variance_currier_cost==0 )
								{
									$variance_currier_cost_per=0;
								}
								else
								{
									$variance_currier_cost_per=($variance_currier_cost/$currier_cost_mkt)*100; 
								}
								echo number_format($variance_currier_cost,2,'.','');
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_cm_cost=$cm_cost_mkt-$cm_cost_actual;
								if($variance_cm_cost_per==0)
								{
									$variance_cm_cost_per=0;
								}
								else
								{
									$variance_cm_cost_per=($variance_cm_cost/$cm_cost_mkt)*100; 
								}
								echo number_format($variance_cm_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_all_cost=$mkt_all_cost-$actual_all_cost;
								if($variance_all_cost==0)
								{
									$variance_all_cost_per=0;
								}
								else
								{
									$variance_all_cost_per=($variance_all_cost/$mkt_all_cost)*100; 
								}
								
								echo number_format($variance_all_cost,2,'.','');
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_margin_cost=$mkt_margin-$actual_margin;
								if($variance_margin_cost==0)
								{
									$variance_margin_cost_per=0;
								}
								else
								{
									$variance_margin_cost_per=($variance_margin_cost/$mkt_margin)*100; 
								}
								echo number_format($variance_margin_cost,2,'.','');
							?>
                        </td>
                        <td  width="100" align="right">
                        	<? 
								$variance_per_cost=$mkt_margin_perc-$actual_margin_perc;
								if($variance_per_cost==0)
								{
									$variance_per_cost_per=0;
								}
								else
								{
									$variance_per_cost_per=($variance_per_cost/$mkt_margin_perc)*100; 
								}
								echo number_format($variance_per_cost,2,'.','');
							?>
                        </td>
                         <td width="100"  align="right">
                        	<? 
								/*$variance_margin_cost=$mkt_margin-$actual_margin;
								$variance_margin_cost_per=($variance_margin_cost/$mkt_margin)*100; 
								echo number_format($variance_margin_cost,2,'.','');*/
							?>
                        </td>
                         <td   align="right">
                        	<? 
								/*$variance_margin_cost=$mkt_margin-$actual_margin;
								$variance_margin_cost_per=($variance_margin_cost/$mkt_margin)*100; 
								echo number_format($variance_margin_cost,2,'.','');*/
							?>
                        </td>
                        
                    </tr>
                    <tr bgcolor="#DFDFDF" onClick="change_color('tr_vp<? echo $i; ?>','#DFDFDF')" id="tr_vp<? echo $i; ?>">
                    	<td width="100" align="left"><b>Variance (%)</b></td>
                    	<td width="100" align="right" ><? echo number_format($variance_qnty_per,2,'.',''); ?></td>
                    	<td width="110" align="right" ><? echo number_format($variance_po_value_per,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($variance_yarn_cost_per,2,'.',''); ?></td>
                          <td width="100" title="Yarn Dyeing/Twisting/Re-Waxing Cost"  align="right"><? echo number_format($variance_yarn_value_addition_cost_per,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($variance_knit_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? //echo number_format($variance_knit_cost_per,2,'.',''); ?></td>
                       
                        <td width="100" title="Grey Fabric Cost"  align="right"><? echo number_format($variance_grey_fab_cost_per,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($variance_dye_finish_cost_per,2,'.',''); ?></td>
                       
                        <td width="110"  align="right"><? echo number_format($variance_aop_cost_per,2,'.',''); ?></td>
                        <td width="100" title="Finished Fabric Transfer Cost"  align="right"><? //echo number_format($variance_aop_cost_per,2,'.',''); ?></td>
                         <td width="100" title="Finished Fabric Purchase Cost"  align="right"><? echo number_format($variance_fin_fabric_cost_per,2,'.',''); ?></td>
                           <td width="110"  align="right"><? echo number_format($variance_finish_purchase_cost_per,2,'.',''); ?></td>
                         <td width="100" title="Tot Finished Fabric Cost"  align="right"><? echo number_format($variance_finished_fab_cost_per,2,'.',''); ?></td>
                         <td width="100"  align="right"><?  echo number_format($variance_tot_fabric_cost_per,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($variance_trims_cost_per,2,'.',''); ?></td>
                        <td width="100" title="Garment Printing Cost"  align="right"><? echo number_format($variance_print_cost_per,2,'.',''); ?></td>
                        <td width="100" title="Garment Embro Cost"  align="right"><? echo number_format($variance_embro_cost_per,2,'.',''); ?></td>
						<td width="100" title="Garment Others Cost"  align="right"><? echo number_format($variance_others_cost_per,2,'.',''); ?></td>
                        <td width="100" title="Garment Dyeing Cost"  align="right"><? echo number_format($variance_dyeing_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_wash_cost_per,2,'.',''); ?></td>
                        
                      
                        <td width="100"  align="right"><? echo number_format($variance_commission_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_comm_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_freight_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_test_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_inspection_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_currier_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_cm_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_all_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_margin_cost_per,2,'.',''); ?></td>
                        <td   width="100" align="right"><? echo number_format($variance_per_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? //echo number_format($variance_margin_cost_per,2,'.',''); ?></td>
                        <td   align="right"><? //echo number_format($variance_margin_cost_per,2,'.',''); ?></td>
                    </tr>
				<?
					$i++;
				}
				
				$tot_mkt_margin_perc=($tot_mkt_margin/$tot_po_value)*100;
				if($tot_ex_factory_val==0)
				{
					$tot_actual_margin_perc=0;
				}
				else
				{
					$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
				}
				
				//$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
				
				?>
            </table>
		</div>
        <table class="rpt_table" width="4730" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr bgcolor="#CCDDEE" onClick="change_color('tr_pt','#CCDDEE')" id="tr_pt" style="font-weight:bold;">
              <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="120" align="">Total</td>
               
                <td width="90"  align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?></td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110"  align="right"><? echo number_format($tot_po_value,2,'.',''); ?></td>
                <td width="100"  align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Pre Costing Total</td>
                <td width="100"  align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_po_value,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_yarn_cost_mkt,2,'.',''); ?></td>
                 <td width="100" title="Yarn Dyeing/Twisting/Re-Waxing Cost"  align="right"><? echo number_format($tot_yarn_value_addition_cost_mkt,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_knit_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? //echo number_format($tot_knit_cost_mkt,2,'.',''); ?></td>
               
                <td width="100" title="Grey Fabric Cost"  align="right"><? echo number_format($tot_grey_fab_cost_mkt,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_dye_finish_cost_mkt,2,'.',''); ?></td>
              
                
                <td width="110"  align="right"><? echo number_format($tot_aop_cost_mkt,2,'.',''); ?></td>
                <td width="100" title="Finished Fabric Transfer Cost"  align="right"><? //echo number_format($tot_yarn_dye_cost_mkt,2,'.',''); ?></td> 
                 <td width="100" title="Finished Fabric Purchase Cost"  align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2,'.',''); ?></td> 
                   <td width="110"  align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2,'.',''); ?></td>
                 <td width="100" title="Tot Finished Fabric Cost"  align="right"><? echo number_format($total_finish_fabric_cost_mkt,2,'.',''); ?></td> 
                 <td width="100"  align="right"><? echo number_format($total_fabric_cost_mkt,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_trims_cost_mkt,2,'.',''); ?></td>
                <td width="100" title="Garment Printing Cost"  align="right"><? echo number_format($tot_print_amount_mkt,2,'.',''); ?></td> 
                <td width="100" title="Garment Embroidery Cost"  align="right"><? echo number_format($tot_embro_amount_mkt,2,'.',''); ?></td> 
				<td width="100" title="Garment Others Cost"  align="right"><? echo number_format($tot_Others_amount_mkt,2,'.',''); ?></td> 
                <td width="100" title="Garment Dyeing Cost"  align="right"><? echo number_format($tot_dyeing_amount_mkt,2,'.',''); ?></td> 
                <td width="100" title="Garment Wash Cost"  align="right"><? echo number_format($tot_wash_amount_mkt,2,'.',''); ?></td> 
                
               
                <td width="100"  align="right"><? echo number_format($tot_commission_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_comm_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_freight_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_test_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_inspection_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_currier_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_cm_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_mkt_all_cost,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_mkt_margin,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_mkt_margin_perc,2,'.',''); ?></td>
                <td width="100"  align="right"><? //echo number_format($tot_mkt_margin,2,'.',''); ?></td>
                <td width=""  align="right"><? //echo number_format($tot_mkt_margin,2,'.',''); ?></td>
            </tr>
            <? //echo die;?>
            <tr bgcolor="#CCCCFF" onClick="change_color('tr_at','#CCCCFF')" id="tr_at" style="font-weight:bold;">
                <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="120" align="">&nbsp;</td>
                <td width="90"  align="right">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110"  align="right">&nbsp;</td>
                <td width="100"  align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Actual Total</td>
                <td width="100" align="right" ><? echo number_format($tot_ex_factory_qnty,0,'.',''); ?></td>
                <td width="110" align="right" ><? echo number_format($tot_ex_factory_val,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_yarn_cost_actual,2,'.',''); ?></td>
                <td width="100" title="Yarn Dyeing/Twisting/Re-Waxing Cost"  align="right"><? echo number_format($tot_yarn_dyeing_twist_actual,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_knit_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_grey_fab_transfer_amt_actual,2,'.',''); ?></td>
               
                <td width="100" title="Grey Fabric Cost"  align="right"><? echo number_format($tot_grey_fab_cost_actual,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_dye_finish_cost_actual,2,'.',''); ?></td>
               
               
                <td width="110"  align="right"><? echo number_format($tot_aop_cost_actual,2,'.',''); ?></td>
                <td width="100" title="Finished Fabric Transfer Cost act"  align="right"><? echo number_format($tot_fin_fab_transfer_amt_actual,2,'.',''); ?></td>
                <td width="100" title="Finished Fabric Purchase Cost act"  align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2,'.',''); ?></td>
                 <td width="110"  align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2,'.',''); ?></td>
                <td width="100" title="Tot Finished Fabric Cost act"  align="right"><? echo number_format($tot_finished_fab_cost_actual,2,'.',''); ?></td>
                 <td width="100" title="Total Fab. Cost" align="right" ><? echo number_format($tot_fabric_cost_actaul,2,'.',''); ?></td>
                <td width="110" align="right" ><? echo number_format($tot_trims_cost_actual,2,'.',''); ?></td>
                <td width="100" title="Garment Printing Cost"  align="right"><? echo number_format($tot_print_amount_actual,2,'.',''); ?></td>
                <td width="100" title="Garment Embro Cost"  align="right"><? echo number_format($tot_embro_amount_actual,2,'.',''); ?></td>
				<td width="100" title="Garment Others Cost"  align="right"><? echo number_format($tot_others_amount_actual,2,'.',''); ?></td>
                <td width="100" title="Garment Dyeing Cost"  align="right"><? echo number_format($tot_dyeing_amount_actual,2,'.',''); ?></td>
                <td width="100" title="Garment Wash Cost"  align="right"><? echo number_format($tot_wash_amount_actual,2,'.',''); ?></td>
                 
                 
               
                <td width="100"  align="right"><? echo number_format($tot_commission_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_comm_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_freight_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_test_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_inspection_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_currier_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_cm_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_actual_all_cost,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_actual_margin,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_actual_margin_perc,2,'.',''); ?></td>
               <td width="100"  align="right"><? echo number_format($tot_yarn_qty_cpa_actual,2,'.',''); ?></td>
               <td width=""  align="right"><? echo number_format($tot_actual_net_margin,2,'.',''); ?></td>
            </tr>
            <tr bgcolor="#FFEEFF" onClick="change_color('tr_vt','#FFEEFF')" id="tr_vt" style="font-weight:bold;">
                <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="120" align="">&nbsp;</td>
                <td width="90"  align="right">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110"  align="right">&nbsp;</td>
                <td width="100"  align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Variance Total</td>
                <td width="100" align="right" >
					<? 
                        $tot_variance_qnty=$tot_po_qnty-$tot_ex_factory_qnty;
                        $tot_variance_qnty_per=($tot_variance_qnty/$tot_po_qnty)*100; 
                        echo number_format($tot_variance_qnty,0,'.',''); 
                    ?>
                </td>
                <td width="110" align="right" >
                    <? 
                        $tot_variance_po_value=$tot_po_value-$tot_ex_factory_val;
                        $tot_variance_po_value_per=($tot_variance_po_value/$tot_po_value)*100; 
                        echo number_format($tot_variance_po_value,2,'.',''); 
                    ?>
                </td>
                <td width="110"  align="right">
                    <? 
                        $tot_variance_yarn_cost=$tot_yarn_cost_mkt-$tot_yarn_cost_actual;
                        $tot_variance_yarn_cost_per=($tot_variance_yarn_cost/$tot_yarn_cost_mkt)*100; 
                        echo number_format($tot_variance_yarn_cost,2,'.',''); 
                    ?>
                </td>
                 <td width="100"  align="right">
                    <? 
                        $tot_variance_yarn_value_addition_cost=$tot_yarn_value_addition_cost_mkt-$tot_yarn_dyeing_twist_actual;
						if($tot_variance_yarn_value_addition_cost==0)
						{
						$tot_variance_yarn_value_addition_cost_per=0;
						}
						else
						{
						$tot_variance_yarn_value_addition_cost_per=($tot_variance_yarn_value_addition_cost/$tot_yarn_value_addition_cost_mkt)*100; 
						}
                       // $tot_variance_yarn_value_addition_cost_per=($tot_variance_yarn_value_addition_cost/$tot_yarn_value_addition_cost_mkt)*100; 
                        echo number_format($tot_variance_yarn_value_addition_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_kint_cost=$tot_knit_cost_mkt-$tot_knit_cost_actual;
                        $tot_variance_knit_cost_per=($tot_variance_kint_cost/$tot_knit_cost_mkt)*100; 
                        echo number_format($tot_variance_kint_cost,2,'.',''); 
                    ?>
                </td>
                 <td width="100"  align="right">
                    <? 
                        //$tot_variance_kint_cost=$tot_knit_cost_mkt-$tot_knit_cost_actual;
                       // $tot_variance_knit_cost_per=($tot_variance_kint_cost/$tot_knit_cost_mkt)*100; 
                       // echo number_format($tot_variance_kint_cost,2,'.',''); 
                    ?>
                </td>
               
                 <td width="100"  title="Grey Fabric Cost" align="right">
                    <? 
                        $tot_variance_grey_fab_cost=$tot_grey_fab_cost_mkt-$tot_grey_fab_cost_actual;
                      if($tot_variance_grey_fab_cost==0)
						{
							$tot_variance_grey_fab_cost_per=0;
						}
						else
						{
							 $tot_variance_grey_fab_cost_per=($tot_variance_grey_fab_cost/$tot_grey_fab_cost_mkt)*100; 
						}    
                        echo number_format($tot_variance_grey_fab_cost,2,'.',''); 
                    ?>
                </td>
                <td width="110"  align="right">
                    <? 
                        $tot_variance_dye_finish_cost=$tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual;
                      if($tot_variance_dye_finish_cost==0)
						{
							$tot_variance_dye_finish_cost_per=0;
						}
						else
						{
							 $tot_variance_dye_finish_cost_per=($tot_variance_dye_finish_cost/$tot_dye_finish_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_dye_finish_cost,2,'.','');
                    ?>
                </td>
              
                <td width="110"  align="right">
                    <? 
                        $tot_variance_aop_cost=$tot_aop_cost_mkt-$tot_aop_cost_actual;
                      if($tot_variance_aop_cost==0)
						{
							$tot_variance_aop_cost_per=0;
						}
						else
						{
							  $tot_variance_aop_cost_per=($tot_variance_aop_cost/$tot_aop_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_aop_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  title="Fabric Purchase Trans Cost"  align="right">
                    <? 
                        /*$tot_variance_aop_cost=$tot_aop_cost_mkt-$tot_aop_cost_actual;
                        $tot_variance_aop_cost_per=($tot_variance_aop_cost/$tot_aop_cost_mkt)*100; 
                        echo number_format($tot_variance_aop_cost,2,'.','');*/ 
                    ?>
                </td>
                <td width="100" title="Fabric Purchase Cost" align="right">
                    <? 
                       $tot_variance_fin_fabric_purchase_cost=$tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual;
                       if($tot_variance_fin_fabric_purchase_cost==0)
						{
							$tot_variance_fin_fabric_purchase_cost_per=0;
						}
						else
						{
							 $tot_variance_fin_fabric_purchase_cost_per=($tot_variance_fin_fabric_purchase_cost/$tot_fabric_purchase_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_fin_fabric_purchase_cost,2,'.','');
                    ?>
                </td>
                  <td width="110"  align="right">
                    <? 
                        $tot_variance_fabric_purchase_cost=$tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual;
                        if($tot_variance_fabric_purchase_cost==0)
						{
							$tot_variance_fabric_purchase_cost_per=0;
						}
						else
						{
							  $tot_variance_fabric_purchase_cost_per=($tot_variance_fabric_purchase_cost/$tot_fabric_purchase_cost_mkt)*100; 
						}
                        echo number_format($tot_variance_fabric_purchase_cost,2,'.','');
                    ?>
                </td>
                <td width="100" title="Tot Fin Fabric  Cost"  align="right">
                    <? 
                       $tot_variance_fin_fab_cost=$total_finish_fabric_cost_mkt-$tot_finished_fab_cost_actual;
                       if($tot_variance_fin_fab_cost==0)
						{
							$tot_variance_fin_fab_cost_per=0;
						}
						else
						{
							   $tot_variance_fin_fab_cost_per=($tot_variance_fin_fab_cost/$total_finish_fabric_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_fin_fab_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100" title="Tot Fabric  Cost"  align="right">
                    <? 
                        $tot_variance_fabric_cost_mkt=$total_fabric_cost_mkt-$tot_fabric_cost_actaul;
                      if($tot_variance_fabric_cost_mkt==0)
						{
							$tot_variance_fabric_cost_mkt_per=0;
						}
						else
						{
							 $tot_variance_fabric_cost_mkt_per=($tot_variance_fabric_cost_mkt/$tot_fabric_cost_mkt)*100;  
						} 
						
                        echo number_format($tot_variance_fabric_cost_mkt,2,'.','');
                    ?>
                </td>
                <td width="110"  align="right">
                    <? 
                        $tot_variance_trims_cost=$tot_trims_cost_mkt-$tot_trims_cost_actual;
                       if($tot_variance_trims_cost==0)
						{
							$tot_variance_trims_cost_per=0;
						}
						else
						{
							   $tot_variance_trims_cost_per=($tot_variance_trims_cost/$tot_trims_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_trims_cost,2,'.','');
                    ?>
                </td>
                  <td width="100" title="Garment Printing Cost" align="right">
                    <? 
                        $tot_variance_print_amount=$tot_print_amount_mkt-$tot_print_amount_actual;
                       if($tot_variance_print_amount==0)
						{
							$tot_variance_print_per=0;
						}
						else
						{
							   $tot_variance_print_per=($tot_variance_print_amount/$tot_print_amount_mkt)*100; 
						} 
                        echo number_format($tot_variance_print_amount,2,'.','');
                    ?>
                </td>
                 <td width="100" title="Garment Embro  Cost"  align="right">
                    <? 
                       $tot_variance_embro_cost=$tot_embro_amount_mkt-$tot_embro_amount_actual;//tot_wash_amount_mkt-tot_wash_amount_actual
                        if($tot_variance_embro_cost==0)
						{
							$tot_variance_embro_cost_per=0;
						}
						else
						{
							$tot_variance_embro_cost_per=($tot_variance_embro_cost/$tot_embro_amount_mkt)*100; 
						} 
                        echo number_format($tot_variance_embro_cost,2,'.','');
                    ?>
                </td>
				<td width="100" title="Garment Embro  Cost"  align="right">
                    <? 
                       $tot_variance_others_cost=$tot_others_amount_mkt-$tot_others_amount_actual;//tot_wash_amount_mkt-tot_wash_amount_actual
                        if($tot_variance_others_cost==0)
						{
							$tot_variance_others_cost_per=0;
						}
						else
						{
							$tot_variance_others_cost_per=($tot_variance_others_cost/$tot_others_amount_mkt)*100; 
						} 
                        echo number_format($tot_variance_others_cost,2,'.','');
                    ?>
                </td>
                 <td width="100" title="Garment Dyeing Cost"  align="right">
                    <? 
					
                        $tot_variance_dyeing_cost=$tot_dyeing_amount_mkt-$tot_dyeing_amount_actual;
                       if($tot_variance_dyeing_cost==0)
						{
							$tot_variance_dyeing_cost_per=0;
						}
						else
						{
							 $tot_variance_dyeing_cost_per=($tot_variance_dyeing_cost/$tot_dyeing_amount_mkt)*100; 
						} 
						//echo $tot_variance_dyeing_cost.'='.$tot_dyeing_amount_mkt.', ';
                        echo number_format($tot_variance_dyeing_cost,2,'.',''); 
                    ?>
                </td>
                 <td width="100" title="Garment Wash Cost"  align="right">
                    <? 
                       $tot_variance_wash_cost=$tot_wash_amount_mkt-$tot_wash_amount_actual;
                      if($tot_variance_wash_cost==0)
						{
							$tot_variance_wash_cost_per=0;
						}
						else
						{
							 $tot_variance_wash_cost_per=($tot_variance_wash_cost/$tot_wash_amount_mkt)*100; 
						}   
                        echo number_format($tot_variance_wash_cost,2,'.','');
                    ?>
                </td>
               
                <td width="100" align="right">
                    <? 
                        $tot_variance_commission_cost=$tot_commission_cost_mkt-$tot_commission_cost_actual;
                        if($tot_variance_commission_cost==0)
						{
							$tot_variance_commission_cost_per=0;
						}
						else
						{
							 $tot_variance_commission_cost_per=($tot_variance_commission_cost/$tot_commission_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_commission_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_comm_cost=$tot_comm_cost_mkt-$tot_comm_cost_actual;
                       if($tot_variance_comm_cost==0)
						{
							$tot_variance_comm_cost_per=0;
						}
						else
						{
							  $tot_variance_comm_cost_per=($tot_variance_comm_cost/$tot_comm_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_comm_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_freight_cost=$tot_freight_cost_mkt-$tot_freight_cost_actual;
                      if($tot_variance_freight_cost==0)
						{
							$tot_variance_freight_cost_per=0;
						}
						else
						{
							$tot_variance_freight_cost_per=($tot_variance_freight_cost/$tot_freight_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_freight_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_test_cost=$tot_test_cost_mkt-$tot_test_cost_actual;
                        if($tot_variance_test_cost==0)
						{
							$tot_variance_test_cost_per=0;
						}
						else
						{
							 $tot_variance_test_cost_per=($tot_variance_test_cost/$tot_test_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_test_cost,2,'.','');
                    ?>
                </td>
                <td width="100" align="right">
                    <? 
                        $tot_variance_inspection_cost=$tot_inspection_cost_mkt-$tot_inspection_cost_actual;
                       if($tot_variance_inspection_cost==0)
						{
							$tot_variance_inspection_cost_per=0;
						}
						else
						{
							  $tot_variance_inspection_cost_per=($tot_variance_inspection_cost/$tot_inspection_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_inspection_cost,2,'.','');
                    ?>
                </td>
                <td width="100" align="right">
                    <? 
                        $tot_variance_currier_cost=$tot_currier_cost_mkt-$tot_currier_cost_actual;
                       if($tot_variance_currier_cost==0)
						{
							$tot_variance_currier_cost_per=0;
						}
						else
						{
							 $tot_variance_currier_cost_per=($tot_variance_currier_cost/$tot_currier_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_currier_cost,2,'.','');
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_cm_cost=$tot_cm_cost_mkt-$tot_cm_cost_actual;
                        if($tot_variance_cm_cost==0)
						{
							$tot_variance_cm_cost_per=0;
						}
						else
						{
							$tot_variance_cm_cost_per=($tot_variance_cm_cost/$tot_cm_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_cm_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100" align="right">
                    <? 
                        $tot_variance_all_cost=$tot_mkt_all_cost-$tot_actual_all_cost;
                      if($tot_variance_all_cost==0)
						{
							$tot_variance_all_cost_per=0;
						}
						else
						{
							$tot_variance_all_cost_per=($tot_variance_all_cost/$tot_mkt_all_cost)*100; 
						} 
                        echo number_format($variance_all_cost,2,'.','');
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_margin_cost=$tot_mkt_margin-$tot_actual_margin;
                        $tot_variance_margin_cost_per=($tot_variance_margin_cost/$tot_mkt_margin)*100; 
                        echo number_format($tot_variance_margin_cost,2,'.','');
                    ?>
                </td>
                <td width="100" align="right">
                    <? 
                        $tot_variance_per_cost=$tot_mkt_margin_perc-$tot_actual_margin_perc;
                        $tot_variance_per_cost_per=($tot_variance_per_cost/$tot_mkt_margin_perc)*100; 
                        echo number_format($tot_variance_per_cost,2,'.','');
                    ?>
                </td>
                 <td width="100"  align="right">
                    <? 
                       /* $tot_variance_margin_cost=$tot_mkt_margin-$tot_actual_margin;
                        $tot_variance_margin_cost_per=($tot_variance_margin_cost/$tot_mkt_margin)*100; 
                        echo number_format($tot_variance_margin_cost,2,'.','');*/
                    ?>
                </td>
                 <td width=""  align="right">
                    <? 
                       /* $tot_variance_margin_cost=$tot_mkt_margin-$tot_actual_margin;
                        $tot_variance_margin_cost_per=($tot_variance_margin_cost/$tot_mkt_margin)*100; 
                        echo number_format($tot_variance_margin_cost,2,'.','');*/
                    ?>
                </td>
            </tr>
            <tr bgcolor="#CCCCEE" onClick="change_color('tr_vpt','#CCCCEE')" id="tr_vpt" style="font-weight:bold;">
                <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="120" align=""></td>
                <td width="90"  align="right">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110" align="right">&nbsp;</td>
                <td width="100"  align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Variance (%) Total</td>
                <td width="100" align="right" ><? echo number_format($tot_variance_qnty_per,0,'.',''); ?></td>
                <td width="110" align="right" ><? echo number_format($tot_variance_po_value_per,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_variance_yarn_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_yarn_value_addition_cost_per,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_variance_knit_cost_per,2,'.',''); ?></td>
                <td width="100" title="Grey Fabric Transfer Cost" align="right"><? //echo number_format($tot_variance_knit_cost_per,2,'.',''); ?></td>
                
               <td width="100" title="Grey Fabric Cost" align="right"><? echo number_format($tot_variance_grey_fab_cost_per,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_variance_dye_finish_cost_per ,2,'.',''); ?></td>
               
                <td width="110"  align="right"><? echo number_format($tot_variance_aop_cost_per,2,'.',''); ?></td>
                 <td width="100" title="Finished Fabric Transfer Cost"  align="right"><? //echo number_format($tot_variance_aop_cost_per,2,'.',''); ?></td>
                 <td width="100"  title="Finished Fabric Purchase Cost"  align="right"><? echo number_format($tot_variance_fin_fabric_purchase_cost_per,2,'.',''); ?></td>
                 <td width="110" align="right"><? echo number_format($tot_variance_fabric_purchase_cost_per ,2,'.',''); ?></td>
                 <td width="100"  title="Finished Fabric Cost"  align="right"><? echo number_format($tot_variance_fin_fab_cost_per,2,'.',''); ?></td>
                  <td width="100"  title="Total Fabric Cost"  align="right"><? echo number_format($tot_variance_fabric_cost_mkt_per,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_variance_trims_cost_per,2,'.',''); ?></td>
                 <td width="100"  title="Garment Printing Cost" align="right"><? echo number_format($tot_variance_print_per,2,'.',''); ?></td>
                 <td width="100"  title="Garment Embro Cost" align="right"><? echo number_format($tot_variance_embro_cost_per,2,'.',''); ?></td>
				 <td width="100"  title="Garment OthersCost" align="right"><? echo number_format($tot_variance_others_cost_per,2,'.',''); ?></td>
                 <td width="100"  title="Garment Dyeing Cost"  align="right"><? echo number_format($tot_variance_dyeing_cost_per,2,'.',''); ?></td>
                 <td width="100"  title="Garment Wash Cost" align="right"><? echo number_format($tot_variance_wash_cost_per,2,'.',''); ?></td>
                  
               
                <td width="100"  align="right"><? echo number_format($tot_variance_commission_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_comm_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_freight_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_test_cost_per,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_variance_inspection_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_currier_cost_per,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_variance_cm_cost_per,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_variance_all_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_margin_cost_per,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_variance_per_cost_per,2,'.',''); ?></td>
                 <td width="100" align="right"><? //echo number_format($tot_variance_margin_cost_per,2,'.',''); ?></td>
                  <td width="" align="right"><? //echo number_format($tot_variance_margin_cost_per,2,'.',''); ?></td>
            </tr>
        </table>
        <br />
        <table width="4330">
            <tr>
                <td width="850" valign="top">
                    <div align="center" style="width:450px" id="div_summary"><input type="button" value="Print Preview" class="formbutton" onClick="new_window2('company_div','summary_full')" /></div>
                    <br /><? $po_ids=implode(',',$po_ids_array);$job_ids=rtrim($job_ids,','); $job_nos=implode(',',array_unique(explode(",",$job_ids))); ?>
                    <div id="summary_full"> <font color="#FF0000" style="display:none">*Yarn Dyeing Charge included with actual Yarn Cost</font>
                        <div align="center" id="company_div" style="visibility:hidden; font-size:24px;width:700px"><b><? echo $company_arr[$company_name].'<br>'; 
						echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to));
						?></b></div>
                        <table width="850" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                            <tr>
                                <th colspan="9">Summary</th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="180">Particulars</th>
                                <th width="130">Pre Costing</th>
                                <th width="80">%</th>
                                <th width="130">At Actual</th>
                                <th width="80">%</th>
                                <th width="80">Variance</th>
                                <th width="80">%</th>
                                <th>Disclosure</th>
                            </tr> 
							<?
								$bgcolor1='#E9F3FF';
								$bgcolor2='#FFFFFF';
							?>
                            </thead>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_1','<? echo $bgcolor1; ?>')" id="trtd_1">
                                <td align="center">1</td>
                                <td>PO/Shipment Value</td>
                                <td align="right"><? echo number_format($tot_po_value,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                                <td align="right"  title="Variance/PO-Shipment Value Mkt*100"><? echo number_format((($tot_ex_factory_val-$tot_po_value)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_2','<? echo $bgcolor2; ?>')" id="trtd_2">
                                <td colspan="9"><b>Cost</b></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_3','<? echo $bgcolor1; ?>')" id="trtd_3">
                                <td align="center">2</td>
                                <td>Grey Yarn Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $po_ids."**".$job_nos; ?>','mkt_yarn_cost','Grey Yarn Mkt. Cost Details')"><? echo number_format($tot_yarn_cost_mkt,2); //$tot_yarn_qty_actual?></a></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','yarn_cost_actual','Yarn Cost Details')"><? echo number_format($tot_yarn_cost_actual,2); //$tot_yarn_qty_actual;?></a></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_yarn_cost_mkt-$tot_yarn_cost_actual,2);//$tot_yarn_amt_actual ?></td>
                                <td align="right"  title="Variance/Grey Yarn Cost Mkt*100"><? echo number_format((($tot_yarn_cost_mkt-$tot_yarn_cost_actual)/$tot_yarn_cost_mkt)*100,2); ?></td>
                                 <td align="right"><?  if($tot_yarn_qty_actual>$tot_yarn_cost_mkt) echo "Returnable Yarn Qty..".$tot_yarn_qty_actual."And Cost..".$tot_yarn_amt_actual; else echo " ";?></td>
                            </tr>
                             <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_4','<? echo $bgcolor2; ?>')" id="trtd_4">
                                <td align="center">3</td>
                                <td>Yarn  Value Addition Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $po_ids."**".$job_nos; ?>','mkt_yarn_dyeing_twisting_cost','Yarn Dyeing Cost Details')"><? echo number_format($tot_yarn_value_addition_cost_mkt,2); ?></a><? //echo number_format($tot_yarn_value_addition_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_yarn_value_addition_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','yarn_dye_twist_cost_actual','Yarn Dyeing & Twist Cost Details','2','900px')"><? echo number_format($tot_yarn_dyeing_twist_actual,2); ?></a></td>
                                <td align="right"><? echo number_format(($tot_yarn_value_addition_cost_mkt/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_yarn_value_addition_cost_mkt-$tot_yarn_dyeing_twist_actual,2); ?></td>
                                 <td align="right" title="Variance/Yarn Value  Addition Cost Mkt*100"><? echo number_format((($tot_yarn_value_addition_cost_mkt-$tot_yarn_dyeing_twist_actual)/$tot_yarn_value_addition_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_5','<? echo $bgcolor2; ?>')" id="trtd_5">
                                <td align="center">4</td>
                                <td>Knitting Cost</td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','knit_cost_actual','Knitting Cost Details')"><? echo number_format($tot_knit_cost_actual,2); ?></a><? //echo number_format($tot_knit_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Knitting Cost Mkt*100"><? echo number_format((($tot_knit_cost_mkt-$tot_knit_cost_actual)/$tot_knit_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_6','<? echo $bgcolor2; ?>')" id="trtd_6">
                                <td align="center">5</td>
                                <td>Grey Fabric Transfer Cost</td>
                                <td align="right"><? //echo number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? //echo number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','grey_fabric_transfer_cost_actual','grey fabric transfer cost')"><? echo number_format($tot_grey_fab_transfer_amt_actual,2); ?></a><?//echo number_format($tot_grey_fab_transfer_amt_actual,2); ?></td>
                                <td align="right"><? //echo number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                                 <td align="right"><? echo "N/A"; ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                             <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_7','<? echo $bgcolor2; ?>')" id="trtd_7">
                                <td align="center">6</td>
                                <td>Grey Fabric Cost =2+3+4+5</td>
                                <td align="right"><? 
								$grey_fab_cost_mkt_sum=$tot_yarn_cost_mkt+$tot_yarn_value_addition_cost_mkt+$tot_knit_cost_mkt;
								$grey_fab_cost_act_sum=$tot_yarn_cost_actual+$tot_yarn_dyeing_twist_actual+$tot_knit_cost_actual+$tot_grey_fab_transfer_amt_actual;
								
								echo number_format($grey_fab_cost_mkt_sum,2); ?></td>
                                <td align="right"><? echo number_format(($grey_fab_cost_mkt_sum/$tot_po_value)*100,2); ?></td>
                                <td align="right" title=""><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','knitting_cost','Knitting Cost Details')"><? //echo number_format($tot_grey_fab_cost_actual,2); ?></a><? echo number_format($grey_fab_cost_act_sum,2); ?></td>
                                <td align="right"><? echo number_format(($grey_fab_cost_act_sum/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($grey_fab_cost_mkt_sum-$grey_fab_cost_act_sum,2); ?></td>
                                 <td align="right" title="Variance/Grey Fab Cost Mkt*100"><? echo number_format((($grey_fab_cost_mkt_sum-$grey_fab_cost_act_sum)/$grey_fab_cost_mkt_sum)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_8','<? echo $bgcolor2; ?>')" id="trtd_8">
                                <td align="center">7</td>
                                <td>Dye & Fin Cost</td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $po_ids; ?>','dye_finish_cost_actual','Actual Dyeing & Finish Cost Details','900px')"><? echo number_format($tot_dye_finish_cost_actual,2,'.',''); ?></a></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Dye & Fin Cost Mkt*100"><?  echo number_format((($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual)/$tot_dye_finish_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_9','<? echo $bgcolor1; ?>')" id="trtd_9">
                                <td align="center">8</td>
                                <td>AOP  Cost</td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/AOP Cost Mkt*100"><? echo number_format((($tot_aop_cost_mkt-$tot_aop_cost_actual)/$tot_aop_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_10','<? echo $bgcolor2; ?>')" id="trtd_10">
                                <td align="center">9</td>
                                <td>Finished Fabric Transfer Cost</td>
                                <td align="right"><? //echo number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? // echo number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_fin_fab_transfer_amt_actual,2); ?></td>
                                <td align="right"><? //echo number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                                 <td align="right"><? echo "N/A"; ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_11','<? echo $bgcolor1; ?>')" id="trtd_11">
                                <td align="center">10</td>
                                <td>Finished Fabric Cost =6+7+8+9</td>
                                <td align="right"><? $tot_fin_fab_cost_sum_mkt=$tot_yarn_cost_mkt+$tot_yarn_value_addition_cost_mkt+$tot_knit_cost_mkt+$tot_dye_finish_cost_mkt+$tot_aop_cost_mkt;
								//$tot_fin_fabCost=$tot_yarn_cost_mkt+$tot_yarn_value_addition_cost_mkt+$tot_knit_cost_mkt+$tot_dye_finish_cost_mkt+$tot_aop_cost_mkt;
								echo number_format($tot_fin_fab_cost_sum_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fin_fab_cost_sum_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? $tot_fin_fab_cost_sum_actual=$tot_grey_fab_cost_actual+$tot_dye_finish_cost_actual+$tot_aop_cost_actual+$tot_fin_fab_transfer_amt_actual;
								echo number_format($tot_grey_fab_cost_actual+$tot_dye_finish_cost_actual+$tot_aop_cost_actual+$tot_fin_fab_transfer_amt_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fin_fab_cost_sum_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_fin_fab_cost_sum_mkt-$tot_fin_fab_cost_sum_actual,2); ?></td>
                                 <td align="right" title="Variance/Finished Fabric Cost Mkt*100"><? echo number_format((($tot_fin_fab_cost_sum_mkt-$tot_fin_fab_cost_sum_actual)/$tot_fin_fab_cost_sum_mkt)*100,2);  ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_12','<? echo $bgcolor2; ?>')" id="trtd_12">
                                <td align="center">11</td>
                                <td>Fabric Purchase Cost</td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fabric_purchase_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','fabric_purchase_cost','Fabric Purchase Cost Details')"><? echo number_format($tot_fabric_purchase_cost_actual,2); ?></a></td>
                                <td align="right"><? echo number_format(($tot_fabric_purchase_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Fabric Purchase Cost Mkt*100"><? echo number_format((($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual)/$tot_fabric_purchase_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                             <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_27','<? echo $bgcolor2; ?>')" id="trtd_27">
                                <td align="center">12</td>
                                <td>Total Fabric Cost(=10+11)</td>
                                <td align="right"><? $tot_fab_cost_mkt=$tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt;
								echo number_format($tot_fab_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format((($tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? 
								$tot_fabric_cost_actual=$tot_grey_fab_cost_actual+$tot_dye_finish_cost_actual+$tot_aop_cost_actual+$tot_fin_fab_transfer_amt_actual+$tot_fabric_purchase_cost_actual;
								echo number_format($tot_grey_fab_cost_actual+$tot_dye_finish_cost_actual+$tot_aop_cost_actual+$tot_fin_fab_transfer_amt_actual+$tot_fabric_purchase_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fabric_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? $tot_fab_cost_vairance=($tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt)-$tot_fabric_cost_actual;
								echo number_format(($tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt)-$tot_fabric_cost_actual,2); ?></td>
                                 <td align="right"><?  echo number_format(($tot_fab_cost_vairance/$tot_fab_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? echo 'N/A'; ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_13','<? echo $bgcolor1; ?>')" id="trtd_13">
                                <td align="center">13</td>
                                <td>Trims Cost</td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_trims_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt-$tot_trims_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Trim Cost Mkt*100"><? echo number_format((($tot_trims_cost_mkt-$tot_trims_cost_actual)/$tot_trims_cost_mkt)*100,2);  ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_14','<? echo $bgcolor2; ?>')" id="trtd_14">
                                <td align="center">14</td>
                                <td>Printing Cost</td>
                                <td align="right"><? echo number_format($tot_print_amount_mkt,2); ?></td>
                                <td align="right"><?  echo number_format(($tot_print_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_print_amount_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_print_amount_mkt-$tot_print_amount_actual,2); ?></td>
                                <td align="right" title="Variance/Printing Cost Mkt*100"><?  echo number_format((($tot_print_amount_mkt-$tot_print_amount_actual)/$tot_print_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">15</td>
                                <td> Embroidery Cost</td>
                                <td align="right"><? echo number_format($tot_embro_amount_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_embro_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_embro_amount_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_embro_amount_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_embro_amount_mkt-$tot_embro_amount_actual,2); ?></td>
                                 <td align="right" title="Variance/Embroidery Cost Mkt*100"><?  echo number_format((($tot_embro_amount_mkt-$tot_embro_amount_actual)/$tot_embro_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
							<tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">16</td>
                                <td> Others Cost</td>
                                <td align="right"><? echo number_format($tot_others_amount_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_others_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_others_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_others_amount_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_others_amount_mkt-$tot_others_amount_actual,2); ?></td>
                                <td align="right" title="Variance/Embroidery Cost Mkt*100"><?  echo number_format((($tot_others_amount_mkt-$tot_others_amount_actual)/$tot_others_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_16','<? echo $bgcolor2; ?>')" id="trtd_16">
                                <td align="center">17</td>
                                <td>Gmt Dyeing  Cost</td>
                                <td align="right"><? echo number_format($tot_dyeing_amount_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_dyeing_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_dyeing_amount_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_dyeing_amount_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_dyeing_amount_mkt-$tot_dyeing_amount_actual,2); ?></td>
                                 <td align="right" title="Variance/Gmt Dyeing Cost Mkt*100"><?  echo number_format((($tot_dyeing_amount_mkt-$tot_dyeing_amount_actual)/$tot_dyeing_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">18</td>
                                <td>Washing  Cost</td>
                                <td align="right"><? echo number_format($tot_wash_amount_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_wash_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_wash_amount_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_wash_amount_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_wash_amount_mkt-$tot_wash_amount_actual,2); ?></td>
                                <td align="right" title="Variance/Washing Cost Mkt*100"><?  echo number_format((($tot_wash_amount_mkt-$tot_wash_amount_actual)/$tot_wash_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_18','<? echo $bgcolor2; ?>')" id="trtd_18">
                                <td align="center">19</td>
                                <td>Commission Cost</td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_commission_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_commission_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt-$tot_commission_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Commission Cost Mkt*100"><?  echo number_format((($tot_commission_cost_mkt-$tot_commission_cost_actual)/$tot_commission_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_19','<? echo $bgcolor1; ?>')" id="trtd_19">
                                <td align="center">20</td>
                                <td>Commercial Cost</td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_comm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_comm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt-$tot_comm_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Commercial Cost Mkt*100"><?  echo number_format((($tot_comm_cost_mkt-$tot_comm_cost_actual)/$tot_comm_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_20','<? echo $bgcolor2; ?>')" id="trtd_20">
                                <td align="center">21</td>
                                <td>Freight Cost</td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_freight_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_freight_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt-$tot_freight_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Freight Cost Mkt*100"><?  echo number_format((($tot_freight_cost_mkt-$tot_freight_cost_actual)/$tot_freight_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_21','<? echo $bgcolor1; ?>')" id="trtd_21">
                                <td align="center">22</td>
                                <td>Testing Cost</td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_test_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_test_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt-$tot_test_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Testing Cost Mkt*100"><?  echo number_format((($tot_test_cost_mkt-$tot_test_cost_actual)/$tot_test_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_22','<? echo $bgcolor2; ?>')" id="trtd_22">
                                <td align="center">23</td>
                                <td>Inspection Cost</td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_inspection_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_inspection_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt-$tot_inspection_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Inspection Cost Mkt*100"><?  echo number_format((($tot_inspection_cost_mkt-$tot_inspection_cost_actual)/$tot_inspection_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_23','<? echo $bgcolor1; ?>')" id="trtd_23">
                                <td align="center">24</td>
                                <td>Courier Cost</td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_currier_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_currier_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt-$tot_currier_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Courier Cost Mkt*100"><?  echo number_format((($tot_currier_cost_mkt-$tot_currier_cost_actual)/$tot_currier_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_24','<? echo $bgcolor2; ?>')" id="trtd_24">
                                <td align="center">25</td>
                                <td>CM</td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_cm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_cm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt-$tot_cm_cost_actual,2); ?></td>
                                <td align="right" title="Variance/CM Cost Mkt*100"><?  echo number_format((($tot_cm_cost_mkt-$tot_cm_cost_actual)/$tot_cm_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
							<tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_25','<? echo $bgcolor1; ?>')" id="trtd_25">
                                <td align="center">26</td><? $aop_trims_yd_cost=$tot_aop_cost_mkt+$tot_trims_cost_mkt+$yarn_dyeing_cost_mkt;?>
                                <td>AOP+Trims+Y/D Cost</td>
                                <td align="right" title="total=<?=$aop_trims_yd_cost;?>"><? echo number_format(($aop_trims_yd_cost*10)/100,2); ?></td>
                                <td align="right"><?=10; //echo number_format(($aop_trims_yd_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? // echo number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><? //echo number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                                <td align="right" title="Variance/Total Cost Mkt*100"><? // echo number_format((($tot_mkt_all_cost-$tot_actual_all_cost)/$tot_mkt_all_cost)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_25','<? echo $bgcolor1; ?>')" id="trtd_25">
                                <td align="center">27</td>
                                <td>Total Cost</td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_all_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><? echo number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                                <td align="right" title="Variance/Total Cost Mkt*100"><?  echo number_format((($tot_mkt_all_cost-$tot_actual_all_cost)/$tot_mkt_all_cost)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_26','<? echo $bgcolor2; ?>')" id="trtd_26">
                                <td align="center">28</td>
                                <td>Margin/Loss</td>
                                <td align="right"><? echo number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_margin,2); ?></td>
                                <td align="right"><? echo number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                                <td align="right" title="Variance/Margin/Loss Mkt*100"><?  echo number_format((($tot_mkt_margin-$tot_actual_margin)/$tot_mkt_margin)*100,2); ?></td>
                                 <td align="right"><? echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_28','<? echo $bgcolor2; ?>')" id="trtd_28">
                                <td align="center">29</td>
                                <td>CPA/Short Fabric Cost</td>
                                <td align="right"><? //echo number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? //echo number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_yarn_qty_cpa_actual,2); ?></td>
                                <td align="right"><? //echo number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                                <td align="right"><?  echo 'N/A'; ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_29','<? echo $bgcolor2; ?>')" id="trtd_29">
                                <td align="center">30</td>
                                <td>Net Margin/Loss(=28-29)</td>
                                <td align="right"><? //echo number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? //echo number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_margin-$tot_yarn_qty_cpa_actual,2); ?></td>
                                <td align="right"><? //echo number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                                 <td align="right"><?  echo 'N/A';  ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td width="525" align="center" valign="top" style="display:none">
                    <div align="center" style="width:500px; height:53px" id="graph">&nbsp;</div>
                    <fieldset style="text-align:center; width:450px" > 
                    	<legend>Chart</legend>
                    </fieldset>
                </td>   
                <td width="" valign="top">
                    <div align="center" style="width:600px" id="div_buyer"><input type="button" value="Print Preview" class="formbutton" onClick="new_window2('company_div_b','summary_buyer')" /></div>
                    <br />
                    <div id="summary_buyer">
                        <div align="center" id="company_div_b" style="visibility:hidden; font-size:24px;width:1505px"><b><? echo $company_arr[$company_name].'<br>';echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></b></div>
                        <table width="100%" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                 <tr>
                                    <th colspan="31">Buyer Level Summary</th>
                                </tr>
                                <tr>
                                    <th width="35">SL</th>
                                    <th width="70">Buyer name</th>
                                    <th width="110">Cost Source</th>
                                    <th width="110">Ex-Factory Value</th>
                                   
                                    <th width="100">Yarn Value Addition Cost</th>
                                    <th width="110">Grey Yarn cost</th>
                                    <th width="110">Knitting Cost</th>
                                    <th width="100">Grey Fabric Transfer Cost</th>
                                   
                                    <th width="110">Grey Fabric Purchase Cost</th>
                                    <th width="100">Grey Fabric Cost</th>
                                    <th width="110">Dye & Fin Cost</th>
                                    <th width="110">AOP</th>
                                    <th width="100">Fin. Fab. Transfer Cost</th>
                                    <th width="100">Fin. Fabric Purchase Cost</th>
                                    <th width="100">Fin. Fabric Cost</th>
                                    <th width="110">Trims Cost</th>
                                    <th width="100">Printing Cost</th>
                                    <th width="100">Embroidery Cost</th>
									<th width="100">Others Cost</th>
                                    <th width="100">Dyeing  Cost</th>
                                   
                                    <th width="100">Washing  Cost</th>
                                        
                                   
                                    <th width="110">Commission Cost</th>
                                    <th width="100">Commercial Cost</th>
                                    <th width="110">Freight Cost</th>
                                    <th width="100">Testing Cost</th>
                                    <th width="100">Inspection Cost</th>
                                    <th width="100">Courier Cost</th>
                                    <th width="110">CM Cost</th>
                                    <th width="110">Total Cost</th>
                                    <th width="110">Margin</th>
                                    <th width="90">Margin %</th>
                                </tr>
                            </thead>
                            <?
                                $j=1;$tot_fin_fab_transfer_amt_actual=$tot_mkt_print_cost=$tot_mkt_embro_cost=$tot_mkt_dyeing_cost=$tot_mkt_wash_cost=$tot_mkt_value_addition_cost=0;
                                foreach($buyer_name_array as $key=>$value)
                                {
                                    if ($j%2==0)  
                                        $bgcolor="#E9F3FF";
                                    else
                                        $bgcolor="#FFFFFF";	
                                ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD_<? echo $j; ?>">
                                        <td rowspan="4"><? echo $j ;?></td>
                                        <td rowspan="4"><? echo $value ;?></td>
                                        <td><b>Pre Costing</b></td>
                                        <td align="right"><? echo number_format($mkt_po_val_array[$key],2); $tot_mkt_po_val+=$mkt_po_val_array[$key];  ?></td>
                                      
                                        <td align="right"><? echo number_format($mkt_yarn_value_addition_array[$key],2); $tot_mkt_value_addition_cost+=$mkt_yarn_value_addition_array[$key]; ?></td>
                                        
                                        <td align="right"><? echo number_format($mkt_yarn_array[$key],2); $tot_mkt_yarn_cost+=$mkt_yarn_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_knit_array[$key],2); $tot_mkt_knit_cost+=$mkt_knit_array[$key]; ?></td>
                                         <td align="right"><? //echo number_format($mkt_yarn_array[$key],2); $tot_mkt_yarn_cost+=$mkt_yarn_array[$key]; ?></td>
                                       
                                        <td align="right"><? echo number_format($mkt_fabric_purchase_array[$key],2); $tot_mkt_fin_pur_cost+=$mkt_fabric_purchase_array[$key];?></td>
                                         <td align="right"><? echo number_format($mkt_grey_fab_cost_array[$key],2); $tot_mkt_grey_fab_cost+=$mkt_grey_fab_cost_array[$key];?></td>
                                          <td align="right"><? echo number_format($mkt_dy_fin_array[$key],2); $tot_mkt_dy_fin_cost+=$mkt_dy_fin_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_aop_array[$key],2); $tot_mkt_aop_cost+=$mkt_aop_array[$key]; ?></td>
                                        <td align="right"><? //echo number_format($mkt_fin_fabric_purchase_array[$key],2);$tot_mkt_fin_fabric_purchase+=$mkt_fin_fabric_purchase_array[$key]; ?></td>
                                        <td align="right"><?  echo number_format($mkt_fin_fabric_purchase_array[$key],2);$tot_mkt_fin_fabric_purchase+=$mkt_fin_fabric_purchase_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_fin_fabric_purchase_array[$key],2); ?></td>
                                        <td align="right"><? echo number_format($mkt_trims_array[$key],2); $tot_mkt_trims_cost+=$mkt_trims_array[$key]; ?></td>
                                         
                                         <td align="right"><? echo number_format($mkt_print_array[$key],2); $tot_mkt_print_cost+=$mkt_print_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($mkt_embro_array[$key],2); $tot_mkt_embro_cost+=$mkt_embro_array[$key]; ?></td>
										 <td align="right"><? echo number_format($mkt_others_array[$key],2); $tot_mkt_others_cost+=$mkt_others_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($mkt_dyeing_array[$key],2); $tot_mkt_dyeing_cost+=$mkt_dyeing_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($mkt_wash_array[$key],2); $tot_mkt_wash_cost+=$mkt_wash_array[$key]; ?></td>
                                                
                                       
                                        <td align="right"><? echo number_format($mkt_commn_array[$key],2); $tot_mkt_commn_cost+=$mkt_commn_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_commercial_array[$key],2); $tot_mkt_commercial_cost+=$mkt_commercial_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_freight_array[$key],2); $tot_mkt_freight_cost+=$mkt_freight_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_test_array[$key],2); $tot_mkt_test_cost+=$mkt_test_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_ins_array[$key],2); $tot_mkt_ins_cost+=$mkt_ins_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_courier_array[$key],2); $tot_mkt_courier_cost+=$mkt_courier_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_cm_array[$key],2); $tot_mkt_cm_cost+=$mkt_cm_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_total_array[$key],2); $tot_mkt_total_cost+=$mkt_total_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_margin_array[$key],2); $tot_mkt_margin_cost+=$mkt_margin_array[$key]; ?></td>
                                        <td align="right"><? $mkt_margin_perc=($mkt_margin_array[$key]/$mkt_po_val_array[$key])*100; echo number_format($mkt_margin_perc,2); $tot_mkt_margin_perc_cost+=$mkt_margin_perc; ?></td> 
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD2_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD2_<? echo $j; ?>">
                                        <td><b>Actual</b></td>
                                        <td align="right"><? echo number_format($ex_factory_val_array[$key],2); $tot_buyer_ex_factory_val+=$ex_factory_val_array[$key];  ?></td>
                                        
                                        <td align="right"><? echo number_format($yarn_dyeing_twist_array[$key],2); $tot_yarn_dyeing_twist_cost+=$yarn_dyeing_twist_array[$key]; ?></td>
                                        
                                        <td align="right"><? echo number_format($yarn_cost_array[$key],2); $tot_buyer_yarn_cost+=$yarn_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($knit_cost_array[$key],2); $tot_buyer_knit_cost+=$knit_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($grey_fab_cost_array[$key],2); $tot_grey_fab_cost+=$grey_fab_cost_array[$key]; ?></td>
                                        
                                         <td align="right"><? echo number_format($actual_fabric_purchase_array[$key],2); $tot_buyer_fin_pur_act_cost+=$actual_fabric_purchase_array[$key]; ?></td>
                                       <td align="right" title="Grey Feb Cost"><? echo number_format($grey_fab_cost_array[$key],2); $tot_grey_fab_cost_act+=$grey_fab_cost_array[$key];?></td>
                                       <td align="right"><? echo number_format($dye_cost_array[$key],2); $tot_buyer_dye_cost+=$dye_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($aop_n_others_cost_array[$key],2); $tot_buyer_aop_n_others_cost+=$aop_n_others_cost_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($fin_fab_transfer_amt_actual_array[$key],2); $tot_fin_fab_transfer_amt_actual+=$fin_fab_transfer_amt_actual_array[$key]; ?></td>
                                          <td align="right"><? echo number_format($actual_fabric_purchase_array[$key],2); $tot_act_fin_fabric_purchase_cost+=$actual_fabric_purchase_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($finished_fab_cost_actual_array[$key],2); $tot_finished_fab_cost_actual_buy+=$finished_fab_cost_actual_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($trims_cost_array[$key],2); $tot_buyer_trims_cost+=$trims_cost_array[$key]; ?></td>
                                          <td align="right"><? echo number_format($print_act_cost_array[$key],2); $tot_act_print_cost+=$print_act_cost_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($embro_act_cost_array[$key],2); $tot_act_embro_cost+=$embro_act_cost_array[$key]; ?></td>
										 <td align="right"><? echo number_format($others_act_cost_array[$key],2); $tot_act_others_cost+=$others_act_cost_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($dyeing_act_cost_array[$key],2); $tot_act_dyeing_cost+=$dyeing_act_cost_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($wash_act_cost_array[$key],2); $tot_act_wash_cost+=$wash_act_cost_array[$key]; ?></td>
                                         
                                      
                                        <td align="right"><? echo number_format($commission_cost_array[$key],2); $tot_buyer_commi_cost+=$commission_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($commercial_cost_array[$key],2); $tot_buyer_commercial_cost+=$commercial_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($freight_cost_array[$key],2); $tot_buyer_freight_cost+=$freight_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($testing_cost_array[$key],2); $tot_buyer_testing_cost+=$testing_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($inspection_cost_array[$key],2); $tot_buyer_inspection_cost+=$inspection_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($courier_cost_array[$key],2); $tot_buyer_courier_cost+=$courier_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($cm_cost_array[$key],2); $tot_buyer_cm_cost+=$cm_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($total_cost_array[$key],2); $tot_buyer_total_cost+=$total_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($margin_array[$key],2); $tot_buyer_margin_cost+=$margin_array[$key]; ?></td>
                                        <td align="right"><? $margin_perc=($margin_array[$key]/$ex_factory_val_array[$key])*100; echo number_format($margin_perc,2); $tot_buyer_margin_perc_cost+=$margin_perc; ?></td> 
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD3_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD3_<? echo $j; ?>">
                                        <td><b>Variance</b></td>
                                        <td align="right">
                                        <?
                                            $ex_var= $mkt_po_val_array[$key]-$ex_factory_val_array[$key];
                                            echo number_format($ex_var,2); 
                                        ?>
                                        </td>
                                         
                                         <td align="right">
                                        <?
                                           $yarn_value_addition_var= $mkt_yarn_value_addition_array[$key]-$yarn_dyeing_twist_array[$key];
                                            echo number_format($yarn_value_addition_var,2);
                                        ?>
                                        </td>
                                        
                                        <td align="right">
                                        <?
                                            $yarn_var= $mkt_yarn_array[$key]-$yarn_cost_array[$key];
                                            echo number_format($yarn_var,2);
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $knit_var= $mkt_knit_array[$key]-$knit_cost_array[$key];
                                            echo number_format($knit_var,2); 
                                        ?>
                                        </td>
                                         <td align="right">
                                        <?
                                           // $yarn_var= $mkt_yarn_array[$key]-$yarn_cost_array[$key];
                                           // echo number_format($yarn_var,2);
                                        ?>
                                        </td>
                                       
                                        <td align="right">
                                        <?
                                            $fin_pur_var= $mkt_fabric_purchase_array[$key]-$actual_fabric_purchase_array[$key];
                                            echo number_format($fin_pur_var,2); 
                                        ?>
                                        </td>
                                       <td align="right" title="grey Feb Cost"><? 
									   $grey_feb_var=$mkt_grey_fab_cost_array[$key]-$grey_fab_cost_array[$key];echo number_format($grey_feb_var,2);?></td>
                                        <td align="right">
                                        <?
                                            $dy_var= $mkt_dy_fin_array[$key]-$dye_cost_array[$key];
                                            echo number_format($dy_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $aop_var= $mkt_aop_array[$key]-$aop_n_others_cost_array[$key];
                                            echo number_format($aop_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                           // $fin_fab_pur_var=$mkt_fin_fabric_purchase_array[$key]-$fin_fab_transfer_amt_actual_array[$key];
                                            //echo number_format($fin_fab_pur_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $fin_feb_pur_var=$mkt_fin_fabric_purchase_array[$key]-$tot_act_fin_fabric_purchase_cost[$key];
                                           echo number_format($fin_feb_pur_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                       
                                        <?
                                            $totfin_fab_var= $mkt_fin_fabric_purchase_array[$key]-$finished_fab_cost_actual_array[$key];
                                            echo number_format($totfin_fab_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $trims_var= $mkt_trims_array[$key]-$trims_cost_array[$key];
                                            echo number_format($trims_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $print_var= $mkt_print_array[$key]-$print_act_cost_array[$key];
                                            echo number_format($print_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                           $embro_var= $mkt_embro_array[$key]-$embro_act_cost_array[$key];
										  
                                           echo number_format($embro_var,2); 
                                        ?>
                                        </td>
										<td align="right">
                                        <?
                                           $others_var= $mkt_others_array[$key]-$others_act_cost_array[$key];
										  
                                           echo number_format($others_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                           $dyeing_var= $mkt_dyeing_array[$key]-$dyeing_act_cost_array[$key];
                                            echo number_format($dyeing_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $wash_var= $mkt_wash_array[$key]-$wash_act_cost_array[$key];
                                            echo number_format($wash_var,2); 
                                        ?>
                                        </td>
                                        
                                       
                                        <td align="right">
                                        <?
                                            $com_var= $mkt_commn_array[$key]-$commission_cost_array[$key];
                                            echo number_format($com_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $commer_var= $mkt_commercial_array[$key]-$commercial_cost_array[$key];
                                            echo number_format($commer_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $fr_var= $mkt_freight_array[$key]-$freight_cost_array[$key];
                                            echo number_format($fr_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $test_var= $mkt_test_array[$key]-$testing_cost_array[$key];
                                            echo number_format($test_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $ins_var= $mkt_ins_array[$key]-$inspection_cost_array[$key];
                                            echo number_format($ins_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $cour_var= $mkt_courier_array[$key]-$courier_cost_array[$key];
                                            echo number_format($cour_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $cm_var= $mkt_cm_array[$key]-$cm_cost_array[$key];
                                            echo number_format($cm_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $tot_var= $mkt_total_array[$key]-$total_cost_array[$key];
                                            echo number_format($tot_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $mar_var= $mkt_margin_array[$key]-$margin_array[$key];
                                            echo number_format($mar_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $margin_perc_to= $mkt_margin_perc-$margin_perc;
                                            echo number_format($margin_perc_to,2); 
                                        ?>
                                        </td>
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD4_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD4_<? echo $j; ?>">
                                        <td><b>Variance (%)</b></td>
                                        <td align="right"><? echo number_format(($ex_var/$mkt_po_val_array[$key]*100),2); ?></td>
                                      
                                         <td align="right"><? echo number_format(($yarn_value_addition_var/$mkt_yarn_value_addition_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($yarn_var/$mkt_yarn_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($knit_var/$mkt_knit_array[$key]*100),2); ?></td>
                                        <td align="right"><? //echo number_format(($ex_var/$mkt_po_val_array[$key]*100),2); ?></td>
                                      
                                        <td align="right"><? echo number_format(($fin_pur_var/$mkt_fabric_purchase_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($grey_feb_var/$mkt_grey_fab_cost_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($dy_var/$mkt_dy_fin_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($aop_var/$mkt_aop_array[$key]*100),2); ?></td>
                                         <td align="right"><? //echo number_format(($fin_fab_pur_var/$mkt_fin_fabric_purchase_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($fin_feb_pur_var/$mkt_fin_fabric_purchase_array[$key][$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($totfin_fab_var/$mkt_fin_fabric_purchase_array[$key]*100),2); ?></td> 
                                        <td align="right"><? echo number_format(($trims_var/$mkt_trims_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($print_var/$mkt_print_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($embro_var/$mkt_embro_array[$key]*100),2); ?></td>
										 <td align="right"><? echo number_format(($others_var/$mkt_others_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($dyeing_var/$mkt_dyeing_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($wash_var/$mkt_wash_array[$key]*100),2); ?></td>
                                      
                                        <td align="right"><? echo number_format(($com_var/$mkt_commn_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($commer_var/$mkt_commercial_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($fr_var/$mkt_freight_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($test_var/$mkt_test_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($ins_var/$mkt_ins_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($cour_var/$mkt_courier_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($cm_var/$mkt_cm_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($tot_var/$mkt_total_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($mar_var/$mkt_margin_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($margin_perc_to/$mkt_margin_perc*100),2); ?></td> 
                                    </tr>
                           	<?
                                $j++;
                                }
								
								$bgcolor5='#CCDDEE';
								$bgcolor6='#CCCCFF';
								$bgcolor7='#FFEEFF';
                            ?>
                            <tr bgcolor="<? echo $bgcolor5; ?>" onClick="change_color('trTD5_<? echo $j; ?>','<? echo $bgcolor5; ?>')" id="trTD5_<? echo $j; ?>">
                                <th colspan="3">Pre Costing</th>
                                <th align="right"><? echo number_format($tot_mkt_po_val,2); ?></th>
                               
                                <th align="right"><? echo number_format($tot_mkt_value_addition_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_yarn_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_knit_cost,2); ?></th>
                                 <th align="right"><? //echo number_format($tot_mkt_po_val,2); ?></th>
                                
                                <th align="right"><? echo number_format($tot_mkt_fin_pur_cost,2); ?></th>
                               <th align="right"><? echo number_format($tot_mkt_grey_fab_cost,2); ?></th>
                               <th align="right"><? echo number_format($tot_mkt_dy_fin_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_aop_cost,2); ?></th>
                                <th align="right"><? //echo number_format($tot_mkt_aop_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_fin_fabric_purchase,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_fin_fabric_purchase,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_trims_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_print_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_embro_cost,2); ?></th>
								 <th align="right"><? echo number_format($tot_mkt_others_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_dyeing_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_wash_cost,2); ?></th>
                               
                                <th align="right"><? echo number_format($tot_mkt_commn_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_commercial_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_freight_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_test_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_ins_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_courier_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_cm_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_total_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_margin_cost,2); ?></th>
                                <th align="right"><? $mm=$tot_mkt_margin_cost/$tot_mkt_po_val*100; echo number_format($mm,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor6; ?>" onClick="change_color('trTD6_<? echo $j; ?>','<? echo $bgcolor6; ?>')" id="trTD6_<? echo $j; ?>">
                                <th colspan="3">Actual</th>
                                <th align="right"><? echo number_format($tot_buyer_ex_factory_val,2); ?></th>
                                <th align="right"><? echo number_format($tot_yarn_dyeing_twist_actual,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_yarn_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_knit_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_grey_fab_cost,2); ?></th>
                               
                                <th align="right"><? echo number_format($tot_buyer_fin_pur_act_cost,2); ?></th>
                               <th align="right"><? echo number_format($tot_grey_fab_cost_act,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_dye_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_aop_n_others_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_fin_fab_transfer_amt_actual,2); ?></th>
                                <th align="right"><? echo number_format($tot_act_fin_fabric_purchase_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_finished_fab_cost_actual_buy,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_trims_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_act_print_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_act_embro_cost,2); ?></th>
								 <th align="right"><? echo number_format($tot_act_others_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_act_dyeing_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_act_wash_cost,2); ?></th>
                                
                                <th align="right"><? echo number_format($tot_buyer_commi_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_commercial_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_freight_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_testing_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_inspection_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_courier_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_cm_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_total_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_margin_cost,2); ?></th>
                                <th align="right"><? $pp=$tot_buyer_margin_cost/$tot_buyer_ex_factory_val*100; echo number_format($pp,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor7; ?>" onClick="change_color('trTD7_<? echo $j; ?>','<? echo $bgcolor7; ?>')" id="trTD7_<? echo $j; ?>">
                                <th colspan="3">Variance</th>
                                <th align="right"><? $evar=$tot_mkt_po_val-$tot_buyer_ex_factory_val;  echo number_format($evar,2); ?></th>
                                 <th align="right"><? $yarn_value_var=$tot_mkt_value_addition_cost-$tot_yarn_dyeing_twist_actual;  echo number_format($yarn_value_var,2); ?></th>
                                 <th align="right"><? $grey_yvar=$tot_mkt_yarn_cost-$tot_buyer_yarn_cost;  echo number_format($grey_yvar,2); ?></th>
                                <th align="right"><? $knit_var=$tot_mkt_knit_cost-$tot_buyer_knit_cost;  echo number_format($knit_var,2); ?></th>
                                <th align="right"><? echo "N/A";//$kvar=$tot_mkt_knit_cost-$tot_buyer_knit_cost;  echo number_format($kvar,2); ?></th>
                                
                                <th align="right"><? $fpfvar=$tot_mkt_fin_pur_cost-$tot_buyer_fin_pur_act_cost;  echo number_format($fpfvar,2); ?></th>
                                 <th align="right"><? $greypfvar=$tot_mkt_grey_fab_cost-$tot_grey_fab_cost_act;  echo number_format($greypfvar,2); ?></th>
                                 <th align="right"><? $dfvar=$tot_mkt_dy_fin_cost-$tot_buyer_dye_cost;  echo number_format($dfvar,2); ?></th>
                                <th align="right"><? $aopvar=$tot_mkt_aop_cost-$tot_buyer_aop_n_others_cost;  echo number_format($aopvar,2); ?></th>
                                <th align="right"><? echo "N/A";//$aopvar=$tot_mkt_aop_cost-$tot_buyer_aop_n_others_cost;  echo number_format($aopvar,2); ?></th>
                                <th align="right"><? //$fin_fab_var=$tot_mkt_fin_fabric_purchase-$tot_finished_fab_cost_actual_buy;  echo number_format($fin_fab_var,2); ?></th>
                                  <th align="right"><? $fin_fab_var=$tot_mkt_fin_fabric_purchase-$tot_finished_fab_cost_actual_buy;  echo number_format($fin_fab_var,2);; ?></th>
                                <th align="right"><? $trimvar=$tot_mkt_trims_cost-$tot_buyer_trims_cost;  echo number_format($trimvar,2); ?></th>
                                 <th align="right"><? $printvar=$tot_mkt_print_cost-$tot_act_print_cost;  echo number_format($printvar,2); ?></th>
                                 <th align="right"><? $embrovar=$tot_mkt_embro_cost-$tot_act_embro_cost;  echo number_format($embrovar,2); ?></th>
								 <th align="right"><? $othersvar=$tot_mkt_others_cost-$tot_act_others_cost;  echo number_format($othersovar,2); ?></th>
                                <th align="right"><? $dyeingvar=$tot_mkt_dyeing_cost-$tot_act_dyeing_cost;  echo number_format($dyeingvar,2); ?></th>
                                <th align="right"><? $washingvar=$tot_mkt_wash_cost-$tot_act_wash_cost;  echo number_format($washingvar,2); ?></th>
                                
                                <th align="right"><? $comvar=$tot_mkt_commn_cost-$tot_buyer_commi_cost;  echo number_format($comvar,2); ?></th>
                                <th align="right"><? $commercialvar=$tot_mkt_commercial_cost-$tot_buyer_commercial_cost;  echo number_format($commercialvar,2); ?></th>
                                <th align="right"><? $fvar=$tot_mkt_freight_cost-$tot_buyer_freight_cost;  echo number_format($fvar,2); ?></th>
                                <th align="right"><? $tvar=$tot_mkt_test_cost-$tot_buyer_testing_cost;  echo number_format($tvar,2); ?></th>
                                <th align="right"><? $ivar=$tot_mkt_ins_cost-$tot_buyer_inspection_cost;  echo number_format($ivar,2); ?></th>
                                <th align="right"><? $courvar=$tot_mkt_courier_cost-$tot_buyer_courier_cost;  echo number_format($courvar,2); ?></th>
                                <th align="right"><? $cmvar=$tot_mkt_cm_cost-$tot_buyer_cm_cost;  echo number_format($cmvar,2); ?></th>
                                <th align="right"><? $totvar=$tot_mkt_total_cost-$tot_buyer_total_cost;  echo number_format($totvar,2); ?></th>
                                <th align="right"><? $mvar=$tot_mkt_margin_cost-$tot_buyer_margin_cost;  echo number_format($mvar,2); ?></th>
                                <th align="right"><? $mpvar=$mm-$pp;  echo number_format($mpvar,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor6; ?>" onClick="change_color('trTD8_<? echo $j; ?>','<? echo $bgcolor6; ?>')" id="trTD8_<? echo $j; ?>">
                                <th colspan="3">Variance (%)</th>
                                <th align="right"><? echo number_format(($evar/$tot_mkt_po_val*100),2); ?></th>
                                 <th align="right"><? echo number_format(($yarn_value_var/$tot_mkt_value_addition_cost*100),2); ?></th>
                                  <th align="right"><? echo number_format(($grey_yvar/$tot_mkt_yarn_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($knit_var/$tot_mkt_yarn_cost*100),2); ?></th>
                                <th align="right"><? echo "N/A";//echo number_format(($kvar/$tot_mkt_knit_cost*100),2); ?></th>
                                
                                <th align="right"><? echo number_format(($fpfvar/$tot_mkt_fin_pur_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($greypfvar/$tot_mkt_grey_fab_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($dfvar/$tot_mkt_dy_fin_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($aopvar/$tot_mkt_aop_cost*100),2); ?></th>
                                <th align="right"><? echo "N/A";//echo number_format(($aopvar/$tot_mkt_aop_cost*100),2); ?></th>
                                <th align="right"><?  echo number_format(($fin_fab_var/$tot_mkt_fin_fabric_purchase*100),2); ?></th>
                                <th align="right"><? echo number_format(($trimvar/$tot_mkt_trims_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($printvar/$tot_mkt_trims_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($embrovar/$tot_mkt_embro_cost*100),2); ?></th>
								<th align="right"><? echo number_format(($othersvar/$tot_mkt_others_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($dyeingvar/$tot_mkt_dyeing_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($washingvar/$tot_mkt_wash_cost*100),2); ?></th> 
                                <th align="right"><? echo number_format(($washingvar/$tot_mkt_wash_cost*100),2); ?></th> 
                              
                                <th align="right"><? echo number_format(($comvar/$tot_mkt_commn_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($commercialvar/$tot_mkt_commercial_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($fvar/$tot_mkt_freight_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($tvar/$tot_mkt_test_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($ivar/$tot_mkt_ins_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($courvar/$tot_mkt_courier_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($cmvar/$tot_mkt_cm_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($totvar/$tot_mkt_total_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($mvar/$tot_mkt_margin_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($mpvar/$mm*100),2); ?></th>   
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
	</fieldset>
 <?
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename****$report_type";
	exit();
}
if($action=="report_generate2")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$report_type=str_replace("'","",$report);
	
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
		

	}
	$poCond="";
	if(str_replace("'","",$txt_ex_date_from)!="" && str_replace("'","",$txt_ex_date_to)!="")
	{
		
		$ex_factory_data=sql_select("SELECT a.id po_id 	FROM wo_po_break_down a,pro_ex_factory_mst b WHERE  a.status_active=1  and 	b.status_active=1 and 
		b.entry_form<>85 and b.po_break_down_id=a.id and b.ex_factory_date between $txt_ex_date_from and $txt_ex_date_to 	GROUP BY a.id");

		

		foreach($ex_factory_data as $row){

			$poIdArr[$row[csf('po_id')]]=$row[csf('po_id')];
		}
		
		//$poids=implode(",",$poIdArr);
		//$poCond="and b.id in ($poids)";
		$poCond=where_con_using_array($poIdArr,0,"b.id");
	}
	 
	
	

	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="") $po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		else $po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
	}
	
	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0) $shipping_status_cond=" and b.shiping_status=$shipping_status";
	
	$po_ids_array=array();
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
		
	$sql="select a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field,a.job_quantity, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $po_id_cond $ref_cond $file_cond $shipping_status_cond $year_cond $poCond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
	//echo $sql; //die;
	$result=sql_select($sql);
	$all_po_id="";	$all_job_id="";
	foreach($result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		//if($all_job_id=="") $all_job_id="'".$row[csf("job_no")]."'"; else $all_job_id.=","."'".$row[csf("job_no")]."'";
		if($all_job_id=="") $all_job_id=$row[csf("job_id")]; else $all_job_id.=",".$row[csf("job_id")];
		$job_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		$job_arr2[$row[csf("job_no")]]['po_quantity']=$row[csf("po_quantity")];
		$job_arr2[$row[csf("job_no")]]['job_quantity']=$row[csf("job_quantity")];
		$poId[$row[csf("id")]]=$row[csf("id")];
	}

	if($all_po_id=="") 
	{ 
		echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
	}

	$sewing_data=sql_select("SELECT c.po_break_down_id, c.item_number_id,sum(d.production_qnty) as sewingin_qnty,c.production_type from pro_garments_production_mst c,pro_garments_production_dtls d where c.id=d.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and c.production_type in (4,5)
	 and d.production_type  in (4,5) and d.is_deleted=0 and c.company_id='$company_name' ".where_con_using_array($poId,1,'c.po_break_down_id')."  group by c.po_break_down_id, c.item_number_id,c.production_type ");

	 foreach($sewing_data as $val){
		if($val[csf("production_type")]==4){
		$sewing_data_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]['sewingin_qnty']+=$val[csf("sewingin_qnty")];
		}else{
		$sewing_data_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]['sewingout_qnty']+=$val[csf("sewingin_qnty")];
		}
	 }


	//	echo $all_job_id;die;
	$jobIds=chop($all_job_id,',');
	//echo $jobIds;die;
	$job_cond_for_in=""; $jobIdCond="";
	$job_ids=count(array_unique(explode(",",$all_job_id)));
	if($db_type==2 && $job_ids>1000)
	{
		$job_cond_for_in=" and (";
		$jobIdCond=" and (";
		$jobIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($jobIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$job_cond_for_in.=" a.job_id in($ids) or"; 
			$jobIdCond.=" a.job_no_id in($ids) or"; 
		}
		$job_cond_for_in=chop($job_cond_for_in,'or ');
		$job_cond_for_in.=")";
		$jobIdCond=chop($jobIdCond,'or ');
		$jobIdCond.=")";
	}
	else
	{
		$jobIds=implode(",",array_unique(explode(",",$all_job_id)));
		$job_cond_for_in=" and a.job_id in($jobIds)";
		$jobIdCond=" and a.job_no_id in($jobIds)";
	}
	//echo $job_cond_for_in;die;
	
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";$po_cond_for_in3=""; 
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$po_cond_for_in2=" and (";
		$po_cond_for_in3=" and (";
		$po_cond_for_in4=" and (";
		$po_cond_for_in5=" and (";
		$po_cond_for_in6=" and (";
		$po_cond_for_in7=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
			$po_cond_for_in2.=" po_id in($ids) or"; 
			$po_cond_for_in3.=" b.order_id in($ids) or";
			$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
			$po_cond_for_in5.=" b.po_id in($ids) or"; 
			$po_cond_for_in6.=" a.to_order_id in($ids) or"; 
			$po_cond_for_in7.=" a.from_order_id in($ids) or"; 
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
		
		$po_cond_for_in2=chop($po_cond_for_in2,'or ');
		$po_cond_for_in2.=")";
		$po_cond_for_in3=chop($po_cond_for_in3,'or ');
		$po_cond_for_in3.=")";
		$po_cond_for_in4=chop($po_cond_for_in4,'or ');
		$po_cond_for_in4.=")";
		$po_cond_for_in5=chop($po_cond_for_in5,'or ');
		$po_cond_for_in5.=")";
		$po_cond_for_in6=chop($po_cond_for_in6,'or ');
		$po_cond_for_in6.=")";
		$po_cond_for_in7=chop($po_cond_for_in7,'or ');
		$po_cond_for_in7.=")";
	}
	else
	{
		$poIds=implode(",",array_unique(explode(",",$all_po_id)));
		$po_cond_for_in=" and b.po_break_down_id in($poIds)";
		$po_cond_for_in2=" and po_id in($poIds)";
		$po_cond_for_in3=" and b.order_id in($poIds)";
		$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";
		$po_cond_for_in5=" and b.po_id in($poIds)";
		$po_cond_for_in6=" and a.to_order_id in($poIds)";
		$po_cond_for_in7=" and a.from_order_id in($poIds)";
	}
		
	if($poIds!='' || $poIds!=0) $po_cond_for_in=$po_cond_for_in; 
	else $po_cond_for_in="and b.po_break_down_id in(0)";
	
	$po_idArr=array_unique(explode(",",$all_po_id));
	$po_id_cond_for_in=where_con_using_array($po_idArr,0,"e.po_id"); 
	
	$ActualArray_sql=sql_select("select a.id,b.pr_date, b.man_power, b.target_per_hour, e.working_hour,e.operator,e.helper,c.from_date,c.to_date,c.capacity,e.po_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size e where a.id=c.mst_id and c.id=b.mast_dtl_id and e.mst_id=a.id and e.dtls_id=c.id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $po_id_cond_for_in");
	//echo "select a.id,b.pr_date, b.man_power, b.target_per_hour, e.working_hour,e.operator,e.helper,c.from_date,c.to_date,c.capacity,e.po_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size e where a.id=c.mst_id and c.id=b.mast_dtl_id and e.mst_id=a.id and e.dtls_id=c.id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $po_id_cond_for_in";
	 
	 
	
		foreach($ActualArray_sql as $val)
		{
			//$pr_date=date('m-Y',$val[csf('pr_date')]);
			$pr_date=date("Y-m",strtotime(($val[csf('pr_date')])));
			//echo $pr_date;
			$prod_resource_array[$val[csf('po_id')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('po_id')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('po_id')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('po_id')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('po_id')]]['operator_helper']=$val[csf('operator')]+$val[csf('helper')];
			$prod_resource_array[$val[csf('po_id')]]['working_hour']+=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('po_id')]]['pr_date']=$val[csf('pr_date')];
			//$prod_resource_array[$val[csf('po_id')]]['day_start']=$val[csf('from_date')];
			$actual_prod_id_arr[$val[csf('id')]]=$val[csf('id')];
			 
		}
		$act_id_cond_for_in=where_con_using_array($actual_prod_id_arr,0,"a.id"); 
		$ActualExtraArray_sql=sql_select("select a.id,b.pr_date, b.man_power, e.style_ref_no,e.number_of_emp, e.adjust_hour as working_hour,c.from_date,c.to_date,c.capacity,e.style_ref_no from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_smv_adj e where a.id=c.mst_id and c.id=b.mast_dtl_id and e.mst_id=a.id and e.dtl_id=b.id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $act_id_cond_for_in");
	
		foreach($ActualExtraArray_sql as $val)
		{
			$pr_date=date("Y-m",strtotime(($val[csf('pr_date')])));
			$adj_resource_array[$val[csf('style_ref_no')]]['working_hour']+=$val[csf('working_hour')];
			$adj_resource_array[$val[csf('style_ref_no')]]['pr_date']=$val[csf('pr_date')];
			$adj_resource_array[$val[csf('style_ref_no')]]['number_of_emp']+=$val[csf('number_of_emp')];
		}
		unset($ActualExtraArray_sql);
		//print_r($prod_resource_array);
		$sql_cpm="select monthly_cm_expense,applying_period_date,applying_period_to_date, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn, interest_expense, income_tax from lib_standard_cm_entry where company_id=$company_name   and status_active=1 and is_deleted=0";
	 
	$lib_data_array=sql_select($sql_cpm);
	foreach ($lib_data_array as $row)
	{
	  $applying_period_date=$row[csf("applying_period_date")];
	  $from_cmp_date=date("Y-m",strtotime(($applying_period_date)));
	 $lib_cpm_array[$from_cmp_date]['cpm']=$row[csf('cost_per_minute')];
	}
	unset($lib_data_array);
	
	
	$ex_factory_arr=return_library_array( "select b.po_break_down_id, sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id", "po_break_down_id", "qnty");	
	
	ob_start();
	?>
    <fieldset>
    	<table width="4830">
            <tr class="form_caption">
                <td colspan="36" align="center"><strong>Post Costing Report V4</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="37" align="center"><strong><? echo $company_arr[$company_name];?></strong>
                    <br>
                    <strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
                </td>
            </tr>
        </table>
        <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="4830" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
                <th width="60">Job Year</th>
                <th width="70">Job No</th>
                <th width="100">PO No</th>
				<th width="100">Sales Contract No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="110">Style Name</th>
                <th width="120">Garments Item</th>
                <th width="90">PO Quantity<br>Pcs</th>
                <th width="50">UOM</th>
                <th width="70">Unit Price</th>
                <th width="110">PO Value</th>
                <th width="100">SMV</th>
                <th width="80">Shipment Date</th>
                <th width="100">Shipping Status</th>
                <th width="100">Cost Source</th>
                <th width="100">PO/Ex-Factory Qnty</th>
                <th width="110">PO/Ex-Factory Value</th>
                <th width="110">Grey Yarn Cost</th>
                <th width="100">Yarn Value Addition Cost</th>
                <th width="110">Knitting Cost</th>
                <th width="100">Grey Fabric Transfer Cost</th>
              
                <th width="100">Grey Fabric Cost</th>
                
                <th width="110">Dye & Fin Cost</th>
                <th width="110">AOP Cost</th>
                <th width="100">Fin. Fab. Transfer Cost</th>
                <th width="100">Fin.Fabric Purchase Cost</th>
                <th width="110">Grey Fabric Purchease Cost</th>
                <th width="100">Finished Fabric Cost</th>
                <th width="100" title="Fin.Fabric Purchase Cost+Finished Fabric Cost">Total Fabric Cost</th>
                <th width="110">Trims Cost</th>
                <th width="100">Printing Cost</th>
                <th width="100">Embroidery Cost</th>
				<th width="100">Others Cost</th>
                <th width="100">Gmt Dyeing Cost</th>
                <th width="100">Washing Cost</th>
               
                <th width="100">Commission Cost</th>
                <th width="100">Commercial Cost</th>
                <th width="100">Freight Cost</th>
                <th width="100">Testing Cost</th>
                <th width="100">Inspection Cost</th>
                <th width="100">Courier Cost</th>
                <th width="100">CM Cost</th>
                <th width="100">Total Cost</th>
                <th width="100">Margin/Loss</th>
                <th  width="100">% to Ex-Factory Value</th>
                <th width="100">CPA/Short Fab. Cost</th>
                <th width="">Net Margin/Loss</th>
            </thead>
        </table>
    	<div style="width:4850px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="4830" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
			$process_costing=return_field_value("process_costing_maintain", "variable_settings_production", "company_name=$company_name and variable_list=34 and is_deleted=0 and status_active=1");
		  //	echo $process_costing.'ddd';die;
			
			$usd_id=2;
			$fabriccostArray=array(); $yarncostArray=array(); $trimsCostArray=array(); $prodcostArray=array(); $actualCostArray=array(); $actualTrimsCostArray=array(); 
			$subconCostArray=array(); $embellCostArray=array(); $washCostArray=array(); $aopCostArray=array(); $yarnTrimsCostArray=array(); 
			$yarncostDataArray=sql_select("select a.job_no, sum(a.amount) as amnt, sum(a.rate*a.avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls a where a.status_active=1 and a.is_deleted=0 $job_cond_for_in group by a.job_no");
			foreach($yarncostDataArray as $yarnRow)
			{
			   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
			}
			unset($yarncostDataArray);
			$fabriccostDataArray=sql_select("select a.job_no, a.costing_per_id, a.embel_cost, a.wash_cost, a.cm_cost, a.commission, a.currier_pre_cost, a.lab_test, a.inspection, a.freight, a.comm_cost from wo_pre_cost_dtls a where a.status_active=1 and a.is_deleted=0 $job_cond_for_in");
			foreach($fabriccostDataArray as $fabRow)
			{
				 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
				 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
			}
			unset($fabriccostDataArray);
			$prodcostDataArray="select a.body_part_id,a.lib_yarn_count_deter_id as deter_id,b.po_break_down_id as po_id,
								  avg(CASE WHEN c.cons_process=1 THEN c.charge_unit END) AS knit_charge,
								  avg(CASE WHEN c.cons_process=30 THEN c.charge_unit END) AS yarn_dye_charge,
								  avg(CASE WHEN c.cons_process=35 THEN c.charge_unit END) AS aop_charge,
								  avg(CASE WHEN c.cons_process not in(1,2,30,35,134) THEN c.charge_unit END) AS dye_finish_charge
								  from wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls b where a.id=c.fabric_description and a.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=c.job_no and c.status_active=1 and c.is_deleted=0  $po_cond_for_in group by  a.lib_yarn_count_deter_id,b.po_break_down_id,a.body_part_id";
			$resultfab_arr = sql_select($prodcostDataArray);
			foreach($resultfab_arr as $prodRow)
			{
				$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['knit_charge']=$prodRow[csf('knit_charge')];
				$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
				$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['aop_charge']=$prodRow[csf('aop_charge')];
				$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
			}	
			unset($resultfab_arr);
			if($poIds!='' || $poIds!=0)
			{
				$po_cond_for_in4=$po_cond_for_in4; 
			}
			else
			{
				$po_cond_for_in4="and b.po_breakdown_id in(0)";
			}
			$finProd_grey_used_sql = "select c.body_part_id,c.fabric_description_id as deter_id,b.po_breakdown_id as po_id, b.quantity as grey_used_qty from order_wise_pro_details b,pro_finish_fabric_rcv_dtls c where  b.dtls_id=c.id and b.entry_form=7 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 ";
			$fingrey_used_data =sql_select($finProd_grey_used_sql );
			foreach($fingrey_used_data as $row)
			{
				$fin_dye_finish_charge=$prodcostArray[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
				$fin_grey_used_arr[$row[csf('po_id')]]['grey_used_amt']+=$row[csf('grey_used_qty')]*$fin_dye_finish_charge;
			}
			unset($fingrey_used_data);
			
		
			$grey_used_sql = "select a.body_part_id,a.fabric_description_id as deter_id,b.qnty,b.po_breakdown_id as po_id from pro_finish_fabric_rcv_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=66 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4";
			$grey_used_data =sql_select($grey_used_sql );
			foreach($grey_used_data as $row)
			{
				$finProd_grey_used=$finProd_grey_usedArr[$row[csf('po_id')]];
				$dye_finish_charge=$prodcostArray[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
				$grey_used_arr[$row[csf('po_id')]]['grey_used_amt']+=$row[csf('qnty')]*$dye_finish_charge;
			}
			unset($grey_used_data);
			if($jobIds!='' || $jobIds!=0)
			{
				$job_cond_for_in=$job_cond_for_in; 
				$jobIdCond=$jobIdCond;
			}
			else
			{
				$job_cond_for_in="and a.job_no in('0')";
				$jobIdCond="and a.job_no in('0')";
			}
			$all_job_IdArr=array_unique(explode(",",$all_job_id));
			$jobId_cond_for_in=where_con_using_array($all_job_IdArr,0,"a.job_no_id"); 
			
			$yarn_dyeing_costArray=array(); //product_id
			 $yarndyeing_sql="select b.booking_date,b.id,b.currency,b.is_short,b.ydw_no,a.job_no,a.yarn_color,a.product_id,avg(a.dyeing_charge) as dyeing_charge,  sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a where  b.id=a.mst_id and b.entry_form in(41,94) and a.status_active=1 and a.is_deleted=0 $jobId_cond_for_in group by b.id,b.currency,b.is_short,a.job_no,a.product_id,b.ydw_no,a.yarn_color,b.booking_date";
			$yarndyeing_result = sql_select($yarndyeing_sql);
			foreach($yarndyeing_result as $yarnRow)
			{
				$yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job']=$yarnRow[csf('job_no')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['ydw_no']=$yarnRow[csf('ydw_no')];
				$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['booking_date']=$yarnRow[csf('booking_date')];
				$yarn_dyeing_curr_arr[$yarnRow[csf('ydw_no')]]['currency']=$yarnRow[csf('currency')];
				$yarn_dyeing_rate_arr[$yarnRow[csf('job_no')]][$yarnRow[csf('yarn_color')]]['rate']=$yarnRow[csf('dyeing_charge')];			
				$yarn_dyeing_mst_idArr[$yarnRow[csf('id')]]=$yarnRow[csf('id')];
			}
			unset($resultyarnIssueData);	
				
			$yarnIssueData="select b.prod_id,sum(a.cons_amount) as cons_amount,sum(a.cons_quantity) as cons_quantity
				from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1) and a.transaction_type in(2,4,5,6) and b.entry_form ='3' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25)  $po_cond_for_in4  group by  b.prod_id";
			$resultyarnIssueData = sql_select($yarnIssueData);
			$all_prod_ids="";
			foreach($resultyarnIssueData as $row)
			{
				if($all_prod_ids=="") $all_prod_ids=$row[csf('prod_id')];else $all_prod_ids.=",".$row[csf('prod_id')];
				
				$yarn_issue_amt_arr[$row[csf('prod_id')]]['amt']+=$row[csf('cons_amount')];
				$yarn_issue_amt_arr[$row[csf('prod_id')]]['qty']+=$row[csf('cons_quantity')];
			}
			unset($resultyarnIssueData);
			$prodIds=chop($all_prod_ids,',');
			$prod_cond_for_in="";
			$prod_ids=count(array_unique(explode(",",$all_prod_ids)));
			if($db_type==2 && $prod_ids>1000)
			{
				$prod_cond_for_in=" and (";
				$prodIdsArr=array_chunk(explode(",",$prodIds),999);
				foreach($prodIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$prod_cond_for_in.=" a.prod_id in($ids) or"; 
				}
				$prod_cond_for_in=chop($prod_cond_for_in,'or ');
				$prod_cond_for_in.=")";
			}
			else
			{
				$prodIds=implode(",",array_unique(explode(",",$all_prod_ids)));
				$prod_cond_for_in=" and a.prod_id in($prodIds)";
			}
			
			if($prodIds!='' || $prodIds!=0)
			{
				$prod_cond_for_in=$prod_cond_for_in;
			}
			else
			{
				$prod_cond_for_in="and a.prod_id in(0)";
			}
					
			 $sql_receive_for_issue="select c.booking_id,a.job_no,a.prod_id,max(a.transaction_date) as transaction_date,c.currency_id,c.receive_purpose,b.lot,b.color, 
			 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt, sum(CASE WHEN c.receive_purpose in(2,15,38) THEN a.order_amount ELSE 0 END) AS order_amount_recv
			  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $prod_cond_for_in  group by c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.lot,b.color";
			$resultReceive_chek = sql_select($sql_receive_for_issue);
			
			foreach($resultReceive_chek as $row)
			{
				$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose']=$row[csf('receive_purpose')];
				$receive_date_array[$row[csf('prod_id')]]['last_trans_date']=$row[csf('transaction_date')];
				$avg_rate=$row[csf('amnt')]/$row[csf('qty')];
				$receive_array[$row[csf('prod_id')]]=$avg_rate;
				if($row[csf('prod_id')]!="")
				{
					$prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
				}
			}
			//fabric_purchase_cost
			unset($resultReceive_chek);
			
			//$jobIdArr=array_unique(explode(",",$all_job_id));
			$YdId_cond_for_in=where_con_using_array($yarn_dyeing_mst_idArr,0,"c.booking_id"); 
			 $sql_receive="select c.recv_number,c.booking_id,a.job_no,a.prod_id,max(a.transaction_date) as transaction_date,c.receive_purpose,b.lot,b.color,c.currency_id,
			 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt,
			 sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv,
			  sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.cons_quantity ELSE 0 END) AS cons_quantity
			  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id  and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1  $YdId_cond_for_in  and a.is_deleted=0    group by c.recv_number,c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.lot,b.color";
			$resultReceive = sql_select($sql_receive);$all_recv_prod_ids="";
			foreach($resultReceive as $invRow)
			{
				if($all_recv_prod_ids=="") $all_recv_prod_ids=$invRow[csf('prod_id')];else $all_recv_prod_ids.=",".$invRow[csf('prod_id')];
				$receive_curr_array[$invRow[csf('prod_id')]]=$invRow[csf('currency_id')];
				$jobNo=$yarn_dyeing_costArray2[$invRow[csf('booking_id')]]['job'];
				$ydw_no=$yarn_dyeing_costArray2[$invRow[csf('booking_id')]]['ydw_no'];
				$is_short=$yarn_dyeing_isshort_arr[$ydw_no]['is_short'];
				//echo $jobNo.'=='.$invRow[csf('booking_id')].'b';
				$yarn_rec_job_arr[$invRow[csf('recv_number')]]['job']=$jobNo;
				$yarn_rec_job_arr[$invRow[csf('recv_number')]]['ydw_no']=$ydw_no;
				$booking_date=$yarn_dyeing_isshort_arr[$ydw_no]['booking_date'];
				$dye_rate=$yarn_dyeing_rate_arr[$jobNo][$invRow[csf('color')]]['rate'];
					//echo $ydw_no.'d'.$dye_rate;
				$currency=$yarn_dyeing_curr_arr[$ydw_no]['currency'];
				//$po_quantity=$job_arr2[$jobNo]['po_quantity'];
				$order_amount_recv=$invRow[csf('order_amount_recv')];
				$cons_quantity=$invRow[csf('cons_quantity')];
				$transaction_date=$booking_date;
				if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
				
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
			
				if($currency==1) $avg_rate=$dye_rate/$currency_rate;//Taka
				else $avg_rate=$dye_rate;	
				//echo $is_short.'='.$avg_rate.'='.$cons_quantity.', ';
				if($is_short==2) $yarn_receive_amount_arr[$jobNo]['order_amount_recv']+=$avg_rate*$cons_quantity;//Main Booking..
				else $yarn_receive_amount_arr[$jobNo]['cpa_order_amount_recv']+=$avg_rate*$cons_quantity;	
			}
		  //	print_r($yarn_receive_amount_arr);
			//echo $all_recv_prod_ids;
			unset($resultReceive);
			 
			if($all_recv_prod_ids!="")
			{
				$recvprodIds=chop($all_recv_prod_ids,',');
				$recv_prod_cond_for_in="";
				$recv_prod_ids=count(array_unique(explode(",",$all_recv_prod_ids)));
				if($db_type==2 && $recv_prod_ids>1000)
				{
					$recv_prod_cond_for_in=" and (";
					$recvprodIdsArr=array_chunk(explode(",",$recvprodIds),999);
					foreach($recvprodIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$recv_prod_cond_for_in.=" a.prod_id in($ids) or"; 
					}
					$recv_prod_cond_for_in=chop($recv_prod_cond_for_in,'or ');
					$recv_prod_cond_for_in.=")";
				}
				else
				{
					$recprodIds=implode(",",array_unique(explode(",",$all_recv_prod_ids)));
					$recv_prod_cond_for_in=" and a.prod_id in($recprodIds)";
				}
			}
			//echo $recv_prod_cond_for_in;
			
			$sql_receive_ret="select a.prod_id,b.lot,b.color,c.received_mrr_no,
			 sum(a.cons_quantity) as cons_quantity
			  from inv_transaction a,product_details_master b,inv_issue_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=8 and c.item_category=1 and a.transaction_type=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $recv_prod_cond_for_in  group by a.prod_id,b.lot,b.color,c.received_mrr_no";
			$resultReceive_ret = sql_select($sql_receive_ret);
			
			foreach($resultReceive_ret as $row)
			{
				$ydw_no=$yarn_rec_job_arr[$row[csf('received_mrr_no')]]['ydw_no'];
				$recv_job=$yarn_rec_job_arr[$row[csf('received_mrr_no')]]['job'];
				$is_short=$yarn_dyeing_isshort_arr[$ydw_no]['is_short'];
				$booking_date=$yarn_dyeing_isshort_arr[$ydw_no]['booking_date'];
				$ydye_rate=$yarn_dyeing_rate_arr[$jobNo][$row[csf('color')]]['rate'];
				$currency=$yarn_dyeing_curr_arr[$ydw_no]['currency'];
				//echo $recv_job.'d'.$currency;
				$transaction_date=$booking_date;
				if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
				
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				if($currency==1) $avg_rate=$ydye_rate/$currency_rate;//Taka
				else $avg_rate=$ydye_rate;	
				
				$ret_amt=$row[csf('cons_quantity')]*$avg_rate;
				//$receive_ret_arr[$row[csf('prod_id')]]['last_trans_date']=$row[csf('cons_quantity')];
				if($is_short==2) $receive_ret_arr[$recv_job]['recv_ret_amt']+=$ret_amt; //Main Booking
				else $receive_ret_arr[$recv_job]['cpa_recv_ret_amt']+=$ret_amt;
			}
			unset($resultReceive_ret);
			//print_r($receive_ret_arr);
			//booking_type,is_short
			if($poIds!='' || $poIds!=0) $po_cond_for_in=$po_cond_for_in; 
			else $po_cond_for_in="and b.po_break_down_id in(0)";
			
			 $sql_wo_aop="select a.id,a.booking_date,a.currency_id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.process,b.po_break_down_id as po_id,
			(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty,
			(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount
			 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in";
			 // echo  $sql_wo_aop;die;
			$result_aop_rate=sql_select( $sql_wo_aop );
			foreach ($result_aop_rate as $row)
			{
				if($row[csf('item_category')]==12)
				{
					$wo_qnty=$row[csf('wo_qnty')];
					$amount=$row[csf('amount')];
					$processId=$row[csf('process')];
					if($amount>0 && $processId==35)
					{
					$avg_wo_aop_rate=$amount/$wo_qnty;
					$aop_prod_array[$row[csf('po_id')]]['aop_rate']=$avg_wo_aop_rate;
					
					$aop_prod_array[$row[csf('po_id')]]['currency_id']=$row[csf('currency_id')];
					$aop_prod_array[$row[csf('po_id')]]['booking_date']=$row[csf('booking_date')];
					}
				}
				else
				{
					$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
					$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
					$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
					$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
				}
			}
			unset($result_aop_rate);
			if($poIds!='' || $poIds!=0) $po_cond_for_in3=$po_cond_for_in3; else $po_cond_for_in3="and b.order_id in(0)";
			
			$sql_aop="select a.company_id, b.batch_issue_qty as issue_qty,b.currency_id,a.receive_date,a.dyeing_source,a.dyeing_company,b.prod_id,b.order_id as po_id,b.grey_used,b.rate from pro_grey_batch_dtls b,inv_receive_mas_batchroll a where a.id=b.mst_id  and a.entry_form=92  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.process_id=35 $po_cond_for_in3";

			// echo "<pre>";
			// print_r($aop_prod_array);
			$result_aop=sql_select( $sql_aop );
			foreach ($result_aop as $row) 
			{
				//$aop_rate=$aop_prod_array[$row[csf('po_id')]]['aop_rate'];
				//$booking_date=$aop_prod_array[$row[csf('po_id')]]['booking_date'];
				//$currency_id=$aop_prod_array[$row[csf('po_id')]]['currency_id'];
				$aop_rate=$row[csf('rate')];
				$currency_id=$row[csf('currency_id')];
				$booking_date=$row[csf('receive_date')];
					
				$booking_date=$booking_date;
				if($db_type==0) $conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
				
				$exchange_rate=set_conversion_rate($usd_id,$conversion_date,1 );
				if($currency_id==1) $avg_rate=$aop_rate/$exchange_rate;//TK
				else $avg_rate=$aop_rate;
				if($row[csf('grey_used')]>0 && $avg_rate>0)
				{	
				//echo $row[csf('grey_used')].'='.$row[csf('grey_used')].'<br>';
				$aop_amt=$row[csf('grey_used')]*$avg_rate;
				$aop_prod_array[$row[csf('po_id')]]['aop_amt']+=$aop_amt;
				}
			}
			
			//die;
			unset($result_aop);
			$sql_yarn="select c.id,a.requisition_no,a.receive_basis,a.prod_id,(a.cons_amount) as cons_amount,c.issue_basis,c.issue_number,c.booking_no from inv_transaction a, order_wise_pro_details b, inv_issue_master c where  a.id=b.trans_id and  c.id=a.mst_id and c.entry_form in(3) and b.entry_form in(3)  and  a.transaction_type=2 $po_cond_for_in4";
			$result_yarn=sql_select($sql_yarn);
			foreach($result_yarn as $invRow)
			{
				if($invRow[csf('issue_basis')]==1)// Booking
				{
					$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('booking_no')];
				}
				else if($invRow[csf('issue_basis')]==3)// Requesition
				{
					$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('requisition_no')];
				}
			}
			
			unset($result_yarn);
				//die;
			if($poIds!='' || $poIds!=0) $po_cond_for_in2=$po_cond_for_in2; 
			else $po_cond_for_in2="and po_id in(0)";
			
			$plan_details_array = return_library_array("select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where company_id=$company_name $po_cond_for_in2 group by dtls_id,booking_no", "dtls_id", "booking_no");
			//print_r($plan_details_array);
			$reqs_array = array();
			
			$reqs_sql = sql_select("select a.knit_id, a.requisition_no as reqs_no, sum(a.yarn_qnty) as yarn_req_qnty from ppl_planning_entry_plan_dtls b,ppl_yarn_requisition_entry a where b.dtls_id=a.knit_id and b.status_active=1 and b.is_deleted=0 $po_cond_for_in5 group by a.knit_id, a.requisition_no");
			foreach ($reqs_sql as $row)
			{
				$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
			}
			unset($reqs_sql);
			//and b.issue_purpose in(2,4,15,38)
		    $yarnissue_retData="select d.booking_no,a.item_category,b.po_breakdown_id, b.prod_id, a.mst_id,a.receive_basis, b.issue_purpose,c.lot,c.color,
			(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.transaction_date ELSE null END) AS transaction_date,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.order_amount ELSE 0 END) AS order_amount_ret,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.order_qnty ELSE 0 END) AS order_qnty_ret
			from inv_transaction a, order_wise_pro_details b,product_details_master c,inv_receive_master d where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id  and d.id=a.mst_id and a.item_category in(1) and a.transaction_type in(4) and b.entry_form in(9) and d.entry_form in(9) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $po_cond_for_in4  group by d.booking_no,b.po_breakdown_id, b.prod_id, a.item_category,a.transaction_date,a.receive_basis,a.mst_id,a.transaction_type,b.entry_form,b.trans_type,b.issue_purpose,c.lot,c.color";
			$yarnissueretDataArray=sql_select($yarnissue_retData); $yarnissueRetCostArray=array();
			foreach($yarnissueretDataArray as $invRow)
			{
				$prod_id=$invRow[csf('prod_id')];
				$prodIdArr[$prod_id]=$prod_id;
			}
			$prodId_cond_for_in=where_con_using_array($prodIdArr,0,"id"); 
			if(count($prodIdArr)>0)
			{
			$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1 $prodId_cond_for_in", "id", "avg_rate_per_unit");
			}
			foreach($yarnissueretDataArray as $invRow)
			{
				$issue_purpose=$invRow[csf('issue_purpose')];
				$receive_basis=$invRow[csf('receive_basis')];
				//echo $receive_basis.'ff';
				if($receive_basis==1) //Booking Basis
				{
					$booking_no=$invRow[csf('booking_no')];
				}
				
				else if($receive_basis==3) //Requisition Basis
				{
					$booking_req_no=$invRow[csf('booking_no')];
					$prog_no=$reqs_array[$booking_req_no]['knit_id'];
					$booking_no=$plan_details_array[$prog_no];
					//echo $booking_req_no.'req';
				}
				$booking_type=$booking_array[$booking_no]['btype'];
				$is_short=$booking_array[$booking_no]['is_short'];
				//echo $booking_type.'='.$is_short.'='.$booking_no.'<br>';
				$returnable_ret_qnty=$invRow[csf('returnable_qnty')];
				//echo $invRow[csf('yarn_iss_return_qty')].'<br>';
				 $issue_ret_qty=$invRow[csf('yarn_iss_return_qty')];
				  $order_qnty_ret=$invRow[csf('order_qnty_ret')];
				  $order_amount_ret=$invRow[csf('order_amount_ret')];
				 $avg_rate=$order_amount_ret/$order_qnty_ret;
				//echo $avg_rate.'<br>';
				//$rate='';
				$transaction_date=$invRow[csf('transaction_date')];
				if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
				
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				//echo $ret_avg_rate= $avg_rate/$conversion_date;
				$currency_id=$receive_curr_array[$invRow[csf('prod_id')]];
				//echo $currency_id.'dddddd';
				if($receive_array[$invRow[csf('prod_id')]]>0)
				{
					//echo $currency_id.'D';
					if($currency_id==1) $rate=$receive_array[$invRow[csf('prod_id')]]/$currency_rate;//Taka
					else $rate=$receive_array[$invRow[csf('prod_id')]];	
				}
				else $rate=$avg_rate_array[$invRow[csf('prod_id')]]/$currency_rate; 
				
				$iss_ret_amnt=$issue_ret_qty*$rate;
				$retble_iss_ret_amnt=$returnable_ret_qnty*$rate;
				if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
				{
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($issue_purpose==1 || $issue_purpose==4) //Knit || Sample With Order
					{
						if($recv_purpose==16) //Grey Yarn
						{
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}
					else if($issue_purpose==2) //Yarn Dyeing
					{
						if($recv_purpose==16) //Grey Yarn
						{
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}
				}
				else //Short Fabric Booking
				{
					//echo $booking_type.'d';
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($issue_purpose==1 || $issue_purpose==4) // Knit || Sample With Order
					{
						if($recv_purpose==16) //Grey Yarn
						{
							//echo $iss_amnt.'cpa';
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}
					else  if($issue_purpose==2)
					{
						if($recv_purpose==16) //Grey Yarn
						{
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}	
				}
			}
			unset($yarnissueretDataArray);
				
			$yarnTrimsData="select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color
					from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25)  $po_cond_for_in4 ";
				
			$yarnTrimsDataArray=sql_select($yarnTrimsData); $yarnTrimsCostArray=array(); $i=0;
			foreach($yarnTrimsDataArray as $invRow)
			{
				if($invRow[csf('item_category')]==1)
				{
					$yarn_issue_amt=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['amt'];
					$yarn_issue_qty=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['qty'];
					$last_trans_date=$receive_date_array[$invRow[csf('prod_id')]]['last_trans_date'];
					
					if($invRow[csf('receive_basis')]==1)//Booking Basis
					{
						$booking_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						if($invRow[csf('issue_purpose')]==2) //Yarn Dyeing purpose
						{
							$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
							$booking_type=1;
							//echo $is_short.'= '.$booking_type.', ';
						}
						else
						{
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
					}
					else if($invRow[csf('receive_basis')]==3) //Requisition Basis
					{
						$booking_req_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						
						$prog_no=$reqs_array[$booking_req_no]['knit_id'];
						$booking_no=$plan_details_array[$prog_no];
						//echo $booking_no.'='.$prog_no.',';
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
					$transaction_date='';
					$transaction_date=$last_trans_date;
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					
					$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
					$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
					$avgrate=$issue_rate/$exchange_rate;
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($invRow[csf('entry_form')]==3 && $recv_purpose==16)//recv_purpose==16=Grey Yarn
					{
						if(($booking_type==1 || $booking_type==4) && $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
							{
								//echo $invRow[csf('mst_id')].'='.$recv_purpose.'='.$is_short.'='.$invRow[csf('quantity')].'-k<br>';
								$iss_amnt=$invRow[csf('quantity')]*$avgrate;
								//echo $iss_amnt.'m';
								$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_amt']+=$iss_amnt;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['yq']+=$invRow[csf('quantity')];
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_qty_retble']+=$invRow[csf('returnable_qnty')];
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_amt_retble']+=$retble_iss_amnt;
							}
						}
						else //Short Fabric Booking
						{
							$iss_amnt=$invRow[csf('quantity')]*$avgrate;
							//echo $is_short.'='.$iss_amnt.'='.$invRow[csf('mst_id')].'<br>';
							$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
							
							if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
							{
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_amt']+=$iss_amnt;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_qty_retble']+=$returnable_qnty;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['cpa_grey_yarn_amt_retble']+=$retble_iss_amnt;
							}
						}
					}
				}
				else
				{
					$trimsAmt=0;
					$trimsAmt=$invRow[csf('quantity')]*$invRow[csf('cons_rate')];
					$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$trimsAmt;
				}
			}
			unset($yarnTrimsDataArray);
					
			$sql_garments="select b.po_break_down_id as po_id,b.embel_name,b.production_date,b.production_type,
				(CASE WHEN b.embel_name=1 THEN b.production_quantity ELSE 0 END) AS printing_qty,
				(CASE WHEN b.embel_name=2 THEN b.production_quantity ELSE 0 END) AS embro_qty,
				(CASE WHEN b.embel_name=3 THEN b.production_quantity ELSE 0 END) AS wash_qty,
				(CASE WHEN b.embel_name=5 THEN b.production_quantity ELSE 0 END) AS dyeing_qty,
				(CASE WHEN b.embel_name=99 THEN b.production_quantity ELSE 0 END) AS others
				 from pro_garments_production_mst b where  b.production_type=2    and b.is_deleted=0 and b.status_active=1 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in";
				$result_gar=sql_select( $sql_garments );
				foreach ($result_gar as $row)
				{
					$embl_issue_prod_array[$row[csf('po_id')]]['printing_qty']+=$row[csf('printing_qty')];
					$embl_issue_prod_array[$row[csf('po_id')]]['embro_qty']+=$row[csf('embro_qty')];
					$embl_issue_prod_array[$row[csf('po_id')]]['wash_qty']+=$row[csf('wash_qty')];
					$embl_issue_prod_array[$row[csf('po_id')]]['dyeing_qty']+=$row[csf('dyeing_qty')];
					$embl_issue_prod_array[$row[csf('po_id')]]['others']+=$row[csf('others')];
				}
				unset($result_gar);
			
				//print_r($yarnTrimsCostArray);
				$recvIssue_array=array(); 
				$sql_trans="select b.trans_type, b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(80,82,81,83,84,13,94) and a.item_category=13 and a.transaction_type in(5,6) and b.trans_type in(5,6) $po_cond_for_in4 group by b.trans_type, b.po_breakdown_id";
				$result_trans=sql_select( $sql_trans );
				foreach ($result_trans as $row)
				{
					$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_trans);
				
				$gen_trims_issue_array=array(); 
				 $sql_gen_trims_inv="select b.transaction_date,(b.cons_amount) as cons_amount,b.order_id as po_id
				  from inv_issue_master  a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(21) and a.booking_no is not null  and b.transaction_type in(2)   $po_cond_for_in3 ";
				$result_gen_trims=sql_select( $sql_gen_trims_inv );
				foreach ($result_gen_trims as $row)
				{
					$transaction_date=$row[csf('transaction_date')];
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$gen_trims_issue_array[$row[csf('po_id')]]['amt']+=$row[csf('cons_amount')]/$currency_rate;//
				}
				unset($result_gen_trims);
				$trims_trans_array=array(); 
 				$sql_trims_inv_amt="SELECT b.po_breakdown_id as po_id, sum(b.quantity) as qnty,d.rate, d.item_group_id, d.order_uom from  order_wise_pro_details b,  inv_trims_entry_dtls d   where b.trans_id=d.trans_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.entry_form in(24) and b.trans_type in(1) $po_cond_for_in4 group by b.po_breakdown_id, d.rate, d.item_group_id, d.order_uom";
				$result_trims_amt=sql_select( $sql_trims_inv_amt );
				foreach ($result_trims_amt as $row)
				{
					$amt=0;
					$amt=$row[csf('qnty')]*$row[csf('rate')];
					$trims_trans_array[$row[csf('po_id')]]['amt']+=$amt;
				}
				
				//die;
				unset($result_trims_amt); 
				$sql_in = " SELECT a.transfer_date,a.to_order_id,SUM (b.transfer_qnty) AS transfer_qnty, SUM (b.transfer_value) AS transfer_amount FROM inv_item_transfer_mst a, inv_item_transfer_dtls b WHERE a.id = b.mst_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 $po_cond_for_in6 group by a.transfer_date,a.to_order_id"; 
				$sql_in_amt=sql_select($sql_in);
				foreach ($sql_in_amt as $row)
				{
					$transaction_date=$row[csf('transfer_date')];
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$amt=0;
					$amt=$row[csf('transfer_amount')];
					$trans_in_array[$row[csf('to_order_id')]]['amt']+=$amt/$currency_rate;
				}
				//echo $currency_rate;
				unset($sql_in_amt); 
				$sql_out = " SELECT a.transfer_date,a.from_order_id,SUM (b.transfer_qnty) AS transfer_qnty, SUM (b.transfer_value) AS transfer_amount FROM inv_item_transfer_mst a, inv_item_transfer_dtls b WHERE a.id = b.mst_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 $po_cond_for_in7 group by a.transfer_date,a.from_order_id"; 
				$sql_out_amt=sql_select($sql_out);
				foreach ($sql_out_amt as $row)
				{
					$transaction_date=$row[csf('transfer_date')];
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$amt=0;
					$amt=$row[csf('transfer_amount')];
					$trans_out_array[$row[csf('from_order_id')]]['amt']+=$amt/$currency_rate;
				}
				unset($sql_out_amt); 
				$sql_trims_inv="select a.transaction_date, a.cons_amount as cons_amount, b.po_breakdown_id as po_id, b.quantity as qnty, c.currency_id,b.order_rate, d.item_group_id, d.order_uom from inv_transaction a, order_wise_pro_details b, inv_receive_master c, inv_trims_entry_dtls d  where a.id=b.trans_id and c.id=a.mst_id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.entry_form in(24) and a.item_category=4 and a.transaction_type in(1) and b.trans_type in(1) $po_cond_for_in4";
				$result_trims=sql_select( $sql_trims_inv );
				foreach ($result_trims as $row)
				{
					$transaction_date=$row[csf('transaction_date')];
					$currency_id=$row[csf('currency_id')];
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($currency_id,$conversion_date );
					$amt=0;
					$amt=$row[csf('qnty')]*$row[csf('order_rate')];
					//$trims_trans_array[$row[csf('po_id')]]['amt']+=$amt;
				}
				
				//die;
				unset($result_trims);
				$fin_fab_trans_array=array(); 
				 $sql_fin_trans="select b.trans_type, b.po_breakdown_id, sum(b.quantity) as qnty
				  from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(15,14) and a.item_category=2 and a.transaction_type in(5,6) and b.trans_type in(5,6) $po_cond_for_in4 group by b.trans_type, b.po_breakdown_id";
				$result_fin_trans=sql_select( $sql_fin_trans );
				foreach ($result_fin_trans as $row)
				{
					$fin_fab_trans_array[$row[csf('po_breakdown_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_fin_trans);
				
				if($poIds!='' || $poIds!=0)
				{
					$po_cond_for_in5=$po_cond_for_in5; 
				}
				else
				{
					$po_cond_for_in5="and b.po_id in(0)";
				}
				$batch_trans_array=array(); 
				$sql_batch="select a.id,a.booking_no,batch_weight as batch_weight
				  from  pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in5 group by a.id,batch_weight,a.booking_no";
				$result_batch=sql_select( $sql_batch );
				foreach ($result_batch as $row)
				{
					$batch_trans_array[$row[csf('id')]]['book']=$row[csf('booking_no')];
					$batch_weight_array[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_batch);
				
				$dye_fin_fab_array=array(); 
				 $sql_fin_trans="select a.receive_date, b.po_breakdown_id,c.mst_id,c.batch_id,c.body_part_id, sum(b.quantity) as finish_qnty  from inv_receive_master a,pro_finish_fabric_rcv_dtls c, order_wise_pro_details b where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(7) and a.item_category=2   and b.trans_type in(1) $po_cond_for_in4 group by a.receive_date, b.po_breakdown_id,c.batch_id,c.mst_id,c.body_part_id";
				$result_fin_trans=sql_select( $sql_fin_trans );
				foreach ($result_fin_trans as $row)
				{
						$receive_date=$row[csf('receive_date')];
						$booking_no=$batch_trans_array[$row[csf('batch_id')]]['book'];
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
						$batch_weight=$batch_weight_array[$row[csf('batch_id')]]['batch_weight'];
						$dye_finish_charge=$prodcostArray[$row[csf('po_breakdown_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['dye_finish_charge'];
						//echo $batch_weight.'kk';
						if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							
							$finish_qnty=$row[csf('finish_qnty')]*$dye_finish_charge;
						}
						else //short Fab. booking 
						{
							$cpa_batch_weight=$batch_weight*$dye_finish_charge;
						}
						//echo $finish_qnty.'='.$cpa_finish_qnty;
						if($db_type==0)
						{
							$conversion_date=change_date_format($receive_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($receive_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						$dye_fin_fab_array[$row[csf('po_breakdown_id')]]['fin_amt']=$finish_qnty;
						$dye_fin_fab_array[$row[csf('po_breakdown_id')]]['cpa_batch_weight']+=$cpa_batch_weight;
						$dye_fin_fab_array[$row[csf('po_breakdown_id')]]['ex_rate']=$currency_rate;
				}
				unset($result_fin_trans);
				
				$grey_fab_array=array();  //Knitting Cost actual 
				  $sql_grey_trans="select  a.id,a.receive_basis,a.booking_no,a.receive_date,b.po_breakdown_id,c.febric_description_id as deter_id,c.body_part_id, c.kniting_charge,
				 (CASE WHEN b.entry_form =2 and b.trans_type in(1) and a.item_category=13  THEN  b.quantity ELSE 0 END) AS grey_qnty
				
				  from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and a.item_category in(2,13)  and b.trans_type in(1) $po_cond_for_in4";
				$result_grey=sql_select( $sql_grey_trans );
				$currency_id=1;
				foreach ($result_grey as $row)
				{
						$transaction_date=$row[csf('transaction_date')];
						$receive_basis=$row[csf('receive_basis')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($row[csf('receive_date')], "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($row[csf('receive_date')], "d-M-y", "-",1);
						}
						$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
						if($currency_id==1) //TK
						{
							$knit_charge=$row[csf('kniting_charge')]/$exchange_rate;
							
						}
						else
						{
							$knit_charge=$row[csf('kniting_charge')];
						}
						
						//$knit_charge=$row[csf('kniting_charge')];//$prodcostArray[$row[csf('po_breakdown_id')]][$row[csf('deter_id')]][$row[csf('body_part_id')]]['knit_charge'];
						//echo $knit_charge.'='.$row[csf('deter_id')].'ff'.$row[csf('po_breakdown_id')];
						if($receive_basis==1) //Booking Basis
						{
							 $booking_no=$row[csf('booking_no')];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							//echo $booking_type.'='.$is_short;
						}
						else if($receive_basis==2) //Knit plan Basis
						{
							 $prog_no=$grey_knit_array[$row[csf('mst_id')]];
							$booking_no=$plan_details_array[$row[csf('booking_no')]];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
						else if($receive_basis==4) //Salse Basis
						{
							 $prog_no=$row[csf('booking_no')];
							$booking_no=$plan_details_array[$prog_no];
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							
						}
						
						if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							$grey_amt=$row[csf('grey_qnty')]*$knit_charge;
							//$finish_qnty=$row[csf('finish_qnty')];
							//echo $row[csf('grey_qnty')].'*'.$knit_charge.'<br>';
						}

						else //short Fab. booking 
						{
							$cpa_grey_amt=$row[csf('grey_qnty')]*$knit_charge;
							//$cpa_finish_qnty=$row[csf('finish_qnty')];
						}
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$grey_fab_array[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('grey_qnty')];
					$grey_fab_array[$row[csf('po_breakdown_id')]]['grey_amt']+=$grey_amt;
					$grey_fab_array[$row[csf('po_breakdown_id')]]['cpa_grey_amt']+=$cpa_grey_amt;
					
				}
				unset($result_grey);
				//print_r($grey_fab_array);
				$sql_fin_purchase="select b.po_breakdown_id,a.transaction_date, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category in (2) and a.transaction_type=1 and b.entry_form in (37) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id,a.transaction_date";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					$transaction_date=$finRow[csf('transaction_date')];
					if($db_type==0)
					{
						$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					}
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					
					$usdFinPurAmt=0;
					$usdFinPurAmt=$finRow[csf('finish_purchase_amnt')]/$currency_rate;
					//echo $usd_id.'='.$conversion_date.'='.$currency_rate.'='.$usdFinPurAmt.'<br>';
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$usdFinPurAmt;
				}
				unset($dataArrayFinPurchase);
				//print_r($finish_purchase_amnt_arr);
				//$ex_rate=76;
				$sql_fin_purchase_wv="SELECT c.currency_id,c.exchange_rate,b.po_breakdown_id, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b, inv_receive_master c where a.id=b.trans_id and c.id=a.mst_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in4 group by b.po_breakdown_id,c.currency_id,c.exchange_rate";
				$dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
				foreach($dataArrayFinPurchaseW as $finRow)
				{
					$ex_rate=$finRow[csf('exchange_rate')];
					if($finRow[csf('currency_id')]!=1){
						$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')]/$ex_rate;
					}
					else{
						$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')];
					}
					
				}
				unset($dataArrayFinPurchaseW);
				//print_r($finish_purchase_amnt_arr);
				$sql_grey_purchase="select b.po_breakdown_id,a.transaction_date, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category in(13,14) and a.transaction_type=1 and b.entry_form in(22,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id,a.transaction_date";
				$dataArraygreyPurchase=sql_select($sql_grey_purchase);
				foreach($dataArraygreyPurchase as $gRow)
				{
					$transaction_date=$gRow[csf('transaction_date')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					$grey_purchase_amnt_arr[$gRow[csf('po_breakdown_id')]]=$gRow[csf('finish_purchase_amnt')]/$currency_rate;
				}
					unset($dataArraygreyPurchase);
				 $actualCostArray=array();
				$actualCostDataArray="select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 $po_cond_for_in2 group by cost_head,po_id";
				$actual_results=sql_select($actualCostDataArray);
				foreach($actual_results as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
				unset($actual_results);
				
				$i=1; $tot_po_qnty=0; $tot_po_value=0; $tot_ex_factory_qnty=0; $tot_ex_factory_val=0; $tot_yarn_cost_mkt=0; $tot_knit_cost_mkt=0; $tot_dye_finish_cost_mkt=0; $tot_yarn_dye_cost_mkt=0; $tot_aop_cost_mkt=0; $tot_trims_cost_mkt=0; $tot_embell_cost_mkt=0; $tot_wash_cost_mkt=0; $tot_commission_cost_mkt=0; $tot_comm_cost_mkt=0; $tot_freight_cost_mkt=0; $tot_test_cost_mkt=0; $tot_inspection_cost_mkt=0; $tot_currier_cost_mkt=0; $tot_cm_cost_mkt=0; $tot_mkt_all_cost=0; $tot_mkt_margin=0; $tot_yarn_cost_actual=0; $tot_knit_cost_actual=0; $tot_dye_finish_cost_actual=0; $tot_yarn_dye_cost_actual=0; $tot_aop_cost_actual=0; $tot_trims_cost_actual=0; $tot_embell_cost_actual=0; $tot_wash_cost_actual=0; $tot_commission_cost_actual=0; $tot_comm_cost_actual=0; $tot_freight_cost_actual=0; $tot_test_cost_actual=0; $tot_inspection_cost_actual=0; $tot_currier_cost_actual=0; $tot_cm_cost_actual=0; $tot_actual_all_cost=0; $tot_actual_margin=0; $tot_fabric_purchase_cost_mkt=$total_fabric_cost_mkt=0; $tot_fabric_purchase_cost_actual=$tot_grey_fab_cost_actual=$tot_actual_net_margin=0;
				$tot_print_amount_mkt=$tot_embro_amount_mkt=$tot_dyeing_amount_mkt=$tot_wash_amount_mkt=$tot_print_amount_actual=$tot_embro_amount_actual=$tot_dyeing_amount_actual=$tot_wash_amount_actual=$tot_finished_fab_cost_actual=$tot_fin_fab_transfer_amt_actual=$tot_yarn_dyeing_twist_actual=$tot_grey_fab_transfer_amt_actual=$tot_yarn_qty_actual=$tot_yarn_amt_actual=$tot_grey_fab_cost_mkt=$tot_yarn_value_addition_cost_mkt=$total_fabric_cost=$tot_fabric_cost_actaul=$tot_yarn_qty_cpa_actual=$total_finish_fabric_cost_mkt=$tot_yarn_qty_cpa_actual=0;
				$buyer_name_array=array();
				$mkt_po_val_array = array(); $mkt_yarn_array = array(); $mkt_knit_array = array(); $mkt_dy_fin_array = array(); $mkt_yarn_dy_array = array();
				$mkt_aop_array = array(); $mkt_trims_array = array(); $mkt_emb_array = array(); $mkt_wash_array=array(); $mkt_commn_array=array(); $mkt_commercial_array=array();
				$mkt_freight_array = array(); $mkt_test_array = array(); $mkt_ins_array = array(); $mkt_courier_array = array(); $mkt_cm_array = array();
				$mkt_total_array = array(); $mkt_margin_array = array(); $mkt_fabric_purchase_array = array();
				
				$ex_factory_val_array= array(); $yarn_cost_array= array(); $knit_cost_array= array(); $dye_cost_array= array(); $yarn_dyeing_cost_array= array();
				$aop_n_others_cost_array= array(); $trims_cost_array= array(); $enbellishment_cost_array= array(); $wash_cost_array= array(); $commission_cost_array= array(); 
				$commercial_cost_array= array(); $freight_cost_array= array(); $testing_cost_array= array(); $inspection_cost_array= array(); $courier_cost_array= array();
				$cm_cost_array= array(); $total_cost_array= array(); $margin_array= array(); $ex_factory_array=array(); $actual_cm_amnt=array(); $actual_fabric_purchase_array = array();
								
				 $condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if(str_replace("'","",$txt_file_no) !=''){
					  $condition->file_no("='$txt_file_no'");
				 }
				 if(str_replace("'","",$txt_ref_no) !=''){
					  $condition->grouping("='$txt_ref_no'");
				 }
				 
				 if(str_replace("'","",$txt_job_no) !=''){
					  $condition->job_no_prefix_num("=$txt_job_no");
				 }
				 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				 {
					 $start_date=str_replace("'","",$txt_date_from);
					 $end_date=str_replace("'","",$txt_date_to);
					 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					 //and b.po_received_date between '$start_date' and '$end_date' 
					// echo 'FFGG';
				 }
				 if(trim($poIds)!='')
				 {
					//$condition->po_number(" like '%".trim(str_replace("'","",$txt_order_no))."%'"); 
					$condition->po_id_in($poIds); 
				 }
				 
				 
				 $condition->init();
				
				 $yarn= new yarn($condition);
				//echo $yarn->getQuery();die;
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $yarn= new yarn($condition);
				 $yarn_costing_qty_arr=$yarn->getOrderWiseYarnQtyArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess(); 
				 $conversion= new conversion($condition);
				 
				 $conversion_costing_arr_process_qty=$conversion->getQtyArray_by_orderAndProcess();
			
				$trims= new trims($condition);
				$trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name_qty=$emblishment->getQtyArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash_qty=$wash->getQtyArray_by_orderAndEmbname();
				$fabric= new fabric($condition);
				 //echo $fabric->getQuery();
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				$usd_id=2;//===============Dollar========
				//print_r($fabric_costing_arr);die;
				$not_yarn_dyed_cost_arr=array(1,2,30,134,35);$job_ids='';

				foreach($result as $row)
				{
					$order_arr[$row[csf('id')]]=$row[csf('id')];
				}

	//============================================SC No===============================================
	$sales_contract_sql=sql_select("SELECT a.contract_no,b.wo_po_break_down_id from com_sales_contract a join com_sales_contract_order_info b on a.id=b.com_sales_contract_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($order_arr,1,' b.wo_po_break_down_id')." group by a.contract_no, b.wo_po_break_down_id");
	$sales_contract_arr=array();
	if(count($sales_contract_sql)>0)
	{
		foreach ($sales_contract_sql as $key => $row) {
			$sales_contract_arr[$row[csf('wo_po_break_down_id')]]=$row[csf('contract_no')];
		}
	}
	
	/* $sales_contract_arr=array();
	if(count($sales_contract_sql)>0)
	{
		foreach ($sales_contract_sql as $key => $row) {
			$sales_contract_arr[$row[csf('wo_po_break_down_id')]].=$row[csf('contract_no')].',';
		}
	}
	$sc_lc_no="";
	if(count($sales_contract_arr)>0)
	{
		$sc_lc_no=implode(",", $sales_contract_arr);
	}
		$sc_lc_no=rtrim($sc_lc_no,',');
		$sc_lc_no=implode(",",array_unique(explode(",",$sc_lc_no))); */
	//============================================SC NO===============================================
	 //============================================knit inbound bill issue===============================================
	$knit_in_bill_sql="select b.id as upd_id,  b.process_id,b.rate, b.amount, b.order_id, b.currency_id ,b.delivery_qty,a.bill_date from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.id=b.mst_id and   a.status_active=1 and a.is_deleted=0 ".where_con_using_array($order_arr,0,'b.order_id')."  order by b.challan_no ASC";
	 //echo  $knit_in_bill_sql;
	 $knit_in_bill_data=sql_select($knit_in_bill_sql);
	 foreach($knit_in_bill_data as $kival){
		if($kival[csf('process_id')]==2){
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
			
		}else{
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
			$order_wise_dye_bill_rate[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
			$order_wise_knit_bill_data[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
		}
		$order_wise_dye_bill_currencyArr[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
	 }
		
	  //============================================knit outbound bill issue/gross same soucre===============================================
	   //$knit_out_bill_sql="SELECT id as dtls_id, receive_id,wo_num_id, receive_date, challan_no, order_id, roll_no, body_part_id, febric_description_id, item_id as prod_id, receive_qty, rec_qty_pcs, uom, rate, amount, remarks,currency_id,process_id FROM subcon_outbound_bill_dtls WHERE  process_id in (2,4) and status_active=1 and is_deleted=0 ".where_con_using_array($order_arr,0,'order_id')." order by id asc";
	   $knit_out_bill_sql="SELECT a.bill_date, b.id as dtls_id, b.receive_id, b.wo_num_id, b.receive_date, b.challan_no, b.order_id, b.roll_no, b.body_part_id, b.febric_description_id, b.item_id as prod_id, b.receive_qty, b.rec_qty_pcs, b.uom, b.rate, b.amount, b.remarks,b.currency_id,b.process_id FROM subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and b.process_id in (2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($order_arr,0,'b.order_id')." order by b.id asc";
	  $knit_out_bill_data=sql_select($knit_out_bill_sql);
	  foreach($knit_out_bill_data as $koval){
			if($koval[csf('process_id')]==2){
				$order_wise_knit_bill_data[$koval[csf('order_id')]]['rate']=$koval[csf('rate')];
				$order_wise_knit_bill_data[$koval[csf('order_id')]]['delivery_qty']+=$koval[csf('receive_qty')];
				$order_wise_knit_bill_data[$koval[csf('order_id')]]['amount']+=$koval[csf('amount')];
				$order_wise_knit_bill_data[$koval[csf('order_id')]]['bill_date']=$koval[csf('bill_date')];
				$order_wise_knit_bill_data[$koval[csf('order_id')]]['currency_id']=$koval[csf('currency_id')];
				$order_wise_dye_bill_rateArr[$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('rate')];
			}else{
				$order_wise_dye_bill_rate[$koval[csf('order_id')]]['rate']=$koval[csf('rate')];
				$order_wise_dye_bill_rateArr[$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('rate')];
			}
			$order_wise_dye_bill_currencyArr[$koval[csf('order_id')]]['currency_id']=$koval[csf('currency_id')];
	  }

	  $knit_inbound_bill_sql="select a.bill_date,b.currency_id,b.rate, b.amount, b.order_id,b.delivery_qty,(b.rate*b.delivery_qty) as qnty from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.id=b.mst_id and b.process_id=2 and a.status_active=1 and a.is_deleted=0  ".where_con_using_array($order_arr,0,'b.order_id')."";
	  $knit_inbound_bill_data=sql_select($knit_inbound_bill_sql);
			foreach($knit_inbound_bill_data as $kival){
					$currency_id=$kival[csf('currency_id')];
					$knit_inbound_bill_data_array[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
					$usd_id=2;
					$conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
					$knit_inbound_bill_data_array[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
					$knit_inbound_bill_data_array[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
					$knit_inbound_bill_data_array[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
					$knit_inbound_bill_data_array[$kival[csf('order_id')]]['subcontact_qnty']+=$kival[csf('qnty')]/$currency_rate;
					$bill_date=$knit_inbound_bill_data_array[$kival[csf('order_id')]]['bill_date'];
					$transfer_value=$knit_inbound_bill_data_array[$kival[csf('order_id')]]['amount'];
					if($currency_id==2) $transfer_value=$transfer_value;//USD
					else $transfer_value=$transfer_value/$currency_rate;
			}
			if(count($knit_inbound_bill_data)>0){
				foreach($knit_inbound_bill_data as $row){
					$amount_knit=0;
					$rate=$knit_inbound_bill_data_array[$row[csf('order_id')]]['rate'];
					$amount=$knit_inbound_bill_data_array[$row[csf('order_id')]]['amount'];
					$delivery_qty=$knit_inbound_bill_data_array[$row[csf('order_id')]]['delivery_qty'];
					$amount_knit=$rate*$delivery_qty;
					$total_in_knit_cost_usd+=$amount_knit;
				}
			}		
			unset($knit_inbound_bill_data);
			//echo $transfer_value.'<br>';
			
			//============================================knit outbound bill issue/gross same soucre===============================================
			$knit_outbound_bill_sql="SELECT b.currency_id,a.bill_date, b.id as dtls_id, b.receive_id, b.wo_num_id, b.receive_date, b.challan_no, b.order_id, b.roll_no, b.body_part_id, b.febric_description_id, b.item_id, b.receive_qty, b.rec_qty_pcs, b.uom, b.rate, b.amount, b.remarks,b.currency_id,(b.rate*b.receive_qty) as qnty FROM subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($order_arr,0,'b.order_id')."";
			$knit_outbound_bill_data=sql_select($knit_outbound_bill_sql);
			foreach($knit_outbound_bill_data as $koval){
				$currency_id=$kival[csf('currency_id')];
					$knit_inbound_bill_data_array[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
					$usd_id=2;
					$conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
					$knit_outbound_bill_data_array[$koval[csf('order_id')]]['rate']=$koval[csf('rate')];
					$knit_outbound_bill_data_array[$koval[csf('order_id')]]['receive_qty']+=$koval[csf('receive_qty')];
					$knit_outbound_bill_data_array[$koval[csf('order_id')]]['amount']+=$koval[csf('amount')];		
					$knit_outbound_bill_data_array[$koval[csf('order_id')]]['subcontact_qnty']+=$koval[csf('qnty')]/$currency_rate;
					$bill_date=$knit_outbound_bill_data_array[$koval[csf('order_id')]]['bill_date'];
					$transfer_out_value=$knit_outbound_bill_data_array[$koval[csf('order_id')]]['amount'];
			}
			//echo '<pre>';print_r($knit_outbound_bill_data_array);die;
			if(count($knit_outbound_bill_data)>0){
				foreach($knit_outbound_bill_data as $row){
					$amount_out_knit=0;
					$rate=$knit_outbound_bill_data_array[$row[csf('order_id')]]['rate'];
					$amount=$knit_outbound_bill_data_array[$row[csf('order_id')]]['amount'];
					$receive_qty=$knit_outbound_bill_data_array[$row[csf('order_id')]]['receive_qty'];
					$amount_out_knit=$receive_qty*$rate;
					 $total_out_knit_cost_usd+=$amount_out_knit;
				}
			}

			unset($knit_out_bill_data);
			//echo $transfer_out_value;

	  $actual_knitting_cost_surma=($transfer_value+$transfer_out_value);

	  //=======================================Dye & Fin Cost=====================================================	 
	 /* $finProd_grey_used_sql=sql_select("SELECT a.company_id,a.receive_purpose,a.recv_number,a.receive_date,a.location_id,a.booking_no as non_order_booking, a.booking_id,a.receive_basis,b.prod_id, b.batch_id,b.fabric_description_id as deter_id, b.receive_qnty, b.reject_qty,b.bin,e.po_breakdown_id as po_id,e.quantity, b.batch_id,b.order_id,e.grey_used_qty,b.job_no,b.booking_no,b.booking_id booking_no_id from inv_receive_master a, inv_transaction c,pro_finish_fabric_rcv_dtls b,order_wise_pro_details e  where a.id=c.mst_id and c.id=b.trans_id  and b.id=e.dtls_id and e.trans_id=c.id  and a.item_category=2 and a.entry_form=37 and e.entry_form=37 and a.status_active=1 and e.status_active=1 and c.status_active=1 and b.status_active=1 ".where_con_using_array($order_arr,0,'e.po_breakdown_id')." ");
	  
	  
	   foreach($finProd_grey_used_sql as $fval){
			$currency_id=$order_wise_dye_bill_currencyArr[$fval[csf('po_id')]]['currency_id'];
			$dye_rate=$order_wise_dye_bill_rateArr[$fval[csf('po_id')]][$fval[csf('prod_id')]]['rate'];
			$usd_id=2;
			$bill_date=$order_wise_knit_bill_data[$fval[csf('po_id')]]['bill_date'];
			if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
			else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
			$currency_rate=set_conversion_rate($usd_id,$conversion_date );
			if($currency_id==2) $dye_rate=$dye_rate;//USD
			else $dye_rate=$dye_rate/$currency_rate;
			$order_wise_fin_fab_rec[$fval[csf('po_id')]]['used_qty']+=$fval[csf('grey_used_qty')]*$dye_rate;
	 	}*/
	  $knit_in_bill_sql="	select a.bill_no,b.id as upd_id,b.item_id as prod_id,b.challan_no, b.process_id,b.rate, b.amount, b.order_id, b.currency_id ,b.add_rate,b.delivery_qty,a.bill_date from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($order_arr,0,'b.order_id')."   and b.process_id in (4)  and b.add_process_name!='35' order by a.bill_no ASC";
		
			// echo  $knit_in_bill_sql;die;
			$knit_in_bill_data=sql_select($knit_in_bill_sql);
			foreach($knit_in_bill_data as $kival){
				
				$dye_finish_charge=$kival[csf('amount')]/$kival[csf('delivery_qty')];
				$currency_id=$kival[csf('currency_id')];//$order_wise_dye_bill_rate[$row[csf('po_id')]]['currency_id'];
				$bill_date=$kival[csf('bill_date')];
				
				$usd_id=2;
				if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				if($currency_id==2) $dye_finish_charge=$dye_finish_charge;//USD
				else $dye_finish_charge=$dye_finish_charge/$currency_rate;
				$used_amount=$kival[csf('delivery_qty')]*$dye_finish_charge;
				$order_wise_fin_fab_rec[$kival[csf('order_id')]]['used_qty']+=$used_amount;
				
			}
			
			//============================================knit outbound bill issue/gross same soucre===============================================
			   $knit_out_bill_sql="SELECT a.bill_no,b.id as dtls_id,b.batch_id, b.receive_id,b.wo_num_id, b.receive_date, b.challan_no, b.order_id, b.roll_no, b.body_part_id, b.febric_description_id, b.item_id as prod_id, b.receive_qty, b.rec_qty_pcs, b.uom, b.rate, b.amount, b.remarks,b.currency_id,b.process_id FROM subcon_outbound_bill_dtls b,subcon_outbound_bill_mst a WHERE  a.id=b.mst_id and b.process_id in (4) and (b.sub_process_id!=35  or b.sub_process_id is null) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($order_arr,0,'b.order_id')."  order by a.bill_no asc";
			   //echo "SELECT a.bill_no,b.id as dtls_id,b.batch_id, b.receive_id,b.wo_num_id, b.receive_date, b.challan_no, b.order_id, b.roll_no, b.body_part_id, b.febric_description_id, b.item_id as prod_id, b.receive_qty, b.rec_qty_pcs, b.uom, b.rate, b.amount, b.remarks,b.currency_id,b.process_id FROM subcon_outbound_bill_dtls b,subcon_outbound_bill_mst a WHERE  a.id=b.mst_id and b.process_id in (4) and b.sub_process_id!= 35 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($order_arr,0,'b.order_id')."   order by a.bill_no asc";
			$knit_out_bill_data=sql_select($knit_out_bill_sql);
			foreach($knit_out_bill_data as $koval){
					 
					$dye_finish_charge=$koval[csf('amount')]/$koval[csf('receive_qty')];
					$currency_id=$koval[csf('currency_id')];//$order_wise_dye_bill_rate[$row[csf('po_id')]]['currency_id'];
					$bill_date=$koval[csf('bill_date')];
						//echo $koval[csf('receive_qty')].'='.$dye_finish_charge.'<br>';
					$usd_id=2;
					if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
					if($currency_id==2) $dye_finish_charge=$dye_finish_charge;//USD
					else $dye_finish_charge=$dye_finish_charge/$currency_rate;
					
				$dye_finish_charge=number_format($dye_finish_charge,4,'.','');
				//echo $koval[csf('receive_qty')].'='.$dye_finish_charge.'<br>'; 
					$used_amount=$koval[csf('receive_qty')]*$dye_finish_charge;
					//echo $currency_id.'='.$dye_finish_charge.'='.$used_amount.'<br>';
					$order_wise_fin_fab_rec[$koval[csf('order_id')]]['used_qty']+=$used_amount;
					
			}
			//print_r($order_wise_fin_fab_rec);
	  



	  
	 //================================================================================================================
		$finis_fab_dat=sql_select("select a.id, a.from_order_id, c.po_number, a.batch_id, b.batch_no, a.from_store, a.to_store, a.from_prod_id, a.color_id,sum(a.transfer_qnty) transfer_qnty ,c.id as poid	from inv_item_transfer_dtls a, pro_batch_create_mst b , wo_po_break_down c where  a.batch_id = b.id and a.from_order_id = c.id and a.status_active = '1' and a.is_deleted = '0' and a.active_dtls_id_in_transfer=1  ".where_con_using_array($order_arr,1,'c.id')." group by a.id, a.from_order_id, c.po_number, a.batch_id, b.batch_no, a.from_store, a.to_store, a.from_prod_id, a.color_id,c.id");


				foreach($finis_fab_dat as $val){
					$order_wise_fin_fab_rec[$val[csf('poid')]]['transfer_qnty']+=$val[csf('transfer_qnty')];
				}



				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$po_ids_array[]=$row[csf('id')];
					$job_ids.=$row[csf('job_no')].',';
					$sc_lc_no=$sales_contract_arr[$row[csf('id')]];
					$gmts_item='';
					$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
					foreach($gmts_item_id as $item_id)
					{
						if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
					}
					

					if($order_wise_knit_bill_data[$row[csf('id')]]['amount'])
					{
					$knit_rate=$order_wise_knit_bill_data[$row[csf('id')]]['amount']/$order_wise_knit_bill_data[$row[csf('id')]]['delivery_qty'];
					}
					else  $knit_rate=0;
					
					$bill_date=$order_wise_knit_bill_data[$row[csf('id')]]['bill_date'];

					$currency_id=$order_wise_knit_bill_data[$row[csf('id')]]['currency_id'];
					$usd_id=2;
					if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					if($currency_id==2) $knit_rate=$knit_rate;//USD
					else $knit_rate=$knit_rate/$currency_rate;

					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$yds_order_qnty_in_pcs=$row[csf('po_quantity')];
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
					$po_value=$order_qnty_in_pcs*$unit_price;
					
					$tot_po_qnty+=$order_qnty_in_pcs; 
					$tot_po_value+=$po_value;
					//echo "DDD";die;
					$ex_factory_qty=$ex_factory_arr[$row[csf('id')]];
					$ex_factory_value=$ex_factory_qty*$unit_price;
					
					$tot_ex_factory_qnty+=$ex_factory_qty; 
					$tot_ex_factory_val+=$ex_factory_value; 
					
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
					
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					$yarn_cost_mkt=$yarn_costing_arr[$row[csf('id')]];
					 $yarn_cost_mkt_qty=$yarn_costing_qty_arr[$row[csf('id')]];
					$knit_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][1]);//($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					$knit_qty_mkt=array_sum($conversion_costing_arr_process_qty[$row[csf('id')]][1]);
					//$yarn_dyeing_cost_mkt+=array_sum($conversion_costing_arr_process[$row[csf('id')]][30]);
					$yarn_dyeing_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][30]);
					//if($yarn_dyeing_cost_mkt) $yarn_dyeing_cost_mkt=$yarn_dyeing_cost_mkt;else $yarn_dyeing_cost_mkt=0;
					$twisting_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][134]);
					//if($twisting_cost_mkt) $twisting_cost_mkt=$twisting_cost_mkt;else $twisting_cost_mkt=0;
					$yarn_value_addition_cost_mkt=$yarn_dyeing_cost_mkt+$twisting_cost_mkt;
					//echo $yarn_dyeing_cost_mkt.'=='.$twisting_cost_mkt.', ';
				
					if($knit_cost_mkt>0)
					{
					$knit_charge_mkt=$knit_cost_mkt/$knit_qty_mkt;//$dye_finish_cost_mkt_qty
					}
					
					$dye_finish_cost_mkt=0;$dye_finish_cost_mkt_qty=0;
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$row[csf('id')]][$process_id]);
							
							$dye_finish_cost_mkt_qty+=array_sum($conversion_costing_arr_process_qty[$row[csf('id')]][$process_id]);
						}
					}	
					if($dye_finish_cost_mkt>0)
					{			
					$knit_charge_mkt_fin=$dye_finish_cost_mkt/$dye_finish_cost_mkt_qty;
					}
					//echo $dye_finish_cost_mkt.'='.$dye_finish_cost_mkt_qty;
					//$yarn_dye_cost_mkt=$conversion_costing_arr_process[$row[csf('id')]][30];
					$aop_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][35]);//($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					$trims_cost_mkt=$trims_costing_arr[$row[csf('id')]];//($order_qnty_in_pcs/$dzn_qnty)*$trimsCostArray[$row[csf('id')]];
					
					$print_amount=$emblishment_costing_arr_name[$row[csf('id')]][1];
					$print_qty=$emblishment_costing_arr_name_qty[$row[csf('id')]][1];
					if($print_amount) $print_amount=$print_amount;else $print_amount=0;
					if($print_qty) $print_qty=$print_qty;else $print_qty=0;
					if($print_amount==0 || $print_qty==0) $print_avg_rate=0;
					else $print_avg_rate=$print_amount/$print_qty;
						
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('id')]][2];
					$embroidery_qty=$emblishment_costing_arr_name_qty[$row[csf('id')]][2];								
					if($embroidery_amount) $embroidery_amount=$embroidery_amount;else $embroidery_amount=0;
					if($embroidery_qty) $embroidery_qty=$embroidery_qty;else $embroidery_qty=0;
					if($embroidery_amount==0 || $embroidery_qty==0) $embro_avg_rate=0;
					else $embro_avg_rate=$embroidery_amount/$embroidery_qty;
				
					$others_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$others_qty=$emblishment_costing_arr_name_qty[$row[csf('id')]][5];
					if($other_amount) $others_amount=$others_amount;else $others_amount=0;
					if($others_qty) $others_qty=$others_qty;else $others_qty=0;
					if($others_amount==0 || $others_amount==0) $others_amount=0;
					else $others_avg_rate=$others_amount/$others_qty;

					//$special_amount=$emblishment_costing_arr_name[$row[csf('id')]][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('id')]][3];
					$wash_qty=$emblishment_costing_arr_name_wash_qty[$row[csf('id')]][3];
					if($wash_cost) $wash_cost=$wash_cost;else $wash_cost=0;
					if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
					if($wash_cost==0 || $wash_qty==0) $wash_avg_rate=0;
					else $wash_avg_rate=$wash_cost/$wash_qty;
					
					$other_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$dyeing_qty=$emblishment_costing_arr_name_qty[$row[csf('id')]][5];
					if($other_amount) $other_amount=$other_amount;else $other_amount=0;
					if($dyeing_qty) $dyeing_qty=$dyeing_qty;else $dyeing_qty=0;
					if($other_amount==0 || $dyeing_qty==0) $dyeing_avg_rate=0;
					else $dyeing_avg_rate=$other_amount/$dyeing_qty;
					
					$foreign_cost=$commission_costing_arr[$row[csf('id')]][1];
					$local_cost=$commission_costing_arr[$row[csf('id')]][2];
					
					$test_cost=$other_costing_arr[$row[csf('id')]]['lab_test'];
					$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];
					$inspection_cost=$other_costing_arr[$row[csf('id')]]['inspection'];
					$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];
					//$common_oh_cost=$other_costing_arr[$row[csf('id')]]['common_oh'];
					$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
					$cm_cost=$other_costing_arr[$row[csf('id')]]['cm_cost'];
					$embell_cost_mkt=$print_amount+$embroidery_amount+$wash_cost+$other_amount;
					
					
					$wash_cost_mkt=$wash_cost;
					$commission_cost_mkt=$foreign_cost+$local_cost;
					$comm_cost_mkt=$commercial_costing_arr[$row[csf('id')]];
					$freight_cost_mkt=$freight_cost;
					$test_cost_mkt=$test_cost;
					$inspection_cost_mkt=$inspection_cost;
					$currier_cost_mkt=$currier_cost;
					$cm_cost_mkt=$cm_cost;
						//echo "MM";die;
					
					$fabric_purchase_cost=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]]);
					//+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					$fabric_purchase_cost2=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					$fabric_purchase_cost_mkt=$fabric_purchase_cost+$fabric_purchase_cost2;
					$finish_fabric_purchase_cost=array_sum($fabric_costing_arr['knit']['finish'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['finish'][$row[csf('id')]]);
					$finish_fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['finish'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['finish'][$row[csf('id')]]);
					$grey_fab_cost_mkt=$yarn_cost_mkt+$yarn_value_addition_cost_mkt+$knit_cost_mkt;
					$mkt_all_cost=$yarn_cost_mkt+$yarn_value_addition_cost_mkt+$knit_cost_mkt+$dye_finish_cost_mkt+$aop_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$commission_cost_mkt+$comm_cost_mkt+$freight_cost_mkt+$test_cost_mkt+$inspection_cost_mkt+$currier_cost_mkt+$cm_cost_mkt+$fabric_purchase_cost_mkt;
					
					$mkt_margin=$po_value-$mkt_all_cost;
					$mkt_margin_perc=($mkt_margin/$po_value)*100;
					$tot_finish_fabric_cost_mkt=$grey_fab_cost_mkt+$dye_finish_cost_mkt+$aop_cost_mkt;
					
					//$yarn_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$yarncostArray[$row[csf('job_no')]];
					//$knit_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					//$dye_finish_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];
					//$yarn_dye_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge'];
					//$aop_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					//$trims_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$trimsCostArray[$row[csf('id')]];
					$grey_prod_ex_rate=$grey_fab_array[$row[csf('id')]]['ex_rate'];
					$dye_fin_ex_rate=$dye_fin_fab_array[$row[csf('id')]]['ex_rate'];
					
					//$fin_fab_prod_qnty=$grey_fab_array[$row[csf('id')]]['fin_qnty'];
					//echo $grey_fab_array[$row[csf('id')]]['qnty'].'*'.$knit_charge_mkt.'/'.$grey_prod_ex_rate;
					if($process_costing==1)
					{
						$grey_prod_cost=$grey_fab_array[$row[csf('id')]]['qnty']*$knit_charge_mkt;
					}
					else
					{
						$grey_prod_cost=$grey_fab_array[$row[csf('id')]]['grey_amt'];
					}
					
					// $knit_cost_actual=$grey_prod_cost;
					$knit_cost_actual=$grey_fab_array[$row[csf('id')]]['qnty']*$knit_rate;
					$cpa_grey_prod_cost=$grey_fab_array[$row[csf('id')]]['cpa_grey_amt'];
					$cpa_knit_cost_actual=$cpa_grey_prod_cost;
					// echo $dye_fin_fab_array[$row[csf('id')]]['cpa_batch_weight'].'/'.$knit_charge_mkt_fin.'=='.$dye_fin_ex_rate.', ';
					$cpa_dye_fin_fab_prod_cost=$dye_fin_fab_array[$row[csf('id')]]['cpa_batch_weight'];
					 $cpa_dye_finish_cost_actual=$cpa_dye_fin_fab_prod_cost;
					
					$fin_fab_prod_cost=$grey_used_arr[$row[csf('id')]]['grey_used_amt']+$fin_grey_used_arr[$row[csf('id')]]['grey_used_amt'];//$dye_fin_fab_array[$row[csf('id')]]['fin_amt'];
					//$dye_finish_cost_actual=$order_wise_fin_fab_rec[$row[csf('id')]]['used_qty']*$dye_rate;//$fin_fab_prod_cost;
					$cpa_recv_ret_amt=$receive_ret_arr[$row[csf('job_no')]]['cpa_recv_ret_amt'];
					$cpa_yarn_recv_amt_bal=$yarn_receive_amount_arr[$row[csf('job_no')]]['cpa_order_amount_recv']-$cpa_recv_ret_amt;
					if($cpa_yarn_recv_amt_bal==0) $cpa_yarn_recv_amt_bal_actual=0;
					else $cpa_yarn_recv_amt_bal_actual=($cpa_yarn_recv_amt_bal/$job_quantity)*$order_qnty_in_pcs;
					
					//$cpa_yarn_recv_amt_bal_actual=($cpa_yarn_recv_amt_bal/$job_quantity)*$order_qnty_in_pcs;
					
					//echo  $yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_yarn_amt'].'='.$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_amt'].'='.$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_amt'].'='.$cpa_knit_cost_actual.'='.$cpa_dye_finish_cost_actual.',';
					$yarn_cost_cpa_actual=$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_yarn_amt']+$cpa_yarn_recv_amt_bal_actual+$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_amt']+$cpa_knit_cost_actual+$cpa_dye_finish_cost_actual-($yarnissueRetCostArray[$row[csf('id')]]['cpa_grey_yarn_ret_amt']+$yarnissueRetCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_ret_amt']);
					//$yarn_dyeing_twist_cpa_actual=$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_dyeing_twist_amt'];
					$yarn_ret_qty_cpa_actual=$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_yarn_qty_retble'];
					$yarn_ret_amt_cpa_actual=$yarnTrimsCostArray[$row[csf('id')]]['cpa_grey_yarn_amt_retble'];
					$tot_yarn_qty_cpa_actual+=$yarn_cost_cpa_actual;
					//$tot_yarn_amt_cpa_actual+=$yarn_ret_qty_cpa_actual;
					//echo $yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt'].'-'.$yarnissueRetCostArray[$row[csf('id')]]['grey_yarn_ret_amt'].'n';
					$yarn_cost_actual=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt']-$yarnissueRetCostArray[$row[csf('id')]]['grey_yarn_ret_amt'];
					//echo $yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt'].'D';
					$yarn_ret_qty_actual=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_qty_retble'];
					$yarn_ret_amt_actual=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt_retble'];
					$tot_yarn_qty_actual+=$yarn_ret_qty_actual;
					$tot_yarn_amt_actual+=$yarn_ret_amt_actual;
					$job_quantity=$job_arr2[$row[csf("job_no")]]['job_quantity'];
					//echo $order_amount_recv_ret.', ';
					//$yarn_receive_amount_arr[$jobNo]['cpa_order_amount_recv']; 
					$order_amount_recv=$yarn_receive_amount_arr[$row[csf('job_no')]]['order_amount_recv'];
					$order_amount_recv_ret=$receive_ret_arr[$row[csf('job_no')]]['recv_ret_amt'];
					
					//echo $cpa_yarn_recv_amt_bal_actual.'/'.$job_quantity.'*'.$order_qnty_in_pcs.', ';
					$order_amount_recv_bal=$order_amount_recv-$order_amount_recv_ret;
					$avg_recv_amount_actual=($order_amount_recv_bal/$job_quantity)*$yds_order_qnty_in_pcs;
					//echo $avg_recv_amount_actual.'/'.$job_quantity.'*'.$order_qnty_in_pcs.', ';
					$yarn_dyeing_twist_actual=$avg_recv_amount_actual;//-$yarnissueRetCostArray[$row[csf('id')]]['grey_dyeing_twist_ret_amt'];
					
					
					$trans_in_amt=$recvIssue_array[$row[csf('id')]][5];
				    $trans_out_amt=$recvIssue_array[$row[csf('id')]][6];
					$fin_trans_in_amt=$fin_fab_trans_array[$row[csf('id')]][5];
				    $fin_trans_out_amt=$fin_fab_trans_array[$row[csf('id')]][6];
					$grey_fab_transfer=$trans_out_amt-$trans_in_amt;
					//echo $grey_fab_transfer.'D';
					$grey_fab_transfer_amt_actual=$grey_fab_transfer*$knit_charge_mkt;
					$fin_fab_transfer=$fin_trans_in_amt-$fin_trans_out_amt;
					//echo $fin_fab_transfer.'='.$knit_charge_mkt_fin;
					// $fin_fab_transfer_amt_actual=$fin_fab_transfer*$knit_charge_mkt_fin;
					//$dye_rate=$order_wise_dye_bill_rate[$row[csf('id')]]['amount']/$order_wise_dye_bill_rate[$row[csf('id')]]['delivery_qty'];
					
					
					
					//if($dye_rate=='' || $dye_rate==0)
					//{
					$dye_rate=$order_wise_dye_bill_rate[$row[csf('id')]]['rate'];
					//echo $dye_rate.'A';;
					//}
					



					$bill_date=$order_wise_dye_bill_rate[$row[csf('id')]]['bill_date'];

					$currency_id=$order_wise_dye_bill_rate[$row[csf('id')]]['currency_id'];
					$usd_id=2;
					if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					if($currency_id==2) $dye_rate=$dye_rate;//USD
					else $dye_rate=$dye_rate/$currency_rate;
					$dye_finish_cost_actual=$order_wise_fin_fab_rec[$row[csf('id')]]['used_qty'];

					// $fin_fab_transfer_amt_actual=$fin_fab_transfer*$dye_rate;
					$fin_fab_transfer_amt_actual=$order_wise_fin_fab_rec[$row[csf('id')]]['transfer_qnty']*$dye_rate;
					//echo $yarn_cost_actual.'<br>';
					//$trims_cost_actual=$yarnTrimsCostArray[$row[csf('id')]][4]/$exchange_rate;
					$trims_amount=$trims_trans_array[$row[csf('id')]]['amt'];
					$trims_in_amount=$trans_in_array[$row[csf('id')]]['amt'];
					$trims_out_amount=$trans_out_array[$row[csf('id')]]['amt'];
					$gen_trims_issue_amt=$gen_trims_issue_array[$row[csf('id')]]['amt'];
					$trims_cost_actual=$trims_amount+$trims_in_amount+$gen_trims_issue_amt-$trims_out_amount;

					//echo $trims_amount.'='.$gen_trims_issue_amt.'='.$trims_in_amount.'='.$trims_out_amount.'<br>';
					$yarn_dye_cost_actual=0;
					$aop_prod_amt=$aop_prod_array[$row[csf('id')]]['aop_amt'];
					//$aop_avg_rate=$aop_prod_array[$row[csf('id')]]['aop_rate'];
					//echo $aop_prod_qty.'='.$aop_avg_rate;
					$aop_cost_actual=$aop_prod_amt;
					$embell_cost_actual=$embellCostArray[$row[csf('id')]];
					$wash_cost_actual=$washCostArray[$row[csf('id')]];
					
					//Finished Fabric Cost
					
					
					$printing_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['printing_qty']/$dzn_qnty)*$print_avg_rate;
					//echo $embl_issue_prod_array[$row[csf('id')]]['printing_qty'].'*'.$print_avg_rate;
					$embro_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['embro_qty']/$dzn_qnty)*$embro_avg_rate;
					$others_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['others']/$dzn_qnty)*$others_avg_rate;
					$dyeing_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['dyeing_qty']/$dzn_qnty)*$dyeing_avg_rate;
					$wash_cost_actual=($embl_issue_prod_array[$row[csf('id')]]['wash_qty']/$dzn_qnty)*$wash_avg_rate;
					
					$commission_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['commission']/$dzn_qnty); 

					// $comm_cost_actual=$actualCostArray[6][$row[csf('id')]];
					$comm_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['comm_cost']/$dzn_qnty); 


					$freight_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty);

					$test_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty);
					$inspection_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty);;
					$currier_cost_actual=$order_qnty_in_pcs*($fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty);
					
				//	$cm_cost_actual=$actualCostArray[5][$row[csf('id')]];
					$working_hour=$prod_resource_array[$row[csf('id')]]['working_hour'];
					$operator_helper=$prod_resource_array[$row[csf('id')]]['operator_helper'];
					$extra_working_hour=$adj_resource_array[$row[csf('style_ref_no')]]['working_hour'];
					$extra_number_of_emp=$adj_resource_array[$row[csf('style_ref_no')]]['number_of_emp'];
					$tot_number_of_employee=$operator_helper+$extra_number_of_emp;
					$tot_working_hr=$working_hour+$extra_working_hour;
					$prod_date=$prod_resource_array[$row[csf('id')]]['pr_date'];
					$from_cpm_date=date("Y-m",strtotime(($prod_date)));
					$cpm=$lib_cpm_array[$from_cpm_date]['cpm'];
					$exchange_rate=set_conversion_rate($usd_id,$prod_date );
				
					//$cm_cost_actual=$tot_working_hr*60*$cpm;
					//echo $working_hour.'='.$extra_number_of_emp.'ds';
					// $cm_cost_actual=($tot_working_hr*60*$tot_number_of_employee*$cpm)/$exchange_rate;
					// $cm_cost_actual=$fabriccostArray[$row[csf('job_no')]]['cm_cost'];
					
					$gmts_item_idArr=array_unique(explode(",",$row[csf("gmts_item_id")]));
					$sewing_qty=0;
					foreach($gmts_item_idArr as $itemid)
					{
					// $sewing_qty+=$sewing_data_arr[$row[csf("id")]][$itemid]['sewingin_qnty'];
					$sewing_qty+=$sewing_data_arr[$row[csf("id")]][$itemid]['sewingout_qnty'];
					
					}
					
					$cm_cost_actual=($fabriccostArray[$row[csf('job_no')]]['cm_cost']/$dzn_qnty)*$sewing_qty;

					$fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$row[csf('id')]];
					//echo $row[csf('id')].'-'.$fabric_purchase_cost_actual.'<br>';
					$grey_fabric_purchase_cost_actual=$grey_purchase_amnt_arr[$row[csf('id')]];
					
					if(fn_number_format($knit_cost_actual,2)==""){
						$knit_cost_actual=0;
					}
					$grey_fab_cost_actual=$yarn_cost_actual+$yarn_dyeing_twist_actual+$knit_cost_actual+$grey_fab_transfer_amt_actual;
					$finished_fab_cost_actual=$grey_fab_cost_actual+$dye_finish_cost_actual+$aop_cost_actual+$fin_fab_transfer_amt_actual+$fabric_purchase_cost_actual;
					$tot_fabric_cost_act=$fabric_purchase_cost_actual+$finished_fab_cost_actual;
					$tot_fabric_cost_actaul+=$tot_fabric_cost_act;
					
					$actual_all_cost=$yarn_cost_actual+$knit_cost_actual+$yarn_dyeing_twist_actual+$grey_fab_transfer_amt_actual+$dye_finish_cost_actual+$yarn_dye_cost_actual+$aop_cost_actual+$trims_cost_actual+$printing_cost_actual+$embro_cost_actual+$others_cost_actual+$dyeing_cost_actual+$wash_cost_actual+$commission_cost_actual+$comm_cost_actual+$freight_cost_actual+$test_cost_actual+$inspection_cost_actual+$currier_cost_actual+$cm_cost_actual+$fabric_purchase_cost_actual+$grey_fabric_purchase_cost_actual;
					// echo $yarn_cost_actual."+".$knit_cost_actual."+".$yarn_dyeing_twist_actual."+".$grey_fab_transfer_amt_actual."+".$dye_finish_cost_actual."+".$yarn_dye_cost_actual."+".$aop_cost_actual."+".$trims_cost_actual."+".$printing_cost_actual."+".$embro_cost_actual."+".$others_cost_actual."+".$dyeing_cost_actual."+".$wash_cost_actual."+".$commission_cost_actual."+".$comm_cost_actual."+".$freight_cost_actual."+".$test_cost_actual."+".$inspection_cost_actual."+".$currier_cost_actual."+".$cm_cost_actual."+".$fabric_purchase_cost_actual."+".$grey_fabric_purchase_cost_actual;
					$actual_margin=$ex_factory_value-$actual_all_cost;
					$actual_margin_perc=($actual_margin/$ex_factory_value)*100;
					$tot_fabric_cost_mkt=$tot_finish_fabric_cost_mkt+$fabric_purchase_cost_mkt;
					$tot_yarn_cost_mkt+=$yarn_cost_mkt;
					$tot_yarn_value_addition_cost_mkt+=$yarn_value_addition_cost_mkt;
					$tot_grey_fab_cost_mkt+=$grey_fab_cost_mkt; 
					$tot_knit_cost_mkt+=$knit_cost_mkt; 
					$tot_dye_finish_cost_mkt+=$dye_finish_cost_mkt; 
					//$tot_yarn_dye_cost_mkt+=$yarn_dye_cost_mkt; 
					$tot_aop_cost_mkt+=$aop_cost_mkt; 
					$total_fabric_cost_mkt+=$tot_fabric_cost_mkt;
					$tot_trims_cost_mkt+=$trims_cost_mkt;
					$total_finish_fabric_cost_mkt+=$tot_finish_fabric_cost_mkt;
					$tot_print_amount_mkt+=$print_amount; 
					$tot_embro_amount_mkt+=$embroidery_amount; 
					$tot_others_amount_mkt+=$others_amount; 
					$tot_dyeing_amount_mkt+=$other_amount; 
					$tot_wash_amount_mkt+=$wash_cost; 
					
					$tot_embell_cost_mkt+=$embell_cost_mkt; 
					$tot_wash_cost_mkt+=$wash_cost_mkt; 
					$tot_commission_cost_mkt+=$commission_cost_mkt; 
					$tot_comm_cost_mkt+=$comm_cost_mkt; 
					$tot_freight_cost_mkt+=$freight_cost_mkt; 
					$tot_test_cost_mkt+=$test_cost_mkt; 
					$tot_inspection_cost_mkt+=$inspection_cost_mkt; 
					$tot_currier_cost_mkt+=$currier_cost_mkt; 
					$tot_cm_cost_mkt+=$cm_cost_mkt; 
					$tot_mkt_all_cost+=$mkt_all_cost; 
					$tot_mkt_margin+=$mkt_margin; 
					$tot_yarn_cost_actual+=$yarn_cost_actual; 
					$tot_yarn_dyeing_twist_actual+=$yarn_dyeing_twist_actual; 
					$tot_knit_cost_actual+=$knit_inbound_bill_data_array[$row[csf('id')]]['subcontact_qnty']+$knit_outbound_bill_data_array[$row[csf('id')]]['subcontact_qnty']; 
					$tot_dye_finish_cost_actual+=$dye_finish_cost_actual;
					$tot_yarn_dye_cost_actual+=$yarn_dye_cost_actual; 
					$tot_aop_cost_actual+=$aop_cost_actual; 
					$tot_trims_cost_actual+=$trims_cost_actual; 
					$tot_grey_fab_cost_actual+=$grey_fab_cost_actual; 
					$tot_grey_fab_transfer_amt_actual+=$grey_fab_transfer_amt_actual; 
					$tot_print_amount_actual+=$printing_cost_actual; 
					$tot_embro_amount_actual+=$embro_cost_actual; 
					$tot_others_amount_actual+=$others_cost_actual; 
					$tot_dyeing_amount_actual+=$dyeing_cost_actual; 
					$tot_wash_amount_actual+=$wash_cost_actual; 
					$tot_finished_fab_cost_actual+=$finished_fab_cost_actual;
					$tot_fin_fab_transfer_amt_actual+=$fin_fab_transfer_amt_actual;
					$tot_embell_cost_actual+=$embell_cost_actual;
					$tot_wash_cost_actual+=$wash_cost_actual; 
					$tot_commission_cost_actual+=$commission_cost_actual; 
					$tot_comm_cost_actual+=$comm_cost_actual; 
					$tot_freight_cost_actual+=$freight_cost_actual; 
					$tot_test_cost_actual+=$test_cost_actual; 
					$tot_inspection_cost_actual+=$inspection_cost_actual; 
					$tot_currier_cost_actual+=$currier_cost_actual; 
					$tot_cm_cost_actual+=$cm_cost_actual; 
					$tot_actual_all_cost+=$actual_all_cost; 
					$tot_actual_margin+=$actual_margin;
					$tot_actual_net_margin+=$actual_margin-$yarn_cost_cpa_actual; 
					
					$tot_fabric_purchase_cost_mkt+=$fabric_purchase_cost_mkt;
					$tot_fabric_purchase_cost_actual+=$fabric_purchase_cost_actual;
					$tot_grey_fabric_purchase_cost_actual+=$grey_fabric_purchase_cost_actual;
					
					$buyer_name_array[$row[csf('buyer_name')]]=$buyer_arr[$row[csf('buyer_name')]];
					$mkt_po_val_array[$row[csf('buyer_name')]]+=$po_value;
					$mkt_yarn_array[$row[csf('buyer_name')]]+=$yarn_cost_mkt;
					
					$mkt_knit_array[$row[csf('buyer_name')]]+=$knit_cost_mkt;
					$mkt_yarn_value_addition_array[$row[csf('buyer_name')]]+=$yarn_value_addition_cost_mkt;
					$mkt_dy_fin_array[$row[csf('buyer_name')]]+=$dye_finish_cost_mkt;
					$mkt_grey_fab_cost_array[$row[csf('buyer_name')]]+=$grey_fab_cost_mkt;
					$mkt_aop_array[$row[csf('buyer_name')]]+=$aop_cost_mkt;
					$mkt_trims_array[$row[csf('buyer_name')]]+=$trims_cost_mkt;
					$mkt_print_array[$row[csf('buyer_name')]]+=$print_amount;
					$mkt_embro_array[$row[csf('buyer_name')]]+=$embroidery_amount;
					$mkt_dyeing_array[$row[csf('buyer_name')]]+=$other_amount;
					//$mkt_wash_array[$row[csf('buyer_name')]]+=$wash_cost;
					
					$mkt_emb_array[$row[csf('buyer_name')]]+=$embell_cost_mkt;
					$mkt_wash_array[$row[csf('buyer_name')]]+=$wash_cost_mkt;
					$mkt_commn_array[$row[csf('buyer_name')]]+=$commission_cost_mkt;
					$mkt_commercial_array[$row[csf('buyer_name')]]+=$comm_cost_mkt;
					$mkt_freight_array[$row[csf('buyer_name')]]+=$freight_cost_mkt;
					$mkt_test_array[$row[csf('buyer_name')]]+=$test_cost_mkt;
					$mkt_ins_array[$row[csf('buyer_name')]]+=$inspection_cost_mkt;
					$mkt_courier_array[$row[csf('buyer_name')]]+=$currier_cost_mkt;
					$mkt_cm_array[$row[csf('buyer_name')]]+=$cm_cost_mkt;
					$mkt_total_array[$row[csf('buyer_name')]]+=$mkt_all_cost;
					$mkt_margin_array[$row[csf('buyer_name')]]+=$mkt_margin;
					$mkt_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_mkt;
					$mkt_fin_fabric_purchase_array[$row[csf('buyer_name')]]+=$finish_fabric_purchase_cost_mkt;
						
					$ex_factory_val_array[$row[csf('buyer_name')]]+=$ex_factory_value;
					$yarn_cost_array[$row[csf('buyer_name')]]+=$yarn_cost_actual;
					$yarn_dyeing_twist_array[$row[csf('buyer_name')]]+=$yarn_dyeing_twist_actual;
					$knit_cost_array[$row[csf('buyer_name')]]+=$knit_cost_actual;
					$dye_cost_array[$row[csf('buyer_name')]]+=$dye_finish_cost_actual;
					$grey_fab_cost_array[$row[csf('buyer_name')]]+=$grey_fab_transfer_amt_actual;//$grey_fab_cost_actual;
					$fin_fab_transfer_amt_actual_array[$row[csf('buyer_name')]]+=$fin_fab_transfer_amt_actual;
					$finished_fab_cost_actual_array[$row[csf('buyer_name')]]+=$finished_fab_cost_actual;
				
					$aop_n_others_cost_array[$row[csf('buyer_name')]]+=$aop_cost_actual;
					$trims_cost_array[$row[csf('buyer_name')]]+=$trims_cost_actual;
					
					$print_act_cost_array[$row[csf('buyer_name')]]+=$printing_cost_actual;
					$embro_act_cost_array[$row[csf('buyer_name')]]+=$embro_cost_actual;
					$others_act_cost_array[$row[csf('buyer_name')]]+=$others_cost_actual;
					$dyeing_act_cost_array[$row[csf('buyer_name')]]+=$dyeing_cost_actual;
					$wash_act_cost_array[$row[csf('buyer_name')]]+=$wash_cost_actual;
					
					$enbellishment_cost_array[$row[csf('buyer_name')]]+=$embell_cost_actual;
					$wash_cost_array[$row[csf('buyer_name')]]+=$wash_cost_actual;
					$commission_cost_array[$row[csf('buyer_name')]]+=$commission_cost_actual;
					$commercial_cost_array[$row[csf('buyer_name')]]+=$comm_cost_actual;
					$freight_cost_array[$row[csf('buyer_name')]]+=$freight_cost_actual;
					$testing_cost_array[$row[csf('buyer_name')]]+=$test_cost_actual;
					$inspection_cost_array[$row[csf('buyer_name')]]+=$inspection_cost_actual;
					$courier_cost_array[$row[csf('buyer_name')]]+=$currier_cost_actual;
					$cm_cost_array[$row[csf('buyer_name')]]+=$cm_cost_actual;
					$total_cost_array[$row[csf('buyer_name')]]+=$actual_all_cost;
					$margin_array[$row[csf('buyer_name')]]+=$actual_margin;
					$actual_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_actual;
					$style_wise_arr[$row[csf('buyer_name')]].=$row[csf('style_ref_no')].',';
					$job_wise_arr[$row[csf('buyer_name')]].=$row[csf('job_no_prefix_num')].',';
					$po_wise_arr[$row[csf('buyer_name')]].=$row[csf('po_number')].',';
					
					
					 
					 
			//$prod_resource_array[$val[csf('po_id')]]['pr_date']=$val[csf('pr_date')]
				 $button_po="<a href='#' onClick=\"generate_po_report('".$company_name."','".$row[csf('id')]."','".$row[csf('job_no')]."','show_po_detail_report','1')\" '> ".$row[csf('po_number')]."<a/>";
					

				 

			
				?>
                	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40" rowspan="4"><? echo $i; ?></td>
                        <td width="70" rowspan="4"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                        <td width="60" align="center" rowspan="4"><? echo $row[csf('year')]; ?></td>
                        <td width="70" align="center" rowspan="4"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="100" rowspan="4" style="word-break:break-all;"><? echo  $row[csf('po_number')];//$button_po; ?></td>
						<td width="100" align="center" rowspan="4" style="word-break:break-all;"><? echo  $sc_lc_no;//$button_po; ?></td>
                        <td width="70" rowspan="4" style="word-break:break-all;"><? echo $row[csf('file_no')]; ?></td>
                        <td width="80" rowspan="4" style="word-break:break-all;"><? echo $row[csf('grouping')]; ?></td>
                        <td width="110" rowspan="4" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                        <td width="120" rowspan="4" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                        <td width="90" align="right"  rowspan="4"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="50" align="center" rowspan="4"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        <td width="70" align="right"  rowspan="4"><? echo number_format($unit_price,2,'.',''); ?></td>
                        <td width="110" align="right"  rowspan="4"><? echo number_format($po_value,2,'.',''); ?></td>
                        <td width="100" align="right"  rowspan="4"><? echo number_format($row[csf('set_smv')],2,'.',''); ?></td>
                        <td width="80" align="center" rowspan="4"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <td width="100" rowspan="4" style="word-break:break-all;"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                        <td width="100" align="left"><b>Pre Costing</b></td>
                        <td width="100" align="right" ><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
                        <td width="110" align="right" ><? echo number_format($po_value,2,'.',''); ?></td>
                        <td width="110"  align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $row[csf('id')]."**".$row[csf('job_no')]; ?>','mkt_yarn_cost','Grey Yarn Mkt. Cost Details')"><? echo number_format($yarn_cost_mkt,2); //$tot_yarn_qty_actual?></a><? //echo  number_format($yarn_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Yarn Value Addition Cost=Yarn Dyeing & Twisting"  align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $row[csf('id')]."**".$row[csf('job_no')]; ?>','mkt_yarn_dyeing_twisting_cost','Yarn Dyeing & Twisting Cost Details')"><? echo number_format($yarn_value_addition_cost_mkt,2); ?></a><? //echo number_format($yarn_value_addition_cost_mkt,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($knit_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Grey Fabric Transfer Cost"  align="right"><? //echo number_format($knit_cost_mkt,2,'.',''); ?></td>
                       
                        <td width="100" title="Tot Grey Fabric Cost"  align="right"><? echo number_format($grey_fab_cost_mkt,2,'.',''); ?></td>
                        <td width="110" title="Without Conv. Process Name: -Knitting,Twisting,All Over Printing,Yarn Dyeing,Weaving"  align="right"><? echo number_format($dye_finish_cost_mkt,2,'.',''); ?></td>
                       
                        <td width="110"  align="right"><? echo number_format($aop_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Finished Fabric Transfer Cost"  align="right"><? //echo number_format($aop_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Fabric Purchase Cost"  align="right"><? echo number_format($finish_fabric_purchase_cost_mkt,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($fabric_purchase_cost_mkt,2,'.',''); ?></td>
                        <td width="100" title="Tot Finished Fabric Cost"  align="right"><? echo number_format($tot_finish_fabric_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><?
						
						 echo number_format($tot_fabric_cost_mkt,2,'.',''); ?></td>
						<td width="110"  align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $row[csf('id')]."**".$row[csf('job_no')]; ?>','trims_cost_mkt','Trims Cost Details','800px')"><? echo number_format($trims_cost_mkt,2,'.',''); ?></a></td>
						<td width="100" title="Garment Printing Cost"  align="right"><? echo number_format($print_amount,2,'.',''); ?></td>
						<td width="100"  align="right"><? echo number_format($embroidery_amount,2,'.',''); ?></td>
						<td width="100"  align="right"><? echo number_format($others_amount,2,'.',''); ?></td>
						<td width="100" title="Garment Dyeing Cost"  align="right"><? echo number_format($other_amount,2,'.',''); ?></td>
						<td width="100"  align="right"><? echo number_format($wash_cost,2,'.',''); ?></td>
					
                        <td width="100"  align="right"><? echo number_format($commission_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($comm_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($freight_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($test_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($inspection_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($currier_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($cm_cost_mkt,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($mkt_all_cost,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($mkt_margin,2,'.',''); ?></td>
                        <td  width="100"  align="right"><? echo number_format($mkt_margin_perc,2,'.',''); ?></td>
                        <td width="100"  align="right"><? //echo number_format($mkt_margin,2,'.',''); ?></td>
                        <td  align="right"><? //echo number_format($mkt_margin,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#F5F5F5" onClick="change_color('tr_a<? echo $i; ?>','#F5F5F5')" id="tr_a<? echo $i; ?>">
                    	<td width="100" align="left"><b>Actual</b></td>
                        <td width="100" align="right" >
						<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no_prefix_num')];?>','<? echo $row[csf('id')]; ?>','650px')"><? echo  number_format($ex_factory_qty,0,'.',''); ?></a>
						<? //echo number_format($ex_factory_qty,0,'.',''); ?></td>
                        <td width="110" align="right" ><? echo number_format($ex_factory_value,2,'.',''); ?></td>
                        <td width="110"  align="right"><a href="#report_details" onClick="openmypage_actual2('<? echo $row[csf('id')]; ?>','yarn_cost_actual','Actual Yarn Cost Details','1','1020px')"><? echo number_format($yarn_cost_actual,2,'.',''); ?></a></td>
                          <td width="100" title="Yarn Dying With Order, Yarn Service Work Order(Rate): Yarn Recv: Yarn Dyeing/Twisting/Re-Waxing Cost :<? echo $order_amount_recv.' - Recv Ret: '.$order_amount_recv_ret.'/ Job Qty :'.$job_quantity.'* Po Qty :'.$order_qnty_in_pcs;?>"  align="right">
						  <a href="#report_details"  onClick="openmypage_actual2('<? echo $row[csf('id')]; ?>','yarn_dye_twist_cost_actual','Actual Yarn Dyeing Twsit Cost Details','2','930px')"><? echo number_format($yarn_dyeing_twist_actual,2,'.',''); ?></a>
						  <? //echo number_format($yarn_dyeing_twist_actual,2,'.',''); ?></td>
                        <td width="110" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','knit_cost_actual','Actual Knitting Cost Details','900px')"><? echo number_format($knit_inbound_bill_data_array[$row[csf('id')]]['subcontact_qnty']+$knit_outbound_bill_data_array[$row[csf('id')]]['subcontact_qnty'],2,'.',''); ?></a><? //echo number_format($knit_cost_actual,2,'.',''); ?></td>


                         <td width="100" title="Grey Fabric Transfer Cost <? echo $knit_charge_mkt;?>"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','grey_fabric_transfer_cost_actual','Grey Fabric Transfer Cost Details','900px')"><? echo number_format($grey_fab_transfer_amt_actual,2,'.',''); ?></a></td>
                        <td width="100" title="Grey Yarn Cost+Yarn Dyeing/Twisting/Re-Waxing Cost+Knitting Cost+Grey Fabric Transfer Cost"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','knit_cost_actual','Actual Knitting Cost Details','900px')"><? //echo number_format($grey_fab_cost_actual,2,'.',''); ?></a><? echo number_format($grey_fab_cost_actual,2,'.',''); ?></td>
                        <td width="110"  title="<?=$order_wise_fin_fab_rec[$row[csf('id')]]['used_qty'].'*'.$dye_rate;?>" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','dye_finish_cost_actual2','Actual Dyeing & Finish Cost Details','900px')"><? echo number_format($order_wise_fin_fab_rec[$row[csf('id')]]['used_qty'],2,'.',''); ?></a><? //echo number_format($dye_finish_cost_actual,2,'.',''); ?></td>
                       
                        <td width="110"  align="right" title="<? echo 'Fabric Service Receive'; ?>"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual2','AOP Cost Details','800px')"><? echo number_format($aop_cost_actual,2,'.',''); ?></a><? //echo number_format($aop_cost_actual,2,'.',''); ?></td>
                          <td width="100" title="<? echo $order_wise_fin_fab_rec[$row[csf('id')]]['transfer_qnty']."*".$dye_rate;?>"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual','Fin Feb Transfer Cost Details','800px')"><? //echo number_format($fin_fab_transfer_amt_actual,2,'.',''); ?></a><? echo number_format($fin_fab_transfer_amt_actual,2,'.',''); ?></td>
                           <td width="100" title="From- Finish Fabric Receive Entry"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual','AOP Cost Details','800px')"><? //echo number_format($fabric_purchase_cost_actual,2,'.',''); ?></a><? echo number_format($fabric_purchase_cost_actual,2,'.',''); ?></td>
                           <td width="110" title="From: Knit Grey Fabric Receive and Woven Grey Fabric Receive"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','fabric_purchase_cost_actual','Fabric Purchase Cost Details','900px')"><? //echo number_format($grey_fabric_purchase_cost_actual,2,'.',''); ?></a><? echo number_format($grey_fabric_purchase_cost_actual,2,'.',''); ?></td>
                            <td width="100" title=""  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual','AOP Cost Details','800px')"><? //echo number_format($finished_fab_cost_actual,2,'.',''); ?></a><? echo number_format($finished_fab_cost_actual,2,'.',''); ?></td>
                             <td width="100" title="Tot Fab. Cost"  align="right"><? echo number_format($tot_fabric_cost_act,2,'.',''); ?></td>
                             
                        <td width="110"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','trims_cost_actual','Trims Cost Details','800px')"><? echo number_format($trims_cost_actual,2,'.',''); ?></a></td>
                        <td width="100"  title="Garment Printing Cost" align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','1','gmt_print_wash_dye_embell_cost_actual','Print Cost Details','800px')"><? echo number_format($printing_cost_actual,2,'.',''); ?></a></td>
                        <td width="100" title="Garment Embro Cost"  align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','2','gmt_print_wash_dye_embell_cost_actual','Embro Cost Details','800px')"><? echo number_format($embro_cost_actual,2,'.',''); ?></a></td>
						<td width="100" title="Garment Embro Cost"  align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','2','gmt_print_wash_dye_embell_cost_actual','others Cost Details','800px')"><? //echo number_format($embro_cost_actual,2,'.',''); ?></a><? echo number_format($others_cost_actual,2,'.',''); ?></td>
                         <td width="100" title="Garment Dyeing Cost"  align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','5','gmt_print_wash_dye_embell_cost_actual','Trims Cost Details','800px')"><? echo number_format($dyeing_cost_actual,2,'.',''); ?></a></td>
                          <td width="100" title="Garment Wash Cost"  align="right"><a href="#report_details" onClick="openmypage_gmt_actual('<? echo $row[csf('id')]; ?>','3','gmt_print_wash_dye_embell_cost_actual','Trims Cost Details','800px')"><? echo number_format($wash_cost_actual,2,'.',''); ?></a></td>
                         
                        <td width="100"  align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]."_".$row[csf('job_no')]."_".$ex_factory_qty."_".$dzn_qnty; ?>','commission_cost_actual','Commission Cost Details','600px')"><? echo number_format($commission_cost_actual,2,'.',''); ?></a></td>
                        <td width="100"  align="right"><? echo number_format($comm_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($freight_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($test_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($inspection_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($currier_cost_actual,2,'.',''); ?></td>
                        <?
                        //	$cm_cost_actual=($tot_working_hr*60*$tot_number_of_employee*$cpm)/$exchange_rate;
						?>
                        <td width="100"  title="Working Hr(<? echo $tot_working_hr;?>)*60*Number Of Employee(<? echo $tot_number_of_employee;?>)*CPM(<? echo $cpm;?>)/ExchangeRate(<? echo $exchange_rate;?>)" align="right"><? echo number_format($cm_cost_actual,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($actual_all_cost,2,'.',''); ?></td>
                        <td width="100"  align="right" title="Ex factory value-Actual all cost;"><? echo number_format($actual_margin,2,'.',''); ?></td>
                        <td  width="100" align="right"  title="Margin /Ex Fact Value*100"><? echo number_format($actual_margin_perc,2,'.',''); ?></td>
                        
                        <td width="100" title="Short Booking,yarn issue for Grey Yarn Amount"  align="right"><? echo number_format($yarn_cost_cpa_actual,2,'.',''); ?></td>
                        <td   align="right"><? echo number_format($actual_margin-$yarn_cost_cpa_actual,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9E9E9" onClick="change_color('tr_v<? echo $i; ?>','#E9E9E9')" id="tr_v<? echo $i; ?>">
                    	<td width="100" align="left"><b>Variance</b></td>
                    	<td width="100" align="right" >
							<? 
								//$variance_qnty=$order_qnty_in_pcs-$ex_factory_qty;
								//$variance_qnty_per=($variance_qnty/$order_qnty_in_pcs)*100; 
								//echo number_format($variance_qnty,0,'.',''); 
							?>
                        </td>
                    	<td width="110" align="right" >
                        	<? 
								$variance_po_value=$po_value-$ex_factory_value;
								$variance_po_value_per=($variance_po_value/$po_value)*100; 
								echo number_format($variance_po_value,2,'.',''); 
							?>
                        </td>
                        <td width="110"  align="right">
                        	<? 
								$variance_yarn_cost=($yarn_cost_mkt+$yarn_dye_cost_mkt)-$yarn_cost_actual;
								if($variance_yarn_cost==0)
								{
									$variance_yarn_cost_per=0;
								}
								else
								{
									$variance_yarn_cost_per=($variance_yarn_cost/($yarn_cost_mkt+$yarn_dye_cost_mkt))*100; 
								}
								echo number_format($variance_yarn_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100" title="Yarn Dyeing/Twisting/Re-Waxing Cost"  align="right">
                        	<? 
								$variance_yarn_value_addition_cost=$yarn_value_addition_cost_mkt-$yarn_dyeing_twist_actual;
								//$variance_yarn_value_addition_cost_per=($variance_yarn_value_addition_cost/$yarn_value_addition_cost_mkt)*100;
								if($variance_yarn_value_addition_cost==0 && $yarn_value_addition_cost_mkt==0)
								{
									$variance_yarn_value_addition_cost_per=0;
								}
								else
								{
									$variance_yarn_value_addition_cost_per=($variance_yarn_value_addition_cost/$yarn_value_addition_cost_mkt)*100;
								} 
								echo number_format($variance_yarn_value_addition_cost,2,'.',''); 
							?>
                        </td>
                        <td width="110"  align="right">
                        	<? 
								$variance_kint_cost=$knit_cost_mkt-$knit_cost_actual;
								if($variance_kint_cost==0)
								{
									$variance_knit_cost_per=0;
								}
								else
								{
									$variance_knit_cost_per=($variance_kint_cost/$knit_cost_mkt)*100; 
								}
								echo number_format($variance_kint_cost,2,'.',''); 
							?>
                        </td>
                         <td width="100" title="Grey Fabric Transfer Cost"  align="right">
                        	<? 
								//$variance_kint_cost=$knit_cost_mkt-$knit_cost_actual;
								//$variance_knit_cost_per=($variance_kint_cost/$knit_cost_mkt)*100; 
								//echo number_format($variance_kint_cost,2,'.',''); 
							?>
                        </td>
                        
                         <td width="100" title="Tot Grey Fabric Cost"  align="right">
                        	<? 
								$variance_grey_fab_cost=$grey_fab_cost_mkt-$grey_fab_cost_actual;
								if($variance_grey_fab_cost==0 )
								{
									$variance_grey_fab_cost_per=0;
								}
								else
								{
									$variance_grey_fab_cost_per=($variance_grey_fab_cost/$grey_fab_cost_mkt)*100; 
								}
								echo number_format($variance_grey_fab_cost,2,'.',''); 
							?>
                        </td>
                        <td width="110"  align="right">
                        	<? 
								$variance_dye_finish_cost=$dye_finish_cost_mkt-$order_wise_fin_fab_rec[$row[csf('id')]]['used_qty']*$dye_rate;
								if($variance_dye_finish_cost==0 && $dye_finish_cost_mkt==0)
								{
									$variance_dye_finish_cost_per=0;
								}
								else
								{
									$variance_dye_finish_cost_per=($variance_dye_finish_cost/$dye_finish_cost_mkt)*100; 
								}
								echo number_format($variance_dye_finish_cost,2,'.','');
							?>
                        </td>
                        <td width="110"  align="right">
                        	<? 
								$variance_aop_cost=$aop_cost_mkt-$aop_cost_actual;
								if($variance_aop_cost==0)
								{
									$variance_aop_cost_per=0;
								}
								else
								{
									$variance_aop_cost_per=($variance_aop_cost/$aop_cost_mkt)*100; 
								}
								echo number_format($variance_aop_cost,2,'.',''); 
							?>
                        </td>
                          <td width="100" title="Finished Fabric Transfer Cost"  align="right">
                        	<? 
								//$variance_aop_cost=$aop_cost_mkt-$aop_cost_actual;
								//$variance_aop_cost_per=($variance_aop_cost/$aop_cost_mkt)*100; 
								//echo number_format($variance_aop_cost,2,'.',''); 
							?>
                        </td>
                         <td width="100" title="Finished Fabric Purchase Cost"  align="right">
                        	<? 
								$variance_fin_fabric_purchase_cost=$finish_fabric_purchase_cost_mkt-$fabric_purchase_cost_actual;
								//$variance_fin_fabric_cost_per=($variance_fin_fabric_purchase_cost/$finish_fabric_purchase_cost_mkt)*100;
								if($variance_fin_fabric_purchase_cost==0)
								{
									$variance_fin_fabric_cost_per=0;
								}
								else
								{
									$variance_fin_fabric_cost_per=($variance_fin_fabric_purchase_cost/$finish_fabric_purchase_cost_mkt)*100; 
								} 
								echo number_format($variance_fin_fabric_purchase_cost,2,'.',''); 
							?>
                        </td>
                         <td width="110"  align="right">
                        	<? 
								$variance_finish_purchase_cost=$fabric_purchase_cost_mkt-$fabric_purchase_cost_actual;
								//$variance_finish_purchase_cost_per=($variance_finish_purchase_cost/$fabric_purchase_cost_mkt)*100; 
								if($variance_finish_purchase_cost==0 && $fabric_purchase_cost_mkt==0)
								{
									$variance_finish_purchase_cost_per=0;
								}
								else
								{
									$variance_finish_purchase_cost_per=($variance_finish_purchase_cost/$fabric_purchase_cost_mkt)*100; 
								}
								echo number_format($variance_finish_purchase_cost,2,'.','');
							?>
                        </td>
                         <td width="100" title="Finished Fabric Cost"  align="right">
                        	<? 
								//$variance_finished_fab_cost=$tot_finish_fabric_cost_mkt-$finished_fab_cost_actual;
								//$variance_finished_fab_cost_per=($variance_finished_fab_cost/$finished_fab_cost_mkt)*100;
								$variance_finished_fab_cost=$tot_finish_fabric_cost_mkt-$finished_fab_cost_actual;
							//	echo $variance_finished_fab_cost.'='.$finished_fab_cost_mkt;
								if($variance_finished_fab_cost==0)
								{
									$variance_finished_fab_cost_per=0;
								}
								else
								{
									$variance_finished_fab_cost_per=($variance_finished_fab_cost/$tot_finish_fabric_cost_mkt)*100; 
								} 
								echo number_format($variance_finished_fab_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100" title="Total Fabric Cost"  align="right">
                        	<? 
								$variance_tot_fabric_cost=$tot_fabric_cost_mkt-$tot_fabric_cost_act;
								//$variance_tot_fabric_cost_per=($variance_tot_fabric_cost/$tot_fabric_cost_mkt)*100; 
								if($variance_tot_fabric_cost==0 && $tot_fabric_cost_mkt==0)
								{
									$variance_tot_fabric_cost_per=0;
								}
								else
								{
									$variance_tot_fabric_cost_per=($variance_tot_fabric_cost/$tot_fabric_cost_mkt)*100; 
								}
								echo number_format($variance_tot_fabric_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_trims_cost=$trims_cost_mkt-$trims_cost_actual;
								if($variance_trims_cost==0)
								{
									$variance_trims_cost_per=0;
								}
								else
								{
									$variance_trims_cost_per=($variance_trims_cost/$trims_cost_mkt)*100; 
								}
								echo number_format($variance_trims_cost,2,'.','');
							?>
                        </td>
                         <td width="100" title="Garment Printing Cost"  align="right">
                        	<? 
								$variance_print_cost=$print_amount-$printing_cost_actual;
								if($variance_print_cost==0)
								{
									$variance_print_cost_per=0;
								}
								else
								{
									$variance_print_cost_per=($variance_print_cost/$print_amount)*100; 
								}
								echo number_format($variance_print_cost,2,'.','');
							?>
                        </td>
                       
						<td width="100" title="Garment Embro Cost"  align="right">
                        	<? 
								$variance_embro_cost=$embroidery_amount-$embro_cost_actual;
								if($variance_embro_cost==0 && $embroidery_amount==0)
								{
									$variance_embro_cost_per=0;
								}
								else
								{
									$variance_embro_cost_per=($variance_embro_cost/$embroidery_amount)*100; 
								}
								echo number_format($variance_embro_cost,2,'.','');
							?>
                        </td>
						<td width="100" title="Garment Embro Cost"  align="right">
                        	<? 
								$variance_others_cost=$others_amount-$others_cost_actual;
								if($variance_others_cost==0 && $others_amount==0)
								{
									$variance_others_cost_per=0;
								}
								else
								{
									$variance_others_cost_per=($variance_others_cost/$others_amount)*100; 
								}
								echo number_format($variance_others_cost,2,'.','');
							?>
                        </td>
                         <td width="100" title="Garment Dyeing Cost"  align="right">
                        	<? 
								$variance_dyeing_cost=$other_amount-$dyeing_cost_actual;
								if($variance_dyeing_cost==0 && $other_amount==0)
								{
									$variance_dyeing_cost_per=0;
								}
								else
								{
									$variance_dyeing_cost_per=($variance_dyeing_cost/$other_amount)*100; 
								}
								echo number_format($variance_dyeing_cost,2,'.','');
							?>
                        </td>
                         <td width="100" title="Garment Wasing Cost"  align="right">
                        	<? 
								$variance_wash_cost=$wash_cost-$wash_cost_actual;
							if($variance_wash_cost==0 )
								{
									$variance_wash_cost_per=0;
								}
								else
								{
									$variance_wash_cost_per=($variance_wash_cost/$wash_cost)*100; 
								}
								echo number_format($variance_wash_cost_per,2,'.','');
							?>
                        </td>
                     
                        <td width="100"  align="right">
                        	<? 
								$variance_commission_cost=$commission_cost_mkt-$commission_cost_actual;
								if($variance_commission_cost==0 && $commission_cost_mkt==0)
								{
									$variance_commission_cost_per=0;
								}
								else
								{
									$variance_commission_cost_per=($variance_commission_cost/$commission_cost_mkt)*100; 
								}
								echo number_format($variance_commission_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_comm_cost=$comm_cost_mkt-$comm_cost_actual;
								if($variance_comm_cost==0 )
								{
									$variance_commission_cost_per=0;
								}
								else
								{
									$variance_comm_cost_per=($variance_comm_cost/$comm_cost_mkt)*100; 
								}
								echo number_format($variance_comm_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_freight_cost=$freight_cost_mkt-$freight_cost_actual;
								if($variance_freight_cost==0 && $freight_cost_mkt==0)
								{
									$variance_freight_cost_per=0;
								}
								else
								{
									$variance_freight_cost_per=($variance_freight_cost/$freight_cost_mkt)*100; 
								}
								echo number_format($variance_freight_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_test_cost=$test_cost_mkt-$test_cost_actual;
								if($variance_test_cost==0 && $test_cost_mkt==0)
								{
									$variance_test_cost_per=0;
								}
								else
								{
									$variance_test_cost_per=($variance_test_cost/$test_cost_mkt)*100; 
								}
								echo number_format($variance_test_cost,2,'.','');
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_inspection_cost=$inspection_cost_mkt-$inspection_cost_actual;
								if($variance_inspection_cost==0 && $inspection_cost_mkt==0)
								{
									$variance_inspection_cost_per=0;
								}
								else
								{
									$variance_inspection_cost_per=($variance_inspection_cost/$inspection_cost_mkt)*100; 
								}
								echo number_format($variance_inspection_cost,2,'.','');
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_currier_cost=$currier_cost_mkt-$currier_cost_actual;
								if($variance_currier_cost==0 )
								{
									$variance_currier_cost_per=0;
								}
								else
								{
									$variance_currier_cost_per=($variance_currier_cost/$currier_cost_mkt)*100; 
								}
								echo number_format($variance_currier_cost,2,'.','');
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_cm_cost=$cm_cost_mkt-$cm_cost_actual;
								if($variance_cm_cost_per==0)
								{
									$variance_cm_cost_per=0;
								}
								else
								{
									$variance_cm_cost_per=($variance_cm_cost/$cm_cost_mkt)*100; 
								}
								echo number_format($variance_cm_cost,2,'.',''); 
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_all_cost=$mkt_all_cost-$actual_all_cost;
								if($variance_all_cost==0)
								{
									$variance_all_cost_per=0;
								}
								else
								{
									$variance_all_cost_per=($variance_all_cost/$mkt_all_cost)*100; 
								}
								
								echo number_format($variance_all_cost,2,'.','');
							?>
                        </td>
                        <td width="100"  align="right">
                        	<? 
								$variance_margin_cost=$mkt_margin-$actual_margin;
								if($variance_margin_cost==0)
								{
									$variance_margin_cost_per=0;
								}
								else
								{
									$variance_margin_cost_per=($variance_margin_cost/$mkt_margin)*100; 
								}
								echo number_format($variance_margin_cost,2,'.','');
							?>
                        </td>
                        <td  width="100" align="right">
                        	<? 
								$variance_per_cost=$mkt_margin_perc-$actual_margin_perc;
								if($variance_per_cost==0)
								{
									$variance_per_cost_per=0;
								}
								else
								{
									$variance_per_cost_per=($variance_per_cost/$mkt_margin_perc)*100; 
								}
								echo number_format($variance_per_cost,2,'.','');
							?>
                        </td>
                         <td width="100"  align="right">
                        	<? 
								/*$variance_margin_cost=$mkt_margin-$actual_margin;
								$variance_margin_cost_per=($variance_margin_cost/$mkt_margin)*100; 
								echo number_format($variance_margin_cost,2,'.','');*/
							?>
                        </td>
                         <td   align="right">
                        	<? 
								/*$variance_margin_cost=$mkt_margin-$actual_margin;
								$variance_margin_cost_per=($variance_margin_cost/$mkt_margin)*100; 
								echo number_format($variance_margin_cost,2,'.','');*/
							?>
                        </td>
                        
                    </tr>
                    <tr bgcolor="#DFDFDF" onClick="change_color('tr_vp<? echo $i; ?>','#DFDFDF')" id="tr_vp<? echo $i; ?>">
                    	<td width="100" align="left"><b>Variance (%)</b></td>
                    	<td width="100" align="right" ><? echo number_format($variance_qnty_per,2,'.',''); ?></td>
                    	<td width="110" align="right" ><? echo number_format($variance_po_value_per,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($variance_yarn_cost_per,2,'.',''); ?></td>
                          <td width="100" title="Yarn Dyeing/Twisting/Re-Waxing Cost"  align="right"><? echo number_format($variance_yarn_value_addition_cost_per,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($variance_knit_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? //echo number_format($variance_knit_cost_per,2,'.',''); ?></td>
                       
                        <td width="100" title="Grey Fabric Cost"  align="right"><? echo number_format($variance_grey_fab_cost_per,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($variance_dye_finish_cost_per,2,'.',''); ?></td>
                       
                        <td width="110"  align="right"><? echo number_format($variance_aop_cost_per,2,'.',''); ?></td>
                        <td width="100" title="Finished Fabric Transfer Cost"  align="right"><? //echo number_format($variance_aop_cost_per,2,'.',''); ?></td>
                         <td width="100" title="Finished Fabric Purchase Cost"  align="right"><? echo number_format($variance_fin_fabric_cost_per,2,'.',''); ?></td>
                           <td width="110"  align="right"><? echo number_format($variance_finish_purchase_cost_per,2,'.',''); ?></td>
                         <td width="100" title="Tot Finished Fabric Cost"  align="right"><? echo number_format($variance_finished_fab_cost_per,2,'.',''); ?></td>
                         <td width="100"  align="right"><?  echo number_format($variance_tot_fabric_cost_per,2,'.',''); ?></td>
                        <td width="110"  align="right"><? echo number_format($variance_trims_cost_per,2,'.',''); ?></td>
                        <td width="100" title="Garment Printing Cost"  align="right"><? echo number_format($variance_print_cost_per,2,'.',''); ?></td>
                        <td width="100" title="Garment Embro Cost"  align="right"><? echo number_format($variance_embro_cost_per,2,'.',''); ?></td>
						<td width="100" title="Garment Others Cost"  align="right"><? echo number_format($variance_others_cost_per,2,'.',''); ?></td>
                        <td width="100" title="Garment Dyeing Cost"  align="right"><? echo number_format($variance_dyeing_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_wash_cost_per,2,'.',''); ?></td>
                        
                      
                        <td width="100"  align="right"><? echo number_format($variance_commission_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_comm_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_freight_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_test_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_inspection_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_currier_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_cm_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_all_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? echo number_format($variance_margin_cost_per,2,'.',''); ?></td>
                        <td   width="100" align="right"><? echo number_format($variance_per_cost_per,2,'.',''); ?></td>
                        <td width="100"  align="right"><? //echo number_format($variance_margin_cost_per,2,'.',''); ?></td>
                        <td   align="right"><? //echo number_format($variance_margin_cost_per,2,'.',''); ?></td>
                    </tr>
				<?
					$i++;
				}
				
				$tot_mkt_margin_perc=($tot_mkt_margin/$tot_po_value)*100;
				if($tot_ex_factory_val==0)
				{
					$tot_actual_margin_perc=0;
				}
				else
				{
					$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
				}
				
				//$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
				
				?>
            </table>
		</div>
        <table class="rpt_table" width="4830" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr bgcolor="#CCDDEE" onClick="change_color('tr_pt','#CCDDEE')" id="tr_pt" style="font-weight:bold;">
              <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="120" align="">Total</td>
               
                <td width="90"  align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?></td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110"  align="right"><? echo number_format($tot_po_value,2,'.',''); ?></td>
                <td width="100"  align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Pre Costing Total</td>
                <td width="100"  align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_po_value,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_yarn_cost_mkt,2,'.',''); ?></td>
                 <td width="100" title="Yarn Dyeing/Twisting/Re-Waxing Cost"  align="right"><? echo number_format($tot_yarn_value_addition_cost_mkt,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_knit_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? //echo number_format($tot_knit_cost_mkt,2,'.',''); ?></td>
               
                <td width="100" title="Grey Fabric Cost"  align="right"><? echo number_format($tot_grey_fab_cost_mkt,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_dye_finish_cost_mkt,2,'.',''); ?></td>
              
                
                <td width="110"  align="right"><? echo number_format($tot_aop_cost_mkt,2,'.',''); ?></td>
                <td width="100" title="Finished Fabric Transfer Cost"  align="right"><? //echo number_format($tot_yarn_dye_cost_mkt,2,'.',''); ?></td> 
                 <td width="100" title="Finished Fabric Purchase Cost"  align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2,'.',''); ?></td> 
                   <td width="110"  align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2,'.',''); ?></td>
                 <td width="100" title="Tot Finished Fabric Cost"  align="right"><? echo number_format($total_finish_fabric_cost_mkt,2,'.',''); ?></td> 
                 <td width="100"  align="right"><? echo number_format($total_fabric_cost_mkt,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_trims_cost_mkt,2,'.',''); ?></td>
                <td width="100" title="Garment Printing Cost"  align="right"><? echo number_format($tot_print_amount_mkt,2,'.',''); ?></td> 
                <td width="100" title="Garment Embroidery Cost"  align="right"><? echo number_format($tot_embro_amount_mkt,2,'.',''); ?></td> 
				<td width="100" title="Garment Others Cost"  align="right"><? echo number_format($tot_Others_amount_mkt,2,'.',''); ?></td> 
                <td width="100" title="Garment Dyeing Cost"  align="right"><? echo number_format($tot_dyeing_amount_mkt,2,'.',''); ?></td> 
                <td width="100" title="Garment Wash Cost"  align="right"><? echo number_format($tot_wash_amount_mkt,2,'.',''); ?></td> 
                
               
                <td width="100"  align="right"><? echo number_format($tot_commission_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_comm_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_freight_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_test_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_inspection_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_currier_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_cm_cost_mkt,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_mkt_all_cost,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_mkt_margin,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_mkt_margin_perc,2,'.',''); ?></td>
                <td width="100"  align="right"><? //echo number_format($tot_mkt_margin,2,'.',''); ?></td>
                <td width=""  align="right"><? //echo number_format($tot_mkt_margin,2,'.',''); ?></td>
            </tr>
            <? //echo die;?>
            <tr bgcolor="#CCCCFF" onClick="change_color('tr_at','#CCCCFF')" id="tr_at" style="font-weight:bold;">
                <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="120" align="">&nbsp;</td>
                <td width="90"  align="right">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110"  align="right">&nbsp;</td>
                <td width="100"  align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Actual Total</td>
                <td width="100" align="right" ><? echo number_format($tot_ex_factory_qnty,0,'.',''); ?></td>
                <td width="110" align="right" ><? echo number_format($tot_ex_factory_val,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_yarn_cost_actual,2,'.',''); ?></td>
                <td width="100" title="Yarn Dyeing/Twisting/Re-Waxing Cost"  align="right"><? echo number_format($tot_yarn_dyeing_twist_actual,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_knit_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_grey_fab_transfer_amt_actual,2,'.',''); ?></td>
               
                <td width="100" title="Grey Fabric Cost"  align="right"><? echo number_format($tot_grey_fab_cost_actual,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_dye_finish_cost_actual,2,'.',''); ?></td>
               
               
                <td width="110"  align="right"><? echo number_format($tot_aop_cost_actual,2,'.',''); ?></td>
                <td width="100" title="Finished Fabric Transfer Cost act"  align="right"><? echo number_format($tot_fin_fab_transfer_amt_actual,2,'.',''); ?></td>
                <td width="100" title="Finished Fabric Purchase Cost act"  align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2,'.',''); ?></td>
                 <td width="110"  align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2,'.',''); ?></td>
                <td width="100" title="Tot Finished Fabric Cost act"  align="right"><? echo number_format($tot_finished_fab_cost_actual,2,'.',''); ?></td>
                 <td width="100" title="Total Fab. Cost" align="right" ><? echo number_format($tot_fabric_cost_actaul,2,'.',''); ?></td>
                <td width="110" align="right" ><? echo number_format($tot_trims_cost_actual,2,'.',''); ?></td>
                <td width="100" title="Garment Printing Cost"  align="right"><? echo number_format($tot_print_amount_actual,2,'.',''); ?></td>
                <td width="100" title="Garment Embro Cost"  align="right"><? echo number_format($tot_embro_amount_actual,2,'.',''); ?></td>
				<td width="100" title="Garment Others Cost"  align="right"><? echo number_format($tot_others_amount_actual,2,'.',''); ?></td>
                <td width="100" title="Garment Dyeing Cost"  align="right"><? echo number_format($tot_dyeing_amount_actual,2,'.',''); ?></td>
                <td width="100" title="Garment Wash Cost"  align="right"><? echo number_format($tot_wash_amount_actual,2,'.',''); ?></td>
                 
                 
               
                <td width="100"  align="right"><? echo number_format($tot_commission_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_comm_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_freight_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_test_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_inspection_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_currier_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_cm_cost_actual,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_actual_all_cost,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_actual_margin,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_actual_margin_perc,2,'.',''); ?></td>
               <td width="100"  align="right"><? echo number_format($tot_yarn_qty_cpa_actual,2,'.',''); ?></td>
               <td width=""  align="right"><? echo number_format($tot_actual_net_margin,2,'.',''); ?></td>
            </tr>
            <tr bgcolor="#FFEEFF" onClick="change_color('tr_vt','#FFEEFF')" id="tr_vt" style="font-weight:bold;">
                <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="120" align="">&nbsp;</td>
                <td width="90"  align="right">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110"  align="right">&nbsp;</td>
                <td width="100"  align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Variance Total</td>
                <td width="100" align="right" >
					<? 
                        $tot_variance_qnty=$tot_po_qnty-$tot_ex_factory_qnty;
                       if($tot_variance_qnty>0 )
					   {
					    $tot_variance_qnty_per=($tot_variance_qnty/$tot_po_qnty)*100; 
					   }
					   else $tot_variance_qnty_per=0;
                        echo number_format($tot_variance_qnty,0,'.',''); 
                    ?>
                </td>
                <td width="110" align="right" >
                    <? 
                        $tot_variance_po_value=$tot_po_value-$tot_ex_factory_val;
						if($tot_variance_po_value>0 )
					    {
                        $tot_variance_po_value_per=($tot_variance_po_value/$tot_po_value)*100; 
					    }
					   else $tot_variance_po_value_per=0; 
                        echo number_format($tot_variance_po_value,2,'.',''); 
                    ?>
                </td>
                <td width="110"  align="right">
                    <? 
                        $tot_variance_yarn_cost=$tot_yarn_cost_mkt-$tot_yarn_cost_actual;
						if($tot_variance_yarn_cost>0 )
						{
                        $tot_variance_yarn_cost_per=($tot_variance_yarn_cost/$tot_yarn_cost_mkt)*100; 
						}
						else $tot_variance_yarn_cost_per=0;
                        echo number_format($tot_variance_yarn_cost,2,'.',''); 
                    ?>
                </td>
                 <td width="100"  align="right">
                    <? 
                        $tot_variance_yarn_value_addition_cost=$tot_yarn_value_addition_cost_mkt-$tot_yarn_dyeing_twist_actual;
						if($tot_variance_yarn_value_addition_cost==0)
						{
						$tot_variance_yarn_value_addition_cost_per=0;
						}
						else
						{
						$tot_variance_yarn_value_addition_cost_per=($tot_variance_yarn_value_addition_cost/$tot_yarn_value_addition_cost_mkt)*100; 
						}
                       // $tot_variance_yarn_value_addition_cost_per=($tot_variance_yarn_value_addition_cost/$tot_yarn_value_addition_cost_mkt)*100; 
                        echo number_format($tot_variance_yarn_value_addition_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_kint_cost=$tot_knit_cost_mkt-$tot_knit_cost_actual;
						if($tot_variance_yarn_cost>0 )
						{
                        $tot_variance_knit_cost_per=($tot_variance_kint_cost/$tot_knit_cost_mkt)*100; 
						}
						else $tot_variance_knit_cost_per=0;
						
                        echo number_format($tot_variance_kint_cost,2,'.',''); 
                    ?>
                </td>
                 <td width="100"  align="right">
                    <? 
                        //$tot_variance_kint_cost=$tot_knit_cost_mkt-$tot_knit_cost_actual;
                       // $tot_variance_knit_cost_per=($tot_variance_kint_cost/$tot_knit_cost_mkt)*100; 
                       // echo number_format($tot_variance_kint_cost,2,'.',''); 
                    ?>
                </td>
               
                 <td width="100"  title="Grey Fabric Cost" align="right">
                    <? 
                        $tot_variance_grey_fab_cost=$tot_grey_fab_cost_mkt-$tot_grey_fab_cost_actual;
                      if($tot_variance_grey_fab_cost==0)
						{
							$tot_variance_grey_fab_cost_per=0;
						}
						else
						{
							 $tot_variance_grey_fab_cost_per=($tot_variance_grey_fab_cost/$tot_grey_fab_cost_mkt)*100; 
						}    
                        echo number_format($tot_variance_grey_fab_cost,2,'.',''); 
                    ?>
                </td>
                <td width="110"  align="right">
                    <? 
                        $tot_variance_dye_finish_cost=$tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual;
                      if($tot_variance_dye_finish_cost==0)
						{
							$tot_variance_dye_finish_cost_per=0;
						}
						else
						{
							 $tot_variance_dye_finish_cost_per=($tot_variance_dye_finish_cost/$tot_dye_finish_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_dye_finish_cost,2,'.','');
                    ?>
                </td>
              
                <td width="110"  align="right">
                    <? 
                        $tot_variance_aop_cost=$tot_aop_cost_mkt-$tot_aop_cost_actual;
                      if($tot_variance_aop_cost==0)
						{
							$tot_variance_aop_cost_per=0;
						}
						else
						{
							  $tot_variance_aop_cost_per=($tot_variance_aop_cost/$tot_aop_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_aop_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  title="Fabric Purchase Trans Cost"  align="right">
                    <? 
                        /*$tot_variance_aop_cost=$tot_aop_cost_mkt-$tot_aop_cost_actual;
                        $tot_variance_aop_cost_per=($tot_variance_aop_cost/$tot_aop_cost_mkt)*100; 
                        echo number_format($tot_variance_aop_cost,2,'.','');*/ 
                    ?>
                </td>
                <td width="100" title="Fabric Purchase Cost" align="right">
                    <? 
                       $tot_variance_fin_fabric_purchase_cost=$tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual;
                       if($tot_variance_fin_fabric_purchase_cost==0)
						{
							$tot_variance_fin_fabric_purchase_cost_per=0;
						}
						else
						{
							 $tot_variance_fin_fabric_purchase_cost_per=($tot_variance_fin_fabric_purchase_cost/$tot_fabric_purchase_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_fin_fabric_purchase_cost,2,'.','');
                    ?>
                </td>
                  <td width="110"  align="right">
                    <? 
                        $tot_variance_fabric_purchase_cost=$tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual;
                        if($tot_variance_fabric_purchase_cost==0)
						{
							$tot_variance_fabric_purchase_cost_per=0;
						}
						else
						{
							  $tot_variance_fabric_purchase_cost_per=($tot_variance_fabric_purchase_cost/$tot_fabric_purchase_cost_mkt)*100; 
						}
                        echo number_format($tot_variance_fabric_purchase_cost,2,'.','');
                    ?>
                </td>
                <td width="100" title="Tot Fin Fabric  Cost"  align="right">
                    <? 
                       $tot_variance_fin_fab_cost=$total_finish_fabric_cost_mkt-$tot_finished_fab_cost_actual;
                       if($tot_variance_fin_fab_cost==0)
						{
							$tot_variance_fin_fab_cost_per=0;
						}
						else
						{
							   $tot_variance_fin_fab_cost_per=($tot_variance_fin_fab_cost/$total_finish_fabric_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_fin_fab_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100" title="Tot Fabric  Cost"  align="right">
                    <? 
                        $tot_variance_fabric_cost_mkt=$total_fabric_cost_mkt-$tot_fabric_cost_actaul;
                      if($tot_variance_fabric_cost_mkt==0)
						{
							$tot_variance_fabric_cost_mkt_per=0;
						}
						else
						{
							 $tot_variance_fabric_cost_mkt_per=($tot_variance_fabric_cost_mkt/$tot_fabric_cost_mkt)*100;  
						} 
						
                        echo number_format($tot_variance_fabric_cost_mkt,2,'.','');
                    ?>
                </td>
                <td width="110"  align="right">
                    <? 
                        $tot_variance_trims_cost=$tot_trims_cost_mkt-$tot_trims_cost_actual;
                       if($tot_variance_trims_cost==0)
						{
							$tot_variance_trims_cost_per=0;
						}
						else
						{
							   $tot_variance_trims_cost_per=($tot_variance_trims_cost/$tot_trims_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_trims_cost,2,'.','');
                    ?>
                </td>
                  <td width="100" title="Garment Printing Cost" align="right">
                    <? 
                        $tot_variance_print_amount=$tot_print_amount_mkt-$tot_print_amount_actual;
                       if($tot_variance_print_amount==0)
						{
							$tot_variance_print_per=0;
						}
						else
						{
							   $tot_variance_print_per=($tot_variance_print_amount/$tot_print_amount_mkt)*100; 
						} 
                        echo number_format($tot_variance_print_amount,2,'.','');
                    ?>
                </td>
                 <td width="100" title="Garment Embro  Cost"  align="right">
                    <? 
                       $tot_variance_embro_cost=$tot_embro_amount_mkt-$tot_embro_amount_actual;//tot_wash_amount_mkt-tot_wash_amount_actual
                        if($tot_variance_embro_cost==0)
						{
							$tot_variance_embro_cost_per=0;
						}
						else
						{
							$tot_variance_embro_cost_per=($tot_variance_embro_cost/$tot_embro_amount_mkt)*100; 
						} 
                        echo number_format($tot_variance_embro_cost,2,'.','');
                    ?>
                </td>
				<td width="100" title="Garment Embro  Cost"  align="right">
                    <? 
                       $tot_variance_others_cost=$tot_others_amount_mkt-$tot_others_amount_actual;//tot_wash_amount_mkt-tot_wash_amount_actual
                        if($tot_variance_others_cost==0)
						{
							$tot_variance_others_cost_per=0;
						}
						else
						{
							$tot_variance_others_cost_per=($tot_variance_others_cost/$tot_others_amount_mkt)*100; 
						} 
                        echo number_format($tot_variance_others_cost,2,'.','');
                    ?>
                </td>
                 <td width="100" title="Garment Dyeing Cost"  align="right">
                    <? 
					
                        $tot_variance_dyeing_cost=$tot_dyeing_amount_mkt-$tot_dyeing_amount_actual;
                       if($tot_variance_dyeing_cost==0)
						{
							$tot_variance_dyeing_cost_per=0;
						}
						else
						{
							 $tot_variance_dyeing_cost_per=($tot_variance_dyeing_cost/$tot_dyeing_amount_mkt)*100; 
						} 
						//echo $tot_variance_dyeing_cost.'='.$tot_dyeing_amount_mkt.', ';
                        echo number_format($tot_variance_dyeing_cost,2,'.',''); 
                    ?>
                </td>
                 <td width="100" title="Garment Wash Cost"  align="right">
                    <? 
                       $tot_variance_wash_cost=$tot_wash_amount_mkt-$tot_wash_amount_actual;
                      if($tot_variance_wash_cost==0)
						{
							$tot_variance_wash_cost_per=0;
						}
						else
						{
							 $tot_variance_wash_cost_per=($tot_variance_wash_cost/$tot_wash_amount_mkt)*100; 
						}   
                        echo number_format($tot_variance_wash_cost,2,'.','');
                    ?>
                </td>
               
                <td width="100" align="right">
                    <? 
                        $tot_variance_commission_cost=$tot_commission_cost_mkt-$tot_commission_cost_actual;
                        if($tot_variance_commission_cost==0)
						{
							$tot_variance_commission_cost_per=0;
						}
						else
						{
							 $tot_variance_commission_cost_per=($tot_variance_commission_cost/$tot_commission_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_commission_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_comm_cost=$tot_comm_cost_mkt-$tot_comm_cost_actual;
                       if($tot_variance_comm_cost==0)
						{
							$tot_variance_comm_cost_per=0;
						}
						else
						{
							  $tot_variance_comm_cost_per=($tot_variance_comm_cost/$tot_comm_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_comm_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_freight_cost=$tot_freight_cost_mkt-$tot_freight_cost_actual;
                      if($tot_variance_freight_cost==0)
						{
							$tot_variance_freight_cost_per=0;
						}
						else
						{
							$tot_variance_freight_cost_per=($tot_variance_freight_cost/$tot_freight_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_freight_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_test_cost=$tot_test_cost_mkt-$tot_test_cost_actual;
                        if($tot_variance_test_cost==0)
						{
							$tot_variance_test_cost_per=0;
						}
						else
						{
							 $tot_variance_test_cost_per=($tot_variance_test_cost/$tot_test_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_test_cost,2,'.','');
                    ?>
                </td>
                <td width="100" align="right">
                    <? 
                        $tot_variance_inspection_cost=$tot_inspection_cost_mkt-$tot_inspection_cost_actual;
                       if($tot_variance_inspection_cost==0)
						{
							$tot_variance_inspection_cost_per=0;
						}
						else
						{
							  $tot_variance_inspection_cost_per=($tot_variance_inspection_cost/$tot_inspection_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_inspection_cost,2,'.','');
                    ?>
                </td>
                <td width="100" align="right">
                    <? 
                        $tot_variance_currier_cost=$tot_currier_cost_mkt-$tot_currier_cost_actual;
                       if($tot_variance_currier_cost==0)
						{
							$tot_variance_currier_cost_per=0;
						}
						else
						{
							 $tot_variance_currier_cost_per=($tot_variance_currier_cost/$tot_currier_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_currier_cost,2,'.','');
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_cm_cost=$tot_cm_cost_mkt-$tot_cm_cost_actual;
                        if($tot_variance_cm_cost==0)
						{
							$tot_variance_cm_cost_per=0;
						}
						else
						{
							$tot_variance_cm_cost_per=($tot_variance_cm_cost/$tot_cm_cost_mkt)*100; 
						} 
                        echo number_format($tot_variance_cm_cost,2,'.',''); 
                    ?>
                </td>
                <td width="100" align="right">
                    <? 
                        $tot_variance_all_cost=$tot_mkt_all_cost-$tot_actual_all_cost;
                      if($tot_variance_all_cost==0)
						{
							$tot_variance_all_cost_per=0;
						}
						else
						{
							$tot_variance_all_cost_per=($tot_variance_all_cost/$tot_mkt_all_cost)*100; 
						} 
                        echo number_format($variance_all_cost,2,'.','');
                    ?>
                </td>
                <td width="100"  align="right">
                    <? 
                        $tot_variance_margin_cost=$tot_mkt_margin-$tot_actual_margin;
                        $tot_variance_margin_cost_per=($tot_variance_margin_cost/$tot_mkt_margin)*100; 
                        echo number_format($tot_variance_margin_cost,2,'.','');
                    ?>
                </td>
                <td width="100" align="right">
                    <? 
                        $tot_variance_per_cost=$tot_mkt_margin_perc-$tot_actual_margin_perc;
                        $tot_variance_per_cost_per=($tot_variance_per_cost/$tot_mkt_margin_perc)*100; 
                        echo number_format($tot_variance_per_cost,2,'.','');
                    ?>
                </td>
                 <td width="100"  align="right">
                    <? 
                       /* $tot_variance_margin_cost=$tot_mkt_margin-$tot_actual_margin;
                        $tot_variance_margin_cost_per=($tot_variance_margin_cost/$tot_mkt_margin)*100; 
                        echo number_format($tot_variance_margin_cost,2,'.','');*/
                    ?>
                </td>
                 <td width=""  align="right">
                    <? 
                       /* $tot_variance_margin_cost=$tot_mkt_margin-$tot_actual_margin;
                        $tot_variance_margin_cost_per=($tot_variance_margin_cost/$tot_mkt_margin)*100; 
                        echo number_format($tot_variance_margin_cost,2,'.','');*/
                    ?>
                </td>
            </tr>
            <tr bgcolor="#CCCCEE" onClick="change_color('tr_vpt','#CCCCEE')" id="tr_vpt" style="font-weight:bold;">
                <td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="120" align=""></td>
                <td width="90"  align="right">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110" align="right">&nbsp;</td>
                <td width="100"  align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Variance (%) Total</td>
                <td width="100" align="right" ><? echo number_format($tot_variance_qnty_per,0,'.',''); ?></td>
                <td width="110" align="right" ><? echo number_format($tot_variance_po_value_per,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_variance_yarn_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_yarn_value_addition_cost_per,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_variance_knit_cost_per,2,'.',''); ?></td>
                <td width="100" title="Grey Fabric Transfer Cost" align="right"><? //echo number_format($tot_variance_knit_cost_per,2,'.',''); ?></td>
                
               <td width="100" title="Grey Fabric Cost" align="right"><? echo number_format($tot_variance_grey_fab_cost_per,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_variance_dye_finish_cost_per ,2,'.',''); ?></td>
               
                <td width="110"  align="right"><? echo number_format($tot_variance_aop_cost_per,2,'.',''); ?></td>
                 <td width="100" title="Finished Fabric Transfer Cost"  align="right"><? //echo number_format($tot_variance_aop_cost_per,2,'.',''); ?></td>
                 <td width="100"  title="Finished Fabric Purchase Cost"  align="right"><? echo number_format($tot_variance_fin_fabric_purchase_cost_per,2,'.',''); ?></td>
                 <td width="110" align="right"><? echo number_format($tot_variance_fabric_purchase_cost_per ,2,'.',''); ?></td>
                 <td width="100"  title="Finished Fabric Cost"  align="right"><? echo number_format($tot_variance_fin_fab_cost_per,2,'.',''); ?></td>
                  <td width="100"  title="Total Fabric Cost"  align="right"><? echo number_format($tot_variance_fabric_cost_mkt_per,2,'.',''); ?></td>
                <td width="110"  align="right"><? echo number_format($tot_variance_trims_cost_per,2,'.',''); ?></td>
                 <td width="100"  title="Garment Printing Cost" align="right"><? echo number_format($tot_variance_print_per,2,'.',''); ?></td>
                 <td width="100"  title="Garment Embro Cost" align="right"><? echo number_format($tot_variance_embro_cost_per,2,'.',''); ?></td>
				 <td width="100"  title="Garment OthersCost" align="right"><? echo number_format($tot_variance_others_cost_per,2,'.',''); ?></td>
                 <td width="100"  title="Garment Dyeing Cost"  align="right"><? echo number_format($tot_variance_dyeing_cost_per,2,'.',''); ?></td>
                 <td width="100"  title="Garment Wash Cost" align="right"><? echo number_format($tot_variance_wash_cost_per,2,'.',''); ?></td>
                  
               
                <td width="100"  align="right"><? echo number_format($tot_variance_commission_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_comm_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_freight_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_test_cost_per,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_variance_inspection_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_currier_cost_per,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_variance_cm_cost_per,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_variance_all_cost_per,2,'.',''); ?></td>
                <td width="100" align="right"><? echo number_format($tot_variance_margin_cost_per,2,'.',''); ?></td>
                <td width="100"  align="right"><? echo number_format($tot_variance_per_cost_per,2,'.',''); ?></td>
                 <td width="100" align="right"><? //echo number_format($tot_variance_margin_cost_per,2,'.',''); ?></td>
                  <td width="" align="right"><? //echo number_format($tot_variance_margin_cost_per,2,'.',''); ?></td>
            </tr>
        </table>
        <br />
        <table width="4330">
            <tr>
                <td width="850" valign="top">
                    <div align="center" style="width:450px" id="div_summary"><input type="button" value="Print Preview" class="formbutton" onClick="new_window2('company_div','summary_full')" /></div>
                    <br /><? $po_ids=implode(',',$po_ids_array);$job_ids=rtrim($job_ids,','); $job_nos=implode(',',array_unique(explode(",",$job_ids))); ?>
                    <div id="summary_full"> <font color="#FF0000" style="display:none">*Yarn Dyeing Charge included with actual Yarn Cost</font>
                        <div align="center" id="company_div" style="visibility:hidden; font-size:24px;width:700px"><b><? echo $company_arr[$company_name].'<br>'; 
						// echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to));
						?></b></div>
						<table width="850" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
						<thead>
								<th>Buyer</th>
								<th>Job</th>
								<th>Style</th>
								<th>Order</th>
					</thead>
							<?
							
							foreach($buyer_name_array as $key=>$value)
							{
							$style_arr=rtrim($style_wise_arr[$key],',');
							$stylearr=implode(",",array_unique(explode(",",$style_arr)));
							$job_arr=rtrim($job_wise_arr[$key],',');
							$jobarr=implode(",",array_unique(explode(",",$job_arr)));
							$po_arr=rtrim($po_wise_arr[$key],',');
							$poarr=implode(",",array_unique(explode(",",$po_arr)));
							?>
								<tr>
									<td><p style="word-break:break-all"><? echo $value ;?></p></td>
									<td><p style="word-break:break-all"><? echo $jobarr;?></p></td>
									<td><p style="word-break:break-all"><? echo $stylearr ;?></p></td>
									<td><p style="word-break:break-all"><? echo $poarr;?></p></td>
								</tr>	

							<? }
							?>
						</table>
                        <table width="850" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                            <tr>
                                <th colspan="9">Summary</th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="180">Particulars</th>
                                <th width="130">Pre Costing</th>
                                <th width="80">%</th>
                                <th width="130">At Actual</th>
                                <th width="80">%</th>
                                <th width="80">Variance</th>
                                <th width="80">%</th>
                                <th>Disclosure</th>
                            </tr> 
							<?
								$bgcolor1='#E9F3FF';
								$bgcolor2='#FFFFFF';
							?>
                            </thead>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_1','<? echo $bgcolor1; ?>')" id="trtd_1">
                                <td align="center">1</td>
                                <td>PO/Shipment Value</td>
                                <td align="right"><? echo fn_number_format($tot_po_value,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo fn_number_format($tot_ex_factory_val,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                                <td align="right"  title="Variance/PO-Shipment Value Mkt*100"><? if($tot_po_value>0 ) echo fn_number_format((($tot_ex_factory_val-$tot_po_value)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_2','<? echo $bgcolor2; ?>')" id="trtd_2">
                                <td colspan="9"><b>Cost</b></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_3','<? echo $bgcolor1; ?>')" id="trtd_3">
                                <td align="center">2</td>
                                <td>Grey Yarn Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $po_ids."**".$job_nos; ?>','mkt_yarn_cost','Grey Yarn Mkt. Cost Details')"><? echo fn_number_format($tot_yarn_cost_mkt,2); //$tot_yarn_qty_actual?></a></td>
                                <td align="right"><? if($tot_yarn_cost_mkt>0 ) echo fn_number_format(($tot_yarn_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','yarn_cost_actual','Yarn Cost Details')"><? echo fn_number_format($tot_yarn_cost_actual,2); //$tot_yarn_qty_actual;?></a></td>
                                <td align="right"><? if($tot_yarn_cost_actual>0 ) echo fn_number_format(($tot_yarn_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_yarn_cost_mkt-$tot_yarn_cost_actual,2);//$tot_yarn_amt_actual ?></td>
                                <td align="right"  title="Variance/Grey Yarn Cost Mkt*100"><? echo fn_number_format((($tot_yarn_cost_mkt-$tot_yarn_cost_actual)/$tot_yarn_cost_mkt)*100,2); ?></td>
                                 <td align="right"><?  if($tot_yarn_qty_actual>$tot_yarn_cost_mkt) echo "Returnable Yarn Qty..".$tot_yarn_qty_actual."And Cost..".$tot_yarn_amt_actual; else echo " ";?></td>
                            </tr>
                             <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_4','<? echo $bgcolor2; ?>')" id="trtd_4">
                                <td align="center">3</td>
                                <td>Yarn  Value Addition Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $po_ids."**".$job_nos; ?>','mkt_yarn_dyeing_twisting_cost','Yarn Dyeing Cost Details')"><? echo fn_number_format($tot_yarn_value_addition_cost_mkt,2); ?></a><? //echo number_format($tot_yarn_value_addition_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_yarn_value_addition_cost_mkt>0 )  echo fn_number_format(($tot_yarn_value_addition_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','yarn_dye_twist_cost_actual_summary','Yarn Dyeing & Twist Cost Details','2','900px')"><? echo fn_number_format($tot_yarn_dyeing_twist_actual,2); ?></a></td>
                                <td align="right"><? if($tot_yarn_value_addition_cost_mkt>0 ) echo fn_number_format(($tot_yarn_value_addition_cost_mkt/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_yarn_value_addition_cost_mkt-$tot_yarn_dyeing_twist_actual,2); ?></td>
                                 <td align="right" title="Variance/Yarn Value  Addition Cost Mkt*100"><? if($tot_yarn_value_addition_cost_mkt-$tot_yarn_dyeing_twist_actual>0 ) echo fn_number_format((($tot_yarn_value_addition_cost_mkt-$tot_yarn_dyeing_twist_actual)/$tot_yarn_value_addition_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_5','<? echo $bgcolor2; ?>')" id="trtd_5">
                                <td align="center">4</td>
                                <td>Knitting Cost</td>
                                <td align="right"><? echo fn_number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_knit_cost_mkt>0)  echo fn_number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','knit_cost_actual','Knitting Cost Details')"><? echo fn_number_format($tot_knit_cost_actual,2); ?></a><? //echo fn_number_format($tot_knit_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_knit_cost_actual>0) echo fn_number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Knitting Cost Mkt*100"><?  if($tot_knit_cost_mkt-$tot_knit_cost_actual>0) echo fn_number_format((($tot_knit_cost_mkt-$tot_knit_cost_actual)/$tot_knit_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_6','<? echo $bgcolor2; ?>')" id="trtd_6">
                                <td align="center">5</td>
                                <td>Grey Fabric Transfer Cost</td>
                                <td align="right"><? //echo fn_number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? //echo fn_number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','grey_fabric_transfer_cost_actual','grey fabric transfer cost')"><? echo fn_number_format($tot_grey_fab_transfer_amt_actual,2); ?></a><?//echo fn_number_format($tot_grey_fab_transfer_amt_actual,2); ?></td>
                                <td align="right"><? //echo fn_number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo fn_number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                                 <td align="right"><? echo "N/A"; ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                             <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_7','<? echo $bgcolor2; ?>')" id="trtd_7">
                                <td align="center">6</td>
                                <td>Grey Fabric Cost =2+3+4+5</td>
                                <td align="right"><? 
								$grey_fab_cost_mkt_sum=$tot_yarn_cost_mkt+$tot_yarn_value_addition_cost_mkt+$tot_knit_cost_mkt;
								$grey_fab_cost_act_sum=$tot_yarn_cost_actual+$tot_yarn_dyeing_twist_actual+$tot_knit_cost_actual+$tot_grey_fab_transfer_amt_actual;
								
								echo fn_number_format($grey_fab_cost_mkt_sum,2); ?></td>
                                <td align="right"><? if($grey_fab_cost_mkt_sum>0) echo fn_number_format(($grey_fab_cost_mkt_sum/$tot_po_value)*100,2); ?></td>
                                <td align="right" title=""><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','knitting_cost','Knitting Cost Details')"><? //echo fn_number_format($tot_grey_fab_cost_actual,2); ?></a><? echo fn_number_format($grey_fab_cost_act_sum,2); ?></td>
                                <td align="right"><? if($grey_fab_cost_act_sum>0) echo fn_number_format(($grey_fab_cost_act_sum/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($grey_fab_cost_mkt_sum-$grey_fab_cost_act_sum,2); ?></td>
                                 <td align="right" title="Variance/Grey Fab Cost Mkt*100"><? if($grey_fab_cost_mkt_sum-$grey_fab_cost_act_sum>0) echo fn_number_format((($grey_fab_cost_mkt_sum-$grey_fab_cost_act_sum)/$grey_fab_cost_mkt_sum)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_8','<? echo $bgcolor2; ?>')" id="trtd_8">
                                <td align="center">7</td>
                                <td>Dye & Fin Cost</td>
                                <td align="right"><? echo fn_number_format($tot_dye_finish_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_dye_finish_cost_mkt>0) echo fn_number_format(($tot_dye_finish_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $po_ids; ?>','dye_finish_cost_actual2','Actual Dyeing & Finish Cost Details','900px')"><? echo fn_number_format($tot_dye_finish_cost_actual,2,'.',''); ?></a></td>
                                <td align="right"><? if($tot_dye_finish_cost_actual>0)  echo fn_number_format(($tot_dye_finish_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Dye & Fin Cost Mkt*100"><?  if($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual>0) echo fn_number_format((($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual)/$tot_dye_finish_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_9','<? echo $bgcolor1; ?>')" id="trtd_9">
                                <td align="center">8</td>
                                <td>AOP  Cost</td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_aop_cost_actual>0) echo fn_number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/AOP Cost Mkt*100"><? if($tot_aop_cost_mkt-$tot_aop_cost_actual>0)  echo fn_number_format((($tot_aop_cost_mkt-$tot_aop_cost_actual)/$tot_aop_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_10','<? echo $bgcolor2; ?>')" id="trtd_10">
                                <td align="center">9</td>
                                <td>Finished Fabric Transfer Cost</td>
                                <td align="right"><? //echo fn_number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? // echo fn_number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_fin_fab_transfer_amt_actual,2); ?></td>
                                <td align="right"><? //echo fn_number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo fn_number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                                 <td align="right"><? echo "N/A"; ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_11','<? echo $bgcolor1; ?>')" id="trtd_11">
                                <td align="center">10</td>
                                <td>Finished Fabric Cost =6+7+8+9</td>
                                <td align="right"><? $tot_fin_fab_cost_sum_mkt=$tot_yarn_cost_mkt+$tot_yarn_value_addition_cost_mkt+$tot_knit_cost_mkt+$tot_dye_finish_cost_mkt+$tot_aop_cost_mkt;
								//$tot_fin_fabCost=$tot_yarn_cost_mkt+$tot_yarn_value_addition_cost_mkt+$tot_knit_cost_mkt+$tot_dye_finish_cost_mkt+$tot_aop_cost_mkt;
								echo fn_number_format($tot_fin_fab_cost_sum_mkt,2); ?></td>
                                <td align="right"><? if($tot_fin_fab_cost_sum_mkt>0) echo fn_number_format(($tot_fin_fab_cost_sum_mkt/$tot_po_value)*100,2);else echo ""; ?></td>
                                <td align="right"><? $tot_fin_fab_cost_sum_actual=$tot_grey_fab_cost_actual+$tot_dye_finish_cost_actual+$tot_aop_cost_actual+$tot_fin_fab_transfer_amt_actual;
								echo fn_number_format($tot_grey_fab_cost_actual+$tot_dye_finish_cost_actual+$tot_aop_cost_actual+$tot_fin_fab_transfer_amt_actual,2); ?></td>
                                <td align="right"><? if($tot_fin_fab_cost_sum_actual>0) echo fn_number_format(($tot_fin_fab_cost_sum_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_fin_fab_cost_sum_mkt-$tot_fin_fab_cost_sum_actual,2); ?></td>
                                 <td align="right" title="Variance/Finished Fabric Cost Mkt*100"><? if($tot_fin_fab_cost_sum_mkt-$tot_fin_fab_cost_sum_actual>0) echo fn_number_format((($tot_fin_fab_cost_sum_mkt-$tot_fin_fab_cost_sum_actual)/$tot_fin_fab_cost_sum_mkt)*100,2);  ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_12','<? echo $bgcolor2; ?>')" id="trtd_12">
                                <td align="center">11</td>
                                <td>Fabric Purchase Cost</td>
                                <td align="right"><? echo fn_number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_fabric_purchase_cost_mkt>0) echo fn_number_format(($tot_fabric_purchase_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','fabric_purchase_cost','Fabric Purchase Cost Details')"><? echo fn_number_format($tot_fabric_purchase_cost_actual,2); ?></a></td>
                                <td align="right"><? if($tot_fabric_purchase_cost_actual>0) echo fn_number_format(($tot_fabric_purchase_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Fabric Purchase Cost Mkt*100"><? if($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual>0) echo fn_number_format((($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual)/$tot_fabric_purchase_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                             <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_27','<? echo $bgcolor2; ?>')" id="trtd_27">
                                <td align="center">12</td>
                                <td>Total Fabric Cost(=10+11)</td>
                                <td align="right"><? $tot_fab_cost_mkt=$tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt;
								echo fn_number_format($tot_fab_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt>0) echo fn_number_format((($tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? 
								$tot_fabric_cost_actual=$tot_grey_fab_cost_actual+$tot_dye_finish_cost_actual+$tot_aop_cost_actual+$tot_fin_fab_transfer_amt_actual+$tot_fabric_purchase_cost_actual;
								echo fn_number_format($tot_grey_fab_cost_actual+$tot_dye_finish_cost_actual+$tot_aop_cost_actual+$tot_fin_fab_transfer_amt_actual+$tot_fabric_purchase_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_fabric_cost_actual>0) echo fn_number_format(($tot_fabric_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? $tot_fab_cost_vairance=($tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt)-$tot_fabric_cost_actual;
								echo fn_number_format(($tot_fin_fab_cost_sum_mkt+$tot_fabric_purchase_cost_mkt)-$tot_fabric_cost_actual,2); ?></td>
                                 <td align="right"><?  if($tot_fab_cost_vairance>0) echo fn_number_format(($tot_fab_cost_vairance/$tot_fab_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? echo 'N/A'; ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_13','<? echo $bgcolor1; ?>')" id="trtd_13">
                                <td align="center">13</td>
                                <td>Trims Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $po_ids."**".$job_nos; ?>','trims_cost_mkt','Trims Cost Details','800px')"><? echo number_format($tot_trims_cost_mkt,2,'.',''); ?></a></td>
                                <td align="right"><? if($tot_trims_cost_mkt>0) echo fn_number_format(($tot_trims_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $po_ids; ?>','trims_cost_actual','Trims Actual Cost Details','900px')"><? echo fn_number_format($tot_trims_cost_actual,2,'.',''); ?></a></td>
                                <td align="right"><? if($tot_trims_cost_actual>0)  echo fn_number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_trims_cost_mkt-$tot_trims_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Trim Cost Mkt*100"><? if($tot_trims_cost_mkt-$tot_trims_cost_actual>0) echo fn_number_format((($tot_trims_cost_mkt-$tot_trims_cost_actual)/$tot_trims_cost_mkt)*100,2);  ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_14','<? echo $bgcolor2; ?>')" id="trtd_14">
                                <td align="center">14</td>
                                <td>Printing Cost</td>
                                <td align="right"><? echo fn_number_format($tot_print_amount_mkt,2); ?></td>
                                <td align="right"><?  echo fn_number_format(($tot_print_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_print_amount_actual,2); ?></td>
                                <td align="right"><? if($tot_trims_cost_actual>0) echo fn_number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_print_amount_mkt-$tot_print_amount_actual,2); ?></td>
                                <td align="right" title="Variance/Printing Cost Mkt*100"><? if($tot_print_amount_mkt-$tot_print_amount_actual>0) echo fn_number_format((($tot_print_amount_mkt-$tot_print_amount_actual)/$tot_print_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">15</td>
                                <td> Embroidery Cost</td>
                                <td align="right"><? echo fn_number_format($tot_embro_amount_mkt,2); ?></td>
                                <td align="right"><? if($tot_embro_amount_mkt>0) echo fn_number_format(($tot_embro_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_embro_amount_actual,2); ?></td>
                                <td align="right"><? if($tot_embro_amount_actual>0 && $tot_ex_factory_val>0) echo fn_number_format(($tot_embro_amount_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_embro_amount_mkt-$tot_embro_amount_actual,2); ?></td>
                                 <td align="right" title="Variance/Embroidery Cost Mkt*100"><? if($tot_embro_amount_mkt-$tot_embro_amount_actual>0) echo fn_number_format((($tot_embro_amount_mkt-$tot_embro_amount_actual)/$tot_embro_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
							<tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">16</td>
                                <td> Others Cost</td>
                                <td align="right"><? echo fn_number_format($tot_others_amount_mkt,2); ?></td>
                                <td align="right"><? if($tot_others_amount_mkt>0)  echo fn_number_format(($tot_others_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_others_actual,2); ?></td>
                                <td align="right"><?  if($tot_others_amount_actual>0)  echo fn_number_format(($tot_others_amount_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_others_amount_mkt-$tot_others_amount_actual,2); ?></td>
                                <td align="right" title="Variance/Embroidery Cost Mkt*100"><?  if($tot_others_amount_mkt-$tot_others_amount_actual>0) echo fn_number_format((($tot_others_amount_mkt-$tot_others_amount_actual)/$tot_others_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_16','<? echo $bgcolor2; ?>')" id="trtd_16">
                                <td align="center">17</td>
                                <td>Gmt Dyeing  Cost</td>
                                <td align="right"><? echo fn_number_format($tot_dyeing_amount_mkt,2); ?></td>
                                <td align="right"><?  if($tot_dyeing_amount_mkt>0) echo fn_number_format(($tot_dyeing_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_dyeing_amount_actual,2); ?></td>
                                <td align="right"><? if($tot_dyeing_amount_actual>0) echo fn_number_format(($tot_dyeing_amount_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_dyeing_amount_mkt-$tot_dyeing_amount_actual,2); ?></td>
                                 <td align="right" title="Variance/Gmt Dyeing Cost Mkt*100"><?  if($tot_dyeing_amount_mkt-$tot_dyeing_amount_actual>0) echo fn_number_format((($tot_dyeing_amount_mkt-$tot_dyeing_amount_actual)/$tot_dyeing_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">18</td>
                                <td>Washing  Cost</td>
                                <td align="right"><? echo fn_number_format($tot_wash_amount_mkt,2); ?></td>
                                <td align="right"><?  if($tot_wash_amount_mkt>0)  echo fn_number_format(($tot_wash_amount_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_wash_amount_actual,2); ?></td>
                                <td align="right"><?  if($tot_wash_amount_actual>0 && $tot_ex_factory_val>0) echo fn_number_format(($tot_wash_amount_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_wash_amount_mkt-$tot_wash_amount_actual,2); ?></td>
                                <td align="right" title="Variance/Washing Cost Mkt*100"><?  if($tot_wash_amount_mkt-$tot_wash_amount_actual>0)  echo fn_number_format((($tot_wash_amount_mkt-$tot_wash_amount_actual)/$tot_wash_amount_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr> 
                            
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_18','<? echo $bgcolor2; ?>')" id="trtd_18">
                                <td align="center">19</td>
                                <td>Commission Cost</td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_commission_cost_mkt>0)  echo fn_number_format(($tot_commission_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_commission_cost_actual>0  && $tot_ex_factory_val>0) echo fn_number_format(($tot_commission_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_mkt-$tot_commission_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Commission Cost Mkt*100"><? if($tot_commission_cost_mkt-$tot_commission_cost_actual>0)  echo fn_number_format((($tot_commission_cost_mkt-$tot_commission_cost_actual)/$tot_commission_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_19','<? echo $bgcolor1; ?>')" id="trtd_19">
                                <td align="center">20</td>
                                <td>Commercial Cost</td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_comm_cost_mkt>0) echo fn_number_format(($tot_comm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_comm_cost_actual>0) echo fn_number_format(($tot_comm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_mkt-$tot_comm_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Commercial Cost Mkt*100"><?  if($tot_comm_cost_mkt-$tot_comm_cost_actual>0) echo fn_number_format((($tot_comm_cost_mkt-$tot_comm_cost_actual)/$tot_comm_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_20','<? echo $bgcolor2; ?>')" id="trtd_20">
                                <td align="center">21</td>
                                <td>Freight Cost</td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_freight_cost_mkt>0) echo fn_number_format(($tot_freight_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_freight_cost_actual>0 && $tot_ex_factory_val>0) echo fn_number_format(($tot_freight_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_mkt-$tot_freight_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Freight Cost Mkt*100"><?  if($tot_freight_cost_mkt-$tot_freight_cost_actual>0) echo fn_number_format((($tot_freight_cost_mkt-$tot_freight_cost_actual)/$tot_freight_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_21','<? echo $bgcolor1; ?>')" id="trtd_21">
                                <td align="center">22</td>
                                <td>Testing Cost</td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_test_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_test_cost_actual>0 && $tot_ex_factory_val>0) echo fn_number_format(($tot_test_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_mkt-$tot_test_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Testing Cost Mkt*100"><?  if($tot_test_cost_mkt-$tot_test_cost_actual>0) echo fn_number_format((($tot_test_cost_mkt-$tot_test_cost_actual)/$tot_test_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_22','<? echo $bgcolor2; ?>')" id="trtd_22">
                                <td align="center">23</td>
                                <td>Inspection Cost</td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_inspection_cost_mkt>0) echo fn_number_format(($tot_inspection_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_inspection_cost_actual>0 && $tot_ex_factory_val>0)  echo fn_number_format(($tot_inspection_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_mkt-$tot_inspection_cost_actual,2); ?></td>
                                <td align="right" title="Variance/Inspection Cost Mkt*100"><?  if($tot_inspection_cost_mkt-$tot_inspection_cost_actual>0) echo fn_number_format((($tot_inspection_cost_mkt-$tot_inspection_cost_actual)/$tot_inspection_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_23','<? echo $bgcolor1; ?>')" id="trtd_23">
                                <td align="center">24</td>
                                <td>Courier Cost</td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_currier_cost_mkt>0) echo fn_number_format(($tot_currier_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_currier_cost_actual>0 && $tot_currier_cost_actual>0) echo fn_number_format(($tot_currier_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_mkt-$tot_currier_cost_actual,2); ?></td>
                                 <td align="right" title="Variance/Courier Cost Mkt*100"><?  if($tot_currier_cost_mkt-$tot_currier_cost_actual>0) echo fn_number_format((($tot_currier_cost_mkt-$tot_currier_cost_actual)/$tot_currier_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_24','<? echo $bgcolor2; ?>')" id="trtd_24">
                                <td align="center">25</td>
                                <td>CM</td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_mkt,2); ?></td>
                                <td align="right"><?  if($tot_cm_cost_mkt>0) echo fn_number_format(($tot_cm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_cm_cost_actual>0 && $tot_cm_cost_actual>0) echo fn_number_format(($tot_cm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_mkt-$tot_cm_cost_actual,2); ?></td>
                                <td align="right" title="Variance/CM Cost Mkt*100"><?  if($tot_cm_cost_mkt-$tot_cm_cost_actual>0) echo fn_number_format((($tot_cm_cost_mkt-$tot_cm_cost_actual)/$tot_cm_cost_mkt)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
							<!--<tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_25','<? echo $bgcolor1; ?>')" id="trtd_25">
                                <td align="center">26</td><? //$aop_trims_yd_cost=$tot_aop_cost_mkt+$tot_trims_cost_mkt+$yarn_dyeing_cost_mkt;?>
                                <td>AOP+Trims+Y/D Cost</td>
                                <td align="right" title="total=<?=$aop_trims_yd_cost;?>"><? //echo fn_number_format(($aop_trims_yd_cost*10)/100,2); ?></td>
                                <td align="right"><?10; //echo fn_number_format(($aop_trims_yd_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? // echo fn_number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><? //echo fn_number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo fn_number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                                <td align="right" title="Variance/Total Cost Mkt*100"><? // echo fn_number_format((($tot_mkt_all_cost-$tot_actual_all_cost)/$tot_mkt_all_cost)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>-->
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_25','<? echo $bgcolor1; ?>')" id="trtd_25">
                                <td align="center">26</td>
                                <td>Total Cost</td>
                                <td align="right"><? echo fn_number_format($tot_mkt_all_cost,2); ?></td>
                                <td align="right"><?  if($tot_mkt_all_cost>0) echo fn_number_format(($tot_mkt_all_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><?  if($tot_actual_all_cost>0 && $tot_ex_factory_val>0) echo fn_number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                                <td align="right" title="Variance/Total Cost Mkt*100"><?  if($tot_mkt_all_cost-$tot_actual_all_cost>0) echo fn_number_format((($tot_mkt_all_cost-$tot_actual_all_cost)/$tot_mkt_all_cost)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_26','<? echo $bgcolor2; ?>')" id="trtd_26">
                                <td align="center">27</td>
                                <td>Margin/Loss</td>
                                <td align="right"><? echo fn_number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? if($tot_mkt_margin>0) echo fn_number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_actual_margin,2); ?></td>
                                <td align="right"><?  if($tot_actual_margin>0) echo fn_number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><?
								$margin_per="";
								if($tot_actual_margin<0){
									//echo fn_number_format($tot_mkt_margin+$tot_actual_margin,2);
									$margin_per=$tot_mkt_margin+$tot_actual_margin;
								}else{
								//echo fn_number_format($tot_mkt_margin-$tot_actual_margin,2);
									$margin_per=$tot_mkt_margin-$tot_actual_margin;
								 }?></td>

                                <td align="right" title="Variance/Margin/Loss Mkt*100"><? // echo fn_number_format((($margin_per)/$tot_mkt_margin)*100,2); ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_28','<? echo $bgcolor2; ?>')" id="trtd_28">
                                <td align="center">28</td>
                                <td>CPA/Short Fabric Cost</td>
                                <td align="right"><? //echo fn_number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? //echo fn_number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_yarn_qty_cpa_actual,2); ?></td>
                                <td align="right"><? //echo fn_number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo fn_number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                                <td align="right"><?  echo 'N/A'; ?></td>
                                 <td align="right"><? //echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_29','<? echo $bgcolor2; ?>')" id="trtd_29">
                                <td align="center">29</td>
                                <td>Net Margin/Loss(=27-28)</td>
                                <td align="right"><? //echo fn_number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? //echo fn_number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_actual_margin-$tot_yarn_qty_cpa_actual,2); ?></td>
                                <td align="right"><? //echo fn_number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? //echo fn_number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                                 <td align="right"><?  echo 'N/A';  ?></td>
                                 <td align="right"><? //echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td width="525" align="center" valign="top" style="display:none">
                    <div align="center" style="width:500px; height:53px" id="graph">&nbsp;</div>
                    <fieldset style="text-align:center; width:450px" > 
                    	<legend>Chart</legend>
                    </fieldset>
                </td>   
                <td width="" valign="top">
                    <div align="center" style="width:600px" id="div_buyer"><input type="button" value="Print Preview" class="formbutton" onClick="new_window2('company_div_b','summary_buyer')" /></div>
                    <br />
                    <div id="summary_buyer">
                        <div align="center" id="company_div_b" style="visibility:hidden; font-size:24px;width:1505px"><b><? echo $company_arr[$company_name].'<br>';echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></b></div>
                        <table width="100%" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                 <tr>
                                    <th colspan="31">Buyer Level Summary</th>
                                </tr>
                                <tr>
                                    <th width="35">SL</th>
                                    <th width="70">Buyer name</th>
                                    <th width="110">Cost Source</th>
                                    <th width="110">Ex-Factory Value</th>
                                   
                                    <th width="100">Yarn Value Addition Cost</th>
                                    <th width="110">Grey Yarn cost</th>
                                    <th width="110">Knitting Cost</th>
                                    <th width="100">Grey Fabric Transfer Cost</th>
                                   
                                    <th width="110">Grey Fabric Purchase Cost</th>
                                    <th width="100">Grey Fabric Cost</th>
                                    <th width="110">Dye & Fin Cost</th>
                                    <th width="110">AOP</th>
                                    <th width="100">Fin. Fab. Transfer Cost</th>
                                    <th width="100">Fin. Fabric Purchase Cost</th>
                                    <th width="100">Fin. Fabric Cost</th>
                                    <th width="110">Trims Cost</th>
                                    <th width="100">Printing Cost</th>
                                    <th width="100">Embroidery Cost</th>
									<th width="100">Others Cost</th>
                                    <th width="100">Dyeing  Cost</th>
                                   
                                    <th width="100">Washing  Cost</th>
                                        
                                   
                                    <th width="110">Commission Cost</th>
                                    <th width="100">Commercial Cost</th>
                                    <th width="110">Freight Cost</th>
                                    <th width="100">Testing Cost</th>
                                    <th width="100">Inspection Cost</th>
                                    <th width="100">Courier Cost</th>
                                    <th width="110">CM Cost</th>
                                    <th width="110">Total Cost</th>
                                    <th width="110">Margin</th>
                                    <th width="90">Margin %</th>
                                </tr>
                            </thead>
                            <?
                                $j=1;$tot_fin_fab_transfer_amt_actual=$tot_mkt_print_cost=$tot_mkt_embro_cost=$tot_mkt_dyeing_cost=$tot_mkt_wash_cost=$tot_mkt_value_addition_cost=0;
                                foreach($buyer_name_array as $key=>$value)
                                {
                                    if ($j%2==0)  
                                        $bgcolor="#E9F3FF";
                                    else
                                        $bgcolor="#FFFFFF";	
                                ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD_<? echo $j; ?>">
                                        <td rowspan="4"><? echo $j ;?></td>
                                        <td rowspan="4"><? echo $value ;?></td>
                                        <td><b>Pre Costing</b></td>
                                        <td align="right"><? echo number_format($mkt_po_val_array[$key],2); $tot_mkt_po_val+=$mkt_po_val_array[$key];  ?></td>
                                      
                                        <td align="right"><? echo number_format($mkt_yarn_value_addition_array[$key],2); $tot_mkt_value_addition_cost+=$mkt_yarn_value_addition_array[$key]; ?></td>
                                        
                                        <td align="right"><? echo number_format($mkt_yarn_array[$key],2); $tot_mkt_yarn_cost+=$mkt_yarn_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_knit_array[$key],2); $tot_mkt_knit_cost+=$mkt_knit_array[$key]; ?></td>
                                         <td align="right"><? //echo number_format($mkt_yarn_array[$key],2); $tot_mkt_yarn_cost+=$mkt_yarn_array[$key]; ?></td>
                                       
                                        <td align="right"><? echo number_format($mkt_fabric_purchase_array[$key],2); $tot_mkt_fin_pur_cost+=$mkt_fabric_purchase_array[$key];?></td>
                                         <td align="right"><? echo number_format($mkt_grey_fab_cost_array[$key],2); $tot_mkt_grey_fab_cost+=$mkt_grey_fab_cost_array[$key];?></td>
                                          <td align="right"><? echo number_format($mkt_dy_fin_array[$key],2); $tot_mkt_dy_fin_cost+=$mkt_dy_fin_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_aop_array[$key],2); $tot_mkt_aop_cost+=$mkt_aop_array[$key]; ?></td>
                                        <td align="right"><? //echo number_format($mkt_fin_fabric_purchase_array[$key],2);$tot_mkt_fin_fabric_purchase+=$mkt_fin_fabric_purchase_array[$key]; ?></td>
                                        <td align="right"><?  echo number_format($mkt_fin_fabric_purchase_array[$key],2);$tot_mkt_fin_fabric_purchase+=$mkt_fin_fabric_purchase_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_fin_fabric_purchase_array[$key],2); ?></td>
                                        <td align="right"><? echo number_format($mkt_trims_array[$key],2); $tot_mkt_trims_cost+=$mkt_trims_array[$key]; ?></td>
                                         
                                         <td align="right"><? echo number_format($mkt_print_array[$key],2); $tot_mkt_print_cost+=$mkt_print_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($mkt_embro_array[$key],2); $tot_mkt_embro_cost+=$mkt_embro_array[$key]; ?></td>
										 <td align="right"><? echo number_format($mkt_others_array[$key],2); $tot_mkt_others_cost+=$mkt_others_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($mkt_dyeing_array[$key],2); $tot_mkt_dyeing_cost+=$mkt_dyeing_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($mkt_wash_array[$key],2); $tot_mkt_wash_cost+=$mkt_wash_array[$key]; ?></td>
                                                
                                       
                                        <td align="right"><? echo number_format($mkt_commn_array[$key],2); $tot_mkt_commn_cost+=$mkt_commn_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_commercial_array[$key],2); $tot_mkt_commercial_cost+=$mkt_commercial_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_freight_array[$key],2); $tot_mkt_freight_cost+=$mkt_freight_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_test_array[$key],2); $tot_mkt_test_cost+=$mkt_test_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_ins_array[$key],2); $tot_mkt_ins_cost+=$mkt_ins_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_courier_array[$key],2); $tot_mkt_courier_cost+=$mkt_courier_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_cm_array[$key],2); $tot_mkt_cm_cost+=$mkt_cm_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_total_array[$key],2); $tot_mkt_total_cost+=$mkt_total_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_margin_array[$key],2); $tot_mkt_margin_cost+=$mkt_margin_array[$key]; ?></td>
                                        <td align="right"><? $mkt_margin_perc=($mkt_margin_array[$key]/$mkt_po_val_array[$key])*100; echo number_format($mkt_margin_perc,2); $tot_mkt_margin_perc_cost+=$mkt_margin_perc; ?></td> 
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD2_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD2_<? echo $j; ?>">
                                        <td><b>Actual</b></td>
                                        <td align="right"><? echo number_format($ex_factory_val_array[$key],2); $tot_buyer_ex_factory_val+=$ex_factory_val_array[$key];  ?></td>
                                        
                                        <td align="right"><? echo number_format($yarn_dyeing_twist_array[$key],2); $tot_yarn_dyeing_twist_cost+=$yarn_dyeing_twist_array[$key]; ?></td>
                                        
                                        <td align="right"><? echo number_format($yarn_cost_array[$key],2); $tot_buyer_yarn_cost+=$yarn_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($knit_cost_array[$key],2); $tot_buyer_knit_cost+=$knit_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($grey_fab_cost_array[$key],2); $tot_grey_fab_cost+=$grey_fab_cost_array[$key]; ?></td>
                                        
                                         <td align="right"><? echo number_format($actual_fabric_purchase_array[$key],2); $tot_buyer_fin_pur_act_cost+=$actual_fabric_purchase_array[$key]; ?></td>
                                       <td align="right" title="Grey Feb Cost"><? echo number_format($grey_fab_cost_array[$key],2); $tot_grey_fab_cost_act+=$grey_fab_cost_array[$key];?></td>
                                       <td align="right"><? echo number_format($dye_cost_array[$key],2); $tot_buyer_dye_cost+=$dye_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($aop_n_others_cost_array[$key],2); $tot_buyer_aop_n_others_cost+=$aop_n_others_cost_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($fin_fab_transfer_amt_actual_array[$key],2); $tot_fin_fab_transfer_amt_actual+=$fin_fab_transfer_amt_actual_array[$key]; ?></td>
                                          <td align="right"><? echo number_format($actual_fabric_purchase_array[$key],2); $tot_act_fin_fabric_purchase_cost+=$actual_fabric_purchase_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($finished_fab_cost_actual_array[$key],2); $tot_finished_fab_cost_actual_buy+=$finished_fab_cost_actual_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($trims_cost_array[$key],2); $tot_buyer_trims_cost+=$trims_cost_array[$key]; ?></td>
                                          <td align="right"><? echo number_format($print_act_cost_array[$key],2); $tot_act_print_cost+=$print_act_cost_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($embro_act_cost_array[$key],2); $tot_act_embro_cost+=$embro_act_cost_array[$key]; ?></td>
										 <td align="right"><? echo number_format($others_act_cost_array[$key],2); $tot_act_others_cost+=$others_act_cost_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($dyeing_act_cost_array[$key],2); $tot_act_dyeing_cost+=$dyeing_act_cost_array[$key]; ?></td>
                                         <td align="right"><? echo number_format($wash_act_cost_array[$key],2); $tot_act_wash_cost+=$wash_act_cost_array[$key]; ?></td>
                                         
                                      
                                        <td align="right"><? echo number_format($commission_cost_array[$key],2); $tot_buyer_commi_cost+=$commission_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($commercial_cost_array[$key],2); $tot_buyer_commercial_cost+=$commercial_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($freight_cost_array[$key],2); $tot_buyer_freight_cost+=$freight_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($testing_cost_array[$key],2); $tot_buyer_testing_cost+=$testing_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($inspection_cost_array[$key],2); $tot_buyer_inspection_cost+=$inspection_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($courier_cost_array[$key],2); $tot_buyer_courier_cost+=$courier_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($cm_cost_array[$key],2); $tot_buyer_cm_cost+=$cm_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($total_cost_array[$key],2); $tot_buyer_total_cost+=$total_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($margin_array[$key],2); $tot_buyer_margin_cost+=$margin_array[$key]; ?></td>
                                        <td align="right"><? $margin_perc=($margin_array[$key]/$ex_factory_val_array[$key])*100; echo number_format($margin_perc,2); $tot_buyer_margin_perc_cost+=$margin_perc; ?></td> 
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD3_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD3_<? echo $j; ?>">
                                        <td><b>Variance</b></td>
                                        <td align="right">
                                        <?
                                            $ex_var= $mkt_po_val_array[$key]-$ex_factory_val_array[$key];
                                            echo number_format($ex_var,2); 
                                        ?>
                                        </td>
                                         
                                         <td align="right">
                                        <?
                                           $yarn_value_addition_var= $mkt_yarn_value_addition_array[$key]-$yarn_dyeing_twist_array[$key];
                                            echo number_format($yarn_value_addition_var,2);
                                        ?>
                                        </td>
                                        
                                        <td align="right">
                                        <?
                                            $yarn_var= $mkt_yarn_array[$key]-$yarn_cost_array[$key];
                                            echo number_format($yarn_var,2);
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $knit_var= $mkt_knit_array[$key]-$knit_cost_array[$key];
                                            echo number_format($knit_var,2); 
                                        ?>
                                        </td>
                                         <td align="right">
                                        <?
                                           // $yarn_var= $mkt_yarn_array[$key]-$yarn_cost_array[$key];
                                           // echo number_format($yarn_var,2);
                                        ?>
                                        </td>
                                       
                                        <td align="right">
                                        <?
                                            $fin_pur_var= $mkt_fabric_purchase_array[$key]-$actual_fabric_purchase_array[$key];
                                            echo number_format($fin_pur_var,2); 
                                        ?>
                                        </td>
                                       <td align="right" title="grey Feb Cost"><? 
									   $grey_feb_var=$mkt_grey_fab_cost_array[$key]-$grey_fab_cost_array[$key];echo number_format($grey_feb_var,2);?></td>
                                        <td align="right">
                                        <?
                                            $dy_var= $mkt_dy_fin_array[$key]-$dye_cost_array[$key];
                                            echo number_format($dy_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $aop_var= $mkt_aop_array[$key]-$aop_n_others_cost_array[$key];
                                            echo number_format($aop_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                           // $fin_fab_pur_var=$mkt_fin_fabric_purchase_array[$key]-$fin_fab_transfer_amt_actual_array[$key];
                                            //echo number_format($fin_fab_pur_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $fin_feb_pur_var=$mkt_fin_fabric_purchase_array[$key]-$tot_act_fin_fabric_purchase_cost[$key];
                                           echo number_format($fin_feb_pur_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                       
                                        <?
                                            $totfin_fab_var= $mkt_fin_fabric_purchase_array[$key]-$finished_fab_cost_actual_array[$key];
                                            echo number_format($totfin_fab_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $trims_var= $mkt_trims_array[$key]-$trims_cost_array[$key];
                                            echo number_format($trims_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $print_var= $mkt_print_array[$key]-$print_act_cost_array[$key];
                                            echo number_format($print_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                           $embro_var= $mkt_embro_array[$key]-$embro_act_cost_array[$key];
										  
                                           echo number_format($embro_var,2); 
                                        ?>
                                        </td>
										<td align="right">
                                        <?
                                           $others_var= $mkt_others_array[$key]-$others_act_cost_array[$key];
										  
                                           echo number_format($others_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                           $dyeing_var= $mkt_dyeing_array[$key]-$dyeing_act_cost_array[$key];
                                            echo number_format($dyeing_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $wash_var= $mkt_wash_array[$key]-$wash_act_cost_array[$key];
                                            echo number_format($wash_var,2); 
                                        ?>
                                        </td>
                                        
                                       
                                        <td align="right">
                                        <?
                                            $com_var= $mkt_commn_array[$key]-$commission_cost_array[$key];
                                            echo number_format($com_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $commer_var= $mkt_commercial_array[$key]-$commercial_cost_array[$key];
                                            echo number_format($commer_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $fr_var= $mkt_freight_array[$key]-$freight_cost_array[$key];
                                            echo number_format($fr_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $test_var= $mkt_test_array[$key]-$testing_cost_array[$key];
                                            echo number_format($test_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $ins_var= $mkt_ins_array[$key]-$inspection_cost_array[$key];
                                            echo number_format($ins_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $cour_var= $mkt_courier_array[$key]-$courier_cost_array[$key];
                                            echo number_format($cour_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $cm_var= $mkt_cm_array[$key]-$cm_cost_array[$key];
                                            echo number_format($cm_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $tot_var= $mkt_total_array[$key]-$total_cost_array[$key];
                                            echo number_format($tot_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $mar_var= $mkt_margin_array[$key]-$margin_array[$key];
                                            echo number_format($mar_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $margin_perc_to= $mkt_margin_perc-$margin_perc;
                                            echo number_format($margin_perc_to,2); 
                                        ?>
                                        </td>
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD4_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD4_<? echo $j; ?>">
                                        <td><b>Variance (%)</b></td>
                                        <td align="right"><? echo number_format(($ex_var/$mkt_po_val_array[$key]*100),2); ?></td>
                                      
                                         <td align="right"><? echo number_format(($yarn_value_addition_var/$mkt_yarn_value_addition_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($yarn_var/$mkt_yarn_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($knit_var/$mkt_knit_array[$key]*100),2); ?></td>
                                        <td align="right"><? //echo number_format(($ex_var/$mkt_po_val_array[$key]*100),2); ?></td>
                                      
                                        <td align="right"><? echo number_format(($fin_pur_var/$mkt_fabric_purchase_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($grey_feb_var/$mkt_grey_fab_cost_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($dy_var/$mkt_dy_fin_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($aop_var/$mkt_aop_array[$key]*100),2); ?></td>
                                         <td align="right"><? //echo number_format(($fin_fab_pur_var/$mkt_fin_fabric_purchase_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($fin_feb_pur_var/$mkt_fin_fabric_purchase_array[$key][$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($totfin_fab_var/$mkt_fin_fabric_purchase_array[$key]*100),2); ?></td> 
                                        <td align="right"><? echo number_format(($trims_var/$mkt_trims_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($print_var/$mkt_print_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($embro_var/$mkt_embro_array[$key]*100),2); ?></td>
										 <td align="right"><? echo number_format(($others_var/$mkt_others_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($dyeing_var/$mkt_dyeing_array[$key]*100),2); ?></td>
                                         <td align="right"><? echo number_format(($wash_var/$mkt_wash_array[$key]*100),2); ?></td>
                                      
                                        <td align="right"><? echo number_format(($com_var/$mkt_commn_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($commer_var/$mkt_commercial_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($fr_var/$mkt_freight_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($test_var/$mkt_test_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($ins_var/$mkt_ins_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($cour_var/$mkt_courier_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($cm_var/$mkt_cm_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($tot_var/$mkt_total_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($mar_var/$mkt_margin_array[$key]*100),2); ?></td>
                                        <td align="right"><? echo number_format(($margin_perc_to/$mkt_margin_perc*100),2); ?></td> 
                                    </tr>
                           	<?
                                $j++;
                                }
								
								$bgcolor5='#CCDDEE';
								$bgcolor6='#CCCCFF';
								$bgcolor7='#FFEEFF';
                            ?>
                            <tr bgcolor="<? echo $bgcolor5; ?>" onClick="change_color('trTD5_<? echo $j; ?>','<? echo $bgcolor5; ?>')" id="trTD5_<? echo $j; ?>">
                                <th colspan="3">Pre Costing</th>
                                <th align="right"><? echo number_format($tot_mkt_po_val,2); ?></th>
                               
                                <th align="right"><? echo number_format($tot_mkt_value_addition_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_yarn_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_knit_cost,2); ?></th>
                                 <th align="right"><? //echo number_format($tot_mkt_po_val,2); ?></th>
                                
                                <th align="right"><? echo number_format($tot_mkt_fin_pur_cost,2); ?></th>
                               <th align="right"><? echo number_format($tot_mkt_grey_fab_cost,2); ?></th>
                               <th align="right"><? echo number_format($tot_mkt_dy_fin_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_aop_cost,2); ?></th>
                                <th align="right"><? //echo number_format($tot_mkt_aop_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_fin_fabric_purchase,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_fin_fabric_purchase,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_trims_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_print_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_embro_cost,2); ?></th>
								 <th align="right"><? echo number_format($tot_mkt_others_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_dyeing_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_mkt_wash_cost,2); ?></th>
                               
                                <th align="right"><? echo number_format($tot_mkt_commn_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_commercial_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_freight_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_test_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_ins_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_courier_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_cm_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_total_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_margin_cost,2); ?></th>
                                <th align="right"><? $mm=$tot_mkt_margin_cost/$tot_mkt_po_val*100; echo number_format($mm,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor6; ?>" onClick="change_color('trTD6_<? echo $j; ?>','<? echo $bgcolor6; ?>')" id="trTD6_<? echo $j; ?>">
                                <th colspan="3">Actual</th>
                                <th align="right"><? echo number_format($tot_buyer_ex_factory_val,2); ?></th>
                                <th align="right"><? echo number_format($tot_yarn_dyeing_twist_actual,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_yarn_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_knit_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_grey_fab_cost,2); ?></th>
                               
                                <th align="right"><? echo number_format($tot_buyer_fin_pur_act_cost,2); ?></th>
                               <th align="right"><? echo number_format($tot_grey_fab_cost_act,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_dye_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_aop_n_others_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_fin_fab_transfer_amt_actual,2); ?></th>
                                <th align="right"><? echo number_format($tot_act_fin_fabric_purchase_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_finished_fab_cost_actual_buy,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_trims_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_act_print_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_act_embro_cost,2); ?></th>
								 <th align="right"><? echo number_format($tot_act_others_cost,2); ?></th>
                                 <th align="right"><? echo number_format($tot_act_dyeing_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_act_wash_cost,2); ?></th>
                                
                                <th align="right"><? echo number_format($tot_buyer_commi_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_commercial_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_freight_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_testing_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_inspection_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_courier_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_cm_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_total_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_margin_cost,2); ?></th>
                                <th align="right"><? $pp=$tot_buyer_margin_cost/$tot_buyer_ex_factory_val*100; echo number_format($pp,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor7; ?>" onClick="change_color('trTD7_<? echo $j; ?>','<? echo $bgcolor7; ?>')" id="trTD7_<? echo $j; ?>">
                                <th colspan="3">Variance</th>
                                <th align="right"><? $evar=$tot_mkt_po_val-$tot_buyer_ex_factory_val;  echo number_format($evar,2); ?></th>
                                 <th align="right"><? $yarn_value_var=$tot_mkt_value_addition_cost-$tot_yarn_dyeing_twist_actual;  echo number_format($yarn_value_var,2); ?></th>
                                 <th align="right"><? $grey_yvar=$tot_mkt_yarn_cost-$tot_buyer_yarn_cost;  echo number_format($grey_yvar,2); ?></th>
                                <th align="right"><? $knit_var=$tot_mkt_knit_cost-$tot_buyer_knit_cost;  echo number_format($knit_var,2); ?></th>
                                <th align="right"><? echo "N/A";//$kvar=$tot_mkt_knit_cost-$tot_buyer_knit_cost;  echo number_format($kvar,2); ?></th>
                                
                                <th align="right"><? $fpfvar=$tot_mkt_fin_pur_cost-$tot_buyer_fin_pur_act_cost;  echo number_format($fpfvar,2); ?></th>
                                 <th align="right"><? $greypfvar=$tot_mkt_grey_fab_cost-$tot_grey_fab_cost_act;  echo number_format($greypfvar,2); ?></th>
                                 <th align="right"><? $dfvar=$tot_mkt_dy_fin_cost-$tot_buyer_dye_cost;  echo number_format($dfvar,2); ?></th>
                                <th align="right"><? $aopvar=$tot_mkt_aop_cost-$tot_buyer_aop_n_others_cost;  echo number_format($aopvar,2); ?></th>
                                <th align="right"><? echo "N/A";//$aopvar=$tot_mkt_aop_cost-$tot_buyer_aop_n_others_cost;  echo number_format($aopvar,2); ?></th>
                                <th align="right"><? //$fin_fab_var=$tot_mkt_fin_fabric_purchase-$tot_finished_fab_cost_actual_buy;  echo number_format($fin_fab_var,2); ?></th>
                                  <th align="right"><? $fin_fab_var=$tot_mkt_fin_fabric_purchase-$tot_finished_fab_cost_actual_buy;  echo number_format($fin_fab_var,2);; ?></th>
                                <th align="right"><? $trimvar=$tot_mkt_trims_cost-$tot_buyer_trims_cost;  echo number_format($trimvar,2); ?></th>
                                 <th align="right"><? $printvar=$tot_mkt_print_cost-$tot_act_print_cost;  echo number_format($printvar,2); ?></th>
                                 <th align="right"><? $embrovar=$tot_mkt_embro_cost-$tot_act_embro_cost;  echo number_format($embrovar,2); ?></th>
								 <th align="right"><? $othersvar=$tot_mkt_others_cost-$tot_act_others_cost;  echo number_format($othersovar,2); ?></th>
                                <th align="right"><? $dyeingvar=$tot_mkt_dyeing_cost-$tot_act_dyeing_cost;  echo number_format($dyeingvar,2); ?></th>
                                <th align="right"><? $washingvar=$tot_mkt_wash_cost-$tot_act_wash_cost;  echo number_format($washingvar,2); ?></th>
                                
                                <th align="right"><? $comvar=$tot_mkt_commn_cost-$tot_buyer_commi_cost;  echo number_format($comvar,2); ?></th>
                                <th align="right"><? $commercialvar=$tot_mkt_commercial_cost-$tot_buyer_commercial_cost;  echo number_format($commercialvar,2); ?></th>
                                <th align="right"><? $fvar=$tot_mkt_freight_cost-$tot_buyer_freight_cost;  echo number_format($fvar,2); ?></th>
                                <th align="right"><? $tvar=$tot_mkt_test_cost-$tot_buyer_testing_cost;  echo number_format($tvar,2); ?></th>
                                <th align="right"><? $ivar=$tot_mkt_ins_cost-$tot_buyer_inspection_cost;  echo number_format($ivar,2); ?></th>
                                <th align="right"><? $courvar=$tot_mkt_courier_cost-$tot_buyer_courier_cost;  echo number_format($courvar,2); ?></th>
                                <th align="right"><? $cmvar=$tot_mkt_cm_cost-$tot_buyer_cm_cost;  echo number_format($cmvar,2); ?></th>
                                <th align="right"><? $totvar=$tot_mkt_total_cost-$tot_buyer_total_cost;  echo number_format($totvar,2); ?></th>
                                <th align="right"><? $mvar=$tot_mkt_margin_cost-$tot_buyer_margin_cost;  echo number_format($mvar,2); ?></th>
                                <th align="right"><? $mpvar=$mm-$pp;  echo number_format($mpvar,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor6; ?>" onClick="change_color('trTD8_<? echo $j; ?>','<? echo $bgcolor6; ?>')" id="trTD8_<? echo $j; ?>">
                                <th colspan="3">Variance (%)</th>
                                <th align="right"><? echo number_format(($evar/$tot_mkt_po_val*100),2); ?></th>
                                 <th align="right"><? echo number_format(($yarn_value_var/$tot_mkt_value_addition_cost*100),2); ?></th>
                                  <th align="right"><? echo number_format(($grey_yvar/$tot_mkt_yarn_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($knit_var/$tot_mkt_yarn_cost*100),2); ?></th>
                                <th align="right"><? echo "N/A";//echo number_format(($kvar/$tot_mkt_knit_cost*100),2); ?></th>
                                
                                <th align="right"><? echo number_format(($fpfvar/$tot_mkt_fin_pur_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($greypfvar/$tot_mkt_grey_fab_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($dfvar/$tot_mkt_dy_fin_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($aopvar/$tot_mkt_aop_cost*100),2); ?></th>
                                <th align="right"><? echo "N/A";//echo number_format(($aopvar/$tot_mkt_aop_cost*100),2); ?></th>
                                <th align="right"><?  echo number_format(($fin_fab_var/$tot_mkt_fin_fabric_purchase*100),2); ?></th>
                                <th align="right"><? echo number_format(($trimvar/$tot_mkt_trims_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($printvar/$tot_mkt_trims_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($embrovar/$tot_mkt_embro_cost*100),2); ?></th>
								<th align="right"><? echo number_format(($othersvar/$tot_mkt_others_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($dyeingvar/$tot_mkt_dyeing_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($washingvar/$tot_mkt_wash_cost*100),2); ?></th> 
                                <th align="right"><? echo number_format(($washingvar/$tot_mkt_wash_cost*100),2); ?></th> 
                              
                                <th align="right"><? echo number_format(($comvar/$tot_mkt_commn_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($commercialvar/$tot_mkt_commercial_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($fvar/$tot_mkt_freight_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($tvar/$tot_mkt_test_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($ivar/$tot_mkt_ins_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($courvar/$tot_mkt_courier_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($cmvar/$tot_mkt_cm_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($totvar/$tot_mkt_total_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($mvar/$tot_mkt_margin_cost*100),2); ?></th>
                                <th align="right"><? echo number_format(($mpvar/$mm*100),2); ?></th>   
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
	</fieldset>
 <?
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename****$report_type";
	exit();
}





if($action=="yarn_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

	//$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	//$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$dataArrayYarn=array();
	$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')]."**".$yarnRow[csf('amount')].",";
	}

	$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
	$receive_array=array();
	$sql_receive="select a.prod_id,c.receive_purpose,b.lot,b.color, sum(a.order_qnty) as qty, sum(a.order_amount) as amnt from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 group by a.prod_id,c.receive_purpose,b.lot,b.color";
	$resultReceive = sql_select($sql_receive);
	foreach($resultReceive as $invRow)
	{
		$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
		$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose']=$invRow[csf('receive_purpose')];
	}
$yarnData="select b.po_breakdown_id, b.prod_id, a.item_category, b.issue_purpose,c.lot,c.color,
		sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose in(2,4,15,38) THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
		sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose in(2,4,15,38) THEN b.returnable_qnty ELSE 0 END) AS returnable_qnty,
		(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2  THEN a.transaction_date ELSE null END) AS transaction_date,
		sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
		sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
		sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
		sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
		from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.po_breakdown_id in($po_id)  group by b.po_breakdown_id, b.prod_id, a.item_category,a.transaction_date,a.transaction_type,b.entry_form,b.trans_type,b.issue_purpose,c.lot,c.color";
		 $yarnDataArray=sql_select($yarnData); $yarnTrimsCostArray=array();
	
	foreach($yarnDataArray as $invRow)
	{
					$issue_purpose=$invRow[csf('issue_purpose')];
						//echo $recv_purpose.'<br>';
						$issue_qty=$invRow[csf('yarn_iss_qty')]-$invRow[csf('yarn_iss_return_qty')];
					
						$transaction_date=$invRow[csf('transaction_date')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						//echo $currency_rate.'dd';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]];
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$currency_rate;
						}
						
						$issue_amnt=$issue_qty*$rate;
						//$retble_iss_amnt=$returnable_qnty*$rate;	
						if($issue_purpose==1)
						{
							$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
							
							if($recv_purpose==16)
							{
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_amt']+=$issue_amnt;
								$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_qty']+=$issue_qty;
								
							}
							
						}
						
		
		$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
		$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][1]+=$iss_qty;
		$rate='';
		if($receive_array[$invRow[csf('prod_id')]]>0)
		{
			$rate=$receive_array[$invRow[csf('prod_id')]];
		}
		else
		{
			$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
		}
		$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][2]+=$iss_qty*$rate;
	}

?>
	<style>
		hr
		{
			color: #676767;
			background-color: #676767;
			height: 1px;
		}
	</style> 
    <div>
        <fieldset style="width:1033px;">
        	<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="90">Order Qty</th>
                    <th width="70">Count</th>
                    <th width="100">Composition</th>
                    <th width="70">Type</th>
                    <th width="90">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                    <th width="100">Cost ($)</th>
                    <th width="90">Issued</th>
                    <th>Cost ($)</th>
                </thead>
            </table>	
            <div style="width:1030px; max-height:310px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1010" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_mkt_required=0; $tot_required_cost=0; $tot_yarn_iss_qty=0; $tot_yarn_iss_cost=0;
				
					 $condition= new condition();
						if($po_id!='')
						{
							$condition->po_id("in($po_id)"); 
						}
					
					 $condition->init();
				
					 $yarn= new yarn($condition);
					 	// echo $yarn->getQuery(); die;
					$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$dzn_qnty=0; $job_mkt_required=0; $yarn_issued=0; $yarn_data_array=array(); //$job_mkt_required_cost=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
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
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$job_mkt_required_cost=$yarn_costing_arr[$row[csf('id')]];
						
						$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
						foreach($dataYarn as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$copm_one_id=$yarnRow[1];
							$percent_one=$yarnRow[2];
							$copm_two_id=$yarnRow[3];
							$percent_two=$yarnRow[4];
							$type_id=$yarnRow[5];
							$qnty=$yarnRow[6];
							$amnt=$yarnRow[7];
							
							$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
							//$mkt_required_cost=$plan_cut_qnty*($amnt/$dzn_qnty);
							$job_mkt_required+=$mkt_required;
							//$job_mkt_required_cost+=$mkt_required_cost;
							
							$yarn_data_array['count'][]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][]=$yarn_type[$type_id];
							
							if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}

							$yarn_data_array['comp'][]=$compos;
						}
						
						$yarn_iss_qty=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_qty'];//$dataArrayYarnIssue[$row[csf('id')]][1];
						$yarn_iss_cost=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt'];//$dataArrayYarnIssue[$row[csf('id')]][2];
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
                            <td width="70">
								<? 
                                    $d=1;
                                    foreach($yarn_data_array['count'] as $yarn_count_value)
                                    {
                                        if($d!=1)
                                        {
                                            echo "<hr/>";
                                        }
                                        echo $yarn_count_value;
                                        $d++;
                                    }
                                ?>
                            </td>
                            <td width="100">
                                <div style="word-wrap:break-word; width:100px">
                                    <? 
                                         $d=1;
                                         foreach($yarn_data_array['comp'] as $yarn_composition_value)
                                         {
                                            if($d!=1)
                                            {
                                                echo "<hr/>";
                                            }
                                            echo $yarn_composition_value;
                                            $d++;
                                         }
                                    ?>
                                </div>
                            </td>
                            <td width="70">
                                <p>
                                    <? 
                                         $d=1;
                                         foreach($yarn_data_array['type'] as $yarn_type_value)
                                         {
                                            if($d!=1)
                                            {
                                               echo "<hr/>";
                                            }
                                            
                                            echo $yarn_type_value; 
                                            $d++;
                                         }
                                    ?>
                                </p>
                            </td>
							<td width="90" align="right"><? echo number_format($job_mkt_required,2,'.',''); ?></td>
                            <td width="100" align="right"><? echo number_format($job_mkt_required_cost,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($yarn_iss_qty,2,'.',''); ?></td>
                            <td align="right" ><? echo number_format($yarn_iss_cost,2,'.',''); ?></td>
						</tr>
					<?
						$i++;
						$tot_mkt_required+=$job_mkt_required; 
						$tot_required_cost+=$job_mkt_required_cost;
						$tot_yarn_iss_qty+=$yarn_iss_qty; 
						$tot_yarn_iss_cost+=$yarn_iss_cost; 
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th colspan="3">&nbsp;</th>
                        <th><? echo number_format($tot_mkt_required,2,'.',''); ?></th>
                        <th><? echo number_format($tot_required_cost,2,'.',''); ?></th>
                        <th><? echo number_format($tot_yarn_iss_qty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_yarn_iss_cost,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="mkt_yarn_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$data=explode("**",$mkt_data);
	$po_ids=$data[0];
	$job_nos=rtrim($data[1],',');
	//echo $po_ids.'='.$job_nos;
	$job_nos=array_unique(explode(",",$job_nos));$po_nos=array_unique(explode(",",$po_ids));
	$job_noss='';
	foreach($job_nos as $job)
	{
		if($job_noss=='') $job_noss="'".$job."'"; else $job_noss.=","."'".$job."'";
	}
	$job_noss=implode(',',array_unique(explode(",",$job_noss)));
	$po_no=str_replace("'","",$po_ids);
	$condition= new condition();
	if(str_replace("'","",$job_noss) !=''){
		 // $condition->job_no("=$job_noss");
	 }
	  if(str_replace("'","",$po_no)!='')
	 {
		$condition->po_id("in($po_no)"); 
	 }
	  $condition->init();
   $yarn= new yarn($condition);
   //echo $yarn->getQuery(); die;
  $yarn_costing_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnAmountArray();
  $yarn_qty_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnQtyArray();
	//print_r($yarn_costing_arr);
	$sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no in($job_noss) group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, rate";
 $data_array=sql_select($sql); 
	?>

    <div align="center">
        <fieldset style="width:620px;">
        	<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            <caption> <h3> Grey Yarn Details</h3></caption>
            	<thead>
                    <th width="40">SL</th>
                   <th width="350">Yarn Desc </th>
                   <th width="60">Qty</th>
                   <th width="60">Rate</th>
                    <th width="">Yarn Cost</th>
                </thead>
                <tbody> 
                <?
				//order,Count,Composition,color,type wise
                  $total_yarn_amount=0;$k=1;
			foreach( $data_array as $row )
            { 
				if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
					
 				
				$yarn_cost=0;$yarn_qty=0;
				foreach($po_nos as $pid)
				{
				 $yarn_cost+=$yarn_costing_arr[$pid][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("color")]][$row[csf("type_id")]];	
				 $yarn_qty+=$yarn_qty_arr[$pid][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("color")]][$row[csf("type_id")]];	
				}
			?>	
            
				
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td align="center" style="padding-right:3px"><? echo $k; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $item_descrition; ?></td>
                  <td align="center" style="padding-right:3px"><? echo number_format($yarn_qty,2); ?></td>
                  <td align="center" style="padding-right:3px"><? echo number_format($yarn_cost/$yarn_qty,2);; ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($yarn_cost,2); ?></td>
                </tr>
               
                <?
				$k++;
				$total_yarn_amount+=$yarn_cost;
			}
				?>
                </tbody>
                <tfoot>
                 <th colspan="4" align="right"> Total</th><th align="right"> <? echo number_format($total_yarn_amount,2);?></th>
                 </tfoot>
            </table>	
        </fieldset>
    </div>
<?
	exit();
}
if($action=="trims_cost_mkt")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$trim_group=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name"  );
	$data=explode("**",$mkt_data);
	$po_ids=$data[0];
	$job_nos=rtrim($data[1],',');
	$job_nos=array_unique(explode(",",$job_nos));$po_nos=array_unique(explode(",",$po_ids));
	$job_noss='';
	foreach($job_nos as $job)
	{
		if($job_noss=='') $job_noss="'".$job."'"; else $job_noss.=","."'".$job."'";
	}
	$job_noss=implode(',',array_unique(explode(",",$job_noss)));
	$po_no=str_replace("'","",$po_ids);
	$condition= new condition();
	if(str_replace("'","",$po_no)!='')
	{
	$condition->po_id("in($po_no)"); 
	}
	$condition->init();
    $trim= new trims($condition);
   //echo $yarn->getQuery(); die;
	$trim_costing_arr=$trim->getAmountArray_by_orderAndPrecostdtlsid();
	$trim_qty_arr=$trim->getQtyArray_by_orderAndPrecostdtlsid();
		//print_r($yarn_costing_arr);
		$sql_trim = "SELECT a.id, a.job_no, a.trim_group, cons_uom from wo_pre_cost_trim_cost_dtls a where a.job_no in($job_noss) and a.status_active=1 and a.is_deleted=0 order by a.seq";
		//echo $sql_trim; die;
		$data_array_trim=sql_select($sql_trim); 
	?>

    <div align="center">
        <fieldset style="width:620px;">
        	<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            <caption> <h3> Trim Budget Cost</h3></caption>
            	<thead>
                    <th width="40">SL</th>
                	<th width="100">Item Group</th>
                	<th width="60">UOM</th>
                	<th width="60">Qty</th>
                	<th width="60">Rate</th>
                	<th width="60">Amount</th>
                </thead>
                <tbody> 
                <?
				//order,Count,Composition,color,type wise
                  $total_yarn_amount=0;$k=1;
			foreach( $data_array_trim as $row )
            { 
				if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";				
 				
				$trim_cost=0;
				$trim_qty=0;
				foreach($po_nos as $pid)
				{
				 $trim_cost+=$trim_costing_arr[$pid][$row[csf("id")]];	
				 $trim_qty+=$trim_qty_arr[$pid][$row[csf("id")]];	
				}
				?>	
            
				
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td align="center" style="padding-right:3px"><? echo $k; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $trim_group[$row[csf('trim_group')]]; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                  <td align="center" style="padding-right:3px"><? echo number_format($trim_qty,2); ?></td>
                  <td align="center" style="padding-right:3px"><? echo number_format($trim_cost/$trim_qty,2);; ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($trim_cost,2); ?></td>
                </tr>
               
                <?
				$k++;
				$total_trim_amount+=$trim_cost;
			}
				?>
                </tbody>
                <tfoot>
                 <th colspan="5" align="right"> Total</th><th align="right"> <? echo number_format($total_trim_amount,2);?></th>
                 </tfoot>
            </table>	
        </fieldset>
    </div>
	<?
	exit();
}
if($action=="trims_cost_actual")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$exchange_rate=110;
	$trim_group=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name"  );
	$sql_trims_inv="SELECT b.po_breakdown_id as po_id, sum(b.quantity) as qnty,d.rate, d.item_group_id, d.order_uom from  order_wise_pro_details b,  inv_trims_entry_dtls d   where b.trans_id=d.trans_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.entry_form in(24) and b.trans_type in(1) and b.po_breakdown_id in($po_id) group by b.po_breakdown_id, d.rate, d.item_group_id, d.order_uom";
	//echo $sql_trims_inv; die;
	$data_array_trim=sql_select($sql_trims_inv);
	foreach ($data_array_trim as $row)
	{
		$amt=0;
		$amt=($row[csf('qnty')]*$row[csf('rate')]);
		$actual_trims_array[$row[csf('item_group_id')]]['item_group_id']=$row[csf('item_group_id')];
		$actual_trims_array[$row[csf('item_group_id')]]['order_uom']=$row[csf('order_uom')];
		$actual_trims_array[$row[csf('item_group_id')]]['qnty']+=$row[csf('qnty')];
		$actual_trims_array[$row[csf('item_group_id')]]['amt']+=$amt;
	}
	?>

    <div align="center">
        <fieldset style="width:620px;">
        	<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            <caption> <h3> Trims Actual Cost</h3></caption>
            	<thead>
                    <th width="40">SL</th>
                	<th width="100">Item Group</th>
                	<th width="60">UOM</th>
                	<th width="60">Qty</th>
                	<th width="60">Amount</th>
                </thead>
                <tbody> 
                <?
				//order,Count,Composition,color,type wise
                  $total_yarn_amount=0;$k=1;
			foreach( $actual_trims_array as $value )
            { 
				if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>	
            
				
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td align="center" style="padding-right:3px"><? echo $k; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $trim_group[$value['item_group_id']]; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $unit_of_measurement[$value['order_uom']]; ?></td>
                  <td align="center" style="padding-right:3px"><? echo number_format($value['qnty'],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($value['amt'],2); ?></td>
                </tr>
               
                <?
				$k++;
				$total_trim_amount+=$value['amt'];
			}
				?>
                </tbody>
                <tfoot>
                 <th colspan="4" align="right"> Total</th><th align="right"> <? echo number_format($total_trim_amount,2);?></th>
                 </tfoot>
            </table>	
		<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0">
		<thead>
				<tr>
					<th colspan="5">Transfer In</th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="160">Item group</th>
					<th width="80">UOM</th>
					<th width="80">QTY</th>
					<th width="80">AMOUNT</th>
				</tr>
			</thead>
			<?
			$total_trans_in_qnty=$total_trans_in_amt=0;
			$sql_in = " SELECT a.transfer_date,b.item_group,b.rate,b.uom,SUM (b.transfer_qnty) AS transfer_qnty, SUM (b.transfer_value) AS transfer_amount FROM inv_item_transfer_mst a, inv_item_transfer_dtls b WHERE a.id = b.mst_id AND a.to_order_id IN ($po_id) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.transfer_date,b.item_group,b.rate, b.uom"; 
			//echo $sql_in;
			$data_array_in=sql_select($sql_in);
			foreach ($data_array_in as $row)
			{
				$transaction_date=$row[csf('transfer_date')];
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				$in_amt=0;
				$in_amt=$row[csf('transfer_amount')]/$exchange_rate;
				$in_trims_array[$row[csf('item_group')]]['item_group']=$row[csf('item_group')];
				$in_trims_array[$row[csf('item_group')]]['uom']=$row[csf('uom')];
				$in_trims_array[$row[csf('item_group')]]['transfer_qnty']+=$row[csf('transfer_qnty')];
				$in_trims_array[$row[csf('item_group')]]['in_amt']+=$in_amt;
			} 
			echo $currency_rate;
			//echo '<pre>';print_r($in_trims_array);
			foreach( $in_trims_array as $value )
            { 
				if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>	
            
				
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td align="center" style="padding-right:3px"><? echo $k; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $trim_group[$value['item_group']]; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $unit_of_measurement[$value['uom']]; ?></td>
                  <td align="center" style="padding-right:3px"><? echo number_format($value['transfer_qnty'],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($value['in_amt'],2); ?></td>
                </tr>
               
                <?
				$k++;
				$total_trim_in_amount+=$value['in_amt'];
			}
			unset($data_array_in);
			?>
			<tr style="font-weight:bold">
			<th colspan="4" align="right"> Total</th><th align="right"> <? echo number_format($total_trim_in_amount,2);?></th>
			</tr>
			<thead>
				<tr>
					<th colspan="5">Transfer out</th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="160">Item group</th>
					<th width="80">UOM</th>
					<th width="80">QTY</th>
					<th width="80">AMOUNT</th>
				</tr>
			</thead>
			<?
			$total_trans_out_qnty=$total_trans_out_amt=0;
			$sql_out = " SELECT a.transfer_date,b.item_group,b.rate,b.uom, SUM (b.transfer_qnty) AS transfer_qnty, SUM (b.transfer_value) AS transfer_amount FROM inv_item_transfer_mst a, inv_item_transfer_dtls b WHERE a.id = b.mst_id AND a.from_order_id IN ($po_id) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.transfer_date,b.item_group,b.rate, b.uom"; 
			//echo $sql_out;
			$data_array_out=sql_select($sql_out);
			foreach ($data_array_out as $row)
			{
				$transaction_date=$row[csf('transfer_date')];
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				$out_amt=0;
				$out_amt=$row[csf('transfer_amount')]/$exchange_rate;
				$out_trims_array[$row[csf('item_group')]]['item_group']=$row[csf('item_group')];
				$out_trims_array[$row[csf('item_group')]]['uom']=$row[csf('uom')];
				$out_trims_array[$row[csf('item_group')]]['transfer_qnty']+=$row[csf('transfer_qnty')];
				$out_trims_array[$row[csf('item_group')]]['out_amt']+=$out_amt;
			} 
			foreach( $out_trims_array as $value )
            { 
				if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>	
            
				
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td align="center" style="padding-right:3px"><? echo $k; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $trim_group[$value['item_group']]; ?></td>
                  <td align="center" style="padding-right:3px"><? echo $unit_of_measurement[$value['uom']]; ?></td>
                  <td align="center" style="padding-right:3px"><? echo number_format($value['transfer_qnty'],2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($value['out_amt'],2); ?></td>
                </tr>
               
                <?
				$k++;
				$total_trim_out_amount+=$value['out_amt'];
			}
			?>
			<tr style="font-weight:bold">
			<th colspan="4" align="right"> Total</th><th align="right"> <? echo number_format($total_trim_out_amount,2);?></th>
			</tr>
        </fieldset>
    </div>
	<?
	exit();
}
if($action=="mkt_yarn_dyeing_twisting_cost") 
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$data=explode("**",$mkt_data);
	$po_ids=$data[0];
	$job_nos=rtrim($data[1],',');
	//echo $po_ids.'='.$job_nos;
	$job_nos=array_unique(explode(",",$job_nos));$po_nos=array_unique(explode(",",$po_ids));
	$job_noss='';
	foreach($job_nos as $job)
	{
		if($job_noss=='') $job_noss="'".$job."'"; else $job_noss.=","."'".$job."'";
	}
	$job_noss=implode(',',array_unique(explode(",",$job_noss)));
	$po_no=str_replace("'","",$po_ids);
	$condition= new condition();
	if(str_replace("'","",$job_noss) !=''){
		 // $condition->job_no("=$job_noss");
	 }
	  if(str_replace("'","",$po_no)!='')
	 {
		$condition->po_id("in($po_no)"); 
	 }
	  $condition->init();
   $conversion= new conversion($condition);
   //echo $yarn->getQuery(); die;
  $conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//print_r($yarn_costing_arr);
	?>
    <script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
	}
	
    </script>
    <input type="hidden" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:400px;">
     <fieldset style="width:400px;">
        	<table class="rpt_table" width="400" cellpadding="0" cellspacing="0" id="table_search" border="1" rules="all" >
             <caption> <h3> Yarn Value Addition Cost </h3></caption>
            	<thead>
                	<th width="50">SL</th>
                    <th width="250">Process</th>
                    <th width="100">Amount</th>
                </thead>
                <tbody id="scroll_body">
                	<?
					$k=1;$total_conversion_cost=0;
					$sql = "select  a.cons_process	from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no in(".$job_noss.") and a.cons_process in(30,134)  group by a.cons_process ";
	$data_array=sql_select($sql);
					foreach($data_array as $row)
					{
						if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$conversion_cost=0;
						foreach($po_nos as $pid)
						{
						 $conversion_cost+=array_sum($conversion_costing_arr[$pid][$row[csf("cons_process")]]);	
						}
					?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                    <td align="center"><? echo $k; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($conversion_cost,4); ?></td>
                    </tr>
                    <?
					$k++;
						$total_conversion_cost+=$conversion_cost;
					}
					?>
                    </tbody>
                <tfoot>
                 <th colspan="2" align="right"> Total</th><th align="right"> <? echo number_format($total_conversion_cost,2);?></th>
                 </tfoot>
               </table>
           </table>
  </fieldset>
  <script>
  setFilterGrid("table_search",-1);
  </script>
    </div>
	<?
	exit();
}


if($action=="knitting_cost") //mkt_yarn_dyeing_twisting_cost
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	
	$subconInBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by b.order_id");
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}	
	
	$subconOutBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
	foreach($subconOutBillDataArray as $subRow)
	{
		//$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		//$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
	}
	function openmypage_bill(po_id,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_v4_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../../');
	}
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:1050px;">
        <fieldset style="width:1050px;">
        	<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty</th>
                    <th width="80">Grey Prod.</th>
                    <th width="90">Knitting Cost</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1050px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_greyProd_qnty=0; $tot_knitCost=0; $tot_knitbill=0; $tot_knitQty=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
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
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$bookingQty=$bookingArray[$row[csf('id')]];
						$tot_booking_qnty+=$bookingQty;
						$greyProdQty=$greyProdArray[$row[csf('id')]];
						$tot_greyProd_qnty+=$greyProdQty;
						$knitCost=($order_qnty_in_pcs/$dzn_qnty)*$knitCostArray[$row[csf('job_no')]];
						$tot_knitCost+=$knitCost;
						$knitQty=$subconCostArray[$row[csf('id')]]['qty'];
						$knitbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						$tot_knitQty+=$knitQty;
						$tot_knitbill+=$knitbill;
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($greyProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($knitCost,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($knitQty,2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]; ?>','knitting_bill','Knitting bill Details')"><? echo number_format($knitbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="7">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_greyProd_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitCost,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitQty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="knitting_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by a.id, a.bill_no, a.company_id, a.party_source");// b.order_id, b.currency_id
	/*foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}*/	
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no");// b.order_id, b.currency_id
	/*foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}*/
	
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data,party_source)
	{
		//alert("su..re");
		var report_title="Knitting Bill";
		var show_val_column='';
		if(party_source==1)
		{
			var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
			if (r==true)
			{
				show_val_column="1";
			}
			else
			{
				show_val_column="0";
			}
		}
		else show_val_column="0";
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/knitting_bill_issue_controller.php?data="+data+'&action=knitting_bill_print', true );
	}
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:470px;">
    
        <fieldset style="width:470px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>','<?php echo $row[csf('party_source')]; ?>')"><? echo $row[csf('bill_no')]; ?></a></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="dye_fin_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	
	$subconInBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=4 group by b.order_id");
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}	
	
	$subconOutBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
	foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$bookingArray=array();
	$bookingDataArray=sql_select( "select b.po_break_down_id, sum(b.grey_fab_qnty) as grey_qnty, sum(b.fin_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
	foreach($bookingDataArray as $bokRow)
	{
		$bookingArray[$bokRow[csf('po_break_down_id')]]['grey']=$bokRow[csf('grey_qnty')];
		$bookingArray[$bokRow[csf('po_break_down_id')]]['fin']=$bokRow[csf('qnty')];
	}
	
	$finProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=7 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$finCostArray=return_library_array( "select job_no, sum(amount) AS dye_fin_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process not in(1,2,30,35) and status_active=1 and is_deleted=0 group by job_no", "job_no", "dye_fin_charge");

?>
<script>
	function openmypage_bill(po_id,type,tittle)
	{
		//alert("su..re"); return;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_v4_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../../');
	}
</script>
    <div>
        <fieldset style="width:1113px;">
        	<table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty(Grey)</th>
                    <th width="80">Booking Qty(Fin)</th>
                    <th width="80">Finish Prod.</th>
                    <th width="90">Dye & Fin Cost</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1110px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1090" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					/*$po_ids=count(array_unique(explode(",",$po_id)));
					//echo $all_po_id;
					$all_po_ids=chop($all_po_id,','); $poIds_cond="";
					//print_r($all_po_ids);
					if($db_type==2 && $po_ids>990)
					{
						$poIds_cond=" and (";
						$poIdsArr=array_chunk(explode(",",$all_po_ids),990);
						//print_r($gate_outIds);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poIds_cond.=" po_id  in($ids) or ";
						}
						$poIds_cond=chop($poIds_cond,'or ');
						$poIds_cond.=")";
					}
					else
					{
						$poIds_cond=" and  po_id  in($all_po_id)";
					}*/
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut , b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_finProd_qnty=0; $tot_finCost=0; $tot_finbill=0; $tot_knitQty=0;
					$condition= new condition();
					
					/* $poids=explode(",",$po_id);
					 foreach( $poids as $po)
					 {
						$condition->po_id(" in($po)");  
					 }*/
					if(trim(str_replace("'","",$po_id))!='')
					 {
						$condition->po_id(" in($po_id)"); 
					 }
					  $condition->init();
					  $conversion= new conversion($condition);
					//  echo $conversion->getQuery();
				 	$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
					
					  
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
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
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						
						$plun_qnty_in_pcs=$row[csf('plan_cut')]*$row[csf('ratio')];
						
						$bookingQty=$bookingArray[$row[csf('id')]]['fin'];
						$tot_booking_qnty+=$bookingQty;
						
						$bookingQtyGrey=$bookingArray[$row[csf('id')]]['grey'];
						$tot_booking_grey_qnty+=$bookingQtyGrey;
						
						$finProdQty=$finProdArray[$row[csf('id')]];
						$tot_finProd_qnty+=$finProdQty;
						
						$finCost=0;$not_yarn_dyed_cost_arr=array(1,2,30,35);
						foreach($conversion_cost_head_array as $process_id=>$val)
						{
							if(!in_array($process_id,$not_yarn_dyed_cost_arr))
							{
								$finCost+=array_sum($conversion_costing_arr_process[$row[csf('id')]][$process_id]);
							}
						}	
						//$finCost=($order_qnty_in_pcs/$dzn_qnty)*$finCostArray[$row[csf('job_no')]];
						//$finCost=($plun_qnty_in_pcs/$dzn_qnty)*$finCostArray[$row[csf('job_no')]];
						$tot_finCost+=$finCost;
						
						$finQty=$subconCostArray[$row[csf('id')]]['qty'];
						$finbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						
						$tot_finQty+=$finQty;
						$tot_finbill+=$finbill;
						
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo number_format($bookingQtyGrey,2,'.',''); ?></td>
                            <td width="80" align="right"><? echo number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($finProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($finCost,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($finQty,2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]; ?>','dyeing_bill','Dyeing bill Details')"><? echo number_format($finbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="7">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo number_format($tot_booking_grey_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_finProd_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_finCost,2,'.',''); ?></th>
                        <th><? echo number_format($tot_finQty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_finbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="dyeing_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=4 group by a.id, a.bill_no, a.company_id, a.party_source");//, b.order_id, b.currency_id
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no");//, b.currency_id, b.order_id
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data)
	{
		//alert("su..re");
		var report_title="Dyeing And Finishing Bill";
		var show_val_column=1;
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/sub_fabric_finishing_bill_issue_controller.php?data="+data+'&action=fabric_finishing_print', true );
	}
	
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:470px;">
        <fieldset style="width:470px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>')"><? echo $row[csf('bill_no')]; ?></a></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	exit();
}

if($action=="yarn_cost_actual")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	
	$sql_wo_fab="select a.id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.job_no,b.po_break_down_id as po_id
				 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id in($po_id)";
				$result_fab_result=sql_select( $sql_wo_fab );
				foreach ($result_fab_result as $row)
				{
						$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
						$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
						$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
						$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
						$job_no=$row[csf('job_no')];
				}
				unset($result_fab_result);
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:980px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:975px; margin-left:7px">
		<div id="report_container">
            <table class="rpt_table" width="975" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue Id</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Purpose</th>
                    <th width="80">Supplier</th>
                    <th width="80">Lot</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Issue Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
				$job_array=return_library_array( "select id,job_no_mst from wo_po_break_down where id in($po_id)", "id", "job_no_mst"  );
				 $sql_yarn="select c.id,a.requisition_no,a.receive_basis,a.prod_id,(a.cons_amount) as cons_amount,c.issue_basis,c.issue_number,c.booking_no from inv_transaction a, order_wise_pro_details b, inv_issue_master c where  a.id=b.trans_id and  c.id=a.mst_id and c.entry_form in(3) and b.entry_form in(3)  and  a.transaction_type=2  and b.po_breakdown_id in($po_id)";
			$result_yarn=sql_select($sql_yarn);
			foreach($result_yarn as $invRow)
			{
				if($invRow[csf('issue_basis')]==1)// Booking
				{
					$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('booking_no')];
				}
				else if($invRow[csf('issue_basis')]==3)// Requesition
				{
					$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('requisition_no')];
				}
			}
			
			unset($result_yarn);
			
				 $yarn_dyeing_costArray=array();
				//$yarndyeing_data=sql_select("select a.ydw_no,a.is_short, b.job_no, sum(b.amount) as amnt,sum(b.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where  a.id=b.mst_id and a.entry_form in(41,94) and b.status_active=1 and b.is_deleted=0 group by a.ydw_no,a.is_short,b.job_no");
				$yarndyeing_data="select b.booking_date,b.id,b.currency,b.is_short,b.ydw_no,a.job_no,a.yarn_color,a.product_id,avg(a.dyeing_charge) as dyeing_charge,  sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a where  b.id=a.mst_id and b.entry_form in(41,94) and a.status_active=1 and a.is_deleted=0 and a.job_no='$job_no'  group by b.id,b.currency,b.is_short,a.job_no,a.product_id,b.ydw_no,a.yarn_color,b.booking_date";
				$yarndyeing_dataResult=sql_select($yarndyeing_data);
				foreach($yarndyeing_dataResult as $yarnRow)
				{
				   $yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
				   $yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				}
				
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$yarnIssueData="select b.prod_id,sum(a.cons_amount) as cons_amount,sum(a.cons_quantity) as cons_quantity
				from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1) and a.transaction_type in(2,4,5,6) and b.entry_form ='3' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25)   and b.po_breakdown_id in($po_id) group by  b.prod_id";
			$resultyarnIssueData = sql_select($yarnIssueData);
			$all_prod_ids="";
			foreach($resultyarnIssueData as $row)
			{
				if($all_prod_ids=="") $all_prod_ids=$row[csf('prod_id')];else $all_prod_ids.=",".$row[csf('prod_id')];
				
				$yarn_issue_amt_arr[$row[csf('prod_id')]]['amt']+=$row[csf('cons_amount')];
				$yarn_issue_amt_arr[$row[csf('prod_id')]]['qty']+=$row[csf('cons_quantity')];
			}
			unset($resultyarnIssueData);
			//echo $all_prod_ids;
			//print_r($yarn_issue_amt_arr);
			$prodIds=chop($all_prod_ids,',');
			$prod_cond_for_in="";
			$prod_ids=count(array_unique(explode(",",$all_prod_ids)));
			if($db_type==2 && $prod_ids>1000)
			{
				$prod_cond_for_in=" and (";
				$prodIdsArr=array_chunk(explode(",",$prodIds),999);
				foreach($prodIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$prod_cond_for_in.=" a.prod_id in($ids) or"; 
				}
				$prod_cond_for_in=chop($prod_cond_for_in,'or ');
				$prod_cond_for_in.=")";
			}
			else
			{
				$prodIds=implode(",",array_unique(explode(",",$all_prod_ids)));
				$prod_cond_for_in=" and a.prod_id in($prodIds)";
			}
			
			if($prodIds!='' || $prodIds!=0)
			{
				$prod_cond_for_in=$prod_cond_for_in;
			}
			else
			{
				$prod_cond_for_in="and a.prod_id in(0)";
			}
			
			$sql_receive_for_issue="select c.booking_id,a.job_no,a.prod_id,max(a.transaction_date) as transaction_date,c.currency_id,c.receive_purpose,b.lot,b.color, 
			 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt,
			 sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv
			  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $prod_cond_for_in  group by c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.lot,b.color";
			$resultReceive_chek = sql_select($sql_receive_for_issue);
			
			foreach($resultReceive_chek as $row)
			{
				$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose']=$row[csf('receive_purpose')];
				$receive_date_array[$row[csf('prod_id')]]['last_trans_date']=$row[csf('transaction_date')];
				$avg_rate=$row[csf('amnt')]/$row[csf('qty')];
				$receive_array[$row[csf('prod_id')]]=$avg_rate;
			}
			unset($resultReceive_chek);
			
			
			
					$plan_details_array = return_library_array("select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where po_id in($po_id)  group by dtls_id,booking_no", "dtls_id", "booking_no");
				//print_r($plan_details_array);
				$reqs_array = array();
				$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
				foreach ($reqs_sql as $row)
				 {
					//$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
					$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
				}
				unset($reqs_sql);
				
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_cost=0;
				 $yarnTrimsData="select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color
					from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25) and b.po_breakdown_id in($po_id) ";
					// select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25) and b.po_breakdown_id in(50943) 
				
			$yarnTrimsDataArray=sql_select($yarnTrimsData); $yarnTrimsCostArray=array(); $i=0;
			$usd_id=2;$tot_iss_amnt=0;$totAmt=0;
			foreach($yarnTrimsDataArray as $invRow)
			{
				if($invRow[csf('item_category')]==1)
				{
					$yarn_issue_amt=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['amt'];
					$yarn_issue_qty=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['qty'];
					$last_trans_date=$receive_date_array[$invRow[csf('prod_id')]]['last_trans_date'];
					
					if($invRow[csf('receive_basis')]==1)//Booking Basis
					{
						$booking_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						if($invRow[csf('issue_purpose')]==2) //Yarn Dyeing purpose
						{
							$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
							$booking_type=1;
							//echo $booking_no.'= '.$booking_type.', ';
						}
						else
						{
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							//echo $is_short.'= '.$booking_type.'A ';
						}
					}
					else if($invRow[csf('receive_basis')]==3) //Requisition Basis
					{
						$booking_req_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						
						$prog_no=$reqs_array[$booking_req_no]['knit_id'];
						$booking_no=$plan_details_array[$prog_no];
						//echo $prog_no.'req';
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
					$transaction_date='';
					$transaction_date=$last_trans_date;
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					
					$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
					$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
					
					$avgrate=$issue_rate/$exchange_rate;
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($invRow[csf('entry_form')]==3 && $recv_purpose==16)//recv_purpose==16=Grey Yarn
					{
						//echo $is_short.'='.$booking_type.'='.$recv_purpose.'<br>';
						// if(($booking_type==1 || $booking_type==4) && $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						// {
							//echo $invRow[csf('issue_purpose')].'X';
							
							if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
							{
								//echo $invRow[csf('mst_id')].'='.$invRow[csf('issue_purpose')].'='.$q.'='.$invRow[csf('quantity')].'-k<br>';
								
								$iss_amnt=$invRow[csf('quantity')]*$avgrate;
								$tot_iss_amnt+=$iss_amnt;
								
								$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['grey_yarn_amt']+=$iss_amnt;
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['yq']+=$invRow[csf('quantity')];
								//$yarnTrimsCostArray[$invRow[csf('prod_id')]]['grey_yarn_qty_retble']+=$invRow[csf('returnable_qnty')];
								//$yarnTrimsCostArray[$invRow[csf('prod_id')]]['grey_yarn_amt_retble']+=$retble_iss_amnt;
								$totAmt+=$iss_amnt;
							}
						//}
						
					}
				}
				
			}
			//echo $totAmt.'TTTTTT';
		//	print_r($yarnTrimsCostArray);
			unset($yarnTrimsDataArray);
			//edn
				/*$sql="SELECT a.booking_no, a.issue_number, a.issue_basis, a.issue_date, c.supplier_id, d.item_category, b.issue_purpose, c.lot, c.color, d.requisition_no,
				 	sum(CASE WHEN d.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose in(1,2,4,15,38) THEN b.quantity ELSE 0 END) AS issue_qnty,
					sum(CASE WHEN d.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					(CASE WHEN d.transaction_type=2 and b.entry_form ='3' and b.trans_type=2  THEN d.transaction_date ELSE null END) AS transaction_date, c.id as prod_id, c.product_name_details, c.avg_rate_per_unit
					from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d 
					where a.id=d.mst_id and d.transaction_type in(2,4,5,6) and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type in(2,4,5,6) and b.entry_form in(3,11,25) and b.po_breakdown_id in($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
					group by a.id,c.id,c.lot,c.color,d.item_category,b.issue_purpose, a.issue_number, a.issue_date, c.supplier_id, c.product_name_details, c.avg_rate_per_unit,d.transaction_type,b.entry_form,b.trans_type,d.transaction_date,a.booking_no,a.issue_basis,d.requisition_no";*/
				$sql_isssue="select d.issue_number,d.issue_date,a.mst_id, a.transaction_type,a.item_category,a.requisition_no, a.receive_basis, a.item_category, a.transaction_date as issue_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id,b.quantity as issue_qnty, b.returnable_qnty, c.lot, c.color,c.product_name_details,d.supplier_id,d.issue_purpose
					from inv_transaction a, order_wise_pro_details b,product_details_master c,inv_issue_master d where a.mst_id=d.id and a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and d.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25) and b.po_breakdown_id in($po_id) ";

					

                $result_issue=sql_select($sql_isssue);$usd_id=2;
				foreach($result_issue as $row) //yarnTrimsCostArray
				{
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['issue_purpose']=$row[csf('issue_purpose')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['issue_number']=$row[csf('issue_number')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['issue_date']=$row[csf('issue_date')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['transaction_type']=$row[csf('transaction_type')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['item_category']=$row[csf('item_category')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['requisition_no']=$row[csf('requisition_no')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['receive_basis']=$row[csf('receive_basis')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['cons_rate']=$row[csf('cons_rate')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['entry_form']=$row[csf('entry_form')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['lot']=$row[csf('lot')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
				$yarn_issue_mst_arr[$row[csf('mst_id')]][$row[csf('prod_id')]]['product_name_details']=$row[csf('product_name_details')];
				}
				//print_r($yarn_issue_mst_arr);
				
				foreach($yarnTrimsCostArray as $mst_id=>$mstData) //yarnTrimsCostArray
				{
					foreach($mstData as $prod_id=>$row)  
					{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
						
					$issue_purposeId=$yarn_issue_mst_arr[$mst_id][$prod_id]['issue_purpose'];
					$issue_number=$yarn_issue_mst_arr[$mst_id][$prod_id]['issue_number'];
					$issue_date=$yarn_issue_mst_arr[$mst_id][$prod_id]['issue_date'];
					$transaction_type=$yarn_issue_mst_arr[$mst_id][$prod_id]['transaction_type'];
					$item_categoryId=$yarn_issue_mst_arr[$mst_id][$prod_id]['item_category'];
					$requisition_no=$yarn_issue_mst_arr[$mst_id][$prod_id]['requisition_no'];
					$receive_basisId=$yarn_issue_mst_arr[$mst_id][$prod_id]['receive_basis'];
					$lot=$yarn_issue_mst_arr[$mst_id][$prod_id]['lot'];
					$supplier_id=$yarn_issue_mst_arr[$mst_id][$prod_id]['supplier_id'];
					//$yarn_issue_mst_arr[$mst_id][$prod_id]['color']=$row[('color')];
					$product_name_details=$yarn_issue_mst_arr[$mst_id][$prod_id]['product_name_details'];
				
					if($item_categoryId==1)
					{
				//	$issue_purpose=$row[('issue_purpose')];
					//$issue_basis=$row[('issue_basis')];
					//$item_category=$row[('item_category')];
					$yarn_issue_amt=$yarn_issue_amt_arr[$prod_id]['amt'];
					$yarn_issue_qty=$yarn_issue_amt_arr[$prod_id]['qty'];
					$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
					$last_trans_date=$last_receive_date_array[$prod_id]['last_date'];
				 
					$yarn_cost=0;
					if($receive_basisId==1)//Booking Basis
					{
						$booking_no=$yarn_booking_no_arr[$mst_id];
						if($issue_purposeId==2) //Yarn Dyeing purpose
						{
							$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
							$booking_type=1;
						}
						else
						{
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
						}
					}
					else if($receive_basisId==3) //Requisition Basis
					{
						$booking_req_no=$yarn_booking_no_arr[$mst_id];
						$prog_no=$reqs_array[$booking_req_no]['knit_id']; 
						$booking_no=$plan_details_array[$prog_no];
						//echo $prog_no.'req';
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
				}
						
						//echo $booking_type.'='.$is_short;
						//if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						
                 
					 
					//echo $row[csf('issue_number')]."__".$booking_type.'__'.$is_short."__".$recv_purpose."__".$issue_purpose."<br>";
					
					// if(($booking_type==1 || $booking_type==4) && (($is_short==2 || $is_short==1 ) &&  $booking_type!='') && ($recv_purpose==16 || $issue_purpose==2))
					// {
						//echo $avg_rate.'a';
					$yarn_cost=$yarnTrimsCostArray[$mst_id][$prod_id]['grey_yarn_amt'];
					$yarn_issued=$yarnTrimsCostArray[$mst_id][$prod_id]['yq'];
				//	echo $yarn_cost;
						//$yarn_issued=$yarnTrimsCostArray[$row[csf('prod_id')]]['yq'];

						if($yarn_cost!=0){
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $issue_number; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($issue_date); ?></td>
                        <td width="80"><p><? echo $yarn_issue_purpose[$issue_purposeId]; ?></p></td>
                        <td width="80"><p><? echo $supplier_array[$supplier_id]; ?></p></td>
                        <td width="80"><p><? echo $lot; ?></p></td>
                        <td width="180"><p><? echo $product_name_details; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($yarn_issued,2); 
								$total_yarn_issue_qnty+=$yarn_issued;
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								
							// echo number_format($avg_rate,2); 
							echo number_format($yarn_cost/$yarn_issued,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								//$yarn_cost=$yarn_issued*$avg_rate;
								echo number_format($yarn_cost,4); 
								$total_yarn_cost+=$yarn_cost;
                            ?>
                        </td>
                    </tr>
                <?
                $i++;

						}
					 }
						
						
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="40">SL</th>
                	<th width="110">Return Id</th>
                    <th width="80">Return Date</th>
                     <th width="80">Purpose</th>
                    <th width="80">Supplier</th>
                    <th width="80">Lot</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_cost=0;
				 $sql="select a.booking_no,a.recv_number,a.receive_basis, a.receive_date,b.issue_purpose, sum(b.quantity) as returned_qnty, c.id as prod_id, c.supplier_id,c.color,c.lot, c.product_name_details, c.avg_rate_per_unit,d.requisition_no from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.id, c.id, a.recv_number, a.booking_no,a.receive_date,b.issue_purpose, c.supplier_id,c.color,c.lot, c.product_name_details,a.receive_basis, c.avg_rate_per_unit,d.requisition_no";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					$issue_basis=$row[csf('receive_basis')];
					$issue_purpose=$row[csf('issue_purpose')];
					$item_category=$row[csf('item_category')];
					if($issue_basis==1) //Booking Basis
						{
							$booking_no=$row[csf('booking_no')];
						}
						else if($issue_basis==3) //Requisition Basis
						{
							$booking_req_no=$row[csf('booking_no')];
							
							$prog_no=$reqs_array[$booking_req_no]['knit_id'];
							$booking_no=$plan_details_array[$prog_no];
							//echo $prog_no.'req';
						}
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
						//echo $issue_basis.'dsd';
						$transaction_date=$row[csf('receive_date')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						
						$currency_id=$receive_curr_array[$row[csf('prod_id')]];
						if($receive_array[$row[csf('prod_id')]]>0)
						{
							
							//echo $currency_id.'D';
							if($currency_id==1) //Taka
							{
								$avg_rate=$receive_array[$row[csf('prod_id')]]/$currency_rate;
								//echo $avg_rate.'C';
							}
							else
							{
								$avg_rate=$receive_array[$row[csf('prod_id')]];	
								//echo $avg_rate.'B';
							}
						}
						else
						{
							$avg_rate=$avg_rate_array[$row[csf('prod_id')]]/$currency_rate;
							//echo $avg_rate.'A';
						}
						//$avg_rate=$receive_array[$row[csf('prod_id')]]/$currency_rate;
						//echo $avg_rate;
						$recv_purpose=$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose'];
						//echo $recv_purpose.', ';
					if($issue_purpose==1 || $issue_purpose==4)
					{
                    	if($recv_purpose==16)
						{
							$yarn_returned=$row[csf('returned_qnty')];
							$iss_ret_amnt=$row[csf('returned_qnty')]*$avg_rate;
							//echo $yarn_returned.'d'.$avg_rate;
						}
					}
					else if($issue_purpose==2) //Yarn Dyeing
					{
						if($recv_purpose==16)
						{
							$yarn_returned=$row[csf('returned_qnty')];
							$iss_ret_amnt=$row[csf('returned_qnty')]*$avg_rate;
							//echo $yarn_returned.'A';
						}
					}
					//$yarn_returned=$row[csf('returned_qnty')];
					//$iss_ret_amnt=$yarn_returned*$avg_rate;
					//$retble_iss_ret_amnt=$returnable_ret_qnty*$avg_rate;
						
					//echo $booking_type.'='.$is_short;
					// if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='' && $recv_purpose==16) //Main and Sample Faric Booking
					// 	{
							$total_yarn_return_qnty+=$yarn_returned;
					//echo $iss_ret_amnt.'A'.$avg_rate;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_issue_purpose[$issue_purpose]; ?></p></td>
						<td width="80"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
						<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
                     
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($yarn_returned,2); 
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								/*if($receive_array[$row[csf('prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$ex_rate;
								}*/
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$yarn_return_cost=$iss_ret_amnt;
								echo number_format($yarn_return_cost,2); 
								$total_yarn_return_cost+=$yarn_return_cost;
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					//	}
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                     <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_return_cost,2);?></td>
                </tr>
               
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                     <th>&nbsp;</th>
                    <th align="right">Balance</th>
                    <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}
if($action=="yarn_cost_actual2")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	
	$sql_wo_fab="select a.id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.job_no,b.po_break_down_id as po_id
				 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id in($po_id)";
				$result_fab_result=sql_select( $sql_wo_fab );
				foreach ($result_fab_result as $row)
				{
						$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
						$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
						$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
						$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
						$job_no=$row[csf('job_no')];
				}
				unset($result_fab_result);
	?>
	<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<div style="width:980px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:975px; margin-left:7px">
		<div id="report_container">
            <table class="rpt_table" width="975" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue Id</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Purpose</th>
                    <th width="80">Supplier</th>
                    <th width="80">Lot</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Issue Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
				//echo $po_id;
				//$job_array=return_library_array( "select id,job_no_mst from wo_po_break_down where id in($po_id)", "id", "job_no_mst"  );
				$sql_po="select id,job_no_mst,status_active from wo_po_break_down where id in($po_id) and status_active!=0";
				$sql_po_result=sql_select($sql_po);
				foreach($sql_po_result as $row)
				{
					$job_array[$row[csf('id')]]=$row[csf('job_no_mst')];
					if($row[csf('status_active')]==3)
					{
						$cancel_po_array[$row[csf('id')]]=$row[csf('id')];
					}
					else
					{
						$po_array[$row[csf('id')]]=$row[csf('id')];
					}
				}
				$po_cond_for_in=where_con_using_array($po_array,0,"b.po_breakdown_id");
				$can_po_cond_for_in=where_con_using_array($cancel_po_array,0,"b.po_breakdown_id");
				//print_r($cancel_po_array);
				
				 $sql_yarn="select c.id,a.requisition_no,a.receive_basis,a.prod_id,(a.cons_amount) as cons_amount,c.issue_basis,c.issue_number,c.booking_no from inv_transaction a, order_wise_pro_details b, inv_issue_master c where  a.id=b.trans_id and  c.id=a.mst_id and c.entry_form in(3) and b.entry_form in(3)  and  a.transaction_type=2  and b.po_breakdown_id in($po_id)";
			$result_yarn=sql_select($sql_yarn);
			foreach($result_yarn as $invRow)
			{
				if($invRow[csf('issue_basis')]==1)// Booking
				{
					$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('booking_no')];
				}
				else if($invRow[csf('issue_basis')]==3)// Requesition
				{
					$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('requisition_no')];
				}
			}
			
			unset($result_yarn);
			
				 $yarn_dyeing_costArray=array();
				//$yarndyeing_data=sql_select("select a.ydw_no,a.is_short, b.job_no, sum(b.amount) as amnt,sum(b.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where  a.id=b.mst_id and a.entry_form in(41,94) and b.status_active=1 and b.is_deleted=0 group by a.ydw_no,a.is_short,b.job_no");
				$yarndyeing_data="select b.booking_date,b.id,b.currency,b.is_short,b.ydw_no,a.job_no,a.yarn_color,a.product_id,avg(a.dyeing_charge) as dyeing_charge,  sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a where  b.id=a.mst_id and b.entry_form in(41,94) and a.status_active=1 and a.is_deleted=0 and a.job_no='$job_no'  group by b.id,b.currency,b.is_short,a.job_no,a.product_id,b.ydw_no,a.yarn_color,b.booking_date";
				$yarndyeing_dataResult=sql_select($yarndyeing_data);
				foreach($yarndyeing_dataResult as $yarnRow)
				{
				   $yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
				   $yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				}
				
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$yarnIssueData="select b.prod_id,sum(a.cons_amount) as cons_amount,sum(a.cons_quantity) as cons_quantity
				from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1) and a.transaction_type in(2,4,5,6) and b.entry_form ='3' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25)   and b.po_breakdown_id in($po_id) group by  b.prod_id";
			$resultyarnIssueData = sql_select($yarnIssueData);
			$all_prod_ids="";
			foreach($resultyarnIssueData as $row)
			{
				if($all_prod_ids=="") $all_prod_ids=$row[csf('prod_id')];else $all_prod_ids.=",".$row[csf('prod_id')];
				
				$yarn_issue_amt_arr[$row[csf('prod_id')]]['amt']+=$row[csf('cons_amount')];
				$yarn_issue_amt_arr[$row[csf('prod_id')]]['qty']+=$row[csf('cons_quantity')];
			}
			unset($resultyarnIssueData);
			//echo $all_prod_ids;
			//print_r($yarn_issue_amt_arr);
			$prodIds=chop($all_prod_ids,',');
			$prod_cond_for_in="";
			$prod_ids=count(array_unique(explode(",",$all_prod_ids)));
			if($db_type==2 && $prod_ids>1000)
			{
				$prod_cond_for_in=" and (";
				$prodIdsArr=array_chunk(explode(",",$prodIds),999);
				foreach($prodIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$prod_cond_for_in.=" a.prod_id in($ids) or"; 
				}
				$prod_cond_for_in=chop($prod_cond_for_in,'or ');
				$prod_cond_for_in.=")";
			}
			else
			{
				$prodIds=implode(",",array_unique(explode(",",$all_prod_ids)));
				$prod_cond_for_in=" and a.prod_id in($prodIds)";
			}
			
			if($prodIds!='' || $prodIds!=0)
			{
				$prod_cond_for_in=$prod_cond_for_in;
			}
			else
			{
				$prod_cond_for_in="and a.prod_id in(0)";
			}
			
			$sql_receive_for_issue="select c.booking_id,a.job_no,a.prod_id,max(a.transaction_date) as transaction_date,c.currency_id,c.receive_purpose,b.lot,b.color, 
			 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt,
			 sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv
			  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $prod_cond_for_in  group by c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.lot,b.color";
			$resultReceive_chek = sql_select($sql_receive_for_issue);
			
			foreach($resultReceive_chek as $row)
			{
				$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose']=$row[csf('receive_purpose')];
				$receive_date_array[$row[csf('prod_id')]]['last_trans_date']=$row[csf('transaction_date')];
				$avg_rate=$row[csf('amnt')]/$row[csf('qty')];
				$receive_array[$row[csf('prod_id')]]=$avg_rate;
			}
			unset($resultReceive_chek);
			
			
			
					$plan_details_array = return_library_array("select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where po_id in($po_id)  group by dtls_id,booking_no", "dtls_id", "booking_no");
				//print_r($plan_details_array);
				$reqs_array = array();
				$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
				foreach ($reqs_sql as $row)
				 {
					//$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
					$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
				}
				unset($reqs_sql);
				
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_cost=0;
				 $yarnTrimsData="select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color,c.supplier_id,c.product_name_details,d.issue_number,d.issue_date,d.issue_purpose
					from inv_transaction a, order_wise_pro_details b,product_details_master c,inv_issue_master d where a.id=b.trans_id and c.id=b.prod_id and d.id=a.mst_id  and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3) $po_cond_for_in ";
					// select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25) and b.po_breakdown_id in(50943) 
				
			$yarnTrimsDataArray=sql_select($yarnTrimsData); $yarnTrimsCostArray=array(); $i=0;
			$usd_id=2;$tot_iss_amnt=0;
			foreach($yarnTrimsDataArray as $invRow)
			{
				if($invRow[csf('item_category')]==1)
				{
					$yarn_issue_amt=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['amt'];
					$yarn_issue_qty=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['qty'];
					$last_trans_date=$receive_date_array[$invRow[csf('prod_id')]]['last_trans_date'];
					
					if($invRow[csf('receive_basis')]==1)//Booking Basis
					{
						$booking_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						if($invRow[csf('issue_purpose')]==2) //Yarn Dyeing purpose
						{
							$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
							$booking_type=1;
							//echo $booking_no.'= '.$booking_type.', ';
						}
						else
						{
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							//echo $is_short.'= '.$booking_type.'A ';
						}
					}
					else if($invRow[csf('receive_basis')]==3) //Requisition Basis
					{
						$booking_req_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						
						$prog_no=$reqs_array[$booking_req_no]['knit_id'];
						$booking_no=$plan_details_array[$prog_no];
						//echo $prog_no.'req';
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
					$transaction_date='';
					$transaction_date=$last_trans_date;
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					
					$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
					$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
					
					$avgrate=$issue_rate/$exchange_rate;
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($invRow[csf('entry_form')]==3 && $recv_purpose==16)//recv_purpose==16=Grey Yarn
					{
						//echo $is_short.'='.$booking_type.'='.$recv_purpose.'<br>';
						if(($booking_type==1 || $booking_type==4) && $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							//echo $invRow[csf('issue_purpose')].'X';
							
							if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
							{
								//echo $invRow[csf('mst_id')].'='.$invRow[csf('issue_purpose')].'='.$q.'='.$invRow[csf('quantity')].'-k<br>';
								
								$iss_amnt=$invRow[csf('quantity')]*$avgrate;
								//$tot_iss_amnt+=$iss_amnt;
								
								$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['grey_yarn_amt']+=$iss_amnt;
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['yq']+=$invRow[csf('quantity')];
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_number']=$invRow[csf('issue_number')];
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_date']=$invRow[csf('issue_date')];
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['product_name_details']=$invRow[csf('product_name_details')];
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['supplier_id']=$invRow[csf('supplier_id')];
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['lot']=$invRow[csf('lot')];
								$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_purpose']=$invRow[csf('issue_purpose')];
							 
								//$yarnTrimsCostArray[$invRow[csf('prod_id')]]['grey_yarn_amt_retble']+=$retble_iss_amnt;
							}
						}
						else
						{
							$iss_amnt=$invRow[csf('quantity')]*$avgrate;
							//$tot_iss_amnt+=$iss_amnt;
							$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['cpa_grey_yarn_amt']+=$iss_amnt;
							$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['cpa_yq']+=$invRow[csf('quantity')];
							$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_number']=$invRow[csf('issue_number')];
							$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_date']=$invRow[csf('issue_date')];
							$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['product_name_details']=$invRow[csf('product_name_details')];
							$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['supplier_id']=$invRow[csf('supplier_id')];
							$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['lot']=$invRow[csf('lot')];
							$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_purpose']=$invRow[csf('issue_purpose')];
						}
						
					}
					
				}
				
			}
			//print_r($yarnTrimsCostArray);
			unset($yarnTrimsDataArray);
			//edn
				/*$sql="SELECT a.booking_no, a.issue_number, a.issue_basis, a.issue_date, c.supplier_id, d.item_category, b.issue_purpose, c.lot, c.color, d.requisition_no,
				 	sum(CASE WHEN d.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose in(1,2,4,15,38) THEN b.quantity ELSE 0 END) AS issue_qnty,
					sum(CASE WHEN d.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					(CASE WHEN d.transaction_type=2 and b.entry_form ='3' and b.trans_type=2  THEN d.transaction_date ELSE null END) AS transaction_date, c.id as prod_id, c.product_name_details, c.avg_rate_per_unit
					from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d 
					where a.id=d.mst_id and d.transaction_type in(2,4,5,6) and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type in(2,4,5,6) and b.entry_form in(3,11,25) and b.po_breakdown_id in($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
					group by a.id,c.id,c.lot,c.color,d.item_category,b.issue_purpose, a.issue_number, a.issue_date, c.supplier_id, c.product_name_details, c.avg_rate_per_unit,d.transaction_type,b.entry_form,b.trans_type,d.transaction_date,a.booking_no,a.issue_basis,d.requisition_no";*/
				/*$sql_isssue="select d.issue_number,d.issue_date,a.mst_id, a.transaction_type,a.item_category,a.requisition_no, a.receive_basis, a.item_category, a.transaction_date as issue_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity as issue_qnty, b.returnable_qnty,c.supplier_id, c.lot, c.color,c.product_name_details
					from inv_transaction a, order_wise_pro_details b,product_details_master c,inv_issue_master d where a.mst_id=d.id and a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1) and a.transaction_type in(2,4,5,6) and d.status_active=1 and a.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3) and b.po_breakdown_id in($po_id) ";
                $result_issue=sql_select($sql_isssue);*/
				$usd_id=2;
				$i=1;
				foreach($yarnTrimsCostArray as $mst_id=>$mstData)
				{
					foreach($mstData as $prod_id=>$row)
					{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					
					//echo $row[csf('issue_number')]."__".$booking_type.'__'.$is_short."__".$recv_purpose."__".$issue_purpose."<br>";
					$cpa_yarn_cost=0;
					
					$issue_purpose=$row[('issue_purpose')]; 
					
					//if(($booking_type==1 || $booking_type==4) && (($is_short==2 || $is_short==1 ) &&  $booking_type!='') && ($recv_purpose==16 || $issue_purpose==2))
					//{
						//echo $avg_rate.'a';
					//echo $booking_type.'='.$is_short.'<br>';
					//if($is_short==1)
					//{
						$cpa_yarn_cost=$row[('cpa_grey_yarn_amt')];//row[('grey_yarn_amt')]
						$cpa_yarn_issued=$row[('cpa_yq')]; 
						//echo $cpa_yarn_cost.'D';
					//}
					$yarn_cost=$row[('grey_yarn_amt')]+$cpa_yarn_cost;
					$yarn_issued=$row[('yq')]+$cpa_yarn_issued;
					
					
					//$yarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['cpa_grey_yarn_amt'];
				//	echo $yarn_cost;
						//$yarn_issued=$yarnTrimsCostArray[$row[csf('prod_id')]]['yq'];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110" title="Mst Id=<? echo $mst_id;?>"><p><? echo $row[('issue_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_issue_purpose[$issue_purpose]; ?></p></td>
                        <td width="80"><p><? echo $supplier_array[$row[('supplier_id')]]; ?></p></td>
                        <td width="80"><p><? echo $row[('lot')]; ?></p></td>
                        <td width="180"><p><? echo $row[('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($yarn_issued,2); 
								$total_yarn_issue_qnty+=$yarn_issued;
                            ?>
                        </td>
                        <td align="right" width="80">
							 <? 
								
								echo number_format($yarn_cost/$yarn_issued,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								//$yarn_cost=$yarn_issued*$avg_rate;
								echo number_format($yarn_cost,4); 
								$total_yarn_cost+=$yarn_cost;
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
						//}
					}
						
						
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_cost,2);?></td>
                </tr>
                <?
                 $i=1; $total_yarn_issue_qnty=0; $total_yarn_cost=0;
				 $cancel_yarnTrimsData="select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color,c.supplier_id,c.product_name_details,d.issue_number,d.issue_date,d.issue_purpose
					from inv_transaction a, order_wise_pro_details b,product_details_master c,inv_issue_master d where a.id=b.trans_id and c.id=b.prod_id and d.id=a.mst_id  and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3) $can_po_cond_for_in ";
					// select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25) and b.po_breakdown_id in(50943) 
				
			$cancelyarnTrimsDataArray=sql_select($cancel_yarnTrimsData); $yarnTrimsCostArray=array(); $i=0;
			$usd_id=2;$tot_iss_amnt=0;
			foreach($cancelyarnTrimsDataArray as $invRow)
			{
				if($invRow[csf('item_category')]==1)
				{
					$yarn_issue_amt=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['amt'];
					$yarn_issue_qty=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['qty'];
					$last_trans_date=$receive_date_array[$invRow[csf('prod_id')]]['last_trans_date'];
					
					if($invRow[csf('receive_basis')]==1)//Booking Basis
					{
						$booking_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						if($invRow[csf('issue_purpose')]==2) //Yarn Dyeing purpose
						{
							$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
							$booking_type=1;
							//echo $booking_no.'= '.$booking_type.', ';
						}
						else
						{
							$booking_type=$booking_array[$booking_no]['btype'];
							$is_short=$booking_array[$booking_no]['is_short'];
							//echo $is_short.'= '.$booking_type.'A ';
						}
					}
					else if($invRow[csf('receive_basis')]==3) //Requisition Basis
					{
						$booking_req_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
						
						$prog_no=$reqs_array[$booking_req_no]['knit_id'];
						$booking_no=$plan_details_array[$prog_no];
						//echo $prog_no.'req';
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
					}
					$transaction_date='';
					$transaction_date=$last_trans_date;
					if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					
					$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
					$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
					
					$avgrate=$issue_rate/$exchange_rate;
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($invRow[csf('entry_form')]==3 && $recv_purpose==16)//recv_purpose==16=Grey Yarn
					{
						//echo $is_short.'='.$booking_type.'='.$recv_purpose.'<br>';
						if(($booking_type==1 || $booking_type==4) && $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
						{
							//echo $invRow[csf('issue_purpose')].'X';
							
							if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
							{
								//echo $invRow[csf('mst_id')].'='.$invRow[csf('issue_purpose')].'='.$q.'='.$invRow[csf('quantity')].'-k<br>';
								
								$iss_amnt=$invRow[csf('quantity')]*$avgrate;
								//$tot_iss_amnt+=$iss_amnt;
								
								$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
								$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['grey_yarn_amt']+=$iss_amnt;
								$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['yq']+=$invRow[csf('quantity')];
								$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_number']=$invRow[csf('issue_number')];
								$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_date']=$invRow[csf('issue_date')];
								$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['product_name_details']=$invRow[csf('product_name_details')];
								$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['supplier_id']=$invRow[csf('supplier_id')];
								$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['lot']=$invRow[csf('lot')];
								$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_purpose']=$invRow[csf('issue_purpose')];
							 
								//$yarnTrimsCostArray[$invRow[csf('prod_id')]]['grey_yarn_amt_retble']+=$retble_iss_amnt;
							}
						}
						else
						{
							$iss_amnt=$invRow[csf('quantity')]*$avgrate;
							//$tot_iss_amnt+=$iss_amnt;
							$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['cpa_grey_yarn_amt']+=$iss_amnt;
							$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['cpa_yq']+=$invRow[csf('quantity')];
							$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_number']=$invRow[csf('issue_number')];
							$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_date']=$invRow[csf('issue_date')];
							$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['product_name_details']=$invRow[csf('product_name_details')];
							$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['supplier_id']=$invRow[csf('supplier_id')];
							$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['lot']=$invRow[csf('lot')];
							$cancelyarnTrimsCostArray[$invRow[csf('mst_id')]][$invRow[csf('prod_id')]]['issue_purpose']=$invRow[csf('issue_purpose')];
						}
						
					}
					
				}
				
			}
			//print_r($yarnTrimsCostArray);
			unset($cancelyarnTrimsDataArray);
				?>
                 <thead>
                    <th colspan="6"><b>Cancel PO Grey Yarn Details</b></th>
                </thead>
                <thead>
                	<th width="40">SL</th>
                	<th width="110">Issue Id</th>
                    <th width="80">Yarn Desc</th>
                     <th width="80">Grey Yarn Qty</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               </thead>
               <?
               $usd_id=2;
				$m=1;$can_total_yarn_qty=$can_total_yarn_cost=0;
				foreach($cancelyarnTrimsCostArray as $mst_id=>$mstData)
				{
					foreach($mstData as $prod_id=>$row)
					{
					if ($m%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					
					//echo $row[csf('issue_number')]."__".$booking_type.'__'.$is_short."__".$recv_purpose."__".$issue_purpose."<br>";
					$cpa_yarn_cost=0;
					$issue_purpose=$row[('issue_purpose')]; 
					$cpa_yarn_cost=$row[('cpa_grey_yarn_amt')];//row[('grey_yarn_amt')]
					$cpa_yarn_issued=$row[('cpa_yq')]; 
					
					$yarn_cost=$row[('grey_yarn_amt')]+$cpa_yarn_cost;
					$yarn_issued=$row[('yq')]+$cpa_yarn_issued;
			   ?>
               <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trm_<? echo $m; ?>','<? echo $bgcolor;?>')" id="trm_<? echo $m;?>">
                    	<td width="40"><? echo $m; ?></td>
                    	<td width="110"><p><? echo $row[('issue_number')]; ?></p></td>
                        <td width="180"><p><? echo $row[('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($yarn_issued,2); 
                            ?>
                        </td>
                        <td title="<? echo $yarn_cost/$yarn_issued;?>" align="right" width="80">
							<? 
								echo number_format($yarn_cost/$yarn_issued,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								//$yarn_return_cost=$iss_ret_amnt;
								echo number_format($yarn_cost,2); 
								$can_total_yarn_qty+=$yarn_issued;
								$can_total_yarn_cost+=$yarn_cost;
                            ?>
                        </td>
                    </tr>
                    <?
						$m++;
						}
					
					}
					?>
                    <tr style="font-weight:bold">
                    <td align="right" colspan="3">Total</td>
                    <td align="right"><? echo number_format($can_total_yarn_qty,2);?></td>
                     <td>&nbsp;</td>
                    <td align="right"><? echo number_format($can_total_yarn_cost,2);?></td>
                </tr>
                    
               
               
                
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="40">SL</th>
                	<th width="110">Return Id</th>
                    <th width="80">Return Date</th>
                     <th width="80">Purpose</th>
                    <th width="80">Supplier</th>
                    <th width="80">Lot</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_cost=0;
				 $sql="select a.booking_no,a.recv_number,a.receive_basis, a.receive_date,b.issue_purpose, sum(b.quantity) as returned_qnty, c.id as prod_id, c.supplier_id,c.color,c.lot, c.product_name_details, c.avg_rate_per_unit,d.requisition_no from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.id, c.id, a.recv_number, a.booking_no,a.receive_date,b.issue_purpose, c.supplier_id,c.color,c.lot, c.product_name_details,a.receive_basis, c.avg_rate_per_unit,d.requisition_no";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					$issue_basis=$row[csf('receive_basis')];
					$issue_purpose=$row[csf('issue_purpose')];
					$item_category=$row[csf('item_category')];
					if($issue_basis==1) //Booking Basis
						{
							$booking_no=$row[csf('booking_no')];
						}
						else if($issue_basis==3) //Requisition Basis
						{
							$booking_req_no=$row[csf('booking_no')];
							
							$prog_no=$reqs_array[$booking_req_no]['knit_id'];
							$booking_no=$plan_details_array[$prog_no];
							//echo $prog_no.'req';
						}
						$booking_type=$booking_array[$booking_no]['btype'];
						$is_short=$booking_array[$booking_no]['is_short'];
						//echo $issue_basis.'dsd';
						$transaction_date=$row[csf('receive_date')];
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						
						$currency_id=$receive_curr_array[$row[csf('prod_id')]];
						if($receive_array[$row[csf('prod_id')]]>0)
						{
							
							//echo $currency_id.'D';
							if($currency_id==1) //Taka
							{
								$avg_rate=$receive_array[$row[csf('prod_id')]]/$currency_rate;
								//echo $avg_rate.'C';
							}
							else
							{
								$avg_rate=$receive_array[$row[csf('prod_id')]];	
								//echo $avg_rate.'B';
							}
						}
						else
						{
							$avg_rate=$avg_rate_array[$row[csf('prod_id')]]/$currency_rate;
							//echo $avg_rate.'A';
						}
						//$avg_rate=$receive_array[$row[csf('prod_id')]]/$currency_rate;
						//echo $avg_rate;
						$recv_purpose=$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose'];
						//echo $recv_purpose.', ';
					if($issue_purpose==1 || $issue_purpose==4)
					{
                    	if($recv_purpose==16)
						{
							$yarn_returned=$row[csf('returned_qnty')];
							$iss_ret_amnt=$row[csf('returned_qnty')]*$avg_rate;
							//echo $yarn_returned.'d'.$avg_rate;
						}
					}
					else if($issue_purpose==2) //Yarn Dyeing
					{
						if($recv_purpose==16)
						{
							$yarn_returned=$row[csf('returned_qnty')];
							$iss_ret_amnt=$row[csf('returned_qnty')]*$avg_rate;
							//echo $yarn_returned.'A';
						}
					}
					//$yarn_returned=$row[csf('returned_qnty')];
					//$iss_ret_amnt=$yarn_returned*$avg_rate;
					//$retble_iss_ret_amnt=$returnable_ret_qnty*$avg_rate;
						
					//echo $booking_type.'='.$is_short;
					if(($booking_type==1 || $booking_type==4) &&  ($is_short==2 || $is_short==1) &&  $booking_type!='' && $recv_purpose==16) //Main and Sample Faric Booking
						{
							$total_yarn_return_qnty+=$yarn_returned;
					//echo $iss_ret_amnt.'A'.$avg_rate;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_issue_purpose[$issue_purpose]; ?></p></td>
						<td width="80"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
						<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
                     
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($yarn_returned,2); 
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								/*if($receive_array[$row[csf('prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$ex_rate;
								}*/
								echo number_format($yarn_return_cost/$yarn_returned,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$yarn_return_cost=$iss_ret_amnt;
								echo number_format($yarn_return_cost,2); 
								$total_yarn_return_cost+=$yarn_return_cost;
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
						}
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                     <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_return_cost,2);?></td>
                </tr>
               
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                     <th>&nbsp;</th>
                    <th align="right">Balance</th>
                    <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
	<?
	exit();
}

if($action=="yarn_dye_twist_cost_actual")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
		
	$job_array=array();
	 $sql_job="select  a.job_quantity,b.job_no_mst,b.po_quantity,b.id as po_id from wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and b.id in($po_id)  and b.status_active=1 and b.is_deleted=0";
	$resultjob = sql_select($sql_job);
	$job_no="";
	foreach($resultjob as $row)
	{
		$job_array[$row[csf('po_id')]]['po_qty']+=$row[csf('po_quantity')];
		$job_array[$row[csf('po_id')]]['job_qty']=$row[csf('job_quantity')];
		$job_no.="'".$row[csf('job_no_mst')]."'".',';
	}
	$job_nos=rtrim($job_no,',');
	$jobnos=implode(",",array_unique(explode(",",$job_nos)));
	$po_ids=array_unique(explode(",",$po_id));
	

	?>
	<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	function generate_worder_report(version_type, booking_no, cbo_company_name, update_id,form_name,entry_form, cbo_pay_mode, cbo_supplier_name, show_comment, path,action_type) {
				
				
				if (action_type == 'show_trim_booking_report')
				{
					var action_method = "action=show_trim_booking_report";
				}
				
				if (entry_form == 41) {

						if (version_type == 1) {
							report_title = "&report_title=Yarn Dyeing Charge Booking";
							http.open("POST", "../../../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php", true);
						}
						else if (version_type == 2) {
							report_title = "&report_title=Partial Fabric Booking";
							http.open("POST", "../../../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php", true);
						} 
						 
					
					
				}else {
					report_title = "&report_title=Yarn Service Work Order";
					http.open("POST", "../../../../order/woven_order/requires/yarn_service_work_order_controller.php", true);
				}

				var data = action_method + report_title +
				'&txt_booking_no=' + "'" + booking_no + "'" +
				'&cbo_company_name=' + "'" + cbo_company_name + "'" +
				'&update_id=' + "'" + update_id + "'" +
				'&cbo_pay_mode=' + "'" + cbo_pay_mode + "'" +
				'&cbo_supplier_name=' + "'" + cbo_supplier_name + "'" +
				'&show_comment=' + "'" + show_comment + "'" +
				'&path=' + "'" + path + "'";
				//alert(data);
				
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_reponse;
			}

			function generate_fabric_report_reponse() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
					d.close();
				}
			}
	</script>	
	<div style="width:1000px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1000px; margin-left:7px">
		<div id="report_container">
            <table class="rpt_table" width="1000" cellpadding="0" cellspacing="0"  border="1" rules="all">
            	<thead>
					<th colspan="10"><b>Yarn Receive (Additional Value)</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Recv. Id</th>
					 <th width="120">Wo No</th>
                    <th width="80">Recv. Date</th>
                    <th width="80">Purpose</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Recv. Qty.</th>
                    <th width="80">Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
				<tbody id="table_search">
                <?
			//	$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
			$yarn_dyeing_costArray=array(); //product_id
			$yarndyeing_sql="select b.booking_date,b.id,b.company_id,b.supplier_id,b.entry_form,b.pay_mode,b.budget_version,b.currency,b.is_short,b.ydw_no,a.job_no,a.yarn_color,a.product_id,avg(a.dyeing_charge) as dyeing_charge,  sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a where  b.id=a.mst_id and b.entry_form in(41,94) and a.status_active=1 and a.is_deleted=0 and a.job_no in($jobnos) group by b.id,b.company_id,b.budget_version,b.supplier_id,b.pay_mode,b.entry_form,b.currency,b.is_short,a.job_no,a.product_id,b.ydw_no,a.yarn_color,b.booking_date";
			$yarndyeing_result = sql_select($yarndyeing_sql);
				foreach($yarndyeing_result as $yarnRow)
				{
				  $yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
				  $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job']=$yarnRow[csf('job_no')];
				  $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['ydw_no']=$yarnRow[csf('ydw_no')];
				   $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['budget_version']=$yarnRow[csf('budget_version')];
				   $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['company_id']=$yarnRow[csf('company_id')];
				   $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['pay_mode']=$yarnRow[csf('pay_mode')];
				 $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['supplier_id']=$yarnRow[csf('supplier_id')];
				 $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['entry_form']=$yarnRow[csf('entry_form')];
					 
				  $yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				  $yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['booking_date']=$yarnRow[csf('booking_date')];
				  $yarn_dyeing_curr_arr[$yarnRow[csf('ydw_no')]]['currency']=$yarnRow[csf('currency')];
				  
				  $yarn_dyeing_rate_arr[$yarnRow[csf('job_no')]][$yarnRow[csf('yarn_color')]]['rate']=$yarnRow[csf('dyeing_charge')];
				}
				
                $i=1; $total_yarn_recv_qnty=0; $total_yarn_cost=0;$usd_id=2;
				
				  $receive_array=array();
				$sql_receive="select  c.id,c.recv_number,c.receive_basis,c.supplier_id,c.receive_date,c.receive_purpose,c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.product_name_details,b.lot,b.color, 
				 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt,
				 sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv,
				  sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.cons_quantity ELSE 0 END) AS cons_quantity_recv
				  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1   and a.is_deleted=0 and c.receive_purpose in(2,15,38) and a.job_no in($jobnos)  group by  c.id,c.booking_id,c.recv_number,c.receive_basis,c.receive_date,a.job_no,a.prod_id,c.supplier_id,b.product_name_details,c.currency_id,c.receive_purpose,b.lot,b.color";
				$resultReceive = sql_select($sql_receive);
				$prod_ids="";
				foreach($resultReceive as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					
					$prod_ids.=$row[csf('prod_id')].',';
					$jobNo=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['job'];
					$ydw_no=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['ydw_no'];
					$budget_version=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['budget_version'];
					$company_id=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['company_id'];
					$entry_form=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['entry_form'];
					$form_name="yarn_dyeing_wo_booking";
					$action_ydn="show_trim_booking_report";
					$show_comment=1;$path="../../../../";
					$cbo_pay_mode=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['pay_mode'];
					$cbo_supplier_name=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['supplier_id'];
					//echo $jobNo;
					$yarn_rec_job_arr[$row[csf('recv_number')]]['job']=$jobNo;
					$yarn_rec_job_arr[$row[csf('recv_number')]]['ydw_no']=$ydw_no;
					$booking_date=$yarn_dyeing_isshort_arr[$ydw_no]['booking_date'];
					$ydye_rate=$yarn_dyeing_rate_arr[$row[csf('job_no')]][$row[csf('color')]]['rate'];
					
					$currency=$yarn_dyeing_curr_arr[$ydw_no]['currency'];
						//echo $currency.'d'.$chy_rate;
					$order_amount_recv=$row[csf('order_amount_recv')];
					//$cons_quantity=$row[csf('cons_quantity_recv')];
					$transaction_date=$booking_date;
					if($db_type==0)
					{
						$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					}
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						//echo $currency_id.'D';
					if($currency==1) //Taka
					{
						$dye_charge=$ydye_rate/$currency_rate;
					}
					else
					{
						$dye_charge=$ydye_rate;	
					}
					$po_quantity=$job_qty=$avg_recv_amount=$avg_cons_quantity=0;
					foreach($po_ids as $poid)
					{
						
					$po_quantity=$job_array[$poid]['po_qty'];
					$job_qty=$job_array[$poid]['job_qty'];
					
					$cons_quantity=$row[csf('cons_quantity_recv')];
					$recv_amount=$cons_quantity*$dye_charge;
					$avg_recv_amount+=($recv_amount/$job_qty)*$po_quantity;
					$avg_cons_quantity+=($cons_quantity/$job_qty)*$po_quantity;
					}
					
							/* $recv_amount=$cons_quantity*$dye_charge;
							$avg_recv_amount=($recv_amount/$job_qty)*$po_quantity;
						//echo $recv_amount.'/'.$job_qty.'*'.$po_quantity.'<br>';
						$avg_cons_quantity=($cons_quantity/$job_qty)*$po_quantity; */
						$is_short=$yarn_dyeing_isshort_arr[$ydw_no]['is_short'];
					//echo $booking_type.'='.$is_short;
					//echo $avg_rate.'a';
					//$ydw_no=$service_booking_array[$row[csf('id')]]['ydw_no'];
					if($is_short==2)
					{
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
						 <td width="120"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_worder_report(<?php echo $budget_version;?>,'<?php echo $ydw_no;?>',<?php echo $company_id;?>,'<?php echo $row[csf('booking_id')];?>','<?php echo $form_name;?>','<?php echo $entry_form;?>',<?php echo $cbo_pay_mode;?>,'<?php echo $cbo_supplier_name;?>',<?php echo $show_comment;?>,'<?php echo $path;?>','<?php echo $action_ydn;?>')"><?php echo $ydw_no;?></span></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="70"><p><? echo $yarn_issue_purpose[$row[csf('receive_purpose')]]; ?></p></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                      
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90" title="<? echo $cons_quantity; ?>">
							<? 
								echo number_format($avg_cons_quantity,2); 
								$total_yarn_recv_qnty+=$avg_cons_quantity;
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								
								echo number_format($dye_charge,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
                        
							<?
								//$yarn_cost=$yarn_issued*$avg_rate;
								echo number_format($avg_recv_amount,2); 
								$total_yarn_cost+=$avg_recv_amount;
                            ?>
                        </td>
                    </tr>
                <?
               
						
						
						 $i++;
					}
                }
				//print_r($yarn_rec_job_arr);
                ?>
                <tr style="font-weight:bold; background:#CCC">
                    <td>&nbsp;</td>
					 <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    <td align="right"> Total</td>
                    <td align="right"><? echo number_format($total_yarn_recv_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_cost,2);?></td>
                </tr>
				</tbody>
                <? //die;?>
                <div>
                <thead >
                    <th colspan="10"><b>Yarn Recv Return</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Return Id</th>
                    <th width="80">Return Date</th>
                    <th width="80">Recv MRR No</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qty.</th>
                    <th width="80">Rate (USD)</th>
                    <th  colspan="2">Cost ($)</th>
               </thead>
			   <tbody id="table_search2">
                <?
				$prod_idss=rtrim($prod_ids,',');
				if($prod_idss!='') $prod_idss=$prod_idss;else $prod_idss=0;
                $total_yarn_return_qnty=0; $total_yarn_return_cost=0;
				 $sql_receive_ret="select a.prod_id,b.product_name_details,b.lot,b.color,c.received_mrr_no,c.issue_number,c.issue_date,c.issue_purpose,c.supplier_id,
				 sum(a.cons_quantity) as cons_quantity
				  from inv_transaction a,product_details_master b,inv_issue_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=8 and c.item_category=1 and a.transaction_type=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id in($prod_idss)  group by a.prod_id,b.lot,b.color,b.product_name_details,c.received_mrr_no,c.issue_number,c.issue_purpose,c.supplier_id,c.issue_date";
				$resultReceive_ret = sql_select($sql_receive_ret);
            $k=1;
				foreach($resultReceive_ret as $row)
				{
					if ($k%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
						$ydw_no=$yarn_rec_job_arr[$row[csf('received_mrr_no')]]['ydw_no'];
						$recv_job=$yarn_rec_job_arr[$row[csf('received_mrr_no')]]['job'];
						$is_short=$yarn_dyeing_isshort_arr[$ydw_no]['is_short'];
						$booking_date=$yarn_dyeing_isshort_arr[$ydw_no]['booking_date'];
						$ydye_rate=$yarn_dyeing_rate_arr[$recv_job][$row[csf('color')]]['rate'];
						$currency=$yarn_dyeing_curr_arr[$ydw_no]['currency']; 
							//echo $ydye_rate.'='.$ydw_no;
						$transaction_date=$booking_date;
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
							//echo $currency_id.'D';
						if($currency==1) //Taka
						{
							$dye_charge=$ydye_rate/$currency_rate;
						}
						else
						{
							$dye_charge=$ydye_rate;	
						}
						$po_quantity=$job_qty=$avg_recv_ret_amount=$avg_recv_ret_qty=0;
						foreach($po_ids as $poid)
						{
							
						 $po_quantity=$job_array[$poid]['po_qty'];
						 $job_qty=$job_array[$poid]['job_qty'];

						 $recv_ret_amount=$row[csf('cons_quantity')]*$dye_charge;
						 $avg_recv_ret_amount+=($recv_ret_amount/$job_qty)*$po_quantity;
						 $avg_recv_ret_qty+=($row[csf('cons_quantity')]/$job_qty)*$po_quantity;
						}
					
						
						
						
						$total_yarn_return_qnty+=$avg_recv_ret_qty;
					if($is_short==2) //Main and Sample Faric Booking
					 {
						//echo $is_short.'ddd';
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                    	<td width="40"><? echo $k; ?></td>
                    	<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td width="70"><p><? echo $row[csf('received_mrr_no')]; ?></p></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($avg_recv_ret_qty,2); 
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								echo number_format($dye_charge,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right" width="80" colspan="2">
							<?
								$yarn_return_cost=$avg_recv_ret_amount;
								echo number_format($yarn_return_cost,2); 
								$total_yarn_return_cost+=$yarn_return_cost;
                            ?>
                        </td>
                    </tr>
                <?
                	$k++;
						}
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                     <td>&nbsp;</td>
                    <td align="right"  colspan="2"><? echo number_format($total_yarn_return_cost,2);?></td>
                </tr>
				</tbody>
               
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                     <th>&nbsp;</th>
                    <th align="right">Balance</th>
                    <th align="right"><? echo number_format(($total_yarn_recv_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"  colspan="2"><? echo number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
                </tfoot>
                </div>
            </table>	
		</div>
	<script>
  	setFilterGrid("table_search",-1);setFilterGrid("table_search2",-1);
  </script>
	</fieldset>  
	<?
	exit();
}

if($action=="yarn_dye_twist_cost_actual_summary")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
		
	$job_array=array();
	 $sql_job="select  a.job_quantity,b.job_no_mst,b.po_quantity,b.id as po_id from wo_po_break_down b, wo_po_details_master a where  a.job_no=b.job_no_mst and b.id in($po_id)  and b.status_active=1 and b.is_deleted=0";
	$resultjob = sql_select($sql_job);
	$job_no="";
	foreach($resultjob as $row)
	{
		$job_array[$row[csf('po_id')]]['po_qty']+=$row[csf('po_quantity')];
		$job_array[$row[csf('po_id')]]['job_qty']=$row[csf('job_quantity')];
		$job_no.="'".$row[csf('job_no_mst')]."'".',';
	}
	$job_nos=rtrim($job_no,',');
	$jobnos=implode(",",array_unique(explode(",",$job_nos)));
	$po_ids=array_unique(explode(",",$po_id));
	

	?>
	<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	function generate_worder_report(version_type, booking_no, cbo_company_name, update_id,form_name,entry_form, cbo_pay_mode, cbo_supplier_name, show_comment, path,action_type) {
				
				
				if (action_type == 'show_trim_booking_report')
				{
					var action_method = "action=show_trim_booking_report";
				}
				
				if (entry_form == 41) {

						if (version_type == 1) {
							report_title = "&report_title=Yarn Dyeing Charge Booking";
							http.open("POST", "../../../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php", true);
						}
						else if (version_type == 2) {
							report_title = "&report_title=Partial Fabric Booking";
							http.open("POST", "../../../../order/woven_order/requires/yarn_dyeing_charge_booking_controller2.php", true);
						} 
						 
					
					
				}else {
					report_title = "&report_title=Yarn Service Work Order";
					http.open("POST", "../../../../order/woven_order/requires/yarn_service_work_order_controller.php", true);
				}

				var data = action_method + report_title +
				'&txt_booking_no=' + "'" + booking_no + "'" +
				'&cbo_company_name=' + "'" + cbo_company_name + "'" +
				'&update_id=' + "'" + update_id + "'" +
				'&cbo_pay_mode=' + "'" + cbo_pay_mode + "'" +
				'&cbo_supplier_name=' + "'" + cbo_supplier_name + "'" +
				'&show_comment=' + "'" + show_comment + "'" +
				'&path=' + "'" + path + "'";
				//alert(data);
				
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_reponse;
			}

			function generate_fabric_report_reponse() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
					d.close();
				}
			}
	</script>	
	<div style="width:1000px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1000px; margin-left:7px">
		<div id="report_container">
            <table class="rpt_table" width="1000" cellpadding="0" cellspacing="0"  border="1" rules="all">
            	<thead>
					<th colspan="10"><b>Yarn Receive (Additional Value)</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Recv. Id</th>
					 <th width="120">Wo No</th>
                    <th width="80">Recv. Date</th>
                    <th width="80">Purpose</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Recv. Qty.</th>
                    <th width="80">Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
				<tbody id="table_search">
                <?
			//	$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
			$yarn_dyeing_costArray=array(); //product_id
			$yarndyeing_sql="select b.booking_date,b.id,b.company_id,b.supplier_id,b.entry_form,b.pay_mode,b.budget_version,b.currency,b.is_short,b.ydw_no,a.job_no,a.yarn_color,a.product_id,avg(a.dyeing_charge) as dyeing_charge,  sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a where  b.id=a.mst_id and b.entry_form in(41,94) and a.status_active=1 and a.is_deleted=0 and a.job_no in($jobnos) group by b.id,b.company_id,b.budget_version,b.supplier_id,b.pay_mode,b.entry_form,b.currency,b.is_short,a.job_no,a.product_id,b.ydw_no,a.yarn_color,b.booking_date";
			$yarndyeing_result = sql_select($yarndyeing_sql);
				foreach($yarndyeing_result as $yarnRow)
				{
				  $yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
				  $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job']=$yarnRow[csf('job_no')];
				  $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['ydw_no']=$yarnRow[csf('ydw_no')];
				   $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['budget_version']=$yarnRow[csf('budget_version')];
				   $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['company_id']=$yarnRow[csf('company_id')];
				   $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['pay_mode']=$yarnRow[csf('pay_mode')];
				 $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['supplier_id']=$yarnRow[csf('supplier_id')];
				 $yarn_dyeing_costArray2[$yarnRow[csf('id')]]['entry_form']=$yarnRow[csf('entry_form')];
					 
				  $yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				  $yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['booking_date']=$yarnRow[csf('booking_date')];
				  $yarn_dyeing_curr_arr[$yarnRow[csf('ydw_no')]]['currency']=$yarnRow[csf('currency')];
				  
				  $yarn_dyeing_rate_arr[$yarnRow[csf('job_no')]][$yarnRow[csf('yarn_color')]]['rate']=$yarnRow[csf('dyeing_charge')];
				}
				
                $i=1; $total_yarn_recv_qnty=0; $total_yarn_cost=0;$usd_id=2;
				$sql_po="select c.id,c.recv_number,c.receive_basis,c.supplier_id,c.receive_date,c.receive_purpose,c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.product_name_details,b.lot,b.color, 
				sum(a.order_qnty) as qty, sum(a.order_amount) as amnt,
				sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv,
				 sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.cons_quantity ELSE 0 END) AS cons_quantity_recv,SUM (d.po_quantity) AS po_quantity from product_details_master b,inv_receive_master c,  inv_transaction a LEFT JOIN wo_po_break_down d ON a.job_no = d.job_no_mst AND d.status_active = 1 AND d.is_deleted = 0 where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1   and a.is_deleted=0 and c.receive_purpose in(2,15,38) and a.job_no in($jobnos) group by  c.id,c.booking_id,c.recv_number,c.receive_basis,c.receive_date,a.job_no,a.prod_id,c.supplier_id,b.product_name_details,c.currency_id,c.receive_purpose,b.lot,b.color";
				$resultPO = sql_select($sql_po);
				foreach($resultPO as $row)
				{
					$po_rec_quantity=$row[csf('po_quantity')];
				}
				unset($resultPO);
				$receive_array=array();
				$sql_receive="select  c.id,c.recv_number,c.receive_basis,c.supplier_id,c.receive_date,c.receive_purpose,c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.product_name_details,b.lot,b.color, 
				 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt,
				 sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv,
				  sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.cons_quantity ELSE 0 END) AS cons_quantity_recv
				  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1   and a.is_deleted=0 and c.receive_purpose in(2,15,38) and a.job_no in($jobnos)  group by  c.id,c.booking_id,c.recv_number,c.receive_basis,c.receive_date,a.job_no,a.prod_id,c.supplier_id,b.product_name_details,c.currency_id,c.receive_purpose,b.lot,b.color";
				$resultReceive = sql_select($sql_receive);
				$prod_ids="";
				foreach($resultReceive as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					$po_quantity=$job_qty=0;
					foreach($po_ids as $poid)
					{
						
					 $po_quantity+=$job_array[$poid]['po_qty'];
					$job_qty=$job_array[$poid]['job_qty'];
					}
					$prod_ids.=$row[csf('prod_id')].',';
					$jobNo=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['job'];
					$ydw_no=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['ydw_no'];
					$budget_version=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['budget_version'];
					$company_id=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['company_id'];
					$entry_form=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['entry_form'];
					$form_name="yarn_dyeing_wo_booking";
					$action_ydn="show_trim_booking_report";
					$show_comment=1;$path="../../../../";
					$cbo_pay_mode=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['pay_mode'];
					$cbo_supplier_name=$yarn_dyeing_costArray2[$row[csf('booking_id')]]['supplier_id'];
					//echo $jobNo;
					$yarn_rec_job_arr[$row[csf('recv_number')]]['job']=$jobNo;
					$yarn_rec_job_arr[$row[csf('recv_number')]]['ydw_no']=$ydw_no;
					$booking_date=$yarn_dyeing_isshort_arr[$ydw_no]['booking_date'];
					$ydye_rate=$yarn_dyeing_rate_arr[$row[csf('job_no')]][$row[csf('color')]]['rate'];
					
					$currency=$yarn_dyeing_curr_arr[$ydw_no]['currency'];
						//echo $currency.'d'.$chy_rate;
					$order_amount_recv=$row[csf('order_amount_recv')];
					$cons_quantity=$row[csf('cons_quantity_recv')];
					$transaction_date=$booking_date;
					if($db_type==0)
					{
						$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					}
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						//echo $currency_id.'D';
					if($currency==1) //Taka
					{
						$dye_charge=$ydye_rate/$currency_rate;
					}
					else
					{
						$dye_charge=$ydye_rate;	
					}
							$recv_amount=$cons_quantity*$dye_charge;
							$avg_recv_amount=($recv_amount/$job_qty)*$po_rec_quantity;
						//echo $recv_amount.'/'.$job_qty.'*'.$po_rec_quantity.'<br>';
						$avg_cons_quantity=($cons_quantity/$job_qty)*$po_rec_quantity;
						$is_short=$yarn_dyeing_isshort_arr[$ydw_no]['is_short'];
					//echo $booking_type.'='.$is_short;
					//echo $avg_rate.'a';
					//$ydw_no=$service_booking_array[$row[csf('id')]]['ydw_no'];
					if($is_short==2)
					{
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
						 <td width="120"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_worder_report(<?php echo $budget_version;?>,'<?php echo $ydw_no;?>',<?php echo $company_id;?>,'<?php echo $row[csf('booking_id')];?>','<?php echo $form_name;?>','<?php echo $entry_form;?>',<?php echo $cbo_pay_mode;?>,'<?php echo $cbo_supplier_name;?>',<?php echo $show_comment;?>,'<?php echo $path;?>','<?php echo $action_ydn;?>')"><?php echo $ydw_no;?></span></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="70"><p><? echo $yarn_issue_purpose[$row[csf('receive_purpose')]]; ?></p></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                      
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90" title="<? echo $cons_quantity; ?>">
							<? 
								echo number_format($avg_cons_quantity,2); 
								$total_yarn_recv_qnty+=$avg_cons_quantity;
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								
								echo number_format($dye_charge,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
                        
							<?
								//$yarn_cost=$yarn_issued*$avg_rate;
								echo number_format($avg_recv_amount,2); 
								$total_yarn_cost+=$avg_recv_amount;
                            ?>
                        </td>
                    </tr>
                <?
               
						
						
						 $i++;
					}
                }
				//print_r($yarn_rec_job_arr);
                ?>
                <tr style="font-weight:bold; background:#CCC">
                    <td>&nbsp;</td>
					 <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    <td align="right"> Total</td>
                    <td align="right"><? echo number_format($total_yarn_recv_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_cost,2);?></td>
                </tr>
				</tbody>
                <? //die;?>
                <div>
                <thead >
                    <th colspan="10"><b>Yarn Recv Return</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Return Id</th>
                    <th width="80">Return Date</th>
                    <th width="80">Recv MRR No</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qty.</th>
                    <th width="80">Rate (USD)</th>
                    <th  colspan="2">Cost ($)</th>
               </thead>
			   <tbody id="table_search2">
                <?
				$prod_idss=rtrim($prod_ids,',');
				if($prod_idss!='') $prod_idss=$prod_idss;else $prod_idss=0;
                $total_yarn_return_qnty=0; $total_yarn_return_cost=0;
				 $sql_receive_ret="select a.prod_id,b.product_name_details,b.lot,b.color,c.received_mrr_no,c.issue_number,c.issue_date,c.issue_purpose,c.supplier_id,
				 sum(a.cons_quantity) as cons_quantity
				  from inv_transaction a,product_details_master b,inv_issue_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=8 and c.item_category=1 and a.transaction_type=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id in($prod_idss)  group by a.prod_id,b.lot,b.color,b.product_name_details,c.received_mrr_no,c.issue_number,c.issue_purpose,c.supplier_id,c.issue_date";
				$resultReceive_ret = sql_select($sql_receive_ret);
            $k=1;
				foreach($resultReceive_ret as $row)
				{
					if ($k%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
						$ydw_no=$yarn_rec_job_arr[$row[csf('received_mrr_no')]]['ydw_no'];
						$recv_job=$yarn_rec_job_arr[$row[csf('received_mrr_no')]]['job'];
						$is_short=$yarn_dyeing_isshort_arr[$ydw_no]['is_short'];
						$booking_date=$yarn_dyeing_isshort_arr[$ydw_no]['booking_date'];
						$ydye_rate=$yarn_dyeing_rate_arr[$recv_job][$row[csf('color')]]['rate'];
						$currency=$yarn_dyeing_curr_arr[$ydw_no]['currency']; 
							//echo $ydye_rate.'='.$ydw_no;
						$transaction_date=$booking_date;
						if($db_type==0)
						{
							$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
						}
						else
						{
							$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
						}
						$currency_rate=set_conversion_rate($usd_id,$conversion_date );
							//echo $currency_id.'D';
						if($currency==1) //Taka
						{
							$dye_charge=$ydye_rate/$currency_rate;
						}
						else
						{
							$dye_charge=$ydye_rate;	
						}
						$po_quantity=$job_qty=0;
						foreach($po_ids as $poid)
						{
							
						 $po_quantity+=$job_array[$poid]['po_qty'];
						 $job_qty=$job_array[$poid]['job_qty'];
						}
					
						$recv_ret_amount=$row[csf('cons_quantity')]*$dye_charge;
						$avg_recv_ret_amount=($recv_ret_amount/$job_qty)*$po_quantity;
						//echo $recv_ret_amount.'/'.$job_qty.'*'.$po_quantity.'<br>';
						$avg_recv_ret_qty=($row[csf('cons_quantity')]/$job_qty)*$po_quantity;
						
						
						$total_yarn_return_qnty+=$avg_recv_ret_qty;
					if($is_short==2) //Main and Sample Faric Booking
					 {
						//echo $is_short.'ddd';
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                    	<td width="40"><? echo $k; ?></td>
                    	<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td width="70"><p><? echo $row[csf('received_mrr_no')]; ?></p></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($avg_recv_ret_qty,2); 
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								echo number_format($dye_charge,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right" width="80" colspan="2">
							<?
								$yarn_return_cost=$avg_recv_ret_amount;
								echo number_format($yarn_return_cost,2); 
								$total_yarn_return_cost+=$yarn_return_cost;
                            ?>
                        </td>
                    </tr>
                <?
                	$k++;
						}
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                     <td>&nbsp;</td>
                    <td align="right"  colspan="2"><? echo number_format($total_yarn_return_cost,2);?></td>
                </tr>
				</tbody>
               
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                     <th>&nbsp;</th>
                    <th align="right">Balance</th>
                    <th align="right"><? echo number_format(($total_yarn_recv_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"  colspan="2"><? echo number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
                </tfoot>
                </div>
            </table>	
		</div>
	<script>
  	setFilterGrid("table_search",-1);setFilterGrid("table_search2",-1);
  </script>
	</fieldset>  
	<?
	exit();
}


if($action=="grey_fabric_transfer_cost_actual")
{
	echo load_html_head_contents("Knitting Cost Actual Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");

	 $condition= new condition();//PROCESS_COSTING_MAINTAIN
				
			
	$condition->po_id("in ($po_id)"); 

	$condition->init();
	$conversion= new conversion($condition);				 
	$conversion_costing_arr_process_arr=$conversion->getAmountArray_by_orderAndProcess();	
	$conversion= new conversion($condition);
	$conversion_costing_arr_process_qty=$conversion->getQtyArray_by_orderAndProcess(); 
	?>
	
		<fieldset style="width:900px; margin-left:7px">
        <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption style="background:#CCDDEE"><b>Transfer In Qty</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="110">Transfer Id</th> 
				<th width="110">Transfer Date</th>
                <th width="80">From Order</th>
                <th width="100">From Job</th>
                <th width="100">Transfer Qnty</th>
                <th width="80">Rate $</th>
				<th width="100">Amount</th>
				<th width="100">Knitting Cost $</th>
                <th>Total $</th>
            </thead>
        </table>
        <div style="width:920px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0"  cellspacing="0">  
			
				<tbody id="table_search">
            	
				<?

			


					$recvIssue_array=array(); 
					$sql_trans="select b.trans_type, b.po_breakdown_id as po_id, sum(b.quantity) as qnty,a.transaction_date,a.REQUISITION_NO,a.ORDER_ID,a.PROD_ID from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(80,82,81,83,84,13,94) and a.item_category=13 and a.transaction_type=6 and b.trans_type=6 and  b.po_breakdown_id in ($po_id) group by b.trans_type, b.po_breakdown_id,a.transaction_date,a.REQUISITION_NO,a.PROD_ID,a.ORDER_ID";
					//echo $sql_trans;die;
					$result_trans=sql_select( $sql_trans );

				


					$i=1;	$knit_cost_mkt="";$knit_qty_mkt="";$total_in_knit_cost_usd=0;$total_in_knit_qnty=0;
                foreach($result_trans as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
				
						
					$knit_cost_mkt=array_sum($conversion_costing_arr_process_arr[$row[csf('po_id')]][1]);
					$knit_qty_mkt=array_sum( $conversion_costing_arr_process_qty[$row[csf('po_id')]][1]);
					$knit_charge=$knit_cost_mkt/$knit_qty_mkt;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('PROD_ID')]; ?></p></td>						
						 <td width="110"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" ><?php echo $row[csf('transaction_date')];?></span></p></td>
						
                        <td width="80" align="center"><? echo $row[csf('ORDER_ID')]; ?></td>
                        <td width="100"><p><? echo $product_array[$row[csf('prod_id')]]; ?></p></td>
                        <td align="right" width="100"><? echo number_format($row[csf('qnty')],2); ?></td>
                        <td align="right" width="80"><? echo number_format($knit_charge,4); ?>&nbsp;</td>
						<td align="right" width="100">
                            <?
								$amount_usd=$knit_charge*$row[csf('qnty')];
                                echo number_format($amount_usd,2); 
                            ?>
                        </td>
						<td align="right" width="100">
                            <?
								$amount_usd=$grey_amt;
                                echo number_format($knit_charge,2); 
                            ?>
                        </td>
                        <td align="right">
                            <?
								$amount_usd=$grey_amt;
                                echo number_format($knit_charge*$row[csf('qnty')],2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('qnty')];
				
					$total_in_knit_cost_usd+=$knit_charge*$row[csf('qnty')];
					//	}
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
					 <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo number_format($total_in_knit_cost_usd/$total_in_knit_qnty,2); ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_in_knit_cost_usd,2); ?></td>
                </tr>
				</tbody>
                
              
			</table>
        </div>	
		<table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption style="background:#CCDDEE"><b>Transfer Out Qty</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="110">Transfer Id</th> 
				<th width="110">Transfer Date</th>
                <th width="80">From Order</th>
                <th width="100">From Job</th>
                <th width="100">Transfer Qnty</th>
                <th width="80">Rate $</th>
				<th width="100">Amount</th>
				<th width="100">Knitting Cost $</th>
                <th>Total $</th>
            </thead>
        </table>
        <div style="width:920px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0"  cellspacing="0">  
			
				<tbody id="table_search">
            	
				<?

			


					$recvIssue_array=array(); 
					$sql_trans="select b.trans_type, b.po_breakdown_id as po_id, sum(b.quantity) as qnty,a.transaction_date,a.REQUISITION_NO,a.ORDER_ID from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(80,82,81,83,84,13,94) and a.item_category=13 and a.transaction_type=5 and b.trans_type=5 and  b.po_breakdown_id in ($po_id) group by b.trans_type, b.po_breakdown_id,a.transaction_date,a.REQUISITION_NO,a.ORDER_ID";
					
					$result_trans=sql_select( $sql_trans );

				


					$i=1;					$knit_cost_mkt="";$knit_qty_mkt="";
                foreach($result_trans as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
				
						
					$knit_cost_mkt=array_sum($conversion_costing_arr_process_arr[$row[csf('po_id')]][1]);
					$knit_qty_mkt=array_sum( $conversion_costing_arr_process_qty[$row[csf('po_id')]][1]);
					$knit_charge=$knit_cost_mkt/$knit_qty_mkt;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('REQUISITION_NO')]; ?></p></td>						
						 <td width="110"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" ><?php echo $row[csf('transaction_date')];?></span></p></td>
						
                        <td width="80" align="center"><? echo $row[csf('ORDER_ID')]; ?></td>
                        <td width="100"><p><? echo $product_array[$row[csf('prod_id')]]; ?></p></td>
                        <td align="right" width="100"><? echo number_format($row[csf('qnty')],2); ?></td>
                        <td align="right" width="80"><? echo number_format($knit_charge,4); ?>&nbsp;</td>
						<td align="right" width="100">
                            <?
								$amount_usd=$grey_amt;
                                echo number_format($amount_usd,2); 
                            ?>
                        </td>
						<td align="right" width="100">
                            <?
								$amount_usd=$grey_amt;
                                echo number_format($knit_charge,2); 
                            ?>
                        </td>
                        <td align="right">
                            <?
								$amount_usd=$grey_amt;
                                echo number_format($knit_charge*$row[csf('qnty')],2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('grey_qnty')];
				
					$total_in_knit_cost_usd+=$amount_usd;
					//	}
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
					 <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo number_format($total_in_knit_cost_usd/$total_in_knit_qnty,2); ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_in_knit_cost_usd,2); ?></td>
                </tr>
				</tbody>
                
              
			</table>
        </div>	
	<script>
  	setFilterGrid("table_search",-1);
  </script>
	</fieldset>  
	<?
	exit();
}


if($action=="knit_cost_actual")
{
	echo load_html_head_contents("Knitting Cost Actual Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");

	?>
	<script>
	function generate_work_order_report(booking_no, cbo_company_name,show_comment,path,action_type) {
				
				if (action_type == 'show_trim_booking_report3')
				{
					var action_method = "action=show_trim_booking_report3";
				}
				
				report_title = "&report_title=Service Booking Sheet For Knitting";
				http.open("POST", "../../../../order/woven_order/requires/service_booking_knitting_controller.php", true);
						
				var data = action_method + report_title +
				'&txt_booking_no=' + "'" + booking_no + "'" +
				'&cbo_company_name=' + "'" + cbo_company_name + "'" +
				'&show_comment=' + "'" + show_comment + "'" +
				'&path=' + "'" + path + "'";
				//alert(data);
				
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_reponse;
			}

			function generate_fabric_report_reponse() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
					d.close();
				}
			}
	</script>
		<fieldset style="width:980px; margin-left:7px">
        <table class="rpt_table" width="875" cellpadding="0" cellspacing="0" border="1" rules="all">
		<caption style="background:#CCDDEE"><b>Knitting Cost Actaul</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="110">Bill No.</th> 
				<th width="110">Service Booking No.</th>
                <th width="80">Bill Date.</th>
                <th width="180">Fabric Description</th>
                <th width="110">Knitting Qty</th>
                <th width="80">Rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:875px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="855" cellpadding="0"  cellspacing="0">  
			
				 
				<tbody id="table_search">
            	
				<?
				/* $condition= new condition();//PROCESS_COSTING_MAINTAIN
				
				 if(str_replace("'","",$po_id) !=''){
					  $condition->po_id("in($po_id)");
				 }
				 	 $condition->init();
				
				
				  $conversion= new conversion($condition);
				 $conversion_costing_arr_process_arr=$conversion->getAmountArray_by_orderAndProcess();
				  $conversion= new conversion($condition);
				  $conversion_costing_arr_process_qty=$conversion->getQtyArray_by_orderAndProcess(); */
				  
				$prodcostDataArray="select a.lib_yarn_count_deter_id as deter_id,a.body_part_id,b.po_break_down_id as po_id,
									  avg(CASE WHEN c.cons_process=1 THEN c.charge_unit END) AS knit_charge,
									  avg(CASE WHEN c.cons_process=30 THEN c.charge_unit END) AS yarn_dye_charge,
									  avg(CASE WHEN c.cons_process=35 THEN c.charge_unit END) AS aop_charge,
									  avg(CASE WHEN c.cons_process not in(1,2,30,35) THEN c.charge_unit END) AS dye_finish_charge
									  from wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls b where a.id=c.fabric_description and a.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=c.job_no and c.status_active=1 and c.is_deleted=0  and b.po_break_down_id in($po_id) group by  a.lib_yarn_count_deter_id,a.body_part_id,b.po_break_down_id";
				$resultfab_arr = sql_select($prodcostDataArray);
				foreach($resultfab_arr as $prodRow)
				{
					//echo $prodRow[csf('knit_charge')].',';
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}
				
				$plan_details_array = return_library_array("select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where po_id in($po_id) group by dtls_id,booking_no", "dtls_id", "booking_no");
				//print_r($plan_details_array);
				$reqs_array = array();
				$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
				foreach ($reqs_sql as $row)
				 {
					//$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
					$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
				}
				unset($reqs_sql);
				$sql_wo_aop="select a.id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.po_break_down_id as po_id,
				(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty,
				(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount
				 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id in($po_id)";
				$result_aop_rate=sql_select( $sql_wo_aop );
				foreach ($result_aop_rate as $row)
				{
					if($row[csf('item_category')]==12)
					{
						$wo_qnty=$row[csf('wo_qnty')];
						$amount=$row[csf('amount')];
						$avg_wo_aop_rate=$amount/$wo_qnty;
						$aop_prod_array[$row[csf('po_id')]]['aop_rate']=$avg_wo_aop_rate;
					}
					else
					{
						$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
						$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
						
						$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
						$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
					}
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				unset($result_aop_rate);
					
				
                $i=1;  
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				$sql_grey_trans="select  a.recv_number,a.company_id,a.service_booking_no,a.receive_basis,a.booking_no,a.receive_date,b.po_breakdown_id as po_id,c.febric_description_id as deter_id,c.body_part_id,c.prod_id,c.kniting_charge,
				 (CASE WHEN b.entry_form =2 and b.trans_type in(1) and a.item_category=13  THEN  b.quantity ELSE 0 END) AS grey_qnty,a.currency_id
				
				  from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and a.item_category in(2,13)  and b.trans_type in(1) and b.po_breakdown_id in($po_id) ";
				  //group by a.recv_number,a.receive_basis,b.po_breakdown_id,a.booking_no,a.receive_date,c.febric_description_id,c.prod_id,c.body_part_id
				$result_grey=sql_select( $sql_grey_trans );
				$currency_id=1;$usd_id=2;

				foreach($result_grey as $row)
                {

					$order_arr[$row[csf('po_id')]]=$row[csf('po_id')];

				}






			//============================================knit inbound bill issue===============================================
			$knit_in_bill_sql="select a.bill_no,b.id as upd_id,  b.process_id,b.rate, b.amount, b.order_id, b.currency_id ,b.add_rate,b.delivery_qty,a.bill_date,b.item_id from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.order_id in($po_id)  order by b.challan_no ASC";
			$knit_in_bill_data=sql_select($knit_in_bill_sql);
			foreach($knit_in_bill_data as $kival){

				if($kival[csf('process_id')]==2){
					$currency_id=$kival[csf('currency_id')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]][$kival[csf('bill_no')]]['bill_no']=$kival[csf('bill_no')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]][$kival[csf('bill_no')]]['rate']=$kival[csf('rate')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]][$kival[csf('bill_no')]]['delivery_qty']+=$kival[csf('delivery_qty')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]][$kival[csf('bill_no')]]['amount']+=$kival[csf('amount')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]][$kival[csf('bill_no')]]['currency_id']=$kival[csf('currency_id')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]][$kival[csf('bill_no')]]['bill_date']=$kival[csf('bill_date')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]][$kival[csf('bill_no')]]['item_id']=$kival[csf('item_id')];
				}else{
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['rate']=$kival[csf('add_rate')];
				}

			}
			if(count($knit_in_bill_data)>0){
				foreach($knit_in_bill_data as $row){
					$amount_knit=0;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$recv_number=$order_wise_knit_bill_data[$row[csf('order_id')]][$kival[csf('bill_no')]]['bill_no'];
					$rate=$order_wise_knit_bill_data[$row[csf('order_id')]][$kival[csf('bill_no')]]['rate'];
					$amount=$order_wise_knit_bill_data[$row[csf('order_id')]][$kival[csf('bill_no')]]['amount'];
					$delivery_qty=$order_wise_knit_bill_data[$row[csf('order_id')]][$kival[csf('bill_no')]]['delivery_qty'];
					$bill_date=$order_wise_knit_bill_data[$row[csf('order_id')]][$kival[csf('bill_no')]]['bill_date'];
					$item_id=$order_wise_knit_bill_data[$row[csf('order_id')]][$kival[csf('bill_no')]]['item_id'];
					
					$usd_id=2;
					$conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
					if($currency_id==2) $amount_knit=$amount_knit;//USD
					else $amount_knit=$amount_knit/$currency_rate;
			?>
			 <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $recv_number; ?></p></td>
						
						 <td width="110"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_work_order_report('<?php echo $row[csf('service_booking_no')];?>',<?php echo $row[csf('company_id')];?>,<?php echo $show_comment;?>,'<?php echo $path;?>','<?php echo $action_type;?>')"><?php echo $row[csf('service_booking_no')];?></span></p></td>
						
                        <td width="80" align="center"><? echo change_date_format($order_wise_knit_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['bill_date']); ?></td>
                        <td width="180"><p><? echo $product_array[$item_id]; ?></p></td>
                        <td align="right" width="110"><? echo number_format($delivery_qty,2); ?></td>
                        <td align="right" width="80"><? echo number_format($rate/$currency_rate,4); ?>&nbsp;</td>
                        <td align="right">
                            <?
								$amount_knit=$rate*$delivery_qty;
                                echo number_format($amount_knit/$currency_rate,2); 
                            ?>
                        </td>
                    </tr><?
					 $i++;
					 $total_in_knit_qnty+=$row[csf('delivery_qty')];
				 
					 $total_in_knit_cost_usd+=$amount_knit/$currency_rate;
				}
			}
			unset($knit_in_bill_data);
			
			//============================================knit outbound bill issue/gross same soucre===============================================
			$knit_out_bill_sql="SELECT  a.bill_no,a.bill_date, b.id as dtls_id, b.receive_id, b.wo_num_id, b.receive_date, b.challan_no, b.order_id, b.roll_no, b.body_part_id, b.febric_description_id, b.item_id, b.receive_qty, b.rec_qty_pcs, b.uom, b.rate, b.amount, b.remarks,b.currency_id,b.process_id FROM subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b WHERE a.id=b.mst_id and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in($po_id) order by b.id asc";
			$knit_out_bill_data=sql_select($knit_out_bill_sql);
			foreach($knit_out_bill_data as $koval){
					$currency_id=$koval[csf('currency_id')];
					$order_wise_knit_out_bill_data[$koval[csf('order_id')]][$koval[csf('bill_no')]]['rate']=$koval[csf('rate')];
					$order_wise_knit_out_bill_data[$koval[csf('order_id')]][$koval[csf('bill_no')]]['receive_qty']+=$koval[csf('receive_qty')];
					$order_wise_knit_out_bill_data[$koval[csf('order_id')]][$koval[csf('bill_no')]]['amount']+=$koval[csf('amount')];
					$order_wise_knit_out_bill_data[$koval[csf('order_id')]][$koval[csf('bill_no')]]['currency_id']=$koval[csf('currency_id')];
					$order_wise_knit_out_bill_data[$koval[csf('order_id')]][$koval[csf('bill_no')]]['bill_date']=$koval[csf('bill_date')];
					$order_wise_knit_out_bill_data[$koval[csf('order_id')]][$koval[csf('bill_no')]]['bill_no']=$koval[csf('bill_no')];
					$order_wise_knit_out_bill_data[$koval[csf('order_id')]][$koval[csf('bill_no')]]['item_id']=$koval[csf('item_id')];
			}
			//echo '<pre>';print_r($order_wise_knit_out_bill_data);
			
			
			if(count($knit_out_bill_data)>0){
				foreach($knit_out_bill_data as $row){
					$amount_out_knit=0;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$recv_number=$order_wise_knit_out_bill_data[$row[csf('order_id')]][$koval[csf('bill_no')]]['bill_no'];
					$rate=$order_wise_knit_out_bill_data[$row[csf('order_id')]][$koval[csf('bill_no')]]['rate'];
					$amount=$order_wise_knit_out_bill_data[$row[csf('order_id')]][$koval[csf('bill_no')]]['amount'];
					$receive_qty=$order_wise_knit_out_bill_data[$row[csf('order_id')]][$koval[csf('bill_no')]]['receive_qty'];
					$bill_date=$order_wise_knit_out_bill_data[$row[csf('order_id')]][$koval[csf('bill_no')]]['bill_date'];
					$item_id=$order_wise_knit_out_bill_data[$row[csf('order_id')]][$koval[csf('bill_no')]]['item_id'];
					
					$usd_id=2;
					$conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
					if($currency_id==2) $amount_out_knit=$amount_out_knit;//USD
					else $amount_out_knit=$amount_out_knit/$currency_rate;
			?>
			 <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $order_wise_knit_out_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['bill_no']; ?></p></td>
						
						 <td width="110"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_work_order_report('<?php echo $row[csf('service_booking_no')];?>',<?php echo $row[csf('company_id')];?>,<?php echo $show_comment;?>,'<?php echo $path;?>','<?php echo $action_type;?>')"><?php echo $row[csf('service_booking_no')];?></span></p></td>
						
                        <td width="80" align="center"><? echo change_date_format($order_wise_knit_out_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['bill_date']); ?></td>
                        <td width="180"><p><? echo $product_array[$order_wise_knit_out_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['item_id']]; ?></p></td>
                        <td align="right" width="110"><? echo number_format($order_wise_knit_out_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['receive_qty'],2); ?></td>
                        <td align="right" width="80"><? echo number_format($order_wise_knit_out_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['rate']/$currency_rate,4); ?>&nbsp;</td>
                        <td align="right">
                            <?
							$amount_out_knit=$order_wise_knit_out_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['receive_qty']*$order_wise_knit_out_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['rate'];
                                echo number_format($amount_out_knit/$currency_rate,2); 
                            ?>
                        </td>
                    </tr><?
					 $i++;
					 $total_out_knit_qnty+=$order_wise_knit_out_bill_data[$row[csf('order_id')]][$row[csf('bill_no')]]['receive_qty'];
				 
					 $total_out_knit_cost_usd+=$amount_out_knit/$currency_rate;
				}
			}

			unset($knit_out_bill_data);

                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
					 <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_in_knit_qnty+$total_out_knit_qnty,2); ?></td>
                    <td align="right"><? //echo number_format($total_in_knit_cost_usd/$total_in_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_in_knit_cost_usd+$total_out_knit_cost_usd,2); ?></td>
                </tr>
				</tbody>
                
              
			</table>
        </div>	
	<script>
  	setFilterGrid("table_search",-1);
  </script>
	</fieldset>  
	<?
	exit();
}
 if($action=="dye_finish_cost_actual2")
{
	echo load_html_head_contents("Dyeing Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id in(13,2)", "id", "product_name_details");
	$batch_no_array=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1", "id", "batch_no");
//	echo $po_id."==A";die;
	?>
	<script>
	function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			$("#list_view_data tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		$("#list_view_data tr:first").show();
	}	
    </script>
	<fieldset style="width:870px; margin-left:7px">
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container">
        <table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" >
        <caption style="background:#CCDDEE"> <b>Dye and Finish Details</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="130">System No.</th>
                <th width="80">Recv. Date</th>
                <th width="100">Challan/Batch No</th>
                <th width="80">PO No</th>
                <th width="200">Fabric Description</th>
                <th width="80">Qty</th>
                <th width="80">Rate</th>
                <th>Amount</th>
            </thead>
        </table>
        <div style="width:870px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" id="list_view_data">  
            	 
				<?
                $i=1; $avg_rate=76;
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
			$po_sql="	select a.id as po_id,a.po_number from wo_po_break_down a where  a.status_active=1 and a.is_deleted=0 and a.id in($po_id)";
			$po_sql_data=sql_select($po_sql);
			foreach($po_sql_data as $row)
			{
				$order_arr[$row[csf('po_id')]]['po_no']=$row[csf('po_number')];
			}
			

		//============================================knit inbound bill issue===============================================
		 	 $knit_in_bill_sql="	select a.bill_no,b.id as upd_id,b.item_id as prod_id,b.challan_no, b.process_id,b.rate, b.amount, b.order_id, b.currency_id ,b.add_rate,b.delivery_qty,a.bill_date from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and order_id in($po_id)  and b.process_id in (4) and b.add_process_name!='35' order by a.bill_no ASC";
		
			// echo  $knit_in_bill_sql;die;
			$knit_in_bill_data=sql_select($knit_in_bill_sql);
			foreach($knit_in_bill_data as $kival){
				if($kival[csf('process_id')]==2){
					$order_wise_knit_bill_data[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
					
					$order_wise_knit_bill_data[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
				}else{
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
				}
				//$order_wise_knit_bill_currencyArr[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
				$dye_finish_charge=$kival[csf('rate')];
				
				$currency_id=$kival[csf('currency_id')];//$order_wise_dye_bill_rate[$row[csf('po_id')]]['currency_id'];
				$bill_date=$kival[csf('bill_date')];
				//echo $currency_id.'D';

				$usd_id=2;
				if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
				$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
				if($currency_id==2) $dye_finish_charge=$dye_finish_charge;//USD
				else $dye_finish_charge=$dye_finish_charge/$currency_rate;
				
				$used_amount=$koval[csf('delivery_qty')]*$dye_finish_charge;
				
				
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['rate']=$kival[csf('amount')]/$kival[csf('delivery_qty')];
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['used_amount']+=$used_amount;
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['bill_no']=$kival[csf('bill_no')];
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['prod_id']=$kival[csf('prod_id')];
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['bill_date']=$kival[csf('bill_date')];
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['currency_id']=$kival[csf('currency_id')];
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['challan_no']=$kival[csf('challan_no')];
				$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['process_id']=$kival[csf('process_id')];
				//$sub_in_dye_bill_Arr[$kival[csf('bill_no')]][$kival[csf('order_id')]][$kival[csf('prod_id')]]['currency_id']=$kival[csf('currency_id')];
				
			}
				
			//============================================knit outbound bill issue/gross same soucre===============================================
			    $knit_out_bill_sql="SELECT a.bill_no,b.id as dtls_id,b.batch_id, b.receive_id,b.wo_num_id, b.receive_date, b.challan_no, b.order_id, b.roll_no, b.body_part_id, b.febric_description_id, b.item_id as prod_id, b.receive_qty, b.rec_qty_pcs, b.uom, b.rate, b.amount, b.remarks,b.currency_id,b.process_id FROM subcon_outbound_bill_dtls b,subcon_outbound_bill_mst a WHERE  a.id=b.mst_id and b.process_id in (4) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.order_id in($po_id) and (b.sub_process_id!=35  or b.sub_process_id is null)  order by a.bill_no asc";
			$knit_out_bill_data=sql_select($knit_out_bill_sql);
			foreach($knit_out_bill_data as $koval){
					if($koval[csf('process_id')]==2){
						$order_wise_knit_bill_data[$koval[csf('order_id')]]['rate']=$koval[csf('rate')];
						$order_wise_dye_bill_rateArr[$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('rate')];
						//$order_wise_knit_bill_data[$koval[csf('order_id')]]['bill_no']=$koval[csf('bill_no')];
					}else{
						$order_wise_dye_bill_rateArr[$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('rate')];
					}
					$order_wise_knit_bill_currencyArr[$koval[csf('order_id')]]['currency_id']=$koval[csf('currency_id')];
					//$order_wise_dye_bill_rateArr[$koval[csf('order_id')]]['bill_no']=$koval[csf('bill_no')];
					$dye_finish_charge=$koval[csf('rate')];
				
					$currency_id=$koval[csf('currency_id')];//$order_wise_dye_bill_rate[$row[csf('po_id')]]['currency_id'];
					$bill_date=$koval[csf('bill_date')];
					//echo $currency_id.'D';
	
					$usd_id=2;
					if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
					if($currency_id==2) $dye_finish_charge=$dye_finish_charge;//USD
					else $dye_finish_charge=$dye_finish_charge/$currency_rate;
					$dye_finish_charge=number_format($dye_finish_charge,4,'.','');
					//echo $koval[csf('receive_qty')].'='.$dye_finish_charge.'<br>';
					$used_amount=$koval[csf('receive_qty')]*$dye_finish_charge;
					
					$sub_dye_bill_Arr[$koval[csf('bill_no')]][$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('amount')]/$koval[csf('receive_qty')];
					$sub_dye_bill_Arr[$koval[csf('bill_no')]][$koval[csf('order_id')]][$koval[csf('prod_id')]]['receive_qty']+=$koval[csf('receive_qty')];
					$sub_dye_bill_Arr[$koval[csf('bill_no')]][$koval[csf('order_id')]][$koval[csf('prod_id')]]['used_amount']+=$used_amount;
					$sub_dye_bill_Arr[$koval[csf('bill_no')]][$koval[csf('order_id')]][$koval[csf('prod_id')]]['bill_no']=$koval[csf('bill_no')];
					$sub_dye_bill_Arr[$koval[csf('bill_no')]][$koval[csf('order_id')]][$koval[csf('prod_id')]]['receive_date']=$koval[csf('receive_date')];
					$sub_dye_bill_Arr[$koval[csf('bill_no')]][$koval[csf('order_id')]][$koval[csf('prod_id')]]['currency_id']=$koval[csf('currency_id')];
					$sub_dye_bill_Arr[$koval[csf('bill_no')]][$koval[csf('order_id')]][$koval[csf('prod_id')]]['batch_id']=$koval[csf('batch_id')];
					$sub_dye_bill_Arr[$koval[csf('bill_no')]][$koval[csf('order_id')]][$koval[csf('prod_id')]]['fab_desc']=$product_array[$row[csf('prod_id')]];
			}


                foreach($sub_in_dye_bill_Arr as $bill_no_id=>$bill_in_data)
                {
			    foreach($bill_in_data as $po_id=>$po_in_data)
                {
			    foreach($po_in_data as $prod_id=>$row)
                {
					 
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
				//$dye_finish_charge=$order_wise_dye_bill_rate[$row[csf('po_id')]]['amount']/$order_wise_dye_bill_rate[$row[csf('po_id')]]['delivery_qty'];

				//$dye_finish_charge=$order_wise_dye_bill_rate[$po_id]['rate'];
				$dye_finish_charge=$row[('rate')];
				
				$currency_id=$row[('currency_id')];//$order_wise_dye_bill_rate[$row[csf('po_id')]]['currency_id'];
				$bill_date=$row[('bill_date')];
				//echo $currency_id.'D';

				$usd_id=2;
				if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
				$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
				if($currency_id==2) $dye_finish_charge=$dye_finish_charge;//USD
				else $dye_finish_charge=$dye_finish_charge/$currency_rate;
				$used_amount=$row['used_amount'];//$row['delivery_qty']*$dye_finish_charge;used_amount
				//$bill_no=$order_wise_knit_bill_data[$row[csf('po_id')]]['bill_no'];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $bill_no_id; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[('receive_date')]); ?></td>
                        <td width="100"><p><? echo $row['challan_no']; ?></p></td>
                        <td width="80"><p><? echo $order_arr[$po_id]['po_no']; ?></p></td>
                        <td width="200"><p><? echo $product_array[$prod_id]; ?></p></td>
                        <td align="right" width="80"><? echo number_format($row['delivery_qty'],2); ?></td>
                        <td align="right" width="80" title="Bill No-<? echo $bill_no;?>"><? echo number_format($dye_finish_charge,2); ?>&nbsp;</td>
                        <td align="right" width="">
						<? 
							echo number_format($used_amount,2); 
						?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row['delivery_qty'];
					
					$total_in_knit_cost_usd+=$used_amount;
						}
					}
                }
				//$finProd_grey_used_sql=sql_select("SELECT a.company_id,a.receive_purpose,a.recv_number,a.receive_date,a.location_id,a.booking_no as non_order_booking, a.booking_id,a.receive_basis,b.prod_id, b.batch_id,b.fabric_description_id as deter_id, b.receive_qnty, b.reject_qty,b.bin,e.po_breakdown_id as po_id,e.quantity, b.batch_id,b.order_id,e.grey_used_qty,b.job_no,b.booking_no,b.booking_id as booking_no_id , f.po_number from inv_receive_master a, inv_transaction c,pro_finish_fabric_rcv_dtls b,order_wise_pro_details e, wo_po_break_down f  where a.id=c.mst_id and c.id=b.trans_id  and b.id=e.dtls_id and e.trans_id=c.id and f.id=e.po_breakdown_id  and a.item_category=2 and a.entry_form=37 and e.entry_form=37 and a.status_active=1 and e.status_active=1 and c.status_active=1 and b.status_active=1 and e.po_breakdown_id in($po_id) ");
				

			 foreach($sub_dye_bill_Arr as $bill_no=>$bill_data)
              {
				foreach($bill_data as $po_id=>$po_data)
               	{
				 foreach($po_data as $prod_id=>$row)
                 {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
					$currency_id=$row[('currency_id')];
					$bill_date=$row[('receive_date')];
					$dye_finish_charge=$row[('rate')];
					// echo $currency_id.'DSD';
					/*$usd_id=2;
					if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
					$currency_rate=set_conversion_rate($usd_id,$conversion_date,1 );
					if($currency_id==2) $dye_finish_charge=$dye_finish_charge;//USD
					else $dye_finish_charge=$dye_finish_charge/$currency_rate;*/
						//echo $bill_date.'='.$dye_finish_charge.'='.$currency_rate.'D'.'<br>';
					//$used_amount=$row['receive_qty']*$dye_finish_charge;
					$used_amount=$row['used_amount'];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $bill_no; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($bill_date); ?></td>
                         <td width="100"><p><? echo $batch_no_array[$row[('batch_id')]]; ?></p></td>
                         <td width="80"><p><? echo $order_arr[$po_id]['po_no']; ?></p></td>
                        <td width="200" title="<? echo $prod_id; ?>"><p><? echo $product_array[$prod_id]; ?></p></td>
                        <td align="right" width="80"><? echo number_format($row['receive_qty'],2); ?></td>
                        <td align="right" width="80"><? echo number_format($dye_finish_charge,2); ?>&nbsp;</td>
                        <td align="right" width="" title="Ex Rate=<? echo $currency_rate;?>">
						<? 
							echo number_format($used_amount,2); 
						?>
                        </td>
                    </tr>
                	<?
                	$i++;
					$total_in_knit_qnty+=$row['receive_qty'];
					
					$total_in_knit_cost_usd+=$used_amount;
				  }
				 }
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? //echo number_format($total_in_knit_qnty/$total_in_knit_cost_usd,2); ?>&nbsp;</td>
                    <td  align="right"> <? echo number_format($total_in_knit_cost_usd,2); ?>&nbsp;</td>
                </tr>
			</table>
        </div>	
        </div>
	</fieldset> 
    <script>
    	setFilterGrid("list_view_data",-1); 
	</script>
	<?
	exit();
}
if($action=="dye_finish_cost_actual")
{
	echo load_html_head_contents("Dyeing Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
	$batch_no_array=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1", "id", "batch_no");
	?>
	<script>
	function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			$("#list_view_data tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		$("#list_view_data tr:first").show();
	}	
    </script>
	<fieldset style="width:870px; margin-left:7px">
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container">
        <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" >
        <caption style="background:#CCDDEE"> <b>Dye and Finish Details</b></caption>
            <thead>
                <th width="40">SL</th>
                <th width="130">System No.</th>
                <th width="80">Recv. Date</th>
                <th width="100">Barcode/ Batch No</th>
                <th width="200">Fabric Description</th>
                <th width="100">Qty</th>
                <th width="80">Rate</th>
                <th>Amount</th>
            </thead>
        </table>
        <div style="width:850px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" id="list_view_data">  
            	 
				<?
                $i=1; $avg_rate=76;
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				
				 $prodcostDataArray="select a.body_part_id,a.lib_yarn_count_deter_id as deter_id,a.body_part_id,b.po_break_down_id as po_id,
									  avg(CASE WHEN c.cons_process=1 THEN c.charge_unit END) AS knit_charge,
									  avg(CASE WHEN c.cons_process=30 THEN c.charge_unit END) AS yarn_dye_charge,
									  avg(CASE WHEN c.cons_process=35 THEN c.charge_unit END) AS aop_charge,
									  avg(CASE WHEN c.cons_process not in(1,2,30,35,134) THEN c.charge_unit END) AS dye_finish_charge
									  from wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls b where a.id=c.fabric_description and a.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=c.job_no and c.status_active=1 and c.is_deleted=0  and b.po_break_down_id in($po_id) group by  a.lib_yarn_count_deter_id,b.po_break_down_id,a.body_part_id";
				$resultfab_arr = sql_select($prodcostDataArray);
				foreach($resultfab_arr as $prodRow)
				{
					//echo $prodRow[csf('knit_charge')].',';
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray2[$prodRow[csf('po_id')]][$prodRow[csf('deter_id')]][$prodRow[csf('body_part_id')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}	
			

			//============================================knit inbound bill issue===============================================
		 	$knit_in_bill_sql="	select b.id as upd_id, b.process_id,b.rate, b.amount, b.order_id, b.currency_id ,b.add_rate,b.delivery_qty,a.bill_date from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and order_id in($po_id)  order by b.challan_no ASC";
		
			//echo  $knit_in_bill_sql;
			$knit_in_bill_data=sql_select($knit_in_bill_sql);
			foreach($knit_in_bill_data as $kival){
				if($kival[csf('process_id')]==2){
					$order_wise_knit_bill_data[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
					$order_wise_knit_bill_data[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
				}else{
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['rate']=$kival[csf('rate')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['delivery_qty']+=$kival[csf('delivery_qty')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['amount']+=$kival[csf('amount')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
					$order_wise_dye_bill_rate[$kival[csf('order_id')]]['bill_date']=$kival[csf('bill_date')];
				}
				$order_wise_knit_bill_currencyArr[$kival[csf('order_id')]]['currency_id']=$kival[csf('currency_id')];
			}
				
			//============================================knit outbound bill issue/gross same soucre===============================================
			   $knit_out_bill_sql="SELECT id as dtls_id, receive_id,wo_num_id, receive_date, challan_no, order_id, roll_no, body_part_id, febric_description_id, item_id as prod_id, receive_qty, rec_qty_pcs, uom, rate, amount, remarks,currency_id,process_id FROM subcon_outbound_bill_dtls WHERE  process_id in (2,4) and status_active=1 and is_deleted=0 and order_id in($po_id)  order by id asc";
			$knit_out_bill_data=sql_select($knit_out_bill_sql);
			foreach($knit_out_bill_data as $koval){
					if($koval[csf('process_id')]==2){
						$order_wise_knit_bill_data[$koval[csf('order_id')]]['rate']=$koval[csf('rate')];
						$order_wise_dye_bill_rateArr[$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('rate')];
					}else{
						$order_wise_dye_bill_rateArr[$koval[csf('order_id')]][$koval[csf('prod_id')]]['rate']=$koval[csf('rate')];
					}
					$order_wise_knit_bill_currencyArr[$koval[csf('order_id')]]['currency_id']=$koval[csf('currency_id')];
			}


                foreach($grey_used_data as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
				//$dye_finish_charge=$order_wise_dye_bill_rate[$row[csf('po_id')]]['amount']/$order_wise_dye_bill_rate[$row[csf('po_id')]]['delivery_qty'];

				$dye_finish_charge=$order_wise_dye_bill_rate[$row[csf('po_id')]]['rate'];
				
				$currency_id=$order_wise_knit_bill_currencyArr[$row[csf('po_id')]]['currency_id'];//$order_wise_dye_bill_rate[$row[csf('po_id')]]['currency_id'];
				$bill_date=$order_wise_dye_bill_rate[$row[csf('po_id')]]['bill_date'];
				//echo $currency_id.'D';
				

				$usd_id=2;
				if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				if($currency_id==2) $dye_finish_charge=$dye_finish_charge;//USD
				else $dye_finish_charge=$dye_finish_charge/$currency_rate;


				$used_amount=$row[csf('qnty')]*$dye_finish_charge;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center">F<? echo change_date_format($row[csf('receive_date')]); ?></td>
                         <td width="100"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                        <td width="200"><p><? echo $product_array[$row[csf('prod_id')]]; ?></p></td>
                        <td align="right" width="100"><? echo number_format($row[csf('qnty')],2); ?></td>
                        <td align="right" width="80"><? echo number_format($dye_finish_charge,2); ?>&nbsp;</td>
                        <td align="right" width="">
						<? 
							echo number_format($used_amount,2); 
						?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('qnty')];
					
					$total_in_knit_cost_usd+=$used_amount;
                }
				$finProd_grey_used_sql=sql_select("SELECT a.company_id,a.receive_purpose,a.recv_number,a.receive_date,a.location_id,a.booking_no as non_order_booking, a.booking_id,a.receive_basis,b.prod_id, b.batch_id,b.fabric_description_id as deter_id, b.receive_qnty, b.reject_qty,b.bin,e.po_breakdown_id as po_id,e.quantity, b.batch_id,b.order_id,e.grey_used_qty,b.job_no,b.booking_no,b.booking_id as booking_no_id, f.po_number from inv_receive_master a, inv_transaction c,pro_finish_fabric_rcv_dtls b,order_wise_pro_details e, wo_po_break_down f  where a.id=c.mst_id and c.id=b.trans_id  and b.id=e.dtls_id and e.trans_id=c.id and f.id=e.po_breakdown_id and a.item_category=2 and a.entry_form=37 and e.entry_form=37 and a.status_active=1 and e.status_active=1 and c.status_active=1 and b.status_active=1 and e.po_breakdown_id in($po_id) ");
				

				foreach($fin_fab_rec_data as $fval){
					//$order_wise_fin_fab_rec[$fval[csf('order_id')]]['used_qty']=$fval[csf('grey_used_qty')];
				}
				

				foreach($finProd_grey_used_sql as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
			

				//$dye_finish_charge=$order_wise_dye_bill_rate[$row[csf('po_id')]]['amount']/$order_wise_dye_bill_rate[$row[csf('po_id')]]['delivery_qty'];
				$dye_finish_charge=$order_wise_dye_bill_rateArr[$row[csf('po_id')]][$row[csf('prod_id')]]['rate'];
				

				$currency_id=$order_wise_knit_bill_currencyArr[$row[csf('po_id')]]['currency_id'];;
				$bill_date=$order_wise_dye_bill_rate[$row[csf('po_id')]]['bill_date'];
				// echo $currency_id.'DSD';

				$usd_id=2;
				if($db_type==0) $conversion_date=change_date_format($bill_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($bill_date, "d-M-y", "-",1);
				$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				if($currency_id==2) $dye_finish_charge=$dye_finish_charge;//USD
				else $dye_finish_charge=$dye_finish_charge/$currency_rate;
			//	echo $bill_date.'='.$dye_finish_charge.'='.$currency_rate.'D'.'<br>';
					
				$used_amount=$row[csf('grey_used_qty')]*$dye_finish_charge;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                         <td width="100"><p><? echo $batch_no_array[$row[csf('batch_id')]]; ?></p></td>
                         <td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="200"><p><? echo $product_array[$row[csf('prod_id')]]; ?></p></td>
                        <td align="right" width="100"><? echo number_format($row[csf('grey_used_qty')],2); ?></td>
                        <td align="right" width="80"><? echo number_format($dye_finish_charge,2); ?>&nbsp;</td>
                        <td align="right" width="" title="Ex Rate=<? echo $currency_rate;?>">
						<? 
							echo number_format($used_amount,2); 
						?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('grey_used_qty')];
					
					$total_in_knit_cost_usd+=$used_amount;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? //echo number_format($total_in_knit_qnty/$total_in_knit_cost_usd,2); ?>&nbsp;</td>
                    <td  align="right"> <? echo number_format($total_in_knit_cost_usd,2); ?>&nbsp;</td>
                </tr>
			</table>
        </div>	
        </div>
	</fieldset> 
    <script>
    	setFilterGrid("list_view_data",-1); 
	</script>
	<?
	exit();
}



if($action=="fabric_purchase_cost_actual")
{
	echo load_html_head_contents("Fabric Purchase Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$ex_rate=76;
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:860px; margin-left:7px">
		<div id="report_container">
        	<u><b>Knit Fabric Purchase</b></u>
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Receive Id</th>
                    <th width="80">Receive Date</th>
                    <th width="280">Fabric Description</th>
                    <th width="110">Receive Qty.</th>
                    <th width="110">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
                $i=1; $total_recv_qnty=0; $total_recv_cost=0;
				$sql="select a.id, a.recv_number, a.receive_date, sum(b.quantity) as recv_qnty, sum(d.cons_rate*b.quantity) as amnt, c.id, c.product_name_details from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=1 and d.item_category=2 and c.item_category_id=2 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=37 and a.entry_form=37 and b.po_breakdown_id=$po_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_basis!=9 group by a.id, c.id, a.recv_number, a.receive_date, c.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
                    $total_recv_qnty+=$row[csf('recv_qnty')];
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="110">
							<? 
								echo number_format($row[csf('recv_qnty')],2); 
                            ?>
                        </td>
                        <td align="right" width="110">
							<? 
								$avg_rate=($row[csf('amnt')]/$row[csf('recv_qnty')])/$ex_rate;
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$cost=$row[csf('recv_qnty')]*$avg_rate;
								//$cost=$row[csf('amnt')]/$ex_rate;
								$total_recv_cost+=$cost;
								echo number_format($cost,2); 
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_recv_qnty,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_recv_cost,2); ?></th>
                </tfoot>
            </table>
            <br>
            <u><b>Woven Fabric Purchase</b></u>
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Receive Id</th>
                    <th width="80">Receive Date</th>
                    <th width="280">Fabric Description</th>
                    <th width="110">Receive Qty.</th>
                    <th width="110">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
                $i=1; $total_recv_qnty_w=0; $total_recv_cost_w=0;
				$sql="select a.id, a.recv_number, a.receive_date, sum(b.quantity) as recv_qnty, sum(d.cons_rate*b.quantity) as amnt, c.id, c.product_name_details from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=1 and d.item_category=3 and c.item_category_id=3 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=17 and a.entry_form=17 and b.po_breakdown_id=$po_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.id, a.recv_number, a.receive_date, c.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
                    $total_recv_qnty_w+=$row[csf('recv_qnty')];
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="110">
							<? 
								echo number_format($row[csf('recv_qnty')],2); 
                            ?>
                        </td>
                        <td align="right" width="110">
							<? 
								$avg_rate=($row[csf('amnt')]/$row[csf('recv_qnty')])/$ex_rate;
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$cost=$row[csf('recv_qnty')]*$avg_rate;
								//$cost=$row[csf('amnt')]/$ex_rate;
								$total_recv_cost_w+=$cost;
								echo number_format($cost,2); 
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_recv_qnty_w,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_recv_cost_w,2); ?></th>
                </tfoot>
            </table>
            <table class="tbl_bottom" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<tr>
                	<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="280">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">Grand Total</td>
                    <td align="right"><? echo number_format($total_recv_cost+$total_recv_cost_w,2); ?></td>
                </tr>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="fabric_purchase_cost")
{
	echo load_html_head_contents("Fabric Purchase Cost Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$usd_id=2;//===============Dollar========
	$exchange_rate=76;
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$sql_fin_purchase="select b.po_breakdown_id, a.transaction_date, sum(b.quantity) as qty, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) group by b.po_breakdown_id,  a.transaction_date";
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		$transaction_date=$finRow[csf('transaction_date')];
		if($db_type==0)
		{
			$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
		}
		else
		{
			$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
		}
		$currency_ratelib=set_conversion_rate($usd_id,$conversion_date );
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['qty']=$finRow[csf('qty')];
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']=$finRow[csf('finish_purchase_amnt')]/$currency_ratelib;
	}
	
	$sql_fin_purchase="SELECT c.currency_id,c.exchange_rate, b.po_breakdown_id, sum(b.quantity) as qty, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b, inv_receive_master c where a.id=b.trans_id and c.id=a.mst_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) group by b.po_breakdown_id, c.currency_id,c.exchange_rate";
	//echo $sql_fin_purchase; die;
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		$exchange_rate=$finRow[csf('exchange_rate')];
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['qty']+=$finRow[csf('qty')];
		if($finRow[csf('currency_id')]!=1){
			$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']+=$finRow[csf('woven_purchase_amnt')]/$exchange_rate;
		}
		else{
			$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']+=$finRow[csf('woven_purchase_amnt')];
		}
		
	}
?>
    <div>
        <fieldset style="width:710px; margin-left:8px">
        	<table class="rpt_table" width="680" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="100">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="110">Order Qty.</th>
                    <th width="110">Received</th>
                    <th>Cost ($)</th>
                </thead>
            </table>	
            <div style="width:700px; max-height:310px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="680" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						

					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_recv_qty=0; $tot_cost=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						
						$recv_qty=$finish_purchase_arr[$row[csf('id')]]['qty'];
						$recv_cost=$finish_purchase_arr[$row[csf('id')]]['amnt'];
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="100" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="110" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="110" align="right"><? echo number_format($recv_qty,2,'.',''); ?></td>
                            <td align="right" ><? echo number_format($recv_cost,2,'.',''); ?></td>
						</tr>
					<?
						$i++;
						$tot_recv_qty+=$recv_qty; 
						$tot_cost+=$recv_cost; 
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_cost,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="aop_cost_actual_old")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

 ?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?




                $i=1; $total_aop_cost=0; $avg_rate=76;
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.item_category=12 and a.booking_type in(3,6) and b.process=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
	<?
	exit();
}
if($action=="aop_cost_actual2")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);


	 /* $sql_wo_aop="select a.id,a.booking_date,a.currency_id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.process,b.po_break_down_id as po_id,
			(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty,
			(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount
			 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and  b.po_break_down_id=$po_id";
			// echo  $sql_wo_aop;
			$result_aop_rate=sql_select( $sql_wo_aop );
			foreach ($result_aop_rate as $row)
			{
				if($row[csf('item_category')]==12)
				{
					$process=$row[csf('process')];
					if($row[csf('amount')]>0 && $process==35)
					{
					$wo_qnty=$row[csf('wo_qnty')];
					$amount=$row[csf('amount')];
						
					$avg_wo_aop_rate=$amount/$wo_qnty;
					$aop_prod_array[$row[csf('po_id')]]['aop_rate']=$avg_wo_aop_rate;
					$aop_prod_array[$row[csf('po_id')]]['amount']+=$amount;
					$aop_prod_array[$row[csf('po_id')]]['wo_qnty']+=$wo_qnty;
					$aop_prod_array[$row[csf('po_id')]]['currency_id']=$row[csf('currency_id')];
					$aop_prod_array[$row[csf('po_id')]]['booking_date']=$row[csf('booking_date')];
					}
				}
				else
				{
					$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
					$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
					$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
					$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
				}
			}
			unset($result_aop_rate);*/
		 $sql_aop="select a.company_id, b.batch_issue_qty as issue_qty,b.currency_id,a.receive_date,b.prod_id,b.order_id as po_id,b.grey_used,b.rate,a.dyeing_source,a.dyeing_company,a.recv_number from pro_grey_batch_dtls b,inv_receive_mas_batchroll a where a.id=b.mst_id  and a.entry_form=92  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.process_id=35 and  b.order_id=$po_id";

			// echo "<pre>";
			// print_r($aop_prod_array);
			$result_aop=sql_select( $sql_aop );
			$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
			$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		
 ?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">Fabric Service  Received ID</th>
                <th width="80">Supplier</th>
                <th width="80">Rec Date</th>
                <th width="120">Grey Qty</th>
                <th width="120">WO Rate</th>
                <th>Amount</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?


                $i=1; $total_aop_cost=0; $avg_rate=76;
               $usd_id=2;
                foreach($result_aop as $row)
                {


					//$aop_rate=$aop_prod_array[$row[csf('po_id')]]['aop_rate'];
					//$amount=$aop_prod_array[$row[csf('po_id')]]['amount'];
					//$wo_qty=$aop_prod_array[$row[csf('po_id')]]['wo_qnty'];
					$dyeing_source=$row[csf('dyeing_source')];
					if($dyeing_source==3)
					{
						$supp_com=$supplier_array[$row[csf('dyeing_company')]];
					}
					else
					{
						$supp_com=$company_short_arr[$row[csf('dyeing_company')]];
					}
					$aop_rate=$row[csf('rate')];
					$currency_id=$row[csf('currency_id')];
					$booking_date=$row[csf('receive_date')];
					//$booking_date=$aop_prod_array[$row[csf('po_id')]]['booking_date'];
					//$currency_id=$aop_prod_array[$row[csf('po_id')]]['currency_id'];
					$booking_date=$booking_date;
					if($db_type==0) $conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
					
					$exchange_rate=set_conversion_rate($usd_id,$conversion_date,1 );
					if($currency_id==1) $avg_rate=$aop_rate/$exchange_rate;//TK
					else $avg_rate=$aop_rate;
						//echo $conversion_date.'='.$currency_id.'='.$aop_rate.',';
					//	echo $currency_id.'='.$aop_rate.'<br>';
					$aop_amt=$row[csf('grey_used')]*$avg_rate;
					$aop_prod_array[$row[csf('po_id')]]['aop_amt']+=$aop_amt;


                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                         <td width="80"><p><? echo $supp_com; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($booking_date); ?></td>
                       
                        <td align="right" width="120">
                            <? 
                                echo number_format($row[csf('issue_qty')],2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo number_format($avg_rate,2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								
                                echo number_format($aop_amt,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$aop_amt;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
	<?
	exit();
}
if($action=="aop_cost_actual")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);


	$sql_wo_aop="select a.id,a.booking_date,a.currency_id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.po_break_down_id as po_id,
			(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty,
			(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount
			 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and  b.po_break_down_id=$po_id";
			// echo  $sql_wo_aop;
			$result_aop_rate=sql_select( $sql_wo_aop );
			foreach ($result_aop_rate as $row)
			{
				if($row[csf('item_category')]==12)
				{
					if($row[csf('amount')]>0)
					{
					$wo_qnty=$row[csf('wo_qnty')];
					$amount=$row[csf('amount')];
					$avg_wo_aop_rate=$amount/$wo_qnty;
					$aop_prod_array[$row[csf('po_id')]]['aop_rate']=$avg_wo_aop_rate;
					$aop_prod_array[$row[csf('po_id')]]['currency_id']=$row[csf('currency_id')];
					$aop_prod_array[$row[csf('po_id')]]['booking_date']=$row[csf('booking_date')];
					}
				}
				else
				{
					$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
					$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
					$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
					$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
				}
			}
			unset($result_aop_rate);









		$sql_aop="select a.company_id, b.batch_issue_qty as issue_qty,b.prod_id,b.order_id as po_id,b.grey_used,b.rate,a.recv_number from pro_grey_batch_dtls b,inv_receive_mas_batchroll a where a.id=b.mst_id  and a.entry_form=92  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.process_id=35 and  b.order_id=$po_id";

			// echo "<pre>";
			// print_r($aop_prod_array);
			$result_aop=sql_select( $sql_aop );
		
 ?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">Fabric Service  Received ID</th>
                <th width="80">Supplier</th>
                <th width="80">Rec Date</th>
                <th width="120">Grey Qty</th>
                <th width="120">WO Rate</th>
                <th>Amount</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?


                $i=1; $total_aop_cost=0; $avg_rate=76;
               $usd_id=2;
                foreach($result_aop as $row)
                {


					$aop_rate=$aop_prod_array[$row[csf('po_id')]]['aop_rate'];
					$booking_date=$aop_prod_array[$row[csf('po_id')]]['booking_date'];
					$currency_id=$aop_prod_array[$row[csf('po_id')]]['currency_id'];
					$booking_date=$booking_date;
					if($db_type==0) $conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
					
					$exchange_rate=set_conversion_rate($usd_id,$conversion_date );
					if($currency_id==1) $avg_rate=$aop_rate/$exchange_rate;//TK
					else $avg_rate=$aop_rate;
						//echo $conversion_date.'='.$currency_id.'='.$aop_rate.',';
					$aop_amt=$row[csf('grey_used')]*$avg_rate;
					$aop_prod_array[$row[csf('po_id')]]['aop_amt']+=$aop_amt;


                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $booking_date; ?></p></td>
                        <td align="right" width="120">
                            <? 
                                echo number_format($row[csf('issue_qty')],2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo number_format($avg_rate,2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								
                                echo number_format($aop_amt,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$aop_amt;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
	<?
	exit();
}
if($action=="trims_cost_actual_bk")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_trims_cost=0; $avg_rate=76;
                $wo_sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";//c.booking_no,a.transaction_date,c.exchange_rate,c.currency_id
				$result_wo=sql_select( $wo_sql );
				 foreach($result_wo as $row)
                {
					$wo_date_arr[$row[csf('booking_no')]]=$row[csf('booking_date')];
				}
				 $sql_gen_trims_inv="select a.booking_no,b.transaction_date,(b.cons_amount) as amount,b.order_id as po_id
				  from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(21) and a.booking_no is not null   and b.transaction_type in(2) and b.order_id in($po_id)";
				  
				  $result_trims_gen=sql_select( $sql_gen_trims_inv );
				  
			      $sql_trims_inv="select c.booking_no, a.transaction_date, c.exchange_rate, c.currency_id, a.transaction_date, a.cons_amount as amount, b.po_breakdown_id as po_id, (b.quantity) as qnty,b.order_rate 
				  from inv_receive_master c,inv_transaction a, order_wise_pro_details b where  c.id=a.mst_id and a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.entry_form in(24) and a.item_category=4 and a.transaction_type in(1) and b.trans_type in(1) and b.po_breakdown_id in($po_id) ";
				  
				$result_trims=sql_select( $sql_trims_inv );
              $usd_id=2;
                foreach($result_trims as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$transaction_date=$row[csf('transaction_date')];
					$currency_id=$row[csf('currency_id')];
					if($db_type==0)
					{
						$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					}
					$currency_rate=set_conversion_rate($currency_id,$conversion_date );
					
					$amnt_tk=$row[csf('amount')];
					// $amount=$row[csf('amount')]/$currency_rate;
					$amount=$row[csf('qnty')]*$row[csf('order_rate')];
					//echo $row[csf('amount')].'=='.$currency_rate.'<br>';
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40" title="Receive"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($wo_date_arr[$row[csf('booking_no')]]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120"><? echo number_format($amnt_tk,2); ?></td>
                        <td align="right" width="120"><? echo number_format($currency_rate,2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($amount,2); ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_trims_cost+=$amount;
                }
				
				foreach($result_trims_gen as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$transaction_date=$row[csf('transaction_date')];
					if($db_type==0)
					{
						$conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
					}
					$currency_rate=set_conversion_rate($usd_id,$conversion_date );
					
					$amnt_tk=$row[csf('amount')];
					$amount=$row[csf('amount')]/$currency_rate;
					
					//echo $row[csf('amount')].'=='.$currency_rate.'<br>';
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40" title="Issue"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($wo_date_arr[$row[csf('booking_no')]]); ?></td>
                        <td width="80"><p><? echo $currency[$usd_id]; ?></p></td>
                        <td align="right" width="120"><? echo number_format($amnt_tk,2); ?></td>
                        <td align="right" width="120"><? echo number_format($currency_rate,2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($amount,2); ?></td>
                    </tr>
                <?
                	$i++;
					$total_trims_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_trims_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="gmt_print_wash_dye_embell_cost_actual")
{
	echo load_html_head_contents("Embellishment Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);


 ?>
	<fieldset style="width:500px; margin-left:7px">
        <table class="rpt_table" width="460" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="150">Embl. Name</th>
                <th width="100">Qty</th>
                <th width="80">Rate</th>
                <th width="">Amount</th>
                
            </thead>
        </table>
        <div style="width:480px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="460" cellpadding="0" cellspacing="0">    
				<?
				
		//$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");

		$sql_per="select a.job_no, a.costing_per_id,b.id as po_id ,c.total_set_qnty from wo_pre_cost_dtls a,wo_po_break_down b, wo_po_details_master c where b.job_no_mst=a.job_no   and b.job_no_mst=c.job_no and b.id in($po_id)";

		$result_costPer=sql_select( $sql_per );
		foreach($result_costPer as $row)
		{
			$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per_id')];
			$job_ratio_arr[$row[csf('po_id')]]=$row[csf('total_set_qnty')];
			$job_arr[$row[csf('po_id')]]=$row[csf('job_no')];
		}

				$condition= new condition();
				if(trim(str_replace("'","",$po_id))!='')
				 {
					$condition->po_id("in($po_id)"); 
				 }
				  $condition->init();
				
				
			
				$emblishment= new emblishment($condition);
					// echo $conversion->getQuery();die;
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name_qty=$emblishment->getQtyArray_by_orderAndEmbname();
				
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash_qty=$wash->getQtyArray_by_orderAndEmbname();
				 
                $i=1; $total_gmt_cost=0; //$avg_rate=76;
                /*$sql="select a.booking_no, a.booking_date,b.job_no, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name!=3 and b.po_break_down_id=$po_id and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,b.job_no, a.booking_date, a.currency_id, a.exchange_rate";*/
				$sql_garments="select b.po_break_down_id as po_id,b.embel_name,b.production_date,b.production_type,
				(CASE WHEN b.embel_name=$embl_name THEN b.production_quantity ELSE 0 END) AS gmt_qty
				
				 from pro_garments_production_mst b where  b.production_type=2    and b.is_deleted=0 and b.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($po_id) and b.embel_name=$embl_name";
				$result_gar=sql_select( $sql_garments );
				foreach ($result_gar as $row)
				{
					
					$embl_issue_prod_array[$row[csf('embel_name')]]['gmt_qty']+=$row[csf('gmt_qty')];
					//$embl_issue_prod_array[$row[csf('embel_name')]]['embro_qty']+=$row[csf('embro_qty')];
					//$embl_issue_prod_array[$row[csf('embel_name')]]['wash_qty']+=$row[csf('wash_qty')];
					//$embl_issue_prod_array[$row[csf('embel_name')]]['dyeing_qty']+=$row[csf('dyeing_qty')];
					
				}
				
              //  $result=sql_select($sql);
                foreach($embl_issue_prod_array as $embel_name=>$row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$jobno=$job_arr[$po_id];
					$ratio=$job_ratio_arr[$po_id];
					$costing_per_id=$costing_per_arr[$jobno];
					$dzn_qnty=0;
						//$costing_per_id=$costing_per_arr[$row[('job_no')]];
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
						$dzn_qnty=$dzn_qnty*$ratio;
						if($embl_name==3) //Wash
						{
						$gmt_amount=$emblishment_costing_arr_name_wash[$po_id][$embl_name];
						$gmt_qty=$emblishment_costing_arr_name_wash_qty[$po_id][$embl_name];
						//echo $gmt_qty.'=='.$gmt_amount;
						$gmt_avg_rate=$gmt_amount/$gmt_qty;
						}
						else
						{
							//echo $embl_name;
						$gmt_amount=$emblishment_costing_arr_name[$po_id][$embl_name];
						$gmt_qty=$emblishment_costing_arr_name_qty[$po_id][$embl_name];
						$gmt_avg_rate=$gmt_amount/$gmt_qty;
						}
					
						$tot_amount=($row[('gmt_qty')]/$dzn_qnty)*$gmt_avg_rate;
                	//echo $gmt_qty.'='.$gmt_amount.'='.$ratio;
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="150"><p><? echo $emblishment_name_array[$embel_name]; ?></p></td>
                        <td width="100" align="right"><? echo $row[('gmt_qty')]; ?></td>
                        <td width="80" align="right"><p><? echo number_format($gmt_avg_rate,4); ?></p></td>
                        <td align="right" width="">
                            <? 
                                echo number_format($tot_amount,2); 
                            ?>
                        </td>
                       
                    </tr>
                <?
                	$i++;
					$total_gmt_cost+=$tot_amount;
                }
                ?>
                <tfoot>
                    
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_gmt_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="wash_cost_actual")
{
	echo load_html_head_contents("Wash Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

 ?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0; $avg_rate=76;
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name=3 and b.po_break_down_id=$po_id and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="commission_cost_actual")
{
	echo load_html_head_contents("Commission Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$data=explode("_",$po_id);
	$poId=$data[0];
	$job_no=$data[1];
	$ex_factory_qty=$data[2];
	$dzn_qnty=$data[3];

 ?>
	<fieldset style="width:560px; margin-left:10px">
        <table class="rpt_table" width="555" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="170">Commission Type</th>
                <th width="170">Commission Base</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:555px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="535" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_comm_cost=0;
                $sql="select particulars_id, commission_base_id, commission_amount as amount from wo_pre_cost_commiss_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0 and commission_amount>0";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="170"><p><? echo $commission_particulars[$row[csf('particulars_id')]]; ?></p></td>
                        <td width="170"><p><? echo $commission_base_array[$row[csf('commission_base_id')]]; ?></p></td>
                        <td align="right">
                            <?
								$amount=($ex_factory_qty/$dzn_qnty)*$row[csf('amount')];
                                echo number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_comm_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_comm_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	
	$unit_price_arr=return_library_array( "select id, unit_price 
	from wo_po_break_down where status_active=1 and is_deleted=0 and id=$id ", "id", "unit_price");	
	?>
    
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
	<div style="width:100%" align="center" id="report_container">
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:550px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="100">Ex-Fact. Return Qty.</th>
                        <th width="">Ex-Fact. Value</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
				$exfac_sql=("select b.challan_no,b.ex_factory_date, 
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
				from   pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
					$tot_exfact_qty=$row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_return_qnty")]; 
					$unit_price=$unit_price_arr[$id]; 
					$tot_exfact_val=$tot_exfact_qty*$unit_price;                            
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("challan_no")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                        <td width="" align="right"><? echo number_format($tot_exfact_val,2); ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
					  $total_exfact_val+=$tot_exfact_val;
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                     <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                    <th><? echo number_format($total_exfact_val,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                 <th><? echo number_format($total_exfact_val,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}
//Ex-Factory Delv. and Return
if($action=="show_po_detail_report")
{
	?>
    <script>
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('html_print_data').innerHTML+'</body</html>');
		d.close(); 
	}
	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_v4_controller.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../../');
	}
	
	function openmypage(po_id,type,tittle)
	{
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_v4_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../../');
	}
	
	
 </script>
 <?	
		
		
		echo load_html_head_contents("Po Detail", "../../../../", 1, 1,$unicode,'','');
		extract($_REQUEST);
		$company_short_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		
		
	  	$bgcolor="#E9F3FF";  $bgcolor2="#FFFFFF";	
		
	  	 $gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$job_no' and body_part_id=1");
		 $gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$job_no' and body_part_id=20");
		 $costing_date=return_field_value("costing_date", "wo_pre_cost_mst", "job_no='$job_no'","costing_date");
		 $po_qty=0;
		 $po_plun_cut_qty=0;
		 $total_set_qnty=0;$tot_po_value=0;
		 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.order_total,b.unit_price,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id and b.id =$po_id  and a.job_no ='$job_no'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$tot_po_value+=$sql_po_row[csf('order_total')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$unit_price=$sql_po_row[csf('unit_price')]/$total_set_qnty;
		$tot_po_value=$po_qty*$unit_price;
		}
		$fab_knit_req_kg_avg=0;
		$fab_woven_req_yds_avg=0;
		if($db_type==0)
		{
		$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.budget_minute,b.approved,b.updated_by,b.sew_smv,b.sew_effi_percent,b.incoterm,b.exchange_rate,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on   b.job_no=c.job_no where a.job_no=b.job_no and a.job_no='$job_no' and a.company_name=$company_name  and a.status_active=1  order by a.job_no";  
		}
		if($db_type==2)
		{
		 $sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.budget_minute,b.approved,b.updated_by,b.sew_smv,b.sew_effi_percent,b.incoterm,b.exchange_rate,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on   b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 and a.job_no='$job_no' and a.company_name=$company_name  order by a.job_no";  
		}
		
		$data_array=sql_select($sql);
		
		
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
		$fabric_costing_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		$yarn= new yarn($condition);
	    $yarn_req_arr=$yarn->getOrderWiseYarnQtyArray();
				
		
		 $fab_knit_qty_gray=($fabric_costing_qty_arr['knit']['grey'][$po_id]/$po_qty)*12;
		 $fab_woven_qty_gray=($fabric_costing_qty_arr['woven']['grey'][$po_id]/$po_qty)*12;
		 
		 $fab_knit_qty_finish=($fabric_costing_qty_arr['knit']['finish'][$po_id]/$po_qty)*12;
		 $fab_woven_qty_finish=($fabric_costing_qty_arr['woven']['finish'][$po_id]/$po_qty)*12;
		 $yarn_req_qty_avg=($yarn_req_arr[$po_id]/$po_qty)*12;
		 //echo  $fab_knit_qty_gray.'='.$fab_woven_qty_gray;
		 
	?>
	<div style="width:100%" align="center"> <input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px;"/>
		<fieldset style="width:850px" id="html_print_data">
       
         <table width="840px"   cellpadding="0" cellspacing="0" border="0">
        	<tr>
            	<td rowspan="3" width="25%">
                   <?
                $data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
              	foreach($data_array2 as $img_row)
                {
					?>
                    <img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
                </td>
            	<td align="center"><b style="font-size:20px;"><? echo $company_short_arr[$company_name]; ?></b></td>
            	<td rowspan="3" width="25%" align="right">
                <?
                   $data_array_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");               
                foreach($data_array_img as $img_row)
                {
					?>
                    <img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='80' align="middle" />	
                    <? 
                }
                ?>
               
                </td>
            </tr>
        	<tr>
            	<td align="center"><b style="font-size:14px;">Pre and Post Cost Comparison</b></td>
            </tr>
           <tr>
                <td align="center">
                    
					<? if( $data_array[0][csf("approved")]==1){echo "<div style='font-size:18px; color:#F00; background:#CCC;'>THIS COST SHEET IS APPROVED </div>";
					
					} else {echo "&nbsp;";} 
					
					$prepared_app_data = $data_array[0][csf("updated_by")];		

					?> 
                     
                </td>
            </tr>
        
        </table>
       			 <? 
				$uom="";
				foreach ($data_array as $row)
				{	
					$order_price_per_dzn=0;
					$order_job_qnty=0;
					$avg_unit_price=0;
					
					$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
					$result =sql_select("select po_number,grouping,file_no,pub_shipment_date from wo_po_break_down where job_no_mst='$job_no'  and id=$po_id and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
					$job_in_orders = '';$pulich_ship_date='';$job_in_file = '';$job_in_ref = '';
					foreach ($result as $val)
					{
						$job_in_orders= $val[csf('po_number')];
						$pulich_ship_date = $val[csf('pub_shipment_date')];
						$job_in_ref.=$val[csf('grouping')].",";
						$job_in_file.=$val[csf('file_no')].",";
						
					}
					$job_in_orders = $job_in_orders;
					
					$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
					$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
					
					foreach ($job_ref as $ref)
					{
						$ref_cond.=", ".$ref;
					}
					$file_con='';
					foreach ($job_file as $file)
					{
						if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
					}
					if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" For 1 DZN";}
					else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" For PCS";}
					else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" For 2 DZN";}
					else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="For 3 DZN";}
					else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" For 4 DZN";}
		
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr bgcolor="<? echo $bgcolor2;?>">
                    	<td width="130">Job Number</td>
                        <td width="100"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="130">Buyer</td>
                        <td width="110"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; $buyer_id=$row[csf("buyer_name")];?></b></td>
                        <td>Garments Item</td>
                        <? 
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="160"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                         <td>Costing Date</td><td><? echo $costing_date;?></td>
                        <td>PO Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo number_format($po_qty)." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    	<td>Order Numbers</td>
                        <td colspan="3"><? echo $job_in_orders; ?></td>
                        <td>Plan Cut Qnty [Cut % <? echo number_format((($po_plun_cut_qty/$total_set_qnty-$po_qty)/$po_qty)*100,2);?>]</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo number_format($po_plun_cut_qty/$total_set_qnty)." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo $fab_knit_qty_gray;$fab_knit_req_kg_avg+=$fab_knit_qty_gray; ?> (Kg)</b></td>
	
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo $fab_woven_qty_gray;$fab_woven_req_yds_avg+= $fab_woven_qty_gray;?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo number_format($row[csf("avg_unit_price")],2); ?> USD</b></td>
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo $yarn_req_qty_avg; ?> (Kg)</b></td>
                        <td>Woven Fin Fabric Cons</td>
                        <td><b><? echo $fab_woven_qty_finish; ?>(Yds)</b></td>
                        
                        <td>Order Value</td>
                        <td><b><? echo number_format($po_qty*$row[csf("avg_unit_price")],2); ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                    	<td>Knit Fin Fabric Cons</td>
                        <td><b><? echo $fab_knit_qty_finish; ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_for; ?></b></td>
                        <td>Inco Term</td>
                        <td><b><? echo $incoterm[$row[csf("incoterm")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                        <td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom ?></b></td>
                        <td>SMV</td>
                        <td><b><? $sew_smv=$row[csf("sew_smv")]; echo number_format($sew_smv,2); ?> </b></td>
                        <td>Efficiency %</td>
                        <td colspan="1"><b><? echo $sew_effi_percent=$row[csf("sew_effi_percent")] ?> </b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                        <td>Exchange Rate</td>
                        <td><b><? echo $exchange_rate=$row[csf("exchange_rate")]; ?></b></td>
                        <td>Budget SAH</td>
                        <td><b><? echo number_format(($row[csf("sew_smv")]*$po_qty)/60,2); ?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo $pulich_ship_date;?></b></td>
                    </tr>
                    
                    
                    
                    
                </table>
            <?	
			
			
	}//end first foearch
	
				$condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$job_no) !=''){
					  $condition->job_no("='$job_no'");
				 }
				if(str_replace("'","",$po_id)!='')
				 {
					 $condition->po_id("=$po_id");
				 }
				 $condition->init();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				 $yarn= new yarn($condition);
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				// print_r($conversion_costing_arr_process);
				 $trims= new trims($condition);
				 $trims_costing_arr=$trims->getAmountArray_by_order();
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
				
				$exchange_rate=76; 
				$sql_fin_purchase="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$po_id group by b.po_breakdown_id";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase_amnt')]/$exchange_rate;
				}
				
				 $subconInBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) and b.order_id=$po_id group by b.order_id");
				foreach($subconInBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
				}	
				$prodcostDataArray=sql_select("select job_no, 
									  sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
									  sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
									  sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
									  sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
									  from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}
				$actualCostDataArray=sql_select("select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 group by cost_head,po_id");
				foreach($actualCostDataArray as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
					
				$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and  a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				foreach($bookingDataArray as $woRow)
				{
					$amount=0; $trimsAmnt=0;
					if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
					
					if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
						{
							$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else
						{
							$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
					}
					else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
					}
					else if($woRow[csf('item_category')]==4)
					{
						if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
						$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
					}
				}
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$sql_receive="select prod_id, sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=1 and item_category=1 and status_active=1 and is_deleted=0 group by prod_id";
				$resultReceive = sql_select($sql_receive);
				foreach($resultReceive as $invRow)
				{
					$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
					$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
				}
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.po_breakdown_id=$po_id group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]];
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no' group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no'");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				
				$ex_factory_arr=return_library_array( "select po_break_down_id, 
				sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as qnty 
				from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id =$po_id group by po_break_down_id", "po_break_down_id", "qnty");
				
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$job_no]['costing_per_id'];
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
					
				 $ex_factory_qty=$ex_factory_arr[$po_id];
				 $unit_price=$unit_price/$total_set_qnty;
				$tot_ex_factory_val=$ex_factory_qty*$unit_price;
				$tot_fabric_purchase_cost_mkt=$fabric_costing_arr['knit']['grey'][$po_id]+$fabric_costing_arr['woven']['grey'][$po_id];
				$tot_fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$po_id];
				$tot_yarn_cost_mkt=$yarn_costing_arr[$po_id];
				$tot_yarn_dye_cost_mkt=$conversion_costing_arr_process[$po_id][30];
				$tot_knit_cost_mkt=$conversion_costing_arr_process[$po_id][1];
				
					$tot_dye_finish_cost_mkt=0;$not_yarn_dyed_cost_arr=array(1,2,30,35);
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$tot_dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$po_id][$process_id]);
						}
					}
					$tot_dye_finish_cost_actual=$subconCostArray[$po_id]['dye_finish_bill']/$exchange_rate;
					$tot_knit_cost_actual=$subconCostArray[$po_id]['knit_bill']/$exchange_rate;
					
					$tot_aop_cost_actual=$aopCostArray[$po_id];
					$tot_aop_cost_mkt=$conversion_costing_arr_process[$po_id][35];
					$tot_trims_cost_mkt=$trims_costing_arr[$po_id];
					$tot_trims_cost_actual=$actualTrimsCostArray[$po_id];
					
					$print_amount=$emblishment_costing_arr_name[$po_id][1];
					$embroidery_amount=$emblishment_costing_arr_name[$po_id][2];
					$special_amount=$emblishment_costing_arr_name[$po_id][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$po_id][3];
					$other_amount=$emblishment_costing_arr_name[$po_id][5];
					
					$tot_embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					$tot_embell_cost_actual=$embellCostArray[$po_id];
					$tot_wash_cost_mkt=$wash_cost;
					$tot_wash_cost_actual=$washCostArray[$po_id];
					
					$foreign_cost=$commission_costing_arr[$po_id][1];
					$local_cost=$commission_costing_arr[$po_id][2];
					
					
					
					$tot_commission_cost_mkt=$foreign_cost+$local_cost;
					$tot_commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$job_no]['commission'];
					$tot_comm_cost_actual=$actualCostArray[6][$po_id];
					$tot_comm_cost_mkt=$commercial_costing_arr[$po_id];
					
					$tot_test_cost_mkt=$other_costing_arr[$po_id]['lab_test'];
					$tot_freight_cost_mkt=$other_costing_arr[$po_id]['freight'];
					$tot_inspection_cost_mkt=$other_costing_arr[$po_id]['inspection'];
					$tot_certificate_cos_mktt=$other_costing_arr[$po_id]['certificate_pre_cost'];
					//$common_oh_cost=$other_costing_arr[$row[csf('id')]]['common_oh'];
					$tot_currier_cost_mkt=$other_costing_arr[$po_id]['currier_pre_cost'];
					$tot_cm_cost_mkt=$other_costing_arr[$po_id]['cm_cost'];
					
					$tot_yarn_cost_actual=$yarnTrimsCostArray[$po_id][1];
					
					$tot_freight_cost_actual=$actualCostArray[2][$po_id];
					$tot_test_cost_actual=$actualCostArray[1][$po_id];
					$tot_inspection_cost_actual=$actualCostArray[3][$po_id];
					$tot_currier_cost_actual=$actualCostArray[4][$po_id];
					$tot_cm_cost_actual=$actualCostArray[5][$po_id];
					
					//$tot_mkt_all_cost=$tot_mkt_all_cost;
					$tot_mkt_all_cost=$tot_yarn_cost_mkt+$tot_knit_cost_mkt+$tot_dye_finish_cost_mkt+$tot_yarn_dye_cost_mkt+$tot_aop_cost_mkt+$tot_trims_cost_mkt+$tot_embell_cost_mkt+$tot_wash_cost_mkt+$tot_commission_cost_mkt+$tot_comm_cost_mkt+$tot_freight_cost_mkt+$tot_test_cost_mkt+$tot_inspection_cost_mkt+$tot_currier_cost_mkt+$tot_cm_cost_mkt+$tot_fabric_purchase_cost_mkt;
					
					$tot_mkt_margin=$tot_po_value-$tot_mkt_all_cost;
					$tot_mkt_margin_perc=($mkt_margin/$tot_po_value)*100;
					
					$tot_actual_all_cost=$tot_yarn_cost_actual+$tot_knit_cost_actual+$tot_dye_finish_cost_actual+$tot_yarn_dye_cost_actual+$tot_aop_cost_actual+$tot_trims_cost_actual+$tot_embell_cost_actual+$tot_wash_cost_actual+$tot_commission_cost_actual+$tot_comm_cost_actual+$tot_freight_cost_actual+$tot_test_cost_actual+$tot_inspection_cost_actual+$tot_currier_cost_actual+$tot_cm_cost_actual+$tot_fabric_purchase_cost_actual;
					
					$tot_actual_margin=$tot_ex_factory_val-$tot_actual_all_cost;
					$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
					
	?>
         <br/> <br/>
        <table width="760" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
      
                            <thead>
                                <th width="30">SL</th>
                                <th width="180">Particulars</th>
                                <th width="130">Pre Costing</th>
                                <th width="80">%</th>
                                <th width="130">Post-Costing</th>
                                <th width="80">%</th>
                                <th>Variance</th>
                            </thead> 
							<?
								$bgcolor1='#E9F3FF';
								$bgcolor2='#FFFFFF';
							?>
                            </thead>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_1','<? echo $bgcolor1; ?>')" id="trtd_1">
                                <td align="center">1</td>
                                <td>PO/Shipment Value</td>
                                <td align="right"><? echo number_format($tot_po_value,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_2','<? echo $bgcolor2; ?>')" id="trtd_2">
                                <td colspan="7"><b>Cost</b></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_3','<? echo $bgcolor1; ?>')" id="trtd_3">
                                <td align="center">2</td>
                                <td>Fabric Purchase Cost</td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fabric_purchase_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fabric_purchase_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_4','<? echo $bgcolor2; ?>')" id="trtd_4">
                                <td align="center">3</td>
                                <td>Yarn Cost+Yarn Dyeing Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $tot_yarn_cost_mkt."**".$tot_yarn_dye_cost_mkt; ?>','mkt_yarn_cost','Grey And Dyed Yarn Mkt. Cost Details')"></a><? echo number_format($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format((($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_yarn_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)-$tot_yarn_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_5','<? echo $bgcolor2; ?>')" id="trtd_5">
                                <td align="center">4</td>
                                <td>Knitting Cost</td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_knit_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_6','<? echo $bgcolor2; ?>')" id="trtd_6">
                                <td align="center">5</td>
                                <td>Dye & Fin Cost</td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','dye_fin_cost','Dye & Fin Cost Details')"></a><? echo number_format($tot_dye_finish_cost_actual,2); ?></td>
                                <td align="right">
								<? 
								echo number_format(($tot_dye_finish_cost_actual/$tot_ex_factory_val)*100,2); 
								
								?>
                                </td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_7','<? echo $bgcolor1; ?>')" id="trtd_7">
                                <td align="center">6</td>
                                <td>AOP & Others Cost</td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_8','<? echo $bgcolor2; ?>')" id="trtd_8">
                                <td align="center">7</td>
                                <td>Trims Cost</td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_trims_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt-$tot_trims_cost_actual,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_9','<? echo $bgcolor1; ?>')" id="trtd_9">
                                <td align="center">8</td>
                                <td>Embellishment Cost</td>
                                <td align="right"><? echo number_format($tot_embell_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_embell_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_embell_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_embell_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_embell_cost_mkt-$tot_embell_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_10','<? echo $bgcolor2; ?>')" id="trtd_10">
                                <td align="center">9</td>
                                <td>Wash Cost</td>
                                <td align="right"><? echo number_format($tot_wash_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_wash_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_wash_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_wash_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_wash_cost_mkt-$tot_wash_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_11','<? echo $bgcolor1; ?>')" id="trtd_11">
                                <td align="center">10</td>
                                <td>Commission Cost</td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_commission_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_commission_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt-$tot_commission_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_12','<? echo $bgcolor2; ?>')" id="trtd_12">
                                <td align="center">11</td>
                                <td>Commercial Cost</td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_comm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_comm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt-$tot_comm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_13','<? echo $bgcolor1; ?>')" id="trtd_13">
                                <td align="center">12</td>
                                <td>Freight Cost</td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_freight_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_freight_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt-$tot_freight_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_14','<? echo $bgcolor2; ?>')" id="trtd_14">
                                <td align="center">13</td>
                                <td>Testing Cost</td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_test_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_test_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt-$tot_test_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">14</td>
                                <td>Inspection Cost</td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_inspection_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_inspection_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt-$tot_inspection_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_16','<? echo $bgcolor2; ?>')" id="trtd_16">
                                <td align="center">15</td>
                                <td>Courier Cost</td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_currier_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_currier_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt-$tot_currier_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">16</td>
                                <td>CM</td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_cm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_cm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt-$tot_cm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_18','<? echo $bgcolor2; ?>')" id="trtd_18">
                                <td align="center">17</td>
                                <td>Total Cost</td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_all_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><? echo number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_19','<? echo $bgcolor1; ?>')" id="trtd_19">
                                <td align="center">18</td>
                                <td>Margin/Loss</td>
                                <td align="right"><? echo number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_margin,2); ?></td>
                                <td align="right"><? echo number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                            </tr>
                        </table>
		</fieldset>
	</div>    
	<?
    exit();	
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=248 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);
	
	$buttonHtml="";
	foreach($printButton as $id){
		if($id==108)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />&nbsp;';
		if($id==195)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generated(2)" />&nbsp;';
	}
	
	echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}
//disconnect($con);


?>
<script>
function openmypage(po_id,type,tittle)
	{
		//alert(po_id);
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_v4_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '');
	}
</script>