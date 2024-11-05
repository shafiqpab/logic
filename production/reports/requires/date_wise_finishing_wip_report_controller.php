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
	echo create_drop_down( "cbo_buyer_name", 80, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 80, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/date_wise_finishing_wip_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 70, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process IN (4,11) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	// echo $sql="select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name";
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
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'date_wise_finishing_wip_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'date_wise_finishing_wip_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
if($action=="sewing_popup")
{

 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);
	?>
	<div id="data_panel" align="center" style="width:100%">
	<script>
	   function new_window()
	   {
		   document.getElementById('scroll_body').style.overflow="auto";
		   document.getElementById('scroll_body').style.maxHeight="none";
		   $('#table_body tr:first').hide();
		   var w = window.open("Surprise", "#");
		   var d = w.document.open();
		   d.write(document.getElementById('details_reports').innerHTML);
		   d.close();
		   $('#table_body tr:first').show();
		   document.getElementById('scroll_body').style.overflowY="scroll";
		   document.getElementById('scroll_body').style.maxHeight="none";
	   }
	</script>
<!-- <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" /> -->
</div>
<br>
<?

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
	$location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_con .= ($id=="") ? "" : " and b.id=".$id;
	$prod_con .= ($item_number_id=="") ? "" : " and c.item_number_id=".$item_number_id;
	$prod_con .= ($color_number_id=="") ? "" : " and c.color_number_id=".$color_number_id;
	// $prod_con .= ($id=="") ? "" : " and b.id=".$id;



	$popup_sql="SELECT a.buyer_name,b.id,a.job_no_prefix_num,b.po_number,a.style_ref_no,b.grouping,c.color_number_id,c.item_number_id,b.shiping_status,a.remarks,b.shipment_date
	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c
	WHERE a.id=b.job_id  and b.id=c.po_break_down_id and a.id=c.job_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $prod_con";
	//  echo $popup_sql;

	$sewing_sql="SELECT d.production_type,b.id,(CASE WHEN d.production_type ='5' THEN e.production_qnty END) AS sewingoutput, a.buyer_name,a.job_no_prefix_num,b.po_number,a.style_ref_no,b.grouping,c.color_number_id,c.item_number_id,c.order_quantity,b.shiping_status,a.remarks,d.floor_id as sewing_floor,b.shipment_date,d.production_source,
	(CASE WHEN d.production_source=1 THEN e.production_qnty ELSE 0 END) as in_house_cut_qnty,
    (CASE WHEN d.production_source=3 THEN e.production_qnty ELSE 0 END) as out_bound_cut_qnty,
	d.sewing_line,d.production_date,d.serving_company,d.location,d.prod_reso_allo
	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
	WHERE a.id=b.job_id  and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $prod_con order by d.production_date";
// echo $sewing_sql;

		$plan_sql="SELECT b.id,c.plan_cut_qnty from wo_po_break_down b,wo_po_color_size_breakdown c WHERE b.id=c.po_break_down_id and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=$id";
		//    echo $plan_sql;
		$plan_arr=array();
		$plan_sql_data=sql_select($plan_sql);
		foreach($plan_sql_data as $v)
		{

			$plan_arr[$v[csf('id')]]['qty']+=$v[csf('plan_cut_qnty')];
		}
		// echo '<pre>';
		// print_r($plan_arr);
		// echo '</pre>';

	$main_sewing_sql=sql_select($popup_sql);
	if(count($main_sewing_sql)==0){
		echo "No data found";
		die();
	}

	$main_sewing_arr=array(); $production_source_arr=array();
	foreach($main_sewing_sql as $row)
	{
		// $main_sewing_arr[$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['qty'] += $row[csf('plan_cut')];
		$main_sewing_arr[$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['buyer_name'] = $row[csf('buyer_name')];
		$main_sewing_arr[$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['po_number'] = $row[csf('po_number')];
		$main_sewing_arr[$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
		$main_sewing_arr[$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$main_sewing_arr[$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['grouping']=$row[csf('grouping')];
		$main_sewing_arr[$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['shipment_date']=$row[csf('shipment_date')];



	}
	//    print_r($main_sewing_arr);

	$sewing_data=sql_select($sewing_sql);
	$sewing_arr=array();
	foreach($sewing_data as $row)
	{
		$sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['sewingoutput']+=$row[csf('sewingoutput')];
		$sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['serving_company']=$row[csf('serving_company')];
	    $sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['sewing_line']=$row[csf('sewing_line')];
		$sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['sewing_floor']=$row[csf('sewing_floor')];
		$sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['location']=$row[csf('location')];
		$sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['production_source']=$row[csf('production_source')];
		$sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['in_house_cut_qnty']+=$row[csf('in_house_cut_qnty')];
		$sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['out_bound_cut_qnty']+=$row[csf('out_bound_cut_qnty')];
		$sewing_arr[$row[csf('id')]][$row[csf('production_date')]]['row_count']+=1;


	}
	//   print_r($sewing_arr);

//    foreach($sewing_data as $val)
//    {
// 	$sewing_line='';
// 		if($val[csf('prod_reso_allo')]==1)
// 		{
// 			$line_number=explode(",",$prod_reso_arr[$val[csf('sewing_line')]]);
// 			foreach($line_number as $v)
// 			{
// 				if($sewing_line=='') $sewing_line=$sewing_library[$v]; else $sewing_line.=",".$sewing_library[$v];
// 			}
// 		}
// 		else $sewing_line=$sewing_library[$val[csf('sewing_line')]];


//    }
//  print_r($sewing_arr);


?>
       <fieldset>
	   <div style="width:780" align="center" id="details_reports">
			<table id="tbl_id" class="rpt_table" width="760px" border="1" rules="all" >
			<thead>
				<tr>
					<th width="100">Buyer</th>
					<th width="100">Job Number</th>
					<th width="100">Style Name</th>
					<th width="100">Order Number</th>
					<th width="100">Int. Ref.</th>
					<th width="100">Ship Date</th>
					<th width="100">Item Name</th>
					<th width="60">Order Qty.</th>
				</tr>
          </thead>
		  <table class="rpt_table" width="760" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tbody>
					<?
					$i=1;
						foreach($main_sewing_arr as $job_no_prefix_num=>$job_data)
						{
							foreach($job_data as $id=>$po_data)
							{
								foreach($po_data as $item_number_id=>$item_data)
								{
									foreach($item_data as $color_number_id=>$color_data)
									{

										if ($i%2==0)
										$bgcolor="#E9F3FF";
										else
										$bgcolor="#FFFFFF";
                                        //  print_r($color_data);

										?>
										<tr bgcolor="<?=$bgcolor;?>">
										<td width="100"><? echo $buyerArr[$color_data['buyer_name']]; ?></td>
										<td width="100"><? echo $job_no_prefix_num;?></td>
										<td width="100"><? echo $color_data['style_ref_no'];?></td>
										<td width="100"><? echo $color_data['po_number']; ?></td>
										<td width="100"><?  echo $color_data['grouping']; ?></td>
										<td width="100"><?  echo $color_data['shipment_date']; ?></td>
										<td width="100"><?  echo  $garments_item[$item_number_id]; ?></td>
										<td width="60"><?  echo  $plan_arr[$id]['qty']; ?></td>


										</tr>
										<?
										$i++;



										}
									}
								}
							}



					?>
				</tbody>
			</table>

		<br><br>
		<div style="width:790px" align="center"  id="scroll_body">
		<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" width="770px" border="1" rules="all" >
		<thead>
			<tr style="font-size:12px">
			<th width="30" rowspan="2">Sl.</th>
			<th width="80" rowspan="2">Sewing Out Date</th>
			<th width="80" rowspan="2">Floor</th>
			<th width="80" rowspan="2">Sewing Line</th>
			<th width="80" rowspan="2">Color</th>
			<th colspan="2">Sewing Qty</th>
			<th width="120" rowspan="2">Sewing Company</th>
			<th width="100" rowspan="2">Location</th>
			</tr>
			<tr style="font-size:12px">
			<th width="100">In-house</th>
			<th width="100">Outside</th>
			</tr>
		</thead>
		<table class="rpt_table" width="770" cellpadding="0" cellspacing="0" border="1" rules="all">
		  <tbody>
		  <?
					$i=1;
							foreach($sewing_arr as $id=>$po_data)
							{
								foreach($po_data as $production_date=>$val)
								{
											if ($i%2==0)
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
											//  print_r($color_data);
											$k=0;
											$product_source=$val['production_source'];

											?>
											<tr bgcolor="<?=$bgcolor;?>">
											<td width="30" ><? echo $i; ?></td>
											<td width="80"><? echo $production_date ;?></td>
											<td width="80" align="center"><? echo $floorArr[$val['sewing_floor']]; ?></td>
											<td width="80" align="center"><? echo $sewing_library[$prod_reso_arr[$val['sewing_line']]]; ?></td>
											<td width="80" align="center"><? echo $color_library[$color_number_id]; ?></td>
											<td width="100" align="right"><? echo $val['in_house_cut_qnty'];?></td>
											<td width="100" align="right"><? echo $val['out_bound_cut_qnty'];?></td>
											<?
												if($k==0){
											?>

											<td width="120" rowspan="<? echo $rowspan_arr[$id][$production_date]['row_count'];?>"><p>
												<?

												echo ($product_source==1)? $company_library[$val['serving_company']] : $supplier_arr[$val['serving_company']];

												?>
											    <?
												}
												?>
											</p>
											</td>
											<td width="100" rowspan="<? echo $rowspan_arr[$id][$production_date]['row_count'];?>"><p><? echo $location_library[$val['location']];?></p></td>
											</tr>
											<?
											$i++;
											$k++;
											$rowsapn_arr[$id][$production_date]=[$id][$production_date];

								}

						   }

						?>

		  </tbody>





	   </div>
	   </div>

	   </fieldset>
<?
}
if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");

	$job_cond_id = "";
	$style_cond = "";
	$order_cond = "";
	$company_id = str_replace("'","",$cbo_company_name);
	// $location_id = str_replace("'","",$cbo_location);
	// $floor_id   = str_replace("'","",$cbo_floor);
	// $buyer_name = str_replace("'","",$cbo_buyer_name);

	// $order_no  = str_replace( "'", "", $txt_order_no );
	// $style_no  = str_replace( "'", "", $txt_style_no );
	$int_ref   = str_replace( "'", "", $txt_int_ref);
	$from_date = str_replace( "'", "", $txt_date_from );
	$to_date  = str_replace( "'", "", $txt_date_to );
	$sql_cond="";
	$sql_cond .= ($company_id!=0) ? " and a.company_name in($company_id)" : "";
	// $sql_cond .= ($buyer_name!=0) ? " and a.buyer_name in($buyer_name)" : "";
	if (str_replace("'", "", $cbo_buyer_name) == 0)  $buyer_name = "";
	else $buyer_name = "and a.buyer_name=" . str_replace("'", "", $cbo_buyer_name) . "";
	if(str_replace("'","",$cbo_location)==0)  $location_name=""; else $location_name="and d.location=".str_replace("'","",$cbo_location)."";
	if(str_replace("'","",$cbo_floor)==0)  $floor_name=""; else $floor_name="and d.floor_id=".str_replace("'","",$cbo_floor)."";
	// $sql_cond .= ($floor_id!=0) ? " and d.floor_id in($floor_id)" : "";
	// $sql_cond .= ($job_no!="") ? " and b.job_no_mst='$job_no'" : "";
	// $sql_cond .=($order_no!="") ? "and b.po_number='$order_no'" : "";
	// $sql_cond .=($style_ref_no!="") ? "and a.style_ref_no='$style_no'" : "";
	if(str_replace("'","",$txt_int_ref)=="") $sql_cond=""; else $sql_cond=" and b.grouping=$txt_int_ref";
	$sql_cond .= ($from_date!="") ? " and d.production_date between '$from_date' and '$to_date'" : "";
	if (str_replace("'", "", $txt_job_no) == "") $job_cond_id = "";
	else $job_cond_id = "and a.job_no_prefix_num='" . str_replace("'", "", $txt_job_no) . "'";
	if (str_replace("'", "", $txt_order_no) == "") $order_cond = "";
	else $order_cond = "and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";
	if (str_replace("'", "", $hidden_style_id) != "")  $style_cond = "and b.id in(" . str_replace("'", "", $hidden_style_id) . ")";
	else  if (str_replace("'", "", $txt_style_no) == "") $style_cond = "";
	else $style_cond = "and a.style_ref_no like '%" . str_replace("'", "", $txt_style_no) . "%' ";





    if($type==1)
    {

         $sql="SELECT d.production_type,(CASE WHEN d.production_type ='8' THEN e.production_qnty END) AS packfinquantity, a.buyer_name,a.job_no_prefix_num,b.id,b.po_number,a.style_ref_no,b.grouping,c.color_number_id,c.item_number_id,d.floor_id,b.shiping_status,a.remarks
		 from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		 WHERE a.id=b.job_id  and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and c.po_break_down_id=d.po_break_down_id and d.production_type =8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sql_cond $buyer_name $location_name $floor_name $job_cond_id $order_cond $style_cond";
	//    echo $sql;
		$mainsql=sql_select($sql);
		if(count($mainsql)==0)
		{
			echo "Data Not Found";
			die();
		}
		$order_arr=array();$po_ids=array();

		foreach($mainsql as $row)
		{

				$order_arr[$row[csf('floor_id')]][$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['po_number'] = $row[csf('po_number')];
				$order_arr[$row[csf('floor_id')]][$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
				$order_arr[$row[csf('floor_id')]][$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$order_arr[$row[csf('floor_id')]][$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['grouping']=$row[csf('grouping')];
				$order_arr[$row[csf('floor_id')]][$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['packfinquantity']+=$row[csf('packfinquantity')];
				$order_arr[$row[csf('floor_id')]][$row[csf('job_no_prefix_num')]][$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['remarks']=$row[csf('remarks')];

				$po_ids[$row[csf('id')]]=$row[csf('id')];


			// else
			// {
			// 	$sewing_arr[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['sewingoutput']+=$row[csf('sewingoutput')];
			// 	$sewing_arr[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['floor_id']=$row[csf('floor_id')];
			// }
		}
		// echo '<pre>';
		// print_r($order_arr);
		// echo '</pre>';
		// ============================== data store to gbl table ==================================//
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=90");
		oci_commit($con);
		disconnect($con);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 90, 1, $po_ids, $empty_arr);//PO ID

		$sql_sewing="SELECT a.po_break_down_id,a.production_type,(CASE WHEN a.production_type ='5' THEN b.production_qnty END) AS sewing_output,a.production_date as sewing_date,d.item_number_id,d.color_number_id,
		a.floor_id as sewing_floor from pro_garments_production_mst a,pro_garments_production_dtls b,gbl_temp_engine tmp,wo_po_color_size_breakdown d  WHERE a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.po_break_down_id=d.po_break_down_id and b.color_size_break_down_id=d.id and tmp.entry_form=90 and tmp.ref_from=1 and tmp.user_id=$user_id and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.production_date desc";
	    //    echo $sql_sewing;

		// if(count($sewing_sql)==0)
		// {
		// 	echo "No data found";
		// 	die();
		// }

		$sql_plan_cut="SELECT b.po_break_down_id,b.color_number_id,b.item_number_id,b.plan_cut_qnty from wo_po_color_size_breakdown b,gbl_temp_engine tmp WHERE  b.po_break_down_id=tmp.ref_val and tmp.entry_form=90 and tmp.ref_from=1 and tmp.user_id=$user_id and b.status_active=1 and b.is_deleted=0";
		//  echo $sql_plan_cut;die();
		$plan_sql=sql_select($sql_plan_cut);
		$plan_arr=array();

		foreach($plan_sql as $v)
		{
			$plan_arr[$v[csf('po_break_down_id')]][$v[csf('item_number_id')]][$v[csf('color_number_id')]]['plan_cut_qnty']+=$v[csf('plan_cut_qnty')];
		}
        // echo '<pre>';
		// print_r($plan_arr);
		// echo '</pre>';

        $sewing_sql=sql_select($sql_sewing);$sewing_arr=array();
		foreach($sewing_sql as $val)
		{
				$sewing_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['sewing_output']+=$val[csf('sewing_output')];
				$sewing_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['sewing_floor']=$val[csf('sewing_floor')];
				$sewing_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['sewing_date']=$val[csf('sewing_date')];
				// if($date_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]=='')
				// {

				// 	$sewing_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]['sewing_date']=$val[csf('sewing_date')];
				// }
				// $date_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('color_number_id')]]=$val[csf('sewing_date')];

		}
		// echo '<pre>';
		// print_r($sewing_arr);
		// echo '</pre>';







		// ============================== End ==================================//
       $subtotal_arr=array();$packfin_subtotal_arr=array();$rowspan_arr=array();
          foreach($order_arr as $floor_id=>$floor_data)
		  {
				foreach($floor_data as $job_no_prefix_num=>$job_data)
				{
					foreach($job_data as $po_id=>$po_data)
					{
						foreach($po_data as $item_number_id=>$item_data)
						{
							foreach($item_data as $color_number_id=>$color_data)
							{
								//   print_r($color_data);
									// $subtotal_arr[$job_no_prefix_num][$po_id]['qty']+=$color_data['qty'];
									// $gr_total+=$color_data['qty'];

									$rowspan_arr[$floor_id][$job_no_prefix_num][$po_id]++;
									$packfin_subtotal_arr[$job_no_prefix_num][$po_id]['packfinquantity']+=$color_data['packfinquantity'];
									//  $packfin_subtotal_arr[$floor_id]['row_count']+=1;
							}
						}
					}
				}
			}

		//    print_r($subtotal_arr);
		//   print_r($gr_subtotal_arr);
		// echo $gr_total;



		$sewing_subtotal_arr=array();
		foreach($sewing_arr as $po_id=>$po_data)
		{
			foreach($po_data as $item_number_id=>$item_data)
			{
				foreach($item_data as $color_number_id=>$color_data)
				{
					//   print_r($color_data);
						$sewing_subtotal_arr[$po_id]['sewing_output']+=$color_data['sewing_output'];
						$gr_total_sewing_output+=$color_data['sewing_output'];
						// $gr_total_sewing_output+=$sewing_subtotal_arr[$id]['sewingoutput'];
					    // $sewing_subtotal_arr[$id]['row_count']+=1;

				}
		    }
	    }
		 	//   print_r($sewing_subtotal_arr);

			    // print_r($packfin_subtotal_arr);
		$plan_subtotal_arr=array();
		foreach($plan_arr as $po_id=>$po_data)
		{
			foreach($po_data as $item_number_id=>$item_data)
			{
				foreach($item_data as $color_number_id=>$r)
				{
					//   print_r($color_data);
						$plan_subtotal_arr[$po_id]['qty']+=$r['plan_cut_qnty'];
						$gr_total+=$r['plan_cut_qnty'];

						// $gr_total_sewing_output+=$sewing_subtotal_arr[$id]['sewingoutput'];
					    // $sewing_subtotal_arr[$id]['row_count']+=1;

				}
		    }
	    }
        //  print_r($plan_subtotal_arr);

		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=90");
		oci_commit($con);
		disconnect($con);

		?>
		<br>
		<div style="width:2420px">
		<fieldset width="100%">
			<table class="rpt_table" width="2400" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="100">SI</th>
						<th width="100">Finishing Floor</th>
						<th width="100">Buyer</th>
						<th width="100">Job</th>
						<th width="100">Order No</th>
						<th width="100">Style</th>
						<th width="100">Int ref</th>
						<th width="100">Gmts Item</th>
						<th width="100">Color</th>
						<th width="100">Colorwise Quantity</th>
						<th width="100">Total Order Qty</th>
						<th width="100">First Output Date</th>
						<th width="100">Color Wise Sewing Output</th>
						<th width="100">Total Sewing Output</th>
						<th width="100">Receive Bal: from Order qty</th>
						<th width="100">Total Pack and Fin</th>
						<th width="100">WIP<br>Pack and Fin:Bal(From Rcv)<br></th>
						<th width="100">Pack and Fin:Bal(From Order Qty) </th>
						<th width="100">Order Wise Total Pack and Fin Qty </th>
						<th width="100">WIP<br>Order Wise Total Pack and Fin:Bal(From Rcv)</br> </th>
						<th width="100">Order Wise Total Pack and Fin Bal(From Order qty)</th>
						<th width="100">Sewing Floor </th>
						<th width="100">Shipment Status </th>
						<th width="100">Remarks </th>
					</tr>
				</thead>
			</table>
			<table class="rpt_table" width="2400" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="html_search_1">
				<tbody id="table_body_id">
					<?
					$i=1;

					$buyer_arr=array();

                    foreach($order_arr as $floor_id=>$floor_data)
					{

						foreach($floor_data as $job_no_prefix_num=>$job_data)
						{

							foreach($job_data as $po_id=>$po_data)
							{
                                $f=0;
								foreach($po_data as $item_number_id=>$item_data)
								{
									foreach($item_data as $color_number_id=>$color_data)
									{
										if ($i%2==0)
										$bgcolor="#E9F3FF";
										else
										$bgcolor="#FFFFFF";
                                        //  print_r($color_data);
										$floor= implode(",",array_unique(array_filter(explode(",", $color_data['floor_id']))));


										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="100"><? echo $i;?></td>
												<td width="100"><p><? echo $floorArr[$floor_id]; ?></p></td>
												<td width="100"><p><? echo $buyerArr[$color_data['buyer_name']]; ?></p></td>
												<td width="100"><? echo $job_no_prefix_num;?></td>
												<td width="100"><p><? echo $color_data['po_number']; ?></p></td>
												<td width="100"><p><? echo $color_data['style_ref_no']; ?></p></td>
												<td width="100"><?  echo $color_data['grouping']; ?></td>
												<td width="100"><p><? echo $garments_item[$item_number_id];?></p></td>
												<td width="100"><p><? echo $color_library[$color_number_id];?></p></td>
												<td width="100" align="right"><? echo $plan_arr[$po_id][$item_number_id][$color_number_id]['plan_cut_qnty'];?></td>
												<?
												if($f==0){
												?>
													<td width="100" align="right"  rowspan="<? echo $rowspan_arr[$floor_id][$job_no_prefix_num][$po_id];?>"><? echo $plan_subtotal_arr[$po_id]['qty'];
													$buyer_arr[$color_data['buyer_name']]['qty']+=$plan_subtotal_arr[$po_id]['qty'];

													?></td>
												<?
												}
												?>
												<td width="100" align="center"><? echo $sewing_arr[$po_id][$item_number_id][$color_number_id]['sewing_date'];?></td>
												<td width="100" align="right"><? echo $sewing_arr[$po_id][$item_number_id][$color_number_id]['sewing_output'];  ?></td>
												<?
												if($f==0){

												 ?>
												  <td width="100" align="right" rowspan="<? echo $rowspan_arr[$floor_id][$job_no_prefix_num][$po_id];?>"><? echo $sewing_subtotal_arr[$po_id]['sewing_output']; $buyer_arr[$color_data['buyer_name']]['sewing_output']+=$sewing_subtotal_arr[$po_id]['sewing_output']; ?></td>
												<?
												}
												?>

												<td title="Colorwise Quantity-Color Wise Sewing Output" width="100" align="right"><? $total_rcv_qty=0; $total_rcv_qty=$plan_arr[$po_id][$item_number_id][$color_number_id]['plan_cut_qnty']- $sewing_arr[$po_id][$item_number_id][$color_number_id]['sewing_output']; echo $total_rcv_qty; $gr_total_rcv_qty+=$total_rcv_qty;?></td>
												<td width="100" align="right"><?  echo $color_data['packfinquantity']; $buyer_arr[$color_data['buyer_name']]['total_pack_fin_qty']+=$color_data['packfinquantity']; ?></td>
												<td title="Color Wise Sewing Output-Total Pack and fin" width="100" align="right"><?  $total_rcv_wip_qty=0;$total_rcv_wip_qty= $sewing_arr[$po_id][$item_number_id][$color_number_id]['sewing_output']- $color_data['packfinquantity'];echo $total_rcv_wip_qty;   ?></td>

												<td title="ColorWise Quantity-Total Pack and fin" width="100" align="right"> <? $total_pack_fin_bal=0; $total_pack_fin_bal=$plan_arr[$po_id][$item_number_id][$color_number_id]['plan_cut_qnty']-$color_data['packfinquantity'];echo $total_pack_fin_bal; ?> </td>

												<?
												if($f==0){

												?>
													<td width="100" align="right"  rowspan="<? echo $rowspan_arr[$floor_id][$job_no_prefix_num][$po_id];?>"><? echo $packfin_subtotal_arr[$job_no_prefix_num][$po_id]['packfinquantity'];?></td>
												<?
												}
												?>
												<?
												if($f==0){

												?>
												<td title="Total Sewing Output- Order Wise Total Pack and Fin Qty" width="100" align="right"  rowspan="<? echo $rowspan_arr[$floor_id][$job_no_prefix_num][$po_id];?>"><? $bal_total=0; $bal_total=$sewing_subtotal_arr[$po_id]['sewing_output']- $packfin_subtotal_arr[$job_no_prefix_num][$po_id]['packfinquantity'];echo $bal_total; ?> </td>
												<?
												}
												?>
												<?
												if($f==0){

												?>
												<td title="Total Order Qty-Order Wise Total Pack and Fin Qty" width="100"align="right"  rowspan="<? echo $rowspan_arr[$floor_id][$job_no_prefix_num][$po_id];?>"><? $main_total=0; $main_total=$plan_subtotal_arr[$po_id]['qty']- $packfin_subtotal_arr[$job_no_prefix_num][$po_id]['packfinquantity'];echo $main_total; ?> </td>
												<?
												}
												?>
												<td width="100"> <? echo $floorArr[$sewing_arr[$po_id][$item_number_id][$color_number_id]['sewing_floor']];?> </td>
												<td width="100"> <p><? echo $shipment_status[$row[csf('shiping_status')]];?></p>  </td>
												<td width="100"><p> <a href="##" onclick="openmypage_sewingoutput('<? echo $floor_id; ?>','<? echo $job_no_prefix_num; ?>','<? echo $po_id; ?>','<? echo $item_number_id; ?>', '<? echo $color_number_id; ?>',  'sewing_popup',850,350)"><? echo "view"; ?></a></p> </td>
										</tr>
										<?
										$i++;
										$f++;

										$rowspan_arr[$floor_id][$job_no_prefix_num][$po_id]=[$floor_id][$job_no_prefix_num][$po_id];
										// $rowsapn_arr[$id]=$id;
										// $buyer_arr[$color_data['buyer_name']]['qty']=$gr_total;
										// $buyer_arr[$color_data['buyer_name']]['sewing_output']=$gr_total_sewing_output;
										$buyer_arr[$color_data['buyer_name']]['pack_fin_qty']=$gr_total_rcv_qty;
										// $buyer_arr[$color_data['buyer_name']]['total_pack_fin_qty']=$gr_total_pack_fin;

									}
								}
							}
						}

					}
					// echo '<pre>';
					// print_r($buyer_arr);
					// echo '</pre>';
					?>
				</tbody>
			</table>


		 </div>

				<h1><b>Finishing Wip Summary</b> </h1>
				<div style="width:720px">
				<table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                          <tr>
						    <th width="100">Buyer Name</th>
							<th width="100">Total Order Qty</th>
							<th  width="100">TTl Rcv</th>
							<th width="100">Rcv Bal from order qty</th>
							<th width="100">Total Pack and fin </th>
							<th width="100">Total Packing and finishing:bal (From Rcv)</th>
							<th width="100">Total Packing and finishing:bal( From Order Qty)</th>
						  </tr>
					</thead>
					<table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tbody>
					<?
					$i=1;

						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";

                        //   print_r($a);

						// print_r($buyer_arr);
						?>
						<?
                         foreach($buyer_arr as $buyer_name=>$val)
						 {
                                     $sewing_total_output=$val['sewing_output'];
									 $pack_fin_total_output=$val['total_pack_fin_qty'];
									 $gr_pack_sewing=$val['sewing_output']-$val['total_pack_fin_qty'];
									 $total_color_qty=$val['qty'];
									 $gr_total_packing_fin_bal=$total_color_qty-$pack_fin_total_output;
									 $gr_total_rcv_balance=$total_color_qty-$sewing_total_output;
                            ?>
							<tr bgcolor="<?=$bgcolor;?>">
								<td width="100"><? echo $buyerArr[$buyer_name]; ?></td>
								<td width="100" align="right"><? echo $val['qty']; $grand_order_qty+=$val['qty']; ?></td>
								<td title="Total Sewing Output" width="100" align="right"><? echo $val['sewing_output']; $grand_sewing_output+=$val['sewing_output']; ?></td>
								<td width="100" align="right" title="Total Order Qty-Total Sewing Output"><? echo $gr_total_rcv_balance; $grand_total_rcv_balance+=$gr_total_rcv_balance; ?></td>
								<td width="100" align="right"><? echo $val['total_pack_fin_qty']; $grand_total_pack_fin_qty+=$val['total_pack_fin_qty'];?></td>
								<td width="100" align="right" title="Total Sewing Output-Total Pack Finish"><? echo $gr_pack_sewing; $grand_pack_sewing+=$gr_pack_sewing; ?></td>
								<td width="100" align="right" title="Total Order Qty-Total Pack Finish"><?   echo $gr_total_packing_fin_bal; $grand_total_packing_fin_bal+=$gr_total_packing_fin_bal; ?></td>

							</tr>
							<?
							}
						?>
					</tbody>
					<tfoot>
						<th>Total</th>
						<th><? echo $grand_order_qty;?></th>
						<th><? echo $grand_sewing_output;?></th>
						<th><? echo $grand_total_rcv_balance;?></th>
						<th><? echo $grand_total_pack_fin_qty;?></th>
						<th><? echo $grand_pack_sewing;?></th>
						<th><? echo $grand_total_packing_fin_bal;?></th>
					</tfoot>
				 </div>
		</fieldset>


		<?


    }

}

?>