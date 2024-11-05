<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_lib_arr=return_library_array( "SELECT id, company_short_name from lib_company where status_active=1 and is_deleted=0  ",'id','company_short_name');
$color_arr 		= return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
$body_part_arr 		= return_library_array("SELECT id, body_part_full_name from lib_body_part where status_active=1 and is_deleted=0", 'id', 'body_part_full_name');


//====================Location ACTION========

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "load_drop_down( 'requires/batch_wise_fabric_process_loss_report_controller',this.value+'_'+$('#cbo_company_id').val(), 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor_id", 100, "select id, floor_name from lib_prod_floor where location_id=$data[0] and company_id=$data[1] and production_process=4 and status_active=1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/batch_wise_fabric_process_loss_report_controller',this.value+'_'+$('#cbo_company_id').val(), 'load_drop_down_machine', 'machine_td');","" );
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
	echo create_drop_down( "cbo_buyer_id", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
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


			show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.		getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'batch_wise_fabric_process_loss_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');
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
	                               echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'batch_wise_fabric_process_loss_report_controller',this.value, 'load_drop_down_buyer','buyer_td');" );
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
	echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch weight ", "100,100,100,100,70","520","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_weight", "batch_wise_fabric_process_loss_report_controller",'setFilterGrid("list_view",-1);','0') ;
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
							$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.",4=>"Batch No.");
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year; ?>, 'create_sales_order_no_search_list_view', 'search_div', 'batch_wise_fabric_process_loss_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
		if($search_by==1) $search_field_cond=" and a.job_no like '%".$search_string."%'";
		else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."%'";
		else  if($search_by==3)  $search_field_cond=" and a.style_ref_no like '%".$search_string."%'";
		else  if($search_by==4)  $search_field_cond=" and b.batch_no like '%".$search_string."%'";
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
	else $year_field="";
	if($search_by==4)
	{
		$sql = "SELECT a.po_buyer, b.batch_no,b.extention_no, a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a,pro_batch_create_mst b  where a.sales_booking_no=b.booking_no and b.status_active=1 and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
		$style_width="110";

	}
	else
	{
		$sql = "SELECT a.po_buyer,   a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a   where     a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
		$style_width="";
	}

	

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
			<th width="<? echo $style_width;?>">Style Ref.</th>
			<?
			if($search_by==4)
			{
				?>
				<th width="80">Batch No.</th>
				<th>Ext. No.</th>
				<?
			}
			?>

			
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
					$buyer=$buyer_arr[$row[csf('po_buyer')]];
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
					 
					<td align="center" width="<? echo $style_width;?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<?
				if($search_by==4)
				{
					?>

					<td width="80"><p><? echo $row[csf('batch_no')]; ?></p></td>
					<td><p><? echo $row[csf('extention_no')]; ?></p></td>
					<?
				}
					?>
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
	$time_start = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$category=str_replace("'","",$cbo_item_cat);
	$po_company_id=str_replace("'","",$cbo_po_company_id); 
	$within_group =str_replace("'","",$cbo_within_group);
	$buyer_name=str_replace("'","",$cbo_buyer_id);
	$year=str_replace("'","",$cbo_year);

	$dynamic_search=str_replace("'","",$txt_dynamic_search);
	$dynamic_id = str_replace("'","",$hide_dynamic_id); 
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$rpt_type=str_replace("'","",$rpt_type);
	$search_by = str_replace("'","",$cbo_search_by);
	$year_selection = str_replace("'","",$cbo_year_selection);
	$txt_dynamic_search = str_replace("'","",$txt_dynamic_search);
	$cbo_batch_status = str_replace("'","",$cbo_batch_status);

	$condition_string="";
	$condition_string.=($within_group)?" and c.within_group=$within_group": "";
	$condition_string.=($po_company_id)?" and c.po_company_id=$po_company_id": "";
	$condition_string.=($buyer_name)?" and c.po_buyer=$buyer_name": "";
	$condition_string.=($year)?" and to_char(c.insert_date,'YYYY')=$year": ""; 
	$condition_string.=($dynamic_id)?" and c.id=$dynamic_id": ""; 
	$condition_string.=($txt_dynamic_search)?" and c.job_no_prefix_num=$txt_dynamic_search": ""; 

	if($db_type==0)
	{
		if($date_from!=="" and $date_to!=="")
		{
			$date_from=change_date_format($date_from,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
			//$date_cond=" and a.receive_date between '$date_from' and '$date_to'";
			$date_cond=" and a.receive_date <= '$date_to'";
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
			//$date_cond=" and a.receive_date between '$date_from' and '$date_to'";
			$date_cond=" and a.receive_date <= '$date_to'";
			$date_cond2=" and a.product_date between '$date_from' and '$date_to'";
		}else {
			if($year_selection>0)
			{
				$production_year_condition =" and to_char(a.insert_date,'YYYY')=$year_selection";
			}
		}
	}

	
	if($buyer_name==0)
	{
		$buyer_cond=$buyer_cond2="";
	}
	else
	{
		//$buyer_cond="and b.buyer_id='$buyer_name'";
		$buyer_cond2="and a.party_id='$buyer_name'";
	}
  

	$source_arr=array(1=>'Inhouse',2=>'Inbound Subcontract',3=>'Outbound');
	if($category==2)
	{
		$status_cond = ($cbo_batch_status==2) ? " and b.batch_status=0 " : "";
		$sql_dtls="SELECT a.recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id,a.fabric_nature,b.id as dtls_id,b.prod_id, cast(b.batch_id as NVARCHAR2 (25)) batch_id, d.batch_no, d.extention_no, b.body_part_id, b.fabric_description_id,cast('' as NVARCHAR2 (25)) fabric_description,b.gsm, b.width, b.color_id, b.receive_qnty as qc_pass_qty, b.reject_qty, b.no_of_roll, cast(b.order_id as NVARCHAR2 (25)) order_id, b.buyer_id, b.machine_no_id, b.rack_no, b.shelf_no, b.batch_status, b.shift_name, b.roll_id, b.roll_no, b.barcode_no, b.dia_width_type, b.room, b.production_qty, b.process_id, b.rate, b.amount, b.dyeing_charge, b.uom, b.fabric_shade, b.is_sales, b.floor, b.original_gsm, b.original_width, 0 as order_type, to_char(b.remarks) as remarks 
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, fabric_sales_order_mst c, pro_batch_create_mst d
		where  a.id=b.mst_id and b.batch_id = d.id and b.order_id =  CAST (c.id AS VARCHAR2(4000))  and a.company_id=$company_id and c.status_active=1 and a.entry_form in(7,66) and a.item_category=2 and a.status_active=1 $date_cond $production_year_condition $condition_string $location_cond $searchByCondition $sourcCond $machine_cond $floorMachinCond $buyer_cond $status_cond and a.is_deleted=0 and b.uom !=0 order by d.batch_no, d.extention_no asc";

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

			$batchNoAndExtention = $row[csf('batch_no')]."**".$row[csf('extention_no')];

			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['recv_number'] 	.= $productionNo.",";
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['reject_qty'] 		= $row[csf('reject_qty')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['knitting_source'] = $row[csf('knitting_source')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['knitting_company']= $row[csf('knitting_company')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['floor'] 			= $row[csf('floor')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['machine_no_id'] 	= $row[csf('machine_no_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['shift_name'] 		= $row[csf('shift_name')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['receive_date'] 	= $row[csf('receive_date')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['buyer_id'] 		= $row[csf('buyer_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['batch_id'] 		= $row[csf('batch_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['batch_no'] 		= $row[csf('batch_no')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['fabric_shade'] 	= $row[csf('fabric_shade')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['dia_width_type'] 	= $row[csf('dia_width_type')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['color_id'] 		= $row[csf('color_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['width'] 			= $row[csf('width')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['gsm'] 			= $row[csf('gsm')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['shelf_no'] 		= $row[csf('shelf_no')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['order_id'] 		= $row[csf('order_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['fab_desc_id'] 	= $row[csf('fabric_description_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['fabric_description'] 	= $row[csf('fabric_description')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['prod_id'] 		= $row[csf('prod_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['is_sales'] 		= $row[csf('is_sales')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['dtls_id'] 		= $row[csf('dtls_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['no_of_roll'] 		= $row[csf('no_of_roll')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['body_part_id'] 	= $row[csf('body_part_id')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['remarks'] 	= $row[csf('remarks')];
			$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['batch_status'] 	= $row[csf('batch_status')];


			if( $row[csf('batch_status')]==0)
			{
				$incomple_batch_arr[$row[csf('knitting_source')]][$order_type][$uom][$row[csf('batch_no')]]=$row[csf('batch_no')];
			}


			$date_frm=date('Y-m-d',strtotime($date_from));
			$transaction_date=date('Y-m-d',strtotime($row[csf('receive_date')]));

			if($transaction_date >= $date_frm)
			{
				$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['qc_pass_qty'] += $row[csf('qc_pass_qty')];
			}
			else
			{
				$data[$row[csf('knitting_source')]][$order_type][$uom][$batchNoAndExtention][$prodId][$row[csf('body_part_id')]]['open_qc_pass_qty'] 	+= $row[csf('qc_pass_qty')];
			}



		}
		/*echo "<pre>";
		print_r($data[1][0][12]);
		echo "</pre>";
		die;*/

		$knitting_company_id 	= implode(",", array_filter(array_unique(explode(",",chop($knitting_company_id,",")))));
		$location_id 		 	= implode(",", array_filter(array_unique(explode(",",chop($location_id,",")))));
		//$color_id 			 	= implode(",", array_filter(array_unique(explode(",",chop($color_id,",")))));
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

		if($buyer_id!="")
		{
			$buyer_Arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 and id in($buyer_id)","id","buyer_name");
		}

		/* if($color_id!="")
		{
			$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 and id in($color_id)", "id", "color_name");
		} */

		if($machine_no_id!="")
		{
			if(!empty($floor_id_arr))
			{
				$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and id in(".implode(",",$floor_id_arr).") and location_id in($location_id) and company_id in($knitting_company_id)",'id','floor_name');
			}
		}

		$composition_arr = array();
		$constructtion_arr = array();
		$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";  // and a.id in ($fabric_description_id)

		$deter_array = sql_select($sql_deter);
		foreach ($deter_array as $row) {
			$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
			$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}

		/* if($sub_con_order!="")
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
		} */

		$con = connect();
		$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (1090)");
		if($r_id)
		{
			oci_commit($con);
		}
		if($batch_id!="")
		{
			$all_batchId_arr=explode(",",$batch_id);

			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 1090, 1,$all_batchId_arr, $empty_arr);//batch ID

			$batch_no_arr=array();$body_part_wise_batch_arr=array();
			$sql_batch=sql_select("SELECT b.body_part_id, a.id,sum(b.batch_qnty) as batch_qnty, a.batch_weight, a.total_trims_weight, a.sales_order_no, a.is_sales, a.batch_no, a.color_range_id, a.booking_no, a.extention_no, b.prod_id
			from pro_batch_create_mst a, pro_batch_create_dtls b, GBL_TEMP_ENGINE g 
			where a.id=b.mst_id and a.id=g.ref_val and g.user_id=$user_name and g.entry_form=1090 and g.ref_from=1 and a.status_active=1 and a.is_deleted=0 
			group by b.body_part_id,a.id,a.batch_weight,a.total_trims_weight,a.sales_order_no, a.is_sales,a.batch_no,a.color_range_id, a.booking_no, a.extention_no, b.prod_id");
			//$batch_conds 

			foreach($sql_batch as $row)
			{
				$body_part_wise_batch_arr[$row[csf('id')]][$row[csf('body_part_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
				$batch_no_arr[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
				$batch_no_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
				$batch_no_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
				$batch_no_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
				$batch_no_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
				$batch_no_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
				$batch_no_arr[$row[csf('id')]]['sales_order_no']=$row[csf('sales_order_no')];
				$batch_no_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				//$booking_no_arr[] = "'".$row[csf('booking_no')]."'";
				//$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];

			}
		}

		if($salesOrder_id!="")
		{
			$booking_arr = array();
			$all_po_arr=explode(",",$salesOrder_id);

			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 1090, 3,$all_po_arr, $empty_arr);//sales ID
			$get_data_from_sales_order = sql_select("SELECT a.id,a.style_ref_no,a.sales_booking_no,a.booking_entry_form,b.determination_id,b.width_dia_type,b.color_id,b.gsm_weight,b.dia from fabric_sales_order_mst a,fabric_sales_order_dtls b, GBL_TEMP_ENGINE g where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id=g.ref_val and g.user_id=$user_name and g.entry_form=1090 and g.ref_from=3 group by a.id,a.style_ref_no, a.sales_booking_no, a.booking_entry_form, b.determination_id, b.width_dia_type,b.color_id, b.gsm_weight,b.dia"); //$po_conds
			
			foreach ($get_data_from_sales_order as $sale_row) 
			{
				$sales_order_data[$sale_row[csf('width_dia_type')]][$sale_row[csf('color_id')]]['gsm']=$sale_row[csf('gsm_weight')];
				$sales_order_data[$sale_row[csf('width_dia_type')]][$sale_row[csf('color_id')]]['dia']=$sale_row[csf('dia')];
				$booking_arr[$sale_row[csf('sales_booking_no')]]="'".$sale_row[csf('sales_booking_no')]."'";

				$po_data_arr[$sale_row[csf('id')]]['style_ref_no']=$sale_row[csf('style_ref_no')];
				$po_data_arr[$sale_row[csf('id')]]['buyer']=$sale_row[csf('po_buyer')];
				$po_data_arr[$sale_row[csf('id')]]['booking_entry_form']=$sale_row[csf('booking_entry_form')];
			}
		}

		$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (1090)");
		if($r_id)
		{
			oci_commit($con);
		}

		ob_start();
		?>
		<style type="text/css">
			.wordwrap{
				word-wrap: break-word;word-break: break-all;
			}

		</style>
		<div>
			<fieldset style="width:2880px;">
				<table width="2880px" cellpadding="0" cellspacing="0" id="caption" align="center">
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
				 
				<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="2850" align="left" >
					<thead>
						<tr>
							<th width="30">SL</th> 
							<th width="100">Source</th>
							<th width="100">Finishing Company</th>
							<th width="100">Production Floor</th>
							<th width="100">Buyer Name</th>
							<th width="100">Style Ref.</th>
							<th width="100">Booking/Job No</th>
							<th width="100">Booking Type</th>
							<th width="100">FSO/Order No</th>
							<th width="100">Batch/Lot No</th>
							<th width="100">Extension</th> 
							<th width="100">Body Part</th>
							<th width="100">Fabric Type</th>
							<th width="120">Fab. Composition</th> 
							<th width="100">F. Dia</th>
							<th width="100">Dia Type</th>
							<th width="100">F. GSM</th>
							<th width="100">Fabric Color</th>
							<th width="100">Color Range</th>
							<th width="100">Fabric Weight</th>
							<th width="100">Trims Weight</th>
							<th width="100">Total Batch Weight</th>
							<th width="100">Previous QC Pass Qty</th>
							<th width="100">QC Pass Qty</th>
							<th width="100">Total QC Pass Qty</th>
							<th width="100">Reject Qty</th>
							<th width="100">Yds To Kg</th>
							<th width="100">Process Loss %</th>
							<th width="100">Process loss % With Reprocess</th>
							<th width="100">No of Roll</th>
							<th width="100">Batch status</th>
						</tr>
					</thead>
				</table>
				<div style="width:2880px; max-height:540px; overflow-y:scroll"  align="left" id="scroll_body">
					<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="2850" >
						<tbody>
							<?
							$batch_wise_loss_per=array();
							$batch_wise_uom=array();


							foreach ($data as $kniting_source_key => $kniting_source_key_val) 
							{
								foreach($kniting_source_key_val as $order_type=>$orderData)
								{
									foreach($orderData as $uomId=>$uomData)
									{
										foreach($uomData as $batchNoExt=>$batchNoExtData)
										{
											foreach($batchNoExtData as $prodId=>$productionArr)
											{
												foreach($productionArr as $bodyPartId=> $row)
												{
													$batchNoExtion = explode("**", $batchNoExt);

													$batch_no = $batchNoExtion[0];
													$batch_ext = $batchNoExtion[1];

													if($cbo_batch_status==0 || $cbo_batch_status==2 || ($cbo_batch_status==1 && $incomple_batch_arr[$kniting_source_key][$order_type][$uomId][$batch_no]==""))
													{
														
														$fabric_weight=$body_part_wise_batch_arr[$row['batch_id']][$bodyPartId]['batch_qnty'];

														if($uomId==27)
														{
															$yds_to_kg=($row['width']* $row['gsm']*$row['qc_pass_qty']*36)/1550000;
														}
														else 
														{
															$yds_to_kg="";
														}

														if($yds_to_kg)
														{
															$process_loss=($fabric_weight-($yds_to_kg+$row['reject_qty']))/$fabric_weight;
														}
														else
														{
															$process_loss=($fabric_weight-($row['qc_pass_qty']+$row['reject_qty']))/$fabric_weight;
														}
														$process_loss = $process_loss*100;

														if($batch_count_chk_arr[$kniting_source_key][$order_type][$uomId][$batchNoExt]["chk"] == "")
														{
															$batch_count_chk_arr[$kniting_source_key][$order_type][$uomId][$batchNoExt]["chk"] = $row['batch_id'];
															$batch_count_arr[$kniting_source_key][$order_type][$uomId][$batch_no]['batch_count']++;
														}
														$batch_count_row_arr[$kniting_source_key][$order_type][$uomId][$batch_no]['row_count']++;

														$batch_count_arr[$kniting_source_key][$order_type][$uomId][$batch_no]["process_loss"] += $process_loss;

														$batch_wise_loss_per[$batch_no]["loss"]+=$process_loss;
														$batch_wise_loss_per[$batch_no]["count"]+=1;
														if($batch_ext)
														$batch_wise_loss_per[$batch_no]["ext"] =$batch_ext;

														$batch_wise_uom[$row['batch_id']]["uom"] .=($batch_wise_uom[$row['batch_id']]["uom"] )?','.$uomId : $uomId;
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['recv_number'] 	.= $row['recv_number'].",";
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['qc_pass_qty'] += $row['qc_pass_qty'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['open_qc_pass_qty'] += $row['open_qc_pass_qty'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['reject_qty'] 	= $row['reject_qty'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['knitting_source'] = $row['knitting_source'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['knitting_company']= $row['knitting_company'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['floor'] = $row['floor'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['machine_no_id'] = $row['machine_no_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['shift_name'] = $row['shift_name'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['receive_date'] = $row['receive_date'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['buyer_id'] = $row['buyer_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['batch_id'] = $row['batch_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['batch_no'] = $row['batch_no'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['fabric_shade'] = $row['fabric_shade'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['dia_width_type'] 	= $row['dia_width_type'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['color_id'] = $row['color_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['width'] = $row['width'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['gsm'] = $row['gsm'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['shelf_no'] = $row['shelf_no'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['order_id'] = $row['order_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['fab_desc_id'] = $row['fab_desc_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['fabric_description'] 	= $row['fabric_description'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['prod_id'] = $row['prod_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['is_sales'] = $row['is_sales'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['dtls_id'] = $row['dtls_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['no_of_roll'] 	= $row['no_of_roll'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['body_part_id'] = $row['body_part_id'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['remarks'] = $row['remarks'];
														$main_array[$kniting_source_key][$order_type][$uomId][$batchNoExt][$prodId][$bodyPartId]['batch_status'] = $row['batch_status'];

														$batch_sum_array[$kniting_source_key][$order_type][$uomId][$batch_no]['qc_pass_qty'] += $row['qc_pass_qty'];

														if($uomId==27)
														{
															$yds_to_kg=( ($row['width']* $row['gsm']*$row['qc_pass_qty']*36)/1550000  + ($row['width']* $row['gsm']*$row['open_qc_pass_qty']*36)/1550000);
														}
														else 
														{
															$yds_to_kg="";
														}

														$batch_sum_array[$kniting_source_key][$order_type][$uomId][$batch_no]['yds_to_kg'] += $yds_to_kg*1;
													

													}

												}

											}
										}
									}
								}
							}
							//echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);die;
							$i=1;
							$total_qc_qty = 0;
							$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
							foreach ($main_array as $kniting_source_key => $kniting_source_key_val) 
							{
								foreach($kniting_source_key_val as $order_type=>$orderData)
								{
									
									foreach($orderData as $uomId=>$uomData)
									{
										?>
										<tr style="background-color:#4a7570;">
											<td colspan="32"><strong><? echo $unit_of_measurement[$uomId];?></strong> </td>
										</tr>
										<?
										foreach($uomData as $batchNoExt=>$batchNoExtData)
										{
											foreach($batchNoExtData as $prodId=>$productionArr)
											{
												foreach ($productionArr as $bodyPartId=>$row) 
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


													$batchNoExtion =explode("**", $batchNoExt);
													?>
													<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<?echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
														<td width="30"><? echo $i;?> </td>
														<td width="100"><p><? echo $source_arr[$row['knitting_source']];?></p>  </td>
														<td width="100"><p><? echo $kinttin_company;?></p>  </td>
														<td width="100"><p><? echo $floor_arr[$machinArr[$row['machine_no_id']]["machine_floor"]];?></p></td>
														<td width="100"><p><? echo $buyer_Arr[$row['buyer_id']];?></p>  </td>
														<td width="100"><p><? echo ($order_type==0)?$po_data_arr[$row['order_id']]['style_ref_no']:$sub_con_data_arr[$row["order_id"]]['cust_style_ref']; ?></p>  </td>
														<td width="100"><p><? echo ($order_type==0)?$batch_no_arr[$row['batch_id']]['booking_no']:$sub_con_data_arr[$row["order_id"]]['subcon_job'];?></p>  </td>
														<td width="100"><p><? echo $booking_type_arr[$po_data_arr[$row['order_id']]['booking_entry_form']];?></p></td>
														<td width="100" class="wordwrap"><p><? echo ($order_type==0)?$batch_no_arr[$row['batch_id']]['sales_order_no']:$sub_con_data_arr[$row["order_id"]]['order_no'];?></p>  </td>
														<td width="100"><p><? echo $batch_no=$batch_no_arr[$row['batch_id']]['batch_no'];?></p> </td>
														<td width="100"><p><? echo $batch_no_arr[$row['batch_id']]['extention_no'];?></p> </td>
														<td width="100"><p><? echo $body_part[$bodyPartId]; ?></p> </td>
														<?
														if ($kniting_source_key==1) 
														{
															if($order_type==1)
															{
																?>
																<td colspan="2" align="center" class="wordwrap"><? echo ($order_type==0)?$composition_arr[$row['fab_desc_id']]:$row['fabric_description'];?></td>
																<?
															}
															else
															{
																?>
																<td align="center" width="100"><p><? echo $constructtion_arr[$row['fab_desc_id']]; ?></p> </td>
																<td  align="center" width="120" class="wordwrap"><p><? echo ($order_type==0)?$composition_arr[$row['fab_desc_id']]:$row['fabric_description'];?></p></td>
																<?
															}
														}
														else
														{
															?>
															<td width="100" class="wordwrap"><? echo $constructtion_arr[$row['fab_desc_id']]; ?></td>
															<td width="120" class="wordwrap"><p><? echo ($order_type==0)?$composition_arr[$row['fab_desc_id']]:$row['fabric_description'];?></p></td>
															<?
														}
														$curr_batch_id=$row['batch_id'];
														$fabric_weight=$body_part_wise_batch_arr[$row['batch_id']][$bodyPartId]['batch_qnty'];
														$batch_wise_uom_st=$batch_wise_uom[$curr_batch_id]["uom"] ; 
														$batch_wise_uom_arr= array_unique(explode(",", $batch_wise_uom_st));
														$multi_uom=0;
														if(count($batch_wise_uom_arr)>1) $multi_uom=1;
														if($multi_uom==1)
														{
															
															if($uomId==12 && !$already_fill_in_chk[$curr_batch_id] )
															{

																$total_trims_weight=$batch_no_arr[$curr_batch_id]['total_trims_weight'];
																$already_fill_in_chk[$curr_batch_id]=420;
															}
															else
																$total_trims_weight=0;
														}
														else
														{
															if(  !$already_fill_in_chk[$curr_batch_id] )
															{
																 
																$total_trims_weight=$batch_no_arr[$curr_batch_id]['total_trims_weight'];
																$already_fill_in_chk[$curr_batch_id]=420;
															}
															else
																$total_trims_weight=0;
															 
														}
														$total_batch_weight=$fabric_weight+$total_trims_weight;
														?>														 
														<td width="100"><? echo $row['width'];?></td>
														<td width="100"><p><? echo $fabric_typee[$row['dia_width_type']]; ?></p> </td>
														<td width="100"><? echo $row['gsm'];?></td>
														<td width="100" class="wordwrap"><? echo $color_arr[$row['color_id']];?></td>
														<td width="100"><p><? echo $color_range[$batch_no_arr[$curr_batch_id]['color_range_id']];?></p></td>
														<? 
														$row_count = $batch_count_row_arr[$kniting_source_key][$order_type][$uomId][$batch_no]['row_count'];
														if($batch_row_chk[$kniting_source_key][$order_type][$uomId][$batch_no] == "")
														{
															$total_qc_pass_qty =$total_yds_to_kg=0;
															$total_qc_pass_qty +=$row['open_qc_pass_qty'] + $batch_sum_array[$kniting_source_key][$order_type][$uomId][$batch_no]['qc_pass_qty'];

															$total_yds_to_kg += $batch_sum_array[$kniting_source_key][$order_type][$uomId][$batch_no]['yds_to_kg'];
														?>
														<td width="100" align="right" rowspan="<? echo $row_count;?>"><p><? echo number_format($fabric_weight,2);?></p></td>
														<td width="100" align="right" rowspan="<? echo $row_count;?>"><p><? echo number_format($total_trims_weight,2);?></p></td>
														<td width="100" align="right" rowspan="<? echo $row_count;?>"><p><? echo number_format($total_batch_weight,2);?></p></td>
														<td width="100" align="right" rowspan="<? echo $row_count;?>"><p><? echo number_format($row['open_qc_pass_qty'],2);?></p></td>
														<?
														$total_unit_wise_open_tot_qty[$kniting_source_key][$order_type][$uomId] += $row['open_qc_pass_qty'];
														}
														?>
														<td width="100" align="right"><p><? echo number_format($row['qc_pass_qty'],2);?></p></td>
														<?
														
														if($batch_row_chk[$kniting_source_key][$order_type][$uomId][$batch_no] == "")
														{
														?>
														<td width="100" align="right" rowspan="<? echo $row_count;?>">
															<p>
																<? echo number_format($total_qc_pass_qty,2);?>
															</p>
														</td>
														<?
														$total_unit_wise_qc_tot_qty[$kniting_source_key][$order_type][$uomId] += $total_qc_pass_qty;
														}
														?>
														<td width="100" align="right"><p><? echo number_format($row['reject_qty'],2);?></p></td>
														<td width="100" align="right">
														
															<?
															if($uomId==27)
															{
																$yds_to_kg=($row['width']* $row['gsm']*$row['qc_pass_qty']*36)/1550000;
															}
															else 
															{
																$yds_to_kg="";
															}
															 echo number_format($yds_to_kg,2);
															?>
														</td>

														<?
														
														if($batch_row_chk[$kniting_source_key][$order_type][$uomId][$batch_no] == "")
														{
														?>
															<td width="100" align="right" rowspan="<? echo $row_count;?>"><p>
															<?
															$process_loss = ($total_batch_weight - ($total_yds_to_kg + $total_qc_pass_qty +$row['reject_qty']))/$total_batch_weight;
															$process_loss = $process_loss*100;

																echo number_format($process_loss,2);
															?>
															</p>
															</td>
															<?
														}
														
														if($batch_row_chk[$kniting_source_key][$order_type][$uomId][$batch_no] == "")
														{
															$batch_row_chk[$kniting_source_key][$order_type][$uomId][$batch_no] = $batch_no;
															$batch_count = $batch_count_arr[$kniting_source_key][$order_type][$uomId][$batch_no]['batch_count'];
															$tot_process_loss = $batch_count_arr[$kniting_source_key][$order_type][$uomId][$batch_no]['process_loss'];
															$process_loss_with_reprocess = $tot_process_loss/$batch_count;

															?>
															<td width="100" align="right" rowspan="<? echo $row_count;?>">
																<p>
																	<?
																		echo number_format($process_loss_with_reprocess,2);
																	?>
																</p>
															</td>
															<?
														}
														?>

														<td width="100"><? echo $row['no_of_roll'];?></td>
														<td width="100"><? echo $batch_status_array[$row['batch_status']];?></td>
													</tr>
													<?
													$total_fab_qty+=$batch_no_arr[$row['batch_id']]['batch_qnty'];
													$total_trims_qty+=$batch_no_arr[$row['batch_id']]['total_trims_weight'];
													$total_batch_qty+=$batch_no_arr[$row['batch_id']]['batch_weight'];
													$total_qc_qty+=$row['qc_pass_qty'];
													$total_rj_qty+=$row['reject_qty'];

													
													$total_unit_wise_qc_qty[$kniting_source_key][$order_type][$uomId] += $row['qc_pass_qty'];
													$total_unit_wise_rj_qty[$kniting_source_key][$order_type][$uomId] += $row['reject_qty'];
													$total_unit_wise_role_qty[$kniting_source_key][$order_type][$uomId] += $row['no_of_roll'];

													$i++;
												}
											}
										}
										?>
										<tr style="background-color:#88a2f2;">
											<td colspan="22"> <strong>Sub Total Of  <?php echo $unit_of_measurement[$uomId];?></strong> </td>
											<td align="right"><p> <strong><?php echo number_format($total_unit_wise_open_tot_qty[$kniting_source_key][$order_type][$uomId],2); ?></strong></p></td>
											<td align="right"><p> <strong><?php echo number_format($total_unit_wise_qc_qty[$kniting_source_key][$order_type][$uomId],2); ?></strong></p></td>
											<td align="right">
												<p>
													<strong>
													<? echo number_format($total_unit_wise_qc_tot_qty[$kniting_source_key][$order_type][$uomId],2); 
													?>
												</strong>
												</p>
											</td>
											<td align="right"><p> <strong><?php echo number_format($total_unit_wise_rj_qty[$kniting_source_key][$order_type][$uomId],2); ?></strong></p></td>
											<td><p>&nbsp;</p></td>
											<td><p>&nbsp;</p></td>
											<td><p>&nbsp;</p></td>
											<td align="center"><p><strong><?php echo $total_unit_wise_role_qty[$order_type][$kniting_source_key][$uomId]; ?></strong></p></td>
											<td><p>&nbsp;</p></td>
											
										</tr>
										<?
									}
								}

							}
							?>
						</tbody>
					</table>
				</div>
					
				</div>
			</fieldset>
		</div>
		<?
		
	}
	else if($category==1)
	{
		$sql_dtls=  "SELECT  1 as type,d.order_id,d.yarn_lot, d.yarn_count,  avg(e.process_loss) as avg_los,sum(e.finish_qty) as fin_qty,sum(e.grey_qty) as grey_qty, e.color_type_id, c.season, d.body_part_id, d.prod_id,d.febric_description_id,d.gsm,d.color_range_id,d.color_id,d.stitch_length, d.width,c.company_id,c.buyer_id, c.style_ref_no, c.job_no, c.job_no_prefix_num, c.sales_booking_no, c.booking_id,c.within_group,c.po_company_id as lc_company_id,c.po_buyer,c.po_job_no,c.booking_without_order,c.booking_type,c.booking_entry_form
		from inv_receive_master a, pro_grey_prod_entry_dtls d, fabric_sales_order_mst c,fabric_sales_order_dtls e 
		where a.id=d.mst_id and d.order_id=CAST (c.id AS VARCHAR2(4000))  and c.id=e.mst_id and a.entry_form in(2) and d.body_part_id=e.body_part_id   and a.receive_basis in(2,4,10) and a.item_category=13   and a.status_active=1    and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and e.is_deleted=0    $condition_string
		group by d.order_id,d.yarn_lot,d.yarn_count ,e.color_type_id, c.season,d.body_part_id, d.prod_id,d.febric_description_id,d.gsm,d.color_range_id,d.color_id,d.stitch_length, d.width,c.company_id,c.buyer_id, c.style_ref_no, c.job_no, c.job_no_prefix_num, c.sales_booking_no, c.booking_id,c.within_group,c.po_company_id ,c.po_buyer,c.po_job_no,c.booking_without_order,c.booking_type,c.booking_entry_form "; 
	 
		//echo $sql_dtls;die;
		 
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
			 
	 
			$salesOrder_id .= $row[csf('order_id')].","; 

		}



		$knitting_company_id 	= implode(",", array_filter(array_unique(explode(",",chop($knitting_company_id,",")))));
		$location_id 		 	= implode(",", array_filter(array_unique(explode(",",chop($location_id,",")))));
		$color_id 			 	= implode(",", array_filter(array_unique(explode(",",chop($color_id,",")))));
	 	$buyer_id 			 	= implode(",", array_filter(array_unique(explode(",",chop($buyer_id,",")))));
		$floor 				 	= implode(",", array_filter(array_unique(explode(",",chop($floor,",")))));
	  	$fabric_description_id 	= implode(",", array_filter(array_unique(explode(",",chop($fabric_description_id,",")))));
		$salesOrder_id 			= implode(",", array_filter(array_unique(explode(",",chop($salesOrder_id,",")))));
		$all_po_arr=array_unique( explode(",", $salesOrder_id));
		$all_po_ids= implode(",",$all_po_arr);
		$po_conds=" and f.id in($all_po_ids)";
		if($db_type==2 && count($all_po_arr)>999)
		{
			$po_conds="";
			$chnk=array_chunk($all_po_arr, 999);
			foreach($chnk as $val)
			{
				$ids=implode(",", $val);
				if(!$po_conds)$po_conds.=" and ( f.id in($ids) ";
				else $po_conds.=" or  f.id in($ids) ";
			}
			$po_conds.=")";

		}
		$po_conds2=str_replace("f.id", "d.id",$po_conds);
		$po_conds3=str_replace("f.id", "b.po_breakdown_id",$po_conds);
		$po_conds_sales=str_replace("f.id", "mst_id",$po_conds);

		$sales_order_qnty="SELECT   mst_id,  body_part_id,  determination_id,  color_id, sum(finish_qty) as finish_qty, avg(process_loss) as process_loss, sum(grey_qty) as grey_qty  from fabric_sales_order_dtls where status_active=1  $po_conds_sales group by mst_id,  body_part_id,  determination_id,  color_id   ";
		$sales_order_qnty_arr=array();
		foreach(sql_select($sales_order_qnty) as $vals)
		{
			$sales_order_qnty_arr[$vals[csf("mst_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_id")]]["finish_qty"]+=$vals[csf("finish_qty")];

			$sales_order_qnty_arr[$vals[csf("mst_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_id")]]["process_loss"]+=$vals[csf("process_loss")];

			$sales_order_qnty_arr[$vals[csf("mst_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_id")]]["grey_qty"]+=$vals[csf("grey_qty")];
		}
		//print_r($sales_order_qnty_arr);
	

		//If sales order data not found in receive then this part will check for transfer in data 
	 	$trans_in_row =  sql_select("SELECT 2 as type, sum(b.transfer_qnty) as transfer_in_qnty,a.to_order_id as order_id,y_count as yarn_count, b.yarn_lot,     d.season,   b.from_prod_id  as prod_id,c.detarmination_id as febric_description_id,c.gsm,c.dia_width as width, e.color_range as color_range_id,b.color_id,b.stitch_length, d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form  
			from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , product_details_master c 
			where a.id=b.mst_id and a.to_order_id=d.id and b.from_prod_id = c.id   and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 $po_conds2
			group by a.to_order_id  ,b.yarn_lot,  y_count,   d.season,   b.from_prod_id  ,c.detarmination_id ,c.gsm,c.dia_width, e.color_range ,b.color_id,b.stitch_length, d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id ,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form ");
		foreach($trans_in_row as $vals)
		{
			$all_prod_arr[$vals[csf("prod_id")]]=$vals[csf("prod_id")];
		}
		$prod_cond="";
		if(count($all_prod_arr)<999)$prod_cond=" and a.prod_id in(".implode(",",$all_prod_arr).")";

		$production_sql_tr = sql_select("SELECT a.body_part_id, a.prod_id, a.color_range_id,b.barcode_no,a.yarn_lot,a.yarn_count,b.po_breakdown_id,a.prod_id,a.color_id,a.stitch_length from pro_grey_prod_entry_dtls a,pro_roll_details b where a.trans_id=0 and a.status_active=1 and a.id=b.dtls_id and b.entry_form in(2) $prod_cond   ");
		foreach ($production_sql_tr as $row) 
		{
			$color_ids=explode(",", $row[csf("color_id")]);
			foreach($color_ids as $val_c)
			{
				$prod_wise_color[$row[csf("prod_id")]][trim($row[csf("yarn_lot")])][trim($row[csf("stitch_length")])]["color"].=','.$color_arr[$val_c];
			}
			
			$prod_wise_color[$row[csf("prod_id")]][trim($row[csf("yarn_lot")])][trim($row[csf("stitch_length")])]["color_range"].=','.$color_range[$row[csf("color_range_id")]];
			$prod_wise_color[$row[csf("prod_id")]][trim($row[csf("yarn_lot")])][trim($row[csf("stitch_length")])]["body_part"].=','.$body_part_arr[$row[csf("body_part_id")]];
			$prod_wise_color[$row[csf("prod_id")]][trim($row[csf("yarn_lot")])][trim($row[csf("stitch_length")])]["body_part_id"] =$row[csf("body_part_id")] ;
			 
		}
		//print_r($prod_wise_color);die;

		$issue_sql = "SELECT b.stitch_length, b.yarn_lot, b.color_id, d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2	and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.is_returned<>1 $po_conds ";

		$sql_iss=sql_select($issue_sql);

		$knit_issue_arr=array();
		$knit_issue_arr2=array();
		foreach($sql_iss as $row)
		{

			$knit_issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['issue_qty'] += $row[csf('issue_qty')];

			$knit_issue_arr2[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][trim($row[csf('yarn_lot')])][trim($row[csf('stitch_length')])]['issue_qty'] += $row[csf('issue_qty')];			 
		}

		unset($sql_iss);

		$trans_out_sql = "SELECT b.stitch_length,b.yarn_lot, a.from_order_id,e.transaction_date,b.from_prod_id,d.qnty as transfer_out_qnty,d.barcode_no	from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and b.from_prod_id=c.id and a.from_order_id=f.id and b.status_active=1 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=6  $po_conds and a.id=d.mst_id and d.entry_form=133 and b.id=d.dtls_id and d.status_active=1 and d.is_deleted=0";
		$trans_out_data = sql_select($trans_out_sql);

		foreach($trans_out_data as $row)
		{
			 
				$transOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][trim($row[csf('yarn_lot')])][trim($row[csf('stitch_length')])] += $row[csf("transfer_out_qnty")]; 
		}

		$rec_sql = "SELECT b.po_breakdown_id , c.prod_id,c.color_id,sum(b.qnty) as receive_qty from inv_receive_master a,  pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst f where a.id=b.mst_id and  c.id=b.dtls_id and b.po_breakdown_id=f.id  and b.entry_form =58 and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13   and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 $po_conds group by b.po_breakdown_id , c.prod_id,c.color_id";
	

		foreach(sql_select($rec_sql) as $row)
		{
			$knit_receive_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['receive_qty'] += $row[csf('receive_qty')];
		}
		$po_conds3=str_replace("f.id", "c.po_breakdown_id",$po_conds);
		$transaction_date_array=array();
		$sql_date="SELECT c.po_breakdown_id,a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date
		from inv_transaction a,order_wise_pro_details c
		where a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13
		$po_conds3 group by c.po_breakdown_id,a.prod_id";

		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}

		if($knitting_company_id!="")
		{
			$kinttin_companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0 and id in($knitting_company_id)" ,"id","company_short_name");

			$kinttin_supplierArr = return_library_array("select id,short_name from lib_supplier where status_active=1 and is_deleted=0 and id in($knitting_company_id)" ,"id","short_name");
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
			if(!empty($floor_id_arr))
			{
				$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and id in(".implode(",",$floor_id_arr).") and location_id in($location_id) and company_id in($knitting_company_id)",'id','floor_name');
			}
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

		ob_start();
		?>
		<div>
		<style type="text/css">
			.alignment_css
			{
				word-break: break-all;
				word-wrap: break-word;
			}
		</style>
		<fieldset style="width:3650px;">
			<table width="3650" cellpadding="0" cellspacing="0" id="caption" align="center">
				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:14px">Style Wise Grey Fabric Process Loss and Transaction Report</strong></td>
				</tr>

				<tr>
					<td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $company_lib_arr[str_replace("'","",$cbo_company_id)];?></strong></td>
				</tr>
			</table> 
			 
			<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="3630" align="left" >
				<thead>
					<tr>
						<th rowspan="2" class="alignment_css" width="30">SL</th>
						<th rowspan="2" class="alignment_css" width="100">Company</th> 
						<th rowspan="2" class="alignment_css" width="100">LC Company</th>						
						<th rowspan="2" class="alignment_css" width="100">Buyer Name</th>
						<th rowspan="2" class="alignment_css" width="100">Style Ref.</th>
						<th rowspan="2" class="alignment_css" width="100">Season</th>

						<th rowspan="2" class="alignment_css" width="100">Booking</th>
						<th rowspan="2" class="alignment_css" width="100">Booking Type</th>
						<th rowspan="2" class="alignment_css" width="100">Short Booking<br> Type</th>
						<th rowspan="2" class="alignment_css" width="100">FSO</th>
						<th rowspan="2" class="alignment_css" width="100">Within Group</th>
						<th colspan="11">Fabric Details</th>
						<th colspan="7">Receive Details </th>
						<th colspan="4">Issue Details</th>
						<th colspan="4">Stock Details </th>
					 
						
					</tr>
					<tr>
						
						<th class="alignment_css" width="100">Body Part</th>
						<th class="alignment_css" width="100">Color Type</th>						
						<th class="alignment_css" width="100">Construction</th>
						<th class="alignment_css" width="100">Composition</th> 
						<th class="alignment_css" width="100">F. Dia</th>
						<th class="alignment_css" width="100">GSM</th>
						<th class="alignment_css" width="100">Color</th>
						<th class="alignment_css" width="100">Color Range</th>
						<th class="alignment_css" width="100">Finish Qty</th>
						<th class="alignment_css" width="100">Avg. Process <br>Loss %</th>
						<th class="alignment_css" width="100">Grey Qty</th>
						<th class="alignment_css" width="100">Yarn Lot</th>
						<th class="alignment_css" width="100">Yarn Count</th>
						<th class="alignment_css" width="100">Stitch Length</th>
						<th class="alignment_css" width="100">Recv. Qty.</th>
						<th class="alignment_css" width="100">Issue Return <br>Qty.</th>
						<th class="alignment_css" width="100">Transf. In<br> Qty.</th>
						<th class="alignment_css" width="100">Total Recv. </th>
						<th class="alignment_css" width="100">Issue Qty. </th>
						<th class="alignment_css" width="100">Receive Return <br>Qty. </th>
						<th class="alignment_css" width="100">Transf. Out <br>Qty. </th>
						<th class="alignment_css" width="100">Total Issue </th>
						<th class="alignment_css" width="100">Stock Qty. </th>
						<th class="alignment_css" width="100">Short/Excess </th>
						<th class="alignment_css" width="100">Age(days) </th>
						<th class="alignment_css" width="100">DOH </th>

						 

					</tr>
				</thead>
			</table>
			<div style="width:3650px; max-height:540px; overflow-y:scroll"  align="left" id="scroll_body">
				<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="3630" align="left" id="table_body" >
					<tbody>
						 <?

						$i=1;
						$composition_arr = array();
						$constructtion_arr = array();
						$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
						$data_array = sql_select($sql_deter);
						foreach ($data_array as $row)
						{
							$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
							$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
						}

						$total_qc_qty = 0;
						$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
						$company_arr 	= return_library_array( "SELECT id,company_short_name from lib_company",'id','company_short_name');
						$buyer_arr   	= return_library_array( "SELECT id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
						$season_arr  	= return_library_array( "SELECT id, season_name from LIB_BUYER_SEASON",'id','season_name');
						$yarn_count_arr = return_library_array("SELECT id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
						
						$count_main=count($sql_dtls_result);
						if(count($sql_dtls_result)>0 && count($trans_in_row)>0)
						{
							$sql_dtls_result=array_merge($sql_dtls_result,$trans_in_row);
						}

						foreach ($sql_dtls_result as  $row) 
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$buyer_name=($row[csf("within_group")]==1)?$buyer_arr[$row[csf('po_buyer')]]:$buyer_arr[$row[csf('buyer_id')]];
							$poId=$row[csf("order_id")];
							$prodId=$row[csf("prod_id")]; 
							$body_part_id=$row[csf("body_part_id")];
							$daysOnHand = datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['max_date'],'','',1),date("Y-m-d"));
							$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['min_date'],'','',1),date("Y-m-d"));
							$body_part_val=($row[csf("type")]==1)? $body_part_arr[$row[csf("body_part_id")]] : $prod_wise_color[$row[csf("prod_id")]][trim($row[csf("yarn_lot")])][trim($row[csf("stitch_length")])]["body_part"];
							$body_part_val=implode(",",array_unique(explode(",",trim($body_part_val,","))));

							$color_val_tr=  $prod_wise_color[$row[csf("prod_id")]][trim($row[csf("yarn_lot")])][trim($row[csf("stitch_length")])]["color"];
							$color_val_tr=implode(",",array_unique(explode(",",trim($color_val_tr,","))));


							$color_range_val=  $prod_wise_color[$row[csf("prod_id")]][trim($row[csf("yarn_lot")])][trim($row[csf("stitch_length")])]["color_range"];
							$color_range_val=implode(",",array_unique(explode(",",trim($color_range_val,","))));
							$tr_body_part_id=$prod_wise_color[$row[csf("prod_id")]][trim($row[csf("yarn_lot")])][trim($row[csf("stitch_length")])]["body_part_id"];


							 ?>
							 <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<?echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
							 	<td class="alignment_css" width="30" align="center"><? echo $i;?> </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo $company_arr[$row[csf("company_id")]];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo $company_arr[$row[csf("lc_company_id")]];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo $buyer_name;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $row[csf("style_ref_no")];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $row[csf("season")];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $row[csf("sales_booking_no")];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $booking_type_arr[$row[csf("booking_entry_form")]];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo $short_booking_type[$row[csf("sales_booking_no")]]['short_booking_type'] ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $row[csf("job_no")];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $yes_no[$row[csf("within_group")]];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $body_part_val;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $color_type[$row[csf("color_type_id")]];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $constructtion_arr[$row[csf("febric_description_id")]];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  $composition_arr[$row[csf("febric_description_id")]];?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   $row[csf("width")] ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   $row[csf("gsm")] ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center">
							 		<p>
							 			<?
							 			$color_ids = array_unique(explode(",",rtrim( $row[csf("color_id")],", ")));
							 			$color_names="";
							 			$fin_qty=$grey_qty=$process_loss=0;
							 			foreach ($color_ids as  $color)
							 			{
							 				$color_names .= $color_arr[$color].", ";
							 				$fin_qty+=$sales_order_qnty_arr[$row[csf("order_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$color]["finish_qty"];
							 				$grey_qty+=$sales_order_qnty_arr[$row[csf("order_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$color]["grey_qty"] ;
							 				$process_loss+=$sales_order_qnty_arr[$row[csf("order_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$color]["process_loss"] ; 

							 			}
							 			echo $color_val=chop($color_names,", ");
							 			if(!$color_val)echo $color_val_tr ;
							 			$all_color_ids=implode(",",$color_ids);

							 			
							 			 $receive_qty=$knit_receive_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['receive_qty'];
							 			 $issue_rtn= $row[csf("issue_rtn")] ;
							 			 $trans_in=  $row[csf("transfer_in_qnty")];
							 			 $ttl_rec= $receive_qty+$issue_rtn+$trans_in;
										 $issue_qty=$knit_issue_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('color_id')]]['issue_qty'];
										 if($trans_in) $issue_qty=$knit_issue_arr2[$row[csf('order_id')]][$row[csf('prod_id')]][trim($row[csf('yarn_lot')])][trim($row[csf('stitch_length')])]['issue_qty'];
										 $receive_rtn=  $row[csf("receive_rtn")] ;
										 $trans_out=$transOutQnty[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('yarn_lot')]][$row[csf('stitch_length')]];
										 $ttl_issue= ($issue_qty+$receive_rtn+$trans_out) ;
										 $stock=$ttl_rec-$ttl_issue;
										 $short_ex=$grey_qty-$ttl_issue;
										 $transfer_data=$poId."__".$row[csf("yarn_lot")]."__".$row[csf("stitch_length")];
										 $finish_data=$poId."__".$row[csf("body_part_id")]."__".$row[csf("febric_description_id")]."__".$all_color_ids."__".$row[csf("sales_booking_no")];
										 $receive_data=$poId."__".$row[csf("body_part_id")]."__".$row[csf("febric_description_id")]."__".$row[csf("sales_booking_no")]."__".$all_color_ids;
										 
							 			?>
							 		</p>
							 	</td>
							 	<td class="alignment_css" width="100" align="center"><p><? 
							 	echo  $range_val= $color_range[$row[csf("color_range_id")]] ;
							 	if(!$range_val) echo $color_range_val;
							 	?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><a href="##" onclick="openmypage_finish_qty('<? echo $finish_data;?>',2);"> <? echo number_format($fin_qty,2) ;?></a>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   number_format($process_loss/count($color_ids),2) ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   number_format($grey_qty ,2);?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   $row[csf("yarn_lot")] ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center">
							 		<p><?
							 			$counts="";
							 			$yarn_counts = array_unique(explode(",", $row[csf("yarn_count")]));
							 			foreach ($yarn_counts as $yarn_count) {
							 				$counts .= $yarn_count_arr[$yarn_count].",";
							 			}
							 			echo trim($counts,", ");
							 			
							 			?>

							 		</p>  
							 	</td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   $row[csf("stitch_length")] ;?></p>  </td>

							 	<td class="alignment_css" width="100" align="center"><a href="##" onclick="openmypage_receive_qty('<? echo $receive_data;?>',2);"> <? echo number_format($receive_qty,2) ;?></a>  </td>
							 	
							 	<td class="alignment_css" width="100" align="center"><p><? echo  number_format($issue_rtn,2) ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><a href="##" onclick="openmypage_transfer_in_out('<? echo $transfer_data;?>',1);"> <? echo number_format($trans_in,2) ;?></a>   </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   number_format($ttl_rec,2)  ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   number_format($issue_qty,2)  ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo number_format($receive_rtn,2);?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"> <a href="##" onclick="openmypage_transfer_in_out('<? echo $transfer_data;?>',2);"> <? echo number_format($trans_out,2) ;?></a>    </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo  number_format($ttl_issue,2) ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo   number_format($stock,2) ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? if($short_ex>0) echo   number_format($short_ex,2) ;?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo $ageOfDays; ?></p>  </td>
							 	<td class="alignment_css" width="100" align="center"><p><? echo $daysOnHand;?></p>  </td>

							 </tr>

							 <?
							 //if($i==$count_main)$sql_dtls_result=$trans_in_row;
							 if($tr_body_part_id)$body_part_id =$tr_body_part_id;
							 $body_part_wise_summ[$body_part_id]["grey"]+=str_replace(",","",$grey_qty);
							 $body_part_wise_summ[$body_part_id]["ttl_issue"]+=str_replace(",","",$ttl_issue);
							 $body_part_wise_summ[$body_part_id]["ttl_rec"]+=str_replace(",","",$ttl_rec);
							 if($short_ex>0)
							 $body_part_wise_summ[$body_part_id]["short_ex"]+=str_replace(",","",$short_ex);

							 $i++;
						}
						?>
					</tbody>
				</table>
			</div>

			<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="3630" align="left" >
				<tfoot>
					<tr>
						<td class="alignment_css" width="30" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td> 
						<td class="alignment_css" width="100" align="center">   </td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>   
						<td class="alignment_css" width="100" align="center"> </td>
						<td class="alignment_css" width="100" align="center">   </td>
						<td class="alignment_css" width="100" align="center">  </td>
						<td class="alignment_css" width="100" align="center"> </td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
 
						<td class="alignment_css" width="100" align="center">   </td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center"></td>
						<td class="alignment_css" width="100" align="center">Total</td>


						<td class="alignment_css" width="100" align="center" id="gr_rec_qty"> </td>
 

						<td class="alignment_css" width="100" id="gr_issue_rtn" align="center"></td>
						<td class="alignment_css" width="100" id="gr_tr_in" align="center"></td>
						<td class="alignment_css" width="100" id="gr_ttl_recv" align="center"></td>
						<td class="alignment_css" width="100" id="gr_issue_qty" align="center"></td>
						<td class="alignment_css" width="100" id="gr_rec_rtn" align="center"></td>
						<td class="alignment_css" width="100" id="gr_tr_out" align="center"></td>
						<td class="alignment_css" width="100" id="gr_ttl_issue" align="center"></td>
						<td class="alignment_css" width="100" id="gr_stock" align="center"></td>
						<td class="alignment_css" width="100"  align="center"></td>
						<td class="alignment_css" width="100"  align="center"></td>
						<td class="alignment_css" width="100"  align="center"></td>
					</tr>
				</tfoot>
			</table>

			</fieldset><br><br>
			<table border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" width="550" align="left" >
			<thead>
				<tr>
					<th width="150">Body Part</th>
					<th width="100">Grey Qty.</th>
					<th width="100">TTL Receive Qty.</th>
					<th width="100">TTL Issue Qty.</th>
					<th width="100">Short/Excess</th>
				</tr>
			</thead>
			<tbody>
				<?
				foreach($body_part_wise_summ as $b_id=> $vals)
				{
					?>
					<tr>
						<td align="center"><? echo  $body_part_arr[$b_id];?></td>
						<td align='right'><? echo number_format($vals["grey"],2) ;?></td>
						<td align='right'><? echo number_format($vals["ttl_rec"],2) ;?></td>
						<td align='right'><? echo number_format($vals["ttl_issue"],2) ;?></td>
						
						<td  align='right' ><? echo number_format($vals["short_ex"],2) ;?></td>
					</tr>

					<?
				}


				?>
			</tbody>
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
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}

if($action=="receive_qtypopup")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($poId,$body_part_id,$determination_id,$booking_no,$color_ids)= explode("__", $data);
	 
	$color_library=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0  ", "id", "color_name");
	$comp_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0  " ,"id","company_short_name");


	?>
	<fieldset style="width:1170;"" >
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1170" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="13"><b>Receive Details</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="90">Program Date</th>
                        <th width="90">Knitting Source</th>
                        <th width="90">Knitting Company</th>
                        <th width="90">Color</th>
                        <th width="90">Color Range</th>
                        <th width="90">M/C Dia</th>
                        <th width="90">M/C GG</th>
                        <th width="90">Stitch Length</th>
                        <th width="90">Span. Stitch Length</th>
                        <th width="90">Program Qnty</th>
                        <th width="90">Receive Qty.</th>
                        <th width="90">Status</th>
                    </tr>
				</thead>
            </table>
            <table border="1" class="rpt_table" rules="all" width="1170" cellpadding="0" cellspacing="0" id="table_body">
                <?
             	     $sql="SELECT a.body_part_id, a.determination_id,a.booking_no,  b.knitting_source,b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg,   b.program_date, b.stitch_length, b.spandex_stitch_length,b.status,sum(b.program_qnty) as qnty from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.booking_no='$booking_no' and body_part_id='$body_part_id'  and determination_id='$determination_id' group by a.body_part_id, a.determination_id, a.booking_no, b.knitting_source,b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg,   b.program_date, b.stitch_length, b.spandex_stitch_length,b.status  ";

             	     $rec_sql = "SELECT c.body_part_id, c.febric_description_id, f.sales_booking_no, b.po_breakdown_id , c.prod_id,c.color_id,sum(b.qnty) as receive_qty
             	    from inv_receive_master a,  pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst f
             	    where a.id=b.mst_id and  c.id=b.dtls_id and b.po_breakdown_id=f.id  and b.entry_form =58 and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13   and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.sales_booking_no='$booking_no' and c.body_part_id='$body_part_id' and c.color_id in ('$color_ids') and c.febric_description_id='$determination_id' group by  c.body_part_id,c.febric_description_id, f.sales_booking_no, b.po_breakdown_id , c.prod_id,c.color_id";


             	    foreach(sql_select($rec_sql) as $row)
             	    {
             	    	foreach( array_unique( explode(",",$row[csf('color_id')])) as $color_val)
             	    	{
             	    		$knit_receive_arr[$row[csf('sales_booking_no')]][$row[csf('febric_description_id')]][$row[csf('body_part_id')]][$color_val]['receive_qty'] += $row[csf('receive_qty')];
             	    	}
             	    	
             	    }
					//print_r($knit_receive_arr);die;

             

				$i=1;
				foreach (sql_select($sql) as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$receive_qty=0;
					$color_names="";
					$unique_color =array_unique( explode(",",$row[csf('color_id')]));
					foreach( $unique_color as $color_val)
         	    	{
         	    		$receive_qty+=$knit_receive_arr[$row[csf('booking_no')]][$row[csf('determination_id')]][$row[csf('body_part_id')]][$color_val]['receive_qty'];
         	    		 
         	    		$color_names.=($color_names)? ','.$color_library[$color_val]: $color_library[$color_val]; 
         	    	}
         	    	$recv_qty= $receive_qty/count($unique_color);

					
					
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                	<td width="30" align='center' ><? echo $i; ?></td>
                	<td width="90" align='center' ><p><? echo change_date_format($row[csf('program_date')]); ?></p></td>
                	<td width="90" align="center"><p><? echo  $knitting_source[$row[csf('knitting_source')]] ; ?></p>&nbsp;</td>
                	<td width="90"  align='center'  ><? echo $comp_arr[$row[csf('knitting_party')]]; ?></td>
                	<td width="90" align='center' ><p><? echo chop($color_names,","); ?></p></td>
                	<td width="90" align="center"><p><? echo  $color_range[$row[csf('color_range')]] ; ?></p>&nbsp;</td>
                	<td width="90" align="center"><? echo $row[csf('machine_dia')]; ?></td>
                	<td width="90" align="center"><? echo $row[csf('machine_gg')]; ?></td>
                	<td width="90" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                	<td width="90" align="center"><? echo $row[csf('spandex_stitch_length')]; ?></td>
                	<td width="90" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                	<td width="90" align="right"><? echo    number_format($receive_qty/count($unique_color),2); ?></td>
                	<td width="90" align="center"><? if($recv_qty)echo "Completed";else echo "Pending"; ?></td>
                </tr>
                <?
                $total_qty+=$row[csf('qnty')];
                $total_receive_qty+=$receive_qty/count($unique_color);
                $i++;
                }
                ?>
                  <tfoot>
                	<tr>
                        <th colspan="10" align="right">Total</th>
                        <th align="right"><? echo number_format($total_qty,2); ?></th>
                        <th align="right"><? echo number_format($total_receive_qty,2); ?></th>
                        <th   align="right"></th>
                    </tr>
                    
                </tfoot>  
            </table>
		</div>
	</fieldset>	
  <script>
  setFilterGrid("table_body",-1);
  </script>
    <?
	exit();
}

if($action=="finish_qtypopup")
{
	echo load_html_head_contents("Finish  Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($poId,$body_part_id,$determination_id,$color_ids,$booking_no)= explode("__", $data);
	 
	$color_library=return_library_array( "SELECT id,color_name from lib_color where status_active=1 and is_deleted=0  ", "id", "color_name");
	

	?>
	<fieldset style="width:830;"" >
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Finish Details</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Color</th>
                        <th width="100">Color Range</th>
                        <th width="100">Cons. UOM</th>
                        <th width="70">Booking Qty.</th>
                        <th width="100">UOM</th>
                        <th width="100">Finish Qty.</th>
                        <th width="100">Process Loss %</th>
                        <th width="100">Grey Qty.</th>
                    </tr>
				</thead>
            </table>
            <table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" id="table_body">
                <?
             	  $sql="SELECT b.color_id,b.color_range_id,  b.  cons_uom,    b.order_uom ,sum(b.finish_qty) as finish_qty, avg(b.process_loss) as process_loss,sum(b.grey_qty) as grey_qty from fabric_sales_order_mst a,fabric_sales_order_dtls b  where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id='$poId' and b.body_part_id='$body_part_id' and determination_id='$determination_id' and b.color_id in ($color_ids) group by  b.color_id,b.color_range_id,  b.  cons_uom,    b.order_uom   ";
             	 $booking_sql="SELECT   b.fabric_color_id, sum(b.grey_fab_qnty) as qnty  from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and b.status_active=1 and  a.status_active=1 and a.lib_yarn_count_deter_id='$determination_id' and a.body_part_id='$body_part_id' and booking_no='$booking_no' group by   b.fabric_color_id
             	 	union all 
             	 	  SELECT  fabric_color as fabric_color_id , sum(grey_fabric) as qnty  from wo_non_ord_samp_booking_dtls where status_active=1 and lib_yarn_count_deter_id='$determination_id' and body_part='$body_part_id' and booking_no='$booking_no' group by fabric_color     	 ";
             	  foreach(sql_select($booking_sql) as $row)
             	  {
             	  	$booking_array[$row[csf("fabric_color_id")]]+=$row[csf("qnty")];
             	  }

             

				$i=1;
				foreach (sql_select($sql) as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$booking_qty=$booking_array[$row[csf("color_id")]];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30" align='center' ><? echo $i; ?></td>
                        <td width="120" align='center' ><p><? echo $color_library[$row[csf('color_id')]]; ?></p></td>
                        <td width="100" align="center"><p><? echo  $color_range[$row[csf('color_range_id')]] ; ?></p>&nbsp;</td>
                        <td width="100"  align='center'  ><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                        <td width="70" align="center"><? echo number_format($booking_qty,2); ?></td>
                        <td width="100" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        <td width="100" align="right"><? echo number_format($row[csf('finish_qty')],2); ?></td>
                        <td width="100" align="right"><? echo number_format($row[csf('process_loss')],2); ?></td>
                        <td width="100" align="right"><? echo number_format($row[csf('grey_qty')],2); ?></td>
                    </tr>
                <?
                $total_finish_qty+=$row[csf('finish_qty')];
                $total_grey_qty+=$row[csf('grey_qty')];
                $i++;
                }
                ?>
                <tfoot>
                	<tr>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_finish_qty,2); ?></th>
                        <th   align="right"></th>
                        <th align="right"><? echo number_format($total_grey_qty,2); ?></th>
                        
                    </tr>
                    
                </tfoot>
            </table>
		</div>
	</fieldset>	
  <script>
  setFilterGrid("table_body",-1);
  </script>
    <?
	exit();
}


if($action=="transfer_in_popup")
{
	echo load_html_head_contents("Transfer In Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($po_id,$yarn_lot,$stitch_length)= explode("__", $data);
	?>
	<fieldset style="width:830;" >
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Transfer In Details</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Transfer ID</th>
                        <th width="100">Transfer Date</th>
                        <th width="100">Transfer In Qty</th>
                        <th width="70">Roll No</th>
                        <th width="100">Program No</th>
                        <th width="100">Barcode No</th>
                        <th width="100">Rack No</th>
                        <th width="100">Shelf</th>
                    </tr>
				</thead>
            </table>
            <table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" id="table_body">
                <?
              if($type==1)
              {
              		  $trans_sql="SELECT e.id,sum(b.transfer_qnty) as qnty,b.to_rack, b.to_shelf,c.roll_no,c.barcode_no,a.to_order_id as order_id,b.yarn_lot,   b.from_prod_id  as prod_id  ,a.transfer_system_id,a.transfer_date from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , pro_roll_details c     	 where a.id=b.mst_id and a.to_order_id=d.id and b.id = c.dtls_id   and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form=133 and c.entry_form=133 and a.transfer_criteria=4 and b.yarn_lot='$yarn_lot' and b.stitch_length='$stitch_length' and a.to_order_id='$po_id' group by e.id,b.to_rack, b.to_shelf,c.roll_no,c.barcode_no,a.to_order_id  ,b.yarn_lot,   b.from_prod_id     ,a.transfer_system_id,a.transfer_date ";

              }
              else
              {
              	$trans_sql="SELECT e.id,sum(b.transfer_qnty) as qnty,b.to_rack, b.to_shelf,c.roll_no,c.barcode_no,a.to_order_id as order_id,b.yarn_lot,   b.from_prod_id  as prod_id  ,a.transfer_system_id,a.transfer_date from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , pro_roll_details c     	 where a.id=b.mst_id and a.to_order_id=d.id and b.id = c.dtls_id   and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form=133 and c.entry_form=133 and a.transfer_criteria=4 and b.yarn_lot='$yarn_lot' and b.stitch_length='$stitch_length' and a.from_order_id='$po_id' group by e.id,b.to_rack, b.to_shelf,c.roll_no,c.barcode_no,a.to_order_id  ,b.yarn_lot,   b.from_prod_id     ,a.transfer_system_id,a.transfer_date ";

              }

				$i=1;
				foreach (sql_select($trans_sql) as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="120"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="100" align="center"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p>&nbsp;</td>
                        <td width="100" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
                        <td width="70" align="center"><? echo $row[csf('roll_no')]; ?></td>
                        <td width="100" align="center"><? echo $row[csf('id')]; ?></td>
                        <td width="100" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        <td width="100" align="center"><? echo $row[csf('to_rack')]; ?></td>
                        <td width="100" align="center"><? echo $row[csf('to_shelf')]; ?></td>
                    </tr>
                <?
                $total_qty+=$row[csf('qnty')];
                $i++;
                }
                ?>
                <tfoot>
                	<tr>
                        <th colspan="3" align="right">Total</th>
                        <th align="right"><? echo number_format($total_qty,2); ?></th>
                        <th colspan="5" align="right"></th>
                    </tr>
                    
                </tfoot>
            </table>
		</div>
	</fieldset>	
  <script>
  setFilterGrid("table_body",-1);
  </script>
    <?
	exit();
}


?>