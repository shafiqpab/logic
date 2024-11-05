<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
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

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$item_library=return_library_array( "select id,item_name from  lib_item_group", "id", "item_name"  );
$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name"  );


if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
			exit();
}
if ($action=="load_drop_down_file_year")
{
	/* $sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0  
	union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0";
	echo create_drop_down( "cbo_file_year", 80,$sql,"lc_sc_year,lc_sc_year", 1, "-- Select --", 1,""); */
	echo create_drop_down( "cbo_file_year", 65, create_year_array(),"", 0,"-- --", date("Y",time()), "",0,"" );
	exit();
}

if ($action=="report_button_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=62 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
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

		function js_set_value( str,tr_id ) {
			if (str!="") str=str.split("_");

			//alert(str_all[2]+'='+tr_id);
			if ( document.getElementById('txt_buyer_id').value!="" && document.getElementById('txt_buyer_id').value!=str[3] )
			{
				alert('Buyer mix not allow');return;
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('txt_buyer_id').value=str[3];

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
				if(selected_id.length==0){
					document.getElementById('txt_buyer_id').value="";
				}
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_quotation_no_search_list_view', 'search_div', 'cost_breakup_report2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if($action=="create_quotation_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//echo $data[1];
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$data[1]";
	}
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	$search_field_cond="";
	if($data[3]!="")
	{
	if($search_by==1)
		$search_field_cond=" and a.style_ref LIKE '%".trim($data[3])."%'";
	else if($search_by==2)
		$search_field_cond=" and a.inquery_id='".trim($data[3])."'";
	else if($search_by==3)
		$search_field_cond=" and a.id=".trim($data[3])."";
	else if($search_by==4)
		$search_field_cond=" and a.mkt_no='".trim($data[3])."'";
	}

	$start_date =trim($data[4]);
	$end_date =trim($data[5]);
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.quot_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.quot_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	$arr=array(0=>$company_library,1=>$buyer_arr);
	$sql= "select a.id, $year_field a.inquery_id, a.company_id,a.mkt_no, a.buyer_id,a.quot_date, a.style_ref from wo_price_quotation a where  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $buyer_id_cond $date_cond order by a.id, a.quot_date";
	echo create_list_view("tbl_list_search", "Company,Buyer,Year,Quotation No,Style Ref.,Mkt No,Inquery ID, Quotation Date", "80,130,50,60,130,50,130","800","220",0, $sql , "js_set_value", "id,id,buyer_id", "this.id", 1, "company_id,buyer_id,0,0,0,0,0,0", $arr , "company_id,buyer_id,year,id,style_ref,mkt_no,inquery_id,quot_date", "",'','0,0,0,0,0,0,0,3','',1) ;
	echo "<input type='hidden' id='txt_buyer_id' />";
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'create_list_style_search', 'search_div', 'cost_breakup_report2_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
if($action=="create_list_style_search")
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

	//echo $search_type;

	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select a.id,a.style_ref_no,a.quotation_id,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a where a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num";
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,QuotationID,Job No,Year","160,100,90,100","500","200",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,quotation_id,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'create_list_style_search', 'search_div', 'cost_breakup_report2_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>', 'order_search_list_view', 'search_div', 'cost_breakup_report2_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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

if($action=="order_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$start_date,$end_date,$cbo_year_selection,$txt_style_ref,$style_ref_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$style_ref_id=str_replace("'","",$style_ref_id);
	$cbo_year=str_replace("'","",$cbo_year_selection);
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

	//echo $txt_style_ref.'='.$style_ref_id;
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and b.id in(".str_replace("'","",$style_ref_id).") ";
		}
		else
		{
			$job_style_cond=" and b.style_ref_no like '%".trim(str_replace("'","",$txt_style_ref))."%'";
		}
	}

	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($txt_style_ref!="")
	{
		//if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref) and year(b.insert_date)= '$cbo_year' ";
		//else $style_cond="and b.job_no_prefix_num in($txt_style_ref) and to_char(b.insert_date,'YYYY')= '$cbo_year' ";
	}
	//else $style_cond="";

	//echo $style_cond."jahid";die;
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.quotation_id,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $year_cond $job_style_cond $search_con $date_cond and a.status_active=1";
	//echo $sql;
	echo create_list_view("list_view", "Order NO,Job No,QuotationId,Year,Style Ref No","150,80,70,70,150","570","150",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,quotation_id,job_year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;
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
		var selected_year = new Array;
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

		function js_set_value( strCon ) {
			if (strCon!="") str=strCon.split("_");
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			var selectYear = splitSTR[3];

			//alert(str_all[2]+'='+tr_id);
			if ( document.getElementById('txt_selected_year').value!="" && document.getElementById('txt_selected_year').value!=str[3] )
			{
				alert('Not Allow Multiple Year');return;
			}
			toggle( str_or, '#FFFFCC');
			document.getElementById('txt_selected_year').value=str[3];

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
				if(selected_id.length==0){
					document.getElementById('txt_selected_year').value="";
				}
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			
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
                    <th id="search_by_td_up">Please Enter File No</th>
                    <th>File Year</th>
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
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "4",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td" width="130">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td>
                        	<?php 
                        		$sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$company and status_active=1 and is_deleted=0 union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$company and status_active=1 and is_deleted=0";
								echo create_drop_down( "cbo_file_year", 80,$sql,"lc_sc_year,lc_sc_year", 1, "-- Select --", 1,"");

                        	 ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $style_ref_id; ?>'+'**'+document.getElementById('cbo_file_year').value, 'file_order_search_list_view', 'search_div', 'cost_breakup_report2_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="6" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="file_order_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$start_date,$end_date,$cbo_year_selection,$txt_style_ref,$style_ref_id,$cbo_file_year)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$style_ref_id=str_replace("'","",$style_ref_id);
	$cbo_file_year=str_replace("'","",$cbo_file_year);

	$file_year_cond='';
	if(!empty($cbo_file_year))
	{
	 	$file_year_cond=" and a.file_year='".$cbo_file_year."' ";
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
	elseif($search_type==4 && $search_value!=''){
		$search_con=" and a.file_no = '$search_value'";
		if($cbo_file_year==0){
			echo "<b style='color:red;font-size: 15px;'>please select file year</b>";die;
			
		}
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

	$job_style_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$style_ref_id)!="")
		{
			$job_style_cond=" and b.id in(".str_replace("'","",$style_ref_id).") ";
		}
		else
		{
			$job_style_cond=" and b.style_ref_no like '%".trim(str_replace("'","",$txt_style_ref))."%'";
		}
	}

	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";

	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,a.file_no,a.file_year,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $year_cond $job_style_cond $search_con $date_cond $file_year_cond and a.status_active=1";
	//echo $sql;
	echo create_list_view("list_view", "Order NO,File No,File Year,Job No,Year,Style Ref No","150,100,100,80,70,150","700","150",0, $sql , "js_set_value", "id,file_no,file_year", "", 1, "0", $arr, "po_number,file_no,file_year,job_no_prefix_num,job_year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	echo "<input type='hidden' id='txt_selected_year' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_order_id;?>';
	var style_des='<? echo $txt_order;?>';
	var style_year='<? echo $txt_order;?>';
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
                                if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
                            }
                            else $buyer_id_cond="";
                        }
                        else $buyer_id_cond=" and b.buyer_id=$buyerID";

                       // if($job_no!=0) $jobno=" and job_no_prefix_num in (".$job_no.")"; else $jobno="";

                           $sql="select distinct(b.season_name) as season,b.id from lib_buyer_season b where  b.status_active=1 and b.is_deleted=0   $buyer_id_cond group by b.season_name,b.id order by b.season_name";
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

	if(isset($_GET['action']))
	{
		$reporttype=str_replace("'","",$_GET['reporttype']);
		$cbo_company_name=str_replace("'","",$_GET['cbo_company_name']);
		$txt_style_ref=str_replace("'","",$_GET['txt_style_ref']);
		$txt_quotation_id=str_replace("'","",$_GET['txt_quotation_id']);
		$cbo_buyer_name=str_replace("'","",$_GET['cbo_buyer_name']);
		$report_title=str_replace("'","",$_GET['report_title']);
		$txt_hidden_quot_id = str_replace("'","",$_GET['txt_quotation_id']);
		$comments_head = str_replace("'","",$_GET['comments_head']);
		$version=str_replace("'","",$_GET['version']);
	}
	else
	{
		
		$process = array( &$_GET );
		extract(check_magic_quote_gpc( $process ));
		$reporttype=str_replace("'","",$reporttype);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_style_owner=str_replace("'","",$cbo_style_owner);
		$txt_order=str_replace("'","",$txt_order);
		$txt_order_id=str_replace("'","",$txt_order_id);
		$file_no=str_replace("'","",$txt_file_no);
		$cbo_file_year=str_replace("'","",$cbo_file_year);
		$file_po_id=str_replace("'","",$txt_file_id);
		$file_no=rtrim($file_no,',');
		$txt_style_ref=str_replace("'","",$txt_style_ref);
		$txt_season_id=str_replace("'","",$txt_season_id);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$style_ref_id=str_replace("'","",$txt_style_ref_id);
		$txt_quotation_id=str_replace("'","",$txt_quotation_id);
		$txt_hidden_quot_id=str_replace("'","",$txt_hidden_quot_id);
		$comments_head=str_replace("'","",$comments_head);
		$revised_no=str_replace("'","",$revised_no);
	}

		// echo $version;die;
			

		if($file_no!="")
		{
			$file_no_arr=array_unique(explode(",",$file_no));

		
			$f=1;
			$file_no="";
			foreach($file_no_arr as $val){
			
				if($f==1){
					$file_no="'".$val."'";
					$f++;
				}else{

					$file_no .=",'".$val."'";
				}
				}
		}


	
			if($txt_hidden_quot_id!='')
			{
				$qoutation_id=$txt_hidden_quot_id;
			}
			else
			{
				$qoutation_id=$txt_quotation_id;//implode(",",array_unique(explode("*",$txt_quotation_id)));
			}
			//echo $reporttype.'-'.$txt_quotation_id.'-'.$txt_hidden_quot_id;
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
					if($file_po_id!="")
					{
						$file_po_idCond="and b.id in($file_po_id)";
					} 
					else {
						$file_no_cond="";
						if(!empty($file_no))
						{
							$file_nos=explode(",", $file_no);
							$file_no_cond=where_con_using_array($file_nos,1,"b.file_no");
						}
						 $file_po_idCond="";
					}

					

					$file_year_cond="";
					if(!empty($cbo_file_year))
					{
						$file_year_cond=" and b.file_year ='".$cbo_file_year."'";
					} 
					$file_year_cond="";
					if(!empty($cbo_file_year))
					{
						$file_year_cond=" and b.file_year ='".$cbo_file_year."'";
					} 
					

			ob_start();

			$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	if($reporttype==1 || $reporttype==7) //Budget Button
	{
		
		$sql="select a.id as job_id,a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order  by b.id";

				$sql_po_result=sql_select($sql);
				$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer="";
				$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;
				//echo $buyer_name;die;
				$job_idArr=array();
				foreach($sql_po_result as $row)
				{
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
					if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
					if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
					if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
					if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
					if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];

					/*$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$total_order_qty+=$row[csf('po_quantity')];
					$total_unit_price+=$row[csf('unit_price')];
					$total_fob_value+=$row[csf('po_total_price')];*/
					$po_qty_by_job[$row[csf("job_no")]]+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$job_idArr[$row[csf("job_id")]]=$row[csf('job_id')];
				}
				$sql_po="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price,c.order_rate, b.pub_shipment_date,c.order_total,c.order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c   where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and c.po_break_down_id=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond    order  by b.id";

				$sql_po_color_result=sql_select($sql_po);
				foreach($sql_po_color_result as $row)
				{
					$order_qty_pcs+=$row[csf('order_quantity')];
					$total_order_qty+=$row[csf('order_quantity')];
					$total_unit_price+=$row[csf('order_rate')];
					$total_fob_value+=$row[csf('order_total')];
				}
				unset($sql_po_color_result);
				
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
				unset($sql_std_para);
				   $nameArray=sql_select( "select commercial_cost_method,id,commercial_cost_percent from  variable_order_tracking where company_name=$cbo_company_name and variable_list=27 order by id" );
				   $commercial_cost_method=$commercial_cost_percent=0;
				   foreach($nameArray as $row)
					{
						$commercial_cost_method=$row[csf('commercial_cost_method')];
						$commercial_cost_percent=$row[csf('commercial_cost_percent')];
					}
					//echo $commercial_cost_method.'=';
					unset($nameArray);

				$sql_pre="select a.job_no,a.approved,a.costing_date,a.machine_line as machine_line,a.job_no, a.prod_line_hr, a.sew_smv, a.sew_effi_percent as sew_effi_percent, a.budget_minute,b.cost_pcs_set,b.price_pcs_or_set,remarks from wo_pre_cost_mst a,wo_pre_cost_dtls b where  a.job_no=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in(".$all_full_job.")  order  by a.id";
				

				    $sql_pre_result=sql_select($sql_pre);
					$sew_smv='';$machine_line='';$prod_line_hr='';$prod_line_hr='';$sew_effi_percent='';$budget_minute=0;
					$approved_msg='';
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
							$remarks.=$row[csf("remarks")].'.';
							if($row[csf("approved")]==1) 
							{
								$approved_msg="This Job Is Approved.";
							}
							else if($row[csf("approved")]==3) 
							{
								$approved_msg="This Job Is Partial Approved";
							}
							//$price_pcs_or_set+=$row[csf('price_pcs_or_set')];
							//$cost_pcs_set+=$row[csf('cost_pcs_set')];
					}
					unset($sql_pre_result);
					//print_r($smv_avg_by_job);
					//echo $sew_smv;
					//print_r($costing_date_arr);
					$condition= new condition();
					if($version != ""){
						$condition->approved_no("=$version");
						$condition->approval_from("=15");
						$version_no =" [ Version: $version ]";
					}
					$condition->company_name("=$cbo_company_name");
				  if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if($txt_order_id!='' || $txt_order_id!=0)
				 {
					$condition->po_id("in($txt_order_id)");
				 } 
				 if($file_po_id!='' || $file_po_id!=0)
				 {
					$condition->po_id("in($file_po_id)");
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
				 //echo $yarn->getQuery();die;
				//print_r($yarn_fabric_cost_data_arr);die;
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$conv_data_qty_arr=$conversion->getQtyArray_by_conversionid();
				$conv_data_amount_arr=$conversion->getAmountArray_by_conversionid();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$conversion_costing_arr=$conversion->getAmountArray_by_order();
				$conversion_process_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
				$trim_arr_qty=$trim->getQtyArray_by_precostdtlsid();
				$trim_arr_amount=$trim->getAmountArray_precostdtlsid();
				$trims_costing_arr=$trim->getAmountArray_by_order();
				$trim= new trims($condition);
				$trims_item_qty_arr=$trim->getQtyArray_by_itemidAndDescription();
				$trim= new trims($condition);
				$trims_item_amount_arr=$trim->getAmountArray_by_itemidAndDescription();

				$emblishment_qty_arr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
				$emblishment_amount_arr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
				
				$emblishment_job_amount_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtypeColor();
				$emblishment_job_qty_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtypeColor();
				
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
				$emblishment_qty_name_type_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtype();
				$emblishment_amount_name_type_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtype();
				$wash_qty_arr=$wash->getQtyArray_by_jobAndEmblishmentid();
				$wash_amount_arr=$wash->getAmountArray_by_jobAndEmblishmentid();
				$wash_type_name_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtype();
				$wash_type_name_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
			
				$wash_job_type_name_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtypeColor();
				$wash_job_type_name_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtypeColor();
				
				
				$wash_costing_arr=$wash->getAmountArray_by_order();
				$commercial_amount_arr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
			//	$commercial_amount_arr=$commercial->getAmountArray_by_orderAndPrecostdtlsid();
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$commercial_item_amount_arr=$commercial->getAmountArray_by_jobAndItemid();
				
				$commission_amount_arr=$commission->getAmountArray_by_jobAndPrecostdtlsid();
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commission_costing_sum_arr=$commission->getAmountArray_by_order();
				$commission_costing_item_arr=$commission->getAmountArray_by_jobAndItemid();

				$total_job_unit_price=($total_fob_value/$total_order_qty);

				 if($revised_no>0){
					$sql_fab="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, c.job_no,c.item_number_id, c.body_part_id as body_id, c.fab_nature_id as nat_id, c.color_type_id as color_type, c.fabric_description as fab_desc, c.avg_cons,c.uom, c.fabric_source as fab_source, c.rate, c.amount, c.avg_finish_cons, c.status_active from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order  by b.id,c.fab_nature_id, c.fabric_description,c.uom";
				 }else{
				   $sql_fab="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, c.job_no,c.item_number_id, c.body_part_id as body_id, c.fab_nature_id as nat_id, c.color_type_id as color_type, c.fabric_description as fab_desc, c.avg_cons,c.uom, c.fabric_source as fab_source, c.rate, c.amount, c.avg_finish_cons, c.status_active from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order  by b.id,c.fab_nature_id, c.fabric_description,c.uom";

				 }

				 	

				  $sql_fabs_result=sql_select($sql_fab);
				  $fabric_detail_arr=array();  $fabric_job_check_arr=array();
				$total_purchase_amt=0;
				foreach($sql_fabs_result as $row)
				{
					$row[csf("fab_source")]=$row[csf("fab_source")];
					$set_ratio=$row[csf("ratio")];
					$item_desc= $body_part[$row[csf("body_id")]].",".$color_type[$row[csf("color_type")]].",".$row[csf("fab_desc")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['amount']=$row[csf("amount")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['rate']=$row[csf("rate")];
					//$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]][$row[csf("uom")]]['rate']=$row[csf("rate")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['pre_fab_id'].=$row[csf("id")].',';

					if($row[csf("fab_source")]==2)
					{
					/*	$group_job_value=$job_no;
						if (!in_array($group_job_value,$fabric_job_check_arr) )
						{
							$total_purchase_amt+=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
							$fabric_job_check_arr[]=$group_job_value;
						}*/
						$total_purchase_amt+=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
					}
					//$fabric_amt=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
					//echo $fabric_amt.',';
				}
				if(empty($set_ratio))
				{
					$sql_ratio=sql_select( "SELECT a.total_set_qnty as ratio from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by a.total_set_qnty");
					if(count($sql_ratio))
					{
						$set_ratio=$sql_ratio[0][csf('ratio')];
					}
				}
				unset($sql_fabs_result);
				 // print($fabric_btb_amt);
								//print_r($fabric_detail_arr);die;
								//echo $total_fob_value.'/'.$total_order_qty;
						$styleRef=explode(",",$txt_style_ref);
						$all_style_job="";
						foreach($styleRef as $sid)
						{
								if($all_style_job=="") $all_style_job="'".$sid."'"; else $all_style_job.=","."'".$sid."'";
						}
						$fabric_rowspan_arr=array();$uom_rowspan_arr=array();
						foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
						{
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

									$fabric_rowspan_arr[$fab_nat_key]=$fabrice_rowspan;
									$uom_rowspan_arr[$fab_nat_key][$uom_key]=$uom_rowspan;
								}
							}
						}


							$style1="#E9F3FF";
							$style="#FFFFFF";

				$sql_yarn="select c.id as id,c.fabric_cost_dtls_id,c.count_id, c.copm_one_id, c.percent_one,c.color,c.type_id,min(c.cons_ratio) as cons_ratio,sum(c.cons_qnty) as cons_qnty,sum(c.amount) as amount,c.rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_yarn_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.count_id,c.fabric_cost_dtls_id, c.copm_one_id, c.percent_one,  c.color,c.type_id, c.rate order  by c.count_id, c.copm_one_id,c.percent_one";

					$result_yarn=sql_select($sql_yarn);
					$yarn_detail_arr=array();
					$yarnamount=$total_yarn_costing=0;
					foreach($result_yarn as $row)
					{
						$item_descrition = $lib_yarn_count[$row[csf("count_id")]].",".$composition[$row[csf("copm_one_id")]].",".$row[csf("percent_one")]."%,".$color_library[$row[csf("color")]].",".$yarn_type[$row[csf("type_id")]];
						//echo $item_descrition.'<br>';
						//echo $yarn_fabric_cost_data_arr[$row[csf("fabric_cost_dtls_id")]].', ';
						$total_yarn_costing+=$yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];
						$row_span+=1;
						$yarn_detail_arr[$item_descrition]['rate']=$row[csf("rate")];
						$yarn_detail_arr[$item_descrition]['count_id']=$row[csf("count_id")];
						$yarn_detail_arr[$item_descrition]['copm_one_id']=$row[csf("copm_one_id")];
						$yarn_detail_arr[$item_descrition]['percent_one']=$row[csf("percent_one")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarn_detail_arr[$item_descrition]['type_id']=$row[csf("type_id")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarnamount=$yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];//$yarn_data_arr[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
						$yarncons_qntys=$yarn_fabric_cost_data_arr[$row[csf("id")]]['qty'];//$yarn_data_arr[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						//echo $yarnamount.'<br/>';
						$yarn_detail_arr[$item_descrition]['yarn_cost']+=$yarnamount;
						$yarn_detail_arr[$item_descrition]['yarn_qty']+=$yarncons_qntys;

						$totalyarn_detail_arr[100]['amount']+=$yarnamount;
					}
					unset($result_yarn);

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
						$tot_conversion_aop_costing=$tot_conversion_yarn_dyeing_costing=0;
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
								$tot_conversion_aop_costing+=array_sum($conversion_process_costing_arr[$pid][35]);
								$tot_conversion_yarn_dyeing_costing+=array_sum($conversion_process_costing_arr[$pid][30]);
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
					//	echo $total_comercial_amt.'DDDDDDDDDDDD'.$reporttype;
						$total_raw_metarial_cost= $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt;
						$total_all_cost= $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt+$total_embl_amt+$total_comercial_amt+$total_commisssion+$total_wash_costing+$total_cm_cost+$total_lab_test_cost+$total_inspection_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost+$total_freight_cost;
						 // echo number_format($total_commisssion,2);
						if($reporttype==7)
						{
						 $samCha_cost=$total_fob_value*.0025;
						 $tot_aop_trim_yd_cost=$tot_conversion_aop_costing+$total_trims_amt+$tot_conversion_yarn_dyeing_costing;
						 $total_aop_trim_yd_cost=($tot_aop_trim_yd_cost*10)/100;
						 $total_all_cost+=$total_aop_trim_yd_cost+$samCha_cost;
						}


					 $sql_commi="select c.id, c.job_no, c.particulars_id,c.commission_base_id,avg(c.commision_rate) as rate, sum(c.commission_amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_commiss_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.commission_base_id>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id, c.job_no, c.particulars_id,c.commission_base_id order by c.id";

					$result_commi=sql_select($sql_commi);
					$commi_detail_arr=array();$tot_commission_rate=0;
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
					}
					unset($result_commi);
					//LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as  po_id,
					 $sql_comm_chk="select 	c.id, c.job_no, c.item_id,avg(c.rate) as rate,sum(c.rate) as tot_rate, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_comarci_cost_dtls c  where a.id=b.job_id 
		and c.job_id=b.job_id and c.job_id=a.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.rate>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id, c.job_no, c.item_id  order by c.id";
					$result_comm_chk=sql_select($sql_comm_chk);
					foreach($result_comm_chk as $row)
					{
						$commer_id_arr[$row[csf("id")]]=$row[csf("id")];
					}
					unset($result_comm_chk);
					$commer_cond=where_con_using_array($commer_id_arr,0,'c.id');

					  $sql_comm="select 	c.id, c.job_no, c.item_id,avg(c.rate) as rate,sum(c.rate) as tot_rate, sum(c.amount) as amount from wo_po_details_master a, wo_pre_cost_comarci_cost_dtls c  where c.job_id=a.id  and a.status_active=1 and a.is_deleted=0 and c.rate>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $buyer_id_cond $season_cond $commer_cond group by c.id, c.job_no, c.item_id  order by c.id";


					

					$result_comm=sql_select($sql_comm);
					$comm_detail_arr=array();$tot_comm_rate=0;$tot_comm_req_amt=0;
					foreach($result_comm as $row)
					{
						//$po_idArr =array_unique(explode(",",$row[csf("po_id")]));
						$item_descrition =$row[csf("description")];
						$comm_rowspan+=1;
						$comm_detail_arr[$row[csf("item_id")]]['item_id']=$row[csf("item_id")];
						$comm_detail_arr[$row[csf("item_id")]]['id']=$row[csf("id")];
						$comm_detail_arr[$row[csf("item_id")]]['amount']=$row[csf("amount")];
						$comm_detail_arr[$row[csf("item_id")]]['rate']=$row[csf("rate")];
						$comm_detail_arr[$row[csf("item_id")]]['job_no'].=$row[csf("job_no")].',';
						$comm_detail_arr[$row[csf("item_id")]]['desc']=$item_descrition;
						$comm_detail_arr[$row[csf("item_id")]]['req_amount']+=$commercial_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						//$emblishment_qty_arr
						//$commamount=0;
						
						$commamount+=$commercial_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						
						$totalcomm_detail_arr[100]['amount']+=$commamount; 
						$tot_comm_rate+=$row[csf("rate")];
						$tot_comm_req_amt+=$commercial_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
					}
					//echo $tot_comm_req_amt.'DD';
					$tot_commercial_cost_amount=$total_comercial_amt=0;
					if($commercial_cost_method==1)
					{
						 $tot_commercial_cost_amount=$total_yarn_costing+$total_trims_amt+$total_purchase_amt;
						 $total_comercial_amt=($tot_commercial_cost_amount*$tot_comm_rate)/100;
					}
					else if($commercial_cost_method==2)// On Selling
					{
						// $commercial_cost_percent_amount=$total_yarn_costing+$total_trims_amt+$total_purchase_amt;
						//($commercial_cost_percent_amount*$tot_comm_rate)/100;
						//echo $total_job_unit_price.'='.$commercial_cost_percent;
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
       <!-- <div class="footer_signature" >
         <?
          //echo signature_table(109, $cbo_company_name, "850px");
		 ?>
      	</div>-->

             <table width="800px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="8" align="center">
                    
                    <strong style=" font-size:18px"><? echo $report_title;?></strong></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" class="form_caption">
                    <strong><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong>
                    <b  style="color:#FF0000; float:right; font-size:large;"><? echo $approved_msg;?> </b>
                    </td>
                    
                </tr>
            </table>
             <table width="850" style="margin-left:10px;" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
                <tr>
                 <th  colspan="2" align="center" style="font-size:16px"> <strong>Summary</strong></th>
               </tr>
             <tr>
             <td style="border:none">
            	<table width="600"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr  bgcolor="<? echo $style; ?>">
                        <td width="120"> <strong>Buyer</strong> </td>
                        <td width=""><? if($cbo_buyer_name==0) echo implode(",",array_unique(explode(",",$all_buyer)));else echo $buyer_arr[$cbo_buyer_name];?> </td>
                        <td width="140" ><strong>Sew. SMV(Avg).</strong></td>
                        <td width="" title="SMV*JOb Wise Qty/Total PO Qty(Pcs)[<? echo $order_qty_pcs;?>]">&nbsp; <?
							$tot_avg_sew_smv=$tot_poQty=0;
							foreach($po_qty_by_job as $jobno=>$poQty)
							{
								$smv_avg=$smv_avg_by_job[$jobno];
								//echo $po_qty_by_job[$jobno].'='.$jobno;
								//echo $poQty.',';
								$tot_avg_sew_smv+=($poQty*$smv_avg)/$order_qty_pcs;
								$tot_poQty+=$poQty;
							}
							// echo number_format($tot_avg_sew_smv,2);
							//echo number_format($smv_avg,2).'='.$tot_poQty;
							echo number_format($smv_avg,2);
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
                        <td width=""><p><? echo implode(",",array_unique(explode(",",$all_style)));?></p></td>
                        <td width="140"> <strong>Style Desc.</strong> </td>
                        <td width=""><p><? echo implode(",",array_unique(explode(",",$all_style_desc)));?></p></td>
                    </tr>
                     <td>
                     <tr  bgcolor="<? echo $style1; ?>">
                        <td width="140"><strong>Avg FOB/UNIT Price[$]</strong></td>
                        <td width=""><? echo number_format($total_job_unit_price,2); ?></td>
                        <td width="140"><strong> Cost Per Minute(TK)</strong> </td>
                        <td width=""><? echo $cost_per_minute;?></td>
                    </tr>
                    <tr  bgcolor="<? echo $style; ?>">
                        <td width="100"><strong>Total Qty.(Pcs)</strong></td>
                        <td><? echo $order_qty_pcs;?></td>
                        <td width="140"><b>Total FOB[$]:</b></td>
                         <td  align="left">  <? echo number_format($total_fob_value,2);?></td>

                    </tr>
                     <tr  bgcolor="<? echo $style; ?>">
                        <td width="100"><strong>Comission [$] :</strong></td>
                        <td><? echo  number_format($total_commisssion,2);?></td>
                         <td width="140"> <b>CM Cost/Dzn(Avg)[$] : </b></td>
                          <td title="Total CM/Total Po qty(<? echo $total_order_qty;?>))*12">  <?
						  echo number_format((($total_cm_cost/$total_order_qty*12)*$set_ratio),2);?>
                         </td>
                    </tr>
                       <tr bgcolor="<? echo $style1?>"  align="left">
                           <td width="100" title=""><b>Total CM Cost[$] :</b></td>
                           <td width="" id="gross_cm_total"> <? echo number_format($total_cm_cost,2);?> </td>
                          <td width="140"  title="Fabric+Yarn+Conversion+Trims Cost"><b>Total Raw Material Cost[$]:</b></td>
                         <td id="td_sum_raw_material_cost">  <?  echo number_format($total_raw_metarial_cost,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style ?>" align="left">
                         <td width="100"><b>Total Cost[$] :</b></td>
                         <td title="Trims+Emblish+Fabric+Conversion+Lab Test+Commercial+Commission">  <?
						 echo number_format($total_all_cost,2);?></td>
                         <td width="140"><b>Total Margin[$] :</b></td>
                         <td title="Total Fob-Total Cost">  <?  $total_margin=$total_fob_value-$total_all_cost;
						 echo number_format($total_fob_value-$total_all_cost,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style1 ?>" align="left">
                         <td width="100"  title="Total Margin/PO Qty Pcs*12"><b>Margin/Dzn :</b></td>
                         <td  >  <?  echo number_format(($total_margin/$order_qty_pcs)*12,2);?></td>
                         <td <? if($reporttype==7){?> title="CM Cost/Dzn(Avg)[$]/(Sew. SMV(Avg)*12*Ratio)" <?}?>>
                         	<b>
                         		<?
	                         	if($reporttype==7)
	                         	{
	                         		echo 'EPM';
	                         	}
	                         	?>
                         	</b>
                         	
                         </td>
                         <td title="CM Cost AVG=<? echo $total_cm_cost.'/'.$total_order_qty.'*12*Ratio='.$set_ratio.' :: AVG SEW SMV='.$tot_avg_sew_smv;?>">
                         	<?
                         	if($reporttype==7)
                         	{
                         		$cm_cost_d_avg=(($total_cm_cost/$total_order_qty*12)*$set_ratio);
                         		echo fn_number_format($cm_cost_d_avg/($tot_avg_sew_smv*12),3);
                         	}
                         	?>
                         </td>
                    </tr>
					<tr bgcolor="<? echo $style ?>" align="left">
                         <td width="100"><b>Remarks :</b></td>
                         <td colspan="3"><? echo $remarks;?></td>                         
                    </tr>
                </table>
             </td>
             <td   width="250" height="50px" valign="middle">
                   <table width="100%"   cellpadding="0" class="rpt_table"  rules="all" cellspacing="0" border="1">
                       <tr>
                       	<td colspan="2" align="center">  <strong> Material Value For BTB</strong> </td>
                       </tr>
                        <tr>
                        	<td align="center"> <strong>Item</strong></td>
                            <td  align="center"> <strong>Value[$]</strong></td>
                        </tr>
                        <tr>
                        	<td> <strong>Yarn</strong> </td>
                            <td  align="right"><? echo number_format($total_yarn_costing,2);?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Trim </strong></td>
                            <td  align="right"><? echo number_format($total_trims_amt,2);?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Fabric(Purchase)</strong> </td>
                            <td  align="right"><? echo number_format($total_purchase_amt,2);?> </td>
                        </tr>

                         <tr bgcolor="#CCCCCC">
                        	<td> <strong>Total</strong> </td>
                            <td  align="right"><? echo number_format($total_yarn_costing+$total_trims_amt+$total_purchase_amt,2);?></td>
                        </tr>
                         <tr>
                            <td><strong> Machine/Line</strong></td>
                            <td align="center"><? echo $machine_line;?></td>
                        </tr>
                         <tr>
                            <td> <strong>Prod/Line/Hr</strong></td>
                            <td  align="center"><? echo $prod_line_hr;?></td>
                        </tr>
                      </table>
             </td>
                </tr>
            </table>
            <br/>
            <table width="600" style="margin-left:10px; font-size:16px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th> SL </th>
                <th>Particulars </th>
                <th>Cost[$] </th>
                <th>Amount[$] </th>
                <th>% </th>
            </thead>
            <tbody>

            <tr  bgcolor="<? echo $style1; ?>">
              <td>1  </td>
              <td><strong>Total FOB[$]: </strong> </td>
              <td  align="right">  <? // echo number_format($total_fob_value,2);?></td>
              <td  align="right">  <? echo number_format($total_fob_value,2);?></td>
              <td  align="right">  <? echo '100';?></td>
            </tr>
             <tr  bgcolor="<? echo $style; ?>">
              <td>2  </td>
              <td><strong>Fabric Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_fabric_amt,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_fabric_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >3  </td>
              <td><strong>Yarn Cost: </strong> </td>
              <td  align="right">   <? echo number_format($total_yarn_costing,2);?></td>
              <td  align="right"> </td>
              <td  align="right">  <? echo number_format(($total_yarn_costing/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >4  </td>
              <td><strong>Conversion Cost to Fabric: </strong> </td>
              <td  align="right">  <? echo number_format($total_conversion_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_conversion_cost/$total_fob_value)*100,2);?></td>
            </tr>
            <tr bgcolor="<? echo $style1; ?>">
              <td >5  </td>
              <td><strong>Trims Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_trims_amt,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_trims_amt/$total_fob_value)*100,2);?></td>
            </tr>
            <tr bgcolor="<? echo $style; ?>">
              <td >6  </td>
              <td><strong>Emblishment Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_embl_amt+$total_wash_costing,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format((($total_embl_amt+$total_wash_costing)/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >7  </td>
              <td><strong>Commercial Cost: </strong> </td>
              <td  align="right">  <? echo number_format($commamount,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($commamount/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >8  </td>
              <td><strong>Commission Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_commisssion,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_commisssion/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >9  </td>
              <td><strong>Lab Test Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_lab_test_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_lab_test_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >10  </td>
              <td><strong>Inspection Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_inspection_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_inspection_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >11  </td>
              <td><strong>CM Cost - IE: </strong> </td>
              <td  align="right">  <? echo number_format($total_cm_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_cm_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >12  </td>
              <td><strong>Freight Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_freight_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_freight_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >13  </td>
              <td><strong>Currier Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_currier_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_currier_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >14  </td>
              <td><strong>Certificate Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_certificate_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_certificate_cost/$total_fob_value)*100,2);?></td>
            </tr>
			<tr bgcolor="<? echo $style; ?>">
              <td >15  </td>
              <td><strong>Sample and Charity Cost : </strong> </td>
              <td  align="right">  <? $samCha_cost=$total_fob_value*.0025; echo number_format($samCha_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo .25;?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >16  </td>
              <td><strong>Office OH: </strong> </td>
              <td  align="right">  <? echo number_format($total_common_oh_cost,2);?></td>
              <td  align="right">&nbsp;  </td>
              <td  align="right">  <? echo number_format(($total_common_oh_cost/$total_fob_value)*100,2);?></td>
            </tr>
            <?
			if($reporttype==7)
			{
			//$tot_aop_trim_yd_cost=$tot_conversion_aop_costing+$total_trims_amt+$tot_conversion_yarn_dyeing_costing;
			//$total_aop_trim_yd_cost=($tot_aop_trim_yd_cost*10)/100;
			?>
			  <tr bgcolor="<? echo $style; ?>">
              <td >17  </td>
              <td><strong>Trims +AOP+Y/D (10%) </strong> </td>
              <td  align="right" title="10% on Total Trims+AOP+Y/D(<? echo $tot_aop_trim_yd_cost;?>)">  <? echo number_format($total_aop_trim_yd_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right"> <? echo number_format(($total_aop_trim_yd_cost/$total_fob_value)*100,2);?></td>
            </tr>
			<?
			//$total_all_cost+=$total_aop_trim_yd_cost;
			}
			?>

             <tr bgcolor="<? echo $style; ?>">
              <td >18  </td>
              <td><strong>Total Cost:</strong> </td>
              <td  align="right">&nbsp;  </td>
              <td  align="right">  <? echo number_format($total_all_cost,2);?></td>
              <td  align="right">  <? $tot_cost_percent=($total_all_cost/$total_fob_value)*100;echo number_format($tot_cost_percent,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >19  </td>
              <td><strong>Total Margin: </strong> </td>
              <td  align="right">&nbsp;  </td>
              <td  align="right" title="Total FOB Value-Total Cost"> <? $tot_margin=$total_fob_value-$total_all_cost; echo number_format($tot_margin,2);?> </td>
              <td  align="right">  <? $tot_margin_percent=($tot_margin/$total_fob_value)*100;echo number_format($tot_margin_percent,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >20  </td>
              <td><strong>Total(%): </strong> </td>
              <td  align="right" colspan="2">  <? //echo number_format($total_embl_amt,2);?></td>
              <td  align="right">  <? echo number_format(100-($tot_cost_percent+$tot_margin_percent),2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >21  </td>
              <td><strong>Margin/ DZN: </strong> </td>
              <td  align="right"  title="Total Margin/PoQty Pcs*12">  <? echo number_format(($tot_margin/$order_qty_pcs)*12,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >22  </td>
              <td><strong>Price / Set: </strong> </td>
              <td  align="right" title="Total FOB Value/Po Qty">
			  <? $price_pcs_or_set=$total_fob_value/$order_qty_pcs;
			  	$cost_pcs_set=$total_all_cost/$order_qty_pcs;
			  	echo number_format($price_pcs_or_set,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >23  </td>
              <td><strong>Cost /Set: </strong> </td>
              <td  align="right" title="Total Cost/Po Qty">  <? echo number_format($cost_pcs_set,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>


            </tbody>
            </table>
           <br/>

	            <?
				$job_id_cond=where_con_using_array($job_idArr,0,'a.job_id');
			 	$lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

		 		//$data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation  from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=7 order by b.id asc");
				$approv_data_array=sql_select(" select a.job_no,b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_pre_cost_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on a.id =d.booking_id  where   b.entry_form=15 $job_id_cond order by b.id asc");
			  //echo " select a.job_no,b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_pre_cost_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on a.id =d.booking_id  where   b.entry_form=15 $job_id_cond order by b.id asc";
			
			foreach($approv_data_array as $row)
			{
				$job_wise_approv[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$job_wise_approv[$row[csf('id')]]['approved_by']=$row[csf('approved_by')];
				$job_wise_approv[$row[csf('id')]]['approved_no']=$row[csf('approved_no')];
				$job_wise_approv[$row[csf('id')]]['approved_date']=$row[csf('approved_date')];
				$job_wise_approv[$row[csf('id')]]['un_approved_reason']=$row[csf('un_approved_reason')];
				$job_wise_approv[$row[csf('id')]]['user_full_name']=$row[csf('user_full_name')];
				$job_wise_approv[$row[csf('id')]]['designation']=$row[csf('designation')];
				$job_wise_approv[$row[csf('id')]]['approval_cause']=$row[csf('approval_cause')];
			}

	 	?>
	 	<table  width="650" style=" margin:5px;" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="25%" style="border:1px solid black;">Job no</th>
                <th width="25%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="15%" style="border:1px solid black;">Approval No</th>
                <th width="30%" style="border:1px solid black;">Un Approval Cause</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($job_wise_approv as $id=>$row){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
          <tr style="border:1px solid black;" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trapp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trapp_<? echo $i; ?>" align="center">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                 <td width="25%" style="border:1px solid black;"><? echo $row[('job_no')];?></td>
                <td width="25%" style="border:1px solid black;"><? echo $row[('user_full_name')]." / ". $lib_designation[$row[('designation')]];?></td>
                <td width="27%" style="border:1px solid black;"><? echo date("d-m-Y h:i:s",strtotime($row[('approved_date')]));?></td>
                <td width="15%" style="border:1px solid black;"><? echo $row[('approved_no')];?></td>
                <td width="30%" style="border:1px solid black;"><? echo $row[('approval_cause')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
 	 	<br>
             <?
                 echo signature_table(109, $cbo_company_name, "850px");
            ?>
           <div id="page_break_div">

            </div>
           <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">PO Details</b></caption>
					<thead>
                    	<th width="30">SL</th>
						<th width="80">Job</th>
						<th width="100">PO Number</th>
						<th width="100">PO Qty.</th>
						<th width="60">UOM</th>
                    	<th width="100">PO Qty.[Pcs]</th>
                        <th width="100">FOB/Pcs</th>
                        <th width="100"> FOB Value[$]</th>
						<th width="">CM Value</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner"  style="width:980px;margin-left:10px; max-height:400px;overflow-y:scroll;" align="left" id="scroll_body1">
					<table class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$i=1;$total_order_qty_pcss=0;$total_fob_val=$total_cm_value=0;$total_po_qty=0;
					foreach($sql_po_result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_qty_pcss=$row[csf('po_quantity')]*$row[csf('ratio')];
						$avg_unit=$row[csf('unit_price')];

					?>

						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80"  align="center"><? echo $row[csf('job_prefix')]; ?></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('po_number')]; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')],0); ?></div></td>
							<td width="60" align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($order_qty_pcss,0) ?></div></td>
                             <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($avg_unit,2); ?></div></td>

                            <td width="100" align="right"><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')]*$avg_unit,2); ?></div></td>
							<td width="" align="right"><div style="word-break:break-all"><? echo number_format($other_costing_arr[$row[csf('po_id')]]['cm_cost'],2); ?></div></td>


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
                            <th colspan="3"><strong>Total</strong> </th>
                            <th align="right"><strong><? echo number_format($total_po_qty,0);?> </strong></th>
                             <th align="right"><strong><? //echo number_format($total_up_charge_val,2);?> </strong></th>
                            <th align="right"><strong><? echo number_format($total_order_qty_pcss,0);?></strong> </th>
                            <th align="right"><strong><? //echo number_format($total_exfact_qty,0);?></strong> </th>
                             <th align="right"><strong><? echo number_format($total_fob_val,2);?></strong> </th>
							  <th align="right"><strong><? echo number_format($total_cm_value,2);?></strong> </th>
                            </tr>
                            </tfoot>

                    </table>
                    </div>
           <br/><br/>
           <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Fabric Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="100">Fab. Nature</th>
						<th width="200">Description</th>
						<th width="100">Source</th>
						<th width="100">Grey Qty</th>
						<th width="100">Fin. Qty </th>
                        <th width="50">UOM</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount[$]</th>
                         <th width=""> %</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:1000px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table"   width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$i=$m=1;$total_greycons=$total_fincons=$total_amount=$grand_total_greycons=$grand_total_fincons=$grand_total_amount=0;
					foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
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
                      	 if($y==1){
						?>
							<td width="30" rowspan="<? echo $uom_rowspan_arr[$fab_nat_key][$uom_key];?>"><? echo $m; ?></td>
							<td width="100" rowspan="<? echo $uom_rowspan_arr[$fab_nat_key][$uom_key];?>">
							<? echo $item_category[$fab_nat_key]; ?></td>
                             <?
							  }
							?>
							<td width="200" align="center"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="center" ><div style="word-break:break-all"><? echo $fabric_source[$source_key]; ?></div></td>
							<td width="100" title="" align="right"><div style="word-break:break-all"><? echo number_format($greycons,4); ?></div></td>
                            <td width="100" title="" align="right"><div style="word-break:break-all"><? echo number_format($fincons,4); ?></div></td>

                            <td width="50" align="center"><? echo $unit_of_measurement[$uom_key]; ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($rate,4); ?></div></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($amount,4); ?></div></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format(($amount/$total_fob_value)*100,4); ?></div></td>
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
							?>
                            <tr bgcolor="#F4F3C4">
                                <td>&nbsp; </td>
                                <td>&nbsp; </td>
                                <td>&nbsp;</td>
                                <td align="right"><strong>Sub Total</strong> </td>
                                <td align="right"><strong><? echo number_format($total_greycons,4);$total_greycons=0;?> </strong></td>
                                <td align="right"><strong><? echo number_format($total_fincons,4);$total_fincons=0;?> </strong></td>
                                <td align="right">&nbsp;</td>
                                <td>&nbsp; </td>
                                <td align="right"><strong><? echo number_format($total_amount,4);?></strong> </td>
                                <td align="right"><?  echo number_format(($total_amount/$total_fob_value)*100,4);$total_amount=0;?> </td>
                                </tr>
                            <?
							}
						}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="4" ><strong>Grand Total</strong> </th>
                            <th align="right"><strong><? echo number_format($grand_total_greycons,4);?> </strong></th>
                            <th align="right"><strong><? echo number_format($grand_total_fincons,4);?> </strong></th>
                            <th align="right">&nbsp;</th>
                            <th>&nbsp; </th>
                            <th align="right"><strong><? echo number_format($grand_total_amount,4);?></strong> </th>
                            <th align="right"><?  echo number_format(($grand_total_amount/$total_fob_value)*100,4);?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div><!--Fabtic Details End-->
            <br/><br/>
            <table id="table_header_1" style="margin-left:10px"  class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Yarn Details :</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Yarn Description</th>
						<th width="100">Yarn Qty.</th>
						<th width="100">Avg.Yarn Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount[$]</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:870px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?


					$i=$m=1;$grand_total_yarncons=$grand_total_yarnavgcons=$grand_total_amount=$grand_total_yarn_per=0;
					foreach($yarn_detail_arr as $desc_key=>$val)
					{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$yarn_cost=$val['yarn_cost'];
					$yarn_qty=$val['yarn_qty'];
					$yarncons_qnty=$yarn_qty;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['qty'];
					$yarnavgcons_qnty=$yarn_qty;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['qty'];
					$yarn_amount=$yarn_cost;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['amount'];
					$totalyarn_amount=$totalyarn_detail_arr[100]['amount'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('try_<? echo $i; ?>','<? echo $bgcolor;?>')" id="try_<? echo $i; ?>"> 					 <?
                      	 if($m==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $row_span;?>"><? echo 'Yarn Cost'; ?></td>
                             <?
							 }
							?>
							<td width="250"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo number_format($yarncons_qnty,4); ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($yarnavgcons_qnty,4); ?></div></td>                       <td width="100" align="right"><? echo number_format($val["rate"],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($yarn_amount,4); ?></div></td>
                             <?
							// $total_fob=$totalyarn_amount;
                      	//if($m==1){
						?>
                             <td width="" align="right" title="Yarn Amout/Total Fob*100" ><? echo number_format(($yarn_amount/$total_fob_value)*100,4); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								$grand_total_yarnavgcons+=$yarnavgcons_qnty;
								$grand_total_amount+=$yarn_amount;
								$grand_total_yarncons+=$yarncons_qnty;
								$grand_total_yarn_per+=($totalyarn_amount/$total_fob_value)*100;
								//$y++;
								$i++;
							$m++;
						}
							?>
                            <tfoot>
                            <tr>
                                <th><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? //echo number_format($grand_total_amount,4);?></strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_yarncons,4);?> </strong></th>
                                <th align="right"><strong><? echo number_format($grand_total_yarnavgcons,4);?> </strong></th>
                                <th align="right"><strong><? //echo number_format($grand_total_amount,4);?></strong> </th>
                                <th align="right"><? echo number_format($grand_total_amount,4);?></th>
                                <th align="center"><strong><? echo number_format(($totalyarn_amount/$total_fob_value)*100,4);?></strong> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Conversion Cost to Fabric :</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
						<th width="100">Required</th>
                        <th width="50">UOM</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount[$]</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:870px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
				   /*$sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond  order by c.cons_process";*/
				    $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,sum(c.req_qnty) as req_qnty,avg(c.charge_unit) as charge_unit,sum(c.amount) as amount,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.fabric_description,a.job_no, c.cons_process,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom  order by c.cons_process";

					$result_conv=sql_select($sql_conv);
					$conv_detail_arr=array();
					$totalconv_amount=0;
					foreach($result_conv as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("pre_costdtl_id")];
						$item_desc = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]];
						$row_span+=1;
						/*$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['id']=$row[csf("id")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;*/
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['id'].=$row[csf("id")].',';
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['desc']=$item_desc;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];

						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$convQty=$conv_data_qty_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;
						$conv_detail_arrData[$item_descrition][$row[csf("cons_process")]]['amt']+=$convamount;
						$conv_detail_arrData[$item_descrition][$row[csf("cons_process")]]['req_qty']+=$convQty;

						//$totalconv_amount+=$convamount;
					}
					//echo $totalconv_amount;
					//print_r($totalconv_detail_arr);
					$conv_rowspan_arr=array();

					foreach($conv_detail_arr as $desc_key=>$desc_data)
					{

							$conv_row_span=0;
							foreach($desc_data as $process_key=>$val)
							{
								$conv_row_span++;
							}
							$conv_rowspan_arr[$desc_key]=$conv_row_span;
					}
					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_conv_qty=$grand_total_conv_amount=$grand_total_amount=$total_conv_qty=$total_conv_amount=0;
					foreach($conv_detail_arr as $desc_key=>$desc_data)
					{
						$z=1;

						foreach($desc_data as $process_key=>$val)
						{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$conv_id=rtrim($val[('id')],',');
						$item_desc=$val[('desc')];
						$conv_ids=array_unique(explode(",",$conv_id));
						//$desc_key=$val[('desc')];
						/*$convsion_qty=$conversion_amt=0;
						foreach($conv_ids as $cid)
						{
							$convsion_qty+=$conv_data_qty_arr[$cid][$val[('uom')]];
							$conversion_amt+= $conv_data_amount_arr[$cid][$val[('uom')]];
						}*/
						$conversion_amt=$conv_detail_arrData[$desc_key][$process_key]['amt'];
						$convsion_qty=$conv_detail_arrData[$desc_key][$process_key]['req_qty'];

						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$process_key];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconv_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconv_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $conv_rowspan_arr[$desc_key];?>"><? echo 'Conversion Cost'; ?></td>
                             <?
							 }
							// $desc_keyArr=array_unique(explode(",",$desc_key));
							 
							?>
							<td width="250"><div style="word-break:break-all"><? echo $item_desc; ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($conversion_amt/$convsion_qty,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>
                             <?
                      //	if($z==1){
						?>
                             <td width="" valign="middle" align="center" title="Conv. Amout(<? echo $totalconv_amount?>)/Total Fob*100" rowspan="<? //echo $conv_rowspan_arr[$desc_key];?>"><? echo  number_format(($conversion_amt/$total_fob_value)*100,4);//number_format(($totalconv_amount/$total_fob_value)*100,2); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								$total_conv_qty+=$convsion_qty;
								$total_conv_amount+=$conversion_amt;
								$grand_total_conv_qty+=$convsion_qty;
								$grand_total_conv_amount+=$conversion_amt;

								$z++;
								$i++;

								}
								?>
                               <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                <td colspan="3" align="right"><strong>Sub Total</strong> </td>
                                <td align="right"><strong><? echo number_format($total_conv_qty,4);$total_conv_qty=0;?> </strong></td>
                                <td align="right"><strong>&nbsp; </strong></td>

                                <td align="right">&nbsp;</td>
                                <td align="right"><? $sub_tot_fab_conv_cost_per=($total_conv_amount/$total_fob_value)*100;echo number_format($total_conv_amount,4);$total_conv_amount=0;?></td>
                                <td align="right"><? echo number_format($sub_tot_fab_conv_cost_per,4);?></td>
                            </tr>
                                <?
							}

							?>

                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_conv_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_conv_amount/$total_fob_value)*100,2);?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                      <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="610" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Conversion Cost to Fabric Summary:</b></caption>
					<thead>
                    	<th width="100">Particulars</th>

						<th width="100">Process</th>
						<th width="100">Required</th>
                        <th width="50">UOM</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:630px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="610" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
				  $sql_conv_sum="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,sum(c.req_qnty) as req_qnty,avg(c.charge_unit) as charge_unit,sum(c.amount) as amount,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.fabric_description,a.job_no, c.cons_process,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom order by c.cons_process";

					$result_conv_sum=sql_select($sql_conv_sum);
					$conv_detail_arr=array();
					foreach($result_conv_sum as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
						//$row_span+=1;
						/*$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['id']=$row[csf("id")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;*/
						$sum_conv_detail_arr[$row[csf("cons_process")]]['id'].=$row[csf("id")].',';
						$sum_conv_detail_arr[$row[csf("cons_process")]]['uom']=$row[csf("uom")];
						//$sum_conv_detail_arr[$row[csf("cons_process")]]['charge_unit']=$row[csf("charge_unit")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['amount']=$row[csf("amount")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['cons_process']=$row[csf("cons_process")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$convQty=$conv_data_qty_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr2[100]['amount']+=$convamount;

						$sum_conv_detail_arr[$row[csf("cons_process")]]['amt']+=$convamount;
						$sum_conv_detail_arr[$row[csf("cons_process")]]['req_qty']+=$convQty;

						$sum_conv_rowspan_arr[$row[csf("cons_process")]]+=1;
					}
							$sconv_row_span=1;$row_span=0;
							foreach($sum_conv_detail_arr as $process_key=>$val)
							{
								$row_span+=$sconv_row_span;
								$sum_conv_detail_arr[$process_key]['charge_unit']=($val['amt']/$val['req_qty']);
							}
					//print_r($sum_conv_rowspan_arr);
						$i=$m=1;$sum_grand_total_conv_qty=$sum_grand_total_conv_amount=$grand_total_amount=$total_conv_qty=$total_conv_amount=0;
						foreach($sum_conv_detail_arr as $process_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$conv_id=rtrim($val[('id')],',');
							$conv_ids=array_unique(explode(",",$conv_id));
							$desc_key=$val[('desc')];/*$sum_convsion_qty=$sum_conversion_amt=0;
							foreach($conv_ids as $cid)
							{
								$sum_convsion_qty+=$conv_data_qty_arr[$cid][$val[('uom')]];
								$sum_conversion_amt+= $conv_data_amount_arr[$cid][$val[('uom')]];
							}*/
							$sum_convsion_qty=$val['req_qty'];
							$sum_conversion_amt=$val['amt'];

							$totalconv_amount_sum=$totalconv_detail_arr2[100]['amount'];
							$process_name=$conversion_cost_head_array[$process_key];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvs_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvs_<? echo $i; ?>"> 					 <?
                      	 if($m==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $row_span;?>"><? echo 'Conversion Cost'; ?></td>
                             <?
							 }
							?>

							<td width="100" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($sum_convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($val["charge_unit"],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($sum_conversion_amt,4); ?></div></td>
                             <?
                      //	if($m==1){
						?>
                             <td width="" valign="middle" align="center" title="Total Conv. Amout(<? echo $totalconv_amount_sum ?>)/Total Fob*100" rowspan="<? //echo $row_span;?>"><? echo number_format(($sum_conversion_amt/$total_fob_value)*100,4); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								//$total_conv_qty+=$sum_convsion_qty;
								//$total_conv_amount+=$sum_conversion_amt;
								$sum_grand_total_conv_qty+=$sum_convsion_qty;
								$sum_grand_total_conv_amount+=$sum_conversion_amt;

								$m++;
								$i++;


								?>

                                <?
							}

							?>

                            <tfoot>
                            <tr>
                                <th colspan="2"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($sum_grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($sum_grand_total_conv_amount,4);?></th>
                                <th align="right"><? echo number_format(($sum_grand_total_conv_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="890" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Trims Cost Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="110">Item Group</th>
						<th width="200">Description</th>
						<th width="130">Nominated Supp</th>
                        <th width="50">UOM</th>
                        <th width="100">Consumption</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:910px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="890" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$trim_group_arr=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name" );
				/*   $sql_trims="select c.id, c.job_no, c.trim_group,c.description,c.brand_sup_ref,c.cons_uom, c.cons_dzn_gmts, c.rate, c.amount, c.apvl_req, c.nominated_supp,c.status_active from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_trim_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  order by c.id";*/
				 // print_r($trims_item_amount_arr);
				 $sql_trims="select c.trim_group,c.description,c.cons_uom, c.nominated_supp from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_trim_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by  c.trim_group,c.description,c.cons_uom,c.nominated_supp  order by c.trim_group";
					$result_trims=sql_select($sql_trims);
					$trims_detail_arr=array();
					foreach($result_trims as $row)
					{
						$item_descrition =$row[csf("description")];
						$trims_rowspan+=1;
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['nominated_supp']=$row[csf("nominated_supp")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['uom']=$row[csf("cons_uom")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['trim_group']=$row[csf("trim_group")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['amount']=$row[csf("amount")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['rate']=$row[csf("rate")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['id'].=$row[csf("id")].',';
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['desc']=$item_descrition;
						//$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						//$trimsamount=$trims_item_amount_arr[$row[csf("trim_group")]][$item_descrition];//$trim_arr_amount[$row[csf("id")]];
						//$totaltrims_detail_arr[100]['amount']+=$trimsamount;
					}
					//echo $trims_rowspan;
					//print_r($totalconv_detail_arr);
					/*$trim_rowspan_arr=array();
					foreach($trims_detail_arr as $trims_key=>$trims_data)
					{
							$conv_row_span=0;
							foreach($trims_data as $desc_key=>$val)
							{
								$conv_row_span++;
							}
							$trim_rowspan_arr[$trims_key]=$conv_row_span;
					}*/
					//echo $conv_row_span;
					//print_r($conv_rowspan_arr);
					$i=$z=1;$grand_total_trim_amount=0;
					foreach($trims_detail_arr as $trims_key=>$trims_data)
					{
						foreach($trims_data as $desc_key=>$desc_data)
						{
							foreach($desc_data as $uom_key=>$trims_data)
							{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$trim_amount=$trims_item_amount_arr[$trims_key][$desc_key];
							$cons_dzn_gmts=$trims_item_qty_arr[$trims_key][$desc_key];
							//$trim_group=$val[('trim_group')];
							$nominated_supp=$val[('nominated_supp')];
						//	$totaltrims_amount=$totaltrims_detail_arr[100]['amount'];
							//$trims_rowspan=$trim_rowspan_arr[$trims_key];
							$avg_rate=$trim_amount/$cons_dzn_gmts;
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrim_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trtrim_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="110"><div style="word-break:break-all"><? echo $trim_group_arr[$trims_key]; ?></div></td>
							<td width="200" align="right"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="130" align="right" ><div style="word-break:break-all"><? echo $supplier_library[$nominated_supp]; ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$uom_key]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($cons_dzn_gmts,4); ?></td></td>
                            <td width="100" align="right"><? echo number_format($avg_rate,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($trim_amount,4); ?> </div></td>
                            <? //if($z==1) { ?>
                             <td width=""  align="center" title="Trims Amount/Total Fob Value*100">
							<? echo number_format(($trim_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_trim_amount+=$trim_amount;
								$i++;//$z++;
										}
									}
							}	?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? //echo number_format($grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_trim_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_trim_amount/$total_fob_value)*100,4); ?></th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
               <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Embellishment Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="120">Particulars</th>
						<th width="100">Type</th>
						<th width="100">Gmts. Qnty (Dzn)</th>
                        <th width="100">Color</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:720px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
			$color_library=return_library_array( "select id,color_name from  lib_color", "id", "color_name"  );
				  /* $sql_emblish="select c.id, c.job_no, c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate, c.amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order by c.id";*/
				     $sql_emblish="select b.id as po_id,c.id, c.job_no, c.emb_name,c.emb_type,d.color_number_id,e.requirment as cons_dzn_gmts,e.rate, e.amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c,wo_po_color_size_breakdown d,wo_pre_cos_emb_co_avg_con_dtls e  where a.id=b.job_id and c.job_id=b.job_id and c.job_id=a.id  and d.po_break_down_id=b.id and d.item_number_id= e.item_number_id and d.color_number_id=e.color_number_id and d.size_number_id=e.size_number_id and c.id=e.pre_cost_emb_cost_dtls_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order by c.id";

					$result_emblish=sql_select($sql_emblish);
					$emblish_detail_arr=array();
					foreach($result_emblish as $row)
					{
						$item_descrition =$row[csf("description")];
						$color_id =$row[csf("color_number_id")];
						$embData =$row[csf("emb_name")];
						$embl_rowspan+=1;
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['emb_name']=$row[csf("emb_name")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['emb_type']=$row[csf("emb_type")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['amount']+=$row[csf("amount")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['rate']=$row[csf("rate")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['job_no'].=$row[csf("job_no")].',';
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['po_id'].=$row[csf("po_id")].',';
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['desc']=$item_descrition;
						//$emblishment_qty_arr
						
						$embsamount=$emblishment_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						$totalemb_detail_arr[100]['amount']+=$embsamount;
					}
					//echo $embl_rowspan;
					//print_r($conv_rowspan_arr);
					
					//$emblishment_po_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidAndGmtscolor();
				//$emblishment_po_qty_arr
					foreach($emblish_detail_arr as $emb_name=>$enm_val)
					{
						$embl_row_span=0;
						foreach($enm_val as $emb_type=>$emb_typeData)
						{
							$embl_typerow_span=0;
							foreach($emb_typeData as $color_id=>$val)
							{
								$embl_row_span++;$embl_typerow_span++;
							}
							$emb_rowspan_arr[$emb_name]=$embl_row_span;
							$emb_rowspan_arr[$emb_name][$embl_typerow_span]=$embl_typerow_span;
						
						}
						
					}
					//print_r($emb_rowspan_arr);
				
					$i=$m=1;$grand_total_embl_amount=$grand_total_cons_dzn_gmts=0;
					foreach($emblish_detail_arr as $emb_name=>$enm_val)
					{
						$emb=0;
						foreach($enm_val as $emb_type=>$emb_typeData)
						{
						foreach($emb_typeData as $color_id=>$val)
						{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						$po_id=rtrim($val[('po_id')],',');
						$po_ids=array_unique(explode(",",$po_id));
						//$emb_name=$val[('emb_name')];$emb_type=$val[('emb_type')];
						 $totalembl_amount=$totalemb_detail_arr[100]['amount'];
						if($emb_name==1) $em_type = $emblishment_print_type[$emb_type];
						else if($emb_name==2) $em_type = $emblishment_embroy_type[$emb_type];
						else if($emb_name==3) $em_type = $emblishment_wash_type[$emb_type];
						else if($emb_name==4) $em_type = $emblishment_spwork_type[$emb_type];
						else if($emb_name==5) $em_type = $emblishment_gmts_type[$emb_type];
						else $em_type="";

             //getAmountArray_by_jobEmbnameAndEmbtypeColor
						$cons_dzn_gmts=0;$embl_amount=0;
						foreach($job_nos as $jno)
						{
							if($emb_name !=3){
								$wash_qty=$emblishment_job_qty_arr[$jno][$emb_name][$emb_type][$color_id];
								$wash_amt=$emblishment_job_amount_arr[$jno][$emb_name][$emb_type][$color_id];
								if($wash_amt) $wash_amt=$wash_amt;else $wash_amt=0;
								if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
								if(($wash_qty!="" || $wash_amt!=0) && ($wash_qty!="" || $wash_amt!=0))
								{
									$em_amount=$emblishment_job_amount_arr[$jno][$emb_name][$emb_type][$color_id];
									$cons_dzn=$emblishment_job_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									if($em_amount) $em_amount=$em_amount;else $em_amount=0;
									if($cons_dzn) $cons_dzn=$cons_dzn;else $cons_dzn=0;

									$cons_dzn_gmts+=$cons_dzn;
									$embl_amount+=$em_amount;
								}
							}
							else if($emb_name ==3){
								$wash_qty=$$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
								$wash_amt=$wash_job_type_name_amount_arr[$jno][$emb_name][$emb_type][$color_id];
								if($wash_amt) $wash_amt=$wash_amt;else $wash_amt=0;
								if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
								if(($wash_qty!="" || $wash_amt!=0) && ($wash_qty!="" || $wash_amt!=0))
								{
									$embl_amt=$wash_job_type_name_amount_arr[$jno][$emb_name][$emb_type][$color_id];
									$cons_dzn=$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									if($embl_amt) $embl_amt=$embl_amt;else $embl_amt=0;
									if($cons_dzn) $cons_dzn=$cons_dzn;else $cons_dzn=0;
									$cons_dzn_gmts+=$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									$embl_amount+=$embl_amt;
								}
							//echo 2;
							}
						}
						//$emb_rowspan=$emb_rowspan_arr[$emb_name];
						//wash_type_name_amount_arr
						//echo $embl_amount.',';
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tremb_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tremb_<? echo $i; ?>">
							<?
                            if($emb==0)
							{
							?>
                            <td width="30" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>">
							<? echo $i; ?></td>
                            <td width="120" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>"><div style="word-break:break-all"><? echo $emblishment_name_array[$emb_name];; ?></div></td>
							<td width="100" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>" align="center"><div style="word-break:break-all"><? echo $em_type; ?></div></td>
                            <?
							}
							?>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($cons_dzn_gmts,4); ?></div>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $color_library[$color_id]; ?></div></td>

                            <td width="100" align="right"><? echo number_format($embl_amount/$cons_dzn_gmts,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($embl_amount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $embl_rowspan;?>" valign="middle" align="center" title="Total Embl Amout/Total Fob*100">
							<? echo number_format(($embl_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_embl_amount+=$embl_amount;
								$grand_total_cons_dzn_gmts+=$cons_dzn_gmts;
								$i++;$emb++;
								}
							}
						  }
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>

                                <th align="right"><strong><? echo number_format($grand_total_cons_dzn_gmts,4);?></strong></th>

                                <th align="right">&nbsp;</th>
                                  <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_embl_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_embl_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
              <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Commercial Cost:</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="200">Particulars</th>

                        <th width="100">Rate In %</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:490px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?

					$i=$m=1;$grand_total_comm_amount=0;
					foreach($comm_detail_arr as $item_id=>$val)
					{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						$pre_dt_id=$val['id'];

						 $total_comm_amount=$totalcomm_detail_arr[100]['amount'];
						// $comm_detail_arr[$row[csf("item_id")]]['req_amount'];
						 $commamount=$val[('req_amount')];
						 
						 //$commercial_amount_arr[$row[csf("job_no")]][$row[csf("id")]]
						//$comm_amount=$commercial_amount_arr[$val[('job_no')]][$comm_key];
					//	$commamount=0;
						foreach($job_nos as $jno)
						{
							//$commamount+=$commercial_amount_arr[$jno][$pre_dt_id];
								//echo $jno.'='.$commercial_amount_arr[$jno][$pre_dt_id].', ';
						}
					
						$comm_amount=(($val['rate']*$tot_commercial_cost_amount)/100);
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trcomm_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trcomm_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="200"><div style="word-break:break-all"><? echo $camarcial_items[$item_id];; ?></div></td>


                            <td width="100" align="right"><? echo number_format($val['rate'],4); ?></td>
                            <td width="100"  align="right" title="Commercial Cost Predefined Method"><div style="word-break:break-all">
							 <? echo number_format($commamount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $comm_rowspan;?>" valign="middle" align="center" title="Commercial Amount=(<? echo $comm_amount; ?>)/Total Fob*100">
							<? echo number_format(($commamount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_comm_amount+=$commamount;
								//$grand_total_cons_dzn_gmts+=$cons_dzn_gmts;
								$i++;$m++;
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>

                                <th align="right"><? echo number_format($grand_total_comm_amount,4);?></th>
                                <th align="center"><? echo number_format(($grand_total_comm_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>

              <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="570" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Commission Cost:</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="200">Particulars</th>
                        <th width="100">Commission Basis</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:590px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="570" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					// 	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no."";

					$i=$m=1;$grand_total_commi_amount=0;
					foreach($commi_detail_arr as $particulars_id=>$val)
					{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						//$particulars_id=$val[('particulars_id')];
						$commission_base_id=$val[('commission_base_id')];
						 $total_commi_amount=$totalcommi_detail_arr[100]['amount'];
						 $commi_amount=0;
						 foreach($job_nos as $jno)
						 {
							$commi_amount+=$commission_costing_item_arr[$jno][$particulars_id];//$commission_amount_arr[$job_no][$commi_key];
						 }
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trcommi_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trcommi_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="200"><div style="word-break:break-all"><? echo $commission_particulars[$particulars_id];; ?></div></td>
							<td width="100" align="center"><? echo $commission_base_array[$val['commission_base_id']]; ?></td>
                            <td width="100" align="right"><? echo number_format($val['rate'],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($commi_amount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $commi_rowspan;?>" valign="middle" align="center" title="Commission Amount/Total Fob*100">
							<? echo number_format(($commi_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_commi_amount+=$commi_amount;
								$i++;//$m++;
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><? echo number_format($grand_total_commi_amount,4);?></th>
                                <th align="center"><? echo number_format(($grand_total_commi_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                      <br/><br/>
                      <?
				  //start	Other Components part report here -------------------------------------------
			?>

        <div style="margin-left:10px">
            <table   class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
            <label><b>Others Components</b></label>
            <thead>
                    <th width="150">Particulars</th>
                    <th width="100">Amount($)</th>
                    <th width="50">%</th>
            </thead>
            <?
          		$style1="#E9F3FF";
				$style2="#FFFFFF";
				 $total_other_components = $total_lab_test_cost+$total_inspection_cost+$total_cm_cost+$total_freight_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost;
   			?>
                <tr bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 1; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 1; ?>">
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($total_lab_test_cost,4); ?></td>
                    <td align="right" title="Lab Cost/Total FOB*100"><? echo number_format(($total_lab_test_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 2; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 2; ?>">
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($total_inspection_cost,4); ?></td>
                    <td align="right" title="Inspection Cost/Total FOB*100"><? echo number_format(($total_inspection_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <tr bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 3; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 3; ?>">
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($total_cm_cost,4); ?></td>
                    <td align="right" title="CM Cost/Total FOB*100"><? echo number_format(($total_cm_cost/$total_fob_value)*100,4); ?></td>
                </tr bgcolor="><? echo $style2 ?>">
                <tr  bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 4; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 4; ?>">
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($total_freight_cost,4); ?></td>
                    <td align="right" title="Freight Cost/Total FOB*100"><? echo number_format(($total_freight_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                 <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 5; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 5; ?>">
                    <td align="left">Currier Cost </td>
                    <td align="right"><? echo number_format($total_currier_cost,4); ?></td>
                    <td align="right" title="Currier Cost/Total FOB*100"><? echo number_format(($total_currier_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                 <tr bgcolor="<? echo $style1; ?>" onClick="change_color('troh_<? echo 6; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 6; ?>">
                    <td align="left">Certificate Cost </td>
                    <td align="right"><? echo number_format($total_certificate_cost,4); ?></td>
                    <td align="right" title="Certificate Cost/Total FOB*100"><? echo number_format(($total_certificate_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 7; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 7; ?>">
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($total_common_oh_cost,4); ?></td>
                    <td align="right" title="Office OH Cost/Total FOB*100"><? echo number_format(($total_common_oh_cost/$total_fob_value)*100,4); ?></td>
                </tr>

                <tfoot>
                <tr>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_other_components,4); ?></th>
                    <th align="right" title="Total Other Components Cost/Total FOB*100"><? echo number_format(($total_other_components/$total_fob_value)*100,4); ?> </th>
                </tr>
                </tfoot>
            </table>
            </div>
             <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Fabric Dyeing Cost Details:</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
                        <th width="100">Fab. Color</th>
						<th width="100">Color Qty.</th>
                        <th width="50">UOM</th>
                        <th width="60">Rate</th>
                        <th width="">Total Value</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:890px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
					foreach($pre_cost as $row)
					{
						$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
					}
					$sql_color="select a.job_no, a.total_set_qnty as ratio, b.id as po_id,c.color_number_id,c.plan_cut_qnty as po_qty from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order  by b.id";
					$result_color=sql_select($sql_color);
					foreach($result_color as $row)
					{
						$costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per==1) $order_price_per_dzn=12;
						else if($costing_per==2) $order_price_per_dzn=1;
						else if($costing_per==3) $order_price_per_dzn=24;
						else if($costing_per==4) $order_price_per_dzn=36;
						else if($costing_per==5) $order_price_per_dzn=48;
					//echo $order_price_per_dzn.'ffd';
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['po_qty']+=$row[csf("po_qty")];
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['costing_per']=$order_price_per_dzn;
						
						$job_po_qty_arr[$row[csf("job_no")]]['po_qty']+=$row[csf("po_qty")];
						$job_po_qty_arr[$row[csf("job_no")]]['costing_per']=$order_price_per_dzn;
					}

				    $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0  and c.cons_process in(31) and c.status_active=1 and c.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order by c.color_break_down";

					$result_conv=sql_select($sql_conv);
					$conv_detail_arr=array();

					foreach($result_conv as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")]."***".$row[csf("job_no")];
						$color_break_down=explode("__",$row[csf("color_break_down")]);
						$cons_qty=0;
						foreach($color_break_down as $fcolor)
						{
							$color_down=explode("_",$fcolor);

							$gmt_color=$color_down[0];
							$unit_charge=$color_down[1];
							$fab_color=$color_down[3];
							$cons_qty=$color_down[4];
							//echo $cons_qty.'='.'<br>';
							$conv_detail_arr[$item_descrition][$fab_color]['job_no'].=$row[csf("job_no")].',';
							$conv_detail_arr[$item_descrition][$fab_color]['uom']=$row[csf("uom")];
							$conv_detail_arr[$item_descrition][$fab_color]['charge_unit']=$row[csf("charge_unit")];
							$conv_detail_arr[$item_descrition][$fab_color]['amount']=$row[csf("amount")];
							$conv_detail_arr[$item_descrition][$fab_color]['cons_process']=$row[csf("cons_process")];
							$conv_detail_arr[$item_descrition][$fab_color]['desc']=$item_descrition;
							$conv_detail_arr[$item_descrition][$fab_color]['gmt_color']=$gmt_color;
							$conv_detail_arr[$item_descrition][$fab_color]['unit_charge']=$unit_charge;
							$conv_detail_arr[$item_descrition][$fab_color]['cons_qty']=$cons_qty;
						}
					}

					//print_r($totalconv_detail_arr);
					$conv_rowspan_arr=array();
					foreach($conv_detail_arr as $fab_key=>$fab_data)
					{
						$conv_row_span=0;
						foreach($fab_data as $color_key=>$val)
						{
							$conv_row_span++;
						}
						$conv_rowspan_arr[$fab_key]=$conv_row_span;
					}

					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_dyeing_conv_qty=$grand_total_dyeing_conv_amount=$grand_total_amount=$total_dyeing_conv_qty=$total_dyeing_conv_amount=0;
					foreach($conv_detail_arr as $fab_key=>$fab_data)
					{
						$z=1;
						foreach($fab_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));

						$cons_qty=$val[('cons_qty')];
						$cons_process=$val[('cons_process')];
						$unit_charge=$val[('unit_charge')];
						$gmt_color=$val[('gmt_color')];

						$color_po_qty=$costing_per=0;
						foreach($job_nos as $jno)
						{

							$costing_per=$color_po_qty_arr[$jno][$gmt_color]['costing_per'];
							if($costing_per!='') $costing_per=$costing_per;else $costing_per=0;
							if($color_po_qty_arr[$jno][$gmt_color]['po_qty']!="" || $color_po_qty_arr[$jno][$gmt_color]['po_qty']!=0) {

							//echo $color_po_qty_arr[$jno][$gmt_color]['po_qty'].', ';
							$color_po_qty+=$color_po_qty_arr[$jno][$gmt_color]['po_qty']/$costing_per;
							}
						}
						$convsion_qty=$cons_qty*$color_po_qty;
						$conversion_amt= $convsion_qty*$unit_charge;

						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$cons_process];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvf_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvf_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
                      	 	$job_fab=explode("***", $fab_key);
						?>
							<td width="100" valign="middle" align="center" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>"><? echo $m; ?></td>
                            <td width="250" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>" title="<?=$job_fab[1]?>"><div style="word-break:break-all"><? echo $job_fab[0]; ?></div></td>
							<td width="100" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
                             <?
							 }
							?>

                            <td width="100" align="right" title="<? echo $cons_qty.'=='.$color_po_qty;?>"><div style="word-break:break-all"><? echo $color_library[$color_key]; ?></div></td>
							<td width="100" align="right" title="<? echo $color_po_qty;?>"><div style="word-break:break-all" ><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="60" align="right"><? echo number_format($unit_charge,4); ?></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>

                            </tr>
                            <?
								$total_dyeing_conv_qty+=$convsion_qty;
								$total_dyeing_conv_amount+=$conversion_amt;
								$grand_total_dyeing_conv_qty+=$convsion_qty;
								$grand_total_dyeing_conv_amount+=$conversion_amt;

								$z++;
								$i++;
									}
									$m++;
									?>
                             	 <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                    <td colspan="4" align="right"><strong>Sub Total</strong> </td>
                                    <td align="right"><strong><? echo number_format($total_dyeing_conv_qty,4);$total_dyeing_conv_qty=0;?> </strong></td>
                                    <td align="right"><strong>&nbsp; </strong></td>
                                    <td align="right">&nbsp;</td>
                                    <td align="right"><? echo number_format($total_dyeing_conv_amount,4);$total_dyeing_conv_amount=0;?></td>
                                	</tr>
                                <?
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_dyeing_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_dyeing_conv_amount,4);?></th>

                            </tr>
                            </tfoot>
                    </table>
                    </div>
                    
                 <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Knitting Cost Details:</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
						<th width="100">Req. Qty.</th>
                        <th width="50">UOM</th>
                        <th width="60">Rate</th>
                        <th width="">Total Value</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:890px; max-height:400px;overflow-y:scroll;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					/*$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
					foreach($pre_cost as $row)
					{
						$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
					}
					$sql_color="select a.job_no, a.total_set_qnty as ratio, b.id as po_id,c.color_number_id,c.plan_cut_qnty as po_qty from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order  by b.id";
					$result_color=sql_select($sql_color);
					foreach($result_color as $row)
					{
						$costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per==1) $order_price_per_dzn=12;
						else if($costing_per==2) $order_price_per_dzn=1;
						else if($costing_per==3) $order_price_per_dzn=24;
						else if($costing_per==4) $order_price_per_dzn=36;
						else if($costing_per==5) $order_price_per_dzn=48;
					//echo $order_price_per_dzn.'ffd';
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['po_qty']+=$row[csf("po_qty")];
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['costing_per']=$order_price_per_dzn;
					}*/

				    $sql_convKnit="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0  and c.cons_process in(1) $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order by c.color_break_down";

					$result_convKnit=sql_select($sql_convKnit);
					$conv_detail_arr=array();

					foreach($result_convKnit as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")]."***".$row[csf("job_no")];
						//$color_break_down=explode("__",$row[csf("color_break_down")]);
						 
						 
						$process_id=1;
							$knit_conv_detail_arr[$item_descrition][$process_id]['job_no'].=$row[csf("job_no")].',';
							$knit_conv_detail_arr[$item_descrition][$process_id]['uom']=$row[csf("uom")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['charge_unit']=$row[csf("charge_unit")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['amount']=$row[csf("amount")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['cons_process']=$row[csf("cons_process")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['desc']=$item_descrition;
							//$conv_detail_arr[$item_descrition][$process_id]['gmt_color']=$gmt_color;
							//$knit_conv_detail_arr[$item_descrition][$process_id]['unit_charge']=$unit_charge;
							$knit_conv_detail_arr[$item_descrition][$process_id]['unit_charge']=$row[csf("charge_unit")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['cons_qty']=$row[csf("req_qnty")];
					}

					//print_r($totalconv_detail_arr);
					$knitconv_rowspan_arr=array();
					foreach($knit_conv_detail_arr as $fab_key=>$fab_data)
					{
						$knitconv_row_span=0;
						foreach($fab_data as $color_key=>$val)
						{
							$knitconv_row_span++;
						}
						$knitconv_rowspan_arr[$fab_key]=$knitconv_row_span;
					}

					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_dyeing_conv_qty=$grand_total_dyeing_conv_amount=$grand_total_amount=$total_dyeing_conv_qty=$total_dyeing_conv_amount=0;
					foreach($knit_conv_detail_arr as $fab_key=>$fab_data)
					{
						$z=1;
						foreach($fab_data as $process_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));

						$cons_qty=$val[('cons_qty')];
						$cons_process=$val[('cons_process')];
						$unit_charge=$val[('unit_charge')];
						$gmt_color=$val[('gmt_color')];

						$color_po_qty=$costing_per=0;
						foreach($job_nos as $jno)
						{

							$costing_per=$job_po_qty_arr[$jno]['costing_per'];
							if($costing_per!='') $costing_per=$costing_per;else $costing_per=0;
							if($job_po_qty_arr[$jno]['po_qty']!="" || $job_po_qty_arr[$jno]['po_qty']!=0) {

							//echo $color_po_qty_arr[$jno][$gmt_color]['po_qty'].', ';
							$color_po_qty+=$job_po_qty_arr[$jno]['po_qty']/$costing_per;
							}
						}
						$convsion_qty=$cons_qty*$color_po_qty;
						$conversion_amt= $convsion_qty*$unit_charge;
						
						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$cons_process];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvk_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvk_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
                      	 	$job_fab=explode("***", $fab_key);
						?>
							<td width="100" valign="middle" align="center" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>"><? echo $m; ?></td>
                            <td width="250" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>" title="<?=$job_fab[1]?>"><div style="word-break:break-all"><? echo $job_fab[0]; ?></div></td>
							<td width="100" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
                             <?
							 }
							?>

                            
							<td width="100" align="right" title="<? echo $color_po_qty;?>"><div style="word-break:break-all" ><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="60" align="right"><? echo number_format($unit_charge,4); ?></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>

                            </tr>
                            <?
								$total_dyeing_conv_qty+=$convsion_qty;
								$total_dyeing_conv_amount+=$conversion_amt;
								$grand_total_dyeing_conv_qty+=$convsion_qty;
								$grand_total_dyeing_conv_amount+=$conversion_amt;

								$z++;
								$i++;
									}
									$m++;
									?>
                             	 <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                    <td colspan="3" align="right"><strong>Sub Total</strong> </td>
                                    <td align="right"><strong><? echo number_format($total_dyeing_conv_qty,4);$total_dyeing_conv_qty=0;?> </strong></td>
                                    <td align="right"><strong>&nbsp; </strong></td>
                                    <td align="right">&nbsp;</td>
                                    <td align="right"><? echo number_format($total_dyeing_conv_amount,4);$total_dyeing_conv_amount=0;?></td>
                                	</tr>
                                <?
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_dyeing_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_dyeing_conv_amount,4);?></th>

                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/>
             		<?
                		 echo signature_table(109, $cbo_company_name, "850px");
           			 ?>


        </div> <!--Main Div End-->
		<?
	 
	}
	if($reporttype==8) //Budget Button
	{    
		     if($version != ""){$table_name="wo_po_break_down_his "; $where_con=" and APPROVED_NO=$version"; }else {$table_name="wo_po_break_down "; $where_con="";}

				  $sql="select a.id as job_id,a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price, b.pub_shipment_date from wo_po_details_master a, $table_name b  where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $where_con $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order  by b.id";

				  //echo $sql;die;

				$sql_po_result=sql_select($sql);
				$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer="";
				$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;
				//echo $buyer_name;die;
				$job_idArr=array();
				foreach($sql_po_result as $row)
				{
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
					if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
					if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
					if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
					if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
					if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];

					/*$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$total_order_qty+=$row[csf('po_quantity')];
					$total_unit_price+=$row[csf('unit_price')];
					$total_fob_value+=$row[csf('po_total_price')];*/
					$po_qty_by_job[$row[csf("job_no")]]+=$row[csf('po_quantity')]*$row[csf('ratio')];
					$job_idArr[$row[csf("job_id")]]=$row[csf('job_id')];
				}
				$sql_po="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.unit_price,c.order_rate, b.pub_shipment_date,c.order_total,c.order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c   where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and c.po_break_down_id=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond    order  by b.id";

				$sql_po_color_result=sql_select($sql_po);
				foreach($sql_po_color_result as $row)
				{
					$order_qty_pcs+=$row[csf('order_quantity')];
					$total_order_qty+=$row[csf('order_quantity')];
					$total_unit_price+=$row[csf('order_rate')];
					$total_fob_value+=$row[csf('order_total')];
				}
				unset($sql_po_color_result);
				
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
				unset($sql_std_para);
				   $nameArray=sql_select( "select commercial_cost_method,id,commercial_cost_percent from  variable_order_tracking where company_name=$cbo_company_name and variable_list=27 order by id" );
				   $commercial_cost_method=$commercial_cost_percent=0;
				   foreach($nameArray as $row)
					{
						$commercial_cost_method=$row[csf('commercial_cost_method')];
						$commercial_cost_percent=$row[csf('commercial_cost_percent')];
					}
					//echo $commercial_cost_method.'=';
					unset($nameArray);

				$sql_pre="select a.job_no,a.approved,a.costing_date,a.machine_line as machine_line,a.job_no, a.prod_line_hr, a.sew_smv, a.sew_effi_percent as sew_effi_percent, a.budget_minute,b.cost_pcs_set,b.price_pcs_or_set,remarks from wo_pre_cost_mst a,wo_pre_cost_dtls b where  a.job_no=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in(".$all_full_job.")  order  by a.id";
				

				    $sql_pre_result=sql_select($sql_pre);
					$sew_smv='';$machine_line='';$prod_line_hr='';$prod_line_hr='';$sew_effi_percent='';$budget_minute=0;
					$approved_msg='';
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
							$remarks.=$row[csf("remarks")].'.';
							if($row[csf("approved")]==1) 
							{
								$approved_msg="This Job Is Approved.";
							}
							else if($row[csf("approved")]==3) 
							{
								$approved_msg="This Job Is Partial Approved";
							}
							//$price_pcs_or_set+=$row[csf('price_pcs_or_set')];
							//$cost_pcs_set+=$row[csf('cost_pcs_set')];
					}
					unset($sql_pre_result);
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
				 if($file_po_id!='' || $file_po_id!=0)
				 {
					$condition->po_id("in($file_po_id)");
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
				
				if($version != ""){
					$fabric = new fabric($condition,0,2);
					$yarn = new yarn($condition,0,2);
					$conversion = new conversion($condition,0,2);
					$trim = new trims($condition,0,2);
					$emblishment = new emblishment($condition,0,2);
					$wash = new wash($condition,0,2);
					$commercial = new commercial($condition,0,2);
					$commission = new commision($condition,0,2);
					$fabric = new fabric($condition,0,2);
					$other = new other($condition,0,2);
				}
				else{
					$fabric = new fabric($condition);
					$yarn = new yarn($condition);
					$conversion = new conversion($condition);
					$trim = new trims($condition);
					$emblishment = new emblishment($condition);
					$wash = new wash($condition);
					$commercial = new commercial($condition);
					$commission = new commision($condition);
					$fabric = new fabric($condition);
					$other = new other($condition);
				}

				 //echo $conversion->getQuery();die;

				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
				$fabric_qty_array=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
				$fabric_amount_array=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
				$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
				$yarn_data_arr=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
				//$yarn_fabric_cost_data_arr=$yarn->get_By_Precostfabricdtlsid_YarnAmountArray();
				$yarn_fabric_cost_data_arr=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();
				 //echo $yarn->getQuery();die;
			//	print_r($yarn_fabric_cost_data_arr);die;
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$conv_data_qty_arr=$conversion->getQtyArray_by_conversionid();
				$conv_data_amount_arr=$conversion->getAmountArray_by_conversionid();
				
				$other_costing_arr=$other->getAmountArray_by_order();
				$conversion_costing_arr=$conversion->getAmountArray_by_order();
				$conversion_process_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
				$trim_arr_qty=$trim->getQtyArray_by_precostdtlsid();
				$trim_arr_amount=$trim->getAmountArray_precostdtlsid();
				$trims_costing_arr=$trim->getAmountArray_by_order();
				//echo $trim->getQuery();die;
				//$trim= new trims($condition);
				$trims_item_qty_arr=$trim->getQtyArray_by_itemidAndDescription();
				//$trim= new trims($condition);
				$trims_item_amount_arr=$trim->getAmountArray_by_itemidAndDescription();

				$emblishment_qty_arr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
				$emblishment_amount_arr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
				
				$emblishment_job_amount_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtypeColor();
				$emblishment_job_qty_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtypeColor();
				
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
				$emblishment_qty_name_type_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtype();
				$emblishment_amount_name_type_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtype();
				$wash_qty_arr=$wash->getQtyArray_by_jobAndEmblishmentid();
				$wash_amount_arr=$wash->getAmountArray_by_jobAndEmblishmentid();
				$wash_type_name_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtype();
				$wash_type_name_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();
			
				$wash_job_type_name_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtypeColor();
				$wash_job_type_name_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtypeColor();
				
				
				$wash_costing_arr=$wash->getAmountArray_by_order();
				$commercial_amount_arr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$commercial_item_amount_arr=$commercial->getAmountArray_by_jobAndItemid();
				$commission_amount_arr=$commission->getAmountArray_by_jobAndPrecostdtlsid();
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commission_costing_sum_arr=$commission->getAmountArray_by_order();
				$commission_costing_item_arr=$commission->getAmountArray_by_jobAndItemid();

				$total_job_unit_price=($total_fob_value/$total_order_qty);

				 if($revised_no>0){
					$sql_fab="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, c.job_no,c.item_number_id, c.body_part_id as body_id, c.fab_nature_id as nat_id, c.color_type_id as color_type, c.fabric_description as fab_desc, c.avg_cons,c.uom, c.fabric_source as fab_source, c.rate, c.amount, c.avg_finish_cons, c.status_active,c.gsm_weight,d.dia_width,d.color_number_id from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no and c.job_no=d.job_no and c.id=d.pre_cost_fabric_cost_dtls_id and b.id=d.po_break_down_id and  d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond  order  by b.id,c.fab_nature_id, c.fabric_description,c.uom,d.dia_width";
				 }else{
				   $sql_fab="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, c.job_no,c.item_number_id, c.body_part_id as body_id, c.fab_nature_id as nat_id, c.color_type_id as color_type, c.fabric_description as fab_desc, c.avg_cons,c.uom, c.fabric_source as fab_source, c.rate, c.amount, c.avg_finish_cons, c.status_active,c.gsm_weight,d.dia_width,d.color_number_id from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d  where a.id=b.job_id and c.job_id=b.job_id and c.job_id=a.id and c.job_id=d.job_id and c.id=d.pre_cost_fabric_cost_dtls_id and b.id=d.po_break_down_id and d.status_active=1 and d.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond  order  by b.id,c.fab_nature_id, c.fabric_description,c.uom,d.dia_width";

				 }
				 //echo  $sql_fab;	

				  $sql_fabs_result=sql_select($sql_fab);
				  $fabric_detail_arr=array();  $fabric_job_check_arr=array();
				$total_purchase_amt=0;
				$fabIdArrayChk=array();
				foreach($sql_fabs_result as $row)
				{
					$set_ratio=$row[csf("ratio")];	
					$item_desc= $body_part[$row[csf("body_id")]].",".$color_type[$row[csf("color_type")]].",".$row[csf("fab_desc")];
					$fab_chk_str=$row[csf("po_id")].'_'.$row[csf("id")].'_'.$row[csf("color_number_id")].'_'.$row[csf("dia_width")].'_'.$row[csf("uom")];
					if($fabIdArrayChk[$fab_chk_str]=='')
					{
					$knit_fin_Qty=$fabric_qty_array['knit']['finish'][$row[csf("po_id")]][$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("dia_width")]][$row[csf("uom")]];
					$knit_grey_Qty=$fabric_qty_array['knit']['grey'][$row[csf("po_id")]][$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("dia_width")]][$row[csf("uom")]];
					$knit_grey_amount=$fabric_amount_array['knit']['grey'][$row[csf("po_id")]][$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("dia_width")]][$row[csf("uom")]];

					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['knit_fin_Qty']+=$knit_fin_Qty;
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['knit_grey_Qty']+=$knit_grey_Qty;
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['knit_grey_amount']+=$knit_grey_amount;


					$woven_fin_Qty=$fabric_qty_array['woven']['finish'][$row[csf("po_id")]][$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("dia_width")]][$row[csf("uom")]];
					$woven_grey_Qty=$fabric_qty_array['woven']['grey'][$row[csf("po_id")]][$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("dia_width")]][$row[csf("uom")]];
					$woven_grey_amount=$fabric_amount_array['woven']['grey'][$row[csf("po_id")]][$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("dia_width")]][$row[csf("uom")]];
	

					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['woven_fin_Qty']+=$woven_fin_Qty;
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['woven_grey_Qty']+=$woven_grey_Qty;
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['woven_grey_amount']+=$woven_grey_amount;

					$fabIdArrayChk[$fab_chk_str]=$fab_chk_str;

				}

					
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['rate']=$row[csf("rate")];
					 $fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['gsm_weight']=$row[csf("gsm_weight")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]][$row[csf("dia_width")]]['pre_fab_id'].=$row[csf("id")].',';

					if($row[csf("fab_source")]==2)
					{
						if($chk_po_dubArr[$row[csf("id")]]=='')
						{
						$total_purchase_amt+=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
						$chk_po_dubArr[$row[csf("id")]]=$row[csf("id")];
						}
					}
				}
				
				if(empty($set_ratio))
				{
					$sql_ratio=sql_select( "SELECT a.total_set_qnty as ratio from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by a.total_set_qnty");
					if(count($sql_ratio))
					{
						$set_ratio=$sql_ratio[0][csf('ratio')];
					}
				}
				unset($sql_fabs_result);
				 // print($fabric_btb_amt);
				//print_r($fabric_detail_arr);die;
				//echo $total_fob_value.'/'.$total_order_qty;
				$styleRef=explode(",",$txt_style_ref);
				$all_style_job="";
				foreach($styleRef as $sid)
				{
					if($all_style_job=="") $all_style_job="'".$sid."'"; else $all_style_job.=","."'".$sid."'";
				}
				$fabric_rowspan_arr=array();$uom_rowspan_arr=array();
				foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
				{
					$fabrice_rowspan=0;
					foreach($fab_data as $uom_key=>$uom_data)
					{
						$uom_rowspan=0;
						foreach($uom_data as $desc_key=>$desc_data)
						{
							foreach($desc_data as $source_key=>$source_data)
							{
								foreach($source_data as $dia_key=>$val)
								{
									$fincons=0;
									if($fab_nat_key==2) //Purchase
									{
										$fincons=$val['knit_fin_Qty'];
									}
									else
									{
										$fincons=$val['woven_fin_Qty'];
									}
									if($fincons!=0)
									{
										$uom_rowspan++;
										$fabrice_rowspan++;
									}
								}
							}
							$uom_rowspan_arr[$fab_nat_key][$uom_key]=$uom_rowspan;
						}
						$fabric_rowspan_arr[$fab_nat_key]=$fabrice_rowspan;
					}
				}


				$style1="#E9F3FF";
				$style="#FFFFFF";

				$sql_yarn="select c.id as id,c.fabric_cost_dtls_id,c.count_id, c.copm_one_id, c.percent_one,c.color,c.type_id,min(c.cons_ratio) as cons_ratio,sum(c.cons_qnty) as cons_qnty,sum(c.amount) as amount,c.rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_yarn_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.count_id,c.fabric_cost_dtls_id, c.copm_one_id, c.percent_one,  c.color,c.type_id, c.rate order  by c.count_id, c.copm_one_id,c.percent_one";
				//echo $sql_yarn;die;

					$result_yarn=sql_select($sql_yarn);
					$yarn_detail_arr=array();
					$yarnamount=$total_yarn_costing=0;
					foreach($result_yarn as $row)
					{
						$item_descrition = $lib_yarn_count[$row[csf("count_id")]].",".$composition[$row[csf("copm_one_id")]].",".$row[csf("percent_one")]."%,".$color_library[$row[csf("color")]].",".$yarn_type[$row[csf("type_id")]];
						//echo $item_descrition.'<br>';
						//echo $yarn_fabric_cost_data_arr[$row[csf("fabric_cost_dtls_id")]].', ';
						$total_yarn_costing+=$yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];
						$row_span+=1;
						$yarn_detail_arr[$item_descrition]['rate']=$row[csf("rate")];
						$yarn_detail_arr[$item_descrition]['count_id']=$row[csf("count_id")];
						$yarn_detail_arr[$item_descrition]['copm_one_id']=$row[csf("copm_one_id")];
						$yarn_detail_arr[$item_descrition]['percent_one']=$row[csf("percent_one")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarn_detail_arr[$item_descrition]['type_id']=$row[csf("type_id")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarnamount=$yarn_fabric_cost_data_arr[$row[csf("id")]]['amount'];//$yarn_data_arr[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
						$yarncons_qntys=$yarn_fabric_cost_data_arr[$row[csf("id")]]['qty'];//$yarn_data_arr[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						//echo $yarnamount.'<br/>';
						$yarn_detail_arr[$item_descrition]['yarn_cost']+=$yarnamount;
						$yarn_detail_arr[$item_descrition]['yarn_qty']+=$yarncons_qntys;

						$totalyarn_detail_arr[100]['amount']+=$yarnamount;
					}

					//echo $total_yarn_costing;die;
					unset($result_yarn);

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
						$tot_conversion_aop_costing=$tot_conversion_yarn_dyeing_costing=0;
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
								$tot_conversion_aop_costing+=array_sum($conversion_process_costing_arr[$pid][35]);
								$tot_conversion_yarn_dyeing_costing+=array_sum($conversion_process_costing_arr[$pid][30]);
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
					//	echo $total_comercial_amt.'DDDDDDDDDDDD'.$reporttype;
						$total_raw_metarial_cost= $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt;
						$total_all_cost= $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt+$total_embl_amt+$total_comercial_amt+$total_commisssion+$total_wash_costing+$total_cm_cost+$total_lab_test_cost+$total_inspection_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost+$total_freight_cost;
						 // echo number_format($total_commisssion,2);
						if($reporttype==7)
						{
						 $tot_aop_trim_yd_cost=$tot_conversion_aop_costing+$total_trims_amt+$tot_conversion_yarn_dyeing_costing;
						 $total_aop_trim_yd_cost=($tot_aop_trim_yd_cost*10)/100;
						 $total_all_cost+=$total_aop_trim_yd_cost;
						}


					 $sql_commi="select c.id, c.job_no, c.particulars_id,c.commission_base_id,avg(c.commision_rate) as rate, sum(c.commission_amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_commiss_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.commission_base_id>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id, c.job_no, c.particulars_id,c.commission_base_id order by c.id";

					$result_commi=sql_select($sql_commi);
					$commi_detail_arr=array();$tot_commission_rate=0;
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
					}
					unset($result_commi);
					$sql_comm="select c.id, c.job_no, c.item_id,avg(c.rate) as rate,sum(c.rate) as tot_rate, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_comarci_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.rate>0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id, c.job_no, c.item_id  order by c.id";


					

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
						// $commercial_cost_percent_amount=$total_yarn_costing+$total_trims_amt+$total_purchase_amt;
						//($commercial_cost_percent_amount*$tot_comm_rate)/100;
						//echo $total_job_unit_price.'='.$commercial_cost_percent;
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
       <!-- <div class="footer_signature" >
         <?
          //echo signature_table(109, $cbo_company_name, "850px");
		 ?>
      	</div>-->

             <table width="800px" style="margin-left:10px">
            
                <tr>
                    <td align="center" colspan="8" class="form_caption">
                    <strong><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></strong>
                   
                    </td>
                    
                </tr>
				<tr class="form_caption">
                    <td colspan="8" align="center">
                    
                    <strong style=" font-size:18px"><? echo $report_title;?></strong><?= $version_no;?></td>
					<b  style="color:#FF0000; float:right; font-size:large;"><? echo $approved_msg;?> </b>
                </tr>
            </table>
             <table width="850" style="margin-left:10px;" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
                <tr>
                 <th  colspan="2" align="center" style="font-size:16px"> <strong>Summary</strong></th>
               </tr>
             <tr>
             <td style="border:none">
            	<table width="600"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr  bgcolor="<? echo $style; ?>">
                        <td width="120"> <strong>Buyer</strong> </td>
                        <td width=""><? if($cbo_buyer_name==0) echo implode(",",array_unique(explode(",",$all_buyer)));else echo $buyer_arr[$cbo_buyer_name];?> </td>
                        <td width="140" ><strong>Sew. SMV(Avg).</strong></td>
                        <td width="" title="SMV*PO Qty/Total PO Qty(Pcs)">&nbsp; <?
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
                        <td width="" title="<? //echo 'Prod Min='.$prod_min.'/Avilable Min='.$available_min?>"><? echo $sew_effi_percent;?></td>
                    </tr>
                     <tr  bgcolor="<? echo $style; ?>">
                        <td width="120"><strong>Style Ref.</strong></td>
                        <td width=""><p><? echo implode(",",array_unique(explode(",",$all_style)));?></p></td>
                        <td width="140"> <strong>Style Desc.</strong> </td>
                        <td width=""><p><? echo implode(",",array_unique(explode(",",$all_style_desc)));?></p></td>
                    </tr>
                     <td>
                     <tr  bgcolor="<? echo $style1; ?>">
                        <td width="140"><strong>Avg FOB/UNIT Price[$]</strong></td>
                        <td width=""><? echo number_format($total_job_unit_price,2); ?></td>
                        <td width="140"><strong> Cost Per Minute(TK)</strong> </td>
                        <td width=""><? echo $cost_per_minute;?></td>
                    </tr>
                    <tr  bgcolor="<? echo $style; ?>">
                        <td width="100"><strong>Total Qty.(Pcs)</strong></td>
                        <td><? echo $order_qty_pcs;?></td>
                        <td width="140"><b>Total FOB[$]:</b></td>
                         <td  align="left">  <? echo number_format($total_fob_value,2);?></td>

                    </tr>
                     <tr  bgcolor="<? echo $style; ?>">
                        <td width="100"><strong>Comission [$] :</strong></td>
                        <td><? echo  number_format($total_commisssion,2);?></td>
                         <td width="140"> <b>CM Cost/Dzn(Avg)[$] : </b></td>
                          <td title="Total CM/Total Po qty(<? echo $total_order_qty;?>))*12*Set Ratio(<?=$set_ratio;?>">  <?
						  echo number_format((($total_cm_cost/$total_order_qty*12)*$set_ratio),2);?>
                         </td>
                    </tr>
                       <tr bgcolor="<? echo $style1?>"  align="left">
                           <td width="100" title=""><b>Total CM Cost[$] :</b></td>
                           <td width="" id="gross_cm_total"> <? echo number_format($total_cm_cost,2);?> </td>
                          <td width="140"  title="Fabric+Yarn+Conversion+Trims Cost"><b>Total Raw Material Cost[$]:</b></td>
                         <td id="td_sum_raw_material_cost">  <?  echo number_format($total_raw_metarial_cost,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style ?>" align="left">
                         <td width="100"><b>Total Cost[$] :</b></td>
                         <td title="Trims+Emblish+Fabric+Conversion+Lab Test+Commercial+Commission">  <?
						 echo number_format($total_all_cost,2);?></td>
                         <td width="140"><b>Total Margin[$] :</b></td>
                         <td title="Total Fob-Total Cost">  <?  $total_margin=$total_fob_value-$total_all_cost;
						 echo number_format($total_fob_value-$total_all_cost,2);?></td>
                    </tr>
                     <tr bgcolor="<? echo $style1 ?>" align="left">
                         <td width="100"  title="Total Margin/PO Qty Pcs*12"><b>Margin/Dzn :</b></td>
                         <td  >  <?  echo number_format(($total_margin/$order_qty_pcs)*12,2);?></td>
						 <?
						 $total_merch_cost=$total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt+$total_embl_amt+$total_wash_costing+$commamount+$total_commisssion+$total_lab_test_cost+$total_inspection_cost+$total_freight_cost+$total_currier_cost+$total_common_oh_cost=$total_certificate_cost;
						 ?>
						 <td width="140"><b>Merchandising Cost[$] :<? echo number_format(($total_merch_cost/$total_fob_value)*100,2);?></b>%</td>
                         <td>  <? echo number_format($total_merch_cost,2);?></td>
                         <td <? if($reporttype==7){?> title="CM Cost/Dzn(Avg)[$]/(Sew. SMV(Avg)*12)" <?}?>>
                         	<b>
                         		<?
	                         	if($reporttype==8)
	                         	{
	                         		//echo 'EPM';
	                         	}
	                         	?>
                         	</b>
                         	
                         </td>
                         <!-- <td>
                         	<?
                         	//if($reporttype==8)
                         	{
                         		//$cm_cost_d_avg=(($total_cm_cost/$total_order_qty*12)*$set_ratio);
                         		//echo fn_number_format($cm_cost_d_avg/($tot_avg_sew_smv*12),3);
                         	}
                         	?>
                         </td> -->
						 
                    </tr>
					<tr bgcolor="<? echo $style ?>" align="left">
                         <td width="100"><b>Remarks :</b></td>
                         <td colspan="3"><? echo $remarks;?></td>                         
                    </tr>
                </table>
             </td>
             <td   width="250" height="50px" valign="middle">
                   <table width="100%"   cellpadding="0" class="rpt_table"  rules="all" cellspacing="0" border="1">
                       <tr>
                       	<td colspan="2" align="center">  <strong> Material Value For BTB</strong> </td>
                       </tr>
                        <tr>
                        	<td align="center"> <strong>Item</strong></td>
                            <td  align="center"> <strong>Value[$]</strong></td>
                        </tr>
                        <tr>
                        	<td> <strong>Yarn</strong> </td>
                            <td  align="right"><? echo number_format($total_yarn_costing,2);?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Trim </strong></td>
                            <td  align="right"><? echo number_format($total_trims_amt,2);?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Fabric(Purchase)</strong> </td>
                            <td  align="right"><? echo number_format($total_purchase_amt,2);?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Conversion Cost to Fabric: </strong> </td>
                            <td  align="right"><?  echo number_format($total_conversion_cost,2);?> </td>
                        </tr>
						<tr>
                        	<td> <strong>Embellishment Cost: </strong> </td>
                            <td  align="right"><?  echo number_format($total_embl_amt,2);?> </td>
                        </tr>

                         <tr bgcolor="#CCCCCC">
                        	<td> <strong>Total</strong> </td>
                            <td  align="right"><? echo number_format($total_yarn_costing+$total_trims_amt+$total_purchase_amt+$total_conversion_cost+$total_embl_amt,2);?></td>
                        </tr>
                         <tr>
                            <td><strong> Machine/Line</strong></td>
                            <td align="center"><? echo $machine_line;?></td>
                        </tr>
                         <tr>
                            <td> <strong>Prod/Line/Hr</strong></td>
                            <td  align="center"><? echo $prod_line_hr;?></td>
                        </tr>
                      </table>
             </td>
                </tr>
            </table>
            <br/>
            <table width="600" style="margin-left:10px; font-size:16px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th> SL </th>
                <th>Particulars </th>
                <th>Cost[$] </th>
                <th>Amount[$] </th>
                <th>% </th>
            </thead>
            <tbody>

            <tr  bgcolor="<? echo $style1; ?>">
              <td>1  </td>
              <td><strong>Total FOB[$]: </strong> </td>
              <td  align="right">  <? // echo number_format($total_fob_value,2);?></td>
              <td  align="right">  <? echo number_format($total_fob_value,2);?></td>
              <td  align="right">  <? echo '100';?></td>
            </tr>
             <tr  bgcolor="<? echo $style; ?>">
              <td>2  </td>
              <td><strong>Fabric Cost(Purchase) : </strong> </td>
              <td  align="right">  <? echo number_format($total_fabric_amt,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_fabric_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >3  </td>
              <td><strong>Yarn Cost: </strong> </td>
              <td  align="right">   <? echo number_format($total_yarn_costing,2);?></td>
              <td  align="right"> </td>
              <td  align="right">  <? echo number_format(($total_yarn_costing/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >4  </td>
              <td><strong>Conversion Cost to Fabric: </strong> </td>
              <td  align="right">  <? echo number_format($total_conversion_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_conversion_cost/$total_fob_value)*100,2);?></td>
            </tr>
			<tr bgcolor="<? echo $style; ?>">
              <td >5  </td>
              <td><b style="font-size:20px;">Total Fabric+Yarn Cost:</b></td>
              <td  align="right"> <b> <? echo number_format($total_fabric_amt+$total_yarn_costing+$total_conversion_cost,2);?><b></td>
              <td  align="right">  </td>
              <td  align="right"> <b> <? echo number_format((($total_fabric_amt/$total_fob_value)+($total_yarn_costing/$total_fob_value)+($total_conversion_cost/$total_fob_value))*100,2);?><b></td>
            </tr>
            <tr bgcolor="<? echo $style1; ?>">
              <td >6  </td>
              <td><strong>Trims Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_trims_amt,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_trims_amt/$total_fob_value)*100,2);?></td>
            </tr>
            <tr bgcolor="<? echo $style; ?>">
              <td >7  </td>
              <td><strong>Embellishment Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_embl_amt+$total_wash_costing,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format((($total_embl_amt+$total_wash_costing)/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >8  </td>
              <td><strong>Commercial Cost: </strong> </td>
              <td  align="right">  <? echo number_format($commamount,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($commamount/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >9  </td>
              <td><strong>Commission Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_commisssion,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_commisssion/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >10  </td>
              <td><strong>Lab Test Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_lab_test_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_lab_test_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >11  </td>
              <td><strong>Inspection Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_inspection_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_inspection_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >12  </td>
              <td><strong>CM Cost - IE: </strong> </td>
              <td  align="right">  <? echo number_format($total_cm_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_cm_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >13  </td>
              <td><strong>Freight Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_freight_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_freight_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >14  </td>
              <td><strong>Courier Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_currier_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_currier_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >15  </td>
              <td><strong>Certificate Cost: </strong> </td>
              <td  align="right">  <? echo number_format($total_certificate_cost,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? echo number_format(($total_certificate_cost/$total_fob_value)*100,2);?></td>
            </tr>
             <!-- <tr bgcolor="<? echo $style1; ?>">
              <td >16  </td>
              <td><strong>Office OH: </strong> </td>
              <td  align="right">  <? echo number_format($total_common_oh_cost,2);?></td>
              <td  align="right">&nbsp;  </td>
              <td  align="right">  <? echo number_format(($total_common_oh_cost/$total_fob_value)*100,2);?></td>
            </tr> -->
          

             <tr bgcolor="<? echo $style; ?>">
              <td >16  </td>
              <td><strong>Total Cost:</strong> </td>
              <td  align="right">&nbsp;  </td>
              <td  align="right">  <? echo number_format($total_all_cost,2);?></td>
              <td  align="right">  <? $tot_cost_percent=($total_all_cost/$total_fob_value)*100;echo number_format($tot_cost_percent,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >17  </td>
              <td><strong>Total Margin: </strong> </td>
              <td  align="right">&nbsp;  </td>
              <td  align="right" title="Total FOB Value-Total Cost"> <? $tot_margin=$total_fob_value-$total_all_cost; echo number_format($tot_margin,2);?> </td>
              <td  align="right">  <? $tot_margin_percent=($tot_margin/$total_fob_value)*100;echo number_format($tot_margin_percent,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >18  </td>
              <td><strong>Total(%): </strong> </td>
              <td  align="right" colspan="2">  <? //echo number_format($total_embl_amt,2);?></td>
              <td  align="right">  <? echo number_format(100-($tot_cost_percent+$tot_margin_percent),2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >19  </td>
              <td><strong>Margin/ DZN: </strong> </td>
              <td  align="right"  title="Total Margin/PoQty Pcs*12">  <? echo number_format(($tot_margin/$order_qty_pcs)*12,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style; ?>">
              <td >20  </td>
              <td><strong>Price : </strong> </td>
              <td  align="right" title="Total FOB Value/Po Qty">
			  <? $price_pcs_or_set=$total_fob_value/$order_qty_pcs;
			  	$cost_pcs_set=$total_all_cost/$order_qty_pcs;
			  	echo number_format($price_pcs_or_set,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>
             <tr bgcolor="<? echo $style1; ?>">
              <td >21  </td>
              <td><strong>Cost : </strong> </td>
              <td  align="right" title="Total Cost/Po Qty">  <? echo number_format($cost_pcs_set,2);?></td>
              <td  align="right">  </td>
              <td  align="right">  <? //echo number_format(($total_embl_amt/$total_fob_value)*100,2);?></td>
            </tr>
             

            </tbody>
            </table>
           <br/>

	            <?
				$job_id_cond=where_con_using_array($job_idArr,0,'a.job_id');
			 	$lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

		 		//$data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation  from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=7 order by b.id asc");
				$approv_data_array=sql_select(" select a.job_no,b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_pre_cost_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on a.id =d.booking_id  where   b.entry_form=15 $job_id_cond order by b.id asc");
			  //echo " select a.job_no,b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_pre_cost_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on a.id =d.booking_id  where   b.entry_form=15 $job_id_cond order by b.id asc";
			
			foreach($approv_data_array as $row)
			{
				$job_wise_approv[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$job_wise_approv[$row[csf('id')]]['approved_by']=$row[csf('approved_by')];
				$job_wise_approv[$row[csf('id')]]['approved_no']=$row[csf('approved_no')];
				$job_wise_approv[$row[csf('id')]]['approved_date']=$row[csf('approved_date')];
				$job_wise_approv[$row[csf('id')]]['un_approved_reason']=$row[csf('un_approved_reason')];
				$job_wise_approv[$row[csf('id')]]['user_full_name']=$row[csf('user_full_name')];
				$job_wise_approv[$row[csf('id')]]['designation']=$row[csf('designation')];
				$job_wise_approv[$row[csf('id')]]['approval_cause']=$row[csf('approval_cause')];
			}

	 	?>
	 	<table  width="650" style=" margin:5px;" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="25%" style="border:1px solid black;">Job no</th>
                <th width="25%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="15%" style="border:1px solid black;">Approval No</th>
                <th width="30%" style="border:1px solid black;">Un Approval Cause</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($job_wise_approv as $id=>$row){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
          <tr style="border:1px solid black;" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trapp_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trapp_<? echo $i; ?>" align="center">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                 <td width="25%" style="border:1px solid black;"><? echo $row[('job_no')];?></td>
                <td width="25%" style="border:1px solid black;"><? echo $row[('user_full_name')]." / ". $lib_designation[$row[('designation')]];?></td>
                <td width="27%" style="border:1px solid black;"><? echo date("d-m-Y h:i:s",strtotime($row[('approved_date')]));?></td>
                <td width="15%" style="border:1px solid black;"><? echo $row[('approved_no')];?></td>
                <td width="30%" style="border:1px solid black;"><? echo $row[('approval_cause')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
 	 	<br>
             <?
                 echo signature_table(109, $cbo_company_name, "850px");
            ?>
           <div id="page_break_div">

            </div>
           <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">PO Details</b></caption>
					<thead>
                    	<th width="30">SL</th>
						<th width="80">Job</th>
						<th width="100">PO Number</th>
						<th width="100">PO Qty.</th>
						<th width="60">UOM</th>
                    	<th width="100">PO Qty.[Pcs]</th>
                        <th width="100">FOB/Pcs</th>
                        <th width="100"> FOB Value[$]</th>
						<th width="">CM Value</th>
                    </thead>
            </table>
                    <div  style="width:980px;margin-left:10px;" align="left">
					<table class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$i=1;$total_order_qty_pcss=0;$total_fob_val=$total_cm_value=0;$total_po_qty=0;
					foreach($sql_po_result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_qty_pcss=$row[csf('po_quantity')]*$row[csf('ratio')];
						$avg_unit=$row[csf('unit_price')];

					?>

						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="80"  align="center"><? echo $row[csf('job_prefix')]; ?></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('po_number')]; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')],0); ?></div></td>
							<td width="60" align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($order_qty_pcss,0) ?></div></td>
                             <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($avg_unit,2); ?></div></td>

                            <td width="100" align="right"><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')]*$avg_unit,2); ?></div></td>
							<td width="" align="right"><div style="word-break:break-all"><? echo number_format($other_costing_arr[$row[csf('po_id')]]['cm_cost'],2); ?></div></td>


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
                            <th colspan="3"><strong>Total</strong> </th>
                            <th align="right"><strong><? echo number_format($total_po_qty,0);?> </strong></th>
                             <th align="right"><strong><? //echo number_format($total_up_charge_val,2);?> </strong></th>
                            <th align="right"><strong><? echo number_format($total_order_qty_pcss,0);?></strong> </th>
                            <th align="right"><strong><? //echo number_format($total_exfact_qty,0);?></strong> </th>
                             <th align="right"><strong><? echo number_format($total_fob_val,2);?></strong> </th>
							  <th align="right"><strong><? echo number_format($total_cm_value,2);?></strong> </th>
                            </tr>
                            </tfoot>

                    </table>
                    </div>
           <br/><br/>
                <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <caption> <b style="float:left">Fabric Details :</b></caption>
                    <thead>
                        <th width="30">SL</th>
                        <th width="80">Fab. Nature</th>
                        <th width="200">Description</th>
                        <th width="80">Source</th>
                        <th width="80">Dia</th>
                        <th width="60">GSM</th>
                        <th width="80">Grey Qty</th>
                        <th width="80">Fin. Qty </th>
                        <th width="50">UOM</th>
                        <th width="80">Rate</th>
                        <th width="80">Amount[$]</th>
                        <th width=""> %</th>
                    </thead>
                </table>
                <div style="width:1000px; margin-left:10px" align="left" >
                <table class="rpt_table"   width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
                    $i=$m=1;$total_greycons=$total_fincons=$total_amount=$grand_total_greycons=$grand_total_fincons=$grand_total_amount=0;
                    foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
                    {
						foreach($fab_data as $uom_key=>$uom_data)
						{
							$y=1;
							foreach($uom_data as $desc_key=>$desc_data)
							{
								foreach($desc_data as $source_key=>$source_data)
								{
									foreach($source_data as $dia_key=>$val)
									{
										if($fab_nat_key==2) //Purchase
										{
											$fincons=$val['knit_fin_Qty'];
											$greycons=$val['knit_grey_Qty'];
											$amount=$val['knit_grey_amount'];
										}
										else
										{
											$fincons=$val['woven_fin_Qty'];
											$greycons=$val['woven_grey_Qty'];
											$amount=$val['woven_grey_amount'];
										}
										if($fincons!=0)
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											$pre_fab_id=rtrim($val['pre_fab_id'],',');
											$pre_fab_ids=array_unique(explode(",",$pre_fab_id));
											$rate=$val['rate'];
											$gsm_weight=$val['gsm_weight'];
											$fincons=$greycons=$amount=0;
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">  <?
												if($y==1){
													?>
													<td width="30" rowspan="<? echo $uom_rowspan_arr[$fab_nat_key][$uom_key];?>"><? echo $m; ?></td>
													<td width="80" rowspan="<? echo $uom_rowspan_arr[$fab_nat_key][$uom_key];?>">
													<? echo $item_category[$fab_nat_key]; ?></td>
													<?
												}
												?>
												<td width="200" align="center"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
												<td width="80" align="center" ><div style="word-break:break-all"><? echo $fabric_source[$source_key]; ?></div></td>
												<td width="80" align="center" ><div style="word-break:break-all"><? echo $dia_key; ?></div></td>
												<td width="60" align="center" ><div style="word-break:break-all"><? echo $gsm_weight; ?></div></td>
												<td width="80" title="" align="right"><div style="word-break:break-all"><? echo number_format($greycons,4); ?></div></td>
												<td width="80" title="" align="right"><div style="word-break:break-all"><? echo number_format($fincons,4); ?></div></td>
												
												<td width="50" align="center"><? echo $unit_of_measurement[$uom_key]; ?></td>
												<td width="80"  align="right"><div style="word-break:break-all"><? echo number_format($rate,4); ?></div></td>
												<td width="80"  align="right"><div style="word-break:break-all"><? echo number_format($amount,4); ?></div></td>
												<td align="right"><div style="word-break:break-all"><? echo number_format(($amount/$total_fob_value)*100,4); ?></div></td>
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
								}
							}
							$m++;
							?>
							<tr bgcolor="#F4F3C4">
                                <td>&nbsp; </td>
                                <td>&nbsp; </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right"><strong>Sub Total</strong> </td>
                                <td align="right"><strong><? echo number_format($total_greycons,4);$total_greycons=0;?> </strong></td>
                                <td align="right"><strong><? echo number_format($total_fincons,4);$total_fincons=0;?> </strong></td>
                                <td align="right">&nbsp;</td>
                                <td>&nbsp; </td>
                                <td align="right"><strong><? echo number_format($total_amount,4);?></strong> </td>
                                <td align="right"><?  echo number_format(($total_amount/$total_fob_value)*100,4);$total_amount=0;?> </td>
							</tr>
							<?
						}
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="6" ><strong>Grand Total</strong> </th>
                            <th align="right"><strong><? echo number_format($grand_total_greycons,4);?> </strong></th>
                            <th align="right"><strong><? echo number_format($grand_total_fincons,4);?> </strong></th>
                            <th align="right">&nbsp;</th>
                            <th>&nbsp; </th>
                            <th align="right"><strong><? echo number_format($grand_total_amount,4);?></strong> </th>
                            <th align="right"><?  echo number_format(($grand_total_amount/$total_fob_value)*100,4);?> </th>
                        </tr>
                    </tfoot>
                </table>
            </div><!--Fabtic Details End-->
            <br/><br/>
            <table id="table_header_1" style="margin-left:10px"  class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Yarn Details :</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Yarn Description</th>
						<th width="100">Yarn Qty.</th>
						<th width="100">Avg.Yarn Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount[$]</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div style="width:870px; margin-left:10px" align="left" >
					<table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?


					$i=$m=1;$grand_total_yarncons=$grand_total_yarnavgcons=$grand_total_amount=$grand_total_yarn_per=0;
					foreach($yarn_detail_arr as $desc_key=>$val)
					{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$yarn_cost=$val['yarn_cost'];
					$yarn_qty=$val['yarn_qty'];
					$yarncons_qnty=$yarn_qty;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['qty'];
					$yarnavgcons_qnty=$yarn_qty;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['qty'];
					$yarn_amount=$yarn_cost;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['amount'];
					$totalyarn_amount=$totalyarn_detail_arr[100]['amount'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('try_<? echo $i; ?>','<? echo $bgcolor;?>')" id="try_<? echo $i; ?>"> 					 <?
                      	 if($m==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $row_span;?>"><? echo 'Yarn Cost'; ?></td>
                             <?
							 }
							?>
							<td width="250"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo number_format($yarncons_qnty,4); ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($yarnavgcons_qnty,4); ?></div></td>                       <td width="100" align="right"><? echo number_format($val["rate"],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($yarn_amount,4); ?></div></td>
                             <?
							// $total_fob=$totalyarn_amount;
                      	//if($m==1){
						?>
                             <td width="" align="right" title="Yarn Amout/Total Fob*100" ><? echo number_format(($yarn_amount/$total_fob_value)*100,4); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								$grand_total_yarnavgcons+=$yarnavgcons_qnty;
								$grand_total_amount+=$yarn_amount;
								$grand_total_yarncons+=$yarncons_qnty;
								$grand_total_yarn_per+=($totalyarn_amount/$total_fob_value)*100;
								//$y++;
								$i++;
							$m++;
						}
							?>
                            <tfoot>
                            <tr>
                                <th><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? //echo number_format($grand_total_amount,4);?></strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_yarncons,4);?> </strong></th>
                                <th align="right"><strong><? echo number_format($grand_total_yarnavgcons,4);?> </strong></th>
                                <th align="right"><strong><? //echo number_format($grand_total_amount,4);?></strong> </th>
                                <th align="right"><? echo number_format($grand_total_amount,4);?></th>
                                <th align="center"><strong><? echo number_format(($totalyarn_amount/$total_fob_value)*100,4);?></strong> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Conversion Cost to Fabric :</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
						<th width="100">Required</th>
                        <th width="50">UOM</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount[$]</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div style="width:870px; margin-left:10px" align="left">
					<table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
				   /*$sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond  order by c.cons_process";*/
				    $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,sum(c.req_qnty) as req_qnty,avg(c.charge_unit) as charge_unit,sum(c.amount) as amount,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.fabric_description,a.job_no, c.cons_process,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom  order by c.cons_process";

					$result_conv=sql_select($sql_conv);
					$conv_detail_arr=array();
					$totalconv_amount=0;
					foreach($result_conv as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("pre_costdtl_id")];
						$item_desc = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]];
						$row_span+=1;
						/*$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['id']=$row[csf("id")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;*/
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['id'].=$row[csf("id")].',';
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$item_descrition][$row[csf("cons_process")]]['desc']=$item_desc;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];

						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$convQty=$conv_data_qty_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;
						$conv_detail_arrData[$item_descrition][$row[csf("cons_process")]]['amt']+=$convamount;
						$conv_detail_arrData[$item_descrition][$row[csf("cons_process")]]['req_qty']+=$convQty;

						//$totalconv_amount+=$convamount;
					}
					//echo $totalconv_amount;
					//print_r($totalconv_detail_arr);
					$conv_rowspan_arr=array();

					foreach($conv_detail_arr as $desc_key=>$desc_data)
					{

							$conv_row_span=0;
							foreach($desc_data as $process_key=>$val)
							{
								$conv_row_span++;
							}
							$conv_rowspan_arr[$desc_key]=$conv_row_span;
					}
					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_conv_qty=$grand_total_conv_amount=$grand_total_amount=$total_conv_qty=$total_conv_amount=0;
					foreach($conv_detail_arr as $desc_key=>$desc_data)
					{
						$z=1;

						foreach($desc_data as $process_key=>$val)
						{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$conv_id=rtrim($val[('id')],',');
						$item_desc=$val[('desc')];
						$conv_ids=array_unique(explode(",",$conv_id));
						//$desc_key=$val[('desc')];
						/*$convsion_qty=$conversion_amt=0;
						foreach($conv_ids as $cid)
						{
							$convsion_qty+=$conv_data_qty_arr[$cid][$val[('uom')]];
							$conversion_amt+= $conv_data_amount_arr[$cid][$val[('uom')]];
						}*/
						$conversion_amt=$conv_detail_arrData[$desc_key][$process_key]['amt'];
						$convsion_qty=$conv_detail_arrData[$desc_key][$process_key]['req_qty'];

						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$process_key];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconv_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconv_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $conv_rowspan_arr[$desc_key];?>"><? echo 'Conversion Cost'; ?></td>
                             <?
							 }
							// $desc_keyArr=array_unique(explode(",",$desc_key));
							 
							?>
							<td width="250"><div style="word-break:break-all"><? echo $item_desc; ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($conversion_amt/$convsion_qty,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>
                             <?
                      //	if($z==1){
						?>
                             <td width="" valign="middle" align="center" title="Conv. Amout(<? echo $totalconv_amount?>)/Total Fob*100" rowspan="<? //echo $conv_rowspan_arr[$desc_key];?>"><? echo  number_format(($conversion_amt/$total_fob_value)*100,4);//number_format(($totalconv_amount/$total_fob_value)*100,2); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								$total_conv_qty+=$convsion_qty;
								$total_conv_amount+=$conversion_amt;
								$grand_total_conv_qty+=$convsion_qty;
								$grand_total_conv_amount+=$conversion_amt;

								$z++;
								$i++;

								}
								?>
                               <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                <td colspan="3" align="right"><strong>Sub Total</strong> </td>
                                <td align="right"><strong><? echo number_format($total_conv_qty,4);$total_conv_qty=0;?> </strong></td>
                                <td align="right"><strong>&nbsp; </strong></td>

                                <td align="right">&nbsp;</td>
                                <td align="right"><? $sub_tot_fab_conv_cost_per=($total_conv_amount/$total_fob_value)*100;echo number_format($total_conv_amount,4);$total_conv_amount=0;?></td>
                                <td align="right"><? echo number_format($sub_tot_fab_conv_cost_per,4);?></td>
                            </tr>
                                <?
							}

							?>

                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_conv_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_conv_amount/$total_fob_value)*100,2);?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                      <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="610" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Conversion Cost to Fabric Summary:</b></caption>
					<thead>
                    	<th width="100">Particulars</th>

						<th width="100">Process</th>
						<th width="100">Required</th>
                        <th width="50">UOM</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div style="width:630px; margin-left:10px" align="left">
					<table class="rpt_table" width="610" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
				  $sql_conv_sum="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,sum(c.req_qnty) as req_qnty,avg(c.charge_unit) as charge_unit,sum(c.amount) as amount,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by c.id,c.fabric_description,a.job_no, c.cons_process,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom order by c.cons_process";

					$result_conv_sum=sql_select($sql_conv_sum);
					$conv_detail_arr=array();
					foreach($result_conv_sum as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
						//$row_span+=1;
						/*$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['id']=$row[csf("id")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['uom']=$row[csf("uom")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['amount']=$row[csf("amount")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['cons_process']=$row[csf("cons_process")];
						$conv_detail_arr[$row[csf("cons_process")]][$row[csf("id")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr[100]['amount']+=$convamount;*/
						$sum_conv_detail_arr[$row[csf("cons_process")]]['id'].=$row[csf("id")].',';
						$sum_conv_detail_arr[$row[csf("cons_process")]]['uom']=$row[csf("uom")];
						//$sum_conv_detail_arr[$row[csf("cons_process")]]['charge_unit']=$row[csf("charge_unit")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['amount']=$row[csf("amount")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['cons_process']=$row[csf("cons_process")];
						$sum_conv_detail_arr[$row[csf("cons_process")]]['desc']=$item_descrition;

						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$convamount=$conv_data_amount_arr[$row[csf("id")]][$row[csf("uom")]];
						$convQty=$conv_data_qty_arr[$row[csf("id")]][$row[csf("uom")]];
						$totalconv_detail_arr2[100]['amount']+=$convamount;

						$sum_conv_detail_arr[$row[csf("cons_process")]]['amt']+=$convamount;
						$sum_conv_detail_arr[$row[csf("cons_process")]]['req_qty']+=$convQty;

						$sum_conv_rowspan_arr[$row[csf("cons_process")]]+=1;
					}
							$sconv_row_span=1;$row_span=0;
							foreach($sum_conv_detail_arr as $process_key=>$val)
							{
								$row_span+=$sconv_row_span;
								$sum_conv_detail_arr[$process_key]['charge_unit']=($val['amt']/$val['req_qty']);
							}
					//print_r($sum_conv_rowspan_arr);
						$i=$m=1;$sum_grand_total_conv_qty=$sum_grand_total_conv_amount=$grand_total_amount=$total_conv_qty=$total_conv_amount=0;
						foreach($sum_conv_detail_arr as $process_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$conv_id=rtrim($val[('id')],',');
							$conv_ids=array_unique(explode(",",$conv_id));
							$desc_key=$val[('desc')];/*$sum_convsion_qty=$sum_conversion_amt=0;
							foreach($conv_ids as $cid)
							{
								$sum_convsion_qty+=$conv_data_qty_arr[$cid][$val[('uom')]];
								$sum_conversion_amt+= $conv_data_amount_arr[$cid][$val[('uom')]];
							}*/
							$sum_convsion_qty=$val['req_qty'];
							$sum_conversion_amt=$val['amt'];

							$totalconv_amount_sum=$totalconv_detail_arr2[100]['amount'];
							$process_name=$conversion_cost_head_array[$process_key];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvs_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvs_<? echo $i; ?>"> 					 <?
                      	 if($m==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $row_span;?>"><? echo 'Conversion Cost'; ?></td>
                             <?
							 }
							?>

							<td width="100" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($sum_convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($val["charge_unit"],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($sum_conversion_amt,4); ?></div></td>
                             <?
                      //	if($m==1){
						?>
                             <td width="" valign="middle" align="center" title="Total Conv. Amout(<? echo $totalconv_amount_sum ?>)/Total Fob*100" rowspan="<? //echo $row_span;?>"><? echo number_format(($sum_conversion_amt/$total_fob_value)*100,4); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								//$total_conv_qty+=$sum_convsion_qty;
								//$total_conv_amount+=$sum_conversion_amt;
								$sum_grand_total_conv_qty+=$sum_convsion_qty;
								$sum_grand_total_conv_amount+=$sum_conversion_amt;

								$m++;
								$i++;


								?>

                                <?
							}

							?>

                            <tfoot>
                            <tr>
                                <th colspan="2"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($sum_grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($sum_grand_total_conv_amount,4);?></th>
                                <th align="right"><? echo number_format(($sum_grand_total_conv_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="890" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Trims Cost Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="110">Item Group</th>
						<th width="200">Description</th>
						<th width="130">Nominated Supp</th>
                        <th width="50">UOM</th>
                        <th width="100">Consumption</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div style="width:910px; max-height:100%;margin-left:10px" align="left">
					<table class="rpt_table" width="890" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$trim_group_arr=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name" );
				/*   $sql_trims="select c.id, c.job_no, c.trim_group,c.description,c.brand_sup_ref,c.cons_uom, c.cons_dzn_gmts, c.rate, c.amount, c.apvl_req, c.nominated_supp,c.status_active from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_trim_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  order by c.id";*/
				 // print_r($trims_item_amount_arr);
				 if($version != ""){$table_name="wo_pre_cost_trim_cost_dtls_his c"; $where_con=" and c.APPROVED_NO=$version"; }else {$table_name="wo_pre_cost_trim_cost_dtls c"; $where_con="";}

				 $sql_trims="select c.trim_group,c.description,c.cons_uom, c.nominated_supp from wo_po_details_master a, wo_po_break_down b, $table_name  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $where_con and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond group by  c.trim_group,c.description,c.cons_uom,c.nominated_supp  order by c.trim_group";
					$result_trims=sql_select($sql_trims);
					$trims_detail_arr=array();
					foreach($result_trims as $row)
					{
						$item_descrition =$row[csf("description")];
						$trims_rowspan+=1;
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['nominated_supp']=$row[csf("nominated_supp")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['uom']=$row[csf("cons_uom")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['trim_group']=$row[csf("trim_group")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['amount']=$row[csf("amount")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['rate']=$row[csf("rate")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['id'].=$row[csf("id")].',';
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition][$row[csf("cons_uom")]]['desc']=$item_descrition;
						//$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						//$trimsamount=$trims_item_amount_arr[$row[csf("trim_group")]][$item_descrition];//$trim_arr_amount[$row[csf("id")]];
						//$totaltrims_detail_arr[100]['amount']+=$trimsamount;
					}
					//echo $trims_rowspan;
					//print_r($totalconv_detail_arr);
					/*$trim_rowspan_arr=array();
					foreach($trims_detail_arr as $trims_key=>$trims_data)
					{
							$conv_row_span=0;
							foreach($trims_data as $desc_key=>$val)
							{
								$conv_row_span++;
							}
							$trim_rowspan_arr[$trims_key]=$conv_row_span;
					}*/
					//echo $conv_row_span;
					//print_r($conv_rowspan_arr);
					$i=$z=1;$grand_total_trim_amount=0;
					foreach($trims_detail_arr as $trims_key=>$trims_data)
					{
						foreach($trims_data as $desc_key=>$desc_data)
						{
							foreach($desc_data as $uom_key=>$trims_data)
							{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$trim_amount=$trims_item_amount_arr[$trims_key][$desc_key];
							$cons_dzn_gmts=$trims_item_qty_arr[$trims_key][$desc_key];
							//$trim_group=$val[('trim_group')];
							$nominated_supp=$val[('nominated_supp')];
						//	$totaltrims_amount=$totaltrims_detail_arr[100]['amount'];
							//$trims_rowspan=$trim_rowspan_arr[$trims_key];
							$avg_rate=$trim_amount/$cons_dzn_gmts;
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrim_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trtrim_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="110"><div style="word-break:break-all"><? echo $trim_group_arr[$trims_key]; ?></div></td>
							<td width="200" align="right"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="130" align="right" ><div style="word-break:break-all"><? echo $supplier_library[$nominated_supp]; ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$uom_key]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($cons_dzn_gmts,4); ?></td></td>
                            <td width="100" align="right"><? echo number_format($avg_rate,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($trim_amount,4); ?> </div></td>
                            <? //if($z==1) { ?>
                             <td width=""  align="center" title="Trims Amount/Total Fob Value*100">
							<? echo number_format(($trim_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_trim_amount+=$trim_amount;
								$i++;//$z++;
										}
									}
							}	?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? //echo number_format($grand_total_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_trim_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_trim_amount/$total_fob_value)*100,4); ?></th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
               <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Embellishment Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="120">Particulars</th>
						<th width="100">Type</th>
						<th width="100">Gmts. Qnty (Dzn)</th>
                        <th width="100">Color</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div style="width:720px; margin-left:10px" align="left">
					<table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
			$color_library=return_library_array( "select id,color_name from  lib_color", "id", "color_name"  );
				  /* $sql_emblish="select c.id, c.job_no, c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate, c.amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order by c.id";*/
				     $sql_emblish="select b.id as po_id,c.id, c.job_no, c.emb_name,c.emb_type,d.color_number_id,e.requirment as cons_dzn_gmts,e.rate, e.amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c,wo_po_color_size_breakdown d,wo_pre_cos_emb_co_avg_con_dtls e  where a.id=b.job_id and c.job_id=b.job_id and c.job_id=a.id  and d.po_break_down_id=b.id and d.item_number_id= e.item_number_id and d.color_number_id=e.color_number_id and d.size_number_id=e.size_number_id and c.id=e.pre_cost_emb_cost_dtls_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0  $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order by c.id";

					$result_emblish=sql_select($sql_emblish);
					$emblish_detail_arr=array();
					foreach($result_emblish as $row)
					{
						$item_descrition =$row[csf("description")];
						$color_id =$row[csf("color_number_id")];
						$embData =$row[csf("emb_name")];
						$embl_rowspan+=1;
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['emb_name']=$row[csf("emb_name")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['emb_type']=$row[csf("emb_type")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['amount']+=$row[csf("amount")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['rate']=$row[csf("rate")];
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['job_no'].=$row[csf("job_no")].',';
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['po_id'].=$row[csf("po_id")].',';
						$emblish_detail_arr[$embData][$row[csf("emb_type")]][$color_id]['desc']=$item_descrition;
						//$emblishment_qty_arr
						
						$embsamount=$emblishment_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						$totalemb_detail_arr[100]['amount']+=$embsamount;
					}
					//echo $embl_rowspan;
					//print_r($conv_rowspan_arr);
					
					//$emblishment_po_amount_arr=$emblishment->getAmountArray_by_OrderEmblishmentidAndGmtscolor();
				//$emblishment_po_qty_arr
					foreach($emblish_detail_arr as $emb_name=>$enm_val)
					{
						$embl_row_span=0;
						foreach($enm_val as $emb_type=>$emb_typeData)
						{
							$embl_typerow_span=0;
							foreach($emb_typeData as $color_id=>$val)
							{
								$embl_row_span++;$embl_typerow_span++;
							}
							$emb_rowspan_arr[$emb_name]=$embl_row_span;
							$emb_rowspan_arr[$emb_name][$embl_typerow_span]=$embl_typerow_span;
						
						}
						
					}
					//print_r($emb_rowspan_arr);
				
					$i=$m=1;$grand_total_embl_amount=$grand_total_cons_dzn_gmts=0;
					foreach($emblish_detail_arr as $emb_name=>$enm_val)
					{
						$emb=0;
						foreach($enm_val as $emb_type=>$emb_typeData)
						{
						foreach($emb_typeData as $color_id=>$val)
						{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						$po_id=rtrim($val[('po_id')],',');
						$po_ids=array_unique(explode(",",$po_id));
						//$emb_name=$val[('emb_name')];$emb_type=$val[('emb_type')];
						 $totalembl_amount=$totalemb_detail_arr[100]['amount'];
						if($emb_name==1) $em_type = $emblishment_print_type[$emb_type];
						else if($emb_name==2) $em_type = $emblishment_embroy_type[$emb_type];
						else if($emb_name==3) $em_type = $emblishment_wash_type[$emb_type];
						else if($emb_name==4) $em_type = $emblishment_spwork_type[$emb_type];
						else if($emb_name==5) $em_type = $emblishment_gmts_type[$emb_type];
						else $em_type="";

       //getAmountArray_by_jobEmbnameAndEmbtypeColor
						$cons_dzn_gmts=0;$embl_amount=0;
						foreach($job_nos as $jno)
						{
							if($emb_name !=3){
								$wash_qty=$emblishment_job_qty_arr[$jno][$emb_name][$emb_type][$color_id];
								$wash_amt=$emblishment_job_amount_arr[$jno][$emb_name][$emb_type][$color_id];
								if($wash_amt) $wash_amt=$wash_amt;else $wash_amt=0;
								if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
								if(($wash_qty!="" || $wash_amt!=0) && ($wash_qty!="" || $wash_amt!=0))
								{
									$em_amount=$emblishment_job_amount_arr[$jno][$emb_name][$emb_type][$color_id];
									$cons_dzn=$emblishment_job_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									if($em_amount) $em_amount=$em_amount;else $em_amount=0;
									if($cons_dzn) $cons_dzn=$cons_dzn;else $cons_dzn=0;

									$cons_dzn_gmts+=$cons_dzn;
									$embl_amount+=$em_amount;
								}
							}
							else if($emb_name ==3){
								$wash_qty=$$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
								$wash_amt=$wash_job_type_name_amount_arr[$jno][$emb_name][$emb_type][$color_id];
								if($wash_amt) $wash_amt=$wash_amt;else $wash_amt=0;
								if($wash_qty) $wash_qty=$wash_qty;else $wash_qty=0;
								if(($wash_qty!="" || $wash_amt!=0) && ($wash_qty!="" || $wash_amt!=0))
								{
									$embl_amt=$wash_job_type_name_amount_arr[$jno][$emb_name][$emb_type][$color_id];
									$cons_dzn=$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									if($embl_amt) $embl_amt=$embl_amt;else $embl_amt=0;
									if($cons_dzn) $cons_dzn=$cons_dzn;else $cons_dzn=0;
									$cons_dzn_gmts+=$wash_job_type_name_qty_arr[$jno][$emb_name][$emb_type][$color_id];
									$embl_amount+=$embl_amt;
								}
							//echo 2;
							}
						}
						//$emb_rowspan=$emb_rowspan_arr[$emb_name];
						//wash_type_name_amount_arr
						//echo $embl_amount.',';
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tremb_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tremb_<? echo $i; ?>">
							<?
                            if($emb==0)
							{
							?>
                            <td width="30" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>">
							<? echo $i; ?></td>
                            <td width="120" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>"><div style="word-break:break-all"><? echo $emblishment_name_array[$emb_name];; ?></div></td>
							<td width="100" rowspan="<? echo $emb_rowspan_arr[$emb_name];?>" align="center"><div style="word-break:break-all"><? echo $em_type; ?></div></td>
                            <?
							}
							?>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($cons_dzn_gmts,4); ?></div>
                            <td width="100" align="center"><div style="word-break:break-all"><? echo $color_library[$color_id]; ?></div></td>

                            <td width="100" align="right"><? echo number_format($embl_amount/$cons_dzn_gmts,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($embl_amount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $embl_rowspan;?>" valign="middle" align="center" title="Total Embl Amout/Total Fob*100">
							<? echo number_format(($embl_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_embl_amount+=$embl_amount;
								$grand_total_cons_dzn_gmts+=$cons_dzn_gmts;
								$i++;$emb++;
								}
							}
						  }
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>

                                <th align="right"><strong><? echo number_format($grand_total_cons_dzn_gmts,4);?></strong></th>

                                <th align="right">&nbsp;</th>
                                  <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_embl_amount,4);?></th>
                                <th align="right"><? echo number_format(($grand_total_embl_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
              <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Commercial Cost:</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="200">Particulars</th>

                        <th width="100">Rate In %</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div style="width:490px; margin-left:10px" align="left">
					<table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?

					$i=$m=1;$grand_total_comm_amount=0;
					foreach($comm_detail_arr as $item_id=>$val)
					{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));

						 $total_comm_amount=$totalcomm_detail_arr[100]['amount'];
						//$comm_amount=$commercial_amount_arr[$val[('job_no')]][$comm_key];
						$comm_amount=0;
						foreach($job_nos as $jno)
						{
							//$comm_amount+=$commercial_item_amount_arr[$jno][$item_id];
						}
						//echo $commercial_cost_percent_amount.'='.$val['rate'].', ';
						$comm_amount=(($val['rate']*$tot_commercial_cost_amount)/100);
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trcomm_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trcomm_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="200"><div style="word-break:break-all"><? echo $camarcial_items[$item_id];; ?></div></td>


                            <td width="100" align="right"><? echo number_format($val['rate'],4); ?></td>
                            <td width="100"  align="right" title="Commercial Cost Predefined Method"><div style="word-break:break-all">
							<? echo number_format($commamount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $comm_rowspan;?>" valign="middle" align="center" title="Commercial Amount=(<? echo $comm_amount; ?>)/Total Fob*100">
							<? echo number_format(($commamount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_comm_amount+=$commamount;
								//$grand_total_cons_dzn_gmts+=$cons_dzn_gmts;
								$i++;$m++;
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>

                                <th align="right"><? echo number_format($grand_total_comm_amount,4);?></th>
                                <th align="center"><? echo number_format(($grand_total_comm_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>

              <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="570" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Commission Cost:</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="200">Particulars</th>
                        <th width="100">Commission Basis</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount($)</th>
                        <th width="">%</th>
                    </thead>
            </table>
                    <div style="width:590px; max-height:400px;margin-left:10px" align="left">
					<table class="rpt_table" width="570" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					// 	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no."";

					$i=$m=1;$grand_total_commi_amount=0;
					foreach($commi_detail_arr as $particulars_id=>$val)
					{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						//$particulars_id=$val[('particulars_id')];
						$commission_base_id=$val[('commission_base_id')];
						 $total_commi_amount=$totalcommi_detail_arr[100]['amount'];
						 $commi_amount=0;
						 foreach($job_nos as $jno)
						 {
							$commi_amount+=$commission_costing_item_arr[$jno][$particulars_id];//$commission_amount_arr[$job_no][$commi_key];
						 }
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trcommi_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trcommi_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="200"><div style="word-break:break-all"><? echo $commission_particulars[$particulars_id];; ?></div></td>
							<td width="100" align="center"><? echo $commission_base_array[$val['commission_base_id']]; ?></td>
                            <td width="100" align="right"><? echo number_format($val['rate'],4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? echo number_format($commi_amount,4); ?> </div></td>
                            <? //if($m==1) { ?>
                             <td width="" rowspan="<? //echo $commi_rowspan;?>" valign="middle" align="center" title="Commission Amount/Total Fob*100">
							<? echo number_format(($commi_amount/$total_fob_value)*100,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_commi_amount+=$commi_amount;
								$i++;//$m++;
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><? echo number_format($grand_total_commi_amount,4);?></th>
                                <th align="center"><? echo number_format(($grand_total_commi_amount/$total_fob_value)*100,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                      <br/><br/>
                      <?
				  //start	Other Components part report here -------------------------------------------
			?>

        <div style="margin-left:10px">
            <table   class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
            <label><b>Others Components</b></label>
            <thead>
                    <th width="150">Particulars</th>
                    <th width="100">Amount($)</th>
                    <th width="50">%</th>
            </thead>
            <?
          		$style1="#E9F3FF";
				$style2="#FFFFFF";
				 $total_other_components = $total_lab_test_cost+$total_inspection_cost+$total_cm_cost+$total_freight_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost;
   			?>
                <tr bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 1; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 1; ?>">
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($total_lab_test_cost,4); ?></td>
                    <td align="right" title="Lab Cost/Total FOB*100"><? echo number_format(($total_lab_test_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 2; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 2; ?>">
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($total_inspection_cost,4); ?></td>
                    <td align="right" title="Inspection Cost/Total FOB*100"><? echo number_format(($total_inspection_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <tr bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 3; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 3; ?>">
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($total_cm_cost,4); ?></td>
                    <td align="right" title="CM Cost/Total FOB*100"><? echo number_format(($total_cm_cost/$total_fob_value)*100,4); ?></td>
                </tr bgcolor="><? echo $style2 ?>">
                <tr  bgcolor="<? echo $style1 ?>" onClick="change_color('troh_<? echo 4; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 4; ?>">
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($total_freight_cost,4); ?></td>
                    <td align="right" title="Freight Cost/Total FOB*100"><? echo number_format(($total_freight_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                 <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 5; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 5; ?>">
                    <td align="left">Currier Cost </td>
                    <td align="right"><? echo number_format($total_currier_cost,4); ?></td>
                    <td align="right" title="Currier Cost/Total FOB*100"><? echo number_format(($total_currier_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                 <tr bgcolor="<? echo $style1; ?>" onClick="change_color('troh_<? echo 6; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 6; ?>">
                    <td align="left">Certificate Cost </td>
                    <td align="right"><? echo number_format($total_certificate_cost,4); ?></td>
                    <td align="right" title="Certificate Cost/Total FOB*100"><? echo number_format(($total_certificate_cost/$total_fob_value)*100,4); ?></td>
                </tr>
                <!-- <tr bgcolor="<? echo $style2 ?>" onClick="change_color('troh_<? echo 7; ?>','<? echo $bgcolor;?>')" id="troh_<? echo 7; ?>">
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($total_common_oh_cost,4); ?></td>
                    <td align="right" title="Office OH Cost/Total FOB*100"><? echo number_format(($total_common_oh_cost/$total_fob_value)*100,4); ?></td> -->
                </tr>

                <tfoot>
                <tr>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_other_components,4); ?></th>
                    <th align="right" title="Total Other Components Cost/Total FOB*100"><? echo number_format(($total_other_components/$total_fob_value)*100,4); ?> </th>
                </tr>
                </tfoot>
            </table>
            </div>
             <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Fabric Dyeing Cost Details:</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
                        <th width="100">Fab. Color</th>
						<th width="100">Color Qty.</th>
                        <th width="50">UOM</th>
                        <th width="60">Rate</th>
                        <th width="">Total Value</th>
                    </thead>
            </table>
                    <div style="width:890px; margin-left:10px" align="left">
					<table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
					foreach($pre_cost as $row)
					{
						$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
					}
					$sql_color="select a.job_no, a.total_set_qnty as ratio, b.id as po_id,c.color_number_id,c.plan_cut_qnty as po_qty from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order  by b.id";
					$result_color=sql_select($sql_color);
					foreach($result_color as $row)
					{
						$costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per==1) $order_price_per_dzn=12;
						else if($costing_per==2) $order_price_per_dzn=1;
						else if($costing_per==3) $order_price_per_dzn=24;
						else if($costing_per==4) $order_price_per_dzn=36;
						else if($costing_per==5) $order_price_per_dzn=48;
					//echo $order_price_per_dzn.'ffd';
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['po_qty']+=$row[csf("po_qty")];
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['costing_per']=$order_price_per_dzn;
						
						$job_po_qty_arr[$row[csf("job_no")]]['po_qty']+=$row[csf("po_qty")];
						$job_po_qty_arr[$row[csf("job_no")]]['costing_per']=$order_price_per_dzn;
					}

				    $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0  and c.cons_process in(31) $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order by c.color_break_down";

					$result_conv=sql_select($sql_conv);
					$conv_detail_arr=array();

					foreach($result_conv as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")]."***".$row[csf("job_no")];
						$color_break_down=explode("__",$row[csf("color_break_down")]);
						$cons_qty=0;
						foreach($color_break_down as $fcolor)
						{
							$color_down=explode("_",$fcolor);

							$gmt_color=$color_down[0];
							$unit_charge=$color_down[1];
							$fab_color=$color_down[3];
							$cons_qty=$color_down[4];
							//echo $cons_qty.'='.'<br>';
							$conv_detail_arr[$item_descrition][$fab_color]['job_no'].=$row[csf("job_no")].',';
							$conv_detail_arr[$item_descrition][$fab_color]['uom']=$row[csf("uom")];
							$conv_detail_arr[$item_descrition][$fab_color]['charge_unit']=$row[csf("charge_unit")];
							$conv_detail_arr[$item_descrition][$fab_color]['amount']=$row[csf("amount")];
							$conv_detail_arr[$item_descrition][$fab_color]['cons_process']=$row[csf("cons_process")];
							$conv_detail_arr[$item_descrition][$fab_color]['desc']=$item_descrition;
							$conv_detail_arr[$item_descrition][$fab_color]['gmt_color']=$gmt_color;
							$conv_detail_arr[$item_descrition][$fab_color]['unit_charge']=$unit_charge;
							$conv_detail_arr[$item_descrition][$fab_color]['cons_qty']=$cons_qty;
						}
					}

					//print_r($totalconv_detail_arr);
					$conv_rowspan_arr=array();
					foreach($conv_detail_arr as $fab_key=>$fab_data)
					{
						$conv_row_span=0;
						foreach($fab_data as $color_key=>$val)
						{
							$conv_row_span++;
						}
						$conv_rowspan_arr[$fab_key]=$conv_row_span;
					}

					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_dyeing_conv_qty=$grand_total_dyeing_conv_amount=$grand_total_amount=$total_dyeing_conv_qty=$total_dyeing_conv_amount=0;
					foreach($conv_detail_arr as $fab_key=>$fab_data)
					{
						$z=1;
						foreach($fab_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));

						$cons_qty=$val[('cons_qty')];
						$cons_process=$val[('cons_process')];
						$unit_charge=$val[('unit_charge')];
						$gmt_color=$val[('gmt_color')];

						$color_po_qty=$costing_per=0;
						foreach($job_nos as $jno)
						{

							$costing_per=$color_po_qty_arr[$jno][$gmt_color]['costing_per'];
							if($costing_per!='') $costing_per=$costing_per;else $costing_per=0;
							if($color_po_qty_arr[$jno][$gmt_color]['po_qty']!="" || $color_po_qty_arr[$jno][$gmt_color]['po_qty']!=0) {

							//echo $color_po_qty_arr[$jno][$gmt_color]['po_qty'].', ';
							$color_po_qty+=$color_po_qty_arr[$jno][$gmt_color]['po_qty']/$costing_per;
							}
						}
						$convsion_qty=$cons_qty*$color_po_qty;
						$conversion_amt= $convsion_qty*$unit_charge;

						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$cons_process];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvf_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvf_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
                      	 	$job_fab=explode("***", $fab_key);
						?>
							<td width="100" valign="middle" align="center" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>"><? echo $m; ?></td>
                            <td width="250" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>" title="<?=$job_fab[1]?>"><div style="word-break:break-all"><? echo $job_fab[0]; ?></div></td>
							<td width="100" rowspan="<? echo $conv_rowspan_arr[$fab_key];?>" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
                             <?
							 }
							?>

                            <td width="100" align="right" title="<? echo $cons_qty.'=='.$color_po_qty;?>"><div style="word-break:break-all"><? echo $color_library[$color_key]; ?></div></td>
							<td width="100" align="right" title="<? echo $color_po_qty;?>"><div style="word-break:break-all" ><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="60" align="right"><? echo number_format($unit_charge,4); ?></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>

                            </tr>
                            <?
								$total_dyeing_conv_qty+=$convsion_qty;
								$total_dyeing_conv_amount+=$conversion_amt;
								$grand_total_dyeing_conv_qty+=$convsion_qty;
								$grand_total_dyeing_conv_amount+=$conversion_amt;

								$z++;
								$i++;
									}
									$m++;
									?>
                             	 <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                    <td colspan="4" align="right"><strong>Sub Total</strong> </td>
                                    <td align="right"><strong><? echo number_format($total_dyeing_conv_qty,4);$total_dyeing_conv_qty=0;?> </strong></td>
                                    <td align="right"><strong>&nbsp; </strong></td>
                                    <td align="right">&nbsp;</td>
                                    <td align="right"><? echo number_format($total_dyeing_conv_amount,4);$total_dyeing_conv_amount=0;?></td>
                                	</tr>
                                <?
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_dyeing_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_dyeing_conv_amount,4);?></th>

                            </tr>
                            </tfoot>
                    </table>
                    </div>
                    
                 <br/><br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Knitting Cost Details:</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
						<th width="100">Req. Qty.</th>
                        <th width="50">UOM</th>
                        <th width="60">Rate</th>
                        <th width="">Total Value</th>
                    </thead>
            </table>
                    <div style="width:890px; margin-left:10px" align="left">
					<table class="rpt_table" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					/*$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
					foreach($pre_cost as $row)
					{
						$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
					}
					$sql_color="select a.job_no, a.total_set_qnty as ratio, b.id as po_id,c.color_number_id,c.plan_cut_qnty as po_qty from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and c.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order  by b.id";
					$result_color=sql_select($sql_color);
					foreach($result_color as $row)
					{
						$costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per==1) $order_price_per_dzn=12;
						else if($costing_per==2) $order_price_per_dzn=1;
						else if($costing_per==3) $order_price_per_dzn=24;
						else if($costing_per==4) $order_price_per_dzn=36;
						else if($costing_per==5) $order_price_per_dzn=48;
					//echo $order_price_per_dzn.'ffd';
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['po_qty']+=$row[csf("po_qty")];
						$color_po_qty_arr[$row[csf("job_no")]][$row[csf("color_number_id")]]['costing_per']=$order_price_per_dzn;
					}*/

				    $sql_convKnit="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,c.req_qnty,c.charge_unit,c.amount,c.color_break_down, c.status_active,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0  and c.cons_process in(1) $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond  $file_po_idCond order by c.color_break_down";

					$result_convKnit=sql_select($sql_convKnit);
					$conv_detail_arr=array();

					foreach($result_convKnit as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")]."***".$row[csf("job_no")];
						//$color_break_down=explode("__",$row[csf("color_break_down")]);
						 
						 
						$process_id=1;
							$knit_conv_detail_arr[$item_descrition][$process_id]['job_no'].=$row[csf("job_no")].',';
							$knit_conv_detail_arr[$item_descrition][$process_id]['uom']=$row[csf("uom")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['charge_unit']=$row[csf("charge_unit")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['amount']=$row[csf("amount")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['cons_process']=$row[csf("cons_process")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['desc']=$item_descrition;
							//$conv_detail_arr[$item_descrition][$process_id]['gmt_color']=$gmt_color;
							//$knit_conv_detail_arr[$item_descrition][$process_id]['unit_charge']=$unit_charge;
							$knit_conv_detail_arr[$item_descrition][$process_id]['unit_charge']=$row[csf("charge_unit")];
							$knit_conv_detail_arr[$item_descrition][$process_id]['cons_qty']=$row[csf("req_qnty")];
					}

					//print_r($totalconv_detail_arr);
					$knitconv_rowspan_arr=array();
					foreach($knit_conv_detail_arr as $fab_key=>$fab_data)
					{
						$knitconv_row_span=0;
						foreach($fab_data as $color_key=>$val)
						{
							$knitconv_row_span++;
						}
						$knitconv_rowspan_arr[$fab_key]=$knitconv_row_span;
					}

					//print_r($conv_rowspan_arr);

					$i=$m=1;$grand_total_dyeing_conv_qty=$grand_total_dyeing_conv_amount=$grand_total_amount=$total_dyeing_conv_qty=$total_dyeing_conv_amount=0;
					foreach($knit_conv_detail_arr as $fab_key=>$fab_data)
					{
						$z=1;
						foreach($fab_data as $process_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));

						$cons_qty=$val[('cons_qty')];

						$cons_process=$val[('cons_process')];
						$unit_charge=$val[('unit_charge')];
						$gmt_color=$val[('gmt_color')];

						$color_po_qty=$costing_per=0;
						foreach($job_nos as $jno)
						{

							$costing_per=$job_po_qty_arr[$jno]['costing_per'];
							if($costing_per!='') $costing_per=$costing_per;else $costing_per=0;
							if($job_po_qty_arr[$jno]['po_qty']!="" || $job_po_qty_arr[$jno]['po_qty']!=0) {

							//echo $color_po_qty_arr[$jno][$gmt_color]['po_qty'].', ';
							$color_po_qty+=$job_po_qty_arr[$jno]['po_qty']/$costing_per;
							}
						}
						$convsion_qty=$cons_qty*$color_po_qty;
						$conversion_amt= $convsion_qty*$unit_charge;
						
						$totalconv_amount=$totalconv_detail_arr[100]['amount'];
						$process_name=$conversion_cost_head_array[$cons_process];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconvk_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconvk_<? echo $i; ?>"> 					 <?
                      	 if($z==1){
                      	 	$job_fab=explode("***", $fab_key);
						?>
							<td width="100" valign="middle" align="center" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>"><? echo $m; ?></td>
                            <td width="250" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>" title="<?=$job_fab[1]?>"><div style="word-break:break-all"><? echo $job_fab[0]; ?></div></td>
							<td width="100" rowspan="<? echo $knitconv_rowspan_arr[$fab_key];?>" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
                             <?
							 }
							?>

                            
							<td width="100" align="right" title="<? echo $color_po_qty;?>"><div style="word-break:break-all" ><? echo number_format($convsion_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="60" align="right"><? echo number_format($unit_charge,4); ?></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format($conversion_amt,4); ?></div></td>

                            </tr>
                            <?
								$total_dyeing_conv_qty+=$convsion_qty;
								$total_dyeing_conv_amount+=$conversion_amt;
								$grand_total_dyeing_conv_qty+=$convsion_qty;
								$grand_total_dyeing_conv_amount+=$conversion_amt;

								$z++;
								$i++;
									}
									$m++;
									?>
                             	 <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                    <td colspan="3" align="right"><strong>Sub Total</strong> </td>
                                    <td align="right"><strong><? echo number_format($total_dyeing_conv_qty,4);$total_dyeing_conv_qty=0;?> </strong></td>
                                    <td align="right"><strong>&nbsp; </strong></td>
                                    <td align="right">&nbsp;</td>
                                    <td align="right"><? echo number_format($total_dyeing_conv_amount,4);$total_dyeing_conv_amount=0;?></td>
                                	</tr>
                                <?
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_dyeing_conv_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_dyeing_conv_amount,4);?></th>

                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/>
             		<?
                		 echo signature_table(109, $cbo_company_name, "850px");
           			 ?>


        </div> <!--Main Div End-->
		<?
	 
	}
	else if($reporttype==2) //Budget Button 2
	{

			$sql="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number,b.plan_cut, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond order  by b.id ";

			$sql_po_result=sql_select($sql);
			$all_po_id="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer="";
			$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;
			//echo $buyer_name;die;
			$fabric_detail_arr=array();
			foreach($sql_po_result as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
				if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
				if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
				if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
				if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
				if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];

				$po_wise_arr[$row[csf("po_id")]]['plan_cut']+=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
				$total_order_qty+=$row[csf('po_quantity')];
				$total_unit_price+=$row[csf('unit_price')];
				$total_fob_value+=$row[csf('po_total_price')];

			}
			$total_job_unit_price=($total_fob_value/$total_order_qty);
			//echo $all_job;die;
			$all_job_no=array_unique(explode(",",$all_full_job));
			$all_jobs="";
			foreach($all_job_no as $jno)
			{
					if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
			}

			$condition= new condition();
			$condition->company_name("=$cbo_company_name");
			if(str_replace("'","",$cbo_buyer_name)>0)
			{
				$condition->buyer_name("=$cbo_buyer_name");
			}
			if($txt_order_id!='' || $txt_order_id!=0)
			{
				$condition->po_id("in($txt_order_id)");
			}
			if($file_po_id!='' || $file_po_id!=0)
			{
				$condition->po_id("in($file_po_id)");
			}
			if(str_replace("'","",$txt_style_ref)!='')
			{
			//$condition->style_ref_no("in($txt_style_ref)");
				$condition->job_no("in($all_jobs)");
			}
			if(str_replace("'","",$file_no)!='')
			{
				$condition->file_no("in($file_no)");
			}
			$condition->init();
			$fabric= new fabric($condition);
			$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

			$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$trim= new trims($condition);
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			$other= new other($condition);
			$other_cost=$other->getAmountArray_by_job();
			$commercial= new commercial($condition);
			$commision= new commision($condition);

			$po_qty=0;
			$po_plun_cut_qty=0;
			$total_set_qnty=0;
			$sql_po="select a.job_no,a.total_set_qnty,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty
			 from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id
			 and a.job_no in(".$all_jobs.")  $file_no_cond  $file_po_idCond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
			  order by b.id";
			$sql_po_data=sql_select($sql_po);
			foreach($sql_po_data as $sql_po_row)
			{
			$po_qty+=$sql_po_row[csf('order_quantity')];
			$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
			$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
			}

			$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
			foreach($pre_cost as $row)
			{
				$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
			}

			$company_con="";
			if($cbo_company_name!=0) $company_con="and company_id=$cbo_company_name";
			else if($cbo_style_owner!=0) $company_con="and company_id=$cbo_style_owner";
			//else if($cbo_company_name==0) $company_con=$cbo_company_name;
			$financial_para=array();

			$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where  status_active=1 and	is_deleted=0 $company_con order by id");
			foreach($sql_std_para as $sql_std_row)
			{
				$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
				$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
				$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
			}

		    $sql_new = "select job_no,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,total_cost ,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
			from wo_pre_cost_dtls
			where job_no in(".$all_jobs.") and status_active=1 and is_deleted=0";
			$data_array_new=sql_select($sql_new);
			$summary_data=array();

            foreach( $data_array_new as $row_new )
			{

					$costing_per=$costing_per_arr[$row_new[csf('job_no')]];

				if($costing_per==1)
				{
				$order_price_per_dzn=12;
				$costing_for=" DZN";
				}
				else if($costing_per==2)
				{
					$order_price_per_dzn=1;
					$costing_for=" PCS";
				}
				else if($costing_per==3)
				{
					$order_price_per_dzn=24;
					$costing_for=" 2 DZN";
				}
				else if($costing_per==4)
				{
					$order_price_per_dzn=36;
					$costing_for=" 3 DZN";
				}
				else if($costing_per==5)
				{
					$order_price_per_dzn=48;
					$costing_for=" 4 DZN";
				}
				$order_job_qnty=$row[csf("job_quantity")];
				$avg_unit_price=$row[csf("avg_unit_price")];


				$summary_data[price_dzn]+=$row_new[csf("price_dzn")];
				$summary_data[price_dzn_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
			    $summary_data[commission]+=$row_new[csf("commission")];
				//$summary_data[commission_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("commission")];
				//echo $po_qty.'/'.$total_set_qnty.'*'.$order_price_per_dzn.'*'.$row_new[csf("price_dzn")];
				$summary_data[trims_cost]+=$row_new[csf("trims_cost")];
				$summary_data[emb_cost]+=$row_new[csf("embel_cost")];

				$summary_data[lab_test]+=$row_new[csf("lab_test")];
				$summary_data[lab_test_job]+=$other_cost[$row_new[csf("job_no")]]['lab_test'];

				$summary_data[inspection]+=$row_new[csf("inspection")];
				$summary_data[inspection_job]+=$other_cost[$row_new[csf("job_no")]]['inspection'];

				$summary_data[freight]+=$row_new[csf("freight")];
				$summary_data[freight_job]+=$other_cost[$row_new[csf("job_no")]]['freight'];

				$summary_data[currier_pre_cost]+=$row_new[csf("currier_pre_cost")];
				$summary_data[currier_pre_cost_job]+=$other_cost[$row_new[csf("job_no")]]['currier_pre_cost'];

				$summary_data[certificate_pre_cost]+=$row_new[csf("certificate_pre_cost")];
				$summary_data[certificate_pre_cost_job]+=$other_cost[$row_new[csf("job_no")]]['certificate_pre_cost'];
				$summary_data[wash_cost]+=$row_new[csf("wash_cost")];

				$summary_data[OtherDirectExpenses]+=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];

				$summary_data[OtherDirectExpenses_job]=$summary_data[lab_test_job]+$summary_data[inspection_job]+$summary_data[freight_job]+$summary_data[currier_pre_cost_job]+$summary_data[certificate_pre_cost_job];

				$summary_data[cm_cost]+=$row_new[csf("cm_cost")];
				$summary_data[cm_cost_job]+=$other_cost[$row_new[csf("job_no")]]['cm_cost'];

				$summary_data[comm_cost]+=$row_new[csf("comm_cost")];
				//$summary_data[comm_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("comm_cost")];

				$summary_data[common_oh]+=$row_new[csf("common_oh")];
				$summary_data[common_oh_job]+=$other_cost[$row_new[csf("job_no")]]['common_oh'];
				$summary_data[depr_amor_pre_cost]+=$row_new[csf("depr_amor_pre_cost")];
				$summary_data[depr_amor_pre_cost_job]+=$other_cost[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
			}

			//Fabric =====================
			$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			//$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			//$fabric_amount_arr=$fabric->getAmountArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
			$fabric_amount_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			//print_r($fabric_amount_arr);
			$sql_fabric = "select  a.uom, sum(a.amount) as amount,b.po_break_down_id  from wo_pre_cost_fabric_cost_dtls a ,wo_pre_cos_fab_co_avg_con_dtls b where a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no in(".$all_jobs.") and b.po_break_down_id in($all_po_id) and a.fabric_source=2 group by a.uom,b.po_break_down_id";
			$data_arr_fabric=sql_select($sql_fabric);
			$fabric_po_check_arr=array();
			foreach($data_arr_fabric as $fab_row)
			{
				$plan_cut=$po_wise_arr[$fab_row[csf("po_break_down_id")]]['plan_cut'];
				$tot_fabric_amount=$fabric_amount_arr['knit']['grey'][$fab_row[csf("po_break_down_id")]][$fab_row[csf("uom")]];
				$tot_fabric_amount2=$fabric_amount_arr['woven']['grey'][$fab_row[csf("po_break_down_id")]][$fab_row[csf("uom")]];
				//echo $plan_cut.'dd';
			//	echo $tot_fabric_amount2.'='.$tot_fabric_amount.' ,';

						/*$group_po_value=$fab_row[csf("po_break_down_id")];
						if (!in_array($group_po_value,$fabric_po_check_arr) )
						{
							$tot_fabric_amountFb=$tot_fabric_amount+$tot_fabric_amount2;
							$dzn_fabric_amount=($tot_fabric_amountFb/$plan_cut)*12;
							if($tot_fabric_amount2>0 || $tot_fabric_amount>0)
							{
								$summary_data[fabric_cost][$fab_row[csf("id")]]=$dzn_fabric_amount;
							}
							$summary_data[fabric_cost_job]+=$tot_fabric_amount2+$tot_fabric_amount;
							$fabric_po_check_arr[]=$group_po_value;
						}*/

						$tot_fabric_amountFb=$tot_fabric_amount+$tot_fabric_amount2;
						$dzn_fabric_amount=($tot_fabric_amountFb/$plan_cut)*12;
						if($tot_fabric_amount2>0 || $tot_fabric_amount>0)
						{
						$summary_data[fabric_cost][$fab_row[csf("id")]]=$dzn_fabric_amount;
						}
						$summary_data[fabric_cost_job]+=$tot_fabric_amount2+$tot_fabric_amount;
			}


			$totYarn=0;
			$YarnData=array();
			$yarn_data_array=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();

			$sql_yarn="select f.id as yarn_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,count_id,copm_one_id,percent_one,color,type_id   from wo_pre_cost_fab_yarn_cost_dtls f where   f.job_no in(".$all_jobs.") and f.is_deleted=0 and f.status_active=1  order by f.id";
			$data_arr_yarn=sql_select($sql_yarn);
			foreach($data_arr_yarn as $yarn_row)
			{
				$yarnrate=$yarn_row[csf("rate")];
				$summary_data[yarn_cost][$yarn_row[csf("yarn_id")]]+=$yarn_row[csf("amount")];
				$summary_data[yarn_cost_job]+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];

				$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."_".$yarn_row[csf("color")]."_".$yarnrate."'";
				$YarnData[$index]['qty']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
				$YarnData[$index]['amount']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
				$YarnData[$index]['dznqty']+=$yarn_row[csf("cons_qnty")];
				$YarnData[$index]['dznamount']+=$yarn_row[csf("amount")];
				$totYarn+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
			}

			// Yarn End
			// Conversion
			$totConv=0;
			$ConvData=array();
			$conv_data=array();
			$conv_amount_arr=$conversion->getAmountArray_by_conversionid();
			$conv_qty_arr=$conversion->getQtyArray_by_conversionid();
			$conv_process_amount_arr=$conversion->getAmountArray_by_jobAndProcess();
			//print_r($conv_amount_arr);
			$sql_conv = "select a.id as con_id, a.fabric_description as fabric_description_id, a.job_no, a.cons_process, a.req_qnty,a.avg_req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.uom  from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id where a.job_no in(".$all_jobs.") ";
			$data_arr_conv=sql_select($sql_conv);
			foreach($data_arr_conv as $conv_row){
				$convamount=$conv_amount_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
				$convQty=$conv_qty_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];

				$conv_data[cons_process][$conv_row[csf('cons_process')]]=$conv_row[csf('cons_process')];

				//$conv_data[conv_id][$conv_row[csf('cons_process')]].=$conv_row[csf('con_id')].',';
				$conv_data[job_no][$conv_row[csf('cons_process')]].=$conv_row[csf('job_no')].',';

				$conv_data[amount][$conv_row[csf('con_id')]]=$conv_row[csf('amount')];
				$conv_data[amount_job][$conv_row[csf('con_id')]]+=$convamount;
				$summary_data[conver_cost_job]+=$convamount;
				//echo $conv_row[csf('amount')].',';
				$index=$conv_row[csf('con_id')];
				$ConvData[$index]['item_descrition']=$body_part[$conv_row[csf("body_part_id")]].", ".$color_type[$conv_row[csf("color_type_id")]].", ".$conv_row[csf("fabric_description")];
				$ConvData[$index]['cons_process']=$conv_row[csf("cons_process")];
				$ConvData[$index]['req_qnty']+=$conv_row[csf("req_qnty")];
				$ConvData[$index]['uom']=$conv_row[csf("uom")];
				$ConvData[$index]['charge_unit']=$conv_row[csf("charge_unit")];
				$ConvData[$index]['amount']+=$conv_row[csf("amount")];
				$ConvData[$index]['tot_req_qnty']+=$convQty;
				$ConvData[$index]['tot_amount']+=$convamount;
				$totConv+=$conv_row[csf("req_qnty")];
			}

			$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls
			where job_no in(".$all_jobs.")  order by id";
			$data_array_trim=sql_select($sql_trim);
			$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
			$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
			$totTrim=0;
			$TrimData=array();
			foreach( $data_array_trim as $row_trim )
			{
				$trim_qty=$trim_qty_arr[$row_trim[csf("id")]];
				$trim_amount=$trim_amount_arr[$row_trim[csf("id")]];
				$summary_data[trims_cost_job]+=$trim_amount;
				$TrimData[$row_trim[csf('id')]]['trim_group']=$row_trim[csf('trim_group')];
				$TrimData[$row_trim[csf('id')]]['description']=$row_trim[csf('description')];
				$TrimData[$row_trim[csf('id')]]['brand_sup_ref']=$row_trim[csf('brand_sup_ref')];
				$TrimData[$row_trim[csf('id')]]['remark']=$row_trim[csf('remark')];
				$TrimData[$row_trim[csf('id')]]['cons_uom']=$row_trim[csf('cons_uom')];
				$TrimData[$row_trim[csf('id')]]['cons_dzn_gmts']=$row_trim[csf('cons_dzn_gmts')];
				$TrimData[$row_trim[csf('id')]]['rate']=$row_trim[csf('rate')];
				$TrimData[$row_trim[csf('id')]]['amount']+=$row_trim[csf('amount')];
				$TrimData[$row_trim[csf('id')]]['apvl_req']=$row_trim[csf('apvl_req')];
				$TrimData[$row_trim[csf('id')]]['nominated_supp']=$row_trim[csf('nominated_supp')];
				$TrimData[$row_trim[csf('id')]]['tot_cons']+=$trim_qty;
				$TrimData[$row_trim[csf('id')]]['tot_amount']+=$trim_amount;
				$totTrim+=$row_trim[csf('cons_dzn_gmts')];
			}
				$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no in(".$all_jobs.") and emb_name in(1,2,4,5)";
				$data_array=sql_select($sql);
				$emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
				$emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();

				$totEmb=0;
				$EmbData=array();

				foreach( $data_array as $row )
				{
					$embqty=$emblishment_qty[$row[csf("job_no")]][$row[csf("id")]];
					$embamount=$emblishment_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data[emb_cost_job]+=$embamount;
					$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
					$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
					$EmbData[$row[csf("id")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
					$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$EmbData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$EmbData[$row[csf("id")]]['tot_cons']+=$embqty;
					$EmbData[$row[csf("id")]]['tot_amount']+=$embamount;
					$totEmb+=$row[csf("cons_dzn_gmts")];
				}

				//End Emb cost Cost part report here -------------------------------------------
				//Wash cost Cost part report here -------------------------------------------
				$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no in(".$all_jobs.") and emb_name =3";
				$data_array=sql_select($sql);
				$wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
				$wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
				foreach( $data_array as $row )
				{
					$washqty=$wash_qty[$row[csf("job_no")]][$row[csf("id")]];
					$washamount=$wash_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data[wash_cost_job]+=$washamount;
					$summary_data[OtherDirectExpenses_job]+=$washamount;
					$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
					$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
					$EmbData[$row[csf("id")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
					$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$EmbData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$EmbData[$row[csf("id")]]['tot_cons']+=$washqty;
					$EmbData[$row[csf("id")]]['tot_amount']+=$washamount;
					$totEmb+=$row[csf("cons_dzn_gmts")];
				}

				//End Wash cost Cost part report here -------------------------------------------
				//Commision cost Cost part report here -------------------------------------------
				$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no in(".$all_jobs.") ";
				$data_array=sql_select($sql);
				$commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
				$totCommi=0;
				$CommiData=array();
				foreach( $data_array as $row )
				{
					$commisionamount=$commision_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data[commission_job]+=$commisionamount;
					$CommiData[$row[csf("id")]]['particulars_id']=$row[csf("particulars_id")];
					$CommiData[$row[csf("id")]]['commission_base_id']=$row[csf("commission_base_id")];
					$CommiData[$row[csf("id")]]['commision_rate']=$row[csf("commision_rate")];
					$CommiData[$row[csf("id")]]['commission_amount']+=$row[csf("commission_amount")];
					$CommiData[$row[csf("id")]]['tot_commission_amount']+=$commisionamount;
					$totCommi+=$row[csf("commission_amount")];
				}

				//End Commision cost Cost part report here -------------------------------------------
				//Commarcial cost Cost part report here -------------------------------------------
				$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls  where job_no in(".$all_jobs.") ";
				$data_array=sql_select($sql);
				$commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();

				$totCommar=0;
				$CommarData=array();
				foreach( $data_array as $row )
				{
					$commarcialamount=$commarcial_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data[comm_cost_job]+=$commarcialamount;
					$CommarData[$row[csf("id")]]['item_id']=$row[csf("item_id")];
					$CommarData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$CommarData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$CommarData[$row[csf("id")]]['tot_amount']+=$commarcialamount;
					$totCommar+=$row[csf("amount")];
				}
			?>

         <div style="width:100%">
             <table width="800px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="8" align="center"><strong style=" font-size:18px"><? echo $report_title;?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="8" class="form_caption"><? echo $company_library[$cbo_company_name].'<br>';if($cbo_style_owner!=0) echo 'Style Owner: '.$company_library[$cbo_style_owner]; ?></td>
                </tr>
            </table>
            <table width="auto" style="margin-left:10px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">

                <thead>
               		 <th colspan="2" align="center"><strong>SUMMARY:</strong></th>
                 </thead>
                    <tr bgcolor="<? echo $style1?>" align="left">
                     	<td width="160"><b>Buyer &nbsp;&nbsp;</b> </td>
                     	<td>  <?
						$total_fob_value_with_upcharge=$total_fob_value+$total_order_upcharge;
						if($cbo_buyer_name==0) echo implode(",",array_unique(explode(",",$all_buyer)));
						else echo $buyer_arr[$cbo_buyer_name];?></td>
                    </tr>
                	<tr bgcolor="<? echo $style?>" align="left">
                        <td width="70"><b>Job No &nbsp;&nbsp; </b></td>
                        <td  width="auto"><p><? echo implode(",",array_unique(explode(",",$all_job)));?></p></td>
                    </tr>
                 	<tr bgcolor="<? echo $style1 ?>" align="left">
                         <td width="80" ><b>Style No&nbsp;&nbsp;</b></td>
                         <td width="auto"><p><? echo implode(",",array_unique(explode(",",$all_style)));?></p></td>
                    </tr>
                     <tr  bgcolor="<? echo $style?>" align="left">
                         <td width="80" ><b>Total Qty. In Pcs :</b></td>
                         <td align="left" id="po_qty_pcs_td">  <? echo $order_qty_pcs;?></td>
                    </tr>
                    <tr  bgcolor="<? echo $style1?>" align="left">
                          <td width="80"><b>Total FOB [$] :</b></td>
                          <td  align="left" id="total_fob_td">  <? echo number_format($total_fob_value,2);?></td>
                    </tr>

                    <tr  bgcolor="<? echo $style ?>" align="left">
                           <td width="80"><b>Style Desc. :</b></td>
                           <td width="auto"><p><? echo implode(",",array_unique(explode(",",$all_style_desc)));?></p></td>
                    </tr>
                    <tr  bgcolor="<? echo $style ?>" align="left">
                           <td width="80"><b>Avg FOB/UNIT Price[$]:</b></td>
                           <td width="auto" title="FOB Value/Order Qty ">  <? echo number_format($total_job_unit_price,2); ?></td>
                    </tr>
           </table>
           <br/><br/>
           <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center; margin-left:10px" rules="all">
         			 <caption> <strong>  Order Profitability </strong> </caption>
                        <thead>
                            <th width="80">Line Items</th>
                            <th width="380">Particulars</th>
                            <th width="100">Amount (USD)/<? echo $costing_for; ?></th>
                            <th width="100">Total Value</th>
                            <th width="100">%</th>
                        </thead>
                        <tr>
                            <td width="80">1</td>
                            <td width="380" align="left" style="font-weight:bold">Gross FOB Value</td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[price_dzn],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[price_dzn_job],4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[price_dzn_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                            <td width="80">2</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: commission</td>
                            <td width="100" align="right"><? echo number_format($summary_data[commission],4); ?></td>
                            <td width="100" align="right"><? echo number_format($summary_data[commission_job],4); ?></td>
                            <td width="180" align="right"><? echo number_format(($summary_data[commission_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                           <td width="80">3</td>
                            <?
							$NetFOBValue=$summary_data[price_dzn]-$summary_data[commission];
							$NetFOBValue_job=$summary_data[price_dzn_job]-$summary_data[commission_job];
							?>
                            <td width="380" align="left" style="font-weight:bold"><b>Net FOB Value (1-2)</b></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($NetFOBValue,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($NetFOBValue_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($NetFOBValue_job/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                            <td width="80">4</td>
                            <td width="380" align="left" style="font-weight:bold"><b>Less: Cost of Material & Services (5+6+7+8+9) </b></td>
                            <?
							$Less_Cost_Material_Services=array_sum($summary_data[yarn_cost])+array_sum($summary_data[fabric_cost])+array_sum($conv_data[amount])+$summary_data[trims_cost]+$summary_data[emb_cost]+$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];

							$Less_Cost_Material_Services_job=$summary_data[yarn_cost_job]+$summary_data[fabric_cost_job]+$summary_data[conver_cost_job]+$summary_data[trims_cost_job]+$summary_data[emb_cost_job]+$summary_data[OtherDirectExpenses_job];
							//echo array_sum($summary_data[yarn_cost]).'*'.array_sum($conv_data[amount]).'*'.$summary_data[trims_cost].'*'.$summary_data[emb_cost].'*'.$summary_data[lab_test].'*'.$summary_data[inspection].'*'.$summary_data[freight].'*'.$summary_data[currier_pre_cost].'*'.$summary_data[certificate_pre_cost].'*'.$summary_data[wash_cost];
							?>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Less_Cost_Material_Services,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Less_Cost_Material_Services_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Less_Cost_Material_Services_job/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                         <tr>
                            <td width="80" rowspan="2">5</td>
                            <td width="380" align="left" style=" padding-left:100px;font-weight:bold">Fabric Purchase Cost</td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format(array_sum($summary_data[fabric_cost]),4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data[fabric_cost_job],4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[fabric_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                            <td width="380" align="left" style=" padding-left:100px;font-weight:bold">Yarn Cost</td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format(array_sum($summary_data[yarn_cost]),4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data[yarn_cost_job],4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[yarn_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>                        </tr>
                        <tr>
                            <td width="80" valign="top">6</td>
                            <td width="380" align="left" style=" padding-left:100px">
                            <table>
                                <tr>
                                <td width="180" style="font-weight:bold">Conversion Cost</td>
                                </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                            <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            <td width="180" align="left"><? echo $conversion_cost_head_array[$conv_data[cons_process][$key]]; ?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="100" align="right" valign="top">
							<? //echo number_format(array_sum($conv_data[amount]),4); ?>

                             <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format(array_sum($conv_data[amount]),4); ?></td>
                            </tr>
                            </table>

                            <table border="1" class="rpt_table" rules="all">
                            <?

							foreach($conv_data[cons_process] as $key => $value){
                             /*$conv_ids=rtrim($conv_data[conv_id][$key],',');
							  $conv_ids=array_unique(explode(",", $conv_ids));
							  foreach($conv_ids as $con_id)
							  {
								 $conv_data[amount][$key];
							  }*/

							 ?>
                            <tr>

                            <td width="100" align="right"><? echo number_format(array_sum($conv_data[amount]),4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="100" align="right" valign="top">

                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[conver_cost_job],4); ?></td>
                            </tr>
                            </table>

                            <table border="1" class="rpt_table" rules="all">
                            <?
								$tot_dye_chemi_process_amount=0;$tot_yarn_dye_process_amount=$tot_aop_process_amount=0;
								foreach($conv_data[cons_process] as $key => $value)
								{

								$job_no=rtrim($conv_data[job_no][$key],',');
								 $job_nos=array_unique(explode(",",$job_no));
								  $process_amount=0;
								 foreach($job_nos as $jno)
								 {
									 if($key==101)
									 {
										  $tot_dye_chemi_process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
									 }
									 else if($key==30)
									 {

										 //echo "dddd".array_sum($conv_process_amount_arr[$jno][$key]);
										  $tot_yarn_dye_process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
									 }
									  else if($key==35) //AOP
									 {

										 //echo "dddd".array_sum($conv_process_amount_arr[$jno][$key]);
										  $tot_aop_process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
									 }
									 $process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
								 }
							 	 //echo $job_no.'fd'.$key.',';
							?>
                            <tr>

                            <td width="100" align="right"><? echo number_format($process_amount,4);//number_format($conv_data[amount_job][$key],4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="180" align="right" valign="top">

                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format(($summary_data[conver_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            </table>

                            <table border="1" class="rpt_table" rules="all">
                            <?
							foreach($conv_data[cons_process] as $key => $value)
							{
								$job_no=rtrim($conv_data[job_no][$key],',');
								 $job_nos=array_unique(explode(",",$job_no));
								  $process_amount=0;
								 foreach($job_nos as $jno)
								 {
									 $process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
								 }
							?>
                            <tr>

                            <td width="180" align="right"><? echo number_format(($process_amount/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                        </tr>

                        <tr>
                            <td width="80">7</td>
                            <td width="380" align="left" style=" padding-left:100px;font-weight:bold" ><b>Trim Cost </b></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data[trims_cost],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[trims_cost_job],4)?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[trims_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                            <td width="80">8</td>
                            <td width="380" align="left" style=" padding-left:100px;font-weight:bold"><b>Embelishment Cost </b></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data[emb_cost],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[emb_cost_job],4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[emb_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>

                            <td width="80" valign="top">9</td>
                            <td width="380" align="left" style=" padding-left:100px">

                            <table>
                            <tr>
                            <td width="180" style="font-weight:bold">Other Direct Expenses</td>

                            </tr>
                            </table>


                            <table border="1" class="rpt_table" rules="all">

                            <tr>
                            <td width="180" align="left">Lab Test</td>

                            </tr>

                            <tr>
                            <td width="180" align="left">Inspection</td>

                            </tr>

                            <tr>
                            <td width="180" align="left">Freight Cost</td>
                            </tr>
                            <tr>
                            <td width="180" align="left">Courier Cost</td>
                            </tr>
                             <tr>
                            <td width="180" align="left">Certificate Cost</td>
                            </tr>
                            <tr>
                            <td width="180" align="left">Garments Wash Cost</td>
                            </tr>
                            </table>
                            </td>
                            <td width="100" align="right" valign="top">
                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[OtherDirectExpenses],4); ?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                                <tr>
                                <td width="100" align="right"><? echo number_format($summary_data[lab_test],4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><? echo number_format($summary_data[inspection],4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><? echo number_format($summary_data[freight],4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><? echo number_format($summary_data[currier_pre_cost],4);?></td>
                                </tr>
                                 <tr>
                                <td width="100" align="right"><? echo number_format($summary_data[certificate_pre_cost],4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><? echo number_format($summary_data[wash_cost],4);?></td>
                                </tr>
                            </table>
                            </td>


                            <td width="100" align="right" valign="top">
							<? //echo number_format($summary_data[OtherDirectExpenses_job],4); ?>
                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data[OtherDirectExpenses_job],4); ?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">

                            <tr>

                            <td width="100" align="right"><? echo number_format($summary_data[lab_test_job],4);?></td>
                            </tr>

                            <tr>

                            <td width="100" align="right"><? echo number_format($summary_data[inspection_job],4);?></td>
                            </tr>

                            <tr>

                            <td width="100" align="right"><? echo number_format($summary_data[freight_job],4);?></td>
                            </tr>

                            <tr>

                            <td width="100" align="right"><? echo number_format($summary_data[currier_pre_cost_job],4);?></td>
                            </tr>

                             <tr>

                            <td width="100" align="right"><? echo number_format($summary_data[certificate_pre_cost_job],4);?></td>
                            </tr>

                            <tr>

                            <td width="100" align="right"><? echo number_format($summary_data[wash_cost_job],4);?></td>
                            </tr>

                            </table>
                            </td>
                            <td width="180" align="right" valign="top">

                            <table>
                            <tr>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($summary_data[OtherDirectExpenses_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">

                            <tr>

                            <td width="180" align="right"><? echo number_format(($summary_data[lab_test_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>

                            <tr>

                            <td width="180" align="right"><? echo number_format(($summary_data[inspection_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>

                            <tr>

                            <td width="180" align="right"><? echo number_format(($summary_data[freight_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>

                            <tr>

                            <td width="180" align="right"><? echo number_format(($summary_data[currier_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>

                             <tr>

                            <td width="180" align="right"><? echo number_format(($summary_data[certificate_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>

                            <tr>

                            <td width="180" align="right"><? echo number_format(($summary_data[wash_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>

                            </table>
                            </td>
                        </tr>
                         <tr>
                            <td width="80">10</td>
                            <td width="380" align="left" style="font-weight:bold">Contributions/Value Additions (3-4)</td>
                            <?
							$Contribution_Margin=$NetFOBValue-$Less_Cost_Material_Services;
							$Contribution_Margin_job=$NetFOBValue_job-$Less_Cost_Material_Services_job;
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($Contribution_Margin,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Contribution_Margin_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Contribution_Margin_job/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                            <td width="80">11</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: CM Cost </td>
                            <td width="100" align="right"><? echo number_format($summary_data[cm_cost],4); ?> </td>
                            <td width="100" align="right"><? echo number_format($summary_data[cm_cost_job],4); ?></td>
                            <td width="180" align="right"><? echo number_format(($summary_data[cm_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                            <td width="80">12</td>
                            <td width="380" align="left" style="font-weight:bold">Gross Profit (10-11)</td>
                            <?
							$Gross_Profit=$Contribution_Margin-$summary_data[cm_cost];
							$Gross_Profit_job=$Contribution_Margin_job-$summary_data[cm_cost_job];
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($Gross_Profit,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Gross_Profit_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Gross_Profit_job/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>

                        <tr>
                            <td width="80">13</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Commercial Cost</td>

                            <td width="100" align="right"> <? echo number_format( $summary_data[comm_cost],4); ?></td>
                            <td width="100" align="right"><? echo number_format( $summary_data[comm_cost_job],4); ?></td>
                            <td width="180" align="right"><? echo number_format(($summary_data[comm_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                            <td width="80">14</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Operating Expensees</td>

                            <td width="100" align="right"><? echo number_format( $summary_data[common_oh],4); ?> </td>
                            <td width="100" align="right"><? echo number_format( $summary_data[common_oh_job],4); ?> </td>
                            <td width="180" align="right"><? echo number_format(($summary_data[common_oh_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>

                        <tr >
                            <td width="80">15</td>
                            <td width="380" align="left" style="font-weight:bold">Operating Profit/ Loss (12-(13+14))</td>
                            <?
							$OperatingProfitLoss=$Gross_Profit-($summary_data[comm_cost]+$summary_data[common_oh]);
							$OperatingProfitLoss_job=$Gross_Profit_job-($summary_data[comm_cost_job]+$summary_data[common_oh_job]);
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($OperatingProfitLoss,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($OperatingProfitLoss_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($OperatingProfitLoss_job/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                         <tr>
                            <td width="80">16</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Depreciation & Amortization </td>

                            <td width="100" align="right"> <? echo number_format( $summary_data[depr_amor_pre_cost],4); ?></td>
                            <td width="100" align="right"><? echo number_format( $summary_data[depr_amor_pre_cost_job],4); ?></td>
                            <td width="180" align="right"><? echo number_format(($summary_data[depr_amor_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>

                        <tr>
							<?
							//echo $financial_para[interest_expense].'DFDDDD';
                            $interest_expense=$NetFOBValue*$financial_para[interest_expense]/100;
                            $income_tax=$NetFOBValue*$financial_para[income_tax]/100;
                            $interest_expense_job=$NetFOBValue_job*$financial_para[interest_expense]/100;
                            $income_tax_job=$NetFOBValue_job*$financial_para[income_tax]/100;
                            ?>
                            <td width="80">17</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Interest </td>

                            <td width="100" align="right"> <? echo number_format( $interest_expense,4); ?></td>
                            <td width="100" align="right"><? echo number_format( $interest_expense_job,4); ?></td>
                            <td width="180" align="right"><? echo number_format(($interest_expense_job/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                         <tr>
                            <td width="80">18</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: Income Tax</td>

                            <td width="100" align="right"> <? echo number_format( $income_tax,4); ?></td>
                            <td width="100" align="right"><? echo number_format( $income_tax_job,4); ?></td>
                            <td width="180" align="right"><? echo number_format(($income_tax_job/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                            <?
							$Netprofit=$OperatingProfitLoss-($summary_data[depr_amor_pre_cost]+$interest_expense+$income_tax);
							$Netprofit_job=$OperatingProfitLoss_job-($summary_data[depr_amor_pre_cost_job]+$interest_expense_job+$income_tax_job);
							?>
                            <td width="80">19</td>
                            <td width="380" align="left" style="font-weight:bold">Net Profit (15-(16+17+18))</td>

                            <td width="100" align="right" style="font-weight:bold"><? echo number_format( $Netprofit,4); ?> </td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format( $Netprofit_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? echo number_format(($Netprofit_job/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                          <tr>
                          <td colspan="5">&nbsp;  </td>
                          </tr>

                        </tr>
                        </table>
                        <br/>
                         <table width="450px" style="margin-left:10px; float:left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  rules="all">
                         <caption> <strong><? echo 'Budget Summary';?></strong></caption>

                             <tr class="form_caption">
                                <td  align="center"><strong>Particulars</strong></td>
                                <td  align="center"><strong>Total Value</strong></td>
                                <td  align="center"><strong>%</strong></td>
                            </tr>
                            <tr>
                                <td width="230" align="left">Yarn Cost</td>
                                <td width="100" align="right"><? echo number_format( $summary_data[yarn_cost_job],4); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($summary_data[yarn_cost_job]/$summary_data[price_dzn_job])*100,4);

								$tot_percent=(($summary_data[yarn_cost_job]/$summary_data[price_dzn_job])*100)+(($tot_dye_chemi_process_amount/$summary_data[price_dzn_job])*100)+(($tot_yarn_dye_process_amount/$summary_data[price_dzn_job])*100)+($summary_data[trims_cost_job]/$summary_data[price_dzn_job]*100)+($summary_data[comm_cost_job]/$summary_data[price_dzn_job]*100)+($summary_data[emb_cost_job]/$summary_data[price_dzn_job])*100+($summary_data[lab_test_job]/$summary_data[price_dzn_job])*100+($summary_data[fabric_cost_job]/$summary_data[price_dzn_job])*100;
								 ?></td>
                            </tr>
                             <tr>
                                <td width="230" align="left">Fabric Purchase</td>
                                <td width="100" align="right"><? echo number_format( $summary_data[fabric_cost_job],4); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($summary_data[fabric_cost_job]/$summary_data[price_dzn_job])*100,4);
								 ?></td>
                            </tr>
                             <tr>
                                <td width="230" align="left">Dyes & Chemical</td>
                                <td width="100" align="right"><? echo number_format( $tot_dye_chemi_process_amount,4); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($tot_dye_chemi_process_amount/$summary_data[price_dzn_job])*100,4); ?></td>
                            </tr>
                             <tr>
                                <td width="230" align="left">Y/D.</td>
                                <td width="100" align="right"><? echo number_format( $tot_yarn_dye_process_amount,4); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($tot_yarn_dye_process_amount/$summary_data[price_dzn_job])*100,4); ?></td>
                            </tr>
                            <tr>
                                <td width="230" align="left">AOP</td>
                                <td width="100" align="right"><? echo number_format($tot_aop_process_amount,4); ?></td>
                                <td width="100"  align="right"><? echo number_format(($tot_aop_process_amount/$summary_data[price_dzn_job])*100,4); ?></td>
                            </tr>
                            <tr>
                                <td width="230" align="left">Accessories</td>
                                <td width="100" align="right"><? echo number_format($summary_data[trims_cost_job],4); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($summary_data[trims_cost_job]/$summary_data[price_dzn_job])*100,4); ?></td>
                            </tr>
                            <tr>
                                <td width="230" align="left">Commercial</td>
                                <td width="100" align="right"><? echo number_format($summary_data[comm_cost_job],4); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($summary_data[comm_cost_job]/$summary_data[price_dzn_job])*100,4); ?></td>
                            </tr>
                             <tr>
                                <td width="130" align="left">Print / Emb</td>
                                <td width="100" align="right"><? echo number_format($summary_data[emb_cost_job],4); ?></td>
                                <td width="100"  align="right"><? echo number_format(($summary_data[emb_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                             <tr>
                                <td width="230" align="left">Test</td>
                                <td width="100" align="right"><? echo number_format($summary_data[lab_test_job],4); ?></td>
                                <td width="100"  align="right"><? echo number_format(($summary_data[lab_test_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                             <tr>
                                <td width="230" align="left">Total for BTB</td>
                                <td width="100" align="right"><?
									$total_btb=$summary_data[lab_test_job]+$summary_data[emb_cost_job]+$summary_data[comm_cost_job]+$summary_data[trims_cost_job]+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount+$summary_data[yarn_cost_job]+$summary_data[fabric_cost_job];
								 echo number_format($total_btb,4);?></td>
                                <td width="100"  align="right"><? echo number_format(($total_btb/$summary_data[price_dzn_job])*100,4);//echo number_format($tot_percent,4);?></td>
                            </tr>
                             <tr>
                                <td width="230" align="left">CM for Fabrics (Knitting & Dyeing Charge)</td>
                                <td width="100" align="right" title="Tot Conversion Cost-(Y/D+Dye & Chemical)"><?
								$tot_cm_for_fab_cost=$summary_data[conver_cost_job]-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
								echo number_format($tot_cm_for_fab_cost,4);?></td>
                                <td width="100"  align="right"><? echo number_format(($tot_cm_for_fab_cost/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                             <tr>

                                <td width="230" align="left">CM for Garments</td>
                                <td width="100" align="right" title="Net FOB Value-Tot CM Fab Cost Cost-Total BTB"><?
								   $total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);
								 echo number_format($total_cm_for_gmt,4);?></td>
                                <td width="100"  align="right"><? echo number_format(($total_cm_for_gmt/$NetFOBValue_job)*100,4);?></td>
                            </tr>
                             <tr>
                                <td width="230" align="left">Net Order Value</td>
                                <td width="100" align="right"><? echo number_format($NetFOBValue_job,4);?></td>
                                <td width="100"  align="right"><? echo number_format(($NetFOBValue_job/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>

                        </table>

                    </div><!--Fabtic Details End-->
                     <br/>
                    	<div style="width:850px">
				    <?
                      		echo signature_table(109, $cbo_company_name, "850px");
                        ?>
                        </div>
             </div>

		<?
	}
	else if($reporttype==3) //Budget Button 3/4
	{



			 $sql="select a.job_no_prefix_num as job_prefix,a.job_no,a.quotation_id, a.gmts_item_id,a.avg_unit_price,a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.file_no,b.po_quantity,b.plan_cut,b.po_total_price, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond $file_year_cond order  by b.id "; 
			// echo $sql;
			$sql_po_result=sql_select($sql);
			$all_po_id="";$all_job_style="";$all_file_no="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer="";
			$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=$tot_plan_cut_qty_pcs=$tot_plan_cut_qty=0;
			//echo $buyer_name;die;
			$fabric_detail_arr=array();
			foreach($sql_po_result as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
				if($all_file_no=="") $all_file_no=$row[csf("file_no")]; else $all_file_no.=",".$row[csf("file_no")];
				if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
				if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
				if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
				if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
				if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];
				if($all_job_style=="") $all_job_style=$row[csf("job_no")]; else $all_job_style.=",".$row[csf("job_no")];

				$pub_shipment_date=$row[csf("pub_shipment_date")];
				$style_wise_arr[$row[csf("style_ref_no")]]['buyer_name']=$row[csf("buyer_name")];
				$style_wise_arr[$row[csf("style_ref_no")]]['quotation_id']=$row[csf("quotation_id")];
				$style_wise_arr[$row[csf("style_ref_no")]]['gmts_item_id']=$row[csf("gmts_item_id")];
				$style_wise_arr[$row[csf("style_ref_no")]]['shipment_date'].=$pub_shipment_date.',';
				$style_wise_arr[$row[csf("style_ref_no")]]['job_no'].=$row[csf("job_no")].',';
				$style_wise_arr[$row[csf("style_ref_no")]]['job_nos']=$row[csf("job_no")];
				$style_wise_arr[$row[csf("style_ref_no")]]['qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];

				$style_wise_arr[$row[csf("style_ref_no")]]['qty']+=$row[csf('po_quantity')];
				$style_wise_arr[$row[csf("style_ref_no")]]['avg_rate']=$row[csf('avg_unit_price')];
				$style_wise_arr[$row[csf("style_ref_no")]]['po_amount']+=$row[csf('po_total_price')];
				$job_wise_arr[$row[csf("job_no")]]['po_amount']+=$row[csf('po_total_price')];
				$po_wise_arr[$row[csf("po_id")]]['plan_cut']+=$row[csf('plan_cut')]*$row[csf('ratio')];

				$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
				$total_order_qty+=$row[csf('po_quantity')];
				$tot_plan_cut_qty_pcs+=$row[csf('plan_cut')]*$row[csf('ratio')];
				$tot_plan_cut_qty+=$row[csf('plan_cut')];
				$total_unit_price+=$row[csf('unit_price')];
				$total_fob_value+=$row[csf('po_total_price')];

			}
			//print_r($style_wise_arr);
			$total_job_unit_price=($total_fob_value/$total_order_qty);
			//echo $all_job;die;
			$all_job_no=array_unique(explode(",",$all_full_job));
			$all_job_style=array_unique(explode(",",$all_job_style));
			$all_jobs="";
			foreach($all_job_no as $jno)
			{
					if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
			}

			// echo $file_no;die;
			$file_no=implode(",",array_unique(explode(",",$file_no)));
				// echo $file_no;die;
			$condition= new condition();
			$condition->company_name("=$cbo_company_name");
			if(str_replace("'","",$cbo_buyer_name)>0)
			{
				$condition->buyer_name("=$cbo_buyer_name");
			}
			if($txt_order_id!='' || $txt_order_id!=0)
			{
				$condition->po_id("in($txt_order_id)");
			}
			if($txt_file_po_id!=0 || $txt_file_po_id!='')
			{
				$condition->po_id("in($txt_file_po_id)");
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
			$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

			$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$trim= new trims($condition);
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			$other= new other($condition);
			$other_cost=$other->getAmountArray_by_job();
			$commercial= new commercial($condition);
			$commision= new commision($condition);
			//echo $commision->getQuery();
			$commision_cost_arr=$commision->getAmountArray_by_job();
			$commision_item_cost_arr=$commision->getAmountArray_by_jobAndItemid();
			$po_qty=0;
			$po_plun_cut_qty=0;
			$total_set_qnty=0;
			 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty
			 from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id
			 and a.job_no in(".$all_jobs.")  and b.id in($all_po_id)  $file_no_cond $file_po_idCond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
			  order by b.id";
			 // echo  $sql_po;
			$sql_po_data=sql_select($sql_po);
			foreach($sql_po_data as $sql_po_row)
			{
			$po_qty+=$sql_po_row[csf('order_quantity')];
			$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
			$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
			//$po_plun_cut_qty[$sql_po_row[csf('id')]]+=$sql_po_row[csf('plan_cut_qnty')]/$sql_po_row[csf('total_set_qnty')];
			}

		$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
		foreach($pre_cost as $row)
		{
			$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
			$costing_date_arr[$row[csf('job_no')]]=$row[csf('costing_date')];
		}

		$company_con="";
		if($cbo_company_name!=0) $company_con="and company_id=$cbo_company_name";
		else if($cbo_style_owner!=0) $company_con="and company_id=$cbo_style_owner";
		//else if($cbo_company_name==0) $company_con=$cbo_company_name;

		$sql_std_para=sql_select("select id,interest_expense,income_tax,cost_per_minute,applying_period_date, applying_period_to_date,operating_expn from lib_standard_cm_entry where  status_active=1 and	is_deleted=0 and cost_per_minute>0 $company_con order by id desc");
			$financial_para_arr=array();
			foreach($sql_std_para as $row )
			{
				$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
						$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
						$financial_para_arr[$newdate]['operating_expn']=$row[csf('operating_expn')];
					if($row[csf("income_tax")]>0)
					{
						$financial_para_arr[$newdate]['income_tax']=$row[csf('income_tax')];
					}
					if($row[csf("interest_expense")]>0)
					{
						$financial_para_arr[$newdate]['interest_expense']=$row[csf('interest_expense')];
					}
				}

				$cost_per_minute=$row[csf("cost_per_minute")];

			}
			$summary_data=array();
			$comm_sql_data=sql_select("SELECT commission_amount from wo_pre_cost_commiss_cost_dtls where status_active=1 and is_deleted=0 and job_no in(".$all_jobs.") and particulars_id=1");

			$summary_data['commission']=0;

			foreach($comm_sql_data as $row){
				$summary_data['commission']+=$row[csf('commission_amount')];
			}



		    $sql_new = "select job_no,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,studio_cost,studio_percent, total_cost , total_cost_percent, price_dzn,price_dzn_percent, margin_dzn,margin_dzn_percent, price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche,design_cost,design_percent
			from wo_pre_cost_dtls
			where job_no in(".$all_jobs.") and status_active=1 and is_deleted=0";
			$data_array_new=sql_select($sql_new);
			

            foreach( $data_array_new as $row_new )
			{

					$costing_per=$costing_per_arr[$row_new[csf('job_no')]];

				if($costing_per==1)
				{
				$order_price_per_dzn=12;
				$costing_for=" DZN";
				}
				else if($costing_per==2)
				{
					$order_price_per_dzn=1;
					$costing_for=" PCS";
				}
				else if($costing_per==3)
				{
					$order_price_per_dzn=24;
					$costing_for=" 2 DZN";
				}
				else if($costing_per==4)
				{
					$order_price_per_dzn=36;
					$costing_for=" 3 DZN";
				}
				else if($costing_per==5)
				{
					$order_price_per_dzn=48;
					$costing_for=" 4 DZN";
				}
				$order_job_qnty=$row[csf("job_quantity")];
				$avg_unit_price=$row[csf("avg_unit_price")];


				$summary_data['price_dzn']+=$row_new[csf("price_dzn")];
				$summary_data['price_dzn_job']+=$job_wise_arr[$row_new[csf("job_no")]]['po_amount'];//($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
			    
				$summary_data['trims_cost']+=$row_new[csf("trims_cost")];
				$summary_data['emb_cost']+=$row_new[csf("embel_cost")];

				$summary_data['lab_test']+=$row_new[csf("lab_test")];
				$summary_data['lab_test_job']+=$other_cost[$row_new[csf("job_no")]]['lab_test'];

				$summary_data['design_cost']+=$row_new[csf("design_cost")];
				$summary_data['design_cost_job']+=$other_cost[$row_new[csf("job_no")]]['design_cost'];

				$summary_data['inspection']+=$row_new[csf("inspection")];
				$summary_data['inspection_job']+=$other_cost[$row_new[csf("job_no")]]['inspection'];

				$summary_data['freight']+=$row_new[csf("freight")];
				$summary_data['freight_job']+=$other_cost[$row_new[csf("job_no")]]['freight'];

				$summary_data['currier_pre_cost']+=$row_new[csf("currier_pre_cost")];
				$summary_data['currier_pre_cost_job']+=$other_cost[$row_new[csf("job_no")]]['currier_pre_cost'];

				$summary_data['certificate_pre_cost']+=$row_new[csf("certificate_pre_cost")];
				$summary_data['certificate_pre_cost_job']+=$other_cost[$row_new[csf("job_no")]]['certificate_pre_cost'];
				//echo $other_cost[$row_new[csf("job_no")]]['studio_cost'].',';
				$summary_data['studio_cost']+=$row_new[csf("studio_cost")];
				$summary_data['studio_cost_job']+=$other_cost[$row_new[csf("job_no")]]['studio_cost'];
				$studio_job_cost_arr[$row_new[csf("job_no")]]['studio_cost']=$row_new[csf("studio_percent")];
				$studio_job_cost_arr[$row_new[csf("job_no")]]['common_oh']=$row_new[csf("common_oh_percent")];

				$summary_data['wash_cost']+=$row_new[csf("wash_cost")];

				$summary_data['OtherDirectExpenses']+=$row_new[csf("lab_test")]+$row_new[csf("design_cost")]+$row_new[csf("inspection")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")]+$row_new[csf("studio_cost")];

				$summary_data['OtherDirectExpenses_job']+=$summary_data['lab_test_job']+$summary_data['design_cost_job']+$summary_data['inspection_job']+$summary_data['currier_pre_cost_job']+$summary_data['certificate_pre_cost_job'];

				$summary_data['cm_cost']+=$row_new[csf("cm_cost")];
				$summary_data['cm_cost_job']+=$other_cost[$row_new[csf("job_no")]]['cm_cost'];

				$summary_data['comm_cost']+=$row_new[csf("comm_cost")];
				//$summary_data[comm_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("comm_cost")];

				$summary_data['common_oh']+=$row_new[csf("common_oh")];
				$summary_data['common_oh_job']+=$other_cost[$row_new[csf("job_no")]]['common_oh'];
				$summary_data['depr_amor_pre_cost']+=$row_new[csf("depr_amor_pre_cost")];
				$summary_data['depr_amor_pre_cost_job']+=$other_cost[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
			}

			//Fabric =====================
			$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			$fabric_amount_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$sql_fabric = "select a.uom, sum(a.amount) as amount,b.po_break_down_id  from wo_pre_cost_fabric_cost_dtls a ,wo_pre_cos_fab_co_avg_con_dtls b where a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no in(".$all_jobs.") and b.po_break_down_id in($all_po_id) and a.fabric_source in (2,3) and a.status_active=1 group by a.uom,b.po_break_down_id";
			$data_arr_fabric=sql_select($sql_fabric);
			foreach($data_arr_fabric as $fab_row)
			{
				$plan_cut=$po_wise_arr[$fab_row[csf("po_break_down_id")]]['plan_cut'];
				$tot_fabric_amount=$fabric_amount_arr['knit']['grey'][$fab_row[csf("po_break_down_id")]][$fab_row[csf("uom")]];
				$tot_fabric_amount2=$fabric_amount_arr['woven']['grey'][$fab_row[csf("po_break_down_id")]][$fab_row[csf("uom")]];
				$tot_fabric_amountFb=$tot_fabric_amount+$tot_fabric_amount2;
				$dzn_fabric_amount=($tot_fabric_amountFb/$plan_cut)*12;
				$summary_data['fabric_cost']=$dzn_fabric_amount;
				$summary_data['fabric_cost_job']+=$tot_fabric_amount2+$tot_fabric_amount;
			}


			$totYarn=0;
			$YarnData=array();
			$yarn_data_array=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();

			 $sql_yarn="SELECT f.id as yarn_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,count_id,copm_one_id,percent_one,color,type_id   from wo_pre_cost_fab_yarn_cost_dtls f where   f.job_no in(".$all_jobs.") and f.is_deleted=0 and f.status_active=1  order by f.id";
			$data_arr_yarn=sql_select($sql_yarn);
			foreach($data_arr_yarn as $yarn_row)
			{
				$yarnrate=$yarn_row[csf("rate")];
				$summary_data['yarn_cost'][$yarn_row[csf("yarn_id")]]+=$yarn_row[csf("amount")];
				$summary_data['yarn_cost_job']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];

				$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."_".$yarn_row[csf("color")]."_".$yarnrate."'";
				$YarnData[$index]['qty']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
				$YarnData[$index]['amount']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
				$YarnData[$index]['dznqty']+=$yarn_row[csf("cons_qnty")];
				$YarnData[$index]['dznamount']+=$yarn_row[csf("amount")];
				$totYarn+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
			}

			// Yarn End
			// Conversion
			$totConv=0;
			$ConvData=array();
			$conv_data=array();
			$conv_amount_arr=$conversion->getAmountArray_by_conversionid();
			$conv_qty_arr=$conversion->getQtyArray_by_conversionid();
			$conv_process_amount_arr=$conversion->getAmountArray_by_jobAndProcess();
			//print_r($conv_amount_arr);
			$sql_conv = "select a.id as con_id, a.fabric_description as fabric_description_id, a.job_no, a.cons_process, a.req_qnty,a.avg_req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.uom  from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id where a.job_no in(".$all_jobs.") and a.status_active=1 ";
			$data_arr_conv=sql_select($sql_conv);
			foreach($data_arr_conv as $conv_row){
				$convamount=$conv_amount_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
				$convQty=$conv_qty_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
				$conv_data['cons_process'][$conv_row[csf('cons_process')]]=$conv_row[csf('cons_process')];
				//$conv_data[conv_id][$conv_row[csf('cons_process')]].=$conv_row[csf('con_id')].',';
				$conv_data['job_no'][$conv_row[csf('cons_process')]].=$conv_row[csf('job_no')].',';
				$conv_data['amount'][$conv_row[csf('cons_process')]]+=$conv_row[csf('amount')];
				$conv_data['amount_job'][$conv_row[csf('con_id')]]+=$convamount;
				$summary_data['conver_cost_job']+=$convamount;
				//echo $conv_row[csf('amount')].',';
				$index=$conv_row[csf('con_id')];
				$ConvData[$index]['item_descrition']=$body_part[$conv_row[csf("body_part_id")]].", ".$color_type[$conv_row[csf("color_type_id")]].", ".$conv_row[csf("fabric_description")];
				$ConvData[$index]['cons_process']=$conv_row[csf("cons_process")];
				$ConvData[$index]['req_qnty']+=$conv_row[csf("req_qnty")];
				$ConvData[$index]['uom']=$conv_row[csf("uom")];
				$ConvData[$index]['charge_unit']=$conv_row[csf("charge_unit")];
				$ConvData[$index]['amount']+=$conv_row[csf("amount")];
				$ConvData[$index]['tot_req_qnty']+=$convQty;
				$ConvData[$index]['tot_amount']+=$convamount;
				$totConv+=$conv_row[csf("req_qnty")];
			}

			$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active	from wo_pre_cost_trim_cost_dtls	where job_no in(".$all_jobs.") and status_active=1  order by id";
			$data_array_trim=sql_select($sql_trim);
			$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
			$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
			$totTrim=0;
			$TrimData=array();
			foreach( $data_array_trim as $row_trim )
			{
				$trim_qty=$trim_qty_arr[$row_trim[csf("id")]];
				$trim_amount=$trim_amount_arr[$row_trim[csf("id")]];
				$summary_data['trims_cost_job']+=$trim_amount;
				$TrimData[$row_trim[csf('id')]]['trim_group']=$row_trim[csf('trim_group')];
				$TrimData[$row_trim[csf('id')]]['description']=$row_trim[csf('description')];
				$TrimData[$row_trim[csf('id')]]['brand_sup_ref']=$row_trim[csf('brand_sup_ref')];
				$TrimData[$row_trim[csf('id')]]['remark']=$row_trim[csf('remark')];
				$TrimData[$row_trim[csf('id')]]['cons_uom']=$row_trim[csf('cons_uom')];
				$TrimData[$row_trim[csf('id')]]['cons_dzn_gmts']=$row_trim[csf('cons_dzn_gmts')];
				$TrimData[$row_trim[csf('id')]]['rate']=$row_trim[csf('rate')];
				$TrimData[$row_trim[csf('id')]]['amount']+=$row_trim[csf('amount')];
				$TrimData[$row_trim[csf('id')]]['apvl_req']=$row_trim[csf('apvl_req')];
				$TrimData[$row_trim[csf('id')]]['nominated_supp']=$row_trim[csf('nominated_supp')];
				$TrimData[$row_trim[csf('id')]]['tot_cons']+=$trim_qty;
				$TrimData[$row_trim[csf('id')]]['tot_amount']+=$trim_amount;
				$totTrim+=$row_trim[csf('cons_dzn_gmts')];
			}
				$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no in(".$all_jobs.") and emb_name in(1,2,4,5) and status_active=1";
				$data_array=sql_select($sql);
				$emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
				$emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();
				$totEmb=0;
				$EmbData=array();
				foreach( $data_array as $row )
				{
					$embqty=$emblishment_qty[$row[csf("job_no")]][$row[csf("id")]];
					$embamount=$emblishment_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data['emb_cost_job']+=$embamount;
					$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
					$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
					$EmbData[$row[csf("id")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
					$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$EmbData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$EmbData[$row[csf("id")]]['tot_cons']+=$embqty;
					$EmbData[$row[csf("id")]]['tot_amount']+=$embamount;
					$totEmb+=$row[csf("cons_dzn_gmts")];
				}

				//End Emb cost Cost part report here -------------------------------------------
				//Wash cost Cost part report here -------------------------------------------
				$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no in(".$all_jobs.") and emb_name =3 and status_active=1";
				$data_array=sql_select($sql);
				$wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
				$wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
				foreach( $data_array as $row )
				{
					$washqty=$wash_qty[$row[csf("job_no")]][$row[csf("id")]];
					$washamount=$wash_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data['wash_cost_job']+=$washamount;
					$summary_data['OtherDirectExpenses_job']+=$washamount;
					$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
					$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
					$EmbData[$row[csf("id")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
					$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$EmbData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$EmbData[$row[csf("id")]]['tot_cons']+=$washqty;
					$EmbData[$row[csf("id")]]['tot_amount']+=$washamount;
					$totEmb+=$row[csf("cons_dzn_gmts")];
				}

				//End Wash cost Cost part report here -------------------------------------------
				//Commision cost Cost part report here -------------------------------------------
				$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where commision_rate>0 and job_no in(".$all_jobs.") and status_active=1";
				$data_array=sql_select($sql);
				$commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
				$totCommi=$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
				$CommiData=array();
				foreach( $data_array as $row )
				{
					$commisionamount=$commision_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data['commission_job']+=$commisionamount;
					$CommiData[$row[csf("id")]]['particulars_id']=$row[csf("particulars_id")];
					$CommiData[$row[csf("id")]]['commission_base_id']=$row[csf("commission_base_id")];
					$CommiData[$row[csf("id")]]['commision_rate']=$row[csf("commision_rate")];
					$CommiData[$row[csf("id")]]['commission_amount']+=$row[csf("commission_amount")];
					$CommiData[$row[csf("id")]]['tot_commission_amount']+=$commisionamount;

					$totCommi+=$row[csf("commission_amount")];
					if($row[csf("particulars_id")]==1) //Foreign
					{
						//$foreign_percent_rate+=$row[csf("commision_rate")];
						$CommiData_foreign_cost+=$commision_item_cost_arr[$row[csf("job_no")]][1];
						$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
						$commision_job_cost_arr[$row[csf("job_no")]]+=$commision_item_cost_arr[$row[csf("job_no")]][1];

					}
					else
					{
						$CommiData_lc_cost+=$commision_item_cost_arr[$row[csf("job_no")]][2];

						$local_dzn_commission_amount+=$row[csf("commission_amount")];
						$commision_local_job_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
					}
				}
				$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls  where rate>0 and job_no in(".$all_jobs.") and status_active=1 ";
				//echo $sql;
				$data_array=sql_select($sql);
				$commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
				$commarcial_item_amount=$commercial->getAmountArray_by_jobAndItemid();
				$totCommar=$commer_lc_cost=$commer_without_lc_cost=0;
				$CommarData=array();
				foreach( $data_array as $row )
				{
					$item_id=$row[csf("item_id")];
					$commarcialamount=$commarcial_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data['comm_cost_job']+=$commarcialamount;
					$CommarData[$row[csf("id")]]['item_id']=$row[csf("item_id")];
					$CommarData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$CommarData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$CommarData[$row[csf("id")]]['tot_amount']+=$commarcialamount;
					$totCommar+=$row[csf("amount")];
					if($item_id==1)//LC
					{
						$commer_lc_cost+=$commarcial_item_amount[$row[csf("job_no")]][$item_id];
						$tot_lc_dzn_Commar+=$row[csf("amount")];
						$commarcial_job_amount[$row[csf("job_no")]]+=$commarcial_item_amount[$row[csf("job_no")]][$item_id];

					}
					else
					{
						$commer_without_lc_cost+=$commarcial_item_amount[$row[csf("job_no")]][$item_id];
						$totCommar+=$row[csf("amount")];
						$tot_without_lc_dzn_Commar+=$row[csf("amount")];

					}
				}
				$summary_data['OtherDirectExpenses_job']+=$CommiData_lc_cost;
				$summary_data['OtherDirectExpenses']+=$local_dzn_commission_amount;
				$summary_data['comm_cost']=0;
				$summary_data['comm_cost']=$tot_without_lc_dzn_Commar;


		?>

        <style>
		#page_sign_td{margin-top:260px; position:absolute;

		}
		#page_sign_td2{ margin-top:-70px; position:absolute;

		}

		</style>
       <div style="width:100%">
             <table width="850px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="9" align="center">
                    <?
						$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'","image_location");
					?>
                    <img  src='../../<? echo $image_location; ?>' height='70' align="left" />
                    <strong style=" font-size:18px"><? echo $company_library[$cbo_company_name];?></strong><br>
                    <strong style="font-size: 16px"><? echo $report_title; ?></strong>
                    
                    </td>
                </tr>
                
            </table>
            <table width="760px"  style="margin-left:10px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <caption> <strong style="float:left">SUMMARY:</strong>  </caption>
                    <thead>

                     	<th width="100"><b>Buyer</b> </th>
                        <th width="120"><b>Item</b> </th>
                        <th width="100"><b>Ship Date</b> </th>
                        <th width="120"><b>Style</b> </th>
                        <th width="70"><b>Job No</b> </th>
						<th width="60"><b>Quot. ID</b> </th>
                        <th width="80"><b>Qty.</b> </th>
                        <th width="80"><b>Qty.(PCS)</b> </th>
                        <th width="60"><b>FOB</b> </th>
                        <th width="80"><b>Total Amount</b> </th>


                    </thead>
                    <?
					$k=1;$total_po_qty=$total_po_pcs_qty=$total_po_amount=$avg_rate=0;

					$all_last_shipdates='';
                    foreach($style_wise_arr as $style_key=>$val)
					{
						 $gmts_item_id=$val[('gmts_item_id')];
						 $shipment_date=rtrim($val[('shipment_date')],',');
						  $shipment_dates=array_unique(explode(",",$shipment_date));
						   $last_shipmentdates=max($shipment_dates);
						   $all_last_shipdates.=$last_shipmentdates.',';
							$gmts_item=''; $gmts_item_id=explode(",",$gmts_item_id);
							foreach($gmts_item_id as $item_id)
							{
								//echo $item_id;
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}
						$job_nos=rtrim($val[('job_no')],',');
						$job_noss=array_unique(explode(",",$job_nos));
						$all_job_nos="";
						foreach($job_noss as $job)
						{
							$job_data=explode("-",$job);
							$job_no_exp=$job_data[1].'-'.$job_data[2];
							if($all_job_nos=="") $all_job_nos=$job_no_exp;else $all_job_nos.=",".$job_no_exp;
						}

					?>
                	<tr>
                        <td width="100"><p> <? echo $buyer_arr[$val[('buyer_name')]];?></p></td>
                        <td  width="120"><p>  <? echo $gmts_item;?></p></td>
                        <td width="100"><p> <? echo change_date_format($last_shipmentdates);?></p></td>
                        <td width="120"><p> <? echo $style_key;?></p></td>
                        <td width="70"><p> <? echo $all_job_nos;?></p></td>
						<td width="60"><p> <? echo $val[('quotation_id')];?></p></td>

                        <td width="80" align="right"><p> <? echo number_format($val[('qty')],0);?></p></td>
                        <td  width="80" align="right">  <? echo  number_format($val[('qty_pcs')],0);?></td>
                        <td width="60" align="right"><p> <? echo number_format($val[('avg_rate')],4);?></p></td>
                        <td width="80" align="right"><p> <? echo number_format($val[('po_amount')],2);?></p></td>

                    </tr>
                    <?
					$k++;
					$total_po_qty+=$val[('qty')];
					$total_po_pcs_qty+=$val[('qty_pcs')];
					$total_po_amount+=$val[('po_amount')];
					$total_po_amount_arr[$val[('job_nos')]]+=$val[('po_amount')];
					$avg_rate+=$val[('avg_rate')];
					}
					//print_r($total_po_amount_arr);
					?>
                    <tfoot>
                     <tr>
                    <td colspan="3" align="right">  <b>Qty DZN </b></td>
                    <td  align="right" title="Plan Cut DZN=<? echo number_format($tot_plan_cut_qty_pcs/12,2);?>"> &nbsp; <? $total_po_pcs_qty_dzn=$total_po_pcs_qty/12;echo number_format($total_po_pcs_qty_dzn,2);?></td>
					<td align="right">&nbsp; </td>
					<td align="right">  <b>Total</b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_po_qty,0);?> </b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_po_pcs_qty,0);?> </b></td>
					<td align="right"><b> &nbsp;<?=fn_number_format($avg_rate,2)?></b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_po_amount,2);?> </b></td>
                    </tr>

                    <tr>
                    <td colspan="3" align="right">  <b>Last Shipment Date </b></td>
                    <td align="right"> &nbsp; <?
					$all_last_ship_dates=rtrim($all_last_shipdates,',');
					 $all_last_ship_dates=array_unique(explode(",",$all_last_ship_dates));
					 $last_shipment_dates=max($all_last_ship_dates);
					 echo change_date_format($last_shipment_dates);?></td>
					  <td align="right"> <b>&nbsp;</b></td>
					 <td align="right"> <b>&nbsp;</b></td>
					 <td align="right" colspan="3"><b>Foreign Commission &nbsp; <? $foreign_percent_rate=($CommiData_foreign_cost/$total_po_amount)*100;
					 echo number_format($foreign_percent_rate,2).'%';?> </b></td>

					<td align="right"><b> &nbsp; <?
					//$pre_foreign_commission_per=$CommiData_lc_cost;
					echo number_format($CommiData_foreign_cost,2);?> </b></td>

                    </tr>
                    <tr>
                    <td  align="right" colspan="3" title="Total PO Value-Commission">  <b>Maximum BB LC-70% </b></td>
                    <td align="right" title="Commission=<? echo $summary_data['commission_job'];?>"> &nbsp; <?
					$net_fob_value=$total_po_amount-$summary_data['commission_job'];
					$gross_fob_value_job=$total_po_amount;

					 echo number_format(($net_fob_value*70)/100,2);?></td>
					  <td align="right"> <b>&nbsp;</b></td>
					  <td align="right"> <b>&nbsp;</b></td>
					 <td align="right" colspan="3"><b>Freight Cost &nbsp; <?
					 $freight_percent_rate=($summary_data['freight_job']/$total_po_amount)*100;
					 echo number_format($freight_percent_rate,2).'%';?> </b></td>
					<td align="right"><b> &nbsp; <?
					$pre_freight_cost_per=$summary_data['freight_job'];//($total_po_amount*$summary_data[freight])/100;
					echo number_format($pre_freight_cost_per,2);?> </b></td>
                    </tr>
					  <tr>
					  	 <td align="right" colspan="6"> </td>
						  <td align="right" colspan="3" title="<? echo $CommiData_foreign_cost;?>"><b>LC Cost &nbsp; <?
						  $commar_rate_percent=($commer_lc_cost/$total_po_amount)*100;
						  echo number_format($commar_rate_percent,2).'%';?> </b></td>
						<td align="right"><b> &nbsp; <?
						$pre_commercial_per=$commer_lc_cost;//($total_po_amount*$commar_rate_percent)/100;
						echo number_format($pre_commercial_per,2);
						$tot_sum_amount=$total_po_amount-($CommiData_foreign_cost+$pre_freight_cost_per+$pre_commercial_per);
						?> </b></td>
					  </tr>
                    <tr>
                    <td colspan="3" align="right"> <b style="float:left"> File No: &nbsp; <? echo implode(",",array_unique(explode(",",$all_file_no))); ?> </b></td>
                    <td colspan="6" align="right"> <b> Total </b></td>

                    <td align="right"> <b> <? echo number_format($tot_sum_amount,2);?> </b></td>
                    </tr>
                    </tfoot>
           </table>


          		 <?
          		 /*echo '<pre>';
          		 print_r($total_po_amount_arr); die;*/
				 $all_jobs_no="";$tot_operating_expense=$tot_sum_amount_calc=$total_job_income_tax_val=$total_job_amount_cal=$total_job_interest_exp_val=$tot_studio_job_wise_cost=$total_job_commision_local_val=0;
					foreach($all_job_style as $jno)
					{
							$costing_date=$costing_date_arr[$jno];
							$total_po_amount_cal=$total_po_amount_arr[$jno];

							$commision_local=$commision_local_job_cost_arr[$jno];
							$commision_job_cost_foreign=$commision_job_cost_arr[$jno];
							$commarcial_job_lc=0;
							$commarcial_job_lc=$commarcial_job_amount[$jno];
							$other_job=$other_cost[$jno]['freight'];
							$studio_job_cost=$studio_job_cost_arr[$jno]['studio_cost'];
							$common_oh=$studio_job_cost_arr[$jno]['common_oh'];
							$tot_sum_amount_job_calc=$total_po_amount_cal-($commision_job_cost_foreign+$commarcial_job_lc+$other_job);
							
							$costing_date=change_date_format($costing_date,'','',1);
							$operating_expn=$financial_para_arr[$costing_date]['operating_expn'];

							$tot_operating_expense+=($tot_sum_amount_job_calc*$operating_expn)/100;
							$tot_studio_job_wise_cost+=($tot_sum_amount_job_calc*$studio_job_cost)/100;
							$income_tax=$financial_para_arr[$costing_date]['income_tax'];
							$interest_expense=$financial_para_arr[$costing_date]['interest_expense'];
							$total_job_income_tax_val+=($total_po_amount_cal*$income_tax)/100;
							$total_job_interest_exp_val+=($total_po_amount_cal*$interest_expense)/100;
							$total_job_commision_local_val+=($tot_sum_amount_job_calc*$commision_local)/100;
							$total_job_amount_cal+=$total_po_amount_arr[$jno];
							$total_job_net_amount_cal+=$tot_sum_amount_job_calc;
					}

					$tot_income_tax_dzn=($total_job_income_tax_val/$total_job_amount_cal)*12;
					$tot_interest_exp_dzn=($total_job_interest_exp_val/$total_job_amount_cal)*12;
					$tot_studio_dzn=($tot_studio_job_wise_cost/$total_job_net_amount_cal)*12;
					$tot_commision_local_dzn=($total_job_commision_local_val/$total_job_net_amount_cal)*12;

					$summary_data['OtherDirectExpenses_job']+=$tot_studio_job_wise_cost;
		 		  ?>

          		 <div style="margin-left:10px">
           <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:650px;text-align:center; margin-left:10px" rules="all">
         			 <caption> <strong>  Order Profitability </strong> </caption>
                        <thead>
                            <th width="40">Line Items</th>
                            <th width="270">Particulars</th>
                            <th width="100">Amount (USD) / DZN</th>
                            <th width="100">Total Value</th>
                            <th width="50">%</th>
                        </thead>
                        <tr>
                            <td width="40">1</td>
                            <td width="270" align="left" style="font-weight:bold">Net FOB Value</td>
                            <td width="100" align="right" style="font-weight:bold"><?
							$summary_data['price_dzn']=0; $summary_data['price_dzn']=($tot_sum_amount/$total_po_pcs_qty)*12;
							echo number_format($summary_data['price_dzn'],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? $summary_data['price_dzn_job']=$tot_sum_amount; echo number_format($summary_data['price_dzn_job'],2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['price_dzn_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                            <?
							$NetFOBValue=$summary_data['price_dzn'];
							$NetFOBValue_job=$summary_data['price_dzn_job'];
							?>
                        <tr>
                            <td width="40">2</td>
                            <td width="270" align="left" style="font-weight:bold"><b>Total Cost of Material & Services (3+4+5+6+7) </b></td>
                            <?
							$summary_data['yarn_cost']=0;$conv_data['amount']=0;
							$summary_data['yarn_cost']=$summary_data['yarn_cost_job']/$total_po_pcs_qty_dzn;
							$conv_data['amount']=$summary_data['conver_cost_job']/$total_po_pcs_qty_dzn;
							

							$summary_data['studio_cost_job']=0;
							$summary_data['studio_cost_job']=$tot_studio_job_wise_cost;
							$summary_data['OtherDirectExpenses_job']=0;
							$summary_data['OtherDirectExpenses_job']=$summary_data['lab_test_job']+$summary_data['design_cost_job']+$summary_data['inspection_job']+$summary_data['currier_pre_cost_job']+$CommiData_lc_cost+$summary_data['certificate_pre_cost_job']+$summary_data['studio_cost_job']+$summary_data['wash_cost_job'];

							$summary_data['OtherDirectExpenses_job_amountdzn']=$summary_data['lab_test_job']+$summary_data['inspection_job']+$summary_data['currier_pre_cost_job']+$summary_data['certificate_pre_cost_job']+$summary_data['wash_cost_job'];
							
							$Less_Cost_Material_Services=array_sum($summary_data['yarn_cost'])+array_sum($summary_data['fabric_cost'])+array_sum($conv_data['amount'])+$summary_data['trims_cost']+$summary_data['emb_cost']+$summary_data['lab_test']+$summary_data['inspection']+$summary_data['currier_pre_cost']+$summary_data['certificate_pre_cost']+$summary_data['wash_cost']+$summary_data['commission'];
							


							$Less_Cost_Material_Services_job=$summary_data['fabric_cost_job']+$summary_data['yarn_cost_job']+$summary_data['conver_cost_job']+$summary_data['trims_cost_job']+$summary_data['emb_cost_job']+$summary_data['OtherDirectExpenses_job'];

							$Less_Cost_Material_Services_job2=$summary_data['fabric_cost_job']+$summary_data['trims_cost_job']+$summary_data['emb_cost_job']+$summary_data['OtherDirectExpenses_job_amountdzn'];
							//echo $conv_data['amount']; die;
							$tot_Less_Cost_Material_Services_job=($Less_Cost_Material_Services_job2/$total_po_pcs_qty_dzn)+$summary_data['yarn_cost']+$conv_data['amount']+$summary_data['commission']+$summary_data['studio_cost']+$summary_data['design_cost'];
							?>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($tot_Less_Cost_Material_Services_job,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Less_Cost_Material_Services_job,2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($Less_Cost_Material_Services_job/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                         <tr>
                            <td width="40" rowspan="2">3</td>
                            <td width="270" align="left" style=" padding-left:100px;font-weight:bold">Fabric Purchase Cost</td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data['fabric_cost_job']/$total_po_pcs_qty_dzn,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data['fabric_cost_job'],2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['fabric_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                        <tr>
                            <td width="270" align="left" style=" padding-left:100px;font-weight:bold">Yarn Cost</td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['yarn_cost'],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data['yarn_cost_job'],2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['yarn_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>                        </tr>
                        <tr>
                            <td width="40" valign="top">4</td>
                            <td width="270" align="left" style=" padding-left:100px">
                            <table>
                                <tr>
                                <td width="180" style="font-weight:bold">Conversion Cost</td>
                                </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                            <? foreach($conv_data['cons_process'] as $key => $value){ ?>
                            <tr>
                            <td width="180" align="left"><? echo $conversion_cost_head_array[$conv_data['cons_process'][$key]]; ?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="100" align="right" valign="top">
							<? //echo number_format(array_sum($conv_data[amount]),4); ?>

                             <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($conv_data['amount'],4);//number_format(array_sum($conv_data[amount]),4);//number_format($summary_data[conver_cost_job]/$total_po_pcs_qty_dzn,4); ?></td>
                            </tr>
                            </table>

                            <table border="1" class="rpt_table" rules="all">
                            <?

							foreach($conv_data['cons_process'] as $key => $value){
								$job_no=rtrim($conv_data['job_no'][$key],',');
								 $job_nos=array_unique(explode(",",$job_no));
								  $process_amount_dzn=0;
								 foreach($job_nos as $jno)
								 {

									 $process_amount_dzn+=array_sum($conv_process_amount_arr[$jno][$key]);
								 }
							 ?>
                            <tr>

                            <td width="100" align="right"><? echo  number_format($process_amount_dzn/$total_po_pcs_qty_dzn,4);//number_format($conv_data[amount][$key],4);//number_format($process_amount_dzn/$total_po_pcs_qty_dzn,4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="100" align="right" valign="top">

                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['conver_cost_job'],2); ?></td>
                            </tr>
                            </table>

                            <table border="1" class="rpt_table" rules="all">
                            <?
								$tot_dye_chemi_process_amount=0;$tot_yarn_dye_process_amount=$tot_aop_process_amount=0;
								foreach($conv_data['cons_process'] as $key => $value)
								{

								$job_no=rtrim($conv_data['job_no'][$key],',');
								 $job_nos=array_unique(explode(",",$job_no));
								  $process_amount=0;
								 foreach($job_nos as $jno)
								 {
									 if($key==101)
									 {
										  $tot_dye_chemi_process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
									 }
									 else if($key==30)
									 {
										 //echo "dddd".array_sum($conv_process_amount_arr[$jno][$key]);
										  $tot_yarn_dye_process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
									 }
									 else if($key==35) //AOP
									 {

										 //echo "dddd".array_sum($conv_process_amount_arr[$jno][$key]);
										  $tot_aop_process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
									 }
									 $process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
								 }
							 	 //echo $job_no.'fd'.$key.',';
							?>
                            <tr>

                            <td width="100" align="right"><? echo number_format($process_amount,2);//number_format($conv_data[amount_job][$key],4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="50" align="right" valign="top">

                            <table>
                            <tr>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['conver_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
                            </table>

                            <table border="1" class="rpt_table" rules="all">
                            <?
							foreach($conv_data['cons_process'] as $key => $value)
							{
								$job_no=rtrim($conv_data['job_no'][$key],',');
								 $job_nos=array_unique(explode(",",$job_no));
								  $process_amount=0;
								 foreach($job_nos as $jno)
								 {
									 $process_amount+=array_sum($conv_process_amount_arr[$jno][$key]);
								 }
							?>
                            <tr>

                            <td width="50" align="right"><? echo number_format(($process_amount/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                        </tr>

                        <tr>
                            <td width="40">5</td>
                            <td width="270" align="left" style=" padding-left:100px;font-weight:bold" ><b>Trim Cost </b></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data['trims_cost_job']/$total_po_pcs_qty_dzn,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['trims_cost_job'],2)?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['trims_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                        <tr>
                            <td width="40">6</td>
                            <td width="270" align="left" style=" padding-left:100px;font-weight:bold"><b>Embelishment Cost </b></td>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($summary_data['emb_cost_job']/$total_po_pcs_qty_dzn,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['emb_cost_job'],2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['emb_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                        <tr>
                            <td width="40" valign="top">7</td>
                            <td width="270" align="left" style=" padding-left:100px">
                            <table>
                            <tr>
                            <td width="180" style="font-weight:bold">Other Direct Expenses</td>
                            </tr>
                            </table>

                            <table border="1" class="rpt_table" rules="all">
                            <tr>
                            <td width="180" align="left">Lab Test</td>
                            </tr>
                            <tr>
                            <td width="180" align="left">Inspection</td>
                            </tr>
							<tr>
                            <td width="180" align="left">Design Cost</td>
                            </tr>
                           <!-- <tr>
                            <td width="180" align="left">Freight Cost</td>
                            </tr>-->
                            <tr>
                            <td width="180" align="left">Courier Cost</td>
                            </tr>
                             <tr>
                            <td width="180" align="left">Commission Cost(Local)</td>
                            </tr>
                             <tr>
                            <td width="180" align="left">Certificate Cost</td>
                            </tr>
                            <tr>
                            <td width="180" title="From Pre Cost Studio Precent(Studio Precent*FOB Value/100) " align="left">S. Cost</td>
                            </tr>
                            <tr>
                            <td width="180" align="left">Garments Wash Cost</td>
                            </tr>
                            </table>
                            </td>
                            <td width="100" align="right" valign="top">
                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold">
							<?
							echo number_format(($summary_data['OtherDirectExpenses_job_amountdzn']/$total_po_pcs_qty_dzn)+$summary_data['commission']+$summary_data['studio_cost']+$summary_data['design_cost'],4); ?>
							</td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                                <tr>
                                <td width="100" align="right"><? echo number_format($summary_data['lab_test_job']/$total_po_pcs_qty_dzn,4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><? echo number_format($summary_data['inspection_job']/$total_po_pcs_qty_dzn,4);?></td>
                                </tr>
								<tr>
                                <td width="100" align="right"><? echo number_format($summary_data['design_cost'],4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><? echo number_format($summary_data['currier_pre_cost_job']/$total_po_pcs_qty_dzn,4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><?
								 	echo $summary_data['commission'];
								?></td>
                                </tr>
                                 <tr>
                                <td width="100" align="right"><?
								$summary_data['certificate_pre_cost']=0;$summary_data['certificate_pre_cost']=$summary_data['certificate_pre_cost_job']/$total_po_pcs_qty_dzn;
								echo number_format($summary_data['certificate_pre_cost'],4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><?
								// $summary_data[studio_cost]=0;$summary_data[studio_cost]=$summary_data[studio_cost_job]/$total_po_pcs_qty_dzn;
								echo number_format($summary_data['studio_cost'],4);?></td>
                                </tr>
                                <tr>
                                <td width="100" align="right"><? $summary_data['wash_cost']=0;$summary_data['wash_cost']=$summary_data['wash_cost_job']/$total_po_pcs_qty_dzn;
								echo number_format($summary_data['wash_cost'],4);?></td>
                                </tr>
                            </table>
                            </td>


                            <td width="100" align="right" valign="top">
							<? //echo number_format($summary_data[OtherDirectExpenses_job],4); ?>
                            <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['OtherDirectExpenses_job'],2); ?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">

                            <tr>

                            <td width="100" align="right"><? echo number_format($summary_data['lab_test_job'],2);?></td>
                            </tr>
                            <tr>
                            <td width="100" align="right"><? echo number_format($summary_data['inspection_job'],2);?></td>
                            </tr>
							<tr>
                            <td width="100" align="right"><? echo number_format($summary_data['design_cost_job'],2);?></td>
                            </tr>
                           <!-- <tr>
                            <td width="100" align="right"><? //echo number_format($summary_data[freight_job],4);?></td>
                            </tr>-->
                            <tr>
                            <td width="100" align="right"><? echo number_format($summary_data['currier_pre_cost_job'],2);?></td>
                            </tr>
                            <tr>
                            <td width="100" align="right" title="job_commi_cal=tot_po_value-(freign_comi_job+Commercial_job_lc+Freight_job); (job_commi_cal*commision_local_rate)/100"><?
							echo number_format($CommiData_lc_cost,2);
							?></td>
                            </tr>

                             <tr>
                            <td width="100" align="right"><? echo number_format($summary_data['certificate_pre_cost_job'],2);?></td>
                            </tr>
                            <tr>
                            <td width="100" align="right"><?
							echo number_format($summary_data['studio_cost_job'],2);?></td>
                            </tr>

                            <tr>

                            <td width="100" align="right"><? echo number_format($summary_data['wash_cost_job'],2);?></td>
                            </tr>

                            </table>
                            </td>
                            <td width="50" align="right" valign="top">

                            <table>
                            <tr>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['OtherDirectExpenses_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">

                            <tr>

                            <td width="50" align="right"><? echo number_format(($summary_data['lab_test_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
							
                            <tr>

                            <td width="50" align="right"><? echo number_format(($summary_data['inspection_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
							<tr>

							<td width="50" align="right"><? echo number_format(($summary_data['design_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
							</tr>
                            <!--<tr>
                            <td width="50" align="right"><? //echo number_format(($summary_data[freight_job]/$summary_data[price_dzn_job])*100,2);?></td>
                            </tr>-->
                            <tr>
                            <td width="50" align="right"><? echo number_format(($summary_data['currier_pre_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
                             <tr>
                            <td width="50" align="right"><? echo number_format(($summary_data['commission_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
                            <tr>
                            <td width="50" align="right"><? echo number_format(($summary_data['certificate_pre_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
                            <tr>
                            <td width="50" align="right"><? echo number_format(($summary_data['studio_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
                            <tr>
                            <td width="50" align="right"><? echo number_format(($summary_data['wash_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>

                            </table>
                            </td>
                        </tr>
                         <tr>
                            <td width="40">8</td>
                            <td width="270" align="left" style="font-weight:bold">Contributions/Value Additions (1-2)</td>
                            <?
							$Contribution_Margin=$NetFOBValue-$Less_Cost_Material_Services;
							$Contribution_Margin_job=$NetFOBValue_job-$Less_Cost_Material_Services_job;
							$tot_contribution_val_dzn=$summary_data['price_dzn']-$tot_Less_Cost_Material_Services_job;
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($tot_contribution_val_dzn,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Contribution_Margin_job,2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($Contribution_Margin_job/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                        <tr>
                            <td width="40">9</td>
                            <td width="270" align="left" style=" padding-left:15px">Less: CM Cost </td>
                            <td width="100" align="right"><? echo number_format($summary_data['cm_cost_job']/$total_po_pcs_qty_dzn,4); ?> </td>
                            <td width="100" align="right"><? echo number_format($summary_data['cm_cost_job'],2); ?></td>
                            <td width="50" align="right"><? echo number_format(($summary_data['cm_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                        <tr>
                            <td width="40">10</td>
                            <td width="270" align="left" style="font-weight:bold">Gross Profit (8-9)</td>
                            <?
							//$Gross_Profit=$Contribution_Margin-$summary_data[cm_cost];
							$Gross_Profit_job=$Contribution_Margin_job-$summary_data['cm_cost_job'];
							$Gross_Profit=$Gross_Profit_job/$total_po_pcs_qty_dzn;
							$gross_profit_dzn=$tot_contribution_val_dzn-($summary_data['cm_cost_job']/$total_po_pcs_qty_dzn);
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($gross_profit_dzn,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($Gross_Profit_job,2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($Gross_Profit_job/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>

                        <tr>
                            <td width="40">11</td>
                            <td width="270" align="left" style=" padding-left:15px">Less: Commercial (Without LC Cost) </td>

                            <td width="100" align="right"> <?
							$summary_data['comm_cost_job']=$commer_without_lc_cost;
							$summary_data['comm_cost']=0;
							$summary_data['comm_cost']=$summary_data['comm_cost_job']/$total_po_pcs_qty_dzn;
							if($summary_data['comm_cost_job']>0) echo number_format( $summary_data['comm_cost'],4);
							else echo "0.00";
							?></td>
                            <td width="100" align="right"><? echo number_format( $summary_data['comm_cost_job'],2); ?></td>
                            <td width="50" align="right"><? echo number_format(($summary_data['comm_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                        <tr>
                            <td width="40">12</td>
                            <td width="270" align="left" style=" padding-left:15px">Less: Operating Expensees/Maintance</td>

                            <td width="100" align="right"><?
							// $summary_data[common_oh_job]=0;
							// $summary_data[common_oh_job]=$tot_operating_expense;
							// $summary_data[common_oh]=0;
							// $summary_data[common_oh]=$summary_data[common_oh_job]/$total_po_pcs_qty_dzn;
							 echo number_format($summary_data['common_oh'],4);; ?> </td>
                             <td width="100" align="right"><? echo number_format($summary_data['common_oh_job'],2); ?> </td>
                            <td width="50" align="right"><? echo number_format(($summary_data['common_oh_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>

                        <tr >
                            <td width="40">13</td>
                            <td width="270" align="left" style="font-weight:bold">Operating Profit/ Loss (10-(11+12))</td>
                            <?
							//$OperatingProfitLoss=$Gross_Profit-($summary_data[comm_cost]+$summary_data[common_oh]);
							$OperatingProfitLoss_job=$Gross_Profit_job-($summary_data['comm_cost_job']+$summary_data['common_oh_job']);
							$OperatingProfitLoss_dzn=$gross_profit_dzn-($summary_data['comm_cost']+$summary_data['common_oh']);
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? echo number_format($OperatingProfitLoss_dzn,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format($OperatingProfitLoss_job,2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($OperatingProfitLoss_job/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                         <tr>
                            <td width="40">14</td>
                            <td width="270" align="left" style=" padding-left:15px">Less: Depreciation & Amortization </td>

                            <td width="100" align="right"> <? $summary_data['depr_amor_pre_cost']=0;
							$summary_data['depr_amor_pre_cost']=$summary_data['depr_amor_pre_cost_job']/$total_po_pcs_qty_dzn;
							echo number_format( $summary_data['depr_amor_pre_cost'],4); ?></td>
                            <td width="100" align="right"><? echo number_format( $summary_data['depr_amor_pre_cost_job'],2); ?></td>
                            <td width="50" align="right"><? echo number_format(($summary_data['depr_amor_pre_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>

                        <tr>
							<?
							//echo $NetFOBValue.'=='.$interest_expense;
                            $interest_expense=$interest_expense_job/$total_po_pcs_qty_dzn;//$tot_interest_exp_dzn;//$NetFOBValue*$interest_expense/100;
                            $income_tax=$income_tax_job/$total_po_pcs_qty_dzn;//$tot_income_tax_dzn;//$NetFOBValue*$income_taxes/100;
                            $interest_expense_job=$total_job_interest_exp_val;//$NetFOBValue_job*$interest_expense/100;
                            $income_tax_job=$total_job_income_tax_val;//$NetFOBValue_job*$income_taxes/100;
                            ?>
                            <td width="40">15</td>
                            <td width="270" align="left" style=" padding-left:15px">Less: Interest </td>

                            <td width="100" align="right"> <? echo number_format( $interest_expense,4); ?></td>
                            <td width="100" align="right"><? echo number_format( $interest_expense_job,2); ?></td>
                            <td width="50" align="right"><? echo number_format(($interest_expense_job/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                         <tr>
                            <td width="40">16</td>
                            <td width="270" align="left" style=" padding-left:15px">Less: Income Tax</td>

                            <td width="100" align="right"> <? echo number_format( $income_tax,4); ?></td>
                            <td width="100" align="right"><? echo number_format( $income_tax_job,2); ?></td>
                            <td width="50" align="right"><? echo number_format(($income_tax_job/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                        <tr>
                            <?
							$Netprofit=$OperatingProfitLoss-($summary_data['depr_amor_pre_cost']+$interest_expense+$income_tax);
							$Netprofit_job=$OperatingProfitLoss_job-($summary_data['depr_amor_pre_cost_job']+$interest_expense_job+$income_tax_job);
							$Netprofit_dzn=$OperatingProfitLoss_dzn-($summary_data['depr_amor_pre_cost']+$interest_expense+$income_tax);
							?>
                            <td width="40">17</td>
                            <td width="270" align="left" style="font-weight:bold">Net Profit (13-(14+15+16))</td>

                            <td width="100" align="right" style="font-weight:bold"><? echo number_format( $Netprofit_dzn,4); ?> </td>
                            <td width="100" align="right" style="font-weight:bold"><? echo number_format( $Netprofit_job,2); ?></td>
                            <td width="50" align="right" style="font-weight:bold"><? echo number_format(($Netprofit_job/$summary_data['price_dzn_job'])*100,2);?></td>
                        </tr>
                        <tr>
                          <td colspan="5">&nbsp;  </td>
                        </tr>
                        </table>
                        <br/>
                         <div>
                         <table width="470px" style="margin-left:10px; float:left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  rules="all">
                         <caption> <strong><? echo 'Budget Summary';?></strong></caption>
                          <thead>
                                <th  align="center"><strong>Line</strong></th>
								<th  align="center"><strong>Particulars</strong></th>
                                <th  align="center"><strong>Total Value</strong></th>
                                <th  align="center"><strong>%</strong></th>
                           </thead>
                            <tr>

								<td width="20" align="center">1</td>
								<td width="230" align="left">Yarn Cost</td>
                                <td width="100" align="right"><? echo number_format( $summary_data['yarn_cost_job'],2); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($summary_data['yarn_cost_job']/$summary_data['price_dzn_job'])*100,2);

								$tot_percent=(($summary_data['yarn_cost_job']/$summary_data['price_dzn_job'])*100)+(($tot_dye_chemi_process_amount/$summary_data['price_dzn_job'])*100)+(($tot_yarn_dye_process_amount/$summary_data['price_dzn_job'])*100)+($summary_data['trims_cost_job']/$summary_data['price_dzn_job']*100)+($summary_data['comm_cost_job']/$summary_data['price_dzn_job']*100)+($summary_data['emb_cost_job']/$summary_data['price_dzn_job'])*100+($summary_data['lab_test_job']/$summary_data['price_dzn_job'])*100+($summary_data['studio_cost_job']/$summary_data['price_dzn_job'])*100;

								 ?></td>
                            </tr>
                             <tr>
                                <td width="20" align="center">2</td>
								<td width="230" align="left">Fabric Purchase</td>
                                <td width="100" align="right"><? echo number_format( $summary_data['fabric_cost_job'],2); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($summary_data['fabric_cost_job']/$summary_data['price_dzn_job'])*100,2);
								 ?></td>
                            </tr>
                             <tr>
                                <td width="20" align="center">3</td>
								<td width="230" align="left">Dyes & Chemical</td>
                                <td width="100" align="right"><? echo number_format( $tot_dye_chemi_process_amount,2); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($tot_dye_chemi_process_amount/$summary_data['price_dzn_job'])*100,2); ?></td>
                            </tr>
                             <tr>
                                <td width="20" align="center">4</td>
								<td width="230" align="left">Y/D.</td>
                                <td width="100" align="right"><? echo number_format( $tot_yarn_dye_process_amount,2); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($tot_yarn_dye_process_amount/$summary_data['price_dzn_job'])*100,2); ?></td>
                            </tr>
                             <tr>
                                <td width="20" align="center">5</td>
								<td width="230" align="left">AOP</td>
                                <td width="100" align="right"><? echo number_format($tot_aop_process_amount,2); ?></td>
                                <td width="100"  align="right"><? echo number_format(($tot_aop_process_amount/$summary_data['price_dzn_job'])*100,2); ?></td>
                            </tr>
                            <tr>
                                <td width="20" align="center">6</td>
								<td width="230" align="left">Accessories</td>
                                <td width="100" align="right"><? echo number_format($summary_data['trims_cost_job'],2); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($summary_data['trims_cost_job']/$summary_data['price_dzn_job'])*100,2); ?></td>
                            </tr>
                            <tr>
                               <td width="20" align="center">7</td>
							    <td width="230" align="left">Commercial</td>
                                <td width="100" align="right"><? echo number_format($summary_data['comm_cost_job'],2); ?></td>
                                <td width="100"  align="right"><? echo number_format( ($summary_data['comm_cost_job']/$summary_data['price_dzn_job'])*100,2); ?></td>
                            </tr>
                             <tr>
                                <td width="20" align="center">8</td>
								<td width="130" align="left">Print/Emb/GMT Dye/Wash</td>
                                <td width="100" align="right"><? $tot_emblish_cost=$summary_data['wash_cost_job']+$summary_data['emb_cost_job'];echo number_format($tot_emblish_cost,2); ?></td>
                                <td width="100"  align="right"><? echo number_format(($tot_emblish_cost/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
                             <tr>
                               <td width="20" align="center">9</td>
							    <td width="230" align="left">Lab Test</td>
                                <td width="100" align="right"><? echo number_format($summary_data['lab_test_job'],2); ?></td>
                                <td width="100"  align="right"><? echo number_format(($summary_data['lab_test_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
							 <tr>
                                <td width="20" align="center">10</td>
								<td width="230" align="left">Operating Expensees/Maintance</td>
                                <td width="100" align="right" title="From Libray(Operatin Expen*Net FOB Value/100) "><? echo number_format($summary_data['common_oh_job'],2); ?></td>
                                <td width="100"  align="right"><? echo number_format(($summary_data['common_oh_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
							 <tr>
                               <td width="20" align="center">11</td>
							    <td width="230" align="left">S. Cost</td>
                                <td width="100" align="right" title="From Pre Cost Studio Precent(Studio Precent*Net FOB Value/100) "><? echo number_format($summary_data['studio_cost_job'],2); ?></td>
                                <td width="100"  align="right"><? echo number_format(($summary_data['studio_cost_job']/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>
							 <tr>
                                <td width="20" align="center">12</td>
								<td width="230" align="left">Inspection/Courier/Commission/Certificate</td>
                                <td width="100" align="right" title=""><?
								$tot_inspect_cour_certi_cost=$summary_data['inspection_job']+$summary_data['currier_pre_cost_job']+$summary_data['certificate_pre_cost_job']+$summary_data['commission_job'];
								echo number_format($tot_inspect_cour_certi_cost,2);?></td>
                                <td width="100"  align="right"><? echo number_format(($tot_inspect_cour_certi_cost/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>

                             <tr>
                                <td width="20" align="center">13</td>
								<td width="230" align="left">Total for BTB</td>
                                <td width="100" align="right"><?
									$total_btb=$summary_data['fabric_cost_job']+$summary_data['lab_test_job']+$tot_emblish_cost+$summary_data['comm_cost_job']+$summary_data['trims_cost_job']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data['yarn_cost_job']+$tot_aop_process_amount+$summary_data['common_oh_job']+$summary_data['studio_cost_job']+$tot_inspect_cour_certi_cost;
								 echo number_format($total_btb,2);?></td>
                                <td width="100"  align="right"><? echo number_format(($total_btb/$summary_data['price_dzn_job'])*100,2);//echo number_format($tot_percent,4);?></td>
                            </tr>
                             <tr>
                                <td width="20" align="center">14</td>
								<td width="230" align="left">CM for Fabrics (Knitting & Dyeing Charge)</td>
                                <td width="100" align="right" title="Tot Conversion Cost-(Y/D+Dye & Chemical)"><?
								$tot_cm_for_fab_cost=$summary_data['conver_cost_job']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
								echo number_format($tot_cm_for_fab_cost,2);?></td>
                                <td width="100"  align="right"><? echo number_format(($tot_cm_for_fab_cost/$summary_data['price_dzn_job'])*100,2);?></td>
                            </tr>


                             <tr>
                             <?

							 $total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);?>
                               <td width="20" align="center">15</td>
							    <td width="230" align="left" title="POQTY=<? echo $total_po_pcs_qty;?>">CM for Garments&nbsp;(CM Dzn=<? echo number_format(($total_cm_for_gmt/$total_po_pcs_qty)*12,3);?>)</td>
                                <td width="100" align="right" title="Gross FOB Value-Tot CM Fab Cost Cost-Total BTB-Inspect-Freight-Courier-Certificate-Commission"><?
								 echo number_format($total_cm_for_gmt,2);?></td>
                                <td width="100"  align="right"><? echo number_format(($total_cm_for_gmt/$NetFOBValue_job)*100,2);?></td>
                            </tr>
                             <tr style="background-color:#CCC">
							 <td width="20" align="center">16</td>
                                <td width="230" align="left"><b>Net Order Value</b></td>
                                <td width="100" align="right"><b><? echo number_format($NetFOBValue_job,2);?></b></td>
                                <td width="100"  align="right"><b><? echo number_format(($NetFOBValue_job/$summary_data['price_dzn_job'])*100,2);?></b></td>
                            </tr>
                             <tr>
                             <td style=" border-bottom:hidden; border-right:hidden;border-left:hidden;" colspan="4">&nbsp;  </td>
                             </tr>
                        </table>
                        </div>
                       <div id="" style="width:870px;">
                         <?
                                    echo signature_table(109, $cbo_company_name, "870px");
                                ?>
                          </div>
                     </div>
                </div> <!--Detail Part-->
             </div>
		<?
	}
	else if($reporttype==4) //Budget Button 3/4
	{

			$sql="select a.job_no_prefix_num as job_prefix,a.quotation_id,a.job_no, a.gmts_item_id,a.avg_unit_price,a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.file_no,b.po_quantity,b.plan_cut,b.po_total_price, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond $file_year_cond order  by b.id ";
			//echo $sql;

			$sql_po_result=sql_select($sql);
			$all_po_id="";$all_file_no="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer="";
			$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=$tot_plan_cut_qty_pcs=$tot_plan_cut_qty=0;
			//echo $buyer_name;die;
			$fabric_detail_arr=array();
			foreach($sql_po_result as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
				if($all_file_no=="") $all_file_no=$row[csf("file_no")]; else $all_file_no.=",".$row[csf("file_no")];
				if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
				if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
				if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
				if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
				if($all_style_desc=="") $all_style_desc=$row[csf("style_desc")]; else $all_style_desc.=",".$row[csf("style_desc")];

				$pub_shipment_date=$row[csf("pub_shipment_date")];
				$style_wise_arr[$row[csf("style_ref_no")]]['buyer_name']=$row[csf("buyer_name")];
				$style_wise_arr[$row[csf("style_ref_no")]]['gmts_item_id']=$row[csf("gmts_item_id")];
				$style_wise_arr[$row[csf("style_ref_no")]]['quotation_id']=$row[csf("quotation_id")];
				$style_wise_arr[$row[csf("style_ref_no")]]['shipment_date'].=$pub_shipment_date.',';
				$style_wise_arr[$row[csf("style_ref_no")]]['job_no'].=$row[csf("job_no")].',';
				$style_wise_arr[$row[csf("style_ref_no")]]['qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];

				$style_wise_arr[$row[csf("style_ref_no")]]['qty']+=$row[csf('po_quantity')];
				$style_wise_arr[$row[csf("style_ref_no")]]['avg_rate']=$row[csf('avg_unit_price')];
				$style_wise_arr[$row[csf("style_ref_no")]]['po_amount']+=$row[csf('po_total_price')];
				$job_wise_arr[$row[csf("job_no")]]['po_amount']+=$row[csf('po_total_price')];
				$po_wise_arr[$row[csf("po_id")]]['plan_cut']+=$row[csf('plan_cut')]*$row[csf('ratio')];

				$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
				$total_order_qty+=$row[csf('po_quantity')];
				$tot_plan_cut_qty_pcs+=$row[csf('plan_cut')]*$row[csf('ratio')];
				$tot_plan_cut_qty+=$row[csf('plan_cut')];
				$total_unit_price+=$row[csf('unit_price')];
				$total_fob_value+=$row[csf('po_total_price')];

			}
			//print_r($style_wise_arr);
			$total_job_unit_price=($total_fob_value/$total_order_qty);
			//echo $all_job;die;
			$all_job_no=array_unique(explode(",",$all_full_job));
			$all_jobs="";
			foreach($all_job_no as $jno)
			{
					if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
			}

			$condition= new condition();
			$condition->company_name("=$cbo_company_name");
			if(str_replace("'","",$cbo_buyer_name)>0)
			{
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
			$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

			$yarn= new yarn($condition);
			$conversion= new conversion($condition);
			$trim= new trims($condition);
			$emblishment= new emblishment($condition);
			$wash= new wash($condition);
			$other= new other($condition);
			$other_cost=$other->getAmountArray_by_job();
			$commercial= new commercial($condition);
			$commision= new commision($condition);
			$commision_cost_arr=$commision->getAmountArray_by_job();
			$commision_item_cost_arr=$commision->getAmountArray_by_jobAndItemid();
			//print_r($commision_cost_arr);
			$po_qty=0;
			$po_plun_cut_qty=0;
			$total_set_qnty=0;
			 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty
			 from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id
			 and a.job_no in(".$all_jobs.")  and b.id in($all_po_id)  $file_no_cond $file_po_idCond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
			  order by b.id";
			$sql_po_data=sql_select($sql_po);
			foreach($sql_po_data as $sql_po_row)
			{
			$po_qty+=$sql_po_row[csf('order_quantity')];
			$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
			$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
			//$po_plun_cut_qty[$sql_po_row[csf('id')]]+=$sql_po_row[csf('plan_cut_qnty')]/$sql_po_row[csf('total_set_qnty')];
			}

		$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst where job_no in(".$all_jobs.")");
		foreach($pre_cost as $row)
		{
			$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
		}

		$company_con="";
		if($cbo_company_name!=0) $company_con="and company_id=$cbo_company_name";
		else if($cbo_style_owner!=0) $company_con="and company_id=$cbo_style_owner";
		//else if($cbo_company_name==0) $company_con=$cbo_company_name;
		$financial_para=array();
		$sql_std_para=sql_select("select id,interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where  status_active=1 and	is_deleted=0 and cost_per_minute>0 $company_con order by id desc");
		//echo $income_taxs=$sql_std_row[0][csf('income_tax')];
		list($income_tax)=$sql_std_para;
		$income_taxes=$income_tax[csf("income_tax")];
		$interest_expense=$income_tax[csf("interest_expense")];
		$cost_per_minute=$income_tax[csf("cost_per_minute")];

		/*foreach($sql_std_para as $sql_std_row)
		{
			//$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
			//$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
			//$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
		}
		print_r($financial_para);*/

		    $sql_new = "select job_no,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,total_cost ,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
			from wo_pre_cost_dtls
			where job_no in(".$all_jobs.") and status_active=1 and is_deleted=0";
			$data_array_new=sql_select($sql_new);
			$summary_data=array();

            foreach( $data_array_new as $row_new )
			{

					$costing_per=$costing_per_arr[$row_new[csf('job_no')]];

				if($costing_per==1)
				{
				$order_price_per_dzn=12;
				$costing_for=" DZN";
				}
				else if($costing_per==2)
				{
					$order_price_per_dzn=1;
					$costing_for=" PCS";
				}
				else if($costing_per==3)
				{
					$order_price_per_dzn=24;
					$costing_for=" 2 DZN";
				}
				else if($costing_per==4)
				{
					$order_price_per_dzn=36;
					$costing_for=" 3 DZN";
				}
				else if($costing_per==5)
				{
					$order_price_per_dzn=48;
					$costing_for=" 4 DZN";
				}
				$order_job_qnty=$row[csf("job_quantity")];
				$avg_unit_price=$row[csf("avg_unit_price")];


				$summary_data[price_dzn]+=$row_new[csf("price_dzn")];
				$summary_data[price_dzn_job]+=$job_wise_arr[$row_new[csf("job_no")]]['po_amount'];//($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
			    $summary_data[commission]+=$row_new[csf("commission")];
				//$commision_cost_arr
				//$summary_data[commission_job]=$commision_cost_arr[$row_new[csf('job_no')]];
				//$summary_data[commission_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("commission")];
				//echo $po_qty.'/'.$total_set_qnty.'*'.$order_price_per_dzn.'*'.$row_new[csf("price_dzn")];
				$summary_data[trims_cost]+=$row_new[csf("trims_cost")];
				$summary_data[emb_cost]+=$row_new[csf("embel_cost")];

				$summary_data[lab_test]+=$row_new[csf("lab_test")];
				$summary_data[lab_test_job]+=$other_cost[$row_new[csf("job_no")]]['lab_test'];

				$summary_data[inspection]+=$row_new[csf("inspection")];
				$summary_data[inspection_job]+=$other_cost[$row_new[csf("job_no")]]['inspection'];

				$summary_data[freight]+=$row_new[csf("freight")];
				$summary_data[freight_job]+=$other_cost[$row_new[csf("job_no")]]['freight'];

				$summary_data[currier_pre_cost]+=$row_new[csf("currier_pre_cost")];
				$summary_data[currier_pre_cost_job]+=$other_cost[$row_new[csf("job_no")]]['currier_pre_cost'];

				$summary_data[certificate_pre_cost]+=$row_new[csf("certificate_pre_cost")];
				$summary_data[certificate_pre_cost_job]+=$other_cost[$row_new[csf("job_no")]]['certificate_pre_cost'];
				$summary_data[wash_cost]+=$row_new[csf("wash_cost")];

				$summary_data[OtherDirectExpenses]+=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")]+$row_new[csf("commission")];

				$summary_data[OtherDirectExpenses_job]=$summary_data[lab_test_job]+$summary_data[inspection_job]+$summary_data[freight_job]+$summary_data[currier_pre_cost_job]+$summary_data[certificate_pre_cost_job]+$commision_cost_arr[$row_new[csf('job_no')]];

				$summary_data[cm_cost]+=$row_new[csf("cm_cost")];
				$summary_data[cm_cost_job]+=$other_cost[$row_new[csf("job_no")]]['cm_cost'];

				$summary_data[comm_cost]+=$row_new[csf("comm_cost")];
				//$summary_data[comm_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("comm_cost")];

				$summary_data[common_oh]+=$row_new[csf("common_oh")];
				$summary_data[common_oh_job]+=$other_cost[$row_new[csf("job_no")]]['common_oh'];
				$summary_data[depr_amor_pre_cost]+=$row_new[csf("depr_amor_pre_cost")];
				$summary_data[depr_amor_pre_cost_job]+=$other_cost[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
			}

			//Fabric =====================
			$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		//	print_r($fabric_qty);
			//$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			//$fabric_amount_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_amount_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			//print_r($fabric_amount_arr);
			//$dzn_fabric_amount=0;
			$sql_fabric = "select a.uom, sum(a.amount) as amount,b.po_break_down_id  from wo_pre_cost_fabric_cost_dtls a ,wo_pre_cos_fab_co_avg_con_dtls b where a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no in(".$all_jobs.") and b.po_break_down_id in($all_po_id) and a.fabric_source=2 group by a.uom,b.po_break_down_id";
			$data_arr_fabric=sql_select($sql_fabric);
			foreach($data_arr_fabric as $fab_row)
			{
				$plan_cut=$po_wise_arr[$fab_row[csf("po_break_down_id")]]['plan_cut'];
				$tot_fabric_amount=$fabric_amount_arr['knit']['grey'][$fab_row[csf("po_break_down_id")]][$fab_row[csf("uom")]];
				$tot_fabric_amount2=$fabric_amount_arr['woven']['grey'][$fab_row[csf("po_break_down_id")]][$fab_row[csf("uom")]];
				//echo $plan_cut.'dd';
				 $tot_fabric_amountFb=$tot_fabric_amount+$tot_fabric_amount2;
				$dzn_fabric_amount=($tot_fabric_amountFb/$plan_cut)*12;
				//echo $dzn_fabric_amount.'=dd';
				//echo $tot_fabric_amountFb.'='.$plan_cut;
				if($tot_fabric_amount2>0 || $tot_fabric_amount>0)
				{
					//$summary_data[fabric_cost][$fab_row[csf("id")]]=$dzn_fabric_amount;
				}
			//	echo $dzn_fabric_amount.'DF';
				$summary_data[fabric_cost]=$dzn_fabric_amount;
				$summary_data[fabric_cost_job]+=$tot_fabric_amount2+$tot_fabric_amount;
			}


			$totYarn=0;
			$YarnData=array();
			$yarn_data_array=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();

			$sql_yarn="select f.id as yarn_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,count_id,copm_one_id,percent_one,color,type_id   from wo_pre_cost_fab_yarn_cost_dtls f where   f.job_no in(".$all_jobs.") and f.is_deleted=0 and f.status_active=1  order by f.id";
			$data_arr_yarn=sql_select($sql_yarn);
			foreach($data_arr_yarn as $yarn_row)
			{
				$yarnrate=$yarn_row[csf("rate")];
				$summary_data[yarn_cost][$yarn_row[csf("yarn_id")]]+=$yarn_row[csf("amount")];
				$summary_data[yarn_cost_job]+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];

				$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."_".$yarn_row[csf("color")]."_".$yarnrate."'";
				$YarnData[$index]['qty']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
				$YarnData[$index]['amount']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
				$YarnData[$index]['dznqty']+=$yarn_row[csf("cons_qnty")];
				$YarnData[$index]['dznamount']+=$yarn_row[csf("amount")];
				$totYarn+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
			}

			// Yarn End
			// Conversion
			$totConv=0;
			$ConvData=array();
			$conv_data=array();
			$conv_amount_arr=$conversion->getAmountArray_by_conversionid();
			$conv_qty_arr=$conversion->getQtyArray_by_conversionid();
			$conv_process_amount_arr=$conversion->getAmountArray_by_jobAndProcess();
			//print_r($conv_amount_arr);
			$sql_conv = "select a.id as con_id, a.fabric_description as fabric_description_id, a.job_no, a.cons_process, a.req_qnty,a.avg_req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.uom  from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id where a.job_no in(".$all_jobs.") ";
			$data_arr_conv=sql_select($sql_conv);
			foreach($data_arr_conv as $conv_row){
				$convamount=$conv_amount_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
				$convQty=$conv_qty_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
				$conv_data[cons_process][$conv_row[csf('cons_process')]]=$conv_row[csf('cons_process')];
				//$conv_data[conv_id][$conv_row[csf('cons_process')]].=$conv_row[csf('con_id')].',';
				$conv_data[job_no][$conv_row[csf('cons_process')]].=$conv_row[csf('job_no')].',';
				$conv_data[amount][$conv_row[csf('con_id')]]=$conv_row[csf('amount')];
				$conv_data[amount_job][$conv_row[csf('con_id')]]+=$convamount;
				$summary_data[conver_cost_job]+=$convamount;
				//echo $conv_row[csf('amount')].',';
				$index=$conv_row[csf('con_id')];
				$ConvData[$index]['item_descrition']=$body_part[$conv_row[csf("body_part_id")]].", ".$color_type[$conv_row[csf("color_type_id")]].", ".$conv_row[csf("fabric_description")];
				$ConvData[$index]['cons_process']=$conv_row[csf("cons_process")];
				$ConvData[$index]['req_qnty']+=$conv_row[csf("req_qnty")];
				$ConvData[$index]['uom']=$conv_row[csf("uom")];
				$ConvData[$index]['charge_unit']=$conv_row[csf("charge_unit")];
				$ConvData[$index]['amount']+=$conv_row[csf("amount")];
				$ConvData[$index]['tot_req_qnty']+=$convQty;
				$ConvData[$index]['tot_amount']+=$convamount;
				$totConv+=$conv_row[csf("req_qnty")];
			}

			$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls
			where job_no in(".$all_jobs.")  order by id";
			$data_array_trim=sql_select($sql_trim);
			$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
			$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
			$totTrim=0;
			$TrimData=array();
			foreach( $data_array_trim as $row_trim )
			{
				$trim_qty=$trim_qty_arr[$row_trim[csf("id")]];
				$trim_amount=$trim_amount_arr[$row_trim[csf("id")]];
				$summary_data[trims_cost_job]+=$trim_amount;
				$TrimData[$row_trim[csf('id')]]['trim_group']=$row_trim[csf('trim_group')];
				$TrimData[$row_trim[csf('id')]]['description']=$row_trim[csf('description')];
				$TrimData[$row_trim[csf('id')]]['brand_sup_ref']=$row_trim[csf('brand_sup_ref')];
				$TrimData[$row_trim[csf('id')]]['remark']=$row_trim[csf('remark')];
				$TrimData[$row_trim[csf('id')]]['cons_uom']=$row_trim[csf('cons_uom')];
				$TrimData[$row_trim[csf('id')]]['cons_dzn_gmts']=$row_trim[csf('cons_dzn_gmts')];
				$TrimData[$row_trim[csf('id')]]['rate']=$row_trim[csf('rate')];
				$TrimData[$row_trim[csf('id')]]['amount']+=$row_trim[csf('amount')];
				$TrimData[$row_trim[csf('id')]]['apvl_req']=$row_trim[csf('apvl_req')];
				$TrimData[$row_trim[csf('id')]]['nominated_supp']=$row_trim[csf('nominated_supp')];
				$TrimData[$row_trim[csf('id')]]['tot_cons']+=$trim_qty;
				$TrimData[$row_trim[csf('id')]]['tot_amount']+=$trim_amount;
				$totTrim+=$row_trim[csf('cons_dzn_gmts')];
			}
				$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no in(".$all_jobs.") and emb_name in(1,2,4,5)";
				$data_array=sql_select($sql);
				$emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
				$emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();
				$totEmb=0;
				$EmbData=array();
				foreach( $data_array as $row )
				{
					$embqty=$emblishment_qty[$row[csf("job_no")]][$row[csf("id")]];
					$embamount=$emblishment_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data[emb_cost_job]+=$embamount;
					$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
					$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
					$EmbData[$row[csf("id")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
					$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$EmbData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$EmbData[$row[csf("id")]]['tot_cons']+=$embqty;
					$EmbData[$row[csf("id")]]['tot_amount']+=$embamount;
					$totEmb+=$row[csf("cons_dzn_gmts")];
				}

				//End Emb cost Cost part report here -------------------------------------------
				//Wash cost Cost part report here -------------------------------------------
				$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no in(".$all_jobs.") and emb_name =3";
				$data_array=sql_select($sql);
				$wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
				$wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
				foreach( $data_array as $row )
				{
					$washqty=$wash_qty[$row[csf("job_no")]][$row[csf("id")]];
					$washamount=$wash_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data[wash_cost_job]+=$washamount;
					$summary_data[OtherDirectExpenses_job]+=$washamount;
					$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
					$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
					$EmbData[$row[csf("id")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
					$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$EmbData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$EmbData[$row[csf("id")]]['tot_cons']+=$washqty;
					$EmbData[$row[csf("id")]]['tot_amount']+=$washamount;
					$totEmb+=$row[csf("cons_dzn_gmts")];
				}

				//End Wash cost Cost part report here -------------------------------------------
				//Commision cost Cost part report here -------------------------------------------
				 $sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no in(".$all_jobs.") and commission_amount>0 and status_active=1 ";
				 //echo $sql; die;
				$data_array=sql_select($sql);
				$commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
				$totCommi=$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
				$CommiData=array();
				foreach( $data_array as $row )
				{
					$commisionamount=$commision_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data[commission_job]+=$commisionamount;
					$CommiData[$row[csf("id")]]['particulars_id']=$row[csf("particulars_id")];
					$CommiData[$row[csf("id")]]['commission_base_id']=$row[csf("commission_base_id")];
					$CommiData[$row[csf("id")]]['commision_rate']=$row[csf("commision_rate")];
					$CommiData[$row[csf("id")]]['commission_amount']+=$row[csf("commission_amount")];
					$CommiData[$row[csf("id")]]['tot_commission_amount']+=$commisionamount;
					$totCommi+=$row[csf("commission_amount")];

					if($row[csf("particulars_id")]==1) //Foreign
					{
						//$foreign_percent_rate+=$row[csf("commision_rate")];
						$CommiData_foreign_cost+=$commision_item_cost_arr[$row[csf("job_no")]][1];
						$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
						$commision_job_cost_arr[$row[csf("job_no")]]+=$commision_item_cost_arr[$row[csf("job_no")]][1];

					}
					else
					{
						$CommiData_lc_cost+=$commision_item_cost_arr[$row[csf("job_no")]][2];

						$local_dzn_commission_amount+=$row[csf("commission_amount")];
						$commision_local_job_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
					}
				}
				//echo "10**".$CommiData_lc_cost; die;
				//echo $CommiData_foreign_cost.'DD';
				//End Commision cost Cost part report here -------------------------------------------
				//Commarcial cost Cost part report here -------------------------------------------
				$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls  where job_no in(".$all_jobs.") and rate>0 and status_active=1";
				$data_array=sql_select($sql);
				$commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
				$commarcial_item_amount=$commercial->getAmountArray_by_jobAndItemid();
				$totCommar=0;
				$CommarData=array();
				foreach( $data_array as $row )
				{
					$commarcialamount=$commarcial_amount[$row[csf("job_no")]][$row[csf("id")]];
					$summary_data[comm_cost_job]+=$commarcialamount;
					$CommarData[$row[csf("id")]]['item_id']=$row[csf("item_id")];
					$CommarData[$row[csf("id")]]['rate']=$row[csf("rate")];
					$CommarData[$row[csf("id")]]['amount']+=$row[csf("amount")];
					$CommarData[$row[csf("id")]]['tot_amount']+=$commarcialamount;
					$totCommar+=$row[csf("amount")];
					$item_id=$row[csf("item_id")];
					if($item_id==1)//LC
					{
						$commer_lc_cost+=$commarcial_item_amount[$row[csf("job_no")]][$item_id];
						$tot_lc_dzn_Commar+=$row[csf("amount")];
						$commarcial_job_amount[$row[csf("job_no")]]+=$commarcial_item_amount[$row[csf("job_no")]][$item_id];

					}
					else
					{
						$commer_without_lc_cost+=$commarcial_item_amount[$row[csf("job_no")]][$item_id];
						$totCommar+=$row[csf("amount")];
						$tot_without_lc_dzn_Commar+=$row[csf("amount")];

					}
				}
		?>

        <style>
		#page_sign_td{margin-top:260px; position:absolute;

		}
		#page_sign_td2{ margin-top:-70px; position:absolute;

		}

		</style>
       <div style="width:100%">
             <table width="850px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="8" align="center">
						<?
						$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'","image_location");
					?>
                    <img  src='../../<? echo $image_location; ?>' height='70' align="left" />
                    <strong style=" font-size:18px"><? echo $company_library[$cbo_company_name];?></strong><br>
                    <strong style="font-size: 16px"><? echo $report_title; ?></strong>                    
                    </td>
                </tr>
                
            </table>
            <table width="auto"  style="margin-left:10px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <caption>
               		 <strong>Details Part:</strong>
                 </caption>
                    <thead>

                     	<th width="120"><b>Buyer</b> </th>
                        <th width="120"><b>Item</b> </th>
                        <th width="100"><b>Ship Date</b> </th>
                        <th width="120"><b>Style</b> </th>
                        <th width="100"><b>Job No</b> </th>
						<th width="80"><b>Quotation No</b> </th>
                        <th width="80"><b>Qty.</b> </th>
                        <th width="80"><b>Qty.(PCS)</b> </th>
                        <th width="60"><b>FOB</b> </th>
                        <th width="80"><b>Total Amount</b> </th>


                    </thead>
                    <?
					$k=1;$total_po_qty=$total_po_pcs_qty=$total_po_amount;

					$all_last_shipdates='';
                    foreach($style_wise_arr as $style_key=>$val)
					{
						 $gmts_item_id=$val[('gmts_item_id')];
						 $shipment_date=rtrim($val[('shipment_date')],',');
						  $shipment_dates=array_unique(explode(",",$shipment_date));
						   $job_no=rtrim($val[('job_no')],',');
						  $job_nos=implode(",",array_unique(explode(",",$job_no)));
						   
						   $last_shipmentdates=max($shipment_dates);
						   $all_last_shipdates.=$last_shipmentdates.',';
							$gmts_item=''; $gmts_item_id=explode(",",$gmts_item_id);
							foreach($gmts_item_id as $item_id)
							{
								//echo $item_id;
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}


					?>
                	<tr>
                        <td width="120"><p> <? echo $buyer_arr[$val[('buyer_name')]];?></p></td>
                        <td  width="120"><p>  <? echo $gmts_item;?></p></td>
                        <td width="100"><p> <? echo change_date_format($last_shipmentdates);?></p></td>
                        <td width="120"><p> <? echo $style_key;?></p></td>
                        <td width="100"><p> <? echo $job_nos;?></p></td>
						<td width="100" align="center"><p> <? echo $val[('quotation_id')];?></p></td>

                        <td width="80" align="right"><p> <? echo number_format($val[('qty')],0);?></p></td>
                        <td  width="80" align="right">  <? echo  number_format($val[('qty_pcs')],0);?></td>
                        <td width="60" align="right"><p> <? echo number_format($val[('avg_rate')],4);?></p></td>
                        <td width="80" align="right"><p> <? echo number_format($val[('po_amount')],2);?></p></td>

                    </tr>
                    <?
					$k++;
					$total_po_qty+=$val[('qty')];
					$total_po_pcs_qty+=$val[('qty_pcs')];
					$total_po_amount+=$val[('po_amount')];
					$total_po_amount_arr[$val[('job_no')]]+=$val[('po_amount')];
					}
					?>
                    <tfoot>
                     <tr>
                    <td colspan="3" align="right">  <b>Qty DZN </b></td>
                    <td align="right" title="Plan Cut Dzn=<? echo number_format($tot_plan_cut_qty_pcs/12,2);?>"> &nbsp; <? $total_po_pcs_qty_dzn=$total_po_pcs_qty/12;echo number_format($total_po_pcs_qty_dzn,2);?></td>
					<td align=""><b> &nbsp;</b></td>
					<td align="right"> <b>Total</b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_po_qty,0);?> </b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_po_pcs_qty,0);?> </b></td>
					<td align=""><b> &nbsp;</b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_po_amount,2);?> </b></td>

                    </tr>

                    <tr>
                    <td colspan="3" align="right">  <b>Last Shipment Date </b></td>
                    <td align="right"> &nbsp; <? $all_last_ship_dates=rtrim($all_last_shipdates,',');$all_last_ship_dates=array_unique(explode(",",$all_last_ship_dates));
					 $last_shipment_dates=max($all_last_ship_dates); echo change_date_format($last_shipment_dates);?></td>
					  <td align="right"> <b>&nbsp;</b></td>
					    <td align="right"> <b>&nbsp;</b></td>
					 <td align="right" colspan="3"><b>Foreign Commission &nbsp; <? $foreign_percent_rate=($CommiData_foreign_cost/$total_po_amount)*100;
					 echo number_format($foreign_percent_rate,2).'%';?> </b></td>

					<td align="right"><b> &nbsp; <?
					//$pre_foreign_commission_per=$CommiData_lc_cost;
					echo number_format($CommiData_foreign_cost,2);?> </b></td>

                    </tr>
                     <tr>
                    <td align="right" colspan="3"  title="Total PO Value-Commission">  <b>Maximum BB LC-70% </b></td>
                    <td align="right" title="Commission=<? echo $summary_data[commission_job];?>"> &nbsp; <?
					$net_fob_value=$total_po_amount-$summary_data[commission_job];
					$gross_fob_value_job=$total_po_amount;

					 echo number_format(($net_fob_value*70)/100,2);?></td>
					   <td align="right"> <b>&nbsp;</b></td>
					  <td align="right"> <b>&nbsp;</b></td>
					 <td align="right" colspan="3"><b>Freight Cost &nbsp; <?
					 $freight_percent_rate=($summary_data[freight_job]/$total_po_amount)*100;
					 echo number_format($freight_percent_rate,2).'%';?> </b></td>
					<td align="right"><b> &nbsp; <?
					$pre_freight_cost_per=$summary_data[freight_job];//($total_po_amount*$summary_data[freight])/100;
					echo number_format($pre_freight_cost_per,2);?> </b></td>
                    </tr>
					 <tr>
					  	 <td align="right" colspan="6"> </td>
						  <td align="right" colspan="3" title="<? echo $CommiData_foreign_cost;?>"><b>LC Cost &nbsp; <?
						  $commar_rate_percent=($commer_lc_cost/$total_po_amount)*100;
						  echo number_format($commar_rate_percent,2).'%';?> </b></td>
						<td align="right"><b> &nbsp; <?
						$pre_commercial_per=$commer_lc_cost;//($total_po_amount*$commar_rate_percent)/100;
						echo number_format($pre_commercial_per,2);
						$tot_sum_amount=$total_po_amount-($CommiData_foreign_cost+$pre_freight_cost_per+$pre_commercial_per);
						?> </b></td>
					  </tr>

                    <tr>
                    <td colspan="4" align="right"> <b style="float:left"> File No: &nbsp; <? echo implode(",",array_unique(explode(",",$all_file_no))); ?> </b></td>
                    <td colspan="2" align="right"> <b> Total </b></td>
                    <td align="right">  <b><? echo number_format($total_po_qty,2);?> </b></td>
                    <td align="right"> <b> <? echo number_format($total_po_pcs_qty,2);?> </b></td>
                    <td align="right">  <b><? //echo number_format($total_po_qty,2);?> </b></td>
                     <td align="right"> <b> <? echo number_format($tot_sum_amount,2);?> </b></td>
                    </tr>
                    </tfoot>
           </table>

           <br/>
		 <!--Detail Part-->
                   <br/>
            <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
           				<caption> <b style="float:left">Fabric Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="100">Fab. Nature</th>
						<th width="200">Description</th>
						<th width="100">Source</th>
						<th width="100">Fab. Cons/Dzn</th>
						<th width="100">Total Cons. </th>
                        <th width="50">UOM</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount (USD)/Dzn</th>
                         <th width="">Total Amount (USD)</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:1000px;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table"   width="980" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$sql_fab="select a.job_no_prefix_num as job_prefix,a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.po_quantity,b.po_total_price, b.po_number, b.pub_shipment_date,c.id, c.job_no,c.item_number_id, c.body_part_id as body_id, c.fab_nature_id as nat_id, c.color_type_id as color_type, c.fabric_description as fab_desc, c.avg_cons,c.uom, c.fabric_source as fab_source, c.rate, c.amount, c.avg_finish_cons, c.status_active from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond $file_year_cond order  by b.id,c.fab_nature_id,c.fabric_source, c.fabric_description,c.uom";
				  $sql_fabs_result=sql_select($sql_fab);
				  $fabric_detail_arr=array();  $fabric_job_check_arr=array();
				$total_purchase_amt=0;
				foreach($sql_fabs_result as $row)
				{
					$row[csf("fab_source")]=$row[csf("fab_source")];
					$item_desc= $body_part[$row[csf("body_id")]].",".$color_type[$row[csf("color_type")]].",".$row[csf("fab_desc")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['amount']=$row[csf("amount")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['rate']=$row[csf("amount")]/$row[csf("avg_cons")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['avg_cons']=$row[csf("avg_cons")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['amount']=$row[csf("amount")];
					$fabric_detail_arr[$row[csf("nat_id")]][$row[csf("uom")]][$item_desc][$row[csf("fab_source")]]['pre_fab_id'].=$row[csf("id")].',';

					if($row[csf("fab_source")]==2)
					{
						$group_job_value=$job_no;
						if (!in_array($group_job_value,$fabric_job_check_arr) )
						{
							$total_purchase_amt+=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
							$fabric_job_check_arr[]=$group_job_value;
						}
					}
					//$fabric_amt=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
					//echo $fabric_amt.',';
				}
				 // print($fabric_btb_amt);
								//print_r($fabric_detail_arr);die;
								//echo $total_fob_value.'/'.$total_order_qty;
				$styleRef=explode(",",$txt_style_ref);
				$all_style_job="";
				foreach($styleRef as $sid)
				{
						if($all_style_job=="") $all_style_job="'".$sid."'"; else $all_style_job.=","."'".$sid."'";
				}
				$fabric_rowspan_arr=array();$uom_rowspan_arr=array();
				foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
				{
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

							$fabric_rowspan_arr[$fab_nat_key]=$fabrice_rowspan;
							$uom_rowspan_arr[$fab_nat_key][$uom_key]=$uom_rowspan;
						}
					}
				}

					$i=$m=1;$total_greycons=$total_tot_greycons=$total_amount=$grand_total_greycons=$grand_total_tot_greycons=$grand_total_amount=0;
					foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
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
								$rate=$val['rate'];$amount=$val['amount'];
								//$amount=$val['amount'];
								$fincons=$tot_greycons=$tot_amount=0;
								foreach($pre_fab_ids as $fab_id)
								{
									if($fab_nat_key==2) //Purchase
									{
										//$fincons+=$fabric_qty['knit']['finish'][$fab_id][$uom_key];
										$tot_greycons+=$fabric_qty['knit']['grey'][$fab_id][$uom_key];
										$tot_amount+=$fabric_amount['knit']['grey'][$fab_id][$uom_key];
									}
									else
									{
										//$fincons+=$fabric_qty['woven']['finish'][$fab_id][$uom_key];
										$tot_greycons+=$fabric_qty['woven']['grey'][$fab_id][$uom_key];
										$tot_amount+=$fabric_amount['woven']['grey'][$fab_id][$uom_key];
									}
								}
								//echo $tot_plan_cut_qty.'g';
								$avg_cons=($tot_greycons/$tot_plan_cut_qty)*12;

					?>

						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">  <?
                      	 if($y==1){
						?>
							<td width="30" rowspan="<? echo $uom_rowspan_arr[$fab_nat_key][$uom_key];?>"><? echo $m; ?></td>
							<td width="100" rowspan="<? echo $uom_rowspan_arr[$fab_nat_key][$uom_key];?>">
							<? echo $item_category[$fab_nat_key]; ?></td>
                             <?
							  }
							?>
							<td width="200" align="center"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="center" ><div style="word-break:break-all"><? echo $fabric_source[$source_key]; ?></div></td>
							<td width="100" title="Tot cons/Plan Cut*12" align="right"><div style="word-break:break-all"><? echo number_format($avg_cons,4); ?></div></td>
                            <td width="100" title="" align="right"><div style="word-break:break-all"><? echo number_format($tot_greycons,4); ?></div></td>

                            <td width="50" align="center"><? echo $unit_of_measurement[$uom_key]; ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($tot_amount/$tot_greycons,4); ?></div></td>
                            <td width="100"  align="right" title="Tot Fab Amt/PO Qty Dzn"><div style="word-break:break-all"><? echo number_format($tot_amount/$total_po_pcs_qty_dzn,4); ?></div></td>
                             <td width=""  align="right"><div style="word-break:break-all"><? echo number_format($tot_amount,4); ?></div></td>
                            </tr>
                            <?
								$total_greycons+=$avg_cons;
								$total_tot_greycons+=$tot_greycons;
								$total_amount+=$amount;$total_tot_amount+=$tot_amount;

								$grand_total_greycons+=$avg_cons;
								$grand_total_tot_greycons+=$tot_greycons;
								$grand_total_amount+=$amount;
								$grand_total_tot_amount+=$tot_amount;
								$y++;
								$i++;
									}
								}
								$m++;
							?>
                            <tr bgcolor="#F4F3C4">
                                <td>&nbsp; </td>
                                <td>&nbsp; </td>
                                <td>&nbsp;</td>
                                <td align="right"><strong>Sub Total</strong> </td>
                                <td align="right"><strong><? echo number_format($total_greycons,4);$total_greycons=0;?> </strong></td>
                                <td align="right"><strong><? echo number_format($total_tot_greycons,4);$total_tot_greycons=0;?> </strong></td>
                                <td align="right">&nbsp;</td>
                                <td>&nbsp; </td>
                                <td align="right"><strong><? $total_amount=$total_tot_amount/$total_po_pcs_qty_dzn;echo number_format($total_amount,4);$total_amount=0;?></strong> </td>
                                <td align="right"><strong><? echo number_format($total_tot_amount,4);$total_tot_amount=0;?></strong> </td>
                                </tr>
                            <?
							}
						}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="4" ><strong>Grand Total</strong> </th>
                            <th align="right"><strong><? echo number_format($grand_total_greycons,4);?> </strong></th>
                            <th align="right"><strong><? echo number_format($grand_total_tot_greycons,4);?> </strong></th>
                            <th align="right">&nbsp;</th>
                            <th>&nbsp; </th>
                            <th align="right"><strong><? echo number_format($grand_total_tot_amount/$total_po_pcs_qty_dzn,4);?></strong> </th>
                            <th align="right"><strong><? echo number_format($grand_total_tot_amount,4);?></strong> </th>
                            </tr>
                            </tfoot>
                    </table>
                     </div>
                   		 <!--Fabtic Details End-->
                     <br/><br/>
                     <table id="table_header_1" style="margin-left:10px"  class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
           					<caption> <b style="float:left">Yarn Details :</b></caption>
					<thead>
                    	<th width="100"></th>
                        <th width="250">Yarn Description</th>
						<th width="100">Yarn Qty(Dzn).</th>
						<th width="100">Total Yarn Qnty</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount/Dzn</th>
                        <th width="">Total Amount</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:870px; margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$yarn= new yarn($condition);
					$yarn_data_arr=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
					$yarn_data_IDarray=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();
				    // $sql_yarn="select min(c.id) as id,c.count_id, c.copm_one_id, c.percent_one,c.color,c.type_id,min(c.cons_ratio) as cons_ratio,(c.cons_qnty) as cons_qnty,(c.amount) as amount,c.rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_yarn_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond $file_year_cond group by c.count_id, c.copm_one_id,c.amount,c.cons_qnty, c.percent_one,  c.color,c.type_id, c.rate order  by c.count_id, c.copm_one_id,c.percent_one";
					$sql_yarn="select (c.id) as id,c.count_id, c.copm_one_id, c.percent_one,c.color,c.type_id,(c.cons_ratio) as cons_ratio,(c.cons_qnty) as cons_qnty,(c.amount) as amount,c.rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_yarn_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond $file_year_cond order  by c.count_id, c.copm_one_id,c.percent_one";

					$result_yarn=sql_select($sql_yarn);
					$yarn_detail_arr=array();
					$yarnamount=0;
					foreach($result_yarn as $row)
					{
						$item_descrition = $lib_yarn_count[$row[csf("count_id")]]."_".$composition[$row[csf("copm_one_id")]]."_".$row[csf("percent_one")]."%_".$color_library[$row[csf("color")]]."_".$yarn_type[$row[csf("type_id")]];
						//echo $item_descrition.'<br>';
						$row_span+=1;
						$yarn_detail_arr[$item_descrition]['rate']=$row[csf("rate")];
						$yarn_detail_arr[$item_descrition]['count_id']=$row[csf("count_id")];
						$yarn_detail_arr[$item_descrition]['copm_one_id']=$row[csf("copm_one_id")];
						$yarn_detail_arr[$item_descrition]['percent_one']=$row[csf("percent_one")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarn_detail_arr[$item_descrition]['type_id']=$row[csf("type_id")];
						$yarn_detail_arr[$item_descrition]['color']=$row[csf("color")];
						$yarn_detail_arr[$item_descrition]['cons_qnty']=$row[csf("cons_qnty")];
						$yarn_detail_arr[$item_descrition]['amount']+=$row[csf("amount")];
						$yarn_detail_arr[$item_descrition]['yarn_amount']+=$yarn_data_IDarray[$row[csf("id")]]['amount'];
						$yarnamount=$yarn_data_arr[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
						$yarncons_qntys=$yarn_data_arr[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						$yarn_detail_arr[$item_descrition]['yarn_cost']+=$yarnamount;
						$yarn_detail_arr[$item_descrition]['yarn_qty']+=$yarn_data_IDarray[$row[csf("id")]]['qty'];
					}
					$i=$m=1;$grand_total_tot_yarncons=$grand_total_yarnavgcons=$grand_total_amount=$grand_total_yarn_tot_amount=0;
					foreach($yarn_detail_arr as $desc_key=>$val)
					{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$yarn_cost=$val['amount'];
					//$cons_qnty=$val['cons_qnty'];
					$tot_yarn_qty=$val['yarn_qty'];
					$tot_yarn_amount=$val['yarn_amount'];
					//$yarncons_qnty=$yarn_qty;//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['qty'];
					//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['qty'];
					$yarn_amount=$val['amount'];//$yarn_data_arr[$val["count_id"]][$val["copm_one_id"]][$val["percent_one"]][$val["type_id"]][$val["color"]][$val["rate"]]['amount'];
					$desc_yarn=implode(", ",array_unique(explode("_",$desc_key)));

					$cons_qnty=($tot_yarn_qty/$tot_plan_cut_qty)*12;
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('try_<? echo $i; ?>','<? echo $bgcolor;?>')" id="try_<? echo $i; ?>"> 					 <?
                      	 if($m==1){
						?>
							<td width="100" valign="middle" rowspan="<? echo $row_span;?>"><? echo 'Yarn Cost'; ?></td>
                             <?
							 }
							?>
							<td width="250"><div style="word-break:break-all"><? echo $desc_yarn; ?></div></td>
							<td width="100" align="right" title="Tot cons/Plan Cut*12"><div style="word-break:break-all"><? echo number_format($cons_qnty,4); ?></div></td>
							<td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($tot_yarn_qty,4); ?></div></td>
                            <td width="100" align="right"><? echo number_format($tot_yarn_amount/$tot_yarn_qty,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($yarn_amount,4);//number_format($tot_yarn_amount/$total_po_pcs_qty_dzn,4); ?></div></td>
                             <?
							// $total_fob=$totalyarn_amount;

						?>
                             <td width="" valign="middle" align="right" title=""><? echo number_format($tot_yarn_amount,4); ?></td>
                            </tr>
                            <?
								$grand_total_yarnavgcons+=$cons_qnty;
								$grand_total_amount+=$yarn_amount;
								$grand_total_tot_yarncons+=$tot_yarn_qty;
								$grand_total_yarn_tot_amount+=$tot_yarn_amount;
								$y++;
								$i++;
							$m++;
						}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="2"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_yarnavgcons,4);?> </strong></th>
                                <th align="right"><strong><? echo number_format($grand_total_tot_yarncons,4);?> </strong></th>
                                <th align="right"><strong><? //echo number_format($grand_total_amount,4);?></strong> </th>
                                <th align="right"><? echo number_format($grand_total_amount,4);?></th>
                                <th align="center"><strong><? echo number_format($grand_total_yarn_tot_amount,4);?></strong> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     <br/><br/>
                      <div>
                      <br/>
           			<table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="950" cellpadding="0" cellspacing="0" border="1" rules="all">
           		<caption> <b style="float:left">Conversion Cost to Fabric :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="250">Particulars</th>
						<th width="100">Process</th>
						<th width="100">Cons/Dzn</th>
                        <th width="100">Total Cons</th>
                        <th width="50">UOM</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount /Dzn</th>
                        <th width="">Total Amount</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:970px; margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="950" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
						$conversion= new conversion($condition);
						$conv_data_amount_arr=$conversion->getAmountArray_by_conversionid();
					
						$conv_data_qty_arr=$conversion->getQtyArray_by_conversionid();
				
						// echo "<pre>";
						// print_r($conv_data_qty_arr);
				   $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,sum(c.req_qnty) as req_qnty,avg(c.charge_unit) as charge_unit,sum(c.amount) as amount,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.fabric_description as descript,d.item_number_id,d.uom from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond $file_year_cond group by c.id,c.fabric_description,a.job_no, c.cons_process,c.color_break_down,d.body_part_id,d.fab_nature_id,d.color_type_id,d.item_number_id,d.uom ,d.fabric_description order by c.fabric_description,c.cons_process";

					$result_conv=sql_select($sql_conv);
					$conv_detail_arr=array();$conv_detail_arr2=array();
					$totalconv_amount=0;
					foreach($result_conv as $row)
					{
						$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("descript")];
						$row_span+=1;

						$conv_detail_arr2[$row[csf("cons_process")]][$item_descrition]['id'].=$row[csf("id")].',';
						$conv_detail_arr2[$row[csf("cons_process")]][$item_descrition]['desc']=$item_descrition;
						$conv_detail_arr2[$row[csf("cons_process")]][$item_descrition]['uom']=$row[csf("uom")];
						$conv_detail_arr2[$row[csf("cons_process")]][$item_descrition]['charge_unit']=$row[csf("charge_unit")];
						$conv_detail_arr2[$row[csf("cons_process")]][$item_descrition]['req_qnty']=$row[csf("req_qnty")];
						$conv_detail_arr2[$row[csf("cons_process")]][$item_descrition]['amount']=$row[csf("amount")];
						$conv_detail_arr2[$row[csf("cons_process")]][$item_descrition]['tot_row']+=1;
						$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
					}
					// echo "<pre>";
					// print_r($conv_detail_arr2);
					$i=$m=1;$grand_total_conv_qty=$total_conv_tot_amount=$grand_total_conv_tot_qty=$grand_total_conv_tot_amount=$total_conv_tot_qty=$total_conv_amount=0;
					foreach($conv_detail_arr2 as $process_key=>$process_data)
					{
						foreach($process_data as $desc_key=>$val)
						{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$desc_key=$val[('desc')];
						$conv_id=rtrim($val['id'],',');
						$conv_ids=array_unique(explode(",",$conv_id));
						
						$tot_req_qty=$tot_convamount=0;
						foreach($conv_ids as $convID)
						{
							$tot_req_qty+=$conv_data_qty_arr[$convID][$val['uom']];
							$tot_convamount+=$conv_data_amount_arr[$convID][$val['uom']];
						}
						//$cons_req_qnty=$val['req_qnty'];
						
						$amount=$val[('amount')];
						$process_name=$conversion_cost_head_array[$process_key];
						$cons_req_qnty=($tot_req_qty/$tot_plan_cut_qty)*12;
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trconv_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trconv_<? echo $i; ?>"> 					 <?
						?>
							<td width="30" valign="middle"><? echo $i; ?></td>
							<td width="250" title="<? echo $conv_id;?>"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo $process_name; ?></div></td>
							<td width="100" align="right" title="Tot cons/Plan Cut*12" ><div style="word-break:break-all"><? echo number_format($cons_req_qnty,4); ?></div>
                            <td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($tot_req_qty,4); ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$val["uom"]]; ?></td></td>
                            <td width="100" align="right"><? echo number_format($tot_convamount/$tot_req_qty,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($amount,4); ?></div></td>
                             <?
                      //	if($z==1){
						?>
                             <td width=""  align="right" ><? echo  number_format($tot_convamount,4); ?></td>
                              <?
							//}
							  ?>
                            </tr>
                            <?
								$total_conv_qty+=$cons_req_qnty;
								$total_conv_tot_qty+=$tot_req_qty;
								$total_conv_amount+=$amount;
								$total_conv_tot_amount+=$tot_convamount;
								$grand_total_conv_qty+=$cons_req_qnty;
								$grand_total_conv_tot_qty+=$tot_req_qty;
								$grand_total_conv_amount+=$amount;
								$grand_total_conv_tot_amount+=$tot_convamount;
								$i++;

								}
								?>
                               <tr  bgcolor="#F4F3C4" class="tbl_bottom">
                                <td colspan="3" align="right"><strong>Sub Total</strong> </td>
                                <td align="right"><strong><? echo number_format($total_conv_qty,4);$total_conv_qty=0;?> </strong></td>
                                <td align="right"><strong><? echo number_format($total_conv_tot_qty,4);$total_conv_tot_qty=0;?> </strong></td>
                                <td align="right"><strong>&nbsp; </strong></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? $total_conv_amount=$total_conv_amount;echo number_format($total_conv_amount,4);$total_conv_amount=0;?></td>
                                <td align="right"><? echo number_format($total_conv_tot_amount,4);$total_conv_tot_amount=0;?></td>
                            </tr>
                                <?
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? echo number_format($grand_total_conv_qty,4);?> </strong></th>
                                 <th align="right"><strong><? echo number_format($grand_total_conv_tot_qty,4);?> </strong></th>
                                <th align="right"><strong>&nbsp; </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_conv_amount,4);?></th>
                                <th align="right"><? echo number_format($grand_total_conv_tot_amount,2);?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                    </div>
                    <br> <br>
                    <div>
                    <br>
                    <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
           				<caption> <b style="float:left">Trims Cost Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="100">Item Group</th>
						<th width="200">Description</th>
						<th width="130">Nominated Supp</th>
                        <th width="50">UOM</th>
                        <th width="100">Cons./Dzn</th>
                        <th width="100">Total Cons.</th>
                        <th width="100">Avg Rate</th>
                        <th width="100">Amount /Dzn</th>
                        <th width="">Total Amount</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:1020px; margin-left:10px" align="left" id="scroll_body1">
					 <table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					$trim_group_arr=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name" );
					$trim= new trims($condition);
					//echo $trim->getQuery(); die;
					$trims_item_qty_arr=$trim->getQtyArray_by_itemidAndDescription();
					//$trims= new trims($condition);
					$trims_item_amount_arr=$trim->getAmountArray_by_itemidAndDescription();
					//print_r($trims_item_amount_arr);

				  $sql_trims="select c.trim_group,c.description,c.cons_uom, c.nominated_supp,sum(c.amount) as amount,sum(c.cons_dzn_gmts) as cons_qty from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_trim_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond $file_year_cond group by  c.trim_group,c.description,c.cons_uom,c.nominated_supp  order by c.trim_group";
					$result_trims=sql_select($sql_trims);
					$trims_detail_arr=array();
					foreach($result_trims as $row)
					{
						$item_descrition =$row[csf("description")];
						$trims_rowspan+=1;
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition]['nominated_supp']=$row[csf("nominated_supp")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition]['uom']=$row[csf("cons_uom")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition]['trim_group']=$row[csf("trim_group")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition]['amount']=$row[csf("amount")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition]['cons_qty']=$row[csf("cons_qty")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition]['rate']=$row[csf("rate")];
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition]['id'].=$row[csf("id")].',';
						$trims_detail_arr[$row[csf("trim_group")]][$item_descrition]['desc']=$item_descrition;
						//$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];

					}
					//echo $trims_rowspan;

					$i=$z=1;$grand_total_trim_amount=$grand_total_trim_tot_amount=$grand_total_trim_tot_qty=0;
					foreach($trims_detail_arr as $trims_key=>$trims_data)
					{

						foreach($trims_data as $desc_key=>$trims_data)
						{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$trim_amount=$trims_item_amount_arr[$trims_key][$desc_key];
						$cons_dzn_gmts=$trims_item_qty_arr[$trims_key][$desc_key];
						//$trim_group=$val[('trim_group')];
						$nominated_supp=$trims_data[('nominated_supp')];
						$amount=$trims_data[('amount')];
						$cons_qty=$trims_data[('cons_qty')];
						$avg_rate=$trim_amount/$cons_dzn_gmts;

						$cons_qty=($cons_dzn_gmts/$tot_plan_cut_qty)*12;


					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trtrim_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trtrim_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="100"><div style="word-break:break-all"><? echo $trim_group_arr[$trims_key]; ?></div></td>
							<td width="200" align="right"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="130" align="right" ><div style="word-break:break-all"><? echo $supplier_library[$nominated_supp]; ?></div>
                            <td width="50" align="right"><? echo $unit_of_measurement[$trims_data["uom"]]; ?></td></td>
                            <td width="100" align="right" title="Tot cons/Plan Cut*12"><? echo number_format($cons_qty,4); ?></td></td>
                            <td width="100" align="right"><? echo number_format($cons_dzn_gmts,4); ?></td></td>
                            <td width="100" align="right"><? echo number_format($avg_rate,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($trim_amount/$total_po_pcs_qty_dzn,4); ?> </div></td>
                             <td width="" align="right" title=""><? echo number_format($trim_amount,4); ?></td>
                            </tr>
                            <?
								$grand_total_trim_tot_amount+=$trim_amount;
								$grand_total_trim_amount+=$amount;
								$grand_total_trim_qty+=$cons_qty;
								$grand_total_trim_tot_qty+=$cons_dzn_gmts;
								$i++;
								}
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="4"><strong>Grand Total</strong> </th>
                                <th align="right"><strong><? //echo number_format($grand_total_conv_qty,4);?> </strong></th>
                                 <th align="right"><strong><? echo number_format($grand_total_trim_qty,4);?> </strong></th>
                                <th align="right"><strong><? echo number_format($grand_total_trim_tot_qty,4);?> </strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_trim_tot_amount/$total_po_pcs_qty_dzn,4);?></th>
                                <th align="right"><? echo number_format($grand_total_trim_tot_amount,4); ?></th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                     </div>
               <br/>
               <div>
               <br/>
            <table id="table_header_1" style="margin-left:10px"   class="rpt_table" width="730" cellpadding="0" cellspacing="0" border="1" rules="all">
           	<caption> <b style="float:left">Embellishment Details :</b></caption>
					<thead>
                    	<th width="30">SL</th>
                        <th width="120">Particulars</th>
						<th width="100">Type</th>
						<th width="100">Cons/Dzn</th>
                        <th width="100">Total Cons</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount</th>
                        <th width="">Total Amount</th>
                    </thead>
            </table>
                    <div class="scroll_div_inner" style="width:750px; margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table" width="730" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
					//$sql_emb_setting="select embellishment_budget_id,embellishment_id from variable_order_tracking where company_name=$cbo_company_name and variable_list=56";
					$sql_emb_vari=sql_select("select embellishment_id,embellishment_budget_id from variable_order_tracking where company_name=$cbo_company_name and variable_list=56 and status_active=1 and is_deleted=0");
					foreach($sql_emb_vari as $vari_row)
					{
					$emb_variArr[$vari_row[csf('embellishment_id')]]=$vari_row[csf('embellishment_budget_id')]?$vari_row[csf('embellishment_budget_id')]:2;
					}

					$emblishment= new emblishment($condition);
					$emblishment_qty_arr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
					$emblishment_amount_arr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
					$emblishment_qty_name_type_arr=$emblishment->getQtyArray_by_jobEmbnameAndEmbtype();
					$emblishment_amount_name_type_arr=$emblishment->getAmountArray_by_jobEmbnameAndEmbtype();
					$wash_type_name_qty_arr=$wash->getQtyArray_by_jobEmbnameAndEmbtype();
					$wash_type_name_amount_arr=$wash->getAmountArray_by_jobEmbnameAndEmbtype();

				  $sql_emblish="select  c.job_no, c.emb_name,c.emb_type,sum(c.cons_dzn_gmts) as cons_dzn_gmts,avg(c.rate) as rate, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_embe_cost_dtls c  where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.amount>0 $company_name_cond  $job_no_cond $job_style_cond $order_cond $buyer_id_cond $season_cond $file_no_cond $file_po_idCond $file_year_cond group by c.job_no, c.emb_name,c.emb_type order by  c.emb_name,c.emb_type";

					$result_emblish=sql_select($sql_emblish);
					$emblish_detail_arr=array();
					foreach($result_emblish as $row)
					{
						$item_descrition =$row[csf("description")];
						$embl_rowspan+=1;
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['emb_name']=$row[csf("emb_name")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['emb_type']=$row[csf("emb_type")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['cons_dzn_gmts']=$row[csf("cons_dzn_gmts")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['amount']=$row[csf("amount")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['rate']=$row[csf("rate")];
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['job_no'].=$row[csf("job_no")].',';
						$emblish_detail_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['desc']=$item_descrition;
						//$emblishment_qty_arr
						//$embsamount=$emblishment_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
						//$totalemb_detail_arr[100]['amount']+=$embsamount;
					}
					//echo $embl_rowspan;
					//print_r($conv_rowspan_arr);
					$i=$m=1;$grand_total_embl_amount=$grand_total_cons_dzn_gmts=0;
					foreach($emblish_detail_arr as $emb_name=>$enm_val)
					{
						foreach($enm_val as $emb_type=>$val)
						{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$job_no=rtrim($val[('job_no')],',');
						$job_nos=array_unique(explode(",",$job_no));
						//$emb_name=$val[('emb_name')];$emb_type=$val[('emb_type')];

						if($emb_name==1)$em_type = $emblishment_print_type[$emb_type];
						else if($emb_name==2)$em_type = $emblishment_embroy_type[$emb_type];
						else if($emb_name==3)$em_type = $emblishment_wash_type[$emb_type];
						else if($emb_name==4)$em_type = $emblishment_spwork_type[$emb_type];
						$emb_cons_dzn=$val['cons_dzn_gmts'];
						$amount=$val['amount'];
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
						$embellishment_setting=$emb_variArr[$emb_name];
						if($embellishment_setting==1) //Po
						{
							$emb_cons_dzn=($cons_dzn_gmts/$total_po_qty)*12;
						}
						else //Plan cut
						{
							$emb_cons_dzn=($cons_dzn_gmts/$tot_plan_cut_qty)*12;
						}
						//wash_type_name_amount_arr
						//echo $embl_amount.',';
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tremb_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tremb_<? echo $i; ?>">
							<td width="30">
							<? echo $i; ?></td>
                            <td width="120"><div style="word-break:break-all"><? echo $emblishment_name_array[$emb_name];; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $em_type; ?></div></td>
							<td width="100" align="right" title="Tot cons/Plan Cut or PO Qty*12"><div style="word-break:break-all"><? echo number_format($emb_cons_dzn,4); ?></div></td>
                            <td width="100" align="right" ><div style="word-break:break-all"><? echo number_format($cons_dzn_gmts,4); ?></div></td>

                            <td width="100" align="right"><? echo number_format($embl_amount/$cons_dzn_gmts,4); ?></td>
                            <td width="100"  align="right"><div style="word-break:break-all">
							<? $amount=$embl_amount/$total_po_pcs_qty_dzn;
							echo number_format($amount,4); ?> </div></td>

                             <td width=""  valign="middle" align="center" title="">
							<? echo number_format($embl_amount,4); ?></td>
                            <?
							//}
							?>
                            </tr>
                            <?
								$grand_total_embl_amount+=$amount;
								$grand_total_embl_tot_amount+=$embl_amount;
								$grand_total_cons_dzn_gmts+=$emb_cons_dzn;
								$grand_total_cons_tot_gmts+=$cons_dzn_gmts;
								$i++;//$m++;
								}
							}
							?>
                            <tfoot>
                            <tr>
                                <th colspan="3"><strong>Grand Total</strong> </th>

                                <th align="right"><strong><? echo number_format($grand_total_cons_dzn_gmts,4);?></strong></th>
                                  <th align="right"><strong><? echo number_format($grand_total_cons_tot_gmts,4);?></strong></th>

                                <th align="right">&nbsp;</th>
                                <th align="right"><? echo number_format($grand_total_embl_amount,4);?></th>
                                <th align="right"><? echo number_format($grand_total_embl_tot_amount,4); ?> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                   	</div>
                    <div id="" style="width:850px; margin-top:-50px">
				    	<?
                      		echo signature_table(109, $cbo_company_name, "850px");
                        ?>
                        </div>
              </div>

		<?
	}
	else if($reporttype==5 || $reporttype==6) //Quotation Report=> Button 4
	{

			$sql="select a.id  as quotation_id,a.mkt_no,a.sew_smv,a.sew_effi_percent,a.gmts_item_id,a.company_id,a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,a.est_ship_date,b.costing_per_id,b.price_with_commn_pcs,b.total_cost,b.costing_per_id from wo_price_quotation a,wo_price_quotation_costing_mst b  where a.id=b.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.offer_qnty>0 $company_name_cond $quot_style_cond  $buyer_id_cond2 $qoutation_id_cond $season_cond2 order  by a.id ";
			$sql_quot_result=sql_select($sql);
			$all_quot_id="";$mkt_no="";
			foreach($sql_quot_result as $row)
			{
				if($all_quot_id=="") $all_quot_id=$row[csf("quotation_id")]; else $all_quot_id.=",".$row[csf("quotation_id")];
				if($mkt_nos=="") $mkt_nos=$row[csf("mkt_no")]; else $mkt_nos.=",".$row[csf("mkt_no")];
				$style_wise_arr[$row[csf("style_ref")]]['costing_per']=$row[csf("costing_per")];

				$style_wise_arr[$row[csf("style_ref")]]['gmts_item_id']=$row[csf("gmts_item_id")];
				$style_wise_arr[$row[csf("style_ref")]]['sew_smv']=$row[csf("sew_smv")];
				$style_wise_arr[$row[csf("style_ref")]]['sew_effi_percent']=$row[csf("sew_effi_percent")];
				$style_wise_arr[$row[csf("style_ref")]]['shipment_date'].=$row[csf('est_ship_date')].',';
				$style_wise_arr[$row[csf("style_ref")]]['quotation_id']=$row[csf("quotation_id")];
				$style_wise_arr[$row[csf("style_ref")]]['buyer_name']=$row[csf("buyer_id")];
				$offer_qnty_pcs=$row[csf('offer_qnty')]*$row[csf('ratio')];
				$style_wise_arr[$row[csf("style_ref")]]['qty_pcs']+=$row[csf('offer_qnty')]*$row[csf('ratio')];
				$style_wise_arr[$row[csf("style_ref")]]['qty']+=$row[csf('offer_qnty')];
				$style_wise_arr[$row[csf("style_ref")]]['final_cost_pcs']=$row[csf('price_with_commn_pcs')];
				$style_wise_arr[$row[csf("style_ref")]]['total_cost']+=$offer_qnty_pcs*$row[csf('price_with_commn_pcs')];
				$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty']=$row[csf("offer_qnty")];
				$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id']=$row[csf("costing_per_id")];
				$quot_wise_arr[$row[csf("quotation_id")]]['quot_date']=$row[csf("quot_date")];
				//$style_wise_arr[$row[csf("style_ref_no")]]['qty']+=$row[csf('po_quantity')];

			}

			$company_con="";
			if($cbo_company_name!=0) $company_con="and company_id=$cbo_company_name";
			//else if($cbo_style_owner!=0) $company_con="and company_id=$cbo_style_owner";
			//else if($cbo_company_name==0) $company_con=$cbo_company_name;
			$financial_para=array();
			$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date,applying_period_to_date,operating_expn from lib_standard_cm_entry where  status_active=1 and	is_deleted=0 $company_con order by id");
			foreach($sql_std_para as $sql_std_row)
			{
				$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
				$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
				$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];

				$applying_period_date=change_date_format($sql_std_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($sql_std_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);

					$financial_para_arr[$newdate]['operating_expn']=$sql_std_row[csf('operating_expn')];
					if($sql_std_row[csf("income_tax")]>0)
					{
						$financial_para_arr[$newdate]['income_tax']=$sql_std_row[csf('income_tax')];
					}
					if($sql_std_row[csf("interest_expense")]>0)
					{
						$financial_para_arr[$newdate]['interest_expense']=$sql_std_row[csf('interest_expense')];
					}

					//$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			//print_r($financial_para_arr);
				$all_quot_ids=array_unique(explode(",",$all_quot_id));
				$mkt_nos=implode(",",array_unique(explode(",",$mkt_nos)));


			$sql_fab = "select quotation_id,sum(avg_cons) as cons_qnty, sum(amount) as amount from wo_pri_quo_fabric_cost_dtls where quotation_id in(".$all_quot_id.") and fabric_source=2 and status_active=1 group by  quotation_id";
			$data_array_fab=sql_select($sql_fab);
			foreach($data_array_fab as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$fab_order_price_per_dzn=12;}
				else if($costing_per_id==2){$fab_order_price_per_dzn=1;}
				else if($costing_per_id==3){$fab_order_price_per_dzn=24;}
				else if($costing_per_id==4){$fab_order_price_per_dzn=36;}
				else if($costing_per_id==5){$fab_order_price_per_dzn=48;}

				$fab_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				 $fab_summary_data[$row[csf("quotation_id")]]['fab_amount_dzn']+=$row[csf("amount")];
				 $fab_summary_data[$row[csf("quotation_id")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$fab_order_job_qnty;
				//$yarn_amount_dzn+=$row[csf('amount')];
			}
			$sql_yarn = "select quotation_id,sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id in(".$all_quot_id.") and status_active=1 group by  quotation_id";
			$data_array_yarn=sql_select($sql_yarn);
			foreach($data_array_yarn as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
				else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
				else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
				else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
				else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
				$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
				 $yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn']+=$row[csf("amount")];
				// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
				 //$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
			}

			$conversion_cost_arr=array();
			$sql_conversion = "select a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
			from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
			where a.quotation_id in(".$all_quot_id.") and a.status_active=1  ";
			$data_array_conversion=sql_select($sql_conversion);
			foreach($data_array_conversion as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$conv_order_price_per_dzn=12;}
				else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
				else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
				else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
				else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
				$conv_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				//$summary_data['conversion_cost_dzn']+=$row[csf("amount")];
				//$summary_data['conversion_cost_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
				 $conv_summary_data[$row[csf("quotation_id")]]['conv_amount_dzn']+=$row[csf("amount")];

				$conversion_cost_arr[$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
				$conversion_cost_arr[$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
			}
			

			$sql_emblishment=sql_select("SELECT emb_name, quotation_id, cons_dzn_gmts, amount from wo_pri_quo_embe_cost_dtls where quotation_id in(".$all_quot_id.") and cons_dzn_gmts>0 and status_active=1 and is_deleted=0 and emb_name!=3");
			foreach($sql_emblishment as $row)
			{
				$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
				if($costing_per_id==1){$order_price_per_dzn=12;}
				else if($costing_per_id==2){$order_price_per_dzn=1;}
				else if($costing_per_id==3){$order_price_per_dzn=24;}
				else if($costing_per_id==4){$order_price_per_dzn=36;}
				else if($costing_per_id==5){$order_price_per_dzn=48;}
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$emblishment_amount_arr[$row[csf("emb_name")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}

			if($db_type==0)
			{
				$sql = "SELECT MAX(id),quotation_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent,offer_qnty from wo_price_quotation_costing_mst where quotation_id in(".$all_quot_id.") and status_active=1 ";
			}
			if($db_type==2)
			{
				$sql = "SELECT MAX(id),fabric_cost,quotation_id,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent from wo_price_quotation_costing_mst where quotation_id in(".$all_quot_id.") and status_active=1   group by fabric_cost,quotation_id,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent ,certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,interest_pre_cost,interest_po_price,income_tax_pre_cost,income_tax_po_price,total_cost ,total_cost_percent,final_cost_dzn ,final_cost_dzn_percent ,confirm_price_dzn ,confirm_price_dzn_percent,final_cost_pcs,margin_dzn,margin_dzn_percent,a1st_quoted_price,confirm_price,revised_price,price_with_commn_dzn,costing_per_id,design_pre_cost,design_percent,studio_pre_cost,studio_percent";
			}
			//echo $sql;
			$data_array=sql_select($sql);
			$order_price_summ=0;
			$less_commision_summ=0;
			$total_cost_summ=0;
			$margin_summ=0;
			$margin_percent_summ=0;
			$percent=0;
			$price_dzn=0;
            $sl=1;
            foreach( $data_array as $row )
            {
				//$sl=$sl+1;
				if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
				else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
				else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
				else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
				else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
				$price_dzn=$row[csf("confirm_price_dzn")];
  				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];				
				$summary_data['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
				$summary_data['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['price_dzn']+=$row[csf("confirm_price_dzn")];
				//$summary_data[price_dzn_job]+=$job_wise_arr[$row[csf("job_no")]]['po_amount'];//($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
			    $summary_data['commission_dzn']+=$row[csf("commission")];
				$summary_data['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['trims_cost_dzn']+=$row[csf("trims_cost")];
				$summary_data['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['embel_cost_dzn']+=$row[csf("embel_cost")];
				$summary_data['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
				//$row[csf("commission")]
				$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];

				$summary_data['other_direct_dzn']+=$other_direct_expenses;
				$summary_data['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['wash_cost_dzn']+=$row[csf("wash_cost")];
				$summary_data['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['lab_test_dzn']+=$row[csf("lab_test")];
				$summary_data['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['inspection_dzn']+=$row[csf("inspection")];
				$summary_data['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['freight_dzn']+=$row[csf("freight")];
				$summary_data['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				$freight_cost_data[$row[csf("quotation_id")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
				$summary_data['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
				$summary_data['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
				$summary_data['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
				$summary_data['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$quot_studio_cost_dzn_arr[$row[csf("quotation_id")]]['studio_dzn_cost']=$row[csf("studio_percent")];
				$quot_studio_cost_dzn_arr[$row[csf("quotation_id")]]['common_oh']=$row[csf("common_oh")];

				$fab_amount_dzn=$fab_summary_data[$row[csf("quotation_id")]]['fab_amount_dzn'];
				$summary_data['fab_amount_dzn']+=$fab_amount_dzn;
				$summary_data['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				$yarn_amount_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
				//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
				$summary_data['yarn_amount_dzn']+=$yarn_amount_dzn;
				$summary_data['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
				 $conv_amount_dzn=$conv_summary_data[$row[csf("quotation_id")]]['conv_amount_dzn'];
				 $summary_data['conversion_cost_dzn']+=$conv_amount_dzn;
				 $summary_data['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;

				//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
				$net_value_dzn=$row[csf("price_with_commn_dzn")];

				$summary_data['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
				$summary_data['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;

				//yarn_amount_total_value
				$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
				//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
				$summary_data['cost_of_material_service']+=$all_cost_dzn;
				$summary_data['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
				$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
				$summary_data['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
				$summary_data['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['cm_cost_dzn']+=$row[csf("cm_cost")];
				$summary_data['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['comm_cost_dzn']+=$row[csf("comm_cost")];
				$summary_data['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;

				$summary_data['common_oh_dzn']+=$row[csf("common_oh")];
				$summary_data['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
				//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
				$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
				$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
				$summary_data['gross_profit_dzn']+=$tot_gross_profit_dzn;
				$summary_data['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;

				//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
				$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
				$summary_data['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
				$summary_data['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
				$summary_data['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
				$summary_data['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$summary_data['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
				$summary_data['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
				$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
				$summary_data['net_profit_dzn']+=$net_profit_dzn;
				$summary_data['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
			}
			$sql_commi = "select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pri_quo_commiss_cost_dtls
			where  quotation_id in(".$all_quot_id.") and status_active=1 and commission_amount>0";
			$result_commi=sql_select($sql_commi);$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
			foreach($result_commi as $row){

				$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
				$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

				if($row[csf("particulars_id")]==1) //Foreign
					{

						$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
						$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
						$CommiData_foreign_quot_cost_arr[$row[csf("quotation_id")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					else
					{
						$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
						$local_dzn_commission_amount+=$row[csf("commission_amount")];
						$commision_local_quot_cost_arr[$row[csf("quotation_id")]]=$row[csf("commision_rate")];
					}
			}


			$sql_comm="select item_id,quotation_id,sum(amount) as amount from wo_pri_quo_comarcial_cost_dtls where  quotation_id in(".$all_quot_id.") and status_active=1 group by quotation_id,item_id";
			$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;$summary_data['comm_cost_dzn']=0;$summary_data['comm_cost_total_value']=0;
			$result_comm=sql_select($sql_comm);
			foreach($result_comm as $row){

			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
				$comm_amtPri=$row[csf('amount')];
				$item_id=$row[csf('item_id')];
				if($item_id==1)//LC
					{
						$commer_lc_cost+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
						$tot_lc_dzn_Commer+=$row[csf("amount")];
						$commer_lc_cost_quot_arr[$row[csf("quotation_id")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
					}
					else
					{
						$commer_without_lc_cost+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
						$tot_without_lc_dzn_Commer+=$row[csf("amount")];
					}
			}
					$summary_data['comm_cost_dzn']=$tot_without_lc_dzn_Commer;
					$summary_data['comm_cost_total_value']=$commer_without_lc_cost;
					//echo $summary_data['other_direct_total_value'].'=='.$CommiData_lc_cost;;
					//$summary_data['other_direct_total_value']+=$CommiData_lc_cost;



		?>

         <style>

				#page_sign_td3{ margin-top:210px; position:absolute;
				}
				#page_sign_td4{ margin-top:-70px; position:absolute;

				}

		</style>

             <table width="850px" style="margin-left:10px">
             <tr class="form_caption">
                    <td colspan="9" align="center"><strong style=" font-size:18px"><? echo $company_library[$cbo_company_name];?></strong></td>
                </tr>
                <tr>
                    <td align="center"  colspan="9" class="form_caption"><strong style=" font-size:16px"><? echo 'Final Cost Sheet';//$report_title; ?></strong></td>
                </tr>
            </table>
			<div style="float:left;width:850px;margin-left:10px;"><b>SUMMARY:</b></div>
            <table   style="margin-left:10px;width:850px;" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<tr>

                    <thead>
                     	<th width="20"><b>SL</b> </th>
                        <th width="110"><b>Buyer</b> </th>
                        <th width="120"><b>Item</b> </th>
                        <th width="80"><b>Ship Date</b> </th>
                        <th width="120"><b>Style</b> </th>
                        <th width="50"><b>Quot. No</b> </th>
						<th width="50"><b>SMV</b> </th>
						<th width="50"><b>Sew Effi.%</b> </th>
                        <th width="80"><b>Qty.</b> </th>
                        <th width="80"><b>Qty.(PCS)</b> </th>
                        <th width="60"><b>FOB</b> </th>
                        <th width="80"><b>Total Amount</b> </th>
                    </thead>
                    <?
					$k=1;$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=0;
					$all_last_shipdates='';
                    foreach($style_wise_arr as $style_key=>$val)
					{
						 if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 $gmts_item_id=$val[('gmts_item_id')];
						 $shipment_date=rtrim($val[('shipment_date')],',');
						  $shipment_dates=array_unique(explode(",",$shipment_date));
						   $last_shipmentdates=max($shipment_dates);
						   $all_last_shipdates.=$last_shipmentdates.',';
							$gmts_item=''; $gmts_item_id=explode(",",$gmts_item_id);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}
					?>
                	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
                        <td width="20"><p> <? echo $k;?></p></td>
                        <td width="110"><p> <? echo $buyer_arr[$val[('buyer_name')]];?></p></td>
                        <td  width="120"><p>  <? echo $gmts_item;?></p></td>
                        <td width="80"><p> <? echo change_date_format($last_shipmentdates);?></p></td>
                        <td width="120"><p> <? echo $style_key;?></p></td>
                        <td width="50"><p> <? echo $val[('quotation_id')];?></p></td>
						<td width="50" align="right"><p> <? echo $val[('sew_smv')];?></p></td>
						<td width="50" align="right"><p> <? echo $val[('sew_effi_percent')].'%';?></p></td>
                        <td width="80" align="right"><p> <? echo number_format($val[('qty')],0);?></p></td>
                        <td  width="80" align="right">  <? echo  number_format($val[('qty_pcs')],0);?></td>
                        <td width="60" align="right"><p> <? echo number_format($val[('final_cost_pcs')],4);?></p></td>
                        <td width="80" align="right"><p> <?
						$total_cost=$val[('qty')]*$val[('final_cost_pcs')];echo number_format($total_cost,2);
						//echo number_format($val[('total_cost')],2);?></p></td>
                    </tr>
                    <?
					$k++;
					$total_quot_qty+=$val[('qty')];
					$total_quot_pcs_qty+=$val[('qty_pcs')];$total_sew_smv+=$val[('sew_smv')];
					$total_quot_amount+=$total_cost;
					$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
					}
					?>
                    <tfoot>
                     <tr>
                    <td align="right" colspan="3">  <b>Qty DZN </b></td>
                    <td align="right"> &nbsp; <? $total_po_qty_dzn=$total_quot_pcs_qty/12;echo number_format($total_po_qty_dzn,2);?></td>
					<td align="right">&nbsp;</td>
					<td align="right">  <b>Total</b></td>
					<td align="right"><b><? echo number_format($total_sew_smv,0);?> </b></td>
					<td align="right">&nbsp;</td>
					<td align="right"><b> &nbsp; <? echo number_format($total_quot_qty,0);?> </b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_quot_pcs_qty,0);?> </b></td>
					<td align=""><b> &nbsp;</b></td>
					<td align="right"><b> &nbsp; <? echo number_format($total_quot_amount,2);?> </b></td>
                    </tr>

                    <tr>
                    <td align="right" colspan="3">  <b>Last Shipment Date </b></td>
                    <td align="right"> &nbsp; <?
					$all_last_ship_dates=rtrim($all_last_shipdates,',');
					 $all_last_ship_dates=array_unique(explode(",",$all_last_ship_dates));
					 $last_shipment_dates=max($all_last_ship_dates);
					 echo change_date_format($last_shipment_dates);?></td>
					 <td align="right">&nbsp;</td>
					 <td align="right">&nbsp;</td>
					 <td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>

					 <td align="right" colspan="3"><b>Foreign Commission &nbsp; <? $foreign_percent_rate=($CommiData_foreign_cost/$total_quot_amount)*100;
					 echo number_format($foreign_percent_rate,2).'%';?> </b></td>
					<td align="right"><b> &nbsp; <? echo number_format($CommiData_foreign_cost,2);?> </b></td>
                    </tr>

                     <tr>
                    <td align="right" colspan="3" title="Total Quotation Value-Commission">  <b>Maximum BTB LC-70% </b></td>
                    <td align="right" title="Commission=<? echo $summary_data['commission_dzn'];?>"> &nbsp; <?
					$net_fob_value=$total_quot_amount-$summary_data['commission_dzn'];
					 echo number_format(($net_fob_value*70)/100,2);?></td>
					  <td align="right">&nbsp;</td>
					 <td align="right">&nbsp;</td>
					 <td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>

					  <td align="right" colspan="3"><b>Freight Cost &nbsp; <?
					 $freight_percent_rate=($summary_data['freight_total_value']/$total_quot_amount)*100;
					 echo number_format($freight_percent_rate,2).'%';?> </b></td>
					<td align="right"><b> &nbsp; <?
					$pri_freight_cost_per=$summary_data['freight_total_value'];
					echo number_format($pri_freight_cost_per,2);?> </b></td>

                    </tr>
					<tr>
					  	 <td align="right" colspan="8"> </td>

						  <td align="right" colspan="3" title="<? echo $CommiData_foreign_cost;?>"><b>LC Cost &nbsp; <?
						  $commar_rate_percent=($commer_lc_cost/$total_quot_amount)*100;
						  echo number_format($commar_rate_percent,2).'%';?> </b></td>
						<td align="right"><b> &nbsp; <?
						$pri_commercial_per=$commer_lc_cost;echo number_format($pri_commercial_per,2);
						$tot_quot_sum_amount=$total_quot_amount-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
						//echo '='.$tot_quot_sum_amount;
						?> </b></td>
					  </tr>

                    <tr>
                    <td colspan="4" align="left"> <b style="float:left">Mkt No:&nbsp; <? echo $mkt_nos; ?> </b></td>
                    <td colspan="7" align="right"> <b> Total </b></td>

                    <td align="right"> <b> <? echo number_format($tot_quot_sum_amount,2);?> </b></td>
                    </tr>
                    </tfoot>

           </table>
           <br>
            <div style="width:100%">
            <? $tot_operating_expense=$total_quot_income_tax_val=$total_quot_interest_exp_val=$total_quot_amount_val=$tot_qout_studio_cost=$total_quot_commision_local_val=$total_quot_net_amount_cal=0;
			foreach($all_quot_ids as $qid)
			{
				$quot_date=$quot_wise_arr[$qid]['quot_date'];
				$pri_quot_date=change_date_format($quot_date,'','',1);
				$freight_total_value=$freight_cost_data[$qid]['freight_total_value'];
				$total_quot_amount_cal=$total_quot_amount_arr[$qid];
				$studio_dzn_cost=$quot_studio_cost_dzn_arr[$qid]['studio_dzn_cost'];
				$common_oh_dzn_cost=$quot_studio_cost_dzn_arr[$qid]['common_oh'];
				$commision_quot_local=$commision_local_quot_cost_arr[$qid];

				$CommiData_foreign_quot_cost=$CommiData_foreign_quot_cost_arr[$qid];
				$commer_lc_cost_quot=$commer_lc_cost_quot_arr[$qid];

				$operating_expn=0;
				$operating_expn=$financial_para_arr[$pri_quot_date]['operating_expn'];
				$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost+$commer_lc_cost_quot+$freight_total_value);
				$tot_operating_expense+=($tot_sum_amount_quot_calc*$operating_expn)/100;
				$tot_qout_studio_cost+=($tot_sum_amount_quot_calc*$studio_dzn_cost)/100;
				//echo $tot_sum_amount_quot_calc.'='.$studio_dzn_cost.',';

				$income_tax=$financial_para_arr[$pri_quot_date]['income_tax'];
				$interest_expense=$financial_para_arr[$pri_quot_date]['interest_expense'];
				//echo $total_quot_amount_cal.'='.$commision_quot_local.',';
				$total_quot_income_tax_val+=($total_quot_amount_cal*$income_tax)/100;
				$total_quot_interest_exp_val+=($total_quot_amount_cal*$interest_expense)/100;
				$total_quot_commision_local_val+=($tot_sum_amount_quot_calc*$commision_quot_local)/100;
				$total_quot_amount_val+=$total_quot_amount_arr[$qid];
				$total_quot_net_amount_cal+=$tot_sum_amount_quot_calc;

			}
			//echo $tot_operating_expense;
			$tot_income_tax_dzn=($total_quot_income_tax_val/$total_quot_amount_val)*12;
			$tot_interest_exp_dzn=($total_quot_interest_exp_val/$total_quot_amount_val)*12;

			$tot_commision_local_dzn=($total_quot_commision_local_val/$total_quot_net_amount_cal)*12;
			//	echo $tot_commision_local_dzn.'='.$total_quot_commision_local_val.'='.$total_quot_net_amount_cal;
			$summary_data['other_direct_total_value']+=$tot_qout_studio_cost+$total_quot_commision_local_val;
			//echo $total_quot_commision_local_val.'dd';
			$summary_data['other_direct_dzn']+=$tot_commision_local_dzn;
					$summary_data['commission_total_value']=0;
					$summary_data['commission_total_value']=$total_quot_commision_local_val;
            if($comments_head!=1)
			{
		   ?>
           <div>
           <div style="margin-left:10px">
		   <div style="float:left;width:700px;margin-left:10px;"><b>Quotation Profitability:</b></div>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:700px;text-align:center;" rules="all">

				<thead>
                <tr style="font-weight:bold">
                    <th width="20">Line Items</th>
                    <th width="">Particulars</th>
                    <th width="100">Amount (USD)/ DZN</th>
                    <th width="100">Total Value( Offer Qnty)</th>
                    <th width="50">%</th>
                </tr>
				</thead>
                    <tr>
                    <td  width="20"><? echo $sl;?></td>
                    <td align="left"><b>Net FOB Value</b></td>
                    <td align="right"><b><?
						$summary_data['price_with_commn_dzn']=0;$summary_data['price_with_commn_dzn']=($tot_quot_sum_amount/$total_quot_pcs_qty)*12;
					echo number_format($summary_data['price_with_commn_dzn'],4); $order_price_summ=$summary_data['price_with_commn_dzn'];?></b></td>
                    <td align="right"><? $NetFOBValue_job=$tot_quot_sum_amount;
					$summary_data['price_with_total_value']=$tot_quot_sum_amount;
					echo number_format($summary_data['price_with_total_value'],2); ?></td>
                    <td align="right"><? echo "100.00"; ?></td>
                </tr>

                 <tr>
                    <td  width="20"><? echo ++$sl; ?></td>
                    <td align="left"><b>Total Cost of Material & Services (3+4+5+6+7) </b></td>
                    <td align="right"><b><?
					$summary_data['studio_pre_cost_total_value']=0;
					$summary_data['studio_pre_cost_total_value']=$tot_qout_studio_cost;
					$summary_data['other_direct_total_value']=0;
				  $summary_data['other_direct_total_value']=$summary_data['wash_cost_total_value']+$summary_data['lab_test_total_value']+$summary_data['inspection_total_value']+$summary_data['currier_pre_cost_total_value']+$summary_data['commission_total_value']+$summary_data['certificate_pre_cost_total_value']+$summary_data['studio_pre_cost_total_value']+$summary_data['design_pre_cost_total_value'];
				   $summary_data['cost_of_material_service_total_value']=0;

				   $summary_data['cost_of_material_service_total_value']=$summary_data['fab_amount_total_value']+$summary_data['yarn_amount_total_value']+$summary_data['conversion_cost_total_value']+$summary_data['trims_cost_total_value']+$summary_data['embel_cost_total_value']+$summary_data['other_direct_total_value'];
				  // echo $summary_data['yarn_amount_total_value'].'=',$summary_data['conversion_cost_total_value'].'=',$summary_data['trims_cost_total_value'].'=',$summary_data['embel_cost_total_value'].'=',$summary_data['other_direct_total_value'];
					$cost_of_material_service_total_value=$summary_data['cost_of_material_service_total_value'];
					$LessCostOfMaterialServices=$summary_data['cost_of_material_service'];echo number_format($cost_of_material_service_total_value/$total_po_qty_dzn,4); ?></b></td>
                    <td align="right"><? echo number_format($cost_of_material_service_total_value,2); ?></td>
                    <td align="right"><b><? echo number_format(($summary_data['cost_of_material_service_total_value']/$summary_data['price_with_total_value'])*100,2)?></b></td>
                 </tr>
                  <tr>
                            <td rowspan="2"><? echo ++$sl; ?></td>
                            <td align="left" style=" padding-left:100px;font-weight:bold">Fabric Purchase Cost</td>
                            <td  align="right" style="font-weight:bold"> <? echo number_format( $summary_data['fab_amount_total_value']/$total_po_qty_dzn,4); ?></td>
                            <td  align="right" style="font-weight:bold"> <? echo number_format( $summary_data['fab_amount_total_value'],2); ?></td>
                            <td  align="right" style="font-weight:bold"><? echo number_format(($summary_data['fab_amount_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                  </tr>
                <tr>

                    <td align="left" style=" padding-left:100px"><strong>Yarn Cost</strong></td>
                    <td align="right"><? $yarn_amount_dzn=$summary_data['yarn_amount_total_value']/$total_po_qty_dzn;echo number_format($yarn_amount_dzn,4); ?></td>
                    <td align="right"><? echo number_format($summary_data['yarn_amount_total_value'],2); ?></td>
                    <td align="right"><? echo number_format(($summary_data['yarn_amount_total_value']/$summary_data['price_with_total_value'])*100,2)?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px">
                    <strong>Conversion Cost</strong>
                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="180"> <? echo $conversion_cost_head_array[$key]; ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                    <td align="right">
					<? $conversion_cost_dzn=$summary_data['conversion_cost_total_value']/$total_po_qty_dzn;echo number_format($conversion_cost_dzn,4); ?>

                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="98" align="right"><? echo number_format(($value['conv_amount_total_value']/$total_po_qty_dzn),4); ?></td></tr>
                    <? }?>
                    </table>

                    </td>
                    <td align="right">
                    <? echo number_format($summary_data['conversion_cost_total_value'],2); ?>

                    <table class="rpt_table" border="1" rules="all">
                    <?
					$tot_dye_chemi_process_amount=$tot_yarn_dye_process_amount=$tot_aop_process_amount=0;
					foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="98" align="right"><?
						if($key==101) //Dye/Chemical
						{
						$tot_dye_chemi_process_amount+=$value['conv_amount_total_value'];
						}
						else if($key==30) //Y/D
						{
						$tot_yarn_dye_process_amount+=$value['conv_amount_total_value'];
						}
						else if($key==35) //AOP
						{
						$tot_aop_process_amount+=$value['conv_amount_total_value'];
						}
						echo number_format($value['conv_amount_total_value'],2); ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                    <td align="right">
					<? echo number_format(($summary_data['conversion_cost_total_value']/$summary_data['price_with_total_value'])*100,2)?>
                    <table class="rpt_table" border="1" rules="all">
                    <? foreach ($conversion_cost_arr as $key => $value){?>
                    <tr><td width="50" align="right"> <? echo number_format(($value['conv_amount_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <? }?>
                    </table>
                    </td>
                </tr>

                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px"><strong>Trims Cost</strong></td>
                    <td align="right"><? echo number_format(($summary_data['trims_cost_total_value']/$total_po_qty_dzn),4); ?></td>
                    <td align="right"><? echo number_format($summary_data['trims_cost_total_value'],2); ?></td>
                    <td align="right"><? echo number_format(($summary_data['trims_cost_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px"><strong>Embellishment Cost</strong></td>
                    <td align="right"><? echo number_format(($summary_data['embel_cost_total_value']/$total_po_qty_dzn),4); ?></td>
                    <td align="right"><? echo number_format($summary_data['embel_cost_total_value'],2); ?></td>
                    <td align="right"><? echo number_format(($summary_data['embel_cost_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td align="left" style=" padding-left:100px">
                    <strong>Other Direct Expenses</strong>

                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="180"> Washing Cost (Gmt.)</td></tr>
                    <tr><td width="180"> Lab Test</td></tr>
                    <tr><td width="180"> Inspection Cost</td></tr>
                 <!--   <tr><td width="180"> Freight Cost</td></tr>-->
                    <tr><td width="180"> Currier Cost</td></tr>
                    <tr><td width="180"> Commision Cost(Local)</td></tr>
                    <tr><td width="180"> Certificate Cost</td></tr>
                     <tr><td width="180"> S.Cost</td></tr>
                    <tr><td width="180"> Design Cost</td></tr>

                    </table>
                    </td>
                    <td align="right">
					<?


					$other_direct_expense=$summary_data['other_direct_total_value']/$total_po_qty_dzn;echo  number_format($other_direct_expense,4);
					?>
                    <table class="rpt_table" border="1" rules="all">
                        <tr><td width="98" align="right"> <? echo number_format(($summary_data['wash_cost_total_value']/$total_po_qty_dzn),4); ?></td></tr>
                        <tr><td width="98" align="right"> <? echo number_format(($summary_data['lab_test_total_value']/$total_po_qty_dzn),4); ?></td></tr>
                        <tr><td width="98" align="right"> <? echo number_format(($summary_data['inspection_total_value']/$total_po_qty_dzn),4); ?></td></tr>
                        <!--<tr><td width="98" align="right"> <? //echo number_format($summary_data['freight_dzn'],4); ?></td></tr>-->
                        <tr><td width="98" align="right"> <? echo number_format(($summary_data['currier_pre_cost_total_value']/$total_po_qty_dzn),4); ?></td></tr>
                        <tr>
                            <td width="98" align="right">
								<?
									//$summary_data['commission_total_value']=$total_quot_commision_local_val;
									$summary_data['commission_dzn']=$summary_data['commission_total_value']/$total_po_qty_dzn;
									if($summary_data['commission_total_value']>0) echo number_format($summary_data['commission_dzn'],4);else echo "&nbsp;";


								?>
                            </td>
                        </tr>
                        <tr><td width="98" align="right"> <? echo number_format(($summary_data['certificate_pre_cost_total_value']/$total_po_qty_dzn),4); ?></td></tr>
                        <tr><td width="98" align="right"> <? echo number_format(($summary_data['studio_pre_cost_total_value']/$total_po_qty_dzn),4); ?></td></tr>
                        <tr><td width="98" align="right"> <? echo number_format(($summary_data['design_pre_cost_total_value']/$total_po_qty_dzn),4); ?></td></tr>

                    </table>
                    </td>
                    <td align="right">
                   <?

				   echo number_format($summary_data['other_direct_total_value'],2); ?>
                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['wash_cost_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['lab_test_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['inspection_total_value'],2); ?></td></tr>
                   <!-- <tr><td width="98" align="right"> <? //echo number_format($summary_data['freight_total_value'],4); ?></td></tr>-->
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['currier_pre_cost_total_value'],2); ?></td></tr>
                     <tr><td width="98" align="right" title="Net Total Value*LC Commision Rate/100"><? echo number_format($summary_data['commission_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['certificate_pre_cost_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"><?
					 echo number_format($summary_data['studio_pre_cost_total_value'],2); ?></td></tr>
                    <tr><td width="98" align="right"> <? echo number_format($summary_data['design_pre_cost_total_value'],2); ?></td></tr>

                    </table>
                    </td>
                    <td align="right">
					<? echo number_format(($summary_data['other_direct_total_value']/$summary_data['price_with_total_value'])*100,2); ?>
                    <table class="rpt_table" border="1" rules="all">
                    <tr><td width="50" align="right"> <? echo number_format(($summary_data['wash_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="50" align="right"> <? echo number_format(($summary_data['lab_test_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="50" align="right"> <? echo number_format(($summary_data['inspection_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                   <!-- <tr><td width="98" align="right"> <? //echo number_format(($summary_data['freight_total_value']/$summary_data['price_with_total_value'])*100,4); ?></td></tr>-->
                    <tr><td width="50" align="right"> <? echo number_format(($summary_data['currier_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                     <tr><td width="50" align="right"> <? echo number_format(($summary_data['commission_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="50" align="right"> <? echo number_format(($summary_data['certificate_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="50" align="right"> <? echo number_format(($summary_data['studio_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>
                    <tr><td width="50" align="right">  <? echo number_format(($summary_data['design_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td></tr>

                    </table>

                    </td>
                </tr>
                <tr>
                     <td><? echo ++$sl; ?></td>
                    <td width="" align="left" style="font-weight:bold">Contributions/Value Additions (1-2)</td>
                    <td width="100" align="right" style="font-weight:bold"> <?
					$summary_data['contribution_margin_total_value']=0;$summary_data['contribution_margin_total_value']=$summary_data['price_with_total_value']-$cost_of_material_service_total_value;
					$Contribution_Margin=$summary_data['contribution_margin_total_value']/$total_po_qty_dzn;echo number_format($Contribution_Margin,4); ?></td>
                    <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['contribution_margin_total_value'],2); ?></td>
                    <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['contribution_margin_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
               </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td width="" align="left" style=" padding-left:15px">Less: CM Cost </td>
                    <td width="100" align="right"><? echo number_format(($summary_data['cm_cost_total_value']/$total_po_qty_dzn),4); ?> </td>
                    <td width="100" align="right"><? echo number_format($summary_data['cm_cost_total_value'],2); ?></td>
                    <td width="50" align="right"><? echo number_format(($summary_data['cm_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
              </tr>
               <tr>
                    <td><? echo ++$sl; ?></td>
                    <td width="" align="left" style="font-weight:bold">Gross Profit (8-9)</td>
                    <td width="100" align="right" style="font-weight:bold"> <?
					$summary_data['gross_profit_total_value']=0;$summary_data['gross_profit_total_value']=$summary_data['contribution_margin_total_value']-$summary_data['cm_cost_total_value'];
					 $Gross_Profit=$summary_data['gross_profit_total_value']/$total_po_qty_dzn; echo number_format($Gross_Profit,4); ?></td>
                    <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['gross_profit_total_value'],2); ?></td>
                    <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['gross_profit_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>

                <tr>
                   <td><? echo ++$sl; ?></td>
                    <td width="" align="left" style=" padding-left:15px">Less: Commercial Cost(Without LC Cost)</td>
                    <td width="100" align="right"><?
					echo number_format(($summary_data['comm_cost_total_value']/$total_po_qty_dzn),4); ?></td>
                    <td width="100" align="right"><? echo number_format($summary_data['comm_cost_total_value'],2); ?></td>
                    <td width="50" align="right"><? echo number_format(($summary_data['comm_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>
                <tr>
                    <td><? echo ++$sl; ?></td>
                    <td width="" align="left" style=" padding-left:15px">Less: Operating Expensees/Maintance</td>

                    <td width="100" align="right"><?
					$summary_data['common_oh_total_value']=0;
					$summary_data['common_oh_total_value']=$tot_operating_expense;
					echo number_format( ($summary_data['common_oh_total_value']/$total_po_qty_dzn),4); ?></td>
                    <td width="100" align="right"><? echo number_format( $summary_data['common_oh_total_value'],2); ?></td>
                    <td width="50" align="right"><? echo number_format(( $summary_data['common_oh_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>

                  <tr>
                       <td><? echo ++$sl; ?></td>
                        <td width="" align="left" style="font-weight:bold">Operating Profit/ Loss (10-(11+12))</td>
                        <td width="100" align="right" style="font-weight:bold"> <?

						 $operating_profit_loss_total=$summary_data['gross_profit_total_value']-($commer_without_lc_cost+$summary_data['common_oh_total_value']);
						$OperatingProfitLoss=$operating_profit_loss_total/$total_po_qty_dzn;echo number_format($OperatingProfitLoss,4); ?></td>
                        <td width="100" align="right" style="font-weight:bold"><?
						echo number_format($operating_profit_loss_total,2); ?></td>
                        <td width="50" align="right" style="font-weight:bold"><? echo number_format(($operating_profit_loss_total/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>
                <tr>
                    <td width="20"><? echo ++$sl; ?></td>
                    <td width="" align="left" style=" padding-left:15px">Less: Depreciation & Amortization </td>
                    <td width="100" align="right"> <? echo number_format(($summary_data['depr_amor_pre_cost_total_value']/$total_po_qty_dzn),4); ?></td>
                    <td width="100" align="right"><? echo number_format($summary_data['depr_amor_pre_cost_total_value'],2); ?></td>
                    <td width="50" align="right"><? echo number_format(($summary_data['depr_amor_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>
               <tr>
                        <td width="20"><? echo ++$sl; ?></td>
                        <td width="" align="left" style=" padding-left:15px">Less: Interest </td>
                        <td width="100" align="right"> <? $summary_data['interest_pre_cost_dzn']=0;$summary_data['interest_pre_cost_total_value']=0;
						$summary_data['interest_pre_cost_dzn']=$summary_data['interest_pre_cost_total_value']/$total_po_qty_dzn;$summary_data['interest_pre_cost_total_value']=$total_quot_interest_exp_val;
						echo number_format($summary_data['interest_pre_cost_dzn'],4); ?></td>
                        <td width="100" align="right"><? echo number_format($summary_data['interest_pre_cost_total_value'],2); ?></td>
                        <td width="50" align="right"><? echo number_format(($summary_data['interest_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>               </tr>
               <tr>
                       <td width="20"><? echo ++$sl; ?></td>
                        <td width="" align="left" style=" padding-left:15px">Less: Income Tax</td>
                        <td width="100" align="right"> <?

						$summary_data['income_tax_pre_cost_dzn']=0;$summary_data['income_tax_pre_cost_total_value']=0;
						$summary_data['income_tax_pre_cost_total_value']=$total_quot_income_tax_val;
						$summary_data['income_tax_pre_cost_dzn']=$summary_data['income_tax_pre_cost_total_value']/$total_po_qty_dzn;

						echo number_format($summary_data['income_tax_pre_cost_dzn'],4); ?></td>
                        <td width="100" align="right"><? echo number_format($summary_data['income_tax_pre_cost_total_value'],2); ?></td>
                        <td width="50" align="right"><? echo number_format(($summary_data['income_tax_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                </tr>
                <tr>

               <td width="20"><? echo ++$sl; ?></td>
                <td width="" align="left" style="font-weight:bold">Net Profit (13-(14+15+16))</td>
                <td width="100" align="right" style="font-weight:bold"><?
				$summary_data['net_profit_dzn_total_value']=0;
				$summary_data['net_profit_dzn_total_value']= $operating_profit_loss_total-($summary_data['depr_amor_pre_cost_total_value']+$summary_data['interest_pre_cost_total_value']+$summary_data['income_tax_pre_cost_total_value']);
				$Netprofit=$summary_data['net_profit_dzn_total_value']/$total_po_qty_dzn;echo number_format($Netprofit,4); ?> </td>
                <td width="100" align="right" style="font-weight:bold"><? echo number_format($summary_data['net_profit_dzn_total_value'],2); ?></td>
                <td width="50" align="right" style="font-weight:bold"><? echo number_format(($summary_data['net_profit_dzn_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
               </tr>

           		 </table>
      			</div> <!--Quotation End-->
                        <br/>
						<div style="width:470px;">
						 <div style="float:left;width:470px;margin-left:10px;"><b>Quotation Summary:</b></div>
                         <table width="470px" style="margin-left:10px;" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  rules="all">

                          <thead>
                               <th  align="center"><strong>Line</strong></th>
							    <th  align="center"><strong>Particulars</strong></th>
                                <th  align="center"><strong>Total Value</strong></th>
                                <th  align="center"><strong>%</strong></th>
                           </thead>
                            <tr>
                                 <td width="20" align="center">1</td>
								<td width="230" align="left">Yarn Cost</td>
                                <td width="100" align="right"><? echo number_format($summary_data['yarn_amount_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($summary_data['yarn_amount_total_value']/$summary_data['price_with_total_value'])*100,2);
								 ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">2</td>
								<td width="230" align="left">Fabric Purchase</td>
                                <td width="100" align="right"><? echo number_format( $summary_data['fab_amount_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($summary_data['fab_amount_total_value']/$summary_data['price_with_total_value'])*100,2);
								 ?></td>
                            </tr>
                             <tr>
                               	 <td width="20" align="center">3</td>
							    <td width="230" align="left">Dyes & Chemical</td>
                                <td width="100" align="right"><? echo number_format( $tot_dye_chemi_process_amount,2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($tot_dye_chemi_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">4</td>
								<td width="230" align="left">Y/D.</td>
                                <td width="100" align="right"><? echo number_format( $tot_yarn_dye_process_amount,2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($tot_yarn_dye_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center">5</td>
								<td width="230" align="left">AOP</td>
                                <td width="100" align="right"><? echo number_format($tot_aop_process_amount,2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($tot_aop_process_amount/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                            <tr>
                                 <td width="20" align="center">6</td>
								<td width="230" align="left">Accessories</td>
                                <td width="100" align="right"><? echo number_format($summary_data['trims_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($summary_data['trims_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
                            <tr>
                                 <td width="20" align="center">7</td>
								<td width="230" align="left">Commercial[without LC]</td>
                                <td width="100" align="right"><? echo number_format($summary_data['comm_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format( ($summary_data['comm_cost_total_value']/$summary_data['price_with_total_value'])*100,2); ?></td>
                            </tr>
							<?
							if(count($emblishment_amount_arr)>0){
								$i=8;
							}
							else{
								$i=7;
							}							
							foreach($emblishment_amount_arr as $name=>$value){ 

							?>
								<tr>
                                 	<td width="20" align="center"><?= $i; ?></td>
									<td width="130" align="left"><?= $emblishment_name_array[$name]  ?></td>
									<td width="100" align="right"><? //$tot_emblish_cost=$summary_data['embel_cost_total_value'];
									echo number_format($value,2);$tot_emblishment_Value+=$value; ?></td>
									<td width="50"  align="right"><? echo number_format(($value/$summary_data['price_with_total_value'])*100,2);?></td>
                            	</tr>
							<?
								$i++;
							}
							?>
                             <tr>
                                <td width="20" align="center"><?= $i++?></td>
							    <td width="230" align="left">Lab Test</td>
                                <td width="100" align="right"><? echo number_format($summary_data['lab_test_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($summary_data['lab_test_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							 <tr>
                                <td width="20" align="center"><?= $i++?></td>
							    <td width="230" align="left">Operating Expensees/Maintance</td>
                                <td width="100" align="right"><? echo number_format($summary_data['common_oh_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($summary_data['common_oh_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							<tr>
                                <td width="20" align="center"><?= $i++?></td>
							    <td width="230" align="left">S.Cost</td>
                                <td width="100" align="right"><? echo number_format($summary_data['studio_pre_cost_total_value'],2); ?></td>
                                <td width="50"  align="right"><? echo number_format(($summary_data['studio_pre_cost_total_value']/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
							  <tr>
                                 <td width="20" align="center"><?= $i++?></td>
								<td width="230" align="left">Inspection/Courier/Commission(Local)/Certificate/Design</td>
                                <td width="100" align="right" title=""><?
								$tot_inspect_cour_certi_cost=$summary_data['inspection_total_value']+$summary_data['currier_pre_cost_total_value']+$summary_data['certificate_pre_cost_total_value']+$summary_data['commission_total_value']+$summary_data['design_pre_cost_total_value'];
								echo number_format($tot_inspect_cour_certi_cost,2);?></td>
                                <td width="50"  align="right"><? echo number_format(($tot_inspect_cour_certi_cost/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>

                             <tr>
                                 <td width="20" align="center"><?= $i++?></td>
								<td width="230" align="left">Total for BTB</td>
                                <td width="100" align="right"  title="Lab Test+Emblish Cost+Cmmercial Cost+Trims Cost+Yarn Dye Process Cost+Dye Chemical+Yarn Cost+AOP Cost+Purchase Cost+S.Cost+(Inspection/Courier/Commission(Local)/Certificate/Design)"><?
									$total_btb=$summary_data['lab_test_total_value']+$tot_emblishment_Value+$summary_data['comm_cost_total_value']+$summary_data['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data['common_oh_total_value']+$summary_data['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost+$summary_data['fab_amount_total_value'];
								 echo number_format($total_btb,2);?></td>
                                <td width="50"  align="right"><? echo number_format(($total_btb/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>
                             <tr>
                                 <td width="20" align="center"><?= $i++?></td>
								<td width="230" align="left">CM for Fabrics (Knitting & Dyeing Charge )</td>
                                <td width="100" align="right" title="Tot Conversion Cost-(Y/D+Dye & Chemical+AOP)"><?
								$tot_cm_for_fab_cost=$summary_data['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
								echo number_format($tot_cm_for_fab_cost,2);?></td>
                                <td width="50"  align="right"><? echo number_format(($tot_cm_for_fab_cost/$summary_data['price_with_total_value'])*100,2);?></td>
                            </tr>


                             <tr>
                             <?
                             $total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);
							 ?>
                                 <td width="20" align="center"><?= $i++?></td>
								<td width="230" align="left">CM for Garments&nbsp;(CM Dzn=<? echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2);?>)</td>
                                <td width="100" align="right" title="Gross FOB Value-Tot CM Fab Cost Cost-Total BTB-Inspect-Freight-Courier-Certificate-Commission"><?

								 echo number_format($total_cm_for_gmt,2);?></td>
                                <td width="50"  align="right"><? echo number_format(($total_cm_for_gmt/$NetFOBValue_job)*100,2);?></td>
                            </tr>
                            <tfoot>
							 <tr style="font-size: medium; border:thick">
							  <td width="20" align="center"><?= $i++?></td>
                                <td width="230" align="left"><b>Net Quotation Value </b></td>
                                <td width="100" align="right"><b><? echo number_format($NetFOBValue_job,2);?></b></td>
                                <td width="50"  align="right"><b><? echo number_format(($NetFOBValue_job/$summary_data['price_with_total_value'])*100,2);?></b></td>
                            </tr>
						</tfoot>

                        </table>
						</div>
                        <br/>
                        <div id="" style="width:850px;">
                         <br/><br/>
				    <?
                      		echo signature_table(109, $cbo_company_name, "850px");
                        ?>
                        </div>

						</div>

                          <?
					}
                   if($comments_head==1)
					{
						?>

                   <div>
                    <br/>
                    <br/>
                	   <table id="table_header_1" style="margin-left:10px" class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
           				<caption> <b style="float:left">Fabric Details :</b></caption>
						<thead>
                    	<th width="30">SL</th>
                        <th width="100">Fab. Nature</th>
						<th width="200">Description</th>
						<th width="100">Source</th>
						<th width="100">Fab. Cons/ DZN</th>
                        <th width="100">Total Cons.</th>
                        <th width="100">Rate</th>
                        <th width="100">Amount/ DZN</th>
                         <th width="">Tot Amount(USD)</th>
                    </thead>
           	 </table>
                    <div style="width:950px;margin-left:10px" align="left" id="scroll_body1">
					<table class="rpt_table"   width="930" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <?
				$sql_fab="select b.id,b.item_number_id,b.quotation_id, b.body_part_id as body_id, b.fab_nature_id as nat_id, b.color_type_id as color_type, b.fabric_description as fab_desc, b.avg_cons,b.fabric_source as fab_source, b.rate, b.amount,b.avg_finish_cons from wo_pri_quo_fabric_cost_dtls b  where b.status_active=1 and b.is_deleted=0 and b.quotation_id in(".$all_quot_id.") order  by b.id,b.fab_nature_id, b.fabric_description";
				  $sql_fabs_result=sql_select($sql_fab);
				  $fabric_detail_arr=array();  $fabric_job_check_arr=array();
				$total_purchase_amt=0;
				foreach($sql_fabs_result as $row)
				{
					$row[csf("fab_source")]=$row[csf("fab_source")];
					$qout_offer_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
					$fab_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

					$item_desc= $body_part[$row[csf("body_id")]].",".$color_type[$row[csf("color_type")]].",".$row[csf("fab_desc")];
					$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['amount']+=$row[csf("amount")];
					$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['rate']=$row[csf("rate")];
					$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['avg_cons']+=$row[csf("avg_cons")];
					$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['rate']=$row[csf("rate")];
					$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['offer_qty']=$qout_offer_qnty;
					$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['fab_per_dzn']=$fab_per_dzn;
					$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['offer_qty_dzn']+=$qout_offer_qnty;

					$fabric_detail_arr[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['quotation_id'].=$row[csf("quotation_id")].',';

					$new_array[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['req_fab_total'] +=(($row[csf("avg_cons")]/$fab_per_dzn)*$qout_offer_qnty);
					$new_array[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['req_fab_total_value'] +=(($row[csf("amount")]/$fab_per_dzn)*$qout_offer_qnty);
					$new_array[$row[csf("nat_id")]][$item_desc][$row[csf("fab_source")]]['req_fab_total_value_dzn'] +=$row[csf("amount")];

					//$fabric_amt=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
					//echo $fabric_amt.',';
				}

				 // print($fabric_btb_amt);
								//print_r($fabric_detail_arr);die;
								//echo $total_fob_value.'/'.$total_order_qty;
				$styleRef=explode(",",$txt_style_ref);
				$all_style_job="";
				foreach($styleRef as $sid)
				{
						if($all_style_job=="") $all_style_job="'".$sid."'"; else $all_style_job.=","."'".$sid."'";
				}
				$fabric_rowspan_arr=array();$uom_rowspan_arr=array();
				foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
				{
					$fabrice_rowspan=0;
					foreach($fab_data as $desc_key=>$desc_data)
					{

						foreach($desc_data as $source_key=>$val)
						{
								//$uom_rowspan++;
								$fabrice_rowspan++;

							$fabric_rowspan_arr[$fab_nat_key]=$fabrice_rowspan;
							//$uom_rowspan_arr[$fab_nat_key][$uom_key]=$uom_rowspan;
						}
					}
				}
					$i=$m=1;$total_greycons=$total_tot_fabcons=$total_fab_tot_amount=$total_amount=$grand_total_greycons=$grand_total_fabcons=$grand_total_fab_amount=$grand_total_amount=0;
					foreach($fabric_detail_arr as $fab_nat_key=>$fab_data)
					{
						$y=1;
						foreach($fab_data as $desc_key=>$desc_data)
						{
							foreach($desc_data as $source_key=>$val)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$quotation_id=rtrim($val['quotation_id'],',');
								$quotation_ids=array_unique(explode(",",$quotation_id));
								//echo $quotation_id.'f';
								$pre_fab_ids=array_unique(explode(",",$pre_fab_id));
								$rate=$val['rate'];
								$avg_cons=$val['avg_cons'];
								$offer_qty=$val['offer_qty'];
								$fab_per_dzn=$val['fab_per_dzn'];
								$offer_qty_dzn=$val['offer_qty_dzn'];
								$amount=$new_array[$fab_nat_key][$desc_key][$source_key]['req_fab_total_value_dzn'];//$qout_offer_qnty=$fab_order_price_per_dzn=0;
								/*foreach($quotation_ids as $quot_id)
								{

									$qout_offer_qnty=$quot_wise_arr[$quot_id]['offer_qnty'];
									$fab_order_price_per_dzn=$quot_price_per_dzn_arr[$quot_id]['order_price_per_dzn'];
								}*/
								$qout_offer_qnty=$offer_qty;
								$fab_order_price_per_dzn=$fab_per_dzn;
								//echo $qout_offer_qnty.'='.$fab_order_price_per_dzn;
							$tot_fab_amt=$new_array[$fab_nat_key][$desc_key][$source_key]['req_fab_total_value'];//($amount/$fab_order_price_per_dzn)*$qout_offer_qnty;
							$avg_cons_dzn=($new_array[$fab_nat_key][$desc_key][$source_key]['req_fab_total']/$total_quot_pcs_qty)*$fab_per_dzn;//($tot_fab_cons/$offer_qty_dzn)*12;
							$tot_fab_cons=$new_array[$fab_nat_key][$desc_key][$source_key]['req_fab_total'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">  <?
                      	 if($y==1){
						?>
							<td width="30" rowspan="<? echo $fabric_rowspan_arr[$fab_nat_key];?>"><? echo $m; ?></td>
							<td width="100" rowspan="<? echo $fabric_rowspan_arr[$fab_nat_key];?>">
							<? echo $item_category[$fab_nat_key]; ?></td>
                             <?
							  }
							?>
							<td width="200" align="center" title="<? echo $qout_offer_qnty;?>"><div style="word-break:break-all"><? echo $desc_key; ?></div></td>
							<td width="100" align="center" ><div style="word-break:break-all"><? echo $fabric_source[$source_key]; ?></div></td>
							<td width="100" title="" align="right"><div style="word-break:break-all"><? echo number_format($avg_cons_dzn,4); ?></div></td>
                            <td width="100" title="Avg Cons/Price Per Dzn*Offer Qty" align="right"><div style="word-break:break-all"><? echo number_format($tot_fab_cons,4); ?></div></td>

                            <td width="100"  align="right"><div style="word-break:break-all"><? echo number_format($rate,4); ?></div></td>
                            <td width="100"  align="right" title="Total Fab Amount/PO Qty Dzn"><div style="word-break:break-all"><? echo number_format(($tot_fab_amt/$total_po_qty_dzn),4); ?></div></td>
                            <td width=""  align="right"><div style="word-break:break-all"><? echo number_format($tot_fab_amt,4); ?></div></td>
                            </tr>
                            <?
								$total_greycons+=$avg_cons_dzn;
								$total_tot_fabcons+=$tot_fab_cons;
								$total_amount+=$amount;
								$total_fab_tot_amount+=$tot_fab_amt;
								$grand_total_greycons+=$avg_cons_dzn;
								$grand_total_fabcons+=$tot_fab_cons;
								$grand_total_amount+=$amount;
								$grand_total_fab_amount+=$tot_fab_amt;
								$y++;
								$i++;
									//}
								}
								$m++;
							?>

                            <?
							}
							?>
                              <tr bgcolor="#F4F3C4">
                                <td>&nbsp; </td>
                                <td>&nbsp; </td>
                                <td>&nbsp;</td>

                                 <td  align="right"><strong>Sub Total</strong></td>
                               <th align="right"><strong><? echo number_format($total_greycons,4);$total_greycons=0;?></strong> </th>
                                <td align="right"><strong><? echo number_format($total_tot_fabcons,4);$total_tot_fabcons=0;?> </strong></td>
                                <td align="right"><strong><? //echo number_format($total_greycons,4);$total_greycons=0;?> </strong></td>

                                <td align="right"><strong><? echo number_format(($total_fab_tot_amount/$total_po_qty_dzn),4);$total_amount=0;?></strong> </td>
                                <td align="right"><strong><? echo number_format($total_fab_tot_amount,4);$total_fab_tot_amount=0;?> </strong></td>
                                </tr>
                            <?
						}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="4" ><strong>Grand Total</strong> </th>
                            <th align="right"><strong><? echo number_format($grand_total_greycons,4);?> </strong></th>
                              <th align="right"><strong><? echo number_format($grand_total_fabcons,4);?> </strong></th>
                            <th>&nbsp; </th>
                            <th align="right"><strong><? echo number_format(($grand_total_fab_amount/$total_po_qty_dzn),4);?></strong> </th>
                            <th align="right"><strong><? echo number_format($grand_total_fab_amount,4);?></strong> </th>
                            </tr>
                            </tfoot>
                    </table>
                    </div>
                    <br>
                    <?
					//Start	Yarn Cost part report here -------------------------------------------
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
               $sql_yarn = "select quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, SUM(cons_qnty) as cons_qnty, rate, SUM(amount) as amount
				from wo_pri_quo_fab_yarn_cost_dtls
				where  quotation_id in(".$all_quot_id.") and status_active=1 group by quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio,rate order by count_id";
				$yarn_data_array=sql_select($sql_yarn);
				foreach($yarn_data_array as $row)
				{
					 if($row[csf("percent_one")]==100)
                    $item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
                    else
                    $item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];

					$qout_offer_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
					$yarn_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

					$yarn_detail_arr[$item_descrition]['cons_qnty']+=($row[csf('cons_qnty')]/$yarn_per_dzn)*$qout_offer_qnty;
					$yarn_detail_arr[$item_descrition]['desc']=$item_descrition;
					$yarn_detail_arr[$item_descrition]['amount']+=$row[csf("amount")];
					$yarn_detail_arr[$item_descrition]['tot_amount']+=($row[csf('amount')]/$yarn_per_dzn)*$qout_offer_qnty;
					//$yarn_detail_arr[$item_descrition]['cons_qnty']+=$row[csf("cons_qnty")];
					$yarn_detail_arr[$item_descrition]['qout_offer_qnty']=$qout_offer_qnty;
					$yarn_detail_arr[$item_descrition]['qout_offer_qnty_dzn']+=$qout_offer_qnty;
					$yarn_detail_arr[$item_descrition]['yarn_per_dzn']=$yarn_per_dzn;

				}
					?>
                    <div style="margin-left:10px">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
                    <caption> <b style="float:left">Yarn Cost:</b></caption>
                    <thead>
                    <th width="30" valign="middle">SL</th>
                    <th width="350">Yarn Desc</th>
                    <th width="100">Yarn Qnty(Dzn)</th>
                    <th width="100">Total Qty</th>
                    <th width="100">Avg Rate(USD)</th>
                    <th width="100">Amount(USD) /Dzn</th>
                    <th width="100">Total Amount (USD)</th>
                    </thead>
                    <?
                    $total_qnty=0;$total_amount=$total_tot_amount=$total_tot_qnty=0;$y=1;$yarn_quot_check_arr=array();
                    foreach( $yarn_detail_arr as $row )
                    {
					if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


						$qout_offer_qnty_dzn=$row['qout_offer_qnty_dzn'];
						$qout_yarn_offer_qnty=$row['qout_offer_qnty'];
						$yarn_order_price_per_dzn=$row['yarn_per_dzn'];
						//echo $qout_yarn_offer_qnty.'='.$yarn_order_price_per_dzn;
						$tot_yarn_cons=$row['cons_qnty'];//($row['cons_qnty']/$yarn_order_price_per_dzn)*$qout_yarn_offer_qnty;
						$tot_yarn_amt=$row['tot_amount'];//($row['amount']/$yarn_order_price_per_dzn)*$qout_yarn_offer_qnty;
						$avg_cons_dzn=($tot_yarn_cons/$total_quot_pcs_qty)*$yarn_order_price_per_dzn;
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('try_<? echo $y; ?>','<? echo $bgcolor;?>')" id="try_<? echo $y; ?>">
                    <td align="left"><? echo $y; ?></td>
                    <td align="left"><? echo $row['desc']; ?></td>
                     <td align="right" title="Yarn Cons/Total Offer Qty*Costing Per"><? echo number_format($avg_cons_dzn,4); ?></td>
                     <td align="right"><? echo number_format($tot_yarn_cons,4); ?></td>
                    <td align="right"><? echo number_format($tot_yarn_amt/$tot_yarn_cons,4); ?></td>
                    <td align="right"><? echo number_format(($tot_yarn_amt/$total_po_qty_dzn),4); ?></td>
                      <td align="right"><? echo number_format($tot_yarn_amt,4); ?></td>
                    </tr>
                    <?
					$y++;
                    $total_qnty += $row['cons_qnty'];
                    $total_amount += $row['amount'];
					$total_tot_qnty += $tot_yarn_cons;
                    $total_tot_amount += $tot_yarn_amt;
                    }
                    ?>
                    <tfoot>
                        <tr class="rpt_bottom" style="font-weight:bold">
                        <th></th>
                        <th align="right">Total</th>
                        <th align="right"><? echo number_format($total_qnty,4); ?></th>
                         <th align="right"><? echo number_format($total_tot_qnty,4); ?></th>
                        <th></th>
                        <th align="right"><? echo number_format(($total_tot_amount/$total_po_qty_dzn),4); ?></th>
                        <th align="right"><? echo number_format($total_tot_amount,4); ?></th>
                        </tr>
                    </tfoot>
                    </table>
                    </div><!--Yarn Details End-->
                    <br>
						<?
                        //start	Conversion Cost to Fabric report here -------------------------------------------
                         $sql_conv = "select  a.quotation_id, a.cons_type, (a.req_qnty) as req_qnty, (a.charge_unit) as charge_unit, (a.amount) as amount,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition
                        from wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
                        where  a.quotation_id in(".$all_quot_id.") and a.status_active=1  order by a.cons_type";
                        $conv_data_array=sql_select($sql_conv);
						foreach( $conv_data_array as $row)
						{
							$item_descrition = $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("construction")].",".$row[csf("composition")];
							$qout_conv_offer_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
							$conv_order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
							if(str_replace(",","",$item_descrition)=="")
							{
							 $item_descrition="All Fabrics";
							}
							$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['req_qnty']+=($row[csf('req_qnty')]/$conv_order_price_per_dzn)*$qout_conv_offer_qnty;

							$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['desc']=$item_descrition;
							//$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['req_qnty']+=$row[csf("req_qnty")];
							$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['amount']+=$row[csf("amount")];
							$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['tot_amount']+=($row[csf('amount')]/$conv_order_price_per_dzn)*$qout_conv_offer_qnty;
							$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['charge_unit']+=$row[csf("charge_unit")];

							$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['offer_qnty']=$qout_conv_offer_qnty;
							$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['offer_qnty_dzn']+=$qout_conv_offer_qnty;
							$conv_cost_arr[$row[csf("cons_type")]][$item_descrition]['per_dzn']=$conv_order_price_per_dzn;
						}
                        ?>

                        <div style="margin-left:10px">
                        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                        <caption> <b style="float:left">Conversion Cost to Fabric:</b></caption>
                        <thead>
                        <th width="30" valign="middle"><b>SL</b></th>
                        <th width="350">Particulars</th>
                        <th width="100">Process</th>
                        <th width="100">Cons/<? echo $costing_val; ?></th>
                        <th width="100">Total Cons</th>
                        <th width="100">Avg Rate (USD)</th>
                        <th width="100">Amount (USD) /Dzn</th>
                        <th width="100">Total Amount (USD)</th>
                        </thead>
                        <?
                        $total_conv_amount=$total_conv_req_qnty=$total_conv_tot_req_qnty=0;$c=1;
                        foreach( $conv_cost_arr as $process_key=>$process_data )
                        {
						 	foreach( $process_data as $desc_key=>$val )
                        	{
							if($c%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$item_descrition = $desc_key;

						$qout_conv_offer_qnty=$val['offer_qnty'];
						$conv_offer_qnty_dzn=$val['offer_qnty_dzn'];
						$conv_order_price_per_dzn=$val['per_dzn'];
						$amount=$val['amount'];
						$req_qnty=$val['req_qnty'];
						$charge_unit=$val['charge_unit'];

						//$qout_conv_offer_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
						//$conv_order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
						//echo $qout_yarn_offer_qnty.'='.$yarn_order_price_per_dzn;
						$tot_conv_cons=$val['req_qnty'];//($req_qnty/$conv_order_price_per_dzn)*$qout_conv_offer_qnty;
						$tot_conv_amt=$val['tot_amount'];//($amount/$conv_order_price_per_dzn)*$qout_conv_offer_qnty;
						$conv_avg_cons_dzn=($tot_conv_cons/$total_quot_pcs_qty)*$conv_order_price_per_dzn;

                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trc_<? echo $c; ?>','<? echo $bgcolor;?>')" id="trc_<? echo $c; ?>">
                        <td align="left"><? echo $c; ?></td>
                        <td align="left"><? echo $item_descrition; ?></td>
                        <td align="left"><? echo $conversion_cost_head_array[$process_key]; ?></td>
                        <td align="right"><? echo number_format($conv_avg_cons_dzn,4); ?></td>
                         <td align="right"><? echo number_format($tot_conv_cons,4); ?></td>
                        <!-- <td align="right"><? //echo number_format($charge_unit,4); ?></td> -->
                        <td align="right"><? echo number_format($tot_conv_amt/$tot_conv_cons,4); ?></td> 
                        <td align="right"><? echo number_format(($tot_conv_amt/$total_po_qty_dzn),4); ?></td>
                        <td align="right"><? echo number_format($tot_conv_amt,4); ?></td>
                        </tr>
                        <?
						$c++;
                        $total_conv_amount += $amount;
						 $total_conv_req_qnty += $conv_avg_cons_dzn;
						 $total_conv_tot_req_qnty += $tot_conv_cons;
						  $total_conv_tot_amount += $tot_conv_amt;
							}
                        }
                        ?>
                         <tfoot>
                        <tr class="rpt_bottom" style="font-weight:bold">
                        <th colspan="4">Total</th>
                        <th align="right"><? echo number_format($total_conv_tot_req_qnty,2); ?></th>
                          <th align="right"><? //echo $total_conv_tot_req_qnty; ?></th>
                         <th align="right"><? echo number_format(($total_conv_tot_amount/$total_po_qty_dzn),2); ?></th>
                          <th align="right"><? echo number_format($total_conv_tot_amount,2); ?></th>
                        </tr>
                        </tr>

                        </table>
                        </div><!--Conversion Details End-->
                        <br>
                        <?
						 $trim_group=return_library_array( "select item_name,id from  lib_item_group ", "id", "item_name"  );
                        //start	Trims Cost part report here -------------------------------------------
						 $sql_trim = "select  quotation_id,seq, trim_group,description, cons_uom, cons_dzn_gmts, amount, nominated_supp
						from wo_pri_quo_trim_cost_dtls
						where quotation_id in(".$all_quot_id.") and status_active=1  order by seq  asc";
						$trim_data_result=sql_select($sql_trim);
						foreach( $trim_data_result as $row)
						{
							$trim_offer_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
							$per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
							//echo $row[csf("quotation_id")].'='.$trim_offer_qnty.'<br>';
							$item_data=$row[csf("seq")].'_'.$row[csf("trim_group")];
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['desc']=$item_descrition;
							//$trims_cost_arr[$row[csf("trim_group")]][$row[csf("description")]]['req_qnty']+=$row[csf("req_qnty")];
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['amount']+=$row[csf("amount")];
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['cons_uom']=$row[csf("cons_uom")];
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['tot_cons_dzn_gmts']+=($row[csf("cons_dzn_gmts")]/$per_dzn)*$trim_offer_qnty;
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['tot_amount']+=($row[csf("amount")]/$per_dzn)*$trim_offer_qnty;
							
							if($row[csf("nominated_supp")]!="")
							{
								$cbonominasupplierarr=explode("_",$row[csf("nominated_supp")]);
								$cbonominasupplierarAr=explode(",",$cbonominasupplierarr[0]);
								if($cbonominasupplierarr[0]!="")
								{
									foreach($cbonominasupplierarAr as $sup_comp=>$sid)
									{
										$nominated_suppArr=array_unique(explode(",",$sup_comp));
										$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['nominated_supp'].=$supplier_library[$sid].',';
									}
								}
								$cbonominasupplierarAAr=explode(",",$cbonominasupplierarr[1]);
								//print_r($cbonominasupplierarAAr);
								if($cbonominasupplierarr[1]!="")
								{
									foreach($cbonominasupplierarAAr as $key_comp=>$val)
									{
										$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['nominated_supp'].=$company_library[$val].',';
									}
								}
							}
							//$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['nominated_supp']=$row[csf("nominated_supp")];
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['seq']=$row[csf("seq")];
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['per_dzn']=$per_dzn;
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['trim_offer_qnty']=$trim_offer_qnty;
							$trims_cost_arr[$item_data][$row[csf("description")]][$row[csf("nominated_supp")]]['trim_offer_qnty_dzn']+=$trim_offer_qnty;
						}
						//print_r($trims_cost_arr);
						?>
                        <div style="margin-left:10px">
                        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                        <label><b>Trims Cost</b></label>
                       <thead>
                        <tr style="font-weight:bold">
                        <th width="30">SL</th>
                        <th width="150">Item Group</th>
                        <th width="150">Description</th>
                        <th width="150">Brand/Supp Ref</th>
                        <th width="100">UOM</th>
                        <th width="100">Cons/<? echo $costing_val; ?></th>
                        <th width="100">Total Cons</th>
                        <th width="100">Avg Rate (USD)</th>
                        <th width="100">Amount (USD) /Dzn</th>
                         <th width="100">Total Amount (USD)</th>
                        </tr>
                        </thead>
                        <?
                        $total_trim_amount=$total_trim_tot_cons=$total_trim_tot_amount=0;$t=1;
                        foreach( $trims_cost_arr as $trim_key_seq=>$trim_data )
                        {
							foreach( $trim_data as $desc_key=>$desc_data )
                        	{
								foreach( $desc_data as $supplier_key=>$val )
                        		{
								if($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$trim_arr=explode("_",$trim_key_seq);
								$trim_key=$trim_arr[1];

								$trim_conv_offer_qnty=$val['trim_offer_qnty'];
								$trim_offer_qnty_dzn=$val['trim_offer_qnty_dzn'];
								$trim_order_price_per_dzn=$val['per_dzn'];
								$seq=$val['seq'];
								//$trim_order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
								$tot_trim_cons=$val['tot_cons_dzn_gmts'];//($val['cons_dzn_gmts']/$trim_order_price_per_dzn)*$trim_conv_offer_qnty;
								$tot_trim_amt=$val['tot_amount'];//($val['amount']/$trim_order_price_per_dzn)*$trim_conv_offer_qnty;
								$trim_avg_cons_dzn=($tot_trim_cons/$total_quot_pcs_qty)*$trim_order_price_per_dzn;

                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trt_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trt_<? echo $t; ?>">
                        <td align="left"><? echo $t; ?></td>
                        <td align="left" title="<? echo $trim_key.'='.$seq;?>"><? echo $trim_group[$trim_key]; ?></td>
                        <td align="left"><? echo $desc_key; ?></td>
                         <td align="left"><?   $nominated_supp=rtrim($val['nominated_supp'],',');$nominated_supps=implode(",",array_unique(explode(",",$nominated_supp)));echo $nominated_supps; ?></td>
                        <td align="left"><? echo $unit_of_measurement[$val['cons_uom']]; ?></td>
                        <td align="right"><? echo number_format($trim_avg_cons_dzn,4); ?></td>
                        <td align="right"><? echo number_format($tot_trim_cons,4); ?></td>
                        <td align="right"><? echo number_format($tot_trim_amt/$tot_trim_cons,4); ?></td>
                        <td align="right"><? echo number_format($tot_trim_amt/$total_po_qty_dzn,4); ?></td>
                        <td align="right"><? echo number_format($tot_trim_amt,4); ?></td>
                        </tr>
                        <?
						$t++;
                        $total_trim_tot_cons+= $tot_trim_cons;
						 $total_trim_amount += $val['amount'];
						 $total_trim_tot_amount += $tot_trim_amt;
						 		}
							}
                        }
                        ?>
                        <tfoot>
                        <tr class="rpt_bottom" style="font-weight:bold">
                        <th colspan="6">Total</th>
                        <th align="right"><? echo number_format($total_trim_tot_cons,4); ?></th>
                        <th align="right"><? //echo number_format($total_trim_amount,4); ?></th>
                         <th align="right"><? echo number_format($total_trim_tot_amount/$total_po_qty_dzn,4); ?></th>
                          <th align="right"><? echo number_format($total_trim_tot_amount,4); ?></th>
                        </tr>
                        </tfoot>
                        </table>
                        </div> <!--Trim End-->
                        <br>
						<?
                        //start	Embellishment Details part report here -------------------------------------------
                       $emb_sql = "select  quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount
                        from wo_pri_quo_embe_cost_dtls
                        where quotation_id in(".$all_quot_id.") and emb_name !=3  and amount>0  and status_active=1  order by quotation_id,emb_name";
                        $emb_data_array=sql_select($emb_sql);
						foreach( $emb_data_array as $row)
						{
							$embl_offer_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
							$emb_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
							//echo $row[csf("quotation_id")].'='.$trim_offer_qnty.'<br>';
							$embl_cost_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['amount']+=$row[csf("amount")];
							$embl_cost_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['tot_amount']+=($row[csf("amount")]/$emb_per_dzn)*$embl_offer_qnty;
							$embl_cost_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['cons_dzn_gmts']+=$row[csf("cons_dzn_gmts")];
							$embl_cost_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['tot_cons_dzn_gmts']+=($row[csf("cons_dzn_gmts")]/$emb_per_dzn)*$embl_offer_qnty;
							$embl_cost_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['per_dzn']=$emb_per_dzn;
							$embl_cost_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['embl_offer_qnty']=$embl_offer_qnty;
							$embl_cost_arr[$row[csf("emb_name")]][$row[csf("emb_type")]]['embl_offer_qnty_dzn']+=$embl_offer_qnty;
						}
   						 ?>
                        <div style="margin-left:10px">
                        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
                        <label><b>Embellishment Details</b></label>
                        <thead>
                        <tr style="font-weight:bold">
                        <th width="30">SL</th>
                        <th width="150">Particulars</th>
                        <th width="150">Type</th>
                        <th width="100">Cons/<? echo $costing_val; ?></th>
                        <th width="100">Total Cons</th>
                        <th width="100">Avg Rate (USD)</th>
                        <th width="100">Amount (USD) /Dzn</th>
                         <th width="100">Total Amount (USD)</th>

                        </tr>
                        <?
                        $total_emb_amount=$total_emb_tot_cons=$total_emb_tot_amount=0;$em=1;
                        foreach( $embl_cost_arr as $emb_name_key=>$emb_name_data )
                        {
							foreach( $emb_name_data as $type_key=>$val )
                        	{
                      	if($em%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        if($emb_name_key==1)$em_type = $emblishment_print_type[$type_key];
                        else if($emb_name_key==2)$em_type = $emblishment_embroy_type[$type_key];
                        else if($emb_name_key==3)$em_type = $emblishment_wash_type[$type_key];
                        else if($emb_name_key==4)$em_type = $emblishment_spwork_type[$type_key];

						$embl_offer_qnty=$val['embl_offer_qnty'];
						$embl_offer_qnty_dzn=$val['embl_offer_qnty_dzn'];
						$embl_order_price_per_dzn=$val['per_dzn'];

						$tot_embl_cons=$val['tot_cons_dzn_gmts'];//($val['cons_dzn_gmts']/$embl_order_price_per_dzn)*$embl_offer_qnty;
						$tot_embl_amt=$val['tot_amount'];//($val['amount']/$embl_order_price_per_dzn)*$embl_offer_qnty;

						$embl_avg_cons_dzn=($tot_embl_cons/$total_quot_pcs_qty)*$embl_order_price_per_dzn;

                        ?>
                       <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trem_<? echo $em; ?>','<? echo $bgcolor;?>')" id="trem_<? echo $em; ?>">
                        <td align="left"><? echo $em; ?></td>
                        <td align="left"><? echo $emblishment_name_array[$emb_name_key]; ?></td>
                        <td align="left"><? echo $em_type; ?></td>
                        <td align="right"><? echo number_format($embl_avg_cons_dzn,4); ?></td>
                         <td align="right"><? echo number_format($tot_embl_cons,4); ?></td>
                        <td align="right"><? echo number_format($tot_embl_amt/$tot_embl_cons,4); ?></td>
                        <td align="right"><? echo number_format($tot_embl_amt/$total_po_qty_dzn,4); ?></td>
                        <td align="right"><? echo number_format($tot_embl_amt,4); ?></td>
                        </tr>
                        <?
						$em++;
                        $total_emb_amount += $val['amount'];
						$total_emb_tot_cons += $tot_embl_cons;
						$total_emb_tot_amount += $tot_embl_amt;
							}
                        }
                        ?>
                        <tfoot>
                        <tr class="rpt_bottom" style="font-weight:bold">
                        <th colspan="4">Total</th>
                        <th align="right"><? echo number_format($total_emb_tot_cons,4); ?></th>
                        <th align="right"><? echo number_format($total_emb_amount,4); ?></th>

                        <th align="right"><? echo number_format($total_emb_tot_amount/$total_po_qty_dzn,4); ?></th>
                        <th align="right"><? echo number_format($total_emb_tot_amount,4); ?></th>
                        </tr>
                        </tfoot>
                        </table>
                        </div><!--Emblish End-->

                        </div>
                          <div id="" style="width:850px;">
				    <?
                      		echo signature_table(109, $cbo_company_name, "850px");
                        ?>
                    </div>
                    <?
                    }
					?>

             </div>

		<?
	}
	if( str_replace("'","",$sign)==1 )
	{
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
		echo "$html****$filename";
	}
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


			//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
			$trimsDataArr=sql_select("select c.po_break_down_id, max(a.costing_per) as costing_per, b.trim_group, max(b.cons_uom) as cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b , wo_pre_cost_trim_co_cons_dtls c where a.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and b.status_active=1 and b.is_deleted=0 and c.po_break_down_id=$po_break_down_id group by b.trim_group, c.po_break_down_id");
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
        		$accss_req_qnty=($row[csf('cons_dzn_gmts')]/$dzn_qnty)*$tot_po_qnty;

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
	//echo $id;//$job_no;
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
                $i=1;

				$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date,
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
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
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
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
