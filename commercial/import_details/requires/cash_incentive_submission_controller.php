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
					<th >System ID</th>
                    <th >Bank Name</th>
					<th >Search By</th>
                    <th>LC/SC No</th>
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
                    <td><?
						echo create_drop_down("cbo_bank", 150, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "");
						?></td>
					<td>
						<?
							$is_arr=array(1=>'LC',2=>'SC');
							echo create_drop_down( "cbo_search_by", 80, $is_arr,"",0, "--Select--", "",'',0 );
						?>
					</td>
					<td >
						<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_lc_sc" id="txt_search_lc_sc" />
					</td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_sys').value+'_'+document.getElementById('cbo_bank').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_lc_sc').value, 'create_system_search_list_view', 'search_div', 'cash_incentive_submission_controller', 'setFilterGrid(\'search_div\',-1)');" style="width:100px;" /></td>
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
	// echo $data;die;
	$com_cond="";$date_cond ="";$year_cond="";$search_text="";$bank_num=""; $search_lc_sc="";
	list($company_id,$search_sys,$bank_id,$submission_start_date, $submission_end_date,$year,$search_string,$is_lc_sc,$lc_sc_no ) = explode('_', $data);
	if ($company_id!=0) {$com_cond=" and a.company_id=$company_id";}
	if ($bank_id!=0) {$bank_num=" and a.bank_id=$bank_id";}

	if($is_lc_sc == 1)
	{
        if ($lc_sc_no!='') {$search_lc_sc="and d.export_lc_no like '%".trim($lc_sc_no)."%'";}
	}else{
        if ($lc_sc_no!='') {$search_lc_sc="and d.contract_no like '%".trim($lc_sc_no)."%'";}
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
				$year_cond=" and YEAR(a.submission_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.submission_date,'YYYY') =$year ";
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
	$is_lc_sc_arr=array(1=>'LC',2=>'SC');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank where status_active=1 and is_deleted=0",'id','bank_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0",'id','buyer_name');
	
	if($db_type==0)
	{
		$sql="SELECT a.id as id, a.company_id as COMPANY_ID,a.bank_id as BANK_ID, a.buyer_id as BUYER_ID,a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE,
		group_concat(distinct(d.export_lc_no)) as SC_LC_NO, a.INTERNAL_FILE_NO, a.LC_SC_ID, 1 as SEARCH_BY  
		from cash_incentive_submission a,com_export_lc d 
		where a.internal_file_no=d.internal_file_no and a.entry_form=565 and a.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $search_lc_sc $date_cond $year_cond 
		group by a.id, a.company_id,a.bank_id, a.buyer_id,a.sys_number_prefix_num, a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID
		union all 
		select a.id as id, a.company_id as COMPANY_ID,a.bank_id as BANK_ID, a.buyer_id as BUYER_ID,a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE,
		group_concat(distinct(d.contract_no)) as SC_LC_NO, a.INTERNAL_FILE_NO, a.LC_SC_ID, 2 as SEARCH_BY
		 from cash_incentive_submission a, com_sales_contract d 
		 where a.internal_file_no=d.internal_file_no and a.entry_form=565 and a.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $search_lc_sc $date_cond $year_cond 
		group by a.id, a.company_id,a.bank_id, a.buyer_id,a.sys_number_prefix_num,a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID"; 
	}
	else
	{
		$sql="SELECT a.id as id, a.company_id as COMPANY_ID,a.bank_id as BANK_ID, a.buyer_id as BUYER_ID,a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE, listagg(cast(d.export_lc_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, a.INTERNAL_FILE_NO, a.LC_SC_ID, 1 as SEARCH_BY  
		from cash_incentive_submission a, cash_incentive_submission_dtls b, com_export_lc d 
		where a.id=b.mst_id and b.lc_sc_id=d.id and b.file_no=d.internal_file_no and a.entry_form=565 and a.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $search_lc_sc $date_cond $year_cond 
		group by a.id, a.company_id,a.bank_id, a.buyer_id,a.sys_number_prefix_num, a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID
		union all 
		select a.id as id, a.company_id as COMPANY_ID,a.bank_id as BANK_ID, a.buyer_id as BUYER_ID,a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.submission_date as SUBMISSION_DATE, listagg(cast(d.contract_no as varchar(4000)),',') within group(order by d.id) as SC_LC_NO, a.INTERNAL_FILE_NO, a.LC_SC_ID, 2 as SEARCH_BY
		 from cash_incentive_submission a, cash_incentive_submission_dtls b, com_sales_contract d 
		 where a.id=b.mst_id and b.lc_sc_id=d.id and b.file_no=d.internal_file_no and a.entry_form=565 and a.is_lc_sc=2 and a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $search_lc_sc $date_cond $year_cond 
		group by a.id, a.company_id,a.bank_id, a.buyer_id,a.sys_number_prefix_num,a.submission_date, a.INTERNAL_FILE_NO, a.LC_SC_ID"; 
	}

	// echo $sql;
	$result=sql_select($sql);
	?>
	<table width="850" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL No</th>
            <th width="150">Company Name</th>
            <th width="60">System ID</th>
            <th width="70">LC/SC</th>
            <th width="150">LC/SC No</th>
            <th width="120">Bank Name</th>
            <th width="120">Buyer Name</th>
            <th>Submission Date</th>
        </thead>
    </table>
    <div style="width:850px; overflow-y:scroll; max-height:280px">
     	<table width="830" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view">
		<?			
            $i = 1;
            foreach($result as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row['ID'];?>)" >  
					<td width="40" align="center"><? echo $i; ?></td>
                    <td width="150"><p><? echo $company_arr[$row['COMPANY_ID']]; ?></p></td>
					<td width="60" align="center"><p><? echo $row['SYS_NUMBER_PREFIX_NUM']; ?></td>
                    <td width="70" align="center"><p><? echo $is_lc_sc_arr[$row['SEARCH_BY']]; ?></p></td>
                    <td width="150" align="center"><p><? echo implode(",",array_unique(explode(",",$row['SC_LC_NO']))); ?></p></td>
					<td width="120"><p><? echo $bank_arr[$row['BANK_ID']]; ?></p></td>
                    <td width="120"><p><? echo $buyer_arr[$row['BUYER_ID']];; ?></p></td>
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
	<?
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$data=explode("**",$data);
	$data_id=$data[0];
	$is_lc_sc=$data[1];
	// , buyer_id as BUYER_ID
	$data_array="select id as ID,sys_number as SYS_NUMBER, company_id as COMPANY_ID, bank_id as BANK_ID, submission_date as SUBMISSION_DATE, submission_invoice_id as SUBMISSION_INVOICE_ID, realization_id as REALIZATION_ID, incentive_bank_file as INCENTIVE_BANK_FILE, net_realize_value as NET_REALIZE_VALUE, remarks as REMARKS, special_submitted_chk as SPECIAL_SUBMITTED_CHK, euro_incentive_chk as EURO_INCENTIVE_CHK, general_incentive_chk as GENERAL_INCENTIVE_CHK, market_submitted_chk as MARKET_SUBMITTED_CHK, special_submitted as SPECIAL_SUBMITTED, euro_incentive as EURO_INCENTIVE, general_incentive as GENERAL_INCENTIVE, market_submitted as MARKET_SUBMITTED, amount as AMOUNT,is_lc_sc as IS_LC_SC,lc_sc_id as LC_SC_ID,internal_file_no as INTERNAL_FILE_NO, file_no_string as FILE_NO_STRING
	from cash_incentive_submission 
    where  status_active=1 and is_deleted=0 and id='$data_id'";
    // echo $data_array;
    $data_result=sql_select($data_array);
    echo "document.getElementById('txt_system_id').value = '".$data_result[0]["SYS_NUMBER"]."';\n";  
    echo "document.getElementById('cbo_company_name').value = '".$data_result[0]["COMPANY_ID"]."';\n";  
    echo "document.getElementById('cbo_bank_name').value = '".$data_result[0]["BANK_ID"]."';\n";  
    echo "document.getElementById('txt_submission_date').value = '".change_date_format($data_result[0]["SUBMISSION_DATE"])."';\n";  
    echo "document.getElementById('realization_id').value = '".$data_result[0]["REALIZATION_ID"]."';\n";  
    echo "document.getElementById('submission_invoice_id').value = '".$data_result[0]["SUBMISSION_INVOICE_ID"]."';\n";  
    echo "document.getElementById('txt_incective_bank_file').value = '".$data_result[0]["INCENTIVE_BANK_FILE"]."';\n";  
    echo "document.getElementById('txt_net_realize_value').value = '".$data_result[0]["NET_REALIZE_VALUE"]."';\n";  
    // echo "document.getElementById('cbo_buyer_name').value = '".$data_result[0]["BUYER_ID"]."';\n";  
    echo "document.getElementById('txt_remarks').value = '".$data_result[0]["REMARKS"]."';\n";   
    echo "document.getElementById('special_submitted').value = '".$data_result[0]["SPECIAL_SUBMITTED"]."';\n";  
    echo "document.getElementById('euro_incentive').value = '".$data_result[0]["EURO_INCENTIVE"]."';\n";  
    echo "document.getElementById('general_incentive').value = '".$data_result[0]["GENERAL_INCENTIVE"]."';\n";  
    echo "document.getElementById('market_submitted').value = '".$data_result[0]["MARKET_SUBMITTED"]."';\n";  
    echo "document.getElementById('total_amount').value = '".$data_result[0]["AMOUNT"]."';\n";  
    echo "document.getElementById('is_lc_sc').value = '".$data_result[0]["IS_LC_SC"]."';\n";  
    echo "document.getElementById('lc_sc_id').value = '".$data_result[0]["LC_SC_ID"]."';\n";  
    echo "document.getElementById('txt_internal_file_no').value = '".$data_result[0]["INTERNAL_FILE_NO"]."';\n"; 
	echo "document.getElementById('txt_file_no_string').value = '".$data_result[0]["FILE_NO_STRING"]."';\n"; 
	if($data_result[0]["SPECIAL_SUBMITTED_CHK"]==1){
		echo "document.getElementById('special_submitted_chk').value = 1;\n";
		echo "$('#special_submitted_chk').attr('checked',true);\n";
	}else{
		echo "document.getElementById('special_submitted_chk').value = 0;\n";
		echo "$('#special_submitted_chk').attr('checked',false);\n";
	}
	// if($data_result[0]["EURO_INCENTIVE_CHK"]==1){
	// 	echo "document.getElementById('euro_incentive_chk').value = 1;\n";
	// 	echo "$('#euro_incentive_chk').attr('checked',true);\n";
	// }else{
	// 	echo "document.getElementById('euro_incentive_chk').value = 0;\n";
	// 	echo "$('#euro_incentive_chk').attr('checked',false);\n";
	// }
	// if($data_result[0]["GENERAL_INCENTIVE_CHK"]==1){
	// 	echo "document.getElementById('general_incentive_chk').value = 1;\n";
	// 	echo "$('#general_incentive_chk').attr('checked',true);\n";
	// }else{
	// 	echo "document.getElementById('general_incentive_chk').value = 0;\n";
	// 	echo "$('#general_incentive_chk').attr('checked',false);\n";
	// }
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
	where a.id in (".$data_result[0]['REALIZATION_ID'].") and a.invoice_bill_id=c.id and a.is_invoice_bill=1 and c.id=d.doc_submission_mst_id and d.lc_sc_id=e.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	union all
	select d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT,e.id as LC_SC_ID, e.contract_no as SC_LC_NO,e.contract_value as SC_LC_VALUE,e.bank_file_no as BANK_FILE_NO, e.sc_year as SC_LC_YEAR
	from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d,com_sales_contract e
	where a.id in (".$data_result[0]['REALIZATION_ID'].") and a.invoice_bill_id=c.id and a.is_invoice_bill=1 and c.id=d.doc_submission_mst_id and d.lc_sc_id=e.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";

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
	echo "document.getElementById('txt_lc_value').value 		= '".array_sum($lc_sc_value)."';\n";
	echo "document.getElementById('txt_file_year').value 		= '".implode(",",array_unique(explode(",",chop($lc_sc_year,','))))."';\n";
	echo "document.getElementById('txt_bank_file_no').value 	= '".implode(",",array_unique(explode(",",chop($lc_sc_bank_file,','))))."';\n";
	echo "document.getElementById('txt_invoice_value').value 	= '".array_sum($bill_value)."';\n";

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
		var is_lc = new Array();var inter_file_no = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_view' ).rows.length; 
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
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_mst_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_mst_id' + str).val() );
				invoice_id.push( $('#txt_invoice_id' + str).val() );
				lc_sc_id.push( $('#txt_LC_SC_id' + str).val() );
				is_lc.push( $('#txt_is_lc' + str).val() );
				inter_file_no.push( $('#txt_inter_file_no' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_mst_id' + str).val() ) break;
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
		
    </script>
    </head>
    <body>
    <div align="center" style="width:760px;">
        <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
            <fieldset style="width:820px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                    <thead>
                        <th>Buyer</th>
						<th >Search By</th>
                    	<th>LC/SC No</th>
                        <th >Realization Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
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
							$is_arr=array(1=>'LC',2=>'SC',3=>'File No');
							echo create_drop_down( "cbo_search_by", 80, $is_arr,"",0, "--Select--", "",'',0 );
						?>
					</td>
					<td >
						<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_lc_sc" id="txt_search_lc_sc" />
					</td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:60px" placeholder="From Date" />
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"placeholder="To Date" />
                        </td>
                        <td>
                            <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'**'+<? echo $beneficiary_name; ?>+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_lc_sc').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value, 'proceed_realization_search_list_view', 'search_div', 'cash_incentive_submission_controller', 'setFilterGrid(\'tbl_list_view\',-1)')" style="width:70px;" />
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
	$lc_sc_no=trim($data[3]);
	$date_form=$data[4];
	$date_to=$data[5];
	$year=$data[6];

	if($company_id!=0) $com_cond="and a.benificiary_id=$company_id"; else $com_cond="";
	// if($buyer_id!=0) $buyer_cond="and a.buyer_id=$buyer_id"; else $buyer_cond="";
	if ($lc_sc_no!=''){
		if($search_by == 1)
		{
			$search_text="and d.export_lc_no like '%".trim($lc_sc_no)."%'";
		}
		else if($search_by == 2)
		{
			$search_text="and d.contract_no like '%".trim($lc_sc_no)."%'";
		}
		else if($search_by == 3)
		{
			$search_text="and d.internal_file_no like '%".trim($lc_sc_no)."%'";
		}
	}


	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond="and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$byer_cond="and a.buyer_id= $buyer_id";
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
	$duplicat_sql="select internal_file_no as INTERNAL_FILE_NO from cash_incentive_submission where status_active=1 and is_deleted=0 ";
	$duplicat_data=sql_select($duplicat_sql);
	$file_no='';
	foreach($duplicat_data as $value)
	{
		if($file_no !=''){$file_no .= ",'".$value['INTERNAL_FILE_NO']."'";}else{$file_no = "'".$value['INTERNAL_FILE_NO']."'";}
	}
	unset($duplicat_data);
	$internal_file_no="";
	if($file_no!="") $internal_file_no=" and d.internal_file_no not in ($file_no)";
	$sql = "select a.id as ID, a.benificiary_id as BENIFICIARY_ID, a.buyer_id as BUYER_ID, a.invoice_bill_id as INVOICE_BILL_ID, a.received_date as RECEIVED_DATE, b.bank_ref_no as BANK_REF_NO, c.net_invo_value as BILL_AMNT,d.id as SC_LC_ID, d.export_lc_no as SC_LC_NO, d.internal_file_no as INTERNAL_FILE_NO, d.lc_year as FILE_YEAR, 1 as SEARCH_BY,e.document_currency as DOCUMENT_CURRENCY, c.id as SUB_DTLS_ID, e.id as RLZ_DTLS_ID
	from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c,com_export_lc d,com_export_proceed_rlzn_dtls e
	where a.id=e.mst_id and a.invoice_bill_id=b.id and a.is_invoice_bill=1 and b.id=c.doc_submission_mst_id and c.is_lc=1 and c.lc_sc_id=d.id and e.type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $com_cond $buyer_cond $search_text $date_cond $year_cond $internal_file_no 
	union all
	select a.id as ID,  a.benificiary_id as BENIFICIARY_ID, a.buyer_id as BUYER_ID, a.invoice_bill_id as INVOICE_BILL_ID, a.received_date as RECEIVED_DATE, b.bank_ref_no as BANK_REF_NO, c.net_invo_value as BILL_AMNT,d.id as SC_LC_ID, d.contract_no as SC_LC_NO, d.internal_file_no as INTERNAL_FILE_NO, d.sc_year as FILE_YEAR, 2 as SEARCH_BY, e.document_currency as DOCUMENT_CURRENCY, c.id as SUB_DTLS_ID, e.id as RLZ_DTLS_ID
	from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c,com_sales_contract d,com_export_proceed_rlzn_dtls e
	where a.id=e.mst_id and a.invoice_bill_id=b.id and a.is_invoice_bill=1 and b.id=c.doc_submission_mst_id and c.is_lc=2 and c.lc_sc_id=d.id and e.type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $com_cond $buyer_cond $search_text $date_cond $year_cond $internal_file_no";
	// echo $sql;
	$is_LC_SC=array(1=>"LC",2=>"SC");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$sql_result = sql_select($sql);
	$result=array();$bill_amount=array();$document_amount=array();
	foreach($sql_result as $row){
		$result[$row['ID']]['ID']=$row['ID'];
		$result[$row['ID']]['INVOICE_BILL_ID']=$row['INVOICE_BILL_ID'];
		$result[$row['ID']]['SC_LC_ID']=$row['SC_LC_ID'];
		$result[$row['ID']]['BANK_REF_NO']=$row['BANK_REF_NO'];
		$result[$row['ID']]['BENIFICIARY_ID']=$row['BENIFICIARY_ID'];
		$result[$row['ID']]['BUYER_ID']=$row['BUYER_ID'];
		$result[$row['ID']]['SEARCH_BY']=$row['SEARCH_BY'];
		$result[$row['ID']]['SC_LC_NO']=$row['SC_LC_NO'];
		$result[$row['ID']]['RECEIVED_DATE']=$row['RECEIVED_DATE'];
		$result[$row['ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
		$result[$row['ID']]['FILE_YEAR']=$row['FILE_YEAR'];
		$bill_amount[$row['ID']][$row['SUB_DTLS_ID']]=$row['BILL_AMNT'];
		$document_amount[$row['ID']][$row['RLZ_DTLS_ID']]=$row['DOCUMENT_CURRENCY'];
	}

	?>
	<table width="820" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL No</th>
            <th width="50">System Id</th>
            <th width="100">Bill No</th>
            <th width="80">Benificiary</th>
            <th width="100">Buyer</th>
            <th width="80">Bill Amnt</th>
            <th width="80">Net Realization Value</th>
            <th width="50">LC/SC</th>
            <th width="50">File No</th>
            <th width="50">File Year</th>
            <th width="100">LC/SC No</th>
            <th width="80">Received Date</th>
        </thead>
     	<tbody  id="tbl_list_view">
		<?			
            $i = 1;
            foreach($result as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)" >  
					<td width="40" align="center"><? echo $i; ?>
					<input type="hidden" name="txt_mst_id" id="txt_mst_id<?php echo $i ?>" value="<? echo $row['ID']; ?>"/>	
					<input type="hidden" name="txt_invoice_id" id="txt_invoice_id<?php echo $i ?>" value="<? echo $row['INVOICE_BILL_ID']; ?>"/>	
					<input type="hidden" name="txt_invoice_id" id="txt_LC_SC_id<?php echo $i ?>" value="<? echo $row['SC_LC_ID']; ?>"/>	
					<input type="hidden" name="txt_is_lc" id="txt_is_lc<?php echo $i ?>" value="<? echo $row['SEARCH_BY']; ?>"/>	
					<input type="hidden" name="txt_inter_file_no" id="txt_inter_file_no<?php echo $i ?>" value="<? echo $row['INTERNAL_FILE_NO']; ?>"/>	
					</td>
                    <td align="center"><p><? echo $row['ID']; ?></p></td>
					<td><p><? echo $row['BANK_REF_NO']; ?></td>
                    <td><p><? echo $comp[$row['BENIFICIARY_ID']]; ?></p></td>
                    <td><p><? echo $buyer_arr[$row['BUYER_ID']]; ?></p></td>
					<td align="right"><p><? echo array_sum($bill_amount[$row['ID']]);?></p></td>
					<td align="right"><p><? echo array_sum($document_amount[$row['ID']]);?></p></td>
					<td align="center"><p><? echo $is_LC_SC[$row['SEARCH_BY']];?></p></td>
					<td ><p><? echo $row['INTERNAL_FILE_NO'];?></p></td>
					<td ><p><? echo $row['FILE_YEAR'];?></p></td>
					<td ><p><? echo $row['SC_LC_NO'];?></p></td>
					<td align="center"><p><? echo change_date_format($row['RECEIVED_DATE']);?></p></td>
				</tr>
            <?
			$i++;
            }
			?>
			</tbody>
		</table>

		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%"> 
						<div style="width:50%; float:left" align="left">
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
	$sql="select a.benificiary_id as BENIFICIARY_ID, e.id as LC_SC_ID, e.export_lc_no as SC_LC_NO, e.lc_value as SC_LC_VALUE, e.lc_year as SC_LC_YEAR, e.bank_file_no as BANK_FILE_NO, b.id as RLZ_DTLS_ID, b.document_currency as DOCUMENT_CURRENCY, d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT, d.is_lc as IS_LC
	from  com_export_proceed_rlzn_dtls b,  com_export_proceed_realization a,  com_export_doc_submission_invo d, com_export_lc e 
	where b.mst_id=a.id and a.invoice_bill_id=d.doc_submission_mst_id  and d.lc_sc_id=e.id and d.is_lc=1 and b.type=1  and a.is_invoice_bill=1 and  a.id in ($data) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	 union all
	select a.benificiary_id as BENIFICIARY_ID, e.id as LC_SC_ID, e.contract_no as SC_LC_NO, e.contract_value as SC_LC_VALUE, e.sc_year as SC_LC_YEAR, e.bank_file_no as BANK_FILE_NO, b.id as RLZ_DTLS_ID, b.document_currency as DOCUMENT_CURRENCY, d.id as SUB_DTLS_ID, d.net_invo_value as BILL_AMNT, d.is_lc as IS_LC
	from  com_export_proceed_rlzn_dtls b,  com_export_proceed_realization a,  com_export_doc_submission_invo d, com_sales_contract e 
	where b.mst_id=a.id and a.invoice_bill_id=d.doc_submission_mst_id  and d.lc_sc_id=e.id and d.is_lc=2 and b.type=1  and a.is_invoice_bill=1 and  a.id in ($data) and a.status_active=1 and a.is_deleted=0 and b.status_active=1
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
		$lc_sc_no .=$row['SC_LC_NO'].',';
		$lc_sc_year .=$row['SC_LC_YEAR'].',';
		$bank_file .=$row['BANK_FILE_NO'].',';
	}
	echo "document.getElementById('txt_lc_sc_no').value 		= '".implode(",",array_unique(explode(",",chop($lc_sc_no,','))))."';\n";
	echo "document.getElementById('txt_bank_file_no').value 	= '".implode(",",array_unique(explode(",",chop($bank_file,','))))."';\n";
	echo "document.getElementById('txt_invoice_value').value 	= '".array_sum($invoice_value_arr)."';\n";
	echo "document.getElementById('txt_net_realize_value').value = '".array_sum($realization_value_arr)."';\n";
	echo "document.getElementById('txt_lc_value').value 		= '".array_sum($lc_sc_value_arr)."';\n";
	echo "document.getElementById('txt_file_year').value 		= '".implode(",",array_unique(explode(",",chop($lc_sc_year,','))))."';\n";
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

		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, entry_form company_id, bank_id, submission_date, submission_invoice_id, realization_id, incentive_bank_file, net_realize_value, remarks, special_submitted_chk, euro_incentive_chk, general_incentive_chk, market_submitted_chk, special_submitted, euro_incentive, general_incentive, market_submitted, amount,is_lc_sc,lc_sc_id,internal_file_no, file_no_string, inserted_by, insert_date, status_active, is_deleted";
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',565,".$cbo_company_name.",".$cbo_bank_name.",".$txt_submission_date.",".$submission_invoice_id.",".$realization_id.",".$txt_incective_bank_file.",".$txt_net_realize_value.",".$txt_remarks.",".$special_submitted_chk.",".$euro_incentive_chk.",".$general_incentive_chk.",".$market_submitted_chk.",".$special_submitted.",".$euro_incentive.",".$general_incentive.",".$market_submitted.",".$total_amount.",".$is_lc_sc.",".$lc_sc_id.",".$txt_internal_file_no.",".$txt_file_no_string.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		// echo "10**INSERT INTO CASH_INCENTIVE_SUBMISSION (".$field_array_mst.") VALUES ".$data_array_mst."</br>"; 
		// die;
		$txt_file_no_string=str_replace("'","",$txt_file_no_string);
		$txt_file_no_string_arr=explode("*",$txt_file_no_string);
		$dtls_id=return_next_id("id", "cash_incentive_submission_dtls", 1);
		$field_array_dtls="id, mst_id, submission_bill_id, realization_id, lc_sc_id, is_lc_sc, file_no, inserted_by, insert_date, status_active, is_deleted";
		foreach($txt_file_no_string_arr as $file_str)
		{
			$file_str_arr=explode("_",$file_str);
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",'".$mst_id."','".$file_str_arr[4]."','".$file_str_arr[3]."','".$file_str_arr[1]."','".$file_str_arr[0]."','".$file_str_arr[2]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$dtls_id++;
			
		}
		//echo "10**".$txt_file_no_string;disconnect($con);die;
		
		$rID=sql_insert("cash_incentive_submission",$field_array_mst,$data_array_mst,0);
		$rID2=sql_insert("cash_incentive_submission_dtls",$field_array_dtls,$data_array_dtls,0);
		// echo '100**'.$rID;oci_rollback($con);die;
		
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

		$field_array_mst="company_id*bank_id*submission_date*submission_invoice_id*realization_id*incentive_bank_file*net_realize_value*remarks*special_submitted_chk*euro_incentive_chk*general_incentive_chk*market_submitted_chk*special_submitted*euro_incentive*general_incentive*market_submitted*amount*is_lc_sc*lc_sc_id*internal_file_no*file_no_string*updated_by*update_date";
		$data_array_mst="".$cbo_company_name."*".$cbo_bank_name."*".$txt_submission_date."*".$submission_invoice_id."*".$realization_id."*".$txt_incective_bank_file."*".$txt_net_realize_value."*".$txt_remarks."*".$special_submitted_chk."*".$euro_incentive_chk."*".$general_incentive_chk."*".$market_submitted_chk."*".$special_submitted."*".$euro_incentive."*".$general_incentive."*".$market_submitted."*".$total_amount."*".$is_lc_sc."*".$lc_sc_id."*".$txt_internal_file_no."*".$txt_file_no_string."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$txt_file_no_string=str_replace("'","",$txt_file_no_string);
		$txt_file_no_string_arr=explode("*",$txt_file_no_string);
		$dtls_id=return_next_id("id", "cash_incentive_submission_dtls", 1);
		$field_array_dtls="id, mst_id, submission_bill_id, realization_id, lc_sc_id, is_lc_sc, file_no, inserted_by, insert_date, status_active, is_deleted";
		foreach($txt_file_no_string_arr as $file_str)
		{
			$file_str_arr=explode("_",$file_str);
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$update_id.",'".$file_str_arr[4]."','".$file_str_arr[3]."','".$file_str_arr[1]."','".$file_str_arr[0]."','".$file_str_arr[2]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$dtls_id++;
			
		}
		//echo "10**update cash_incentive_submission_dtls set update_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id = $update_id ";oci_rollback($con);disconnect($con);die;
		$rID=sql_update("CASH_INCENTIVE_SUBMISSION",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID3=execute_query("update cash_incentive_submission_dtls set update_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id = $update_id ");
		
		$rID2=sql_insert("cash_incentive_submission_dtls",$field_array_dtls,$data_array_dtls,0);
		//echo "10**$rID=$rID2=$rID3"; oci_rollback($con);disconnect($con);die;
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
		
		$rID=sql_delete("CASH_INCENTIVE_SUBMISSION",$field_array,$data_array,"id","".$update_id."",0);

		// echo "10**".$rID."</br>"; die;
		if($db_type==0)
		{
			if($rID==1)
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
			if($rID==1)
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
