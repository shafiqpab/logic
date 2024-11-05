<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=34 and is_deleted=0 and status_active=1");
	if($print_report_format=='') $print_report_format=0;else $print_report_format=$print_report_format;
	echo "document.getElementById('hidden_report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;

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
			// alert(str[0]);
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
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'style_owner_and_party_wise_yarn_reconciliation_summary_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//echo $year_id;die;
	$month_id=$data[5];


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

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no_prefix_num";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by=" and YEAR(insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by=" and to_char(insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(insert_date) as year";
	else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	if($month_id!=0) $month_cond=" $month_field_by=$month_id"; else $month_cond="";


	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field  from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond order by job_no";
	//echo $sql;die;

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;

	exit();
}

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
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

			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
	</script>
	<input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
	<input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	if ($cbo_knitting_source==3)
	{
		$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(1,9,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
	}
	elseif($cbo_knitting_source==1)
	{
		$sql="select id, company_name as party_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;

	exit();
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id=new Array; var selected_name=new Array; var booking_type=new Array; var job_no=new Array;

		function check_all_data()
		{
			// alert('find');return;
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value2( str )
		{
			// alert(str);return;
			if (str!="") str=str.split("_");
			if(booking_type.length==0)
			{
				booking_type.push( str[3] );
			}
			else if( jQuery.inArray( str[3], booking_type )== -1 &&  booking_type.length>0)
			{
				alert("Booking Mixed is Not Allow");return;
			}

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[4], job_no ) == -1 )
			{
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				job_no.push( str[4] );
				// alert(job_no+'Test');
			}
			else
			{
				// alert(job_no+'=Test2');
				for( var i = 0; i < job_no.length; i++ )
				{
					// alert(job_no[i] +'=='+ str[4] );
					if( job_no[i] == str[4] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				job_no.splice( i, 1 );

			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			//alert(id+'*'+name+'='+str[3]);return;// 16845*OG-Fb-20-00063=1

			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			//$('#hide_recv_id').val( rec_id );
			$("#hide_booing_type").val(str[3]);
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
							<th>Booking Type</th>

							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Booking No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_booing_type" id="hide_booing_type" value="" />
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
									$search_by_arr=array(1=>"With Order",2=>"Without Order");
									$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down( "cbo_booking_type", 100, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Booking No");
									$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
									echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value+'**'+'<? echo $job_ids;?>', 'create_booking_no_search_list_view', 'search_div', 'style_owner_and_party_wise_yarn_reconciliation_summary_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//echo $year_id;die;
	$month_id=$data[5];
	$booking_type=$data[6];
	$job_ids=$data[7];

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

	if($search_by==2)
	{
		$search_field="a.style_ref_no";
	}
	else if($search_by==1)
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
	if($job_ids!="") $job_cond = " and a.id in ($job_ids) ";

	if($booking_type==1)
	{
		 $sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.id as booking_id
		 from wo_po_details_master a, wo_booking_dtls b, wo_booking_mst c
		 where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4,3) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond $job_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id  order by a.job_no desc";
	}
	else if($booking_type==2 && $job_ids =="")
	{
		$sql= "select a.company_id as company_name, a.buyer_id as buyer_name, b.style_des as style_ref_no,a.booking_no,a.id as booking_id  from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_type=4 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond2 $year_cond $month_cond group by a.booking_no,a.company_id, a.buyer_id, b.style_des,a.id  order by a.booking_no desc";
	}
	//echo $sql;die;
	$sqlResult=sql_select($sql);
	?>

	<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="130">Company</th>
				<th width="110">Buyer</th>
				<th width="110">Job No</th>
				<th width="120">Style Ref.</th>
				<th width="">Booking No</th>

			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach($sqlResult as $row )
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no')].'_'.$booking_type.'_'.$row[csf('job_no')];
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
		<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/>
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

	<?

	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();

	if($type==1) //Source and Party and Booking Wise
	{
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_style_owner=str_replace("'","",$cbo_style_owner);
		$txt_job_no=str_replace("'","",$txt_job_no);
		$txt_booking_no=str_replace("'","",$txt_booking_no);
		$txt_internal_ref=str_replace("'","",$txt_internal_ref);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$txt_job_id=str_replace("'","",$txt_job_id);
		$hide_booking_id=str_replace("'","",$hide_booking_id);
		$hide_booking_type=str_replace("'","",$hide_booking_type);
		$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
		$txt_knitting_com_id=str_replace("'","",$txt_knitting_com_id);
		$cbo_year=str_replace("'","",$cbo_year);

		if($cbo_knitting_source) $source_cond = " and a.knitting_source=".$cbo_knitting_source;
		if($txt_knitting_com_id!="") $source_cond = " and a.knitting_company in (".$txt_knitting_com_id.")";

		if($cbo_knitting_source) $source_cond2 = " and a.knit_dye_source=".$cbo_knitting_source;
		if($txt_knitting_com_id!="") $source_cond2 = " and a.knit_dye_company in (".$txt_knitting_com_id.")";

		$issue_wise_location=return_library_array( "select id, location_id from inv_issue_master where status_active=1 and is_deleted=0 and entry_form=3 and issue_basis in(1,3) and issue_purpose in(1,4,8) and company_id=$cbo_company_name", "id", "location_id");

		$con = connect();
        $r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4,5,6,7) and ENTRY_FORM = 99");
        if($r_id1)
        {
            oci_commit($con);
			disconnect($con);
        }

		$all_data_arr=array(); $all_po_check=array();$allPoId=array();$issue_po_check=array();$all_po_id="";

		if($txt_job_no =="" && $txt_booking_no =="" && $txt_internal_ref =="" && $txt_date_from !="" && $txt_date_to !="" )
		{
			$issue_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.brand_id, b.cons_uom, b.cons_quantity,b.remarks, b.requisition_no, c.po_breakdown_id as po_breakdown_id
			from inv_issue_master a, inv_transaction b, order_wise_pro_details c
			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and nvl(c.is_sales,0)!=1 and a.issue_purpose in(1,4) and b.transaction_type=2 and c.trans_type=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond2
			union all
			select a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.brand_id, b.cons_quantity,b.remarks, b.requisition_no, 0 as po_breakdown_id
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and a.entry_form=3 and a.issue_purpose=8 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond2";

			//echo $issue_sql;die;

			$issue_result=sql_select($issue_sql);

			if(count($issue_result)<1)
			{
				echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
			}

			foreach($issue_result as $row)
			{
				if($trans_check[$row[csf("mst_id")]][$row[csf("trans_id")]]=="")
				{
					$trans_check[$row[csf("mst_id")]][$row[csf("trans_id")]]=$row[csf("trans_id")];

					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("issue_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				if($issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
				}

				if($all_po_check[$row[csf("po_breakdown_id")]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$all_po_check[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
					$allPoId[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				}
			}
			//var_dump($allPoId);

			unset($issue_result);
			$book_without_order_cond="";
			if($db_type==0)
			{
				$book_without_order_cond=" and (b.booking_without_order=0 or b.booking_without_order='')";
			}
			else
			{
				$book_without_order_cond=" and (b.booking_without_order=0 or b.booking_without_order is null)";
			}

			$receive_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, b.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no,b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, c.po_breakdown_id as po_breakdown_id, a.issue_id
			from inv_receive_master a, inv_transaction b , order_wise_pro_details c
			where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(1,13) and a.entry_form in(9,58) and c.entry_form in(9,58) and nvl(c.is_sales,0)!=1 and b.transaction_type in(1,4) and c.trans_type in(1,4) and a.receive_basis in(1,2,3,9,10,11)  $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond
			union all
			select a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, b.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id , a.issue_id
			from inv_receive_master a, inv_transaction b
			where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,58) and b.transaction_type in(1,4) and a.receive_basis in(1,2,3,9,10,11) and b.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.transaction_date between '$txt_date_from' and '$txt_date_to' $source_cond";

			//echo $receive_sql;die; //entry_form in(9,2,22,58)
			$receive_result=sql_select($receive_sql);

            $recvIdChk = array();$recvIdArr = array();
			foreach($receive_result as $row)
			{
				if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
				{
					$location_id="";
					if($row[csf('entry_form')]==9)
					{
						$location_id=$issue_wise_location[$row[csf('issue_id')]];
					}
					else
					{
						$location_id=$row[csf('location_id')];
					}
					$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];

					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];

					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}
				if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
				}

				if($all_po_check[$row[csf("po_breakdown_id")]]=="" && $row[csf("po_breakdown_id")]>0)
				{
					$all_po_check[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
					$allPoId[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				}

                if($recvIdChk[$row[csf('mst_id')]] == "")
                {
                    $recvIdChk[$row[csf('mst_id')]] = $row[csf('mst_id')];
					$recvIdArr[$row[csf("mst_id")]] = $row[csf("mst_id")];
                }
			}

			unset($receive_result);

            //var_dump($recvIdArr);die;
            if(!empty($recvIdArr))
            {
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 99, 5,$recvIdArr, $empty_arr);
				//die;

                $roll_rcv_sql="SELECT a.id as mst_id, a.recv_number, c.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.trans_id, b.prod_id, 0 as pi_wo_batch_no, b.uom as cons_uom, 0 as brand_id, b.grey_receive_qnty as cons_quantity, 0 as return_qnty, b.reject_fabric_receive as cons_reject_qnty, 1 as transaction_type, c.po_breakdown_id as po_breakdown_id
                from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, gbl_temp_engine d
                where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and nvl(c.is_sales,0)!=1 and a.receive_basis in(10) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and a.id=d.ref_val and d.user_id=$user_id and d.ref_from=5 and d.entry_form=99";

                //echo $roll_rcv_sql;die;
				$roll_rcv_sql_rslt=sql_select($roll_rcv_sql);

				$all_roll_data_arr = array();
				foreach($roll_rcv_sql_rslt as $row)
				{
					$all_roll_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
				}
            }

			if(!empty($allPoId))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 99, 1,$allPoId, $empty_arr);
				//die;

				$order_sql="SELECT a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_break_down b, wo_po_details_master a, gbl_temp_engine c where b.job_id = a.id and b.id=c.ref_val and c.user_id=$user_id and c.ref_from=1 and c.entry_form=99 ";
				//echo $order_sql;die;
				$datapoArray=sql_select($order_sql);
				$po_arr = array();
				foreach($datapoArray as $row)
				{
					$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
					$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
					$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
					$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];
				}

				unset($datapoArray);

				$sql_requ= "SELECT a.dtls_id, a.booking_no, b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where b.knit_id=a.dtls_id  and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//echo $sql_requ;die;
				$result_requ=sql_select($sql_requ);
				$requisition_booking_arr=array();
				$grey_booking_no=array();
				foreach($result_requ as $row)
				{
					$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
					$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];
				}
			}
		}
		else
		{
			if($txt_booking_no !="" && $hide_booking_type==2)
			{
				$booking_ord_sql="select a.company_id as company_name, a.buyer_id as buyer_name, a.booking_no, a.id as booking_id
				from wo_non_ord_samp_booking_mst a
				where  a.id in($hide_booking_id)";
				$booking_ord_result=sql_select($booking_ord_sql);
				foreach($booking_ord_result as $row)
				{
					$po_arr[$row[csf('booking_id')]]['buyer_name']=$row[csf('buyer_name')];
				}

				unset($booking_ord_result);

				if($txt_date_from!="" && $txt_date_to!="")
				{
					$date_condtion=" and b.transaction_date between '$txt_date_from' and '$txt_date_to'";
				}


				$issue_booking_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity,b.remarks, 0 as po_breakdown_id
				from inv_issue_master a, inv_transaction b
				where a.id=b.mst_id and a.entry_form=3 and a.issue_purpose=8 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1) and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond2 ";

				//echo $issue_sql;die;
				$issue_booking_result=sql_select($issue_booking_sql);

				$book_arr=explode(",",$txt_booking_no);
				$book_cond="";
				foreach($book_arr as $book_no)
				{
					$book_cond.="'".$book_no."',";
				}
				$book_cond=chop($book_cond,",");
				$sql_requ= "SELECT a.dtls_id, a.booking_no,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0 and a.booking_no in($book_cond)";
				//echo $sql_requ;die;
				$result_requ=sql_select($sql_requ);
				$requisition_booking_arr=array();
				$grey_booking_no=array();
				foreach($result_requ as $row)
				{
					$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
					$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];

					$all_req_no.=$row[csf('requisition_no')].",";
				}

				$all_req_no=implode(",",array_unique(explode(",",chop($all_req_no,","))));
				if($all_req_no=="") $req_no_cond=""; else $req_no_cond=" and b.requisition_no in($all_req_no)";

				if($all_req_no !="")
				{
					$issue_req_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.cons_uom, b.requisition_no, b.brand_id, b.cons_quantity,b.remarks, 0 as po_breakdown_id
					from inv_issue_master a, inv_transaction b
					where a.id=b.mst_id and a.entry_form=3 and a.issue_purpose=8 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(3) and a.company_id=$cbo_company_name $req_no_cond $date_condtion $source_cond2 ";
					$issue_req_result=sql_select($issue_req_sql);
				}

				$issue_result = array_merge($issue_booking_result,$issue_req_result);

				if(count($issue_result)<1)
				{
					echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
				}

				foreach($issue_result as $row)
				{
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}

				unset($issue_result);


				$sql_requ_smn= "SELECT a.id AS mst_id,a.recv_number, a.booking_id,d.dtls_id,d.booking_no
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, ppl_planning_entry_plan_dtls d
				WHERE  a.id = b.mst_id AND b.id = c.dtls_id and cast(d.dtls_id as varchar2(4000))=c.booking_no AND a.entry_form IN (58) AND c.entry_form IN (58) AND NVL (c.is_sales, 0) != 1 AND a.receive_basis IN (10) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND a.company_id = 9 AND d.booking_no IN ($book_cond) ";
				//echo $sql_requ_smn;die;
				$result_requ_smn=sql_select($sql_requ_smn);
				$requisition_booking_arr=array();
				$grey_booking_no=array();
				foreach($result_requ_smn as $row)
				{
					$all_booking_id.=$row[csf('booking_id')].",";
				}

				$all_booking_id=implode(",",array_unique(explode(",",chop($all_booking_id,","))));
				if($all_booking_id=="") $booking_id_cond=""; else $booking_id_cond=" and a.booking_id in($all_booking_id)";

				if($all_booking_id !="")
				{
					$receive_smn_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id, a.issue_id
					from inv_receive_master a, inv_transaction b
					where a.id=b.mst_id and b.item_category in(13) and a.entry_form in(58) and b.transaction_type in(1,4) and a.receive_basis in(10) and a.booking_without_order=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $booking_id_cond $date_condtion $source_cond";
					//echo $receive_smn_sql;die;
					$receive_smn_result=sql_select($receive_smn_sql);
				}

				$receive_sql1="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, 0 as po_breakdown_id, a.issue_id
				from inv_receive_master a, inv_transaction b
				where a.id=b.mst_id and b.item_category in(1,13) and a.entry_form in(9,58) and b.transaction_type in(1,4) and a.receive_basis in(1,2,3,9,10,11) and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond";


				//echo $receive_sql;die;
				$receive_result1=sql_select($receive_sql1); // [$row[csf('location_id')]]

				$receive_result = array_merge($receive_result1,$receive_smn_result);

				foreach($receive_result as $row)
				{
					$location_id="";
					if($row[csf('entry_form')]==9)
					{
						$location_id=$issue_wise_location[$row[csf('issue_id')]];
					}
					else
					{
						$location_id=$row[csf('location_id')];
					}

					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];

					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];

					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];

					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
					$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
				}

				unset($receive_result);
			}
			else
			{
				if($txt_date_from!="" && $txt_date_to!="")
				{
					$date_condtion=" and b.transaction_date between '$txt_date_from' and '$txt_date_to'";
				}

				if($db_type==0) $year_field_by=" and YEAR(a.insert_date)";
				else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
				else $year_field_by="";

				$year_cond="";
				if($cbo_year > 0) $year_cond=" $year_field_by=$cbo_year";

				if($txt_booking_no !="")
				{
					$all_po_check=array();$allPoId=array();
					$booking_ord_sql= "SELECT a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d
					where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.id in($hide_booking_id) $year_cond";
					// echo $booking_ord_sql;die;
					$booking_ord_result=sql_select($booking_ord_sql);
					foreach($booking_ord_result as $row)
					{
						$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
						$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
						$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
						$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
						$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
						$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];
						if($all_po_check[$row[csf('id')]]=="")
						{
							$all_po_check[$row[csf('id')]]=$row[csf('id')];
							$allPoId[$row[csf("id")]] = $row[csf("id")];
						}
					}

					unset($booking_ord_result);

					$book_arr=explode(",",$txt_booking_no);
					$book_cond="";
					foreach($book_arr as $book_no)
					{
						$book_cond.="'".$book_no."',";
					}
					$book_cond=chop($book_cond,",");
					$sql_requ= "select a.dtls_id, a.booking_no,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0 and a.booking_no in($book_cond)";
					//echo $sql_requ;die;
					$result_requ=sql_select($sql_requ);
					$requisition_booking_arr=array();
					$grey_booking_no=array();
					foreach($result_requ as $row)
					{
						$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
						$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];

						$all_plan_id.=$row[csf('dtls_id')].",";
						$all_req_no.=$row[csf('requisition_no')].",";
					}
					$all_plan_id=implode(",",array_unique(explode(",",chop($all_plan_id,","))));
					$all_req_no=implode(",",array_unique(explode(",",chop($all_req_no,","))));

					//echo $all_plan_id;die;
					if($all_req_no=="") $req_no_cond=""; else $req_no_cond=" and b.requisition_no in($all_req_no)";
					if($all_req_no=="") $iss_rtn_req_no_cond=""; else $iss_rtn_req_no_cond=" and a.booking_id in($all_req_no)";

					if($db_type==0) $trans_year=" and YEAR(b.transaction_date)";
					else if($db_type==2) $trans_year=" and to_char(b.transaction_date,'YYYY')";
					else $trans_year="";

					$trans_year_cond="";
					if($cbo_year > 0) $trans_year_cond=" $trans_year=$cbo_year";

					if($hide_booking_id !="")
					{
						$issue_book_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.requisition_no, b.brand_id, b.cons_uom, b.cons_quantity,b.remarks, c.po_breakdown_id as po_breakdown_id
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and nvl(c.is_sales,0)!=1 and a.issue_purpose in(1,4) and b.transaction_type=2 and c.trans_type=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1) and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond2 $trans_year_cond";
						//c.is_sales!=1 and
						//echo $issue_sql;die;
						$issue_book_result=sql_select($issue_book_sql);
					}

					if($all_req_no !="")
					{
						$issue_req_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.requisition_no, b.brand_id, b.cons_uom, b.cons_quantity,b.remarks, c.po_breakdown_id as po_breakdown_id
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and a.issue_purpose in(1,4) and b.transaction_type=2 and c.trans_type=2 and  nvl(c.is_sales,0)!=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(3) and a.company_id=$cbo_company_name $req_no_cond $date_condtion $source_cond2 $trans_year_cond";
						$issue_req_result=sql_select($issue_req_sql);
					}

					$issue_result= array_merge($issue_book_result,$issue_req_result);

					if(count($issue_result)<1)
					{
						echo '<span style="font-weight: bold; font-size:20px;">No Data Found</span>';die;
					}
					foreach($issue_result as $row) //
					{
						if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
						{
							$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
						}
						if($issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
						{
							$issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
							$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
						}
					}
					unset($issue_result);
					$book_without_order_cond="";
					if($db_type==0)
					{
						$book_without_order_cond=" and (a.booking_without_order=0 or a.booking_without_order='')";
					}
					else
					{
						$book_without_order_cond=" and (a.booking_without_order=0 or a.booking_without_order is null)";
					}

					$issue_rtn_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.issue_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, c.po_breakdown_id as po_breakdown_id
					from inv_receive_master a, inv_transaction b , order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(1) and a.entry_form in(9) and c.entry_form in(9) and nvl(c.is_sales,0)!=1 and b.transaction_type in(4) and c.trans_type in(4) and a.receive_basis in(1) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and a.booking_id in($hide_booking_id) $date_condtion $source_cond $trans_year_cond
					union all
					select a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.issue_id, a.receive_basis, a.entry_form, a.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, c.po_breakdown_id as po_breakdown_id
					from inv_receive_master a, inv_transaction b , order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(1) and a.entry_form in(9) and c.entry_form in(9) and nvl(c.is_sales,0)!=1 and b.transaction_type in(4) and c.trans_type in(4) and a.receive_basis in(3) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name $iss_rtn_req_no_cond $date_condtion $source_cond $trans_year_cond";
					 //echo $issue_rtn_sql;die;

					$issue_rtn_result=sql_select($issue_rtn_sql);
					foreach($issue_rtn_result as $row)
					{
						if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
						{
							$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];

							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];

							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
						}
						if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
						{
							$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
							$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
						}
					}

					unset($issue_rtn_result);


					if(!empty($book_cond))
					{
						if($all_plan_id=="")
						{
							$all_prog_id="'". 0 . "'";
						}
						else
						{
							$all_plan_id_arr=explode(",",$all_plan_id);
							foreach($all_plan_id_arr as $plan_id)
							{
								$all_prog_id.="'". $plan_id . "',";
							}
							$all_prog_id=chop($all_prog_id,",");
						}

						$receive_sql="SELECT a.id as mst_id, a.recv_number, c.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, a.booking_without_order, b.trans_id, b.prod_id, 0 as pi_wo_batch_no, b.uom as cons_uom, 0 as brand_id, b.grey_receive_qnty as cons_quantity, 0 as return_qnty, b.reject_fabric_receive as cons_reject_qnty, 1 as transaction_type, c.po_breakdown_id as po_breakdown_id
						from inv_receive_master a, pro_grey_prod_entry_dtls b,  pro_roll_details  c
						where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and nvl(c.is_sales,0)!=1 and a.receive_basis in(10) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and (c.booking_no in($book_cond) or c.booking_no in($all_prog_id) )  $source_cond ";
						//$date_condtion , $trans_year_cond

						//echo $receive_sql;die;

						$receive_result=sql_select($receive_sql);
						//[$row[csf('location_id')]]
						foreach($receive_result as $row)
						{
							if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
							{
								$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];

								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];

								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
							}
							if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
							{
								$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
							}
						}

						unset($receive_result);
					}
				}
				else
				{
					if($txt_job_no !="")
					{
						$order_sql="select a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_break_down b, wo_po_details_master a where b.job_no_mst=a.job_no and a.id in($txt_job_id) $year_cond";
						//echo $order_sql;
						$datapoArray=sql_select($order_sql);
						$all_po_check = array(); $allPoId = array();
						foreach($datapoArray as $row)
						{
							$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
							$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
							$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
							$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
							$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
							$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];

							if($all_po_check[$row[csf('id')]]=="")
							{
								$all_po_check[$row[csf('id')]]=$row[csf('id')];
								$allPoId[$row[csf("id")]] = $row[csf("id")];
							}
						}

						unset($datapoArray);

						if(!empty($allPoId))
						{
							fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 99, 6,$allPoId, $empty_arr);
							fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 99, 7,$allPoId, $empty_arr);
							//die;
							$roll_rcv_sql="SELECT a.id as mst_id,  c.booking_no, a.knitting_source, a.knitting_company, b.prod_id, 0 as pi_wo_batch_no, c.po_breakdown_id as po_breakdown_id
							from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, gbl_temp_engine d
							where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and nvl(c.is_sales,0)!=1 and a.receive_basis in(10) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.ref_from in (6) and d.entry_form=99";

							//echo $roll_rcv_sql;die;
							$roll_rcv_sql_rslt=sql_select($roll_rcv_sql);

							$all_roll_data_arr = array();
							foreach($roll_rcv_sql_rslt as $row)
							{
								$all_roll_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
							}

							$sql_requ= "SELECT a.dtls_id, a.booking_no, b.requisition_no, b.prod_id from ppl_planning_entry_plan_dtls a left join ppl_yarn_requisition_entry b on b.knit_id = a.dtls_id, gbl_temp_engine c where a.status_active=1 and a.is_deleted=0 and a.po_id=c.ref_val and c.user_id=$user_id and c.ref_from in (7) and c.entry_form=99";

							//echo $sql_requ;die;
							$result_requ=sql_select($sql_requ);
							$requisition_booking_arr=array();
							$grey_booking_no=array();
							foreach($result_requ as $row)
							{
								$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
								$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];

							}

						}

					}
					else
					{
						//grouping
						$order_sql="select a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.po_quantity, b.grouping as int_file_no from wo_po_break_down b, wo_po_details_master a where b.job_no_mst=a.job_no and b.grouping ='$txt_internal_ref' $year_cond";
						//echo $order_sql;
						$datapoArray=sql_select($order_sql);
						foreach($datapoArray as $row)
						{
							$po_arr[$row[csf('id')]]['name']=$row[csf('po_number')];
							$po_arr[$row[csf('id')]]['qnty']=$row[csf('po_quantity')];
							$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
							$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
							$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
							$po_arr[$row[csf('id')]]['int_file_no']=$row[csf('int_file_no')];

							if($all_po_check[$row[csf('id')]]=="")
							{
								$all_po_check[$row[csf('id')]]=$row[csf('id')];
								$allPoId[$row[csf("id")]] = $row[csf("id")];
							}
						}
						unset($datapoArray);
					}

					if(!empty($allPoId))
					{
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 99, 2,$allPoId, $empty_arr);
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 99, 3,$allPoId, $empty_arr);
						fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 99, 4,$allPoId, $empty_arr);
						//die;
						$sql_requ= "SELECT a.dtls_id, a.booking_no, b.requisition_no, b.prod_id from ppl_planning_entry_plan_dtls a left join ppl_yarn_requisition_entry  b on b.knit_id = a.dtls_id, gbl_temp_engine c where a.status_active=1 and a.is_deleted=0 and a.po_id=c.ref_val and c.user_id=$user_id and c.ref_from in (2) and c.entry_form=99";
						//echo $sql_requ;die;
						$result_requ=sql_select($sql_requ);
						$requisition_booking_arr=array();
						$grey_booking_no=array();
						foreach($result_requ as $row)
						{
							$requisition_booking_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
							$grey_booking_no[$row[csf('dtls_id')]]['booking_no']=$row[csf('booking_no')];
						}
						unset($result_requ);

						$issue_sql="SELECT a.id as mst_id, a.entry_form, a.issue_number, a.challan_no, a.issue_basis, a.issue_purpose, a.booking_no, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.location_id, b.id as trans_id, b.prod_id, b.transaction_date, b.requisition_no, b.brand_id, b.cons_uom, b.cons_quantity as cons_quantity,b.remarks, c.po_breakdown_id as po_breakdown_id
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c, gbl_temp_engine d
						where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=3 and c.entry_form=3 and nvl(c.is_sales,0)!=1 and a.issue_purpose in(1,4) and b.transaction_type=2 and c.trans_type=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.knit_dye_company>0 and a.issue_basis in(1,3) and a.company_id=$cbo_company_name $date_condtion $source_cond2 and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.ref_from in (3) and d.entry_form=99";//b.cons_quantity,
						//echo $issue_sql;die;
						$issue_result=sql_select($issue_sql);


						foreach($issue_result as $row)
						{
							if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
							{
								$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("transaction_date")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("issue_number")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("issue_basis")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_purpose"]=$row[csf("issue_purpose")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["requisition_no"].=$row[csf("requisition_no")].",";
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
							}
							if($issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
							{
								$issue_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
								$all_data_arr[$row[csf('knit_dye_source')]][$row[csf('knit_dye_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
							}
						}
						unset($issue_result);

						$book_without_order_cond="";
						if($db_type==0)
						{
							$book_without_order_cond=" and (b.booking_without_order=0 or b.booking_without_order='')";
						}
						else
						{
							$book_without_order_cond=" and (b.booking_without_order=0 or b.booking_without_order is null)";
						}

						$receive_sql="SELECT a.id as mst_id, a.recv_number, a.booking_no, a.booking_id, a.receive_date, a.item_category, a.challan_no, a.yarn_issue_challan_no, a.knitting_source, a.knitting_company, a.knitting_location_id as location_id, a.receive_basis, a.entry_form, b.booking_without_order, b.id as trans_id, b.prod_id, b.pi_wo_batch_no, b.cons_uom, b.brand_id, b.cons_quantity, b.return_qnty, b.cons_reject_qnty, b.transaction_type,b.remarks, c.po_breakdown_id as po_breakdown_id, a.issue_id
						from inv_receive_master a, inv_transaction b , order_wise_pro_details c, gbl_temp_engine d
						where a.id=b.mst_id and b.id=c.trans_id and b.item_category in(1,13) and a.entry_form in(9,58) and c.entry_form in(9,58) and (c.is_sales!=1 or c.is_sales is null) and b.transaction_type in(1,4) and c.trans_type in(1,4) and a.receive_basis in(1,2,3,9,10,11) $book_without_order_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.company_id=$cbo_company_name $date_condtion $source_cond and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.ref_from in (4) and d.entry_form=99";
						//echo $receive_sql;die;//entry_form in(9,2,22,58)
						$receive_result=sql_select($receive_sql);

						foreach($receive_result as $row)
						{
							if($trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=="")
							{
								$trans_check[$row[csf('mst_id')]][$row[csf('trans_id')]]=$row[csf("trans_id")];
								$location_id="";
								if($row[csf('entry_form')]==9)
								{
									$location_id=$issue_wise_location[$row[csf('issue_id')]];
								}
								else
								{
									$location_id=$row[csf('location_id')];
								}

								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["issue_mst_id"]=$row[csf("mst_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_date"]=$row[csf("receive_date")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["entry_form"]=$row[csf("entry_form")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["trans_ref_no"]=$row[csf("recv_number")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["challan_no"]=$row[csf("challan_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["basis"]=$row[csf("receive_basis")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_id"]=$row[csf("booking_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_no"]=$row[csf("booking_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["booking_without_order"]=$row[csf("booking_without_order")];

								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["prod_id"]=$row[csf("prod_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["transaction_date"]=$row[csf("transaction_date")];

								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["brand_id"]=$row[csf("brand_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_uom"]=$row[csf("cons_uom")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_quantity"]+=$row[csf("cons_quantity")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["cons_reject_qnty"]+=$row[csf("cons_reject_qnty")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["remarks"]=$row[csf("remarks")];
							}
							if($rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=="" && $row[csf("po_breakdown_id")]>0)
							{
								$rcv_po_check[$row[csf("po_breakdown_id")]][$row[csf('mst_id')]][$row[csf('prod_id')]]=$row[csf("po_breakdown_id")];
								$all_data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('mst_id')]][$row[csf('prod_id')]]["po_breakdown_id"].=$row[csf("po_breakdown_id")].",";
							}
						}

						unset($receive_result);
					}
				}
			}
		}

		if($cbo_style_owner>0)
		{
			$style_owner_sql= "SELECT a.job_no, b.booking_no from wo_po_details_master a, wo_booking_mst b
			where a.job_no=b.job_no and b.company_id=$cbo_company_name and a.style_owner=$cbo_style_owner and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $style_owner_sql;
			$style_owner_result=sql_select($style_owner_sql);

			$booking_chk_arr = array();
			foreach($style_owner_result as $row)
			{
				if (!in_array($row[csf('booking_no')],$booking_chk_arr))
				{
					$booking_chk_arr[]=$row[csf('booking_no')];
				}
			}
			//echo "<pre>";print_r($booking_chk_arr);
		}


		$main_data_arr = array();
		foreach ($all_data_arr as $source_id=>$source_data )
		{
			foreach ($source_data as $party_id=>$party_data )
			{
				foreach($party_data as $mst_id=>$mst_data)
				{
					foreach($mst_data as $prod_id=>$prod_data)
					{
						$all_po_breakdown_id=explode(",",chop($prod_data["po_breakdown_id"],","));
						$buyer_id=$style_num=$order_num=$booking_req_no=$int_file_no="";$order_qnty=0;
						foreach($all_po_breakdown_id as $po_id)
						{
							$buyer_id=$po_arr[$po_id]['buyer_name'];
							if($style_check[$i][$po_arr[$po_id]['style']]=="")
							{
								$style_check[$i][$po_arr[$po_id]['style']]=$po_arr[$po_id]['style'];
								$style_num.=$po_arr[$po_id]['style'].",";
							}
							$order_num.=$po_arr[$po_id]['name'].",";
							$int_file_no.=$po_arr[$po_id]['int_file_no'].",";
							$order_qnty+=$po_arr[$po_id]['qnty'];
						}

						$issue_qty=$issue_rtn_qty=$issue_rtn_reject_qty=$grey_rcv_qty=0;$booking_no="";$grey_rcv_reject_qty=0;

						if($prod_data["entry_form"]==3)
						{
							//echo $mst_id."==>".$prod_id."==>".$prod_data["cons_quantity"]."<br>";
							$issue_qty=$prod_data["cons_quantity"];
							if($prod_data["basis"]==1)
							{
								$booking_req_no=$prod_data["booking_no"];
								$booking_no=$prod_data["booking_no"];
							}
							else
							{
								$booking_req_no=implode(",",array_unique(explode(",",chop($prod_data["requisition_no"],","))));
								$booking_req_arr=array_unique(explode(",",chop($prod_data["requisition_no"],",")));
								foreach ($booking_req_arr as $req_no) {
									$booking_no=$requisition_booking_arr[$req_no][$prod_id]['booking_no'];
								}
							}
						}
						else if($prod_data["entry_form"]==9)
						{
							$issue_rtn_qty=$prod_data["cons_quantity"];
							$issue_rtn_reject_qty=$prod_data["cons_reject_qnty"];

							if($prod_data["basis"]==3)
							{
								$booking_req_no=$prod_data["booking_no"];
								$booking_no=$requisition_booking_arr[$booking_req_no][$prod_id]['booking_no'];
							}
							else if($prod_data["basis"]==1)
							{
								$booking_no=$prod_data["booking_no"];
							}
						}
						else
						{
							if($prod_data["basis"]==10)
							{

								if($hide_booking_id !="")
								{

									$booking_no=$grey_booking_no[$prod_data["booking_no"]]['booking_no'];
								}
								else
								{
									$booking_no = $grey_booking_no[$all_roll_data_arr[$source_id][$party_id][$mst_id][$prod_id]["booking_no"]]['booking_no'];
								}
							}
							else
							{
								$booking_no=$prod_data["booking_no"];
							}
							//$booking_no=$prod_data["booking_no"];
							$grey_rcv_qty=$prod_data["cons_quantity"];
							$grey_rcv_reject_qty=$prod_data["cons_reject_qnty"];
						}


						// ==========================Start Main Data Area=================
						if($cbo_style_owner>0)
						{
							if (in_array($booking_no,$booking_chk_arr))
							{
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["trans_ref_no"]= $prod_data["trans_ref_no"];
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["trans_date"]= $prod_data["trans_date"];
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["challan_no"]= $prod_data["challan_no"];
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["booking_req_no"]= $booking_req_no;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["buyer_id"]= $buyer_id;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["style_num"]= $style_num;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["int_file_no"]= $int_file_no;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["order_num"]= $order_num;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["order_qnty"]= $order_qnty;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["brand_id"]= $prod_data["brand_id"];
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["cons_uom"]= $prod_data["cons_uom"];
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["issue_qty"]= $issue_qty;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["grey_rcv_qty"]= $grey_rcv_qty;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["grey_rcv_reject_qty"]= $grey_rcv_reject_qty;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["issue_rtn_qty"]= $issue_rtn_qty;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["issue_rtn_reject_qty"]= $issue_rtn_reject_qty;
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["remarks"]= $prod_data["remarks"];
								$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["basis"]= $prod_data["basis"];
							}
						}
						else
						{
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["trans_ref_no"]= $prod_data["trans_ref_no"];
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["trans_date"]= $prod_data["trans_date"];
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["challan_no"]= $prod_data["challan_no"];
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["booking_req_no"]= $booking_req_no;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["buyer_id"]= $buyer_id;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["style_num"]= $style_num;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["int_file_no"]= $int_file_no;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["order_num"]= $order_num;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["order_qnty"]= $order_qnty;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["brand_id"]= $prod_data["brand_id"];
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["cons_uom"]= $prod_data["cons_uom"];
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["issue_qty"]= $issue_qty;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["grey_rcv_qty"]= $grey_rcv_qty;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["grey_rcv_reject_qty"]= $grey_rcv_reject_qty;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["issue_rtn_qty"]= $issue_rtn_qty;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["issue_rtn_reject_qty"]= $issue_rtn_reject_qty;
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["remarks"]= $prod_data["remarks"];
							$main_data_arr[$source_id][$party_id][$booking_no][$mst_id][$prod_id]["basis"]= $prod_data["basis"];
						}
					}
				}
			}
		}
		//echo "<pre>";print_r($main_data_arr);

		$con = connect();
        execute_query("DELETE FROM gbl_temp_engine WHERE user_id = ".$user_id." and ref_from in (1,2,3,4,5,6,7) and entry_form=99");
        oci_commit($con);
        disconnect($con);


		$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, short_name from  lib_buyer", "id", "short_name");
		$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		$location_arr=return_library_array("select id, location_name from lib_location","id","location_name");
		?>
		<div>
			<table width="1650" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
				<tr>
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? //echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
				</tr>
			</table>
			<table width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="80">Date</th>
					<th width="130">Transaction Ref.</th>
					<th width="80">Challan No</th>
					<th width="100">Booking/Reqsn</th>
					<th width="70">Buyer</th>
					<th width="110">Style Ref.</th>
					<th width="80">Brand</th>
					<th width="150">Item Description</th>
					<th width="80">Lot</th>
					<th width="60">UOM</th>
					<th width="90">Yarn Issued</th>
					<th width="90">Fabric Received</th>
					<th width="90">Reject Fabric Received</th>
					<th width="90">Yarn Returned</th>
					<th width="90">Reject Yarn Returned</th>
					<th width="90">Balance</th>
					<th width="">Remarks</th>
				</thead>
			</table>
			<div style="width:1650px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1630" cellpadding="2" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
					<?
					$product_arr=array();
					$sql_prod="select id, product_name_details, lot from product_details_master where item_category_id in (1) ";//13
					$sql_prod_res=sql_select($sql_prod);
					foreach($sql_prod_res as $rowp)
					{
						$product_arr[$rowp[csf('id')]]['name']=$rowp[csf('product_name_details')];
						$product_arr[$rowp[csf('id')]]['lot']=$rowp[csf('lot')];
					}
					unset($sql_prod_res);

					$i=1;
					foreach ($main_data_arr as $source_id=>$source_data )
					{
						foreach ($source_data as $party_id=>$party_data )
						{
							?>
							<tr bgcolor="#EFEFEF"><td colspan="18"><b>Party Name:
							<?
							if($source_id==1)
							{
								echo $company_arr[$party_id];

							}
							else echo $supplier_arr[$party_id];
							?>
							</b></td></tr>
							<?
							foreach ($party_data as $booking_no => $booking_no_data)
							{

								?>
								<tr bgcolor="#EFEFEF"><td colspan="18" ><b>Booking No:
								<?
									echo $booking_no;
								?>
								</b></td></tr>
								<?
								foreach($booking_no_data as $mst_id=>$mst_data)
								{
									foreach($mst_data as $prod_id=>$prod_data)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$balance +=($prod_data["issue_qty"]-($prod_data["issue_rtn_qty"]+$prod_data["issue_rtn_reject_qty"]+$prod_data["grey_rcv_qty"]+$prod_data["grey_rcv_reject_qty"]));

										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30" align="center" title="<? echo "Prog Num=".$prod_data["booking_no"]; ?>"><? echo $i; ?></td>
											<td width="80" align="center"><? echo change_date_format($prod_data["trans_date"]); ?></td>
											<td width="130" style="word-break:break-all" title="<? echo $prod_data["basis"]; ?>"><? echo $prod_data["trans_ref_no"]; ?></td>
											<td width="80"><p>&nbsp;<? echo  $prod_data["challan_no"]; ?></p></td>
											<td width="100" align="center"><p>&nbsp;<? echo $prod_data["booking_req_no"]; ?></p></td>
											<td width="70"><p><? echo $buyer_arr[$prod_data["buyer_id"]]; ?>&nbsp;</p></td>
											<td width="110" style="word-break:break-all"><? echo chop($prod_data["style_num"],","); ?></td>
											<td width="80"><p>&nbsp;<? echo $brand_arr[$prod_data["brand_id"]]; ?></p></td>
											<td width="150" style="word-break:break-all"><? echo $product_arr[$prod_id]['name']; ?></td>
											<td width="80"><p><? echo $product_arr[$prod_id]['lot']; ?></p></td>
											<td width="60" align="center"><? echo $unit_of_measurement[$prod_data["cons_uom"]]; ?>&nbsp;</td>
											<td width="90" align="right"><? echo number_format($prod_data["issue_qty"],2); ?></td>
											<td width="90" align="right"><? echo number_format($prod_data["grey_rcv_qty"],2); ?></td>
											<td width="90" align="right"><? echo number_format($prod_data["grey_rcv_reject_qty"],2); ?></td>
											<td width="90" align="right"><? echo number_format($prod_data["issue_rtn_qty"],2); ?></td>
											<td width="90" align="right"><? echo number_format($prod_data["issue_rtn_reject_qty"],2); ?></td>
											<td width="90" align="right"><? echo number_format($balance,2); ?></td>
											<td style="word-break:break-all"><? echo $prod_data["remarks"]; ?></td>
										</tr>
										<?
										$i++;

										$booking_issue_qty+=$prod_data["issue_qty"];
										$booking_rec_qty+=$prod_data["grey_rcv_qty"];
										$booking_fab_rej_qty+=$prod_data["grey_rcv_reject_qty"];
										$booking_return_qty+=$prod_data["issue_rtn_qty"];
										$booking_yarn_rej_qty+=$prod_data["issue_rtn_reject_qty"];

										$party_issue_qty+=$prod_data["issue_qty"];
										$party_rec_qty+=$prod_data["grey_rcv_qty"];
										$party_fab_rej_qty+=$prod_data["grey_rcv_reject_qty"];
										$party_return_qty+=$prod_data["issue_rtn_qty"];
										$party_yarn_rej_qty+=$prod_data["issue_rtn_reject_qty"];

										$tot_issue_qty+=$prod_data["issue_qty"];
										$tot_rec_qty+=$prod_data["grey_rcv_qty"];
										$tot_fab_rej_qty+=$prod_data["grey_rcv_reject_qty"];
										$tot_return_qty+=$prod_data["issue_rtn_qty"];
										$tot_yarn_rej_qty+=$prod_data["issue_rtn_reject_qty"];

									}
								}
								?>
								<tr bgcolor="#CCCCCC" >
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td colspan="3" align="right"><strong>Booking Total:</strong></td>
									<td align="right"><? echo number_format($booking_issue_qty,2); ?></td>
									<td align="right"><? echo number_format($booking_rec_qty,2); ?></td>
									<td align="right"><? echo number_format($booking_fab_rej_qty,2); ?></td>
									<td align="right"><? echo number_format($booking_return_qty,2); ?></td>
									<td align="right"><? echo number_format($booking_yarn_rej_qty,2); ?></td>
									<td align="right"><? echo number_format($balance,2); ?></td>
									<td>&nbsp;</td>
								</tr>
								<?
								$booking_issue_qty=$booking_rec_qty=$booking_fab_rej_qty=$booking_return_qty=$booking_yarn_rej_qty= 0;
							}


							?>
							<tr bgcolor="#CCCCCC" >
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td colspan="3" align="right" ><strong>Party Total:</strong></td>
								<td align="right"><? echo number_format($party_issue_qty,2); ?></td>
								<td align="right"><? echo number_format($party_rec_qty,2); ?></td>
								<td align="right"><? echo number_format($party_fab_rej_qty,2); ?></td>
								<td align="right"><? echo number_format($party_return_qty,2); ?></td>
								<td align="right"><? echo number_format($party_yarn_rej_qty,2); ?></td>
								<td align="right"><? echo number_format($balance,2); ?></td>
								<td>&nbsp;</td>
							</tr>
							<?
							$tot_balance+=$balance;
							$party_issue_qty=$party_rec_qty=$party_fab_rej_qty=$party_return_qty=$party_yarn_rej_qty=0;$balance=0;
						}
					}
					?>
				</table>
			</div>
			<table width="1650" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
				<tr>
					<td width="30">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="150">&nbsp;</td>
					<td width="140"><b>Grand Total:</b></td>
					<td width="90" align="right"><? echo number_format($tot_issue_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_rec_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_fab_rej_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_return_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_yarn_rej_qty,2); ?></td>
					<td width="90" align="right"><? echo number_format($tot_balance,2); ?></td>
					<td width="">&nbsp;</td>
				</tr>
			</table>
		</div>
		<?
	}

	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
		//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

?>
