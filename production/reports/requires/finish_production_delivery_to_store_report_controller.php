<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finishing production Report

Functionality	:
JS Functions	:
Created by		:	Md. Didarul Alam
Creation date 	: 	14-08-2018
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//====================Location ACTION========

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "load_drop_down( 'requires/finish_production_delivery_to_store_report_controller',this.value+'_'+$('#cbo_company_id').val(), 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor_id", 100, "select id, floor_name from lib_prod_floor where location_id=$data[0] and company_id=$data[1] and status_active=1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", 0, "","" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	exit();
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
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

		function js_set_value2( str )
		{
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
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );


			$('#hide_booking_id').val( id );
			$('#hide_booking_no').val( name );
		}

		function show_list_details()
		{
			var booking_type = '<? echo $booking_type?>';
			if($('#txt_search_common').val() =="" || (booking_type==2 && $('#cbo_search_by').val()==2))
			{
				if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
				{
					return;
				}
			}

			show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_booking_type; ?>', 'create_booking_no_search_list_view', 'search_div', 'finish_production_delivery_to_store_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');
		}
	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:760px;">
				<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th width="110">Buyer</th>
						<th width="100">Search By</th>
						<th id="search_by_td_up" width="120">Please Enter Booking No</th>
						<th width="140">Booking Date</th>
						<th colspan="2"><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
						<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />

					</thead>
					<tbody>
						<tr>
							<td align="center" width="110">
								<?
								echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>

							<td align="center">
								<?
								$search_by_arr=array(1=>"Booking No",2=>"Job No");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td" >
								<input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td width="140">
								<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px;" placeholder="From Date" />To
								<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px;" placeholder="To Date" />
							</td>

						</td>
						<td align="center" width="100">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_details ();" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" ><? echo load_month_buttons(1); ?></td>
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

	$company_id 		=	$data[0];
	$year_id 			=	$data[4];
	$date_from 			= 	trim($data[5]);
	$date_to 			= 	trim($data[6]);
	$cbo_booking_type 	= 	trim($data[7]);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 and id = $company_id",'id','company_name');

	if($data[1]==0)
	{
		$buyer_id_cond= "";
	}else {
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}

	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and c.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
			$date_cond2="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}else{
			$date_cond="and c.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
			$date_cond2="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";


	$job_no_cond = "";

	if($search_by==2)
	{
		$job_no_cond =" and a.job_no_prefix_num like ".$search_string;
	}
	else
	{
		$booking_no_cond = " and b.booking_no like '".$search_string."'";
		$booking_no_cond2 = " and a.booking_no like '".$search_string."'";
	}

	if($db_type==0)
	{
		$year_field_by="and YEAR(c.insert_date)";
		$year_field_by2="and YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field_by=" and to_char(c.insert_date,'YYYY')";
		$year_field_by2=" and to_char(a.insert_date,'YYYY')";
	}
	else {
		$year_field_by="";
		$year_field_by2="";
	}

	if($year_id!=0)
	{
		$year_cond=" $year_field_by=$year_id";
		$year_cond2=" $year_field_by2=$year_id";
	}
	else
	{
		$year_cond="";
	}

	if($cbo_booking_type !=5)
	{
		//order

		if($cbo_booking_type ==1){
			//main
			$booking_type_cond = " and c.booking_type=1 and c.is_short=2 and c.entry_form !=108";
		}
		else if($cbo_booking_type ==2){
			//partial
			$booking_type_cond = " and c.booking_type=1 and c.is_short=2 and c.entry_form=108";
		}
		else if($cbo_booking_type ==3){
			//short
			$booking_type_cond = " and c.booking_type=1 and c.is_short=1";
		}
		else if($cbo_booking_type ==4){
			//sample
			$booking_type_cond = " and c.booking_type=4";
		}

		$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, b.booking_no,c.booking_no_prefix_num,c.id as booking_id,0 as order_type   from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $job_no_cond $buyer_id_cond $year_cond $booking_no_cond $date_cond $booking_type_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num ";
	}
	else
	{
		//non order
		$sql= "select null as job_no, null as job_no_prefix_num, a.company_id as company_name, a.buyer_id as buyer_name, a.booking_no, a.booking_no_prefix_num, a.id as booking_id,1 as order_type
		from wo_non_ord_samp_booking_mst a where a.item_category in (2,3) and a.status_active =1 and a.is_deleted =0 and a.company_id=$company_id $buyer_id_cond2 $booking_no_cond2 $year_cond2 $date_cond2 ";
	}

	/*$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, b.booking_no,c.booking_no_prefix_num,c.id as booking_id,0 as order_type   from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $job_no_cond $buyer_id_cond $year_cond $booking_no_cond $date_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num

	union all

	select null as job_no, null as job_no_prefix_num, a.company_id as company_name, a.buyer_id as buyer_name, a.booking_no, a.booking_no_prefix_num, a.id as booking_id,1 as order_type
	from wo_non_ord_samp_booking_mst a where a.item_category in (2,3) and a.status_active =1 and a.is_deleted =0 and a.company_id=$company_id $buyer_id_cond2 $booking_no_cond2 $year_cond2 $date_cond2";*/

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

							$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no')];
                            //echo $data;
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
								<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
								<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
								<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
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

