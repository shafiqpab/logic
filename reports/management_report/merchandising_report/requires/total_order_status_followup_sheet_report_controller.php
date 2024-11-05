<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$season_arr=return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/total_order_status_followup_sheet_report_controller',this.value, 'load_drop_down_season', 'season_td' );" ,0);
	exit();
}
if ($action=="load_drop_down_season")
{
//echo "select id,season from lib_buyer_season where status_active =1 and is_deleted=0 and buyer_id='$data' ";
	echo create_drop_down( "cbo_season", 130, "select id,season_name from lib_buyer_season where status_active =1 and is_deleted=0 and buyer_id='$data' ","id,season_name", 1, "-- All Season --", $selected, "load_drop_down( 'requires/total_order_status_followup_sheet_report_controller',this.value, 'load_drop_down_season', 'season_td' );" ,0);
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_search_list_view', 'search_div', 'total_order_status_followup_sheet_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
if($action=="style_refarence_search_list_view")
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
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a where a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by a.job_no_prefix_num";
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
if($action=="order_popup")
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'order_search_list_view', 'search_div', 'total_order_status_followup_sheet_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
	list($company,$buyer,$search_type,$search_value,$start_date,$end_date,$cbo_year,$txt_style_ref)=explode('**',$data);
	?>
    <script>
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_year=str_replace("'","",$cbo_year);
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

		if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref)  ";
		else $style_cond="and b.job_no_prefix_num in($txt_style_ref) ";
		if($db_type==0)
		{
			if($cbo_year!=0) $year_cond="and year(b.insert_date)= '$cbo_year'";else $year_cond="";
		}
		else
		{
			if($cbo_year!=0) $year_cond=" and to_char(b.insert_date,'YYYY')= '$cbo_year'";$year_cond="";
		}
	}
	else $style_cond="";

	//echo $style_cond."jahid";die;
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $year_cond $style_cond $search_con $date_cond $year_cond and a.status_active=1";
	// echo $sql;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","150",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,job_year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;
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


	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$cbo_season=str_replace("'","",$cbo_season);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);

	//echo $txt_date_from;die;
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	$ship_date_cond="";
	/*if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}*/
		if($txt_date_from!="" && $txt_date_to!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$ship_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";

		}

	$job_no_cond="";
	if(trim($txt_style_ref)!="") $job_no_cond.=" and a.job_no_prefix_num  in($txt_style_ref)";
	if(trim($style_ref_id)!="") $job_no_cond.=" and a.id  in($style_ref_id)";
	//echo $job_no_cond.'DDD';die;
	/*$order_cond="";
	if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	if($txt_order_id!="") $order_cond="and b.id in($txt_order_id)";*/

	$order_cond="";
	if(trim(str_replace("'","",$txt_order))!="")
	{
		if(str_replace("'","",$txt_order_id)!="")
		{
			//echo $txt_order_id.'AAAAAA';
			$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
		}
		else
		{
			$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
		}
	}
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";

	$sql_yarn = "select c.id as y_id,a.sequence_no,a.id,c.yarn_count,a.construction,b.copmposition_id,b.percent  from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id and b.count_id=c.id  order by a.id, a.sequence_no";
	$data_arr_ycount=sql_select($sql_yarn);
	foreach($data_arr_ycount as $row)
	{
		$precost_yarnCount_arr[$row[csf("y_id")]]['count']=$row[csf("yarn_count")];
	}
	unset($data_arr_ycount);

	$sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no,a.season_buyer_wise, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty,b.unit_price, b.plan_cut,b.po_quantity, b.pub_shipment_date,b.shipment_date,b.file_no,b.grouping,b.po_received_date, c.color_number_id,c.order_quantity as order_quantity,c.plan_cut_qnty,c.color_number_id as color,c.item_number_id,d.body_part_id,d.id as fab_dtls_id,d.lib_yarn_count_deter_id as deter_min_id,d.construction,d.composition, d.gsm_weight,d.width_dia_type
	from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c , wo_pre_cost_fabric_cost_dtls d
	where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and c.item_number_id=d.item_number_id  and a.job_no=d.job_no and b.job_no_mst=d.job_no and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1  and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond order by a.job_no,b.id, b.pub_shipment_date";


	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();$m=0;
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		 $fabric_desc=$row[csf("composition")].','.$row[csf("construction")].','.$row[csf("gsm_weight")];
		//$yarnCount=$precost_yarnCount_arr[$row[csf("deter_min_id")]]['count'];

		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["po_number"]=$row[csf("po_number")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["order_quantity"]=$row[csf("order_quantity")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["plan_cut_qnty"]+=$row[csf("plan_cut_qnty")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["job_no"]=$row[csf("job_no_prefix_num")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["buyer_name"]=$row[csf("buyer_name")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["file_no"]=$row[csf("file_no")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["ref_no"]=$row[csf("grouping")];
		
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["style"]=$row[csf("style_ref_no")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["body_part_id"]=$row[csf("body_part_id")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["deter_min_id"]=$row[csf("deter_min_id")];

		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["po_received_date"]=$row[csf("po_received_date")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["shipment_date"]=$row[csf("shipment_date")];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["item_id"].=$garments_item[$row[csf("item_number_id")]].',';
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["season"]=$season_arr[$row[csf("season_buyer_wise")]];
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["desc"]= $fabric_desc;
		$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["width"]= $row[csf("width_dia_type")];
		//$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["yarn_count"] .= $yarnCount.',';

		$po_id=$row[csf("po_id")];
		//$booking_color=$order_id.$booking_no.$color_id;
		if (!in_array($po_id,$result_job_wise))
		{ $m++;
			 $result_job_wise[]=$po_id;
			  $po_quantity=$row[csf("po_quantity")];
		}
		else
		{
			 $po_quantity=0;
		}
		$buyer_summary_arr[$row[csf("buyer_name")]]["order_quantity"]+=$po_quantity;
		$buyer_summary_arr[$row[csf("buyer_name")]]["po_value"]+=$po_quantity*$row[csf("unit_price")];

		$po_data_arr[$row[csf("job_no")]]["pub_shipment_date"].=$row[csf("pub_shipment_date")].',';
		$po_data_arr[$row[csf("job_no")]]["po_received_date"].=$row[csf("po_received_date")].',';
		$po_wise_data_arr[$row[csf("po_id")]]["po_quantity"]=$row[csf("po_quantity")];
		$po_wise_data_arr[$row[csf("po_id")]]["unit_price"]=$row[csf("unit_price")];
		$po_wise_data_arr[$row[csf("po_id")]]["po_value"]=$row[csf("po_quantity")]*$row[csf("unit_price")];
		$po_wise_data_arr[$row[csf("po_id")]]["plan_cut_qnty"]=$row[csf("plan_cut")];
		$po_wise_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
		$po_wise_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];

		$JobArr[]="'".$row[csf('job_no')]."'";
	}
	//echo $all_po_id.'d';die;
	$condition= new condition();
		$condition->company_name("=$cbo_company_name");
	  if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
	 if($txt_order_id!='')
	 {
		$condition->po_id("in($txt_order_id)");
	 }
	 if(str_replace("'","",$txt_style_ref)!='')
	 {
		$condition->job_no_prefix_num("in($txt_style_ref)");
	 }
	 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			 }
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery();die;
	$fabric_arr=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
	//print_r($fabric_arr);

	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";
			$po_cond_for_in4=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" c.po_break_down_id in($ids) or";
				$po_cond_for_in2.=" c.po_breakdown_id in($ids) or";
				$po_cond_for_in3.=" c.order_id in($ids) or";
				$po_cond_for_in4.=" c.po_id in($ids) or";
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
			$po_cond_for_in4=chop($po_cond_for_in4,'or ');
			$po_cond_for_in4.=")";
		}
		else
		{
			$po_cond_for_in=" and c.po_break_down_id in($poIds)";
			$po_cond_for_in2=" and c.po_breakdown_id in($poIds)";
			$po_cond_for_in3=" and c.order_id in($poIds)";
			$po_cond_for_in4=" and c.po_id in($poIds)";
		}//


		$booking_data_array=array();
		$booking_data=sql_select("select c.pre_cost_fabric_cost_dtls_id as fab_dtls_id,c.po_break_down_id as po_id,c.gmts_color_id,d.body_part_id,
		(c.fin_fab_qnty) as fin_fab_qnty,
		(c.grey_fab_qnty) as grey_fab_qnty
		 from wo_booking_mst b,wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls d where b.booking_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id=d.id and c.job_no=d.job_no and   c.booking_type=1 and c.is_short=2 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in order by c.po_break_down_id");
		foreach($booking_data as $row)
		{
				$booking_data_array[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		}
		unset($booking_data);

	 $salesOrderDetailsDataSql = "select b.mst_id,b.determination_id,b.width_dia_type,c.job_no,sum(d.grey_qty) as grey_qty,e.yarn_count_id from fabric_sales_order_dtls b,wo_booking_dtls c,fabric_sales_order_mst a , fabric_sales_order_yarn d,fabric_sales_order_yarn_dtls e where a.id=b.mst_id and  d.mst_id=a.id and  e.mst_id=a.id and  e.yarn_dtls_id=d.id  and  e.deter_id=d.deter_id and c.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.sales_booking_no=c.booking_no  and b.is_deleted=0 and b.status_active=1 and a.within_group in(1)  $po_cond_for_in group by b.mst_id,b.determination_id,b.width_dia_type,c.job_no,e.yarn_count_id";
		$salesOrderDetailsDataResult = sql_select($salesOrderDetailsDataSql);
		foreach ($salesOrderDetailsDataResult as $row) {

			$yarnCount=$precost_yarnCount_arr[$row[csf("yarn_count_id")]]['count'];
			//echo $yarnCount.'DD';
			$sales_order_arr[$row[csf('job_no')]][$row[csf("determination_id")]]['width_dia_type'] .=$fabric_typee[$row[csf("width_dia_type")]].',';
			$sales_order_arr[$row[csf('job_no')]][$row[csf("determination_id")]]['ycount'] .=$yarnCount.',';
			$sales_order_qty_arr[$row[csf('job_no')]][$row[csf("determination_id")]]['grey_qty']=$row[csf("grey_qty")];
			$job_no_from_sales_arr[$row[csf('mst_id')]]['job_no']=$row[csf('job_no')];
			$sales_mst_id.=$row[csf('mst_id')].',';
		}
		$sales_mst_id=rtrim($sales_mst_id,',');
		//$sales_mst_id=implode(",",array_unique(explode(",",$sales_mst_id)));

		$all_sales_id=implode(",",array_unique(explode(",",$sales_mst_id)));
	$soIds=chop($all_sales_id,','); $sales_cond_for_in=""; $sales_cond_for_in2="";$sales_cond_for_in3="";
	$so_ids=count(array_unique(explode(",",$all_sales_id)));
		if($db_type==2 && $so_ids>1000)
		{
			$sales_cond_for_in=" and (";
			$sales_cond_for_in2=" and (";

			$soIdsArr=array_chunk(explode(",",$soIds),999);
			foreach($soIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$sales_cond_for_in.=" a.po_id in($ids) or";
				$sales_cond_for_in2.=" a.sales_order_id in($ids) or";
				$sales_cond_for_in3.=" c.po_breakdown_id in($ids) or";

			}
			$sales_cond_for_in=chop($sales_cond_for_in,'or ');
			$sales_cond_for_in.=")";
			$sales_cond_for_in2=chop($po_cond_for_in2,'or ');
			$sales_cond_for_in2.=")";
			$sales_cond_for_in3=chop($sales_cond_for_in3,'or ');
			$sales_cond_for_in3.=")";

		}
		else
		{
			$sales_cond_for_in=" and a.po_id in($soIds)";
			$sales_cond_for_in2=" and a.sales_order_id in($soIds)";
			$sales_cond_for_in3=" and c.po_breakdown_id in($soIds)";

		}//


		unset($salesOrderDetailsDataResult);
		if($soIds != ''){
			$yarn_qty_requisition = sql_select("select a.dtls_id, a.determination_id,b.knit_id,b.requisition_no,b.yarn_qnty,a.program_qnty,a.po_id,b.id,b.prod_id,c.yarn_count_id,c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_type from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $sales_cond_for_in");

			foreach ($yarn_qty_requisition as $row) {
				$yarn_qty_requisition_arr[$row[csf('po_id')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_type')]] = $row[csf('yarn_qnty')];
				$yarn_qty_requisition_arr[$row[csf('po_id')]]['requisition'] = $row[csf('id')];
				$yarn_qty_requisition_arr[$row[csf('po_id')]][$row[csf('determination_id')]]['requisition_qnty'] += $row[csf('yarn_qnty')];
				$yarn_qty_requisition_arr[$row[csf('po_id')]][$row[csf('determination_id')]]['requisition_no'] .= $row[csf('requisition_no')].",";
				$job_no=$job_no_from_sales_arr[$row[csf('po_id')]]['job_no'];
				$yarn_req_qty_requisition_arr[$job_no]['requisition_qnty'] += $row[csf('yarn_qnty')];
				$yarn_req_qty_requisition_arr[$job_no]['requisition_id'] .= $row[csf('id')].',';

				$prog_sales_qty_arr[$job_no]['program_qnty'] += $row[csf('program_qnty')];
				$prog_sales_qty_arr[$job_no]['program_id'] .= $row[csf('po_id')].',';
			}
			//print_r($yarn_req_qty_requisition_arr);

			unset($yarn_qty_requisition);
			$sql_yarn_iss = "select a.po_id,a.determination_id,d.id,sum(d.cons_quantity) cons_quantity from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join inv_transaction d on (b.requisition_no=d.requisition_no and d.transaction_type=2 and b.prod_id=d.prod_id) where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $sales_cond_for_in group by a.po_id,d.id,a.determination_id";
			$dataArrayIssue = sql_select($sql_yarn_iss);
			foreach ($dataArrayIssue as $row) {
				$yarn_issue_details_arr[$row[csf('po_id')]][$row[csf('determination_id')]]['issue_qnty'] += $row[csf('cons_quantity')];
				$yarn_issue_details_arr[$row[csf('po_id')]][$row[csf('determination_id')]]['issue_ids'] .= $row[csf('mst_id')].",";
				$job_no=$job_no_from_sales_arr[$row[csf('po_id')]]['job_no'];
				$yarn_issue_qty_requisition_arr[$job_no]['yarn_issue_qnty'] += $row[csf('cons_quantity')];
				$yarn_issue_qty_requisition_arr[$job_no]['yarn_issue_id'] .= $row[csf('id')].',';
			}

		}

		 if($soIds != ''){
		 	$batch_sql = "select a.id as batch_id,a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no,sum(b.batch_qnty) qnty from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id  and (a.extention_no is null or a.extention_no=0) and a.status_active=1 and a.is_deleted=0 $sales_cond_for_in2 group by a.id, a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no";
			$batch_result = sql_select($batch_sql);
			$batch_arr=array();
			foreach ($batch_result as $row) {
				$job_no=$job_no_from_sales_arr[$row[csf('sales_order_id')]]['job_no'];
				$batch_qty_arr[$job_no]['batch_qty']+= $row[csf("qnty")];
				$batch_qty_arr[$job_no]['batch_id'] .= $row[csf("batch_id")].',';
			}
		// DYEING PRODUCTION
		$sql_dye = "select b.po_id, a.id as batch_id,b.prod_id,d.detarmination_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c,product_details_master d where a.id=b.mst_id and a.id=c.batch_id and b.prod_id=d.id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sales_cond_for_in2 group by b.po_id, a.id,b.prod_id,d.detarmination_id";
		$resultDye = sql_select($sql_dye);
		foreach ($resultDye as $row) {
		$job_no=$job_no_from_sales_arr[$row[csf('po_id')]]['job_no'];
			$dyeing_qnty_arr[$job_no]['dyeing_qty']+= $row[csf('dye_qnty')];
			$dyeing_qnty_arr[$job_no]['batch_id'].= $row[csf('batch_id')].',';
		}
		unset($resultDye);
		 }

		  if($soIds != ''){
		  	$fab_store_data_array=array();
			$fabstore_data=sql_select("select a.id,a.body_part_id,c.trans_type,c.po_breakdown_id as po_id, c.color_id, c.quantity from pro_finish_fabric_rcv_dtls a, order_wise_pro_details c
	where a.id=c.dtls_id and c.entry_form=37  and a.prod_id=c.prod_id and c.trans_type=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $sales_cond_for_in3");
			foreach($fabstore_data as $row)
			{
					$job_no=$job_no_from_sales_arr[$row[csf('po_id')]]['job_no'];
					$fab_store_data_array[$job_no]['store_quantity']+=$row[csf('quantity')];
					$fab_store_data_array[$job_no]['dtls_id'].=$row[csf('id')].',';
			}
			unset($fabstore_data);
		  }

		if($sales_mst_id != ''){
			$knit_data_array=array();
		 	$knit_data=sql_select("select b.body_part_id, b.febric_description_id,b.id as dtls_id, c.po_breakdown_id as po_id, c.quantity,c.trans_id from pro_grey_prod_entry_dtls b, order_wise_pro_details c  where  b.id=c.dtls_id  and c.entry_form=2 and c.trans_type=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.po_breakdown_id in($sales_mst_id)");
			foreach($knit_data as $row)
			{
				$job_no=$job_no_from_sales_arr[$row[csf('po_id')]]['job_no'];
				$knit_data_array[$job_no]['quantity']+=$row[csf('quantity')];
				$knit_data_array[$job_no]['knit_dtls_id']=$row[csf('dtls_id')];
			}
			unset($knit_data);
		}


		$fab_deli_data_array=array();
		$fabdel_data=sql_select("select max(a.issue_date) as issue_date,c.po_breakdown_id  as po_id from inv_issue_master a,inv_finish_fabric_issue_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=18 and c.entry_form=18 and a.item_category=2 group by c.po_breakdown_id");
		foreach($fabdel_data as $row)
		{
				$fab_deli_data_array[$row[csf('po_id')]]['delivery_date']=$row[csf('issue_date')];
		}
		unset($fabstore_data);
		$exfactory_data_array=array();
		$exfactory_data=sql_select("select c.po_break_down_id,c.shiping_status,
		sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as return_qnty,
		MAX(c.ex_factory_date) as ex_factory_date from pro_ex_factory_mst c  where c.status_active=1 and c.is_deleted=0 $po_cond_for_in group by c.po_break_down_id,c.shiping_status");
		foreach($exfactory_data as $exfatory_row)
		{
				$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_qnty']=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')];
				$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_date']=$exfatory_row[csf('ex_factory_date')];
				$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['shiping_status']=$exfatory_row[csf('shiping_status')];
				$buyer_name=$po_wise_data_arr[$exfatory_row[csf("po_break_down_id")]]["buyer_name"];
				$unit_price=$po_wise_data_arr[$exfatory_row[csf("po_break_down_id")]]["unit_price"];
				$buyer_exf_arr[$buyer_name]['exfact_qty']+=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')];
				$buyer_exf_arr[$buyer_name]['exfact_value']+=($exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')])*$unit_price;
		}
		unset($exfactory_data);


		$labdip_data_array=array();
		$labdip_data=sql_select("select c.po_break_down_id,
		c.approval_status,c.approval_status_date,c.color_name_id as color_id from wo_po_lapdip_approval_info c  where c.status_active=1 and c.is_deleted=0 and c.approval_status>0 $po_cond_for_in group by c.po_break_down_id,c.approval_status,c.approval_status_date,c.color_name_id");
		foreach($labdip_data as $row)
		{
				if($row[csf('approval_status')]==1)//Submit
				{
					$labdip_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['submit_date']=$row[csf('approval_status_date')];
				}
				else if($row[csf('approval_status')]==3)//Approved
				{
					$labdip_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['approv_date']=$row[csf('approval_status_date')];
				}
		}
		unset($labdip_data);//
		$samp_data_array=array();
		$samp_data=sql_select("select c.po_break_down_id,
		c.approval_status,c.approval_status_date,d.color_number_id as color_id,c.sample_type_id from wo_po_sample_approval_info c,wo_po_color_size_breakdown d  where  d.id=c.color_number_id and d.job_no_mst=c.job_no_mst  and c.status_active=1 and c.is_deleted=0  and c.sample_type_id in(7,18) and c.approval_status>0 $po_cond_for_in group by c.po_break_down_id,c.approval_status,c.approval_status_date,d.color_number_id,c.sample_type_id");
		foreach($samp_data as $row)
		{
			if($row[csf('approval_status')]==1)//Submit
			{
				if($row[csf('sample_type_id')]==7) //PP
				{
					//echo $row[csf('approval_status_date')].'AA';
					$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['samp_submit_date']=$row[csf('approval_status_date')];
				}
			}
			else if($row[csf('approval_status')]==3)//Approved //Size Set
			{
				if($row[csf('sample_type_id')]==18)
				{
					//echo $row[csf('approval_status_date')].'BB';
				$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['samp_approv_date']=$row[csf('approval_status_date')];
				}
			}
		}
		//print_r($samp_data_array);
		unset($samp_data);//wo_po_sample_approval_info
		$embl_data_array=array();
		$embl_data=sql_select("select c.po_break_down_id,
		c.approval_status,c.approval_status_date,c.color_name_id as color_id,c.embellishment_id from wo_po_embell_approval c  where c.status_active=1 and c.is_deleted=0  and c.embellishment_id in(1,2) and c.approval_status>0 $po_cond_for_in group by c.po_break_down_id,c.approval_status,c.approval_status_date,c.color_name_id,c.embellishment_id");


		foreach($embl_data as $row)
		{
				if($row[csf('embellishment_id')]==1)//Print
				{
					if($row[csf('approval_status')]==1)//Submit
					{
						$embl_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['print_submit_date']=$row[csf('approval_status_date')];
					}
					else if($row[csf('approval_status')]==3)//Approved
					{
						$embl_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['print_approv_date']=$row[csf('approval_status_date')];
					}
				}
				else if($row[csf('embellishment_id')]==2)//Embro
				{
					if($row[csf('approval_status')]==1)//Submit
					{
						$embl_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['embro_submit_date']=$row[csf('approval_status_date')];
					}
					else if($row[csf('approval_status')]==3)//Approved
					{
						$embl_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['embro_approv_date']=$row[csf('approval_status_date')];
					}
				}
		}
		unset($embl_data);

	$SqlgmtsProdData="select  c.po_break_down_id,
					sum(CASE WHEN c.production_type=1 THEN c.production_quantity ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN c.production_type=2 and c.embel_name=1 and c.production_source=1 THEN c.production_quantity ELSE 0 END) AS print_issue_qnty_in,
					sum(CASE WHEN c.production_type=2 and c.embel_name=1 and c.production_source=3 THEN c.production_quantity ELSE 0 END) AS print_issue_qnty_out,
					sum(CASE WHEN c.production_type=3 and c.embel_name=1 and c.production_source=1 THEN c.production_quantity ELSE 0 END) AS print_recv_qnty_in,
					sum(CASE WHEN c.production_type=3 and c.embel_name=1 and c.production_source=3 THEN c.production_quantity ELSE 0 END) AS print_recv_qnty_out,
					sum(CASE WHEN c.production_type=2 and c.embel_name=2 and c.production_source=1 THEN c.production_quantity ELSE 0 END) AS emb_issue_qnty_in,
					sum(CASE WHEN c.production_type=2 and c.embel_name=2 and c.production_source=3 THEN c.production_quantity ELSE 0 END) AS emb_issue_qnty_out,
					sum(CASE WHEN c.production_type=3 and c.embel_name=2 and c.production_source=1 THEN c.production_quantity ELSE 0 END) AS emb_recv_qnty_in,
					sum(CASE WHEN c.production_type=3 and c.embel_name=2 and c.production_source=3 THEN c.production_quantity ELSE 0 END) AS emb_recv_qnty_out,
					sum(CASE WHEN c.production_type=4 and c.production_source=1 THEN c.production_quantity ELSE 0 END) AS sew_input_qnty_in,
					sum(CASE WHEN c.production_type=4 and c.production_source=3 THEN c.production_quantity ELSE 0 END) AS sew_input_qnty_out,
					sum(CASE WHEN c.production_type=5 and c.production_source=1 THEN c.production_quantity ELSE 0 END) AS sew_recv_qnty_in,
					sum(CASE WHEN c.production_type=5 and c.production_source=3 THEN c.production_quantity ELSE 0 END) AS sew_recv_qnty_out,
					sum(CASE WHEN c.production_type=8 and c.production_source=1 THEN c.production_quantity ELSE 0 END) AS finish_qnty_in,
					sum(CASE WHEN c.production_type=8 and c.production_source=3 THEN c.production_quantity ELSE 0 END) AS finish_qnty_out,
					sum(CASE WHEN c.production_type=3 and c.embel_name=3 and c.production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
					sum(CASE WHEN c.production_type=3 and c.embel_name=3 and c.production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,

					sum(CASE WHEN c.production_type=2  THEN c.production_quantity ELSE 0 END) AS emblish_issue_qnty_in,
					sum(CASE WHEN c.production_type=3  THEN c.production_quantity ELSE 0 END) AS embblish_recv_qnty_in,

					sum(CASE WHEN c.production_type=3 and c.embel_name=1 THEN c.reject_qnty ELSE 0 END) AS print_reject_qnty,
					sum(CASE WHEN c.production_type=3 and c.embel_name=2 THEN c.reject_qnty ELSE 0 END) AS emb_reject_qnty,
					sum(CASE WHEN c.production_type=5 THEN c.reject_qnty ELSE 0 END) AS sew_reject_qnty,
					sum(CASE WHEN c.production_type=8 THEN c.reject_qnty ELSE 0 END) AS finish_reject_qnty,
					sum(CASE WHEN c.production_type=1 THEN c.reject_qnty ELSE 0 END) AS cutting_reject_qnty,
					sum(CASE WHEN c.production_type=7 THEN c.reject_qnty ELSE 0 END) AS iron_rej_qnty
					from pro_garments_production_mst c where c.is_deleted=0 and c.status_active=1 $po_cond_for_in group by c.po_break_down_id";



	$garment_prod_data_arr=array();
	//echo $SqlgmtsProdData;
	$gmtsProdDataArr=sql_select($SqlgmtsProdData);
	foreach($gmtsProdDataArr as $row)
	{
		//echo $row[csf("emblish_issue_qnty_in")].'='.$row[csf("embblish_recv_qnty_in")].', ';
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_qnty']=$row[csf("cutting_qnty")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
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
	$tbl_width=4580;

	ob_start();
	//49
	?>
	<style type="text/css">
            .block_div {
                    width:auto;
                    height:auto;
                    text-wrap:normal;
                    vertical-align:bottom;
                    display: block;
                    position: !important;
                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }

        </style>
        <div style="width:100%; margin-left:10px;">
             <table width="<? echo $tbl_width;?>">
                <tr>
                    <td align="center" width="100%" colspan="70" class="form_caption"><? echo $report_title.'<br>'.$company_library[str_replace("'","",$cbo_company_name)].'<br>';
					echo show_company(str_replace("'","",$cbo_company_name),'','').'<br>'; if($cbo_buyer_name>0) echo $buyer_arr[$cbo_buyer_name];
					 ?></td>
                </tr>
            </table>
			  <table width="730" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" >
			  <caption> <b style=" text-align:center"> Summary Report</b></caption>
			  	<thead>
					<th width="30">SL </th>
					<th  width="100">Buyer</th>
					<th width="100">Order Qty </th>
					<th width="100">Order Value</th>
					<th width="100">Ex. Factory Qty </th>
					<th width="100">Ex. Factory Value </th>
					<th width="100">Access/Short Qty</th>
					<th>Access/Short Value</th>

				</thead>
				<?
				//
				$k=1;$total_summ_buyer_qty=$total_summ_buyer_value=$total_summ_buyer_exfact_qty=$total_summ_buyer_exfact_value=$total_summ_buyer_short_access_qty=$total_summ_buyer_short_access_value=0;
				foreach($buyer_summary_arr as $buyer_id=>$val)
				{
				if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$buyer_exf_arr[$buyer_name]['exfact_qty'];
				//$buyer_exf_arr[$buyer_name]['exfact_value'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsumm_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trsumm_<? echo $k; ?>" style="font-size:13px">
					<td> <? echo $k; ?> </td>
					<td> <? echo $buyer_arr[$buyer_id]; ?></td>
					<td align="right"> <? echo $val["order_quantity"]; ?> </td>
					<td align="right" style="word-break:break-all"> <? echo number_format($val["po_value"],3); ?> </td>
					<td align="right"> <? echo $buyer_exf_arr[$buyer_id]['exfact_qty']; ?> </td>
					<td align="right" style="word-break:break-all"> <? echo number_format($buyer_exf_arr[$buyer_id]['exfact_value'],3); ?> </td>

					<td align="right"> <? $short_access_qty=$val["order_quantity"]-$buyer_exf_arr[$buyer_id]['exfact_qty'];echo  number_format($short_access_qty,0); ?> </td>
					<td align="right"> <? $short_access_value=$val["po_value"]-$buyer_exf_arr[$buyer_id]['exfact_value'];echo number_format($short_access_value,0); ?> </td>



				</tr>
				<?
				$k++;
				$total_summ_buyer_qty+=$val["order_quantity"];
				$total_summ_buyer_value+=$val["po_value"];
				$total_summ_buyer_exfact_qty+=$buyer_exf_arr[$buyer_id]['exfact_qty'];
				$total_summ_buyer_exfact_value+=$buyer_exf_arr[$buyer_id]['exfact_value'];
				$total_summ_buyer_short_access_qty+=$short_access_qty;
				$total_summ_buyer_short_access_value+=$short_access_value;
				}
				?>
				<tfoot>
					<th colspan="2" align="right">Total </th>
					<th align="right"> <? echo number_format($total_summ_buyer_qty,0); ?> </th>
					<th align="right"> <? echo number_format($total_summ_buyer_value,0); ?> </th>
					<th align="right"> <? echo number_format($total_summ_buyer_exfact_qty,0); ?> </th>
					<th align="right"> <? echo number_format($total_summ_buyer_exfact_value,0); ?> </th>
					<th align="right"> <? echo number_format($total_summ_buyer_short_access_qty,0); ?> </th>
					<th align="right"> <? echo number_format($total_summ_buyer_short_access_value,0); ?> </th>

				</tfoot>
			  </table>

			  <br/>
			  <table style="margin-left:200px; margin-top:5px" id="table_notes">
           	 <tr>
                <td bgcolor="orange" height="15" width="30"></td>
                <td>RUNNING</td>
                <td  bgcolor="#99FF33" height="15" width="30">&nbsp;</td>
                <td>SHIPPED</td>
                <td bgcolor="red" height="15" width="30"></td>
                <td>ON RISK</td>

            </tr>
        	</table>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                	<tr style="font-size:13px">
                        <th width="40"  rowspan="2">SL </th>
                        <th width="110" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">File No</th>
                        <th width="70" rowspan="2">Ref. No</th>
                                 
                        <th width="110" rowspan="2">Season</th>
                        <th width="50" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Style</th>
                        <th width="100" rowspan="2">Ord / PO</th>

                        <th width="80" rowspan="2">Qty In Pcs</th>
						<th width="80" rowspan="2">Ord Rcv Date</th>
                        <th width="80" rowspan="2">Publish Ship Date</th>
                        <th width="80" rowspan="2">Original Ship Date</th>
                         
                        <th width="110" rowspan="2">Item</th>
                        <th width="100" rowspan="2">Body Part</th>
                        <th width="150" rowspan="2">Fabrication</th>
                        <th width="80" rowspan="2">Color</th>
						<th width="" colspan="2">LabDip</th>
						<th width="" colspan="2">Pso Approval Status</th>
						<th width="" colspan="2">Eso Approval Status</th>
						<th width="" colspan="2">Sample Approval Status</th>
						<th width="80" rowspan="2">Req. Fab. In Kg</th>
						<th width="80" rowspan="2">Yarn Count</th>
						<th width="80" rowspan="2">Width</th>
						<th width="" colspan="3">Yarn Status</th>
						<th width="" colspan="3">Knitting Status</th>
						<th width="" colspan="3">Dyeing Status</th>
						<th width="" colspan="2">Fab. Store Status</th>
						<th width="80" rowspan="2">Fab. Del. Date</th>

						<th width="" colspan="3">Cutting Status</th>
						<th width="" colspan="2">Printing Status</th>
						<th width="" colspan="2">Emb. Prod Status</th>
						<th width="" colspan="3">Sewing & Finish Status</th>
						<th width="" colspan="4">Shipment Status</th>
						<th width="" rowspan="2">Ex. Factory Date</th>
					</tr>
					 <tr>
                        <th width="80">Submit</th>
                        <th width="80">Approve</th>
                        <th width="80">Submit</th>
                        <th width="80">Approve</th>
						<th width="80">Submit</th>
                        <th width="80">Approve</th>
						<th width="80">PP </th>
                        <th width="80">Size Set</th>

						<th width="80" title="Yarn Status">Requ. Qty </th>
                        <th width="80">Y/Issue</th>
						<th width="80">Balance</th>

						<th width="80" title="Knitting Status">Prog. Qty </th>
                        <th width="80">Knit Prod</th>
						<th width="80">Balance</th>

						<th width="80" title="Fab Store Status">Batch Qty </th>
                        <th width="80">Dyeing Qty </th>
						<th width="80">Balance</th>


						<th width="80">Inhouse</th>
						<th width="80">Balance</th>

						<th width="80" title="Printing Status"> Req. Qty</th>
						<th width="80">Cut Qty</th>

						<th width="80" title="Emb Prod Status"> Cut In</th>
						<th width="80">Recv</th>
						<th width="80" title="Emb Prod Status"> Send</th>
						<th width="80">Recv</th>
						<th width="80" title=""> Send</th>

						<th width="80" title="Sew Fin Status"> Input</th>
						<th width="80">Output</th>
						<th width="80">Finish</th>

						<th width="80" title="Shipment Status"> Shipped Qty</th>

						<th width="80">Unit Price</th>
						<th width="80">Total Value</th>
						<th width="80">Excess Value</th>
                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

                <?
					$po_rowspan_arr=array();$job_rowspan_arr=array();$color_rowspan_arr=array();$fab_rowspan_arr=array();

				   foreach($po_color_data_arr as $job_id=>$job_data)
					{
						$job_rowspan=0;
						foreach($job_data as $po_id=>$po_data)
						{
							$po_rowspan=0;
							foreach($po_data as $body_id=>$body_data)
							{

									$color_rowspan=0;
									foreach($body_data as $color_id=>$val)
									{
										$job_rowspan++;
										$po_rowspan++;
										$color_rowspan++;
										//$fab_rowspan++;
									}
									$job_rowspan_arr[$job_id]=$job_rowspan;
									$po_rowspan_arr[$job_id][$po_id]=$po_rowspan;
									$color_rowspan_arr[$job_id][$po_id][$body_id]=$color_rowspan;
									//$fab_rowspan_arr[$job_id][$po_id][$body_id][$fab_dsec]=$fab_rowspan;

							}
						}
					 }
						//print_r($color_rowspan_arr);

						$i=1;$grand_fabric_qty_kg=$grand_tot_program_qnty=$grand_tot_knit_qty=$tot_order_qty=$tot_plancut_qty=$tot_cutting_qty=$tot_cutting_bal_qty=$tot_print_issue_qnty_in=$tot_print_recv_qnty_in=$tot_emb_issue_qnty_in=$tot_emb_recv_qnty_in=$tot_sew_input_qnty_in=$tot_sew_recv_qnty_output=$tot_finish_qnty_in=$grand_tot_shipped_qty=$grand_po_value=$grand_tot_excess_value=$grand_tot_yarn_req_qty=$grand_tot_yarn_issue_qty=$grand_tot_yarn_balance_qty=$grand_tot_knit_balance_qty=$grand_tot_batch_qty=$grand_tot_dyeing_qty=$grand_tot_dyeing_balance=0;
						foreach($po_color_data_arr as $job_id=>$job_data)
						{
							$x=1;$sub_tot_order_qty=$sub_fabric_qty_kg=$sub_tot_plancut_qty=$sub_tot_cutting_bal_qty=$sub_tot_cutting_qty=$sub_tot_print_issue_qnty_in=$sub_tot_print_recv_qnty_in=$sub_tot_emb_issue_qnty_in=$sub_tot_emb_recv_qnty_in=$sub_sew_input_qnty_in=$sub_tot_sew_recv_qnty_output=$sub_tot_finish_qnty_in=$sub_tot_shipped_qty=$sub_tot_po_value=$sub_tot_excess_value=$sub_tot_program_qnty=$sub_tot_knit_qty=$sub_tot_yarn_req_qty=$sub_tot_yarn_issue_qty=$sub_tot_yarn_issue_balance_qty=$sub_tot_knit_balance_qty=$sub_tot_batch_qty=$sub_tot_dyeing_qty=$sub_dyeing_balance=0;

						 foreach($job_data as $po_id=>$po_data)
						 {
							$y=1;
							foreach($po_data as $body_id=>$body_data)
							{

								$z=1;
								foreach($body_data as $color_id=>$val)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";



							$body_part_id=$val["body_part_id"];$deter_min_id=$val["deter_min_id"];
							$dia_type_id=rtrim($sales_order_arr[$job_id][$deter_min_id]['width_dia_type'],',');
							$ycountData=rtrim($sales_order_arr[$job_id][$deter_min_id]['ycount'],',');
							$yarn_req_grey_qty=$yarn_req_qty_requisition_arr[$job_id]['requisition_qnty'];
							//$prog_sales_qty_arr[$job_no]['program_id']
							$knit_qty_check=$knit_data_array[$job_id]['quantity'];
							$shiping_status=$exfactory_data_array[$po_id]['shiping_status'];

							$program_id=rtrim($prog_sales_qty_arr[$job_id]['program_id'],',');
						  	$program_id=implode(",",array_unique(explode(",",$program_id)));
							$requisition_id=rtrim($yarn_req_qty_requisition_arr[$job_id]['requisition_id'],',');
							$requisition_ids=implode(",",array_unique(explode(",",$requisition_id)));

							$yarn_issue_qty=$yarn_issue_qty_requisition_arr[$job_id]['yarn_issue_qnty'];
							$yarn_trans_id=rtrim($yarn_issue_qty_requisition_arr[$job_id]['yarn_issue_id'],',');
							$yarn_trans_id=implode(",",array_unique(explode(",",$yarn_trans_id)));
							//print $grey_qty.',';
							//$ycount=array_unique(explode(",",$ycount));
							$dia_type_id=implode(",",array_unique(explode(",",$dia_type_id)));
							$item_id=rtrim($val["item_id"],',');
							$item_ids=implode(",",array_unique(explode(",",$item_id)));
							//$yarn_count=rtrim($val["yarn_count"],',');
							$yarn_counts=implode(",",array_unique(explode(",",$ycountData)));

							$fabric_qty_kg=$booking_data_array[$po_id][$body_id][$color_id]['fin_fab_qnty'];
						//	echo $val["pub_shipment_date"].'='.$exfactory_data_array[$po_id]['ex_factory_date'].',';
							if($knit_qty_check>0 && ($shiping_status==2 || $shiping_status=='')) //Knitting Prod
							{
								$po_color_td="orange";
							}
							else if($shiping_status==3 && ($val["pub_shipment_date"]>$exfactory_data_array[$po_id]['ex_factory_date']) )
							{
								$po_color_td="#99FF33";
							}
							else if($val["pub_shipment_date"]<$exfactory_data_array[$po_id]['ex_factory_date'])
							{
								if($exfactory_data_array[$po_id]['ex_factory_date']!='')
								{
									$po_color_td="red";
								}
							}
							else $po_color_td="";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<?
								if($x==1)
								{
								?>
								<td width="40"  valign="middle" title="<? echo $shiping_status;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo $i; ?></td>
                                
                                <td width="110"  valign="middle" title="<? echo $shiping_status;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                                <td width="70"  valign="middle" title="<? echo $shiping_status;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo $val["file_no"]; ?></td>
                                <td width="70"  valign="middle" title="<? echo $shiping_status;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo $val["ref_no"]; ?></td>
                                
								<td width="110" valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><div style="word-wrap:break-word; width:110px"><? echo $val["season"]; ?></div></td>
								<td width="50" align="center"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><p><? echo $val["job_no"]; ?>&nbsp;</p></td>
								<td width="100" align="center"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><p><? echo $val["style"]; ?>&nbsp;</p></td>
								<?
								}
								if($y==1)
								{
								$tot_order_qty+=$po_wise_data_arr[$po_id]["po_quantity"];
							 	$sub_tot_order_qty+=$po_wise_data_arr[$po_id]["po_quantity"];
								?>
								<td width="100" bgcolor="<? echo $po_color_td;?>" title="PO Id<? echo $po_id;?>"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><div style="word-wrap:break-word; width:100px"><? echo $val["po_number"]; ?></div></td>
								<td width="80" align="right" valign="middle"  rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><p><? echo number_format($po_wise_data_arr[$po_id]["po_quantity"],0); ?></p></td>
								<td width="80" title="Max Ord Recv Date" valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><div><? if(trim($val["po_received_date"])!="" && trim($val["po_received_date"])!='0000-00-00') echo change_date_format($val["po_received_date"]); ?>&nbsp;</div></td>
								<td width="80" title="Max Pub ShipDate"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><div><? echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</div></td>
                                <td width="80" title="Original ShipDate"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><div><? echo change_date_format($val["shipment_date"]); ?>&nbsp;</div></td>
								<?
								}

								if($x==1)
								{
								?>

								<td width="110"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>" ><div><? echo $item_ids; ?>&nbsp;</div></td>
								<?
									$knit_qty=$knit_data_array[$job_id]['quantity'];
									$knit_trans_id=rtrim($knit_data_array[$job_id]['knit_dtls_id'],',');
									$program_qnty=$prog_sales_qty_arr[$job_id]['program_qnty'];
									$batch_qty=$batch_qty_arr[$job_id]['batch_qty'];
									$batch_id=$batch_qty_arr[$job_id]['batch_id'];
									$batch_id=rtrim($dyeing_qnty_arr[$job_no]['batch_id'],',');
									$dyeing_qty=$dyeing_qnty_arr[$job_no]['dyeing_qty'];
									$dyeing_batch_id=rtrim($dyeing_qnty_arr[$job_no]['batch_id'],',');
								}
								//$color_rowspan_arr[$job_id][$po_id][$body_id]
								if($z==1)
								{
								?>

								<td  width="100" rowspan="<? echo $color_rowspan_arr[$job_id][$po_id][$body_id];?>"><div style="word-wrap:break-word; width:100px"><? echo $body_part[$body_part_id]; ?></div></td>
								<td  width="150" rowspan="<? echo $color_rowspan_arr[$job_id][$po_id][$body_id];?>"><div style="word-wrap:break-word; width:150px"><? echo $val["desc"]; ?></div></td>
								<?
								}
								?>

                                <!--40 dnw-->
								<td width="80" style="word-break:break-all"><? echo $color_library[$color_id]; ?></td>
								<td align="right" width="80"><? echo change_date_format($labdip_data_array[$po_id][$color_id]['submit_date']); ?></td>
								<td align="right" width="80"><? echo change_date_format($labdip_data_array[$po_id][$color_id]['approv_date']);; ?></td>
								<td align="right" width="80"><? echo change_date_format($embl_data_array[$po_id][$color_id]['print_submit_date']); ?></td>
								<td align="right" width="80"><? echo change_date_format($embl_data_array[$po_id][$color_id]['print_approv_date']); ?></td>

								<?
								/*if($x==1)
								{*/
								?>
								<td align="right" width="80"  valign="middle"><? echo change_date_format($embl_data_array[$po_id][$color_id]['embro_submit_date']); ?></td>
								<td align="right" width="80"  valign="middle"><? echo change_date_format($embl_data_array[$po_id][$color_id]['embro_approv_date']); ?></td>

								<td align="right" width="80"  valign="middle"><? echo change_date_format($samp_data_array[$po_id][$color_id]['samp_submit_date']); ?></td>
								<td align="right" width="80"  valign="middle"><? echo change_date_format($samp_data_array[$po_id][$color_id]['samp_approv_date']); ?></td>

								<?

									//}
									//$prog_data_array[$po_id][$body_id][$color_id]['program_qnty'];




								?>

								<td align="right" width="80"><? echo number_format($fabric_qty_kg); ?></td>
								<?
								if($x==1)
								{
								$sub_tot_yarn_req_qty+=$yarn_req_grey_qty;
								$sub_tot_yarn_issue_qty+=$yarn_issue_qty;
								$sub_tot_yarn_issue_balance_qty+=$yarn_req_grey_qty-$yarn_issue_qty;

								$sub_tot_program_qnty+=$program_qnty;
								$sub_tot_knit_qty+=$knit_qty;
								$sub_tot_batch_qty+=$batch_qty;
								$sub_tot_dyeing_qty+=$dyeing_qty;
								$sub_tot_knit_balance_qty+=$program_qnty-$knit_qty;
								$tot_dyeing_balance=$batch_qty-$dyeing_qty;
								$sub_dyeing_balance+=$tot_dyeing_balance;

								$fab_store_qty=$fab_store_data_array[$job_id]['store_quantity'];
								$fab_store_dtls_id=rtrim($fab_store_data_array[$job_id]['dtls_id'],',');

								   $yarn_req_grey_qty_view="<a href='##' onClick=\"openmypage('".$requisition_ids."','".$job_id."','show_po_yarn_requisition_dtls','1')\"> ".number_format($yarn_req_grey_qty,2)." </a>";
								 $yarn_issue_qty_view="<a href='##' onClick=\"openmypage('".$yarn_trans_id."','".$job_id."','show_yarn_issue_dtls','2')\"> ".number_format($yarn_issue_qty,2)." </a>";
								  $program_qnty_view="<a href='##' onClick=\"openmypage('".$program_id."','".$job_id."','show_program_sales_dtls','3')\"> ".number_format($program_qnty,2)." </a>";
								   $knit_qty_view="<a href='##' onClick=\"openmypage('".$knit_trans_id."','".$job_id."','show_knitting_recv_dtls','4')\"> ".number_format($knit_qty,2)." </a>";
								   $batch_qty_view="<a href='##' onClick=\"openmypage('".$batch_id."','".$job_id."','show_batch_popup_dtls','5')\"> ".number_format($batch_qty,2)." </a>";
								$dyeing_qty_view="<a href='##' onClick=\"openmypage('".$dyeing_batch_id."','".$job_id."','show_dyeing_popup_dtls','6')\"> ".number_format($dyeing_qty,2)." </a>";
								$fab_store_qty_view="<a href='##' onClick=\"openmypage('".$fab_store_dtls_id."','".$job_id."','show_fab_store_popup_dtls','7')\"> ".number_format($fab_store_qty,2)." </a>";
								?>
								<td align="right"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>" width="80"><div style="word-wrap:break-word; width:80px"><? echo $yarn_counts; ?></div></td>
								<td align="right"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>" width="80"><? echo $dia_type_id; ?></td>

								<td align="right"  valign="middle"  rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? echo $yarn_req_grey_qty_view; ?></td>
								<td align="right"   valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? echo $yarn_issue_qty_view; ?></td>

								<td align="right"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? $yarn_balance=$yarn_req_grey_qty-$yarn_issue_qty;echo number_format($yarn_balance); ?></td>
								<td align="right"   valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? echo $program_qnty_view; ?></td>
								<td align="right"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? echo $knit_qty_view; ?></td>
								<td align="right"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? echo number_format($program_qnty-$knit_qty); ?></td>

								<td align="center"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? echo   $batch_qty_view; ?></td>


								<td align="right"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? echo $dyeing_qty_view; ?></td>
								<td align="right"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"  width="80"><? echo number_format($tot_dyeing_balance); ?></td>
								 <td align="right" width="80"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>" title="Fab Store -Dyeing Qty"><?

								//$dyeing_req_qty=0;
								$balance_fab_store_qty=$dyeing_qty-$fab_store_qty;
								echo $fab_store_qty_view; ?></td>
								<td align="right" width="80" valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo number_format($balance_fab_store_qty); ?></td>
								<?
								}
								?>

								<?
								if($x==1)
								{
								?>
								<td align="center" width="80"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo change_date_format($fab_deli_data_array[$po_id]['delivery_date']); ?></td>
								<?
								}

								if($y==1)
								{
								$plan_cut_qnty=$po_wise_data_arr[$po_id]["plan_cut_qnty"];//val["plan_cut_qnty"]
								$cutting_qnty=$garment_prod_data_arr[$po_id]['cutting_qnty'];

								$print_issue_qnty_in=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
								$print_recv_qnty_in=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
								$emb_issue_qnty_in=$garment_prod_data_arr[$po_id]['emb_issue_qnty_in'];
								$emb_recv_qnty_in=$garment_prod_data_arr[$po_id]['emb_recv_qnty_in'];

								$sew_input_qnty_in=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
								$sew_recv_qnty_output=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
								$finish_qnty_in=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
								$po_value=$po_wise_data_arr[$po_id]["po_value"];
								$shipped_qty=$exfactory_data_array[$po_id]['ex_factory_qnty'];



								$tot_plancut_qty+=$plan_cut_qnty;
							 	$sub_tot_plancut_qty+=$plan_cut_qnty;

								$cut_bal=$plan_cut_qnty-$cutting_qnty;
								$tot_cutting_qty+=$cutting_qnty;
								$tot_cutting_bal_qty+=$cut_bal;

								$tot_print_issue_qnty_in+=$print_issue_qnty_in;
								$tot_print_recv_qnty_in+=$print_recv_qnty_in;
								$tot_emb_issue_qnty_in+=$emb_issue_qnty_in;
								$tot_emb_recv_qnty_in+=$emb_recv_qnty_in;

								$tot_sew_input_qnty_in+=$sew_input_qnty_in;
								$tot_sew_recv_qnty_output+=$sew_recv_qnty_output;
								$tot_finish_qnty_in+=$finish_qnty_in;
								//$tot_shipped_qty+=$shipped_qty;

							 	$sub_tot_cutting_qty+=$cutting_qnty;
								$sub_tot_cutting_bal_qty+=$cut_bal;
								$sub_tot_print_issue_qnty_in+=$print_issue_qnty_in;
								$sub_tot_print_recv_qnty_in+=$print_recv_qnty_in;
								$sub_tot_emb_issue_qnty_in+=$emb_issue_qnty_in;
								$sub_tot_emb_recv_qnty_in+=$emb_recv_qnty_in;

								$sub_sew_input_qnty_in+=$sew_input_qnty_in;
								$sub_tot_sew_recv_qnty_output+=$sew_recv_qnty_output;
								$sub_tot_finish_qnty_in+=$finish_qnty_in;

								$sub_tot_po_value+=$po_value;
								$sub_tot_shipped_qty+=$shipped_qty;




								?>

								<td align="right" width="80"  valign="middle" title="Cut Req" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($plan_cut_qnty,0); ?></td>
								<td align="right" width="80"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($cutting_qnty); ?></td>
								<td align="right" width="80" valign="middle"  rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($cut_bal); ?></td>

								<td align="right" width="80"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($print_recv_qnty_in); ?></td>
								<td align="right" width="80" valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($print_issue_qnty_in); ?></td>
								<td align="right" width="80"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($emb_recv_qnty_in); ?></td>
								<td align="right" width="80"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($emb_issue_qnty_in); ?></td>

								<td align="right" width="80"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($sew_input_qnty_in); ?></td>
								<td align="right" width="80" valign="middle"  rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($sew_recv_qnty_output); ?></td>
								<td align="right" width="80"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($finish_qnty_in); ?></td>
								<td align="right" width="80" valign="middle"  rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($shipped_qty); ?></td>

								<?


								$unit_price=$po_wise_data_arr[$po_id]["unit_price"];
								$shipped_value=$shipped_qty*$unit_price;
								$excess_value=$po_value-$shipped_value;
								//$sub_tot_shipped_qty+=$shipped_qty;
								$sub_tot_excess_value+=$excess_value;
								?>

								<td align="right" width="80"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($unit_price); ?></td>
								<td align="right" width="80"  valign="middle" rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($po_value); ?></td>
								<td align="right" width="80"  valign="middle"  rowspan="<? echo $po_rowspan_arr[$job_id][$po_id];?>"><? echo number_format($excess_value); ?></td>
								<?
								}
								if($x==1)
								{
								?>
								<td align="left"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo change_date_format($exfactory_data_array[$po_id]['ex_factory_date']); ?></td>
								<?
								}
								?>
							</tr>
							<?
								$sub_fabric_qty_kg+=$fabric_qty_kg;
								$grand_fabric_qty_kg+=$fabric_qty_kg;


								$i++;$x++;$y++;$z++;
								} //color End

							}
							$grand_tot_shipped_qty+=$shipped_qty;
							$grand_po_value+=$po_value;
							$grand_tot_excess_value+=$excess_value;


						  }
						  ?>
						 	<tr style="font-size:13px" class="tbl_bottom">
							<td width="40">&nbsp;</td>
                            <td width="110">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            
							<td width="110">&nbsp;</td>
							<td width="50">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">Sub Total</td>
							<td width="80" align="right"><? echo number_format($sub_tot_order_qty,0); $sub_tot_order_qty=0;?></td>
							<td width="80"></td>
                            <td width="80"></td>
							<td width="80"></td>
							<td width="110"></td>
							<td width="100"></td>
							<td width="150" align="right" id=""></td>
							<td width="80" align="right" id=""></td>
							<td width="80" align="right" id=""></td>
							<td width="80" align="right" id=""></td>
							<td width="80" align="right" id=""></td>
							<td width="80" align="right" id=""><? //echo number_format($sub_tot_order_qty,0); $sub_tot_order_qty=0;?></td>
							<td width="80" align="right" id="" bgcolor="#FFFFCC"></td>
							<td width="80" align="right" id=""></td>
							<td width="80" align="right" id=""></td>
							<td width="80" align="right" id=""></td>
							<td width="80" align="right" id=""><? echo number_format($sub_fabric_qty_kg,0); $sub_fabric_qty_kg=0;?></td>
							<td width="80" align="right" id="">&nbsp;</td>

							<td width="80" align="right" id="">&nbsp;</td>

							<td width="80" align="right" id=""><? echo number_format($sub_tot_yarn_req_qty);$sub_tot_yarn_req_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_yarn_issue_qty);$sub_tot_yarn_issue_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_yarn_issue_balance_qty); $sub_tot_yarn_issue_balance_qty=0;?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_program_qnty);$sub_tot_program_qnty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_knit_qty);$sub_tot_knit_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_knit_balance_qty); $sub_tot_knit_balance_qty=0;?></td>
							<td width="80" align="right"><? echo number_format($sub_tot_batch_qty); $sub_tot_batch_qty=0;?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_dyeing_qty);$sub_tot_dyeing_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_dyeing_balance);$sub_dyeing_balance=0; ?></td>
							<td width="80" align="right" id=""><? //echo number_format($sub_dyeing_balance);$sub_dyeing_balance=0; ?></td>
							<td width="80" align="right" id=""><? //echo number_format($tot_sewRcvBal_qty); ?></td>
							<td width="80" align="right" id="">&nbsp;</td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_plancut_qty);$sub_tot_plancut_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_cutting_qty); $sub_tot_cutting_qty=0;?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_cutting_bal_qty);$sub_tot_cutting_bal_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_print_recv_qnty_in);$sub_tot_print_recv_qnty_in=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_print_issue_qnty_in);$sub_tot_print_issue_qnty_in=0;  ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_emb_recv_qnty_in);$sub_tot_emb_recv_qnty_in=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_emb_issue_qnty_in);$sub_tot_emb_issue_qnty_in=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_sew_input_qnty_in);$sub_sew_input_qnty_in=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_sew_recv_qnty_output); $sub_tot_sew_recv_qnty_output=0;?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_finish_qnty_in);$sub_tot_finish_qnty_in=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_shipped_qty);$sub_tot_shipped_qty=0; ?></td>

							<td width="80" align="right" id=""><? //echo number_format($sub_tot_shipped_qty); ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_po_value);$sub_tot_po_value=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_excess_value); ?></td>
							<td width="" align="right" id="">&nbsp;</td>
               			 </tr>
						 <?
						 $grand_tot_yarn_req_qty+=$yarn_req_grey_qty;
						 $grand_tot_yarn_issue_qty+=$yarn_issue_qty;
						 $grand_tot_yarn_balance_qty+=$yarn_req_grey_qty-$yarn_issue_qty;
						 $grand_tot_program_qnty+=$program_qnty;
						 $grand_tot_knit_qty+=$knit_qty;
						 $grand_tot_knit_balance_qty+=$program_qnty-$knit_qty;
						 $grand_tot_batch_qty+=$batch_qty;
						 $grand_tot_dyeing_qty+=$dyeing_qty;
						 $grand_tot_dyeing_balance+=$sub_dyeing_balance;
						}

					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                            
                    <td width="110">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">Total</td>
					<td width="80"><? echo number_format($tot_order_qty,0); ?></td>
					<td width="80"></td>
                    <td width="80"></td>
					<td width="80"></td>
					<td width="110"></td>
					<td width="100"></td>
                    <td width="150" align="right" id="td_yarn_req_qty"></td>
                    <td width="80" align="right" id="td_wovenReqQty"></td>
                    <td width="80" align="right" id="td_wovenRecQty"></td>
                    <td width="80" align="right" id="td_wovenRecBalQty"></td>
                    <td width="80" align="right" id="td_wovenIssueQty"></td>
                    <td width="80" align="right" id="td_wovenIssueBalQty"></td>
                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"></td>
                    <td width="80" align="right" id="td_cutting_qty"></td>
                    <td width="80" align="right" id="td_printIssIn_qty"></td>
                    <td width="80" align="right" id="td_printIssOut_qty"></td>
					<td width="80" align="right" id="td_printRcvOut_qty"><? echo number_format($grand_fabric_qty_kg); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty">&nbsp;</td>

                    <td width="80" align="right" id="td_printRcvIn_qty">&nbsp;</td>

                    <td width="80" align="right" id="td_sales_yarn_qty"><? echo number_format($grand_tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_grand_tot_yarn_issue_qty"><? echo number_format($grand_tot_yarn_issue_qty,2); ?></td>
                    <td width="80" align="right" id="td_grand_tot_yarn_issue_balance"><? echo number_format($grand_tot_yarn_balance_qty,2); ?></td>
                    <td width="80" align="right" id="td_sewInOutput_qty"><? echo number_format($grand_tot_program_qnty); ?></td>
                    <td width="80" align="right" id="td_sewIn_qty"><? echo number_format($grand_tot_knit_qty); ?></td>
                    <td width="80" align="right" id="td_sewInBal_qty"><? echo number_format($grand_tot_knit_balance_qty); ?></td>
                    <td width="80"><? echo number_format($grand_tot_batch_qty); ?></td>
                    <td width="80" align="right" id="td_batch_qty"><? echo number_format($grand_tot_dyeing_qty); ?></td>
                    <td width="80" align="right" id="td_dyeing_qty"><? echo number_format($grand_tot_dyeing_balance); ?></td>
                    <td width="80" align="right" id="td_sewRcv_qty"><? //echo number_format($grand_tot_dyeing_balance); ?></td>
                    <td width="80" align="right" id="td_sewRcvBal_qty"><? //echo number_format($tot_plancut_qty); ?></td>
                    <td width="80" align="right" id="td_sewRcvRjt_qty">&nbsp;</td>
                    <td width="80" align="right" id="td_washRcvIn_qty"><? echo number_format($tot_plancut_qty); ?></td>
                    <td width="80" align="right" id="td_washRcvOut_qty"><? echo number_format($tot_cutting_qty); ?></td>
                    <td width="80" align="right" id="td_gmtFinIn_qty"><? echo number_format($tot_cutting_bal_qty); ?></td>

                    <td width="80" align="right" id=""><? echo number_format($tot_print_recv_qnty_in); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($tot_print_issue_qnty_in); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($tot_emb_recv_qnty_in); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($tot_emb_issue_qnty_in); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($tot_sew_input_qnty_in); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($tot_sew_recv_qnty_output); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($tot_finish_qnty_in);//number_format($tot_finish_qnty_in); ?></td>
					 <td width="80" align="right" id=""><? echo number_format($grand_tot_shipped_qty); ?></td>

                    <td width="80" align="right" id=""></td>
                    <td width="80" align="right" id=""><? echo number_format($grand_po_value); ?></td>
					<td width="80" align="right" id=""><? echo number_format($grand_tot_excess_value); ?></td>
                    <td width="" align="right">&nbsp;</td>
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
    echo "$html**$filename**$cbo_search_type";
    exit();
}
if ($action == "show_po_yarn_requisition_dtls") {
	echo load_html_head_contents("Yarn Requisition Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);


	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	//echo $req_id.'DD';
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<div style="width:870px" align="center"> <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:865px; margin-left:3px">
			<div id="report_div">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="11"><b>Yarn Requisition</b></th>
					</thead>
					<thead>
						<th width="105">SL</th>
						<th width="105">Booking No</th>
						<th width="80">Requisition No</th>
						<th width="75">Requisition Date</th>
						<th width="70">Brand</th>
						<th width="200">Yarn Description</th>
						<th width="60">Lot No</th>
						<th width="80">Yarn Type</th>
						<th width="90">Requisition Qnty</th>
					</thead>
					<?
					$i = 1;
					$total_yarn_issue_qnty = 0;
					$total_yarn_issue_qnty_out = 0;
					if($req_id!="")
					{
						$sql = "select a.dtls_id,a.booking_no, a.determination_id,b.knit_id,b.requisition_no,b.requisition_date,b.yarn_qnty,a.po_id,b.id,b.prod_id,c.product_name_details,yarn_type,c.lot,c.brand from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($req_id)";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	
							$issue_to = "";
							if ($row[csf('knit_dye_source')] == 1) {
								$issue_to = $company_library[$row[csf('knit_dye_company')]];
							} else {
								$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
							}
	
							$yarn_issued = $row[csf('issue_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
								<td width="105" class="center"><? echo $i; ?></td>
								<td width="105" class="center"><? echo $row[csf('booking_no')]; ?></td>
								<td width="80" class="center"><? echo $row[csf('requisition_no')]; ?></td>
								<td width="75" class="center"><? echo change_date_format($row[csf('requisition_date')]); ?></td>
								<td width="70" class="center"><? echo $brand_array[$row[csf('brand')]]; ?></td>
								<td width="60" class="center"><? echo $row[csf('product_name_details')]; ?></td>
								<td width="60" class="center"><? echo $row[csf('lot')]; ?></td>
								<td width="80" class="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
								<td align="right" width="90"><? echo $row[csf('yarn_qnty')]; ?></td>
							</tr>
							<?
							$total_req_qnty += $row[csf('yarn_qnty')];
							$i++;
						}
					}
					?>
					<tr style="font-weight:bold">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right">Total</td>
						<td align="right"><? echo number_format($total_req_qnty, 2); ?></td>
					</tr>
				</table>
			</div>

		</fieldset>
		<?
		exit();
	}
if ($action == "show_yarn_issue_dtls") //
 {
			echo load_html_head_contents("Yarn Requisition Details info", "../../../../", 1, 1,$unicode,'','');
 			extract($_REQUEST);
		$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		//$order_id = explode('_', $req_id);
		?>
		<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}
		</script>
		<div style="width:870px" align="center"><input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
			<fieldset style="width:865px; margin-left:3px">
				<div id="report_div">

				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="11"><b>Yarn Issue</b></th>
					</thead>
					<thead>
						<th width="105">Issue Id</th>
						<th width="90">Issue To</th>
						<th width="105">Booking No</th>
						<th width="80">Challan No</th>
						<th width="70">Brand</th>
						<th width="200">Yarn Description</th>
						<th width="60">Lot No</th>
						<th width="75">Issue Date</th>
						<th width="80">Yarn Type</th>
						<th width="90">Issue Qnty (In)</th>
						<th>Issue Qnty (Out)</th>
					</thead>
					<?
					$i = 1;
					$total_yarn_issue_qnty = 0;
					$total_yarn_issue_qnty_out = 0;
				/*$sql_yarn_iss = "select a.po_id,a.determination_id,d.mst_id,sum(d.cons_quantity) cons_quantity from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join inv_transaction d on (b.requisition_no=d.requisition_no and d.transaction_type=2 and b.prod_id=d.prod_id) where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.id in ($req_id) group by a.po_id,d.mst_id,a.determination_id";*/
				if($req_id!="")
				{
				$sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and d.id in ($req_id)  and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$issue_to = "";
						if ($row[csf('knit_dye_source')] == 1) {
							$issue_to = $company_library[$row[csf('knit_dye_company')]];
						} else {
							$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
						}

						$yarn_issued = $row[csf('issue_qnty')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="105" class="center"><? echo $row[csf('issue_number')]; ?></td>
							<td width="90" class="center"><? echo $issue_to; ?></td>
							<td width="105" class="center"><? echo $row[csf('booking_no')]; ?></td>
							<td width="80" class="center"><? echo $row[csf('challan_no')]; ?></td>
							<td width="70" class="center"><? echo $brand_array[$row[csf('brand_id')]]; ?></td>
							<td width="60" class="center"><? echo $row[csf('product_name_details')]; ?></td>
							<td width="60" class="center"><? echo $row[csf('lot')]; ?></td>
							<td width="75" class="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
							<td width="80" class="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
							<td align="right" width="90">
								<?
								if ($row[csf('knit_dye_source')] != 3) {
									echo number_format($yarn_issued, 2);
									$total_yarn_issue_qnty += $yarn_issued;
								} else echo "&nbsp;";
								?>
							</td>
							<td align="right">
								<?
								if ($row[csf('knit_dye_source')] == 3) {
									echo number_format($yarn_issued, 2);
									$total_yarn_issue_qnty_out += $yarn_issued;
								} else echo "&nbsp;";
								?>
							</td>
						</tr>
						<?
						$i++;
					}
				}
					?>
					<tr style="font-weight:bold">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right">Total</td>
						<td align="right"><? echo number_format($total_yarn_issue_qnty, 2); ?></td>
						<td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2); ?></td>
					</tr>
					<tr style="font-weight:bold">
						<td align="right" colspan="10">Issue Total</td>
						<td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2); ?></td>
					</tr>
					<thead>
						<th colspan="11"><b>Yarn Return</b></th>
					</thead>
					<thead>
						<th width="105">Return Id</th>
						<th width="90">Return From</th>
						<th width="105">Booking No</th>
						<th width="80">Challan No</th>
						<th width="70">Brand</th>
						<th width="200">Yarn Description</th>
						<th width="60">Lot No</th>
						<th width="75">Return Date</th>
						<th width="80">Yarn Type</th>
						<th width="90">Return Qnty (In)</th>
						<th>Return Qnty (Out)</th>
					</thead>
					<?
					$total_yarn_return_qnty = 0;
					$total_yarn_return_qnty_out = 0;
					//$issue_ids = return_field_value("listagg(mst_id ,',') within group (order by mst_id) as mst_id","inv_transaction", "requisition_no=$order_id[1]","mst_id");
					if($req_id!="")
					{
					$sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.product_name_details,c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.prod_id=c.id and d.id in ($req_id)) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot,c.product_name_details, c.yarn_type, c.product_name_details, d.brand_id";
					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$return_from = "";
						if ($row[csf('knitting_source')] == 1) {
							$return_from = $company_library[$row[csf('knitting_company')]];
						} else {
							$return_from = $supplier_details[$row[csf('knitting_company')]];
						}

						$yarn_returned = $row[csf('returned_qnty')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="90"><p><? echo $return_from; ?></p></td>
							<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
							<td align="right" width="90">
								<?
								if ($row[csf('knitting_source')] != 3) {
									echo number_format($yarn_returned, 2);
									$total_yarn_return_qnty += $yarn_returned;
								} else echo "&nbsp;";
								?>
							</td>
							<td align="right">
								<?
								if ($row[csf('knitting_source')] == 3) {
									echo number_format($yarn_returned, 2);
									$total_yarn_return_qnty_out += $yarn_returned;
								} else echo "&nbsp;";
								?>
							</td>
						</tr>
						<?
						$i++;
					}
					}
					?>
					<tr style="font-weight:bold">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right">Balance</td>
						<td align="right"><? echo number_format($total_yarn_issue_qnty - $total_yarn_return_qnty, 2); ?></td>
						<td align="right"><? echo number_format($total_yarn_issue_qnty_out - $total_yarn_return_qnty_out, 2); ?></td>
					</tr>
					<tfoot>
						<tr>
							<th align="right" colspan="10">Total Balance</th>
							<th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
		exit();
	}
