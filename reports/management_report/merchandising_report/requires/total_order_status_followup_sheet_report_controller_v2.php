<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
//require_once('../../../../includes/class4/class.conversions.php');
//require_once('../../../../includes/class4/class.emblishments.php');
//require_once('../../../../includes/class4/class.commisions.php');
//require_once('../../../../includes/class4/class.commercials.php');
//require_once('../../../../includes/class4/class.others.php');
//require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
//require_once('../../../../includes/class4/class.washes.php');

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
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/total_order_status_followup_sheet_report_controller_v2',this.value, 'load_drop_down_season', 'season_td' );" ,0);
	exit();
}
if ($action=="load_drop_down_season")
{
//echo "select id,season from lib_buyer_season where status_active =1 and is_deleted=0 and buyer_id='$data' ";
	echo create_drop_down( "cbo_season", 130, "select id,season_name from lib_buyer_season where status_active =1 and is_deleted=0 and buyer_id='$data' ","id,season_name", 1, "-- All Season --", $selected, "load_drop_down( 'requires/total_order_status_followup_sheet_report_controller_v2',this.value, 'load_drop_down_season', 'season_td' );" ,0);
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_search_list_view', 'search_div', 'total_order_status_followup_sheet_report_controller_v2', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'order_search_list_view', 'search_div', 'total_order_status_followup_sheet_report_controller_v2', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
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

	 $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year,a.gmts_item_id,a.style_ref_no,a.season_buyer_wise, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty,b.unit_price, b.plan_cut,b.po_quantity, b.pub_shipment_date,b.shipment_date,b.file_no,b.grouping,b.po_received_date, c.color_number_id,c.order_quantity as order_quantity,c.plan_cut_qnty,c.color_number_id as color,c.item_number_id as item_id,c.order_rate,c.order_total,d.body_part_id as body_id,d.color_break_down,d.id as fab_dtls_id,d.lib_yarn_count_deter_id as deter_min_id,d.construction,d.composition, d.gsm_weight,d.width_dia_type
	from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c , wo_pre_cost_fabric_cost_dtls d
	where a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id  and c.item_number_id=d.item_number_id  and a.id=d.job_id and b.job_id=d.job_id  and c.job_id=d.job_id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1  and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond order by b.id ";


	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();$m=0;
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		 $fabric_desc=$row[csf("composition")].','.$row[csf("construction")].','.$row[csf("gsm_weight")];
		 $job_no=$row[csf("job_no")];
		//$yarnCount=$precost_yarnCount_arr[$row[csf("deter_min_id")]]['count'];

		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["po_number"]=$row[csf("po_number")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["order_quantity"]+=$row[csf("order_quantity")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["plan_cut_qnty"]+=$row[csf("plan_cut_qnty")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["order_rate"]=$row[csf("order_rate")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["order_total"]+=$row[csf("order_total")];
		
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["job_no_prefix"]=$row[csf("job_no_prefix_num")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["job_no"]=$row[csf("job_no")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["buyer_name"]=$row[csf("buyer_name")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["file_no"]=$row[csf("file_no")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["ref_no"]=$row[csf("grouping")];
	//	$po_color_data_arr[$job_no][$row[csf("item_id")]][$row[csf("po_id")]][$row[csf("color")]]["deter_min_id"].=$row[csf("deter_min_id")].',';
		
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["style"]=$row[csf("style_ref_no")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["body_part_id"].=$body_part[$row[csf("body_id")]].',';
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["deter_min_id"].=$row[csf("deter_min_id")].',';
////$body_part[$body_id]
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["po_received_date"]=$row[csf("po_received_date")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["shipment_date"]=$row[csf("shipment_date")];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["item_id"].=$garments_item[$row[csf("item_number_id")]].',';
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["season"]=$season_arr[$row[csf("season_buyer_wise")]];
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["desc"].=$fabric_desc.',';;
		$po_color_data_arr[$job_no][$row[csf("po_id")]][$row[csf("color")]]["width"]= $row[csf("width_dia_type")];
		//$po_color_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("fab_dtls_id")]][$row[csf("color")]]["yarn_count"] .= $yarnCount.',';

		$po_id=$row[csf("po_id")];
		//$booking_color=$order_id.$booking_no.$color_id;
		if (!in_array($po_id,$result_job_wise))
		{ $m++;
			 $result_job_wise[]=$po_id;
			  $po_quantity=$row[csf("order_quantity")];
			   $plan_cut=$row[csf("plan_cut_qnty")];
			    $po_value=$row[csf("order_total")];
		}
		else
		{
			 $po_quantity=0; $plan_cut=0; $po_value=0;
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
		
		$po_item_wise_data_arr[$row[csf("po_id")]][$row[csf("item_id")]]["po_qty"]+=$row[csf("order_quantity")];
		$po_item_wise_data_arr2[$row[csf("po_id")]]["po_qty"]+=$row[csf("order_quantity")];
		
		$po_item_wise_data_arr2[$row[csf("po_id")]]["po_qty"]+=$row[csf("order_quantity")];
		
		$po_item_color_wise_data_arr[$row[csf("po_id")]][$row[csf("color")]]["po_qty"]+=$row[csf("order_quantity")];//$po_quantity;
		$po_item_color_wise_data_arr[$row[csf("po_id")]][$row[csf("color")]]["plan_cut"]+=$row[csf("plan_cut_qnty")];
		$po_item_color_wise_data_arr[$row[csf("po_id")]][$row[csf("color")]]["po_value"]+=$po_value;

		//$JobArr[]="'".$row[csf('job_no')]."'";
	}
//	print_r($po_item_wise_data_arr2);
	ksort($po_color_data_arr);
	 
	 $sql_pre_contrast="select b.job_id,b.id as po_id,f.gmts_color_id as color_id,d.body_part_id as body_id,d.id as fab_dtls_id,d.lib_yarn_count_deter_id as deter_min_id,d.item_number_id as item_id,f.contrast_color_id as contrast_color from wo_po_details_master a ,wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_color_dtls f where b.job_id=d.job_id and b.job_id=f.job_id and a.id=b.job_id and a.id=d.job_id and a.id=f.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=f.pre_cost_fabric_cost_dtls_id  and c.item_number_id=d.item_number_id and c.color_number_id=f.gmts_color_id and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond";
	$sql_contrast_result=sql_select($sql_pre_contrast);
	foreach($sql_contrast_result as $row)
	{
		$pre_color_contrast_arr[$row[csf("po_id")]][$row[csf("color_id")]]=$row[csf("contrast_color")];
	}
	 
	 
	//echo $all_po_id.'d';die;
	/*$condition= new condition();
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
*/
	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));//$yarn_allo_po_cond.=" a.po_break_down_id in($ids) or ";
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";$yarn_allo_po_cond="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";
			$po_cond_for_in4=" and (";
			$yarn_allo_po_cond=" and (";
			$batch_po_cond=" and (";
			$cut_po_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" c.po_break_down_id in($ids) or";
				$po_cond_for_in2.=" c.po_breakdown_id in($ids) or";
				$po_cond_for_in3.=" c.order_id in($ids) or";
				$po_cond_for_in4.=" c.po_id in($ids) or";
				$yarn_allo_po_cond.=" a.po_break_down_id in($ids) or";
				$batch_po_cond.=" b.po_id in($ids) or";
				$cut_po_cond.=" c.order_id in($ids) or";
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
			$po_cond_for_in4=chop($po_cond_for_in4,'or ');
			$po_cond_for_in4.=")";
			$yarn_allo_po_cond=chop($yarn_allo_po_cond,'or ');
			$yarn_allo_po_cond.=")";
			$batch_po_cond=chop($batch_po_cond,'or ');
			$batch_po_cond.=")";
			$cut_po_cond=chop($cut_po_cond,'or ');
			$cut_po_cond.=")";
		}
		else
		{
			$po_cond_for_in=" and c.po_break_down_id in($poIds)";
			$po_cond_for_in2=" and c.po_breakdown_id in($poIds)";//po_breakdown_id
			$po_cond_for_in3=" and c.order_id in($poIds)";
			$po_cond_for_in4=" and c.po_id in($poIds)";
			$yarn_allo_po_cond=" and a.po_break_down_id in($poIds)";
			$batch_po_cond=" and b.po_id in($poIds)";
			$cut_po_cond=" and c.order_id in($poIds)";
		}//
		 $sql_colorSiZe=sql_select("select c.job_no_mst,c.po_break_down_id as po_id ,c.color_number_id as color_id,c.order_quantity as order_quantity,c.plan_cut_qnty,c.item_number_id as item_id,c.order_rate,c.order_total  from wo_po_break_down b,wo_po_color_size_breakdown c where   b.id=c.po_break_down_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in ");
		 
		foreach($sql_colorSiZe as $row)
		{
			$po_color_Arr[$row[csf('po_id')]][$row[csf('color_id')]]['size_qty']+=$row[csf('order_quantity')];
			$po_color_Arr[$row[csf('po_id')]][$row[csf('color_id')]]['plan_size_qty']+=$row[csf('plan_cut_qnty')];
			$po_color_Arr[$row[csf('po_id')]][$row[csf('color_id')]]['order_total']+=$row[csf('order_total')];
			
			$po_color_qty_Arr[$row[csf('po_id')]]['po_qty']+=$row[csf('order_quantity')];
			$job_po_color_qty_Arr[$row[csf('job_no_mst')]]['po_qty']+=$row[csf('order_quantity')];
			
		}
		
  $sql_yarn="select d.job_no,c.po_break_down_id as po_id,c.order_quantity as order_quantity,c.plan_cut_qnty,c.color_number_id as color,c.item_number_id as item_id,d.body_part_id as body_id,d.id as fab_dtls_id,d.lib_yarn_count_deter_id as deter_min_id,d.construction,d.composition, d.gsm_weight,d.width_dia_type,f.copm_one_id,f.count_id,f.percent_one,f.type_id,f.copm_two_id,f.percent_two,f.fabric_cost_dtls_id
	from wo_po_color_size_breakdown c ,wo_pre_cost_fabric_cost_dtls d, wo_pre_cost_fab_yarn_cost_dtls f
	where  d.job_id=c.job_id and d.id=f.fabric_cost_dtls_id and c.item_number_id=d.item_number_id and c.status_active=1  and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $po_cond_for_in order by d.job_no";
 
	$sql_yarn_result=sql_select($sql_yarn);
	 
	foreach($sql_yarn_result as $row)
	{
			$yarnCount=$precost_yarnCount_arr[$row[csf("count_id")]]['count'];
	$yarn_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color")]]["count"].=$yarnCount.',';
	$yarn_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color")]]["countId"].=$row[csf("count_id")].',';
	$yarn_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color")]]["composition"].=$composition[$row[csf("copm_one_id")]].',';
	$yarn_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color")]]["type_id"].=$yarn_type[$row[csf("type_id")]].',';
	//$yarn_data_arr[$row[csf("job_no")]][$row[csf("item_id")]][$row[csf("po_id")]][$row[csf("body_id")]][$row[csf("color")]]["composition"].=$composition[$row[csf("copm_one_id")]].',';
	}
	unset($sql_yarn_result);
		
		$booking_data_array=array();
		$booking_data=sql_select("select c.pre_cost_fabric_cost_dtls_id as fab_dtls_id,c.po_break_down_id as po_id,c.gmts_color_id,d.body_part_id,d.item_number_id as item_id,
		(c.fin_fab_qnty) as fin_fab_qnty,
		(c.grey_fab_qnty) as grey_fab_qnty
		 from wo_booking_mst b,wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls d where b.booking_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id=d.id and c.job_no=d.job_no and   c.booking_type in(1,4)  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in order by c.po_break_down_id");
		 
		 
		foreach($booking_data as $row)
		{
			$booking_data_array[$row[csf('po_id')]][$row[csf('gmts_color_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			$booking_data_array[$row[csf('po_id')]][$row[csf('gmts_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			
			$booking_data_color_array[$row[csf('po_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			$booking_data_color_array[$row[csf('po_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			
			$booking_data_po_color_array[$row[csf('po_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			$booking_data_po_color_array[$row[csf('po_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			
			$booking_poColor_data_array[$row[csf('po_id')]][$row[csf('gmts_color_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			$booking_poColor_data_array[$row[csf('po_id')]][$row[csf('gmts_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			
		}
		unset($booking_data);
		// ".where_con_using_array($job_id_arr,1,'a.id')." 
		
		// echo "select d.id,a.job_no,d.size_qty as qc_pass,c.color_id,c.gmt_item_id from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c where a.job_no=d.job_no   and d.id=c.mst_id and d.entry_form=289 and c.status_active=1 and c.is_deleted=0     $buyer_id_cond $year_cond $job_no_cond $po_cond_for_in3  order by d.id asc";
		 
	 $sql_dtls_cut=sql_select("select b.id,c.order_id,c.size_qty,a.color_id,a.gmt_item_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c where b.id=a.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.mst_id and a.id=c.dtls_id $cut_po_cond ");
		foreach($sql_dtls_cut as $row)
		{
			$cut_lay_first_arr[$row[csf('order_id')]][$row[csf('color_id')]]['size_qty']+=$row[csf('size_qty')];
			
		}
			unset($sql_dtls_cut);
			$sql_yarn_iss = "select c.po_breakdown_id as po_id,a.detarmination_id as deter_id,(c.quantity) cons_quantity,a.yarn_count_id,a.yarn_type from order_wise_pro_details c ,inv_transaction d,product_details_master a   where d.id=c.trans_id and a.id=c.prod_id and a.id=d.prod_id and c.trans_type=2 and c.entry_form=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $po_cond_for_in2 ";
			$dataArrayIssue = sql_select($sql_yarn_iss);
			foreach ($dataArrayIssue as $row) { //[$row[csf('yarn_count_id')]]
				$yarn_issue_details_arr[$row[csf('po_id')]]['issue_qnty']+= $row[csf('cons_quantity')];
			}
		unset($dataArrayIssue);
		 if($poIds != ''){
		 	/*$batch_sql = "select a.id as batch_id,a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no,sum(b.batch_qnty) qnty from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id  and (a.extention_no is null or a.extention_no=0) and a.status_active=1 and a.is_deleted=0 $batch_po_cond group by a.id, a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no";
			$batch_result = sql_select($batch_sql);
			$batch_arr=array();
			foreach ($batch_result as $row) {
				$job_no=$job_no_from_sales_arr[$row[csf('sales_order_id')]]['job_no'];
				$batch_qty_arr[$job_no]['batch_qty']+= $row[csf("qnty")];
				$batch_qty_arr[$job_no]['batch_id'] .= $row[csf("batch_id")].',';
			}*/
		// DYEING PRODUCTION
		 $sql_dye = "select a.color_id,b.po_id, a.id as batch_id,b.prod_id,d.detarmination_id,b.body_part_id, (b.batch_qnty) as dye_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c,product_details_master d where a.id=b.mst_id and a.id=c.batch_id and b.prod_id=d.id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $batch_po_cond ";
		$resultDye = sql_select($sql_dye);
		foreach ($resultDye as $row) {
		$job_no=$job_no_from_sales_arr[$row[csf('po_id')]]['job_no'];
			$dyeing_qnty_arr[$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color_id')]]['dyeing_qty']+= $row[csf('dye_qnty')];
			//$dyeing_qnty_arr[$job_no]['batch_id'].= $row[csf('batch_id')].',';
		}
		unset($resultDye);
		 }

		  if($poIds != ''){
		  	$fab_store_data_array=array();
			$fabstore_data=sql_select("select a.id,a.color_id as fin_color,a.body_part_id,a.fabric_description_id as determin_id,a.body_part_id as body_id,c.trans_type,c.po_breakdown_id as po_id, c.color_id, c.quantity from pro_finish_fabric_rcv_dtls a, order_wise_pro_details c
	where a.id=c.dtls_id and c.entry_form in(68,37)  and a.prod_id=c.prod_id and c.trans_type=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $po_cond_for_in2"); //need Roll

			foreach($fabstore_data as $row)
			{
					//$job_no=$job_no_from_sales_arr[$row[csf('po_id')]]['job_no'];
					$fab_store_data_array[$row[csf('po_id')]][$row[csf('determin_id')]]['store_quantity']+=$row[csf('quantity')];
					//$fab_store_data_array[$job_no]['dtls_id'].=$row[csf('id')].',';
			}
			unset($fabstore_data);
		  }

		 
			$knit_data_array=array();
		 	$knit_data=sql_select("select b.body_part_id as body_id, b.febric_description_id as deter_id,b.id as dtls_id, c.po_breakdown_id as po_id, c.quantity,c.trans_id,c.color_id from pro_grey_prod_entry_dtls b, order_wise_pro_details c  where  b.id=c.dtls_id  and c.entry_form=2 and c.trans_type=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $po_cond_for_in2");
			
			foreach($knit_data as $row)
			{
			$knit_data_array[$row[csf('po_id')]][$row[csf('deter_id')]]['knitQty']+=$row[csf('quantity')];;
				//$knit_data_array[$job_no]['quantity']+=$row[csf('quantity')];
				//$knit_data_array[$job_no]['knit_dtls_id']=$row[csf('dtls_id')];
			}
			unset($knit_data);
		 


		
		$exfactory_data_array=array();
		$exfactory_data=sql_select("select b.color_number_id as color_id,c.po_break_down_id as po_id,c.item_number_id as item_id,c.shiping_status,
		sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as return_qnty,
		MAX(c.ex_factory_date) as ex_factory_date from pro_ex_factory_mst c ,pro_ex_factory_dtls d,wo_po_color_size_breakdown b where c.id=d.mst_id and b.id=d.color_size_break_down_id and c.po_break_down_id=b.po_break_down_id and c.status_active=1 and c.is_deleted=0 $po_cond_for_in group by b.color_number_id,c.po_break_down_id,c.item_number_id ,c.shiping_status");
		 
		foreach($exfactory_data as $row)
		{
				$exfactory_data_array[$row[csf('po_id')]][$row[csf('color_id')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
				$exfactory_data_array[$row[csf('po_id')]][$row[csf('color_id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
				$exfactory_data_array[$row[csf('po_id')]][$row[csf('color_id')]]['shiping_status']=$row[csf('shiping_status')];
		}
		unset($exfactory_data);


		
		$samp_data_array=array();
		$samp_data=sql_select("select c.po_break_down_id,
		c.approval_status,c.approval_status_date,d.color_number_id as color_id,c.sample_type_id,c.submitted_to_buyer from wo_po_sample_approval_info c,wo_po_color_size_breakdown d  where  d.id=c.color_number_id and d.job_no_mst=c.job_no_mst  and c.status_active=1 and c.is_deleted=0  and c.sample_type_id in(7,2,5,29) and c.approval_status>0 $po_cond_for_in group by c.po_break_down_id,c.approval_status,c.approval_status_date,d.color_number_id,c.submitted_to_buyer,c.sample_type_id");
		
	 
		foreach($samp_data as $row)
		{
			if( $row[csf('sample_type_id')]==7)//Photo sample //Submit
			{
					$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['samp_submit_date']=$row[csf('submitted_to_buyer')];
			/*}
			else if($row[csf('approval_status')]==3 && $row[csf('sample_type_id')]==7)
			{*/
				$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['samp_approv_date']=$row[csf('approval_status_date')];
			}
			else if( $row[csf('sample_type_id')]==2)// //Approved //Size Set
			{
					$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['size_submit_date']=$row[csf('submitted_to_buyer')];
			/*}
			else if($row[csf('approval_status')]==3 && $row[csf('sample_type_id')]==2 )//Approved //Size Set
			{*/
				$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['size_approv_date']=$row[csf('approval_status_date')];
			}
			else if( $row[csf('sample_type_id')]==5)// //Approved //Test 
			{
					$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['test_submit_date']=$row[csf('submitted_to_buyer')];
			/*}
			else if($row[csf('approval_status')]==3 && $row[csf('sample_type_id')]==5)//Approved //Test
			{*/
				$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['test_approv_date']=$row[csf('approval_status_date')];
			}
			else if( $row[csf('sample_type_id')]==29)// //Approved //Quality 
			{
					$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['quality_submit_date']=$row[csf('submitted_to_buyer')];
			/*}
			else if($row[csf('approval_status')]==3 && $row[csf('sample_type_id')]==29)//Approved //Quality
			{*/
				$samp_data_array[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['quality_approv_date']=$row[csf('approval_status_date')];
			}
			
			 
		}
		//print_r($samp_data_array);
		unset($samp_data);//wo_po_sample_approval_info
		
	  $SqlgmtsProdData="select  c.po_break_down_id as po_id,c.item_number_id as item_id,b.color_number_id as color,
					sum(CASE WHEN c.production_type=1 THEN d.production_qnty ELSE 0 END) AS cutting_qnty,
					sum(CASE WHEN c.production_type=2 and c.embel_name=1 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS print_issue_qnty_in,
					sum(CASE WHEN c.production_type=2 and c.embel_name=1 and c.production_source=3 THEN d.production_qnty ELSE 0 END) AS print_issue_qnty_out,
					sum(CASE WHEN c.production_type=3 and c.embel_name=1 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS print_recv_qnty_in,
					sum(CASE WHEN c.production_type=3 and c.embel_name=1 and c.production_source=3 THEN d.production_qnty ELSE 0 END) AS print_recv_qnty_out,
					sum(CASE WHEN c.production_type=2 and c.embel_name=2 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS emb_issue_qnty_in,
					sum(CASE WHEN c.production_type=2 and c.embel_name=2 and c.production_source=3 THEN d.production_qnty ELSE 0 END) AS emb_issue_qnty_out,
					sum(CASE WHEN c.production_type=3 and c.embel_name=2 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS emb_recv_qnty_in,
					sum(CASE WHEN c.production_type=3 and c.embel_name=2 and c.production_source=3 THEN d.production_qnty ELSE 0 END) AS emb_recv_qnty_out,
					sum(CASE WHEN c.production_type=4 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS sew_input_qnty_in,
					sum(CASE WHEN c.production_type=4 and c.production_source=3 THEN d.production_qnty ELSE 0 END) AS sew_input_qnty_out,
					sum(CASE WHEN c.production_type=5 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS sew_recv_qnty_in,
					sum(CASE WHEN c.production_type=5 and c.production_source=3 THEN d.production_qnty ELSE 0 END) AS sew_recv_qnty_out,
					sum(CASE WHEN c.production_type=8 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS finish_qnty_in,
					sum(CASE WHEN c.production_type=8 and c.production_source=3 THEN d.production_qnty ELSE 0 END) AS finish_qnty_out,
					sum(CASE WHEN c.production_type=3 and c.embel_name=3 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS wash_recv_qnty_in,
					sum(CASE WHEN c.production_type=3 and c.embel_name=3 and c.production_source=3 THEN d.production_qnty ELSE 0 END) AS wash_recv_qnty_out,

					sum(CASE WHEN c.production_type=2  THEN d.production_qnty ELSE 0 END) AS emblish_issue_qnty_in,
					sum(CASE WHEN c.production_type=3  THEN d.production_qnty ELSE 0 END) AS embblish_recv_qnty_in,

					sum(CASE WHEN c.production_type=3 and c.embel_name=1 THEN d.reject_qty ELSE 0 END) AS print_reject_qnty,
					sum(CASE WHEN c.production_type=3 and c.embel_name=2 THEN d.reject_qty ELSE 0 END) AS emb_reject_qnty,
					sum(CASE WHEN c.production_type=5 THEN d.reject_qty ELSE 0 END) AS sew_reject_qnty,
					sum(CASE WHEN c.production_type=8 THEN d.reject_qty ELSE 0 END) AS finish_reject_qnty,
					sum(CASE WHEN c.production_type=1 THEN d.reject_qty ELSE 0 END) AS cutting_reject_qnty,
					sum(CASE WHEN c.production_type=7 THEN d.reject_qty ELSE 0 END) AS iron_rej_qnty
					from pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown b where c.id=d.mst_id and b.id=d.color_size_break_down_id and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in group by c.po_break_down_id,c.item_number_id,b.color_number_id ";
//color_size_break_down_id


	$garment_prod_data_arr=array();
	//echo $SqlgmtsProdData;
	$gmtsProdDataArr=sql_select($SqlgmtsProdData);
	foreach($gmtsProdDataArr as $row)
	{ 
		//echo $row[csf("emblish_issue_qnty_in")].'='.$row[csf("embblish_recv_qnty_in")].', ';
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['cutting_qnty']=$row[csf("cutting_qnty")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
		//$garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['finish_qnty_out']=$row[csf("finish_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['wash_recv_qnty_in']=$row[csf("wash_recv_qnty_in")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['wash_recv_qnty_out']=$row[csf("wash_recv_qnty_out")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['print_reject_qnty']=$row[csf("print_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['emb_reject_qnty']=$row[csf("emb_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['sew_reject_qnty']=$row[csf("sew_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['finish_reject_qnty']=$row[csf("finish_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['cutting_reject_qnty']=$row[csf("cutting_reject_qnty")];
		$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['iron_rej_qnty']=$row[csf("iron_rej_qnty")];
	}
	unset($gmtsProdDataArr);
	   $sql_yarn_allocation="select a.po_break_down_id as po_id,a.booking_no, b.yarn_count_id,b.detarmination_id as deter_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
				sum(a.qnty) AS allocation_qty
				from inv_material_allocation_dtls a, product_details_master b where a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $yarn_allo_po_cond group by a.po_break_down_id,a.booking_no, b.detarmination_id ,b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
		$dataArrayAllocation=sql_select($sql_yarn_allocation);
		foreach($dataArrayAllocation as $row)
		{
			$yarnAllocationArr[$row[csf('po_id')]].=$row[csf('yarn_count_id')]."**".$row[csf('yarn_comp_type1st')]."**".$row[csf('yarn_comp_percent1st')]."**".$row[csf('yarn_comp_type2nd')]."**".$row[csf('yarn_comp_percent2nd')]."**".$allocationRow[csf('yarn_type')]."**".$row[csf('allocation_qty')].",";
			$yarnAllocationQtyArr[$row[csf('po_id')]][$row[csf('yarn_count_id')]]+=$row[csf('allocation_qty')];
			$po_yarnAllocationQtyArr[$row[csf('po_id')]]+=$row[csf('allocation_qty')];
		}
		unset($dataArrayAllocation);
		
	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	$tbl_width=4730;
	 
	
	
			 $condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$cbo_season)>0){
				  $condition->season("=$cbo_season");
			 }
			
			 //job_year
			 if(trim(str_replace("'","",$txt_order))!="")
			{
				if(str_replace("'","",$txt_order_id)!="")
				{
					//echo $txt_order_id.'AAAAAA';
					//$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
					$condition->po_id("in($txt_order)"); 
				}
				else
				{
					//$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
					$condition->po_number("=$txt_order"); 
				}
			}
			if(trim(str_replace("'","",$txt_style_ref))!="")
			{
				if(str_replace("'","",$txt_style_ref_id)!="")
				{
					//echo $txt_order_id.'AAAAAA';
					//$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
					$condition->jobid_in("in($txt_style_ref_id)"); 
				}
				else
				{
					//$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
					$condition->job_no_prefix_num("=$txt_style_ref"); 
				}
			}
	
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
				//$ship_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
				 $condition->country_ship_date(" between '$start_date' and '$end_date'");
	
			}
		
			  $condition->init();
			  $yarn= new yarn($condition);
			//  echo  $yarn->getQuery();die;
			 // $fabric_costing_arr=$fabric->getQtyArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish();
			  $yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			  // $yarn_qty_arr=$yarn->getOrderAndGmtsItemWiseYarnQtyArray();
		// print_r($yarn_qty_arr);die;
			 
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
                        <th width="80" rowspan="2">Garments Item </th>
                        <th width="100" rowspan="2">Ord / PO</th>

                        <th width="80" rowspan="2">Qty In Pcs</th>
						<th width="80" rowspan="2">Ord Rcv Date</th>
                        <th width="80" rowspan="2">Pub. Ship Date</th>
                        <th width="80" rowspan="2">Original <br>Ship Date</th>
                         
                        
                        <th width="100" rowspan="2">Body Part</th>
                        <th width="150" rowspan="2">Fabrication</th>
                       
						
						
						<th width="" colspan="7">Yarn Status</th>
						<th width="" colspan="3">Knitting Status</th>
						<th width="" colspan="8">Dyeing Status</th>
                        <th width="" colspan="8">Sample Status</th>
						<th width="" colspan="3">Cutting Status</th>
                        
						<th width="" colspan="2">Printing Status</th>
						<th width="" colspan="2">Emb. Prod Status</th>
						<th width="" colspan="3">Sewing & Finish Status</th>
						<th width="" colspan="6">Shipment Status</th>
						<th width="" rowspan="2">Ex. Factory Date</th>
					</tr>
					 <tr>
                       

						<th width="80" title="Yarn Status">Count</th>
                        <th width="80">Composition</th>
						<th width="80">Type</th>
						<th width="80" title="Knitting Status">Required<br>(As Per Budget)</th>
                        <th width="80">Allocated</th>
						<th width="80">Yet to <br>Allocate</th>
						<th width="80" title="Y Issue">Issued</th>
                        
                        <th width="80">Grey Req.<br> Qty(Kg)</th>
						<th width="80">Knitting <br>Production</th>
						<th width="80" title="Knitting end">Yet to <br>Production</th>
                        
                        <th width="80" title="Dying start">Fabric Color</th>
						<th width="80">Req. As per<br>Booking</th>
						<th width="80" title="30"> Dyeing Qty</th>
						<th width="80">Dyeing<br> Balance</th>
						<th width="80" title="">Req. As per<br> Booking</th>
						<th width="80">Store Receive Qty</th>
						<th width="80" title=""> Balance qty</th>
						
						<th width="80" title="">Garments Color</th>
                        

						<th width="80" title="Sample"> Photo Submit</th>
						<th width="80">Photo Approve</th>
						<th width="80">Size Set<br> Submit</th>
						<th width="80" title="40">Size Set Approve</th>
                        <th width="80">Quality Submit</th>
						<th width="80">Quality Approve</th>
						<th width="80" title="">Test Submit</th>
                        <th width="80" title="">Test Approval</th>
                        
                        <th width="80" title="Cutting">OrderQty(Pcs)</th>
                        <th width="80" title="">Required <br>Qty(Pcs)</th>
                        <th width="80" title="">Complete</th>
                        
                         <th width="80" title="Print">Send</th>
                        <th width="80" title="">Recv</th>
                        
                         <th width="80" title="50Emrob ">Send</th>
                        <th width="80" title="">Recv</th>
                        
                         <th width="80" title="Sew Fin ">Input</th>
                        <th width="80" title="">Output</th>
                        <th width="80" title="">Finish</th>
                        

						<th width="80">Shipped Qty</th>
                        <th width="80">Unit Price</th>
						<th width="80">Total Value</th>
						<th width="80">Excess Value</th>
                        
                         
                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

                <?
					$job_rowspan_arr=array();	$po_rowspan_arr=array();$po_item_rowspan_arr=array();$po_item_color_rowspan_arr=array();//$fab_rowspan_arr=array();

				   foreach($po_color_data_arr as $job_id=>$job_data)
					{
						$job_rowspan=0;
						foreach($job_data as $po_id=>$po_data)
						{
						$po_rowspan=0;
						//foreach($po_data as $item_id=>$item_data)
					//	{

							$item_rowspan=0;
							foreach($po_data as $color_id=>$val)
							{
								$job_rowspan++;
								$item_rowspan++;
								$po_rowspan++;
								$color_rowspan++;
								//$fab_rowspan++;
							}
							$job_rowspan_arr[$job_id]=$job_rowspan;
							$item_rowspan_arr[$job_id][$item_id]=$item_rowspan;
							//$po_rowspan_arr[$job_id][$item_id][$po_id]=$po_rowspan;
							$po_rowspan_arr[$job_id][$item_id][$po_id]=$po_rowspan;
							$po_rowspan_arr2[$job_id][$po_id]=$po_rowspan;

							 
							//}
						}
					 }
				//	print_r($po_rowspan_arr); 

						$i=$k=1;$grand_fabric_fin_qty_kg=$grand_allocate_qty=$tot_order_qty=$grand_yet_to_allocated_qty=$grand_yarn_issue_qty=$grand_grey_fab_req_qnty=$tot_print_issue_qnty_in=$tot_print_recv_qnty_in=$tot_emb_issue_qnty_in=$tot_emb_recv_qnty_in=$tot_sew_input_qnty_in=$tot_sew_recv_qnty_output=$tot_finish_qnty_in=$grand_knit_prod_qty=$grand_tot_shipped_qty=$grand_po_value=$grand_tot_excess_value=$grand_knit_yet_to_production=$grand_grey_fab_req_qnty=$grand_dyeing_balance_qty=$grand_dyeing_qnty=$grand_dyeing_booking_fin_req_qty=$grand_fab_store_recv_qty=$grand_fab_store_recv_bal=$grand_plan_cut_qnty=$grand_cut_complete_qty=$sub_tot_grey_fab_req_qnty_dyeing=$grand_order_quantity_pcs=$grand_plan_cut_qnty=0;$grand_dyeing_booking_fin_req_qty_yarn=$grand_grey_fab_req_qnty_kniting=$grand_grey_fab_req_qnty_dyeing=0;$grand_grey_fab_budget_req_qty=0;
						foreach($po_color_data_arr as $job_id=>$job_data)
						{
							$x=1; 
							 $sub_tot_order_qty=$sub_booking_fin_req_qty=$sub_booking_fin_req_qty_dying=$sub_tot_yet_to_allocated_qty=$sub_tot_allocate_qty=$sub_tot_yarn_issue_qty=$sub_tot_grey_fab_req_qnty=$sub_tot_knit_prod_qty=$sub_tot_dyeing_qty=$sub_tot_yet_to_production=$sub_tot_dyeing_balance_qty=$sub_booking_fin_req_qty=$sub_fab_store_recv_qty=$sub_fab_store_recv_bal=$sub_tot_po_value=$sub_tot_excess_value=$sub_tot_plancut_qty=$sub_po_qty_pcs=$sub_cut_complete_qty=0;$sub_booking_fin_req_qty_yarn=0;$sub_grey_fab_budget_req_qty_yarn=0;
						foreach($job_data as $po_id=>$po_data)
						 {
						
						//$po_item_wise_data_arr2[$row[csf("po_id")]]["po_qty"]
							$booking_fin_req_qty=$booking_data_po_color_array[$po_id]['fin_fab_qnty'];//$booking_data_array[$item_id][$po_id][$color_id]['fin_fab_qnty'];
							$grey_fab_req_qnty=$booking_data_po_color_array[$po_id]['grey_fab_qnty'];
							
						$p=1; 
						// foreach($po_data as $item_id=>$item_data)
						 //{
							

								$it=1;
								foreach($po_data as $color_id=>$val)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							//echo $fabric_costing_arr['knit']['grey'][$po_id][$item_id].'d';
					//	$grey_knit=array_sum($fabric_costing_arr['knit']['grey'][$po_id][$item_id]);
						//$grey_woven=array_sum($fabric_costing_arr['woven']['grey'][$po_id][$item_id]);
						$yarn_qty=$yarn_qty_arr[$po_id];
						$grey_fab_budget_req_qty=$yarn_qty;
							
							$gmts_item_id=rtrim($val["gmts_item_id"],',');
							$gmts_item_Arr=array_unique(explode(",",$gmts_item_id));
							//print_r($gmts_item_Arr);
							$deter_min_id=rtrim($val["deter_min_id"],',');
							$deter_min_ids=array_unique(explode(",",$deter_min_id));
							$body_part_id=rtrim($val["body_part_id"],',');
							//echo $val["body_part_id"].',';
							$body_part_name=implode(", ",array_unique(explode(",",$body_part_id)));
							//if($allocate_qty==0) 
							$color_fin_fab_qnty=$booking_poColor_data_array[$po_id][$color_id]['fin_fab_qnty'];
							$color_grey_fab_qnty=$booking_poColor_data_array[$po_id][$color_id]['grey_fab_qnty'];
			
							
							$knit_prod_qty=0;	$dyeing_qnty=$fab_store_recv_qty=0;
							foreach($deter_min_ids as $d_id)
							{
								$knit_prod_qty+=$knit_data_array[$po_id][$d_id]['knitQty'];
								$dyeing_qnty+=$dyeing_qnty_arr[$po_id][$d_id][$color_id]['dyeing_qty'];
								$fab_store_recv_qty+=$fab_store_data_array[$po_id][$d_id]['store_quantity'];
							}
							$ycountId=rtrim($yarn_data_arr[$job_id][$po_id][$color_id]["countId"],',');
							$ycountId_ids=array_unique(explode(",",$ycountId));
							$allocate_qty=0;$yarn_issue_qty=0;
							foreach($ycountId_ids as $y_id)
							{
								//$allocate_qty+=$yarnAllocationQtyArr[$po_id][$y_id];
								$yarn_issue_qty+=$yarn_issue_details_arr[$po_id][$y_id]['issue_qnty'];
							}
							
								
							//$job_id=$val["job_no"];
							
							
							$booking_fin_req_qty2=$booking_data_color_array[$po_id][$color_id]['fin_fab_qnty'];
							$grey_fab_req_qnty2=$booking_data_color_array[$po_id][$color_id]['grey_fab_qnty'];
							
							
							
							$sum_grey_fab_req_qnty=array_sum($booking_data_array[$po_id][$color_id]['grey_fab_qnty']);
							 
							$pre_color_contrast=$pre_color_contrast_arr[$po_id][$color_id];
							$ycountData=rtrim($yarn_data_arr[$job_id][$po_id][$color_id]["count"],',');
							$yarn_counts=implode(", ",array_unique(explode(",",$ycountData)));
							$compositionData=rtrim($yarn_data_arr[$job_id][$po_id][$color_id]["composition"],',');
							$compositionName=implode(", ",array_unique(explode(",",$compositionData)));
							
							$type_idData=rtrim($yarn_data_arr[$job_id][$po_id][$color_id]["type_id"],',');
							$yarnType=implode(", ",array_unique(explode(",",$type_idData)));
							$fabric_qty_kg=$booking_data_array[$po_id][$color_id]['fin_fab_qnty'];
							$cut_complete_qty=$cut_lay_first_arr[$po_id][$color_id]['size_qty'];
						//	echo $val["pub_shipment_date"].'='.$exfactory_data_array[$po_id]['ex_factory_date'].',';
							if($knit_qty_check>0 && ($shiping_status==2 || $shiping_status=='')) //Knitting Prod
							{
								$po_color_td="orange";
							}
							else if($shiping_status==3 && ($val["pub_shipment_date"]>$exfactory_data_array[$po_id][$color_id]['ex_factory_date']) )
							{
								$po_color_td="#99FF33";
							}
							else if($val["pub_shipment_date"]<$exfactory_data_array[$po_id][$color_id]['ex_factory_date'])
							{
								if($exfactory_data_array[$po_id][$color_id]['ex_factory_date']!='')
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
								<td width="40"  valign="middle" title="<? echo $job_id;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo $k; ?></td>
                                
                                <td width="110"  valign="middle" title="<? echo $shiping_status;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                                <td width="70"  valign="middle" title="<? echo $shiping_status;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo $val["file_no"]; ?></td>
                                <td width="70"  valign="middle" title="<? echo $job_id;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><? echo $val["ref_no"]; ?></td>
                                
								<td width="110" valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><div style="word-wrap:break-word; width:110px"><? echo $val["season"]; ?></div></td>
								<td width="50" align="center"  valign="middle" title="<? echo $job_id;?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><p><? echo $val["job_no_prefix"]; ?>&nbsp;</p></td>
								<td width="100" align="center"  valign="middle" rowspan="<? echo $job_rowspan_arr[$job_id];?>"><p><? echo $val["style"]; ?>&nbsp;</p></td>
                                <td align="center" width="80"  valign="middle" title="<? echo $val["gmts_item_id"];?>" rowspan="<? echo $job_rowspan_arr[$job_id];?>">
                                <p><? 
								$item_name="";
								foreach($gmts_item_Arr as $gitem)
								{
									//echo $gitem.'D';
									if($item_name=="") $item_name=$garments_item[$gitem];else  $item_name.=",".$garments_item[$gitem];
								}
								echo $item_name;//number_format($plan_cut_qnty,0); ?></p></td>
								<?
								}
								
								
								
								if($p==1)
								{ 
								 $po_item_qty=$po_color_qty_Arr[$po_id]['po_qty'];
								$tot_order_qty+=$po_item_qty;//
								$job_po_item_qty=$job_po_color_qty_Arr[$job_id]['po_qty'];
								$sub_tot_order_qty+=$po_item_qty;//
							 	
								?>
								<td width="100" bgcolor="<? echo $po_color_td;?>" title="PO Id=<? echo $po_id;?>"  valign="middle" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"><div style="word-wrap:break-word; width:100px"><? echo $val["po_number"]; ?></div></td>
								<td width="80" align="right" valign="middle"  rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"><p><? echo number_format($po_item_qty,0); ?></p></td>
								<td width="80" title="Max Ord Recv Date" valign="middle" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"><div><? if(trim($val["po_received_date"])!="" && trim($val["po_received_date"])!='0000-00-00') echo change_date_format($val["po_received_date"]); ?>&nbsp;</div></td>
								<td width="80" title="Max Pub ShipDate"  valign="middle" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"><div><? echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</div></td>
                                <td width="80" title="Original ShipDate"  valign="middle" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"><div><? echo change_date_format($val["shipment_date"]); ?>&nbsp;</div></td>
								<?
								
									$dec=rtrim($val["desc"],',');
									$item_dec=implode(", ",array_unique(explode(",",$dec)));
								?>
								<td  width="100" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"><div style="word-wrap:break-word; width:100px"><? echo $body_part_name; ?></div></td>
								<td  width="150" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"><div style="word-wrap:break-word; width:150px"><? echo $item_dec; ?></div></td>
								<td align="center" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>" width="80"><div style="word-wrap:break-word; width:80px"><? echo $yarn_counts; ?></div></td>
								<td align="center"  valign="middle" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>" width="80"><div style="word-wrap:break-word; width:80px"><? echo $compositionName; ?></div></td>
								<td align="center"  valign="middle" rowspan="<?  echo $po_rowspan_arr2[$job_id][$po_id];?>" width="80"><div style="word-wrap:break-word; width:80px"><? echo $yarnType; ?></div></td>
                                <td align="right"  valign="middle" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>" width="80"><? echo number_format($grey_fab_budget_req_qty,0); ?></td>
								<?
								 
								?>
                                <!--26 dnw-->
								<?
									$yarn_issue_qty=$yarn_issue_details_arr[$po_id]['issue_qnty'];
									$allocate_qty=$po_yarnAllocationQtyArr[$po_id];
									//$item_rowspan_arr[$job_id][$item_id]
								?>
								<td align="right"  valign="middle"  rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"  width="80"><? echo number_format($allocate_qty,0); ?></td>
								<td align="right"  title="Budget Grey req-AllocatedQty"  valign="middle" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];;?>"  width="80"><? $yet_to_allocated_qty=$grey_fab_budget_req_qty-$allocate_qty; echo number_format($yet_to_allocated_qty,0);?></td>
                                <td align="right"  valign="middle" rowspan="<?  echo $po_rowspan_arr2[$job_id][$po_id];?>"  width="80"><? echo number_format($yarn_issue_qty,0); ?></td>
                                <?
								$sub_tot_allocate_qty+=$allocate_qty;
								$sub_tot_yet_to_allocated_qty+=$yet_to_allocated_qty;
								$sub_tot_yarn_issue_qty+=$yarn_issue_qty;$sub_grey_fab_budget_req_qty_yarn+=$grey_fab_budget_req_qty;
								
								$grand_allocate_qty+=$allocate_qty;
								$grand_yet_to_allocated_qty+=$yet_to_allocated_qty;
								$grand_fabric_fin_qty_kg+=$booking_fin_req_qty;
								$grand_yarn_issue_qty+=$yarn_issue_qty;
								
								$grand_grey_fab_budget_req_qty+=$grey_fab_budget_req_qty;
							
								
								
								?>

								
								<td align="right"   valign="middle" title="Grey Fab BookingQty" rowspan="<?  echo $po_rowspan_arr2[$job_id][$po_id];;?>"  width="80"><? echo number_format($grey_fab_req_qnty,0); ?></td>
								<? 
								$sub_tot_grey_fab_req_qnty+=$grey_fab_req_qnty;
								// if($z==1)
								//{
								?>
                                <td align="right"  valign="middle" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"  width="80"><? echo number_format($knit_prod_qty,0); ?></td>
								<td align="right"  valign="middle" title="Grey Fab Qty-KnittingQty" rowspan="<? echo $po_rowspan_arr2[$job_id][$po_id];?>"  width="80"><? 
								$yet_to_production=$grey_fab_req_qnty-$knit_prod_qty;
								echo number_format($yet_to_production,0); ?></td>
                                <?
								$grand_knit_prod_qty+=$knit_prod_qty;	
								$grand_knit_yet_to_production+=$yet_to_production;
								
								$sub_tot_knit_prod_qty+=$knit_prod_qty;
								$sub_tot_yet_to_production+=$yet_to_production;
								$sub_booking_fin_req_qty_yarn+=$booking_fin_req_qty;
								  $grand_grey_fab_req_qnty_kniting+=$grey_fab_req_qnty;
							 	}
								
								?>


								<td align="center"  valign="middle" title="C-Color=<? echo $pre_color_contrast;?>" rowspan="<? //echo $job_rowspan_arr[$job_id];?>"  width="80"><div style="word-wrap:break-word; width:80px"><? if($pre_color_contrast) echo $color_library[$pre_color_contrast];else echo $color_library[$color_id]; ?></div></td>
                                <?
                             //  if($p==1)
								//{
								?>
                                <td align="right"  valign="middle" title="Grey Fab" rowspan="<? //echo $po_rowspan_arr2[$job_id][$po_id];;?>"  width="80"><? echo  number_format($booking_fin_req_qty,0); ?></td>
								<td align="right" title="30 line"  valign="middle" rowspan="<? //echo $po_rowspan_arr2[$job_id][$po_id];;?>"  width="80"><? echo number_format($dyeing_qnty,0); ?></td>
								<td align="right"  valign="middle" title="Fin Fab Qty-Dying Qty" rowspan="<? //echo $po_rowspan_arr2[$job_id][$po_id];;?>"  width="80"><? $dyeing_balance_qty=$booking_fin_req_qty-$dyeing_qnty;echo number_format($dyeing_balance_qty,0); ?></td>
								 <td align="right" width="80"  valign="middle" title="Fin Fab" rowspan="<? //echo $po_rowspan_arr2[$job_id][$po_id];;?>" title="Fab Store -Dyeing Qty"><?

								//$dyeing_req_qty=0;
								//$balance_fab_store_qty=$dyeing_qty-$fab_store_qty;
								echo number_format($booking_fin_req_qty,0); ?></td>
								<td align="right" width="80" valign="middle" rowspan="<? //echo $po_rowspan_arr2[$job_id][$po_id];?>"><? echo number_format($fab_store_recv_qty,0); ?></td>
								<?
							//	}
							
								$fab_store_recv_bal=$booking_fin_req_qty-$fab_store_recv_qty;
								//if($x==1)
								//{
								?>
								<td align="right" width="80"  valign="middle" title="Booking Fin-fab store recv" rowspan="<? //echo $po_rowspan_arr2[$job_id][$po_id];?>"><? echo number_format($fab_store_recv_bal,0); ?></td>
								<?
								$sub_booking_fin_req_qty_dying+=$booking_fin_req_qty;
								$sub_booking_fin_req_qty+=$booking_fin_req_qty;
								$sub_tot_dyeing_qty+=$dyeing_qnty;
								$sub_tot_dyeing_balance_qty+=$dyeing_balance_qty;
								//$sub_booking_fin_req_qty=$booking_fin_req_qty;
								$sub_fab_store_recv_qty+=$fab_store_recv_qty;
								$sub_fab_store_recv_bal+=$fab_store_recv_bal;
							//	$sub_tot_grey_fab_req_qnty_dyeing+=$grey_fab_req_qnty;
							
							 $grand_dyeing_booking_fin_req_qty_yarn+=$booking_fin_req_qty;
								 
								  $grand_grey_fab_req_qnty_dyeing+=$booking_fin_req_qty;
								  $grand_dyeing_balance_qty+=$dyeing_balance_qty;
								  $grand_dyeing_qnty+=$dyeing_qnty;
									//$grand_dyeing_balance_qty+=$dyeing_balance_qty;
								$grand_fab_store_recv_qty+=$fab_store_recv_qty;
								$grand_fab_store_recv_bal+=$fab_store_recv_bal;
								//}
									 
			
								$plan_cut_qnty=$po_color_Arr[$po_id][$color_id]["plan_size_qty"];//val["plan_cut_qnty"]
								$order_quantity_pcs=$po_color_Arr[$po_id][$color_id]["size_qty"];
								 
								$po_value=$po_color_Arr[$po_id][$color_id]["order_total"];
								$cutting_qnty=$garment_prod_data_arr[$po_id][$color_id]['cutting_qnty'];
							 	//$sub_tot_cutting_qty+=$cutting_qnty;
							//	$sub_tot_cutting_bal_qty+=$cut_bal;
								//if($it==1)
								//{
								?>
								
                                <?
								//}
								?>
								<td align="center" width="80" title="<? echo $color_id;?>"   valign="middle" rowspan="<? //echo $po_rowspan_arr[$job_id][$po_id];?>"><div style="word-wrap:break-word; width:80px"><? echo $color_library[$color_id];//number_format($cutting_qnty); ?></div></td>
                                <td align="center" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($samp_data_array[$po_id][$color_id]['samp_submit_date'],0); ?></td>
								<td align="center" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($samp_data_array[$po_id][$color_id]['samp_approv_date']); ?></td>

								<td align="center" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($samp_data_array[$po_id][$color_id]['size_submit_date']); ?></td>
								<td align="center" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($samp_data_array[$po_id][$color_id]['size_approv_date']); ?></td>
                                <td align="center" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($samp_data_array[$po_id][$color_id]['quality_submit_date']); ?></td>
								<td align="center" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($samp_data_array[$po_id][$color_id]['quality_approv_date']); ?></td>
                                
								  <td align="center" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($samp_data_array[$po_id][$color_id]['test_submit_date'],0); ?></td>
								<td align="center" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($samp_data_array[$po_id][$color_id]['test_approv_date']); ?></td>

								
                                
                                <?
                               ////if($z==1)
								//{
								
								$print_issue_qnty_in=$garment_prod_data_arr[$po_id][$color_id]['print_issue_qnty_in'];
								$print_recv_qnty_in=$garment_prod_data_arr[$po_id][$color_id]['print_recv_qnty_in'];
								$emb_issue_qnty_in=$garment_prod_data_arr[$po_id][$color_id]['emb_issue_qnty_in'];
								$emb_recv_qnty_in=$garment_prod_data_arr[$po_id][$color_id]['emb_recv_qnty_in'];
	
								$sew_input_qnty_in=$garment_prod_data_arr[$po_id][$color_id]['sew_input_qnty_in'];
								$sew_recv_qnty_output=$garment_prod_data_arr[$po_id][$color_id]['sew_recv_qnty_in'];
								$finish_qnty_in=$garment_prod_data_arr[$po_id][$color_id]['finish_qnty_in'];
								
								//$po_wise_data_arr[$po_id]["po_value"];
								$shipped_qty=$exfactory_data_array[$po_id][$color_id]['ex_factory_qnty'];
								
								$sub_tot_print_issue_qnty_in+=$print_issue_qnty_in;
								$sub_tot_print_recv_qnty_in+=$print_recv_qnty_in;
								$sub_tot_emb_issue_qnty_in+=$emb_issue_qnty_in;
								$sub_tot_emb_recv_qnty_in+=$emb_recv_qnty_in;

								$sub_sew_input_qnty_in+=$sew_input_qnty_in;
								$sub_tot_sew_recv_qnty_output+=$sew_recv_qnty_output;
								$sub_tot_finish_qnty_in+=$finish_qnty_in;
								
								$sub_po_qty_pcs+=$order_quantity_pcs;//$po_wise_data_arr[$po_id]["po_quantity"];
								$sub_tot_plancut_qty+=$plan_cut_qnty;
								$sub_cut_complete_qty+=$cut_complete_qty;

								$sub_tot_po_value+=$po_value;
								$sub_tot_shipped_qty+=$shipped_qty;
									
								
							
									?>
								
							<td align="right" width="80" valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($order_quantity_pcs,0);; ?></td>
                            <td align="right" width="80" valign="middle" title="Plan Cut"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($plan_cut_qnty,0); ?></td>
                            <td align="right" width="80" valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($cut_complete_qty,0); ?></td>
                            <td align="right" width="80" valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($print_issue_qnty_in,0); ?></td>
                            <td align="right" width="80" valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($print_recv_qnty_in,0); ?></td>
                            <td align="right" width="80" valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($emb_issue_qnty_in,0); ?></td>
                            <td align="right" width="80" valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($emb_recv_qnty_in,0); ?></td>
                            <td align="right" width="80" valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($sew_input_qnty_in,0); ?></td>
								<?
								


								$unit_price=$val["order_rate"];//$po_wise_data_arr[$po_id]["unit_price"];
								$shipped_value=$shipped_qty*$unit_price;
								$excess_value=$po_value-$shipped_value;
								//$sub_tot_shipped_qty+=$shipped_qty;
								$sub_tot_excess_value+=$excess_value;
								?>

								<td align="right" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($sew_recv_qnty_output,0);//number_format($unit_price); ?></td>
								<td align="right" width="80"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($finish_qnty_in,0);//number_format($po_value); ?></td>
								<td align="right" width="80"  valign="middle" title="Ex-FactQty"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($shipped_qty,0); ?></td>
                                <td align="right" width="80"  valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($unit_price,2); ?></td>
                                <td align="right" width="80"  valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($po_value,0); ?></td>
                                <td align="right" width="80"  valign="middle"   rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo number_format($excess_value,0); ?></td>
								<?
								//}
								///if($x==1)
								//{
								?>
								<td align="left"  valign="middle"  rowspan="<? //echo $po_rowspan_arr[$job_id][$item_id][$po_id];?>"><? echo change_date_format($exfactory_data_array[$po_id][$color_id]['ex_factory_date']); ?></td>
								<?
								//}
								?>
							</tr>
							<?
								
								
									
								
							//	$grand_grey_fab_req_qnty+=$grey_fab_req_qnty;
							
								$grand_tot_shipped_qty+=$shipped_qty;
								
								//$tot_plancut_qty+=$plan_cut_qnty;
								$tot_print_issue_qnty_in+=$print_issue_qnty_in;
								$tot_print_recv_qnty_in+=$print_recv_qnty_in;
								$tot_emb_issue_qnty_in+=$emb_issue_qnty_in;
								$tot_emb_recv_qnty_in+=$emb_recv_qnty_in;

								$tot_sew_input_qnty_in+=$sew_input_qnty_in;
								$tot_sew_recv_qnty_output+=$sew_recv_qnty_output;
								$tot_finish_qnty_in+=$finish_qnty_in;
								//$grand_plan_cut_qnty+=$plan_cut_qnty;
								$grand_cut_complete_qty+=$cut_complete_qty;
								
								$grand_order_quantity_pcs+=$order_quantity_pcs;
								$grand_plan_cut_qnty+=$plan_cut_qnty;
								$grand_po_value+=$po_value;
								$grand_tot_excess_value+=$excess_value;
		
									$i++;$x++;$y++;$p++;$it++;
									} //color End	
								
								?>
                               
                                <?
							 
							 
						// }
							
							?>
                            
                             

						<?
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
                            <td width="80" align="right" id=""><? //echo number_format($sub_booking_fin_req_qty); ?></td>
							<td width="100">Sub Total</td>
							<td width="80" align="right"><? echo number_format($sub_tot_order_qty,0); $sub_tot_order_qty=0;?></td>
							<td width="80"></td>
                            <td width="80"></td>
							<td width="80"></td>
							 
							<td width="100"></td>
							<td width="150" align="right" id=""></td>
							<td width="80" align="right" id=""></td>
                            
                            
                            
							
							<td width="80" align="right" id=""><? //echo number_format($sub_fabric_qty_kg,0); $sub_fabric_qty_kg=0;?></td>
							<td width="80" align="right" id="">&nbsp;</td>

							<td width="80" align="right" id=""><? echo number_format($sub_grey_fab_budget_req_qty_yarn,0); $sub_grey_fab_budget_req_qty_yarn=0;?></td>

							<td width="80" align="right" id=""><? echo number_format($sub_tot_allocate_qty,0);$sub_tot_allocate_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_yet_to_allocated_qty,0);$sub_tot_yet_to_allocated_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_yarn_issue_qty,0); $sub_tot_yarn_issue_qty=0;?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_grey_fab_req_qnty,0);$sub_tot_grey_fab_req_qnty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_knit_prod_qty,0);$sub_tot_knit_prod_qty=0; ?></td>
                            <td width="80" align="right" id=""><? echo number_format($sub_tot_yet_to_production,0);$sub_tot_yet_to_production=0; ?></td>
                          

							<td width="80" align="right" id=""><? //echo number_format($sub_tot_dyeing_qty,0); $sub_tot_dyeing_qty=0;?></td>
							<td width="80" align="right"><? echo number_format($sub_booking_fin_req_qty_dying,0); ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_dyeing_qty,0);$sub_tot_dyeing_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_dyeing_balance_qty,0); ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_booking_fin_req_qty_dying,0);$sub_booking_fin_req_qty_dying=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_fab_store_recv_qty,0); $sub_fab_store_recv_qty=0;?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_fab_store_recv_bal,0);$sub_fab_store_recv_bal=0; ?></td>
                            
                            <td width="80" align="right" id=""><? //echo number_format($sub_booking_fin_req_qty); ?></td>
                            <td width="80" align="right" id=""><? //echo number_format($sub_booking_fin_req_qty); ?></td>
                            <td width="80" align="right" id=""><? //echo number_format($sub_booking_fin_req_qty); ?></td>
                            <td width="80" align="right" id="">&nbsp;</td>
                            <td width="80" align="right" id="">&nbsp;</td>
							<td width="80" align="right" id=""><? //echo number_format($sub_po_qty_pcs,0);$sub_po_qty_pcs=0; ?></td>
							<td width="80" align="right" id=""><? //echo number_format($sub_tot_plancut_qty); $sub_tot_plancut_qty=0;?></td>
							<td width="80" align="right" id=""><? //echo number_format($sub_cut_complete_qty);$sub_cut_complete_qty=0; ?></td>
                            
							<td width="80" align="right" id=""><? //echo number_format($sub_tot_print_recv_qnty_in);$sub_tot_print_recv_qnty_in=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_po_qty_pcs,0);$sub_po_qty_pcs=0;  ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_plancut_qty,0);$sub_tot_plancut_qty=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_cut_complete_qty,0);$sub_cut_complete_qty=0; ?></td>
                            <td width="80" align="right" id=""><? echo number_format($sub_tot_print_issue_qnty_in,0); $sub_tot_print_issue_qnty_in=0;?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_print_recv_qnty_in,0);$sub_tot_print_recv_qnty_in=0; ?></td>
							
                            <td width="80" align="right" id=""><? echo number_format($sub_tot_emb_issue_qnty_in,0);$sub_tot_emb_issue_qnty_in=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_emb_recv_qnty_in,0);$sub_tot_emb_recv_qnty_in=0; ?></td>
						

							<td width="80" align="right" id=""><? echo number_format($sub_sew_input_qnty_in,0);$sub_sew_input_qnty_in=0; ?></td>
                            
							<td width="80" align="right" id=""><? echo number_format($sub_tot_sew_recv_qnty_output,0);$sub_tot_sew_recv_qnty_output=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_finish_qnty_in,0);$sub_tot_finish_qnty_in=0; ?></td>
                            <td width="80" align="right" id=""><? echo number_format($sub_tot_shipped_qty,0);$sub_tot_shipped_qty=0; ?></td>
							<td width="80" align="right" id=""><? //echo number_format($sub_tot_po_value);$sub_tot_po_value=0; ?></td>
							<td width="80" align="right" id=""><? echo number_format($sub_tot_po_value,0); ?></td>
                            <td width="80" align="right" id=""><? echo number_format($sub_tot_excess_value,0); ?></td>
							<td width="" align="right" id="">&nbsp;</td>
               			 </tr>
						 <?
						 /*$grand_tot_yarn_req_qty+=$yarn_req_grey_qty;
						 $grand_tot_yarn_issue_qty+=$yarn_issue_qty;
						 $grand_tot_yarn_balance_qty+=$yarn_req_grey_qty-$yarn_issue_qty;
						 $grand_tot_program_qnty+=$program_qnty;
						 $grand_tot_knit_qty+=$knit_qty;
						 $grand_tot_knit_balance_qty+=$program_qnty-$knit_qty;
						 $grand_tot_batch_qty+=$batch_qty;
						 $grand_tot_dyeing_qty+=$dyeing_qty;
						 $grand_tot_dyeing_balance+=$sub_dyeing_balance;*/
						 $k++;
						
						
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
                    <td width="80" align="right" id=""><? //echo number_format($sub_booking_fin_req_qty); ?></td>
                    <td width="100">Total</td>
					<td width="80"><? echo number_format($tot_order_qty,0); ?></td>
					<td width="80"></td>
                    <td width="80"></td>
					<td width="80"></td>
					 
					<td width="100"></td>
                    <td width="150" align="right" id="td_yarn_req_qty"></td>
                    <td width="80" align="right" id="td_wovenReqQty"></td>
                  
                   
					<td width="80" align="right" id="td_printRcvOut_qty"><? //echo number_format($grand_fabric_qty_kg,0); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty">&nbsp;</td>

                    <td width="80" align="right" id="td_fab_finIn_qty"><? echo number_format($grand_grey_fab_budget_req_qty,0); ?> </td>
                     <td width="80" align="right" id="td_printRcvIn_qty"><? echo number_format($grand_allocate_qty,0); ?></td>
                     <td width="80" align="right" id="td_printRcvIn_qty"><? echo number_format($grand_yet_to_allocated_qty,0); ?></td>
                     <td width="80" align="right" id="td_printRcvIn_qty"><? echo number_format($grand_yarn_issue_qty,0); ?></td>
                      
                     <td width="80" align="right" id="td_grey_fab_req_qty"><? echo number_format($grand_grey_fab_req_qnty_kniting,0); ?></td>
                     <td width="80" align="right" id="td_printRcvIn_qty"><? echo number_format($grand_knit_prod_qty,0); ?></td>
                    <td width="80" align="right" id="td_sales_yarn_qty"><? echo number_format($grand_knit_yet_to_production,0); ?></td>
                    
                    <td width="80" align="right" id="td_grand_tot_yarn_issue_qty"><? //echo number_format($grand_tot_yarn_issue_qty,2); ?></td>
                    <td width="80" align="right" id="td_grand_tot_yarn_issue_balance"><? echo number_format($grand_grey_fab_req_qnty_dyeing,0); ?></td>
                    
                    <td width="80" align="right" id="td_sewInOutput_qty"><? echo number_format($grand_dyeing_qnty,0); ?></td>
                    <td width="80" align="right" id="td_sewIn_qty"><? echo number_format($grand_dyeing_balance_qty,0); ?></td>
                    
                    
                    <td width="80" align="right" id="td_dyeing_booking_fin_qty"><? echo number_format($grand_grey_fab_req_qnty_dyeing,0); ?></td>
                     <td width="80" align="right" id="td_store_recv_qty"><? echo number_format($grand_fab_store_recv_qty,0); ?></td>
                      <td width="80" align="right" id="td_store_recv_qty_bal"><? echo number_format($grand_fab_store_recv_bal,0); ?></td>
                      
                   
                    <td width="80" align="right" id=""><? //echo number_format($grand_tot_dyeing_qty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($grand_tot_dyeing_balance); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($grand_tot_dyeing_balance); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_plancut_qty); ?></td>
                    <td width="80" align="right" id="">&nbsp;</td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_plancut_qty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($grand_plan_cut_qnty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_cutting_bal_qty); ?></td>

                    <td width="80" align="right" id=""><? //echo number_format($tot_order_qty,0); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($grand_order_quantity_pcs,0); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($grand_plan_cut_qnty,0); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($grand_cut_complete_qty,0); ?></td>
                     <td width="80" align="right" id=""><? echo number_format($tot_print_issue_qnty_in,0); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($tot_print_recv_qnty_in,0); ?></td>
                    <td width="80" align="right" id="50"><? echo number_format($tot_emb_issue_qnty_in,0); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($tot_emb_recv_qnty_in,0);//number_format($tot_finish_qnty_in); ?></td>
					

                   
                    <td width="80" align="right" id=""><? echo number_format($tot_sew_input_qnty_in,0); ?></td>
					<td width="80" align="right" id=""><? echo number_format($tot_sew_recv_qnty_output,0); ?></td>
                     <td width="80" align="right" id=""><? echo number_format($tot_finish_qnty_in,0); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($grand_tot_shipped_qty,0); ?></td>
					<td width="80" align="right" id=""><? //echo number_format($grand_po_value,0); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($grand_po_value,0); ?></td>
                    <td width="80" align="right" id=""><?  echo number_format($grand_tot_excess_value,0); ?></td>
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
