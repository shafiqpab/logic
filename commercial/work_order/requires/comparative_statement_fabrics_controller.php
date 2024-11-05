<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$user_level=$_SESSION['logic_erp']['user_level'];

//========== start ========


if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=19 and report_id=147 and is_deleted=0 and status_active=1");
	
	//echo $print_report_format;  die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#generate_cs').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			
			if($id==739){echo "$('#generate_cs').show();\n";}
			
			
		}
	}
	exit();	
}

if ($action=="load_drop_down_buyer_brand")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'comparative_statement_fabrics_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'comparative_statement_fabrics_controller', this.value, 'load_drop_down_season', 'season_td')" );
	exit();	
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 80, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if($action == "load_drop_down_group")
{
	echo create_drop_down( "cbo_item_group", 130,"select item_name,id from lib_item_group where item_category in ($data) and status_active= 1 and is_deleted= 0 order by item_name","id,item_name", 1, "-- Select --", $selected, "" );
	// die;
}

//========== start CS Number ========
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
		function fn_show()
		{
			/*if(form_validation('cs_no','CS No')==false && form_validation('txt_date_from*txt_date_to','CS Date Range')==false )
			{
				return;
			}*/
			var cs_no=$("#cs_no").val();
			var demand_no=$("#demand_no").val();
			if(cs_no=="" && demand_no=="")
			{
				if(form_validation('txt_date_from*txt_date_to','CS Date Range')==false )
				{
					return;
				}
			}
			show_list_view ( document.getElementById('cs_no').value+'_'+document.getElementById('demand_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_company_id').value, 'create_cs_search_list_view', 'search_div', 'comparative_statement_fabrics_controller', 'setFilterGrid(\'search_div\',-1)');
			setFilterGrid('tbl_list_search',-1);
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
					<th>Company Name</th>
                    <th>CS No</th>
                    <th>Style No</th>
                    <th colspan="2">CS Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="id_field" id="id_field" value="" /></th>
                </tr>        
            </thead>
            <tr class="general">
				<td>
					<?
					$selected_company="";
					if ($cbo_company_id != "") $selected_company=$cbo_company_id;
					echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select --", $selected_company, "" ); 
					?>
				</td>
                <td> 
                    <input type="hidden" id="selected_id">
					<input name="cs_no" id="cs_no" class="text_boxes" style="width:120px">
                </td>
                <td> 
					<input name="demand_no" id="demand_no" class="text_boxes" style="width:120px">
                </td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show()" style="width:100px;" />
                </td>
        	</tr>
            <tr>
                <td align="center" colspan="5"><? echo load_month_buttons(1); ?></td>
            </tr>
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

if($action=="create_cs_search_list_view")
{
	//echo $data;die;
	$cs_num=$demand_num="";$date_cond ="";$year_cond="";$company_cond="";
	list($cs_no,$demand_no,$cs_start_date,$cs_end_date,$cbo_year_selection,$company_id) = explode('_', $data);
	if ($cs_no!='') { $cs_num=" and sys_number like '%$cs_no'"; }
	if ($company_id>0) { $company_cond=" and company_name='$company_id'"; }
	if ($demand_no!='') { $demand_num=" and req_item_no like '%$demand_no%'"; }
	if ($cs_start_date != '' && $cs_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and cs_date '" . change_date_format($cs_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($cs_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and cs_date between '" . change_date_format($cs_start_date, '', '', 1) . "' and '" . change_date_format($cs_end_date, '', '', 1) . "'";
		}

    }
	
	if($cbo_year_selection>0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(cs_date) =$cbo_year_selection ";
		}
		else
		{	
			$year_cond=" and to_char(cs_date,'YYYY') =$cbo_year_selection ";
		}
	}

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$sql= "select id, company_name, sys_number, sys_number_prefix_num, cs_date, req_item_no, company_id from req_comparative_mst where status_active=1 and is_deleted=0 and entry_form=512 $cs_num $demand_num $date_cond $year_cond $company_cond order by id DESC";
	//echo $sql;//die;
	$sql_result= sql_select($sql);
	
	?>
	<table width="800" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
	<thead>
		<th width="40">SL</th>
		<th width="100">Company Name</th>
		<th width="80">CS No</th>
		<th width="50">CS Suffix</th>
		<th width="80">CS Date</th>
		<th width="150">Style/Item No</th>
		<th width="150">Applicable Company</th>
	</thead>
	</table>
	<table width="800" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
		<tbody>
		<div style="width:820px; overflow-y:scroll; max-height:280px">
		<?			
            $i = 1;
            foreach($sql_result as $row)
            {
                if ($i%2==0) {$bgcolor="#FFFFFF";} else{ $bgcolor="#E9F3FF";}
                $company_mult=$row[csf('company_id')];
                $company_mult_arr=explode(',',$company_mult);
                $com_short_nam='';
                foreach($company_mult_arr as $com){
                    if($com_short_nam !='')	{$com_short_nam .= ", ".$company_arr[$com];}else{$com_short_nam =$company_arr[$com];}
                }
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>')" >  
                    <td align="center" width="40"><? echo $i; ?></td>
					<td align="center" width="100"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
                    <td align="center" width="80"><p><? echo $row[csf('sys_number')]; ?></p></td>
                    <td align="center"  width="50"><p><? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td align="center" width="80"><p><? echo change_date_format($row[csf('cs_date')]); ?></td>
                    <td align="center" width="150"><p><? echo $row[csf('req_item_no')]; ?></p></td>
                    <td align="center" width="150"><p> <?  echo $com_short_nam; ?></p></td>

                </tr>
                <?
                $i++;
            }
            ?>
        </div>
		</tbody>
	</table>
	<?
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$data_array=sql_select("select id, company_name, sys_number, sys_number_prefix, sys_number_prefix_num, basis_id, req_item_no,req_item_mst_id,req_item_dtls_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, ready_to_approved, approved, company_id, comments, style_specific_cs, job_id, style_ref_no from req_comparative_mst where id='$data' and is_deleted=0 and status_active=1");
	$supp_mult_arr=array();
	$basis='';
	foreach ($data_array as $row)
	{ 
		$supp_mult=$row[csf('supp_id')];
		$basis_id=$row[csf('basis_id')];
		$supp_mult_arr=explode(',',$supp_mult);
		$supp_nam='';
		foreach($supp_mult_arr as $supp){
			if($supp_nam !='')	{$supp_nam .= ", ".$supplier_arr[$supp];}else{$supp_nam =$supplier_arr[$supp];}
		}
		echo "document.getElementById('cbo_company_id').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('txt_system_id').value = '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('cbo_basis_name').value = '".$row[csf("basis_id")]."';\n";  
		echo "document.getElementById('txt_demand').value = '".$row[csf("req_item_no")]."';\n";  
		echo "document.getElementById('txt_requisition_mst').value = '".$row[csf("req_item_mst_id")]."';\n";  
		echo "document.getElementById('txt_requisition_dtls').value = '".$row[csf("req_item_dtls_id")]."';\n";  
		echo "document.getElementById('prev_req_dtls_id').value = '".$row[csf("req_item_dtls_id")]."';\n";  
		echo "document.getElementById('txt_cs_date').value = '".change_date_format($row[csf("cs_date")])."';\n";
		echo "document.getElementById('supplier_id').value = '".$row[csf("supp_id")]."';\n";  
		echo "document.getElementById('cbo_currency_name').value = '".$row[csf("currency_id")]."';\n"; 
		echo "document.getElementById('txt_validity_date').value = '".change_date_format($row[csf("cs_valid_date")])."';\n"; 		
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "set_multiselect('cbo_company_name','0','1','".$row[csf('company_id')]."','0');\n";   
		echo "document.getElementById('txt_comments').value = '".$row[csf("comments")]."';\n";
		echo "document.getElementById('hidd_job_id').value = '".$row[csf("job_id")]."';\n";
		echo "document.getElementById('txt_style_no').value = '".$row[csf("style_ref_no")]."';\n"; 
		if ($row[csf("style_specific_cs")]==1) {
			echo "$('#txt_style_check').prop('checked', true);\n";
			echo "$('#txt_style_no').attr('disabled',false);\n";
		}

		if ($row[csf("approved")] == 1) echo "$('#approved').text('Approved');\n";
		else if($row[csf("approved")] == 3) echo "$('#approved').text('Partial Approved');\n";
		else echo "$('#approved').text('');\n";
		
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n"; 

		echo "document.getElementById('txt_supplier_name').value = '".$supp_nam."';\n"; 
	}

	/* $sql_rate="select a.id, a.effective_from as APPROVED_DATE, a.supplier_id as SUPPLIER_ID, a.is_supp_comp as IS_SUPP_COMP, a.rate as RATE, a.item_category_id as ITEM_CATEGORY_ID, a.item_group_id as ITEM_GROUP_ID, a.prod_id as PROD_ID, b.detarmination_id as DETARMINATION_ID, b.item_description as ITEM_DESCRIPTION, b.brand_supplier as BRAND_SUPPLIER, b.weight as WEIGHT, b.unit_of_measure as UOM, b.color as COLOR, b.gmts_size as GMTS_SIZE, b.item_color as ITEM_COLOR, b.item_size as ITEM_SIZE from lib_supplier_wise_rate a, product_details_master b where a.prod_id=b.id and a.entry_form=512 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
	$sql_rate_res=sql_select($sql_rate);
	$product_id_array=array();
	foreach ($sql_rate_res as $row) {
		$key=$row["ITEM_CATEGORY_ID"].'**'.$row["DETARMINATION_ID"].'**'.$row["WEIGHT"].'**'.$row["UOM"];
		$product_id_array[$key][$row["SUPPLIER_ID"]][$row["IS_SUPP_COMP"]]['RATE']=$row["RATE"];
		//$product_id_array[$key][$row["SUPPLIER_ID"]][$row["IS_SUPP_COMP"]]['APPROVED_DATE']=$row["APPROVED_DATE"];
	} */
	//echo '<pre>';print_r($product_id_array);die;
	
	$dtls_sql="select id as ID, 3 as ITEM_CATEGORY_ID, job_id as JOB_ID, detarmination_id as DETARMINATION_ID, fabric_cost_dtls_id as FABRIC_COST_DTLS_ID, item_description as ITEM_DESCRIPTION, mill_reff as MILL_REFF, rd_no as RD_NO, weight as WEIGHT, cutable_width as CUTABLE_WIDTH, uom as UOM, req_qty as REQ_QTY, req_rate as REQ_RATE, req_amount as REQ_AMOUNT, weight as WEIGHT, all_company_quoted_price as ALL_COMPANY_QUOTED_PRICE, all_company_neg_price as ALL_COMPANY_NEG_PRICE, all_company_con_price as ALL_COMPANY_CON_PRICE, 0 as COLOR, 0 as GMTS_SIZE, 0 as ITEM_COLOR, null as ITEM_SIZE
	from req_comparative_dtls
	where mst_id='$data' and is_deleted=0 and status_active=1
	order by ID";
	$dtls_sql_result=sql_select($dtls_sql);
	
	$supp_dtls_arr=$com_dtls_arr=array();
	$supp_dtls_arr=sql_select("select id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, item_description as ITEM_DESCRIPTION, uom as UOM, quoted_price as QUOTED_PRICE, neg_price as NEG_PRICE, con_price as CON_PRICE, cutable_width as CUTABLE_WIDTH, pay_term as PAY_TERM, tenor as TENOR, ship_mode as SHIP_MODE, source as SOURCE, last_approval_rate as LAST_APPROVAL_RATE, is_recommend as IS_RECOMMEND, origin_id as ORIGIN_ID,lc_type from req_comparative_supp_dtls where mst_id='$data' and is_deleted=0 and status_active=1 and supp_type=1 order by DTLS_ID, ID");

	$supplier_check_arr=array();
	$supp_origin_arr=array();
	foreach($supp_dtls_arr as $row){
		$supplier_check_arr[$row["SUPP_ID"]]=$row[csf("IS_RECOMMEND")];
		$supp_origin_arr[$row["SUPP_ID"]]=$row["ORIGIN_ID"];
	}

	$com_dtls_arr=sql_select("select id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, item_description as ITEM_DESCRIPTION, uom as UOM, quoted_price as QUOTED_PRICE, neg_price as NEG_PRICE, con_price as CON_PRICE, cutable_width as CUTABLE_WIDTH, pay_term as PAY_TERM, tenor as TENOR, ship_mode as SHIP_MODE, source as SOURCE, last_approval_rate as LAST_APPROVAL_RATE, is_recommend as IS_RECOMMEND, origin_id as ORIGIN_ID from req_comparative_supp_dtls where mst_id='$data' and is_deleted=0 and status_active=1 and supp_type=2 order by DTLS_ID, ID");
	$company_check_arr=array();
	$com_origin_arr=array();
	foreach($com_dtls_arr as $row){
		$company_check_arr[$row["SUPP_ID"]]=$row[csf("IS_RECOMMEND")];
		$com_origin_arr[$row["SUPP_ID"]]=$row["ORIGIN_ID"];
	}

	$supplier_count=count($supp_mult_arr);
	$is_company=$data_array[0][csf("company_id")];
	$company_all=explode(',',$data_array[0][csf("company_id")]);
	$company_count=count($company_all);
	if($is_company!=''){$company_width=$company_count*720;}else{$company_width=0;}
	//echo count($company_all).'system';
	//echo count($supp_mult_arr);
	$tbl_width=860+($supplier_count*720)+$company_width;

	$data_tbl.='<table width="'.$tbl_width.'" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all"  id="tbl_details">';
	$data_tbl.='<thead><tr>';
	$data_tbl.='<th rowspan="2" width="40">SL</th>';
	$data_tbl.='<th rowspan="2" width="80">Item Category</th>';
	$data_tbl.='<th rowspan="2" width="180">Items Description</th>';
	$data_tbl.='<th rowspan="2" width="70">Mill Reff.</th>';
	$data_tbl.='<th rowspan="2" width="70">RD No/ Byr</th>';
	$data_tbl.='<th rowspan="2" width="80">WEIGHT (Before Wash)</th>';
	$data_tbl.='<th rowspan="2" width="50">UOM</th>';
	$data_tbl.='<th rowspan="2" width="50">Cutable Width</th>';
	$data_tbl.='<th rowspan="2" width="80">Req. Qty.</th>';
	$data_tbl.='<th rowspan="2" width="80">Costing Price</th>';
	$data_tbl.='<th rowspan="2" width="80">TTL Amount</th>';
	$sql="select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name";
	if($is_company!='')
	{
		foreach($company_all as $comp)
		{
			if ($company_check_arr[$comp]==1) $checked='checked="checked"';
			else $checked='';

			$data_tbl.='<th colspan="3" width="240">Origin&nbsp;'.create_drop_down("companyOrigin_".$comp, 100, $sql, "id,country_name", 1, "-- Select --", $com_origin_arr[$comp], "", "").'</th>';
			$data_tbl.='<th colspan="6" width="480">'.$company_arr[$comp].'&nbsp;&nbsp;<input type="checkbox" id="txt_company_check_'.$comp.'" name="txt_company_check_'.$comp.'"'.$checked.' onchange="fn_price_check(1,'.$comp.')"/></th>';
		}
	}
	foreach($supp_mult_arr as $supp)
	{
		if ($supplier_check_arr[$supp]==1) $checked='checked="checked"';
		else $checked='';
		$data_tbl.='<th colspan="3" width="240">Origin&nbsp;'.create_drop_down("origin_".$supp, 100, $sql, "id,country_name", 1, "-- Select --", $supp_origin_arr[$supp], "", "").'</th>';
		$data_tbl.='<th colspan="6" width="480">'.$supplier_arr[$supp].'&nbsp;&nbsp;<input type="checkbox" id="txt_supplier_check_'.$supp.'" name="txt_supplier_check[]"'.$checked.' onchange="fn_price_check(2,'.$supp.')"/></th>';
	}
	$data_tbl.='</tr><tr>';
	if($is_company!='')
	{
		$m=1;
		foreach($company_all as $row)
		{
			$data_tbl.='<th width="80">Quoted Price</th>';
			$data_tbl.='<th width="80">Last Price</th>';
			$data_tbl.='<th width="80">Neg. Price</th>';
			$data_tbl.='<th width="80">Total Value</th>';

			$data_tbl.='<th width="80">Cut Width&nbsp;';
			if ($m==1) { $data_tbl.='<input type="checkbox" id="txt_cutablewidth_check" name="" onchange="fn_cutablewidth_check()"/>'; }$data_tbl.='</th>';
			$data_tbl.='<th width="80">Pay Term&nbsp;';
			//$data_tbl.='<th width="80">L/C Type&nbsp;';
			if ($m==1) { $data_tbl.='<input type="checkbox" id="txt_payterm_check" name="" onchange="fn_payterm_check()"/>'; }
			$data_tbl.='</th>';

			$data_tbl.='<th width="80">Tenor</th>';
			$data_tbl.='<th width="80">Ship Mode</th>';
			$data_tbl.='<th width="80">Source</th>';
			$m++;
		}
	}
	$n=1;
	foreach($supp_mult_arr as $row)
	{
		$data_tbl.='<th width="80">Quoted Price</th>';
		$data_tbl.='<th width="80">Last Price</th>';
		$data_tbl.='<th width="80">Neg. Price</th>';
		$data_tbl.='<th width="80">Total Value</th>';
		$data_tbl.='<th width="80">Cut Width&nbsp;';

		if ($company_count<1 && $n==1) { $data_tbl.='<input type="checkbox" id="txt_cutablewidth_check" name="" onchange="fn_cutablewidth_check()"/>'; }$data_tbl.='</th>';
		$data_tbl.='<th width="80">Pay Term&nbsp;';
		$data_tbl.='<th width="80">L/C Type&nbsp;';
		if ($company_count<1 && $n==1) { $data_tbl.='<input type="checkbox" id="txt_payterm_check" name="" onchange="fn_payterm_check()"/>'; }
		$data_tbl.='</th>';

		$data_tbl.='<th width="80">Tenor</th>';
		$data_tbl.='<th width="80">Ship Mode</th>';
		$data_tbl.='<th width="80">Source</th>';
		$n++;
	}

	$data_tbl.='</tr></thead><tbody>';
	$i=1;	
	$lc_type_arr= array( 4 => "TT/Pay Order", 5 => "FTT", 6 => "FDD/RTGS");

	foreach($dtls_sql_result as $row)
	{		
		$key=$row["ITEM_CATEGORY_ID"].'**'.$row["DETARMINATION_ID"].'**'.$row["WEIGHT"].'**'.$row["UOM"];
		$data_tbl.='<tr class="general" id="'.$i.'">';
		$data_tbl.='<td align="center">'.$i.' <input type="hidden" name="" id="txtprod_'.$i.'" value="" ><input type="hidden" name="" id="txtdeterminationid_'.$i.'" value="'.$row['DETARMINATION_ID'].'" ><input type="hidden" name="" id="txtjobid_'.$i.'" value="'.$row['JOB_ID'].'" ><input type="hidden" name="" id="txtfabriccostdtlsid_'.$i.'" value="'.$row['FABRIC_COST_DTLS_ID'].'" ></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:75px" name="txtItemCategory_'.$i.'" id="txtItemCategory_'.$i.'" class="text_boxes" title="'.$row['ITEM_CATEGORY_ID'].'" value="'.$item_category[$row['ITEM_CATEGORY_ID']].'" readonly></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:170px" name="txtItemDescrip_'.$i.'" id="txtItemDescrip_'.$i.'" class="text_boxes" value="'.$row['ITEM_DESCRIPTION'].'" readonly></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:70px" name="txtMillReff_'.$i.'" id="txtMillReff_'.$i.'" class="text_boxes" value="'.$row['MILL_REFF'].'" readonly></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:70px" name="txtRdNo_'.$i.'" id="txtRdNo_'.$i.'" class="text_boxes" value="'.$row['RD_NO'].'" readonly></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:70px" name="txtWeight_'.$i.'" id="txtWeight_'.$i.'" class="text_boxes_numeric" value="'.$row['WEIGHT'].'" readonly></td>';		
		$data_tbl.='<td align="center"><input type="text" style="width:50px" name="txtUom_'.$i.'" id="txtUom_'.$i.'" class="text_boxes" title="'.$row['UOM'].'" value="'.$unit_of_measurement[$row['UOM']].'" readonly></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:50px" name="txtCutableWidth_'.$i.'" id="txtCutableWidth_'.$i.'" class="text_boxes_numeric" value="'.$row['CUTABLE_WIDTH'].'" readonly></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:70px" name="txtQty_'.$i.'" id="txtQty_'.$i.'" class="text_boxes_numeric" title="'.$row['REQ_QTY'].'" value="'.number_format($row['REQ_QTY'],4,'.','').'" readonly></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:70px" name="txtRate_'.$i.'" id="txtRate_'.$i.'" class="text_boxes_numeric" title="'.$row['REQ_RATE'].'" value="'.number_format($row['REQ_RATE'],2,'.','').'" readonly></td>';
		$data_tbl.='<td align="center"><input type="text" style="width:70px" name="txtAmt_'.$i.'" id="txtAmt_'.$i.'" class="text_boxes_numeric" title="'.number_format($row['REQ_AMOUNT'],8,'.','').'" value="'.number_format($row['REQ_AMOUNT'],4,'.','').'" readonly></td>';
		$row_id=$row['ID'];
		if($is_company!='')
		{
			foreach($company_all as $com_nam)
			{
				foreach($com_dtls_arr as $val)
				{
					$dtls_row_id=$val["DTLS_ID"];
					$com_id=$val["SUPP_ID"];
					if($row_id==$dtls_row_id)
					{
						if($com_nam==$com_id)
						{
							$com_neg_price=$product_id_array[$key][$com_id][2]['RATE'];
							if ($com_neg_price !="") $com_neg_price=$com_neg_price;
							else $com_neg_price=$val['NEG_PRICE'];

							$total_value=number_format(($row['REQ_QTY']*$com_neg_price),4,'.','');
							if ($total_value==0) $total_value="";
							else $total_value=$total_value;
							//echo $val[csf('pay_term')].'**';
							$dtls_parameter="\'".$i.'**'.$com_id."\'";

							$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyQuoted_'.$i.'_'.$com_id.'" value="'.$val["QUOTED_PRICE"].'" class="text_boxes_numeric"></td>';
							$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyCon_'.$i.'_'.$com_id.'" placeholder="Display" value="'.$val['CON_PRICE'].'" readonly class="text_boxes_numeric"></td>';
							$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyNeg_'.$i.'_'.$com_id.'" value="'.$com_neg_price.'" class="text_boxes_numeric" onKeyUp="chkNegPrice(this.value,1,'.$dtls_parameter.')"></td>';
							$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyTotalValue_'.$i.'_'.$com_id.'" value="'.$total_value.'" class="text_boxes_numeric" readonly></td>';
							$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyCutableWidth_'.$i.'_'.$com_id.'" value="'.$val['CUTABLE_WIDTH'].'" class="text_boxes_numeric"></td>';
							$data_tbl.='<td align="center" >'.create_drop_down("txtCompanyPayTerm_".$i.'_'.$com_id, 80, $pay_term, "", 1, "-- Select --", $val['PAY_TERM'], "", "").'</td>';
							
							//$data_tbl.='<td align="center" >'.create_drop_down("txtCompanyLCType_".$i.'_'.$com_id, 80, $lc_type_arr, "", 1, "-- Select --", 0, "", 0, '').'</td>';
							$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyTenor_'.$i.'_'.$com_id.'" value="'.$val['TENOR'].'" class="text_boxes_numeric"></td>';
							$data_tbl.='<td align="center" >'.create_drop_down("companyShipMode_".$i.'_'.$com_id, 80, $shipment_mode, "", 1, "-- Select --", $val['SHIP_MODE'], "", "").'</td>';
							$data_tbl.='<td align="center" >'.create_drop_down("companySource_".$i.'_'.$com_id, 80, $source, "", 1, "-- Select --", $val['SOURCE'], "", "").'</td>';
						}
					}
				}
			}
		}
		//echo '<pre>';print_r($supp_dtls_arr);
		foreach($supp_mult_arr as $supp)
		{
			foreach($supp_dtls_arr as $val)
			{
				$dtls_row_id=$val["DTLS_ID"];
				$supp_id=$val["SUPP_ID"];
				if($row_id==$dtls_row_id)
				{					
					if($supp==$supp_id)
					{
						$supp_neg_price=$val['LAST_APPROVAL_RATE'];
						if ($supp_neg_price != 0) $supp_neg_price=$supp_neg_price;
						else $supp_neg_price=$val['NEG_PRICE'];

						$total_value=number_format(($row['REQ_QTY']*$supp_neg_price),4,'.','');
						if ($total_value==0) $total_value="";
						else $total_value=$total_value;

						$dtls_parameter="\'".$i.'**'.$supp_id."\'";

						$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtquoted_'.$i.'_'.$supp_id.'" value="'.$val["QUOTED_PRICE"].'" class="text_boxes_numeric"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtcon_'.$i.'_'.$supp_id.'" placeholder="Display" value="'.$val['CON_PRICE'].'" readonly class="text_boxes_numeric"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtneg_'.$i.'_'.$supp_id.'" value="'.$supp_neg_price.'" class="text_boxes_numeric" onKeyUp="chkNegPrice(this.value,2,'.$dtls_parameter.')"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txttotalvalue_'.$i.'_'.$supp_id.'" value="'.$total_value.'" class="text_boxes_numeric" readonly></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtSuppCutableWidth_'.$i.'_'.$supp_id.'" value="'.$val['CUTABLE_WIDTH'].'" class="text_boxes_numeric"></td>';
						$data_tbl.='<td align="center" >'.create_drop_down("txtpayterm_".$i.'_'.$supp_id, 80, $pay_term, "", 1, "-- Select --", $val['PAY_TERM'] , "", "").'</td>';
						$data_tbl.='<td align="center" >'.create_drop_down("txtLCType_".$i.'_'.$supp_id, 80, $lc_type_arr, "", 1, "-- Select --", $val['LC_TYPE'] , "", "").'</td>'; 

						$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txttenor_'.$i.'_'.$supp_id.'" value="'.$val['TENOR'].'" class="text_boxes_numeric"></td>';
						$data_tbl.='<td align="center" >'.create_drop_down("cboShipMode_".$i.'_'.$supp_id, 80, $shipment_mode, "", 1, "-- Select --", $val['SHIP_MODE'] , "", "").'</td>';
						$data_tbl.='<td align="center" >'.create_drop_down("cboSource_".$i.'_'.$supp_id, 80, $source, "", 1, "-- Select --", $val['SOURCE'] , "", "").'</td>';
					}
				}
			}
		}
		$data_tbl.='</tr>';
		$i++;
	}	

	$data_tbl.='</tbody></table>';
	echo "document.getElementById('cs_tbl').innerHTML = '".$data_tbl."';\n"; 

	exit();
}
//========== End CS Number ========

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo "10**5=$operation=";die;

	if($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$mst_id=return_next_id("id", "req_comparative_mst", 1);
		
		if($db_type==0) $insert_date_con=" and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con=" and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";

		//$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'CSF', date("Y"), 5, "select sys_number_prefix,sys_number_prefix_num from req_comparative_mst where entry_form=512 and company_name=$cbo_company_id and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC", "sys_number_prefix", "sys_number_prefix_num" ));
		$new_sys_no=explode("*",return_mrr_number( '', '', '', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from req_comparative_mst where entry_form=512 $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));
		
		$field_array_mst="id, company_name, sys_number, sys_number_prefix, sys_number_prefix_num, entry_form, basis_id, req_item_no, req_item_mst_id, req_item_dtls_id, cs_date, supp_id, currency_id, cs_valid_date, ready_to_approved, approved, company_id, comments, inserted_by, insert_date, status_active, is_deleted, style_specific_cs, job_id, style_ref_no";

		$txt_requisition_mst=implode(",",array_unique(explode(",",chop(str_replace("'","",$txt_requisition_mst),","))));
		$data_array_mst="(".$mst_id.",'".str_replace("'","",$cbo_company_id)."','".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',512,".$cbo_basis_name.",".$txt_demand.",'".$txt_requisition_mst."',".$txt_requisition_dtls.",".$txt_cs_date.",".$supplier_id.",".$cbo_currency_name.",".$txt_validity_date.",".$cbo_ready_to_approved.",0,".$cbo_company_name.",".$txt_comments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$txt_style_check.",".$hidd_job_id.",".$txt_style_no.")";
		//echo "10**INSERT INTO req_comparative_mst (".$field_array_mst.") VALUES ".$data_array_mst; 
		// die;

		// Demand Basis Item Check
		if (str_replace("'", "", $cbo_basis_name)==1)
		{
			$sql_item_check=sql_select("select b.item_category_id, b.item_group_id, b.item_description, b.brand_supplier, b.uom from req_comparative_mst a, req_comparative_dtls where a.id=b.mst_id and a.entry_form=512 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$item_check_arr=array();
			foreach ($sql_item_check as $row) {
				$item_check_arr[]=$row[csf("item_category_id")].'**'.$row[csf("item_group_id")].'**'.$row[csf("item_description")].'**'.$row[csf("brand_supplier")].'**'.$row[csf("uom")];
			}

			for($i=1; $i<=$row_num; $i++)
			{
				$txt_item_ategory="txtItemCategory_".$i;
				$txt_group ="txtGroup_".$i;			
				$txt_item_descrip="txtItemDescrip_".$i;
				$txt_brand_supplier="txtBrandSupplier_".$i;
				$txt_uom="txtUom_".$i;
				$item=$$txt_item_ategory.'**'.$$txt_group.'**'.$$txt_item_descrip.'**'.$$txt_brand_supplier.'**'.$$txt_uom;
				if (in_array($item, $item_check_arr)){
					echo "20**This Item Used in another CS"; disconnect($con); die;
				}
			}
		}
		

		$field_array_dtls="id, mst_id, item_category_id, detarmination_id, item_description, mill_reff, rd_no, weight, cutable_width, uom, req_qty, req_rate, req_amount, supp_data, com_data, job_id, fabric_cost_dtls_id, inserted_by, insert_date, is_deleted, status_active";

		$field_array_supp_dtls="id, mst_id, dtls_id, supp_type, supp_id, item_description, uom, quoted_price, neg_price, con_price, cutable_width, last_approval_rate,  pay_term, tenor, ship_mode, source, is_recommend, origin_id, approved, inserted_by, insert_date, is_deleted, status_active,lc_type";
 
		$id_dtls=return_next_id("id", "req_comparative_dtls", 1);
		$id_supp_dtls=return_next_id("id", "req_comparative_supp_dtls", 1);
		//echo "10**$cbo_company_name";die;
		$col_num_arr = explode(',',str_replace("'","",$supplier_id));
		$company_num_arr = explode(',',str_replace("'","",$cbo_company_name));
		$com_data='';
		$data_array_dtls='';
		$data_array_supp_dtls='';

		for($i=1; $i<=$row_num; $i++)
		{
			$supp_data='';
			$com_data='';
			$txtItemCategory="txtItemCategory_".$i;
			$txtdeterminationid="txtdeterminationid_".$i;
			$txtfabriccostdtlsid="txtfabriccostdtlsid_".$i;
			$txtjobid="txtjobid_".$i;
			$txtItemDescrip="txtItemDescrip_".$i;
			$txtMillReff="txtMillReff_".$i;
			$txtRdNo="txtRdNo_".$i;
			$txtWeight ="txtWeight_".$i;
			$txtCutableWidth="txtCutableWidth_".$i;	
			$txtUom="txtUom_".$i;			
			$txtQty="txtQty_".$i;
			$txtRate="txtRate_".$i;
			$txtAmt="txtAmt_".$i;				
		
			for($m=0; $m<$col_num; $m++)
			{
				$mm=$col_num_arr[$m];				
				$txtsuppier= "txtsuppier_".$i."_".$mm;
				$txtquoted="txtquoted_".$i."_".$mm;
				$txtneg="txtneg_".$i."_".$mm;
				$txtcon="txtcon_".$i."_".$mm;
				$txtSuppCutableWidth="txtSuppCutableWidth_".$i."_".$mm;
				$txtpayterm="txtpayterm_".$i."_".$mm;  
				$txtLCType="txtLCType_".$i."_".$mm; 
				$txttenor="txttenor_".$i."_".$mm;				
				$cboShipMode="cboShipMode_".$i."_".$mm;				
				$cboSource="cboSource_".$i."_".$mm;
				$txt_supplier_check="txt_supplier_check_".$mm;
				$origin="origin_".$mm;
				
				//echo "10**".$txt_supplier_check;die;
				
				if($supp_data!=''){
					$supp_data.= "*".$$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtSuppCutableWidth."_".$$txtpayterm."_".$$txttenor."_".$$cboShipMode."_".$$cboSource;
				}else{
					$supp_data = $$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtSuppCutableWidth."_".$$txtpayterm."_".$$txttenor."_".$$cboShipMode."_".$$cboSource;
				}
				
				if ($data_array_supp_dtls!='') {$data_array_supp_dtls .=",";}
				$data_array_supp_dtls .="(".$id_supp_dtls.",".$mst_id.",".$id_dtls.",1,'".$$txtsuppier."','".$$txtItemDescrip."','".$$txtUom."','".$$txtquoted."','".$$txtneg."','".$$txtcon."','".$$txtSuppCutableWidth."','".$$txtneg."','".$$txtpayterm."','".$$txttenor."','".$$cboShipMode."','".$$cboSource."','".$$txt_supplier_check."','".$$origin."',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1,'".$$txtLCType."')";
				$id_supp_dtls++;
			}

			for($m=0; $m<$company_num; $m++)
			{
				$mm=$company_num_arr[$m];
				$txtCompany= "txtCompany_".$i."_".$mm;
				$txtCompanyQuoted="txtCompanyQuoted_".$i."_".$mm;
				$txtCompanyNeg="txtCompanyNeg_".$i."_".$mm;
				$txtCompanyCon="txtCompanyCon_".$i."_".$mm;
				$txtCompanyCutableWidth="txtCompanyCutableWidth_".$i."_".$mm;
				$txtCompanyPayTerm="txtCompanyPayTerm_".$i."_".$mm;
				 
				$txtCompanyTenor="txtCompanyTenor_".$i."_".$mm;				
				$companyShipMode="companyShipMode_".$i."_".$mm;				
				$companySource="companySource_".$i."_".$mm;
				$txt_company_check="txt_company_check_".$mm;
				$companyOrigin="companyOrigin_".$mm;
				
				if($com_data!=''){
					$com_data.= "*".$$txtCompany."_".$$txtCompanyQuoted."_".$$txtCompanyNeg."_".$$txtCompanyCon."_".$$txtCompanyCutableWidth."_".$$txtCompanyPayTerm."_".$$txtCompanyTenor."_".$$companyShipMode."_".$$companySource;
				}else{
					$com_data = $$txtCompany."_".$$txtCompanyQuoted."_".$$txtCompanyNeg."_".$$txtCompanyCon."_".$$txtCompanyCutableWidth."_".$$txtCompanyPayTerm."_".$$txtCompanyTenor."_".$$companyShipMode."_".$$companySource;
				}
				
				if ($data_array_supp_dtls!='') {$data_array_supp_dtls .=",";}
				$data_array_supp_dtls .="(".$id_supp_dtls.",".$mst_id.",".$id_dtls.",2,'".$$txtCompany."','".$$txtItemDescrip."','".$$txtUom."','".$$txtCompanyQuoted."','".$$txtCompanyNeg."','".$$txtCompanyCon."','".$$txtCompanyCutableWidth."','".$$txtCompanyNeg."','".$$txtCompanyPayTerm."','".$$txtCompanyTenor."','".$$companyShipMode."','".$$companySource."','".$$txt_company_check."','".$$companyOrigin."',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				$id_supp_dtls++;
			}

			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			
			$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$$txtItemCategory."','".$$txtdeterminationid."','".$$txtItemDescrip."','".$$txtMillReff."','".$$txtRdNo."','".$$txtWeight."','".$$txtCutableWidth."','".$$txtUom."','".$$txtQty."','".$$txtRate."','".$$txtAmt."','".$supp_data."','".$com_data."','".$$txtjobid."','".$$txtfabriccostdtlsid."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";


			$id_dtls++;
		}
		//echo "</br>100**INSERT INTO req_comparative_mst (".$field_array_mst.") VALUES ".$data_array_mst; die;
		//echo "1000**INSERT INTO req_comparative_supp_dtls (".$field_array_supp_dtls.") VALUES ".$data_array_supp_dtls; die;
		
		$rID=sql_insert("req_comparative_mst",$field_array_mst,$data_array_mst,0);
		$rID1=sql_insert("req_comparative_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID2=sql_insert("req_comparative_supp_dtls",$field_array_supp_dtls,$data_array_supp_dtls,0);
		//echo '10**'.$rID.'**'.$rID1.'**'.$rID2;oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".str_replace("'","",$txt_requisition_dtls);
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
				echo "0**".$new_sys_no[0]."**".$mst_id."**".str_replace("'","",$txt_requisition_dtls);
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
	elseif($operation==1) // Update Here----------------------------------------------------------
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$cs_approved=return_field_value("approved","req_comparative_mst","id=$update_id","approved");
		if($cs_approved==1 || $cs_approved==3)
		{
			echo "11**CS Approved, Update Not Allow";disconnect($con);oci_rollback($con);die;
		}

		// Demand Basis Item Check
		if (str_replace("'", "", $cbo_basis_name)==1)
		{
			$sql_item_check=sql_select("select b.item_category_id, b.item_group_id, b.item_description, b.brand_supplier, b.uom from req_comparative_mst a, req_comparative_dtls where a.id=b.mst_id and a.entry_form=512 and a.id<>$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$item_check_arr=array();
			foreach ($sql_item_check as $row) {
				$item_check_arr[]=$row[csf("item_category_id")].'**'.$row[csf("item_group_id")].'**'.$row[csf("item_description")].'**'.$row[csf("brand_supplier")].'**'.$row[csf("uom")];
			}

			for($i=1; $i<=$row_num; $i++)
			{
				$txt_item_ategory="txtItemCategory_".$i;
				$txt_group ="txtGroup_".$i;			
				$txt_item_descrip="txtItemDescrip_".$i;
				$txt_brand_supplier="txtBrandSupplier_".$i;
				$txt_uom="txtUom_".$i;
				$item=$$txt_item_ategory.'**'.$$txt_group.'**'.$$txt_item_descrip.'**'.$$txt_brand_supplier.'**'.$$txt_uom;
				if (in_array($item, $item_check_arr)){
					echo "20**This Item Used in another CS"; die;
				}
			}
		}

		$field_array_mst="basis_id*req_item_no*req_item_mst_id*req_item_dtls_id*cs_date*supp_id*currency_id*cs_valid_date* ready_to_approved*company_id*comments*style_specific_cs*job_id*style_ref_no*updated_by*update_date";
		$txt_requisition_mst=implode(",",array_unique(explode(",",chop(str_replace("'","",$txt_requisition_mst),","))));
		$data_array_mst="".$cbo_basis_name."*".$txt_demand."*'".$txt_requisition_mst."'*".$txt_requisition_dtls."*".$txt_cs_date."*".$supplier_id."*".$cbo_currency_name."*".$txt_validity_date."*".$cbo_ready_to_approved."*".$cbo_company_name."*".$txt_comments."*".$txt_style_check."*".$hidd_job_id."*".$txt_style_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$cbo_company_name=str_replace("'","",$cbo_company_name);

		$field_array_dtls="id, mst_id, item_category_id, detarmination_id, item_description, mill_reff, rd_no, weight, cutable_width, uom, req_qty, req_rate, req_amount, supp_data, com_data, job_id, fabric_cost_dtls_id, inserted_by, insert_date, is_deleted, status_active";

		$field_array_supp_dtls="id, mst_id, dtls_id, supp_type, supp_id, item_description, uom, quoted_price, neg_price, con_price, cutable_width, last_approval_rate, pay_term, tenor, ship_mode, source, is_recommend, origin_id, inserted_by, insert_date, is_deleted, status_active,lc_type";

		$id_dtls=return_next_id("id", "req_comparative_dtls", 1);
		$id_supp_dtls=return_next_id("id", "req_comparative_supp_dtls", 1);
		$col_num_arr = explode(',',$supplier_id);
		$company_num_arr = explode(',',$cbo_company_name);
		$com_data='';
		$data_array_dtls='';
		$data_array_supp_dtls='';
		$mst_id=str_replace("'","",$update_id);
		for($i=1; $i<=$row_num; $i++)
		{
			$supp_data='';
			$com_data='';
			$txtItemCategory="txtItemCategory_".$i;
			$txtdeterminationid="txtdeterminationid_".$i;
			$txtjobid="txtjobid_".$i;
			$txtfabriccostdtlsid="txtfabriccostdtlsid_".$i;
			$txtItemDescrip="txtItemDescrip_".$i;
			$txtMillReff="txtMillReff_".$i;
			$txtRdNo="txtRdNo_".$i;
			$txtWeight="txtWeight_".$i;
			$txtCutableWidth="txtCutableWidth_".$i;
			$txtUom="txtUom_".$i;
			
			$txtQty="txtQty_".$i;
			$txtRate="txtRate_".$i;
			$txtAmt="txtAmt_".$i;

			$txtAllCompanyQuoted="txtAllCompanyQuoted_".$i;
			$txtAllCompanyNeg="txtAllCompanyNeg_".$i;
			$txtAllCompanyCon="txtAllCompanyCon_".$i;
			
			for($m=0; $m<$col_num; $m++)
			{
				$mm=str_replace("'","",$col_num_arr[$m]);
				$txtsuppier= "txtsuppier_".$i."_".$mm;
				$txtquoted="txtquoted_".$i."_".$mm;
				$txtneg="txtneg_".$i."_".$mm;
				$txtcon="txtcon_".$i."_".$mm;
				$txtSuppCutableWidth="txtSuppCutableWidth_".$i."_".$mm;
				$txtpayterm="txtpayterm_".$i."_".$mm;
				$txtLCType="txtLCType_".$i."_".$mm;
				$txttenor="txttenor_".$i."_".$mm;
				$cboShipMode="cboShipMode_".$i."_".$mm;
				$cboSource="cboSource_".$i."_".$mm;
				$txt_supplier_check="txt_supplier_check_".$mm;
				$origin="origin_".$mm;
				
				if($supp_data!=''){
					$supp_data.= "*".$$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtSuppCutableWidth."_".$$txtpayterm."_".$$txttenor."_".$$cboShipMode."_".$$cboSource;
				}else{
					$supp_data = $$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtSuppCutableWidth."_".$$txtpayterm."_".$$txttenor."_".$$cboShipMode."_".$$cboSource;
				}
				
				if ($data_array_supp_dtls!='') {$data_array_supp_dtls .=",";}
				$data_array_supp_dtls .="(".$id_supp_dtls.",".$mst_id.",".$id_dtls.",1,'".$$txtsuppier."','".$$txtItemDescrip."','".$$txtUom."','".$$txtquoted."','".$$txtneg."','".$$txtcon."','".$$txtSuppCutableWidth."','".$$txtneg."','".$$txtpayterm."','".$$txttenor."','".$$cboShipMode."','".$$cboSource."','".$$txt_supplier_check."','".$$origin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1,'".$$txtLCType."')";
				$id_supp_dtls++;
			}

			for($m=0; $m<$company_num; $m++)
			{				
				$mm=str_replace("'","",$company_num_arr[$m]);
				$txtCompany= "txtCompany_".$i."_".$mm;
				$txtCompanyQuoted="txtCompanyQuoted_".$i."_".$mm;
				$txtCompanyNeg="txtCompanyNeg_".$i."_".$mm;
				$txtCompanyCon="txtCompanyCon_".$i."_".$mm;
				$txtCompanyCutableWidth="txtCompanyCutableWidth_".$i."_".$mm;
				$txtCompanyPayTerm="txtCompanyPayTerm_".$i."_".$mm;
				$txtCompanyTenor="txtCompanyTenor_".$i."_".$mm;
				$companyShipMode="companyShipMode_".$i."_".$mm;
				$companySource="companySource_".$i."_".$mm;
				$txt_company_check="txt_company_check_".$mm;
				$companyOrigin="companyOrigin_".$mm;

				if($com_data!=''){
					$com_data.= "*".$$txtCompany."_".$$txtCompanyQuoted."_".$$txtCompanyNeg."_".$$txtCompanyCon."_".$$txtCompanyCutableWidth."_".$$txtCompanyPayTerm."_".$$txtCompanyTenor."_".$$companyShipMode."_".$$companySource;
				}else{
					$com_data = $$txtCompany."_".$$txtCompanyQuoted."_".$$txtCompanyNeg."_".$$txtCompanyCon."_".$$txtCompanyCutableWidth."_".$$txtCompanyPayTerm."_".$$txtCompanyTenor."_".$$companyShipMode."_".$$companySource;
				}
				
				if ($data_array_supp_dtls!='') {$data_array_supp_dtls .=",";}
				$data_array_supp_dtls .="(".$id_supp_dtls.",".$mst_id.",".$id_dtls.",2,'".$$txtCompany."','".$$txtItemDescrip."','".$$txtUom."','".$$txtCompanyQuoted."','".$$txtCompanyNeg."','".$$txtCompanyCon."','".$$txtCompanyCutableWidth."','".$$txtCompanyNeg."','".$$txtCompanyPayTerm."','".$$txtCompanyTenor."','".$$companyShipMode."','".$$companySource."','".$$txt_company_check."','".$$companyOrigin."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				$id_supp_dtls++;
			}

			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$$txtItemCategory."','".$$txtdeterminationid."','".$$txtItemDescrip."','".$$txtMillReff."','".$$txtRdNo."','".$$txtWeight."','".$$txtCutableWidth."','".$$txtUom."','".$$txtQty."','".$$txtRate."','".$$txtAmt."','".$supp_data."','".$com_data."','".$$txtjobid."','".$$txtfabriccostdtlsid."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";

			$id_dtls++;
		}

		$rID=sql_update("req_comparative_mst",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID1=execute_query("delete from req_comparative_dtls where mst_id =".$update_id."",0);
		$rID2=execute_query("delete from req_comparative_supp_dtls where mst_id =".$update_id."",0);
		//echo "10**INSERT INTO req_comparative_supp_dtls (".$field_array_supp_dtls.") VALUES ".$data_array_supp_dtls; oci_rollback($con); disconnect($con);die;
		$rID3=sql_insert("req_comparative_dtls",$field_array_dtls,$data_array_dtls,0);	
		$rID4=sql_insert("req_comparative_supp_dtls",$field_array_supp_dtls,$data_array_supp_dtls,0);	
		//echo "10**INSERT INTO req_comparative_supp_dtls (".$field_array_supp_dtls.") VALUES ".$data_array_supp_dtls; 
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4; oci_rollback($con); disconnect($con);die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1 && $rID3==1 && $rID4==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id)."**".str_replace("'","",$txt_requisition_dtls);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1 && $rID2==1 && $rID3==1 && $rID4==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id)."**".str_replace("'","",$txt_requisition_dtls);
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
		$cs_approved=return_field_value("approved","req_comparative_mst","id=$update_id","approved");
		if($cs_approved==1 || $cs_approved==3)
		{
			echo "11**CS Approved, Delete Not Allow";disconnect($con);oci_rollback($con);die;
		}
		

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("req_comparative_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=sql_delete("req_comparative_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);
		$rID2=sql_delete("req_comparative_supp_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);
		// echo "10**".$rID.'='.$rID1.'='.$rID2."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1)
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
			if($rID==1 && $rID1==1 && $rID2==1)
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

//========== Generate CS ========
if($action=="load_cs_table")
{
	list($basis, $req_mst, $req_dtls, $supplier_id, $update_id, $txt_requisition_dtls,$company_id) = explode('**', $data);
	//echo $user_id.test;die;
	$supplier=explode(',', $supplier_id);
	$company=explode(',', $company_id);
	$supplier_count=count($supplier);
	$company_count=count(array_filter($company));
	$lc_type_arr= array( 4 => "TT/Pay Order", 5 => "FTT", 6 => "FDD/RTGS");
	$supplier_arr=return_library_array( "select supplier_name,id from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
	$company_arr=return_library_array( "select company_name,id from lib_company where status_active=1 and is_deleted=0",'id','company_name');
	$company_width=$company_count*720;

	$tbl_width=860+($supplier_count*720)+($company_width*1);
	$maing_grp_sql="select id, main_group_name, user_id from lib_main_group where is_deleted=0";
	$maing_grp_sql_result=sql_select($maing_grp_sql);
	$group_wise_user=array();
	foreach($maing_grp_sql_result as $row)
	{
		$main_group_arr[$row[csf("id")]]=$row[csf("main_group_name")];
		$user_id_arr=explode(",",$row[csf("user_id")]);
		foreach($user_id_arr as $u_id)
		{
			if($u_id) $group_wise_user[$u_id][$row[csf("id")]]=$row[csf("id")];
		}
	}
	
	//echo "<pre>";print_r($group_wise_user);die;
	
	if($basis==1) // Demand
	{
		$sql = "select a.main_group_id as MAIN_GROUP_ID, a.item_group_id as ITEM_GROUP_ID, a.brand_supplier as BRAND_SUPPLIER, a.item_description as ITEM_DESCRIPTION, a.uom as UOM, a.nominate_supplier_id as NOMINATED_SUPP, a.req_qty as REQ_QTY, a.req_rate as REQ_RATE, a.req_amount as REQ_AMOUNT, 4 as ITEM_CATEGORY_ID, b.item_name as ITEM_NAME, b.main_group_id as MAIN_GROUP_ID, 0 as COLOR, 0 as GMTS_SIZE, 0 as ITEM_COLOR, null as ITEM_SIZE, 0 as JOB_ID
		from scm_demand_dtls a, lib_item_group b 
		where a.item_group_id=b.id and a.id in($req_dtls) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$data_array=sql_select($sql);
	}
	else if($basis==2) //item
	{
		$sql = "select  4 as ITEM_CATEGORY_ID, b.main_group_id as MAIN_GROUP_ID, a.item_group_id as ITEM_GROUP_ID, b.item_name as ITEM_NAME, a.brand_supplier as BRAND_SUPPLIER, a.item_description as ITEM_DESCRIPTION, a.order_uom as UOM, 0 as REQ_QTY, 0 as REQ_RATE, 0 as REQ_AMOUNT, 0 as COLOR, 0 as GMTS_SIZE, 0 as ITEM_COLOR, null as ITEM_SIZE, 0 as JOB_ID
		from product_details_master a, lib_item_group b 
		where a.item_group_id=b.id and a.id in($req_mst) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by b.main_group_id, a.item_group_id, b.item_name, a.brand_supplier, a.item_description, a.order_uom";
		$data_array=sql_select($sql);
	}
	else //$basis==3, style
	{
		$condition= new condition();
		if($req_mst !=''){
			$condition->jobid_in ("$req_mst");
		}
		$condition->init();
		$fabrics=new fabric($condition);
		//echo $trim->getQuery();die;
		$totalqtyarray_arr=$fabrics->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

		$sql = "select a.id as ID, a.job_id as JOB_ID, a.fabric_description as ITEM_DESCRIPTION, a.uom as UOM, a.rate as RATE, 3 as ITEM_CATEGORY_ID, a.nominated_supp_multi as NOMINATED_SUPP, a.gsm_weight as WEIGHT, b.id as DETERMINATION_ID, b.fabric_ref as FABRIC_REF, b.rd_no as RD_NO, max(c.item_size) as CUTABLE_WIDTH, a.seq
		from wo_pre_cost_fabric_cost_dtls a, lib_yarn_count_determina_mst b, wo_pre_cos_fab_co_avg_con_dtls c
		where a.lib_yarn_count_deter_id=b.id and a.id=c.pre_cost_fabric_cost_dtls_id and a.id in($req_dtls) and b.entry_form=426 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.job_id, a.fabric_description, a.uom, a.rate, a.nominated_supp_multi, a.gsm_weight, b.id, b.fabric_ref, b.rd_no, a.seq
		order by a.seq";
		$data_array=sql_select($sql);
		
	}
	//echo "<pre>";print_r($data_array);
	foreach($data_array as $row)
	{
		if ($row['NOMINATED_SUPP'] != ""){			
			$nominated_supp.=$row["NOMINATED_SUPP"].',';
		}
	}
	$nominated_supp_arr=explode(",",rtrim($nominated_supp,','));
	$country_arr=return_library_array( "select id, country_name from lib_country where status_active=1 and is_deleted=0",'id','country_name');

	$supp_prev_entry_data=array();
	if($update_id)
	{
		$prev_sup_sql="select b.id as ID, b.mst_id as MST_ID, b.dtls_id as DTLS_ID, b.supp_id as SUPP_ID, b.quoted_price as QUOTED_PRICE, b.neg_price as NEG_PRICE, b.con_price as CON_PRICE, b.pay_term as PAY_TERM, b.tenor as TENOR, b.ship_mode as SHIP_MODE, b.origin_id as ORIGIN_ID, b.supp_type as SUPP_TYPE
		from req_comparative_mst a, req_comparative_supp_dtls b 
		where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.basis_id=3 and b.mst_id=$update_id and a.req_item_dtls_id='$txt_requisition_dtls'";
		//echo $prev_sup_sql;die;
		$prev_sup_sql_result=sql_select($prev_sup_sql);
		$supp_comp_origin_arr=array();
		$supp_prev_entry_data=array();
		foreach($prev_sup_sql_result as $row)
		{
			$supp_prev_entry_data[$row["DTLS_ID"]][$row["SUPP_ID"]]["quoted_price"]=$row["QUOTED_PRICE"];
			$supp_prev_entry_data[$row["DTLS_ID"]][$row["SUPP_ID"]]["neg_price"]=$row["NEG_PRICE"];
			$supp_prev_entry_data[$row["DTLS_ID"]][$row["SUPP_ID"]]["con_price"]=$row["CON_PRICE"];
			$supp_prev_entry_data[$row["DTLS_ID"]][$row["SUPP_ID"]]["pay_term"]=$row["PAY_TERM"];
			$supp_prev_entry_data[$row["DTLS_ID"]][$row["SUPP_ID"]]["tenor"]=$row["TENOR"];
			$supp_prev_entry_data[$row["DTLS_ID"]][$row["SUPP_ID"]]["ship_mode"]=$row["SHIP_MODE"];
			$supp_comp_origin_arr[$row["SUPP_ID"]][$row["SUPP_TYPE"]]=$row["ORIGIN_ID"];
		}
	}

	$sql_rate="select a.effective_from as APPROVED_DATE, a.supplier_id as SUPPLIER_ID, a.is_supp_comp as IS_SUPP_COMP, a.rate as RATE, a.item_category_id as ITEM_CATEGORY_ID, a.item_group_id as ITEM_GROUP_ID, a.prod_id as PROD_ID, b.detarmination_id as DETARMINATION_ID, b.weight as WEIGHT, b.item_description as ITEM_DESCRIPTION, b.brand_supplier as BRAND_SUPPLIER, b.unit_of_measure as UOM, b.color as COLOR, b.gmts_size as GMTS_SIZE, b.item_color as ITEM_COLOR, b.item_size as ITEM_SIZE from lib_supplier_wise_rate a, product_details_master b where a.prod_id=b.id and a.entry_form=512 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rate_res=sql_select($sql_rate);
	$product_id_array=array();
	foreach ($sql_rate_res as $row) {
		$key=$row["ITEM_CATEGORY_ID"].'**'.$row["DETARMINATION_ID"].'**'.$row["WEIGHT"].'**'.$row["UOM"];
		$product_id_array[$key][$row["SUPPLIER_ID"]][$row["IS_SUPP_COMP"]]['RATE'].=$row["RATE"].',';
		$product_id_array[$key][$row["SUPPLIER_ID"]][$row["IS_SUPP_COMP"]]['APPROVED_DATE'].=$row["APPROVED_DATE"].',';
	}
	
	//echo "<pre>";print_r($product_id_array);die;
	//echo $sql;die;
	

	?>

	<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all"  id="tbl_details">
		<thead>
			<tr>
				<th rowspan="2" width="40">SL</th>
				<th rowspan="2" width="80">Item Category</th>
				<th rowspan="2" width="180">Items Description</th>
				<th rowspan="2" width="70">Mill Reff.</th>
				<th rowspan="2" width="70">RD No/ Byr</th>
				<th rowspan="2" width="80">WEIGHT (Before Wash)</th>
				<th rowspan="2" width="50">UOM</th>
				<th rowspan="2" width="50">Cutable Width</th>
                <th rowspan="2" width="80">Req. Qty.</th>
				<th rowspan="2" width="80" title="Minimum Rate">Costing Price</th>
				<th rowspan="2" width="80">TTL Amount</th>
				<?
				$sql="select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name";
				if($company_count>0)
				{					
					foreach($company as $comp)
					{
						?>
						<th colspan="3" width="240">Origin&nbsp;<? echo create_drop_down("companyOrigin_".$comp, 100, $sql, "id,country_name", 1, "-- Select --", '', "", ""); ?></th>
						<th colspan="6" width="480"><? echo $company_arr[$comp]; ?>&nbsp;&nbsp;<input type="checkbox" id="txt_company_check_<?= $comp; ?>" name=""  onchange="fn_price_check(1,<?= $comp; ?>)"/></th>
						<?
					}
				}
				foreach($supplier as $supp)
				{		
					//if (in_array($supp,$nominated_supp_arr)) $nominated_supp="Nominated";
					//else $nominated_supp="Recommend";
					?>
					<th colspan="3" width="240">Origin&nbsp;<? echo create_drop_down("origin_".$supp, 100, $sql, "id,country_name", 1, "-- Select --", '', "", ""); ?></th>
					<th colspan="7" width="480"><? echo $supplier_arr[$supp]; ?>&nbsp;&nbsp;<input type="checkbox" id="txt_supplier_check_<?= $supp; ?>" name="" value="0" onchange="fn_price_check(2,<?= $supp; ?>)"/></th>
					<?
				}
				?>
			</tr>
			<tr>
				<?
				$m=1;
				if($company_count>0)
				{
					foreach($company as $comp)
					{
						?>
						<th width="80">Quoted Price</th>
						<th width="80" title="Lowest price">Last Price</th>
						<th width="80">Neg. Price</th>					
						<th width="80">Total Value</th>
						<th width="80">Cut Width&nbsp;<? if ($m==1) { ?><input type="checkbox" id="txt_cutablewidth_check" name="" onchange="fn_cutablewidth_check()"/><? } ?></th>
						<th width="80">Pay Term&nbsp;<? if ($m==1) { ?><input type="checkbox" id="txt_payterm_check" name="" onchange="fn_payterm_check()"/><? } ?></th>
						<th width="80">Tenor</th>
						<th width="80">Ship Mode</th>
						<th width="80">Source</th>
						<?
						$m++;
					}
				}
				$n=1;
				foreach($supplier as $supp)
				{
					?>
					<th width="80">Quoted Price</th>
					<th width="80" title="Lowest price">Last Price</th>
					<th width="80">Neg. Price</th>					
					<th width="80">Total Value</th>
					<th width="80">Cut Width&nbsp;<? if ($company_count<1 && $n==1) { ?><input type="checkbox" id="txt_cutablewidth_check" name="txt_cutablewidth_check" onchange="fn_cutablewidth_check()"/><? } ?></th>
					<th width="80">Pay Term&nbsp;<? if ($company_count<1 && $n==1) { ?><input type="checkbox" id="txt_payterm_check" name="" onchange="fn_payterm_check()"/><? } ?></th>

					<th>L/C Type</th>					

					<th width="80">Tenor</th>
					<th width="80">Ship Mode</th>
					<th width="80">Source</th>
					<?
					$n++;
				}
				?>
			</tr>
		</thead>
		<tbody>
		<?
		$i=1;
		foreach($data_array as $row)
		{
			$key=$row['ITEM_CATEGORY_ID'].'**'.$row['DETARMINATION_ID'].'**'.$row['WEIGHT'].'**'.$row['UOM'];
			$req_qty=array_sum($totalqtyarray_arr['woven']['grey'][$row['ID']]);
			
			$req_amount=$req_qty*$row['RATE'];
			if ($row['CUTABLE_WIDTH']==0) $cutable_width=""; else $cutable_width=$row['CUTABLE_WIDTH'];		
			?>
            <tr class="general" id="<? echo $i;?>">
                <td align="center"><?= $i;?>
                	<input type="hidden" name="" id="txtprod_<?= $i;?>" value="<? echo $row['PROD_IDS'] ;?>" >
                	<input type="hidden" name="" id="txtdeterminationid_<?= $i;?>" value="<? echo $row['DETERMINATION_ID']; ?>">
					<input type="hidden" name="" id="txtfabriccostdtlsid_<?= $i;?>" value="<? echo $row['ID']; ?>">
					<input type="hidden" name="" id="txtjobid_<?= $i;?>" value="<? echo $row['JOB_ID']; ?>">
                </td>
                <td align="center">
                    <input type="text" style="width:75px" name="txtItemCategory_<?= $i;?>" id="txtItemCategory_<?= $i;?>" class="text_boxes" title="<? echo $row['ITEM_CATEGORY_ID'] ;?>" value="<? echo $item_category[$row['ITEM_CATEGORY_ID']];?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:170px" name="txtItemDescrip_<?= $i;?>" id="txtItemDescrip_<?= $i;?>" class="text_boxes" value="<? echo $row['ITEM_DESCRIPTION']; ?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:70px" name="txtMillReff_<?= $i;?>" id="txtMillReff_<?= $i;?>" class="text_boxes" value="<? echo $row['FABRIC_REF'] ;?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:70px" name="txtRdNo_<?= $i;?>" id="txtRdNo_<?= $i;?>" class="text_boxes" value="<? echo $row['RD_NO'] ;?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:70px" name="txtWeight_<?= $i;?>" id="txtWeight_<?= $i;?>" class="text_boxes_numeric" value="<? echo $row['WEIGHT'] ;?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:50px" name="txtUom_<?= $i;?>" id="txtUom_<?= $i;?>" class="text_boxes" title="<? echo $row['UOM'] ;?>" value="<? echo $unit_of_measurement[$row['UOM']] ;?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:50px" name="txtCutableWidth_<?= $i;?>" id="txtCutableWidth_<?= $i;?>" class="text_boxes_numeric" value="<? echo $cutable_width; ?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:70px" name="txtQty_<?= $i;?>" id="txtQty_<?= $i;?>" class="text_boxes_numeric" title="<? echo $req_qty; ?>" value="<? echo number_format($req_qty,4,'.',''); ?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:70px" name="txtRate_<?= $i;?>" id="txtRate_<?= $i;?>" class="text_boxes_numeric" title="<? echo $row['RATE']; ?>" value="<? echo number_format($row['RATE'],2,'.',''); ?>" readonly>
                </td>
                <td align="center" >
                    <input type="text" style="width:70px" name="txtAmt_<?= $i;?>" id="txtAmt_<?= $i;?>" class="text_boxes_numeric" title="<? echo number_format($req_amount,8,'.',''); ?>" value="<? echo number_format($req_amount,4,'.',''); ?>" readonly>
                </td>
                <?
				if($company_count>0)
				{
					foreach($company as $comp_id)
					{
						$com_approved_rate_arr=explode(",",rtrim($product_id_array[$key][$comp_id][2]['RATE'],','));
						$com_last_price=min($com_approved_rate_arr);
						?>
							<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyQuoted_<?= $i.'_'.$comp_id;?>" value="" class="text_boxes_numeric"></td>
							<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyCon_<?= $i.'_'.$comp_id;?>" value="" class="text_boxes_numeric" placeholder="Display" readonly value="<?=  $com_last_price; ?>"></td>
							<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyNeg_<?= $i.'_'.$comp_id;?>" value="" onKeyUp="chkNegPrice(this.value,1,'<? echo $i.'**'.$comp_id; ?>')" class="text_boxes_numeric"></td>
							<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyTotalValue_<?= $i.'_'.$comp_id;?>" value="" class="text_boxes_numeric" readonly></td>
							<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyCutableWidth_<?= $i.'_'.$comp_id;?>" value="" class="text_boxes_numeric" placeholder="write"></td>
							<td align="center" >
								<? echo create_drop_down("txtCompanyPayTerm_".$i.'_'.$comp_id, 80, $pay_term, "", 1, "-- Select --", '', "", ""); ?>
							</td>
							<td align="center" ><input type="text" style="width:70px" name="" id="txtCompanyTenor_<?= $i.'_'.$comp_id;?>" value="" class="text_boxes_numeric"></td>
							<td align="center" >
								<? echo create_drop_down("companyShipMode_".$i.'_'.$comp_id, 80, $shipment_mode, "", 1, "-- Select --", '', "", ""); ?>
							</td>
							<td align="center" >
								<? echo create_drop_down("companySource_".$i.'_'.$comp_id, 80, $source, "", 1, "-- Select --", '', "", ""); ?>
							</td>
						<?
					}
				}
                foreach($supplier as $sup_id)
                {
                	$supp_approved_rate_arr=explode(",",rtrim($product_id_array[$key][$sup_id][1]['RATE'],','));
					$supp_last_price=min($supp_approved_rate_arr);
					$backgroundColor="";
					$nominated_supp="Recommend";
					if (in_array($sup_id,$nominated_supp_arr)) {
						$backgroundColor="background-color: yellow";
						$nominated_supp="Nominated";
					}
                    ?>
                        <td align="center" style="<?= $backgroundColor; ?>"><input type="text" style="width:70px; <?= $backgroundColor; ?>" name="" id="txtquoted_<?= $i.'_'.$sup_id;?>" value="" class="text_boxes_numeric"></td>
                        <td align="center" style="<?= $backgroundColor; ?>"><input type="text" style="width:70px; <?= $backgroundColor; ?>" name="" id="txtcon_<?= $i.'_'.$sup_id;?>"  value="<? echo $supp_last_price; ?>" class="text_boxes_numeric" placeholder="Display" readonly></td>
						<td align="center" style="<?= $backgroundColor; ?>"><input type="text" style="width:70px; <?= $backgroundColor; ?>" name="" id="txtneg_<?= $i.'_'.$sup_id;?>" value="" onKeyUp="chkNegPrice(this.value,2,'<? echo $i.'**'.$sup_id; ?>')"class="text_boxes_numeric"></td>
						<td align="center" style="<?= $backgroundColor; ?>"><input type="text" style="width:70px; <?= $backgroundColor; ?>" name="" id="txttotalvalue_<?= $i.'_'.$sup_id;?>" class="text_boxes_numeric" readonly></td>
						<td align="center" style="<?= $backgroundColor; ?>"><input type="text" style="width:70px; <?= $backgroundColor; ?>" name="" id="txtSuppCutableWidth_<?= $i.'_'.$sup_id;?>" class="text_boxes_numeric" placeholder="write"></td>
						<td align="center" style="<?= $backgroundColor; ?>">
							<? echo create_drop_down("txtpayterm_".$i.'_'.$sup_id, 80, $pay_term, "", 1, "-- Select --", '', "", ""); ?>		
						</td>
						<td align="center" style="<?= $backgroundColor; ?>">
							<? 
							
							 echo create_drop_down("txtLCType_".$i.'_'.$sup_id, 80, $lc_type_arr,"", 1, "-- Select --",  '' , "", "") ?>		
						</td>
						
						<td align="center" style="<?= $backgroundColor; ?>"><input type="text" style="width:70px; <?= $backgroundColor; ?>" name="" id="txttenor_<?= $i.'_'.$sup_id;?>" value="" class="text_boxes_numeric"></td>
						<td align="center" style="<?= $backgroundColor; ?>"><? echo create_drop_down("cboShipMode_".$i.'_'.$sup_id, 80, $shipment_mode, "", 1, "-- Select --", '', "", ""); ?></td>
						<td align="center" style="<?= $backgroundColor; ?>"><? echo create_drop_down("cboSource_".$i.'_'.$sup_id, 80, $source, "", 1, "-- Select --", '', "", ""); ?></td>
                    <? 
                }
                ?>
            </tr>
            <?
            $i++;	
		}
		?>
		</tbody>
	</table>
	<?
	exit();
}

//========== Statment ========
if($action=="load_statment_table")
{
	list($basis, $req_mst, $req_dtls, $supplier_id, $update_id) = explode('**', $data);
	// $supplier=explode(',', $supplier_id);
	// $supplier_count=count($supplier);
	// $tbl_width=800+($supplier_count*500);

	$supplier_arr=return_library_array( "select supplier_name,id from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
	$company_arr = return_library_array("select id,company_name from lib_company where is_deleted=0","id","company_name");

	$update_id_cond='';
	if ($update_id != '') $update_id_cond=" and a.id <> $update_id";

	$sql_items_res=sql_select("select a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.cs_date as CS_DATE, a.cs_valid_date as CS_VALID_DATE, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.mill_reff as MILL_REFF, b.rd_no as RD_NO, b.weight as WEIGHT, b.cutable_width as CUTABLE_WIDTH, b.uom as UOM, b.detarmination_id as DETARMINATION_ID
	from req_comparative_mst a, req_comparative_dtls b
	where a.id=b.mst_id and a.entry_form=512 and a.basis_id=$basis and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $update_id_cond order by b.id desc");
	$item_names_arr=array();

	foreach ($sql_items_res as $row) 
	{
		$items=$row['ITEM_CATEGORY_ID'].'**'.$row['DETARMINATION_ID'].'**'.$row['WEIGHT'].'**'.$row['UOM'];
		if ($check_item[$items] == "")
		{
			$check_item[$items]=$items;
			$item_names_arr[$items]['ITEMS']=$items;
			$item_names_arr[$items]['DTLS_ID']=$row['DTLS_ID'];
			$item_names_arr[$items]['CS_VALID_DATE']=$row['CS_VALID_DATE'];
		}		
	}
	//echo '<pre>';print_r($item_names_arr);

	/*$sql_rate="select a.effective_from as APPROVED_DATE, a.supplier_id as SUPPLIER_ID, a.is_supp_comp as IS_SUPP_COMP, a.rate as RATE, a.item_category_id as ITEM_CATEGORY_ID, a.item_group_id as ITEM_GROUP_ID, a.prod_id as PROD_ID, b.item_description as ITEM_DESCRIPTION, b.brand_supplier as BRAND_SUPPLIER, b.unit_of_measure as UOM, b.color as COLOR, b.gmts_size as GMTS_SIZE, b.item_color as ITEM_COLOR, b.item_size as ITEM_SIZE from lib_supplier_wise_rate a, product_details_master b where a.prod_id=b.id and a.entry_form=482 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
	$sql_rate_res=sql_select($sql_rate);
	$product_id_array=array();
	foreach ($sql_rate_res as $row) {
		$key=$row["ITEM_CATEGORY_ID"].'**'.$row["ITEM_GROUP_ID"].'**'.$row["BRAND_SUPPLIER"].'**'.$row["ITEM_DESCRIPTION"].'**'.$row["UOM"].'**'.$row["COLOR"].'**'.$row["GMTS_SIZE"].'**'.$row["ITEM_COLOR"].'**'.$row["ITEM_SIZE"];
		$product_id_array[$key][$row["SUPPLIER_ID"]][$row["IS_SUPP_COMP"]]['RATE']=$row["RATE"];
	}*/

	if($basis==1) // demand
	{
		$sql = "select a.main_group_id as MAIN_GROUP_ID, a.item_group_id as ITEM_GROUP_ID, a.brand_supplier as BRAND_SUPPLIER, a.item_description as ITEM_DESCRIPTION, a.uom as UOM, a.req_qty as REQ_QTY, a.req_rate as REQ_RATE, a.req_amount as REQ_AMOUNT, 4 as ITEM_CATEGORY_ID, b.item_name as ITEM_NAME, b.main_group_id as MAIN_GROUP_ID
		from scm_demand_dtls a, lib_item_group b 
		where a.item_group_id=b.id and a.id in($req_dtls) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$data_array=sql_select($sql);
		
	}
	else if($basis==2) //$basis==2, item
	{
		$sql = "select  4 as ITEM_CATEGORY_ID, b.main_group_id as MAIN_GROUP_ID, a.item_group_id as ITEM_GROUP_ID, b.item_name as ITEM_NAME, a.brand_supplier as BRAND_SUPPLIER, a.item_description as ITEM_DESCRIPTION, a.order_uom as UOM, 0 as REQ_QTY, 0 as REQ_RATE, 0 as REQ_AMOUNT 
		from product_details_master a, lib_item_group b 
		where a.item_group_id=b.id and a.id in($req_mst) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by b.main_group_id, a.item_group_id, a.brand_supplier, a.item_description, a.uom";
		$data_array=sql_select($sql);
	}
	else //$basis==3, style
	{
		$sql = "select a.id as ID, a.fabric_description as ITEM_DESCRIPTION, a.uom as UOM, a.rate as RATE, 3 as ITEM_CATEGORY_ID, a.gsm_weight as WEIGHT, b.id as DETERMINATION_ID, b.fabric_ref as FABRIC_REF, b.rd_no as RD_NO, b.cutable_width as CUTABLE_WIDTH
		from wo_pre_cost_fabric_cost_dtls a, lib_yarn_count_determina_mst b 
		where a.lib_yarn_count_deter_id=b.id and a.id in($req_dtls) and b.entry_form=426 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by a.id desc";

		$dtls_data=sql_select($sql);
		$data_array=array();
		foreach($dtls_data as $row)
		{				
			$data_ref=$items=$row['ITEM_CATEGORY_ID'].'**'.$row['DETERMINATION_ID'].'**'.$row['WEIGHT'].'**'.$row['UOM'];
			$data_array[$data_ref]["ITEM_CATEGORY_ID"]=$row["ITEM_CATEGORY_ID"];
			$data_array[$data_ref]["ITEM_DESCRIPTION"]=$row["ITEM_DESCRIPTION"];
			$data_array[$data_ref]["UOM"]=$row["UOM"];
			$data_array[$data_ref]["WEIGHT"]=$row["WEIGHT"];
			$data_array[$data_ref]["FABRIC_REF"]=$row["FABRIC_REF"];
			$data_array[$data_ref]["RD_NO"]=$row["RD_NO"];
			$data_array[$data_ref]["CUTABLE_WIDTH"]=$row["CUTABLE_WIDTH"];
			$data_array[$data_ref]["UOM"]=$row["UOM"];
		}
	}
	// echo $sql;die;
	
	?>

	<table width="650" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="300">Item Description</th>
				<th width="150">Last Supplier Name</th>
				<th width="80">Last CS Rate</th>
				<th width="80">Price Validity Date</th>
			</tr>
		</thead>
		<tbody>
		<?
		$i=1;
		foreach($data_array as $row)
		{
			$item_desc=$row['ITEM_DESCRIPTION'];
			$items=$row['ITEM_CATEGORY_ID'].'**'.$row['DETERMINATION_ID'].'**'.$row['WEIGHT'].'**'.$row['UOM'];
			if ($items==$item_names_arr[$items]['ITEMS'])
			{
				$dtls_id=$item_names_arr[$items]['DTLS_ID'];
				$supp_dtls_arr=sql_select("select supp_id as SUPP_ID, supp_type as SUPP_TYPE, last_approval_rate as LAST_APPROVAL_RATE from req_comparative_supp_dtls where dtls_id in($dtls_id) and last_approval_rate is not null and status_active=1 and is_deleted=0");
				$row_num=count($supp_dtls_arr);
				$cs_valid_date=change_date_format($item_names_arr[$items]['CS_VALID_DATE']);
			}
			?>
			<tr>
				<td width="40" align="center" rowspan="<?= $row_num; ?>"><?= $i;?>
				<td width="300" rowspan="<?= $row_num; ?>"><? echo $item_desc; ?></td>
				<?
				if ($row_num > 0)
				{	
					foreach ($supp_dtls_arr as $val) 
					{
						?>
						<td width="150">
							<?
							$supp_name='';
							if ($val['SUPP_TYPE']==1) $supp_name=$supplier_arr[$val['SUPP_ID']];
							else if ($val['SUPP_TYPE']==2) $supp_name=$company_arr[$val['SUPP_ID']];
							echo $supp_name; 
							?>									
						</td>
						<td width="80" align="right"><? echo $val['LAST_APPROVAL_RATE']; ?></td>
						<td width="80" align="center"><? echo $cs_valid_date; ?></td>
						</tr>
						<?
					}
				}
				else
				{
					?>
					<td width="150"></td>
					<td width="80"></td>
					<td width="80"></td>
					<?
				}		
				?>						
			</tr>
			<?
			$i++;
		}
		?>
		</tbody>
	</table>

	<?
	exit();
}

//========== start Demand No/Item ========
if($action=="demand_popup")
{
    echo load_html_head_contents("Demand Popup", "../../../", 1, 1,$unicode,'','');
    extract($_REQUEST);
	$txt_requisition_mst=str_replace("'","",$txt_requisition_mst);
	$txt_requisition_dtls=str_replace("'","",$txt_requisition_dtls);
	$update_id=str_replace("'","",$update_id);
	//echo $txt_requisition_mst."=".$txt_requisition_dtls;die;
    /*$userCredential = sql_select("SELECT item_cate_id FROM user_passwd where id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    if($item_cate_id !='') {
        $item_cate_credential_cond = $item_cate_id ;
    }
    else
    {
        $cre_cat_arr=array_keys($general_item_category);
        array_push($cre_cat_arr, 5, 6, 7, 23  );
        $item_cate_credential_cond = implode(",",$cre_cat_arr);
    }*/
    ?>
    <script>
		var permission='<? echo $permission; ?>';
		function set_all_old_data()
		{
			var old=document.getElementById('old_data_row_color').value;
			if(old!="")
			{ 
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] ) ;
				}
			}
		}
		
    	function fn_show_list()
		{
			var txt_req_no=$("#txt_req_no").val();
			var cbo_item_category=trim($("#cbo_item_category").val());
			//alert(cbo_item_category);
			if ($(ddo_checkbox_id).is(":checked")) 
			{
				var txt_days=$("#txt_days").val();
				var ddo_checkbox_value=1;				
			}
			else
			{
				if(txt_req_no=="" && cbo_item_category==0)
				{
					if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
					{
						return;
					}
				}
				var ddo_checkbox_value=2;
			}	
			
			
            show_list_view ( document.getElementById('cbo_item_category').value+'**'+document.getElementById('txt_req_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_requisition_mst; ?>'+'**'+'<? echo $txt_requisition_dtls; ?>'+'**'+'<? echo $update_id; ?>'+'**'+txt_days+'**'+ddo_checkbox_value,'requisition_list_view', 'search_div', 'comparative_statement_fabrics_controller', 'setFilterGrid(\'list_view\',-1)');
			setFilterGrid('tbl_list_view_req',-1);
			set_all_old_data();
        };

		var selected_no = new Array(); var selected_id = new Array(); 
		var selected_dtls = new Array(); var selected_job_id = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_view_req' ).rows.length; 
			tbl_row_count = tbl_row_count ;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($("#search"+i).is(':visible'))
				{
					js_set_value( i );
				}
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		// function set_all()
		// {
		// 	var old=document.getElementById('txt_req_row_id').value; 
		// 	if(old!="")
		// 	{   
		// 		old=old.split(",");
		// 		for(var k=0; k<old.length; k++)
		// 		{   
		// 			js_set_value( old[k] ) 
		// 		} 
		// 	}
		// }
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_dtls' + str).val(), selected_dtls ) == -1 ) {
				selected_no.push( $('#txt_mst_no' + str).val() );
				selected_id.push( $('#txt_mst_id' + str).val() );
				selected_dtls.push( $('#txt_dtls' + str).val() );
				selected_job_id.push( $('#txt_job_id' + str).val() );
			}
			else {
				for( var i = 0; i < selected_dtls.length; i++ ) {
					if( selected_dtls[i] == $('#txt_dtls' + str).val() ) break;
				}
				selected_no.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_dtls.splice( i, 1 );
				selected_job_id.splice( i, 1 );
			}
			var req_no = '';var req_id = ''; var req_dtls_id = ''; var job_id = '';
			for( var i = 0; i < selected_dtls.length; i++ ) {
				req_no += selected_no[i] + ',';
				req_id += selected_id[i] + ',';
				req_dtls_id += selected_dtls[i] + ',';
				job_id += selected_job_id[i] + ',';
			}
			req_no = req_no.substr( 0, req_no.length - 1 );
			req_id = req_id.substr( 0, req_id.length - 1 );
			req_dtls_id = req_dtls_id.substr( 0, req_dtls_id.length - 1 );
			job_id = job_id.substr( 0, job_id.length - 1 );
			$('#hidden_req_no').val(req_no);
			$('#hidden_req_id').val(req_id);
			$('#hidden_req_dtls_id').val(req_dtls_id);
			$('#hidden_job_id').val(job_id);
		}
		
		function req_reset()
		{
			reset_form('searchexportinformationfrm','search_div','cbo_item_category','','');
			//alert($("#cbo_item_category").val());
		}
    </script>
	</head>
        <body>
            <div align="center" style="width:1110px;">
                <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
                <input type="hidden" name="hidden_req_no" id="hidden_req_no" class="text_boxes" value="">
                <input type="hidden" name="hidden_req_id" id="hidden_req_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_req_dtls_id" id="hidden_req_dtls_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_job_id" id="hidden_job_id" class="text_boxes" value="">
                    <fieldset style="width:1110px;">
                        <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all" align="center">
                            <thead>
                                <tr>
                                    <th width="160" class="must_entry_caption" >Item Category</th>
                                    <th width="150">Demand No</th>
                                    <th width="200" class="must_entry_caption" >Date Range</th>
                                    <th width="120" title="Pending Demand">PD Date Over</th>
                                    <th>
                                        <input type="button" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" onClick="req_reset()" />
                                    </th>
                                </tr>
                            </thead>
                            <tr class="general">
                                <td>
                                    <? 
                                    echo create_drop_down( "cbo_item_category", 150,"select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_id in (4) order by short_name","category_id,short_name", 0, "", 4, 1,'',"","","","");
                                    ?>
                                </td>
                                <td >
                                	<input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:140px;" />
                                </td>
                                <td>
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date"/>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" placeholder="To Date"/>
                                </td>
                                <td>
                                    <input type="checkbox" id="ddo_checkbox_id" name="ddo_checkbox_id"/>
                                    <input type="text" name="txt_days" id="txt_days" class="text_boxes_numeric" style="width:80px;" placeholder="3 Days"/>
                                </td>
                                <td>
                                    <input type="button" id="search_button" class="formbutton" value="Show" onClick="fn_show_list();" style="width:80px;" />
                                </td>
                            </tr> 
                            <tr>
                                <td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
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

if($action==='requisition_list_view')
{
	list($category_id, $req_no, $req_start_date, $req_end_date, $year, $txt_requisition_mst, $txt_requisition_dtls, $update_id, $txt_days, $ddo_checkbox_value) = explode('**', $data);
	//echo $txt_requisition_dtls;die;
	$requisition_dtlsArr = explode(",",$txt_requisition_dtls);
	//echo "<pre>";print_r($requisition_dtlsArr);die;
	
	//$main_group_arr = return_library_array("select id, main_group_name from lib_main_group where is_deleted=0","id","main_group_name");
	$maing_grp_sql="select id, main_group_name, user_id from lib_main_group where is_deleted=0";
	$maing_grp_sql_result=sql_select($maing_grp_sql);
	$group_wise_user=array();
	foreach($maing_grp_sql_result as $row)
	{
		$main_group_arr[$row[csf("id")]]=$row[csf("main_group_name")];
		$main_group_user[$row[csf("id")]]=$row[csf("user_id")];
		$user_id_arr=explode(",",$row[csf("user_id")]);
		foreach($user_id_arr as $u_id)
		{
			if($u_id) $group_wise_user[$u_id][$row[csf("id")]]=$row[csf("id")];
		}
	}

	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where is_deleted=0","id","supplier_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
	$dealing_merchant_arr = return_library_array("select b.id, b.team_member_name from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.project_type=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0","id","team_member_name");
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$user_name_arr=return_library_array( "select id, user_name from user_passwd where valid=1",'id','user_name');
	$category_cond ='';$req_num ='';$year_cond="";
	if($req_no !='') {$req_num = "and a.sys_number like '%$req_no%'";}
	if ($req_start_date != '' && $req_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and a.demand_date between '" . change_date_format($req_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($req_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and a.demand_date between '" . change_date_format($req_start_date, '', '', 1) . "' and '" . change_date_format($req_end_date, '', '', 1) . "'";
        }
    } 
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.demand_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.demand_date,'YYYY') =$year ";
			}
		}
    }
	$mst_cond="";
	if($update_id) $mst_cond=" and id<>$update_id ";
	$duplicat_sql="select req_item_dtls_id from req_comparative_mst where basis_id=1 and status_active=1 and is_deleted=0 $mst_cond";
	//echo $duplicat_sql."<br>";
	$duplicat_data=sql_select($duplicat_sql);
	$req_dtls_id_arr=array();
	foreach($duplicat_data as $value)
	{
		$req_id_arr=explode(",",$value[csf('req_item_dtls_id')]);
		foreach($req_id_arr as $req_id)
		{
			$req_dtls_id_arr[$req_id]=$req_id;
		}
	}
	unset($duplicat_data);

	if ($db_type==0) $current_date=strtotime(date("Y-m-d"));
	else $current_date=strtotime(date("d-M-Y"));

	if ($ddo_checkbox_value==1) // check condition
	{
		if ($db_type==0)
		{
			if ($txt_days != '') $pending_demand_date=date("Y-m-d", strtotime("-$txt_days days"));
			else $pending_demand_date=date('Y-m-d', strtotime('-3 days'));
		}
		else
		{
			if ($txt_days != '') $pending_demand_date=date("d-M-Y", strtotime("-$txt_days days"));
			else $pending_demand_date=date('d-M-Y', strtotime('-3 days'));
		}		

		$sql = "select a.id, a.sys_number, a.sys_number_prefix_num, a.demand_date, a.buyer_id, a.deling_merchant_id, 4 as item_category_id, b.id as dtls_id, b.main_group_id, b.item_group_id, b.pre_cost_dtls_id, b.brand_supplier, b.item_description, b.nominate_supplier_id, b.uom, b.job_id
		from scm_demand_mst a, scm_demand_dtls b
		where a.id=b.mst_id and a.demand_date<='$pending_demand_date' and a.entry_form=479 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by a.id desc";
		//echo $sql;//die;
		$data_array=sql_select($sql);
	}
	else // uncheck condition
	{
		$sql = "select a.id, a.sys_number, a.sys_number_prefix_num, a.demand_date, a.buyer_id, a.deling_merchant_id, 4 as item_category_id, b.id as dtls_id, b.main_group_id, b.item_group_id, b.pre_cost_dtls_id, b.brand_supplier, b.item_description, b.nominate_supplier_id, b.uom,b.job_id
		from scm_demand_mst a, scm_demand_dtls b
		where a.id=b.mst_id and a.entry_form=479 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $req_num $date_cond $year_cond 
		order by a.id desc";
		//echo $sql;//die;
		$data_array=sql_select($sql);
	}	
			
	?>
	<table width="1090" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="30">SL No</th>
            <th width="100">Demand No</th>
            <th width="70">Demand Date</th>
            <th width="100">Buyer</th>
            <th width="100">Dealing Merchant</th>
            <th width="100">Procurement Concern person</th>
            <th width="100">Main Group</th>
            <th width="100">Items Name</th>
            <th width="80">Supp. Reff.</th>
            <th width="150">Item Description</th>
            <th width="40">UOM</th>
            <th>Nominated Supp</th>
        </thead>
     </table>
     <div style="width:1110px; overflow-y:scroll; max-height:280px">
     	<table width="1090" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view_req">
			<?			
	        $i = 1;$oldDataRow="";
	        $background_color="";
	        if ($user_level==2) // Admin user
	        {
	        	foreach($data_array as $row)
		        {
		            if( in_array($row[csf('dtls_id')], $requisition_dtlsArr) )
					{
						if($oldDataRow=="") $oldDataRow = $i; else $oldDataRow .= ",".$i;
					}
					
					if($req_dtls_id_arr[$row[csf('dtls_id')]]=="")
					{
						if ($i%2==0) $bgcolor="#FFFFFF";
						else $bgcolor="#E9F3FF";
						$user_ids=explode(',',$main_group_user[$row[csf('main_group_id')]]);
						$user_name='';
						foreach ($user_ids as $id) {
							$user_name.=$user_name_arr[$id].',';
						}
						$demand_date=strtotime($row[csf('demand_date')]);
						$date_difference=floor((($current_date-$demand_date)/(60*60*24)));
						if ($date_difference>=3) $background_color="background-color: red";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer; <?= $background_color; ?>" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)" >  
	                        <td width="30" align="center"><? echo $i; ?>
	                        <input type="hidden" name="txt_mst_no" id="txt_mst_no<?php echo $i ?>" value="<? echo $row[csf('sys_number_prefix_num')]; ?>"/>
	                        <input type="hidden" name="txt_mst_id" id="txt_mst_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>	
	                        <input type="hidden" name="txt_dtls" id="txt_dtls<?php echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
	                        <input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('job_id')]; ?>"/>
	                        </td>
	                        <td width="100"><p><? echo $row[csf('sys_number')]; ?></p></td>
	                        <td width="70" align="center"><p><? echo change_date_format($row[csf('demand_date')]); ?></p></td>
	                        <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
	                        <td width="100"><p><? echo $dealing_merchant_arr[$row[csf('deling_merchant_id')]]; ?></td>
	                        <td width="100"><p><? echo rtrim($user_name,','); ?></td>
	                        <td width="100"><p><? echo $main_group_arr[$row[csf('main_group_id')]]; ?></td>
	                        <td width="100"><p><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></p></td>
	                        <td align="center" width="80"><p><? echo $row[csf('brand_supplier')];?></p></td>
	                        <td width="150"><p><? echo $row[csf('item_description')];; ?></p></td>
	                        <td  width="40"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
	                        <td><p><? echo $supplier_arr[$row[csf('nominate_supplier_id')]]; ?></td>	                        
	                    </tr>
	                    <?
	                    $i++;
					}
		        }
	        }
	        else 
	        {	
		        foreach($data_array as $row)
		        {	        	
		            if($group_wise_user[$user_id][$row[csf('main_group_id')]]) // User wise permission check in main group
					{	

						if( in_array($row[csf('dtls_id')], $requisition_dtlsArr) )
						{
							if($oldDataRow=="") $oldDataRow = $i; else $oldDataRow .= ",".$i;
						}
						
						if($req_dtls_id_arr[$row[csf('dtls_id')]]=="")
						{
							if ($i%2==0) $bgcolor="#FFFFFF";
							else $bgcolor="#E9F3FF";
							$user_ids=explode(',',$main_group_user[$row[csf('main_group_id')]]);
							$user_name='';
							foreach ($user_ids as $id) {
								$user_name.=$user_name_arr[$id].',';
							}
							$demand_date=strtotime($row[csf('demand_date')]);
							$date_difference=floor((($current_date-$demand_date)/(60*60*24)));
							if ($date_difference>=3) $background_color="background-color: red";
							?>
		                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer; <?= $background_color; ?>" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)" >  
		                        <td width="30" align="center"><? echo $i; ?>
		                        <input type="hidden" name="txt_mst_no" id="txt_mst_no<?php echo $i ?>" value="<? echo $row[csf('sys_number_prefix_num')]; ?>"/>
		                        <input type="hidden" name="txt_mst_id" id="txt_mst_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>	
		                        <input type="hidden" name="txt_dtls" id="txt_dtls<?php echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
		                        <input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('job_id')]; ?>"/>
		                        </td>
		                        <td width="100"><p><? echo $row[csf('sys_number')]; ?></p></td>
		                        <td width="70" align="center"><p><? echo change_date_format($row[csf('demand_date')]); ?></p></td>
		                        <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
		                        <td width="100"><p><? echo $dealing_merchant_arr[$row[csf('deling_merchant_id')]]; ?></td>
		                        <td width="100"><p><? echo rtrim($user_name,','); ?></td>
		                        <td width="100"><p><? echo $main_group_arr[$row[csf('main_group_id')]]; ?></td>
		                        <td width="100"><p><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></p></td>
		                        <td align="center" width="80"><p><? echo $row[csf('brand_supplier')];?></p></td>
		                        <td width="150"><p><? echo $row[csf('item_description')];; ?></p></td>
		                        <td  width="40"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
		                        <td><p><? echo $supplier_arr[$row[csf('nominate_supplier_id')]]; ?></td>	                        
		                    </tr>
		                    <?
		                    $i++;
						}
		            }
		        }
	        }  
			?>
		</table>
    </div>
		<table width="1090" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%"> 
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            <input type="hidden" name="old_data_row_color" id="old_data_row_color" value="<? echo $oldDataRow; ?>"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</body>           
	</html>
	<?
	exit();
}

