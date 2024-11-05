<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
$bank_details=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
$buyer_details=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

if($action=="create_file_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	if($data[1]==0) $lien_bank="%%"; else $lien_bank=$data[1];
	if($data[2]==0) $buyer_name="%%"; else $buyer_name=$data[2];
	$internal_file_no="%".trim($data[3])."%";
	$year="%".trim($data[4])."%";

	if($db_type==0)
	{
		$sql="select internal_file_no, lc_year, lien_bank, buyer_name from
		(
			select a.internal_file_no, a.lc_year, a.lien_bank, a.buyer_name from com_export_lc a where a.beneficiary_name='$company_id' and a.buyer_name like '$buyer_name' and a.lien_bank like '$lien_bank' and a.internal_file_no like '$internal_file_no' and a.lc_year like '$year' and a.status_active=1 and a.is_deleted=0 group by a.internal_file_no, a.lc_year
		union 
			select b.internal_file_no, b.sc_year, b.lien_bank, b.buyer_name from com_sales_contract b where b.beneficiary_name='$company_id' and b.buyer_name like '$buyer_name' and b.lien_bank like '$lien_bank' and b.internal_file_no like '$internal_file_no' and b.sc_year like '$year' and b.status_active=1 and b.is_deleted=0 group by b.internal_file_no, b.sc_year
		)  	 
		com_export_lc group by internal_file_no, lc_year order by internal_file_no ASC";
	}
	else
	{
		$sql="select a.internal_file_no, a.lc_year, max(a.lien_bank) as lien_bank, max(a.buyer_name) as buyer_name from com_export_lc a where a.beneficiary_name='$company_id' and a.buyer_name like '$buyer_name' and a.lien_bank like '$lien_bank' and a.internal_file_no like '$internal_file_no' and a.status_active=1 and a.is_deleted=0 group by a.internal_file_no, a.lc_year
			union 
			select b.internal_file_no, b.sc_year, max(b.lien_bank) as lien_bank, max(b.buyer_name) as buyer_name from com_sales_contract b where b.beneficiary_name='$company_id' and b.buyer_name like '$buyer_name' and b.lien_bank like '$lien_bank' and b.internal_file_no like '$internal_file_no' and b.status_active=1 and b.is_deleted=0 group by b.internal_file_no, b.sc_year
			order by internal_file_no ASC";
	}
	//echo $sql;
	$arr=array (2=>$buyer_details,3=>$bank_details);
		
	echo create_list_view("tbl_list_search", "File No,Year,Buyer,Lien Bank", "120,100,120","580","230",0, $sql , "js_set_value", "internal_file_no,lc_year", "", 1, "0,0,buyer_name,lien_bank", $arr , "internal_file_no,lc_year,buyer_name,lien_bank", "","",'0,0,0,0','',1) ;
	
   exit(); 
}
 
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
$job_company_library=array(); $ratio_arr=array();
$jobData=sql_select("select job_no, company_name, total_set_qnty from wo_po_details_master");
foreach($jobData as $row)
{
	$job_company_library[$row[csf('job_no')]] = $row[csf('company_name')];
	$ratio_arr[$row[csf('job_no')]] = $row[csf('total_set_qnty')];
}

//Bank Percent Details
$sql = "SELECT account_id, company_id, loan_limit FROM lib_bank_account where account_type=20 and loan_type=0 and status_active=1 and is_deleted=0";
$result = sql_select($sql);

$percent_details = array();
foreach($result as $row)
{
	$percent_details[$row[csf('account_id')]][$row[csf('company_id')]] = $row[csf('loan_limit')];
}

$sql_cost_heads="SELECT company_name, cost_heads, cost_heads_status FROM variable_settings_commercial where variable_list=17 and status_active=1 and is_deleted=0 order by cost_heads";
$result_cost_heads = sql_select($sql_cost_heads);

