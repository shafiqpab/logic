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
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "load_drop_down( 'requires/finishing_production_report_controller',this.value+'_'+$('#cbo_company_id').val(), 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor_id", 100, "select id, floor_name from lib_prod_floor where location_id=$data[0] and company_id=$data[1] and production_process=4 and status_active=1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/finishing_production_report_controller',this.value+'_'+$('#cbo_company_id').val(), 'load_drop_down_machine', 'machine_td');","" );
	exit();
}

if ($action=="load_drop_down_machine")
{
	$data=explode("_",$data);
	echo create_drop_down( "txt_mc_no", 100, "select id, machine_no from lib_machine_name where floor_id=$data[0] and company_id=$data[1] and status_active=1 and is_deleted=0","id,machine_no", 1, "-- select machine --", 0, "","" );
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
			var company_id=$('#cbo_company_id').val();
			if(company_id==0) 
			{ 
				document.getElementById("message").innerHTML = "Please Select Company Name";
			}
			else
			{
				document.getElementById("message").innerHTML = "";
			}
			var booking_type = '<? echo $booking_type?>';
			if($('#cbo_company_id').val() ==0 ||$('#txt_search_common').val() =="" || (booking_type==2 && $('#cbo_search_by').val()==2))
			{				
				if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
				{
					return;
				}
			}			


			show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.		getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'finishing_production_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');
		}
	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:900px;">
				<table width="900" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th width="160" class="must_entry_caption">Company</th>
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
							<td>
	                            <? 
	                               echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'finishing_production_report_controller',this.value, 'load_drop_down_buyer','buyer_td');" );
	                            ?>                            
                        	</td>
							<td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
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
			<div style="color: red; margin-top:15px;" id="message"></div>
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
	$date_from 	= trim($data[5]);
	$date_to 	= trim($data[6]);

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

	$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, b.booking_no,c.booking_no_prefix_num,c.id as booking_id,0 as order_type   from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $job_no_cond $buyer_id_cond $year_cond $booking_no_cond $date_cond and b.booking_type!=2
	group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num

	union all

	select null as job_no, null as job_no_prefix_num, a.company_id as company_name, a.buyer_id as buyer_name, a.booking_no, a.booking_no_prefix_num, a.id as booking_id,1 as order_type
	from wo_non_ord_samp_booking_mst a where a.item_category in (2,3) and a.status_active =1 and a.is_deleted =0 and a.company_id=$company_id $buyer_id_cond2 $booking_no_cond2 $year_cond2 $date_cond2";

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

	$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and is_deleted = 0 $year_field_grpby ";

	$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch weight ", "100,100,100,100,70","520","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_weight", "finishing_production_report_controller",'setFilterGrid("list_view",-1);','0') ;
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year; ?>, 'create_sales_order_no_search_list_view', 'search_div', 'finishing_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

	if($db_type==0)
	{
		if($date_from!=="" and $date_to!=="")
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
			$date_cond=" and a.receive_date between '$date_from' and '$date_to'";
			$date_cond2=" and a.product_date between '$date_from' and '$date_to'";
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
			$date_cond=" and a.receive_date between '$date_from' and '$date_to'";
			$date_cond2=" and a.product_date between '$date_from' and '$date_to'";
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
		$floor_cond="and b.floor=$floor_id"; //$floor_cond="and e.floor_id=$floor_id";
	}

	if($txt_mc_no==0)
	{
		$machine_cond="";
	}else {
		$machine_cond="and b.machine_no_id=$txt_mc_no";
		$machine_cond2="and b.machine_id='$txt_mc_no'";
	}

	if($buyer_name==0)
	{
		$buyer_cond=$buyer_cond2="";
	}else {
		$buyer_cond="and b.buyer_id='$buyer_name'";
		$buyer_cond2="and a.party_id='$buyer_name'";
	}

	if($source_id==0)
	{
		$sourcCond="";
	}else {
		$sourcCond="and a.knitting_source = $source_id";
	}

	if($floor_id!=0)
	{		
		$floorMachinsql=sql_select("select id from lib_machine_name where status_active=1 and is_deleted=0 and floor_id=$floor_id and company_id=$company_id");
		foreach($floorMachinsql as $row)
		{
			$machine_no_id .= $row[csf('id')].",";
		}

		$machine_no_id = implode(",", array_unique(explode(",",chop($machine_no_id,","))));
		if($machine_no_id!="")
		{
			$floorMachinCond ="and b.machine_no_id in($machine_no_id)";
			$floormachinCond2 ="and b.machine_id in($machine_no_id)";
		}
	}

	if($search_by==1 && $dynamic_search!="") // Batch no
	{
		if($dynamic_id!="")
		{
			$searchByCondition = "and b.batch_id=$dynamic_id";
		} 
		else 
		{

			$sqlbatch=sql_select("SELECT id as batch_id FROM pro_batch_create_mst WHERE status_active=1 AND is_deleted=0 AND batch_no='$dynamic_search'");

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
			}
		}
	}
	else if ($search_by==2 && $dynamic_search!="") // FSO
	{
		if($dynamic_id!="")
		{
			$searchByCondition = "and b.order_id='$dynamic_id'";
		} else {
			$sqlFso = sql_select("select a.id as order_id  from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $production_year_condition and a.job_no_prefix_num='$dynamic_search'");

			foreach($sqlFso as $row)
			{
				$order_id .= $row[csf('order_id')].",";
			}

			$order_id = implode(",", array_filter(array_unique(explode(",",chop($order_id,",")))));
			if($order_id!="")
			{
				$searchByCondition = "and b.order_id in('$order_id')";
			}else {
				$searchByCondition = "and b.order_id in('0')";
			}
		}
	}
	else if ($search_by==3 && $dynamic_search!="") // Booking no
	{
		$bookingNoArr = explode("*", $dynamic_search);
		$bookingNo = "'".implode("','", $bookingNoArr)."'";

		$sql_booking = sql_select("SELECT a.id from pro_batch_create_mst a where a.status_active=1 and a.is_deleted=0 and a.booking_no in($bookingNo)");

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
		}
	}
	else if (($search_by==4 || $search_by==5) && $dynamic_search!="") // Order no
	{
		$orderNoArr = explode("*", $dynamic_search);
		$orderNo = "'".implode("','", $orderNoArr)."'";

		if($dynamic_id!="")
		{
			if($search_by==4){
				$searchByCondition = "and b.order_id='$dynamic_id'";
			}else{
				$searchByCondition = "and b.job_no_mst like '%$dynamic_id%'";
			}
		} else {
			$subcon_cond = ($search_by==4)?"and b.order_no in('$dynamic_search')":"and b.job_no_mst like '%$dynamic_search%'";
			$sql_sub_con_sql="select b.job_no_mst,b.id order_id from subcon_ord_mst a,subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $production_year_condition $subcon_cond";
			$sql_sub_con=sql_select($sql_sub_con_sql);
			foreach($sql_sub_con as $row)
			{
				$order_id .= "'".$row[csf('order_id')]."',";
				$job_no_mst .= "'".$row[csf('job_no_mst')]."',";
			}

			if($search_by==4){
				$order_id = implode(",", array_filter(array_unique(explode(",",chop($order_id,",")))));
				if($order_id!="")
				{
					$searchByCondition = "and b.order_id in($order_id)";
				}
			}else{

				$job_no = implode(",", array_filter(array_unique(explode(",",chop($job_no_mst,",")))));
				if($job_no!="")
				{
					$searchByCondition = "and b.job_no_mst like '%$dynamic_id%'";
				}
			}
		}
	}

	$source_arr=array(1=>'Inhouse',2=>'Inbound Subcontract',3=>'Outbound');

	$sql_dtls="SELECT a.recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id,a.fabric_nature,b.id as dtls_id,b.prod_id, cast(b.batch_id as NVARCHAR2 (25)) batch_id, b.body_part_id, b.fabric_description_id,cast('' as NVARCHAR2 (25)) fabric_description,b.gsm, b.width, b.color_id, b.receive_qnty as qc_pass_qty, b.reject_qty, b.no_of_roll, cast(b.order_id as NVARCHAR2 (25)) order_id, b.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, b.is_sales, b.floor, b.original_gsm, b.original_width, 0 as order_type, to_char(b.remarks) as remarks, b.grey_used_qty 
	from inv_receive_master a,pro_finish_fabric_rcv_dtls b 
	where a.company_id=$company_id and a.id=b.mst_id and a.entry_form in(7,66) and a.item_category=2 and a.status_active=1 $date_cond $production_year_condition $location_cond $searchByCondition $sourcCond $machine_cond $floorMachinCond $buyer_cond and a.is_deleted=0 and b.uom !=0";
	if ($source_id!=3 && $source_id!=1)
	{
		$sql_dtls .=" union all ";
		$sql_dtls .="SELECT a.product_no recv_number,a.product_date receive_date,1 as knitting_source,a.company_id knitting_company,a.location_id,0 as fabric_nature,b.id dtls_id,0 prod_id,b.batch_id, 0 as body_part_id,b.cons_comp_id fabric_description_id,b.fabric_description, b.gsm,b.dia_width width,b.color_id,c.quantity qc_pass_qty,b.reject_qnty reject_qty,b.no_of_roll,cast(c.order_id as NVARCHAR2 (25)) order_id, cast(a.party_id as VARCHAR2 (25)) buyer_id, b.machine_id machine_no_id,0 as rack_no,0 as shelf_no,0 as batch_status, cast(b.shift as number (3)) shift_name, 0 as roll_id,0 as roll_no,0 as barcode_no,b.dia_width_type,0 as room,c.quantity production_qty, cast(b.process as varchar2 (200)) process_id,0 as rate,0 as amount,0 as dyeing_charge,d.order_uom as uom,0 as fabric_shade,0 as is_sales,b.floor_id as floor,0 as original_gsm,'' as original_width, 1 as order_type, to_char(a.remarks) as remarks, null as grey_used_qty 
		from subcon_production_mst a,subcon_production_dtls b,subcon_production_qnty c,subcon_ord_dtls d 
		where a.product_type=4 and a.entry_form = 292 and a.company_id=$company_id and a.id=b.mst_id and b.id=c.dtls_id and c.order_id=d.id $date_cond2 $production_year_condition $location_cond $machine_cond2 $floormachinCond2 $searchByCondition $buyer_cond2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	}
	
	 // echo $sql_dtls;
	$sql_dtls_result=sql_select($sql_dtls);

	$data=array();
	$batch_id="";
	foreach($sql_dtls_result as $row)
	{
		$knitting_company_id .= $row[csf('knitting_company')].",";
		$location_id = $row[csf('location_id')].","; 
		$color_id .= $row[csf('color_id')].",";
		if($row[csf('batch_id')]!="")
		$batch_id .= $row[csf('batch_id')].",";
		$machine_no_id .= $row[csf('machine_no_id')].",";
		$buyer_id .= $row[csf('buyer_id')].",";
		$floor .= $row[csf('floor')].",";
		$fabric_description_id .= $row[csf('fabric_description_id')].",";
		$shelf_no .= $row[csf('shelf_no')].",";

		if($row[csf('is_sales')]==1)
		{
			$salesOrder_id .= $row[csf('order_id')].",";
		}
		else
		{
			$po_id .= $row[csf('order_id')].",";
		}

		if($row[csf('order_type')]==1){
			$sub_con_order .= $row[csf('order_id')].",";
		}

		$uom 			= $row[csf('uom')];
		$order_type 	= $row[csf('order_type')];
		$prodId 		= $row[csf('prod_id')];
		$productionNo 	= $row[csf('recv_number')];

		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['recv_number'] 	= $productionNo;
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['qc_pass_qty'] 	+= $row[csf('qc_pass_qty')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['grey_used_qty'] 	+= $row[csf('grey_used_qty')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['reject_qty'] 		+= $row[csf('reject_qty')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['knitting_source'] = $row[csf('knitting_source')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['knitting_company']= $row[csf('knitting_company')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['floor'] 			= $row[csf('floor')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['machine_no_id'] 	= $row[csf('machine_no_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['shift_name'] 		= $row[csf('shift_name')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['receive_date'] 	= $row[csf('receive_date')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['buyer_id'] 		= $row[csf('buyer_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['batch_id'] 		= $row[csf('batch_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['fabric_shade'] 	= $row[csf('fabric_shade')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['dia_width_type'] 	= $row[csf('dia_width_type')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['color_id'] 		= $row[csf('color_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['width'] 			= $row[csf('width')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['gsm'] 			= $row[csf('gsm')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['shelf_no'] 		= $row[csf('shelf_no')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['order_id'] 		= $row[csf('order_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['fab_desc_id'] 	= $row[csf('fabric_description_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['fabric_description'] 	= $row[csf('fabric_description')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['prod_id'] 		= $row[csf('prod_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['is_sales'] 		= $row[csf('is_sales')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['dtls_id'] 		= $row[csf('dtls_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['no_of_roll'] 		+= $row[csf('no_of_roll')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['body_part_id'] 	= $row[csf('body_part_id')];
		$data[$row[csf('knitting_source')]][$order_type][$uom][$prodId][$productionNo]['remarks'] 	= $row[csf('remarks')];
	}
	/*echo "<pre>";
	print_r($data);
	echo "</pre>";*/

	$knitting_company_id 	= implode(",", array_filter(array_unique(explode(",",chop($knitting_company_id,",")))));
	$location_id 		 	= implode(",", array_filter(array_unique(explode(",",chop($location_id,",")))));
	$color_id 			 	= implode(",", array_filter(array_unique(explode(",",chop($color_id,",")))));
	$machine_no_id 		 	= implode(",", array_filter(array_unique(explode(",",chop($machine_no_id,",")))));
	$buyer_id 			 	= implode(",", array_filter(array_unique(explode(",",chop($buyer_id,",")))));
	$floor 				 	= implode(",", array_filter(array_unique(explode(",",chop($floor,",")))));
	$batch_id 			 	= implode(",", array_filter(array_unique(explode(",",chop($batch_id,",")))));
	$shelf_no 			 	= implode(",", array_filter(array_unique(explode(",",chop($shelf_no,",")))));
	$fabric_description_id 	= implode(",", array_filter(array_unique(explode(",",chop($fabric_description_id,",")))));
	$salesOrder_id 			= implode(",", array_filter(array_unique(explode(",",chop($salesOrder_id,",")))));
	$sub_con_order 			= implode(",", array_filter(array_unique(explode(",",chop($sub_con_order,",")))));
	$po_id 					= implode(",", array_filter(array_unique(explode(",",chop($po_id,",")))));

	if($knitting_company_id!="")
	{
		$kinttin_companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0 and id in($knitting_company_id)" ,"id","company_short_name");

		$kinttin_supplierArr = return_library_array("select id,short_name from lib_supplier where status_active=1 and is_deleted=0 and id in($knitting_company_id)" ,"id","short_name");
	}

	if($location_id!="")
	{
		$location_arr=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0 and id in($location_id) and company_id in($knitting_company_id)",'id','location_name');
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
		$machinSql=sql_select("select id,machine_no,brand,floor_id from lib_machine_name where status_active=1 and is_deleted=0 and id in($machine_no_id) and location_id in($location_id) and company_id in($knitting_company_id)");
		foreach ($machinSql as $machine_row) {
			$machinArr[$machine_row[csf("id")]]["machine_name"]  = $machine_row[csf("machine_no")].(($machine_row[csf("brand")]!="")?"-".$machine_row[csf("brand")]:"");
			$machinArr[$machine_row[csf("id")]]["machine_floor"] = $machine_row[csf("floor_id")];
			$floor_id_arr[$machine_row[csf("floor_id")]] = $machine_row[csf("floor_id")];
		}

		if(!empty($floor_id_arr))
		{
			$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and id in(".implode(",",$floor_id_arr).") and location_id in($location_id) and company_id in($knitting_company_id)",'id','floor_name');
		}
	}

	if($shelf_no!="")
	{
		$shelf_Arr = return_library_array("select floor_room_rack_id as id,floor_room_rack_name as name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 and floor_room_rack_id in($shelf_no)","id","name");
	}

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in ($fabric_description_id)";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	if($sub_con_order!="")
	{
		$sub_con_data_arr=array();
		$sql_sub_con=sql_select("select a.subcon_job,b.id order_id,b.order_no,b.cust_style_ref,b.order_uom from subcon_ord_mst a,subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($sub_con_order)");
		foreach($sql_sub_con as $row)
		{
			$sub_con_data_arr[$row[csf('order_id')]]['subcon_job'] 		= $row[csf('subcon_job')];
			$sub_con_data_arr[$row[csf('order_id')]]['order_no'] 		= $row[csf('order_no')];
			$sub_con_data_arr[$row[csf('order_id')]]['cust_style_ref']	= $row[csf('cust_style_ref')];
			$sub_con_data_arr[$row[csf('order_id')]]['order_uom']		= $row[csf('order_uom')];
		}
	}

	if($po_id!="")
	{
		$po_data_arr=array();
		$sql_po=sql_select("select b.id, a.style_ref_no,b.b.po_number from wo_po_details_master a, wo_po_break_down b where b.job_no_mst=a.job_no and  a.status_active=1 and a.is_deleted=0 and b.id in($po_id)");
		foreach($sql_po as $row)
		{
			$po_data_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}

	if($batch_id!="")
	{
		$batch_no_arr=array();
		$batch_id_arr=array_unique( explode(",",$batch_id) );
		$batch_id_conds="";
		if(count($batch_id_arr)>999)
		{
			$chnk_arr=array_chunk($batch_id_arr, 999);
			foreach($chnk_arr as $vals)
			{
				$ids=implode(",",$vals);
				if($batch_id_conds)$batch_id_conds.=" or a.id in($ids) ";
				else $batch_id_conds.=" and (  a.id in($ids) ";
			}
			$batch_id_conds.=" ) ";
		}
		else
		{
			$batch_id_conds=" and a.id in($batch_id) ";
		}
		 
		$sql_batch=sql_select("SELECT a.id,sum(b.batch_qnty) as batch_qnty, COUNT(b.id) as no_of_roll_batch, a.batch_weight,a.total_trims_weight,a.sales_order_no,a.is_sales,a.batch_no,a.color_range_id,a.booking_no,a.extention_no,b.prod_id,b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 $batch_id_conds group by a.id,a.batch_weight,a.total_trims_weight,a.sales_order_no,a.is_sales,a.batch_no,a.color_range_id,a.booking_no,a.extention_no,b.prod_id,b.item_description" );
		// echo "SELECT a.id,sum(b.batch_qnty) as batch_qnty, COUNT(b.id) as count_batch, a.batch_weight,a.total_trims_weight,a.sales_order_no,a.is_sales,a.batch_no,a.color_range_id,a.booking_no,a.extention_no,b.prod_id,b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 $batch_id_conds group by a.id,a.batch_weight,a.total_trims_weight,a.sales_order_no,a.is_sales,a.batch_no,a.color_range_id,a.booking_no,a.extention_no,b.prod_id,b.item_description";
		foreach($sql_batch as $row)
		{
			$itemDescription=explode(",", $row[csf('item_description')]);
			$itemDescription=trim($itemDescription[0]).", ".trim($itemDescription[1]);

			$batch_no_arr[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batch_no_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
			$batch_no_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_no_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_no_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			$batch_no_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_no_arr[$row[csf('id')]]['sales_order_no']=$row[csf('sales_order_no')];
			$batch_no_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$booking_no_arr[] = "'".$row[csf('booking_no')]."'";
			$prod_id_arr[] = $row[csf("prod_id")];
			$batchNoArr[$row[csf('id')]][$itemDescription]['batch_qnty']+=$row[csf('batch_qnty')];

			$batch_no_arr[$row[csf('id')]]['no_of_roll_batch'] += $row[csf('no_of_roll_batch')];

		}
	}
	/*echo "select f.process_start_date,f.process_end_date,a.id from pro_batch_create_mst a, pro_fab_subprocess f where  f.batch_id=a.id and a.id in($batch_id) and f.entry_form=35 and f.load_unload_id=1 and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 ";*/
	$sql=sql_select("select f.process_start_date,f.process_end_date,a.id from pro_batch_create_mst a, pro_fab_subprocess f where  f.batch_id=a.id $batch_id_conds  and f.entry_form in (35,38) and f.load_unload_id=1 and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 ");

	$proce_start_date=array();
	foreach($sql as $p_date)
	{
		$proce_start_date[$p_date[csf('id')]]['process_start_date']=$p_date[csf('process_start_date')];
		$proce_start_date[$p_date[csf('id')]]['process_end_date']=$p_date[csf('process_end_date')];
	}
	/*echo "<pre>";
	print_r($proce_start_date);
	echo "</pre>";*/

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
		$booking_arr = array();
		$salesOrder_id_arr=array_unique( explode(",",$salesOrder_id) );
		$sales_id_conds="";
		if(count($salesOrder_id_arr)>999)
		{
			$chnk_arr=array_chunk($salesOrder_id_arr, 999);
			foreach($chnk_arr as $vals)
			{
				$ids=implode(",",$vals);
				if($sales_id_conds)$sales_id_conds.=" or a.id in($ids) ";
				else $sales_id_conds.=" and (  a.id in($ids) ";
			}
			$sales_id_conds.=" ) ";
		}
		else
		{
			$sales_id_conds=" and a.id in($salesOrder_id) ";
		}
		 


		$get_data_from_sales_order = sql_select("SELECT a.id,a.style_ref_no,a.sales_booking_no,a.booking_entry_form,b.determination_id,b.width_dia_type,b.color_id,b.gsm_weight,b.dia from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $sales_id_conds group by a.id,a.style_ref_no,a.sales_booking_no,a.booking_entry_form,b.determination_id,b.width_dia_type,b.color_id,b.gsm_weight,b.dia");
		foreach ($get_data_from_sales_order as $sale_row) {

			$sales_order_data[$sale_row[csf('width_dia_type')]][$sale_row[csf('color_id')]]['gsm']=$sale_row[csf('gsm_weight')];
			$sales_order_data[$sale_row[csf('width_dia_type')]][$sale_row[csf('color_id')]]['dia']=$sale_row[csf('dia')];
			$booking_arr[$sale_row[csf('sales_booking_no')]]="'".$sale_row[csf('sales_booking_no')]."'";

			$po_data_arr[$sale_row[csf('id')]]['style_ref_no']=$sale_row[csf('style_ref_no')];
			$po_data_arr[$sale_row[csf('id')]]['buyer']=$sale_row[csf('po_buyer')];
			$po_data_arr[$sale_row[csf('id')]]['booking_entry_form']=$sale_row[csf('booking_entry_form')];
		}

		if(!empty($booking_arr)){
			if($db_type==0){
				$booking_info = sql_select("select a.booking_no,a.booking_type,a.short_booking_type, group_concat(b.division_id) division_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no in(".implode(",",$booking_arr).") and a.status_active!=0 and a.is_deleted!=1 and b.status_active!=0 and b.is_deleted!=1 group by a.booking_no,a.booking_type,a.short_booking_type");
			}else{
				$booking_info = sql_select("select a.booking_no,a.booking_type,a.short_booking_type, listagg(b.division_id, ',') within group (order by b.division_id) as division_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no in(".implode(",",$booking_arr).") and a.status_active!=0 and a.is_deleted!=1 and b.status_active!=0 and b.is_deleted!=1 group by a.booking_no,a.booking_type,a.short_booking_type");
			}

			foreach ($booking_info as $booking_row) 
			{
				$booking_data[$booking_row[csf("booking_no")]]["division_id"] 	= $booking_row[csf("division_id")];
				$booking_data[$booking_row[csf("booking_no")]]["short_booking_type"] = $booking_row[csf("short_booking_type")];
			}
			/*echo "<pre>";
			print_r($booking_data);
			echo "</pre>";*/
		}
	}

	$machineArr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');

	ob_start();
	?>
	<div>
		<fieldset style="width:2920px;">
			<table width="2920px" cellpadding="0" cellspacing="0" id="caption" align="center">
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
			<div style="width:3160px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
					<thead style="width:3160px; position: sticky; top: 0;">
						<th width="40">SL</th>
						<th width="120">Production ID</th>
						<th width="80">Source</th>
						<th width="80">Finishing Company</th>
						<th width="80">Production Floor</th>
						<th width="80">M/C Name</th>
						<th width="80">Shift</th>
						<th width="80">Finishing Date</th>
						<th width="80">Dying Start Date</th>
						<th width="80">Execution Days</th>
						<th width="100">Buyer Name</th>
						<th width="80">Style Ref.</th>
						<th width="80">Booking/Job No</th>
						<th width="80">Booking Type</th>
						<th width="80">Short Booking Type</th>
						<th width="80">Division</th>
						<th width="80">FSO/Order No</th>
						<th width="80">Batch/Lot No</th>
						<th width="80">Extension</th>
						<th width="100">Body Part</th>
						<th width="100">Fabric Type</th>
						<th width="120">Fab. Composition</th>
						<!--<th width="80">Booking Dia</th>-->
						<th width="80">F. Dia</th>
						<th width="80">Dia Type</th>
						<!--<th width="80">Booking GSM</th>-->
						<th width="80">F. GSM</th>
						<th width="80">Fabric Color</th>
						<th width="80">Color Range</th>
						<th width="80">Fabric Weight</th>
						<th width="80">Trims Weight</th>
						<th width="80">Total Batch Weight</th> 
						<th width="80">No. Of Roll (Batch)</th>
						<th width="80">QC Pass Qty</th>
						<th width="80">Grey Used</th>
						<th width="80">Reject Qty</th> 
						<th width="80">Weight Loss</th> 
						<th width="80">No of Roll</th>
						<th width="80">Shelf</th>
						<th width="80">Remarks</th>
					</thead>
					<tbody style="width:3160px; overflow-y:scroll; max-height:350px;" >
						<?
						$i=1;
						$total_qc_qty = 0;
						$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
						foreach ($data as $kniting_source_key => $kniting_source_key_val) 
						{
							foreach($kniting_source_key_val as $order_type=>$orderData)
							{
								if ($kniting_source_key==1) 
								{
									?>
									<tr style="background-color:#ccc;">
										<td colspan="38"><strong><? echo ($order_type==0)?"Inhouse":"Inbound Subcontract";?></strong> </td>
									</tr>
									<?
								}
								else
								{
									?>
									<tr style="background-color:#ccc;">
										<td colspan="38"><strong><? echo "Outbound";?></strong> </td>
									</tr>
									<?
								}
								foreach($orderData as $uomId=>$productArr)
								{
									?>
									<tr style="background-color:#ccc;">
										<td colspan="38"><strong><? echo $unit_of_measurement[$uomId];?></strong> </td>
									</tr>
									<?
									foreach($productArr as $prodId=>$productionArr)
									{
										foreach($productionArr as $row)
										{
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

											if($row['is_sales']==1)
											{
												$booking_gsm = $sales_order_data[$row['dia_width_type']][$row['color_id']]['gsm'];
												$booking_dia = $sales_order_data[$row['dia_width_type']][$row['color_id']]['dia'];
											}

											if($row['knitting_source']==1)
											{
												$kinttin_company = $kinttin_companyArr[$row['knitting_company']];
											}else{
												$kinttin_company = $kinttin_supplierArr[$row['knitting_company']];
											}

											$division_ids = $booking_data[$batch_no_arr[$row['batch_id']]['booking_no']]["division_id"];
											//echo $division_ids;
											$division_name="";
											foreach (explode(',', $division_ids)  as $division) 
											{
												//echo $division.'Tipu';
												$division_name .= $short_division_array[$division].',';
											}
											$division_name = rtrim($division_name,",");
											?>
											<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<?echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
												<td width="40" align="center"><? echo $i;?> </td>
												<td width="120"><p><? echo $row['recv_number'];?></p> </td>
												<td width="80"><p><? echo $source_arr[$row['knitting_source']];?></p>  </td>
												<td width="80"><p><? echo $kinttin_company;?></p>  </td>
												<td width="80"><p><? echo $floor_arr[$machinArr[$row['machine_no_id']]["machine_floor"]];?></p>  </td>
												<td width="80"><p><? echo $machineArr[$row['machine_no_id']];?></p> </td>
												<td width="80" align="center"><p><? echo $shift_name[$row['shift_name']];?></p>  </td>
												<td width="80"><p><? echo change_date_format($row['receive_date']);?></p>  </td>

												<td width="80"><p><? if($proce_start_date[$row['batch_id']]['process_end_date']!="") echo change_date_format($proce_start_date[$row['batch_id']]['process_end_date']);?></p></td>

												<td width="80" align="center"><p><?  if($proce_start_date[$row['batch_id']]['process_end_date']!="") echo datediff("d",$proce_start_date[$row['batch_id']]['process_end_date'],$row['receive_date']);?></p></td>
												<td width="100" align="center"><p><? echo $buyer_Arr[$row['buyer_id']];?></p>  </td>
												<td width="80"><p><? echo ($order_type==0)?$po_data_arr[$row['order_id']]['style_ref_no']:$sub_con_data_arr[$row["order_id"]]['cust_style_ref']; ?></p>  </td>
												<td width="80"><p><? echo ($order_type==0)?$batch_no_arr[$row['batch_id']]['booking_no']:$sub_con_data_arr[$row["order_id"]]['subcon_job'];?></p>  </td>
												<td width="80"><p><? echo $booking_type_arr[$po_data_arr[$row['order_id']]['booking_entry_form']];?></p></td>
												<td width="80"><p><? echo $short_booking_type[$booking_data[$batch_no_arr[$row['batch_id']]['booking_no']]['short_booking_type']];?></p></td>
												<td width="80"><p><? echo $division_name;?></p></td>
												<td width="80" style="word-wrap: break-word;word-break: break-all;"><p><? echo ($order_type==0)?$batch_no_arr[$row['batch_id']]['sales_order_no']:$sub_con_data_arr[$row["order_id"]]['order_no'];?></p>  </td>
												<td width="80"> <p><? echo $batch_no_arr[$row['batch_id']]['batch_no'];?></p> </td>
												<td width="80" align="center"> <p><? echo $batch_no_arr[$row['batch_id']]['extention_no'];?></p> </td>
												<td width="100"><p><? echo $body_part[$row['body_part_id']] ; ?></p> </td>
												<?
												if ($kniting_source_key==1) 
												{
													if($order_type==1)
													{
														?>
														<td colspan="2" style="word-wrap: break-word;word-break: break-all; width: 220px;"><? echo ($order_type==0)? $fbType_1=$composition_arr[$row['fab_desc_id']]:$fbType_1=$row['fabric_description'];?></td>
														<?
													}
													else
													{
														?>
														<td width="100"><p><? echo $fbType_1=$constructtion_arr[$row['fab_desc_id']]; ?></p> </td>
														<td width="120" style="word-wrap: break-word;word-break: break-all;"><p><? echo ($order_type==0)?$fbType_2=$composition_arr[$row['fab_desc_id']]:$fbType_2=$row['fabric_description'];?></p></td>
														<?
													}
												}
												else
												{
													?>
													<td width="100"><p><? echo $fbType_1=$constructtion_arr[$row['fab_desc_id']]; ?></p> </td>
													<td width="120" style="word-wrap: break-word;word-break: break-all;"><p><? echo ($order_type==0)?$fbType_2=$composition_arr[$row['fab_desc_id']]:$fbType_2=$row['fabric_description'];?></p></td>
													<?
												}
												?>
												
												<!--<td width="80"  align="right"><p><? //echo $booking_dia; ?></p></td>-->
												<td width="80"  align="right" id="total_qc_qty"><p><?php echo $row['width'];?></p>  </td>
												<td width="80"><p><? echo $fabric_typee[$row['dia_width_type']]; ?></p> </td>
												<!--<td width="80"><p><? //echo $booking_gsm;?></p></td>-->
												<td width="80"><p><?php echo $row['gsm'];?></p></td>
												<td width="80"><p><?php echo $color_library[$row['color_id']];?></p></td>
												<td width="80"><p><?php echo $color_range[$batch_no_arr[$row['batch_id']]['color_range_id']];?></p></td>
												<td width="80" align="right"><p>
													<? 
														//echo number_format($batch_no_arr[$row['batch_id']]['batch_qnty'],2);
														$fabriceDesc=trim($fbType_1).", ".trim($fbType_2);
														echo number_format($batchNoArr[$row['batch_id']][trim($fabriceDesc)]['batch_qnty'],2);
													?>
												</p></td>
												<td width="80" align="right"><p><? echo $batch_no_arr[$row['batch_id']]['total_trims_weight'];?></p></td>
												<td width="80" align="right"><p><? echo $batch_no_arr[$row['batch_id']]['batch_weight'];?></p></td>
												<td width="80" align="right"><p><? echo $batch_no_arr[$row['batch_id']]['no_of_roll_batch'];?></p></td>
												<td width="80" align="right"><p><? echo number_format($row['qc_pass_qty'],2);?></p></td>
												<td width="80" align="right"><p><? echo number_format($row['grey_used_qty'],2);?></p></td>
												<td width="80" align="right"><p><? echo number_format($row['reject_qty'],2);?></p></td>
												<td width="80" align="right"><p><? $weight_loss = ($row['grey_used_qty'] - $row['qc_pass_qty']) - $row['reject_qty']; echo number_format($weight_loss ,2);?></p></td>
												<td width="80" align="center"><p><?php echo $row['no_of_roll'];?></p></td>
												<td width="80"><p><?php echo $shelf_Arr[$row['shelf_no']];?></p></td>
												<td width="80"><p><?php echo $row['remarks'];?></p></td>
											</tr>
											<?
											$total_fab_qty+=$batch_no_arr[$row['batch_id']]['batch_qnty'];
											$total_trims_qty+=$batch_no_arr[$row['batch_id']]['total_trims_weight'];
											$total_batch_qty+=$batch_no_arr[$row['batch_id']]['batch_weight'];
											$total_qc_qty+=$row['qc_pass_qty'];
											$total_rj_qty+=$row['reject_qty'];

											$total_unit_wise_qc_qty[$kniting_source_key][$order_type][$uomId] += $row['qc_pass_qty'];
											$total_unit_wise_grey_used_qty[$kniting_source_key][$order_type][$uomId] += $row['grey_used_qty'];
											$total_unit_wise_rj_qty[$kniting_source_key][$order_type][$uomId] += $row['reject_qty'];
											$total_unit_wise_role_qty[$kniting_source_key][$order_type][$uomId] += $row['no_of_roll'];

											$i++;
										}
									}
									?>
									<tr style="background-color:#ccc;">
										<td colspan="31"> <strong>Sub Total Of  <?php echo $unit_of_measurement[$uomId];?></strong> </td>
										<td align="right"><p> <strong><?php echo number_format($total_unit_wise_qc_qty[$kniting_source_key][$order_type][$uomId],2); ?></strong></p></td>
										<td align="right"><p> <strong><?php echo number_format($total_unit_wise_grey_used_qty[$kniting_source_key][$order_type][$uomId],2); ?></strong></p></td>
										<td align="right"><p> <strong><?php echo number_format($total_unit_wise_rj_qty[$kniting_source_key][$order_type][$uomId],2); ?></strong></p></td>
										<td align="center"><p><strong><?php echo $total_unit_wise_role_qty[$order_type][$kniting_source_key][$uomId]; ?></strong></p></td>
										<td><p>&nbsp;</p></td>
										<td><p>&nbsp;</p></td>
										<td><p>&nbsp;</p></td>
									</tr>
									<?
								}
							}

						}
						?>
					</tbody>
				<!-- </table>
				<table class="rpt_table" width="3080" cellpadding="0" cellspacing="0" border="1" rules="all"> 
					<tfoot>
						<tr>
							<th width="40"><p>&nbsp;</p></th>
							<th width="120"><p>&nbsp;</p></th>
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
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="100"><p>&nbsp;</p></th>
							<th width="100"><p>&nbsp;</p></th>
							<th width="120"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;<? //echo number_format($total_fab_qty,2); ?></p></th>
							<th width="80"><p>&nbsp;<? //echo number_format($total_trims_qty,2); ?></p></th>
							<th width="80"><p>&nbsp;<? //echo number_format($total_batch_qty,2); ?></p></th>
							<th width="80"><p><? //echo number_format($total_qc_qty,2); ?></p></th>
							<th width="80"><p><? //echo number_format($total_rj_qty,2); ?></p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p></th>
							<th width="80"><p>&nbsp;</p>  </th>
						</tr>
					</tfoot>-->
				</table>
			</div>
		</fieldset>
	</div>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}

?>