if($action=="item_popup")
{
    echo load_html_head_contents("Demand Popup", "../../../", 1, 1,$unicode,'','');
    extract($_REQUEST);
    /*$userCredential = sql_select("SELECT item_cate_id FROM user_passwd where id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    if($item_cate_id !='') {
        $item_cate_credential_cond = $item_cate_id ;
    }
    else
    {
        $cre_cat_arr=array_keys($general_item_category);
        array_push($cre_cat_arr, 5, 6, 7, 23  );
        $item_cate_credential_cond = implode(",",$cre_cat_arr);
    }*/
    ?>
    <script>
		var permission='<? echo $permission; ?>';
    	function fn_show_list(){
        if(form_validation('cbo_item_category','Item Category')==false){
				document.getElementById('search_div').innerHTML="Please Select Category First";
                return;
            }
            show_list_view ( document.getElementById('cbo_item_category').value+'**'+document.getElementById('cbo_item_group').value+'**'+document.getElementById('txt_code').value+'**'+'<? echo $txt_requisition_mst; ?>'+'**'+'<? echo $txt_requisition_dtls; ?>'+'**'+'<? echo $update_id; ?>','item_list_view', 'search_div', 'comparative_statement_fabrics_controller', 'setFilterGrid(\'list_view\',-1)');
			setFilterGrid('tbl_list_view_req',-1);
        }

		var selected_no = new Array();var selected_id = new Array(); var selected_dtls = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_view_req' ).rows.length; 
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
				selected_no.push( $('#txt_mst_no' + str).val() );
				selected_id.push( $('#txt_mst_id' + str).val() );
				selected_dtls.push( $('#txt_dtls' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_mst_id' + str).val() ) break;
				}
				selected_no.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_dtls.splice( i, 1 );
			}
			var item_no = '';var item_id = ''; var item_dtls_id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				item_no += selected_no[i] + ',';
				item_id += selected_id[i] + ',';
				item_dtls_id += selected_dtls[i] + ',';
			}
			item_no = item_no.substr( 0, item_no.length - 1 );
			item_id = item_id.substr( 0, item_id.length - 1 );
			item_dtls_id = item_dtls_id.substr( 0, item_dtls_id.length - 1 );

			$('#hidden_item_no').val(item_no);
			$('#hidden_item_id').val(item_id);
			$('#hidden_item_dtls_id').val(item_dtls_id);
		}
		
		function req_reset()
		{
			reset_form('searchexportinformationfrm','search_div','cbo_item_category','','');
			//alert($("#cbo_item_category").val());
		}
    </script>
	</head>
        <body>
            <div align="center" style="width:900px;">
                <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
                <input type="hidden" name="hidden_item_no" id="hidden_item_no" class="text_boxes" value="">
                <input type="hidden" name="hidden_item_id" id="hidden_item_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_item_dtls_id" id="hidden_item_dtls_id" class="text_boxes" value="">
                    <fieldset style="width:850px;">
                        <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all">
                            <thead>
                                <tr>
                                    <th class="must_entry_caption" >Item Category</th>
                                    <th>Item Group</th>
                                    <th>Item Ref/Code</th>
                                    <th>
                                        <input type="button" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" onClick="req_reset()" />
                                    </th>
                                </tr>
                            </thead>
                            <tr class="general">
                                <td>
                                    <? 
                                    echo create_drop_down( "cbo_item_category", 150,"select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_id in (4) order by short_name","category_id,short_name", 0, "", 4, "",'',"","","","");
                                    ?>
                                </td>
                                <td>
									<?	echo create_drop_down("cbo_item_group",130,"select id, item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in (4) order by item_name","id,item_name",1,"-- Select --",0, "" );?>
                                </td>
                                <td >
                                <input type="text" name="txt_code" id="txt_code" class="text_boxes" style="width:140px;" />
                                </td>
                                <td>
                                    <input type="button" id="search_button" class="formbutton" value="Show" onClick="fn_show_list()" style="width:100px;" />
                                </td>
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

