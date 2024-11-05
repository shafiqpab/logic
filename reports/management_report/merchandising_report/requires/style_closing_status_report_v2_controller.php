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

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$season_name_library=return_library_array( "select id, season_name from  lib_buyer_season", "id", "season_name"  );


if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/style_closing_status_report_v2_controller', this.value, 'load_drop_down_season', 'season_td');" ,0);
	exit();
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_surch_list_view', 'search_div', 'style_closing_status_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
	//echo $companyID.'FFFFD';die;
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $type_id; ?>', 'order_surch_list_view', 'search_div', 'style_closing_status_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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


if($action=='job_report_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$cbo_date_cat=str_replace("'","",$cbo_date_cat);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$txt_file_no=str_replace("'","",$txt_file_no);
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_style=str_replace("'","",$txt_style);


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
	$ship_date_cond="";
	if($cbo_date_cat==1)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
		}
	}
	else if($cbo_date_cat==3)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and c.EX_FACTORY_DATE between '$txt_date_from' and '$txt_date_to'  and c.PO_BREAK_DOWN_ID=b.id ";
			$table_c=", PRO_EX_FACTORY_MST c";
		}
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
		}
	}

	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($style_ref_id)!="") $job_no_cond="and a.id  in($style_ref_id)";
	
	if(trim($txt_file_no)!="") $file_no_cond="and b.file_no in($txt_file_no)";
	if($cbo_season_id>0) $season_cond="and a.season_buyer_wise  in($cbo_season_id)";
	
	if(trim($txt_style_ref)!="") $job_no_cond.="and a.job_no_prefix_num  in($txt_style_ref)";
	
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	
		
	if(trim($txt_style)!="") $job_no_cond.="and a.style_ref_no like('%$txt_style%')";
	if(trim($txt_style) || trim($txt_file_no) || trim($txt_style_ref) || trim($cbo_season_id))
	{
		$full_close_cond="";
	} 
	else $full_close_cond="and b.shiping_status=3";
		
	$sql_po="select a.buyer_name, a.job_no, a.season_buyer_wise,a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date
	from wo_po_details_master a, wo_po_break_down b $table_c
	where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $season_cond $file_no_cond  $full_close_cond
	group by a.buyer_name, a.job_no, a.season_buyer_wise,a.job_no_prefix_num, to_char(a.insert_date,'YYYY') , a.style_ref_no, a.total_set_qnty , b.id , b.file_no, b.po_number, b.po_quantity , b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date
	order by a.job_no, b.pub_shipment_date, b.id";//
   	//echo  $sql_po;  
	$sql_po_result=sql_select($sql_po);
	
	//Full ship check start............................................................
	$selectedJobArr=array();
	foreach($sql_po_result as $row)
	{
		$selectedJobArr[$row[csf("job_no")]]=$row[csf("job_no")];
	}
	
	
	$poSql="select JOB_NO_MST from wo_po_break_down where shiping_status <>3  and STATUS_ACTIVE=1 and IS_DELETED=0 ";//
	$p=1;
	$job_chunk_arr=array_chunk($selectedJobArr,999);
	foreach($job_chunk_arr as $jobArr)
	{
		if($p==1){$poSql .=" and (JOB_NO_MST in('".implode("','",$jobArr)."')";} 
		else{$poSql .=" or JOB_NO_MST in('".implode("','",$jobArr)."')";}
		$p++;
	}
	$poSql .=")";
	//echo $poSql;
	$poSqlResult=sql_select($poSql);
	$noFullShipJobArr=array();
	foreach($poSqlResult as $row)
	{
		$noFullShipJobArr[$row[JOB_NO_MST]]=1;
	}
	//Full ship check end............................................................
	
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();
	foreach($sql_po_result as $row)
	{
		if($noFullShipJobArr[$row[csf("job_no")]]!=1)
		{
			if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
			$result_data_arr[$row[csf("job_no")]]["po_id"].=$row[csf("po_id")].',';
			$result_data_arr[$row[csf("job_no")]]["season"]=$row[csf("season_buyer_wise")];
			$result_data_arr[$row[csf("job_no")]]["file_no"].=$row[csf("file_no")].',';
			$result_data_arr[$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
			$result_data_arr[$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
			$result_data_arr[$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$result_data_arr[$row[csf("job_no")]]["job_year"]=$row[csf("job_year")];
			$result_data_arr[$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$result_data_arr[$row[csf("job_no")]]["ratio"]=$row[csf("ratio")];
			$result_data_arr[$row[csf("job_no")]]["ref_no"]=$row[csf("grouping")];
			$result_data_arr[$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
			$result_data_arr[$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
			$result_data_arr[$row[csf("job_no")]]["po_qnty"]+=$row[csf("po_qnty")]*$row[csf("ratio")];
			$result_data_arr[$row[csf("job_no")]]["plan_cut"]+=$row[csf("plan_cut")]*$row[csf("ratio")];
			$result_data_arr[$row[csf("job_no")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
			//$result_data_arr[$row[csf("job_no")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
			//$result_data_arr[$row[csf("job_no")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
			$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
			$JobArr[]="'".$row[csf('job_no')]."'";
			$job_no=$row[csf('job_no')];
		}
	}
	if($all_po_id==''){echo "<h2 style='color:#FE4B4B;'>Data not found</h2>";exit();}
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
	/*if($db_type==2)
	{
		$col_grp="listagg(CAST(a.booking_no as VARCHAR(4000)),',') within group (order by a.booking_no) as booking_no";
	}
	else
	{
		$col_grp="group_concat(a.booking_no) as booking_no";
	}*/
	$booking_req_arr=array();
	$sql_wo=sql_select("select a.booking_no,a.booking_type,b.po_break_down_id,
	(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.fin_fab_qnty ELSE 0 END) AS fin_fab_qnty,
	(CASE WHEN a.item_category=12 and a.process=35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty
	from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in ");
	
	//finish_prod_arr
	foreach ($sql_wo as $brow)
	{
		if($brow[csf("grey_req_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['gray']+=$brow[csf("grey_req_qnty")];
		}
		if($brow[csf("woven_req_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['woven']+=$brow[csf("woven_req_qnty")];
		}
		if($brow[csf("fin_fab_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['fin']+=$brow[csf("fin_fab_qnty")];
		}
		if($brow[csf("aop_wo_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['aop_wo_qnty']+=$brow[csf("aop_wo_qnty")];
		}
		if($brow[csf("booking_type")]==1)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['booking_no'].=$brow[csf("booking_no")].',';
		}
	}
	$sql_res=sql_select("select b.po_break_down_id as po_id,
	sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty,
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as exfac_qnty
	from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 and b.shiping_status=3 $po_cond_for_in group by b.po_break_down_id");
	$ex_factory_qty_arr=array();
	foreach($sql_res as $row)
	{
		$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('exfac_qnty')]-$row[csf('return_qnty')];
	}

	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	

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

	$dataArrayTrans=sql_select("select a.po_breakdown_id,
	sum(CASE WHEN a.entry_form ='11' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_yarn,
	sum(CASE WHEN a.entry_form ='11' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_yarn,
	sum(CASE WHEN a.entry_form ='13' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_knit,
	sum(CASE WHEN a.entry_form ='13' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_knit,
	sum(CASE WHEN a.entry_form in(14,15) and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_finish,
	sum(CASE WHEN a.entry_form in(14,15) and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_finish,
	sum(CASE WHEN a.entry_form ='14' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_rec_knit2,
	sum(CASE WHEN a.entry_form ='14' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_rec_knit2,
	sum(CASE WHEN a.entry_form in(82,183,110) and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty_rec_knit,
	sum(CASE WHEN a.entry_form in(82,183,110) and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty_rec_knit,
	
	sum(case when a.entry_form in (126,52) then a.quantity else 0 end) as issue_ret_qnty,
	sum(case when a.entry_form in (46) then a.quantity else 0 end) as rec_ret_qnty,
	sum(case when a.entry_form in(14,15,134) and a.trans_type=5 then a.quantity else 0 end) as rec_trns_qnty,
	sum(case when a.entry_form in(14,15,134) and a.trans_type=6 then a.quantity else 0 end) as issue_trns_qnty
	

	from order_wise_pro_details a where a.status_active=1 and a.is_deleted=0 and a.entry_form in(11,13,15,14,82,183,110,134,126,52,46)
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
		//============================new add========
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["issue_ret_qnty"]=$row[csf("issue_ret_qnty")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["rec_ret_qnty"]=$row[csf("rec_ret_qnty")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["rec_trns_qnty"]=$row[csf("rec_trns_qnty")];
		$transfer_data_arr[$row[csf("po_breakdown_id")]]["issue_trns_qnty"]=$row[csf("issue_trns_qnty")];
	}

	$prodKnitDataArr=sql_select("select a.po_breakdown_id,
	sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
	sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
	sum(CASE WHEN a.entry_form in(58,22,23) and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_inside,
	sum(CASE WHEN a.entry_form in(58,22,23) and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_outside
	from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category=13 and a.entry_form in(2,22,58,23) and c.entry_form in(2,22,58,23) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 group by a.po_breakdown_id");// and c.receive_basis<>9
				
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_in"]=$row[csf("knit_qnty_in")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_out"]=$row[csf("knit_qnty_out")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec_inside"]=$row[csf("knit_qnty_rec_inside")];
		$kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec_outside"]=$row[csf("knit_qnty_rec_outside")];
	}

	$prodFinDataArr=sql_select("select a.po_breakdown_id,
	(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,66,68) THEN a.quantity ELSE 0 END) AS finish_qnty_in,
	(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,66,68) THEN a.quantity ELSE 0 END) AS finish_qnty_out,
	(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_in_rec_gmt,
	(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(37) THEN a.quantity ELSE 0 END) AS finish_qnty_out_rec_gmt,
	(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec
	from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category in (2,3) and a.entry_form in(7,17,37,66,68) and c.entry_form in(7,17,37,66,68) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $po_cond_for_in2");
//7,37,66,68
	$finish_prod_arr=array();
	foreach($prodFinDataArr as $row)
	{
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in"]+=$row[csf("finish_qnty_in_rec_gmt")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out"]+=$row[csf("finish_qnty_out")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in_rec_gmt"]+=$row[csf("finish_qnty_in_rec_gmt")];
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out_rec_gmt"]+=$row[csf("finish_qnty_out_rec_gmt")];
		
		$finish_prod_arr[$row[csf("po_breakdown_id")]]["woven_rec"]+=$row[csf("woven_rec")];
	}//c.knit_dye_source
	$issueData=sql_select("select po_breakdown_id,
	(CASE WHEN a.entry_form=16 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_in_qnty,
	(CASE WHEN a.entry_form=16  and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_out_qnty,
	(CASE WHEN a.entry_form=61 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_out,
	(CASE WHEN a.entry_form=61 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS grey_issue_qnty_roll_wise_in,
	(CASE WHEN a.entry_form in(18) THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty,
	(CASE WHEN a.entry_form in(46) THEN a.quantity ELSE 0 END) AS rec_ret_qnty,
	(CASE WHEN a.entry_form=71 THEN a.quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
	(CASE WHEN a.entry_form=19 THEN a.quantity ELSE 0 END) AS woven_issue
	from order_wise_pro_details a,inv_grey_fabric_issue_dtls b,inv_issue_master
	c where a.dtls_id=b.id and b.mst_id=c.id  and a.entry_form in(16,18,19,61,71,46) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in2 ");

	$grey_cut_issue_arr=array();
	foreach($issueData as $row)
	{
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty_in"]=$row[csf("grey_issue_in_qnty")]+$row[csf("grey_issue_qnty_roll_wise_in")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty_out"]=$row[csf("grey_issue_out_qnty")]+$row[csf("grey_issue_qnty_roll_wise_out")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["issue_to_cut_qnty"]=$row[csf("issue_to_cut_qnty")]+$row[csf("issue_to_cut_qnty_roll_wise")]+$row[csf("rec_ret_qnty")];
		$grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["woven_issue"]=$row[csf("woven_issue")];
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
	if($cbo_search_type==3)
	{
		$tbl_width=3640;
	}
	else if($cbo_search_type==1)//Textile
	{
		$tbl_width=1770;
	}
	else if($cbo_search_type==2) //Gmt Part
	{
		$tbl_width=2440;
	}
	else
	{
		$tbl_width=3560;
		//$ship_date_html="Last Shipment Date";
		//$ex_fact_date_html="Last Ex-Fact. Date";
		//Textle with=1110+580=1690 //Gmt=1780+580=2360
	}
	ob_start();
	?>
    <div style="width:100%">
         <table width="<? echo $tbl_width;?>">
            <tr>
                <td align="center" width="100%" colspan="44" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)].'<br/>';
				if($txt_date_from!="") echo  $txt_date_from.' To '.$txt_date_to;
				 ?></td>
            </tr>
        </table>
        <?
        if($cbo_search_type==3) //Both part
		{
			?>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                    <tr>
                    	<th colspan="7">Style Information</th>
                     	<th colspan="15"> Textile Part</th>
                     	<th colspan="24">Garment Part</th>
                    </tr>
                    <tr style="font-size:13px">
                       	<th width="40">SL</th>
                       	<th width="110">Buyer</th>
                       	<th width="100">Style No</th>
                       	<th width="50">Job No</th>
                      	<th width="80">Season</th>
                       	<th width="100">Order No</th>
                       	<th width="80">Job Qty.<br/> (Pcs)</th>
                       	<th width="80">Yarn Req.<br/><font style="font-size:9px;color: red; font-weight:100">(As Per Pre-Cost)</font></th>
                       	<th width="80">Yarn Total <br/>Issued</th>
                       	<th width="90">Yarn Less or Over Issued</th>
                       	<th width="80">Knit. Gray <br/>Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>                        
                       	<th width="80">Knit. Total <br/>Prod.</th>                        
                       	<th width="80">Grey.  Receive</th>
                       	<th width="80">Knit. Under or Over Prod.</th>
                       	<th width="80">Knit. Issued <br/>To Dyeing</th>
                       	<th width="80">Knit. Left <br/>Over</th>
                       	<th width="80">Fin Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>                        
                       	<th width="80">Fin Prod. <br/>Total</th>
                       	<th width="80">Fin. Fab. Receive</th>
                       	<th width="80">Fin Less or Over</th>
                       	<th width="80">Fin Issue <br/>To Cut</th>
                       	<th width="80">Fin Left Over</th>  

                        <th width="80" title="Gmt Part Start from">Plan Qty(Pcs)</th>
                        <th width="80">Cutting Qty</th>                        
                        <th width="80">Cutting%</th>                        
                        <th width="80">Cutting  Excess or Less Qty</th>
                        <th width="80">Gmts. Total Print Issued</th> 
                        <th width="80">Print.Delv. Balance</th>
                        <th width="80">Print. Rcv.</th>
                        <th width="100">Print. Reject</th>  
                        <th width="80">Prnt Yet to Rcv.</th>
                        <th width="80">Total Sew. Input</th>
                        <th width="80">Yet to Sew. Input</th>
                        <th width="80">Total  Sewing</th>
                        <th width="80">Sew Out Reject</th>
                        <th width="80">Yet to Sew Out</th>
                        <th width="80">Total Finish</th>
                        <th width="80">Finish Reject</th>
                        <th width="80">Yet to Finish</th>
                        <th width="80">Ex-Factory</th>
                        <th width="80">Left Over</th>
                        <th width="80">Short Ex-Fac. Qty</th>                        
                        <th width="80">Total Reject</th>
                        <th width="80">Total Balance</th>
                        <th width="">Cut TO Ship Ratio</th>
                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
                <?
					$i=1;$total_grey_rec_qty=$total_grey_req_qty=$tot_yarn_less_over_qty=$tot_knit_prod_qty=$total_grey_rec_qty=$total_under_over_prod=$total_knit_issuedToDyeQnty=$total_knit_left_over=$tot_fin_req_qty=$tot_total_finishing_prod=$total_fin_fab_recv=$tot_fin_less_over_prod=$total_issuedToCutQnty=$tot_finish_left_over=$tot_tot_po_qnty=$tot_cuttingQty=$tot_cutting_excess_lessQty=$tot_total_print_issued=$tot_print_delv_balance=$tot_total_print_recv=$tot_print_recv_reject_qnty=$tot_print_yet_recv_qty=$tot_total_sew_input=$tot_total_sew_recv=$tot_yet_sewing_output=$tot_sew_reject_qnty=$tot_total_gmts_finish_qnty=$tot_finish_reject_qnty=$tot_yet_to_finish=$tot_ex_factory_qty=$tot_left_over_finish_gmts=$tot_short_exfac_qty=$tot_total_reject=$tot_total_balance_qty=$tot_cut_to_ship_ratio=$tot_tot_plan_qnty=0;
					
					foreach($result_data_arr as $job_no=>$val)
					{
						$ratio=$val["ratio"];
						$po_id=rtrim($val["po_id"],',');
						$po_ids=array_unique(explode(",",$po_id));
						$yarn_required=$yarn_req_job=$tot_grey_req_qty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$grey_fabric_aop_req_wo_qnty=$aop_delivery_qty=$aop_aop_recv_qnty=$knit_qnty_in=$knit_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_recv_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_reject_qnty=$total_sew_recv=$total_gmts_finish_qnty=$finish_reject_qnty=$ex_factory_qty=0;
						$grey_fabric_req_qnty=$cuttingQty=$finish_fabric_req_qnty=$knit_gray_rec_inside=$knit_gray_rec_outside=$transfer_in_qnty_knit=$transfer_out_qnty_knit=$transfer_in_qnty_rec_knit=$transfer_out_qnty_rec_knit=$issuedToDyeQnty_in=$issuedToDyeQnty_out=$issuedToCutQnty=$transfer_out_qnty_finish=$transfer_in_qnty_finish=$finish_qnty_out_rec_gmt=$print_issue_qnty_out=$print_issue_qnty_in=$gmt_finish_in=$gmt_finish_out=0;
						$finish_qnty_in_rec_gmt=$finish_qnty_out=$finish_qnty_in=$$issue_ret_qnty=$rec_ret_qnty=$rec_trns_qnty=$issue_trns_qnty=0;
						foreach($po_ids as $pId)
						{
							$yarn_required+=$yarn_qty_arr[$pId];
							$yarn_req_job+=$yarn_qty_arr[$pId];
							//$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							$yarn_issue_inside+=$yarn_issue_arr[$pId]["issue_qnty_in"]-$yarn_issue_rtn_arr[$pId]["return_qnty_in"];
							$yarn_issue_outside+=$yarn_issue_arr[$pId]["issue_qnty_out"]-$yarn_issue_rtn_arr[$pId]["return_qnty_out"];
							$transfer_in_qnty_yarn+=$transfer_data_arr[$pId]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn+=$transfer_data_arr[$pId]["transfer_out_qnty_yarn"];
							$grey_fabric_req_qnty+=$booking_req_arr[$pId]['gray'];
							//echo $grey_fabric_req_qnty.'X';
							//$grey_fabric_aop_req_wo_qnty+=$booking_req_arr[$pId]['aop_wo_qnty'];
							$aop_delivery_qty+=$aop_delivery_array[$pId]['batch_issue_qty'];
							$aop_aop_recv_qnty+=$aop_delivery_array[$pId]['aop_recv_qnty'];
							
							$knit_qnty_in+=$kniting_prod_arr[$pId]["knit_qnty_in"];
							$knit_qnty_out+=$kniting_prod_arr[$pId]["knit_qnty_out"];
							
							$knit_gray_rec_inside+=$kniting_prod_arr[$pId]["knit_qnty_rec_inside"];
							$knit_gray_rec_outside+=$kniting_prod_arr[$pId]["knit_qnty_rec_outside"];
							$transfer_in_qnty_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_knit"];
							$transfer_in_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_rec_knit"];
							$issuedToDyeQnty_in+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_out"];
							$issuedToCutQnty+=$grey_cut_issue_arr[$pId]["issue_to_cut_qnty"]+$transfer_data_arr[$pId]["issue_trns_qnty"];
							$finish_fabric_req_qnty+=$booking_req_arr[$pId]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							
							$issue_ret_qnty+=$transfer_data_arr[$pId]["issue_ret_qnty"];
							//$rec_ret_qnty+=$transfer_data_arr[$pId]["rec_ret_qnty"];
							$rec_trns_qnty+=$transfer_data_arr[$pId]["rec_trns_qnty"];
							//$issue_trns_qnty+=$transfer_data_arr[$pId]["issue_trns_qnty"];
							
		 
							
							$finish_qnty_in+=$finish_prod_arr[$pId]["finish_qnty_in"];
							$finish_qnty_out+=$finish_prod_arr[$pId]["finish_qnty_out"];
							$finish_qnty_in_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_out_rec_gmt"];
							$transfer_in_qnty_finish+=$transfer_data_arr[$pId]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish+=$transfer_data_arr[$pId]["transfer_out_qnty_finish"];
							
					
							$print_recv_qnty_in+=$garment_prod_data_arr[$pId]['print_recv_qnty_in'];
							$print_recv_qnty_out+=$garment_prod_data_arr[$pId]['print_recv_qnty_out'];
							$print_issue_qnty_in+=$garment_prod_data_arr[$pId]['print_issue_qnty_in'];
							$print_issue_qnty_out+=$garment_prod_data_arr[$pId]['print_issue_qnty_out'];
							
							$print_recv_reject_qnty+=$garment_prod_data_arr[$pId]['print_reject_qnty'];
							$sew_input_qnty_in+=$garment_prod_data_arr[$pId]['sew_input_qnty_in'];
							$sew_input_qnty_out+=$garment_prod_data_arr[$pId]['sew_input_qnty_out'];
							$sew_reject_qnty+=$garment_prod_data_arr[$pId]['sew_reject_qnty'];
							
							$sew_recv_qnty_in=$garment_prod_data_arr[$pId]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$pId]['sew_recv_qnty_out'];
							$total_sew_recv+=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							
							$gmt_finish_in=$garment_prod_data_arr[$pId]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$pId]['finish_qnty_out'];
							$total_gmts_finish_qnty+=$gmt_finish_in+$gmt_finish_out;
							$finish_reject_qnty+=$garment_prod_data_arr[$pId]['finish_reject_qnty'];
							$cuttingQty+=$garment_prod_data_arr[$pId]['cutting_qnty'];
						}
						//$issue_ret_qnty+=$transfer_data_arr[$pId]["issue_ret_qnty"];
							//$rec_ret_qnty+=$transfer_data_arr[$pId]["rec_ret_qnty"];
							//$rec_trns_qnty+=$transfer_data_arr[$pId]["rec_trns_qnty"];
							//$issue_trns_qnty
							
						$total_knitting=$knit_qnty_in+$knit_qnty_out;
						$fin_fab_recv=$finish_qnty_in+$finish_qnty_out+$issue_ret_qnty;
						
						$grey_issuedToDyeQnty=$issuedToDyeQnty_in+$issuedToDyeQnty_out;
						$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
						$tot_po_qnty=$val["po_qnty"];
						$plan_cut_qty=$val["plan_cut"];
						$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
						$under_over_issued=$yarn_required-$total_issued;
						
						$tot_grey_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;
						$grey_left_over=$tot_grey_rec_qty-$grey_issuedToDyeQnty;
						$total_finishing_prod=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;
						$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);
						$finish_left_over=$finish_fabric_req_qnty-$issuedToCutQnty;
						//	$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
						$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
						$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
						$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
						$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
						$fin_less_over_prod=$finish_fabric_req_qnty-$total_finishing_prod;
						?>
						<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
			 				<td width="40"><? echo $i; ?></td>
                            <td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                            <td width="100"><p><? echo $val["style_ref_no"]; ?></p></td>
                            <td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><p><? echo $season_name_library[$val["season"]]; ?>&nbsp;</p></td>
                            <td width="100" align="center"> <a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','PO Info','po_popup')">View</a></td>
                            <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty); ?></p></td>
                            <td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
                            <td align="right" width="90" title="YarnReq-YarnIssueQty"><? $yarn_less_over_qty=$yarn_required-$total_issued;echo number_format($yarn_less_over_qty,2); ?></td>
                            <td align="right" width="80"> <a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Booking Info','booking_popup',1)"><? echo number_format($grey_fabric_req_qnty,2); ?></a></td>
                            <td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
                            <td align="right" width="80" title="Transfer In=(<? echo $transfer_in_qnty_rec_knit.',Out='.$transfer_out_qnty_rec_knit;?>)"><? echo number_format($tot_grey_rec_qty,2); ?></td>
                            <td align="right" width="80" title="Knit Grey Fab Req-Total Prod Qty"><? echo number_format($under_over_prod,2); ?></td>
                             <td align="right" width="80"><? echo number_format($grey_issuedToDyeQnty,2); ?></td>
                            <td align="right" width="80" title="Grey Recv-Grey issuetoDye"><? echo number_format($grey_left_over,2); ?></td>
                            <td align="right" width="80"> <a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Booking Info','booking_popup',2)"><? echo number_format($finish_fabric_req_qnty,2); ?></a><? //echo number_format($finish_fabric_req_qnty,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_finishing_prod,2); ?></td>
                            <td align="right" width="80" title="Knit Fin Fab Recv"><? echo number_format($fin_fab_recv,2); ?></td>
                            <td align="right" width="80" title="Fin Fab Req-Fin Fab Total"><? echo number_format($fin_less_over_prod,2); ?></td>
                            
                            <td align="right" width="80" title="Knit Fin Fab Issue"><? echo number_format($issuedToCutQnty,2); ?></td>
                            <td align="right" width="80" title="Fin Fab Req-Fin IssueToCut"><? echo number_format($finish_left_over,2); ?></td>
                            <td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($plan_cut_qty,0); ?></td>
            				<td align="right" width="80" title="Cutting QC"><? echo number_format($cuttingQty,0); ?></td>
                            <td align="right" width="80" title="CuttingQC/PlanQty*100"><? if($cuttingQty>0) echo number_format($cuttingQty/$plan_cut_qty*100,3);else echo "0"; ?></td>
                            <td align="right" width="80"  title="Cutting-PlanQty"><? $cutting_excess_lessQty=$cuttingQty-$plan_cut_qty;echo number_format($cutting_excess_lessQty,0); ?></td>
                            <td align="right" width="80" title="Gmts Print issue Only"><? echo number_format($total_print_issued,0); ?></td>
                            <td align="right" width="80" title="CuttingQc-Print Issue total"><? $print_delv_balance=$cuttingQty-$total_print_issued;echo number_format($print_delv_balance,0); ?></td>
                            <td align="right" width="80" title="Gmt Print Recv"><? echo number_format($total_print_recv,0); ?></td>
                            <td align="right" width="100"><? echo number_format($print_recv_reject_qnty,0);?></td>
                            
                            <td align="right" width="80" title="Total Print Issue-Total Print Rcv"><? $print_yet_recv_qty=$total_print_issued-$total_print_recv; echo number_format($print_yet_recv_qty,0); ?></td>
                            <td align="right" width="80"><? echo number_format($total_sew_input,0); ?></td>
                            <td align="right" width="80" title="CuttingQc-Total Sew Input"><? $yet_sewing_input=$cuttingQty-$total_sew_input;echo number_format($yet_sewing_input,0);//sew_reject_qnty ?></td>

                            <td align="right" width="80"><? echo number_format($total_sew_recv,0); ?></td>
                            <td align="right" width="80"><? echo number_format($sew_reject_qnty,0); ?></td>
                            <td align="right" width="80" title="Tot Sew input-Total Sew-Sew Reject"><? $yet_sewing_output=$total_sew_input-($total_sew_recv+$sew_reject_qnty);echo number_format($yet_sewing_output,0); //echo number_format($finish_reject_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($total_gmts_finish_qnty,0); ?></td>
            
                            <td align="right" width="80">
                            
                            <? echo number_format($finish_reject_qnty); ?></td>
                            <td align="right" width="80" itle="Tot Sewing-Total Finish-Finish Reject"><? $yet_to_finish=$total_sew_recv-$total_gmts_finish_qnty-$finish_reject_qnty;echo number_format($yet_to_finish); ?></td>
                            <td align="right" width="80">
							<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo implode(',',array_unique(explode(",",$po_id))); ?>','550px')"><? echo  number_format($ex_factory_qty,0); ?></a>
							<? //echo number_format($exfactory_qnty); ?></td>
                            <td align="right" width="80" title="Total Finish-Exfac Qty"><? $left_over_finish_gmts=$total_gmts_finish_qnty-$ex_factory_qty;echo number_format($left_over_finish_gmts,0); ?></td>
                            <td align="right" width="80" title="Job Qty-Exfact Qty"><? $short_exfac_qty=$tot_po_qnty-$ex_factory_qty; echo number_format($short_exfac_qty,0); ?></td>
                            <td align="right" width="80" title="Sewing+Finish Reject"><? $total_reject=$finish_reject_qnty+$sew_reject_qnty; echo number_format($total_reject,0); ?></td>
                            <td align="right" width="80" title="Yet to Sew Input+Output+Finish+LeftOver Qty"><? $total_balance_qty=$yet_sewing_output+$yet_sewing_input+$yet_to_finish+$left_over_finish_gmts; echo number_format($total_balance_qty,0); ?></td>
                            <td align="right" width="" title="Ex-Fac Qty/CuttingQc*100"><? if($ex_factory_qty>0) $cut_to_ship_ratio=$ex_factory_qty/$cuttingQty*100;else $cut_to_ship_ratio=0;
							 echo number_format($cut_to_ship_ratio,0); ?></td>
						</tr>
						<?
						$tot_order_qty+=$tot_po_qnty;
						$tot_yarn_req_qty+=$yarn_required;
						$tot_yarn_issue_qty+=$total_issued;
						$tot_yarn_less_over_qty+=$yarn_less_over_qty;
						$total_grey_req_qty+=$grey_fabric_req_qnty;
						$tot_knit_prod_qty+=$total_knitting;
						$total_grey_rec_qty+=$tot_grey_rec_qty;
						$total_under_over_prod+=$under_over_prod;
						$total_knit_issuedToDyeQnty+=$grey_issuedToDyeQnty;
						$total_knit_left_over+=$grey_left_over;
						$tot_fin_req_qty+=$finish_fabric_req_qnty;
						$tot_total_finishing_prod+=$total_finishing_prod;
						$total_fin_fab_recv+=$fin_fab_recv;
						$tot_fin_less_over_prod+=$fin_less_over_prod;
						$total_issuedToCutQnty+=$issuedToCutQnty;
						$tot_finish_left_over+=$finish_left_over;
						
						$tot_tot_po_qnty+=$tot_po_qnty;
						$tot_tot_plan_qnty+=$plan_cut_qty;
						$tot_cuttingQty+=$cuttingQty;
						$tot_cutting_excess_lessQty+=$cutting_excess_lessQty;
						$tot_total_print_issued+=$total_print_issued;
						$tot_print_delv_balance+=$print_delv_balance;
						$tot_total_print_recv+=$total_print_recv;
						$tot_print_recv_reject_qnty+=$print_recv_reject_qnty;
						$tot_print_yet_recv_qty+=$print_yet_recv_qty;
						$tot_total_sew_input+=$total_sew_input; 
						$tot_yet_sewing_input+=$yet_sewing_input;
						$tot_total_sew_recv+=$total_sew_recv;
						$tot_sew_reject_qnty+=$sew_reject_qnty;
						$tot_yet_sewing_output+=$yet_sewing_output;
						$tot_total_gmts_finish_qnty+=$total_gmts_finish_qnty;
						$tot_finish_reject_qnty+=$finish_reject_qnty;
						$tot_yet_to_finish+=$yet_to_finish;
						$tot_ex_factory_qty+=$ex_factory_qty;
						$tot_left_over_finish_gmts+=$left_over_finish_gmts;
						$tot_short_exfac_qty+=$short_exfac_qty;
						$tot_total_reject+=$total_reject;
						$tot_total_balance_qty+=$total_balance_qty;
						$tot_cut_to_ship_ratio+=$cut_to_ship_ratio;
					
						$i++;
					}
					
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="100">Total :</td>   
                    <td width="50">&nbsp;</td>
                    <td width="80">&nbsp;</td> 
                    <td width="100">&nbsp;</td> 
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                    <td width="90" align="right" id="td_yarn_less_qty"><? echo number_format($tot_yarn_less_over_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($total_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_knit_qty"><? echo number_format($tot_knit_prod_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_recv_qty"><? echo number_format($total_grey_rec_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_undOver_qty"><? echo number_format($total_under_over_prod,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty"><? echo number_format($total_knit_issuedToDyeQnty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($total_knit_left_over,2); ?></td>
                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_total_finishing_prod,2); ?></td>
                    <td width="80" align="right" id="td_fin_prLoss_qty"><? echo number_format($total_fin_fab_recv,2); ?></td>
                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_less_over_prod,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($total_issuedToCutQnty,2); ?></td>
                    <td width="80" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_finish_left_over,2); ?></td> 
                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_tot_plan_qnty); ?></td>
                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cuttingQty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_cutting_excess_qty"><? echo number_format($tot_cutting_excess_lessQty); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty"><? echo number_format($tot_total_print_issued); ?></td>
                    <td width="80" align="right" id="td_print_delv_balance_qty"><? echo number_format($tot_print_delv_balance); ?></td>
                    <td width="80" align="right" id="td_print_recv_qty"><? echo number_format($tot_total_print_recv); ?></td>
                   	<td width="100" align="right" id="td_print_recv_reject_qty"><? echo number_format($tot_print_recv_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_ptint_yet_recv_qty"><? echo number_format($tot_print_yet_recv_qty); ?></td>
                    <td width="80" align="right" id="td_sewInput_qty"><? echo number_format($tot_total_sew_input); ?></td>
                    <td width="80" align="right" id="td_Yettosew_qty"><? echo number_format($tot_yet_sewing_input); ?></td>
                    <td width="80" align="right" id="td_sewingRecv_qty"><? echo number_format($tot_total_sew_recv); ?></td>
                    <td width="80" align="right" id="td_sew_reject_qty"><? echo number_format($tot_sew_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_yetSewOut_qty"><? echo number_format($tot_yet_sewing_output); ?></td>                    
                    <td width="80" align="right" id="td_total_finish_qty"><? echo number_format($tot_total_gmts_finish_qnty); ?></td>
                    <td width="80" align="right" id="td_fin_reject_qty"><? echo number_format($tot_finish_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_tot_yet_to_finish_qty"><? echo number_format($tot_yet_to_finish); ?></td>
                    <td width="80" align="right" id="td_tot_ex_factory_qty"><? echo number_format($tot_ex_factory_qty); ?></td>
                    <td width="80" align="right" id="td_tot_left_over_finish_gmts"><? echo number_format($tot_left_over_finish_gmts); ?></td>
                    <td width="80" align="right" id="td_tot_short"><? echo number_format($tot_short_exfac_qty); ?></td>
                    <td width="80" align="right" id="td_tot_reject_qty"><? echo number_format($tot_total_reject); ?></td>
                    <td align="right" width="80" id="td_tot_total_balance"><? echo number_format($tot_total_balance_qty); ?></td>
                    <td align="right" width="" id="td_cutToShip_qty"><? echo number_format($tot_cut_to_ship_ratio); ?></td>
                </tr>
           </table>
           <?
		} //Both part End
		else if($cbo_search_type==1) //Textile part Start
		{
			?>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                    <tr>
                    	<th colspan="7">Style Information</th>
                     	<th colspan="15"> Textile Part</th>
                    </tr>
                    <tr style="font-size:13px">
                       	<th width="40">SL</th>
                       	<th width="110">Buyer</th>
                       	<th width="100">Style No</th>
                       	<th width="50">Job No</th>
                       	<th width="80">Season</th>
                       	<th width="100">Order No</th>
                       	<th width="80">Job Qty.<br/> (Pcs)</th>
                       	<th width="80">Yarn Req.<br/><font style="font-size:9px;color: red; font-weight:100">(As Per Pre-Cost)</font></th>
                       	<th width="80">Yarn Total <br/>Issued</th>
                       	<th width="90">Yarn Less or Over Issued</th>
                       	<th width="80">Knit. Gray <br/>Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>                        
                       	<th width="80">Knit. Total <br/>Prod.</th>                        
                       	<th width="80">Grey.  Receive</th>
                       	<th width="80">Knit. Under or Over Prod.</th>
                       	<th width="80">Knit. Issued <br/>To Dyeing</th>
                       	<th width="80">Knit. Left <br/>Over</th>
                       	<th width="80">Fin Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>                        
                       	<th width="80">Fin Prod. <br/>Total</th>
                       	<th width="80">Fin. Fab. Receive</th>
                       	<th width="80">Fin Less or Over</th>
                       	<th width="80">Fin Issue <br/>To Cut</th>
                       	<th width="">Fin Left<br/>Over</th>
                    </tr>
                </thead>
           	</table>
            <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
                <?
					$i=1;$total_grey_rec_qty=$total_grey_req_qty=$tot_yarn_less_over_qty=$tot_knit_prod_qty=$total_grey_rec_qty=$total_under_over_prod=$total_knit_issuedToDyeQnty=$total_knit_left_over=$tot_fin_req_qty=$tot_total_finishing_prod=$total_fin_fab_recv=$tot_fin_less_over_prod=$total_issuedToCutQnty=$tot_finish_left_over=$tot_tot_po_qnty=$tot_cuttingQty=$tot_cutting_excess_lessQty=$tot_total_print_issued=$tot_print_delv_balance=$tot_total_print_recv=$tot_print_recv_reject_qnty=$tot_print_yet_recv_qty=$tot_total_sew_input=$tot_total_sew_recv=$tot_yet_sewing_output=$tot_sew_reject_qnty=$tot_total_gmts_finish_qnty=$tot_finish_reject_qnty=$tot_yet_to_finish=$tot_ex_factory_qty=$tot_left_over_finish_gmts=$tot_short_exfac_qty=$tot_total_reject=$tot_total_balance_qty=$tot_cut_to_ship_ratio=$tot_finish_fabric_req_qnty=0;
					
					foreach($result_data_arr as $job_no=>$val)
					{
						$ratio=$val["ratio"];
						$po_id=rtrim($val["po_id"],',');
						$po_ids=array_unique(explode(",",$po_id));
						$yarn_required=$yarn_req_job=$tot_grey_req_qty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$grey_fabric_req_qnty=$grey_fabric_aop_req_wo_qnty=$aop_delivery_qty=$aop_aop_recv_qnty=$knit_qnty_in=$knit_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_recv_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_reject_qnty=$total_sew_recv=$total_gmts_finish_qnty=$finish_reject_qnty=$ex_factory_qty=$knit_gray_rec_inside=$knit_gray_rec_outside=$transfer_in_qnty_rec_knit=$transfer_out_qnty_knit=$transfer_out_qnty_rec_knit=$issuedToDyeQnty_in=$issuedToDyeQnty_out=$issuedToCutQnty=$finish_fabric_req_qnty=$finish_qnty_in=$finish_qnty_out=$finish_qnty_in_rec_gmt=$finish_qnty_out_rec_gmt=$transfer_in_qnty_finish=$transfer_out_qnty_finish=$gmt_finish_in=$gmt_finish_out=$issue_ret_qnty=$rec_trns_qnty=0;
						foreach($po_ids as $pId)
						{
							$yarn_required+=$yarn_qty_arr[$pId];  
							$yarn_req_job+=$yarn_qty_arr[$pId];
							//$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							$yarn_issue_inside+=$yarn_issue_arr[$pId]["issue_qnty_in"]-$yarn_issue_rtn_arr[$pId]["return_qnty_in"];
							$yarn_issue_outside+=$yarn_issue_arr[$pId]["issue_qnty_out"]-$yarn_issue_rtn_arr[$pId]["return_qnty_out"];
							$transfer_in_qnty_yarn+=$transfer_data_arr[$pId]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn+=$transfer_data_arr[$pId]["transfer_out_qnty_yarn"];
							$grey_fabric_req_qnty+=$booking_req_arr[$pId]['gray'];
							$grey_fabric_aop_req_wo_qnty+=$booking_req_arr[$pId]['aop_wo_qnty'];
							$aop_delivery_qty+=$aop_delivery_array[$pId]['batch_issue_qty'];
							$aop_aop_recv_qnty+=$aop_delivery_array[$pId]['aop_recv_qnty'];
							
							$knit_qnty_in+=$kniting_prod_arr[$pId]["knit_qnty_in"];
							$knit_qnty_out+=$kniting_prod_arr[$pId]["knit_qnty_out"];
							
							$knit_gray_rec_inside+=$kniting_prod_arr[$pId]["knit_qnty_rec_inside"];
							$knit_gray_rec_outside+=$kniting_prod_arr[$pId]["knit_qnty_rec_outside"];
							$transfer_in_qnty_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_knit"];
							$transfer_in_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_rec_knit"];
							$issuedToDyeQnty_in+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_out"];
							//$issuedToCutQnty+=$grey_cut_issue_arr[$pId]["issue_to_cut_qnty"];
							$issuedToCutQnty+=$grey_cut_issue_arr[$pId]["issue_to_cut_qnty"]+$transfer_data_arr[$pId]["issue_trns_qnty"];
							$finish_fabric_req_qnty+=$booking_req_arr[$pId]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							$finish_qnty_in+=$finish_prod_arr[$pId]["finish_qnty_in"];
							$finish_qnty_out+=$finish_prod_arr[$pId]["finish_qnty_out"];
							$finish_qnty_in_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_out_rec_gmt"];
							$transfer_in_qnty_finish+=$transfer_data_arr[$pId]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish+=$transfer_data_arr[$pId]["transfer_out_qnty_finish"];
							
							$issue_ret_qnty+=$transfer_data_arr[$pId]["issue_ret_qnty"];
							//$rec_ret_qnty+=$transfer_data_arr[$pId]["rec_ret_qnty"];
							$rec_trns_qnty+=$transfer_data_arr[$pId]["rec_trns_qnty"];
							
							$print_recv_qnty_in+=$garment_prod_data_arr[$pId]['print_recv_qnty_in'];
							$print_recv_qnty_out+=$garment_prod_data_arr[$pId]['print_recv_qnty_out'];
							$print_recv_reject_qnty+=$garment_prod_data_arr[$pId]['print_reject_qnty'];
							$sew_input_qnty_in+=$garment_prod_data_arr[$pId]['sew_input_qnty_in'];
							$sew_input_qnty_out+=$garment_prod_data_arr[$pId]['sew_input_qnty_out'];
							$sew_reject_qnty+=$garment_prod_data_arr[$pId]['sew_reject_qnty'];
							
							$sew_recv_qnty_in=$garment_prod_data_arr[$pId]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$pId]['sew_recv_qnty_out'];
							$total_sew_recv+=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							
							$gmt_finish_in=$garment_prod_data_arr[$pId]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$pId]['finish_qnty_out'];
							$total_gmts_finish_qnty+=$gmt_finish_in+$gmt_finish_out;
							$finish_reject_qnty+=$garment_prod_data_arr[$pId]['finish_reject_qnty'];
						}
						$total_knitting=$knit_qnty_in+$knit_qnty_out;
						$fin_fab_recv=$finish_qnty_in+$finish_qnty_out+$issue_ret_qnty;
						$grey_issuedToDyeQnty=$issuedToDyeQnty_in+$issuedToDyeQnty_out;
						
						$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
						$tot_po_qnty=$val["po_qnty"];
						//$exfactory_qnty=$val["ex_factory_qnty"]-$ex_factory_qty;
						$plan_cut_qty=$val["plan_cut"];
						//$job_no=$val["job_no"];
						$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
						$under_over_issued=$yarn_required-$total_issued;
						
						$tot_grey_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;
						$grey_left_over=$tot_grey_rec_qty-$grey_issuedToDyeQnty;
						$total_finishing_prod=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;
						$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);
						$finish_left_over=$finish_fabric_req_qnty-$issuedToCutQnty;
						$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
						$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
						$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
						$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
						$fin_less_over_prod=$finish_fabric_req_qnty-$total_finishing_prod;
						?>
						<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
			 				<td width="40"><? echo $i; ?></td>
                            <td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                            <td width="100"><p><? echo $val["style_ref_no"]; ?></p></td>
                            <td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?></p></td> 
                            <td width="80" align="center"><p><? echo $season_name_library[$val["season"]]; ?></p></td>
                            <td width="100"><a href="javascript:open_po_popup('<? echo implode(', ',array_unique(explode(",",$po_id)));?>','PO Info','po_popup')">View</a></td>
                            <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty,0); ?></p></td>
                            <td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
                            <td align="right" width="90" title="YarnReq-YarnIssueQty"><? $yarn_less_over_qty=$yarn_required-$total_issued;echo number_format($yarn_less_over_qty,2); ?></td>
                            <td align="right" width="80"><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Booking Info','booking_popup',1)"><? echo number_format($grey_fabric_req_qnty,2); ?></a></td>
                            <td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
                            <td align="right" width="80"><? echo number_format($tot_grey_rec_qty,2); ?></td>
                            <td align="right" width="80" title="Knit Grey Fab Req-Total Prod Qty"><? echo number_format($under_over_prod,2); ?></td>
                             <td align="right" width="80"><? echo number_format($grey_issuedToDyeQnty,2); ?></td>
                            <td align="right" width="80" title="Grey Recv-Grey issuetoDye"><? echo number_format($grey_left_over,2); ?></td>
                            <td align="right" width="80"><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Booking Info','booking_popup',2)"><? echo number_format($finish_fabric_req_qnty,2); ?></a><? //echo number_format($finish_fabric_req_qnty,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_finishing_prod,2); ?></td>
                            <td align="right" width="80" title="Knit Fin Fab Recv"><? echo number_format($fin_fab_recv,2); ?></td>
                            <td align="right" width="80" title="Fin Fab Req-Fin Fab Total"><? echo number_format($fin_less_over_prod,2); ?></td>
                            <td align="right" width="80" title="Knit Fin Fab Issue"><? echo number_format($issuedToCutQnty,2); ?></td>
                            <td align="right" width="" title="Fin Fab Req-Fin IssueToCut"><? echo number_format($finish_left_over,2); ?></td>
						</tr>
						<?
						$tot_order_qty+=$tot_po_qnty;
						$tot_yarn_req_qty+=$yarn_required;
					
						$tot_yarn_issue_qty+=$total_issued;
						$tot_yarn_less_over_qty+=$yarn_less_over_qty;
						$total_grey_req_qty+=$grey_fabric_req_qnty;
						$tot_knit_prod_qty+=$total_knitting;
						$total_grey_rec_qty+=$tot_grey_rec_qty;
						$total_under_over_prod+=$under_over_prod;
						
						$total_knit_issuedToDyeQnty+=$grey_issuedToDyeQnty;
						$total_knit_left_over+=$grey_left_over;
						$tot_fin_req_qty+=$finish_fabric_req_qnty;
						$tot_total_finishing_prod+=$total_finishing_prod;
						$total_fin_fab_recv+=$fin_fab_recv;
						$tot_fin_less_over_prod+=$fin_less_over_prod;
						$total_issuedToCutQnty+=$issuedToCutQnty;
						$tot_finish_left_over+=$finish_left_over;
						$tot_tot_po_qnty+=$tot_po_qnty;
						$tot_cuttingQty+=$cuttingQty;
						$i++;
					}
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="100">Total:</td>   
                 
                    <td width="50">&nbsp;</td>
                     <td width="80">&nbsp;</td>
                    <td width="100">&nbsp;</td> 
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                    <td width="90" align="right" id="td_yarn_less_qty"><? echo number_format($tot_yarn_less_over_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($total_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_knit_qty"><? echo number_format($tot_knit_prod_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_recv_qty"><? echo number_format($total_grey_rec_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_undOver_qty"><? echo number_format($total_under_over_prod,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty"><? echo number_format($total_knit_issuedToDyeQnty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($total_knit_left_over,2); ?></td>
                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_total_finishing_prod,2); ?></td>
                    <td width="80" align="right" id="td_fin_prLoss_qty"><? echo number_format($total_fin_fab_recv,2); ?></td>
                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_less_over_prod,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($total_issuedToCutQnty,2); ?></td>
                    <td width="" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_finish_left_over,2); ?></td> 
                </tr>
           	</table>
           	<?
		} //Textile part End
		else   if($cbo_search_type==2) //Gmt Start here
		{
			?>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                    <tr>
                    	<th colspan="7">Style Information</th>
                     	<th colspan="24">Garment Part</th>
                    </tr>
                    <tr style="font-size:13px">
                       	<th width="40">SL</th>
                       	<th width="110">Buyer</th>
                       	<th width="100">Style No</th>
                       	<th width="50">Job No</th> 
                       	<th width="80">Season</th>
                       	<th width="100">Order No</th>
                       	<th width="80">Job Qty.<br/> (Pcs)</th>
                        
                        <th width="80" title="Gmt Part Start from">Plan Qty(Pcs).</th>
                        <th width="80">Cutting Qty</th>                        
                        <th width="80">Cutting%</th>                        
                        <th width="80">Cutting  Excess or Less Qty</th>
                        <th width="80">Gmts. Total Print Issued</th> 
                        <th width="80">Print.Delv. Balance</th>
                        <th width="80">Print. Rcv.</th>
                        <th width="100">Print. Reject</th>  
                        <th width="80">Prnt Yet to Rcv.</th>
                        <th width="80">Total Sew. Input</th>
                        <th width="80">Yet to Sew. Input</th>
                        <th width="80">Total  Sewing</th>
                        <th width="80">Sew Out Reject</th>
                        <th width="80">Yet to Sew Out</th>
                        <th width="80">Total Finish</th>
                        <th width="80">Finish Reject</th>
                        <th width="80">Yet to Finish</th>
                        <th width="80">Ex-Factory</th>
                        <th width="80">Left Over</th>
                        <th width="80">Short Ex-Fac. Qty</th>                        
                        <th width="80">Total Reject</th>
                        <th width="80">Total Balance</th>
                        <th width="">Cut TO Ship Ratio</th>
                    </tr>
                </thead>
           	</table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
	            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
	                <?
					$i=1;$tot_grey_rec_qty=$tot_grey_req_qty=$tot_yarn_less_over_qty=$tot_knit_prod_qty=$total_grey_rec_qty=$total_under_over_prod=$total_knit_issuedToDyeQnty=$total_knit_left_over=$tot_fin_req_qty=$tot_total_finishing_prod=$total_fin_fab_recv=$tot_fin_less_over_prod=$total_issuedToCutQnty=$tot_finish_left_over=$tot_tot_po_qnty=$tot_cuttingQty=$tot_cutting_excess_lessQty=$tot_total_print_issued=$tot_print_delv_balance=$tot_total_print_recv=$tot_print_recv_reject_qnty=$tot_print_yet_recv_qty=$tot_total_sew_input=$tot_total_sew_recv=$tot_yet_sewing_output=$tot_sew_reject_qnty=$tot_total_gmts_finish_qnty=$tot_finish_reject_qnty=$tot_yet_to_finish=$tot_ex_factory_qty=$tot_left_over_finish_gmts=$tot_short_exfac_qty=$tot_total_reject=$tot_total_balance_qty=$tot_cut_to_ship_ratio=$tot_finish_fabric_req_qnty=$tot_plan_qty=0;
						
					foreach($result_data_arr as $job_no=>$val)
					{
						$ratio=$val["ratio"];
						$po_id=rtrim($val["po_id"],',');
						$po_ids=array_unique(explode(",",$po_id));
						$yarn_required=$yarn_req_job=$tot_grey_req_qty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$grey_fabric_req_qnty=$grey_fabric_aop_req_wo_qnty=$aop_delivery_qty=$aop_aop_recv_qnty=$knit_qnty_in=$knit_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_recv_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_reject_qnty=$total_sew_recv=$total_gmts_finish_qnty=$finish_reject_qnty=$ex_factory_qty=$cuttingQty=$print_issue_qnty_in=$print_issue_qnty_out=$knit_gray_rec_inside=$knit_gray_rec_outside=$transfer_in_qnty_knit=$transfer_out_qnty_knit=$transfer_in_qnty_rec_knit=$transfer_out_qnty_rec_knit=$issuedToDyeQnty_in=$issuedToDyeQnty_out=$issuedToCutQnty=$finish_fabric_req_qnty=0;
						foreach($po_ids as $pId)
						{
							$yarn_required+=$yarn_qty_arr[$pId];
							$yarn_req_job+=$yarn_qty_arr[$pId];
							//$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							$yarn_issue_inside+=$yarn_issue_arr[$pId]["issue_qnty_in"]-$yarn_issue_rtn_arr[$pId]["return_qnty_in"];
							$yarn_issue_outside+=$yarn_issue_arr[$pId]["issue_qnty_out"]-$yarn_issue_rtn_arr[$pId]["return_qnty_out"];
							$transfer_in_qnty_yarn+=$transfer_data_arr[$pId]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn+=$transfer_data_arr[$pId]["transfer_out_qnty_yarn"];
							$grey_fabric_req_qnty+=$booking_req_arr[$pId]['gray'];
							$grey_fabric_aop_req_wo_qnty+=$booking_req_arr[$pId]['aop_wo_qnty'];
							$aop_delivery_qty+=$aop_delivery_array[$pId]['batch_issue_qty'];
							$aop_aop_recv_qnty+=$aop_delivery_array[$pId]['aop_recv_qnty'];
							
							$knit_qnty_in+=$kniting_prod_arr[$pId]["knit_qnty_in"];
							$knit_qnty_out+=$kniting_prod_arr[$pId]["knit_qnty_out"];
							
							$knit_gray_rec_inside+=$kniting_prod_arr[$pId]["knit_qnty_rec_inside"];
							$knit_gray_rec_outside+=$kniting_prod_arr[$pId]["knit_qnty_rec_outside"];
							$transfer_in_qnty_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_knit"];
							
							$transfer_in_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_rec_knit"];
							$issuedToDyeQnty_in+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_out"];
							$issuedToCutQnty+=$grey_cut_issue_arr[$pId]["issue_to_cut_qnty"];
							$finish_fabric_req_qnty+=$booking_req_arr[$pId]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							
							$finish_qnty_in+=$finish_prod_arr[$pId]["finish_qnty_in"];
							$finish_qnty_out+=$finish_prod_arr[$pId]["finish_qnty_out"];
							$finish_qnty_in_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_out_rec_gmt"];
							$transfer_in_qnty_finish+=$transfer_data_arr[$pId]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish+=$transfer_data_arr[$pId]["transfer_out_qnty_finish"];
							
					
							$print_issue_qnty_in+=$garment_prod_data_arr[$pId]['print_issue_qnty_in'];
							$print_issue_qnty_out+=$garment_prod_data_arr[$pId]['print_issue_qnty_out'];
							$print_recv_qnty_in+=$garment_prod_data_arr[$pId]['print_recv_qnty_in'];
							$print_recv_qnty_out+=$garment_prod_data_arr[$pId]['print_recv_qnty_out'];
							$print_recv_reject_qnty+=$garment_prod_data_arr[$pId]['print_reject_qnty'];
							$sew_input_qnty_in+=$garment_prod_data_arr[$pId]['sew_input_qnty_in'];
							$sew_input_qnty_out+=$garment_prod_data_arr[$pId]['sew_input_qnty_out'];
							$sew_reject_qnty+=$garment_prod_data_arr[$pId]['sew_reject_qnty'];
							
							$sew_recv_qnty_in=$garment_prod_data_arr[$pId]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$pId]['sew_recv_qnty_out'];
							$total_sew_recv+=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							
							$gmt_finish_in=$garment_prod_data_arr[$pId]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$pId]['finish_qnty_out'];
							$total_gmts_finish_qnty+=$gmt_finish_in+$gmt_finish_out;
							$finish_reject_qnty+=$garment_prod_data_arr[$pId]['finish_reject_qnty'];
							
						
							$cuttingQty+=$garment_prod_data_arr[$pId]['cutting_qnty'];
						}
						$grey_issuedToDyeQnty=$issuedToDyeQnty_in+$issuedToDyeQnty_out;
						
						$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
						$tot_po_qnty=$val["po_qnty"];
						$plan_cut_qty=$val["plan_cut"];
						$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
						$under_over_issued=$yarn_required-$total_issued;

						$fin_less_over_prod=$grey_fabric_req_qnty-$total_finishing_prod;
						$tot_grey_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;
						
						$grey_left_over=$tot_grey_rec_qty-$grey_issuedToDyeQnty;
						
						
						$total_finishing_prod=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;
						
						$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);
						$finish_left_over=$finish_fabric_req_qnty-$issuedToCutQnty;
						
						$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
						$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
						$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
						?>
						<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">

			 				<td width="40"><? echo $i; ?></td>
                            <td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                            <td width="100"><p><? echo $val["style_ref_no"]; ?></p></td>
                            <td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><p><? echo $season_name_library[$val["season"]]; ?>&nbsp;</p></td>
                            <td width="100" align="center"><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','PO Info','po_popup')">View</a></td>                 <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty,0); ?></p></td>
                           
                            <td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($plan_cut_qty,0); ?></td>
            				<td align="right" width="80" title="Cutting QC"><? echo number_format($cuttingQty,0); ?></td>
                            <td align="right" width="80" title="CuttingQC/PlanQty*100"><? if($cuttingQty>0) echo number_format($cuttingQty/$plan_cut_qty*100);else echo "0"; ?></td>
                            <td align="right" width="80"  title="Cutting-PlanQty"><? $cutting_excess_lessQty=$cuttingQty-$plan_cut_qty;echo number_format($cutting_excess_lessQty); ?></td>
                            <td align="right" width="80" title="Gmts Print issue Only"><? echo number_format($total_print_issued); ?></td>
                            <td align="right" width="80" title="CuttingQc-Print Issue total"><? $print_delv_balance=$cuttingQty-$total_print_issued;echo number_format($print_delv_balance,0); ?></td>
                            <td align="right" width="80" title="Gmt Print Recv"><? echo number_format($total_print_recv); ?></td>
                            <td align="right" width="100"><? echo $print_recv_reject_qnty;?></td>
                            <td align="right" width="80" title="Total Print Issue-Total Print Rcv"><? $print_yet_recv_qty=$total_print_issued-$total_print_recv; echo number_format($print_yet_recv_qty,0); ?></td>
                            <td align="right" width="80"><? echo number_format($total_sew_input); ?></td>
                            <td align="right" width="80" title="CuttingQc-Total Sew Input"><? $yet_sewing_input=$cuttingQty-$total_sew_input;echo number_format($yet_sewing_input);//sew_reject_qnty ?></td>

                            <td align="right" width="80"><? echo number_format($total_sew_recv); //number_format($total_gmts_finish_qnty);?></td>
                            <td align="right" width="80"><? echo number_format($sew_reject_qnty);//$finish_balance_qnty ?></td>
                            <td align="right" width="80" title="Tot Sew input-Total Sew-Sew Reject"><? $yet_sewing_output=$total_sew_input-($total_sew_recv+$sew_reject_qnty);echo number_format($yet_sewing_output,0); //echo number_format($finish_reject_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($total_gmts_finish_qnty,0); ?></td>
                            <td align="right" width="80">
                            <? echo number_format($finish_reject_qnty); ?></td>
                            <td align="right" width="80" itle="Tot Sewing-Total Finish-Finish Reject"><? $yet_to_finish=$total_sew_recv-$total_gmts_finish_qnty-$finish_reject_qnty;echo number_format($yet_to_finish,0); ?></td>
                            <td align="right" width="80">
							<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo implode(',',array_unique(explode(",",$po_id))); ?>','550px')"><? echo  number_format($ex_factory_qty); ?></a>
							<? //echo number_format($exfactory_qnty); ?></td>
                            <td align="right" width="80" title="Total Finish-Exfac Qty"><? $left_over_finish_gmts=$total_gmts_finish_qnty-$ex_factory_qty;echo number_format($left_over_finish_gmts); ?></td>
                            <td align="right" width="80" title="Job Qty-Exfact Qty"><? $short_exfac_qty=$tot_po_qnty-$ex_factory_qty; echo number_format($short_exfac_qty); ?></td>
                            <td align="right" width="80" title="Sewing+Finish Reject"><? $total_reject=$finish_reject_qnty+$sew_reject_qnty; echo number_format($total_reject); ?></td>
                            <td align="right" width="80" title="Yet to Sew Input+Output+Finish+LeftOver Qty"><? $total_balance_qty=$yet_sewing_output+$yet_sewing_input+$yet_to_finish+$left_over_finish_gmts; echo number_format($total_balance_qty); ?></td>
                            <td align="right" width="" title="Ex-Fac Qty/CuttingQc*100"><? if($ex_factory_qty>0) $cut_to_ship_ratio=$ex_factory_qty/$cuttingQty*100;else $cut_to_ship_ratio=0;
							echo number_format($cut_to_ship_ratio,0); ?></td>
						</tr>
						<?
						$tot_order_qty+=$tot_po_qnty;
						$tot_plan_qty+=$plan_cut_qty;
						$tot_yarn_req_qty+=$yarn_required;
						/*$tot_yarn_issue_qty+=$total_issued;
						$tot_yarn_less_over_qty+=$yarn_less_over_qty;
						$tot_grey_req_qty+=$grey_fabric_req_qnty;
						$tot_knit_prod_qty+=$total_knitting;
						$total_grey_rec_qty+=$tot_grey_rec_qty;
						$total_under_over_prod+=$under_over_prod;
						
						$total_knit_issuedToDyeQnty+=$grey_issuedToDyeQnty;
						$total_knit_left_over+=$grey_left_over;
						$tot_fin_req_qty+=$finish_fabric_req_qnty;
						$tot_total_finishing_prod+=$total_finishing_prod;
						$total_fin_fab_recv+=$fin_fab_recv;
						$tot_fin_less_over_prod+=$fin_less_over_prod;
						$total_issuedToCutQnty+=$issuedToCutQnty;
						$tot_finish_left_over+=$finish_left_over;*/
						$tot_tot_po_qnty+=$tot_po_qnty;
						$tot_cuttingQty+=$cuttingQty;
						$tot_cutting_excess_lessQty+=$cutting_excess_lessQty;
						$tot_total_print_issued+=$total_print_issued;
						$tot_print_delv_balance+=$print_delv_balance;
						$tot_total_print_recv+=$total_print_recv;
						$tot_print_recv_reject_qnty+=$print_recv_reject_qnty;
						$tot_print_yet_recv_qty+=$print_yet_recv_qty;
						$tot_total_sew_input+=$total_sew_input; 
						$tot_yet_sewing_input+=$yet_sewing_input;
						$tot_total_sew_recv+=$total_sew_recv;
						$tot_sew_reject_qnty+=$sew_reject_qnty;
						$tot_yet_sewing_output+=$yet_sewing_output;
						$tot_total_gmts_finish_qnty+=$total_gmts_finish_qnty;
						$tot_finish_reject_qnty+=$finish_reject_qnty;
						$tot_yet_to_finish+=$yet_to_finish;
						
						$tot_ex_factory_qty+=$ex_factory_qty;
						$tot_left_over_finish_gmts+=$left_over_finish_gmts;
						
						$tot_short_exfac_qty+=$short_exfac_qty;
						$tot_total_reject+=$total_reject;
						$tot_total_balance_qty+=$total_balance_qty;
						$tot_cut_to_ship_ratio+=$cut_to_ship_ratio;
					
						$i++;
					}
					?>
	            </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="100">Total :</td>   
                    <td width="50">&nbsp;</td> 
                    <td width="80">&nbsp;</td>
                    <td width="100">&nbsp;</td> 
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_plan_qty); ?></td>
                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cuttingQty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_cutting_excess_qty"><? echo number_format($tot_cutting_excess_lessQty); ?></td>
                    <td width="80" align="right" id="td_printIssue_qty"><? echo number_format($tot_total_print_issued); ?></td>
                    <td width="80" align="right" id="td_print_delv_balance_qty"><? echo number_format($tot_print_delv_balance); ?></td>
                    <td width="80" align="right" id="td_print_recv_qty"><? echo number_format($tot_total_print_recv); ?></td>
                   	<td width="100" align="right" id="td_print_recv_reject_qty"><? echo number_format($tot_print_recv_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_ptint_yet_recv_qty"><? echo number_format($tot_print_yet_recv_qty); ?></td>
                    <td width="80" align="right" id="td_sewInput_qty"><? echo number_format($tot_total_sew_input); ?></td>
                    <td width="80" align="right" id="td_Yettosew_qty"><? echo number_format($tot_yet_sewing_input); ?></td>
                    <td width="80" align="right" id="td_sewingRecv_qty"><? echo number_format($tot_total_sew_recv); ?></td>
                    <td width="80" align="right" id="td_sew_reject_qty"><? echo number_format($tot_sew_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_yetSewOut_qty"><? echo number_format($tot_yet_sewing_output); ?></td>                    
                    <td width="80" align="right" id="td_total_finish_qty"><? echo number_format($tot_total_gmts_finish_qnty); ?></td>
                    <td width="80" align="right" id="td_fin_reject_qty"><? echo number_format($tot_finish_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_tot_yet_to_finish_qty"><? echo number_format($tot_yet_to_finish); ?></td>
                    <td width="80" align="right" id="td_tot_ex_factory_qty"><? echo number_format($tot_ex_factory_qty); ?></td>
                    <td width="80" align="right" id="td_tot_left_over_finish_gmts"><? echo number_format($tot_left_over_finish_gmts); ?></td>
                    <td width="80" align="right" id="td_tot_short"><? echo number_format($tot_short_exfac_qty); ?></td>
                    <td width="80" align="right" id="td_tot_reject_qty"><? echo number_format($tot_total_reject); ?></td>
                    <td align="right" width="80" id="td_tot_total_balance"><? echo number_format($tot_total_balance_qty); ?></td>
                    <td align="right" width="" id="td_cutToShip_qty"><? echo number_format($tot_cut_to_ship_ratio); ?></td>
                </tr>
           	</table>
           	<?
		} //Gmt part End
	   	?>
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
    echo "$html****$filename****$cbo_search_type****$type";
    exit();
}

if($action=='summary_report_generate') // Summary
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$cbo_date_cat=str_replace("'","",$cbo_date_cat);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$txt_file_no=str_replace("'","",$txt_file_no);
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_style=str_replace("'","",$txt_style);


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
	$ship_date_cond="";
	if($cbo_date_cat==1)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
		}
	}
	else if($cbo_date_cat==3)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and c.EX_FACTORY_DATE between '$txt_date_from' and '$txt_date_to'  and c.PO_BREAK_DOWN_ID=b.id ";
			$table_c=", PRO_EX_FACTORY_MST c";
		}
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$ship_date_cond="and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
		}
	}

	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($style_ref_id)!="") $job_no_cond="and a.id  in($style_ref_id)";
	
	if(trim($txt_file_no)!="") $file_no_cond="and b.file_no in($txt_file_no)";
	if($cbo_season_id>0) $season_cond="and a.season_buyer_wise  in($cbo_season_id)";
	
	if(trim($txt_style_ref)!="") $job_no_cond.="and a.job_no_prefix_num  in($txt_style_ref)";
	
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	
		
	if(trim($txt_style)!="") $job_no_cond.="and a.style_ref_no like('%$txt_style%')";
		
	$sql_po="select a.buyer_name, a.job_no, a.season_buyer_wise,a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date
	from wo_po_details_master a, wo_po_break_down b $table_c
	where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $season_cond $file_no_cond and b.shiping_status=3 
	group by a.buyer_name, a.job_no, a.season_buyer_wise,a.job_no_prefix_num, to_char(a.insert_date,'YYYY') , a.style_ref_no, a.total_set_qnty , b.id , b.file_no, b.po_number, b.po_quantity , b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date
	order by a.job_no, b.pub_shipment_date, b.id";//
   	//echo  $sql_po;  
	$sql_po_result=sql_select($sql_po);
	
	//Full ship check start............................................................
	$selectedJobArr=array();
	foreach($sql_po_result as $row)
	{
		$selectedJobArr[$row[csf("job_no")]]=$row[csf("job_no")];
	}
	
	
	$poSql="select JOB_NO_MST from wo_po_break_down where shiping_status <>3 and STATUS_ACTIVE=1 and IS_DELETED=0 ";//
	$p=1;
	$job_chunk_arr=array_chunk($selectedJobArr,999);
	foreach($job_chunk_arr as $jobArr)
	{
		if($p==1){$poSql .=" and (JOB_NO_MST in('".implode("','",$jobArr)."')";} 
		else{$poSql .=" or JOB_NO_MST in('".implode("','",$jobArr)."')";}
		$p++;
	}
	$poSql .=")";
	//echo $poSql;
	$poSqlResult=sql_select($poSql);
	$noFullShipJobArr=array();
	foreach($poSqlResult as $row)
	{
		$noFullShipJobArr[$row[JOB_NO_MST]]=1;
	}
	//Full ship check end............................................................
	
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();
	foreach($sql_po_result as $row)
	{
		if($noFullShipJobArr[$row[csf("job_no")]]!=1)
		{
			if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
			$result_data_arr[$row[csf("buyer_name")]]["po_id"].=$row[csf("po_id")].',';
			$result_data_arr[$row[csf("buyer_name")]]["season"]=$row[csf("season_buyer_wise")];
			$result_data_arr[$row[csf("buyer_name")]]["file_no"].=$row[csf("file_no")].',';
			$result_data_arr[$row[csf("buyer_name")]]["buyer_name"]=$row[csf("buyer_name")];
			$result_data_arr[$row[csf("buyer_name")]]["job_no"]=$row[csf("job_no")];
			$result_data_arr[$row[csf("buyer_name")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$result_data_arr[$row[csf("buyer_name")]]["job_year"]=$row[csf("job_year")];
			$result_data_arr[$row[csf("buyer_name")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$result_data_arr[$row[csf("buyer_name")]]["ratio"]=$row[csf("ratio")];
			$result_data_arr[$row[csf("buyer_name")]]["ref_no"]=$row[csf("grouping")];
			$result_data_arr[$row[csf("buyer_name")]]["file_no"]=$row[csf("file_no")];
			$result_data_arr[$row[csf("buyer_name")]]["po_number"]=$row[csf("po_number")];
			$result_data_arr[$row[csf("buyer_name")]]["po_qnty"]+=$row[csf("po_qnty")]*$row[csf("ratio")];
			$result_data_arr[$row[csf("buyer_name")]]["plan_cut"]+=$row[csf("plan_cut")]*$row[csf("ratio")];
			$result_data_arr[$row[csf("buyer_name")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
			//$result_data_arr[$row[csf("job_no")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
			//$result_data_arr[$row[csf("job_no")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
			$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
			$JobArr[]="'".$row[csf('job_no')]."'";
			$job_no=$row[csf('job_no')];
		}
	}
	if($all_po_id==''){echo "<h2 style='color:#FE4B4B;'>Data not found</h2>";exit();}
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
	/*if($db_type==2)
	{
		$col_grp="listagg(CAST(a.booking_no as VARCHAR(4000)),',') within group (order by a.booking_no) as booking_no";
	}
	else
	{
		$col_grp="group_concat(a.booking_no) as booking_no";
	}*/
	$booking_req_arr=array();
	$sql_wo=sql_select("select a.booking_no,a.booking_type,b.po_break_down_id,
	(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.fin_fab_qnty ELSE 0 END) AS fin_fab_qnty,
	(CASE WHEN a.item_category=12 and a.process=35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty
	from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in ");
	
	//finish_prod_arr
	foreach ($sql_wo as $brow)
	{
		if($brow[csf("grey_req_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['gray']+=$brow[csf("grey_req_qnty")];
		}
		if($brow[csf("woven_req_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['woven']+=$brow[csf("woven_req_qnty")];
		}
		if($brow[csf("fin_fab_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['fin']+=$brow[csf("fin_fab_qnty")];
		}
		if($brow[csf("aop_wo_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['aop_wo_qnty']+=$brow[csf("aop_wo_qnty")];
		}
		if($brow[csf("booking_type")]==1)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['booking_no'].=$brow[csf("booking_no")].',';
		}
	}
	$sql_res=sql_select("select b.po_break_down_id as po_id,
	sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty,
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as exfac_qnty
	from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 and b.shiping_status=3 $po_cond_for_in group by b.po_break_down_id");
	$ex_factory_qty_arr=array();
	foreach($sql_res as $row)
	{
		$ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('exfac_qnty')]-$row[csf('return_qnty')];
	}

	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	

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

	$dataArrayTrans=sql_select("select a.po_breakdown_id,
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

	$prodKnitDataArr=sql_select("select a.po_breakdown_id,
	sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
	sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
	sum(CASE WHEN a.entry_form in(58,22,23) and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_inside,
	sum(CASE WHEN a.entry_form in(58,22,23) and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS knit_qnty_rec_outside
	from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category=13 and a.entry_form in(2,22,58,23) and c.entry_form in(2,22,58,23) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 group by a.po_breakdown_id");// and c.receive_basis<>9
				
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
	if($cbo_search_type==3) //Both part
	{
		$tbl_width=1470;
	}
	else if($cbo_search_type==1) //Textile
	{
		$tbl_width=1470;
	}
	else if($cbo_search_type==2) //Gmt Part
	{
		$tbl_width=1450;
	}
	else
	{
		$tbl_width=3560;
		//$ship_date_html="Last Shipment Date";
		//$ex_fact_date_html="Last Ex-Fact. Date";
		//Textle with=1110+580=1690 //Gmt=1780+580=2360
	}
	ob_start();
	?>
    <div style="width:100%">
        <table width="<? echo $tbl_width;?>">
            <tr>
                <td align="center" width="100%" colspan="18" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)].'<br/>';
				if($txt_date_from!="") echo  $txt_date_from.' To '.$txt_date_to;
				 ?></td>
            </tr>
        </table>
        <?
        if($cbo_search_type==3) // Summary Both part Start
		{
			?>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                    <tr>
                     	<th colspan="18"> Textile Part</th>
                    </tr>
                    <tr style="font-size:13px">
                       	<th width="40">SL</th>
                       	<th width="110">Buyer</th>

                       	<th width="80">Job Qty.<br/> (Pcs)</th>
                       	<th width="80">Yarn Req.<br/><font style="font-size:9px;color: red; font-weight:100">(As Per Pre-Cost)</font></th>
                       	<th width="80">Yarn <br/>Issued</th>
                       	<th width="90">Yarn Less or Over Issued</th>
                       	<th width="80">Gray <br/>Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>                        
                       	<th width="80">Knitting <br/>Prod.</th>
                       	<th width="80">Gray Fab. Receive</th>
                       	<th width="80">Knit. Under or Over Prod.</th>
                       	<th width="80">Knit. Issued <br/>To Dyeing</th>
                       	<th width="80">Knit. Left <br/>Over</th>
                       	<th width="80">Fin Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>
                       	<th width="80">Fin Fab. <br/>Prod.</th>
                       	<th width="80">Fin. Fab. Receive</th>
                       	<th width="80">Fin Less or Over</th>
                       	<th width="80">Fin Fab. Issue <br/>To Cut</th>
                       	<th width="">Fin Left<br/>Over</th>
                    </tr>
                </thead>
           	</table>
            <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
                <?
					$i=1;$total_grey_rec_qty=$total_grey_req_qty=$tot_yarn_less_over_qty=$tot_knit_prod_qty=$total_grey_rec_qty=$total_under_over_prod=$total_knit_issuedToDyeQnty=$total_knit_left_over=$tot_fin_req_qty=$tot_total_finishing_prod=$total_fin_fab_recv=$tot_fin_less_over_prod=$total_issuedToCutQnty=$tot_finish_left_over=$tot_tot_po_qnty=$tot_cuttingQty=$tot_cutting_excess_lessQty=$tot_total_print_issued=$tot_print_delv_balance=$tot_total_print_recv=$tot_print_recv_reject_qnty=$tot_print_yet_recv_qty=$tot_total_sew_input=$tot_total_sew_recv=$tot_yet_sewing_output=$tot_sew_reject_qnty=$tot_total_gmts_finish_qnty=$tot_finish_reject_qnty=$tot_yet_to_finish=$tot_ex_factory_qty=$tot_left_over_finish_gmts=$tot_short_exfac_qty=$tot_total_reject=$tot_total_balance_qty=$tot_cut_to_ship_ratio=$tot_finish_fabric_req_qnty=0;
					
					foreach($result_data_arr as $job_no=>$val)
					{
						$ratio=$val["ratio"];
						$po_id=rtrim($val["po_id"],',');
						$po_ids=array_unique(explode(",",$po_id));
						$yarn_required=$yarn_req_job=$tot_grey_req_qty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$grey_fabric_req_qnty=$grey_fabric_aop_req_wo_qnty=$aop_delivery_qty=$aop_aop_recv_qnty=$knit_qnty_in=$knit_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_recv_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_reject_qnty=$total_sew_recv=$total_gmts_finish_qnty=$finish_reject_qnty=$ex_factory_qty=$knit_gray_rec_inside=$knit_gray_rec_outside=$transfer_in_qnty_rec_knit=$transfer_out_qnty_knit=$transfer_out_qnty_rec_knit=$issuedToDyeQnty_in=$issuedToDyeQnty_out=$issuedToCutQnty=$finish_fabric_req_qnty=$finish_qnty_in=$finish_qnty_out=$finish_qnty_in_rec_gmt=$finish_qnty_out_rec_gmt=$transfer_in_qnty_finish=$transfer_out_qnty_finish=$gmt_finish_in=$gmt_finish_out=0;
						foreach($po_ids as $pId)
						{
							$yarn_required+=$yarn_qty_arr[$pId];  
							$yarn_req_job+=$yarn_qty_arr[$pId];
							//$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							$yarn_issue_inside+=$yarn_issue_arr[$pId]["issue_qnty_in"]-$yarn_issue_rtn_arr[$pId]["return_qnty_in"];
							$yarn_issue_outside+=$yarn_issue_arr[$pId]["issue_qnty_out"]-$yarn_issue_rtn_arr[$pId]["return_qnty_out"];
							$transfer_in_qnty_yarn+=$transfer_data_arr[$pId]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn+=$transfer_data_arr[$pId]["transfer_out_qnty_yarn"];
							$grey_fabric_req_qnty+=$booking_req_arr[$pId]['gray'];
							$grey_fabric_aop_req_wo_qnty+=$booking_req_arr[$pId]['aop_wo_qnty'];
							$aop_delivery_qty+=$aop_delivery_array[$pId]['batch_issue_qty'];
							$aop_aop_recv_qnty+=$aop_delivery_array[$pId]['aop_recv_qnty'];
							
							$knit_qnty_in+=$kniting_prod_arr[$pId]["knit_qnty_in"];
							$knit_qnty_out+=$kniting_prod_arr[$pId]["knit_qnty_out"];
							
							$knit_gray_rec_inside+=$kniting_prod_arr[$pId]["knit_qnty_rec_inside"];
							$knit_gray_rec_outside+=$kniting_prod_arr[$pId]["knit_qnty_rec_outside"];
							$transfer_in_qnty_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_knit"];
							$transfer_in_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_rec_knit"];
							$issuedToDyeQnty_in+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_out"];
							$issuedToCutQnty+=$grey_cut_issue_arr[$pId]["issue_to_cut_qnty"];
							$finish_fabric_req_qnty+=$booking_req_arr[$pId]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							$finish_qnty_in+=$finish_prod_arr[$pId]["finish_qnty_in"];
							$finish_qnty_out+=$finish_prod_arr[$pId]["finish_qnty_out"];
							$finish_qnty_in_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_out_rec_gmt"];
							$transfer_in_qnty_finish+=$transfer_data_arr[$pId]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish+=$transfer_data_arr[$pId]["transfer_out_qnty_finish"];
							
					
							$print_recv_qnty_in+=$garment_prod_data_arr[$pId]['print_recv_qnty_in'];
							$print_recv_qnty_out+=$garment_prod_data_arr[$pId]['print_recv_qnty_out'];
							$print_recv_reject_qnty+=$garment_prod_data_arr[$pId]['print_reject_qnty'];
							$sew_input_qnty_in+=$garment_prod_data_arr[$pId]['sew_input_qnty_in'];
							$sew_input_qnty_out+=$garment_prod_data_arr[$pId]['sew_input_qnty_out'];
							$sew_reject_qnty+=$garment_prod_data_arr[$pId]['sew_reject_qnty'];
							
							$sew_recv_qnty_in=$garment_prod_data_arr[$pId]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$pId]['sew_recv_qnty_out'];
							$total_sew_recv+=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							
							$gmt_finish_in=$garment_prod_data_arr[$pId]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$pId]['finish_qnty_out'];
							$total_gmts_finish_qnty+=$gmt_finish_in+$gmt_finish_out;
							$finish_reject_qnty+=$garment_prod_data_arr[$pId]['finish_reject_qnty'];
						}
						$total_knitting=$knit_qnty_in+$knit_qnty_out;
						$fin_fab_recv=$finish_qnty_in+$finish_qnty_out;
						$grey_issuedToDyeQnty=$issuedToDyeQnty_in+$issuedToDyeQnty_out;
						
						$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
						$tot_po_qnty=$val["po_qnty"];
						//$exfactory_qnty=$val["ex_factory_qnty"]-$ex_factory_qty;
						$plan_cut_qty=$val["plan_cut"];
						//$job_no=$val["job_no"];
						$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
						$under_over_issued=$yarn_required-$total_issued;
						
						$tot_grey_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;
						$grey_left_over=$tot_grey_rec_qty-$grey_issuedToDyeQnty;
						$total_finishing_prod=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;
						$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);
						$finish_left_over=$finish_fabric_req_qnty-$issuedToCutQnty;
						$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
						$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
						$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
						$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
						$fin_less_over_prod=$finish_fabric_req_qnty-$total_finishing_prod;
						?>
						<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
			 				<td width="40"><? echo $i; ?></td>
                            <td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                            <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty,0); ?></p></td>
                            <td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
                            <td align="right" width="90" title="YarnReq-YarnIssueQty"><? $yarn_less_over_qty=$yarn_required-$total_issued;echo number_format($yarn_less_over_qty,2); ?></td>
                            <td align="right" width="80"><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Booking Info','booking_popup',1)"><? echo number_format($grey_fabric_req_qnty,2); ?></a></td>
                            <td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
                            <td align="right" width="80"><? echo number_format($tot_grey_rec_qty,2); ?></td>
                            <td align="right" width="80" title="Knit Grey Fab Req-Total Prod Qty"><? echo number_format($under_over_prod,2); ?></td>
                             <td align="right" width="80"><? echo number_format($grey_issuedToDyeQnty,2); ?></td>
                            <td align="right" width="80" title="Grey Recv-Grey issuetoDye"><? echo number_format($grey_left_over,2); ?></td>
                            <td align="right" width="80"><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Booking Info','booking_popup',2)"><? echo number_format($finish_fabric_req_qnty,2); ?></a><? //echo number_format($finish_fabric_req_qnty,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_finishing_prod,2); ?></td>
                            <td align="right" width="80" title="Knit Fin Fab Recv"><? echo number_format($fin_fab_recv,2); ?></td>
                            <td align="right" width="80" title="Fin Fab Req-Fin Fab Total"><? echo number_format($fin_less_over_prod,2); ?></td>
                            <td align="right" width="80" title="Knit Fin Fab Issue"><? echo number_format($issuedToCutQnty,2); ?></td>
                            <td align="right" width="" title="Fin Fab Req-Fin IssueToCut"><? echo number_format($finish_left_over,2); ?></td>
						</tr>
						<?
						$tot_order_qty+=$tot_po_qnty;
						$tot_yarn_req_qty+=$yarn_required;
					
						$tot_yarn_issue_qty+=$total_issued;
						$tot_yarn_less_over_qty+=$yarn_less_over_qty;
						$total_grey_req_qty+=$grey_fabric_req_qnty;
						$tot_knit_prod_qty+=$total_knitting;
						$total_grey_rec_qty+=$tot_grey_rec_qty;
						$total_under_over_prod+=$under_over_prod;
						
						$total_knit_issuedToDyeQnty+=$grey_issuedToDyeQnty;
						$total_knit_left_over+=$grey_left_over;
						$tot_fin_req_qty+=$finish_fabric_req_qnty;
						$tot_total_finishing_prod+=$total_finishing_prod;
						$total_fin_fab_recv+=$fin_fab_recv;
						$tot_fin_less_over_prod+=$fin_less_over_prod;
						$total_issuedToCutQnty+=$issuedToCutQnty;
						$tot_finish_left_over+=$finish_left_over;
						$tot_tot_po_qnty+=$tot_po_qnty;
						$tot_cuttingQty+=$cuttingQty;
						$i++;
					}
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110" align="right">Total:</td>
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                    <td width="90" align="right" id="td_yarn_less_qty"><? echo number_format($tot_yarn_less_over_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($total_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_knit_qty"><? echo number_format($tot_knit_prod_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_recv_qty"><? echo number_format($total_grey_rec_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_undOver_qty"><? echo number_format($total_under_over_prod,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty"><? echo number_format($total_knit_issuedToDyeQnty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($total_knit_left_over,2); ?></td>
                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_total_finishing_prod,2); ?></td>
                    <td width="80" align="right" id="td_fin_prLoss_qty"><? echo number_format($total_fin_fab_recv,2); ?></td>
                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_less_over_prod,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($total_issuedToCutQnty,2); ?></td>
                    <td width="" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_finish_left_over,2); ?></td> 
                </tr>
           	</table>
           	<br>
           	<?
           	// Textile Part end
           	// Garment Part start
           	?>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                    <tr>
                    	<th colspan="12">Garment Part Sewing (Pcs)</th>
                     	<th colspan="6">Garment Part Finishing & Export (Pcs)</th>
                    </tr>
                    <tr style="font-size:13px">
                       	<th width="40">SL</th>
                       	<th width="110">Buyer</th>

                       	<th width="80">Job Qty.<br/> (Pcs)</th>                        
                        <th width="80" title="Gmt Part Start from">Plan Qty</th>
                        <th width="80">Cutting Qty</th>                     
                        <th width="80">Cutting%</th>                        
                        <th width="80">Cutting  Excess or Less Qty</th>

                        <th width="80" title="Total Sew. Input"> Sew. Input</th>
                        <th width="80">Yet to Sew. Input</th>
                        <th width="80" title="Total Sewing">Sewing Qty.<br/> (Pcs.)</th>
                        <th width="80" title="Sew Out Reject">Sewg. Reject Qty. (Pcs.)</th>
                        <th width="80">Yet to Sew Out</th>
                        <th width="80">Total Finish</th>
                        <th width="80">Finish Reject</th>
                        <th width="80">Yet to Finish</th>
                        <th width="80">Ex-Factory</th>

                        <th width="80">Total Balance</th>                        
                        <th width="">Cut TO Ship Ratio</th>
                    </tr>
                </thead>
           	</table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body_garment">
	            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
	                <?
					$i=1;$tot_grey_rec_qty=$tot_grey_req_qty=$tot_yarn_less_over_qty=$tot_knit_prod_qty=$total_grey_rec_qty=$total_under_over_prod=$total_knit_issuedToDyeQnty=$total_knit_left_over=$tot_fin_req_qty=$tot_total_finishing_prod=$total_fin_fab_recv=$tot_fin_less_over_prod=$total_issuedToCutQnty=$tot_finish_left_over=$tot_tot_po_qnty=$tot_cuttingQty=$tot_cutting_excess_lessQty=$tot_total_print_issued=$tot_print_delv_balance=$tot_total_print_recv=$tot_print_recv_reject_qnty=$tot_print_yet_recv_qty=$tot_total_sew_input=$tot_total_sew_recv=$tot_yet_sewing_output=$tot_sew_reject_qnty=$tot_total_gmts_finish_qnty=$tot_finish_reject_qnty=$tot_yet_to_finish=$tot_ex_factory_qty=$tot_left_over_finish_gmts=$tot_short_exfac_qty=$tot_total_reject=$tot_total_balance_qty=$tot_cut_to_ship_ratio=$tot_finish_fabric_req_qnty=$tot_plan_qty=0;
						
					foreach($result_data_arr as $job_no=>$val)
					{
						$ratio=$val["ratio"];
						$po_id=rtrim($val["po_id"],',');
						$po_ids=array_unique(explode(",",$po_id));
						$yarn_required=$yarn_req_job=$tot_grey_req_qty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$grey_fabric_req_qnty=$grey_fabric_aop_req_wo_qnty=$aop_delivery_qty=$aop_aop_recv_qnty=$knit_qnty_in=$knit_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_recv_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_reject_qnty=$total_sew_recv=$total_gmts_finish_qnty=$finish_reject_qnty=$ex_factory_qty=$cuttingQty=$print_issue_qnty_in=$print_issue_qnty_out=$knit_gray_rec_inside=$knit_gray_rec_outside=$transfer_in_qnty_knit=$transfer_out_qnty_knit=$transfer_in_qnty_rec_knit=$transfer_out_qnty_rec_knit=$issuedToDyeQnty_in=$issuedToDyeQnty_out=$issuedToCutQnty=$finish_fabric_req_qnty=0;
						foreach($po_ids as $pId)
						{
							$yarn_required+=$yarn_qty_arr[$pId];
							$yarn_req_job+=$yarn_qty_arr[$pId];
							//$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							$yarn_issue_inside+=$yarn_issue_arr[$pId]["issue_qnty_in"]-$yarn_issue_rtn_arr[$pId]["return_qnty_in"];
							$yarn_issue_outside+=$yarn_issue_arr[$pId]["issue_qnty_out"]-$yarn_issue_rtn_arr[$pId]["return_qnty_out"];
							$transfer_in_qnty_yarn+=$transfer_data_arr[$pId]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn+=$transfer_data_arr[$pId]["transfer_out_qnty_yarn"];
							$grey_fabric_req_qnty+=$booking_req_arr[$pId]['gray'];
							$grey_fabric_aop_req_wo_qnty+=$booking_req_arr[$pId]['aop_wo_qnty'];
							$aop_delivery_qty+=$aop_delivery_array[$pId]['batch_issue_qty'];
							$aop_aop_recv_qnty+=$aop_delivery_array[$pId]['aop_recv_qnty'];
							
							$knit_qnty_in+=$kniting_prod_arr[$pId]["knit_qnty_in"];
							$knit_qnty_out+=$kniting_prod_arr[$pId]["knit_qnty_out"];
							
							$knit_gray_rec_inside+=$kniting_prod_arr[$pId]["knit_qnty_rec_inside"];
							$knit_gray_rec_outside+=$kniting_prod_arr[$pId]["knit_qnty_rec_outside"];
							$transfer_in_qnty_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_knit"];
							
							$transfer_in_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_rec_knit"];
							$issuedToDyeQnty_in+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_out"];
							$issuedToCutQnty+=$grey_cut_issue_arr[$pId]["issue_to_cut_qnty"];
							$finish_fabric_req_qnty+=$booking_req_arr[$pId]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							
							$finish_qnty_in+=$finish_prod_arr[$pId]["finish_qnty_in"];
							$finish_qnty_out+=$finish_prod_arr[$pId]["finish_qnty_out"];
							$finish_qnty_in_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_out_rec_gmt"];
							$transfer_in_qnty_finish+=$transfer_data_arr[$pId]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish+=$transfer_data_arr[$pId]["transfer_out_qnty_finish"];
							
					
							$print_issue_qnty_in+=$garment_prod_data_arr[$pId]['print_issue_qnty_in'];
							$print_issue_qnty_out+=$garment_prod_data_arr[$pId]['print_issue_qnty_out'];
							$print_recv_qnty_in+=$garment_prod_data_arr[$pId]['print_recv_qnty_in'];
							$print_recv_qnty_out+=$garment_prod_data_arr[$pId]['print_recv_qnty_out'];
							$print_recv_reject_qnty+=$garment_prod_data_arr[$pId]['print_reject_qnty'];
							$sew_input_qnty_in+=$garment_prod_data_arr[$pId]['sew_input_qnty_in'];
							$sew_input_qnty_out+=$garment_prod_data_arr[$pId]['sew_input_qnty_out'];
							$sew_reject_qnty+=$garment_prod_data_arr[$pId]['sew_reject_qnty'];
							
							$sew_recv_qnty_in=$garment_prod_data_arr[$pId]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$pId]['sew_recv_qnty_out'];
							$total_sew_recv+=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							
							$gmt_finish_in=$garment_prod_data_arr[$pId]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$pId]['finish_qnty_out'];
							$total_gmts_finish_qnty+=$gmt_finish_in+$gmt_finish_out;
							$finish_reject_qnty+=$garment_prod_data_arr[$pId]['finish_reject_qnty'];
						
							$cuttingQty+=$garment_prod_data_arr[$pId]['cutting_qnty'];
							//echo $garment_prod_data_arr[$pId]['cutting_qnty'].'<br>';
						}
						$grey_issuedToDyeQnty=$issuedToDyeQnty_in+$issuedToDyeQnty_out;
						
						$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
						$tot_po_qnty=$val["po_qnty"];
						$plan_cut_qty=$val["plan_cut"];
						$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
						$under_over_issued=$yarn_required-$total_issued;

						$fin_less_over_prod=$grey_fabric_req_qnty-$total_finishing_prod;
						$tot_grey_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;
						
						$grey_left_over=$tot_grey_rec_qty-$grey_issuedToDyeQnty;
						
						
						$total_finishing_prod=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;
						
						$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);
						$finish_left_over=$finish_fabric_req_qnty-$issuedToCutQnty;
						
						$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
						$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
						$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
						?>
						<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">

			 				<td width="40"><? echo $i; ?></td>
                            <td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                                             
                            <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty,0); ?></p></td>                           
                            <td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($plan_cut_qty,0); ?></td>
            				<td align="right" width="80" title="Cutting QC"><? echo number_format($cuttingQty,0); ?></td>
                            <td align="right" width="80" title="CuttingQC/PlanQty*100"><? if($cuttingQty>0) echo number_format($cuttingQty/$plan_cut_qty*100);else echo "0"; ?></td>
                            <td align="right" width="80"  title="Cutting-PlanQty"><? $cutting_excess_lessQty=$cuttingQty-$plan_cut_qty;echo number_format($cutting_excess_lessQty); ?></td>

                            <td align="right" width="80"><? echo number_format($total_sew_input); ?></td>
                            <td align="right" width="80" title="CuttingQc-Total Sew Input"><? $yet_sewing_input=$cuttingQty-$total_sew_input;echo number_format($yet_sewing_input);//sew_reject_qnty ?></td>
                            <td align="right" width="80"><? echo number_format($total_sew_recv); //number_format($total_gmts_finish_qnty);?></td>
                            <td align="right" width="80"><? echo number_format($sew_reject_qnty);//$finish_balance_qnty ?></td>
                            <td align="right" width="80" title="Tot Sew input-Total Sew-Sew Reject"><? $yet_sewing_output=$total_sew_input-($total_sew_recv+$sew_reject_qnty);echo number_format($yet_sewing_output,0); //echo number_format($finish_reject_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($total_gmts_finish_qnty,0); ?></td>
                            <td align="right" width="80">
                            <? echo number_format($finish_reject_qnty); ?></td>
                            <td align="right" width="80" itle="Tot Sewing-Total Finish-Finish Reject"><? $yet_to_finish=$total_sew_recv-$total_gmts_finish_qnty-$finish_reject_qnty;echo number_format($yet_to_finish,0); ?></td>
                            <td align="right" width="80">
							<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo implode(',',array_unique(explode(",",$po_id))); ?>','550px')"><? echo  number_format($ex_factory_qty); ?></a>
							<? //echo number_format($exfactory_qnty); ?></td>                           


                            <td align="right" width="80" title="Yet to Sew Input+Output+Finish+LeftOver Qty"><? $left_over_finish_gmts=$total_gmts_finish_qnty-$ex_factory_qty;
                            $total_balance_qty=$yet_sewing_output+$yet_sewing_input+$yet_to_finish+$left_over_finish_gmts; echo number_format($total_balance_qty); ?></td>
                            <td align="right" width="" title="Ex-Fac Qty/CuttingQc*100=<? echo $ex_factory_qty.'/'.$cuttingQty.'*100'; ?>"><? if($ex_factory_qty>0) $cut_to_ship_ratio=$ex_factory_qty/$cuttingQty*100;else $cut_to_ship_ratio=0;//echo $ex_factory_qty.'/'.$cuttingQty.'*100';
							echo number_format($cut_to_ship_ratio,0); ?></td>
						</tr>
						<?
						$tot_order_qty+=$tot_po_qnty;
						$tot_plan_qty+=$plan_cut_qty;
						$tot_yarn_req_qty+=$yarn_required;
						$tot_tot_po_qnty+=$tot_po_qnty;
						$tot_cuttingQty+=$cuttingQty;
						$tot_cutting_excess_lessQty+=$cutting_excess_lessQty;
						$tot_total_print_issued+=$total_print_issued;
						$tot_print_delv_balance+=$print_delv_balance;
						$tot_total_print_recv+=$total_print_recv;
						$tot_print_recv_reject_qnty+=$print_recv_reject_qnty;
						$tot_print_yet_recv_qty+=$print_yet_recv_qty;
						$tot_total_sew_input+=$total_sew_input; 
						$tot_yet_sewing_input+=$yet_sewing_input;
						$tot_total_sew_recv+=$total_sew_recv;
						$tot_sew_reject_qnty+=$sew_reject_qnty;
						$tot_yet_sewing_output+=$yet_sewing_output;
						$tot_total_gmts_finish_qnty+=$total_gmts_finish_qnty;
						$tot_finish_reject_qnty+=$finish_reject_qnty;
						$tot_yet_to_finish+=$yet_to_finish;
						
						$tot_ex_factory_qty+=$ex_factory_qty;
						$tot_left_over_finish_gmts+=$left_over_finish_gmts;
						
						$tot_short_exfac_qty+=$short_exfac_qty;
						$tot_total_reject+=$total_reject;
						$tot_total_balance_qty+=$total_balance_qty;
						$tot_cut_to_ship_ratio+=$cut_to_ship_ratio;
					
						$i++;
					}
					?>
	            </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">Total :</td>
 
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_plan_qty); ?></td>
                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cuttingQty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_cutting_excess_qty"><? echo number_format($tot_cutting_excess_lessQty); ?></td>

                    <td width="80" align="right" id="td_sewInput_qty"><? echo number_format($tot_total_sew_input); ?></td>
                    <td width="80" align="right" id="td_Yettosew_qty"><? echo number_format($tot_yet_sewing_input); ?></td>
                    <td width="80" align="right" id="td_sewingRecv_qty"><? echo number_format($tot_total_sew_recv); ?></td>
                    <td width="80" align="right" id="td_sew_reject_qty"><? echo number_format($tot_sew_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_yetSewOut_qty"><? echo number_format($tot_yet_sewing_output); ?></td>                    
                    <td width="80" align="right" id="td_total_finish_qty"><? echo number_format($tot_total_gmts_finish_qnty); ?></td>
                    <td width="80" align="right" id="td_fin_reject_qty"><? echo number_format($tot_finish_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_tot_yet_to_finish_qty"><? echo number_format($tot_yet_to_finish); ?></td>
                    <td width="80" align="right" id="td_tot_ex_factory_qty"><? echo number_format($tot_ex_factory_qty); ?></td>                    

                    <td align="right" width="80" id="td_tot_total_balance"><? echo number_format($tot_total_balance_qty); ?></td>
                    <td align="right" width="" id="td_cutToShip_qty"><? echo number_format($tot_cut_to_ship_ratio); ?></td>
                </tr>
           	</table>
           	<?
		} //Both part End

		if($cbo_search_type==1) // Summary Textile part Start
		{
			?>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                    <tr>
                     	<th colspan="18"> Textile Part</th>
                    </tr>
                    <tr style="font-size:13px">
                       	<th width="40">SL</th>
                       	<th width="110">Buyer</th>

                       	<th width="80">Job Qty.<br/> (Pcs)</th>
                       	<th width="80">Yarn Req.<br/><font style="font-size:9px;color: red; font-weight:100">(As Per Pre-Cost)</font></th>
                       	<th width="80">Yarn <br/>Issued</th>
                       	<th width="90">Yarn Less or Over Issued</th>
                       	<th width="80">Gray <br/>Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>                        
                       	<th width="80">Knitting <br/>Prod.</th>
                       	<th width="80">Gray Fab. Receive</th>
                       	<th width="80">Knit. Under or Over Prod.</th>
                       	<th width="80">Knit. Issued <br/>To Dyeing</th>
                       	<th width="80">Knit. Left <br/>Over</th>
                       	<th width="80">Fin Fab Req. <br/><font style="font-size:9px;color: red; font-weight:100">(As Per Booking)</font></th>
                       	<th width="80">Fin Fab. <br/>Prod.</th>
                       	<th width="80">Fin. Fab. Receive</th>
                       	<th width="80">Fin Less or Over</th>
                       	<th width="80">Fin Fab. Issue <br/>To Cut</th>
                       	<th width="">Fin Left<br/>Over</th>
                    </tr>
                </thead>
           	</table>
            <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
                <?
					$i=1;$total_grey_rec_qty=$total_grey_req_qty=$tot_yarn_less_over_qty=$tot_knit_prod_qty=$total_grey_rec_qty=$total_under_over_prod=$total_knit_issuedToDyeQnty=$total_knit_left_over=$tot_fin_req_qty=$tot_total_finishing_prod=$total_fin_fab_recv=$tot_fin_less_over_prod=$total_issuedToCutQnty=$tot_finish_left_over=$tot_tot_po_qnty=$tot_cuttingQty=$tot_cutting_excess_lessQty=$tot_total_print_issued=$tot_print_delv_balance=$tot_total_print_recv=$tot_print_recv_reject_qnty=$tot_print_yet_recv_qty=$tot_total_sew_input=$tot_total_sew_recv=$tot_yet_sewing_output=$tot_sew_reject_qnty=$tot_total_gmts_finish_qnty=$tot_finish_reject_qnty=$tot_yet_to_finish=$tot_ex_factory_qty=$tot_left_over_finish_gmts=$tot_short_exfac_qty=$tot_total_reject=$tot_total_balance_qty=$tot_cut_to_ship_ratio=$tot_finish_fabric_req_qnty=0;
					
					foreach($result_data_arr as $job_no=>$val)
					{
						$ratio=$val["ratio"];
						$po_id=rtrim($val["po_id"],',');
						$po_ids=array_unique(explode(",",$po_id));
						$yarn_required=$yarn_req_job=$tot_grey_req_qty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$grey_fabric_req_qnty=$grey_fabric_aop_req_wo_qnty=$aop_delivery_qty=$aop_aop_recv_qnty=$knit_qnty_in=$knit_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_recv_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_reject_qnty=$total_sew_recv=$total_gmts_finish_qnty=$finish_reject_qnty=$ex_factory_qty=$knit_gray_rec_inside=$knit_gray_rec_outside=$transfer_in_qnty_rec_knit=$transfer_out_qnty_knit=$transfer_out_qnty_rec_knit=$issuedToDyeQnty_in=$issuedToDyeQnty_out=$issuedToCutQnty=$finish_fabric_req_qnty=$finish_qnty_in=$finish_qnty_out=$finish_qnty_in_rec_gmt=$finish_qnty_out_rec_gmt=$transfer_in_qnty_finish=$transfer_out_qnty_finish=$gmt_finish_in=$gmt_finish_out=0;
						foreach($po_ids as $pId)
						{
							$yarn_required+=$yarn_qty_arr[$pId];  
							$yarn_req_job+=$yarn_qty_arr[$pId];
							//$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							$yarn_issue_inside+=$yarn_issue_arr[$pId]["issue_qnty_in"]-$yarn_issue_rtn_arr[$pId]["return_qnty_in"];
							$yarn_issue_outside+=$yarn_issue_arr[$pId]["issue_qnty_out"]-$yarn_issue_rtn_arr[$pId]["return_qnty_out"];
							$transfer_in_qnty_yarn+=$transfer_data_arr[$pId]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn+=$transfer_data_arr[$pId]["transfer_out_qnty_yarn"];
							$grey_fabric_req_qnty+=$booking_req_arr[$pId]['gray'];
							$grey_fabric_aop_req_wo_qnty+=$booking_req_arr[$pId]['aop_wo_qnty'];
							$aop_delivery_qty+=$aop_delivery_array[$pId]['batch_issue_qty'];
							$aop_aop_recv_qnty+=$aop_delivery_array[$pId]['aop_recv_qnty'];
							
							$knit_qnty_in+=$kniting_prod_arr[$pId]["knit_qnty_in"];
							$knit_qnty_out+=$kniting_prod_arr[$pId]["knit_qnty_out"];
							
							$knit_gray_rec_inside+=$kniting_prod_arr[$pId]["knit_qnty_rec_inside"];
							$knit_gray_rec_outside+=$kniting_prod_arr[$pId]["knit_qnty_rec_outside"];
							$transfer_in_qnty_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_knit"];
							$transfer_in_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_rec_knit"];
							$issuedToDyeQnty_in+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_out"];
							$issuedToCutQnty+=$grey_cut_issue_arr[$pId]["issue_to_cut_qnty"];
							$finish_fabric_req_qnty+=$booking_req_arr[$pId]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							$finish_qnty_in+=$finish_prod_arr[$pId]["finish_qnty_in"];
							$finish_qnty_out+=$finish_prod_arr[$pId]["finish_qnty_out"];
							$finish_qnty_in_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_out_rec_gmt"];
							$transfer_in_qnty_finish+=$transfer_data_arr[$pId]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish+=$transfer_data_arr[$pId]["transfer_out_qnty_finish"];
							
					
							$print_recv_qnty_in+=$garment_prod_data_arr[$pId]['print_recv_qnty_in'];
							$print_recv_qnty_out+=$garment_prod_data_arr[$pId]['print_recv_qnty_out'];
							$print_recv_reject_qnty+=$garment_prod_data_arr[$pId]['print_reject_qnty'];
							$sew_input_qnty_in+=$garment_prod_data_arr[$pId]['sew_input_qnty_in'];
							$sew_input_qnty_out+=$garment_prod_data_arr[$pId]['sew_input_qnty_out'];
							$sew_reject_qnty+=$garment_prod_data_arr[$pId]['sew_reject_qnty'];
							
							$sew_recv_qnty_in=$garment_prod_data_arr[$pId]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$pId]['sew_recv_qnty_out'];
							$total_sew_recv+=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							
							$gmt_finish_in=$garment_prod_data_arr[$pId]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$pId]['finish_qnty_out'];
							$total_gmts_finish_qnty+=$gmt_finish_in+$gmt_finish_out;
							$finish_reject_qnty+=$garment_prod_data_arr[$pId]['finish_reject_qnty'];
						}
						$total_knitting=$knit_qnty_in+$knit_qnty_out;
						$fin_fab_recv=$finish_qnty_in+$finish_qnty_out;
						$grey_issuedToDyeQnty=$issuedToDyeQnty_in+$issuedToDyeQnty_out;
						
						$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
						$tot_po_qnty=$val["po_qnty"];
						//$exfactory_qnty=$val["ex_factory_qnty"]-$ex_factory_qty;
						$plan_cut_qty=$val["plan_cut"];
						//$job_no=$val["job_no"];
						$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
						$under_over_issued=$yarn_required-$total_issued;
						
						$tot_grey_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;
						$grey_left_over=$tot_grey_rec_qty-$grey_issuedToDyeQnty;
						$total_finishing_prod=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;
						$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);
						$finish_left_over=$finish_fabric_req_qnty-$issuedToCutQnty;
						$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
						$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
						$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
						$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
						$fin_less_over_prod=$finish_fabric_req_qnty-$total_finishing_prod;
						?>
						<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
			 				<td width="40"><? echo $i; ?></td>
                            <td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                            <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty,0); ?></p></td>
                            <td align="right" width="80"><? echo number_format($yarn_required,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_issued,2); ?></td>
                            <td align="right" width="90" title="YarnReq-YarnIssueQty"><? $yarn_less_over_qty=$yarn_required-$total_issued;echo number_format($yarn_less_over_qty,2); ?></td>
                            <td align="right" width="80"><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Booking Info','booking_popup',1)"><? echo number_format($grey_fabric_req_qnty,2); ?></a></td>
                            <td align="right" width="80"><? echo number_format($total_knitting,2); ?></td>
                            <td align="right" width="80"><? echo number_format($tot_grey_rec_qty,2); ?></td>
                            <td align="right" width="80" title="Knit Grey Fab Req-Total Prod Qty"><? echo number_format($under_over_prod,2); ?></td>
                             <td align="right" width="80"><? echo number_format($grey_issuedToDyeQnty,2); ?></td>
                            <td align="right" width="80" title="Grey Recv-Grey issuetoDye"><? echo number_format($grey_left_over,2); ?></td>
                            <td align="right" width="80"><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Booking Info','booking_popup',2)"><? echo number_format($finish_fabric_req_qnty,2); ?></a><? //echo number_format($finish_fabric_req_qnty,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_finishing_prod,2); ?></td>
                            <td align="right" width="80" title="Knit Fin Fab Recv"><? echo number_format($fin_fab_recv,2); ?></td>
                            <td align="right" width="80" title="Fin Fab Req-Fin Fab Total"><? echo number_format($fin_less_over_prod,2); ?></td>
                            <td align="right" width="80" title="Knit Fin Fab Issue"><? echo number_format($issuedToCutQnty,2); ?></td>
                            <td align="right" width="" title="Fin Fab Req-Fin IssueToCut"><? echo number_format($finish_left_over,2); ?></td>
						</tr>
						<?
						$tot_order_qty+=$tot_po_qnty;
						$tot_yarn_req_qty+=$yarn_required;
					
						$tot_yarn_issue_qty+=$total_issued;
						$tot_yarn_less_over_qty+=$yarn_less_over_qty;
						$total_grey_req_qty+=$grey_fabric_req_qnty;
						$tot_knit_prod_qty+=$total_knitting;
						$total_grey_rec_qty+=$tot_grey_rec_qty;
						$total_under_over_prod+=$under_over_prod;
						
						$total_knit_issuedToDyeQnty+=$grey_issuedToDyeQnty;
						$total_knit_left_over+=$grey_left_over;
						$tot_fin_req_qty+=$finish_fabric_req_qnty;
						$tot_total_finishing_prod+=$total_finishing_prod;
						$total_fin_fab_recv+=$fin_fab_recv;
						$tot_fin_less_over_prod+=$fin_less_over_prod;
						$total_issuedToCutQnty+=$issuedToCutQnty;
						$tot_finish_left_over+=$finish_left_over;
						$tot_tot_po_qnty+=$tot_po_qnty;
						$tot_cuttingQty+=$cuttingQty;
						$i++;
					}
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110" align="right">Total:</td>
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_yarn_req_qty"><? echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_yarn_issue_qty"><? echo number_format($tot_yarn_issue_qty,2); ?></td>
                    <td width="90" align="right" id="td_yarn_less_qty"><? echo number_format($tot_yarn_less_over_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_req_qty"><? echo number_format($total_grey_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_knit_qty"><? echo number_format($tot_knit_prod_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_recv_qty"><? echo number_format($total_grey_rec_qty,2); ?></td>
                    <td width="80" align="right" id="td_grey_undOver_qty"><? echo number_format($total_under_over_prod,2); ?></td>
                    <td width="80" align="right" id="td_grey_issDye_qty"><? echo number_format($total_knit_issuedToDyeQnty,2); ?></td>
                    <td width="80" align="right" id="td_grey_lftOver_qty"><? echo number_format($total_knit_left_over,2); ?></td>
                    <td width="80" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="80" align="right" id="td_fin_qty"><? echo number_format($tot_total_finishing_prod,2); ?></td>
                    <td width="80" align="right" id="td_fin_prLoss_qty"><? echo number_format($total_fin_fab_recv,2); ?></td>
                    <td width="80" align="right" id="td_fin_undOver_qty"><? echo number_format($tot_fin_less_over_prod,2); ?></td>
                    <td width="80" align="right" id="td_fin_issCut_qty"><? echo number_format($total_issuedToCutQnty,2); ?></td>
                    <td width="" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_finish_left_over,2); ?></td> 
                </tr>
           	</table>
           	<?
		} //Textile part End
		else if($cbo_search_type==2) // Summary Gmt Start here
		{
			?>
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                    <tr>
                    	<th colspan="12">Garment Part Sewing (Pcs)</th>
                     	<th colspan="6">Garment Part Finishing & Export (Pcs)</th>
                    </tr>
                    <tr style="font-size:13px">
                       	<th width="40">SL</th>
                       	<th width="110">Buyer</th>

                       	<th width="80">Job Qty.<br/> (Pcs)</th>                        
                        <th width="80" title="Gmt Part Start from">Plan Qty</th>
                        <th width="80">Cutting Qty</th>                     
                        <th width="80">Cutting%</th>                        
                        <th width="80">Cutting  Excess or Less Qty</th>

                        <th width="80" title="Total Sew. Input"> Sew. Input</th>
                        <th width="80">Yet to Sew. Input</th>
                        <th width="80" title="Total Sewing">Sewing Qty.<br/> (Pcs.)</th>
                        <th width="80" title="Sew Out Reject">Sewg. Reject Qty. (Pcs.)</th>
                        <th width="80">Yet to Sew Out</th>
                        <th width="80">Total Finish</th>
                        <th width="80">Finish Reject</th>
                        <th width="80">Yet to Finish</th>
                        <th width="80">Ex-Factory</th>

                        <th width="80">Total Balance</th>                        
                        <th width="">Cut TO Ship Ratio</th>
                    </tr>
                </thead>
           	</table>
            <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
	            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
	                <?
					$i=1;$tot_grey_rec_qty=$tot_grey_req_qty=$tot_yarn_less_over_qty=$tot_knit_prod_qty=$total_grey_rec_qty=$total_under_over_prod=$total_knit_issuedToDyeQnty=$total_knit_left_over=$tot_fin_req_qty=$tot_total_finishing_prod=$total_fin_fab_recv=$tot_fin_less_over_prod=$total_issuedToCutQnty=$tot_finish_left_over=$tot_tot_po_qnty=$tot_cuttingQty=$tot_cutting_excess_lessQty=$tot_total_print_issued=$tot_print_delv_balance=$tot_total_print_recv=$tot_print_recv_reject_qnty=$tot_print_yet_recv_qty=$tot_total_sew_input=$tot_total_sew_recv=$tot_yet_sewing_output=$tot_sew_reject_qnty=$tot_total_gmts_finish_qnty=$tot_finish_reject_qnty=$tot_yet_to_finish=$tot_ex_factory_qty=$tot_left_over_finish_gmts=$tot_short_exfac_qty=$tot_total_reject=$tot_total_balance_qty=$tot_cut_to_ship_ratio=$tot_finish_fabric_req_qnty=$tot_plan_qty=0;
						
					foreach($result_data_arr as $job_no=>$val)
					{
						$ratio=$val["ratio"];
						$po_id=rtrim($val["po_id"],',');
						$po_ids=array_unique(explode(",",$po_id));
						$yarn_required=$yarn_req_job=$tot_grey_req_qty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$grey_fabric_req_qnty=$grey_fabric_aop_req_wo_qnty=$aop_delivery_qty=$aop_aop_recv_qnty=$knit_qnty_in=$knit_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_recv_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_reject_qnty=$total_sew_recv=$total_gmts_finish_qnty=$finish_reject_qnty=$ex_factory_qty=$cuttingQty=$print_issue_qnty_in=$print_issue_qnty_out=$knit_gray_rec_inside=$knit_gray_rec_outside=$transfer_in_qnty_knit=$transfer_out_qnty_knit=$transfer_in_qnty_rec_knit=$transfer_out_qnty_rec_knit=$issuedToDyeQnty_in=$issuedToDyeQnty_out=$issuedToCutQnty=$finish_fabric_req_qnty=0;
						foreach($po_ids as $pId)
						{
							$yarn_required+=$yarn_qty_arr[$pId];
							$yarn_req_job+=$yarn_qty_arr[$pId];
							//$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							$yarn_issue_inside+=$yarn_issue_arr[$pId]["issue_qnty_in"]-$yarn_issue_rtn_arr[$pId]["return_qnty_in"];
							$yarn_issue_outside+=$yarn_issue_arr[$pId]["issue_qnty_out"]-$yarn_issue_rtn_arr[$pId]["return_qnty_out"];
							$transfer_in_qnty_yarn+=$transfer_data_arr[$pId]["transfer_in_qnty_yarn"];
							$transfer_out_qnty_yarn+=$transfer_data_arr[$pId]["transfer_out_qnty_yarn"];
							$grey_fabric_req_qnty+=$booking_req_arr[$pId]['gray'];
							$grey_fabric_aop_req_wo_qnty+=$booking_req_arr[$pId]['aop_wo_qnty'];
							$aop_delivery_qty+=$aop_delivery_array[$pId]['batch_issue_qty'];
							$aop_aop_recv_qnty+=$aop_delivery_array[$pId]['aop_recv_qnty'];
							
							$knit_qnty_in+=$kniting_prod_arr[$pId]["knit_qnty_in"];
							$knit_qnty_out+=$kniting_prod_arr[$pId]["knit_qnty_out"];
							
							$knit_gray_rec_inside+=$kniting_prod_arr[$pId]["knit_qnty_rec_inside"];
							$knit_gray_rec_outside+=$kniting_prod_arr[$pId]["knit_qnty_rec_outside"];
							$transfer_in_qnty_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_knit"];
							$transfer_out_qnty_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_knit"];
							
							$transfer_in_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_in_qnty_rec_knit"];
							$transfer_out_qnty_rec_knit+=$transfer_data_arr[$pId]["transfer_out_qnty_rec_knit"];
							$issuedToDyeQnty_in+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_in"];
							$issuedToDyeQnty_out+=$grey_cut_issue_arr[$pId]["grey_issue_qnty_out"];
							$issuedToCutQnty+=$grey_cut_issue_arr[$pId]["issue_to_cut_qnty"];
							$finish_fabric_req_qnty+=$booking_req_arr[$pId]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
							
							$finish_qnty_in+=$finish_prod_arr[$pId]["finish_qnty_in"];
							$finish_qnty_out+=$finish_prod_arr[$pId]["finish_qnty_out"];
							$finish_qnty_in_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_in_rec_gmt"];
							$finish_qnty_out_rec_gmt+=$finish_prod_arr[$pId]["finish_qnty_out_rec_gmt"];
							$transfer_in_qnty_finish+=$transfer_data_arr[$pId]["transfer_in_qnty_finish"];
							$transfer_out_qnty_finish+=$transfer_data_arr[$pId]["transfer_out_qnty_finish"];
							
					
							$print_issue_qnty_in+=$garment_prod_data_arr[$pId]['print_issue_qnty_in'];
							$print_issue_qnty_out+=$garment_prod_data_arr[$pId]['print_issue_qnty_out'];
							$print_recv_qnty_in+=$garment_prod_data_arr[$pId]['print_recv_qnty_in'];
							$print_recv_qnty_out+=$garment_prod_data_arr[$pId]['print_recv_qnty_out'];
							$print_recv_reject_qnty+=$garment_prod_data_arr[$pId]['print_reject_qnty'];
							$sew_input_qnty_in+=$garment_prod_data_arr[$pId]['sew_input_qnty_in'];
							$sew_input_qnty_out+=$garment_prod_data_arr[$pId]['sew_input_qnty_out'];
							$sew_reject_qnty+=$garment_prod_data_arr[$pId]['sew_reject_qnty'];
							
							$sew_recv_qnty_in=$garment_prod_data_arr[$pId]['sew_recv_qnty_in'];
							$sew_recv_qnty_out=$garment_prod_data_arr[$pId]['sew_recv_qnty_out'];
							$total_sew_recv+=$sew_recv_qnty_in+$sew_recv_qnty_out;
							$ex_factory_qty+=$ex_factory_qty_arr[$pId];
							
							$gmt_finish_in=$garment_prod_data_arr[$pId]['finish_qnty_in'];
							$gmt_finish_out=$garment_prod_data_arr[$pId]['finish_qnty_out'];
							$total_gmts_finish_qnty+=$gmt_finish_in+$gmt_finish_out;
							$finish_reject_qnty+=$garment_prod_data_arr[$pId]['finish_reject_qnty'];
						
							$cuttingQty+=$garment_prod_data_arr[$pId]['cutting_qnty'];
							//echo $garment_prod_data_arr[$pId]['cutting_qnty'].'<br>';
						}
						$grey_issuedToDyeQnty=$issuedToDyeQnty_in+$issuedToDyeQnty_out;
						
						$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
						$tot_po_qnty=$val["po_qnty"];
						$plan_cut_qty=$val["plan_cut"];
						$total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
						$under_over_issued=$yarn_required-$total_issued;

						$fin_less_over_prod=$grey_fabric_req_qnty-$total_finishing_prod;
						$tot_grey_rec_qty=($knit_gray_rec_inside+$knit_gray_rec_outside+$transfer_in_qnty_rec_knit)-$transfer_out_qnty_rec_knit;
						
						$grey_left_over=$tot_grey_rec_qty-$grey_issuedToDyeQnty;
						
						
						$total_finishing_prod=($finish_qnty_in_rec_gmt+$finish_qnty_out_rec_gmt+$transfer_in_qnty_finish)-$transfer_out_qnty_finish;
						
						$process_loss_dyeing=($issuedToDyeQnty_in+$issuedToDyeQnty_out)-($finish_qnty_in+$finish_qnty_out);
						$finish_left_over=$finish_fabric_req_qnty-$issuedToCutQnty;
						
						$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
						$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
						$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
						?>
						<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">

			 				<td width="40"><? echo $i; ?></td>
                            <td width="110"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                                             
                            <td width="80" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty,0); ?></p></td>                           
                            <td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($plan_cut_qty,0); ?></td>
            				<td align="right" width="80" title="Cutting QC"><? echo number_format($cuttingQty,0); ?></td>
                            <td align="right" width="80" title="CuttingQC/PlanQty*100"><? if($cuttingQty>0) echo number_format($cuttingQty/$plan_cut_qty*100);else echo "0"; ?></td>
                            <td align="right" width="80"  title="Cutting-PlanQty"><? $cutting_excess_lessQty=$cuttingQty-$plan_cut_qty;echo number_format($cutting_excess_lessQty); ?></td>

                            <td align="right" width="80"><? echo number_format($total_sew_input); ?></td>
                            <td align="right" width="80" title="CuttingQc-Total Sew Input"><? $yet_sewing_input=$cuttingQty-$total_sew_input;echo number_format($yet_sewing_input);//sew_reject_qnty ?></td>
                            <td align="right" width="80"><? echo number_format($total_sew_recv); //number_format($total_gmts_finish_qnty);?></td>
                            <td align="right" width="80"><? echo number_format($sew_reject_qnty);//$finish_balance_qnty ?></td>
                            <td align="right" width="80" title="Tot Sew input-Total Sew-Sew Reject"><? $yet_sewing_output=$total_sew_input-($total_sew_recv+$sew_reject_qnty);echo number_format($yet_sewing_output,0); //echo number_format($finish_reject_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($total_gmts_finish_qnty,0); ?></td>
                            <td align="right" width="80">
                            <? echo number_format($finish_reject_qnty); ?></td>
                            <td align="right" width="80" itle="Tot Sewing-Total Finish-Finish Reject"><? $yet_to_finish=$total_sew_recv-$total_gmts_finish_qnty-$finish_reject_qnty;echo number_format($yet_to_finish,0); ?></td>
                            <td align="right" width="80">
							<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo implode(',',array_unique(explode(",",$po_id))); ?>','550px')"><? echo  number_format($ex_factory_qty); ?></a>
							<? //echo number_format($exfactory_qnty); ?></td>                           


                            <td align="right" width="80" title="Yet to Sew Input+Output+Finish+LeftOver Qty"><? $left_over_finish_gmts=$total_gmts_finish_qnty-$ex_factory_qty;
                            $total_balance_qty=$yet_sewing_output+$yet_sewing_input+$yet_to_finish+$left_over_finish_gmts; echo number_format($total_balance_qty); ?></td>
                            <td align="right" width="" title="Ex-Fac Qty/CuttingQc*100=<? echo $ex_factory_qty.'/'.$cuttingQty.'*100'; ?>"><? if($ex_factory_qty>0) $cut_to_ship_ratio=$ex_factory_qty/$cuttingQty*100;else $cut_to_ship_ratio=0;//echo $ex_factory_qty.'/'.$cuttingQty.'*100';
							echo number_format($cut_to_ship_ratio,0); ?></td>
						</tr>
						<?
						$tot_order_qty+=$tot_po_qnty;
						$tot_plan_qty+=$plan_cut_qty;
						$tot_yarn_req_qty+=$yarn_required;
						$tot_tot_po_qnty+=$tot_po_qnty;
						$tot_cuttingQty+=$cuttingQty;
						$tot_cutting_excess_lessQty+=$cutting_excess_lessQty;
						$tot_total_print_issued+=$total_print_issued;
						$tot_print_delv_balance+=$print_delv_balance;
						$tot_total_print_recv+=$total_print_recv;
						$tot_print_recv_reject_qnty+=$print_recv_reject_qnty;
						$tot_print_yet_recv_qty+=$print_yet_recv_qty;
						$tot_total_sew_input+=$total_sew_input; 
						$tot_yet_sewing_input+=$yet_sewing_input;
						$tot_total_sew_recv+=$total_sew_recv;
						$tot_sew_reject_qnty+=$sew_reject_qnty;
						$tot_yet_sewing_output+=$yet_sewing_output;
						$tot_total_gmts_finish_qnty+=$total_gmts_finish_qnty;
						$tot_finish_reject_qnty+=$finish_reject_qnty;
						$tot_yet_to_finish+=$yet_to_finish;
						
						$tot_ex_factory_qty+=$ex_factory_qty;
						$tot_left_over_finish_gmts+=$left_over_finish_gmts;
						
						$tot_short_exfac_qty+=$short_exfac_qty;
						$tot_total_reject+=$total_reject;
						$tot_total_balance_qty+=$total_balance_qty;
						$tot_cut_to_ship_ratio+=$cut_to_ship_ratio;
					
						$i++;
					}
					?>
	            </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="40">&nbsp;</td>
                    <td width="110">Total :</td>
 
                    <td width="80" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <td width="80" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_plan_qty); ?></td>
                    <td width="80" align="right" id="td_cutting_qty"><? echo number_format($tot_cuttingQty); ?></td>
                    <td width="80" align="right" id=""><? //echo number_format($tot_printIssue_qty); ?></td>
                    <td width="80" align="right" id="td_cutting_excess_qty"><? echo number_format($tot_cutting_excess_lessQty); ?></td>

                    <td width="80" align="right" id="td_sewInput_qty"><? echo number_format($tot_total_sew_input); ?></td>
                    <td width="80" align="right" id="td_Yettosew_qty"><? echo number_format($tot_yet_sewing_input); ?></td>
                    <td width="80" align="right" id="td_sewingRecv_qty"><? echo number_format($tot_total_sew_recv); ?></td>
                    <td width="80" align="right" id="td_sew_reject_qty"><? echo number_format($tot_sew_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_yetSewOut_qty"><? echo number_format($tot_yet_sewing_output); ?></td>                    
                    <td width="80" align="right" id="td_total_finish_qty"><? echo number_format($tot_total_gmts_finish_qnty); ?></td>
                    <td width="80" align="right" id="td_fin_reject_qty"><? echo number_format($tot_finish_reject_qnty); ?></td>
                    <td width="80" align="right" id="td_tot_yet_to_finish_qty"><? echo number_format($tot_yet_to_finish); ?></td>
                    <td width="80" align="right" id="td_tot_ex_factory_qty"><? echo number_format($tot_ex_factory_qty); ?></td>                    

                    <td align="right" width="80" id="td_tot_total_balance"><? echo number_format($tot_total_balance_qty); ?></td>
                    <td align="right" width="" id="td_cutToShip_qty"><? echo number_format($tot_cut_to_ship_ratio); ?></td>
                </tr>
           	</table>
           	<?
		} //Gmt part End
	   	?>
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
    echo "$html****$filename****$cbo_search_type****$type";
    exit();
}

if($action=='po_popup')
{
	echo load_html_head_contents("PO Details info", "../../../../", 1, 1,$unicode,'','');
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
<fieldset style="width:670px;" >
<legend>PO  POPUP</legend>
    <div style="100%" id="report_container">
       <table width="670" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="7">PO  Details</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="200">Order No</th>
                    <th width="100">File No</th>
                    <th width="50">Order UOM </th>
                    <th width="100">Order Qty</th>
                    <th width="70">Orgin.Ship date</th>
                    <th width="">Pub.Ship Date</th>
                   
                </tr>
            </thead>
            <?
		 $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num,a.style_ref_no,a.order_uom, a.total_set_qnty as ratio, b.id as po_id,b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date,b.shipment_date

		from wo_po_details_master a, wo_po_break_down b	where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.id in($po_id) order by b.id";
		  $sql_result=sql_select($sql_po);
		  $i=1;
			foreach($sql_result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
                    <td><p><? echo $i; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
                     <td><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
                    <td align="right"><? echo $row[csf('po_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo change_date_format($row[csf('shipment_date')]); ?>&nbsp;</td>
                    <td align="right"><? echo change_date_format($row[csf('pub_shipment_date')]);; ?>&nbsp;</td>
                    
                </tr>
            <?
				$tot_po_qnty+=$row[csf('po_qnty')];
				$i++;
			}
			?>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"></th>
                    
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    
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
 
	
	$location_arr=return_library_array("select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id","location_name");
	$floor_arr=return_library_array("select id,FLOOR_NAME from LIB_PROD_FLOOR where status_active =1 and is_deleted=0 order by FLOOR_NAME","id","FLOOR_NAME");

	?>
    <script>
	function new_window5()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container3').innerHTML+'</body</html>');
		d.close(); 
	}

	</script>
    
    
	<div style="width:100%" align="center" id="report_container3"><br>
    
		<fieldset style="width:530px">
            <div class="form_caption" align="center"><strong>Order Details</strong></div><br>
        	<div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <th width="30">SL</th>
                    <th>Buyer</th>
                    <th>Job Number</th>
                    <th>Style Name</th>
                    <th>Order Number</th>	
                    <th >Item Name</th>
                    <th>Order Qty.</th>
                </thead>
                <?
                $i=1;
				
				$order_sql_res=sql_select("select a.JOB_NO,a.BUYER_NAME,a.STYLE_REF_NO,a.GMTS_ITEM_ID,b.PO_NUMBER,b.PO_QUANTITY
				from  WO_PO_DETAILS_MASTER a,wo_po_break_down b  where a.id=b.job_id and b.status_active=1 and b.is_deleted=0 and b.id in($id)");
				
                foreach($order_sql_res as $row)
                {
                    $bgcolor=($i%2==0)?"#EFEFEF":"#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $row[JOB_NO]; ?></td>
                        <td><? echo $buyer_arr[$row[BUYER_NAME]]; ?></td>
                        <td><? echo $row[STYLE_REF_NO]; ?></td>
                        <td><? echo $row[PO_NUMBER]; ?></td>
                        <td><? echo $garments_item[$row[GMTS_ITEM_ID]]; ?></td>
                        <td align="right"><? echo $row[PO_QUANTITY]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </table>
            </div>
        
        
        
        
        
        
        	<div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br>
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="100">System Id	
                        <th width="100">Delivery Company	
                        <th width="100">Delivery Location	
                        <th width="100">Floor	
                        <th width="100">Ex-Factory Qty	
                        <th width="100">Ex-Factory Date	
                        <th width="100">Ex-Factory Status
                     </tr>
                </thead>
                <?
                $i=1;
				
				$sql_res="select a.SYS_NUMBER,a.DELIVERY_COMPANY_ID,a.DELIVERY_LOCATION_ID,a.DELIVERY_FLOOR_ID,b.shiping_status,b.po_break_down_id,b.ex_factory_date,
				sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty,
				sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as exfac_qnty
				from  PRO_EX_FACTORY_DELIVERY_MST a,pro_ex_factory_mst b  where b.DELIVERY_MST_ID=a.id and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) group by a.SYS_NUMBER,a.DELIVERY_COMPANY_ID,a.DELIVERY_LOCATION_ID,a.DELIVERY_FLOOR_ID,b.shiping_status,b.po_break_down_id,b.ex_factory_date";
				
                $exf_sql_dtls=sql_select($sql_res);
				$tot_exf_rec_qnty=0;
                foreach($exf_sql_dtls as $row)
                {
                    $bgcolor=($i%2==0)?"#EFEFEF":"#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td><? echo $row[SYS_NUMBER]; ?></td>
                        <td><? echo $company_library[$row[DELIVERY_COMPANY_ID]]; ?></td>
                        <td><? echo $location_arr[$row[DELIVERY_LOCATION_ID]]; ?></td>
                        <td><? echo $floor_arr[$row[DELIVERY_FLOOR_ID]]; ?></td>
                        <td align="right"><? echo number_format($row[csf("exfac_qnty")]-$row[csf("return_qnty")],0); ?></td>
                        <td align="center"><? echo change_date_format($row[csf("ex_factory_date")]); ?></td>
                        <td align="center"><? echo $shipment_status[$row[csf("shiping_status")]]; ?></td>
                    </tr>
                    <?
                    $tot_exf_rec_qnty+=($row[csf("exfac_qnty")]-$row[csf("return_qnty")]);
                    $i++;
                }
                ?>
                <tfoot>
                    <th colspan="5">Total</th>
                    <th align="right"><? echo number_format($tot_exf_rec_qnty); ?></th>
                    <th colspan="2"></th>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
    <div style="text-align:center; margin-top:5px;">
    <input type="button" onClick="new_window5()" value="Print Preview" name="Print" class="formbutton" style="width:120px;"/>
    </div>

	<?
    exit();
}
if($action=="booking_popup")
{
 	echo load_html_head_contents("Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	/*echo $from.'_'.$to;//$job_no;
	die;*/
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:530px">
            <div style="width:100%">
            <?
     $sql_wo="select a.is_short,a.booking_no,a.booking_type,b.po_break_down_id,
	(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.fin_fab_qnty ELSE 0 END) AS fin_fab_qnty,
	(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.fin_fab_qnty ELSE 0 END) AS fin_woven_req_qnty,
	
	(b.fin_fab_qnty) as fin_fab_qnty,
	(CASE WHEN a.item_category=12 and a.process=35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty
	from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.po_break_down_id in($po_id) order by a.booking_no,a.booking_type";
	
	 $sql_book=sql_select($sql_wo);
	// echo $type.'ddd';
	
	foreach ($sql_book as $brow)
	{
		if($type==1)
		{
			$qty_type=$brow[csf("grey_req_qnty")];
		} 
		else $qty_type=$brow[csf("fin_fab_qnty")];
		
		if($brow[csf("grey_req_qnty")]>0 && $brow[csf("booking_type")]==1 && $brow[csf("is_short")]==2)
		{
		$main_booking_req_arr[$brow[csf("booking_no")]]['main']+=$qty_type;
		}
		else if($brow[csf("grey_req_qnty")]>0 && $brow[csf("booking_type")]==1 && $brow[csf("is_short")]==1)
		{
		$short_booking_req_arr[$brow[csf("booking_no")]]['short']+=$qty_type;
		}
		else if($brow[csf("grey_req_qnty")]>0 && $brow[csf("booking_type")]==4)
		{
		$samp_booking_req_arr[$brow[csf("booking_no")]]['sample']+=$qty_type;
		}
		
	}
			?>
          <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <caption> Main Fabric Booking</caption>
                <thead>
                    <tr>
                        <th width="20">SL</th>
                        <th width="100">Booking No</th>
                        <th width="100">Booking Grey Qty</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
				$tot_grey_main=0;
                foreach($main_booking_req_arr as $booking_no=>$row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trm_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trm_<? echo $i; ?>">
                        <td width="20"><? echo $i; ?></td>
                        <td width="100"><? echo $booking_no; ?></td>
                        <td width="100" align="right"><? echo number_format($row[("main")],2); ?></td>
                    </tr>
                    <?
                    $tot_grey_main+=$row[("main")];
					
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th align="right"><? echo number_format($tot_grey_main,2); ?></th>
                </tr>
                
                </tfoot>
            </table>
            
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <caption> Short Fabric Booking</caption>
                <thead>
                    <tr>
                        <th width="20">SL</th>
                        <th width="100">Booking No</th>
                        <th width="100">Booking Grey Qty</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
				$tot_grey_short=0;
                foreach($short_booking_req_arr as $booking_no=>$row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trshort_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trshort_<? echo $i; ?>">
                        <td width="20"><? echo $i; ?></td>
                        <td width="100"><? echo $booking_no; ?></td>
                        <td width="100" align="right"><? echo number_format($row[("short")],2); ?></td>
                    </tr>
                    <?
                    $tot_grey_short+=$row[("short")];
					
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th align="right"><? echo number_format($tot_grey_short,2); ?></th>
                </tr>
                
                </tfoot>
            </table>
            
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <caption> Sample Fabric Booking</caption>
                <thead>
                    <tr>
                        <th width="20">SL</th>
                        <th width="100">Booking No</th>
                        <th width="100">Booking Grey Qty</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
				$tot_grey_sample=0;
                foreach($samp_booking_req_arr as $booking_no=>$row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
                        <td width="20"><? echo $i; ?></td>
                        <td width="100"><? echo $booking_no; ?></td>
                        <td width="100" align="right"><? echo number_format($row[("sample")],2); ?></td>
                    </tr>
                    <?
                    $tot_grey_sample+=$row[("sample")];
					
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th align="right"><? echo number_format($tot_grey_sample,2); ?></th>
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
//disconnect($con);
?>
