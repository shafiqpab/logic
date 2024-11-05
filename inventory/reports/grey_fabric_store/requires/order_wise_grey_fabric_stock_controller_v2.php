<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$storeNameArr=return_library_array( "SELECT id,store_name from lib_store_location ", "id", "store_name" );

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action == "load_drop_down_store")
{
	echo create_drop_down("cbo_store_name", 150, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in(13)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_store_name','0','0','','0');\n";
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=197 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#show').hide();\n";
	echo "$('#buyer_wise').hide();\n";
	echo "$('#report2').hide();\n";
	echo "$('#report3').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==108){echo "$('#show').show();\n";}
			if($id==54){echo "$('#buyer_wise').show();\n";}     
			if($id==256){echo "$('#report2').show();\n";}     
			if($id==267){echo "$('#report3').show();\n";}         
		}
	}
	exit(); 
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}

	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th>
							<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</th>
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
								$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller_v2', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
exit();  }

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array; var selected_name = new Array;

		function check_all_datas()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
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

		function js_set_value2( str ) {

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
				name += selected_name[i] + ',';

			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );


			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			//$('#hide_recv_id').val( rec_id );
			//$("#hide_booing_type").val(str[3]);
		}
	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:680px;">
				<table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Booking No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />

						<!--   <input type="hidden" name="hide_recv_id" id="hide_recv_id" value="" />-->
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
								$search_by_arr=array(1=>"Booking No",2=>"Job No",3=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_booking_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller_v2', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
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

if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}




	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==3)
	{
		$search_field="a.style_ref_no";
	}
	else if($search_by==2)
	{
		$search_field="a.job_no_prefix_num";
	}
	else $search_field="b.booking_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(a.insert_date)";
	else if($db_type==2) $month_field_by=" and to_char(a.insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";

	$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.booking_no_prefix_num,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num  order by a.job_no desc";

	//echo $sql;die;
	$sqlResult=sql_select($sql);
	?>
	<div align="center">

		<fieldset style="width:650px;margin-left:10px">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Company</th>
						<th width="110">Buyer</th>
						<th width="110">Job No</th>
						<th width="120">Style Ref.</th>
						<th width="">Booking No</th>

					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
						<?
						$i=1;
						foreach($sqlResult as $row )
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no_prefix_num')];
						//echo $data;
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
								<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
								<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
								<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
								<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								<td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
				<table width="650" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%">
								<div style="width:50%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_datas()"/>
									Check / Uncheck All
								</div>
								<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>

	</div>


	<?

	exit();
}

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#order_no_id").val(splitData[0]);
			$("#order_no_val").val(splitData[1]);
			parent.emailwindow.hide();
		}

	</script>
	<input type="hidden" id="order_no_id" />
	<input type="hidden" id="order_no_val" />
	<?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num='$data[2]'";

	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no DESC";
	//echo $sql;
	$arr=array(1=>$buyer_arr);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_wise_grey_fabric_stock_controller_v2",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

if($action=="check_color_id")
{
	echo load_html_head_contents("Color Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
	<?
	$sql="select id, color_name from lib_color where is_deleted=0 and status_active=1 order by id";
	$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "ID,Color Name", "50,200","300","300",0, $sql, "js_set_value", "id,color_name", "", 1, "0,0", $arr , "id,color_name", "",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_buyer_id 		= str_replace("'","",$cbo_buyer_id);
	$txt_order_no 		= str_replace("'","",$txt_order_no);
	$hdn_color 			= str_replace("'","",$hdn_color);
	$txt_color 			= str_replace("'","",$txt_color);
	$cbo_year 			= str_replace("'","",$cbo_year);
	$booking_no 		= str_replace("'","",$txt_booking_no);
	$job_no 			= str_replace("'","",$txt_job_no);
	$date_from			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$rpt_type 			= str_replace("'","",$rpt_type);
	$txt_ir_no 			= str_replace("'","",$txt_ir_no);
	$txt_file_no 		= str_replace("'","",$txt_file_no);

	$_SESSION["date_from"]=date('Y-m-d',strtotime($date_from));
	$_SESSION["date_to"]=date('Y-m-d',strtotime($date_to));

	$company_arr 		= return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_array 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
	if($job_no!='') $job_no_cond="and d.job_no_prefix_num in($job_no)";else $job_no_cond="";
	if($txt_order_no!='') $po_no_cond="and c.po_number='$txt_order_no'";else $po_no_cond="";
	if($txt_ir_no!='') $ir_no_cond="and c.grouping LIKE '%$txt_ir_no%'";else $ir_no_cond="";
	if($txt_file_no!='') $file_no_cond="and c.file_no LIKE '%$txt_file_no%'";else $file_no_cond="";

	if($hdn_color!='') $color_cond="and b.fabric_color_id in($hdn_color)";else $color_cond="";
	if($hdn_color!='') $color_cond2=" and b.color_id in('$hdn_color')";else $color_cond2="";
	if($db_type==0) $year_field_cond="and YEAR(a.insert_date)=$cbo_year";
	else if($db_type==2) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond=" and e.transaction_date <= '$end_date'";
		$date_cond2=" and a.transfer_date <= '$end_date'";
	}

	if($cbo_store_name!="")
	{
		$store_cond  = " and e.store_id in($cbo_store_name)";
		$store_cond1  = " and a.store_id in($cbo_store_name)";
		$store_cond2 = " and b.to_store in($cbo_store_name)";
		$store_cond3 = " and b.from_store in($cbo_store_name)";
	}

	if($job_no_cond!="" || $book_no_cond!="" || $po_no_cond !="" || $color_cond!="" || $cbo_year!="" || $ir_no_cond !="" || $file_no_cond !="")
	{
		$sql="select A.BUYER_ID, B.JOB_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, SUM(B.GREY_FAB_QNTY) AS GREY_REQ_QNTY, A.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO 
		from wo_booking_mst A, wo_booking_dtls B, wo_po_break_down C, wo_po_details_master D 
		where A.company_id=$cbo_company_id and A.item_category in(2,13) and A.is_deleted=0 and A.status_active=1 and A.booking_no=B.booking_no and B.is_deleted=0 and B.status_active=1 and B.po_break_down_id=C.id and C.job_no_mst=D.job_no $job_no_cond $book_no_cond $po_no_cond $color_cond $year_field_cond $ir_no_cond $file_no_cond 
		group by A.buyer_id, B.job_no, B.po_break_down_id, B.construction, B.fabric_color_id, A.booking_no, D.buyer_name, C.po_number, D.style_ref_no";
		//echo $sql;die;
		$sql_result=sql_select($sql);
		$po_ids='';

		foreach( $sql_result as $row )
		{
			if($po_ids=='') $po_ids=$row['PO_ID'];else $po_ids.=",".$row['PO_ID'];
		}

		$po_ids = array_unique(explode(",",$po_ids));
		$poIds_cond_roll="";

		$po_idss = implode(",", $po_ids);
		if($db_type==2 && count($po_ids)>999)
		{
			$po_chunk=array_chunk($po_ids,999) ;
			$barcode_cond = " and (";

			foreach($po_chunk as $chunk_arr)
			{
				$poIds_cond_roll.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			}

			$poIds_cond_roll = chop($poIds_cond_roll,"or ");
			$poIds_cond_roll .=")";
		}
		else
		{
			$poIds_cond_roll  = " and c.po_breakdown_id in($po_idss)";
		}

		unset($sql_result);
	}
	// check tomorrow 19020388324
	
	$main_query="SELECT A.ID, A.ENTRY_FORM, A.RECEIVE_BASIS, A.BOOKING_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY,B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.COLOR_RANGE_ID, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.PROD_ID, NULL AS FROM_TRANS_ID, NULL AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 1 AS TYPE, E.STORE_ID, E.TRANSACTION_DATE, C.RE_TRANSFER
	from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C
	where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and A.company_id=$cbo_company_id and A.entry_form in(2,22,58) and B.trans_id<>0 and C.entry_form in(2,22,58) and C.status_active=1 and C.is_deleted=0 $trans_date $poIds_cond_roll $year_field_cond $date_cond $store_cond $color_cond2 and A.status_active=1 and A.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0
	union all
	select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY,D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID,D.GSM GSM,D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE,B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER
	from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
	where A.id=B.mst_id and B.id=C.dtls_id and B.from_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(83) and C.entry_form in(83) and C.status_active=1 and C.is_deleted=0 $transfer_date $poIds_cond_roll $year_field_cond $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0
	union all
	select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID, B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER
	from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
	where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(82) and C.entry_form in(82) and C.status_active=1 and C.is_deleted=0 $transfer_date $poIds_cond_roll $year_field_cond $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0
	union all
	select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE AS TRANSACTION_DATE, C.RE_TRANSFER
	from  inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
	where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(183) and C.entry_form in(183) and C.status_active=1 and C.is_deleted=0 $transfer_date $poIds_cond_roll $year_field_cond $date_cond2 $store_cond2 and C.booking_without_order=0 and A.status_active=1 and B.status_active=1 and C.status_active=1 ";//and a.transfer_criteria in (1)
	//echo $main_query;die();
	//==================================
	$result=sql_select( $main_query );
	//echo count($result);die;
	if(count($result)==0)
	{
		echo "No Data Found";die;
	}
	$store_arr=array();
	$con = connect();
	if(!empty($result))
	{
		/*foreach ($result as $row)
		{
			// $barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			if($row["BARCODE_NO"]!="" && $barcode_check[$row["BARCODE_NO"]]=="")
			{
				$barcode_check[$row["BARCODE_NO"]]=$row["BARCODE_NO"];
				$barcodeNo = $row["BARCODE_NO"];
				$rID=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$barcodeNo,1)");
			}
		}
		if($rID)
		{
			oci_commit($con);
		}*/
		
		foreach($result as $row)
		{
			if($row["PO_BREAKDOWN_ID"]!="" && $po_id_check[$row["PO_BREAKDOWN_ID"]]=="")
			{
				$po_id_check[$row["PO_BREAKDOWN_ID"]]=$row["PO_BREAKDOWN_ID"];
				$poId = $row["PO_BREAKDOWN_ID"];
				$rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$poId,2)");
			}
		}
		if($rID2)
		{
			oci_commit($con);
		}
		
		$production_sql = "SELECT B.BARCODE_NO, A.COLOR_RANGE_ID, A.YARN_LOT, A.YARN_COUNT, A.BRAND_ID, B.PO_BREAKDOWN_ID, A.PROD_ID, B.BOOKING_NO, B.RECEIVE_BASIS, A.COLOR_ID, A.FEBRIC_DESCRIPTION_ID, A.GSM, A.WIDTH, A.STITCH_LENGTH, A.MACHINE_DIA, A.MACHINE_GG, A.MACHINE_NO_ID, C.KNITTING_SOURCE, C.CHALLAN_NO AS PRODUCTION_CHALLAN, C.KNITTING_COMPANY, A.YARN_PROD_ID, A.BODY_PART_ID 
		from inv_receive_master C, pro_grey_prod_entry_dtls A, pro_roll_details B, tmp_poid F
		where C.id=A.mst_id and A.id=B.dtls_id and C.entry_form  in(2,22) and B.entry_form in(2,22) and A.status_active=1 and B.status_active=1 and B.po_breakdown_id=F.poid and F.userid=$user_id and F.type=2";
		//echo $production_sql;die;
		$production_info = sql_select($production_sql);
		foreach ($production_info as $row)
		{
			$prodBarcodeData[$row['BARCODE_NO']]["prod_basis"] 			= $row['RECEIVE_BASIS'];
			$prodBarcodeData[$row['BARCODE_NO']]["prog_book"] 				= $row['BOOKING_NO'];
			$prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] 		= $row['COLOR_RANGE_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] 				= $row['YARN_LOT'];
			$prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] 			= $row['YARN_COUNT'];
			$prodBarcodeData[$row['BARCODE_NO']]["yarn_prod_id"] 			= $row['YARN_PROD_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["prod_id"] 				= $row['PROD_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["color_id"] 				= $row['COLOR_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] 	= $row['FEBRIC_DESCRIPTION_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["gsm"] 					= $row['GSM'];
			$prodBarcodeData[$row['BARCODE_NO']]["width"] 					= $row['WIDTH'];
			$prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] 			= $row['STITCH_LENGTH'];
			$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"] 			= $row['MACHINE_DIA'];
			$prodBarcodeData[$row['BARCODE_NO']]["machine_gg"] 			= $row['MACHINE_GG'];
			$prodBarcodeData[$row['BARCODE_NO']]["machine_no_id"] 			= $row['MACHINE_NO_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["prod_challan"] 			= $row['PRODUCTION_CHALLAN'];
			$prodBarcodeData[$row['BARCODE_NO']]["knitting_source"] 		= $row['KNITTING_SOURCE'];
			$prodBarcodeData[$row['BARCODE_NO']]["knitting_company"] 		= $row['KNITTING_COMPANY'];
			$prodBarcodeData[$row['BARCODE_NO']]["body_part_id"] 			= $row['BODY_PART_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["brand_id"] 				= $row['BRAND_ID'];
			$allDeterArr[$row['FEBRIC_DESCRIPTION_ID']] 					= $row['FEBRIC_DESCRIPTION_ID'];
			$allColorArr[$row['COLOR_ID']] 								= $row['COLOR_ID'];
			$allYarnProdArr[$row['YARN_PROD_ID']] 							= $row['YARN_PROD_ID'];
		}
		unset($production_info);
		
		foreach($result as $row)
		{
			if($hdn_color)
			{
				if($hdn_color !=$prodBarcodeData[$row['BARCODE_NO']]["color_id"])continue;
			}

			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));

			$febric_description = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];
			$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];
			
			
			if($row['ENTRY_FORM']== 2 || $row['ENTRY_FORM']== 22 || $row['ENTRY_FORM']== 58)
			{
				$all_rcv[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] += $row['QNTY'];
				$count_rcv_barcode[$row['BARCODE_NO']]=$row['BARCODE_NO'];
			}
			else
			{
				if($count_rcv_barcode[$row['BARCODE_NO']]=="")
				{
					$all_rcv[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] += $row['QNTY'];
				}
			}
			
			$row_barcodess[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$row['BARCODE_NO']]=$row['BARCODE_NO'];
			if($transaction_date < $date_frm)
			{
				if($row['ENTRY_FORM']== 2 || $row['ENTRY_FORM']== 22 || $row['ENTRY_FORM']== 58){
					$openingReceiveArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] += $row['QNTY'];
					$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"]][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["receive"] += 0;

					$PoWiseData[$row['PO_BREAKDOWN_ID']]["opening_rcv"] += $row['QNTY'];
				}else{
					$openingTransInArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] += $row['QNTY'];
					$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"]][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["in"] += 0;

					$PoWiseData[$row['PO_BREAKDOWN_ID']]["opening_trans_in"]  += $row['QNTY'];
				}
			}else{
				if($row['ENTRY_FORM']== 2 || $row['ENTRY_FORM']== 22 || $row['ENTRY_FORM']== 58){
					$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"]][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["receive"] += $row['QNTY'];

					$PoWiseData[$row['PO_BREAKDOWN_ID']]["receive"] += $row['QNTY'];

				}else{
					$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"]][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["in"] += $row['QNTY'];

					$PoWiseData[$row['PO_BREAKDOWN_ID']]["trans_in"] += $row['QNTY'];
				}
			}

			$color_wise_count[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]]++;

			if($row['COLOR_ID']!=""){
				$all_color_arr[$row['COLOR_ID']] = $row['COLOR_ID'];
			}

			$barcodeArr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
			$febric_description_arr[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"]][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["febric_descriptions"]=$febric_description;
			$storeArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$row['RE_TRANSFER']] .= $row['STORE_ID'].",";
			$noOfRollArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] .= $row['BARCODE_NO'].",";
			$bookingIdArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] .= $row['BOOKING_ID'].",";
			$store_id_arr[$row['STORE_ID']] = $row['STORE_ID'];
			// $po_id_arr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
			$color_count++;
			
		}
		unset($result);
		
		$color_count = 1;
		$_SESSION["date_frm"]=date('Y-m-d',strtotime($start_date));
	}
	//echo "<pre>";print_r($dataArr);die;
	
	//echo $main_query;die();
	/*echo "<pre>";
	print_r($openingReceiveArr);
	echo "</pre>".$test_datas;*/
	//if(!empty($po_id_arr)){
	/*$receive_barcodes = implode(",", $po_id_arr);
	if($db_type==2 && count($po_id_arr)>999)
	{
		$barcode_chunk=array_chunk($po_id_arr,999) ;
		$po_id_cond = " and (";

		foreach($barcode_chunk as $chunk_arr)
		{
			$po_id_cond.=" c.id in(".implode(",",$chunk_arr).") or ";
		}

		$po_id_cond = chop($po_id_cond,"or ");
		$po_id_cond .=")";
	}
	else
	{
		$po_id_cond  = " and c.id in(".implode(",",$po_id_arr).")";
	}*/
	//and c.id in(SELECT poid from tmp_poid where userid=$user_id and type=2)
		 
	$po_sql="SELECT B.JOB_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, SUM(B.GREY_FAB_QNTY) AS GREY_REQ_QNTY, B.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO, B.IS_SHORT, D.BUYER_NAME as BUYER_ID, c.GROUPING, C.FILE_NO 
	from wo_booking_dtls B, wo_po_break_down C, wo_po_details_master D, tmp_poid E  
	where D.COMPANY_NAME=$cbo_company_id and B.BOOKING_TYPE=1 and B.is_deleted=0 and B.status_active=1 and B.po_break_down_id=C.id and C.job_id=D.id and C.id=E.poid and E.userid=$user_id and E.type=2
	group by B.JOB_NO, B.PO_BREAK_DOWN_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, B.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO, b.IS_SHORT, D.BUYER_NAME, c.GROUPING, C.FILE_NO
	order by B.PO_BREAK_DOWN_ID asc, B.IS_SHORT desc";
	//echo $po_sql;die;

	$po_sql_result=sql_select($po_sql);
	$po_ids='';

	foreach( $po_sql_result as $row )
	{
		//$key=$row['BUYER_ID'].$row['JOB_NO'].$row[csf('po_id')].$row['CONSTRUCTION'].$row['FABRIC_COLOR_ID'];
		$key=$row['BUYER_ID'].$row['JOB_NO'].$row['PO_ID'].$row['CONSTRUCTION'].$row['FABRIC_COLOR_ID'];
		$grey_qnty_array[$key] += $row['GREY_REQ_QNTY'];

		$booking_array[$row['PO_ID']]['job_no'] 		= $row['JOB_NO'];
		$booking_array[$row['PO_ID']]['po_number'] 		= $row['PO_NUMBER'];
		$booking_array[$row['PO_ID']]['style_ref_no'] 	= $row['STYLE_REF_NO'];
		$booking_array[$row['PO_ID']]['buyer_name'] 	= $row['BUYER_NAME'];
		$booking_array[$row['PO_ID']]['grouping'] 	    = $row['GROUPING'];
		$booking_array[$row['PO_ID']]['file_no'] 	    = $row['FILE_NO'];
		if($booking_po_check[$row['PO_ID']][$row['BOOKING_NO']]=="")
		{
			$booking_po_check[$row['PO_ID']][$row['BOOKING_NO']]=$row['BOOKING_NO'];
			if($row['IS_SHORT']==1) $booking_nos=$row['BOOKING_NO']."[S]"; else $booking_nos=$row['BOOKING_NO'];
			$booking_array[$row['PO_ID']]['booking_no'] .= $booking_nos.",";
		}
	}
	unset($po_sql_result);
	//}

	/*echo "<pre>";
	print_r($grey_qnty_array);
	echo "</pre>";
	die;*/

	if(!empty($store_id_arr)){
		$storeNameArr=return_library_array( "select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id and a.id in(".implode(",",$store_id_arr).")", "id", "store_name" );
	}
	$color_ids = rtrim(implode(",",$all_color_arr),", ");
	$colorArr=return_library_array( "select id, color_name from lib_color", "id", "color_name" );
	$febric_description_arr = array_filter($febric_description_arr);
	if(!empty($febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($febric_description_arr,999);
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}

		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}
	//and c.barcode_no in(SELECT poid from tmp_poid where userid=$user_id and type=1)
	$split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	from pro_roll_split C, pro_roll_details D, tmp_poid E 
	where C.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and D.PO_BREAKDOWN_ID=E.poid and E.userid=$user_id and E.type=2");

	if(!empty($split_chk_sql))
	{
		foreach ($split_chk_sql as $val)
		{
			$split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
		}

		$split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE from pro_roll_details A, pro_roll_details B where A.barcode_no in (".implode(",", $split_barcode_arr).") and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
		if(!empty($split_ref_sql))
		{
			foreach ($split_ref_sql as $value)
			{
				$mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
			}
		}
	}
	unset($split_chk_sql);
	unset($split_ref_sql);

	$issue_sql = "SELECT C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM
	from pro_roll_details C, inv_grey_fabric_issue_dtls B, inv_transaction E, tmp_poid F
	where C.entry_form=61 and C.status_active=1 and C.is_deleted=0 $date_cond $store_cond	
	and C.booking_without_order = 0 and C.dtls_id=B.id and B.trans_id=E.id and E.transaction_type=2 and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2
	union all
	select D.PO_BREAKDOWN_ID, C.BARCODE_NO, A.TRANSFER_DATE TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM
	from order_wise_pro_details D, inv_item_transfer_dtls B, pro_roll_details C, inv_item_transfer_mst A, tmp_poid F
	where D.trans_id=B.trans_id and B.id=C.dtls_id and C.mst_id=A.id and b.mst_id=A.id and A.entry_form=83 and C.entry_form=83 and C.status_active=1 and C.is_deleted=0 and D.status_active=1 and D.is_deleted=0 and D.trans_type=6 and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2
	$date_cond2 $store_cond3 and C.booking_without_order = 0 and C.re_transfer=0
	union all
	select B.FROM_ORDER_ID AS PO_BREAKDOWN_ID, C.BARCODE_NO, A.TRANSFER_DATE TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM
	from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, tmp_poid F
	where A.id = B.mst_id and B.id = C.dtls_id and A.id = C.mst_id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and C.re_transfer=0
	$date_cond2 $store_cond3
	and A.entry_form = 82 and C.entry_form = 82 and B.status_active = 1 and C.status_active = 1 and A.status_active = 1 and C.booking_without_order = 0
	union all
	select A.FROM_ORDER_ID AS PO_BREAKDOWN_ID, C.BARCODE_NO, A.TRANSFER_DATE TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM
	from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, tmp_poid F
	where A.id = B.mst_id and A.id = C.mst_id and B.id = C.dtls_id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and C.re_transfer=0
	$date_cond2 $store_cond3
	and A.entry_form = 110 and C.entry_form = 110 and B.status_active = 1 and C.status_active = 1 and A.status_active = 1";//and a.transfer_criteria in (1)
	//echo $issue_sql;//die;
	$issue_info=sql_select($issue_sql);
	foreach($issue_info as $row)
	{
		$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];

		$mother_barcode_no = $mother_barcode_arr[$row['BARCODE_NO']];
		if($mother_barcode_no != "")
		{
			$fabrication = $prodBarcodeData[$mother_barcode_no]["febric_description_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["gsm"] . "*" . $prodBarcodeData[$mother_barcode_no]["width"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_range_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["stitch_length"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_lot"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_count"] . "*" . $prodBarcodeData[$mother_barcode_no]["brand_id"]."*".$prodBarcodeData[$mother_barcode_no]["machine_dia"];
		}else{
			$mother_barcode_no = $row['BARCODE_NO'];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));
		if($row['ENTRY_FORM']==61)
		{
			$all_issue[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$mother_barcode_no]["color_id"]] += $row['QNTY'];
		}
		
		if($transaction_date < $date_frm)
		{
			if($row['ENTRY_FORM']==61){
				$openingIssueArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$mother_barcode_no]["color_id"]]["issue"] += $row['QNTY'];
				$PoWiseData[$row['PO_BREAKDOWN_ID']]["opening_issue"] += $row['QNTY'];
			}else{
				$openingTransOutArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$mother_barcode_no]["color_id"]]["trans_out"] += $row['QNTY'];
				$PoWiseData[$row['PO_BREAKDOWN_ID']]["opening_trans_out"] += $row['QNTY'];
			}
		}else{
			if($row['ENTRY_FORM']==61){
				$issue_arr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$mother_barcode_no]["color_id"]]["issue"] += $row['QNTY'];
				$PoWiseData[$row['PO_BREAKDOWN_ID']]["issue"] += $row['QNTY'];
			}else{
				$trans_out_arr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$mother_barcode_no]["color_id"]]["trans_out"] += $row['QNTY'];
				$PoWiseData[$row['PO_BREAKDOWN_ID']]["trans_out"] += $row['QNTY'];
			}
		}

		$noOfRollIssueArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$mother_barcode_no]["color_id"]] .= $row['BARCODE_NO'].",";
	}
	
	//echo "<pre>$test_datas2";
	/*print_r($issue_arr);
	echo "</pre>";*/
	unset($issue_info);
	$iss_rtn_qty_sql=sql_select("SELECT C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE, SUM(C.QNTY) QNTY
		from pro_roll_details C, pro_grey_prod_entry_dtls B, inv_transaction E, tmp_poid F
		where C.entry_form=84 and C.status_active=1 and C.is_deleted=0 and C.dtls_id=B.id and B.trans_id=E.id and B.status_active=1 and B.is_deleted=0 and E.status_active=1 and E.is_deleted=0 and E.transaction_type=4 $store_cond and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2
		group by C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE");

	foreach($iss_rtn_qty_sql as $row)
	{
		$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));
		if($transaction_date < $date_frm)
		{
			$openingReturnArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]]["issue_return"] += $row['QNTY'];
			$PoWiseData[$row['PO_BREAKDOWN_ID']]["opening_issue_return"] += $row['QNTY'];
		}else{
			$issue_return_arr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]]["issue_return"] += $row['QNTY'];
			$PoWiseData[$row['PO_BREAKDOWN_ID']]["issue_return"] += $row['QNTY'];
		}
	}
	unset($iss_rtn_qty_sql);
	$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type in(1,2)");
	if($r_id3)
	{
		oci_commit($con);
	}
	//print_r($issue_return_arr);die;
	$width = 3600;
	ob_start();
	?>
	<style>
		.word-break { word-break: break-all; }
		.rpt_table tbody tr td { height:auto !important; padding:3px 0; }
	</style>
	<?
	if ($rpt_type == 1)
	{
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th colspan="16" width="1610">Fabric Details</th>
						<th colspan="3" width="300">Used Yarn Details</th>
						<th rowspan="2" width="100">Req. Qty.</th>
						<th rowspan="2" width="120">Opening</th>
						<th colspan="5" width="400">Receive Details</th>
						<!--<th rowspan="2" width="80">Pre Issue</th>-->
						<th colspan="5" width="400">Issue Details</th>
						<th colspan="4" width="450">Stock Details</th>
					</tr>
					<tr>
						<th width="110">Job No.</th>
						<th width="100">Buyer</th>
						<th width="110">Order No.</th>
						<th width="80">File No</th>
						<th width="100">IR/IB</th>
						<th width="140">Style Ref</th>
						<th width="110">Booking No.</th>
						<th width="110">Constraction</th>
						<th width="120">Composition</th>
						<th width="80">GSM</th>
						<th width="80">F/Dia</th>
						<th width="80">M/Dia</th>
						<th width="100">Stich Length</th>
						<th width="100">Dyeing Color</th>
						<th width="100">Color Range</th>
						<th width="100">Color Type</th>

						<th width="100">Y. Count</th>
						<th width="100">Y. Brand</th>
						<th width="100">Y. Lot</th>

						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">Recv. Roll</th>

						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">Issue Roll</th>

						<th width="100">Stock Qty.</th>
						<th width="150">Store Name</th>
						<th width="80">Roll Qty.</th>
						<th>Recv. Balance</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;">
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<?

					if(!empty($dataArr))
					{
						$i=1;
						$total_receive=$total_issue_return=$total_trans_in=$all_recv_trans_total=$total_issue=$total_trans_out=$all_issue_trans_total=$total_stock=$total_recv_balance=$total_issue_balance=$total_req_qnty=$total_no_of_roll=$total_opening=0;
						foreach ($dataArr as $po_id=>$po_row)
						{
							foreach($po_row as $febric_description_id => $febric_data)
							{
								foreach ($febric_data as $color_id=>$color_data)
								{
									$color_wise_req_qnty =$color_wise_receive =$color_wise_issue_return=$color_wise_trans_in=$color_wiseall_recv_trans_total=$color_wise_opening=$color_wise_issue=$color_wise_trans_out=$color_wiseall_issue_trans_total=$color_wise_stock=$color_wise_recv_balance=$color_wise_issue_balance=
									$color_wise_no_of_roll=0;

									foreach ($color_data as $febric_description=>$row)
									{

										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
										//$fabrication = explode("*", $febric_description);
										$fabrication = explode("*",$row["febric_descriptions"]);
										$fabrication_id = $fabrication[0];

										$yarn_counts_arr = explode(",", $fabrication[6]);

										$yarn_counts="";
										foreach ($yarn_counts_arr as $count) {
											$yarn_counts .= $count_arr[$count] . ",";
										}
										$yarn_counts = rtrim($yarn_counts, ", ");

										$job_no 		= $booking_array[$po_id]['job_no'];
										$booking_no 	= chop($booking_array[$po_id]['booking_no'],",");
										$po_number 		= $booking_array[$po_id]['po_number'];
										$style_ref_no 	= $booking_array[$po_id]['style_ref_no'];
										$buyer_name 	= $booking_array[$po_id]['buyer_name'];
										$grouping 	    = $booking_array[$po_id]['grouping'];
										$file_no 	    = $booking_array[$po_id]['file_no'];

										$key = $buyer_name.$job_no.$po_id.$constuction_arr[$fabrication[0]].$color_id;
										$required_qnty = number_format($grey_qnty_array[$key],2,".","");

										//$fabri_desc = $fabrication[0] . "*" . $fabrication[1] . "*" . $fabrication[2] . "*" . $fabrication[3] . "*" . $fabrication[6] . "*" . $fabrication[7]. "*" . $fabrication[8];
										$fabri_desc =$febric_description;

										$store_ids = array_unique(explode(",",rtrim($storeArr[$po_id][$fabri_desc][$color_id][0],", ")));
										$store_name = "";
										foreach ($store_ids as $store) {
											$store_name .= $storeNameArr[$store].",";
										}
										$store_name = rtrim($store_name,", ");

										$barcode_nos = implode(",",array_unique(explode(",",rtrim($noOfRollArr[$po_id][$fabri_desc][$color_id],", "))));
										$no_of_roll = count(array_unique(explode(",",rtrim($noOfRollArr[$po_id][$fabri_desc][$color_id],", "))));
										$booking_ids = implode(",",array_unique(explode(",",rtrim($bookingIdArr[$po_id][$fabri_desc][$color_id],", "))));
										$no_of_roll_issue = count(array_unique(explode(",",rtrim($noOfRollIssueArr[$po_id][$fabri_desc][$color_id],", "))));

										$opening_receive_qnty 	= number_format($openingReceiveArr[$po_id][$fabri_desc][$color_id],2,".","");
										$opening_return_qnty 	= number_format($openingReturnArr[$po_id][$fabri_desc][$color_id]["issue_return"],2,".","");
										$opening_trans_in_qnty 	= number_format($openingTransInArr[$po_id][$fabri_desc][$color_id],2,".","");

										$opening_issue_qnty 	= number_format($openingIssueArr[$po_id][$fabri_desc][$color_id]["issue"],2,".","");
										$opening_trans_out_qnty = number_format($openingTransOutArr[$po_id][$fabri_desc][$color_id]["trans_out"],2,".","");

										$opening = ($opening_receive_qnty+$opening_return_qnty+$opening_trans_in_qnty)-($opening_issue_qnty+$opening_trans_out_qnty);
										$opening_title = "Receive=$opening_receive_qnty \nIssue Return = $opening_return_qnty \nTrans In = $opening_trans_in_qnty \nIssue = $opening_issue_qnty \nTrans Out = $opening_trans_out_qnty";

										$receive_qnty 		= number_format($row["receive"],2,".","");
										$trans_in_qnty 		= number_format($row["in"],2,".","");
										$issue_return_qnty	= number_format($issue_return_arr[$po_id][$fabri_desc][$color_id]["issue_return"],2,".","");

										$issue_qnty    		= number_format($issue_arr[$po_id][$fabri_desc][$color_id]["issue"],2,".","");
										$trans_out    		= number_format($trans_out_arr[$po_id][$fabri_desc][$color_id]["trans_out"],2,".","");


										$all_receive_qnty	= number_format($receive_qnty + $trans_in_qnty + $issue_return_qnty,2,".","");
										$all_issue_qnty		= number_format($issue_qnty + $trans_out,2,".","");
										$stock_qnty 		= number_format(($opening + $all_receive_qnty) - $all_issue_qnty,2,".","");

										$recv_balance 		= number_format($required_qnty - $all_receive_qnty,2,".","");

										$data_all=$po_id."__".$constuction_arr[$fabrication[0]]."__".$fabrication[3]."__".$fabrication[6]."__".$composition_arr[$fabrication[0]]."__".$fabrication[7]."__".$fabrication[5]."__".$fabrication[4]."__".$fabrication[1]."__".$stock_qnty."__".$color_id."__".$barcode_nos."__".$booking_ids."__".$fabrication_id."__".$cbo_store_name;
										//echo $fabrication_id;die;
										//po,con,range,count,comp,brand,lot,stitch,gsm,stock
										$data_all_tin=$data_all."_1_".$trans_in_qnty."_".$fabrication[8];
										$data_all_tout=$data_all."_2_".$trans_out."_".$fabrication[8];
										$data_all_stock=$data_all."__0__".$stock_qnty."__".$fabrication[2]."__".$fabrication[8];

										$color_name="";
										$color_ids = explode(",", $color_id);
										foreach ($color_ids as $color) {
											$color_name .= $colorArr[$color] . ", ";
										}

										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i;?></td>
											<td width="110" style="word-break:break-all"><? echo $job_no;?></td>
											<td width="100" style="word-break:break-all"><? echo $buyer_array[$buyer_name];?></td>
											<td width="110" title="Order IDs = <? echo $po_id;?>" style="word-break:break-all"><? echo $po_number;?></td>
											<td width="80" style="word-break:break-all"><? echo $file_no;?></td>
											<td width="100" style="word-break:break-all"><? echo $grouping;?></td>
											<td width="140" style="word-break:break-all"><? echo $style_ref_no;?></td>
											<td width="110" style="word-break:break-all"><? echo $booking_no;?></td>
											<td width="110" title="<? echo $febric_description; ?>" style="word-break:break-all"><? echo  $constuction_arr[$fabrication[0]];?></td>
											<td width="120" style="word-break:break-all"><? echo $composition_arr[$fabrication[0]];?></td>
											<td width="80" title="GSM" style="word-break:break-all"><? echo $fabrication[1];?></td>
											<td width="80" title="F/Dia" style="word-break:break-all"><? echo $fabrication[2];?></td>
											<td width="80" title="M/Dia" style="word-break:break-all"><? echo $fabrication[8];?></td>
											<td width="100" style="word-break:break-all" title="Stich Length"><? echo $fabrication[4];?></td>
											<td width="100" style="word-break:break-all" title="Dyeing Color = <? echo $color_id ;?>"><? echo trim($color_name,", ");?></td>
											<td width="100" style="word-break:break-all" ><? echo $color_range[$fabrication[3]];?></td>
											<td width="100" title="Color Type"></td>
											<td width="100" style="word-break:break-all" ><? echo $yarn_counts;?></td>
											<td width="100" style="word-break:break-all" ><? echo $brand_arr[$fabrication[7]];?></td>
											<td width="100" style="word-break:break-all" title="Yarn Lot"><? echo $fabrication[5];?></td>
											<td width="100" title="Req. Qty." align="right"><? echo $required_qnty; ?></td>
											<td width="120" align="right" title="<? echo $febric_description."\n".$opening_title;?>"><? echo $opening; ?></td>
											<td width="80" title="Recv. Qty." align="right"><a href="##" onClick="openpage('recv_popup','<? echo $data_all; ?>','750')"><? echo $receive_qnty; ?></a></td>
											<td width="80" align="right" title="Issue Ret. Qty."><? echo $issue_return_qnty; ?></td>
											<td width="80" align="right"><a href="##" onClick="openpage('transfer_popup','<? echo $data_all_tin; ?>','750')"><? echo $trans_in_qnty; ?></a></td>
											<td width="80" align="right" title="Total Recv."><? echo $all_receive_qnty; ?></td>
											<td width="80" align="right" title="<? echo $barcode_nos;?>"><? echo $no_of_roll; ?></td>
											<td width="80" align="right" title="Issue Qty."><a href="##" onClick="openpage('iss_popup','<? echo $data_all; ?>','650')"><? echo $issue_qnty; ?></a></td>
											<td width="80" align="right" ></td>
											<td width="80" align="right"><a href="##" onClick="openpage('transfer_popup','<? echo $data_all_tout; ?>','750')"><? echo $trans_out; ?></a></td>
											<td width="80" align="right" ><? echo $all_issue_qnty; ?></td>
											<td width="80" align="right" ></td>

											<td width="100" align="right" title="Stock Qty. <? echo $data_all_stock; ?>"><a href="##" onClick="openpage('stock_popup','<? echo $data_all_stock; ?>','700')"><? echo $stock_qnty; ?></a></td>
											<td width="150" title="<? echo $fabri_desc; ?>"><? echo $store_name; ?><br /><? //print_r($store_ids);?></td>
											<td width="80" align="right" title="<? echo $barcode_nos;?>"><? echo $no_of_roll_issue; ?></td>
											<td align="right" title="Recv. Balance (<? echo $required_qnty .'-'. $total_receive;?>)"><? echo $recv_balance; ?></td>
										</tr>
										<?
										$total_req_qnty 		+= $required_qnty;
										$total_receive 			+= $receive_qnty;
										$total_issue_return 	+= $issue_return_qnty;
										$total_trans_in 		+= $trans_in_qnty;
										$all_recv_trans_total 	+= $all_receive_qnty;

										$total_opening			+= $opening;

										$total_issue 			+= $issue_qnty;
										$total_trans_out 		+= $trans_out;
										$all_issue_trans_total 	+= $all_issue_qnty;

										$total_stock 			+= $stock_qnty;
										$total_recv_balance		+= $recv_balance;
										$total_issue_balance 	+= $issue_balance;

										$total_no_of_roll		+= $no_of_roll;
										$i++;
										$color_wise_req_qnty 		+= $required_qnty;
										$color_wise_receive 			+= $receive_qnty;
										$color_wise_issue_return 	+= $issue_return_qnty;
										$color_wise_trans_in 		+= $trans_in_qnty;
										$color_wiseall_recv_trans_total 	+= $all_receive_qnty;

										$color_wise_opening			+= $opening;

										$color_wise_issue 			+= $issue_qnty;
										$color_wise_trans_out 		+= $trans_out;
										$color_wiseall_issue_trans_total 	+= $all_issue_qnty;

										$color_wise_stock 			+= $stock_qnty;
										$color_wise_recv_balance		+= $recv_balance;
										$color_wise_issue_balance 	+= $issue_balance;

										$color_wise_no_of_roll		+= $no_of_roll;

									}

									?>
									<tr bgcolor="#ACC9FO">
										<td width="30"></td>
                                        <td width="110" title="Job No"></td>
                                        <td width="100" title="Buyer"></td>
                                        <td width="110" title="Order No"></td>
                                        <td width="80" title="File No"></td>
                                        <td width="100" title="IR"></td>
                                        <td width="140" title="Style Ref"></td>
                                        <td width="110" title="Booking No"></td>
                                        <td width="110" title="Constraction"></td>
                                        <td width="120" title="Composition"></td>
                                        <td width="80" title="GSM"></td>
                                        <td width="80" title="F/Dia"></td>
                                        <td width="80" title="M/Dia"></td>
                                        <td width="100" title="Stich Length"></td>
                                        <td width="100" title="Dyeing Color"></td>
                                        <td width="100" class="word-break" title="Color Range"></td>
                                        <td width="100" title="Color Type"></td>

                                        <td width="100" class="word-break" title="Count"></td>
                                        <td width="100" class="word-break" title="Y. Brand"></td>
                                        <td width="100" class="word-break" title="Yarn Lot"><strong>Color Total=</strong></td>

                                        <td width="100" title="Req. Qty." align="right" class="skip_tdw"><? echo number_format($color_wise_req_qnty,2,".",""); ?></td>
                                        <td width="120" align="right" title="Pre Rcv" ><? echo number_format($color_wise_opening,2,".",""); ?></td>

                                        <td width="80" title="Recv. Qty." align="right"><? echo number_format($color_wise_receive,2,".",""); ?></td>
                                        <td width="80" align="right" title="Issue Ret. Qty."><? echo number_format($color_wise_issue_return,2,".",""); ?></td>
                                        <td width="80" align="right" title="Transfer In"><? echo number_format($color_wise_trans_in,2,".",""); ?></td>
                                        <td width="80" align="right" title="Total Recv."><? echo number_format($color_wise_recv_trans_total,2,".",""); ?></td>
                                        <td width="80" align="right" title="Recv. Roll"></td>
                                        <td width="80" align="right" title="Issue Qty."><? echo number_format($color_wise_issue,2,".",""); ?></td>
                                        <td width="80" align="right" title="Recv. Ret. Qty."></td>
                                        <td width="80" align="right" title="Transf. Out Qty."><? echo number_format($color_wise_trans_out,2,".",""); ?></td>
                                        <td width="80" align="right" title="Total Issue"><? echo number_format($color_wise_issue_trans_total,2,".",""); ?></td>
                                        <td width="80" align="right" title="Issue Roll"></td>

                                        <td width="100" align="right" title="Stock Qty."><? echo number_format($color_wise_stock,2,".",""); ?></td>
                                        <td width="150" title="Store Name"></td>
                                        <td width="80" align="right" title="Roll Qty."><? echo $color_wise_no_of_roll;?></td>
                                        <td align="right" titleRecv. Balance><? echo number_format($color_wise_recv_balance,2,".",""); ?></td>
                                    </tr>
                                    <?
								}
							}
						}
					}
					?>
				</table>
				</div>
				<table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                        <tfoot>
                            <th width="30"></th>
                            <th width="110" title="Job No"></th>
                            <th width="100" title="Buyer"></th>
                            <th width="110" title="Order No"></th>
                            <th width="80" title="File No"></th>
                            <th width="100" title="IR"></th>
                            <th width="140" title="Style Ref"></th>
                            <th width="110" title="Booking No"></th>
                            <th width="110" title="Constraction"></th>
                            <th width="120" title="Composition"></th>
                            <th width="80" title="GSM"></th>
                            <th width="80" title="F/Dia"></th>
                            <th width="80" title="M/Dia"></th>
                            <th width="100" title="Stich Length"></th>
                            <th width="100" title="Dyeing Color"></th>
                            <th width="100" class="word-break" title="Color Range"></th>
                            <th width="100" title="Color Type"></th>

                            <th width="100" class="word-break" title="Count"></th>
                            <th width="100" class="word-break" title="Y. Brand"></th>
                            <th width="100" class="word-break" title="Yarn Lot"></th>

                            <th id="value_td_total_req_qnty" width="100" title="Req. Qty." align="right"><? echo number_format($total_req_qnty,2,".",""); ?></th>
                            <th id="value_total_opening_td" width="120" align="right" title="Pre Rcv"><? echo number_format($total_opening,2,".",""); ?></th>

                            <th width="80" title="Recv. Qty." align="right"><? echo number_format($total_receive,2,".",""); ?></th>
                            <th width="80" align="right" title="Issue Ret. Qty."><? echo number_format($total_issue_return,2,".",""); ?></th>
                            <th width="80" align="right" title="Transfer In"><? echo number_format($total_trans_in,2,".",""); ?></th>
                            <th width="80" align="right" title="Total Recv."><? echo number_format($all_recv_trans_total,2,".",""); ?></th>
                            <th width="80" align="right" title="Recv. Roll"></th>



                            <th width="80" align="right" title="Issue Qty."><? echo number_format($total_issue,2,".",""); ?></th>
                            <th width="80" align="right" title="Recv. Ret. Qty."></th>
                            <th width="80" align="right" title="Transf. Out Qty."><? echo number_format($total_trans_out,2,".",""); ?></th>
                            <th width="80" align="right" title="Total Issue"><? echo number_format($all_issue_trans_total,2,".",""); ?></th>
                            <th width="80" align="right" title="Issue Roll"></th>

                            <th width="100" align="right" title="Stock Qty."><? echo number_format($total_stock,2,".",""); ?></th>
                            <th width="150" title="Store Name"></th>
                            <th width="80" align="right" title="Roll Qty."><? echo $total_no_of_roll;?></th>
                            <th align="right" titleRecv. Balance><? echo number_format($total_recv_balance,2,".",""); ?></th>
                        </tfoot>
                </table>
        </fieldset>

        <?
    }
	else
	{
		foreach ($PoWiseData as $po_id => $row)
		{
			$ref_buyer = $booking_array[$po_id]['buyer_name'];
			$buyerWiseData[$ref_buyer]["opening_rcv"] += $row["opening_rcv"];
			$buyerWiseData[$ref_buyer]["opening_issue"] += $row["opening_issue"];
			$buyerWiseData[$ref_buyer]["opening_trans_out"] += $row["opening_trans_out"];
			$buyerWiseData[$ref_buyer]["opening_trans_in"] += $row["opening_trans_in"];
			$buyerWiseData[$ref_buyer]["opening_issue_return"] += $row["opening_issue_return"];

			$buyerWiseData[$ref_buyer]["receive"] += $row["receive"];
			$buyerWiseData[$ref_buyer]["trans_in"] += $row["trans_in"];
			$buyerWiseData[$ref_buyer]["trans_out"] += $row["trans_out"];
			$buyerWiseData[$ref_buyer]["issue"] += $row["issue"];
			$buyerWiseData[$ref_buyer]["issue_return"] += $row["issue_return"];
		}


		?>
		<fieldset style="width:1200px;margin:5px auto;">

			<table width="1150" cellspacing="0" cellpadding="0" border="0" rules="all" >
				<tr class="form_caption">
					<td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
				</tr>
				<tr class="form_caption">
					<td colspan="12" align="center"><? echo $company_arr[$cbo_company_id]; ?></td>
				</tr>
				<tr class="form_caption">
					<td colspan="12" align="center">Buyer Wise Summary</td>
				</tr>
			</table>

			<table width="1200" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="120">Buyer</th>
						<th rowspan="2" width="100">Opening</th>
						<th colspan="4">Receive Details</th>
						<th colspan="4">Issue Details</th>
						<th rowspan="2" width="100">Stock Details</th>
					</tr>
					<tr>
						<th width="100">Recv. Qty.</th>
						<th width="100">Issue Ret. Qty.</th>
						<th width="100">Transf. In Qty.</th>
						<th width="100">Total Recv.</th>
						<th width="100">Issue Qty.</th>
						<th width="100">Recv. Ret. Qty.</th>
						<th width="100">Transf. Out Qty.</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:1220px; overflow-y: scroll; max-height:380px;">
				<table width="1200" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<tbody>
						<?
						$i++;
						foreach ($buyerWiseData as $buyer_id => $row)
						{
							$opening_rcv = $row["opening_rcv"];
							$opening_issue = $row["opening_issue"];
							$opening_trans_out = $row["opening_trans_out"];
							$opening_trans_in = $row["opening_trans_in"];
							$opening_issue_return = $row["opening_issue_return"];
							$opening = ($opening_rcv + $opening_trans_in + $opening_issue_return) - ($opening_issue + $opening_trans_out);

							$total_receive = $row["receive"]+ $row["issue_return"] +$row["trans_in"];
							$total_issue = $row["issue"]+ $row["trans_out"];
							$stock = ($opening + $total_receive) - $total_issue;

							$grand_opening += $opening;
							$grand_receive += $row["receive"];
							$grand_issue_return += $row["issue_return"];
							$grand_trans_in += $row["trans_in"];
							$grand_total_receive += $total_receive;
							$grand_issue += $row["issue"];
							$grand_trans_out += $row["trans_out"];
							$grand_total_issue += $total_issue;
							$grand_stock += $stock;
							?>
							<tr>
								<td align="center" width="30"><? echo $i;?></td>
								<td align="center" width="120"><? echo $buyer_array[$buyer_id]; ?></td>
								<td align="right" width="100"><? echo number_format($opening,2);?></td>
								<td align="right" width="100"><? echo number_format($row["receive"],2);?></td>
								<td align="right" width="100"><? echo number_format($row["issue_return"],2);?></td>
								<td align="right" width="100"><? echo number_format($row["trans_in"],2);?></td>
								<td align="right" width="100"><? echo number_format($total_receive,2);?></td>
								<td align="right" width="100"><? echo number_format($row["issue"],2);?></td>
								<td width="100"></td>
								<td align="right" width="100"><? echo number_format($row["trans_out"],2);?></td>
								<td align="right" width="100"><? echo number_format($total_issue,2);?></td>
								<td align="right" width="100"><? echo number_format($stock,2);?></td>
							</tr>
							<?
							$i++;
						}

						?>
					</tbody>
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="100"><? echo number_format($grand_opening,2); ?></th>
							<th width="100"><? echo number_format($grand_receive,2); ?></th>
							<th width="100"><? echo number_format($grand_issue_return,2); ?></th>
							<th width="100"><? echo number_format($grand_trans_in,2); ?></th>
							<th width="100"><? echo number_format($grand_total_receive,2); ?></th>
							<th width="100"><? echo number_format($grand_issue,2); ?></th>
							<th width="100">&nbsp;</th>
							<th width="100"><? echo number_format($grand_trans_out,2); ?></th>
							<th width="100"><? echo number_format($grand_total_issue,2); ?></th>
							<th width="100"><? echo number_format($grand_stock,2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

	echo "<br />Execution Time: " . (microtime(true) - $started) . "S";

	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$rpt_type";
	exit();
}


if($action=="report_generate_summery_bk")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_buyer_id 		= str_replace("'","",$cbo_buyer_id);
	$txt_order_no 		= str_replace("'","",$txt_order_no);
	$hdn_color 			= str_replace("'","",$hdn_color);
	$txt_color 			= str_replace("'","",$txt_color);
	$cbo_year 			= str_replace("'","",$cbo_year);
	$booking_no 		= str_replace("'","",$txt_booking_no);
	$job_no 			= str_replace("'","",$txt_job_no);
	$date_from			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$rpt_type 			= str_replace("'","",$rpt_type);
	$cbo_trans_year		= str_replace("'","",$cbo_trans_year);
	
	
	//echo $cbo_store_name;die;

	$_SESSION["date_from"]=date('Y-m-d',strtotime($date_from));
	$_SESSION["date_to"]=date('Y-m-d',strtotime($date_to));

	$company_arr 		= return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_array 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
	if($job_no!='') $job_no_cond="and d.job_no_prefix_num in($job_no)";else $job_no_cond="";
	if($txt_order_no!='') $po_no_cond="and c.po_number='$txt_order_no'";else $po_no_cond="";
	if($hdn_color!='') $color_cond="and b.fabric_color_id in($hdn_color)";else $color_cond="";
	if($hdn_color!='') $color_cond2=" and b.color_id in('$hdn_color')";else $color_cond2="";
	//echo $cbo_year;die;
	$year_field_cond="";
	if($cbo_year !="")
	{
		$cbo_year_ref=explode(",",$cbo_year);
		$year_id_string="";
		foreach($cbo_year_ref as $year_id)
		{
			$year_id_string.="'".$year_id."',";
		}
		$year_id_string=chop($year_id_string,",");
		//echo $year_id_string;die;
		
		if($job_no!='')
		{
			if($db_type==0)
			{
				$year_field_cond="and YEAR(C.insert_date) in($cbo_year)";
			}
			else
			{
				$year_field_cond=" and to_char(C.insert_date,'YYYY') in($year_id_string)";
			}
		}
		else
		{
			if($db_type==0) 
			{
				$year_field_cond="and YEAR(a.insert_date) in($cbo_year)";
			}
			else 
			{
				$year_field_cond=" and to_char(a.insert_date,'YYYY') in($year_id_string)";
			}
		}
	}
	
	/*if($job_no!='')
	{
		if($cbo_year !="")
		{
			if($db_type==0) $year_field_cond="and YEAR(C.insert_date)=$cbo_year";
			else if($db_type==2) $year_field_cond=" and to_char(C.insert_date,'YYYY')=$cbo_year";
		}
	}
	else
	{
		if($cbo_year>0)
		{
			if($db_type==0) $year_field_cond="and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}*/
	
	if($cbo_trans_year>0)
	{
		if($db_type==0) $trans_year_field_cond="and YEAR(a.insert_date)=$cbo_trans_year";
		else if($db_type==2) $trans_year_field_cond=" and to_char(a.insert_date,'YYYY')=$cbo_trans_year";
	}
	

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond=" and e.transaction_date <= '$end_date'";
		$date_cond2=" and a.transfer_date <= '$end_date'";
	}

	if($cbo_store_name!="")
	{
		$store_cond  = " and e.store_id in($cbo_store_name)";
		$store_cond1  = " and a.store_id in($cbo_store_name)";
		$store_cond2 = " and b.to_store in($cbo_store_name)";
		$store_cond3 = " and b.from_store in($cbo_store_name)";
	}
	
	$po_ids_array=array();
	if($job_no_cond!="" || $book_no_cond!="" || $po_no_cond !="" || $color_cond!="" || $year_field_cond!="" || $cbo_buyer_id > 0)
	{
		$buyer_cond="";
		if($cbo_buyer_id>0) $buyer_cond=" and A.BUYER_ID=$cbo_buyer_id";
		$sql="select B.PO_BREAK_DOWN_ID AS PO_ID, e.BARCODE_NO 
		from wo_booking_mst A, wo_booking_dtls B, wo_po_break_down C, wo_po_details_master D, pro_roll_details e 
		where A.booking_no=B.booking_no and B.po_break_down_id=C.id and C.job_no_mst=D.job_no and c.id=e.po_breakdown_id and A.company_id=$cbo_company_id and A.item_category in(2,13) and A.is_deleted=0 and A.status_active=1  and B.is_deleted=0 and B.status_active=1  $job_no_cond $book_no_cond $po_no_cond $color_cond $year_field_cond  $buyer_cond
		group by  B.PO_BREAK_DOWN_ID, e.BARCODE_NO";
		//echo $sql;die;
		$sql_result=sql_select($sql);
		$po_ids='';
		
		foreach( $sql_result as $row )
		{
			if($po_ids=='') $po_ids=$row['PO_ID'];else $po_ids.=",".$row['PO_ID'];
			if($row['PO_ID']!="" && $po_ids_array[$row['PO_ID']]=="")
			{
				$po_ids_array[$row['PO_ID']]=$row['PO_ID'];
				$poId = $row['PO_ID'];
				$rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$poId,2)");
			}
			if($barcode_check_array[$row['BARCODE_NO']]=="")
			{
				$barcode_check_array[$row['BARCODE_NO']]==$row['BARCODE_NO'];
				$barcode_nos = $row['BARCODE_NO'];
				$rID3=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$barcode_nos,1)");
			}
		}

		if($rID2 && $rID3)
		{
			oci_commit($con);
		}
		
		/*
		$po_ids = array_unique(explode(",",$po_ids));
		$poIds_cond_roll="";
		$po_idss = implode(",", $po_ids);
		if($db_type==2 && count($po_ids)>999)
		{
			$po_chunk=array_chunk($po_ids,999) ;
			$poIds_cond_roll = " and (";

			foreach($po_chunk as $chunk_arr)
			{
				$poIds_cond_roll.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			}

			$poIds_cond_roll = chop($poIds_cond_roll,"or ");
			$poIds_cond_roll .=")";
		}
		else
		{
			$poIds_cond_roll  = " and c.po_breakdown_id in($po_idss)";
		}*/
		
		unset($sql_result);
	}
	
	//echo count($po_ids_array);die;
	
	//echo $poIds_cond_roll;die;
	if(count($po_ids_array)>0)
	{
		$main_query="SELECT A.ID, A.ENTRY_FORM, A.RECEIVE_BASIS, A.BOOKING_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.COLOR_RANGE_ID, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.PROD_ID, NULL AS FROM_TRANS_ID, NULL AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 1 AS TYPE, E.STORE_ID, E.TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C, tmp_poid F
		where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(2,22,58,84) and B.trans_id<>0 and C.entry_form in(2,22,58,84) and C.status_active=1 and C.is_deleted=0 $trans_date $date_cond $store_cond $color_cond2 and A.status_active=1 and A.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY,D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID,D.GSM GSM,D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE,B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.from_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(83) and C.entry_form in(83) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID, B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(82) and C.entry_form in(82) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE AS TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from  inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(183) and C.entry_form in(183) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order=0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		order by BARCODE_NO ASC, ROLL_ID DESC";
	}
	else
	{
		$main_query="SELECT A.ID, A.ENTRY_FORM, A.RECEIVE_BASIS, A.BOOKING_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY,B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.COLOR_RANGE_ID, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.PROD_ID, NULL AS FROM_TRANS_ID, NULL AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 1 AS TYPE, E.STORE_ID, E.TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C
		where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and A.company_id=$cbo_company_id and A.entry_form in(2,22,58,84) and B.trans_id<>0 and C.entry_form in(2,22,58,84) and C.status_active=1 and C.is_deleted=0 $trans_date $date_cond $store_cond $color_cond2 and A.status_active=1 and A.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY,D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID,D.GSM GSM,D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE,B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.from_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(83) and C.entry_form in(83) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID, B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(82) and C.entry_form in(82) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE AS TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from  inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(183) and C.entry_form in(183) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order=0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		order by BARCODE_NO ASC, ROLL_ID DESC";
	}
	//echo count($po_ids_array);die;
	//echo $main_query;die();
	//==================================
	$result=sql_select( $main_query );
	//echo count($result);die;
	if(count($result)==0)
	{
		echo "No Data Found";die;
	}
	$store_arr=array();
	if(count($po_ids_array)==0)
	{
		foreach($result as $row)
		{
			if($row["PO_BREAKDOWN_ID"]!="" && $po_id_check[$row["PO_BREAKDOWN_ID"]]=="")
			{
				$po_id_check[$row["PO_BREAKDOWN_ID"]]=$row["PO_BREAKDOWN_ID"];
				$poId = $row["PO_BREAKDOWN_ID"];
				$rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$poId,2)");
			}
			if($barcode_check_array[$row['BARCODE_NO']]=="")
			{
				$barcode_check_array[$row['BARCODE_NO']]==$row['BARCODE_NO'];
				$barcode_nos = $row['BARCODE_NO'];
				$rID3=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$barcode_nos,1)");
			}
		}
		if($rID2 && $rID3)
		{
			oci_commit($con);
		}
	}
	
	
	$production_sql = "SELECT B.BARCODE_NO, A.COLOR_RANGE_ID, A.YARN_LOT, A.YARN_COUNT, A.BRAND_ID, B.PO_BREAKDOWN_ID, A.PROD_ID, B.BOOKING_NO, B.RECEIVE_BASIS, A.COLOR_ID, A.FEBRIC_DESCRIPTION_ID, A.GSM, A.WIDTH, A.STITCH_LENGTH, A.MACHINE_DIA, A.MACHINE_GG, A.MACHINE_NO_ID, C.KNITTING_SOURCE, C.CHALLAN_NO AS PRODUCTION_CHALLAN, C.KNITTING_COMPANY, A.YARN_PROD_ID, A.BODY_PART_ID 
	from inv_receive_master C, pro_grey_prod_entry_dtls A, pro_roll_details B, tmp_poid F
	where C.id=A.mst_id and A.id=B.dtls_id and C.entry_form=2 and B.entry_form in(2) and A.status_active=1 and B.status_active=1 and B.BARCODE_NO=F.poid and F.userid=$user_id and F.type=1";
	//echo $production_sql;die;
	$production_info = sql_select($production_sql);
	foreach ($production_info as $row)
	{
		$prodBarcodeData[$row['BARCODE_NO']]["prod_basis"] 			= $row['RECEIVE_BASIS'];
		$prodBarcodeData[$row['BARCODE_NO']]["prog_book"] 				= $row['BOOKING_NO'];
		$prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] 		= $row['COLOR_RANGE_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] 				= $row['YARN_LOT'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] 			= $row['YARN_COUNT'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_prod_id"] 			= $row['YARN_PROD_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["prod_id"] 				= $row['PROD_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["color_id"] 				= $row['COLOR_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] 	= $row['FEBRIC_DESCRIPTION_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["gsm"] 					= $row['GSM'];
		$prodBarcodeData[$row['BARCODE_NO']]["width"] 					= $row['WIDTH'];
		$prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] 			= $row['STITCH_LENGTH'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"] 			= $row['MACHINE_DIA'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_gg"] 			= $row['MACHINE_GG'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_no_id"] 			= $row['MACHINE_NO_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["prod_challan"] 			= $row['PRODUCTION_CHALLAN'];
		$prodBarcodeData[$row['BARCODE_NO']]["knitting_source"] 		= $row['KNITTING_SOURCE'];
		$prodBarcodeData[$row['BARCODE_NO']]["knitting_company"] 		= $row['KNITTING_COMPANY'];
		$prodBarcodeData[$row['BARCODE_NO']]["body_part_id"] 			= $row['BODY_PART_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["brand_id"] 				= $row['BRAND_ID'];
		$allDeterArr[$row['FEBRIC_DESCRIPTION_ID']] 					= $row['FEBRIC_DESCRIPTION_ID'];
		$allColorArr[$row['COLOR_ID']] 								= $row['COLOR_ID'];
		$allYarnProdArr[$row['YARN_PROD_ID']] 							= $row['YARN_PROD_ID'];
		
		$febric_description_arr[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
	}
	unset($production_info);
	//echo "<pre>";print_r($febric_description_arr);die;
	foreach($result as $row)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));

		$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];
		if($dup_barcode_rcv[$row['BARCODE_NO']]=="")
		{
			$dup_barcode_rcv[$row['BARCODE_NO']]=$row['BARCODE_NO'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["tot_receive"] += $row['QNTY'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["tot_receive_barcode"].=$row['BARCODE_NO'].",";
			if($transaction_date < $date_frm)
			{
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["opening_rcv"] += $row['QNTY'];
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["receive"] += 0;
			}
			else
			{
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["opening_rcv"] += 0;
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["receive"] += $row['QNTY'];
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["receive_barcode"].=$row['BARCODE_NO'].",";
			}
		}
		$store_id_arr[$row['STORE_ID']]=$row['STORE_ID'];
	}
	//echo "<pre>";print_r($dataArr);die;
	unset($result);
	
	$color_count = 1;
	$_SESSION["date_frm"]=date('Y-m-d',strtotime($start_date));
	
	$po_sql="SELECT B.JOB_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, SUM(B.GREY_FAB_QNTY) AS GREY_REQ_QNTY, B.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO, B.IS_SHORT, D.BUYER_NAME as BUYER_ID 
	from wo_booking_dtls B, wo_po_break_down C, wo_po_details_master D, tmp_poid E  
	where D.COMPANY_NAME=$cbo_company_id and B.BOOKING_TYPE=1 and B.is_deleted=0 and B.status_active=1 and B.po_break_down_id=C.id and C.job_no_mst=D.job_no and C.id=E.poid and E.userid=$user_id and E.type=2
	group by B.JOB_NO, B.PO_BREAK_DOWN_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, B.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO, b.IS_SHORT, D.BUYER_NAME
	order by B.PO_BREAK_DOWN_ID asc, B.IS_SHORT desc";
	//echo $po_sql;die;

	$po_sql_result=sql_select($po_sql);
	//echo "<pre>";print_r($febric_description_arr);die;
	$po_ids='';
	foreach( $po_sql_result as $row )
	{
		//$key=$row['BUYER_ID'].$row['JOB_NO'].$row[csf('po_id')].$row['CONSTRUCTION'].$row['FABRIC_COLOR_ID'];
		$key=$row['PO_ID'].$row['FABRIC_COLOR_ID'].$row['CONSTRUCTION'];
		$grey_qnty_array[$key] += $row['GREY_REQ_QNTY'];

		$booking_array[$row['PO_ID']]['job_no'] 		= $row['JOB_NO'];
		$booking_array[$row['PO_ID']]['po_number'] 		= $row['PO_NUMBER'];
		$booking_array[$row['PO_ID']]['style_ref_no'] 	= $row['STYLE_REF_NO'];
		$booking_array[$row['PO_ID']]['buyer_name'] 	= $row['BUYER_NAME'];
		if($booking_po_check[$row['PO_ID']][$row['BOOKING_NO']]=="")
		{
			$booking_po_check[$row['PO_ID']][$row['BOOKING_NO']]=$row['BOOKING_NO'];
			if($row['IS_SHORT']==1) $booking_nos=$row['BOOKING_NO']."[S]"; else $booking_nos=$row['BOOKING_NO'];
			$booking_array[$row['PO_ID']]['booking_no'] .= $booking_nos.",";
		}
	}
	unset($po_sql_result);

	/*echo "<pre>";
	print_r($grey_qnty_array);
	echo "</pre>";
	die;*/
	//echo "<pre>";print_r($febric_description_arr);die;
	if(!empty($store_id_arr)){
		$storeNameArr=return_library_array( "select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id and a.id in(".implode(",",$store_id_arr).")", "id", "store_name" );
	}
	//$color_ids = rtrim(implode(",",$all_color_arr),", ");
	
	$colorArr=return_library_array( "select id, color_name from lib_color", "id", "color_name" );
	$febric_description_arr = array_filter($febric_description_arr);
	
	if(!empty($febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($febric_description_arr,999);
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}

		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}
	//and c.barcode_no in(SELECT poid from tmp_poid where userid=$user_id and type=1)
	$split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	from pro_roll_split C, pro_roll_details D, tmp_poid E 
	where C.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and D.PO_BREAKDOWN_ID=E.poid and E.userid=$user_id and E.type=2");

	if(!empty($split_chk_sql))
	{
		foreach ($split_chk_sql as $val)
		{
			$split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
		}

		$split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE from pro_roll_details A, pro_roll_details B where A.barcode_no in (".implode(",", $split_barcode_arr).") and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
		if(!empty($split_ref_sql))
		{
			foreach ($split_ref_sql as $value)
			{
				$mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
			}
		}
	}
	unset($split_chk_sql);
	
	$issue_sql = "SELECT C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM
	from pro_roll_details C, inv_grey_fabric_issue_dtls B, inv_transaction E, tmp_poid F
	where C.entry_form=61 and C.status_active=1 and C.is_deleted=0 and C.IS_RETURNED=0 $date_cond $store_cond $trans_year_field_cond		
	and C.booking_without_order = 0 and C.dtls_id=B.id and B.trans_id=E.id and E.transaction_type=2 and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2";//and a.transfer_criteria in (1)
	//echo $issue_sql;die;
	$issue_info=sql_select($issue_sql);
	foreach($issue_info as $row)
	{
		$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];

		$mother_barcode_no = $mother_barcode_arr[$row['BARCODE_NO']];
		if($mother_barcode_no != "")
		{
			$fabrication = $prodBarcodeData[$mother_barcode_no]["febric_description_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["gsm"] . "*" . $prodBarcodeData[$mother_barcode_no]["width"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_range_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["stitch_length"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_lot"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_count"] . "*" . $prodBarcodeData[$mother_barcode_no]["brand_id"]."*".$prodBarcodeData[$mother_barcode_no]["machine_dia"];
		}else{
			$mother_barcode_no = $row['BARCODE_NO'];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));
		if($dup_barcode_issue[$row['BARCODE_NO']]=="")
		{
			$dup_barcode_issue[$row['BARCODE_NO']]=$row['BARCODE_NO'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["tot_issue"] += $row['QNTY'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["tot_issue_barcode"].=$row['BARCODE_NO'].",";
			if($transaction_date < $date_frm)
			{
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["opening_issue"] += $row['QNTY'];
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["issue"] += 0;
			}
			else
			{
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["opening_issue"] += 0;
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["issue"] += $row['QNTY'];
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["issue_barcode"].=$row['BARCODE_NO'].",";
			}
		}
	}
	unset($issue_info);
	//echo "<pre>$test_datas2";
	/*print_r($issue_arr);
	echo "</pre>";*/
	//echo "<pre>";print_r($dataArr);die;
	
	unset($iss_rtn_qty_sql);
	$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type in(1,2)");
	if($r_id3)
	{
		oci_commit($con);
	}
	//print_r($issue_return_arr);die;
	$width = 2500;
	ob_start();
	?>
	<style>
		.word-break { word-break: break-all; }
		.rpt_table tbody tr td { height:auto !important; padding:3px 0; }
	</style>
    <fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
    	<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="12" align="center"><? echo $company_arr[$cbo_company_id]; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="12" align="center">Date From: <? echo change_date_format($date_from); ?> To : <? echo change_date_format($date_to); ?></td>
            </tr>
        </table>
        <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <tr>
                    <th rowspan="2" width="30">SL</th>
                    <th colspan="14" width="1430">Fabric Details</th>
                    <th colspan="3" width="320">Used Yarn Details</th>
                    <th rowspan="2" width="100">Req. Qty.</th>
                    <th rowspan="2" width="100">Opening Stock</th>
                    <th rowspan="2" width="100">Receive(Date Range)</th>
                    <th rowspan="2" width="100">Issue(Date Range)</th>
                    <th rowspan="2" width="100">Total Receive</th>
                    <th rowspan="2" width="100">Total Issue</th>
                    <th rowspan="2">Closing Stock</th>
                </tr>
                <tr>
                    <th width="110">Job No.</th>
                    <th width="100">Buyer</th>
                    <th width="110">Order No.</th>
                    <th width="140">Style Ref</th>
                    <th width="110">Booking No.</th>
                    <th width="110">Constraction</th>
                    <th width="120">Composition</th>
                    <th width="80">GSM</th>
                    <th width="80">F/Dia</th>
                    <th width="80">M/Dia</th>
                    <th width="100">Stich Length</th>
                    <th width="100">Dyeing Color</th>
                    <th width="100">Color Range</th>
                    <th width="100">Color Type</th>

                    <th width="100">Y. Count</th>
                    <th width="120">Y. Brand</th>
                    <th width="100">Y. Lot</th>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <?
                $i=1;
				$total_receive=$total_issue_return=$total_trans_in=$all_recv_trans_total=$total_issue=$total_trans_out=$all_issue_trans_total=$total_stock=$total_recv_balance=$total_issue_balance=$total_req_qnty=$total_no_of_roll=$total_opening=0;
				foreach ($dataArr as $po_id=>$po_row)
				{
					foreach ($po_row as $color_id=>$color_data)
					{
						foreach ($color_data as $febric_description=>$row)
						{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							//$fabrication = explode("*", $febric_description);
							$fabrication = explode("*",$febric_description);
							$fabrication_id = $fabrication[0];

							$yarn_counts_arr = explode(",", $fabrication[6]);

							$yarn_counts="";
							foreach ($yarn_counts_arr as $count) {
								$yarn_counts .= $count_arr[$count] . ",";
							}
							$yarn_counts = rtrim($yarn_counts, ", ");

							$job_no 		= $booking_array[$po_id]['job_no'];
							$booking_no 	= chop($booking_array[$po_id]['booking_no'],",");
							$po_number 		= $booking_array[$po_id]['po_number'];
							$style_ref_no 	= $booking_array[$po_id]['style_ref_no'];
							$buyer_name 	= $booking_array[$po_id]['buyer_name'];

							$key = $po_id.$color_id.$constuction_arr[$fabrication[0]];
							$required_qnty = number_format($grey_qnty_array[$key],2,".","");
							$fabri_desc =$febric_description;
							
							//######## dev later
							//$store_ids = array_unique(explode(",",rtrim($storeArr[$po_id][$fabri_desc][$color_id][0],", ")));
							//$store_name = "";
							//foreach ($store_ids as $store) {
								//$store_name .= $storeNameArr[$store].",";
							//}
							//$store_name = rtrim($store_name,", ");
							//$barcode_nos = implode(",",array_unique(explode(",",rtrim($noOfRollArr[$po_id][$fabri_desc][$color_id],", "))));
							//$no_of_roll = count(array_unique(explode(",",rtrim($noOfRollArr[$po_id][$fabri_desc][$color_id],", "))));
							//$booking_ids = implode(",",array_unique(explode(",",rtrim($bookingIdArr[$po_id][$fabri_desc][$color_id],", "))));
							//$no_of_roll_issue = count(array_unique(explode(",",rtrim($noOfRollIssueArr[$po_id][$fabri_desc][$color_id],", "))));
							//$data_all=$po_id."__".$constuction_arr[$fabrication[0]]."__".$fabrication[3]."__".$fabrication[6]."__".$composition_arr[$fabrication[0]]."__".$fabrication[7]."__".$fabrication[5]."__".$fabrication[4]."__".$fabrication[1]."__".$stock_qnty."__".$color_id."__".$barcode_nos."__".$booking_ids."__".$fabrication_id."__".$cbo_store_name;
							
							//######## dev later
							
							$color_name="";
							$color_ids = explode(",", $color_id);
							foreach ($color_ids as $color) {
								$color_name .= $colorArr[$color] . ", ";
							}

							$opening = $row["opening_rcv"]-$row["opening_issue"];
							$runtime_stock=$row["receive"]-$row["issue"];
							$closing_stock=$opening+$runtime_stock;
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30" align="center" title=""><? echo $i;?></td>
								<td width="110" title="Job No" style="word-break:break-all"><? echo $job_no;?></td>
								<td width="100" title="Buyer" style="word-break:break-all"><? echo $buyer_array[$buyer_name];?></td>
								<td width="110" title="Order IDs = <? echo $po_id;?>" style="word-break:break-all"><? echo $po_number;?></td>
								<td width="140" title="Style Ref" style="word-break:break-all"><? echo $style_ref_no;?></td>
								<td width="110" title="Booking No" style="word-break:break-all"><? echo $booking_no;?></td>
								<td width="110" title="<? echo $febric_description; ?>" style="word-break:break-all"><? echo $constuction_arr[$fabrication[0]];?></td>
								<td width="120" title="Composition" style="word-break:break-all"><? echo $composition_arr[$fabrication[0]];?></td>
								<td width="80" title="GSM" style="word-break:break-all"><? echo $fabrication[1];?></td>
								<td width="80" title="F/Dia" style="word-break:break-all"><? echo $fabrication[2];?></td>
								<td width="80" title="M/Dia" style="word-break:break-all"><? echo $fabrication[8];?></td>
								<td width="100" style="word-break:break-all" title="Stich Length"><? echo $fabrication[4];?></td>
								<td width="100" style="word-break:break-all" title="Dyeing Color = <? echo $color_id ;?>"><? echo trim($color_name,", ");?></td>
								<td width="100" style="word-break:break-all" title="Color Range"><? echo $color_range[$fabrication[3]];?></td>
								<td width="100" title="Color Type"></td>
								<td width="100" style="word-break:break-all" title="Count"><? echo $yarn_counts;?></td>
								<td width="120" style="word-break:break-all" title="Y. Brand"><? echo $brand_arr[$fabrication[7]];?></td>
								<td width="100" style="word-break:break-all" title="Yarn Lot"><? echo $fabrication[5];?></td>
                                <?
								if($req_qnty_check[$key]=="")
								{
									$req_qnty_check[$key]=$key;
									$total_req_qnty+=$required_qnty;
									?>
                                    <td width="100" title="<? echo $key; ?>" align="right"><? echo $required_qnty; ?></td>
                                    <?
								}
								else
								{
									?>
                                    <td width="100" title="<? echo $key; ?>" align="right"><span style="color:<? echo $bgcolor;?>">'</span><? echo $required_qnty; ?></td>
                                    <?
								}
                                ?>
								
								<td width="100" align="right" title=""><? echo number_format($opening,2); ?></td>
								<td width="100" align="right" title="<?=  chop($row["receive_barcode"],",")."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo chop($row["receive_barcode"],","); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','1','800')"><? echo number_format($row["receive"],2); ?></a></td>
								<td width="100" align="right" title="<?=  chop($row["issue_barcode"],",")."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo chop($row["issue_barcode"],","); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','2','800')"><? echo number_format($row["issue"],2); ?></a></td>
                                <td width="100" align="right" title="<?=  chop($row["tot_receive_barcode"],",")."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo chop($row["tot_receive_barcode"],","); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','3','800')"><? echo number_format($row["tot_receive"],2); ?></a></td>
								<td width="100" align="right" title="<?=  chop($row["tot_issue_barcode"],",")."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo chop($row["tot_issue_barcode"],","); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','4','800')"><? echo number_format($row["tot_issue"],2); ?></a></td>
                                <?
								$stock_barcode_arr=array_diff(explode(",",chop($row["tot_receive_barcode"],",")),explode(",",chop($row["tot_issue_barcode"],",")));
								?>
								<td align="right" title="<?=  implode(",",$stock_barcode_arr)."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo implode(",",$stock_barcode_arr); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','5','800')"><? echo number_format($closing_stock,2); ?></a></td>
							</tr>
							<?
							$i++;
							
							$total_opening+=$opening;
							$total_receive+=$row["receive"];
							$total_issue+=$row["issue"];
							$total_tot_receive+=$row["tot_receive"];
							$total_tot_issue+=$row["tot_issue"];
							$total_closing_stock+=$closing_stock;
						}
					}
				}
                ?>
            </table>
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body_footer">
                    <tfoot>
                        <th width="30"></th>
                        <th width="110" title="Job No"></th>
                        <th width="100" title="Buyer"></th>
                        <th width="110" title="Order No"></th>
                        <th width="140" title="Style Ref"></th>
                        <th width="110" title="Booking No"></th>
                        <th width="110" title="Constraction"></th>
                        <th width="120" title="Composition"></th>
                        <th width="80" title="GSM"></th>
                        <th width="80" title="F/Dia"></th>
                        <th width="80" title="M/Dia"></th>
                        <th width="100" title="Stich Length"></th>
                        <th width="100" title="Dyeing Color"></th>
                        <th width="100" class="word-break" title="Color Range"></th>
                        <th width="100" title="Color Type"></th>
                        <th width="100" class="word-break" title="Count"></th>
                        <th width="120" class="word-break" title="Y. Brand"></th>
                        <th width="100" class="word-break" title="Yarn Lot"></th>

                        <th width="100" align="right" id="value_total_req_qnty"><? echo number_format($total_req_qnty,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_opening"><? echo number_format($total_opening,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_receive"><? echo number_format($total_receive,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_issue"><? echo number_format($total_issue,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_tot_receive"><? echo number_format($total_tot_receive,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_tot_issue"><? echo number_format($total_tot_issue,2,".",""); ?></th>
                        <th align="right"  id="value_total_closing_stock"><? echo number_format($total_closing_stock,2,".",""); ?></th>
                    </tfoot>
            </table>
    </fieldset>
    <?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "S";

	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$rpt_type";
	exit();
}

if($action=="report_generate_summery")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();

	$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type in(1,2)");
	if($r_id3)
	{
		oci_commit($con);
	}

	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_buyer_id 		= str_replace("'","",$cbo_buyer_id);
	$txt_order_no 		= str_replace("'","",$txt_order_no);
	$hdn_color 			= str_replace("'","",$hdn_color);
	$txt_color 			= str_replace("'","",$txt_color);
	$cbo_year 			= str_replace("'","",$cbo_year);
	$booking_no 		= str_replace("'","",$txt_booking_no);
	$job_no 			= str_replace("'","",$txt_job_no);
	$date_from			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$rpt_type 			= str_replace("'","",$rpt_type);
	$cbo_trans_year		= str_replace("'","",$cbo_trans_year);
	
	
	//echo $cbo_store_name;die;

	$_SESSION["date_from"]=date('Y-m-d',strtotime($date_from));
	$_SESSION["date_to"]=date('Y-m-d',strtotime($date_to));

	$company_arr 		= return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_array 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
	if($job_no!='') $job_no_cond="and d.job_no_prefix_num in($job_no)";else $job_no_cond="";
	if($txt_order_no!='') $po_no_cond="and c.po_number='$txt_order_no'";else $po_no_cond="";
	if($hdn_color!='') $color_cond="and b.fabric_color_id in($hdn_color)";else $color_cond="";
	if($hdn_color!='') $color_cond2=" and b.color_id in('$hdn_color')";else $color_cond2="";
	//echo $cbo_year;die;
	$year_field_cond="";
	if($cbo_year !="")
	{
		$cbo_year_ref=explode(",",$cbo_year);
		$year_id_string="";
		foreach($cbo_year_ref as $year_id)
		{
			$year_id_string.="'".$year_id."',";
		}
		$year_id_string=chop($year_id_string,",");
		//echo $year_id_string;die;
		
		if($job_no!='')
		{
			if($db_type==0)
			{
				$year_field_cond="and YEAR(C.insert_date) in($cbo_year)";
			}
			else
			{
				$year_field_cond=" and to_char(C.insert_date,'YYYY') in($year_id_string)";
			}
		}
		else
		{
			if($db_type==0) 
			{
				$year_field_cond="and YEAR(a.insert_date) in($cbo_year)";
			}
			else 
			{
				$year_field_cond=" and to_char(a.insert_date,'YYYY') in($year_id_string)";
			}
		}
	}
	
	/*if($job_no!='')
	{
		if($cbo_year !="")
		{
			if($db_type==0) $year_field_cond="and YEAR(C.insert_date)=$cbo_year";
			else if($db_type==2) $year_field_cond=" and to_char(C.insert_date,'YYYY')=$cbo_year";
		}
	}
	else
	{
		if($cbo_year>0)
		{
			if($db_type==0) $year_field_cond="and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}*/
	
	if($cbo_trans_year>0)
	{
		if($db_type==0) $trans_year_field_cond="and YEAR(a.insert_date)=$cbo_trans_year";
		else if($db_type==2) $trans_year_field_cond=" and to_char(a.insert_date,'YYYY')=$cbo_trans_year";
	}
	

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond=" and e.transaction_date <= '$end_date'";
		$date_cond2=" and a.transfer_date <= '$end_date'";
	}

	if($cbo_store_name!="")
	{
		$store_cond  = " and e.store_id in($cbo_store_name)";
		$store_cond1  = " and a.store_id in($cbo_store_name)";
		$store_cond2 = " and b.to_store in($cbo_store_name)";
		$store_cond3 = " and b.from_store in($cbo_store_name)";
	}
	
	$po_ids_array=array();
	if($job_no_cond!="" || $book_no_cond!="" || $po_no_cond !="" || $color_cond!="" || $year_field_cond!="" || $cbo_buyer_id > 0)
	{
		//and e.RE_TRANSFER=0
		$buyer_cond="";
		if($cbo_buyer_id>0) $buyer_cond=" and A.BUYER_ID=$cbo_buyer_id";
		$sql="select B.PO_BREAK_DOWN_ID AS PO_ID, e.BARCODE_NO 
		from wo_booking_mst A, wo_booking_dtls B, wo_po_break_down C, wo_po_details_master D, pro_roll_details e 
		where A.booking_no=B.booking_no and B.po_break_down_id=C.id and C.job_no_mst=D.job_no and c.id=e.po_breakdown_id and A.company_id=$cbo_company_id and A.item_category in(2,13) and A.is_deleted=0 and A.status_active=1 and B.is_deleted=0 and B.status_active=1  $job_no_cond $book_no_cond $po_no_cond $color_cond $year_field_cond $buyer_cond
		group by B.PO_BREAK_DOWN_ID, e.BARCODE_NO";
		//echo $sql;die;
		$sql_result=sql_select($sql);
		$po_ids='';
		
		foreach( $sql_result as $row )
		{
			if($po_ids=='') $po_ids=$row['PO_ID'];else $po_ids.=",".$row['PO_ID'];
			if($row['PO_ID']!="" && $po_ids_array[$row['PO_ID']]=="")
			{
				$po_ids_array[$row['PO_ID']]=$row['PO_ID'];
				$poId = $row['PO_ID'];
				$rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$poId,2)");
			}
			if($barcode_check_array[$row['BARCODE_NO']]=="")
			{
				$barcode_check_array[$row['BARCODE_NO']]==$row['BARCODE_NO'];
				$barcode_nos = $row['BARCODE_NO'];
				$rID3=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$barcode_nos,1)");
			}
		}

		if($rID2 && $rID3)
		{
			oci_commit($con);
		}
		
		/*
		$po_ids = array_unique(explode(",",$po_ids));
		$poIds_cond_roll="";
		$po_idss = implode(",", $po_ids);
		if($db_type==2 && count($po_ids)>999)
		{
			$po_chunk=array_chunk($po_ids,999) ;
			$poIds_cond_roll = " and (";

			foreach($po_chunk as $chunk_arr)
			{
				$poIds_cond_roll.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			}

			$poIds_cond_roll = chop($poIds_cond_roll,"or ");
			$poIds_cond_roll .=")";
		}
		else
		{
			$poIds_cond_roll  = " and c.po_breakdown_id in($po_idss)";
		}*/
		
		unset($sql_result);
	}
	
	//echo count($po_ids_array);die;
	
	//echo $poIds_cond_roll;die;  
	if(count($po_ids_array)>0)
	{
		$main_query="SELECT A.ID, A.ENTRY_FORM, A.RECEIVE_BASIS, A.BOOKING_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.COLOR_RANGE_ID, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.PROD_ID, NULL AS FROM_TRANS_ID, NULL AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 1 AS TYPE, E.STORE_ID, E.TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C, tmp_poid F
		where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(2,22,58,84) and B.trans_id<>0 and C.entry_form in(2,22,58,84) and C.status_active=1 and C.is_deleted=0 $trans_date $date_cond $store_cond $color_cond2 and A.status_active=1 and A.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY,D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID,D.GSM GSM,D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE,B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.from_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(83) and C.entry_form in(83) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID, B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.to_company=$cbo_company_id and A.entry_form in(82) and C.entry_form in(82) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE AS TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from  inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.to_company=$cbo_company_id and A.entry_form in(183) and C.entry_form in(183) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order=0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		order by BARCODE_NO ASC, ROLL_ID DESC";
	}
	else
	{
		$main_query="SELECT A.ID, A.ENTRY_FORM, A.RECEIVE_BASIS, A.BOOKING_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY,B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.COLOR_RANGE_ID, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.PROD_ID, NULL AS FROM_TRANS_ID, NULL AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 1 AS TYPE, E.STORE_ID, E.TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C
		where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and A.company_id=$cbo_company_id and A.entry_form in(2,22,58,84) and B.trans_id<>0 and C.entry_form in(2,22,58,84) and C.status_active=1 and C.is_deleted=0 $trans_date $date_cond $store_cond $color_cond2 and A.status_active=1 and A.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY,D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID,D.GSM GSM,D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE,B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.from_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(83) and C.entry_form in(83) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID, B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.to_company=$cbo_company_id and A.entry_form in(82) and C.entry_form in(82) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE AS TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from  inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.to_company=$cbo_company_id and A.entry_form in(183) and C.entry_form in(183) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order=0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		order by BARCODE_NO ASC, ROLL_ID DESC";
	}

	//echo $main_query;

	//echo count($po_ids_array);die;
	//echo $main_query;die();
	//==================================
	$result=sql_select( $main_query );
	//echo count($result);die;
	if(count($result)==0)
	{
		echo "No Data Found";die;
	}
	$store_arr=array();
	if(count($po_ids_array)==0)
	{
		foreach($result as $row)
		{
			if($row["PO_BREAKDOWN_ID"]!="" && $po_id_check[$row["PO_BREAKDOWN_ID"]]=="")
			{
				$po_id_check[$row["PO_BREAKDOWN_ID"]]=$row["PO_BREAKDOWN_ID"];
				$poId = $row["PO_BREAKDOWN_ID"];
				$rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$poId,2)");
			}
			if($barcode_check_array[$row['BARCODE_NO']]=="")
			{
				$barcode_check_array[$row['BARCODE_NO']]==$row['BARCODE_NO'];
				$barcode_nos = $row['BARCODE_NO'];
				$rID3=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$barcode_nos,1)");
			}
		}
		if($rID2 && $rID3)
		{
			oci_commit($con);
		}
	}
	
	
	$production_sql = "SELECT B.BARCODE_NO, A.COLOR_RANGE_ID, A.YARN_LOT, A.YARN_COUNT, A.BRAND_ID, B.PO_BREAKDOWN_ID, A.PROD_ID, B.BOOKING_NO, B.RECEIVE_BASIS, A.COLOR_ID, A.FEBRIC_DESCRIPTION_ID, A.GSM, A.WIDTH, A.STITCH_LENGTH, A.MACHINE_DIA, A.MACHINE_GG, A.MACHINE_NO_ID, C.KNITTING_SOURCE, C.CHALLAN_NO AS PRODUCTION_CHALLAN, C.KNITTING_COMPANY, A.YARN_PROD_ID, A.BODY_PART_ID 
	from inv_receive_master C, pro_grey_prod_entry_dtls A, pro_roll_details B, tmp_poid F
	where C.id=A.mst_id and A.id=B.dtls_id and C.entry_form=2 and B.entry_form in(2) and A.status_active=1 and B.status_active=1 and B.BARCODE_NO=F.poid and F.userid=$user_id and F.type=1";
	//echo $production_sql;die;
	$production_info = sql_select($production_sql);
	foreach ($production_info as $row)
	{
		$prodBarcodeData[$row['BARCODE_NO']]["prod_basis"] 			= $row['RECEIVE_BASIS'];
		$prodBarcodeData[$row['BARCODE_NO']]["prog_book"] 				= $row['BOOKING_NO'];
		$prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] 		= $row['COLOR_RANGE_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] 				= $row['YARN_LOT'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] 			= $row['YARN_COUNT'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_prod_id"] 			= $row['YARN_PROD_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["prod_id"] 				= $row['PROD_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["color_id"] 				= $row['COLOR_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] 	= $row['FEBRIC_DESCRIPTION_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["gsm"] 					= $row['GSM'];
		$prodBarcodeData[$row['BARCODE_NO']]["width"] 					= $row['WIDTH'];
		$prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] 			= $row['STITCH_LENGTH'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"] 			= $row['MACHINE_DIA'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_gg"] 			= $row['MACHINE_GG'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_no_id"] 			= $row['MACHINE_NO_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["prod_challan"] 			= $row['PRODUCTION_CHALLAN'];
		$prodBarcodeData[$row['BARCODE_NO']]["knitting_source"] 		= $row['KNITTING_SOURCE'];
		$prodBarcodeData[$row['BARCODE_NO']]["knitting_company"] 		= $row['KNITTING_COMPANY'];
		$prodBarcodeData[$row['BARCODE_NO']]["body_part_id"] 			= $row['BODY_PART_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["brand_id"] 				= $row['BRAND_ID'];
		$allDeterArr[$row['FEBRIC_DESCRIPTION_ID']] 					= $row['FEBRIC_DESCRIPTION_ID'];
		$allColorArr[$row['COLOR_ID']] 								= $row['COLOR_ID'];
		$allYarnProdArr[$row['YARN_PROD_ID']] 							= $row['YARN_PROD_ID'];
		
		$febric_description_arr[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
	}
	unset($production_info);
	//echo "<pre>";print_r($febric_description_arr);die;
	foreach($result as $row)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));

		$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];
		if($dup_barcode_rcv[$row['BARCODE_NO']]=="")
		{
			$dup_barcode_rcv[$row['BARCODE_NO']]=$row['BARCODE_NO'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["tot_receive"] += $row['QNTY'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["tot_receive_barcode"].=$row['BARCODE_NO'].",";
			if($transaction_date < $date_frm)
			{
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["opening_rcv"] += $row['QNTY'];
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["receive"] += 0;
			}
			else
			{
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["opening_rcv"] += 0;
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["receive"] += $row['QNTY'];
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$fabrication]["receive_barcode"].=$row['BARCODE_NO'].",";
			}
		}
		$store_id_arr[$row['STORE_ID']]=$row['STORE_ID'];
	}
	//echo "<pre>";print_r($dataArr);die;
	unset($result);
	
	$color_count = 1;
	$_SESSION["date_frm"]=date('Y-m-d',strtotime($start_date));
	
	$po_sql="SELECT B.JOB_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, SUM(B.GREY_FAB_QNTY) AS GREY_REQ_QNTY, B.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO, B.IS_SHORT, D.BUYER_NAME as BUYER_ID 
	from wo_booking_dtls B, wo_po_break_down C, wo_po_details_master D, tmp_poid E  
	where D.COMPANY_NAME=$cbo_company_id and B.BOOKING_TYPE=1 and B.is_deleted=0 and B.status_active=1 and B.po_break_down_id=C.id and C.job_no_mst=D.job_no and C.id=E.poid and E.userid=$user_id and E.type=2
	group by B.JOB_NO, B.PO_BREAK_DOWN_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, B.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO, b.IS_SHORT, D.BUYER_NAME
	order by B.PO_BREAK_DOWN_ID asc, B.IS_SHORT desc";
	//echo $po_sql;die;

	$po_sql_result=sql_select($po_sql);
	//echo "<pre>";print_r($febric_description_arr);die;
	$po_ids='';
	foreach( $po_sql_result as $row )
	{
		//$key=$row['BUYER_ID'].$row['JOB_NO'].$row[csf('po_id')].$row['CONSTRUCTION'].$row['FABRIC_COLOR_ID'];
		$key=$row['PO_ID'].$row['FABRIC_COLOR_ID'].$row['CONSTRUCTION'];
		$grey_qnty_array[$key] += $row['GREY_REQ_QNTY'];

		$booking_array[$row['PO_ID']]['job_no'] 		= $row['JOB_NO'];
		$booking_array[$row['PO_ID']]['po_number'] 		= $row['PO_NUMBER'];
		$booking_array[$row['PO_ID']]['style_ref_no'] 	= $row['STYLE_REF_NO'];
		$booking_array[$row['PO_ID']]['buyer_name'] 	= $row['BUYER_NAME'];
		if($booking_po_check[$row['PO_ID']][$row['BOOKING_NO']]=="")
		{
			$booking_po_check[$row['PO_ID']][$row['BOOKING_NO']]=$row['BOOKING_NO'];
			if($row['IS_SHORT']==1) $booking_nos=$row['BOOKING_NO']."[S]"; else $booking_nos=$row['BOOKING_NO'];
			$booking_array[$row['PO_ID']]['booking_no'] .= $booking_nos.",";
		}
	}
	unset($po_sql_result);

	/*echo "<pre>";
	print_r($grey_qnty_array);
	echo "</pre>";
	die;*/
	//echo "<pre>";print_r($febric_description_arr);die;
	if(!empty($store_id_arr)){
		$storeNameArr=return_library_array( "select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) and a.company_id=$cbo_company_id and a.id in(".implode(",",$store_id_arr).")", "id", "store_name" );
	}
	//$color_ids = rtrim(implode(",",$all_color_arr),", ");
	
	$colorArr=return_library_array( "select id, color_name from lib_color", "id", "color_name" );
	$febric_description_arr = array_filter($febric_description_arr);
	
	if(!empty($febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($febric_description_arr,999);
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}

		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}
	//and c.barcode_no in(SELECT poid from tmp_poid where userid=$user_id and type=1)
	$split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	from pro_roll_split C, pro_roll_details D, tmp_poid E 
	where C.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and D.PO_BREAKDOWN_ID=E.poid and E.userid=$user_id and E.type=2");

	if(!empty($split_chk_sql))
	{
		foreach ($split_chk_sql as $val)
		{
			$split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
			if($val["BARCODE_NO"]!="" && $barcode_no_check[$val["BARCODE_NO"]]=="")
			{
				$barcode_no_check[$val["BARCODE_NO"]]=$val["BARCODE_NO"];
				$barcode = $val["BARCODE_NO"];
				$rID3=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$barcode,3)");
			}
		}
		if($rID3)
		{
			oci_commit($con);
		}

		/*$split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE from pro_roll_details A, pro_roll_details B where A.barcode_no in (".implode(",", $split_barcode_arr).") and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");*/
		$split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE from pro_roll_details A, pro_roll_details B, tmp_poid c where A.barcode_no=c.poid and c.userid=$user_id and c.type=3 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
		if(!empty($split_ref_sql))
		{
			foreach ($split_ref_sql as $value)
			{
				$mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
			}
		}
	}
	unset($split_chk_sql);
	
	$issue_sql = "SELECT C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM
	from pro_roll_details C, inv_grey_fabric_issue_dtls B, inv_transaction E, tmp_poid F
	where C.entry_form=61 and C.status_active=1 and C.is_deleted=0 and C.IS_RETURNED=0 $date_cond $store_cond $trans_year_field_cond		
	and C.booking_without_order = 0 and C.dtls_id=B.id and B.trans_id=E.id and E.transaction_type=2 and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2";//and a.transfer_criteria in (1)
	//echo $issue_sql;die;
	$issue_info=sql_select($issue_sql);
	foreach($issue_info as $row)
	{
		$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];

		$mother_barcode_no = $mother_barcode_arr[$row['BARCODE_NO']];
		if($mother_barcode_no != "")
		{
			$fabrication = $prodBarcodeData[$mother_barcode_no]["febric_description_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["gsm"] . "*" . $prodBarcodeData[$mother_barcode_no]["width"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_range_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["stitch_length"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_lot"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_count"] . "*" . $prodBarcodeData[$mother_barcode_no]["brand_id"]."*".$prodBarcodeData[$mother_barcode_no]["machine_dia"];
		}else{
			$mother_barcode_no = $row['BARCODE_NO'];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));
		if($dup_barcode_issue[$row['BARCODE_NO']]=="")
		{
			$dup_barcode_issue[$row['BARCODE_NO']]=$row['BARCODE_NO'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["tot_issue"] += $row['QNTY'];
			$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["tot_issue_barcode"].=$row['BARCODE_NO'].",";
			if($transaction_date < $date_frm)
			{
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["opening_issue"] += $row['QNTY'];
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["issue"] += 0;
			}
			else
			{
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["opening_issue"] += 0;
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["issue"] += $row['QNTY'];
				$dataArr[$row['PO_BREAKDOWN_ID']][$prodBarcodeData[$mother_barcode_no]["color_id"]][$fabrication]["issue_barcode"].=$row['BARCODE_NO'].",";
			}
		}
	}
	unset($issue_info);
	//echo "<pre>$test_datas2";
	/*print_r($issue_arr);
	echo "</pre>";*/
	//echo "<pre>";print_r($dataArr);die;
	
	unset($iss_rtn_qty_sql);
	

	/*$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type in(1,2)");
	if($r_id3)
	{
		oci_commit($con);
	}*/


	//print_r($issue_return_arr);die;
	$width = 2500;
	ob_start();
	?>
	<style>
		.word-break { word-break: break-all; }
		.rpt_table tbody tr td { height:auto !important; padding:3px 0; }
	</style>
    <fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
    	<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="12" align="center"><? echo $company_arr[$cbo_company_id]; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="12" align="center">Date From: <? echo change_date_format($date_from); ?> To : <? echo change_date_format($date_to); ?></td>
            </tr>
        </table>
        <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <tr>
                    <th rowspan="2" width="30">SL</th>
                    <th colspan="14" width="1430">Fabric Details</th>
                    <th colspan="3" width="320">Used Yarn Details</th>
                    <th rowspan="2" width="100">Req. Qty.</th>
                    <th rowspan="2" width="100">Opening Stock</th>
                    <th rowspan="2" width="100">Receive(Date Range)</th>
                    <th rowspan="2" width="100">Issue(Date Range)</th>
                    <th rowspan="2" width="100">Total Receive</th>
                    <th rowspan="2" width="100">Total Issue</th>
                    <th rowspan="2">Closing Stock</th>
                </tr>
                <tr>
                    <th width="110">Job No.</th>
                    <th width="100">Buyer</th>
                    <th width="110">Order No.</th>
                    <th width="140">Style Ref</th>
                    <th width="110">Booking No.</th>
                    <th width="110">Constraction</th>
                    <th width="120">Composition</th>
                    <th width="80">GSM</th>
                    <th width="80">F/Dia</th>
                    <th width="80">M/Dia</th>
                    <th width="100">Stich Length</th>
                    <th width="100">Dyeing Color</th>
                    <th width="100">Color Range</th>
                    <th width="100">Color Type</th>

                    <th width="100">Y. Count</th>
                    <th width="120">Y. Brand</th>
                    <th width="100">Y. Lot</th>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <?
                $i=1;
				$total_receive=$total_issue_return=$total_trans_in=$all_recv_trans_total=$total_issue=$total_trans_out=$all_issue_trans_total=$total_stock=$total_recv_balance=$total_issue_balance=$total_req_qnty=$total_no_of_roll=$total_opening=0;
				foreach ($dataArr as $po_id=>$po_row)
				{
					foreach ($po_row as $color_id=>$color_data)
					{
						foreach ($color_data as $febric_description=>$row)
						{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							//$fabrication = explode("*", $febric_description);
							$fabrication = explode("*",$febric_description);
							$fabrication_id = $fabrication[0];

							$yarn_counts_arr = explode(",", $fabrication[6]);

							$yarn_counts="";
							foreach ($yarn_counts_arr as $count) {
								$yarn_counts .= $count_arr[$count] . ",";
							}
							$yarn_counts = rtrim($yarn_counts, ", ");

							$job_no 		= $booking_array[$po_id]['job_no'];
							$booking_no 	= chop($booking_array[$po_id]['booking_no'],",");
							$po_number 		= $booking_array[$po_id]['po_number'];
							$style_ref_no 	= $booking_array[$po_id]['style_ref_no'];
							$buyer_name 	= $booking_array[$po_id]['buyer_name'];

							$key = $po_id.$color_id.$constuction_arr[$fabrication[0]];
							$required_qnty = number_format($grey_qnty_array[$key],2,".","");
							$fabri_desc =$febric_description;
							
							//######## dev later
							//$store_ids = array_unique(explode(",",rtrim($storeArr[$po_id][$fabri_desc][$color_id][0],", ")));
							//$store_name = "";
							//foreach ($store_ids as $store) {
								//$store_name .= $storeNameArr[$store].",";
							//}
							//$store_name = rtrim($store_name,", ");
							//$barcode_nos = implode(",",array_unique(explode(",",rtrim($noOfRollArr[$po_id][$fabri_desc][$color_id],", "))));
							//$no_of_roll = count(array_unique(explode(",",rtrim($noOfRollArr[$po_id][$fabri_desc][$color_id],", "))));
							//$booking_ids = implode(",",array_unique(explode(",",rtrim($bookingIdArr[$po_id][$fabri_desc][$color_id],", "))));
							//$no_of_roll_issue = count(array_unique(explode(",",rtrim($noOfRollIssueArr[$po_id][$fabri_desc][$color_id],", "))));
							//$data_all=$po_id."__".$constuction_arr[$fabrication[0]]."__".$fabrication[3]."__".$fabrication[6]."__".$composition_arr[$fabrication[0]]."__".$fabrication[7]."__".$fabrication[5]."__".$fabrication[4]."__".$fabrication[1]."__".$stock_qnty."__".$color_id."__".$barcode_nos."__".$booking_ids."__".$fabrication_id."__".$cbo_store_name;
							
							//######## dev later
							
							$color_name="";
							$color_ids = explode(",", $color_id);
							foreach ($color_ids as $color) {
								$color_name .= $colorArr[$color] . ", ";
							}

							$opening = $row["opening_rcv"]-$row["opening_issue"];
							$runtime_stock=$row["receive"]-$row["issue"];
							$closing_stock=$opening+$runtime_stock;
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30" align="center" title=""><? echo $i;?></td>
								<td width="110" title="Job No" style="word-break:break-all"><? echo $job_no;?></td>
								<td width="100" title="Buyer" style="word-break:break-all"><? echo $buyer_array[$buyer_name];?></td>
								<td width="110" title="Order IDs = <? echo $po_id;?>" style="word-break:break-all"><? echo $po_number;?></td>
								<td width="140" title="Style Ref" style="word-break:break-all"><? echo $style_ref_no;?></td>
								<td width="110" title="Booking No" style="word-break:break-all"><? echo $booking_no;?></td>
								<td width="110" title="<? echo $febric_description; ?>" style="word-break:break-all"><? echo $constuction_arr[$fabrication[0]];?></td>
								<td width="120" title="Composition" style="word-break:break-all"><? echo $composition_arr[$fabrication[0]];?></td>
								<td width="80" title="GSM" style="word-break:break-all"><? echo $fabrication[1];?></td>
								<td width="80" title="F/Dia" style="word-break:break-all"><? echo $fabrication[2];?></td>
								<td width="80" title="M/Dia" style="word-break:break-all"><? echo $fabrication[8];?></td>
								<td width="100" style="word-break:break-all" title="Stich Length"><? echo $fabrication[4];?></td>
								<td width="100" style="word-break:break-all" title="Dyeing Color = <? echo $color_id ;?>"><? echo trim($color_name,", ");?></td>
								<td width="100" style="word-break:break-all" title="Color Range"><? echo $color_range[$fabrication[3]];?></td>
								<td width="100" title="Color Type"></td>
								<td width="100" style="word-break:break-all" title="Count"><? echo $yarn_counts;?></td>
								<td width="120" style="word-break:break-all" title="Y. Brand"><? echo $brand_arr[$fabrication[7]];?></td>
								<td width="100" style="word-break:break-all" title="Yarn Lot"><? echo $fabrication[5];?></td>
                                <?
								if($req_qnty_check[$key]=="")
								{
									$req_qnty_check[$key]=$key;
									$total_req_qnty+=$required_qnty;
									?>
                                    <td width="100" title="<? echo $key; ?>" align="right"><? echo $required_qnty; ?></td>
                                    <?
								}
								else
								{
									?>
                                    <td width="100" title="<? echo $key; ?>" align="right"><span style="color:<? echo $bgcolor;?>">'</span><? echo $required_qnty; ?></td>
                                    <?
								}
                                ?>
								
								<td width="100" align="right" title=""><? echo number_format($opening,2); ?></td>
								<td width="100" align="right" title="<?=  chop($row["receive_barcode"],",")."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo chop($row["receive_barcode"],","); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','1','800')"><? echo number_format($row["receive"],2); ?></a></td>
								<td width="100" align="right" title="<?=  chop($row["issue_barcode"],",")."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo chop($row["issue_barcode"],","); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','2','800')"><? echo number_format($row["issue"],2); ?></a></td>
                                <td width="100" align="right" title="<?=  chop($row["tot_receive_barcode"],",")."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo chop($row["tot_receive_barcode"],","); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','3','800')"><? echo number_format($row["tot_receive"],2); ?></a></td>
								<td width="100" align="right" title="<?=  chop($row["tot_issue_barcode"],",")."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo chop($row["tot_issue_barcode"],","); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','4','800')"><? echo number_format($row["tot_issue"],2); ?></a></td>
                                <?
								$stock_barcode_arr=array_diff(explode(",",chop($row["tot_receive_barcode"],",")),explode(",",chop($row["tot_issue_barcode"],",")));
								?>
								<td align="right" title="<?=  implode(",",$stock_barcode_arr)."=".$po_id;?>"><a href="##" onClick="openpage_details('roll_details_popup','<? echo implode(",",$stock_barcode_arr); ?>',<? echo $po_id; ?>,'<? echo $cbo_store_name; ?>','5','800')"><? echo number_format($closing_stock,2); ?></a></td>
							</tr>
							<?
							$i++;
							
							$total_opening+=$opening;
							$total_receive+=$row["receive"];
							$total_issue+=$row["issue"];
							$total_tot_receive+=$row["tot_receive"];
							$total_tot_issue+=$row["tot_issue"];
							$total_closing_stock+=$closing_stock;
						}
					}
				}
                ?>
            </table>
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body_footer">
                    <tfoot>
                        <th width="30"></th>
                        <th width="110" title="Job No"></th>
                        <th width="100" title="Buyer"></th>
                        <th width="110" title="Order No"></th>
                        <th width="140" title="Style Ref"></th>
                        <th width="110" title="Booking No"></th>
                        <th width="110" title="Constraction"></th>
                        <th width="120" title="Composition"></th>
                        <th width="80" title="GSM"></th>
                        <th width="80" title="F/Dia"></th>
                        <th width="80" title="M/Dia"></th>
                        <th width="100" title="Stich Length"></th>
                        <th width="100" title="Dyeing Color"></th>
                        <th width="100" class="word-break" title="Color Range"></th>
                        <th width="100" title="Color Type"></th>
                        <th width="100" class="word-break" title="Count"></th>
                        <th width="120" class="word-break" title="Y. Brand"></th>
                        <th width="100" class="word-break" title="Yarn Lot"></th>

                        <th width="100" align="right" id="value_total_req_qnty"><? echo number_format($total_req_qnty,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_opening"><? echo number_format($total_opening,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_receive"><? echo number_format($total_receive,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_issue"><? echo number_format($total_issue,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_tot_receive"><? echo number_format($total_tot_receive,2,".",""); ?></th>
                        <th width="100" align="right" id="value_total_tot_issue"><? echo number_format($total_tot_issue,2,".",""); ?></th>
                        <th align="right"  id="value_total_closing_stock"><? echo number_format($total_closing_stock,2,".",""); ?></th>
                    </tfoot>
            </table>
    </fieldset>
    <?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "S";

	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$rpt_type";
	exit();
}

function category_period_ending_date($company_id, $item_cat_id)
{
	return return_field_value("period_ending_date", "lib_item_category_comp_wise", "company_id=$company_id and category_id=$item_cat_id and status_active=1 and is_deleted=0");
}

if($action=="report_generate3")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();

	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_buyer_id 		= str_replace("'","",$cbo_buyer_id);
	$txt_order_no 		= str_replace("'","",$txt_order_no);
	$hdn_color 			= str_replace("'","",$hdn_color);
	$txt_color 			= str_replace("'","",$txt_color);
	$cbo_year 			= str_replace("'","",$cbo_year);
	$booking_no 		= str_replace("'","",$txt_booking_no);
	$job_no 			= str_replace("'","",$txt_job_no);
	$date_from			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$rpt_type 			= str_replace("'","",$rpt_type);
	$cbo_trans_year		= str_replace("'","",$cbo_trans_year);
	
	// this function is added in common_functions
	$last_closing_date = category_period_ending_date(1, 13);

	$company_arr 		= return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_array 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
	if($job_no!='') $job_no_cond="and d.job_no_prefix_num in($job_no)";else $job_no_cond="";
	if($txt_order_no!='') $po_no_cond="and c.po_number='$txt_order_no'";else $po_no_cond="";
	if($hdn_color!='') $color_cond="and b.fabric_color_id in($hdn_color)";else $color_cond="";
	if($hdn_color!='') $color_cond2=" and b.color_id in('$hdn_color')";else $color_cond2="";
	if($db_type==0) $year_field_cond="and YEAR(a.insert_date)=$cbo_year";
	else if($db_type==2) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		if($last_closing_date != "")
		{
			//echo $last_closing_date." yes " . $date_from;
			if(strtotime($start_date) > strtotime($last_closing_date))
			{
				$closing_month = trim(date("m", strtotime($last_closing_date)),"0");
				$date_cond=" and e.transaction_date > '$last_closing_date' and e.transaction_date <= '$end_date'";
				$date_cond2=" and a.transfer_date > '$last_closing_date' and a.transfer_date <= '$end_date'";
			}
			else
			{
				$sql_period="select max(PERIOD_ENDING_DATE) as PERIOD_ENDING_DATE from lib_item_category_comp_wise where company_id=$cbo_company_id and category_id=13 and status_active=1 and is_deleted=0 and period_ending_date < '$start_date'";

				$sql_period_result=sql_select($sql_period);
				$previous_closing_date = $sql_period_result[0]["PERIOD_ENDING_DATE"];
				$closing_month = trim(date("m", strtotime($previous_closing_date)),"0");
				if($previous_closing_date == "")
				{
					$date_cond=" and e.transaction_date <= '$end_date'";
					$date_cond2=" and a.transfer_date <= '$end_date'";
				}
				else
				{
					$date_cond=" and e.transaction_date > '$previous_closing_date' and e.transaction_date <= '$end_date'";
					$date_cond2=" and a.transfer_date > '$previous_closing_date' and a.transfer_date <= '$end_date'";
				}
			}
			
		}
		else
		{
			$date_cond=" and e.transaction_date <= '$end_date'";
			$date_cond2=" and a.transfer_date <= '$end_date'";
		}

		//$date_cond=" and e.transaction_date >= '$start_date' and e.transaction_date <= '$end_date'";
		//$date_cond2=" and a.transfer_date >= '$start_date' and a.transfer_date <= '$end_date'";

	}
	//echo $date_cond;
	if($cbo_store_name!="")
	{
		$store_cond  = " and e.store_id in($cbo_store_name)";
		$store_cond1  = " and a.store_id in($cbo_store_name)";
		$store_cond2 = " and b.to_store in($cbo_store_name)";
		$store_cond3 = " and b.from_store in($cbo_store_name)";
	}

	if($job_no_cond!="" || $book_no_cond!="" || $po_no_cond !="" || $color_cond!="")
	{
		$sql="select a.buyer_id, b.job_no, b.po_break_down_id as po_id, b.construction, b.fabric_color_id, sum(b.grey_fab_qnty) as grey_req_qnty, a.booking_no, d.buyer_name, c.po_number, d.style_ref_no
		from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
		where a.company_id=$cbo_company_id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and a.booking_no=b.booking_no and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=c.id and c.job_no_mst=d.job_no $job_no_cond $book_no_cond $po_no_cond $color_cond $year_field_cond 
		group by a.buyer_id, b.job_no, b.po_break_down_id, b.construction, b.fabric_color_id, a.booking_no, d.buyer_name, c.po_number, d.style_ref_no";
		//echo $sql;die;
		$sql_result=sql_select($sql);
		$po_ids='';

		foreach( $sql_result as $row )
		{
			if($po_ids=='') $po_ids=$row['PO_ID'];else $po_ids.=",".$row['PO_ID'];
		}

		$po_ids = array_unique(explode(",",$po_ids));
		$poIds_cond_roll="";

		$po_idss = implode(",", $po_ids);
		if($db_type==2 && count($po_ids)>999)
		{
			$po_chunk=array_chunk($po_ids,999) ;
			$barcode_cond = " and (";

			foreach($po_chunk as $chunk_arr)
			{
				$poIds_cond_roll.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			}

			$poIds_cond_roll = chop($poIds_cond_roll,"or ");
			$poIds_cond_roll .=")";
		}
		else
		{
			$poIds_cond_roll  = " and c.po_breakdown_id in($po_idss)";
		}

		unset($sql_result);
	}

	$main_query="SELECT A.ID, A.ENTRY_FORM, A.RECEIVE_BASIS, A.BOOKING_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY,B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.COLOR_RANGE_ID, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.PROD_ID, NULL AS FROM_TRANS_ID, NULL AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 1 AS TYPE, E.STORE_ID, E.TRANSACTION_DATE, C.RE_TRANSFER
	from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C
	where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and A.company_id=$cbo_company_id and A.entry_form in(2,22,58) and B.trans_id<>0 and C.entry_form in(2,22,58) and C.status_active=1 and C.is_deleted=0 $poIds_cond_roll $year_field_cond $date_cond $store_cond $color_cond2 and A.status_active=1 and A.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0
	union all
	select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY,D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID,D.GSM GSM,D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE,B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER
	from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
	where A.id=B.mst_id and B.id=C.dtls_id and B.from_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(83) and C.entry_form in(83) and C.status_active=1 and C.is_deleted=0 $transfer_date $poIds_cond_roll $year_field_cond $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1
	union all
	select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID, B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER
	from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
	where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(82) and C.entry_form in(82) and C.status_active=1 and C.is_deleted=0 $transfer_date $poIds_cond_roll $year_field_cond $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1
	union all
	select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE AS TRANSACTION_DATE, C.RE_TRANSFER
	from  inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
	where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(183) and C.entry_form in(183) and C.status_active=1 and C.is_deleted=0 $transfer_date $poIds_cond_roll $year_field_cond $date_cond2 $store_cond2 and C.booking_without_order=0 and A.status_active=1 and B.status_active=1 and C.status_active=1 ";//and a.transfer_criteria in (1)
	$result=sql_select( $main_query );
	if(count($result)==0)
	{
		echo "No Data Found";die;
	}

	$store_arr=$po_arr=array();
	$con = connect();
	if(!empty($result))
	{
		foreach ($result as $row)
		{
			if($row["BARCODE_NO"]!="" && $barcode_check[$row["BARCODE_NO"]]=="")
			{
				$barcode_check[$row["BARCODE_NO"]]=$row["BARCODE_NO"];
			}
			$po_arr[$row["PO_BREAKDOWN_ID"]]=$row["PO_BREAKDOWN_ID"];
			$prod_arr[$row["PROD_ID"]]=$row["PROD_ID"];
		}

		$barcode_cond=$barcode_cond2="";

		$barcodes = implode(",", $barcode_check);		
		if($db_type==2 && count($barcode_check)>999)
		{
			$barcode_chunk=array_chunk($barcode_check,999) ;
			$barcode_cond = " and (";
			$barcode_cond2 = " and (";

			foreach($barcode_chunk as $chunk_arr)
			{
				$barcode_cond.=" B.BARCODE_NO in(".implode(",",$chunk_arr).") or ";
				$barcode_cond2.=" C.BARCODE_NO in(".implode(",",$chunk_arr).") or ";
			}

			$barcode_cond = chop($barcode_cond,"or ");
			$barcode_cond .=")";
			
			$barcode_cond2 = chop($barcode_cond2,"or ");
			$barcode_cond2 .=")";
		}
		else
		{
			$barcode_cond  = " and B.BARCODE_NO in($barcodes)";
			$barcode_cond2  = " and C.BARCODE_NO in($barcodes)";
		}

		$po_cond=$po_closing_cond=$po_id_cond="";
		$po_nos = implode(",", $po_arr);
		if($db_type==2 && count($po_arr)>999)
		{
			$po_chunk=array_chunk($po_arr,999) ;
			$po_cond = " and (";
			$po_closing_cond = " and (";
			$po_id_cond = " and (";

			foreach($po_chunk as $chunk_arr)
			{
				$po_cond.=" B.PO_BREAK_DOWN_ID in(".implode(",",$chunk_arr).") or ";
				$po_closing_cond.=" REF_ID in(".implode(",",$chunk_arr).") or ";
				$po_id_cond.=" b.po_id in(".implode(",",$chunk_arr).") or ";
			}

			$po_cond = chop($po_cond,"or ");
			$po_cond .=")";
			
			$po_closing_cond = chop($po_closing_cond,"or ");
			$po_closing_cond .=")";

			$po_id_cond = chop($po_id_cond,"or ");
			$po_id_cond .=")";
		}
		else
		{
			$po_cond  = " and B.PO_BREAK_DOWN_ID in($po_nos)";
			$po_closing_cond  = " and REF_ID in($po_nos)";
			$po_id_cond  = " and b.po_id in($po_nos)";
		}

		$production_sql = "SELECT B.BARCODE_NO, A.COLOR_RANGE_ID, A.YARN_LOT, A.YARN_COUNT, A.BRAND_ID, B.PO_BREAKDOWN_ID, A.PROD_ID, B.BOOKING_NO, B.RECEIVE_BASIS, A.COLOR_ID, A.FEBRIC_DESCRIPTION_ID, A.GSM, A.WIDTH, A.STITCH_LENGTH, A.MACHINE_DIA, A.MACHINE_GG, A.MACHINE_NO_ID, C.KNITTING_SOURCE, C.CHALLAN_NO AS PRODUCTION_CHALLAN, C.KNITTING_COMPANY, A.YARN_PROD_ID, A.BODY_PART_ID 
		from inv_receive_master C, pro_grey_prod_entry_dtls A, pro_roll_details B
		where C.id=A.mst_id and A.id=B.dtls_id and C.entry_form=2 and B.entry_form in(2) and A.status_active=1 and B.status_active=1 $barcode_cond";
		//echo $production_sql;die;
		$production_info = sql_select($production_sql);
		foreach ($production_info as $row)
		{
			$prodBarcodeData[$row['BARCODE_NO']]["prod_basis"] 				= $row['RECEIVE_BASIS'];
			$prodBarcodeData[$row['BARCODE_NO']]["prog_book"] 				= $row['BOOKING_NO'];
			$prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] 			= $row['COLOR_RANGE_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] 				= $row['YARN_LOT'];
			$prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] 				= $row['YARN_COUNT'];
			$prodBarcodeData[$row['BARCODE_NO']]["yarn_prod_id"] 			= $row['YARN_PROD_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["prod_id"] 				= $row['PROD_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["color_id"] 				= $row['COLOR_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] 	= $row['FEBRIC_DESCRIPTION_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["gsm"] 					= $row['GSM'];
			$prodBarcodeData[$row['BARCODE_NO']]["width"] 					= $row['WIDTH'];
			$prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] 			= $row['STITCH_LENGTH'];
			$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"] 			= $row['MACHINE_DIA'];
			$prodBarcodeData[$row['BARCODE_NO']]["machine_gg"] 				= $row['MACHINE_GG'];
			$prodBarcodeData[$row['BARCODE_NO']]["machine_no_id"] 			= $row['MACHINE_NO_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["prod_challan"] 			= $row['PRODUCTION_CHALLAN'];
			$prodBarcodeData[$row['BARCODE_NO']]["knitting_source"] 		= $row['KNITTING_SOURCE'];
			$prodBarcodeData[$row['BARCODE_NO']]["knitting_company"] 		= $row['KNITTING_COMPANY'];
			$prodBarcodeData[$row['BARCODE_NO']]["body_part_id"] 			= $row['BODY_PART_ID'];
			$prodBarcodeData[$row['BARCODE_NO']]["brand_id"] 				= $row['BRAND_ID'];
			$allDeterArr[$row['FEBRIC_DESCRIPTION_ID']] 					= $row['FEBRIC_DESCRIPTION_ID'];
			$allColorArr[$row['COLOR_ID']] 									= $row['COLOR_ID'];
			$allYarnProdArr[$row['YARN_PROD_ID']] 							= $row['YARN_PROD_ID'];
		}
		unset($production_info);
		
		foreach($result as $row)
		{
			if($hdn_color)
			{
				if($hdn_color !=$prodBarcodeData[$row['BARCODE_NO']]["color_id"])continue;
			}

			$date_frm=date('Y-m-d',strtotime($start_date));
			$date_to=date('Y-m-d',strtotime($end_date));
			$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));

			$febric_description = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];
			$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];
			
			
			if($row['ENTRY_FORM']== 2 || $row['ENTRY_FORM']== 22 || $row['ENTRY_FORM']== 58)
			{
				$all_rcv[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] += $row['QNTY'];
				$count_rcv_barcode[$row['BARCODE_NO']]=$row['BARCODE_NO'];
			}
			else
			{
				if($count_rcv_barcode[$row['BARCODE_NO']]=="")
				{
					$all_rcv[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] += $row['QNTY'];
				}
			}
			
			$row_barcodess[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$row['BARCODE_NO']]=$row['BARCODE_NO'];

			if(strtotime($row['TRANSACTION_DATE']) >= strtotime($start_date))
			{
				if($row['ENTRY_FORM']== 2 || $row['ENTRY_FORM']== 22 || $row['ENTRY_FORM']== 58){
					$dataArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["receive"] += $row['QNTY'];
				}else{
					$dataArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["in"] += $row['QNTY'];
				}
			}
			else
			{
				if($row['ENTRY_FORM']== 2 || $row['ENTRY_FORM']== 22 || $row['ENTRY_FORM']== 58){
					$openingReceiveArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["opening_rcv"] += $row['QNTY'];
				}else{
					$openingTransInArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["opening_trans_in"]  += $row['QNTY'];
				}
			}

			$color_wise_count[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]]++;

			if($row['COLOR_ID']!=""){
				$all_color_arr[$row['COLOR_ID']] = $row['COLOR_ID'];
			}

			$barcodeArr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
			$febric_description_arr[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
			$storeArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]][$row['RE_TRANSFER']] .= $row['STORE_ID'].",";
			$noOfRollArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] .= $row['BARCODE_NO'].",";
			$bookingIdArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$row['BARCODE_NO']]["color_id"]] .= $row['BOOKING_ID'].",";
			$store_id_arr[$row['STORE_ID']] = $row['STORE_ID'];
			// $po_id_arr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
			$color_count++;
			
		}
		unset($result);

		$po_sql="SELECT B.JOB_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, SUM(B.GREY_FAB_QNTY) AS GREY_REQ_QNTY, B.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO, B.IS_SHORT, D.BUYER_NAME as BUYER_ID 
		from wo_booking_dtls B, wo_po_break_down C, wo_po_details_master D
		where D.COMPANY_NAME=$cbo_company_id and B.BOOKING_TYPE=1 and B.is_deleted=0 and B.status_active=1 and B.po_break_down_id=C.id and C.job_no_mst=D.job_no $po_cond group by B.JOB_NO, B.PO_BREAK_DOWN_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, B.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO, b.IS_SHORT, D.BUYER_NAME order by B.PO_BREAK_DOWN_ID asc, B.IS_SHORT desc";
		//echo $po_sql;die;

		$po_sql_result=sql_select($po_sql);
		$po_ids='';

		foreach( $po_sql_result as $row )
		{
			$key=$row['BUYER_ID'].$row['JOB_NO'].$row['PO_ID'].$row['CONSTRUCTION'].$row['FABRIC_COLOR_ID'];
			$grey_qnty_array[$key] += $row['GREY_REQ_QNTY'];

			$booking_array[$row['PO_ID']]['job_no'] 		= $row['JOB_NO'];
			$booking_array[$row['PO_ID']]['po_number'] 		= $row['PO_NUMBER'];
			$booking_array[$row['PO_ID']]['style_ref_no'] 	= $row['STYLE_REF_NO'];
			$booking_array[$row['PO_ID']]['buyer_name'] 	= $row['BUYER_NAME'];
			if($booking_po_check[$row['PO_ID']][$row['BOOKING_NO']]=="")
			{
				$booking_po_check[$row['PO_ID']][$row['BOOKING_NO']]=$row['BOOKING_NO'];
				if($row['IS_SHORT']==1) $booking_nos=$row['BOOKING_NO']."[S]"; else $booking_nos=$row['BOOKING_NO'];
				$booking_array[$row['PO_ID']]['booking_no'] .= $booking_nos.",";
			}
		}
		unset($po_sql_result);

		$product_array=array();	
		if(!empty($prod_arr))
		{
			$prod_query="Select ID, DETARMINATION_ID,ITEM_DESCRIPTION, GSM, DIA_WIDTH,COLOR from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 and id in(".implode(",",$prod_arr).")";
			$prod_query_sql=sql_select($prod_query);
			foreach( $prod_query_sql as $row )
			{
				$product_array[$row[csf('id')]]['item_description_id']=$row['DETARMINATION_ID'];
				$product_array[$row[csf('id')]]['item_description']=$row['ITEM_DESCRIPTION'];
				$product_array[$row[csf('id')]]['color']=$row['COLOR'];
				$product_array[$row[csf('id')]]['gsm']=$row['GSM'];
				$product_array[$row[csf('id')]]['dia_width']=$row['DIA_WIDTH'];
			}
		}
	}

	if($date_from!="" && $date_to!="")
	{
		$sql_closing="select * from year_close_item_ref where company_id=$cbo_company_id and ref_type=7 $po_closing_cond";
		$data_closing=sql_select($sql_closing);
		$po_prod_opening_from_closing=array();
		
		foreach( $data_closing as $row )
		{
			$po_prod_opening_from_closing[$row['REF_ID']][$row['PROD_ID']] = $row["PERIOD_$closing_month"];
		}
	}
	$issue_sql = "SELECT C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM,E.PROD_ID
	from pro_roll_details C, inv_grey_fabric_issue_dtls B, inv_transaction E
	where C.entry_form=61 and C.status_active=1 and C.is_deleted=0 $date_cond $store_cond	
	and C.booking_without_order = 0 and C.dtls_id=B.id and B.trans_id=E.id and E.transaction_type=2 $barcode_cond2
	group by C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM,E.PROD_ID
	union all
	select D.PO_BREAKDOWN_ID, C.BARCODE_NO, A.TRANSFER_DATE TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM,D.PROD_ID
	from order_wise_pro_details D, inv_item_transfer_dtls B, pro_roll_details C, inv_item_transfer_mst A
	where D.trans_id=B.trans_id and B.id=C.dtls_id and C.mst_id=A.id and b.mst_id=A.id and A.entry_form=83 and C.entry_form=83 and C.status_active=1 and C.is_deleted=0 and D.status_active=1 and D.is_deleted=0 and D.trans_type=6
	$date_cond2 $store_cond3 $barcode_cond2 and C.booking_without_order = 0
	group by D.PO_BREAKDOWN_ID, C.BARCODE_NO, A.TRANSFER_DATE, C.QNTY, C.ENTRY_FORM,D.PROD_ID
	union all
	select B.FROM_ORDER_ID AS PO_BREAKDOWN_ID, C.BARCODE_NO, A.TRANSFER_DATE TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM,B.FROM_PROD_ID PROD_ID
	from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C
	where A.id = B.mst_id and B.id = C.dtls_id and A.id = C.mst_id
	$date_cond2 $store_cond3 $barcode_cond2
	and A.entry_form = 82 and C.entry_form = 82 and B.status_active = 1 and C.status_active = 1 and A.status_active = 1 and C.booking_without_order = 0 
	group by B.FROM_ORDER_ID, C.BARCODE_NO, A.TRANSFER_DATE, C.QNTY, C.ENTRY_FORM,B.FROM_PROD_ID
	union all
	select A.FROM_ORDER_ID AS PO_BREAKDOWN_ID, C.BARCODE_NO, A.TRANSFER_DATE TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM,B.FROM_PROD_ID PROD_ID
	from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C
	where A.id = B.mst_id and A.id = C.mst_id and B.id = C.dtls_id
	$date_cond2 $store_cond3 $barcode_cond2
	and A.entry_form = 110 and C.entry_form = 110 and B.status_active = 1 and C.status_active = 1 and A.status_active = 1
	group by A.FROM_ORDER_ID, C.BARCODE_NO, A.TRANSFER_DATE, C.QNTY, C.ENTRY_FORM,B.FROM_PROD_ID";
	//echo $issue_sql;//die;
	$issue_info=sql_select($issue_sql);
	foreach($issue_info as $row)
	{
		$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];

		$mother_barcode_no = $mother_barcode_arr[$row['BARCODE_NO']];
		if($mother_barcode_no != "")
		{
			$fabrication = $prodBarcodeData[$mother_barcode_no]["febric_description_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["gsm"] . "*" . $prodBarcodeData[$mother_barcode_no]["width"] . "*" . $prodBarcodeData[$mother_barcode_no]["color_range_id"] . "*" . $prodBarcodeData[$mother_barcode_no]["stitch_length"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_lot"] . "*" . $prodBarcodeData[$mother_barcode_no]["yarn_count"] . "*" . $prodBarcodeData[$mother_barcode_no]["brand_id"]."*".$prodBarcodeData[$mother_barcode_no]["machine_dia"];
		}else{
			$mother_barcode_no = $row['BARCODE_NO'];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));
		if($row['ENTRY_FORM']==61)
		{
			$all_issue[$row['PO_BREAKDOWN_ID']][$fabrication] += $row['QNTY'];
		}

		if(strtotime($row['TRANSACTION_DATE']) >= strtotime($start_date))
		{
			if($row['ENTRY_FORM']==61){
				$dataArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["issue"] += $row['QNTY'];
			}else{
				$dataArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["trans_out"] += $row['QNTY'];
			}
		}
		else
		{
			if($row['ENTRY_FORM']==61){
				$openingIssueArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["issue"] += $row['QNTY'];
				$PoWiseData[$row['PO_BREAKDOWN_ID']]["opening_issue"] += $row['QNTY'];
			}else{
				$openingTransOutArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["trans_out"] += $row['QNTY'];
				$PoWiseData[$row['PO_BREAKDOWN_ID']]["opening_trans_out"] += $row['QNTY'];
			}
		}

		$noOfRollIssueArr[$row['PO_BREAKDOWN_ID']][$fabrication][$prodBarcodeData[$mother_barcode_no]["color_id"]] .= $row['BARCODE_NO'].",";
	}
	unset($issue_info);

	$iss_rtn_qty_sql="SELECT C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE,E.PROD_ID, SUM(C.QNTY) QNTY
	from pro_roll_details C, pro_grey_prod_entry_dtls B, inv_transaction E
	where C.entry_form=84 and C.status_active=1 and C.is_deleted=0 and C.dtls_id=B.id and B.trans_id=E.id and B.status_active=1 and B.is_deleted=0 and E.status_active=1 and E.is_deleted=0 and E.transaction_type=4 $store_cond $barcode_cond2 
	group by C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE,E.PROD_ID";
	$iss_rtn_qty=sql_select($iss_rtn_qty_sql);
	
	foreach($iss_rtn_qty as $row)
	{
		$fabrication = $prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["gsm"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["width"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] . "*" . $prodBarcodeData[$row['BARCODE_NO']]["brand_id"]."*".$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));

		if(strtotime($row['TRANSACTION_DATE']) >= strtotime($start_date))
		{
			$dataArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["issue_return"] += $row['QNTY'];
		}
		else
		{
			$openingReturnArr[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]["issue_return"] += $row['QNTY'];
		}
	}
	
	
	$febric_description_arr = array_filter($febric_description_arr);
	
	if(!empty($febric_description_arr))
	{
		$ref_febric_description_ids = implode(",", $febric_description_arr);
		$fabCond = $ref_febric_description_cond = "";
		if($db_type==2 && count($febric_description_arr)>999)
		{
			$ref_febric_description_arr_chunk=array_chunk($febric_description_arr,999);
			foreach($ref_febric_description_arr_chunk as $chunk_arr)
			{
				$fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
			}
			$ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
		}
		else
		{
			$ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
		}

		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
		$deter_array=sql_select($sql_deter);
		if(count($deter_array)>0)
		{
			foreach($deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}

				$constuction_arr[$row[csf('id')]]=$row[csf('construction')];

				if($row[csf('type_id')]>0)
				{
					$type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}
		unset($deter_array);
	}

	if($db_type==0)
	{
		$program_no_array=return_library_array( "select b.po_id, group_concat(distinct(dtls_id)) as dtls_id from  ppl_planning_entry_plan_dtls b where status_active=1 and is_deleted=0 $po_id_cond group by po_id", "po_id", "dtls_id"  );
	}
	else
	{
		$program_no_array=return_library_array( "select po_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as dtls_id from ppl_planning_entry_plan_dtls b where status_active=1 and is_deleted=0 $po_id_cond group by po_id", "po_id", "dtls_id"  );	
	}
	
	//$colorArr=return_library_array( "select id, color_name from lib_color", "id", "color_name" );

	
	$width = 1600;
	ob_start();
	?>
	<style>
		.word-break { word-break: break-all; }
		.rpt_table tbody tr td { height:auto !important; padding:3px 0; }
	</style>
    <fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
    	<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="12" align="center"><? echo $company_arr[$cbo_company_id]; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="12" align="center">Date From: <? echo change_date_format($date_from); ?> To : <? echo change_date_format($date_to); ?></td>
            </tr>
        </table>
		
        <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th width="20" rowspan="2">SL</th>
					<th colspan="9">Fabric Details</th>
					<!-- <th width="80" rowspan="2">Req. Qty.</th> -->
					<th width="80" rowspan="2">Opening</th>
					<th colspan="4">Receive Details</th>
					<th colspan="4">Issue Details</th>
					<th colspan="6">Stock Details</th>
				</tr>
				<tr>
					<th width="80">Job No.</th>
					<th width="80">Buyer</th>
					<th width="80">Order No.</th>
					<th width="80">Style Ref</th>
					<th width="100">Program No.</th>
					<th width="100">Booking No.</th>
					<th width="140">Description</th>
					<th width="60">GSM</th>
					<th width="60">F/Dia</th>
					<!-- <th width="60">Color</th> -->

					<th width="80">Recv. Qty.</th>
					<th width="80">Issue Ret. Qty.</th>
					<th width="80">Transf. In Qty.</th>
					<th width="80">Total Recv.</th>
					<th width="80">Issue Qty.</th>
					<th width="80">Recv. Ret. Qty.</th>
					<th width="80">Transf. Out Qty.</th>
					<th width="80">Total Issue</th>
					<th width="80">Stock Qty.</th>
				</tr>
			</thead>

				<?
				if(!empty($dataArr))
				{
					$i=1;
					$total_receive=$total_issue_return=$total_trans_in=$all_recv_trans_total=$total_issue=$total_trans_out=$all_issue_trans_total=$total_stock=$total_recv_balance=$total_issue_balance=$total_req_qnty=$total_no_of_roll=$total_opening=0;
					foreach ($dataArr as $po_id=>$po_row)
					{
						foreach ($po_row as $prod_id => $row)
						{
							//foreach ($prod_row as $color_id => $row){
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$item_deter_id 		= $product_array[$prod_id]['item_description_id'];
								$item_description 	= $constuction_arr[$item_deter_id] . ", " . $composition_arr[$item_deter_id];
								$gsm 				= $product_array[$prod_id]['gsm'];
								$dia_width 			= $product_array[$prod_id]['dia_width'];

								$job_no 		= $booking_array[$po_id]['job_no'];
								$booking_no 	= chop($booking_array[$po_id]['booking_no'],",");
								$po_number 		= $booking_array[$po_id]['po_number'];
								$style_ref_no 	= $booking_array[$po_id]['style_ref_no'];
								$buyer_name 	= $booking_array[$po_id]['buyer_name'];

								$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));

								$year_closing_opening_balance = number_format($po_prod_opening_from_closing[$po_id][$prod_id]*1,2,".","");

								$opening_receive_qnty 	= number_format($openingReceiveArr[$po_id][$prod_id]["opening_rcv"],2,".","");
								$opening_return_qnty 	= number_format($row["issue_return"],2,".","");
								$opening_trans_in_qnty 	= number_format($openingTransInArr[$po_id][$prod_id]["opening_trans_in"],2,".","");

								$opening_issue_qnty 	= number_format($openingIssueArr[$po_id][$prod_id]["issue"],2,".","");
								$opening_trans_out_qnty = number_format($openingTransOutArr[$po_id][$prod_id]["trans_out"],2,".","");

								$opening = number_format(($year_closing_opening_balance+($opening_receive_qnty+$opening_return_qnty+$opening_trans_in_qnty)-($opening_issue_qnty+$opening_trans_out_qnty)),2,".","");
								$opening_title = "Receive=$opening_receive_qnty \nIssue Return = $opening_return_qnty \nTrans In = $opening_trans_in_qnty \nIssue = $opening_issue_qnty \nTrans Out = $opening_trans_out_qnty";

								$receive_qnty 		= number_format($row["receive"],2,".","");
								$trans_in_qnty 		= number_format($row["in"],2,".","");
								$issue_return_qnty	= number_format($issue_return_arr[$po_id][$prod_id]["issue_return"],2,".","");

								$issue_qnty    		= number_format($row["issue"],2,".","");
								$trans_out    		= number_format($row["trans_out"],2,".","");


								$all_receive_qnty	= number_format($receive_qnty + $trans_in_qnty + $issue_return_qnty,2,".","");
								$all_issue_qnty		= number_format($issue_qnty + $trans_out,2,".","");
								$stock_qnty 		= number_format(($opening + $all_receive_qnty) - $all_issue_qnty,2,".","");

								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="20" title="<? echo implode(",",$row_barcodess[$po_id][$prod_id]);?>">
									<? echo $i;?>
									</td>
									<td title="Job No"><? echo $job_no;?></td>
									<td title="Buyer"><? echo $buyer_array[$buyer_name];?></td>
									<td title="Order IDs = <? echo $po_id;?>"><? echo $po_number;?></td>
									<td title="Style Ref"><? echo $style_ref_no;?></td>
									<td title="Program No"><? echo $program_no;?></td>
									<td title="Booking No"><? echo $booking_no;?></td>
									<td title="<? echo $item_description; ?>"><? echo $item_description;?></td>
									<td title="GSM"><? echo $gsm;?></td>
									<td title="F/Dia"><? echo $dia_width;?></td>
									<!-- <td title="Fabric Color"><? //echo $colorArr[$color_id];?></td>
									<td title="Req. Qty." align="right"><? //echo $required_qnty; ?></td>-->
									<td align="right" title="<? echo $febric_description."\n".$opening_title;?>"><? echo $opening; ?></td>
									<td title="Recv. Qty." align="right"><a href="##" onClick="openpage('recv_popup','<? echo $data_all; ?>','750')"><? echo $receive_qnty; ?></a></td>										
									<td align="right" title="Issue Ret. Qty."><? echo $issue_return_qnty; ?></td>
									<td align="right"><a href="##" onClick="openpage('transfer_popup','<? echo $data_all_tin; ?>','750')"><? echo $trans_in_qnty; ?></a></td>
									<td align="right" title="Total Recv."><? echo $all_receive_qnty; ?></td>
									<td align="right" title="Issue Qty."><a href="##" onClick="openpage('iss_popup','<? echo $data_all; ?>','650')"><? echo $issue_qnty; ?></a></td>
									<td align="right" title="Recv. Ret. Qty."></td>
									<td align="right" title="Transf. Out Qty."><a href="##" onClick="openpage('transfer_popup','<? echo $data_all_tout; ?>','750')"><? echo $trans_out; ?></a></td>
									<td title="Opening" align="right"><? echo $all_issue_qnty; ?></td>
									<td title="Opening" align="right"><? echo $stock_qnty; ?></td>
								</tr>
							<?
							$i++;
							//}
						}
					}
					
				}
				?>
			</table>
		</div>
	</fieldset>

    <?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "S";
}

if($action=="report_generate_buyer")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_buyer_id 		= str_replace("'","",$cbo_buyer_id);
	$txt_order_no 		= str_replace("'","",$txt_order_no);
	$hdn_color 			= str_replace("'","",$hdn_color);
	$txt_color 			= str_replace("'","",$txt_color);
	$cbo_year 			= str_replace("'","",$cbo_year);
	$booking_no 		= str_replace("'","",$txt_booking_no);
	$job_no 			= str_replace("'","",$txt_job_no);
	$date_from			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$rpt_type 			= str_replace("'","",$rpt_type);
	$cbo_trans_year		= str_replace("'","",$cbo_trans_year);
	
	
	//echo $cbo_store_name."test";die;

	$_SESSION["date_from"]=date('Y-m-d',strtotime($date_from));
	$_SESSION["date_to"]=date('Y-m-d',strtotime($date_to));

	$company_arr 		= return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_array 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr 			= return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
	if($job_no!='') $job_no_cond="and d.job_no_prefix_num in($job_no)";else $job_no_cond="";
	if($txt_order_no!='') $po_no_cond="and c.po_number='$txt_order_no'";else $po_no_cond="";
	if($hdn_color!='') $color_cond="and b.fabric_color_id in($hdn_color)";else $color_cond="";
	if($hdn_color!='') $color_cond2=" and b.color_id in('$hdn_color')";else $color_cond2="";
	$year_field_cond="";
	
	if($cbo_year !="")
	{
		$cbo_year_ref=explode(",",$cbo_year);
		$year_id_string="";
		foreach($cbo_year_ref as $year_id)
		{
			$year_id_string.="'".$year_id."',";
		}
		$year_id_string=chop($year_id_string,",");
		//echo $year_id_string;die;
		
		if($job_no!='')
		{
			if($db_type==0)
			{
				$year_field_cond="and YEAR(C.insert_date) in($cbo_year)";
			}
			else
			{
				$year_field_cond=" and to_char(C.insert_date,'YYYY') in($year_id_string)";
			}
		}
		else
		{
			if($db_type==0) 
			{
				$year_field_cond="and YEAR(a.insert_date) in($cbo_year)";
			}
			else 
			{
				$year_field_cond=" and to_char(a.insert_date,'YYYY') in($year_id_string)";
			}
		}
	}
	
	/*if($job_no!='')
	{
		if($cbo_year>0)
		{
			if($db_type==0) $year_field_cond="and YEAR(C.insert_date)=$cbo_year";
			else if($db_type==2) $year_field_cond=" and to_char(C.insert_date,'YYYY')=$cbo_year";
		}
		
	}
	else
	{
		if($cbo_year>0)
		{
			if($db_type==0) $year_field_cond="and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}*/
	
	if($cbo_trans_year>0)
	{
		if($db_type==0) $trans_year_field_cond="and YEAR(a.insert_date)=$cbo_trans_year";
		else if($db_type==2) $trans_year_field_cond=" and to_char(a.insert_date,'YYYY')=$cbo_trans_year";
	}
	

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond=" and e.transaction_date <= '$end_date'";
		$date_cond2=" and a.transfer_date <= '$end_date'";
	}

	if($cbo_store_name!="")
	{
		$store_cond  = " and e.store_id in($cbo_store_name)";
		$store_cond1  = " and a.store_id in($cbo_store_name)";
		$store_cond2 = " and b.to_store in($cbo_store_name)";
		$store_cond3 = " and b.from_store in($cbo_store_name)";
	}
	
	$po_ids_array=array();
	if($job_no_cond!="" || $book_no_cond!="" || $po_no_cond !="" || $color_cond!="" || $year_field_cond!="" || $cbo_buyer_id > 0)
	{
		$buyer_cond="";
		if($cbo_buyer_id>0) $buyer_cond=" and A.BUYER_ID=$cbo_buyer_id";
		$sql="select A.BUYER_ID, B.JOB_NO, B.PO_BREAK_DOWN_ID AS PO_ID, B.CONSTRUCTION, B.FABRIC_COLOR_ID, SUM(B.GREY_FAB_QNTY) AS GREY_REQ_QNTY, A.BOOKING_NO, D.BUYER_NAME, C.PO_NUMBER, D.STYLE_REF_NO 
		from wo_booking_mst A, wo_booking_dtls B, wo_po_break_down C, wo_po_details_master D 
		where A.company_id=$cbo_company_id and A.item_category in(2,13) and A.is_deleted=0 and A.status_active=1 and A.booking_no=B.booking_no and B.is_deleted=0 and B.status_active=1 and B.po_break_down_id=C.id and C.job_no_mst=D.job_no $job_no_cond $book_no_cond $po_no_cond $color_cond $year_field_cond  $buyer_cond
		group by A.buyer_id, B.job_no, B.po_break_down_id, B.construction, B.fabric_color_id, A.booking_no, D.buyer_name, C.po_number, D.style_ref_no";
		//echo $sql;//die;
		$sql_result=sql_select($sql);
		$po_ids='';
		
		foreach( $sql_result as $row )
		{
			if($po_ids=='') $po_ids=$row['PO_ID'];else $po_ids.=",".$row['PO_ID'];
			if($row['PO_ID']!="" && $po_ids_array[$row['PO_ID']]=="")
			{
				$po_ids_array[$row['PO_ID']]=$row['PO_ID'];
				$poId = $row['PO_ID'];
				$rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$poId,2)");
			}
		}

		if($rID2)
		{
			oci_commit($con);
		}
		
		/*
		$po_ids = array_unique(explode(",",$po_ids));
		$poIds_cond_roll="";
		$po_idss = implode(",", $po_ids);
		if($db_type==2 && count($po_ids)>999)
		{
			$po_chunk=array_chunk($po_ids,999) ;
			$poIds_cond_roll = " and (";

			foreach($po_chunk as $chunk_arr)
			{
				$poIds_cond_roll.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			}

			$poIds_cond_roll = chop($poIds_cond_roll,"or ");
			$poIds_cond_roll .=")";
		}
		else
		{
			$poIds_cond_roll  = " and c.po_breakdown_id in($po_idss)";
		}*/
		
		unset($sql_result);
	}
	
	//echo count($po_ids_array);die;
	
	//echo $poIds_cond_roll;die;
	if(count($po_ids_array)>0)
	{
		$main_query="SELECT A.ID, A.ENTRY_FORM, A.RECEIVE_BASIS, A.BOOKING_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.COLOR_RANGE_ID, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.PROD_ID, NULL AS FROM_TRANS_ID, NULL AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 1 AS TYPE, E.STORE_ID, E.TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C, tmp_poid F
		where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(2,22,58,84) and B.trans_id<>0 and C.entry_form in(2,22,58,84) and C.status_active=1 and C.is_deleted=0 $trans_date $date_cond $store_cond $color_cond2 and A.status_active=1 and A.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY,D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID,D.GSM GSM,D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE,B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.from_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(83) and C.entry_form in(83) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID, B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(82) and C.entry_form in(82) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE AS TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from  inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D, tmp_poid F
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2 and A.company_id=$cbo_company_id and A.entry_form in(183) and C.entry_form in(183) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order=0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		order by BARCODE_NO ASC, ROLL_ID DESC";
	}
	else
	{
		$main_query="SELECT A.ID, A.ENTRY_FORM, A.RECEIVE_BASIS, A.BOOKING_ID, A.KNITTING_SOURCE, A.KNITTING_COMPANY,B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.COLOR_RANGE_ID, B.YARN_LOT, B.YARN_COUNT, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_NO_ID, B.PROD_ID, NULL AS FROM_TRANS_ID, NULL AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 1 AS TYPE, E.STORE_ID, E.TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C
		where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and A.company_id=$cbo_company_id and A.entry_form in(2,22,58,84) and B.trans_id<>0 and C.entry_form in(2,22,58,84) and C.status_active=1 and C.is_deleted=0 $trans_date $date_cond $store_cond $color_cond2 and A.status_active=1 and A.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY,D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID,D.GSM GSM,D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE,B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.from_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(83) and C.entry_form in(83) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID, B.TO_PROD_ID PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(82) and C.entry_form in(82) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order = 0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		union all
		select A.ID, A.ENTRY_FORM, NULL AS RECEIVE_BASIS, NULL AS BOOKING_ID, NULL AS KNITTING_SOURCE, NULL AS KNITTING_COMPANY, D.DETARMINATION_ID AS FEBRIC_DESCRIPTION_ID, D.GSM GSM, D.DIA_WIDTH AS WIDTH, NULL AS COLOR_ID, NULL AS COLOR_RANGE_ID, NULL AS YARN_LOT, NULL AS YARN_COUNT, NULL AS STITCH_LENGTH, NULL AS BRAND_ID, NULL AS MACHINE_NO_ID,B.TO_PROD_ID, B.TRANS_ID AS FROM_TRANS_ID, B.TO_TRANS_ID AS TO_TRANS_ID, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, 2 AS TYPE, B.TO_STORE STORE_ID, A.TRANSFER_DATE AS TRANSACTION_DATE, C.RE_TRANSFER, C.ID as ROLL_ID
		from  inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C, product_details_master D
		where A.id=B.mst_id and B.id=C.dtls_id and B.to_prod_id=D.id and A.company_id=$cbo_company_id and A.entry_form in(183) and C.entry_form in(183) and C.status_active=1 and C.is_deleted=0 $transfer_date $date_cond2 $store_cond2 and C.booking_without_order=0 and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 $trans_year_field_cond
		order by BARCODE_NO ASC, ROLL_ID DESC";
	}
	//echo count($po_ids_array);die;
	//echo $main_query;die();
	//==================================
	$result=sql_select( $main_query );
	//echo count($result);die;
	if(count($result)==0)
	{
		echo "No Data Found";die;
	}
	$store_arr=array();
	if(count($po_ids_array)==0)
	{
		foreach($result as $row)
		{
			if($row["PO_BREAKDOWN_ID"]!="" && $po_id_check[$row["PO_BREAKDOWN_ID"]]=="")
			{
				$po_id_check[$row["PO_BREAKDOWN_ID"]]=$row["PO_BREAKDOWN_ID"];
				$poId = $row["PO_BREAKDOWN_ID"];
				$rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$poId,2)");
			}
		}
		if($rID2)
		{
			oci_commit($con);
		}
	}
	
	/*$production_sql = "SELECT B.BARCODE_NO, A.COLOR_RANGE_ID, A.YARN_LOT, A.YARN_COUNT, A.BRAND_ID, B.PO_BREAKDOWN_ID, A.PROD_ID, B.BOOKING_NO, B.RECEIVE_BASIS, A.COLOR_ID, A.FEBRIC_DESCRIPTION_ID, A.GSM, A.WIDTH, A.STITCH_LENGTH, A.MACHINE_DIA, A.MACHINE_GG, A.MACHINE_NO_ID, C.KNITTING_SOURCE, C.CHALLAN_NO AS PRODUCTION_CHALLAN, C.KNITTING_COMPANY, A.YARN_PROD_ID, A.BODY_PART_ID 
	from inv_receive_master C, pro_grey_prod_entry_dtls A, pro_roll_details B, tmp_poid F
	where C.id=A.mst_id and A.id=B.dtls_id and C.entry_form=2 and B.entry_form in(2) and A.status_active=1 and B.status_active=1 and B.po_breakdown_id=F.poid and F.userid=$user_id and F.type=2";
	//echo $production_sql;die;
	$production_info = sql_select($production_sql);
	foreach ($production_info as $row)
	{
		$prodBarcodeData[$row['BARCODE_NO']]["prod_basis"] 			= $row['RECEIVE_BASIS'];
		$prodBarcodeData[$row['BARCODE_NO']]["prog_book"] 				= $row['BOOKING_NO'];
		$prodBarcodeData[$row['BARCODE_NO']]["color_range_id"] 		= $row['COLOR_RANGE_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_lot"] 				= $row['YARN_LOT'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_count"] 			= $row['YARN_COUNT'];
		$prodBarcodeData[$row['BARCODE_NO']]["yarn_prod_id"] 			= $row['YARN_PROD_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["prod_id"] 				= $row['PROD_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["color_id"] 				= $row['COLOR_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["febric_description_id"] 	= $row['FEBRIC_DESCRIPTION_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["gsm"] 					= $row['GSM'];
		$prodBarcodeData[$row['BARCODE_NO']]["width"] 					= $row['WIDTH'];
		$prodBarcodeData[$row['BARCODE_NO']]["stitch_length"] 			= $row['STITCH_LENGTH'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_dia"] 			= $row['MACHINE_DIA'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_gg"] 			= $row['MACHINE_GG'];
		$prodBarcodeData[$row['BARCODE_NO']]["machine_no_id"] 			= $row['MACHINE_NO_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["prod_challan"] 			= $row['PRODUCTION_CHALLAN'];
		$prodBarcodeData[$row['BARCODE_NO']]["knitting_source"] 		= $row['KNITTING_SOURCE'];
		$prodBarcodeData[$row['BARCODE_NO']]["knitting_company"] 		= $row['KNITTING_COMPANY'];
		$prodBarcodeData[$row['BARCODE_NO']]["body_part_id"] 			= $row['BODY_PART_ID'];
		$prodBarcodeData[$row['BARCODE_NO']]["brand_id"] 				= $row['BRAND_ID'];
		$allDeterArr[$row['FEBRIC_DESCRIPTION_ID']] 					= $row['FEBRIC_DESCRIPTION_ID'];
		$allColorArr[$row['COLOR_ID']] 								= $row['COLOR_ID'];
		$allYarnProdArr[$row['YARN_PROD_ID']] 							= $row['YARN_PROD_ID'];
		
		$febric_description_arr[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
	}
	unset($production_info);*/
	
	$color_count = 1;
	$_SESSION["date_frm"]=date('Y-m-d',strtotime($start_date));
	
	$po_sql="SELECT C.Id AS PO_ID, D.BUYER_NAME
	from wo_po_break_down C, wo_po_details_master D, tmp_poid E  
	where D.COMPANY_NAME=$cbo_company_id and C.job_no_mst=D.job_no and C.id=E.poid and E.userid=$user_id and E.type=2
	group by C.Id, D.BUYER_NAME";
	//echo $po_sql;die;

	$po_sql_result=sql_select($po_sql);
	//echo "<pre>";print_r($febric_description_arr);die;
	$po_ids='';
	foreach( $po_sql_result as $row )
	{
		$booking_array[$row['PO_ID']]['buyer_name'] 	= $row['BUYER_NAME'];
	}
	unset($po_sql_result);
	
	//echo "<pre>";print_r($febric_description_arr);die;
	foreach($result as $row)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));
		
		if($dup_barcode_rcv[$row['BARCODE_NO']]=="")
		{
			$dup_barcode_rcv[$row['BARCODE_NO']]=$row['BARCODE_NO'];
			$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["tot_receive"] += $row['QNTY'];
			if($transaction_date < $date_frm)
			{
				$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["opening_rcv"] += $row['QNTY'];
				$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["receive"] += 0;
			}
			else
			{
				$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["opening_rcv"] += 0;
				$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["receive"] += $row['QNTY'];
			}
		}
		$store_id_arr[$row['STORE_ID']]=$row['STORE_ID'];
	}
	//echo "<pre>";print_r($dataArr);die;
	unset($result);
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" );
	
	//and c.barcode_no in(SELECT poid from tmp_poid where userid=$user_id and type=1)
	$split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
	from pro_roll_split C, pro_roll_details D, tmp_poid E 
	where C.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and D.PO_BREAKDOWN_ID=E.poid and E.userid=$user_id and E.type=2");

	if(!empty($split_chk_sql))
	{
		foreach ($split_chk_sql as $val)
		{
			$split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
		}

		$split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE from pro_roll_details A, pro_roll_details B where A.barcode_no in (".implode(",", $split_barcode_arr).") and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
		if(!empty($split_ref_sql))
		{
			foreach ($split_ref_sql as $value)
			{
				$mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
			}
		}
	}
	unset($split_chk_sql);
	
	$issue_sql = "SELECT C.PO_BREAKDOWN_ID, C.BARCODE_NO, E.TRANSACTION_DATE, C.QNTY, C.ENTRY_FORM
	from pro_roll_details C, inv_grey_fabric_issue_dtls B, inv_transaction E, tmp_poid F
	where C.entry_form=61 and C.status_active=1 and C.is_deleted=0 and C.IS_RETURNED=0 $date_cond $store_cond $trans_year_field_cond		
	and C.booking_without_order = 0 and C.dtls_id=B.id and B.trans_id=E.id and E.transaction_type=2 and C.PO_BREAKDOWN_ID=F.poid and F.userid=$user_id and F.type=2";//and a.transfer_criteria in (1)
	//echo $issue_sql;die;
	$issue_info=sql_select($issue_sql);
	foreach($issue_info as $row)
	{
		if($mother_barcode_no != "")
		{
			$mother_barcode_no = $mother_barcode_arr[$row['BARCODE_NO']];
		}else{
			$mother_barcode_no = $row['BARCODE_NO'];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));
		if($dup_barcode_issue[$row['BARCODE_NO']]=="")
		{
			$dup_barcode_issue[$row['BARCODE_NO']]=$row['BARCODE_NO'];
			$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["tot_issue"] += $row['QNTY'];
			if($transaction_date < $date_frm)
			{
				$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["opening_issue"] += $row['QNTY'];
				$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["issue"] += 0;
			}
			else
			{
				$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["opening_issue"] += 0;
				$dataArr[$booking_array[$row['PO_BREAKDOWN_ID']]['buyer_name']]["issue"] += $row['QNTY'];
			}
		}
	}
	unset($issue_info);
	//echo "<pre>"; print_r($dataArr);die;
	/*
	echo "</pre>";*/
	//echo "<pre>";print_r($dataArr);die;
	
	unset($iss_rtn_qty_sql);
	$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type in(1,2)");
	if($r_id3)
	{
		oci_commit($con);
	}
	//print_r($issue_return_arr);die;
	$width = 850;
	ob_start();
	?>
	<style>
		.word-break { word-break: break-all; }
		.rpt_table tbody tr td { height:auto !important; padding:3px 0; }
	</style>
    <fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
        
        <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="12" align="center"><? echo $company_arr[$cbo_company_id]; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="12" align="center">Buyer Wise Summary</td>
            </tr>
        </table>
    
        <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="150">Buyer</th>
                    <th width="100">Opening</th>
                    <th width="100">Receive(Date Range)</th>
                    <th width="100">Issue(Date Range)</th>
                    <th width="100">Total Receive</th>
                    <th width="100">Total Issue</th>
                    <th>Closing Stock</th>
                </tr>
            </thead>
        </table>
        
        <div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <?
                $i=1;
				$total_receive=$total_issue_return=$total_trans_in=$all_recv_trans_total=$total_issue=$total_trans_out=$all_issue_trans_total=$total_stock=$total_recv_balance=$total_issue_balance=$total_req_qnty=$total_no_of_roll=$total_opening=0;
				foreach ($dataArr as $buyer_id=>$row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$opening = $row["opening_rcv"]-$row["opening_issue"];
					$runtime_stock=$row["receive"]-$row["issue"];
					$closing_stock=$opening+$runtime_stock;
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="50" align="center" title=""><? echo $i;?></td>
						<td width="150" title="<? echo $buyer_id;?>" style="word-break:break-all"><? echo $buyer_arr[$buyer_id];?></td>
						<td width="100" align="right" title=""><? echo number_format($opening,2); ?></td>
						<td width="100" align="right"><? echo number_format($row["receive"],2); ?></td>
						<td width="100" align="right"><? echo number_format($row["issue"],2); ?></td>
						<td width="100" align="right"><? echo number_format($row["tot_receive"],2); ?></td>
						<td width="100" align="right"><? echo number_format($row["tot_issue"],2); ?></td>
						<td align="right"><? echo number_format($closing_stock,2); ?></td>
					</tr>
					<?
					$i++;
					$total_opening+=$opening;
					$total_receive+=$row["receive"];
					$total_issue+=$row["issue"];
					$total_tot_receive+=$row["tot_receive"];
					$total_tot_issue+=$row["tot_issue"];
					$total_closing_stock+=$closing_stock;
				}
                ?>
            </table>
            </div>
            <table width="<? echo $width; ?>" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
                <tfoot>
                    <th width="50"></th>
                    <th width="150">Total:</th>
                    <th width="100" align="right" id="value_total_opening"><? echo number_format($total_opening,2,".",""); ?></th>
                    <th width="100" align="right" id="value_total_receive"><? echo number_format($total_receive,2,".",""); ?></th>
                    <th width="100" align="right" id="value_total_issue"><? echo number_format($total_issue,2,".",""); ?></th>
                    <th width="100" align="right" id="value_total_tot_receive"><? echo number_format($total_tot_receive,2,".",""); ?></th>
                    <th width="100" align="right" id="value_total_tot_issue"><? echo number_format($total_tot_issue,2,".",""); ?></th>
                    <th align="right"  id="value_total_closing_stock"><? echo number_format($total_closing_stock,2,".",""); ?></th>
                </tfoot>
            </table>
    </fieldset>
    <?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "S";

	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$rpt_type";
	exit();
}

if($action=="roll_details_popup")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$user_arr=return_library_array("select id, user_name from user_passwd","id","user_name");
	//list($po_ids,$con,$range,$count,$comp,$brand,$lot,$stitch,$gsm,$stock,$color_id,$barcode_nos,$booking_ids,$fabrication_id)=explode("_",$data);
	$barcode=trim(str_replace("'","",$barcode));
	$po_id=trim(str_replace("'","",$po_id));
	$store_id=trim(str_replace("'","",$store_id));
	$popup_type=trim(str_replace("'","",$popup_type));
	//echo $store_id;die;
	if($barcode=="")
	{
		echo "No Data Found";die;
	}
	//echo "$barcode = $po_id = $popup_type"; die;
	$date_frm=$_SESSION["date_frm"];
	?>
	<script>
		var tableFilters = {

			col_operation: {
				id: ["value_grey_qty"],
				col: [7],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			var tbl_list_search_1 = document.getElementById("tbl_list_search_1");
			setFilterGrid('tbl_list_search_1',-1,tableFilters);
		});
	</script>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<div>
		<table cellpadding="0" width="790" class="rpt_table" rules="all" border="1">
			<thead>
				<tr>
					<th width="30" class="wrd_brk">SL</th>
					<th class="wrd_brk">System ID</th>
					<th width="65" class="wrd_brk">Transaction Date</th>
                    <th width="80" class="wrd_brk">Transaction Type</th>
					<th width="120" class="wrd_brk">Store Name</th>
					<th width="80" class="wrd_brk">Roll No</th>
					<th width="80" class="wrd_brk">Barcode No</th>
					<th width="80" class="wrd_brk">Qty</th>
                    <th class="wrd_brk">User</th>
				</tr>
			</thead>
		</table>
		<div style="width:790px; max-height:320px; overflow-y:scroll;">
			<table cellpadding="0" width="770" class="rpt_table" rules="all" border="1" id="tbl_list_search_1">
				<?
				$orderWiseData=array();
				$total_transfer=0;
				$i=0; $tot_grey_qnty=0; $y=0;
				$store_conds="";
				if($store_id!="") $store_conds=" and E.STORE_ID IN($store_id)";
				if($store_id!="") $store_conds_transfer=" and B.TO_STORE IN($store_id)";
				if($popup_type==1 || $popup_type==3 || $popup_type==5)
				{
					$sql="SELECT A.ID, A.RECV_NUMBER, E.TRANSACTION_DATE, E.STORE_ID, E.TRANSACTION_TYPE, C.ROLL_NO, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, C.INSERTED_BY, A.ENTRY_FORM, C.PO_BREAKDOWN_ID as DTLS_FROM_ORDER, 0 as FROM_BOOKING_WITHOUT_ORDER, 1 AS TYPE
					from inv_receive_master A, inv_transaction E, pro_grey_prod_entry_dtls B, pro_roll_details C
					where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and A.entry_form in(2,22,58,84) and B.trans_id<>0 and C.entry_form in(2,22,58,84) and A.status_active=1 and B.status_active=1 and C.status_active=1 and E.status_active=1 and C.booking_without_order=0 and E.TRANSACTION_TYPE in(1,4) and C.RE_TRANSFER=0 and C.PO_BREAKDOWN_ID=$po_id and C.BARCODE_NO in($barcode) $store_conds
					union all
					select A.ID, A.TRANSFER_SYSTEM_ID as RECV_NUMBER, A.TRANSFER_DATE as TRANSACTION_DATE, B.TO_STORE as STORE_ID, 5 as TRANSACTION_TYPE, C.ROLL_NO, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, C.INSERTED_BY, A.ENTRY_FORM, B.from_order_id as DTLS_FROM_ORDER, B.FROM_BOOKING_WITHOUT_ORDER, 2 AS TYPE
					from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C
					where A.id=B.mst_id and B.id=C.dtls_id and A.entry_form in(82,83,183) and C.entry_form in(82,83,183) and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 and C.PO_BREAKDOWN_ID=$po_id and C.BARCODE_NO in($barcode) $store_conds_transfer";
				}
				else
				{
					$sql="SELECT A.ID, A.ISSUE_NUMBER as RECV_NUMBER, E.TRANSACTION_DATE, E.STORE_ID, E.TRANSACTION_TYPE, C.ROLL_NO, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, C.INSERTED_BY, A.ENTRY_FORM, C.PO_BREAKDOWN_ID as DTLS_FROM_ORDER, 0 as FROM_BOOKING_WITHOUT_ORDER, 3 AS TYPE
					from inv_issue_master A, inv_transaction E, inv_grey_fabric_issue_dtls B, pro_roll_details C
					where A.id=E.mst_id and E.id=B.trans_id and B.id=C.dtls_id and A.entry_form=61 and C.entry_form=61 and C.status_active=1 and C.is_deleted=0 and C.booking_without_order = 0 and E.transaction_type=2 and C.IS_RETURNED=0 and C.PO_BREAKDOWN_ID=$po_id and C.BARCODE_NO in($barcode) $store_conds" ;
				}
				//echo $sql;die;
				$tot_qnty=$i=0;
				$result= sql_select($sql);
				
				if($popup_type==3 || $popup_type==4)
				{
					foreach($result as $row)
					{
						if( $row['ENTRY_FORM'] ==183 || ( $row['ENTRY_FORM'] ==82 && $row['FROM_BOOKING_WITHOUT_ORDER'] == 1))
						{
							if($row['DTLS_FROM_ORDER']) $non_order_arr[$row['DTLS_FROM_ORDER']] = $row['DTLS_FROM_ORDER'];
						}
						else
						{
							if($row['DTLS_FROM_ORDER']) $po_no_arr[$row['DTLS_FROM_ORDER']] = $row['DTLS_FROM_ORDER'];
						}
						if($row['PO_BREAKDOWN_ID']) $po_no_arr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];

						$barcode_po[$row['BARCODE_NO']]['from'] = $row['DTLS_FROM_ORDER'];
						$barcode_po[$row['BARCODE_NO']]['to'] = $row['PO_BREAKDOWN_ID'];
					}
					//echo "<pre>";print_r($barcode_po);die;
					if(!empty($po_no_arr))
					{
						$booking_sql = sql_select('SELECT PO_BREAK_DOWN_ID, BOOKING_NO from wo_booking_dtls where booking_type=1 and po_break_down_id in ('.implode(",", $po_no_arr).') and status_active=1 and is_deleted=0 group by PO_BREAK_DOWN_ID, BOOKING_NO');
						
						foreach ($booking_sql as $val) 
						{
							if($book_no_check[$val['PO_BREAK_DOWN_ID']][$val['BOOKING_NO']]=="")
							{
								$book_no_check[$val['PO_BREAK_DOWN_ID']][$val['BOOKING_NO']]=$val['BOOKING_NO'];
								$po_book_arr[$val['PO_BREAK_DOWN_ID']] .= $val['BOOKING_NO'].",";
							}
						}
					}
					if(!empty($non_order_arr))
					{
						$booking_sql = sql_select('SELECT id as po_break_down_id, BOOKING_NO from wo_non_ord_samp_booking_dtls where  id in ('.implode(",", $non_order_arr).') and status_active=1 and is_deleted=0');
						
						foreach ($booking_sql as $val) 
						{
							if($nonOrd_book_no_check[$val['PO_BREAK_DOWN_ID']][$val['BOOKING_NO']]=="")
							{
								$nonOrd_book_no_check[$val['PO_BREAK_DOWN_ID']][$val['BOOKING_NO']]=$val['BOOKING_NO'];
								$no_book_arr[$val['PO_BREAK_DOWN_ID']] .= $val['BOOKING_NO'].",";
							}
						}
					}
				}
				
				if($popup_type==4)
				{
					$sql_transfer="select A.ID, A.TRANSFER_SYSTEM_ID as RECV_NUMBER, A.TRANSFER_DATE as TRANSACTION_DATE, B.TO_STORE as STORE_ID, 5 as TRANSACTION_TYPE, C.ROLL_NO, C.BARCODE_NO, C.PO_BREAKDOWN_ID, C.QNTY, C.INSERTED_BY, A.ENTRY_FORM, B.from_order_id as DTLS_FROM_ORDER, B.FROM_BOOKING_WITHOUT_ORDER, 2 AS TYPE
					from inv_item_transfer_mst A, inv_item_transfer_dtls B, pro_roll_details C
					where A.id=B.mst_id and B.id=C.dtls_id and A.entry_form in(82,83,183) and C.entry_form in(82,83,183) and A.status_active=1 and B.status_active=1 and C.status_active=1 and C.RE_TRANSFER=0 and C.PO_BREAKDOWN_ID=$po_id and C.BARCODE_NO in($barcode) $store_conds_transfer";
					//echo $sql_transfer;
					$sql_transfer_result=sql_select($sql_transfer);
					
					//echo "<pre>";print_r($po_book_arr);die;
					foreach($sql_transfer_result as $row)
					{
						$tranfer_order[$row['DTLS_FROM_ORDER']]=$row['DTLS_FROM_ORDER'];
					}
					
					$tansf_booking_sql = sql_select('SELECT PO_BREAK_DOWN_ID, BOOKING_NO, IS_SHORT from wo_booking_dtls where booking_type=1 and po_break_down_id in ('.implode(",", $tranfer_order).') and status_active=1 and is_deleted=0 group by PO_BREAK_DOWN_ID, BOOKING_NO, IS_SHORT');
						
					foreach ($tansf_booking_sql as $val) 
					{
						if($trans_book_no_check[$val['PO_BREAK_DOWN_ID']][$val['BOOKING_NO']]=="")
						{
							$trans_book_no_check[$val['PO_BREAK_DOWN_ID']][$val['BOOKING_NO']]=$val['BOOKING_NO'];
							if($val['IS_SHORT']==1)
							{
								$trans_po_book_arr[$val['PO_BREAK_DOWN_ID']] .= $val['BOOKING_NO']."(Main),";
							}
							else
							{
								$trans_po_book_arr[$val['PO_BREAK_DOWN_ID']] .= $val['BOOKING_NO']."(Short),";
							}
							
						}
					}
					//echo "<pre>";print_r($trans_po_book_arr);die;
					$transfer_book_arr=array();
					foreach($sql_transfer_result as $row)
					{
						$from_booking_no = chop($trans_po_book_arr[$row['DTLS_FROM_ORDER']],",");
						$transfer_book_arr[$row['BARCODE_NO']]["booking_no"]=$from_booking_no;
						$transfer_book_arr[$row['BARCODE_NO']]["qnty"]=$row["QNTY"];
					}
				}
				
				foreach($result as $row)
				{
					//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
					//if($transaction_date < $date_frm)continue;
					
					if($popup_type==3 || $popup_type==4)
					{
						if( $row['ENTRY_FORM'] ==183 || ( $row['ENTRY_FORM'] ==82 && $row['FROM_BOOKING_WITHOUT_ORDER'] == 1))
						{
							$from_booking_no = chop($no_book_arr[$barcode_po[$row['BARCODE_NO']]['from']],",");
						}else{
							$from_booking_no = chop($po_book_arr[$barcode_po[$row['BARCODE_NO']]['from']],",");
						}

						$to_booking_no = chop($po_book_arr[$barcode_po[$row['BARCODE_NO']]['to']],",");
						
						
						//receive and issue tooltip "Booking =" and transfer From Booking, To Booking
						if( $row['TYPE'] ==1 || $popup_type==4)
						{
							$tooltip_str = "Booking =".$from_booking_no;
						}
						else{
							$tooltip_str = "From Booking =".$from_booking_no." \n \nTo Booking =".$to_booking_no;
						}
					}
					
					$i++;
					$store_name = $storeNameArr[$row["STORE_ID"]];
					if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" width="30" class="wrd_brk"><? echo $i; ?></td>
						<td align="center" width="120" class="wrd_brk"><? echo $row["RECV_NUMBER"]; ?>&nbsp;</td>
						<td align="center" width="65" class="wrd_brk"><? echo change_date_format($row["TRANSACTION_DATE"]); ?></td>
						<td align="center" width="80" class="wrd_brk"><? echo $transaction_type[$row["TRANSACTION_TYPE"]];?>&nbsp;</td>
						<td align="center" width="120" class="wrd_brk"><? echo $store_name; ?>&nbsp;</td>
						<td align="center" width="80" class="wrd_brk"><? echo $row["ROLL_NO"]; ?>&nbsp;</td>
						<td align="center" width="80" class="wrd_brk"><? echo $row["BARCODE_NO"]; ?>&nbsp;</td>
						<td width="80" class="wrd_brk" align="right" title="<? echo $tooltip_str;?>"><? echo number_format($row["QNTY"],2); ?></td>
                        <td class="wrd_brk" align="center"><? echo $user_arr[$row["INSERTED_BY"]]; ?></td>
					</tr>
					<?
					$tot_qnty += $row["QNTY"];
				}
				?>
			</table>
		</div>
		<table cellpadding="0" width="770" class="rpt_table" rules="all" border="1">
			<tfoot>
				<tr bgcolor="#CCCCCC">
					<td width="30" class="wrd_brk">&nbsp;</td>
					<td width="120" class="wrd_brk">&nbsp;</td>
					<td width="65" class="wrd_brk">&nbsp;</td>
					<td width="80" class="wrd_brk">&nbsp;</td>
					<td width="120" class="wrd_brk center">&nbsp;</td>
					<td width="80" class="wrd_brk center">&nbsp;</td>
					<td width="80" class="wrd_brk" align="right"><strong>Total:</strong></td>
					<td width="80" id="value_grey_qty" align="right" style="font-size:13px; font-weight:bold;"><strong><? echo number_format($tot_qnty,2); ?></strong></td>
                    <td class="wrd_brk">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
        
	</div>
	<?
	if($popup_type==4)
	{
		?>
        <p style="font-size:14; font-weight:bold; color:red; margin-top:10px;">Transfer Information(Don't Calculate With The Issue Qnty)</p>
        <table cellpadding="0" width="770" class="rpt_table" rules="all" border="1" style="margin-top:5px;">
			<thead>
				<tr bgcolor="#CCCCCC">
					<th width="30">SL</th>
					<th width="80">Barcode No</th>
					<th>Booking No</th>
					<th width="80">Qnty</th>
				</tr>
			</thead>
            <tbody>
            	<?
				$i=1;
				foreach($transfer_book_arr as $bar_code_no=>$val)
				{
					if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td align="center"><?= $i;?></td>
                        <td align="center" class="wrd_brk"><?= $bar_code_no;?></td>
                        <td class="wrd_brk"><?= $val["booking_no"];?></td>
                        <td align="right" class="wrd_brk"><?= $val["qnty"];?></td>
                    </tr>
                    <?
					$i++;
				}
				?>
            </tbody>
		</table>
        <?
	}
	exit();
}

if($action=="recv_popup")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	list($po_ids,$con,$range,$count,$comp,$brand,$lot,$stitch,$gsm,$stock,$color_id,$barcode_nos,$booking_ids,$fabrication_id)=explode("_",$data);

	//echo "string"; die;
	$date_frm=$_SESSION["date_frm"];
	?>
	<script>
		var tableFilters = {

			col_operation: {
				id: ["value_grey_qty"],
				col: [4],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			var tbl_list_search_1 = document.getElementById("tbl_list_search_1");
			var tbl_list_search_2 = document.getElementById("tbl_list_search_2");
			var tbl_list_search_3 = document.getElementById("tbl_list_search_3");
			if(tbl_list_search_1){
				setFilterGrid('tbl_list_search_1',-1,tableFilters);
			}
			if(tbl_list_search_2){
				setFilterGrid('tbl_list_search_2',-1,tableFilters);
			}
			if(tbl_list_search_3){
				setFilterGrid('tbl_list_search_3',-1,tableFilters);
			}
		});
	</script>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<div>
		<table cellpadding="0" width="720" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="11" class="wrd_brk">Receive Details</th></tr>
				<tr>
					<th width="30" class="wrd_brk">SL</th>
					<th width="60" class="wrd_brk">Receive <br>ID</th>
					<th width="100" class="wrd_brk">Receive Type</th>
					<th width="100" class="wrd_brk">Recv From <br>Order</th>
					<th width="120" class="wrd_brk">Store Name</th>
					<th width="80" class="wrd_brk">Roll No</th>
					<th width="80" class="wrd_brk">Barcode No</th>
					<th width="60" class="wrd_brk">Room</th>
					<th width="60" class="wrd_brk">Rack</th>
					<th width="60" class="wrd_brk">Self</th>
					<th width="50" class="wrd_brk">Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:738px; max-height:250px; overflow-y:scroll;">
			<table cellpadding="0" width="720" class="rpt_table" rules="all" border="1" id="tbl_list_search_1">
				<?
				$orderWiseData=array();
				$total_transfer=0;
				$i=0; $tot_grey_qnty=0; $y=0;


				$sql="SELECT e.transaction_date, a.recv_number_prefix_num as r_id,b.room, b.rack, b.self, a.receive_basis, c.roll_no, c.qnty, e.store_id, c.barcode_no from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details c where   a.entry_form in(2,22,58) and a.id=e.mst_id and e.id=b.trans_id and b.id=c.dtls_id and b.trans_id<>0 and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and c.booking_without_order=0 and b.order_id='$po_ids' and b.color_id='$color_id' and b.color_range_id='$range' and c.barcode_no in($barcode_nos)  and  b.yarn_count='$count' and a.booking_id in($booking_ids) and b.febric_description_id='$fabrication_id' group by e.transaction_date,a.recv_number_prefix_num ,b.room, b.rack, b.self, a.receive_basis, c.roll_no, c.qnty, e.store_id, c.barcode_no" ;// and b.brand_id='$brand'


				$tot_qnty=0;
				$result= sql_select($sql);

				foreach($result as $row)
				{
					$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
					if($transaction_date < $date_frm)continue;

					$i++;
					$store_name = "";
					foreach (explode(",",$row[csf("store_id")]) as $store) {
						$store_name .= $storeNameArr[$store].",";
					}
					$store_name = rtrim($store_name,", ");

					if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" width="30" class="wrd_brk"><? echo $i; ?></td>
						<td align="center" width="60" class="wrd_brk"><? echo $row[csf("r_id")]; ?>&nbsp;</td>
						<td align="center" width="100" class="wrd_brk"><? echo "Receive"; ?></td>
						<td  align="center" width="100" class="wrd_brk"><? //echo $store_name;?>&nbsp;</td>

						<td align="center" width="120" class="wrd_brk"><? echo $store_name; ?>&nbsp;</td>
						<td align="center" width="80" class="wrd_brk"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
						<td align="center" width="80" class="wrd_brk"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
						<td align="center" width="60" class="wrd_brk"><? echo $row[csf('room')]; ?></td>
						<td align="center" width="60" class="wrd_brk"><? echo $row[csf('rack')]; ?></td>
						<td align="center" width="60" class="wrd_brk"><? echo $row[csf('self')] ; ?></td>
						<td width="50" class="wrd_brk" align="center"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$tot_qnty += $row[csf('qnty')];
					$y++;

				}

				?>
			</table>

		</div>
		<table cellpadding="0" width="720" class="rpt_table" rules="all" border="1">
			<tfoot>
				<tr>
					<td width="30" class="wrd_brk"></td>
					<td width="60" class="wrd_brk">&nbsp; </td>
					<td width="100" class="wrd_brk"> </td>
					<td width="100" class="wrd_brk"> </td>
					<td width="120" class="wrd_brk center"> </td>
					<td width="80" class="wrd_brk center"> </td>
					<td width="80" class="wrd_brk center"> </td>
					<td width="60" class="wrd_brk right"> </td>
					<td width="60" class="wrd_brk right"> </td>
					<td width="60" class="wrd_brk right"><strong>Total=</strong> </td>
					<td width="50" class="wrd_brk" align="right"><strong><? echo number_format($tot_qnty,2); ?></strong></td>
				</tr>
			</tfoot>
		</table>
	</div>


	<?
	exit();
}
if($action=="transfer_popup")
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	list($po_ids,$con,$range,$count,$comp,$brand,$lot,$stitch,$gsm,$stock,$color_id,$barcode_nos,$booking_ids,$fabrication_id,$store_ids,$type,$qnty,$machine_dia)=explode("_",$data);
	//echo "string"; die;
	$date_frm=$_SESSION["date_frm"];
	$msg=($type==1)? "Transfer In":"Transfer Out";
	$span=($type==1)? "12":"11";

	if($qnty=="0.00" || $qnty<0)die;
	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_grey_qty"],
				col: [4],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			var tbl_list_search_1 = document.getElementById("tbl_list_search_1");
			var tbl_list_search_2 = document.getElementById("tbl_list_search_2");
			var tbl_list_search_3 = document.getElementById("tbl_list_search_3");
			if(tbl_list_search_1){
				setFilterGrid('tbl_list_search_1',-1,tableFilters);
			}
			if(tbl_list_search_2){
				setFilterGrid('tbl_list_search_2',-1,tableFilters);
			}
			if(tbl_list_search_3){
				setFilterGrid('tbl_list_search_3',-1,tableFilters);
			}
		});
	</script>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<div>
		<table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="13" class="wrd_brk"><? echo $msg;?> Details</th></tr>
				<tr>
					<th width="30" class="wrd_brk">SL</th>
					<th width="60" class="wrd_brk">System <br>ID</th>
					<th width="100" class="wrd_brk">Date</th>
					<th width="100" class="wrd_brk">Transfer Criteria</th>
					<?
					if($type==1)
					{
						?>
						<th width="100" class="wrd_brk">Recv From <br>Order/ Store</th>
						<th width="100" class="wrd_brk">From<br> Booking</th>

						<?
					}
					else
					{
						?>
						<th width="100" class="wrd_brk">Send To<br>Order</th>
						<th width="100" class="wrd_brk">To<br> Booking</th>

						<?
					}
					?>
					<th width="120" class="wrd_brk">Store Name</th>
					<th width="100" class="wrd_brk">Barcode No</th>
					<th width="80" class="wrd_brk">Roll No</th>
					<th width="60" class="wrd_brk">Room</th>
					<th width="60" class="wrd_brk">Rack</th>
					<th width="60" class="wrd_brk">Self</th>
					<th width="50" class="wrd_brk">Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:840px; max-height:250px; overflow-y:scroll;">
			<table cellpadding="0" width="820" class="rpt_table" rules="all" border="1" id="tbl_list_search_1">
				<?
				$orderWiseData=array();
				$total_transfer=0;
				$i=0; $tot_grey_qnty=0; $y=0;
				$store_cond = ($store_ids!="")?"and b.to_store in($store_ids)":"";
				$store_cond2 = ($store_ids!="")?"and b.from_store in($store_ids)":"";

				$production_sql = "SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,a.brand_id,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg, a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from inv_receive_master c, pro_grey_prod_entry_dtls a,pro_roll_details b where c.id=a.mst_id and a.id=b.dtls_id and c.entry_form = 2 and b.entry_form in (2) and a.status_active=1 and b.status_active=1 and b.barcode_no in($barcode_nos) and a.color_id = '$color_id' and a.febric_description_id = '$fabrication_id' and a.gsm = '$gsm' and a.machine_dia = '$machine_dia' and a.stitch_length = '$stitch' and a.color_range_id = '$range' and a.yarn_lot = '$lot'";

				$production_info = sql_select($production_sql);
				foreach ($production_info as $row)
				{
					$prodBarcodeData[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
				}

				$barcode_nos = implode(",",$prodBarcodeData);
				if($type==1)
				{
					$sql="SELECT b.batch_id, b.from_order_id, b.from_store, a.transfer_date as transaction_date , a.transfer_prefix_number as sys_num,a.transfer_criteria,b.to_store store_id,c.roll_no,c.qnty,c.re_transfer,c.barcode_no from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
					where  a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) $store_cond and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1
					union all	SELECT  b.batch_id, b.from_order_id, b.from_store,a.transfer_date as transaction_date ,a.transfer_prefix_number as sys_num,a.transfer_criteria,b.to_store store_id,c.roll_no,c.qnty,c.re_transfer,c.barcode_no
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
					where   a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(82) and c.entry_form in(82)  and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1
					union all
					SELECT  b.batch_id,b.from_order_id, b.from_store, a.transfer_date as transaction_date ,a.transfer_prefix_number as sys_num,a.transfer_criteria,b.to_store store_id,c.roll_no,c.qnty,c.re_transfer,c.barcode_no
					from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
					where   a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) $store_cond and c.booking_without_order=0 and a.status_active=1 and b.status_active=1 and c.status_active=1";

				}
				else if($type==2)
				{
					$sql="SELECT b.to_order_id, a.transfer_date as transaction_date ,a.transfer_prefix_number as sys_num,a.transfer_criteria,b.to_store store_id,c.roll_no,c.qnty,c.re_transfer,c.barcode_no
					from order_wise_pro_details d, inv_item_transfer_dtls b, pro_roll_details c, inv_item_transfer_mst a
					where d.trans_id=b.trans_id and b.id=c.dtls_id and c.mst_id=a.id and a.entry_form=83 and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.trans_type=6 and c.barcode_no in($barcode_nos) $store_cond2 and c.booking_without_order = 0
					union all
					select   b.to_order_id,a.transfer_date as transaction_date ,a.transfer_prefix_number as sys_num,a.transfer_criteria,b.to_store store_id,c.roll_no,c.qnty,c.re_transfer,c.barcode_no
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
					where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.barcode_no in($barcode_nos) $store_cond2
					and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0
					union all
					select b.to_order_id, a.transfer_date as transaction_date , a.transfer_prefix_number as sys_num,a.transfer_criteria,b.to_store store_id,c.roll_no,c.qnty,c.re_transfer,c.barcode_no
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
					where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.barcode_no in($barcode_nos) $store_cond2
					and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1  ";
				}

				$tot_qnty=$tot_qnty_re=0;
				$result= sql_select($sql);
				$po_ids_arr=array();
				foreach($result as $vals)
				{
					if($vals[csf("from_order_id")])$po_ids_arr[$vals[csf("from_order_id")]]=$vals[csf("from_order_id")];
					if($vals[csf("to_order_id")])$po_ids_arr[$vals[csf("to_order_id")]]=$vals[csf("to_order_id")];
				}

				$all_pos=implode(",",$po_ids_arr);
				$po_arr=return_library_array( "SELECT id, po_number from wo_po_break_down where id in($all_pos)", "id", "po_number");
				$booking_arr=return_library_array( "SELECT po_break_down_id, booking_no from wo_booking_dtls where booking_type=1 and  po_break_down_id in($all_pos)", "po_break_down_id", "booking_no");

				foreach($result as $row)
				{
					if($row[csf('re_transfer')]==0){
						$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						if($transaction_date < $date_frm)continue;

						$i++;
						$store_name = "";
						foreach (explode(",",$row[csf("store_id")]) as $store) {
							$store_name .= $storeNameArr[$store].",";
						}
						$store_name = rtrim($store_name,", ");

						$store_name_from = "";
						if($row[csf("transfer_criteria")]==1)
						{
							$store_name_from =$po_arr[$row[csf("from_order_id")]];
						}
						else
						{
							foreach (explode(",",$row[csf("from_store")]) as $store) {
								$store_name_from .= $storeNameArr[$store].",";
							}
							$store_name_from = rtrim($store_name_from,", ");
						}

						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center" width="30" class="wrd_brk"><? echo $i; ?></td>
							<td align="center" width="60" class="wrd_brk"><? echo $row[csf("sys_num")]; ?>&nbsp;</td>
							<td align="center" width="100" class="wrd_brk"><? echo  change_date_format($row[csf("transaction_date")]); ?>&nbsp;</td>
							<td align="center" width="100" class="wrd_brk"><? echo $item_transfer_criteria[$row[csf("transfer_criteria")]]; ?></td>
							<?
							if($type==1)
							{
								?>
								<td  align="center" width="100" class="wrd_brk"><? echo $store_name_from;?>&nbsp;</td>
								<td  align="center" width="100" class="wrd_brk"><? echo $booking_arr[$row[csf("from_order_id")]];?>&nbsp;</td>

								<?
							}
							else
							{
								?>
								<td  align="center" width="100" class="wrd_brk"><? echo $po_arr[$row[csf("to_order_id")]];?>&nbsp;</td>
								<td  align="center" width="100" class="wrd_brk"><? echo $booking_arr[$row[csf("to_order_id")]];?>&nbsp;</td>

								<?
							}
							?>
							<td align="center" width="120" class="wrd_brk"><? echo $store_name; ?></td>
							<td align="center" width="100" class="wrd_brk"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
							<td align="center" width="80" class="wrd_brk"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
							<td align="center" width="60" class="wrd_brk"><? echo $row[csf('room')]; ?></td>
							<td align="center" width="60" class="wrd_brk"><? echo $row[csf('rack')]; ?></td>
							<td align="center" width="60" class="wrd_brk"><? echo $row[csf('self')] ; ?></td>
							<td width="50" class="wrd_brk" align="center"><? echo number_format($row[csf('qnty')],2); ?></td>

						</tr>
						<?
						$tot_qnty += $row[csf('qnty')];
						$y++;
					}
				}
				?>
			</table>
		</div>
		<table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
			<tr>
				<td width="30" class="wrd_brk"></td>
				<td width="60" class="wrd_brk"></td>
				<td width="100" class="wrd_brk"> </td>
				<td width="100" class="wrd_brk"> </td>
				<td width="100" class="wrd_brk"></td>
				<td width="100" class="wrd_brk"> </td>
				<td width="120" class="wrd_brk center"> </td>
				<td width="100" class="wrd_brk center"> </td>
				<td width="80" class="wrd_brk center"> </td>
				<td width="60" class="wrd_brk right"> </td>
				<td width="60" class="wrd_brk right"> </td>
				<td width="60" class="wrd_brk right"><strong>Total=</strong> </td>
				<td width="50" class="wrd_brk" align="right"><strong><? echo number_format($tot_qnty,2); ?></strong></td>
			</tr>
		</table>
		<br />
		<table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="13" class="wrd_brk">Re <? echo $msg;?> Details</th></tr>
				<tr>
					<th width="30" class="wrd_brk">SL</th>
					<th width="60" class="wrd_brk">System <br>ID</th>
					<th width="100" class="wrd_brk">Date</th>
					<th width="100" class="wrd_brk">Transfer Criteria</th>
					<?
					if($type==1)
					{
						?>
						<th width="100" class="wrd_brk">Recv From <br>Order/ Store</th>
						<th width="100" class="wrd_brk">From<br> Booking</th>
						<?
					}
					else
					{
						?>
						<th width="100" class="wrd_brk">Send To<br>Order</th>
						<th width="100" class="wrd_brk">To<br> Booking</th>

						<?
					}
					?>
					<th width="120" class="wrd_brk">Store Name</th>
					<th width="100" class="wrd_brk">Barcode No</th>
					<th width="80" class="wrd_brk">Roll No</th>
					<th width="60" class="wrd_brk">Room</th>
					<th width="60" class="wrd_brk">Rack</th>
					<th width="60" class="wrd_brk">Self</th>
					<th width="50" class="wrd_brk">Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:838px; max-height:250px; overflow-y:scroll;">
			<table cellpadding="0" width="820" class="rpt_table" rules="all" border="1" id="tbl_list_search_1">
				<?
				foreach($result as $row)
				{
					if($row[csf('re_transfer')]==1){
						$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						if($transaction_date < $date_frm)continue;

						$i++;
						$store_name = "";
						foreach (explode(",",$row[csf("store_id")]) as $store) {
							$store_name .= $storeNameArr[$store].",";
						}
						$store_name = rtrim($store_name,", ");

						$store_name_from = "";
						if($row[csf("transfer_criteria")]==1)
						{
							$store_name_from =$po_arr[$row[csf("from_order_id")]];
						}
						else
						{
							foreach (explode(",",$row[csf("from_store")]) as $store) {
								$store_name_from .= $storeNameArr[$store].",";
							}
							$store_name_from = rtrim($store_name_from,", ");
						}

						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center" width="30" class="wrd_brk"><? echo $i; ?></td>
							<td align="center" width="60" class="wrd_brk"><? echo $row[csf("sys_num")]; ?>&nbsp;</td>
							<td align="center" width="100" class="wrd_brk"><? echo  change_date_format($row[csf("transaction_date")]); ?>&nbsp;</td>
							<td align="center" width="100" class="wrd_brk"><? echo $item_transfer_criteria[$row[csf("transfer_criteria")]]; ?></td>
							<?
							if($type==1)
							{
								?>
								<td  align="center" width="100" class="wrd_brk"><? echo $store_name_from;?>&nbsp;</td>
								<td  align="center" width="100" class="wrd_brk"><? echo $booking_arr[$row[csf("from_order_id")]];?>&nbsp;</td>

								<?
							}
							else
							{
								?>
								<td  align="center" width="100" class="wrd_brk"><? echo $po_arr[$row[csf("to_order_id")]];?>&nbsp;</td>
								<td  align="center" width="100" class="wrd_brk"><? echo $booking_arr[$row[csf("to_order_id")]];?>&nbsp;</td>

								<?
							}
							?>
							<td align="center" width="120" class="wrd_brk"><? echo $store_name; ?>&nbsp;</td>
							<td align="center" width="100" class="wrd_brk"><? echo $row[csf('barcode_no')]; ?>&nbsp;</td>
							<td align="center" width="80" class="wrd_brk"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
							<td align="center" width="60" class="wrd_brk"><? echo $row[csf('room')]; ?></td>
							<td align="center" width="60" class="wrd_brk"><? echo $row[csf('rack')]; ?></td>
							<td align="center" width="60" class="wrd_brk"><? echo $row[csf('self')] ; ?></td>
							<td width="50" class="wrd_brk" align="center"><? echo number_format($row[csf('qnty')],2); ?></td>
						</tr>
						<?
						$tot_qnty_re += $row[csf('qnty')];
						$y++;
					}
				}
				?>
			</table>
		</div>
		<table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
			<tfoot>
				<tr>
					<td width="30" class="wrd_brk"></td>
					<td width="60" class="wrd_brk"></td>
					<td width="100" class="wrd_brk"> </td>
					<td width="100" class="wrd_brk"> </td>
					<td width="100" class="wrd_brk"></td>
					<td width="100" class="wrd_brk"> </td>
					<td width="120" class="wrd_brk center"> </td>
					<td width="100" class="wrd_brk center"> </td>
					<td width="80" class="wrd_brk center"> </td>
					<td width="60" class="wrd_brk right"> </td>
					<td width="60" class="wrd_brk right"> </td>
					<td width="60" class="wrd_brk right"><strong>Total=</strong> </td>
					<td width="50" class="wrd_brk" align="right"><strong><? echo number_format($tot_qnty_re,2); ?></strong></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="iss_popup")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	list($po_ids,$con,$range,$count,$comp,$brand,$lot,$stitch,$gsm,$stock,$color_id,$barcode_nos,$booking_ids,$fabrication_id,$store_ids)=explode("_",$data);
	$date_frm=$_SESSION["date_frm"];
	?>
	<script>
		$(document).ready(function() {
			var tbl_list_search_issue_1 = document.getElementById("tbl_list_search_issue_1");
			var tbl_list_search_issue_2 = document.getElementById("tbl_list_search_issue_2");
			if(tbl_list_search_issue_1){
				setFilterGrid('tbl_list_search_issue_1',-1);
			}
			if(tbl_list_search_issue_2){
				setFilterGrid('tbl_list_search_issue_2',-1);
			}
		});
	</script>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<div>



		<table cellpadding="0" width="720" class="rpt_table" rules="all" border="1">
			<thead>
				<tr><th colspan="10" class="wrd_brk">Issue Details</th></tr>
				<tr>
					<th width="30" class="wrd_brk">SL</th>
					<th width="60" class="wrd_brk">Issue <br>ID</th>
					<th width="100" class="wrd_brk">Issue Type</th>

					<th width="120" class="wrd_brk">Store Name</th>
					<th width="100" class="wrd_brk">Barcode No</th>
					<th width="80" class="wrd_brk">Roll No</th>
					<th width="60" class="wrd_brk">Room</th>
					<th width="60" class="wrd_brk">Rack</th>
					<th width="60" class="wrd_brk">Self</th>
					<th width="50" class="wrd_brk">Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:738px; max-height:250px; overflow-y:scroll;">
			<table cellpadding="0" width="720" class="rpt_table" rules="all" border="1" id="tbl_list_search_1">
				<?
				$orderWiseData=array();
				$total_transfer=0;
				$i=0; $tot_grey_qnty=0; $y=0;
				$store_cond = ($store_ids!="")?" and e.store_id in($store_ids)":"";
				$sql= "SELECT e.transaction_date, a.issue_number_prefix_num as r_id, b.room, b.rack, b.self,c.barcode_no, c.roll_no, c.qnty,  e.store_id
				from inv_issue_master a ,pro_roll_details c,inv_grey_fabric_issue_dtls b,inv_transaction e
				where a.id=e.mst_id and  c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in ($po_ids) and c.barcode_no in($barcode_nos) and b.color_id='$color_id' $store_cond
				and c.booking_without_order = 0 and c.dtls_id=b.id and b.trans_id=e.id and e.transaction_type=2 group by e.transaction_date,a.issue_number_prefix_num , b.room, b.rack, b.self, c.barcode_no, c.roll_no, c.qnty,  e.store_id";

				$tot_qnty=0;
				$result= sql_select($sql);

				foreach($result as $row)
				{
					$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
					if($transaction_date < $date_frm)continue;
					$i++;
					$store_name = "";
					foreach (explode(",",$row[csf("store_id")]) as $store) {
						$store_name .= $storeNameArr[$store].",";
					}
					$store_name = rtrim($store_name,", ");

					if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" width="30" class="wrd_brk"><? echo $i; ?></td>
						<td align="center" width="60" class="wrd_brk"><? echo $row[csf("r_id")]; ?>&nbsp;</td>
						<td align="center" width="100" class="wrd_brk"><? echo "Issue"; ?></td>

						<td align="center" width="120" class="wrd_brk"><? echo $store_name; ?>&nbsp;</td>
						<td align="center" width="100" class="wrd_brk"><? echo $row[csf('barcode_no')]; ?></td>
						<td align="center" width="80" class="wrd_brk"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
						<td align="center" width="60" class="wrd_brk"><? echo $row[csf('room')]; ?></td>
						<td align="center" width="60" class="wrd_brk"><? echo $row[csf('rack')]; ?></td>
						<td align="center" width="60" class="wrd_brk"><? echo $row[csf('self')] ; ?></td>
						<td width="50" class="wrd_brk" align="center"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$tot_qnty += $row[csf('qnty')];
					$y++;

				}

				?>
			</table>

		</div>
		<table cellpadding="0" width="720" class="rpt_table" rules="all" border="1">
			<tfoot>
				<tr>
					<td width="30" class="wrd_brk"></td>
					<td width="60" class="wrd_brk">&nbsp; </td>
					<td width="100" class="wrd_brk"> </td>

					<td width="120" class="wrd_brk center"> </td>
					<td width="100" class="wrd_brk center"> </td>
					<td width="80" class="wrd_brk center"> </td>
					<td width="60" class="wrd_brk right"> </td>
					<td width="60" class="wrd_brk right"> </td>
					<td width="60" class="wrd_brk right"><strong>Total=</strong> </td>
					<td width="50" class="wrd_brk" align="right"><strong><? echo number_format($tot_qnty,2); ?></strong></td>
				</tr>
			</tfoot>
		</table>
	</div>


	<?
	exit();
}

if($action=="stock_popup")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo "test";die;

	//$data=$data0.'_'.$data1.'_'.$data2.'_'.$data3.'_'.$data4.'_'.$data5.'_'.$data6.'_'.$data7.'_'.$data8.'_'.$data9.'_'.$data10.'_'.$data11.'_'.$data12.'_'.$data13.'_'.$data14.'_'.$data15.'_'.$data16.'_'.$data17.'_'.$data18;

	list($po_ids,$con,$range,$count,$comp,$brand,$lot,$stitch,$gsm,$stock,$color_id,$barcode_nos,$booking_ids,$fabrication_id,$store_ids,$type,$qnty,$width,$machine_dia)=explode("__",$data);

	$date_from=$_SESSION["date_from"];
	$date_to=$_SESSION["date_to"];

	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		//$date_cond=" and e.transaction_date <= '$end_date'";
		//$date_cond2=" and a.transfer_date <= '$end_date'";
	}

	//echo $color_id;die;
	if($qnty=="0.00" || $qnty<0)die;


	$production_sql = "select b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,a.brand_id,b.po_breakdown_id,a.prod_id,b.booking_no, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg, a.machine_no_id, c.knitting_source, c.challan_no as production_challan,c.knitting_company, a.yarn_prod_id, a.body_part_id from inv_receive_master c, pro_grey_prod_entry_dtls a,pro_roll_details b where c.id=a.mst_id and a.id=b.dtls_id and c.entry_form = 2 and b.entry_form in (2) and a.status_active=1 and b.status_active=1 and b.barcode_no in($barcode_nos) and a.color_id = '$color_id' and a.febric_description_id = '$fabrication_id' and a.gsm = '$gsm' and a.machine_dia = '$machine_dia' and a.stitch_length = '$stitch' and a.color_range_id = '$range' and a.yarn_lot = '$lot'";
	//echo $production_sql;
	$production_info = sql_select($production_sql);
	foreach ($production_info as $row)
	{
		$prodBarcodeData[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
	}


	$stock_query="SELECT a.entry_form, 1 as type, c.roll_no, c.qnty, e.store_id,e.transaction_date, c.barcode_no
	from inv_receive_master a,inv_transaction e, pro_grey_prod_entry_dtls b, pro_roll_details c
	where  a.entry_form in(2,22,58) and a.id=e.mst_id and e.id=b.trans_id and b.id=c.dtls_id and b.trans_id<>0 and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and c.booking_without_order=0 and c.po_breakdown_id in($po_ids) 
	and c.barcode_no in($barcode_nos) $date_cond
	union all
	select  a.entry_form, 2 as type,c.roll_no, c.qnty,b.to_store store_id,a.transfer_date transaction_date, c.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
	where  a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.po_breakdown_id in($po_ids) 
	and c.barcode_no in($barcode_nos) and C.RE_TRANSFER=0 $date_cond2
	union all
	select  a.entry_form, 2 as type,c.roll_no, c.qnty,b.to_store store_id,a.transfer_date transaction_date, c.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
	where  a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(82) and c.entry_form in(82) and C.RE_TRANSFER=0 and c.status_active=1 and c.is_deleted=0 and c.booking_without_order = 0 and a.status_active=1 and b.status_active=1 and c.status_active=1  and c.po_breakdown_id in($po_ids) 
	and c.barcode_no in($barcode_nos) $date_cond2
	union all
	select  a.entry_form, 2 as type,c.roll_no, c.qnty,b.to_store store_id,a.transfer_date transaction_date, c.barcode_no
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c,product_details_master d
	where  a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(183) and c.entry_form in(183) and c.status_active=1 and c.is_deleted=0  and c.booking_without_order=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.po_breakdown_id in($po_ids) 
	and c.barcode_no in($barcode_nos)  $date_cond2";
	//echo $stock_query;

	$stock_data_array=array();
	$date_frm=$_SESSION["date_frm"];
	foreach(sql_select($stock_query) as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["barcode_no"])
		{
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

			if($transaction_date < $date_frm)
			{
				if($row[csf('entry_form')]== 2 || $row[csf('entry_form')]== 22 || $row[csf('entry_form')]== 58)
				{
					$rollWiseOpeningReceiveArr[$row[csf('roll_no')]] += $row[csf('qnty')];

				}
				else
				{

					$rollWiseOpeningTransInArr[$row[csf('roll_no')]]+= $row[csf('qnty')];

				}
			}
			else
			{
				if($row[csf('entry_form')]== 2 || $row[csf('entry_form')]== 22 || $row[csf('entry_form')]== 58)
				{
					$rollWiseRcvArr[$row[csf('roll_no')]] += $row[csf('qnty')];
				}
				else
				{
					$rollWiseInArr[$row[csf('roll_no')]] += $row[csf('qnty')];

				}
			}

			$stock_data_array[$row[csf('roll_no')]]=$row[csf('store_id')];
			$stock_barcode_data_array[$row[csf('roll_no')]]=$row[csf('barcode_no')];
			$stock_data_array_store[$row[csf('roll_no')]].=','.$row[csf('store_id')];
		}
	}
	//echo "<pre>";print_r($stock_data_array);die;
	//echo "<pre>";print_r($rollWiseOpeningTransInArr);
	//and c.po_breakdown_id in($po_ids)
	$iss_rtn_qty_sql=sql_select("SELECT c.roll_no, e.transaction_date, sum(c.qnty) qnty, c.barcode_no
		from pro_roll_details c,pro_grey_prod_entry_dtls b,inv_transaction e
		where c.entry_form=84 and c.status_active=1 and c.is_deleted=0 and c.dtls_id=b.id and b.trans_id=e.id and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=4 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) group by c.roll_no, e.transaction_date, c.barcode_no ");
	foreach($iss_rtn_qty_sql as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["barcode_no"])
		{
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
			if($transaction_date < $date_frm)
			{
				$rollWiseOpeningReturnArr[$row[csf('roll_no')]]+= $row[csf('qnty')];
			}
			else
			{
				$rollWiseIssue_return_arr[$row[csf('roll_no')]]+= $row[csf('qnty')];
			}
		}
	}

 	//and c.po_breakdown_id in($po_ids)
	//and c.po_breakdown_id in($po_ids)
	//and c.po_breakdown_id in($po_ids)
	//and c.po_breakdown_id in($po_ids)
	
	$issue_sql = "SELECT  c.roll_no,e.transaction_date , c.qnty,c.entry_form, c.barcode_no
	from pro_roll_details c, inv_grey_fabric_issue_dtls b, inv_transaction e
	where c.entry_form=61 and c.status_active=1 and c.is_deleted=0	and c.booking_without_order = 0 and c.dtls_id=b.id and b.trans_id=e.id and e.transaction_type=2 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) $date_cond
	union all
	select c.roll_no, a.transfer_date transaction_date, c.qnty, c.entry_form, c.barcode_no
	from order_wise_pro_details d, inv_item_transfer_dtls b, pro_roll_details c, inv_item_transfer_mst a
	where d.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.trans_type=6 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and c.booking_without_order = 0 and c.re_transfer=0 $date_cond2
	union all
	select c.roll_no,a.transfer_date transaction_date, c.qnty ,c.entry_form	, c.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.booking_without_order = 0 and c.re_transfer=0 $date_cond2
	union all
	select  c.roll_no,a.transfer_date transaction_date, c.qnty, c.entry_form, c.barcode_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 $date_cond2";
	//echo $issue_sql;
	$issue_info=sql_select($issue_sql);
	foreach($issue_info as $row)
	{
		if($prodBarcodeData[$row[csf("barcode_no")]]["barcode_no"])
		{
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
			if($transaction_date < $date_frm)
			{
				if($row[csf('entry_form')]==61)
				{
					$rollWiseOpeningIssueArr[$row[csf('roll_no')]] += $row[csf('qnty')];
				}
				else
				{
					$rollWiseOpeningTransOutArr[$row[csf('roll_no')]]+= $row[csf('qnty')];
				}
			}
			else
			{
				if($row[csf('entry_form')]==61)
				{
					$rollWiseIssue_arr[$row[csf('roll_no')]] += $row[csf('qnty')];
				}
				else
				{
					$rollWiseTrans_out_arr[$row[csf('roll_no')]]+= $row[csf('qnty')];
				}
			}
		}
	}

	$trans_store_arr=return_library_array("select c.barcode_no, s.store_name from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and  c.barcode_no not in(select barcode_no from pro_roll_details where barcode_no in($barcode_nos) and entry_form=82 and status_active=1 and is_deleted=0)
		union all
		select c.barcode_no, s.store_name  from inv_item_transfer_mst a, inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids)
		order by store_name, barcode_no","barcode_no","store_name");

	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_grey_qty"],
				col: [7],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});
	</script>
	<fieldset style="width:720px">


		<table cellpadding="0" width="700" class="rpt_table" rules="all" border="1">
			<thead>
				<th width="40">SL</th>
				<th width="120">Store Name</th>
				<th width="80">Barcode No</th>
				<th width="80">Roll No</th>
				<th width="80">Room</th>
				<th width="80">Rack</th>
				<th width="80">Self</th>
				<th>Qty</th>
			</thead>
		</table>
		<div style="width:700px; max-height:250px; overflow-y:scroll">
			<table cellpadding="0" width="680" class="rpt_table" rules="all" border="1" id="tbl_list_search">
				<?
				$i=0; $tot_stock_qnty=0;
				
				foreach($stock_data_array as $roll_no=>$store_id)
				{

					$opening_receive_qnty 	= $rollWiseOpeningReceiveArr[$roll_no] ;
					$opening_return_qnty 	= $rollWiseOpeningReturnArr[$roll_no] ;
					$opening_trans_in_qnty 	= $rollWiseOpeningTransInArr[$roll_no] ;

					$opening_issue_qnty 	= $rollWiseOpeningIssueArr[$roll_no];
					$opening_trans_out_qnty = $rollWiseOpeningTransOutArr[$roll_no] ;
					$opening = ($opening_receive_qnty+$opening_return_qnty+$opening_trans_in_qnty)-($opening_issue_qnty+$opening_trans_out_qnty);
					$receive_qnty 		= $rollWiseRcvArr[$roll_no];
					$trans_in_qnty 		= $rollWiseInArr[$roll_no];
					$issue_return_qnty	= $rollWiseIssue_return_arr[$roll_no];
					$issue_qnty    		= $rollWiseIssue_arr[$roll_no];
					$trans_out    		= $rollWiseTrans_out_arr[$roll_no];
					$all_receive_qnty	= $receive_qnty + $trans_in_qnty + $issue_return_qnty;
					$all_issue_qnty		= $issue_qnty + $trans_out;
					$stock_qnty 		= ($opening + $all_receive_qnty) - $all_issue_qnty ;

					/*$store_name = "";
					$st_ids=trim($stock_data_array_store[$roll_no],",");
					foreach ( array_unique(explode(",",$st_ids)) as $store)
					{
						$store_name .= $storeNameArr[$store].",";
					}
					$store_name = rtrim($store_name,", ");*/

					$barcode_no = $stock_barcode_data_array[$roll_no];

					if($stock_qnty>0)
					{
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="120"><p><? echo $trans_store_arr[$barcode_no]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $barcode_no; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $roll_no; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $room; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $rack; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $self; ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($stock_qnty,2); ?></td>
						</tr>
						<?
						$tot_stock_qnty+=$stock_qnty;
						$i++;
					}
				}
				?>
			</table>
		</div>
		<table cellpadding="0" width="680" class="rpt_table" rules="all" border="1">
			<tfoot>
				<tr>
					<td width="40"></td>
					<td width="120"></td>
					<td width="80"></td>

					<td width="80" align="center"></td>
					<td width="80" align="center"></td>
					<td width="80" align="center"></td>
					<td width="80" align="center"><strong>Total=</strong></td>
					<td align="right" id="value_grey_qty"> </td>

				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?
	exit();
}

if($action=="stock_popup1")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$data=$data0.'_'.$data1.'_'.$data2.'_'.$data3.'_'.$data4.'_'.$data5.'_'.$data6.'_'.$data7.'_'.$data8.'_'.$data9.'_'.$data10.'_'.$data11.'_'.$data12.'_'.$data13.'_'.$data14.'_'.$data15.'_'.$data16.'_'.$data17;

	list($po_ids,$con,$range,$count,$comp,$brand,$lot,$stitch,$gsm,$stock,$color_id,$barcode_nos,$booking_ids,$fabrication_id,$store_ids,$type,$qnty,$width,$machine_dia)=explode("_",$data);

	$date_from=$_SESSION["date_from"];
	$date_to=$_SESSION["date_to"];

	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		//$date_cond=" and e.transaction_date <= '$end_date'";
		//$date_cond2=" and a.transfer_date <= '$end_date'";
	}

	//echo $color_id;die;

	if($qnty=="0.00" || $qnty<0){
		echo "No Data Found";
		die;
	}

	$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where  a.entry_form = 61 and a.po_breakdown_id in($po_ids)  and a.roll_id = b.id and a.status_active =1 and b.status_active=1");

	if(!empty($split_ref_sql))
	{
		foreach ($split_ref_sql as $value)
		{
			$mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
		}
	}

	$iss_sql = sql_select("SELECT c.po_breakdown_id, c.barcode_no, c.qnty from pro_roll_details c where c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.is_returned = 0 and c.booking_without_order = 0
		union all
		select a.po_breakdown_id, c.barcode_no, c.qnty from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and a.trans_type=6 and a.po_breakdown_id in($po_ids) and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
		union all
		select b.from_order_id as po_breakdown_id,c.barcode_no, sum(c.qnty) as qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and b.from_order_id in($po_ids)  and a.transfer_criteria  in (1) and a.entry_form = 82 and c.entry_form = 82 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.barcode_no in($barcode_nos) and c.booking_without_order = 0
		group by c.barcode_no, b.from_order_id
		union all
		select a.from_order_id as po_breakdown_id, c.barcode_no, sum(c.qnty) as qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b, pro_roll_details c
		where a.id = b.mst_id and a.id = c.mst_id and b.id = c.dtls_id and a.from_order_id in($po_ids) and a.entry_form = 110 and c.entry_form = 110 and b.status_active = 1 and c.status_active = 1 and a.status_active = 1 and c.barcode_no in($barcode_nos)
		group by c.barcode_no, a.from_order_id");



	foreach ($iss_sql as $val)
	{
		$iss_qty_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] += $val[csf("qnty")];
		if($mother_barcode_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] != "")
		{
			$iss_qty_arr[$mother_barcode_arr[$val[csf("barcode_no")]]][$val[csf("po_breakdown_id")]] += $val[csf("qnty")];
		}
	}

	$trans_store_arr=return_library_array("select c.barcode_no, s.store_name from inv_receive_master a, lib_store_location s,  pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.store_id=s.id and a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)  and  c.barcode_no not in(select barcode_no from pro_roll_details where barcode_no in($barcode_nos) and entry_form=82 and status_active=1 and is_deleted=0)
		union all
		select c.barcode_no, s.store_name  from inv_item_transfer_mst a, inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)
		order by store_name, barcode_no","barcode_no","store_name");
		?>
		<script>
			var tableFilters = {
				col_operation: {
					id: ["value_grey_qty"],
					col: [4],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			}
			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1,tableFilters);
			});
		</script>
		<fieldset style="width:1190px">

			<table cellpadding="0" width="500" class="rpt_table" rules="all" border="1">
				<thead>
					<th width="40">SL</th>
					<th width="120">Store Name</th>
					<th width="100">Bacode No</th>
					<th width="80">Roll No</th>
					<th>Roll Weight</th>
				</thead>
			</table>
			<div style="width:500px; max-height:250px; overflow-y:scroll">
				<table cellpadding="0" width="480" class="rpt_table" rules="all" border="1" id="tbl_list_search">
					<?
					$i=0; $tot_stock_qnty=0;


					$sql=" SELECT s.store_name, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, 1 as type from inv_receive_master a left join lib_store_location s on a.store_id=s.id, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.po_breakdown_id in($po_ids) and c.booking_without_order = 0
					union all
					select s.store_name, c.barcode_no, c.roll_no, c.qnty, b.to_order_id as po_breakdown_id, 2 as type
					from inv_item_transfer_mst a,  inv_item_transfer_dtls b left join lib_store_location s on b.to_store=s.id, pro_roll_details c
					WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and b.to_order_id in($po_ids) and a.transfer_criteria = 1 and c.booking_without_order = 0
					order by store_name, barcode_no";

					$result= sql_select($sql);
					foreach($result as $row)
					{
						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

						$stock_qty=$row[csf('qnty')]-$iss_qty_arr[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]];
						if($stock_qty>0)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="120"><p><? echo $trans_store_arr[$row[csf('barcode_no')]];//$row[csf('store_name')]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
								<td align="right"><? echo number_format($stock_qty,2); ?></td>
							</tr>
							<?
							$tot_stock_qnty+=$stock_qty;
						}
					}

					$trans_sql="select b.mst_id, c.barcode_no, c.roll_no, c.qnty, a.po_breakdown_id
					from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c where a.trans_id=b.to_trans_id and b.id=c.dtls_id and c.entry_form=83 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and a.po_breakdown_id in($po_ids) and c.booking_without_order = 0";

					$trans_result=sql_select($trans_sql);
					foreach($trans_result as $row)
					{
						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

						$stock_qty=$row[csf('qnty')]-$iss_qty_arr[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]];
						if($stock_qty>0)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="120"><p><? echo $trans_store_arr[$row[csf('barcode_no')]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
								<td width="80" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
								<td align="right"><? echo number_format($stock_qty,2); ?></td>
							</tr>
							<?
							$tot_stock_qnty+=$stock_qty;
						}
					}
					?>
				</table>
			</div>
			<table cellpadding="0" width="480" class="rpt_table" rules="all" border="1">
				<tfoot>
					<th colspan="3">Roll Total :</th>
					<th width="80" style="text-align:center"><? echo $i; ?></th>
					<th width="134" id="value_grey_qty"><? echo number_format($tot_stock_qnty,2); ?></th>
				</tfoot>
			</table>
		</fieldset>
		<?
		exit();
	}

	if($action=="report_generate_bk")
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));
		$rpt_type=str_replace("'","",$rpt_type);
		$cbo_search_by=str_replace("'","",$cbo_search_by);
		$txt_search_comm=str_replace("'","",$txt_search_comm);
		$cbo_sock_for=str_replace("'","",$cbo_sock_for);
		$cbo_value_with=str_replace("'","",$cbo_value_with);
		$booking_no=str_replace("'","",$txt_booking_no);
		$composition_arr=array();

		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);

		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
					list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
					$copmpositionArr[$row[csf('id')]]=$cps;
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$constructionArr[$row[csf('id')]]=$row[csf('construction')];
					list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
					$copmpositionArr[$row[csf('id')]]=$cps;
				}
			}
		}
		unset($data_array);

		$transaction_date_array=array();
		$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=13 group by prod_id";
		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}
		unset($sql_date_result);

		if($rpt_type==1)
		{
			if($booking_no!="")
			{
				echo "<div align='center'><font style='color:#F00; font-size:18px; font-weight:bold'>Booking No Search Not Allow For This button.</font></div>";
				die;
			}
		}

		if($rpt_type==3)
		{
			if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
			$sql="select a.buyer_id,b.job_no,b.po_break_down_id as po_id,b.construction,b.fabric_color_id, sum(b.grey_fab_qnty) as grey_req_qnty,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $book_no_cond group by a.buyer_id,b.job_no,b.po_break_down_id,b.construction,b.fabric_color_id,a.booking_no";

			$sql_result=sql_select($sql);
			$po_ids='';

			foreach( $sql_result as $row )
			{
			$key=$row[csf('buyer_id')].$row[csf('job_no')].$row[csf('po_id')].$row[csf('construction')].$row[csf('fabric_color_id')];//$color_arr[
			$grey_qnty_array[$key]+=$row[csf('grey_req_qnty')];

			if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
			$booking_array[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
		}
		unset($sql_result);

		$po_idss=implode(",",array_unique(explode(",",$po_ids)));

		if($booking_no!='')
		{
			if($po_ids!='')
			{
				$po_id_cond="and a.id in($po_idss)";
				$po_id_cond_c="and c.id in($po_idss)";
			}
			else {
				$po_id_cond="";
				$po_id_cond_c="";
			}
		}
	}
	else
	{
		if($booking_no!='') $book_no_cond="and a.booking_no_prefix_num in($booking_no)";else $book_no_cond="";
		$sql_book="select b.po_break_down_id as po_id,b.booking_no, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $book_no_cond group by b.po_break_down_id,b.booking_no";
		$book_result=sql_select($sql_book);
		$po_ids='';
		foreach($book_result as $row )
		{
			$grey_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_req_qnty')];
			$booking_array[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
			if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		}
		$po_idss=implode(",",array_unique(explode(",",$po_ids)));
		if($booking_no!='')
		{
			if($po_ids!='') $po_id_cond="and a.id in($po_idss)";else $po_id_cond="";
		}
	}

	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}

	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.style_ref_no LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.po_number LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.file_no LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.grouping LIKE '$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}

	$order_cond="";
	if($cbo_sock_for==1)
	{
		$order_cond=" and a.shiping_status<>3 and a.status_active=1";
	}
	else if($cbo_sock_for==2)
	{
		$order_cond=" and a.status_active=3";
	}
	else if($cbo_sock_for==3)
	{
		$order_cond=" and a.shiping_status=3 and a.status_active=1";
	}
	else
	{
		$order_cond="";
	}

	if($rpt_type==1)
	{

		if(str_replace("'","",$cbo_presentation)==1)
		{
			$program_no_array=array();
			if($db_type==0)
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
			}
			else
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");

			}

			foreach( $programData as $row )
			{
				$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]=$row[csf('prog_no')];
			}

			if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";

			$product_array=array();
			$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
			$prod_query_sql=sql_select($prod_query);
			foreach( $prod_query_sql as $row )
			{
				$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
				$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
			}

			$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
			$sql_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,82,83,84,110,183) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
			$result_trans=sql_select( $sql_trans );
			foreach ($result_trans as $row)
			{
				$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
				$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
			}

			/*$sql_transfer_in="select a.transaction_type, a.order_id, a.prod_id, sum(a.cons_quantity) as trans_qnty from inv_transaction a, inv_item_transfer_mst b where a.mst_id=b.id and b.transfer_criteria=4 and b.item_category=13 and a.item_category=13 and a.transaction_type in(5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.order_id=7175 $trans_date group by a.transaction_type,a.order_id,a.prod_id";
			echo $sql_transfer_in;
			$data_transfer_in_array=sql_select($sql_transfer_in);
			foreach( $data_transfer_in_array as $row )
			{
				$trans_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]=$row[csf('trans_qnty')];
				$product_id_arr[$row[csf('order_id')]].=$row[csf('prod_id')].",";
			}*/
			//print_r($trans_arr[3593]);

			ob_start();
			?>
			<fieldset style="width:1410px">
				<table cellpadding="0" cellspacing="0" width="1410">
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="16" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="16" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
					<thead>
						<tr>
							<th width="40" rowspan="2">SL</th>
							<th colspan="5">Fabric Details</th>
							<th colspan="4">Receive Details</th>
							<th colspan="4">Issue Details</th>
							<th colspan="2">Stock Details</th>
						</tr>
						<tr>
							<th width="100">Program No.</th>
							<th width="70">Product ID</th>
							<th width="150">Const. & Comp</th>
							<th width="70">GSM</th>
							<th width="60">F/Dia</th>
							<th width="90">Recv. Qty.</th>
							<th width="90">Issue Return Qty.</th>
							<th width="90">Transf. In Qty.</th>
							<th width="90">Total Recv.</th>
							<th width="90">Issue Qty.</th>
							<th width="90">Receive Return Qty.</th>
							<th width="90">Transf. Out Qty.</th>
							<th width="90">Total Issue</th>
							<th width="90">Stock Qty.</th>
							<th>DOH</th>
						</tr>
					</thead>
				</table>
				<div style="width:1430px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="" align="left">
						<?

						$sql="select b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, a.id, a.po_number, a.pub_shipment_date, a.grouping, a.file_no, a.po_quantity from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond order by a.id, a.pub_shipment_date";
						$result=sql_select( $sql );
						$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
						foreach($result as $row)
						{
							$dataProd=array_filter(array_unique(explode(",",substr($product_id_arr[$row[csf('id')]],0,-1))));
							if(count($dataProd)>0)
							{
								?>
								<tr><td colspan="16" style="font-size:14px" bgcolor="#CCCCAA"><b><?php echo "Order No: ".$row[csf('po_number')]."; Job No: ".$row[csf('job_no')]."; Style Ref: ".$row[csf('style_ref_no')]."; Buyer: ".$buyer_arr[$row[csf('buyer_name')]]."; File No: ".$row[csf('file_no')]."; Int Ref. No: ".$row[csf('grouping')]."; RMG Qty: ".number_format($row[csf('po_quantity')]*$row[csf('ratio')],0).";"."Order Id ".$row[csf('id')]; ?>;<a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $row[csf('id')]; ?>')"><? echo "Grey Fabric Qty(Kg): ".number_format($grey_qnty_array[$row[csf('id')]],2); ?></a></b></td></tr>
								<?
								$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;
								foreach($dataProd as $prodId)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$recv_qty=$recvIssue_array[$row[csf('id')]][$prodId][1];
									$iss_qty=$recvIssue_array[$row[csf('id')]][$prodId][2];
									$iss_ret_qty=$recvIssue_array[$row[csf('id')]][$prodId][4];
									$recv_ret_qty=$recvIssue_array[$row[csf('id')]][$prodId][3];
									//$trans_in_sam_qty=$recvIssue_array[$row[csf('id')]][$prodId][5];
									//$trans_out_sam_qty=$recvIssue_array[$row[csf('id')]][$prodId][6];
									//$trans_in_qty=$trans_arr[$row[csf('id')]][$prodId][5]+$recvIssue_array[$row[csf('id')]][$prodId][5];
									//$trans_out_qty=$trans_arr[$row[csf('id')]][$prodId][6]+$recvIssue_array[$row[csf('id')]][$prodId][6];

									$trans_in_qty=$recvIssue_array[$row[csf('id')]][$prodId][5];
									$trans_out_qty=$recvIssue_array[$row[csf('id')]][$prodId][6];

									$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
									$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
									$stock_qty=$recv_tot_qty-$iss_tot_qty;

									$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('id')]][$prodId])));
									if($cbo_value_with==1 && $stock_qty>=0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?></p></td>
											<td width="70"><p><? echo $prodId; ?></p></td>
											<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
											<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?></p></td>
											<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
											<td width="90" align="right" title="<? echo $row[csf('id')]."**".$prodId; ?>"><? echo number_format($trans_in_qty,2); ?></td>
											<td width="90" align="right">
												<a href='#report_details' onClick="openmypage_delivery('<? echo $row[csf('id')]; ?>','<? echo $program_no; ?>','<? echo $prodId; ?>','<? echo ""; ?>','<? echo ""; ?>','1050px','grey_recv_popup',1);"><? echo number_format($recv_tot_qty,2); ?></a>
											</td>
											<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
											<td width="90" align="right">
												<a href='#report_details' onClick="openmypage_delivery('<? echo $row[csf('id')]; ?>','<? echo $program_no; ?>','<? echo $prodId; ?>','<? echo ""; ?>','<? echo ""; ?>','1050px','grey_recv_popup',2);"><? echo number_format($iss_tot_qty,2); ?></a>
											</td>
											<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
											<?
											$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
											?>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
										</tr>
										<?
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty;
										$order_trans_in_qty+=$trans_in_qty;
										$order_trans_out_qty+=$trans_out_qty;
										$order_tot_recv_qnty+=$recv_tot_qty;
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;

										$tot_recv_qty+=$recv_qty;
										$tot_iss_ret_qty+=$iss_ret_qty;
										$tot_iss_qty+=$iss_qty;
										$tot_rec_ret_qty+=$recv_ret_qty;
										$tot_trans_in_qty+=$trans_in_qty;
										$tot_trans_out_qty+=$trans_out_qty;
										$grand_tot_recv_qty+=$recv_tot_qty;
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;
										$i++;

									}
									else if($cbo_value_with==2 && $stock_qty>0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?></p></td>
											<td width="70"><p><? echo $prodId; ?></p></td>
											<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
											<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?></p></td>
											<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
											<td width="90" align="right" title="<? echo $row[csf('id')]."**".$prodId; ?>"><? echo number_format($trans_in_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
											<?
											$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
											?>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
										</tr>
										<?
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty;
										$order_trans_in_qty+=$trans_in_qty;
										$order_trans_out_qty+=$trans_out_qty;
										$order_tot_recv_qnty+=$recv_tot_qty;
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;

										$tot_recv_qty+=$recv_qty;
										$tot_iss_ret_qty+=$iss_ret_qty;
										$tot_iss_qty+=$iss_qty;
										$tot_rec_ret_qty+=$recv_ret_qty;
										$tot_trans_in_qty+=$trans_in_qty;
										$tot_trans_out_qty+=$trans_out_qty;
										$grand_tot_recv_qty+=$recv_tot_qty;
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;
										$i++;
									}
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="6" align="right"><b>Order Total</b></td>
									<td align="right"><? echo number_format($order_recv_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_iss_ret_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_trans_in_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_tot_recv_qnty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_iss_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_rec_ret_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_trans_out_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_tot_iss_qnty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_stock_qnty,2,'.',''); ?></td>
									<td align="right"></td>
								</tr>
								<?
							}
						}
						?>
						<tfoot>
							<tr>
								<th colspan="6" align="right"><b>Grand Total</b></th>
								<th align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_trans_in_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_trans_out_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_stock_qty,2,'.',''); ?></th>
								<th align="right"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<?
		}
		else if(str_replace("'","",$cbo_presentation)==2)
		{
			$date_from=str_replace("'","",$txt_date_from);
			if( $date_from=="") $receive_date=""; else $receive_date= " and e.receive_date <=".$txt_date_from."";
			//==========================================================
			if(str_replace("'","",$cbo_buyer_id)==0)
			{
				if ($_SESSION['logic_erp']["data_level_secured"]==1)
				{
					if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
				}
				else
				{
					$buyer_id_cond_trans="";
				}
			}
			else
			{
				$buyer_id_cond_trans=" and d.buyer_name=$cbo_buyer_id";//.str_replace("'","",$cbo_buyer_name)
			}

			$sql_dtls="select a.po_number as po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no, c.quantity, a.id as po_breakdown_id, c.entry_form, c.prod_id, d.stitch_length, d.brand_id, d.color_id, d.color_range_id, d.yarn_lot, d.yarn_count, d.rack, d.self, d.machine_no_id, d.no_of_roll as rec_roll, e.booking_no
			from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
			where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58) and c.po_breakdown_id=a.id and c.dtls_id=d.id and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id and e.item_category=13 and e.status_active=1 and e.is_deleted=0
			$year_cond $buyer_id_cond $job_no_cond $search_cond $receive_date $order_cond
			order by a.id, a.po_number, c.prod_id";

			$all_data_arr=sql_select( $sql_dtls ); $po_grey_data_arr=array(); $tot_rows=0; $poIds=''; $prodIds='';
			if(count($all_data_arr)>0)
			{
				foreach($all_data_arr as $row)
				{
					$tot_rows++;
					$poIds.=$row[csf("po_breakdown_id")].",";
					$prodIds.=$row[csf("prod_id")].",";
					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["groupby_data"]=$row[csf("po_number")]."***".$row[csf("file_no")]."***".$row[csf("grouping")]."***".$row[csf("po_quantity")]."***".$row[csf("job_no")]."***".$row[csf("buyer_name")]."***".$row[csf("style_ref_no")];

					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["stitch_length"].=$row[csf("stitch_length")]."___";
					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["brand_id"].=$row[csf("brand_id")]."___";
					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["color_id"].=$row[csf("color_id")]."___";
					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["color_range_id"].=$row[csf("color_range_id")]."___";
					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["machine_no_id"].=$row[csf("machine_no_id")]."___";
					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["rec_roll"]+=$row[csf("rec_roll")];
					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["booking_no"].=$row[csf("booking_no")]."___";

					$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]["quantity"]+=$row[csf("quantity")];
				}
			}
			else
			{
				echo "3**".'Data Not Found'; die;
			}
			unset($all_data_arr);

			$poIds=chop($poIds,','); $poIds_prog_cond=""; $poid_trns_in=""; $poid_trns=""; $poid_return=""; $poid_issue="";

			if($db_type==2 && $tot_rows>1000)
			{
				$poIds_prog_cond=" and (";
				$poid_trns=" and (";
				$poid_return=" and (";
				$poid_issue=" and (";

				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$poIds_prog_cond.=" c.po_breakdown_id in($ids) or ";
					$poid_trns.=" a.to_order_id in($ids) or  a.from_order_id in( $ids ) or ";
					$poid_return.=" b.po_breakdown_id in($ids) or ";
					$poid_issue.=" b.po_breakdown_id in($ids) or ";
				}
				$poIds_prog_cond=chop($poIds_prog_cond,'or ');
				$poIds_prog_cond.=")";

				$poid_trns=chop($poid_trns,'or ');
				$poid_trns.=")";

				$poid_return=chop($poid_return,'or ');
				$poid_return.=")";

				$poid_issue=chop($poid_issue,'or ');
				$poid_issue.=")";
			}
			else
			{
				$poIds_prog_cond=" and c.po_breakdown_id in ($poIds)";
				$poid_trns="and ( a.to_order_id in ($poIds) or a.from_order_id in($poIds))";
				$poid_return=" and b.po_breakdown_id in ($poIds)";
				$poid_issue=" and b.po_breakdown_id in ($poIds)";
			}

			$prodIds=chop($prodIds,','); $prod_dtls_mst=""; $prod_trns="";
			if($db_type==2 && $tot_rows>1000)
			{
				$prod_dtls_mst=" and (";
				$prod_trns=" and (";

				$prodIdsArr=array_chunk(explode(",",$prodIds),999);
				foreach($prodIdsArr as $pids)
				{
					$pids=implode(",",$pids);
					$prod_dtls_mst.=" id in($pids) or ";
					$prod_trns.=" prod_id in($pids) or";
				}
				$prod_dtls_mst=chop($prod_dtls_mst,'or ');
				$prod_dtls_mst.=")";

				$prod_trns=chop($prod_trns,'or ');
				$prod_trns.=")";
			}
			else
			{
				$prod_dtls_mst=" and id in ($prodIds)";
				$prod_trns=" and prod_id in ($prodIds)";
			}

			//die;
			$program_no_array=array();
			if($db_type==0)
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self");
			}
			else
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self");
			}

			foreach( $programData as $row )
			{
				$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]=$row[csf('prog_no')];
			}

			$job_no=str_replace("'","",$txt_job_no);
			if ($job_no=="") $job_no_cond_trans=""; else $job_no_cond_trans=" and d.job_no_prefix_num in ($job_no) ";
			$year_id=str_replace("'","",$cbo_year);

			$variable_set_cond=" and e.entry_form in (2,22,58)";

			if($db_type==0)
			{
				if($year_id!=0) $year_cond_trans=" and year(d.insert_date)=$year_id"; else $year_cond_trans="";
			}
			else if($db_type==2)
			{
				if($year_id!=0) $year_cond_trans=" and TO_CHAR(d.insert_date,'YYYY')=$year_id"; else $year_cond_trans="";
			}


			$order_no=str_replace("'","",$txt_order_id);
			if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

			$date_from=str_replace("'","",$txt_date_from);
			if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";

			//=================Order/Rack & Shelf Wise================
			$trans_order_cond="";
			if($cbo_sock_for==1)
			{
				$trans_order_cond=" and c.shiping_status<>3 and c.status_active=1";
			}
			else if($cbo_sock_for==2)
			{
				$trans_order_cond=" and c.status_active=3";
			}
			else if($cbo_sock_for==3)
			{
				$trans_order_cond=" and c.shiping_status=3 and c.status_active=1";
			}
			else
			{
				$trans_order_cond="";
			}
			$transfer_in_arr=array(); $trans_arr=array(); $transfer_out_arr=array();
			$sql_transfer_in="select a.transfer_criteria, a.from_order_id, a.to_order_id, b.from_prod_id, b.to_rack, b.to_shelf, b.y_count, b.yarn_lot, sum(b.transfer_qnty) as transfer_qnty, sum(b.roll) as transfer_roll
			from inv_item_transfer_mst a, inv_item_transfer_dtls b
			where a.company_id=$cbo_company_id and a.id=b.mst_id and a.transfer_criteria in(4,6,7) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poid_trns
			group by a.transfer_criteria, a.from_order_id, a.to_order_id, b.from_prod_id, b.to_rack, b.to_shelf, b.y_count, b.yarn_lot";

			$data_transfer_in_array=sql_select($sql_transfer_in);
			if(count($data_transfer_in_array)>0)
			{
				foreach( $data_transfer_in_array as $row )
				{
					if($row[csf('to_shelf')]=="") $row[csf('to_shelf')]=0;
					if($row[csf('transfer_criteria')]==4 || $row[csf('transfer_criteria')]==7)
					{
						$transfer_in_arr[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('to_rack')]][$row[csf('to_shelf')]]['qty']=$row[csf('transfer_qnty')];
						$transfer_in_arr[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('to_rack')]][$row[csf('to_shelf')]]['roll']=$row[csf('transfer_roll')];

						$trans_data=$row[csf('to_order_id')]."_".$row[csf('from_prod_id')]."_".$row[csf('y_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('to_rack')]."_".$row[csf('to_shelf')];

						$trans_arr[]=$trans_data;
					}
					if($row[csf('transfer_criteria')]==4 || $row[csf('transfer_criteria')]==6)
					{
						$transfer_out_arr[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('shelf')]]['qty']=$row[csf('transfer_qnty')];
						$transfer_out_arr[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('shelf')]]['roll']=$row[csf('transfer_roll')];

						$trans_data=$row[csf('from_order_id')]."_".$row[csf('from_prod_id')]."_".$row[csf('y_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('rack')]."_".$row[csf('shelf')];

						if(!in_array($trans_data,$trans_arr))
						{
							$trans_arr[]=$trans_data;
						}
					}
				}
			}

			$product_array=array();
			$prod_query="Select id, detarmination_id, gsm, dia_width, brand, yarn_count_id, lot, color from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 $prod_dtls_mst";
			$prod_query_sql=sql_select($prod_query);
			if(count($prod_query_sql)>0)
			{
				foreach( $prod_query_sql as $row )
				{
					$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
					$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
					$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
					$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
					$product_array[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
					$product_array[$row[csf('id')]]['lot']=$row[csf('lot')];
					$product_array[$row[csf('id')]]['color']=$row[csf('color')];
				}
			}

			$transaction_date_array=array();
			$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 $prod_trns group by prod_id";
			$sql_date_result=sql_select($sql_date);
			foreach( $sql_date_result as $row )
			{
				$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
				$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
			}

			$retn_arr=array(); $retn_data_arr=array();

			$return_order_cond="";
			if($cbo_sock_for==1)
			{
				$return_order_cond=" and c.shiping_status<>3 and c.status_active=1";
			}
			else if($cbo_sock_for==2)
			{
				$return_order_cond=" and c.status_active=3";
			}
			else if($cbo_sock_for==3)
			{
				$return_order_cond=" and c.shiping_status=3 and c.status_active=1";
			}
			else
			{
				$return_order_cond="";
			}

			$sql_retn="select b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type=4 and b.trans_type=4 and b.entry_form in(51,84) then b.quantity end) as iss_rtn_qty, sum(case when a.transaction_type=3 and b.trans_type=3 and b.entry_form=45 then b.quantity end) as rcv_rtn_qty
			from inv_transaction a, order_wise_pro_details b
			where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(45,51,84) and a.company_id=$cbo_company_id and a.transaction_type in(3,4) and b.trans_type in (3,4) $poid_return
			group by b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";
			$data_retn_array=sql_select($sql_retn);
			foreach($data_retn_array as $row )
			{
				if($row[csf('self')]=="") $row[csf('self')]=0;
				$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss']=$row[csf('iss_rtn_qty')];
				$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['recv']=$row[csf('rcv_rtn_qty')];
				$rtn_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('batch_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];
				$retn_data_arr[]=$rtn_data;
			}
			unset($data_retn_array);

			ob_start();
			?>
			<fieldset>
				<table cellpadding="0" cellspacing="0" width="2100">
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="28" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="28" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="28" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th colspan="8">Fabric Details</th>
							<th colspan="3">Used Yarn Details</th>
							<th width="100" rowspan="2">Receive Basis (BK/PLN/ PI/GPE)</th>
							<th colspan="5">Receive Details</th>
							<th colspan="5">Issue Details</th>
							<th colspan="5">Stock Details</th>
						</tr>
						<tr>
							<th width="100">Related Program No.</th>
							<th width="">Const. & Comp</th>
							<th width="60">GSM</th>
							<th width="60">F/Dia</th>
							<th width="60">M/Dia</th>
							<th width="60">Stich Length</th>
							<th width="80">Dyeing Color</th>
							<th width="80">Color Type</th>
							<th width="60">Y. Count</th>
							<th width="80">Y. Brand</th>
							<th width="80">Y. Lot</th>
							<th width="80">Recv. Qty.</th>
							<th width="80">Issue Return Qty.</th>
							<th width="80">Transf. In Qty.</th>
							<th width="80">Total Recv.</th>
							<th width="60">Recv. Roll</th>
							<th width="80">Issue Qty.</th>
							<th width="80">Receive Return Qty.</th>
							<th width="80">Transf. Out Qty.</th>
							<th width="80">Total Issue</th>
							<th width="60">Issue Roll</th>
							<th width="80">Stock Qty.</th>
							<th width="60">Roll Qty.</th>
							<th width="50">Rack</th>
							<th width="50">Shelf</th>
							<th width="50">DOH</th>
						</tr>
					</thead>
				</table>
				<div style="width:2120px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
						<?
						$issue_qty_roll_array=array(); $isuue_data_arr=array();
						$issue_order_cond="";
						if($cbo_sock_for==1) $issue_order_cond=" and p.shiping_status<>3 and p.status_active=1";
						else if($cbo_sock_for==2) $issue_order_cond=" and p.status_active=3";
						else if($cbo_sock_for==3) $issue_order_cond=" and p.shiping_status=3 and p.status_active=1";
						else $issue_order_cond="";

						$sql_issue="Select a.po_breakdown_id, a.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, sum(a.quantity ) as issue_qnty, sum(b.no_of_roll) as issue_roll
						from order_wise_pro_details a, inv_grey_fabric_issue_dtls b
						where a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(16,61) $poid_issue
						group by a.po_breakdown_id, a.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self";
						$result_sql_issue=sql_select( $sql_issue );
						foreach ($result_sql_issue as $row)
						{
							if($row[csf('self')]=="") $row[csf('self')]=0;

							$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty']=$row[csf('issue_qnty')];
							$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll']=$row[csf('issue_roll')];
							$issue_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];
                        //echo $issue_data."<br>";
							$isuue_data_arr[]=$issue_data;
						}

                    /*if($db_type==0)
                    {
                        $sql_dtls="select a.po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no,
                        sum(c.quantity) as quantity, c.po_breakdown_id, c.prod_id, group_concat(d.stitch_length) as stitch_length,
                        group_concat(d.brand_id) as brand_id, group_concat(d.color_id) as color_id, max(d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, d.brand_id, d.rack, d.self, d.machine_no_id,
                        sum(case when c.trans_type=1 then d.no_of_roll else 0 end) as rec_roll, e.booking_no
                        from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
                        where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0
                        and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58)
                        and c.po_breakdown_id=a.id
                        and c.dtls_id=d.id
                        and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id and e.item_category=13
                        and e.status_active=1 and e.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $year_cond $search_cond $receive_date $variable_set_cond $order_cond
                        group by a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no order by a.id, a.po_number, c.prod_id";	//, d.color_id
                    }
                    else if($db_type==2)
                    {
                        $sql_dtls="select a.po_number as po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no,
                        sum(c.quantity) as quantity, a.id as po_breakdown_id, c.prod_id,
                        listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, listagg(d.brand_id,',') within group (order by d.brand_id) as brand_id, listagg(d.color_id,',') within group (order by d.color_id) as color_id, max(d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, d.rack, d.self, max(d.machine_no_id) as machine_no_id,
                        sum(case when c.trans_type=1 then d.no_of_roll else 0 end) as rec_roll,
                        listagg(e.booking_no,',') within group (order by e.booking_no) as booking_no
                        from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c,  pro_grey_prod_entry_dtls d, inv_receive_master e
                        where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0
                        and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58)
                        and c.po_breakdown_id=a.id
                        and c.dtls_id=d.id
                        and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id
                        and e.item_category=13 and e.status_active=1 and e.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $receive_date $variable_set_cond $order_cond
                        group by a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no order by a.id, a.po_number, c.prod_id";//, d.color_id
                    }
                    //echo $sql_dtls;//die;
                    $nameArray=sql_select( $sql_dtls );*/
                    $i=1; $k=1; $m=1; $order_arr=array(); $trnsfer_in_qty=0; $trans_in_array=array(); $issue_array=array(); $return_array=array();


					//$po_grey_data_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("yarn_count")]][$row[csf("yarn_lot")]][$row[csf("rack")]][$row[csf("self")]]
					//foreach ($nameArray as $row)
                    foreach ($po_grey_data_arr as $order_id=>$po_data)
                    {
                    	foreach ($po_data as $prod_id=>$prod_data)
                    	{
                    		foreach ($prod_data as $yarn_count=>$yarn_count_data)
                    		{
                    			foreach ($yarn_count_data as $yarn_lot=>$yarn_lot_data)
                    			{
                    				foreach ($yarn_lot_data as $rack=>$rack_data)
                    				{
                    					foreach ($rack_data as $self=>$val)
                    					{
                    						$ex_gruopby_data=explode("***",$val["groupby_data"]);
                    						$colorIdsArr=array_unique(explode('___',$val["color_id"]));

                    						$color_name='';
                    						foreach ($colorIdsArr as $val)
                    						{
                    							if($val>0){ if($color_name=='') $color_name=$color_arr[$val]; else $color_name.=",".$color_arr[$val]; }
                    						}
						//$order_id	$prod_id	$yarn_count	$yarn_lot	$rack	$self
                    						if($self=="") $selfd==0; else $selfd=$self;

                    						$program_no=implode(",",array_unique(explode(",",$program_no_array[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$selfd])));

                    						$po_number=$ex_gruopby_data[0];
                    						$file_no=$ex_gruopby_data[1];
                    						$grouping=$ex_gruopby_data[2];
                    						$po_quantity=$ex_gruopby_data[3];
                    						$job_no=$ex_gruopby_data[4];
                    						$buyer_name=$ex_gruopby_data[5];
                    						$style_ref_no=$ex_gruopby_data[6];

                    						$count_id=explode(',',$yarn_count); $count_val='';
                    						foreach ($count_id as $val)
                    						{
                    							if($val>0){ if($count_val=='') $count_val=$count_arr[$val]; else $count_val.=",".$count_arr[$val]; }
                    						}

                    						$brand_id=array_unique(explode(',',$val["brand_id"])); $brand_name="";
                    						foreach ($brand_id as $val)
                    						{
                    							if($val>0){ if($brand_name=='') $brand_name=$brand_arr[$val]; else $brand_name.=",".$brand_arr[$val]; }
                    						}

                    						$color_range_id=array_unique(explode(',',$val["color_range_id"])); $color_range_name="";
                    						foreach ($color_range_id as $val)
                    						{
                    							if($val>0){ if($color_range_name=='') $color_range_name=$color_range[$val]; else $color_range_name.=",".$color_range[$val]; }
                    						}

                    						$machine_no_id=array_unique(explode(',',$val["machine_no_id"])); $machine_no_name="";
                    						foreach ($machine_no_id as $val)
                    						{
                    							if($val>0){ if($machine_no_name=='') $machine_no_name=$machine_arr[$val]; else $machine_no_name.=",".$machine_arr[$val]; }
                    						}


                    						$trans_data_in=$order_id."_".$prod_id."_".$yarn_count."_".$yarn_lot."_".$rack."_".$selfd;
                    						$trans_in_array[]=$trans_data_in;
                    						$issue_array[]=$trans_data_in;
                    						$return_array[]=$trans_data_in;

                    						if(!in_array($order_id,$order_arr))
                    						{
                    							if($k!=1)
                    							{
                    								foreach($trans_arr as $key=>$val2)
                    								{
                    									$value=explode("_",$val2);
                    									$po_id=$value[0];

                    									$count=explode(',',$value[2]); $count_value='';
                    									foreach ($count as $count_id)
                    									{
                    										if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
                    									}

                    									if($po_id==$prev_order_id)
                    									{
                    										if(!in_array($val2,$trans_in_array))
                    										{
                    											$trnsfer_in_qty=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    											$trnsfer_in_roll=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
                    											$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    											$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
                    											$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    											$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
                    											$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
                    											$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];

                    											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    											$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
                    											$rec_bal=$trnsfer_in_qty+$issue_retn;
                    											$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
                    											$stock=$rec_bal-$issue_bal;
                    											if($cbo_value_with==1 && $stock>=0)
                    											{

                    												?>
                    												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    													<td width="30"><? echo $i; ?></td>
                    													<td width="100"><p><? echo $program_no; ?></p></td>
                    													<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="60"><p><? echo $count_value; ?></p></td>
                    													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    													<td width="80"><p><? echo $value[3]; ?></p></td>
                    													<td width="100"><p></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    													<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2);$tot_transfer_in_qty+=$trnsfer_in_qty;?></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal;?></p></td>
                    													<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    													<td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
                    													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
                    													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    													<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    													<?
                    													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    													?>
                    													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    												</tr>
                    												<?
                    											}
                    											else if($cbo_value_with==2 && $stock>0)
                    											{

                    												?>

                    												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    													<td width="30"><? echo $i; ?></td>
                    													<td width="100"><p><? echo $program_no; ?></p></td>
                    													<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="60"><p><? echo $count_value; ?></p></td>
                    													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    													<td width="80"><p><? echo $value[3]; ?></p></td>
                    													<td width="100"><p></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    													<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2);$tot_transfer_in_qty+=$trnsfer_in_qty;?></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal;?></p></td>
                    													<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    													<td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
                    													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
                    													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    													<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    													<?
                    													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    													?>
                    													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    												</tr>

                    											<? }
                    											$i++;
                    											$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
                    											$grand_tot_rec_bal+=$rec_bal;
                    											$grand_tot_rec_roll+=$rec_roll;
                    											$grand_tot_issue_qty+=$issue_qty;
                    											$grand_tot_issue_retn_qty+=$issue_retn;
                    											$grand_tot_recv_retn_qty+=$recv_retn;
                    											$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                    											$grand_tot_issue_bal+=$issue_bal;
                    											$grand_tot_issue_roll+=$iss_roll;
                    											$grand_tot_stock+=$stock;
                    											$grand_tot_roll_qty+=$stock_roll_qty;

                    											$issue_array[]=$val2;
                    											$return_array[]=$val2;
                    										}
                    									}
                    								}

                    								foreach($isuue_data_arr as $key=>$val2)
                    								{
                    									$value=explode("_",$val2);
                    									$po_id=$value[0];

                    									$count=explode(',',$value[2]); $count_value='';
                    									foreach ($count as $count_id)
                    									{
                    										if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
                    									}

                    									if($po_id==$prev_order_id)
                    									{
                    										if(!in_array($val2,$issue_array))
                    										{
                    											$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    											$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
                    											$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    											$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];

                    											$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];

                    											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    											$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
                    											$rec_bal=$issue_retn;
                    											$issue_bal=$issue_qty+$trnsfer_out_qty;
                    											$stock=$rec_bal-$issue_bal;

                    											if($cbo_value_with==1 && $stock>=0)
                    											{
                    												?>
                    												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    													<td width="30"><? echo $i; ?></td>
                    													<td width="100"><p><? echo $program_no; ?></p></td>
                    													<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="60"><p><? echo $count_value; ?></p></td>
                    													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    													<td width="80"><p><? echo $value[3]; ?></p></td>
                    													<td width="100"><p></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
                    													<td width="60" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
                    													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    													<td width="60" align="right"><p><? $stock_roll_qty=$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    													<?
                    													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    													?>
                    													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    												</tr>
                    											<?	 }
                    											else if($cbo_value_with==2 && $stock>0)
                    											{
                    												?>
                    												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    													<td width="30"><? echo $i; ?></td>
                    													<td width="100"><p><? echo $program_no; ?></p></td>
                    													<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="60"><p><? echo $count_value; ?></p></td>
                    													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    													<td width="80"><p><? echo $value[3]; ?></p></td>
                    													<td width="100"><p></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
                    													<td width="60" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
                    													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    													<td width="60" align="right"><p><? $stock_roll_qty=$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    													<?
                    													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    													?>
                    													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    												</tr>
                    												<?
                    											}

                    											$i++;
                    											$grand_tot_issue_qty+=$issue_qty;
                    											$grand_tot_issue_retn_qty+=$issue_retn;
                    											$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                    											$grand_tot_issue_bal+=$issue_bal;
                    											$grand_tot_issue_roll+=$iss_roll;
                    											$grand_tot_stock+=$stock;
                    											$grand_tot_roll_qty+=$stock_roll_qty;

                    											$return_array[]=$val2;
                    										}
                    									}
                    								}

                    								foreach($retn_data_arr as $key=>$val3)
                    								{
                    									$value=explode("_",$val3);
                    									$po_id=$value[0];

                    									$count=explode(',',$value[2]); $count_value='';
                    									foreach ($count as $count_id)
                    									{
                    										if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
                    									}

                    									if($po_id==$prev_order_id)
                    									{
                    										if(!in_array($val3,$return_array))
                    										{
                    											$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
                    											$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];

                    											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    											$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
                    											$rec_bal=$issue_retn;
                    											$issue_bal=$recv_retn;  $stock=$rec_bal-$issue_bal;
											//$stock=$rec_bal-$issue_bal;
                    											if($cbo_value_with==1 && $stock>=0)
                    											{
                    												?>
                    												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    													<td width="30"><? echo $i; ?></td>
                    													<td width="100"><p><? echo $program_no; ?></p></td>
                    													<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="60"><p><? echo $count_value; ?></p></td>
                    													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    													<td width="80"><p><? echo $value[3]; ?></p></td>
                    													<td width="100"><p></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
                    													<td width="60" align="right"><p></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    													<td width="60" align="right"></td>
                    													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    													<td width="60" align="right"><p></p></td>
                    													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    													<?
                    													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    													?>
                    													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    												</tr>
                    												<?
                    											}
                    											else if($cbo_value_with==2 && $stock>0)
                    											{
                    												?>
                    												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    													<td width="30"><? echo $i; ?></td>
                    													<td width="100"><p><? echo $program_no; ?></p></td>
                    													<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="60"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="80"><p></p></td>
                    													<td width="60"><p><? echo $count_value; ?></p></td>
                    													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    													<td width="80"><p><? echo $value[3]; ?></p></td>
                    													<td width="100"><p></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
                    													<td width="60" align="right"><p></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
                    													<td width="80" align="right"><p></p></td>
                    													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    													<td width="60" align="right"></td>
                    													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    													<td width="60" align="right"><p></p></td>
                    													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    													<?
                    													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    													?>
                    													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    												</tr>
                    												<?
                    											}
                    											$i++;

                    											$grand_tot_issue_retn_qty+=$issue_retn;
                    											$grand_tot_recv_retn_qty+=$recv_retn;
                    											$grand_tot_stock+=$stock;
                    											$grand_tot_rec_bal+=$rec_bal;
                    											$grand_tot_issue_bal+=$issue_bal;

                    											$return_array[]=$val3;
                    										}
                    									}
                    								}

                    								?>
                    								<tr class="tbl_bottom">
                    									<td colspan="13" align="right"><b>Order Total</b></td>
                    									<td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
                    									<td align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?></td>
                    									<td align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?></td>
                    									<td align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?></td>
                    									<td align="right"><? echo $tot_rec_roll; ?></td>
                    									<td align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?></td>
                    									<td align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?></td>
                    									<td align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?></td>
                    									<td align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?></td>
                    									<td align="right"><? echo $tot_issue_roll; ?></td>
                    									<td align="right"><? echo number_format($tot_stock,2,'.',''); ?></td>
                    									<td align="right"><? echo $tot_stock_roll_qty; ?></td>
                    									<td align="right"></td>
                    									<td align="right"></td>
                    									<td align="right"></td>
                    								</tr>
                    								<?
                    								unset($tot_req_qty);
                    								unset($tot_rec_qty);
                    								unset($tot_transfer_in_qty);
                    								unset($tot_rec_bal);
                    								unset($tot_rec_roll);
                    								unset($tot_issue_qty);
                    								unset($tot_transfer_out_qty);
                    								unset($tot_issue_bal);
                    								unset($tot_issue_roll);
                    								unset($tot_stock);
                    								unset($tot_stock_roll_qty);
                    								unset($tot_iss_retn_qty);
                    								unset($tot_recv_retn_qty);
                    							}
                    							?>
                    							<tr><td colspan="28" style="font-size:14px" bgcolor="#CCCCAA"><b><?php echo "Order No: ".$po_number."; Job No: ".$job_no."; Style Ref: ".$style_ref_no."; Buyer: ".$buyer_arr[$buyer_name]."; File No: ".$file_no."; Int Ref. No: ".$grouping."; RMG Qty: ".number_format($po_quantity,2).";"."Order Id ".$order_id; ?>;<a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $order_id; ?>')"><? echo "Grey Fabric Qty(Kg): ".number_format($grey_qnty_array[$order_id],2); ?></a></b></td></tr>
                    							<?
                    							$order_arr[]=$order_id;
                    							$k++;
                    						}
                    						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                    						if($self=="") $self=0;
                    						$trnsfer_in_qty=$transfer_in_arr[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$self]['qty'];
                    						$trnsfer_in_roll=$transfer_in_arr[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$self]['roll'];
                    						$trnsfer_out_qty=$transfer_out_arr[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$self]['qty'];
                    						$trnsfer_out_roll=$transfer_out_arr[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$self]['roll'];
                    						$issue_qty=$issue_qty_roll_array[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$self]['qty'];
                    						$issue_roll=$issue_qty_roll_array[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$self]['roll'];

                    						$issue_retn=$retn_arr[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$self]['iss'];
                    						$recv_retn=$retn_arr[$order_id][$prod_id][$yarn_count][$yarn_lot][$rack][$self]['recv'];
						//print_r($transfer_out_arr[$order_id][$prod_id]);
                    						$rec_bal=$row[csf('quantity')]+$trnsfer_in_qty+$issue_retn;
                    						$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
                    						$stock=$rec_bal-$issue_bal;
                    						if($cbo_value_with==1 && $stock>=0)
                    						{
                    							?>
                    							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    								<td width="30"><? echo $i; ?></td>
                    								<td width="100"><p><? echo $program_no; ?></p></td>
                    								<td width=""><p><? echo $composition_arr[$product_array[$prod_id]['detarmination_id']]; ?></p></td>
                    								<td width="60"><p><? echo $product_array[$prod_id]['gsm']; ?></p></td>
                    								<td width="60"><p><? echo $product_array[$prod_id]['dia_width']; ?></p></td>
                    								<td width="60"><p><? echo $machine_no_name; ?></p></td>
                    								<td width="60"><p><? echo implode(",",array_unique(explode(",",$val["stitch_length"]))); ?></p></td>
                    								<td width="80"><p><? echo $color_name;//$color_arr[$row[csf('color_id')]]; ?></p></td>
                    								<td width="80"><p><? echo $color_range_name; ?></p></td>
                    								<td width="60"><p><? echo $count_val; ?></p></td>
                    								<td width="80"><p><? echo $brand_name;//$brand_arr[$product_array[$prod_id]['brand']]; ?></p></td>
                    								<td width="80"><p><? echo $yarn_lot; ?></p></td>
                    								<td width="100"><p><? echo implode(",",array_unique(explode(",",$val["booking_no"]))); ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($val["quantity"],2); $tot_rec_qty+=$val["quantity"]; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?></p></td>
                    								<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?></p></td>
                    								<td width="60" align="right"><p><? $rec_roll=$val["rec_roll"]+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?></p></td>
                    								<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    								<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></p></td>
                    								<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    								<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?></p></td>
                    								<td width="50" align="center"><p><? echo $rack; ?></p></td>
                    								<td width="50" align="center"><p><? echo $self; ?></p></td>
                    								<?
                    								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$prod_id]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    								?>
                    								<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    							</tr>
                    							<?
                    							$prev_order_id=$order_id;
                    							$i++;
                    							$grand_tot_rec_qty+=$val["quantity"];
                    							$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
                    							$grand_tot_rec_bal+=$rec_bal;
							//echo $rec_roll."**".$i."<br>";
                    							$grand_tot_rec_roll+=$rec_roll;
                    							$grand_tot_issue_qty+=$issue_qty;
                    							$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                    							$grand_tot_issue_bal+=$issue_bal;
                    							$grand_tot_issue_roll+=$iss_roll;
                    							$grand_tot_stock+=$stock;
                    							$grand_tot_roll_qty+=$stock_roll_qty;
                    							$grand_tot_issue_retn_qty+=$issue_retn;
                    							$grand_tot_recv_retn_qty+=$recv_retn;
                    						}
                    						else if($cbo_value_with==2 && $stock>0)
                    						{
                    							?>
                    							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    								<td width="30"><? echo $i; ?></td>
                    								<td width="100"><p><? echo $program_no; ?></p></td>
                    								<td width=""><p><? echo $composition_arr[$product_array[$prod_id]['detarmination_id']]; ?></p></td>
                    								<td width="60"><p><? echo $product_array[$prod_id]['gsm']; ?></p></td>
                    								<td width="60"><p><? echo $product_array[$prod_id]['dia_width']; ?></p></td>
                    								<td width="60"><p><? echo $machine_no_name; ?></p></td>
                    								<td width="60"><p><? echo implode(",",array_unique(explode(",",$val["stitch_length"]))); ?></p></td>
                    								<td width="80"><p><? echo $color_name;//$color_arr[$row[csf('color_id')]]; ?></p></td>
                    								<td width="80"><p><? echo $color_range_name; ?></p></td>
                    								<td width="60"><p><? echo $count_val; ?></p></td>
                    								<td width="80"><p><? echo $brand_name;//$brand_arr[$product_array[$prod_id]['brand']]; ?></p></td>
                    								<td width="80"><p><? echo $yarn_lot; ?></p></td>
                    								<td width="100"><p><? echo implode(",",array_unique(explode(",",$val["booking_no"]))); ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($val["quantity"],2); $tot_rec_qty+=$val["quantity"]; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?></p></td>
                    								<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?></p></td>
                    								<td width="60" align="right"><p><? $rec_roll=$val["rec_roll"]+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?></p></td>
                    								<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?></p></td>
                    								<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    								<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></p></td>
                    								<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    								<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?></p></td>
                    								<td width="50" align="center"><p><? echo $rack; ?></p></td>
                    								<td width="50" align="center"><p><? echo $self; ?></p></td>
                    								<?
                    								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$prod_id]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    								?>
                    								<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    							</tr>
                    							<?
                    							$prev_order_id=$order_id;
                    							$i++;
                    							$grand_tot_rec_qty+=$val["quantity"];
                    							$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
                    							$grand_tot_rec_bal+=$rec_bal;
							//echo $rec_roll."**".$i."<br>";
                    							$grand_tot_rec_roll+=$rec_roll;
                    							$grand_tot_issue_qty+=$issue_qty;
                    							$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                    							$grand_tot_issue_bal+=$issue_bal;
                    							$grand_tot_issue_roll+=$iss_roll;
                    							$grand_tot_stock+=$stock;
                    							$grand_tot_roll_qty+=$stock_roll_qty;
                    							$grand_tot_issue_retn_qty+=$issue_retn;
                    							$grand_tot_recv_retn_qty+=$recv_retn;
                    						}
                    					}
                    				}
                    			}
                    		}
                    	}
                    }
						//var_dump($trans_in_array);

                    foreach($trans_arr as $key=>$val3)
                    {
                    	$value=explode("_",$val3);
                    	$po_id=$value[0];

                    	if($po_id==$prev_order_id )
                    	{
                    		if(!in_array($val3,$trans_in_array))
                    		{
                    			$trnsfer_in_qty=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    			$trnsfer_in_roll=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
                    			$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    			$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
                    			$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    			$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];

                    			$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
                    			$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];

                    			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                    			$count=explode(',',$value[2]); $count_value='';
                    			foreach ($count as $count_id)
                    			{
                    				if($count_id>0) { if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
                    			}

                    			$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
                    			$rec_bal=$trnsfer_in_qty+$issue_retn;$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
                    			$stock=$rec_bal-$issue_bal;
                    			if($cbo_value_with==1 && $stock>=0)
                    			{
                    				?>
                    				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    					<td width="30"><? echo $i; ?></td>
                    					<td width="100"><p><? echo $program_no; ?></p></td>
                    					<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="60"><p><? echo $count_value; ?></p></td>
                    					<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    					<td width="80"><p><? echo $value[3]; ?></p></td>
                    					<td width="100"><p></p></td>
                    					<td width="80" align="right"><p><? //echo $val3;?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?></p></td>
                    					<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?></p></td>
                    					<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    					<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    					<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?></p></td>
                    					<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    					<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    					<?
                    					$daysOnHand=datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    					?>
                    					<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    				</tr>
                    				<?
                    				$i++;
                    				$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
                    				$grand_tot_rec_bal+=$rec_bal;
                    				$grand_tot_rec_roll+=$rec_roll;
                    				$grand_tot_issue_qty+=$issue_qty;
                    				$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                    				$grand_tot_issue_bal+=$issue_bal;
                    				$grand_tot_issue_roll+=$iss_roll;
                    				$grand_tot_stock+=$stock;
                    				$grand_tot_roll_qty+=$stock_roll_qty;
                    				$grand_tot_issue_retn_qty+=$issue_retn;
                    				$grand_tot_recv_retn_qty+=$recv_retn;
                    			}

                    			else if($cbo_value_with==2 && $stock>0)
                    			{
                    				?>
                    				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    					<td width="30"><? echo $i; ?></td>
                    					<td width="100"><p><? echo $program_no; ?></p></td>
                    					<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="60"><p><? echo $count_value; ?></p></td>
                    					<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    					<td width="80"><p><? echo $value[3]; ?></p></td>
                    					<td width="100"><p></p></td>
                    					<td width="80" align="right"><p><? //echo $val3;?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?></p></td>
                    					<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?></p></td>
                    					<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    					<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    					<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?></p></td>
                    					<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    					<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    					<?
                    					$daysOnHand=datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    					?>
                    					<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    				</tr>
                    				<?
                    				$i++;
                    				$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
                    				$grand_tot_rec_bal+=$rec_bal;
                    				$grand_tot_rec_roll+=$rec_roll;
                    				$grand_tot_issue_qty+=$issue_qty;
                    				$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                    				$grand_tot_issue_bal+=$issue_bal;
                    				$grand_tot_issue_roll+=$iss_roll;
                    				$grand_tot_stock+=$stock;
                    				$grand_tot_roll_qty+=$stock_roll_qty;
                    				$grand_tot_issue_retn_qty+=$issue_retn;
                    				$grand_tot_recv_retn_qty+=$recv_retn;

                    				$issue_array[]=$val3;
                    				$return_array[]=$val3;
                    			}

                    		}
                    	}
                    }

                    foreach($isuue_data_arr as $key=>$val3)
                    {
                    	$value=explode("_",$val3);
                    	$po_id=$value[0];

                    	$count=explode(',',$value[2]); $count_value='';
                    	foreach ($count as $count_id)
                    	{
                    		if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
                    	}

                    	if($po_id==$prev_order_id)
                    	{
                    		if(!in_array($val3,$issue_array))
                    		{
                    			$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    			$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
                    			$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
                    			$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];

                    			$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];

                    			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    			$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
                    			$rec_bal=$issue_retn;  $issue_bal=$issue_qty+$trnsfer_out_qty; $stock=$rec_bal-$issue_bal;
                    			if($cbo_value_with==1 && $stock>=0)
                    			{
                    				?>
                    				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    					<td width="30"><? echo $i; ?></td>
                    					<td width="100"><p><? echo $program_no; ?></p></td>
                    					<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="60"><p><? echo $count_value; ?></p></td>
                    					<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    					<td width="80"><p><? echo $value[3]; ?></p></td>
                    					<td width="100"><p></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
                    					<td width="60" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    					<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
                    					<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    					<td width="60" align="right"><p><? $stock_roll_qty=-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
                    					<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    					<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    					<?
                    					$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    					?>
                    					<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    				</tr>
                    				<?
                    				$i++;

                    				$grand_tot_issue_qty+=$issue_qty;
                    				$grand_tot_transfer_out_qty+=$trnsfer_out_qty;

                    				$grand_tot_issue_bal+=$issue_bal;
                    				$grand_tot_rec_bal+=$rec_bal;

                    				$grand_tot_issue_roll+=$iss_roll;
                    				$grand_tot_stock+=$stock;

                    				$grand_tot_roll_qty+=$stock_roll_qty;
                    				$grand_tot_issue_retn_qty+=$issue_retn;

                    				$return_array[]=$val3;
                    			}

                    			else if($cbo_value_with==2 && $stock>0)
                    			{
                    				?>
                    				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    					<td width="30"><? echo $i; ?></td>
                    					<td width="100"><p><? echo $program_no; ?></p></td>
                    					<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="60"><p><? echo $count_value; ?></p></td>
                    					<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    					<td width="80"><p><? echo $value[3]; ?></p></td>
                    					<td width="100"><p></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
                    					<td width="60" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    					<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
                    					<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    					<td width="60" align="right"><p><? $stock_roll_qty=-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
                    					<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    					<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    					<?
                    					$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    					?>
                    					<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    				</tr>
                    				<?
                    				$i++;

                    				$grand_tot_issue_qty+=$issue_qty;
                    				$grand_tot_transfer_out_qty+=$trnsfer_out_qty;

                    				$grand_tot_issue_bal+=$issue_bal;
                    				$grand_tot_rec_bal+=$rec_bal;

                    				$grand_tot_issue_roll+=$iss_roll;
                    				$grand_tot_stock+=$stock;

                    				$grand_tot_roll_qty+=$stock_roll_qty;
                    				$grand_tot_issue_retn_qty+=$issue_retn;

                    				$return_array[]=$val3;
                    			}

                    		}
                    	}
                    }

                    foreach($retn_data_arr as $key=>$val3)
                    {
                    	$value=explode("_",$val3);
                    	$po_id=$value[0];

                    	$count=explode(',',$value[2]); $count_value='';
                    	foreach ($count as $count_id)
                    	{
                    		if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
                    	}

                    	if($po_id==$prev_order_id)
                    	{
                    		if(!in_array($val3,$return_array))
                    		{
                    			$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
                    			$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];

                    			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    			$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
                    			$rec_bal=$issue_retn;$issue_bal=$recv_retn;$stock=$rec_bal-$issue_bal;
                    			if($cbo_value_with==1 && $stock>=0)
                    			{
                    				?>
                    				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    					<td width="30"><? echo $i; ?></td>
                    					<td width="100"><p><? echo $program_no; ?></p></td>
                    					<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="60"><p><? echo $count_value; ?></p></td>
                    					<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    					<td width="80"><p><? echo $value[3]; ?></p></td>
                    					<td width="100"><p></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
                    					<td width="60" align="right"><p></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    					<td width="60" align="right"></td>
                    					<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    					<td width="60" align="right"><p></p></td>
                    					<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    					<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    					<?
                    					$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    					?>
                    					<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    				</tr>
                    				<?
                    				$i++;
                    				$grand_tot_issue_retn_qty+=$issue_retn;
                    				$grand_tot_recv_retn_qty+=$recv_retn;
                    				$grand_tot_stock+=$stock;
                    				$grand_tot_rec_bal+=$rec_bal;
                    				$grand_tot_issue_bal+=$issue_bal;

                    				$return_array[]=$val3;
                    			}
                    			else if($cbo_value_with==2 && $stock>0)
                    			{
                    				?>
                    				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    					<td width="30"><? echo $i; ?></td>
                    					<td width="100"><p><? echo $program_no; ?></p></td>
                    					<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                    					<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="60"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="80"><p></p></td>
                    					<td width="60"><p><? echo $count_value; ?></p></td>
                    					<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
                    					<td width="80"><p><? echo $value[3]; ?></p></td>
                    					<td width="100"><p></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
                    					<td width="60" align="right"><p></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
                    					<td width="80" align="right"><p></p></td>
                    					<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                    					<td width="60" align="right"></td>
                    					<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
                    					<td width="60" align="right"><p></p></td>
                    					<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
                    					<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
                    					<?
                    					$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                    					?>
                    					<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                    				</tr>
                    				<?
                    				$i++;
                    				$grand_tot_issue_retn_qty+=$issue_retn;
                    				$grand_tot_recv_retn_qty+=$recv_retn;
                    				$grand_tot_stock+=$stock;
                    				$grand_tot_rec_bal+=$rec_bal;
                    				$grand_tot_issue_bal+=$issue_bal;
                    				$return_array[]=$val3;
                    			}

                    		}
                    	}
                    }
                    ?>
                    <tr class="tbl_bottom">
                    	<td colspan="13" align="right"><b>Order Total</b></td>
                    	<td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
                    	<td align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?></td>
                    	<td align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?></td>
                    	<td align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?></td>
                    	<td align="right"><? echo $tot_rec_roll; ?></td>
                    	<td align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?></td>
                    	<td align="right"><? echo number_format($tot_recv_retn_qty ,2,'.',''); ?></td>
                    	<td align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?></td>
                    	<td align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?></td>
                    	<td align="right"><? echo $tot_issue_roll; ?></td>
                    	<td align="right"><? echo number_format($tot_stock,2,'.',''); ?></td>
                    	<td align="right"><? echo $tot_stock_roll_qty; ?></td>
                    	<td align="right"></td>
                    	<td align="right"></td>
                    	<td align="right"></td>
                    </tr>
                    <tfoot>
                    	<tr>
                    		<th colspan="13" align="right"><b>Grand Total</b></th>
                    		<th align="right"><? echo number_format($grand_tot_rec_qty,2,'.',''); ?></th>
                    		<th align="right"><? echo number_format($grand_tot_issue_retn_qty,2,'.',''); ?></th>
                    		<th align="right"><? echo number_format($grand_tot_transfer_in_qty,2,'.',''); ?></th>
                    		<th align="right"><? echo number_format($grand_tot_rec_bal,2,'.',''); ?></th>
                    		<th align="right"><? echo $grand_tot_rec_roll; ?></th>
                    		<th align="right"><? echo number_format($grand_tot_issue_qty,2,'.',''); ?></th>
                    		<th align="right"><? echo number_format($grand_tot_recv_retn_qty,2,'.',''); ?></th>
                    		<th align="right"><? echo number_format($grand_tot_transfer_out_qty,2,'.',''); ?></th>
                    		<th align="right"><? echo number_format($grand_tot_issue_bal,2,'.',''); ?></th>
                    		<th align="right"><? echo $grand_tot_issue_roll; ?></th>
                    		<th align="right"><? echo number_format($grand_tot_stock,2,'.',''); ?></th>
                    		<th align="right"><? echo $grand_tot_roll_qty; ?></th>
                    		<th align="right"></th>
                    		<th align="right"></th>
                    		<th align="right"></th>
                    	</tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
        <?
    }
    else if(str_replace("'","",$cbo_presentation)==3)
    {
    	$program_no_array=array();
    	if($db_type==0)
    	{
    		$programData=sql_select("select c.po_breakdown_id, b.prod_id, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
    	}
    	else
    	{
    		$programData=sql_select("select c.po_breakdown_id, b.prod_id, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
    	}

    	foreach($programData as $row )
    	{
    		$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]=$row[csf('prog_no')];
    	}

    	if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";

    	$product_array=array();
    	$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
    	$prod_query_sql=sql_select($prod_query);
    	foreach( $prod_query_sql as $row )
    	{
    		$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
    		$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
    		$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
    		$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
    	}

    	$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
    	$sql_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,16,45,51,58,61,80,81,83,84) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
    	$result_trans=sql_select( $sql_trans );
    	foreach ($result_trans as $row)
    	{
    		$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
    		$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
    	}
    	ob_start();
    	?>
    	<fieldset style="width:1410px">
    		<table cellpadding="0" cellspacing="0" width="1410">
    			<tr class="form_caption" style="border:none;">
    				<td align="center" width="100%" colspan="16" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
    			</tr>
    			<tr class="form_caption" style="border:none;">
    				<td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
    			</tr>
    			<tr class="form_caption" style="border:none;">
    				<td align="center" width="100%" colspan="16" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
    			</tr>
    		</table>
    		<table width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
    			<thead>
    				<tr>
    					<th width="40" rowspan="2">SL</th>
    					<th colspan="5">Fabric Details</th>
    					<th colspan="4">Receive Details</th>
    					<th colspan="4">Issue Details</th>
    					<th colspan="2">Stock Details</th>
    				</tr>
    				<tr>
    					<th width="100">Program No.</th>
    					<th width="70">Product ID</th>
    					<th width="150">Const. & Comp</th>
    					<th width="70">GSM</th>
    					<th width="60">F/Dia</th>
    					<th width="90">Recv. Qty.</th>
    					<th width="90">Issue Return Qty.</th>
    					<th width="90">Transf. In Qty.</th>
    					<th width="90">Total Recv.</th>
    					<th width="90">Issue Qty.</th>
    					<th width="90">Receive Return Qty.</th>
    					<th width="90">Transf. Out Qty.</th>
    					<th width="90">Total Issue</th>
    					<th width="90">Stock Qty.</th>
    					<th>DOH</th>
    				</tr>
    			</thead>
    		</table>
    		<div style="width:1430px; overflow-y: scroll; max-height:380px;" id="scroll_body">
    			<table width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
    				<?
    				if($db_type==0)
    				{
    					$sql="select b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, group_concat(a.id) as po_id, sum(a.po_quantity*b.total_set_qnty) as po_qty from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond group by b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty order by b.id";
    				}
    				else
    				{
    					$sql="select b.id, b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as po_id, sum(a.po_quantity*b.total_set_qnty) as po_qty from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond group by b.id, b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty order by b.id";
    				}

    				$result=sql_select( $sql );
    				$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
    				foreach($result as $row)
    				{
    					$grey_qty=0; $dataProdIds='';
    					$poIds=explode(",",$row[csf('po_id')]);
    					foreach($poIds as $id)
    					{
    						$grey_qty+=$grey_qnty_array[$id];
    						$dataProdIds.=$product_id_arr[$id].",";
    					}
    					$dataProd=array_filter(array_unique(explode(",",substr($dataProdIds,0,-1))));
    					if(count($dataProd)>0)
    					{
    						?>
    						<tr><td colspan="16" style="font-size:14px" bgcolor="#CCCCAA"><b><?php echo "Job No: ".$row[csf('job_no')]."; Style Ref: ".$row[csf('style_ref_no')]."; Buyer: ".$buyer_arr[$row[csf('buyer_name')]]."; RMG Qty: ".number_format($row[csf('po_qty')],0); ?><a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $row[csf('po_id')]; ?>')"><? echo "Grey Fabric Qty(Kg): ".number_format($grey_qty,2); ?></a></b></td></tr>
    						<?
    						$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;
    						foreach($dataProd as $prodId)
    						{
    							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

    							$recv_qty=0; $iss_qty=0; $iss_ret_qty=0; $recv_ret_qty=0; $trans_in_qty=0; $trans_out_qty=0;
    							foreach($poIds as $id)
    							{
    								$recv_qty+=$recvIssue_array[$id][$prodId][1];
    								$iss_qty+=$recvIssue_array[$id][$prodId][2];
    								$iss_ret_qty+=$recvIssue_array[$id][$prodId][4];
    								$recv_ret_qty+=$recvIssue_array[$id][$prodId][3];
    								$trans_in_qty+=$recvIssue_array[$id][$prodId][5];
    								$trans_out_qty+=$recvIssue_array[$id][$prodId][6];
    							}

    							$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
    							$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
    							$stock_qty=$recv_tot_qty-$iss_tot_qty;

    							$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('id')]][$prodId])));
    							if($cbo_value_with==1 && $stock_qty>=0)
    							{
    								?>
    								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
    									<td width="40"><? echo $i; ?></td>
    									<td width="100"><p><? echo $program_no; ?></p></td>
    									<td width="70"><p><? echo $prodId; ?></p></td>
    									<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
    									<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?></p></td>
    									<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?></p></td>
    									<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($trans_in_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
    									<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
    									<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
    									<?
    									$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
    									?>
    									<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
    								</tr>
    								<?
    								$i++;

    								$order_recv_qty+=$recv_qty;
    								$order_iss_ret_qty+=$iss_ret_qty;
    								$order_iss_qty+=$iss_qty;
    								$order_rec_ret_qty+=$recv_ret_qty;
    								$order_trans_in_qty+=$trans_in_qty;
    								$order_trans_out_qty+=$trans_out_qty;
    								$order_tot_recv_qnty+=$recv_tot_qty;
    								$order_tot_iss_qnty+=$iss_tot_qty;
    								$order_stock_qnty+=$stock_qty;

    								$tot_recv_qty+=$recv_qty;
    								$tot_iss_ret_qty+=$iss_ret_qty;
    								$tot_iss_qty+=$iss_qty;
    								$tot_rec_ret_qty+=$recv_ret_qty;
    								$tot_trans_in_qty+=$trans_in_qty;
    								$tot_trans_out_qty+=$trans_out_qty;
    								$grand_tot_recv_qty+=$recv_tot_qty;
    								$grand_tot_iss_qty+=$iss_tot_qty;
    								$grand_stock_qty+=$stock_qty;
    							}
    							else if($cbo_value_with==2 && $stock_qty>0)
    							{
    								?>
    								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
    									<td width="40"><? echo $i; ?></td>
    									<td width="100"><p><? echo $program_no; ?></p></td>
    									<td width="70"><p><? echo $prodId; ?></p></td>
    									<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
    									<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?></p></td>
    									<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?></p></td>
    									<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($trans_in_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
    									<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
    									<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
    									<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
    									<?
    									$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
    									?>
    									<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
    								</tr>

    								<?
    								$i++;

    								$order_recv_qty+=$recv_qty;
    								$order_iss_ret_qty+=$iss_ret_qty;
    								$order_iss_qty+=$iss_qty;
    								$order_rec_ret_qty+=$recv_ret_qty;
    								$order_trans_in_qty+=$trans_in_qty;
    								$order_trans_out_qty+=$trans_out_qty;
    								$order_tot_recv_qnty+=$recv_tot_qty;
    								$order_tot_iss_qnty+=$iss_tot_qty;
    								$order_stock_qnty+=$stock_qty;

    								$tot_recv_qty+=$recv_qty;
    								$tot_iss_ret_qty+=$iss_ret_qty;
    								$tot_iss_qty+=$iss_qty;
    								$tot_rec_ret_qty+=$recv_ret_qty;
    								$tot_trans_in_qty+=$trans_in_qty;
    								$tot_trans_out_qty+=$trans_out_qty;
    								$grand_tot_recv_qty+=$recv_tot_qty;
    								$grand_tot_iss_qty+=$iss_tot_qty;
    								$grand_stock_qty+=$stock_qty;
    							}

    						}
    						?>
    						<tr class="tbl_bottom">
    							<td colspan="6" align="right"><b>Order Total</b></td>
    							<td align="right"><? echo number_format($order_recv_qty,2,'.',''); ?></td>
    							<td align="right"><? echo number_format($order_iss_ret_qty,2,'.',''); ?></td>
    							<td align="right"><? echo number_format($order_trans_in_qty,2,'.',''); ?></td>
    							<td align="right"><? echo number_format($order_tot_recv_qnty,2,'.',''); ?></td>
    							<td align="right"><? echo number_format($order_iss_qty,2,'.',''); ?></td>
    							<td align="right"><? echo number_format($order_rec_ret_qty,2,'.',''); ?></td>
    							<td align="right"><? echo number_format($order_trans_out_qty,2,'.',''); ?></td>
    							<td align="right"><? echo number_format($order_tot_iss_qnty,2,'.',''); ?></td>
    							<td align="right"><? echo number_format($order_stock_qnty,2,'.',''); ?></td>
    							<td align="right"></td>
    						</tr>
    						<?
    					}
    				}
    				?>
    				<tfoot>
    					<tr>
    						<th colspan="6" align="right"><b>Grand Total</b></th>
    						<th align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
    						<th align="right"><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></th>
    						<th align="right"><? echo number_format($tot_trans_in_qty,2,'.',''); ?></th>
    						<th align="right"><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></th>
    						<th align="right"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
    						<th align="right"><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></th>
    						<th align="right"><? echo number_format($tot_trans_out_qty,2,'.',''); ?></th>
    						<th align="right"><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></th>
    						<th align="right"><? echo number_format($grand_stock_qty,2,'.',''); ?></th>
    						<th align="right"></th>
    					</tr>
    				</tfoot>
    			</table>
    		</div>
    	</fieldset>
    	<?
    }
    else if(str_replace("'","",$cbo_presentation)==4)
    {
    	ob_start();
    	?>
    	<div style="width:920px;" align="center">
    		<table cellpadding="0" cellspacing="0" width="900">
    			<tr class="form_caption" style="border:none;">
    				<td align="center" width="100%" colspan="16" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
    			</tr>
    			<tr class="form_caption" style="border:none;">
    				<td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
    			</tr>
    			<tr class="form_caption" style="border:none;">
    				<td align="center" width="100%" colspan="16" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
    			</tr>
    		</table>
    		<table width="900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
    			<thead>
    				<tr>
    					<th width="30" rowspan="2">SL</th>
    					<th width="120" rowspan="2">Buyer name</th>
    					<th width="100" rowspan="2">Opening stock</th>
    					<th width="240" colspan="3">Received</th>
    					<th width="240" colspan="3">Issue</th>
    					<th width="100" rowspan="2">Closing Stock</th>
    					<th rowspan="2">Remarks</th>
    				</tr>
    				<tr>
    					<th width="80">Receive</th>
    					<th width="80">Issue Rtn</th>
    					<th width="80">Total Receive</th>
    					<th width="80">Issue</th>
    					<th width="80">Receive Rtn</th>
    					<th width="80">Total Issue</th>
    				</tr>
    			</thead>
    			<?

				/*$date_cond="";
				if( str_replace("'","",$txt_date_from)!="")
				{
					$txt_date_from=str_replace("'","",$txt_date_from);
					if($db_type==0)
					{
						$txt_date_from=change_date_format($txt_date_from,'dd-mm-yyyy');
						$date_cond=" and date_format(c.insert_date,'%d-%c-%Y')='$txt_date_from'";
					}
					else
					{
						$txt_date_from=change_date_format($txt_date_from,'','',0);
						$date_cond=" and TO_CHAR(c.insert_date,'DD-MM-YYYY')='$txt_date_from'";
					}
				}*/

				$txt_date_from=str_replace("'","",$txt_date_from);
				$sql="select  b.buyer_name, max(b.remarks) as remarks, sum(a.po_quantity*b.total_set_qnty) as po_qty,
				sum(case when c.entry_form in(2,22,58) and d.transaction_date<'$txt_date_from' then c.quantity else 0 end) as rcv_open_qnty,
				sum(case when c.entry_form in(16,61) and d.transaction_date<'$txt_date_from' then c.quantity else 0 end) as issue_open_qnty,
				sum(case when c.entry_form in(45) and d.transaction_date<'$txt_date_from' then c.quantity else 0 end) as rcv_rtn_open_qnty,
				sum(case when c.entry_form in(51,84) and d.transaction_date<'$txt_date_from' then c.quantity else 0 end) as issue_rtn_open_qnty,
				sum(case when c.entry_form in(2,22,58) and d.transaction_date='$txt_date_from' then c.quantity else 0 end) as rcv_qnty,
				sum(case when c.entry_form in(16,61) and d.transaction_date='$txt_date_from' then c.quantity else 0 end) as issue_qnty,
				sum(case when c.entry_form in(45) and d.transaction_date='$txt_date_from' then c.quantity else 0 end) as rcv_rtn_qnty,
				sum(case when c.entry_form in(51,84) and d.transaction_date='$txt_date_from' then c.quantity else 0 end) as issue_rtn_qnty
				from wo_po_details_master b, wo_po_break_down a, order_wise_pro_details c, inv_transaction d
				where b.job_no=a.job_no_mst and a.id=c.po_breakdown_id and c.trans_id=d.id and c.entry_form in(2,22,58,16,61,45,51,84) and c.trans_id>0 and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond
				group by  b.buyer_name
				order by b.buyer_name ";

				//echo $sql;//die;
				$result=sql_select( $sql );
				$i=1;
				foreach($result as $row)
				{
					$open_stock=(($row[csf("rcv_open_qnty")]+$row[csf("issue_rtn_open_qnty")])-($row[csf("issue_open_qnty")]+$row[csf("rcv_rtn_open_qnty")]));
					$tot_rcv=($row[csf("rcv_qnty")]+$row[csf("issue_rtn_qnty")]);
					$tot_issue=($row[csf("issue_qnty")]+$row[csf("rcv_rtn_qnty")]);
					$close_stock=(($open_stock+$tot_rcv)-$tot_issue);

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td align="center"><? echo $i; ?></td>
						<td><p><? echo $buyer_array[$row[csf('buyer_name')]]; ?></p></td>
						<td align="right"><? echo number_format($open_stock,2); $grand_open_stock+=$open_stock; ?></td>
						<td align="right"><? echo number_format($row[csf("rcv_qnty")],2); $grand_rcv_qnty+=$row[csf("rcv_qnty")]; ?></td>
						<td align="right"><? echo number_format($row[csf("issue_rtn_qnty")],2); $grand_issue_rtn_qnty+=$row[csf("issue_rtn_qnty")]; ?></td>
						<td align="right"><? echo number_format($tot_rcv,2); $grand_tot_rcv+=$tot_rcv; ?></td>
						<td align="right"><? echo number_format($row[csf("issue_qnty")],2); $grand_issue_qnty+=$row[csf("issue_qnty")]; ?></td>
						<td align="right"><? echo number_format($row[csf("rcv_rtn_qnty")],2); $grand_rcv_rtn_qnty+=$row[csf("rcv_rtn_qnty")]; ?></td>
						<td align="right"><? echo number_format($tot_issue,2); $grand_tot_issue+=$tot_issue; ?></td>
						<td align="right"><? echo number_format($close_stock,2);  $grand_close_stock+=$close_stock; ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?
					$i++;
					/*if($cbo_value_with==1 && $close_stock>0)
					{

					}*/
				}
				?>
				<tfoot>
					<th></th>
					<th>Grand Total</th>
					<th align="right"><? echo number_format($grand_open_stock,2);?></th>
					<th align="right"><? echo number_format($grand_rcv_qnty,2);?></th>
					<th align="right"><? echo number_format($grand_issue_rtn_qnty,2);?></th>
					<th align="right"><? echo number_format($grand_tot_rcv,2);?></th>
					<th align="right"><? echo number_format($grand_issue_qnty,2);?></th>
					<th align="right"><? echo number_format($grand_rcv_rtn_qnty,2);?></th>
					<th align="right"><? echo number_format($grand_tot_issue,2);?></th>
					<th align="right"><? echo number_format($grand_close_stock,2);?></th>
					<th></th>
				</tfoot>
			</table>
		</div>
		<?
	}

}
else if($rpt_type==2)
{
	if($db_type==0)
	{
		$po_id_con="distinct(a.id) as po_id";
		$program_no_array=return_library_array( "select po_id, group_concat(distinct(dtls_id)) as dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
	}
	else
	{
		$po_id_con="LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as po_id ";
		$program_no_array=return_library_array( "select po_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
	}

	if($db_type==0)
	{
		$program_no_array=return_library_array( "select b.po_id, group_concat(distinct(dtls_id)) as dtls_id from wo_po_break_down b where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
	}
	else
	{
		$program_no_array=return_library_array( "select po_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as dtls_id from wo_po_break_down b where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
	}

	if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";

	$product_array=array();
	$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
	$prod_query_sql=sql_select($prod_query);
	foreach( $prod_query_sql as $row )
	{
		$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
		$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
		$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
	}

	$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
	$sql_trans="Select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,82,83,84) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type  in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
	$result_trans=sql_select( $sql_trans );
	foreach ($result_trans as $row)
	{
		$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]+=$row[csf('qnty')];
		$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
	}

	ob_start();
	?>
	<style type="text/css">
		.word_break_wrap{
			word-break:break-all;
			word-wrap:break-word;
		}
	</style>
	<fieldset style="width:2220px">
		<table cellpadding="0" cellspacing="0" width="1810">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="22" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="22" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="22" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="2220" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="100" rowspan="2">Order No</th>
					<th width="100" rowspan="2">File No</th>
					<th width="100" rowspan="2">Ref. No</th>
					<th width="100" rowspan="2">Job No</th>
					<th width="100" rowspan="2">Style No</th>
					<th width="100" rowspan="2">Buyer</th>
					<th width="110" rowspan="2">Booking No</th>
					<th width="100" rowspan="2">Booking Qnty</th>
					<th colspan="5">Fabric Details</th>
					<th colspan="4">Receive Details</th>
					<th colspan="4">Issue Details</th>
					<th colspan="2">Stock Details</th>
				</tr>
				<tr>
					<th width="100">Program No.</th>
					<th width="70">Product ID</th>
					<th width="150">Const. & Comp</th>
					<th width="70">GSM</th>
					<th width="60">F/Dia</th>
					<th width="90">Recv. Qty.</th>
					<th width="90">Issue Return Qty.</th>
					<th width="90">Transf. In Qty.</th>
					<th width="90">Total Recv.</th>
					<th width="90">Issue Qty.</th>
					<th width="90">Receive Return Qty.</th>
					<th width="90">Transf. Out Qty.</th>
					<th width="90">Total Issue</th>
					<th width="90">Stock Qty.</th>
					<th width="80">DOH</th>
				</tr>
			</thead>
		</table>
		<div style="width:2238px; overflow-y: scroll; max-height:280px;" id="scroll_body">
			<table width="2220" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
				<?
				$sql="select b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, a.id, a.po_number, a.file_no, a.grouping, a.pub_shipment_date, a.po_quantity from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id  and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond $order_cond $po_id_cond order by a.id, a.pub_shipment_date";

				$result=sql_select( $sql );
				$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
				foreach($result as $row)
				{
					$dataProd=array_filter(array_unique(explode(",",substr($product_id_arr[$row[csf('id')]],0,-1))));
					if(count($dataProd)>0)
					{
						$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;$p=1;
						foreach($dataProd as $prodId)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$recv_qty=$recvIssue_array[$row[csf('id')]][$prodId][1];
							$iss_qty=$recvIssue_array[$row[csf('id')]][$prodId][2];
							$iss_ret_qty=$recvIssue_array[$row[csf('id')]][$prodId][4];
							$recv_ret_qty=$recvIssue_array[$row[csf('id')]][$prodId][3];
							$trans_in_qty=$recvIssue_array[$row[csf('id')]][$prodId][5];
							$trans_out_qty=$recvIssue_array[$row[csf('id')]][$prodId][6];
							$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
							$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
							$stock_qty=$recv_tot_qty-$iss_tot_qty;

							$bookingNo=rtrim($booking_array[$row[csf('id')]]['booking_no'],',');
							$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('id')]])));
							$booking_no=implode(",",array_unique(explode(",",$bookingNo)));
							if($cbo_value_with==1 && $stock_qty>=0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i;//echo "==".$row[csf('id')]."=".$prodId; ?></td>
									<?
									if($p==1)
									{
										?>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('po_number')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('file_no')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('grouping')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('job_no')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('style_ref_no')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
										<td width="110"><p class="word_break_wrap"><? echo $booking_no; ?></p></td>
										<td width="100" align="right"><? echo number_format($grey_qnty_array[$row[csf('id')]],2); ?></td>
										<?
										$tot_booking_qty+=$grey_qnty_array[$row[csf('id')]];
									}
									else
									{
										?>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('po_number')]; ?></p></td>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="110"><p></p></td>
										<td width="100"><p></p></td>
										<?
									}
									$p++;
									?>
									<td width="100"><p><? echo $program_no; ?></p></td>
									<td width="70"><p><? echo $prodId; ?></p></td>
									<td width="150"><p class="word_break_wrap"><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
									<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?></p></td>
									<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?></p></td>
									<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($trans_in_qty,2);?></td>
									<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
									<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
									<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
									<? $daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d")); ?>
									<td  width="80" align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
								</tr>
								<?
								$i++;

								$order_recv_qty+=$recv_qty;
								$order_iss_ret_qty+=$iss_ret_qty;
								$order_iss_qty+=$iss_qty;
								$order_rec_ret_qty+=$recv_ret_qty;
								$order_trans_in_qty+=$trans_in_qty;
								$order_trans_out_qty+=$trans_out_qty;
								$order_tot_recv_qnty+=$recv_tot_qty;
								$order_tot_iss_qnty+=$iss_tot_qty;
								$order_stock_qnty+=$stock_qty;


								$tot_recv_qty+=$recv_qty;
								$tot_iss_ret_qty+=$iss_ret_qty;
								$tot_iss_qty+=$iss_qty;
								$tot_rec_ret_qty+=$recv_ret_qty;
								$tot_trans_in_qty+=$trans_in_qty;
								$tot_trans_out_qty+=$trans_out_qty;
								$grand_tot_recv_qty+=$recv_tot_qty;
								$grand_tot_iss_qty+=$iss_tot_qty;
								$grand_stock_qty+=$stock_qty;
							}
							else if($cbo_value_with==2 && $stock_qty>0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="40"><? echo $i; ?></td>
									<?
									if($p==1)
									{
										?>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('po_number')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('file_no')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('grouping')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('job_no')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $row[csf('style_ref_no')]; ?></p></td>
										<td width="100"><p class="word_break_wrap"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
										<td width="110"><p class="word_break_wrap"><? echo $booking_no; ?></p></td>
										<td width="100" align="right"><? echo number_format($grey_qnty_array[$row[csf('id')]],2); ?></td>
										<?
										$tot_booking_qty+=$grey_qnty_array[$row[csf('id')]];
									}
									else
									{
										?>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="100"><p></p></td>
										<td width="110"><p></p></td>
										<td width="100"><p></p></td>
										<?
									}
									$p++;
									?>
									<td width="100"><p class="word_break_wrap"><? echo $program_no; ?></p></td>
									<td width="70"><p class="word_break_wrap"><? echo $prodId; ?></p></td>
									<td width="150"><p class="word_break_wrap"><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
									<td width="70"><p class="word_break_wrap"><? echo $product_array[$prodId]['gsm']; ?></p></td>
									<td width="60"><p class="word_break_wrap"><? echo $product_array[$prodId]['dia_width']; ?></p></td>
									<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($trans_in_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
									<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
									<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
									<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
									<? $daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d")); ?>
									<td width="80" align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
								</tr>

								<?
								$i++;

								$order_recv_qty+=$recv_qty;
								$order_iss_ret_qty+=$iss_ret_qty;
								$order_iss_qty+=$iss_qty;
								$order_rec_ret_qty+=$recv_ret_qty;
								$order_trans_in_qty+=$trans_in_qty;
								$order_trans_out_qty+=$trans_out_qty;
								$order_tot_recv_qnty+=$recv_tot_qty;
								$order_tot_iss_qnty+=$iss_tot_qty;
								$order_stock_qnty+=$stock_qty;


								$tot_recv_qty+=$recv_qty;
								$tot_iss_ret_qty+=$iss_ret_qty;
								$tot_iss_qty+=$iss_qty;
								$tot_rec_ret_qty+=$recv_ret_qty;
								$tot_trans_in_qty+=$trans_in_qty;
								$tot_trans_out_qty+=$trans_out_qty;
								$grand_tot_recv_qty+=$recv_tot_qty;
								$grand_tot_iss_qty+=$iss_tot_qty;
								$grand_stock_qty+=$stock_qty;
							}

						}
					}
				}
				?>
			</table>
		</div>
		<table width="2220" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
			<tfoot>
				<tr>

					<th width="40"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="110">Grand Total</th>
					<th align="right" width="100" id="value_tot_booking_qty"><? echo number_format($tot_booking_qty,2,'.',''); ?></th>
					<th align="right" width="100"></th>
					<th align="right" width="70"></th>
					<th align="right" width="150"></th>
					<th align="right" width="70"></th>
					<th align="right" width="60"></th>
					<th align="right" width="90" id="value_tot_recv_qty"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_tot_iss_ret_qty"><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_tot_trans_in_qty"><? echo number_format($tot_trans_in_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_grand_tot_recv_qty"><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_tot_iss_qty"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_tot_rec_ret_qty"><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_tot_trans_out_qty"><? echo number_format($tot_trans_out_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_grand_tot_iss_qty"><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></th>
					<th align="right" width="90" id="value_grand_stock_qty"><? echo number_format($grand_stock_qty,2,'.',''); ?></th>
					<th align="right" width="80"></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?
}
else if($rpt_type==3)
{
	if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";
	if(str_replace("'","",$cbo_presentation)==1 || str_replace("'","",$cbo_presentation)==2 ||str_replace("'","",$cbo_presentation)==3)
	{
		$date_from=str_replace("'","",$txt_date_from);
		if( $date_from=="") $receive_date=""; else $receive_date= " and e.receive_date <=".$txt_date_from."";

		if(str_replace("'","",$cbo_buyer_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond_trans="";
			}
			else
			{
				$buyer_id_cond_trans="";
			}
		}
		else
		{
			$buyer_id_cond_trans=" and d.buyer_name=$cbo_buyer_id";
		}

		$program_no_array=array();
		if($db_type==0)
		{
			$programData=sql_select("select c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self");
		}
		else
		{
			$programData=sql_select("select c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self");
		}

		foreach( $programData as $row )
		{
			$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]=$row[csf('prog_no')];
		}

		$job_no=str_replace("'","",$txt_job_no);
		if ($job_no=="") $job_no_cond_trans=""; else $job_no_cond_trans=" and d.job_no_prefix_num in ($job_no) ";
		$year_id=str_replace("'","",$cbo_year);

		$variable_set_cond=" and e.entry_form in (2,22,58)";

		if($db_type==0)
		{
			if($year_id!=0) $year_cond_trans=" and year(d.insert_date)=$year_id"; else $year_cond_trans="";
		}
		else if($db_type==2)
		{
			if($year_id!=0) $year_cond_trans=" and TO_CHAR(d.insert_date,'YYYY')=$year_id"; else $year_cond_trans="";
		}


		$order_no=str_replace("'","",$txt_order_id);
		if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

		$date_from=str_replace("'","",$txt_date_from);
		if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";

		//=================Order/Rack & Shelf Wise================
		$trans_order_cond="";
		if($cbo_sock_for==1)
		{
			$trans_order_cond=" and c.shiping_status<>3 and c.status_active=1";
		}
		else if($cbo_sock_for==2)
		{
			$trans_order_cond=" and c.status_active=3";
		}
		else if($cbo_sock_for==3)
		{
			$trans_order_cond=" and c.shiping_status=3 and c.status_active=1";
		}
		else
		{
			$trans_order_cond="";
		}

		$store_cond="";
		$cbo_store_name = str_replace("'","",$cbo_store_name);
		if( $cbo_store_name > 0 )
		{
			$trans_in_store_cond=" and b.to_store in($cbo_store_name)";
			$trans_out_store_cond=" and b.from_store in($cbo_store_name)";
			$return_store_cond=" and a.store_id in($cbo_store_name)";
			$issue_store_cond=" and c.store_id in($cbo_store_name)";
			$receive_store_cond=" and f.store_id in($cbo_store_name)";
		}

		$transfer_in_arr=array(); $trans_arr=array();
		$sql_transfer_in="select a.to_order_id, b.from_prod_id, b.to_rack, b.to_shelf, b.y_count, b.yarn_lot, sum(b.transfer_qnty) as transfer_in_qnty, sum(b.roll) as transfer_in_roll
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_po_break_down c, wo_po_details_master d
		where c.job_no_mst=d.job_no and a.to_order_id=c.id and a.company_id=$cbo_company_id and a.id=b.mst_id and a.transfer_criteria in(4,7) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond_trans $year_cond_trans $job_no_cond_trans $order_id_cond_trans $trans_order_cond $trans_in_store_cond
		group by a.to_order_id, b.from_prod_id, b.y_count, b.yarn_lot, b.to_rack, b.to_shelf";

		$data_transfer_in_array=sql_select($sql_transfer_in);
		if(count($data_transfer_in_array)>0)
		{
			foreach( $data_transfer_in_array as $row )
			{
				if($row[csf('to_shelf')]=="") $row[csf('to_shelf')]=0;

				$transfer_in_arr[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('to_rack')]][$row[csf('to_shelf')]]['qty']=$row[csf('transfer_in_qnty')];
				$transfer_in_arr[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('to_rack')]][$row[csf('to_shelf')]]['roll']=$row[csf('transfer_in_roll')];

				$trans_data=$row[csf('to_order_id')]."_".$row[csf('from_prod_id')]."_".$row[csf('y_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('to_rack')]."_".$row[csf('to_shelf')];

				$trans_arr[]=$trans_data;
			}
		}

		$product_array=array();
		$prod_query="Select id, detarmination_id, gsm, dia_width, brand, yarn_count_id, lot, color from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
		$prod_query_sql=sql_select($prod_query);
		if(count($prod_query_sql)>0)
		{
			foreach( $prod_query_sql as $row )
			{
				$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
				$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
				$product_array[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
				$product_array[$row[csf('id')]]['lot']=$row[csf('lot')];
				$product_array[$row[csf('id')]]['color']=$row[csf('color')];
			}
		}

		$transfer_out_arr=array();
		$sql_transfer_out="select b.from_order_id, b.from_prod_id, b.rack, b.shelf, b.y_count, b.yarn_lot, sum(b.transfer_qnty) as transfer_out_qnty, sum(b.roll) as transfer_out_roll from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.transfer_criteria in(1,2,3,4,5,6) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $trans_out_store_cond group by b.from_order_id, b.from_prod_id, b.y_count, b.yarn_lot, b.rack, b.shelf ";

		$data_transfer_out_array=sql_select($sql_transfer_out);
		if(count($data_transfer_out_array)>0)
		{
			foreach( $data_transfer_out_array as $row )
			{
				if($row[csf('shelf')]=="") $row[csf('shelf')]=0;

				$transfer_out_arr[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('shelf')]]['qty']=$row[csf('transfer_out_qnty')];
				$transfer_out_arr[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('shelf')]]['roll']=$row[csf('transfer_out_roll')];

				$trans_data=$row[csf('from_order_id')]."_".$row[csf('from_prod_id')]."_".$row[csf('y_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('rack')]."_".$row[csf('shelf')];

				if(!in_array($trans_data,$trans_arr))
				{
					$trans_arr[]=$trans_data;
				}
			}
		}

		$transaction_date_array=array();
		$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 group by prod_id";
		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}

		$retn_arr=array(); $retn_data_arr=array();

		$return_order_cond="";
		if($cbo_sock_for==1)
		{
			$return_order_cond=" and c.shiping_status<>3 and c.status_active=1";
		}
		else if($cbo_sock_for==2)
		{
			$return_order_cond=" and c.status_active=3";
		}
		else if($cbo_sock_for==3)
		{
			$return_order_cond=" and c.shiping_status=3 and c.status_active=1";
		}
		else
		{
			$return_order_cond="";
		}

		$sql_trans_sql=sql_select("Select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(61) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type  in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id");
		$issue_arr=array();
		foreach ($sql_trans_sql as $row) {
			$issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][2]["issueQnty"]=$row[csf('qnty')];
		}

		$sql_retn="select b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot,a.store_id, a.rack, a.self, sum(case when a.transaction_type=4 and b.trans_type=4 and b.entry_form in(51,84) then b.quantity end) as iss_rtn_qty, sum(case when a.transaction_type=3 and b.trans_type=3 and b.entry_form=45 then b.quantity end) as rcv_rtn_qty,
		sum(case when a.transaction_type in(6) and b.trans_type in(6) and b.entry_form in(82) then b.quantity end) as transferOut,
		sum(case when a.transaction_type in(5) and b.trans_type in(5) and b.entry_form in(82) then b.quantity end) as transferIn
		from inv_transaction a, order_wise_pro_details b, wo_po_break_down c
		where a.id=b.trans_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(45,51,81,82,83,84) and a.company_id=$cbo_company_id and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6) $return_order_cond $return_store_cond
		group by b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count,a.store_id, a.rack, a.self";
		$data_retn_array=sql_select($sql_retn);
		$transf_data_arr=array();
		foreach($data_retn_array as $row )
		{
			if($row[csf('self')]=="") $row[csf('self')]=0;
			$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss']=$row[csf('iss_rtn_qty')];
			$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['recv']=$row[csf('rcv_rtn_qty')];

			$transf_data_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]][5]['trnsfIn']=$row[csf('transferIn')];
			$transf_data_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]][6]['trnsfOut']=$row[csf('transferOut')];

			$rtn_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('batch_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];
			$retn_data_arr[]=$rtn_data;
		}

		ob_start();
		?>
		<fieldset>
			<table cellpadding="0" cellspacing="0" width="2750">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="36" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="36" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="36" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2750" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th colspan="14">Fabric Details</th>

						<th colspan="3">Used Yarn Details</th>
						<th width="120" rowspan="2">Receive Basis (BK/PLN/ PI/GPE)</th>
						<th width="80" rowspan="2">Req. Qty.</th>
						<th colspan="5">Receive Details</th>
						<th colspan="5">Issue Details</th>
						<th colspan="7">Stock Details</th>
					</tr>
					<tr>
						<th width="80">Job No.</th>
						<th width="80">Buyer</th>
						<th width="80">Order No.</th>
						<th width="80">Style Ref</th>
						<th width="100">Program No.</th>
						<th width="100">Booking No.</th>
						<th width="100">Construction</th>
						<th width="100">Composition</th>
						<th width="60">GSM</th>
						<th width="60">F/Dia</th>
						<th width="60">M/Dia</th>
						<th width="60">Stich Length</th>
						<th width="80">Dyeing Color</th>
						<th width="80">Color Range</th>

						<th width="60">Y. Count</th>
						<th width="80">Y. Brand</th>
						<th width="80">Y. Lot</th>
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="60">Recv. Roll</th>
						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="60">Issue Roll</th>
						<th width="80">Stock Qty.</th>
						<th width="60">Roll Qty.</th>
						<th width="50">Rack</th>
						<th width="50">Shelf</th>
						<th width="50">DOH</th>
						<th width="50">Recv. Balance</th>
						<th>Issue Balance</th>
					</tr>
				</thead>
			</table>
			<div style="width:2750px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="2730" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
					<?
					$issue_qty_roll_array=array(); $isuue_data_arr=array();
					$issue_order_cond="";
					if($cbo_sock_for==1)
					{
						$issue_order_cond=" and p.shiping_status<>3 and p.status_active=1";
					}
					else if($cbo_sock_for==2)
					{
						$issue_order_cond=" and p.status_active=3";
					}
					else if($cbo_sock_for==3)
					{
						$issue_order_cond=" and p.shiping_status=3 and p.status_active=1";
					}
					else
					{
						$issue_order_cond="";
					}

					$sql_issue="Select a.po_breakdown_id, a.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, sum(a.quantity ) as issue_qnty, sum(b.no_of_roll) as issue_roll,c.store_id
					from wo_po_break_down p, order_wise_pro_details a, inv_grey_fabric_issue_dtls b,inv_transaction c
					where p.id=a.po_breakdown_id and a.dtls_id=b.id and b.trans_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(16) $issue_order_cond and c.status_active=1 and c.transaction_type=2
					group by a.po_breakdown_id, a.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self,c.store_id
					union all
					select a.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, sum(a.qnty ) as issue_qnty, count(a.id) as issue_roll,c.store_id
					from wo_po_break_down p, pro_roll_details a, inv_grey_fabric_issue_dtls b,inv_transaction c
					where p.id=a.po_breakdown_id and a.dtls_id=b.id and b.trans_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(61) $issue_order_cond $issue_store_cond and c.status_active=1 and c.transaction_type=2
					group by a.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self,c.store_id ";
					$result_sql_issue=sql_select( $sql_issue );
					foreach ($result_sql_issue as $row)
					{
						if($row[csf('self')]=="") $row[csf('self')]=0;

						$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty']=$row[csf('issue_qnty')];
						$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll']=$row[csf('issue_roll')];
						$issue_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];

						$isuue_data_arr[]=$issue_data;
					}

					if($db_type==0)
					{
						$sql_dtls="select a.po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no,
						sum(c.quantity) as quantity, a.id as po_breakdown_id, c.prod_id, group_concat(d.stitch_length) as stitch_length,
						group_concat(d.brand_id) as brand_id, group_concat(d.color_id) as color_id, max(d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, d.rack, d.self, max(d.machine_no_id) as machine_no_id,

						case when e.entry_form=58 then count(d.id) when e.entry_form in (2,22) then sum(d.no_of_roll) else 0 end as rec_roll,
						group_concat(e.booking_no) as booking_no,e.entry_form,f.store_id
						from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d,inv_transaction f, inv_receive_master e
						where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0
						and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58)
						and c.po_breakdown_id=a.id
						and c.dtls_id=d.id
						and d.trans_id=f.id and f.mst_id=e.id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id and e.item_category=13
						and e.status_active=1 and e.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $receive_date $variable_set_cond $order_cond $po_id_cond $receive_store_cond and f.status_active=1
						group by a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no, e.entry_form,f.store_id order by a.id,a.po_number, c.prod_id";
					}
					else if($db_type==2)
					{
						echo $sql_dtls="select a.po_number as po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no,
						sum(c.quantity) as quantity, a.id as po_breakdown_id, c.prod_id,
						listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, listagg(d.brand_id,',') within group (order by d.brand_id) as brand_id, listagg(d.color_id,',') within group (order by d.color_id) as color_id, max(d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, d.rack, d.self, max(d.machine_no_id) as machine_no_id,
						case when e.entry_form=58 then count(d.id) when e.entry_form in (2,22) then sum(d.no_of_roll) else 0 end as rec_roll,
						listagg(e.booking_no,',') within group (order by e.booking_no) as booking_no,e.entry_form,f.store_id
						from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c,pro_grey_prod_entry_dtls d,inv_transaction f,inv_receive_master e
						where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0
						and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58)
						and c.po_breakdown_id=a.id
						and c.dtls_id=d.id
						and d.trans_id=f.id and f.mst_id=e.id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id
						and e.item_category=13 and e.status_active=1 and e.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $receive_date $variable_set_cond $order_cond $po_id_cond $receive_store_cond and f.status_active=1
						group by a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no,e.entry_form,f.store_id order by a.id,a.po_number, c.prod_id";
					}

					$nameArray=sql_select( $sql_dtls ); $total_grey_qnty=0;
					$i=1; $k=1; $m=1; $order_arr=array(); $trnsfer_in_qty=0; $trans_in_array=array(); $issue_array=array(); $return_array=array();$ttt=1;$rece_data_arr=array();
					foreach ($nameArray as $row)
					{
						$prod_id=$row[csf("prod_id")];
						$order_id=$row[csf("po_breakdown_id")];
						$yarn_count=$row[csf('yarn_count')];
						$yarn_lot=$row[csf('yarn_lot')];
						$rack=$row[csf("rack")];

						if($row[csf('self')]=="") $selfd==0;
						else $selfd=$row[csf("self")];

						$count_id=explode(',',$yarn_count); $count_val='';
						foreach ($count_id as $val)
						{
							if($val>0){ if($count_val=='') $count_val=$count_arr[$val]; else $count_val.=",".$count_arr[$val]; }
						}

						$color_id=array_unique(explode(',',$row[csf('color_id')])); $color_name='';$colorId_string='';
						$rece_data_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['recvQnty']=$row[csf('quantity')];

						foreach ($color_id as $val)
						{
							if($val>0)
							{
								if($color_name=='')
								{
									$color_name=$color_arr[$val];
									$colorId=$val;
								}
								else
								{
									$color_name.=",".$color_arr[$val];
								}
							}
						}

						$brand_id=array_unique(explode(',',$row[csf('brand_id')])); $brand_name="";
						foreach ($brand_id as $val)
						{
							if($val>0){ if($brand_name=='') $brand_name=$brand_arr[$val]; else $brand_name.=",".$brand_arr[$val]; }
						}

						$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('po_breakdown_id')]][$prod_id][$yarn_count][$yarn_lot][$rack][$selfd])));
						$trans_data_in=$order_id."_".$prod_id."_".$yarn_count."_".$yarn_lot."_".$rack."_".$selfd;
						$trans_in_array[]=$trans_data_in;
						$issue_array[]=$trans_data_in;
						$return_array[]=$trans_data_in;

						if(!in_array($row[csf('po_breakdown_id')],$order_arr))
						{
							if($k!=1)
							{
								foreach($trans_arr as $key=>$val2)
								{
									$value=explode("_",$val2);
									$po_id=$value[0];

									$count=explode(',',$value[2]); $count_value='';
									foreach ($count as $count_id)
									{
										if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
									}

									if($po_id==$prev_order_id)
									{
										if(!in_array($val2,$trans_in_array))
										{
											$trnsfer_in_qty=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
											$trnsfer_in_roll=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
											$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
											$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
											$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
											$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];

											$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
											$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];

											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
											$rec_bal=$trnsfer_in_qty+$issue_retn; $issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
											$stock=$rec_bal-$issue_bal;
											if($cbo_value_with==1 && $stock>=0)
											{
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?></p></td>
													<td width="100"><p><? echo $program_no; ?></p></td>
													<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$po_id]['booking_no'],",")))); ?></p></td>
													<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p></p></td>
													<td width="60"><p></p></td>
													<td width="80"><p></p></td>
													<td width="80"><p></p></td>

													<td width="60"><p><? echo $count_value; ?></p></td>
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
													<td width="80"><p><? echo $value[3]; ?></p></td>
													<td width="120"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2);$tot_transfer_in_qty+=$trnsfer_in_qty;?></p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal;?></p></td>
													<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
													<td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
													<?
													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="" align="center"></td>
												</tr>
												<?
												$i++;
												$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
												$grand_tot_rec_bal+=$rec_bal;
												$grand_tot_rec_roll+=$rec_roll;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_recv_retn_qty+=$recv_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;

												$issue_array[]=$val2;
												$return_array[]=$val2;


												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];
											}
											else if($cbo_value_with==2 && $stock>0)
											{
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">


													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?></p></td>
													<td width="100"><p><? echo $program_no; ?></p></td>
													<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$po_id]['booking_no'],",")))); ?></p></td>
													<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p></p></td>
													<td width="60"><p></p></td>
													<td width="80"><p></p></td>
													<td width="80"><p></p></td>

													<td width="60"><p><? echo $count_value; ?></p></td>
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
													<td width="80"><p><? echo $value[3]; ?></p></td>
													<td width="120" align="right"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2);$tot_transfer_in_qty+=$trnsfer_in_qty;?></p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal;?></p></td>
													<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
													<td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
													<?
													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="" align="center"></td>
												</tr>

												<?
												$i++;
												$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
												$grand_tot_rec_bal+=$rec_bal;
												$grand_tot_rec_roll+=$rec_roll;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_recv_retn_qty+=$recv_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;

												$issue_array[]=$val2;
												$return_array[]=$val2;

												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];
											}
										}
									}
								}

								foreach($isuue_data_arr as $key=>$val2)
								{
									$value=explode("_",$val2);
									$po_id=$value[0];

									$count=explode(',',$value[2]); $count_value='';
									foreach ($count as $count_id)
									{
										if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
									}

									if($po_id==$prev_order_id)
									{
										if(!in_array($val2,$issue_array))
										{
											$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
											$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
											$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
											$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];

											$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];

											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
											$rec_bal=$issue_retn; $issue_bal=$issue_qty+$trnsfer_out_qty;
											$stock=$rec_bal-$issue_bal;
											if($cbo_value_with==1 && $stock>=0)
											{
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">


													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?></p></td>
													<td width="100"><p><? echo $program_no; ?></p></td>
													<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$po_id]['booking_no'],",")))); ?></p></td>
													<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p></p></td>
													<td width="60"><p></p></td>
													<td width="80"><p></p></td>
													<td width="80"><p></p></td>

													<td width="60"><p><? echo $count_value; ?></p></td>
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
													<td width="80"><p><? echo $value[3]; ?></p></td>
													<td width="120"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
													<td width="60" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
													<?
													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="" align="center"></td>
												</tr>
												<?
												$i++;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;

												$return_array[]=$val2;

												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];
											}
											else if($cbo_value_with==2 && $stock>0)
											{
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?></p></td>
													<td width="100"><p><? echo $program_no; ?></p></td>
													<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$po_id]['booking_no'],",")))); ?></p></td>
													<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p></p></td>
													<td width="60"><p></p></td>
													<td width="80"><p></p></td>
													<td width="80"><p></p></td>

													<td width="60"><p><? echo $count_value; ?></p></td>
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
													<td width="80"><p><? echo $value[3]; ?></p></td>
													<td width="120"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
													<td width="60" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
													<?
													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="" align="center"></td>
												</tr>
												<?
												$i++;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;

												$return_array[]=$val2;

												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];
											}
										}
									}
								}

								foreach($retn_data_arr as $key=>$val3)
								{
									$value=explode("_",$val3);
									$po_id=$value[0];

									$count=explode(',',$value[2]); $count_value='';
									foreach ($count as $count_id)
									{
										if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
									}

									if($po_id==$prev_order_id)
									{
										if(!in_array($val3,$return_array))
										{
											$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
											$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
											$trnsfIn=$transf_data_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]][5]['trnsfIn'];
											$trnsfOut=$transf_data_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]][6]['trnsfOut'];

											$recvQnty=$rece_data_arr[$po_id][$value[1]]['recvQnty'];
											$issueQnty=$issue_arr[$po_id][$value[1]][2]["issueQnty"];

											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
											$rec_bal=$issue_retn; $issue_bal=$recv_retn;
											$stock=$rec_bal-$issue_bal;
											if($cbo_value_with==1 && $stock>=0)
											{
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?></p></td>
													<td width="100"><p><? echo $program_no; ?></p></td>
													<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$po_id]['booking_no'],",")))); ?></p></td>
													<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p></p></td>
													<td width="60"><p></p></td>
													<td width="80"><p></p></td>
													<td width="80"><p></p></td>

													<td width="60"><p><? echo $count_value; ?></p></td>
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
													<td width="80"><p><? echo $value[3]; ?></p></td>
													<td width="120" align="right"><p></p></td>
													<td width="80" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($recvQnty,2);$tot_rec_qty+=$recvQnty; ?></p></td>
													<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfIn,2); $tot_transfer_in_qty+=$trnsfIn; ?></p></td>
													<td width="80" align="right"><p><? $RecQnty=$recvQnty+$issue_retn+$trnsfIn;echo number_format($RecQnty,2);$tot_rec_bal+=$RecQnty;?></p></td>
													<td width="60" align="right"><p></p></td>
													<td width="80" align="right"><p><? echo number_format($issueQnty,2);$tot_issue_qty+=$issueQnty;?></p></td>
													<td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfOut,2); $tot_transfer_out_qty+=$trnsfOut; ?></p></td>
													<td width="80" align="right"><p><? $issueQnt=$issueQnty+$recv_retn+$trnsfOut; echo number_format($issueQnt,2);$tot_issue_bal+=$issueQnt; ?></p></td>
													<td width="60" align="right"></td>
													<td width="80" align="right"><p><? $stockQnty=$RecQnty-$issueQnt; echo number_format($stockQnty,2);$tot_stock+=$stockQnty; ?></p></td>
													<td width="60" align="right"><p></p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
													<?
													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="" align="center"></td>
												</tr>
												<?
											}
											else if($cbo_value_with==2 && $stock>0)
												{ ?>
													<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

														<td width="30"><? echo $i; ?></td>
														<td width="80"><p><? echo $job_no;?></p></td>
														<td width="80"><p><? echo $buyer_name; ?></p></td>
														<td width="80"><p><? echo $po_number; ?></p></td>
														<td width="80"><p><? echo $style_ref_no; ?></p></td>
														<td width="100"><p><? echo $program_no; ?></p></td>
														<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$po_id]['booking_no'],",")))); ?></p></td>
														<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
														<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
														<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
														<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
														<td width="60"><p></p></td>
														<td width="60"><p></p></td>
														<td width="80"><p></p></td>
														<td width="80"><p></p></td>

														<td width="60"><p><? echo $count_value; ?></p></td>
														<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
														<td width="80"><p><? echo $value[3]; ?></p></td>
														<td width="120" align="right"><p></p></td>
														<td width="80" align="right"><p></p></td>
														<td width="80" align="right"><p></p></td>
														<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
														<td width="80" align="right"><p></p></td>
														<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
														<td width="60" align="right"><p></p></td>
														<td width="80" align="right"><p></p></td>
														<td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
														<td width="80" align="right"><p></p></td>
														<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
														<td width="60" align="right"></td>
														<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
														<td width="60" align="right"><p></p></td>
														<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
														<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
														<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
														<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
														<td width="50" align="center"></td>
														<td width="50" align="center"></td>
													</tr>
													<?
												}
												$i++;

												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_recv_retn_qty+=$recv_retn;
												$grand_tot_stock+=$stock;
												$grand_tot_rec_bal+=$rec_bal;
												$grand_tot_issue_bal+=$issue_bal;
												$return_array[]=$val3;

												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];
												$stich_length_string=implode(",",array_unique(explode(",",$row[csf('stitch_length')])));
											}
										}
									}
								}
								$order_arr[]=$row[csf('po_breakdown_id')];
								$k++;
							}

							//--------------------------------------------------------------------------start
							$job_no=$row[csf('job_no')];
							$style_ref_no=$row[csf('style_ref_no')];
							$buyer_name=$buyer_arr[$row[csf('buyer_name')]];
							$buyer_id=$row[csf('buyer_name')];
							$po_number=$row[csf('po_number')];
							$break_down_id=$row[csf('po_breakdown_id')];
							$construction_data=$constructionArr[$product_array[$prod_id]['detarmination_id']];
							$dyeing_color_string=$color_name;
							$colorId_string=$colorId;

							$groupKey = $buyer_id.$job_no.$break_down_id.$construction_data.$colorId_string;
							$groupKeyReq = $buyer_id_prv.$job_no_prv.$break_down_id_prv.$construction_data_prv.$colorId_string_prv;
							$grey_qnty=$grey_qnty_array[$groupKeyReq];

							if(!in_array($groupKey,$con_arr)){
								if($ttt!=1)
								{
									$total_grey_qnty+=$grey_qnty;
									?>
									<tr class="tbl_bottom">

										<td width="30"></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $job_no_prv;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $buyer_name_prv;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $po_number_prv;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $style_ref_no_prv;?></p></td>
										<td width="100"></td>
										<td width="100"></td>
										<td width="100" style="color:#E2E2E2;"><p><? echo $construction_data_prv;?></p></td>
										<td width="100" style="color:#E2E2E2;"><p><? echo $composition_string_prv;?></p></td>
										<td width="60" style="color:#E2E2E2;"><p><? echo $gsm_string;?></p></td>
										<td width="60" style="color:#E2E2E2;"><p><? echo $fdia_string;?></p></td>
										<td width="60"></td>
										<td width="60"></td>
										<td width="80" style="color:#E2E2E2;"></td>
										<td width="80" style="color:#E2E2E2;"><p><? echo $dyeing_color_string_prv;?></p></td>

										<td width="60"></td>
										<td width="80"></td>
										<td width="80"></td>
										<td width="120"></td>
										<td width="80" align="right"><p><b><? echo number_format($grey_qnty,2,'.','');?></b></p></td>
										<td width="80" align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
										<td width="80"  align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?></td>
										<td width="80" align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?></td>
										<td width="80" align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?></td>
										<td width="60" align="right"><? echo $tot_rec_roll; ?></td>
										<td width="80" align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?></td>
										<td width="80" align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?></td>
										<td width="80" align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?></td>
										<td width="80" align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?></td>
										<td width="60" align="right"><? echo $tot_issue_roll; ?></td>
										<td width="80" align="right"><? echo number_format($tot_stock,2,'.',''); ?></td>
										<td width="60" align="right"></td>
										<td width="50" align="right"></td>
										<td width="50" align="right"></td>
										<td width="50" align="right"></td>
										<td  width="50" align="right"><? echo number_format($grey_qnty-$tot_rec_bal,2,'.',''); ?></td>
										<td  width="" align="right"><? echo number_format($grey_qnty-$tot_issue_bal,2,'.',''); ?></td>
									</tr>
									<?
									unset($colorData);
									unset($colorId_string);
									unset($grey_qnty);
									unset($dyeing_color_string);
									unset($colorId_string);
									unset($break_down_id);
									unset($tot_req_qty);
									unset($tot_rec_qty);
									unset($tot_transfer_in_qty);
									unset($tot_rec_bal);
									unset($tot_rec_roll);
									unset($tot_issue_qty);
									unset($tot_transfer_out_qty);
									unset($tot_issue_bal);
									unset($tot_issue_roll);
									unset($tot_stock);
									unset($tot_stock_roll_qty);
									unset($tot_iss_retn_qty);
									unset($tot_recv_retn_qty);
								}
								$ttt++;

							}
							$con_arr[]=$groupKey;
							//-----------------------------------------------------------------------------end

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($row[csf('self')]=="") $row[csf('self')]=0;
							$trnsfer_in_qty=$transfer_in_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$trnsfer_in_roll=$transfer_in_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];
							$trnsfer_out_qty=$transfer_out_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$trnsfer_out_roll=$transfer_out_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];

							$issue_qty=$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$issue_roll=$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];


							$issue_retn=$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss'];
							$recv_retn=$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['recv'];

							$rec_bal=$row[csf('quantity')]+$trnsfer_in_qty+$issue_retn;
							$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
							$stock=$rec_bal-$issue_bal;

							if($cbo_value_with==1 && $stock>=0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

									<td width="30"><? echo $i; ?></td>
									<td width="80"><p><? echo $job_no;?></p></td>
									<td width="80"><p><? echo $buyer_name; ?></p></td>
									<td width="80"><p><? echo $po_number; ?></p></td>
									<td width="80"><p><? echo $style_ref_no; ?></p></td>
									<td width="100"><p><? echo $program_no; ?></p></td>
									<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$row[csf('po_breakdown_id')]]['booking_no'],",")))); ?></p></td>
									<td width="100"><p><? echo $constructionArr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
									<td width="100"><p><? echo $copmpositionArr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
									<td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['gsm']; ?></p></td>
									<td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['dia_width']; ?></p></td>
									<td width="60"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
									<td width="60"><p><? echo implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?></p></td>
									<td width="80"><p><? echo $color_name; ?></p></td>
									<td width="80"><p><? echo $color_range[$row[csf('color_range_id')]]; ?></p></td>

									<td width="60"><p><? echo $count_val; ?></p></td>
									<td width="80"><p><? echo $brand_name; ?></p></td>
									<td width="80"><p><? echo $row[csf('yarn_lot')]; ?></p></td>

									<td width="120"><p></p></td>
									<td width="80"><p></p></td>
									<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); $tot_rec_qty+=$row[csf('quantity')]; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?></p></td>
									<td width="60" align="right"><p><? $rec_roll=$row[csf('rec_roll')]+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>

									<td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
									<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
									<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?></p></td>
									<td width="50" align="center"><p><? echo $row[csf('rack')]; ?></p></td>
									<td width="50" align="center"><p><? echo $row[csf('self')]; ?></p></td>
									<?
									$daysOnHand = datediff("d",change_date_format($transaction_date_array[$row[csf('prod_id')]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
									<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
									<td width="50" align="center"></td>
									<td width="" align="center"></td>
								</tr>
								<?
								$prev_order_id=$row[csf('po_breakdown_id')];
								$i++;
								$grand_tot_rec_qty+=$row[csf('quantity')];
								$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
								$grand_tot_rec_bal+=$rec_bal;
								$grand_tot_rec_roll+=$rec_roll;
								$grand_tot_issue_qty+=$issue_qty;
								$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
								$grand_tot_issue_bal+=$issue_bal;
								$grand_tot_issue_roll+=$iss_roll;
								$grand_tot_stock+=$stock;
								$grand_tot_roll_qty+=$stock_roll_qty;
								$grand_tot_issue_retn_qty+=$issue_retn;
								$grand_tot_recv_retn_qty+=$recv_retn;

								$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
								$gsm_string=$product_array[$value[1]]['gsm'];
								$fdia_string=$product_array[$value[1]]['dia_width'];

								$colorId_string_prv=$colorId;
								$dyeing_color_string_prv=$color_name;
								$job_no_prv=$row[csf('job_no')];
								$style_ref_no_prv=$row[csf('style_ref_no')];
								$buyer_name_prv=$buyer_arr[$row[csf('buyer_name')]];
								$buyer_id_prv=$row[csf('buyer_name')];
								$po_number_prv=$row[csf('po_number')];
								$break_down_id_prv=$row[csf('po_breakdown_id')];
								$construction_data_prv=$constructionArr[$product_array[$prod_id]['detarmination_id']];
							}
							else if($cbo_value_with==2 && $stock>0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="80"><p><? echo $job_no;?></p></td>
									<td width="80"><p><? echo $buyer_name; ?></p></td>
									<td width="80"><p><? echo $po_number; ?></p></td>
									<td width="80"><p><? echo $style_ref_no; ?></p></td>
									<td width="100"><p><? echo $program_no; ?></p></td>
									<td width="100"><p><? echo implode(",",array_unique(explode(",",chop($booking_array[$row[csf('po_breakdown_id')]]['booking_no'],",")))); ?></p></td>
									<td width="100"><p><? echo $constructionArr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
									<td width="100"><p><? echo $copmpositionArr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
									<td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['gsm']; ?></p></td>
									<td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['dia_width']; ?></p></td>
									<td width="60"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
									<td width="60"><p><? echo implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?></p></td>
									<td width="80"><p><? echo $color_name; ?></p></td>
									<td width="80"><p><? echo $color_range[$row[csf('color_range_id')]]; ?></p></td>

									<td width="60"><p><? echo $count_val; ?></p></td>
									<td width="80"><p><? echo $brand_name; ?></p></td>
									<td width="80"><p><? echo $row[csf('yarn_lot')]; ?></p></td>
									<td width="120"><p><? echo implode(",",array_unique(explode(",",$row[csf('booking_no')]))); ?></p></td>
									<td width="80"><p></p></td>
									<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); $tot_rec_qty+=$row[csf('quantity')]; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?></p></td>
									<td width="60" align="right"><p><? $rec_roll=$row[csf('rec_roll')]+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
									<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
									<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?></p></td>
									<td width="50" align="center"><p><? echo $row[csf('rack')]; ?></p></td>
									<td width="50" align="center"><p><? echo $row[csf('self')]; ?></p></td>
									<?
									$daysOnHand = datediff("d",change_date_format($transaction_date_array[$row[csf('prod_id')]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
									<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
									<td width="50" align="center"></td>
									<td width="" align="center"></td>
								</tr>
								<?
								$prev_order_id=$row[csf('po_breakdown_id')];
								$i++;
								$grand_tot_rec_qty+=$row[csf('quantity')];
								$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
								$grand_tot_rec_bal+=$rec_bal;
								$grand_tot_rec_roll+=$rec_roll;
								$grand_tot_issue_qty+=$issue_qty;
								$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
								$grand_tot_issue_bal+=$issue_bal;
								$grand_tot_issue_roll+=$iss_roll;
								$grand_tot_stock+=$stock;
								$grand_tot_roll_qty+=$stock_roll_qty;
								$grand_tot_issue_retn_qty+=$issue_retn;
								$grand_tot_recv_retn_qty+=$recv_retn;

								$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
								$gsm_string=$product_array[$value[1]]['gsm'];
								$fdia_string=$product_array[$value[1]]['dia_width'];

								$colorId_string_prv=$colorId;
								$dyeing_color_string_prv=$color_name;
								$job_no_prv=$row[csf('job_no')];
								$style_ref_no_prv=$row[csf('style_ref_no')];
								$buyer_name_prv=$buyer_arr[$row[csf('buyer_name')]];
								$buyer_id_prv=$row[csf('buyer_name')];
								$po_number_prv=$row[csf('po_number')];
								$break_down_id_prv=$row[csf('po_breakdown_id')];
								$construction_data_prv=$constructionArr[$product_array[$prod_id]['detarmination_id']];
							}
						}

						foreach($trans_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];

							if($po_id==$prev_order_id )
							{
								if(!in_array($val3,$trans_in_array))
								{
									$trnsfer_in_qty=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_in_roll=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];

									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
									$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];

									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$count=explode(',',$value[2]); $count_value='';
									foreach ($count as $count_id)
									{
										if($count_id>0) { if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
									}

									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$rec_bal=$trnsfer_in_qty+$issue_retn;
									$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
									$stock=$rec_bal-$issue_bal;
									if($cbo_value_with==1 && $stock>=0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="80"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_name; ?></p></td>
											<td width="80"><p><? echo $po_number; ?></p></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $program_no; ?></p></td>
											<td width="100"><p><? echo $booking_array[$po_id]['booking_no']; ?></p></td>
											<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"><p></p></td>
											<td width="60"><p></p></td>
											<td width="80"><p></p></td>
											<td width="80"><p></p></td>

											<td width="60"><p><? echo $count_value; ?></p></td>
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
											<td width="80"><p><? echo $value[3]; ?></p></td>
											<td width="120"><p></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?></p></td>
											<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?></p></td>
											<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?></p></td>
											<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></p></td>
											<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
											<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?></p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
											<?
											$daysOnHand=datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));								?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="50" align="center"><p></p></td>
											<td width="" align="center"><p></p></td>
										</tr>
										<?
										$i++;
										$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_rec_roll+=$rec_roll;
										$grand_tot_issue_qty+=$issue_qty;
										$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
										$grand_tot_issue_bal+=$issue_bal;
										$grand_tot_issue_roll+=$iss_roll;
										$grand_tot_stock+=$stock;
										$grand_tot_roll_qty+=$stock_roll_qty;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										$issue_array[]=$val3;
										$return_array[]=$val3;

										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];
									}
									else if($cbo_value_with==2 && $stock>0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="80"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_name; ?></p></td>
											<td width="80"><p><? echo $po_number; ?></p></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>

											<td width="100"><p><? echo $program_no; ?></p></td>
											<td width="100"><p><? echo $booking_array[$po_id]['booking_no']; ?></p></td>
											<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"><p></p></td>
											<td width="60"><p></p></td>
											<td width="80"><p></p></td>
											<td width="80"><p></p></td>

											<td width="60"><p><? echo $count_value; ?></p></td>
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
											<td width="80"><p><? echo $value[3]; ?></p></td>
											<td width="120"><p></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? //echo $val3;?></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?></p></td>
											<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?></p></td>
											<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?></p></td>
											<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></p></td>
											<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
											<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?></p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
											<?
											$daysOnHand=datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="50" align="center"><p></p></td>
											<td width="" align="center"><p></p></td>
										</tr>
										<?
										$i++;
										$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_rec_roll+=$rec_roll;
										$grand_tot_issue_qty+=$issue_qty;
										$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
										$grand_tot_issue_bal+=$issue_bal;
										$grand_tot_issue_roll+=$iss_roll;
										$grand_tot_stock+=$stock;
										$grand_tot_roll_qty+=$stock_roll_qty;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;

										$issue_array[]=$val3;
										$return_array[]=$val3;

										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];
									}
								}
							}
						}

						foreach($isuue_data_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];

							$count=explode(',',$value[2]); $count_value='';
							foreach ($count as $count_id)
							{
								if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
							}

							if($po_id==$prev_order_id)
							{
								if(!in_array($val3,$issue_array))
								{
									$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];

									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];

									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$rec_bal=$issue_retn; $issue_bal=$issue_qty+$trnsfer_out_qty;
									$stock=$rec_bal-$issue_bal;
									if($cbo_value_with==1 && $stock>=0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="80"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_name; ?></p></td>
											<td width="80"><p><? echo $po_number; ?></p></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $program_no; ?></p></td>
											<td width="100"><p><? echo $booking_array[$po_id]['booking_no']; ?></p></td>
											<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"><p></p></td>
											<td width="60"><p></p></td>
											<td width="80"><p></p></td>
											<td width="80"><p></p></td>

											<td width="60"><p><? echo $count_value; ?></p></td>
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
											<td width="80"><p><? echo $value[3]; ?></p></td>
											<td width="120"><p></p></td>
											<td width="80"><p></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
											<td width="60" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
											<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
											<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
											<td width="60" align="right"><p><? $stock_roll_qty=-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
											<?
											$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));	?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="50" align="center"></td>
											<td width="" align="center"></td>
										</tr>
										<?
										$i++;
										$grand_tot_issue_qty+=$issue_qty;
										$grand_tot_transfer_out_qty+=$trnsfer_out_qty;

										$grand_tot_issue_bal+=$issue_bal;
										$grand_tot_rec_bal+=$rec_bal;

										$grand_tot_issue_roll+=$iss_roll;
										$grand_tot_stock+=$stock;

										$grand_tot_roll_qty+=$stock_roll_qty;
										$grand_tot_issue_retn_qty+=$issue_retn;

										$return_array[]=$val3;

										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];
									}
									else if($cbo_value_with==2 && $stock>0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="80"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_name; ?></p></td>
											<td width="80"><p><? echo $po_number; ?></p></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $program_no; ?></p></td>
											<td width="100"><p><? echo $booking_array[$po_id]['booking_no']; ?></p></td>
											<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"></td>
											<td width="60"></td>
											<td width="80"></td>
											<td width="80"></td>

											<td width="60"><p><? echo $count_value; ?></p></td>
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
											<td width="80"><p><? echo $value[3]; ?></p></td>
											<td width="120"></td>
											<td width="80"></td>
											<td width="80" align="right"></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
											<td width="80" align="right"></td>
											<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
											<td width="60" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?></p></td>
											<td width="80" align="right"></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?></p></td>
											<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?></td>
											<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
											<td width="60" align="right"><p><? $stock_roll_qty=-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?></p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
											<?
											$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="50" align="center"></td>
											<td width="" align="center"></td>
										</tr>
										<?
										$i++;
										$grand_tot_issue_qty+=$issue_qty;
										$grand_tot_transfer_out_qty+=$trnsfer_out_qty;

										$grand_tot_issue_bal+=$issue_bal;
										$grand_tot_rec_bal+=$rec_bal;

										$grand_tot_issue_roll+=$iss_roll;
										$grand_tot_stock+=$stock;

										$grand_tot_roll_qty+=$stock_roll_qty;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_total_rec_qty+=$tot_rec_qty;

										$return_array[]=$val3;

										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];
									}
								}
							}
						}

						foreach($retn_data_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];

							$count=explode(',',$value[2]); $count_value='';
							foreach ($count as $count_id)
							{
								if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
							}

							if($po_id==$prev_order_id)
							{
								if(!in_array($val3,$return_array))
								{
									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
									$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];

									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$rec_bal=$issue_retn;  $issue_bal=$recv_retn;
									$stock=$rec_bal-$issue_bal;
									if($cbo_value_with==1 && $stock>=0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="80"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_name; ?></p></td>
											<td width="80"><p><? echo $po_number; ?></p></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $program_no; ?></p></td>
											<td width="100"><p><? echo $booking_array[$po_id]['booking_no']; ?></p></td>
											<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"><p></p></td>
											<td width="60"><p></p></td>
											<td width="80"><p></p></td>
											<td width="80"><p></p></td>


											<td width="60"><p><? echo $count_value; ?></p></td>
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
											<td width="80"><p><? echo $value[3]; ?></p></td>
											<td width="120"><p></p></td>
											<td width="80"><p></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
											<td width="60" align="right"><p></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"></td>
											<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
											<td width="60" align="right"><p></p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
											<?
											$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="50" align="center"></td>
											<td width="" align="center"></td>

										</tr>
										<?
										$i++;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										$grand_tot_stock+=$stock;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_issue_bal+=$issue_bal;

										$return_array[]=$val3;

										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];
									}
									else if($cbo_value_with==2 && $stock>0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

											<td width="30"><? echo $i; ?></td>
											<td width="80"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_name; ?></p></td>
											<td width="80"><p><? echo $po_number; ?></p></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $program_no; ?></p></td>
											<td width="100"><p><? echo $booking_array[$po_id]['booking_no']; ?></p></td>
											<td width="100"><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"><p></p></td>
											<td width="60"><p></p></td>
											<td width="80"><p></p></td>
											<td width="80"><p></p></td>

											<td width="60"><p><? echo $count_value; ?></p></td>
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?></p></td>
											<td width="80"><p><? echo $value[3]; ?></p></td>
											<td width="120"><p></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?></p></td>
											<td width="60" align="right"><p></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?></p></td>
											<td width="80" align="right"><p></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"></td>
											<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?></p></td>
											<td width="60" align="right"><p></p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?></p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?></p></td>
											<?
											$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="50" align="center"></td>
											<td width="" align="center"></td>
										</tr>
										<?
										$i++;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										$grand_tot_stock+=$stock;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_issue_bal+=$issue_bal;

										$return_array[]=$val3;

										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];
									}
								}
							}
						}

						$groupKeyReq = $buyer_id_prv.$job_no_prv.$break_down_id_prv.$construction_data_prv.$colorId_string_prv;
						$grey_qnty=$grey_qnty_array[$groupKeyReq];
						$total_grey_qnty+=$grey_qnty;
						?>
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $job_no;?></p></td>
							<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $buyer_name;?></p></td>
							<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $po_number;?></p></td>
							<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $style_ref_no;?></p></td>
							<td width="100"></td>
							<td width="100"><p></p></td>
							<td width="100" style="color:#E2E2E2;"><p><? echo $construction_data_prv;?></p></td>
							<td width="100" style="color:#E2E2E2;"><p><? echo $composition_string;?></p></td>
							<td width="60" style="color:#E2E2E2;"><p><? echo $gsm_string;?></p></td>
							<td width="60" style="color:#E2E2E2;"><p><? echo $fdia_string;?></p></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="80" style="color:#E2E2E2;"><p><? echo $dyeing_color_string_prv;?></p></td>
							<td width="80"></td>

							<td width="60"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="120"></td>
							<td width="80" align="right"><p><? echo number_format($grey_qnty,2,'.','');?></p></td>
							<td width="80" align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?></td>
							<td width="60" align="right"><? echo $tot_rec_roll; ?></td>
							<td width="80" align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?></td>
							<td width="60" align="right"><? echo $tot_issue_roll; ?></td>
							<td width="80" align="right"></td>
							<td width="60" align="right"></td>
							<td width="50" align="right"></td>
							<td width="50" align="right"></td>
							<td width="50" align="right"></td>
							<td width="50" align="right"><p><? echo number_format($grey_qnty-$tot_rec_bal,2,'.','');?></p></td>
							<td width="" align="right"><p><? echo number_format($grey_qnty-$tot_issue_bal,2,'.',''); ?></p></td>
						</tr>
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="80" style="color:#E2E2E2;" align="center"></td>
							<td width="80" style="color:#E2E2E2;" align="center"></td>
							<td width="80" style="color:#E2E2E2;" align="center"></td>
							<td width="80" style="color:#E2E2E2;" align="center"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" style="color:#E2E2E2;"></td>
							<td width="100" style="color:#E2E2E2;"></td>
							<td width="60" style="color:#E2E2E2;"></td>
							<td width="60" style="color:#E2E2E2;"></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="80" style="color:#E2E2E2;"></p></td>
							<td width="80"></td>

							<td width="60"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="120"></td>
							<td width="80" align="right"><p><? echo number_format($total_grey_qnty,2,'.','');?></p></td>
							<td width="80" align="right"><? echo number_format($grand_tot_rec_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($grand_tot_issue_retn_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($grand_tot_transfer_in_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($grand_tot_rec_bal,2,'.',''); ?></td>
							<td width="60" align="right"><? echo $grand_tot_rec_roll; ?></td>
							<td width="80" align="right"><? echo number_format($grand_tot_issue_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($grand_tot_recv_retn_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($grand_tot_transfer_out_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($grand_tot_issue_bal,2,'.',''); ?></td>
							<td width="60" align="right"><? echo $grand_tot_issue_roll; ?></td>
							<td width="80" align="right"><? echo number_format($grand_tot_stock,2,'.',''); ?></td>
							<td width="60" align="right"><? echo $grand_tot_roll_qty; ?></td>
							<td width="50" align="right"></td>
							<td width="50" align="right"></td>
							<td width="50" align="right"></td>
							<td width="50" align="right"><p><? echo number_format($total_grey_qnty-$grand_tot_rec_bal,2,'.',''); ?></p></td>
							<td width="" align="right"><p><? echo number_format($total_grey_qnty-$grand_tot_issue_qty,2,'.',''); ?></p></td>
						</tr>
					</table>
				</div>
			</fieldset>
			<?
		}

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
	echo "$html####$filename####$rpt_type";
	exit();
}

if($action=="fabric_booking_popup")
{
	echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	?>
	<fieldset style="width:890px">
		<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
			<thead>
				<th width="40">SL</th>
				<th width="60">Booking No</th>
				<th width="50">Year</th>
				<th width="60">Type</th>
				<th width="80">Booking Date</th>
				<th width="90">Color</th>
				<th width="110">Fabric</th>
				<th width="150">Composition</th>
				<th width="70">GSM</th>
				<th width="70">Dia</th>
				<th>Grey Req. Qty.</th>
			</thead>
		</table>
		<div style="width:100%; max-height:320px; overflow-y:scroll">
			<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
				<?
				if($db_type==0) $year_field="YEAR(a.insert_date) as year";
				else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		        else $year_field="";//defined Later

		        $i=1; $tot_grey_qnty=0;
		        $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_date, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.id, a.booking_type, a.is_short, a.booking_date, a.insert_date, a.booking_no_prefix_num, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width order by a.id";
		       //echo $sql;//die;
		        $result= sql_select($sql);
		        foreach($result as $row)
		        {
		        	if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

		        	if($row[csf('booking_type')]==4)
		        	{
		        		$booking_type="Sample";
		        	}
		        	else
		        	{
		        		if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main";
		        	}
		        	?>
		        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		        		<td width="40"><? echo $i; ?></td>
		        		<td width="60"><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
		        		<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
		        		<td width="60" align="center"><p><? echo $booking_type; ?></p></td>
		        		<td width="80" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?></td>
		        		<td width="90"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></p></td>
		        		<td width="110"><p><? echo $row[csf('construction')]; ?></p></td>
		        		<td width="150"><p><? echo $row[csf('copmposition')]; ?></p></td>
		        		<td width="70"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
		        		<td width="70"><p><? echo $row[csf('dia_width')]; ?></p></td>
		        		<td align="right" style="padding-right:5px"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
		        	</tr>
		        	<?
		        	$tot_grey_qnty+=$row[csf('grey_fab_qnty')];
		        	$i++;
		        }
		        ?>
		        <tfoot>
		        	<th colspan="10">Total</th>
		        	<th style="padding-right:5px"><? echo number_format($tot_grey_qnty,2); ?></th>
		        </tfoot>
		    </table>
		</div>
	</fieldset>
	<?
	exit();
}
if($action=="grey_recv_popup")
{
	echo load_html_head_contents("Grey Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$orderID=$orderID;
	$programNo=$programNo;
	$prodID=$prodID;
	$product_details_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
	$buyer_name_arr = return_library_array("select id, buyer_name from  lib_buyer", 'id', 'buyer_name');
	$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');

	?>
	<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<?
						if ($type==1) {$tbl_title='Grey Fabrics Receive Details';}else{$tbl_title='Grey Fabrics Issue Details';}
						?>
						<th colspan="12"><b><? echo $tbl_title; ?></b></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="70"><?if ($type==1) { echo 'Receive ID';}else{echo 'Issue ID';} ?></th>
						<th width="120"><?if ($type==1) { echo 'Receive Date';}else{echo 'Issue Date';} ?></th>
						<th width="200">Fabric Des</th>
						<th width="80">Store</th>
						<th width="80">Room</th>
						<th width="100">Rack </th>
						<th width="100">Shelf</th>
						<th width="60">Bin</th>
						<th width="60">UOM</th>
						<th width="60">Qty</th>
						<th>No of Roll</th>
					</tr>
				</thead>
			</table>
			<div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body">
					<?
					if ($type==1)
					{
						$programData=sql_select("select a.recv_number as sys_number,a.receive_date as sys_no_date,sum(b.cons_quantity) as grey_receive_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id
							from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d
							where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.entry_form in(2,22,58) and c.trans_id <>0 and c.trans_type=1 and c.entry_form in (2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.recv_number,a.receive_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
					}
					else
					{
						$programData=sql_select("select a.issue_number as sys_number,a.issue_date as sys_no_date,sum(b.cons_quantity) as grey_receive_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id from inv_issue_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d where a.id=b.mst_id and b.id=c.trans_id and c.dtls_id=d.id and a.entry_form in(16,61) and c.trans_id <>0 and c.trans_type=2 and c.entry_form in (16,61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.issue_number,a.issue_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
					}



					$i=1;
					foreach ($programData as $row) {

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td width="70"><p><? echo $row[csf('sys_number')]; ?></p></td>
							<td width="120"  align="center"><? echo $row[csf('sys_no_date')];// ?></td>
							<td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
							<td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]];//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
							<td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
							<td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
							<td width="60" align="right"><? echo $row[csf('grey_receive_qnty')]; ?></td>
							<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>

						</tr>
						<?
						$total_recv_qty+=$row[csf('grey_receive_qnty')];
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="10" align="right">Total</th>
							<th align="right"><? echo number_format($total_recv_qty,2); ?></th>
							<th align="right"></th>
						</tr>

					</tfoot>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<?
						if ($type==1) {$tbl_title='Grey Issue Return Details';}else{$tbl_title='Grey Receive Return Details';}
						?>
						<th colspan="12"><b><? echo $tbl_title; ?></b></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="70"><?if ($type==1) { echo 'Issue Return ID';}else{echo 'Receive Return ID';} ?></th>
						<th width="120"><?if ($type==1) { echo 'Issue Date';}else{echo 'Receive Date';} ?></th>
						<th width="200">Fabric Des</th>
						<th width="80">Store</th>
						<th width="80">Room</th>
						<th width="100">Rack </th>
						<th width="100">Shelf</th>
						<th width="60">Bin</th>
						<th width="60">UOM</th>
						<th width="60">Qty</th>
						<th>No of Roll</th>
					</tr>
				</thead>
			</table>
			<div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body2">
				<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body2">
					<?
					if ($type==1)
					{
						$programData=sql_select("select a.recv_number as sys_number,a.receive_date as sys_number_date,sum(b.cons_quantity) as grey_issue_rtn_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id,count(d.id) as roll_no
							from inv_receive_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d
							where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.dtls_id=d.id and a.entry_form in(84,51) and c.trans_id <>0 and c.entry_form in (84,51) and c.trans_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.recv_number,a.receive_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id");
					}
					else
					{
						$programData=sql_select("select a.issue_number as sys_number,a.issue_date as sys_number_date,sum(b.cons_quantity) as grey_issue_rtn_qnty,b.room,b.rack,b.self,b.bin_box,d.no_of_roll,b.store_id,b.cons_uom,c.po_breakdown_id,b.prod_id,count(d.id) as roll_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c,pro_grey_prod_entry_dtls d where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and a.entry_form in(45) and c.trans_id <>0 and c.entry_form in (45) and c.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=1 and c.po_breakdown_id in($orderID) and c.prod_id in($prodID) group by a.issue_number,a.issue_date,b.room,b.rack,b.self,b.bin_box,b.cons_uom,d.no_of_roll,b.store_id,c.po_breakdown_id,b.prod_id ");
					}

					$ii=1;
					foreach ($programData as $row) {
						$store_arr[$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
							<td width="30"><? echo $ii; ?></td>
							<td width="70"><p><? echo $row[csf('sys_number')]; ?></p></td>
							<td width="120"  align="center"><? echo $row[csf('sys_number_date')];// ?></td>
							<td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
							<td width="80"><div style="word-break:break-all"><? echo $store_name_arr[$row[csf('store_id')]];//$buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
							<td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
							<td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
							<td width="60" align="right"><? echo $row[csf('grey_issue_rtn_qnty')]; ?></td>
							<td align="right"><? echo $row[csf("roll_no")]; ?></td>

						</tr>
						<?
						$total_issue_rtn_qty+=$row[csf('grey_issue_rtn_qnty')];
						$ii++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="10" align="right">Total</th>
							<th align="right"><? echo number_format($total_issue_rtn_qty,2); ?></th>
							<th align="right"></th>
						</tr>

					</tfoot>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<?
						if ($type==1) {$tbl_title='Grey Transfer In Details';}else{$tbl_title='Grey Transfer Out Details';}
						?>
						<th colspan="12"><b><? echo $tbl_title; ?></b></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="70">Transfer ID</th>
						<th width="120">Receive Date</th>
						<th width="200">Fabric Des</th>
						<th width="80">Store</th>
						<th width="80">Room</th>
						<th width="100">Rack </th>
						<th width="100">Shelf</th>
						<th width="60">Bin</th>
						<th width="60">UOM</th>
						<th width="60">Qty</th>
						<th>No of Roll</th>
					</tr>
				</thead>
			</table>
			<div style="width:1050px; max-height:410px; overflow-y:scroll" id="scroll_body3">
				<table border="1" class="rpt_table" rules="all" width="1030" cellpadding="0" cellspacing="0" id="table_body3">
					<?
					if ($type==1)
					{
						//$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.to_trans_id=c.trans_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll");
						$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no ,sum(d.qnty) as roll_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d where a.id=b.mst_id and b.to_trans_id=c.trans_id and b.id=d.dtls_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=1 and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll,d.roll_no");


					}
					else
					{
						//$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.trans_id=c.trans_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll");

						$programData=sql_select("select a.transfer_system_id,a.transfer_date,a.to_store_id,sum(b.transfer_qnty) as transfer_qnty,b.to_rack as rack,b.to_shelf as self,b.bin_box,b.uom,b.no_of_roll,c.po_breakdown_id,b.from_prod_id as prod_id,d.roll_no,sum(d.qnty) as roll_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,pro_roll_details d  where a.id=b.mst_id and b.trans_id=c.trans_id and  b.id=d.dtls_id and a.entry_form in(83) and c.trans_id <>0 and c.entry_form in (83) and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$companyID  and c.prod_id in($prodID) group by c.po_breakdown_id,b.from_prod_id,a.transfer_system_id,a.transfer_date,a.to_store_id,b.to_rack,b.to_shelf,b.bin_box,b.uom,b.no_of_roll,d.roll_no");
					}



					$iii=1;
					foreach ($programData as $row) {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $iii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $iii;?>">
							<td width="30"><? echo $iii; ?></td>
							<td width="70"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="120"  align="center"><? echo $row[csf('transfer_date')];// ?></td>
							<td width="200"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
							<td width="80"><div style="word-break:break-all"><? if($row[csf('store_id')]>0){echo $store_name_arr[$row[csf('to_store_id')]];}else{echo  $store_name_arr[$store_arr[$row[csf('prod_id')]]['store_id']];} ?></div></td>                            <td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('room')]; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('rack')]; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo $row[csf('self')]; ?></div></td>
							<td width="60" align="center"><p><? echo $row[csf('bin_box')]; ?></p></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td width="60" align="right"><? echo $row[csf('roll_qnty')]; ?></td>
							<td align="right"><? echo $row[csf("roll_no")]; ?></td>

						</tr>
						<?
						$total_trnsf_qty+=$row[csf('roll_qnty')];
						$iii++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="10" align="right">Total</th>
							<th align="right"><? echo number_format($total_trnsf_qty,2); ?></th>
							<th align="right"></th>
						</tr>

					</tfoot>
				</table>
			</div>

		</div>
	</fieldset>

	<?
	die;

	foreach( $programData as $row )
	{
		$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]=$row[csf('prog_no')];
	}

	$i=1; $product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
	$sql_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,83,84,110,183) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
	$result_trans=sql_select( $sql_trans );
	foreach ($result_trans as $row)
	{
		$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
		$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
	}

	foreach($dataArray as $row)
	{
						//$issue_id_arr[]=$row[csf('id')];
		if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
		$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
							//$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$yarn_count_value = "";
		foreach ($y_count as $val) {
			if ($val > 0) {
				if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
			}
		}
							/*$brand_value = "";
							foreach ($brand_id as $bid) {
							if ($bid > 0) {
							if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
							}
						}*/
						$po_id=$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["po_id"];
						$barcode_no=$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["barcode_no"];
						$no_of_roll=$roll_no_data_arr2[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["no_of_roll"];
							//echo $po_id.'dff';
						$brand_value=$brand_name_arr[$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["brand_id"]];
						$body_part_name=$body_part[$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["body_part_id"]];
						$gsm=$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["gsm"] ;
						$width=$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["width"] ;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td width="70"><p><? echo $row[csf('issue_date')]; ?></p></td>
							<td width="120"><? echo $row[csf('issue_number')];// ?></td>
							<td width="150"><div style="word-break:break-all"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></div></td>
							<td width="80"><div style="word-break:break-all"><? echo $buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
							<td width="80"><div style="word-break:break-all"><? echo $job_data_arr[$po_id]["style"]; ?></div></td>
							<td width="100"><div style="word-break:break-all"><? echo $job_data_arr[$po_id]["job_no"] ; ?></div></td>
							<td width="100"><div style="word-break:break-all"><? echo $body_part_name; ?></div></td>
							<td width="60"><p><? echo $row[csf('stitch_length')]; ?></p></td>
							<td width="60"><? echo $gsm; ?></td>
							<td width="60"><? echo $width; ?></td>
							<td><? echo $dya_gauge_arr[$row[csf("machine_id")]]["dia_width"] ?></td>

						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="10" align="right">Total</th>
							<th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</fieldset>
	<script>
		setFilterGrid("table_body",-1);
	</script>
	<br>


	<?
	exit();
}
