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
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
                    <th >Buyer Name</th>
                    <th >Search By</th>
                    <th >LC/SC No</th>
                    <th>System ID</th>
                    <th colspan="2">Received Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="id_field" id="id_field" value="" /></th>
                </tr>        
            </thead>
            <tbody>
                <tr class="general">
                    <td class="must_entry_caption"> 
                        <input type="hidden" id="selected_id">
                        <? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, '',1);?>
                    </td>
                    <td>
						<?
                            echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id  and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$cbo_company_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
                        ?>
                    </td>
                    <td>
                        <? $is_arr=array(1=>'LC',2=>'SC');
                            echo create_drop_down( "cbo_search_by", 80, $is_arr,"",0, "--Select--", "",'',0 );
                        ?>
                    </td>
                    <td >
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_lc_sc" id="txt_lc_sc" />
                    </td>
                    <td><input name="txt_sys" id="txt_sys" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date"></td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_lc_sc').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_sys').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_system_search_list_view', 'search_div', 'cash_incentive_received_controller', 'setFilterGrid(\'search_div\',-1)');" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="8"><? echo load_month_buttons(1); ?></td>
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
	//echo $data;die;
	$com_cond="";$date_cond ="";$year_cond="";$search_text="";$search_lc_sc_no="";
    list($company_id,$search_by,$lc_sc_no,$received_start_date, $received_end_date,$year,$string_search_by,$search_sys,$buyer_id) = explode('_', $data);

	if ($company_id!=0) {$com_cond=" and a.company_id=$company_id";}
	if ($buyer_id!=0) {$buyer_cond=" and d.BUYER_NAME=$buyer_id";}
	if($search_by == 1)
	{
        if ($lc_sc_no!='') {$search_lc_sc_no="and d.export_lc_no like '%".trim($lc_sc_no)."%'";}
	}else{
        if ($lc_sc_no!='') {$search_lc_sc_no="and d.contract_no like '%".trim($lc_sc_no)."%'";}
	}

	if ($submission_start_date != '' && $submission_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and a.received_date '" . change_date_format($submission_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($submission_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and a.received_date between '" . change_date_format($submission_start_date, '', '', 1) . "' and '" . change_date_format($submission_end_date, '', '', 1) . "'";
		}
    } 
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.received_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.received_date,'YYYY') =$year ";
			}
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

	
	$sql="select A.ID, A.COMPANY_ID, A.SYS_NUMBER_PREFIX_NUM, A.RECEIVED_DATE, 1 AS SEARCH_BY, rtrim(xmlagg(xmlelement(e,export_lc_no,',').extract('//text()') order by d.id).GetClobVal(),',') AS SC_LC_NO, d.BUYER_NAME
	from cash_incentive_received_mst a, cash_incentive_submission b, cash_incentive_submission_dtls c, com_export_lc d 
	where a.cash_incentive_sub_id=b.id and b.id=c.mst_id and c.lc_sc_id=d.id and c.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 $com_cond $search_text $date_cond $year_cond $search_lc_sc_no $buyer_cond
	group by a.id, a.company_id, a.sys_number_prefix_num, a.received_date, d.buyer_name
	union all 
	select A.ID, A.COMPANY_ID, A.SYS_NUMBER_PREFIX_NUM, A.RECEIVED_DATE, 2 AS SEARCH_BY, rtrim(xmlagg(xmlelement(e,contract_no,',').extract('//text()') order by d.id).GetClobVal(),',') AS SC_LC_NO, d.BUYER_NAME
	from cash_incentive_received_mst a, cash_incentive_submission b, cash_incentive_submission_dtls c, com_sales_contract d 
	where a.cash_incentive_sub_id=b.id and b.id=c.mst_id and c.lc_sc_id=d.id and c.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 $com_cond $search_text $date_cond $year_cond $search_lc_sc_no $buyer_cond
	group by a.id, a.company_id, a.sys_number_prefix_num, a.received_date, d.buyer_name"; 
	//echo $sql;die;
	$sql_result=sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer comp where status_active =1 and is_deleted=0",'id','buyer_name');
    $type=array(1=>'LC',2=>'SC');
    //$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$type);
	//echo  create_list_view("search_div", "Company Name,Buyer,LC/SC,LC/SC No,System ID,Received Date", "170,150,70,300,70","920","300",0, $sql , "js_set_value", "id", "", 1, "company_id,buyer_name,search_by,type_id,0,0", $arr , "company_id,buyer_name,search_by,SC_LC_NO,sys_number_prefix_num,received_date", "",'','0,0,0,0,0,3');
	?>
    <div align="center" style="width:920px;">
        <table cellpadding="0" cellspacing="0" width="920" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150">Company Name</th>
                <th width="120">Buyer</th>
                <th width="50">LC/SC</th>
                <th width="420">LC/SC No</th>
                <th width="50">System ID</th>
                <th>Received Date</th>
            </thead>
		</table>
        <div style="width:920px; max-height:350px; overflow-y:scroll">
     	<table width="900" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
        	<tbody>
            <?
            $i=1;
			foreach($sql_result as $key=>$val)
			{
				if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";//$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$type);
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $val["ID"]; ?>);" >
                    <td width="30" align="center"><?= $i++; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $company_arr[$val["COMPANY_ID"]]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $buyer_arr[$val["BUYER_NAME"]]; ?></td>
                    <td width="50" align="center"><? echo $type[$val["SEARCH_BY"]]; ?></td>
                    <td width="420" style="word-break:break-all"><? echo implode(",",array_unique(explode(",",$val["SC_LC_NO"]->load()))); ?></td>
                    <td width="50" align="center"><? echo $val["SYS_NUMBER_PREFIX_NUM"]; ?></td>
                    <td align="center"><? echo change_date_format($val["RECEIVED_DATE"]); ?></td>
                </tr>
                <?
			}
			?>
            </tbody>
        </table>
        </div>
    </div>
    <?
	exit();
}

