<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/cm_gmt_class.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer", "id", "buyer_name");
$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$item_library = return_library_array("select id,item_name from  lib_item_group", "id", "item_name");
$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");


if ($db_type == 2) $select_date = " to_char(a.insert_date,'YYYY')";
else if ($db_type == 0) $select_date = " year(a.insert_date)";

if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", 0);
    exit();
}


if ($action == "report_button_setting") {
    $print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=11 and report_id=62 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('$print_report_format');\n";
}


if ($action == "job_popup") {
    echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(str) {
            $("#hide_job_no").val(str);
            parent.emailwindow.hide();
        }
    </script>
    <?
    if ($buyer_id == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")"; else $buyer_cond = "";
        } else {
            $buyer_cond = "";
        }
    } else {
        $buyer_cond = " and a.buyer_name=$buyer_id";
    }
    if (trim($cbo_year) != 0) {
        if ($db_type == 0) {
            $year_cond = " and YEAR(insert_date)=$cbo_year";
            $year_field = "YEAR(insert_date)";
        } else {
            $year_cond = " and to_char(insert_date,'YYYY')=$cbo_year";
            $year_field = "to_char(insert_date,'YYYY')";
        }
    } else $year_cond = "";

    $arr = array(2 => $company_library, 3 => $buyer_arr);
    $sql = "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a where a.company_name=$company_id $buyer_cond $year_cond order by a.id";
    //echo $sql;
    echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100", "570", "320", 0, $sql, "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr, "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "", "setFilterGrid('list_view',-1)", '0,0,0,0,0');
    echo "<input type='hidden' id='hide_job_no' />";

    exit();
}


//if($action=="quotation_popup")
if($action=="quotation_popup")
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
                    <th id="search_by_td_up" width="170">Please Enter Style </th>
                    <th>Quot. Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
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
                       		$search_by_arr=array(1=>"Style Ref",2=>"Inquery Id",3=>"Quotation Id",4=>"Mkt No");
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_quotation_no_search_list_view', 'search_div', 'cost_breakup_report_woven_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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
if ($action == "create_quotation_no_search_list_view") {
    $data = explode('**', $data);
    $company_id = $data[0];
    //echo $data[1];
    if ($data[1] == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "") $buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")"; else $buyer_id_cond = "";
        } else {
            $buyer_id_cond = "";
        }
    } else {
        $buyer_id_cond = " and a.buyer_id=$data[1]";
    }
    $search_by = $data[2];
    $search_string = "%" . trim($data[3]) . "%";
    $search_field_cond = "";
    if ($data[3] != "") {
        if ($search_by == 1)
            $search_field_cond = " and a.style_ref LIKE '%" . trim($data[3]) . "%'";
        else if ($search_by == 2)
            $search_field_cond = " and a.inquery_id='" . trim($data[3]) . "'";
        else if ($search_by == 3)
            $search_field_cond = " and a.id=" . trim($data[3]) . "";
        else if ($search_by == 4)
            $search_field_cond = " and a.mkt_no='" . trim($data[3]) . "'";
    }

    $start_date = trim($data[4]);
    $end_date = trim($data[5]);
    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and a.quot_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
        } else {
            $date_cond = "and a.quot_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
        }
    } else {
        $date_cond = "";
    }
    if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
    else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
    $arr = array(0 => $company_library, 1 => $buyer_arr);
    $sql = "select a.id, $year_field a.inquery_id, a.company_id, a.buyer_id,a.quot_date, a.style_ref from wo_price_quotation a where  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $buyer_id_cond $date_cond order by a.id, a.quot_date";
    echo create_list_view("tbl_list_search", "Company,Buyer,Year,Quotation No,Style Ref.,Inquery ID, Quotation Date", "80,130,50,60,130,130", "760", "220", 0, $sql, "js_set_value", "id,id", "", 1, "company_id,buyer_id,0,0,0,0,0", $arr, "company_id,buyer_id,year,id,style_ref,inquery_id,quot_date", "", '', '0,0,0,0,0,0,3', '', 1);
    exit();
}//Order Search End

if($action=="style_refarence_search")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Job Year </th>
					 <th>Search By </th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
						<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" ); ?></td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'create_list_style_search', 'search_div', 'cost_breakup_report_woven_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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

//style search------------------------------//
if ($action == "create_list_style_search") {
    extract($_REQUEST);
    //echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    list($company, $buyer, $search_type, $search_value, $cbo_year, $txt_style_ref_no, $txt_style_ref_id, $txt_style_ref) = explode('**', $data);

    $buyer = str_replace("'", "", $buyer);
    $company = str_replace("'", "", $company);
    $cbo_year = str_replace("'", "", $cbo_year);
    if (trim($cbo_year) != 0) {
        if ($db_type == 0) {
            $year_cond = " and YEAR(a.insert_date)=$cbo_year";
        } else {
            $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
        }
    }
    if ($db_type == 0) if ($cbo_year != 0) $job_cond = " and year(a.insert_date)='$cbo_year'";
    else if ($cbo_year != 0) $job_cond = " and to_char(a.insert_date,'YYYY')='$cbo_year'";

    if ($search_type == 1 && $search_value != '') {
        $search_con = " and a.job_no like('%$search_value')";

    } else if ($search_type == 2 && $search_value != '') {
        $search_con = " and a.style_ref_no like('%$search_value%')";
    }

    //echo $search_type;

    if ($buyer != 0) $buyer_cond = "and a.buyer_name=$buyer"; else $buyer_cond = "";
    $sql = "select a.id,a.style_ref_no,a.quotation_id,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a where a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num";
    //echo $sql; die;
    echo create_list_view("list_view", "Style Ref No,QuotationID,Job No,Year", "160,100,90,100", "500", "200", 0, $sql, "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,quotation_id,job_no_prefix_num,job_year", "", "setFilterGrid('list_view',-1)", "0", "", 1);
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    ?>
    <script language="javascript" type="text/javascript">
        var style_no = '<? echo $txt_style_ref_no;?>';
        var style_id = '<? echo $txt_style_ref_id;?>';
        var style_des = '<? echo $txt_style_ref;?>';
        //alert(style_id);
        if (style_no != "") {
            style_no_arr = style_no.split(",");
            style_id_arr = style_id.split(",");
            style_des_arr = style_des.split(",");
            var str_ref = "";
            for (var k = 0; k < style_no_arr.length; k++) {
                str_ref = style_no_arr[k] + '_' + style_id_arr[k] + '_' + style_des_arr[k];
                js_set_value(str_ref);
            }
        }
    </script>
    <?
    exit();
}

