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
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );


if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/oms_report_controller', this.value, 'load_drop_down_season', 'season_td');" ,0);
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_surch_list_view', 'search_div', 'fabric_followup_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $type_id; ?>', 'order_surch_list_view', 'search_div', 'fabric_followup_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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


if($action=='fabric_report_generate'){

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_job=str_replace("'","",$txt_job);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$style_ref_id=str_replace("'","",$txt_style_ref_id);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
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
	
		if($txt_date_from!="" && $txt_date_to!="")
		{
			if($db_type==0)
			{
				$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
				$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			
				$ship_date_cond="and b.pub_shipment_date between '$date_from' and '$date_to' ";
			}
			else
			{
				$date_from=$txt_date_from;
				$date_to=$txt_date_to;
				$ship_date_cond="and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
			}
		}
	

	$job_no_cond="";$ref_no_cond="";$file_no_cond="";
	if(trim($style_ref_id)!="" && trim($txt_style_ref)!="") 
	{ 	
		$job_no_cond="and a.id  in($style_ref_id)";
	}
	else if(trim($style_ref_id)=="" && trim($txt_style_ref)!="") 
	{
		if(trim($txt_style_ref)!="") $job_no_cond.="and a.style_ref_no  in('$txt_style_ref')";
	}
	if($txt_job=="") $job_cond="";else  $job_cond="and a.job_no_prefix_num=$txt_job";
	if($cbo_season_id==0) $season_cond="";else  $season_cond="and a.season_buyer_wise=$cbo_season_id";
	
	
	
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	//wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,wo_pre_cos_fab_co_color_dtls e
		$sql_po="select a.id as job_id,a.buyer_name, a.job_no, a.job_no_prefix_num,a.order_uom, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date,c.color_number_id as color_id,c.order_quantity,d.id as fab_dtls_id,d.body_part_id as bpart_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,d.color_type_id,d.construction,d.composition,d.gsm_weight,d.nominated_supp,d.width_dia_type,d.uom
		from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d
		where a.id=b.job_id and c.po_break_down_id=b.id and  a.id=d.job_id and  d.job_id=b.job_id and  d.job_id=c.job_id and  a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond $ref_no_cond $file_no_cond $season_cond $job_cond order by a.job_no, c.color_number_id"; 
	
	//echo  $sql_po;//die;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $chk_po_arr=array();
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_job_id=="") $all_job_id=$row[csf("job_id")]; else $all_job_id.=",".$row[csf("job_id")];
		
		$bpart_id=$row[csf("bpart_id")];
		$deter_id=$row[csf("deter_id")];
		$color_id=$row[csf("color_id")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["po_id"].=$row[csf("po_id")].',';
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["buyer_name"]=$row[csf("buyer_name")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["job_no"]=$row[csf("job_no")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["construction"]=$row[csf("construction")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["composition"]=$row[csf("composition")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["gsm_weight"]=$row[csf("gsm_weight")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["nominated_supp"]=$row[csf("nominated_supp")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["color_type_id"]=$color_type[$row[csf("color_type_id")]];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["width_dia_type"]=$fabric_typee[$row[csf("width_dia_type")]];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["fab_uom"]=$row[csf("uom")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["order_uom"]=$row[csf("order_uom")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["sensitive"]=$row[csf("color_size_sensitive")];
		
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["style_ref_no"]=$row[csf("style_ref_no")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["ref_no"].=$row[csf("grouping")].',';
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["file_no"].=$row[csf("file_no")].',';
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["po_number"].=$row[csf("po_number")].',';
		if($chk_po_arr[$row[csf("po_id")]]=="")
		{
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["po_qnty_pcs"]+=$row[csf("po_qnty")]*$row[csf("ratio")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["po_qnty"]+=$row[csf("po_qnty")];
		$chk_po_arr[$row[csf("po_id")]]=1000;
		}
		
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["plan_cut"]+=$row[csf("plan_cut")];
		$fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["pub_shipment_date"].=$row[csf("pub_shipment_date")].',';
	}
	ksort($fabric_wise_arr);
	$all_job_ids=implode(",",array_unique(explode(",",$all_job_id)));
	$all_po_ids=implode(",",array_unique(explode(",",$all_po_id)));
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(!empty($all_po_id)){
	 $condition->po_id_in("$all_po_ids");
	}
	
	if(trim($style_ref_id)!="" && trim($txt_style_ref)!="") 
	{
	 $condition->jobid_in("$style_ref_id");
	}
	else if(trim($style_ref_id)=="" && trim($txt_style_ref)!="") 
	{
	   $condition->style_ref_no("='$txt_style_ref'");
	}
	//jobid_in
	
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
    }
	if(str_replace("'","",$txt_job) !='')
	{
	   $condition->job_no_prefix_num("=$txt_job");
	}
	
					 
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery(); die;
	$fabric_req_arr=$fabric->getQtyArray_by_OrderBodypartDeterminIdAndGmtscolor_knitAndwoven_greyAndfinish();
	$fabric_req_cost_arr=$fabric->getAmountArray_by_OrderBodypartDeterminIdAndGmtscolor_knitAndwoven_greyAndfinish();
	//print_r($fabric_req_arr);die;
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
	$poIds=implode(",",(array_unique(explode(",",$poIds))));
	$po_cond_for_in=" and b.po_break_down_id in($poIds)";
	$po_cond_for_in2=" and a.po_breakdown_id  in($poIds)";
	$po_cond_for_in3=" and b.order_id in($poIds)";
	}
	
	$jobIds=chop($all_job_ids,','); $job_cond_for_in="";  
	$job_ids=count(array_unique(explode(",",$all_job_ids)));
	if($db_type==2 && $job_ids>1000)
	{
	$job_cond_for_in=" and (";
	$jobIdsArr=array_chunk(explode(",",$jobIds),999);
	foreach($jobIdsArr as $ids)
	{
	$ids=implode(",",$ids);
	$job_cond_for_in.=" d.job_id in($ids) or"; 
	}
	$job_cond_for_in=chop($job_cond_for_in,'or ');
	$job_cond_for_in.=")";
	}
	else
	{
	$jobIds=implode(",",(array_unique(explode(",",$jobIds))));
	$job_cond_for_in=" and d.job_id in($jobIds)";
	}
	
	 $sql_fab="select d.id as fab_dtls_id,d.job_id,d.job_no,d.body_part_id as bpart_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,d.color_type_id,d.construction,d.composition,e.gmts_color_id,e.contrast_color_id from wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_color_dtls e where d.id=e.pre_cost_fabric_cost_dtls_id and d.status_active=1 and e.status_active=1 $job_cond_for_in";
		$sql_fab_result=sql_select($sql_fab);
		foreach($sql_fab_result as $row)
		{
			$bpart_id=$row[csf("bpart_id")];
			$deter_id=$row[csf("deter_id")];
			$color_id=$row[csf("gmts_color_id")];
			$contrast_fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["contrast_color_id"]=$row[csf("contrast_color_id")];
		}
		unset($sql_fab_result);
	$booking_req_arr=array();
	 $sql_wo="select a.id, a.booking_no,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,b.gmts_color_id,a.booking_type,b.po_break_down_id as po_id,
	(b.grey_fab_qnty) as grey_fab_qnty,b.job_no,b.amount,c.body_part_id as bpart_id,c.color_size_sensitive,c.lib_yarn_count_deter_id as deter_id
	from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=1 and b.fin_fab_qnty>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $po_cond_for_in";
	$sql_wo_res=sql_select($sql_wo);
	foreach ($sql_wo_res as $row)
	{
		if($all_booking_id=="") $all_booking_id=$row[csf("id")]; else $all_booking_id.=",".$row[csf("id")];
		$booking_req_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['grey']+=$row[csf("grey_fab_qnty")];
		$booking_req_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['amount']+=$row[csf("amount")]/$row[csf("exchange_rate")];
		if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
		{
			$com_supplier=$company_library[$row[csf("supplier_id")]];
		}
		else
		{
			$com_supplier=$supplier_library[$row[csf("supplier_id")]];
		}
		$booking_supp_arr[$row[csf("job_no")]]['supp_com'].=$com_supplier.',';
		$booking_supp_arr[$row[csf("job_no")]]['booking_no']=$row[csf("booking_no")];
	}
	unset($sql_wo_res);
	$bkIds=chop($all_booking_id,','); $book_cond_for_in="";  
	$bk_ids=count(array_unique(explode(",",$all_booking_id)));
	if($db_type==2 && $bk_ids>1000)
	{
	$book_cond_for_in=" and (";
	$bkIdsArr=array_chunk(explode(",",$bkIds),999);
	foreach($bkIdsArr as $ids)
	{
	$ids=implode(",",$ids);
	$book_cond_for_in.=" b.work_order_id in($ids) or"; 
	}
	$book_cond_for_in=chop($book_cond_for_in,'or ');
	$book_cond_for_in.=")";
	}
	else
	{
	$bkIds=implode(",",(array_unique(explode(",",$bkIds))));
	$book_cond_for_in=" and b.work_order_id in($bkIds)";
	}
	
	$sql="select b.pi_id,a.id,a.item_category_id,a.pi_number,a.pi_date,a.importer_id, b.work_order_no,b.work_order_id,b.item_group,b.item_prod_id,
	b.determination_id
	from com_pi_master_details  a,  com_pi_item_details b
	where a.id=b.pi_id and a.importer_id=$cbo_company_name   and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	and b.is_deleted=0 $book_cond_for_in";
	  //echo $sql ; // die;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$piDataArr[$row[csf("work_order_no")]].=$row[csf("pi_number")].',';
	}
	unset($sql_result);
		
		//print_r($booking_req_arr);

	/*$dataArrayTrans=sql_select("select po_breakdown_id,
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
	}*/
	$prodKnitDataArr=sql_select("select a.po_breakdown_id as po_id,b.fabric_description_id as deter_id,b.rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form in(17) THEN a.quantity ELSE 0 END) AS knit_qnty_rec
	from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category=3 and a.entry_form in(17) and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 ");// and c.receive_basis<>9
	
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
	$kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_rec"]+=$row[csf("knit_qnty_rec")];
	$kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_rec_amt"]+=$row[csf("knit_qnty_rec")]*$row[csf("rate")];
	}
	unset($prodKnitDataArr);
	$issueprodKnitDataArr=sql_select("select a.po_breakdown_id as po_id,d.detarmination_id as deter_id,b.cons_rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form in(19) THEN a.quantity ELSE 0 END) AS knit_qnty_issue
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c,product_details_master d where a.trans_id=b.id and b.mst_id=c.id and d.id=b.prod_id and a.prod_id=d.id and c.item_category=3 and a.entry_form in(19) and c.entry_form in(19) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 ");// and c.receive_basis<>9
	$issue_kniting_prod_arr=array();
	foreach($issueprodKnitDataArr as $row)
	{
	$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_issue"]+=$row[csf("knit_qnty_issue")];
	$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_issue_amt"]+=$row[csf("knit_qnty_issue")]*$row[csf("cons_rate")];
	}
	unset($issueprodKnitDataArr);
	
	if(empty($all_po_id))
	{
	echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	$tbl_width=2650;
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
          
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
                <thead>
                    
                    <tr style="font-size:13px">
                       <th width="30">SL</th>
                       <th width="110">Buyer</th>
                       <th width="100">Job No</th>
                       <th width="100">Style Ref</th>
                       <th width="70">Internal Ref.</th>
                       <th width="70">File No</th>
                       <th width="100">Order No</th>
                       <th width="70">Style Qty</th>
                       <th width="40">UOM</th>                        
                       <th width="70">Qty (Pcs)</th>                        
                       <th width="70">Shipment Date</th>
                       <th width="100">Body Part</th>
                       <th width="100">Fabric Construction</th>
                       <th width="150">Fabric Compostion</th>
                       <th width="70">Color Type</th>                        
                       <th width="70">Fabric Weight</th>
                       <th width="70">Width</th>
                       <th width="100">Gmt. Color</th>
                       <th width="100">Fabric Color</th>
                       
                       <th width="70">Req Qty</th>  
                        <th width="50" title="">Fabric UOM</th>
                        <th width="70">Pre Costing Value</th>                        
                        <th width="70">WO Qty</th>                        
                        <th width="70">WO Value (USD)</th>
                        
                        <th width="120">Supplier</th> 
                        <th width="100">PI No.</th>
                        <th width="70">In-House Qty</th>
                        <th width="70">In-House Amount</th>  
                        <th width="70">Receive Balance</th>
                        <th width="70">Issue to Cutting</th>
                        <th width="70">Issue Amount</th>
                        <th width="70">Left Over / Balance</th>
                        <th width="">Left Over / Balance Amount</th>
                    </tr>
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">

                <?    $job_wise_arr=array();$body_wise_arr=array();
						foreach($fabric_wise_arr as $job_no=>$job_data)
						{
							$job_wise_row=0;
						 foreach($job_data as $bprat_id=>$body_data)
						 {
							 $body_wise_row=0;
						  foreach($body_data as $deter_id=>$deter_data)
						  {
							   $deter_wise_row=0;
							foreach($deter_data as $gmt_color=>$val)
						    {
								$job_wise_row++;$body_wise_row++;$deter_wise_row++;
							}
							$job_wise_arr[$job_no]=$job_wise_row;
							$body_wise_arr[$job_no][$bprat_id]=$body_wise_row;
							$fab_wise_arr[$job_no][$bprat_id][$deter_id]=$deter_wise_row;
						  }
						 }
						}
						//print_r($job_wise_arr);
					$i=1;$total_po_qnty=$total_po_qnty_pcs=$total_fabric_req=$total_fabric_req_cost=$total_fabric_req=$total_fabric_amount=$total_kniting_prod_recv_amt=$total_kniting_prod_recv=$total_fab_recv_balance=$total_kniting_prod_issue=$total_kniting_prod_issue_amt=$total_left_over_bal=$total_left_over_bal_amount=0;
					
						foreach($fabric_wise_arr as $job_no=>$job_data)
						{
							$j=1;
						 foreach($job_data as $bprat_id=>$body_data)
						 {
							 $b=1;
						  foreach($body_data as $deter_id=>$deter_data)
						  {
							  $f=1;
							foreach($deter_data as $gmt_color=>$val)
						    {
							$ratio=$val["ratio"];
							$po_id=rtrim($val["po_id"],',');
							$po_ids=array_unique(explode(",",$po_id));
						 $tot_fabric_req=$tot_fabric_req_cost=$booking_req=$booking_amount=$kniting_prod_recv=$kniting_prod_recv_amt=$kniting_prod_issue=$kniting_prod_issue_amt=0;
							foreach($po_ids as $pId)
							{
								
							$fabric_req_knit=array_sum($fabric_req_arr['knit']['finish'][$pId][$bprat_id][$deter_id][$gmt_color]);
							$fabric_req_wov=array_sum($fabric_req_arr['woven']['finish'][$pId][$bprat_id][$deter_id][$gmt_color]);
							$tot_fabric_req+=$fabric_req_knit+$fabric_req_wov;
							$fabric_req_knit_cost=array_sum($fabric_req_cost_arr['knit']['finish'][$pId][$bprat_id][$deter_id][$gmt_color]);
							$fabric_req_wov_cost=array_sum($fabric_req_cost_arr['woven']['finish'][$pId][$bprat_id][$deter_id][$gmt_color]);
							$tot_fabric_req_cost+=$fabric_req_wov_cost+$fabric_req_knit_cost;
							$booking_req+=$booking_req_arr[$pId][$bprat_id][$deter_id][$gmt_color]['grey'];
							$booking_amount+=$booking_req_arr[$pId][$bprat_id][$deter_id][$gmt_color]['amount'];
							$kniting_prod_recv+=$kniting_prod_arr[$pId][$bprat_id][$deter_id][$gmt_color]["knit_qnty_rec"];
							$kniting_prod_recv_amt+=$kniting_prod_arr[$pId][$bprat_id][$deter_id][$gmt_color]["knit_qnty_rec_amt"];
							$kniting_prod_issue+=$issue_kniting_prod_arr[$pId][$bprat_id][$deter_id][$gmt_color]["knit_qnty_issue"];
							$kniting_prod_issue_amt+=$issue_kniting_prod_arr[$pId][$bprat_id][$deter_id][$gmt_color]["knit_qnty_issue"];
	
							}
							
							
							$pub_shipment_date=rtrim($val["pub_shipment_date"],',');
							$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
							$min_pub_shipment_date=min($pub_shipment_date);
							
							$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
							if($sensitive==3)
							{
									$contrast_color=$contrast_fabric_wise_arr[$job_no][$bprat_id][$deter_id][$gmt_color]["contrast_color_id"];
									$fab_gmt_color=$color_library[$contrast_color];
							}
							else
							{
									$fab_gmt_color=$color_library[$gmt_color];
							}
							$booking_no=$booking_supp_arr[$job_no]['booking_no'];
							$pi_no=rtrim($piDataArr[$booking_no],',');
							$pi_no=implode(",",array_unique(explode(",",$pi_no)));
							//$job_wise_arr[$job_no]
							?>
				 <tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
                        
                        <?
                        if($j==1)
						{
							$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
							$tot_po_qnty=$val["po_qnty"];
							$po_qnty_pcs=$val["po_qnty"];
							$sensitive=$val["sensitive"];
							$plan_cut_qnty=$val["plan_cut_qnty"];	
							$total_po_qnty+=$tot_po_qnty;
							$total_po_qnty_pcs+=$po_qnty_pcs;
						?>
                        <td width="30" rowspan="<? echo $job_wise_arr[$job_no]; ?>"><? echo $i; ?></td>
                        <td width="110" rowspan="<? echo $job_wise_arr[$job_no]; ?>"><? echo $buyer_arr[$val["buyer_name"]]; ?></td>
                        <td width="100" rowspan="<? echo $job_wise_arr[$job_no]; ?>"><? echo $val["job_no_prefix_num"]; ?></td>
                        <td width="100" rowspan="<? echo $job_wise_arr[$job_no]; ?>" align="center"><div style="word-wrap:break-word:100px;"><? echo $val["style_ref_no"]; ?></div></td>
                        <td width="70" rowspan="<? echo $job_wise_arr[$job_no]; ?>" align="center"> <div style="word-wrap:break-word:70px;"><? echo $ref_no=rtrim($val["ref_no"],",");implode(',',array_unique(explode(",",$ref_no)));?></div></td>
                        <td width="70" rowspan="<? echo $job_wise_arr[$job_no]; ?>" align="center" bgcolor="#FFFFCC"><div style="word-wrap:break-word:70px;"><?  $file_no=rtrim($val["file_no"],",");$file_nos=implode(',',array_unique(explode(",",$file_no))); echo $file_nos;//number_format($tot_po_qnty); ?></div></td>
                        <td align="center" style="" rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="100"><div style="word-break:break-all"><? $po_no=rtrim($val["po_number"],',');$po_nos=implode(",",array_unique(explode(",",$po_no)));echo $po_nos; ?></div></td>
                        <td align="right" rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="70"><? echo number_format($tot_po_qnty,0); ?></td>
                        <td align="center" rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="40" title=""><? echo $unit_of_measurement[$val["order_uom"]]; ?></td>
                        <td align="center" rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="70"> <? echo number_format($po_qnty_pcs,0); ?></td>
                        <td align="center" rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="70"><? echo change_date_format($min_pub_shipment_date); ?></td>
                        <?
						//$body_wise_arr[$job_no][$bprat_id]
						}
						 if($b==1)
						 {
							 //$fab_wise_arr[$job_no][$bprat_id][$deter_id]
						?>
                        <td align="center" rowspan="<? echo $body_wise_arr[$job_no][$bprat_id]; ?>" width="100" style="word-break:break-all"><? echo $body_part[$bprat_id]; ?></td>
                       <?
                        }
						 if($f==1)
						 {
						?>
                        <td align="center" width="100" title="DeterId=<? echo $deter_id;?>" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>" style="word-break:break-all"><div style="word-wrap:break-word:100px;"><? echo $val["construction"]; ?></div></td>
                         <td align="center" width="150" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>" style=""><div style="word-wrap:break-word:150px;"><? echo $val["composition"]; ?></div></td>
                        <td align="center" width="70" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>" style="word-break:break-all" title=""><p><? echo $val["color_type_id"]; ?></p></td>
                        <td align="center" width="70" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>"><? echo $val["gsm_weight"]; ?></td>
                        <td align="center" width="70" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>" style="word-break:break-all"><? echo $val["width_dia_type"]; ?></td>
                        <?
                        }?>
                        <td align="center" width="100" style="word-break:break-all" title="Gmts Color=<? echo $gmt_color;?>"><p><? echo $color_library[$gmt_color]; ?></p></td>
                        <td align="center" width="100" style="word-break:break-all" title="sensitive=<? echo $sensitive; ?>"><p><? echo $fab_gmt_color; ?></p></td>
                        <td align="right" width="70" title="Knit Fin Fab Issue"><? echo number_format($tot_fabric_req,2); ?></td>
                        <td align="right" width="50" title=""><? echo $unit_of_measurement[$val["fab_uom"]]; ?></td>
                        <td align="right" width="70" bgcolor="#FFFFCC"><? echo number_format($tot_fabric_req_cost,2); ?></td>
                        <td align="right" width="70" title="Grey Qty"><a href="javascript:open_wo_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','<? echo $bprat_id;?>','<? echo $deter_id;?>','<? echo $gmt_color;?>','<? echo $job_no;?>','WO Info','wo_popup')"><? echo number_format($booking_req,2); ?></a></td>
                        <td align="right" width="70" title=""><?  echo number_format($booking_amount,2); ?></td>
                        <td align="center" width="120" style="word-break:break-all"  title="BookingNo=<? echo $booking_no;?>"><div style="word-wrap:break-word:120px;"><? $supp_com= rtrim($booking_supp_arr[$job_no]['supp_com'],',');
						$supp_coms=implode(",",array_unique(explode(",",$supp_com)));echo $supp_coms; ?></div></td>
                        <td align="center" width="100" title="PI No"><div style="word-wrap:break-word:100px;"><? echo $pi_no; ?></div></td>
                        <td align="right" width="70" title="Woven Fin Recv"><a href="javascript:open_wo_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','<? echo $bprat_id;?>','<? echo $deter_id;?>','<? echo $gmt_color;?>','<? echo $job_no;?>','Fin Recv Info','fin_recv_popup')"><? echo number_format($kniting_prod_recv,2); ?></a></td>
                        <td align="right" width="70" title="Gmt Print Recv"><? echo number_format($kniting_prod_recv_amt,2); ?></td>
                        <td align="center" width="70" title="Wo Qty-Fin Recv Qty"><?  $recv_balance=$booking_req-$kniting_prod_recv;echo number_format($recv_balance,2);?></td>
                        <td align="right" width="70" title="Fin Issue"><a href="javascript:open_wo_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','<? echo $bprat_id;?>','<? echo $deter_id;?>','<? echo $gmt_color;?>','<? echo $job_no;?>','Fin Issue Info','fin_issue_popup')"><?   echo number_format($kniting_prod_issue,2); ?></a></td>
                        <td align="right" width="70"><? echo number_format($kniting_prod_issue_amt); ?></td>
                        <td align="right" width="70" title="Recv-Issue"><? $left_over_bal=$kniting_prod_recv-$kniting_prod_issue;echo number_format($left_over_bal,2);//sew_reject_qnty ?></td>
                        <td align="right" width="" title="Amount Recv-Issue"><? $left_over_bal_amount=$kniting_prod_recv_amt-$kniting_prod_issue_amt;
                         echo number_format($left_over_bal_amount,2); ?></td>

							</tr>
							<?
							
							$total_fabric_req+=$tot_fabric_req;
							$total_fabric_req_cost+=$tot_fabric_req_cost;
							$total_booking_req+=$booking_req;
							$total_fabric_amount+=$booking_amount;	
							$total_kniting_prod_recv+=$kniting_prod_recv;
							$total_kniting_prod_recv_amt+=$kniting_prod_recv_amt;
							$total_fab_recv_balance+=$recv_balance;
							
							$total_kniting_prod_issue+=$kniting_prod_issue;
							$total_kniting_prod_issue_amt+=$kniting_prod_issue_amt;
							$total_left_over_bal+=$left_over_bal;
							$total_left_over_bal_amount+=$left_over_bal_amount;
							
						
							$i++;$j++;$b++;$f++;
						
							}
						  }
						 }
						}
					
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="30">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="100">Total :</td>   
                    <td width="100">&nbsp;</td>
                    
                    <td width="70">&nbsp;</td> 
                    <td width="70" align="right" id="" bgcolor="#FFFFCC"><? //echo number_format($tot_order_qty); ?></td>
                    <td width="100" align="right" id=""><? //echo number_format($tot_yarn_req_qty,2); ?></td>
                    <td width="70" align="right" id="td_order_qty"><? echo number_format($total_po_qnty,2); ?></td>
                    
                    <td width="40" align="right" id=""><? //echo number_format($tot_po_qnty_pcs,2); ?></td>
                    <td width="70" align="right" id="td_order_qty_pcs"><? echo number_format($total_po_qnty_pcs,2); ?></td>
                    <td width="70" align="right" id=""><? //echo number_format($tot_knit_prod_qty,2); ?></td>
                    
                    <td width="100" align="right" id=""><? //echo number_format($total_grey_rec_qty,2); ?></td>
                    <td width="100" align="right" id=""><? //echo number_format($total_under_over_prod,2); ?></td>
                    <td width="150" align="right" id=""><? //echo number_format($total_knit_issuedToDyeQnty,2); ?></td>
                    <td width="70" align="right" id=""><? //echo number_format($total_knit_left_over,2); ?></td>
                    <td width="70" align="right" id=""><? //echo number_format($tot_fin_req_qty,2); ?></td>
                    <td width="70" align="right" id=""><? //echo number_format($tot_total_finishing_prod,2); ?></td>
                    
                    <td width="100" align="right" id=""><? //echo number_format($total_fin_fab_recv,2); ?></td>
                    <td width="100" align="right" id=""><? //echo number_format($tot_fin_less_over_prod,2); ?></td>
                    
                    <td width="70" align="right" id=""><? echo number_format($total_fabric_req,2); ?></td>
                    <td width="50" align="right" id=""><? //echo number_format($tot_finish_left_over,2); ?></td> 
                    <td width="70" align="right" id="" bgcolor="#FFFFCC"><? echo number_format($total_fabric_req_cost); ?></td>
                    <td width="70" align="right" id=""><? echo number_format($total_fabric_req); ?></td>
                    <td width="70" align="right" id=""><? echo number_format($total_fabric_amount); ?></td>
                    
                    <td width="120" align="right" id=""><? // echo number_format($tot_cutting_excess_lessQty); ?></td>
                    <td width="100" align="right" id=""><? //echo number_format($tot_total_print_issued); ?></td>
                    <td width="70" align="right" id=""><? echo number_format($total_kniting_prod_recv); ?></td>
                    <td width="70" align="right" id=""><? echo number_format($total_kniting_prod_recv_amt); ?></td>
                    <td width="70" align="right" id=""><? echo number_format($total_fab_recv_balance); ?></td>
                    
                    <td width="70" align="right" id=""><? echo number_format($total_kniting_prod_issue); ?></td>
                    <td width="70" align="right" id=""><? echo number_format($total_kniting_prod_issue_amt); ?></td>
                    <td width="70" align="right" id=""><? echo number_format($total_left_over_bal); ?></td>
                    <td align="right" width="" id=""><? echo number_format($total_left_over_bal_amount); ?></td>
                </tr>
           </table>
           <?
			 //Both part End
			
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
    echo "$html**$filename**$cbo_search_type**$type";
    exit();


}


if($action=='wo_popup')
{
	echo load_html_head_contents("WO Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id."*".$body_id;die;

	//echo $ratio;die;

?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
    
 <div  style="width:750px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
  </div>
    <div  id="report_div" style="100%;" align="center">
   
       <table width="750" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="8">WO Summary</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="100">WO No</th>
                    <th width="70">Wo Date</th>
                    <th width="100">Body Part</th>
                    <th width="200">Fabric Desc.</th>
                    <th width="70">Wo Qty</th>
                    <th width="50">UOM</th>
                    <th width="">Supplier</th>
                   
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            <?
		 $sql_wo="select a.id, a.booking_no,a.pay_mode,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,b.gmts_color_id,a.booking_type,b.po_break_down_id as po_id,
	(b.grey_fab_qnty) as grey_fab_qnty,b.job_no,b.amount,c.body_part_id as bpart_id,c.color_size_sensitive,c.lib_yarn_count_deter_id as deter_id,c.construction,c.composition,c.gsm_weight,c.uom
	from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=1 and b.fin_fab_qnty>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and b.po_break_down_id in ($po_id)
	and b.gmts_color_id in ($color_id) and c.body_part_id in ($body_id)  and c.lib_yarn_count_deter_id in ($deterId)
	";
	$sql_wo_res=sql_select($sql_wo);
	foreach ($sql_wo_res as $row)
	{
	
		
		if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
		{
			$com_supplier=$company_library[$row[csf("supplier_id")]];
		}
		else
		{
			$com_supplier=$supplier_library[$row[csf("supplier_id")]];
		}
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['grey']+=$row[csf("grey_fab_qnty")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['amount']+=$row[csf("amount")]/$row[csf("exchange_rate")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['desc']=$body_part[$row[csf("bpart_id")]].','.$row[csf("construction")].','.$row[csf("composition")].','.$row[csf("gsm_weight")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['supp']=$com_supplier;
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['uom']=$row[csf("uom")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['booking_date']=$row[csf("booking_date")];
	}
	 
		  
		  $i=1;$tot_wo_qnty=0;
			foreach($booking_req_arr as $booking=>$book_Data)
			{
			 foreach($book_Data as $body_id=>$body_Data)
			 {
			  foreach($body_Data as $deterId=>$row)
			 {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
                    <td><p><? echo $i; ?>&nbsp;</p></td>
                    <td><p><? echo $booking; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[('booking_date')]);; ?>&nbsp;</td>
                    <td align="center"><? echo   $body_part[$body_id]; ?>&nbsp;</td>
                    <td align="center"><? echo $row[('desc')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[('grey')],2); ?>&nbsp;</td>
                    <td align="center"><? echo $unit_of_measurement[$row[('uom')]]; ?>&nbsp;</td>
                    <td align="center"><? echo $row[('supp')]; ?>&nbsp;</td>
                    
                </tr>
            <?
				$tot_wo_qnty+=$row[('grey')];
				$i++;
			  }
			 }
			}
			?>
            </tbody>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_wo_qnty,2,'.',''); ?>&nbsp;</th>
                    <th align="right"></th>
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>
          <script>   setFilterGrid("table_body_popup",-1);</script>
        </div>
<?

	exit();
}
if($action=='fin_recv_popup')
{
	echo load_html_head_contents("Recv Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id."*".$body_id;die;

	//echo $ratio;die;

?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
    
 <div  style="width:1150px;" align="center">
      <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
  </div>
    <div  id="report_div" style="100%;" align="center">
   
       <table width="1150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="15">Recevied Details</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="60">Prod. ID</th>
                    <th width="110">MRR No</th>
                    <th width="70">Challan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="100">WO No</th>
                    <th width="100">PI No</th>
                    <th width="100">Body Part</th>
                    <th width="150">Fabric Desc.</th>
                    <th width="70">Recv. Qty</th>
                    <th width="70">Rate</th>
                     <th width="70">Amount</th>
                    <th width="40">Uom</th> 
                    <th width="100">Supplier</th> 
                    <th width="">Insert By</th>
                    
                   
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            <?
	


	$prodKnitDataArr=sql_select("select c.recv_number,c.challan_no,c.supplier_id,c.booking_no,c.booking_id,c.receive_date,c.inserted_by,a.po_breakdown_id as po_id,b.uom,b.prod_id,b.fabric_description_id as deter_id,b.rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form in(17) THEN a.quantity ELSE 0 END) AS knit_qnty_rec
	from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category=3 and a.entry_form in(17) and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_breakdown_id in ($po_id)
	and a.color_id in ($color_id) and b.body_part_id in($body_id) and b.fabric_description_id in($deterId) ");// and c.receive_basis<>9
	 
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["knit_qnty_rec"]+=$row[csf("knit_qnty_rec")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["knit_qnty_rec_amt"]+=$row[csf("knit_qnty_rec")]*$row[csf("rate")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["uom"]=$row[csf("uom")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["inserted_by"]=$row[csf("inserted_by")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["receive_date"]=$row[csf("receive_date")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["challan_no"]=$row[csf("challan_no")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["booking_no"]=$row[csf("booking_no")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["supplier_id"]=$row[csf("supplier_id")];
	$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
	$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
	}
	$desc_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$prod_id_arr).")",'id','product_name_details');
	//echo "select id, item_description from product_details_master where id in(".implode(",",$prod_id_arr).")";

	/*$issueprodKnitDataArr=sql_select("select a.po_breakdown_id as po_id,d.detarmination_id as deter_id,b.cons_rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form in(19) THEN a.quantity ELSE 0 END) AS knit_qnty_issue
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c,product_details_master d where a.trans_id=b.id and b.mst_id=c.id and d.id=b.prod_id and a.prod_id=d.id and c.item_category=3 and a.entry_form in(19) and c.entry_form in(19) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 ");// and c.receive_basis<>9
	$issue_kniting_prod_arr=array();
	foreach($issueprodKnitDataArr as $row)
	{
	$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_issue"]+=$row[csf("knit_qnty_issue")];
	$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_issue_amt"]+=$row[csf("knit_qnty_issue")]*$row[csf("cons_rate")];
	}
	*/
	$sql="select b.pi_id,a.id,a.item_category_id,a.pi_number,a.pi_date,a.importer_id, b.work_order_no,b.work_order_id,b.item_group,b.item_prod_id,
	b.determination_id
	from com_pi_master_details  a,  com_pi_item_details b
	where a.id=b.pi_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	and b.is_deleted=0 and b.work_order_id in(".implode(",",$booking_id_arr).")";
	  //echo $sql ; // die;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$piDataArr[$row[csf("work_order_no")]].=$row[csf("pi_number")].',';
	}
	
	 
	 $user_library=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );

		  
		  $i=1;$tot_recv_qnty=$tot_recv_amt=0;
			foreach($kniting_prod_arr as $recvNo=>$recv_Data)
			{
			 foreach($recv_Data as $prod_id=>$prod_Data)
			 {
			  foreach($prod_Data as $bodyId=>$row)
			 {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";//change_date_format
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
                    <td><p><? echo $i; ?>&nbsp;</p></td>
                    <td><p><? echo $prod_id; ?>&nbsp;</p></td>
                    <td><p><? echo $recvNo; ?>&nbsp;</p></td>
                    <td><p><? echo $row[('challan_no')]; ?>&nbsp;</p></td>
                    <td><p><? echo change_date_format($row[('receive_date')]); ?>&nbsp;</p></td>
                    <td><p><? echo $row[('booking_no')]; ?>&nbsp;</p></td>
                    <td><p><? echo $piDataArr[$row[('booking_no')]]; ?>&nbsp;</p></td>
                    <td><p><? echo  $body_part[$bodyId]; ?>&nbsp;</p></td>
                     <td><p><? echo  $desc_arr[$prod_id]; ?>&nbsp;</p></td>
                    <td align="center"><? echo number_format($row[('knit_qnty_rec')]);; ?>&nbsp;</td>
                    <td align="center"><? echo  number_format($row[('knit_qnty_rec_amt')]/$row[('knit_qnty_rec')],2); ?>&nbsp;</td>
                    <td align="center"><? echo $row[('knit_qnty_rec_amt')]; ?>&nbsp;</td>
                    <td align="right"><? echo $unit_of_measurement[$row[('uom')]];///supplier_id ?>&nbsp;</td>
                    <td align="center"><? echo $supplier_library[$row[('supplier_id')]]; ?>&nbsp;</td>
                    <td align="right"><? echo $user_library[$row[('inserted_by')]];///unit_of_measurement ?>&nbsp;</td>
                    
                </tr>
            <?
				$tot_recv_qnty+=$row[('knit_qnty_rec')];
				$tot_recv_amt+=$row[('knit_qnty_rec_amt')];
				$i++;
			  }
			 }
			}
			?>
            </tbody>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                      <th align="right">&nbsp;</th>
                     <th align="right">Total</th>
                     <th align="right"><? echo number_format($tot_recv_qnty,2,'.',''); ?></th>
                     <th align="right">&nbsp;</th>
                     <th align="right"><? echo number_format($tot_recv_amt,2,'.',''); ?>&nbsp;</th>
                     <th align="right">&nbsp;</th>
                  
                    <th align="right"></th>
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>
          <script>   setFilterGrid("table_body_popup",-1);</script>
        </div>
<?

	exit();
}
if($action=='fin_issue_popup')
{
	echo load_html_head_contents("Issue info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id."*".$body_id;die;

	//echo $ratio;die;
	$user_library=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );
	$style_ref_library=return_library_array( "select job_no,style_ref_no from wo_po_details_master", "job_no", "style_ref_no"  );

?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
    
 <div  style="width:1040px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
  </div>
    <div  id="report_div" style="100%;" align="center">
   
       <table width="1040" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="13">Isseue Details</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="60">Prod. ID</th>
                    <th width="110">Issue No</th>
                    <th width="70">Issue. Date</th>
                    <th width="100">Job No</th>
                    <th width="100">Style No</th>
                    <th width="100">Body Part</th>
                    <th width="150">Fabric Desc.</th>
                    <th width="70">Issue. Qty</th>
                    <th width="50">Rate</th>
                    <th width="70">Amount</th>
                    <th width="70">Uom</th> 
                    <th width="">Insert By</th>
                    
                   
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            <?


	
	$issueprodKnitDataArr="select c.issue_number,c.issue_date,c.inserted_by,a.po_breakdown_id as po_id,b.prod_id,d.detarmination_id as deter_id,b.cons_rate,b.body_part_id,b.order_uom,a.color_id,
	(CASE WHEN a.entry_form in(19) THEN a.quantity ELSE 0 END) AS knit_qnty_issue
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c,product_details_master d where a.trans_id=b.id and b.mst_id=c.id and d.id=b.prod_id and a.prod_id=d.id and c.item_category=3 and a.entry_form in(19) and c.entry_form in(19) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_breakdown_id in ($po_id) and a.color_id in ($color_id) and b.body_part_id in($body_id) and d.detarmination_id in($deterId)";// and c.receive_basis<>9
	$issue_prodKnitDataArr=sql_select($issueprodKnitDataArr);
	$issue_kniting_prod_arr=array();
	foreach($issue_prodKnitDataArr as $row)
	{
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["knit_qnty_issue"]+=$row[csf("knit_qnty_issue")];
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["knit_qnty_issue_amt"]+=$row[csf("knit_qnty_issue")]*$row[csf("cons_rate")];
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["issue_date"]=$row[csf("issue_date")];
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["order_uom"]=$row[csf("order_uom")];
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["inserted_by"]=$row[csf("inserted_by")];
	$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
	}
	$desc_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$prod_id_arr).")",'id','product_name_details');
		  $i=1;$tot_knit_qnty_issue_amt=$tot_knit_qnty_issue=0;
			foreach($issue_kniting_prod_arr as $issueNo=>$issue_Data)
			{
			 foreach($issue_Data as $prod_id=>$prod_Data)
			 {
			  foreach($prod_Data as $bodyId=>$row)
			  {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
                    <td><p><? echo $i; ?>&nbsp;</p></td>
                    <td><p><? echo $prod_id; ?>&nbsp;</p></td>
                    <td><p><? echo $issueNo; ?>&nbsp;</p></td>
                    <td><p><? echo change_date_format($row[('issue_date')]); ?>&nbsp;</p></td>
                    <td><p><? echo $jobNo; ?>&nbsp;</p></td>
                    <td><p><? echo $style_ref_library[$jobNo]; ?>&nbsp;</p></td>
                    <td><p><? echo $body_part[$bodyId]; ?>&nbsp;</p></td>
                    <td><p><? echo $desc_arr[$prod_id]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[('knit_qnty_issue')]);; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[('knit_qnty_issue_amt')]/$row[('knit_qnty_issue')],2); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[('knit_qnty_issue_amt')],2);///unit_of_measurement ?>&nbsp;</td>
                     <td align="center"><? echo $unit_of_measurement[$row[('order_uom')]]; ?>&nbsp;</td>
                    <td align="center"><? echo $user_library[$row['inserted_by']]; ?>&nbsp;</td>
                  
                    
                </tr>
            <?
				$tot_knit_qnty_issue+=$row[('knit_qnty_issue')];
				$tot_knit_qnty_issue_amt+=$row[('knit_qnty_issue_amt')];
				$i++;
			  }
			 }
			}
			?>
            </tbody>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right"></th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_knit_qnty_issue,2,'.',''); ?>;</th>
                    <th align="right"></th>
                    <th align="right"><? echo number_format($tot_knit_qnty_issue_amt,2,'.',''); ?>&nbsp;</th>
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>
          <script>   setFilterGrid("table_body_popup",-1);</script>
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