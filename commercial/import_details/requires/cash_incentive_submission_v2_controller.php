<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
//========== start ========

if( $action == 'submission_details' ) 
{
	?>
    <tr>
        <td align="center"></td>
        <td id="buyer_1"></td>
        <td id="lcSc_1"></td>
        <td id="billNo_1"></td>
        <td id="expNo_1"></td>
        <td id="invoiceNo_1"></td>
        <td id="invoiceQnty_1" align="right"></td>
        <td id="invoiceValue_1" align="right"></td>
        <td id="rlzValue_1" align="center"><input type="text" name="txtRlzValue[]" id="txtRlzValue_1" class="text_boxes_numeric" style="width:80px" onKeyUp="fn_total_amt(0,1)" /></td>
        <td id="rlzDate_1" align="center"></td>
        <td id="specialIncentive_1" align="center"><input type="text" name="txtspecialIncentive[]" id="txtspecialIncentive_1" class="text_boxes_numeric" style="width:80px" readonly /></td>
        <td id="eurolIncentive_1" align="center"><input type="text" name="txteuroIncentive[]" id="txteuroIncentive_1" class="text_boxes_numeric" style="width:80px" readonly /></td>
        <td id="generalIncentive_1" align="center"><input type="text" name="txtgeneralIncentive[]" id="txtgeneralIncentive_1" class="text_boxes_numeric" style="width:80px" readonly /></td>
        <td id="marketIncentive_1" align="center">
        <input type="text" name="txtmarketIncentive[]" id="txtmarketIncentive_1" class="text_boxes_numeric" style="width:80px" readonly />
        <input type="hidden" name="txtRlzId[]" id="txtRlzId_1" class="text_boxes" />
        <input type="hidden" name="txtInoiceId[]" id="txtInoiceId_1" class="text_boxes" />
        <input type="hidden" name="txtLcScId[]" id="txtLcScId_1" class="text_boxes" />
        <input type="hidden" name="txtIsLcSc[]" id="txtIsLcSc_1" class="text_boxes" />
        <input type="hidden" name="updateDtlsId[]" id="updateDtlsId_1" class="text_boxes" />
        </td>
    </tr>
    <?
	exit();
}


//========= Start System ID =========
if ($action=="system_popup")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,1,'');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="900" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
					<th >System ID</th>
                    <th >Buyer Name</th>
                    <th >Bank Name</th>
					<th >Search By</th>
                    <th>Search</th>
                    <th colspan="2">Submission Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					</th>
                </tr>        
            </thead>
            <tbody>
                <tr class="general">
                    <td class="must_entry_caption"> 
                        <input type="hidden" id="selected_id">
                        <? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, '',1);?>
                    </td>
                    <td > 
						<input name="txt_sys" id="txt_sys" class="text_boxes" style="width:100px">
                    </td>
                    <td>
						<?
                            echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id  and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$cbo_company_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
                        ?>
                    </td>
                    <td>
						<?
						echo create_drop_down("cbo_bank", 150, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "");
						?>
                    </td>
					<td>
						<?
							$is_arr=array(1=>'LC/SC',2=>'EXP NO',3=>'Invoice NO');
							echo create_drop_down( "cbo_search_by", 80, $is_arr,"",0, "--Select--", "",'',0 );
						?>
					</td>
					<td >
						<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_lc_sc" id="txt_search_lc_sc" />
					</td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_sys').value+'_'+document.getElementById('cbo_bank').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_lc_sc').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_system_search_list_view', 'search_div', 'cash_incentive_submission_v2_controller', 'setFilterGrid(\'tbl_list_view\',-1)');" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="9"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
		<br>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
      <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_system_search_list_view")
{
	// echo $data;die;
	$com_cond="";$date_cond ="";$year_cond="";$search_text="";$bank_num=""; $search_lc_sc="";
	list($company_id,$search_sys,$bank_id,$submission_start_date, $submission_end_date,$year,$search_string,$is_lc_sc,$lc_sc_no,$buyer_id ) = explode('_', $data);
	//echo $buyer_id.test;die;
	if ($company_id!=0) {$com_cond=" and a.company_id=$company_id";}
	if ($bank_id!=0) {$bank_num=" and a.bank_id=$bank_id";}
	if ($buyer_id!=0) {$buyer_cond=" and d.BUYER_NAME=$buyer_id";}
	
	if($lc_sc_no!='')
	{
		if($is_lc_sc == 1)
		{
			$search_lc_sc="and d.export_lc_no like '%".trim($lc_sc_no)."%'";
			$search_lc_sc="and d.contract_no like '%".trim($lc_sc_no)."%'";
		}
		elseif($is_lc_sc == 2)
		{
			 $search_lc_sc="and c.EXP_FORM_NO like '%".trim($lc_sc_no)."%'";
		}
		else
		{
			 $search_lc_sc="and c.INVOICE_NO like '%".trim($lc_sc_no)."%'";
		}
	}
	

	if ($submission_start_date != '' && $submission_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and a.submission_date '" . change_date_format($submission_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($submission_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and a.submission_date between '" . change_date_format($submission_start_date, '', '', 1) . "' and '" . change_date_format($submission_end_date, '', '', 1) . "'";
		}
    }
	
	if ($search_sys != '')
	{
		if($search_string==1)
			{$search_text="and a.sys_number like '".trim($search_sys)."'";}
		else if ($search_string==2) 
			{$search_text="and a.sys_number like '".trim($search_sys)."%'";}
		else if ($search_string==3)
			{$search_text="and a.sys_number like '%".trim($search_sys)."'";}
		else if ($search_string==4 || $search_string==0)
			{$search_text="and a.sys_number like '%".trim($search_sys)."%'";}
	}
	$is_lc_sc_arr=array(1=>'LC',2=>'SC');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank where status_active=1 and is_deleted=0",'id','bank_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0",'id','buyer_name');
	
	//$sql="SELECT a.id as id, a.company_id as COMPANY_ID, a.bank_id as BANK_ID, d.BUYER_NAME as BUYER_ID, a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE, a.INTERNAL_FILE_NO, a.LC_SC_ID, 1 as SEARCH_BY, a.SUBMISSION_INVOICE_ID, a.NET_REALIZE_VALUE, a.TOTAL_VALUE, listagg(cast(d.export_lc_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, listagg(cast(c.EXP_FORM_NO as varchar(4000)),',') within group(order by c.id) as EXP_FORM_NO, listagg(cast(c.INVOICE_NO as varchar(4000)),',') within group(order by c.id) as INVOICE_NO  