if($action=="batchnumbershow")
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1,'','','');
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

	$color_library = return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	if($db_type==0) $year_field_grpby="GROUP BY batch_no";
	else if($db_type==2) $year_field_grpby=" GROUP BY batch_no,id,batch_no,batch_for,booking_no,color_id,batch_weight order by batch_no desc";

	$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where working_company_id=$company_name and is_deleted = 0 $year_field_grpby ";

	$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch weight ", "100,100,100,100,70","520","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_weight", "finish_production_delivery_to_store_report_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if($action=="sales_order_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data)
		{
			document.getElementById('hidden_booking_data').value=booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>
						<td align="center">
							<?
							$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year; ?>, 'create_sales_order_no_search_list_view', 'search_div', 'finish_production_delivery_to_store_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_sales_order_no_search_list_view")
{
	$data=explode('_',$data);

	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	$cbo_year = $data[4];

	$company_arr=return_library_array( "select id,company_short_name from lib_company where id=$company_id",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and a.job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and a.style_ref_no like '".$search_string."%'";
	}

	if ($db_type == 0)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and YEAR(a.insert_date)=$cbo_year";
		}
	}
	else if ($db_type == 2)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]];
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('id')]."**".$row[csf('job_no_prefix_num')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$floor_id=str_replace("'","",$cbo_floor_id);
	$txt_mc_no=str_replace("'","",$txt_mc_no);
	$source_id =str_replace("'","",$cbo_source);
	$buyer_name=str_replace("'","",$cbo_buyer_name);

	$dynamic_search=str_replace("'","",$txt_dynamic_search);
	$dynamic_id = str_replace("'","",$hide_dynamic_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$rpt_type=str_replace("'","",$rpt_type);
	$search_by = str_replace("'","",$cbo_search_by);
	$year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	if($db_type==0)
	{
		if($date_from!=="" and $date_to!=="")
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
			$date_cond=" c.delevery_date between '$date_from' and '$date_to'";
		}else{
			if($year_selection>0)
			{
				$production_year_condition=" and YEAR(a.insert_date)=$year_selection";
			}
		}
	}
	else if($db_type==2)
	{
		if($date_from!=="" and $date_to!=="")
		{
			$date_from=change_date_format($date_from,'','',1);
			$date_to=change_date_format($date_to,'','',1);
			$date_cond=" and c.delevery_date between '$date_from' and '$date_to'";
		}else {
			if($year_selection>0)
			{
				$production_year_condition =" and to_char(a.insert_date,'YYYY')=$year_selection";
			}
		}
	}

	if($location_id==0)
	{
		$location_cond="";
	}else {
		$location_cond="and a.location_id=$location_id";
	}

	if($floor_id==0)
	{
		$floor_cond="";
	}else {
		$floor_cond="and b.floor=$floor_id";
	}

	if($buyer_name==0)
	{
		$buyer_cond="";
	}else {
		$buyer_cond="and b.buyer_id='$buyer_name'";
	}

	if($source_id==0)
	{
		$sourcCond="";
	}else {
		$sourcCond="and a.knitting_source = $source_id";
	}

	if($txt_mc_no!="")
	{
		$machinsql=sql_select("select id from lib_machine_name where status_active=1 and is_deleted=0 and machine_no like '%$txt_mc_no%' and company_id in($company_id)");
		foreach($machinsql as $row)
		{
			$machine_no_id .= $row[csf('id')].",";
		}

		$machine_no_id = implode(",", array_filter(array_unique(explode(",",chop($machine_no_id,",")))));
		if($machine_no_id!="")
		{
			$machinCond ="and b.machine_no_id in($machine_no_id)";
		}else{
			$machinCond ="and b.machine_no_id in(0)";
		}
	}

	if($search_by==1 && $dynamic_search!="") // Batch no
	{
		if($dynamic_id!="")
		{
			$searchByCondition = "and b.batch_id=$dynamic_id";
		} else {

			/*$sqlbatch = sql_select("SELECT id as batch_id FROM pro_batch_create_mst WHERE status_active=1 AND is_deleted=0 AND batch_no='$dynamic_search'");

			foreach($sqlbatch as $row)
			{
				$batch_id .= $row[csf('batch_id')].",";
			}
			$batch_ids = implode(",", array_filter(array_unique(explode(",",chop($batch_id,",")))));
			if($batch_id!="")
			{
				$searchByCondition = "and b.batch_id in($batch_ids)";
			}else {
				$searchByCondition = "and b.batch_id in(0)";
			}*/

			$searchByCondition = " and e.batch_no='$dynamic_search'";
		}
	}
	else if ($search_by==2 && $dynamic_search!="") // FSO
	{
		if($dynamic_id!="")
		{
			$searchByCondition = " and b.order_id='$dynamic_id' and b.is_sales=1";
		} else {
			$sqlFso = sql_select("select a.id as order_id  from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $production_year_condition and a.job_no_prefix_num='$dynamic_search'");

			foreach($sqlFso as $row)
			{
				$order_id .= $row[csf('order_id')].",";
			}

			$order_id = implode(",", array_filter(array_unique(explode(",",chop($order_id,",")))));
			if($order_id!="")
			{
				$searchByCondition = " and b.order_id in('$order_id') and b.is_sales=1";
			}else {
				$searchByCondition = " and b.order_id in('0')";
			}
		}
	}
	else if ($search_by==3 && $dynamic_search!="") // Booking no
	{
		$bookingNoArr = explode("*", $dynamic_search);

		$bookingNo = "'".implode("','", $bookingNoArr)."'";

		/*$sql_booking = sql_select("SELECT a.id from pro_batch_create_mst a where a.status_active=1 and a.is_deleted=0 and a.booking_no in($bookingNo)");

		foreach($sql_booking as $row)
		{
			$all_batch_id .= $row[csf('id')].",";
		}

		$all_batch_id = implode(",", array_filter(array_unique(explode(",",chop($all_batch_id,",")))));

		if($all_batch_id!="")
		{
			$searchByCondition = "and b.batch_id in($all_batch_id)";
		}else {
			$searchByCondition = "and b.batch_id in('0')";
		}*/

		$searchByCondition = " and f.booking_no in($bookingNo)";
	}


	if($search_by==3)
	{
		if($cbo_booking_type ==1)
		{
			//main
			$booking_type_cond = " and f.booking_type=1 and f.is_short=2 and f.entry_form !=108";
		}
		else if($cbo_booking_type ==2)
		{
			//partial
			$booking_type_cond = " and f.booking_type=1 and f.is_short=2 and f.entry_form=108";
		}
		else if($cbo_booking_type ==3)
		{
			//main
			$booking_type_cond = " and f.booking_type=1 and f.is_short=1";
		}
		else if($cbo_booking_type ==4)
		{
			//sample with order
			$booking_type_cond = " and f.booking_type=4 and f.booking_no not like '%-SMN-%'";
		}
		else if($cbo_booking_type ==5)
		{
			//sample without order
			$booking_type_without_cond = " and f.booking_type=4 and f.booking_no like '%-SMN-%'";
		}
	}


	$source_arr=array(1=>'Inhouse',3=>'Outbound');

	if($db_type==0)
	{
		$recv_number_str="group_concat(a.recv_number)";
		$delv_date_str="group_concat(c.delevery_date)";


	}
	else if($db_type==2)
	{
		$recv_number_str="listagg(a.recv_number,',') within group (order by a.recv_number)";
		$delv_date_str="listagg(c.delevery_date,',') within group (order by c.delevery_date)";
	}

	/*$sql_dtls="SELECT  listagg(cast ( a.recv_number as varchar(4000)),',') within group (order by a.recv_number) as recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id,a.fabric_nature,b.id,b.prod_id,
	b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty as qc_pass_qty, b.reject_qty, b.no_of_roll, b.order_id, b.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no,
	b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, b.is_sales, b.floor,
	b.original_gsm, b.original_width, listagg(cast (c.delevery_date as varchar(4000)),',') within group (order by c.delevery_date) as delevery_date,sum(d.current_delivery) as current_delivery,sum(d.roll) as roll,b.remarks
	 from inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_grey_prod_delivery_mst c,pro_grey_prod_delivery_dtls d where a.knitting_company=$company_id and a.id=b.mst_id and a.id=d.grey_sys_id and b.id=d.sys_dtls_id and c.id=d.mst_id and a.entry_form in(7,66) and d.entry_form=54 and a.item_category=2 and a.status_active=1 $date_cond $production_year_condition $location_cond $searchByCondition $sourcCond $machinCond $floor_cond $buyer_cond  and a.is_deleted=0 and b.uom !=0 and d.is_deleted=0 and d.status_active=1  group by a.receive_date,a.knitting_source,a.knitting_company,a.location_id,a.fabric_nature,b.id,b.prod_id,
	b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.order_id, b.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no,
	b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, b.is_sales, b.floor,
	b.original_gsm, b.original_width,b.remarks
	order by b.uom,b.prod_id, b.batch_id ";*/

	$sql_dtls_order=$sql_dtls_nonOrder=$concate="";

	if($booking_type_without_cond =="")
	{
		$sql_dtls_order="SELECT  listagg(cast ( a.recv_number as varchar(4000)),',') within group (order by a.recv_number) as recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id,a.fabric_nature,b.id,b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty as qc_pass_qty, b.reject_qty, b.no_of_roll, b.order_id, b.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, b.is_sales, b.floor, b.original_gsm, b.original_width, listagg(cast (c.delevery_date as varchar(4000)),',') within group (order by c.delevery_date) as delevery_date, sum(d.current_delivery) as current_delivery, sum(d.roll) as roll,b.remarks, c.sys_number, b.grey_used_qty,c.remarks as delivery_remarks
 		from inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_grey_prod_delivery_mst c,pro_grey_prod_delivery_dtls d, pro_batch_create_mst e, wo_booking_mst f where a.knitting_company=$company_id and a.id=b.mst_id and a.id=d.grey_sys_id and b.id=d.sys_dtls_id and c.id=d.mst_id and a.entry_form in(7,66) and d.entry_form=54 and a.item_category=2 and a.status_active=1 $date_cond $production_year_condition $location_cond $searchByCondition $sourcCond $machinCond $floor_cond $buyer_cond $booking_type_cond and a.is_deleted=0 and b.uom !=0 and d.is_deleted=0 and d.status_active=1 and b.batch_id=e.id and e.booking_no=f.booking_no group by a.receive_date, a.knitting_source, a.knitting_company, a.location_id, a.fabric_nature, b.id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.order_id, b.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, b.is_sales, b.floor, b.original_gsm, b.original_width,b.remarks, c.sys_number, b.grey_used_qty,c.remarks";
	}

	if($booking_type_cond =="")
	{
		$sql_dtls_nonOrder ="SELECT  listagg(cast ( a.recv_number as varchar(4000)),',') within group (order by a.recv_number) as recv_number, a.receive_date,a.knitting_source,a.knitting_company, a.location_id, a.fabric_nature, b.id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty as qc_pass_qty, b.reject_qty, b.no_of_roll, b.order_id, b.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, b.is_sales, b.floor, b.original_gsm, b.original_width, listagg(cast (c.delevery_date as varchar(4000)),',') within group (order by c.delevery_date) as delevery_date, sum(d.current_delivery) as current_delivery, sum(d.roll) as roll, b.remarks, c.sys_number, b.grey_used_qty,c.remarks as delivery_remarks
 		from inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_grey_prod_delivery_mst c,pro_grey_prod_delivery_dtls d, pro_batch_create_mst e, wo_non_ord_samp_booking_mst f where a.knitting_company=$company_id and a.id=b.mst_id and a.id=d.grey_sys_id and b.id=d.sys_dtls_id and c.id=d.mst_id and a.entry_form in(7,66) and d.entry_form=54 and a.item_category=2 and a.status_active=1 $date_cond $production_year_condition $location_cond $searchByCondition $sourcCond $machinCond $floor_cond $buyer_cond $booking_type_without_cond and a.is_deleted=0 and b.uom !=0 and d.is_deleted=0 and d.status_active=1 and b.batch_id=e.id and e.booking_no=f.booking_no group by a.receive_date, a.knitting_source, a.knitting_company, a.location_id, a.fabric_nature, b.id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.order_id, b.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, b.is_sales, b.floor, b.original_gsm, b.original_width,b.remarks, c.sys_number, b.grey_used_qty,c.remarks
		order by uom,prod_id, batch_id ";
	}

	//echo $sql_dtls_order;
 	$concate = " union all ";

 	if($sql_dtls_order !="" && $sql_dtls_nonOrder =="")
 	{
 		$sql_dtls = $sql_dtls_order;
 	}
 	else if($sql_dtls_order =="" && $sql_dtls_nonOrder !="")
 	{
 		$sql_dtls = $sql_dtls_nonOrder;
 	}
 	else if($sql_dtls_order !="" && $sql_dtls_nonOrder !="")
 	{
 		$sql_dtls = $sql_dtls_order.$concate.$sql_dtls_nonOrder;
 	}

 	//echo $sql_dtls;

	$sql_dtls_result=sql_select($sql_dtls);
	$data=array();$production_dtls_id="";
	foreach($sql_dtls_result as $row)
	{
		$knitting_company_id .= $row[csf('knitting_company')].",";
		$location_id .= $row[csf('location_id')].",";
		$color_id .= $row[csf('color_id')].",";
		$batch_id .= $row[csf('batch_id')].",";
		$machine_no_id .= $row[csf('machine_no_id')].",";
		$buyer_id .= $row[csf('buyer_id')].",";
		$floor .= $row[csf('floor')].",";
		$fabric_description_id .= $row[csf('fabric_description_id')].",";
		if($row[csf('is_sales')]==1)
		{
			$salesOrder_id .= $row[csf('order_id')].",";
		}
		else
		{
			$po_id .= $row[csf('order_id')].",";
		}

		$uom = $row[csf('uom')];
		$prodId = $row[csf('prod_id')];
		$batchId = $row[csf('batch_id')];
		$deleveryDate = $row[csf('delevery_date')];
		//$receiveNo = $row[csf('recv_number')];
		//$receiveDate = $row[csf('receive_date')];

		$data[$uom][$prodId][$batchId][$deleveryDate]['recv_number'] 		= $row[csf('recv_number')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['qc_pass_qty'] 		+= $row[csf('qc_pass_qty')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['reject_qty'] 		+= $row[csf('reject_qty')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['knitting_source'] 	= $row[csf('knitting_source')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['knitting_company'] 	= $row[csf('knitting_company')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['floor'] 				= $row[csf('floor')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['machine_no_id'] 		= $row[csf('machine_no_id')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['shift_name'] 		= $row[csf('shift_name')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['receive_date'] 		= $row[csf('receive_date')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['buyer_id'] 			= $row[csf('buyer_id')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['batch_id'] 			= $row[csf('batch_id')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['fabric_shade'] 		= $row[csf('fabric_shade')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['dia_width_type'] 	= $row[csf('dia_width_type')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['color_id'] 			= $row[csf('color_id')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['width'] 				= $row[csf('width')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['gsm'] 				= $row[csf('gsm')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['shelf_no'] 			= $row[csf('shelf_no')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['order_id'] 			= $row[csf('order_id')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['fab_descr_id']		= $row[csf('fabric_description_id')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['delivery_qty'] 		+= $row[csf('current_delivery')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['remarks'] 			= $row[csf('remarks')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['roll'] 				= $row[csf('roll')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['challan_no'] 		= $row[csf('sys_number')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['grey_used_qty'] 		= $row[csf('grey_used_qty')];
		$data[$uom][$prodId][$batchId][$deleveryDate]['delivery_remarks'] 	= $row[csf('delivery_remarks')];
		$production_dtls_id.=$row[csf('id')].",";

	}
	/*$production_dtls_id=chop($production_dtls_id,",");
	$sql_rev_qnty = sql_select("select a.recv_number, a.receive_date, sum(b.receive_qnty) as receive_qnty,b.uom,b.batch_id,b.prod_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in(7,66) and a.status_active=1 and a.is_deleted=0 $production_year_condition $location_cond $searchByCondition $sourcCond $machinCond $floor_cond $buyer_cond and b.id in($production_dtls_id) group by a.recv_number, a.receive_date,b.uom,b.batch_id,b.prod_id  order by a.recv_number,b.uom,b.prod_id,b.batch_id");

	foreach ($sql_rev_qnty as $row) {
		$receive_qnty[$row[csf('recv_number')]][$row[csf('uom')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['receive_qnty']=$row[csf('receive_qnty')];
	}*/

	$knitting_company_id = implode(",", array_filter(array_unique(explode(",",chop($knitting_company_id,",")))));
	$location_id = implode(",", array_filter(array_unique(explode(",",chop($location_id,",")))));
	$color_id = implode(",", array_filter(array_unique(explode(",",chop($color_id,",")))));
	$machine_no_id = implode(",", array_filter(array_unique(explode(",",chop($machine_no_id,",")))));
	$buyer_id = implode(",", array_filter(array_unique(explode(",",chop($buyer_id,",")))));
	$floor = implode(",", array_filter(array_unique(explode(",",chop($floor,",")))));
	$batch_id = implode(",", array_filter(array_unique(explode(",",chop($batch_id,",")))));
	$fabric_description_id = implode(",", array_filter(array_unique(explode(",",chop($fabric_description_id,",")))));

	$salesOrder_id = implode(",", array_filter(array_unique(explode(",",chop($salesOrder_id,",")))));
	$po_id = implode(",", array_filter(array_unique(explode(",",chop($po_id,",")))));



	if($knitting_company_id!="")
	{
		$kinttin_companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0 and id in($knitting_company_id)" ,"id","company_short_name");

		$kinttin_supplierArr = return_library_array("select id,short_name from lib_supplier where status_active=1 and is_deleted=0 and id in($knitting_company_id)" ,"id","short_name");
	}

	if($location_id!="")
	{
		$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0 and id in($location_id) and company_id in($knitting_company_id)",'id','location_name');
	}

	if($floor!="")
	{
		$floor_arr=return_library_array("select floor_room_rack_id as id,floor_room_rack_name as name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 and floor_room_rack_id in($floor) and company_id in($knitting_company_id)","id","name");
	}

	if($buyer_id!="")
	{
		$buyer_Arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 and id in($buyer_id)","id","buyer_name");
	}

	if($color_id!="")
	{
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 and id in($color_id)", "id", "color_name");
	}

	if($machine_no_id!="")
	{
		if($db_type==0)
		{
			$machinArr=return_library_array( "select id, concat(machine_no,'-',brand) as machine_name from lib_machine_name where status_active=1 and is_deleted=0 and id in($machine_no_id) and location_id in($location_id) and company_id in($knitting_company_id)",'id','machine_name');
		}
		else
		{
			$machinArr=return_library_array( "select id, (machine_no || '-' || brand) as machine_name from lib_machine_name where status_active=1 and is_deleted=0 and id in($machine_no_id) and location_id in($location_id) and company_id in($knitting_company_id)",'id','machine_name');
		}
	}

	$composition_arr = array();
	$constructtion_arr = array(); // yearn data puller
	if($fabric_description_id != "")
	{
		$fabric_desc_arr = explode(",", $fabric_description_id);
	    $all_fabric_desc_cond=""; $fabDesCond="";
	    if($db_type==2 && count($fabric_desc_arr)>999)
	    {
	    	$all_fabric_desc_arr_chunk=array_chunk($fabric_desc_arr,999) ;
	    	foreach($all_fabric_desc_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$fabDesCond.="  a.id in($chunk_arr_value) or ";
	    	}
	    	$all_fabric_desc_cond.=" and (".chop($fabDesCond,'or ').")";
	    }
	    else
	    {
	    	$all_fabric_desc_cond=" and a.id in($fabric_description_id)";
	    }

		$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $all_fabric_desc_cond";
		$data_array = sql_select($sql_deter);
		foreach ($data_array as $row) {
			$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
			$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}
	}

	if($po_id!="")
	{
		$po_data_arr=array(); // wo po data puller
		$sql_po=sql_select("select b.id, a.style_ref_no,b.b.po_number from wo_po_details_master a, wo_po_break_down b where b.job_no_mst=a.job_no and  a.status_active=1 and a.is_deleted=0 and b.id in($po_id)");
		foreach($sql_po as $row)
		{
			$po_data_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}


	if($batch_id!="")
	{
		$batch_id_arr = explode(",", $batch_id);
        $all_batch_id_cond=""; $batchCond="";
        if($db_type==2 && count($batch_id_arr)>999)
        {
        	$all_batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
        	foreach($all_batch_id_arr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$batchCond.="  a.id in($chunk_arr_value) or ";
        	}
        	$all_batch_id_cond.=" and (".chop($batchCond,'or ').")";
        }
        else
        {
        	$all_batch_id_cond=" and a.id in($batch_id)";
        }

		$batch_no_arr=array(); // batch data puller
		$booking_no_chk=array();
		$bookingNoArr=array();
		$sql_batch=sql_select("SELECT a.id,sum(b.batch_qnty) as batch_qnty, a.batch_weight,a.total_trims_weight,a.sales_order_no,a.is_sales,a.batch_no,a.booking_no,a.extention_no, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 $all_batch_id_cond group by a.id,a.batch_weight,a.total_trims_weight,a.sales_order_no,a.is_sales,a.batch_no,a.booking_no,a.extention_no, b.item_description" );


		foreach($sql_batch as $row)
		{
			$batch_no_arr[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batch_no_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
			$batch_no_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_no_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_no_arr[$row[csf('id')]]['sales_order_no']=$row[csf('sales_order_no')];
			$batch_no_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$batch_no_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_no_arr[$row[csf('id')]]['item_description']=$row[csf('item_description')];
			$booking_no_arr[] = "'".$row[csf('booking_no')]."'";

			$prod_id_arr[] = $row[csf("prod_id")];

			if($booking_no_chk[$row[csf('booking_no')]] == "")
			{
				$booking_no_chk[$row[csf('booking_no')]] = $row[csf('booking_no')];
				array_push($bookingNoArr,$row[csf('booking_no')]);
			}

		}

		$sql_booking="SELECT a.booking_no, b.fabric_color_id, b.construction, b.fin_fab_qnty, b.grey_fab_qnty
		from wo_booking_mst a, wo_booking_dtls b
		where a.booking_no=b.booking_no  and b.booking_type=1 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($bookingNoArr,1,'a.booking_no')."";
		//echo $sql_booking;
		$bookingArray=sql_select($sql_booking);
		$fab_booking_qty_arr=$grey_fab_booking_qnty_arr=array();
		foreach ($bookingArray as $value)
		{
			$fab_booking_qty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]][$value[csf('construction')]]['fin_fab_qnty']+=$value[csf('fin_fab_qnty')];
			$grey_fab_booking_qnty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]][$value[csf('construction')]]['grey_fab_qnty']+=$value[csf('grey_fab_qnty')];
		}

		//var_dump($fab_booking_qty_arr);
	}

	$prod_array=array();
	if(!empty($prod_id_arr)){
		$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where id in(".implode(",",$prod_id_arr).") and item_category_id in(2,13)");
		foreach($prodData as $row)
		{
			$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		}
	}


	if($salesOrder_id!="")
	{
		$get_data_from_sales_order = sql_select("select a.id,a.style_ref_no,b.determination_id,b.width_dia_type,b.color_id,b.gsm_weight,b.dia from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id in($salesOrder_id) group by a.id,a.style_ref_no,b.determination_id,b.width_dia_type,b.color_id,b.gsm_weight,b.dia");
		foreach ($get_data_from_sales_order as $sale_row)
		{
			$po_data_arr[$sale_row[csf('id')]]['style_ref_no']=$sale_row[csf('style_ref_no')];
			$po_data_arr[$sale_row[csf('id')]]['buyer']=$sale_row[csf('po_buyer')];
		}
	}

	ob_start();
	?>
	<div>
		<fieldset style="width:2580px;">
			<table width="1760px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$cbo_company];?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
				</tr>
			</table>
			<table width="2577" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Product ID</th>
					<th width="100">Challan No</th>
					<th width="80">Finishing Date</th>
					<th width="80">Delivery Date</th>
					<th width="80">Execution Days</th>
					<th width="80">Source</th>
					<th width="80">Finishing Company</th>
					<th width="80">Floor</th>
					<th width="80">M/C Name</th>
					<th width="80">Buyer Name</th>
					<th width="100">Style Ref.</th>
					<th width="80">Booking No</th>
					<th width="80">FSO No</th>
					<th width="80">Batch/Lot No</th>
					<th width="100">Extension No</th>
					<th width="80">Fabric Type</th>
					<th width="100">Fab. Composition</th>
					<th width="100">F. Dia</th>
					<th width="80">Dia Type</th>
					<th width="80">F.GSM</th>
					<th width="80">Fabric Color</th>
					<th width="80">Grey used</th>
					<th width="80">QC Pass Qty</th>
					<th width="80">Delivery Qty</th>
					<th width="80">Balance</th>
					<th width="100">Process Loss%</th>
					<th width="80">No of Roll</th>
					<th width="100">Production Remarks</th>
					<th width="">Delevery to Store Remarks</th>

				</thead>
			</table>
			<div style="width:2580px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table width="2560" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="tbl_dyeing">
					<?
					$i=1;
					$total_qc_qty = 0;
					foreach($data as $uomId=>$productArr)
					{
						?>
						<tr style="background-color:#ccc;">
							<td colspan="30"> <? echo $unit_of_measurement[$uomId];?> </td>
						</tr>
						<?
						foreach($productArr as $producId=>$daliveryDateArr)
						{
							foreach($daliveryDateArr as $batchID=>$batch_data)
							{
								foreach($batch_data as $deliveryDate=>$row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if($row['knitting_source']==1)
									{
										$kinttin_company = $kinttin_companyArr[$row['knitting_company']];
									}else{
										$kinttin_company = $kinttin_supplierArr[$row['knitting_company']];
									}
									$stock = $row['qc_pass_qty']-$row['delivery_qty'];

									// $desc = explode(",", $batch_no_arr[$row['batch_id']]['item_description']);
									// $booking_no = $batch_no_arr[$row['batch_id']]['booking_no'];
									// $fab_book_qty = $fab_booking_qty_arr[$booking_no][$row['color_id']][$desc[0]]['fin_fab_qnty'];
									// $grey_fab_book_qnty = $grey_fab_booking_qnty_arr[$booking_no][$row['color_id']][$desc[0]]['grey_fab_qnty'];
									// if($fab_book_qty>0)
									// {
									// 	$kd_process_loss=(($grey_fab_book_qnty-$fab_book_qty)/$fab_book_qty)*100;
									// } else $kd_process_loss=0;

									$process_loss=($row['grey_used_qty']-$row['delivery_qty'])/$row['grey_used_qty']*100;

									if($cbo_value_with==0){

									?>

										<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
											<td width="40"><? echo $i;?> </td>
											<td width="100"><p><? echo $producId;?></p> </td>
											<td width="100"><p><? echo $row['challan_no'];?></p> </td>
											<td width="80"><p><? echo change_date_format($row['receive_date']);?></p></td>
											<td width="80"><p><?
											$delDate =explode(",",$deliveryDate);
											$delvDate="";$executionDay="";
											foreach ($delDate as $val ) {
												$delvDate.=change_date_format($val).",";
												$executionDay+=datediff("d",$row['receive_date'],$val);
											}
											$delvDate=chop($delvDate,",");
											 echo $delvDate; //echo change_date_format($deliveryDate);?></p></td>
											<td width="80"><p><?  if($row['receive_date']!="") echo $executionDay; //datediff("d",$row['receive_date'],$deliveryDate);?></p></td>
											<td width="80"><p><? echo $source_arr[$row['knitting_source']];?></p></td>
											<td width="80" align="center"><p><? echo $kinttin_company;?></p></td>
											<td width="80"><p><? echo $floor_arr[$row['floor']];?></p></td>
											<td width="80"><p><? echo $machinArr[$row['machine_no_id']]; ?></p></td>
											<td width="80" align="center"><p><? echo $buyer_Arr[$row['buyer_id']];?></p></td>
											<td width="100" align="center"><p><? echo $po_data_arr[$row['order_id']]['style_ref_no'];?></p></td>
											<td width="80"><p><? echo $batch_no_arr[$row['batch_id']]['booking_no'];?></p></td>
											<td width="80"><p><? echo $batch_no_arr[$row['batch_id']]['sales_order_no']; ?></p></td>
											<td width="80" title="<? echo $row['batch_id']; ?>"><p><? echo $batch_no_arr[$row['batch_id']]['batch_no'];?></p>  </td>
											<td width="100" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['extention_no'];?></p>  </td>
											<td width="80"> <p><? echo $constructtion_arr[$row['fab_descr_id']] ; ?></p></td>
											<td width="100"><p><? echo $composition_arr[$row['fab_descr_id']];?></p></td>
											<td width="100" align="right"><p><?php echo $row['width'];?></p>  </td>
											<td width="80" align="right"><p><? echo $fabric_typee[$row['dia_width_type']];?></p></td>
											<td width="80" align="right"><p><?php echo $row['gsm'];?></p></td>
											<td width="80"><p><?php echo $color_library[$row['color_id']];?></p></td>
											<td width="80" align="right"><p><?php echo $row['grey_used_qty'];?></p></td>
											<td width="80" align="right" title="<? echo number_format($row['qc_pass_qty'],2); ?>">
												<p>
													<?
													//if ($recvNo!=$row['recv_number']) {
														echo number_format($row['qc_pass_qty'],2);
													//}

														//echo number_format($receive_qnty[$row['recv_number']][$uomId][$producId][$batchID]['receive_qnty'],2);
													?>
												</p>
											</td>
											<td width="80" align="right"><p><?php echo number_format($row['delivery_qty'],2);?></p></td>
											<td width="80" align="right"><p><?php echo number_format( ($row['qc_pass_qty']-$row['delivery_qty']) ,2 );?></p></td>
											<td width="100" align="center"><p>
											<?php echo number_format($process_loss,3); ?>%</p></td>
											<td width="80" align="center"><p><?php echo $row['roll']; ?></p></td>
											<td width="100" align="left"><p><?php echo $row['remarks']; ?></p></td>
											<td width="" align="left"><p><?php echo $row['delivery_remarks']; ?></p></td>
										</tr>
										<?
										$total_qc_qty+=$row['qc_pass_qty'];
										//$total_qc_qty_temporary=
										$total_delv_qty+=$row['delivery_qty'];
										$total_balance_qty+=($row['qc_pass_qty']-$row['delivery_qty']);

										//if ($recvNo!=$row['recv_number']) {
											$total_unit_wise_qc_qty[$uomId] += $row['qc_pass_qty'];
										//}
										$total_unit_wise_delv_qty[$uomId] += $row['delivery_qty'];
										$total_unit_wise_balance_qty[$uomId] += ($row['qc_pass_qty']-$row['delivery_qty']);
										$total_unit_wise_roll_no[$uomId] += $row['roll'];
										$total_unit_wise_grey_used_qty[$uomId] += $row['grey_used_qty'];
										$recvNo=$row['recv_number'];


										$i++;
									}
									else if($cbo_value_with==1 && $stock>0){
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
											<td width="40"><? echo $i;?> </td>
											<td width="100"><p><? echo $producId;?></p> </td>
											<td width="100"><p><? echo $row['challan_no'];?></p> </td>
											<td width="80"><p><? echo change_date_format($row['receive_date']);?></p></td>
											<td width="80"><p><?
											$delDate =explode(",",$deliveryDate);
											$delvDate="";$executionDay="";
											foreach ($delDate as $val ) {
												$delvDate.=change_date_format($val).",";
												$executionDay+=datediff("d",$row['receive_date'],$val);
											}
											$delvDate=chop($delvDate,",");
											 echo $delvDate; //echo change_date_format($deliveryDate);?></p></td>
											<td width="80"><p><?  if($row['receive_date']!="") echo $executionDay; //datediff("d",$row['receive_date'],$deliveryDate);?></p></td>
											<td width="80"><p><? echo $source_arr[$row['knitting_source']];?></p></td>
											<td width="80" align="center"><p><? echo $kinttin_company;?></p></td>
											<td width="80"><p><? echo $floor_arr[$row['floor']];?></p></td>
											<td width="80"><p><? echo $machinArr[$row['machine_no_id']]; ?></p></td>
											<td width="80" align="center"><p><? echo $buyer_Arr[$row['buyer_id']];?></p></td>
											<td width="100" align="center"><p><? echo $po_data_arr[$row['order_id']]['style_ref_no'];?></p></td>
											<td width="80"><p><? echo $batch_no_arr[$row['batch_id']]['booking_no'];?></p></td>
											<td width="80"><p><? echo $batch_no_arr[$row['batch_id']]['sales_order_no']; ?></p></td>
											<td width="80" title="<? echo $row['batch_id']; ?>"><p><? echo $batch_no_arr[$row['batch_id']]['batch_no'];?></p>  </td>
											<td width="100" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['extention_no'];?></p>  </td>
											<td width="80"> <p><? echo $constructtion_arr[$row['fab_descr_id']] ; ?></p></td>
											<td width="100"><p><? echo $composition_arr[$row['fab_descr_id']];?></p></td>
											<td width="100" align="right"><p><?php echo $row['width'];?></p>  </td>
											<td width="80" align="right"><p><? echo $fabric_typee[$row['dia_width_type']];?></p></td>
											<td width="80" align="right"><p><?php echo $row['gsm'];?></p></td>
											<td width="80"><p><?php echo $color_library[$row['color_id']];?></p></td>
											<td width="80" align="right"><p><?php echo $row['grey_used_qty'];?></p></td>
											<td width="80" align="right" title="<? echo number_format($row['qc_pass_qty'],2); ?>">
												<p>
													<?
													//if ($recvNo!=$row['recv_number']) {
														echo number_format($row['qc_pass_qty'],2);
													//}

														//echo number_format($receive_qnty[$row['recv_number']][$uomId][$producId][$batchID]['receive_qnty'],2);
													?>
												</p>
											</td>
											<td width="80" align="right"><p><?php echo number_format($row['delivery_qty'],2);?></p></td>
											<td width="80" align="right"><p><?php echo number_format( ($row['qc_pass_qty']-$row['delivery_qty']) ,2 );?></p></td>
											<td width="100" align="center"><p><?php echo number_format($process_loss,3); ?>%</p></td>
											<td width="80" align="center"><p><?php echo $row['roll']; ?></p></td>
											<td width="100" align="left"><p><?php echo $row['remarks']; ?></p></td>
											<td width="" align="left"><p><?php echo $row['delivery_remarks']; ?></p></td>
										</tr>
										<?
										$total_qc_qty+=$row['qc_pass_qty'];
										//$total_qc_qty_temporary=
										$total_delv_qty+=$row['delivery_qty'];
										$total_balance_qty+=($row['qc_pass_qty']-$row['delivery_qty']);

										//if ($recvNo!=$row['recv_number']) {
											$total_unit_wise_qc_qty[$uomId] += $row['qc_pass_qty'];
										//}
										$total_unit_wise_delv_qty[$uomId] += $row['delivery_qty'];
										$total_unit_wise_balance_qty[$uomId] += ($row['qc_pass_qty']-$row['delivery_qty']);
										$total_unit_wise_roll_no[$uomId] += $row['roll'];
										$total_unit_wise_grey_used_qty[$uomId] += $row['grey_used_qty'];
										$recvNo=$row['recv_number'];


										$i++;
									}

									else if($cbo_value_with==2 && $stock==0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
											<td width="40"><? echo $i;?> </td>
											<td width="100"><p><? echo $producId;?></p></td>
											<td width="100"><p><? echo $row['challan_no'];?></p></td>
											<td width="80"><p><? echo change_date_format($row['receive_date']);?></p></td>
											<td width="80"><p><?
											$delDate =explode(",",$deliveryDate);
											$delvDate="";$executionDay="";
											foreach ($delDate as $val ) {
												$delvDate.=change_date_format($val).",";
												$executionDay+=datediff("d",$row['receive_date'],$val);
											}
											$delvDate=chop($delvDate,",");
											 echo $delvDate; //echo change_date_format($deliveryDate);?></p></td>
											<td width="80"><p><?  if($row['receive_date']!="") echo $executionDay; //datediff("d",$row['receive_date'],$deliveryDate);?></p></td>
											<td width="80"><p><? echo $source_arr[$row['knitting_source']];?></p></td>
											<td width="80" align="center"><p><? echo $kinttin_company;?></p></td>
											<td width="80"><p><? echo $floor_arr[$row['floor']];?></p></td>
											<td width="80"><p><? echo $machinArr[$row['machine_no_id']]; ?></p></td>
											<td width="80" align="center"><p><? echo $buyer_Arr[$row['buyer_id']];?></p></td>
											<td width="100" align="center"><p><? echo $po_data_arr[$row['order_id']]['style_ref_no'];?></p></td>
											<td width="80"><p><? echo $batch_no_arr[$row['batch_id']]['booking_no'];?></p></td>
											<td width="80"><p><? echo $batch_no_arr[$row['batch_id']]['sales_order_no']; ?></p></td>
											<td width="80" title="<? echo $row['batch_id']; ?>"><p><? echo $batch_no_arr[$row['batch_id']]['batch_no'];?></p>  </td>
											<td width="100" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['extention_no'];?></p>  </td>
											<td width="80"> <p><? echo $constructtion_arr[$row['fab_descr_id']] ; ?></p></td>
											<td width="100"><p><? echo $composition_arr[$row['fab_descr_id']];?></p></td>
											<td width="100" align="right"><p><?php echo $row['width'];?></p>  </td>
											<td width="80" align="right"><p><? echo $fabric_typee[$row['dia_width_type']];?></p></td>
											<td width="80" align="right"><p><?php echo $row['gsm'];?></p></td>
											<td width="80"><p><?php echo $color_library[$row['color_id']];?></p></td>
											<td width="80" align="right"><p><?php echo $row['grey_used_qty'];?></p></td>
											<td width="80" align="right" title="<? echo number_format($row['qc_pass_qty'],2); ?>">
												<p>
													<?
													//if ($recvNo!=$row['recv_number']) {
														echo number_format($row['qc_pass_qty'],2);
													//}

														//echo number_format($receive_qnty[$row['recv_number']][$uomId][$producId][$batchID]['receive_qnty'],2);
													?>
												</p>
											</td>
											<td width="80" align="right"><p><?php echo number_format($row['delivery_qty'],2);?></p></td>
											<td width="80" align="right"><p><?php echo number_format( ($row['qc_pass_qty']-$row['delivery_qty']) ,2 );?></p></td>
											<td width="100" align="center"><p><?php echo number_format($process_loss,3); ?>%</p></td>
											<td width="80" align="center"><p><?php echo $row['roll']; ?></p></td>
											<td width="" align="left"><p><?php echo $row['remarks']; ?></p></td>
											<td width="" align="left"><p><?php echo $row['delivery_remarks']; ?></p></td>
										</tr>
										<?
										$total_qc_qty+=$row['qc_pass_qty'];
										//$total_qc_qty_temporary=
										$total_delv_qty+=$row['delivery_qty'];
										$total_balance_qty+=($row['qc_pass_qty']-$row['delivery_qty']);

										//if ($recvNo!=$row['recv_number']) {
											$total_unit_wise_qc_qty[$uomId] += $row['qc_pass_qty'];
										//}
										$total_unit_wise_delv_qty[$uomId] += $row['delivery_qty'];
										$total_unit_wise_balance_qty[$uomId] += ($row['qc_pass_qty']-$row['delivery_qty']);
										$total_unit_wise_roll_no[$uomId] += $row['roll'];
										$total_unit_wise_grey_used_qty[$uomId] += $row['grey_used_qty'];
										$recvNo=$row['recv_number'];


										$i++;
									}
								}
							}
						}
						?>
						<tr style="background-color:#ccc;">
							<td colspan="22"> Sub Total Of  <?php echo $unit_of_measurement[$uomId];?> </td>
							<td align="right"><p> <?php echo number_format($total_unit_wise_grey_used_qty[$uomId],2); ?></p></td>
							<td align="right"><p> <?php echo number_format($total_unit_wise_qc_qty[$uomId],2); ?></p></td>
							<td align="right"><p> <?php echo number_format($total_unit_wise_delv_qty[$uomId],2); ?></td>
								<td align="right"><p><?php echo number_format($total_unit_wise_balance_qty[$uomId],2); ?></p></td>
								<td align="center"><p><?php //echo number_format($total_unit_wise_roll_no[$uomId],2); ?></p></td>
								<td align="center"><p><?php echo number_format($total_unit_wise_roll_no[$uomId],2); ?></p></td>
								<td align="right"><p></p></td>
								<td align="right"><p></p></td>
							</tr>
							<?
						}
						?>
					</table>
					<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<tr>
								<th width="40"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="100"></th>
								<th width="80"><p>&nbsp;</p></th>
								<th width="100"><p>&nbsp;</p></th>
								<th width=""><p>&nbsp;</p></th>

							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>
		<?
		exit();
}

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$floor_id=str_replace("'","",$cbo_floor_id);
	$txt_mc_no=str_replace("'","",$txt_mc_no);
	$source_id =str_replace("'","",$cbo_source);
	$buyer_name=str_replace("'","",$cbo_buyer_name);

	$dynamic_search=str_replace("'","",$txt_dynamic_search);
	$dynamic_id = str_replace("'","",$hide_dynamic_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$rpt_type=str_replace("'","",$rpt_type);
	$search_by = str_replace("'","",$cbo_search_by);
	$year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	if($db_type==0)
	{
		if($date_from!=="" and $date_to!=="")
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
			$date_cond=" c.delevery_date between '$date_from' and '$date_to'";
		}else{
			if($year_selection>0)
			{
				$production_year_condition=" and YEAR(a.insert_date)=$year_selection";
			}
		}
	}
	else if($db_type==2)
	{
		if($date_from!=="" and $date_to!=="")
		{
			$date_from=change_date_format($date_from,'','',1);
			$date_to=change_date_format($date_to,'','',1);
			$date_cond=" and c.delevery_date between '$date_from' and '$date_to'";
		}else {
			if($year_selection>0)
			{
				$production_year_condition =" and to_char(a.insert_date,'YYYY')=$year_selection";
			}
		}
	}

	if($location_id==0)
	{
		$location_cond="";
	}else {
		$location_cond="and a.location_id=$location_id";
	}

	if($floor_id==0)
	{
		$floor_cond="";
	}else {
		$floor_cond="and b.floor=$floor_id";
	}

	if($buyer_name==0)
	{
		$buyer_cond="";
	}else {
		$buyer_cond="and b.buyer_id='$buyer_name'";
	}

	if($source_id==0)
	{
		$sourcCond="";
	}else {
		$sourcCond="and a.knitting_source = $source_id";
	}

	if($txt_mc_no!="")
	{
		$machinsql=sql_select("select id from lib_machine_name where status_active=1 and is_deleted=0 and machine_no like '%$txt_mc_no%' and company_id in($company_id)");
		foreach($machinsql as $row)
		{
			$machine_no_id .= $row[csf('id')].",";
		}

		$machine_no_id = implode(",", array_filter(array_unique(explode(",",chop($machine_no_id,",")))));
		if($machine_no_id!="")
		{
			$machinCond ="and b.machine_no_id in($machine_no_id)";
		}else{
			$machinCond ="and b.machine_no_id in(0)";
		}
	}

	if($search_by==1 && $dynamic_search!="") // Batch no
	{
		if($dynamic_id!="")
		{
			$searchByCondition = "and b.batch_id=$dynamic_id";
		} else {

			/*$sqlbatch = sql_select("SELECT id as batch_id FROM pro_batch_create_mst WHERE status_active=1 AND is_deleted=0 AND batch_no='$dynamic_search'");

			foreach($sqlbatch as $row)
			{
				$batch_id .= $row[csf('batch_id')].",";
			}
			$batch_ids = implode(",", array_filter(array_unique(explode(",",chop($batch_id,",")))));
			if($batch_id!="")
			{
				$searchByCondition = "and b.batch_id in($batch_ids)";
			}else {
				$searchByCondition = "and b.batch_id in(0)";
			}*/

			$searchByCondition = " and e.batch_no='$dynamic_search'";
		}
	}
	else if ($search_by==2 && $dynamic_search!="") // FSO
	{
		if($dynamic_id!="")
		{
			$searchByCondition = " and b.order_id='$dynamic_id' and b.is_sales=1";
		} else {
			$sqlFso = sql_select("select a.id as order_id  from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $production_year_condition and a.job_no_prefix_num='$dynamic_search'");

			foreach($sqlFso as $row)
			{
				$order_id .= $row[csf('order_id')].",";
			}

			$order_id = implode(",", array_filter(array_unique(explode(",",chop($order_id,",")))));
			if($order_id!="")
			{
				$searchByCondition = " and b.order_id in('$order_id') and b.is_sales=1";
			}else {
				$searchByCondition = " and b.order_id in('0')";
			}
		}
	}
	else if ($search_by==3 && $dynamic_search!="") // Booking no
	{
		$bookingNoArr = explode("*", $dynamic_search);

		$bookingNo = "'".implode("','", $bookingNoArr)."'";

		/*$sql_booking = sql_select("SELECT a.id from pro_batch_create_mst a where a.status_active=1 and a.is_deleted=0 and a.booking_no in($bookingNo)");

		foreach($sql_booking as $row)
		{
			$all_batch_id .= $row[csf('id')].",";
		}

		$all_batch_id = implode(",", array_filter(array_unique(explode(",",chop($all_batch_id,",")))));

		if($all_batch_id!="")
		{
			$searchByCondition = "and b.batch_id in($all_batch_id)";
		}else {
			$searchByCondition = "and b.batch_id in('0')";
		}*/

		$searchByCondition = " and f.booking_no in($bookingNo)";
	}


	if($search_by==3)
	{
		if($cbo_booking_type ==1)
		{
			//main
			$booking_type_cond = " and f.booking_type=1 and f.is_short=2 and f.entry_form !=108";
		}
		else if($cbo_booking_type ==2)
		{
			//partial
			$booking_type_cond = " and f.booking_type=1 and f.is_short=2 and f.entry_form=108";
		}
		else if($cbo_booking_type ==3)
		{
			//main
			$booking_type_cond = " and f.booking_type=1 and f.is_short=1";
		}
		else if($cbo_booking_type ==4)
		{
			//sample with order
			$booking_type_cond = " and f.booking_type=4 and f.booking_no not like '%-SMN-%'";
		}
		else if($cbo_booking_type ==5)
		{
			//sample without order
			$booking_type_without_cond = " and f.booking_type=4 and f.booking_no like '%-SMN-%'";
		}
	}


	$source_arr=array(1=>'Inhouse',3=>'Outbound');

	if($db_type==0)
	{
		$recv_number_str="group_concat(a.recv_number)";
		$delv_date_str="group_concat(c.delevery_date)";


	}
	else if($db_type==2)
	{
		$recv_number_str="listagg(a.recv_number,',') within group (order by a.recv_number)";
		$delv_date_str="listagg(c.delevery_date,',') within group (order by c.delevery_date)";
	}

	$sql_dtls_order=$sql_dtls_nonOrder=$concate="";

	if($booking_type_without_cond =="")
	{
		$sql_dtls_order="SELECT  listagg(cast ( a.recv_number as varchar(4000)),',') within group (order by a.recv_number) as recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id,a.fabric_nature,b.id,b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty as qc_pass_qty, b.reject_qty, b.no_of_roll, b.order_id, f.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, e.is_sales, b.floor, b.original_gsm, b.original_width, listagg(cast (c.delevery_date as varchar(4000)),',') within group (order by c.delevery_date) as delevery_date, sum(d.current_delivery) as current_delivery, sum(d.roll) as roll,b.remarks, c.sys_number, b.grey_used_qty,c.remarks as delivery_remarks
 		from inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_grey_prod_delivery_mst c,pro_grey_prod_delivery_dtls d, pro_batch_create_mst e, wo_booking_mst f where a.knitting_company=$company_id and a.id=b.mst_id and a.id=d.grey_sys_id and b.id=d.sys_dtls_id and c.id=d.mst_id and a.entry_form in(7,66) and d.entry_form=67 and a.item_category=2 and a.status_active=1 $date_cond $production_year_condition $location_cond $searchByCondition $sourcCond $machinCond $floor_cond $buyer_cond $booking_type_cond and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and b.batch_id=e.id and e.booking_no=f.booking_no group by a.receive_date, a.knitting_source, a.knitting_company, a.location_id, a.fabric_nature, b.id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.order_id, f.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, e.is_sales, b.floor, b.original_gsm, b.original_width,b.remarks, c.sys_number, b.grey_used_qty,c.remarks"; //and b.uom !=0
	}

	if($booking_type_cond =="")
	{
		$sql_dtls_nonOrder ="SELECT  listagg(cast ( a.recv_number as varchar(4000)),',') within group (order by a.recv_number) as recv_number, a.receive_date,a.knitting_source,a.knitting_company, a.location_id, a.fabric_nature, b.id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty as qc_pass_qty, b.reject_qty, b.no_of_roll, b.order_id, f.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, e.is_sales, b.floor, b.original_gsm, b.original_width, listagg(cast (c.delevery_date as varchar(4000)),',') within group (order by c.delevery_date) as delevery_date, sum(d.current_delivery) as current_delivery, sum(d.roll) as roll, b.remarks, c.sys_number, b.grey_used_qty,c.remarks as delivery_remarks
 		from inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_grey_prod_delivery_mst c,pro_grey_prod_delivery_dtls d, pro_batch_create_mst e, wo_non_ord_samp_booking_mst f where a.knitting_company=$company_id and a.id=b.mst_id and a.id=d.grey_sys_id and b.id=d.sys_dtls_id and c.id=d.mst_id and a.entry_form in(7,66) and d.entry_form=67 and a.item_category=2 and a.status_active=1 $date_cond $production_year_condition $location_cond $searchByCondition $sourcCond $machinCond $floor_cond $buyer_cond $booking_type_without_cond and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and b.batch_id=e.id and e.booking_no=f.booking_no group by a.receive_date, a.knitting_source, a.knitting_company, a.location_id, a.fabric_nature, b.id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.receive_qnty, b.reject_qty, b.no_of_roll, b.order_id, f.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, e.is_sales, b.floor, b.original_gsm, b.original_width,b.remarks, c.sys_number, b.grey_used_qty,c.remarks
		order by uom,prod_id, batch_id "; //and b.uom !=0
	}

	//echo $sql_dtls_order;
 	$concate = " union all ";

 	if($sql_dtls_order !="" && $sql_dtls_nonOrder =="")
 	{
 		$sql_dtls = $sql_dtls_order;
 	}
 	else if($sql_dtls_order =="" && $sql_dtls_nonOrder !="")
 	{
 		$sql_dtls = $sql_dtls_nonOrder;
 	}
 	else if($sql_dtls_order !="" && $sql_dtls_nonOrder !="")
 	{
 		$sql_dtls = $sql_dtls_order.$concate.$sql_dtls_nonOrder;
 	}

 	//echo $sql_dtls;

	$sql_dtls_result=sql_select($sql_dtls);
	$data=array();$production_dtls_id="";
	$batchIdChk = array();
	$batchIdIdArr = array();
	foreach($sql_dtls_result as $row)
	{
		$knitting_company_id .= $row[csf('knitting_company')].",";
		$location_id .= $row[csf('location_id')].",";
		$color_id .= $row[csf('color_id')].",";
		$batch_id .= $row[csf('batch_id')].",";
		$machine_no_id .= $row[csf('machine_no_id')].",";
		$buyer_id .= $row[csf('buyer_id')].",";
		$floor .= $row[csf('floor')].",";
		$fabric_description_id .= $row[csf('fabric_description_id')].",";
		if($row[csf('is_sales')]==1)
		{
			$salesOrder_id .= $row[csf('order_id')].",";
		}
		else
		{
			$po_id .= $row[csf('order_id')].",";
		}

		$uom = $row[csf('uom')];
		$prodId = $row[csf('prod_id')];
		$batchId = $row[csf('batch_id')];
		$deleveryDate = $row[csf('delevery_date')];
		//$receiveNo = $row[csf('recv_number')];
		//$receiveDate = $row[csf('receive_date')];

		$data[$prodId][$batchId][$deleveryDate]['recv_number'] 		= $row[csf('recv_number')];
		$data[$prodId][$batchId][$deleveryDate]['qc_pass_qty'] 		+= $row[csf('qc_pass_qty')];
		$data[$prodId][$batchId][$deleveryDate]['reject_qty'] 		+= $row[csf('reject_qty')];
		$data[$prodId][$batchId][$deleveryDate]['knitting_source'] 	= $row[csf('knitting_source')];
		$data[$prodId][$batchId][$deleveryDate]['knitting_company'] 	= $row[csf('knitting_company')];
		$data[$prodId][$batchId][$deleveryDate]['floor'] 				= $row[csf('floor')];
		$data[$prodId][$batchId][$deleveryDate]['machine_no_id'] 		= $row[csf('machine_no_id')];
		$data[$prodId][$batchId][$deleveryDate]['shift_name'] 		= $row[csf('shift_name')];
		$data[$prodId][$batchId][$deleveryDate]['receive_date'] 		= $row[csf('receive_date')];
		$data[$prodId][$batchId][$deleveryDate]['buyer_id'] 			= $row[csf('buyer_id')];
		$data[$prodId][$batchId][$deleveryDate]['batch_id'] 			= $row[csf('batch_id')];
		$data[$prodId][$batchId][$deleveryDate]['fabric_shade'] 		= $row[csf('fabric_shade')];
		$data[$prodId][$batchId][$deleveryDate]['dia_width_type'] 	= $row[csf('dia_width_type')];
		$data[$prodId][$batchId][$deleveryDate]['color_id'] 			= $row[csf('color_id')];
		$data[$prodId][$batchId][$deleveryDate]['width'] 				= $row[csf('width')];
		$data[$prodId][$batchId][$deleveryDate]['gsm'] 				= $row[csf('gsm')];
		$data[$prodId][$batchId][$deleveryDate]['shelf_no'] 			= $row[csf('shelf_no')];
		$data[$prodId][$batchId][$deleveryDate]['order_id'] 			= $row[csf('order_id')];
		$data[$prodId][$batchId][$deleveryDate]['fab_descr_id']		= $row[csf('fabric_description_id')];
		$data[$prodId][$batchId][$deleveryDate]['delivery_qty'] 		+= $row[csf('current_delivery')];
		$data[$prodId][$batchId][$deleveryDate]['remarks'] 			= $row[csf('remarks')];
		$data[$prodId][$batchId][$deleveryDate]['roll'] 				= $row[csf('roll')];
		$data[$prodId][$batchId][$deleveryDate]['challan_no'] 		= $row[csf('sys_number')];
		$data[$prodId][$batchId][$deleveryDate]['grey_used_qty'] 		= $row[csf('grey_used_qty')];
		$data[$prodId][$batchId][$deleveryDate]['delivery_remarks'] 	= $row[csf('delivery_remarks')];
		$data[$prodId][$batchId][$deleveryDate]['barcode_no'] 	.= $row[csf('barcode_no')].',';
		$data[$prodId][$batchId][$deleveryDate]['no_of_roll']++;
		$production_dtls_id.=$row[csf('id')].",";


		if($batchIdChk[$row[csf('batch_id')]] == "")
        {
            $batchIdChk[$row[csf('batch_id')]] = $row[csf('batch_id')];
            array_push($batchIdIdArr,$row[csf('batch_id')]);
        }

	}
	if(!empty($batchIdIdArr))
	{
		$sql_dyeing = "SELECT a.machine_id,a.batch_id,a.floor_id FROM pro_fab_subprocess a, lib_machine_name b WHERE a.machine_id = b.id AND a.load_unload_id = 1  and a.entry_form = 35
		AND a.status_active = 1 AND a.is_deleted = 0 ".where_con_using_array($batchIdIdArr,0,'a.batch_id')." ";
		//echo $sql_dyeing;
		$rslt_dyeing = sql_select($sql_dyeing);
		$dyeing_production_arr = array();
		foreach($rslt_dyeing as $row)
		{
			$dyeing_production_arr[$row[csf('batch_id')]]['machine_id'] = $row[csf('machine_id')];
			$dyeing_production_arr[$row[csf('batch_id')]]['floor_id'] = $row[csf('floor_id')];
		}
	}
	/*$production_dtls_id=chop($production_dtls_id,",");
	$sql_rev_qnty = sql_select("select a.recv_number, a.receive_date, sum(b.receive_qnty) as receive_qnty,b.uom,b.batch_id,b.prod_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in(7,66) and a.status_active=1 and a.is_deleted=0 $production_year_condition $location_cond $searchByCondition $sourcCond $machinCond $floor_cond $buyer_cond and b.id in($production_dtls_id) group by a.recv_number, a.receive_date,b.uom,b.batch_id,b.prod_id  order by a.recv_number,b.uom,b.prod_id,b.batch_id");

	foreach ($sql_rev_qnty as $row) {
		$receive_qnty[$row[csf('recv_number')]][$row[csf('uom')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['receive_qnty']=$row[csf('receive_qnty')];
	}*/

	$knitting_company_id = implode(",", array_filter(array_unique(explode(",",chop($knitting_company_id,",")))));
	$location_id = implode(",", array_filter(array_unique(explode(",",chop($location_id,",")))));
	$color_id = implode(",", array_filter(array_unique(explode(",",chop($color_id,",")))));
	$machine_no_id = implode(",", array_filter(array_unique(explode(",",chop($machine_no_id,",")))));
	$buyer_id = implode(",", array_filter(array_unique(explode(",",chop($buyer_id,",")))));
	$floor = implode(",", array_filter(array_unique(explode(",",chop($floor,",")))));
	$batch_id = implode(",", array_filter(array_unique(explode(",",chop($batch_id,",")))));
	$fabric_description_id = implode(",", array_filter(array_unique(explode(",",chop($fabric_description_id,",")))));

	$salesOrder_id = implode(",", array_filter(array_unique(explode(",",chop($salesOrder_id,",")))));
	$po_id = implode(",", array_filter(array_unique(explode(",",chop($po_id,",")))));



	if($knitting_company_id!="")
	{
		$kinttin_companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0 and id in($knitting_company_id)" ,"id","company_short_name");

		$kinttin_supplierArr = return_library_array("select id,short_name from lib_supplier where status_active=1 and is_deleted=0 and id in($knitting_company_id)" ,"id","short_name");
	}

	if($location_id!="")
	{
		$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0 and id in($location_id) and company_id in($knitting_company_id)",'id','location_name');
	}


	$floor_arr=return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id in($knitting_company_id)","id","floor_name");

	if($buyer_id!="")
	{
		$buyer_Arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 and id in($buyer_id)","id","buyer_name");
	}

	if($color_id!="")
	{
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 and id in($color_id)", "id", "color_name");
	}


	$machinArr=return_library_array( "select id, (machine_no || '-' || brand) as machine_name from lib_machine_name where status_active=1 and is_deleted=0 and company_id in($knitting_company_id)",'id','machine_name');

	$composition_arr = array();
	$constructtion_arr = array(); // yearn data puller
	if($fabric_description_id != "")
	{
		$fabric_desc_arr = explode(",", $fabric_description_id);
	    $all_fabric_desc_cond=""; $fabDesCond="";
	    if($db_type==2 && count($fabric_desc_arr)>999)
	    {
	    	$all_fabric_desc_arr_chunk=array_chunk($fabric_desc_arr,999) ;
	    	foreach($all_fabric_desc_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$fabDesCond.="  a.id in($chunk_arr_value) or ";
	    	}
	    	$all_fabric_desc_cond.=" and (".chop($fabDesCond,'or ').")";
	    }
	    else
	    {
	    	$all_fabric_desc_cond=" and a.id in($fabric_description_id)";
	    }

		$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $all_fabric_desc_cond";
		$data_array = sql_select($sql_deter);
		foreach ($data_array as $row) {
			$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
			$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}
	}

	if($po_id!="")
	{
		$po_data_arr=array(); // wo po data puller
		$sql_po=sql_select("SELECT b.id, a.style_ref_no,b.b.po_number from wo_po_details_master a, wo_po_break_down b where b.job_no_mst=a.job_no and  a.status_active=1 and a.is_deleted=0 and b.id in($po_id)");
		foreach($sql_po as $row)
		{
			$po_data_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}


	if($batch_id!="")
	{
		$batch_id_arr = explode(",", $batch_id);
        $all_batch_id_cond=""; $batchCond="";
        if($db_type==2 && count($batch_id_arr)>999)
        {
        	$all_batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
        	foreach($all_batch_id_arr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$batchCond.="  a.id in($chunk_arr_value) or ";
        	}
        	$all_batch_id_cond.=" and (".chop($batchCond,'or ').")";
        }
        else
        {
        	$all_batch_id_cond=" and a.id in($batch_id)";
        }

		$batch_no_arr=array(); // batch data puller
		$batch_info_data_arr=array(); // batch data puller
		$booking_no_chk=array();
		$bookingNoArr=array();


		$sql_batch=sql_select("SELECT a.id,sum(b.batch_qnty) as batch_qnty, a.batch_weight,a.total_trims_weight,a.sales_order_no,a.is_sales,a.batch_no,a.booking_no,a.extention_no, b.item_description, b.barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 $all_batch_id_cond group by a.id,a.batch_weight,a.total_trims_weight,a.sales_order_no,a.is_sales,a.batch_no,a.booking_no,a.extention_no, b.item_description, b.barcode_no" );

		foreach($sql_batch as $row)
		{
			$batch_no_arr[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batch_no_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
			$batch_no_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_no_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_no_arr[$row[csf('id')]]['sales_order_no']=$row[csf('sales_order_no')];
			$batch_no_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$batch_no_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_no_arr[$row[csf('id')]]['item_description']=$row[csf('item_description')];
			$batch_info_data_arr[$row[csf('barcode_no')]]['batch_qnty']=$row[csf('batch_qnty')];
			$booking_no_arr[] = "'".$row[csf('booking_no')]."'";

			$prod_id_arr[] = $row[csf("prod_id")];

			if($booking_no_chk[$row[csf('booking_no')]] == "")
			{
				$booking_no_chk[$row[csf('booking_no')]] = $row[csf('booking_no')];
				array_push($bookingNoArr,$row[csf('booking_no')]);
			}

		}
		//echo "<pre>";print_r($batch_info_data_arr);

		$sql_booking="SELECT a.booking_no, b.fabric_color_id, b.construction, b.fin_fab_qnty, b.grey_fab_qnty
		from wo_booking_mst a, wo_booking_dtls b
		where a.booking_no=b.booking_no  and b.booking_type=1 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($bookingNoArr,1,'a.booking_no')."";
		//echo $sql_booking;
		$bookingArray=sql_select($sql_booking);
		$fab_booking_qty_arr=$grey_fab_booking_qnty_arr=array();
		foreach ($bookingArray as $value)
		{
			$fab_booking_qty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]][$value[csf('construction')]]['fin_fab_qnty']+=$value[csf('fin_fab_qnty')];
			$grey_fab_booking_qnty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]][$value[csf('construction')]]['grey_fab_qnty']+=$value[csf('grey_fab_qnty')];
		}

		//var_dump($fab_booking_qty_arr);
	}

	$prod_array=array();
	if(!empty($prod_id_arr)){
		$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where id in(".implode(",",$prod_id_arr).") and item_category_id in(2,13)");
		foreach($prodData as $row)
		{
			$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		}
	}


	if($salesOrder_id!="")
	{
		$get_data_from_sales_order = sql_select("select a.id,a.style_ref_no,b.determination_id,b.width_dia_type,b.color_id,b.gsm_weight,b.dia from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id in($salesOrder_id) group by a.id,a.style_ref_no,b.determination_id,b.width_dia_type,b.color_id,b.gsm_weight,b.dia");
		foreach ($get_data_from_sales_order as $sale_row)
		{
			$po_data_arr[$sale_row[csf('id')]]['style_ref_no']=$sale_row[csf('style_ref_no')];
			$po_data_arr[$sale_row[csf('id')]]['buyer']=$sale_row[csf('po_buyer')];
		}
	}

	ob_start();
	?>
	<div>
		<fieldset style="width:2100px;">
			<table width="1760px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$cbo_company];?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
				</tr>
			</table>
			<table width="2097" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Challan No</th>
					<th width="80">Finishing Date</th>
					<th width="80">Delivery Date</th>
					<th width="80">Source</th>
					<th width="80">Finishing Company</th>
					<th width="80">Floor</th>
					<th width="80">M/C Name</th>
					<th width="80">Buyer Name</th>
					<th width="100">Style Ref.</th>
					<th width="80">Booking No</th>
					<th width="80">FSO No</th>
					<th width="80">Batch/Lot No</th>
					<th width="100">Extension No</th>
					<th width="80">Fabric Type</th>
					<th width="100">Fab. Composition</th>
					<th width="100">F. Dia</th>
					<th width="80">Dia Type</th>
					<th width="80">F.GSM</th>
					<th width="80">Fabric Color</th>
					<th width="80">Grey used</th>
					<th width="80">QC Pass Qty</th>
					<th width="80">Delivery Qty</th>
					<th width="80">Balance</th>
					<th width="">No of Roll</th>

				</thead>
			</table>
			<div style="width:2100px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table width="2080" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="tbl_dyeing">
					<?
					$i=1;
					$total_qc_qty=$total_delv_qty=$total_balance_qty=$total_grey_used_qty = 0;

						foreach($data as $producId=>$daliveryDateArr)
						{
							foreach($daliveryDateArr as $batchID=>$batch_data)
							{
								foreach($batch_data as $deliveryDate=>$row)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if($row['knitting_source']==1)
									{
										$kinttin_company = $kinttin_companyArr[$row['knitting_company']];
									}else{
										$kinttin_company = $kinttin_supplierArr[$row['knitting_company']];
									}
									$stock = $row['qc_pass_qty']-$row['delivery_qty'];

									$machine_id = $dyeing_production_arr[$batchID]['machine_id'];
									$floor_id = $dyeing_production_arr[$batchID]['floor_id'];

									$barcode_nos=array_unique(array_filter(explode(",",$row['barcode_no'])));
									//echo "<pre>";print_r($barcode_nos);
									$grey_used_qnty = 0;
									foreach ($barcode_nos as $b_barcode)
									{
										$grey_used_qnty +=$batch_info_data_arr[$b_barcode]['batch_qnty'];
									}



									if($cbo_value_with==0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
											<td width="40" align="center"><? echo $i;?> </td>
											<td width="100" align="center"><p><? echo $row['challan_no'];?></p> </td>
											<td width="80" align="center"><p><? echo change_date_format($row['receive_date']);?></p></td>
											<td width="80" align="center"><p><?
											$delDate =explode(",",$deliveryDate);
											$delvDate="";$executionDay="";
											foreach ($delDate as $val )
											{
												$delvDate.=change_date_format($val).",";
												$executionDay+=datediff("d",$row['receive_date'],$val);
											}
											$delvDate=chop($delvDate,",");
											 echo $delvDate; ?></p></td>
											<td width="80" align="center"><p><? echo $source_arr[$row['knitting_source']];?></p></td>
											<td width="80" align="center"><p><? echo $kinttin_company;?></p></td>
											<td width="80" align="center" title="<? echo $floor_id;?>"><p><? echo $floor_arr[$floor_id];?></p></td>
											<td width="80" align="center" title="<? echo $machine_id;?>"><p><? echo $machinArr[$machine_id]; ?></p></td>
											<td width="80" align="center"><p><? echo $buyer_Arr[$row['buyer_id']];?></p></td>
											<td width="100" align="center"><p><? echo $po_data_arr[$row['order_id']]['style_ref_no'];?></p></td>
											<td width="80" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['booking_no'];?></p></td>
											<td width="80" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['sales_order_no']; ?></p></td>
											<td width="80" align="center" title="<? echo $row['batch_id']; ?>"><p><? echo $batch_no_arr[$row['batch_id']]['batch_no'];?></p>  </td>
											<td width="100" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['extention_no'];?></p>  </td>
											<td width="80" align="center"> <p><? echo $constructtion_arr[$row['fab_descr_id']] ; ?></p></td>
											<td width="100" align="center"><p><? echo $composition_arr[$row['fab_descr_id']];?></p></td>
											<td width="100" align="center"><p><?php echo $row['width'];?></p>  </td>
											<td width="80" align="center"><p><? echo $fabric_typee[$row['dia_width_type']];?></p></td>
											<td width="80" align="center"><p><?php echo $row['gsm'];?></p></td>
											<td width="80" align="center"><p><?php echo $color_library[$row['color_id']];?></p></td>
											<td width="80" align="right" title="<? echo $producId.'='.$row['batch_id'];?>"><p><?php echo number_format($grey_used_qnty,2);?></p></td>
											<td width="80" align="right" title="<? echo number_format($row['delivery_qty'],2); ?>">
												<p><? echo number_format($row['delivery_qty'],2);?></p>
											</td>
											<td width="80" align="right"><p><?php echo number_format($row['delivery_qty'],2);?></p></td>
											<td width="80" align="right"><p><?php echo number_format( ($row['delivery_qty']-$row['delivery_qty']) ,2 );?></p></td>
											<td width="" align="right"><p><?php echo $row['no_of_roll']; ?></p></td>
										</tr>
										<?
										$total_qc_qty+=$row['delivery_qty'];
										$total_delv_qty += $row['delivery_qty'];
										$total_balance_qty += ($row['delivery_qty']-$row['delivery_qty']);
										$total_grey_used_qty += $grey_used_qnty;

										$i++;
									}
									else if($cbo_value_with==1 && $stock>0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
											<td width="40" align="center"><? echo $i;?> </td>
											<td width="100" align="center"><p><? echo $row['challan_no'];?></p> </td>
											<td width="80" align="center"><p><? echo change_date_format($row['receive_date']);?></p></td>
											<td width="80" align="center"><p><?
											$delDate =explode(",",$deliveryDate);
											$delvDate="";$executionDay="";
											foreach ($delDate as $val ) {
												$delvDate.=change_date_format($val).",";
												$executionDay+=datediff("d",$row['receive_date'],$val);
											}
											$delvDate=chop($delvDate,",");
											 echo $delvDate; ?></p></td>
											<td width="80" align="center"><p><? echo $source_arr[$row['knitting_source']];?></p></td>
											<td width="80" align="center"><p><? echo $kinttin_company;?></p></td>
											<td width="80" align="center"><p><? echo $floor_arr[$row['floor']];?></p></td>
											<td width="80" align="center"><p><? echo $machinArr[$row['machine_no_id']]; ?></p></td>
											<td width="80" align="center"><p><? echo $buyer_Arr[$row['buyer_id']];?></p></td>
											<td width="100" align="center"><p><? echo $po_data_arr[$row['order_id']]['style_ref_no'];?></p></td>
											<td width="80" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['booking_no'];?></p></td>
											<td width="80" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['sales_order_no']; ?></p></td>
											<td width="80" align="center" title="<? echo $row['batch_id']; ?>"><p><? echo $batch_no_arr[$row['batch_id']]['batch_no'];?></p>  </td>
											<td width="100" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['extention_no'];?></p>  </td>
											<td width="80" align="center"> <p><? echo $constructtion_arr[$row['fab_descr_id']] ; ?></p></td>
											<td width="100" align="center"><p><? echo $composition_arr[$row['fab_descr_id']];?></p></td>
											<td width="100" align="center"><p><?php echo $row['width'];?></p>  </td>
											<td width="80" align="center"><p><? echo $fabric_typee[$row['dia_width_type']];?></p></td>
											<td width="80" align="center"><p><?php echo $row['gsm'];?></p></td>
											<td width="80" align="center"><p><?php echo $color_library[$row['color_id']];?></p></td>
											<td width="80" align="right"><p><?php echo number_format($grey_used_qnty,2);?></p></td>
											<td width="80" align="right" title="<? echo number_format($row['delivery_qty'],2); ?>">
												<p><? echo number_format($row['delivery_qty'],2); ?></p>
											</td>
											<td width="80" align="right"><p><?php echo number_format($row['delivery_qty'],2);?></p></td>
											<td width="80" align="right"><p><?php echo number_format( ($row['delivery_qty']-$row['delivery_qty']) ,2 );?></p></td>
											<td width="" align="center"><p><?php echo $row['no_of_roll']; ?></p></td>
										</tr>
										<?
										$total_qc_qty+=$row['delivery_qty'];
										$total_delv_qty += $row['delivery_qty'];
										$total_balance_qty += ($row['delivery_qty']-$row['delivery_qty']);
										$total_grey_used_qty += $grey_used_qnty;

										$i++;
									}

									else if($cbo_value_with==2 && $stock==0)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
											<td width="40" align="center"><? echo $i;?> </td>
											<td width="100" align="center"><p><? echo $row['challan_no'];?></p></td>
											<td width="80" align="center"><p><? echo change_date_format($row['receive_date']);?></p></td>
											<td width="80" align="center"><p><?
											$delDate =explode(",",$deliveryDate);
											$delvDate="";$executionDay="";
											foreach ($delDate as $val ) {
												$delvDate.=change_date_format($val).",";
												$executionDay+=datediff("d",$row['receive_date'],$val);
											}
											$delvDate=chop($delvDate,",");
											 echo $delvDate; ?></p></td>
											<td width="80" align="center"><p><? echo $source_arr[$row['knitting_source']];?></p></td>
											<td width="80" align="center"><p><? echo $kinttin_company;?></p></td>
											<td width="80" align="center"><p><? echo $floor_arr[$row['floor']];?></p></td>
											<td width="80" align="center"><p><? echo $machinArr[$row['machine_no_id']]; ?></p></td>
											<td width="80" align="center"><p><? echo $buyer_Arr[$row['buyer_id']];?></p></td>
											<td width="100" align="center"><p><? echo $po_data_arr[$row['order_id']]['style_ref_no'];?></p></td>
											<td width="80" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['booking_no'];?></p></td>
											<td width="80" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['sales_order_no']; ?></p></td>
											<td width="80" align="center" title="<? echo $row['batch_id']; ?>"><p><? echo $batch_no_arr[$row['batch_id']]['batch_no'];?></p>  </td>
											<td width="100" align="center"><p><? echo $batch_no_arr[$row['batch_id']]['extention_no'];?></p>  </td>
											<td width="80" align="center"> <p><? echo $constructtion_arr[$row['fab_descr_id']] ; ?></p></td>
											<td width="100" align="center"><p><? echo $composition_arr[$row['fab_descr_id']];?></p></td>
											<td width="100" align="center"><p><?php echo $row['width'];?></p>  </td>
											<td width="80" align="center"><p><? echo $fabric_typee[$row['dia_width_type']];?></p></td>
											<td width="80" align="center"><p><?php echo $row['gsm'];?></p></td>
											<td width="80" align="center"><p><?php echo $color_library[$row['color_id']];?></p></td>
											<td width="80" align="right"><p><?php echo number_format($grey_used_qnty,2);?></p></td>
											<td width="80" align="right" title="<? echo number_format($row['delivery_qty'],2); ?>">
												<p><? echo number_format($row['delivery_qty'],2);?></p>
											</td>
											<td width="80" align="right"><p><?php echo number_format($row['delivery_qty'],2);?></p></td>
											<td width="80" align="right"><p><?php echo number_format( ($row['delivery_qty']-$row['delivery_qty']) ,2 );?></p></td>
											<td width="" align="center"><p><?php echo $row['no_of_roll']; ?></p></td>
										</tr>
										<?
										$total_qc_qty+=$row['delivery_qty'];
										$total_delv_qty += $row['delivery_qty'];
										$total_balance_qty += ($row['delivery_qty']-$row['delivery_qty']);
										$total_grey_used_qty += $grey_used_qnty;

										$i++;
									}
								}
							}
						}

						?>
					</table>
					<table class="rpt_table" width="2080" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<tr>
							<th width="40">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80"><?php echo number_format($total_grey_used_qty,2); ?></th>
							<th width="80"><?php echo number_format($total_qc_qty,2); ?></th>
							<th width="80"><?php echo number_format($total_delv_qty,2); ?></th>
							<th width="80"><?php echo number_format($total_balance_qty,2); ?></th>
							<th width="">&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>
		<?
		exit();
}

?>