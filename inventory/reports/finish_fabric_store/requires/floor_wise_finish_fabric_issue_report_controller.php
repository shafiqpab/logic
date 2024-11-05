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
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action=="load_drop_down_sewing_com")
{
	list($source,$lc_company )= explode("_",$data);
	if($source==1)
	{
		echo create_drop_down( "cbo_working_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name",0, "--Select Working Company--", "", "","" );
	}
	else if($source==3)
	{
		echo create_drop_down( "cbo_working_company_id", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,21,24,22) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 0, "--Select Working Company--", 1, "" );
	}
	else
	{
		echo create_drop_down( "cbo_working_company_id", 140, $blank_array,"",1, "--Select Working Company--", 1, "" );
	}
	exit();
}



if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and  b.category_type=2 order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
}

if($action=="load_drop_down_cutting_floor")
{
	list($source,$working_company )= explode("_",$data);
	if($source==1){
		echo create_drop_down( "cbo_cutting_floor", 140, "select id,floor_name from lib_prod_floor where company_id in($working_company) and production_process=1 and status_active=1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", 0);
	}else{
		echo create_drop_down( "cbo_cutting_floor", 140, $blank_array,"",1, "--Select Floor--", 1, "" );
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
		
		function fn_generate_list(){
			if((form_validation('txt_search_job','Job')==false) && (form_validation('txt_search_style','Style')==false))
			{
				return;
			}
			else
			{
				show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'floor_wise_finish_fabric_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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

if($action=="create_job_no_search_list_view")
{
	list($company_id,$buyer_name,$job_no,$style,$year_id)=explode('**',$data);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($buyer_name==0)
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
		$buyer_id_cond=" and buyer_name=$buyer_name";
	}

	if($job_no!='') $job_cond=" and job_no like ('%".trim($job_no)."')"; else $job_cond="";
	if($style!='') $style_cond=" and style_ref_no like '".trim($style)."'"; else $style_cond="";

	if($db_type==0) $year_field_by="year(insert_date)";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";

	$arr=array (0=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, buyer_name, style_ref_no, $year_field_by as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Year,Style Ref. No", "170,130,80,60","610","270",0, $sql , "js_set_value", "job_no_prefix_num,job_no", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no,year,style_ref_no", "",'','0,0,0,0','',1) ;
	exit();
}

if($action=="order_no_popup")
{
	echo load_html_head_contents("Order Info", "../../../../", 1, 1,'','','');
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
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( ddd );
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
		
		function fn_generate_list(){
			if((form_validation('txt_search_job','Job')==false) && (form_validation('txt_search_style','Style')==false))
			{
				return;
			}
			else
			{
				show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_order_no_search_list_view', 'search_div', 'floor_wise_finish_fabric_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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
	//$month_id=$data[5];
	//echo $month_id;

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

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

	if($data[2]!='') $job_cond=" and a.job_no_prefix_num=".trim($data[2]).""; else $job_cond="";
	if($data[3]!='') $style_cond=" and a.style_ref_no like '".trim($data[3])."'"; else $style_cond="";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(a.insert_date)";
	else if($db_type==2) $year_field_by="to_char(a.insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";

	$arr=array (0=>$buyer_arr);
	$sql= "select b.id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by a.id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Po Number,Style Ref. No", "100,100,200,60","610","270",0, $sql , "js_set_value", "id,po_number", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no,po_number,style_ref_no", "",'','0,0,0,0','',1) ;
	exit();
}

if($action=="booking_popup")
{
	echo load_html_head_contents("Booking", "../../../../", 1, 1,'','','');
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
			$('#hide_booking_id').val( id );
			$('#hide_booking_no').val( ddd );
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
		
		function fn_generate_list(){
			if((form_validation('txt_booking','Booking')==false) && (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false))
			{
				return;
			}
			else
			{
				show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year_id; ?>', 'create_booking_no_search_list_view', 'search_div', 'floor_wise_finish_fabric_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');			}
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
							<th>Booking</th>
							<th>Booking Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                            <input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:130px" class="text_boxes" name="txt_booking" id="txt_booking" placeholder="Booking No" />
								</td>
								<td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" value="" readonly/>
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" value="" readonly/>
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

if($action=="create_booking_no_search_list_view")
{
	list($company_id,$buyer_name,$booking_no,$strt_sate,$end_date,$year)=explode('**',$data);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$buyer_name";
	}

	if($booking_no!='') $where_cond .=" and a.booking_no like('%".trim($booking_no)."')";
	

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
		$where_cond .=" and a.booking_date between '$strt_sate' and '$end_date'";		
	}


	$arr=array (0=>$buyer_arr);

	$sql = "select a.id,a.buyer_id,a.booking_no,a.job_no, a.booking_date  from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  $buyer_id_cond $where_cond";	
	
	echo create_list_view("tbl_list_search", "Buyer Name,Booking No,Job No,Booking Date", "100,100,200,60","610","270",0, $sql , "js_set_value", "id,booking_no", "", 1, "buyer_id,0,0,0", $arr , "buyer_id,booking_no,job_no,booking_date", "",'','0,0,0,0','',1) ;
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
		
		
		function fn_generate_list(){
			if((form_validation('txt_batch','Company Name')==false) && (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false))
			{
				return;
			}
			else
			{
				show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_batch').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year_id; ?>', 'create_batch_search_list_view', 'search_div', 'floor_wise_finish_fabric_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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
	list($company_id,$batch_no,$strt_sate,$end_date,$year)=explode('**',$data);
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


	$arr=array (1=>$color_arr);

	$sql = "select a.id,a.batch_no,a.batch_date,a.color_id from pro_batch_create_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $where_cond";	
	
	echo create_list_view("tbl_list_search", "Batch No,Color,Batch Date", "200,100,100","610","270",0, $sql , "js_set_value", "id,batch_no", "", 1, "0,color_id,0", $arr , "batch_no,color_id,batch_date", "",'','0,0,3','',1) ;
	exit();
}







if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$cutting_floor_lib=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	$supplier_name_lib=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,21,24,22) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
	
	


	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_source=str_replace("'","",$cbo_source);
	$cbo_working_company_id=str_replace("'","",$cbo_working_company_id);
	$cbo_cutting_floor=str_replace("'","",$cbo_cutting_floor);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$txt_batch_id=str_replace("'","",$txt_batch_id);
	$txt_booking_no_show=str_replace("'","",$txt_booking_no_show);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_order_no_show=str_replace("'","",$txt_order_no_show);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$cbo_order_type=str_replace("'","",$cbo_order_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	//with order........................
	if($cbo_source){$whereCon =" and a.knit_dye_source=$cbo_source";}
	if($cbo_buyer_id){$whereCon .=" and e.buyer_name=$cbo_buyer_id";}
	if($cbo_store_name){$whereCon .=" and b.store_id=$cbo_store_name";}
	if($cbo_working_company_id){$whereCon .=" and a.knit_dye_company in($cbo_working_company_id)";}
	if($cbo_company_id){$whereCon .=" and a.company_id in($cbo_company_id)";}
	if($cbo_cutting_floor){$whereCon .=" and b.cutting_unit in($cbo_cutting_floor)";}
	
	
	
	if($txt_batch_no){$whereCon .=" and g.batch_no in('".str_replace(',',"','",$txt_batch_no)."')";}
	if($txt_batch_id && $txt_batch_no){$whereCon .=" and b.batch_id in($txt_batch_id)";}
	
	
	
	if($txt_job_no){$whereCon .=" and e.job_no like('%$txt_job_no')";}
	if($txt_job_id && $txt_job_no){$whereCon .=" and e.job_no_prefix_num in($txt_job_id)";}
	
	if($txt_order_no_show){$whereCon .=" and d.po_number in('".str_replace(',',"','",$txt_order_no_show)."')";}
	if($txt_order_no){$whereCon .=" and c.po_breakdown_id in($txt_order_no)";}
	
	
	if($txt_booking_no_show){$whereCon .=" and g.booking_no in('".str_replace(',',"','",$txt_booking_no_show)."')";}
	
	
	if($txt_date_from!='' and $txt_date_to!=''){
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$txt_date_from=change_date_format($txt_date_from,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);
		}
		$whereCon .=" and a.issue_date between '$txt_date_from' and '$txt_date_to'";		
	}
	
	//echo $whereCon;
	
	//without order........................
	if($cbo_source){$whereCon2 =" and a.knit_dye_source=$cbo_source";}
	if($cbo_store_name){$whereCon2 .=" and b.store_id=$cbo_store_name";}
	if($cbo_working_company_id){$whereCon2 .=" and a.knit_dye_company in($cbo_working_company_id)";}
	if($cbo_company_id){$whereCon2 .=" and a.company_id in($cbo_company_id)";}
	if($cbo_cutting_floor){$whereCon2 .=" and b.cutting_unit in($cbo_cutting_floor)";}
	if($txt_batch_id){$whereCon2 .=" and b.batch_id in($txt_batch_id)";}
	if($txt_booking_no_show){$whereCon2 .=" and d.booking_no in('".str_replace(',',"','",$txt_booking_no_show)."')";}
	
	if($txt_date_from!='' and $txt_date_to!=''){
		$whereCon2 .=" and a.issue_date between '$txt_date_from' and '$txt_date_to'";
	}
	
	
	
	
/*$sql_query = "select c.transaction_date,a.buyer_name, c.id as trans_id,
	(case when c.transaction_type = 1 then d.quantity else 0 end) as receive_qnty,
	(case when c.transaction_type=2 then d.quantity else 0 end) as issue_qnty,
	(case when c.transaction_type=3 then d.quantity else 0 end) as rec_ret_qnty,
	(case when c.transaction_type =4 then d.quantity else 0 end) as issue_ret_qnty,
	(case when c.transaction_type=5 then d.quantity else 0 end) as rec_trns_qnty,
	(case when c.transaction_type =6 then d.quantity else 0 end) as issue_trns_qnty
	from  wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
	where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0
	and d.entry_form in (7,37,66,68,15,18,71,126,134,17,19,195,196,46,52) and c.item_category = $cbo_item_category and c.status_active=1 and c.is_deleted=0 and c.id=d.trans_id and d.trans_id!=0
	and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id=b.id and
	a.company_name='$cbo_company_id' and c.transaction_date between $txt_date_from and $txt_date_to $buyer_id_cond $cbo_product_category_cond $search_cond
	order by c.transaction_date,a.buyer_name";*/

	if($cbo_order_type==1){
		$sql_query="select a.id,a.issue_purpose,a.company_id,a.knit_dye_company, a.issue_number, a.issue_date,a.challan_no,g.booking_no, b.prod_id,b.batch_id, b.cutting_unit, c.quantity,c.po_breakdown_id,c.color_id,d.po_number,d.job_no_mst,e.style_ref_no,e.buyer_name,(e.total_set_qnty*d.po_quantity) as po_qty,f.product_name_details
		from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, wo_po_break_down d,wo_po_details_master e,product_details_master f,pro_batch_create_mst g
		where a.id=b.mst_id and b.id=c.dtls_id and d.id=c.po_breakdown_id and e.job_no=d.job_no_mst and f.id=b.prod_id and b.batch_id=g.id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $whereCon ";
	}
	else{
		
		
		$sql_query="select a.id,a.issue_purpose,a.company_id,a.knit_dye_company, a.issue_number, a.issue_date,a.challan_no,a.booking_no,a.buyer_id as buyer_name, b.prod_id,b.batch_id, b.cutting_unit, b.issue_qnty as quantity,c.color as color_id, d.booking_no
,c.product_name_details from inv_issue_master a, inv_finish_fabric_issue_dtls b,product_details_master c,pro_batch_create_mst d
		where a.id=b.mst_id and c.id=b.prod_id and d.id=b.batch_id and a.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.issue_purpose=8 $whereCon2 ";
	}
	
	// echo $sql_query;
	
	$nameArray=sql_select($sql_query);
	$dataArray = array();$issue_qty_arr=array();
	foreach ($nameArray as $val)
	{
		$key=$val[csf("knit_dye_company")].$val[csf("cutting_unit")].$val[csf("buyer_name")].$val[csf("job_no_mst")].$val[csf("po_breakdown_id")].$val[csf("challan_no")].$val[csf("batch_id")].$val[csf("issue_date")];
		
		$issue_qty_arr[$key]+=$val[csf("quantity")];
		
		$dataArray[$key]= array(
			challan_no=>$val[csf("challan_no")],
			knit_dye_company=>$val[csf("knit_dye_company")],
			issue_purpose=>$val[csf("issue_purpose")],
			issue_date=>$val[csf("issue_date")],
			batch_id=>$val[csf("batch_id")],
			cutting_unit=>$val[csf("cutting_unit")],
			color_id=>$val[csf("color_id")],
			job_no_mst=>$val[csf("job_no_mst")],
			po_number=>$val[csf("po_number")],
			style_ref_no=>$val[csf("style_ref_no")],
			buyer_name=>$val[csf("buyer_name")],
			po_qty=>$val[csf("po_qty")],
			product_name_details=>$val[csf("product_name_details")],
			booking_no=>$val[csf("booking_no")],
			quantity=>$issue_qty_arr[$key],
		);
		
		$key=$val[csf("knit_dye_company")].'*'.$val[csf("buyer_name")].'*'.$val[csf("cutting_unit")];
		$buyerSummary[$key]+=$val[csf("quantity")];
		
		
		
		$batchArr[$val[csf("batch_id")]]=$val[csf("batch_id")];
		//$dataArray[$key]["issue_qnty"] += $val[csf("issue_qnty")];
	} 
	
	
	$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(',',$batchArr).")", "id", "batch_no");	
	
	
$working_company_arr=($cbo_source==1)?$company_arr:$supplier_name_lib;	
	
	$width=1550;
	ob_start();

 		?>
			<fieldset>
				<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" align="center">
					<tr  class="form_caption">
						<td align="center" colspan="11" style="font-size:18px"><? echo $company_arr[$cbo_company_id]; ?></td>
					</tr>
					<tr class="form_caption">
						<td align="center" colspan="11" style="font-size:16px"><? echo $report_title; ?></td>
					</tr>
                    <tr  class="form_caption">
						<td align="center" colspan="11" style="font-size:12px"><? echo "Date : ".change_date_format($txt_date_from);?> To <? echo change_date_format($txt_date_to);?></td>
					</tr>
				</table>
                
                <div id="summary">
                <h2>Summary</h2>
				<table width="500" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
                        <th width="35">SL</th>
                        <th width="150">Working Company</th>
                        <th width="130">Cutting Floor</th>
                        <th width="100">Buyer</th>
                        <th>Issue Qty</th>
					</thead>
                    <tbody>
                       <? 
					   $total_issue_qty=0;
					   $i=1;
					   foreach($buyerSummary as $key=>$issueQty){
					    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
					   	list($wc_company,$buyer_id,$floor)=explode('*',$key);
					   ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('str<? echo $i;?>','<? echo $bgcolor;?>')" id="str<? echo $i;?>">
                            <td align="center"><? echo $i; ?></td>
                            <td><p><? echo $working_company_arr[$wc_company]; ?></p></td>
                            <td><p><? echo $cutting_floor_lib[$floor]; ?></p></td>
                            <td><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
                            <td align="right"><p><? echo number_format($issueQty,2); ?></p></td>
                        </tr>
                        <? 
							$total_issue_qty+=$issueQty;
							$i++;
						} 
						?>
                    </tbody>
                    <tfoot>
                        <th colspan="4">Grand Total  </th>
                        <th align="right"><? echo number_format($total_issue_qty,2);?></th>
                    </tfoot>
				</table>
                </div>
               
                <div id="details">
                <h2>Details</h2>
				<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<tr>
                            <th width="35">SL</th>
                            <th width="100">Working Company</th>
                            <th width="100">Cutting Floor</th>
                            <th width="100">Buyer</th>
                            <th width="100">Job No</th>
                            <th width="100">Fab. Booking No</th>
                            <th width="100">Style No</th>
                            <th width="100">Order No</th>
                            <th width="100">Order Qty.</th>
                            <th width="100">Issue Purpose</th>
                            <th width="100">Challan No</th>
                            <th width="100">Batch No</th>
                            <th width="100">Fabric Description</th>
                            <th width="100">Color</th>
                            <th width="100">Issue Date</th>
                            <th>Issue Qty.</th>
						</tr>
					</thead>
				</table>
				<div style=" width:<? echo $width+20;?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
					<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
						<?
							$i=1;
							$total_issue_qty=0;
							foreach ($dataArray as $row)
							{
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
									
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									
										<td width="35" align="center"><? echo $i; ?></td>
										<td width="100"><p><? echo $working_company_arr[$row["knit_dye_company"]]; ?></p></td>
										<td width="100"><p><? echo $cutting_floor_lib[$row["cutting_unit"]];?></p></td>
										<td width="100"><p><? echo $buyer_arr[$row["buyer_name"]];?></p></td>
										<td width="100"><p><? echo $row["job_no_mst"];?></p></td>
										<td width="100"><p><? echo $row["booking_no"];?></p></td>
										<td width="100"><p><? echo $row["style_ref_no"];?></p></td>
										<td width="100"><p><? echo $row["po_number"];?></p></td>
										<td width="100" align="right"><p><? echo number_format($row["po_qty"]);?></p></td>
										<td width="100"><p><? echo $yarn_issue_purpose[$row["issue_purpose"]];?></p></td>
										<td width="100"><p><? echo $row["challan_no"];?></p></td>
										<td width="100"><p><? echo $batch_no_arr[$row["batch_id"]];?></p></td>
										<td width="100"><p><? echo $row["product_name_details"]; ?></p></td>
										<td width="100"><p><? echo $color_arr[$row["color_id"]];?></p></td>
										<td width="100" align="center"><? echo change_date_format($row["issue_date"]);?></td>
										<td align="right"><p><? echo number_format($row["quantity"],2);?></p></td>

									</tr>
									<?
									$i++;
									$total_issue_qty+=$row["quantity"];

							}

						?>
					</table>
                    </div>
					<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
						<tfoot>
							<th width="35"></th>
							<th width="100">Grand Total</th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th align="right" id="value_td_total_issue_qty"><? echo number_format($total_issue_qty,2);?></th>
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



if($action=="issue_ret_popup")
{
	echo load_html_head_contents("Issue Ret. Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1080px;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

			/*	$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        }); */

    </script>
    <?
    ob_start();
    ?>
    <div id="scroll_body" align="center">
    	<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
    		<tr>
    			<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
    			<td> <div id="report_container"> </div> </td>
    		</tr>
    	</table>
    	<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="center">
    		<thead>
    			<tr>
    				<th colspan="11">Issue Return Details</th>
    			</tr>
    			<tr>
    				<th width="30">Sl</th>
    				<th width="110">System ID</th>
    				<th width="80">Ret. Date</th>
    				<th width="80">Dyeing Source</th>
    				<th width="120">Dyeing Company</th>
    				<th width="100">Challan No</th>
    				<th width="100">Color</th>
    				<th width="100">Batch No</th>
    				<th width="80">Rack</th>
    				<th width="80">Ret. Qty</th>
    				<th width="">Fabric Des.</th>

    			</tr>
    		</thead>
    		<tbody>
    			<?
    			$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
    			$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
    			$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";
    			$result_issue=sql_select($sql_issue);
    			$issue_arr=array();
    			foreach($result_issue as $row)
    			{
    				$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
    				$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
    				$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
    			}

    			$i=1;

    			$ret_sql="select a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
    			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52,126) and c.entry_form in (52,126)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.trans_id!=0 group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id, b.pi_wo_batch_no,c.color_id";
					//echo $ret_sql;

    			$retDataArray=sql_select($ret_sql);

    			foreach($retDataArray as $row)
    			{
    				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
    				$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
						//echo $row[csf('pi_wo_batch_no')].'='.$batch_no_arr[$row[csf('pi_wo_batch_no')]];
    				$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
    				$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];

    				if($knit_dye_source==1)
    				{
    					$knitting_company=$company_arr[$knit_dye_company];
    				}
    				else
    				{
    					$knitting_company=$supplier_name_arr[$knit_dye_company];
    				}


    				?>
    				<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
    					<td width="30"><p><? echo $i; ?></p></td>
    					<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
    					<td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
    					<td width="80"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
    					<td width="120" ><p><? echo $knitting_company; ?></p></td>
    					<td width="100" ><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
    					<td  width="100" align="right"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
    					<td  width="100" align="right"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
    					<td  width="80" align="right"><p><? echo $row[csf('Rack')]; ?></p></td>
    					<td  width="80" align="right"><p><? echo $row[csf('quantity')]; ?></p></td>

    					<td align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
    				</tr>
    				<?
    				$tot_issue_return_qty+=$row[csf('quantity')];
						//$tot_returnable_qnty+=$row[csf('returnable_qnty')];
    				$i++;
    			}
    			?>
    		</tbody>
    		<tfoot>
    			<tr class="tbl_bottom">
    				<td colspan="9" align="right">Total</td>
    				<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
    				<td align="right">&nbsp;</td>
    			</tr>
    		</tfoot>
    	</table>
    </div>
    <?
	    $html=ob_get_contents();
	    ob_flush();

	    foreach (glob(""."*.xls") as $filename)
	    {
	    	@unlink($filename);
	    }
				//html to xls convert
	    $name=time();
	    $name=$user_id."_".$name.".xls";
	    $create_new_excel = fopen(''.$name, 'w');
	    $is_created = fwrite($create_new_excel,$html);
	    ?>
	    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	    <script>
	    	$(document).ready(function(e) {
	    		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
	    	});

	    </script>
	</fieldset>

	<?
	exit();
}


if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );


	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
		}

	</script>

	<?
	ob_start();
	?>
	<fieldset style="width:970px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="10">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="60">Rack No</th>
						<th width="80">Qty</th>
                       <!-- <th width="80">Trans. Out Qty.</th>-->
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$i=1;

					$sql_trans_out="select a.id, a.transfer_system_id as issue_number,a.to_company, a.transfer_date as issue_date,a.challan_no, b.uom, b.from_prod_id, (c.quantity) as quantity_out, b.batch_id,to_rack as rack_no,c.prod_id, c.quantity,c.color_id from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 ";
					//group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, b.batch_id,c.prod_id, c.quantity,c.color_id
					$trans_out=sql_select($sql_trans_out);

					$mrr_sql="select a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity,c.color_id
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID'  and c.color_id='$color'";
					//echo $mrr_sql;

					$dtlsArray=sql_select($mrr_sql);
					$tot_out_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="60" ><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                           <!-- <td width="80" align="right" ><p><? //echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>-->
							<td  align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"><? //echo number_format($tot_out_qty,2); ?></td>

					</tr>

				</tfoot>
			</table>

			<br>
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="display:none">
				<thead>
					<tr>
						<th colspan="6">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="70">Transfer Date</th>
						<th width="200">Fabric Des.</th>
						<th width="50">UOM</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					//$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$companyID and a.id=b.mst_id and a.transfer_criteria=4 and a.from_order_id in($po_id) and b.from_prod_id in ($prod_id) and a.item_category=2 group by a.from_order_id, b.from_prod_id, a.id, a.transfer_system_id, a.transfer_date, a.from_order_id, b.from_prod_id, b.uom";

					$sql_transfer_out="select a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id, sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(15,134) and c.trans_type=6 and c.color_id='$color' and a.transfer_criteria=4 and a.from_order_id in($po_id) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.uom, b.from_prod_id";
					$transfer_out=sql_select($sql_transfer_out);
					foreach($transfer_out as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
							<td width="50" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?> &nbsp;</p></td>
							<td  align="right"><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Issue Balance</td>
						<td align="right"><? $tot_iss_bal=$tot_qty+$tot_trans_qty; echo number_format($tot_iss_bal,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</table>
	</div>
	<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
				//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
	</fieldset>
	<?
	exit();
} // Issue End


if($action=="woven_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
		}

	</script>

	<?
	ob_start();
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th width="30">Sl</th>
					<th width="100">Issue ID</th>
					<th width="75">Issue Date</th>
					<th width="230">Fabric Description</th>
					<th>Qty</th>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;

					$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id, c.quantity
					from  inv_issue_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and c.color_id='$color'";
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}
