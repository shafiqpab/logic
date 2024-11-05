<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

$search_by_arr=array(1=>"Date Wise Report",2=>"Wait For Heat Setting",5=>"Wait For Singeing",3=>"Wait For Dyeing",4=>"Wait For Re-Dyeing");//--------------------------------------------------------------------------------------------------------------------


if($action=="load_drop_down_buyer")
{
	echo load_html_head_contents("Buyer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	$company=$data[0];
	$report_type=$data[1];
	//echo $report_type;
	if($report_type==1 || $report_type==3)
	{
		echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else if($report_type==2)
	{
		echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else if($report_type==0)
	{
		echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,2,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","");
	}
	exit();
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;

		function check_all_data()
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value, 'create_booking_no_search_list_view', 'search_div', 'recipe_dyes_chemical_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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
	$booking_type=$data[6];


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
	if($booking_type==1)
	{
		$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4,3) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id  order by a.job_no desc";
	}
	else
	{
		$sql= "select a.company_id as company_name, a.buyer_id as buyer_name, b.style_des as style_ref_no,a.booking_no,a.id as booking_id  from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_type=4 and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond2 $year_cond $month_cond group by a.booking_no,a.company_id, a.buyer_id, b.style_des,a.id  order by a.booking_no desc";
	}

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

				$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no')].'_'.$booking_type;
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

if ($action == "FSO_No_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var hide_fso_id='<? echo $hide_fso_id; ?>';
		var selected_id = new Array, selected_name = new Array();

		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_fso_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] )
				}
			}
		}

		function js_set_value( str)
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );


			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_fso_id').val( id );
			$('#hide_fso_no').val( name );
		}

	</script>

</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
				<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Buyer Name</th>
						<th>Year</th>
						<th>Within Group</th>
						<th>Search By</th>
						<th id="search_by_td_up">FSO No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
						<input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",1 );
								?>
							</td>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$cbo_company_id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
							<td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $selected, "",0,"" );?></td>
							<td>
								
                                <?
                                $search_by_arr = array(1 => "FSO No", 2 => "Booking No", 3 => "Style Ref.");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                                ?>
							</td>
							<td>
								<input type="text" style="width:100px" class="text_boxes" name="txt_search_no" id="txt_search_no" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'recipe_dyes_chemical_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year=$data[2];
	$within_group=$data[3];
	$search_by=trim($data[4]);
	$search_string=trim($data[5]);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$search_cond = "";

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_cond_with_1=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_cond_with_2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_cond_with_1 =  "";
				$buyer_cond_with_2 =  "";
			}
		}
		else
		{
			$buyer_cond_with_1 =  "";
			$buyer_cond_with_2 =  "";
		}
	}
	else
	{
		$buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
		$buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
	}

	if($search_by==1)
	{
			if($search_string != "") $search_cond .= " and a.job_no_prefix_num = '$search_string'" ;
	}
	else if($search_by==2)
	{
			if($search_string != "") $search_cond .= " and a.sales_booking_no  like '%$search_string%'" ;
	}
	else if($search_by==3)
	{
			if($search_string != "") $search_cond .= " and a.style_ref_no  like '%$search_string%'" ;
	}


	/*if($fso_no != "")
	{
		$search_cond .= " and a.job_no_prefix_num = '$fso_no'" ;
	}
	if($booking_no != "")
	{
		$search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
	}*/
	if($db_type==0)
	{
		if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
		$year_field_con=" YEAR(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$year_field_con=" to_char(a.insert_date,'YYYY') as year";
		$year_field=" to_char(a.insert_date,'YYYY')";
		if($year!=0) $search_cond .=" and $year_field=$year"; else $search_cond .="";
		
	}

	$sql_2 ="select a.id, a.job_no,$year_field_con, a.sales_booking_no,a.style_ref_no,a.within_group,a.company_id,a.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
	group by a.id, a.job_no, a.insert_date,a.sales_booking_no,a.style_ref_no,a.within_group,a.company_id,a.buyer_id
	order by id desc";

	$sql_1 = "select a.id, a.job_no,$year_field_con, a.sales_booking_no,a.style_ref_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
	group by a.id, a.job_no, a.sales_booking_no,a.insert_date,a.style_ref_no,a.within_group,a.company_id,c.buyer_id";

	if($within_group == 1)
	{
		$sql = $sql_1 ;
	}
	else if($within_group == 2)
	{
		$sql = $sql_2;
	}
	else
	{
		$sql = $sql_1." union all ". $sql_2 ;
	}
//echo $sql;
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
		<thead>
			<th width="20">SL</th>
			<th width="130">Company</th>
			<th width="120">Buyer</th>
            <th width="50">Year</th>
			<th width="120">FSO No</th>
			<th width="110">Booking No</th>
            <th width="">Style ref no</th>
		</thead>
	</table>
	<div style="width:700px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search" >
			<?php
			$i=1; $fso_row_id="";
			$nameArray=sql_select( $sql );
			foreach ($nameArray as $selectResult)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>

				<tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
					<td width="20" align="center"><?php echo "$i"; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
				</td>
				<td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
				<td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
				<td width="50"><p><?php echo $selectResult[csf('year')];?></p></td>
                <td width="120"><p><?php echo $selectResult[csf('job_no')];?></p></td>
				<td width="110"><?php echo $selectResult[csf('sales_booking_no')];?></td>
                <td width=""><p><?php echo $selectResult[csf('style_ref_no')];?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