if ($action == "show_knitting_recv_dtls") {
			echo load_html_head_contents("Knitting Details info", "../../../../", 1, 1,$unicode,'','');
 			extract($_REQUEST);

			$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
			?>
			<script>

				var tableFilters = {
					col_operation: {
						id: ["value_receive_qnty_in", "value_receive_qnty_out", "value_receive_qnty_tot"],
						col: [7, 8, 9],
						operation: ["sum", "sum", "sum"],
						write_method: ["innerHTML", "innerHTML", "innerHTML"]
					}
				}
				$(document).ready(function (e) {
					setFilterGrid('tbl_list_search', -1, tableFilters);
				});

				function print_window() {
					document.getElementById('scroll_body').style.overflow = "auto";
					document.getElementById('scroll_body').style.maxHeight = "none";

					$('#tbl_list_search tr:first').hide();

					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

					d.close();
					document.getElementById('scroll_body').style.overflowY = "scroll";
					document.getElementById('scroll_body').style.maxHeight = "230px";

					$('#tbl_list_search tr:first').show();
				}

			</script>
			<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
				style="width:100px" class="formbutton"/></div>
				<fieldset style="width:1037px;">
					<div id="report_container">

						</table>
						<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
							<thead>
								<th colspan="12"><b>Knitting Receive Info</b></th>
							</thead>
							<thead>
								<th width="30">SL</th>
								<th width="115">Receive Id</th>
								<th width="95">Receive Basis</th>
								<th width="110">Product Details</th>
								<th width="100">Booking / Program No</th>
								<th width="60">Machine No</th>
								<th width="75">Production Date</th>
								<th width="80">Inhouse Production</th>
								<th width="80">Outside Production</th>
								<th width="80">Production Qnty</th>
								<th width="70">Challan No</th>
								<th>Kniting Com.</th>
							</thead>
						</table>
						<div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
							<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0"
							id="tbl_list_search">
							<?
							$i = 1;
							$total_receive_qnty = 0;
							$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
							$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
							$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
							if($req_id!="")
							{
							$sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and b.id in($req_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
							$result = sql_select($sql);
							foreach ($result as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$total_receive_qnty += $row[csf('quantity')];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"
									onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i; ?></td>
									<td width="115" class="center"><? echo $row[csf('recv_number')]; ?></td>
									<td width="95" class="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
									<td width="110" class="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
									<td width="100" class="center"><? echo $row[csf('booking_no')]; ?></td>
									<td width="60" class="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
									<td width="75" class="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td align="right" width="80">
										<?
										if ($row[csf('knitting_source')] != 3) {
											echo number_format($row[csf('quantity')], 2, '.', '');
											$total_receive_qnty_in += $row[csf('quantity')];
										} else echo "&nbsp;";
										?>
									</td>
									<td align="right" width="80">
										<?
										if ($row[csf('knitting_source')] == 3) {
											echo number_format($row[csf('quantity')], 2, '.', '');
											$total_receive_qnty_out += $row[csf('quantity')];
										} else echo "&nbsp;";
										?>
									</td>
									<td class="right"
									width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
									<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
									<td>
										<p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
									</td>
								</tr>
								<?
								$i++;
							}
							}
							?>
						</table>
					</div>
					<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="115">&nbsp;</th>
							<th width="95">&nbsp;</th>
							<th width="110">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="75" align="right">Total</th>
							<th width="80" align="right"
							id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
							<th width="80" align="right"
							id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
							<th width="80" align="right"
							id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
							<th width="70">&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<?
	exit();
}
if($action=="show_batch_popup_dtls"){
	echo load_html_head_contents(" Batch Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
		?>
		<script>
			function print_window() {
				document.getElementById('scroll_body').style.overflow = "auto";
				document.getElementById('scroll_body').style.maxHeight = "none";
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
				d.close();
				document.getElementById('scroll_body').style.overflowY = "scroll";
				document.getElementById('scroll_body').style.maxHeight = "230px";
			}
		</script>
			<?
			$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
			if($req_id!="")
			{
			$result = sql_select("select a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,sum(b.batch_qnty)batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 and a.id in($req_id)   group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,b.is_sales order by a.batch_date desc");

						?>
	<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>
		<fieldset style="width:1030px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="100">Batch No</th>
							<th width="70">Ext. No</th>
							<th width="150">Sales Order No</th>
							<th width="105">Booking No</th>
							<th width="80">Batch Quantity</th>
							<th width="80">Batch Date</th>
							<th width="80">Batch Against</th>
							<th width="85">Batch For</th>
							<th>Color</th>
						</tr>
					</thead>
				</table>
				<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
						<tbody>
							<?php
$i = 1;
foreach ($result as $row) {
	if ($i % 2 == 0) {
		$bgcolor = "#E9F3FF";
	} else {
		$bgcolor = "#FFFFFF";
	}

	?>
								<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>)" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
									<td style="text-align: center;" width="50"><?php echo $i; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
									<td style="text-align: center;" width="70"><?php echo $row[csf("extention_no")]; ?></td>
									<td style="text-align: center;" width="150"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
									<td style="text-align: center;" width="105"><?php echo $row[csf("booking_no")]; ?></td>
									<td width="80" align="right"><?php echo number_format($row[csf("batch_qnty")], 2); ?></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("batch_date")]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
									<td style="text-align: center;" width="85"><?php echo $batch_for[$row[csf("batch_for")]]; ?></td>
									<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
								</tr>
								<?php
$total_batch_qnty += $row[csf("batch_qnty")];
	$i++;
}
			}
?>
							<tfoot>
								<tr>
									<th colspan="5" align="right">Total</th>
									<th align="right"><? echo number_format($total_batch_qnty, 2); ?></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</fieldset>
<?
exit();
}