//	from cash_incentive_submission a, cash_incentive_submission_dtls b, COM_EXPORT_INVOICE_SHIP_MST c, com_export_lc d 
//	where a.id=b.mst_id and a.entry_form=566 and b.submission_bill_id=c.id and c.lc_sc_id=d.id and c.is_lc=1 and a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $search_lc_sc $date_cond $year_cond $buyer_cond
//	group by a.id, a.company_id, a.bank_id, d.BUYER_NAME, a.sys_number_prefix_num, a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID, a.SUBMISSION_INVOICE_ID, a.NET_REALIZE_VALUE, a.TOTAL_VALUE
//	union all 
//	select a.id as id, a.company_id as COMPANY_ID,a.bank_id as BANK_ID, d.BUYER_NAME as BUYER_ID,a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE, a.INTERNAL_FILE_NO, a.LC_SC_ID, 2 as SEARCH_BY, a.SUBMISSION_INVOICE_ID, a.NET_REALIZE_VALUE, a.TOTAL_VALUE, listagg(cast(d.contract_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, listagg(cast(c.EXP_FORM_NO as varchar(4000)),',') within group(order by c.id) as EXP_FORM_NO, listagg(cast(c.INVOICE_NO as varchar(4000)),',') within group(order by c.id) as INVOICE_NO
//	from cash_incentive_submission a, cash_incentive_submission_dtls b, COM_EXPORT_INVOICE_SHIP_MST c, com_sales_contract d 
//	where a.id=b.mst_id and a.entry_form=566 and b.submission_bill_id=c.id and c.lc_sc_id=d.id and c.is_lc=2 and a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $search_lc_sc $date_cond $year_cond $buyer_cond
//	group by a.id, a.company_id, a.bank_id, d.BUYER_NAME, a.sys_number_prefix_num,a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID, a.SUBMISSION_INVOICE_ID, a.NET_REALIZE_VALUE, a.TOTAL_VALUE";
	
	$inv_sql="select ID, EXP_FORM_NO, INVOICE_NO from COM_EXPORT_INVOICE_SHIP_MST where BENIFICIARY_ID=$company_id";
	//echo $inv_sql;die;
	$inv_sql_result=sql_select($inv_sql);
	$inv_data=array();
	foreach($inv_sql_result as $val)
	{
		$inv_data[$val["ID"]]["EXP_FORM_NO"]=$val["EXP_FORM_NO"];
		$inv_data[$val["ID"]]["INVOICE_NO"]=$val["INVOICE_NO"];
	}
	unset($inv_sql_result);
	
	
	$sql_dtls="select a.MST_ID, a.SUBMISSION_BILL_ID, a.REALIZATION_ID from CASH_INCENTIVE_SUBMISSION_DTLS a, CASH_INCENTIVE_SUBMISSION b where a.status_active=1 and a.mst_id=b.id and b.COMPANY_ID=$company_id";
	$sql_dtls_result=sql_select($sql_dtls);
	$sub_inv_id_arr=array();$rlz_id_arr=array();
	foreach($sql_dtls_result as $val)
	{
		$sub_inv_id_arr[$val["MST_ID"]]["SUBMISSION_BILL_ID"].=$val["SUBMISSION_BILL_ID"].",";
		$rlz_id_arr[$val["MST_ID"]]["REALIZATION_ID"].=$val["REALIZATION_ID"].",";
	}
	//, a.SUBMISSION_INVOICE_ID
	$sql="SELECT a.id as ID, a.company_id as COMPANY_ID, a.bank_id as BANK_ID, d.BUYER_NAME as BUYER_ID, a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE, a.INTERNAL_FILE_NO, a.LC_SC_ID, 1 as SEARCH_BY, a.NET_REALIZE_VALUE, a.TOTAL_VALUE, listagg(cast(d.export_lc_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as INV_IDS 
	from cash_incentive_submission a, cash_incentive_submission_dtls b, COM_EXPORT_INVOICE_SHIP_MST c, com_export_lc d 
	where a.id=b.mst_id and a.entry_form=566 and b.submission_bill_id=c.id and c.lc_sc_id=d.id and c.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $com_cond $bank_num $search_text $search_lc_sc $date_cond $year_cond $buyer_cond
	group by a.id, a.company_id, a.bank_id, d.BUYER_NAME, a.sys_number_prefix_num, a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID, a.SUBMISSION_INVOICE_ID, a.NET_REALIZE_VALUE, a.TOTAL_VALUE
	union all 
	select a.id as ID, a.company_id as COMPANY_ID,a.bank_id as BANK_ID, d.BUYER_NAME as BUYER_ID,a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE, a.INTERNAL_FILE_NO, a.LC_SC_ID, 2 as SEARCH_BY, a.NET_REALIZE_VALUE, a.TOTAL_VALUE, listagg(cast(d.contract_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as INV_IDS
	from cash_incentive_submission a, cash_incentive_submission_dtls b, COM_EXPORT_INVOICE_SHIP_MST c, com_sales_contract d 
	where a.id=b.mst_id and a.entry_form=566 and b.submission_bill_id=c.id and c.lc_sc_id=d.id and c.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $com_cond $bank_num $search_text $search_lc_sc $date_cond $year_cond $buyer_cond
	group by a.id, a.company_id, a.bank_id, d.BUYER_NAME, a.sys_number_prefix_num,a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID, a.SUBMISSION_INVOICE_ID, a.NET_REALIZE_VALUE, a.TOTAL_VALUE";

	//echo $sql;//die;
	$result=sql_select($sql);
	?>
	<table width="1100" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="30">SL No</th>
            <th width="40">System ID</th>
            <th width="30">LC/SC</th>
            <th width="110">LC/SC No</th>
            <th width="220">EXP NO</th>
            <th width="220">Invoice NO</th>
            <th width="80">Net Realize Value</th>
            <th width="80">Total Value</th>
            <th width="100">Bank Name</th>
            <th width="100">Buyer Name</th>
            <th>Submission Date</th>
        </thead>
    </table>
    <div style="width:1100px; overflow-y:scroll; max-height:280px">
     	<table width="1080" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view">
		<?			
            $i = 1;
            foreach($result as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				$inv_id_arr=array_unique(explode(",",chop($sub_inv_id_arr[$row['ID']]["SUBMISSION_BILL_ID"],",")));
				$inv_no=$exp_no="";
				foreach($inv_id_arr as $in_id)
				{
					$inv_no.=$inv_data[$in_id]["INVOICE_NO"].",";
					$exp_no.=$inv_data[$in_id]["EXP_FORM_NO"].",";
				}
				$inv_no=chop($inv_no,",");$exp_no=chop($exp_no,",");
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row['ID']."_".chop($sub_inv_id_arr[$row['ID']]["SUBMISSION_BILL_ID"],",");?>')" >  
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="40" align="center"><p><? echo $row['SYS_NUMBER_PREFIX_NUM']; ?></td>
                    <td width="30" align="center"><p><? echo $is_lc_sc_arr[$row['SEARCH_BY']]; ?></p></td>
                    <td width="110" align="center"><p><? echo implode(",",array_unique(explode(",",$row['SC_LC_NO']))); ?></p></td>
                    <td width="220"><p><? echo $exp_no; ?></p></td>
                    <td width="220"><p><? echo $inv_no; ?></p></td>
                    <td width="80" align="right"><? echo number_format($row['NET_REALIZE_VALUE'],2); ?></td>
                    <td width="80" align="right"><? echo number_format($row['TOTAL_VALUE'],2); ?></td>
					<td width="100" align="center"><p><? echo $bank_arr[$row['BANK_ID']]; ?></p></td>
                    <td width="100" align="center"><p><? echo $buyer_arr[$row['BUYER_ID']];; ?></p></td>
					<td align="center"><p><? echo change_date_format($row['SUBMISSION_DATE']); ?></p></td>
				</tr>
				<?
                $i++;
            }
			?>
		</table>
    </div>
	</body>           
	</html>
    <!--<script>setFilterGrid('tbl_list_view',-1);</script>-->
	<?
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$data=explode("**",$data);
	$data_id=$data[0];
	$is_lc_sc=$data[1];
	
	$sql_dtls="select SUBMISSION_BILL_ID, REALIZATION_ID from CASH_INCENTIVE_SUBMISSION_DTLS where status_active=1 and mst_id=$data_id";
	$sql_dtls_result=sql_select($sql_dtls);
	$sub_inv_id_arr=array();$rlz_id_arr=array();
	foreach($sql_dtls_result as $val)
	{
		$sub_inv_id_arr[$val["SUBMISSION_BILL_ID"]]=$val["SUBMISSION_BILL_ID"];
		$rlz_id_arr[$val["REALIZATION_ID"]]=$val["REALIZATION_ID"];
	}
	
	// , buyer_id as BUYER_ID
	$data_array="SELECT id as ID,sys_number as SYS_NUMBER, company_id as COMPANY_ID, bank_id as BANK_ID, submission_date as SUBMISSION_DATE, submission_invoice_id as SUBMISSION_INVOICE_ID, realization_id as REALIZATION_ID, incentive_bank_file as INCENTIVE_BANK_FILE, net_realize_value as NET_REALIZE_VALUE, remarks as REMARKS, special_submitted_chk as SPECIAL_SUBMITTED_CHK, euro_incentive_chk as EURO_INCENTIVE_CHK, general_incentive_chk as GENERAL_INCENTIVE_CHK, market_submitted_chk as MARKET_SUBMITTED_CHK, special_submitted as SPECIAL_SUBMITTED, euro_incentive as EURO_INCENTIVE, general_incentive as GENERAL_INCENTIVE, market_submitted as MARKET_SUBMITTED, amount as AMOUNT,is_lc_sc as IS_LC_SC,lc_sc_id as LC_SC_ID,internal_file_no as INTERNAL_FILE_NO, file_no_string as FILE_NO_STRING, TOTAL_NET_WEIGHT, YARN_QNTY, YARN_VALUE, YARN_RATE, OVER_HEAD_CHARGE, TOTAL_VALUE, DAYS_TO_REALIZE, POSSIBLE_REALI_DATE,CERTIFICATE_AMOUNT,SUB_EXCHANGE_RATE,AUDIT_EXCHANGE_RATE,LOAN_VALUE,LOAN_BASED_ON,LOAN_GIVEN_VALUE
	from cash_incentive_submission 
    where  status_active=1 and is_deleted=0 and id='$data_id'";
    // echo $data_array;
    $data_result=sql_select($data_array);
    echo "document.getElementById('txt_system_id').value = '".$data_result[0]["SYS_NUMBER"]."';\n";  
    echo "document.getElementById('cbo_company_name').value = '".$data_result[0]["COMPANY_ID"]."';\n";  
    echo "document.getElementById('cbo_bank_name').value = '".$data_result[0]["BANK_ID"]."';\n";  
    echo "document.getElementById('txt_submission_date').value = '".change_date_format($data_result[0]["SUBMISSION_DATE"])."';\n";  
    echo "document.getElementById('realization_id').value = '".implode(",",$rlz_id_arr)."';\n";  
    echo "document.getElementById('submission_invoice_id').value = '".implode(",",$sub_inv_id_arr)."';\n";  
	echo "document.getElementById('is_lc_sc').value = '".$data_result[0]["IS_LC_SC"]."';\n";  
    echo "document.getElementById('lc_sc_id').value = '".$data_result[0]["LC_SC_ID"]."';\n";  
    echo "document.getElementById('txt_file_no').value = '".$data_result[0]["INTERNAL_FILE_NO"]."';\n"; 
	//echo "document.getElementById('txt_file_no_string').value = '".$data_result[0]["FILE_NO_STRING"]."';\n"; 	
    echo "document.getElementById('txt_incective_bank_file').value = '".$data_result[0]["INCENTIVE_BANK_FILE"]."';\n";  
    echo "document.getElementById('txt_net_realize_value').value = '".$data_result[0]["NET_REALIZE_VALUE"]."';\n";  
    // echo "document.getElementById('cbo_buyer_name').value = '".$data_result[0]["BUYER_ID"]."';\n";  
    echo "document.getElementById('txt_remarks').value = '".$data_result[0]["REMARKS"]."';\n";
	echo "document.getElementById('txt_day_to_realize').value = '".$data_result[0]["DAYS_TO_REALIZE"]."';\n";  
    echo "document.getElementById('txt_possible_reali_date').value = '".change_date_format($data_result[0]["POSSIBLE_REALI_DATE"])."';\n";
	
	echo "document.getElementById('txt_net_weight').value = '".$data_result[0]["TOTAL_NET_WEIGHT"]."';\n";  
    echo "document.getElementById('txt_yarn_qnty').value = '".$data_result[0]["YARN_QNTY"]."';\n";  
    echo "document.getElementById('txt_yarn_value').value = '".$data_result[0]["YARN_VALUE"]."';\n"; 
	echo "document.getElementById('txt_yarn_rate').value = '".$data_result[0]["YARN_RATE"]."';\n"; 	
    echo "document.getElementById('txt_over_head_charge').value = '".$data_result[0]["OVER_HEAD_CHARGE"]."';\n";  
    echo "document.getElementById('txt_total_value').value = '".$data_result[0]["TOTAL_VALUE"]."';\n"; 
	
	echo "document.getElementById('euro_incentive_percent').value = '".$data_result[0]["EURO_INCENTIVE_CHK"]."';\n";  
    echo "document.getElementById('general_incentive_percent').value = '".$data_result[0]["GENERAL_INCENTIVE_CHK"]."';\n";     
    echo "document.getElementById('txt_total_special_incentive').value = '".$data_result[0]["SPECIAL_SUBMITTED"]."';\n";  
    echo "document.getElementById('txt_total_euro_incentive').value = '".$data_result[0]["EURO_INCENTIVE"]."';\n";  
    echo "document.getElementById('txt_total_general_incentive').value = '".$data_result[0]["GENERAL_INCENTIVE"]."';\n";  
    echo "document.getElementById('txt_total_market_incentive').value = '".$data_result[0]["MARKET_SUBMITTED"]."';\n";
	echo "document.getElementById('txt_certificate_amount').value = '".$data_result[0]["CERTIFICATE_AMOUNT"]."';\n";  
	echo "document.getElementById('txt_sub_exchange_rate').value = '".$data_result[0]["SUB_EXCHANGE_RATE"]."';\n";  
	echo "document.getElementById('txt_audit_exchange_rate').value = '".$data_result[0]["AUDIT_EXCHANGE_RATE"]."';\n"; 
	echo "document.getElementById('txt_loan').value = '".$data_result[0]["LOAN_VALUE"]."';\n";
	echo "document.getElementById('cbo_loan_value').value = '".$data_result[0]["LOAN_BASED_ON"]."';\n";
	echo "document.getElementById('txt_loan_given').value = '".$data_result[0]["LOAN_GIVEN_VALUE"]."';\n";   
    //echo "document.getElementById('total_amount').value = '".$data_result[0]["AMOUNT"]."';\n";  
    
	if($data_result[0]["SPECIAL_SUBMITTED_CHK"]==1){
		echo "document.getElementById('special_submitted_chk').value = 1;\n";
		echo "$('#special_submitted_chk').attr('checked',true);\n";
	}else{
		echo "document.getElementById('special_submitted_chk').value = 0;\n";
		echo "$('#special_submitted_chk').attr('checked',false);\n";
	}
	
	if($data_result[0]["MARKET_SUBMITTED_CHK"]==1){
		echo "document.getElementById('market_submitted_chk').value = 1;\n";
		echo "$('#market_submitted_chk').attr('checked',true);\n";
	}else{
		echo "document.getElementById('market_submitted_chk').value = 0;\n";
		echo "$('#market_submitted_chk').attr('checked',false);\n";
	}
	echo "document.getElementById('update_id').value = '".$data_result[0]["ID"]."';\n";
	
     

	$sql = "select d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT,e.id as LC_SC_ID, e.export_lc_no as SC_LC_NO,e.lc_value as SC_LC_VALUE ,e.bank_file_no as BANK_FILE_NO, e.lc_year as SC_LC_YEAR
	from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d,com_export_lc e
	where a.id in (".implode(",",$rlz_id_arr).") and a.invoice_bill_id=c.id and a.is_invoice_bill=1 and c.id=d.doc_submission_mst_id and d.lc_sc_id=e.id and d.is_lc=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	union all
	select d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT,e.id as LC_SC_ID, e.contract_no as SC_LC_NO,e.contract_value as SC_LC_VALUE,e.bank_file_no as BANK_FILE_NO, e.sc_year as SC_LC_YEAR
	from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d,com_sales_contract e
	where a.id in (".implode(",",$rlz_id_arr).") and a.invoice_bill_id=c.id and a.is_invoice_bill=1 and c.id=d.doc_submission_mst_id and d.lc_sc_id=e.id and d.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";

	$sql_result=sql_select($sql);
	$lc_sc_no=''; $lc_sc_year=''; $lc_sc_bank_file=''; $lc_sc_value=array(); $bill_value=array();
	foreach($sql_result as $rows){
		$lc_sc_no.=$rows['SC_LC_NO'].',';
		$lc_sc_year.=$rows['SC_LC_YEAR'].',';
		$lc_sc_bank_file.=$rows['BANK_FILE_NO'].',';
		$lc_sc_value[$rows['LC_SC_ID']]=$rows['SC_LC_VALUE'];
		$bill_value[$rows['SUB_DTLS_ID']]=$rows['BILL_AMNT'];
	}
	echo "document.getElementById('txt_lc_sc_no').value 		= '".implode(",",array_unique(explode(",",chop($lc_sc_no,','))))."';\n";
	echo "document.getElementById('txt_lc_value').value 		= '".number_format(array_sum($lc_sc_value),2,'.','')."';\n";
	echo "document.getElementById('txt_file_year').value 		= '".implode(",",array_unique(explode(",",chop($lc_sc_year,','))))."';\n";
	echo "document.getElementById('txt_bank_file_no').value 	= '".implode(",",array_unique(explode(",",chop($lc_sc_bank_file,','))))."';\n";
	echo "document.getElementById('txt_invoice_value').value 	= '".number_format(array_sum($bill_value),2,'.','')."';\n";

	exit();
}
//========= End System ID =========
//========= Start LC/SC No =========
if($action=="proceed_realization_popup_search")
{
	echo load_html_head_contents("Export Proceeds Realization Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
	<script>

		var selected_id = new Array();var invoice_id = new Array();var lc_sc_id = new Array();
		var is_lc = new Array();var inter_file_no = new Array();buyerArr = new Array();lcScArr = new Array();lcScIdArr = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length; 
			tbl_row_count = tbl_row_count ;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) 
		{
			
			if(buyerArr.length==0)
			{
				buyerArr.push( $('#txt_buyer' + str).val() );
			}
			else if( jQuery.inArray( $('#txt_buyer' + str).val(), buyerArr )== -1 &&  buyerArr.length>0)
			{
				alert("Buyer Mixed is Not Allow");return;
			}
			
			/*if(lcScIdArr.length==0)
			{
				lcScIdArr.push( $('#txt_LC_SC_id' + str).val() );
			}
			else if( jQuery.inArray( $('#txt_LC_SC_id' + str).val(), lcScIdArr )== -1 &&  lcScIdArr.length>0)
			{
				alert("LC/SC Mixed is Not Allow");return;
				
			}
			
			if(lcScArr.length==0)
			{
				lcScArr.push( $('#txt_is_lc' + str).val() );
			}
			else if( jQuery.inArray( $('#txt_is_lc' + str).val(), lcScArr )== -1 &&  lcScArr.length>0)
			{
				alert("LC/SC Type Mixed is Not Allow");return;
			}*/
			
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_invoice_id' + str).val(), invoice_id ) == -1 ) {
				selected_id.push( $('#txt_mst_id' + str).val() );
				invoice_id.push( $('#txt_invoice_id' + str).val() );
				lc_sc_id.push( $('#txt_LC_SC_id' + str).val() );
				is_lc.push( $('#txt_is_lc' + str).val() );
				inter_file_no.push( $('#txt_inter_file_no' + str).val() );
			}
			else {
				for( var i = 0; i < invoice_id.length; i++ ) {
					if( invoice_id[i] == $('#txt_invoice_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				invoice_id.splice( i, 1 );
				lc_sc_id.splice( i, 1 );
				is_lc.splice( i, 1 );
				inter_file_no.splice( i, 1 );
			}
			var item_id = ''; var invo_id='';var sc_lc_id='';
			var is_sc='';var file_no='';var file_string='';
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				item_id += selected_id[i] + ',';
				invo_id += invoice_id[i] + ',';
				sc_lc_id += lc_sc_id[i] + ',';
				is_sc += is_lc[i] + ',';
				file_no += inter_file_no[i] + ',';
				file_string+=is_lc[i]+"_"+lc_sc_id[i]+"_"+inter_file_no[i]+"_"+selected_id[i]+"_"+invoice_id[i]+'*';
			}
			item_id = item_id.substr( 0, item_id.length - 1 );
			invo_id = invo_id.substr( 0, invo_id.length - 1 );
			sc_lc_id = sc_lc_id.substr( 0, sc_lc_id.length - 1 );
			is_sc = is_sc.substr( 0, is_sc.length - 1 );
			file_no = file_no.substr( 0, file_no.length - 1 );
			file_string = file_string.substr( 0, file_string.length - 1 );
			//var file_string=is_sc+"_"+sc_lc_id+"_"+file_no+"_"+item_id+"_"+invo_id
			$('#hidden_realization_id').val(item_id);
			$('#hidden_invoice_id').val(invo_id);
			$('#hidden_lc_sc_id').val(sc_lc_id);
			$('#hidden_is_lc').val(is_sc);
			$('#hidden_inter_file_no').val(file_no);
			$('#file_no_string').val(file_string);
		}
		
		function set_all()
		{
			var old=document.getElementById('old_data_row_color').value;
			if(old!="")
			{ 
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{ 
					js_set_value( old[i] ) ;
					//toggle( document.getElementById( 'search' + i ), '#FFFFCC' );
				}
			}
		}
		
    </script>
    </head>
    <body>
    <div align="center" style="width:1130px;">
        <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
            <fieldset style="width:1130px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="1000" class="rpt_table" align="center">
                    <thead>
                        <th>Buyer</th>
						<th>Search By</th>
                    	<th>Search</th>
                        <th>Realization Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_realization_id" id="hidden_realization_id" value="" />
                            <input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id" value="" />
                            <input type="hidden" name="hidden_lc_sc_id" id="hidden_lc_sc_id" value="" />
                            <input type="hidden" name="hidden_is_lc" id="hidden_is_lc" value="" />
                            <input type="hidden" name="hidden_inter_file_no" id="hidden_inter_file_no" value="" />
                            <input type="hidden" name="file_no_string" id="file_no_string" value="" />
                        </th>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id  and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$beneficiary_name $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
                            ?>
                        </td>
						<td>
						<?
							$is_arr=array(1=>'LC/SC',2=>'File No',3=>'Invoice',4=>'EXP No');
							echo create_drop_down( "cbo_search_by", 152, $is_arr,"",0, "--Select--", "",'',0 );
						?>
					</td>
					<td >
						<input type="text" style="width:140px;" class="text_boxes"  name="txt_search_lc_sc" id="txt_search_lc_sc" />
					</td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:70px" placeholder="From Date" />
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"placeholder="To Date" />
                        </td>
                        <td>
                            <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'**'+<? echo $beneficiary_name; ?>+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_lc_sc').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $update_id; ?>'+'**'+'<? echo $submission_invoice_id; ?>', 'proceed_realization_search_list_view', 'search_div', 'cash_incentive_submission_v2_controller', 'setFilterGrid(\'list_view\',-1)');set_all();" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
            	</table>
                <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="proceed_realization_search_list_view")
{
	$data=explode('**',$data);

	$buyer_id=$data[0];
	$company_id=$data[1];
	$search_by=$data[2];
	$search_string=trim($data[3]);
	$date_form=$data[4];
	$date_to=$data[5];
	$year=$data[6];
	$update_id=$data[7];
	$submission_invoice_id_arr = explode(",",$data[8]);
	//echo $submission_invoice_id."=test";die;
	if($company_id!=0) $com_cond="and a.benificiary_id=$company_id"; else $com_cond="";
	if($buyer_id!=0) $buyer_cond="and a.buyer_id=$buyer_id"; else $buyer_cond="";
	if($search_string!='')
	{
		if($search_by == 1)
		{
			$search_text=" and d.export_lc_no like '%".trim($search_string)."%'";
			$search_text2=" and d.contract_no like '%".trim($search_string)."%'";
		}
		else if($search_by == 2)
		{
			$search_text=" and d.internal_file_no like '%".trim($search_string)."%'";
			$search_text2=" and d.internal_file_no like '%".trim($search_string)."%'";
		}
		else if($search_by == 3)
		{
			$search_text="and e.INVOICE_NO like '%".trim($search_string)."%'";
			$search_text2="and e.INVOICE_NO like '%".trim($search_string)."%'";
		}
		else if($search_by == 4)
		{
			$search_text="and e.EXP_FORM_NO like '%".trim($search_string)."%'";
			$search_text2="and e.EXP_FORM_NO like '%".trim($search_string)."%'";
		}
	}

	$date_cond="";
	if($date_form!="" && $date_to !="")
	{
		if($db_type==0)
		{
			$date_form=change_date_format($date_form,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		else
		{
			$date_form=change_date_format($date_form,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}
		$date_cond=" and a.received_date between '$date_form' and '$date_to'";
	}
	else 
    {
        /*$date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.received_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.received_date,'YYYY') =$year ";
			}
		}*/
	}
	$update_cond="";
	if($update_id!="") $update_cond=" and mst_id <> $update_id";
	$previouse_entry_sql="select SUBMISSION_BILL_ID, sum(SPECIAL_INCENTIVE) as SPECIAL_INCENTIVE, sum(EURO_ZONE_INCENTIVE) as EURO_ZONE_INCENTIVE, sum(GENERAL_INCENTIVE) as GENERAL_INCENTIVE, sum(MARKET_INCENTIVE) as MARKET_INCENTIVE from CASH_INCENTIVE_SUBMISSION_DTLS where status_active=1 and is_deleted=0 $update_cond group by SUBMISSION_BILL_ID";
	$previouse_entry_sql_result=sql_select($previouse_entry_sql);
	$privous_data=array();
	foreach($previouse_entry_sql_result as $val)
	{
		if($val["SPECIAL_INCENTIVE"]>0 && $val["EURO_ZONE_INCENTIVE"]>0 && $val["GENERAL_INCENTIVE"]>0 && $val["MARKET_INCENTIVE"]>0)
		{
			$privous_data[$val["SUBMISSION_BILL_ID"]]=$val["SUBMISSION_BILL_ID"];
		}
		
	}
	
	
	$sql = "select a.id as ID, a.benificiary_id as BENIFICIARY_ID, a.buyer_id as BUYER_ID, a.invoice_bill_id as INVOICE_BILL_ID, a.received_date as RECEIVED_DATE, b.bank_ref_no as BANK_REF_NO, c.net_invo_value as BILL_AMNT, d.id as SC_LC_ID, d.export_lc_no as SC_LC_NO, d.internal_file_no as INTERNAL_FILE_NO, d.lc_year as FILE_YEAR, 1 as SEARCH_BY, c.id as SUB_DTLS_ID, e.id as INV_ID, e.INVOICE_NO, e.EXP_FORM_NO, e.INVOICE_QUANTITY, e.NET_INVO_VALUE, e.COUNTRY_ID
	from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst e, com_export_lc d
	where a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=e.id and c.lc_sc_id=d.id and e.lc_sc_id=d.id and a.is_invoice_bill=1 and c.is_lc=1 and e.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $com_cond $buyer_cond $search_text $date_cond $year_cond 
	union all
	select a.id as ID, a.benificiary_id as BENIFICIARY_ID, a.buyer_id as BUYER_ID, a.invoice_bill_id as INVOICE_BILL_ID, a.received_date as RECEIVED_DATE, b.bank_ref_no as BANK_REF_NO, c.net_invo_value as BILL_AMNT, d.id as SC_LC_ID, d.contract_no as SC_LC_NO, d.internal_file_no as INTERNAL_FILE_NO, d.sc_year as FILE_YEAR, 2 as SEARCH_BY, c.id as SUB_DTLS_ID, e.id as INV_ID, e.INVOICE_NO, e.EXP_FORM_NO, e.INVOICE_QUANTITY, e.NET_INVO_VALUE, e.COUNTRY_ID
	from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst e, com_sales_contract d
	where a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=e.id and c.lc_sc_id=d.id and e.lc_sc_id=d.id and a.is_invoice_bill=1 and c.is_lc=2 and e.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $com_cond $buyer_cond $search_text2 $date_cond $year_cond order by EXP_FORM_NO asc";
	//echo $sql;//die;
	$is_LC_SC=array(1=>"LC",2=>"SC");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');

	$sql_result = sql_select($sql);
	$result=array();$bill_amount=array();$document_amount=array();
	foreach($sql_result as $row)
	{
		if($privous_data[$row['INV_ID']]=="")
		{
			$result[$row['INV_ID']]['ID']=$row['ID'];
			$result[$row['INV_ID']]['INVOICE_BILL_ID']=$row['INVOICE_BILL_ID'];
			$result[$row['INV_ID']]['SC_LC_ID']=$row['SC_LC_ID'];
			$result[$row['INV_ID']]['BANK_REF_NO']=$row['BANK_REF_NO'];
			$result[$row['INV_ID']]['BENIFICIARY_ID']=$row['BENIFICIARY_ID'];
			$result[$row['INV_ID']]['BUYER_ID']=$row['BUYER_ID'];
			$result[$row['INV_ID']]['SEARCH_BY']=$row['SEARCH_BY'];
			$result[$row['INV_ID']]['SC_LC_NO']=$row['SC_LC_NO'];
			$result[$row['INV_ID']]['RECEIVED_DATE']=$row['RECEIVED_DATE'];
			$result[$row['INV_ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
			$result[$row['INV_ID']]['FILE_YEAR']=$row['FILE_YEAR'];
			$result[$row['INV_ID']]['INVOICE_NO']=$row['INVOICE_NO'];
			$result[$row['INV_ID']]['EXP_FORM_NO']=$row['EXP_FORM_NO'];
			$result[$row['INV_ID']]['COUNTRY_ID']=$row['COUNTRY_ID'];
			$result[$row['INV_ID']]['INVOICE_QUANTITY']=$row['INVOICE_QUANTITY'];
			$result[$row['INV_ID']]['NET_INVO_VALUE']=$row['NET_INVO_VALUE'];
		}
	}

	?>
	<table width="1120" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
    	<thead>
            <th width="40">SL No</th>
            <th width="70">Benificiary</th>
            <th width="70">Buyer</th>
            <th width="100">EXP No.</th>
            <th width="120">Invoice No</th>
            <th width="80">Invoice Qty</th>
            <th width="100">Net Invoice Value</th>
            <th width="100">Bill No</th>
            <th width="50">LC/SC</th>
            <th width="120">LC/SC No</th>
            <th width="60">File No</th>
            <th width="100">Country</th>
            <th>Received Date</th>
        </thead>
    </table>
    <div style="width:1120px; max-height:240px; overflow-y:scroll" id="search_div" align="left">	 
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="list_view"> 
     	<tbody>
		<?			
		$i = 1;$oldDataRow="";
		foreach($result as $inv_id=>$row)
		{
			if ($i%2==0)
				$bgcolor="#FFFFFF";
			else
				$bgcolor="#E9F3FF";

			//old data row arrange here------
			if( in_array($inv_id, $submission_invoice_id_arr) )
			{
				if($oldDataRow=="") $oldDataRow = $i; else $oldDataRow .= ",".$i;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)" >  
				<td width="40" align="center"><? echo $i; ?>
				<input type="hidden" name="txt_mst_id" id="txt_mst_id<? echo $i ?>" value="<? echo $row['ID']; ?>"/>	
				<input type="hidden" name="txt_invoice_id" id="txt_invoice_id<? echo $i ?>" value="<? echo $inv_id; ?>"/>	
				<input type="hidden" name="txt_LC_SC_id" id="txt_LC_SC_id<? echo $i ?>" value="<? echo $row['SC_LC_ID']; ?>"/>	
				<input type="hidden" name="txt_is_lc" id="txt_is_lc<? echo $i ?>" value="<? echo $row['SEARCH_BY']; ?>"/>	
				<input type="hidden" name="txt_inter_file_no" id="txt_inter_file_no<? echo $i ?>" value="<? echo $row['INTERNAL_FILE_NO']; ?>"/>
                <input type="hidden" name="txt_buyer" id="txt_buyer<? echo $i ?>" value="<? echo $row['BUYER_ID']; ?>"/>	
				</td>
				<td width="70"><p><? echo $comp[$row['BENIFICIARY_ID']]; ?></p></td>
				<td width="70"><p><? echo $buyer_arr[$row['BUYER_ID']]; ?></p></td>
				<td align="center" width="100"><p><? echo $row['EXP_FORM_NO']; ?></p></td>
				<td width="120"><p><? echo $row['INVOICE_NO']; ?></p></td>
				<td width="80" align="right"><p><? echo number_format($row['INVOICE_QUANTITY'],0);?></p></td>
				<td width="100" align="right"><p><? echo number_format($row['NET_INVO_VALUE'],2);?></p></td>
				<td width="100" align="center"><p><? echo $row['BANK_REF_NO'];?></p></td>
				<td width="50" align="center"><p><? echo $is_LC_SC[$row['SEARCH_BY']];?></p></td>
				<td width="120"><p><? echo $row['SC_LC_NO'];?></p></td>
				<td width="60"><p><? echo $row['INTERNAL_FILE_NO'];?></p></td>
                <td width="100" align="center"><p><? echo $country_arr[$row['COUNTRY_ID']];?></p></td>
				<td align="center"><p><? echo change_date_format($row['RECEIVED_DATE']);?></p></td>
			</tr>
			<?
            $i++;
		}
		?>
		</tbody>
	</table>
    </div>

    <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%"> 
                    <div style="width:50%; float:left" align="left">
                    	<input type="hidden" name="old_data_row_color" id="old_data_row_color" value="<? echo $oldDataRow; ?>"/>
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>

	<?
	exit();
}

if($action=="populate_data_from_invoice_bill")
{
	// , a.buyer_id as BUYER_ID
	$sql="select a.benificiary_id as BENIFICIARY_ID, e.id as LC_SC_ID, e.export_lc_no as SC_LC_NO, e.lc_value as SC_LC_VALUE, e.lc_year as SC_LC_YEAR, e.bank_file_no as BANK_FILE_NO, b.id as RLZ_DTLS_ID, b.document_currency as DOCUMENT_CURRENCY, d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT, d.is_lc as IS_LC, c.id as INV_ID, c.CARTON_NET_WEIGHT
	from  com_export_proceed_rlzn_dtls b, com_export_proceed_realization a, com_export_doc_submission_invo d, com_export_invoice_ship_mst c, com_export_lc e 
	where b.mst_id=a.id and a.invoice_bill_id=d.doc_submission_mst_id and d.INVOICE_ID=c.id and c.lc_sc_id=e.id and d.lc_sc_id=e.id and c.is_lc=1 and d.is_lc=1 and b.type=1 and a.is_invoice_bill=1 and  a.id in ($data) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	 union all
	select a.benificiary_id as BENIFICIARY_ID, e.id as LC_SC_ID, e.contract_no as SC_LC_NO, e.contract_value as SC_LC_VALUE, e.sc_year as SC_LC_YEAR, e.bank_file_no as BANK_FILE_NO, b.id as RLZ_DTLS_ID, b.document_currency as DOCUMENT_CURRENCY, d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT, d.is_lc as IS_LC, c.id as INV_ID, c.CARTON_NET_WEIGHT
	from  com_export_proceed_rlzn_dtls b, com_export_proceed_realization a, com_export_doc_submission_invo d, com_export_invoice_ship_mst c, com_sales_contract e 
	where b.mst_id=a.id and a.invoice_bill_id=d.doc_submission_mst_id and d.INVOICE_ID=c.id and c.lc_sc_id=e.id and d.lc_sc_id=e.id and d.is_lc=2 and c.is_lc=2 and b.type=1  and a.is_invoice_bill=1 and  a.id in ($data) and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	and b.is_deleted=0";

	//   echo $sql;die;

	$data_array=sql_select($sql);
	echo "document.getElementById('cbo_company_name').value 	= '".$data_array[0]["BENIFICIARY_ID"]."';\n";
	// echo "document.getElementById('cbo_buyer_name').value 		= '".$data_array[0]["BUYER_ID"]."';\n";

	$realization_value_arr=array();$invoice_value_arr=array();$lc_sc_value_arr=array();$lc_sc_no='';$bank_file='';
	foreach($data_array as $row){
		$realization_value_arr[$row['RLZ_DTLS_ID']]=$row['DOCUMENT_CURRENCY'];
		$invoice_value_arr[$row['SUB_DTLS_ID']]=$row['BILL_AMNT'];
		$lc_sc_value_arr[$row['LC_SC_ID']]=$row['SC_LC_VALUE'];
		$inv_net_weight_arr[$row['INV_ID']]=$row['CARTON_NET_WEIGHT'];
		$lc_sc_no .=$row['SC_LC_NO'].',';
		$lc_sc_year .=$row['SC_LC_YEAR'].',';
		$bank_file .=$row['BANK_FILE_NO'].',';
	}
	echo "document.getElementById('txt_lc_sc_no').value 		= '".implode(",",array_unique(explode(",",chop($lc_sc_no,','))))."';\n";
	echo "document.getElementById('txt_bank_file_no').value 	= '".implode(",",array_unique(explode(",",chop($bank_file,','))))."';\n";
	echo "document.getElementById('txt_file_year').value 		= '".implode(",",array_unique(explode(",",chop($lc_sc_year,','))))."';\n";
	
	echo "document.getElementById('txt_invoice_value').value 	= '".number_format(array_sum($invoice_value_arr),2,'.','')."';\n";
	echo "document.getElementById('txt_net_realize_value').value = '".number_format(array_sum($realization_value_arr),2,'.','')."';\n";
	echo "document.getElementById('txt_lc_value').value 		= '".number_format(array_sum($lc_sc_value_arr),2,'.','')."';\n";
	echo "document.getElementById('txt_net_weight').value 		= '".number_format(array_sum($inv_net_weight_arr),2,'.','')."';\n";
	
	exit();
}

if( $action == 'show_invoice_dtls_listview' ) 
{
	$data=explode("_",$data);
	//print_r($data);
	$invoice_ids=$data[0];
	$is_lc_sc=$data[1];
	$update_id=$data[2];
	
	//echo $update_id.test;die;
	
	$update_cond="";
	//if($update_id!="") $update_cond=" and mst_id <> $update_id";
	$previouse_entry_sql="SELECT SUBMISSION_BILL_ID, MST_ID, RLZ_VALUE, sum(SPECIAL_INCENTIVE) as SPECIAL_INCENTIVE, sum(EURO_ZONE_INCENTIVE) as EURO_ZONE_INCENTIVE, sum(GENERAL_INCENTIVE) as GENERAL_INCENTIVE, sum(MARKET_INCENTIVE) as MARKET_INCENTIVE 
	from CASH_INCENTIVE_SUBMISSION_DTLS where status_active=1 and is_deleted=0 and SUBMISSION_BILL_ID in($invoice_ids) $update_cond 
	group by SUBMISSION_BILL_ID, MST_ID, RLZ_VALUE";
	// echo $update_id."**".$previouse_entry_sql;
	$previouse_entry_sql_result=sql_select($previouse_entry_sql);
	$privous_data=array();$current_data=array();
	foreach($previouse_entry_sql_result as $val)
	{
		if($update_id!="" && $update_id==$val["MST_ID"])
		{
			$current_data[$val["SUBMISSION_BILL_ID"]]["SPECIAL_INCENTIVE"]+=$val["SPECIAL_INCENTIVE"];
			$current_data[$val["SUBMISSION_BILL_ID"]]["EURO_ZONE_INCENTIVE"]+=$val["EURO_ZONE_INCENTIVE"];
			$current_data[$val["SUBMISSION_BILL_ID"]]["GENERAL_INCENTIVE"]+=$val["GENERAL_INCENTIVE"];
			$current_data[$val["SUBMISSION_BILL_ID"]]["MARKET_INCENTIVE"]+=$val["MARKET_INCENTIVE"];
			$current_data[$val["SUBMISSION_BILL_ID"]]["RLZ_VALUE"]=$val["RLZ_VALUE"];
		}
		else
		{
			$privous_data[$val["SUBMISSION_BILL_ID"]]["SPECIAL_INCENTIVE"]+=$val["SPECIAL_INCENTIVE"];
			$privous_data[$val["SUBMISSION_BILL_ID"]]["EURO_ZONE_INCENTIVE"]+=$val["EURO_ZONE_INCENTIVE"];
			$privous_data[$val["SUBMISSION_BILL_ID"]]["GENERAL_INCENTIVE"]+=$val["GENERAL_INCENTIVE"];
			$privous_data[$val["SUBMISSION_BILL_ID"]]["MARKET_INCENTIVE"]+=$val["MARKET_INCENTIVE"];
		}
	}
	// echo "<pre>";print_r($current_data);
	/*if($is_lc_sc==1)
	{
		$sql = "SELECT e.ID as LC_SC_ID, e.EXPORT_LC_NO as SC_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID as INV_ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, min(a.ID) as RLZ_ID, min(a.RECEIVED_DATE) as RECEIVED_DATE
		from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_export_lc e
		where a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=1 and a.is_invoice_bill=1 and d.id in ($invoice_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		group by e.ID, e.EXPORT_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE";		
	}
	else
	{
		$sql = "SELECT e.ID as LC_SC_ID, e.CONTRACT_NO as SC_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID as INV_ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, min(a.ID) as RLZ_ID, min(a.RECEIVED_DATE) as RECEIVED_DATE
		from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_sales_contract e
		where a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=2 and a.is_invoice_bill=1 and d.id in ($invoice_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		group by e.ID, e.CONTRACT_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE";	
	}*/
	$sql = "SELECT e.ID as LC_SC_ID, e.EXPORT_LC_NO as SC_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID as INV_ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, min(a.ID) as RLZ_ID, min(a.RECEIVED_DATE) as RECEIVED_DATE, d.IS_LC
	from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_export_lc e
	where a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=1 and a.is_invoice_bill=1 and d.id in ($invoice_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
	group by e.ID, e.EXPORT_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, d.IS_LC
	union all
	SELECT e.ID as LC_SC_ID, e.CONTRACT_NO as SC_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID as INV_ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, min(a.ID) as RLZ_ID, min(a.RECEIVED_DATE) as RECEIVED_DATE, d.IS_LC
	from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_sales_contract e
	where a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=2 and a.is_invoice_bill=1 and d.id in ($invoice_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
	group by e.ID, e.CONTRACT_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, d.IS_LC";
	
	//echo $sql;//die;
	$data_array=sql_select($sql);
	//$yarn_count_library = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$buyer_library = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');
	$i=1;
	foreach($data_array as $row)
	{
		
		if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		//echo $i.'='.$book_currency."<br>";
		$special_bgcolor=$euro_bgcolor=$general_bgcolor=$market_bgcolor="";
		if($privous_data[$row["INV_ID"]]["SPECIAL_INCENTIVE"]>0) $special_bgcolor=' bgcolor="#FF0000"';
		if($privous_data[$row["INV_ID"]]["EURO_ZONE_INCENTIVE"]>0) $euro_bgcolor=' bgcolor="#FF0000"';
		if($privous_data[$row["INV_ID"]]["GENERAL_INCENTIVE"]>0) $general_bgcolor=' bgcolor="#FF0000"';
		if($privous_data[$row["INV_ID"]]["MARKET_INCENTIVE"]>0) $market_bgcolor=' bgcolor="#FF0000"';
		if($current_data[$row["INV_ID"]]["RLZ_VALUE"]>0)
		{$rlzVal=$current_data[$row["INV_ID"]]["RLZ_VALUE"];}
		else
		{$rlzVal=$row["NET_INVO_VALUE"];}
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
			<td align="center"><? echo $i;?></td>
			<td id="buyer_<? echo $i; ?>" title="<?= $row["BUYER_NAME"]; ?>"><? echo $buyer_library[$row["BUYER_NAME"]];?></td>
			<td id="lcSc_<? echo $i; ?>" title="<?= $row["SC_LC_NO"]; ?>"><?= $row["SC_LC_NO"]; ?></td>
			<td id="billNo_<? echo $i; ?>" title="<?= $row["BANK_REF_NO"]; ?>"><?= $row["BANK_REF_NO"]; ?></td>
			<td id="expNo_<? echo $i; ?>" title="<?= $row["EXP_FORM_NO"]; ?>"><?= $row["EXP_FORM_NO"]; ?></td>
			<td id="invoiceNo_<? echo $i; ?>" title="<?= $row["INVOICE_NO"]; ?>"><?= $row["INVOICE_NO"]; ?></td>
			<td id="invoiceQnty_<? echo $i; ?>" align="right" title="<?= $row["INVOICE_QUANTITY"]; ?>"><?= number_format($row["INVOICE_QUANTITY"],0); ?></td>
			<td id="invoiceValue_<? echo $i; ?>" align="right" title="<?= $row["NET_INVO_VALUE"]; ?>"><?= number_format($row["NET_INVO_VALUE"],2); ?></td>
			<td id="rlzValue_<? echo $i; ?>" align="center"><input type="text" name="txtRlzValue[]" id="txtRlzValue_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<?=$rlzVal;?>" onKeyUp="fn_total_amt(0,<? echo $i; ?>)" /></td>
			<td id="rlzDate_<? echo $i; ?>" align="center" title="<?= $row["RECEIVED_DATE"]; ?>"><?= change_date_format($row["RECEIVED_DATE"]); ?></td>
			<td id="specialIncentive_<? echo $i; ?>" align="center" <?= $special_bgcolor;?> ><input type="text" name="txtspecialIncentive[]" id="txtspecialIncentive_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $current_data[$row["INV_ID"]]["SPECIAL_INCENTIVE"];?>" style="width:80px" title="<? if($privous_data[$row["INV_ID"]]["SPECIAL_INCENTIVE"]>0) echo $privous_data[$row["INV_ID"]]["SPECIAL_INCENTIVE"]; else echo "0"; ?>" readonly /></td>
			<td id="eurolIncentive_<? echo $i; ?>" align="center" <?= $euro_bgcolor;?> ><input type="text" name="txteuroIncentive[]" id="txteuroIncentive_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $current_data[$row["INV_ID"]]["EURO_ZONE_INCENTIVE"];?>" title="<? if($privous_data[$row["INV_ID"]]["EURO_ZONE_INCENTIVE"]>0) echo $privous_data[$row["INV_ID"]]["EURO_ZONE_INCENTIVE"]; else echo "0"; ?>" readonly /></td>
			<td id="generalIncentive_<? echo $i; ?>" align="center" <?= $general_bgcolor;?> ><input type="text" name="txtgeneralIncentive[]" id="txtgeneralIncentive_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $current_data[$row["INV_ID"]]["GENERAL_INCENTIVE"];?>" style="width:80px" title="<? if($privous_data[$row["INV_ID"]]["GENERAL_INCENTIVE"]>0) echo $privous_data[$row["INV_ID"]]["GENERAL_INCENTIVE"]; else echo "0"; ?>" readonly /></td>
			<td id="marketIncentive_<? echo $i; ?>" align="center" <?= $market_bgcolor;?> ><input type="text" name="txtmarketIncentive[]" id="txtmarketIncentive_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $current_data[$row["INV_ID"]]["MARKET_INCENTIVE"];?>" title="<? if($privous_data[$row["INV_ID"]]["MARKET_INCENTIVE"]>0) echo $privous_data[$row["INV_ID"]]["MARKET_INCENTIVE"]; else echo "0"; ?>" readonly />
            <input type="hidden" name="txtRlzId[]" id="txtRlzId_<? echo $i; ?>" class="text_boxes" value="<?= $row["RLZ_ID"]; ?>" />
            <input type="hidden" name="txtInoiceId[]" id="txtInoiceId_<? echo $i; ?>" class="text_boxes" value="<?= $row["INV_ID"]; ?>" />
            <input type="hidden" name="txtLcScId[]" id="txtLcScId_<? echo $i; ?>" class="text_boxes" value="<?= $row["LC_SC_ID"]; ?>" />
            <input type="hidden" name="txtIsLcSc[]" id="txtIsLcSc_<? echo $i; ?>" class="text_boxes" value="<?= $row["IS_LC"]; ?>" />
            <input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<? echo $i; ?>" class="text_boxes" value="" />
            </td>
		</tr>
		<?
		$i++;
    }
	exit();
}


if( $action == 'show_invoice_dtls_listview_update' ) 
{
	$data=explode("_",$data);
	//print_r($data);
	$invoice_ids=$data[0];
	$is_lc_sc=$data[1];
	$mst_id=$data[2];
	
	
	$update_cond="";
	if($mst_id!="") $update_cond=" and mst_id <> $mst_id";
	$previouse_entry_sql="SELECT SUBMISSION_BILL_ID, MST_ID, RLZ_VALUE, sum(SPECIAL_INCENTIVE) as SPECIAL_INCENTIVE, sum(EURO_ZONE_INCENTIVE) as EURO_ZONE_INCENTIVE, sum(GENERAL_INCENTIVE) as GENERAL_INCENTIVE, sum(MARKET_INCENTIVE) as MARKET_INCENTIVE 
	from CASH_INCENTIVE_SUBMISSION_DTLS where status_active=1 and is_deleted=0 and SUBMISSION_BILL_ID in($invoice_ids) $update_cond 
	group by SUBMISSION_BILL_ID, MST_ID, RLZ_VALUE";
	// echo $update_id."**".$previouse_entry_sql;
	$previouse_entry_sql_result=sql_select($previouse_entry_sql);
	$privous_data=array();$current_data=array();
	foreach($previouse_entry_sql_result as $val)
	{
		if($update_id!="" && $update_id==$val["MST_ID"])
		{
			$current_data[$val["SUBMISSION_BILL_ID"]]["SPECIAL_INCENTIVE"]+=$val["SPECIAL_INCENTIVE"];
			$current_data[$val["SUBMISSION_BILL_ID"]]["EURO_ZONE_INCENTIVE"]+=$val["EURO_ZONE_INCENTIVE"];
			$current_data[$val["SUBMISSION_BILL_ID"]]["GENERAL_INCENTIVE"]+=$val["GENERAL_INCENTIVE"];
			$current_data[$val["SUBMISSION_BILL_ID"]]["MARKET_INCENTIVE"]+=$val["MARKET_INCENTIVE"];
			$current_data[$val["SUBMISSION_BILL_ID"]]["RLZ_VALUE"]=$val["RLZ_VALUE"];
		}
		else
		{
			$privous_data[$val["SUBMISSION_BILL_ID"]]["SPECIAL_INCENTIVE"]+=$val["SPECIAL_INCENTIVE"];
			$privous_data[$val["SUBMISSION_BILL_ID"]]["EURO_ZONE_INCENTIVE"]+=$val["EURO_ZONE_INCENTIVE"];
			$privous_data[$val["SUBMISSION_BILL_ID"]]["GENERAL_INCENTIVE"]+=$val["GENERAL_INCENTIVE"];
			$privous_data[$val["SUBMISSION_BILL_ID"]]["MARKET_INCENTIVE"]+=$val["MARKET_INCENTIVE"];
		}
	}
	
	/*if($is_lc_sc==1)
	{
		$sql = "SELECT e.ID as LC_SC_ID, e.EXPORT_LC_NO as SC_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID as INV_ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, a.ID as RLZ_ID, a.RECEIVED_DATE, p.ID as UP_DTLS_ID, p.SPECIAL_INCENTIVE, p.EURO_ZONE_INCENTIVE, p.GENERAL_INCENTIVE, p.MARKET_INCENTIVE, p.RLZ_VALUE
		from cash_incentive_submission_dtls p, com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_export_lc e
		where p.REALIZATION_ID=a.id and p.SUBMISSION_BILL_ID=d.id and a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=1 and a.is_invoice_bill=1 and d.id in ($invoice_ids) and p.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and p.status_active=1 and p.is_deleted=0";		
	}
	else
	{
		$sql = "SELECT e.ID as LC_SC_ID, e.CONTRACT_NO as SC_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID as INV_ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, a.ID as RLZ_ID, a.RECEIVED_DATE, p.ID as UP_DTLS_ID, p.SPECIAL_INCENTIVE, p.EURO_ZONE_INCENTIVE, p.GENERAL_INCENTIVE, p.MARKET_INCENTIVE, p.RLZ_VALUE 
		from cash_incentive_submission_dtls p, com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_sales_contract e
		where p.REALIZATION_ID=a.id and p.SUBMISSION_BILL_ID=d.id and a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=2 and a.is_invoice_bill=1 and d.id in ($invoice_ids) and p.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and p.status_active=1 and p.is_deleted=0";	
	}*/
	
	$sql = "SELECT e.ID as LC_SC_ID, e.EXPORT_LC_NO as SC_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID as INV_ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, a.ID as RLZ_ID, a.RECEIVED_DATE, p.ID as UP_DTLS_ID, p.SPECIAL_INCENTIVE, p.EURO_ZONE_INCENTIVE, p.GENERAL_INCENTIVE, p.MARKET_INCENTIVE, p.RLZ_VALUE, d.IS_LC
	from cash_incentive_submission_dtls p, com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_export_lc e
	where p.REALIZATION_ID=a.id and p.SUBMISSION_BILL_ID=d.id and a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=1 and a.is_invoice_bill=1 and d.id in ($invoice_ids) and p.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and p.status_active=1 and p.is_deleted=0
	union all
	SELECT e.ID as LC_SC_ID, e.CONTRACT_NO as SC_LC_NO, e.BUYER_NAME, b.BANK_REF_NO, d.ID as INV_ID, d.EXP_FORM_NO, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, a.ID as RLZ_ID, a.RECEIVED_DATE, p.ID as UP_DTLS_ID, p.SPECIAL_INCENTIVE, p.EURO_ZONE_INCENTIVE, p.GENERAL_INCENTIVE, p.MARKET_INCENTIVE, p.RLZ_VALUE, d.IS_LC 
	from cash_incentive_submission_dtls p, com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_sales_contract e
	where p.REALIZATION_ID=a.id and p.SUBMISSION_BILL_ID=d.id and a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=2 and a.is_invoice_bill=1 and d.id in ($invoice_ids) and p.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and p.status_active=1 and p.is_deleted=0";
	
	//echo $sql;//die;
	$data_array=sql_select($sql);
	//$yarn_count_library = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$buyer_library = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');
	$i=1;
	foreach($data_array as $row)
	{
		
		if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$special_bgcolor=$euro_bgcolor=$general_bgcolor=$market_bgcolor="";
		if($privous_data[$row["INV_ID"]]["SPECIAL_INCENTIVE"]>0) $special_bgcolor=' bgcolor="#FF0000"';
		if($privous_data[$row["INV_ID"]]["EURO_ZONE_INCENTIVE"]>0) $euro_bgcolor=' bgcolor="#FF0000"';
		if($privous_data[$row["INV_ID"]]["GENERAL_INCENTIVE"]>0) $general_bgcolor=' bgcolor="#FF0000"';
		if($privous_data[$row["INV_ID"]]["MARKET_INCENTIVE"]>0) $market_bgcolor=' bgcolor="#FF0000"';
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
			<td align="center"><? echo $i;?></td>
			<td id="buyer_<? echo $i; ?>" title="<?= $row["BUYER_NAME"]; ?>"><? echo $buyer_library[$row["BUYER_NAME"]];?></td>
			<td id="lcSc_<? echo $i; ?>" title="<?= $row["SC_LC_NO"]; ?>"><?= $row["SC_LC_NO"]; ?></td>
			<td id="billNo_<? echo $i; ?>" title="<?= $row["BANK_REF_NO"]; ?>"><?= $row["BANK_REF_NO"]; ?></td>
			<td id="expNo_<? echo $i; ?>" title="<?= $row["EXP_FORM_NO"]; ?>"><?= $row["EXP_FORM_NO"]; ?></td>
			<td id="invoiceNo_<? echo $i; ?>" title="<?= $row["INVOICE_NO"]; ?>"><?= $row["INVOICE_NO"]; ?></td>
			<td id="invoiceQnty_<? echo $i; ?>" align="right" title="<?= $row["INVOICE_QUANTITY"]; ?>"><?= number_format($row["INVOICE_QUANTITY"],0); ?></td>
			<td id="invoiceValue_<? echo $i; ?>" align="right" title="<?= $row["NET_INVO_VALUE"]; ?>"><?= number_format($row["NET_INVO_VALUE"],2); ?></td>
			<td id="rlzValue_<? echo $i; ?>" align="center"><input type="text" name="txtRlzValue[]" id="txtRlzValue_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" onKeyUp="fn_total_amt(0,<? echo $i; ?>)" value="<? echo $row["RLZ_VALUE"];?>" /></td>
			<td id="rlzDate_<? echo $i; ?>" align="center" title="<?= $row["RECEIVED_DATE"]; ?>"><?= change_date_format($row["RECEIVED_DATE"]); ?></td>
			<td id="specialIncentive_<? echo $i; ?>" align="center" <?= $special_bgcolor;?> ><input type="text" name="txtspecialIncentive[]" id="txtspecialIncentive_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row["SPECIAL_INCENTIVE"];?>" title="0" readonly /></td>
			<td id="eurolIncentive_<? echo $i; ?>" align="center" <?= $euro_bgcolor;?> ><input type="text" name="txteuroIncentive[]" id="txteuroIncentive_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row["EURO_ZONE_INCENTIVE"];?>" title="0" readonly /></td>
			<td id="generalIncentive_<? echo $i; ?>" align="center" <?= $general_bgcolor;?> ><input type="text" name="txtgeneralIncentive[]" id="txtgeneralIncentive_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row["GENERAL_INCENTIVE"];?>" title="0" readonly /></td>
			<td id="marketIncentive_<? echo $i; ?>" align="center" <?= $market_bgcolor;?> ><input type="text" name="txtmarketIncentive[]" id="txtmarketIncentive_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row["MARKET_INCENTIVE"];?>" title="0" readonly />
            <input type="hidden" name="txtRlzId[]" id="txtRlzId_<? echo $i; ?>" class="text_boxes" value="<?= $row["RLZ_ID"]; ?>" />
            <input type="hidden" name="txtInoiceId[]" id="txtInoiceId_<? echo $i; ?>" class="text_boxes" value="<?= $row["INV_ID"]; ?>" />
            <input type="hidden" name="txtLcScId[]" id="txtLcScId_<? echo $i; ?>" class="text_boxes" value="<?= $row["LC_SC_ID"]; ?>" />
            <input type="hidden" name="txtIsLcSc[]" id="txtIsLcSc_<? echo $i; ?>" class="text_boxes" value="<?= $row["IS_LC"]; ?>" />
            <input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<? echo $i; ?>" class="text_boxes" value="<? echo $row["UP_DTLS_ID"];?>" />
            </td>
		</tr>
		<?
		$i++;
    }
	exit();
}



//========= End LC/SC No =========

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$mst_id=return_next_id("id", "CASH_INCENTIVE_SUBMISSION", 1);
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'CIS', date("Y",time()), 5, "SELECT sys_number_prefix,sys_number_prefix_num from cash_incentive_submission where company_id=$cbo_company_name $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));
		//submission_invoice_id, realization_id,
		//,".$submission_invoice_id.",".$realization_id." 
		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, entry_form, company_id, bank_id, submission_date, incentive_bank_file, net_realize_value, remarks, special_submitted_chk, euro_incentive_chk, general_incentive_chk, market_submitted_chk, special_submitted, euro_incentive, general_incentive, market_submitted, lc_sc_id, internal_file_no, file_no_string, total_net_weight, yarn_qnty, yarn_value, yarn_rate, over_head_charge, total_value, inserted_by, insert_date, status_active, is_deleted, days_to_realize, possible_reali_date,certificate_amount,sub_exchange_rate,audit_exchange_rate,loan_based_on,loan_value,loan_given_value,loan_no,loan_date";

		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',566,".$cbo_company_name.",".$cbo_bank_name.",".$txt_submission_date.",".$txt_incective_bank_file.",".$txt_net_realize_value.",".$txt_remarks.",".$special_submitted_chk.",".$euro_incentive_percent.",".$general_incentive_percent.",".$market_submitted_chk.",".$txt_total_special_incentive.",".$txt_total_euro_incentive.",".$txt_total_general_incentive.",".$txt_total_market_incentive.",".$lc_sc_id.",".$txt_file_no.",".$txt_file_no_string.",".$txt_net_weight.",".$txt_yarn_qnty.",".$txt_yarn_value.",".$txt_yarn_rate.",".$txt_over_head_charge.",".$txt_total_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$txt_day_to_realize.",".$txt_possible_reali_date.",".$txt_certificate_amount.",".$txt_sub_exchange_rate.",".$txt_audit_exchange_rate.",".$cbo_loan_value.",".$txt_loan.",".$txt_loan_given.",".$txt_loan_no.",".$txt_loan_date.")";
		
		
		$txt_file_no_string=str_replace("'","",$txt_file_no_string);
		$txt_file_no_string_arr=explode("*",$txt_file_no_string);
		$dtls_id=return_next_id("id", "cash_incentive_submission_dtls", 1);
		$field_array_dtls="id, mst_id, submission_bill_id, realization_id, lc_sc_id, is_lc_sc, special_incentive, euro_zone_incentive, general_incentive, market_incentive, rlz_value, inserted_by, insert_date, status_active, is_deleted";
		for($i=1; $i<=$tot_row; $i++)
		{
			$txtRlzValue="txtRlzValue".$i;
			$txtspecialIncentive="txtspecialIncentive".$i;
			$txteuroIncentive="txteuroIncentive".$i;
			$txtgeneralIncentive="txtgeneralIncentive".$i;
			$txtmarketIncentive="txtmarketIncentive".$i;
			
			$txtRlzId="txtRlzId".$i;
			$txtInoiceId="txtInoiceId".$i;
			$txtLcScId="txtLcScId".$i;
			$txtIsLcSc="txtIsLcSc".$i;
			$updateDtlsId="updateDtlsId".$i;
			
			$data_array_dtls.="(".$dtls_id.",'".$mst_id."','".$$txtInoiceId."','".$$txtRlzId."','".$$txtLcScId."','".$$txtIsLcSc."','".$$txtspecialIncentive."','".$$txteuroIncentive."','".$$txtgeneralIncentive."','".$$txtmarketIncentive."','".$$txtRlzValue."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$dtls_id++;
		}
		//echo "10** insert into cash_incentive_submission_dtls ($field_array_dtls) values $data_array_dtls";oci_rollback($con);disconnect($con);die;
		$rID=sql_insert("cash_incentive_submission",$field_array_mst,$data_array_mst,0);
		$rID2=sql_insert("cash_incentive_submission_dtls",$field_array_dtls,$data_array_dtls,0);
		//echo '10**'.$rID."=".$rID2;oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$new_sys_no[0]."**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//*submission_invoice_id*realization_id
		//*".$submission_invoice_id."*".$realization_id."
		$field_array_mst="company_id*bank_id*submission_date*incentive_bank_file*net_realize_value*remarks*special_submitted_chk*euro_incentive_chk*general_incentive_chk*market_submitted_chk*special_submitted*euro_incentive*general_incentive*market_submitted*lc_sc_id*internal_file_no*file_no_string*total_net_weight*yarn_qnty*yarn_value*yarn_rate*over_head_charge*total_value*updated_by*update_date*days_to_realize*possible_reali_date*certificate_amount*sub_exchange_rate*audit_exchange_rate*loan_based_on*loan_value*loan_given_value*loan_no*loan_date";
		$data_array_mst="".$cbo_company_name."*".$cbo_bank_name."*".$txt_submission_date."*".$txt_incective_bank_file."*".$txt_net_realize_value."*".$txt_remarks."*".$special_submitted_chk."*".$euro_incentive_percent."*".$general_incentive_percent."*".$market_submitted_chk."*".$txt_total_special_incentive."*".$txt_total_euro_incentive."*".$txt_total_general_incentive."*".$txt_total_market_incentive."*".$lc_sc_id."*".$txt_file_no."*".$txt_file_no_string."*".$txt_net_weight."*".$txt_yarn_qnty."*".$txt_yarn_value."*".$txt_yarn_rate."*".$txt_over_head_charge."*".$txt_total_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_day_to_realize."*".$txt_possible_reali_date."*".$txt_certificate_amount."*".$txt_sub_exchange_rate."*".$txt_audit_exchange_rate."*".$cbo_loan_value."*".$txt_loan."*".$txt_loan_given."*".$txt_loan_no."*".$txt_loan_date."";
		
		$txt_file_no_string=str_replace("'","",$txt_file_no_string);
		$txt_file_no_string_arr=explode("*",$txt_file_no_string);
		$dtls_id=return_next_id("id", "cash_incentive_submission_dtls", 1);
		$field_array_dtls="id, mst_id, submission_bill_id, realization_id, lc_sc_id, is_lc_sc, special_incentive, euro_zone_incentive, general_incentive, market_incentive, rlz_value, inserted_by, insert_date, status_active, is_deleted";
		//echo "10**$tot_row"; oci_rollback($con);disconnect($con);die;
		for($i=1; $i<=$tot_row; $i++)
		{
			$txtRlzValue="txtRlzValue".$i;
			$txtspecialIncentive="txtspecialIncentive".$i;
			$txteuroIncentive="txteuroIncentive".$i;
			$txtgeneralIncentive="txtgeneralIncentive".$i;
			$txtmarketIncentive="txtmarketIncentive".$i;
			
			$txtRlzId="txtRlzId".$i;
			$txtInoiceId="txtInoiceId".$i;
			$txtLcScId="txtLcScId".$i;
			$txtIsLcSc="txtIsLcSc".$i;
			$updateDtlsId="updateDtlsId".$i;
			
			$data_array_dtls.="(".$dtls_id.",".$update_id.",'".$$txtInoiceId."','".$$txtRlzId."','".$$txtLcScId."','".$$txtIsLcSc."','".$$txtspecialIncentive."','".$$txteuroIncentive."','".$$txtgeneralIncentive."','".$$txtmarketIncentive."','".$$txtRlzValue."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$dtls_id++;
		}
		
		
		//echo "10**update cash_incentive_submission_dtls set update_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id = $update_id ";oci_rollback($con);disconnect($con);die;
		$field_array_del="status_active*is_deleted*update_by*update_date";
		$data_array_del="7*8*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=$rID3=$rID2=true;
		$rID=sql_update("CASH_INCENTIVE_SUBMISSION",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID3=sql_delete("cash_incentive_submission_dtls",$field_array_del,$data_array_del,"mst_id","".$update_id."",0);
		//$rID3=execute_query("update cash_incentive_submission_dtls set update_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."', status_active=7, is_deleted=8 where mst_id = $update_id ");
		$rID2=sql_insert("cash_incentive_submission_dtls",$field_array_dtls,$data_array_dtls,0);
		//echo "10**$rID=$rID2=$rID3="; oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
	
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$cash_incentive_rcv_sql=sql_select("SELECT SYS_NUMBER from CASH_INCENTIVE_RECEIVED_MST where STATUS_ACTIVE=1 and CASH_INCENTIVE_SUB_ID=$update_id ");
		if(count($cash_incentive_rcv_sql)>0)
		{
			echo "404**Delete is not allow. Cash Incentive Received Entry: ".$cash_incentive_rcv_sql[0]["SYS_NUMBER"];disconnect($con);die;
		}
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_del="status_active*is_deleted*update_by*update_date";
		$data_array_del="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$rID=sql_delete("CASH_INCENTIVE_SUBMISSION",$field_array,$data_array,"id","".$update_id."",0);
		$rID2=sql_delete("cash_incentive_submission_dtls",$field_array_del,$data_array_del,"mst_id","".$update_id."",0);
		//$rID2=execute_query("update cash_incentive_submission_dtls set update_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id = $update_id ");

		// echo "10**".$rID."</br>"; die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}// Delete Here End ----------------------------------------------------------
	
}

if($action=="print_generate")
{
	$data_arr=explode("*",$data);
	$company_id=$data_arr[0];
	$mst_id=$data_arr[1];

	$supplier_arr=return_library_array( "select supplier_name,id from  lib_supplier",'id','supplier_name');
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");

	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data)
  	{
		$company_arr[$company_data[csf('id')]]=$company_data[csf('company_name')];
		if($company_data[csf('plot_no')]!='') $plot_no = $company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='') $level_no = $company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='') $road_no = $company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='') $block_no = $company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='') $city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='') $zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0) $country = $country_arr[$company_data[csf('country_id')]].','.' ';else $country='';
	}

	$mst_sql="SELECT a.id as ID, a.bank_id as BANK_ID, a.submission_date as SUBMISSION_DATE, a.INCENTIVE_BANK_FILE, a.REMARKS, a.is_lc_sc as IS_LC_SC, a.lc_sc_id as LC_SC_ID, a.realization_id as REALIZATION_ID, a.submission_invoice_id as SUBMISSION_INVOICE_ID, a.net_realize_value as NET_REALIZE_VALUE, a.SPECIAL_SUBMITTED_CHK, a.MARKET_SUBMITTED_CHK, a.EURO_INCENTIVE_CHK, a.GENERAL_INCENTIVE_CHK, b.RLZ_VALUE, b.SPECIAL_INCENTIVE, b.EURO_ZONE_INCENTIVE, b.GENERAL_INCENTIVE, b.MARKET_INCENTIVE, c.invoice_no as INVOICE_NO, c.exp_form_no as EXP_FORM_NO, c.NET_INVO_VALUE, c.INVOICE_DATE, d.received_date as REALIZATION_DATE
	from cash_incentive_submission a, cash_incentive_submission_dtls b,com_export_invoice_ship_mst c, com_export_proceed_realization d
	where a.id=b.mst_id and c.id=b.submission_bill_id and b.realization_id=d.id and a.id=$mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1";
	//echo $mst_sql;

	$result=sql_select($mst_sql);
	$bank_id=$result[0]["BANK_ID"];
	$realization_id=$result[0]["REALIZATION_ID"];
	$for_par="(";
	if($result[0]["SPECIAL_SUBMITTED_CHK"]){$for_par.="for Special Incentive 1%, ";}
	if($result[0]["MARKET_SUBMITTED_CHK"]){$for_par.="for New Market 4%, ";}
	if($result[0]["EURO_INCENTIVE_CHK"]){$for_par.="for Euro Zone ".$result[0]["EURO_INCENTIVE_CHK"]."%, ";}
	if($result[0]["GENERAL_INCENTIVE_CHK"]){$for_par.="for General Incentive ".$result[0]["GENERAL_INCENTIVE_CHK"]."%, ";}
	$for_par.=")";
	$sql_bank_info=sql_select("SELECT id, bank_name, branch_name, address from lib_bank where id=$bank_id");
	$bank_name=$sql_bank_info[0]["BANK_NAME"];
	$bank_branch=$sql_bank_info[0]["BRANCH_NAME"];

	$sql = "SELECT d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT,e.id as LC_SC_ID, e.export_lc_no as SC_LC_NO,e.lc_value as SC_LC_VALUE ,e.bank_file_no as BANK_FILE_NO, e.lc_year as SC_LC_YEAR, e.lc_date as LC_SC_DATE,e.APPLICANT_NAME
	from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d,com_export_lc e
	where a.id in ($realization_id) and a.invoice_bill_id=c.id and a.is_invoice_bill=1 and c.id=d.doc_submission_mst_id and d.lc_sc_id=e.id and d.is_lc=1 and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1
	union all
	select d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT,e.id as LC_SC_ID, e.contract_no as SC_LC_NO,e.contract_value as SC_LC_VALUE,e.bank_file_no as BANK_FILE_NO, e.sc_year as SC_LC_YEAR, e.contract_date as LC_SC_DATE,e.APPLICANT_NAME
	from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d,com_sales_contract e
	where a.id in ($realization_id) and a.invoice_bill_id=c.id and a.is_invoice_bill=1 and c.id=d.doc_submission_mst_id and d.lc_sc_id=e.id and d.is_lc=2 and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1";

	$sql_result=sql_select($sql);
	$lc_sc_no=$lc_sc_year=$lc_sc_applicant=$lc_sc_bank_file=''; $lc_sc_value=array(); $bill_value=array();
	foreach($sql_result as $rows){
		$lc_sc_no.=$rows['SC_LC_NO'].',';
		$lc_sc_year.=$rows['SC_LC_YEAR'].',';
		$lc_sc_bank_file.=$rows['BANK_FILE_NO'].',';
		if($rows['APPLICANT_NAME']){$lc_sc_applicant.=$supplier_arr[$rows['APPLICANT_NAME']].',';}
		$lc_sc_value[$rows['LC_SC_ID']]=$rows['SC_LC_VALUE'];
		$lc_sc_date[$rows['LC_SC_ID']]=$rows['LC_SC_DATE'];
		$bill_value[$rows['SUB_DTLS_ID']]=$rows['BILL_AMNT'];
	}
	$lc_sc_no=implode(", ",array_unique(explode(",",chop($lc_sc_no,','))));
	$lc_sc_date=implode(", ",$lc_sc_date);
	$lc_sc_value=array_sum($lc_sc_value);
	$lc_sc_applicant=implode(", ",array_unique(explode(",",chop($lc_sc_applicant,','))));
	$lc_sc_year=implode(", ",array_unique(explode(",",chop($lc_sc_year,','))));
	$lc_sc_bank_file=implode(", ",array_unique(explode(",",chop($lc_sc_bank_file,','))));
	$bill_value=array_sum($bill_value);
	

	?>

		<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 23cm;
	           /* font-family: Bookman Old Style; */
	        }
			.headfooter{
				margin: 0px;
			  padding: 0px;
			}
		</style>
		<div class="a4size">
		    <table width="794" cellpadding="0" cellspacing="0" border="0" >
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <br>
		        <tr>
		            <td width="25"></td>
		            <td width="650" align="left"><?=$company_arr[$company_id];?></td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		           	<? echo trim($plot_no.$level_no,', '); ?><br>
					<? echo trim($road_no.$block_no,', '); ?><br>
					<? echo trim($city.$zip_code.$country,', '); ?>					
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>
				<tr>
		            <td width="25" ></td>
		            <td width="650" align="left">Bank ref.<?=$result[0]["INCENTIVE_BANK_FILE"];?> </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="center"><span style="font-size: 18px;"><strong><u>Certificate of Cash Assistance</u></strong> </span></br>[Textile]</td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left"> The application of <?=$lc_sc_applicant;?>, dated <?=change_date_format($result[0]["SUBMISSION_DATE"]);?> related to Export Contact No. <?=$lc_sc_no;?>, dated <?=$lc_sc_date;?>, value USD <?=number_format($lc_sc_value,2);?> of <?=$bank_name;?>, <?=$bank_branch?>, having export value of USD <?=number_format($bill_value,2);?> against which Exp. number, realized value and realized date are as under:</td>
		            <td width="25" ></td>
		        </tr>
				<tr>
	        		<td colspan="3" height="15"></td>
	        	</tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="justify">
						<table width="620" cellpadding="0" cellspacing="0" border="1" >
							<thead>
								<tr>
									<th width="160">Exp. no.</th>
									<th width="110">Export value USD</th>
									<th width="110">Realized value USD</th>
									<th width="110">Realized date</th>
									<th>Shipment date</th>
								</tr>
							</thead>
							<tbody>
							<?
								foreach($result as $row)
								{
									?>
									<tr>
										<td><?=$row["EXP_FORM_NO"];?></td>
										<td><?=number_format($row["NET_INVO_VALUE"],2);?></td>
										<td><?=number_format($row["RLZ_VALUE"],2);?></td>
										<td><?=change_date_format($row["REALIZATION_DATE"]);?></td>
										<td><?=change_date_format($row["INVOICE_DATE"]);?></td>
									</tr>
									<?
									$tot_net_invo_value+=$row["NET_INVO_VALUE"];
									$tot_rlz_value+=$row["RLZ_VALUE"];
									$tot_incentive_value+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
								}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th>Total</th>
									<th><?=number_format($tot_net_invo_value,2);?></th>
									<th><?=number_format($tot_rlz_value,2);?></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
		            </td>
		            <td width="25" ></td>
		        </tr>
			    <tr>
	        		<td colspan="3" height="15"></td>
	        	</tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">Relating to this, the amount of cash assistance claim is </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">Based on our audit in assordance with TOR, the amount of eligible cash assistance <?=$for_par;?> is USD <?=number_format($tot_incentive_value,2)." (".number_to_words(number_format($tot_incentive_value,2)).")";?> only, which is true and correct.</td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="70"></td>
		        </tr>
				<tr>
		            <td width="25" ></td>
		            <td width="650" >
						<div style="clear:both;">
							<div style="float:left">
								Financial year: <?=$lc_sc_year;?></br>
								Certificate No. <?=$result[0]["REMARKS"];?>
							</div>
							<div style="float:right"><?=$company_arr[$company_id];?></br><?=date("d M Y");?> </div>
						</div>
					</td>
		            <td width="25" ></td>
		        </tr>
		    </table>
		</div>

	<footer style="width:794; font-size:80%;">
		<div align="center">
			<p class="headfooter" >Cash Assistance Certificate issued for <?=$bank_name;?>, <?=$bank_branch?></p>
		</div>
	</footer>
	<?
	exit();
}