if($action=="order_search")
{

	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_no').val( num );
		}
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th>
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td" width="130">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>', 'order_search_list_view', 'search_div', 'cost_breakup_report_woven_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if ($action == "order_search_list_view") {
    extract($_REQUEST);
    //echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    list($company, $buyer, $search_type, $search_value, $start_date, $end_date, $cbo_year_selection, $txt_style_ref, $style_ref_id) = explode('**', $data);

    $buyer = str_replace("'", "", $buyer);
    $company = str_replace("'", "", $company);
    $txt_style_ref = str_replace("'", "", $txt_style_ref);
    $style_ref_id = str_replace("'", "", $style_ref_id);
    $cbo_year = str_replace("'", "", $cbo_year_selection);
    if (trim($cbo_year) != 0) {
        if ($db_type == 0) {
            $year_cond = " and YEAR(b.insert_date)=$cbo_year";
        } else {
            $year_cond = " and to_char(b.insert_date,'YYYY')=$cbo_year";
        }
    }

    if ($search_type == 1 && $search_value != '') {
        $search_con = " and a.po_number like('%$search_value')";
    } elseif ($search_type == 2 && $search_value != '') {
        $search_con = " and a.style_ref_no like('%$search_value')";
    } elseif ($search_type == 3 && $search_value != '') {
        $search_con = " and a.job_no_mst like('%$search_value')";
    }


    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and a.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and a.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
        }
    } else {
        $date_cond = "";
    }

    //echo $txt_style_ref.'='.$style_ref_id;
    $job_style_cond = "";
    if (trim(str_replace("'", "", $txt_style_ref)) != "") {
        if (str_replace("'", "", $style_ref_id) != "") {
            $job_style_cond = " and b.id in(" . str_replace("'", "", $style_ref_id) . ") ";
        } else {
            $job_style_cond = " and b.style_ref_no like '%" . trim(str_replace("'", "", $txt_style_ref)) . "%'";
        }
    }

    if ($buyer != 0) $buyer_cond = "and b.buyer_name=$buyer"; else $buyer_cond = "";
    if ($txt_style_ref != "") {
        //if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref) and year(b.insert_date)= '$cbo_year' ";
        //else $style_cond="and b.job_no_prefix_num in($txt_style_ref) and to_char(b.insert_date,'YYYY')= '$cbo_year' ";
    }
    //else $style_cond="";

    //echo $style_cond."jahid";die;
    $sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.quotation_id,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $year_cond $job_style_cond $search_con $date_cond and a.status_active=1";
    //echo $sql;
    echo create_list_view("list_view", "Order NO,Job No,QuotationId,Year,Style Ref No", "150,80,70,70,150", "570", "150", 0, $sql, "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,quotation_id,job_year,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    ?>
    <script language="javascript" type="text/javascript">
        var style_no = '<? echo $txt_order_id_no;?>';
        var style_id = '<? echo $txt_order_id;?>';
        var style_des = '<? echo $txt_order;?>';
        //alert(style_id);
        if (style_no != "") {
            style_no_arr = style_no.split(",");
            style_id_arr = style_id.split(",");
            style_des_arr = style_des.split(",");
            var str_ref = "";
            for (var k = 0; k < style_no_arr.length; k++) {
                str_ref = style_no_arr[k] + '_' + style_id_arr[k] + '_' + style_des_arr[k];
                js_set_value(str_ref);
            }
        }
    </script>
    <?
    exit();
}
if($action=="order_search_file")
{

	echo load_html_head_contents("File  No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	//echo "DDDDDDDD";
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_no').val( num );
		}
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th>
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No",4=>"File No");
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td" width="130">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>', 'file_order_search_list_view', 'search_div', 'cost_breakup_report_woven_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if ($action == "file_order_search_list_view") {
    extract($_REQUEST);
    //echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    list($company, $buyer, $search_type, $search_value, $start_date, $end_date, $cbo_year_selection, $txt_style_ref, $style_ref_id) = explode('**', $data);

    $buyer = str_replace("'", "", $buyer);
    $company = str_replace("'", "", $company);
    $txt_style_ref = str_replace("'", "", $txt_style_ref);
    $style_ref_id = str_replace("'", "", $style_ref_id);
    $cbo_year = str_replace("'", "", $cbo_year_selection);
    if (trim($cbo_year) != 0) {
        if ($db_type == 0) {
            $year_cond = " and YEAR(b.insert_date)=$cbo_year";
        } else {
            $year_cond = " and to_char(b.insert_date,'YYYY')=$cbo_year";
        }
    }

    if ($search_type == 1 && $search_value != '') {
        $search_con = " and a.po_number like('%$search_value')";
    } elseif ($search_type == 2 && $search_value != '') {
        $search_con = " and a.style_ref_no like('%$search_value')";
    } elseif ($search_type == 3 && $search_value != '') {
        $search_con = " and a.job_no_mst like('%$search_value')";
    } elseif ($search_type == 4 && $search_value != '') {
        $search_con = " and a.file_no like('%$search_value%')";
    }


    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and a.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and a.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
        }
    } else {
        $date_cond = "";
    }

    //echo $txt_style_ref.'='.$style_ref_id;
    $job_style_cond = "";
    if (trim(str_replace("'", "", $txt_style_ref)) != "") {
        if (str_replace("'", "", $style_ref_id) != "") {
            $job_style_cond = " and b.id in(" . str_replace("'", "", $style_ref_id) . ") ";
        } else {
            $job_style_cond = " and b.style_ref_no like '%" . trim(str_replace("'", "", $txt_style_ref)) . "%'";
        }
    }

    if ($buyer != 0) $buyer_cond = "and b.buyer_name=$buyer"; else $buyer_cond = "";
    if ($txt_style_ref != "") {
        //if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref) and year(b.insert_date)= '$cbo_year' ";
        //else $style_cond="and b.job_no_prefix_num in($txt_style_ref) and to_char(b.insert_date,'YYYY')= '$cbo_year' ";
    }
    //else $style_cond="";

    //echo $style_cond."jahid";die;
    $sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,a.file_no,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $year_cond $job_style_cond $search_con $date_cond and a.status_active=1";
    //echo $sql;
    echo create_list_view("list_view", "Order NO,File No,Job No,Year,Style Ref No", "150,100,80,70,150", "600", "150", 0, $sql, "js_set_value", "id,file_no", "", 1, "0", $arr, "po_number,file_no,job_no_prefix_num,job_year,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    ?>
    <script language="javascript" type="text/javascript">
        var style_no = '<? echo $txt_order_id_no;?>';
        var style_id = '<? echo $txt_order_id;?>';
        var style_des = '<? echo $txt_order;?>';
        //alert(style_id);
        if (style_no != "") {
            style_no_arr = style_no.split(",");
            style_id_arr = style_id.split(",");
            style_des_arr = style_des.split(",");
            var str_ref = "";
            for (var k = 0; k < style_no_arr.length; k++) {
                str_ref = style_no_arr[k] + '_' + style_id_arr[k] + '_' + style_des_arr[k];
                js_set_value(str_ref);
            }
        }
    </script>
    <?
    exit();
}