</div>

<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
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

<?
exit();
}
if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{ 
			//alert(id);
			$('#hidden_batch_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body  align="center">
	<div align="center">
    <form name="searchbatchnofrm"  id="searchbatchnofrm">
        <fieldset style="width:850px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="850" border="1"  align="center" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="5">
                          <?
							 echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                          ?>
                        </th>
                    </tr>                	
                    <tr>
                        <th width="120px">Batch Type</th>
                        <th width="100px">Search By</th>
                         <th width="100px" id="search_by_td_up">Batch No</th>
                        <th width="220px">Batch Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td align="center">	
                        <?
                            echo create_drop_down( "cbo_batch_type", 150, $order_source,"",0, "--Select--", 1,0,0 );
                        ?>
                    </td>
                     <td align="center">				
						<?
                        $search_by_arr = array(1 => "Batch No", 2 => "Booking No");
						$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
                        echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                        ?>
							
                    </td> 
                      <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_no" id="txt_search_no" />	
                    </td> 
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
                    </td>
                   
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_batch_type').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_no').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $company_name;?>', 'create_batch_search_list_view', 'search_div', 'recipe_dyes_chemical_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <table width="100%" style="margin-top:5px;">
                <tr>
                    <td colspan="5">
                        <div style="width:100%; margin-left:3px;" id="search_div" align="center"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$start_date =$data[0];
	$end_date =$data[1];
	$company_id =$data[2];
	$batch_type =$data[3];
	$search_by =$data[4];
	$search_string =$data[5];
	
	$search_type =$data[6];
	$company_id =$data[7];
	//echo $search_type.'ddd';
	if ($company_id>0) $lc_comp_cond=" and d.company_id=$company_id"; else $lc_comp_cond="";
 	
	if($search_type==1)
	{
		if($search_by==1) 
		{
			if ($search_string!='') $batch_cond=" and d.batch_no='$search_string'"; else $batch_cond="";
		}
		else if($search_by==2) 
		{
		if ($search_string!='') $batch_cond=" and d.booking_no like '%$search_string'"; else $batch_cond="";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		//if ($batch_no!='') $batch_cond=" and d.batch_no like '%$search_string%'"; else $batch_cond="";
		//if ($booking_no!='') $batch_cond=" and d.booking_no like '%$search_string%'"; else $book_cond="";
		
		if($search_by==1) 
		{
			if ($search_string!='') $batch_cond=" and d.batch_no like '%$search_string%'"; else $batch_cond="";
		}
		else if($search_by==2) 
		{
		if ($search_string!='') $batch_cond=" and d.booking_no like '%$search_string%'"; else $batch_cond="";
		}
	}
	else if($search_type==2)
	{
		//if ($batch_no!='') $batch_cond=" and d.batch_no like '$search_string%'"; else $batch_cond="";
		//if ($booking_no!='') $batch_cond=" and d.booking_no like '$search_string%'"; else $book_cond="";
		if($search_by==1) 
		{
			if ($search_string!='') $batch_cond=" and d.batch_no like '$search_string%'"; else $batch_cond="";
		}
		else if($search_by==2) 
		{
		if ($search_string!='') $batch_cond=" and d.booking_no like '$search_string%'"; else $batch_cond="";
		}
	}
	else if($search_type==3)
	{
		//if ($batch_no!='') $batch_cond=" and d.batch_no like '%$search_string'"; else $batch_cond="";
		//if ($booking_no!='') $book_cond=" and d.booking_no like '%$search_string'"; else $book_cond="";
		if($search_by==1) 
		{
			if ($search_string!='') $batch_cond=" and d.batch_no like '%$search_string'"; else $batch_cond="";
		}
		else if($search_by==2) 
		{
		if ($search_string!='') $batch_cond=" and d.booking_no like '%$search_string'"; else $batch_cond="";
		}
	}	
	
	if($batch_type==0)
		$search_field_cond_batch="and d.entry_form in (0,36)";
	else if($batch_type==1)
		$search_field_cond_batch="and d.entry_form=0";
	else if($batch_type==2)
		$search_field_cond_batch="and d.entry_form=36";
		//if ($company_id!=0) $company_cond=" and d.company_id='$company_id'"; else $company_cond="";
	//echo $company_cond;die;
	$sql_sales_job=array();
	$sql_sales_job=sql_select("SELECT b.job_no as job_no_mst,b.booking_no,f.job_no as sales_order_no,f.within_group from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, FABRIC_SALES_ORDER_MST f,pro_batch_create_mst d where a.booking_no=b.booking_no and b.booking_no=f.SALES_BOOKING_NO and b.po_break_down_id=c.id and d.sales_order_id=f.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond_batch $book_cond $batch_cond group by b.job_no,b.booking_no,f.job_no,f.within_group");

	foreach ($sql_sales_job as $sales_job_row) {
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["within_group"] = $sales_job_row[csf('within_group')];
		
		
	}

	$sql_sales_job=sql_select("SELECT   sales_booking_no as booking_no , job_no as sales_order_no,within_group  from   FABRIC_SALES_ORDER_MST   where status_active=1 group by sales_booking_no  , job_no,within_group ");

	foreach ($sql_sales_job as $sales_job_row)
	{
		//$sales_job_arr[$sales_job_row[csf('booking_no')]]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
		$sales_job_arr_within_group_no[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr_within_group_no[$sales_job_row[csf('booking_no')]]["within_group"] = $sales_job_row[csf('within_group')];
		//$sales_job_arr[$sales_job_row[csf('booking_no')]]["within_group"] = $sales_job_row[csf('within_group')];
	}


	if($db_type==2)
		{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and d.batch_date between '".change_date_format($start_date, "mm-dd-yyyy", "-",1)."' and '".change_date_format($end_date, "mm-dd-yyyy", "-",1)."'"; else $batch_date_con ="";
		
		if($batch_type==0 || $batch_type==2)
			{
		
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
			else
			{
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,listagg(cast(a.po_number as varchar2(4000)),',') within group (order by a.po_number) as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
		}
		if($db_type==0)
		{	
		if ($start_date!="" &&  $end_date!="") $batch_date_con = " and d.batch_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $batch_date_con ="";
		if($batch_type==0 || $batch_type==2)
			{
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,group_concat( distinct a.order_no )  as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
			else
			{
			$sql_po=sql_select("select b.mst_id, a.job_no_mst,group_concat(distinct a.po_number)  as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
			}
		}
	
	$po_num=array();
	foreach($sql_po as $row_po_no)
	{
	$po_num[$row_po_no[csf('mst_id')]]['po_no']=$row_po_no[csf('po_no')];
	$po_num[$row_po_no[csf('mst_id')]]['job_no_mst']=$row_po_no[csf('job_no_mst')];
		
	} 	//and company_id=$company_id
	 $sql = "select d.id, d.batch_no, d.batch_date, d.batch_for,d.batch_weight, d.booking_no, d.total_trims_weight,d.extention_no, d.color_id, d.batch_against, d.re_dyeing_from,d.is_sales from pro_batch_create_mst d where d.batch_for in(0,1) and d.batch_against<>4 and d.status_active=1 and d.is_deleted=0 $search_field_cond_batch $batch_date_con $batch_cond $lc_comp_cond $book_cond $company_cond order by d.id desc"; 
	//echo $sql;//die;
	?>
    <div align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="1160" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="80">Batch No</th>
                <th width="80">Extention No</th>
                <th width="80">Batch Date</th>
                <th width="90">Batch Weight</th>
                <th width="90">Trims Weight</th>
                <th width="90">Batch Against</th>
                <th width="90">Batch For</th>
                <th width="115">Job No</th>
                <th width="115">Booking No</th>
                <th width="80">Color</th>
                <th>Po/FSO No</th>
            </thead>
        </table>
        <div style="width:1160px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1140" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				//$nameArray=sql_select( $sql );
				$nameArray=sql_select( $sql );
				
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$is_sales= $selectResult[csf('is_sales')];
					$within_group=$sales_job_arr_within_group_no[$selectResult[csf('booking_no')]]["within_group"];
					$po_no='';					
					if($selectResult[csf('re_dyeing_from')]==0 || 1==1){	
						if($is_sales == 1){
								if($within_group == 1){
									$po_no = $sales_job_arr[$selectResult[csf('booking_no')]]["sales_order_no"];
									$job_no= $sales_job_arr[$selectResult[csf('booking_no')]]["job_no_mst"];
								}else{
									$po_no = $sales_job_arr_within_group_no[$selectResult[csf('booking_no')]]["sales_order_no"];
									$job_no= "";								}
							}else{
								$po_no = implode(",", array_unique(explode(",", $po_num[$selectResult[csf('id')]]['po_no'])));
								$job_no= $po_num[$selectResult[csf('id')]]['job_no_mst'];
							}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $selectResult[csf('id')]. '_' . $selectResult[csf('batch_no')]; ?>')"> 
							<td width="40" align="center"><? echo $i; ?></td>	
							<td width="80"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                            <td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
							<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
							<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
                            <td width="90" align="right"><? echo $selectResult[csf('total_trims_weight')]; ?></td> 
                            <td width="90" align="center"><? echo $batch_against[$selectResult[csf('batch_against')]]; ?></td> 
                            <td width="90" align="center"><? echo $batch_for[$selectResult[csf('batch_for')]]; ?></td> 
                            <td width="115"><p><? echo $job_no; ?></p></td>
                            <td width="115"><p><? echo $selectResult[csf('booking_no')]; ?></p></td>
							<td width="80"><p><? echo $color_library[$selectResult[csf('color_id')]]; ?></p></td>
							<td><? echo $po_no; ?></td>	
						</tr>
						<?
						$i++;
					}
					else
					{
						//$sql_re= "select id, batch_no, batch_date, batch_weight, booking_no, MAX(extention_no) as extention_no, color_id, batch_against, re_dyeing_from from pro_batch_create_mst where  batch_for in(0,1) and entry_form in(0,36) and batch_against<>4 and status_active=1 and is_deleted=0 and id=".$selectResult[csf('re_dyeing_from')]." group by id, batch_no, batch_date, batch_weight, booking_no,color_id, batch_against,re_dyeing_from ";
						//$dataArray=sql_select( $sql_re );
						$dataArray=array();
						
						foreach($dataArray as $row)
						{
							if($row[csf('re_dyeing_from')]==0)
							{
								/*$sql_po="select a.po_number as po_no,a.job_no_mst from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and b.mst_id=".$row[csf('id')]." and b.status_active=1 and b.is_deleted=0 $select_group";
								$poArray=sql_select( $sql_po );
								foreach ($poArray as $row2)
								{
									if($po_no=='') $po_no=$row2[csf('po_no')]; else $po_no.=",".$row2[csf('po_no')];
								}*/
								
								$po_no=implode(",",array_unique(explode(",",$po_num[$selectResult[csf('id')]]['po_no'])));
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]. '_' . $selectResult[csf('batch_no')]; ?>)"> 
									<td width="40" align="center"><? echo $i; ?></td>	
									<td width="80"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
									<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
									<td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
									<td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td> 
                                    <td width="90" align="right"><? echo $selectResult[csf('total_trims_weight')]; ?></td>
                                     <td width="90" align="center"><? echo $batch_against[$selectResult[csf('batch_against')]]; ?></td> 
                            		<td width="90" align="center"><? echo $batch_for[$selectResult[csf('batch_for')]]; ?></td>  
                                    <td width="115"><p><? echo $po_num[$selectResult[csf('id')]]['job_no_mst']; ?></p></td>
                                     <td width="115"><p><? echo $selectResult[csf('booking_no')]; ?></p></td>
									<td width="80"><p><? echo $color_library[$selectResult[csf('color_id')]]; ?></p></td>
									<td><? echo $po_no; ?></td>	
								</tr>
								<?
								$i++;
							}
						}
					}
				}
			?>
            </table>
        </div>
	</div>           
<?

exit();
}


if($action=="recipe_batch_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$txt_batch_no = str_replace("'","",$txt_batch_no);
	$txt_batch_id = str_replace("'","",$txt_batch_id);
	$txt_fso_no = str_replace("'","",$txt_fso_no);
	$txt_fso_id = str_replace("'","",$txt_fso_id);
	$cbo_base_on = str_replace("'","",$cbo_base_on);
	$txt_lot_no = str_replace("'","",$txt_lot_no);
//b.item_lot
	$year = str_replace("'","",$cbo_year);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	

	
	if ($txt_batch_no=="") $batch_num_cond=""; else $batch_num_cond="  and b.batch_no='".str_replace("'","",$txt_batch_no)."'";
	if ($txt_lot_no=="") $lot_cond=""; else $lot_cond="  and b.item_lot='".$txt_lot_no."'";


	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to) 
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			if($cbo_base_on==1)
			{
				$dates_com="and b.batch_date BETWEEN '$date_from' AND '$date_to'";
			}
			else
			{
				$dates_com="and d.production_date BETWEEN '$date_from' AND '$date_to'";
			}
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			if($cbo_base_on==1)
			{
			$dates_com="and b.batch_date BETWEEN '$date_from' AND '$date_to'";
			}
			else{
				$dates_com="and d.production_date BETWEEN '$date_from' AND '$date_to'";
			}
		}
	}

	
	
		

		$sales_orders_cond="";
		if($txt_fso_no != ""){
			$sales_orders="";
			foreach (explode(",", $txt_fso_no) as $row)
			{
				$sales_orders.= ($sales_orders=="") ? "'".$row."'" : ",'".$row."'";
			}

			if($sales_orders)
			{
				$sales_orders_cond ="and a.job_no in ($sales_orders)";
			}
		}
		

		$all_req_ids='';$all_recipe_ids='';
		if($cbo_base_on==1)
			{
				   $sql_sales_order= "SELECT b.id as batch_id,b.batch_weight,b.sales_order_no,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,a.job_no,d.mst_id as req_id from  fabric_sales_order_mst a, pro_batch_create_mst b,pro_recipe_entry_mst c,dyes_chem_requ_recipe_att d where a.id=b.sales_order_id and c.batch_id=b.id and d.recipe_id=c.id and  b.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sales_orders_cond $dates_com $batch_num_cond  group by  b.id ,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id,b.sales_order_no,b.batch_weight,a.job_no,d.mst_id";
				//die;
				$result_data=sql_select($sql_sales_order);
				foreach ($result_data as $value)
				{
					$batch_id_arr[$value[csf("batch_id")]]['batch_no']=$value[csf("batch_no")];
					$batch_id_arr[$value[csf("batch_id")]]['batch_weight']=$value[csf("batch_weight")];
					$batch_id_arr[$value[csf("batch_id")]]['color_id']=$value[csf("color_id")];
					$batch_id_arr[$value[csf("batch_id")]]['color_range_id']=$value[csf("color_range_id")];
					$batch_id_arr[$value[csf("batch_id")]]['booking_no']=$value[csf("booking_no")];
					$batch_id_arr[$value[csf("batch_id")]]['sales_order_no']=$value[csf("sales_order_no")];
					if($all_req_ids=='') $all_req_ids=$value[csf("req_id")];else $all_req_ids.=",".$value[csf("req_id")];
					if($all_recipe_ids=='') $all_recipe_ids=$value[csf("recipe_id")];else $all_recipe_ids.=",".$value[csf("recipe_id")];
				}
			}
			else
			{
				 $sql_sales_order= "SELECT  b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.sales_order_no,b.color_id,b.color_range_id,c.id as recipe_id,a.job_no,e.mst_id as req_id  from  fabric_sales_order_mst a, pro_batch_create_mst b,pro_recipe_entry_mst c,pro_fab_subprocess d,dyes_chem_requ_recipe_att e where a.id=b.sales_order_id and c.batch_id=b.id and e.recipe_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sales_orders_cond $dates_com $batch_num_cond  group by  b.id ,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id,b.sales_order_no,b.batch_weight,a.job_no,e.mst_id";
				$result_data=sql_select($sql_sales_order);
				foreach ($result_data as $value)
				{
					$batch_id_arr[$value[csf("batch_id")]]['batch_no']=$value[csf("batch_no")];
					$batch_id_arr[$value[csf("batch_id")]]['batch_weight']=$value[csf("batch_weight")];
					$batch_id_arr[$value[csf("batch_id")]]['color_id']=$value[csf("color_id")];
					$batch_id_arr[$value[csf("batch_id")]]['color_range_id']=$value[csf("color_range_id")];
					$batch_id_arr[$value[csf("batch_id")]]['booking_no']=$value[csf("booking_no")];
					$batch_id_arr[$value[csf("batch_id")]]['sales_order_no']=$value[csf("sales_order_no")];
					if($all_req_ids=='') $all_req_ids=$value[csf("req_id")];else $all_req_ids.=",".$value[csf("req_id")];
					if($all_recipe_ids=='') $all_recipe_ids=$value[csf("recipe_id")];else $all_recipe_ids.=",".$value[csf("recipe_id")];
				}

			}
		//	echo $all_req_ids.'b,';
			//$recipe_Ids = implode(",", $recipe_id_arr);
			$ReqIds=chop($all_req_ids,','); //$po_cond_for_in=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
			$req_ids=count(array_unique(explode(",",$all_req_ids)));
				if($db_type==2 && $req_ids>1000)
				{
					$req_cond_for_in=" and (";
					$req_cond_for_in2=" and (";
					$reqIdsArr=array_chunk(explode(",",$ReqIds),999);
					foreach($reqIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						//$poIds_cond.=" po_break_down_id in($ids) or ";
						$req_cond_for_in.=" a.id in($ids) or"; 
						$req_cond_for_in2.=" b.requisition_no in($ids) or"; 
					}
					$req_cond_for_in=chop($req_cond_for_in,'or ');
					$req_cond_for_in.=")";
					$req_cond_for_in2=chop($req_cond_for_in2,'or ');
					$req_cond_for_in2.=")";
				}
				else
				{
					$req_cond_for_in=" and a.id in($ReqIds)";
					$req_cond_for_in2=" and b.requisition_no in($ReqIds)";
					
				}
				$RecipIds=chop($all_recipe_ids,','); //$po_cond_for_in=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
				$recipe_ids=count(array_unique(explode(",",$all_recipe_ids)));
				if($db_type==2 && $recipe_ids>1000)
				{
					$recp_cond_for_in=" and (";
					$recpIdsArr=array_chunk(explode(",",$RecipIds),999);
					foreach($recpIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$recp_cond_for_in.=" b.mst_id in($ids) or"; 
					}
					$recp_cond_for_in=chop($recp_cond_for_in,'or ');
					$recp_cond_for_in.=")";
					
				}
				else
				{
					$recp_cond_for_in=" and b.mst_id in($RecipIds)";
				
					
				}
			$item_group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7) and status_active=1 and is_deleted=0",'id','item_name');
			 $sql_issue_dtls = "select d.recipe_id,a.batch_no,
			 avg(b.cons_rate) as cons_rate, sum(b.cons_quantity) as cons_quantity, d.product_id as prod_id,
			 c.item_group_id, d.sub_process,d.requ_no,a.req_no
			  from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d
			  where a.id=b.mst_id and b.id =d.trans_id and d.product_id=c.id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=5 and b.item_category in (5,6,7) $req_cond_for_in2  group by d.recipe_id,a.batch_no,d.product_id, c.item_group_id,d.requ_no,a.req_no, d.sub_process order by d.sub_process "; 
			 // echo $sql_isue_dtls;die;
			  $sql_result_issue= sql_select($sql_issue_dtls);
			  $issue_data_arr=array(); $issue_req_chk_arr=array();
			  foreach($sql_result_issue as $row)
			  {
				  $issue_data_arr[$row[csf('req_no')]][$row[csf('sub_process')]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['issue_qty']+=$row[csf('cons_quantity')]; 
				  $issue_data_arr[$row[csf('req_no')]][$row[csf('sub_process')]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['cons_rate']=$row[csf('cons_rate')]; 
				  $issue_req_chk_arr[$row[csf('req_no')]]=$row[csf('req_no')]; 
			  }
			 // print_r( $issue_req_chk_arr);
		
		   $sql_dtls = "select a.id as req_id,a.requ_no,a.requisition_date,a.requisition_basis, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.item_lot,b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size,c.lot, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id  and b.item_category in (5,6,7) and a.entry_form=156 and b.req_qny_edit!=0 and c.item_category_id in (5,6,7) $lot_cond $req_cond_for_in  order by b.id, b.seq_no"; 
			$result_issue_req=sql_select($sql_dtls);
				foreach ($result_issue_req as $row)
				{
					//$batch_ids=explode(",",$row[csf("batch_id")]);
					$req_rec_data="";
				$req_rec_data=$row[csf("recipe_id")].'*'.$row[csf("requ_no")].'*'.$row[csf("batch_id")].'*'.$row[csf("req_id")];
				// $issue_qty_chk=$issue_data_arr[$row[csf('req_no')]][$row[csf('sub_process')]][$row[csf('prod_id')]][$row[csf('item_group_id')]][$row[csf('recipe_id')]]['issue_qty'];
				$issue_found=$issue_req_chk_arr[$row[csf("req_id")]];
				//echo $issue_req.', '.$row[csf("requ_no")];
				 if($issue_found)
				 {
				//echo 	$req_rec_data.'<br>';
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['required_qnty']=$row[csf("required_qnty")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['item_category']=$row[csf("item_category")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['item_group_id']=$row[csf("item_group_id")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['item_description']=$row[csf("item_description")].' '.$row[csf("item_size")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['unit_of_measure']=$row[csf("unit_of_measure")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['dose_base']=$row[csf("dose_base")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['ratio']=$row[csf("ratio")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['recipe_qnty']=$row[csf("recipe_qnty")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['adjust_percent']=$row[csf("adjust_percent")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['adjust_type']=$row[csf("adjust_type")];
				$issue_req_arr[$req_rec_data][$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]]['lot']=$row[csf("item_lot")];
				 }
				}
				//print_r($issue_req_dtls_arr); requisition_no
				
		
	 
	//  print_r($issue_data_arr);
	  $ratio_arr=array();
	$prevRatioData=sql_select( "select b.mst_id as recipe_id,b.total_liquor,b.prod_id,b.mst_id as recipe_id,b.liquor_ratio,  b.sub_process_id, b.ratio,c.item_group_id from pro_recipe_entry_dtls b,product_details_master c where b.prod_id=c.id and b.status_active=1 and b.is_deleted=0 and c.item_category_id in (5,6,7) $recp_cond_for_in");
	//echo "select b.mst_id as recipe_id,b.total_liquor,b.prod_id,b.mst_id as recipe_id,  b.sub_process_id, b.ratio,c.item_group_id from pro_recipe_entry_dtls b,product_details_master c where b.prod_id=c.id and b.status_active=1 and b.is_deleted=0 and c.item_category_id in (5,6,7) $recp_cond_for_in";
	$recep_check_arr=array();
	foreach($prevRatioData as $row)
	{
		
		if($recep_check_arr[$row[csf('mst_id')]][$row[csf('sub_process_id')]]=="")
		{
		$recipe_ratio_arr[$row[csf('sub_process_id')]][$row[csf('recipe_id')]]['total_liquor']=$row[csf('total_liquor')];
		$recipe_ratio_arr[$row[csf('sub_process_id')]][$row[csf('recipe_id')]]['liquor_ratio']=$row[csf('liquor_ratio')];
		}
	}
	
	  
	   $req_rowspan_arr=array();
	   foreach($issue_req_arr as $req_nos=>$req_data)
		{
			$req_row_span=0;$sub_row_span=0;
			 foreach($req_data as $sub_process_id=>$process_data)
			 {
				 	
				 foreach($process_data as $prod_id=>$prod_data)
				 {
					   foreach($prod_data as $item_id=>$row)
						{
							$req_row_span++;$sub_row_span++;
						}
						 $req_rowspan_arr[$req_nos]=$req_row_span;
						  
				 }
				 $sub_req_rowspan_arr[$req_nos][$sub_process_id]=$sub_row_span;
				
			 }
		}
	//print_r($sub_req_rowspan_arr);
	//echo $issue_data_arr[1177][1][8701][301][1959]['issue_qty'].'fff';;
	//echo $requ_no.'='.$sub_process_id.'='.$prod_id.'='.$item_id.'='.$recipe_id.'X'.$issue_qty.'<br>';
		ob_start();
	?>
   	 		<div align="left" style="margin-left:5px;">
           <table width="1260" cellpadding="0" cellspacing="0" border="0" >
           <tr>
           <td colspan="15" align="center">
           <b> <? echo $company_library[$company].'<br>';
						 echo $report_title.'<br>';
				 	if($txt_date_from!="") echo change_date_format($txt_date_from).' To '.change_date_format($txt_date_to,'yyyy-mm-dd');else echo " ";?></b> </td>
           </tr>
           </table>
           <?
            $i=1;$f=0;$total_issue_qty=$total_recipe_qnty=$total_amount_req_value=$total_ratio_qty=0;
			$k=1;  $sub_process_array=array();$req_chk_arr=array();
			foreach($issue_req_arr as $req_nos=>$req_data)
			{
		   ?>
   				 <div>
                 <table class="rpt_table" width="1260" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <tr>
                                <th class="" width="">&nbsp;</th>
                                <th class="word_wrap_break" width="30">SL</th>
                                <th class="word_wrap_break" width="100">Item Cat.</th>
                                <th class="word_wrap_break" width="60">Lot No</th>
                                <th class="word_wrap_break" width="100">Item Group</th>
                                <th class="word_wrap_break" width="120">Item Description</th>
                                <th class="word_wrap_break" width="50">UOM</th>
                                <th class="word_wrap_break" width="70">Dose Base</th>
                                <th class="word_wrap_break" width="70">Ratio</th>
                                <th class="word_wrap_break" width="90">Recipe Qty.</th>
                                <th class="word_wrap_break" width="60">Adj%</th>
                                <th class="word_wrap_break" width="70">Adj Type</th>
                                <th class="word_wrap_break" width="90">Iss. Qty.</th>
                                <th class="word_wrap_break" width="70">Unit Price</th>
                                <th class="word_wrap_break" width="90">Amount(BDT)</th>
                                
                            </tr>
                        </thead>
                    </table>
                   <div class="scroll_div_inner" style=" max-height:350px; width:1280px;" >
                       <table class="rpt_table" id="table_body" width="1260" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <tbody>
                                <?
                               
									$r=1;$sub_issue_qty=$sub_recipe_qty=$sub_ratio_qty=$sub_req_amt_qty=$sub_issue_qty=0;
									$batchNo="";$color_name_id="";;$color_range_id="";$sales_order_no="";$booking_nos="";$batch_weight=0;
									$req_nos_data=array_unique(explode("*",$req_nos));
									$recipe_id=$req_nos_data[0];
									$requ_no=$req_nos_data[1];
									$batch_id=$req_nos_data[2];
									$req_id=$req_nos_data[3];
									$color_name='';$color_range_name='';$sales_order_no='';
									$batch_ids = array_unique(explode(",",$batch_id));
									foreach($batch_ids as $bId)
									{
										$batchNo.=$batch_id_arr[$bId]['batch_no'].',';
										//echo $batchNo.'='.$bId.', ';
										$batch_weight+=$batch_id_arr[$bId]['batch_weight'].',';
										$color_range_id.=$color_range[$batch_id_arr[$bId]['color_range_id']].',';
										$color_name_id.=$color_library[$batch_id_arr[$bId]['color_id']].',';
										$sales_order_no.=$batch_id_arr[$bId]['sales_order_no'].',';
										$booking_nos.=$batch_id_arr[$bId]['booking_no'].',';
									}
									//echo $batch_id.'ddds';
									
												
									 foreach($req_data as $sub_process_id=>$process_data)
                               		 {
										?>
										
                                               
										<?
										$sub_process_id_chk=$sub_process_id.$req_nos;
										$total_liquor=$recipe_ratio_arr[$sub_process_id][$recipe_id]['total_liquor'];
										$liquor_ratio=$recipe_ratio_arr[$sub_process_id][$recipe_id]['liquor_ratio'];
												
										if (!in_array($sub_process_id_chk,$sub_process_array) )
											{
												?>  <tr bgcolor="#CCCCCC">
                                                <? //if($r==1) {?>
												<td colspan="15"  align="center" rowspan="<? //echo $sub_req_rowspan_arr[$req_nos][$sub_process_id]; ?>"><strong><? echo $dyeing_sub_process[$sub_process_id].',Liqur Ratio: &nbsp;'.$liquor_ratio.', Total Liquor: &nbsp; '.$total_liquor; ?></strong></td>
                                                <?
												 // }
												?>
												</tr> 
												
												<? 
												$sub_process_array[]=$sub_process_id_chk;
											}
										 foreach($process_data as $prod_id=>$prod_data)
                               			 {
											   foreach($prod_data as $item_id=>$row)
                               			 		{
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												
												//echo $sales_order_no.'d';
												$batchNos=rtrim($batchNo,',');
												$color_name_id=rtrim($color_name_id,',');
												$color_range_ids=rtrim($color_range_id,',');
												$booking_nos=rtrim($booking_nos,',');
												$sales_order_no=rtrim($sales_order_no,',');
												
												$batchNos=implode(",",array_filter(array_unique(explode(",",$batchNos))));
												$color_range_name=implode(",",array_filter(array_unique(explode(",",$color_range_ids))));
												$color_name=implode(",",array_filter(array_unique(explode(",",$color_name_id))));
												$booking_nos=implode(",",array_filter(array_unique(explode(",",$booking_nos))));
												$sales_order_no=implode(",",array_filter(array_unique(explode(",",$sales_order_no))));
												
												$issue_qty=$issue_data_arr[$req_id][$sub_process_id][$prod_id][$item_id][$recipe_id]['issue_qty'];
												$issue_cons_rate=$issue_data_arr[$req_id][$sub_process_id][$prod_id][$item_id][$recipe_id]['cons_rate'];
												$total_liquor=$recipe_ratio_arr[$sub_process_id][$item_id][$prod_id][$recipe_id]['total_liquor'];
												$liquor_ratio=$recipe_ratio_arr[$sub_process_id][$item_id][$prod_id][$recipe_id]['liquor_ratio'];
												//echo $req_id.'='.$sub_process_id.'='.$prod_id.'='.$item_id.'='.$recipe_id.'X'.$issue_qty.'<br>';
											
												?>
                                             <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                             <?
                                           if (!in_array($requ_no,$req_chk_arr) )
											{ 
												$f++;//batch_weight
												?>
                                             <td style="word-break:break-all" rowspan="<? //echo $req_rowspan_arr[$req_nos]; ?>" align="center" width="" title="<? //echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo "Recipe No:".$recipe_id.', Req. No:'.$requ_no.', Batch No:'.$batchNos.', Batch Wgt:'.$batch_weight.', FSO No:'.$sales_order_no.', Booking No:'.$booking_nos.', Batch Color:'.$color_name.', Color Range:'.$color_range_name; ?></p></td>
                                              
                                             <?
											 	 $req_chk_arr[]=$requ_no;
                                              }
											  else
											  { ?>
												    <td style="" rowspan="<? //echo $req_rowspan_arr[$req_nos]; ?>" align="center" width="" title="<? //echo change_date_format($batch[csf('batch_date')]); ?>">
                                                    </td>
                                              
											  <?
                                              }
                                             ?>
                                               <td class="" width="30"><? echo $r; ?></td>
                                                <td class="" align="center" width="100"><p><? echo $item_category[$row[("item_category")]]; ?></p></td>
                                                <td class="" width="60" title=""><p><? echo $row[("lot")]; ?></p></td>
                                                <td class="" width="100" ><p><? echo  $item_group_arr[$item_id]; ?></p></td>
                                                <td class="" width="120"><p><? echo $row[('item_description')]; ?></p></td>
                                                <td width="50"><p class=""><? echo $unit_of_measurement[$row[('unit_of_measure')]]; ?></p></td>
                                                <td width="70">
                                                    <p class="">
                                                    <?
                                                        echo  $dose_base[$row[("dose_base")]];
                                                    ?>
                                                    </p>
                                                </td>
                                                <td class="" width="70" align="center"><p><? echo number_format($row[("ratio")],6,'.','');//chop($job_from_fso_arr[$batch[csf('fso_no')]]["job_no"],","); ?></p></td>
                                                <td width="90" align="right"><p class=""><? echo number_format($row[("recipe_qnty")],6,'.','');; ?></p></td>
                                                <td class="" align="right" width="60"><p><?  echo $row[("adjust_percent")];; ?></p></td>
                                                <td class="" align="right" width="70"><p><? echo $increase_decrease[$row[("adjust_type")]]; ?></p></td>
                                                <td class="" align="right" width="90"><p><? echo  number_format($issue_qty,6,'.',''); ?></p></td>
                                                <td class="" width="70" align="right"><p><? echo number_format($issue_cons_rate,6,'.','');; ?></p></td>
                                                <td class="" width="90" align="right"><p><?  $amount_req_value=$issue_qty*$issue_cons_rate; echo number_format($amount_req_value,6,'.',''); ?></p></td>
                                            </tr>
                                            <?
                                            $i++;$r++;
											$sub_issue_qty+=$issue_qty;
											$sub_ratio_qty+=$row[("ratio")];
                                            $sub_recipe_qty+=$row[("recipe_qnty")];
											$sub_req_amt_qty+=$amount_req_value;
                                         
                                           $total_recipe_qnty += $row[("recipe_qnty")];
											$total_issue_qty += $issue_qty;
										 	$total_ratio_qty += $row[("ratio")];
                                            $total_amount_req_value += $amount_req_value;
												
											}
										 }
										 ?>
                                            <tr style="background:#999999">
                                            <td colspan="8" align="right"><strong>Total:</strong></td>
                                            <td align="right"><?php  echo number_format($sub_ratio_qty,6,'.',''); ?></td>
                                            <td align="right"><?php  echo number_format($sub_recipe_qty,6,'.',''); ?></td>
                                            <td>&nbsp;</td>
                                            
                                            <td align="right"><?php  //echo number_format($req_qny_issue_sum,6,'.',''); ?></td>
                                            <td align="right"><?php  echo number_format($sub_issue_qty,6,'.',''); ?></td>
                                            <td align="right"></td>
                                            <td align="right"><?php  echo number_format($sub_req_amt_qty,6,'.',''); ?></td>
                                            </tr> 
                                         <?
										 $sub_recipe_qty=0;
										$sub_issue_qty=0;
										$sub_ratio_qty=0;
										$sub_req_amt_qty=0;
									 }
									 ?>
                                     
                                      <tr style="background:#999999">
                                        <td width="">&nbsp;</td>
                                        <td width="30">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        <td width="60">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        <td width="120">&nbsp;</td>
                                        <td width="50">&nbsp;</td>
                                        <td width="70"><strong>Grand Total</strong></td>
                                        <td width="70" align="right">  <? echo number_format($total_ratio_qty,6);?></td>
                                       
                                        <td width="90" align="right"><? echo number_format($total_recipe_qnty,6);?></td>
                                        <td width="60"  align="right"><? //echo number_format($total_recipe_qnty,6);?></td>
                                        <td width="70"></td>
                                        <td width="90" align="right"><? echo number_format($total_issue_qty,6);?></td>
                                        <td width="70">&nbsp;</td>
                                        <td width="90" align="right"><? echo number_format($total_amount_req_value,6);?></td>
                                       
                                    </tr>
                                    <tr style="background:#999999">
                                    <td colspan="13" align="right"><strong> Cost Per Kg :</strong></td>
                                    <td colspan="2" align="right" title="Total Amount/Batch Weight"><?php echo number_format($total_amount_req_value/($batch_weight),6,'.','');
                                    $total_amount_req_value=0;$total_issue_qty=0;$total_recipe_qnty=0;$total_ratio_qty=0;
                                     ?></td>
                                	</tr>  
            					 </tbody>
                                
                           
                        </table>
                         
                        <br>
                             <?
                                } //Req NO End
                                ?>
                  	 </div>	
                     </div>
                    </div>
 <?

		$html = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
			@unlink($filename);
		}
		$name=time();
		$filename=$user_name."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$html**$filename**$report_type";
		exit();
}

?>