if($action=="print_generate2")
{
	$data_arr=explode("*",$data);
	$company_id=$data_arr[0];
	$mst_id=$data_arr[1];

	$supplier_arr=return_library_array( "select supplier_name,id from  lib_supplier",'id','supplier_name');
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data)
  	{
		$company_arr[$company_data[csf('id')]]=$company_data[csf('company_name')];
		if($company_data[csf('plot_no')]!='') $plot_no = $company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='') $level_no = $company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='') $road_no = $company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='') $block_no = $company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='') $city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='') $zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0) $country = $country_arr[$company_data[csf('country_id')]].','.' ';else $country='';
	}

	$mst_sql="SELECT a.id as ID, a.bank_id as BANK_ID, a.submission_date as SUBMISSION_DATE, a.INCENTIVE_BANK_FILE, a.REMARKS, a.is_lc_sc as IS_LC_SC, a.lc_sc_id as LC_SC_ID, b.realization_id as REALIZATION_ID, a.submission_invoice_id as SUBMISSION_INVOICE_ID, a.net_realize_value, a.SPECIAL_SUBMITTED_CHK, a.MARKET_SUBMITTED_CHK, a.EURO_INCENTIVE_CHK, a.GENERAL_INCENTIVE_CHK, b.RLZ_VALUE,a.total_value, b.special_incentive, b.EURO_ZONE_INCENTIVE, b.GENERAL_INCENTIVE, b.MARKET_INCENTIVE, c.invoice_no as INVOICE_NO, c.exp_form_no as EXP_FORM_NO, c.net_invo_value, c.INVOICE_DATE, d.received_date as REALIZATION_DATE,a.INTERNAL_FILE_NO,
	b.LC_SC_ID,a.sub_exchange_rate,b.euro_zone_incentive,b.general_incentive,b.market_incentive,a.sub_exchange_rate,a.loan_given_value,a.special_submitted_chk,a.euro_incentive_chk,a.general_incentive_chk,a.market_submitted_chk,a.company_id,a.loan_value from cash_incentive_submission a, cash_incentive_submission_dtls b,com_export_invoice_ship_mst c, com_export_proceed_realization d
	where a.id=b.mst_id and c.id=b.submission_bill_id and b.realization_id=d.id and a.id=$mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1";
	//echo $mst_sql;

	$result=sql_select($mst_sql);

	foreach($result as $row)
	{
		$total_special_incentive+=$row[csf('special_incentive')];
		$sub_exchange_rate=$row[csf('sub_exchange_rate')];
		$total_euro_zone_incentive+=$row[csf('euro_zone_incentive')];
		$total_general_incentive+=$row[csf('general_incentive')];
		$total_market_incentive+=$row[csf('market_incentive')];
		$total_value=$total_special_incentive+$total_euro_zone_incentive+$total_general_incentive+$total_market_incentive;
		$LC_SC_ID.=$row['LC_SC_ID'].",";
		$realization_id.=$row['REALIZATION_ID'].",";
		$net_invo_value+=$row[csf('net_invo_value')];
		$rlz_value+=$row[csf('rlz_value')];
		$total_val=$row[csf('total_value')];
		$net_realize_value=$row[csf('net_realize_value')];
		$sub_exchange_rate=$row[csf('sub_exchange_rate')];
		$loan_given_value=$row[csf('loan_given_value')];
		$special_submitted_chk=$row[csf('special_submitted_chk')];
		$euro_incentive_chk=$row[csf('euro_incentive_chk')];
		$general_incentive_chk=$row[csf('general_incentive_chk')];
		$market_submitted_chk= $row["MARKET_SUBMITTED_CHK"];
		$company_id=$row[csf('company_id')];
		$loan_value=$row[csf('loan_value')];
		
	}
	$realization_ids=implode(", ",array_unique(explode(",",chop($realization_id,','))));
	$LC_SC_IDs=implode(", ",array_unique(explode(",",chop($LC_SC_ID,','))));
	$bank_id=$result[0]["BANK_ID"];
	$sql_bank_info=sql_select("SELECT id, bank_name, branch_name, address from lib_bank where id=$bank_id");
	$bank_name=$sql_bank_info[0]["BANK_NAME"];
	$bank_branch=$sql_bank_info[0]["BRANCH_NAME"];
	$address=$sql_bank_info[0]["ADDRESS"];
	

    $sql = "SELECT d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT,e.id as LC_SC_ID, e.export_lc_no as SC_LC_NO,e.lc_value as SC_LC_VALUE ,e.bank_file_no as BANK_FILE_NO, e.lc_year as SC_LC_YEAR, e.lc_date as LC_SC_DATE,e.APPLICANT_NAME,c.BANK_REF_NO,c.SUBMIT_DATE
	from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d,com_export_lc e
	where a.id in ($realization_ids) and a.invoice_bill_id=c.id and a.is_invoice_bill=1 and c.id=d.doc_submission_mst_id and d.lc_sc_id=e.id and d.is_lc=1 and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1
	union all
	select d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT,e.id as LC_SC_ID, e.contract_no as SC_LC_NO,e.contract_value as SC_LC_VALUE,e.bank_file_no as BANK_FILE_NO, e.sc_year as SC_LC_YEAR, e.contract_date as LC_SC_DATE,e.APPLICANT_NAME,c.BANK_REF_NO,c.SUBMIT_DATE
	from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d,com_sales_contract e
	where a.id in ($realization_ids) and a.invoice_bill_id=c.id and a.is_invoice_bill=1 and c.id=d.doc_submission_mst_id and d.lc_sc_id=e.id and d.is_lc=2 and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1";

	$sql_result=sql_select($sql);
	$lc_sc_no=$lc_sc_year=$lc_sc_applicant=$lc_sc_bank_file=''; $lc_sc_value=array(); $bill_value=array();
	foreach($sql_result as $rows){
		$lc_sc_no.=$rows['SC_LC_NO'].',';
		$lc_sc_year.=$rows['SC_LC_YEAR'].',';
		$lc_sc_bank_file.=$rows['BANK_FILE_NO'].',';
		if($rows['APPLICANT_NAME']){$lc_sc_applicant.=$supplier_arr[$rows['APPLICANT_NAME']].',';}
		$lc_sc_value[$rows['LC_SC_ID']]=$rows['SC_LC_VALUE'];
		$lc_sc_date[$rows['LC_SC_ID']]=$rows['LC_SC_DATE'];
		$bill_value[$rows['SUB_DTLS_ID']]=$rows['BILL_AMNT'];
		$bill_no.=$rows['BANK_REF_NO'];
	}
	$lc_sc_no=implode(", ",array_unique(explode(",",chop($lc_sc_no,','))));
	$lc_sc_date=implode(", ",$lc_sc_date);
	$lc_sc_value=array_sum($lc_sc_value);
	$lc_sc_applicant=implode(", ",array_unique(explode(",",chop($lc_sc_applicant,','))));
	$lc_sc_year=implode(", ",array_unique(explode(",",chop($lc_sc_year,','))));
	$lc_sc_bank_file=implode(", ",array_unique(explode(",",chop($lc_sc_bank_file,','))));
	$bill_value=array_sum($bill_value);

	?>
	<style>
		@media print {
		.a4size{
			width: 18cm;
			height: 10.7cm;
			margin: 40mm 25mm 40mm 10mm; 
	} 

	}
	</style>
   <div class="a4size">

	<table width="900" style="margin-top: 100px; border-collapse:collapse">
			<tr>
				<td width="390px">Date :&nbsp;<?echo change_date_format(date("d-m-Y"));?></td>
				<td style="font-size: 16px;"><b>File No.&nbsp;<? echo $result[0]['INTERNAL_FILE_NO']?></b></td>
			</tr>
			<tr>
				<td >To,</td>
				
			</tr>
			<tr>
				<td>The General Manager</td>
			</tr>
			<tr>
				<td><?echo $bank_name?></td>
			</tr>
			<tr>
				<td><?echo $bank_branch?></td>
			</tr>
			<tr>
				<td><?echo $address?></td>
			</tr>
			<tr height="15px"></tr>
			<tr>
				<td colspan="2">Sub: Proposal for cash incentive of BDT <?echo number_format($total_value*$sub_exchange_rate,2)?>/- against export USD <?echo number_format($total_value,2)?> @<?
				if ($market_submitted_chk==1)
				{
					$market_submitted=4;

				}
				else{
					$market_submitted="";

				}
				if($special_submitted_chk==1)
				{
					$special_submitted=1;

				}
				else{
					$special_submitted="";

				}
				$values = [$special_submitted, $euro_incentive_chk, $general_incentive_chk, $market_submitted];
				$output = '';

				foreach ($values as $value) {
					if (!empty($value)) {
						$output .= $value . "%, ";
					}
				}
				$output = rtrim($output, ', ');
				echo $output;
	
				?>

				of 80%</td>
			</tr>
			<tr height="15px"></tr>
			<tr><td>Muhtaram</td></tr>
			<tr><td>Assalamu Alikum</td></tr>
			<tr height="15px"></tr>
			<tr>
				<td colspan="2 ">With reference to the above we do here submit the following cash incentive proposal for your kind persual:</td>
			</tr>
	</table>

	
	<table width="900"  style="margin-top: 15px; border-collapse:collapse">

			<tr>
				<td width="10">01.</td>
				<td width="200">Export LC/Contract No.</td>
				<td width="10">:</td>
				<td><?echo $lc_sc_no?></td>
			</tr>
			<tr>
				<td width="10">02.</td>
				<td width="200">Export Value</td>
				<td width="10">:</td>
				<td><?echo "$".number_format($net_invo_value,2)?></td>
			</tr>
			<tr>
				<td width="10">03.</td>
				<td width="200">Proceeds Realized Value</td>
				<td width="10">:</td>
				<td><?echo "$".number_format($rlz_value,2)?></td>
			</tr>
			<tr>
				<td width="10">04.</td>
				<td width="200"> Proceeds Realization Date</td>
				<td width="10">:</td>
				<td></td>
			</tr>
			<tr>
				<td width="10">05.</td>
				<td width="200">Total Cost of Fabrics</td>
				<td width="10">:</td>
				<td><? echo "$".number_format($total_val,2)?></td>
			</tr>
			<tr>
				<td width="10">06.</td>
				<td width="200">Admissible Cost For Incentive</td>
				<td width="10">:</td>
				<td><? echo "$".number_format(($rlz_value*.8),2)?></td>
			</tr>
			<tr>
				<td width="10">07.</td>
				<td width="200">Rate Of Cash Incentive</td>
				<td width="10">:</td>
				<td><?
				if ($market_submitted_chk==1)
				{
					$market_submitted=4;

				}
				else{
					$market_submitted="";

				}
				if($special_submitted_chk==1)
				{
					$special_submitted=1;

				}
				else{
					$special_submitted="";

				    }
					$values = [$special_submitted, $euro_incentive_chk, $general_incentive_chk, $market_submitted];
					$output = '';
					foreach ($values as $value) {
						if (!empty($value)) {
							$output .= $value . "%, ";
						}
					}
					$output = rtrim($output, ', ');
					echo $output;

				?></td>
			</tr>
			<tr>
				<td width="10">08.</td>
				<td width="200">Cash Incentive In Fc.</td>
				<td width="10">:</td>
				<td><?echo "$".number_format($total_value,2)?></td>
			</tr>
			<tr>
				<td width="10">09.</td>
				<td width="200">Exchange Rate</td>
				<td width="10">:</td>
				<td><? echo $sub_exchange_rate?></td>
			</tr>
			<tr>
				<td width="10">10.</td>
				<td width="200"> Cash Incentive Claim In BDT</td>
				<td width="10">:</td>
				<td><?echo number_format($total_value*$sub_exchange_rate,2)."/-"?></td>
			</tr>
			<tr>
				<td width="10">11.</td>
				<td width="200">Pre-audit Proposal @<?echo  $loan_value?>%</td>
				<td width="10">:</td>
				<td><?echo number_format($loan_given_value,2)."/-"?></td>
			</tr>
			<tr height="15"></tr>
			<tr>
				<td colspan="4">
				You are therefore requisted to arrange disbursement of above mentioned cash incentive at your earliest <br>	convenience.	
				</td>
			</tr>
			<tr height="15"></tr>
			<tr><td colspan="2">Yours faithfully	</td></tr>
			<tr height="15"></tr>
			<tr>
			<td colspan="4"><?echo $company_arr[$company_id]?></td>
			</tr>

	 </table>

	<div style="width: 900px; display:flex;">

		<div style="width: 300px; margin-top:5px">
									
		<table  style="margin-left: 30px;" >
			<tr><td>Enclosed:</td></tr>
			<tr>
				<td>1</td>
				<td>)Form-Kha</td>
			</tr>
			<tr>
				<td>2</td>
				<td>)Cost Sheet</td>
			</tr>
			<tr>
				<td>3</td>
				<td>)BGMEA Certificate</td>
			</tr>
			<tr>
				<td>4</td>
				<td>)Export L/C</td>
			</tr>
			<tr>
				<td>5</td>
				<td>)Commercial Invoice (Export)</td>
			</tr>
			<tr>
				<td>6</td>
				<td>)Packing List (export)</td>
			</tr>
			<tr>
				<td>7</td>
				<td>)Bill Of Landing</td>
			</tr>
			<tr>
				<td>8</td>
				<td>)Bill Of Entry/Shipping Bill</td>
			</tr>
			<tr>
				<td>9</td>
				<td>)EXP Form</td>
			</tr>
			<tr>
				<td>10</td>
				<td>)BTB L/C</td>
			</tr>
			<tr>
				<td>11</td>
				<td>)PI</td>
			</tr>
			<tr>
				<td>12</td>
				<td>)Bank Certificate</td>
			</tr>
			<tr>
				<td>13</td>
				<td>)BTMA</td>
			</tr>
			<tr>
				<td>14</td>
				<td>)Mushok-11</td>
			</tr>
			<tr>
				<td>15</td>
				<td>)Commercial Invoice (Yarn)</td>
			</tr>
			<tr>
				<td>16</td>
				<td>)Packing List (yarn)</td>
			</tr>
			<tr>
				<td>17</td>
				<td>)Delivery Challan/ Truck Receipt (Yarn)</td>
			</tr>
			<tr>
				<td>18</td>
				<td>)Certificate Of Origin (Yarn)</td>
			</tr>
			<tr>
				<td>19</td>
				<td>)Beneficiary Certificate (Yarn)</td>
			</tr>
			<tr>
				<td>20</td>
				<td>)UD</td>
			</tr>
			<tr>
				<td>21</td>
				<td>)Bond/Undertaking On Judicial Stamp</td>
			</tr>
			</table>
		
		</div>
		<div style="width: 300px;margin-top:30px ;margin-left:90px" >
		Export Bill No.:
            <table border="1"  cellpadding="0" cellspacing="0">
			
				<tr>
					<td width="10">SL</td>
					<td>FDBC NO.</td>
					<td>Date</td>
				</tr>
				<?
				$i=1;
				$seen_values = []; // Array to keep track of seen values
				$unique_count = 0; // Counter for unique values
				foreach ($sql_result as $rows) {
					// Check if the current value has been seen before
					if (!in_array($rows["BANK_REF_NO"], $seen_values)) {
						// Increment unique count and print the row only if the BANK_REF_NO is unique
						$unique_count++;
						?>
						<tr>
							<td width="10"><?php echo $unique_count ?></td>
							<td><?php echo $rows["BANK_REF_NO"]?></td>
							<td><?php echo change_date_format($rows["SUBMIT_DATE"])?></td>
						</tr>
						<?php
						// Add the current BANK_REF_NO to the seen values list
						$seen_values[] = $rows["BANK_REF_NO"];
					}
			}
      ?>



				</table>


			</div>

		</div>

		</div>

		<?
		

	




}