if($action=="season_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Season No Info", "../../../../", 1, 1,'','','');
	?>
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		var selected_name = new Array;var selected_id = new Array;
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
			//alert(str);
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_name ) == -1 ) {
				selected_name.push( str[1] );
				selected_id.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_name.length; i++ ) {
					if( selected_name[i] == str[1] ) break;
				}
				selected_name.splice( i, 1 );
				selected_id.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_name.length; i++ ) {
				name += selected_name[i] + ',';
				id += selected_id[i] + ',';
			}

			name = name.substr( 0, name.length - 1 );
			id = id.substr( 0, id.length - 1 );

			$('#hide_season').val( name );
			$('#hide_season_id').val( id );
		}
    </script>


    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:350px;">
                    <input type="text" name="hide_season" id="hide_season" value="" />
                     <input type="text" name="hide_season_id" id="hide_season_id" value="" />
                    <?
                        if($buyerID==0)
                        {
                            if ($_SESSION['logic_erp']["data_level_secured"]==1)
                            {
                                if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
                            }
                            else $buyer_id_cond="";
                        }
                        else $buyer_id_cond=" and a.buyer_name=$buyerID";

                       // if($job_no!=0) $jobno=" and job_no_prefix_num in (".$job_no.")"; else $jobno="";

                           $sql="select distinct(b.season_name) as season,b.id from wo_po_details_master a,lib_buyer_season b where a.season_matrix=b.id and a.status_active=1 and a.is_deleted=0 and a.company_name=$companyID $jobno  $buyer_id_cond order by b.season_name";
						   //$sql="select distinct(season) as season from lib_buyer_season where status_active=1 and is_deleted=0  $buyer_id_cond order by season";

                        //echo $sql;
                        echo create_list_view("tbl_list_search", "Season", "200","300","280",0, $sql , "js_set_value", "season,id", "", 1, "0", $arr , "season", "","",'0','',1) ;
                        ?>
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

			$reporttype=str_replace("'","",$reporttype);
			$cbo_company_name=str_replace("'","",$cbo_company_name);
			$cbo_style_owner=str_replace("'","",$cbo_style_owner);
			$txt_order=str_replace("'","",$txt_order);
			$txt_order_id=str_replace("'","",$txt_order_id);
			$file_no=str_replace("'","",$txt_file_no);
			$file_no=rtrim($file_no,',');
			$txt_style_ref=str_replace("'","",$txt_style_ref);
			$txt_season_id=str_replace("'","",$txt_season_id);
			$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
			$style_ref_id=str_replace("'","",$txt_style_ref_id);
			$txt_quotation_id=str_replace("'","",$txt_quotation_id);
			$txt_hidden_quot_id=str_replace("'","",$txt_hidden_quot_id);
			$comments_head=str_replace("'","",$comments_head);
			if($txt_hidden_quot_id!='')
			{
				$qoutation_id=$txt_hidden_quot_id;
			}
			else
			{
				$qoutation_id=$txt_quotation_id;//implode(",",array_unique(explode("*",$txt_quotation_id)));
			}

			if($reporttype!=5 && $reporttype!=6) //Quotation Button
			{
				if($qoutation_id!='' && str_replace("'","",$sign)==1 )
				{
					echo "<p style='font-size:30px; color:red', align='center'>Search by Quotation Id  is not allowed for this button.<p/>";die;
				}
			}

			if($reporttype==5 || $reporttype==6) //Quotation Button
			{
				if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_id='$cbo_company_name' ";
				if($qoutation_id=="") $qoutation_id_cond=""; else $qoutation_id_cond=" and a.id in($qoutation_id)";
				if(trim($txt_style_ref)!="")
				{
					$quot_style_cond=" and a.style_ref = '".trim($txt_style_ref)."'";
				}
				else $quot_style_cond="";
			}
			else
			{
				if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
			}

			if(str_replace("'","",$cbo_buyer_name)==0)
			{
				if ($_SESSION['logic_erp']["data_level_secured"]==1)
				{
					if($_SESSION['logic_erp']["buyer_id"]!="")
					{
						$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
						$buyer_id_cond2=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
					}
					else{
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
				$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
				$buyer_id_cond2=" and buyer_id=$cbo_buyer_name";
			}

			$job_style_cond="";
			if(trim(str_replace("'","",$txt_style_ref))!="")
			{
				if(str_replace("'","",$style_ref_id)!="")
				{
					$job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
				}
				else
				{
					$job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
				}
			}

			$order_cond="";
			if(trim(str_replace("'","",$txt_order))!="")
			{
				if(str_replace("'","",$txt_order_id)!="")
				{
					$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
				}
				else
				{
					$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
				}
			}
					$season_cond2=$season_cond='';
					if($txt_season_id!="")
					{
						$season_cond="and a.season_matrix in($txt_season_id)";
						$season_cond2="and season_buyer_wise in($txt_season_id)";
						//
					}

			ob_start();

			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
			$supplier_library_fabric=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"); //b.party_type in(1,9) and

	if($reporttype==1) //Budget Button
	{
		$sql="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond order  by b.id";
			//echo $sql; die;
				$sql_po_result=sql_select($sql);
				$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer="";
				$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;
				//echo $buyer_name;die;

				foreach($sql_po_result as $row)
				{
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
					if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
					if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
					if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
					if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
					if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];

					$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$total_order_qty+=$row[csf('po_quantity')];
					$total_unit_price+=$row[csf('unit_price')];
					$total_fob_value+=$row[csf('po_total_price')];
					$po_qty_by_job[$row[csf("job_no")]]=$row[csf('po_quantity')]*$row[csf('ratio')];
				}
				//print_r($po_qty_by_job);
				$all_job_no=array_unique(explode(",",$all_full_job));
				$all_jobs="";
				foreach($all_job_no as $jno)
				{
					if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
				}
			//echo $all_jobs;
				$financial_para=array();
				$sql_std_para=sql_select("select cost_per_minute,applying_period_date as from_period_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0  order by id desc");
				foreach($sql_std_para as $row)
				{
					$period_date=date("m-Y", strtotime($row[csf('from_period_date')]));
					$financial_para[$period_date]['cost_per_minute']=$row[csf('cost_per_minute')];
				}
				   $nameArray=sql_select( "select commercial_cost_method,id,commercial_cost_percent from  variable_order_tracking where company_name=$cbo_company_name and variable_list=27 order by id" );
				   $commercial_cost_method=$commercial_cost_percent=0;
				   foreach($nameArray as $row)
					{
						$commercial_cost_method=$row[csf('commercial_cost_method')];
						$commercial_cost_percent=$row[csf('commercial_cost_percent')];
					}
					//echo $commercial_cost_method.'=';


				$sql_pre="select a.job_no,a.costing_date,a.machine_line as machine_line,a.job_no, a.prod_line_hr, a.sew_smv, a.sew_effi_percent as sew_effi_percent, a.budget_minute,b.cost_pcs_set,b.price_pcs_or_set from wo_pre_cost_mst a,wo_pre_cost_dtls b where  a.job_no=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in(".$all_full_job.")  order  by a.id";
				    $sql_pre_result=sql_select($sql_pre);
					$sew_smv='';$machine_line='';$prod_line_hr='';$prod_line_hr='';$sew_effi_percent='';$budget_minute=0;
				  foreach($sql_pre_result as $row)
					{
							$machine_line.=$row[csf("machine_line")].',';
							$prod_line_hr.=$row[csf("prod_line_hr")].',';
							$sew_smv.=$row[csf("sew_smv")].',';
							$sew_effi_percent.=$row[csf("sew_effi_percent")].',';
							$smv_avg_by_job[$row[csf("job_no")]]=$row[csf("sew_smv")];
							$efficincy_hr_mc_by_job[$row[csf("job_no")]]=$row[csf("machine_line")].'**'.$row[csf("prod_line_hr")];
							$smv_avg_by_job[$row[csf("job_no")]]=$row[csf("sew_smv")];
							$costing_date=date("m-Y", strtotime($row[csf('costing_date')]));
							$cost_per_minute.=$financial_para[$costing_date]['cost_per_minute'].',';
							//$price_pcs_or_set+=$row[csf('price_pcs_or_set')];
							//$cost_pcs_set+=$row[csf('cost_pcs_set')];
					}
					//print_r($smv_avg_by_job);
					//echo $sew_smv;
					//print_r($costing_date_arr);

					$condition= new condition();
					$condition->company_name("=$cbo_company_name");
				  if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if($txt_order_id!='' || $txt_order_id!=0)
				 {
					$condition->po_id("in($txt_order_id)");
				 }
				 if(str_replace("'","",$txt_style_ref)!='')
				 {
					$condition->job_no("in($all_jobs)");
				 }
				 if(str_replace("'","",$file_no)!='')
				 {
					$condition->file_no("in($file_no)");
				 }
				$condition->init();
				$fabric= new fabric($condition);
				$yarn= new yarn($condition);
				//echo $yarn->getQuery();die;
				$conversion= new conversion($condition);
				$trim= new trims($condition);
				$emblishment= new emblishment($condition);
				$wash= new wash($condition);
				$commercial= new commercial($condition);
				$commission= new commision($condition);

				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
				$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
				$yarn_data_arr=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
				//$yarn_fabric_cost_data_arr=$yarn->get_By_Precostfabricdtlsid_YarnAmountArray();
				$yarn_fabric_cost_data_arr=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$conv_data_qty_arr=$conversion->getQtyArray_by_conversionid();
				$conv_data_amount_arr=$conversion->getAmountArray_by_conversionid();
				$other= new other($condition);
                //echo $other->getQuery(); die;
				$other_costing_arr=$other->getAmountArray_by_order();
				$conversion_costing_arr=$conversion->getAmountArray_by_order();
				$trim_arr_qty=$trim->getQtyArray_by_precostdtlsid();
				$trim_arr_amount=$trim->getAmountArray_precostdtlsid();
				$trims_costing_arr=$trim->getAmountArray_by_order();
				$trim= new trims($condition);
				$trims_item_qty_arr=$trim->getQtyArray_by_itemidAndDescription();
				$trim= new trims($condition);
				$trims_item_amount_arr=$trim->getAmountArray_by_itemidAndDescription();
				$emblishment_qty_arr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
				$emblishment_amount_arr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();

				$emblishment_qty_name_type_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtype();
				$emblishment_amount_name_type_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtype();
				$wash_qty_arr=$wash->getQtyArray_by_jobAndEmblishmentid();
				$wash_amount_arr=$wash->getAmountArray_by_jobAndEmblishmentid();
				$wash_type_name_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtype();
				$wash_type_name_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
				$wash_costing_arr=$wash->getAmountArray_by_order();
				$commercial_amount_arr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$commercial_item_amount_arr=$commercial->getAmountArray_by_jobAndItemid();
				$commission_amount_arr=$commission->getAmountArray_by_jobAndPrecostdtlsid();
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commission_costing_sum_arr=$commission->getAmountArray_by_order();
				$commission_costing_item_arr=$commission->getAmountArray_by_jobAndItemid();

				$total_job_unit_price=($total_fob_value/$total_order_qty);
				  $sql_fab="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, c.job_no,c.item_number_id, c.body_part_id as body_id, c.fab_nature_id as nat_id, c.color_type_id as color_type, c.fabric_description as fab_desc, c.avg_cons,c.uom, c.fabric_source as fab_source, c.rate, c.amount, c.avg_finish_cons, c.status_active, c.nominated_supp from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond order  by b.id,c.fab_nature_id, c.fabric_description,c.uom";
				  $sql_fabs_result=sql_select($sql_fab);
				  $fabric_detail_arr=array();  $fabric_job_check_arr=array();
				$total_purchase_amt=0;

				foreach($sql_fabs_result as $row)
				{
					$row[csf("fab_source")]=$row[csf("fab_source")];
					$item_desc= $body_part[$row[csf("body_id")]].",".$color_type[$row[csf("color_type")]].",".$row[csf("fab_desc")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("style_ref_no")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['amount'] += $row[csf("amount")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("style_ref_no")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['rate'] += $row[csf("rate")];
					if($fabric_detail_arr[$row[csf("nat_id")]][$row[csf("style_ref_no")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['supplier'] == ''){
						$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("style_ref_no")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['supplier'] = $supplier_library_fabric[$row[csf("nominated_supp")]];
					}
					else{
						$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("style_ref_no")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['supplier'] .= $supplier_library_fabric[$row[csf("nominated_supp")]].',';
					}

					//$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]][$row[csf("uom")]]['rate']=$row[csf("rate")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("style_ref_no")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['pre_fab_id'].=$row[csf("id")].',';

					if($row[csf("fab_source")]==2)
					{
						$total_purchase_amt+=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
					}
				}
				$styleRef=explode(",",$txt_style_ref);
				$all_style_job="";
				foreach($styleRef as $sid)
				{
						if($all_style_job=="") $all_style_job="'".$sid."'"; else $all_style_job.=","."'".$sid."'";
				}
				$fabric_rowspan_arr=array();$uom_rowspan_arr=array();
				foreach($fabric_detail_arr as $fab_nat_key=>$fab_style_data)
				{
					foreach ($fab_style_data as $fab_style_key => $fab_data) {
						$fabrice_rowspan=0;
						foreach($fab_data as $uom_key=>$uom_data)
						{
							$uom_rowspan=0;
							foreach($uom_data as $desc_key=>$desc_data)
							{

								foreach($desc_data as $source_key=>$val)
								{
									$uom_rowspan++;
									$fabrice_rowspan++;
								}

								$fabric_rowspan_arr[$fab_style_key]=$fabrice_rowspan;
								$uom_rowspan_arr[$fab_style_key][$uom_key]=$uom_rowspan;
							}
						}
					}
				}


				$style1="#E9F3FF";
				$style="#FFFFFF";

				$sql_yarn="select min(c.id) as id,c.fabric_cost_dtls_id,c.count_id, c.copm_one_id, c.percent_one,c.color,c.type_id,min(c.cons_ratio) as cons_ratio,sum(c.cons_qnty) as cons_qnty,sum(c.amount) as amount,c.rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_yarn_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond group by c.count_id,c.fabric_cost_dtls_id, c.copm_one_id, c.percent_one,  c.color,c.type_id, c.rate order  by c.count_id, c.copm_one_id,c.percent_one";

					$result_yarn=sql_select($sql_yarn);
					$yarn_detail_arr=array();
					$yarnamount=$total_yarn_costing=0;
					$row_span = 0;
					foreach($result_yarn as $row)
					{
						$item_descrition = $lib_yarn_count[$row[csf("count_id")]]."_".$composition[$row[csf("copm_one_id")]]."_".$row[csf("percent_one")]."%_".$color_library[$row[csf("color")]]."_".$yarn_type[$row[csf("type_id")]];

						$total_yarn_costing += $yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];
						$row_span+=1;

						$yarn_detail_arr[$item_descrition]['rate']=$row[csf("rate")];
						$yarn_detail_arr[$item_descrition]['count_id']=$row[csf("count_id")];
						$yarn_detail_arr[$item_descrition]['copm_one_id']=$row[csf("copm_one_id")];
						$yarn_detail_arr[$item_descrition]['percent_one']=$row[csf("percent_one")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarn_detail_arr[$item_descrition]['type_id']=$row[csf("type_id")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];

						//$yarnamount=$yarnamount=$yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];
						//$yarncons_qntys=$yarnamount=$yarn_fabric_cost_data_arr[$row[csf("id")]]['qty'];
						$yarnamount=$yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];
						$yarncons_qntys=$yarn_fabric_cost_data_arr[$row[csf("id")]]['qty'];
						$yarn_detail_arr[$item_descrition]['yarn_cost']+=$yarnamount;
						$yarn_detail_arr[$item_descrition]['yarn_qty']+=$yarncons_qntys;

						$totalyarn_detail_arr[100]['amount']+=$yarnamount;
					}


							$machine_line=rtrim($machine_line,',');
							$machine_line=implode(",",array_unique(explode(",",$machine_line)));
							$prod_line_hr=rtrim($prod_line_hr,',');
							$prod_line_hr=implode(",",array_unique(explode(",",$prod_line_hr)));
							$sew_effi_percent=rtrim($sew_effi_percent,',');
							$sew_effi_percent=implode(",",array_unique(explode(",",$sew_effi_percent)));
							$cost_per_minute=rtrim($cost_per_minute,',');
							$cost_per_minute=implode(",",array_unique(explode(",",$cost_per_minute)));
							$sew_smv=rtrim($sew_smv,',');
							$sew_smv=implode(",",array_unique(explode(",",$sew_smv)));
							$po_ids=array_unique(explode(",",$all_po_id));
						  $total_embell_cost=$total_cm_cost=$total_lab_test_cost=$total_inspection_cost=$total_currier_cost=$total_certificate_cost=$total_common_oh_cost=$total_freight_cost=$total_wash_costing=0;
						  $total_commisssion=$total_fabric_amt=$total_conversion_cost=$total_trims_amt=$total_embl_amt=$total_comercial_amt=$total_commisssion=0;
						  $foreign=0;$local=$total_studio_cost=$total_design_cost=0;
						 // print_r($po_ids);
						  foreach($po_ids as $pid)
						  {

							   $foreign_local=$commission_costing_sum_arr[$pid];
								$total_wash_costing+=$wash_costing_arr[$pid];
								$total_commisssion+=$foreign_local;
							    $total_embl_amt+=$emblishment_costing_arr[$pid];
								$total_comercial_amt+=$commercial_costing_arr[$pid];
								$tot_fabric=array_sum($fabric_costing_arr['knit']['grey'][$pid])+array_sum($fabric_costing_arr['woven']['grey'][$pid]);
							    $total_fabric_amt+=$tot_fabric;
								$conversion_costing=array_sum($conversion_costing_arr[$pid]);
								$yarn_costing=$yarn_costing_arr[$pid];

								$total_conversion_cost+=$conversion_costing;
							    $total_trims_amt+=$trims_costing_arr[$pid];

								//$total_raw_metarial_cost=$total_finish_amt+$total_embl_amt+$total_trims_amt;
								$total_cm_cost+=$other_costing_arr[$pid]['cm_cost'];
								$total_lab_test_cost+=$other_costing_arr[$pid]['lab_test'];
								$total_inspection_cost+=$other_costing_arr[$pid]['inspection'];
								$total_currier_cost+=$other_costing_arr[$pid]['currier_pre_cost'];
								$total_certificate_cost+=$other_costing_arr[$pid]['certificate_pre_cost'];
								$total_studio_cost+=$other_costing_arr[$pid]['studio_cost'];
								$total_design_cost+=$other_costing_arr[$pid]['design_cost'];
								$total_common_oh_cost+=$other_costing_arr[$pid]['common_oh'];
								$total_freight_cost+=$other_costing_arr[$pid]['freight'];
						  }

						$total_raw_metarial_cost= $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt;
						$total_all_cost= $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt+$total_embl_amt+$total_comercial_amt+$total_commisssion+$total_wash_costing+$total_cm_cost+$total_lab_test_cost+$total_inspection_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost+$total_freight_cost;
						 // echo number_format($total_commisssion,2);

                        $total_cost_title= "fabric + yarn + conversion + trims + embl + comercial + commission + wash + CM + lab + inspection + Currier + Certificate + OH + freight";

					 $sql_commi="select c.id, c.job_no, c.particulars_id,c.commission_base_id,avg(c.commision_rate) as rate, sum(c.commission_amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_commiss_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.commission_base_id>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond group by c.id, c.job_no, c.particulars_id,c.commission_base_id order by c.id";
					$result_commi=sql_select($sql_commi);
					$commi_detail_arr=array();$tot_commission_rate=0; $commiamount_local = 0;

					foreach($result_commi as $row)
					{

						$commi_rowspan+=1;
						$commi_detail_arr[$row[csf("particulars_id")]]['particulars_id']=$row[csf("particulars_id")];
						$commi_detail_arr[$row[csf("particulars_id")]]['amount']=$row[csf("amount")];
						$commi_detail_arr[$row[csf("particulars_id")]]['rate']=$row[csf("rate")];
						$commi_detail_arr[$row[csf("particulars_id")]]['job_no'].=$row[csf("job_no")].',';
						$commi_detail_arr[$row[csf("particulars_id")]]['commission_base_id']=$row[csf("commission_base_id")];
						//$emblishment_qty_arr
						$commiamount=$commission_costing_item_arr[$row[csf("job_no")]][$row[csf("particulars_id")]];
						$totalcommi_detail_arr[100]['amount']+=$commiamount;
						$tot_commission_rate+=$row[csf("rate")];

						if($row[csf("particulars_id")] == 2){
							$commiamount_local += $commission_costing_item_arr[$row[csf("job_no")]][$row[csf("particulars_id")]];
						}

					}

					$sql_comm="select c.id, c.job_no, c.item_id,avg(c.rate) as rate,sum(c.rate) as tot_rate, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_comarci_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond group by c.id, c.job_no, c.item_id  order by c.id";

					$result_comm=sql_select($sql_comm);
					$comm_detail_arr=array();$tot_comm_rate=0;
					foreach($result_comm as $row)
					{
						$item_descrition =$row[csf("description")];
						$comm_rowspan+=1;
						$comm_detail_arr[$row[csf("item_id")]]['item_id']=$row[csf("item_id")];
						$comm_detail_arr[$row[csf("item_id")]]['amount']=$row[csf("amount")];
						$comm_detail_arr[$row[csf("item_id")]]['rate']=$row[csf("rate")];
						$comm_detail_arr[$row[csf("item_id")]]['job_no'].=$row[csf("job_no")].',';
						$comm_detail_arr[$row[csf("item_id")]]['desc']=$item_descrition;
						//$emblishment_qty_arr
						$commamount+=$commercial_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						$totalcomm_detail_arr[100]['amount']+=$commamount;
						$tot_comm_rate+=$row[csf("rate")];
					}
					//echo $commercial_cost_method.'DD';
					$tot_commercial_cost_amount=$total_comercial_amt=0;
					if($commercial_cost_method==1)
					{
						 $tot_commercial_cost_amount=$total_yarn_costing+$total_trims_amt+$total_purchase_amt;
						 $total_comercial_amt=($tot_commercial_cost_amount*$tot_comm_rate)/100;
					}
					else if($commercial_cost_method==2)// On Selling
					{
						  $tot_commercial_cost_amount=($total_job_unit_price*$commercial_cost_percent)/100;
						   $total_comercial_amt=$tot_commercial_cost_amount;
					}
					else if($commercial_cost_method==3) // Net Selling
					{
					 	$net_commi_rate=$total_job_unit_price-$tot_commission_rate;
					 	 $tot_commercial_cost_amount=($net_commi_rate*$commercial_cost_percent)/100;
						$total_comercial_amt=$tot_commercial_cost_amount;

					}
					else if($commercial_cost_method==5)
					{
					 	 $tot_commercial_cost_amount=$total_embl_amt+$total_trims_amt+$total_purchase_amt+$total_wash_costing+$total_lab_test_cost+$total_inspection_cost+$total_cm_cost+$total_freight_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost+$total_studio_cost+$total_design_cost;
						$total_comercial_amt=($tot_commercial_cost_amount*$commercial_cost_percent)/100;
					}



		?>
        <div style="width:100%">
        <style>
		@media print {
			  #page_break_div {
				page-break-before: always;
			  }

				.footer_signature {
				position:fixed;
				height:auto;
				bottom:0;
				width:100%;
				}
			}
		</style>


             <table width="600px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="8" align="center"><strong style=" font-size:18px"><? echo $report_title;?></strong></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" class="form_caption"><strong><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong></td>
                </tr>
            </table>
            <table width="780" style="margin-left:10px;" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
	            <tr>
	                <th  colspan="2" align="center" style="font-size:16px"> <strong>Summary</strong></th>
	            </tr>
	            <tr>
		            <td style="border:none">
		            	<table width="780"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		                    <tr  bgcolor="<? echo $style; ?>">
		                        <td width="120"> <strong>Buyer</strong> </td>
		                        <td width=""><? if($cbo_buyer_name==0) echo implode(",",array_unique(explode(",",$all_buyer)));else echo $buyer_arr[$cbo_buyer_name];?> </td>
		                        <td width="140" ><strong>Sew. SMV(Avg).</strong></td>
		                        <td width="" title="SMV*PO Qty/Total PO Qty">&nbsp; <?
									$tot_avg_sew_smv=0;
									foreach($po_qty_by_job as $jobno=>$poQty)
									{
										$smv_avg=$smv_avg_by_job[$jobno];
										//echo $po_qty_by_job[$jobno].'='.$jobno;
										//echo $poQty.',';
										$tot_avg_sew_smv+=($poQty*$smv_avg)/$order_qty_pcs;
									}
									echo number_format($tot_avg_sew_smv,2);
									$available_min=$prod_min=0;
									foreach($efficincy_hr_mc_by_job as $jobno=>$mc_hr)
									{
										$mc_hr_data=explode("**",$mc_hr);
										//echo $mc_hr_data[0].'m'.$mc_hr_data[1];
										$prd_min_smv_avg=$smv_avg_by_job[$jobno];
										$mc_no=$mc_hr_data[0];
										$hr_line_no=$mc_hr_data[1];
										$available_min+=$mc_no*10*60;
										$prod_min+=($hr_line_no*10)*$prd_min_smv_avg;
									}
								//$efficincy_hr_mc_by_job[$row[csf("job_no")]];

								?> </td>
		                    </tr>
		                    <tr  bgcolor="<? echo $style1; ?>">
		                        <td width="120"> <strong>Job No.</strong> </td>
		                        <td width=""><? echo implode(",",array_unique(explode(",",$all_job)));?></td>
		                        <td width="140"><strong>Sew Efficiency(Avg)%</strong></td>
		                        <td width="" title="<? echo 'Prod Min='.$prod_min.'/Avilable Min='.$available_min?>"><? echo number_format($prod_min/$available_min,2);?></td>
		                    </tr>
		                     <tr  bgcolor="<? echo $style; ?>">
		                        <td width="120"><strong>Style Ref.</strong></td>
		                        <td width=""><? echo implode(",",array_unique(explode(",",$all_style)));?></td>
		                        <td width="140"> <strong>Style Desc.</strong> </td>
		                        <td width=""><? echo implode(",",array_unique(explode(",",$all_style_desc)));?></td>
		                    </tr>
		                    <tr><td colspan="4">&nbsp;</td></tr>
		                     <td>
		                     <tr  bgcolor="<? echo $style1; ?>">
		                        <td width="140"><strong>Avg FOB/UNIT Price[$]</strong></td>
		                        <td width=""><? echo number_format($total_job_unit_price,2); ?></td>
		                        <td width="140"> <b>Total CM Cost[$] : </b></td>
		                        <td>  <? echo number_format($total_cm_cost,2);?></td>
		                    </tr>
		                    <tr  bgcolor="<? echo $style; ?>">
		                        <td width="100"><strong>Total Qty.(Pcs)</strong></td>
		                        <td><? echo $order_qty_pcs;?></td>
		                        <td width="140"><b>Fabric Cost[$]:</b></td>
		                        <td  align="left" title="Fabric + Conversion + Yarn"><? echo number_format($total_fabric_amt+$total_conversion_cost+$total_yarn_costing,2);?></td>


		                    </tr>
		                     <tr  bgcolor="<? echo $style; ?>">
		                     	<td width="140"><b>Total FOB[$]:</b></td>
		                        <td  align="left">  <? echo number_format($total_fob_value,2);?></td>
		                        <td width="140"><b>Trims Cost[$]:</b></td>
		                        <td  align="left"><? echo number_format($total_trims_amt,2);?></td>
		                         </td>
		                    </tr>
		                    <tr bgcolor="<? echo $style1?>"  align="left">
		                        <td width="100"><b>Total Cost[$] :</b></td>
		                        <td title="<? echo $total_cost_title; ?>">  <?
								 echo number_format($total_all_cost,2);?></td>
								<td width="140"><b>Commercial Cost[$]</b></td>
		                        <td  align="left"><? echo number_format($commamount,2);?></td>
		                    </tr>
		                     <tr bgcolor="<? echo $style ?>" align="left">
		                         <td width="140"><b>Total Profit[$]</b></td>
		                         <td title="Total Fob-Total Cost">  <?  $total_margin=$total_fob_value-$total_all_cost;
								 echo number_format($total_fob_value-$total_all_cost,2);?></td>
								 <td width="140"><b>Operating Cost[$]</b></td>
		                        <td  align="left"><? echo number_format($total_common_oh_cost,2);?></td>
		                    </tr>
		                    <tr bgcolor="<? echo $style ?>" align="left">
		                    	<td>&nbsp;</td><td>&nbsp;</td>
								 <td width="140"><b>Emblishment Cost[$]</b></td>
		                        <td  align="left"> <? echo number_format($total_embl_amt+$total_wash_costing+$total_lab_test_cost+$total_inspection_cost+$total_freight_cost+$total_currier_cost+$total_certificate_cost,2);?></td>
		                    </tr>
                            <tr bgcolor="<? echo $style ?>" align="left">
                                <td>&nbsp;</td><td>&nbsp;</td>
                                 <td width="140"><b>Commision Cost[$]</b></td>
                                <td  align="left"> <? echo number_format($total_commisssion,2);?></td>
                            </tr>
                            <? //echo $total_embl_amt.'='.$total_wash_costing.'='.$total_lab_test_cost.'='.$total_inspection_cost.'='.$total_freight_cost.'='.$total_currier_cost.'='.$total_certificate_cost.'='.$total_common_oh_cost; die;
                            //echo $total_wash_costing.'+'.$total_lab_test_cost.'+'.$total_inspection_cost.'+'.$total_freight_cost.'+'.$total_currier_cost.'+'.$total_certificate_cost.'+'.$$total_common_oh_cost;  ?>

		                </table>
		             </td>
	            </tr>
            </table>
            <br>
           <div id="page_break_div">

            </div>
           <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="text-align: center;">PO Details</b></caption>
					<thead>
                    	<th width="50">Job No</th>
						<th width="100">PO Number</th>
                    	<th width="100">PO Qty.[Pcs]</th>
                        <th width="100">FOB/Pcs</th>
                        <th width="100">FOB Value[$]</th>
                        <th width="100">CM/Pcs[$]</th>
						<th width="100">CM Value</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner"  style="width:800px;margin-left:10px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body1">
					<table class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$i=1;$total_order_qty_pcss=0;$total_fob_val=$total_cm_value=0;$total_po_qty=0;
					foreach($sql_po_result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_qty_pcss=$row[csf('po_quantity')]*$row[csf('ratio')];
						$avg_unit=$row[csf('unit_price')];

					?>

						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="50"  align="center"><? echo $row[csf('job_prefix')]; ?></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('po_number')]; ?></div></td>
							<!--
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')],0); ?></div></td>
							<td width="60" align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td> -->
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($order_qty_pcss,0) ?></div></td>
                             <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($avg_unit,2); ?></div></td>

                            <td width="100" align="right"><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')]*$avg_unit,2); ?></div></td>
                            <td width="100" align="right"><div style="word-break:break-all"><? echo number_format($other_costing_arr[$row[csf('po_id')]]['cm_cost']/$row[csf('po_quantity')],2); ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo number_format($other_costing_arr[$row[csf('po_id')]]['cm_cost'],2); ?></div></td>
                            </tr>

                            <?
							$total_fob_val+=$row[csf('po_quantity')]*$avg_unit;
							$total_po_qty+=$row[csf('po_quantity')];
							$total_order_qty_pcss+=$order_qty_pcss;
							$total_cm_value+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];

							$i++;

					}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="2"><strong>Total</strong> </th>
                            <th align="right"><strong><? echo number_format($total_po_qty,0);?> </strong></th>
                            <th align="right"></strong></th>
                            <th align="right"><strong><? echo number_format($total_fob_val,2);?></strong> </th>
                            <th align="right"><strong></th>
							<th align="right"><strong><? echo number_format($total_cm_value,2);?></strong> </th>
                            </tr>
                            </tfoot>

                    </table>
                    </div>
           <br/><br/>
           <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="text-align: center;">Fabric Details</b></caption>
					<thead>
                    	<th width="200">Style Ref.</th>
                        <th width="200">Nominated Supplier Name</th>
						<th width="60">Fin. Qty </th>
                        <th width="60">UOM</th>
                        <th width="60">Amount[$]</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:800px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table"   width="780" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$i=$m=1;$total_greycons=$total_fincons=$total_amount=$grand_total_greycons=$grand_total_fincons=$grand_total_amount=0;
					foreach($fabric_detail_arr as $fab_nat_key=>$fab_style_data)
					{
						foreach($fab_style_data as $fab_style_key => $fab_data)
						{
							foreach($fab_data as $uom_key=>$uom_data)
							{
								$y=1;
								foreach($uom_data as $desc_key=>$desc_data)
								{

									foreach($desc_data as $source_key=>$val)
									{
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$pre_fab_id=rtrim($val['pre_fab_id'],',');
										$pre_fab_ids=array_unique(explode(",",$pre_fab_id));
										$rate=$val['rate'];
										//$amount=$val['amount'];
										$fincons=$greycons=$amount=0;
										foreach($pre_fab_ids as $fab_id)
										{
											if($fab_nat_key==2) //Purchase
											{
												$fincons+=$fabric_qty['knit']['finish'][$fab_id][$uom_key];
												$greycons+=$fabric_qty['knit']['grey'][$fab_id][$uom_key];
												$amount+=$fabric_amount['knit']['grey'][$fab_id][$uom_key];
											}
											else
											{
												$fincons+=$fabric_qty['woven']['finish'][$fab_id][$uom_key];
												$greycons+=$fabric_qty['woven']['grey'][$fab_id][$uom_key];
												$amount+=$fabric_amount['woven']['grey'][$fab_id][$uom_key];
											}
										}
										?>

										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">  <?
			                      	 	if($y==1){ ?>
											<td width="200" rowspan="<? echo $uom_rowspan_arr[$fab_style_key][$uom_key];?>">
											<? echo $fab_style_key//$item_category[$fab_nat_key]; ?></td><?
										}
										?>
										<td width="200" align="center"><? echo $val['supplier']; ?></td>
			                            <td width="60" title="" align="right"><div style="word-break:break-all"><? echo number_format($fincons,4); ?></div></td>

			                            <td width="60" align="center"><? echo $unit_of_measurement[$uom_key]; ?></td>
			                            <td width="60"  align="right"><div style="word-break:break-all"><? echo number_format($amount,4); ?></div></td>
			                            </tr>
	                            		<?
										$total_greycons+=$greycons;
										$total_fincons+=$fincons;
										$total_amount+=$amount;

										$grand_total_greycons+=$greycons;
										$grand_total_fincons+=$fincons;
										$grand_total_amount+=$amount;
										$y++;
										$i++;
									}
								}
									$m++;
							}
						}

					}
						?>
                            <tfoot>
                            <tr>
                            <th colspan="2" ><strong>Grand Total</strong> </th>
                            <th align="right"><strong><? echo number_format($grand_total_fincons,4);?> </strong></th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><strong><? echo number_format($grand_total_amount,4);?></strong> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div><!--Fabtic Details End-->
            <br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="text-align: center;">Trims Cost Details</b></caption>
					<thead>
                        <th width="250">Item Group</th>
						<th width="200">Nominated Supplier Name</th>
                        <th width="100">Amount($)</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:800px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$trim_group_arr=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name" );
				 	$sql_trims="select c.trim_group,c.description,c.cons_uom, c.nominated_supp from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_trim_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond group by  c.trim_group,c.description,c.cons_uom,c.nominated_supp  order by c.trim_group";
					$result_trims=sql_select($sql_trims);
					$trims_detail_arr=array();
                    $k=0;
					foreach($result_trims as $row)
					{
						/*$item_descrition =$row[csf("description")];
						$trims_rowspan+=1;
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['nominated_supp']=$row[csf("nominated_supp")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['uom']=$row[csf("cons_uom")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['trim_group']=$row[csf("trim_group")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['amount']=$row[csf("amount")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['rate']=$row[csf("rate")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['id'].=$row[csf("id")].',';
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['desc']=$item_descrition;*/
                        //if($k != 0) $add_comm = ','; else $add_comm = ' ';
                        $item_descrition =$row[csf("description")];
                        $trims_rowspan+=1;
                        $trims_detail_arr[$row[csf("nominated_supp")]]['nominated_supp']=$row[csf("nominated_supp")];
                        $trims_detail_arr[$row[csf("nominated_supp")]]['uom']=$row[csf("cons_uom")];
                        $trims_detail_arr[$row[csf("nominated_supp")]]['trim_group'][]=$trim_group_arr[$row[csf("trim_group")]];
                        if($trims_item_amount_arr[$row[csf("trim_group")]][$row[csf("description")]] != 'inf' || $trims_item_amount_arr[$row[csf("trim_group")]][$row[csf("description")]] == ''){
                            $trims_detail_arr[$row[csf("nominated_supp")]]['amount']+=$trims_item_amount_arr[$row[csf("trim_group")]][$row[csf("description")]];
                            }
                            else{
                                $trims_detail_arr[$row[csf("nominated_supp")]]['amount'] += 0;
                            }
                        $trims_detail_arr[$row[csf("nominated_supp")]]['rate']+=$row[csf("rate")];
                        $trims_detail_arr[$row[csf("nominated_supp")]]['id'].=$row[csf("id")].',';
                        $trims_detail_arr[$row[csf("nominated_supp")]]['desc']=$item_descrition;

					}
                    /*echo '<pre>';
                    print_r($trims_detail_arr); die;*/
                    $i=$z=1;$grand_total_trim_amount=0;
                    foreach ($trims_detail_arr as $key => $value) {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $trim_amount = $value['amount']
                     ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrim_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trtrim_<? echo $i; ?>">
                            <td width="250"><div style="word-break:break-all"><? echo implode (", ", $value['trim_group']); ?></div></td>
                            <td width="200" align="right" ><div style="word-break:break-all"><? echo $supplier_library_fabric[$key]; ?></div>
                            <td width="100"  align="right"><div style="word-break:break-all">
                            <? echo number_format($trim_amount,4); ?> </div></td>

                            </tr>
                            <?
                                $grand_total_trim_amount+=$trim_amount;
                                $i++;
                            }   ?>
                            <tfoot>
                            <tr>
                                <th colspan="2"><strong>Grand Total</strong> </th>
                                <th align="right"><? echo number_format($grand_total_trim_amount,4);?></th>
                            </tr>
                            </tfoot>
                    </table>

                    </div>
               <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="text-align: center;">Embellishment Cost. Details</b></caption>
					<thead>
                        <th width="120">Particulars</th>
						<th width="100">Type</th>
						<th width="150">Nominated Supplier Name</th>
                        <th width="100">Gmts. Qnty(Dzn)</th>
                        <th width="100">Amount($)</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:800px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?

				   $sql_emblish="select c.id, c.job_no, c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate, c.amount, c.supplier_id from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond order by c.id";
					$result_emblish=sql_select($sql_emblish);
					$emblish_detail_arr=array();
					foreach($result_emblish as $row)
					{
						$item_descrition =$row[csf("description")];
						$embl_rowspan+=1;
						if($row[csf("supplier_id")] !=0){
							$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['supplier_id']=$row[csf("supplier_id")];
						}
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['emb_name']=$row[csf("emb_name")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['emb_type']=$row[csf("emb_type")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['cons_dzn_gmts'] += $row[csf("cons_dzn_gmts")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['amount']+=$row[csf("amount")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['rate']+=$row[csf("rate")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['job_no'].=$row[csf("job_no")].',';
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['desc']=$item_descrition;
						//$emblishment_qty_arr
						$embsamount=$emblishment_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						$totalemb_detail_arr[100]['amount']+=$embsamount;
					}
					//echo $embl_rowspan;
					//print_r($conv_rowspan_arr);
                    $grand_total_embl_amount = $total_lab_test_cost+$total_inspection_cost+$total_freight_cost+$total_currier_cost+$total_certificate_cost;
                    //$total_common_oh_cost
					$i=$m=1;$grand_total_cons_dzn_gmts=0;
					foreach($emblish_detail_arr as $emb_name=>$enm_val)
					{
						foreach($enm_val as $emb_type=>$val)
						{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						//$emb_name=$val[('emb_name')];$emb_type=$val[('emb_type')];
						 $totalembl_amount=$totalemb_detail_arr[100]['amount'];
						if($emb_name==1)$em_type = $emblishment_print_type[$emb_type];
						else if($emb_name==2)$em_type = $emblishment_embroy_type[$emb_type];
						else if($emb_name==3)$em_type = $emblishment_wash_type[$emb_type];
						else if($emb_name==4)$em_type = $emblishment_spwork_type[$emb_type];

						$cons_dzn_gmts=0;$embl_amount=0;
						foreach($job_nos as $jno)
						{
							if($emb_name !=3){
								$wash_qty=$emblishment_qty_name_type_arr[$jno][$emb_name][$emb_type];
								$wash_amt=$emblishment_amount_name_type_arr[$jno][$emb_name][$emb_type];
								if($wash_amt) $wash_amt=$wash_amt;else $wash_amt=0;
								if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
								if(($wash_qty!="" || $wash_amt!=0) && ($wash_qty!="" || $wash_amt!=0))
								{
									$em_amount=$emblishment_amount_name_type_arr[$jno][$emb_name][$emb_type];
									$cons_dzn=$emblishment_qty_name_type_arr[$jno][$emb_name][$emb_type];
									if($em_amount) $em_amount=$em_amount;else $em_amount=0;
									if($cons_dzn) $cons_dzn=$cons_dzn;else $cons_dzn=0;

									$cons_dzn_gmts+=$cons_dzn;
									$embl_amount+=$em_amount;
								}
							}
							else if($emb_name ==3){
								$wash_qty=$wash_type_name_qty_arr[$jno][$emb_name][$emb_type];
								$wash_amt=$wash_type_name_amount_arr[$jno][$emb_name][$emb_type];
								if($wash_amt) $wash_amt=$wash_amt;else $wash_amt=0;
								if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
								if(($wash_qty!="" || $wash_amt!=0) && ($wash_qty!="" || $wash_amt!=0))
								{
									$embl_amt=$wash_type_name_amount_arr[$jno][$emb_name][$emb_type];
									$cons_dzn=$wash_type_name_qty_arr[$jno][$emb_name][$emb_type];
									if($embl_amt) $embl_amt=$embl_amt;else $embl_amt=0;
									if($cons_dzn) $cons_dzn=$cons_dzn;else $cons_dzn=0;
									$cons_dzn_gmts+=$wash_type_name_qty_arr[$jno][$emb_name][$emb_type];
									$embl_amount+=$embl_amt;
								}
							//echo 2;
							}
						}
						//wash_type_name_amount_arr
						//echo $embl_amount.',';
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tremb_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tremb_<? echo $i; ?>">
                            <td width="120"><div style="word-break:break-all"><? echo $emblishment_name_array[$emb_name];; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $em_type; ?></div></td>
							<td width="150" align="right" ><div style="word-break:break-all"><? echo $supplier_library_fabric[$val['supplier_id']]; ?></div>

                            <td width="100" align="right"><? echo number_format($cons_dzn_gmts,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($embl_amount,4); ?> </div></td>
                            </tr>
                            <?
								$grand_total_embl_amount+=$embl_amount;
								$grand_total_cons_dzn_gmts+=$cons_dzn_gmts;
								$i++;//$m++;
								}
							}
							?>
                            <tr bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 1; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 1; ?>">
                                <td align="left">Lab Test </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td align="right"><? echo number_format($total_lab_test_cost,4); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 2; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 2; ?>">
                                <td align="left">Inspection Cost</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td align="right"><? echo number_format($total_inspection_cost,4); ?></td>
                            </tr>
                            <tr  bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 4; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 4; ?>">
                                <td align="left">Freight Cost</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td align="right"><? echo number_format($total_freight_cost,4); ?></td>
                            </tr>
                             <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 5; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 5; ?>">
                                <td align="left">Currier Cost </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td align="right"><? echo number_format($total_currier_cost,4); ?></td>
                            </tr>
                             <tr bgcolor="<? echo $style1; ?>" onClick="change_color('troh_<? echo 6; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 6; ?>">
                                <td align="left">Certificate Cost </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td align="right"><? echo number_format($total_certificate_cost,4); ?></td>
                            </tr>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>

                                <th align="right"><strong><? echo number_format($grand_total_cons_dzn_gmts,4);?></strong></th>
                                <th align="right"><? echo number_format($grand_total_embl_amount,4);?></th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>

            <br/>
     		<?
        		 echo signature_table(109, $cbo_company_name, "850px");
   			 ?>
        </div>
		<?
	}

	if( str_replace("'","",$sign)==1 )
	{
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$html****$filename";
	}
    exit();
}

if ($action == 'trims_popup') {
    echo load_html_head_contents("Trims Details info", "../../../../", 1, 1, $unicode, '', '');
    extract($_REQUEST);
    //echo $po_break_down_id."*".$tot_po_qnty;die;

    //echo $ratio;die;

    ?>
    <script>

        function window_close() {
            parent.emailwindow.hide();
        }

    </script>
    <fieldset style="width:650px;">
        <legend>Accessories Status pop up</legend>
        <div style="100%" id="report_container">
            <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                    <th colspan="7">Accessories Status</th>
                </tr>
                <tr>
                    <th width="110">Item</th>
                    <th width="70">UOM</th>
                    <th width="90">Req. Qty.</th>
                    <th width="90">Received</th>
                    <th width="90">Recv. Balance</th>
                    <th width="90">Issued</th>
                    <th>Left Over</th>
                </tr>
                </thead>
                <?
                $item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
                $trims_array = array();
                $trimsDataArr = sql_select("select b.item_group_id,
									sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
									sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
									from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($po_break_down_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
                foreach ($trimsDataArr as $row) {
                    $trims_array[$row[csf('item_group_id')]]['recv'] = $row[csf('recv_qnty')];
                    $trims_array[$row[csf('item_group_id')]]['iss'] = $row[csf('issue_qnty')];
                }


                //$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
                $trimsDataArr = sql_select("select c.po_break_down_id, max(a.costing_per) as costing_per, b.trim_group, max(b.cons_uom) as cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b , wo_pre_cost_trim_co_cons_dtls c where a.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and b.status_active=1 and b.is_deleted=0 and c.po_break_down_id=$po_break_down_id group by b.trim_group, c.po_break_down_id");
                $i = 1;
                $tot_accss_req_qnty = 0;
                $tot_recv_qnty = 0;
                $tot_iss_qnty = 0;
                $tot_recv_bl_qnty = 0;
                $tot_trims_left_over_qnty = 0;
                foreach ($trimsDataArr as $row) {
                    if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                    $dzn_qnty = '';
                    if ($row[csf('costing_per')] == 1) $dzn_qnty = 12;
                    else if ($row[csf('costing_per')] == 3) $dzn_qnty = 12 * 2;
                    else if ($row[csf('costing_per')] == 4) $dzn_qnty = 12 * 3;
                    else if ($row[csf('costing_per')] == 5) $dzn_qnty = 12 * 4;
                    else $dzn_qnty = 1;

                    $dzn_qnty = $dzn_qnty * $ratio;
                    $accss_req_qnty = ($row[csf('cons_dzn_gmts')] / $dzn_qnty) * $tot_po_qnty;

                    $trims_recv = $trims_array[$row[csf('trim_group')]]['recv'];
                    $trims_issue = $trims_array[$row[csf('trim_group')]]['iss'];
                    $recv_bl = $accss_req_qnty - $trims_recv;
                    $trims_left_over = $trims_recv - $trims_issue;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($accss_req_qnty, 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_recv, 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($recv_bl, 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_issue, 2, '.', ''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_left_over, 2, '.', ''); ?>&nbsp;</td>
                    </tr>
                    <?
                    $tot_accss_req_qnty += $accss_req_qnty;
                    $tot_recv_qnty += $trims_recv;
                    $tot_recv_bl_qnty += $recv_bl;
                    $tot_iss_qnty += $trims_issue;
                    $tot_trims_left_over_qnty += $trims_left_over;
                    $i++;
                }
                $tot_trims_left_over_qnty_perc = ($tot_trims_left_over_qnty / $tot_recv_qnty) * 100;
                ?>
                <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_accss_req_qnty, 0, '.', ''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_qnty, 0, '.', ''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_bl_qnty, 0, '.', ''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_iss_qnty, 0, '.', ''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_trims_left_over_qnty, 0, '.', ''); ?>&nbsp;</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?

    exit();
}
//Ex-Factory Delv. and Return
if ($action == "ex_factory_popup") {
    echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1, $unicode, '', '');
    extract($_REQUEST);
    //echo $id;//$job_no;
    ?>
    <div style="width:100%" align="center">
        <fieldset style="width:500px">
            <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div>
            <br/>

            <div style="width:100%">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>

                    </tr>
                    </thead>
                </table>
            </div>
            <div style="width:100%; max-height:400px;">
                <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <?
                    $i = 1;

                    $exfac_sql = ("select b.challan_no,a.sys_number,b.ex_factory_date,
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                    $sql_dtls = sql_select($exfac_sql);

                    foreach ($sql_dtls as $row_real) {
                        if ($i % 2 == 0) $bgcolor = "#EFEFEF"; else $bgcolor = "#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><? echo $i; ?></td>
                            <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                            <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                            <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                            <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                        </tr>
                        <?
                        $rec_qnty += $row_real[csf("ex_factory_qnty")];
                        $rec_return_qnty += $row_real[csf("ex_factory_return_qnty")];
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th><? echo number_format($rec_qnty, 2); ?></th>
                        <th><? echo number_format($rec_return_qnty, 2); ?></th>
                    </tr>
                    <tr>
                        <th colspan="3">Total Balance</th>
                        <th colspan="2" align="right"><? echo number_format($rec_qnty - $rec_return_qnty, 2); ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    </div>
    <?
    exit();
}
//disconnect($con);
?>
