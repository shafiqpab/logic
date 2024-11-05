<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 80, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/job_wise_rejection_status_report_controller', this.value, 'load_drop_down_season', 'season_td');");
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 80, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/job_wise_rejection_status_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 70, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}
//item style------------------------------//
if($action=="style_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
	?>
	<script type="text/javascript">
	  function js_set_value(id)
		  {
			//alert(id);
			document.getElementById('selected_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
	<?
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
	    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
		if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
		$job_year_cond="";
		if($cbo_year!=0)
		{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
	    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
		}
		if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";

		if($db_type==2) $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		else if($db_type==0) $group_field="group_concat(distinct b.po_number ) as po_number";

		$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company  and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date ";
		// echo $sql;


	//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	             <th width="40">Year</th>
	             <th width="50">Job no</th>
	            <th width="100">Style</th>
	            <th width="">Po number</th>

	        </tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
		 foreach($rows as $data)
		 {
			 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
	  ?>
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('style_ref_no')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
	        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
			<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
	        <td width=""><p><? echo $po_num; ?></p></td>

		</tr>
	    <? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	disconnect($con);
	exit();
}
if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array;var selected_id_arr = new Array;

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

			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_style.push( str[3] );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
			}
			 var id = ''; var name = '';var style = '';
			 for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );

			 $('#hide_job_id').val( id );
			// $('#hide_job_no').val( name );
			 $('#hide_style_no').val( style );
			$("#hide_job_no").val(name);
			  parent.emailwindow.hide();
		}

    </script>
	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th class="must_entry_caption">Company Name</th>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;">
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <?
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'job_wise_rejection_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>
	                        <td align="center" id="buyer_td">
	                        	 <?
									echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>
	                        <td align="center">
	                    	<?
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
							?>
	                        </td>
	                        <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>
	                        <td align="center" id="search_by_td">
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
	                        </td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'job_wise_rejection_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script type="text/javascript">
		$("#cbo_year").val('<?=$cbo_year;?>');
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

	if($data[1]==0)
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}

	$search_by=$data[2];
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}

	if($search_by==1)
		$search_field="a.job_no_prefix_num";
	else if($search_by==2)
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field like '%$search_string%'";}
	$job_year =$data[4];

	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";


	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc";
    // echo $sql;

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "100,100,50,100","550","220",0, $sql , "js_set_value", "id,job_no_prefix_num,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
   exit();
}