if ($action=="populate_data_from_search_popup")
{

	if($db_type==0)
	{
		// ,b.buyer_id as BUYER_ID
		$sql="SELECT a.id as ID, a.company_id as COMPANY_ID,a.sys_number as SYS_NUMBER,a.submission_invoice_id as SUBMISSION_INVOICE_ID,a.cash_incentive_sub_id as CASH_INCENTIVE_SUB_ID, a.received_date as RECEIVED_DATE,a.bill_no as BILL_NO,a.remarks as REMARKS,a.is_lc_sc as IS_LC_SC,a.lc_sc_id as LC_SC_ID,b.sys_number as SUBMISSION_SYS_NUMBER,b.bank_id as BANK_ID,b.amount as AMOUNT,b.special_submitted as SPECIAL_SUBMITTED,b.euro_incentive as EURO_INCENTIVE,b.general_incentive as GENERAL_INCENTIVE,b.market_submitted as MARKET_SUBMITTED, group_concat(distinct(d.export_lc_no)) as SC_LC_NO
		from cash_incentive_received_mst a, cash_incentive_submission b,com_export_lc d 
		where a.id='$data' and a.cash_incentive_sub_id=b.id and b.internal_file_no=d.internal_file_no and a.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0  
		group by a.id, a.company_id,a.sys_number,a.submission_invoice_id,a.cash_incentive_sub_id, a.received_date,a.bill_no,a.remarks,b.b.bank_id,b.amount,b.special_submitted,b.euro_incentive,b.general_incentive,b.market_submitted,a.is_lc_sc,a.lc_sc_id,b.sys_number
		union all 
		a.id as ID, a.company_id as COMPANY_ID,a.sys_number as SYS_NUMBER,a.submission_invoice_id as SUBMISSION_INVOICE_ID,a.cash_incentive_sub_id as CASH_INCENTIVE_SUB_ID, a.received_date as RECEIVED_DATE,a.bill_no as BILL_NO,a.remarks as REMARKS,a.is_lc_sc as IS_LC_SC,a.lc_sc_id as LC_SC_ID,b.sys_number as SUBMISSION_SYS_NUMBER,b.bank_id as BANK_ID,b.amount as AMOUNT,b.special_submitted as SPECIAL_SUBMITTED,b.euro_incentive as EURO_INCENTIVE,b.general_incentive as GENERAL_INCENTIVE,b.market_submitted as MARKET_SUBMITTED,	group_concat(distinct(d.contract_no)) as SC_LC_NO
		 from cash_incentive_received_mst a, cash_incentive_submission b, com_sales_contract d 
		 where a.id='$data' and a.cash_incentive_sub_id=b.id and b.internal_file_no=d.internal_file_no and a.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 
		group by a.id, a.company_id,a.sys_number,a.submission_invoice_id,a.cash_incentive_sub_id, a.received_date,a.bill_no,a.remarks,b.b.bank_id,b.amount,b.special_submitted,b.euro_incentive,b.general_incentive,b.market_submitted,a.is_lc_sc,a.lc_sc_id,b.sys_number"; 
	}
	else
	{
		$sql="SELECT a.id as ID, a.company_id as COMPANY_ID,a.sys_number as SYS_NUMBER,a.submission_invoice_id as SUBMISSION_INVOICE_ID,a.cash_incentive_sub_id as CASH_INCENTIVE_SUB_ID, a.received_date as RECEIVED_DATE,a.bill_no as BILL_NO,a.remarks as REMARKS,c.is_lc_sc as IS_LC_SC,a.lc_sc_id as LC_SC_ID,b.sys_number as SUBMISSION_SYS_NUMBER,b.bank_id as BANK_ID,b.amount as AMOUNT,b.special_submitted as SPECIAL_SUBMITTED,b.euro_incentive as EURO_INCENTIVE,b.general_incentive as GENERAL_INCENTIVE,b.market_submitted as MARKET_SUBMITTED, rtrim(xmlagg(xmlelement(e,d.export_lc_no,',').extract('//text()') order by d.id).GetClobVal(),',') AS SC_LC_NO, d.BUYER_NAME
		from cash_incentive_received_mst a, cash_incentive_submission b, cash_incentive_submission_dtls c, com_export_lc d 
		where a.id='$data' and a.cash_incentive_sub_id=b.id and b.id=c.mst_id and c.lc_sc_id=d.id and c.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 
		group by a.id, a.company_id,a.sys_number,a.submission_invoice_id,a.cash_incentive_sub_id, a.received_date,a.bill_no,a.remarks,b.bank_id,b.amount,b.special_submitted,b.euro_incentive,b.general_incentive,b.market_submitted,c.is_lc_sc,a.lc_sc_id,b.sys_number, d.BUYER_NAME
		union all 
		select a.id as ID, a.company_id as COMPANY_ID,a.sys_number as SYS_NUMBER,a.submission_invoice_id as SUBMISSION_INVOICE_ID,a.cash_incentive_sub_id as CASH_INCENTIVE_SUB_ID, a.received_date as RECEIVED_DATE,a.bill_no as BILL_NO,a.remarks as REMARKS,c.is_lc_sc as IS_LC_SC,a.lc_sc_id as LC_SC_ID,b.sys_number as SUBMISSION_SYS_NUMBER,b.bank_id as BANK_ID,b.amount as AMOUNT,b.special_submitted as SPECIAL_SUBMITTED,b.euro_incentive as EURO_INCENTIVE,b.general_incentive as GENERAL_INCENTIVE,b.market_submitted as MARKET_SUBMITTED, rtrim(xmlagg(xmlelement(e,d.contract_no,',').extract('//text()') order by d.id).GetClobVal(),',') AS SC_LC_NO, d.BUYER_NAME
		 from cash_incentive_received_mst a, cash_incentive_submission b, cash_incentive_submission_dtls c, com_sales_contract d 
		 where a.id='$data' and a.cash_incentive_sub_id=b.id and b.id=c.mst_id and c.lc_sc_id=d.id and c.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 
		group by a.id, a.company_id,a.sys_number,a.submission_invoice_id,a.cash_incentive_sub_id, a.received_date,a.bill_no,a.remarks,b.bank_id,b.amount,b.special_submitted,b.euro_incentive,b.general_incentive,b.market_submitted,c.is_lc_sc,a.lc_sc_id,b.sys_number, d.BUYER_NAME"; 
	}
    //echo $sql;die;
    $data_result=sql_select($sql);
	$incentive_claim_value=$data_result[0]['SPECIAL_SUBMITTED']+$data_result[0]['EURO_INCENTIVE']+$data_result[0]['GENERAL_INCENTIVE']+$data_result[0]['MARKET_SUBMITTED'];

    echo "document.getElementById('txt_system_id').value = '".$data_result[0]["SYS_NUMBER"]."';\n";  
    echo "document.getElementById('cbo_company_name').value = '".$data_result[0]["COMPANY_ID"]."';\n";  
    echo "document.getElementById('txt_lc_sc_no').value = '".implode(",",array_unique(explode(",",$data_result[0]["SC_LC_NO"]->load())))."';\n";  
    echo "document.getElementById('submission_id').value = '".$data_result[0]["CASH_INCENTIVE_SUB_ID"]."';\n";  
    echo "document.getElementById('txt_received_date').value = '".change_date_format($data_result[0]["RECEIVED_DATE"])."';\n";  
    echo "document.getElementById('invoice_id').value = '".$data_result[0]["SUBMISSION_INVOICE_ID"]."';\n";
    echo "document.getElementById('cbo_buyer_name').value = '".$data_result[0]["BUYER_NAME"]."';\n";
    echo "document.getElementById('cbo_bank_name').value = '".$data_result[0]["BANK_ID"]."';\n";  
    echo "document.getElementById('txt_bill_no').value = '".$data_result[0]["BILL_NO"]."';\n"; 
    echo "document.getElementById('txt_incentive_claim_value').value = '".$incentive_claim_value."';\n"; 
    echo "document.getElementById('txt_remarks').value = '".$data_result[0]["REMARKS"]."';\n";  
    echo "document.getElementById('txt_submission').value = '".$data_result[0]["SUBMISSION_SYS_NUMBER"]."';\n";  
    echo "document.getElementById('hidden_is_lc').value = '".$data_result[0]["IS_LC_SC"]."';\n";  
    echo "document.getElementById('hidden_is_lc_sc_id').value = '".$data_result[0]["LC_SC_ID"]."';\n";  
    
    echo "document.getElementById('update_id').value = '".$data_result[0]["ID"]."';\n"; 
	exit();
}

