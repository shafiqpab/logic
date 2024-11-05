<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
	exit();
}

/*if ($action=="load_drop_down_lc_year")
{
	$sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0  
	union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0";
	echo create_drop_down( "hide_year", 100,$sql,"lc_sc_year,lc_sc_year", 1, "-- Select --", 0,"");
	exit();
}*/

if($action=="lcsc_popup")
{
	echo load_html_head_contents("LC/SC Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    //echo $txt_hidden_pi_id;die;
	?>

	<script>
        function js_set_value( str )
        {
            $("#hidden_data").val(str);
            parent.emailwindow.hide();
        }
    </script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
		<form name="searchpofrm"  id="searchpofrm">
            <input type="hidden" name="hidden_data" id="hidden_data">
			<fieldset style="width:820px">
				<table style="margin-top:5px;" width="680" cellspacing="0" border="1" rules="all" cellpadding="0" class="rpt_table">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th>Search</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" onClick="reset_hide_field(1)" style="width:60px;"></th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active=1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name",1, "-- Select--",0,"",0 );
							?>
						</td>
						<td align="center">
							<?
								$arr=array(1=>'L/C',2=>'S/C',3=>'File No.');
								echo create_drop_down( "cbo_search_by", 150, $arr,"",1, "--- Select ---", '0',"load_search_td(this.value,$company_id)",0 );
							?>

						</td>
						<td align="center" id="searchText">
							<input type="text" name="txt_search_text" id="txt_search_common" class="text_boxes" />
						</td>
						<td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_name; ?>+'_'+<? echo $cbo_year; ?>, 'create_lc_search_list_view', 'search_div', 'com_incentive_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;"/>							
						</td>
				</tr>
			</table>
			<table width="100%" style="margin-top:5px">
				<tr>
					<td id="search_div"></td>
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

if($action==='create_lc_search_list_view')
{
 	$ex_data = explode("_",$data);
	$cbo_buyer_name = $ex_data[0];
    if($cbo_buyer_name==0) $cbo_buyer_name="%%"; else $cbo_buyer_name=$cbo_buyer_name;
    $cbo_search_by = $ex_data[1];
    $txt_search_common =  trim($ex_data[2]);
    $cbo_company_name = $ex_data[3];
    $cbo_year = $ex_data[4];

	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

    if($cbo_search_by==0)
    {
        $sql="select a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type from com_export_lc a where a.beneficiary_name='$cbo_company_name' and a.buyer_name like '$cbo_buyer_name' and lc_year='$cbo_year' and a.export_lc_no like '%$txt_search_common%' and a.status_active=1 and a.is_deleted=0 group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value
        union all
        select b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type from com_sales_contract b where b.beneficiary_name='$cbo_company_name' and b.buyer_name like '$cbo_buyer_name' and sc_year='$cbo_year' and b.contract_no like '%$txt_search_common%' and b.status_active=1 and b.is_deleted=0 group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value";
    }
    else if($cbo_search_by==1)
    {
        $sql="select a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type from com_export_lc a where a.beneficiary_name='$cbo_company_name' and a.buyer_name like '$cbo_buyer_name' and lc_year='$cbo_year' and a.export_lc_no like '%$txt_search_common%' and a.status_active=1 and a.is_deleted=0 group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value";
    }
    else if($cbo_search_by==2)
    {
        $sql="select b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type from com_sales_contract b where b.beneficiary_name='$cbo_company_name' and b.buyer_name like '$cbo_buyer_name' and sc_year='$cbo_year' and b.contract_no like '%$txt_search_common%' and b.status_active=1 and b.is_deleted=0 group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value";
    }
    else if($cbo_search_by==3)
    {
        $sql="select a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type from com_export_lc a where a.beneficiary_name='$cbo_company_name' and a.buyer_name like '$cbo_buyer_name' and lc_year='$cbo_year' and a.internal_file_no ='$txt_search_common' and a.status_active=1 and a.is_deleted=0 group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value
        union all
        select b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type from com_sales_contract b where b.beneficiary_name='$cbo_company_name' and b.buyer_name like '$cbo_buyer_name' and sc_year='$cbo_year' and b.internal_file_no ='$txt_search_common' and b.status_active=1 and b.is_deleted=0 group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value";
    }
	//echo $sql;die;
    $lc_sc_type_array=array (0=>"LC",1=>"SC");
	$arr=array (2=>$lc_sc_type_array,3=>$company_arr,4=>$buyer_arr);

	echo create_list_view("tbl_list_search", "LC/SC No,LC/SC Date,Type,Beneficiary,Buyer,LC/SC Value", "150,80,80,150,150,150","820","200",0, $sql , "js_set_value", "sc_lc_id,type,sc_lc_no", "", 1, "0,0,type,company_name,buyer_name,0", $arr , "sc_lc_no,lc_sc_date,type,company_name,buyer_name,sc_lc_value", "","",'0,3,0,0,0,2','','') ;
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name); 
    $cbo_buyer_name=str_replace("'","",$cbo_buyer_name); 
    $cbo_year=str_replace("'","",$cbo_year);
    $cbo_search_by=str_replace("'","",$cbo_search_by);
    $txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
    $txt_lcsc_id=str_replace("'","",$txt_lcsc_id);
    $lcsc_type=str_replace("'","",$lcsc_type);
    
    $currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $supplier_name_arr=return_library_array( "select id,supplier_name from lib_supplier ",'id','supplier_name');
    $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer ",'id','buyer_name');
    $buyer_add_arr=return_library_array( "select id,address_1 from lib_buyer ",'id','address_1');
    ob_start();
    if($rpt_type==1)
    {
        $company_info=sql_select( "select company_name,plot_no,level_no,road_no,block_no,country_id,city,zip_code,erc_no,erc_expiry_date from lib_company where id=$cbo_company_name");
        $company_address='';
		foreach($company_info as $row){
			if($row[csf('plot_no')]!=''){$company_address.=$row[csf('plot_no')].', ';}
			if($row[csf('level_no')]!=''){$company_address.=$row[csf('level_no')].', ';}
			if($row[csf('road_no')]!=''){$company_address.=$row[csf('road_no')].', ';}
			if($row[csf('block_no')]!=''){$company_address.=$row[csf('block_no')].', ';}
			if($row[csf('city')]!=''){$company_address.=$row[csf('city')].', ';}
			if($row[csf('zip_code')]!=''){$company_address.=$row[csf('zip_code')].', ';}
            if($row[csf('country_id')]!=''){$company_address.=$country_arr[$row[csf('country_id')]];}
            if($row[csf('erc_no')]!=''){$erc_no = $row[csf('erc_no')];}
            if($row[csf('erc_expiry_date')]!=''){$erc_expiry_date=change_date_format($row[csf('erc_expiry_date')]);}
		}
        $search_cond="";
        if($cbo_company_name!=0){$search_cond.=" and a.beneficiary_name=$cbo_company_name";}
        if($cbo_buyer_name!=0){$search_cond.=" and a.buyer_name=$cbo_buyer_name";}
        
        if($lcsc_type==0) // LC
        {
            $sql="SELECT a.export_lc_no as SC_LC_NO, a.lc_year as SC_LC_YEAR, a.lc_date as SC_LC_DATE, a.lc_value as SC_LC_VALUE, a.buyer_name as BUYER_NAME, a.issuing_bank_name as ISSUING_BANK, a.currency_name as CURRENCY_NAME, b.id as INVOICE_ID, b.bl_date as BL_DATE,b.country_id as COUNTRY_ID,b.exp_form_no as EXP_FORM_NO, b.invoice_no as INVOICE_NO, b.invoice_date as INVOICE_DATE,b.INVOICE_VALUE as INVOICE_VALUE 
            from com_export_lc a, com_export_invoice_ship_mst b 
            where a.id=$txt_lcsc_id and a.export_lc_no='$txt_lc_sc_no' and a.lc_year='$cbo_year' $search_cond and b.is_lc=1 and b.lc_sc_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
        }
        else if($lcsc_type==1) // SC
        {
            $sql="select a.contract_no as SC_LC_NO, a.sc_year as SC_LC_YEAR, a.contract_date as SC_LC_DATE, a.contract_value as SC_LC_VALUE, a.buyer_name as BUYER_NAME, a.issuing_bank as ISSUING_BANK, a.currency_name as CURRENCY_NAME, b.id as INVOICE_ID, b.bl_date as BL_DATE, b.country_id as COUNTRY_ID,b.exp_form_no as EXP_FORM_NO, b.invoice_no as INVOICE_NO, b.invoice_date as INVOICE_DATE,b.INVOICE_VALUE as INVOICE_VALUE 
            from com_sales_contract a, com_export_invoice_ship_mst b  
            where a.id=$txt_lcsc_id and a.contract_no='$txt_lc_sc_no' and a.sc_year='$cbo_year' $search_cond and b.is_lc=2 and b.lc_sc_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
        }        
        
        // echo $sql.'<br>';
        $sql_data=sql_select($sql);
        $invoice_id_arr=array();
        foreach($sql_data as $row)
        {
            $invoice_id_arr[$row['INVOICE_ID']]=$row['INVOICE_ID'];
        }
        $search_invoice=where_con_using_array($invoice_id_arr,0,'b.id');
        $invoice_sql="SELECT b.id, sum(c.current_invoice_qnty) as INVOICE_QUANTITY ,sum(c.current_invoice_value) as INVOICE_VALUE, e.order_uom as ORDER_UOM from com_export_invoice_ship_mst b,com_export_invoice_ship_dtls c, wo_po_break_down d,wo_po_details_master e where b.id=c.mst_id and c.po_breakdown_id=d.id and d.job_id=e.id $search_invoice and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by b.id,e.order_uom ";
        // echo $invoice_sql.'<br>';
        $invoice_sql_data=sql_select($invoice_sql);

        $realization_sql="SELECT sum(c.net_invo_value) as NET_BANK_VALUE from com_export_invoice_ship_mst b, com_export_doc_submission_invo c,com_export_doc_submission_mst d where b.id=c.invoice_id and b.is_lc=c.is_lc and b.lc_sc_id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 $search_invoice and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";
        // echo $realization_sql;
        $realization_sql_data=sql_select($realization_sql);
        ?>
            <style>
                .wrd_brk{word-break: break-all;}
                .left{text-align: left;}
                .center{text-align: center;}
                .right{text-align: right;}
            </style>
            <div style="width:900px;" id="scroll_body">
                <table width="900" cellpadding="0" cellspacing="0" border="0" rules="all">
                    <tr>
                        <td class="left"><strong style="font-size:10px;">অনুচ্চেদ ০৪( ক) , এফই সার্কুলার নং -০১/২০২০ দ্রষ্ঠব্য</strong></td>
                    </tr>
                    <tr>
                        <td class="center" ><strong><u style="font-size:18px;">ফরম-খ</u></strong></td>
                    </tr>
                    <tr>
                        <td class="center"><strong style="font-size:14px;">বিজিএমইএ কর্তৃক প্রদেয়  সনদপত্র</strong></td>
                    </tr>
                    <tr>
                        <td class="center"><strong style="font-size:14px;">তৈরী পোশাক রপ্তানির বিপরীতে বিশেষ নগদ সহায়তা প্রাপ্তির প্রত্যয়ন সনদ পত্র ।</strong></td>
                    </tr>
                </table>
                <table width="900" cellpadding="0" cellspacing="0" border="0" rules="all">
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25">১</td>
                        <td class="left" width="400"><strong>আবেদনকারী প্রতিষ্ঠানের নাম, ঠিকানা ও ইআরসি নং :</strong></td>
                        <td class="left"><?=$company_info[0][csf('company_name')];?></td>
                    </tr>
                    <tr>
                        <td class="center" width="25"></td>
                        <td ></td>
                        <td><?=$company_address;?></td>
                    </tr>
                    <tr>
                        <td class="center" width="25"></td>
                        <td ></td>
                        <td><?echo $erc_no."  Date:  ".$erc_expiry_date;?></td>
                    </tr>
                    <tr> <td colspan="3" height="10"> </td> </tr>
                    <tr>
                        <td class="center" width="25" valign="top">২</td>
                        <td valign="top" ><strong>আবেদনকারী প্রতিষ্ঠানের নাম, ঠিকানা ও ইআরসি নং :</strong></td>
                        <td class="left">
                            <table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <td class="center" width="250"><strong>CONTACT NO.</strong></td>
                                        <td class="center" width="100"><strong>DATE</strong></td>
                                        <td class="center"><strong>VALUE</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="center wrd_brk"><?=$sql_data[0]['SC_LC_NO'];?></td>
                                        <td class="center"><?=change_date_format($sql_data[0]['SC_LC_DATE']);?></td>
                                        <td class="right"><?=number_format($sql_data[0]['SC_LC_VALUE'],2);?>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="right">Total Value (USD.):  </td>
                                        <td class="right"><?=number_format($sql_data[0]['SC_LC_VALUE'],2);?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr> <td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25">৩</td>
                        <td ><strong>বিদেশী ক্রেতার নাম ও ঠিকানা: </strong></td>
                        <td ><?=$buyer_name_arr[$sql_data[0]['BUYER_NAME']];?></td>
                    </tr>
                    <tr>
                        <td class="center" width="25"></td>
                        <td ></td>
                        <td ><?=$buyer_add_arr[$sql_data[0]['BUYER_NAME']];?></td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25">৪</td>
                        <td ><strong>ক্রেতার ব্যাংকের নাম ও ঠিকানা</strong></td>
                        <td ><?echo $sql_data[0]['ISSUING_BANK'];?></td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" valign="top" width="25">৫</td>
                        <td valign="top"><strong>(ক)  ইনভয়েস নং ও তারিখ </strong></td>
                        <td class="center">
                            <table width="350" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tbody>
                                    <?
                                        foreach($sql_data as $row)
                                        {
                                            ?>
                                                <tr>
                                                    <td class="left" width="250"><?=$row['INVOICE_NO'];?></td>
                                                    <td class="center"><?=change_date_format($row['INVOICE_DATE']);?></td>
                                                </tr>
                                            <?
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25"></td>
                        <td  valign="top"><strong>(খ) ইনভয়েসে উল্লেখিত পণ্যের পরিমান ও মূল্য</strong></td>
                        <td class="center">
                            <table width="300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tbody>
                                    <?
                                        foreach($invoice_sql_data as $row)
                                        {
                                            ?>
                                                <tr>
                                                    <td class="center" width="100"><?=$row['INVOICE_QUANTITY'];?></td>
                                                    <td class="center" width="80"><?=$unit_of_measurement[$row['ORDER_UOM']];?></td>
                                                    <td class="right"><?=number_format($row['INVOICE_VALUE'],2);?></td>
                                                </tr>
                                            <?
                                            $total_invoice_qnty+=$row['INVOICE_QUANTITY'];
                                            $total_invoice_value+=$row['INVOICE_VALUE'];
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25">৬</td>
                        <td colspan="2"><strong>রপ্তানীকৃত তৈরি পোশাক উৎপাদনে  স্থানীয়   উপকরনাদির সংগ্রহসূত্র (সরবরাহকারীর  নাম ও ঠিকানা ) পরিমান ও মূল্য :</strong></td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25">৭</td>
                        <td colspan="2"><strong>রপ্তানীকৃত তৈরি পোশাক উৎপাদনে  আমদানীকৃত উপকরনাদির সংগ্রহসূত্র (সরবরাহকারীর  নাম ও ঠিকানা ) পরিমান ও মূল্য :</strong></td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25">৮</td>
                        <td ><strong>রপ্তানী পণ্যের বিবরন:</strong></td>
                        <td class="center">
                            <table width="300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tbody>
                                    <td class="center">RMG</td>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25"></td>
                        <td  valign="top"><strong>পরিমান ও মূল্য: </strong></td>
                        <td class="center">
                            <table width="400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tbody>
                                    <td class="right"><?=$total_invoice_qnty;?></td>
                                    <td class="center">PCS</td>
                                    <td class="right"><?=$currency_sign_arr[$sql_data[0]['CURRENCY_NAME']].' '.number_format($total_invoice_value,2);?></td>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25" valign="top">৯</td>
                        <td  valign="top"><strong>জাহাজীকরনের তারিখ:</strong></td>
                        <td class="center">
                            <table width="300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tbody>
                                    <?
                                        foreach($sql_data as $row)
                                        {
                                            ?>
                                                <tr>
                                                    <td class="center" width="100"><?echo change_date_format($row['BL_DATE']);?></td>
                                                    <td class="center" width="100">গন্তব্য</td>
                                                    <td class="center"><?echo $country_arr[$row['COUNTRY_ID']];?></td>
                                                </tr>
                                            <?
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25" valign="top">১০</td>
                        <td  valign="top"><strong>ইএক্সপি নম্বর: </strong></td>
                        <td class="center">
                            <table width="300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tbody>
                                    <?
                                        foreach($sql_data as $row)
                                        {
                                            ?>
                                                <tr>
                                                    <td class="center" width="100"><?echo $row['EXP_FORM_NO'];?></td>
                                                    <td class="center" width="100">মূল্য ঃ </td>
                                                    <td class="right"><?echo number_format($row['INVOICE_VALUE'],2);?></td>
                                                </tr>
                                            <?
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25" valign="top">১১</td>
                        <td  valign="top"><strong>বৈদেশিক মূদ্রায় মোট প্রত্যাবাসিত মূল্য:</strong></td>
                        <td class="center">
                            <table width="400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tbody>
                                    <td class="right" width="150"><?=number_format($realization_sql_data[0]['NET_BANK_VALUE']);?></td>
                                    <td class="right" width="150">নীট এফ ও বি মূল্যঃ</td>
                                    <td class="right"><?=number_format($realization_sql_data[0]['NET_BANK_VALUE']);?></td>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25" valign="top">১২</td>
                        <td  valign="top"><strong>প্রত্যাবাসিত মূল্য এর সনদপত্রের নং:</strong></td>
                        <td class="center">
                            <table width="400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tbody>
                                    <td class="right" width="150"></td>
                                    <td class="right" width="150"><strong>তারিখঃ</strong></td>
                                    <td class="right"></td>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3" height="100"> </td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3"><strong>রপ্তানীকারকের স্বাক্ষর ও তারিখ</strong></td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3"><strong>এতদ্বারা প্রত্যায়ন করা যাই যে , আমাদের নিজস্ব কারখানায়  উৎপাদিত তৈরি পোশাক উপরোক্ত ৬ ও ৭ নং ক্রমিকে বর্নিত সূত্র হতে উপকরনাদি সংগ্রহের মাধ্যমে রপ্তানির বিপরীতে বিশেষ নগদ  সহায়তার জন্য  উপরোক্ত অনুচ্ছেদসমূহের বক্তব্য সঠিক ও নিভর্‚ল ।  বিদেশী ক্রেতা বা আমদানীকারকের ক্রয়াদেশ  যথার্থতা /বিশ্বাস যোগ্যতা সম্পর্কেও নিশ্চিত করা হইল ।</strong></td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3" height="100"></td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3"><strong>রপ্তানীকারকের স্বাক্ষর ও  তারিখ</strong></td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3"><strong>রপ্তানীকারকের উপরোক্ত ঘোষনাগুলির যথার্থতা যাচাইয়ে সম্পূর্ণ সঠিক পাওয়া গিয়াছে । ৮ নং ক্রমিকে উল্লিখিত ঘোষিত রপ্তানী মূল্য যৌক্তিক ও বিদ্যমান আন্তর্জাতিক বাজার মূল্যের সাথে সংগতিপূর্ণ পাওয়া গিয়াছে এবং বিদেশী ক্রেতার যথার্থতা / বিশ্বাসযোগ্যতা সম্পর্কেও নিশ্চিত হওয়া গিয়াছে । প্রত্যাবাসিত রপ্তানি মূল্যের ( নীট এফ ও বি মূল্য ) ওপর বিশেষ নগদ সহায়তার পরিশোধের সুপারিশ করা হইল।</strong></td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3" height="100"></td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3"><strong>বিজিএমইএ /বিকেএমইএ / বিটিএমইএ  এর  দুইজন কর্মকর্তার স্বাক্ষর ও সীল</strong></td>
                    </tr>
                    <tr>
                        <td class="center" colspan="3"><strong>( কোন প্রকার ঘষামাজা ,কাঁটাছেড়া  বা সংশোধন করা হইলে  এ প্রত্যায়নপত্র বাতিল বলিয়া গণ্য হবে )</strong></td>
                    </tr>
                </table>
            </div>
        <?
    }
    if($rpt_type==2)
    {
        $search_cond="";
        if($cbo_company_name!=0){$search_cond.=" and a.beneficiary_name=$cbo_company_name";}
        if($cbo_buyer_name!=0){$search_cond.=" and a.buyer_name=$cbo_buyer_name";}
        // , d.bank_ref_no as BILL_NO

        if ($lcsc_type==0) // LC 
        {
            $sql="SELECT a.export_lc_no as SC_LC_NO, a.lc_date as SC_LC_DATE, a.lc_value as SC_LC_VALUE, a.currency_name as CURRENCY_NAME, b.id as INVOICE_ID, b.exp_form_no as EXP_FORM_NO,b.invoice_value as INVOICE_VALUE, sum(c.net_invo_value) as BANK_VALUE, d.id as DOC_MST_ID,d.BANK_REF_NO,max(e.received_date) as REALIZATION_DATE, sum(f.document_currency) as DOCUMENT_CURRENCY
            from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c,com_export_doc_submission_mst d
            left join com_export_proceed_realization e on d.id=e.invoice_bill_id and e.status_active=1 and e.is_deleted=0
            left join com_export_proceed_rlzn_dtls f on e.id=f.mst_id and f.type=0 and f.status_active=1 and f.is_deleted=0
            where a.id=$txt_lcsc_id and a.export_lc_no='$txt_lc_sc_no' and a.lc_year='$cbo_year' $search_cond and b.is_lc=1 and b.lc_sc_id=a.id and b.id=c.invoice_id and b.is_lc=c.is_lc and b.lc_sc_id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 
            and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  
            group by a.export_lc_no, a.lc_date, a.lc_value, a.currency_name, b.id, b.exp_form_no,b.invoice_value, d.id,d.BANK_REF_NO";
        }
        else if($lcsc_type==1) // SC
        {
            $sql="SELECT a.contract_no as SC_LC_NO, a.contract_date as SC_LC_DATE, a.contract_value as SC_LC_VALUE, a.currency_name as CURRENCY_NAME, b.id as INVOICE_ID, b.exp_form_no as EXP_FORM_NO, b.invoice_value as INVOICE_VALUE, sum(c.net_invo_value) as BANK_VALUE, d.id as DOC_MST_ID,d.BANK_REF_NO, max(e.received_date) as REALIZATION_DATE, sum(f.document_currency) as DOCUMENT_CURRENCY
            from com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c,com_export_doc_submission_mst d
            left join com_export_proceed_realization e on d.id=e.invoice_bill_id and e.status_active=1 and e.is_deleted=0
            left join com_export_proceed_rlzn_dtls f on e.id=f.mst_id and f.type=0 and f.status_active=1 and f.is_deleted=0
            where a.id=$txt_lcsc_id and a.contract_no='$txt_lc_sc_no' and a.sc_year='$cbo_year' $search_cond and b.is_lc=2 and b.lc_sc_id=a.id and b.id=c.invoice_id and b.is_lc=c.is_lc and b.lc_sc_id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
            group by a.contract_no, a.contract_date, a.contract_value, a.currency_name, b.id, b.exp_form_no, b.invoice_value, d.id,d.BANK_REF_NO";
        }       
        // echo $sql.'<br>';
        $total_inv_bank_val=array();
        $sql_data=sql_select($sql);
        foreach($sql_data as $row)
        {
            $total_incoice_val+=$row['INVOICE_VALUE'];
            $total_inv_bank_val[$row['DOC_MST_ID']]+=$row['BANK_VALUE'];
        }
        foreach($sql_data as $row)
        {
            $against_realization+=$row['BANK_VALUE']-(($row['BANK_VALUE']/$total_inv_bank_val[$row['DOC_MST_ID']]*1)*$row['DOCUMENT_CURRENCY']);
        }
        ?>
            <style>
                .wrd_brk{word-break: break-all;}
                .left{text-align: left;}
                .center{text-align: center;}
                .right{text-align: right;}
            </style>
            <div style="width:900px;" id="scroll_body">
                <table width="900" cellpadding="0" cellspacing="0" border="0" rules="all">
                    <tr>
                        <td colspan="2"><strong>Ref: </strong></td>
                    </tr>
                    <tr>
                        <td  colspan="2" class="center"><strong><u style="font-size:16px;">EXPORT PROCEEDS REALIZATION CERTIFICATE</u></strong></td>
                    </tr>
                    <tr>
                        <td  colspan="2" class="center"><strong>FOR 1% CASH ASSISTANCE</strong></td>
                    </tr>
                    <tr>
                        <td  colspan="2" height="30"></td>
                    </tr>
                    <tr>
                        <td width="300" valign="top">We  certify  that  we  have  received <?=number_format($against_realization,2);?> against</td>
                        <td >
                            <table width="350" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tr>
                                    <td class="center" width="150"><strong>Sales Contract #</strong></td>
                                    <td class="center" width="100"><strong>Date</strong></td>
                                    <td class="center"><strong>Value</strong></td>
                                </tr>
                                <tr>
                                    <td class="center wrd_brk"><?=$sql_data[0]['SC_LC_NO'];?></td>
                                    <td class="center"><?=change_date_format($sql_data[0]['SC_LC_DATE']);?></td>
                                    <td class="right"><?=number_format($sql_data[0]['SC_LC_VALUE'],2);?>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td  colspan="2" height="20"></td>
                    </tr>
                    <tr>
                        <td  colspan="2" >favouring <?=$company_arr[$cbo_company_name];?> being the proceeds of export against EXP Form Numbers as mentioned below :</td>
                    </tr>
                    <tr>
                        <td  colspan="2" height="10"></td>
                    </tr>
                </table>
                <table width="900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <td class="center" width="200"><strong>Bill No.</strong></td>
                            <td class="center" width="250"><strong>Exp Number</strong></td>
                            <td class="center" width="100"><strong>Invoice Value </strong></td>
                            <td class="center" width="100"><strong>Realized Value </strong></td>
                            <td class="center" width="100"><strong>Realized Date</strong></td>
                            <td class="center"><strong>Reporting Month</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                            foreach($sql_data as $row)
                            {
                                $realization_value=$against_realization*($row['INVOICE_VALUE']/$total_incoice_val);
                                $total_realization+=$realization_value;
                                ?>
                                    <tr>
                                        <td class="wrd_brk "><?=$row['BANK_REF_NO'];?></td>
                                        <td class="wrd_brk "><?echo $row['EXP_FORM_NO'];?></td>
                                        <td class="wrd_brk right"><?=number_format($row['INVOICE_VALUE'],2);?></td>
                                        <td class="wrd_brk right"><?=number_format($realization_value,2);?></td>
                                        <td class="wrd_brk center"><?=change_date_format($row['REALIZATION_DATE']);?></td>
                                        <td class="wrd_brk center"><?=date("M/Y", strtotime($row['REALIZATION_DATE']));?></td>
                                    </tr>
                                <?
                                $total_invoice+=$row['INVOICE_VALUE'];
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="right"> <b>Total </b></td>
                            <td class="right"><b><?=number_format($total_invoice,2);?></b></td>
                            <td class="right"><b><?=number_format($total_realization,2);?></b></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <table width="900" cellpadding="0" cellspacing="0" border="0" rules="all">
                    <tr>
                        <td colspan="2" height="20"></td>
                    </tr>
                    <tr>
                        <td colspan="2">The transactions have been/will be reported to the Foreign Exchange Policy Department, Bangladesh Bank,Head Office, Dhaka in Schedule A -1/0 -1 summery statement S -1 </td>
                    </tr>
                    <tr>
                        <td colspan="2" height="100"></td>
                    </tr>
                    <tr>
                        <td class="center">Authorized Signature</td>
                        <td class="center">Authorized Signature</td>
                    </tr>
                </table>
            </div>
        <?
    }
    if($rpt_type==3)
    {
        $company_info=sql_select( "select company_name,plot_no,level_no,road_no,block_no,country_id,city,zip_code,erc_no,erc_expiry_date from lib_company where id=$cbo_company_name");
        $company_address='';
		foreach($company_info as $row){
			if($row[csf('plot_no')]!=''){$company_address.=$row[csf('plot_no')].', ';}
			if($row[csf('level_no')]!=''){$company_address.=$row[csf('level_no')].', ';}
			if($row[csf('road_no')]!=''){$company_address.=$row[csf('road_no')].', ';}
			if($row[csf('block_no')]!=''){$company_address.=$row[csf('block_no')].', ';}
			if($row[csf('city')]!=''){$company_address.=$row[csf('city')].', ';}
			if($row[csf('zip_code')]!=''){$company_address.=$row[csf('zip_code')].', ';}
            if($row[csf('country_id')]!=''){$company_address.=$country_arr[$row[csf('country_id')]];}
            if($row[csf('erc_no')]!=''){$erc_no = $row[csf('erc_no')];}
            if($row[csf('erc_expiry_date')]!=''){$erc_expiry_date=change_date_format($row[csf('erc_expiry_date')]);}
		}
        $search_cond="";
        if($cbo_company_name!=0){$search_cond.=" and a.beneficiary_name=$cbo_company_name";}
        if($cbo_buyer_name!=0){$search_cond.=" and a.buyer_name=$cbo_buyer_name";}

        if ($lcsc_type==0) // LC
        {
            $sql="SELECT a.export_lc_no as SC_LC_NO, a.lc_date as SC_LC_DATE, a.lc_value as SC_LC_VALUE, a.currency_name as CURRENCY_NAME, c.lc_number as BTB_NUMBER, c.lc_date as BTB_DATE, c.lc_value as BTB_VALUE, c.supplier_id as SUPPLIER_ID, e.item_category_id as ITEM_CATEGORY_ID, e.source as SOURCE
            from com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_master_details e
            where a.id=$txt_lcsc_id and a.export_lc_no='$txt_lc_sc_no' and a.lc_year='$cbo_year' $search_cond and b.is_lc_sc=0 and b.lc_sc_id=a.id and b.import_mst_id=c.id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.status_active=1 
            group by a.export_lc_no, a.lc_date, a.lc_value, a.currency_name, c.lc_number , c.lc_date, c.lc_value, c.supplier_id, e.item_category_id, e.source";
        }
        else if($lcsc_type==1) // SC
        {
            $sql="select a.contract_no as SC_LC_NO, a.contract_date as SC_LC_DATE, a.contract_value as SC_LC_VALUE, a.currency_name as CURRENCY_NAME, c.lc_number as BTB_NUMBER, c.lc_date as BTB_DATE, c.lc_value as BTB_VALUE, c.supplier_id as SUPPLIER_ID, e.item_category_id as ITEM_CATEGORY_ID, e.source as SOURCE
            from com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_master_details c, com_btb_lc_pi d, com_pi_master_details e
            where a.id=$txt_lcsc_id and a.contract_no='$txt_lc_sc_no' and a.sc_year='$cbo_year' $search_cond and b.is_lc_sc=1 and b.lc_sc_id=a.id and b.import_mst_id=c.id and c.id=d.com_btb_lc_master_details_id and d.pi_id=e.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.status_active=1
            group by a.contract_no, a.contract_date, a.contract_value, a.currency_name, c.lc_number, c.lc_date, c.lc_value, c.supplier_id, e.item_category_id, e.source";
        }
        // echo $sql.'<br>';
        $sql_data=sql_select($sql);

        $realization_sql="SELECT a.export_lc_no as SC_LC_NO, a.lc_date as SC_LC_DATE, a.lc_value as SC_LC_VALUE, a.currency_name as CURRENCY_NAME, b.id as INVOICE_ID, b.exp_form_no as EXP_FORM_NO, b.country_id as COUNTRY_ID, b.invoice_quantity as INVOICE_QUANTITY, b.invoice_value as INVOICE_VALUE, b.bl_date as BL_DATE, sum(c.net_invo_value) as BANK_VALUE, d.id as DOC_MST_ID,max(e.received_date) as REALIZATION_DATE, sum(f.document_currency) as DOCUMENT_CURRENCY
        from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c,com_export_doc_submission_mst d
        left join com_export_proceed_realization e on d.id=e.invoice_bill_id and e.status_active=1 and e.is_deleted=0
        left join com_export_proceed_rlzn_dtls f on e.id=f.mst_id and f.type=0 and f.status_active=1 and f.is_deleted=0
        where a.export_lc_no='$txt_lc_sc_no' and a.lc_year='$cbo_year' $search_cond and b.is_lc=1 and b.lc_sc_id=a.id and b.id=c.invoice_id and b.is_lc=c.is_lc and b.lc_sc_id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 
        and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  
        group by a.export_lc_no, a.lc_date, a.lc_value, a.currency_name, b.id, b.exp_form_no,b.country_id, b.invoice_quantity, b.invoice_value, b.bl_date, d.id 
        union all 
        select a.contract_no as SC_LC_NO, a.contract_date as SC_LC_DATE, a.contract_value as SC_LC_VALUE, a.currency_name as CURRENCY_NAME, b.id as INVOICE_ID, b.exp_form_no as EXP_FORM_NO, b.country_id as COUNTRY_ID, b.invoice_quantity as INVOICE_QUANTITY, b.invoice_value as INVOICE_VALUE, b.bl_date as BL_DATE, sum(c.net_invo_value) as BANK_VALUE, d.id as DOC_MST_ID, max(e.received_date) as REALIZATION_DATE, sum(f.document_currency) as DOCUMENT_CURRENCY
        from com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c,com_export_doc_submission_mst d
        left join com_export_proceed_realization e on d.id=e.invoice_bill_id and e.status_active=1 and e.is_deleted=0
        left join com_export_proceed_rlzn_dtls f on e.id=f.mst_id and f.type=0 and f.status_active=1 and f.is_deleted=0
        where  a.contract_no='$txt_lc_sc_no' and a.sc_year='$cbo_year' $search_cond and b.is_lc=2 and b.lc_sc_id=a.id and b.id=c.invoice_id and b.is_lc=c.is_lc and b.lc_sc_id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
        group by a.contract_no, a.contract_date, a.contract_value, a.currency_name, b.id, b.exp_form_no, b.country_id, b.invoice_quantity, b.invoice_value, b.bl_date, d.id ";
        // echo $realization_sql.'<br>';
        $total_inv_bank_val=array();
        $realization_sql_data=sql_select($realization_sql);
        foreach($realization_sql_data as $row)
        {
            $total_incoice_val+=$row['INVOICE_VALUE'];
            $total_inv_bank_val[$row['DOC_MST_ID']]+=$row['BANK_VALUE'];
        }
        foreach($realization_sql_data as $row)
        {
            $against_realization+=$row['BANK_VALUE']-(($row['BANK_VALUE']/$total_inv_bank_val[$row['DOC_MST_ID']]*1)*$row['DOCUMENT_CURRENCY']);
        }
        ?>
            <style>
                .wrd_brk{word-break: break-all;}
                .left{text-align: left;}
                .center{text-align: center;}
                .right{text-align: right;}
            </style>
            <div style="width:900px;" id="scroll_body">
                <table width="900" cellpadding="0" cellspacing="0" border="0" rules="all">
                    <tr>
                        <td >
                            <div style="width:100%; display: flex;justify-content: space-between;">
                                <div >(অনুচ্ছেদ ০৩(খ) এফই সার্কুলার নং- ০১/২০২০ দ্রষ্টব্য)</div>
                                <div >ফরম-ক”</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="center" ><strong><u style="font-size:18px;">তৈরী পোশাক রপ্তানির বিপরীতে বিশেষ নগদ সহায়তার আবেদনপত্র</u></strong></td>
                    </tr>
                </table>
                <table width="900" cellpadding="0" cellspacing="0" border="0" rules="all">
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" width="25">(ক)</td>
                        <td class="left" width="300">আবেদনকারী প্রতিষ্ঠানের নাম ও ঠিকানা: </td>
                        <td class="left"><?=$company_info[0][csf('company_name')];?></td>
                    </tr>
                    <tr>
                        <td class="center"></td>
                        <td ></td>
                        <td><?=$company_address;?></td>
                    </tr>
                    <tr>
                        <td class="center" ></td>
                        <td >রপ্তানী নিবন্ধন সনদপত্র নম্বর:</td>
                        <td><?echo $erc_no;?></td>
                    </tr>
                    <tr> <td colspan="3" height="10"> </td> </tr>
                    <tr>
                        <td class="center" width="25" valign="top">(খ)</td>
                        <td valign="top" >রপ্তানী ঋনপত্রের নম্বর, তারিখ ও মুল্য:</td>
                        <td class="left">
                            <table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <td class="center" width="250"><strong>CONTACT NO.</strong></td>
                                        <td class="center" width="100"><strong>DATE</strong></td>
                                        <td class="center"><strong>VALUE USD$</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="center wrd_brk"><?=$sql_data[0]['SC_LC_NO'];?></td>
                                        <td class="center"><?=change_date_format($sql_data[0]['SC_LC_DATE']);?></td>
                                        <td class="right"><?=number_format($sql_data[0]['SC_LC_VALUE'],2);?>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="right">Total Value (USD.):  </td>
                                        <td class="right"><?=number_format($sql_data[0]['SC_LC_VALUE'],2);?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr> <td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" >(গ)</td>
                        <td >(পাঠযোগ্য সত্যায়িত কপি দাখিল করিতে হইবে) </td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td >(১) দেশীয় উপকরণাদি:</td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">
                            <table width="750" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <td class="center" width="250"><strong>সরবরাহকারীর নাম ও ঠিকানা</strong></td>
                                        <td class="center" width="100"><strong>পণ্যের নাম ও পরিমান</strong></td>
                                        <td class="center" width="300" colspan="2"><strong>অভ্যন্তরীণ ব্যাক টু ব্যাক ঋনপত্র/ ঋনপত্র/ ডকুমেন্টারী কালেকশন নম্বর, তারিখ</strong></td>
                                        <td class="center"><strong>মূল্য (মাঃ ডলার)</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="center" >(১)</td>
                                        <td class="center" >(২)</td>
                                        <td class="center" colspan="2">(৩)</td>
                                        <td class="center" >(৪)</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                        foreach($sql_data as $row)
                                        {
                                            if($row['SOURCE']==2 || $row['SOURCE']==3)
                                            {
                                                ?>
                                                    <tr>
                                                        <td class="wrd_brk"><?=$supplier_name_arr[$row['SUPPLIER_ID']];?></td>
                                                        <td class="center wrd_brk"><?=$item_category[$row['ITEM_CATEGORY_ID']];?></td>
                                                        <td class="wrd_brk"><?=$row['BTB_NUMBER'];?></td>
                                                        <td class="center wrd_brk"><?=change_date_format($row['BTB_DATE']);?></td>
                                                        <td class="right wrd_brk"><?=number_format($row['BTB_VALUE'],2);?></td>
                                                    </tr>
                                                <?
                                            }
                                        }
                                    ?>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" ></td>
                        <td colspan="2">(২) নং ক্যলামে উলে­খিত ঋনপত্রগুলির পাঠযোগ্য সত্যায়িত কপি দাখিল করিতে হইবে। রপ্তানী পণ্যের বর্ণনা, পরিমান মূল্য এবং সংগ্রহ সূত্রের বিষয়ে  বিজিএমইএ/ বিকেএমইএ /বিটিএমএ প্রদত্ত সনদপত্র দাখিল করিতে হইবে।</td>
                    </tr>
                    <tr>
                        <td class="center" ></td>
                        <td colspan="2">(৩) আমদানীকৃত আনুসংগিক উপকরণাদি:</td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td ></td>
                        <td colspan="2" >
                            <table width="750" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <td class="center" width="250"><strong>সরবরাহকারীর নাম ও ঠিকানা</strong></td>
                                        <td class="center" width="100"><strong>পণ্যের নাম ও পরিমান</strong></td>
                                        <td class="center" width="300" colspan="2"><strong>অভ্যন্তরীণ ঋনপত্রের নম্বর, তারিখ </strong></td>
                                        <td class="center"><strong>মূল্য (মাঃ ডলার)</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="center" >(১)</td>
                                        <td class="center" >(২)</td>
                                        <td class="center" colspan="2">(৩)</td>
                                        <td class="center" >(৪)</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                        foreach($sql_data as $row)
                                        {
                                            if($row['SOURCE']==1)
                                            {
                                                ?>
                                                    <tr>
                                                        <td class="wrd_brk"><?=$supplier_name_arr[$row['SUPPLIER_ID']];?></td>
                                                        <td class="center wrd_brk"><?=$item_category[$row['ITEM_CATEGORY_ID']];?></td>
                                                        <td class="wrd_brk"><?=$row['BTB_NUMBER'];?></td>
                                                        <td class="center wrd_brk"><?=change_date_format($row['BTB_DATE']);?></td>
                                                        <td class="right wrd_brk"><?=number_format($row['BTB_VALUE'],2);?></td>
                                                    </tr>
                                                <?
                                            }
                                        }
                                    ?>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" >(ঙ)</td>
                        <td colspan="2"> নং কলামে উলে­খিত ঋনপত্রগুলির পাঠযোগ্য সত্যায়িত কপি দাখিল করিতে হইবে</td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">
                            <table width="850" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <td class="center" width="100"><strong>পণ্যের বর্ণনা</strong></td>
                                        <td class="center" width="100"><strong>পরিমান পিস</strong></td>
                                        <td class="center" width="150" ><strong>আমদানিকারকের দেশের নাম</strong></td>
                                        <td class="center" width="100"><strong>ইনভয়েস মূল্য (বৈদেশিক মুদ্রায়) মাঃ ডঃ</strong></td>
                                        <td class="center" width="100"><strong>জাহাজীকরণের তারিখ</strong></td>
                                        <td class="center" width="100"><strong>ইএক্সপি নম্বর</strong></td>
                                        <td class="center" colspan="2"><strong>বৈদেশিক মদ্রায় প্রত্যাবাসিত রপ্তানী মূল্য (মাঃ ডঃ) ও প্রত্যাবাসনের তারিখ</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="center" >(১)</td>
                                        <td class="center" >(২)</td>
                                        <td class="center" >(৩)</td>
                                        <td class="center" >(৪)</td>
                                        <td class="center" >(৫)</td>
                                        <td class="center" >(৬)</td>
                                        <td class="center" colspan="2">(৭)</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                        foreach($realization_sql_data as $row)
                                        {
                                            $realization_val=$against_realization*($row['INVOICE_VALUE']/$total_incoice_val);
                                            $total_realization_val+=$realization_val;
                                            ?>
                                                <tr>
                                                    <td class="center" ><?="RMG";?></td>
                                                    <td class="right"><?=$row['INVOICE_QUANTITY'];?></td>
                                                    <td ><?=$country_arr[$row['COUNTRY_ID']];?></td>
                                                    <td class="right"><?=number_format($row['INVOICE_VALUE'],2);?></td>
                                                    <td class="center"><?=change_date_format($row['BL_DATE']);?></td>
                                                    <td ><?=$row['EXP_FORM_NO'];?></td>
                                                    <td class="right" width="100"><?=number_format($realization_val,2);?></td>
                                                    <td class="center"><?=change_date_format($row['REALIZATION_DATE']);?></td>
                                                </tr>
                                            <?
                                            $total_inv_qty+=$row['INVOICE_QUANTITY'];
                                            $total_inv_val+=$row['INVOICE_VALUE'];
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td ></td>
                                        <td class="right" ><?=number_format($total_inv_qty);?></td>
                                        <td class="right">Grand Total (USD.):</td>
                                        <td class="right" ><?=number_format($total_inv_val,2);?></td>
                                        <td ></td>
                                        <td ></td>
                                        <td class="right" ><?=number_format($total_realization_val,2);?></td>
                                        <td class="center" ></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2" >(রপ্তানী ইনভয়েস,প্যাকিং লিষ্ট এবং বিল অব লেডিং / এয়ারওয়ে বিল ইত্যাদির সত্যায়িত পাঠযোগ্য কপি এবং মূল্য রপ্তানীমূল্য প্রত্যাবাসন সনদপত্র (পিআরসি) দাখিল করিতে হইবে। </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center">(চ)</td>
                        <td >বিশেষ নগদ সহায়তার আবেদনকৃত অংক:</td>
                        <td > </td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">
                            <table width="850" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <td class="center" width="100"><strong>প্রত্যাবাসিত রপ্তানীমূল্য</strong></td>
                                        <td class="center" width="100"></td>
                                        <td class="center" width="250" ><strong>প্রযোজ্য ক্ষেত্রে জাহাজ ভাড়ার পরিমান</strong></td>
                                        <td class="center" width="150"><strong>বৈদেশিক মুদ্রায় বিদেশে পরিশোধ্য কমিশন, ইনসুরেন্স ইত্যাদি(যদি থাকে)</strong></td>
                                        <td class="center" width="100"><strong>নীট এফওবি রপ্তানী মূল্য (১)-(২+৩)</strong></td>
                                        <td class="center" ><strong>প্রাপ্য সহায়তা (১)*------   ১০০</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="center" >(১)</td>
                                        <td class="center" ></td>
                                        <td class="center" >(২)</td>
                                        <td class="center" >(৩)</td>
                                        <td class="center" >(৪)</td>
                                        <td class="center" >(৫)</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="right" colspan="2"><?=number_format($total_realization_val,2);?></td>
                                        <td ></td>
                                        <td ></td>
                                        <td class="right"><?=number_format($total_realization_val,2);?></td>
                                        <td class="right"><?=number_format($total_realization_val*.01,2);?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2" >(প্রযোজ্য ক্ষেত্রে জাহাজ ভাড়ার উলে­খ সম্মিলিত ফ্রেইট সার্টিফিকেটের সত্যায়িত কপি দাখিল করিতে হইবে। </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">* ২ নং অনুচ্ছেদে উলে­খিত হার। </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">এই মর্মে অঙ্গীকার করা যাইতেছে যে, আমাদের নিজস্ব কারখানায় প্রক্রিয়াজাত/উৎপাদিত পণ্য রপ্তানীর বিপরীতে বিশেষ নগদ সহায়তার  জন্য আবেদন করা হইল। এই আবেদনপত্রে প্রদত্ত তথ্যাদি/ঘোষনা সম্পূর্ণ সঠিক। যদি পরবর্তীতে ইহাতে কোন ভুল/অসত্য/প্রতারনা/জালিয়াতি প্রমানিত হয় তবে গৃহীত সহায়তার সমুদয় অর্থ বা উহার অংশ বিশেষ আমার/আমাদের নিকট হইতে আমার/আমাদের ব্যাংক হিসাব হইতে আদায় করিয়া লওয়া যাইবে। </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">
                            <div style="width:100%; display: flex;justify-content: space-between;">
                                <div >তারিখ:</div>
                                <div >------------------------------------</br>আবদেনকারী প্রতষ্ঠিানরে স্বত্ত্বাধকিারী/</br>ক্ষমতাপ্রাপ্ত কর্মকর্তার স্বাক্ষর ও পদবী</div>
                            </div>
                        </td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td class="center" >(ছ) </td>
                        <td colspan="2">বিশেষ নগদ সহায়তা প্রদানকারী ব্যাংক শাখা কর্তৃক পুরণীয়:</td>
                    </tr>
                    <tr><td colspan="3" height="10"> </td></tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">
                            <table width="850" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <td class="center" width="150"><strong>প্রত্যাবাসিত রপ্তানীমূল্য বৈদেশিক মুদ্রায় (USD.)</strong></td>
                                        <td class="center" width="250"><strong>বৈদেশিক মুদ্রায় বিদেশে পরিশোধ্য কমিশন,ইনন্সুরেন্স ইত্যাদি (যদি থাকে)</strong></td>
                                        <td class="center" width="200" ><strong>নীট এফওবি রপ্তানী মূল্য (১)-(২+৩) বৈদেশিক মুদ্রায় (১)-(২)</strong></td>
                                        <td class="center" ><strong>পরিশোধ্য সহায়তার পরিমান (টাকায়) ( ১)×------× রপ্তানীমূল্য প্রত্যাবাসন তারিখে ১০০  সংশ্লিষ্ট বৈদেশিক মুদ্রার ওডি সাইট ক্রয় মূল্য )</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="center" >(১)</td>
                                        <td class="center" >(২)</td>
                                        <td class="center" >(৩)</td>
                                        <td class="center" >(৪)</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td height="15"></td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">* ২ নং অনুচ্ছেদে উলে­খিত হার। </td>
                    </tr>
                    <tr><td colspan="3" height="50"> </td></tr>
                    <tr>
                        <td ></td>
                        <td colspan="2">পরিশোধিত সহায়তার পরিমান (টাকায়) </br>পরিশোধের তারিখ:</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="right">বিশেষ নগদ সহায়তার অনুমোদনের</br>ক্ষমতাপ্রাপ্ত ব্যাংক কর্মকর্তার স্বাক্ষর,নাম ও পদবী।</td>
                    </tr>
                </table>
            </div>
        <?
    }
    if($rpt_type==4)
    {
        $short_company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
        $short_buyer_name_arr=return_library_array( "select id,short_name from lib_buyer ",'id','short_name');

        $company_info=sql_select( "select company_name,plot_no,level_no,road_no,block_no,country_id,city,zip_code,erc_no,erc_expiry_date from lib_company where id=$cbo_company_name");
        $company_address='';
		foreach($company_info as $row){
			if($row[csf('plot_no')]!=''){$company_address.=$row[csf('plot_no')].', ';}
			if($row[csf('level_no')]!=''){$company_address.=$row[csf('level_no')].', ';}
			if($row[csf('road_no')]!=''){$company_address.=$row[csf('road_no')].', ';}
			if($row[csf('block_no')]!=''){$company_address.=$row[csf('block_no')].', ';}
			if($row[csf('city')]!=''){$company_address.=$row[csf('city')].', ';}
			if($row[csf('zip_code')]!=''){$company_address.=$row[csf('zip_code')].', ';}
            if($row[csf('country_id')]!=''){$company_address.=$country_arr[$row[csf('country_id')]];}
            if($row[csf('erc_no')]!=''){$erc_no = $row[csf('erc_no')];}
            if($row[csf('erc_expiry_date')]!=''){$erc_expiry_date=change_date_format($row[csf('erc_expiry_date')]);}
		}
        $sql_bank_info = sql_select("SELECT ID, BANK_NAME, ADDRESS from lib_bank ");
        $bank_dtls_arr=array();
        foreach($sql_bank_info as $row)
        {
            $bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
            $bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
        }
        $search_cond="";
        if($cbo_company_name!=0){$search_cond.=" and a.beneficiary_name=$cbo_company_name";}
        if($cbo_buyer_name!=0){$search_cond.=" and a.buyer_name=$cbo_buyer_name";}
        // , d.bank_ref_no as BILL_NO

        if ($lcsc_type==0) // LC
        {
            $sql="SELECT a.beneficiary_name as BENEFICIARY_NAME, a.export_lc_no as SC_LC_NO, a.lc_date as SC_LC_DATE, a.lc_value as SC_LC_VALUE, a.currency_name as CURRENCY_NAME, a.lien_bank as LIEN_BANK, a.buyer_name as BUYER_NAME, a.last_shipment_date as SHIPMENT_DATE, b.id as INVOICE_ID, b.exp_form_no as EXP_FORM_NO, b.invoice_no as INVOICE_NO, b.invoice_date as INVOICE_DATE, b.invoice_quantity as INVOICE_QUANTITY, b.invoice_value as INVOICE_VALUE, b.country_id as COUNTRY_ID, sum(c.net_invo_value) as BANK_VALUE, d.id as DOC_MST_ID,max(e.received_date) as REALIZATION_DATE, sum(f.document_currency) as DOCUMENT_CURRENCY
            from com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c,com_export_doc_submission_mst d
            left join com_export_proceed_realization e on d.id=e.invoice_bill_id and e.status_active=1 and e.is_deleted=0
            left join com_export_proceed_rlzn_dtls f on e.id=f.mst_id and f.type=0 and f.status_active=1 and f.is_deleted=0
            where a.id=$txt_lcsc_id and a.export_lc_no='$txt_lc_sc_no' and a.lc_year='$cbo_year' $search_cond and b.is_lc=1 and b.lc_sc_id=a.id and b.id=c.invoice_id and b.is_lc=c.is_lc and b.lc_sc_id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 
            and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  
            group by a.beneficiary_name, a.export_lc_no, a.lc_date, a.lc_value, a.currency_name, a.lien_bank, a.buyer_name, a.last_shipment_date, b.id, b.exp_form_no, b.invoice_no, b.invoice_date, b.invoice_quantity,b.invoice_value, b.country_id, d.id";
        }
        else if($lcsc_type==1) // SC
        {
            $sql="SELECT a.beneficiary_name as BENEFICIARY_NAME, a.contract_no as SC_LC_NO, a.contract_date as SC_LC_DATE, a.contract_value as SC_LC_VALUE, a.currency_name as CURRENCY_NAME, a.lien_bank as LIEN_BANK, a.buyer_name as BUYER_NAME, a.last_shipment_date as SHIPMENT_DATE, b.id as INVOICE_ID, b.exp_form_no as EXP_FORM_NO, b.invoice_no as INVOICE_NO, b.invoice_date as INVOICE_DATE, b.invoice_quantity as INVOICE_QUANTITY, b.invoice_value as INVOICE_VALUE, b.country_id as COUNTRY_ID, sum(c.net_invo_value) as BANK_VALUE, d.id as DOC_MST_ID, max(e.received_date) as REALIZATION_DATE, sum(f.document_currency) as DOCUMENT_CURRENCY
            from com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c,com_export_doc_submission_mst d
            left join com_export_proceed_realization e on d.id=e.invoice_bill_id and e.status_active=1 and e.is_deleted=0
            left join com_export_proceed_rlzn_dtls f on e.id=f.mst_id and f.type=0 and f.status_active=1 and f.is_deleted=0
            where a.id=$txt_lcsc_id and a.contract_no='$txt_lc_sc_no' and a.sc_year='$cbo_year' $search_cond and b.is_lc=2 and b.lc_sc_id=a.id and b.id=c.invoice_id and b.is_lc=c.is_lc and b.lc_sc_id=c.lc_sc_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
            group by a.beneficiary_name, a.contract_no, a.contract_date, a.contract_value, a.currency_name, a.lien_bank, a.buyer_name, a.last_shipment_date,b.id, b.exp_form_no, b.invoice_no, b.invoice_date, b.invoice_quantity, b.invoice_value, b.country_id, d.id";
        }        
        // echo $sql.'<br>';
        $total_inv_bank_val=array();
        $sql_data=sql_select($sql);
        foreach($sql_data as $row)
        {
            $total_incoice_val+=$row['INVOICE_VALUE'];
            $total_inv_bank_val[$row['DOC_MST_ID']]+=$row['BANK_VALUE'];
        }
        foreach($sql_data as $row)
        {
            $against_realization+=$row['BANK_VALUE']-(($row['BANK_VALUE']/$total_inv_bank_val[$row['DOC_MST_ID']]*1)*$row['DOCUMENT_CURRENCY']);
        }
        ?>
            <style>
                .wrd_brk{word-break: break-all;}
                .left{text-align: left;}
                .center{text-align: center;}
                .right{text-align: right;}
            </style>
            <div style="width:1100px;" id="scroll_body">
                <table width="1100" cellpadding="0" cellspacing="0" border="0" rules="all">
                    <tr>
                        <td  colspan="5" class="center"><strong><u style="font-size:16px;">ATTACHED SHEET</u></strong></td>
                    </tr>
                    <tr>
                        <td colspan="5" height="30"></td>
                    </tr>
                    <tr>
                        <td width="150">Name of the Unit</td>
                        <td width="300"><?=$company_arr[$cbo_company_name];?></td>
                        <td width="100"><?=$erc_no;?></td>
                        <td width="100">YEAR :</td>
                        <td >2020</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="10"></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="4">
                            <table width="350" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <tr>
                                    <td class="center" width="150"><strong>Sales Contract #</strong></td>
                                    <td class="center" width="100"><strong>Date</strong></td>
                                    <td class="center"><strong>Value</strong></td>
                                </tr>
                                <tr>
                                    <td class="center wrd_brk"><?=$sql_data[0]['SC_LC_NO'];?></td>
                                    <td class="center"><?=change_date_format($sql_data[0]['SC_LC_DATE']);?></td>
                                    <td class="right"><?=number_format($sql_data[0]['SC_LC_VALUE'],2);?>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td  colspan="5" height="10"></td>
                    </tr>
                    <tr>
                        <td >Buyer Name :</td>
                        <td colspan="4"><?=$buyer_name_arr[$sql_data[0]['BUYER_NAME']];?></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="4"><?=$buyer_add_arr[$sql_data[0]['BUYER_NAME']];?></td>
                    </tr>
                    <tr>
                        <td  colspan="5" height="10"></td>
                    </tr>
                    <tr>
                        <td valign="top">Local Bank Name & Address :</td>
                        <td colspan="4"><?=$bank_dtls_arr[$sql_data[0]['LIEN_BANK']]['BANK_NAME'].', '. $bank_dtls_arr[$sql_data[0]['LIEN_BANK']]['ADDRESS'];?></td>
                    </tr>
                    <tr>
                        <td  colspan="5" height="10"></td>
                    </tr>
                </table>
                <table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <td class="center" width="20"><strong>Sl.</strong></td>
                            <td class="center" width="100"><strong>Invoice No.</strong></td>
                            <td class="center" width="60"><strong>Invoice Date</strong></td>
                            <td class="center" width="60"><strong>Garmts Qty</strong></td>
                            <td class="center" width="100"><strong>Destination</strong></td>
                            <td class="center" width="60"><strong>Invoice Value</strong></td>
                            <td class="center" width="60"><strong>Shipment Date</strong></td>
                            <td class="center" width="100"><strong>EXP No.</strong></td>
                            <td class="center" width="80"><strong>Realized Value</strong></td>
                            <td class="center" width="80"><strong>Realized Date</strong></td>
                            <td class="center" width="50"><strong>Unit</strong></td>
                            <td class="center" width="50"><strong>Buyer</strong></td>
                            <td class="center" width="50"><strong>Bank</strong></td>
                            <td class="center" width="100"><strong>Contract</strong></td>
                            <td class="center" width="50"><strong>REF. NO.</strong></td>
                            <td class="center" ><strong>Bank Sub. Date</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                            $i=1;
                            foreach($sql_data as $row)
                            {
                                $realization_value=$against_realization*($row['INVOICE_VALUE']/$total_incoice_val);
                                $total_realization+=$realization_value;
                                ?>
                                    <tr>
                                        <td class="center"><?=$i;?></td>
                                        <td class="wrd_brk "><?echo $row['INVOICE_NO'];?></td>
                                        <td class="wrd_brk center"><?=change_date_format($row['INVOICE_DATE']);?></td>
                                        <td class="wrd_brk right"><?=$row['INVOICE_QUANTITY'];?></td>
                                        <td class="wrd_brk "><?=$country_arr[$row['COUNTRY_ID']];?></td>
                                        <td class="wrd_brk right"><?=number_format($row['INVOICE_VALUE'],2);?></td>
                                        <td class="wrd_brk center"><?=change_date_format($row['LAST_SHIPMENT_DATE'])?></td>
                                        <td class="wrd_brk"><?=$row['EXP_FORM_NO'];?></td>
                                        <td class="wrd_brk right"><?=number_format($realization_value,2);?></td>
                                        <td class="wrd_brk center"><?=change_date_format($row['REALIZATION_DATE']);?></td>
                                        <td class="wrd_brk"><?=$short_company_arr[$row['BENEFICIARY_NAME']];?></td>
                                        <td class="wrd_brk"><?=$short_buyer_name_arr[$row['BUYER_NAME']];?></td>
                                        <td class="wrd_brk"><?=$bank_dtls_arr[$row['LIEN_BANK']]['BANK_NAME'];?></td>
                                        <td class="wrd_brk"><?=$row['SC_LC_NO'];?></td>
                                        <td class="wrd_brk"></td>
                                        <td class="wrd_brk"></td>
                                    </tr>
                                <?
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        <?
    }


    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

disconnect($con);
?>