if($action==='item_list_view')
{
	list($category_id, $group_id, $item_code, $txt_requisition_mst, $txt_requisition_dtls, $update_id) = explode('**', $data);
	$main_group_arr = return_library_array("select id, main_group_name from lib_main_group where is_deleted=0","id","main_group_name");
	$category_cond ='';$group_num ='';$item_name ='';
	if($category_id !=0) {$category_cond = "and a.item_category_id in ($category_id)";}
	if($group_id !=0) {$group_num = "and b.id=$group_id";}
	if($item_code !='') {$item_name = "and a.brand_supplier like '%$item_code%'";}

	$sql = "select a.item_category_id, a.item_description, a.brand_supplier, a.order_uom, b.id as item_group_id, b.item_name, b.main_group_id, c.short_name as category_name,
	listagg(cast(a.id as varchar(4000)),',') within group (order by a.id) as id 
	from product_details_master a, lib_item_group b, lib_item_category_list c 
	where a.item_group_id=b.id and a.item_category_id=c.category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $category_cond $group_num $item_name
	group by a.item_category_id, a.item_description, a.brand_supplier, a.order_uom, b.id, b.item_name, b.main_group_id, c.short_name";
	//echo $sql;die;
	$data_array=sql_select($sql);		
	?>
	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL No</th>
            <th width="150">Item Category</th>
            <th width="150">Main Group</th>
            <th width="150">Items Name</th>
            <th width="200">Item Description</th>
            <th width="100">Supp. Reff.</th>
            <th>UOM</th>
        </thead>
     </table>
     <div style="width:900px; overflow-y:scroll; max-height:280px">
     	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view_req">
		<?			
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)" >  
					<td width="40" align="center"><? echo $i; ?>
					<input type="hidden" name="txt_mst_no" id="txt_mst_no<?php echo $i ?>" value="<? echo $row[csf('item_description')]; ?>"/>
					<input type="hidden" name="txt_mst_id" id="txt_mst_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>	
					<input type="hidden" name="txt_dtls" id="txt_dtls<?php echo $i ?>" value="<? echo $row[csf('item_group_id')]; ?>"/>
					</td>
                    <td width="150" align="center"><p><? echo $row[csf('category_name')]; ?></p></td>
					<td width="150"><p><? echo $main_group_arr[$row[csf('main_group_id')]]; ?></td>
                    <td width="150"><p><? echo $row[csf('item_name')];; ?></p></td>
                    <td width="200"><p><? echo $row[csf('item_description')];; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('brand_supplier')];;?></p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]];;?></p></td>
				</tr>
				<?
                $i++;
            }
			?>
		</table>
    </div>
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
	</body>           
	</html>
	<?
	exit();
}