if($action=="show_dyeing_popup_dtls")
{
	echo load_html_head_contents(" Dyeing Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
						?>
						<script>
							function print_window() {
								document.getElementById('scroll_body').style.overflow = "auto";
								document.getElementById('scroll_body').style.maxHeight = "none";
								var w = window.open("Surprise", "#");
								var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
									'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
								d.close();
								document.getElementById('scroll_body').style.overflowY = "scroll";
								document.getElementById('scroll_body').style.maxHeight = "230px";
							}
						</script>
						<?
						$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
						if($req_id!="")
						{
						$result = sql_select("select a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, b.item_description as febric_description, sum(b.batch_qnty) as batch_qnty,c.process_end_date,c.process_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id  and c.load_unload_id=2 and c.entry_form=35 and a.id in($req_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, b.item_description,c.process_end_date,c.process_id");
						?>
	<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>
		<fieldset style="width:1030px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
				<caption><b> Deying Details</b> </caption>
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="100">Batch No</th>
							<th width="70">Ext. No</th>
							<th width="150">Sales Order No</th>
							<th width="105">Booking No</th>
							<th width="80">Dyeing Quantity</th>
							<th width="80">Dyeing Date</th>
							<th width="80">Batch Against</th>
							<th width="85">Process For</th>
							<th>Color</th>
						</tr>
					</thead>
				</table>
				<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
						<tbody>
							<?php
$i = 1;
foreach ($result as $row) {
	if ($i % 2 == 0) {
		$bgcolor = "#E9F3FF";
	} else {
		$bgcolor = "#FFFFFF";
	}

	?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
									<td style="text-align: center;" width="50"><?php echo $i; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
									<td style="text-align: center;" width="70"><?php echo $row[csf("extention_no")]; ?></td>
									<td style="text-align: center;" width="150"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
									<td style="text-align: center;" width="105"><?php echo $row[csf("booking_no")]; ?></td>
									<td width="80" align="right"><?php echo number_format($row[csf("batch_qnty")], 2); ?></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("process_end_date")]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
									<td style="text-align: center;" width="85"><?php echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
									<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
								</tr>
								<?php
$total_batch_qnty += $row[csf("batch_qnty")];
	$i++;
}
						}
?>
							<tfoot>
								<tr>
									<th colspan="5" align="right">Total</th>
									<th align="right"><? echo number_format($total_batch_qnty, 2); ?></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</fieldset>
	<?
exit();
}
if ($action == "show_program_sales_dtls") //
 {
		echo load_html_head_contents(" Batch Details info", "../../../../", 1, 1,$unicode,'','');
 		extract($_REQUEST);
		$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

		?>
		<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}
		</script>
		<div style="width:870px" align="center"><input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
			<fieldset style="width:865px; margin-left:3px">
				<div id="report_div">

				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="11"><b>Program Sales Details</b></th>
					</thead>
					<thead>
						<th width="100">Prog Id</th>
						<th width="120">Sales Order No </th>
						<th width="110">Booking No</th>
						<th width="200">Fabric Desc.</th>
						<th width="100">Prog Qnty</th>

					</thead>
					<?
					$i = 1;
					$total_yarn_issue_qnty = 0;
					$total_yarn_issue_qnty_out = 0;
					if($req_id!="")
					{
				 $salesOrderDetailsDataSql = "select a.job_no,b.mst_id,b.determination_id,b.width_dia_type,c.job_no,sum(d.grey_qty) as grey_qty,e.yarn_count_id from fabric_sales_order_dtls b,wo_booking_dtls c,fabric_sales_order_mst a , fabric_sales_order_yarn d,fabric_sales_order_yarn_dtls e where a.id=b.mst_id and  d.mst_id=a.id and  e.mst_id=a.id and  e.yarn_dtls_id=d.id  and  e.deter_id=d.deter_id and c.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.sales_booking_no=c.booking_no  and b.is_deleted=0 and b.status_active=1 and a.within_group in(1) and a.id in($req_id)  group by a.job_no,b.mst_id,b.determination_id,b.width_dia_type,c.job_no,e.yarn_count_id";
				$salesOrderDetailsDataResult = sql_select($salesOrderDetailsDataSql);
				foreach($salesOrderDetailsDataResult as $row)
				{
					$sales_no_arr[$req_id]['sales_no']=$row[csf('job_no')];
				}

				 $prog_sales_requisition ="select a.dtls_id, a.determination_id,a.booking_no,b.knit_id,b.requisition_no,a.program_qnty,a.po_id,b.id,b.prod_id,c.product_name_details,c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_type from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_id in($req_id)";

					$prog_sales_result = sql_select($prog_sales_requisition);
					$total_program_qnty=0;
					foreach ($prog_sales_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="100" class="center"><? echo $row[csf('dtls_id')]; ?></td>
							<td width="120" class="center"><? echo $sales_no_arr[$req_id]['sales_no']; ?></td>
							<td width="110" class="center"><? echo $row[csf('booking_no')]; ?></td>
							<td width="200" class="center"><? echo $row[csf('product_name_details')]; ?></td>

							<td align="right" width="100">
								<?
								echo $row[csf('program_qnty')];
								?>
							</td>

						</tr>
						<?
						$i++;
						$total_program_qnty+= $row[csf('program_qnty')];
					}
					}
					?>
					<tfoot>
					<tr style="font-weight:bold">
						<th></th>
						<th></th>

						<th></th>
						<th align="right">Total</th>
						<th align="right"><? echo number_format($total_program_qnty, 2); ?></th>
					</tr>

					</tfoot>

				</table>
			</div>
		</fieldset>
		<?
		exit();
	}
