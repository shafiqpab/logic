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
    echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/style_item_wise_chemical_cost_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/style_item_wise_chemical_cost_report_controller', this.value, 'load_drop_down_season', 'season_td' )", 0);
    exit();
}
if ($action=="load_drop_down_brand")
{
	//echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id in($data) and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "-- All Brand --", $selected, "",0 );
	exit();
}
if ($action=="load_drop_down_season")
{
	//$data_arr = explode("*", $data);
	//if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id in($data) and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- All Season --", "", 0 );
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
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_quotation_no_search_list_view', 'search_div', 'style_item_wise_chemical_cost_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'create_list_style_search', 'search_div', 'style_item_wise_chemical_cost_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                            <!-- +'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>' -->
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
    list($company, $buyer, $search_type, $search_value, $cbo_year) = explode('**', $data);
    // , $txt_style_ref_no, $txt_style_ref_id, $txt_style_ref
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
    <!-- <script language="javascript" type="text/javascript">
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
    </script> -->
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
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value, 'order_search_list_view', 'search_div', 'style_item_wise_chemical_cost_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                                <!-- +'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>' -->
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
    list($company, $buyer, $search_type, $search_value, $start_date, $end_date, $cbo_year_selection) = explode('**', $data);
    // , $txt_style_ref, $style_ref_id
    $buyer = str_replace("'", "", $buyer);
    $company = str_replace("'", "", $company);
    // $txt_style_ref = str_replace("'", "", $txt_style_ref);
    // $style_ref_id = str_replace("'", "", $style_ref_id);
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
    // if (trim(str_replace("'", "", $txt_style_ref)) != "") {
    //     if (str_replace("'", "", $style_ref_id) != "") {
    //         $job_style_cond = " and b.id in(" . str_replace("'", "", $style_ref_id) . ") ";
    //     } else {
    //         $job_style_cond = " and b.style_ref_no like '%" . trim(str_replace("'", "", $txt_style_ref)) . "%'";
    //     }
    // }

    if ($buyer != 0) $buyer_cond = "and b.buyer_name=$buyer"; else $buyer_cond = "";
    if ($txt_style_ref != "") {
        //if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref) and year(b.insert_date)= '$cbo_year' ";
        //else $style_cond="and b.job_no_prefix_num in($txt_style_ref) and to_char(b.insert_date,'YYYY')= '$cbo_year' ";
    }
    //else $style_cond="";

    //echo $style_cond."jahid";die;
     $sql = "select a.id,a.po_number,a.grouping as int_ref,a.job_no_mst,b.style_ref_no,b.quotation_id,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name in($company)   $job_style_cond $search_con $date_cond and a.status_active=1";
    //echo $sql;
    echo create_list_view("list_view", "Order NO,int.Ref No,Job No,QuotationId,Year,Style Ref No", "150,100,80,70,70,150", "670", "150", 0, $sql, "js_set_value", "id,int_ref", "", 1, "0", $arr, "po_number,int_ref,job_no_prefix_num,quotation_id,job_year,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    ?>
    <!-- <script language="javascript" type="text/javascript">
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
    </script> -->
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
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>', 'file_order_search_list_view', 'search_div', 'style_item_wise_chemical_cost_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
    $season_name_arr=return_library_array( "select id, season_name from  lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
    $brand_name_arr=return_library_array( "select id, brand_name from  lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');
    $reporttype=str_replace("'","",$reporttype);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    //$cbo_season_year=str_replace("'","",$cbo_season_year);
    $txt_order=str_replace("'","",$txt_order);
    $txt_order_id=str_replace("'","",$txt_order_id);
    $cbo_brand_id=str_replace("'","",$cbo_brand_id);
    $season_year=str_replace("'","",$cbo_season_year);
    //$file_no=rtrim($cbo_brand_id,',');
    $txt_style_ref=str_replace("'","",$txt_style_ref);
    $txt_season_id=str_replace("'","",$cbo_season_id);
    $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
    $style_ref_id=str_replace("'","",$txt_style_ref_id);
    $txt_ex_rate=str_replace("'","",$txt_ex_rate);
    
    if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
    if($season_year==0) $season_year_cond=""; else $season_year_cond=" and a.season_year='$season_year' ";

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
    $con = connect();
    if(trim(str_replace("'","",$txt_style_ref))!="")
    {
        if(str_replace("'","",$style_ref_id)!="")
        {
            $style_ref_arr=array_unique(explode(',',$style_ref_id));
            foreach($style_ref_arr as $value) {
                if($value!=0) {
                    $r_id2=execute_query("insert into tmp_poid(userid, poid, type) values($user_id,$value,121)");
                }            
            }
            // $job_style_cond=" and a.id in(".str_replace("'","",$style_ref_id).")";
            $job_style_cond=" and a.id=c.poid";
            
        }
        else
        {
            $style_ref_arr=array_unique(explode(',',$txt_style_ref));
            foreach($style_ref_arr as $value) {
                if($value!='') {
                    $r_id2=execute_query("insert into tmp_poid(userid, pono, type) values($user_id,$value,121)");
                }            
            }
            // $job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
            $job_style_cond=" and a.style_ref_no = c.pono";
        }

        if($db_type==0) {
            if($r_id2) {
                mysql_query("COMMIT");
            }
        }
        if($db_type==2 || $db_type==1) {
            if($r_id2) {
                oci_commit($con);  
            }
        }
    }

    $order_cond="";
    if(trim(str_replace("'","",$txt_order))!="")
    {
        if(str_replace("'","",$txt_order_id)!="")
        {
            $order_id_arr=array_unique(explode(',',$txt_order_id));
            foreach($order_id_arr as $value) {
                if($value!=0) {
                    $r_id2=execute_query("insert into tmp_poid(userid, poid, type) values($user_id,$value,122)");
                }            
            }
            // $order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
            $order_cond=" and b.id=c.poid ";
        }
        else
        {
            $order_arr=array_unique(explode(',',$txt_order));
            foreach($order_arr as $value) {
                if($value!='') {
                    $r_id2=execute_query("insert into tmp_poid(userid, pono, type) values($user_id,$value,122)");
                }            
            }
            // $order_cond=" and b.grouping = '".trim(str_replace("'","",$txt_order))."'";
            $order_cond=" and b.grouping = c.pono";
        }
        if($db_type==0) {
            if($r_id2) {
                mysql_query("COMMIT");
            }
        }
        if($db_type==2 || $db_type==1) {
            if($r_id2) {
                oci_commit($con);  
            }
        }
    }
            $season_cond2=$season_cond='';
            if($txt_season_id!=0)
            {
                $season_cond="and a.season_matrix in($txt_season_id)";
                $season_cond2="and season_buyer_wise in($txt_season_id)";
                //
            }

    ob_start();

    //$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
    ////$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
    //$supplier_library_fabric=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"); //b.party_type in(1,9) and

	if($reporttype==1) //Budget Button
	{
		  $sql="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name,a.season_buyer_wise,a.season_year,a.brand_id, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date 
          from wo_po_details_master a, wo_po_break_down b ,tmp_poid c
          where a.job_no=b.job_no_mst and c.type=121 and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond  $buyer_id_cond $season_cond $season_year_cond 
          union all
          select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name,a.season_buyer_wise,a.season_year,a.brand_id, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date
          from wo_po_details_master a, wo_po_break_down b ,tmp_poid c
          where a.job_no=b.job_no_mst and c.type=122 and c.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond  $order_cond $buyer_id_cond $season_cond $season_year_cond "; 
            //   order by b.id
			// echo $sql; die;
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
					$season_brand =$brand_name_arr[$row[csf('brand_id')]].','. $season_name_arr[$row[csf('season_buyer_wise')]].'-'.substr( $row[csf('season_year')], -2);
					$style_wise_arr[$row[csf("style_ref_no")]]['qty']+=$row[csf('po_quantity')]*$row[csf('ratio')];
					//$style_wise_arr[$row[csf("style_ref_no")]]['brand_id']=$row[csf("brand_id")];
					$style_wise_arr[$row[csf("style_ref_no")]]['season']=$season_brand;
					$style_wise_arr[$row[csf("style_ref_no")]]['job_no']=$row[csf("job_no")];
					
					$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
				}
				//print_r($po_qty_by_job);
				 $sql_wash_order="select a.id,b.buyer_po_id as po_id,b.buyer_style_ref,b.id as sub_po_id,b.order_no from subcon_ord_mst a,subcon_ord_dtls b where  a.subcon_job=b.job_no_mst and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.entry_form=295 ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."   order  by a.id";
				    $sql_wash_result=sql_select($sql_wash_order);
					 
					foreach($sql_wash_result as $row)
					{
						$wash_po_id_arr[$row[csf("sub_po_id")]]=$row[csf("sub_po_id")];
					}
					unset($sql_wash_result);
					//echo $txt_ex_rate;
					 $sql_wash_issue="select a.id,a.style_ref,a.buyer_job_no,b.prod_id,b.cons_quantity, b.cons_rate, b.cons_amount,c.item_description from inv_issue_master a,inv_transaction b,product_details_master c where  a.id=b.mst_id and c.id=b.prod_id and  a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0    and a.entry_form=298  ".where_con_using_array($wash_po_id_arr,0,'a.sub_order_id')."  order  by a.id";
				    $sql_wash_issue_result=sql_select($sql_wash_issue);
					 
					foreach($sql_wash_issue_result as $row)
					{
						$prod_wise_arr[$row[csf("prod_id")]]['desc']=$row[csf("item_description")];
						$prod_wise_arr[$row[csf("prod_id")]]['cons_rate']=$row[csf("cons_rate")];
						$prod_wise_arr[$row[csf("prod_id")]]['cons_amount']+=$row[csf("cons_amount")];
						
						$prod_style_wise_arr[$row[csf("prod_id")]][$row[csf("style_ref")]]['qty']+=$row[csf("cons_quantity")];
						$prod_style_wise_arr[$row[csf("prod_id")]][$row[csf("style_ref")]]['amt']+=$row[csf("cons_amount")];
						$prod_style_wise_arr[$row[csf("prod_id")]][$row[csf("style_ref")]]['rate']=$row[csf("cons_rate")];
					}
					unset($sql_wash_issue_result);
					//print_r($smv_avg_by_job);
					//echo $sew_smv;
				//	print_r($prod_style_wise_arr);
					$condition= new condition();
					$condition->company_name("=$cbo_company_name");
				  if(str_replace("'","",$cbo_buyer_name)>0){
					 // $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if($all_po_id)
				 {
					$condition->po_id_in("$all_po_id");
				 }
				 
				$condition->init();
				
				$wash= new wash($condition);
				//	echo $wash->getQuery();die;
				$wash_amount_arr=$wash->getAmountArray_by_job();
				//print_r($wash_amount_arr);
			
				$style1="#E9F3FF";
				$style="#FFFFFF";
			//print_r($style_wise_arr);
			//echo count($style_wise_arr);
			if(count($style_wise_arr)==1)
			{
				$width_td=650*count($style_wise_arr);
			}
			else
			{
			$width_td=490*count($style_wise_arr);
			}
			//echo $width_td;
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


             <table width="<? echo $width_td;?>px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="8" align="center"><strong style=" font-size:18px"><? echo $report_title;?></strong></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" class="form_caption"><strong><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong></td>
                </tr>
            </table>
            <table width="<? echo $width_td;?>" style="margin-left:10px;" cellpadding="0" cellspacing="0" border="2" id="table_header_1">
                <tr>
		            <td style="border:none">
		            	<table width="<? echo $width_td;?>"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                            <tr bgcolor="#CCCCCC">
                            <th colspan="2" width="150"  >
                            <div style="float:right">
                            <?
                            echo "<strong style='style='float:right'>Style Ref.<hr></strong>";
							echo "<strong style='style='float:right'>Style Qty.<hr></strong>";
							echo "<strong style='style='float:right'>Brand & Season</strong>";
							?>
                            </div>
                             </th>
                             <?
                                foreach($style_wise_arr as $style_key=>$style)
								{
							 ?>
                                <th colspan="<? echo count($style);?>"  width="100"> <? 
								echo $style_key.'<hr>';
								echo $style['qty'].'<hr>';
								echo $style['season'];
								
								?></th>
                                <?
								}
								?>
                            <th colspan="3">  Style Total</th>
                            </tr>
                            <tr>
                          	   <th width="20">#SL</th>
                                <th width="230">Name of chemicals</th>
                                <?
                                foreach($style_wise_arr as $style_key=>$style)
								{
							  ?>
                                <th  width="80">Chem. Issue Qty (Kg.)</th>
                                <th  width="80">Avg Unit Price</th>
                                <th  width="80">Chem. Issue Value</th>
                                <?
								}
								?>
                               <th  width="80">Chem. Issue Qty (Kg.)</th> 
                                <th  width="80">Avg Unit Price</th>
                                <th  width="80">Total Chem. Issue Value</th>
                               </tr>
                           </thead>
                           <?
                           //die;
                       $i=1;$total_prod_qty=0;$total_fob_val=$total_cm_value=0;$total_po_qty=0;
					foreach($prod_wise_arr as $prod_id=>$row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$order_qty_pcss=$row[csf('po_quantity')]*$row[csf('ratio')];
						//$avg_unit=$row[csf('unit_price')];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="20"  align="center"><? echo $i; ?></td>
							<td width="230" align="left"><div style="word-break:break-all"><? echo $row[('desc')]; ?></div></td>
							 <? 
							 $tot_prod_qty=$tot_prod_amt=0;
							 foreach($style_wise_arr as $style_key=>$style)
								{
									$prod_qty=$prod_style_wise_arr[$prod_id][$style_key]['qty'];
									$prod_amt=$prod_style_wise_arr[$prod_id][$style_key]['amt'];
							  ?>
                            <td width="80"  align="right" title="<?echo $prod_qty;?>"><div style="word-break:break-all"><? echo number_format($prod_qty,2) ?></div></td>
                            <td width="80"  align="right" title="<?echo $prod_amt/$prod_qty;?>"><div style="word-break:break-all"><? echo number_format($prod_amt/$prod_qty,2); ?></div></td>
                            <td width="80" align="right" title="<?echo $prod_amt;?>"><div style="word-break:break-all"><? echo number_format($prod_amt,2); ?></div></td>
                            <?
								 $tot_prod_qty+=$prod_qty;
								  $tot_prod_amt+=$prod_amt;
								  $tot_prod_style_qty_arr[$style_key]+=$prod_qty;
								  $tot_prod_style_amt_arr[$style_key]+=$prod_amt;
								}
							?>
                            
                            <td width="80" align="right" title="<?echo $tot_prod_qty;?>"><div style="word-break:break-all"><? echo number_format($tot_prod_qty,2); ?></div></td>
                             <td width="80" align="right" title="<?echo $tot_prod_amt/$tot_prod_qty;?>"><div style="word-break:break-all"><? echo number_format($tot_prod_amt/$tot_prod_qty,2); ?></div></td>
							<td width="80" align="right"  title="<?echo $tot_prod_amt;?>"><div style="word-break:break-all"><? echo number_format($tot_prod_amt,2); ?></div></td>
                            </tr>

                            <?
							$total_prod_qty+=$tot_prod_qty;
							$total_prod_amt+=$tot_prod_amt;
							$total_cm_value+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
							$i++;

							}
							?>
                            
                            <tr bgcolor="#CCCCCC">
                            <td colspan="2" align="right"><strong>Total (BDT)</strong> </td>
                            <? foreach($style_wise_arr as $style_key=>$style)
								{
							  ?>
                              
                            <td align="right"><strong><? echo number_format($tot_prod_style_qty_arr[$style_key],2);?> </strong></td>
                            <td align="right"></strong></td>
                            <td align="right"><strong><? echo number_format($tot_prod_style_amt_arr[$style_key],2);?></strong> </td>
                            <?
								}
							?>
                            <td align="right"><? echo number_format($total_prod_qty,2);?></td>
							<td align="right"><strong><? echo number_format($total_prod_amt/$total_prod_qty,2);?></strong> </td>
                            <td align="right"><strong><? echo number_format($total_prod_amt,2);?></strong> </td>
                            </tr>
                            <tr  bgcolor="#CCCCCC">
                            <td colspan="2" align="right"><strong>Budget Cost (BDT)</strong> </td>
                            <? 
							$tot_wash_amount=0;
								foreach($style_wise_arr as $style_key=>$style)
								{
									 $wash_amount=$wash_amount_arr[$style['job_no']]*$txt_ex_rate;  
							  ?>
                              
                            <td align="right"><strong><? //echo number_format($tot_prod_style_qty_arr[$style_key],2);?> </strong></td>
                            <td align="right"></strong></td>
                            <td align="right"><strong><? echo number_format($wash_amount,2);?></strong> </td>
                            <?
								$tot_wash_amount+=$wash_amount;
								}
							?>
                            <td align="right"><? // echo number_format($total_prod_qty,2);?></td>
							<td align="right"><strong><? ///echo number_format($total_prod_amt/$total_prod_qty,2);?></strong> </td>
                            <td align="right"><strong><? echo number_format($tot_wash_amount,2);?></strong> </td>
                            </tr>
                            <tr bgcolor="#CCCCCC">
                            <td colspan="2" align="right"><strong>Budget VS Actual Cost (BDT)</strong> </td>
                            <? 
							$tot_budgt_acl_diff=0;
							foreach($style_wise_arr as $style_key=>$style)
								{
									$wash_amount=$wash_amount_arr[$style['job_no']]*$txt_ex_rate;
									$tot_amt_diff=$wash_amount-$tot_prod_style_amt_arr[$style_key];
									
									if($tot_amt_diff<1)
									{
										$th_color="red";
									} 
									else $th_color="";
							  ?>
                              
                            <td align="right"><strong><? //echo number_format($total_prod_amt-$tot_wash_amount,2);?> </strong></td>
                            <td align="right"></strong></td>
                            <td align="right" title="Budget Cost-Total Cost" bgcolor="<? echo $th_color;?>"><strong><?  echo number_format($tot_amt_diff,2);?></strong> </td>
                            <?
								$tot_budgt_acl_diff+=$tot_amt_diff;
								}
								$summ_amt_diff=$tot_wash_amount-$total_prod_amt;
									if($summ_amt_diff<1)
									{
										$th_color2="red";
									} 
									else $th_color2="";
							?>
                            <td align="right"><? //echo number_format($total_prod_qty,2);?></td>
							<td align="right"><strong><? //echo number_format($total_prod_amt/$total_prod_qty,2);?></strong> </td>
                            <td align="right" bgcolor="<? echo $th_color2;?>"><strong><? echo number_format($summ_amt_diff,2);?></strong> </td>
                            </tr>
                          
		                </table>
		             </td>
	            </tr>
                
            </table>
            <br>
           <div id="page_break_div">
			<? //die; ?>
            </div>
     		<?
        		// echo signature_table(109, $cbo_company_name, "850px");
   			 ?>
        </div>
		<?
	}

	
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
	
        $r_id3=execute_query("delete from tmp_poid where userid=$user_id and type in (121,122)");
        if($db_type==0) {
            if($r_id3) {
                mysql_query("COMMIT");
            }
        }
        if($db_type==2 || $db_type==1 ) {
            if($r_id3) {
                oci_commit($con);  
            }
        }
        
    disconnect($con);
    die();
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