if($action=="details_list_view")
{
	$nameArray=sql_select( "select id as ID, account_head_id as ACCOUNT_HEAD_ID, document_currency as DOCUMENT_CURRENCY, conversion_rate as CONVERSION_RATE, domestic_currency as DOMESTIC_CURRENCY from cash_incentive_received_dtls where mst_id='$data' and status_active=1 and is_deleted=0" );
	$num_row=count($nameArray);

	$i=1;

    if($num_row>0)
    {
        foreach($nameArray as $row){
            ?>
            <tr class="general" id="<? echo $i;?>">
                <td>
                    <input type="text" name="cboHead[]" id="cboHead_<?=$i;?>" class="text_boxes" style="width:170px;"  onDblClick="fn_commercial_head_display(<?=$i;?>)" value="<?= $commercial_head[$row['ACCOUNT_HEAD_ID']];?>" placeholder="Browse" readonly />
                    <input type="hidden" name="cboHeadID[]" id="cboHeadID_<?=$i;?>"  value="<?= $row['ACCOUNT_HEAD_ID'];?>" />
                </td>
                <td>
                    <input type="text" name="documentCurrency[]" id="documentCurrency_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'documentCurrency_')" value="<? echo $row['DOCUMENT_CURRENCY'];?>"/>
                </td>
                <td>
                    <input type="text" name="conversionRate[]" id="conversionRate_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'conversionRate_')" value="<? echo $row['CONVERSION_RATE'];?>"/>
                </td>
                <td>
                    <input type="text" name="domesticCurrency[]" id="domesticCurrency_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'domesticCurrency_')" value="<? echo $row['DOMESTIC_CURRENCY'];?>"/>
                </td>
                <td width="65">
                    <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                    <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                </td>  
            </tr>
            <?
            $i++;
        }

    }
    else
    {
        ?>
        <tr class="general" id="<? echo $i;?>">
            <td>
                <input type="text" name="cboHead[]" id="cboHead_<?=$i;?>" class="text_boxes" style="width:170px;"  onDblClick="fn_commercial_head_display(<?=$i;?>)"  placeholder="Browse" readonly />
                <input type="hidden" name="cboHeadID[]" id="cboHeadID_<?=$i;?>"/>
            </td>
            <td>
                <input type="text" name="documentCurrency[]" id="documentCurrency_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'documentCurrency_')"/>
            </td>
            <td>
                <input type="text" name="conversionRate[]" id="conversionRate_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'conversionRate_')"/>
            </td>
            <td>
                <input type="text" name="domesticCurrency[]" id="domesticCurrency_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'domesticCurrency_')" />
            </td>
            <td width="65">
                <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
            </td>  
        </tr>
        <?

    }

}
//========== End System ID ===========

