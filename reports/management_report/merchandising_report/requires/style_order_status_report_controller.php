<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');
include('../../../../includes/class4/class.trims.php');
include('../../../../includes/class4/class.yarns.php');
include('../../../../includes/class4/class.washes.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if($action=="print_button_variable_setting")
{
	$buttonIdArr = ['108#show_button1', '259#show_button2', '242#show_button3', '359#show_button4', '712#show_button5', '389#show_button6', '887#show_button7'];
	$print_report_format_arr = get_report_button_array($data, 11, 220, $user_id, $buttonIdArr);
    exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
		<script>
			function js_set_value(str)
			{
				$("#hide_job_no").val(str);
				parent.emailwindow.hide();
			}
		</script>
	<?
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	if(trim($cbo_year)!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)=$cbo_year";
			$year_field="YEAR(insert_date)";
		}
		else
		{
			$year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";
			$year_field="to_char(insert_date,'YYYY')";
		}
	}
	else $year_cond="";

	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a where a.company_name=$company_id $buyer_cond $year_cond order by a.id";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","320",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";

	exit();
}


if($action=="style_refarence_surch")
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
                    <th>Search By</th>
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_surch_list_view', 'search_div', 'style_order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
if($action=="style_refarence_surch_for_booking")
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
			//alert(splitSTR)
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
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Booking No</th>
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
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Booking No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_surch_list_view_for_booking', 'search_div', 'style_order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
if($action=="style_refarence_surch_list_view_for_booking")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id,$txt_style_ref,$txt_booking_no,$txt_order_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";

	if($search_type==1 && $search_value!=''){
		$search_con="and b.booking_no like('%$search_value%') ";
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";
	}
	else if($search_type==3 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";
	}

	$txt_order_id_cond .= ($txt_order_id!="") ? " and a.PO_BREAK_DOWN_ID=$txt_order_id" : "";

	$sql = "SELECT a.BOOKING_NO ,a.PO_BREAK_DOWN_ID FROM WO_BOOKING_MST a WHERE $txt_order_id_cond  a.STATUS_ACTIVE=1 AND a.IS_DELETED=0";
	//echo $sql ;
    $sql_sel = sql_select($sql);
	foreach ($sql_sel as $key => $value) {
		$order_cond_arr[$value['PO_BREAK_DOWN_ID']] = $value['PO_BREAK_DOWN_ID'];
	}
	$order = implode(",",$order_cond_arr);
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select b.job_no,b.po_break_down_id,
	b.booking_no, a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a , wo_booking_mst b where a.job_no = b.job_no AND a.company_name=$company $buyer_cond $year_cond $job_cond $search_con  and a.status_active=1 AND a.is_deleted=0
	and b.status_active=1 AND b.is_deleted=0 order by job_no_prefix_num";
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Booking No,Year,Job No","160,90,100,100","500","200",0, $sql , "js_set_value", "id,booking_no", "", 1, "0", $arr, "style_ref_no,booking_no,job_year,job_no_prefix_num", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
	var style_book='<? echo $txt_booking_no;?>';
	alert(txt_booking_no);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}
//style search------------------------------//
if($action=="style_refarence_surch_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id,$txt_style_ref)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";

	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";
	}



	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a where a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num";
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="order_surch")
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $type_id; ?>', 'order_surch_list_view', 'search_div', 'style_order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

if($action=="order_surch_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$start_date,$end_date,$cbo_year,$txt_style_ref,$type_id)=explode('**',$data);
	?>
    <script>
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_year=str_replace("'","",$cbo_year);
	$type_id=str_replace("'","",$type_id);
	//echo $type_id.'ddd';
	if(trim($cbo_year)!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(b.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";
		}
	}

	if($search_type==1 && $search_value!=''){
		$search_con=" and a.po_number like('%$search_value')";
	}
	elseif($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value')";
	}
	elseif($search_type==3 && $search_value!=''){
		$search_con=" and a.job_no_mst like('%$search_value')";
	}


	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and a.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}




	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($txt_style_ref!="")
	{
		if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref) and year(b.insert_date)= '$cbo_year' ";
		else $style_cond="and b.job_no_prefix_num in($txt_style_ref) and to_char(b.insert_date,'YYYY')= '$cbo_year' ";
	}
	else $style_cond="";
	if($type_id==2)
	{
		$type_cond="id,grouping";
	}
	else
	{
		$type_cond="id,po_number";
	}
	//echo $style_cond."jahid";die;
	$sql = "select a.id,a.po_number,a.grouping,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $year_cond $style_cond $search_con $date_cond and a.status_active=1";
	 //echo $sql;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No,Internal Ref","150,80,70,150,100","600","150",0, $sql , "js_set_value", "$type_cond", "", 1, "0", $arr, "po_number,job_no_prefix_num,job_year,style_ref_no,grouping", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_order_id;?>';
	var style_des='<? echo $txt_order;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$ref_no=str_replace("'","",$txt_ref_no);
	$file_no=str_replace("'","",$txt_file_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	//$txt_ex_date_to=str_replace("'","",$txt_ex_date_to);
	$type=str_replace("'","",$type);

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	$ship_date_cond="";$ship_date_cond2="";
	if($cbo_date_category==1)
	{

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
		$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
	}
	}
	else if($cbo_date_category==2)
	{
		//$ex_fact_date_cond="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and c.ex_factory_date between '$txt_date_from' and '$txt_date_to' AND c.is_deleted = 0 AND c.status_active = 1";
			$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";

		}
	}
	else if($cbo_date_category==3) //Ref Closing date
	{

		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
			$ship_date_cond="and d.closing_date between '$txt_date_from' and '$txt_date_to'";
		}
	}


	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
	$order_cond="";
	if(trim($ref_no)!="") $ref_no_cond="and b.grouping in('$ref_no')";
	if(trim($file_no)!="") $file_no_cond="and b.file_no in('$file_no')";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	if($txt_order_id!="") $order_cond="and b.id in($txt_order_id)";
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	//if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	if($cbo_date_category==2) // Ex-Fact Date
	{
		$sql_po="SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
		where a.job_no=b.job_no_mst and  b.id=c.po_break_down_id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else if($cbo_date_category==1)  // Ship Date Date
	{
		 $sql_po="SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1
		where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else if($cbo_date_category==3) //ref Closing
	{
		  $sql_po="SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,d.closing_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty
		from wo_po_details_master a, inv_reference_closing d,wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id
		where a.job_no=b.job_no_mst  and  b.id=d.inv_pur_req_mst_id  and d.reference_type=163 and d.closing_status=1 and b.shiping_status=3  and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	//echo $sql_po;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array(); $poIdArr=array();
	foreach($sql_po_result as $row)
	{
		//if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];

		$poIdArr[$row[csf("po_id")]]=$row[csf("po_id")];

		$result_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$result_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["ref_no"]=$row[csf("grouping")];
		$result_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("po_id")]]["po_total_price"]=$row[csf("po_total_price")];
		$result_data_arr[$row[csf("po_id")]]["unit_price"]=$row[csf("unit_price")];
		$result_data_arr[$row[csf("po_id")]]["po_qnty"]=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")];
		$result_data_arr[$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("po_id")]]["closing_date"]=$row[csf("closing_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
		$JobArr[]="'".$row[csf('job_no')]."'";
	}

	/*$yarn= new yarn($JobArr,'job');
	$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();*/
	//print_r($yarn_qty_arr);
	//$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));

	$all_po_id=implode(",",$poIdArr);

	$sql_po_ref="SELECT b.inv_pur_req_mst_id as po_id,max(b.closing_date) as  closing_date from inv_reference_closing b
		where b.reference_type=163 and b.closing_status=1  and b.is_deleted=0 and b.status_active=1  ".where_con_using_array($poIdArr,0,'b.inv_pur_req_mst_id')." group by  b.inv_pur_req_mst_id order by b.inv_pur_req_mst_id desc";
	$sql_po_ref_result=sql_select($sql_po_ref);
	foreach($sql_po_ref_result as $row)
	{
		$Ref_closing_arr[$row[csf("po_id")]]=$row[csf("closing_date")];
	}
	if($cbo_date_category==2)
	{
		$Ref_closing_arr = return_library_array("SELECT po_break_down_id,max(ex_factory_date) as closing_date from PRO_EX_FACTORY_MST where status_active=1 ".where_con_using_array($poIdArr,0,'po_break_down_id')." and SHIPING_STATUS=3 group by po_break_down_id","po_break_down_id","closing_date");
	}
	//$JobNoArr=implode(",",$JobArr);
	//$yarn= new yarn($JobArr,'job');
	//$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	//print_r($yarn_qty_arr);
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(!empty($JobArr)){
	 $condition->po_id_in("$all_po_id");
	}
	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	//$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
	$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();




	$booking_req_arr=array();
	$sql_wo=sql_select("select b.po_break_down_id,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	sum(b.fin_fab_qnty) as fin_fab_qnty

	from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id");
	//and b.po_break_down_id in ($all_po_id)


	foreach ($sql_wo as $brow)
	{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['gray']=$brow[csf("grey_req_qnty")];
		$booking_req_arr[$brow[csf("po_break_down_id")]]['woven']=$brow[csf("woven_req_qnty")];
		$booking_req_arr[$brow[csf("po_break_down_id")]]['fin']=$brow[csf("fin_fab_qnty")];
	}
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_qnty,
		sum(CASE WHEN b.entry_form!=85 $ship_date_cond2 THEN b.ex_factory_qnty ELSE 0 END) as curr_ex_fact_qnty
		from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id");
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('return_qnty')];
			$tot_ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('ex_fact_qnty')];
			$curr_tot_ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('curr_ex_fact_qnty')];
		}



	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$dataArrayYarnReq=array();
	$yarn_sql="select job_no, sum(avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarnReq[$yarnRow[csf('job_no')]]=$yarnRow[csf('qnty')];
	}

	$reqDataArray=sql_select("select  a.po_break_down_id, sum((b.requirment/b.pcs)*a.plan_cut_qnty) as grey_req, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 ".where_con_using_array($poIdArr,0,'a.po_break_down_id')." group by a.po_break_down_id");//and a.po_break_down_id in ($all_po_id)
	$grey_finish_require_arr=array();
	foreach($reqDataArray as $row)
	{
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["grey_req"]=$row[csf("grey_req")];
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["finish_req"]=$row[csf("finish_req")];
	}

	$yarnDataArr=sql_select("select a.po_breakdown_id,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_issue_master c
						where a.trans_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')."  and b.item_category=1 and c.issue_purpose in (1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
						group by a.po_breakdown_id");//and a.po_breakdown_id in($all_po_id)
	$yarn_issue_arr=array();
	foreach($yarnDataArr as $row)
	{
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_in"]=$row[csf("issue_qnty_in")];
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_out"]=$row[csf("issue_qnty_out")];
	}

	$yarnReturnDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
				from order_wise_pro_details a, inv_transaction b, inv_receive_master c
				where a.trans_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
				group by a.po_breakdown_id");// and a.po_breakdown_id in($all_po_id)


	$yarn_issue_rtn_arr=array();
	foreach($yarnReturnDataArr as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
	}


	$dataArrayTrans=sql_select("select po_breakdown_id,
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish
							from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(11,13,15)  ".where_con_using_array($poIdArr,0,'po_breakdown_id')."
							group by po_breakdown_id");//and po_breakdown_id in($all_po_id)

	$transfer_data_arr=array();
	foreach($dataArrayTrans as $row)
	{
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_yarn"]=$row[csf("transfer_in_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_yarn"]=$row[csf("transfer_out_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_knit"]=$row[csf("transfer_in_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_knit"]=$row[csf("transfer_out_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_finish"]=$row[csf("transfer_in_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_finish"]=$row[csf("transfer_out_qnty_finish")];
	}

	// decision pending	dyed yearn receive
	//$greyYarnIssueQnty=return_library_array("select c.po_breakdown_id, sum(c.quantity) as issue_qnty from inv_transaction a, inv_issue_master b,  order_wise_pro_details c where a.mst_id=b.id and a.id=c.trans_id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and c.po_breakdown_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2 group by c.po_breakdown_id","po_breakdown_id","issue_qnty");


	//$dyedYarnRecvQnty=return_field_value("sum(a.cons_quantity) as recv_qnty","inv_transaction a, inv_receive_master b","a.mst_id=b.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1","recv_qnty");

	$prodKnitDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
				sum(CASE WHEN a.entry_form=22 THEN a.quantity ELSE 0 END) AS knit_qnty_rec
				from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and c.item_category=13 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");// and c.receive_basis<>9 and a.po_breakdown_id in($all_po_id)
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_in"]=$row[csf("knit_qnty_in")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_out"]=$row[csf("knit_qnty_out")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec"]=$row[csf("knit_qnty_rec")];
	}

	$prodFinDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
				sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec
				from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9 group by a.po_breakdown_id");// and a.po_breakdown_id in($all_po_id)

	$finish_prod_arr=array();
	foreach($prodFinDataArr as $row)
	{
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in"]=$row[csf("finish_qnty_in")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out"]=$row[csf("finish_qnty_out")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["woven_rec"]=$row[csf("woven_rec")];
	}
	$issueData=sql_select("select po_breakdown_id,
							sum(CASE WHEN entry_form=16 THEN quantity ELSE 0 END) AS grey_issue_qnty,
							sum(CASE WHEN entry_form=61 THEN quantity ELSE 0 END) AS grey_issue_qnty_roll_wise,
							sum(CASE WHEN entry_form=18 THEN quantity ELSE 0 END) AS issue_to_cut_qnty,
							sum(CASE WHEN entry_form=71 THEN quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
							sum(CASE WHEN entry_form=19 THEN quantity ELSE 0 END) AS woven_issue
							from order_wise_pro_details where entry_form in(16,18,19,61,71)  ".where_con_using_array($poIdArr,0,'po_breakdown_id')." and status_active=1 and is_deleted=0 group by po_breakdown_id");//po_breakdown_id in($all_po_id) and


	$grey_cut_issue_arr=array();
	foreach($issueData as $row)
	{
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty"]=$row[csf("grey_issue_qnty")]+$row[csf("grey_issue_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["issue_to_cut_qnty"]=$row[csf("issue_to_cut_qnty")]+$row[csf("issue_to_cut_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["woven_issue"]=$row[csf("woven_issue")];
	}
	$trimsDataArr=sql_select("select a.po_breakdown_id,
							sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
							sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
							from order_wise_pro_details a, product_details_master b where a.prod_id=b.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");// and a.po_breakdown_id in($all_po_id)
	foreach($trimsDataArr as $row)
	{
		$trims_array[$row[csf('po_breakdown_id')]]['recv']=$row[csf('recv_qnty')];
		$trims_array[$row[csf('po_breakdown_id')]]['iss']=$row[csf('issue_qnty')];
	}

	$sql_consumtiont_qty=sql_select("select b.po_break_down_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id, c.body_part_id ");//and b.po_break_down_id in ($all_po_id)
			$finish_consumtion_arr=array();
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg=0;
				$con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];///str_replace("'","",$row_sew[csf("pcs")]);


				$finish_consumtion_arr[$row_consum[csf('po_break_down_id')]]+=$con_avg;
			}

	$gmtsProdDataArr=sql_select("select  po_break_down_id,
					sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty_in,
					sum(CASE WHEN production_type=2 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_issue_qnty_out,
					sum(CASE WHEN production_type=3 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_recv_qnty_in,
					sum(CASE WHEN production_type=3 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_recv_qnty_out,
					sum(CASE WHEN production_type=2 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_in,
					sum(CASE WHEN production_type=2 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_out,
					sum(CASE WHEN production_type=3 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_in,
					sum(CASE WHEN production_type=3 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_out,
					sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_input_qnty_in,
					sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_input_qnty_out,
					sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,
					sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
					sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS finish_qnty_in,
					sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS finish_qnty_out,
					sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
					sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,
					sum(CASE WHEN production_type=3 and embel_name=1 THEN reject_qnty ELSE 0 END) AS print_reject_qnty,
					sum(CASE WHEN production_type=3 and embel_name=2 THEN reject_qnty ELSE 0 END) AS emb_reject_qnty,
					sum(CASE WHEN production_type=5 THEN reject_qnty ELSE 0 END) AS sew_reject_qnty,
					sum(CASE WHEN production_type=8 THEN reject_qnty ELSE 0 END) AS finish_reject_qnty,
					sum(CASE WHEN production_type=1 THEN reject_qnty ELSE 0 END) AS cutting_reject_qnty,
					sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty
					from pro_garments_production_mst where  is_deleted=0 and status_active=1 ".where_con_using_array($poIdArr,0,'po_break_down_id')." group by po_break_down_id");//po_break_down_id in($all_po_id) and

	$garment_prod_data_arr=array();
	foreach($gmtsProdDataArr as $row)
	{
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_qnty']=$row[csf("cutting_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_out']=$row[csf("finish_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_in']=$row[csf("wash_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_out']=$row[csf("wash_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_reject_qnty']=$row[csf("print_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_reject_qnty']=$row[csf("emb_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_reject_qnty']=$row[csf("sew_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_reject_qnty']=$row[csf("finish_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_reject_qnty']=$row[csf("cutting_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['iron_rej_qnty']=$row[csf("iron_rej_qnty")];
	}
	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	if($cbo_search_type==1)
	{
		$tbl_width=7130;

		$ship_date_html="Shipment Date";
		$ex_fact_date_html="Ex-Fact. Date";
	}
	else
	{
		$tbl_width=6500;
		$ship_date_html="Last Shipment Date";
		$ex_fact_date_html="Last Ex-Fact. Date";
		//$ex_fact_date_html="Closing Date";
	}
	ob_start();
	?>
        <div style="width:100%">
             <table width="<? echo $tbl_width;?>">
                <tr>
                    <td align="center" width="100%" colspan="70" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
            </table>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                	<tr style="font-size:13px">
                        <th width="40" >SL</th>
                        <th width="110" >Buyer</th>
                        <th width="50" >Job Year</th>
                        <th width="50" >Job No</th>
                        <th width="100" >Style No</th>

                        <?
						if($cbo_search_type==1)
						{
							?>
                        	<th width="100" >Order No</th>
							<?
						}
						?>
                        <th width="80" >Order Qty.(Pcs)</th>
                        <th width="80">FOB</th>
                        <th width="80">Order Value</th>

                        <?
						if($cbo_search_type==1)
						{
							?>
							<th width="70" ><? echo $ship_date_html; ?></th>
							<th width="70"><? echo $ex_fact_date_html; ?></th>

							<?
						}
						?>
                        <th width="70"><? echo 'Closing Date'; ?></th>
                        <th width="80">Yarn Req.<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                        <th width="80">Yarn Issued In</th>
                        <th width="80">Yarn Issued Out</th>
                        <th width="80">Yarn Trans In</th>
                        <th width="80">Yarn Trans Out</th>
                        <th width="80">Yarn Total Issued</th>
                        <th width="80">Yarn Under or Over Issued</th>

                        <th width="80">Knit. Gray Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Knit. Prod Inside</th>
                        <th width="80">Knit. Prod SubCon</th>
                        <th width="80">Knit. Trans. In</th>
                        <th width="80">Knit. Trans. Out</th>
                        <th width="80">Knit. Total Prod.</th>
                        <th width="80">Knit. Receive</th>
                        <th width="80">Knit. Process Loss</th>
                        <th width="80">Knit. Under or Over Prod.</th>
                        <th width="80">Knit. Issued To Dyeing</th>
                        <th width="80">Knit. Left Over</th>

                        <th width="80">Fin Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Fin Prod. Inside</th>
                        <th width="80">Fin Prod. SubCon</th>
                        <th width="80">Fin Trans. In</th>
                        <th width="80">Fin Trans. Out</th>
                        <th width="80">Fin Prod. Total</th>
                        <th width="80">Fin Process Loss</th>
                        <th width="80">Fin Under or Over</th>
                        <th width="80">Fin Issue To Cut</th>
                        <th width="80">Fin Left Over</th>

                        <th width="80">Woven Fabric Req.</th>
                        <th width="80">Woven Fabric Received</th>
                        <th width="80">Woven Fab. Rec. Bal.</th>
                        <th width="80">Woven Fabric Issue</th>
                        <th width="80">Woven Fabric Issue Bal.</th>


                        <th width="80">Gmts. Req (Po Qty)</th>
                        <th width="80">Cutting Qty</th>
                        <th width="80">Gmts. Print Issued In</th>
                        <th width="80">Gmts. Print Issued SubCon</th>
                        <th width="80">Gmts. Total Print Issued</th>
                        <th width="80">Gmts. Print Rec. Inside</th>
                        <th width="80">Gmts. Print Rec. SubCon</th>
                        <th width="80">Gmts. Total Rec. Print</th>
                        <th width="80">Gmts. Reject</th>

                        <th width="80">Sew. Input Inside</th>
                        <th width="80">Sew. Input SubCon</th>
                        <th width="80">Total Sew. Input</th>
                        <th width="80">Sew. Input Balance</th>
                        <th width="100">Accessories Status</th>
                        <th width="80">Sew. Out Inside</th>
                        <th width="80">Sew Out SubCon</th>
                        <th width="80">Total Out Sew</th>
                        <th width="80">Sew Out Balance</th>
                        <th width="80">Sew Out Reject</th>

                        <th width="80">Wash Inside</th>
                        <th width="80">Wash SubCon</th>
                        <th width="80">Total Wash</th>
                        <th width="80">Wash Balance</th>

                        <th width="80">Finish Inside</th>
                        <th width="80">Finish SubCon</th>
                        <th width="80">Total Finish</th>
                        <th width="80">Finish Balance</th>
                        <th width="80">Finish Reject</th>
                        <th width="80">Total Reject</th>
                        <th width="80">Current Ex-Fact.Qty</th>
                        <th width="80">TTL Ex-Factory</th>
                        <th width="80">Left Over</th>
                        <th width="80">Short/Exces Ex-Fac.Qty</th>
                        <th width="80">Process Loss Yarn</th>
                        <th width="80" title="Knit Proces Loss*100/Total Yarn Issue">Process Loss Yarn &percnt;</th>
                        <th width="80">Process Loss Dyeing</th>
                        <th width="80" title="(Knit issue to dyeing- Fin Production Total)*100/Knit issue to dyeing">Process Loss Dyeing &percnt;</th>
                        <th width="80">Process Loss Cutting</th>
                        <th  width="80" title="(Total Cutting Qty-Total Order Qty)*100/total ordr qty">Process Loss Cutting &percnt;</th>
                        <th width="80" title="(Actual cut qyt-TTL Ex-fact Qtys">Cut to Ship</th>
                        <th width="80" title="TTL Ex-Fact Qty/Actual Cut Qty*100">Cut to Ship Percentage</th>
                        <th width="80" title=" Order Qty (Pcs)-TTL Ex-fact Qty">Order to Ship Qty</th>
                        <th width="" title="TTL Ex-Fact Qty/Order Qty (pcs)*100">Order to Ship Percentage</th>

                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

                <?
					$i=1;$tot_order_value=$tot_shortExcess_exFactory_qty=0;
					if($cbo_search_type==1)
					{
						foreach($result_data_arr as $po_id=>$val)
						{
							$ratio=$val["ratio"];$ref_no=$val["ref_no"];
							$tot_po_qnty=$val["po_qnty"];
							$exfactory_qnty=$tot_ex_factory_qty_arr[$po_id]-$ex_factory_qty_arr[$po_id];//$tot_ex_factory_qty_arr[$po_id];
							$current_ex_fact_qnty=$curr_tot_ex_factory_qty_arr[$po_id];//$val["ex_factory_qnty"];

							$plan_cut_qty=$val["plan_cut"];
							$job_no=$val["job_no"];
							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$ratio;

							$yarn_req_job=$yarn_qty_arr[$po_id];//$dataArrayYarnReq[$job_no];
							$yarn_required=$yarn_qty_arr[$po_id];//$plan_cut_qty*($yarn_req_job/$dzn_qnty);
							$yarn_issue_inside=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
							$yarn_issue_outside=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
							$transfer_in_qnty_yarn=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
							$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
							$under_over_issued=$yarn_required-$total_issued;

							$grey_fabric_req_qnty=$booking_req_arr[$po_id]['gray'];//$grey_finish_require_arr[$po_id]["grey_req"];
							$knit_qnty_in=$kniting_prod_arr[$po_id]["knit_qnty_in"];
							$knit_qnty_out=$kniting_prod_arr[$po_id]["knit_qnty_out"];
							$knit_gray_rec=$kniting_prod_arr[$po_id]["knit_qnty_rec"];
							$transfer_in_qnty_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];

							$total_knitting=$knit_qnty_in+$knit_qnty_out+$transfer_in_qnty_knit-$transfer_out_qnty_knit;

							$process_loss=($yarn_issue_inside+$yarn_issue_outside)-($knit_qnty_in+$knit_qnty_out);
							$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
							$issuedToDyeQnty=$grey_cut_issue_arr[$po_id]["grey_issue_qnty"];
							$left_over=$total_knitting-$issuedToDyeQnty;

							$finish_fabric_req_qnty=$booking_req_arr[$po_id]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							$finish_qnty_in=$finish_prod_arr[$po_id]["finish_qnty_in"];
							$finish_qnty_out=$finish_prod_arr[$po_id]["finish_qnty_out"];
							$transfer_in_qnty_finish=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];
							$total_finishing=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
							$process_loss_dyeing=$issuedToDyeQnty-($finish_qnty_in+$finish_qnty_out);
							$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
							$issuedToCutQnty=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];
							$finish_left_over=$total_finishing-$issuedToCutQnty;

							$wovenReqQty=$booking_req_arr[$po_id]['woven'];
							$wovenRecQty=$finish_prod_arr[$po_id]["woven_rec"];
							$wovenFabRecBal=$wovenReqQty-$wovenRecQty;
							$wovenIssueQty=$grey_cut_issue_arr[$po_id]["woven_issue"];
							$wovenFabIssueBal=$wovenRecQty-$wovenIssueQty;


							$cuttingQty=$garment_prod_data_arr[$po_id]['cutting_qnty'];
							if($finish_consumtion_arr[$po_id] !=0){
								$possible_cut_pcs=$issuedToCutQnty/$finish_consumtion_arr[$po_id];
							}
							else{
								$possible_cut_pcs = 0;
							}

							$cutting_process_loss=$possible_cut_pcs-$cuttingQty;

							$print_issue_qnty_in=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
							$print_issue_qnty_out=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
							$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
							$print_recv_qnty_in=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
							$print_recv_qnty_out=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
							$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
							$print_reject_qnty=$garment_prod_data_arr[$po_id]['print_reject_qnty'];

							$sew_input_qnty_in=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
							$sew_input_qnty_out=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
							$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
							$sew_input_balance_qnty=$tot_po_qnty-$total_sew_input;

							$sew_recv_qnty_in=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
							$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;
							$sew_reject_qnty=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];
							$cutting_reject_qnty=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

							$wash_recv_qnty_in=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
							$wash_recv_qnty_out=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
							$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
							$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

							$gmt_finish_in=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
							$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
							$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
							$finish_reject_qnty=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
							$left_over_finish_gmts=$total_gmts_finish_qnty-$exfactory_qnty;

							$short_excess_exFactoryQty=$tot_po_qnty-$exfactory_qnty;

							$trims_recv=$trims_array[$po_id]['recv'];
							$trims_issue=$trims_array[$po_id]['iss'];
							$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

							$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
							$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;


							$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
							$total_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$finish_reject_qnty+$iron_rej_qnty+$emb_reject_qnty;
							$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($total_reject_qnty,2).'</a></p>';
							if($cbo_date_category==1 || $cbo_date_category==2)
							{
								$closing_date=$Ref_closing_arr[$po_id];
							}
							else
							{
								$closing_date=$val["closing_date"];
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="40"><? echo $i; ?></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$val["buyer_name"]]; ?></div></td>
								<td width="50" align="center"><p><? echo $val["job_year"]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $val["style_ref_no"]; ?></div></td>

								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $val["po_number"]; ?></div></td>
                                <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty); ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($val["unit_price"],4); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($val["po_total_price"]); ?></p></td>
								<td width="70"><p><? if(trim($val["pub_shipment_date"])!="" && trim($val["pub_shipment_date"])!='0000-00-00') echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</p></td>
								<td width="70"><p><? if(trim($val["ex_factory_date"])!="" && trim($val["ex_factory_date"])!='0000-00-00') echo change_date_format($val["ex_factory_date"]); ?>&nbsp;</p></td>
                                <td width="70"><p><? if(trim($closing_date)!="" && trim($closing_date)!='0000-00-00') echo change_date_format($closing_date); ?>&nbsp;</p></td>

								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_inside,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_outside,2);?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_issued,2); ?></td>

								<td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
                                <td align="right" width="80"><? echo number_format($knit_gray_rec,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToDyeQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToCutQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($wovenReqQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenRecQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenFabRecBal,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenIssueQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenFabIssueBal,2); ?></td>

								<td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($plan_cut_qty); ?></td>
                                <td align="right" width="80"><? echo number_format($cuttingQty); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($sew_input_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_input); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_balance_qnty); ?></td>

								<td align="center" width="100"><a href="javascript:open_trims_dtls('<? echo $po_id;?>','<? echo $tot_po_qnty; ?>','<? echo $ratio; ?>','Trims Info','trims_popup')">View</a></td>

								<td align="right" width="80"><? echo number_format($sew_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($wash_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($wash_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_wash_recv); ?></td>
								<td align="right" width="80"><? echo number_format($wash_balance_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($gmt_finish_in); ?></td>
								<td align="right" width="80"><? echo number_format($gmt_finish_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_gmts_finish_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_balance_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>
                                <td align="right" width="80"><? echo $reject_button; ?></td>
                                <td align="right" width="80"><? echo number_format($current_ex_fact_qnty); ?></td>
								<td align="right" width="80">
								<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($exfactory_qnty); ?></a>
								<? //echo number_format($exfactory_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
								<td align="right" width="80"><? echo number_format($short_excess_exFactoryQty); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss); ?></td>
								<td align="right" width="80"><?
									if($total_issued!=0){
										$process_loss_yern_per = ($process_loss*100)/$total_issued;
									}
									else{
										$process_loss_yern_per = 0;
									}
								 	echo number_format($process_loss_yern_per);
								 ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_dyeing); ?></td>
								<td align="right" width="80"><?
									if($issuedToDyeQnty != 0){
										$process_loss_dyeing_per = ($issuedToDyeQnty-$total_finishing)*100/$issuedToDyeQnty;
									}
									else{
										$process_loss_dyeing_per = 0;
									}
									echo number_format($process_loss_dyeing_per);
								?></td>
								<td align="right" width="80"><? echo number_format($cutting_process_loss); ?></td>
								<td align="right" width="80"><?
									if($tot_po_qnty!=0){
										$process_loss_cutting_per = ($cuttingQty-$tot_po_qnty)*100/$tot_po_qnty;
									}
									else{
										$process_loss_cutting_per = 0;
									}
									echo number_format($process_loss_cutting_per);
								?></td>
                                <td align="right" width="80"><? $cut_to_ship=$cuttingQty-$exfactory_qnty; echo number_format($cut_to_ship,0); ?></td>
                                <td align="right" width="80"><? $cut_to_ship_per=$exfactory_qnty/$cuttingQty*100;  if($cuttingQty) echo number_format($cut_to_ship_per,0);else echo 0; ?></td>
                                <td align="right" width="80"><? $order_to_ship=$tot_po_qnty-$exfactory_qnty;echo number_format($order_to_ship); ?></td>
                                <td align="right" width=""><?  $order_to_ship_per=$exfactory_qnty/$tot_po_qnty*100;if($exfactory_qnty) echo number_format($order_to_ship_per);else echo 0; ?></td>
							</tr>
							<?
							$tot_order_qty+=$tot_po_qnty;
							$tot_order_value+=$val["po_total_price"];
							$tot_plan_cut_qty+=$plan_cut_qty;
							$tot_cut_to_ship+=$cut_to_ship;
							$tot_order_to_ship+=$order_to_ship;
							$tot_current_ex_fact_qnty+=$current_ex_fact_qnty;

							$tot_yarn_req_qty+=$yarn_required;
							$tot_yarn_issIn_qty+=$yarn_issue_inside;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;
							$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
							$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
							$tot_yarn_issue_qty+=$total_issued;
							$tot_yarn_undOvr_qty+=$under_over_issued;

							$tot_grey_req_qty+=$grey_fabric_req_qnty;
							$tot_grey_in_qty+=$knit_qnty_in;
							$tot_grey_out_qty+=$knit_qnty_out;
							$tot_grey_rec_qty+=$knit_gray_rec;
							$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
							$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
							$tot_grey_qty+=$total_knitting;
							$tot_grey_prLoss_qty+=$process_loss;
							$tot_grey_undOver_qty+=$under_over_prod;
							$tot_grey_issDye_qty+=$issuedToDyeQnty;
							$tot_grey_lftOver_qty+=$left_over;

							$tot_fin_req_qty+=$finish_fabric_req_qnty;
							$tot_fin_in_qty+=$finish_qnty_in;
							$tot_fin_out_qty+=$finish_qnty_out;
							$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
							$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
							$tot_fin_qty+=$total_finishing;
							$tot_fin_prLoss_qty+=$process_loss_finishing;
							$tot_fin_undOver_qty+=$under_over_finish_prod;
							$tot_fin_issCut_qty+=$issuedToCutQnty;
							$tot_fin_lftOver_qty+=$finish_left_over;

							$tot_wovenReqQty+=$wovenReqQty;
							$tot_wovenRecQty+=$wovenRecQty;
							$tot_wovenRecBalQty+=$wovenFabRecBal;
							$tot_wovenIssueQty+=$wovenIssueQty;
							$tot_wovenIssueBalQty+=$wovenFabIssueBal;

							$tot_gmt_qty+=$plan_cut_qty;
							$tot_cutting_qty+=$cuttingQty;
							$tot_printIssIn_qty+=$print_issue_qnty_in;
							$tot_printIssOut_qty+=$print_issue_qnty_out;
							$tot_printIssue_qty+=$total_print_issued;
							$tot_printRcvIn_qty+=$print_recv_qnty_in;
							$tot_printRcvOut_qty+=$print_recv_qnty_out;
							$tot_printRcv_qty+=$total_print_recv;
							$tot_printRjt_qty+=$print_reject_qnty;

							$tot_sewInInput_qty+=$sew_input_qnty_in;
							$tot_sewInOutput_qty+=$sew_input_qnty_out;
							$tot_sewIn_qty+=$total_sew_input;
							$tot_sewInBal_qty+=$sew_input_balance_qnty;

							$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
							$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
							$tot_sewRcv_qty+=$total_sew_recv;
							$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
							$tot_sewRcvRjt_qty+=$sew_reject_qnty;

							$tot_washRcvIn_qty+=$wash_recv_qnty_in;
							$tot_washRcvOut_qty+=$wash_recv_qnty_out;
							$tot_washRcv_qty+=$total_wash_recv;
							$tot_washRcvBal_qty+=$wash_balance_qnty;

							$tot_gmtFinIn_qty+=$gmt_finish_in;
							$tot_gmtFinOut_qty+=$gmt_finish_out;
							$tot_gmtFin_qty+=$total_gmts_finish_qnty;
							$tot_gmtFinBal_qty+=$finish_balance_qnty;
							$tot_gmtFinRjt_qty+=$finish_reject_qnty;
							$tot_gmtEx_qty+=$exfactory_qnty;
							$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

							$tot_shortExcess_exFactory_qty+=$short_excess_exFactoryQty;
							$tot_prLoss_qty+=$process_loss;
							$tot_prLossDye_qty+=$process_loss_dyeing;
							$tot_prLossCut_qty+=$cutting_process_loss;
							$tot_process_loss_yern_per += $process_loss_yern_per;
							$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
							$tot_process_loss_cutting_per += $process_loss_cutting_per;
							$i++;
						}
					}
					else
					{
						foreach($result_job_wise as $po_id_job)
						{
							$po_id_arr=array_unique(explode(",",substr($po_id_job,0,-1)));
							$tot_po_qnty=$yarn_required=$tot_exfactory_qnty=$grey_fabric_req_qnty=$finish_fabric_req_qnty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$transfer_in_qnty_knit=$transfer_in_qnty_finish=$transfer_out_qnty_finish=$knit_qnty_in=$issuedToDyeQnty=$issuedToCutQnty=$finish_qnty_in=$finish_qnty_out=$finish_reject_qnty=$print_issue_qnty_in=$print_issue_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_recv_qnty_in=$sew_recv_qnty_out=$sew_reject_qnty=$wash_recv_qnty_in=$wash_recv_qnty_out=$trims_recv=$trims_issue=$emb_reject_qnty=$gmt_finish_in=$gmt_finish_out=$gmt_finish_reject_qnty=$total_reject_qnty=$tot_process_loss_yern_per=$tot_process_loss_dyeing_per=0;
							foreach($po_id_arr as $po_id)
							{
								$tot_po_qnty +=$result_data_arr[$po_id]["po_qnty"];
								$tot_exfactory_qnty +=$result_data_arr[$po_id]["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
								$yarn_required+=$yarn_qty_arr[$po_id];
								$grey_fabric_req_qnty +=$booking_req_arr[$po_id]['gray'];;
								$finish_fabric_req_qnty +=$booking_req_arr[$po_id]['fin'];

								$yarn_issue_inside +=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
								$yarn_issue_outside +=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
								$transfer_in_qnty_yarn +=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
								$transfer_out_qnty_yarn +=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
								$transfer_in_qnty_knit +=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
								$transfer_out_qnty_knit +=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];
								$transfer_in_qnty_finish +=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
								$transfer_out_qnty_finish +=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];

								$total_issued =$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
								$under_over_issued =$grey_fabric_req_qnty-$total_issued;

								$knit_qnty_in +=$kniting_prod_arr[$po_id]["knit_qnty_in"];
								$knit_qnty_out +=$kniting_prod_arr[$po_id]["knit_qnty_out"];
								$total_knitting=$knit_qnty_in+$knit_qnty_out+$transfer_in_qnty_knit-$transfer_out_qnty_knit;
								$process_loss=($knit_qnty_in+$knit_qnty_out)-$total_issued;
								$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
								$issuedToDyeQnty +=$grey_cut_issue_arr[$po_id]["grey_issue_qnty"];
								$left_over=$total_knitting-$issuedToDyeQnty;

								$issuedToCutQnty +=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];

								$finish_qnty_in +=$finish_prod_arr[$po_id]["finish_qnty_in"];
								$finish_qnty_out +=$finish_prod_arr[$po_id]["finish_qnty_out"];
								$total_finish_qnty=$finish_qnty_in+$finish_qnty_out;
								$finish_balance_qnty=$tot_po_qnty-$total_finish_qnty;
								$finish_reject_qnty +=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
								$left_over_finish_gmts=$total_finish_qnty-$tot_exfactory_qnty;

								$total_finishing=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
								$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;
								$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
								$finish_left_over=$total_finishing-$issuedToCutQnty;

								$print_issue_qnty_in +=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
								$print_issue_qnty_out +=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
								$print_recv_qnty_in +=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
								$print_recv_qnty_out +=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
								$print_reject_qnty +=$garment_prod_data_arr[$po_id]['print_reject_qnty'];

								$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
								$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;

								$sew_input_qnty_in +=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
								$sew_input_qnty_out +=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
								$total_sew_issued=$sew_input_qnty_in+$sew_input_qnty_out;
								$sew_balance_qnty=$tot_po_qnty-$total_sew_issued;

								$sew_recv_qnty_in +=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
								$sew_recv_qnty_out +=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
								$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;

								$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;

								$sew_reject_qnty +=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];

								$wash_recv_qnty_in +=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
								$wash_recv_qnty_out +=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
								$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
								$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

								$gmt_finish_in+=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
								$gmt_finish_out+=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
								$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
								$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
								$gmt_finish_reject_qnty+=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
								$left_over_finish_gmts=$total_gmts_finish_qnty-$tot_exfactory_qnty;

								$trims_recv+=$trims_array[$po_id]['recv'];
								$trims_issue+=$trims_array[$po_id]['iss'];
								$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

								$emb_reject_qnty +=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
								$cutting_reject_qnty +=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

								$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
								$tot_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$gmt_finish_reject_qnty+$iron_rej_qnty+$emb_reject_qnty;
								$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($tot_reject_qnty,2).'</a></p>';
							}
							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="40"><? echo $i; ?></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$result_data_arr[$po_id]["buyer_name"]]; ?></div></td>
								<td width="50" align="center"><? echo $result_data_arr[$po_id]["job_year"]; ?></td>
								<td width="50" align="center"><? echo $result_data_arr[$po_id]["job_no_prefix_num"]; ?></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $result_data_arr[$po_id]["style_ref_no"]; ?></div></td>
								<td width="80" align="right" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty,0); ?></td>

								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_inside,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_outside,2);?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_issued,2); ?></td>

								<td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToDyeQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToCutQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_left_over,2); ?></td>

								<td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($sew_input_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_issued); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_qnty); ?></td>

								<td width="100" align="center">View</td>

								<td align="right" width="80"><? echo number_format($sew_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>

								<td width="80" align="right"><? echo number_format($wash_recv_qnty_in); ?></td>
								<td width="80" align="right"><? echo number_format($wash_recv_qnty_out); ?></td>
								<td width="80" align="right"><? echo number_format($total_wash_recv); ?></td>
								<td width="80" align="right"><? echo number_format($wash_balance_qnty); ?></td>

								<td width="80" align="right"><? echo number_format($gmt_finish_in); ?></td>
								<td width="80" align="right"><? echo number_format($gmt_finish_out); ?></td>
								<td width="80" align="right"><? echo number_format($total_gmts_finish_qnty); ?></td>
								<td width="80" align="right"><? echo number_format($finish_balance_qnty); ?></td>
								<td width="80" align="right"><? echo number_format($gmt_finish_reject_qnty); ?></td>
                                 <td align="right" width="80"><? echo $reject_button; ?></td>

								<td align="right" width="80">
								<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $result_data_arr[$po_id]["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($tot_exfactory_qnty); ?></a>
								<? //echo number_format($tot_exfactory_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>

								<td align="right" width="80"><? echo number_format($left_over); ?></td>
								<td align="right" width="80"><? echo number_format($finish_left_over); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
								<td align="right" width="80"><? echo number_format($tot_trims_left_over_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($emb_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($process_loss); ?></td>
								<td align="right" width="80"><?
									if($total_issued!=0){
										$process_loss_yern_per = ($process_loss*100)/$total_issued;
									}
									else{
										$process_loss_yern_per = 0;
									}
								 	echo number_format($process_loss_yern_per);
								 ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_dyeing); ?></td>
								<td align="right" width="80"><?
									if($issuedToDyeQnty != 0){
										$process_loss_dyeing_per = ($issuedToDyeQnty-$total_finishing)*100/$issuedToDyeQnty;
									}
									else{
										$process_loss_dyeing_per = 0;
									}
									echo number_format($process_loss_dyeing_per);
								?></td>
								<td align="right" width="80"><? echo number_format($process_loss_cutting); ?></td>
								<td align="right"><?
									if($tot_po_qnty!=0){
										$process_loss_cutting_per = ($cuttingQty-$tot_po_qnty)*100/$tot_po_qnty;
									}
									else{
										$process_loss_cutting_per = 0;
									}
									echo number_format($process_loss_cutting_per);
								?></td>
							</tr>
                            <?
							$tot_order_qty+=$tot_po_qnty;
							$tot_yarn_req_qty+=$yarn_required;
							$tot_yarn_issIn_qty+=$yarn_issue_inside;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;
							$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
							$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
							$tot_yarn_issue_qty+=$total_issued;
							$tot_yarn_undOvr_qty+=$under_over_issued;

							$tot_grey_req_qty+=$grey_fabric_req_qnty;
							$tot_grey_in_qty+=$knit_qnty_in;
							$tot_grey_out_qty+=$knit_qnty_out;
							$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
							$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
							$tot_grey_qty+=$total_knitting;
							$tot_grey_prLoss_qty+=$process_loss;
							$tot_grey_undOver_qty+=$under_over_prod;
							$tot_grey_issDye_qty+=$issuedToDyeQnty;
							$tot_grey_lftOver_qty+=$left_over;

							$tot_fin_req_qty+=$finish_fabric_req_qnty;
							$tot_fin_in_qty+=$finish_qnty_in;
							$tot_fin_out_qty+=$finish_qnty_out;
							$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
							$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
							$tot_fin_qty+=$total_finishing;
							$tot_fin_prLoss_qty+=$process_loss_finishing;
							$tot_fin_undOver_qty+=$under_over_finish_prod;
							$tot_fin_issCut_qty+=$issuedToCutQnty;
							$tot_fin_lftOver_qty+=$finish_left_over;

							$tot_gmt_qty+=$tot_po_qnty;
							$tot_printIssIn_qty+=$print_issue_qnty_in;
							$tot_printIssOut_qty+=$print_issue_qnty_out;
							$tot_printIssue_qty+=$total_print_issued;
							$tot_printRcvIn_qty+=$print_recv_qnty_in;
							$tot_printRcvOut_qty+=$print_recv_qnty_out;
							$tot_printRcv_qty+=$total_print_recv;
							$tot_printRjt_qty+=$print_reject_qnty;

							$tot_sewInInput_qty+=$sew_input_qnty_in;
							$tot_sewInOutput_qty+=$sew_input_qnty_out;
							$tot_sewIn_qty+=$total_sew_issued;
							$tot_sewInBal_qty+=$sew_balance_qnty;

							$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
							$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
							$tot_sewRcv_qty+=$total_sew_recv;
							$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
							$tot_sewRcvRjt_qty+=$sew_reject_qnty;

							$tot_washRcvIn_qty+=$wash_recv_qnty_in;
							$tot_washRcvOut_qty+=$wash_recv_qnty_out;
							$tot_washRcv_qty+=$total_wash_recv;
							$tot_washRcvBal_qty+=$wash_balance_qnty;

							$tot_gmtFinIn_qty+=$gmt_finish_in;
							$tot_gmtFinOut_qty+=$gmt_finish_out;
							$tot_gmtFin_qty+=$total_gmts_finish_qnty;
							$tot_gmtFinBal_qty+=$finish_balance_qnty;
							$tot_gmtFinRjt_qty+=$gmt_finish_reject_qnty;
							$tot_gmtEx_qty+=$tot_exfactory_qnty;
							$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

							$tot_leftOver_qty+=$left_over;
							$tot_leftOverFin_qty+=$finish_left_over;
							$tot_leftOverGmtFin_qty+=$left_over_finish_gmts;
							$tot_leftOverTrm_qty+=$tot_trims_left_over_qnty;

							$tot_rjtPrint_qty+=$print_reject_qnty;
							$tot_rjtEmb_qty+=$emb_reject_qnty;
							$tot_rjtSew_qty+=$sew_reject_qnty;
							$tot_rjtFin_qty+=$finish_reject_qnty;

							$tot_prLoss_qty+=$process_loss;
							$tot_prLossFin_qty+=$process_loss_finishing;
							$tot_prLossCut_qty+=$cutting_process_loss;
							$total_reject_qnty+=$tot_reject_qnty;

							$tot_process_loss_yern_per += $process_loss_yern_per;
							$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
							$tot_process_loss_cutting_per += $process_loss_cutting_per;
							$i++;
						}
					}
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="50">&nbsp;</td>

                    <td width="100">Total :</td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="100">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="" bgcolor="#FFFFCC"><? //echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_order_val" bgcolor="#FFFFCC"><? echo number_format($tot_order_value); ?></td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="70" align="right" id=""><? //echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issIn_qty"><? echo number_format($tot_yarn_issIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issOut_qty"><? echo number_format($tot_yarn_issOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_trnsInq_qty"><? echo number_format($tot_yarn_trnsInq_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_trnsOut_qty"><? echo number_format($tot_yarn_trnsOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_undOvr_qty"><? echo number_format($tot_yarn_undOvr_qty,2); ?></td>

                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($tot_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_in_qty"><? echo number_format($tot_grey_in_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_out_qty"><? echo number_format($tot_grey_out_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_trnsIn_qty"><? echo number_format($tot_grey_trnsIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_transOut_qty"><? echo number_format($tot_grey_transOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_qty"><? echo number_format($tot_grey_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_rec_qty"><? echo number_format($tot_grey_rec_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_prLoss_qty"><? echo number_format($tot_grey_prLoss_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_undOver_qty"><? echo number_format($tot_grey_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty"><? echo number_format($tot_grey_issDye_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($tot_grey_lftOver_qty,2); ?></td>

                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_in_qty"><? echo number_format($tot_fin_in_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_out_qty"><? echo number_format($tot_fin_out_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_transIn_qty"><? echo number_format($tot_fin_transIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_transOut_qty"><? echo number_format($tot_fin_transOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_fin_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_prLoss_qty"><? echo number_format($tot_fin_prLoss_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($tot_fin_issCut_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_fin_lftOver_qty,2); ?></td>

                    <td width="80" align="right" id="td_wovenReqQty"><? echo number_format($tot_wovenReqQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenRecQty"><? echo number_format($tot_wovenRecQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenRecBalQty"><? echo number_format($tot_wovenRecBalQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenIssueQty"><? echo number_format($tot_wovenIssueQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenIssueBalQty"><? echo number_format($tot_wovenIssueBalQty,2); ?></td>


                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_gmt_qty,0); ?></td>
                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cutting_qty); ?></td>
                    <td width="80" align="right" id="td_printIssIn_qty"><? echo number_format($tot_printIssIn_qty); ?></td>
                    <td width="80" align="right" id="td_printIssOut_qty"><? echo number_format($tot_printIssOut_qty); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty"><? echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_printRcvIn_qty"><? echo number_format($tot_printRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_printRcvOut_qty"><? echo number_format($tot_printRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_printRcv_qty"><? echo number_format($tot_printRcv_qty); ?></td>
                    <td width="80" align="right" id="td_printRjt_qty"><? echo number_format($tot_printRjt_qty); ?></td>

                    <td width="80" align="right" id="td_sewInInput_qty"><? echo number_format($tot_sewInInput_qty); ?></td>
                    <td width="80" align="right" id="td_sewInOutput_qty"><? echo number_format($tot_sewInOutput_qty); ?></td>
                    <td width="80" align="right" id="td_sewIn_qty"><? echo number_format($tot_sewIn_qty); ?></td>
                    <td width="80" align="right" id="td_sewInBal_qty"><? echo number_format($tot_sewInBal_qty); ?></td>

                    <td width="100">&nbsp;</td>

                    <td width="80" align="right" id="td_sewRcvIn_qty"><? echo number_format($tot_sewRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvOut_qty"><? echo number_format($tot_sewRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcv_qty"><? echo number_format($tot_sewRcv_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvBal_qty"><? echo number_format($tot_sewRcvBal_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvRjt_qty"><? echo number_format($tot_sewRcvRjt_qty); ?></td>

                    <td width="80" align="right" id="td_washRcvIn_qty"><? echo number_format($tot_washRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvOut_qty"><? echo number_format($tot_washRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_washRcv_qty"><? echo number_format($tot_washRcv_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvBal_qty"><? echo number_format($tot_washRcvBal_qty); ?></td>

                    <td width="80" align="right" id="td_gmtFinIn_qty"><? echo number_format($tot_gmtFinIn_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinOut_qty"><? echo number_format($tot_gmtFinOut_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFin_qty"><? echo number_format($tot_gmtFin_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinBal_qty"><? echo number_format($tot_gmtFinBal_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinRjt_qty"><? echo number_format($tot_gmtFinRjt_qty); ?></td>
                   <td width="80" align="right" id="td_gmtrej_qty"><? echo number_format($total_reject_qnty); ?></td>
                   <td width="80" align="right" id="td_current_ex_qty"><? echo number_format($tot_current_ex_fact_qnty); ?></td>
                    <td width="80" align="right" id="td_gmtEx_qty"><? echo number_format($tot_gmtEx_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinLeftOver_qty"><? echo number_format($tot_gmtFinLeftOver_qty); ?></td>

                    <td width="80" align="right" id="td_shortExcess_exFactory_qty"><? echo number_format($tot_shortExcess_exFactory_qty); ?></td>

                    <td width="80" align="right" id="td_prLoss_qty"><? echo number_format($tot_prLoss_qty); ?></td>
                    <td width="80" align="right" id="td_prLoss_yarn_qty"><? echo number_format($tot_process_loss_yern_per); ?></td>
                    <td width="80" align="right" id="td_prLossDye_qty"><? echo number_format($tot_prLossDye_qty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_dyeing_per); ?></td>
                    <td align="right"  width="80" id="td_prLossCut_qty"><? echo number_format($tot_prLossCut_qty); ?></td>
                    <td width="80" align="right" id="td_prLoss_qty_cut"><? echo number_format($tot_process_loss_cutting_per); ?></td>
                     <td width="80" align="right" id="td_qty_cut_to_ship"><? echo number_format($tot_cut_to_ship); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_cutting_per); ?></td>
                    <td width="80" align="right" id="td_qty_order_to_ship"><? echo number_format($tot_order_to_ship); ?></td>
                    <td width="" align="right" id=""><? //echo number_format($tot_process_loss_cutting_per); ?></td>
                </tr>
           </table>
        </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$cbo_search_type**$type";
    exit();
}

if($action=="report_generate5")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$ref_no=str_replace("'","",$txt_ref_no);
	$file_no=str_replace("'","",$txt_file_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	//$txt_ex_date_to=str_replace("'","",$txt_ex_date_to);
	$type=str_replace("'","",$type);

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	$ship_date_cond="";$ship_date_cond2="";
	if($cbo_date_category==1)
	{

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
		$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
	}
	}
	else if($cbo_date_category==2)
	{
		//$ex_fact_date_cond="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and c.ex_factory_date between '$txt_date_from' and '$txt_date_to' AND c.is_deleted = 0 AND c.status_active = 1";
			$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";

		}
	}
	else if($cbo_date_category==3) //Ref Closing date
	{

		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
			$ship_date_cond="and d.closing_date between '$txt_date_from' and '$txt_date_to'";
		}
	}


	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
	$order_cond="";
	if(trim($ref_no)!="") $ref_no_cond="and b.grouping in('$ref_no')";
	if(trim($file_no)!="") $file_no_cond="and b.file_no in('$file_no')";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	if($txt_order_id!="") $order_cond="and b.id in($txt_order_id)";
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	//if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	if($cbo_date_category==2) // Ex-Fact Date
	{
		$sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
		where a.job_no=b.job_no_mst and  b.id=c.po_break_down_id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else if($cbo_date_category==1)  // Ship Date Date
	{
		 $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1
		where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else if($cbo_date_category==3) //ref Closing
	{
		  $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,d.closing_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty
		from wo_po_details_master a, inv_reference_closing d,wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id
		where a.job_no=b.job_no_mst  and  b.id=d.inv_pur_req_mst_id  and d.reference_type=163 and d.closing_status=1 and b.shiping_status=3  and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	//echo $sql_po;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array(); $poIdArr=array();
	foreach($sql_po_result as $row)
	{
		//if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];

		$poIdArr[$row[csf("po_id")]]=$row[csf("po_id")];

		$result_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$result_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["ref_no"]=$row[csf("grouping")];
		$result_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("po_id")]]["po_total_price"]=$row[csf("po_total_price")];
		$result_data_arr[$row[csf("po_id")]]["unit_price"]=$row[csf("unit_price")];
		$result_data_arr[$row[csf("po_id")]]["po_qnty"]=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")];
		$result_data_arr[$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("po_id")]]["closing_date"]=$row[csf("closing_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
		$JobArr[]="'".$row[csf('job_no')]."'";
		$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
	}



		//==========================================================================================
		$yarn_purchase_req_data=sql_select("select a.id as mst_id, a.company_id,a.basis,b.booking_no,b.booking_id, b.id, b.mst_id, b.job_id, b.job_no, b.buyer_id, b.style_ref_no, b.color_id, b.count_id, b.composition_id,b.com_percent, b.yarn_type_id, b.cons_uom, b.quantity, b.rate, b.amount, b.yarn_inhouse_date, b.remarks,b.yarn_finish,b.yarn_spinning_system,b.certification from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($job_arr,1,'b.job_no')." order by b.id asc ");

			foreach($yarn_purchase_req_data as $row){

						$yarn_purchase_req_arr[$row[csf('job_no')]]['qnty']+=$row[csf('quantity')];
			}



		$fabric_booking_data=sql_select("select a.id as booking_dtls_id,b.id, a.fabric_color_id, a.fin_fab_qnty, a.grey_fab_qnty, a.amount, a.rate, a.po_break_down_id
		from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id 	".where_con_using_array($job_arr,1,'a.job_no')." and a.booking_type=1 and a.status_active=1 and a.is_deleted=0");

		foreach($fabric_booking_data as $row){

			$fabric_booking_arr[$row[csf('po_break_down_id')]]['qnty']+=$row[csf('grey_fab_qnty')];
			}

		//========================================================================================




		// echo "<pre>";
		// print_r($JobArr);



	/*$yarn= new yarn($JobArr,'job');
	$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();*/
	//print_r($yarn_qty_arr);
	//$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));

	$all_po_id=implode(",",$poIdArr);
	/*$poIds=chop($all_po_id,','); $po_cond_for_in="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
	$po_cond_for_in=" and (";
	$poIdsArr=array_chunk(explode(",",$poIds),999);
	foreach($poIdsArr as $ids)
	{
	$ids=implode(",",$ids);
	$po_cond_for_in.=" b.po_break_down_id in($ids) or";

	}
	$po_cond_for_in=chop($po_cond_for_in,'or ');
	$po_cond_for_in.=")";
	}
	else
	{
	$po_cond_for_in=" and b.po_break_down_id in($poIds)";
	}*/
	 $sql_po_ref="select b.inv_pur_req_mst_id as po_id,max(b.closing_date) as  closing_date from inv_reference_closing b
		where b.reference_type=163 and b.closing_status=1  and b.is_deleted=0 and b.status_active=1  ".where_con_using_array($poIdArr,0,'b.inv_pur_req_mst_id')." group by  b.inv_pur_req_mst_id order by b.inv_pur_req_mst_id desc";
		$sql_po_ref_result=sql_select($sql_po_ref);
		foreach($sql_po_ref_result as $row)
		{
			$Ref_closing_arr[$row[csf("po_id")]]=$row[csf("closing_date")];
		}
	//$JobNoArr=implode(",",$JobArr);
	//$yarn= new yarn($JobArr,'job');
	//$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	//print_r($yarn_qty_arr);
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(!empty($JobArr)){
	 $condition->po_id_in("$all_po_id");
	}
	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	//$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
	$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();




	$booking_req_arr=array();
	$sql_wo=sql_select("select b.po_break_down_id,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	sum(b.fin_fab_qnty) as fin_fab_qnty

	from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id");
	//and b.po_break_down_id in ($all_po_id)


	foreach ($sql_wo as $brow)
	{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['gray']=$brow[csf("grey_req_qnty")];
		$booking_req_arr[$brow[csf("po_break_down_id")]]['woven']=$brow[csf("woven_req_qnty")];
		$booking_req_arr[$brow[csf("po_break_down_id")]]['fin']=$brow[csf("fin_fab_qnty")];
	}
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_qnty,
		sum(CASE WHEN b.entry_form!=85 $ship_date_cond2 THEN b.ex_factory_qnty ELSE 0 END) as curr_ex_fact_qnty
		from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id");
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('return_qnty')];
			$tot_ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('ex_fact_qnty')];
			$curr_tot_ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('curr_ex_fact_qnty')];
		}



	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$dataArrayYarnReq=array();
	$yarn_sql="select job_no, sum(avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarnReq[$yarnRow[csf('job_no')]]=$yarnRow[csf('qnty')];
	}

	$reqDataArray=sql_select("select  a.po_break_down_id, sum((b.requirment/b.pcs)*a.plan_cut_qnty) as grey_req, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 ".where_con_using_array($poIdArr,0,'a.po_break_down_id')." group by a.po_break_down_id");//and a.po_break_down_id in ($all_po_id)
	$grey_finish_require_arr=array();
	foreach($reqDataArray as $row)
	{
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["grey_req"]=$row[csf("grey_req")];
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["finish_req"]=$row[csf("finish_req")];
	}

	$yarnDataArr=sql_select("select a.po_breakdown_id,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_issue_master c
						where a.trans_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')."  and b.item_category=1 and c.issue_purpose in (1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
						group by a.po_breakdown_id");//and a.po_breakdown_id in($all_po_id)
	$yarn_issue_arr=array();
	foreach($yarnDataArr as $row)
	{
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_in"]=$row[csf("issue_qnty_in")];
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_out"]=$row[csf("issue_qnty_out")];
	}

	$yarnReturnDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
				from order_wise_pro_details a, inv_transaction b, inv_receive_master c
				where a.trans_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
				group by a.po_breakdown_id");// and a.po_breakdown_id in($all_po_id)


	$yarn_issue_rtn_arr=array();
	foreach($yarnReturnDataArr as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
	}


	$dataArrayTrans=sql_select("select po_breakdown_id,
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish
							from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(11,13,15)  ".where_con_using_array($poIdArr,0,'po_breakdown_id')."
							group by po_breakdown_id");//and po_breakdown_id in($all_po_id)

	$transfer_data_arr=array();
	foreach($dataArrayTrans as $row)
	{
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_yarn"]=$row[csf("transfer_in_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_yarn"]=$row[csf("transfer_out_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_knit"]=$row[csf("transfer_in_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_knit"]=$row[csf("transfer_out_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_finish"]=$row[csf("transfer_in_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_finish"]=$row[csf("transfer_out_qnty_finish")];
	}

	// decision pending	dyed yearn receive
	//$greyYarnIssueQnty=return_library_array("select c.po_breakdown_id, sum(c.quantity) as issue_qnty from inv_transaction a, inv_issue_master b,  order_wise_pro_details c where a.mst_id=b.id and a.id=c.trans_id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and c.po_breakdown_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2 group by c.po_breakdown_id","po_breakdown_id","issue_qnty");


	//$dyedYarnRecvQnty=return_field_value("sum(a.cons_quantity) as recv_qnty","inv_transaction a, inv_receive_master b","a.mst_id=b.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1","recv_qnty");

	$prodKnitDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
				sum(CASE WHEN a.entry_form=22 THEN a.quantity ELSE 0 END) AS knit_qnty_rec
				from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and c.item_category=13 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");// and c.receive_basis<>9 and a.po_breakdown_id in($all_po_id)
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_in"]=$row[csf("knit_qnty_in")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_out"]=$row[csf("knit_qnty_out")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec"]=$row[csf("knit_qnty_rec")];
	}



	$prodFinDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
				sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec
				from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9 group by a.po_breakdown_id");// and a.po_breakdown_id in($all_po_id)

	$finish_prod_arr=array();
	foreach($prodFinDataArr as $row)
	{
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in"]=$row[csf("finish_qnty_in")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out"]=$row[csf("finish_qnty_out")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["woven_rec"]=$row[csf("woven_rec")];
	}
	$issueData=sql_select("select po_breakdown_id,
							sum(CASE WHEN entry_form=16 THEN quantity ELSE 0 END) AS grey_issue_qnty,
							sum(CASE WHEN entry_form=61 THEN quantity ELSE 0 END) AS grey_issue_qnty_roll_wise,
							sum(CASE WHEN entry_form=18 THEN quantity ELSE 0 END) AS issue_to_cut_qnty,
							sum(CASE WHEN entry_form=71 THEN quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
							sum(CASE WHEN entry_form=19 THEN quantity ELSE 0 END) AS woven_issue
							from order_wise_pro_details where entry_form in(16,18,19,61,71)  ".where_con_using_array($poIdArr,0,'po_breakdown_id')." and status_active=1 and is_deleted=0 group by po_breakdown_id");//po_breakdown_id in($all_po_id) and


	$grey_cut_issue_arr=array();
	foreach($issueData as $row)
	{
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty"]=$row[csf("grey_issue_qnty")]+$row[csf("grey_issue_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["issue_to_cut_qnty"]=$row[csf("issue_to_cut_qnty")]+$row[csf("issue_to_cut_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["woven_issue"]=$row[csf("woven_issue")];
	}


	$trimsDataArr=sql_select("select a.po_breakdown_id,
							sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
							sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
							from order_wise_pro_details a, product_details_master b where a.prod_id=b.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");// and a.po_breakdown_id in($all_po_id)
	foreach($trimsDataArr as $row)
	{
		$trims_array[$row[csf('po_breakdown_id')]]['recv']=$row[csf('recv_qnty')];
		$trims_array[$row[csf('po_breakdown_id')]]['iss']=$row[csf('issue_qnty')];
	}

	$sql_consumtiont_qty=sql_select("select b.po_break_down_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id, c.body_part_id ");//and b.po_break_down_id in ($all_po_id)
			$finish_consumtion_arr=array();
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg=0;
				$con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];///str_replace("'","",$row_sew[csf("pcs")]);


				$finish_consumtion_arr[$row_consum[csf('po_break_down_id')]]+=$con_avg;
			}

	$gmtsProdDataArr=sql_select("select  po_break_down_id,
					sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty_in,
					sum(CASE WHEN production_type=2 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_issue_qnty_out,
					sum(CASE WHEN production_type=3 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_recv_qnty_in,
					sum(CASE WHEN production_type=3 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_recv_qnty_out,
					sum(CASE WHEN production_type=2 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_in,
					sum(CASE WHEN production_type=2 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_out,
					sum(CASE WHEN production_type=3 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_in,
					sum(CASE WHEN production_type=3 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_out,
					sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_input_qnty_in,
					sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_input_qnty_out,
					sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,
					sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
					sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS finish_qnty_in,
					sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS finish_qnty_out,
					sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
					sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,
					sum(CASE WHEN production_type=3 and embel_name=1 THEN reject_qnty ELSE 0 END) AS print_reject_qnty,
					sum(CASE WHEN production_type=3 and embel_name=2 THEN reject_qnty ELSE 0 END) AS emb_reject_qnty,
					sum(CASE WHEN production_type=5 THEN reject_qnty ELSE 0 END) AS sew_reject_qnty,
					sum(CASE WHEN production_type=8 THEN reject_qnty ELSE 0 END) AS finish_reject_qnty,
					sum(CASE WHEN production_type=1 THEN reject_qnty ELSE 0 END) AS cutting_reject_qnty,
					sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty
					from pro_garments_production_mst where  is_deleted=0 and status_active=1 ".where_con_using_array($poIdArr,0,'po_break_down_id')." group by po_break_down_id");//po_break_down_id in($all_po_id) and

	$garment_prod_data_arr=array();
	foreach($gmtsProdDataArr as $row)
	{
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_qnty']=$row[csf("cutting_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_out']=$row[csf("finish_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_in']=$row[csf("wash_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_out']=$row[csf("wash_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_reject_qnty']=$row[csf("print_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_reject_qnty']=$row[csf("emb_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_reject_qnty']=$row[csf("sew_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_reject_qnty']=$row[csf("finish_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_reject_qnty']=$row[csf("cutting_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['iron_rej_qnty']=$row[csf("iron_rej_qnty")];
	}
	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	if($cbo_search_type==1)
	{
		$tbl_width=6250;

		$ship_date_html="Shipment Date";
		$ex_fact_date_html="Ex-Fact. Date";
	}
	else
	{
		$tbl_width=5620;
		$ship_date_html="Last Shipment Date";
		$ex_fact_date_html="Last Ex-Fact. Date";
		//$ex_fact_date_html="Closing Date";
	}
	ob_start();
	?>
        <div style="width:100%">
             <table width="<? echo $tbl_width;?>">
                <tr>
                    <td align="center" width="100%" colspan="70" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
            </table>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                	<tr style="font-size:13px">
                        <th width="40" >SL</th>
                        <th width="110" >Buyer</th>
                        <th width="50" >Job Year</th>
                        <th width="50" >Job No</th>
                        <th width="100" >Style No</th>

                        <?
						if($cbo_search_type==1)
						{
							?>
                        	<th width="100" >Order No</th>
							<?
						}
						?>
                        <th width="80" >Order Qty.(Pcs)</th>
                        <th width="80">FOB</th>
                        <th width="80">Order Value</th>

                        <?
						if($cbo_search_type==1)
						{
							?>
							<th width="70" ><? echo $ship_date_html; ?></th>
							<th width="70"><? echo $ex_fact_date_html; ?></th>

							<?
						}
						?>
                        <th width="70"><? echo 'Closing Date'; ?></th>
                        <th width="80">Yarn Req.<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
						<th width="80">Yarn Purchase Req.</th>
						<th width="80">Fabric Booking </th>
                        <th width="80">Yarn Issued In</th>
                        <th width="80">Yarn Issued Out</th>

                        <th width="80">Yarn Total Issued</th>
                        <th width="80">Yarn Under or Over Issued</th>

                        <th width="80">Knit. Gray Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Knit. Prod Inside</th>
                        <th width="80">Knit. Prod SubCon</th>
                        <th width="80">Knit. Trans. In</th>
                        <th width="80">Knit. Trans. Out</th>
                        <th width="80">Knit. Total Prod.</th>
                        <th width="80">Knit. Receive</th>
                        <th width="80">Knit. Process Loss</th>
                        <th width="80">Knit. Under or Over Prod.</th>
                        <th width="80">Knit. Issued To Dyeing</th>
                        <th width="80">Knit. Left Over</th>

                        <th width="80">Fin Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Fin Prod. Inside</th>
                        <th width="80">Fin Prod. SubCon</th>
                        <th width="80">Fin Trans. In</th>
                        <th width="80">Fin Trans. Out</th>
                        <th width="80">Fin Prod. Total</th>
                        <th width="80">Fin Process Loss</th>
                        <th width="80">Fin Under or Over</th>
                        <th width="80">Fin Issue To Cut</th>
                        <th width="80">Fin Left Over</th>




                        <th width="80">Gmts. Req (Po Qty)</th>
                        <th width="80">Cutting Qty</th>
                        <th width="80">Gmts. Print Issued In</th>
                        <th width="80">Gmts. Print Issued SubCon</th>
                        <th width="80">Gmts. Total Print Issued</th>
                        <th width="80">Gmts. Print Rec. Inside</th>
                        <th width="80">Gmts. Print Rec. SubCon</th>
                        <th width="80">Gmts. Total Rec. Print</th>
                        <th width="80">Gmts. Reject</th>

                        <th width="80">Sew. Input Inside</th>
                        <th width="80">Sew. Input SubCon</th>
                        <th width="80">Total Sew. Input</th>
                        <th width="80">Sew. Input Balance</th>
                        <th width="100">Accessories Status</th>
                        <th width="80">Sew. Out Inside</th>
                        <th width="80">Sew Out SubCon</th>
                        <th width="80">Total Out Sew</th>
                        <th width="80">Sew Out Balance</th>
                        <th width="80">Sew Out Reject</th>

                        <th width="80">Wash Inside</th>
                        <th width="80">Wash SubCon</th>
                        <th width="80">Total Wash</th>
                        <th width="80">Wash Balance</th>

                        <th width="80">Finish Inside</th>
                        <th width="80">Finish SubCon</th>
                        <th width="80">Total Finish</th>
                        <th width="80">Finish Balance</th>
                        <th width="80">Finish Reject</th>
                        <th width="80">Total Reject</th>
                        <th width="80">Current Ex-Fact.Qty</th>
                        <th width="80">TTL Ex-Factory</th>
                        <th width="80">Left Over</th>
                        <th width="80">Short/Exces Ex-Fac.Qty</th>

                        <th width="80" title="(Actual cut qyt-TTL Ex-fact Qtys">Cut to Ship</th>
                        <th width="80" title="TTL Ex-Fact Qty/Actual Cut Qty*100">Cut to Ship Percentage</th>
                        <th width="80" title=" Order Qty (Pcs)-TTL Ex-fact Qty">Order to Ship Qty</th>
                        <th width="" title="TTL Ex-Fact Qty/Order Qty (pcs)*100">Order to Ship Percentage</th>

                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

                <?
					$i=1;$tot_order_value=$tot_shortExcess_exFactory_qty=0;
					if($cbo_search_type==1)
					{
						foreach($result_data_arr as $po_id=>$val)
						{
							$ratio=$val["ratio"];$ref_no=$val["ref_no"];
							$tot_po_qnty=$val["po_qnty"];
							$exfactory_qnty=$tot_ex_factory_qty_arr[$po_id]-$ex_factory_qty_arr[$po_id];//$tot_ex_factory_qty_arr[$po_id];
							$current_ex_fact_qnty=$curr_tot_ex_factory_qty_arr[$po_id];//$val["ex_factory_qnty"];

							$plan_cut_qty=$val["plan_cut"];
							$job_no=$val["job_no"];
							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$ratio;

							$yarn_req_job=$yarn_qty_arr[$po_id];//$dataArrayYarnReq[$job_no];
							$yarn_required=$yarn_qty_arr[$po_id];//$plan_cut_qty*($yarn_req_job/$dzn_qnty);
							$yarn_issue_inside=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
							$yarn_issue_outside=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
							$transfer_in_qnty_yarn=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
							$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
							$under_over_issued=$yarn_required-$total_issued;

							$grey_fabric_req_qnty=$booking_req_arr[$po_id]['gray'];//$grey_finish_require_arr[$po_id]["grey_req"];
							$knit_qnty_in=$kniting_prod_arr[$po_id]["knit_qnty_in"];
							$knit_qnty_out=$kniting_prod_arr[$po_id]["knit_qnty_out"];
							$knit_gray_rec=$kniting_prod_arr[$po_id]["knit_qnty_rec"];
							$transfer_in_qnty_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];

							$total_knitting=$knit_qnty_in+$knit_qnty_out+$transfer_in_qnty_knit-$transfer_out_qnty_knit;

							$process_loss=($yarn_issue_inside+$yarn_issue_outside)-($knit_qnty_in+$knit_qnty_out);
							$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
							$issuedToDyeQnty=$grey_cut_issue_arr[$po_id]["grey_issue_qnty"];
							$left_over=$total_knitting-$issuedToDyeQnty;

							$finish_fabric_req_qnty=$booking_req_arr[$po_id]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							$finish_qnty_in=$finish_prod_arr[$po_id]["finish_qnty_in"];
							$finish_qnty_out=$finish_prod_arr[$po_id]["finish_qnty_out"];
							$transfer_in_qnty_finish=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];
							$total_finishing=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
							$process_loss_dyeing=$issuedToDyeQnty-($finish_qnty_in+$finish_qnty_out);
							$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
							$issuedToCutQnty=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];
							$finish_left_over=$total_finishing-$issuedToCutQnty;

							$wovenReqQty=$booking_req_arr[$po_id]['woven'];
							$wovenRecQty=$finish_prod_arr[$po_id]["woven_rec"];
							$wovenFabRecBal=$wovenReqQty-$wovenRecQty;
							$wovenIssueQty=$grey_cut_issue_arr[$po_id]["woven_issue"];
							$wovenFabIssueBal=$wovenRecQty-$wovenIssueQty;


							$cuttingQty=$garment_prod_data_arr[$po_id]['cutting_qnty'];
							if($finish_consumtion_arr[$po_id] !=0){
								$possible_cut_pcs=$issuedToCutQnty/$finish_consumtion_arr[$po_id];
							}
							else{
								$possible_cut_pcs = 0;
							}

							$cutting_process_loss=$possible_cut_pcs-$cuttingQty;

							$print_issue_qnty_in=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
							$print_issue_qnty_out=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
							$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
							$print_recv_qnty_in=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
							$print_recv_qnty_out=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
							$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
							$print_reject_qnty=$garment_prod_data_arr[$po_id]['print_reject_qnty'];

							$sew_input_qnty_in=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
							$sew_input_qnty_out=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
							$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
							$sew_input_balance_qnty=$tot_po_qnty-$total_sew_input;

							$sew_recv_qnty_in=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
							$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;
							$sew_reject_qnty=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];
							$cutting_reject_qnty=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

							$wash_recv_qnty_in=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
							$wash_recv_qnty_out=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
							$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
							$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

							$gmt_finish_in=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
							$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
							$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
							$finish_reject_qnty=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
							$left_over_finish_gmts=$total_gmts_finish_qnty-$exfactory_qnty;

							$short_excess_exFactoryQty=$tot_po_qnty-$exfactory_qnty;

							$trims_recv=$trims_array[$po_id]['recv'];
							$trims_issue=$trims_array[$po_id]['iss'];
							$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

							$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
							$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;


							$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
							$total_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$finish_reject_qnty+$iron_rej_qnty+$emb_reject_qnty;
							$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($total_reject_qnty,2).'</a></p>';
							if($cbo_date_category==1 || $cbo_date_category==2)
							{
								$closing_date=$Ref_closing_arr[$po_id];
							}
							else
							{
								$closing_date=$val["closing_date"];
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="40"><? echo $i; ?></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$val["buyer_name"]]; ?></div></td>
								<td width="50" align="center"><p><? echo $val["job_year"]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $val["style_ref_no"]; ?></div></td>

								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $val["po_number"]; ?></div></td>
                                <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty); ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($val["unit_price"]); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($val["po_total_price"]); ?></p></td>
								<td width="70"><p><? if(trim($val["pub_shipment_date"])!="" && trim($val["pub_shipment_date"])!='0000-00-00') echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</p></td>
								<td width="70"><p><? if(trim($val["ex_factory_date"])!="" && trim($val["ex_factory_date"])!='0000-00-00') echo change_date_format($val["ex_factory_date"]); ?>&nbsp;</p></td>
                                <td width="70"><p><? if(trim($closing_date)!="" && trim($closing_date)!='0000-00-00') echo change_date_format($closing_date); ?>&nbsp;</p></td>
								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('yarn_purchase_req_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Yarn Purchase Req Details')"><? echo number_format($yarn_purchase_req_arr[$val['job_no']]['qnty'],2); ?></a></td>

								<td align="right" width="80"><a href="##" onClick="generate_popup('fabric_booking_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Fabric Booking Details')"><? echo number_format($fabric_booking_arr[$po_id]['qnty'],2); ?></a></td>

								<td align="right" width="80"><a href="##" onClick="generate_popup('yarn_issued_in_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Yarn Issued In Details')"><? echo number_format($yarn_issue_inside,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('yarn_issued_out_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Yarn Issued Out Details')"><? echo number_format($yarn_issue_outside,2); ?></a></td>


								<td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_issued,2); ?></td>

								<td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('knit_prod_inside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Knit Prod Inside Details')"><? echo number_format($knit_qnty_in,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('knit_prod_outside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Knit Prod Outside Details')"><? echo number_format($knit_qnty_out,2); ?></a></td>

								<td align="right" width="80"><a href="##" onClick="generate_popup('trans_in_knit_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Trans In Knit Details')"><? echo number_format($transfer_in_qnty_knit,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('trans_out_knit_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Trans Out Knit Details')"><? echo number_format($transfer_out_qnty_knit,2); ?></a></td>


								<td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>

								<td align="right" width="80"><a href="##" onClick="generate_popup('knit_receive_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Knit Receive Details')"><? echo number_format($knit_gray_rec,2); ?></a></td>

								<td align="right" width="80"><? echo number_format($process_loss,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_prod,2); ?></td>

								<td align="right" width="80"><a href="##" onClick="generate_popup('knit_issue_to_deying_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Knit Issue To Deying Details')"><? echo number_format($issuedToDyeQnty,2); ?></a></td>

								<td align="right" width="80"><? echo number_format($left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>

								<td align="right" width="80"><a href="##" onClick="generate_popup('fin_prod_inside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Fin Prod Inside Details')"><? echo number_format($finish_qnty_in,2); ?></a></td>

								<td align="right" width="80"><a href="##" onClick="generate_popup('fin_prod_outside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Fin Prod Outside Details')"><? echo number_format($finish_qnty_out,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('fin_trans_in_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Fin Trans In Details')"><? echo number_format($transfer_in_qnty_finish,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('fin_trans_out_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Fin Trans Out Details')"><? echo number_format($transfer_out_qnty_finish,2); ?></a></td>




								<td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('fin_issue_to_cut_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Fin Issue To Cut Details')"><? echo number_format($issuedToCutQnty,2); ?></a></td>



								<td align="right" width="80"><? echo number_format($finish_left_over,2); ?></td>
								<td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($plan_cut_qty); ?></td>


								<td align="right" width="80"><a href="##" onClick="generate_popup('cutting_qty_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Cutting Qty Details')"><? echo number_format($cuttingQty,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('gmts_print_issued_in_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','GMTS Print Issued In Details')"><? echo number_format($print_issue_qnty_in,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('gmts_print_issued_out_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','GMTS Print Issued Out Details')"><? echo number_format($print_issue_qnty_out,2); ?></a></td>



								<td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>


								<td align="right" width="80"><a href="##" onClick="generate_popup('sew_input_inside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Sew Input Inside Details')"><? echo number_format($sew_input_qnty_in,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('sew_input_outside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Sew Input Outside Details')"><? echo number_format($sew_input_qnty_out,2); ?></a></td>


								<td align="right" width="80"><? echo number_format($total_sew_input); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_balance_qnty); ?></td>

								<td align="center" width="100"><a href="javascript:open_trims_dtls('<? echo $po_id;?>','<? echo $tot_po_qnty; ?>','<? echo $ratio; ?>','Trims Info','trims_popup')">View</a></td>



								<td align="right" width="80"><a href="##" onClick="generate_popup('sew_output_inside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Sew Output Inside Details')"><? echo number_format($sew_recv_qnty_in,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('sew_output_outside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Sew Output Outside Details')"><? echo number_format($sew_recv_qnty_out,2); ?></a></td>



								<td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>

								<td align="right" width="80"><a href="##" onClick="generate_popup('wash_input_inside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Wash Intput Inside Details')"><? echo number_format($wash_recv_qnty_in,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('wash_input_outside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Wash Intput Outside Details')"><? echo number_format($wash_recv_qnty_out,2); ?></a></td>




								<td align="right" width="80"><? echo number_format($total_wash_recv); ?></td>
								<td align="right" width="80"><? echo number_format($wash_balance_qnty); ?></td>


								<td align="right" width="80"><a href="##" onClick="generate_popup('finish_input_inside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Finish Intput Outside Details')"><? echo number_format($gmt_finish_in,2); ?></a></td>
								<td align="right" width="80"><a href="##" onClick="generate_popup('finish_input_outside_popup',<? echo $cbo_company_name;?>,'<? echo $val['job_no'];?>','<? echo $po_id; ?>','Finish Intput Outside Details')"><? echo number_format($gmt_finish_out,2); ?></a></td>

								<td align="right" width="80"><? echo number_format($total_gmts_finish_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_balance_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($reject_button); ?></td>


                                <td align="right" width="80"><? echo number_format($current_ex_fact_qnty); ?></td>
								<td align="right" width="80">
								<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($exfactory_qnty); ?></a>
								<? //echo number_format($exfactory_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
								<td align="right" width="80"><? echo number_format($short_excess_exFactoryQty); ?></td>

                                <td align="right" width="80"><? $cut_to_ship=$cuttingQty-$exfactory_qnty; echo number_format($cut_to_ship,0); ?></td>
                                <td align="right" width="80"><? $cut_to_ship_per=$exfactory_qnty/$cuttingQty*100;  if($cuttingQty) echo number_format($cut_to_ship_per,0);else echo 0; ?></td>
                                <td align="right" width="80"><? $order_to_ship=$tot_po_qnty-$exfactory_qnty;echo number_format($order_to_ship); ?></td>
                                <td align="right" width=""><?  $order_to_ship_per=$exfactory_qnty/$tot_po_qnty*100;if($exfactory_qnty) echo number_format($order_to_ship_per);else echo 0; ?></td>
							</tr>
							<?
							$tot_order_qty+=$tot_po_qnty;
							$tot_order_value+=$val["po_total_price"];
							$tot_plan_cut_qty+=$plan_cut_qty;
							$tot_cut_to_ship+=$cut_to_ship;
							$tot_order_to_ship+=$order_to_ship;
							$tot_current_ex_fact_qnty+=$current_ex_fact_qnty;

							$tot_yarn_req_qty+=$yarn_required;
							$tot_yarn_issIn_qty+=$yarn_issue_inside;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;
							$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
							$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
							$tot_yarn_issue_qty+=$total_issued;
							$tot_yarn_undOvr_qty+=$under_over_issued;

							$tot_grey_req_qty+=$grey_fabric_req_qnty;
							$tot_grey_in_qty+=$knit_qnty_in;
							$tot_grey_out_qty+=$knit_qnty_out;
							$tot_grey_rec_qty+=$knit_gray_rec;
							$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
							$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
							$tot_grey_qty+=$total_knitting;
							$tot_grey_prLoss_qty+=$process_loss;
							$tot_grey_undOver_qty+=$under_over_prod;
							$tot_grey_issDye_qty+=$issuedToDyeQnty;
							$tot_grey_lftOver_qty+=$left_over;

							$tot_fin_req_qty+=$finish_fabric_req_qnty;
							$tot_fin_in_qty+=$finish_qnty_in;
							$tot_fin_out_qty+=$finish_qnty_out;
							$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
							$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
							$tot_fin_qty+=$total_finishing;
							$tot_fin_prLoss_qty+=$process_loss_finishing;
							$tot_fin_undOver_qty+=$under_over_finish_prod;
							$tot_fin_issCut_qty+=$issuedToCutQnty;
							$tot_fin_lftOver_qty+=$finish_left_over;

							$tot_wovenReqQty+=$wovenReqQty;
							$tot_wovenRecQty+=$wovenRecQty;
							$tot_wovenRecBalQty+=$wovenFabRecBal;
							$tot_wovenIssueQty+=$wovenIssueQty;
							$tot_wovenIssueBalQty+=$wovenFabIssueBal;

							$tot_gmt_qty+=$plan_cut_qty;
							$tot_cutting_qty+=$cuttingQty;
							$tot_printIssIn_qty+=$print_issue_qnty_in;
							$tot_printIssOut_qty+=$print_issue_qnty_out;
							$tot_printIssue_qty+=$total_print_issued;
							$tot_printRcvIn_qty+=$print_recv_qnty_in;
							$tot_printRcvOut_qty+=$print_recv_qnty_out;
							$tot_printRcv_qty+=$total_print_recv;
							$tot_printRjt_qty+=$print_reject_qnty;

							$tot_sewInInput_qty+=$sew_input_qnty_in;
							$tot_sewInOutput_qty+=$sew_input_qnty_out;
							$tot_sewIn_qty+=$total_sew_input;
							$tot_sewInBal_qty+=$sew_input_balance_qnty;

							$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
							$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
							$tot_sewRcv_qty+=$total_sew_recv;
							$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
							$tot_sewRcvRjt_qty+=$sew_reject_qnty;

							$tot_washRcvIn_qty+=$wash_recv_qnty_in;
							$tot_washRcvOut_qty+=$wash_recv_qnty_out;
							$tot_washRcv_qty+=$total_wash_recv;
							$tot_washRcvBal_qty+=$wash_balance_qnty;

							$tot_gmtFinIn_qty+=$gmt_finish_in;
							$tot_gmtFinOut_qty+=$gmt_finish_out;
							$tot_gmtFin_qty+=$total_gmts_finish_qnty;
							$tot_gmtFinBal_qty+=$finish_balance_qnty;
							$tot_gmtFinRjt_qty+=$finish_reject_qnty;
							$tot_gmtEx_qty+=$exfactory_qnty;
							$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

							$tot_shortExcess_exFactory_qty+=$short_excess_exFactoryQty;
							$tot_prLoss_qty+=$process_loss;
							$tot_prLossDye_qty+=$process_loss_dyeing;
							$tot_prLossCut_qty+=$cutting_process_loss;
							$tot_process_loss_yern_per += $process_loss_yern_per;
							$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
							$tot_process_loss_cutting_per += $process_loss_cutting_per;
							$i++;
						}
					}
					else
					{
						foreach($result_job_wise as $po_id_job)
						{
							$po_id_arr=array_unique(explode(",",substr($po_id_job,0,-1)));
							$tot_po_qnty=$yarn_required=$tot_exfactory_qnty=$grey_fabric_req_qnty=$finish_fabric_req_qnty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$transfer_in_qnty_knit=$transfer_in_qnty_finish=$transfer_out_qnty_finish=$knit_qnty_in=$issuedToDyeQnty=$issuedToCutQnty=$finish_qnty_in=$finish_qnty_out=$finish_reject_qnty=$print_issue_qnty_in=$print_issue_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_recv_qnty_in=$sew_recv_qnty_out=$sew_reject_qnty=$wash_recv_qnty_in=$wash_recv_qnty_out=$trims_recv=$trims_issue=$emb_reject_qnty=$gmt_finish_in=$gmt_finish_out=$gmt_finish_reject_qnty=$total_reject_qnty=$tot_process_loss_yern_per=$tot_process_loss_dyeing_per=0;
							foreach($po_id_arr as $po_id)
							{
								$tot_po_qnty +=$result_data_arr[$po_id]["po_qnty"];
								$tot_exfactory_qnty +=$result_data_arr[$po_id]["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
								$yarn_required+=$yarn_qty_arr[$po_id];
								$grey_fabric_req_qnty +=$booking_req_arr[$po_id]['gray'];;
								$finish_fabric_req_qnty +=$booking_req_arr[$po_id]['fin'];

								$yarn_issue_inside +=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
								$yarn_issue_outside +=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
								$transfer_in_qnty_yarn +=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
								$transfer_out_qnty_yarn +=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
								$transfer_in_qnty_knit +=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
								$transfer_out_qnty_knit +=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];
								$transfer_in_qnty_finish +=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
								$transfer_out_qnty_finish +=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];

								$total_issued =$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
								$under_over_issued =$grey_fabric_req_qnty-$total_issued;

								$knit_qnty_in +=$kniting_prod_arr[$po_id]["knit_qnty_in"];
								$knit_qnty_out +=$kniting_prod_arr[$po_id]["knit_qnty_out"];
								$total_knitting=$knit_qnty_in+$knit_qnty_out+$transfer_in_qnty_knit-$transfer_out_qnty_knit;
								$process_loss=($knit_qnty_in+$knit_qnty_out)-$total_issued;
								$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
								$issuedToDyeQnty +=$grey_cut_issue_arr[$po_id]["grey_issue_qnty"];
								$left_over=$total_knitting-$issuedToDyeQnty;

								$issuedToCutQnty +=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];

								$finish_qnty_in +=$finish_prod_arr[$po_id]["finish_qnty_in"];
								$finish_qnty_out +=$finish_prod_arr[$po_id]["finish_qnty_out"];
								$total_finish_qnty=$finish_qnty_in+$finish_qnty_out;
								$finish_balance_qnty=$tot_po_qnty-$total_finish_qnty;
								$finish_reject_qnty +=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
								$left_over_finish_gmts=$total_finish_qnty-$tot_exfactory_qnty;

								$total_finishing=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
								$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;
								$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
								$finish_left_over=$total_finishing-$issuedToCutQnty;

								$print_issue_qnty_in +=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
								$print_issue_qnty_out +=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
								$print_recv_qnty_in +=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
								$print_recv_qnty_out +=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
								$print_reject_qnty +=$garment_prod_data_arr[$po_id]['print_reject_qnty'];

								$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
								$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;

								$sew_input_qnty_in +=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
								$sew_input_qnty_out +=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
								$total_sew_issued=$sew_input_qnty_in+$sew_input_qnty_out;
								$sew_balance_qnty=$tot_po_qnty-$total_sew_issued;

								$sew_recv_qnty_in +=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
								$sew_recv_qnty_out +=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
								$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;

								$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;

								$sew_reject_qnty +=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];

								$wash_recv_qnty_in +=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
								$wash_recv_qnty_out +=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
								$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
								$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

								$gmt_finish_in+=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
								$gmt_finish_out+=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
								$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
								$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
								$gmt_finish_reject_qnty+=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
								$left_over_finish_gmts=$total_gmts_finish_qnty-$tot_exfactory_qnty;

								$trims_recv+=$trims_array[$po_id]['recv'];
								$trims_issue+=$trims_array[$po_id]['iss'];
								$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

								$emb_reject_qnty +=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
								$cutting_reject_qnty +=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

								$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
								$tot_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$gmt_finish_reject_qnty+$iron_rej_qnty+$emb_reject_qnty;
								$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($tot_reject_qnty,2).'</a></p>';
							}
							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="40"><? echo $i; ?></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$result_data_arr[$po_id]["buyer_name"]]; ?></div></td>
								<td width="50" align="center"><? echo $result_data_arr[$po_id]["job_year"]; ?></td>
								<td width="50" align="center"><? echo $result_data_arr[$po_id]["job_no_prefix_num"]; ?></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $result_data_arr[$po_id]["style_ref_no"]; ?></div></td>
								<td width="80" align="right" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty,0); ?></td>

								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_inside,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_outside,2);?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_issued,2); ?></td>

								<td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToDyeQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToCutQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_left_over,2); ?></td>

								<td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($sew_input_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_issued); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_qnty); ?></td>

								<td width="100" align="center">View</td>

								<td align="right" width="80"><? echo number_format($sew_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>

								<td width="80" align="right"><? echo number_format($wash_recv_qnty_in); ?></td>
								<td width="80" align="right"><? echo number_format($wash_recv_qnty_out); ?></td>
								<td width="80" align="right"><? echo number_format($total_wash_recv); ?></td>
								<td width="80" align="right"><? echo number_format($wash_balance_qnty); ?></td>

								<td width="80" align="right"><? echo number_format($gmt_finish_in); ?></td>
								<td width="80" align="right"><? echo number_format($gmt_finish_out); ?></td>
								<td width="80" align="right"><? echo number_format($total_gmts_finish_qnty); ?></td>
								<td width="80" align="right"><? echo number_format($finish_balance_qnty); ?></td>
								<td width="80" align="right"><? echo number_format($gmt_finish_reject_qnty); ?></td>
                                 <td align="right" width="80"><? echo $reject_button; ?></td>

								<td align="right" width="80">
								<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $result_data_arr[$po_id]["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($tot_exfactory_qnty); ?></a>
								<? //echo number_format($tot_exfactory_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>

								<td align="right" width="80"><? echo number_format($left_over); ?></td>
								<td align="right" width="80"><? echo number_format($finish_left_over); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
								<td align="right" width="80"><? echo number_format($tot_trims_left_over_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($emb_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($process_loss); ?></td>
								<td align="right" width="80"><?
									if($total_issued!=0){
										$process_loss_yern_per = ($process_loss*100)/$total_issued;
									}
									else{
										$process_loss_yern_per = 0;
									}
								 	echo number_format($process_loss_yern_per);
								 ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_dyeing); ?></td>
								<td align="right" width="80"><?
									if($issuedToDyeQnty != 0){
										$process_loss_dyeing_per = ($issuedToDyeQnty-$total_finishing)*100/$issuedToDyeQnty;
									}
									else{
										$process_loss_dyeing_per = 0;
									}
									echo number_format($process_loss_dyeing_per);
								?></td>
								<td align="right" width="80"><? echo number_format($process_loss_cutting); ?></td>
								<td align="right"><?
									if($tot_po_qnty!=0){
										$process_loss_cutting_per = ($cuttingQty-$tot_po_qnty)*100/$tot_po_qnty;
									}
									else{
										$process_loss_cutting_per = 0;
									}
									echo number_format($process_loss_cutting_per);
								?></td>
							</tr>
                            <?
							$tot_order_qty+=$tot_po_qnty;
							$tot_yarn_req_qty+=$yarn_required;
							$tot_yarn_issIn_qty+=$yarn_issue_inside;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;
							$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
							$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
							$tot_yarn_issue_qty+=$total_issued;
							$tot_yarn_undOvr_qty+=$under_over_issued;

							$tot_grey_req_qty+=$grey_fabric_req_qnty;
							$tot_grey_in_qty+=$knit_qnty_in;
							$tot_grey_out_qty+=$knit_qnty_out;
							$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
							$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
							$tot_grey_qty+=$total_knitting;
							$tot_grey_prLoss_qty+=$process_loss;
							$tot_grey_undOver_qty+=$under_over_prod;
							$tot_grey_issDye_qty+=$issuedToDyeQnty;
							$tot_grey_lftOver_qty+=$left_over;

							$tot_fin_req_qty+=$finish_fabric_req_qnty;
							$tot_fin_in_qty+=$finish_qnty_in;
							$tot_fin_out_qty+=$finish_qnty_out;
							$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
							$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
							$tot_fin_qty+=$total_finishing;
							$tot_fin_prLoss_qty+=$process_loss_finishing;
							$tot_fin_undOver_qty+=$under_over_finish_prod;
							$tot_fin_issCut_qty+=$issuedToCutQnty;
							$tot_fin_lftOver_qty+=$finish_left_over;

							$tot_gmt_qty+=$tot_po_qnty;
							$tot_printIssIn_qty+=$print_issue_qnty_in;
							$tot_printIssOut_qty+=$print_issue_qnty_out;
							$tot_printIssue_qty+=$total_print_issued;
							$tot_printRcvIn_qty+=$print_recv_qnty_in;
							$tot_printRcvOut_qty+=$print_recv_qnty_out;
							$tot_printRcv_qty+=$total_print_recv;
							$tot_printRjt_qty+=$print_reject_qnty;

							$tot_sewInInput_qty+=$sew_input_qnty_in;
							$tot_sewInOutput_qty+=$sew_input_qnty_out;
							$tot_sewIn_qty+=$total_sew_issued;
							$tot_sewInBal_qty+=$sew_balance_qnty;

							$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
							$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
							$tot_sewRcv_qty+=$total_sew_recv;
							$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
							$tot_sewRcvRjt_qty+=$sew_reject_qnty;

							$tot_washRcvIn_qty+=$wash_recv_qnty_in;
							$tot_washRcvOut_qty+=$wash_recv_qnty_out;
							$tot_washRcv_qty+=$total_wash_recv;
							$tot_washRcvBal_qty+=$wash_balance_qnty;

							$tot_gmtFinIn_qty+=$gmt_finish_in;
							$tot_gmtFinOut_qty+=$gmt_finish_out;
							$tot_gmtFin_qty+=$total_gmts_finish_qnty;
							$tot_gmtFinBal_qty+=$finish_balance_qnty;
							$tot_gmtFinRjt_qty+=$gmt_finish_reject_qnty;
							$tot_gmtEx_qty+=$tot_exfactory_qnty;
							$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

							$tot_leftOver_qty+=$left_over;
							$tot_leftOverFin_qty+=$finish_left_over;
							$tot_leftOverGmtFin_qty+=$left_over_finish_gmts;
							$tot_leftOverTrm_qty+=$tot_trims_left_over_qnty;

							$tot_rjtPrint_qty+=$print_reject_qnty;
							$tot_rjtEmb_qty+=$emb_reject_qnty;
							$tot_rjtSew_qty+=$sew_reject_qnty;
							$tot_rjtFin_qty+=$finish_reject_qnty;

							$tot_prLoss_qty+=$process_loss;
							$tot_prLossFin_qty+=$process_loss_finishing;
							$tot_prLossCut_qty+=$cutting_process_loss;
							$total_reject_qnty+=$tot_reject_qnty;

							$tot_process_loss_yern_per += $process_loss_yern_per;
							$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
							$tot_process_loss_cutting_per += $process_loss_cutting_per;
							$i++;
						}
					}
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="50">&nbsp;</td>

                    <td width="100">Total :</td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="100">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="" bgcolor="#FFFFCC"><? //echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_order_val" bgcolor="#FFFFCC"><? echo number_format($tot_order_value); ?></td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="70" align="right" id=""><? //echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
					<td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
					<td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issIn_qty"><? echo number_format($tot_yarn_issIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issOut_qty"><? echo number_format($tot_yarn_issOut_qty,2); ?></td>

                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_undOvr_qty"><? echo number_format($tot_yarn_undOvr_qty,2); ?></td>

                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($tot_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_in_qty"><? echo number_format($tot_grey_in_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_out_qty"><? echo number_format($tot_grey_out_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_trnsIn_qty"><? echo number_format($tot_grey_trnsIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_transOut_qty"><? echo number_format($tot_grey_transOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_qty"><? echo number_format($tot_grey_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_rec_qty"><? echo number_format($tot_grey_rec_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_prLoss_qty"><? echo number_format($tot_grey_prLoss_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_undOver_qty"><? echo number_format($tot_grey_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty"><? echo number_format($tot_grey_issDye_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($tot_grey_lftOver_qty,2); ?></td>

                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_in_qty"><? echo number_format($tot_fin_in_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_out_qty"><? echo number_format($tot_fin_out_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_transIn_qty"><? echo number_format($tot_fin_transIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_transOut_qty"><? echo number_format($tot_fin_transOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_fin_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_prLoss_qty"><? echo number_format($tot_fin_prLoss_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($tot_fin_issCut_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_fin_lftOver_qty,2); ?></td>


                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_gmt_qty,0); ?></td>
                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cutting_qty); ?></td>
                    <td width="80" align="right" id="td_printIssIn_qty"><? echo number_format($tot_printIssIn_qty); ?></td>
                    <td width="80" align="right" id="td_printIssOut_qty"><? echo number_format($tot_printIssOut_qty); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty"><? echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_printRcvIn_qty"><? echo number_format($tot_printRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_printRcvOut_qty"><? echo number_format($tot_printRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_printRcv_qty"><? echo number_format($tot_printRcv_qty); ?></td>
                    <td width="80" align="right" id="td_printRjt_qty"><? echo number_format($tot_printRjt_qty); ?></td>

                    <td width="80" align="right" id="td_sewInInput_qty"><? echo number_format($tot_sewInInput_qty); ?></td>
                    <td width="80" align="right" id="td_sewInOutput_qty"><? echo number_format($tot_sewInOutput_qty); ?></td>
                    <td width="80" align="right" id="td_sewIn_qty"><? echo number_format($tot_sewIn_qty); ?></td>
                    <td width="80" align="right" id="td_sewInBal_qty"><? echo number_format($tot_sewInBal_qty); ?></td>

                    <td width="100">&nbsp;</td>

                    <td width="80" align="right" id="td_sewRcvIn_qty"><? echo number_format($tot_sewRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvOut_qty"><? echo number_format($tot_sewRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcv_qty"><? echo number_format($tot_sewRcv_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvBal_qty"><? echo number_format($tot_sewRcvBal_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvRjt_qty"><? echo number_format($tot_sewRcvRjt_qty); ?></td>

                    <td width="80" align="right" id="td_washRcvIn_qty"><? echo number_format($tot_washRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvOut_qty"><? echo number_format($tot_washRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_washRcv_qty"><? echo number_format($tot_washRcv_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvBal_qty"><? echo number_format($tot_washRcvBal_qty); ?></td>

                    <td width="80" align="right" id="td_gmtFinIn_qty"><? echo number_format($tot_gmtFinIn_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinOut_qty"><? echo number_format($tot_gmtFinOut_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFin_qty"><? echo number_format($tot_gmtFin_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinBal_qty"><? echo number_format($tot_gmtFinBal_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinRjt_qty"><? echo number_format($tot_gmtFinRjt_qty); ?></td>
                   <td width="80" align="right" id="td_gmtrej_qty"><? echo number_format($total_reject_qnty); ?></td>
                   <td width="80" align="right" id="td_current_ex_qty"><? echo number_format($tot_current_ex_fact_qnty); ?></td>
                    <td width="80" align="right" id="td_gmtEx_qty"><? echo number_format($tot_gmtEx_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinLeftOver_qty"><? echo number_format($tot_gmtFinLeftOver_qty); ?></td>

                    <td width="80" align="right" id="td_shortExcess_exFactory_qty"><? echo number_format($tot_shortExcess_exFactory_qty); ?></td>

                     <td width="80" align="right" id="td_qty_cut_to_ship"><? echo number_format($tot_cut_to_ship); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_cutting_per); ?></td>
                    <td width="80" align="right" id="td_qty_order_to_ship"><? echo number_format($tot_order_to_ship); ?></td>
                    <td width="" align="right" id=""><? //echo number_format($tot_process_loss_cutting_per); ?></td>
                </tr>
           </table>
        </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$cbo_search_type**$type";
    exit();
}


if($action=="order_report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$ref_no=str_replace("'","",$txt_ref_no);
	$file_no=str_replace("'","",$txt_file_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	//$txt_ex_date_form=str_replace("'","",$txt_ex_date_form);
	//$txt_ex_date_to=str_replace("'","",$txt_ex_date_to);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$type=str_replace("'","",$type);

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	/*$ship_date_cond="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	$ex_fact_date_cond="";
	if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	{
		$ex_fact_date_cond="and c.ex_factory_date between '$txt_ex_date_form' and '$txt_ex_date_to'  AND c.is_deleted = 0 AND c.status_active = 1";
	}*/
	$ship_date_cond="";
	if($cbo_date_category==1)
	{

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	}
	else if($cbo_date_category==2)
	{
		//$ex_fact_date_cond="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and c.ex_factory_date between '$txt_date_from' and '$txt_date_to' AND c.is_deleted = 0 AND c.status_active = 1";
		}
	}
	/*else if($cbo_date_category==3) //Ref Closing date
	{

		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and d.closing_date between '$txt_date_from' and '$txt_date_to'";
		}
	}*/


	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
	$order_cond="";
	if(trim($ref_no)!="") $ref_no_cond="and b.grouping in('$ref_no')";
	if(trim($file_no)!="") $ref_no_cond="and b.file_no in('$file_no')";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	//echo $txt_order_id;die;
	if($txt_order_id!="")
	{
		$order_cond="and b.id in($txt_order_id)";
	}
	else
	{
		if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'"; else $order_cond="";
	}
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	//if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	if($cbo_date_category==2)
	{
		$sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
		where a.job_no=b.job_no_mst and  b.id=c.po_break_down_id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else
	{
		 $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1
		where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	//echo  $sql_po;die;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$result_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["ref_no"]=$row[csf("grouping")];
		$result_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("po_id")]]["po_qnty"]=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")];
		$result_data_arr[$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
		$JobArr[]="'".$row[csf('job_no')]."'";
		$job_no=$row[csf('job_no')];
	}
	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	//$JobNoArr=implode(",",$JobArr);
	//$yarn= new yarn($JobArr,'job');
	//$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	//print_r($yarn_qty_arr);
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(!empty($JobArr)){
	 $condition->po_id_in("$all_po_id");
	}
	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	//$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
	$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();


	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
	$po_cond_for_in=" and (";
	$po_cond_for_in2=" and (";
	$po_cond_for_in3=" and (";

	$poIdsArr=array_chunk(explode(",",$poIds),999);
	foreach($poIdsArr as $ids)
	{
	$ids=implode(",",$ids);
	$po_cond_for_in.=" b.po_break_down_id in($ids) or";
	$po_cond_for_in2.=" a.po_breakdown_id in($ids) or";
	$po_cond_for_in3.=" b.order_id in($ids) or";

	}
	$po_cond_for_in=chop($po_cond_for_in,'or ');
	$po_cond_for_in.=")";
	$po_cond_for_in2=chop($po_cond_for_in2,'or ');
	$po_cond_for_in2.=")";
	$po_cond_for_in3=chop($po_cond_for_in3,'or ');
	$po_cond_for_in3.=")";

	}
	else
	{
	$po_cond_for_in=" and b.po_break_down_id in($poIds)";
	$po_cond_for_in2=" and a.po_breakdown_id  in($poIds)";
	$po_cond_for_in3=" and b.order_id in($poIds)";

	}
			if($db_type==2)
			{
				//$col_grp="listagg(CAST(a.booking_no as VARCHAR(4000)),',') within group (order by a.booking_no) as booking_no";
				$col_grp="rtrim(xmlagg(xmlelement(e,a.booking_no,',').extract('//text()') order by a.booking_no).GetClobVal(),',') as booking_no";
			}
			else
			{
				$col_grp="group_concat(a.booking_no) as booking_no";
			}
	$booking_req_arr=array();
	$sql_wo=sql_select("select $col_grp,a.booking_type,b.po_break_down_id,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	sum(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	sum(b.fin_fab_qnty) as fin_fab_qnty,
	sum(CASE WHEN a.item_category=12 and a.process=35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty
	from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in group by a.booking_type,b.po_break_down_id");

	//finish_prod_arr
	foreach ($sql_wo as $brow)
	{
		if($brow[csf("grey_req_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['gray']=$brow[csf("grey_req_qnty")];
		}
		if($brow[csf("woven_req_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['woven']=$brow[csf("woven_req_qnty")];
		}
		if($brow[csf("fin_fab_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['fin']=$brow[csf("fin_fab_qnty")];
		}
		if($brow[csf("aop_wo_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['aop_wo_qnty']=$brow[csf("aop_wo_qnty")];
		}
		if($brow[csf("booking_type")]==1)
		{
			if($db_type==2 && $brow[csf("booking_no")]!=""){
				$brow[csf("booking_no")] = $brow[csf("booking_no")]->load();
			}
			$booking_req_arr[$brow[csf("po_break_down_id")]]['booking_no']=$brow[csf("booking_no")];
		}
	}
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('return_qnty')];
		}

	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$dataArrayYarnReq=array();
	$yarn_sql="select job_no, sum(avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarnReq[$yarnRow[csf('job_no')]]=$yarnRow[csf('qnty')];
	}

	$reqDataArray=sql_select("select  a.po_break_down_id, sum((b.requirment/b.pcs)*a.plan_cut_qnty) as grey_req, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1  $po_cond_for_in group by a.po_break_down_id");

	$grey_finish_require_arr=array();
	foreach($reqDataArray as $row)
	{
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["grey_req"]=$row[csf("grey_req")];
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["finish_req"]=$row[csf("finish_req")];
	}

	$yarnDataArr=sql_select("select a.po_breakdown_id,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_issue_master c
						where a.trans_id=b.id and b.mst_id=c.id   and b.item_category=1 and c.issue_purpose in(1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2
						group by a.po_breakdown_id");
	$yarn_issue_arr=array();
	foreach($yarnDataArr as $row)
	{
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_in"]=$row[csf("issue_qnty_in")];
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_out"]=$row[csf("issue_qnty_out")];
	}
	//order_wise_pro_details
	$yarnReturnDataArr=sql_select("select a.po_breakdown_id, sum(CASE WHEN a.ENTRY_FORM in(2,22,58) and c.ENTRY_FORM in(2,22,58)  THEN b.cons_reject_qnty ELSE 0 END) as cons_reject_qnty,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
				from order_wise_pro_details a, inv_transaction b, inv_receive_master c
				where a.trans_id=b.id and b.mst_id=c.id and b.item_category in (1,13)
				and b.transaction_type IN (1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2
				group by a.po_breakdown_id");
				


	$yarn_issue_rtn_arr=array();
	foreach($yarnReturnDataArr as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["cons_reject_qnty"]=$row[csf("cons_reject_qnty")];
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
	}

	$knit_finReturnDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN a.entry_form=52 and c.entry_form=52  THEN a.quantity ELSE 0 END) AS return_qnty

				from order_wise_pro_details a, inv_transaction b, inv_receive_master c
				where a.trans_id=b.id and b.mst_id=c.id  and  a.entry_form=52 and c.entry_form=52 and b.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2
				group by a.po_breakdown_id");



	$knit_fin_rtn_arr=array();
	foreach($knit_finReturnDataArr as $row)
	{
		$knit_fin_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty"]=$row[csf("return_qnty")];

	}
	unset($knit_finReturnDataArr);


	$dataArrayTrans=sql_select("select po_breakdown_id,
								sum(CASE WHEN a.entry_form ='11' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN a.entry_form ='11' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN a.entry_form ='13' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN a.entry_form ='13' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN a.entry_form in(14,15) and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_finish,
								sum(CASE WHEN a.entry_form in(14,15) and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_finish,
								sum(CASE WHEN a.entry_form ='14' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_rec_knit2,
								sum(CASE WHEN a.entry_form ='14' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_rec_knit2,
								sum(CASE WHEN a.entry_form in(82,183,110) and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_rec_knit,
								sum(CASE WHEN a.entry_form in(82,183,110) and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_rec_knit

							from order_wise_pro_details a where a.status_active=1 and a.is_deleted=0 and a.entry_form in(11,13,15,14,82,183,110)
							 $po_cond_for_in2 group by a.po_breakdown_id");



	$transfer_data_arr=array();
	foreach($dataArrayTrans as $row)
	{
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_yarn"]=$row[csf("transfer_in_qnty_yarn")];//transfer_in_qnty_rec_knit
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_yarn"]=$row[csf("transfer_out_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_knit"]=$row[csf("transfer_in_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_knit"]=$row[csf("transfer_out_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_finish"]=$row[csf("transfer_in_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_finish"]=$row[csf("transfer_out_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_rec_knit"]=$row[csf("transfer_in_qnty_rec_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_rec_knit"]=$row[csf("transfer_out_qnty_rec_knit")];
	}

	// decision pending	dyed yearn receive
	//$greyYarnIssueQnty=return_library_array("select c.po_breakdown_id, sum(c.quantity) as issue_qnty from inv_transaction a, inv_issue_master b,  order_wise_pro_details c where a.mst_id=b.id and a.id=c.trans_id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and c.po_breakdown_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2 group by c.po_breakdown_id","po_breakdown_id","issue_qnty");


	//$dyedYarnRecvQnty=return_field_value("sum(a.cons_quantity) as recv_qnty","inv_transaction a, inv_receive_master b","a.mst_id=b.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1","recv_qnty");

	$prodKnitDataArr=sql_select("select a.po_breakdown_id,b.reject_fabric_receive,
				sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
				sum(CASE WHEN a.entry_form in(58,22) and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_inside,
				sum(CASE WHEN a.entry_form in(58,22) and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_outside
				from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category=13 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 group by a.po_breakdown_id,b.reject_fabric_receive");// and c.receive_basis<>9

	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_in"]=$row[csf("knit_qnty_in")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_out"]=$row[csf("knit_qnty_out")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec_inside"]=$row[csf("knit_qnty_rec_inside")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["reject_fabric_receive"]=$row[csf("reject_fabric_receive")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec_outside"]=$row[csf("knit_qnty_rec_outside")];
	}

	$prodFinDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
				sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_in_rec_gmt,
				sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_out_rec_gmt,
				sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec
				from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $po_cond_for_in2 group by a.po_breakdown_id");



	$finish_prod_arr=array();
	foreach($prodFinDataArr as $row)
	{
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in"]=$row[csf("finish_qnty_in")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out"]=$row[csf("finish_qnty_out")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in_rec_gmt"]=$row[csf("finish_qnty_in_rec_gmt")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out_rec_gmt"]=$row[csf("finish_qnty_out_rec_gmt")];

		$finish_prod_arr[$row[csf("po_breakdown_id")]]["woven_rec"]=$row[csf("woven_rec")];
	}//c.knit_dye_source
	$issueData=sql_select("select po_breakdown_id,
							sum(CASE WHEN a.entry_form=16 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_in_qnty,
							sum(CASE WHEN a.entry_form=16  and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_out_qnty,
							sum(CASE WHEN a.entry_form=61 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_out,
							sum(CASE WHEN a.entry_form=61 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_in,
							sum(CASE WHEN a.entry_form=18 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty,
							sum(CASE WHEN a.entry_form=71 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
							sum(CASE WHEN a.entry_form=19 THEN a.quantity ELSE 0 END) AS woven_issue
							from order_wise_pro_details a,inv_grey_fabric_issue_dtls b,inv_issue_master
	c where a.dtls_id=b.id and b.mst_id=c.id  and a.entry_form in(16,18,19,61,71) and a.status_active=1 and a.is_deleted=0 $po_cond_for_in2 group by a.po_breakdown_id");


	$grey_cut_issue_arr=array();
	foreach($issueData as $row)
	{
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty_in"]=$row[csf("grey_issue_in_qnty")]+$row[csf("grey_issue_qnty_roll_wise_in")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty_out"]=$row[csf("grey_issue_out_qnty")]+$row[csf("grey_issue_qnty_roll_wise_out")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["issue_to_cut_qnty"]=$row[csf("issue_to_cut_qnty")]+$row[csf("issue_to_cut_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["woven_issue"]=$row[csf("woven_issue")];
	}
	$trimsDataArr=sql_select("select a.po_breakdown_id,
							sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
							sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
							from order_wise_pro_details a, product_details_master b where a.prod_id=b.id  and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 group by a.po_breakdown_id");
	foreach($trimsDataArr as $row)
	{
		$trims_array[$row[csf('po_breakdown_id')]]['recv']=$row[csf('recv_qnty')];
		$trims_array[$row[csf('po_breakdown_id')]]['iss']=$row[csf('issue_qnty')];
	}
	$issuetoprocessDataArr=sql_select("select b.order_id,

							sum(CASE WHEN a.entry_form=91 THEN b.batch_issue_qty ELSE 0 END) AS batch_issue_qty,
							sum(CASE WHEN a.entry_form=92 THEN b.batch_issue_qty ELSE 0 END) AS aop_recv_qnty
							from inv_receive_mas_batchroll a,pro_grey_batch_dtls b where  a.id=b.mst_id  and  a.entry_form in(91,92)  and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in3 group by b.order_id");


	foreach($issuetoprocessDataArr as $row)
	{
		$aop_delivery_array[$row[csf('order_id')]]['batch_issue_qty']=$row[csf('batch_issue_qty')];
		$aop_delivery_array[$row[csf('order_id')]]['aop_recv_qnty']=$row[csf('aop_recv_qnty')];

	}

	$sql_consumtiont_qty=sql_select("select b.po_break_down_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0  $po_cond_for_in group by b.po_break_down_id, c.body_part_id ");
			$finish_consumtion_arr=array();
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg=0;
				$con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];///str_replace("'","",$row_sew[csf("pcs")]);
				$finish_consumtion_arr[$row_consum[csf('po_break_down_id')]]+=$con_avg;
			}

	$gmtsProdDataArr=sql_select("select  b.po_break_down_id,
					sum(CASE WHEN b.production_type=1 THEN b.production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN b.production_type=2 and b.embel_name=1 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS print_issue_qnty_in,
					sum(CASE WHEN b.production_type=2 and b.embel_name=1 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS print_issue_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS print_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS print_recv_qnty_out,
					sum(CASE WHEN b.production_type=2 and b.embel_name=2 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS emb_issue_qnty_in,
					sum(CASE WHEN b.production_type=2 and b.embel_name=2 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS emb_issue_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS emb_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS emb_recv_qnty_out,
					sum(CASE WHEN b.production_type=4 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS sew_input_qnty_in,
					sum(CASE WHEN b.production_type=4 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS sew_input_qnty_out,
					sum(CASE WHEN b.production_type=5 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS sew_recv_qnty_in,
					sum(CASE WHEN b.production_type=5 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS sew_recv_qnty_out,
					sum(CASE WHEN b.production_type=8 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS finish_qnty_in,
					sum(CASE WHEN b.production_type=8 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS finish_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=3 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS wash_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=3 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS wash_recv_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 THEN b.reject_qnty ELSE 0 END) AS print_reject_qnty,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 THEN b.reject_qnty ELSE 0 END) AS emb_reject_qnty,
					sum(CASE WHEN b.production_type=5 THEN b.reject_qnty ELSE 0 END) AS sew_reject_qnty,
					sum(CASE WHEN b.production_type=8 THEN b.reject_qnty ELSE 0 END) AS finish_reject_qnty,
					sum(CASE WHEN b.production_type=1 THEN b.reject_qnty ELSE 0 END) AS cutting_reject_qnty,
					sum(CASE WHEN b.production_type=7 THEN b.reject_qnty ELSE 0 END) AS iron_rej_qnty
					from pro_garments_production_mst b where  b.is_deleted=0 and b.status_active=1 $po_cond_for_in group by b.po_break_down_id");


	$garment_prod_data_arr=array();
	foreach($gmtsProdDataArr as $row)
	{
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_qnty']=$row[csf("cutting_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_out']=$row[csf("finish_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_in']=$row[csf("wash_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_out']=$row[csf("wash_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_reject_qnty']=$row[csf("print_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_reject_qnty']=$row[csf("emb_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_reject_qnty']=$row[csf("sew_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_reject_qnty']=$row[csf("finish_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_reject_qnty']=$row[csf("cutting_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['iron_rej_qnty']=$row[csf("iron_rej_qnty")];
	}
	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	if($cbo_search_type==1)
	{
		$tbl_width=8870;

		$ship_date_html="Shipment Date";
		$ex_fact_date_html="Ex-Fact. Date";
	}
	else
	{
		$tbl_width=6780;
		$ship_date_html="Last Shipment Date";
		$ex_fact_date_html="Last Ex-Fact. Date";
	}
	ob_start();
	?>
        <div style="width:100%">
             <table width="<? echo $tbl_width;?>">
                <tr>
                    <td align="center" width="100%" colspan="69" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
            </table>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                	<tr style="font-size:13px">
                        <th width="40" >SL</th>
                        <th width="110" >Buyer</th>
                        <th width="50" >Job Year</th>
                        <th width="50" >Job No</th>
                        <th width="100" >Style No</th>
                        <th width="110">Fb Booking No</th>
                        <th width="80" >Internal Ref. No</th>
                        <?
						if($cbo_search_type==1)
						{
							?>
                        	<th width="100" >Order No</th>
							<?
						}
						?>
                        <th width="80" >Order Qty. (Pcs)</th>
                        <?
						if($cbo_search_type==1)
						{
							?>
							<th width="70" ><? echo $ship_date_html; ?></th>
							<th width="70"><? echo $ex_fact_date_html; ?></th>
							<?
						}
						?>
                        <th width="80">Yarn Req.<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                        <th width="80">Yarn/Grey Req.
						<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Yarn Issued In</th>
                        <th width="80">Yarn Issued Rtn In</th>
                        <th width="80">Yarn Reject Qty</th>

                        <th width="80">Yarn Issued Out</th>
                        <th width="80">Yarn Issued Rtn Out</th>

                        <th width="80">Yarn Total Issued</th>
                        <th width="80">Yarn Total Issued Rtn</th>

                        <th width="80">Yarn Under or Over Issued<font style="font-size:9px; font-weight:100">(As Per PreCost)</font></th>

                        <th width="80">Yarn Under or Over Issued <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Knit. Prod Inside</th>
                        <th width="80">Knit. Prod SubCon</th>
                        <th width="80">Knit. Total Prod.</th>

                        <th width="80">Knit. Under or Over Prod.</th>
                        <th width="80">Knit. Process Loss Kg. inside</th>

                        <th width="80">Knit. Process Loss Kg. Outside</th>
                        <th width="80">Total Knit. Process Loss Kg.</th>

                        <th width="80">Knit. Receive Inside</th>
                        <th width="80">Fabric Reject Qty</th>
                        <th width="80">Knit. Receive Outside</th>

                        <th width="80">Knit. Trans. In</th>
                        <th width="80">Knit. Trans. Out</th>
                        <th width="80">Total Knit. Receive</th>
                        <th width="80">Received. Under or Over</th>



                        <th width="80">Knit. Issued To Dyeing inside</th>
                        <th width="80">Knit. Issued To Dyeing outside</th>
                        <th width="80">Total Knit. Issued To Dyeing</th>
                        <th width="80">Knit. Left Over</th>
                        <th width="80">AOP Req.<br><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">AOP Delivery</th>
                        <th width="80">AOP Received</th>
                        <th width="80">Balance (Loss/Gain)</th>

                        <th width="80">Fin Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Fin Prod. Inside</th>
                        <th width="80">Fin Prod. SubCon</th>

                        <th width="80">Total Production</th>
                        <th width="80">Fin Prod. Under or Over</th>
                        <th width="80">Fin Prod. Process Loss Kg. inside</th>
                        <th width="80">Fin Prod. Process Loss Kg. Outside</th>
                        <th width="80">Total Knit. Process Loss Kg.</th>
                        <th width="80">Fin. Receive Inside</th>
                        <th width="80">Fin. Receive Outside</th>

                        <th width="80">Fin Trans. In</th>
                        <th width="80">Fin Trans. Out</th>
                        <th width="80">Total Fin. Receive</th>

                        <th width="80">Received. Under or Over</th>
                        <th width="80">Fin Issue To Cut</th>
                        <th width="80">Fin Issued Rtn</th>

                        <th width="80">Fin Left Over</th>

                        <th width="80">Woven Fabric Req.</th>
                        <th width="80">Woven Fabric Received</th>
                        <th width="80">Woven Fab. Rec. Bal.</th>
                        <th width="80">Woven Fabric Issue</th>
                        <th width="80">Woven Fabric Issue Bal.</th>


                        <th width="80">Plan Cut Qty</th>
                        <th width="80">Cutting Qty</th>
                        <th width="80">Gmts. Print Issued In</th>
                        <th width="80">Gmts. Print Issued SubCon</th>
                        <th width="80">Gmts. Total Print Issued</th>
                        <th width="80">Gmts. Print Rec. Inside</th>
                        <th width="80">Gmts. Print Rec. SubCon</th>
                        <th width="80">Gmts. Total Rec. Print</th>

                        <th width="80">Gmts. Embry. Issued In</th>
                        <th width="80">Gmts. Embry. Issued SubCon</th>
                        <th width="80">Gmts. Total Embry. Issued</th>
                        <th width="80">Gmts. Embry. Rec. Inside</th>
                        <th width="80">Gmts. Embry. Rec. SubCon</th>
                        <th width="80">Gmts. Total Rec. Embry.</th>


                        <th width="80">Gmts. Print + Eby. Reject</th>

                        <th width="80">Sew. Input Inside</th>
                        <th width="80">Sew. Input SubCon</th>
                        <th width="80">Total Sew. Input</th>
                        <th width="80">Sew. Input Balance</th>

                        <th width="100">Accessories Status</th>

                        <th width="80">Sew. Out Inside</th>
                        <th width="80">Sew Out SubCon</th>
                        <th width="80">Total Out Sew</th>
                        <th width="80">Sew Out Balance</th>
                        <th width="80">Sew Out Reject</th>

                        <th width="80">Wash Inside</th>
                        <th width="80">Wash SubCon</th>
                        <th width="80">Total Wash</th>


                        <th width="80">Finish Inside</th>
                        <th width="80">Finish SubCon</th>
                        <th width="80">Total Finish</th>
                        <th width="80">Finish Balance</th>
                        <th width="80">Finish Reject</th>
                        <th width="80">Total Reject</th>
                        <th width="80">Ex-Factory</th>
                        <th width="80">Left Over</th>

                        <th width="80">Short Ex-Fac. Qty</th>

                        <th width="80">Process Loss Yarn</th>
                        <th width="80" title="Knit Proces Loss*100/Total Yarn Issue">Process Loss Yarn &percnt;</th>
                        <th width="80">Process Loss Dyeing</th>
                        <th width="80" title="(Knit issue to dyeing- Fin Production Total)*100/Knit issue to dyeing">Process Loss Dyeing &percnt;</th>
                        <th>Process Loss Cutting</th>
                        <th width="80" title="(Total Cutting Qty-Total Order Qty)*100/total ordr qty">Process Loss Cutting &percnt;</th>
                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">

                <?
					$i=1;$tot_grey_rec_qty=$tot_grey_req_qty=0;
					$tot_yarn_issue_ret_inside=$tot_yarn_issue_ret_outside=$tot_yarn_issue_ret=0;

					if($cbo_search_type==1)
					{
						foreach($result_data_arr as $po_id=>$val)
						{
							$ratio=$val["ratio"];
							$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
							$tot_po_qnty=$val["po_qnty"];
							$exfactory_qnty=$val["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
							$plan_cut_qty=$val["plan_cut"];
							$job_no=$val["job_no"];
							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$ratio;

							$yarn_req_job=$yarn_qty_arr[$po_id];//$dataArrayYarnReq[$job_no];
							$yarn_required=$yarn_qty_arr[$po_id];//$plan_cut_qty*($yarn_req_job/$dzn_qnty);
							$yarn_issue_inside=$yarn_issue_arr[$po_id]["issue_qnty_in"];
							$yarn_issue_outside=$yarn_issue_arr[$po_id]["issue_qnty_out"];

							$yarn_issue_ret_inside=$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
							$cons_reject_qnty=$yarn_issue_rtn_arr[$po_id]["cons_reject_qnty"];
							$yarn_issue_ret_outside=$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];

							$transfer_in_qnty_yarn=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
							$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
							$under_over_issued=$yarn_required-$total_issued;

							$grey_fabric_req_qnty=$booking_req_arr[$po_id]['gray'];
							$grey_fabric_aop_req_wo_qnty=$booking_req_arr[$po_id]['aop_wo_qnty'];//$booking_req_arr[$brow[csf("po_break_down_id")]]['aop_wo_qnty']
							//$grey_finish_require_arr[$po_id]["grey_req"];
							$aop_delivery_qty=$aop_delivery_array[$po_id]['batch_issue_qty'];
							$aop_aop_recv_qnty=$aop_delivery_array[$po_id]['aop_recv_qnty'];

							$knit_qnty_in=$kniting_prod_arr[$po_id]["knit_qnty_in"];
							$knit_qnty_out=$kniting_prod_arr[$po_id]["knit_qnty_out"];
							$knit_gray_rec_inside=$kniting_prod_arr[$po_id]["knit_qnty_rec_inside"];
							$reject_fabric_receive=$kniting_prod_arr[$po_id]["reject_fabric_receive"];
							$knit_gray_rec_outside=$kniting_prod_arr[$po_id]["knit_qnty_rec_outside"];

							$transfer_in_qnty_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];

							$transfer_in_qnty_rec_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_rec_knit"];

							$total_knitting=$knit_qnty_in+$knit_qnty_out;//+$transfer_in_qnty_knit-$transfer_out_qnty_knit;
							$process_loss=($yarn_issue_inside+$yarn_issue_outside)-($knit_qnty_in+$knit_qnty_out);
							$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
							$issuedToDyeQnty_in=$grey_cut_issue_arr[$po_id]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out=$grey_cut_issue_arr[$po_id]["grey_issue_qnty_out"];

							$tot_knit_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;

							$left_over=$tot_knit_rec_qty-($issuedToDyeQnty_in+$issuedToDyeQnty_out);


							$finish_fabric_req_qnty=$booking_req_arr[$po_id]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							$finish_qnty_in=$finish_prod_arr[$po_id]["finish_qnty_in"];
							$finish_qnty_out=$finish_prod_arr[$po_id]["finish_qnty_out"];

							$knit_fin_return_qnty=$knit_fin_rtn_arr[$po_id]["return_qnty"];

							$finish_qnty_in_rec_gmt=$finish_prod_arr[$po_id]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt=$finish_prod_arr[$po_id]["finish_qnty_out_rec_gmt"];

							$transfer_in_qnty_finish=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];

							$total_finishing=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;

							$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);

							$issuedToCutQnty=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];
							$finish_left_over=($total_finishing-$issuedToCutQnty)+$knit_fin_return_qnty;
						//knit_fin_return_qnty
							$wovenReqQty=$booking_req_arr[$po_id]['woven'];
							$wovenRecQty=$finish_prod_arr[$po_id]["woven_rec"];
							$fb_booking_no=$booking_req_arr[$po_id]['booking_no'];
							$wovenFabRecBal=$wovenReqQty-$wovenRecQty;
							$wovenIssueQty=$grey_cut_issue_arr[$po_id]["woven_issue"];
							$wovenFabIssueBal=$wovenRecQty-$wovenIssueQty;


							$cuttingQty=$garment_prod_data_arr[$po_id]['cutting_qnty'];
							if($finish_consumtion_arr[$po_id] !=0){
								$possible_cut_pcs=$issuedToCutQnty/$finish_consumtion_arr[$po_id];
							}
							else{
								$possible_cut_pcs = 0;
							}

							$cutting_process_loss=$possible_cut_pcs-$cuttingQty;

							$print_issue_qnty_in=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
							$print_issue_qnty_out=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
							$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
							$print_recv_qnty_in=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
							$print_recv_qnty_out=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
							$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;

							$emb_issue_qnty_in=$garment_prod_data_arr[$po_id]['emb_issue_qnty_in'];
							$emb_issue_qnty_out=$garment_prod_data_arr[$po_id]['emb_issue_qnty_out'];
							$emb_recv_qnty_in=$garment_prod_data_arr[$po_id]['emb_recv_qnty_in'];
							$emb_recv_qnty_out=$garment_prod_data_arr[$po_id]['emb_recv_qnty_out'];
							$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];



							$print_reject_qnty=$garment_prod_data_arr[$po_id]['print_reject_qnty']+$emb_reject_qnty;

							$sew_input_qnty_in=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
							$sew_input_qnty_out=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
							$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
							$sew_input_balance_qnty=$tot_po_qnty-$total_sew_input;

							$sew_recv_qnty_in=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
							$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;
							$sew_reject_qnty=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];
							$cutting_reject_qnty=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

							$wash_recv_qnty_in=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
							$wash_recv_qnty_out=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
							$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
							$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

							$gmt_finish_in=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
							$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
							$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
							$finish_reject_qnty=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
							$left_over_finish_gmts=$total_gmts_finish_qnty-$exfactory_qnty;

							$short_excess_exFactoryQty=$tot_po_qnty-$exfactory_qnty;

							$trims_recv=$trims_array[$po_id]['recv'];
							$trims_issue=$trims_array[$po_id]['iss'];
							$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

							$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
							$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;


							$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
							$total_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$finish_reject_qnty+$iron_rej_qnty+$print_reject_qnty;
							$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($total_reject_qnty,2).'</a></p>';
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="40"><? echo $i; ?></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$val["buyer_name"]]; ?></div></td>
								<td width="50" align="center"><p><? echo $val["job_year"]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $val["style_ref_no"]; ?></div></td>
                                <td width="110"><div style="word-wrap:break-word; width:110px"><? $fb_booking_nos=implode(",",array_unique(explode(",",$fb_booking_no))); echo $fb_booking_nos; ?></div></td>
                                <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $val["ref_no"]; ?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $val["po_number"]; ?></div></td>
								<td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty); ?></p></td>
								<td width="70"><p><? if(trim($val["pub_shipment_date"])!="" && trim($val["pub_shipment_date"])!='0000-00-00') echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</p></td>
								<td width="70"><p><? if(trim($val["ex_factory_date"])!="" && trim($val["ex_factory_date"])!='0000-00-00') echo change_date_format($val["ex_factory_date"]); ?>&nbsp;</p></td>

								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
                                <td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_inside,2); ?></td>
                                <td align="right" width="80"><? echo number_format($yarn_issue_ret_inside,2); ?></td>
                                <td align="right" width="80"><? echo number_format($cons_reject_qnty,2); ?></td>

								<td align="right" width="80"><? echo number_format($yarn_issue_outside,2);?></td>
                                 <td align="right" width="80"><? echo number_format($yarn_issue_ret_outside,2); ?></td>

								<td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
                                 <td align="right" width="80" title="Issue Ret In+Out"><? echo number_format($yarn_issue_ret_inside+$yarn_issue_ret_outside,2); ?></td>

								<td align="right" width="80"><? echo number_format($under_over_issued,2); ?></td>

								<td align="right" width="80" title="Yarn/Grey Req-Yarn Total Issued"><? $yarn_under_over_issued=$grey_fabric_req_qnty-$total_issued;
									echo number_format($yarn_under_over_issued,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_out,2); ?></td>
                                <td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
								<td align="right" width="80" title="Yarn/Grey Req as Per Booking-Total Knit">
								<? $knit_under_over_prod_qty=$grey_fabric_req_qnty-$total_knitting;echo number_format($knit_under_over_prod_qty,2); ?></td>
								<td align="right" width="80" title="Yarn Issue In-(Yarn issue Ret in+Kit Prod Inside)"><? $knit_process_lossKg_in=$yarn_issue_inside-($yarn_issue_ret_inside+$knit_qnty_in);echo number_format($knit_process_lossKg_in,2); ?></td>
                                <td align="right" width="80"  title="Yarn Issue Out-(Yarn Issue Ret Out+Kit Prod Outside)"><?
								$knit_processLossKgOut=$yarn_issue_outside-($yarn_issue_ret_outside+$knit_qnty_out);echo number_format($knit_processLossKgOut,2); ?></td>
								<td align="right" width="80" title="Total Yarn Issue-Total Kit Prod"><? $tot_knit_processLogssKg=$total_issued-$total_knitting; echo number_format($tot_knit_processLogssKg,2); ?></td>

                                <td align="right" width="80"><? echo number_format($knit_gray_rec_inside,2); ?></td>
                                <td align="right" width="80"><? echo number_format($reject_fabric_receive,2); ?></td>
                                <td align="right" width="80"><? echo number_format($knit_gray_rec_outside,2); ?></td>

                                <td align="right" width="80"><? echo number_format($transfer_in_qnty_rec_knit,2); ?></td>
                                <td align="right" width="80"><? echo number_format($transfer_out_qnty_rec_knit,2); ?></td>
                                <td align="right" width="80"><?
								echo number_format($tot_knit_rec_qty,2); ?></td>

								<td align="right" width="80" title="Yarn/Grey Req. Booking-Total Knit Recv Qty"><?
								$recv_under_over_prod=$grey_fabric_req_qnty-$tot_knit_rec_qty;echo number_format($recv_under_over_prod,2); ?></td>

								<td align="right" width="80"><? echo number_format($issuedToDyeQnty_in,2); ?></td>
                                <td align="right" width="80"><? echo number_format($issuedToDyeQnty_out,2); ?></td>
                                <td align="right" width="80"><?  $tot_issuedToDyeQnty=$issuedToDyeQnty_in+$issuedToDyeQnty_out;echo number_format($tot_issuedToDyeQnty,2); ?></td>
								<td align="right" width="80" title="Total Knit Recv-Total Knit Issued ToDyeing"><? echo number_format($left_over,2); ?></td>

                                <td align="right" width="80"><? echo number_format($grey_fabric_aop_req_wo_qnty,2); ?></td>
                                <td align="right" width="80"><? echo number_format($aop_delivery_qty,2); ?></td>
                                <td align="right" width="80"><? echo number_format($aop_aop_recv_qnty,2); ?></td>
                                <td align="right" width="80"><? $aop_balance_qty=$aop_delivery_qty-$aop_aop_recv_qnty;echo number_format($aop_balance_qty,2); ?></td>


								<td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_out,2); ?></td>

                                <td align="right" width="80"><? $tot_fin_prod_qty=$finish_qnty_in+$finish_qnty_out;echo number_format($tot_fin_prod_qty,2); ?></td>
                                <td align="right" width="80" title="Fin Fab Req-Total Production"><?
								$fin_prod_under_over_qty=$finish_fabric_req_qnty-$tot_fin_prod_qty;
								echo number_format($fin_prod_under_over_qty,2); ?></td>
                                <td align="right" width="80" title="IssueToDye Inside-Fin Prod Inside"><? $fin_fab_process_loss_inside=$issuedToDyeQnty_in-$finish_qnty_in;
								echo number_format($fin_fab_process_loss_inside,2); ?></td>
                                <td align="right" width="80" title="IssueToDye Outside-Fin Prod Outside"><? $fin_fab_process_loss_outside=$issuedToDyeQnty_out-$finish_qnty_out;
								echo number_format($fin_fab_process_loss_outside,2); ?></td>
                                <td align="right" width="80" title="Total Knit Dye IssuedToDye-Total Production"><? $tot_knit_process_loss=$tot_issuedToDyeQnty-$tot_fin_prod_qty;echo number_format($tot_knit_process_loss,2); ?></td>
                                <td align="right" width="80"><? echo number_format($finish_qnty_in_rec_gmt,2); ?></td>
                                <td align="right" width="80"><? echo number_format($finish_qnty_out_rec_gmt,2); ?></td>


                                <?
                                	$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
									// $finish_fabric_req_qnty-$total_finishing;
								?>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>

								<td align="right" title="Fin Fab Req Qty-Total Fin Recv Qty" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToCutQnty,2); ?></td>
                                <td align="right" width="80"><? echo number_format($knit_fin_return_qnty,2); ?></td>
								<td align="right" width="80" title="(Tot Fin Recv-Fin issueToCut)+Knit Fin Issue Ret"><? echo number_format($finish_left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($wovenReqQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenRecQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenFabRecBal,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenIssueQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenFabIssueBal,2); ?></td>

								<td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($tot_po_plan_qnty); ?></td>
                                <td align="right" width="80"><? echo number_format($cuttingQty); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_recv); ?></td>

                                <td align="right" width="80"><? echo number_format($emb_issue_qnty_in); ?></td>
                                <td align="right" width="80"><? echo number_format($emb_issue_qnty_out); ?></td>
                                <td align="right" width="80"><? $tot_emb_issueQty=$emb_issue_qnty_in+$emb_issue_qnty_out;
								 echo number_format($tot_emb_issueQty); ?></td>
                                <td align="right" width="80"><? echo number_format($emb_recv_qnty_in); ?></td>
                                <td align="right" width="80"><? echo number_format($emb_recv_qnty_out); ?></td>
                                <td align="right" width="80"><? $tot_emb_recvQty=$emb_recv_qnty_in+$emb_recv_qnty_out; echo number_format($tot_emb_recvQty); ?></td>

								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($sew_input_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_input); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_balance_qnty); ?></td>

								<td align="center" width="100"><a href="javascript:open_trims_dtls('<? echo $po_id;?>','<? echo $tot_po_qnty; ?>','<? echo $ratio; ?>','Trims Info','trims_popup')">View</a></td>

								<td align="right" width="80"><? echo number_format($sew_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($wash_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($wash_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_wash_recv); ?></td>


								<td align="right" width="80"><? echo number_format($gmt_finish_in); ?></td>
								<td align="right" width="80"><? echo number_format($gmt_finish_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_gmts_finish_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_balance_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>
                                <td align="right" width="80"><? echo $reject_button; ?></td>
								<td align="right" width="80">
								<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($exfactory_qnty); ?></a>
								<? //echo number_format($exfactory_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
								<td align="right" width="80"><? echo number_format($short_excess_exFactoryQty); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss); ?></td>
								<td align="right" width="80"><?
									if($total_issued!=0){
										$process_loss_yern_per = ($process_loss*100)/$total_issued;
									}
									else{
										$process_loss_yern_per = 0;
									}
								 	echo number_format($process_loss_yern_per);
								 ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_dyeing); ?></td>
								<td align="right" width="80"><?
									if($issuedToDyeQnty != 0){
										$process_loss_dyeing_per = ($issuedToDyeQnty-$total_finishing)*100/$issuedToDyeQnty;
									}
									else{
										$process_loss_dyeing_per = 0;
									}
									echo number_format($process_loss_dyeing_per);
								?></td>
								<td align="right" ><? echo number_format($cutting_process_loss); ?></td>
								<td align="right" width="80"><?
									if($tot_po_qnty!=0){
										$process_loss_cutting_per = ($cuttingQty-$tot_po_qnty)*100/$tot_po_qnty;
									}
									else{
										$process_loss_cutting_per = 0;
									}
									echo number_format($process_loss_cutting_per);
								?></td>
							</tr>
							<?
							$tot_order_qty+=$tot_po_qnty;

							$tot_yarn_req_qty+=$yarn_required;
							$tot_yarn_issIn_qty+=$yarn_issue_inside;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;

							$tot_yarn_issue_ret_inside+=$yarn_issue_ret_inside;
							$tot_cons_reject_qnty+=$cons_reject_qnty;
							$tot_yarn_issue_ret_outside+=$yarn_issue_ret_outside;
							$tot_yarn_issue_ret+=$yarn_issue_ret_inside+$yarn_issue_ret_outside;

							$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
							$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
							$tot_yarn_issue_qty+=$total_issued;
							$tot_yarn_undOvr_qty+=$under_over_issued;
							$tot_yarn_under_over_issued+=$yarn_under_over_issued;


							$tot_grey_req_qty+=$grey_fabric_req_qnty;
							$tot_grey_in_qty+=$knit_qnty_in;
							$tot_grey_out_qty+=$knit_qnty_out;
							$tot_grey_rec_qty+=$knit_gray_rec_inside;
							$tot_reject_fabric_receive+=$reject_fabric_receive;
							$tot_grey_rec_qty_outside+=$knit_gray_rec_outside;
							$tot_knit_under_over_prod_qty+=$knit_under_over_prod_qty;

							$tot_knit_process_lossKg_in+=$knit_process_lossKg_in;
							$tot_knit_processLossKgOut+=$knit_processLossKgOut;
							$tot_tot_knit_processLogssKg+=$tot_knit_processLogssKg;


							$tot_grey_rec_qty_in_trans+=$transfer_in_qnty_rec_knit;
							$tot_grey_rec_qty_out_trans+=$transfer_out_qnty_rec_knit;
							$tot_knit_rec_qty_total+=$tot_knit_rec_qty;

							$tot_issuedToDyeQnty_in+=$issuedToDyeQnty_in;
							$tot_issuedToDyeQnty_out+=$issuedToDyeQnty_out;
							$tot_tot_issuedToDyeQnty+=$tot_issuedToDyeQnty;

							$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
							$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
							$tot_grey_qty+=$total_knitting;
							$tot_grey_prLoss_qty+=$process_loss;
							$tot_grey_undOver_qty+=$under_over_prod;
							$tot_recv_under_over_prod+=$recv_under_over_prod;
							$tot_grey_issDye_qty+=$issuedToDyeQnty;
							$tot_grey_lftOver_qty+=$left_over;

							$tot_fin_req_qty+=$finish_fabric_req_qnty;
							$tot_fin_in_qty+=$finish_qnty_in;
							$tot_fin_out_qty+=$finish_qnty_out;
							$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
							$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
							$tot_fin_qty+=$total_finishing;
							$tot_fin_prLoss_qty+=$process_loss_finishing;
							$tot_fin_undOver_qty+=$under_over_finish_prod;
							$tot_fin_issCut_qty+=$issuedToCutQnty;
							$tot_fin_issRet_qty+=$knit_fin_return_qnty;
							$tot_fin_lftOver_qty+=$finish_left_over;

							$tot_grey_fabric_aop_req_wo_qnty+=$grey_fabric_aop_req_wo_qnty;
							$tot_aop_delivery_qty+=$aop_delivery_qty;
							$tot_aop_recv_qty+=$aop_aop_recv_qnty;
							$tot_aop_balance_qty+=$aop_balance_qty;

							$total_fin_prod_qty+=$tot_fin_prod_qty;
							$total_fin_prod_under_over_qty+=$fin_prod_under_over_qty;
							$total_fin_fab_process_loss_inside+=$fin_fab_process_loss_inside;
							$total_fin_fab_process_loss_outside+=$fin_fab_process_loss_outside;
							$total_tot_knit_process_loss+=$tot_knit_process_loss;

							$total_emb_issue_qnty_in+=$emb_issue_qnty_in;
							$total_emb_issue_qnty_out+=$emb_issue_qnty_out;
							$total_tot_emb_issueQty+=$tot_emb_issueQty;

							$total_emb_recv_qnty_in+=$emb_recv_qnty_in;
							$total_emb_recv_qnty_out+=$emb_recv_qnty_out;
							$total_tot_emb_recvQty+=$tot_emb_recvQty;

							$total_finish_qnty_in_rec_gmt+=$finish_qnty_in_rec_gmt;
							$total_finish_qnty_out_rec_gmt+=$finish_qnty_out_rec_gmt;

							$tot_wovenReqQty+=$wovenReqQty;
							$tot_wovenRecQty+=$wovenRecQty;
							$tot_wovenRecBalQty+=$wovenFabRecBal;
							$tot_wovenIssueQty+=$wovenIssueQty;
							$tot_wovenIssueBalQty+=$wovenFabIssueBal;

							$tot_gmt_qty+=$tot_po_plan_qnty;
							$tot_cutting_qty+=$cuttingQty;
							$tot_printIssIn_qty+=$print_issue_qnty_in;
							$tot_printIssOut_qty+=$print_issue_qnty_out;
							$tot_printIssue_qty+=$total_print_issued;
							$tot_printRcvIn_qty+=$print_recv_qnty_in;
							$tot_printRcvOut_qty+=$print_recv_qnty_out;
							$tot_printRcv_qty+=$total_print_recv;
							$tot_printRjt_qty+=$print_reject_qnty;

							$tot_sewInInput_qty+=$sew_input_qnty_in;
							$tot_sewInOutput_qty+=$sew_input_qnty_out;
							$tot_sewIn_qty+=$total_sew_input;
							$tot_sewInBal_qty+=$sew_input_balance_qnty;

							$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
							$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
							$tot_sewRcv_qty+=$total_sew_recv;
							$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
							$tot_sewRcvRjt_qty+=$sew_reject_qnty;

							$tot_washRcvIn_qty+=$wash_recv_qnty_in;
							$tot_washRcvOut_qty+=$wash_recv_qnty_out;
							$tot_washRcv_qty+=$total_wash_recv;
							$tot_washRcvBal_qty+=$wash_balance_qnty;

							$tot_gmtFinIn_qty+=$gmt_finish_in;
							$tot_gmtFinOut_qty+=$gmt_finish_out;
							$tot_gmtFin_qty+=$total_gmts_finish_qnty;
							$tot_gmtFinBal_qty+=$finish_balance_qnty;
							$tot_gmtFinRjt_qty+=$finish_reject_qnty;
							$tot_gmtEx_qty+=$exfactory_qnty;
							$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

							$tot_shortExcess_exFactory_qty+=$short_excess_exFactoryQty;
							$tot_prLoss_qty+=$process_loss;
							$tot_prLossDye_qty+=$process_loss_dyeing;
							$tot_prLossCut_qty+=$cutting_process_loss;
							$tot_process_loss_yern_per += $process_loss_yern_per;
							$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
							$tot_process_loss_cutting_per += $process_loss_cutting_per;
							$i++;
						}
					}

					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="100"></td>
                   <td width="110"></td>
                    <td width="80">Total :</td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="100">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" title="<? echo $tot_grey_req_qty;?>" id="td_yarn_req_qty_booking"><? echo number_format($tot_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issIn_qty"><? echo number_format($tot_yarn_issIn_qty,2); ?></td>
                     <td width="80" align="right" id="td_yarn_issRetIn_qty"><? echo number_format($tot_yarn_issue_ret_inside,2); ?></td>
                     <td width="80" align="right" id="td_yarn_issReject_qty"><? echo number_format($tot_cons_reject_qnty,2); ?></td>

                    <td width="80" align="right" id="td_yarn_issOut_qty"><? echo number_format($tot_yarn_issOut_qty,2); ?></td>
                     <td width="80" align="right" id="td_yarn_issRetout_qty"><? echo number_format($tot_yarn_issue_ret_outside,2); ?></td>

                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                     <td width="80" align="right" id="td_yarn_issRettot_qty"><? echo number_format($tot_yarn_issue_ret,2); ?></td>
                    <td width="80" align="right" id="td_yarn_undOvr_qty"><? echo number_format($tot_yarn_undOvr_qty,2); ?></td>

                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($tot_yarn_under_over_issued,2); ?></td>
                    <td width="80" align="right" id="td_grey_in_qty"><? echo number_format($tot_grey_in_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_out_qty"><? echo number_format($tot_grey_out_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_qty"><? echo number_format($tot_grey_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_knit_under_overPord"><? echo number_format($tot_knit_under_over_prod_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_processLossInSide"><? echo number_format($tot_knit_process_lossKg_in,2); ?></td>
                   <td width="80" align="right" id="td_grey_total_processLossOutSide"><? echo number_format($tot_knit_processLossKgOut,2); ?></td>
                    <td width="80" align="right" id="td_grey_total_processLossKG"><? echo number_format($tot_tot_knit_processLogssKg,2); ?></td>

                    <td width="80" align="right" id="td_grey_rec_qty"><? echo number_format($tot_grey_rec_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_reject_fab_qty"><? echo number_format($tot_reject_fabric_receive,2); ?></td>
                    <td width="80" align="right" id="td_grey_rec_qty_outside"><? echo number_format($tot_grey_rec_qty_outside,2); ?></td>
                    <td width="80" align="right" id="td_grey_rec_qty_inside_trans"><? echo number_format($tot_grey_rec_qty_in_trans,2); ?></td>
                    <td width="80" align="right" id="td_grey_rec_qty_outside_trans"><? echo number_format($tot_grey_rec_qty_out_trans,2); ?></td>
                    <td width="80" align="right" id="td_grey_rec_qty_total"><? echo number_format($tot_knit_rec_qty_total,2); ?></td>
                    <td width="80" align="right" id="td_recv_under_over_prod_qty"><? echo number_format($tot_recv_under_over_prod,2); ?></td>

                    <td width="80" align="right" id="td_grey_issDye_qty_inside"><? echo number_format($tot_issuedToDyeQnty_in,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty_outside"><? echo number_format($tot_issuedToDyeQnty_out,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty_total"><? echo number_format($tot_tot_issuedToDyeQnty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($tot_grey_lftOver_qty,2); ?></td>


                    <td width="80" align="right" id="td_aop_req_qty"><? echo number_format($tot_grey_fabric_aop_req_wo_qnty,2); ?></td>
                    <td width="80" align="right" id="td_aop_deli_qty"><? echo number_format($tot_aop_delivery_qty,2); ?></td>
                    <td width="80" align="right" id="td_aop_rec_qty"><? echo number_format($tot_aop_recv_qty,2); ?></td>
                    <td width="80" align="right" id="td_aop_balance_qty"><? echo number_format($tot_aop_balance_qty,2); ?></td>

                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>


                    <td width="80" align="right" id="td_fin_in_qty"><? echo number_format($tot_fin_in_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_out_qty"><? echo number_format($tot_fin_out_qty,2); ?></td>

                    <td width="80" align="right" id="td_fin_total_prod_qty"><? echo number_format($total_fin_prod_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_prod_under_over_qty"><? echo number_format($total_fin_prod_under_over_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_prod_process_loss_inside_qty"><? echo number_format($total_fin_fab_process_loss_inside,2); ?></td>
                    <td width="80" align="right" id="td_fin_prod_process_loss_outside_qty"><? echo number_format($total_fin_fab_process_loss_outside,2); ?></td>
                    <td width="80" align="right" id="td_fin_total_knit_process_qty"><? echo number_format($total_tot_knit_process_loss,2); ?></td>
                    <td width="80" align="right" id="td_fin_recv_qty_inside"><? echo number_format($total_finish_qnty_in_rec_gmt,2); ?></td>
                    <td width="80" align="right" id="td_fin_recv_qty_outside"><? echo number_format($total_finish_qnty_out_rec_gmt,2); ?></td>



                    <td width="80" align="right" id="td_fin_transIn_qty"><? echo number_format($tot_fin_transIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_transOut_qty"><? echo number_format($tot_fin_transOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_fin_qty,2); ?></td>

                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($tot_fin_issCut_qty,2); ?></td>
                     <td width="80" align="right" id="td_fin_issRet_qty"><? echo number_format($tot_fin_issRet_qty,2); ?></td>

                    <td width="80" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_fin_lftOver_qty,2); ?></td>

                    <td width="80" align="right" id="td_wovenReqQty"><? echo number_format($tot_wovenReqQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenRecQty"><? echo number_format($tot_wovenRecQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenRecBalQty"><? echo number_format($tot_wovenRecBalQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenIssueQty"><? echo number_format($tot_wovenIssueQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenIssueBalQty"><? echo number_format($tot_wovenIssueBalQty,2); ?></td>


                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_gmt_qty); ?></td>
                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cutting_qty); ?></td>
                    <td width="80" align="right" id="td_printIssIn_qty"><? echo number_format($tot_printIssIn_qty); ?></td>
                    <td width="80" align="right" id="td_printIssOut_qty"><? echo number_format($tot_printIssOut_qty); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty"><? echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_printRcvIn_qty"><? echo number_format($tot_printRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_printRcvOut_qty"><? echo number_format($tot_printRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_printRcv_qty"><? echo number_format($tot_printRcv_qty); ?></td>

                     <td width="80" align="right" id="td_embroIssueIn_qty"><? echo number_format($total_emb_issue_qnty_in); ?></td>
                     <td width="80" align="right" id="td_embroIssueSuncon_qty"><? echo number_format($total_emb_issue_qnty_out); ?></td>
                     <td width="80" align="right" id="td_embroIssueTotal_qty"><? echo number_format($total_tot_emb_issueQty); ?></td>
                     <td width="80" align="right" id="td_embroRcvIn_qty"><? echo number_format($total_emb_recv_qnty_in); ?></td>
                     <td width="80" align="right" id="td_embroRcvSubcon_qty"><? echo number_format($total_emb_recv_qnty_out); ?></td>
                     <td width="80" align="right" id="td_embroRcvTotal_qty"><? echo number_format($total_tot_emb_recvQty); ?></td>

                    <td width="80" align="right" id="td_printRjt_qty"><? echo number_format($tot_printRjt_qty); ?></td>

                    <td width="80" align="right" id="td_sewInInput_qty"><? echo number_format($tot_sewInInput_qty); ?></td>
                    <td width="80" align="right" id="td_sewInOutput_qty"><? echo number_format($tot_sewInOutput_qty); ?></td>
                    <td width="80" align="right" id="td_sewIn_qty"><? echo number_format($tot_sewIn_qty); ?></td>
                    <td width="80" align="right" id="td_sewInBal_qty"><? echo number_format($tot_sewInBal_qty); ?></td>

                    <td width="100">&nbsp;</td>

                    <td width="80" align="right" id="td_sewRcvIn_qty"><? echo number_format($tot_sewRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvOut_qty"><? echo number_format($tot_sewRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcv_qty"><? echo number_format($tot_sewRcv_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvBal_qty"><? echo number_format($tot_sewRcvBal_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvRjt_qty"><? echo number_format($tot_sewRcvRjt_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvIn_qty"><? echo number_format($tot_washRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvOut_qty"><? echo number_format($tot_washRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_washRcv_qty"><? echo number_format($tot_washRcv_qty); ?></td>


                    <td width="80" align="right" id="td_gmtFinIn_qty"><? echo number_format($tot_gmtFinIn_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinOut_qty"><? echo number_format($tot_gmtFinOut_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFin_qty"><? echo number_format($tot_gmtFin_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinBal_qty"><? echo number_format($tot_gmtFinBal_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinRjt_qty"><? echo number_format($tot_gmtFinRjt_qty); ?></td>
                    <td width="80" align="right" id="td_gmtrej_qty"><? echo number_format($total_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_gmtEx_qty"><? echo number_format($tot_gmtEx_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinLeftOver_qty"><? echo number_format($tot_gmtFinLeftOver_qty); ?></td>
                    <td width="80" align="right" id="td_shortExcess_exFactory_qty"><? echo number_format($tot_shortExcess_exFactory_qty); ?></td>

                    <td width="80" align="right" id="td_prLoss_qty"><? echo number_format($tot_prLoss_qty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_yern_per); ?></td>
                    <td width="80" align="right" id="td_prLossDye_qty"><? echo number_format($tot_prLossDye_qty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_dyeing_per); ?></td>
                    <td align="right" id="td_prLossCut_qty"><? echo number_format($tot_prLossCut_qty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_cutting_per); ?></td>
                </tr>
           </table>
        </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$cbo_search_type**$type";
    exit();
}
if($action=="order_report_generate7") //Show 7 //GBL REF FROM 1
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$ref_no=str_replace("'","",$txt_ref_no);
	$file_no=str_replace("'","",$txt_file_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	//echo $txt_booking_no ;
	//$txt_ex_date_form=str_replace("'","",$txt_ex_date_form);
	//$txt_ex_date_to=str_replace("'","",$txt_ex_date_to);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$type=str_replace("'","",$type);

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	/*$ship_date_cond="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	$ex_fact_date_cond="";
	if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	{
		$ex_fact_date_cond="and c.ex_factory_date between '$txt_ex_date_form' and '$txt_ex_date_to'  AND c.is_deleted = 0 AND c.status_active = 1";
	}*/
	$ship_date_cond="";
	if($cbo_date_category==1)
	{

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	}
	else if($cbo_date_category==2)
	{
		//$ex_fact_date_cond="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and c.ex_factory_date between '$txt_date_from' and '$txt_date_to' AND c.is_deleted = 0 AND c.status_active = 1";
		}
	}
	/*else if($cbo_date_category==3) //Ref Closing date
	{

		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and d.closing_date between '$txt_date_from' and '$txt_date_to'";
		}
	}*/


	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
	$order_cond="";
	if(trim($ref_no)!="") $ref_no_cond="and b.grouping in('$ref_no')";
	if(trim($file_no)!="") $ref_no_cond="and b.file_no in('$file_no')";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	//echo $txt_order_id;die;
	if($txt_order_id!="")
	{
		$order_cond="and b.id in($txt_order_id)";
	}

	else
	{
		if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'"; else $order_cond="";
	}
	$booking_cond .= ($txt_booking_no !="") ? "and d.booking_no in('$txt_booking_no')" :"";
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	//if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	if($cbo_date_category==2)
	{
		$sql_po="SELECT a.id as job_id,d.booking_no, a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c , wo_booking_dtls d
		where a.job_no=b.job_no_mst and  b.id=c.po_break_down_id and d.po_break_down_id=b.id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond $booking_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else
	{
		 $sql_po="SELECT a.id as job_id,d.booking_no, a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_booking_dtls d , wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1
		where a.job_no=b.job_no_mst and d.po_break_down_id=b.id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond $booking_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	//echo  $sql_po;die;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=$po_id_array=$job_id_array=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$result_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["ref_no"]=$row[csf("grouping")];
		$result_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("po_id")]]["po_qnty"]=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")];
		$result_data_arr[$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
		$JobArr[]="'".$row[csf('job_no')]."'";
		$job_no=$row[csf('job_no')];
		$po_id_array[$row["PO_ID"]]=$row["PO_ID"];
		$job_id_array[$row["JOB_ID"]]=$row["JOB_ID"];
	}
	//=========================================================================================================
	//												CLEAR TEMP ENGINE
	// ==========================================================================================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=171 and ref_from in (1,2) ");
	oci_commit($con);   
	// =========================================================================================================
	//												INSERT DATA INTO TEMP ENGINE
	// =========================================================================================================
	fnc_tempengine("gbl_temp_engine", $user_id, 171, 1,$po_id_array, $empty_arr); 
	fnc_tempengine("gbl_temp_engine", $user_id, 171, 2,$job_id_array, $empty_arr); 


	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$all_po_id_str=implode("','",array_unique(explode(",",$all_po_id)));
	//$JobNoArr=implode(",",$JobArr);
	//$yarn= new yarn($JobArr,'job');
	//$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	//print_r($yarn_qty_arr);
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(!empty($JobArr)){
	 $condition->po_id_in("$all_po_id");
	}
	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	//$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
	$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();


	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";
	$poIdStr = "'".$all_po_id_str."'";
	// echo $poIdStr; die;
	$poIds=chop($all_po_id);
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
	$po_cond_for_in=" and (";
	$po_cond_for_in2=" and (";
	$po_cond_for_in3=" and (";

	$poIdsArr=array_chunk(explode(",",$poIds),999);
	foreach($poIdsArr as $ids)
	{
	$ids=implode(",",$ids);
	$po_cond_for_in.=" b.po_break_down_id in($ids) or";
	$po_cond_for_in2.=" a.po_breakdown_id in($ids) or";
	$po_cond_for_in3.=" b.order_id in($ids) or";

	}
	$po_cond_for_in=chop($po_cond_for_in,'or ');
	$po_cond_for_in.=")";
	$po_cond_for_in2=chop($po_cond_for_in2,'or ');
	$po_cond_for_in2.=")";
	$po_cond_for_in3=chop($po_cond_for_in3,'or ');
	$po_cond_for_in3.=")";

	}
	else
	{
	$po_cond_for_in=" and b.po_break_down_id in($poIds)";
	$po_cond_for_in2=" and a.po_breakdown_id  in($poIds)";
	$po_cond_for_in3=" and b.order_id in($poIds)";


	}
	$po_cond_for_in5="  b.PO_ID in($poIds)";
	$po_cond_for_in6="  FROM_ORDER_ID IN($poIds)";
	$po_cond_for_in7="  ORDER_ID IN($poIds)";
	$po_cond_for_in8="  ORDER_ID IN($poIdStr)";

			if($db_type==2)
			{
				//$col_grp="listagg(CAST(a.booking_no as VARCHAR(4000)),',') within group (order by a.booking_no) as booking_no";
				$col_grp="rtrim(xmlagg(xmlelement(e,a.booking_no,',').extract('//text()') order by a.booking_no).GetClobVal(),',') as booking_no";
			}
			else
			{
				$col_grp="group_concat(a.booking_no) as booking_no";
			}
	$booking_req_arr=array();
	$sql_wo=sql_select("SELECT $col_grp,a.booking_type,b.po_break_down_id,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	sum(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	sum(b.fin_fab_qnty) as fin_fab_qnty,
	sum(CASE WHEN a.item_category=12 and a.process=35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty
	from wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine tmp  where a.booking_no=b.booking_no and b.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.booking_type,b.po_break_down_id");
	// echo $sql_wo; die;


	//finish_prod_arr
	foreach ($sql_wo as $brow)
	{
		if($brow[csf("grey_req_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['gray']=$brow[csf("grey_req_qnty")];
		}
		if($brow[csf("woven_req_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['woven']=$brow[csf("woven_req_qnty")];
		}
		if($brow[csf("fin_fab_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['fin']=$brow[csf("fin_fab_qnty")];
		}
		if($brow[csf("aop_wo_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['aop_wo_qnty']=$brow[csf("aop_wo_qnty")];
		}
		if($brow[csf("booking_type")]==1)
		{
			if($db_type==2 && $brow[csf("booking_no")]!=""){
				$brow[csf("booking_no")] = $brow[csf("booking_no")]->load();
			}
			$booking_req_arr[$brow[csf("po_break_down_id")]]['booking_no']=$brow[csf("booking_no")];
		}
	}
	$booking_cond2 = $txt_booking_no  ? " and a.booking_no in($txt_booking_no) " : "";
	$sql_for_booking = " SELECT  a.booking_no, a.booking_type, b.po_break_down_id, SUM ( CASE WHEN a.fabric_source = 1 AND a.item_category = 2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty, SUM ( CASE WHEN a.fabric_source IN (1, 2) AND a.item_category = 3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty, SUM (b.fin_fab_qnty) AS fin_fab_qnty, SUM ( CASE WHEN a.item_category = 12 AND a.process = 35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty FROM wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine tmp WHERE a.booking_no = b.booking_no and b.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id  AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1 $booking_cond2 GROUP BY a.booking_no, a.booking_type, b.po_break_down_id";
	// echo $sql_for_booking ;
	$book_arr  = array();
	foreach(sql_select($sql_for_booking) as $key_=>$data)
	{
		$book_arr[$data['PO_BREAK_DOWN_ID']]['booking_no'] = $data['BOOKING_NO'];
		$book_arr[$data['PO_BREAK_DOWN_ID']]['fin'] = $data['FIN_FAB_QNTY'];
	}
	// echo "<pre>";
	// print_r($book_arr);
	$sql_res=sql_select("SELECT b.po_break_down_id as po_id,
	sum(b.ex_factory_qnty) as return_qnty
	from  pro_ex_factory_mst b,gbl_temp_engine tmp where b.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and b.entry_form=85 and b.status_active=1 and b.is_deleted=0  group by b.po_break_down_id"); 
	$ex_factory_qty_arr=array();
	foreach($sql_res as $row)
	{
		$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('return_qnty')];
	}

	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$dataArrayYarnReq=array();
	$yarn_sql="SELECT a.job_no, a.avg_cons_qnty as qnty from wo_pre_cost_fab_yarn_cost_dtls a,gbl_temp_engine tmp where a.job_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=2 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 ";
	// echo $yarn_sql; die;
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarnReq[$yarnRow[csf('job_no')]] +=$yarnRow[csf('qnty')];
	}

	$reqDataArray=sql_select("SELECT  a.po_break_down_id, sum((b.requirment/b.pcs)*a.plan_cut_qnty) as grey_req, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b,gbl_temp_engine tmp where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.is_deleted=0 and a.status_active=1  group by a.po_break_down_id");  

	$grey_finish_require_arr=array();
	foreach($reqDataArray as $row)
	{
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["grey_req"]=$row[csf("grey_req")];
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["finish_req"]=$row[csf("finish_req")];
	}

	$yarnDataArr=sql_select("SELECT a.po_breakdown_id,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_issue_master c,gbl_temp_engine tmp
						where a.trans_id=b.id and b.mst_id=c.id  and a.po_breakdown_id=tmp.ref_val and b.item_category=1 and c.issue_purpose in(1,4) and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");  
	$yarn_issue_arr=array();
	foreach($yarnDataArr as $row)
	{
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_in"]=$row[csf("issue_qnty_in")];
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_out"]=$row[csf("issue_qnty_out")];
	}
	//order_wise_pro_details
	$yarnReturnDataArr=sql_select("SELECT a.po_breakdown_id,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
				from order_wise_pro_details a, inv_transaction b, inv_receive_master c,gbl_temp_engine tmp
				where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id=tmp.ref_val and b.item_category=1 and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
				group by a.po_breakdown_id");


	$yarn_issue_rtn_arr=array();
	foreach($yarnReturnDataArr as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
	}

	$knit_finReturnDataArr=sql_select("SELECT a.po_breakdown_id,
				sum(CASE WHEN a.entry_form=52 and c.entry_form=52  THEN a.quantity ELSE 0 END) AS return_qnty

				from order_wise_pro_details a, inv_transaction b, inv_receive_master c,gbl_temp_engine tmp
				where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id=tmp.ref_val and a.entry_form=52 and c.entry_form=52 and b.item_category=2 and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 
				group by a.po_breakdown_id");



	$knit_fin_rtn_arr=array();
	foreach($knit_finReturnDataArr as $row)
	{
		$knit_fin_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty"]=$row[csf("return_qnty")];

	}
	unset($knit_finReturnDataArr);


	$dataArrayTrans=sql_select("SELECT po_breakdown_id,
								sum(CASE WHEN a.entry_form ='11' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN a.entry_form ='11' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN a.entry_form ='13' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN a.entry_form ='13' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN a.entry_form in(14,15) and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_finish,
								sum(CASE WHEN a.entry_form in(14,15) and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_finish,
								sum(CASE WHEN a.entry_form ='14' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_rec_knit2,
								sum(CASE WHEN a.entry_form ='14' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_rec_knit2,
								sum(CASE WHEN a.entry_form in(82,183,110) and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_rec_knit,
								sum(CASE WHEN a.entry_form in(82,183,110) and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_rec_knit

							from order_wise_pro_details a,gbl_temp_engine tmp where  a.po_breakdown_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and a.entry_form in(11,13,15,14,82,183,110)
							 group by a.po_breakdown_id");



	$transfer_data_arr=array();
	foreach($dataArrayTrans as $row)
	{
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_yarn"]=$row[csf("transfer_in_qnty_yarn")];//transfer_in_qnty_rec_knit
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_yarn"]=$row[csf("transfer_out_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_knit"]=$row[csf("transfer_in_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_knit"]=$row[csf("transfer_out_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_finish"]=$row[csf("transfer_in_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_finish"]=$row[csf("transfer_out_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_rec_knit"]=$row[csf("transfer_in_qnty_rec_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_rec_knit"]=$row[csf("transfer_out_qnty_rec_knit")];
	}

	// decision pending	dyed yearn receive
	//$greyYarnIssueQnty=return_library_array("select c.po_breakdown_id, sum(c.quantity) as issue_qnty from inv_transaction a, inv_issue_master b,  order_wise_pro_details c where a.mst_id=b.id and a.id=c.trans_id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and c.po_breakdown_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2 group by c.po_breakdown_id","po_breakdown_id","issue_qnty");


	//$dyedYarnRecvQnty=return_field_value("sum(a.cons_quantity) as recv_qnty","inv_transaction a, inv_receive_master b","a.mst_id=b.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1","recv_qnty");

	$prodKnitDataArr=sql_select("SELECT a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
				sum(CASE WHEN a.entry_form in(58,22) and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_inside,
				sum(CASE WHEN a.entry_form in(58,22) and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_outside
				from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c,gbl_temp_engine tmp where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and c.item_category=13 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.po_breakdown_id");// and c.receive_basis<>9  
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_in"]=$row[csf("knit_qnty_in")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_out"]=$row[csf("knit_qnty_out")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec_inside"]=$row[csf("knit_qnty_rec_inside")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec_outside"]=$row[csf("knit_qnty_rec_outside")];
	}
	// echo "<pre>"; print_r($kniting_prod_arr); die;
	$prodFinDataArr=sql_select("SELECT a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
				sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_in_rec_gmt,
				sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_out_rec_gmt,
				sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec
				from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c ,gbl_temp_engine tmp where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id"); 



	$finish_prod_arr=array();

	foreach($prodFinDataArr as $row)
	{
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in"]=$row[csf("finish_qnty_in")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out"]=$row[csf("finish_qnty_out")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in_rec_gmt"]=$row[csf("finish_qnty_in_rec_gmt")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out_rec_gmt"]=$row[csf("finish_qnty_out_rec_gmt")];

		$finish_prod_arr[$row[csf("po_breakdown_id")]]["woven_rec"]=$row[csf("woven_rec")];
	}//c.knit_dye_source
	$gray = "SELECT a.qnty,a.po_breakdown_id,a.entry_form from pro_roll_details a,gbl_temp_engine tmp where a.po_breakdown_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.entry_form = 61 and a.status_active=1 and a.is_deleted=0";
	// echo $gray ;die;
	$gray_iss_arr = array();
	foreach(sql_select($gray) as $key=> $data)
	{
		$gray_iss_arr[$data['PO_BREAKDOWN_ID']]['PrQty'] += $data['QNTY']	;
	}
	//Batch Qnty Data
	$batchDataArr=sql_select("SELECT b.po_id,b.batch_qnty as batch_qty from pro_batch_create_mst a,pro_batch_create_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and b.po_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.batch_against not in(2) and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");  
	$po_wise_batch_array = array();
	foreach ($batchDataArr as $v) 
	{
		$po_wise_batch_array[$v['PO_ID']] += $v['BATCH_QTY'];
	} 
	// echo "<pre>"; print_r($po_wise_batch_array); die;
	//febric transfer
	$transfer_Arr = array();
	$transfer = "SELECT a.from_order_id, a.transfer_qnty from inv_item_transfer_dtls a,gbl_temp_engine tmp where a.from_order_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 ";
	// echo $transfer; die;
    foreach(sql_select($transfer) as $key=>$res)
	{
		$transfer_Arr[$res['FROM_ORDER_ID']]['Trans_Qty'] = $res['TRANSFER_QNTY'];
	}
	//cutting feb rcv
	$cutting_rcv= "SELECT a.cons_quantity,a.order_id from inv_transaction a,gbl_temp_engine tmp where a.order_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0  and a.transaction_type=5";
	// echo $cutting_rcv; die;
     $cutting_rcv_arr =array();
	 foreach(sql_select($cutting_rcv) as $val=>$fiz)
	 {
		$cutting_rcv_arr[$fiz['ORDER_ID']]['Cutt_Feb_Rcv'] = $fiz['CONS_QUANTITY'];
	 }

	//  echo "<pre>";print_r($cutting_rcv_arr);die;
	//cutting feb delivery
	$cutting_rcv_del= "SELECT a.issue_qnty ,a.order_id from inv_finish_fabric_issue_dtls a,order_wise_pro_details b,gbl_temp_engine tmp  where a.id=b.dtls_id and b.po_breakdown_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and b.entry_form=18 and a.status_active=1 and a.is_deleted=0 ";
	//  echo $cutting_rcv_del; die;  
	$cutting_del_arr =array();
	foreach(sql_select($cutting_rcv_del) as $val=>$data)
	{
	 $cutting_del_arr[$data['ORDER_ID']]['ISSUE_QNTY'] = $data['ISSUE_QNTY'];
	}
	$rej_arr =array();
	$rej_sql = "SELECT a.po_break_down_id,a.reject_qnty,production_type,production_quantity from pro_garments_production_mst a,gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and production_type in(1,3,5,7,8)";
	// echo $rej_sql; die;
	$gmt_arr = array();
	$output_data_arr = array();
	$Iron_data_arr = array();
	$packing_data_arr = array();
	foreach(sql_select($rej_sql) as $data=>$res)
	{
		if ($res['PRODUCTION_TYPE']==1) 
		{
			$rej_arr[$res['PO_BREAK_DOWN_ID']]['REJECT_QNTY'] += $res['REJECT_QNTY']; 
		}
		elseif ($res['PRODUCTION_TYPE']==3) 
		{
			$gmt_arr[$res['PO_BREAK_DOWN_ID']]['REJECT_Gmt_QNTY'] += $res['REJECT_QNTY'];
		}
		elseif ($res['PRODUCTION_TYPE']==5) 
		{
			
			$output_data_arr[$res['PO_BREAK_DOWN_ID']]['PrDataQty'] += $res['PRODUCTION_QUANTITY']; 
		}
		elseif ($res['PRODUCTION_TYPE']==7) 
		{
			$Iron_data_arr[$res['PO_BREAK_DOWN_ID']]['IronQty'] += $res['PRODUCTION_QUANTITY']; 
		}
		elseif ($res['PRODUCTION_TYPE']==8) 
		{ 
			$packing_data_arr[$res['PO_BREAK_DOWN_ID']]['Packing_qty'] += $res['PRODUCTION_QUANTITY']; 
		}
	}
	// echo "<pre>"; print_r($Iron_data_arr); die;
	// avg cons
	$cons = "SELECT a.po_break_down_id,a.cons from  wo_pre_cos_fab_co_avg_con_dtls a,gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0";
	// echo 	$cons; die;
	foreach(sql_select($cons) as $cons_data=>$row)
	{
		$cons_arr_data[$row['PO_BREAK_DOWN_ID']]['CONS'] += $row['CONS'];
		$cons_arr_count[$row['PO_BREAK_DOWN_ID']] ++;

	}
	// Rcv Entry
	$sel = "SELECT a.po_break_down_id,a.order_quantity from wo_po_color_size_breakdown a,gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0";
	// echo $sel;die;
	$ex1 = sql_select($sel);
	$rcv_qty_arr = array();
	foreach($ex1 as $rel=>$val)
	{
		$rcv_qty_arr[$val['PO_BREAK_DOWN_ID']]['OrderQty'] += $val['ORDER_QUANTITY'];
	} 
 
	$Total_Fin_Receive_sql = "SELECT a.receive_qnty,a.order_id from pro_finish_fabric_rcv_dtls a,order_wise_pro_details b,gbl_temp_engine tmp where a.id=b.dtls_id and b.po_breakdown_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	// echo $Total_Fin_Receive_sql; die;
	
	$sql_fin_arr = array();
	foreach(sql_select($Total_Fin_Receive_sql) as $key_d=>$rcv)
	{
		$sql_fin_arr[$rcv['ORDER_ID']]['RCVQNTY'] = $rcv['RECEIVE_QNTY'];
	}
    // echo "<pre>";
	// print_r($sql_fin_arr);
	$ex_sql = "SELECT a.po_break_down_id,a.ex_factory_qnty from pro_ex_factory_mst a,gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0";
	// echo $ex_sql; die;
	$ex_fect_arr = array();
	foreach(sql_select($ex_sql) as $ex_data=>$ex_row)
	{
		$ex_fect_arr[$ex_row['PO_BREAK_DOWN_ID']]['EX_FACTORY_QNTY'] += $ex_row['EX_FACTORY_QNTY'];
	}

	$issueData=sql_select("SELECT po_breakdown_id,
							sum(CASE WHEN a.entry_form=16 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_in_qnty,
							sum(CASE WHEN a.entry_form=16  and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_out_qnty,
							sum(CASE WHEN a.entry_form=61 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_out,
							sum(CASE WHEN a.entry_form=61 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_in,
							sum(CASE WHEN a.entry_form=18 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty,
							sum(CASE WHEN a.entry_form=71 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
							sum(CASE WHEN a.entry_form=19 THEN a.quantity ELSE 0 END) AS woven_issue
							from order_wise_pro_details a,inv_grey_fabric_issue_dtls b,inv_issue_master
	c,gbl_temp_engine tmp where a.dtls_id=b.id and b.mst_id=c.id and a.entry_form in(16,18,19,61,71) and a.po_breakdown_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.po_breakdown_id");
	// echo $issueData; die;

	$grey_cut_issue_arr=array();
	foreach($issueData as $row)
	{
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty_in"]=$row[csf("grey_issue_in_qnty")]+$row[csf("grey_issue_qnty_roll_wise_in")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty_out"]=$row[csf("grey_issue_out_qnty")]+$row[csf("grey_issue_qnty_roll_wise_out")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["issue_to_cut_qnty"]=$row[csf("issue_to_cut_qnty")]+$row[csf("issue_to_cut_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["woven_issue"]=$row[csf("woven_issue")];
	}
	$trimsDataArr=sql_select("SELECT a.po_breakdown_id,
							sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
							sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
							from order_wise_pro_details a, product_details_master b,gbl_temp_engine tmp where a.prod_id=b.id and a.po_breakdown_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");
	// echo $trimsDataArr; die;							
	foreach($trimsDataArr as $row)
	{
		$trims_array[$row[csf('po_breakdown_id')]]['recv']=$row[csf('recv_qnty')];
		$trims_array[$row[csf('po_breakdown_id')]]['iss']=$row[csf('issue_qnty')];
	}
	$issuetoprocessDataArr=sql_select("SELECT b.order_id,

							sum(CASE WHEN a.entry_form=91 THEN b.batch_issue_qty ELSE 0 END) AS batch_issue_qty,
							sum(CASE WHEN a.entry_form=92 THEN b.batch_issue_qty ELSE 0 END) AS aop_recv_qnty
							from inv_receive_mas_batchroll a,pro_grey_batch_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and b.order_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and a.entry_form in(91,92) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.order_id");

	// echo $issuetoprocessDataArr; die;
	foreach($issuetoprocessDataArr as $row)
	{
		$aop_delivery_array[$row[csf('order_id')]]['batch_issue_qty']=$row[csf('batch_issue_qty')];
		$aop_delivery_array[$row[csf('order_id')]]['aop_recv_qnty']=$row[csf('aop_recv_qnty')];

	}

	$sql_consumtiont_qty=sql_select("SELECT b.po_break_down_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c,gbl_temp_engine tmp
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 and b.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 group by b.po_break_down_id, c.body_part_id ");
							//   echo $sql_consumtiont_qty; die;
			$finish_consumtion_arr=array();
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg=0;
				$con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];///str_replace("'","",$row_sew[csf("pcs")]);
				$finish_consumtion_arr[$row_consum[csf('po_break_down_id')]]+=$con_avg;
			}


	$gmtsProdDataArr=sql_select("SELECT  b.po_break_down_id,
					sum(CASE WHEN b.production_type=1 THEN b.production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN b.production_type=2 and b.embel_name=1 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS print_issue_qnty_in,
					sum(CASE WHEN b.production_type=2 and b.embel_name=1 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS print_issue_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS print_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS print_recv_qnty_out,
					sum(CASE WHEN b.production_type=2 and b.embel_name=2 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS emb_issue_qnty_in,
					sum(CASE WHEN b.production_type=2 and b.embel_name=2 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS emb_issue_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS emb_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS emb_recv_qnty_out,
					sum(CASE WHEN b.production_type=4 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS sew_input_qnty_in,
					sum(CASE WHEN b.production_type=4 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS sew_input_qnty_out,
					sum(CASE WHEN b.production_type=5 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS sew_recv_qnty_in,
					sum(CASE WHEN b.production_type=5 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS sew_recv_qnty_out,
					sum(CASE WHEN b.production_type=8 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS finish_qnty_in,
					sum(CASE WHEN b.production_type=8 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS finish_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=3 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS wash_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=3 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS wash_recv_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 THEN b.reject_qnty ELSE 0 END) AS print_reject_qnty,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 THEN b.reject_qnty ELSE 0 END) AS emb_reject_qnty,
					sum(CASE WHEN b.production_type=5 THEN b.reject_qnty ELSE 0 END) AS sew_reject_qnty,
					sum(CASE WHEN b.production_type=8 THEN b.reject_qnty ELSE 0 END) AS finish_reject_qnty,
					sum(CASE WHEN b.production_type=1 THEN b.reject_qnty ELSE 0 END) AS cutting_reject_qnty,
					sum(CASE WHEN b.production_type=7 THEN b.reject_qnty ELSE 0 END) AS iron_rej_qnty
					from pro_garments_production_mst b,gbl_temp_engine tmp where  b.po_break_down_id=tmp.ref_val and tmp.entry_form=171 and tmp.ref_from=1 and tmp.user_id=$user_id and b.is_deleted=0 and b.status_active=1 group by b.po_break_down_id");
	// echo $gmtsProdDataArr; die;

	$garment_prod_data_arr=array();
	foreach($gmtsProdDataArr as $row)
	{
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_qnty']=$row[csf("cutting_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_out']=$row[csf("finish_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_in']=$row[csf("wash_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_out']=$row[csf("wash_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_reject_qnty']=$row[csf("print_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_reject_qnty']=$row[csf("emb_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_reject_qnty']=$row[csf("sew_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_reject_qnty']=$row[csf("finish_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_reject_qnty']=$row[csf("cutting_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['iron_rej_qnty']=$row[csf("iron_rej_qnty")];
	}
	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	if($cbo_search_type==1)
	{
		$tbl_width=8710;

		$ship_date_html="Shipment Date";
		$ex_fact_date_html="Ex-Fact. Date";
	}
	else
	{
		$tbl_width=6620;
		$ship_date_html="Last Shipment Date";
		$ex_fact_date_html="Last Ex-Fact. Date";
	}
	$width = 3120;
	ob_start();
	?>
		<style>
			th,td{
				word-break: break-all;
			}
		</style>
        <div style="width:100%">
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
				<thead class="form_caption">
					<tr>
						<td align="center" width="100%" colspan="69" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
					</tr>
				</thead>	
            </table>
            <div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead class="form_caption">	  
						<tr style="font-size:13px">
							<th width="40" >SL</th>
							<th width="110" >Buyer</th>
							<th width="50" >Job Year</th>
							<th width="50" >Job No</th>
							<th width="100" >Style No</th>
							<th width="110">Fb Booking No <br> Main/Short</th>

							<?
							if($cbo_search_type==1)
							{
								?>
								<th width="100" >Order No</th>
								<?
							}
							?>
							<th width="80" >Order Qty. (Pcs)</th>
							<?
							if($cbo_search_type==1)
							{
								?>
								<th width="80" ><? echo $ship_date_html; ?></th>

								<?
							}
							?>
							<th width="80">Yarn Req.<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
							<th width="80">Yarn/Grey Req.
							<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>


							<th width="80">Yarn Total Issued</th>
							<th width="80"> Total Knit.  Prod.</th>


							<th width="80">Total Knit. Process Loss Kg.</th>


							<th width="80">Total Knit. Fab Receive.</th>
							<th width="80">Total Knit. Fabric Issued to Batch.</th>

							<th width="80">Gray Balance.</th>
							<th width="80">Batch Qnty.</th>
							<th width="80">Fin Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
							<th width="80">Total Fin. Receive.</th>
							<th width="80">Total finish fabric Transfer.</th>
							<th width="80">Cutting Fabric received KG.</th>
							<th width="80">Cutting Fabric Delivery KG.</th>
							<th width="80">Fabric Balance.</th>
							<th width="80"> Avg. Per Dz Consumption As per KD</th>
							<th width="80">Cutting Qty pcs.</th>
							<th width="80">Cut Panel Rej. (Pcs)</th>
							<th width="80">Cut Panel Rej %</th>
							<th width="80">Gmts. Total Print Issued</th>
							<th width="80">Gmts. Total Print Received</th>
							<th width="80">Gmts. Total Embry. Issued</th>
							<th width="80">Gmts. Total Rec. Embry.</th>
							<th width="80">Gmts. Print + Eby. Reject</th>

							<th width="80">Total Sew. Input (PCS)</th>
							<th width="80">Total  Sewing output (Pcs)</th>
							<th width="80">Total Finish Iron (Pcs)</th>
							<th width="80">Total Finish output (Pcs)</th>
							<th width="80">Shipment qty /Ex-Factory</th>

							<th width="80">Left Over  (Pcs)</th>
							<th width="80">Left over converted fabric (kg)</th>

						</tr>
					</thead>
				</table>	
				<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_7" width="<?= $width; ?>" rules="all" align="left">
						<tbody>
							<?
								$i=1;$tot_grey_rec_qty=$tot_grey_req_qty=0;
								$tot_yarn_issue_ret_inside=$tot_yarn_issue_ret_outside=$tot_yarn_issue_ret=0;

								if($cbo_search_type==1)
								{
									foreach($result_data_arr as $po_id=>$val)
									{
										$ratio=$val["ratio"];
										$tot_po_plan_qnty=$val["plan_cut"];
										$tot_po_qnty=$val["po_qnty"];
										$exfactory_qnty=$val["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
										$plan_cut_qty=$val["plan_cut"];
										$job_no=$val["job_no"];
										$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
										if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
										else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
										else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
										else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
										else $dzn_qnty=1;
										$dzn_qnty=$dzn_qnty*$ratio;

										$yarn_req_job=$yarn_qty_arr[$po_id];
										$yarn_required=$yarn_qty_arr[$po_id];
										$yarn_issue_inside=$yarn_issue_arr[$po_id]["issue_qnty_in"];
										$yarn_issue_outside=$yarn_issue_arr[$po_id]["issue_qnty_out"];

										$yarn_issue_ret_inside=$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
										$yarn_issue_ret_outside=$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];

										$transfer_in_qnty_yarn=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
										$transfer_out_qnty_yarn=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
										$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
										$under_over_issued=$yarn_required-$total_issued;

										$batch_qty = $po_wise_batch_array[$po_id];

										$grey_fabric_req_qnty=$booking_req_arr[$po_id]['gray'];
										$grey_fabric_aop_req_wo_qnty=$booking_req_arr[$po_id]['aop_wo_qnty'];
										$aop_delivery_qty=$aop_delivery_array[$po_id]['batch_issue_qty'];
										$aop_aop_recv_qnty=$aop_delivery_array[$po_id]['aop_recv_qnty'];

										$knit_qnty_in=$kniting_prod_arr[$po_id]["knit_qnty_in"];
										$knit_qnty_out=$kniting_prod_arr[$po_id]["knit_qnty_out"];
										$knit_gray_rec_inside=$kniting_prod_arr[$po_id]["knit_qnty_rec_inside"];
										$knit_gray_rec_outside=$kniting_prod_arr[$po_id]["knit_qnty_rec_outside"];

										$transfer_in_qnty_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
										$transfer_out_qnty_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];

										$transfer_in_qnty_rec_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_rec_knit"];
										$transfer_out_qnty_rec_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_rec_knit"];

										$total_knitting=$knit_qnty_in+$knit_qnty_out;
										$process_loss=($yarn_issue_inside+$yarn_issue_outside)-($knit_qnty_in+$knit_qnty_out);
										$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
										$issuedToDyeQnty_in=$grey_cut_issue_arr[$po_id]["grey_issue_qnty_in"];
										$issuedToDyeQnty_out=$grey_cut_issue_arr[$po_id]["grey_issue_qnty_out"];

										$tot_knit_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;

										$left_over=$tot_knit_rec_qty-($issuedToDyeQnty_in+$issuedToDyeQnty_out);


										$finish_fabric_req_qnty=$booking_req_arr[$po_id]['fin'];
										$finish_qnty_in=$finish_prod_arr[$po_id]["finish_qnty_in"];
										$finish_qnty_out=$finish_prod_arr[$po_id]["finish_qnty_out"];

										$knit_fin_return_qnty=$knit_fin_rtn_arr[$po_id]["return_qnty"];

										$finish_qnty_in_rec_gmt=$finish_prod_arr[$po_id]["finish_qnty_in_rec_gmt"];
										$finish_qnty_out_rec_gmt=$finish_prod_arr[$po_id]["finish_qnty_out_rec_gmt"];

										$transfer_in_qnty_finish=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
										$transfer_out_qnty_finish=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];

										$total_finishing=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;

										$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);

										$issuedToCutQnty=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];
										$finish_left_over=($total_finishing-$issuedToCutQnty)+$knit_fin_return_qnty;

										$wovenReqQty=$booking_req_arr[$po_id]['woven'];
										$wovenRecQty=$finish_prod_arr[$po_id]["woven_rec"];
										$fb_booking_no=$booking_req_arr[$po_id]['booking_no'];
										$wovenFabRecBal=$wovenReqQty-$wovenRecQty;
										$wovenIssueQty=$grey_cut_issue_arr[$po_id]["woven_issue"];
										$wovenFabIssueBal=$wovenRecQty-$wovenIssueQty;


										$cuttingQty=$garment_prod_data_arr[$po_id]['cutting_qnty'];
										if($finish_consumtion_arr[$po_id] !=0){
											$possible_cut_pcs=$issuedToCutQnty/$finish_consumtion_arr[$po_id];
										}
										else{
											$possible_cut_pcs = 0;
										}

										$cutting_process_loss=$possible_cut_pcs-$cuttingQty;

										$print_issue_qnty_in=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
										$print_issue_qnty_out=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
										$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
										$print_recv_qnty_in=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
										$print_recv_qnty_out=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
										$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;

										$emb_issue_qnty_in=$garment_prod_data_arr[$po_id]['emb_issue_qnty_in'];
										$emb_issue_qnty_out=$garment_prod_data_arr[$po_id]['emb_issue_qnty_out'];
										$emb_recv_qnty_in=$garment_prod_data_arr[$po_id]['emb_recv_qnty_in'];
										$emb_recv_qnty_out=$garment_prod_data_arr[$po_id]['emb_recv_qnty_out'];
										$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
										$total_emb_issue = $emb_issue_qnty_in + $emb_issue_qnty_out;


										$print_reject_qnty=$garment_prod_data_arr[$po_id]['print_reject_qnty']+$emb_reject_qnty;

										$sew_input_qnty_in=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
										$sew_input_qnty_out=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
										$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
										$sew_input_balance_qnty=$tot_po_qnty-$total_sew_input;

										$sew_recv_qnty_in=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
										$sew_recv_qnty_out=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
										$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;
										$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;
										$sew_reject_qnty=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];
										$cutting_reject_qnty=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

										$wash_recv_qnty_in=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
										$wash_recv_qnty_out=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
										$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
										$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

										$gmt_finish_in=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
										$gmt_finish_out=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
										$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
										$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
										$finish_reject_qnty=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
										$left_over_finish_gmts=$total_gmts_finish_qnty-$exfactory_qnty;

										$short_excess_exFactoryQty=$tot_po_qnty-$exfactory_qnty;

										$trims_recv=$trims_array[$po_id]['recv'];
										$trims_issue=$trims_array[$po_id]['iss'];
										$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

										$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
										$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;


										$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
										$total_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$finish_reject_qnty+$iron_rej_qnty+$print_reject_qnty;
										$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($total_reject_qnty,2).'</a></p>';
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
											<td width="40"><? echo $i; ?></td>
											<td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
											<td width="50" align="center"><p><? echo $val["job_year"]; ?></p></td>
											<td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?></p></td>
											<td width="100"><? echo $val["style_ref_no"]; ?></td>
											<td width="110"><?
											$fb_booking_nos=implode(",",array_unique(explode(",",$fb_booking_no)));
											if($txt_booking_no =="")
											{
												$book_nodata = $fb_booking_nos;
											}else{
												$book_nodata = $book_arr[$po_id]['booking_no'] ;
											}

											echo $book_nodata; ?></td>

											<td width="100"><? echo $val["po_number"]; ?></td>
											<td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty); ?></p></td>
											<td width="80"><p><? if(trim($val["pub_shipment_date"])!="" && trim($val["pub_shipment_date"])!='0000-00-00') echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</p></td>


											<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
											<td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>

											<td align="right" width="80"><? echo number_format($total_issued,2); ?></td>

											<td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>

											<td align="right" width="80" title="Total Yarn Issue-Total Kit Prod"><? $tot_knit_processLogssKg=$total_issued-$total_knitting; echo number_format($tot_knit_processLogssKg,2); ?></td>


											<td align="right" width="80"><? echo number_format($knit_gray_rec_inside,2) ?></td>
											<td align="right" width="80"><? echo $gray_iss_arr[$po_id]['PrQty'] ?></td>
											<td align="right" width="80"><? echo $knit_gray_rec_inside-$gray_iss_arr[$po_id]['PrQty'] ?></td>

											<td align="right" width="80"><a href="##" onClick="generate_batch_popup('Batch_Quantity_Popup','<? echo $val["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo $batch_qty ?></a></td>

											<td align="right" width="80">
												<? if($txt_booking_no=="")
													{
														$d = $finish_fabric_req_qnty;
													}else{
														$d= $book_arr[$po_id]['fin'];
													}
													echo number_format($d,2); 
												?>
											</td>

											<td align="right" width="80"><? echo $sql_fin_arr[$po_id]['RCVQNTY'] ?></td>
											<td align="right" width="80"><? echo $transfer_Arr[$po_id]['Trans_Qty'] ?></td>
											<td align="right" width="80"><? echo $cutting_rcv_arr[$po_id]['Cutt_Feb_Rcv']?></td>
											<td align="right" width="80"><? echo $cutting_del_arr[$po_id]['ISSUE_QNTY']?></td>
											<td align="right" width="80"><? echo $cutting_rcv_arr[$po_id]['Cutt_Feb_Rcv'] - $cutting_del_arr[$po_id]['ISSUE_QNTY']?></td>
											<td align="right" width="80"><? echo number_format($cons_arr_data[$po_id]['CONS']/$cons_arr_count[$po_id],2); ?></td>
											<td align="right" width="80"><? echo number_format($cuttingQty); ?></td>
											<td align="right" width="80"><? echo $rej_arr[$po_id]['REJECT_QNTY'] ?></td>
											<td align="right" width="80"><?
											$cutQry=$rej_arr[$po_id]['REJECT_QNTY']?($rej_arr[$po_id]['REJECT_QNTY']*100)/$cuttingQty:0;
											echo number_format($cutQry,2)."%"  ?></td>
											<td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
											<td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
											<td align="right" width="80"><? echo number_format($total_emb_issue); ?></td> 
											<td align="right" width="80"><? $tot_emb_recvQty=$emb_recv_qnty_in+$emb_recv_qnty_out; echo number_format($tot_emb_recvQty); ?></td>

											<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>




											<td align="right" width="80"><? echo number_format($total_sew_input); ?></td>

											<td align="right" width="80"><? echo $output_data_arr[$po_id]['PrDataQty']; ?></td>
											<td align="right" width="80"><? echo $Iron_data_arr[$po_id]['IronQty']; ?></td>
											<td align="right" width="80"><? echo $packing_data_arr[$po_id]['Packing_qty']; ?></td>

											<td align="right" width="80">
											<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  $ex_fect_arr[$po_id]['EX_FACTORY_QNTY'] ?></a>
											<?  ?></td>
											<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
											<?
												$left_over_con = ($left_over_finish_gmts/12)* ($cons_arr_data[$po_id]['CONS']/$cons_arr_count[$po_id]);
											?>
											<td align="right" width="80"><?= number_format($left_over_con,2);  ?></td>
										</tr>
										<?
										$tot_order_qty+=$tot_po_qnty;

										$tot_yarn_req_qty+=$yarn_required;
										$tot_yarn_issIn_qty+=$yarn_issue_inside;
										$tot_yarn_issOut_qty+=$yarn_issue_outside;

										$tot_yarn_issue_ret_inside+=$yarn_issue_ret_inside;
										$tot_yarn_issue_ret_outside+=$yarn_issue_ret_outside;
										$tot_yarn_issue_ret+=$yarn_issue_ret_inside+$yarn_issue_ret_outside;

										$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
										$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
										$tot_yarn_issue_qty+=$total_issued;
										$tot_yarn_undOvr_qty+=$under_over_issued;
										$tot_yarn_under_over_issued+=$yarn_under_over_issued;


										$tot_grey_req_qty+=$grey_fabric_req_qnty;
										$tot_grey_in_qty+=$knit_qnty_in;
										$tot_grey_out_qty+=$knit_qnty_out;
										$tot_grey_rec_qty+=$knit_gray_rec_inside;
										$tot_grey_rec_qty_outside+=$knit_gray_rec_outside;
										$tot_knit_under_over_prod_qty+=$knit_under_over_prod_qty;

										$tot_knit_process_lossKg_in+=$knit_process_lossKg_in;
										$tot_knit_processLossKgOut+=$knit_processLossKgOut;
										$tot_tot_knit_processLogssKg+=$tot_knit_processLogssKg;


										$tot_grey_rec_qty_in_trans+=$transfer_in_qnty_rec_knit;
										$tot_grey_rec_qty_out_trans+=$transfer_out_qnty_rec_knit;
										$tot_knit_rec_qty_total+=$tot_knit_rec_qty;

										$tot_issuedToDyeQnty_in+=$issuedToDyeQnty_in;
										$tot_issuedToDyeQnty_out+=$issuedToDyeQnty_out;
										$tot_tot_issuedToDyeQnty+=$tot_issuedToDyeQnty;

										$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
										$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
										$tot_grey_qty+=$total_knitting;
										$tot_grey_prLoss_qty+=$process_loss;
										$tot_grey_undOver_qty+=$under_over_prod;
										$tot_recv_under_over_prod+=$recv_under_over_prod;
										$tot_grey_issDye_qty+=$issuedToDyeQnty;
										$tot_grey_lftOver_qty+=$left_over;
										if($txt_booking_no=="")
										{
											$tot_fin_req_qty+=$finish_fabric_req_qnty;
										}else{
											$tot_fin_req_qty+=$book_arr[$po_id]['fin'];
										}
										$b_qty += $batch_qty ;
										$tot_fin_in_qty+=$finish_qnty_in;
										$tot_fin_out_qty+=$finish_qnty_out;
										$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
										$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
										$tot_fin_qty+=$total_finishing;
										$tot_fin_prLoss_qty+=$process_loss_finishing;
										$tot_fin_undOver_qty+=$under_over_finish_prod;
										$tot_fin_issCut_qty+=$issuedToCutQnty;
										$tot_fin_issRet_qty+=$knit_fin_return_qnty;
										$tot_fin_lftOver_qty+=$finish_left_over;

										$tot_grey_fabric_aop_req_wo_qnty+=$grey_fabric_aop_req_wo_qnty;
										$tot_aop_delivery_qty+=$aop_delivery_qty;
										$tot_aop_recv_qty+=$aop_aop_recv_qnty;
										$tot_aop_balance_qty+=$aop_balance_qty;

										$total_fin_prod_qty+=$tot_fin_prod_qty;
										$total_fin_prod_under_over_qty+=$fin_prod_under_over_qty;
										$total_fin_fab_process_loss_inside+=$fin_fab_process_loss_inside;
										$total_fin_fab_process_loss_outside+=$fin_fab_process_loss_outside;


										$total_tot_knit_process_loss+=$tot_knit_process_loss;

										$total_emb_issue_qnty_in+=$emb_issue_qnty_in;
										$total_emb_issue_qnty_out+=$emb_issue_qnty_out;
										$total_tot_emb_issueQty+=$tot_emb_issueQty;

										$total_emb_recv_qnty_in+=$emb_recv_qnty_in;
										$total_emb_recv_qnty_out+=$emb_recv_qnty_out;
										$total_tot_emb_recvQty+=$tot_emb_recvQty;

										$total_finish_qnty_in_rec_gmt+=$finish_qnty_in_rec_gmt;
										$total_finish_qnty_out_rec_gmt+=$finish_qnty_out_rec_gmt;

										$tot_wovenReqQty+=$wovenReqQty;
										$tot_wovenRecQty+=$wovenRecQty;
										$tot_wovenRecBalQty+=$wovenFabRecBal;
										$tot_wovenIssueQty+=$wovenIssueQty;
										$tot_wovenIssueBalQty+=$wovenFabIssueBal;

										$tot_gmt_qty+=$tot_po_plan_qnty;
										$tot_cutting_qty+=$cuttingQty;
										$tot_printIssIn_qty+=$print_issue_qnty_in;
										$tot_printIssOut_qty+=$print_issue_qnty_out;
										$tot_printIssue_qty+=$total_print_issued;
										$tot_printRcvIn_qty+=$print_recv_qnty_in;
										$tot_printRcvOut_qty+=$print_recv_qnty_out;
										$tot_printRcv_qty+=$total_print_recv;
										$tot_printRjt_qty+=$print_reject_qnty;

										$tot_sewInInput_qty+=$sew_input_qnty_in;
										$tot_sewInOutput_qty+=$sew_input_qnty_out;
										$tot_sewIn_qty+=$total_sew_input;
										$tot_sewInBal_qty+=$sew_input_balance_qnty;

										$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
										$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
										$tot_sewRcv_qty+=$total_sew_recv;
										$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
										$tot_sewRcvRjt_qty+=$sew_reject_qnty;

										$tot_washRcvIn_qty+=$wash_recv_qnty_in;
										$tot_washRcvOut_qty+=$wash_recv_qnty_out;
										$tot_washRcv_qty+=$total_wash_recv;
										$tot_washRcvBal_qty+=$wash_balance_qnty;

										$tot_gmtFinIn_qty+=$gmt_finish_in;
										$tot_gmtFinOut_qty+=$gmt_finish_out;
										$tot_gmtFin_qty+=$total_gmts_finish_qnty;
										$tot_gmtFinBal_qty+=$finish_balance_qnty;
										$tot_gmtFinRjt_qty+=$finish_reject_qnty;
										$tot_gmtEx_qty+=$ex_fect_arr[$po_id]['EX_FACTORY_QNTY'];
										$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

										$tot_shortExcess_exFactory_qty+=$short_excess_exFactoryQty;
										$tot_prLoss_qty+=$process_loss;
										$tot_prLossDye_qty+=$process_loss_dyeing;
										$tot_prLossCut_qty+=$cutting_process_loss;
										$tot_process_loss_yern_per += $process_loss_yern_per;
										$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
										$tot_process_loss_cutting_per += $process_loss_cutting_per;
										$i++;
										$gray_sub_total += $gray_iss_arr[$po_id]['PrQty'];
										$gray_blnce_total += $knit_gray_rec_inside-$gray_iss_arr[$po_id]['PrQty'] ;
										$total_feb_trans += $transfer_Arr[$po_id]['Trans_Qty'] ;
										$total_rv += $sql_fin_arr[$po_id]['RCVQNTY'] ;
										$total_cutting_feb_recv += $cutting_rcv_arr[$po_id]['Cutt_Feb_Rcv'] ;
										$total_del_issue_qty += $cutting_del_arr[$po_id]['ISSUE_QNTY'] ;
										$total_feb_blnc += $cutting_rcv_arr[$po_id]['Cutt_Feb_Rcv'] - $cutting_del_arr[$po_id]['ISSUE_QNTY'] ;
										$total_rej_qty += $rej_arr[$po_id]['REJECT_QNTY'] ;
										$total_rej_gmt_qty +=  $gmt_arr[$po_id]['REJECT_Gmt_QNTY'] ;
										$gmt_qt += $rcv_qty_arr[$po_id]['OrderQty'] ;
										$total_sew_total += $output_data_arr[$po_id]['PrDataQty'] ;
										$total_fin_iron += $Iron_data_arr[$po_id]['IronQty'] ;
										$total_packing_qty += $packing_data_arr[$po_id]['Packing_qty'] ;
									}
								}

							?>
						</tbody>
					</table>
				</div>
				<div style="width:<?= $width+20;?>px;float:left;">
					<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
						<tfoot>		
							<tr>
								<th width="40">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="100"></th>
								<th width="110" align="right">Total :</th>

								<?
									if($cbo_search_type==1)
									{
										?>
										<th width="100">&nbsp;</th>
										<?
									}
								?>
								<th width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></th>
								<?
									if($cbo_search_type==1)
									{
										?>
										<th width="80">&nbsp;</th>

										<?
									}
								?>
								<th width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></th>
								<th width="80" align="right" title="<? echo $tot_grey_req_qty;?>" id="td_grey_req_qty"><? echo number_format($tot_grey_req_qty,2); ?></th>


								<th width="80" align="right" id="tot_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></th>
								<th width="80" align="right" id="td_grey_qty"><? echo number_format($tot_grey_qty,2); ?></th>

								<th width="80" align="right" id="td_knit_processLogssKg"><? echo number_format($tot_tot_knit_processLogssKg,2); ?></th>
								<th width="80" align="right" id="td_grey_rec_qty"><? echo number_format($tot_grey_rec_qty,2); ?></th>
								<th width="80" align="right" id="td_gray_sub_total"><? echo number_format($gray_sub_total,2); ?></th>
								<th width="80" align="right" id="td_gray_blnce_total"><? echo number_format($gray_blnce_total,2); ?></th>
								<th width="80" align="right" id="td_b_qty"><? echo $b_qty ?></th>
								<th width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></th>
								<th width="80" align="right" id="td_total_rv"><? echo $total_rv ?></th>
								<th width="80" align="right" id="td_total_feb_trans"><? echo number_format($total_feb_trans,2); ?></th>
								<th width="80" align="right" id="td_total_cutting_feb_recv"><? echo number_format($total_cutting_feb_recv,2); ?></th>
								<th width="80" align="right" id="td_total_del_issue_qty"><? echo number_format($total_del_issue_qty,2); ?></th>
								<th width="80" align="right" id="td_total_feb_blnc"><? echo number_format($total_feb_blnc,2); ?></th>
								<th width="80" align="right" id=""></th> 

								<th width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cutting_qty); ?></th>
								<th width="80" align="right" id="td_total_rej_qty"><? echo number_format($total_rej_qty,2); ?></th>
								<th width="80" align="right"></th>
								<th width="80" align="right" id="td_printIssue_qty"><? echo number_format($tot_printIssue_qty); ?></th>
								<th width="80" align="right" id="td_gmt_qt"><? echo number_format($gmt_qt) ?></th>
								<th width="80" align="right" id="td_total_tot_emb_recvQty"><? echo number_format($total_tot_emb_recvQty); ?></th>
								<th width="80" align="right" id="td_total_rej_gmt_qty"><? echo number_format($total_rej_gmt_qty,2) ?></th>

								<th width="80" align="right" id="td_printRjt_qty"><? echo number_format($tot_printRjt_qty); ?></th>

								<th width="80" align="right" id="td_sewIn_qty"><? echo number_format($tot_sewIn_qty); ?></th>
								<th width="80" align="right" id="td_total_sew_total"><? echo $total_sew_total ?></th>
								<th width="80" align="right" id="td_total_fin_iron"><? echo $total_fin_iron ?></th>
								<th width="80" align="right" id="td_total_packing_qty"><? echo $total_packing_qty ?></th>

								<th width="80" align="right" id="td_gmtEx_qty"><? echo number_format($tot_gmtEx_qty); ?></th>
								<th width="80" align="right" id="td_gmtFinLeftOver_qty"><? echo number_format($tot_gmtFinLeftOver_qty); ?></th>
								<th width="80" align="right" ></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div> 
        </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$cbo_search_type**$type";
    exit();
}
if($action=="Batch_Quantity_Popup")
{
	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	/*echo $from.'_'.$to;//$job_no;
	die;*/
	//echo $cbo_date_category;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Batch Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Batch. Date</th>
                        <th width="120">Batch no</th>
                        <th width="100">Batch Qnty.</th>


                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
               <?php
			   $batchDataArr=sql_select("SELECT SUM(b.batch_qnty) AS batch_qty, a.batch_no, a.batch_date
			   FROM pro_batch_create_mst a, pro_batch_create_dtls b
			   WHERE     a.id = b.mst_id
				  AND a.batch_against NOT IN (2)
				  AND a.entry_form = 0
				  AND b.status_active = 1
				  AND b.is_deleted = 0
				  AND a.status_active = 1
				  AND a.is_deleted = 0
				  AND b.po_id IN ($id)
			   GROUP BY a.batch_no, a.batch_date");
			  // echo $batchDataArr ;

				   //$batch_qty=$batchDataArr[0][csf('batch_qty')];


                foreach($batchDataArr as $row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35" align="center"><? echo $i; ?></td>
                        <td width="90"align="center"><? echo change_date_format($row[csf("batch_date")]); ?></td>
                        <td width="120"align="center"><? echo $row[csf("batch_no")]; ?></td>
                        <td width="100" align="right"><? echo $row[csf("batch_qty")]; ?></td>

                    </tr>
                    <?

                }
                ?>

            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}
if($action=='job_report_generate_new')
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$ref_no=str_replace("'","",$txt_ref_no);
	$file_no=str_replace("'","",$txt_file_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_ex_date_form=str_replace("'","",$txt_ex_date_form);
	$txt_ex_date_to=str_replace("'","",$txt_ex_date_to);
	$type=str_replace("'","",$type);
	$cbo_date_category=str_replace("'","",$cbo_date_category);

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	/*$ship_date_cond="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	$ex_fact_date_cond="";
	if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	{
		$ex_fact_date_cond="and c.ex_factory_date between '$txt_ex_date_form' and '$txt_ex_date_to'  AND c.is_deleted = 0 AND c.status_active = 1";
	}*/
	$ship_date_cond="";
	if($cbo_date_category==1)
	{

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	}
	else if($cbo_date_category==2)
	{
		//$ex_fact_date_cond="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and c.ex_factory_date between '$txt_date_from' and '$txt_date_to' AND c.is_deleted = 0 AND c.status_active = 1";
		}
	}
	/*else if($cbo_date_category==3) //Ref Closing date
	{

		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and d.closing_date between '$txt_date_from' and '$txt_date_to'";
		}
	}*/

	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
	$order_cond="";
	if(trim($ref_no)!="") $ref_no_cond="and b.grouping in('$ref_no')";
	if(trim($file_no)!="") $ref_no_cond="and b.file_no in('$file_no')";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	//echo $txt_order_id;die;
	if($txt_order_id!="")
	{
		$order_cond="and b.id in($txt_order_id)";
	}
	else
	{
		if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'"; else $order_cond="";
	}
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	if($cbo_date_category==2)//Ex-factory date
	{
		$sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
		where a.job_no=b.job_no_mst and  b.id=c.po_break_down_id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else
	{
		 $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1
		where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	//echo  $sql_po;//die;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$result_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["ref_no"]=$row[csf("grouping")];
		$result_data_arr[$row[csf("po_id")]]["file_no"]=$row[csf("file_no")];
		$result_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("po_id")]]["po_qnty"]=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")];
		$result_data_arr[$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
		$JobArr[]="'".$row[csf('job_no')]."'";
		$job_no=$row[csf('job_no')];
	}
	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	//$JobNoArr=implode(",",$JobArr);
	//$yarn= new yarn($JobArr,'job');
	//$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	//print_r($yarn_qty_arr);
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(!empty($JobArr)){
	 $condition->po_id_in("$all_po_id");
	}
	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	//$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
	$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();


	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
	$po_cond_for_in=" and (";
	$po_cond_for_in2=" and (";
	$po_cond_for_in3=" and (";

	$poIdsArr=array_chunk(explode(",",$poIds),999);
	foreach($poIdsArr as $ids)
	{
	$ids=implode(",",$ids);
	$po_cond_for_in.=" b.po_break_down_id in($ids) or";
	$po_cond_for_in2.=" a.po_breakdown_id in($ids) or";
	$po_cond_for_in3.=" b.order_id in($ids) or";

	}
	$po_cond_for_in=chop($po_cond_for_in,'or ');
	$po_cond_for_in.=")";
	$po_cond_for_in2=chop($po_cond_for_in2,'or ');
	$po_cond_for_in2.=")";
	$po_cond_for_in3=chop($po_cond_for_in3,'or ');
	$po_cond_for_in3.=")";

	}
	else
	{
	$po_cond_for_in=" and b.po_break_down_id in($poIds)";
	$po_cond_for_in2=" and a.po_breakdown_id  in($poIds)";
	$po_cond_for_in3=" and b.order_id in($poIds)";

	}
			if($db_type==2)
			{
				$col_grp="listagg(CAST(a.booking_no as VARCHAR(4000)),',') within group (order by a.booking_no) as booking_no";
			}
			else
			{
				$col_grp="group_concat(a.booking_no) as booking_no";
			}
	$booking_req_arr=array();
	$sql_wo=sql_select("select $col_grp,a.booking_type,b.po_break_down_id,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	sum(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	sum(b.fin_fab_qnty) as fin_fab_qnty,
	sum(CASE WHEN a.item_category=12 and a.process=35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty
	from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in group by a.booking_type,b.po_break_down_id");

	//finish_prod_arr
	foreach ($sql_wo as $brow)
	{
		if($brow[csf("grey_req_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['gray']=$brow[csf("grey_req_qnty")];
		}
		if($brow[csf("woven_req_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['woven']=$brow[csf("woven_req_qnty")];
		}
		if($brow[csf("fin_fab_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['fin']=$brow[csf("fin_fab_qnty")];
		}
		if($brow[csf("aop_wo_qnty")]>0)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['aop_wo_qnty']=$brow[csf("aop_wo_qnty")];
		}
		if($brow[csf("booking_type")]==1)
		{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['booking_no']=$brow[csf("booking_no")];
		}
	}
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('return_qnty')];
		}

	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$dataArrayYarnReq=array();
	$yarn_sql="select job_no, sum(avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarnReq[$yarnRow[csf('job_no')]]=$yarnRow[csf('qnty')];
	}

	$reqDataArray=sql_select("select  a.po_break_down_id, sum((b.requirment/b.pcs)*a.plan_cut_qnty) as grey_req, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1  $po_cond_for_in group by a.po_break_down_id");

	$grey_finish_require_arr=array();
	foreach($reqDataArray as $row)
	{
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["grey_req"]=$row[csf("grey_req")];
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["finish_req"]=$row[csf("finish_req")];
	}

	$yarnDataArr=sql_select("select a.po_breakdown_id,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_issue_master c
						where a.trans_id=b.id and b.mst_id=c.id   and b.item_category=1 and c.issue_purpose in(1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2
						group by a.po_breakdown_id");
	$yarn_issue_arr=array();
	foreach($yarnDataArr as $row)
	{
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_in"]=$row[csf("issue_qnty_in")];
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_out"]=$row[csf("issue_qnty_out")];
	}

	$yarnReturnDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
				sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
				from order_wise_pro_details a, inv_transaction b, inv_receive_master c
				where a.trans_id=b.id and b.mst_id=c.id  and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2
				group by a.po_breakdown_id");


	$yarn_issue_rtn_arr=array();
	foreach($yarnReturnDataArr as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
	}


	$dataArrayTrans=sql_select("select po_breakdown_id,
								sum(CASE WHEN a.entry_form ='11' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN a.entry_form ='11' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN a.entry_form ='13' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN a.entry_form ='13' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN a.entry_form in(14,15) and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_finish,
								sum(CASE WHEN a.entry_form in(14,15) and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_finish,
								sum(CASE WHEN a.entry_form ='14' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_rec_knit2,
								sum(CASE WHEN a.entry_form ='14' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_rec_knit2,
								sum(CASE WHEN a.entry_form in(82,183,110) and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_rec_knit,
								sum(CASE WHEN a.entry_form in(82,183,110) and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_rec_knit

							from order_wise_pro_details a where a.status_active=1 and a.is_deleted=0 and a.entry_form in(11,13,15,14,82,183,110)
							 $po_cond_for_in2 group by a.po_breakdown_id");



	$transfer_data_arr=array();
	foreach($dataArrayTrans as $row)
	{
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_yarn"]=$row[csf("transfer_in_qnty_yarn")];//transfer_in_qnty_rec_knit
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_yarn"]=$row[csf("transfer_out_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_knit"]=$row[csf("transfer_in_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_knit"]=$row[csf("transfer_out_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_finish"]=$row[csf("transfer_in_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_finish"]=$row[csf("transfer_out_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_rec_knit"]=$row[csf("transfer_in_qnty_rec_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_rec_knit"]=$row[csf("transfer_out_qnty_rec_knit")];
	}

	// decision pending	dyed yearn receive
	//$greyYarnIssueQnty=return_library_array("select c.po_breakdown_id, sum(c.quantity) as issue_qnty from inv_transaction a, inv_issue_master b,  order_wise_pro_details c where a.mst_id=b.id and a.id=c.trans_id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and c.po_breakdown_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2 group by c.po_breakdown_id","po_breakdown_id","issue_qnty");


	//$dyedYarnRecvQnty=return_field_value("sum(a.cons_quantity) as recv_qnty","inv_transaction a, inv_receive_master b","a.mst_id=b.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1","recv_qnty");

	$prodKnitDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
				sum(CASE WHEN a.entry_form in(58,22) and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_inside,
				sum(CASE WHEN a.entry_form in(58,22) and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_outside
				from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category=13 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 group by a.po_breakdown_id");// and c.receive_basis<>9

	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_in"]=$row[csf("knit_qnty_in")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_out"]=$row[csf("knit_qnty_out")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec_inside"]=$row[csf("knit_qnty_rec_inside")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec_outside"]=$row[csf("knit_qnty_rec_outside")];
	}

	$prodFinDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
				sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_in_rec_gmt,
				sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_out_rec_gmt,
				sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec
				from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $po_cond_for_in2 group by a.po_breakdown_id");



	$finish_prod_arr=array();
	foreach($prodFinDataArr as $row)
	{
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in"]=$row[csf("finish_qnty_in")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out"]=$row[csf("finish_qnty_out")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in_rec_gmt"]=$row[csf("finish_qnty_in_rec_gmt")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out_rec_gmt"]=$row[csf("finish_qnty_out_rec_gmt")];

		$finish_prod_arr[$row[csf("po_breakdown_id")]]["woven_rec"]=$row[csf("woven_rec")];
	}//c.knit_dye_source
	$issueData=sql_select("select po_breakdown_id,
							sum(CASE WHEN a.entry_form=16 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_in_qnty,
							sum(CASE WHEN a.entry_form=16  and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_out_qnty,
							sum(CASE WHEN a.entry_form=61 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_out,
							sum(CASE WHEN a.entry_form=61 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_in,
							sum(CASE WHEN a.entry_form=18 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty,
							sum(CASE WHEN a.entry_form=71 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
							sum(CASE WHEN a.entry_form=19 THEN a.quantity ELSE 0 END) AS woven_issue
							from order_wise_pro_details a,inv_grey_fabric_issue_dtls b,inv_issue_master
	c where a.dtls_id=b.id and b.mst_id=c.id  and a.entry_form in(16,18,19,61,71) and a.status_active=1 and a.is_deleted=0 $po_cond_for_in2 group by a.po_breakdown_id");


	$grey_cut_issue_arr=array();
	foreach($issueData as $row)
	{
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty_in"]=$row[csf("grey_issue_in_qnty")]+$row[csf("grey_issue_qnty_roll_wise_in")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty_out"]=$row[csf("grey_issue_out_qnty")]+$row[csf("grey_issue_qnty_roll_wise_out")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["issue_to_cut_qnty"]=$row[csf("issue_to_cut_qnty")]+$row[csf("issue_to_cut_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["woven_issue"]=$row[csf("woven_issue")];
	}
	$trimsDataArr=sql_select("select a.po_breakdown_id,
							sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
							sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
							from order_wise_pro_details a, product_details_master b where a.prod_id=b.id  and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 group by a.po_breakdown_id");
	foreach($trimsDataArr as $row)
	{
		$trims_array[$row[csf('po_breakdown_id')]]['recv']=$row[csf('recv_qnty')];
		$trims_array[$row[csf('po_breakdown_id')]]['iss']=$row[csf('issue_qnty')];
	}
	$issuetoprocessDataArr=sql_select("select b.order_id,

							sum(CASE WHEN a.entry_form=91 THEN b.batch_issue_qty ELSE 0 END) AS batch_issue_qty,
							sum(CASE WHEN a.entry_form=92 THEN b.batch_issue_qty ELSE 0 END) AS aop_recv_qnty
							from inv_receive_mas_batchroll a,pro_grey_batch_dtls b where  a.id=b.mst_id  and  a.entry_form in(91,92)  and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in3 group by b.order_id");


	foreach($issuetoprocessDataArr as $row)
	{
		$aop_delivery_array[$row[csf('order_id')]]['batch_issue_qty']=$row[csf('batch_issue_qty')];
		$aop_delivery_array[$row[csf('order_id')]]['aop_recv_qnty']=$row[csf('aop_recv_qnty')];

	}

	$sql_consumtiont_qty=sql_select("select b.po_break_down_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0  $po_cond_for_in group by b.po_break_down_id, c.body_part_id ");
			$finish_consumtion_arr=array();
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg=0;
				$con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];///str_replace("'","",$row_sew[csf("pcs")]);
				$finish_consumtion_arr[$row_consum[csf('po_break_down_id')]]+=$con_avg;
			}

	$gmtsProdDataArr=sql_select("select  b.po_break_down_id,
					sum(CASE WHEN b.production_type=1 THEN b.production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN b.production_type=2 and b.embel_name=1 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS print_issue_qnty_in,
					sum(CASE WHEN b.production_type=2 and b.embel_name=1 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS print_issue_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS print_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS print_recv_qnty_out,
					sum(CASE WHEN b.production_type=2 and b.embel_name=2 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS emb_issue_qnty_in,
					sum(CASE WHEN b.production_type=2 and b.embel_name=2 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS emb_issue_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS emb_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS emb_recv_qnty_out,
					sum(CASE WHEN b.production_type=4 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS sew_input_qnty_in,
					sum(CASE WHEN b.production_type=4 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS sew_input_qnty_out,
					sum(CASE WHEN b.production_type=5 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS sew_recv_qnty_in,
					sum(CASE WHEN b.production_type=5 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS sew_recv_qnty_out,
					sum(CASE WHEN b.production_type=8 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS finish_qnty_in,
					sum(CASE WHEN b.production_type=8 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS finish_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=3 and b.production_source=1 THEN b.production_quantity ELSE 0 END) AS wash_recv_qnty_in,
					sum(CASE WHEN b.production_type=3 and b.embel_name=3 and b.production_source=3 THEN b.production_quantity ELSE 0 END) AS wash_recv_qnty_out,
					sum(CASE WHEN b.production_type=3 and b.embel_name=1 THEN b.reject_qnty ELSE 0 END) AS print_reject_qnty,
					sum(CASE WHEN b.production_type=3 and b.embel_name=2 THEN b.reject_qnty ELSE 0 END) AS emb_reject_qnty,
					sum(CASE WHEN b.production_type=5 THEN b.reject_qnty ELSE 0 END) AS sew_reject_qnty,
					sum(CASE WHEN b.production_type=8 THEN b.reject_qnty ELSE 0 END) AS finish_reject_qnty,
					sum(CASE WHEN b.production_type=1 THEN b.reject_qnty ELSE 0 END) AS cutting_reject_qnty,
					sum(CASE WHEN b.production_type=7 THEN b.reject_qnty ELSE 0 END) AS iron_rej_qnty
					from pro_garments_production_mst b where  b.is_deleted=0 and b.status_active=1 $po_cond_for_in group by b.po_break_down_id");


	$garment_prod_data_arr=array();
	foreach($gmtsProdDataArr as $row)
	{
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_qnty']=$row[csf("cutting_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_out']=$row[csf("finish_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_in']=$row[csf("wash_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_out']=$row[csf("wash_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_reject_qnty']=$row[csf("print_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_reject_qnty']=$row[csf("emb_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_reject_qnty']=$row[csf("sew_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_reject_qnty']=$row[csf("finish_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_reject_qnty']=$row[csf("cutting_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['iron_rej_qnty']=$row[csf("iron_rej_qnty")];
	}
	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	if($cbo_search_type==1)
	{
		$tbl_width=3630;

		$ship_date_html="Shipment Date";
		$ex_fact_date_html="Ex-Fact. Date";
	}
	else
	{
		$tbl_width=3630;
		$ship_date_html="Last Shipment Date";
		$ex_fact_date_html="Last Ex-Fact. Date";
	}
	ob_start();
	?>
        <div style="width:100%">
             <table width="<? echo $tbl_width;?>">
                <tr>
                    <td align="center" width="100%" colspan="69" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
            </table>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                	<tr style="font-size:13px">
                         <th width="40" >SL</th>
                        <th width="110" >Buyer</th>
                        <th width="50" >Job No</th>
                        <th width="100" >Style No</th>
                     	<th width="100" >Order No</th>

                        <th width="100" >File No</th>
                        <th width="80" >Order Qty.<br/> (Pcs)</th>
                        <th width="70" ><? echo $ship_date_html; ?></th>
                        <th width="70"><? echo $ex_fact_date_html; ?></th>
                        <th width="80">Yarn Req.<br/><font style="font-size:9px;color: red; font-weight:100">(As Per Pre-Cost)</font></th>

                        <th width="80">Yarn Total <br/>Issued</th>
                        <th width="90">Yarn Under <br/>Or<br/> Over Issued</th>
                        <th width="80">Knit. Gray <br/>Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Knit. Total <br/>Prod.</th>
                        <th width="80">Knit. Process <br/>Loss</th>

                       <th width="80">Knit. Under <br/>Or<br/> Over Prod.</th>
                        <th width="80">Knit. Issued <br/>To Dyeing</th>
                        <th width="80">Knit. Left <br/>Over</th>
                        <th width="80">Fin Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>
                        <th width="80">Fin Prod. <br/>Total</th>

                        <th width="80">Fin Process <br/>Loss</th>
                        <th width="80">Fin Under <br/>Or<br/> Over</th>
                        <th width="80">Fin Issue <br/>To Cut</th>
                        <th width="80">Fin Left Over</th>
                        <th width="80">Gmts. Req <br/>(Po Qty)</th>

                        <th width="80">Cutting Qty</th>
                        <th width="80">Gmts. Total <br/>Print Issued</th>
                        <th width="80">Gmts. Total <br/>Rec. Print</th>
                        <th width="80">Gmts. Reject</th>
                        <th width="80">Total Sew. <br/>Input</th>

                        <th width="80">Sew. Input <br/>Balance</th>
                        <th width="100">Accessories <br/>Status</th>
                        <th width="80">Total Out <br/>Sew</th>
                        <th width="80">Sew Out <br/>Balance</th>
                        <th width="80">Sew Out <br/>Reject</th>

                        <th width="80">Total Finish</th>
                        <th width="80">Finish <br/>Balance</th>
                        <th width="80">Finish Reject</th>
                        <th width="80">Total Reject</th>
                        <th width="80">Ex-Factory</th>

                        <th width="80">Left Over</th>
                        <th width="80">Short Ex-Fac. Qty</th>
                        <th width="80">Process Loss Yarn</th>
                        <th width="80">Process Loss Dyeing</th>
                        <th width="80">Process Loss Cutting</th>

                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">

                <?
					$i=1;$tot_grey_rec_qty=$tot_grey_req_qty=0;
					if($cbo_search_type==1)
					{
						foreach($result_data_arr as $po_id=>$val)
						{
							$ratio=$val["ratio"];
							$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
							$tot_po_qnty=$val["po_qnty"];
							$exfactory_qnty=$val["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
							$plan_cut_qty=$val["plan_cut"];
							$job_no=$val["job_no"];
							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$ratio;

							$yarn_req_job=$yarn_qty_arr[$po_id];//$dataArrayYarnReq[$job_no];
							$yarn_required=$yarn_qty_arr[$po_id];//$plan_cut_qty*($yarn_req_job/$dzn_qnty);
							$yarn_issue_inside=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
							$yarn_issue_outside=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
							$transfer_in_qnty_yarn=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
							$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
							$under_over_issued=$yarn_required-$total_issued;

							$grey_fabric_req_qnty=$booking_req_arr[$po_id]['gray'];
							$grey_fabric_aop_req_wo_qnty=$booking_req_arr[$po_id]['aop_wo_qnty'];//$booking_req_arr[$brow[csf("po_break_down_id")]]['aop_wo_qnty']
							//$grey_finish_require_arr[$po_id]["grey_req"];
							$aop_delivery_qty=$aop_delivery_array[$po_id]['batch_issue_qty'];
							$aop_aop_recv_qnty=$aop_delivery_array[$po_id]['aop_recv_qnty'];

							$knit_qnty_in=$kniting_prod_arr[$po_id]["knit_qnty_in"];
							$knit_qnty_out=$kniting_prod_arr[$po_id]["knit_qnty_out"];
							$knit_gray_rec_inside=$kniting_prod_arr[$po_id]["knit_qnty_rec_inside"];
							$knit_gray_rec_outside=$kniting_prod_arr[$po_id]["knit_qnty_rec_outside"];

							$transfer_in_qnty_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];

							$transfer_in_qnty_rec_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_rec_knit"];

							$total_knitting=$knit_qnty_in+$knit_qnty_out;//+$transfer_in_qnty_knit-$transfer_out_qnty_knit;
							$process_loss=($yarn_issue_inside+$yarn_issue_outside)-($knit_qnty_in+$knit_qnty_out);
							$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
							$issuedToDyeQnty_in=$grey_cut_issue_arr[$po_id]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out=$grey_cut_issue_arr[$po_id]["grey_issue_qnty_out"];

							$tot_knit_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;

							$left_over=$tot_knit_rec_qty-($issuedToDyeQnty_in+$issuedToDyeQnty_out);


							$finish_fabric_req_qnty=$booking_req_arr[$po_id]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							$finish_qnty_in=$finish_prod_arr[$po_id]["finish_qnty_in"];
							$finish_qnty_out=$finish_prod_arr[$po_id]["finish_qnty_out"];

							$finish_qnty_in_rec_gmt=$finish_prod_arr[$po_id]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt=$finish_prod_arr[$po_id]["finish_qnty_out_rec_gmt"];

							$transfer_in_qnty_finish=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];

							$total_finishing=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;

							$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);

							$issuedToCutQnty=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];
							$finish_left_over=$total_finishing-$issuedToCutQnty;

							$wovenReqQty=$booking_req_arr[$po_id]['woven'];
							$wovenRecQty=$finish_prod_arr[$po_id]["woven_rec"];
							$fb_booking_no=$booking_req_arr[$po_id]['booking_no'];
							$wovenFabRecBal=$wovenReqQty-$wovenRecQty;
							$wovenIssueQty=$grey_cut_issue_arr[$po_id]["woven_issue"];
							$wovenFabIssueBal=$wovenRecQty-$wovenIssueQty;


							$cuttingQty=$garment_prod_data_arr[$po_id]['cutting_qnty'];
							if($finish_consumtion_arr[$po_id] !=0){
								$possible_cut_pcs=$issuedToCutQnty/$finish_consumtion_arr[$po_id];
							}
							else{
								$possible_cut_pcs = 0;
							}

							$cutting_process_loss=$possible_cut_pcs-$cuttingQty;

							$print_issue_qnty_in=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
							$print_issue_qnty_out=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
							$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
							$print_recv_qnty_in=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
							$print_recv_qnty_out=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
							$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;

							$emb_issue_qnty_in=$garment_prod_data_arr[$po_id]['emb_issue_qnty_in'];
							$emb_issue_qnty_out=$garment_prod_data_arr[$po_id]['emb_issue_qnty_out'];
							$emb_recv_qnty_in=$garment_prod_data_arr[$po_id]['emb_recv_qnty_in'];
							$emb_recv_qnty_out=$garment_prod_data_arr[$po_id]['emb_recv_qnty_out'];
							$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];



							$print_reject_qnty=$garment_prod_data_arr[$po_id]['print_reject_qnty']+$emb_reject_qnty;

							$sew_input_qnty_in=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
							$sew_input_qnty_out=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
							$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
							$sew_input_balance_qnty=$tot_po_qnty-$total_sew_input;

							$sew_recv_qnty_in=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
							$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;
							$sew_reject_qnty=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];
							$cutting_reject_qnty=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

							$wash_recv_qnty_in=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
							$wash_recv_qnty_out=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
							$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
							$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

							$gmt_finish_in=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
							$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
							$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
							$finish_reject_qnty=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
							$left_over_finish_gmts=$total_gmts_finish_qnty-$exfactory_qnty;

							$short_excess_exFactoryQty=$tot_po_qnty-$exfactory_qnty;

							$trims_recv=$trims_array[$po_id]['recv'];
							$trims_issue=$trims_array[$po_id]['iss'];
							$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

							$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
							$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;


							$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
							$total_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$finish_reject_qnty+$iron_rej_qnty+$print_reject_qnty;
							$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($total_reject_qnty,2).'</a></p>';
							?>
				<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">

								 <td width="40"><? echo $i; ?></td>
                                                <td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                                                <td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
                                                <td width="100"><? echo $val["style_ref_no"]; ?></td>
                                                <td width="100"><? echo $val["po_number"]; ?></td>


                                                <td width="100"><? echo $val["file_no"]; ?></td>
                                                <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty); ?></p></td>
                                                <td width="70"><p><? if(trim($val["pub_shipment_date"])!="" && trim($val["pub_shipment_date"])!='0000-00-00') echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</p></td>
                                                <td width="70"><p><? if(trim($val["ex_factory_date"])!="" && trim($val["ex_factory_date"])!='0000-00-00') echo change_date_format($val["ex_factory_date"]); ?>&nbsp;</p></td>
                                                <td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>



                                                <td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
                                                <td align="right" width="90"><? echo number_format($under_over_issued,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>

                                                <td align="right" width="80"><? echo number_format($process_loss,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($under_over_prod,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($issuedToDyeQnty,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($left_over,2); ?></td>

                                                <td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>


                                                <td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($process_loss_finishing,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($issuedToCutQnty,2); ?></td>
                                                <td align="right" width="80"><? echo number_format($finish_left_over,2); ?></td>



                                                <td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty); ?></td>
                                				<td align="right" width="80"><? echo number_format($cuttingQty); ?></td>

                                                <td align="right" width="80"><? echo number_format($total_print_issued); ?></td>

                                                <td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
                                                <td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>


                                                <td align="right" width="80"><? echo number_format($total_sew_input); ?></td>
                                                <td align="right" width="80"><? echo number_format($sew_input_balance_qnty); ?></td>

                                                <td align="center" width="100"><a href="javascript:open_trims_dtls('<? echo $po_id;?>','<? echo $tot_po_qnty; ?>','<? echo $ratio; ?>','Trims Info','trims_popup')">View</a></td>


                                                <td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
                                                <td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
                                                <td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>




                                                <td align="right" width="80"><? echo number_format($total_gmts_finish_qnty); ?></td>
                                                <td align="right" width="80"><? echo number_format($finish_balance_qnty); ?></td>
                                                <td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>
                                                <td align="right" width="80"><? echo $reject_button; ?></td>

                                                <td align="right" width="80">
                                                <a href="##" onClick="generate_ex_factory_popup_show4('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px','<? echo $txt_ex_date_form; ?>','<? echo $txt_ex_date_to; ?>')"><? echo  number_format($exfactory_qnty); ?></a>
                                                <? //echo number_format($exfactory_qnty); ?></td>
                                                <td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
                                                <td align="right" width="80"><? echo number_format($short_excess_exFactoryQty); ?></td>
                                                <td align="right" width="80"><? echo number_format($process_loss); ?></td>

                                                <td align="right" width="80"><? echo number_format($process_loss_dyeing); ?></td>

                                                <td align="right" width="80"><? echo number_format($cutting_process_loss); ?></td>


							</tr>
							<?
							$tot_order_qty+=$tot_po_qnty;

							$tot_yarn_req_qty+=$yarn_required;
							$tot_yarn_issIn_qty+=$yarn_issue_inside;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;
							$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
							$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
							$tot_yarn_issue_qty+=$total_issued;
							$tot_yarn_undOvr_qty+=$under_over_issued;
							$tot_yarn_under_over_issued+=$yarn_under_over_issued;


							$tot_grey_req_qty+=$grey_fabric_req_qnty;
							$tot_grey_in_qty+=$knit_qnty_in;
							$tot_grey_out_qty+=$knit_qnty_out;
							$tot_grey_rec_qty+=$knit_gray_rec_inside;
							$tot_grey_rec_qty_outside+=$knit_gray_rec_outside;
							$tot_knit_under_over_prod_qty+=$knit_under_over_prod_qty;

							$tot_knit_process_lossKg_in+=$knit_process_lossKg_in;
							$tot_knit_processLossKgOut+=$knit_processLossKgOut;
							$tot_tot_knit_processLogssKg+=$tot_knit_processLogssKg;


							$tot_grey_rec_qty_in_trans+=$transfer_in_qnty_rec_knit;
							$tot_grey_rec_qty_out_trans+=$transfer_out_qnty_rec_knit;
							$tot_knit_rec_qty_total+=$tot_knit_rec_qty;

							$tot_issuedToDyeQnty_in+=$issuedToDyeQnty_in;
							$tot_issuedToDyeQnty_out+=$issuedToDyeQnty_out;
							$tot_tot_issuedToDyeQnty+=$tot_issuedToDyeQnty;

							$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
							$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
							$tot_grey_qty+=$total_knitting;
							$tot_grey_prLoss_qty+=$process_loss;
							$tot_grey_undOver_qty+=$under_over_prod;
							$tot_recv_under_over_prod+=$recv_under_over_prod;
							$tot_grey_issDye_qty+=$issuedToDyeQnty;
							$tot_grey_lftOver_qty+=$left_over;

							$tot_fin_req_qty+=$finish_fabric_req_qnty;
							$tot_fin_in_qty+=$finish_qnty_in;
							$tot_fin_out_qty+=$finish_qnty_out;
							$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
							$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
							$tot_fin_qty+=$total_finishing;
							$tot_fin_prLoss_qty+=$process_loss_finishing;
							$tot_fin_undOver_qty+=$under_over_finish_prod;
							$tot_fin_issCut_qty+=$issuedToCutQnty;
							$tot_fin_lftOver_qty+=$finish_left_over;

							$tot_grey_fabric_aop_req_wo_qnty+=$grey_fabric_aop_req_wo_qnty;
							$tot_aop_delivery_qty+=$aop_delivery_qty;
							$tot_aop_recv_qty+=$aop_aop_recv_qnty;
							$tot_aop_balance_qty+=$aop_balance_qty;

							$total_fin_prod_qty+=$tot_fin_prod_qty;
							$total_fin_prod_under_over_qty+=$fin_prod_under_over_qty;
							$total_fin_fab_process_loss_inside+=$fin_fab_process_loss_inside;
							$total_fin_fab_process_loss_outside+=$fin_fab_process_loss_outside;
							$total_tot_knit_process_loss+=$tot_knit_process_loss;

							$total_emb_issue_qnty_in+=$emb_issue_qnty_in;
							$total_emb_issue_qnty_out+=$emb_issue_qnty_out;
							$total_tot_emb_issueQty+=$tot_emb_issueQty;

							$total_emb_recv_qnty_in+=$emb_recv_qnty_in;
							$total_emb_recv_qnty_out+=$emb_recv_qnty_out;
							$total_tot_emb_recvQty+=$tot_emb_recvQty;

							$total_finish_qnty_in_rec_gmt+=$finish_qnty_in_rec_gmt;
							$total_finish_qnty_out_rec_gmt+=$finish_qnty_out_rec_gmt;

							$tot_wovenReqQty+=$wovenReqQty;
							$tot_wovenRecQty+=$wovenRecQty;
							$tot_wovenRecBalQty+=$wovenFabRecBal;
							$tot_wovenIssueQty+=$wovenIssueQty;
							$tot_wovenIssueBalQty+=$wovenFabIssueBal;

							$tot_gmt_qty+=$tot_po_plan_qnty;
							$tot_cutting_qty+=$cuttingQty;
							$tot_printIssIn_qty+=$print_issue_qnty_in;
							$tot_printIssOut_qty+=$print_issue_qnty_out;
							$tot_printIssue_qty+=$total_print_issued;
							$tot_printRcvIn_qty+=$print_recv_qnty_in;
							$tot_printRcvOut_qty+=$print_recv_qnty_out;
							$tot_printRcv_qty+=$total_print_recv;
							$tot_printRjt_qty+=$print_reject_qnty;

							$tot_sewInInput_qty+=$sew_input_qnty_in;
							$tot_sewInOutput_qty+=$sew_input_qnty_out;
							$tot_sewIn_qty+=$total_sew_input;
							$tot_sewInBal_qty+=$sew_input_balance_qnty;

							$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
							$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
							$tot_sewRcv_qty+=$total_sew_recv;
							$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
							$tot_sewRcvRjt_qty+=$sew_reject_qnty;

							$tot_washRcvIn_qty+=$wash_recv_qnty_in;
							$tot_washRcvOut_qty+=$wash_recv_qnty_out;
							$tot_washRcv_qty+=$total_wash_recv;
							$tot_washRcvBal_qty+=$wash_balance_qnty;

							$tot_gmtFinIn_qty+=$gmt_finish_in;
							$tot_gmtFinOut_qty+=$gmt_finish_out;
							$tot_gmtFin_qty+=$total_gmts_finish_qnty;
							$tot_gmtFinBal_qty+=$finish_balance_qnty;
							$tot_gmtFinRjt_qty+=$finish_reject_qnty;
							$tot_gmtEx_qty+=$exfactory_qnty;
							$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

							$tot_shortExcess_exFactory_qty+=$short_excess_exFactoryQty;
							$tot_prLoss_qty+=$process_loss;
							$tot_prLossDye_qty+=$process_loss_dyeing;
							$tot_prLossCut_qty+=$cutting_process_loss;
							$tot_process_loss_yern_per += $process_loss_yern_per;
							$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
							$tot_process_loss_cutting_per += $process_loss_cutting_per;
							$i++;
						}
					}

					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="100">Total :</td>
                    <td width="100">&nbsp;</td>


                    <td width="100">&nbsp;</td>
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>


                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                    <td width="90" align="right" id="td_yarn_undOvr_qty"><? echo number_format($tot_yarn_undOvr_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($tot_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_qty"><? echo number_format($tot_grey_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_prLoss_qty"><? echo number_format($tot_grey_prLoss_qty,2); ?></td>


                    <td width="80" align="right" id="td_grey_undOver_qty"><? echo number_format($tot_grey_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty"><? echo number_format($tot_grey_issDye_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($tot_grey_lftOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_fin_qty,2); ?></td>


                    <td width="80" align="right" id="td_fin_prLoss_qty"><? echo number_format($tot_fin_prLoss_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($tot_fin_issCut_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_fin_lftOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_gmt_qty); ?></td>


                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cutting_qty); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty"><? echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_printRcv_qty"><? echo number_format($tot_printRcv_qty); ?></td>
                    <td width="80" align="right" id="td_printRjt_qty"><? echo number_format($tot_printRjt_qty); ?></td>
                    <td width="80" align="right" id="td_sewIn_qty"><? echo number_format($tot_sewIn_qty); ?></td>


                    <td width="80" align="right" id="td_sewInBal_qty"><? echo number_format($tot_sewInBal_qty); ?></td>
                   	<td width="100" align="right">&nbsp;</td>
                    <td width="80" align="right" id="td_sewRcv_qty"><? echo number_format($tot_sewRcv_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvBal_qty"><? echo number_format($tot_sewRcvBal_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvRjt_qty"><? echo number_format($tot_sewRcvRjt_qty); ?></td>


                    <td width="80" align="right" id="td_gmtFin_qty"><? echo number_format($tot_gmtFin_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinBal_qty"><? echo number_format($tot_gmtFinBal_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinRjt_qty"><? echo number_format($tot_gmtFinRjt_qty); ?></td>
                    <td width="80" align="right" id="td_gmtrej_qty"><? echo number_format($total_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_gmtEx_qty"><? echo number_format($tot_gmtEx_qty); ?></td>


                    <td width="80" align="right" id="td_gmtFinLeftOver_qty"><? echo number_format($tot_gmtFinLeftOver_qty); ?></td>
                    <td width="80" align="right" id="td_shortExcess_exFactory_qty"><? echo number_format($tot_shortExcess_exFactory_qty); ?></td>
                    <td width="80" align="right" id="td_prLoss_qty"><? echo number_format($tot_prLoss_qty); ?></td>
                    <td width="80" align="right" id="td_prLossDye_qty"><? echo number_format($tot_prLossDye_qty); ?></td>
                    <td align="right" width="80" id="td_prLossCut_qty"><? echo number_format($tot_prLossCut_qty); ?></td>


                </tr>
           </table>
        </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$cbo_search_type**$type";
    exit();


}
if($action=="job_report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$ref_no=str_replace("'","",$txt_ref_no);
	$file_no=str_replace("'","",$txt_file_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_ex_date_form=str_replace("'","",$txt_ex_date_form);
	$txt_ex_date_to=str_replace("'","",$txt_ex_date_to);
	$type=str_replace("'","",$type);
	$cbo_date_category=str_replace("'","",$cbo_date_category);


	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	/*$ship_date_cond="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	$ex_fact_date_cond="";
	if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	{
		$ex_fact_date_cond="and c.ex_factory_date between '$txt_ex_date_form' and '$txt_ex_date_to'  AND c.is_deleted = 0 AND c.status_active = 1";
	}*/
	$ship_date_cond="";
	if($cbo_date_category==1)
	{

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	}
	else if($cbo_date_category==2)
	{
		//$ex_fact_date_cond="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and c.ex_factory_date between '$txt_date_from' and '$txt_date_to' AND c.is_deleted = 0 AND c.status_active = 1";
		}
	}
	/*else if($cbo_date_category==3) //Ref Closing date
	{

		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and d.closing_date between '$txt_date_from' and '$txt_date_to'";
		}
	}
	*/
	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
	$order_cond="";
	if(trim($ref_no)!="") $ref_no_cond="and b.grouping in('$ref_no')";
	if(trim($file_no)!="") $ref_no_cond="and b.file_no in('$file_no')";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	//echo $txt_order_id;die;
	if($txt_order_id!="")
	{
		$order_cond="and b.id in($txt_order_id)";
	}
	else
	{
		if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'"; else $order_cond="";
	}
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";

		 $sql_po="select a.buyer_name, a.job_no,a.gmts_item_id as item_number_id, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1
		where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";

	//echo  $sql_po;die;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();
	$all_int_ref="";
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($row[csf("grouping")]!="")
		{
		if($all_int_ref=="") $all_int_ref=$row[csf("grouping")]; else $all_int_ref.=",".$row[csf("grouping")];
		}
		$result_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
		//$result_data_arr[$row[csf("po_id")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
		$result_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("po_id")]]["item_number_id"]=$row[csf("item_number_id")];
		$result_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];

		$result_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["po_qnty"]=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
		$JobArr[]="'".$row[csf('job_no')]."'";
		$job_no=$row[csf('job_no')];

	}
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	  if($job_no !=''){
	 $condition->job_no("='$job_no'");
	}
	$condition->init();
	$yarn= new yarn($condition);

	//$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
	$yarn_qty_arr=$yarn->getJobWiseYarnQtyArray();
	//print_r($yarn_qty_arr);die;

	$yarn_req_qty=$yarn_qty_arr[$job_no];
	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
	$po_cond_for_in=" and (";
	$po_cond_for_in2=" and (";
	$po_cond_for_in3=" and (";

	$poIdsArr=array_chunk(explode(",",$poIds),999);
	foreach($poIdsArr as $ids)
	{
	$ids=implode(",",$ids);
	$po_cond_for_in.=" b.po_break_down_id in($ids) or";
	$po_cond_for_in2.=" a.po_breakdown_id in($ids) or";
	$po_cond_for_in3.=" b.order_id in($ids) or";

	}
	$po_cond_for_in=chop($po_cond_for_in,'or ');
	$po_cond_for_in.=")";
	$po_cond_for_in2=chop($po_cond_for_in2,'or ');
	$po_cond_for_in2.=")";
	$po_cond_for_in3=chop($po_cond_for_in3,'or ');
	$po_cond_for_in3.=")";

	}
	else
	{
	$po_cond_for_in=" and b.po_break_down_id in($poIds)";
	$po_cond_for_in2=" and a.po_breakdown_id  in($poIds)";
	$po_cond_for_in3=" and b.order_id in($poIds)";

	}
	//sum(b.grey_fab_qnty) as grey_req_qnty, sum(b.fin_fab_qnty) as fin_fab_qnty
	$booking_req_arr=array();
	$sql_wo=sql_select("select a.booking_no,a.booking_no_prefix_num,a.is_short,a.booking_type,b.po_break_down_id,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	sum(CASE WHEN a.item_category in(2,13) THEN b.fin_fab_qnty ELSE 0 END) AS fin_fab_qnty,
	sum(b.grey_fab_qnty) as grey_fab_qty,
	sum(CASE WHEN a.item_category=12 and a.process=35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty
	from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in group by a.booking_no,a.booking_no_prefix_num,a.is_short,a.booking_type,b.po_break_down_id");

	//finish_prod_arr
	$all_booking_nos="";$tot_fin_fab_qty=$tot_grey_fab_qty=0;
	foreach ($sql_wo as $brow)
	{
		if($brow[csf("fin_fab_qnty")]>0)
		{
			$tot_fin_fab_qty+=$brow[csf("fin_fab_qnty")];
		}
		if($brow[csf("grey_fab_qty")]>0)
		{
			$tot_grey_fab_qty+=$brow[csf("grey_fab_qty")];
		}
		if($brow[csf("booking_type")]==1 && $brow[csf("is_short")]==1)
		{
		$all_booking_nos.=$brow[csf("booking_no")].' M,';
		}
		if($brow[csf("booking_type")]==1 && $brow[csf("is_short")]==2)
		{
		$all_booking_nos.=$brow[csf("booking_no")].' S,';
		}
	}
	$all_booking_nos=rtrim($all_booking_nos,',');

		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('return_qnty')];
		}

	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));

	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;

	}
	if($cbo_search_type==1)
	{
		$tbl_width=1000;
		//$ship_date_html="Shipment Date";
		//$ex_fact_date_html="Ex-Fact. Date";
	}

	ob_start();
	?>
        <div style="width:100%">
             <table width="<? echo $tbl_width;?>">
                <tr>
                    <td align="left" width="<? echo $tbl_width;?>" colspan="11" style="font-size: large;"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                 <tr>
                    <td align="left" width="<? echo $tbl_width/2;?>" colspan="5">Internal Ref. No:<? echo implode(",",array_unique(explode(",",$all_int_ref))); ?></td>
                     <td align="left" width="<? echo $tbl_width/2;?>" colspan="6">Booking No:<? echo implode(",",array_unique(explode(",",$all_booking_nos)));; ?></td>
                </tr>
            </table>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                	<tr style="font-size:13px">
                        <th width="20">SL</th>
                        <th width="110">Order No</th>
                        <th width="110">Job No</th>
                        <th width="120">Buyer</th>
                        <th width="120">Style</th>
                        <th width="100">Gmts. Item</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">Shipment Date</th>
                        <th width="80">Ex-Fact. Qty.</th>
                        <th width="80">Ship Out%</th>
                        <th width="">Short/Exces</th>
                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3">

                <?
					$i=1;$tot_order_qty=$tot_exf_qty=$tot_plun_cut_qnty=0;$ratio=0;
					if($cbo_search_type==1)
					{
						foreach($result_data_arr as $po_id=>$val)
						{
							$ratio=$val["ratio"];
							$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
							$tot_po_qnty=$val["po_qnty"];
							$exfactory_qnty=$val["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
							$plan_cut_qty=$val["plan_cut"];
							$job_no=$val["job_no"];
							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$ratio;

							 $gmts_item=''; $gmts_item_id=explode(",",$val['item_number_id']);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
							}

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="20"><? echo $i; ?></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $val["po_number"]; ?></div></td>
								<td width="110" align="center"><p><? echo $val["job_no"]; ?>&nbsp;</p></td>
								<td width="120" align="center"><p><? echo  $buyer_arr[$val["buyer_name"]]; ?>&nbsp;</p></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $val["style_ref_no"]; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><?  echo $gmts_item; ?></div></td>
                                <td width="80"  align="right"><div style="word-wrap:break-word; width:80px"><? echo number_format($val["po_qnty"],0); ?></div></td>
								<td width="80"><div style="word-wrap:break-word; width:80px"><?  if(trim($val["pub_shipment_date"])!="" && trim($val["pub_shipment_date"])!='0000-00-00') echo change_date_format($val["pub_shipment_date"]);; ?></div></td>
								<td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($val["ex_factory_qnty"],0); ?></p></td>
								<td width="80" align="right" title="ExFact Qty/PO Qty*100"><p><? echo number_format(($val["ex_factory_qnty"]/$val["po_qnty"])*100,2); ?>&nbsp;</p></td>
								<td align="right"  width=""><? $short_ex_qty=$val["ex_factory_qnty"]-$val["po_qnty"]; echo number_format($short_ex_qty,2); ?></td>

							</tr>
							<?
							$tot_order_qty+=$tot_po_qnty;
							$tot_plun_cut_qnty+=$val["plan_cut"];

							$tot_exf_qty+=$val["ex_factory_qnty"];
							$tot_short_ex_qty+=$short_ex_qty;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;


							$i++;
						}
					}

					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="20">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120"></td>
                   <td width="100">Total :</td>
                   <td width="80" id="po_qty_td"><? echo $tot_order_qty;?></td>
                   <td width="80"></td>
                   <td width="80" id="exf_qty_td"><? echo $tot_exf_qty;?></td>
                   <td width="80"><? echo number_format($tot_exf_qty/$tot_order_qty,2);?></td>
                    <td width="" id="short_qty_td"><? echo $tot_short_ex_qty;?></td>

                </tr>
           </table>
           <br>
           <?
          $yarnDataArr=sql_select("select
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_issue_master c where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and b.item_category=1 and c.issue_purpose!=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1");

			$yarnReturnDataArr=sql_select("select
						sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
						sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_receive_master c where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1");

			$yarn_qnty_in=$yarnDataArr[0][csf('issue_qnty_in')]-$yarnReturnDataArr[0][csf('return_qnty_in')];
			$yarn_qnty_out=$yarnDataArr[0][csf('issue_qnty_out')]-$yarnReturnDataArr[0][csf('return_qnty_out')];
			unset($yarnDataArr); unset($yarnReturnDataArr);

			$dataArrayTrans=sql_select("select
					sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
					sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
					sum(CASE WHEN entry_form in (83,13,82,183,110) and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
					sum(CASE WHEN entry_form in (83,13,82,183,110) and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
					sum(CASE WHEN entry_form in(134,15,14) and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
					sum(CASE WHEN entry_form  in(134,15,14) and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish
					from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(11,83,13,15,82,183,110,134,14) and po_breakdown_id in($all_po_id)");



			$transfer_in_qnty_yarn=$dataArrayTrans[0][csf('transfer_in_qnty_yarn')];
			$transfer_out_qnty_yarn=$dataArrayTrans[0][csf('transfer_out_qnty_yarn')];
			$transfer_in_qnty_knit=$dataArrayTrans[0][csf('transfer_in_qnty_knit')];
			$transfer_out_qnty_knit=$dataArrayTrans[0][csf('transfer_out_qnty_knit')];
			$transfer_in_qnty_finish=$dataArrayTrans[0][csf('transfer_in_qnty_finish')];
			$transfer_out_qnty_finish=$dataArrayTrans[0][csf('transfer_out_qnty_finish')];

			unset($dataArrayTrans);

			$total_issued=$yarn_qnty_in+$yarn_qnty_out+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
			$under_over_issued=$yarn_req_qty-$total_issued;

			$greyYarnIssueQnty=return_field_value("sum(a.cons_quantity) as issue_qnty","inv_transaction a, inv_issue_master b","a.mst_id=b.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2","issue_qnty");

			$dyedYarnRecvQnty=return_field_value("sum(a.cons_quantity) as recv_qnty","inv_transaction a, inv_receive_master b","a.mst_id=b.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1","recv_qnty");

			$dyed_yarn_balance=$greyYarnIssueQnty-$dyedYarnRecvQnty;

			$yarn_qnty_in_perc=($yarn_qnty_in/$yarn_req_qty)*100;
			$yarn_qnty_out_perc=($yarn_qnty_out/$yarn_req_qty)*100;
			$transfer_in_qnty_yarn_perc=($transfer_in_qnty_yarn/$yarn_req_qty)*100;
			$transfer_out_qnty_yarn_perc=($transfer_out_qnty_yarn/$yarn_req_qty)*100;
			$total_issued_perc=($total_issued/$yarn_req_qty)*100;
			$under_over_issued_perc=($under_over_issued/$yarn_req_qty)*100;
			$greyYarnIssueQnty_perc=($greyYarnIssueQnty/$yarn_req_qty)*100;
			$dyedYarnRecvQnty_perc=($dyedYarnRecvQnty/$yarn_req_qty)*100;
			$dyed_yarn_balance_perc=($dyed_yarn_balance/$yarn_req_qty)*100;

	//Yarn End

		   ?>
          <table width="1000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Yarn Status</th>
                    <th colspan="3" width="270">Dyed Yarn Status</th>
                </tr>
                <tr>
                    <th width="90">Required <br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                    <th width="90">Issued Inside</th>
                    <th width="90">Issued SubCon</th>
                    <th width="90">Transfer In</th>
                    <th width="90">Transfer Out</th>
                    <th width="90">Total Issued</th>
                    <th width="90">Under or Over Issued</th>
                    <th width="90">Grey Yarn Issued</th>
                    <th width="90">Dyed Yarn Received</th>
                    <th>Balance/(Yarn dyed P.L)</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($yarn_req_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($yarn_qnty_in,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($yarn_qnty_out,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_yarn,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_yarn,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_issued,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="Yarn Req-Total Issue"><? echo number_format($under_over_issued,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($greyYarnIssueQnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($dyedYarnRecvQnty,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="Grey Yarn Issued-Dyed Yarn Received"><? echo number_format($dyed_yarn_balance,2,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($yarn_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($yarn_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_yarn_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_yarn_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_issued_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($under_over_issued_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($greyYarnIssueQnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($dyedYarnRecvQnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($dyed_yarn_balance_perc,2,'.',''); ?>&nbsp;</td>
            </tr>
    	</table>
        <br>
        <?
						/*$fab_reqDataArray=sql_select("select sum( CASE WHEN c.fabric_source=1 and c.fab_nature_id=2 THEN ((b.requirment/b.pcs)*a.plan_cut_qnty) ELSE 0 END) as grey_req,
						sum( CASE WHEN fabric_source=1  and c.fab_nature_id=2 THEN ((b.cons/b.pcs)*a.plan_cut_qnty) ELSE 0 END) as finish_req,
						sum( CASE WHEN fabric_source=2  and c.fab_nature_id=2 THEN ((b.cons/b.pcs)*a.plan_cut_qnty) ELSE 0 END) as purchase_finish_req,
						sum( CASE WHEN fabric_source in(1,2,3)  and c.fab_nature_id=3 THEN ((b.cons/b.pcs)*a.plan_cut_qnty) ELSE 0 END) as woven_finish_req
						from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b ,wo_pre_cost_fabric_cost_dtls c where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no   and a.job_no_mst=c.job_no and   a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($all_po_id)");*/

						$fabric= new fabric($condition);
						$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
						//print_r($fabric_qty_arr);

						$fab_reqDataArray=sql_select("select a.id as po_id,c.uom,c.fabric_source
						from wo_po_break_down a,wo_pre_cost_fabric_cost_dtls c where a.job_no_mst=c.job_no  and c.is_deleted=0 and c.status_active=1  and a.is_deleted=0 and a.status_active=1 and a.id in ($all_po_id)");
						$total_purchase_qty_woven=$total_purchase_qty_knit_fin=0;
						foreach($fab_reqDataArray as $row)
						{
							if($row[csf("fabric_source")]==2)
							{
								$total_purchase_qty_woven+=$fabric_qty_arr['woven']['finish'][$row[csf("po_id")]][$row[csf("uom")]]+$fabric_qty_arr['woven']['finish'][$row[csf("po_id")]][$row[csf("uom")]];

							}
							$total_purchase_qty_knit_fin+=$fabric_qty_arr['knit']['finish'][$row[csf("po_id")]][$row[csf("uom")]]+$fabric_qty_arr['knit']['finish'][$row[csf("po_id")]][$row[csf("uom")]];

						}
						unset($fab_reqDataArray);
						//echo $total_purchase_qty_woven;
						$fab_purchase_req_qnty=$total_purchase_qty_knit_fin;//$fab_reqDataArray[0][csf('purchase_finish_req')];
						$woven_finish_fabric_req_qnty=$total_purchase_qty_woven;//$fab_reqDataArray[0][csf('woven_finish_req')];
						$grey_fabric_req_qnty=$tot_grey_fab_qty;
						$prodDataArr=sql_select("select
						sum(CASE WHEN c.knitting_source!=3 and c.entry_form in(2,22) THEN a.quantity ELSE 0 END) AS knit_qnty_in,
						sum(CASE WHEN c.knitting_source=3 and c.entry_form in(2,22) THEN a.quantity ELSE 0 END) AS knit_qnty_out,
						sum(CASE WHEN a.entry_form in(58,22) and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_inside,
						sum(CASE WHEN a.entry_form in(58,22) and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_outside
						from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and c.item_category=13 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9");

						$knit_qnty_in=$prodDataArr[0][csf('knit_qnty_in')];
						$knit_qnty_out=$prodDataArr[0][csf('knit_qnty_out')];
						$knit_qnty_recv_inside=$prodDataArr[0][csf('knit_qnty_rec_inside')];
						$knit_qnty_recv_outside=$prodDataArr[0][csf('knit_qnty_rec_outside')];
						unset($prodDataArr);

						$issueData=sql_select("select
							sum(CASE WHEN a.entry_form=16 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_in_qnty,
							sum(CASE WHEN a.entry_form=16  and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_out_qnty,
							sum(CASE WHEN a.entry_form=61 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_out,
							sum(CASE WHEN a.entry_form=61 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_in,
							sum(CASE WHEN a.entry_form=18 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty,
							sum(CASE WHEN a.entry_form=71 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
							sum(CASE WHEN a.entry_form=19 THEN a.quantity ELSE 0 END) AS woven_issue
							from order_wise_pro_details a,inv_grey_fabric_issue_dtls b,inv_issue_master
	c where a.dtls_id=b.id and b.mst_id=c.id  and a.entry_form in(16,18,19,61,71) and a.status_active=1 and a.is_deleted=0 and po_breakdown_id in($all_po_id) ");


						$knit_grey_issue_Todye_in_qnty=$issueData[0][csf('grey_issue_in_qnty')]+$issueData[0][csf('grey_issue_qnty_roll_wise_in')];
						$knit_grey_issue_Todye_out_qnty=$issueData[0][csf('grey_issue_out_qnty')]+$issueData[0][csf('grey_issue_qnty_roll_wise_out')];
						$tot_knit_grey_issue_Todye_out_qnty=$knit_grey_issue_Todye_in_qnty+$knit_grey_issue_Todye_out_qnty;
						$total_issued=$yarn_qnty_in+$yarn_qnty_out+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;


						$issueData2=sql_select("select
						sum(CASE WHEN entry_form=16 THEN quantity ELSE 0 END) AS grey_issue_qnty,
						sum(CASE WHEN entry_form=61 THEN quantity ELSE 0 END) AS grey_issue_qnty_roll_wise,
						sum(CASE WHEN entry_form=18 THEN quantity ELSE 0 END) AS issue_to_cut_qnty,
						sum(CASE WHEN entry_form=71 THEN quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise
						from order_wise_pro_details where po_breakdown_id in($all_po_id) and status_active=1 and is_deleted=0");
						$issuedToCutQnty=$issueData2[0][csf('issue_to_cut_qnty')]+$issueData2[0][csf('issue_to_cut_qnty_roll_wise')];



						$total_knitting=$knit_qnty_in+$knit_qnty_out;
						$process_loss=$total_issued-$total_knitting;//$knit_qnty_in-$knit_qnty_out
						$process_loss_perc=($process_loss/$total_issued)*100;
						$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
						$knit_processloss_inside=$yarn_qnty_in-$knit_qnty_in;
						$knit_processloss_outside=$yarn_qnty_out-$knit_qnty_out;
						$total_knit_processloss=$total_issued-$total_knitting;

						$grey_avilable=$knit_qnty_recv_inside+$knit_qnty_recv_outside+$transfer_in_qnty_knit+$transfer_out_qnty_knit;
						$knit_grey_left_over=$grey_avilable-$tot_knit_grey_issue_Todye_out_qnty;

						$knit_qnty_in_perc=($knit_qnty_in/$grey_fabric_req_qnty)*100;
						$knit_qnty_out_perc=($knit_qnty_out/$grey_fabric_req_qnty)*100;
						$total_knitting_perc=($total_knitting/$grey_fabric_req_qnty)*100;
						$knit_processloss_inside_perc=($knit_processloss_inside/$grey_fabric_req_qnty)*100;
						$knit_processloss_outside_perc=($knit_processloss_outside/$grey_fabric_req_qnty)*100;
						$total_knit_processloss_perc=($total_knit_processloss/$grey_fabric_req_qnty)*100;
						$total_knit_processloss_perc=($total_knit_processloss/$grey_fabric_req_qnty)*100;
						$knit_qnty_recv_inside_perc=($knit_qnty_recv_inside/$grey_fabric_req_qnty)*100;
						$knit_qnty_recv_outside_perc=($knit_qnty_recv_outside/$grey_fabric_req_qnty)*100;
						$grey_avilable_outside_perc=($grey_avilable/$grey_fabric_req_qnty)*100;
						$knit_grey_issue_Todye_in_qnty_perc=($knit_grey_issue_Todye_in_qnty/$grey_fabric_req_qnty)*100;
						$knit_grey_issue_Todye_out_qnty_perc=($knit_grey_issue_Todye_out_qnty/$grey_fabric_req_qnty)*100;
						$tot_knit_grey_issue_Todye_out_qnty_perc=($tot_knit_grey_issue_Todye_out_qnty/$grey_fabric_req_qnty)*100;
						$knit_grey_left_over_perc=($knit_grey_left_over/$grey_fabric_req_qnty)*100;
						// $knit_processloss_inside=$yarn_qnty_in-$knit_qnty_in;


		?>
        <table width="1620" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="18" width="1620">Knitting Production and Grey Status</th>
                </tr>
                <tr>
                    <th width="90">Gray Fab Req.<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                    <th width="90">Inside Prod.</th>
                    <th width="90">SubCon Prod.</th>
                    <th width="90">Total Knit Prod.</th>
                    <th width="90">Under or Over Prod.</th>
                    <th width="90">Knit. Process Loss Kg. inside</th>
                    <th width="90">Knit. Process Loss Kg. Outside</th>
                    <th width="90">Total Knit. Process Loss Kg.</th>
                    <th width="90">Knit. Receive Inside</th>
                    <th width="90">Knit. Receive SubCon Prod.</th>
                    <th width="90">Transfer In</th>
                    <th width="90">Transfer Out</th>
                    <th width="90">Grey Available</th>
                    <th width="90">Knit. Issued To Dyeing inside</th>
                    <th width="90">Knit. Issued To Dyeing outside</th>
                    <th width="90">Total Knit. Issued To Dyeing</th>
                    <th>Left Over</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($grey_fabric_req_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_in,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_out,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_knitting,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="Gray Fab Req.-Total Knit Prod"><? echo number_format($under_over_prod,2,'.',''); ?>&nbsp;</td>
                <td align="right"  title="Yarn Issue In.-Inside Prod."><? echo number_format($knit_processloss_inside,2,'.',''); ?>&nbsp;</td>
                <td align="right"  title="Yarn Issue Subcon.-Knit Outside Prod."><? echo number_format($knit_processloss_outside,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="total Yarn Issue-Total Knit Prod."><? echo number_format($total_knit_processloss,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_recv_inside,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_recv_outside,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_knit,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_knit,2,'.',''); ?>&nbsp;</td>
                <td align="right"  title="Knit Rec Inside+outside+knit TransferIN+Out"><? echo number_format($grey_avilable,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_grey_issue_Todye_in_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_grey_issue_Todye_out_qnty,2,'.',''); ?>&nbsp;</td>

                <td align="right"><? echo number_format($tot_knit_grey_issue_Todye_out_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="Grey Available-Total Knit IssueToDye"><? echo number_format($knit_grey_left_over,2,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_knitting_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($under_over_prod_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_processloss_inside_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_processloss_outside_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_knit_processloss_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_recv_inside_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_recv_outside_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_knit_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_knit_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($grey_avilable_outside_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_grey_issue_Todye_in_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_grey_issue_Todye_in_qnty_perc,2,'.',''); ?>&nbsp;</td>


                <td align="right"><? echo number_format($tot_knit_grey_issue_Todye_out_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_grey_left_over_perc,2,'.',''); ?>&nbsp;</td>
            </tr>
    	</table>
        <br>
        <?
		$batchDataArr=sql_select("select sum(b.batch_qnty) as batch_qty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id  and a.batch_against not in(2) and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id in($all_po_id)");
		//echo "select sum(b.batch_qnty) as batch_qty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id  and a.batch_against not in(2) and a.entry_form!=36 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and b.po_id in($all_po_id)";
			$batch_qty=$batchDataArr[0][csf('batch_qty')];
			unset($batchDataArr);
			$prodFinDataArr=sql_select("select
						sum(CASE WHEN c.knitting_source!=3 and  a.entry_form in(7) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
						sum(CASE WHEN c.knitting_source=3  and  a.entry_form  in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
						sum(CASE WHEN  a.entry_form in(37) and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS finish_fab_qnty_inside,
						sum(CASE WHEN  a.entry_form in(37) and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS finish_fab_qnty_outside,
						sum(CASE WHEN c.receive_basis=1  and  a.entry_form  in(17,37) THEN a.quantity ELSE 0 END) AS fin_knit_fab_purchase
						from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and c.item_category=2 and a.entry_form in(7,37,66) and c.entry_form in(7,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1");

		$finish_qnty_in=$prodFinDataArr[0][csf('finish_qnty_in')];
		$finish_qnty_out=$prodFinDataArr[0][csf('finish_qnty_out')];
		$finish_qnty_inside=$prodFinDataArr[0][csf('finish_fab_qnty_inside')];
		$finish_qnty_outside=$prodFinDataArr[0][csf('finish_fab_qnty_outside')];
		$fab_purchase_req_qnty=0;
		$fab_purchase_req_qnty=$prodFinDataArr[0][csf('fin_knit_fab_purchase')];
	//	echo $finish_qnty_inside.'=x'.$finish_qnty_outside;
		unset($prodFinDataArr);

        $finish_fabric_req_qnty= $tot_fin_fab_qty;//finish_fabric_req_qnty
		$batch_qty_perc=($batch_qty/$finish_fabric_req_qnty)*100;
		$total_finishing=$finish_qnty_in+$finish_qnty_out;
		$inside_dye_fin_processlossKg=$knit_grey_issue_Todye_in_qnty-$finish_qnty_in;
		$outside_dye_fin_processlossKg=$knit_grey_issue_Todye_out_qnty-$finish_qnty_out;
		$tot_dye_fin_processlossKg=$tot_knit_grey_issue_Todye_out_qnty-$total_finishing;//tot_knit_grey_issue_Todye_out_qnty
		//$issuedToCutQnty=$knit_grey_issue_Todye_out_qnty;
		//$transfer_in_qnty_finish
		$finish_available=($finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish+$fab_purchase_req_qnty)-($transfer_out_qnty_finish+$issuedToCutQnty);
		$process_loss_finishing=$knit_grey_issue_Todye_in_qnty-$total_finishing;
		$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
		$finish_left_over=$total_finishing-$knit_grey_issue_Todye_out_qnty;

		$finish_qnty_in_perc=($finish_qnty_in/$finish_fabric_req_qnty)*100;
		$finish_qnty_out_perc=($finish_qnty_out/$finish_fabric_req_qnty)*100;
		$transfer_in_qnty_finish_perc=($transfer_in_qnty_finish/$finish_fabric_req_qnty)*100;
		$transfer_out_qnty_finish_perc=($transfer_out_qnty_finish/$finish_fabric_req_qnty)*100;
		$total_finishing_perc=($total_finishing/$finish_fabric_req_qnty)*100;

		//$finish_qnty_inside.'=x'.$finish_qnty_outside;
		$finish_qnty_inside_perc=($finish_qnty_inside/$finish_fabric_req_qnty)*100;
		$finish_qnty_outsidee_perc=($finish_qnty_outside/$finish_fabric_req_qnty)*100;

		$inside_dye_fin_processlossKg_perc=($inside_dye_fin_processlossKg/$finish_fabric_req_qnty)*100;
		$outside_dye_fin_processlossKg_perc=($outside_dye_fin_processlossKg/$finish_fabric_req_qnty)*100;
		$tot_dye_fin_processlossKg_perc=($tot_dye_fin_processlossKg/$finish_fabric_req_qnty)*100;

		$finish_available_perc=($finish_available/$finish_fabric_req_qnty)*100;
		$process_loss_finishing_perc=($process_loss_finishing/$issuedToDyeQnty)*100;
		$under_over_finish_prod_perc=($under_over_finish_prod/$finish_fabric_req_qnty)*100;
		$issuedToCutQnty_perc=($issuedToCutQnty/$finish_fabric_req_qnty)*100;//knit_grey_issue_Todye_out_qnty
		$finish_left_over_perc=($finish_left_over/$total_finishing)*100;

		?>
        <table width="1600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="18" width="1620">Dyeing and Finish Fabric Production</th>
                </tr>
                <tr>
                    <th width="90">Finish Fab Req.<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                    <th width="90">Batch Qty.</th>
                    <th width="90">Inside Prod.</th>
                    <th width="90">SubCon Prod.</th>
                    <th width="90">Total Prod.</th>
                    <th width="90">Inside Process Loss Kg.</th>
                    <th width="90">Outside Process Loss Kg.</th>
                    <th width="90">TTL Process Loss</th>
                    <th width="90">Finished Fabrics Inside Recd.</th>
                    <th width="90">Finished Fabrics Outside Recd.</th>
                    <th width="90">Transfer In</th>
                    <th width="90">Purchase Qty.</th>
                    <th width="90">Transfer Out</th>
                    <th width="90">Finish Available</th>

                    <th width="90">Under or Over Prod.</th>
                    <th width="90">Issued To Cutting</th>
                    <!--<th>Left Over</th>-->
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($finish_fabric_req_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($batch_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_in,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_out,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finishing,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="Knit. Issued To Dyeing inside-Dye Fin Inside Prod."><? echo number_format($inside_dye_fin_processlossKg,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="Knit. Issued To Dyeing Outside-SubCon Prod."><? echo number_format($outside_dye_fin_processlossKg,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="Total Knit. Issued To Dyeing-TTL Prod"><? echo number_format($tot_dye_fin_processlossKg,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_inside,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_outside,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_finish,2,'.',''); ?>&nbsp;</td>
                <td align="right" title="Fin&KnitWoven(PI Basis) Recv Qty"><? echo number_format($fab_purchase_req_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_finish,2,'.',''); ?>&nbsp;</td>

                <td align="right" title="Finished Fabrics Inside Recd.+Finished Fabrics Outside Recd+Purchase Qty+Trans IN-(Trans Out+issueToCut Qty)"><? echo number_format($finish_available,2,'.',''); ?>&nbsp;</td>

                <td align="right" title="Finish Fab Req-Total Prod. Qty"><? echo number_format($under_over_finish_prod,2,'.',''); ?>&nbsp;</td>

                <td align="right"><? echo number_format($issuedToCutQnty,2,'.',''); ?>&nbsp;</td>
                <!--<td align="right" title="Total Prod-IssueTodye Fin Out"><? //echo number_format($finish_left_over,2,'.',''); ?>&nbsp;</td>-->
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($batch_qty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finishing_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($inside_dye_fin_processlossKg_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($outside_dye_fin_processlossKg_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_dye_fin_processlossKg_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_inside_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_outside_perc,2,'.',''); ?>&nbsp;</td>

                <td align="right"><? echo number_format($transfer_in_qnty_finish_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($purchase_finish_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_finish_perc,2,'.',''); ?>&nbsp;</td>

                <td align="right"><? echo number_format($finish_available_perc,2,'.',''); ?>&nbsp;</td>

                <td align="right"><? echo number_format($under_over_finish_prod_perc,2,'.',''); ?>&nbsp;</td>

                <td align="right"><? echo number_format($issuedToCutQnty_perc,2,'.',''); ?>&nbsp;</td>
                <!--<td align="right"><? //echo number_format($finish_left_over_perc,2,'.',''); ?>&nbsp;</td>-->
            </tr>
    	</table>
        <br>
        <?
        	$woven_recv= sql_select("select sum(CASE WHEN c.entry_form in (17) and a.item_category=3  THEN c.quantity END) AS woven_receive_qnty from inv_transaction a,product_details_master b, order_wise_pro_details c where a.id=c.trans_id  and b.id=c.prod_id and c.entry_form in (17) and c.trans_id!=0 and a.item_category=3 and c.po_breakdown_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
			$wov_qnty_recv=$woven_recv[0][csf('woven_receive_qnty')];
			unset($woven_recv);
			$sql_issue=sql_select("select sum(b.quantity) as issue_qnty from inv_transaction c, order_wise_pro_details b where c.id=b.trans_id and b.status_active=1 and b.is_deleted=0 and b.entry_form=19 and b.po_breakdown_id in($all_po_id)");
			$wov_qnty_issue=$sql_issue[0][csf('issue_qnty')];
			unset($sql_issue);
			$woven_left_over=$wov_qnty_recv-$wov_qnty_issue;
			$tot_woven_available_qty=$wov_qnty_recv; //-($transfer_in_qnty_finish+$transfer_out_qnty_finish);

			$woven_issuedToCutQnty_perc=($wov_qnty_issue/$woven_finish_fabric_req_qnty)*100;
			$total_woven_qty=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;

			$woven_left_over_perc=($woven_left_over/$wov_qnty_recv)*100;
			$woven_recv_qnty_perc=($wov_qnty_recv/$woven_finish_fabric_req_qnty)*100;
			$tot_woven_available_qty_perc=($tot_woven_available_qty/$woven_finish_fabric_req_qnty)*100;
		?>
        <table width="800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="8" width="910">Woven Fabric</th>
                </tr>
                <tr>
                    <th width="90">Woven Fab Req.(Pre Cost)</th>
                    <th width="90">Receive/Prod.</th>
                    <th width="90">Transfer In.</th>
                    <th width="90">Transfer Out</th>
                    <th width="90">Total Available Qty.</th>
                    <th width="90">Issued To Cutting</th>

                    <th>Left Over</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($woven_finish_fabric_req_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($wov_qnty_recv,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($transfer_in_qnty_finish,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($transfer_out_qnty_finish,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_woven_available_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($wov_qnty_issue,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($woven_left_over,2,'.',''); ?>&nbsp;</td>

            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($woven_recv_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($woven_recv_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($woven_recv_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_woven_available_qty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($woven_issuedToCutQnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><?  echo number_format($woven_left_over_perc,2,'.',''); ?>&nbsp;</td>

            </tr>
    	</table>
         <br />
         <?
		 $gmtsProdDataArr=sql_select("select
						sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
						sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty_in,
						sum(CASE WHEN production_type=2 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_issue_qnty_out,
						sum(CASE WHEN production_type=3 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_recv_qnty_in,
						sum(CASE WHEN production_type=3 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_recv_qnty_out,
						sum(CASE WHEN production_type=2 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_in,
						sum(CASE WHEN production_type=2 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_out,
						sum(CASE WHEN production_type=3 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_in,
						sum(CASE WHEN production_type=3 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_out,
						sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_input_qnty_in,
						sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_input_qnty_out,
						sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,
						sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
						sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS finish_qnty_in,
						sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS finish_qnty_out,

						sum(CASE WHEN production_type=2 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_issue_qnty_in,
						sum(CASE WHEN production_type=2 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_issue_qnty_out,
						sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
						sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,
						sum(CASE WHEN production_type=3 and embel_name=1 THEN reject_qnty ELSE 0 END) AS print_reject_qnty,
						sum(CASE WHEN production_type=3 and embel_name=2 THEN reject_qnty ELSE 0 END) AS emb_reject_qnty,
						sum(CASE WHEN production_type=3 and embel_name=3 THEN reject_qnty ELSE 0 END) AS wash_reject_qnty,
						sum(CASE WHEN production_type=5 THEN reject_qnty ELSE 0 END) AS sew_reject_qnty,
						sum(CASE WHEN production_type=8 THEN reject_qnty ELSE 0 END) AS finish_reject_qnty
						from pro_garments_production_mst where po_break_down_id in($all_po_id) and is_deleted=0 and status_active=1");

			$cutting_qnty=$gmtsProdDataArr[0][csf('cutting_qnty')];
			$print_issue_qnty_in=$gmtsProdDataArr[0][csf('print_issue_qnty_in')];
			$print_issue_qnty_out=$gmtsProdDataArr[0][csf('print_issue_qnty_out')];
			$print_recv_qnty_in=$gmtsProdDataArr[0][csf('print_recv_qnty_in')];
			$print_recv_qnty_out=$gmtsProdDataArr[0][csf('print_recv_qnty_out')];
			$embro_issue_qnty_in=$gmtsProdDataArr[0][csf('emb_issue_qnty_in')];
			$embro_issue_qnty_out=$gmtsProdDataArr[0][csf('emb_issue_qnty_out')];
			$embro_recv_qnty_in=$gmtsProdDataArr[0][csf('emb_recv_qnty_in')];
			$embro_recv_qnty_out=$gmtsProdDataArr[0][csf('emb_recv_qnty_out')];
			$print_embr_reject=$gmtsProdDataArr[0][csf('print_reject_qnty')]+$gmtsProdDataArr[0][csf('emb_reject_qnty')];
			$print_reject_qnty=$gmtsProdDataArr[0][csf('print_reject_qnty')];
			$emb_reject_qnty=$gmtsProdDataArr[0][csf('emb_reject_qnty')];
			$finish_reject_qnty=$gmtsProdDataArr[0][csf('finish_reject_qnty')];
			$sew_reject_qnty=$gmtsProdDataArr[0][csf('sew_reject_qnty')];

         $sql_consumtiont_qty=sql_select("select c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 and b.po_break_down_id in ($all_po_id) group by c.body_part_id ");
			$finish_consumtion=0;
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg=0;
				$con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];///str_replace("'","",$row_sew[csf("pcs")]);
				$finish_consumtion+=$con_avg;
			}
			unset($sql_consumtiont_qty);

			$fab_recv_perc=($issuedToCutQnty/$finish_fabric_req_qnty)*100;
			$actual_perc=($cutting_qnty/$tot_plun_cut_qnty)*100;


			$possible_cut_pcs=$issuedToCutQnty/$finish_consumtion;
			$cutting_process_loss=$possible_cut_pcs-$cutting_qnty;
			$cutting_process_loss_perc=($cutting_process_loss/$cutting_qnty)*100;


		$cutting_qnty_perc=($cutting_qnty/$finish_fabric_req_qnty)*100;
		$finish_consumtion_perc=($finish_consumtion/$finish_fabric_req_qnty)*100;


		 ?>
        <table width="820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="8" width="640">Cutting Production</th>
                </tr>
                <tr>
                    <th width="90">Fabric Req.</th>
                    <th width="90">Fabric Recv.</th>
                    <th width="90">Fabric Cutting Qty.</th>
                    <th width="90">Finish Consumption</th>
                    <th width="90">Possible Cut (Pcs)</th>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Actual Cut (Pcs)</th>
                    <th>Process Loss</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($finish_fabric_req_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($issuedToCutQnty,2,'.',''); ?>&nbsp;</td>
                 <td align="right"><? echo number_format($cutting_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_consumtion,5,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($possible_cut_pcs); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_plun_cut_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_process_loss,2,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($fab_recv_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_qnty_perc,2,'.',''); ?>&nbsp;</td>
                 <td align="right"><? echo number_format($finish_consumtion_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($possible_cut_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right"><? echo number_format($actual_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_process_loss_perc,2,'.',''); ?>&nbsp;</td>
            </tr>
    	</table>
        <br>
        <table width="1360" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="14" width="1360">Emblishment Production Status</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Gmts. Print Issued In</th>
                    <th width="90">Gmts. Print Issued SubCon</th>
                    <th width="90">Gmts. Total Print Issued</th>
                    <th width="90">Gmts. Print Rec. Inside</th>
                    <th width="90">Gmts. Print Rec. SubCon</th>
                    <th width="90">Gmts. Total Rec. Print</th>
                    <th width="90">Gmts. Embry. Issued In</th>
                    <th width="90">Gmts. Embry. Issued SubCon</th>
                    <th width="90">Gmts. Total Embry. Issued</th>
                    <th width="90">Gmts. Embry. Rec. Inside</th>
                    <th width="90">Gmts. Embry. Rec. SubCon</th>
                    <th width="90">Gmts. Total Rec. Embry.</th>
                    <th>Gmts. Print + Eby. Reject</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($tot_plun_cut_qnty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_issue_qnty_in,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_issue_qnty_out,5,'.',''); ?>&nbsp;</td>
                <td align="right"><? $tot_emb_print_issue=$print_issue_qnty_in+$print_issue_qnty_out;echo number_format($tot_emb_print_issue); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_recv_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_recv_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? $tot_emb_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;echo number_format($tot_emb_print_recv,2,'.',''); ?>&nbsp;</td>
                 <td align="right"><? echo number_format($embro_issue_qnty_in,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($embro_issue_qnty_out,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? $tot_embro_print_recv=$embro_issue_qnty_in+$embro_issue_qnty_out;echo number_format($tot_embro_print_recv,5,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($embro_recv_qnty_in); ?>&nbsp;</td>
                <td align="right"><? echo number_format($embro_recv_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><?  $tot_embro_recv_qnty_out=$embro_recv_qnty_in+$embro_recv_qnty_out; echo number_format($tot_embro_recv_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_embr_reject,0,'.',''); ?>&nbsp;</td>
            </tr>
            <?
            $print_issue_qnty_in_perc=($print_issue_qnty_in/$tot_plun_cut_qnty)*100;
			$print_issue_qnty_out_perc=($print_issue_qnty_out/$tot_plun_cut_qnty)*100;
			$tot_emb_print_issue_perc=($tot_emb_print_issue/$tot_plun_cut_qnty)*100;

			$print_recv_qnty_in_perc=($print_recv_qnty_in/$tot_plun_cut_qnty)*100;
			$print_recv_qnty_out_perc=($print_recv_qnty_out/$tot_plun_cut_qnty)*100;
			$tot_emb_print_recv_perc=($tot_emb_print_recv/$tot_plun_cut_qnty)*100;

			$embro_issue_qnty_in_perc=($embro_issue_qnty_in/$tot_plun_cut_qnty)*100;
			$embro_issue_qnty_out_perc=($embro_issue_qnty_out/$tot_plun_cut_qnty)*100;

			$tot_embro_print_recv_perc=($tot_embro_print_recv/$tot_plun_cut_qnty)*100;
			$embro_recv_qnty_in_perc=($embro_recv_qnty_in/$tot_plun_cut_qnty)*100;
			$print_recv_qnty_out_perc=($print_recv_qnty_out/$tot_plun_cut_qnty)*100;
			$tot_embro_recv_qnty_out_perc=($tot_embro_recv_qnty_out/$tot_plun_cut_qnty)*100;

			$print_reject_qnty_perc=($print_embr_reject/$tot_plun_cut_qnty)*100;
			?>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($print_issue_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_issue_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_emb_print_issue_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_recv_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_recv_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_emb_print_recv_perc,2,'.',''); ?>&nbsp;</td>
                 <td align="right"><? echo number_format($embro_issue_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($embro_issue_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_embro_print_recv_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($embro_recv_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_recv_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_embro_recv_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($print_reject_qnty_perc,2,'.',''); ?>&nbsp;</td>
            </tr>
    	</table>
		<br>
		<?
         $wash= new wash($condition);
	    //  $wash_qtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		//   print_r($wash_qtyArr);
		 $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
		 $wash_qty_job_name_arr =$wash->getQtyArray_by_jobAndEmbname();
		 $wash_amount_job_name_arr =$wash->getAmountArray_by_jobAndEmbname();
		//   print_r($wash_qty_job_name_arr);
	    $wash_req_qty=$wash_qty_job_name_arr[$job_no][3];
		// echo $wash_req_qty;
        $wash_issue_in=$gmtsProdDataArr[0][csf('wash_issue_qnty_in')];
        $wash_issue_out=$gmtsProdDataArr[0][csf('wash_issue_qnty_out')];
        $total_issue=$wash_issue_in+$wash_issue_out;
		$wash_recv_in=$gmtsProdDataArr[0][csf('wash_recv_qnty_in')];
		$wash_recv_out=$gmtsProdDataArr[0][csf('wash_recv_qnty_out')];
		$total_issue_recv=$wash_recv_in+$wash_recv_out;
		$wash_reject_qty=$gmtsProdDataArr[0][csf('wash_reject_qnty')];
		$balance=$total_issue-($total_issue_recv+$wash_reject_qty);

		$wash_input_qnty_in_perc=($wash_recv_in/$wash_req_qty)*100;
		$wash_input_qnty_out_perc=($wash_recv_out/$wash_req_qty)*100;
		$wash_total_issue_perc=($total_issue/$wash_req_qty)*100;
		$wash_total_recv_perc=($total_issue_recv/$wash_req_qty)*100;
		$wash_recv_reject_perc=($wash_reject_qty/$wash_req_qty)*100;
		$wash_recv_balance=($balance/$wash_req_qty)*100;



		?>
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Garments Washing</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Total Issue</th>
                    <th width="90">Wash Inside</th>
                    <th width="90">Wash Subcon</th>
                    <th width="90">Total Rcv Wash</th>
                    <th width="90">Total Reject</th>
                    <th width="90">Balance</th>
                </tr>
			</thead>
			<tr bgcolor="#FFFFFF">
				<td>Quantity</td>
				<td align="right"><? echo number_format($wash_req_qty,4,'.',''); ?>&nbsp;</td>
				<td align="right"><? echo number_format($total_issue,4,'.',''); ?>&nbsp;</td>
				<td align="right"><? echo number_format($wash_recv_in,4,'.',''); ?>&nbsp;</td>
				<td align="right"><?  echo number_format($wash_recv_out,4,'.',''); ?>&nbsp;</td>
				<td align="right"><? echo number_format($total_issue_recv,4,'.',''); ?>&nbsp;</td>
				<td align="right"><? echo number_format($wash_reject_qty,4,'.',''); ?>&nbsp;</td>
	        	<td align="right"><? echo number_format($balance,4,'.','');  ?>&nbsp;</td>
	        </tr>
			   <tr bgcolor="#E9F3FF">
                    <td>In %</td>
					<td>&nbsp;</td>
					<td align="right"><? echo number_format($wash_total_issue_perc,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($wash_input_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($wash_input_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($wash_total_recv_perc,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($wash_recv_reject_perc,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($wash_recv_balance,2,'.',''); ?>&nbsp;</td>
               </tr>
        </table>
        <br><br>
        <?
        $sew_input_qnty_in=$gmtsProdDataArr[0][csf('sew_input_qnty_in')];
		$sew_input_qnty_out=$gmtsProdDataArr[0][csf('sew_input_qnty_out')];
		$total_sew_issued=$sew_input_qnty_in+$sew_input_qnty_out;

		$sew_input_qnty_in_perc=($sew_input_qnty_in/$tot_plun_cut_qnty)*100;
		$sew_input_qnty_out_perc=($sew_input_qnty_out/$tot_plun_cut_qnty)*100;
		$total_sew_issued_perc=($total_sew_issued/$tot_plun_cut_qnty)*100;

		$sew_balance_qnty=$tot_po_qnty-$total_sew_issued;
		$sew_balance_qnty_perc=($sew_balance_qnty/$tot_plun_cut_qnty)*100;
		?>
        <table width="550" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="5" width="460">Input</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Input Inside</th>
                    <th width="90">Input SubCon</th>
                    <th width="90">Total Input</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($tot_plun_cut_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_input_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_input_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_issued,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_balance_qnty,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($sew_input_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_input_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_issued_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_balance_qnty_perc,2,'.',''); ?>&nbsp;</td>
             </tr>
        </table>
        <br>
        <br />
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Accessories Status</th>
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
			$trim= new trims($condition);
			$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
			//print_r($trim_qty_arr);
			$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
			$trims_array=array();
			$trimsDataArr=sql_select("select b.item_group_id,
									sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
									sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
									from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($all_po_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
			foreach($trimsDataArr as $row)
			{
				$trims_array[$row[csf('item_group_id')]]['recv']=$row[csf('recv_qnty')];
				$trims_array[$row[csf('item_group_id')]]['iss']=$row[csf('issue_qnty')];
			}
			unset($trimsDataArr);

			$trimsDataArr=sql_select("select b.id, a.job_no, a.costing_per, b.trim_group, b.cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' group by b.id,b.trim_group, a.job_no, a.costing_per, b.cons_uom");
		//	echo "select a.job_no, a.costing_per, b.trim_group, b.cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' group by b.trim_group, a.job_no, a.costing_per, b.cons_uom";
			$i=1; $tot_accss_req_qnty=0; $tot_recv_qnty=0; $tot_iss_qnty=0; $tot_recv_bl_qnty=0; $tot_trims_left_over_qnty=0;
			foreach($trimsDataArr as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$dzn_qnty='';
				if($row[csf('costing_per')]==1) $dzn_qnty=12;
				else if($row[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($row[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($row[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;

				$dzn_qnty=$dzn_qnty*$ratio;
        		$accss_req_qnty=$trim_qty_arr[$row[csf('id')]];//($row[csf('cons_dzn_gmts')]/$dzn_qnty)*$tot_plun_cut_qnty;

				$trims_recv=$trims_array[$row[csf('trim_group')]]['recv'];
				$trims_issue=$trims_array[$row[csf('trim_group')]]['iss'];
				$recv_bl=$accss_req_qnty-$trims_recv;
				$trims_left_over=$trims_recv-$trims_issue;
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>">
                    <td>&nbsp;</td>
                    <td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($accss_req_qnty,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_recv,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($recv_bl,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_issue,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_left_over,2,'.',''); ?>&nbsp;</td>
                </tr>
            <?
				$tot_accss_req_qnty+=$accss_req_qnty;
				$tot_recv_qnty+=$trims_recv;
				$tot_recv_bl_qnty+=$recv_bl;
				$tot_iss_qnty+=$trims_issue;
				$tot_trims_left_over_qnty+=$trims_left_over;
				$i++;
			}
			unset($trimsDataArr);
			$tot_trims_left_over_qnty_perc=($tot_trims_left_over_qnty/$tot_recv_qnty)*100;
			?>
            <tfoot>
                <tr style="display:none">
                    <th>Total</th>
                    <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                    <th align="right"><? echo number_format($tot_accss_req_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_iss_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_trims_left_over_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>
         <br />
        <?
			$sew_recv_qnty_in=$gmtsProdDataArr[0][csf('sew_recv_qnty_in')];
			$sew_recv_qnty_out=$gmtsProdDataArr[0][csf('sew_recv_qnty_out')];
			$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;

			$sew_recv_qnty_in_perc=($sew_recv_qnty_in/$tot_plun_cut_qnty)*100;
			$sew_recv_qnty_out_perc=($sew_recv_qnty_out/$tot_plun_cut_qnty)*100;
			$total_sew_recv_perc=($total_sew_recv/$tot_plun_cut_qnty)*100;

			$sew_balance_recv_qnty=$tot_plun_cut_qnty-$total_sew_recv;
			$sew_balance_recv_qnty_perc=($sew_balance_recv_qnty/$tot_plun_cut_qnty)*100;

			$sew_reject_qnty=$gmtsProdDataArr[0][csf('sew_reject_qnty')];
			$sew_reject_qnty_perc=($sew_reject_qnty/$total_print_recv)*100;
		?>
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Sewing Production</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Input Revd</th>
                    <th width="90">Sew Inside</th>
                    <th width="90">Sew SubCon</th>
                    <th width="90">Total Sew</th>
                    <th width="90">Sew Balance</th>
                    <th>Reject</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($tot_plun_cut_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_issued,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_recv_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_recv_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_recv,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_balance_recv_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_reject_qnty,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_issued_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_recv_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_recv_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_recv_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_balance_recv_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_reject_qnty_perc,2,'.',''); ?>&nbsp;</td>
             </tr>
        </table>
        <br>
        <?
        $finish_qnty_in=$gmtsProdDataArr[0][csf('finish_qnty_in')];
		$finish_qnty_out=$gmtsProdDataArr[0][csf('finish_qnty_out')];
		$total_finish_qnty=$finish_qnty_in+$finish_qnty_out;

		$finish_qnty_in_perc=($finish_qnty_in/$tot_plun_cut_qnty)*100;
		$finish_qnty_out_perc=($finish_qnty_out/$tot_plun_cut_qnty)*100;
		$total_finish_qnty_perc=($total_finish_qnty/$tot_plun_cut_qnty)*100;

		$finish_balance_qnty=$tot_plun_cut_qnty-$total_finish_qnty;
		$finish_balance_qnty_perc=($finish_balance_qnty/$tot_plun_cut_qnty)*100;

		$finish_reject_qnty=$gmtsProdDataArr[0][csf('finish_reject_qnty')];
		$finish_reject_qnty_perc=($finish_reject_qnty/$total_print_recv)*100;
		unset($gmtsProdDataArr);
		?>
        <br />
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Garment Finishing</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Received</th>
                    <th width="90">Finish Inside</th>
                    <th width="90">Finish SubCon</th>
                    <th width="90">Total Finish</th>
                    <th width="90">Finish Balance</th>
                    <th>Reject</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($tot_plun_cut_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_recv,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finish_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_balance_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_recv_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_in_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_out_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finish_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty_perc,2,'.',''); ?>&nbsp;</td>
             </tr>
        </table>
        <br />
          <br />
        <table width="450" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="4" width="360">Left Over</th>
                </tr>
                <tr>
                    <th width="90">Gray Fab.</th>
                    <th width="90">Finish Fab.</th>
                    <th width="">Garment</th>

                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($left_over,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_left_over,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($left_over_finish_gmts,0,'.',''); ?>&nbsp;</td>

            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td align="right"><? echo number_format($left_over_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_left_over_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($left_over_finish_gmts_perc,2,'.',''); ?>&nbsp;</td>

             </tr>
        </table>
        <br />
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
            	<tr>
                    <th width="450" colspan="5">Reject</th>
                    <th width="270" colspan="3">Process Loss</th>
                </tr>
                <tr>
                    <th width="90">Particulars</th>
                    <th width="90">Print Gmts.</th>
                    <th width="90">Emb Gmts.</th>
                    <th width="90">Sewing Gmts.</th>
                    <th width="90">Finishing Gmts.</th>
                    <th width="90">Yarn</th>
                    <th width="90">Dyeing</th>
                    <th>Cutting</th>
            	</tr>
            </thead>
            <?
			$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
			$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
            $print_reject_qnty_perc=($print_reject_qnty/$total_print_recv)*100;
			 $emb_reject_qnty_perc=($emb_reject_qnty/$total_print_recv)*100;


		$sew_reject_qnty_perc=($sew_reject_qnty/$total_print_recv)*100;
		$finish_reject_qnty_perc=($finish_reject_qnty/$total_print_recv)*100;

			?>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($print_reject_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($emb_reject_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_reject_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_finishing,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_process_loss,2,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td align="right"><? echo number_format($print_reject_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($emb_reject_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_reject_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_finishing_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_process_loss_perc,2,'.',''); ?>&nbsp;</td>
             </tr>
        </table>

        </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$cbo_search_type**$type";
    exit();
}

if($action=="report_generate6")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$ref_no=str_replace("'","",$txt_ref_no);
	$file_no=str_replace("'","",$txt_file_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$type=str_replace("'","",$type);

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}

	$ship_date_cond="";$ship_date_cond2="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($cbo_date_category==1)
		{
			$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
			$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_date_category==2)
		{
			$ship_date_cond="and c.ex_factory_date between '$txt_date_from' and '$txt_date_to' AND c.is_deleted = 0 AND c.status_active = 1";
			$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_date_category==3) //Ref Closing date
		{
			$ship_date_cond2="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
			$ship_date_cond="and d.closing_date between '$txt_date_from' and '$txt_date_to'";
		}
	}


	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
	$order_cond="";
	if(trim($ref_no)!="") $ref_no_cond="and b.grouping in('$ref_no')";
	if(trim($file_no)!="") $file_no_cond="and b.file_no in('$file_no')";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	if($txt_order_id!="") $order_cond="and b.id in($txt_order_id)";
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	//if($txt_ex_date_form!="" && $txt_ex_date_to!="")
	if($cbo_date_category==2) // Ex-Fact Date
	{
		$sql_po="SELECT a.buyer_name, a.id as JOB_ID, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty
		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
		where a.job_no=b.job_no_mst and  b.id=c.po_break_down_id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else if($cbo_date_category==1)  // Ship Date Date
	{
		$sql_po="SELECT a.buyer_name, a.id as JOB_ID, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty
		from wo_po_details_master a, wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1
		where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	else if($cbo_date_category==3) //ref Closing
	{
		$sql_po="SELECT a.buyer_name, a.id as JOB_ID, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date,b.unit_price,b.po_total_price, c.ex_factory_date,d.closing_date,
		CASE WHEN c.entry_form!=85  THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty
		from wo_po_details_master a, inv_reference_closing d,wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id
		where a.job_no=b.job_no_mst  and  b.id=d.inv_pur_req_mst_id  and d.reference_type=163 and d.closing_status=1 and b.shiping_status=3  and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond order by a.job_no, b.pub_shipment_date, b.id";
	}
	//echo $sql_po;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array(); $poIdArr=array();
	foreach($sql_po_result as $row)
	{
		$poIdArr[$row[csf("po_id")]]=$row[csf("po_id")];

		$result_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
		$result_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("po_id")]]["job_id"]=$row["JOB_ID"];
		$result_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$result_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["ref_no"]=$row[csf("grouping")];
		$result_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("po_id")]]["po_total_price"]=$row[csf("po_total_price")];
		$result_data_arr[$row[csf("po_id")]]["unit_price"]=$row[csf("unit_price")];
		$result_data_arr[$row[csf("po_id")]]["po_qnty"]=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")];
		$result_data_arr[$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("po_id")]]["closing_date"]=$row[csf("closing_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$result_data_arr[$row[csf("po_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
		$JobArr[]="'".$row[csf('job_no')]."'";
	}
	$all_po_id=implode(",",$poIdArr);

	$sql_po_ref="SELECT b.inv_pur_req_mst_id as po_id,max(b.closing_date) as  closing_date from inv_reference_closing b
	where b.reference_type=163 and b.closing_status=1  and b.is_deleted=0 and b.status_active=1  ".where_con_using_array($poIdArr,0,'b.inv_pur_req_mst_id')." group by  b.inv_pur_req_mst_id order by b.inv_pur_req_mst_id desc";
	$sql_po_ref_result=sql_select($sql_po_ref);
	foreach($sql_po_ref_result as $row)
	{
		$Ref_closing_arr[$row[csf("po_id")]]=$row[csf("closing_date")];
	}
	//$JobNoArr=implode(",",$JobArr);
	//$yarn= new yarn($JobArr,'job');
	//$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	//print_r($yarn_qty_arr);
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(!empty($JobArr)){
	 $condition->po_id_in("$all_po_id");
	}
	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	//$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
	$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();

	$booking_req_arr=array();
	$sql_wo=sql_select("SELECT b.po_break_down_id,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	sum(CASE WHEN a.fabric_source=1 and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	sum(b.fin_fab_qnty) as fin_fab_qnty
	from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id");
	//and b.po_break_down_id in ($all_po_id)

	foreach ($sql_wo as $brow)
	{
		$booking_req_arr[$brow[csf("po_break_down_id")]]['gray']=$brow[csf("grey_req_qnty")];
		$booking_req_arr[$brow[csf("po_break_down_id")]]['woven']=$brow[csf("woven_req_qnty")];
		$booking_req_arr[$brow[csf("po_break_down_id")]]['fin']=$brow[csf("fin_fab_qnty")];
	}

	$sql_res=sql_select("SELECT b.po_break_down_id as po_id,
	sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty,
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_qnty,
	sum(CASE WHEN b.entry_form!=85 $ship_date_cond2 THEN b.ex_factory_qnty ELSE 0 END) as curr_ex_fact_qnty
	from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id");
	$ex_factory_qty_arr=array();
	foreach($sql_res as $row)
	{
		$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('return_qnty')];
		$tot_ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('ex_fact_qnty')];
		$curr_tot_ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('curr_ex_fact_qnty')];
	}

	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$dataArrayYarnReq=array();
	$yarn_sql="SELECT job_no, sum(avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarnReq[$yarnRow[csf('job_no')]]=$yarnRow[csf('qnty')];
	}

	$reqDataArray=sql_select("SELECT a.po_break_down_id, sum((b.requirment/b.pcs)*a.plan_cut_qnty) as grey_req, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 ".where_con_using_array($poIdArr,0,'a.po_break_down_id')." group by a.po_break_down_id");//and a.po_break_down_id in ($all_po_id)
	$grey_finish_require_arr=array();
	foreach($reqDataArray as $row)
	{
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["grey_req"]=$row[csf("grey_req")];
		$grey_finish_require_arr[$row[csf("po_break_down_id")]]["finish_req"]=$row[csf("finish_req")];
	}

	$yarnDataArr=sql_select("SELECT a.po_breakdown_id,
	sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
	sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c
	where a.trans_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')."  and b.item_category=1 and c.issue_purpose in (1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
	group by a.po_breakdown_id");//and a.po_breakdown_id in($all_po_id)
	$yarn_issue_arr=array();
	foreach($yarnDataArr as $row)
	{
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_in"]=$row[csf("issue_qnty_in")];
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_out"]=$row[csf("issue_qnty_out")];
	}

	$yarnReturnDataArr=sql_select("SELECT a.po_breakdown_id,
	sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
	sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
	from order_wise_pro_details a, inv_transaction b, inv_receive_master c
	where a.trans_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
	group by a.po_breakdown_id");

	$yarn_issue_rtn_arr=array();
	foreach($yarnReturnDataArr as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
	}


	$dataArrayTrans=sql_select("SELECT po_breakdown_id,
	sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
	sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
	sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
	sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
	sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
	sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish
	from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(11,13,15)  ".where_con_using_array($poIdArr,0,'po_breakdown_id')."
	group by po_breakdown_id");//and po_breakdown_id in($all_po_id)

	$transfer_data_arr=array();
	foreach($dataArrayTrans as $row)
	{
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_yarn"]=$row[csf("transfer_in_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_yarn"]=$row[csf("transfer_out_qnty_yarn")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_knit"]=$row[csf("transfer_in_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_knit"]=$row[csf("transfer_out_qnty_knit")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_finish"]=$row[csf("transfer_in_qnty_finish")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_finish"]=$row[csf("transfer_out_qnty_finish")];
	}

	$prodKnitDataArr=sql_select("SELECT a.po_breakdown_id,
	sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
	sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
	sum(CASE WHEN a.entry_form=22 THEN a.quantity ELSE 0 END) AS knit_qnty_rec
	from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and c.item_category=13 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_in"]=$row[csf("knit_qnty_in")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_out"]=$row[csf("knit_qnty_out")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec"]=$row[csf("knit_qnty_rec")];
	}

	$prodFinDataArr=sql_select("SELECT a.po_breakdown_id,
	sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
	sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
	sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec
	from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9 group by a.po_breakdown_id");

	$finish_prod_arr=array();
	foreach($prodFinDataArr as $row)
	{
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in"]=$row[csf("finish_qnty_in")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out"]=$row[csf("finish_qnty_out")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["woven_rec"]=$row[csf("woven_rec")];
	}
	$issueData=sql_select("SELECT po_breakdown_id,
	sum(CASE WHEN entry_form=16 THEN quantity ELSE 0 END) AS grey_issue_qnty,
	sum(CASE WHEN entry_form=61 THEN quantity ELSE 0 END) AS grey_issue_qnty_roll_wise,
	sum(CASE WHEN entry_form=18 THEN quantity ELSE 0 END) AS issue_to_cut_qnty,
	sum(CASE WHEN entry_form=71 THEN quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
	sum(CASE WHEN entry_form=19 THEN quantity ELSE 0 END) AS woven_issue
	from order_wise_pro_details where entry_form in(16,18,19,61,71)  ".where_con_using_array($poIdArr,0,'po_breakdown_id')." and status_active=1 and is_deleted=0 group by po_breakdown_id");

	$grey_cut_issue_arr=array();
	foreach($issueData as $row)
	{
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty"]=$row[csf("grey_issue_qnty")]+$row[csf("grey_issue_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["issue_to_cut_qnty"]=$row[csf("issue_to_cut_qnty")]+$row[csf("issue_to_cut_qnty_roll_wise")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["woven_issue"]=$row[csf("woven_issue")];
	}
	$trimsDataArr=sql_select("SELECT a.po_breakdown_id,
	sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
	sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
	from order_wise_pro_details a, product_details_master b where a.prod_id=b.id ".where_con_using_array($poIdArr,0,'a.po_breakdown_id')." and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");
	foreach($trimsDataArr as $row)
	{
		$trims_array[$row[csf('po_breakdown_id')]]['recv']=$row[csf('recv_qnty')];
		$trims_array[$row[csf('po_breakdown_id')]]['iss']=$row[csf('issue_qnty')];
	}

	$sql_consumtiont_qty=sql_select("SELECT b.po_break_down_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
	from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
	where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 ".where_con_using_array($poIdArr,0,'b.po_break_down_id')." group by b.po_break_down_id, c.body_part_id ");
	$finish_consumtion_arr=array();
	foreach($sql_consumtiont_qty as $row_consum)
	{
		$con_avg=0;
		$con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];
		$finish_consumtion_arr[$row_consum[csf('po_break_down_id')]]+=$con_avg;
	}

	$gmtsProdDataArr=sql_select("SELECT  po_break_down_id,
	sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
	sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty_in,
	sum(CASE WHEN production_type=2 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_issue_qnty_out,
	sum(CASE WHEN production_type=3 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_recv_qnty_in,
	sum(CASE WHEN production_type=3 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_recv_qnty_out,
	sum(CASE WHEN production_type=2 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_in,
	sum(CASE WHEN production_type=2 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_out,
	sum(CASE WHEN production_type=3 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_in,
	sum(CASE WHEN production_type=3 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_out,
	sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_input_qnty_in,
	sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_input_qnty_out,
	sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,
	sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
	sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS finish_qnty_in,
	sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS finish_qnty_out,
	sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
	sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,
	sum(CASE WHEN production_type=3 and embel_name=1 THEN reject_qnty ELSE 0 END) AS print_reject_qnty,
	sum(CASE WHEN production_type=3 and embel_name=2 THEN reject_qnty ELSE 0 END) AS emb_reject_qnty,
	sum(CASE WHEN production_type=5 THEN reject_qnty ELSE 0 END) AS sew_reject_qnty,
	sum(CASE WHEN production_type=8 THEN reject_qnty ELSE 0 END) AS finish_reject_qnty,
	sum(CASE WHEN production_type=1 THEN reject_qnty ELSE 0 END) AS cutting_reject_qnty,
	sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty
	from pro_garments_production_mst where  is_deleted=0 and status_active=1 ".where_con_using_array($poIdArr,0,'po_break_down_id')." group by po_break_down_id");

	$garment_prod_data_arr=array();
	foreach($gmtsProdDataArr as $row)
	{
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_qnty']=$row[csf("cutting_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_out']=$row[csf("finish_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_in']=$row[csf("wash_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_out']=$row[csf("wash_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_reject_qnty']=$row[csf("print_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_reject_qnty']=$row[csf("emb_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_reject_qnty']=$row[csf("sew_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_reject_qnty']=$row[csf("finish_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_reject_qnty']=$row[csf("cutting_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['iron_rej_qnty']=$row[csf("iron_rej_qnty")];
	}
	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	if($cbo_search_type==1)
	{
		$tbl_width=7280;

		$ship_date_html="Shipment Date";
		$ex_fact_date_html="Ex-Fact. Date";
	}
	else
	{
		$tbl_width=6650;
		$ship_date_html="Last Shipment Date";
		$ex_fact_date_html="Last Ex-Fact. Date";
	}
	ob_start();
	?>
        <div style="width:100%">
			<table width="<? echo $tbl_width;?>">
				<tr>
					<td align="center" width="100%" colspan="70" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
			</table>
			<table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
				<thead>
					<tr style="font-size:13px">
						<th width="40" >SL</th>
						<th width="110" >Buyer</th>
						<th width="50" >Job Year</th>
						<th width="50" >Job No</th>
						<th width="100" >Style No</th>

						<?
						if($cbo_search_type==1)
						{
							?>
							<th width="100" >Order No</th>
							<?
						}
						?>
						<th width="80" >Order Qty.(Pcs)</th>
						<th width="80">FOB</th>
						<th width="80">Order Value</th>

						<?
						if($cbo_search_type==1)
						{
							?>
							<th width="70" ><? echo $ship_date_html; ?></th>
							<th width="70"><? echo $ex_fact_date_html; ?></th>

							<?
						}
						?>
						<th width="70"><? echo 'Closing Date'; ?></th>
						<th width="80">Yarn Req.<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
						<th width="80">Yarn Issued In</th>
						<th width="80">Yarn Issued Out</th>
						<th width="80">Yarn Trans In</th>
						<th width="80">Yarn Trans Out</th>
						<th width="80">Yarn Total Issued</th>
						<th width="80">Yarn Under or Over Issued</th>

						<th width="80">Knit. Gray Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
						<th width="80">Knit. Prod Inside</th>
						<th width="80">Knit. Prod SubCon</th>
						<th width="80">Knit. Trans. In</th>
						<th width="80">Knit. Trans. Out</th>
						<th width="80">Knit. Total Prod.</th>
						<th width="80">Knit. Receive</th>
						<th width="80">Knit. Process Loss</th>
						<th width="80">Knit. Under or Over Prod.</th>
						<th width="80">Knit. Issued To Dyeing</th>
						<th width="80">Knit. Left Over</th>

						<th width="80">Fin Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
						<th width="80">Fin Prod. Inside</th>
						<th width="80">Fin Prod. SubCon</th>
						<th width="80">Fin Trans. In</th>
						<th width="80">Fin Trans. Out</th>
						<th width="80">Fin Prod. Total</th>
						<th width="80">Fin Process Loss</th>
						<th width="80">Fin Under or Over</th>
						<th width="80">Fin Issue To Cut</th>
						<th width="80">Fin Left Over</th>

						<th width="80">Woven Fabric Req.</th>
						<th width="80">Woven Fabric Received</th>
						<th width="80">Woven Fab. Rec. Bal.</th>
						<th width="80">Woven Fabric Issue</th>
						<th width="80">Woven Fabric Issue Bal.</th>


						<th width="80">Gmts. Req (Po Qty)</th>
						<th width="80">Cutting Qty</th>
						<th width="80">Gmts. Print Issued In</th>
						<th width="80">Gmts. Print Issued SubCon</th>
						<th width="80">Gmts. Total Print Issued</th>
						<th width="80">Gmts. Print Rec. Inside</th>
						<th width="80">Gmts. Print Rec. SubCon</th>
						<th width="80">Gmts. Total Rec. Print</th>
						<th width="80">Gmts. Reject</th>

						<th width="80">Sew. Input Inside</th>
						<th width="80">Sew. Input SubCon</th>
						<th width="80">Total Sew. Input</th>
						<th width="80">Sew. Input Balance</th>
						<th width="100">Accessories Status</th>
						<th width="80">Sew. Out Inside</th>
						<th width="80">Sew Out SubCon</th>
						<th width="80">Total Out Sew</th>
						<th width="80">Sew Out Balance</th>
						<th width="80">Sew Out Reject</th>

						<th width="80">Wash Inside</th>
						<th width="80">Wash SubCon</th>
						<th width="80">Total Wash</th>
						<th width="80">Wash Balance</th>

						<th width="80">Finish Inside</th>
						<th width="80">Finish SubCon</th>
						<th width="80">Total Finish</th>
						<th width="80">Finish Balance</th>
						<th width="80">Finish Reject</th>
						<th width="80">Total Reject</th>
						<th width="80">Current Ex-Fact.Qty</th>
						<th width="80">TTL Ex-Factory</th>
						<th width="80">Left Over</th>
						<th width="80">Short/Exces Ex-Fac.Qty</th>
						<th width="80">Process Loss Yarn</th>
						<th width="80" title="Knit Proces Loss*100/Total Yarn Issue">Process Loss Yarn &percnt;</th>
						<th width="80">Process Loss Dyeing</th>
						<th width="80" title="(Knit issue to dyeing- Fin Production Total)*100/Knit issue to dyeing">Process Loss Dyeing &percnt;</th>
						<th width="80">Process Loss Cutting</th>
						<th  width="80" title="(Total Cutting Qty-Total Order Qty)*100/total ordr qty">Process Loss Cutting &percnt;</th>
						<th width="80" title="(Actual cut qyt-TTL Ex-fact Qtys">Cut to Ship</th>
						<th width="80" title="TTL Ex-Fact Qty/Actual Cut Qty*100">Cut to Ship Percentage</th>
						<th width="80" title=" Order Qty (Pcs)-TTL Ex-fact Qty">Order to Ship Qty</th>
						<th width="80" title="TTL Ex-Fact Qty/Order Qty (pcs)*100">Order to Ship Percentage</th>
						<th width="100">Import Status</th>
						<th>Export Status</th>

					</tr>
				</thead>
			</table>
        	<div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" >
					<?
					$i=1;$tot_order_value=$tot_shortExcess_exFactory_qty=0;
					if($cbo_search_type==1)
					{
						foreach($result_data_arr as $po_id=>$val)
						{
							$ratio=$val["ratio"];$ref_no=$val["ref_no"];
							$tot_po_qnty=$val["po_qnty"];
							$exfactory_qnty=$tot_ex_factory_qty_arr[$po_id]-$ex_factory_qty_arr[$po_id];//$tot_ex_factory_qty_arr[$po_id];
							$current_ex_fact_qnty=$curr_tot_ex_factory_qty_arr[$po_id];//$val["ex_factory_qnty"];

							$plan_cut_qty=$val["plan_cut"];
							$job_no=$val["job_no"];
							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$ratio;

							$yarn_req_job=$yarn_qty_arr[$po_id];//$dataArrayYarnReq[$job_no];
							$yarn_required=$yarn_qty_arr[$po_id];//$plan_cut_qty*($yarn_req_job/$dzn_qnty);
							$yarn_issue_inside=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
							$yarn_issue_outside=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
							$transfer_in_qnty_yarn=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
							$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
							$under_over_issued=$yarn_required-$total_issued;

							$grey_fabric_req_qnty=$booking_req_arr[$po_id]['gray'];//$grey_finish_require_arr[$po_id]["grey_req"];
							$knit_qnty_in=$kniting_prod_arr[$po_id]["knit_qnty_in"];
							$knit_qnty_out=$kniting_prod_arr[$po_id]["knit_qnty_out"];
							$knit_gray_rec=$kniting_prod_arr[$po_id]["knit_qnty_rec"];
							$transfer_in_qnty_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];

							$total_knitting=$knit_qnty_in+$knit_qnty_out+$transfer_in_qnty_knit-$transfer_out_qnty_knit;

							$process_loss=($yarn_issue_inside+$yarn_issue_outside)-($knit_qnty_in+$knit_qnty_out);
							$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
							$issuedToDyeQnty=$grey_cut_issue_arr[$po_id]["grey_issue_qnty"];
							$left_over=$total_knitting-$issuedToDyeQnty;

							$finish_fabric_req_qnty=$booking_req_arr[$po_id]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							$finish_qnty_in=$finish_prod_arr[$po_id]["finish_qnty_in"];
							$finish_qnty_out=$finish_prod_arr[$po_id]["finish_qnty_out"];
							$transfer_in_qnty_finish=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];
							$total_finishing=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
							$process_loss_dyeing=$issuedToDyeQnty-($finish_qnty_in+$finish_qnty_out);
							$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
							$issuedToCutQnty=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];
							$finish_left_over=$total_finishing-$issuedToCutQnty;

							$wovenReqQty=$booking_req_arr[$po_id]['woven'];
							$wovenRecQty=$finish_prod_arr[$po_id]["woven_rec"];
							$wovenFabRecBal=$wovenReqQty-$wovenRecQty;
							$wovenIssueQty=$grey_cut_issue_arr[$po_id]["woven_issue"];
							$wovenFabIssueBal=$wovenRecQty-$wovenIssueQty;


							$cuttingQty=$garment_prod_data_arr[$po_id]['cutting_qnty'];
							if($finish_consumtion_arr[$po_id] !=0){
								$possible_cut_pcs=$issuedToCutQnty/$finish_consumtion_arr[$po_id];
							}
							else{
								$possible_cut_pcs = 0;
							}

							$cutting_process_loss=$possible_cut_pcs-$cuttingQty;

							$print_issue_qnty_in=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
							$print_issue_qnty_out=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
							$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
							$print_recv_qnty_in=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
							$print_recv_qnty_out=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
							$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
							$print_reject_qnty=$garment_prod_data_arr[$po_id]['print_reject_qnty'];

							$sew_input_qnty_in=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
							$sew_input_qnty_out=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
							$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
							$sew_input_balance_qnty=$tot_po_qnty-$total_sew_input;

							$sew_recv_qnty_in=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
							$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;
							$sew_reject_qnty=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];
							$cutting_reject_qnty=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

							$wash_recv_qnty_in=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
							$wash_recv_qnty_out=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
							$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
							$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

							$gmt_finish_in=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
							$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
							$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
							$finish_reject_qnty=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
							$left_over_finish_gmts=$total_gmts_finish_qnty-$exfactory_qnty;

							$short_excess_exFactoryQty=$tot_po_qnty-$exfactory_qnty;

							$trims_recv=$trims_array[$po_id]['recv'];
							$trims_issue=$trims_array[$po_id]['iss'];
							$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

							$emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
							$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;


							$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
							$total_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$finish_reject_qnty+$iron_rej_qnty+$emb_reject_qnty;
							$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($total_reject_qnty,2).'</a></p>';
							if($cbo_date_category==1 || $cbo_date_category==2)
							{
								$closing_date=$Ref_closing_arr[$po_id];
							}
							else
							{
								$closing_date=$val["closing_date"];
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="40"><? echo $i; ?></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$val["buyer_name"]]; ?></div></td>
								<td width="50" align="center"><p><? echo $val["job_year"]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $val["style_ref_no"]; ?></div></td>

								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $val["po_number"]; ?></div></td>
								<td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($val["unit_price"]); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($val["po_total_price"]); ?></p></td>
								<td width="70"><p><? if(trim($val["pub_shipment_date"])!="" && trim($val["pub_shipment_date"])!='0000-00-00') echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</p></td>
								<td width="70"><p><? if(trim($val["ex_factory_date"])!="" && trim($val["ex_factory_date"])!='0000-00-00') echo change_date_format($val["ex_factory_date"]); ?>&nbsp;</p></td>
								<td width="70"><p><? if(trim($closing_date)!="" && trim($closing_date)!='0000-00-00') echo change_date_format($closing_date); ?>&nbsp;</p></td>

								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_inside,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_outside,2);?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_issued,2); ?></td>

								<td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_gray_rec,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToDyeQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToCutQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($wovenReqQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenRecQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenFabRecBal,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenIssueQty,2); ?></td>
								<td align="right" width="80"><? echo number_format($wovenFabIssueBal,2); ?></td>

								<td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($plan_cut_qty); ?></td>
								<td align="right" width="80"><? echo number_format($cuttingQty); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($sew_input_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_input); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_balance_qnty); ?></td>

								<td align="center" width="100"><a href="javascript:open_trims_dtls('<? echo $po_id;?>','<? echo $tot_po_qnty; ?>','<? echo $ratio; ?>','Trims Info','trims_popup')">View</a></td>

								<td align="right" width="80"><? echo number_format($sew_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($wash_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($wash_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_wash_recv); ?></td>
								<td align="right" width="80"><? echo number_format($wash_balance_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($gmt_finish_in); ?></td>
								<td align="right" width="80"><? echo number_format($gmt_finish_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_gmts_finish_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_balance_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>
								<td align="right" width="80"><? echo $reject_button; ?></td>
								<td align="right" width="80"><? echo number_format($current_ex_fact_qnty); ?></td>
								<td align="right" width="80">
								<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($exfactory_qnty); ?></a>
								<? //echo number_format($exfactory_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
								<td align="right" width="80"><? echo number_format($short_excess_exFactoryQty); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss); ?></td>
								<td align="right" width="80"><?
									if($total_issued!=0){
										$process_loss_yern_per = ($process_loss*100)/$total_issued;
									}
									else{
										$process_loss_yern_per = 0;
									}
									echo number_format($process_loss_yern_per);
								?></td>
								<td align="right" width="80"><? echo number_format($process_loss_dyeing); ?></td>
								<td align="right" width="80"><?
									if($issuedToDyeQnty != 0){
										$process_loss_dyeing_per = ($issuedToDyeQnty-$total_finishing)*100/$issuedToDyeQnty;
									}
									else{
										$process_loss_dyeing_per = 0;
									}
									echo number_format($process_loss_dyeing_per);
								?></td>
								<td align="right" width="80"><? echo number_format($cutting_process_loss); ?></td>
								<td align="right" width="80"><?
									if($tot_po_qnty!=0){
										$process_loss_cutting_per = ($cuttingQty-$tot_po_qnty)*100/$tot_po_qnty;
									}
									else{
										$process_loss_cutting_per = 0;
									}
									echo number_format($process_loss_cutting_per);
								?></td>
								<td align="right" width="80"><? $cut_to_ship=$cuttingQty-$exfactory_qnty; echo number_format($cut_to_ship,0); ?></td>
								<td align="right" width="80"><? $cut_to_ship_per=$exfactory_qnty/$cuttingQty*100;  if($cuttingQty) echo number_format($cut_to_ship_per,0);else echo 0; ?></td>
								<td align="right" width="80"><? $order_to_ship=$tot_po_qnty-$exfactory_qnty;echo number_format($order_to_ship); ?></td>
								<td align="right" width="80"><?  $order_to_ship_per=$exfactory_qnty/$tot_po_qnty*100;if($exfactory_qnty) echo number_format($order_to_ship_per);else echo 0; ?></td>
								<td align="center" width="100"><a href="##" onClick="fnc_open_view('import_popup','Import Statement',<?=$cbo_company_name;?>,'<?=$val['job_id'];?>','<?=$val['job_no'];?>','<?=$po_id;?>')">View</a></td>
								<td align="center" onClick="fnc_open_view('export_popup','Export Statement',<?=$cbo_company_name;?>,'<?=$val['job_id'];?>','<?=$val['job_no'];?>','<?=$po_id;?>')"><a href="##" >View</a></td>
							</tr>
							<?
							$tot_order_qty+=$tot_po_qnty;
							$tot_order_value+=$val["po_total_price"];
							$tot_plan_cut_qty+=$plan_cut_qty;
							$tot_cut_to_ship+=$cut_to_ship;
							$tot_order_to_ship+=$order_to_ship;
							$tot_current_ex_fact_qnty+=$current_ex_fact_qnty;

							$tot_yarn_req_qty+=$yarn_required;
							$tot_yarn_issIn_qty+=$yarn_issue_inside;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;
							$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
							$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
							$tot_yarn_issue_qty+=$total_issued;
							$tot_yarn_undOvr_qty+=$under_over_issued;

							$tot_grey_req_qty+=$grey_fabric_req_qnty;
							$tot_grey_in_qty+=$knit_qnty_in;
							$tot_grey_out_qty+=$knit_qnty_out;
							$tot_grey_rec_qty+=$knit_gray_rec;
							$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
							$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
							$tot_grey_qty+=$total_knitting;
							$tot_grey_prLoss_qty+=$process_loss;
							$tot_grey_undOver_qty+=$under_over_prod;
							$tot_grey_issDye_qty+=$issuedToDyeQnty;
							$tot_grey_lftOver_qty+=$left_over;

							$tot_fin_req_qty+=$finish_fabric_req_qnty;
							$tot_fin_in_qty+=$finish_qnty_in;
							$tot_fin_out_qty+=$finish_qnty_out;
							$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
							$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
							$tot_fin_qty+=$total_finishing;
							$tot_fin_prLoss_qty+=$process_loss_finishing;
							$tot_fin_undOver_qty+=$under_over_finish_prod;
							$tot_fin_issCut_qty+=$issuedToCutQnty;
							$tot_fin_lftOver_qty+=$finish_left_over;

							$tot_wovenReqQty+=$wovenReqQty;
							$tot_wovenRecQty+=$wovenRecQty;
							$tot_wovenRecBalQty+=$wovenFabRecBal;
							$tot_wovenIssueQty+=$wovenIssueQty;
							$tot_wovenIssueBalQty+=$wovenFabIssueBal;

							$tot_gmt_qty+=$plan_cut_qty;
							$tot_cutting_qty+=$cuttingQty;
							$tot_printIssIn_qty+=$print_issue_qnty_in;
							$tot_printIssOut_qty+=$print_issue_qnty_out;
							$tot_printIssue_qty+=$total_print_issued;
							$tot_printRcvIn_qty+=$print_recv_qnty_in;
							$tot_printRcvOut_qty+=$print_recv_qnty_out;
							$tot_printRcv_qty+=$total_print_recv;
							$tot_printRjt_qty+=$print_reject_qnty;

							$tot_sewInInput_qty+=$sew_input_qnty_in;
							$tot_sewInOutput_qty+=$sew_input_qnty_out;
							$tot_sewIn_qty+=$total_sew_input;
							$tot_sewInBal_qty+=$sew_input_balance_qnty;

							$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
							$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
							$tot_sewRcv_qty+=$total_sew_recv;
							$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
							$tot_sewRcvRjt_qty+=$sew_reject_qnty;

							$tot_washRcvIn_qty+=$wash_recv_qnty_in;
							$tot_washRcvOut_qty+=$wash_recv_qnty_out;
							$tot_washRcv_qty+=$total_wash_recv;
							$tot_washRcvBal_qty+=$wash_balance_qnty;

							$tot_gmtFinIn_qty+=$gmt_finish_in;
							$tot_gmtFinOut_qty+=$gmt_finish_out;
							$tot_gmtFin_qty+=$total_gmts_finish_qnty;
							$tot_gmtFinBal_qty+=$finish_balance_qnty;
							$tot_gmtFinRjt_qty+=$finish_reject_qnty;
							$tot_gmtEx_qty+=$exfactory_qnty;
							$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

							$tot_shortExcess_exFactory_qty+=$short_excess_exFactoryQty;
							$tot_prLoss_qty+=$process_loss;
							$tot_prLossDye_qty+=$process_loss_dyeing;
							$tot_prLossCut_qty+=$cutting_process_loss;
							$tot_process_loss_yern_per += $process_loss_yern_per;
							$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
							$tot_process_loss_cutting_per += $process_loss_cutting_per;
							$i++;
						}
					}
					else
					{
						foreach($result_job_wise as $po_id_job)
						{
							$po_id_arr=array_unique(explode(",",substr($po_id_job,0,-1)));
							$tot_po_qnty=$yarn_required=$tot_exfactory_qnty=$grey_fabric_req_qnty=$finish_fabric_req_qnty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$transfer_in_qnty_knit=$transfer_in_qnty_finish=$transfer_out_qnty_finish=$knit_qnty_in=$issuedToDyeQnty=$issuedToCutQnty=$finish_qnty_in=$finish_qnty_out=$finish_reject_qnty=$print_issue_qnty_in=$print_issue_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_recv_qnty_in=$sew_recv_qnty_out=$sew_reject_qnty=$wash_recv_qnty_in=$wash_recv_qnty_out=$trims_recv=$trims_issue=$emb_reject_qnty=$gmt_finish_in=$gmt_finish_out=$gmt_finish_reject_qnty=$total_reject_qnty=$tot_process_loss_yern_per=$tot_process_loss_dyeing_per=0;
							foreach($po_id_arr as $po_id)
							{
								$tot_po_qnty +=$result_data_arr[$po_id]["po_qnty"];
								$tot_exfactory_qnty +=$result_data_arr[$po_id]["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
								$yarn_required+=$yarn_qty_arr[$po_id];
								$grey_fabric_req_qnty +=$booking_req_arr[$po_id]['gray'];;
								$finish_fabric_req_qnty +=$booking_req_arr[$po_id]['fin'];

								$yarn_issue_inside +=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
								$yarn_issue_outside +=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
								$transfer_in_qnty_yarn +=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
								$transfer_out_qnty_yarn +=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
								$transfer_in_qnty_knit +=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
								$transfer_out_qnty_knit +=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];
								$transfer_in_qnty_finish +=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
								$transfer_out_qnty_finish +=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];

								$total_issued =$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
								$under_over_issued =$grey_fabric_req_qnty-$total_issued;

								$knit_qnty_in +=$kniting_prod_arr[$po_id]["knit_qnty_in"];
								$knit_qnty_out +=$kniting_prod_arr[$po_id]["knit_qnty_out"];
								$total_knitting=$knit_qnty_in+$knit_qnty_out+$transfer_in_qnty_knit-$transfer_out_qnty_knit;
								$process_loss=($knit_qnty_in+$knit_qnty_out)-$total_issued;
								$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
								$issuedToDyeQnty +=$grey_cut_issue_arr[$po_id]["grey_issue_qnty"];
								$left_over=$total_knitting-$issuedToDyeQnty;

								$issuedToCutQnty +=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];

								$finish_qnty_in +=$finish_prod_arr[$po_id]["finish_qnty_in"];
								$finish_qnty_out +=$finish_prod_arr[$po_id]["finish_qnty_out"];
								$total_finish_qnty=$finish_qnty_in+$finish_qnty_out;
								$finish_balance_qnty=$tot_po_qnty-$total_finish_qnty;
								$finish_reject_qnty +=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
								$left_over_finish_gmts=$total_finish_qnty-$tot_exfactory_qnty;

								$total_finishing=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
								$process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;
								$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
								$finish_left_over=$total_finishing-$issuedToCutQnty;

								$print_issue_qnty_in +=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
								$print_issue_qnty_out +=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
								$print_recv_qnty_in +=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
								$print_recv_qnty_out +=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
								$print_reject_qnty +=$garment_prod_data_arr[$po_id]['print_reject_qnty'];

								$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
								$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;

								$sew_input_qnty_in +=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
								$sew_input_qnty_out +=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
								$total_sew_issued=$sew_input_qnty_in+$sew_input_qnty_out;
								$sew_balance_qnty=$tot_po_qnty-$total_sew_issued;

								$sew_recv_qnty_in +=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
								$sew_recv_qnty_out +=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
								$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;

								$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;

								$sew_reject_qnty +=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];

								$wash_recv_qnty_in +=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
								$wash_recv_qnty_out +=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
								$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
								$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

								$gmt_finish_in+=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
								$gmt_finish_out+=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
								$total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
								$finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
								$gmt_finish_reject_qnty+=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
								$left_over_finish_gmts=$total_gmts_finish_qnty-$tot_exfactory_qnty;

								$trims_recv+=$trims_array[$po_id]['recv'];
								$trims_issue+=$trims_array[$po_id]['iss'];
								$tot_trims_left_over_qnty=$trims_recv+$trims_issue;

								$emb_reject_qnty +=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
								$cutting_reject_qnty +=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

								$iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
								$tot_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$gmt_finish_reject_qnty+$iron_rej_qnty+$emb_reject_qnty;
								$reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($tot_reject_qnty,2).'</a></p>';
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="40"><? echo $i; ?></td>
								<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$result_data_arr[$po_id]["buyer_name"]]; ?></div></td>
								<td width="50" align="center"><? echo $result_data_arr[$po_id]["job_year"]; ?></td>
								<td width="50" align="center"><? echo $result_data_arr[$po_id]["job_no_prefix_num"]; ?></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $result_data_arr[$po_id]["style_ref_no"]; ?></div></td>
								<td width="80" align="right" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty,0); ?></td>

								<td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_inside,2); ?></td>
								<td align="right" width="80"><? echo number_format($yarn_issue_outside,2);?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_yarn,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_issued,2); ?></td>

								<td align="right" width="80"><? echo number_format($grey_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($knit_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_knit,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToDyeQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($left_over,2); ?></td>

								<td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_in,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_qnty_out,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_in_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($transfer_out_qnty_finish,2); ?></td>
								<td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($process_loss_finishing,2); ?></td>
								<td align="right" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
								<td align="right" width="80"><? echo number_format($issuedToCutQnty,2); ?></td>
								<td align="right" width="80"><? echo number_format($finish_left_over,2); ?></td>

								<td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_issue_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($print_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($sew_input_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_input_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_issued); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_qnty); ?></td>

								<td width="100" align="center">View</td>

								<td align="right" width="80"><? echo number_format($sew_recv_qnty_in); ?></td>
								<td align="right" width="80"><? echo number_format($sew_recv_qnty_out); ?></td>
								<td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
								<td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>

								<td width="80" align="right"><? echo number_format($wash_recv_qnty_in); ?></td>
								<td width="80" align="right"><? echo number_format($wash_recv_qnty_out); ?></td>
								<td width="80" align="right"><? echo number_format($total_wash_recv); ?></td>
								<td width="80" align="right"><? echo number_format($wash_balance_qnty); ?></td>

								<td width="80" align="right"><? echo number_format($gmt_finish_in); ?></td>
								<td width="80" align="right"><? echo number_format($gmt_finish_out); ?></td>
								<td width="80" align="right"><? echo number_format($total_gmts_finish_qnty); ?></td>
								<td width="80" align="right"><? echo number_format($finish_balance_qnty); ?></td>
								<td width="80" align="right"><? echo number_format($gmt_finish_reject_qnty); ?></td>
								<td align="right" width="80"><? echo $reject_button; ?></td>

								<td align="right" width="80">
								<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $result_data_arr[$po_id]["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($tot_exfactory_qnty); ?></a>
								<? //echo number_format($tot_exfactory_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>

								<td align="right" width="80"><? echo number_format($left_over); ?></td>
								<td align="right" width="80"><? echo number_format($finish_left_over); ?></td>
								<td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
								<td align="right" width="80"><? echo number_format($tot_trims_left_over_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($emb_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>
								<td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>

								<td align="right" width="80"><? echo number_format($process_loss); ?></td>
								<td align="right" width="80"><?
									if($total_issued!=0){
										$process_loss_yern_per = ($process_loss*100)/$total_issued;
									}
									else{
										$process_loss_yern_per = 0;
									}
									echo number_format($process_loss_yern_per);
								?></td>
								<td align="right" width="80"><? echo number_format($process_loss_dyeing); ?></td>
								<td align="right" width="80"><?
									if($issuedToDyeQnty != 0){
										$process_loss_dyeing_per = ($issuedToDyeQnty-$total_finishing)*100/$issuedToDyeQnty;
									}
									else{
										$process_loss_dyeing_per = 0;
									}
									echo number_format($process_loss_dyeing_per);
								?></td>
								<td align="right" width="80"><? echo number_format($process_loss_cutting); ?></td>
								<td align="right" width="80"><?
									if($tot_po_qnty!=0){
										$process_loss_cutting_per = ($cuttingQty-$tot_po_qnty)*100/$tot_po_qnty;
									}
									else{
										$process_loss_cutting_per = 0;
									}
									echo number_format($process_loss_cutting_per);
								?></td>
								<td align="center" width="100"><a href="##" onClick="fnc_open_view('import_popup','Import Statement',<?=$cbo_company_name;?>,'<?=$result_data_arr[$po_id]['job_id'];?>','<?=$result_data_arr[$po_id]['job_no'];?>','<?=$po_id;?>')">View</a></td>
								<td align="center" onClick="fnc_open_view('export_popup','Export Statement',<?=$cbo_company_name;?>,'<?=$result_data_arr[$po_id]['job_id'];?>','<?=$result_data_arr[$po_id]['job_no'];?>','<?=$po_id;?>')"><a href="##" >View</a></td>
							</tr>
							<?
							$tot_order_qty+=$tot_po_qnty;
							$tot_yarn_req_qty+=$yarn_required;
							$tot_yarn_issIn_qty+=$yarn_issue_inside;
							$tot_yarn_issOut_qty+=$yarn_issue_outside;
							$tot_yarn_trnsInq_qty+=$transfer_in_qnty_yarn;
							$tot_yarn_trnsOut_qty+=$transfer_out_qnty_yarn;
							$tot_yarn_issue_qty+=$total_issued;
							$tot_yarn_undOvr_qty+=$under_over_issued;

							$tot_grey_req_qty+=$grey_fabric_req_qnty;
							$tot_grey_in_qty+=$knit_qnty_in;
							$tot_grey_out_qty+=$knit_qnty_out;
							$tot_grey_trnsIn_qty+=$transfer_in_qnty_knit;
							$tot_grey_transOut_qty+=$transfer_out_qnty_knit;
							$tot_grey_qty+=$total_knitting;
							$tot_grey_prLoss_qty+=$process_loss;
							$tot_grey_undOver_qty+=$under_over_prod;
							$tot_grey_issDye_qty+=$issuedToDyeQnty;
							$tot_grey_lftOver_qty+=$left_over;

							$tot_fin_req_qty+=$finish_fabric_req_qnty;
							$tot_fin_in_qty+=$finish_qnty_in;
							$tot_fin_out_qty+=$finish_qnty_out;
							$tot_fin_transIn_qty+=$transfer_in_qnty_finish;
							$tot_fin_transOut_qty+=$transfer_out_qnty_finish;
							$tot_fin_qty+=$total_finishing;
							$tot_fin_prLoss_qty+=$process_loss_finishing;
							$tot_fin_undOver_qty+=$under_over_finish_prod;
							$tot_fin_issCut_qty+=$issuedToCutQnty;
							$tot_fin_lftOver_qty+=$finish_left_over;

							$tot_gmt_qty+=$tot_po_qnty;
							$tot_printIssIn_qty+=$print_issue_qnty_in;
							$tot_printIssOut_qty+=$print_issue_qnty_out;
							$tot_printIssue_qty+=$total_print_issued;
							$tot_printRcvIn_qty+=$print_recv_qnty_in;
							$tot_printRcvOut_qty+=$print_recv_qnty_out;
							$tot_printRcv_qty+=$total_print_recv;
							$tot_printRjt_qty+=$print_reject_qnty;

							$tot_sewInInput_qty+=$sew_input_qnty_in;
							$tot_sewInOutput_qty+=$sew_input_qnty_out;
							$tot_sewIn_qty+=$total_sew_issued;
							$tot_sewInBal_qty+=$sew_balance_qnty;

							$tot_sewRcvIn_qty+=$sew_recv_qnty_in;
							$tot_sewRcvOut_qty+=$sew_recv_qnty_out;
							$tot_sewRcv_qty+=$total_sew_recv;
							$tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
							$tot_sewRcvRjt_qty+=$sew_reject_qnty;

							$tot_washRcvIn_qty+=$wash_recv_qnty_in;
							$tot_washRcvOut_qty+=$wash_recv_qnty_out;
							$tot_washRcv_qty+=$total_wash_recv;
							$tot_washRcvBal_qty+=$wash_balance_qnty;

							$tot_gmtFinIn_qty+=$gmt_finish_in;
							$tot_gmtFinOut_qty+=$gmt_finish_out;
							$tot_gmtFin_qty+=$total_gmts_finish_qnty;
							$tot_gmtFinBal_qty+=$finish_balance_qnty;
							$tot_gmtFinRjt_qty+=$gmt_finish_reject_qnty;
							$tot_gmtEx_qty+=$tot_exfactory_qnty;
							$tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

							$tot_leftOver_qty+=$left_over;
							$tot_leftOverFin_qty+=$finish_left_over;
							$tot_leftOverGmtFin_qty+=$left_over_finish_gmts;
							$tot_leftOverTrm_qty+=$tot_trims_left_over_qnty;

							$tot_rjtPrint_qty+=$print_reject_qnty;
							$tot_rjtEmb_qty+=$emb_reject_qnty;
							$tot_rjtSew_qty+=$sew_reject_qnty;
							$tot_rjtFin_qty+=$finish_reject_qnty;

							$tot_prLoss_qty+=$process_loss;
							$tot_prLossFin_qty+=$process_loss_finishing;
							$tot_prLossCut_qty+=$cutting_process_loss;
							$total_reject_qnty+=$tot_reject_qnty;

							$tot_process_loss_yern_per += $process_loss_yern_per;
							$tot_process_loss_dyeing_per += $process_loss_dyeing_per;
							$tot_process_loss_cutting_per += $process_loss_cutting_per;
							$i++;
						}
					}
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="50">&nbsp;</td>

                    <td width="100">Total :</td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="100">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="" bgcolor="#FFFFCC"><? //echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_order_val" bgcolor="#FFFFCC"><? echo number_format($tot_order_value); ?></td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="70" align="right" id=""><? //echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issIn_qty"><? echo number_format($tot_yarn_issIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issOut_qty"><? echo number_format($tot_yarn_issOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_trnsInq_qty"><? echo number_format($tot_yarn_trnsInq_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_trnsOut_qty"><? echo number_format($tot_yarn_trnsOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_undOvr_qty"><? echo number_format($tot_yarn_undOvr_qty,2); ?></td>

                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($tot_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_in_qty"><? echo number_format($tot_grey_in_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_out_qty"><? echo number_format($tot_grey_out_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_trnsIn_qty"><? echo number_format($tot_grey_trnsIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_transOut_qty"><? echo number_format($tot_grey_transOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_qty"><? echo number_format($tot_grey_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_rec_qty"><? echo number_format($tot_grey_rec_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_prLoss_qty"><? echo number_format($tot_grey_prLoss_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_undOver_qty"><? echo number_format($tot_grey_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty"><? echo number_format($tot_grey_issDye_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($tot_grey_lftOver_qty,2); ?></td>

                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_in_qty"><? echo number_format($tot_fin_in_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_out_qty"><? echo number_format($tot_fin_out_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_transIn_qty"><? echo number_format($tot_fin_transIn_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_transOut_qty"><? echo number_format($tot_fin_transOut_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_fin_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_prLoss_qty"><? echo number_format($tot_fin_prLoss_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_undOver_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($tot_fin_issCut_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_fin_lftOver_qty,2); ?></td>

                    <td width="80" align="right" id="td_wovenReqQty"><? echo number_format($tot_wovenReqQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenRecQty"><? echo number_format($tot_wovenRecQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenRecBalQty"><? echo number_format($tot_wovenRecBalQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenIssueQty"><? echo number_format($tot_wovenIssueQty,2); ?></td>
                    <td width="80" align="right" id="td_wovenIssueBalQty"><? echo number_format($tot_wovenIssueBalQty,2); ?></td>


                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_gmt_qty,0); ?></td>
                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cutting_qty); ?></td>
                    <td width="80" align="right" id="td_printIssIn_qty"><? echo number_format($tot_printIssIn_qty); ?></td>
                    <td width="80" align="right" id="td_printIssOut_qty"><? echo number_format($tot_printIssOut_qty); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty"><? echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_printRcvIn_qty"><? echo number_format($tot_printRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_printRcvOut_qty"><? echo number_format($tot_printRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_printRcv_qty"><? echo number_format($tot_printRcv_qty); ?></td>
                    <td width="80" align="right" id="td_printRjt_qty"><? echo number_format($tot_printRjt_qty); ?></td>

                    <td width="80" align="right" id="td_sewInInput_qty"><? echo number_format($tot_sewInInput_qty); ?></td>
                    <td width="80" align="right" id="td_sewInOutput_qty"><? echo number_format($tot_sewInOutput_qty); ?></td>
                    <td width="80" align="right" id="td_sewIn_qty"><? echo number_format($tot_sewIn_qty); ?></td>
                    <td width="80" align="right" id="td_sewInBal_qty"><? echo number_format($tot_sewInBal_qty); ?></td>

                    <td width="100">&nbsp;</td>

                    <td width="80" align="right" id="td_sewRcvIn_qty"><? echo number_format($tot_sewRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvOut_qty"><? echo number_format($tot_sewRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcv_qty"><? echo number_format($tot_sewRcv_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvBal_qty"><? echo number_format($tot_sewRcvBal_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvRjt_qty"><? echo number_format($tot_sewRcvRjt_qty); ?></td>

                    <td width="80" align="right" id="td_washRcvIn_qty"><? echo number_format($tot_washRcvIn_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvOut_qty"><? echo number_format($tot_washRcvOut_qty); ?></td>
                    <td width="80" align="right" id="td_washRcv_qty"><? echo number_format($tot_washRcv_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvBal_qty"><? echo number_format($tot_washRcvBal_qty); ?></td>

                    <td width="80" align="right" id="td_gmtFinIn_qty"><? echo number_format($tot_gmtFinIn_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinOut_qty"><? echo number_format($tot_gmtFinOut_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFin_qty"><? echo number_format($tot_gmtFin_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinBal_qty"><? echo number_format($tot_gmtFinBal_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinRjt_qty"><? echo number_format($tot_gmtFinRjt_qty); ?></td>
                   <td width="80" align="right" id="td_gmtrej_qty"><? echo number_format($total_reject_qnty); ?></td>
                   <td width="80" align="right" id="td_current_ex_qty"><? echo number_format($tot_current_ex_fact_qnty); ?></td>
                    <td width="80" align="right" id="td_gmtEx_qty"><? echo number_format($tot_gmtEx_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinLeftOver_qty"><? echo number_format($tot_gmtFinLeftOver_qty); ?></td>

                    <td width="80" align="right" id="td_shortExcess_exFactory_qty"><? echo number_format($tot_shortExcess_exFactory_qty); ?></td>

                    <td width="80" align="right" id="td_prLoss_qty"><? echo number_format($tot_prLoss_qty); ?></td>
                    <td width="80" align="right" id="td_prLoss_yarn_qty"><? echo number_format($tot_process_loss_yern_per); ?></td>
                    <td width="80" align="right" id="td_prLossDye_qty"><? echo number_format($tot_prLossDye_qty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_dyeing_per); ?></td>
                    <td align="right"  width="80" id="td_prLossCut_qty"><? echo number_format($tot_prLossCut_qty); ?></td>
                    <td width="80" align="right" id="td_prLoss_qty_cut"><? echo number_format($tot_process_loss_cutting_per); ?></td>
                     <td width="80" align="right" id="td_qty_cut_to_ship"><? echo number_format($tot_cut_to_ship); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_cutting_per); ?></td>
                    <td width="80" align="right" id="td_qty_order_to_ship"><? echo number_format($tot_order_to_ship); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_process_loss_cutting_per); ?></td>
                    <td width="100" ></td>
                    <td ></td>
                </tr>
           </table>
        </div>
	<?
	$html = ob_get_contents();
    ob_clean();
	foreach (glob("$user_id*.xls") as $filename)
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w') or die('canot open');
    $is_created = fwrite($create_new_doc,$html) or die('canot write');
    echo "$html**$filename**$cbo_search_type**$type";
    exit();
}

if($action=='trims_popup')
{
	echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id."*".$tot_po_qnty;die;

	//echo $ratio;die;

	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:650px;" >
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
				$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
				$trims_array=array();
				$trimsDataArr=sql_select("select b.item_group_id,
										sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
										sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
										from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($po_break_down_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
				foreach($trimsDataArr as $row)
				{
					$trims_array[$row[csf('item_group_id')]]['recv']=$row[csf('recv_qnty')];
					$trims_array[$row[csf('item_group_id')]]['iss']=$row[csf('issue_qnty')];
				}
				$condition= new condition();
				if($po_break_down_id !=''){
				$condition->po_id("in($po_break_down_id)");
				}
				$condition->init();
				$trims= new trims($condition);
				$trims_qty_arr=$trims->getQtyArray_by_orderAndItemid();
				//print_r($trims_qty_arr);

				//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
				$trimsDataArr=sql_select("select c.po_break_down_id as po_id, max(a.costing_per) as costing_per, b.trim_group, max(b.cons_uom) as cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b , wo_pre_cost_trim_co_cons_dtls c where a.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and b.status_active=1 and b.is_deleted=0 and c.po_break_down_id=$po_break_down_id group by b.trim_group, c.po_break_down_id");
				$i=1; $tot_accss_req_qnty=0; $tot_recv_qnty=0; $tot_iss_qnty=0; $tot_recv_bl_qnty=0; $tot_trims_left_over_qnty=0;
				foreach($trimsDataArr as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$dzn_qnty='';
					if($row[csf('costing_per')]==1) $dzn_qnty=12;
					else if($row[csf('costing_per')]==3) $dzn_qnty=12*2;
					else if($row[csf('costing_per')]==4) $dzn_qnty=12*3;
					else if($row[csf('costing_per')]==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					$dzn_qnty=$dzn_qnty*$ratio;
					$accss_req_qnty=$trims_qty_arr[$row[csf('po_id')]][$row[csf('trim_group')]];//($row[csf('cons_dzn_gmts')]/$dzn_qnty)*$tot_po_qnty;

					$trims_recv=$trims_array[$row[csf('trim_group')]]['recv'];
					$trims_issue=$trims_array[$row[csf('trim_group')]]['iss'];
					$recv_bl=$accss_req_qnty-$trims_recv;
					$trims_left_over=$trims_recv-$trims_issue;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
						<td align="right"><? echo number_format($accss_req_qnty,2,'.',''); ?>&nbsp;</td>
						<td align="right"><? echo number_format($trims_recv,2,'.',''); ?>&nbsp;</td>
						<td align="right"><? echo number_format($recv_bl,2,'.',''); ?>&nbsp;</td>
						<td align="right"><? echo number_format($trims_issue,2,'.',''); ?>&nbsp;</td>
						<td align="right"><? echo number_format($trims_left_over,2,'.',''); ?>&nbsp;</td>
					</tr>
				<?
					$tot_accss_req_qnty+=$accss_req_qnty;
					$tot_recv_qnty+=$trims_recv;
					$tot_recv_bl_qnty+=$recv_bl;
					$tot_iss_qnty+=$trims_issue;
					$tot_trims_left_over_qnty+=$trims_left_over;
					$i++;
				}
				$tot_trims_left_over_qnty_perc=($tot_trims_left_over_qnty/$tot_recv_qnty)*100;
				?>
				<tfoot>
					<tr>
						<th align="right">&nbsp;</th>
						<th align="right">Total</th>
						<th align="right"><? echo number_format($tot_accss_req_qnty,0,'.',''); ?>&nbsp;</th>
						<th align="right"><? echo number_format($tot_recv_qnty,0,'.',''); ?>&nbsp;</th>
						<th align="right"><? echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
						<th align="right"><? echo number_format($tot_iss_qnty,0,'.',''); ?>&nbsp;</th>
						<th align="right"><? echo number_format($tot_trims_left_over_qnty,0,'.',''); ?>&nbsp;</th>
					</tr>
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
	/*echo $from.'_'.$to;//$job_no;
	die;*/
	//echo $cbo_date_category;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
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
				//echo $txt_date_to;
				if(str_replace("'","",$txt_date_from)!="")
				{
				if($db_type==0)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
					//$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				}
				else if($db_type==2)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
					//$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				}
				$date_cond=" and b.ex_factory_date between '$start_date' and '$end_date'";
				}

				$ship_date_cond="";
				if($cbo_date_category==1)
				{

				if($txt_date_from!="" && $txt_date_to!="")
				{
					$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
				}
				 $exfac_sql="select b.challan_no,a.sys_number,b.ex_factory_date,
					CASE WHEN b.entry_form!=85  THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85  THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_mst b left join pro_ex_factory_delivery_mst a on b.delivery_mst_id=a.id and a.status_active=1 and a.is_deleted=0
 				where b.status_active=1  and b.is_deleted=0 and b.po_break_down_id in($id) order by b.ex_factory_date";
				}
				else if($cbo_date_category==2)
				{
					//$ex_fact_date_cond="";
					if($txt_date_from!="" && $txt_date_to!="")
					{
						$ship_date_cond="and b.ex_factory_date between '$txt_date_from' and '$txt_date_to' AND c.is_deleted = 0 AND c.status_active = 1";
					}
					 $exfac_sql="select b.challan_no,a.sys_number,b.ex_factory_date,
					CASE WHEN b.entry_form!=85  THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85  THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_mst b left join pro_ex_factory_delivery_mst a on b.delivery_mst_id=a.id and a.status_active=1 and a.is_deleted=0
 				where b.status_active=1  and b.is_deleted=0 and b.po_break_down_id in($id) order by b.ex_factory_date";
				}
				else if($cbo_date_category==3) //Ref Closing date
				{

					if($txt_date_from!="" && $txt_date_to!="")
					{
						$ship_date_cond="and d.closing_date between '$txt_date_from' and '$txt_date_to'";
					}
				  $exfac_sql="select b.challan_no,a.sys_number,b.ex_factory_date,
				CASE WHEN b.entry_form!=85   THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85    THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from   pro_ex_factory_mst b left join pro_ex_factory_delivery_mst a on b.delivery_mst_id=a.id and a.status_active=1 and a.is_deleted=0
 				where  b.status_active=1   and b.is_deleted=0 and b.po_break_down_id in($id) order by b.ex_factory_date";
				}
                $i=1;

				// echo $exfac_sql;
				//die;
                $sql_dtls=sql_select($exfac_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty); ?></th>
                    <th><? echo number_format($rec_return_qnty); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>

                 <th ><? echo number_format($rec_qnty-$rec_return_qnty); ?></th>
                 <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}

if ($action=="reject_qty")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="7">Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="75">Cutting Reject Qty</th>
                    <th width="75">Embellishment Reject Qty</th>
                    <th width="75">Sewing Out Reject Qty</th>
                    <th width="75">Iron Reject Qty</th>
                    <th width="75">Finish Reject Qty.</th>
                    <th >Total Reject Qty.</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$sql_qry="Select a.po_break_down_id,
							sum(CASE WHEN a.production_type ='1' THEN b.reject_qty ELSE 0 END) AS cutting_rej_qnty,
							sum(CASE WHEN a.production_type ='3' and  a.embel_name in(1,2) THEN b.reject_qty ELSE 0 END) AS emb_rej_qnty,
							sum(CASE WHEN a.production_type ='7' THEN b.reject_qty ELSE 0 END) AS iron_rej_qnty,
			 				sum(CASE WHEN a.production_type ='8' THEN b.reject_qty ELSE 0 END) AS finish_rej_qnty,
							sum(CASE WHEN a.production_type ='5' THEN b.reject_qty ELSE 0 END) AS sewingout_rej_qnty
							from pro_garments_production_mst a, pro_garments_production_dtls b
							where a.id=b.mst_id and  a.po_break_down_id in ($po_id)  and a.status_active=1 and a.is_deleted=0  group by a.po_break_down_id";
			 // echo $sql_qry;
			$sql_result=sql_select($sql_qry);

			$i=1;
			foreach($sql_result as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo number_format($row[csf('cutting_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('emb_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('sewingout_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('iron_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('finish_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? $total_reject=$row[csf('cutting_rej_qnty')]+$row[csf('emb_rej_qnty')]+$row[csf('iron_rej_qnty')]+$row[csf('sewingout_rej_qnty')]+$row[csf('finish_rej_qnty')]; echo $total_reject; ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="knit_prod_inside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">prod. Id</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$prodKnitDataArr=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
			sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
			sum(CASE WHEN a.entry_form=22 THEN a.quantity ELSE 0 END) AS knit_qnty_rec,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num
			from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id and c.item_category=13 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num");// and c.receive_basis<>9 and a.po_breakdown_id in($all_po_id)












			$i=1;
			foreach($prodKnitDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('recv_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('knit_qnty_in')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="trans_in_knit_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
					<th width="75">Transfer Id </th>
                    <th width="75">Transfer Date </th>
                    <th width="75">From Order</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$dataArrayTrans=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN a.entry_form ='11' and a.trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
			sum(CASE WHEN a.entry_form ='11' and a.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
			sum(CASE WHEN a.entry_form ='13' and a.trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
			sum(CASE WHEN a.entry_form ='13' and a.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
			sum(CASE WHEN a.entry_form ='15' and a.trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
			sum(CASE WHEN a.entry_form ='15' and a.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish,a.trans_id,c.issue_number,c.issue_number_prefix_num,c.issue_date	from order_wise_pro_details a, inv_transaction b, inv_issue_master c where a.trans_id=b.id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and a.entry_form in(11,13,15)  and a.po_breakdown_id=$po_id group by a.po_breakdown_id,a.trans_id,c.issue_number,c.issue_number_prefix_num,c.issue_date");//and po_breakdown_id in($all_po_id)




			$i=1;
			foreach($dataArrayTrans as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('issue_number_prefix_num')]; ?>&nbsp;</td>
					<td align="right"><? echo $row[csf('issue_date')]; ?>&nbsp;</td>
                    <td align="right"><? //echo $row[csf('issue_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('transfer_in_qnty_knit')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="knit_receive_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
					<th width="75">Receive ID </th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$prodKnitDataArr=sql_select("select a.po_breakdown_id,
				sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
				sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
				sum(CASE WHEN a.entry_form=22 THEN a.quantity ELSE 0 END) AS knit_qnty_rec,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num
				from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id and c.item_category=13 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num");// and c.receive_basis<>9 and a.po_breakdown_id in($all_po_id)


			$i=1;
			foreach($prodKnitDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('recv_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('knit_qnty_rec')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="fin_prod_inside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
					<th width="75">Production ID</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$prodFinDataArr=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
			sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
			sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num
			from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9 group by a.po_breakdown_id,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num");// and a.po_breakdown_id in($all_po_id)






			$i=1;
			foreach($prodFinDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('recv_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("finish_qnty_in")],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="fin_trans_in_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
					<th width="75">Receive ID </th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$dataArrayTrans=sql_select("select po_breakdown_id,
			sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
			sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
			sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
			sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
			sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
			sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish
		from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(11,13,15)  and po_breakdown_id=$po_id
		group by po_breakdown_id");//and po_breakdown_id in($all_po_id)






			$i=1;
			foreach($dataArrayTrans as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('recv_number')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("transfer_in_qnty_finish")],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="fin_trans_out_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
					<th width="75">Receive ID </th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$dataArrayTrans=sql_select("select po_breakdown_id,
			sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
			sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
			sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
			sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
			sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
			sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish
		from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(11,13,15)  and po_breakdown_id=$po_id
		group by po_breakdown_id");//and po_breakdown_id in($all_po_id)






			$i=1;
			foreach($dataArrayTrans as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('recv_number')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("transfer_out_qnty_finish")],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="fin_prod_outside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
					<th width="75">Production ID </th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$prodFinDataArr=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
			sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
			sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num
			from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9 group by a.po_breakdown_id,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num");// and a.po_breakdown_id in($all_po_id)



			$i=1;
			foreach($prodFinDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('recv_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf("finish_qnty_out")],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="knit_issue_to_deying_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
					<th width="75">Issue No </th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$issueData=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN a.entry_form=16 THEN quantity ELSE 0 END) AS grey_issue_qnty,
			sum(CASE WHEN a.entry_form=61 THEN quantity ELSE 0 END) AS grey_issue_qnty_roll_wise,
			sum(CASE WHEN a.entry_form=18 THEN quantity ELSE 0 END) AS issue_to_cut_qnty,
			sum(CASE WHEN a.entry_form=71 THEN quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
			sum(CASE WHEN a.entry_form=19 THEN quantity ELSE 0 END) AS woven_issue,a.trans_id,c.issue_number,c.issue_number_prefix_num,c.issue_date
			from order_wise_pro_details a, inv_transaction b, inv_issue_master c where a.trans_id=b.id and b.mst_id=c.id and  a.entry_form in(16,18,19,61,71)
			 and a.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 group by a.po_breakdown_id,a.trans_id,c.issue_number,c.issue_number_prefix_num,c.issue_date");//po_breakdown_id in($all_po_id) and


			$i=1;
			foreach($issueData as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 $qty=$row[csf("grey_issue_qnty")]+$row[csf("grey_issue_qnty_roll_wise")];
				 if($qty>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('issue_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('issue_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($qty,0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="trans_out_knit_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
					<th width="75">Transfer Id </th>
                    <th width="75">Transfer Date </th>
                    <th width="75">To Order</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";



			$dataArrayTrans=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN a.entry_form ='11' and a.trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
			sum(CASE WHEN a.entry_form ='11' and a.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
			sum(CASE WHEN a.entry_form ='13' and a.trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
			sum(CASE WHEN a.entry_form ='13' and a.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
			sum(CASE WHEN a.entry_form ='15' and a.trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
			sum(CASE WHEN a.entry_form ='15' and a.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish,a.trans_id,c.issue_number,c.issue_number_prefix_num,c.issue_date	from order_wise_pro_details a, inv_transaction b, inv_issue_master c where a.trans_id=b.id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and a.entry_form in(11,13,15)  and a.po_breakdown_id=$po_id group by a.po_breakdown_id,a.trans_id,c.issue_number,c.issue_number_prefix_num,c.issue_date");//and po_breakdown_id in($all_po_id)




			$i=1;
			foreach($prodKnitDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
					<td align="right"><? echo $row[csf('issue_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('issue_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('prod_id')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('transfer_out_qnty_knit')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="fin_issue_to_cut_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>

                    <th width="75">Date </th>
                    <th width="75">Issue No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$issueData=sql_select("select a.po_breakdown_id,
			sum(case when a.entry_form=16 then quantity else 0 end) as grey_issue_qnty,
			sum(case when a.entry_form=61 then quantity else 0 end) as grey_issue_qnty_roll_wise,
			sum(case when a.entry_form=18 then quantity else 0 end) as issue_to_cut_qnty,
			sum(case when a.entry_form=71 then quantity else 0 end) as issue_to_cut_qnty_roll_wise,
			sum(case when a.entry_form=19 then quantity else 0 end) as woven_issue,c.issue_number_prefix_num,c.issue_date
			from order_wise_pro_details a, inv_transaction b, inv_issue_master c
			where a.trans_id=b.id and b.mst_id=c.id and a.entry_form in(16,18,19,61,71)  and a.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 group by a.po_breakdown_id,c.issue_number_prefix_num,c.issue_date");//po_breakdown_id in($all_po_id) and





			$i=1;
			foreach($issueData as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('issue_to_cut_qnty')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('issue_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('issue_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('issue_to_cut_qnty')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="cutting_qty_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">Cutting No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN production_type=1 THEN reject_qnty ELSE 0 END) AS cutting_reject_qnty,
			sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty,challan_no,production_date
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and




			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('cutting_qnty')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('production_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('cutting_qnty')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="gmts_print_issued_in_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">issue No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$gmtsProdDataArr=sql_select("select  po_break_down_id,
					sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty_in,
					sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,challan_no,production_date
					from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and




			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('print_issue_qnty_in')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('production_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('print_issue_qnty_in')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="gmts_print_issued_out_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">Issue No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty_in,
			sum(CASE WHEN production_type=2 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_issue_qnty_out,
			sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
			sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,challan_no,production_date
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and



			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('print_issue_qnty_out')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('production_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('print_issue_qnty_out')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="sew_input_inside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>

                    <th width="75"> Date </th>
                    <th width="75">Challan No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_input_qnty_in,
			sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_input_qnty_out,
			sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,
			sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,challan_no,production_date
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and



			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('sew_input_qnty_in')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('production_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('sew_input_qnty_in')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="sew_input_outside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>

                    <th width="75"> Date </th>
                    <th width="75">Challan No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,

			sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_input_qnty_in,
			sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_input_qnty_out,
			sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,challan_no,production_date
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and


			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('sew_input_qnty_out')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('production_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('sew_input_qnty_out')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="sew_output_inside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>

                    <th width="75"> Date </th>
                    <th width="75">Challan No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,

			sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,
			sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
			sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty,challan_no,production_date
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and


			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('sew_recv_qnty_in')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('production_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('sew_recv_qnty_in')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="sew_output_outside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75"> Date </th>
                    <th width="75">challan no</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";
			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
			sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty,challan_no,production_date
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and

			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('sew_recv_qnty_out')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('production_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('sew_recv_qnty_out')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="wash_input_inside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>

                    <th width="75"> Date </th>
                    <th width="75">Challan No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
			sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,
			sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty,challan_no,production_date
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and


			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('wash_recv_qnty_in')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('production_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('challan_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('wash_recv_qnty_in')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="wash_input_outside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>

                    <th width="75"> Date </th>
                    <th width="75">Challan No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
			sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,
			sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty,challan_no,production_date
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id,challan_no,production_date");//po_break_down_id in($all_po_id) and


			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($row[csf('wash_recv_qnty_out')]>0){
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('prod_id')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('wash_recv_qnty_out')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }}
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="finish_input_inside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
			sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS finish_qnty_in,
			sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS finish_qnty_out,
			sum(CASE WHEN production_type=1 THEN reject_qnty ELSE 0 END) AS cutting_reject_qnty,
			sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id");//po_break_down_id in($all_po_id) and



			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>

                    <td align="right"><? echo number_format($row[csf('finish_qnty_in')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="finish_input_outside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75"> Date </th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$gmtsProdDataArr=sql_select("select  po_break_down_id,
			sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS finish_qnty_in,
			sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS finish_qnty_out,
			sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty
			from pro_garments_production_mst where  is_deleted=0 and status_active=1 and po_break_down_id=$po_id group by po_break_down_id");//po_break_down_id in($all_po_id) and



			$i=1;
			foreach($gmtsProdDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>

                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>

                    <td align="right"><? echo number_format($row[csf('finish_qnty_out')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="knit_prod_outside_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">prod. Id</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";


			$prodKnitDataArr=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
			sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
			sum(CASE WHEN a.entry_form=22 THEN a.quantity ELSE 0 END) AS knit_qnty_rec,c.recv_number,c.receive_date,b.prod_id,c.recv_number_prefix_num
			from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id and c.item_category=13 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id,c.recv_number,c.receive_date,b.prod_id");// and c.receive_basis<>9 and a.po_breakdown_id in($all_po_id)






			$i=1;
			foreach($prodKnitDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('receive_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('recv_number_prefix_num')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('knit_qnty_out')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="yarn_issued_in_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">Booking No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$yarnDataArr=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
			sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out,c.issue_number,c.issue_date
			from order_wise_pro_details a, inv_transaction b, inv_issue_master c
			where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id   and b.item_category=1 and c.issue_purpose in (1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
			group by a.po_breakdown_id,c.issue_number,c.issue_date");//and a.po_breakdown_id in($all_po_id)




				$yarn_issue_arr=array();
				foreach($yarnDataArr as $row)
				{
					$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_in"]=$row[csf("issue_qnty_in")];
					$yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_out"]=$row[csf("issue_qnty_out")];
				}

				$yarnReturnDataArr=sql_select("select a.po_breakdown_id,
					sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
					sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
					from order_wise_pro_details a, inv_transaction b, inv_receive_master c
					where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
					group by a.po_breakdown_id");// and a.po_breakdown_id in($all_po_id)


				$yarn_issue_rtn_arr=array();
				foreach($yarnReturnDataArr as $row)
				{
				$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
				$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
				}

			$i=1;
			foreach($yarnDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('issue_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('issue_number')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('issue_qnty_in')]-$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="yarn_issued_out_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>

                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">Booking No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$yarnDataArr=sql_select("select a.po_breakdown_id,
			sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
			sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out,c.issue_number,c.issue_date
			from order_wise_pro_details a, inv_transaction b, inv_issue_master c
			where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id   and b.item_category=1 and c.issue_purpose in (1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
			group by a.po_breakdown_id,c.issue_number,c.issue_date");//and a.po_breakdown_id in($all_po_id)






				$yarnReturnDataArr=sql_select("select a.po_breakdown_id,
					sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
					sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
					from order_wise_pro_details a, inv_transaction b, inv_receive_master c
					where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id=$po_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
					group by a.po_breakdown_id");// and a.po_breakdown_id in($all_po_id)


				$yarn_issue_rtn_arr=array();
				foreach($yarnReturnDataArr as $row)
				{
				$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
				$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
				}

			$i=1;
			foreach($yarnDataArr as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('issue_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('issue_number')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('issue_qnty_out')]-$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="yarn_purchase_req_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
             	<tr>
                	<th colspan="7">Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">Booking No</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			$job_no=str_replace("'","",$job_no);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$yarn_purchase_req_data=sql_select("select a.id as mst_id, a.company_id,a.basis,b.booking_no,b.booking_id, b.id, b.mst_id, b.job_id, b.job_no, b.buyer_id, b.style_ref_no, b.color_id, b.count_id, b.composition_id,b.com_percent, b.yarn_type_id, b.cons_uom, b.quantity, b.rate, b.amount, b.yarn_inhouse_date, b.remarks,b.yarn_finish,b.yarn_spinning_system,b.certification,a.requisition_date,a.requ_no from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.job_no='$job_no' order by b.id asc ");



			$i=1;
			foreach($yarn_purchase_req_data as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('requisition_date')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('requ_no')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('quantity')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if ($action=="fabric_booking_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

	//echo $po_id;
	?>
     <div style="width:500px;" align="center">
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
             <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="75">Date </th>
                    <th width="75">Booking No</th>
					<th width="75">Booking Type</th>
                    <th width="75">Qty</th>
                 </tr>
              </thead>
              <tbody>
			 <?
			$po_id=str_replace("'","",$po_id);
			$company=str_replace("'","",$company);
			$job_no=str_replace("'","",$job_no);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";

			$fabric_booking_data=sql_select("select a.id as booking_dtls_id,b.id, a.fabric_color_id, a.fin_fab_qnty, a.grey_fab_qnty, a.amount, a.rate, a.po_break_down_id,a.booking_no,c.booking_date,a.booking_type 	from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_booking_mst c where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id  and a.booking_mst_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and   a.po_break_down_id=$po_id and a.booking_type=1 and a.status_active=1 and a.is_deleted=0");




			$i=1;
			foreach($fabric_booking_data as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="left"><? echo $row[csf('booking_date')]; ?>&nbsp;</td>
                    <td align="left"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
					<td align="left"><? echo "main"; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('fin_fab_qnty')],0); ?>&nbsp;</td>
                 </tr>
             <?
			  	$i++;
			 }
			 ?>
             </tbody>

         </table>
     </div>
	<?
	exit();
}

if($action=="import_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($title, "../../../../", 1, 1,$unicode,'','');

	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$data_sql="SELECT to_char(a.wo_number) as WO_NUMBER, sum(b.amount) as WO_AMOUNT, c.id as PI_ID, c.PI_NUMBER, c.ITEM_CATEGORY_ID, c.PI_DATE, c.total_amount as PI_AMOUNT, c.net_total_amount as NET_PI_AMOUNT
	from wo_non_order_info_mst a, wo_non_order_info_dtls b, com_pi_master_details c, com_pi_item_details d
	where a.id=b.mst_id and c.id=d.pi_id and b.id=d.work_order_dtls_id and a.company_name=$company_id and c.importer_id=$company_id and b.po_breakdown_id=$po_id and b.job_no='$job_no' and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.item_category_id=1
	group by a.wo_number, c.id, c.pi_number, c.item_category_id, c.pi_date, c.total_amount, c.net_total_amount
	union all
	SELECT to_char(a.booking_no) as WO_NUMBER, sum(b.amount) as WO_AMOUNT, c.id as PI_ID, c.PI_NUMBER, c.ITEM_CATEGORY_ID, c.PI_DATE, c.total_amount as PI_AMOUNT, c.net_total_amount as NET_PI_AMOUNT
	from wo_booking_mst a, wo_booking_dtls b, com_pi_master_details c, com_pi_item_details d
	where a.booking_no=b.booking_no and c.id=d.pi_id and b.id=d.work_order_dtls_id and a.company_id=$company_id and c.importer_id=$company_id and b.po_break_down_id=$po_id and b.job_no='$job_no' and a.booking_type=b.booking_type and a.is_short=b.is_short and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.item_category_id=4
	group by a.booking_no, c.id, c.pi_number, c.item_category_id, c.pi_date, c.total_amount, c.net_total_amount
	union all
	SELECT to_char(a.booking_no) as WO_NUMBER, sum(b.amount) as WO_AMOUNT, c.id as PI_ID, c.PI_NUMBER, c.ITEM_CATEGORY_ID, c.PI_DATE, c.total_amount as PI_AMOUNT, c.net_total_amount as NET_PI_AMOUNT
	from wo_booking_mst a, wo_booking_dtls b, com_pi_master_details c, com_pi_item_details d
	where a.booking_no=b.booking_no and c.id=d.pi_id and a.id=d.work_order_id and a.company_id=$company_id and c.importer_id=$company_id and b.po_break_down_id=$po_id and b.job_no='$job_no' and a.booking_type=b.booking_type and a.is_short=b.is_short and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.item_category_id in (2,3)
	group by a.booking_no, c.id, c.pi_number, c.item_category_id, c.pi_date, c.total_amount, c.net_total_amount";
	// echo $data_sql;

	$data_result=sql_select($data_sql);
	$pi_id_arr=array();
	foreach($data_result as $row)
	{
		$pi_id_arr[$row["PI_ID"]]=$row["PI_ID"];
	}
	$pi_id_in=where_con_using_array($pi_id_arr,0,'b.pi_id');

	$btb_sql="SELECT a.id as BTB_ID, a.SUPPLIER_ID, a.LC_NUMBER, a.PAYTERM_ID, a.LC_DATE, a.LC_VALUE, b.PI_ID
	from com_btb_lc_master_details a, com_btb_lc_pi b
	where a.id=b.com_btb_lc_master_details_id and a.status_active=1 and b.status_active=1 and a.importer_id=$company_id $pi_id_in ";
	// echo $btb_sql;

	$btb_result=sql_select($btb_sql);
	$btb_arr=array();$btb_id_arr=array();
	foreach($btb_result as $row)
	{
		$btb_arr[$row["PI_ID"]]['PI_ID']=$row["PI_ID"];
		$btb_arr[$row["PI_ID"]]['BTB_ID']=$row["BTB_ID"];
		$btb_arr[$row["PI_ID"]]['SUPPLIER_ID']=$row["SUPPLIER_ID"];
		$btb_arr[$row["PI_ID"]]['LC_NUMBER']=$row["LC_NUMBER"];
		$btb_arr[$row["PI_ID"]]['PAYTERM_ID']=$row["PAYTERM_ID"];
		$btb_arr[$row["PI_ID"]]['LC_DATE']=$row["LC_DATE"];
		$btb_arr[$row["PI_ID"]]['LC_VALUE']=$row["LC_VALUE"];
		$btb_id_arr[$row["BTB_ID"]]=$row["BTB_ID"];
	}
	$btb_id_in=where_con_using_array($btb_id_arr,0,'b.btb_lc_id');

	$acceptance_sql="SELECT a.id as INVOICE_ID, a.INVOICE_NO, a.COMPANY_ACC_DATE, a.BANK_ACC_DATE, a.BANK_REF, b.PI_ID, b.BTB_LC_ID, sum(b.current_acceptance_value) as CURRENT_ACCEPTANCE_VALUE
	from com_import_invoice_mst a, com_import_invoice_dtls b
	where a.id=b.import_invoice_id and a.status_active=1 and b.status_active=1 $btb_id_in
	group by a.id, a.invoice_no, a.company_acc_date, a.bank_acc_date, a.bank_ref, b.pi_id, b.btb_lc_id";
	// echo $acceptance_sql;

	$acceptance_result=sql_select($acceptance_sql);
	$acceptance_arr=array();$invoice_id_arr=array();
	foreach($acceptance_result as $row)
	{
		$acceptance_arr[$row["BTB_LC_ID"]]['INVOICE_ID']=$row["INVOICE_ID"];
		$acceptance_arr[$row["BTB_LC_ID"]]['INVOICE_NO']=$row["INVOICE_NO"];
		$acceptance_arr[$row["BTB_LC_ID"]]['COMPANY_ACC_DATE']=$row["COMPANY_ACC_DATE"];
		$acceptance_arr[$row["BTB_LC_ID"]]['BANK_ACC_DATE']=$row["BANK_ACC_DATE"];
		$acceptance_arr[$row["BTB_LC_ID"]]['BANK_REF']=$row["BANK_REF"];
		$acceptance_arr[$row["BTB_LC_ID"]]['CURRENT_ACCEPTANCE_VALUE']=$row["CURRENT_ACCEPTANCE_VALUE"];
		$invoice_id_arr[$row["INVOICE_ID"]]=$row["INVOICE_ID"];
	}

	$invoice_id_in=where_con_using_array($invoice_id_arr,0,'a.invoice_id');
	$payment_atsight_sql="SELECT a.SYSTEM_NUMBER, a.PAYMENT_DATE, a.INVOICE_ID, b.ACCEPTED_AMMOUNT
	from com_import_payment_com_mst a, com_import_payment_com b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.company_id=$company_id $invoice_id_in";
	// echo $payment_atsight_sql;

	$payment_atsight_result=sql_select($payment_atsight_sql);
	$payment_atsight_arr=array();
	foreach($payment_atsight_result as $row)
	{
		$payment_atsight_arr[$row["INVOICE_ID"]]['INVOICE_ID']=$row["INVOICE_ID"];
		$payment_atsight_arr[$row["INVOICE_ID"]]['SYSTEM_NUMBER'].=$row["SYSTEM_NUMBER"].", ";
		$payment_atsight_arr[$row["INVOICE_ID"]]['PAYMENT_DATE'].=change_date_format($row["PAYMENT_DATE"]).", ";
		$payment_atsight_arr[$row["INVOICE_ID"]]['ACCEPTED_AMMOUNT']+=$row["ACCEPTED_AMMOUNT"];
	}

	$payment_usance_sql="SELECT a.SYSTEM_NUMBER, a.PAYMENT_DATE, a.INVOICE_ID, b.ACCEPTED_AMMOUNT
	from com_import_payment_mst a, com_import_payment b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $invoice_id_in";
	// echo $payment_usance_sql;

	$payment_usance_result=sql_select($payment_usance_sql);
	$payment_usance_arr=array();
	foreach($payment_usance_result as $row)
	{
		$payment_usance_arr[$row["INVOICE_ID"]]['INVOICE_ID']=$row["INVOICE_ID"];
		$payment_usance_arr[$row["INVOICE_ID"]]['SYSTEM_NUMBER'].=$row["SYSTEM_NUMBER"].", ";
		$payment_usance_arr[$row["INVOICE_ID"]]['PAYMENT_DATE'].=change_date_format($row["PAYMENT_DATE"]).", ";;
		$payment_usance_arr[$row["INVOICE_ID"]]['ACCEPTED_AMMOUNT']+=$row["ACCEPTED_AMMOUNT"];
	}

	?>
	<fieldset style="width:750px;" >
	<legend>IMPORT STATEMENT</legend>
		<div style="width:100%" id="report_container">
			<table width="1700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th colspan="7">PI Details</th>
						<th colspan="5">BTB LC Details</th>
						<th colspan="4">Acceptance Details</th>
						<th colspan="4">Payment Details</th>
					</tr>
					<tr>
						<!-- PI Details -->
						<th width="80">PI Number</th>
						<th width="80">Item Category</th>
						<th width="80">PI Date</th>
						<th width="80">Work Order Number</th>
						<th width="80">WO Value</th>
						<th width="80">PI Value</th>
						<th width="80">Net PI Value</th>
						<!-- BTB LC Details -->
						<th width="80">Supplier</th>
						<th width="80">BTB LC No</th>
						<th width="80">Pay Terms</th>
						<th width="80">LC Date</th>
						<th width="80">LC Value</th>
						<!-- Acceptance Details -->
						<th width="80">Company Accpt. Date</th>
						<th width="80">Bank Accpt. Date</th>
						<th width="80">Invoice No</th>
						<th width="80">Value</th>
						<!-- Payment Details -->
						<th width="80">Date</th>
						<th width="80">System Number</th>
						<th width="80">Bank Reference</th>
						<th >Payment Value</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($data_result as $row)
					{
						if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<!-- PI Details -->
							<td ><? echo $row["PI_NUMBER"]; ?></td>
							<td ><? echo $item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
							<td align="center"><? echo change_date_format($row["PI_DATE"]); ?></td>
							<td ><? echo $row["WO_NUMBER"]; ?></td>
							<td align="right"><? echo number_format($row["WO_AMOUNT"],2); ?></td>
							<td align="right"><? echo number_format($row["PI_AMOUNT"],2); ?></td>
							<td align="right"><? echo number_format($row["NET_PI_AMOUNT"],2); ?></td>
							<!-- BTB LC Details -->
							<td ><? echo $supplier_lib[$btb_arr[$row["PI_ID"]]['SUPPLIER_ID']]; ?></td>
							<td ><? echo $btb_arr[$row["PI_ID"]]['LC_NUMBER']; ?></td>
							<td align="center"><? echo $pay_term[$btb_arr[$row["PI_ID"]]['PAYTERM_ID']]; ?></td>
							<td align="center"><? echo change_date_format($btb_arr[$row["PI_ID"]]['LC_DATE']); ?></td>
							<td align="right"><? echo number_format($btb_arr[$row["PI_ID"]]['LC_VALUE'],2); ?></td>
							<!-- Acceptance Details -->
							<td align="center"><? echo change_date_format($acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['COMPANY_ACC_DATE']); ?></td>
							<td align="center"><? echo change_date_format($acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['BANK_ACC_DATE']); ?></td>
							<td ><? echo $acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_NO']; ?></td>
							<td align="right"><? echo number_format($acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['CURRENT_ACCEPTANCE_VALUE'],2); ?></td>
							<!-- Payment Details -->
							<?
								if($btb_arr[$row["PI_ID"]]['PAYTERM_ID']==1)
								{
									?>
										<td align="center"><? echo rtrim($payment_atsight_arr[$acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_ID']]['PAYMENT_DATE'],", ") ; ?></td>
										<td ><? echo rtrim($payment_atsight_arr[$acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_ID']]['SYSTEM_NUMBER'],", ") ; ?></td>
										<td ><? echo $acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['BANK_REF']; ?></td>
										<td align="right"><? echo number_format($payment_atsight_arr[$acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_ID']]['ACCEPTED_AMMOUNT'],2) ; ?></td>
									<?
								}
								else
								{
									?>
										<td align="center"><? echo rtrim($payment_usance_arr[$acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_ID']]['PAYMENT_DATE'],", ") ; ?></td>
										<td ><? echo rtrim($payment_usance_arr[$acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_ID']]['SYSTEM_NUMBER'],", ") ; ?></td>
										<td ><? echo $acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['BANK_REF']; ?></td>
										<td align="right"><? echo number_format($payment_usance_arr[$acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_ID']]['ACCEPTED_AMMOUNT'],2) ; ?></td>
									<?
								}
							?>
						</tr>
						<?
						$i++;
						$total_wo_po_ammount += $row["WO_AMOUNT"]  ;
						$total_pi_ammount += $row["PI_AMOUNT"] ;
						$total_net_pi_ammount += $row["NET_PI_AMOUNT"] ;
						$total_lc_val += $btb_arr[$row["PI_ID"]]['LC_VALUE'];
						$current_acc_val += $acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['CURRENT_ACCEPTANCE_VALUE'] ;
						if($btb_arr[$row["PI_ID"]]['PAYTERM_ID']==1)
					    {
							$total_accepted_ammount += 	$payment_atsight_arr[$acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_ID']]['ACCEPTED_AMMOUNT'];
						}
						else{
							$total_accepted_ammount +=	$payment_usance_arr[$acceptance_arr[$btb_arr[$row["PI_ID"]]['BTB_ID']]['INVOICE_ID']]['ACCEPTED_AMMOUNT'];
						}
					}
					?>
				</tbody>
				<tfoot>
					<th colspan="5">Total</th>
					<th><? echo number_format($total_wo_po_ammount,2)  ?></th>
					<th><? echo number_format($total_pi_ammount,2)  ?></th>
					<th><? echo number_format($total_net_pi_ammount,2)  ?></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th><? echo number_format($total_lc_val,2) ?></th>
					<th></th>
					<th></th>
					<th></th>
					<th><? echo number_format($current_acc_val,2) ?></th>
					<th></th>
					<th></th>
					<th></th>
					<th><? echo number_format($total_accepted_ammount,2) ?></th>


				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="export_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($title, "../../../../", 1, 1,$unicode,'','');

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");

	$sub_sql=sql_select("SELECT b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date
	from com_export_doc_submission_mst a,com_export_doc_submission_invo b
	where a.id=b.doc_submission_mst_id and a.company_id=$company_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$bank_sub_data=array();

	foreach($sub_sql as $row)
	{
		$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
		$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
		$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
		$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
		$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
	}

	$buyer_submit_date_arr=return_library_array("SELECT b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.company_id=$company_id and a.entry_form=39","invoice_id","submit_date");

	$rlz_date_arr=return_library_array(" SELECT a.invoice_id,b.received_date,b.is_invoice_bill
	from com_export_doc_submission_invo a, com_export_proceed_realization b
	where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill=1 and b.benificiary_id=$company_id
	union all
	select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
	from  com_export_proceed_realization b
	where  b.is_invoice_bill  = 2
	order by invoice_id","invoice_id","received_date");

	$rlz_date_res = sql_select("SELECT  a.invoice_id,b.received_date,b.is_invoice_bill ,c.type,   sum( c.document_currency) as document_currency
	from com_export_doc_submission_invo a, com_export_proceed_realization b , com_export_proceed_rlzn_dtls c
	where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill=1 and b.id = c.mst_id and b.benificiary_id=$company_id
	group by  a.invoice_id,b.received_date,b.is_invoice_bill , c.type
	union all
	select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill, c.type,  sum(c.document_currency) as document_currency
	from  com_export_proceed_realization b , com_export_proceed_rlzn_dtls c
	where  b.is_invoice_bill  = 2 and b.id = c.mst_id and b.benificiary_id=$company_id
	group by b.invoice_bill_id, b.received_date , b.is_invoice_bill, c.type
	order by invoice_id");

	$rlzdtlsChk =array();// $rlz_date_arr=array();
	foreach ($rlz_date_res as $val)
	{
		if($val[csf("type")]==0)
		{
			$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["deduct"] += $val[csf("document_currency")];
		}
		else
		{
			$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["dist"] += $val[csf("document_currency")];
		}
			$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["total"] += $val[csf("document_currency")];

	}

	$exfact_qnty_arr=return_library_array(" SELECT invoice_no,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
	from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
	$variable_standard_arr=return_library_array(" select monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$company_id and variable_list=19","monitor_head_id","monitoring_standard_day");

	$sql_order_set=sql_select("SELECT a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_id=c.id and c.id=$job_id and b.id=$po_id and a.status_active=1 and a.is_deleted=0");
	$inv_qnty_pcs_arr=array();
	foreach($sql_order_set as $row)
	{
		$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
	}

	$find_lc_sc_all=sql_select("SELECT a.id as LC_SC_ID, 1 as TYPE
	FROM com_export_lc a, com_export_lc_order_info b
	WHERE a.beneficiary_name=$company_id and a.id=b.com_export_lc_id and b.wo_po_break_down_id=$po_id and a.status_active=1 and b.status_active=1
	UNION ALL
	SELECT  a.id as LC_SC_ID, 2 as TYPE
	FROM com_sales_contract a, com_sales_contract_order_info b
	WHERE a.beneficiary_name=$company_id and a.id=b.com_sales_contract_id and b.wo_po_break_down_id=$po_id and a.status_active=1 and b.status_active=1 ");
	$lc_id=$sc_id="";
	foreach($find_lc_sc_all as $row)
	{
		if($row["TYPE"]==1)
		{
			$lc_id.=$row["LC_SC_ID"].",";
		}
		else
		{
			$sc_id.=$row["LC_SC_ID"].",";
		}
	}
	$lc_id=implode(",",array_unique(explode(",",chop($lc_id,','))));
	$sc_id=implode(",",array_unique(explode(",",chop($sc_id,','))));
	$sql="";
	if($db_type==0)
	{
		if($lc_id!="")
		{
			$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type
			FROM com_export_invoice_ship_mst a, com_export_lc b
			WHERE a.benificiary_id=$company_id and b.id in ($lc_id)and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
		}
		if($sc_id!="")
		{
			if($sql!=""){$sql.=" UNION ALL ";}
			$sql.="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
			FROM com_export_invoice_ship_mst a, com_sales_contract c
			WHERE a.benificiary_id=$company_id and c.id in ($sc_id) and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
		}
	}
	else
	{
		if($lc_id!="")
		{
			$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, d.current_invoice_qnty as invoice_quantity, d.current_invoice_value as invoice_value, 1 as type
			FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls d
			WHERE a.benificiary_id=$company_id and b.id in ($lc_id) and a.is_lc=1 and a.lc_sc_id=b.id and d.mst_id=a.id and d.po_breakdown_id=$po_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and d.status_active=1";
		}
		if($sc_id!="")
		{
			if($sql!=""){$sql.=" UNION ALL ";}
			$sql.="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, d.current_invoice_qnty as invoice_quantity, d.current_invoice_value as invoice_value, 2 as type
			FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls d
			WHERE a.benificiary_id=$company_id and c.id in ($sc_id) and a.is_lc=2 and a.lc_sc_id=c.id and d.mst_id=a.id and d.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and d.status_active=1";
		}
	}
	// echo $sql;
	$sql_re=sql_select($sql);
	?>
	<fieldset style="width:1840px;" >
	<legend>EXPORT STATEMENT</legend>
	<div style="width:1840px">
		<br />
		<table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="50">Sl</th>
					<th width="100">Invoice No.</th>
					<th width="70">Invoice Date</th>
					<th width="70">SC/LC</th>
					<th width="100">SC/LC No.</th>
					<th width="70">Buyer Name</th>
					<th width="100">Ex-factory Qnty</th>
					<th width="100">Invoice Qnty.</th>
					<th width="100">Invoice Qnty. Pcs</th>
					<th width="100">Invoice value</th>
					<th width="100">Net Invoice Amount</th>
					<th width="80">Currency</th>
					<th width="70">Ex-Factory Date</th>
					<th width="100">Bank Bill No.</th>
					<th width="70">Bank Bill Date</th>
					<th width="80">Pay Term</th>
					<th width="70">Actual Realized Date</th>
					<th width="70">Realization Amount</th>
					<th width="100">Distributions</th>
					<th width="100">Deduction at source</th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1840px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
			<table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
				<?

				$k=1;$gb=1;
				foreach($sql_re as $row_result)
				{
					if ($k%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$id=$row_result[csf('id')];
					$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
					$bl_date_calculate=$row_result[csf('bl_date')];
					$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
					if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
					{
						$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
						$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);

					}
					if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
					{
						if($row_result[csf("type")]==1)
						{
							$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
							$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
							$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
						}
						if($row_result[csf("type")]==2)
						{
							$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
							$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
							$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
						}
						$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
						$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
						$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
						$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);

					}
					if($group_buyer[$row_result[csf('buyer_id')]]=="")
					{
						$group_buyer[$row_result[csf('buyer_id')]]=$row_result[csf('buyer_id')];
						if($gb!=1)
						{
							?>
							<tr bgcolor="#EFEFEF">
								<th width="50">&nbsp;</th>
								<th width="100">&nbsp;</th>

								<th width="70">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="70">Sub Total:</th>

								<th width="100" align="right"><? echo number_format($sub_ex_fact_qnty,2); ?></th>
								<th width="100" align="right"><? echo number_format($sub_invoice_qty,2); ?></th>
								<th width="100" align="right"><? echo number_format($sub_invoice_qty_pcs,2); ?></th>
								<th width="100" align="right"><?  ?></th>
								<th width="100"  align="right"><? echo number_format($sub_order_qnty,2);  ?></th>
								<th width="80">&nbsp;</th>

								<th width="70">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="70">&nbsp;</th>


								<th width="80">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="70" align="right"><? echo number_format($sub_rlz_amt,2); ?></th>
								<th width="100" align="right"><? echo fn_number_format($sub_rlz_dist,2); ?></th>
								<th width="100" align="right"><? echo fn_number_format($sub_rlz_deduct,2); ?></th>
								<th >&nbsp;</th>
							</tr>
							<?
							$sub_ex_fact_qnty=$sub_invoice_qty=$sub_invoice_qty_pcs=$sub_order_qnty=$sub_rlz_amt=$sub_rlz_dist=$sub_rlz_deduct=$distribution_amt=$diductiontion_amt=0;
						}
						?>
						<tr bgcolor="#EFEFEF">
							<td colspan="21"><b><? echo $buyer_arr[$row_result[csf('buyer_id')]];?></b></td>
						</tr>
						<?
						$gb++;
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
						<td width="50"><? echo $k;//$row_result[csf('id')];?></th>

						<td width="100"><? echo $row_result[csf('invoice_no')];?></td>

						<td width="70" align="center">
						<? if($row_result[csf('invoice_date')]!="0000-00-00" && $row_result[csf('invoice_date')]!="") {echo change_date_format($row_result[csf('invoice_date')]);} else {echo "&nbsp;";}?>
						</td>

						<td width="70"  align="center"><? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?></td>
						<td width="100"><? echo $row_result[csf('lc_sc_no')];?></td>
						<td width="70"><? echo  $buyer_arr[$row_result[csf('buyer_id')]];?></td>

						<td width="100" align="right">
							<? echo  number_format($exfact_qnty_arr[$row_result[csf('id')]],2);
							$total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];$sub_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];
							?>
						</td>
						<td width="100" align="right"><? echo number_format($row_result[csf('invoice_quantity')],2); $total_invoice_qty +=$row_result[csf('invoice_quantity')]; $sub_invoice_qty +=$row_result[csf('invoice_quantity')];?></td>
						<td width="100" align="right"><? echo number_format($inv_qnty_pcs_arr[$row_result[csf('id')]],2); $total_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]]; $sub_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]];?></td>

						<td width="100" align="right"><? echo number_format($row_result[csf('invoice_value')],2,'.',''); $total_grs_value +=$row_result[csf('invoice_value')];?></td>

						<td width="100" align="right"><? echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_order_qnty +=$row_result[csf('net_invo_value')]; $sub_order_qnty +=$row_result[csf('net_invo_value')];?></td>
						<td width="80" align="center"><? echo $currency[$row_result[csf('currency_name')]];?></td>


						<td width="70"  align="center"><? if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {echo change_date_format($row_result[csf('ex_factory_date')]);} else {echo "&nbsp;";} ?></td>

						<td width="100"><? echo $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"]; ?></td>
						<td width="70"   align="center"><?

						if(!(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])==""))
						{
							echo change_date_format($bank_sub_data[$row_result[csf('id')]]["submit_date"]);
						}
						else
						{
							echo "&nbsp;";
						}
						?></td>

						<td width="80"><? echo $pay_term[$row_result[csf('pay_term')]];?></td>

						<td width="70"   align="center">
						<?
						if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
						{
							echo change_date_format($rlz_date_arr[$row_result[csf('id')]]);
						}
						else
						{
							echo "&nbsp;";
						}
							?>
						</td>

						<td width="70" align="right"><? if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
						{
							echo number_format($row_result[csf('net_invo_value')],2,'.','');
							$total_rlz_amt+=$row_result[csf('net_invo_value')]; $sub_rlz_amt+=$row_result[csf('net_invo_value')];
						}
						else
						{
							echo "";
						}
						?></td>
						<td width="100" align="right" title="invoice Distribution share =  Distribution X ( invoice Realization / total Realization)">

								<?
								$distribution_amt=$rlz_invoice_deduc_dist[$row_result[csf('id')]]["dist"] * ($row_result[csf('net_invo_value')]/($rlz_invoice_deduc_dist[$row_result[csf('id')]]["total"]));

										echo fn_number_format($distribution_amt,2,".","");
										//echo $distribution_amt;
										if(fn_number_format($distribution_amt,2,".","")!='')
										{
											$sub_rlz_dist += $distribution_amt;
											$total_rlz_dist += $distribution_amt;
										}


								?>

						</td>
						<td width="100" align="right" title="invoice Deduction share =  Deduction X ( invoice Realization / total Realization)">

								<?
									$diductiontion_amt=$rlz_invoice_deduc_dist[$row_result[csf('id')]]["deduct"] * ($row_result[csf('net_invo_value')]/$rlz_invoice_deduc_dist[$row_result[csf('id')]]["total"]);
									echo fn_number_format($diductiontion_amt,2,".","");
									if(fn_number_format($diductiontion_amt,2,".","")!='')
									{
										$total_rlz_deduct += $diductiontion_amt;

										$sub_rlz_deduct += $diductiontion_amt;
									}
								?>

						</td>
						<td><? echo $row_result[csf('remarks')];?></td>

					</tr>
					<?
					$k++;
				}
				?>
				</tbody>
			</table>
			<table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
				<tfoot>
					<tr class="tbl_bottom">
						<th width="50">&nbsp;</th>
						<th width="100">&nbsp;</th>

						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">Sub Total:</th>

						<th width="100" align="right"><? echo number_format($sub_ex_fact_qnty,2); ?></th>
						<th width="100" align="right"><? echo number_format($sub_invoice_qty,2); ?></th>
						<th width="100" align="right"><? echo number_format($sub_invoice_qty_pcs,2); ?></th>
						<th width="100" align="right"><?  ?></th>
						<th width="100"  align="right"><? echo number_format($sub_order_qnty,2);  ?></th>
						<th width="80">&nbsp;</th>

						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>


						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70" align="right"><? echo number_format($sub_rlz_amt,2); ?></th>
						<th width="100" align="right"><? echo fn_number_format($sub_rlz_dist,2); ?></th>
						<th width="100" align="right"><? echo fn_number_format($sub_rlz_deduct,2); ?></th>
						<th >&nbsp;</th>
					</tr>
					<tr>
						<th width="50">&nbsp;</th>
						<th width="100">&nbsp;</th>

						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">Total:</th>

						<th width="100" id="value_total_ex_fact_qnty" align="right"><? echo number_format($total_ex_fact_qnty,2); ?></th>
						<th width="100" id="value_total_invoice_qty" align="right"><? echo number_format($total_invoice_qty,2); ?></th>
						<th width="100" id="value_total_invoice_qty_pcs" align="right"><? echo number_format($total_invoice_qty_pcs,2); ?></th>
						<th width="100"  align="right"><?  ?></th>
						<th width="100"  id="value_total_net_invo_value"  align="right"><? echo number_format($total_order_qnty,2);  ?></th>
						<th width="80">&nbsp;</th>

						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>


						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70"  id="value_total_rlz_amt"  align="right"><? echo number_format($total_rlz_amt,2);?></th>
						<th width="100" id="value_total_rlz_dist" align="right"><? echo fn_number_format($total_rlz_dist,2)?></th>
						<th width="100" id="value_total_rlz_deduct" align="right"><? echo fn_number_format($total_rlz_deduct,2);?></th>
						<th >&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	</fieldset>
	<?

	exit();
}
//disconnect($con);
?>
