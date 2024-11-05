<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="load_drop_down_buyer")
{
	$party="1,3,21,90";
	echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			var job_id=str[0];
			var job_no=str[1];

			$('#hide_job_id').val( job_id );
			$('#hide_job_no').val( job_no );
			parent.emailwindow.hide();
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
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'booking_wise_finish_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

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
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date) as year ";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	if($db_type==0) $year_field="and year(insert_date)=$year_id";
	else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";

	if($db_type==0)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and year(insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and to_char(insert_date,'YYYY')='$year_id'";
	}
	else $year_cond="";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field_by from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',0) ;
	exit();
}

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'1','');
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year_id=$data[2];

	?>
	<script>

		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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

			$('#hdn_booking_id').val(id);
			$('#hdn_booking_no').val(name);
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
							<thead>
								<th width="150">Buyer Name</th>
								<th width="100">Job No</th>
								<th width="150">Booking No</th>
								<th width="200">Date Range</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hdn_booking_no" id="hdn_booking_no" value="">
									<input type="hidden" name="hdn_booking_id" id="hdn_booking_id" value="">
								</th>
							</thead>
							<tr>
								<td>
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_id,"",0 );
									?>
								</td>
								<td>
									<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px">
								</td>
								<td>
									<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px">
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_job_no').value,'create_booking_search_list_view', 'search_div', 'booking_wise_finish_stock_report_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<?
							echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
							?>
							<? echo load_month_buttons();  ?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top" id="search_div">
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[3];
	$booking_no=$data[4];
	$job_no=$data[5];

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; else $booking_date ="";
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$booking_cond = ($booking_no!="")?" and a.booking_no_prefix_num=$booking_no":"";
	$job_no_cond = ($job_no!="")?" and b.job_no like '%$job_no%'":"";

	$sql= "select a.company_id, a.booking_no_prefix_num, a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.buyer_id, b.job_no, b.style_ref_no from wo_booking_mst a,wo_booking_dtls d, wo_po_details_master b where company_id=$company $booking_cond $job_no_cond $booking_date and a.booking_no=d.booking_no and d.job_no=b.job_no and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_type in (1,4)  group by a.company_id, a.booking_no_prefix_num, a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,  a.buyer_id,b.job_no, b.style_ref_no order by a.id desc";

	?>
	<br>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="90">Company</th>
			<th width="100">Buyer</th>
			<th width="110">Style Ref.</th>
			<th width="90">Job No</th>
			<th width="100">Booking No</th>
			<th width="90">Booking Type</th>
			<th width="100">Booking Date</th>
		</thead>
	</table>
	<div style="width:670px; max-height:265px; overflow-y:scroll; float: left;" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" align="left">
			<?
			$i=1;
			$result = sql_select($sql);
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('booking_type')]==4)
				{
					$booking_type = "Sample";
				}
				else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
				{
					$booking_type = "Main";
				}
				else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
				{
					$booking_type = "Short";
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $i; ?>','<? echo $row[csf('is_approved')]; ?>')" id="search<? echo $i;?>">
					<td width="40" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("booking_no")]; ?>"/>
					</td>
					<td width="90" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="100" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="110" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="90" align="center"><? echo $booking_type; ?></td>
					<td width="100" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="650" cellspacing="0" cellpadding="0" style="border:none" align="left">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);

	?>
    <input type="hidden" id="order_no_id" />
    <input type="hidden" id="order_no_val" />
	
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#order_no_id").val(splitData[0]);
			$("#order_no_val").val(splitData[1]);
			parent.emailwindow.hide();
		}
		
		function fn_generate_list(){
			if((form_validation('txt_search_job','Job')==false) && (form_validation('txt_search_style','Style')==false))
			{
				return;
			}
			else
			{
				show_list_view ('<? echo $data[0]; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $data[3]; ?>', 'create_order_no_search_list_view', 'search_div', 'booking_wise_finish_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
			}
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
							<th>Search Job</th>
							<th>Search Style</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                            <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
							<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$data[1],"",0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_job" id="txt_search_job" placeholder="Job No" value="<? echo $data[2];?>" />
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_style" id="txt_search_style" placeholder="Style Ref." />
								</td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="fn_generate_list()" style="width:70px;" />
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];

	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";

	if(trim($data[2])!='') $job_no_cond=" and b.job_no_prefix_num=".trim($data[2]).""; else $job_cond="";
	if(trim($data[3])!='') $style_cond=" and b.style_ref_no like '".trim($data[3])."'"; else $style_cond="";

	if($db_type==0) $year_field_by="and YEAR(b.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(b.insert_date,'YYYY')";
	if(trim($data[4])!=0) $year_cond=" $year_field_by='$data[4]'"; else $year_cond="";

	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond $year_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array(1=>$buyer);
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "booking_wise_finish_stock_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','',0) ;
	disconnect($con);
	exit();
}