//========= Start Submission ID =========
if($action=="proceed_submission_popup_search")
{
	echo load_html_head_contents("Export Proceeds Realization Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide()
		}

		function fn_change_caption(type)
		{
			if(type==1){document.getElementById('search_th').innerHTML="LC No";}
			if(type==2){document.getElementById('search_th').innerHTML="SC No";}
			if(type==3){document.getElementById('search_th').innerHTML="Submission ID";}
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:1000px;">
        <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
            <fieldset style="width:1000px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="900" class="rpt_table">
                    <thead>
                        <th>Company</th>
                        <th>Buyer Name</th>
                        <th>Bank Name</th>
                        <th>Submission Year</th>
                        <th>Search By</th>
                        <th id="search_th">LC No</th>
                        <th colspan="2">Submission Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                            <input type="hidden" id="selected_id">
                        </th>
                    </thead>
                    <tr class="general">
                        <td class="must_entry_caption"> 
                            <? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, '',1);?>
                        </td>
                        <td>
							<?
                                echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id  and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$cbo_company_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_bank_name", 152, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name","id,bank_name", 1, "-- All Bank --", $selected, "",0 );

                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_sub_year", 80, $year,"",0, "--Select--", date("Y"),"",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                $is_arr=array(1=>'LC',2=>'SC',3=>'Submission ID');
                                // $arr=array(1=>'Bill No',2=>'Invoice No',3=>'Cash In Adv. Invoice No.');
                                echo create_drop_down( "cbo_search_by", 80, $is_arr,"",0, "--Select--", "","fn_change_caption(this.value)",0 );
                            ?>
                        </td>
                        <td >
                            <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_lc_sc" id="txt_search_lc_sc" />
                        </td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date"></td> 
                        <td>
                            <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'**'+document.getElementById('cbo_bank_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_lc_sc').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_sub_year').value, 'proceed_submission_search_list_view', 'search_div', 'cash_incentive_received_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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