$cost_heads_fabric_array=array(); $cost_heads_embellish_array=array(); $cost_details=array();
foreach( $result_cost_heads as $row )
{
	if($row[csf('cost_heads_status')]==1)
	{
		if($row[csf('cost_heads')]=="101" || $row[csf('cost_heads')]=="102" || $row[csf('cost_heads')]=="103")
		{
			if (array_key_exists($row[csf('company_name')], $cost_heads_embellish_array)) 
			{
				$cost_heads_embellish_array[$row[csf('company_name')]].=",".substr($row[csf('cost_heads')],-1);
			}
			else
			{
				$cost_heads_embellish_array[$row[csf('company_name')]]=substr($row[csf('cost_heads')],-1);	
			}
		}
		else
		{
			if($row[csf('cost_heads')]!=75 && $row[csf('cost_heads')]!=78)
			{
				if (array_key_exists($row[csf('company_name')], $cost_heads_fabric_array)) 
				{
					$cost_heads_fabric_array[$row[csf('company_name')]].=",".$row[csf('cost_heads')];	
				}
				else
				{
					$cost_heads_fabric_array[$row[csf('company_name')]]=$row[csf('cost_heads')];		
				}
			}
		}
	}
	
	/*if($row[csf('cost_heads')]=="101" || $row[csf('cost_heads')]=="102" || $row[csf('cost_heads')]=="103")
	{
		$cost_heads=substr($row[csf('cost_heads')],-1);
	}
	else
	{
		$cost_heads=$row[csf('cost_heads')];
	}*/
	$cost_heads=$row[csf('cost_heads')];
	$cost_details[$row[csf('company_name')]][$cost_heads] = $row[csf('cost_heads_status')];
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="conversion_rate_popup")
{
	echo load_html_head_contents("BTB Liability Coverage Report", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
	
		function fnc_close()
		{
			var data=''; var currency_id=''; var rate='';
			var row_num = $('#tbl_list tbody tr').length;
			for(var j=1;j<=row_num;j++) 
			{
				currency_id=$('#txt_currency_id_'+j).val();
				rate=$('#txt_coversion_rate_'+j).val();
				
				if(data=="")
				{
					data=currency_id+"**"+rate;
				}
				else
				{
					data+=','+currency_id+"**"+rate;
				}
			}
			
			$('#all_conversion_rate_id').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="conversion_rate" id="conversion_rate">
		<fieldset style="width:450px;">
			<div class="form_caption" style="margin-bottom:20px">Conversion Rate</div>         
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Currency</th>
                    <th>Conversion Rate</th>
                    <input type="hidden" name="all_conversion_rate_id" id="all_conversion_rate_id" value="" />
                </thead>
                <tbody>
                	<?
					$i=1;
					
					$conversion_data=explode(",",$hide_conversion_rate);
					
					foreach($currency as $key=>$value)
					{
						if($key==2)
						{
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							
							$currency_data = explode("**",$conversion_data[$i-1]);
							$rate=$currency_data[1];
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
                                <td style=" padding-left:8px">
                                <? echo $value; ?>
                                <input type="hidden" name="txt_currency_id_<? echo $i;?>" id="txt_currency_id_<? echo $i;?>" value="<? echo $key; ?>"/>
                                </td>
                                <td align="center"><input type="text" name="txt_coversion_rate_<? echo $i; ?>" id="txt_coversion_rate_<? echo $i; ?>" value="<? echo $rate; ?>" class="text_boxes_numeric" style="width:145px"></td>
							</tr>
							<?
							$i++;	
						}
					}
					?>
            	</tbody>
           	</table>
			<table width="100%">
				<tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="internal_file_no_search_popup")
{
	echo load_html_head_contents("BTB Liability Coverage Report", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var year_arr=new Array;
		
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
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
			}

			if( jQuery.inArray( str[2], year_arr) == -1 ) 
			{
				year_arr.push(str[2]);
			}
			
		
			var id = ''; var year='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			if(year_arr.length<2) 
			{
				var year=year_arr;
			}
			
			$('#internal_file_no').val( id );
			$('#txt_year').val( year );
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="internal_file_frm" id="internal_file_frm">
		<fieldset style="width:580px;">
            <table width="550" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Enter File No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="internal_file_no" id="internal_file_no" value="" />
                    <input type="hidden" name="txt_year" id="txt_year" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_internal_file_no" id="txt_internal_file_no" />	
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+'<? echo $lien_bank; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_internal_file_no').value+'**'+'<? echo $year; ?>', 'create_file_no_search_list_view', 'search_div', 'btb_liability_coverage_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $reportType;die;
	$reportType=str_replace("'","",$reportType);
	$company_name=str_replace("'","",$cbo_company_name);
	if(str_replace("'","",$cbo_lien_bank)==0) $lien_bank_id="%%"; else $lien_bank_id=str_replace("'","",$cbo_lien_bank);
	if(str_replace("'","",$txt_year)=="") $file_year="%%"; else $file_year=str_replace("'","",$txt_year);
	//echo $txt_internal_file_no;die;
	if(str_replace("'","",$txt_internal_file_no)=="")
	{
		$file_no_cond_lc="";
		$file_no_cond_sc="";
	}
	else
	{
		$file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
		$file_no_string="";
		foreach($file_no_arr as $file_no)
		{
			$file_no_string.="'".$file_no."',";
		}
		$file_no_string=chop($file_no_string,",");
		$file_no_cond_lc="and a.internal_file_no in(".$file_no_string.")";
		$file_no_cond_sc="and b.internal_file_no in(".$file_no_string.")";	
	}
	
	//echo $file_no_cond_lc."<br>".$file_no_cond_sc;die;
	
	$currency_conversion=explode(",",str_replace("'","",$hide_conversion_rate));
	$conversion_details=array();
	for($d=0;$d<count($currency_conversion);$d++)
	{
		$conv_rate=explode("**",$currency_conversion[$d]);
		
		if($conv_rate[1]=="" || $conv_rate[1]==0) $rate=1; else $rate=$conv_rate[1];
		
		$conversion_details[$conv_rate[0]] = $rate;
	}
	
	$fabriccostArray=array(); $costing_date_library=array();
	$costing_sql=sql_select("select job_no, costing_per_id, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
	foreach($costing_sql as $row)
	{
		$fabriccostArray[$row[csf('job_no')]]['cpi']=$row[csf('costing_per_id')]; 
		$fabriccostArray[$row[csf('job_no')]]['freight']=$row[csf('freight')];
		$fabriccostArray[$row[csf('job_no')]]['comm_cost']=$row[csf('comm_cost')];
	}
	
	//$trimscostArray=array();
	//$trimsArray=sql_select("select a.job_no, b.po_break_down_id, sum(b.cons*a.rate) as trims_cost_total from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 group by a.job_no, b.po_break_down_id");
	/*$trimsArray=sql_select("select a.job_no, sum(a.amount) as trims_cost_total from wo_pre_cost_trim_cost_dtls a where a.status_active=1 and a.is_deleted=0 group by a.job_no");
	foreach($trimsArray as $row)
	{
		//$trimscostArray[$row[csf('job_no')]][$row[csf('po_break_down_id')]]=$row[csf('trims_cost_total')]; 
		$trimscostArray[$row[csf('job_no')]]=$row[csf('trims_cost_total')]; 
	}*/
	
	/*$convcostArray=array();
	$convArray=sql_select("select job_no, cons_process, sum(amount) as amount from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no, cons_process");
	foreach($convArray as $row)
	{
		$convcostArray[$row[csf('job_no')]][$row[csf('cons_process')]]=$row[csf('amount')]; 
	}*/
	
	$emblcostArray=array();
	$emblArray=sql_select("select job_no, emb_name, sum(amount) as amount from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0 group by job_no, emb_name");
	foreach($emblArray as $row)
	{
		$emblcostArray[$row[csf('job_no')]][$row[csf('emb_name')]]=$row[csf('amount')]; 
	}
	
	$yarn_cons_cost_arr=array();
	$yarnDataArr=sql_select( "select job_no, cons_qnty as qnty, avg_cons_qnty as avg_qnty, rate from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0");
	foreach($yarnDataArr as $row)
	{
		if($row[csf('avg_qnty')]==0) $row[csf('avg_qnty')]=$row[csf('qnty')];
		
		$amount=0;
		$amount=$row[csf('avg_qnty')]*$row[csf('rate')];
		
		$yarn_cons_cost_arr[$row[csf('job_no')]]['qnty']+=$row[csf('qnty')]; 
		$yarn_cons_cost_arr[$row[csf('job_no')]]['avg_qnty']+=$row[csf('avg_qnty')];
		$yarn_cons_cost_arr[$row[csf('job_no')]]['amount']+=$amount;
	}
	unset($yarnDataArr);
	
	$l_cost_arr=return_library_array( "select job_no, commission_amount from wo_pre_cost_commiss_cost_dtls where particulars_id=2 and status_active=1 and is_deleted=0","job_no","commission_amount");
	$woven_knit_purchase_cost_arr=array();
	$fabricArray=sql_select("select job_no, fab_nature_id, sum(amount) as amount from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 and fabric_source=2 group by job_no, fab_nature_id");
	foreach($fabricArray as $row)
	{
		if($row[csf('fab_nature_id')]==3)
		{
			$woven_knit_purchase_cost_arr[$row[csf('job_no')]]['woven']=$row[csf('amount')]; 
		}
		else if($row[csf('fab_nature_id')]==2)
		{
			$woven_knit_purchase_cost_arr[$row[csf('job_no')]]['knit']=$row[csf('amount')]; 
		}
	}
	//print_r($woven_knit_purchase_cost_arr['MFG-23-00012']); die;
	//echo "$reportType test $template";die;
	if($reportType==1)
	{
		if($template==1)
		{
			ob_start();
			?>
			<div style="width:5240px;">
				<fieldset style="width:100%;">	 
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
						   <td colspan="35" align="center" width="100%" style="font-size:18px"><b>BTB Liability Coverage Report</b></td>
						</tr>
						<tr>
						   <td colspan="35" align="center" width="100%" style="font-size:18px"><b><? echo $company_library[$company_name]; ?></b></td>
						</tr>
					</table>
					<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th width="40" rowspan="2">SL</th>
								<th width="100" rowspan="2">Internal File No.</th>
								<th width="100" rowspan="2">Bank File No.</th>
								<th width="120" rowspan="2">SC/LC No.</th>
								<th width="80" rowspan="2">Conv/ Replace Type</th>
								<th width="120" rowspan="2">SC Value(LC/SC, Finance)</th>
								<th width="120" rowspan="2">LC/SC Replaced</th>
								<th width="120" rowspan="2">Balance</th>
								<th width="120" rowspan="2">SC Value (Direct)</th>
                                <th width="120" rowspan="2">LC Value (Direct)</th>
                                <th width="120" rowspan="2">File Value</th>
								<th width="90" rowspan="2">Pay Term</th>
								<th width="70" rowspan="2">Tenor</th>
								<th width="120" rowspan="2">Lien Bank</th>
								<th width="80" rowspan="2">Last Ship Date</th>
								<th width="120" rowspan="2">Buyer Name</th>
								<th width="120" rowspan="2">Order Value (As Attached)</th>
								<th width="100" rowspan="2">Foreign Commission</th>
								<th width="60" rowspan="2">%</th>
								<th width="100" rowspan="2">Local Commission</th>
								<th width="60" rowspan="2">%</th>
								<th width="100" rowspan="2">Freight & Commercial Cost</th>
								<th width="100" rowspan="2">Deduction on Export Bill</th>
								<th width="60" rowspan="2">%</th>
								<th width="120" rowspan="2">Net L/C Value</th>
								<th width="80" rowspan="2">Currency</th>
								<th colspan="4" width="420">BB LC Liability</th>
								<th colspan="5" width="500">Packing Credit (Domestic Currency)</th>
								<th width="100" rowspan="2">PC Liability in %</th>
								<th colspan="6" width="720">Shipment Status</th>
								<th colspan="3" width="360">DFC Position</th>
								<th colspan="2" width="220">BTB Liability</th>
								<th width="100" rowspan="2">BTB Liability % On Resource</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="120">BBL/C Entitlement</th>
								<th width="120">Opening</th>
								<th width="60">%</th>
								<th width="120">Fund Available</th>
								<th width="120">PC Limit </th>
								<th width="120">PC Drawn</th>
								<th width="120">Fund Available</th>
								<th width="120">PC Adjusted</th>
								<th width="120">Outstanding BDT</th>
								<th width="120">Gross Bill Amount</th>
                                <th width="120">Net Bill Amount</th>
								<th width="120">Realized</th>
								<th width="120">Short realised</th>
								<th width="120">Unrealized</th>
								<th width="120">Net Ship Balance</th>
								<th width="120">Margin Build</th>
								<th width="120">Paid</th>
								<th width="120">Balance</th>
								<th width="110">Adjustment</th>
								<th width="110">Liability</th>
							</tr>
						</thead>
					</table>
					<div style="width:5260px; max-height:400px; overflow-y:scroll" id="dfc_liability">
						<table width="5240" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
						<?
							$internal_file_no_arr=array(); $lc_arr=array(); $sc_arr=array(); $lc_claim_comn_arr=array(); $sc_claim_comn_arr=array(); 
							$i=1; $lc_ids=''; $sc_ids='';
							
							$sql="select a.internal_file_no, a.id as lc_sc_id, a.bank_file_no, a.export_lc_no as no, a.replacement_lc as repl_conv, a.lc_value as value, a.pay_term, a.tenor, a.last_shipment_date, a.remarks, a.foreign_comn, a.lien_bank, a.currency_name, a.buyer_name, a.applicant_name, a.claim_adjustment, 0 as converted_from, 1 as type 
							from com_export_lc a 
							where a.beneficiary_name='$company_name' and a.lien_bank like '$lien_bank_id' and a.lc_year like '$file_year' and a.status_active=1 and a.is_deleted=0 $file_no_cond_lc 
							union all 
							select b.internal_file_no, b.id as lc_sc_id, b.bank_file_no, b.contract_no as no, b.convertible_to_lc as repl_conv, b.contract_value as value, b.pay_term, b.tenor, b.last_shipment_date, b.remarks, b.foreign_comn, b.lien_bank, b.currency_name, b.buyer_name, b.applicant_name, b.claim_adjustment, b.converted_from, 2 as type 
							from com_sales_contract b 
							where b.beneficiary_name='$company_name' and b.lien_bank like '$lien_bank_id' and b.sc_year like '$file_year' and b.status_active=1 and b.is_deleted=0 $file_no_cond_sc 
							order by internal_file_no, type ASC";
							//echo $sql;die;
							$nameArray=sql_select( $sql );
							foreach ($nameArray as $row)
							{
								$internal_file_no_arr[$row[csf('internal_file_no')]].=$row[csf('lc_sc_id')]."**".$row[csf('type')]."**".$row[csf('pay_term')]."**".$row[csf('lien_bank')]."**".$row[csf('buyer_name')]."**".$row[csf('applicant_name')]."**".$row[csf('currency_name')].",";
								
								
								if($row[csf('type')]==1)
								{
									if($row[csf('repl_conv')] !=1)
									{
										$internal_file_value[$row[csf('internal_file_no')]]+=$row[csf('value')];
									}
									
									$lc_ids.=$row[csf('lc_sc_id')].",";
									$lc_arr[$row[csf('lc_sc_id')]]=$row[csf('bank_file_no')]."**".$row[csf('no')]."**".$row[csf('repl_conv')]."**".$row[csf('value')]."**".$row[csf('pay_term')]."**".$row[csf('tenor')]."**".$row[csf('last_shipment_date')]."**".$row[csf('foreign_comn')]."**".$row[csf('lien_bank')]."**".$row[csf('currency_name')]."**".$row[csf('buyer_name')]."**".$row[csf('applicant_name')]."**".$row[csf('converted_from')]."**".$row[csf('claim_adjustment')]."**".$row[csf('remarks')];
									
									$lc_claim_comn_arr[$row[csf('lc_sc_id')]]['foreign_comn']=$row[csf('foreign_comn')];
									$lc_claim_comn_arr[$row[csf('lc_sc_id')]]['clad']=$row[csf('claim_adjustment')];
								}
								else
								{
									if($row[csf('repl_conv')]!=2 && $row[csf('converted_from')] <=0)
									{
										$internal_file_value[$row[csf('internal_file_no')]]+=$row[csf('value')];
									}
									if($row[csf('repl_conv')]==2)
									{
										$internal_file_value[$row[csf('internal_file_no')]]+=$row[csf('value')];
									}
									
									$sc_ids.=$row[csf('lc_sc_id')].",";
									$sc_arr[$row[csf('lc_sc_id')]]=$row[csf('bank_file_no')]."**".$row[csf('no')]."**".$row[csf('repl_conv')]."**".$row[csf('value')]."**".$row[csf('pay_term')]."**".$row[csf('tenor')]."**".$row[csf('last_shipment_date')]."**".$row[csf('foreign_comn')]."**".$row[csf('lien_bank')]."**".$row[csf('currency_name')]."**".$row[csf('buyer_name')]."**".$row[csf('applicant_name')]."**".$row[csf('converted_from')]."**".$row[csf('claim_adjustment')]."**".$row[csf('remarks')];
									
									$sc_claim_comn_arr[$row[csf('lc_sc_id')]]['foreign_comn']=$row[csf('foreign_comn')];
									$sc_claim_comn_arr[$row[csf('lc_sc_id')]]['clad']=$row[csf('claim_adjustment')];
								}
							}
							
							$lc_ids=chop($lc_ids,','); $sc_ids=chop($sc_ids,',');  $poidStr=$jobidStr="";
							$lc_order_arr=array(); $sc_order_arr=array(); $btb_lc_data_arr=array(); $btb_sc_data_arr=array(); 
							
							$net_inv_val_arr=array(); $sc_inv_id_arr=array();
							//echo $lc_ids."**".$sc_ids;die;
							if($lc_ids!="")
							{
								$invoiceData=sql_select("select id, is_lc, lc_sc_id, net_invo_value, (discount_ammount+bonus_ammount+claim_ammount+commission) as deduct_amnt from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 and is_lc=1 and lc_sc_id in($lc_ids)");
								foreach($invoiceData as $invRow)
								{
									$net_inv_val_arr[$invRow[csf('is_lc')]][$invRow[csf('lc_sc_id')]]['net_invo_value']+=$invRow[csf('net_invo_value')];
									$net_inv_val_arr[$invRow[csf('is_lc')]][$invRow[csf('lc_sc_id')]]['deduct_amnt']+=$invRow[csf('deduct_amnt')];
								}
								
								$sql_lc="select b.com_export_lc_id, b.attached_value, b.attached_qnty, b.attached_rate, c.id as po_id, c.po_quantity, c.job_no_mst, c.job_id from com_export_lc_order_info b, wo_po_break_down c where b.wo_po_break_down_id=c.id and b.com_export_lc_id in($lc_ids) and b.status_active=1 and b.is_deleted=0";
								$dataLc=sql_select( $sql_lc );
								foreach ($dataLc as $rowL)
								{
									$lc_order_arr[$rowL[csf('com_export_lc_id')]].=$rowL[csf('attached_value')]."**".$rowL[csf('attached_qnty')]."**".$rowL[csf('attached_rate')]."**".$rowL[csf('po_id')]."**".$rowL[csf('job_no_mst')]."**".$rowL[csf('po_quantity')].",";
									if($poidStr=="") $poidStr=$rowL[csf('po_id')]; else $poidStr.=','.$rowL[csf('po_id')];
									if($jobidStr=="") $jobidStr=$rowL[csf('job_id')]; else $jobidStr.=','.$rowL[csf('job_id')];
								}
								
								$sql_opened_lc="select b.lc_sc_id, sum(b.current_distribution) as lc_value, c.id, c.lc_value as lc_value2, c.payterm_id from com_btb_export_lc_attachment b, com_btb_lc_master_details c where b.is_lc_sc=0 and b.import_mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.lc_sc_id in($lc_ids) group by b.lc_sc_id, c.id, c.lc_value, c.payterm_id";
								$res_open_lc=sql_select($sql_opened_lc);
								foreach($res_open_lc as $row_open_lc)
								{
									$btb_lc_data_arr[$row_open_lc[csf('lc_sc_id')]].=$row_open_lc[csf('id')]."**".$row_open_lc[csf('lc_value')]."**".$row_open_lc[csf('payterm_id')].",";
								}
								
								$pc_drawn_lc_arr=return_library_array( "select lc_sc_id, sum(amount) as amount from com_pre_export_lc_wise_dtls where export_type=1 and lc_sc_id in($lc_ids) group by lc_sc_id","lc_sc_id","amount");
								
								if($db_type==0)
								{
									$subm_id_lc_arr=return_library_array( "select lc_sc_id, group_concat(doc_submission_mst_id) as sub_id from com_export_doc_submission_invo where is_lc=1 and lc_sc_id in($lc_ids) group by lc_sc_id","lc_sc_id","sub_id");
								}
								else
								{
									$subm_id_lc_arr=return_library_array( "select lc_sc_id, LISTAGG(doc_submission_mst_id, ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as sub_id from com_export_doc_submission_invo where is_lc=1 and lc_sc_id in($lc_ids) group by lc_sc_id","lc_sc_id","sub_id");
								}
								
								$doc_inv_lc_arr=return_library_array( "select b.lc_sc_id, sum(c.current_invoice_value) as current_invoice_value from com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where b.is_lc=1 and b.id=c.mst_id and b.lc_sc_id in($lc_ids) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.lc_sc_id","lc_sc_id","current_invoice_value");
							}
							
							if($sc_ids!="")
							{
								$invoiceData=sql_select("select id, is_lc, lc_sc_id, net_invo_value, (discount_ammount+bonus_ammount+claim_ammount+commission) as deduct_amnt from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 and is_lc=2 and lc_sc_id in($sc_ids)");
								foreach($invoiceData as $invRow)
								{
									$net_inv_val_arr[$invRow[csf('is_lc')]][$invRow[csf('lc_sc_id')]]['net_invo_value']+=$invRow[csf('net_invo_value')];
									$net_inv_val_arr[$invRow[csf('is_lc')]][$invRow[csf('lc_sc_id')]]['deduct_amnt']+=$invRow[csf('deduct_amnt')];
									$sc_inv_id_arr[$invRow[csf('lc_sc_id')]].=$invRow[csf('id')].",";
								}
								
								$sql_sc="select b.com_sales_contract_id, b.attached_value, b.attached_qnty, b.attached_rate, c.id as po_id, c.po_quantity, c.job_no_mst, c.job_id from com_sales_contract_order_info b, wo_po_break_down c where b.wo_po_break_down_id=c.id and b.com_sales_contract_id in($sc_ids) and b.status_active=1 and b.is_deleted=0";
								$dataSc=sql_select( $sql_sc );
								foreach ($dataSc as $rowS)
								{
									$sc_order_arr[$rowS[csf('com_sales_contract_id')]].=$rowS[csf('attached_value')]."**".$rowS[csf('attached_qnty')]."**".$rowS[csf('attached_rate')]."**".$rowS[csf('po_id')]."**".$rowS[csf('job_no_mst')]."**".$rowS[csf('po_quantity')].",";
									if($poidStr=="") $poidStr=$rowS[csf('po_id')]; else $poidStr.=','.$rowS[csf('po_id')];
									if($jobidStr=="") $jobidStr=$rowS[csf('job_id')]; else $jobidStr.=','.$rowS[csf('job_id')];
								}
								
								
								$sql_opened_sc="select b.lc_sc_id, sum(b.current_distribution) as lc_value, c.id, c.lc_value as lc_value2, c.payterm_id from com_btb_export_lc_attachment b, com_btb_lc_master_details c where b.is_lc_sc=1 and b.import_mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.lc_sc_id in($sc_ids) group by b.lc_sc_id, c.id, c.lc_value, c.payterm_id";
								$res_open_sc=sql_select($sql_opened_sc);
								foreach($res_open_sc as $row_open_sc)
								{
									$btb_sc_data_arr[$row_open_sc[csf('lc_sc_id')]].=$row_open_sc[csf('id')]."**".$row_open_sc[csf('lc_value')]."**".$row_open_sc[csf('payterm_id')].",";
								}
								
								$pc_drawn_sc_arr=return_library_array( "select lc_sc_id, sum(amount) as amount from com_pre_export_lc_wise_dtls where export_type=2 and lc_sc_id in($sc_ids) group by lc_sc_id","lc_sc_id","amount");
								
								if($db_type==0)
								{
									$subm_id_sc_arr=return_library_array( "select lc_sc_id, group_concat(doc_submission_mst_id) as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in($sc_ids) group by lc_sc_id","lc_sc_id","sub_id");
								}
								else
								{
									$subm_id_sc_arr=return_library_array( "select lc_sc_id, LISTAGG(doc_submission_mst_id, ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in($sc_ids) group by lc_sc_id","lc_sc_id","sub_id");
								}
								
								$doc_inv_sc_arr=return_library_array( "select b.lc_sc_id, sum(c.current_invoice_value) as current_invoice_value from com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where b.is_lc=2 and b.id=c.mst_id and b.lc_sc_id in($sc_ids) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.lc_sc_id","lc_sc_id","current_invoice_value");
							}
							
							$doc_sub_arr=array();
							$doc_sub_data=sql_select("select doc_submission_mst_id,
														sum(CASE WHEN acc_head=20 THEN dom_curr END) AS packing,
														sum(CASE WHEN acc_head=5 THEN lc_sc_curr END) AS margin_build
														from com_export_doc_sub_trans where status_active=1 and is_deleted=0 group by doc_submission_mst_id");
							foreach($doc_sub_data as $rowD)
							{
								$doc_sub_arr[$rowD[csf('doc_submission_mst_id')]][20]=$rowD[csf('packing')];
								$doc_sub_arr[$rowD[csf('doc_submission_mst_id')]][5]=$rowD[csf('margin_build')];
							}
								
							$proc_real_arr=array();
							$proc_real_data=sql_select("select d.is_invoice_bill, d.invoice_bill_id,
														sum(CASE WHEN e.account_head=20 THEN e.domestic_currency END) AS packing,
														sum(CASE WHEN e.account_head=5 THEN e.document_currency END) AS margin_build,
														sum(CASE WHEN e.type=1 THEN e.document_currency END) AS shortrealized,
														sum(CASE WHEN e.type=0 THEN e.document_currency END) AS realized
														from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.is_invoice_bill, d.invoice_bill_id");
							foreach($proc_real_data as $rowP)
							{
								$proc_real_arr[$rowP[csf('is_invoice_bill')]][$rowP[csf('invoice_bill_id')]]['packing']=$rowP[csf('packing')];
								$proc_real_arr[$rowP[csf('is_invoice_bill')]][$rowP[csf('invoice_bill_id')]]['margin_build']=$rowP[csf('margin_build')];
								$proc_real_arr[$rowP[csf('is_invoice_bill')]][$rowP[csf('invoice_bill_id')]]['shortrealized']=$rowP[csf('shortrealized')];
								$proc_real_arr[$rowP[csf('is_invoice_bill')]][$rowP[csf('invoice_bill_id')]]['realized']=$rowP[csf('realized')];
							}	
							
							$btb_paid_arr=array();
							$paid_sql="select a.btb_lc_id, b.adj_source, sum(b.accepted_ammount) as accepted_ammount from com_import_invoice_mst a, com_import_payment b where a.id=b.invoice_id and a.is_lc=1 and b.payment_head=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.btb_lc_id, b.adj_source";
							$paidArray=sql_select( $paid_sql );
							foreach($paidArray as $row_paid)
							{
								$btb_paid_arr[$row_paid[csf('btb_lc_id')]][$row_paid[csf('adj_source')]]=$row_paid[csf('accepted_ammount')];
							}	
							
							$paidAdjArr=array();
							$paid_sql_adj="select a.btb_lc_id, 
														sum(case when a.retire_source=5 then b.current_acceptance_value else 0 end) as paid_val,
														sum(case when a.retire_source!=5 then b.current_acceptance_value else 0 end) as paid_adj_val
														from com_import_invoice_mst a, com_import_invoice_dtls b where a.id=b.import_invoice_id and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.btb_lc_id";
							$paidArrayAdj=sql_select($paid_sql_adj);
							foreach($paidArrayAdj as $rowAdj)
							{
								$paidAdjArr[$rowAdj[csf('btb_lc_id')]]['paid_val']=$rowAdj[csf('paid_val')];
								$paidAdjArr[$rowAdj[csf('btb_lc_id')]]['paid_adj_val']=$rowAdj[csf('paid_adj_val')];
							}	
							
							$poids=implode(",",array_filter(array_unique(explode(",",$poidStr))));
							$jobids=implode(",",array_filter(array_unique(explode(",",$jobidStr))));

							$condition= new condition();
							if($poids !=""){
								$condition->po_id_in("$poids");
								$condition->jobid_in("$jobids");
								$condition->company_name($company_name); 
								
								$condition->init();
								$trims= new trims($condition);
								//echo $trims->getQuery(); die;
								$trimscostArray=$trims->getAmountArray_by_order();
								
								/*$yarns= new yarn($condition);
								//echo $yarns->getQuery(); die;
								$yarnQntyArray=$yarns->getOrderWiseYarnQtyArray();
								$yarncostArray=$yarns->getOrderWiseYarnAmountArray();
								//echo "<pre>";print_r($yarncostArray);die;*/
								
								$fabric= new fabric($condition);
								//echo $fabric->getQuery(); die;
								$fabricCostArrayClass=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
								//$fabricQntyArrayClass=$fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
								//echo "<pre>";
								//print_r($fabricCostArrayClass['woven']['finish']); die;
								$conversion= new conversion($condition);
								//echo $conversion->getQuery(); die;
								$convcostArray=$conversion->getAmountArray_by_orderAndProcess();
							}
							
							//print_r($internal_file_no_arr);
							foreach ($internal_file_no_arr as $internal_file_no=>$data)
							{
								$file_lc_amnt=0; $file_sc_amnt=0; $file_sc_amntD=0; $file_replace_amnt=0; $file_value=0; $file_order_value=0; $file_fore_comn=0; $file_carring_cost=0; $file_deducd=0; $file_net_lc_val=0; $file_local_comn=0; $file_bblc_entitle=0; $file_tot_opening=0; $file_fund_avail=0; $file_pc_limit=0; $file_pc_drawn=0;$file_fund_avail_pack=0; $file_pc_adjust=0; $file_out_bdt=0; $file_bill_amnt=0; $file_net_bill_amnt=0; $file_realized=0; $file_short_realized=0; $file_unrealized=0; $file_ship_bal=0; $file_margin_build=0; $file_paid=0; $file_btb_liability=0;
						
								$s=1; $foreign_comn=0; $local_comm=0; $local_comm_per=0; $carring_cost=0; $foreign_comn_cost=0; $foreign_comm_per=0; $file_deduction=0; $file_deduction_per=0; $net_lc_value=0; $bbb_lc_entitle=0; $btb_opened_val=0; $fund_available=0; $bblc_per=0; $pc_limit=0; $pc_drawn=0; $fund_available_packing=0; $pc_adjusted=0; $out_bdt=0; $margin_build=0; $gross_bill_amnt=0; $net_bill_amnt=0; $realized_val=0; $unrealized_val=0; $short_realized=0; $ship_balance=0; $pc_liability_per=0; $paid_val=0; $balance=0; $btb_liability=0; $btb_liability_adjust=0; $liability_reso_per=0; $cost_heads_fabric_order=0; $cost_heads_embellish_order=0; $trims_cost=0; $yarn_cost=0; $att_total_value=0; $deduc_on_claim_adjustment=0; $cost_heads_knit_purchase=0; $cost_heads_woven_purc=0; $invoice_id_sc_of_cash_in_advance=array();
								
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$lc_id=''; $sc_id=''; $sc_id_of_cash_in_advance='';
								$lcScData=explode(",",chop($data,','));
								foreach($lcScData as $value)
								{
									$lc_sc=explode("**",$value);
									$lc_sc_id=$lc_sc[0];
									$type=$lc_sc[1];
									$pay_term_id=$lc_sc[2];
									$lien_bankId=$lc_sc[3];
									$buyer_name=$lc_sc[4];
									$applicant_name=$lc_sc[5];
									$currency_name=$lc_sc[6];
									
									if($type==1)
									{
										$lcScOrArray=$lc_order_arr[$lc_sc_id];
										$foreign_comn_lc_sc=$lc_claim_comn_arr[$lc_sc_id]['foreign_comn'];
										$claim_adjustment=$lc_claim_comn_arr[$lc_sc_id]['clad'];
										$deduc_on_claim_adjustment+=$claim_adjustment;
									}
									else
									{
										$lcScOrArray=$sc_order_arr[$lc_sc_id];
										$foreign_comn_lc_sc=$sc_claim_comn_arr[$lc_sc_id]['foreign_comn'];
										$claim_adjustment=$sc_claim_comn_arr[$lc_sc_id]['clad'];
										$deduc_on_claim_adjustment+=$claim_adjustment;
									}
									
									$lcScOrArray=explode(",",chop($lcScOrArray,','));
									foreach($lcScOrArray as $val)
									{
										$lcSc_Data=explode("**",chop($val,','));
										$attached_value=$lcSc_Data[0];
										$attached_qnty=$lcSc_Data[1];
										$attached_rate=$lcSc_Data[2];
										$po_id=$lcSc_Data[3];
										$job_no_mst=$lcSc_Data[4];
										$poQty=$lcSc_Data[5];
										
										$att_total_value+=$attached_value;
										$costing_per_id=$fabriccostArray[$job_no_mst]['cpi'];
										$ratio=$ratio_arr[$job_no_mst];
										$dzn_qnty=0;
										if($costing_per_id==1) $dzn_qnty=12;
										else if($costing_per_id==3) $dzn_qnty=12*2;
										else if($costing_per_id==4) $dzn_qnty=12*3;
										else if($costing_per_id==5) $dzn_qnty=12*4;
										else $dzn_qnty=1;
										//$dzn_qnty=$dzn_qnty*$ratio;
										
										$freight=$fabriccostArray[$job_no_mst]['freight'];
										$comm_cost=$fabriccostArray[$job_no_mst]['comm_cost'];
										
										$freight_cost=($attached_qnty/$dzn_qnty)*$freight;
										$commercial_cost=($attached_qnty/$dzn_qnty)*$comm_cost;
										
										$carring_cost+=$freight_cost+$commercial_cost;
										
										//$trimscost=$trimscostArray[$job_no_mst][$po_id];
										//$trims_cost+=($attached_qnty/$dzn_qnty)*$trimscost;
										$trimscost=$trimscostArray[$po_id];
										$trims_cost+=($trimscost/$poQty)*$attached_qnty;
										
										$yarn_cons_cost=$yarn_cons_cost_arr[$job_no_mst]['amount'];
										$yarn_cost+=($attached_qnty/$dzn_qnty)*$yarn_cons_cost;
										//echo $attached_qnty.'/'.$dzn_qnty.'*'.$yarn_cons_cost;
										//echo ($attached_qnty/$dzn_qnty)*$yarn_cons_cost." PO- ".$po_id."<br>";
										$l_cost=$l_cost_arr[$job_no_mst];
										$local_comm+=($attached_qnty/$dzn_qnty)*$l_cost;
										
										if($foreign_comn==0 && $foreign_comn_lc_sc>0)
										{ 
											$foreign_comn=$foreign_comn_lc_sc;
										}
										//echo $cost_heads_fabric_array[$job_company_library[$job_no_mst]];
										$cost_heads_fabric=explode(",",$cost_heads_fabric_array[$job_company_library[$job_no_mst]]);
										//print_r($cost_heads_fabric);
										foreach($convcostArray[$po_id] as $cons_process=>$uomdata)
										{
											foreach($uomdata as $uom=>$amnt)
											{
												/*if($costId==30)
												{
													$costIds=array(30);
												}
												else if($costId==35)
												{
													$costIds=array(35,36,37,40);
												}
												else if($costId==1)
												{
													$costIds=array(1,3,4,134);
												}
												else
												{
													$costIds=array(25,26,31,33,39,101,61,70,137,34,32,65,136,67,68,69,71,94,82,89,138,135,123,132,60,62,63,66,72,73,74,75,77,78,79,80,81,83,84,85,86,87,88,90,91,92,93,128,129,133);
												}
												$fabric_cost=0;
												foreach($costIds as $id)
												{
													//$fabric_cost+=$convcostArray[$job_no_mst][$id];
													//$fabric_cost+=($amnt/$poQty)*$attached_qnty;
												}*/
												$cost_heads_fabric_order+=($amnt/$poQty)*$attached_qnty;//($attached_qnty/$dzn_qnty)*$fabric_cost;
											}
										}
										
										foreach($emblcostArray[$job_no_mst] as $emb_name=>$amnt)
										{
											$embellish_cost=((($amnt/$dzn_qnty)*$attached_qnty)/$poQty)*$attached_qnty;
											$cost_heads_embellish_order+=$embellish_cost;//($attached_qnty/$dzn_qnty)*$embellish_cost;
										}
										
										/*$cost_heads_embellish=explode(",",$cost_heads_embellish_array[$job_company_library[$job_no_mst]]);
										foreach($cost_heads_embellish as $embellId)
										{
											//$embellish_cost=$emblcostArray[$job_no_mst][$embellId];
											//$cost_heads_embellish_order+=($attached_qnty/$dzn_qnty)*$embellish_cost;
										}*/
										//print_r($fabricCostArrayClass['knit']['finish']);
										if($cost_details[$job_company_library[$job_no_mst]][75]==1)
										{
											$knit_purc_cost=0;
											foreach($fabricCostArrayClass['knit']['finish'][$po_id][2] as $fab_amt)
											{
												$knit_purc_cost+=($fab_amt/$poQty)*$attached_qnty;
											}
											$cost_heads_knit_purchase+=$knit_purc_cost;
										}
										
										
										if($cost_details[$job_company_library[$job_no_mst]][78]==1)
										{
											//echo $job_no_mst.'=';
											/*foreach($fabricCostArrayClass['woven']['finish'][$po_id][2] as $wfab_amt)
											{
												$woven_purchase_cost+=($wfab_amt/$poQty)*$attached_qnty;
											}
											$cost_heads_woven_purc+=$woven_purchase_cost;*/
											$woven_purchase_cost=$woven_knit_purchase_cost_arr[$job_no_mst]['woven'];
											$cost_heads_woven_purc+=($attached_qnty/$dzn_qnty)*$woven_purchase_cost;
										}
									}

									if($type==1)
									{
										if($lc_id=="") $lc_id=$lc_sc_id; else $lc_id.=",".$lc_sc_id;
									}
									else 
									{
										if($pay_term_id==3)
										{
											$invoice_id=chop($sc_inv_id_arr[$lc_sc_id],',');
											if($invoice_id!="")
											{
												$invoice_id_sc_of_cash_in_advance[$invoice_id]=$invoice_id; 
											}
										}
										
										if($sc_id=="") $sc_id=$lc_sc_id; else $sc_id.=",".$lc_sc_id;
									}
								}
								
								$local_comm_per=($local_comm/$att_total_value)*100;
								$foreign_comn_cost=($att_total_value*$foreign_comn)/100;
								$foreign_comm_per=($foreign_comn_cost/$att_total_value)*100;
								
								$bbb_lc_entitle=$cost_heads_fabric_order+$cost_heads_embellish_order+$yarn_cost+$trims_cost+$cost_heads_knit_purchase+$cost_heads_woven_purc;
								//echo $yarn_cost;
								
								$btb_lc_id_array=array(); $btb_lc_id_atSight_array=array(); $doc_sub_sc_packing=0; $doc_sub_sc_margin_build=0; 
								$proc_real_sc_packing=0; $proc_real_sc_margin_build=0; $proc_real_sc_realized=0; $proc_real_sc_shortrealized=0;
								$proc_real_sc_packing_invoice=0; $proc_real_sc_margin_build_invoice=0; $proc_real_sc_realized_invoice=0; $proc_real_sc_shortrealized_invoice=0;
								$doc_sub_lc_packing=0; $doc_sub_lc_margin_build=0; $proc_real_lc_packing=0; $proc_real_lc_margin_build=0; $proc_real_lc_realized=0; 
								$proc_real_lc_shortrealized=0; $deduc_on_lc=0; $deduc_on_sc=0; $net_bill_amnt=0; $pc_drawn_lc=0; $pc_drawn_sc=0; $bill_id_lc=''; $bill_id_sc='';
								$n=0; $opened_lc=0; $opened_sc=0; $doc_inv_lc=0; $doc_inv_sc=0;
								$lcIdArr=explode(",",$lc_id); $btbPaidArr=array();
								//echo $subm_id_lc_arr[2]."**".$subm_id_lc_arr[20];
								foreach($lcIdArr as $lcId)
								{
									$net_bill_amnt+=$net_inv_val_arr[1][$lcId]['net_invo_value'];
									$deduc_on_lc+=$net_inv_val_arr[1][$lcId]['deduct_amnt'];
									$pc_drawn_lc+=$pc_drawn_lc_arr[$lcId];
									$bill_id_lc.=$subm_id_lc_arr[$lcId].",";
									$doc_inv_lc+=$doc_inv_lc_arr[$lcId];
									
									$btb_lc_data=explode(",",chop($btb_lc_data_arr[$lcId],','));
									foreach($btb_lc_data as $btb_data)
									{
										$btb=explode("**",$btb_data);
										$btbId=$btb[0];
										$btbVal=$btb[1];
										$paytermId=$btb[2];
										
										$opened_lc+=$btbVal;

										if($paytermId==1)
										{
											$btb_lc_id_atSight_array[$n]=$btbId;
											if(!in_array($btbId,$btbPaidArr))
											{
												foreach($paidAdjArr[$btbId] as $adj_source=>$amnt)
												{
													if($adj_source=="paid_val")
													{
														$paid_val+=$amnt;
													}
													else
													{
														$btb_liability_adjust+=$amnt;
													}
												}
												$btbPaidArr[]=$btbId;
											}
										}
										else
										{
											if(!in_array($btbId,$btbPaidArr))
											{
												foreach($btb_paid_arr[$btbId] as $adj_source=>$amnt)
												{
													if($adj_source==5)
													{
														$paid_val+=$amnt;
													}
													else
													{
														$btb_liability_adjust+=$amnt;
													}
												}
												$btbPaidArr[]=$btbId;
											}
											$btb_lc_id_array[$n]=$btbId;
										}
										
										$n++;
									}
								}
								
								$bill_id_lc=array_unique(explode(",",chop($bill_id_lc,',')));
								foreach($bill_id_lc as $billId)
								{
									$doc_sub_lc_packing+=$doc_sub_arr[$billId][20]; 
									$doc_sub_lc_margin_build+=$doc_sub_arr[$billId][5];
									
									$proc_real_lc_packing+=$proc_real_arr[1][$billId]['packing']; 
									$proc_real_lc_margin_build+=$proc_real_arr[1][$billId]['margin_build']; 
									$proc_real_lc_realized+=$proc_real_arr[1][$billId]['shortrealized']; 
									$proc_real_lc_shortrealized+=$proc_real_arr[1][$billId]['realized']; 
								}
								
								$bill_id_lc=implode(",",$bill_id_lc);
								
								$scIdArr=explode(",",$sc_id);
								foreach($scIdArr as $scId)
								{
									$net_bill_amnt+=$net_inv_val_arr[2][$scId]['net_invo_value'];
									$deduc_on_sc+=$net_inv_val_arr[2][$scId]['deduct_amnt'];
									$pc_drawn_sc+=$pc_drawn_sc_arr[$scId];
									$bill_id_sc.=$subm_id_sc_arr[$scId].",";
									$doc_inv_sc+=$doc_inv_sc_arr[$scId];
									
									$btb_sc_data=explode(",",chop($btb_sc_data_arr[$scId],','));
									foreach($btb_sc_data as $btb_data)
									{
										$btb=explode("**",$btb_data);
										$btbId=$btb[0];
										$btbVal=$btb[1];
										$paytermId=$btb[2];
										
										if($paytermId==1)
										{
											if(!in_array($btbId,$btb_lc_id_array))
											{
												$opened_sc+=$btbVal;
												$btb_lc_id_array[$n]=$btbId;
											}
											if(!in_array($btbId,$btb_lc_id_atSight_array))
											{
												$btb_lc_id_atSight_array[$n]=$btbId;
												foreach($paidAdjArr[$btbId] as $adj_source=>$amnt)
												{
													if($adj_source=="paid_val")
													{
														$paid_val+=$amnt;
													}
													else
													{
														$btb_liability_adjust+=$amnt;
													}
												}
											}
										}
										else
										{
											if(!in_array($btbId,$btb_lc_id_array))
											{
												$opened_sc+=$btbVal;
												$btb_lc_id_array[$n]=$btbId;
												foreach($btb_paid_arr[$btbId] as $adj_source=>$amnt)
												{
													if($adj_source==5)
													{
														$paid_val+=$amnt;
													}
													else
													{
														$btb_liability_adjust+=$amnt;
													}
												}	
											}
										}
										$n++;
									}
								}
								
								$bill_id_sc=array_unique(explode(",",chop($bill_id_sc,',')));
								foreach($bill_id_sc as $billId)
								{
									$doc_sub_lc_packing+=$doc_sub_arr[$billId][20]; 
									$doc_sub_lc_margin_build+=$doc_sub_arr[$billId][5];
									
									$proc_real_lc_packing+=$proc_real_arr[1][$billId]['packing']; 
									$proc_real_lc_margin_build+=$proc_real_arr[1][$billId]['margin_build']; 
									$proc_real_lc_realized+=$proc_real_arr[1][$billId]['shortrealized']; 
									$proc_real_lc_shortrealized+=$proc_real_arr[1][$billId]['realized']; 
								}
								
								$bill_id_sc=implode(",",$bill_id_sc,',');
								foreach($invoice_id_sc_of_cash_in_advance as $invoId)
								{
									$proc_real_sc_packing_invoice+=$proc_real_arr[2][$invoId]['packing']; 
									$proc_real_sc_margin_build_invoice+=$proc_real_arr[2][$invoId]['margin_build']; 
									$proc_real_sc_realized_invoice+=$proc_real_arr[2][$invoId]['shortrealized']; 
									$proc_real_sc_shortrealized_invoice+=$proc_real_arr[2][$invoId]['realized']; 
								}
								
								$file_deduction=$deduc_on_claim_adjustment+$deduc_on_lc+$deduc_on_sc;
								$file_deduction_per=($file_deduction/$att_total_value)*100;
								$net_lc_value=$att_total_value-$foreign_comn_cost-$local_comm-$carring_cost-$file_deduction;
								
								$btb_opened_val=$opened_lc+$opened_sc;
								$fund_available=$bbb_lc_entitle-$btb_opened_val;
								$bblc_per=($btb_opened_val/$net_lc_value)*100;
								
								if($conversion_details[$currency_name]=="") $conversion_rate=1; 
								else $conversion_rate=$conversion_details[$currency_name];
					
								$pc_limit=(($net_lc_value*$percent_details[$lien_bankId][$company_name])/100)*$conversion_rate;
								$pc_drawn=$pc_drawn_lc+$pc_drawn_sc;
								$fund_available_packing=$pc_limit-$pc_drawn;
								
								$pc_adjusted=$doc_sub_lc_packing+$doc_sub_sc_packing+$proc_real_lc_packing+$proc_real_sc_packing+$proc_real_sc_packing_invoice;
								$margin_build=$doc_sub_lc_margin_build+$doc_sub_sc_margin_build+$proc_real_lc_margin_build+$proc_real_sc_margin_build+$proc_real_sc_shortrealized_invoice;
								
								$out_bdt=$pc_drawn-$pc_adjusted;
								$gross_bill_amnt=$doc_inv_lc+$doc_inv_sc;
								
								$realized_val=$proc_real_lc_realized+$proc_real_sc_realized+$proc_real_sc_realized_invoice; 
								$short_realized=$proc_real_lc_shortrealized+$proc_real_sc_shortrealized+$proc_real_sc_shortrealized_invoice+$deduc_on_lc+$deduc_on_sc; 
								$unrealized_val=$gross_bill_amnt-($realized_val+$short_realized);

								//$ship_balance=$att_total_value-$net_bill_amnt;
								$ship_balance=$att_total_value-$gross_bill_amnt;// has been changed as per fatuallah apperals requiremnts
								
								$pc_liability_per=(($out_bdt/$conversion_rate)/($unrealized_val+$ship_balance))*100;
								
								$balance=$margin_build-$paid_val;
								$btb_liability=$btb_opened_val-($paid_val+$btb_liability_adjust);
								$bl=fn_number_format($unrealized_val+$ship_balance,2,'.','');
								$liability_reso_per=($btb_liability/($bl+$balance))*100;
								
								foreach($lcScData as $value)
								{
									$lc_sc=explode("**",$value);
									$lc_sc_id=$lc_sc[0];
									$type=$lc_sc[1];
									
									if($type==1)
									{
										$datas=$lc_arr[$lc_sc_id];
									}
									else
									{
										$datas=$sc_arr[$lc_sc_id];
									}
									
									$alldata=explode("**",$datas);
									$bank_file_no=$alldata[0];
									$no=$alldata[1];
									$repl_conv=$alldata[2];
									$value=$alldata[3];
									$pay_term_id=$alldata[4];
									$tenor=$alldata[5];
									$last_shipment_date=$alldata[6];
									$foreign_comn=$alldata[7];
									$lien_bank=$alldata[8];
									$currency_name=$alldata[9];
									$buyer_name=$alldata[10];
									$applicant_name=$alldata[11];
									$converted_from=$alldata[12];
									$claim_adjustment=$alldata[13];
									$remarks=$alldata[14];
									
									if($s==1)
									{
									?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td width="40"><? echo $i; ?></td>
                                            <td width="100">
                                            <p>
                                                <?
												echo $internal_file_no . "<br><a href='#report_details' style='color:#000' onclick= \"generate_report_file('".$internal_file_no."**".$buyer_name."**".$applicant_name."**".$company_name."**".$lien_bank_id."**".$file_year."**".$internal_file_value[$internal_file_no]."','internal_file_no_info','requires/internal_file_info_report','');\">Order Qnty Wise</a><br><a href='#report_details' style='color:#000' onclick= \"generate_report_file('".$internal_file_no."**".$buyer_name."**".$applicant_name."**".$company_name."**".$lien_bank_id."**".$file_year."**".$internal_file_value[$internal_file_no]."','internal_file_no_info','requires/internal_file_info_report_attach','');\">Attach Qnty Wise</a>"; 
                                                   
                                                ?>
                                              </p>
                                            </td>
                                            <td width="100"><p><? echo $bank_file_no; ?>&nbsp;</p></td>
                                            <?
											if($type==1)
											{			
											?>
												<td width="120"><p>LC-<? echo $no; ?></p></td>
												<td width="80" align="center"><? echo $yes_no[$repl_conv]; ?>&nbsp;</td>
												<td width="120" align="right">&nbsp;</td>
												<?
												if($repl_conv==1)
												{
													echo '<td width="120" align="right">'.fn_number_format($value,2).'</td>';	
													echo '<td width="120" align="right">&nbsp;</td><td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">&nbsp;</td>';	
													
													$file_replace_amnt+=$value; 
													$tot_replace_amnt+=$value;
												}
												else
												{
													echo '<td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">&nbsp;</td><td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">'.fn_number_format($value,2).'</td>';	
													
													$file_lc_amnt+=$value; 
													$tot_lc_amnt+=$value; 
												}
											}
											else
											{
											?>
												<td width="120"><p>SC-<? echo $no;?></p></td>
											<?
												if($repl_conv==2)
												{
                                                	echo '<td width="80" align="center">'.$convertible_to_lc[$repl_conv].'</td>';
													echo '<td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">&nbsp;</td><td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">'.fn_number_format($value,2).'&nbsp;</td>';
													
													$file_sc_amntD+=$value; 
													$tot_sc_amntD+=$value;
												}
												else
												{
													if($converted_from<=0)
													{
														echo '<td width="80" align="center">'.$convertible_to_lc[$repl_conv].'</td>';
														echo '<td width="120" align="right">'.fn_number_format($value,2).'&nbsp;</td>';
														echo '<td width="120" align="right">&nbsp;</td><td width="120" align="right">&nbsp;</td>';
														echo '<td width="120" align="right">&nbsp;</td>';
														
														$file_sc_amnt+=$value; 
														$tot_sc_amnt+=$value;
													}
													else
													{
														echo '<td width="80" align="center">'.$convertible_to_lc[$repl_conv]." (F)".'</td>';
														echo '<td width="120" align="right">&nbsp;</td>';
														echo '<td width="120" align="right">'.fn_number_format($value,2).'&nbsp;</td><td width="120" align="right">&nbsp;</td>';
														echo '<td width="120" align="right">&nbsp;</td>';
														
														$file_replace_amnt+=$value; 
														$tot_replace_amnt+=$value;
													}
												}
											?>
                                            	<td width="120" align="right">&nbsp;</td>
                                            <?
											}
											?>
                                            <td width="120" align="right">&nbsp;</td>
                                            <td width="90"><p><? echo $pay_term[$pay_term_id]; ?>&nbsp;</p></td>
                                            <td width="70"><? echo $tenor; ?>&nbsp;</td>
                                            <td width="120"><p><? echo $bank_details[$lien_bank]; ?>&nbsp;</p></td>
                                            <td width="80" align="center"><? echo change_date_format($last_shipment_date); ?></td>
                                            <td width="120"><p><? echo $buyer_details[$buyer_name];?></p></td>
                                            <td width="120" align="right"><? 
                                                echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','order_info','Order Info');\">".fn_number_format($att_total_value,2)."</a>";
                                                $tot_order_value+=$att_total_value; 
                                                $file_order_value+=$att_total_value; 
                                                ?>
                                            </td>
                                            <td width="100" align="right"><? echo fn_number_format($foreign_comn_cost,2); $tot_fore_comn+=$foreign_comn_cost; ?></td>
                                            <td width="60" align="right"><? echo fn_number_format($foreign_comm_per,2); ?></td>
                                            <td width="100" align="right"><? echo fn_number_format($local_comm,2); $tot_local_comn+=$local_comm; ?></td>
                                            <td width="60" align="right"><? echo fn_number_format($local_comm_per,2); ?></th>
                                            <td width="100" align="right"><? echo fn_number_format($carring_cost,2); $tot_carring_cost+=$carring_cost; ?></td>
                                            <td width="100" align="right"><? echo fn_number_format($file_deduction,2); $tot_deducd+=$file_deduction; ?></td>
                                            <td width="60" align="right"><? echo fn_number_format($file_deduction_per,2); ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($net_lc_value,2); $tot_net_lc_val+=$net_lc_value; ?></td>
                                            <td width="80" align="center"><? echo $currency[$currency_name]; ?></td>
                                            <td width="120" align="right" title="<?=$cost_heads_fabric_order.'-'.$cost_heads_embellish_order.'-'.$yarn_cost.'-'.$trims_cost.'-'.$cost_heads_knit_purchase.'-'.$cost_heads_woven_purc;?>"><? echo fn_number_format($bbb_lc_entitle,2); $tot_bblc_entitle+=$bbb_lc_entitle; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','btb_open_info','BTB Info');\">".fn_number_format($btb_opened_val,2)."</a>"; $tot_opening+=$btb_opened_val; ?></td>
                                            <td width="60" align="right"><? echo fn_number_format($bblc_per,2); ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($fund_available,2); $tot_fund_avail+=$fund_available; ?> </td>
                                            <td width="120" align="right" title="<?='(('.$net_lc_value.'*'.$percent_details[$lien_bankId][$company_name].')/100)*'.$conversion_rate; ?>"><? echo fn_number_format($pc_limit,2); $tot_pc_limit+=$pc_limit; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','pc_drawn_info','PC Drawn Info');\">".fn_number_format($pc_drawn,2)."</a>"; $tot_pc_drawn+=$pc_drawn; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($fund_available_packing,2); $tot_fund_avail_pack+=$fund_available_packing; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($pc_adjusted,2); $tot_pc_adjust+=$pc_adjusted; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($out_bdt,2); $tot_out_bdt+=$out_bdt; ?></td>
                                            <td width="100" align="right"><? echo fn_number_format($pc_liability_per,2); ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','gross_bill_info','Export Invoice Info');\">".fn_number_format($gross_bill_amnt,2)."</a>"; $grand_bill_amnt+=$gross_bill_amnt; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','net_bill_info','Export Invoice Info');\">".fn_number_format($net_bill_amnt,2)."</a>"; $grand_net_bill_amnt+=$net_bill_amnt; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','export_proceed_info','Export Proceed Realization Info');\">".fn_number_format($realized_val,2)."</a>"; $tot_realized+=$realized_val; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','short_realized','Short Realization Info');\">".fn_number_format($short_realized,2)."</a>"; $tot_short_realized+=$short_realized; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($unrealized_val,2); $tot_unrealized+=$unrealized_val; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($ship_balance,2); $tot_ship_bal+=$ship_balance; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($margin_build,2); $tot_margin_build+=$margin_build; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','dfc_paid_info','DFC Paid Info');\">".fn_number_format($paid_val,2)."</a>"; $tot_paid+=$paid_val; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($balance,2); $tot_balance+=$balance; ?></td>
                                            <td width="110" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','dfc_paid_info_adjust','DFC Paid Info');\">".fn_number_format($btb_liability_adjust,2)."</a>"; $tot_btb_liability_adjust+=$btb_liability_adjust; ?></td>
                                            <td width="110" align="right" title="<? echo "(btb opened value-(paid value+btb liability adjust))"; ?>"><? echo fn_number_format($btb_liability,2); $tot_btb_liability+=$btb_liability; ?></td>
                                            <td width="100" align="right"><? echo fn_number_format($liability_reso_per,2); ?></td>
                                            <td><p><? echo $remarks; ?>&nbsp;</p></td>
                                        </tr>
									<?
									}
									else
									{
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i.'_'.$s; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i.'_'.$s; ?>">
											<td width="40">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="100"><p><? echo $bank_file_no; ?>&nbsp;</p></td>
                                            <?
											if($type==1)
											{
											?>
												<td width="120"><p>LC-<? echo $no; ?></p></td>
												<td width="80" align="center"><? echo $yes_no[$repl_conv]; ?>&nbsp;</td>
												<td width="120" align="right">&nbsp;</td>
												<?
												if($repl_conv==1)
												{
													echo '<td width="120" align="right">'.fn_number_format($value,2).'</td>';	
													echo '<td width="120" align="right">&nbsp;</td><td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">&nbsp;</td>';	
													
													$file_replace_amnt+=$value; 
													$tot_replace_amnt+=$value;
												}
												else
												{
													echo '<td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">&nbsp;</td><td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">'.fn_number_format($value,2).'</td>';
													
													$file_lc_amnt+=$value; 
													$tot_lc_amnt+=$value; 
												}
											}
											else
											{
											?>
												<td width="120"><p>SC-<? echo $no;?></p></td>
											<?
												if($repl_conv==2)
												{
                                                	echo '<td width="80" align="center">'.$convertible_to_lc[$repl_conv].'</td>';
													echo '<td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">&nbsp;</td><td width="120" align="right">&nbsp;</td>';
													echo '<td width="120" align="right">'.fn_number_format($value,2).'&nbsp;</td>';
													
													$file_sc_amntD+=$value; 
													$tot_sc_amntD+=$value;
												}
												else
												{
													if($converted_from<=0)
													{
														echo '<td width="80" align="center">'.$convertible_to_lc[$repl_conv].'</td>';
														echo '<td width="120" align="right">'.fn_number_format($value,2).'&nbsp;</td>';
														echo '<td width="120" align="right">&nbsp;</td><td width="120" align="right">&nbsp;</td>';
														echo '<td width="120" align="right">&nbsp;</td>';
														
														$file_sc_amnt+=$value; 
														$tot_sc_amnt+=$value;
													}
													else
													{
														echo '<td width="80" align="center">'.$convertible_to_lc[$repl_conv]." (F)".'</td>';
														echo '<td width="120" align="right">&nbsp;</td>';
														echo '<td width="120" align="right">'.fn_number_format($value,2).'&nbsp;</td><td width="120" align="right">&nbsp;</td>';
														echo '<td width="120" align="right">&nbsp;</td>';
														
														$file_replace_amnt+=$value; 
														$tot_replace_amnt+=$value;
													}
												}
											?>
                                            	<td width="120" align="right">&nbsp;</td>
                                            <?
											}
											?>
                                            <td width="120" align="right">&nbsp;</td>
											<td width="90"><p><? echo $pay_term[$pay_term_id]; ?>&nbsp;</p></td>
											<td width="70"><? echo $tenor; ?>&nbsp;</td>
											<td width="120"><p><? echo $bank_details[$lien_bank]; ?>&nbsp;</p></td>
											<td width="80" align="center"><? echo change_date_format($last_shipment_date); ?></td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="60">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="60">&nbsp;</th>
											<td width="100">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="60">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="80" align="center"><? echo $currency[$currency_name]; ?>&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="60">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="110">&nbsp;</td>
											<td width="110">&nbsp;</td>
											<td width="100">&nbsp;</td>
											<td><p><? echo $remarks; ?>&nbsp;</p></td>
										</tr>
									<?
									}
								$s++;
								}
								?>
								<tr bgcolor="#CCCCCC">
									<td colspan="5" align="right"><b>File Total</b></td>
									<td align="right"><? echo fn_number_format($file_sc_amnt,2); ?></td>
									<td align="right"><? echo fn_number_format($file_replace_amnt,2); ?></td>
									<td align="right"><? $balance=$file_sc_amnt-$file_replace_amnt; $bal=abs($balance); echo fn_number_format($balance,2); $tot_bal+=$balance; ?></td>
                                    <td align="right"><? echo fn_number_format($file_sc_amntD,2); ?></td>
                                    <td align="right"><? echo fn_number_format($file_lc_amnt,2); ?></td>
									<td align="right">
										<?
											$file_value=$balance+$file_replace_amnt+$file_sc_amntD+$file_lc_amnt; 
											//$file_value=$file_sc_amnt+$file_sc_amntD+$file_lc_amnt; 
											echo fn_number_format($file_value,2); 
											$tot_file_value+=$file_value;
										?>
									</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td widfile="60">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
                                    <td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<?
								$i++;
							}
							?>
							<tfoot>
								<th colspan="5" align="right">Grand Total</th>
								<th align="right"><? echo fn_number_format($tot_sc_amnt,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_replace_amnt,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_bal,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_sc_amntD,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_lc_amnt,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_file_value,2); ?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th align="right"><? echo fn_number_format($tot_order_value,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_fore_comn,2); ?></th>
								<th width="60">&nbsp;</th>
								<th align="right"><? echo fn_number_format($tot_local_comn,2); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo fn_number_format($tot_carring_cost,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_deducd,2); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo fn_number_format($tot_net_lc_val,2); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo fn_number_format($tot_bblc_entitle,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_opening,2); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo fn_number_format($tot_fund_avail,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_pc_limit,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_pc_drawn,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_fund_avail_pack,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_pc_adjust,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_out_bdt,2); ?></th>
								<th>&nbsp;</th>
								<th align="right"><? echo fn_number_format($grand_bill_amnt,2); ?></th>
                                <th align="right"><? echo fn_number_format($grand_net_bill_amnt,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_realized,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_short_realized,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_unrealized,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_ship_bal,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_margin_build,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_paid,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_balance,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_btb_liability_adjust,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_btb_liability,2); ?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tfoot>
						</table>
					</div>
					<br />
					<div align="left">
						<b>Summary</b>
						<table width="900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th width="130">Unrealized Proceeds</th>
									<th width="130">Shipment Balance</th>
									<th width="130">DFC Balance</th>
									<th width="130">Total Resource</th>
									<th width="130">BTB Liability</th>
									<th width="120">BTB Liability  %</th>
									<th>PC Liability (BDT)</th>
								</tr>
							</thead>
							<tfoot>
								<tr class="tbl_bottom">
									<td align="right"><? echo fn_number_format($tot_unrealized,2); ?></td>
									<td align="right"><? echo fn_number_format($tot_ship_bal,2); ?></td>
									<td align="right"><? echo fn_number_format($tot_balance,2); ?></td>
									<td align="right"><? $total_res=$tot_unrealized+$tot_ship_bal+$tot_balance; echo fn_number_format($total_res,2); ?></td>
									<td align="right"><? echo fn_number_format($tot_btb_liability,2); ?></td>
									<td align="right"><? echo fn_number_format(($tot_btb_liability/fn_number_format($total_res,2,'.',''))*100,2); ?></td>
									<td align="right"><? echo fn_number_format($tot_out_bdt,2); ?></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</fieldset>
			</div>
		<?
		}
	}
	else if($reportType==2)
	{
		if($template==1)
		{
			ob_start();
		?>
			<div style="width:2500px;">
				<fieldset style="width:100%;">	 
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
						   <td colspan="22" align="center" width="100%" style="font-size:18px"><b>BTB Liability Coverage Report</b></td>
						</tr>
						<tr>
						   <td colspan="22" align="center" width="100%" style="font-size:18px"><b><? echo $company_library[$company_name]; ?></b></td>
						</tr>
						<tr><td colspan="22" height="15"></td></tr>
					</table>
					<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th width="40" rowspan="2">SL</th>
                                <th width="120" rowspan="2">File No.</th>
								<th width="120" rowspan="2">Buyer Name</th>
								<th width="120" rowspan="2">SC/LC No.</th>
								<th width="120" rowspan="2">Net L/C Value</th>
								<th width="120">BB LC Liability</th>
								<th width="120" rowspan="2">PC Drawn</th>
                                <th width="120" rowspan="2">PC Adjusted</th>
                                <th width="120" rowspan="2">PC Liability</th>
								<th colspan="6" width="700">Shipment Status</th>
                                <th rowspan="2" width="100">Margin Build</th>
								<th colspan="2" width="220">BTB Liability</th>
								<th width="200" colspan="2"></th>
								<th rowspan="2" width="120">Surplus (TK)</th>
                                <th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="120">Opening</th>
								<th width="120">Gross Bill Amount</th>
								<th width="120">Realized</th>
								<th width="120">Short realised</th>
								<th width="120">Unrealized</th>
								<th width="120">Net Ship Balance</th>
                                <th width="100">Bal. Commission</th>
								<th width="110">Adjustment</th>
								<th width="110">Liability Balance</th>
								<th width="100">Liability (TK)</th>
								<th width="100">Realized (TK)</th>
							</tr>
						</thead>
					</table>
					<div style="width:2500px; max-height:400px; overflow-y:scroll" id="dfc_liability">
						<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
						<?
							$internal_file_no_arr=array(); $lc_arr=array(); $sc_arr=array(); $lc_claim_comn_arr=array(); $sc_claim_comn_arr=array(); 
							$i=1; $lc_ids=''; $sc_ids='';
							
							$sql="select a.internal_file_no, a.id as lc_sc_id, a.bank_file_no, a.export_lc_no as no, a.replacement_lc as repl_conv, a.lc_value as value, a.pay_term, a.tenor, a.last_shipment_date, a.remarks, a.foreign_comn, a.local_comn, a.lien_bank, a.currency_name, a.buyer_name, a.applicant_name, a.claim_adjustment, 0 as converted_from, 1 as type from com_export_lc a where a.beneficiary_name='$company_name' and a.lien_bank like '$lien_bank_id' and a.lc_year like '$file_year' and a.status_active=1 and a.is_deleted=0 $file_no_cond_lc 
								union all 
									select b.internal_file_no, b.id as lc_sc_id, b.bank_file_no, b.contract_no as no, b.convertible_to_lc as repl_conv, b.contract_value as value, b.pay_term, b.tenor, b.last_shipment_date, b.remarks, b.foreign_comn, b.local_comn, b.lien_bank, b.currency_name, b.buyer_name, b.applicant_name, b.claim_adjustment, b.converted_from, 2 as type from com_sales_contract b where b.beneficiary_name='$company_name' and b.lien_bank like '$lien_bank_id' and b.sc_year like '$file_year' and b.status_active=1 and b.is_deleted=0 $file_no_cond_sc order by internal_file_no, type ASC";
							//echo $sql;die;
							$nameArray=sql_select( $sql );
							foreach ($nameArray as $row)
							{
								$internal_file_no_arr[$row[csf('internal_file_no')]].=$row[csf('lc_sc_id')]."**".$row[csf('type')]."**".$row[csf('pay_term')]."**".$row[csf('lien_bank')]."**".$row[csf('buyer_name')]."**".$row[csf('applicant_name')]."**".$row[csf('currency_name')].",";
								if($row[csf('type')]==1)
								{
									$lc_ids.=$row[csf('lc_sc_id')].",";
									$lc_arr[$row[csf('lc_sc_id')]]=$row[csf('bank_file_no')]."**".$row[csf('no')]."**".$row[csf('repl_conv')]."**".$row[csf('value')]."**".$row[csf('pay_term')]."**".$row[csf('tenor')]."**".$row[csf('last_shipment_date')]."**".$row[csf('foreign_comn')]."**".$row[csf('lien_bank')]."**".$row[csf('currency_name')]."**".$row[csf('buyer_name')]."**".$row[csf('applicant_name')]."**".$row[csf('converted_from')]."**".$row[csf('claim_adjustment')]."**".$row[csf('remarks')];
									
									$lc_claim_comn_arr[$row[csf('lc_sc_id')]]['foreign_comn']=$row[csf('foreign_comn')];
									$lc_claim_comn_arr[$row[csf('lc_sc_id')]]['local_comn']=$row[csf('local_comn')];
									$lc_claim_comn_arr[$row[csf('lc_sc_id')]]['clad']=$row[csf('claim_adjustment')];
								}
								else
								{
									$sc_ids.=$row[csf('lc_sc_id')].",";
									$sc_arr[$row[csf('lc_sc_id')]]=$row[csf('bank_file_no')]."**".$row[csf('no')]."**".$row[csf('repl_conv')]."**".$row[csf('value')]."**".$row[csf('pay_term')]."**".$row[csf('tenor')]."**".$row[csf('last_shipment_date')]."**".$row[csf('foreign_comn')]."**".$row[csf('lien_bank')]."**".$row[csf('currency_name')]."**".$row[csf('buyer_name')]."**".$row[csf('applicant_name')]."**".$row[csf('converted_from')]."**".$row[csf('claim_adjustment')]."**".$row[csf('remarks')];
									
									$sc_claim_comn_arr[$row[csf('lc_sc_id')]]['foreign_comn']=$row[csf('foreign_comn')];
									$sc_claim_comn_arr[$row[csf('lc_sc_id')]]['local_comn']=$row[csf('local_comn')];
									$sc_claim_comn_arr[$row[csf('lc_sc_id')]]['clad']=$row[csf('claim_adjustment')];
								}
							}
							
							$lc_ids=chop($lc_ids,','); $sc_ids=chop($sc_ids,','); 
							$lc_order_arr=array(); $sc_order_arr=array(); $btb_lc_data_arr=array(); $btb_sc_data_arr=array(); 
							
							$net_inv_val_arr=array(); $sc_inv_id_arr=array();
							//echo $lc_ids."**".$sc_ids;die;
							
							
							if($lc_ids!="")
							{
								$invoiceData=sql_select("select id, is_lc, lc_sc_id, net_invo_value, (discount_ammount+bonus_ammount+claim_ammount+commission) as deduct_amnt from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 and is_lc=1 and lc_sc_id in($lc_ids)");
								foreach($invoiceData as $invRow)
								{
									$net_inv_val_arr[$invRow[csf('is_lc')]][$invRow[csf('lc_sc_id')]]['net_invo_value']+=$invRow[csf('net_invo_value')];
									$net_inv_val_arr[$invRow[csf('is_lc')]][$invRow[csf('lc_sc_id')]]['deduct_amnt']+=$invRow[csf('deduct_amnt')];
								}
								
								$sql_lc="select b.com_export_lc_id, b.attached_value, b.attached_qnty, b.attached_rate, c.id as po_id, c.job_no_mst from com_export_lc_order_info b, wo_po_break_down c where b.wo_po_break_down_id=c.id and b.com_export_lc_id in($lc_ids) and b.status_active=1 and b.is_deleted=0";
								
								
								$dataLc=sql_select( $sql_lc );
								foreach ($dataLc as $rowL)
								{
									$lc_order_arr[$rowL[csf('com_export_lc_id')]].=$rowL[csf('attached_value')]."**".$rowL[csf('attached_qnty')]."**".$rowL[csf('attached_rate')]."**".$rowL[csf('po_id')]."**".$rowL[csf('job_no_mst')].",";
								}
								
								 $sql_opened_lc="select b.lc_sc_id, b.current_distribution as lc_value, c.id, c.lc_value as lc_value2, c.payterm_id from com_btb_export_lc_attachment b, com_btb_lc_master_details c where b.is_lc_sc=0 and b.import_mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.lc_sc_id in($lc_ids) group by b.lc_sc_id, c.id, c.lc_value, c.payterm_id,b.current_distribution"; 
								 
								
								$res_open_lc=sql_select($sql_opened_lc);
								foreach($res_open_lc as $row_open_lc)
								{
									$btb_lc_data_arr[$row_open_lc[csf('lc_sc_id')]].=$row_open_lc[csf('id')]."**".$row_open_lc[csf('lc_value')]."**".$row_open_lc[csf('payterm_id')].",";
								}
								
								$pc_drawn_lc_arr=return_library_array( "select lc_sc_id, sum(amount) as amount from com_pre_export_lc_wise_dtls where export_type=1 and lc_sc_id in($lc_ids) group by lc_sc_id","lc_sc_id","amount");
								
								if($db_type==0)
								{
									$subm_id_lc_arr=return_library_array( "select lc_sc_id, group_concat(doc_submission_mst_id) as sub_id from com_export_doc_submission_invo where is_lc=1 and lc_sc_id in($lc_ids) group by lc_sc_id","lc_sc_id","sub_id");
								}
								else
								{
									$subm_id_lc_arr=return_library_array( "select lc_sc_id, LISTAGG(doc_submission_mst_id, ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as sub_id from com_export_doc_submission_invo where is_lc=1 and lc_sc_id in($lc_ids) group by lc_sc_id","lc_sc_id","sub_id");
								}
								
								$doc_inv_lc_arr=return_library_array( "select b.lc_sc_id, sum(c.current_invoice_value) as current_invoice_value from com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where b.is_lc=1 and b.id=c.mst_id and b.lc_sc_id in($lc_ids) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.lc_sc_id","lc_sc_id","current_invoice_value");
							}
							
							if($sc_ids!="")
							{
								$invoiceData=sql_select("select id, is_lc, lc_sc_id, net_invo_value, (discount_ammount+bonus_ammount+claim_ammount+commission) as deduct_amnt from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 and is_lc=2 and lc_sc_id in($sc_ids)");
								foreach($invoiceData as $invRow)
								{
									$net_inv_val_arr[$invRow[csf('is_lc')]][$invRow[csf('lc_sc_id')]]['net_invo_value']+=$invRow[csf('net_invo_value')];
									$net_inv_val_arr[$invRow[csf('is_lc')]][$invRow[csf('lc_sc_id')]]['deduct_amnt']+=$invRow[csf('deduct_amnt')];
									$sc_inv_id_arr[$invRow[csf('lc_sc_id')]].=$invRow[csf('id')].",";
								}
								
								$sql_sc="select b.com_sales_contract_id, b.attached_value, b.attached_qnty, b.attached_rate, c.id as po_id, c.job_no_mst from com_sales_contract_order_info b, wo_po_break_down c where b.wo_po_break_down_id=c.id and b.com_sales_contract_id in($sc_ids) and b.status_active=1 and b.is_deleted=0";
								
								
								$dataSc=sql_select( $sql_sc );
								foreach ($dataSc as $rowS)
								{
									$sc_order_arr[$rowS[csf('com_sales_contract_id')]].=$rowS[csf('attached_value')]."**".$rowS[csf('attached_qnty')]."**".$rowS[csf('attached_rate')]."**".$rowS[csf('po_id')]."**".$rowS[csf('job_no_mst')].",";
								}
								
								$sql_opened_sc="select b.lc_sc_id, b.current_distribution as lc_value, c.id, c.lc_value as lc_value2, c.payterm_id from com_btb_export_lc_attachment b, com_btb_lc_master_details c where b.is_lc_sc=1 and b.import_mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.lc_sc_id in($sc_ids) group by b.lc_sc_id, c.id, c.lc_value, c.payterm_id,b.current_distribution"; 
								
								 
								$res_open_sc=sql_select($sql_opened_sc);
								foreach($res_open_sc as $row_open_sc)
								{
									$btb_sc_data_arr[$row_open_sc[csf('lc_sc_id')]].=$row_open_sc[csf('id')]."**".$row_open_sc[csf('lc_value')]."**".$row_open_sc[csf('payterm_id')].",";
								}
								
								$pc_drawn_sc_arr=return_library_array( "select lc_sc_id, sum(amount) as amount from com_pre_export_lc_wise_dtls where export_type=2 and lc_sc_id in($sc_ids) group by lc_sc_id","lc_sc_id","amount");
								
								if($db_type==0)
								{
									$subm_id_sc_arr=return_library_array( "select lc_sc_id, group_concat(doc_submission_mst_id) as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in($sc_ids) group by lc_sc_id","lc_sc_id","sub_id");
								}
								else
								{
									$subm_id_sc_arr=return_library_array( "select lc_sc_id, LISTAGG(doc_submission_mst_id, ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as sub_id from com_export_doc_submission_invo where is_lc=2 and lc_sc_id in($sc_ids) group by lc_sc_id","lc_sc_id","sub_id");
								}
								
								$doc_inv_sc_arr=return_library_array( "select b.lc_sc_id, sum(c.current_invoice_value) as current_invoice_value from com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where b.is_lc=2 and b.id=c.mst_id and b.lc_sc_id in($sc_ids) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.lc_sc_id","lc_sc_id","current_invoice_value");
							}
							
							
							
							
							$doc_sub_arr=array();
							$doc_sub_data=sql_select("select doc_submission_mst_id,
														sum(CASE WHEN acc_head=20 THEN dom_curr END) AS packing,
														sum(CASE WHEN acc_head=5 THEN lc_sc_curr END) AS margin_build
														from com_export_doc_sub_trans where status_active=1 and is_deleted=0 group by doc_submission_mst_id");
							foreach($doc_sub_data as $rowD)
							{
								$doc_sub_arr[$rowD[csf('doc_submission_mst_id')]][20]=$rowD[csf('packing')];
								$doc_sub_arr[$rowD[csf('doc_submission_mst_id')]][5]=$rowD[csf('margin_build')];
							}
							
							$proc_real_arr=array();
							$proc_real_data=sql_select("select d.is_invoice_bill, d.invoice_bill_id,
														sum(CASE WHEN e.account_head=20 THEN e.domestic_currency END) AS packing,
														sum(CASE WHEN e.account_head=5 THEN e.document_currency END) AS margin_build,
														sum(CASE WHEN e.type=0 THEN e.document_currency END) AS realized,
														sum(CASE WHEN e.type=1 THEN e.document_currency END) AS shortrealized
														from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.is_invoice_bill, d.invoice_bill_id");
							foreach($proc_real_data as $rowP)
							{
								$proc_real_arr[$rowP[csf('is_invoice_bill')]][$rowP[csf('invoice_bill_id')]]['packing']=$rowP[csf('packing')];
								$proc_real_arr[$rowP[csf('is_invoice_bill')]][$rowP[csf('invoice_bill_id')]]['margin_build']=$rowP[csf('margin_build')];
								$proc_real_arr[$rowP[csf('is_invoice_bill')]][$rowP[csf('invoice_bill_id')]]['shortrealized']=$rowP[csf('shortrealized')];
								$proc_real_arr[$rowP[csf('is_invoice_bill')]][$rowP[csf('invoice_bill_id')]]['realized']=$rowP[csf('realized')];
							}	
							
							$btb_paid_arr=array();
							$paid_sql="select a.btb_lc_id, b.adj_source, sum(b.accepted_ammount) as accepted_ammount from com_import_invoice_mst a, com_import_payment b where a.id=b.invoice_id and a.is_lc=1 and b.payment_head=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.btb_lc_id, b.adj_source";
							
							
							$paidArray=sql_select( $paid_sql );
							foreach($paidArray as $row_paid)
							{
								$btb_paid_arr[$row_paid[csf('btb_lc_id')]][$row_paid[csf('adj_source')]]=$row_paid[csf('accepted_ammount')];
							}	
							
							$paidAdjArr=array();
							$paid_sql_adj="select a.btb_lc_id, 
														sum(case when a.retire_source=5 then b.current_acceptance_value else 0 end) as paid_val,
														sum(case when a.retire_source!=5 then b.current_acceptance_value else 0 end) as paid_adj_val
														from com_import_invoice_mst a, com_import_invoice_dtls b where a.id=b.import_invoice_id and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.btb_lc_id";
														
															
							$paidArrayAdj=sql_select($paid_sql_adj);
							foreach($paidArrayAdj as $rowAdj)
							{
								$paidAdjArr[$rowAdj[csf('btb_lc_id')]]['paid_val']=$rowAdj[csf('paid_val')];
								$paidAdjArr[$rowAdj[csf('btb_lc_id')]]['paid_adj_val']=$rowAdj[csf('paid_adj_val')];
							}	
							
							//print_r($internal_file_no_arr);
							foreach ($internal_file_no_arr as $internal_file_no=>$data)
							{
								$s=1; $foreign_comn=0; $local_comm=0; $local_comm_per=0; $carring_cost=0; $foreign_comn_cost=0; $foreign_comm_per=0; $file_deduction=0; $file_deduction_per=0; $net_lc_value=0; $bbb_lc_entitle=0; $btb_opened_val=0; $fund_available=0; $bblc_per=0; $pc_limit=0; $pc_drawn=0; $fund_available_packing=0; $pc_adjusted=0; $out_bdt=0; $margin_build=0; $gross_bill_amnt=0; $net_bill_amnt=0; $realized_val=0; $unrealized_val=0; $short_realized=0; $ship_balance=0; $pc_liability_per=0; $paid_val=0; $balance=0; $btb_liability=0; $btb_liability_adjust=0; $liability_reso_per=0; $cost_heads_fabric_order=0; $cost_heads_embellish_order=0; $trims_cost=0; $yarn_cost=0; $att_total_value=0; $deduc_on_claim_adjustment=0; $cost_heads_knit_purchase=0; $cost_heads_woven_purc=0; $invoice_id_sc_of_cash_in_advance=array();
								
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$lc_id=''; $sc_id=''; $sc_id_of_cash_in_advance='';
								$lcScData=explode(",",chop($data,','));
								foreach($lcScData as $value)
								{
									$lc_sc=explode("**",$value);
									$lc_sc_id=$lc_sc[0];
									$type=$lc_sc[1];
									$pay_term_id=$lc_sc[2];
									$lien_bankId=$lc_sc[3];
									$buyer_name=$lc_sc[4];
									$applicant_name=$lc_sc[5];
									$currency_name=$lc_sc[6];
									
									if($type==1)
									{
										$lcScOrArray=$lc_order_arr[$lc_sc_id];
										$foreign_comn_lc_sc=$lc_claim_comn_arr[$lc_sc_id]['foreign_comn'];
										$local_comn_lc_sc=$lc_claim_comn_arr[$lc_sc_id]['local_comn'];
										$claim_adjustment=$lc_claim_comn_arr[$lc_sc_id]['clad'];
										$deduc_on_claim_adjustment+=$claim_adjustment;
									}
									else
									{
										$lcScOrArray=$sc_order_arr[$lc_sc_id];
										$foreign_comn_lc_sc=$sc_claim_comn_arr[$lc_sc_id]['foreign_comn'];
										$local_comn_lc_sc=$sc_claim_comn_arr[$lc_sc_id]['local_comn'];
										$claim_adjustment=$sc_claim_comn_arr[$lc_sc_id]['clad'];
										$deduc_on_claim_adjustment+=$claim_adjustment;
									}
									
									$lcScOrArray=explode(",",chop($lcScOrArray,','));
									foreach($lcScOrArray as $val)
									{
										$lcSc_Data=explode("**",chop($val,','));
										$attached_value=$lcSc_Data[0];
										$attached_qnty=$lcSc_Data[1];
										$attached_rate=$lcSc_Data[2];
										$po_id=$lcSc_Data[3];
										$job_no_mst=$lcSc_Data[4];
										
										$att_total_value+=$attached_value;
										$costing_per_id=$fabriccostArray[$job_no_mst]['cpi'];
										$ratio=$ratio_arr[$job_no_mst];
										$dzn_qnty=0;
										if($costing_per_id==1) $dzn_qnty=12;
										else if($costing_per_id==3) $dzn_qnty=12*2;
										else if($costing_per_id==4) $dzn_qnty=12*3;
										else if($costing_per_id==5) $dzn_qnty=12*4;
										else $dzn_qnty=1;
										//$dzn_qnty=$dzn_qnty*$ratio;
										
										$freight=$fabriccostArray[$job_no_mst]['freight'];
										$comm_cost=$fabriccostArray[$job_no_mst]['comm_cost'];
										
										$freight_cost=($attached_qnty/$dzn_qnty)*$freight;
										$commercial_cost=($attached_qnty/$dzn_qnty)*$comm_cost;
										
										$carring_cost+=$freight_cost+$commercial_cost;
										
										$trimscost=$trimscostArray[$job_no_mst][$po_id];
										$trims_cost+=($attached_qnty/$dzn_qnty)*$trimscost;
										
										$yarn_cons_cost=$yarn_cons_cost_arr[$job_no_mst]['amount'];
										$yarn_cost+=($attached_qnty/$dzn_qnty)*$yarn_cons_cost;
										//echo ($attached_qnty/$dzn_qnty)*$yarn_cons_cost." PO- ".$po_id."<br>";
										$l_cost=$l_cost_arr[$job_no_mst];
										//$local_comm+=($attached_qnty/$dzn_qnty)*$l_cost;
										
										if($foreign_comn==0 && $foreign_comn_lc_sc>0)
										{ 
											$foreign_comn=$foreign_comn_lc_sc;
										}
										
										if($local_comm==0 && $local_comn_lc_sc>0)
										{ 
											$local_comm=$local_comn_lc_sc;
										}

										//$cost_heads_fabric=explode(",",$cost_heads_fabric_array[$job_company_library[$job_no_mst]]);
										//print_r($cost_heads_fabric);
										/*foreach($cost_heads_fabric as $costId)
										{
											if($costId==30)
											{
												$costIds=array(30);
											}
											else if($costId==35)
											{
												$costIds=array(35,36,37,40);
											}
											else if($costId==1)
											{
												$costIds=array(1,3,4,134);
											}
											else
											{
												$costIds=array(25,26,31,33,39,101,61,70,137,34,32,65,136,67,68,69,71,94,82,89,138,135,123,132,60,62,63,66,72,73,74,75,77,78,79,80,81,83,84,85,86,87,88,90,91,92,93,128,129,133);
											}
											$fabric_cost=0;
											foreach($costIds as $id)
											{
												$fabric_cost+=$convcostArray[$job_no_mst][$id];
											}
											$cost_heads_fabric_order+=($attached_qnty/$dzn_qnty)*$fabric_cost;
										}
										
										$cost_heads_embellish=explode(",",$cost_heads_embellish_array[$job_company_library[$job_no_mst]]);
										foreach($cost_heads_embellish as $embellId)
										{
											$embellish_cost=$emblcostArray[$job_no_mst][$embellId];
											$cost_heads_embellish_order+=($attached_qnty/$dzn_qnty)*$embellish_cost;
										}
										
										if($cost_details[$job_company_library[$job_no_mst]][75]==1)
										{
											$knit_purchase_cost=$woven_knit_purchase_cost_arr[$job_no_mst]['knit'];
											$cost_heads_knit_purchase+=($attached_qnty/$dzn_qnty)*$knit_purchase_cost;
										}
										
										if($cost_details[$job_company_library[$job_no_mst]][78]==1)
										{
											$woven_purchase_cost=$woven_knit_purchase_cost_arr[$job_no_mst]['woven'];
											$cost_heads_woven_purc+=($attached_qnty/$dzn_qnty)*$woven_purchase_cost;
										}*/
									}

									if($type==1)
									{
										if($lc_id=="") $lc_id=$lc_sc_id; else $lc_id.=",".$lc_sc_id;
									}
									else 
									{
										if($pay_term_id==3)
										{
											$invoice_id=chop($sc_inv_id_arr[$lc_sc_id],',');
											if($invoice_id!="")
											{
												$invoice_id_sc_of_cash_in_advance[$invoice_id]=$invoice_id; 
											}
										}
										
										if($sc_id=="") $sc_id=$lc_sc_id; else $sc_id.=",".$lc_sc_id;
									}
								}
								
								//$foreign_comn_cost=($att_total_value*$foreign_comn)/100;
								//$commission=$local_comm+$foreign_comn_cost;
								
								$btb_lc_id_array=array(); $btb_lc_id_atSight_array=array(); $doc_sub_sc_packing=0; $doc_sub_sc_margin_build=0; 
								$proc_real_sc_packing=0; $proc_real_sc_margin_build=0; $proc_real_sc_realized=0; $proc_real_sc_shortrealized=0;
								$proc_real_sc_packing_invoice=0; $proc_real_sc_margin_build_invoice=0; $proc_real_sc_realized_invoice=0; $proc_real_sc_shortrealized_invoice=0;
								$doc_sub_lc_packing=0; $doc_sub_lc_margin_build=0; $proc_real_lc_packing=0; $proc_real_lc_margin_build=0; $proc_real_lc_realized=0; 
								$proc_real_lc_shortrealized=0; $deduc_on_lc=0; $deduc_on_sc=0; $net_bill_amnt=0; $pc_drawn_lc=0; $pc_drawn_sc=0; $bill_id_lc=''; $bill_id_sc='';
								$n=0; $opened_lc=0; $opened_sc=0; $doc_inv_lc=0; $doc_inv_sc=0; $btbPaidArr=array();
								$lcIdArr=explode(",",$lc_id);
								foreach($lcIdArr as $lcId)
								{
									$net_bill_amnt+=$net_inv_val_arr[1][$lcId]['net_invo_value'];
									$deduc_on_lc+=$net_inv_val_arr[1][$lcId]['deduct_amnt'];
									$pc_drawn_lc+=$pc_drawn_lc_arr[$lcId];
									$bill_id_lc.=$subm_id_lc_arr[$lcId].",";
									$doc_inv_lc+=$doc_inv_lc_arr[$lcId];
									
									$btb_lc_data=explode(",",chop($btb_lc_data_arr[$lcId],','));
									
									//print_r($lcId); die;
									
									foreach($btb_lc_data as $btb_data)
									{
										$btb=explode("**",$btb_data);
										$btbId=$btb[0];
										$btbVal=$btb[1];
										$paytermId=$btb[2];
										
										$opened_lc+=$btbVal;

										if($paytermId==1)
										{
											$btb_lc_id_atSight_array[$n]=$btbId;
											if(!in_array($btbId,$btbPaidArr))
											{
												foreach($paidAdjArr[$btbId] as $adj_source=>$amnt)
												{
													if($adj_source=="paid_val")
													{
														$paid_val+=$amnt;
													}
													else
													{
														$btb_liability_adjust+=$amnt;
													}
												}
												$btbPaidArr[]=$btbId;
											}
										}
										else
										{
											if(!in_array($btbId,$btbPaidArr))
											{
												foreach($btb_paid_arr[$btbId] as $adj_source=>$amnt)
												{
													if($adj_source==5)
													{
														$paid_val+=$amnt;
													}
													else
													{
														$btb_liability_adjust+=$amnt;
													}
												}
												$btbPaidArr[]=$btbId;
											}
											$btb_lc_id_array[$n]=$btbId;
										}
										
										$n++;
									}
								}
								
								$bill_id_lc=array_unique(explode(",",chop($bill_id_lc,',')));
								foreach($bill_id_lc as $billId)
								{
									$doc_sub_lc_packing+=$doc_sub_arr[$billId][20]; 
									$doc_sub_lc_margin_build+=$doc_sub_arr[$billId][5];
									
									$proc_real_lc_packing+=$proc_real_arr[1][$billId]['packing']; 
									$proc_real_lc_margin_build+=$proc_real_arr[1][$billId]['margin_build']; 
									$proc_real_lc_realized+=$proc_real_arr[1][$billId]['shortrealized']; 
									$proc_real_lc_shortrealized+=$proc_real_arr[1][$billId]['realized']; 
								}
								
								$bill_id_lc=implode(",",$bill_id_lc);
								
								$scIdArr=explode(",",$sc_id);
								foreach($scIdArr as $scId)
								{
									$net_bill_amnt+=$net_inv_val_arr[2][$scId]['net_invo_value'];
									$deduc_on_sc+=$net_inv_val_arr[2][$scId]['deduct_amnt'];
									$pc_drawn_sc+=$pc_drawn_sc_arr[$scId];
									$bill_id_sc.=$subm_id_sc_arr[$scId].",";
									$doc_inv_sc+=$doc_inv_sc_arr[$scId];
									
									$btb_sc_data=explode(",",chop($btb_sc_data_arr[$scId],','));
									foreach($btb_sc_data as $btb_data)
									{
										$btb=explode("**",$btb_data);
										$btbId=$btb[0];
										$btbVal=$btb[1];
										$paytermId=$btb[2];
										
										if($paytermId==1)
										{
											if(!in_array($btbId,$btb_lc_id_array))
											{
												$opened_sc+=$btbVal;
												$btb_lc_id_array[$n]=$btbId;
											}
											if(!in_array($btbId,$btb_lc_id_atSight_array))
											{
												$btb_lc_id_atSight_array[$n]=$btbId;
												foreach($paidAdjArr[$btbId] as $adj_source=>$amnt)
												{
													if($adj_source=="paid_val")
													{
														$paid_val+=$amnt;
													}
													else
													{
														$btb_liability_adjust+=$amnt;
													}
												}
											}
										}
										else
										{
											if(!in_array($btbId,$btb_lc_id_array))
											{
												$opened_sc+=$btbVal;
												$btb_lc_id_array[$n]=$btbId;
												foreach($btb_paid_arr[$btbId] as $adj_source=>$amnt)
												{
													if($adj_source==5)
													{
														$paid_val+=$amnt;
													}
													else
													{
														$btb_liability_adjust+=$amnt;
													}
												}	
											}
										}
										$n++;
									}

								}
								
								$bill_id_sc=array_unique(explode(",",chop($bill_id_sc,',')));
								foreach($bill_id_sc as $billId)
								{
									$doc_sub_lc_packing+=$doc_sub_arr[$billId][20]; 
									$doc_sub_lc_margin_build+=$doc_sub_arr[$billId][5];
									
									$proc_real_lc_packing+=$proc_real_arr[1][$billId]['packing']; 
									$proc_real_lc_margin_build+=$proc_real_arr[1][$billId]['margin_build']; 
									$proc_real_lc_realized+=$proc_real_arr[1][$billId]['shortrealized']; 
									$proc_real_lc_shortrealized+=$proc_real_arr[1][$billId]['realized']; 
								}
								$bill_id_sc=implode(",",$bill_id_sc);
								
								foreach($invoice_id_sc_of_cash_in_advance as $invoId)
								{
									$proc_real_sc_packing_invoice+=$proc_real_arr[2][$invoId]['packing']; 
									$proc_real_sc_margin_build_invoice+=$proc_real_arr[2][$invoId]['margin_build']; 
									$proc_real_sc_realized_invoice+=$proc_real_arr[2][$invoId]['shortrealized']; 
									$proc_real_sc_shortrealized_invoice+=$proc_real_arr[2][$invoId]['realized']; 
								}
								
								$file_deduction=$deduc_on_claim_adjustment+$deduc_on_lc+$deduc_on_sc;
								$file_deduction_per=($file_deduction/$att_total_value)*100;
								$net_lc_value=$att_total_value-$foreign_comn_cost-$local_comm-$carring_cost-$file_deduction;
								
								$btb_opened_val=$opened_lc+$opened_sc;
								
								$fund_available=$bbb_lc_entitle-$btb_opened_val;
								$bblc_per=($btb_opened_val/$net_lc_value)*100;
								
								if($conversion_details[$currency_name]=="") $conversion_rate=1; 
								else $conversion_rate=$conversion_details[$currency_name];
					
								$pc_limit=(($net_lc_value*$percent_details[$lien_bankId][$company_name])/100)*$conversion_rate;
								$pc_drawn=$pc_drawn_lc+$pc_drawn_sc;
								$fund_available_packing=$pc_limit-$pc_drawn;
								
								$pc_adjusted=$doc_sub_lc_packing+$doc_sub_sc_packing+$proc_real_lc_packing+$proc_real_sc_packing+$proc_real_sc_packing_invoice;
								$margin_build=$doc_sub_lc_margin_build+$doc_sub_sc_margin_build+$proc_real_lc_margin_build+$proc_real_sc_margin_build+$proc_real_sc_shortrealized_invoice;
								
								$out_bdt=$pc_drawn-$pc_adjusted;
								$gross_bill_amnt=$doc_inv_lc+$doc_inv_sc;
								
								$realized_val=$proc_real_lc_realized+$proc_real_sc_realized+$proc_real_sc_realized_invoice; 
								$short_realized=$proc_real_lc_shortrealized+$proc_real_sc_shortrealized+$proc_real_sc_shortrealized_invoice+$deduc_on_lc+$deduc_on_sc; 
								$unrealized_val=$gross_bill_amnt-($realized_val+$short_realized);

								//$ship_balance=$att_total_value-$net_bill_amnt;
								$ship_balance=$att_total_value-$gross_bill_amnt;// has been changed as per fatuallah apperals requiremnts
								
								$pc_liability_per=(($out_bdt/$conversion_rate)/($unrealized_val+$ship_balance))*100;
								
								$balance=$margin_build-$paid_val;
								//$btb_liability=$btb_opened_val-($paid_val+$btb_liability_adjust);
								$btb_liability=$btb_opened_val-$margin_build;
								$bl=fn_number_format($unrealized_val+$ship_balance,2,'.','');
								$liability_reso_per=($btb_liability/($bl+$balance))*100;
								
								$commission=($ship_balance*($foreign_comn+$local_comm))/100;
								
								foreach($lcScData as $value)
								{
									$lc_sc=explode("**",$value);
									$lc_sc_id=$lc_sc[0];
									$type=$lc_sc[1];
									
									if($type==1)
									{
										$datas=$lc_arr[$lc_sc_id];
									}
									else
									{
										$datas=$sc_arr[$lc_sc_id];
									}
									
									$alldata=explode("**",$datas);
									$bank_file_no=$alldata[0];
									$no=$alldata[1];
									$repl_conv=$alldata[2];
									$value=$alldata[3];
									$pay_term_id=$alldata[4];
									$tenor=$alldata[5];
									$last_shipment_date=$alldata[6];
									$foreign_comn=$alldata[7];
									$lien_bank=$alldata[8];
									$currency_name=$alldata[9];
									$buyer_name=$alldata[10];
									$applicant_name=$alldata[11];
									$converted_from=$alldata[12];
									$claim_adjustment=$alldata[13];
									$remarks=$alldata[14];
									
									if($s==1)
									{
									?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                            <td width="40"><? echo $i; ?></td>
                                            <td width="120"><? echo $internal_file_no; ?></td>
                                            <td width="120"><p><? echo $buyer_details[$buyer_name];?></p></td>
                                            <?
											if($type==1)
											{
												$file_lc_amnt+=$value; 
												$tot_lc_amnt+=$value;
											?>
												<td width="120"><p>LC-<? echo $no; ?></p></td>
											<?
											}
											else
											{
												if($converted_from<=0)
												{
													$sc_val=$value;
													$file_sc_amnt+=$value; 
													$tot_sc_amnt+=$value;
													$suffix="";
												}
												else
												{
													$file_lc_amnt+=$value; 
													$tot_lc_amnt+=$value;
													$suffix=" (F)";
													$sc_val="";
												}
											?>
                                            	<td width="120"><p>SC-<? echo $no;?></p></td>
                                            <?
											}
											
											$tot_order_value+=$att_total_value; 
                                            $file_order_value+=$att_total_value; 
											
											$resource=$unrealized_val+$ship_balance-$commission;
											$bl_btb_liability=$btb_opened_val-$margin_build;
											$net_resource=$resource-$bl_btb_liability;
											$net_resource_tk=$net_resource*$conversion_details[2];
											$ar_val=$net_resource_tk-$out_bdt;
											?>
                                            <td width="120" align="right"><? echo fn_number_format($net_lc_value,2); $tot_net_lc_val+=$net_lc_value; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','btb_open_info','BTB Info');\">".fn_number_format($btb_opened_val,2)."</a>"; $tot_opening+=$btb_opened_val; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','pc_drawn_info','PC Drawn Info');\">".fn_number_format($pc_drawn,2)."</a>"; $tot_pc_drawn+=$pc_drawn; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($pc_adjusted,2); $tot_pc_adjust+=$pc_adjusted; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($out_bdt,2); $tot_out_bdt+=$out_bdt; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','gross_bill_info','Export Invoice Info');\">".fn_number_format($gross_bill_amnt,2)."</a>"; $grand_bill_amnt+=$gross_bill_amnt; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','export_proceed_info','Export Proceed Realization Info');\">".fn_number_format($realized_val,2)."</a>"; $tot_realized+=$realized_val; ?></td>
                                            <td width="120" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','short_realized','Short Realization Info');\">".fn_number_format($short_realized,2)."</a>"; $tot_short_realized+=$short_realized; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($unrealized_val,2); $tot_unrealized+=$unrealized_val; ?></td>
                                            <td width="120" align="right"><? echo fn_number_format($ship_balance,2); $tot_ship_bal+=$ship_balance; ?></td>
                                            <td width="100" align="right"><? echo fn_number_format($commission,2); $tot_commission+=$commission; ?></td>
                                            <td width="100" align="right"><? echo fn_number_format($margin_build,2); $tot_margin_build+=$margin_build; ?></td>
                                            <td width="110" align="right"><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage('".$internal_file_no."',$company_name,'$lien_bank_id','$file_year','dfc_paid_info_adjust','DFC Paid Info');\">".fn_number_format($btb_liability_adjust,2)."</a>"; $tot_btb_liability_adjust+=$btb_liability_adjust; ?></td>
                                            <td width="110" align="right" title="<? echo "(btb opened value-Margin Build)"; ?>"><? echo fn_number_format($btb_liability,2); $tot_btb_liability+=$btb_liability; ?></td>
                                            <td width="100" align="right"><? $btb_lib_tk=($btb_liability*$conversion_details[2]); echo fn_number_format($btb_lib_tk,2); $total_btb_liabilit += ($btb_liability*$conversion_details[2]); ?></td>
                                            <td width="100" align="right"><? echo fn_number_format(($realized_val*$conversion_details[2]),2); $total_realized_val +=($realized_val*$conversion_details[2]); ?></td>
                                            <td width="120" align="right" title="<? echo "((Unrealized Value+Ship balance-Commission)*Rate-(PC Liability))"; ?>"><p><? echo fn_number_format($ar_val,2); $total_ar_val+=$ar_val; ?></p></td>
                                            <td><p><? echo $remarks; ?>&nbsp;</p></td>
                                        </tr>
									<?
									}
									else
									{
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i.'_'.$s; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i.'_'.$s; ?>">
											<td width="40">&nbsp;</td>
											<td width="120">&nbsp;</td>
                                            <td width="120">&nbsp;</td>
                                            <?
											if($type==1)
											{
											?>
												<td width="120"><p>LC-<? echo $no; ?></p></td>
											<?
											}
											else
											{
												if($converted_from<=0)
												{
													$sc_val=$value;
													$file_sc_amnt+=$value; 
													$tot_sc_amnt+=$value;
													$suffix="";
												}
												else
												{
													$file_lc_amnt+=$value; 
													$tot_lc_amnt+=$value;
													$suffix=" (F)";
													$sc_val="";
												}
											?>
                                            	<td width="120"><p>SC-<? echo $no;?></p></td>
                                            <?
											}
											?>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
                                            <td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
                                            <td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="110">&nbsp;</td>
											<td width="110">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="100">&nbsp;</td>
                                            <td width="120">&nbsp;</td>
											<td><p><? echo $remarks; ?>&nbsp;</p></td>
										</tr>
									<?
									}
								$s++;
								}
								$i++;
							}
							?>
							<tfoot>
								<th colspan="4" align="right">Grand Total</th>
								<th align="right"><? echo fn_number_format($tot_net_lc_val,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_opening,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_pc_drawn,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_pc_drawn_adjusted,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_pc_liability,2); ?></th>
								<th align="right"><? echo fn_number_format($grand_bill_amnt,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_realized,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_short_realized,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_unrealized,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_ship_bal,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_commission,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_margin_build,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_btb_liability_adjust,2); ?></th>
								<th align="right"><? echo fn_number_format($tot_btb_liability,2); ?></th>
								<th align="right"><? echo fn_number_format($total_btb_liabilit,2); ?></th>
								<th align="right"><? echo fn_number_format($total_realized_val,2); ?></th>
                                <th align="right"><? echo fn_number_format($total_ar_val,2); ?></th>
								<th>&nbsp;</th>
							</tfoot>
						</table>
					</div>
					<br />
					<div align="left" style="display:none">
						<b>Summary</b>
						<table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th width="130">Unrealized Proceeds</th>
									<th width="130">Shipment Balance</th>
                                    <th width="130">Commission</th>
									<th width="130">DFC Balance</th>
									<th width="130">Total Resource</th>
									<th width="130">BTB Liability</th>
									<th width="120">BTB Liability  %</th>
									<th>PC Liability (BDT)</th>
								</tr>
							</thead>
							<tfoot>
								<tr class="tbl_bottom">
									<td align="right"><? echo fn_number_format($tot_unrealized,2); ?></td>
									<td align="right"><? echo fn_number_format($tot_ship_bal,2); ?></td>
                                    <td align="right"><? echo fn_number_format($tot_commission,2); ?></td>
									<td align="right"><? echo fn_number_format($tot_balance,2); ?></td>
									<td align="right"><? $total_res=$tot_unrealized+$tot_ship_bal+$tot_balance-$tot_commission; echo fn_number_format($total_res,2); ?></td>
									<td align="right"><? echo fn_number_format($tot_btb_liability,2); ?></td>
									<td align="right"><? echo fn_number_format(($tot_btb_liability/fn_number_format($total_res,2,'.',''))*100,2); ?></td>
									<td align="right"><? echo fn_number_format($tot_out_bdt,2); ?></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</fieldset>
			</div>
		<?
		}
	}
	
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

if($action=="order_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:945px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	 <fieldset style="width:100%; margin-left:10px">
         <div id="report_container" align="center" style="width:100%"> 
             <div style="width:945px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="30">SL</th>
                        <th width="90">Order No.</th>
                        <th width="110">Job No.</th>
                        <th width="100">LC/SC No.</th>
                        <th width="100">Order Qnty</th>
                        <th width="50">UOM</th>
                        <th width="50">Unit Price</th>
                        <th width="110">Order Value</th>
                        <th width="105">Att. Value in LC</th>
                        <th width="105">Att. Value in SC</th>
                        <th>Shipment Date</th>
                    </thead>
                </table>
			</div>
			<div style="width:945px; overflow-y:scroll; max-height:230px;" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1;
                    $sql="(select a.export_lc_no as lc_or_sc, c.job_no_mst, c.po_number, c.shipment_date, c.po_quantity, c.unit_price, d.order_uom, b.attached_value as attached_value, 1 as type from com_export_lc a, com_export_lc_order_info b, wo_po_break_down c, wo_po_details_master d where a.id=b.com_export_lc_id and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0)
                    union all
                    (
                        select a.contract_no as lc_or_sc, c.job_no_mst, c.po_number, c.shipment_date, c.po_quantity, c.unit_price, d.order_uom, b.attached_value as attached_value, 2 as type from com_sales_contract a, com_sales_contract_order_info b, wo_po_break_down c, wo_po_details_master d where a.id=b.com_sales_contract_id and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                    )
                    ";
                    $result=sql_select($sql);
                    $total_order_qnty = 0; $total_order_amnt = 0;$total_att_lc_value=0; $total_att_sc_value=0;
                    foreach($result as $row) 
                    {
                        $total_order_qnty += $row[csf('po_quantity')];
                        $total_order_amnt += $row[csf('po_quantity')]*$row[csf('unit_price')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="110"><? echo $row[csf('job_no_mst')]; ?></td>
                        <td width="100">
                            <p>
                            <? 
                                if($row[csf('type')]==1)
                                {
                                    echo "LC-".$row[csf('lc_or_sc')];
                                }
                                else
                                {
                                    echo "SC-".$row[csf('lc_or_sc')];
                                }
                            ?>
                            </p>
                        </td>
                        <td width="100" align="right"><? echo fn_number_format($row[csf('po_quantity')],2); ?></td>
                        <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        <td width="50" align="right"><? echo fn_number_format($row[csf('unit_price')],2); ?>&nbsp;</td>
                        <td width="110" align="right"><? echo fn_number_format($row[csf('po_quantity')]*$row[csf('unit_price')],2); ?>&nbsp;</td>
                        <td width="105" align="right">
                            <?
                                if($row[csf('type')]==1)
                                { 
                                    echo fn_number_format($row[csf('attached_value')],2); 
                                     $total_att_lc_value += $row[csf('attached_value')];
                                }
                            ?>
                            &nbsp;
                        </td>
                        <td width="105" align="right">
                            <?
                                if($row[csf('type')]==2)
                                { 
                                    echo fn_number_format($row[csf('attached_value')],2);
                                    $total_att_sc_value+=$row[csf('attached_value')];
                                }
                            ?>
                            &nbsp;
                        </td>
                        <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                    </tr>
                    <?
                    $i++;
					}
                    ?>
                     <tfoot class="tbl_bottom">
                        <tr>
                            <td colspan="4" align="right">Total</td>
                            <td align="right"><? echo fn_number_format($total_order_qnty,2); ?></td>
                            <td></th>
                            <td></th>
                            <td align="right"><? echo fn_number_format($total_order_amnt,2); ?></td>
                            <td align="right"><? echo fn_number_format($total_att_lc_value,2); ?></td>
                            <td align="right"><? echo fn_number_format($total_att_sc_value,2); ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="8" align="right">Total Attached value in LC and SC</td>
                            <td colspan="2" align="right">
                                <?
                                    $tot_sum=$total_att_lc_value+$total_att_sc_value;
                                    echo fn_number_format($tot_sum,2);
                                ?>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
        	</div>
		</div>
	</fieldset>
</div>
<?
exit();
}

if($action=="btb_open_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:810px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />    	
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="130">BTB No.</th>
                        <th width="90">BTB Date</th>
                        <th width="140">Supplier</th>
                        <th width="140">Item Category</th>
                        <th width="120">BTB Value</th>
                        <th>Shipment Date</th>
                    </thead>
                </table>
           </div>
           <div style="width:810px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $btb_lc_id=array();
                    $sql_opened_lc="select c.id, c.lc_number, c.lc_date, c.supplier_id, c.pi_entry_form, c.lc_value as lc_value2, c.last_shipment_date, sum(b.current_distribution) as lc_value from com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c where  a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.lc_number, c.lc_date, c.supplier_id, c.pi_entry_form, c.lc_value, c.last_shipment_date";
                    $result=sql_select($sql_opened_lc);
                    $total_btb_amnt = 0;
					
					$categorywiseentryformArr=array_flip($category_wise_entry_form);
					
                    foreach($result as $row)  
                    {
                        $total_btb_amnt += $row[csf('lc_value')];
                        $btb_lc_id[]=$row[csf('id')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="130"><p><? echo $row[csf('lc_number')]; ?></p></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('lc_date')]); ?></td>
                        <td width="140"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="140"><? echo $item_category[$categorywiseentryformArr[$row[csf('pi_entry_form')]]];  ?></td>
                        <td width="120" align="right"><? echo fn_number_format($row[csf('lc_value')],2); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?></td>
                    </tr>
                    <?
                    $i++;
                    }
                    
                    $sql_opened_sc="select c.id, c.lc_number, c.lc_date, c.supplier_id, c.pi_entry_form, c.lc_value as lc_value2, c.last_shipment_date, sum(b.current_distribution) as lc_value from com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c where  a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.id, c.lc_number, c.lc_date, c.supplier_id, c.pi_entry_form, c.lc_value, c.last_shipment_date";
                    $result=sql_select($sql_opened_sc);
                    foreach($result as $row_sc)  
                    {
                        if(!in_array($row_sc[csf('id')],$btb_lc_id))
                        {
                            $total_btb_amnt += $row_sc[csf('lc_value')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                        
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="130"><p><? echo $row_sc[csf('lc_number')]; ?></p></td>
                                <td width="90" align="center"><? echo change_date_format($row_sc[csf('lc_date')]); ?></td>
                                <td width="140"><p><? echo $supplier_details[$row_sc[csf('supplier_id')]]; ?></p></td>
                                <td width="140"><? echo  $item_category[$categorywiseentryformArr[$row_sc[csf('pi_entry_form')]]] ;//$item_category[$row_sc[csf('item_category_id')]];  ?></td>
                                <td width="120" align="right"><? echo fn_number_format($row_sc[csf('lc_value')],2); ?></td>
                                <td align="center"><? echo change_date_format($row_sc[csf('last_shipment_date')]); ?></td>
                            </tr>
                            <?
                        $i++;
                        }
                    }
                    ?>
                     <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="5" align="right">Total</td>
                            <td align="right"><? echo fn_number_format($total_btb_amnt,2)?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}

if($action=="pc_drawn_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:710px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />    
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="180">PC No.</th>
                        <th width="100">PC Date</th>
                        <th width="180">LC/SC No.</th>
                        <th>PC Amount (BDT)</th>
                    </thead>
                </table>
            </div>
            <div style="width:710px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1;
					if($db_type==0)
					{
						$sql="(select a.export_lc_no as lc_or_sc, b.loan_number, c.loan_date, d.amount from com_export_lc a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c,  com_pre_export_lc_wise_dtls d where a.id=d.lc_sc_id and d.export_type=1 and d.pre_export_dtls_id=b.id and b.mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.export_lc_no, b.loan_number)
						union all
						(
							select a.contract_no as lc_or_sc, b.loan_number, c.loan_date, d.amount from com_sales_contract a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c,  com_pre_export_lc_wise_dtls d where a.id=d.lc_sc_id and d.export_type=2 and d.pre_export_dtls_id=b.id and b.mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.contract_no, b.loan_number
						)
						";
					}
					else
					{
						$sql="(select a.export_lc_no as lc_or_sc, b.loan_number, c.loan_date, d.amount from com_export_lc a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c,  com_pre_export_lc_wise_dtls d where a.id=d.lc_sc_id and d.export_type=1 and d.pre_export_dtls_id=b.id and b.mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0)
						union all
						(
							select a.contract_no as lc_or_sc, b.loan_number, c.loan_date, d.amount from com_sales_contract a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c,  com_pre_export_lc_wise_dtls d where a.id=d.lc_sc_id and d.export_type=2 and d.pre_export_dtls_id=b.id and b.mst_id=c.id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
						)
						order by lc_or_sc, loan_number";
					}
					//echo $sql;
                    $result=sql_select($sql);
                    $total_pc_drawn_amnt = 0;
                    foreach($result as $row)  
                    {
                        $total_pc_drawn_amnt += $row[csf('amount')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="180"><p><? echo $row[csf('loan_number')]; ?></p></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('loan_date')]); ?>&nbsp;</td>
                            <td width="180"><p><? echo $row[csf('lc_or_sc')]; ?></p></td>
                            <td align="right"><? echo fn_number_format($row[csf('amount')],2); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                     <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="4" align="right">Total</td>
                            <td align="right"><? echo fn_number_format($total_pc_drawn_amnt,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}

if($action=="gross_bill_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:800px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Invoice No.</th>
                        <th width="90">Invoice Date</th>
                        <th width="150">Buyer Name</th>
                        <th width="110">Invoice Qnty</th>
                        <th width="80">Rate</th>
                        <th>Invoice Value</th>
                    </thead>
                </table>
           </div>
           <div style="width:800px; overflow-y:scroll; max-height:280px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $total_value=0;
                    $sql_lc="select b.invoice_no, b.invoice_date, b.buyer_id, c.current_invoice_value, c.current_invoice_qnty, c.current_invoice_rate from com_export_lc a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where a.id=b.lc_sc_id and b.is_lc=1 and b.id=c.mst_id and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and c.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                    $result=sql_select($sql_lc);
                    foreach($result as $row)  
                    {
                        $total_value += $row[csf('current_invoice_value')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                        <td width="150"><p><? echo $buyer_details[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="110" align="right"><? echo fn_number_format($row[csf('current_invoice_qnty')],2); ?></td>
                        <td width="80" align="right"><? echo fn_number_format($row[csf('current_invoice_rate')],2); ?></td>
                        <td align="right"><? echo fn_number_format($row[csf('current_invoice_value')],2); ?></td>
                    </tr>
                    <?
                    $i++;
                    }
                    
                    $sql_sc="select b.invoice_no, b.invoice_date, b.buyer_id, c.current_invoice_value, c.current_invoice_qnty, c.current_invoice_rate from com_sales_contract a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where a.id=b.lc_sc_id and b.is_lc=2 and b.id=c.mst_id and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and c.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                    $result_sc=sql_select($sql_sc);
                    foreach($result_sc as $row_sc)  
                    {
                        $total_value += $row_sc[csf('current_invoice_value')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                    
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="150"><p><? echo $row_sc[csf('invoice_no')]; ?></p></td>
                            <td width="90" align="center"><? echo change_date_format($row_sc[csf('invoice_date')]); ?></td>
                            <td width="150"><p><? echo $buyer_details[$row_sc[csf('buyer_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo fn_number_format($row_sc[csf('current_invoice_qnty')],2); ?></td>
                            <td width="80" align="right"><? echo fn_number_format($row_sc[csf('current_invoice_rate')],2); ?></td>
                            <td align="right"><? echo fn_number_format($row_sc[csf('current_invoice_value')],2); ?></td>
                        </tr>
                        <?
                    $i++;
                    }
                    ?>
                     <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo fn_number_format($total_value,2); ?></th>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>    
</div>
<?
exit();
}

if($action=="net_bill_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:820px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:810px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Invoice No.</th>
                        <th width="90">Invoice Date</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Invoice Value</th>
                        <th width="90">Upcharge Value</th>
                        <th width="90">Deduct Value</th>
                        <th>Net Invoice Value</th>
                    </thead>
                </table>
           </div>
           <div style="width:810px; overflow-y:scroll; max-height:290px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $total_value=0; $total_upcharge=0; $total_deduct_value=0; $total_net_value=0;
                    if($db_type==0)
					{
						$sql_lc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value,b.upcharge, (IFNULL(b.discount_ammount,0)+IFNULL(b.bonus_ammount,0)+IFNULL(b.claim_ammount,0)+IFNULL(b.commission,0)+IFNULL(b.other_discount_amt,0)) as deduct_amnt from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					}
					else
					{
						$sql_lc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value,b.upcharge,  (nvl(b.discount_ammount,0)+nvl(b.bonus_ammount,0)+nvl(b.claim_ammount,0)+nvl(b.commission,0)+ nvl(b.other_discount_amt,0)) as deduct_amnt from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					}
                    $result=sql_select($sql_lc);
                    foreach($result as $row)  
                    {
                        $total_value += $row[csf('invoice_value')];
                        $total_upcharge += $row_sc[csf('upcharge')];
						$total_deduct_value += $row[csf('deduct_amnt')];
						$total_net_value += $row[csf('net_invo_value')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                        <td width="150"><p><? echo $buyer_details[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="100" align="right"><? echo fn_number_format($row[csf('invoice_value')],2); ?></td>
                        <td width="90" align="right"><? echo fn_number_format($row[csf('upcharge')],2); ?></td>
                        <td width="90" align="right"><? echo fn_number_format($row[csf('deduct_amnt')],2); ?></td>
                        <td align="right"><? echo fn_number_format($row[csf('net_invo_value')],2); ?></td>
                    </tr>
                    <?
                    $i++;
                    }
                    
                    if($db_type==0)
					{
						$sql_sc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value,b.upcharge, (IFNULL(b.discount_ammount,0)+IFNULL(b.bonus_ammount,0)+IFNULL(b.claim_ammount,0)+IFNULL(b.commission,0)+IFNULL(b.other_discount_amt,0)) as deduct_amnt from com_sales_contract a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=2 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					}
					else
					{
						$sql_sc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value,b.upcharge, (nvl(b.discount_ammount,0)+nvl(b.bonus_ammount,0)+nvl(b.claim_ammount,0)+nvl(b.commission,0)+ nvl(b.other_discount_amt,0)) as deduct_amnt from com_sales_contract a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=2 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					}
                    $result_sc=sql_select($sql_sc);
                    foreach($result_sc as $row_sc)  
                    {
                        $total_value += $row_sc[csf('invoice_value')];
                        $total_upcharge += $row_sc[csf('upcharge')];
						$total_deduct_value += $row_sc[csf('deduct_amnt')];
						$total_net_value += $row_sc[csf('net_invo_value')];
						
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="150"><p><? echo $row_sc[csf('invoice_no')]; ?></p></td>
                            <td width="90" align="center"><? echo change_date_format($row_sc[csf('invoice_date')]); ?></td>
                            <td width="150"><p><? echo $buyer_details[$row_sc[csf('buyer_id')]]; ?></p></td>
                            <td width="100" align="right"><? echo fn_number_format($row_sc[csf('invoice_value')],2); ?></td>
                            <td width="90" align="right"><? echo fn_number_format($row_sc[csf('upcharge')],2); ?></td>
                            <td width="90" align="right"><? echo fn_number_format($row_sc[csf('deduct_amnt')],2); ?></td>
                            <td align="right"><? echo fn_number_format($row_sc[csf('net_invo_value')],2); ?></td>
                        </tr>
                   	<?
                    	$i++;
                    }
                    ?>
                     <tfoot>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo fn_number_format($total_value,2); ?></th>
                        <th align="right"><? echo fn_number_format($total_upcharge,2); ?></th>
                        <th align="right"><? echo fn_number_format($total_deduct_value,2); ?></th>
                        <th align="right"><? echo fn_number_format($total_net_value,2); ?></th>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>    
</div>
<?
exit();
}

if($action=="export_proceed_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:710px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Bill / Invoice No.</th>
                        <th width="80">Bill/Invoice</th>
                        <th width="150">LC/SC No.</th>
                        <th width="100">Realization Date</th>
                        <th>Realized Amount</th>
                    </thead>
                </table>
            </div>
            <div style="width:710px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $total_realized_amnt = 0;
					if($db_type==0)
					{
						$bill_id_lc=return_field_value("group_concat(distinct(c.id)) as id","com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=1 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
					   
						$bill_id_sc=return_field_value("group_concat(distinct(c.id)) as id","com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=2 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						
						$invoice_id_sc=return_field_value("group_concat(distinct(b.id)) as id","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and a.id=b.lc_sc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.pay_term=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
					}
					else
					{
						$bill_id_lc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=1 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
					    $bill_id_lc=implode(",",array_unique(explode(",",$bill_id_lc)));
						
						$bill_id_sc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=2 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						$bill_id_sc=implode(",",array_unique(explode(",",$bill_id_sc)));
						
						$invoice_id_sc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and a.id=b.lc_sc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.pay_term=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
						$invoice_id_sc=implode(",",array_unique(explode(",",$invoice_id_sc)));
					}
                    if($bill_id_lc!="")
                    {
                        $sql_lc="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($bill_id_lc) and d.is_invoice_bill=1 and e.type=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id, d.invoice_bill_id, d.received_date";
                        $result_lc=sql_select($sql_lc);
                        
                        foreach($result_lc as $row) 
                        {
                            $total_realized_amnt += $row[csf('document_currency')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                                
                            $bill_no=return_field_value("bank_ref_no","com_export_doc_submission_mst","id=".$row[csf('invoice_bill_id')]);
                            $lc_no=return_field_value("a.export_lc_no as export_lc_no","com_export_lc a, com_export_doc_submission_invo b","a.id=b.lc_sc_id and b.is_lc=1 and b.doc_submission_mst_id=".$row[csf('invoice_bill_id')],"export_lc_no");
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $bill_no; ?></p></td>
                                <td width="80">Bill</td>
                                <td width="150"><p><? echo $lc_no; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row[csf('received_date')]); ?></td>
                                <td align="right"><? echo fn_number_format($row[csf('document_currency')],2); ?></td>
                            </tr>
                            <?
                        $i++;
                        }
                    }
                    
					if($bill_id_sc!="")
                    {
                       $sql_sc="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($bill_id_sc) and d.is_invoice_bill=1 and e.type=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id, d.invoice_bill_id, d.received_date";
                        $result_sc=sql_select($sql_sc);
                        foreach($result_sc as $row_sc) 
                        {
                            $total_realized_amnt += $row_sc[csf('document_currency')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            $invoice_no=return_field_value("bank_ref_no","com_export_doc_submission_mst","id=".$row_sc[csf('invoice_bill_id')]);	
                            $sc_no=return_field_value("a.contract_no as contract_no","com_sales_contract a, com_export_doc_submission_invo b","a.id=b.lc_sc_id and b.is_lc=2 and b.doc_submission_mst_id=".$row_sc[csf('invoice_bill_id')],"contract_no");
                            
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $invoice_no; ?>&nbsp;</p></td>
                                <td width="80">Bill</td>
                                <td width="150"><p><? echo $sc_no; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row_sc[csf('received_date')]); ?>&nbsp;</td>
                                <td align="right"><? echo fn_number_format($row_sc[csf('document_currency')],2); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
					
					if($invoice_id_sc!="")
                    {
                        $sql_sc="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($invoice_id_sc) and d.is_invoice_bill=2 and e.type=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id";
                        $result_sc=sql_select($sql_sc);
                        foreach($result_sc as $row_sc) 
                        {
                            $total_realized_amnt += $row_sc[csf('document_currency')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            $bill_no=return_field_value("invoice_no","com_export_invoice_ship_mst","id=".$row_sc[csf('invoice_bill_id')]);	
                            $sc_no=return_field_value("a.contract_no as contract_no","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and b.id=".$row_sc[csf('invoice_bill_id')],"contract_no");
                            
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $bill_no; ?></p></td>
                                <td width="80">Invoice</td>
                                <td width="150"><p><? echo $sc_no; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($row_sc[csf('received_date')]); ?></td>
                                <td align="right"><? echo fn_number_format($row_sc[csf('document_currency')],2); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                    ?>
                     <tfoot class="tbl_bottom">
                        <td colspan="5" align="right">Total</td>
                        <td align="right"><? echo fn_number_format($total_realized_amnt,2); ?></td>
                     </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}

if($action=="short_realized")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%;">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:800px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
               	<table style="margin-top:2px" class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                   <thead>
                        <tr>
                            <th colspan="9">Deduction at Export Invoice</th>
                        </tr>
                        <tr>
                            <th width="40">SL</th>
                            <th width="110">LC/SC No.</th>
                            <th width="110">Invoice No.</th>
                            <th width="80">Invoice Date</th>
                            <th width="80">Discount Amount</th>
                            <th width="80">Bonus Amount</th>
                            <th width="80">Claim Amount</th>
                            <th width="80">Commission</th>
                            <th>Total Deduct Amount</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div style="width:800px; overflow-y:scroll; max-height:175px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                	<? 
					$i=1; $tot_deduct_amnt=0;
					$sql="(select a.export_lc_no as sc_lc_no, b.is_lc, b.lc_sc_id, b.invoice_no, b.invoice_date, b.discount_ammount, b.bonus_ammount, b.claim_ammount, b.commission from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0)
					union all
					(
						select c.contract_no as sc_lc_no, d.is_lc, d.lc_sc_id, d.invoice_no, d.invoice_date, d.discount_ammount, d.bonus_ammount, d.claim_ammount, d.commission from com_sales_contract c, com_export_invoice_ship_mst d where c.id=d.lc_sc_id and d.is_lc=2 and c.beneficiary_name='$company_name' and c.internal_file_no='$file_no' and c.lien_bank like '$bank_id' and c.sc_year like '$text_year' and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					)
					";
					$result=sql_select($sql);
					foreach($result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						$deduct_amnt=$row[csf('discount_ammount')]+$row[csf('bonus_ammount')]+$row[csf('claim_ammount')]+$row[csf('commission')];
						$tot_deduct_amnt+=$deduct_amnt;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="110"><p><? echo $row[csf('sc_lc_no')]; ?></p></td>
							<td width="110"><p><? echo $row[csf('invoice_no')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
							<td width="80" align="right"><? echo fn_number_format($row[csf('discount_ammount')],2); ?></td>
							<td width="80" align="right"><? echo fn_number_format($row[csf('bonus_ammount')],2); ?></td>
							<td width="80" align="right"><? echo fn_number_format($row[csf('claim_ammount')],2); ?></td>
                            <td width="80" align="right"><? echo fn_number_format($row[csf('commission')],2); ?></td>
							<td align="right"><? echo fn_number_format($deduct_amnt,2); ?></td>
						</tr>
						<?
					$i++;	
					}
					?>
                    <tfoot class="tbl_bottom">
                        <td colspan="8" align="right">Total</td>
                        <td align="right"><? echo fn_number_format($tot_deduct_amnt,2); ?></td>
                    </tfoot>
                </table>
            </div>
            <table style="margin-top:2px" class="rpt_table" border="1" rules="all" width="790" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="5">Deduction at Export realization</th>
                    </tr>
                    <tr>
                        <th width="50">SL</th>
                        <th width="200">LC/SC No.</th>
                        <th width="200">Export Bill No</th>
                        <th width="140">Realized Date</th>
                        <th>Total Deduct Amount</th>
                    </tr>
                </thead>
            </table>
            <div style="width:790px;" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="790" cellpadding="0" cellspacing="0">
                <? 
					if($db_type==0)
					{
						$bill_id_lc=return_field_value("group_concat(distinct(c.id)) as id","com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=1 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						
						$bill_id_sc=return_field_value("group_concat(distinct(c.id)) as id","com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=2 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						
						$invoice_id_sc=return_field_value("group_concat(distinct(b.id)) as id","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and a.id=b.lc_sc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.pay_term=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
					}
					else
					{
						$bill_id_lc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_export_lc a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=1 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						$bill_id_lc=implode(",",array_unique(explode(",",$bill_id_lc)));
						
						$bill_id_sc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_sales_contract a, com_export_doc_submission_invo b, com_export_doc_submission_mst c","a.id=b.lc_sc_id and b.is_lc=2 and c.id=b.doc_submission_mst_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","id");
						$bill_id_sc=implode(",",array_unique(explode(",",$bill_id_sc)));
						
						$invoice_id_sc=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as id","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and a.id=b.lc_sc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.pay_term=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id");
						$invoice_id_sc=implode(",",array_unique(explode(",",$invoice_id_sc)));
					}
					
                    $result=''; $i=1; $total_realized_amnt = 0;
                    if($bill_id_lc!='' && $bill_id_sc!='')
                    {
                        $bill_id_lc=explode(",",$bill_id_lc);
                        $bill_id_sc=explode(",",$bill_id_sc);
                        $result = array_merge( $bill_id_lc, $bill_id_sc);
                        $result =implode(",",$result);	
                    }
                    else if($bill_id_lc!='')
                    {
                        $result=$bill_id_lc;
                    }
                    else if($bill_id_sc!='')
                    {
                        $result=$bill_id_sc;
                    }
                    
					if($result!="")
					{
						$sql="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($result) and d.is_invoice_bill=1 and e.type=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id, d.invoice_bill_id, d.received_date";
						$result=sql_select($sql);
						foreach($result as $row) 
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$bill_no=return_field_value("bank_ref_no","com_export_doc_submission_mst","id=".$row[csf('invoice_bill_id')],"bank_ref_no");	
							$lc_sc_no=return_field_value("a.contract_no as contract_no","com_sales_contract a, com_export_doc_submission_invo b","a.id=b.lc_sc_id and b.is_lc=2 and b.doc_submission_mst_id=".$row[csf('invoice_bill_id')],"contract_no");
							if($lc_sc_no=="")
							{
								$lc_sc_no=return_field_value("a.export_lc_no as export_lc_no","com_export_lc a, com_export_doc_submission_invo b","a.id=b.lc_sc_id and b.is_lc=1 and b.doc_submission_mst_id=".$row[csf('invoice_bill_id')],"export_lc_no"); 
							}
							 
							$total_ded_amnt_short += $row[csf('document_currency')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trr_<? echo $i; ?>">
								<td width="50"><? echo $i; ?></td>
								<td width="200"><p><? echo $lc_sc_no; ?></p></td>
								<td width="200"><p><? echo $bill_no; ?>&nbsp;</p></td>
								<td width="140" align="center"><? echo change_date_format($row[csf('received_date')]); ?>&nbsp;</td>
								<td align="right"><? echo fn_number_format($row[csf('document_currency')],2); ?></td>
							</tr>
							<?
						$i++;
						}
					}
					
					if($invoice_id_sc!="")
					{
						$sql="select d.invoice_bill_id, d.received_date, sum(e.document_currency) as document_currency from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e where d.id=e.mst_id and d.invoice_bill_id in($invoice_id_sc) and d.is_invoice_bill=2 and e.type=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by d.id, d.invoice_bill_id, d.received_date";
						$result=sql_select($sql);
						foreach($result as $row) 
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$bill_no=return_field_value("invoice_no","com_export_invoice_ship_mst","id=".$row[csf('invoice_bill_id')]);	
							$lc_sc_no=return_field_value("a.contract_no as contract_no","com_sales_contract a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=2 and b.id=".$row[csf('invoice_bill_id')],"contract_no");
							if($lc_sc_no=="")
							{
								$lc_sc_no=return_field_value("a.export_lc_no as export_lc_no","com_export_lc a, com_export_invoice_ship_mst b","a.id=b.lc_sc_id and b.is_lc=1 and b.id=".$row[csf('invoice_bill_id')],"export_lc_no"); 
							}
							 
							$total_ded_amnt_short += $row[csf('document_currency')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trr_<? echo $i; ?>">
								<td width="50"><? echo $i; ?></td>
								<td width="150"><p><? echo $lc_sc_no; ?></p></td>
								<td width="150"><p><? echo $bill_no; ?></p></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('received_date')]); ?></td>
								<td align="right"><? echo fn_number_format($row[csf('document_currency')],2); ?></td>
							</tr>
							<?
						$i++;
						}
					}
                    ?>
                    <tfoot class="tbl_bottom">
                    	<tr>
                            <td colspan="4" align="right">Total</td>
                            <td align="right"><? echo fn_number_format($total_ded_amnt_short,2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">Grand Total</th>
                            <td align="right"><? echo fn_number_format($tot_deduct_amnt+$total_ded_amnt_short,2); ?></td>
                        </tr>
                	</tfoot>
                </table>
            </div>
        </div>
	</fieldset>
</div>
<?
exit();
}


if($action=="dfc_paid_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:700px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Bill No.</th>
                        <th width="150">Supplier</th>
                        <th width="110">Bill Value</th>
                        <th width="100">Paid Date</th>
                        <th>Paid Value</th>
                    </thead>
                </table>
           </div>
           <div style="width:700px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $btb_lc_id=array(); $total_paid_amnt = 0;
					
                    $sql_paid_lc="select b.import_mst_id as id, c.id as import_inovice_id, c.bank_ref, d.payment_date, d.accepted_ammount as amnt from com_export_lc a, com_btb_export_lc_attachment b, com_import_invoice_mst c, com_import_payment d where a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.btb_lc_id and c.is_lc=1 and c.id=d.invoice_id and d.payment_head=40 and d.adj_source=5 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by b.import_mst_id, d.id, d.payment_date, d.accepted_ammount, c.id, c.bank_ref";
                    $result_lc=sql_select($sql_paid_lc);
                    foreach($result_lc as $row) 
                    {
                        $total_paid_amnt += $row[csf('amnt')];
                        $btb_lc_id[]=$row[csf('id')];
                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
                        $supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row[csf('id')]);
						$bill_amnt=return_field_value("sum(current_acceptance_value) as val","com_import_invoice_dtls","import_invoice_id=".$row[csf('import_inovice_id')],"val");
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="150"><p><? echo $row[csf('bank_ref')]; ?></p></td>
							<td width="150"><p><? echo $supplier_details[$supplier_id]; ?></p></td>
							<td width="110" align="right"><? echo fn_number_format($bill_amnt,2); ?></td>
							<td width="100" align="center"><? echo change_date_format($row[csf('payment_date')]); ?></td>
							<td align="right"><? echo fn_number_format($row[csf('amnt')],2); ?></td>
						</tr>
						<?
                    $i++;
					}
					
                    $sql_paid_sc="select b.import_mst_id as id, c.id as import_inovice_id, c.bank_ref, d.payment_date, d.accepted_ammount as amnt from com_sales_contract a, com_btb_export_lc_attachment b, com_import_invoice_mst c,com_import_payment d where a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.btb_lc_id and c.is_lc=1 and c.id=d.invoice_id and d.payment_head=40 and d.adj_source=5 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by b.import_mst_id, d.id, d.payment_date, d.accepted_ammount, c.id, c.bank_ref";
                    $result_sc=sql_select($sql_paid_sc);
                    foreach($result_sc as $row_sc)
                    {
                        if(!in_array($row_sc[csf('id')],$btb_lc_id))
                        {
                            $total_paid_amnt += $row_sc[csf('amnt')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                        
							$supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row_sc[csf('id')]);
							$bill_amnt=return_field_value("sum(current_acceptance_value) as val","com_import_invoice_dtls","import_invoice_id=".$row_sc[csf('import_inovice_id')],"val");
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $row_sc[csf('bank_ref')]; ?></p></td>
                                <td width="150"><p><? echo $supplier_details[$supplier_id]; ?></p></td>
                                <td width="110" align="right"><? echo fn_number_format($bill_amnt,2); ?></td>
                                <td width="100" align="center"><? echo change_date_format($row_sc[csf('payment_date')]); ?></td>
                                <td align="right"><? echo fn_number_format($row_sc[csf('amnt')],2); ?></td>
                            </tr>
                            <?
                        $i++;
                        }
                    }
					
					$result='';
					/*if($db_type==0)
					{
						$btb_lc_id_atSight=return_field_value("group_concat(distinct(c.id)) as btb_lc_id","com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						
						$btb_sc_id_atSight=return_field_value("group_concat(distinct(c.id)) as btb_lc_id","com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
					}
					else
					{
						$btb_lc_id_atSight=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as btb_lc_id","com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						$btb_lc_id_atSight=implode(",",array_unique(explode(",",$btb_lc_id_atSight)));
					
						$btb_sc_id_atSight=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as btb_lc_id","com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						$btb_lc_id_atSight=implode(",",array_unique(explode(",",$btb_lc_id_atSight)));
					}
					
					if($btb_lc_id_atSight!='' && $btb_sc_id_atSight!='')
                    {
                        $btb_lc_id_atSight=explode(",",$btb_lc_id_atSight);
                        $btb_sc_id_atSight=explode(",",$btb_sc_id_atSight);
                        $result = array_merge( $btb_lc_id_atSight, $btb_sc_id_atSight);
                        $result =implode(",",$result);
                    }
                    else if($btb_lc_id_atSight!='')
                    {
                        $result=$btb_lc_id_atSight;
                    }
                    else if($btb_sc_id_atSight!='')
                    {
                        $result=$btb_sc_id_atSight;
                    }*/
					
					if($result!="")// no need
					{
						$result="'".implode("','",explode(",",$result))."'";
						$paid_sql_adj="select a.btb_lc_id, a.invoice_no, a.invoice_date, sum(b.current_acceptance_value) as paid_val from com_import_invoice_mst a, com_import_invoice_dtls b where a.id=b.import_invoice_id and a.retire_source=5 and a.btb_lc_id in(".$result.") and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.btb_lc_id, a.invoice_no, a.invoice_date";
						$paidArrayAdj=sql_select( $paid_sql_adj );
						
						foreach($paidArrayAdj as $row_btb_adj)
						{
							$total_paid_amnt += $row_btb_adj[csf('paid_val')];
							
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
						
							$supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row_btb_adj[csf('btb_lc_id')]);
								
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="150"><p><? echo $row_btb_adj[csf('invoice_no')]; ?></p></td>
								<td width="150"><p><? echo $supplier_details[$supplier_id]; ?></p></td>
								<td width="110" align="right"><? echo fn_number_format($row_btb_adj[csf('paid_val')],2); ?></td>
								<td width="100" align="center"><? echo change_date_format($row_btb_adj[csf('invoice_date')]); ?></td>
								<td align="right"><? echo fn_number_format($row_btb_adj[csf('paid_val')],2); ?></td>
							</tr>
							<?
							$i++;
						}
					}
                    ?>
                     <tfoot class="tbl_bottom">
                        <td colspan="5" align="right">Total</td>
                        <td align="right"><? echo fn_number_format($total_paid_amnt,2); ?></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>    
</div>
<?
exit();
}


if($action=="dfc_paid_info_adjust")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:100%">
            <div style="width:700px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                    <thead>
                        <th width="200">Company Name</th>
                        <th>File No</th>
                    </thead>
                    <tr bgcolor="#EFEFEF">
                        <td><? echo $company_library[$company_name]; ?></td>
                        <td><? echo $file_no; ?></td>
                    </tr>
                </table>
                <br />  
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Bill No.</th>
                        <th width="150">Supplier</th>
                        <th width="110">Bill Value</th>
                        <th width="100">Paid Date</th>
                        <th>Paid Value</th>
                    </thead>
                </table>
           </div>
           <div style="width:700px; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $btb_lc_id=array(); $total_paid_amnt = 0;
                    $sql_paid_lc="select b.import_mst_id as id, c.id as import_inovice_id, c.bank_ref, d.payment_date, d.accepted_ammount as amnt from com_export_lc a, com_btb_export_lc_attachment b, com_import_invoice_mst c,com_import_payment  d where a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.btb_lc_id and c.is_lc=1 and c.id=d.invoice_id and d.payment_head=40 and d.adj_source<>5 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by b.import_mst_id, d.id, d.payment_date, d.accepted_ammount, c.id, c.bank_ref"; //echo $sql_paid_lc;
                    $result_lc=sql_select($sql_paid_lc);
                    foreach($result_lc as $row) 
                    {
                        $total_paid_amnt += $row[csf('amnt')];
                        $btb_lc_id[]=$row[csf('id')];

                        
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
                        $supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row[csf('id')]);
						$bill_amnt=return_field_value("sum(current_acceptance_value) as val","com_import_invoice_dtls","import_invoice_id=".$row[csf('import_inovice_id')],"val");
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="150"><p><? echo $row[csf('bank_ref')]; ?></p>&nbsp;</td>
							<td width="150"><p><? echo $supplier_details[$supplier_id]; ?>&nbsp;</p></td>
							<td width="110" align="right"><? echo fn_number_format($bill_amnt,2); ?></td>
							<td width="100" align="center"><? echo change_date_format($row[csf('payment_date')]); ?></td>
							<td align="right"><? echo fn_number_format($row[csf('amnt')],2); ?></td>
						</tr>
						<?
                    $i++;
					}
					//print_r($btb_lc_id);
                    $sql_paid_sc="select b.import_mst_id as id, c.id as import_inovice_id, c.bank_ref, d.payment_date, d.accepted_ammount as amnt from com_sales_contract a, com_btb_export_lc_attachment b, com_import_invoice_mst c,com_import_payment  d where a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.btb_lc_id and c.is_lc=1 and c.id=d.invoice_id and d.payment_head=40 and d.adj_source<>5 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by b.import_mst_id, d.id, d.payment_date, d.accepted_ammount, c.id, c.bank_ref";
                    $result_sc=sql_select($sql_paid_sc);
                    foreach($result_sc as $row_sc)
                    {
                        if(!in_array($row_sc[csf('id')],$btb_lc_id))
                        {
                            $total_paid_amnt += $row_sc[csf('amnt')];
                            if ($i%2==0)  
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                        
							$supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row_sc[csf('id')]);
							$bill_amnt=return_field_value("sum(current_acceptance_value) as val","com_import_invoice_dtls","import_invoice_id=".$row_sc[csf('import_inovice_id')],"val");
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><p><? echo $row_sc[csf('bank_ref')]; ?>&nbsp;</p></td>
                                <td width="150"><p><? echo $supplier_details[$supplier_id]; ?>&nbsp;</p></td>
                                <td width="110" align="right"><? echo fn_number_format($bill_amnt,2); ?></td>
                                <td width="100" align="center"><? echo change_date_format($row_sc[csf('payment_date')]); ?></td>
                                <td align="right"><? echo fn_number_format($row_sc[csf('amnt')],2); ?></td>
                            </tr>
                            <?
                        $i++;
                        }
                    }
					
					$result='';
					if($db_type==0)
					{
						$btb_lc_id_atSight=return_field_value("group_concat(distinct(c.id)) as btb_lc_id","com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						
						$btb_sc_id_atSight=return_field_value("group_concat(distinct(c.id)) as btb_lc_id","com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
					}
					else
					{
						$btb_lc_id_atSight=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as btb_lc_id","com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						$btb_lc_id_atSight=implode(",",array_unique(explode(",",$btb_lc_id_atSight)));
						
						$btb_sc_id_atSight=return_field_value("LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as btb_lc_id","com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c","a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id=c.id and c.payterm_id=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0","btb_lc_id");
						$btb_sc_id_atSight=implode(",",array_unique(explode(",",$btb_sc_id_atSight)));
					}
					
					if($btb_lc_id_atSight!='' && $btb_sc_id_atSight!='')
                    {
                        $btb_lc_id_atSight=explode(",",$btb_lc_id_atSight);
                        $btb_sc_id_atSight=explode(",",$btb_sc_id_atSight);
                        $result = array_merge( $btb_lc_id_atSight, $btb_sc_id_atSight);
                        $result =implode(",",$result);	
                    }
                    else if($btb_lc_id_atSight!='')
                    {
                        $result=$btb_lc_id_atSight;
                    }
                    else if($btb_sc_id_atSight!='')
                    {
                        $result=$btb_sc_id_atSight;
                    }
					
					if($result!="")
					{
						$result="'".implode("','",explode(",",$result))."'";
						$paid_sql_adj="select a.btb_lc_id, a.invoice_no, a.invoice_date, sum(b.current_acceptance_value) as paid_val from com_import_invoice_mst a, com_import_invoice_dtls b where a.id=b.import_invoice_id and a.retire_source!=5 and a.btb_lc_id in($result) and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.btb_lc_id, a.invoice_no, a.invoice_date";
						$paidArrayAdj=sql_select( $paid_sql_adj );
						
						foreach($paidArrayAdj as $row_btb_adj)
						{
							$payterm_id=return_field_value("payterm_id","com_btb_lc_master_details","id=".$row_btb_adj[csf('btb_lc_id')]);
							if($payterm_id==2)
							{
								$total_paid_amnt += $row_btb_adj[csf('paid_val')];
								
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
							
								$supplier_id=return_field_value("supplier_id","com_btb_lc_master_details","id=".$row_btb_adj[csf('btb_lc_id')]);
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<td width="150"><p><? echo $row_btb_adj[csf('invoice_no')]; ?>&nbsp;</p></td>
									<td width="150"><p><? echo $supplier_details[$supplier_id]; ?>&nbsp;</p></td>
									<td width="110" align="right"><? echo fn_number_format($row_btb_adj[csf('paid_val')],2); ?></td>
									<td width="100" align="center"><? echo change_date_format($row_btb_adj[csf('invoice_date')]); ?></td>
									<td align="right"><? echo fn_number_format($row_btb_adj[csf('paid_val')],2); ?></td>
								</tr>
								<?
								$i++;
							}
						}
						
					}
                    ?>
                     <tfoot class="tbl_bottom">
                        <td colspan="5" align="right">Total</td>
                        <td align="right"><? echo fn_number_format($total_paid_amnt,2); ?></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>    
</div>
<?
exit();
}

disconnect($con);
?>