if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$report_type 		= str_replace("'","",$cbo_report_type);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$book_no 			= trim(str_replace("'","",$txt_book_no));
	$book_id 			= str_replace("'","",$txt_book_id);
	$job_no 			= trim(str_replace("'","",$txt_job_no));
	$txt_ref_no 		= trim(str_replace("'","",$txt_ref_no));
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$txt_order_no 		= str_replace("'","",$txt_order_no);
	$txt_order_id 		= str_replace("'","",$txt_order_id);
	$cbo_value_with 	= str_replace("'","",$cbo_value_with);


	if($buyer_id==0)
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
		$buyer_id_cond=" and b.buyer_name=$buyer_id";
	}

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and YEAR(b.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(b.insert_date,'YYYY')=$job_year";
	}

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	if ($book_no=="") 
	{
		$booking_no_cond="";
	} 
	else 
	{
		$booking_serch_nos = "'".implode("','",explode(",", $book_no))."'";
		$booking_no_cond=" and c.booking_no in ($booking_serch_nos)";
	}

	$order_cond="";
	if($txt_order_no !="") $order_cond .= " and a.po_number='".$txt_order_no."'";
	if($txt_order_id !="") $order_cond .= " and a.id='".$txt_order_id."'"; 

	if($txt_ref_no !="") $reference_cond = " and a.grouping='".$txt_ref_no."'"; else $reference_cond="";

	$sql="select * from
	        (select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.gsm_weight, c.dia_width, c.fabric_color_id, c.booking_type, c.is_short, c.color_type,
	        sum(c.fin_fab_qnty) as fin_fab_qnty 
	        from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d 
	        where b.company_name=$cbo_company_id and c.booking_type=1 and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $buyer_id_cond $job_no_cond $year_cond $booking_no_cond $reference_cond $order_cond
	        and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id = c.pre_cost_fabric_cost_dtls_id  and a.is_confirmed =1
	        group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, a.shiping_status, c.booking_no,d.body_part_id, c.gsm_weight, c.dia_width, d.lib_yarn_count_deter_id, c.fabric_color_id, c.booking_type, c.is_short, c.color_type
	        union all
	        select b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no,a.shiping_status, c.booking_no ,d.body_part_id, d.lib_yarn_count_deter_id, c.gsm_weight, c.dia_width, c.fabric_color_id, c.booking_type, c.is_short, c.color_type, 
	        sum( c.fin_fab_qnty) as fin_fab_qnty 
	        from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cost_fab_conv_cost_dtls e 
	        where b.company_name=$cbo_company_id and c.booking_type in (3,4) and b.job_no=a.job_no_mst and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $buyer_id_cond $job_no_cond $year_cond $booking_no_cond $reference_cond $order_cond and a.is_confirmed =1
	        and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.id=c.pre_cost_fabric_cost_dtls_id and d.id = e.fabric_description  
	        group by b.job_no, b.buyer_name, a.id, a.po_number, a.grouping, a.file_no, a.shiping_status, c.booking_no, d.body_part_id, d.lib_yarn_count_deter_id, c.gsm_weight, c.dia_width, c.fabric_color_id, c.booking_type, c.is_short, c.color_type
	        ) t 
	order by grouping,fabric_color_id";

	$result=sql_select($sql);
	if(!empty($result))
	{
		$con = connect();
		$r_id2=execute_query("delete from tmp_poid where userid=$user_id ");
		if($r_id2)
		{
			oci_commit($con);
		}

		foreach($result as $row)
		{
			$r_id=execute_query("insert into tmp_poid (userid, type, poid) values ($user_id, 222, ".$row[csf('id')].")");
			if($r_id)
			{
				$r_id=1;
			}
			else
			{
				echo "insert into tmp_poid (userid, type, poid) values ($user_id, 222, ".$row[csf('id')].")";
				$r_id2=execute_query("delete from tmp_poid where userid=$user_id ");
				oci_rollback($con);
				die;
			}

			$tot_rows++;

			if($row[csf('booking_type')]==3)
			{
				$booking_type = "Service";
			}
			else if($row[csf('booking_type')]==4)
			{
				$booking_type = "Sample";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
			{
				$booking_type = "Main";
			}
			else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
			{
				$booking_type = "Short";
			}

			$ref_file = $row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('grouping')]."_".$row[csf('body_part_id')]."_".$row[csf('lib_yarn_count_deter_id')]."_".$row[csf('fabric_color_id')]."_".$booking_type."_".$row[csf('color_type')]."_".$row[csf('gsm_weight')]."_".$row[csf('dia_width')];

			$poIds.=$row[csf('id')].",";

			$poArr[$row[csf('id')]]=$row[csf('job_no')];

			$bookJobArr[$row[csf('booking_no')]]=$row[csf('job_no')];
			$buyer_job_type_str = $row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$booking_type;
			$fileRefArr[$row[csf('booking_no')]]["buyer_job_type_str"]=$buyer_job_type_str;
			$fileRefArr[$row[csf('booking_no')]]["grouping_po"].= $row[csf('grouping')]."#".$row[csf('po_number')].",";

			$booking_job_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$ref_file] += $row[csf('fin_fab_qnty')];
		}
	}
	else
	{
		echo "Data Not Found";die;
	}
	unset($result);

	if($r_id)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
		disconnect($con);
		die;
	}


	$poIds = implode(",",array_unique(explode(",",chop($poIds,","))));

	$rcv_sql = "SELECT b.id,e.booking_no, e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company,a.booking_id as wo_pi_prod_id,a.booking_no as wo_pi_prod_no, b.transaction_date, b.prod_id, b.store_id, c.body_part_id,c.fabric_description_id, c.gsm, c.width, c.color_id, b.cons_uom,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as dia_width_type, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as po_breakdown_id, b.cons_quantity as quantity,b.order_rate,b.pi_wo_batch_no, a.lc_sc_no
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, order_wise_pro_details d, pro_batch_create_mst e, tmp_poid f
	WHERE a.company_id =$cbo_company_id and a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and d.status_active =1 and b.pi_wo_batch_no=e.id and c.trans_id = d.trans_id and a.entry_form=37 and d.po_breakdown_id <>0 and f.poid=d.po_breakdown_id and f.type=222 and f.userid=$user_id
	group by b.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, c.body_part_id, c.fabric_description_id, c.gsm, c.width, c.color_id,b.cons_uom,c.dia_width_type,b.cons_quantity,b.order_rate,b.pi_wo_batch_no, a.lc_sc_no order by a.company_id";

	//and d.po_breakdown_id in ($poIds)

	//echo $rcv_sql."<br>";

	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$ref_str = $val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("color_id")]."*".$val[csf("gsm")]."*".$val[csf("width")];
		$recvDtlsDataArr[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str]['recv']+=$val[csf("quantity")];

		$storeWiseStock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]+=$val[csf("quantity")];


		$book_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$val[csf("store_id")]]+=$val[csf("quantity")];
		$job_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("store_id")]]+=$val[csf("quantity")];



		$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];
		$allStoreArr[$val[csf("store_id")]] =$val[csf("store_id")];
	}
	//echo "<pre>";
	//print_r($recvDtlsDataArr);die;

	
	$trans_in_sql = "SELECT c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, d.detarmination_id, d.gsm, d.dia_width as width, d.color as color_id, c.cons_uom, sum(c.cons_quantity) as quantity,c.order_rate, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as po_breakdown_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, order_wise_pro_details f, product_details_master d, pro_batch_create_mst e, tmp_poid g
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1  and a.entry_form in (14,15,306) and c.id = f.trans_id and f.trans_type = 5 and f.status_active=1 and f.po_breakdown_id<>0 $all_book_nos_cond and g.poid=f.po_breakdown_id and g.type=222 and g.userid=$user_id
		group by c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate order by c.company_id";
		//and f.po_breakdown_id in ($poIds)
	//echo $trans_in_sql."<br>";
	$trans_in_data = sql_select($trans_in_sql);
	foreach ($trans_in_data as  $val)
	{
		$ref_str = $val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color_id")]."*".$val[csf("gsm")]."*".$val[csf("width")];
		$recvDtlsDataArr[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str]['transfer_in']+=$val[csf("quantity")];
		$storeWiseStock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]+=$val[csf("quantity")];

		$book_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$val[csf("store_id")]]+=$val[csf("quantity")]; 
		$job_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("store_id")]]+=$val[csf("quantity")];

		$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];
		$allStoreArr[$val[csf("store_id")]] =$val[csf("store_id")];
	}
	
	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		$batch_ids= implode(",",$batch_id_arr);

		$all_batch_ids_cond=""; $batchCond="";
		if($db_type==2 && count($batch_id_arr)>999)
		{
			$batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
			foreach($batch_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.="  e.id in($chunk_arr_value) or ";
			}
			$all_batch_ids_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_ids_cond=" and e.id in($batch_ids)";
		}
	}


	$issRtnSql = "select c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id, c.store_id, b.fabric_description_id, b.gsm, b.width, b.color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id =c.id and c.issue_id =d.id and a.entry_form =52 and a.item_category =2 and c.pi_wo_batch_no =e.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id = $cbo_company_id $all_batch_ids_cond";
	$issRtnData = sql_select($issRtnSql);
	foreach ($issRtnData as $val)
	{
		$ref_str = $val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("color_id")]."*".$val[csf("gsm")]."*".$val[csf("width")];
		$recvDtlsDataArr[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str]['issue_return']+=$val[csf("quantity")];
		$storeWiseStock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]+=$val[csf("quantity")];

		$book_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$val[csf("store_id")]]+=$val[csf("quantity")]; 
		$job_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("store_id")]]+=$val[csf("quantity")];


		$allStoreArr[$val[csf("store_id")]] =$val[csf("store_id")];
	}

	$issue_sql = sql_select("select a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.cons_quantity, c.id as trans_id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width as width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id=$cbo_company_id $all_batch_ids_cond and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category=2 and c.transaction_type=2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2)");

	foreach ($issue_sql as $val)
	{
		$ref_str = $val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")]."*".$val[csf("gsm")]."*".$val[csf("width")];
		$recvDtlsDataArr[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str]['issue']+=$val[csf("cons_quantity")];
		$storeWiseStock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]-=$val[csf("cons_quantity")];

		$book_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];
		$job_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];
	}

	$rcvRtnSql = sql_select("select c.transaction_date, c.company_id, c.prod_id, c.store_id, c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width as width, d.color, e.booking_no, e.booking_without_order, b.body_part_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id=$cbo_company_id $all_batch_ids_cond and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active=1 and b.status_active=1 and c.status_active=1");

	foreach ($rcvRtnSql as $val)
	{
		$ref_str = $val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")]."*".$val[csf("gsm")]."*".$val[csf("width")];
		$recvDtlsDataArr[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str]['rcv_return']+=$val[csf("cons_quantity")];
		$storeWiseStock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]] -= $val[csf("cons_quantity")];

		$book_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];
		$job_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];

	}

	$transOutSql = sql_select("select c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, d.detarmination_id, d.gsm, d.dia_width as width, d.color, c.cons_uom, c.cons_quantity, c.order_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id =$cbo_company_id $all_batch_ids_cond and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.active_dtls_id_in_transfer=1");

	foreach ($transOutSql as $val)
	{
		$ref_str = $val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")]."*".$val[csf("gsm")]."*".$val[csf("width")];
		$recvDtlsDataArr[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str]['trans_out']+=$val[csf("cons_quantity")];
		$storeWiseStock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]-=$val[csf("cons_quantity")];

		$book_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("booking_no")]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];
		$job_store_stock[$bookJobArr[$val[csf('booking_no')]]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];
	}

	$stores = sql_select("select a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(2) and a.company_id=$cbo_company_id and a.id in (".implode(",", $allStoreArr).") group by a.id,a.store_name order by a.store_name asc");

	$num_of_store=0;
	foreach ($stores as $s_row)
	{
		$store_name_arr[$s_row[csf("id")]] = $s_row[csf("store_name")];
		$num_of_store++;
	}

    $composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
    $data_deter=sql_select($sql_deter);

    if(count($data_deter)>0)
    {
    	foreach( $data_deter as $row )
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
    			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
    			$constructionArr[$row[csf('id')]]=$row[csf('construction')];
    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
    			$copmpositionArr[$row[csf('id')]]=$cps;
    		}
    	}
    }

    $width = (2450+($num_of_store*110));  

    /*echo "<pre>";
    print_r($book_store_stock);
    die;*/

	$r_id2=execute_query("delete from tmp_poid where userid=$user_id ");
	oci_rollback($con);
	die;
    
	ob_start();
	?>
	<style type="text/css">
		.word_break_wrap {
			word-break: break-all;
			word-wrap: break-word;
		}
		.grad1 {
			  background-image: linear-gradient(#e6e6e6, #b1b1cd, #e0e0eb);
			}
	</style>
	<fieldset style="width:<? echo $width+50; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $width;?>">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="<? echo 22+$num_of_store?>" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="<? echo 22+$num_of_store?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="<? echo 22+$num_of_store?>" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>

		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th colspan="10" >Booking Info</th>
					<th colspan="16">Receive/Issue Info</th>
					<th colspan="<? echo $num_of_store;?>">Store Summary</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="100" rowspan="2">Booking Type</th>
					<th width="110" rowspan="2">Body Part</th>
					<th width="110" rowspan="2">Color Type</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="110" rowspan="2">Composition</th>
					<th width="55" rowspan="2">GSM</th>
					<th width="55" rowspan="2">Dia</th>
					<th width="105" rowspan="2">Color</th>
					<th width="120" rowspan="2"><p class="word_break_wrap">Finish Fabric Qty(Kg)</p></th>
					
					<th width="110" rowspan="2">Body Part</th>
					<th width="110" rowspan="2">Color Type</th>
					<th width="110" rowspan="2">Construction</th>
					<th width="110" rowspan="2">Composition</th>
					<th width="55" rowspan="2">GSM</th>
					<th width="55" rowspan="2">Dia</th>
					<th width="105" rowspan="2">Color</th>

					<th colspan="4">Total Receive</th>
					<th colspan="4">Total Issue</th>
					<th width="105" rowspan="2">Stock Qty.</th>

					<? foreach ($stores as $row) 
					{
						?>
						<th width="110" rowspan="2"><? echo $row[csf("store_name")];?></th>
						<?
					}
					?>
				</tr>
				<tr>
					<th width="90">Received Qty(Kg)</th>
					<th width="90">Issue Return</th>
					<th width="90">Transfer From</th>
					<th width="90">Grand Total</th>

					<th width="90">Issue</th>
					<th width="90">Receive Return</th>
					<th width="90">Transfer To</th>
					<th width="90">Grand Total</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?

				foreach($booking_job_arr as $job_no=>$jobNoData)
				{
					foreach ($jobNoData as $bookingNo => $bookingNoData)
					{
						$stock_qty_smry_arr[$job_no][$bookingNo]=0;
						if(!empty($recvDtlsDataArr[$job_no][$bookingNo]))
						{
							foreach ($recvDtlsDataArr[$job_no][$bookingNo] as $ref_str => $val) 
							{

								$total_receive_smry = $val["recv"]+$val["issue_return"]+$val["transfer_in"];
								$total_issue_smry = $val["issue"]+$val["rcv_return"]+$val["trans_out"];
								$stock_qty_smry =$total_receive_smry-$total_issue_smry;
								if(($cbo_value_with==1 && $stock_qty_smry>=0) || ($cbo_value_with==2 && $stock_qty_smry>0))
								{
									$stock_qty_smry_arr[$job_no][$bookingNo]=1;
								}	
							}
						}
					}
				}

				$i=1;$y=1;$c=1;
				$sub_recv=$sub_issue_ret=$sub_transfer_in=$sub_total_receive=$sub_issue=$sub_recv_return=$sub_trans_out=$sub_total_issue=$sub_stock_qty=0;
				foreach($booking_job_arr as $job_no=>$jobNoData)
				{
					$job_booking_total=0;$job_total_arr[$job_no]=0;
					foreach ($jobNoData as $bookingNo => $bookingNoData)
					{
						if ($stock_qty_smry_arr[$job_no][$bookingNo]>0) 
						{
							$job_total_arr[$job_no]=1;

							$buyerJobTypeArr =explode("_", $fileRefArr[$bookingNo]["buyer_job_type_str"]);
							$reference = "";$po_number="";
							foreach ( explode(",", chop($fileRefArr[$bookingNo]["grouping_po"],",")) as $key) 
							{
								$grouping_po_arr = explode("#", $key);
								$reference.= $grouping_po_arr[0].",";
								$po_number.= $grouping_po_arr[1].",";
							}

							$reference = implode(",",array_unique(explode(",", chop($reference,","))));
							$po_number = implode(",",array_unique(explode(",", chop($po_number,","))));

							?>
							<tr>
								<td >
									<? 
									echo "<p style='font-weight:bold;width:860px'>Buyer: ".$buyer_arr[$buyerJobTypeArr[0]].", Job No: ".$buyerJobTypeArr[1].", Ref No: ".$reference.", Booking No: ".$bookingNo. "(" .$buyerJobTypeArr[2]."), PO No: ".$po_number."</p>";
									?>
									<table cellpadding="0" cellspacing="0" border="0">
										<?
										$sub_booking_total=0;
										foreach ($bookingNoData as $fileRefStr => $row) 
										{
											$fileRefData=explode("_",$fileRefStr);

											$buyer_name = $fileRefData[0];
											$job_number = $fileRefData[1];
											$grouping = $fileRefData[2];
											$body_part_id = $fileRefData[3];
											$deter_id = $fileRefData[4];
											$fabric_color_id = $fileRefData[5];
											$booking_type = $fileRefData[6];
											$color_type_id = $fileRefData[7];
											$gsm = $fileRefData[8];
											$dia_width = $fileRefData[9];
											if($c%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $c;?>','<? echo $bgcolor;?>')" id="tr<? echo $c;?>">
												<td width="40"><? echo $c; ?></td>
												<td width="100"><p><? echo $booking_type; ?>&nbsp;</p></td>
												<td width="110"><p class="word_break_wrap"><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
												<td width="110"><p class="word_break_wrap"><? echo $color_type[$color_type_id]; ?>&nbsp;</p></td>
												<td width="110"><p class="word_break_wrap"><? echo $constructionArr[$deter_id]; ?>&nbsp;</p></td>
												<td width="110"><p class="word_break_wrap"><? echo $composition_arr[$deter_id]; ?>&nbsp;</p></td>
												<td width="55"><p class="word_break_wrap"><? echo $gsm; ?>&nbsp;</p></td>
												<td width="55"><p class="word_break_wrap"><? echo $dia_width; ?>&nbsp;</p></td>
												<td width="105"><p class="word_break_wrap"><? echo $color_arr[$fabric_color_id]; ?>&nbsp;</p></td>
												<td width="120" align="right"><p><? echo number_format($row,2,'.',''); ?>&nbsp;</p></td>											
											</tr>
											<?
											$c++;
											$sub_booking_total +=number_format($row,2,'.','');
											$job_booking_total +=number_format($row,2,'.','');
											$grand_booking_total +=number_format($row,2,'.','');
										}

										?>
									</table>
								</td>
								<td>
									<? echo "&nbsp;";?>
									<table cellpadding="0" cellspacing="0" border="0" >
										<?
										if(!empty($recvDtlsDataArr[$job_no][$bookingNo]))
										{
											foreach ($recvDtlsDataArr[$job_no][$bookingNo] as $ref_str => $val) 
											{
												$ref_str_data=explode("*",$ref_str);
												$body_part_id = $ref_str_data[0];
												$deter_id = $ref_str_data[1];
												$fabric_color_id = $ref_str_data[2];
												$gsm = $ref_str_data[3];
												$dia_width = $ref_str_data[4];

												$total_receive = $val["recv"]+$val["issue_return"]+$val["transfer_in"];
												$total_issue = $val["issue"]+$val["rcv_return"]+$val["trans_out"];
												$stock_qty =$total_receive-$total_issue;

												if($y%2==0) $bgcolor1="#E9F3FF"; else $bgcolor1="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor1;?>" onClick="change_color('trr<? echo $y;?>','<? echo $bgcolor1;?>')" id="trr<? echo $y;?>">

													<td width="110" title="<? echo $body_part_id;?>">
														<? echo $body_part[$body_part_id];?>
													</td>
													<td width="110" ><? //echo $color_type[$color_type_id];?></td>
													<td width="110" title="<? echo $deter_id;?>"><p><? echo $constructionArr[$deter_id];?></p></td>
													<td width="110" title="<? echo $deter_id;?>"><p><? echo $composition_arr[$deter_id];?></p></td>
													<td width="55"><p><? echo $gsm;?></p></td>
													<td width="55"><p><? echo $dia_width;?></p></td>
													<td width="105" align="center" title="<? echo $fabric_color_id;?>">
														<p><? echo $color_arr[$fabric_color_id]; ?>&nbsp;</p>
													</td>

													<td width="90" align="right"><? echo number_format($val["recv"],2);?></td>
													<td width="90" align="right"><? echo number_format($val["issue_return"],2);?></td>
													<td width="90" align="right"><? echo number_format($val["transfer_in"],2);?></td>
													<td width="90" align="right"><? echo number_format($total_receive,2);?></td>

													<td width="90" align="right"><? echo number_format($val["issue"],2);?></td>
													<td width="90" align="right"><? echo number_format($val["rcv_return"],2);?></td>
													<td width="90" align="right"><? echo number_format($val["trans_out"],2);?></td>
													<td width="90" align="right"><? echo number_format($total_issue,2);?></td>

													<td width="105" align="right"><? echo number_format($stock_qty,2);?></td>
													<? foreach ($stores as $row) 
													{
														?>
														<td width="110" align="right"><? echo number_format($storeWiseStock[$job_no][$bookingNo][$ref_str][$row[csf("id")]],2);?></td>
														<?
														$grand_store_stock[$row[csf("id")]] += $storeWiseStock[$job_no][$bookingNo][$ref_str][$row[csf("id")]];

													}
													?>
												</tr>
												<?
												$y++;
												$sub_book_recv 				+= $val["recv"];
												$sub_book_issue_ret 		+=$val["issue_return"];
												$sub_book_transfer_in 		+= $val["transfer_in"];
												$sub_book_total_receive 	+=$total_receive;
												$sub_book_issue 			+=$val["issue"];
												$sub_book_recv_return 		+=$val["rcv_return"];
												$sub_book_trans_out 		+=$val["trans_out"];
												$sub_book_total_issue 		+=$total_issue;
												$sub_book_stock_qty 		+=$stock_qty;

												$sub_job_recv 				+= $val["recv"];
												$sub_job_issue_ret 			+=$val["issue_return"];
												$sub_job_transfer_in 		+= $val["transfer_in"];
												$sub_job_total_receive 		+=$total_receive;
												$sub_job_issue 				+=$val["issue"];
												$sub_job_recv_return 		+=$val["rcv_return"];
												$sub_job_trans_out 			+=$val["trans_out"];
												$sub_job_total_issue 		+=$total_issue;
												$sub_job_stock_qty 			+=$stock_qty;

												$grand_recv 			+= $val["recv"];
												$grand_issue_ret 		+=$val["issue_return"];
												$grand_transfer_in 		+= $val["transfer_in"];
												$grand_total_receive 	+=$total_receive;
												$grand_issue 			+=$val["issue"];
												$grand_recv_return 		+=$val["rcv_return"];
												$grand_trans_out 		+=$val["trans_out"];
												$grand_total_issue 		+=$total_issue;
												$grand_stock_qty 		+=$stock_qty;
											}
										}
										?>
									</table>
								</td>
							</tr>

							<tr>
								<td >
									<table cellpadding="0" cellspacing="0" border="0">
										<tfoot>
											<tr>
												<th width="40">&nbsp;</th>
												<th width="100"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="55"><p>&nbsp;</p></th>
												<th width="55"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="105"><p>Booking Total:</p></th>
												<th width="120" align="right"><p><? echo number_format($sub_booking_total,2);?></p></th>		
											</tr>
										</tfoot>
									</table>
								</td>
								<td>
									<table cellpadding="0" cellspacing="0" border="0" >
										<tfoot>
										<tr>
											<th width="110">&nbsp;</th>
											<th width="110">&nbsp;</th>
											<th width="110">&nbsp;</th>
											<th width="55">&nbsp;</th>
											<th width="55">&nbsp;</th>
											<th width="110">&nbsp;</th>
											<th width="105"><p>Booking Total:</p></th>

											<th width="90" align="right"><? echo number_format($sub_book_recv,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_book_issue_ret,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_book_transfer_in,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_book_total_receive,2);?></th>

											<th width="90" align="right"><? echo number_format($sub_book_issue,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_book_recv_return,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_book_trans_out,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_book_total_issue,2);?></th>

											<th width="105" align="right"><? echo number_format($sub_book_stock_qty,2);?></th>

											<? foreach ($stores as $row) 
											{
												?>
												<th width="110" align="right"><? echo number_format($book_store_stock[$job_no][$bookingNo][$row[csf("id")]],2); ?></th>
												<?
											}
											?>
										</tr>
										</tfoot>
									</table>
								</td>
							</tr>
							<?
						
							$sub_book_recv=$sub_book_issue_ret=$sub_book_transfer_in=$sub_book_total_receive=$sub_book_issue=$sub_book_recv_return=$sub_book_trans_out=$sub_book_total_issue=$sub_book_stock_qty=0;
						}
					}

					if ($job_total_arr[$job_no]>0) 
					{					
						?>
							<tr>
								<td >
									<table cellpadding="0" cellspacing="0" border="0">
										<tfoot>
											<tr>
												<th width="40">&nbsp;</th>
												<th width="100"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="55"><p>&nbsp;</p></th>
												<th width="55"><p>&nbsp;</p></th>
												<th width="110"><p>&nbsp;</p></th>
												<th width="105"><p>Job Total:</p></th>
												<th width="120" align="right"><p><? echo number_format($job_booking_total,2);?></p></th>											
											</tr>
										</tfoot>
									</table>
								</td>
								<td>
									<table cellpadding="0" cellspacing="0" border="0" >
										<tfoot>
										<tr>
											<th width="110">&nbsp;</th>
											<th width="110">&nbsp;</th>
											<th width="110">&nbsp;</th>
											<th width="55">&nbsp;</th>
											<th width="55">&nbsp;</th>
											<th width="110">&nbsp;</th>
											<th width="105"><p>Job Total:</p></th>

											<th width="90" align="right"><? echo number_format($sub_job_recv,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_job_issue_ret,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_job_transfer_in,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_job_total_receive,2);?></th>

											<th width="90" align="right"><? echo number_format($sub_job_issue,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_job_recv_return,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_job_trans_out,2);?></th>
											<th width="90" align="right"><? echo number_format($sub_job_total_issue,2);?></th>

											<th width="105" align="right"><? echo number_format($sub_job_stock_qty,2);?></th>

											<? foreach ($stores as $row)
											{
												?>
												<th width="110" align="right"><? echo number_format($job_store_stock[$job_no][$row[csf("id")]],2); ?></th>
												<?
											}
											?>
										</tr>
										</tfoot>
									</table>
								</td>
							</tr>
						<?
						$sub_job_recv=$sub_job_issue_ret=$sub_job_transfer_in=$sub_job_total_receive=$sub_job_issue=$sub_job_recv_return=$sub_job_trans_out=$sub_job_total_issue=$sub_job_stock_qty=0;
					}
				}
				?>
			</table>
		</div>
		<table cellpadding="0" cellspacing="0" border="0" width="<? echo $width; ?>" class="rpt_table">
			<tfoot>
				<tr>
					<td >
						<table cellpadding="0" cellspacing="0" border="0">
							<tfoot>
								<tr>
									<th width="40">&nbsp;</th>
									<th width="100"><p>&nbsp;</p></th>
									<th width="110"><p>&nbsp;</p></th>
									<th width="110"><p>&nbsp;</p></th>
									<th width="110"><p>&nbsp;</p></th>
									<th width="55"><p>&nbsp;</p></th>
									<th width="55"><p>&nbsp;</p></th>
									<th width="110"><p>&nbsp;</p></th>
									<th width="105"><p>Grand Total</p></th>
									<th width="120" align="right"><p><? echo number_format($grand_booking_total,2);?></p></th>											
								</tr>
							</tfoot>
						</table>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" >
							<tfoot>
							<tr>
								<th width="110">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="55">&nbsp;</th>
								<th width="55">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="105"><p>Grand Total:</p></th>

								<th width="90" align="right"><? echo number_format($grand_recv,2);?></th>
								<th width="90" align="right"><? echo number_format($grand_issue_ret,2);?></th>
								<th width="90" align="right"><? echo number_format($grand_transfer_in,2);?></th>
								<th width="90" align="right"><? echo number_format($grand_total_receive,2);?></th>

								<th width="90" align="right"><? echo number_format($grand_issue,2);?></th>
								<th width="90" align="right"><? echo number_format($grand_recv_return,2);?></th>
								<th width="90" align="right"><? echo number_format($grand_trans_out,2);?></th>
								<th width="90" align="right"><? echo number_format($grand_total_issue,2);?></th>

								<th width="105" align="right"><? echo number_format($grand_stock_qty,2);?></th>

								<? foreach ($stores as $row)
								{
									?>
									<th width="110" align="right"><? echo number_format($grand_store_stock[$row[csf("id")]],2); ?></th>
									<?
								}
								?>
							</tr>
							</tfoot>
						</table>
					</td>
				</tr>
			</tfoot>
		</table>

	</fieldset>
	<?
	//echo "Execution Time: " . (microtime(true) - $started) . "S";
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename=$user_id."_".$name.".xls";
	echo "$html####$filename####$report_type";

	exit();
}
?>