if($action=="proceed_submission_search_list_view")
{
	list($company_id,$bank_id,$search_by,$lc_sc_no,$submission_start_date, $submission_end_date,$year,$buyer_id,$sub_year) = explode('**', $data);
    $com_cond="";$date_cond ="";$year_cond="";$type_num="";$bank_num="";
	//if($buyer_id!=0) $byer_cond="and a.buyer_id=$buyer_id"; else $byer_cond="";

	$search_cond="";
	if ($company_id!=0) {$com_cond=" and a.company_id=$company_id";}
	if ($bank_id!=0) {$bank_num=" and a.buyer_id=$bank_id";}
	if ($buyer_id!=0) {$buyer_cond=" and d.BUYER_NAME=$buyer_id";}
    if($search_by == 1)
	{
        if ($lc_sc_no!='') {$search_text="and d.export_lc_no like '%".trim($lc_sc_no)."%'";}
	}
	elseif($search_by == 2)
	{
        if ($lc_sc_no!='') {$search_text="and d.contract_no like '%".trim($lc_sc_no)."%'";}
	}
	elseif($search_by == 3)
	{
        if ($lc_sc_no!='') {
			$search_text="and a.SYS_NUMBER_PREFIX_NUM ='".trim($lc_sc_no)."'";
			if($sub_year) $year_conds=" and to_char(a.insert_date,'YYYY')='$sub_year'";
			$prv_add_sql=" SELECT b.SYS_NUMBER
			from cash_incentive_submission a, CASH_INCENTIVE_RECEIVED_MST b
			where a.id=b.CASH_INCENTIVE_SUB_ID and a.company_id=$company_id $year_conds $search_text and a.is_deleted=0 and b.is_deleted=0 ";
			$prv_add_data=sql_select($prv_add_sql);
			//echo $prv_add_sql;
			if(count($prv_add_data)>0)
			{
				echo "<strong>Previously Cash Incentive Received Found: ".$prv_add_data[0]['SYS_NUMBER']."</strong>";die;
			}
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
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.insert_date) =$sub_year ";
			}
			else
			{	
				$year_cond=" and to_char(a.insert_date,'YYYY') ='$sub_year' ";
			}
		}
	}

	if($db_type==0)
	{
		$sql="SELECT a.id as id, a.sys_number_prefix_num, a.company_id as COMPANY_ID,a.bank_id as BANK_ID,a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE,
		group_concat(distinct(d.export_lc_no)) as SC_LC_NO, a.INTERNAL_FILE_NO, a.LC_SC_ID, 1 as SEARCH_BY  
		from cash_incentive_submission a,com_export_lc d 
		where a.INTERNAL_FILE_NO=d.INTERNAL_FILE_NO and a.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $date_cond $year_cond $buyer_cond and a.id not in ( select CASH_INCENTIVE_SUB_ID from CASH_INCENTIVE_RECEIVED_MST where COMPANY_ID=$company_id and status_active=1 )
		group by a.id, a.sys_number_prefix_num, a.company_id,a.bank_id, a.sys_number_prefix_num, a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID
		union all 
		select a.id as id, a.sys_number_prefix_num, a.company_id as COMPANY_ID,a.bank_id as BANK_ID,a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE,
		group_concat(distinct(d.contract_no)) as SC_LC_NO, a.INTERNAL_FILE_NO, a.LC_SC_ID, 2 as SEARCH_BY
		 from cash_incentive_submission a, com_sales_contract d 
		 where a.INTERNAL_FILE_NO=d.INTERNAL_FILE_NO and a.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $date_cond $year_cond $buyer_cond and a.id not in ( select CASH_INCENTIVE_SUB_ID from CASH_INCENTIVE_RECEIVED_MST where COMPANY_ID=$company_id and status_active=1 )
		group by a.id, a.sys_number_prefix_num, a.company_id,a.bank_id,a.sys_number_prefix_num,a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID"; 
	}
	else
	{
		$sql="SELECT a.id as id, a.SYS_NUMBER_PREFIX_NUM, a.company_id as COMPANY_ID,a.bank_id as BANK_ID, a.submission_date as SUBMISSION_DATE, listagg(cast(d.export_lc_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, a.INTERNAL_FILE_NO, a.LC_SC_ID, 1 as SEARCH_BY, d.BUYER_NAME  
		from cash_incentive_submission a, cash_incentive_submission_dtls b, com_export_lc d 
		where a.id=b.mst_id and b.lc_sc_id=d.id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_cond $bank_num $search_text $date_cond $year_cond $buyer_cond and a.id not in ( select CASH_INCENTIVE_SUB_ID from CASH_INCENTIVE_RECEIVED_MST where COMPANY_ID=$company_id and status_active=1 )
		group by a.id, a.sys_number_prefix_num, a.company_id,a.bank_id, a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID, d.BUYER_NAME
		union all 
		select a.id as id, a.SYS_NUMBER_PREFIX_NUM, a.company_id as COMPANY_ID,a.bank_id as BANK_ID, a.submission_date as SUBMISSION_DATE, listagg(cast(d.contract_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, a.INTERNAL_FILE_NO, a.LC_SC_ID, 2 as SEARCH_BY, d.BUYER_NAME
		from cash_incentive_submission a, cash_incentive_submission_dtls b, com_sales_contract d 
		where a.id=b.mst_id and b.lc_sc_id=d.id and b.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $com_cond $bank_num $search_text $date_cond $year_cond $buyer_cond and a.id not in ( select CASH_INCENTIVE_SUB_ID from CASH_INCENTIVE_RECEIVED_MST where COMPANY_ID=$company_id and status_active=1 )
		group by a.id, a.sys_number_prefix_num, a.company_id, a.bank_id,a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID, d.BUYER_NAME"; 
	}
	//echo $sql; // die;
	$is_lc_sc_arr=array(1=>'LC',2=>'SC');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0",'id','bank_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0",'id','buyer_name');

	$arr=array (0=>$comp,1=>$buyer_arr,2=>$bank_arr,3=>$is_lc_sc_arr);

	echo create_list_view("list_view", "Company,Buyer,Bank,LC/SC,LC/SC No,Sub. Date,Sub. ID", "150,140,150,40,350,60","1000","280",0, $sql, "js_set_value", "id", "", 1, "COMPANY_ID,BUYER_NAME,BANK_ID,SEARCH_BY,0,0,0,0,0", $arr , "COMPANY_ID,BUYER_NAME,BANK_ID,SEARCH_BY,SC_LC_NO,SUBMISSION_DATE,SYS_NUMBER_PREFIX_NUM", "",'','0,0,0,0,0,3,0');
	exit();
}