if ($action == "show_fab_store_popup_dtls") //
 {
		echo load_html_head_contents(" Batch Details info", "../../../../", 1, 1,$unicode,'','');
 		extract($_REQUEST);
		$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

		?>
		<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}
		</script>
		<div style="width:870px" align="center"><input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
			<fieldset style="width:865px; margin-left:3px">
				<div id="report_div">

				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="11"><b>Fabric Store Details</b></th>
					</thead>
					<thead>
						<th width="100">Recv Id</th>
						<th width="120">Recv Date </th>
						<th width="110">Booking/Challan No</th>
						<th width="200">Fabric Desc.</th>
						<th width="100">Recv. Qnty</th>

					</thead>
					<?
					$i = 1;
					if($req_id!="")
					{
					$fabstore_data="select a.id,a.body_part_id,c.trans_type,c.po_breakdown_id as po_id, c.color_id, c.quantity,d.recv_number,d.receive_date,d.booking_no,e.product_name_details from pro_finish_fabric_rcv_dtls a, order_wise_pro_details c ,inv_receive_master d,product_details_master e
where a.id=c.dtls_id and c.entry_form=37  and a.prod_id=c.prod_id and e.id=a.prod_id and e.id=c.prod_id and d.id=a.mst_id and d.entry_form=37  and c.trans_type=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id in($req_id)";

				// $prog_sales_requisition ="select a.dtls_id, a.determination_id,a.booking_no,b.knit_id,b.requisition_no,a.program_qnty,a.po_id,b.id,b.prod_id,c.product_name_details,c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_type from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_id in($req_id)";

					$fabstore_data_result = sql_select($fabstore_data);
					$total_qnty=0;
					foreach ($fabstore_data_result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="100" class="center"><? echo $row[csf('recv_number')]; ?></td>
							<td width="120" class="center"><? echo  $row[csf('receive_date')]; ?></td>
							<td width="110" class="center"><? echo $row[csf('booking_no')]; ?></td>
							<td width="200" class="center"><? echo $row[csf('product_name_details')]; ?></td>

							<td align="right" width="100">
								<?
								echo $row[csf('quantity')];
								?>
							</td>

						</tr>
						<?
						$i++;
						$total_qnty+= $row[csf('quantity')];
					}
					}
					?>
					<tfoot>
					<tr style="font-weight:bold">
						<th></th>
						<th></th>

						<th></th>
						<th align="right">Total</th>
						<th align="right"><? echo number_format($total_qnty,2); ?></th>
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
				if($id!="")
				{
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


?>