if($action=="style_popup")
{
    echo load_html_head_contents("Style Popup", "../../../", 1, 1,$unicode,'','');
    extract($_REQUEST);
	$txt_requisition_mst=str_replace("'","",$txt_requisition_mst);
	$txt_requisition_dtls=str_replace("'","",$txt_requisition_dtls);
	$update_id=str_replace("'","",$update_id);
	$cbo_company_id=str_replace("'","",$cbo_company_id);

    ?>
    <script>
		var permission='<? echo $permission; ?>';
		function set_all_old_data()
		{
			var old=document.getElementById('old_data_row_color').value;
			if(old!="")
			{ 
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] ) ;
				}
			}
		}
		
    	function fn_show_list()
		{
			var txt_job_prifix = $("#txt_job_prifix").val();
			if (txt_job_prifix != "")
			{
				if(form_validation('cbo_company_id','Company')==false )
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_id*cbo_buyer_id','Company*Buyer')==false )
				{
					return;
				}
			}			
	
            show_list_view ( document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_id').value+'**'+document.getElementById('cbo_brand_id').value+'**'+document.getElementById('cbo_season_id').value+'**'+document.getElementById('cbo_season_year').value+'**'+document.getElementById('txt_job_prifix').value+'**'+document.getElementById('txt_style').value+'**'+document.getElementById('txt_internal_ref').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_requisition_mst; ?>'+'**'+'<? echo $txt_requisition_dtls; ?>'+'**'+'<? echo $update_id; ?>','style_list_view', 'search_div', 'comparative_statement_fabrics_controller', 'setFilterGrid(\'list_view\',-1)');
			setFilterGrid('tbl_list_view_req',-1);
			set_all_old_data();
        };

		var selected_no = new Array();var selected_id = new Array(); var selected_dtls = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_view_req' ).rows.length; 
			tbl_row_count = tbl_row_count ;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($("#search"+i).is(':visible'))
				{
					js_set_value( i );
				}
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
			if ($('#txt_approved' + str).val()!=1 && $('#txt_approved' + str).val() !=3){
				alert("Please At First Budget Approve.");
				return;
			}
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_dtls' + str).val(), selected_dtls ) == -1 ) {
				selected_no.push( $('#txt_mst_no' + str).val() );
				selected_id.push( $('#txt_mst_id' + str).val() );
				selected_dtls.push( $('#txt_dtls' + str).val() );
			}
			else {
				for( var i = 0; i < selected_dtls.length; i++ ) {
					if( selected_dtls[i] == $('#txt_dtls' + str).val() ) break;
				}
				selected_no.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_dtls.splice( i, 1 );
			}
			var req_no = '';var req_id = ''; var req_dtls_id = '';
			for( var i = 0; i < selected_dtls.length; i++ ) {
				req_no += selected_no[i] + ',';
				req_id += selected_id[i] + ',';
				req_dtls_id += selected_dtls[i] + ',';
			}
			req_no = req_no.substr( 0, req_no.length - 1 );
			req_id = req_id.substr( 0, req_id.length - 1 );
			req_dtls_id = req_dtls_id.substr( 0, req_dtls_id.length - 1 );
			$('#hidden_req_no').val(req_no);
			$('#hidden_req_id').val(req_id);
			$('#hidden_req_dtls_id').val(req_dtls_id);
		}
		
		function req_reset()
		{
			reset_form('searchexportinformationfrm','search_div','','','');
			//alert($("#cbo_item_category").val());
		}
    </script>
	</head>
        <body>
            <div align="center" style="width:1100px;">
                <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
                <input type="hidden" name="hidden_req_no" id="hidden_req_no" value="">
                <input type="hidden" name="hidden_req_id" id="hidden_req_id" value="">
                <input type="hidden" name="hidden_req_dtls_id" id="hidden_req_dtls_id" value="">
                    <fieldset style="width:1120px;">
                        <table cellpadding="0" cellspacing="0" width="900" class="rpt_table" border="1" rules="all" align="center">
                            <thead>
                                <tr>
                                    <th width="120" class="must_entry_caption">Company Name</th>
				                    <th width="120" class="must_entry_caption">Buyer Name</th>
				                    <th width="80">Brand</th>
				                    <th width="80">Season</th>
				                    <th width="80">Season Year</th>
				                    <th width="80">Job No</th>
				                    <th width="80">Style Ref</th>
				                    <th width="90">M.Style/Int. Ref</th>
				                    <th width="140" colspan="2">Ship. Date Range</th>
				                    <th width="80">
                                        <input type="button" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" onClick="req_reset()" />
                                    </th>
                                </tr>
                            </thead>                         
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_id,"", 1); ?></td>
				                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --","", "load_drop_down( 'comparative_statement_fabrics_controller', this.value, 'load_drop_down_brand', 'brand_td'); load_drop_down( 'comparative_statement_fabrics_controller', this.value, 'load_drop_down_season', 'season_td')" ); ?></td>
				                <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 80, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
				                <td id="season_td"><? echo create_drop_down( "cbo_season_id", 80, $blank_array,'', 1, "--Season--",$selected, "" ); ?></td>
				                <td><? echo create_drop_down( "cbo_season_year", 80, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
				               
				                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
				                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
				                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
				                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"></td>
				                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"></td>
                                <td>
                                    <input type="button" id="search_button" class="formbutton" value="Show" onClick="fn_show_list();" style="width:80px;" />
                                </td>
                            </tr> 
                            <tr>
                                <td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
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

if($action==='style_list_view')
{
	extract($_REQUEST);
	$data=explode('**', $data);
	$cbo_company_id=$data[0];
	$cbo_buyer_id=$data[1];
	$cbo_brand_id=$data[2];
	$cbo_season_id=$data[3];
	$cbo_season_year=$data[4];
	$txt_job_prifix=$data[5];
	$txt_style=$data[6];
	$txt_internal_ref=$data[7];
	$txt_date_from=$data[8];
	$txt_date_to=$data[9];
	$cbo_year_selection=$data[10];
	$txt_requisition_mst=$data[11];
	$txt_requisition_dtls=$data[12];
	$update_id=$data[13];
	//echo $cbo_brand_id;

	$requisition_dtlsArr = explode(",",$txt_requisition_dtls);
	//echo "<pre>";print_r($requisition_dtlsArr);die;

	/*if($cbo_company_id==0 && $cbo_buyer_id==0)
	{
		echo "<span style='font-weight:bold; text-align:center; color:red; font-size:24px'>Please Select Company or Buyer First.</span>"; die;
	}*/
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where is_deleted=0","id","supplier_name");
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$preCostApprovStatus = array('' =>'No',0 =>'No',1=>'Yes',2=>'No',3=>'Yes');

	$company_cond=$buyer_cond=$brand_cond="";
	$season_cond=$season_year_cond=$job_cond="";
	$style_cond=$internal_ref_cond="";

	if ($cbo_company_id!=0) $company_cond=" and a.company_name='$cbo_company_id'";
	if ($cbo_buyer_id!=0) $buyer_cond=" and a.buyer_name='$cbo_buyer_id'";
	if ($cbo_brand_id!=0) $brand_cond=" and a.brand_id='$cbo_brand_id'";
	if ($cbo_season_id!=0) $season_cond=" and a.season_buyer_wise='$cbo_season_id'";
	if ($cbo_season_year!=0) $season_year_cond=" and a.season_year='$cbo_season_year'";
	if ($txt_job_prifix !="") $job_cond=" and a.job_no_prefix_num like '%$txt_job_prifix%' ";
	if ($txt_style!="") $style_cond=" and a.style_ref_no like '%$txt_style%'";
	if ($txt_internal_ref!="") $internal_ref_cond=" and b.grouping='$txt_internal_ref'";

	$date_cond=$year_cond=$insert_year="";
	if($db_type==0)
	{
		if ($txt_date_from != '' && $txt_date_to != '') $date_cond = "and b.pub_shipment_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year_selection";
		$insert_year="YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		if ($txt_date_from != '' && $txt_date_to != '') $date_cond = "and b.pub_shipment_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
		$insert_year="to_char(a.insert_date,'YYYY')";
	}

	$mst_cond="";
	if ($update_id) $mst_cond=" and id<>$update_id";
	$duplicat_sql="select req_item_dtls_id from req_comparative_mst where entry_form=512 and basis_id=3 and status_active=1 and is_deleted=0 $mst_cond";
	//echo $duplicat_sql."<br>";
	$duplicat_data=sql_select($duplicat_sql);
	$req_dtls_id_arr=array();
	foreach($duplicat_data as $value)
	{
		$req_id_arr=explode(",",$value[csf('req_item_dtls_id')]);
		foreach($req_id_arr as $req_id)
		{
			$req_dtls_id_arr[$req_id]=$req_id;
		}
	}
	unset($duplicat_data);
	
	$sql= "SELECT a.id as JOB_ID, $insert_year as YEAR, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, a.job_no as JOB_NO, a.season_buyer_wise as SEASON, a.brand_id as BRAND_ID, a.company_name as COMPANY_NAME, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, b.grouping as GROUPING, b.shipment_date as SHIPMENT_DATE, c.id as PRECOST_ID, c.approved as APPROVED, d.id as FAB_COST_DTLS_ID, d.color_type_id as COLOR_TYPE_ID, d.fabric_description as DESCRIPTION, d.nominated_supp_multi as NOMINATED_SUPP, d.uom as UOM, d.rate as RATE, d.amount as AMOUNT
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_fabric_cost_dtls d 
	where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.garments_nature=3 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $date_cond $company_cond $buyer_cond $brand_cond $season_cond $season_year_cond $job_cond $style_cond $internal_ref_cond $year_cond order by a.id DESC";
	//echo $sql;die;	
	$sql_res=sql_select($sql);
	$fab_cost_dtlsid_check=array();
	$main_data_arr=array();
	foreach ($sql_res as $row) 
	{
		if ($job_id_check[$row['JOB_ID']] == "")
		{
			$job_id_check[$row['JOB_ID']]=$row['JOB_ID'];
			$job_ids.=$row['JOB_ID'].',';		
		}
		if ($fab_cost_dtlsid_check[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]] == "")
		{
			$fab_cost_dtlsid_check[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]=$row["FAB_COST_DTLS_ID"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['year']=$row["YEAR"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['job_no_prefix_num']=$row["JOB_NO_PREFIX_NUM"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['season']=$row["SEASON"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['brand_id']=$row["BRAND_ID"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['company_name']=$row["COMPANY_NAME"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['buyer_name']=$row["BUYER_NAME"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['style_ref_no']=$row["STYLE_REF_NO"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['grouping'].=$row["GROUPING"].',';
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['shipment_date'].=$row["SHIPMENT_DATE"].',';
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['precost_id']=$row["PRECOST_ID"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['approved']=$row["APPROVED"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['color_type_id']=$row["COLOR_TYPE_ID"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['description']=$row["DESCRIPTION"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['nominated_supp']=$row["NOMINATED_SUPP"];
			$main_data_arr[$row["JOB_ID"]][$row["FAB_COST_DTLS_ID"]]['uom']=$row["UOM"];
		}
		
	}
	//echo "<pre>";print_r($main_data_arr);die;
	$job_ids=rtrim($job_ids);
	//echo $job_ids;die;
	$condition= new condition();
	$condition->jobid_in ("$job_ids");
	$condition->init();
	$fabrics=new fabric($condition);
	//echo $trim->getQuery();die;
	$totalqtyarray_arr=$fabrics->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	//echo "<pre>";print_r($totalqtyarray_arr);die;
	?>
	<style type="text/css">
		.wrd_brk{ word-break: break-all; }
	</style>
	<table width="1120" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="30">SL</th>
			<th width="40">Year</th>
			<th width="45">Job No</th>               
			<th width="50">Company</th>
			<th width="90">Buyer</th>
            <th width="50">Brand</th>
            <th width="50">Season</th>
            <th width="100">Style Ref.</th>
			<th width="90">M.Style/Internal Ref.</th>
			<th width="70">Nominated Supp</th>            
			<th width="70">Color Type</th>        
            <th width="180">Item Description</th>
            <th width="70">Req Qty</th>
			<th width="40">UOM</th>
			<th width="60">Max Ship. Date</th>
            <th>App. Status</th>
        </thead>
    </table>
    <div style="width:1140px; overflow-y:scroll; max-height:280px">
     	<table width="1120" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view_req">
		    <?			
            $i=1; $oldDataRow="";
            foreach($main_data_arr as $job_id => $job_data)
            {
            	foreach($job_data as $fab_cost_dtls_id => $row)
            	{
					if( in_array($fab_cost_dtls_id, $requisition_dtlsArr) )
					{
						if($oldDataRow=="") $oldDataRow = $i; else $oldDataRow .= ",".$i;
					}

					$ex_ship_date=explode(",",rtrim($row['shipment_date'],','));
					$internal_ref=implode(",",array_unique(explode(",",rtrim($row['grouping'],','))));
					$max_ship_date=max($ex_ship_date);
					$req_qty=array_sum($totalqtyarray_arr['woven']['grey'][$fab_cost_dtls_id]);

					$nominated_supplier_name="";
					$nominated_supplier=explode(',',$row['nominated_supp']);
					foreach($nominated_supplier as $supplier_id)
					{
						$nominated_supplier_name.=$supplier_arr[$supplier_id].',';
					}
					
					if ($req_dtls_id_arr[$fab_cost_dtls_id]=="")
					{
						if ($i%2==0) $bgcolor="#FFFFFF";
						else $bgcolor="#E9F3FF";
						?>
	                    <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<?= $i; ?>" onClick="js_set_value(<?= $i; ?>)" >  
	                        <td width="30" align="right"><?= $i; ?>
	                        <input type="hidden" name="txt_mst_no" id="txt_mst_no<?= $i ?>" value="<?= $row['style_ref_no']; ?>"/>
	                        <input type="hidden" name="txt_mst_id" id="txt_mst_id<?= $i ?>" value="<?= $job_id; ?>"/>	
	                        <input type="hidden" name="txt_dtls" id="txt_dtls<?= $i ?>" value="<?= $fab_cost_dtls_id; ?>"/>
	                        <input type="hidden" name="txt_approved" id="txt_approved<?= $i ?>" value="<?= $row['approved']; ?>"/>
	                        </td>
	                        <td width="40" align="right" class="wrd_brk"><p><?= $row['year']; ?></p></td>
	                        <td width="45" align="center" class="wrd_brk"><p><?= $row['job_no_prefix_num']; ?></p></td>
	                        <td width="50" class="wrd_brk"><p><?= $company_arr[$row['company_name']]; ?></p></td>
	                        <td width="90" class="wrd_brk"><p><?= $buyer_arr[$row['buyer_name']]; ?></td>
	                        <td width="50" class="wrd_brk"><p><?= $brand_arr[$row['brand_id']]; ?></p></td>
	                        <td width="50" class="wrd_brk"><p><?= $season_arr[$row['season']]; ?></p></td>
	                        <td align="center" width="100" class="wrd_brk"><p><?= $row['style_ref_no'];?></p></td>
	                        <td width="90" class="wrd_brk"><p><?= $internal_ref; ?></td>
	                        <td width="70" class="wrd_brk"><p><?= rtrim($nominated_supplier_name,','); ?></td>
	                        <td width="70" class="wrd_brk"><p><?= $color_type[$row['color_type_id']]; ?></td>
	                        <td width="180" class="wrd_brk"><p><?= $row['description']; ?></td>
	                        <td width="70" align="right" class="wrd_brk"><p><?= number_format($req_qty,4,".",""); ?></td>
	                        <td width="40" align="center" class="wrd_brk"><p><?= $unit_of_measurement[$row['uom']]; ?></td>
	                        <td width="60" class="wrd_brk"><p><?= change_date_format($max_ship_date); ?></td>
	                        <td class="wrd_brk"><p><?= $preCostApprovStatus[$row['approved']]; ?></td>
	                    </tr>
	                    <?
	                    $i++;
					}
	
				}
            }
			?>
		</table>
    </div>
	<table width="1090" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        <input type="hidden" name="old_data_row_color" id="old_data_row_color" value="<? echo $oldDataRow; ?>"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
	</body>           
	</html>
	<?
	exit();
}
//========== End Demand No/Item ========

if($action=="nominated_supplier_name_popup")
{	
	$data=explode('**',$data);	
	$requisition_dtlsid=$data[0];
	$basis=$data[1];
	$supplier_arr=return_library_array( "select supplier_name,id from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');

	if ($basis==3) {
		$nominated_supp="select a.nominated_supp_multi as supplier_id, a.id from wo_pre_cost_fabric_cost_dtls a where a.id in($requisition_dtlsid) and a.status_active=1 and a.is_deleted=0 ";
	}
	
	$nominated_supp_res=sql_select($nominated_supp);
	$supplier_name='';
	foreach ($nominated_supp_res as $row) {
		if ($row[csf('supplier_id')] != ""){
			$supplier_id.=$row[csf('supplier_id')].',';
			$supplier_name_arr=explode(',',$row[csf('supplier_id')]);			
			foreach($supplier_name_arr as $supp_id){
				$supplier_name.=$supplier_arr[$supp_id].',';
			}			
		}
	}
	$supplier_id=implode(",",array_unique(explode(",",rtrim($supplier_id,','))));
	$supplier_name=implode(",",array_unique(explode(",",rtrim($supplier_name,','))));

	echo "document.getElementById('supplier_id').value = '".$supplier_id."';\n";
	echo "document.getElementById('txt_supplier_name').value = '".$supplier_name."';\n";
	exit();
}

if($action=="supplier_name_popup")
{
	echo load_html_head_contents("Supplier Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r($_REQUEST);
	//echo $party_type_id;
	?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		var selected_id = new Array(); var selected_name = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
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
		function set_all()
		{
			var old=document.getElementById('txt_supplier_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidden_supplier_id').val(id);
			$('#hidden_supplier_name').val(name);
		}
    </script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
		    	<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" class="text_boxes" value="">
		        <input type="hidden" name="hidden_supplier_name" id="hidden_supplier_name" class="text_boxes" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
		                <thead>
		                    <th width="50">SL</th>
		                    <th>Supplier Name</th>
		                </thead>
		            </table>
		            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
		                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
		                <?
		                	                	
                            $data_sql=sql_select("select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type in(9) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name");
                            // var_dump($data_sql);die;
		                    $i=1; $supplier_row_id=''; 
							$hidden_supplier_id=explode(",",$supplier_id);
		                    foreach($data_sql as $row)
		                    {
								
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$id=$row[('ID')];
								if(in_array($id,$hidden_supplier_id)) 
								{ 
									if($supplier_row_id=="") $supplier_row_id=$i; else $supplier_row_id.=",".$i;
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
									<td width="50" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[('ID')]; ?>"/>	
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[('SUPPLIER_NAME')]; ?>"/>
									</td>	
									<td><p><? echo $row[('SUPPLIER_NAME')]; ?></p></td>
								</tr>
								<?
								$i++;

		                    }
		                ?>
		                <input type="hidden" name="txt_supplier_row_id" id="txt_supplier_row_id" value="<?php echo $supplier_row_id; ?>"/>
		                </table>
		            </div>
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
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}

if($action=="style_refno_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r($_REQUEST);
	//echo $party_type_id;
	?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		var selected_id = new Array(); var selected_dtlsid = new Array(); 
		var selected_jobid = new Array(); var selected_name = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
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
		function set_all()
		{
			var old=document.getElementById('txt_job_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}



		function js_set_value( str ) 
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_dtls_id' + str).val(), selected_dtlsid ) == -1 ) {
				selected_dtlsid.push( $('#txt_dtls_id' + str).val() );
				selected_id.push( $('#txt_mst_id' + str).val() );
				selected_jobid.push( $('#txt_job_id' + str).val() );
				selected_name.push( $('#txt_style_no' + str).val() );
			}
			else {
				for( var i = 0; i < selected_dtlsid.length; i++ ) {
					if( selected_dtlsid[i] == $('#txt_dtls_id' + str).val() ) break;
				}
				selected_dtlsid.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_jobid.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var dtlsid=''; var mstid = ''; var jobid=''; var name = '';
			for( var i = 0; i < selected_dtlsid.length; i++ ) {
				dtlsid += selected_dtlsid[i] + ',';
				mstid += selected_id[i] + ',';
				jobid += selected_jobid[i] + ',';
				name += selected_name[i] + ',';
			}
			dtlsid = dtlsid.substr( 0, dtlsid.length - 1 );
			mstid = mstid.substr( 0, mstid.length - 1 );
			jobid = jobid.substr( 0, jobid.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidden_mst_id').val(mstid);
			$('#hidden_dtls_id').val(dtlsid);
			$('#hidden_job_id').val(jobid);
			$('#hidden_style_no').val(name);	
		}
    </script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
		    	<input type="hidden" name="hidden_job_id" id="hidden_job_id" value="">
		        <input type="hidden" name="hidden_style_no" id="hidden_style_no" value="">
		        <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" value="">
		        <input type="hidden" name="hidden_dtls_id" id="hidden_dtls_id" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table width="1220" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
				        <thead>
				            <th width="30">SL</th>
							<th width="40">Year</th>
							<th width="45">Job No</th>               
							<th width="50">Company</th>
							<th width="90">Buyer</th>
				            <th width="50">Brand</th>
				            <th width="50">Season</th>
				            <th width="100">Style Ref.</th>
				            <th width="70">Body/Wash Color</th>
							<th width="90">M.Style/Internal Ref.</th>
							<th width="70">Nominated Supp</th>               
							<th width="70">Main Group</th>
							<th width="90">Items Name</th>
				            <th width="70">Item Description</th>
				            <th width="70">Req Qty</th>
							<th width="55">UOM</th>
							<th width="60">Max Ship. Date</th>
				            <th width="40">App. Sta.</th>
							<th>BOM ID</th>
				        </thead>
				    </table>
		            <div style="width:1220px; overflow-y:scroll; max-height:280px">
				     	<table width="1200" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view_req">
						<?	
							$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
							$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
							$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
							$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
							$color_arr=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
							$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where is_deleted=0","id","supplier_name");
							$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
							$preCostApprovStatus = array('' =>'No',0 =>'No',1=>'Yes',2=>'No',3=>'Yes');
							
							$year_cond=$insert_year="";
							if($db_type==0)
							{
								$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year_selection";
								$insert_year="YEAR(a.insert_date)";
							}
							else if($db_type==2)
							{
								$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
								$insert_year="to_char(a.insert_date,'YYYY')";
							}

							$maing_grp_sql="select id, main_group_name, user_id from lib_main_group where is_deleted=0";
							$maing_grp_sql_result=sql_select($maing_grp_sql);
							$group_wise_user=array();
							foreach($maing_grp_sql_result as $row)
							{
								$main_group_arr[$row[csf("id")]]=$row[csf("main_group_name")];
								$user_id_arr=explode(",",$row[csf("user_id")]);
								foreach($user_id_arr as $u_id)
								{
									if($u_id) $group_wise_user[$u_id][$row[csf("id")]]=$row[csf("id")];
								}
							}

							//echo $demand_mst_id.'**'.$demand_dtls_id;
							//echo "select a.id from scm_demand_dtls a, wo_pre_cost_trim_cost_dtls b where b.id=a.pre_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($demand_dtls_id)";die;
							$sql_demand_res=sql_select("select a.id as demand_dtls_id, a.mst_id as demand_mst_id, a.pre_cost_dtls_id from scm_demand_dtls a, wo_pre_cost_trim_cost_dtls b where b.id=a.pre_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id in($demand_mst_id)");
							foreach ($sql_demand_res as $val) 
							{
								$pre_cost_dtlsid_arr[$val[csf('pre_cost_dtls_id')]]['demand_dtls_id']=$val[csf('demand_dtls_id')];
								$pre_cost_dtlsid_arr[$val[csf('pre_cost_dtls_id')]]['demand_mst_id']=$val[csf('demand_mst_id')];
							}

							$mst_cond="";
							if ($update_id) $mst_cond=" and id<>$update_id";
							$duplicat_data=sql_select("select req_item_dtls_id from req_comparative_mst where entry_form=482 and basis_id=1 and status_active=1 and is_deleted=0 $mst_cond");
							$req_dtls_id_arr=array();
							foreach($duplicat_data as $value)
							{
								$req_id_arr=explode(",",$value[csf('req_item_dtls_id')]);
								foreach($req_id_arr as $req_id)
								{
									$req_dtls_id_arr[$req_id]=$req_id;
								}
							}
							unset($duplicat_data);

					
							$sql= "SELECT a.id as job_id, $insert_year as year, a.job_no_prefix_num, a.job_no, a.season_buyer_wise as season, a.brand_id, a.company_name, a.buyer_name, a.style_ref_no, a.body_wash_color, a.quotation_id, a.job_quantity, b.grouping, b.shipment_date, c.id as precost_id, c.approved, d.id trim_cost_dtls_id, d.brand_sup_ref, d.description, d.nominated_supp_multi as nominated_supp, d.cons_uom as uom, d.rate, d.amount, d.remark, e.id as item_group_id, e.item_name, e.main_group_id
							from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_trim_cost_dtls d, lib_item_group e 
							where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and d.trim_group=e.id and a.garments_nature=3 and a.status_active=1 and b.status_active=1 and a.id in($hidd_job_id) order by a.id DESC";
							//echo $sql;die;	
							$sql_res=sql_select($sql);
							$trim_cost_dtlsid_check=array();
							$main_data_arr=array();
							foreach ($sql_res as $row) 
							{
								if ($job_id_check[$row[csf('job_id')]] == "")
								{
									$job_id_check[$row[csf('job_id')]]=$row[csf('job_id')];
									$job_ids.=$row[csf('job_id')].',';		
								}
								if ($trim_cost_dtlsid_check[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]] == "")
								{
									$trim_cost_dtlsid_check[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]=$row[csf("trim_cost_dtls_id")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['year']=$row[csf("year")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['job_no_prefix_num']=$row[csf("job_no_prefix_num")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['season']=$row[csf("season")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['brand_id']=$row[csf("brand_id")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['company_name']=$row[csf("company_name")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['buyer_name']=$row[csf("buyer_name")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['style_ref_no']=$row[csf("style_ref_no")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['body_wash_color']=$row[csf("body_wash_color")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['grouping'].=$row[csf("grouping")].',';
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['shipment_date'].=$row[csf("shipment_date")].',';
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['precost_id']=$row[csf("precost_id")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['approved']=$row[csf("approved")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['brand_sup_ref']=$row[csf("brand_sup_ref")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['description']=$row[csf("description")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['nominated_supp']=$row[csf("nominated_supp")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['uom']=$row[csf("uom")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['item_group_id']=$row[csf("item_group_id")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['item_name']=$row[csf("item_name")];
									$main_data_arr[$row[csf("job_id")]][$row[csf("trim_cost_dtls_id")]]['main_group_id']=$row[csf("main_group_id")];
								}
								
							}
							$job_ids=rtrim($job_ids);
							//echo '<pre>';print_r($main_data_arr);
							//echo $job_ids;die;
							$condition= new condition();
							$condition->jobid_in ("$job_ids");
							$condition->init();
							$trim=new trims($condition);
							//echo $trim->getQuery();die;
							$totalqtyarray_arr=$trim->getQtyArray_by_precostdtlsid();		
				            $i=1; $oldDataRow="";
				            foreach($main_data_arr as $job_id => $job_data)
				            {
				            	foreach($job_data as $trim_cost_dtls_id => $row)
				            	{
				            		if($group_wise_user[$user_id][$row['main_group_id']]) // User wise permission check in main group
									{
										/*if( in_array($trim_cost_dtls_id, $requisition_dtlsArr) )
										{
											if($oldDataRow=="") $oldDataRow = $i; else $oldDataRow .= ",".$i;
										}*/

										$demand_dtls_id=$pre_cost_dtlsid_arr[$trim_cost_dtls_id]['demand_dtls_id'];
										$demand_mst_id=$pre_cost_dtlsid_arr[$trim_cost_dtls_id]['demand_mst_id'];

										$ex_ship_date=explode(",",rtrim($row['shipment_date'],','));
										$internal_ref=implode(",",array_unique(explode(",",rtrim($row['grouping'],','))));
										$max_ship_date=max($ex_ship_date);					
										if ($req_dtls_id_arr[$demand_dtls_id]=='')
										{
											if ($i%2==0)
						                    $bgcolor="#FFFFFF";
											else
												$bgcolor="#E9F3FF";
											?>
						                    <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<?= $i; ?>" onClick="js_set_value(<?= $i; ?>)" >  
						                        <td width="30" align="right"><?= $i; ?>
						                        <input type="hidden" name="txt_style_no" id="txt_style_no<?= $i ?>" value="<?= $row['style_ref_no']; ?>"/>
						                        <input type="hidden" name="txt_job_id" id="txt_job_id<?= $i ?>" value="<?= $job_id; ?>"/>
						                        <input type="hidden" name="txt_mst_id" id="txt_mst_id<?= $i ?>" value="<?= $demand_mst_id; ?>"/>	
						                        <input type="hidden" name="txt_dtls_id" id="txt_dtls_id<?= $i ?>" value="<?= $demand_dtls_id; ?>"/> 
						                        </td>
						                        <td width="40" align="right" class="wrd_brk"><p><?= $row['year']; ?></p></td>
						                        <td width="45" align="center" class="wrd_brk"><p><?= $row['job_no_prefix_num']; ?></p></td>
						                        <td width="50" class="wrd_brk"><p><?= $company_arr[$row['company_name']]; ?></p></td>
						                        <td width="90" class="wrd_brk"><p><?= $buyer_arr[$row['buyer_name']]; ?></td>
						                        <td width="50" class="wrd_brk"><p><?= $brand_arr[$row['brand_id']]; ?></p></td>
						                        <td width="50" class="wrd_brk"><p><?= $season_arr[$row['season']]; ?></p></td>
						                        <td align="center" width="100" class="wrd_brk"><p><?= $row['style_ref_no'];?></p></td>
						                        <td width="70" class="wrd_brk"><p><?= $color_arr[$row['body_wash_color']]; ?></p></td>
						                        <td width="90" class="wrd_brk"><p><?= $internal_ref; ?></td>
						                        <td width="70" class="wrd_brk"><p><?= $supplier_arr[$row['nominated_supp']]; ?></td>
						                        <td width="70" class="wrd_brk"><p><?= $main_group_arr[$row['main_group_id']]; ?></td>
						                        <td width="90" class="wrd_brk"><p><?= $row['item_name']; ?></td>
						                        <td width="70" class="wrd_brk"><p><?= $row['description']; ?></td>
						                        <td width="70" align="right" class="wrd_brk"><p><?= number_format($totalqtyarray_arr[$trim_cost_dtls_id],4,".",""); ?></td>
						                        <td width="55" class="wrd_brk"><p><?= $unit_of_measurement[$row['uom']]; ?></td>
						                        <td width="60" class="wrd_brk"><p><?= change_date_format($max_ship_date); ?></td>
						                        <td width="40" class="wrd_brk"><p><?= $preCostApprovStatus[$row['approved']]; ?></td>
						                        <td class="wrd_brk"><p><?= $row['precost_id']; ?></p></td>
						                    </tr>
						                    <input type="hidden" name="txt_job_row_id" id="txt_job_row_id" value="<? //echo $oldDataRow; ?>"/>
						                    <?
						                    $i++;
										}
									}	
								}
				            }
							?>
						</table>
				    </div>
				    <table width="1090" cellspacing="0" cellpadding="0" style="border:none" align="center">
						<tr>
							<td align="center" height="30" valign="bottom">
								<div style="width:100%"> 
									<div style="width:50%; float:left" align="left">
										<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
									</div>
									<div style="width:50%; float:left" align="left">
										<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
			                            <input type="hidden" name="old_data_row_color" id="old_data_row_color" value="<? //echo $oldDataRow; ?>"/>
									</div>
								</div>
							</td>
						</tr>
					</table>
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}	

//========== Print Button ========
if($action=="comparative_statement_print")
{
	extract($_REQUEST);
    $data=explode('*',$data);
    $mst_id=$data[0];
	$company_name=$data[2];
	$cbo_template_id=$data[3];
	
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
	//   print_r ($data); die;
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$brand_arr=return_library_array("select id, brand_name from lib_brand where status_active =1 and is_deleted=0",'id','brand_name');
	$team_leader_arr=return_library_array("select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0",'id','team_leader_name');
	$deling_merchant_arr=return_library_array( "select b.id, b.team_member_name from lib_mkt_team_member_info b where b.status_active =1 and b.is_deleted=0",'id','team_member_name');
	$supplier_contact_arr=return_library_array( "select id, contact_no from lib_supplier",'id','contact_no');
	$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$main_group_arr=return_library_array("select id, main_group_name from lib_main_group where status_active=1","id","main_group_name");
	$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1 and item_category=4","id","item_name");
	$group_address=return_field_value("address","lib_group","is_deleted= 0 order by id desc","address");
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$color_arr = return_library_array("select id,color_name from lib_color where is_deleted=0","id","color_name");
	$user_arr = return_library_array("select id, user_name from user_passwd where status_active=1","id","user_name");	

	$data_array=sql_select("SELECT a.id as ID, a.sys_number as SYS_NUMBER, a.sys_number_prefix as SYS_NUMBER_PREFIX, a.sys_number_prefix_num as SYS_NUMBER_PREFIX_NUM, a.basis_id as BASIS_ID, a.req_item_no as REQ_ITEM_NO, a.req_item_mst_id as REQ_ITEM_MST_ID, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.rec_date as REC_DATE, a.cs_date as CS_DATE,  a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.source as SOURCE, a.approved as APPROVED, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.comments as COMMENTS from req_comparative_mst a where a.id='$mst_id' and a.entry_form=512 and a.status_active=1 and a.is_deleted=0");
	$job_ids=$data_array[0]['REQ_ITEM_MST_ID'];
	$pre_cost_fabric_cost_dtls_ids=$data_array[0]['REQ_ITEM_DTLS_ID'];
	$sys_number=$data_array[0]['SYS_NUMBER'];
	$comments=$data_array[0]['COMMENTS'];

	$sql_precost="select a.id as ID, a.gsm_weight as WEIGHT, a.gsm_weight_type as WEIGHT_TYPE, a.nominated_supp_multi as NOMINATED_SUPP_MULTI, b.ITEM_SIZE as CUTABLE_WIDTH from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
	where a.id=b.pre_cost_fabric_cost_dtls_id and a.id in($pre_cost_fabric_cost_dtls_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_precost_res=sql_select($sql_precost);
	$precost_fabric_cost_dtls_arr=array();
	$nominated_supp_multi="";
	foreach ($sql_precost_res as $row){
		$precost_fabric_cost_dtls_arr[$row['ID']]['WEIGHT']=$row['WEIGHT'];
		$precost_fabric_cost_dtls_arr[$row['ID']]['WEIGHT_TYPE']=$row['WEIGHT_TYPE'];
		if ($row['CUTABLE_WIDTH'] != 0){
			$precost_fabric_cost_dtls_arr[$row['ID']]['CUTABLE_WIDTH'].=$row['CUTABLE_WIDTH'].',';
		}
		if ($row['NOMINATED_SUPP_MULTI'] != "")	{
			$nominated_supp_multi.=$row['NOMINATED_SUPP_MULTI'].',';
		}
	}
	$nominated_supp_arr=explode(',',rtrim($nominated_supp_multi,','));

	$sql_contrast_color=sql_select("select PRE_COST_FABRIC_COST_DTLS_ID, CONTRAST_COLOR_ID from WO_PRE_COS_FAB_CO_COLOR_DTLS where PRE_COST_FABRIC_COST_DTLS_ID in($pre_cost_fabric_cost_dtls_ids) and STATUS_ACTIVE=1");
	foreach ($sql_contrast_color as $row)
	{
		$precost_fabric_cost_dtls_arr[$row['PRE_COST_FABRIC_COST_DTLS_ID']]['contrast_color_id'].=$color_arr[$row['CONTRAST_COLOR_ID']].',';
	}

	$sql_order= "SELECT a.id as JOB_ID, a.job_no as JOB_NO, a.style_ref_no as STYLE_REF_NO, a.company_name as COMPANY_ID,  a.brand_id as BRAND_ID, a.buyer_name as BUYER_NAME,  a.team_leader as TEAM_LEADER, a.dealing_marchant as DEALING_MARCHANT, a.avg_unit_price as AVG_UNIT_PRICE, a.job_quantity as JOB_QUANTITY, min(b.pack_handover_date) as PHD_DATE, min(b.po_received_date) as PO_RECEIVED_DATE, min(c.country_ship_date) as COUNTRY_SHIP_DATE
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
	where a.id=b.job_id and b.job_id=c.job_id and a.id in($job_ids) and a.garments_nature=3 and a.status_active=1 and b.status_active=1 and c.status_active=1 
	group by a.id, a.job_no, a.style_ref_no, a.company_name,  a.brand_id, a.buyer_name,  a.team_leader, a.dealing_marchant, a.avg_unit_price, a.job_quantity
	order by a.id";
	$sql_order_res=sql_select($sql_order);
	$po_array=array();
	foreach ($sql_order_res as $row) 
	{
		$company_id=$row['COMPANY_ID'];
		if ($check_job_id[$row['JOB_ID']]==""){
			$check_job_id[$row['JOB_ID']]=$row['JOB_ID'];
			$buyer_name.=$buyer_arr[$row['BUYER_NAME']].',';
			$brand_name.=$brand_arr[$row['BRAND_ID']].',';
			$team_leader_name.=$team_leader_arr[$row['TEAM_LEADER']].',';
			$deling_merchant_name.=$deling_merchant_arr[$row['DEALING_MARCHANT']].',';
			//$merchandiser.=$team_leader_arr[$row['TEAM_LEADER']].'/ '.$deling_merchant_arr[$row['DEALING_MARCHANT']].', ';
			if ($row['AVG_UNIT_PRICE'] != "") $avg_unit_price.='$'.$row['AVG_UNIT_PRICE'].' Pcs, ';		
			$job_quantity.=$row['JOB_QUANTITY'].' Pcs, ';
			$style_ref_no.=$row['STYLE_REF_NO'].', ';
			$job_no.=$row['JOB_NO'].', ';
			$job_id.=$row['JOB_ID'].', ';
			$phd_date_calculation=date('d-M-y', strtotime($row['PHD_DATE'] .' -7 day'));
			$require_del_date.=date("d-m-Y", strtotime($phd_date_calculation)).', ';
			$order_confirm_date.=date("d-m-Y", strtotime($row['PO_RECEIVED_DATE'])).', ';
			$country_ship_date.=date("d-m-Y", strtotime($row['COUNTRY_SHIP_DATE'])).', ';
		}
	}

	$buyer_names = implode(', ',array_unique(explode(',',chop($buyer_name,','))));
	$brand_names = implode(', ',array_unique(explode(',',chop($brand_name,','))));
	$team_leader_names = implode(', ',array_unique(explode(',',chop($team_leader_name,','))));
	$deling_merchant_names = implode(', ',array_unique(explode(',',chop($deling_merchant_name,','))));
	if ($team_leader_names==$deling_merchant_names || $deling_merchant_names=="") $merchandiser=$team_leader_names;
	else $merchandiser=$team_leader_names.'/ '.$deling_merchant_names;

	if ($brand_names == "") $buyer_brand_names=$buyer_names;
	else $buyer_brand_names=$buyer_names.'/ '.$brand_names;
	$job_ids=rtrim($job_id, ', ');

	if ($job_ids != "")
	{
		$sql_appr=sql_select("select b.approved_date as APPROVED_DATE from wo_pre_cost_mst a, approval_history b where a.id=b.mst_id and b.entry_form=15 and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.job_id in($job_ids)");
		$approved_date="";
		foreach($sql_appr as $row){
			$approved_date.=date("d-m-Y", strtotime($row['APPROVED_DATE'])).', ';
		}
	}

	$group_id = return_field_value("group_id", "lib_company", "status_active=1 and is_deleted=0 and id=$company_name", "group_id");
	$comp_logo = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='group_logo' and is_deleted=0 and master_tble_id='$group_id'", "image_location");
	?>
	<div>
    <table cellspacing="0" cellpadding="0" width="1300">
    	<tr>
    		<td align="left"><img src="../../<? echo $comp_logo; ?>" height="50" width="100"></td>
            <td colspan="5" style="font-size:xx-large;" align="center"><strong>Competitive Price Comparison for Fabric</strong></td>
        </tr>
        <tr>
			<td align="left" width='180' valign="top"><strong>Job No</strong></td>
            <td align="left" width='10' valign="top"><strong>:</strong></td>
            <td align="left" width='400'><? echo rtrim($job_no,', '); ?></td>            
			<td align="left" width='180'><strong>Order Confirm. Date</strong></td>
            <td align="left" width='10'><strong>:</strong></td>
            <td align="left" ><? echo rtrim($order_confirm_date, ', '); ?></td>
        </tr>
        <tr>
			<td align="left" width='180'><strong>CS Number</strong></td>
            <td align="left" width='10'><strong>:</strong></td>
            <td align="left" width='400'><? echo $data_array[0]['SYS_NUMBER']; ?></td>
			<td align="left" width='180'><strong>Merchant .Forw. Date</strong></td>
            <td align="left" width='10'><strong>:</strong></td>
            <td align="left"><? echo rtrim($approved_date,', '); ?></td>
        </tr>
        <tr>
			<td align="left" width='180'><strong>CS Date</strong></td>
            <td align="left" width='10'><strong>:</strong></td>
            <td align="left" width='400'><? echo change_date_format($data_array[0]['CS_DATE']); ?></td> 
			<td align="left" width='180'><strong>Require Del Date</strong></td>
            <td align="left" width='10'><strong>:</strong></td>
            <td align="left"><? echo rtrim($require_del_date,', '); ?></td>          
            
        </tr>
        <tr>
			<td align="left" width='180' valign="top"><strong>CS Validity Date</strong></td>
            <td align="left" width='10' valign="top"><strong>:</strong></td>
            <td align="left" width='400'valign="top"><? echo change_date_format($data_array[0]['CS_VALID_DATE']); ?></td>           
            <td align="left" width='180'><strong>Garment Ship Start Date</strong></td>
            <td align="left" width='10'><strong>:</strong></td>
            <td align="left"><? echo rtrim($country_ship_date, ', '); ?></td>
        </tr>
        <tr>
			<td align="left" width='180' valign="top"><strong>Buyer/Brand Name</strong></td>
            <td align="left" width='10' valign="top"><strong>:</strong></td>
            <td align="left" width='400'><? echo $buyer_brand_names; ?></td>            
            <td align="left" width='180'><strong>GMT. Style Qty</strong></td>
            <td align="left" width='10'><strong>:</strong></td>
            <td align="left"><? echo rtrim($job_quantity,', '); ?></td>
        </tr>
        <tr>
			<td align="left" width='180' valign="top"><strong>Style Name</strong></td>
            <td align="left" width='10' valign="top"><strong>:</strong></td>
            <td align="left" width='400'><? echo rtrim($style_ref_no,', '); ?></td>
			<td align="left" width='180'><strong>Merchandiser</strong></td>
            <td align="left" width='10'><strong>:</strong></td>
            <td align="left"><? echo $merchandiser; ?></td>
        </tr>
	</table>
	<br>
	<?	
	$dtls_sql="SELECT id as ID, item_category_id as ITEM_CATEGORY_ID, fabric_cost_dtls_id as FABRIC_COST_DTLS_ID, item_description as ITEM_DESCRIPTION, mill_reff as MILL_REFF, weight as WEIGHT, req_qty as REQ_QTY, UOM, req_rate as REQ_RATE, req_amount as REQ_AMOUNT 
	from req_comparative_dtls
	where mst_id='$mst_id' and is_deleted=0 and status_active=1
	order by ID";
	$dtls_sql_result=sql_select($dtls_sql);

	$supp_dtls_arr=sql_select("SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, main_group_id as MAIN_GROUP_ID, item_group_id as ITEM_GROUP_ID, brand_supplier as BRAND_SUPPLIER, item_description as ITEM_DESCRIPTION, uom as UOM, quoted_price as QUOTED_PRICE, neg_price as NEG_PRICE, con_price as CON_PRICE, allocated_qty as ALLOCATED_QTY, last_approval_rate as LAST_APPROVAL_RATE, pay_term as PAY_TERM, tenor as TENOR, cutable_width as CUTABLE_WIDTH, ship_mode as SHIP_MODE, source as SOURCE, is_recommend as IS_RECOMMEND, origin_id as ORIGIN_ID, lc_type from req_comparative_supp_dtls where mst_id='$mst_id' and is_deleted=0 and status_active=1 and supp_type=1 order by DTLS_ID, ID");
	$supp_payterm_arr=array();
	$supp_recommend_arr=array();
	$supp_last_approval_rate_arr=array();
	$pay_term_arr=array();
	$lc_type_arr=array(); $lc_type= array( 4 => "TT/Pay Order", 5 => "FTT", 6 => "FDD/RTGS");
	$cutable_width_arr=array();
	foreach ($supp_dtls_arr as $val) {
		if ($check_supplier_arr[$val['SUPP_ID']]==""){
			$check_supplier_arr[$val['SUPP_ID']]=$val['SUPP_ID'];
			$supp_payterm_arr[$val['SUPP_ID']]['PAY_TERM']=$val['PAY_TERM'];
			$supp_payterm_arr[$val['SUPP_ID']]['TENOR']=$val['TENOR'];
			$supp_payterm_arr[$val['SUPP_ID']]['CUTABLE_WIDTH']=$val['CUTABLE_WIDTH'];
			$supp_payterm_arr[$val['SUPP_ID']]['SHIP_MODE']=$val['SHIP_MODE'];
			$supp_payterm_arr[$val['SUPP_ID']]['SOURCE']=$val['SOURCE'];	
			$supp_payterm_arr[$val['SUPP_ID']]['ORIGIN_ID']=$val['ORIGIN_ID'];		
			$supp_recommend_arr[$val['SUPP_ID']]['IS_RECOMMEND']=$val['IS_RECOMMEND'];	
		}		
		$supp_last_approval_rate_arr[$val['DTLS_ID']][$val['SUPP_ID']]['LAST_APPROVAL_RATE']=$val['LAST_APPROVAL_RATE'];

		if ($val['CUTABLE_WIDTH'] != "") $cutable_width_arr[$val['DTLS_ID']].=$val['CUTABLE_WIDTH'].'",';
		if ($val['PAY_TERM'] == 2) $pay_term_arr[$val['DTLS_ID']].="LC ".$val['TENOR']." Days".',';
		else $pay_term_arr[$val['DTLS_ID']].=$pay_term[$val['PAY_TERM']].',';
		$lc_type_arr[$val['DTLS_ID']].=$lc_type[$val['LC_TYPE']].",";
	}

	$com_dtls_arr=sql_select("SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, main_group_id as MAIN_GROUP_ID, item_group_id as ITEM_GROUP_ID, brand_supplier as BRAND_SUPPLIER, item_description as ITEM_DESCRIPTION, uom as UOM, quoted_price as QUOTED_PRICE, neg_price as NEG_PRICE, con_price as CON_PRICE, allocated_qty as ALLOCATED_QTY, last_approval_rate as LAST_APPROVAL_RATE, pay_term as PAY_TERM, tenor as TENOR, cutable_width as CUTABLE_WIDTH, ship_mode as SHIP_MODE, source as SOURCE, is_recommend as IS_RECOMMEND, origin_id as ORIGIN_ID from req_comparative_supp_dtls where mst_id='$mst_id' and is_deleted=0 and status_active=1 and supp_type=2 order by DTLS_ID, ID");
	$com_payterm_arr=array();
	$com_recommend_arr=array();
	$com_last_approval_rate_arr=array();
	foreach ($com_dtls_arr as $val) {
		if ($check_company_arr[$val['SUPP_ID']]==""){
			$check_company_arr[$val['SUPP_ID']]=$val['SUPP_ID'];
			//$com_payterm_arr[$val['SUPP_ID']]['PAY_TERM']=$val['PAY_TERM'];
			$com_payterm_arr[$val['SUPP_ID']]['TENOR']=$val['TENOR'];
			//$com_payterm_arr[$val['SUPP_ID']]['CUTABLE_WIDTH']=$val['CUTABLE_WIDTH'];
			$com_payterm_arr[$val['SUPP_ID']]['SHIP_MODE']=$val['SHIP_MODE'];
			$com_payterm_arr[$val['SUPP_ID']]['SOURCE']=$val['SOURCE'];
			$com_payterm_arr[$val['SUPP_ID']]['ORIGIN_ID']=$val['ORIGIN_ID'];
			$com_recommend_arr[$val['SUPP_ID']]['IS_RECOMMEND']=$val['IS_RECOMMEND'];					
		}
		$com_last_approval_rate_arr[$val['DTLS_ID']][$val['SUPP_ID']]['LAST_APPROVAL_RATE']=$val['LAST_APPROVAL_RATE'];
		if ($val['CUTABLE_WIDTH'] != "") $cutable_width_arr[$val['DTLS_ID']].=$val['CUTABLE_WIDTH'].'",';
		if ($val['PAY_TERM'] == 2) $pay_term_arr[$val['DTLS_ID']].="LC ".$val['TENOR']." Days".',';
		else $pay_term_arr[$val['DTLS_ID']].=$pay_term[$val['PAY_TERM']].',';
	}
	//echo '<pre>';print_r($pay_term_arr);

	

	$is_company=$data_array[0]['COMPANY_ID'];
	$company_all=explode(',',$data_array[0]['COMPANY_ID']);
	if($is_company!=''){$company_width=(count($company_all)*240);}else{$company_width=0;}

	$is_supplier=$data_array[0]['SUPP_ID'];
	$supplier_all=explode(',',$data_array[0]['SUPP_ID']);
	if($is_supplier!=''){$supplier_width=(count($supplier_all)*240);}else{$supplier_width=0;}

	$supplier_count=count($supp_dtls_arr);
	$tbl_width=1030+$supplier_width+$company_width;

	?>
    <table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th rowspan="2" width="200" align="center">Item Description</th>
				<th rowspan="2" width="80" align="center">Fab. Ref#</th>
				<th rowspan="2" width="80" align="center">Weight (BforeWash)</th>
				<th rowspan="2" width="80" align="center">Cut Width</th>
				<th rowspan="2" width="80" align="center" title="Contrast Color">Color</th>
				<th rowspan="2" width="80" align="center">L/C / Payment Term</th>
				<th rowspan="2" width="80" align="center">Order Qty</th>
				<th rowspan="2" width="50" align="center">UOM</th>
				<th rowspan="2" width="80" align="center">Costing Prc</th>
				<th rowspan="2" width="80" align="center">Costing Amount</th>
				<?
				if($is_company!='')
				{
					foreach($company_all as $company_id)
					{
						?>
						<th colspan="3" width="240"><?= $lib_company_arr[$company_id];?></th>
						<?
					}
				}
				
				foreach($supplier_all as $supplier_id)
				{
					if ($supp_recommend_arr[$supplier_id]['IS_RECOMMEND']==1) $nominated_supplier="[Recommend]";
					else if (in_array($supplier_id,$nominated_supp_arr)) $nominated_supplier="[Nominated]";
					else $nominated_supplier="";					
					?>
					<th colspan="3" width="240"><?= $supplier_arr[$supplier_id].'&nbsp;&nbsp;&nbsp;'.$nominated_supplier;?></th>
					<?
				}
				?>
				<th rowspan="2" align="center">Remarks</th>
			</tr>
			<tr>
				<?
					if($is_company!='')
					{
						foreach($company_all as $row)
						{
							?>
							<th width="80">Qtd Prc</th>
							<th width="80">Ngtd. Prc</th>
							<th width="80">T. Amount USD</th>
							<?
						}
					}
					foreach($supplier_all as $row)
					{
						?>
						<th width="80">Qtd Prc</th>
						<th width="80">Ngtd. Prc</th>
						<th width="80">T. Amount USD</th>
						<?
					}
				?>				
			</tr>
			<tr>
				<th width="200" align="center"><strong>COO/Ship Mode</strong></th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<?
					if($is_company!='')
					{
						foreach($company_all as $row)
						{
							?>
							<th colspan="3" width="240">
								<?
								$origin_id=$com_payterm_arr[$row]['ORIGIN_ID'];
								$com_ship_mode=$shipment_mode[$com_payterm_arr[$val['SUPP_ID']]['SHIP_MODE']];
								if ($com_ship_mode != "") echo $country_arr[$origin_id].' ('.$com_ship_mode.')';
								else echo $country_arr[$origin_id];
								?>
							</th>
							<?
						}
					}

					foreach($supplier_all as $row)
					{
						?>
						<th colspan="3" width="240">
							<?
							$origin_id=$supp_payterm_arr[$row]['ORIGIN_ID'];
							$sup_ship_mode=$shipment_mode[$supp_payterm_arr[$row]['SHIP_MODE']];
							if ($sup_ship_mode != "") echo $country_arr[$origin_id].' ('.$sup_ship_mode.')';
							else echo $country_arr[$origin_id];
							?>
						</th>
						<?
					}					
				?>
				<th>&nbsp;</th>
			</tr>					
		</thead>
		<tbody>
			<?
				$i=1;
				$grand_total_com=array();
				$grand_total_supp=array();
				foreach($dtls_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					//$item_description=explode(",",  $row['ITEM_DESCRIPTION']);
					$item_description=$row['ITEM_DESCRIPTION'];
					if ($precost_fabric_cost_dtls_arr[$row['FABRIC_COST_DTLS_ID']]['WEIGHT_TYPE']==1){
						$weight_before_wash=$precost_fabric_cost_dtls_arr[$row['FABRIC_COST_DTLS_ID']]['WEIGHT'].' OZ';
					}else {
						$weight_before_wash=$precost_fabric_cost_dtls_arr[$row['FABRIC_COST_DTLS_ID']]['WEIGHT'].' GSM';
					}

					$cutable_width = implode(', ',array_unique(explode(',', rtrim($cutable_width_arr[$row['ID']],','))));
					$contrast_color = implode(', ',array_unique(explode(',', rtrim($precost_fabric_cost_dtls_arr[$row['FABRIC_COST_DTLS_ID']]['contrast_color_id'],','))));
					$pay_term_val = implode(', ',array_unique(explode(',', rtrim($pay_term_arr[$row['ID']],','))));
					$pay_term_val = explode(",",$pay_term_val);
					if(count($pay_term_val)>1){
						$pay_term_val="";
					}
					else{
						$pay_term_val=implode(",",$pay_term_val);
					}
					$lc_val = implode(', ',array_unique(explode(',', rtrim($lc_type_arr[$row['ID']],','))));
					$lc_val = explode(",",$lc_val);
					if(count($lc_val)>1){
						$lc_val="";
					}
					else{
						$lc_val=implode(",",$lc_val);
					}
					?>
					<tr bgcolor="<?= $bgcolor; ?>">
						<td><? echo $item_description; ?></td>						
						<td><? echo $row['MILL_REFF']; ?></td>
						<td align="right"><? echo $weight_before_wash; ?></td>
						<td align="center"><? echo $cutable_width; ?></td>
						<td align="center"><? echo $contrast_color; ?></td>
						<td><? 
							if($pay_term_val!="" && $lc_val!=""){
								echo $lc_val." / ";		
							}
							else{
								echo $lc_val;
							}
						
						echo $pay_term_val; ?></td>					
                        <td align="right"><? if ($row['REQ_QTY'] != "") echo number_format($row["REQ_QTY"],4); ?></td>
						<td align="center"><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
                        <td align="right"><? if ($row['REQ_RATE'] != "") echo '$'.number_format($row["REQ_RATE"],2); ?></td>
                        <td align="right"><? if ($row['REQ_AMOUNT'] != "") echo '$'.number_format($row["REQ_AMOUNT"],4); ?></td>
						<?
						$row_id=$row['ID'];
						if($is_company!='')
						{
							foreach($company_all as $com_nam)
							{
								foreach($com_dtls_arr as $val)
								{
									$dtls_row_id=$val["DTLS_ID"];
									$com_id=$val["SUPP_ID"];
									if($row_id==$dtls_row_id)
									{
										if($com_nam==$com_id)
										{
											$com_neg_price=$val["NEG_PRICE"];
											if ($com_last_approval_rate_arr[$dtls_row_id][$com_id]['LAST_APPROVAL_RATE'] !='') $com_neg_price=$com_last_approval_rate_arr[$dtls_row_id][$com_id]['LAST_APPROVAL_RATE'];
											?>
											<td align="right"><? if ($val["QUOTED_PRICE"] !='') echo '$'.number_format($val["QUOTED_PRICE"],2); ?></td>
											<td align="right">
												<? echo '$'.number_format($com_neg_price,2); ?>
											</td>
											<td align="right"><? echo '$'.number_format(($row["REQ_QTY"]*$com_neg_price),4); ?></td>
											<?
											$grand_total_com[$com_id]+=$row["REQ_QTY"]*$com_neg_price;
										}
									}
								}
							}
						}
						foreach($supplier_all as $supp)
						{
							foreach($supp_dtls_arr as $rows)
							{
								$dtls_row_id=$rows["DTLS_ID"];
								$supp_id=$rows["SUPP_ID"];
								if($row_id==$dtls_row_id)
								{
									if($supp==$supp_id)
									{
										$supp_neg_price=$val["NEG_PRICE"];
										if ($supp_last_approval_rate_arr[$dtls_row_id][$supp_id]['LAST_APPROVAL_RATE'] !='') $supp_neg_price=$supp_last_approval_rate_arr[$dtls_row_id][$supp_id]['LAST_APPROVAL_RATE'];
										?>
										<td align="right"><? if ($val["QUOTED_PRICE"] !='') echo '$'.number_format($rows["QUOTED_PRICE"],2); ?></td>
										<td align="right">
											<? echo '$'.number_format($supp_neg_price,2); ?>
										</td>
                                        <td align="right"><? echo '$'.number_format(($row["REQ_QTY"]*$supp_neg_price),4); ?></td>
										<?
										$grand_total_supp[$supp_id] += $row["REQ_QTY"]*$supp_neg_price;
									}
								}
							}
						}
						if ($i==1) { ?>
							<td rowspan="<? echo count($dtls_sql_result); ?>"><? echo $comments; ?></td>
						<? } ?>
					</tr>
					<?
					$i++;
					$total_req_amount+=$row['REQ_AMOUNT'];
				}
			?>
			<tr style="height: 30px;">
				<td colspan='9' align="right"><strong>Total&nbsp;</strong></td>
				<td align="right"><strong><span style="border-bottom: 4px double;"><? echo '$'.number_format($total_req_amount,4); ?></span></strong></td>
				<?
				if($is_company!='')
				{
					foreach($grand_total_com as $total_com)
					{
						?>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80" align="right"><strong><span style="border-bottom: 4px double;"><? echo '$'.number_format($total_com,4); ?></span></strong></th>
						<?
					}
				}
				foreach($grand_total_supp as $total_supp)
				{
					?>
					<th width="80">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="80" align="right"><strong><span style="border-bottom: 4px double;"><? echo '$'.number_format($total_supp,4); ?></span></strong></th>
					<?
				}
				?>				
				<td>&nbsp;</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table width="1000" cellpadding="0" cellspacing="0" border="0" rules="all" class="rpt_table">
		<tr>
			<th align="left" style="font-size: 18px;">Approval Comments:</th>
		</tr>
	</table>
	<table  width="100%" class="rpt_table"  cellpadding="0" cellspacing="0">
		<?
		$data_array = sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no='".str_replace("'", "", $sys_number)."' and entry_form=512 order by id");
		if (count($data_array) > 0) 
		{
			$i=1;
			foreach ($data_array as $row)
			{
				?>
				<tr>
					<td width="100%"><? echo $i.'.'.$row[csf('terms')]; ?></td>
				</tr>
				<?
				$i++;
			}
		}
		?>
	</table>
	
	<!-- <table width="1200" cellpadding="0" cellspacing="0" style="padding-top: 150px; padding-bottom: 30px;">
		<tr>
			<th width="200" style="margin-right: 20px; text-decoration:overline">Prepared By (Sourcing)</th>
			<th width="200" style="margin-right: 20px; text-decoration:overline">Merchant Team Leader</th>
			<th width="200" style="margin-right: 20px; text-decoration:overline">Sourcing Team Leader</th>
			<th width="200" style="margin-right: 20px; text-decoration:overline">Director- Sourcing & SCM</th>
			<th width="200" style="margin-right: 20px; text-decoration:overline">C.E.O</th>
			<th width="200" style="margin-right: 20px; text-decoration:overline">Approved By Managing Director</th>
		</tr>
	</table> -->
	</div>
	<?
	echo signature_table(512, 0,"1500px",$cbo_template_id,40);
	?>
	<?
	exit();
}