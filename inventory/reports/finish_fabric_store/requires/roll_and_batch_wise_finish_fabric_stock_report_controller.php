<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 90, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","" );
	exit();
}

if($action=="load_drop_down_buyer")
{
	$party="1,3,21,90";
	echo create_drop_down( "cbo_buyer_id", 90, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action=="load_drop_down_store")
{
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, company_location_id, item_cate_id FROM user_passwd where id=$user_id");
	$store_location_id = $userCredential[0][csf('store_location_id')];

	if ($store_location_id != '')
	{
		$store_location_credential_cond = "and a.id in($store_location_id)";
	} else {
		$store_location_credential_cond = "";
	}

	$sql ="SELECT a.id, a.store_name from lib_store_location a, lib_store_location_category b
	where a.id= b.store_location_id and a.company_id=$data and b.category_type=2 and a.status_active=1 and a.is_deleted=0
	 $store_location_credential_cond group by a.id, a.store_name order by a.store_name";

	//$sql = "select comp.id, comp.store_location from lib_store_location comp where comp.status_active=1 and comp.is_deleted=0 and comp.company_id=$data order by comp.store_location";
	echo create_drop_down( "cbo_store_id", 90, $sql,"id,store_name", 1, "--Select Store--", $selected, "","" );
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
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
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
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_job" id="txt_search_job" placeholder="Job No" />
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_style" id="txt_search_style" placeholder="Style Ref." />
								</td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'buyer_wise_finish_fabric_received_issued_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
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
	//$month_id=$data[5];
	//echo $month_id;

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

	if($data[2]!='') $job_cond=" and job_no_prefix_num=$data[2]"; else $job_cond="";
	if($data[3]!='') $style_cond=" and style_ref_no like '$data[3]'"; else $style_cond="";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date)";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";

	$arr=array (0=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, buyer_name, style_ref_no, $year_field_by as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Year,Style Ref. No", "170,130,80,60","610","270",0, $sql , "js_set_value", "id,job_no", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no,year,style_ref_no", "",'','0,0,0,0','',1) ;
	exit();
}

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year_id=$data[2];
	$job_nos=$data[3];

	?>
	<script>
		function js_set_value(booking_str)
		{
			var booking_arr = booking_str.split('_');
			var booking_id = booking_arr[0];
			var booking_no = booking_arr[1];
			document.getElementById('selected_booking_no').value=booking_no;
			document.getElementById('selected_booking_id').value=booking_id;
			parent.emailwindow.hide();
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
								<th width="150">Booking No</th>
								<th width="200">Date Range</th>
								<th></th>
							</thead>
							<tr>
								<input type="hidden" id="selected_booking_no" value="">
								<input type="hidden" id="selected_booking_id" value="">
								<td>
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_id,"",0 );
									?>
								</td>
								<td>
									<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px">
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									<input type="hidden" name="hdn_job_nos" id="hdn_job_nos" value="<? echo $job_nos;?>">
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('txt_booking_no').value +'_'+ document.getElementById('hdn_job_nos').value,'create_booking_search_list_view', 'search_div', 'roll_and_batch_wise_finish_fabric_stock_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	$buyer_id=$data[0];
	$company=$data[3];
	$booking_no=$data[4];
	$job_nos=$data[5];

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; else $booking_date ="";
	}

if($job_nos!="")
{
	$all_job_nos = "'".implode("','",explode(",",$job_nos))."'";

	$booking_job_cond = " and d.job_no in (".$all_job_nos.")";
}

if($buyer_id)
{
	$buyer_cond = " and a.buyer_id=".$buyer_id;
}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$po_num,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	$booking_cond = ($booking_no!="")?" and a.booking_no_prefix_num=$booking_no":"";


	//$sql= "SELECT booking_no_prefix_num, booking_no,booking_date,company_id, buyer_id,job_no,po_break_down_id,item_category, fabric_source,supplier_id, is_approved,ready_to_approved from wo_booking_mst  where company_id in ($company) $buyer $booking_date $booking_cond and booking_type=1 and is_short in(1,2) and  status_active=1 and is_deleted=0 order by booking_no";

	$sql=" SELECT a.id as booking_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved
from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and a.company_id in ($company) $buyer_cond $booking_date $booking_cond and a.booking_type=1 and a.is_short in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
and c.status_active=1 and c.is_deleted=0 $booking_job_cond
group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id,a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved order by a.booking_no";


	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_id,booking_no", "", 1, "0,0,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,0,0,0,0,0,0,0,0,0','','');

	exit();
}