if($action=="populate_data_from_submission")
{
    if($db_type==0)
	{
		// , a.buyer_id as BUYER_ID
		$sql="SELECT a.id as id, a.company_id as COMPANY_ID, a.bank_id as BANK_ID, a.sys_number as SYS_NUMBER, a.submission_date as SUBMISSION_DATE, d.BUYER_NAME, group_concat(distinct(d.export_lc_no)) as SC_LC_NO, a.internal_file_no as INTERNAL_FILE_NO, a.LC_SC_ID, a.submission_invoice_id as SUBMISSION_INVOICE_ID,a.amount as AMOUNT,a.special_submitted as SPECIAL_SUBMITTED,a.euro_incentive as EURO_INCENTIVE,a.general_incentive as GENERAL_INCENTIVE,a.market_submitted as MARKET_SUBMITTED,a.is_lc_sc as IS_LC_SC,a.lc_sc_id as LC_SC_ID
		from cash_incentive_submission a,com_export_lc d 
		where a.id=$data and a.internal_file_no=d.internal_file_no and a.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id, a.company_id, a.bank_id, a.sys_number, a.submission_date, d.BUYER_NAME, a.internal_file_no, a.LC_SC_ID,a.submission_invoice_id,a.amount,a.special_submitted,a.euro_incentive,a.general_incentive,a.market_submitted, a.is_lc_sc, a.lc_sc_id
		union all 
		select a.id as id, a.company_id as COMPANY_ID,a.bank_id as BANK_ID,a.sys_number as SYS_NUMBER, a.submission_date as SUBMISSION_DATE,group_concat(distinct(d.contract_no)) as SC_LC_NO, a.internal_file_no as INTERNAL_FILE_NO, a.LC_SC_ID, a.submission_invoice_id as SUBMISSION_INVOICE_ID,a.amount as AMOUNT,a.special_submitted as SPECIAL_SUBMITTED,a.euro_incentive as EURO_INCENTIVE,a.general_incentive as GENERAL_INCENTIVE,a.market_submitted as MARKET_SUBMITTED,a.is_lc_sc as IS_LC_SC,a.lc_sc_id as LC_SC_ID, d.BUYER_NAME
		 from cash_incentive_submission a, com_sales_contract d 
		 where a.id=$data and a.internal_file_no=d.internal_file_no and a.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id, a.company_id,a.bank_id,a.sys_number,a.submission_date, a.internal_file_no, a.LC_SC_ID,a.submission_invoice_id,a.amount,a.special_submitted,a.euro_incentive,a.general_incentive,a.market_submitted, a.is_lc_sc, a.lc_sc_id, d.BUYER_NAME "; 
	}
	else
	{
		$sql="SELECT a.id as id, a.company_id as COMPANY_ID,a.bank_id as BANK_ID,a.sys_number as SYS_NUMBER, a.submission_date as SUBMISSION_DATE, listagg(cast(d.export_lc_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, a.internal_file_no as INTERNAL_FILE_NO, a.LC_SC_ID ,a.submission_invoice_id as SUBMISSION_INVOICE_ID,a.amount as AMOUNT,a.special_submitted as SPECIAL_SUBMITTED,a.euro_incentive as EURO_INCENTIVE,a.general_incentive as GENERAL_INCENTIVE,a.market_submitted as MARKET_SUBMITTED,b.is_lc_sc as IS_LC_SC,a.lc_sc_id as LC_SC_ID, d.BUYER_NAME
		from cash_incentive_submission a, cash_incentive_submission_dtls b,com_export_lc d 
		where a.id=$data and a.id=b.mst_id and b.lc_sc_id=d.id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
		group by a.id, a.company_id,a.bank_id,a.sys_number, a.submission_date, a.internal_file_no, a.LC_SC_ID,a.submission_invoice_id,a.amount,a.special_submitted,a.euro_incentive,a.general_incentive,a.market_submitted, b.is_lc_sc, a.lc_sc_id, d.BUYER_NAME 
		union all 
		select a.id as id, a.company_id as COMPANY_ID,a.bank_id as BANK_ID,a.sys_number as SYS_NUMBER, a.submission_date as SUBMISSION_DATE, listagg(cast(d.contract_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, a.internal_file_no as INTERNAL_FILE_NO, a.LC_SC_ID,a.submission_invoice_id as SUBMISSION_INVOICE_ID,a.amount as AMOUNT,a.special_submitted as SPECIAL_SUBMITTED,a.euro_incentive as EURO_INCENTIVE,a.general_incentive as GENERAL_INCENTIVE,a.market_submitted as MARKET_SUBMITTED,b.is_lc_sc as IS_LC_SC,a.lc_sc_id as LC_SC_ID, d.BUYER_NAME
		 from cash_incentive_submission a, cash_incentive_submission_dtls b, com_sales_contract d 
		 where a.id=$data and a.id=b.mst_id and b.lc_sc_id=d.id and b.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
		group by a.id, a.company_id,a.bank_id,a.sys_number,a.submission_date, a.internal_file_no, a.LC_SC_ID,a.submission_invoice_id,a.amount,a.special_submitted,a.euro_incentive,a.general_incentive,a.market_submitted, b.is_lc_sc, a.lc_sc_id, d.BUYER_NAME "; 
	}
	// echo $sql;
	$data_array=sql_select($sql);
	$incentive_claim_value=$data_array[0]['SPECIAL_SUBMITTED']+$data_array[0]['EURO_INCENTIVE']+$data_array[0]['GENERAL_INCENTIVE']+$data_array[0]['MARKET_SUBMITTED'];
    echo "document.getElementById('cbo_company_name').value = '".$data_array[0]['COMPANY_ID']."';\n";
    // echo "document.getElementById('cbo_buyer_name').value = '".$data_array[0]['BUYER_ID']."';\n";
    echo "document.getElementById('cbo_bank_name').value = '".$data_array[0]['BANK_ID']."';\n";
    echo "document.getElementById('txt_incentive_claim_value').value = '".number_format($incentive_claim_value,4,'.','')."';\n";
    echo "document.getElementById('invoice_id').value = '".$data_array[0]['SUBMISSION_INVOICE_ID']."';\n";
    echo "document.getElementById('txt_lc_sc_no').value = '".$data_array[0]['SC_LC_NO']."';\n";
    echo "document.getElementById('submission_id').value = '".$data_array[0]['ID']."';\n";
    echo "document.getElementById('txt_submission').value = '".$data_array[0]['SYS_NUMBER']."';\n";
    echo "document.getElementById('hidden_is_lc').value = '".$data_array[0]['IS_LC_SC']."';\n";
    echo "document.getElementById('hidden_is_lc_sc_id').value = '".$data_array[0]['LC_SC_ID']."';\n";
	echo "document.getElementById('cbo_buyer_name').value = '".$data_array[0]['BUYER_NAME']."';\n";
}
//========= End Submission ID =========

if ($action=="append_load_details_container")
{  
    $i = $data;
    ?>
    <tr class="general" id="<? echo $i;?>">
        <td>
            <input type="text" name="cboHead[]" id="cboHead_<?=$i;?>" class="text_boxes" style="width:170px;"  onDblClick="fn_commercial_head_display(<?=$i;?>)"  placeholder="Browse" readonly />
            <input type="hidden" name="cboHeadID[]" id="cboHeadID_<?=$i;?>"/>
        </td>
        <td>
            <input type="text" name="documentCurrency[]" id="documentCurrency_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'documentCurrency_')"/>
        </td>
        <td>
            <input type="text" name="conversionRate[]" id="conversionRate_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'conversionRate_')"/>
        </td>
        <td>
            <input type="text" name="domesticCurrency[]" id="domesticCurrency_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'domesticCurrency_')" />
        </td>
        <td width="65">
            <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
            <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
        </td>  
    </tr>
    <?
    exit();
}

if($action=="commercial_head_popup")
{
	echo load_html_head_contents("Export Proceeds Realization Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,fld_val)
		{
			//alert(id+"="+fld_val);
			$('#hdn_head_id').val(id);
			$('#hdn_head_val').val(fld_val);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:360px;">
        <table cellpadding="0" cellspacing="0" width="360" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th>Account Head
                <input type="hidden" id="hdn_head_id" />
                <input type="hidden" id="hdn_head_val" />
                </th>
                
            </thead>
		</table>
        <div style="width:360px; max-height:350px; overflow-y:scroll">
     	<table width="340" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
        	<tbody>
            <?
            $i=1;
			foreach($commercial_head as $key=>$val)
			{
				if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $key.",'".$val."'"; ?>);" >
                    <td width="50" align="center"><?= $i++; ?></td>
                    <td><? echo $val; ?></td>
                </tr>
                <?
			}
			?>
            </tbody>
        </table>
        </div>
    </div>
    </body>
    <!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
    <script>setFilterGrid('list_view',-1)</script>
    </html>
    <?
	exit();
}

if($action=="save_update_delete")
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
	
		$mst_id=return_next_id("id", "CASH_INCENTIVE_RECEIVED_MST", 1);
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'CIR', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from cash_incentive_received_mst where company_id=$cbo_company_name $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));

		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, company_id, received_date, submission_invoice_id, cash_incentive_sub_id, bill_no,remarks,is_lc_sc,lc_sc_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',".$cbo_company_name.",".$txt_received_date.",".$invoice_id.",".$submission_id.",".$txt_bill_no.",".$txt_remarks.",".$hidden_is_lc.",".$hidden_is_lc_sc_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		// echo "10**INSERT INTO CASH_INCENTIVE_RECEIVED_MST (".$field_array_mst.") VALUES ".$data_array_mst; 
		// die;
        $dtls_id=return_next_id("id", "CASH_INCENTIVE_RECEIVED_DTLS", 1);
        $data_array_dtls='';

        $field_array_dtls="id, mst_id, account_head_id, document_currency, conversion_rate, domestic_currency, inserted_by, insert_date, is_deleted, status_active";
        for($i=1;$i<=$total_row;$i++)
        {
            $cbo_acc_head      = "cbo_acc_head_".$i;
            $txt_document_currency    = "txt_document_currency_".$i;
            $txt_conversion_rate    = "txt_conversion_rate_".$i;
            $txt_domestic_currency    = "txt_domestic_currency_".$i;

            if ($data_array_dtls!='') {$data_array_dtls .=",";}
            $data_array_dtls .="(".$dtls_id.",".$mst_id.",'".$$cbo_acc_head."','".$$txt_document_currency."','".$$txt_conversion_rate."','".$$txt_domestic_currency."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
            $dtls_id++;
        }

		//echo "10**INSERT INTO CASH_INCENTIVE_RECEIVED_MST (".$field_array_mst.") VALUES ".$data_array_mst;oci_rollback($con);disconnect($con);die;
		$rID=sql_insert("CASH_INCENTIVE_RECEIVED_MST",$field_array_mst,$data_array_mst,0);
		$rID1=sql_insert("CASH_INCENTIVE_RECEIVED_DTLS",$field_array_dtls,$data_array_dtls,0);	
		//echo '</br>10**'.$rID.'**'.$rID1;oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".$dtls_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1)
			{
				oci_commit($con);  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".$dtls_id;
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
		$field_array_mst="company_id*received_date*submission_invoice_id*cash_incentive_sub_id*bill_no*remarks*is_lc_sc*lc_sc_id*updated_by*update_date";

		$data_array_mst="".$cbo_company_name."*".$txt_received_date."*".$invoice_id."*".$submission_id."*".$txt_bill_no."*".$txt_remarks."*".$hidden_is_lc."*".$hidden_is_lc_sc_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $dtls_id=return_next_id("id", "CASH_INCENTIVE_RECEIVED_DTLS", 1);
        $data_array_dtls='';

        $field_array_dtls="id, mst_id, account_head_id, document_currency, conversion_rate, domestic_currency, inserted_by, insert_date, is_deleted, status_active";
        for($i=1;$i<=$total_row;$i++)
        {
            $cbo_acc_head      = "cbo_acc_head_".$i;
            $txt_document_currency    = "txt_document_currency_".$i;
            $txt_conversion_rate    = "txt_conversion_rate_".$i;
            $txt_domestic_currency    = "txt_domestic_currency_".$i;

            if ($data_array_dtls!='') {$data_array_dtls .=",";}
            $data_array_dtls .="(".$dtls_id.",".$update_id.",'".$$cbo_acc_head."','".$$txt_document_currency."','".$$txt_conversion_rate."','".$$txt_domestic_currency."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
            $dtls_id++;
        }
    
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("CASH_INCENTIVE_RECEIVED_MST",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID1=sql_delete("CASH_INCENTIVE_RECEIVED_DTLS",$field_array,$data_array,"mst_id","".$update_id."",0);
		$rID2=sql_insert("CASH_INCENTIVE_RECEIVED_DTLS",$field_array_dtls,$data_array_dtls,0);	
	
		// echo "10**".$rID.'='.$rID1.'='.$rID2."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1)
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
			if($rID==1 && $rID1==1 && $rID2==1)
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
	
		//check_table_status( $_SESSION['menu_id'],0);
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

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("CASH_INCENTIVE_RECEIVED_MST",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=sql_delete("CASH_INCENTIVE_RECEIVED_DTLS",$field_array,$data_array,"mst_id","".$update_id."",0);
		// echo "10**".$rID.'='.$rID1."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1)
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
			if($rID==1 && $rID1==1)
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
	}//
}