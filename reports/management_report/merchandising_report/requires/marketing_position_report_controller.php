<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.others.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_surch_list_view', 'search_div', 'marketing_position_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $txt_style_ref; ?>'+'**'+'<? echo $type_id; ?>', 'order_surch_list_view', 'search_div', 'marketing_position_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
	$year_cond="";
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
		if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref) ";
		else $style_cond="and b.job_no_prefix_num in($txt_style_ref)";
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
	//echo $year_cond."jahid";die;
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
	$txt_ex_date_form=str_replace("'","",$txt_ex_date_form);
	$txt_ex_date_to=str_replace("'","",$txt_ex_date_to);
	$type=str_replace("'","",$type);
//echo $txt_order.'dds';
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
		$ship_date_cond="and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}
	
	$job_no_cond="";$ref_no_cond="";$file_no_cond="";$order_cond="";$order_cond_id="";
	if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
	
	$order_cond="";
	if(trim($ref_no)!="") $ref_no_cond="and b.grouping in('$ref_no')";
	if(trim($file_no)!="") $file_no_cond="and b.file_no in('$file_no')";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	if($txt_order_id!="") { $order_cond_id.="and b.id in($txt_order_id)";}
	if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	//echo $order_cond;die;
	
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	
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
			//$date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
			//$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
		
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if(str_replace("'","",$txt_style_ref) !=''){
			$condition->job_no_prefix_num("=$txt_style_ref");
		}
		if(str_replace("'","",$txt_file_no)!='')
		{
			$condition->file_no("=$file_no");
		}
		if(str_replace("'","",$txt_internal_ref)!='')
		{
			$condition->grouping("='$ref_no'");
		}
		
		if(str_replace("'","",$order_cond_id)!='' && str_replace("'","",$txt_order)!='')
		{
			$condition->po_id("in($order_cond_id)");
		}
		if(str_replace("'","",$txt_order_id)=='' && str_replace("'","",$txt_order)!='')
		{
			$condition->po_number("='$txt_order'");
		}
		
		if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			}
			
			$condition->init();
			
			$other= new other($condition);
			//echo $other->getQuery(); die;
			$other_costing_arr=$other->getAmountArray_by_order();
			$costPerArr=$condition->getCostingPerArr();
		
	 $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num,a.team_leader,$select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.grouping,b.pub_shipment_date,b.po_total_price
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no=b.job_no_mst  and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_quantity>0 $buyer_id_cond $year_cond $job_no_cond $ship_date_cond $order_cond $order_cond_id  $ref_no_cond $file_no_cond order by a.team_leader,b.pub_shipment_date"; 
	
	//echo $sql_po;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$$shipdate_data_arr=array();$all_po_id=""; $JobArr=array();
	foreach($sql_po_result as $row)
	{
		$date_type=date("Y-M",strtotime($row[csf("pub_shipment_date")]));
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		$result_data_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("grouping")]][$date_type][$row[csf("po_id")]]["po_id"].=$row[csf("po_id")].',';
		$result_data_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("grouping")]][$date_type]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("grouping")]][$date_type]["plan_cut"]+=$row[csf("plan_cut")];
		$result_data_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("grouping")]][$date_type]["po_qnty"]+=$row[csf("po_qnty")];
		$result_data_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("grouping")]][$date_type]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("grouping")]][$date_type]["po_total_price"]=$row[csf("po_total_price")];
		$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
		$result_data_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("grouping")]][$date_type]["cm_cost"]+=$cm_cost;
		$costPerQty=$costPerArr[$row[csf("job_no")]];

		$result_data_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("grouping")]][$date_type]["costPerQty"]=$costPerQty;
		
		$shipdate_data_arr[date("Y-M",strtotime($row[csf("pub_shipment_date")]))]=date("Y-M",strtotime($row[csf("pub_shipment_date")]));
		
	
		$JobArr[]="'".$row[csf('job_no')]."'";
	}
	//print_r($shipdate_data_arr);
	
  $team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	//echo $yarn->getQuery(); die;
	//$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
	//$yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	
	if(empty($all_po_id))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	$td_width= count($shipdate_data_arr)*320;
	$tbl_width=350+$td_width;
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
                        <th width="20" rowspan="2">SL</th>
                        <th width="130" rowspan="2">Team Leader</th>
                        <th width="" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">IR no.</th>
                       <?
						foreach($shipdate_data_arr as $mon_key=>$mon)
						{
						?>
						<th  align="center" colspan="4">
						<? 
						echo $mon_key;
						?>
						</th>
						<?
						}
					 ?>
                    </tr>
                     <tr>
                      <?
						foreach($shipdate_data_arr as $mon_key=>$mon)
						{
						?>
						<th width="80" align="center"> Total </th>
                        <th width="80" align="center"> Value </th>
                        <th width="80" align="center">Total CM </th>
                        <th width="80" align="center"> Margin </th>
                        <?
						}
						?>
                    </tr>
                
                </thead>
           </table>
            <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

                <?
				 
					$i=1;$tot_po_value=$tot_cm_cost=$tot_order_qty=$total_margin=0;
						foreach($result_data_arr as $team_id=>$team_val)
						{
							$sub_tot_order_qty=$sub_po_value=$sub_tot_cm_cost=$sub_tot_margin=0;
						 foreach($team_val as $buyer_id=>$buyer_val)
						 {
						  foreach($buyer_val as $ref_no=>$date_val)
						  {
							foreach($date_val as $mon_key=>$val)
						    {
							$ratio=$val["ratio"]; 
							$tot_po_qnty=$val["po_qnty"];
							
							$po_value=$val["po_total_price"];
							$cm_cost=$val["cm_cost"];
							$costPerQty=$val["costPerQty"];
							//echo $costPerQty.'ddd';
							$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
							if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
							else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
							else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
							else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$ratio;

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
								<td width="20"><? echo $i; ?></td>
								<td width="130" style="word-break:break-all"><? echo $team_leader_arr[$team_id]; ?></td>
								<td width=""  style="word-break:break-all" ><? echo $buyer_arr[$buyer_id]; ?></td> 
								<td width="70" style="word-break:break-all"><? echo $ref_no; ?></td>
                                <?
								foreach($shipdate_data_arr as $mon_keys=>$mon)
								{
									if($cm_cost>0)
									{
									$tot_cm_cost=($cm_cost/$costPerQty)*$tot_po_qnty;
									} else $tot_cm_cost="";
									$tot_margin=$po_value-$tot_cm_cost;
								?>
								<td width="80" align="right"><? echo number_format($tot_po_qnty,0); ?></td>
								<td width="80" align="right"><? echo number_format($po_value,2); ?></td>
								<td width="80" align="right" title="Tot CM(<? echo $cm_cost; ?>)/Costing Per(<? echo $costPerQty; ?>)*PO Qty" bgcolor="#FFFFCC"><p><? echo number_format($tot_cm_cost,2); ?></p></td>
								<td width="80" align="right" title="PO Value-Tot CM"><p><?  echo number_format($tot_margin,2); ?></p></td>
                                <?
                                } ?>
							</tr>
							<?
							$sub_tot_order_qty+=$tot_po_qnty;
							$sub_po_value+=$po_value;
							$sub_tot_cm_cost+=$tot_cm_cost;
							$sub_tot_margin+=$tot_margin;
							
							$tot_order_qty+=$tot_po_qnty;
							$tot_po_value+=$po_value;
							$tot_cm_cost+=$tot_cm_cost;
							$total_margin+=$tot_margin;
						
							$i++;
						 	}
						   }
						  }
						  ?>
                           <tr style="font-size:13px; background:#CCC">
                            <td colspan="4" align="right"><b><? echo $team_leader_arr[$team_id]; ?>&nbsp;&nbsp;Total </b></td>
                           
                             <?
                            foreach($shipdate_data_arr as $mon_key=>$mon)
                            {
                            ?>
                             <td width="80" align="right"><b><?  echo number_format($sub_tot_order_qty,2); ?></b></td>
                             <td width="80" align="right"><b><?  echo number_format($sub_po_value,2); ?></b></td>
                             <td  width="80" align="right" id=""><b><? echo number_format($sub_tot_cm_cost); ?></b></td>
                            <td width="80" align="right" id=""><b><? echo number_format($sub_tot_margin); ?></b></td>
                            <?
                            }?>
                          </tr>
                          <?
						}// Loop  End
					
					
					?>
                </table>
            </div>
            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                   
                   
                    <td  colspan="4"  align="right">Total Order in Hand</td>
                     <?
					foreach($shipdate_data_arr as $mon_key=>$mon)
					{
					?>
                     <td width="80" align="right"><?  echo number_format($tot_order_qty,2); ?></td>
                     <td width="80" align="right"><?  echo number_format($tot_po_value,2); ?></td>
                    <td  width="80" align="right" id=""><? echo number_format($tot_cm_cost); ?></td>
                    <td width="80" align="right" id=""><? echo number_format($total_margin); ?></td>
                    <?
                    }?>
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
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) ");
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
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty); ?></th>
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