if($action=="batch_popup")
{
	echo load_html_head_contents("Batch", "../../../../", 1, 1,'','','');
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
			$('#hide_batch_id').val( id );
			$('#hide_batch_no').val( ddd );
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

		function fn_generate_list()
		{
			if(document.getElementById('txt_job_no').value=="" && document.getElementById('txt_book_no').value=="" && document.getElementById('txt_book_id').value=="")
			{
				if((form_validation('txt_batch','Company Name')==false) && (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false))
				{
					return;
				}
			}
			else
			{
				show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_batch').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+ document.getElementById('txt_job_no').value+'**'+ document.getElementById('txt_book_no').value +'**'+ document.getElementById('txt_book_id').value, 'create_batch_search_list_view', 'search_div', 'roll_and_batch_wise_finish_fabric_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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
							<th>Batch</th>
							<th>Batch Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:130px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                            <input type="hidden" name="hide_batch_id" id="hide_batch_id" value="" />
							<input type="hidden" name="hide_batch_no" id="hide_batch_no" value="" />
							<input type="hidden" name="txt_job_no" id="txt_job_no" value="<? echo $txt_job_no;?>" />
							<input type="hidden" name="txt_book_no" id="txt_book_no" value="<? echo $txt_book_no;?>" />
							<input type="hidden" name="txt_book_id" id="txt_book_id" value="<? echo $txt_book_id;?>" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_batch" id="txt_batch" placeholder="Batch No" />
								</td>
								<td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px;" value="" readonly/>
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px;" value="" readonly/>
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

if($action=="create_batch_search_list_view")
{
	list($company_id,$batch_no,$strt_sate,$end_date,$year,$job_no,$booking_no,$booking_id)=explode('**',$data);
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	if($batch_no!='') $where_cond .=" and a.batch_no like('%".trim($batch_no)."%')";

	if($strt_sate!='' and $end_date!=''){
		if($db_type==0)
		{
			$strt_sate=change_date_format($strt_sate,'yyyy-mm-dd');
			$end_date=change_date_format($end_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$strt_sate=change_date_format($strt_sate,'','',1);
			$end_date=change_date_format($end_date,'','',1);
		}
		$where_cond .=" and a.batch_date between '$strt_sate' and '$end_date'";
	}

	if($booking_no!="") $where_cond .=" and a.booking_no like '%".trim($booking_no)."%'";
	if($booking_id!="") $where_cond .=" and b.id= '".trim($booking_id)."'";

	$arr=array (1=>$color_arr);

	$sql ="SELECT a.id,a.batch_no,a.batch_date,a.color_id
	from pro_batch_create_mst a, wo_booking_mst b, wo_booking_dtls c
	where a.booking_no=b.booking_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $where_cond group by a.id,a.batch_no,a.batch_date,a.color_id ";

	echo create_list_view("tbl_list_search", "Batch No,Color,Batch Date", "200,100,100","610","270",0, $sql , "js_set_value", "id,batch_no", "", 1, "0,color_id,0", $arr , "batch_no,color_id,batch_date", "",'','0,0,3','',1) ;
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id= str_replace("'","",$cbo_company_id);
	$cbo_location_id= trim(str_replace("'","",$cbo_location_id));
	$cbo_buyer_id= trim(str_replace("'","",$cbo_buyer_id));
	$cbo_year= trim(str_replace("'","",$cbo_year));
	$txt_job_no= trim(str_replace("'","",$txt_job_no));
	$txt_booking_no= trim(str_replace("'","",$txt_book_no));
	$txt_book_id= trim(str_replace("'","",$txt_book_id));
	$txt_batch_no= trim(str_replace("'","",$txt_batch_no));
	$txt_batch_id= trim(str_replace("'","",$txt_batch_id));
	$cbo_store_id= trim(str_replace("'","",$cbo_store_id));

	$txt_date_from_batch= trim(str_replace("'","",$txt_date_from));
	$txt_date_to_batch= trim(str_replace("'","",$txt_date_to));
	$txt_date_from_booking= trim(str_replace("'","",$txt_date_from_booking));
	$txt_date_to_booking= trim(str_replace("'","",$txt_date_to_booking));
	$txt_date_from_receive= trim(str_replace("'","",$txt_date_from_receive));
	$txt_date_to_receive= trim(str_replace("'","",$txt_date_to_receive));
	$txt_product_id= trim(str_replace("'","",$txt_product_id));
	$txt_roll_no= trim(str_replace("'","",$txt_roll_no));

	$con = connect();
	$r_id3=execute_query("delete from tmp_barcode_no where userid=$user_id");
	oci_commit($con);

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and f.buyer_name=$cbo_buyer_id";


	$search_cond='';
	// if($txt_job_no !=""){
	// 	$job_cond = " and f.job_no like '%$txt_job_no%'";
	// }

	if($txt_job_no !="")
	{
		$job_data = "'".implode("','",explode(",",$txt_job_no))."'";
		$job_cond = " and f.job_no in (".$job_data.")";
	}

	if($txt_batch_no !="")
	{
		$txt_batch_nos = "'".implode("','",explode(",",$txt_batch_no))."'";
		$batch_cond = " and c.batch_no in (".$txt_batch_nos.")";

		if($txt_batch_id !="")
		{
			$batch_cond .= " and c.id in (".$txt_batch_id.")";
		}
	}

	if($txt_booking_no !=""){
		$booking_cond = " and c.booking_no like '%$txt_booking_no%'";
	}

	if($txt_date_from_batch !="" && $txt_date_to_batch != ""){
		$batch_date_cond = " and c.batch_date between '$txt_date_from_batch' and '$txt_date_to_batch' ";
	}
	if($txt_date_from_booking !="" && $txt_date_to_booking != ""){
		$booking_date_cond = " and h.booking_date between '$txt_date_from_booking' and '$txt_date_to_booking' ";
	}

	if($txt_date_from_receive !="" && $txt_date_to_receive != ""){
		$receive_date_cond = " and a.transaction_date between '$txt_date_from_receive' and '$txt_date_to_receive' ";
	}

	if($cbo_store_id != "" && $cbo_store_id !=0){
		$store_cond = " and a.store_id in ($cbo_store_id) ";
	}
	if($txt_product_id != ""){
		$product_cond = " and a.prod_id in ($txt_product_id) ";
	}

	if($txt_roll_no !=""){
		$roll_cond = " and d.roll_no='$txt_roll_no'";
	}

	if($db_type==0)
    {
        if($cbo_year!=0) $job_year = " and year(d.po_received_date) = $cbo_year" ;
    }
    else if($db_type==2)
    {
        if($cbo_year!=0) $job_year=" and to_char(d.po_received_date,'YYYY')=$cbo_year";
    }

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array_deter);
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	ob_start();

	?>
	<style type='text/css'>
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>
	<fieldset style="width:1920px;">
		<table cellpadding="0" cellspacing="0" width="1920">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="11" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="11" style="font-size:14px"><strong> <? echo "Date : ".change_date_format(str_replace("'","",$txt_date_from));?></strong> To <strong> <? echo change_date_format(str_replace("'","",$txt_date_to));?></strong></td>
			</tr>
		</table>
		<table width="1440" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="110">Booking No.</th>
					<th width="90">Buyer</th>
					<th width="90">Job</th>
					<th width="90">Order</th>
					<th width="90">Style</th>
					<th width="100">Color</th>
					<th width="120">Fabric Des.</th>
					<th width="90">Batch No</th>
					<th width="90">Batch Qty</th>
					<th width="90">Roll No</th>
					<th width="90">Roll Wise Fin Rcv Qty</th>
					<th width="90">Total Issue Qty.</th>
					<th width="90">Balance Qty</th>
					<th width="90">GSM</th>
					<th width="90">F.Dia</th>
				</tr>
			</thead>
		</table>
		<div style="width:1460px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="1440" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
				<?
					$sql_batch_details = "select c.batch_no, c.extention_no, c.id as batch_id, b.quantity, d.qnty, f.buyer_name, c.booking_no, e.id as po_id, e.po_number, f.job_no, f.style_ref_no, c.color_id, a.prod_id, g.detarmination_id, g.gsm, g.dia_width, d.roll_no, d.barcode_no
					from inv_transaction a, order_wise_pro_details b, pro_batch_create_mst c, pro_roll_details d, wo_po_break_down e, wo_po_details_master f, product_details_master g, wo_booking_mst h
					where a.id=b.trans_id and a.pi_wo_batch_no=c.id and b.dtls_id=d.dtls_id and b.entry_form=37 and d.is_sales=0 and d.entry_form=37 and b.po_breakdown_id=e.id and e.job_id=f.id and a.prod_id=g.id and c.booking_no=h.booking_no and a.company_id=$company_id $buyer_id_cond $booking_cond $job_cond $batch_cond $batch_date_cond $booking_date_cond $receive_date_cond $roll_cond $store_cond $product_cond
					order by c.batch_no asc, c.id asc, d.roll_no asc";
					// echo $sql_batch_details; //die;
					$batch_dtls_result = sql_select($sql_batch_details);

					foreach ($batch_dtls_result as $row) {
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["roll_rcv_qty"]+=$row[csf("qnty")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["booking_no"]=$row[csf("booking_no")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["batch_no"]=$row[csf("batch_no")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["buyer_name"]=$row[csf("buyer_name")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["job_no"]=$row[csf("job_no")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["po_number"]=$row[csf("po_number")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["color_id"]=$row[csf("color_id")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["fabrications"]=$constructtion_arr[$row[csf("detarmination_id")]].', '.$composition_arr[$row[csf("detarmination_id")]];

						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["gsm"]=$row[csf("gsm")];
						$batch_data_array[$row[csf("batch_id")]][$row[csf("po_id")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["dia_width"]=$row[csf("dia_width")];
						$batch_data_array[$row[csf("batch_id")]]["batch_qnty"]+=$row[csf("qnty")];
						$batch_data_array[$row[csf("batch_id")]]["count"]++;

						$rID2=execute_query("insert into TMP_BARCODE_NO (userid, entry_form, barcode_no) values ($user_id,999,".$row[csf("barcode_no")].")");
					}
					oci_commit($con);

					/* echo "<pre>";
					print_r($batch_data_array);
					die; */


					$sql_issue = "select a.id, a.transaction_date, a.company_id, a.prod_id, a.pi_wo_batch_no, b.po_breakdown_id as po_id, c.roll_no,
					sum(c.qnty) as issue_qnty
					from order_wise_pro_details b, inv_transaction a, pro_roll_details c, TMP_BARCODE_NO d
					where b.trans_id = a.id and b.dtls_id=c.dtls_id and c.barcode_no=d.barcode_no and d.userid=$user_id and d.entry_form=999 and c.entry_form=71 and c.entry_form=71 and a.transaction_type = 2
					and a.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and a.item_category = 2 and a.company_id = $company_id
					group by a.id, a.transaction_date, a.company_id, a.prod_id, a.pi_wo_batch_no, b.po_breakdown_id,c.roll_no";
					//echo $sql_issue;die;

					$nameArray=sql_select($sql_issue);
					foreach ($nameArray as $row) {
						$transaction_data_array[$row[csf("po_id")]][$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]][$row[csf("roll_no")]]["issue_qnty"]+=$row[csf("issue_qnty")];
					}

					$r_id3=execute_query("delete from TMP_BARCODE_NO where userid=$user_id and entry_form=999");
					oci_commit($con);
					disconnect($con);

					$batch_id_count = array();
					foreach ($batch_data_array as $batch_id => $batch_data)
					{
						foreach ($batch_data as $po_id => $po_data)
						{
							foreach ($po_data as $prod_id => $prod_data)
							{
								foreach ($prod_data as $roll_no => $value)
								{
									$batch_id_count[$batch_id]++;
								}
							}
						}
					}

					$i=1;
					$grand_total_rcv=$grand_total_issue=0;

					foreach ($batch_data_array as $batch_id => $batch_data)
					{
						$j=1;
						foreach ($batch_data as $po_id => $po_data)
						{
							foreach ($po_data as $prod_id => $prod_data)
							{
								foreach ($prod_data as $roll_no => $value)
								{
									$batch_id_span = $batch_id_count[$batch_id];

									$total_iss=$balance=0;

									$total_iss = $transaction_data_array[$po_id][$batch_id][$prod_id][$roll_no]["issue_qnty"];
									$balance = $value["roll_rcv_qty"]- $total_iss;
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<?
										if(!in_array($batch_id,$batch_id_chk))
										{
											$batch_id_chk[]=$batch_id;
											?>
											<td width="30" align="center" rowspan="<? echo $batch_id_span;?>"><? echo $i; ?> </td>
											<?
										}
										?>
										<td width="110" align="center" class="word_wrap_break"><? echo $value["booking_no"] ; ?></td>
										<td width="90" align="center" class="word_wrap_break"><? echo $buyer_arr[$value["buyer_name"]];?></td>
										<td width="90" align="center"><? echo $value["job_no"];?></td>
										<td width="90" align="center"><? echo $value["po_number"];?></td>
										<td width="90" align="center" class="word_wrap_break"><? echo $value["style_ref_no"];?></td>
										<td width="100" align="center" class="word_wrap_break"><? echo $color_arr[$value["color_id"]];?></td>
										<td width="120" align="right" class="word_wrap_break"><? echo $value["fabrications"];?></td>
										<td width="90" align="center"><p><? echo $value["batch_no"];?></p></td>
										<?
										if(!in_array($batch_id,$batch_id_chk1))
										{
											$batch_id_chk1[]=$batch_id;
											$batch_qnty = $batch_data_array[$batch_id]["batch_qnty"];
											?>
											<td width="90" align="center" rowspan="<? echo $batch_id_span;?>"><p><? echo $batch_qnty;?></p></td>
											<?
										}
										?>

										<td width="90" align="right"><p><? echo $roll_no;?></p></td>
										<td width="90" align="right"><p><? echo $value["roll_rcv_qty"];?></p></td>
										<td width="90" align="right"><p><? echo number_format($total_iss);?></p></td>
										<td width="90" align="right"><p><? echo number_format($balance);?></p></td>

										<td width="90" align="right"><p><? echo $value["gsm"];?></p></td>
										<td width="90" align="right"><p><? echo $value["dia_width"];?></p></td>

									</tr>
									<?
									$j++;

									$grand_total_rcv +=  $value["roll_rcv_qty"];
									$grand_total_issue+= $total_iss;
									$grand_total_balance+= $balance;
								}

							}
						}
						$i++;
					}

				?>
			</table>
			<table width="1440" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="90"></th>
					<th width="90"></th>
					<th width="90"></th>
					<th width="90"></th>
					<th width="100"></th>
					<th width="120"></th>
					<th width="90"></th>
					<th width="90"></th>
					<th width="90">Total:</th>
					<th width="90"><? echo $grand_total_rcv;?></th>
					<th width="90"><? echo $grand_total_issue;?></th>
					<th width="90"><? echo $grand_total_balance;?></th>
					<th width="90"></th>
					<th width="90"></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?

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
    echo "$html**$filename";
    exit();
}

//item search------------------------------//
if($action=="item_description_search")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
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

				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push(str);
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

				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}

		function fn_check_lot()
		{
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>+'_'+ document.getElementById('txt_product_id').value, 'create_lot_search_list_view', 'search_div', 'roll_and_batch_wise_finish_fabric_stock_report_controller', 'setFilterGrid("list_view",-1)');
		}

		function search_by_type( val )
		{
			$('#txt_search_common').val('');

			if(val==1)
			{
				$('#search_by_td_up').html('Enter Item Description');
			}
			else if(val==2)
			{
				$('#search_by_td_up').html('Enter Item Code');
			}
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th>Search By</th>
						<th align="center" width="200" id="search_by_td_up">Enter Item Description </th>
						<th align="center" width="100">Product ID</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td align="center">
							<?
								$search_by = array(1=>'Item Description',2=>'Item Code');
								$dd="";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", "search_by_type(this.value);", 0);
							?>
						</td>
						<td width="180" align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td width="100" align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_product_id" id="txt_product_id" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />
						</td>
					</tr>
 				</tbody>
			</table>
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div>
			</form>
	   </div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}

if($action=="create_lot_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$product_id = trim($ex_data[3]);

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for LOT NO
		{
			$sql_cond= " and product_name_details LIKE '%$txt_search_common%'";
 		}
		else if(trim($txt_search_by)==2) // for Yarn Count
		{
			if($txt_search_common==0 || $txt_search_common=="")
			{
				$sql_cond= " ";
			}
			else
			{
				$sql_cond=" and item_code='$txt_search_common'";
			}
 		}
 	}
	if($product_id)
	{
		$sql_cond.= " and id=$product_id";
	}

	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

 	$sql = "select id,product_name_details,gsm,dia_width,color from product_details_master where company_id=$company and item_category_id ='2' $sql_cond";
	$arr=array(4=>$color_arr);
	echo create_list_view("list_view", "Product Id, Item Description, GSM, Dia, Color","70,230,100,100","650","260",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,0,0,0,color", $arr, "id,product_name_details,gsm,dia_width,color", "","","0","",1) ;
	exit();
}