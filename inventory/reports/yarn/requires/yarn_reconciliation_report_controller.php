<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
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
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_job_no_search_list_view', 'search_div', 'yarn_reconciliation_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no_prefix_num";
	//$year="year(insert_date)";
	if($db_type==0) $year_field=" YEAR(insert_date) as year";
	else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
	else $year_field="";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field  from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;

   exit();
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
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
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
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
                    <th id="search_by_td_up" width="170">Please Enter Booking No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
                       		$search_by_arr=array(1=>"Service WO No",2=>"Job No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_booking_no_search_list_view', 'search_div', 'yarn_reconciliation_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>

            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();
}

if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_id=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="job_no"; else $search_field="booking_no_prefix_num";
	//$year="year(insert_date)";
	if($db_type==0) $year_field=" YEAR(insert_date) as year";
	else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
	else $year_field="";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, booking_no_prefix_num, company_id, buyer_id, booking_no, $year_field  from wo_booking_mst where status_active=1 and is_deleted=0 and booking_type=3 and company_id=$company_id and $search_field like '$search_string' $buyer_id_cond order by job_no";
	//echo $sql;die;
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Booking No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "company_id,buyer_id,0,0,0", $arr , "company_id,buyer_id,job_no_prefix_num,year,booking_no_prefix_num", "",'','0,0,0,0,0','',1) ;

   exit();
}

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../../", 1, 1,'','','');
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
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	if ($cbo_knitting_source==3)
	{
		$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(1,9,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
	}
	elseif($cbo_knitting_source==1)
	{
		$sql="select id, company_name as party_name from lib_company where status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;

   exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	//$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	//$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	//$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	//$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	//echo $from_date."=".$to_date;die;
	$cbo_value_with=str_replace("'","",$cbo_value_with);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
	$txt_challan=str_replace("'","",$txt_challan);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$cbo_po_status=str_replace("'","",$cbo_po_status);

	$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');

	$type=str_replace("'","",$type);

	if($type==1)
	{
		if($txt_job_id!="" && $cbo_search_type==2) $book_cond=" and a.id in($txt_job_id)";
		$dataBookArray=sql_select("select a.booking_no as BOOKING_NO, b.program_no as PROGRAM_NO
		from wo_booking_mst a, wo_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type=3 and b.booking_type=3 and a.company_id=$cbo_company_name $book_cond");
		if($txt_job_id!="" && $cbo_search_type==2 && count($dataBookArray)<1)
		{
			echo "10####<p style=\"margin-top:20px; padding-top:10px; font-weight:bold; font-size:16px; color:red;\"></p>";die;
		}

		$all_prog_id=array();
		foreach($dataBookArray as $row)
		{
			if($txt_job_id!="" && $cbo_search_type==2) $all_prog_id[$row['PROGRAM_NO']]=$row['PROGRAM_NO'];
			$service_book_arr[$row['PROGRAM_NO']]['BOOKING_NO']=$row['BOOKING_NO'];
		}
		unset($dataBookArray);

		$product_arr=array();
		$sql_prod="select id as ID, product_name_details as PRODUCT_NAME_DETAILS, lot as LOT, brand as BRAND from product_details_master where item_category_id in (1,13) ";
		$sql_prod_res=sql_select($sql_prod);
		foreach($sql_prod_res as $rowp)
		{
			$product_arr[$rowp['ID']]['PRODUCT_NAME_DETAILS']=$rowp['PRODUCT_NAME_DETAILS'];
			$product_arr[$rowp['ID']]['LOT']=$rowp['LOT'];
			//$product_arr[$rowp['ID']]['BRAND']=$rowp['BRAND'];
		}
		unset($sql_prod_res);
		//echo $sql_prod;die;
		$yarn_prod_sql=sql_select("select a.id, b.brand_name from product_details_master a, lib_brand b where a.brand = b.id and a.item_category_id = 1 and a.company_id=$cbo_company_name");
		// $prodids_cond
		foreach($yarn_prod_sql as $row)
		{
			if($duplicate_chk[$row[csf('id')]] =='')
			{
				$duplicate_chk[$row[csf('id')]]=$row[csf('id')];
				$yarn_brand_ref[$row[csf('id')]] .=$row[csf('brand_name')];
			}
		}
		unset($yarn_prod_sql);

		$sql_prog=sql_select("select b.id as PROG_NO, a.booking_no as BOOKING_NO from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql_prog as $row)
		{
			$prog_book_arr[$row['PROG_NO']]=$row['BOOKING_NO'];
		}
		unset($sql_prog);

		$sql_cond_issue="";
		if($txt_challan !="") $sql_cond_issue.=" and a.issue_number_prefix_num=$txt_challan";
		if($from_date !="" && $to_date !="" && count($all_trans_id)<1 && count($all_prog_id)<1) $sql_cond_issue.=" and b.transaction_date between '$from_date' and '$to_date'";
		if($cbo_knitting_source > 0) $sql_cond_issue.=" and a.knit_dye_source=$cbo_knitting_source";
		if($txt_knitting_com_id !="") $sql_cond_issue.=" and a.knit_dye_company in ($txt_knitting_com_id)";
		if(count($all_trans_id)>0) $sql_cond_issue.=" and b.id in (".implode(",",$all_trans_id).")";
		if(count($all_prog_id)>0) $sql_cond_issue.=" and c.knit_id in (".implode(",",$all_prog_id).")";

		$sql_issue="select a.id as MST_ID, a.issue_number as ISSUE_NUMBER, a.issue_number_prefix_num as ISSUE_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.issue_date as ISSUE_DATE, a.challan_no as CHALLAN_NO, a.issue_basis as ISSUE_BASIS, a.knit_dye_source as KNIT_DYE_SOURCE, a.knit_dye_company as KNIT_DYE_COMPANY, b.id as TRANS_ID, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE, b.cons_uom as CONS_UOM, b.requisition_no as REQUISITION_NO, b.brand_id as BRAND_ID, b.cons_quantity as CONS_QUANTITY, b.return_qnty as RETURN_QNTY, c.knit_id as PROGRAM_NO,d.po_breakdown_id as PO_BREAKDOWN_ID
		from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c ,order_wise_pro_details d
		where a.id=b.mst_id and b.requisition_no=c.requisition_no and b.prod_id=c.prod_id and b.id=d.trans_id and b.prod_id=d.prod_id and a.entry_form=3 and a.issue_basis=3 and a.issue_purpose=1 and b.receive_basis=3 and a.company_id=$cbo_company_name and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond_issue";
		//echo  $sql_issue;die;
		$issue_result=sql_select($sql_issue);
		$details_data=array();
		$prod_id_arr=array();
		$r_id3=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
		if($r_id3)oci_commit($con);
		$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
		if($temp_table_id=="") $temp_table_id=1;
		if(count($issue_result)<1){echo "10####<p style=\"margin-top:20px; padding-top:10px; font-weight:bold; font-size:16px; color:red;\"></p>";die;}

		foreach($issue_result as $row)
		{
			$all_po_id[$row[csf("PO_BREAKDOWN_ID")]]=$row[csf("PO_BREAKDOWN_ID")];

			$prod_id_arr[$row[csf("PROGRAM_NO")]].=$row[csf("PROD_ID")].',';
		}

		$po_id_cond="";
		if(count($all_po_id)>0)
		{
			$all_po_id_arr=array_chunk($all_po_id,999);

			$po_id_cond.=" and (";
			foreach($all_po_id_arr as $po_id)
			{
				if($po_id_cond==" and (") $po_id_cond.=" c.po_breakdown_id in(".implode(",",$po_id).") "; else $po_id_cond.=" and c.po_breakdown_id in(".implode(",",$po_id).") ";
			}
			$po_id_cond.=")";
		}

		$po_arr=array();

		if($txt_job_id!="" && $cbo_search_type==1) $job_cond=" and a.id in($txt_job_id)";

		if($po_id_cond!="")
		{
			$datapoArray=sql_select("select a.buyer_name as BUYER_NAME, a.job_no as JOB_NO, b.id as PO_ID, b.po_number as PO_NUMBER, c.trans_id as TRANS_ID from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c
			where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.entry_form in(2,3,9,22,58) and b.status_active=$cbo_po_status and a.company_name=$cbo_company_name $po_id_cond $job_cond");

			if($txt_job_id!="" && $cbo_search_type==1 && count($datapoArray)<1)
			{
				echo "10####<p style=\"margin-top:20px; padding-top:10px; font-weight:bold; font-size:16px; color:red;\">NO DATA FOUND</p>";die;
			}

			$all_trans_id=array();
			foreach($datapoArray as $row)
			{
				if($txt_job_id!="" && $cbo_search_type==1) $all_trans_id[$row['TRANS_ID']]=$row['TRANS_ID'];
				$po_arr[$row['TRANS_ID']]['job_no']=$row['JOB_NO'];
				$po_arr[$row['TRANS_ID']]['po_number']=$row['PO_NUMBER'];
				$po_arr[$row['TRANS_ID']]['buyer']=$row['BUYER_NAME'];
			}
			unset($datapoArray);
		}

		foreach($issue_result as $row)
		{
			if($ref_check[$row["PROGRAM_NO"]]=="")
			{
				$ref_check[$row["PROGRAM_NO"]]=$row["PROGRAM_NO"];
				$r_id=execute_query("insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row["PROGRAM_NO"].",15,$user_id,'".$row["BOOKING_NO"]."')");
				if($r_id) $r_id=1; else {echo "insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row["PROGRAM_NO"].",15,$user_id,'".$row["BOOKING_NO"]."')";oci_rollback($con);die;}
				$temp_table_id++;
			}

			if($trans_check[$row["MST_ID"]][$row['TRANS_ID']]=="")
			{
				$trans_check[$row["MST_ID"]][$row['TRANS_ID']]=$row['TRANS_ID'];

				if($po_arr[$row['TRANS_ID']]['job_no'])
				{
					$knit_party=$row["KNIT_DYE_SOURCE"]."*".$row["KNIT_DYE_COMPANY"];
					$mst_prod_id=$row["MST_ID"]."*".$row["PROD_ID"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["RCV_ISSUE_NUMBER"]=$row["ISSUE_NUMBER"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["ISSUE_NUMBER_PREFIX_NUM"]=$row["ISSUE_NUMBER_PREFIX_NUM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["ENTRY_FORM"]=$row["ENTRY_FORM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["BUYER_ID"]=$row["BUYER_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["BOOKING_NO"]=$row["BOOKING_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["ISSUE_DATE"]=$row["ISSUE_DATE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["CHALLAN_NO"]=$row["CHALLAN_NO"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["ISSUE_BASIS"]=$row["ISSUE_BASIS"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["KNIT_DYE_SOURCE"]=$row["KNIT_DYE_SOURCE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["KNIT_DYE_COMPANY"]=$row["KNIT_DYE_COMPANY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["PROD_ID"]=$row["PROD_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["TRANSACTION_DATE"]=$row["TRANSACTION_DATE"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["CONS_UOM"]=$row["CONS_UOM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["REQUISITION_NO"]=$row["REQUISITION_NO"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["BRAND_ID"]=$row["BRAND_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["CONS_QUANTITY"]+=$row["CONS_QUANTITY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["RETURN_QNTY"]+=$row["RETURN_QNTY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["PROGRAM_NO"]=$row["PROGRAM_NO"];
				}

			}
		}
		unset($issue_result);

		if($r_id)oci_commit($con);else{echo "Temporery Table Execution Failed";oci_rollback($con);die;}
		//echo  $sql_issue;die;
		//echo "test";die;
		//order by KNITTING_COMPANY, RECEIVE_BASIS, TRANSACTION_TYPE, RECEIVE_DATE
		/*$sql_recv="select a.id as MST_ID, a.recv_number as RECV_NUMBER, a.recv_number_prefix_num as RECV_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.receive_date as RECEIVE_DATE, a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,  a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, b.id as TRANS_ID, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE, b.cons_uom as CONS_UOM, b.brand_id as BRAND_ID, b.cons_quantity as RCV_QNTY, b.return_qnty as RETURN_QNTY, b.cons_reject_qnty as CONS_REJECT_QNTY, p.booking_id as PROGRAM_NO
		from inv_receive_master p, pro_grey_prod_delivery_dtls q, inv_receive_master a, inv_transaction b
		where p.id=q.grey_sys_id and q.mst_id=a.booking_id and a.id=b.mst_id and a.item_category=13 and p.entry_form in(2) and p.receive_basis=2 and a.receive_basis=10 and a.entry_form in(58) and a.company_id=$cbo_company_name and b.item_category in(13) and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.booking_id in(select ref_val from gbl_temp_report_id where user_id=$user_id and ref_from=15)";*/
		$sql_recv="select a.id as MST_ID, a.recv_number as RECV_NUMBER, a.recv_number_prefix_num as RECV_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.receive_date as RECEIVE_DATE, a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,  a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, b.id as TRANS_ID, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE, b.cons_uom as CONS_UOM, b.brand_id as BRAND_ID, b.cons_quantity as RCV_QNTY, b.return_qnty as RETURN_QNTY, b.cons_reject_qnty as CONS_REJECT_QNTY, p.booking_id as PROGRAM_NO
		from inv_receive_master p, pro_grey_prod_delivery_dtls q, inv_receive_master a, inv_transaction b, gbl_temp_report_id g
		where p.id=q.grey_sys_id and q.mst_id=a.booking_id and a.id=b.mst_id and a.item_category=13 and p.entry_form in(2) and p.receive_basis=2 and a.receive_basis=10 and a.entry_form in(58) and a.company_id=$cbo_company_name and b.item_category in(13) and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.booking_id=g.ref_val and g.user_id=$user_id and g.ref_from=15";
		//echo $sql_recv;die;
		$recv_result=sql_select($sql_recv);
		foreach($recv_result as $row)
		{
			if($po_arr[$row['TRANS_ID']]['job_no'] && $trans_check[$row['TRANS_ID']]=="")
			{
				$trans_check[$row['TRANS_ID']]=$row['TRANS_ID'];
				$knit_party=$row["KNITTING_SOURCE"]."*".$row["KNITTING_COMPANY"];
				$mst_prod_id=$row["MST_ID"]."*".$row["PROD_ID"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RCV_ISSUE_NUMBER"]=$row["RECV_NUMBER"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RECV_NUMBER_PREFIX_NUM"]=$row["RECV_NUMBER_PREFIX_NUM"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["ENTRY_FORM"]=$row["ENTRY_FORM"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["BUYER_ID"]=$row["BUYER_ID"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["BOOKING_NO"]=$row["BOOKING_NO"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RECEIVE_DATE"]=$row["RECEIVE_DATE"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["TRANSACTION_DATE"]=$row["TRANSACTION_DATE"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["CHALLAN_NO"]=$row["CHALLAN_NO"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["YARN_ISSUE_CHALLAN_NO"]=$row["YARN_ISSUE_CHALLAN_NO"];

				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["KNITTING_SOURCE"]=$row["KNITTING_SOURCE"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["KNITTING_COMPANY"]=$row["KNITTING_COMPANY"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["PROD_ID"]=$row["PROD_ID"];

				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["CONS_UOM"]=$row["CONS_UOM"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["BRAND_ID"]=$row["BRAND_ID"];

				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RCV_QNTY"]+=$row["RCV_QNTY"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RETURN_QNTY"]+=$row["RETURN_QNTY"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RCV_REJECT_QNTY"]+=$row["CONS_REJECT_QNTY"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["PROGRAM_NO"]=$row["PROGRAM_NO"];
			}
		}
		unset($recv_result);
		//echo $sql_recv;die;
		$sql_recv_rtn="select a.id as MST_ID, a.recv_number as RECV_NUMBER, a.recv_number_prefix_num as RECV_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.receive_date as RECEIVE_DATE, a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,  a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, b.id as TRANS_ID, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE, b.cons_uom as CONS_UOM, b.brand_id as BRAND_ID, b.cons_quantity as RCV_QNTY, b.return_qnty as RETURN_QNTY, b.cons_reject_qnty as CONS_REJECT_QNTY, c.knit_id as PROGRAM_NO, c.requisition_no as REQUISITION_NO
		from ppl_yarn_requisition_entry c, inv_receive_master a, inv_transaction b, gbl_temp_report_id g
		where c.requisition_no=a.booking_id and a.id=b.mst_id and a.item_category=1 and a.receive_basis=3 and a.entry_form in(9) and a.company_id=$cbo_company_name and b.item_category in(1) and b.transaction_type in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.knit_id=g.ref_val and g.user_id=$user_id and g.ref_from=15";
		//echo $sql_recv_rtn;die;
		$recv_rtn_result=sql_select($sql_recv_rtn);
		foreach($recv_rtn_result as $row)
		{
			if($po_arr[$row['TRANS_ID']]['job_no'] && $trans_check[$row['TRANS_ID']]=="")
			{
				$trans_check[$row['TRANS_ID']]=$row['TRANS_ID'];
				$knit_party=$row["KNITTING_SOURCE"]."*".$row["KNITTING_COMPANY"];
				$mst_prod_id=$row["MST_ID"]."*".$row["PROD_ID"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RCV_ISSUE_NUMBER"]=$row["RECV_NUMBER"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RECV_NUMBER_PREFIX_NUM"]=$row["RECV_NUMBER_PREFIX_NUM"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["ENTRY_FORM"]=$row["ENTRY_FORM"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["BUYER_ID"]=$row["BUYER_ID"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["BOOKING_NO"]=$row["BOOKING_NO"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RECEIVE_DATE"]=$row["RECEIVE_DATE"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["TRANSACTION_DATE"]=$row["TRANSACTION_DATE"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["CHALLAN_NO"]=$row["CHALLAN_NO"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["YARN_ISSUE_CHALLAN_NO"]=$row["YARN_ISSUE_CHALLAN_NO"];

				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["KNITTING_SOURCE"]=$row["KNITTING_SOURCE"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["KNITTING_COMPANY"]=$row["KNITTING_COMPANY"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["PROD_ID"]=$row["PROD_ID"];

				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["CONS_UOM"]=$row["CONS_UOM"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["BRAND_ID"]=$row["BRAND_ID"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["REQUISITION_NO"]=$row["REQUISITION_NO"];

				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["ISS_RTN_QNTY"]+=$row["RCV_QNTY"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RETURN_QNTY"]+=$row["RETURN_QNTY"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RTN_REJECT_QNTY"]+=$row["CONS_REJECT_QNTY"];
				$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["PROGRAM_NO"]=$row["PROGRAM_NO"];
			}
		}
		unset($recv_rtn_result);
		//echo $sql_recv_rtn;die;
		$r_id3=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
		if($r_id3)oci_commit($con);
		//echo "<pre>";print_r($details_data);die;

		ob_start();
		?>
		<div>
			<table width="1570" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
				<tr>
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
				<tr>
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><?  if($from_date !="" && $to_date!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<table width="1570" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="115">General WO.</th>
					<th width="115">Service WO</th>
					<th width="70">Prog. No</th>
					<th width="70">Reqsn. No</th>
					<th width="70">Trans. Date</th>
					<th width="125">Trans. Ref.</th>
					<th width="80">Brand</th>
					<th width="200">Item Description</th>
					<th width="80">Lot</th>
					<th width="80">Yarn Issued</th>
					<th width="80">Fabric Received</th>
					<th width="80">Reject Fabric Received</th>
					<th width="80">Yarn Returned</th>
					<th width="80">Reject Yarn Returned</th>
					<th>Balance</th>
				</thead>
			</table>
			<div style="width:1570px; overflow-y: scroll; max-height:280px;" id="scroll_body">
				<table width="1550" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
				<?
				$i=1;
				foreach($details_data as $knit_party_ref=>$party_data)
				{
					$knit_party_ref=explode("*",$knit_party_ref);
					$knitting_party_source=$knit_party_ref[0];
					$knitting_party_id=$knit_party_ref[1];
					if($knitting_party_source==1) $knitting_party=$company_arr[$knitting_party_id]; else $knitting_party=$supplier_arr[$knitting_party_id];
					?>
					<tr bgcolor="#EFEFEF"><td colspan="22"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
					<?
					foreach($party_data as $job_no=>$job_data)
					{
						foreach($job_data as $data_type_id=>$type_data)
						{
							foreach($type_data as $trans_id=>$val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="80"><p><? echo $job_no; ?>&nbsp;</p></td>
									<td width="115"><p><? echo $prog_book_arr[$val['PROGRAM_NO']]; ?>&nbsp;</p></td>
									<td width="115"><p><? echo $service_book_arr[$val['PROGRAM_NO']]['BOOKING_NO']; ?>&nbsp;</p></td>
									<td width="70" align="center"><p><? echo $val['PROGRAM_NO']; ?>&nbsp;</p></td>
									<td width="70" align="center"><p><? echo $val['REQUISITION_NO']; ?>&nbsp;</p></td>
									<td width="70" align="center"><p><? echo change_date_format($val['TRANSACTION_DATE']); ?>&nbsp;</p></td>
									<td width="125"><p><? echo $val['RCV_ISSUE_NUMBER']; ?>&nbsp;</p></td>
									<td width="80"><p>

									<?
									//$yarn_brand_value ="";
									$yarn_brand_value =array();
									$y_prod_id_arr=array_filter(array_unique(explode(",",chop($prod_id_arr[$val['PROGRAM_NO']],","))));

									foreach ($y_prod_id_arr as  $yprodId)
									{
										//$yarn_brand_value .= $yarn_brand_ref[$yprodId].",";
										$yarn_brand_value[]= $yarn_brand_ref[$yprodId];
									}
									$yarn_brand_value = implode(',',array_unique($yarn_brand_value));
									echo $yarn_brand_value=chop($yarn_brand_value,",");
									//echo $yarn_brand_value=chop($yarn_brand_value,",");

									?>&nbsp;
									</p></td>
									<td width="150"><p><? echo $product_arr[$val['PROD_ID']]['PRODUCT_NAME_DETAILS']; ?>&nbsp;</p></td>
									<td width="80" title="<? echo $val['PROD_ID'] ; ?>"><p><? echo $product_arr[$val['PROD_ID']]['LOT']; ?></p></td>
									<td width="80" align="right"><? if($val['CONS_QUANTITY']) echo number_format($val['CONS_QUANTITY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['RCV_QNTY']) echo number_format($val['RCV_QNTY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['RCV_REJECT_QNTY']) echo number_format($val['RCV_REJECT_QNTY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['ISS_RTN_QNTY']) echo number_format($val['ISS_RTN_QNTY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['RTN_REJECT_QNTY']) echo number_format($val['RTN_REJECT_QNTY'],2); else echo ""; ?></td>
									<td align="right"><? //echo number_format($balance,2); ?></td>
								</tr>
								<?
								$i++;
								$job_issue_qnty+=$val['CONS_QUANTITY'];
								$job_rcv_qnty+=$val['RCV_QNTY'];
								$job_rcv_reject_qnty+=$val['RCV_REJECT_QNTY'];
								$job_issu_rtn_qnty+=$val['ISS_RTN_QNTY'];
								$job_rtn_reject_qnty+=$val['RTN_REJECT_QNTY'];

								$party_issue_qnty+=$val['CONS_QUANTITY'];
								$party_rcv_qnty+=$val['RCV_QNTY'];
								$party_rcv_reject_qnty+=$val['RCV_REJECT_QNTY'];
								$party_issu_rtn_qnty+=$val['ISS_RTN_QNTY'];
								$party_rtn_reject_qnty+=$val['RTN_REJECT_QNTY'];

								$tot_issue_qnty+=$val['CONS_QUANTITY'];
								$tot_rcv_qnty+=$val['RCV_QNTY'];
								$tot_rcv_reject_qnty+=$val['RCV_REJECT_QNTY'];
								$tot_issu_rtn_qnty+=$val['ISS_RTN_QNTY'];
								$tot_rtn_reject_qnty+=$val['RTN_REJECT_QNTY'];
							}
						}
						?>
						<tr bgcolor="#FFFFCC">
							<td width="40">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="115">&nbsp;</td>
							<td width="115">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="125">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="200">&nbsp;</td>
							<td width="80" align="right" style="font-weight:bold; font-size:14px;">Job Total:</td>
							<td width="80" align="right"><? echo number_format($job_issue_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_rcv_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_rcv_reject_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_issu_rtn_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_rtn_reject_qnty,2);?></td>
							<td align="right"><? $job_balance_qnty=($job_issue_qnty-($job_rcv_qnty+$job_issu_rtn_qnty)); echo number_format($job_balance_qnty,2); ?></td>
						</tr>
						<?
						$job_issue_qnty=$job_rcv_qnty=$job_rcv_reject_qnty=$job_issu_rtn_qnty=$job_rtn_reject_qnty=0;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td width="40">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="115">&nbsp;</td>
						<td width="115">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="125">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="200">&nbsp;</td>
						<td width="80" align="right" align="right" style="font-weight:bold; font-size:14px;">Party Total</td>
						<td width="80" align="right"><? echo number_format($party_issue_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_rcv_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_rcv_reject_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_issu_rtn_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_rtn_reject_qnty,2);?></td>
						<td align="right"><? $party_balance_qnty=($party_issue_qnty-($party_rcv_qnty+$party_issu_rtn_qnty)); echo number_format($party_balance_qnty,2); ?></td>
					</tr>
					<?
					$party_issue_qnty=$party_rcv_qnty=$party_rcv_reject_qnty=$party_issu_rtn_qnty=$party_rtn_reject_qnty=0;
				}
				?>
				</table>
			</div>
			<table width="1570" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="40">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="115">&nbsp;</td>
					<td width="115">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="125">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="200">&nbsp;</td>
					<td width="80" align="right" style="font-weight:bold; font-size:14px;">Grand Total:</td>
					<td width="80" align="right"><? echo number_format($tot_issue_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_rcv_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_rcv_reject_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_issu_rtn_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_rtn_reject_qnty,2);?></td>
					<td align="right"><? $tot_balance_qnty=($tot_issue_qnty-($tot_rcv_qnty+$tot_issu_rtn_qnty)); echo number_format($tot_balance_qnty,2); ?></td>
				</tr>
			</table>
		</div>
		<?
	}
	else if($type==2)
	{
		if($txt_job_id!="" && $cbo_search_type==2) $book_cond=" and a.id in($txt_job_id)";
	
		$dataBookArray=sql_select("select a.booking_no as BOOKING_NO, b.program_no as PROGRAM_NO
		from wo_booking_mst a, wo_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type=3 and b.booking_type=3 and a.company_id=$cbo_company_name $book_cond  ");

		if($txt_job_id!="" && $cbo_search_type==2 && count($dataBookArray)<1)
		{
			echo "10####<p style=\"margin-top:20px; padding-top:10px; font-weight:bold; font-size:16px; color:red;\"></p>";die;
		}

		if($txt_job_id!="" && $cbo_search_type==1)
		{
			// echo "SELECT c.po_breakdown_id as PO_BREAKDOWN_ID from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c
			//  where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.entry_form in(2,3,9,22,58) and b.status_active=$cbo_po_status and a.company_name=$cbo_company_name and a.id=$txt_job_id";die;
			$datapoArrr=sql_select("SELECT c.po_breakdown_id as PO_BREAKDOWN_ID from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c
			where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.entry_form in(2,3,9,22,58) and b.status_active=$cbo_po_status and a.company_name=$cbo_company_name and a.id in($txt_job_id)");

			$poIdArr=[];
			$poIdChk=[];
			foreach ($datapoArrr as $row) 
			{
				if($poIdChk[$row[csf('PO_BREAKDOWN_ID')]] == "")
				{
					$poIdChk[$row[csf('PO_BREAKDOWN_ID')]] = $row[csf('PO_BREAKDOWN_ID')];
					array_push($poIdArr,$row["PO_BREAKDOWN_ID"]);
				}
				
			}
			unset($datapoArrr);
			//$po_id_data_conds='';
			if(!empty($poIdArr))
			{
				$sql_po_cond_data ="".where_con_using_array($poIdArr,0,'d.po_breakdown_id')."";
			}

		}
		//echo "<pre>";print_r($sql_po_cond_data);die;

		$all_prog_id=array();
		foreach($dataBookArray as $row)
		{
			if($txt_job_id!="" && $cbo_search_type==2) $all_prog_id[$row['PROGRAM_NO']]=$row['PROGRAM_NO'];
			$service_book_arr[$row['PROGRAM_NO']]['BOOKING_NO']=$row['BOOKING_NO'];
		}
		unset($dataBookArray);

		$sql_cond_issue="";
		if($txt_challan !="") $sql_cond_issue.=" and a.issue_number_prefix_num=$txt_challan";
		if($from_date !="" && $to_date !="" && count($all_trans_id)<1 && count($all_prog_id)<1) $sql_cond_issue.=" and b.transaction_date between '$from_date' and '$to_date'";

		if($cbo_knitting_source > 0) $sql_cond_issue.=" and a.knit_dye_source=$cbo_knitting_source";
		if($txt_knitting_com_id !="") $sql_cond_issue.=" and a.knit_dye_company in ($txt_knitting_com_id)";
		if(count($all_trans_id)>0) $sql_cond_issue.=" and b.id in (".implode(",",$all_trans_id).")";
		if(count($all_prog_id)>0) $sql_cond_issue.=" and c.knit_id in (".implode(",",$all_prog_id).")";
	

		$sql_issue="SELECT a.id as MST_ID, a.issue_number as ISSUE_NUMBER, a.issue_number_prefix_num as ISSUE_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.issue_date as ISSUE_DATE, a.challan_no as CHALLAN_NO, a.issue_basis as ISSUE_BASIS, a.knit_dye_source as KNIT_DYE_SOURCE, a.knit_dye_company as KNIT_DYE_COMPANY, b.id as TRANS_ID, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE, b.cons_uom as CONS_UOM, b.requisition_no as REQUISITION_NO, b.brand_id as BRAND_ID, b.cons_quantity as CONS_QUANTITY, b.return_qnty as RETURN_QNTY, c.knit_id as PROGRAM_NO,d.po_breakdown_id as PO_BREAKDOWN_ID
		from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c ,order_wise_pro_details d
		where a.id=b.mst_id and b.requisition_no=c.requisition_no and b.prod_id=c.prod_id and b.id=d.trans_id and b.prod_id=d.prod_id and a.entry_form=3 and a.issue_basis=3 and a.issue_purpose=1 and b.receive_basis=3 and a.company_id=$cbo_company_name $sql_po_cond_data and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond_issue ";
		//echo  $sql_issue;die;
		$issue_result=sql_select($sql_issue);
		$details_data=array();
		$prod_id_arr=array();

		$con = connect();
		execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in=127");
		oci_commit($con);
		disconnect($con);


		if(count($issue_result)<1){echo "10####<p style=\"margin-top:20px; padding-top:10px; font-weight:bold; font-size:16px; color:red;\"></p>";die;}

		foreach($issue_result as $row)
		{
			$all_po_id[$row[csf("PO_BREAKDOWN_ID")]]=$row[csf("PO_BREAKDOWN_ID")];

			$prod_id_arr[$row[csf("PROGRAM_NO")]].=$row[csf("PROD_ID")].',';
		}

		$po_arr=array();

		if($txt_job_id!="" && $cbo_search_type==1) $job_cond=" and a.id in($txt_job_id)";

		$all_po_id = array_filter($all_po_id);
		if(!empty($all_po_id))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 127, 3,$all_po_id, $empty_arr);
			//die;

			$datapoArray=sql_select("SELECT a.buyer_name as BUYER_NAME, a.job_no as JOB_NO, b.id as PO_ID, b.po_number as PO_NUMBER, c.trans_id as TRANS_ID from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c, gbl_temp_engine d
			where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.entry_form in(2,3,9,22,58) and b.status_active=$cbo_po_status and a.company_name=$cbo_company_name $job_cond and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.ref_from=3 and d.entry_form=127");

			if($txt_job_id!="" && $cbo_search_type==1 && count($datapoArray)<1)
			{
				echo "10####<p style=\"margin-top:20px; padding-top:10px; font-weight:bold; font-size:16px; color:red;\">NO DATA FOUND</p>";die;
			}

			$all_trans_id=array();
			foreach($datapoArray as $row)
			{
				if($txt_job_id!="" && $cbo_search_type==1) $all_trans_id[$row['TRANS_ID']]=$row['TRANS_ID'];
				$po_arr[$row['TRANS_ID']]['job_no']=$row['JOB_NO'];
				$po_arr[$row['TRANS_ID']]['po_number']=$row['PO_NUMBER'];
				$po_arr[$row['TRANS_ID']]['buyer']=$row['BUYER_NAME'];
			}
			unset($datapoArray);
		}

		$allProgramNoArr = [];
		$ref_check = [];
		$prod_id_check = [];
		$allprodIdArr = [];
		$program_no_job_arr = [];
		foreach($issue_result as $row)
		{
			if($ref_check[$row["PROGRAM_NO"]]=="")
			{
				$ref_check[$row["PROGRAM_NO"]]=$row["PROGRAM_NO"];

				$allProgramNoArr[$row["PROGRAM_NO"]] = $row["PROGRAM_NO"];
			}

			if($prod_id_check[$row["PROD_ID"]]=="")
			{
				$prod_id_check[$row["PROD_ID"]]=$row["PROD_ID"];

				$allprodIdArr[$row["PROD_ID"]] = $row["PROD_ID"];
			}

			if($trans_check[$row["MST_ID"]][$row['TRANS_ID']]=="")
			{
				$trans_check[$row["MST_ID"]][$row['TRANS_ID']]=$row['TRANS_ID'];

				if($po_arr[$row['TRANS_ID']]['job_no'])
				{
					$knit_party=$row["KNIT_DYE_SOURCE"]."*".$row["KNIT_DYE_COMPANY"];
					$mst_prod_id=$row["MST_ID"]."*".$row["PROD_ID"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["RCV_ISSUE_NUMBER"]=$row["ISSUE_NUMBER"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["ISSUE_NUMBER_PREFIX_NUM"]=$row["ISSUE_NUMBER_PREFIX_NUM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["ENTRY_FORM"]=$row["ENTRY_FORM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["BUYER_ID"]=$row["BUYER_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["BOOKING_NO"]=$row["BOOKING_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["ISSUE_DATE"]=$row["ISSUE_DATE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["CHALLAN_NO"]=$row["CHALLAN_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["ISSUE_BASIS"]=$row["ISSUE_BASIS"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["KNIT_DYE_SOURCE"]=$row["KNIT_DYE_SOURCE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["KNIT_DYE_COMPANY"]=$row["KNIT_DYE_COMPANY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["PROD_ID"]=$row["PROD_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["TRANSACTION_DATE"]=$row["TRANSACTION_DATE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["CONS_UOM"]=$row["CONS_UOM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["REQUISITION_NO"]=$row["REQUISITION_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["BRAND_ID"]=$row["BRAND_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["CONS_QUANTITY"]+=$row["CONS_QUANTITY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["RETURN_QNTY"]+=$row["RETURN_QNTY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][1][$mst_prod_id]["PROGRAM_NO"]=$row["PROGRAM_NO"];

					$program_no_job_arr[$row["PROGRAM_NO"]]['job_no']=$po_arr[$row['TRANS_ID']]['job_no'];
					$program_no_job_arr[$row["PROGRAM_NO"]]['knit_party']=$knit_party;
					$program_no_job_arr[$row["PROGRAM_NO"]]['requisition_no']=$row["REQUISITION_NO"];
				}
			}
		}
		unset($issue_result);

		//var_dump($allProgramNoArr);die;
		$allProgramNoArr = array_filter($allProgramNoArr);
		if(!empty($allProgramNoArr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 127, 1,$allProgramNoArr, $empty_arr);
			//die;

			$sql_recv="SELECT a.id as MST_ID, a.recv_number as RECV_NUMBER, a.recv_number_prefix_num as RECV_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.receive_date as RECEIVE_DATE, a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,  a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, b.id as TRANS_ID, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE, b.cons_uom as CONS_UOM, b.brand_id as BRAND_ID, b.cons_quantity as RCV_QNTY, b.return_qnty as RETURN_QNTY, b.cons_reject_qnty as CONS_REJECT_QNTY, p.booking_id as PROGRAM_NO
			from inv_receive_master p, pro_grey_prod_delivery_dtls q, inv_receive_master a, inv_transaction b, gbl_temp_engine g
			where p.id=q.grey_sys_id and q.mst_id=a.booking_id and a.id=b.mst_id and a.item_category=13 and p.entry_form in(2) and p.receive_basis=2 and a.receive_basis=10 and a.entry_form in(58) and a.company_id=$cbo_company_name and b.item_category in(13) and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.booking_id=g.ref_val and g.user_id=$user_id and g.ref_from=1 and g.entry_form=127 ";
			//echo $sql_recv;die;
			$recv_result=sql_select($sql_recv);
			foreach($recv_result as $row)
			{
				if($prod_id_check[$row["PROD_ID"]]=="")
				{
					$prod_id_check[$row["PROD_ID"]]=$row["PROD_ID"];

					$allprodIdArr[$row["PROD_ID"]] = $row["PROD_ID"];
				}

				if($po_arr[$row['TRANS_ID']]['job_no'] && $trans_check[$row['TRANS_ID']]=="")
				{
					$trans_check[$row['TRANS_ID']]=$row['TRANS_ID'];
					$knit_party=$row["KNITTING_SOURCE"]."*".$row["KNITTING_COMPANY"];
					$mst_prod_id=$row["MST_ID"]."*".$row["PROD_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RCV_ISSUE_NUMBER"]=$row["RECV_NUMBER"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RECV_NUMBER_PREFIX_NUM"]=$row["RECV_NUMBER_PREFIX_NUM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["ENTRY_FORM"]=$row["ENTRY_FORM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["BUYER_ID"]=$row["BUYER_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["BOOKING_NO"]=$row["BOOKING_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RECEIVE_DATE"]=$row["RECEIVE_DATE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["TRANSACTION_DATE"]=$row["TRANSACTION_DATE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["CHALLAN_NO"]=$row["CHALLAN_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["YARN_ISSUE_CHALLAN_NO"]=$row["YARN_ISSUE_CHALLAN_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["KNITTING_SOURCE"]=$row["KNITTING_SOURCE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["KNITTING_COMPANY"]=$row["KNITTING_COMPANY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["PROD_ID"]=$row["PROD_ID"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["CONS_UOM"]=$row["CONS_UOM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["BRAND_ID"]=$row["BRAND_ID"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RCV_QNTY"]+=$row["RCV_QNTY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RETURN_QNTY"]+=$row["RETURN_QNTY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["RCV_REJECT_QNTY"]+=$row["CONS_REJECT_QNTY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][2][$mst_prod_id]["PROGRAM_NO"]=$row["PROGRAM_NO"];

					$program_no_job_arr[$row["PROGRAM_NO"]]['job_no']=$po_arr[$row['TRANS_ID']]['job_no'];
					$program_no_job_arr[$row["PROGRAM_NO"]]['knit_party']=$knit_party;
					$program_no_job_arr[$row["PROGRAM_NO"]]['requisition_no']=$row["REQUISITION_NO"];
				}
			}
			unset($recv_result);
			//echo $sql_recv;die;

			$sql_recv_rtn="SELECT a.id as MST_ID, a.recv_number as RECV_NUMBER, a.recv_number_prefix_num as RECV_NUMBER_PREFIX_NUM, a.entry_form as ENTRY_FORM, a.buyer_id as BUYER_ID, a.booking_no as BOOKING_NO, a.receive_date as RECEIVE_DATE, a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,  a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY, b.id as TRANS_ID, b.prod_id as PROD_ID, b.transaction_date as TRANSACTION_DATE, b.cons_uom as CONS_UOM, b.brand_id as BRAND_ID, b.cons_quantity as RCV_QNTY, b.return_qnty as RETURN_QNTY, b.cons_reject_qnty as CONS_REJECT_QNTY, c.knit_id as PROGRAM_NO, c.requisition_no as REQUISITION_NO
			from ppl_yarn_requisition_entry c, inv_receive_master a, inv_transaction b, gbl_temp_engine g
			where c.requisition_no=a.booking_id and a.id=b.mst_id and a.item_category=1 and a.receive_basis=3 and a.entry_form in(9) and a.company_id=$cbo_company_name and b.item_category in(1) and b.transaction_type in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.knit_id=g.ref_val and g.user_id=$user_id and g.ref_from=1 and g.entry_form=127  ";
			//echo $sql_recv_rtn;die;
			$recv_rtn_result=sql_select($sql_recv_rtn);
			foreach($recv_rtn_result as $row)
			{
				if($prod_id_check[$row["PROD_ID"]]=="")
				{
					$prod_id_check[$row["PROD_ID"]]=$row["PROD_ID"];

					$allprodIdArr[$row["PROD_ID"]] = $row["PROD_ID"];
				}

				if($po_arr[$row['TRANS_ID']]['job_no'] && $trans_check[$row['TRANS_ID']]=="")
				{
					$trans_check[$row['TRANS_ID']]=$row['TRANS_ID'];
					$knit_party=$row["KNITTING_SOURCE"]."*".$row["KNITTING_COMPANY"];
					$mst_prod_id=$row["MST_ID"]."*".$row["PROD_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RCV_ISSUE_NUMBER"]=$row["RECV_NUMBER"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RECV_NUMBER_PREFIX_NUM"]=$row["RECV_NUMBER_PREFIX_NUM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["ENTRY_FORM"]=$row["ENTRY_FORM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["BUYER_ID"]=$row["BUYER_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["BOOKING_NO"]=$row["BOOKING_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RECEIVE_DATE"]=$row["RECEIVE_DATE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["TRANSACTION_DATE"]=$row["TRANSACTION_DATE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["CHALLAN_NO"]=$row["CHALLAN_NO"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["YARN_ISSUE_CHALLAN_NO"]=$row["YARN_ISSUE_CHALLAN_NO"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["KNITTING_SOURCE"]=$row["KNITTING_SOURCE"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["KNITTING_COMPANY"]=$row["KNITTING_COMPANY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["PROD_ID"]=$row["PROD_ID"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["CONS_UOM"]=$row["CONS_UOM"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["BRAND_ID"]=$row["BRAND_ID"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["REQUISITION_NO"]=$row["REQUISITION_NO"];

					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["ISS_RTN_QNTY"]+=$row["RCV_QNTY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RETURN_QNTY"]+=$row["RETURN_QNTY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RTN_REJECT_QNTY"]+=$row["CONS_REJECT_QNTY"];
					$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["PROGRAM_NO"]=$row["PROGRAM_NO"];

					if($piIdChk[$row[csf('pi_id')]] == "")
					{
						$piIdChk[$row[csf('pi_id')]] = $row[csf('pi_id')];
						array_push($piIdArr,$row[csf('pi_id')]);
					}

					$program_no_job_arr[$row["PROGRAM_NO"]]['job_no']=$po_arr[$row['TRANS_ID']]['job_no'];
					$program_no_job_arr[$row["PROGRAM_NO"]]['knit_party']=$knit_party;
					$program_no_job_arr[$row["PROGRAM_NO"]]['requisition_no']=$row["REQUISITION_NO"];
				}
			}
			unset($recv_rtn_result);

			$sql_debit_note = "SELECT A.COMPANY_ID AS KNITTING_COMPANY, A.DEBIT_NOTE_AGAINST,A.GOODS_NAME,A.BASIS,A.SYS_NUMBER, B.ID AS TR_ID, A.WO_ISSUE_ID, A.DEBIT_NOTE_DATE, sum(B.PROCESS_LOSS_DEDUC_QNTY) as PROCESS_LOSS_DEDUC_QNTY, sum(B.DEBIT_NOTE_QNTY) as DEBIT_NOTE_QNTY,B.FEBRIC_DESCRIPTION, B.YARN_BRAND, B.YARN_LOT FROM DEBIT_NOTE_ENTRY_MST A, DEBIT_NOTE_ENTRY_DTLS B, GBL_TEMP_ENGINE C
			WHERE A.ID=B.MST_ID AND B.ENTRY_FORM=633 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and A.WO_ISSUE_ID=C.REF_VAL AND C.USER_ID=$user_id and C.ref_from=1 and C.entry_form=127 group by A.COMPANY_ID,A.DEBIT_NOTE_AGAINST,A.GOODS_NAME,A.BASIS,A.SYS_NUMBER,B.ID, A.WO_ISSUE_ID, a.DEBIT_NOTE_DATE, B.FEBRIC_DESCRIPTION, B.YARN_BRAND, B.YARN_LOT";
			//echo $sql_debit_note;die;
			$debit_note_result=sql_select($sql_debit_note);
			$debitNoteInfoArr = [];
			foreach($debit_note_result as $row)
			{
				//$knit_party=$row["KNITTING_SOURCE"]."*".$row["KNITTING_COMPANY"];
				$mst_prod_id=$row["FEBRIC_DESCRIPTION"]."*".$row["YARN_BRAND"]."*".$row["YARN_LOT"];
				$job_no='';
				if($row["DEBIT_NOTE_AGAINST"]==1 && $row["GOODS_NAME"]==1&& $row["BASIS"]==5)
				{
					$job_no = $program_no_job_arr[$row["WO_ISSUE_ID"]]['job_no'];
					$knit_party=$program_no_job_arr[$row["WO_ISSUE_ID"]]['knit_party'];
				}
				//$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["PROGRAM_NO"]=$row["PROGRAM_NO"];

				$details_data[$knit_party][$job_no][4][$mst_prod_id]["PROGRAM_NO"]= $row["WO_ISSUE_ID"];
				$details_data[$knit_party][$job_no][4][$mst_prod_id]["RCV_ISSUE_NUMBER"]= $row["SYS_NUMBER"];
				$details_data[$knit_party][$job_no][4][$mst_prod_id]["REQUISITION_NO"]= $program_no_job_arr[$row["WO_ISSUE_ID"]]['requisition_no'];
				$details_data[$knit_party][$job_no][4][$mst_prod_id]["TRANSACTION_DATE"]= $row["DEBIT_NOTE_DATE"];
				$details_data[$knit_party][$job_no][4][$mst_prod_id]["DEBIT_NOTE_QNTY"]= $row["DEBIT_NOTE_QNTY"];
				$details_data[$knit_party][$job_no][4][$mst_prod_id]["PROCESS_LOSS_DEDUC_QNTY"]= $row["PROCESS_LOSS_DEDUC_QNTY"];
				//$details_data[$knit_party][$po_arr[$row['TRANS_ID']]['job_no']][3][$mst_prod_id]["RCV_ISSUE_NUMBER"]=$row["RECV_NUMBER"];

				$debitNoteInfoArr[$row["WO_ISSUE_ID"]]["debit_note_qnty"]=$row["DEBIT_NOTE_QNTY"];
				$debitNoteInfoArr[$row["WO_ISSUE_ID"]]["process_loss_deduc_qnty"]=$row["PROCESS_LOSS_DEDUC_QNTY"];
			}
			//echo "<pre>";print_r($debitNoteInfoArr);die;

			$sql_prog=sql_select("SELECT b.id as PROG_NO, a.booking_no as BOOKING_NO from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, gbl_temp_engine c where a.id=b.mst_id and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=c.ref_val and c.user_id=$user_id and c.ref_from=1 and c.entry_form=127");
			foreach($sql_prog as $row)
			{
				$prog_book_arr[$row['PROG_NO']]=$row['BOOKING_NO'];
			}
			unset($sql_prog);

		}

		$allprodIdArr = array_filter($allprodIdArr);
		if(!empty($allprodIdArr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 127, 2,$allprodIdArr, $empty_arr);
			//die;
			$product_arr=array();
			$sql_product="SELECT a.id as ID, a.product_name_details as PRODUCT_NAME_DETAILS, a.lot as LOT, a.brand as BRAND from product_details_master a, gbl_temp_engine b where a.item_category_id in(1,13) and a.company_id=$cbo_company_name and a.id=b.ref_val and b.user_id=$user_id and b.ref_from=2 and b.entry_form=127";
			//echo $sql_product;die;
			$sql_product_res=sql_select($sql_product);
			foreach($sql_product_res as $rowp)
			{
				$product_arr[$rowp['ID']]['PRODUCT_NAME_DETAILS']=$rowp['PRODUCT_NAME_DETAILS'];
				$product_arr[$rowp['ID']]['LOT']=$rowp['LOT'];
				$product_arr[$rowp['ID']]['BRAND']=$rowp['BRAND'];
			}
			unset($sql_prod_res);
			//echo $sql_prod;die;

			/* $yarn_prod_sql=sql_select("SELECT a.id, b.brand_name from product_details_master a, lib_brand b, gbl_temp_engine c where a.brand = b.id and a.item_category_id = 1 and a.company_id=$cbo_company_name and a.id=c.ref_val and c.user_id=$user_id and c.ref_from=2 and c.entry_form=127");
			// $prodids_cond
			foreach($yarn_prod_sql as $row)
			{
				if($duplicate_chk[$row[csf('id')]] =='')
				{
					$duplicate_chk[$row[csf('id')]]=$row[csf('id')];
					$yarn_brand_ref[$row[csf('id')]] .=$row[csf('brand_name')];
				}
			}
			unset($yarn_prod_sql); */
		}

		execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (127)");
		oci_commit($con);
		disconnect($con);

		ob_start();
		?>
		<div>
			<table width="1730" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
				<tr>
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
				<tr>
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
				<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><?  if($from_date !="" && $to_date!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<table width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="115">General WO.</th>
					<th width="115">Service WO</th>
					<th width="70">Prog. No</th>
					<th width="70">Reqsn. No</th>
					<th width="70">Trans. Date</th>
					<th width="125">Trans. Ref.</th>
					<th width="80">Brand</th>
					<th width="200">Item Description</th>
					<th width="80">Lot</th>
					<th width="80">Yarn Issued</th>
					<th width="80">Fabric Received</th>
					<th width="80">Reject Fabric Received</th>
					<th width="80">Yarn Returned</th>
					<th width="80">Reject Yarn Returned</th>
					<th width="80">Process Loss/Other duduction qty</th>
					<th width="80">Debit Note Qty</th>
					<th>Balance</th>
				</thead>
			</table>
			<div style="width:1730px; overflow-y: scroll; max-height:280px;" id="scroll_body">
				<table width="1710" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
				<?
				$i=1;
				foreach($details_data as $knit_party_ref=>$party_data)
				{
					$knit_party_ref=explode("*",$knit_party_ref);
					$knitting_party_source=$knit_party_ref[0];
					$knitting_party_id=$knit_party_ref[1];
					if($knitting_party_source==1) $knitting_party=$company_arr[$knitting_party_id]; else $knitting_party=$supplier_arr[$knitting_party_id];
					
					?>
					<tr bgcolor="#EFEFEF"><td colspan="24"><b>Party name: <? echo $knitting_party; ?></b></td></tr>
					<?
					foreach($party_data as $job_no=>$job_data)
					{
						foreach($job_data as $data_type_id=>$type_data)
						{
							foreach($type_data as $trans_id=>$val)
							{

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								if($data_type_id==4)
								{
									$yarn_info=explode("*",$trans_id);
									$febric_description = $yarn_info[0];
									$yarn_brand = $yarn_info[1];
									$yarn_lot = $yarn_info[2];
								}
								
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="80"><p><? echo $job_no; ?>&nbsp;</p></td>
									<td width="115" title="<? echo $val['PROGRAM_NO'];?>"><p><? echo $prog_book_arr[$val['PROGRAM_NO']]; ?>&nbsp;</p></td>
									<td width="115"><p><? echo $service_book_arr[$val['PROGRAM_NO']]['BOOKING_NO']; ?>&nbsp;</p></td>
									<td width="70" align="center"><p><? echo $val['PROGRAM_NO']; ?>&nbsp;</p></td>
									<td width="70" align="center"><p><? echo $val['REQUISITION_NO']; ?>&nbsp;</p></td>
									<td width="70" align="center"><p><? echo change_date_format($val['TRANSACTION_DATE']); ?>&nbsp;</p></td>
									<td width="125"><p><? echo $val['RCV_ISSUE_NUMBER']; ?>&nbsp;</p></td>
									<td width="80"><p>

									<?
									//$yarn_brand_value ="";
									/* $yarn_brand_value =array();
									$y_prod_id_arr=array_filter(array_unique(explode(",",chop($prod_id_arr[$val['PROGRAM_NO']],","))));

									foreach ($y_prod_id_arr as  $yprodId)
									{
										//$yarn_brand_value .= $yarn_brand_ref[$yprodId].",";
										$yarn_brand_value[]= $yarn_brand_ref[$yprodId];
									}
									$yarn_brand_value = implode(',',array_unique($yarn_brand_value));
									echo $yarn_brand_value=chop($yarn_brand_value,","); */
									//echo $yarn_brand_value=chop($yarn_brand_value,",");

									if($data_type_id==4)
									{
										echo $yarn_brand;
									}
									else
									{
										echo $brand_arr[$product_arr[$val['PROD_ID']]['BRAND']];
									}
										
									?>&nbsp;
									</p></td>
									<td width="150"><p>
										<? 
										if($data_type_id==4)
										{
											echo $febric_description;
										}
										else
										{
											echo $product_arr[$val['PROD_ID']]['PRODUCT_NAME_DETAILS']; 
										}
										
										?>
										&nbsp;</p></td>
									<td width="80" title="<? echo $val['PROD_ID'] ; ?>"><p>
										<? 
										if($data_type_id==4)
										{
											echo $yarn_lot;
										}
										else
										{
											echo $product_arr[$val['PROD_ID']]['LOT'];
										}
										
										 ?>
									</p></td>
									<td width="80" align="right"><? if($val['CONS_QUANTITY']) echo number_format($val['CONS_QUANTITY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['RCV_QNTY']) echo number_format($val['RCV_QNTY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['RCV_REJECT_QNTY']) echo number_format($val['RCV_REJECT_QNTY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['ISS_RTN_QNTY']) echo number_format($val['ISS_RTN_QNTY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['RTN_REJECT_QNTY']) echo number_format($val['RTN_REJECT_QNTY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['PROCESS_LOSS_DEDUC_QNTY']) echo number_format($val['PROCESS_LOSS_DEDUC_QNTY'],2); else echo ""; ?></td>
									<td width="80" align="right"><? if($val['DEBIT_NOTE_QNTY']) echo number_format($val['DEBIT_NOTE_QNTY'],2); else echo ""; ?></td>
									<td align="right"><? //echo number_format($balance,2); ?></td>
								</tr>
								<?
								$i++;

								if($porgramNoChk[$val['PROGRAM_NO']] =="")
								{
									$porgramNoChk[$val['PROGRAM_NO']] = $val['PROGRAM_NO'];

									$job_debit_note_qnty += $debitNoteInfoArr[$val["PROGRAM_NO"]]["debit_note_qnty"];
									$job_process_loss_deduc_qnty += $debitNoteInfoArr[$val["PROGRAM_NO"]]["process_loss_deduc_qnty"];
									$party_debit_note_qnty += $debitNoteInfoArr[$val["PROGRAM_NO"]]["debit_note_qnty"];
									$party_process_loss_deduc_qnty += $debitNoteInfoArr[$val["PROGRAM_NO"]]["process_loss_deduc_qnty"];
									$tot_debit_note_qnty += $debitNoteInfoArr[$val["PROGRAM_NO"]]["debit_note_qnty"];
									$tot_process_loss_deduc_qnty += $debitNoteInfoArr[$val["PROGRAM_NO"]]["process_loss_deduc_qnty"];
								}

								$job_issue_qnty+=$val['CONS_QUANTITY'];
								$job_rcv_qnty+=$val['RCV_QNTY'];
								$job_rcv_reject_qnty+=$val['RCV_REJECT_QNTY'];
								$job_issu_rtn_qnty+=$val['ISS_RTN_QNTY'];
								$job_rtn_reject_qnty+=$val['RTN_REJECT_QNTY'];

								$party_issue_qnty+=$val['CONS_QUANTITY'];
								$party_rcv_qnty+=$val['RCV_QNTY'];
								$party_rcv_reject_qnty+=$val['RCV_REJECT_QNTY'];
								$party_issu_rtn_qnty+=$val['ISS_RTN_QNTY'];
								$party_rtn_reject_qnty+=$val['RTN_REJECT_QNTY'];

								$tot_issue_qnty+=$val['CONS_QUANTITY'];
								$tot_rcv_qnty+=$val['RCV_QNTY'];
								$tot_rcv_reject_qnty+=$val['RCV_REJECT_QNTY'];
								$tot_issu_rtn_qnty+=$val['ISS_RTN_QNTY'];
								$tot_rtn_reject_qnty+=$val['RTN_REJECT_QNTY'];
							}
						}
						?>
						<tr bgcolor="#FFFFCC">
							<td width="40">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="115">&nbsp;</td>
							<td width="115">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="125">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="200">&nbsp;</td>
							<td width="80" align="right" style="font-weight:bold; font-size:14px;">Job Total:</td>
							<td width="80" align="right"><? echo number_format($job_issue_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_rcv_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_rcv_reject_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_issu_rtn_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_rtn_reject_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_process_loss_deduc_qnty,2);?></td>
							<td width="80" align="right"><? echo number_format($job_debit_note_qnty,2);?></td>
							<td align="right"><? $job_balance_qnty=($job_issue_qnty-($job_rcv_qnty+$job_issu_rtn_qnty)-($job_process_loss_deduc_qnty+$job_debit_note_qnty)); echo number_format($job_balance_qnty,2); ?></td>
						</tr>
						<?
						$job_issue_qnty=$job_rcv_qnty=$job_rcv_reject_qnty=$job_issu_rtn_qnty=$job_rtn_reject_qnty=$job_process_loss_deduc_qnty=$job_debit_note_qnty=0;
					}
					?>
					<tr bgcolor="#CCCCCC">
						<td width="40">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="115">&nbsp;</td>
						<td width="115">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="125">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="200">&nbsp;</td>
						<td width="80" align="right" align="right" style="font-weight:bold; font-size:14px;">Party Total</td>
						<td width="80" align="right"><? echo number_format($party_issue_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_rcv_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_rcv_reject_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_issu_rtn_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_rtn_reject_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_process_loss_deduc_qnty,2);?></td>
						<td width="80" align="right"><? echo number_format($party_debit_note_qnty,2);?></td>
						<td align="right"><? $party_balance_qnty=($party_issue_qnty-($party_rcv_qnty+$party_issu_rtn_qnty)-($party_process_loss_deduc_qnty+$party_debit_note_qnty)); echo number_format($party_balance_qnty,2); ?></td>
					</tr>
					<?
					$party_issue_qnty=$party_rcv_qnty=$party_rcv_reject_qnty=$party_issu_rtn_qnty=$party_rtn_reject_qnty=$party_process_loss_deduc_qnty=$party_debit_note_qnty=0;
				}
				?>
				</table>
			</div>
			<table width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="40">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="115">&nbsp;</td>
					<td width="115">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="125">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="200">&nbsp;</td>
					<td width="80" align="right" style="font-weight:bold; font-size:14px;">Grand Total:</td>
					<td width="80" align="right"><? echo number_format($tot_issue_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_rcv_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_rcv_reject_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_issu_rtn_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_rtn_reject_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_process_loss_deduc_qnty,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_debit_note_qnty,2);?></td>
					<td align="right" style="padding-right: 20px;"><? $tot_balance_qnty=($tot_issue_qnty-($tot_rcv_qnty+$tot_issu_rtn_qnty)-($tot_process_loss_deduc_qnty+$tot_debit_note_qnty)); echo number_format($tot_balance_qnty,2); ?></td>
				</tr>
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

if($action=="report_generate_job")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	if($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}
	if ($from_date!='' && $to_date!='') $issue_date_cond=" and e.issue_date between '$from_date' and '$to_date'"; else $issue_date_cond="";
	if ($from_date!='' && $to_date!='') $recv_date_cond=" and e.receive_date between '$from_date' and '$to_date'"; else $recv_date_cond="";

	$knitting_company=str_replace("'","",$txt_knitting_com_id);
	$type=str_replace("'","",$type);
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and c.job_no_prefix_num in ($job_no) ";//and FIND_IN_SET(c.job_no_prefix_num,'$job_no')
	$txt_internal_ref=str_replace("'","",$txt_internal_ref);
	if ($txt_internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping = '$txt_internal_ref'";


	ob_start();

	$all_party=explode(",",$knitting_company);

	?>
        <div>
            <table width="2147" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="22" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="22" style="font-size:14px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="22" style="font-size:12px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <table width="2147" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="70">Date</th>
                    <th width="120">Transaction Ref.</th>
                    <th width="100">Recv. Challan No</th>
                    <th width="100">Issue Challan No</th>
                    <th width="120">Booking/ Req. No</th>
                    <th width="80">Buyer</th>
                    <th width="130">Style Ref.</th>
                    <th width="130">Order Numbers</th>
                    <th width="90">Order Qty.</th>
                    <th width="80">Brand</th>
                    <th width="150">Item Description</th>
                    <th width="80">Lot</th>
                    <th width="50">UOM</th>
                    <th width="100">Yarn Issued</th>
                    <th width="100">Returnable Qty.</th>
                    <th width="100">Fabric Received</th>
                    <th width="100">Reject Fabric Received</th>
                    <th width="100">Yarn Returned</th>
                    <th width="100">Reject Yarn Returned</th>
                    <th width="100">Balance</th>
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:2147px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="2130" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
				<?
				$po_arr=array();
				$datapoArray=sql_select("select id, po_number, po_quantity from wo_po_break_down");

				foreach($datapoArray as $row)
				{
					$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
				}

				$order_nos_array=array();
				if($db_type==0)
				{
					$datapropArray=sql_select("select trans_id,
						CASE WHEN entry_form='3' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
						CASE WHEN entry_form in (2,22) and trans_type=1 THEN group_concat(po_breakdown_id) END AS grey_order_id,
						CASE WHEN entry_form='9' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id
						from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,3,9,22) and status_active=1 and is_deleted=0 group by trans_id ");
				}
				elseif($db_type==2)
				{
					$datapropArray=sql_select("select trans_id,
						listagg(CASE WHEN entry_form='3' and trans_type=2 THEN  po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
						listagg(CASE WHEN entry_form in (2,22) and trans_type=1 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) END AS grey_order_id,
						listagg(CASE WHEN entry_form='9' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) END AS yarn_return_order_id
						from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (2,3,9,22) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type ");
				}

				foreach($datapropArray as $row)
				{
					$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
					$order_nos_array[$row[csf('trans_id')]]['grey_recv']=$row[csf('grey_order_id')];
					$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
				}

				if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_knitting_source";
				if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party="  and a.id in ($knitting_company)";
				if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp="  and id in ($knitting_company)";
				$knit_source=str_replace("'","",$cbo_knitting_source);
				//echo $cbo_knitting_source;
				if ($knit_source==3)
				{
					$sql_party="select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$cbo_company_name and b.party_type in(1,9,20) and a.status_active=1 $knitting_company_cond_party group by a.id, a.supplier_name order by a.supplier_name";
				}
				elseif($knit_source==1)
				{
					$sql_party="select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $knitting_company_cond_comp $company_cond order by comp.company_name";
				}

				$all_party=sql_select($sql_party);


                    $i=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0;
					foreach($all_party as $party)//($j=0;$j<=count($all_party)-1;$j++)
					{
						$party_name=$party[csf('id')];

						if($knit_source==1)
							$knitting_party=$company_arr[$party_name];
						else if($knit_source==3)
							$knitting_party=$supplier_arr[$party_name];
						else
							$knitting_party="&nbsp;";

						echo '<tr bgcolor="#EFEFEF"><td colspan="22"><b>Party name: '.$knitting_party.'</b></td></tr>';

						if (str_replace("'","",$txt_challan)=="") $issue_challan_cond=""; else $issue_challan_cond=" and e.challan_no=$txt_challan";

						if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and e.knit_dye_source=$cbo_knitting_source";
						if ($party_name=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and e.knit_dye_company in ($party_name)";

							 if($db_type==0)
							 {
								 $sql_job="select a.id, a.trans_id, a.po_breakdown_id,
									CASE WHEN a.entry_form='3' and a.trans_type=2 THEN group_concat(a.po_breakdown_id) END AS yarn_order_id,
									CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN group_concat(a.po_breakdown_id) END AS grey_order_id,
									CASE WHEN a.entry_form='9' and a.trans_type=4 THEN group_concat(a.po_breakdown_id) END AS yarn_return_order_id ,
									b.id, group_concat(distinct(b.po_number)) as po_number, b.po_quantity,
									c.job_no, c.buyer_name, c.style_ref_no,
									d.id as trans_id, d.cons_uom, d.requisition_no, d.brand_id, d.cons_quantity as issue_qnty, d.return_qnty,
									e.issue_number, e.buyer_id, e.booking_id, e.booking_no, e.buyer_id, e.issue_date, e.challan_no, e.issue_basis,
									f.product_name_details, f.lot
									from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e, product_details_master f
									where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,3,9,22) and a.status_active=1 and a.is_deleted=0
									and a.po_breakdown_id=b.id and b.status_active=1
									and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
									and a.trans_id=d.id and d.item_category=1 and d.transaction_type=2 and d.status_active=1 and d.is_deleted=0
									and d.mst_id=e.id and e.entry_form in (2,3,9,22)  and e.status_active=1 and e.is_deleted=0
									and d.prod_id=f.id $knit_source_cond_party $knit_company_cond_party $job_no_cond $issue_challan_cond $issue_date_cond $internal_ref_cond
									group by c.job_no";

							 }
							 else if($db_type==2)
							 {
									$sql_job="select  min(a.trans_id) as trans_id ,sum(b.po_quantity) as po_quantity,
									c.job_no, c.buyer_name, c.style_ref_no,
									min(d.cons_uom) as cons_uom, max(d.requisition_no) as requisition_no, min(d.brand_id) as brand_id, sum(d.cons_quantity) as issue_qnty, sum( d.return_qnty) as return_qnty,
									e.issue_number, min(e.booking_id),min(e.booking_no), max(e.issue_date) as issue_date, min(e.challan_no) as challan_no,min(e.issue_basis) as issue_basis,
									f.product_name_details, min(f.lot) as lot ,
									listagg( CASE WHEN a.entry_form='3' and a.trans_type=2 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_order_id, 						listagg(CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS grey_order_id,
									listagg(CASE WHEN a.entry_form='9' and a.trans_type=4 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_return_order_id,
									b.po_number
									from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e, product_details_master f
									where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,3,9,22) and a.status_active=1 and a.is_deleted=0
									and a.po_breakdown_id=b.id and b.status_active=1
									and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
									and a.trans_id=d.id and d.item_category=1 and d.transaction_type=2 and d.status_active=1 and d.is_deleted=0
									and d.mst_id=e.id and e.entry_form in (2,3,9,22) and e.issue_date between '$from_date' and '$to_date' and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party $knit_company_cond_party $job_no_cond $issue_challan_cond $issue_date_cond $internal_ref_cond group by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.issue_number order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.issue_number ";
							 }

						//echo $sql_job;die;//and e.issue_date between '$from_date' and '$to_date'

						$result_job=sql_select($sql_job); $job_array=array();
						foreach($result_job as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($row[csf('issue_basis')]==1)
								$booking_reqsn_no=$row[csf('booking_no')];
							else if($row[csf('issue_basis')]==3)
								$booking_reqsn_no=$row[csf('requisition_no')];
							else
								$booking_reqsn_no="&nbsp;";


							$balance=$balance+$row[csf('issue_qnty')];
                    		$tot_iss_qnty=$tot_iss_qnty+$row[csf('issue_qnty')];

							$po_num=$row[csf('po_number')];
							$po_number=implode(",",array_unique(explode(",",$po_num)));


							$order_nos=''; $order_qnty=0;
							if(!in_array($row[csf('job_no')],$job_array))
							{
								if($i!=1)
								{
								?>
									<tr class="tbl_bottom">
                                        <td colspan="9" align="right"><b>Job Total</b></td>
                                        <?
										//$po_qty_tot=0;
										?>
                                        <td align="right"><? echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right" colspan="4">&nbsp;</td>
                                        <td align="right"><? echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_qnty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_balance,2,'.',''); ?>&nbsp;</td>
                                    </tr>
							<?
									unset($po_qty_tot);
									unset($issue_qty_tot);
									unset($returnable_qnty_tot);
									unset($balance_qty_tot);
									unset($returnable_balance);
								}
							?>
								<tr><td colspan="22" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $row[csf('job_no')]; ?></b></td></tr>
							<?
								$job_array[$i]=$row[csf('job_no')];
							}

							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="70" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row[csf('issue_number')]; ?></div></td>
								<td width="100">&nbsp;</td>
								<td width="100"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
								<td width="120"><p>&nbsp;<? echo $booking_reqsn_no; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                <td width="130"><div style="word-wrap:break-word; width:130px"><? echo $row[csf('style_ref_no')]; ?></div></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $po_number; ?></div></td>
								<td width="90" align="right"><? echo number_format($row[csf('po_quantity')],0,'.',''); ?></td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
								<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
                                <td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?></td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
								<td width="100" align="right"><? echo number_format($balance,2,'.',''); ?></td>
                                <td align="right"><? $return_balance=$row[csf('return_qnty')]; echo number_format($return_balance,2,'.',''); ?></td>
							</tr>
						<?
							$po_qty_tot+=$row[csf('po_quantity')];
							$issue_qty_tot+=$row[csf('issue_qnty')];
							$balance_qty_tot+=$balance;

							$returnable_qnty_tot+=$row[csf('return_qnty')];
							$tot_returnable_qnty+=$row[csf('return_qnty')];
							$returnable_balance+=$row[csf('return_qnty')];
							$tot_returnable_balance+=$row[csf('return_qnty')];
							$i++;
						}

						if (str_replace("'","",$txt_challan)=="") $recissue_challan_cond=""; else $recissue_challan_cond=" and a.yarn_issue_challan_no=$txt_challan";

						//echo $query="select a.recv_number, a.buyer_id, a.booking_no, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, b.id as trans_id, b.cons_uom,  b.brand_id, b.cons_quantity, b.cons_reject_qnty, c.product_name_details, c.lot from inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d, wo_po_break_down e where b.id=d.trans_id and d.trans_type in (1,4) and d.entry_form in (2,22,9) and d.po_breakdown_id=e.id and a.item_category in(1,13) and a.entry_form in(2,22,9) and a.company_id=$cbo_company_name and a.knitting_source=$cbo_knitting_source and a.knitting_company=$party_name  and a.id=b.mst_id and b.item_category in(1,13) and b.transaction_type in(1,4) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $recissue_challan_cond order by b.transaction_type, a.receive_date";//and a.receive_date between '$from_date' and '$to_date'

						if ($knit_source==0) $knit_source_cond_party_rec=""; else $knit_source_cond_party_rec=" and e.knitting_source in ($knit_source)";
						if ($party_name=='') $knit_company_cond_party_rec=""; else  $knit_company_cond_party_rec=" and e.knitting_company in ($party_name)";

						if($db_type==0)
						{
							$query="select a.trans_id, b.po_number, sum(b.po_quantity) as po_quantity,
								c.job_no,c.style_ref_no, c.buyer_name, c.style_ref_no,
								d.cons_uom,d.requisition_no, d.brand_id, sum(d.cons_quantity) as receive_qnty, sum( d.return_qnty) as return_qnty, sum(d.cons_quantity) as cons_quantity, sum(d.cons_reject_qnty) as cons_reject_qnty,
								e.booking_id, max(e.receive_date) as receive_date, e.challan_no, e.receive_basis, e.recv_number,
								group_concat(e.yarn_issue_challan_no) as yarn_issue_challan_no,
								group_concat(e.item_category) as item_category,
								group_concat(e.booking_no) as booking_no,
								f.product_name_details,
								group_concat(f.lot) as lot,
								CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  group_concat(a.po_breakdown_id) END  AS grey_order_id,
								CASE WHEN a.entry_form='9' and a.trans_type=4 THEN group_concat(a.po_breakdown_id) END  AS yarn_return_order_id
								from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_receive_master e, product_details_master f
								where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,22,9) and a.status_active=1 and a.is_deleted=0
								and a.po_breakdown_id=b.id and b.status_active=1
								and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
								and a.trans_id=d.id and d.item_category=1 and d.transaction_type in (1,4) and d.status_active=1 and d.is_deleted=0
								and d.mst_id=e.id and e.entry_form in (2,22,9)  and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party_rec $knit_company_cond_party_rec $job_no_cond $recissue_challan_cond  $recv_date_cond group by c.job_no,c.style_ref_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number, e.item_category order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number";
						}
						elseif($db_type==2)
						{
							$query="select  min(a.trans_id) as trans_id, b.po_number, sum(b.po_quantity) as po_quantity,
								c.job_no, c.buyer_name, c.style_ref_no,
								min(d.cons_uom) as cons_uom, max(d.requisition_no) as requisition_no, min(d.brand_id) as brand_id, sum(d.cons_quantity) as receive_qnty, sum(d.cons_quantity) as cons_quantity, sum(d.cons_reject_qnty) as cons_reject_qnty, sum( d.return_qnty) as return_qnty,
								min(e.booking_id), max(e.receive_date) as receive_date, min(e.challan_no) as challan_no, min(e.receive_basis) as receive_basis, e.recv_number,
								listagg(CAST(e.yarn_issue_challan_no as varchar2(4000)),',') within group (order by e.yarn_issue_challan_no) as yarn_issue_challan_no,
								listagg(CAST(e.item_category as varchar2(4000)),',') within group (order by e.item_category) as item_category,
								listagg(CAST(e.booking_no as varchar2(4000)),',') within group (order by e.booking_no) as booking_no, e.item_category,
								f.product_name_details,
								listagg(CAST(f.lot as varchar2(4000)),',') within group (order by f.lot) as lot,
								listagg(CASE WHEN a.entry_form in (2,22) and a.trans_type=1 THEN  a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS grey_order_id,
								listagg(CASE WHEN a.entry_form='9' and a.trans_type=4 THEN a.po_breakdown_id END,',') within group (order by a.po_breakdown_id)  AS yarn_return_order_id
								from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_receive_master e, product_details_master f
								where a.trans_id<>0 and a.quantity>0 and a.entry_form in (2,22,9) and a.status_active=1 and a.is_deleted=0
								and a.po_breakdown_id=b.id and b.status_active=1
								and b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and c.company_name=$cbo_company_name
								and a.trans_id=d.id and d.item_category=1 and d.transaction_type in (1,4) and d.status_active=1 and d.is_deleted=0
								and d.mst_id=e.id and e.entry_form in (2,22,9)  and e.status_active=1 and e.is_deleted=0 and d.prod_id=f.id $knit_source_cond_party_rec $knit_company_cond_party_rec $job_no_cond $recissue_challan_cond  $recv_date_cond group by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number, e.item_category order by c.job_no, c.buyer_name, c.style_ref_no, b.po_number, f.product_name_details, e.recv_number";
						}
						$result2=sql_select($query); //$job_rec_array=array();
						foreach($result2 as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($row[csf('item_category')]==13)
							{
								$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['grey_recv']);
								$tot_recv_qnty+=$row[csf('cons_quantity')];
								$tot_rej_qnty+=$row[csf('cons_reject_qnty')];
								$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
							}
							else
							{
								$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_return']);
								$tot_ret_qnty+=$row[csf('cons_quantity')];
								$tot_reject_yarn_qnty+=$row[csf('cons_reject_qnty')];
								$balance=$balance-($row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')]);
							}

							$order_nos=''; $order_qnty=0;
							foreach($all_po_id as $po_id)
							{
								if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
								$order_qnty+=$po_arr[$po_id]['qnty'];
							}
							$po_number=implode(",",array_unique(explode(",",$row[csf('po_number')])));
							$returnable_tot+=$row[csf('return_qnty')];
							$grand_tot_balance+=$row[csf('cons_quantity')]+$row[csf('cons_reject_qnty')];

							if(!in_array($row[csf('job_no')],$job_rec_array))
							{
								if($i!=1)
								{
								?>
									<tr class="tbl_bottom">
                                        <td colspan="9" align="right"><b>Job Total</b></td>
                                        <?
										//$po_qty_tot=0;
										?>
                                        <td align="right"><? //echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right" colspan="4">&nbsp;</td>
                                        <td align="right"><? echo number_format($returnable_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($receive_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($tot_returnable_balance,2,'.',''); ?>&nbsp;</td>
                                    </tr>
								<?
									unset($po_qty_tot);
									unset($receive_qty_tot);
									unset($returnable_tot);
									unset($balance_qty_tot);
									unset($tot_returnable_balance);
								}
							?>
								<tr><td colspan="22" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo $row[csf('job_no')]; ?></b></td></tr>
							<?
								$job_rec_array[$i]=$row[csf('job_no')];
							}
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row[csf('recv_number')]; ?></div></td>
								<td width="100"><p>&nbsp;<? echo $row[csf('challan_no')]; ?></p></td>
								<td width="100"><p>&nbsp;<? echo $row[csf('yarn_issue_challan_no')]; ?></p></td>
								<td width="120"><p>&nbsp;<? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                <td width="130"><div style="word-wrap:break-word; width:130px"><? echo $row[csf('style_ref_no')]; ?></div></td>
								<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $po_number; ?></div></td>
								<td width="90" align="right"><? echo number_format($row[csf('po_quantity')],0,'.',''); ?>&nbsp;</td>
								<td width="80"><p>&nbsp;<? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
								<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
								<td width="100" align="right">&nbsp;</td>
                                <td width="100" align="right"><? echo number_format($row[csf('return_qnty')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right"><? if($row[csf('item_category')]==13) echo number_format($row[csf('cons_quantity')],2,'.',''); $fab_rec_tot=$row[csf('cons_quantity')]; ?></td>
								<td width="100" align="right"><? if($row[csf('item_category')]==13) echo number_format($row[csf('cons_reject_qnty')],2,'.',''); ?></td>
								<td width="100" align="right"><? if($row[csf('item_category')]==1) echo number_format($row[csf('cons_quantity')],2,'.',''); ?></td>
								<td width="100" align="right"><? if($row[csf('item_category')]==1) echo number_format($row[csf('cons_reject_qnty')],2,'.',''); ?></td>
								<td width="100" align="right"><? echo number_format($balance,2,'.',''); ?></td>
                                <td align="right"><? $return_balance=$row[csf('return_qnty')]-$row[csf('cons_quantity')]; echo number_format($return_balance,2,'.',''); ?></td>
							</tr>
						<?
							$po_qty_tot+=$row[csf('po_quantity')];
							$receive_qty_tot+=$row[csf('receive_qnty')];
							$balance_qty_tot+=$balance;
							$tot_returnable_qnty+=$row[csf('return_qnty')];
							$returnable_balance+=$row[csf('return_qnty')]-$row[csf('cons_quantity')];
							$tot_returnable_balance+=$row[csf('return_qnty')]-$row[csf('cons_quantity')];
							$grand_tot_balance+=$balance_qty_tot;
							$i++;
						}
					}
                    ?>
                        <tr class="tbl_bottom">
                            <td colspan="9" align="right"><b>Job Total</b></td>
                            <?
							unset($tot_returnable_balance);
                            //$po_qty_tot=0;
                            ?>
                            <td align="right"><? //echo number_format($po_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right" colspan="4">&nbsp;</td>
                            <td align="right"><? echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($returnable_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? //echo number_format($issue_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($balance_qty_tot,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($tot_returnable_balance,2,'.',''); ?>&nbsp;</td>
                        </tr>
                    <tfoot>
                        <th colspan="14" align="right">Total</th>
                        <th align="right"><? echo number_format($tot_iss_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_returnable_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_recv_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_rej_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_ret_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_reject_yarn_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_iss_qnty-($tot_recv_qnty+$tot_rej_qnty+$tot_ret_qnty+$tot_reject_yarn_qnty),2); ?></th>
                        <th align="right"><? echo number_format($tot_returnable_qnty-$tot_ret_qnty,2); ?></th>
                    </tfoot>
                </table>
            </div>
        </div>
	<?

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

if($action=="report_generate_excel")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");



	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	elseif($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}
	$knitting_company=str_replace("'","",$txt_knitting_com_id);
	$type=str_replace("'","",$type);
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond=""; else $knitting_source_cond=" and a.knit_dye_source=$cbo_knitting_source";
	if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_rec_cond=""; else $knitting_source_rec_cond=" and a.knitting_source=$cbo_knitting_source";
	if ($knitting_company=='') $knitting_company_cond=""; else  $knitting_company_cond="  and a.knit_dye_company in ($knitting_company)";



	ob_start();

		$po_arr=array();
		$datapoArray=sql_select("select id, job_no_mst, po_number, po_quantity from wo_po_break_down");

		foreach($datapoArray as $row)
		{
			$po_arr[$row[csf('id')]]['job']=$row[csf('job_no_mst')];
			$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
		}
		if($db_type==0) $grpby_field="group by trans_id";
		if($db_type==2) $grpby_field="group by trans_id,entry_form,trans_type";
		else $grpby_field="";

		if (str_replace("'","",$txt_challan)=="") $challan_cond=""; else $challan_cond=" and a.issue_number_prefix_num=$txt_challan";

		$order_nos_array=array();
		if($db_type==0)
		{
			$datapropArray=sql_select("select trans_id,
				CASE WHEN entry_form='3' and trans_type=2 THEN group_concat(po_breakdown_id) END AS yarn_order_id,
				CASE WHEN entry_form='9' and trans_type=4 THEN group_concat(po_breakdown_id) END AS yarn_return_order_id
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (3,9) and status_active=1 and is_deleted=0 group by trans_id");
		}
		else
		{
			$datapropArray=sql_select("select trans_id,
				listagg(CASE WHEN entry_form='3' and trans_type=2 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_order_id,
				listagg(CASE WHEN entry_form='9' and trans_type=4 THEN po_breakdown_id END,',') within group (order by po_breakdown_id) AS yarn_return_order_id
				from order_wise_pro_details where trans_id<>0 and quantity>0 and entry_form in (3,9) and status_active=1 and is_deleted=0 group by trans_id,entry_form,trans_type");
		}

		foreach($datapropArray as $row)
		{
			$order_nos_array[$row[csf('trans_id')]]['yarn_issue']=$row[csf('yarn_order_id')];
			$order_nos_array[$row[csf('trans_id')]]['yarn_return']=$row[csf('yarn_return_order_id')];
		}



		if (str_replace("'","",$cbo_knitting_source)==0) $knitting_source_cond_party=""; else $knitting_source_cond_party=" and knit_dye_source=$cbo_knitting_source";
		if ($knitting_company=='') $knitting_company_cond_party=""; else  $knitting_company_cond_party=" and a.id in ($knitting_company)";
		if ($knitting_company=='') $knitting_company_cond_comp=""; else  $knitting_company_cond_comp=" and id in ($knitting_company)";
		$knit_source=str_replace("'","",$cbo_knitting_source);
		if ($knit_source==0) $knit_source_cond_party=""; else $knit_source_cond_party=" and a.knit_dye_source in ($knit_source)";
		if ($knitting_company=='') $knit_company_cond_party=""; else  $knit_company_cond_party=" and a.knit_dye_company in ($knitting_company)";
		if ($from_date!='' && $to_date!='') $iss_date_cond=" and a.issue_date between '$from_date' and '$to_date'"; else $iss_date_cond="";


		$sql="select a.id as issue_id, a.issue_number, a.issue_number_prefix_num, a.issue_purpose, a.buyer_id, a.knit_dye_company, a.knit_dye_source, a.booking_id, a.booking_no, a.issue_date, a.issue_basis, b.id as trans_id, b.requisition_no, b.supplier_id, b.cons_quantity as issue_qnty, b.return_qnty, c.yarn_count_id, c.yarn_type, c.lot
		from inv_issue_master a, inv_transaction b, product_details_master c
		where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.return_qnty!=0 $knit_source_cond_party  $knit_company_cond_party $challan_cond $iss_date_cond order by a.knit_dye_company, a.issue_number_prefix_num";
		$result=sql_select($sql);
		$all_issue_data=array();
		foreach($result as $row)
		{
			$all_po_id=explode(",",$order_nos_array[$row[csf('trans_id')]]['yarn_issue']);
			$all_job_no=""; $order_nos=''; $order_qnty=0;
			foreach($all_po_id as $po_id)
			{
				if($order_nos=='') $order_nos=$po_arr[$po_id]['name']; else $order_nos.=",".$po_arr[$po_id]['name'];
				if($all_job_no=='') $all_job_no=$po_arr[$po_id]['job']; else $all_job_no.=",".$po_arr[$po_id]['job'];
				$order_qnty+=$po_arr[$po_id]['qnty'];
			}
			$job_no=implode(",",array_unique(explode(",",$all_job_no)));
			$issue_id_arr[$row[csf("issue_id")]]=$row[csf("issue_id")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number"]=$row[csf("issue_number")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_number_prefix_num"]=$row[csf("issue_number_prefix_num")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_purpose"]=$row[csf("issue_purpose")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["buyer_id"]=$row[csf("buyer_id")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
			if($row[csf("issue_basis")]==1 || $row[csf("issue_basis")]==4)
			{
				$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_id"]=$row[csf("booking_id")];
				$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_no"]=$row[csf("booking_no")];
			}
			else
			{
				$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_id"]=$row[csf("requisition_no")];
				$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["booking_no"]=$row[csf("requisition_no")];
			}

			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_date"]=$row[csf("issue_date")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_basis"]=$row[csf("issue_basis")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["trans_id"]=$row[csf("trans_id")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["issue_qnty"]=$row[csf("issue_qnty")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["returnable_qnty"]=$row[csf("return_qnty")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_count_id"]=$row[csf("yarn_count_id")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["yarn_type"]=$row[csf("yarn_type")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["lot"]=$row[csf("lot")];
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["job_no"]=$job_no;
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["order_nos"]=$order_nos;
			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["type"]="2";

			$all_issue_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("trans_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$issue_purpose[$row[csf("issue_number_prefix_num")]]=$row[csf("issue_purpose")];
		}



		$receive_ret_array=array();
		 $sql_return="select a.recv_number, a.knitting_source as knit_dye_source, a.knitting_company as knit_dye_company, a.booking_no, a.buyer_id, a.receive_date,a.recv_number, a.item_category, b.issue_challan_no as issue_number_prefix_num, b.issue_id, b.id as trans_id, c.supplier_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, c.yarn_count_id, c.yarn_type, c.lot
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and  a.entry_form=9 and a.company_id=$cbo_company_name and  b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// echo $sql_return;
		$sql_return_result=sql_select($sql_return);
		foreach($sql_return_result as $row)
		{

			$issue_ret_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("lot")]]["return_qnty"]+=$row[csf("cons_quantity")];
			$issue_ret_data[$row[csf("knit_dye_company")]][$row[csf("issue_number_prefix_num")]][$row[csf("lot")]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];

		}


		if($knit_source==1){
			$knitting_party=$company_arr;
		}
		else if($knit_source==3) {
			$knitting_party=$supplier_arr;
		}


		?>
        <div>
            <table width="1380" cellpadding="0" cellspacing="0" id="caption" align="left" style="font-size:16px">
                <tr>
                   <td align="center" colspan="17"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" colspan="17"><strong><? echo $report_title; ?> (Returnable Without Challan)</strong></td>
                </tr>
                <tr>
                   <td align="center" colspan="17"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
            </table>
            <br />
            <table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Party name</th>
                    <th width="100">Challan No</th>
                    <th width="100">Issue Purpose</th>
                    <th width="100">Job No.</th>
                    <th width="100">Order No</th>
                    <th width="100">Buyer</th>
                    <th width="50">Count</th>
                    <th width="100">Supplier</th>
                    <th width="100">Type</th>
                    <th width="50">Lot</th>
                    <th width="100">Booking/Reqsn. No</th>
                    <th width="60">Issue Qty.</th>
                    <th width="60">Returnable Qty.</th>
                    <th width="60">Returned Qty</th>
                    <th width="60">Reject Qty</th>
                    <th width="">Returnable Balanace</th>
                </thead>
            </table>
            <div style="width:1398px; overflow-y: scroll; max-height:380px;" id="scroll_body">
                <table width="1380" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
				<?




				$i=1; $k=1; $j=1; $balance=0; $tot_iss_qnty=0; $tot_recv_qnty=0; $tot_rej_qnty=0; $tot_ret_qnty=0; $tot_reject_yarn_qnty=0; $challan_array=array(); $party_array=array();
				foreach($all_issue_data as $knit_company=>$knit_company_data)
				{
					foreach($knit_company_data as $issue_chalan_no=>$issue_chalan_no_data)
					{
						foreach($issue_chalan_no_data as $trans_id=>$value)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$return_balance=($value['returnable_qnty']-($value['return_qnty']+$value['cons_reject_qnty']));
							?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="100"><? echo $knitting_party[$knit_company];?></td>
                                <td width="100"><? echo $issue_chalan_no; ?></td>
                                <td width="100"><? echo $yarn_issue_purpose[$issue_purpose[$issue_chalan_no]]; ?></td>                                <td width="100"><p><? echo $value['job_no']; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $value['order_nos']; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $buyer_arr[$value['buyer_id']]; ?>&nbsp;</p></td>
                                <td width="50"><p><? echo $count_arr[$value['yarn_count_id']]; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $supplier_arr[$value['supplier_id']]; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $yarn_type[$value['yarn_type']]; ?>&nbsp;</p></td>
                                <td width="50"><p><? echo $value['lot']; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $value['booking_no']; ?>&nbsp;</p></td>
                                <td width="60" align="right"><? echo number_format($value['issue_qnty'],2,'.',''); ?></td>
                                <td width="60" align="right"><? echo number_format($value['returnable_qnty'],2,'.',''); ?></td>
                                <td width="60" align="right"><? echo number_format($issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"],2);//echo number_format($value['return_qnty'],2,'.',''); ?></td>
                                <td width="60" align="right"><? echo number_format($issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"],2);//number_format($value['cons_reject_qnty'],2,'.',''); ?></td>
                                <td align="right"><?
								$tot_ret_rej_qty = $issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"]+ $issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"];
								$balance=$return_balance-$tot_ret_rej_qty;
								echo number_format($balance,2,'.','');
								 ?></td>

                            </tr>
							<?
							$i++;
							$challan_issue_qnty+=$value['issue_qnty'];
							$challan_returnable_qnty+=$value['returnable_qnty'];
							$challan_return_qnty+=$issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["return_qnty"];
							$challan_cons_reject_qnty+=$issue_ret_data[$knit_company][$issue_chalan_no][$value['lot']]["cons_reject_qnty"];
							$challan_return_balance+=$balance;

						}
					}
				}

				?>


			<tfoot>
                <th colspan="12" align="right"><b>Total</b></th>
                <th align="right"><? echo number_format($challan_issue_qnty,2,'.',''); ?></th>
                <th align="right"><? echo number_format($challan_returnable_qnty,2,'.',''); ?></th>
                <th align="right"><? echo number_format($challan_return_qnty,2,'.',''); ?></th>
                <th align="right"><? echo number_format($challan_cons_reject_qnty,2,'.',''); ?></th>
                <th align="right"><? echo number_format($challan_return_balance,2,'.',''); ?></th>
			</tfoot>
		</table>
	</div>
    </div>
    <?



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



?>