if ($action == "order_wise_search")
 {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon) {
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			if ($('#tr_' + str).css("display") != 'none') {
				toggle(document.getElementById('tr_' + str), '#FFFFCC');

				if (jQuery.inArray(selectID, selected_id) == -1) {
					selected_id.push(selectID);
					selected_name.push(selectDESC);
				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == selectID) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
				}
			}
			var id = '';
			var name = '';
			var job = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}
	</script>
	<?
	extract($_REQUEST);
	//echo $job_no;die;
	if ($company == 0) $company_name = "";
	else $company_name = " and b.company_name=$company";
	if ($buyer == 0) $buyer_name = "";
	else $buyer_name = "and b.buyer_name=$buyer";


	if (str_replace("'", "", $job_id) != "")  $job_cond = "and b.id in(" . str_replace("'", "", $job_id) . ")";
	else  if (str_replace("'", "", $job_no) == "") $job_cond = "";
	else $job_cond = " and b.job_no_prefix_num in (" . str_replace("'", "", $job_no) . ")";

	if (str_replace("'", "", $style_id) != "")  $style_cond = "and b.id in(" . str_replace("'", "", $style_id) . ")";
	else  if (str_replace("'", "", $style_no) == "") $style_cond = "";
	else $style_cond = "and b.style_ref_no='" . $style_no . "'";
    $year="year(b.insert_date)";
	if($db_type==0) $year_field="YEAR(b.insert_date) as year";
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(b.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";
	}
	$sql = "SELECT  a.id ,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$year_field
    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and
    b.status_active=1 and b.is_deleted=0 $company_name  $buyer_name $brand_name_cond $buyer_season_name_cond $buyer_season_year_cond order by job_no_mst";
	//  echo $sql;//die;
	echo create_list_view("list_view", "Order Number,Job No, Year,Style Ref", "150,100,100,150", "550", "310", 0, $sql, "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	$company_id = str_replace("'","",$cbo_company_name);
	$from_date = str_replace( "'", "", $txt_date_from );
	$to_date  = str_replace( "'", "", $txt_date_to );
	$season_id=str_replace("'","",$cbo_season_id);
	$shipping_status=str_replace("'","",$shipping_status);
	$orderStatus=str_replace("'","",$orderStatus);
	$job_year=str_replace("'","",$cbo_year);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$sql_cond="";
	$job_cond_id = "";
	$style_cond = "";
	$order_cond = "";
	$order_status_cond="";
	$sql_cond .= ($company_id!=0) ? " and a.company_name in($company_id)" : "";
	if($company_id == "" || $company_id == 0) $pro_company_name=""; else $pro_company_name="and c.serving_company in($company_id)";
	if (str_replace("'", "", $cbo_buyer_name) == 0)  $buyer_name = "";
	else $buyer_name = "and a.buyer_name=" . str_replace("'", "", $cbo_buyer_name) . "";
	if(str_replace("'","",$cbo_location)==0)  $location_name=""; else $location_name="and d.location=".str_replace("'","",$cbo_location)."";
	if(str_replace("'","",$cbo_location)==0)  $location_name_one=""; else $location_name_one="and a.location_name=".str_replace("'","",$cbo_location)."";
	$sql_cond .= ($from_date!="") ? " and b.pub_shipment_date between '$from_date' and '$to_date'" : "";
	if (str_replace("'", "", $txt_job_no) == "") $job_cond_id = "";
	else $job_cond_id = "and a.job_no_prefix_num='" . str_replace("'", "", $txt_job_no) . "'";
	if (str_replace("'", "", $txt_job_no) == "") $job_cond_id = "";
	else $job_cond_id_one = "and a.job_no='" . str_replace("'", "", $txt_job_no) . "'";
	if (str_replace("'", "", $hidden_style_id) != "")  $style_cond = "and b.id in(" . str_replace("'", "", $hidden_style_id) . ")";
	else  if (str_replace("'", "", $txt_style_no) == "") $style_cond = "";
	else $style_cond = "and a.style_ref_no like '%" . str_replace("'", "", $txt_style_no) . "%' ";
	if($season_id!=0) $season_cond.=" and a.season_buyer_wise=$season_id";
	if($job_year!=0) $year_cond.=" and to_char(a.insert_date,'YYYY')='$job_year'";
	if($orderStatus) $order_status_cond = " and b.is_confirmed=$orderStatus ";
	$sql_cond .= ($shipping_status==0) 	? "" : " and b.shiping_status in($shipping_status)";
	if (str_replace("'", "", $txt_order_no) == "") $order_cond = "";
	else $order_cond = "and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";


    if($type==1)
    {

		 $job_wise_summary_sql="SELECT a.buyer_name,a.style_ref_no,b.job_no_mst as job_id,b.id as po_id,b.po_quantity,d.po_break_down_id,d.grey_fab_qnty from  wo_po_details_master a,wo_po_break_down b,wo_booking_dtls d where a.id=b.job_id  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$job_wise_summary=sql_select($job_wise_summary_sql);

		$job_summary_arr=array();

		foreach($job_wise_summary as $row)
		{
			$job_summary_arr[$row[csf('job_id')]]['buyer_name']=$row[csf('buyer_name')];
			$job_summary_arr[$row[csf('job_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$job_summary_arr[$row[csf('job_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];

		}
		// echo '<pre>';
		// print_r($job_summary_arr);
		// echo '</pre>';

		$job_wise_order_sql="SELECT a.buyer_name,a.style_ref_no,b.job_no_mst as job_id,b.id as po_id,b.po_quantity from  wo_po_details_master a,wo_po_break_down b where a.id=b.job_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$job_wise_order=sql_select($job_wise_order_sql);

		$job_wise_order_arr=array();

		foreach($job_wise_order as $row)
		{
			$job_wise_order_arr[$row[csf('job_id')]]['order_qty']+=$row[csf('po_quantity')];
		}






		 $kniting_sql="SELECT b.id as po_id,b.job_no_mst as job_id,c.reject_fabric_receive,c.grey_receive_qnty from wo_po_details_master a,wo_po_break_down b,pro_grey_prod_entry_dtls c,order_wise_pro_details d where a.id=b.job_id and c.id=d.dtls_id and b.id=d.po_breakdown_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and d.entry_form=2 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		 $kniting_total_sql=sql_select($kniting_sql);
         $kniting_arr=array();
		 foreach($kniting_total_sql as $row)
		 {
			$kniting_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_fabric_receive')];
			$kniting_arr[$row[csf('job_id')]]['grey_receive_qnty']+=$row[csf('grey_receive_qnty')];
		 }

		 $dyeing_sql="SELECT b.id as po_id,b.job_no_mst as job_id,e.reject_qnty,e.roll_weight from wo_po_details_master a,wo_po_break_down b,   pro_finish_fabric_rcv_dtls  c,pro_roll_details d, pro_qc_result_mst e where a.id=b.job_id and c.id=d.dtls_id and d.dtls_id=e.pro_dtls_id and b.id=d.po_breakdown_id and e.barcode_no=d.barcode_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.entry_form=66 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		 $dyeing_total_sql=sql_select($dyeing_sql);
         $dyeing_arr=array();
		 foreach($dyeing_total_sql as $row)
		 {
			$dyeing_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qnty')];
			$dyeing_arr[$row[csf('job_id')]]['roll_weight']+=$row[csf('roll_weight')];
		 }
        //  echo '<pre>';
		//  print_r($dyeing_arr);
		//  echo '</pre>';

		$cutting_qc_sql="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=1 and d.production_type=1 AND d.cut_no is not null $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond $pro_company_name";

		$total_cutting_sql=sql_select($cutting_qc_sql);
		$cutting_arr=array();

		foreach($total_cutting_sql as $row)
		{
			$cutting_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$cutting_arr[$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		}

		//  echo '<pre>';
	    //  print_r($cutting_arr);
	    //  echo '</pre>';

		 $printing_rcv_reject="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=3 and d.production_type=3 and c.embel_name=1 AND d.cut_no is not null $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		 $total_printing_rcv_reject=sql_select($printing_rcv_reject);
         $printing_r_arr=array();

		 foreach($total_printing_rcv_reject as $row)
		 {
			$printing_r_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$printing_r_arr[$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		 }
	    //  echo '<pre>';
	    //  print_r($printing_r_arr);
	    //  echo '</pre>';
		$embrodiery_reject="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=3 and d.production_type=3 and c.embel_name=2 AND d.cut_no is not null $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		 $total_embrodiery=sql_select($embrodiery_reject);
         $em_arr=array();

		 foreach($total_embrodiery as $row)
		 {
			$em_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$em_arr[$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		 }
		//   echo '<pre>';
	    //  print_r($em_arr);
	    //  echo '</pre>';
		$special_reject="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=3 and d.production_type=3 and c.embel_name=4 AND d.cut_no is not null $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		 $total_special=sql_select($special_reject);
         $sp_arr=array();

		 foreach($total_special as $row)
		 {
			$sp_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$sp_arr[$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		 }
		//   echo '<pre>';
	    //  print_r($sp_arr);
	    //  echo '</pre>';

		$sewing_reject="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=5 and d.production_type=5  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		 $total_sewing=sql_select($sewing_reject);
         $sewing_arr=array();

		 foreach($total_sewing as $row)
		 {
			$sewing_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$sewing_arr[$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		 }
		//   echo '<pre>';
	    //  print_r($sewing_arr);
	    //  echo '</pre>';
	  	$wash_reject="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=3 and d.production_type=3 and  c.embel_name=3  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";
		$total_wash_reject=sql_select($wash_reject);
		$wash_arr=array();

		foreach($total_wash_reject as $row)
		{
			$wash_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$wash_arr[$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		}

	    //   echo '<pre>';
	    //  print_r($wash_arr);
	    //  echo '</pre>';

	   $poly_reject="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=11 and d.production_type=11  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";


	   $total_poly_reject=sql_select($poly_reject);
	   $poly_arr=array();

	   foreach($total_poly_reject as $row)
	   {
		   $poly_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
		   $poly_arr[$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
	   }

	   $finishing_reject="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=8 and d.production_type=8  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

	   $total_finish_reject=sql_select($finishing_reject);
	   $finish_arr=array();

	   foreach($total_finish_reject as $row)
	   {
		   $finish_arr[$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
		   $finish_arr[$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
	   }

	    //   echo '<pre>';
	    //  print_r($finish_arr);
	    //  echo '</pre>';

		$pro_ex_factory_sql="SELECT b.id as po_id,b.job_no_mst as job_id,b.po_quantity,c.ex_factory_qnty from  wo_po_details_master a,wo_po_break_down b,pro_ex_factory_mst c  WHERE a.id=b.job_id and b.id=c.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form !=85 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		$total_ex_factory_sql=sql_select($pro_ex_factory_sql);
        $total_ex_factory_arr=array();
		foreach($total_ex_factory_sql as $row)
		{
			$total_ex_factory_arr[$row[csf('job_id')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];

		}
	    //  echo '<pre>';
	    //  print_r($total_ex_factory_arr);
	    //  echo '</pre>';

		// ===============================Job Wise End=======================================//
        //  =============================For Second Table Query Start Here=========================//
		$main_sql="SELECT a.buyer_name,b.po_number,b.id as po_id,b.job_no_mst as job_id,a.style_ref_no,b.is_confirmed,b.pub_shipment_date from wo_po_details_master a,wo_po_break_down b WHERE a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		$purpose_sql=sql_select($main_sql);
		$purpose_arr=array();

		foreach($purpose_sql as $row)
		{
			$purpose_arr[$row[csf('job_id')]][$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
			$purpose_arr[$row[csf('job_id')]][$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
			$purpose_arr[$row[csf('job_id')]][$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$purpose_arr[$row[csf('job_id')]][$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
			$purpose_arr[$row[csf('job_id')]][$row[csf('po_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
		}
		//   echo '<pre>';
	    //  print_r($purpose_arr);
	    //  echo '</pre>';
		$printing_rcv="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=3 and d.production_type=3 and c.embel_name=1 AND d.cut_no is not null $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		$total_printing_rcv=sql_select($printing_rcv);
		$printing_rcv_arr=array();

		foreach($total_printing_rcv as $row)
		{
		   $printing_rcv_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
		   $printing_rcv_arr[$row[csf('po_id')]][$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		}
	   //  echo '<pre>';
	   //  print_r($printing_rcv_arr);
	   //  echo '</pre>';
		$grey_qnty_sql="SELECT b.job_no_mst as job_id,b.id as po_id,d.grey_fab_qnty from  wo_po_details_master a,wo_po_break_down b,wo_booking_dtls d where a.id=b.job_id  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$total_grey_qnty_sql=sql_select($grey_qnty_sql);
		$total_grey_arr=array();

		foreach($total_grey_qnty_sql as $row)
		{
            $total_grey_arr[$row[csf('po_id')]][$row[csf('job_id')]]['grey_qnty']+=$row[csf('grey_fab_qnty')];
		}

		 $yarn_qnty_sql="SELECT b.job_no_mst as job_id,b.id as po_id,c.cons_quantity from  wo_po_details_master a,wo_po_break_down b,  inv_transaction c,order_wise_pro_details d,inv_issue_master e where a.id=b.job_id and b.id=d.po_breakdown_id and c.id = d.trans_id and c.prod_id=d.prod_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and e.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.knit_dye_source=1  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$total_yarn_qnty_sql=sql_select($yarn_qnty_sql);
		$total_yarn_arr=array();

		foreach($total_yarn_qnty_sql as $row)
		{
            $total_yarn_arr[$row[csf('po_id')]][$row[csf('job_id')]]['cons_quantity']+=$row[csf('cons_quantity')];
		}

		//   echo '<pre>';
	    //  print_r($total_yarn_arr);
	    //  echo '</pre>';


		$embrodiery_sql="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=3 and d.production_type=3 and c.embel_name=2 AND d.cut_no is not null $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		$total_embrodiery_sql=sql_select($embrodiery_sql);
		$em_tot_arr=array();

		foreach($total_embrodiery_sql as $row)
		{
		   $em_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
		   $em_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		}
	   //   echo '<pre>';
	   //  print_r($em_tot_arr);
	   //  echo '</pre>';

	   $special_sql="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=3 and d.production_type=3 and c.embel_name=4 AND d.cut_no is not null $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

	   $total_special_sql=sql_select($special_sql);
	   $sp_tot_arr=array();

	   foreach($total_special_sql as $row)
	   {
		  $sp_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
		  $sp_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
	   }
		//     echo '<pre>';
		//    print_r($sp_tot_arr);
		//    echo '</pre>';

		$sewing_sql="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=5 and d.production_type=5  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		$total_sewing_sql=sql_select($sewing_sql);
		$total_sewing_arr=array();

		foreach($total_sewing_sql as $row)
		{
		   $total_sewing_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
		   $total_sewing_arr[$row[csf('po_id')]][$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		}
	    //  echo '<pre>';
	    // print_r($total_sewing_arr);
	    // echo '</pre>';
		$wash_sql="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=3 and d.production_type=3 and  c.embel_name=3  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";
		$total_wash_sql=sql_select($wash_sql);
		$wash_tot_arr=array();

		foreach($total_wash_sql as $row)
		{
			$wash_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$wash_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		}

	    //   echo '<pre>';
	    //  print_r($wash_tot_arr);
	    //  echo '</pre>';

		$poly_sql="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=11 and d.production_type=11  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";


		$total_poly_sql=sql_select($poly_sql);
		$poly_tot_arr=array();

		foreach($total_poly_sql as $row)
		{
			$poly_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$poly_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		}

		$finishing_sql="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=8 and d.production_type=8  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		$total_finish_sql=sql_select($finishing_sql);
		$finish_tot_arr=array();

		foreach($total_finish_sql as $row)
		{
			$finish_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$finish_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];
		}

		 //   echo '<pre>';
		 //  print_r($finish_tot_arr);
		 //  echo '</pre>';

		 $ex_factory_sql="SELECT b.id as po_id,b.job_no_mst as job_id,b.po_quantity,c.ex_factory_qnty,c.remarks from  wo_po_details_master a,wo_po_break_down b,pro_ex_factory_mst c  WHERE a.id=b.job_id and b.id=c.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form !=85 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		 $total_ex_factory=sql_select($ex_factory_sql);
		 $ex_factory_arr=array();
		 foreach($total_ex_factory as $row)
		 {
			 $ex_factory_arr[$row[csf('po_id')]][$row[csf('job_id')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
			 $ex_factory_arr[$row[csf('po_id')]][$row[csf('job_id')]]['remarks']=$row[csf('remarks')];
		 }
		//   echo '<pre>';
		//   print_r($ex_factory_arr);
		//   echo '</pre>';

    	$kniting_main_sql="SELECT b.id as po_id,b.job_no_mst as job_id,c.reject_fabric_receive,c.grey_receive_qnty from wo_po_details_master a,wo_po_break_down b,pro_grey_prod_entry_dtls c,order_wise_pro_details d,inv_receive_master e where a.id=b.job_id and c.id=d.dtls_id and b.id=d.po_breakdown_id and e.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and d.entry_form=2 and e.status_active=1 and e.is_deleted=0 and e.knitting_source=1 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$kniting_main_total_sql=sql_select($kniting_main_sql);
		$kniting_main_arr=array();
		foreach($kniting_main_total_sql as $row)
		{
		   $kniting_main_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_fabric_receive')];
		   $kniting_main_arr[$row[csf('po_id')]][$row[csf('job_id')]]['grey_receive_qnty']+=$row[csf('grey_receive_qnty')];
		}

		$cutting_main_sql="SELECT b.id as po_id,b.job_no_mst as job_id, c.po_break_down_id,d.reject_qty,d.production_qnty from  wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d WHERE a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.production_type=1 and d.production_type=1 AND d.cut_no is not null $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond $pro_company_name";

		$total_cutting_main_sql=sql_select($cutting_main_sql);
		$cutting_main_arr=array();

		foreach($total_cutting_main_sql as $row)
		{
			$cutting_main_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qty')];
			$cutting_main_arr[$row[csf('po_id')]][$row[csf('job_id')]]['production_qnty']+=$row[csf('production_qnty')];

		}

		//  echo '<pre>';
	    //  print_r($cutting_main_arr);
	    //  echo '</pre>';

		$order_sql="SELECT b.id as po_id,b.job_no_mst as job_id,b.po_quantity,b.plan_cut from  wo_po_details_master a,wo_po_break_down b  WHERE  a.id=b.job_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		$order_main_sql=sql_select($order_sql);
		$order_arr=array();

		foreach($order_main_sql as $row)
		{
            $order_arr[$row[csf('po_id')]][$row[csf('job_id')]]['po_quantity']=$row[csf('po_quantity')];
			$order_arr[$row[csf('po_id')]][$row[csf('job_id')]]['plan_cut']=$row[csf('plan_cut')];
		}

		$plan_cut_sql="SELECT b.id as po_id,b.job_no_mst as job_id,c.marker_qty  from  wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_dtls c  WHERE  a.id=b.job_id and b.id=c.order_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond $year_cond $order_cond $order_status_cond";

		$plan_main_sql=sql_select($plan_cut_sql);
		$plan_arr=array();

		foreach($plan_main_sql as $row)
		{
            $plan_arr[$row[csf('po_id')]][$row[csf('job_id')]]['marker_qty']+=$row[csf('marker_qty')];

		}

		$kniting_outbound="SELECT b.id as po_id,b.job_no_mst as job_id,c.reject_fabric_receive,c.grey_receive_qnty from wo_po_details_master a,wo_po_break_down b,pro_grey_prod_entry_dtls c,order_wise_pro_details d,inv_receive_master e where a.id=b.job_id and c.id=d.dtls_id and b.id=d.po_breakdown_id and e.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and d.entry_form=2 and e.status_active=1 and e.is_deleted=0 and e.knitting_source=3 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$knit_outbound=sql_select($kniting_outbound);
        $knit_out_arr=array();

		foreach($knit_outbound as $row)
		{
			$knit_out_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_fabric_receive')];
			$knit_out_arr[$row[csf('po_id')]][$row[csf('job_id')]]['grey_receive_qnty']+=$row[csf('grey_receive_qnty')];
		}

		// echo '<pre>';
	    //  print_r($knit_out_arr);
	    //  echo '</pre>';

		$yarn_outbound="SELECT b.job_no_mst as job_id,b.id as po_id,c.cons_quantity from  wo_po_details_master a,wo_po_break_down b,  inv_transaction c,order_wise_pro_details d,inv_issue_master e where a.id=b.job_id and b.id=d.po_breakdown_id and c.id = d.trans_id and c.prod_id=d.prod_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and e.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.knit_dye_source=3  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$yarn_tot_outbound=sql_select($yarn_outbound);
		$yarn_tot_arr=array();

		foreach($yarn_tot_outbound as $row)
        {
			$yarn_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['quantity']+=$row[csf('cons_quantity')];
		}

		$fin_fabric_sql="SELECT b.job_no_mst as job_id,b.id as po_id,d.po_break_down_id,d.fin_fab_qnty from  wo_po_details_master a,wo_po_break_down b,wo_booking_dtls d where a.id=b.job_id  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$fabric_tot=sql_select($fin_fabric_sql);
		$fabric_tot_arr=array();

		foreach($fabric_tot as $row)
        {
			$fabric_tot_arr[$row[csf('po_id')]][$row[csf('job_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		}


	   $fabric_issue_out="SELECT b.job_no_mst as job_id,b.id as po_id,c.issue_qnty from  wo_po_details_master a,wo_po_break_down b,  inv_grey_fabric_issue_dtls c,order_wise_pro_details d,inv_issue_master e where a.id=b.job_id and b.id=d.po_breakdown_id and c.id = d.dtls_id and c.prod_id=d.prod_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and e.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.knit_dye_source=3  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";


	   $fabric_issue_tot_out=sql_select($fabric_issue_out);
	   $fabric_issue_tot_out_arr=array();

	   foreach($fabric_issue_tot_out as $row)
	   {
		   $fabric_issue_tot_out_arr[$row[csf('po_id')]][$row[csf('job_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
	   }

	   $fabric_issue_in="SELECT b.job_no_mst as job_id,b.id as po_id,c.issue_qnty from  wo_po_details_master a,wo_po_break_down b,  inv_grey_fabric_issue_dtls c,order_wise_pro_details d,inv_issue_master e where a.id=b.job_id and b.id=d.po_breakdown_id and c.id = d.dtls_id and c.prod_id=d.prod_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and e.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.knit_dye_source=1  $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";


	   $fabric_issue_tot_in=sql_select($fabric_issue_in);
	   $fabric_issue_tot_in_arr=array();

	   foreach($fabric_issue_tot_in as $row)
	   {
		   $fabric_issue_tot_in_arr[$row[csf('po_id')]][$row[csf('job_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
	   }

	   $dyeing_in_sql="SELECT b.id as po_id,b.job_no_mst as job_id,e.reject_qnty,e.roll_weight from wo_po_details_master a,wo_po_break_down b,  pro_roll_details d, pro_qc_result_mst e, inv_receive_master  f where a.id=b.job_id  and b.id=d.po_breakdown_id and d.dtls_id=e.pro_dtls_id and e.barcode_no=d.barcode_no and f.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.knitting_source=1 and d.entry_form=66 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

	   $dyeing_total_in_sql=sql_select($dyeing_in_sql);
	   $dyeing_in_arr=array();
	   foreach($dyeing_total_in_sql as $row)
	   {
		  $dyeing_in_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qnty')];
		  $dyeing_in_arr[$row[csf('po_id')]][$row[csf('job_id')]]['roll_weight']+=$row[csf('roll_weight')];
	   }
		//    echo '<pre>';
		//    print_r($dyeing_in_arr);
		//    echo '</pre>';

		$dyeing_out_sql="SELECT b.id as po_id,b.job_no_mst as job_id,e.reject_qnty,e.roll_weight from wo_po_details_master a,wo_po_break_down b,  pro_roll_details d, pro_qc_result_mst e, inv_receive_master  f where a.id=b.job_id  and b.id=d.po_breakdown_id and d.dtls_id=e.pro_dtls_id and e.barcode_no=d.barcode_no and f.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.knitting_source=3 and d.entry_form=66 $sql_cond $buyer_name $location_name_one $job_cond_id $style_cond $season_cond  $year_cond $order_cond $order_status_cond";

		$dyeing_total_out_sql=sql_select($dyeing_out_sql);
		$dyeing_out_arr=array();
		foreach($dyeing_total_out_sql as $row)
		{
		$dyeing_out_arr[$row[csf('po_id')]][$row[csf('job_id')]]['reject_qty']+=$row[csf('reject_qnty')];
		$dyeing_out_arr[$row[csf('po_id')]][$row[csf('job_id')]]['roll_weight']+=$row[csf('roll_weight')];
		}
		// echo '<pre>';
		// print_r($dyeing_out_arr);
		// echo '</pre>';




    }
	?>
	<br>

	<h1>Job Wise Summary </h1>
	<div style="width:2630px">
	<fieldset width="100%">
	 <table class="rpt_table" width="2610" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
              <tr>
				 <th rowspan="2" width="50">SL</th>
				 <th rowspan="2" width="120">Buyer Name</th>
				 <th rowspan="2" width="120">Order Qty(Kg)</th>
				 <th rowspan="2" width="120">Order Qty(Pcs)</th>
				 <th width="200" colspan="2">Knitting</th>
				 <th width="200" colspan="2">Dyeing and Finishing</th>
				 <th width="200" colspan="2">Cutting</th>
				 <th width="200" colspan="2">Printing</th>
				 <th width="200" colspan="2">Embroidery</th>

				 <th width="200" colspan="2">Special Work</th>
				 <th width="200" colspan="2">Sewing Output</th>
				 <th width="200" colspan="2">Washing</th>
				 <th width="200" colspan="2">Poly</th>
				 <th width="200" colspan="2">Finishing</th>
				 <th width="200" colspan="2">Ex-Factory</th>
			  </tr>
			  <tr>
				<th width="100">Rejection Qty(kg)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Kg)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Pcs)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Pcs)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Pcs)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Pcs)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Pcs)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Pcs)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Pcs)</th>
				<th width="100">Rejection%</th>
				<th width="100">Rejection Qty(Pcs)</th>
				<th width="100">Rejection%</th>
				<th width="100">Ex-Factory Qty(Pcs)</th>
				<th width="100">Ex-Factory Balance</th>
			  </tr>
		</thead>
		<tbody id="table_body_id">
		<?
            $i=1;
					foreach($job_summary_arr as $job_id=>$val)
					{

							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
						 ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo  $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						 <td><? echo $i;?></td>
						 <td><? echo $buyerArr[$val['buyer_name']] ;?></td>
						 <td align="right"> <? echo number_format($val['grey_fab_qnty'],2);?></td>
						 <td align="right"><? echo number_format($job_wise_order_arr[$job_id]['order_qty'],2);?></td>
						 <td align="right"><? echo $kniting_arr[$job_id]['reject_qty'];?></td>
						 <td align="right"><? $cut_knit=($kniting_arr[$job_id]['reject_qty']/$kniting_arr[$job_id]['grey_receive_qnty'])*100;echo fn_number_format($cut_knit,2);?></td>
						 <td align="right"><? echo $dyeing_arr[$job_id]['reject_qty'];?></td>
						 <td align="right"><? $cut_dyeing=($dyeing_arr[$job_id]['reject_qty']/$dyeing_arr[$job_id]['roll_weight'])*100;echo fn_number_format($cut_dyeing);?></td>
						 <td align="right"><? echo $cutting_arr[$job_id]['reject_qty'];?></td>
						 <td align="right"><? $cut_per=( $cutting_arr[$job_id]['reject_qty']/$cutting_arr[$job_id]['production_qnty'])*100; echo fn_number_format($cut_per,2); ?> </td>
						 <td align="right"><?  echo $printing_r_arr[$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_per_print=( $printing_r_arr[$job_id]['reject_qty']/$printing_r_arr[$job_id]['production_qnty'])*100; echo fn_number_format($cut_per_print,2); ?></td>
						 <td align="right"><?  echo $em_arr[$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_per_em=( $em_arr[$job_id]['reject_qty']/$em_arr[$job_id]['production_qnty'])*100; echo fn_number_format($cut_per_em,2) ?></td>
						 <td align="right"><?  echo $sp_arr[$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_per_sp=( $sp_arr[$job_id]['reject_qty']/$sp_arr[$job_id]['production_qnty'])*100; echo fn_number_format($cut_per_sp,2); ?></td>
						 <td align="right"><?  echo $sewing_arr[$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_per_sew=( $sewing_arr[$job_id]['reject_qty']/$sewing_arr[$job_id]['production_qnty'])*100; echo fn_number_format($cut_per_sew,2); ?></td>
						 <td align="right"><? echo $wash_arr[$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_per_wash=( $wash_arr[$job_id]['reject_qty']/$wash_arr[$job_id]['production_qnty'])*100; echo fn_number_format($cut_per_wash,2); ?></td>
						 <td align="right"><?  echo $poly_arr[$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_per_poly=( $poly_arr[$job_id]['reject_qty']/$poly_arr[$job_id]['production_qnty'])*100; echo fn_number_format($cut_per_poly,2); ?></td>
						 <td align="right"> <?  echo $finish_arr[$job_id]['reject_qty']; ?> </td>
						 <td  align="right"><? $cut_per_finish=( $finish_arr[$job_id]['reject_qty']/$finish_arr[$job_id]['production_qnty'])*100; echo fn_number_format($cut_per_finish,2); ?></td>
						 <td align="right"><?  echo $total_ex_factory_arr[$job_id]['ex_factory_qnty']; ?></td>
						 <td align="right"><? $balance=$job_wise_order_arr[$job_id]['order_qty']-$total_ex_factory_arr[$job_id]['ex_factory_qnty']; echo fn_number_format($balance,2); ?></td>

						 </tr>
                       <?
					    $i++;
						$tot_kniting_qty+=$kniting_arr[$job_id]['reject_qty'];
						if($cut_knit!=is_nan($cut_knit))
						{
							$tot_cut_knit+=$cut_knit;
						}

						$tot_dying_qty+=$dyeing_arr[$job_id]['reject_qty'];
						$tot_cutting_qty+=$cutting_arr[$job_id]['reject_qty'];
						if($cut_per!=is_nan($cut_per))
						{
							$tot_cut_per+=$cut_per;
						}

						if($cut_dyeing!=is_nan($cut_dyeing))
						{
							$tot_cut_dyeing+=$cut_dyeing;
						}

						$tot_printing+=$printing_rcv_arr[$job_id]['reject_qty'];
						if($cut_per_print!=is_nan($cut_per_print))
						{
							$tot_cut_print+=$cut_per_print;
						}

						$tot_em+=$em_arr[$job_id]['reject_qty'];
						if($cut_per_em!=is_nan($cut_per_em))
						{
							$tot_em_per+=$cut_per_em;
						}
						$tot_sp+=$sp_arr[$job_id]['reject_qty'];
						if($cut_per_sp!=is_nan($cut_per_sp))
						{
							$tot_sp_per+=$cut_per_sp;
						}
						$tot_sewing+=$sewing_arr[$job_id]['reject_qty'];
						if($cut_per_sew!=is_nan($cut_per_sew))
						{
							$tot_per_sew+=$cut_per_sew;
						}
						$tot_wash+=$wash_arr[$job_id]['reject_qty'];
						if($cut_per_wash!=is_nan($cut_per_wash))
						{
							$tot_per_wash+=$cut_per_wash;
						}
						$tot_poly+=$poly_arr[$job_id]['reject_qty'];
						if($cut_per_poly!=is_nan($cut_per_poly))
						{
							$tot_per_poly+=$cut_per_poly;
						}

						$tot_finish+=$finish_arr[$job_id]['reject_qty'];
						if($cut_per_finish!=is_nan($cut_per_finish))
						{
							$tot_per_finish+=$cut_per_finish;
						}
						$tot_ex_factory+=$total_ex_factory_arr[$job_id]['ex_factory_qnty'];
						$tot_balance+=$balance;

					}
				?>
		</tbody>
	 </table>

		<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="2610" id="" >
			<tfoot>
				<tr>
					<th width="50px"></th>
					<th width="120px"></th>
					<th width="120px"></th>
					<th width="120px">Total</th>
					<th width="100px"><? echo number_format($tot_kniting_qty,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_cut_knit,2);?></th>
					<th width="100px"><? echo number_format($tot_dying_qty,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_cut_dyeing);?></th>
					<th width="100px"><? echo fn_number_format($tot_cutting_qty,2);?></th>
					<th width="100px"><? echo number_format($tot_cut_per,2); ?></th>
					<th width="100px"><? echo fn_number_format($tot_printing,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_cut_print,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_em,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_em_per,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_sp,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_sp_per,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_sewing,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_per_sew,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_wash,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_per_wash,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_poly,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_per_poly,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_finish,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_per_finish,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_ex_factory,2);?></th>
					<th width="100px"><? echo fn_number_format($tot_balance,2);?></th>

				</tr>
			</tfoot>
		</table>
    </div>
	<br><br>
	<div style="width:5870px">
		<table class="rpt_table" width="5850" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
				<th width="650px" colspan="7">Basic Info</th>
				<th width="1100px" colspan="11">Knitting</th>
				<th width="1100px" colspan="11">Dyeing Finishing</th>
				<th width="600px" colspan="6">Cutting</th>
				<th width="300px" colspan="3">Print</th>
				<th width="300px" colspan="3">Embrodiery</th>
				<th width="300px" colspan="3">Special Work</th>
				<th width="300px" colspan="3">Sewing</th>
				<th width="300px" colspan="3">Washing</th>
				<th width="300px" colspan="3">Poly</th>
				<th width="300px" colspan="3">Finishing</th>
				<th width="300px" colspan="3">Ex-Factory</th>
				</tr>
				<tr>
					<th width="50px">SI</th>
					<th width="100px">Buyer Name</th>
					<th width="100px">Job No</th>
					<th width="100px">Order Number</th>
					<th width="100px">Order Status</th>
					<th width="100px">Style Name</th>
					<th width="100px">Ship Date</th>
					<th width="100px">Grey Fabric Req.  Qty. (KG)</th>
					<th width="100px">Yarn Issue to Knitting (Inhouse)</th>
					<th width="100px">Knitting Production (Inhouse) (Kg)</th>
					<th width="100px">Fabric Rej. Qty. (kg)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Yarn Issue to Knitting (Outbound)</th>
					<th width="100px">Knitting Production (Outbound)-(Kg)</th>
					<th width="100px">Rejection Qty. (kg)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Total Knit Rejection Qty. (kg)</th>
					<th width="100px">Total Knit Rejection %</th>
					<th width="100px">Fin. Fabric Req.  Qty. (KG)</th>
					<th width="100px">Fabric Issue to Dyeing (Inhouse)</th>
					<th width="100px">D.F Production (Inhouse) (Kg)</th>
					<th width="100px">Rejection Qty. (kg)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Fabric Issue to Dyeing (Outbound)</th>
					<th width="100px">D.F Production (Outbound)-(Kg)</th>
					<th width="100px">Rejection Qty. (kg)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Total D.F Rejection Qty. (kg)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Order Qty. (Pcs)</th>
					<th width="100px">Required Qty with Plan cut</th>
					<th width="100px">Cutting Production</th>
					<th width="100px">Cutting QC Pass</th>
					<th width="100px">Rejection Qty. (Pcs)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Print Production</th>
					<th width="100px">Rejection Qty. (Pcs)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Embroidery Production</th>
					<th width="100px">Rejection Qty. (Pcs)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Spacial Work Production</th>
					<th width="100px">Rejection Qty. (Pcs)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Sewing Production</th>
					<th width="100px">Rejection Qty. (Pcs)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Washing Production</th>
					<th width="100px">Rejection Qty. (Pcs)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Poly Production</th>
					<th width="100px">Rejection Qty. (Pcs)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Finishing Production</th>
					<th width="100px">Rejection Qty. (Pcs)</th>
					<th width="100px">Rejection %</th>
					<th width="100px">Ex-Factory Qty (Pcs)</th>
					<th width="100px">Ex-Factory Balance</th>
					<th width="100px">Remarks</th>

				</tr>
			</thead>
			<tbody id="table_body_id_two">
				<?
					$i=1;
					foreach($purpose_arr as $job_id=>$job_val)
					{
						$job_wise_grey=0;
						$job_wise_cut_kn_per=0;
						$job_wise_cut_knit=0;
						$job_wise_tot_cut_knit=0;
						$job_wise_cut_fabric=0;
						$job_wise_cut_qc_per=0;
						$job_wise_cut_print=0;
						$job_wise_cut_em=0;
						$job_wise_cut_sp_per=0;
						$job_wise_cut_sewing_per=0;
						$job_wise_cut_wash_per=0;
						$job_wise_cut_poly_per=0;
						$job_wise_cut_finish_per=0;
						$job_wise_po=0;

						foreach($job_val as $po_id=>$val)
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";

						?>
						 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo  $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						 <td><? echo $i;?></td>
						 <td><? echo $buyerArr[$val['buyer_name']] ;?></td>
						 <td><? echo $job_id; ?></td>
						 <td><? echo $val['po_number']; ?></td>
						 <td><? echo $order_status[$val['is_confirmed']]; ?></td>
						 <td><? echo $val['style_ref_no']; ?></td>
						 <td><? echo $val['pub_shipment_date']; ?></td>
						 <td align="right"><? echo fn_number_format($total_grey_arr[$po_id][$job_id]['grey_qnty'],2); ?></td>
						 <td align="right"><? echo $total_yarn_arr[$po_id][$job_id]['cons_quantity'];  ?></td>
						 <td align="right"><? echo $kniting_main_arr[$po_id][$job_id]['grey_receive_qnty']; ?></td>
						 <td  align="right"><? echo $kniting_main_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_kn_per=($kniting_main_arr[$po_id][$job_id]['reject_qty']/$kniting_main_arr[$po_id][$job_id]['grey_receive_qnty'])*100; echo fn_number_format($cut_kn_per,2); ?></td>
						 <td align="right"><? echo $yarn_tot_arr[$po_id][$job_id]['quantity']; ?></td>
						 <td align="right"><? echo $knit_out_arr[$po_id][$job_id]['grey_receive_qnty']; ?></td>
						 <td align="right"><? echo $knit_out_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_knit=($knit_out_arr[$po_id][$job_id]['reject_qty']/ $knit_out_arr[$po_id][$job_id]['grey_receive_qnty'])*100; echo fn_number_format($cut_knit,2); ?></td>
						 <td align="right"><? $tot_rej=$kniting_main_arr[$po_id][$job_id]['reject_qty']+$knit_out_arr[$po_id][$job_id]['reject_qty']; echo fn_number_format($tot_rej,2);  ?></td>
						 <td align="right"><? $tot_cut_knit=$cut_knit_per+$cut_knit;echo fn_number_format($tot_cut_knit,2); ?></td>
						 <td align="right"><? echo fn_number_format($fabric_tot_arr[$po_id][$job_id]['fin_fab_qnty'],2) ?></td>
						 <td align="right"><? echo fn_number_format($fabric_issue_tot_in_arr[$po_id][$job_id]['issue_qnty'],2) ?></td>
						 <td align="right"><? echo fn_number_format($dyeing_in_arr[$po_id][$job_id]['roll_weight'],2);  ?></td>
						 <td align="right"><? echo fn_number_format($dyeing_in_arr[$po_id][$job_id]['reject_qty'],2);  ?></td>
						 <td align="right"><? $cut_fabric=($dyeing_in_arr[$po_id][$job_id]['reject_qty']/$dyeing_in_arr[$po_id][$job_id]['roll_weight'])*100; echo fn_number_format($cut_fabric,2); ?></td>
						 <td align="right"><? echo $fabric_issue_tot_out_arr[$po_id][$job_id]['issue_qnty']; ?></td>
						 <td align="right"><? echo fn_number_format($dyeing_out_arr[$po_id][$job_id]['roll_weight'],2); ?></td>
						 <td align="right"><? echo fn_number_format($dyeing_out_arr[$po_id][$job_id]['reject_qty'],2);   ?></td>
						 <td align="right"><?  $dying_out_per=($dyeing_out_arr[$po_id][$job_id]['reject_qty']/$dyeing_out_arr[$po_id][$job_id]['roll_weight'])*100;echo fn_number_format($dying_out_per,2); ?></td>
						 <td align="right"><?  $tot_dying_reject=$dyeing_in_arr[$po_id][$job_id]['reject_qty']+$dyeing_out_arr[$po_id][$job_id]['reject_qty']; echo fn_number_format($tot_dying_reject,2); ?></td>
						 <td align="right"><? $tot_dying_weight=$dyeing_in_arr[$po_id][$job_id]['roll_weight']+$dyeing_out_arr[$po_id][$job_id]['roll_weight']; $tot_dying_reject_per=($tot_dying_reject/$tot_dying_weight)*100; echo fn_number_format($tot_dying_reject_per,2); ?></td>
						 <td align="right"><? echo $order_arr[$po_id][$job_id]['po_quantity']; ?></td>
						 <td align="right"><?  echo $order_arr[$po_id][$job_id]['plan_cut']; ?></td>
						 <td align="right"><? echo $plan_arr[$po_id][$job_id]['marker_qty'];  ?></td>
						 <td align="right"><? echo $cutting_main_arr[$po_id][$job_id]['production_qnty']; ?></td>
						 <td  align="right"><? echo $cutting_main_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_qc_per=($cutting_main_arr[$po_id][$job_id]['reject_qty']/ $cutting_main_arr[$po_id][$job_id]['production_qnty'])*100; echo fn_number_format($cut_qc_per,2); ?></td>
						 <td align="right"><?  echo $printing_rcv_arr[$po_id][$job_id]['production_qnty'];  ?></td>
						 <td align="right"><?  echo $printing_rcv_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_print=($printing_rcv_arr[$po_id][$job_id]['reject_qty']/ $printing_rcv_arr[$po_id][$job_id]['production_qnty'])*100; echo fn_number_format($cut_print,2) ?></td>
						 <td align="right"><? echo  $em_tot_arr[$po_id][$job_id]['production_qnty'];  ?></td>
						 <td align="right"><?  echo  $em_tot_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_em_per=($em_tot_arr[$po_id][$job_id]['reject_qty']/ $em_tot_arr[$po_id][$job_id]['production_qnty'])*100; echo fn_number_format($cut_em_per,2); ?></td>
						 <td align="right"><? echo  $sp_tot_arr[$po_id][$job_id]['production_qnty']; ?></td>
						 <td align="right"><? echo  $sp_tot_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td  align="right"><? $cut_sp_per=( $sp_tot_arr[$po_id][$job_id]['reject_qty']/$sp_tot_arr[$po_id][$job_id]['production_qnty'])*100; echo fn_number_format($cut_sp_per,2); ?></td>
						 <td align="right"><?  echo  $total_sewing_arr[$po_id][$job_id]['production_qnty']; ?></td>
						 <td align="right"><?   echo  $total_sewing_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_sewing_per=( $total_sewing_arr[$po_id][$job_id]['reject_qty']/ $total_sewing_arr[$po_id][$job_id]['production_qnty'])*100;echo fn_number_format($cut_sewing_per,2); ?></td>
						 <td align="right"><? echo  $wash_tot_arr[$po_id][$job_id]['production_qnty']; ?></td>
						 <td align="right"><? echo  $wash_tot_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_wash_per=( $wash_tot_arr[$po_id][$job_id]['reject_qty']/ $wash_tot_arr[$po_id][$job_id]['production_qnty'])*100;echo fn_number_format($cut_wash_per,2);  ?></td>
						 <td align="right"><?  echo  $poly_tot_arr[$po_id][$job_id]['production_qnty']; ?></td>
						 <td align="right"><?  echo  $poly_tot_arr[$po_id][$job_id]['reject_qty'];  ?></td>
						 <td align="right"><? $cut_poly_per=( $poly_tot_arr[$po_id][$job_id]['reject_qty']/ $poly_tot_arr[$po_id][$job_id]['production_qnty'])*100;echo fn_number_format($cut_poly_per,2);  ?></td>
						 <td align="right"><?  echo  $finish_tot_arr[$po_id][$job_id]['production_qnty']; ?></td>
						 <td align="right"><? echo  $finish_tot_arr[$po_id][$job_id]['reject_qty']; ?></td>
						 <td align="right"><? $cut_finish_per=( $finish_tot_arr[$po_id][$job_id]['reject_qty']/ $finish_tot_arr[$po_id][$job_id]['production_qnty'])*100; echo fn_number_format($cut_finish_per,2);?></td>
						 <td align="right"><?   echo  $ex_factory_arr[$po_id][$job_id]['ex_factory_qnty']; ?></td>
						 <td align="right"><? $balance=$order_arr[$po_id][$job_id]['po_quantity']- $ex_factory_arr[$po_id][$job_id]['ex_factory_qnty']; echo fn_number_format($balance,2); ?></td>
						 <td><? echo $ex_factory_arr[$po_id][$job_id]['remarks']; ?></td>
						 </tr>
						<?
						 $i++;
						 $tot_grey_fabric+=$total_grey_arr[$po_id][$job_id]['grey_qnty'];
						 $tot_yarn_qty+=$total_yarn_arr[$po_id][$job_id]['cons_quantity'];
						 $tot_knit_qty+=$kniting_main_arr[$po_id][$job_id]['grey_receive_qnty'];
						 $tot_knit_reject_qty+=$kniting_main_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_kn_per!=is_nan($cut_kn_per))
						 {
						    $tot_cut_kn_per+=$cut_kn_per;

						 }
						 $tot_yarn_arr+= $yarn_tot_arr[$po_id][$job_id]['quantity'];
						 $tot_knit_out+=$knit_out_arr[$po_id][$job_id]['grey_receive_qnty'];
						 $tot_knit_out_reject+=$knit_out_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_knit!=is_nan($cut_knit))
						 {
							$gr_tot_cut_knit+=$cut_knit;
						 }
						 $gr_tot_rej+=$tot_rej;
						 $gr_cut_knit+=$tot_cut_knit;
						 $fabric_tot_fab+=$fabric_tot_arr[$po_id][$job_id]['fin_fab_qnty'];
						 $fabric_issue_total+=$fabric_issue_tot_in_arr[$po_id][$job_id]['issue_qnty'];
						 $dying_grand_tot_roll+=$dyeing_in_arr[$po_id][$job_id]['roll_weight'];
						 $dying_grand_tot_reject+=$dyeing_in_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_fabric!=is_nan($cut_fabric))
						 {
							$gr_tot_cut_dying+=$cut_fabric;
						 }
						 $fabric_total_issue_qnty+=$fabric_issue_tot_out_arr[$po_id][$job_id]['issue_qnty'];
						 $dying_total_out_qnty+=$dyeing_out_arr[$po_id][$job_id]['roll_weight'];
						 $dying_out_total_reject+=$dyeing_out_arr[$po_id][$job_id]['reject_qty'];
						 if($dying_out_per!=is_nan($dying_out_per))
						 {
                            $total_dying_out_per+=$dying_out_per;
						 }

						 $grand_tot_cutting_reject+=$tot_dying_reject;
						 $grand_tot_reject_per+=$tot_dying_reject_per;
						 $tot_po_qty+=$order_arr[$po_id][$job_id]['po_quantity'];
						 $tot_plan_qty+=$order_arr[$po_id][$job_id]['plan_cut'];
						 $tot_marker_qty+=$plan_arr[$po_id][$job_id]['marker_qty'];
						 $tot_cut_prod_qty+=$cutting_main_arr[$po_id][$job_id]['production_qnty'];
						 $tot_cut_reject_qty+=$cutting_main_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_qc_per!=is_nan($cut_qc_per))
						 {
							$gr_tot_cut_qc_per+=$cut_qc_per;
						 }
						 $tot_print_qty+=$printing_rcv_arr[$po_id][$job_id]['production_qnty'];
						 $tot_print_reject_qty+=$printing_rcv_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_print!=is_nan($cut_print))
						 {
							$gr_tot_cut_print+=$cut_print;
						 }
						 $tot_em_qty+=$em_tot_arr[$po_id][$job_id]['production_qnty'];
						 $tot_em_reject_qty+=$em_tot_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_em_per!=is_nan($cut_em_per))
						 {
							$gr_tot_cut_em_per+=$cut_em_per;
						 }
						 $tot_sp_qty+=$sp_tot_arr[$po_id][$job_id]['production_qnty'];
						 $tot_sp_reject_qty+=$sp_tot_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_sp_per!=is_nan($cut_sp_per))
						 {
							$gr_tot_cut_sp_per+=$cut_sp_per;
						 }
						 $tot_sew_qty+=$total_sewing_arr[$po_id][$job_id]['production_qnty'];
						 $tot_reject_qty+=$total_sewing_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_sewing_per!=is_nan($cut_sewing_per))
						 {
							$gr_tot_cut_sew_per+=$cut_sewing_per;
						 }
						 $tot_wash_qty+=$wash_tot_arr[$po_id][$job_id]['production_qnty'];
						 $tot_wash_reject_qty+=$wash_tot_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_wash_per!=is_nan($cut_wash_per))
						 {
							$gr_tot_cut_wash+=$cut_wash_per;
						 }
						 $tot_poly_qty+=$poly_tot_arr[$po_id][$job_id]['production_qnty'];
						 $tot_poly_reject_qty+=$poly_tot_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_poly_per!=is_nan($cut_poly_per))
						 {
							$gr_tot_cut_poly+=$cut_poly_per;
						 }
						 $tot_finish_qty+=$finish_tot_arr[$po_id][$job_id]['production_qnty'];
						 $tot_finish_reject_qty+=$finish_tot_arr[$po_id][$job_id]['reject_qty'];
						 if($cut_finish_per!=is_nan($cut_finish_per))
						 {
							$gr_tot_cut_finish+=$cut_finish_per;
						 }
                         $tot_ex_factory_qty+=$ex_factory_arr[$po_id][$job_id]['ex_factory_qnty'];
						 $tot_balance_qty+=$balance;




						 //==========================Job Wise Summation==================================//
						 $job_wise_grey+=$total_grey_arr[$po_id][$job_id]['grey_qnty'];
						 $job_wise_cut_kn_per+=$cut_kn_per;
						 if($cut_knit!=is_nan($cut_knit))
						{
							$job_wise_cut_knit+=$cut_knit;
						}
						if($tot_cut_knit!=is_nan($tot_cut_knit))
						{
							$job_wise_tot_cut_knit+=$tot_cut_knit;
						}

						if($cut_fabric!=is_nan($cut_fabric))
						{
							$job_wise_cut_fabric+=$cut_fabric;
						}

						if($cut_qc_per!=is_nan($cut_qc_per))
						{
							$job_wise_cut_qc_per+=$cut_qc_per;
						}
						if($cut_print!=is_nan($cut_print))
						{
							$job_wise_cut_print+=$cut_print;
						}
						if($cut_em_per!=is_nan($cut_em_per))
						{
							$job_wise_cut_em+=$cut_em_per;
						}
						if($cut_sp_per!=is_nan($cut_sp_per))
						{
							$job_wise_cut_sp_per+=$cut_sp_per;
						}
						if($cut_sewing_per!=is_nan($cut_sewing_per))
						{
							$job_wise_cut_sewing_per+=$cut_sewing_per;
						}
						if($cut_wash_per!=is_nan($cut_wash_per))
						{
							$job_wise_cut_wash_per+=$cut_wash_per;
						}
						if($cut_poly_per!=is_nan($cut_poly_per))
						{
							$job_wise_cut_poly_per+=$cut_poly_per;
						}

						if($cut_finish_per!=is_nan($cut_finish_per))
						{
							$job_wise_cut_finish_per=$cut_finish_per;
						}

						 $job_wise_po+=$order_arr[$po_id][$job_id]['po_quantity'];

						}
						?>

						 <tr style="text-align: right;font-weight:bold;background:#dccddc;">
							<th width="50"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100">Job Wise Sub Total:</th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_grey,2)?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_kn_per,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_knit,2);?></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_tot_cut_knit,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_fabric,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_po,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_qc_per,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_print,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_em,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_sp_per,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_sewing_per,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_wash_per,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_poly_per,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><? echo fn_number_format($job_wise_cut_finish_per,2);?></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
						</tr>
				  <?
					}



                 ?>

			</tbody>
			<tfoot>
				<tr>
                    <th width="50"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100">Grand Total:</th>
                    <th width="100"></th>
                    <th width="100"><? echo fn_number_format($tot_grey_fabric,2)?></th>
					<th width="100"><? echo fn_number_format($tot_yarn_qty,2);?></th>
                    <th width="100"><? echo fn_number_format($tot_knit_qty,2);?></th>
                    <th width="100"><? echo fn_number_format($tot_knit_reject_qty,2);?></th>
                    <th width="100"><? echo fn_number_format($tot_cut_kn_per,2);?></th>
                    <th width="100"><? echo fn_number_format($tot_yarn_arr,2);?></th>
                    <th width="100"><? echo fn_number_format($tot_knit_out,2);?></th>
                    <th width="100"><? echo fn_number_format($tot_knit_out_reject,2);?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_knit,2);?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_rej,2);?></th>
                    <th width="100"><? echo fn_number_format($gr_cut_knit,2);?></th>
                    <th width="100"><? echo fn_number_format($fabric_tot_fab,2);?></th>
                    <th width="100"><? echo fn_number_format($fabric_issue_total,2);?></th>
                    <th width="100"><? echo fn_number_format($dying_grand_tot_roll,2);?></th>
                    <th width="100"><? echo fn_number_format($dying_grand_tot_reject,2);?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_dying,2);?></th>
                    <th width="100"><? echo fn_number_format($fabric_total_issue_qnty,2);?></th>
                    <th width="100"><? echo fn_number_format($dying_total_out_qnty,2);?></th>
                    <th width="100"><? echo fn_number_format($dying_out_total_reject,2);?></th>
                    <th width="100"><? echo fn_number_format($total_dying_out_per,2);?></th>
                    <th width="100"><? echo fn_number_format($grand_tot_cutting_reject,2);?></th>
                    <th width="100"><? echo fn_number_format($grand_tot_reject_per,2);?></th>
                    <th width="100"><? echo fn_number_format($tot_po_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_plan_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_marker_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_cut_prod_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_cut_reject_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_qc_per,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_print_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_print_reject_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_print,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_em_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_em_reject_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_em_per,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_sp_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_sp_reject_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_sp_per,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_sew_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_reject_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_sew_per,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_wash_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_wash_reject_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_wash,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_poly_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_poly_reject_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_poly,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_finish_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_finish_reject_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($gr_tot_cut_finish,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_ex_factory_qty,2); ?></th>
                    <th width="100"><? echo fn_number_format($tot_balance_qty,2); ?></th>
                    <th width="100"></th>



				</tr>
			</tfoot>
		</table>

	</div>
	</fieldset>
<?
}

?>