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
	$year = str_replace("'","",$year);
	if($db_type==0) $year_field_by="and YEAR(insert_date)";
		else if($db_type==2) $year_field_by=" and to_char(insert_date,'YYYY')";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";

	if($db_type==0) $year_field_grpby="GROUP BY batch_no";
	else if($db_type==2) $year_field_grpby=" GROUP BY batch_no,id,batch_no,batch_for,booking_no,color_id,batch_weight order by batch_no desc";
	$sql="SELECT id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and is_deleted = 0 and batch_no is not null $year_cond $year_field_grpby";
	$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,70","520","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

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
		echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($company) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else if($report_type==2)
	{
		echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($company) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else if($report_type==0)
	{
		echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($company) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,2,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value, 'create_booking_no_search_list_view', 'search_div', 'batch_report_for_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="jobnumbershow")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
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
	if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $year_field_grpby="GROUP BY a.job_no order by b.id desc";
	else if($db_type==2) $year_field_grpby=" GROUP BY a.job_no,a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num,a.insert_date,b.id order by b.id desc";
	$year_job = str_replace("'","",$year);
	if(trim($year)!=0) $year_cond=" $year_field_by=$year_job"; else $year_cond="";

	if(trim($cbo_buyer_name)==0) $buyer_name_cond=""; else $buyer_name_cond=" and a.buyer_name=$cbo_buyer_name";
	if(trim($cbo_buyer_name)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$cbo_buyer_name";
	if ($batch_type==0 || $batch_type==1)
	{
		$sql="SELECT a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num as job_prefix,$year_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name in($company_id) $buyer_name_cond $year_cond and a.is_deleted=0 $year_field_grpby";
	}
	else
	{
		$sql="SELECT a.id,a.party_id as buyer_name,c.item_id as gmts_item_id,b.cust_style_ref as style_ref_no,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field from  subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id in($company_id)  $sub_buyer_name_cond $year_cond and a.is_deleted=0 group by a.id,a.party_id,b.cust_style_ref,b.order_no ,a.job_no_prefix_num,a.insert_date,c.item_id";
	}

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
			<tr><th colspan="7"><? if($batch_type==0 || $batch_type==1)
			{ echo "Self Batch Order";} else if($batch_type==2) { echo "SubCon Batch Order";}?>  </th></tr>
			<tr>
				<th width="30">SL</th>
				<th width="100">Po number</th>
				<th width="50">Job no</th>
				<th width="40">Year</th>
				<th width="100">Buyer</th>
				<th width="100">Style</th>
				<th>Item Name</th>
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
				?>
				<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('job_prefix')]; ?>')" style="cursor:pointer;">
					<td width="30"><? echo $i; ?></td>
					<td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
					<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
					<td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
					<td width="100"><p><? echo $buyer_arr[$data[csf('buyer_name')]]; ?></p></td>
					<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
					<td><p><?
					$itemid=explode(",",$data[csf('gmts_item_id')]);
					foreach($itemid as $index=>$id){
						echo ($itemid[$index]==end($itemid))? $garments_item[$id] : $garments_item[$id].', ';
					}
					?></p></td>
				</tr>
				<? $i++; } ?>
			</table>
		</div>
		<script> setFilterGrid("table_body2",-1); </script>
		<?
		disconnect($con);
		exit();
	}
	if($action=="order_number_popup")
	{
		echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
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
		$buyer = str_replace("'","",$buyer_name);
		$year = str_replace("'","",$year);
		$buyer = str_replace("'","",$buyer_name);
		if($db_type==0) $year_field="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";
		else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
		if($db_type==0) $year_field_by="and YEAR(b.insert_date)";
		else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
		if ($company_name==0) $company=""; else $company=" and b.company_name=$company_name";
		if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
//echo $buyer;die;

//if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";

if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
if ($batch_type==0 || $batch_type==1)
{
	$sql = "select distinct a.id,b.job_no,a.po_number,b.company_name,b.buyer_name,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company $buyername $year_cond order by a.id desc";

}
else
{
	$sql="select distinct a.id,b.job_no_mst as job_no ,a.party_id as buyer_name,a.company_id as company_name ,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field from  subcon_ord_mst a , subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_name  $sub_buyer_name_cond $year_cond and a.is_deleted =0 group by a.id,a.party_id,b.job_no_mst,b.order_no ,a.job_no_prefix_num,a.company_id,b.insert_date";
}

$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
?>
<table width="370" border="1" rules="all" class="rpt_table">
	<thead>
		<tr><th colspan="5"><? if($batch_type==0 || $batch_type==1) echo "Self Batch Order"; else echo "SubCon Batch Order";?>  </th></tr>
		<tr>
			<th width="30">SL</th>
			<th width="100">Order Number</th>
			<th width="50">Job no</th>
			<th width="80">Buyer</th>
			<th width="40">Year</th>
		</tr>
	</thead>
</table>
<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="370" border="1" rules="all" class="rpt_table">
		<? $rows=sql_select($sql);
		$i=1;
		foreach($rows as $data)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('po_number')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
				<td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
				<td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
				<td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
			</tr>
			<? $i++; } ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}
if($action=="batchextensionpopup")
{
	echo load_html_head_contents("Batch Ext Info", "../../../", 1, 1,'','','');
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
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$batch_number= str_replace("'","",$batch_number_show);
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if ($company_name==0) $company=""; else $company=" and a.company_id=$company_name";
	if ($batch_number==0) $batch_no=""; else $batch_no=" and a.batch_no=$batch_number";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	//echo $buyer;die;
	if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";
	$sql="select a.id,a.batch_no,a.extention_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.is_deleted=0 $company $batch_no ";
	$arr=array(2=>$color_library);
	echo  create_list_view("list_view", "Batch no,Extention No,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,100,170","620","350",0, $sql, "js_set_value", "extention_no,extention_no", "", 1, "0,0,color_id,0,0,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if($action=="batch_report") // For CCL
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$operation = str_replace("'","",$operation);
	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_id);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$txt_style_no = trim(str_replace("'","",$txt_style_no));
	$batch_no = str_replace("'","",$batch_number_show);
	$batch_type = str_replace("'","",$cbo_batch_type);

	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$year = str_replace("'","",$cbo_year);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$fso_number_show = str_replace("'","",$fso_number_show);
	$txt_booking_no_show = str_replace("'","",$txt_booking_no_show);
	$batch_against_id = str_replace("'","",$cbo_batch_against);
	$load_unload_id = str_replace("'","",$cbo_load_unload);
	$txt_int_ref_no = str_replace("'","",$txt_int_ref_no);

	$process=33;

	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='$buyer'";
	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_name='$buyer'";
	if ($buyer==0) $party_cond=""; else $party_cond="  and a.party_id='$buyer'";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".str_replace("'","",$batch_no)."'";
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if ($job_number_id=="") $jobdata=""; else $jobdata=" and a.job_no_prefix_num in($job_number_id)";
	if ($txt_style_no=="") $style_no_cond=""; else $style_no_cond="  and a.style_ref_no = '$txt_style_no'";
	if ($txt_style_no=="") $style_no_cond_2=""; else $style_no_cond_2="  and c.style_ref_no = '$txt_style_no'";
	if ($txt_style_no=="") $cust_style_cond=""; else $cust_style_cond="  and b.cust_style_ref = '$txt_style_no'";

	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $find_inset="and  FIND_IN_SET(33,a.process_id)"; else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if($batch_against_id) $batch_against_cond = " and a.batch_against = ".$batch_against_id; else $batch_against_cond = "";

	if($load_unload_id ==1 || $load_unload_id ==2)
	{
		$load_unload_cond = " having max(e.load_unload_id) = $load_unload_id";
	}
	else if($load_unload_id ==3 )
	{
		$load_unload_cond = " having max(e.load_unload_id) is null";
	}


	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}

	if($batch_type==0 || $batch_type==1)
	{
		if($txt_booking_no_show !="")
		{
			$booking_no = "'".implode("','",explode(",", $txt_booking_no_show))."'";
		}

		$booking="";$fso_nos="";
		if ($jobdata != "" || $year_cond != "" || $buyer_cond != "")
		{
			$jobNo_arr=sql_select("select c.booking_no from wo_po_details_master a,wo_po_break_down b,wo_booking_dtls c where c.po_break_down_id=c.po_break_down_id $jobdata $year_cond $buyer_cond and a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1");

			foreach ($jobNo_arr as $value)
			{
				$booking_no .= ($booking_no=="")? "'".$value[csf("booking_no")]."'" : ",'".$value[csf("booking_no")]."'";
			}
		}

		$sales_orders_cond="";
		if($fso_number_show != ""){
			$sales_orders="";
			foreach (explode(",", $fso_number_show) as $row)
			{
				$sales_orders.= ($sales_orders=="") ? "'".$row."'" : ",'".$row."'";
			}

			if($sales_orders)
			{
				$sales_orders_cond ="and a.job_no in ($sales_orders)";
			}
		}
		$all_booking_no_cond="";
		if($booking_no)
		{
			$booking_no = implode(",",array_filter(array_unique(explode(",", $booking_no))));
			$book_arr = explode(",", $booking_no);
			if($db_type==0)
			{
				$all_booking_no_cond=" and a.sales_booking_no in ( $booking_no)";
			}
			else
			{
				if(count($book_arr)>999)
				{
					$book_chunk_arr=array_chunk($book_arr, 999);
					$all_booking_no_cond=" and (";
					foreach ($book_chunk_arr as $value)
					{
						$all_booking_no_cond .="a.sales_booking_no in (".implode(",", $value).") or ";
					}
					$all_booking_no_cond=chop($all_booking_no_cond,"or ");
					$all_booking_no_cond.=")";
				}
				else
				{
					$all_booking_no_cond=" and a.sales_booking_no in ( $booking_no)";
				}
			}
		}

		if($all_booking_no_cond != "" || $sales_orders_cond != "" || $style_no_cond != "")
		{
		 	$sql_sales_order= "SELECT a.id,a.job_no from  fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sales_orders_cond $all_booking_no_cond $style_no_cond group by a.id,a.job_no";
			$result_data=sql_select($sql_sales_order);
			foreach ($result_data as $value)
			{
				$sales_ord_wise_fso_arr[$value[csf("id")]]=$value[csf("id")];
			}

			$fso_nos = implode(",", $sales_ord_wise_fso_arr);
		}

		$all_fso_nos_arr = array_filter($sales_ord_wise_fso_arr);
		$all_fso_no_cond=""; $fsoCond="";
		if(!empty($all_fso_nos_arr))
		{
			$all_fso_nos = implode(",", $all_fso_nos_arr);
	        if($db_type==2 && count($all_fso_nos_arr)>999)
	        {
	        	$all_fso_nos_arr_chunk=array_chunk($all_fso_nos_arr,999) ;
	        	foreach($all_fso_nos_arr_chunk as $chunk_arr)
	        	{
	        		$chunk_arr_value=implode(",",$chunk_arr);
	        		$fsoCond.=" c.id in($chunk_arr_value) or ";
	        	}
	        	$all_fso_no_cond.=" and (".chop($fsoCond,'or ').")";
	        }
	        else
	        {
	        	$all_fso_no_cond=" and c.id in($all_fso_nos)";
	        }
		}

		//echo "". $all_booking_no_cond ."=". $sales_orders_cond ."=" .$style_no_cond ."=". $fso_cond;


		if($txt_int_ref_no !="")
		{
			$int_ref_no_cond = " and b.grouping like '%".$txt_int_ref_no."%'";
			$int_ref_sql = "SELECT a.booking_mst_id,a.po_break_down_id, b.grouping FROM wo_booking_dtls a, wo_po_break_down b WHERE a.po_break_down_id = b.id $int_ref_no_cond group by a.booking_mst_id,a.booking_no,a.po_break_down_id, b.grouping";
			//echo $int_ref_sql;
			$int_ref_sql_rslt=sql_select($int_ref_sql);
			$bookingIdsChk = array();
			$bookingIdsArr = array();
			foreach($int_ref_sql_rslt as $row)
			{
				if($bookingIdsChk[$row[csf('booking_mst_id')]] == "")
				{
					$bookingIdsChk[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
					array_push($bookingIdsArr,$row[csf('booking_mst_id')]);
				}
			}
			unset($int_ref_sql_rslt);
			if(!empty($bookingIdsArr))
			{
				$bookingIdsCond="".where_con_using_array($bookingIdsArr,0,'c.booking_id')."";
			}
		}

		$con = connect();	
		$r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ENTRY_FORM = 180");
		$r_id=execute_query("delete from tmp_barcode_no where userid=$user_name and entry_form=180");
		oci_commit($con);

		if($operation==1) // Show
		{
			if((($all_booking_no_cond != "" || $sales_orders_cond != "" || $style_no_cond != "") && $all_fso_no_cond != "") || ($all_booking_no_cond == "" && $sales_orders_cond == "" && $style_no_cond =="" && $all_fso_no_cond == ""))
			{
				$sql="SELECT a.id, a.company_id, a.working_company_id, a.batch_against,a.entry_form,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id, a.color_range_id,a.booking_no,a.extention_no, a.batch_sl_no, a.booking_no_id, a.remarks, a.process_id,a.floor_id,a.shift_id,a.dyeing_machine, b.item_description,b.prod_id,b.po_id, b.batch_qnty as batch_qnty, c.job_no as fso_no,c.style_ref_no,c.po_buyer,c.buyer_id,c.within_group,b.body_part_id, b.color_type AS color_type_ids, b.id as roll_no, c.customer_buyer,c.booking_id, b.program_no,b.barcode_no, b.width_dia_type, e.load_unload_id
				from pro_batch_create_mst a
				left join pro_fab_subprocess e on a.id = e.batch_id and e.status_active =1,
				pro_batch_create_dtls b, fabric_sales_order_mst c
				where a.company_id in($company) and a.status_active=1 $dates_com $batch_num $ext_no $all_fso_no_cond $batch_against_cond $style_no_cond_2 $bookingIdsCond and a.is_sales=1 and a.id=b.mst_id and b.po_id=c.id and b.is_deleted=0 and c.status_active=1 and b.barcode_no!=0 $load_unload_cond
				order by a.id,a.extention_no,b.prod_id desc";
				// echo $sql;
				$batchdata=sql_select($sql);
			}
		}

		//grouping=[batch_id][item_description][prod_id][po_id][body_part_id]

		// echo $sql;
		$grey_production_dtls_id_arr = array();
		$all_sales_id_arr = array();
		$bookingIdChk = array();
		$bookingIdArr = array();
		$programNoChk = array();
		$programNoArr = array();
		$all_barcode_arr=array();
		if($batch_type==0 || $batch_type==1)
		{
			foreach($batchdata as $batch)
			{
				// $grey_production_dtls_id_arr[] = $batch[csf("dtls_ids")];
				$sales_ord_wise_fso_arr[$batch[csf("po_id")]]=$batch[csf("po_id")];
				$all_batch_arr[$batch[csf("id")]]=$batch[csf("id")];

				array_push($all_sales_id_arr,$batch[csf("po_id")]);
				if($batch[csf("booking_id")] !="")
				{
					if($bookingIdChk[$batch[csf('booking_id')]] == "")
					{
						$bookingIdChk[$batch[csf('booking_id')]] = $batch[csf('booking_id')];
						array_push($bookingIdArr,$batch[csf('booking_id')]);
					}
				}

				if($batch[csf("program_no")] !="")
				{
					if($programNoChk[$batch[csf('program_no')]] == "")
					{
						$programNoChk[$batch[csf('program_no')]] = $batch[csf('program_no')];
						$programNoArr[$batch[csf('program_no')]] = $batch[csf('program_no')];
					}
				}

				$barcode_arr[$batch[csf("barcode_no")]]=$batch[csf("barcode_no")];
				$all_barcode_arr[$batch[csf("barcode_no")]]=$batch[csf("barcode_no")];
			}
		}

		// echo "<pre>";print_r($barcode_arr);die;
		foreach ($barcode_arr as $key => $barcodeno) 
		{
			// echo $key.'='.$barcodeno.'<br>';
			if( $barcode_no_check[$barcodeno] =="" )
            {
                $barcode_no_check[$barcodeno]=$barcodeno;
                // echo "insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_name,$barcodeno, 180)";
                $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form,type) values ($user_name,$barcodeno, 180,1)");
            }
		}
		oci_commit($con);

		if (!empty($barcode_arr)) 
		{
			$split_sql="SELECT c.barcode_no as mother_barcode, d.barcode_no , d.qnty, d.qc_pass_qnty_pcs, d.coller_cuff_size from tmp_barcode_no g, pro_roll_split c, pro_roll_details d
			where g.barcode_no=c.barcode_no and  g.userid=$user_name and g.entry_form=180 and g.type=1 and c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1";
			// echo $split_sql;die;
			$split_sql_data = sql_select($split_sql);
			foreach ($split_sql_data as $key => $row)
			{
				//$split_data_array[$row[csf('mother_barcode')]]["qc_pass_qnty_pcs"] = $row[csf('qc_pass_qnty_pcs')];
				$all_barcode_arr[$row[csf('mother_barcode')]] = $row[csf('mother_barcode')];
				$mother_barcode_array[$row[csf('barcode_no')]]['mom'] = $row[csf('mother_barcode')];
			}
			unset($split_sql_data);

			// create batch using child barcode but mother barcode not in this batch
			$split_sql="SELECT d.barcode_no,e.barcode_no as mother_barcode, d.qnty, d.qc_pass_qnty_pcs, d.coller_cuff_size 
			from tmp_barcode_no g, pro_roll_details d, pro_roll_details e 
			where g.barcode_no=d.barcode_no and d.roll_split_from=e.ID and d.status_active = 1 and e.status_active = 1 and d.ENTRY_FORM= 62 and e.ENTRY_FORM= 62 and  g.userid=$user_name and g.entry_form=180 and g.type=1";
			// echo $split_sql;
			$split_sql_data = sql_select($split_sql);
			foreach ($split_sql_data as $key => $row)
			{
				$all_barcode_arr[$row[csf('mother_barcode')]] = $row[csf('mother_barcode')];
				$mother_barcode_array[$row[csf('barcode_no')]]['mom'] = $row[csf('mother_barcode')];
			}
			unset($split_sql_data);
			// echo "<pre>";print_r($all_barcode_arr);die;

			foreach ($all_barcode_arr as $key => $barcode) 
			{
				if( $barcode_check[$barcode] =="" )
	            {
	                $barcode_check[$barcode]=$barcode;
	                $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form,type) values ($user_name,$barcode, 180,2)");
	            }
			}
			oci_commit($con);

			$production_dtls = "SELECT d.id as dtls_id, e.receive_basis,e.booking_id, d.febric_description_id,d.gsm,d.width, d.machine_dia,d.machine_gg,d.machine_no_id, d.yarn_lot, d.yarn_count, d.stitch_length, d.brand_id, e.knitting_source, e.knitting_company, c.barcode_no, p.detarmination_id 
			from tmp_barcode_no g, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e, product_details_master p 
			where g.barcode_no=c.barcode_no and c.dtls_id=d.id and d.mst_id=e.id and d.prod_id = p.id and e.entry_form in(2,22) and c.entry_form in(2,22) and g.userid=$user_name and g.entry_form=180 and g.type=2";
			// echo $production_dtls;die;
			$production_data = sql_select($production_dtls);
			foreach ($production_data as $row)
			{
				$production_info[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
				$production_info[$row[csf("barcode_no")]]['booking_id']=$row[csf("booking_id")];
				$production_info[$row[csf("barcode_no")]]['febric_description_id']=$row[csf("febric_description_id")];
				$production_info[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
				$production_info[$row[csf("barcode_no")]]['width']=$row[csf("width")];
				$production_info[$row[csf("barcode_no")]]['machine_dia']=$row[csf("machine_dia")];
				$production_info[$row[csf("barcode_no")]]['machine_gg']=$row[csf("machine_gg")];
				$production_info[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
				$production_info[$row[csf("barcode_no")]]['yarn_lot']=$row[csf("yarn_lot")];
				$production_info[$row[csf("barcode_no")]]['yarn_count']=$row[csf("yarn_count")];
				$production_info[$row[csf("barcode_no")]]['stitch_length']=$row[csf("stitch_length")];
				$production_info[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
				$production_info[$row[csf("barcode_no")]]['knitting_source']=$row[csf("knitting_source")];
				$production_info[$row[csf("barcode_no")]]['knitting_company']=$row[csf("knitting_company")];
				$production_info[$row[csf("barcode_no")]]['detarmination_id']=$row[csf("detarmination_id")];
				$production_info[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];

				$grey_production_dtls_id_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
			}
			unset($production_data);
		}
		// echo "<pre>";print_r($all_barcode_arr);die;

		$max = 0;
		/*function get_highest($arr) {
			$max = $arr[0];
			foreach($arr as $obj) { 
				$num = $obj['LOAD_UNLOAD_ID'];
				if($num > $max['LOAD_UNLOAD_ID']) { 
					$max = $obj;
				}
			}
			return $max;
		}
		$max_data_arr=get_highest($batchdata);*/

		foreach($batchdata as $row)
		{
			if($mother_barcode_array[$row[csf('barcode_no')]]['mom']!="")
			{
				$barcode_number= $mother_barcode_array[$row[csf('barcode_no')]]['mom'];
			}
			else
			{
				$barcode_number= $row[csf("barcode_no")];
			}

			$yarn_lot = $production_info[$barcode_number]['yarn_lot'];
			$yarn_count = $production_info[$barcode_number]['yarn_count'];
			$brand_id = $production_info[$barcode_number]['brand_id'];
			$dtls_id = $production_info[$barcode_number]['dtls_id'];

			$dataString = $row[csf("id")].'__'.$row[csf("prod_id")].'__'.$row[csf('po_id')].'__'.$row[csf("body_part_id")].'__'.$row[csf("width_dia_type")];

			$batch_data_arr[$dataString]['id']=$row[csf("id")];
			$batch_data_arr[$dataString]['prod_id']=$row[csf("prod_id")];
			$batch_data_arr[$dataString]['po_id']=$row[csf("po_id")];
			$batch_data_arr[$dataString]['body_part_id']=$row[csf("body_part_id")];
			$batch_data_arr[$dataString]['width_dia_type']=$row[csf("width_dia_type")];
			$batch_data_arr[$dataString]['item_description']=$row[csf("item_description")];
			$batch_data_arr[$dataString]['company_id']=$row[csf("company_id")];
			$batch_data_arr[$dataString]['working_company_id']=$row[csf("working_company_id")];
			$batch_data_arr[$dataString]['batch_against']=$row[csf("batch_against")];
			$batch_data_arr[$dataString]['entry_form']=$row[csf("entry_form")];
			$batch_data_arr[$dataString]['batch_no']=$row[csf("batch_no")];
			$batch_data_arr[$dataString]['batch_date']=$row[csf("batch_date")];
			$batch_data_arr[$dataString]['batch_weight']=$row[csf("batch_weight")];
			$batch_data_arr[$dataString]['total_trims_weight']=$row[csf("total_trims_weight")];
			$batch_data_arr[$dataString]['color_id']=$row[csf("color_id")];
			$batch_data_arr[$dataString]['color_range_id']=$row[csf("color_range_id")];
			$batch_data_arr[$dataString]['booking_no']=$row[csf("booking_no")];
			$batch_data_arr[$dataString]['extention_no']=$row[csf("extention_no")];
			$batch_data_arr[$dataString]['batch_sl_no']=$row[csf("batch_sl_no")];
			$batch_data_arr[$dataString]['booking_no_id']=$row[csf("booking_no_id")];
			$batch_data_arr[$dataString]['booking_no_id']=$row[csf("booking_no_id")];
			$batch_data_arr[$dataString]['item_description']=$row[csf("item_description")];
			$batch_data_arr[$dataString]['style_ref_no'] =$row[csf("style_ref_no")];
			$batch_data_arr[$dataString]['dyeing_machine'] =$row[csf("dyeing_machine")];			
			$batch_data_arr[$dataString]['within_group'] =$row[csf("within_group")];			
			$batch_data_arr[$dataString]['remarks'] =$row[csf("remarks")];
			$batch_data_arr[$dataString]['process_id'] =$row[csf("process_id")];
			$batch_data_arr[$dataString]['floor_id'] =$row[csf("floor_id")];
			$batch_data_arr[$dataString]['shift_id'] =$row[csf("shift_id")];
			$batch_data_arr[$dataString]['po_buyer'] =$row[csf("po_buyer")];
			$batch_data_arr[$dataString]['buyer_id'] =$row[csf("buyer_id")];
			$batch_data_arr[$dataString]['fso_no'] =$row[csf("fso_no")];
			$batch_data_arr[$dataString]['color_type_ids'] .=$row[csf("color_type_ids")].',';
			$batch_data_arr[$dataString]['dtls_ids'] .=$dtls_id.',';			
			$batch_data_arr[$dataString]['customer_buyer'] =$row[csf("customer_buyer")];
			$batch_data_arr[$dataString]['booking_id'] =$row[csf("booking_id")];
			$batch_data_arr[$dataString]['program_no'] =$row[csf("program_no")];			
			$batch_data_arr[$dataString]['yarn_lot']=$yarn_lot;
			$batch_data_arr[$dataString]['yarn_count']=$yarn_count;
			$batch_data_arr[$dataString]['brand_id']=$brand_id;

			if ($dtls_id_check[$row[csf("roll_no")]]=="") 
			{
				$dtls_id_check[$row[csf("roll_no")]]=$row[csf("roll_no")];
				$batch_data_arr[$dataString]['batch_qnty'] +=$row[csf("batch_qnty")];
				$batch_data_arr[$dataString]['roll_no'] +=count($row[csf("roll_no")]);
			}

			if ($row[csf("load_unload_id")] > $max) {
		        // Update max if the current number is greater
		        $max = $row[csf("load_unload_id")];
		    }
			// $batch_data_arr[$dataString]['load_unload_id'] =$max_data_arr['LOAD_UNLOAD_ID'];
			$batch_data_arr[$dataString]['load_unload_id'] =$max;

			
		}
		//echo $max;die;
		// echo "<pre>";print_r($batch_data_arr);die;

		$programNoArr = array_filter($programNoArr);
		if(!empty($programNoArr))
		{	
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 180, 1,$programNoArr, $empty_arr); //recv id
			//die;
			$program_sql = "SELECT a.id as program_no, a.stitch_length from ppl_planning_info_entry_dtls a, gbl_temp_engine x where a.status_active=1 and a.is_deleted=0 and is_sales=1 and a.id=x.ref_val and x.user_id=$user_name and x.entry_form=180 and x.ref_from=1";
			
			//echo $program_sql;die;
			$program_rslt = sql_select($program_sql);

			$program_info_arr = array();
			foreach($program_rslt as $row)
			{
				$program_info_arr[$row[csf('program_no')]]['stitch_length'] = $row[csf('stitch_length')];
			}
			unset($program_rslt);
		}

		if(!empty($bookingIdArr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 180, 2,$bookingIdArr, $empty_arr); //recv id

			$int_ref_sql = "SELECT a.booking_mst_id, a.po_break_down_id, b.grouping, b.pub_shipment_date
			FROM GBL_TEMP_ENGINE g, wo_booking_dtls a, wo_po_break_down b 
			WHERE g.ref_val=a.booking_mst_id and g.user_id=$user_name and g.entry_form=180 and g.ref_from=2 and a.po_break_down_id = b.id group by a.booking_mst_id,a.booking_no,a.po_break_down_id, b.grouping, b.pub_shipment_date";
			//echo $int_ref_sql; //".where_con_using_array($bookingIdArr,0,'a.booking_mst_id')."
			$int_ref_sql_rslt=sql_select($int_ref_sql);
			$int_ref_arr = array();
			foreach($int_ref_sql_rslt as $row)
			{
				$int_ref_arr[$row[csf("booking_mst_id")]]['int_ref'] .=$row[csf("grouping")].',';
				$shipment_date_arr[$row[csf("booking_mst_id")]]["ship_date"].= change_date_format($row[csf("pub_shipment_date")]).",";
			}
			unset($int_ref_sql_rslt);
			//echo "<pre>";print_r($int_ref_arr);
		}

		$yarn_lot_arr=array();
		$grey_production_dtls_id_arr = array_filter(array_unique(explode(",",implode(",",$grey_production_dtls_id_arr))));
		if(!empty($grey_production_dtls_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 180, 3,$grey_production_dtls_id_arr, $empty_arr); //dtls_id

			$yarn_lot_data=sql_select("SELECT a.id dtls_id,b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot,a.yarn_prod_id, a.machine_no_id, a.machine_dia, a.machine_gg 
			from GBL_TEMP_ENGINE g, pro_grey_prod_entry_dtls a, order_wise_pro_details b 
			where g.ref_val=a.id and g.user_id=$user_name and g.entry_form=180 and g.ref_from=3 and a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $all_grey_production_dtls_id_cond group by a.id,a.prod_id,a.yarn_lot, b.po_breakdown_id,a.yarn_prod_id,a.machine_no_id,a.machine_dia, a.machine_gg");
			//and a.yarn_prod_id is not null

			foreach($yarn_lot_data as $rows)
			{
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['yarn_prod_id'] 	.= $rows[csf('yarn_prod_id')].',';
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_no_id'] 	.= $rows[csf('machine_no_id')].',';
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_dia'] 		.= $rows[csf('machine_dia')].',';
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_gg'] 		.= $rows[csf('machine_gg')].',';
				$yarn_prod_id_arr[$rows[csf('yarn_prod_id')]] = $rows[csf('yarn_prod_id')];
			}
		}

		$machine_name_arr  = return_library_array("select id,machine_no || '-' || brand as machine_name from lib_machine_name where status_active in (1,2) and is_deleted=0 and is_locked=0 order by seq_no","id","machine_name");

		$all_yarn_prod_id_arr = array_filter(array_unique(explode(",",implode(",", $yarn_prod_id_arr))));
		if(!empty($all_yarn_prod_id_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 180, 4,$all_yarn_prod_id_arr, $empty_arr); //yarn_prod_id

	        $yarn_sql = sql_select("SELECT a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.brand  
	        from GBL_TEMP_ENGINE g, product_details_master a 
	        where g.ref_val=a.id and g.user_id=$user_name and g.entry_form=180 and g.ref_from=4 and a.status_active =1 ");//$all_yarn_prod_id_cond

	        foreach ($yarn_sql as $row)
	        {
	        	$yarn_ref_arr[$row[csf("id")]]["lot"] = $row[csf("lot")];
	        	$yarn_ref_arr[$row[csf("id")]]["yarn_count_id"] = $row[csf("yarn_count_id")];
	        	$yarn_ref_arr[$row[csf("id")]]["yarn_comp_type1st"] = $row[csf("yarn_comp_type1st")];
	        	$yarn_ref_arr[$row[csf("id")]]["yarn_type"] = $row[csf("yarn_type")];
	        	$yarn_ref_arr[$row[csf("id")]]["brand"] = $row[csf("brand")];
	        }
	    }

	    // echo "<pre>";print_r($sales_ord_wise_fso_arr);die;
	    if(!empty($sales_ord_wise_fso_arr))
		{
		    fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 180, 5,$sales_ord_wise_fso_arr, $empty_arr); //fso id

			$job_fso_chk=array();$job_from_fso_arr=array();

			$job_from_fso =  sql_select("SELECT c.booking_no,c.booking_type,c.is_short, b.job_no_prefix_num,b.job_no, a.job_no as fso_no, d.short_booking_type, d.company_id,d.po_break_down_id,d.item_category,d.fabric_source,d.job_no,d.is_approved
			from GBL_TEMP_ENGINE g, fabric_sales_order_mst a, wo_booking_mst d, wo_booking_dtls c, wo_po_details_master b
			where g.ref_val=a.id and g.user_id=$user_name and g.entry_form=180 and g.ref_from=5 and a.booking_id = d.id and d.id=c.booking_mst_id and c.job_no=b.job_no and d.job_no=b.job_no and a.company_id in($company) and a.within_group=1
			UNION ALL 
			SELECT b.booking_no,4 as booking_type,0 as is_short,0 as job_no_prefix_num,null as job_no, a.job_no as fso_no, null as short_booking_type, b.company_id, null as po_break_down_id,b.item_category,b.fabric_source,b.job_no,b.is_approved
			from GBL_TEMP_ENGINE g, fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b 
			where g.ref_val=a.id and g.user_id=$user_name and g.entry_form=180 and g.ref_from=5 and a.within_group=1 and a.sales_booking_no=b.booking_no and a.company_id in($company)");
			foreach ($job_from_fso as $val)// $fso_no_cond
			{
				if($job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] == "")
				{
					$job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
					$job_from_fso_arr[$val[csf("fso_no")]]["job_no"] .= $val[csf("job_no_prefix_num")].",";

					$short_booking_type_arr[$val[csf("booking_no")]]=$short_booking_type[$val[csf("short_booking_type")]];
					if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
					{
						$booking_type_arr[$val[csf("booking_no")]]="Main";
					}
					else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
					{
						$booking_type_arr[$val[csf("booking_no")]]="Short";
					}
					else if($val[csf("booking_type")]==4)
					{
						$booking_type_arr[$val[csf("booking_no")]]="Sample";
					}

					$booking_Arr[$val[csf('booking_no')]]['booking_company_id'] = $val[csf('company_id')];
			        $booking_Arr[$val[csf('booking_no')]]['booking_order_id'] = $val[csf('po_break_down_id')];
			        $booking_Arr[$val[csf('booking_no')]]['booking_fabric_natu'] = $val[csf('item_category')];
			        $booking_Arr[$val[csf('booking_no')]]['booking_fabric_source'] = $val[csf('fabric_source')];
			        $booking_Arr[$val[csf('booking_no')]]['booking_job_no'] = $val[csf('job_no')];
			        $booking_Arr[$val[csf('booking_no')]]['is_approved'] = $val[csf('is_approved')];
				}
			}
		}
		// echo "<pre>";print_r($booking_Arr);die;

		if(!empty($barcode_arr)) // for roll issue no and recv by batch no
		{
			// for roll issue no
			$roll_issue_data=sql_select("SELECT a.mst_id as batch_id, b.mst_id, c.issue_number_prefix_num as issue_id
			from TMP_BARCODE_NO g, PRO_BATCH_CREATE_DTLS a, PRO_ROLL_DETAILS b, INV_ISSUE_MASTER c  
			where g.barcode_no=a.barcode_no and a.barcode_no=b.barcode_no  and b.mst_id=c.id and b.entry_form in(61) and c.entry_form in(61) and  g.userid=$user_name and g.entry_form=180 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			group by a.mst_id, b.mst_id, c.issue_number_prefix_num");
			foreach($roll_issue_data as $rows)
			{
				$issue_no_arr[$rows[csf('batch_id')]].=$rows[csf('issue_id')].',';
			}

			$roll_recb_by_batch_data=sql_select("SELECT a.mst_id as batch_id, b.mst_id, c.recv_number_prefix_num as receive_id
			from TMP_BARCODE_NO g, PRO_BATCH_CREATE_DTLS a, PRO_ROLL_DETAILS b, INV_RECEIVE_MAS_BATCHROLL c  
			where g.barcode_no=a.barcode_no and a.barcode_no=b.barcode_no  and b.mst_id=c.id and b.entry_form in(62) and c.entry_form in(62) and  g.userid=$user_name and g.entry_form=180 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			group by a.mst_id, b.mst_id, c.recv_number_prefix_num");
			foreach($roll_recb_by_batch_data as $rows)
			{
				$recv_by_batch_no_arr[$rows[csf('batch_id')]].=$rows[csf('receive_id')].',';
			}
		}
		// echo "<pre>";print_r($issue_no_arr);die;

		// Color Wise Grey Booking Qty
		if(!empty($sales_ord_wise_fso_arr))
		{
			$job_from_fso =  sql_select("SELECT a.job_no as fso_no, b.body_part_id, b.color_id, b.grey_qty, b.process_loss, c.batch_no, c.id as batch_id
			from GBL_TEMP_ENGINE g, fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_batch_create_mst c 
			where g.ref_val=a.id and b.mst_id=c.SALES_ORDER_ID and a.id=c.SALES_ORDER_ID
			and g.user_id=$user_name and g.entry_form=180 and g.ref_from=5 and a.id = b.mst_id and a.company_id in($company) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0");
			foreach ($job_from_fso as $val)
			{
				$grey_qty_arr[$val[csf('batch_id')]][$val[csf('color_id')]][$val[csf('body_part_id')]]+=$val[csf('grey_qty')];
				$process_loss_arr[$val[csf('batch_id')]][$val[csf('color_id')]][$val[csf('body_part_id')]]+=$val[csf('process_loss')];
			}
		}
	}

	// SubCon Sql not use for ccl
	if(($batch_type==0 || $batch_type==2) && ($txt_booking_no_show == "" && $fso_number_show == ""))
	{
		$all_search_ord_cond=""; $searchCond="";
		if($cust_style_cond != "" || $jobdata != "" || $party_cond != "")
		{
			$sub_product_sql = sql_select("SELECT a.subcon_job, a.party_id, b.id, b.order_no, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form =238 and a.company_id =$company and a.status_active =1 and b.status_active =1 and a.subcon_job = b.job_no_mst $cust_style_cond $jobdata $year_cond $party_cond");

	        foreach ($sub_product_sql as $row)
	        {
				$search_ord_arr[$row[csf('id')]]=$row[csf('id')];
	        }
	        $search_ord_arr = array_filter($search_ord_arr);
	        if(count($search_ord_arr) > 0)
	        {
		        $all_search_ord_ids = implode(",", $search_ord_arr);
		        if($db_type==2 && count($search_ord_arr)>999)
		        {
		        	$search_ord_arr_chunk=array_chunk($search_ord_arr,999) ;
		        	foreach($search_ord_arr_chunk as $chunk_arr)
		        	{
		        		$chunk_arr_value=implode(",",$chunk_arr);
		        		$searchCond.="  b.po_id in($chunk_arr_value) or ";
		        	}

		        	$all_search_ord_cond.=" and (".chop($searchCond,'or ').")";
		        }
		        else
		        {
		        	$all_search_ord_cond=" and b.po_id in($all_search_ord_ids)";
		        }
		    }
	    }

	    if((($cust_style_cond != "" || $jobdata != "" || $party_cond != "") && $all_search_ord_cond != "") || ($cust_style_cond == "" && $jobdata == "" && $party_cond == ""))
	    {
			$sub_cond= "SELECT x.*, max(e.load_unload_id) as load_unload_id
			from (select a.id, a.batch_against, a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.total_trims_weight, a.color_id, a.booking_no, a.extention_no, a.working_company_id, a.batch_sl_no, a.booking_no_id, b.item_description, b.grey_dia, b.gsm, b.prod_id, b.po_id, sum(b.batch_qnty) as batch_qnty, '' as fso_no, '' as style_ref_no, a.machine_no, '' as dtls_ids, 0 as po_buyer, count(b.roll_no) as roll_no, a.remarks, a.process_id,a.floor_id from pro_batch_create_mst a, pro_batch_create_dtls b
			where a.company_id=$company and a.status_active=1 $dates_com $batch_num $ext_no $all_search_ord_cond and a.id=b.mst_id and b.is_deleted=0 and a.entry_form =36
			group by a.id, a.batch_against, a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.working_company_id, a.batch_sl_no, a.booking_no_id, b.item_description, b.grey_dia, b.gsm, b.prod_id, b.po_id,  a.machine_no, a.total_trims_weight, a.remarks, a.process_id,a.floor_id
			order by a.batch_no
			) x left join pro_fab_subprocess e on x.id = e.batch_id and e.status_active =1
			group by x.id, x.batch_against, x.entry_form, x.batch_no, x.batch_date, x.batch_weight, x.total_trims_weight, x.color_id, x.booking_no, x.extention_no, x.working_company_id, x.batch_sl_no, x.booking_no_id, x.item_description, x.grey_dia, x.gsm, x.prod_id, x.po_id, x.batch_qnty,  x.fso_no,  x.style_ref_no, x.machine_no,  x.dtls_ids,  x.po_buyer,  x.roll_no, x.remarks,x.floor_id, x.process_id $load_unload_cond";
			//echo $sub_cond;
			$subbatchdata=sql_select($sub_cond);
	    }

		//echo $sub_cond;die;

		foreach($subbatchdata as $batch)
		{
			$subcon_ord_arr[$batch[csf("po_id")]]=$batch[csf("po_id")];
			$all_subcon_batch_id_arr[$batch[csf("id")]]=$batch[csf("id")];
		}

		if(!empty($subcon_ord_arr))
		{
			$all_subcon_ord_ids = "'".implode("','", $subcon_ord_arr)."'";
			$subcon_ord_arr = explode(",", $all_subcon_ord_ids);

	        $all_subcon_ord_arr_cond=""; $subOrdCond="";
	        if($db_type==2 && count($subcon_ord_arr)>999)
	        {
	        	$subcon_ord_arr_chunk=array_chunk($subcon_ord_arr,999) ;
	        	foreach($subcon_ord_arr_chunk as $chunk_arr)
	        	{
	        		$chunk_arr_value=implode(",",$chunk_arr);
	        		$subOrdCond.="  b.id in($chunk_arr_value) or ";
	        	}

	        	$all_subcon_ord_arr_cond.=" and (".chop($subOrdCond,'or ').")";
	        }
	        else
	        {
	        	$all_subcon_ord_arr_cond=" and b.id in($all_subcon_ord_ids)";
	        }

	        $sub_product_sql = sql_select("SELECT a.subcon_job, a.party_id, b.id, b.order_no, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.company_id =$company and a.status_active =1 and b.status_active =1 and a.subcon_job = b.job_no_mst $all_subcon_ord_arr_cond $cust_style_cond");// a.entry_form =238 and
			//echo "select a.subcon_job, a.party_id, b.id, b.order_no, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form =238 and a.company_id =$company and a.status_active =1 and b.status_active =1 and a.subcon_job = b.job_no_mst $all_subcon_ord_arr_cond $cust_style_cond";
	        $sub_ord_arr=array();
	        foreach ($sub_product_sql as $row)
	        {
	        	$sub_ord_arr[$row[csf('id')]]['job']=$row[csf('subcon_job')];
				$sub_ord_arr[$row[csf('id')]]['party']=$row[csf('party_id')];
				$sub_ord_arr[$row[csf('id')]]['order']=$row[csf('order_no')];
				$sub_ord_arr[$row[csf('id')]]['style_ref']=$row[csf('cust_style_ref')];
	        }
		}
	}

	$yarn_count_arr  = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0","id","yarn_count");
	$brand_name_arr  = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0","id","brand_name");
	$load_unload_arr = array(1=>"Loading",2=>"Un-loading",3=>"Waiting for loading");

	$batch_print_report_format=return_field_value("format_id","lib_report_template","template_name in($company) and module_id=7 and report_id=56 and is_deleted=0 and status_active=1");
    $batch_format_ids=explode(",",$batch_print_report_format);
    $print_btn=$batch_format_ids[0];

    $roll_level= sql_select("SELECT fabric_roll_level from variable_settings_production where company_name in($company) and item_category_id=50 and variable_list=3 and status_active=1 and is_deleted= 0 order by id");
	foreach($roll_level as $row)
	{
		$roll_maintained = $row[csf('fabric_roll_level')];
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ENTRY_FORM=180");
	execute_query("DELETE from tmp_barcode_no where userid=$user_name and entry_form=180");
	oci_commit($con);
	disconnect($con);

	foreach($batch_data_arr as $batch)
	{
		$key_1 = $batch[("id")];
		$gData_1[$key_1] += 1;
	}

	ob_start();
	if ($operation==1) // Show
	{
		?>
		<style type="text/css">
			.word_wrap_break {
				word-break: break-all;
				word-wrap: break-word;
			}
		</style>
	    <div align="left">
	        <fieldset style="width:2585px;">
	        	<?
	        	if(count($batchdata)>0 || count($subbatchdata)>0)
	        	{
	        		?>
		            <div  align="center">
						<strong> <? echo $company_library[$company]; ?> </strong>
		            <br><b>
		                <?

		                $date_head="";
		                if( $date_from)
		                {
		                	$date_head .= change_date_format($date_from).' To ';
		                }
		                if( $date_to)
		                {
		                	$date_head .= change_date_format($date_to);
		                }
		                echo $date_head;
		                ?> </b>
		            </div>
		        	<?
		        }else{
		        	echo "<b>Data Not Found</b>";
		        }
		        ?>
	            <div align="left">
					<?
	                if(($batch_type==0 || $batch_type==1) && count($batchdata)>0)
	                {
	                	?>
	                    <div align="center">
							<b>Self Batch </b>
							<br>
							<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
						</div>
	                    <table class="rpt_table" width="3035" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	                        <thead>
	                            <tr>
	                                <th width="30" class="word_wrap_break">SL</th>
	                                <th width="75" class="word_wrap_break">Batch Date</th>
	                                <th width="100" class="word_wrap_break">LC Company</th>
	                                <th width="100" class="word_wrap_break">Working Company</th>
	                                <th width="80" class="word_wrap_break">Color Range</th>
	                                <th width="80" class="word_wrap_break">Color Type</th>
	                                <th width="80" class="word_wrap_break">Buyer</th>
	                                <th width="100" class="word_wrap_break">Internal Ref No.</th>
	                                <th width="110" class="word_wrap_break">FSO No</th>
	                                <th width="60" class="word_wrap_break">Job No</th>
	                                <th width="120" class="word_wrap_break">Fabric Booking No.</th>
	                                <th width="50" class="word_wrap_break">Booking Type</th>
	                                <th width="70" class="word_wrap_break">Issue Id</th>
	                                <th width="70" class="word_wrap_break">Received Id</th>
	                                <th width="100" class="word_wrap_break">Batch No</th>
	                                <th width="50" class="word_wrap_break">M/C No</th>
	                                <th width="80" class="word_wrap_break">Batch Color</th>
	                                <th width="75" class="word_wrap_break">Ship Date</th>
	                                <th width="80" class="word_wrap_break">Batch Against</th>
	                                <th width="70" class="word_wrap_break">Body Part</th>
	                                <th width="100" class="word_wrap_break">Construction</th>
	                                <th width="150" class="word_wrap_break">Fab. Composition</th>
	                                <th width="100" class="word_wrap_break">Dia Type</th>
	                                <th width="50" class="word_wrap_break">Dia/ Width</th>
	                                <th width="50" class="word_wrap_break">GSM</th>	                                
	                                <th width="70" class="word_wrap_break">Yarn Count</th>
	                                <th width="70" class="word_wrap_break">Stich Length</th>
	                                <th width="70" class="word_wrap_break">Yarn Brand</th>
	                                <th width="100" class="word_wrap_break">Y.Lot No</th>
	                                <th width="70" class="word_wrap_break">Fabric Weight.</th>
	                                <th width="70" class="word_wrap_break">Trims Weight</th>
	                                <th width="70" class="word_wrap_break">Total Batch Weight</th>
	                                <th width="40" class="word_wrap_break">No of Roll</th>
	                                <th width="120" class="word_wrap_break">Color Wise Grey Booking Qty</th>
	                                <th width="70" class="word_wrap_break">Booking PL%</th>
	                                 <th width="70" class="word_wrap_break" title="Dyeing Machine">Batch Position</th>
	                                <th width="70" class="word_wrap_break">Batch Status</th>
	                                <th width="100" class="word_wrap_break">Remarks</th>
	                            </tr>
	                        </thead>
	                    </table>
	                    <div style=" max-height:350px; width:3055px; overflow-y:scroll;" id="scroll_body">
	                        <table class="rpt_table" id="table_body" width="3035" cellpadding="0" cellspacing="0" border="1" rules="all">
	                            <tbody>
	                                <?
	                                $i=1;$btq=0;$f=1;
	                                foreach($batch_data_arr as $batch)
	                                {
	                                    $grey_production_dtls_ids = array_unique(explode(",",$batch[('dtls_ids')]));
	                                    $yarn_prod_ids=$machine_gg_no=$machine_dia=$machine_no_id=$machine_names=$batch_status=""; $yarn_prod_id_arr = array();
	                                    foreach ($grey_production_dtls_ids as $dtlid)
	                                    {
	                                        $yarn_prod_ids .= $yarn_lot_arr[$dtlid][$batch[('prod_id')]]['yarn_prod_id'].',';
	                                        $machine_gg_no .= $yarn_lot_arr[$dtlid][$batch[('prod_id')]]['machine_gg'].',';
	                                        $machine_dia .= $yarn_lot_arr[$dtlid][$batch[('prod_id')]]['machine_dia'].',';
	                                        $machine_no_id .= $yarn_lot_arr[$dtlid][$batch[('prod_id')]]['machine_no_id'].',';
	                                    }
	                                   
	                                    $machine_gg_no=implode(",",array_filter(array_unique(explode(",",chop($machine_gg_no)))));
	                                    $machine_dia=implode(",",array_filter(array_unique(explode(",",chop($machine_dia)))));
	                                    $machine_no_id_arr=array_filter(array_unique(explode(",",chop($machine_no_id))));
	                                    foreach ($machine_no_id_arr as $mId)
	                                    {
	                                    	$machine_names  .= $machine_name_arr[$mId]."*";
	                                    }

	                                    $machine_dia_gg = ($machine_gg_no != "") ? $machine_dia."X".$machine_gg_no : $machine_dia;

	                                    $yarn_prod_id_arr=array_filter(array_unique(explode(",",chop($yarn_prod_ids,","))));
	                                    $lot_no = $yarn_count_name = $yarn_comp_name = $yarn_type_name = $yarn_brand_name = "";
	                                    foreach ($yarn_prod_id_arr as $yProd)
	                                    {
	                                    	$lot_no  .= $yarn_ref_arr[$yProd]["lot"]."*";
								        	$yarn_count_name .= $yarn_count_arr[$yarn_ref_arr[$yProd]["yarn_count_id"]]."*";
								        	$yarn_comp_name .= $composition[$yarn_ref_arr[$yProd]["yarn_comp_type1st"]]."*";
								        	$yarn_type_name .= $yarn_type[$yarn_ref_arr[$yProd]["yarn_type"]]."*";
								        	$yarn_brand_name .= $brand_name_arr[$yarn_ref_arr[$yProd]["brand"]]."*";
	                                    }
	                                    $lot_no=implode(",",array_filter(array_unique(explode("*",$lot_no))));
	                                    $yarn_count_name=implode(",",array_filter(array_unique(explode("*",$yarn_count_name))));
	                                    $yarn_comp_name=implode(",",array_filter(array_unique(explode("*",$yarn_comp_name))));
	                                    $yarn_type_name=implode(",",array_filter(array_unique(explode("*",$yarn_type_name))));
	                                    $yarn_brand_name=implode(",",array_filter(array_unique(explode("*",$yarn_brand_name))));
	                                    $machine_names=implode(",",array_filter(array_unique(explode("*",$machine_names))));
	                                    $batch_status = ($batch[("load_unload_id")]) ? $batch[("load_unload_id")] : "3";

	                                    $desc = explode(",", $batch[('item_description')]);

	                                    $process_name = '';
										$process_id_array = explode(",", $batch[("process_id")]);
										foreach ($process_id_array as $val)
										{
											if ($process_name == ""){
												$process_name = $conversion_cost_head_array[$val];
											}
											else{
												$process_name .= "," . $conversion_cost_head_array[$val];
											}
										}

										$color_type_id_array = array_unique(explode(",", $batch[('color_type_ids')]));
										
										$color_type_name ='';
										foreach ($color_type_id_array as $val)
										{
											if ($color_type_name == ""){
												$color_type_name = $color_type[$val];
											}
											else{
												$color_type_name .= "," . $color_type[$val];
											}
										}

										$width_dia_type_array = array_unique(explode(",", $batch[('width_dia_type')]));
										
										$width_dia_type_name ='';
										foreach ($width_dia_type_array as $val)
										{
											if ($width_dia_type_name == ""){
												$width_dia_type_name = $fabric_typee[$val];
											}
											else{
												$width_dia_type_name .= "," . $fabric_typee[$val];
											}
										}

										$int_ref_no = chop(implode(',',array_unique(explode(",",$int_ref_arr[$batch[("booking_id")]]['int_ref']))),',');
										$ship_date = chop(implode(',',array_unique(explode(",",$shipment_date_arr[$batch[("booking_id")]]['ship_date']))),',');

										$issue_id=$issue_no_arr[$batch[('id')]];
										$receive_id=$recv_by_batch_no_arr[$batch[('id')]];
										$issue_ids = chop(implode(',',array_unique(explode(",",$issue_id))),',');
										$receive_ids = chop(implode(',',array_unique(explode(",",$receive_id))),',');

										$grey_qty=$grey_qty_arr[$batch[('id')]][$batch[('color_id')]][$batch[("body_part_id")]];
										$process_loss=$process_loss_arr[$batch[('id')]][$batch[('color_id')]][$batch[("body_part_id")]];

										// production\reports\requires\fabric_sales_order_receive_status_report_controller.php > HyperLink > search > $booking_Arr
										$sale_booking_no= $batch[('booking_no')];
										$booking_company=$booking_Arr[$sale_booking_no]['booking_company_id'];
						                $booking_order_id=$booking_Arr[$sale_booking_no]['booking_order_id'];
						                $booking_fabric_natu=$booking_Arr[$sale_booking_no]['booking_fabric_natu'];
						                $booking_fabric_source=$booking_Arr[$sale_booking_no]['booking_fabric_source'];
						                $booking_job_no=$booking_Arr[$sale_booking_no]['booking_job_no'];
						                $is_approved_id=$booking_Arr[$sale_booking_no]['is_approved'];

										$sale_booking_no_sm_smn=explode('-', $sale_booking_no);
										$sale_booking_no_sm_smn[1];

										if($sale_booking_no_sm_smn[1]=='SMN')
						                {
						                	$booking_entry_form='SMN';
						                	$fbReportId='0';
						                }
						                else if($sale_booking_no_sm_smn[1]=='SM')
						                {
						                	$booking_entry_form='SM';
						                	$fbReportId='0';// Sample with order
						                }
						                else
						                {
						                	$booking_entry_form=118;
						                	$fbReportId=502;
						                }

	                                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                                    ?>
	                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
											<?
											$groupData_1 = $batch[("id")];
											if (!in_array($groupData_1, $date_array_1)) {
											?>
												<td class="word_wrap_break" width="30" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><? echo $f; ?></td>
												<td class="word_wrap_break" width="75" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>" title="<? echo 'Batch ID:'.$batch[('id')]; ?>"> <p><? echo change_date_format($batch[('batch_date')]); ?></p>
												<td class="word_wrap_break" width="100" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><? echo $company_library[$batch[('company_id')]]; ?></p></td>	
												<td class="word_wrap_break"  width="100" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><? echo $company_library[$batch[('working_company_id')]]; ?></p></td>
												<td width="80" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p class="word_wrap_break"><? echo $color_range[$batch[('color_range_id')]]; ?></p></td>
												<td width="80" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p class="word_wrap_break"><? echo $color_type_name; ?></p></td>
		                                        <td width="80" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>">
		                                        	<p class="word_wrap_break">
		                                        	<?
		                                        		if($batch[('within_group')] == 1)
		                                        		{
															$buyer_id = $batch[('po_buyer')];
														}
														else
														{
															$buyer_id = $batch[('buyer_id')];
														}
															echo $buyer_arr[$buyer_id];
		                                        	?>
		                                        	</p>
		                                        </td>
		                                        <td class="word_wrap_break" width="100" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><? echo $int_ref_no; ?></p></td>
		                                        <td class="word_wrap_break" width="110" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><?  echo $batch[('fso_no')]; ?></p></td>
		                                        <td class="word_wrap_break" width="60"  align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><? echo chop($job_from_fso_arr[$batch[('fso_no')]]["job_no"],","); ?></p></td>          

		                                        <td class="word_wrap_break" width="120" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>" title="<? echo $booking_company.'=='.$booking_entry_form;?>"><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sale_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$sale_booking_no</a>"; ?>&nbsp;</p></td>

		                                        <td class="word_wrap_break" width="50" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><? echo $booking_type_arr[$batch[('booking_no')]]; ?></p></td>
		                                        <td class="word_wrap_break" width="70" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><? echo $issue_ids; ?></p></td>
		                                        <td class="word_wrap_break"  width="70" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><? echo $receive_ids; ?></p></td>

		                                        <td class="word_wrap_break" width="100" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>" title="<? echo $batch[('batch_no')]; ?>"><a href="##" onClick="generate_batch_print_report('<? echo 419;?>','<? echo $company;?>','<? echo $batch[('id')]?>','<? echo $batch[('batch_no')]?>','<? echo $batch[('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[('batch_sl_no')]?>','<? echo $batch[('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[('batch_no')]; ?></a>
												</td>
		                                        <td width="50" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p class="word_wrap_break"><? echo $machine_names; ?></p></td>
		                                        <td width="80" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p class="word_wrap_break"><? echo $color_library[$batch[('color_id')]]; ?></p></td>
		                                        <td width="75" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p class="word_wrap_break"><? echo $ship_date; ?></p></td>
		                                        <td  width="80" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_1]; ?>"><p><? echo $batch_against[$batch[('batch_against')]]; ?></p></td>
												<?
												$date_array_1[] = $groupData_1;
												$f++;
											}
											?>
	                                        
	                                        <td class="word_wrap_break" width="70"><p><? echo $body_part[$batch[("body_part_id")]]; ?></p></td>
	                                        <td width="100" align="center"><p class="word_wrap_break"><? echo $desc[0]; ?></p></td>
	                                        <td width="150" align="center"><p class="word_wrap_break"><? echo $desc[1]; ?></p></td>
	                                        <td width="100" align="center"><p class="word_wrap_break"><? echo $width_dia_type_name; ?></p></td>
	                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $desc[3]; ?></p></td>
	                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $desc[2]; ?></p></td>

	                                        <td width="70" align="center"><p class="word_wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_name; ?></p></td>
	                                        <td width="70" align="center"><p class="word_wrap_break" style="mso-number-format:'\@';"><? echo $program_info_arr[$batch[('program_no')]]['stitch_length']; ?></p></td>
	                                        <td width="70" align="center"><p class="word_wrap_break"><? echo $yarn_brand_name; ?></p></td>

	                                        <td class="word_wrap_break" width="100" title="<? echo 'Prod Id'.$batch[('prod_id')].'=PO ID'.$order_id;?>" align="left"><div style="width:60px; word-wrap:break-word;"><? echo $lot_no; ?></div></td>
	                                        <td class="word_wrap_break" align="right" width="70" title="<? echo $batch[('batch_qnty')];  ?>"><? echo number_format($batch[('batch_qnty')],2);  ?></td>
	                                        <?
											$groupData_2 = $batch[("id")];
											if (!in_array($groupData_2, $date_array_2)) 
											{
	                                            ?>
	                                            <td class="word_wrap_break" width="70" align="right" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_2]; ?>" title="<? echo $batch[('total_trims_weight')]; ?>">
	                                                <?
	                                                echo $batch[('total_trims_weight')];
	                                                ?>
	                                            </td>
	                                            <td class="word_wrap_break" align="right" width="70" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_2]; ?>" title="<? echo $batch[('batch_weight')]; ?>">
	                                                <?
	                                                echo number_format(($batch[('batch_weight')]+$batch[('total_trims_weight')]),2);//($batch[('batch_qnty')]+$batch[('total_trims_weight')])
	                                                ?>
	                                            </td>
	                                            <?
	                                            $bttw+=$batch[('total_trims_weight')];
	                                            $bw+=($batch[('batch_weight')]+$batch[('total_trims_weight')]);
	                                            $date_array_2[] = $groupData_2;
	                                        }
	                                        ?>

	                                        <td width="40" class="word_wrap_break" align="right"><a href="##" onclick="openmypage_roll('<? echo $batch[("id")]; ?>','<? echo $batch[("prod_id")]; ?>','<? echo $batch[("po_id")]; ?>','<? echo $batch[("body_part_id")]; ?>','<? echo $batch[("width_dia_type")]; ?>');"><?php echo $batch[("roll_no")]; ?></a></td>

	                                        <td width="120" align="right"><p class="word_wrap_break"><? echo number_format($grey_qty,2,".",""); ?></p></td>
	                                        <td width="70" align="right"><p class="word_wrap_break"><? echo  number_format($process_loss,2,".","");?></p></td>
	                                        
	                                        <?
											$groupData_3 = $batch[("id")];
											if (!in_array($groupData_3, $date_array_3)) 
											{
	                                            ?>
	                                        	<td class="word_wrap_break" width="70" align="center" style="vertical-align: middle;" rowspan="<? echo $gData_1[$groupData_3]; ?>"><? echo $machine_name_arr[$batch[('dyeing_machine')]];?></td>
	                                        	<?
	                                        	$date_array_3[] = $groupData_3;
	                                        }
	                                        ?>

	                                        <td class="word_wrap_break" align="center" width="70"><? echo $load_unload_arr[$batch_status];?></td>
	                                        <td width="100" class="word_wrap_break" align="center"><? echo $batch[("remarks")];?></td>
	                                    </tr>
	                                    <?
	                                    $i++;
	                                    $btq+=$batch[('batch_qnty')];
	                                    $tot_grey_qty+=$grey_qty;
	                                    $tot_process_loss+=$batch[('process_loss')];
	                                    $batch_chk[$batch[('id')]] = $batch[('id')];
	                                    $tot_batch_roll_no += $batch[("roll_no")];

	                                    $tot_batch[$batch[('id')]]=$batch[('id')];
	                                }
	                                // echo "<pre>";print_r($tot_batch);
	                                ?>
	                            </tbody>
	                        </table>
	                    </div>
	                    <table class="rpt_table" width="3035" cellpadding="0" cellspacing="0" border="1" rules="all">
	                        <tfoot>
	                            <tr>
	                            	<th width="30">&nbsp;</th>
	                                <th width="75">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="80">&nbsp;</th>                                
	                                <th width="100">&nbsp;</th>
	                                <th width="110">&nbsp;</th>
	                                <th width="60">&nbsp;</th>
	                                <th width="120">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="75">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="150">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">Tot. Batch Wt.</th>	                                
	                                <th class="word_wrap_break" width="70"><? echo number_format($btq,2,".",""); ?></th>
	                                <th class="word_wrap_break" width="70"><? echo number_format($bttw,2,".",""); ?></th>
	                                <th class="word_wrap_break" width="70"><? echo number_format($bw,2,".",""); ?></th>
	                                <th class="word_wrap_break" width="40"><? echo $tot_batch_roll_no;?></th>
	                                <th width="120"><? echo number_format($tot_grey_qty,2,".","");?></th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                            </tr>
	                            <tr>
	                            	<th width="30">&nbsp;</th>
	                                <th width="75">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="80">&nbsp;</th>                                
	                                <th width="100">&nbsp;</th>
	                                <th width="110">&nbsp;</th>
	                                <th width="60">&nbsp;</th>
	                                <th width="120">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="75">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="150">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">Total Batch</th>	                                
	                                <th width="70"><? echo count($tot_batch);?></th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="40">&nbsp;</th>
	                                <th width="120">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                            </tr>
	                        </tfoot>
	                    </table>
	                    <?
	                }
	                if(($batch_type==0 || $batch_type==2) && count($subbatchdata)>0) // not use for ccl
	                {
	                	?>
	                    <div align="center" style="width:1758px;"><b>Inbound Subcontract</b></div>
	                    <table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	                        <thead>
	                            <tr>
	                                <th class="word_wrap_break" width="30">SL</th>
	                                <th class="word_wrap_break" width="75">Batch Date</th>
	                                <th class="word_wrap_break" width="60">Batch No</th>
	                                <th class="word_wrap_break" width="100">Batch No</th>
	                                <th class="word_wrap_break" width="40">Ext. No</th>
	                                <th class="word_wrap_break" width="80">Batch Against</th>
	                                <th class="word_wrap_break" width="80">Batch Color</th>
	                                <th class="word_wrap_break" width="80">Party</th>
	                                <th class="word_wrap_break" width="100">Job No</th>
	                                <th class="word_wrap_break" width="80">Cust. Style Ref.</th>
	                                <th class="word_wrap_break" width="110">Order No</th>

	                                <th class="word_wrap_break" width="100">Construction</th>
	                                <th class="word_wrap_break" width="150">Composition</th>
	                                <th class="word_wrap_break" width="50">Dia/ Width</th>
	                                <th class="word_wrap_break" width="50">GSM</th>
	                                <th class="word_wrap_break" width="70">Dyeing Machine</th>
	                                <th class="word_wrap_break" width="70">Fabric Weight.</th>
	                                <th class="word_wrap_break" width="70">Trims Weight</th>
	                                <th class="word_wrap_break" width="70">Total Batch Weight</th>
	                                <th class="word_wrap_break" width="70">No of roll</th>
	                                <th class="word_wrap_break" width="70">Batch Status</th>
	                                <th class="word_wrap_break" width="100">Remarks</th>
	                                <th class="word_wrap_break" width="100">Process Name</th>
	                            </tr>
	                        </thead>
	                    </table>
	                    <div style="max-height:350px; width:1758px; overflow-y:scroll;" id="scroll_body_subcon">
	                        <table class="rpt_table" width="1740" id="table_body2" cellpadding="0" cellspacing="0" border="1" rules="all">
	                            <tbody>
	                                <?
	                                $i=1;$btq=0;$batch_status="";
	                                foreach($subbatchdata as $row)
	                                {
	                                	$process_name = '';
										$process_id_array = explode(",", $row[csf("process_id")]);
										foreach ($process_id_array as $val)
										{
											if ($process_name == "")
												$process_name = $conversion_cost_head_array[$val];
											else
												$process_name .= "," . $conversion_cost_head_array[$val];
										}

	                                    $desc = explode(",", $row[csf('item_description')]);
	    								$batch_status = ($row[csf("load_unload_id")]) ? $row[csf("load_unload_id")] : "3";
	                                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                                    ?>
	                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
	                                        <td class="word_wrap_break" width="30"><? echo $i; ?></td>
	                                        <td class="word_wrap_break" align="center" width="75" title="<? echo change_date_format($row[csf('batch_date')]); ?>"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>

	                                        <td class="word_wrap_break" width="60" title="<? echo $row[csf('batch_no')]; ?>"><a href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $row[csf('id')]?>','<? echo $row[csf('batch_no')]?>','<? echo $row[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $row[csf('batch_sl_no')]?>','<? echo $row[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $row[csf('batch_no')]; ?></a></td>

	                                        <td class="word_wrap_break" width="100" title="<? echo $row[csf('floor_id')]; ?>"><p><? echo $floor_name_arr[$row[csf('floor_id')]]; ?></p></td>
	                                        <td class="word_wrap_break" width="40" title="<? echo $row[csf('extention_no')]; ?>"><? echo $row[csf('extention_no')]; ?></td>
	                                        <td class="word_wrap_break" width="80"><? echo $batch_against[$row[csf('batch_against')]]; ?></td>
	                                        <td width="80"><p class="word_wrap_break"><? echo $color_library[$row[csf('color_id')]]; ?></p></td>
	                                        <td class="word_wrap_break" width="80"><? echo $buyer_arr[$sub_ord_arr[$row[csf('po_id')]]['party']]; ?></td>
	                                        <td width="100" title="POID=<? echo $row[csf('po_id')];?>"><p class="word_wrap_break"><? echo $sub_ord_arr[$row[csf('po_id')]]['job']; ?></p></td>
	                                        <td width="80"><p class="word_wrap_break"><? echo $sub_ord_arr[$row[csf('po_id')]]['style_ref']; ?></p></td>
	                                        <td width="110"><p class="word_wrap_break"><? echo $sub_ord_arr[$row[csf('po_id')]]['order']; ?></p></td>

	                                        <td class="word_wrap_break" width="100"><? echo $desc[0]; ?></td>
	                                        <td class="word_wrap_break" width="150"><? echo $desc[1]; ?></td>
	                                        <td class="word_wrap_break" width="50" align="center"><p class="word_wrap_break"><? echo $row[csf('grey_dia')]; ?></p></td>
	                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $row[csf('gsm')]; ?></p></td>


	                                        <td class="word_wrap_break" width="70" align="center"><? echo $machine_name_arr[$row[csf('machine_no')]];?></td>
	                                        <td class="word_wrap_break" align="right" width="70" title="<? echo $row[csf('batch_qnty')];  ?>"><? echo number_format($row[csf('batch_qnty')],2);  ?></td>
	                                        <?
	                                        if($sbatch_chk[$row[csf('id')]]=="")
	                                        {
	                                            ?>
	                                            <td class="word_wrap_break" align="right" width="70" title="<? echo $row[csf('total_trims_weight')];  ?>">
	                                                <?
	                                                echo $row[csf('total_trims_weight')];
	                                                ?>
	                                            </td>
	                                            <td class="word_wrap_break" align="right" width="70" title="<? echo $row[csf('batch_weight')]; ?>">
	                                                <?
	                                                echo number_format(($row[csf('batch_qnty')]+$row[csf('total_trims_weight')]),2);
	                                                ?>
	                                            </td>
	                                            <?
	                                            $sbttw+=$row[csf('total_trims_weight')];
	                                            $sbw+=($row[csf('batch_qnty')]+$row[csf('total_trims_weight')]);
	                                        }
	                                        else
	                                        {
	                                            ?>
	                                            <td class="word_wrap_break" align="right" width="70">&nbsp;</td>
	                                            <td class="word_wrap_break" width="70" align="right">
	                                                <?
	                                                echo number_format($row[csf('batch_qnty')],2);
	                                                ?>
	                                            </td>
	                                            <?
	                                            $bw+=$row[csf('batch_qnty')];
	                                        }
	                                        ?>
	                                        <td class="word_wrap_break" width="70" align="center"><? echo $row[csf("roll_no")];?></td>
	                                        <td class="word_wrap_break" width="70" align="center"><? echo $load_unload_arr[$batch_status];?></td>
	                                        <td class="word_wrap_break" width="100" align="center"><? echo $row[csf("remarks")];?></td>
	                                        <td class="word_wrap_break" width="100" align="center"><? echo $process_name;?></td>
	                                    </tr>
	                                    <?
	                                    $i++;
	                                    $sbtq+=$row[csf('batch_qnty')];
	                                    $sbatch_chk[$row[csf('id')]] = $row[csf('id')];
	                                    $tot_roll_no += $row[csf("roll_no")];
	                                }
	                                ?>
	                            </tbody>
	                        </table>
	                   	</div>
	                    <table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all">
	                        <tfoot>
	                            <tr>
	                                <th width="30">&nbsp;</th>
	                                <th width="75">&nbsp;</th>
	                                <th width="60">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="40">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="110">&nbsp;</th>

	                                <th width="100">&nbsp;</th>
	                                <th width="150">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th class="word_wrap_break" width="70" id="value_sbatch_qnty" style="text-align: right"><? echo number_format($sbtq,2); ?></th>
	                                <th class="word_wrap_break" width="70" id="value_stotal_trims_weight" style="text-align: right"><? echo number_format($sbttw,2); ?></th>
	                                <th class="word_wrap_break" width="70" id="value_sbatch_weight" style="text-align: right"><? echo number_format($sbw,2); ?></th>
	                                <th class="word_wrap_break" width="70" id="value_s_roll_total" align="center"><? echo $tot_roll_no; ?></th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                            </tr>
	                        </tfoot>
	                    </table>
	                    <?
	                }
	                ?>
	            </div>
	        </fieldset>
	    </div>
		<?
	}
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$operation";
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
						<th>Job Year</th>
						<th>Within Group</th>
						<th>FSO NO.</th>
						<th>Booking NO.</th>
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
								<input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
							</td>
							<td>
								<input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'batch_report_for_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$fso_no=trim($data[4]);
	$booking_no=trim($data[5]);

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


	if($fso_no != "")
	{
		$search_cond .= " and a.job_no_prefix_num = '$fso_no'" ;
	}
	if($booking_no != "")
	{
		$search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
	}
	if($db_type==0)
	{
		if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
	}

	$sql_2 ="select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	order by id desc";

	$sql_1 = "select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id";

	if($within_group == 1)
	{
		$sql = $sql_1 ;
	}
	else if($within_group == 2)
	{
		$sql = $sql_2;
	}else
	{
		$sql = $sql_1." union all ". $sql_2 ;
	}
	//echo $sql;
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="120">Buyer</th>
			<th width="150">FSO No</th>
			<th width="">Booking No</th>
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
					<td width="40" align="center"><?php echo "$i"; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
				</td>
				<td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
				<td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
				<td width="150"><p><?php echo $selectResult[csf('job_no')];?></p></td>
				<td width=""><?php echo $selectResult[csf('sales_booking_no')];?></td>
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

if ($action=="roll_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);

	$company=$data[0];
	$batch_id=$data[1];
	$product_ids=$data[2];
	$po_id=$data[3];
	$body_part_id=$data[4];
	$width_dia_type=$data[5];

	if ($data[1]!="") $batch_id_cond=" and a.id in($data[1])";
	if ($data[2]!="") $product_cond=" and b.prod_id in($data[2])";
	if ($data[3]!="") $po_id_cond=" and b.po_id in($data[3])";
	if ($data[4]!="") $body_part_cond=" and b.body_part_id in($data[4])";
	if ($data[5]!="") $dia_type_cond=" and b.width_dia_type in($data[5])";

	$yarn_count_arr  = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0","id","yarn_count");
	$brand_name_arr  = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0","id","brand_name");

	$con = connect();
	execute_query("DELETE from tmp_barcode_no where userid=$user_name and entry_form=180");
	oci_commit($con);

	$batch_sql="SELECT a.id, a.company_id, a.working_company_id, a.batch_against,a.entry_form,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id, a.color_range_id,a.booking_no,a.extention_no, a.batch_sl_no, a.booking_no_id, a.remarks, a.process_id,a.floor_id,a.shift_id,a.dyeing_machine, b.item_description,b.prod_id,b.po_id, b.batch_qnty as batch_qnty, c.job_no as fso_no,c.style_ref_no,c.po_buyer,c.buyer_id,c.within_group,b.body_part_id, b.color_type AS color_type_ids, b.id as roll_no, c.customer_buyer,c.booking_id, b.program_no,b.barcode_no, b.width_dia_type
	from pro_batch_create_mst a, pro_batch_create_dtls b, fabric_sales_order_mst c 
	where a.company_id in($company) $batch_id_cond $product_cond $po_id_cond $body_part_cond $dia_type_cond and a.status_active=1 and a.is_sales=1 and a.id=b.mst_id and b.po_id=c.id and b.is_deleted=0 and c.status_active=1 and b.barcode_no!=0";
	// echo $batch_sql;die;
	$batchdata=sql_select($batch_sql);
	$all_barcode_arr=array();
	foreach($batchdata as $batch)
	{
		$barcode_arr[$batch[csf("barcode_no")]]=$batch[csf("barcode_no")];
		$all_barcode_arr[$batch[csf("barcode_no")]]=$batch[csf("barcode_no")];
	}
	foreach ($barcode_arr as $key => $barcodeno) 
	{
		// echo $key.'='.$barcodeno.'<br>';
		if( $barcode_no_check[$barcodeno] =="" )
        {
            $barcode_no_check[$barcodeno]=$barcodeno;
            // echo "insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_name,$barcodeno, 180)";
            execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form,type) values ($user_name,$barcodeno, 180,1)");
        }
	}
	oci_commit($con);
	// echo "<pre>";print_r($barcode_arr);

	if (!empty($barcode_arr)) 
	{
		$split_sql="SELECT c.barcode_no as mother_barcode, d.barcode_no , d.qnty, d.qc_pass_qnty_pcs, d.coller_cuff_size from tmp_barcode_no g, pro_roll_split c, pro_roll_details d
		where g.barcode_no=c.barcode_no and  g.userid=$user_name and g.entry_form=180 and g.type=1 and c.entry_form = 75 and  c.split_from_id = d.roll_split_from and c.status_active = 1 and d.status_active = 1";
		// echo $split_sql;die;
		$split_sql_data = sql_select($split_sql);
		foreach ($split_sql_data as $key => $row)
		{
			//$split_data_array[$row[csf('mother_barcode')]]["qc_pass_qnty_pcs"] = $row[csf('qc_pass_qnty_pcs')];
			$all_barcode_arr[$row[csf('mother_barcode')]] = $row[csf('mother_barcode')];
			$mother_barcode_array[$row[csf('barcode_no')]]['mom'] = $row[csf('mother_barcode')];
		}
		unset($split_sql_data);

		// create batch using child barcode but mother barcode not in this batch
		$split_sql="SELECT d.barcode_no,e.barcode_no as mother_barcode, d.qnty, d.qc_pass_qnty_pcs, d.coller_cuff_size

		from tmp_barcode_no g, pro_roll_details d, pro_roll_details e 
		where g.barcode_no=d.barcode_no and d.roll_split_from=e.ID and d.status_active = 1 and e.status_active = 1 and d.ENTRY_FORM= 62 and e.ENTRY_FORM= 62 and  g.userid=$user_name and g.entry_form=180 and g.type=1";
		// echo $split_sql;
		$split_sql_data = sql_select($split_sql);
		foreach ($split_sql_data as $key => $row)
		{
			$all_barcode_arr[$row[csf('mother_barcode')]] = $row[csf('mother_barcode')];
			$mother_barcode_array[$row[csf('barcode_no')]]['mom'] = $row[csf('mother_barcode')];
		}
		unset($split_sql_data);
		// echo "<pre>";print_r($all_barcode_arr);die;

		foreach ($all_barcode_arr as $key => $barcode) 
		{
			if( $barcode_check[$barcode] =="" )
            {
                $barcode_check[$barcode]=$barcode;
                $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form,type) values ($user_name,$barcode, 180,2)");
            }
		}
		oci_commit($con);
		// echo "<pre>";print_r($all_barcode_arr);die;
		//$result = array_unique(array_merge($barcode_arr, $all_barcode_arr));
		//echo "<pre>";print_r($result);

		$production_dtls = "SELECT d.id as dtls_id, e.receive_basis,e.booking_id, d.febric_description_id,d.gsm,d.width, d.machine_dia,d.machine_gg,d.machine_no_id, d.yarn_lot, d.yarn_count, d.stitch_length, d.brand_id, e.knitting_source, e.knitting_company, c.barcode_no, p.detarmination_id 
		from tmp_barcode_no g, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e, product_details_master p 
		where g.barcode_no=c.barcode_no and c.dtls_id=d.id and d.mst_id=e.id and d.prod_id = p.id and e.entry_form in(2,22) and c.entry_form in(2,22) and g.userid=$user_name and g.entry_form=180 and g.type=2";
		// echo $production_dtls;die;
		$production_data = sql_select($production_dtls);
		foreach ($production_data as $row)
		{
			$production_info[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
			$production_info[$row[csf("barcode_no")]]['booking_id']=$row[csf("booking_id")];
			$production_info[$row[csf("barcode_no")]]['febric_description_id']=$row[csf("febric_description_id")];
			$production_info[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
			$production_info[$row[csf("barcode_no")]]['width']=$row[csf("width")];
			$production_info[$row[csf("barcode_no")]]['machine_dia']=$row[csf("machine_dia")];
			$production_info[$row[csf("barcode_no")]]['machine_gg']=$row[csf("machine_gg")];
			$production_info[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
			$production_info[$row[csf("barcode_no")]]['yarn_lot']=$row[csf("yarn_lot")];
			$production_info[$row[csf("barcode_no")]]['yarn_count']=$row[csf("yarn_count")];
			$production_info[$row[csf("barcode_no")]]['stitch_length']=$row[csf("stitch_length")];
			$production_info[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			$production_info[$row[csf("barcode_no")]]['knitting_source']=$row[csf("knitting_source")];
			$production_info[$row[csf("barcode_no")]]['knitting_company']=$row[csf("knitting_company")];
			$production_info[$row[csf("barcode_no")]]['detarmination_id']=$row[csf("detarmination_id")];
			$production_info[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];

			$grey_production_dtls_id_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
		}
		unset($production_data);

		$tranfer_sql="SELECT a.barcode_no, b.transfer_system_id
		FROM TMP_BARCODE_NO g, pro_roll_details a, INV_ITEM_TRANSFER_MST b
		WHERE g.barcode_no=a.barcode_no and a.mst_id=b.id and a.entry_form in(133) and b.entry_form=133 and a.status_active = 1 and a.is_deleted = 0 and a.is_sales=1 and b.status_active = 1 and b.is_deleted = 0 and a.re_transfer=0 and g.userid=$user_name and g.entry_form=180 and g.type=2";
		$tranfer_sql_data = sql_select($tranfer_sql);
		foreach ($tranfer_sql_data as $row)
		{
			$transfer_info_arr[$row[csf("barcode_no")]]['transfer_no']=$row[csf("transfer_system_id")];
		}
	}
	// echo "string";die;

	$yarn_lot_arr=array();
		$grey_production_dtls_id_arr = array_filter(array_unique(explode(",",implode(",",$grey_production_dtls_id_arr))));
	if(!empty($grey_production_dtls_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 180, 3,$grey_production_dtls_id_arr, $empty_arr); //dtls_id

		$yarn_lot_data=sql_select("SELECT a.id dtls_id,b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot,a.yarn_prod_id, a.machine_no_id, a.machine_dia, a.machine_gg 
		from GBL_TEMP_ENGINE g, pro_grey_prod_entry_dtls a, order_wise_pro_details b 
		where g.ref_val=a.id and g.user_id=$user_name and g.entry_form=180 and g.ref_from=3 and a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id,a.prod_id,a.yarn_lot, b.po_breakdown_id,a.yarn_prod_id,a.machine_no_id,a.machine_dia, a.machine_gg");
		//and a.yarn_prod_id is not null

		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['yarn_prod_id'] 	.= $rows[csf('yarn_prod_id')].',';
			$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_no_id'] 	.= $rows[csf('machine_no_id')].',';
			$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_dia'] 		.= $rows[csf('machine_dia')].',';
			$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_gg'] 		.= $rows[csf('machine_gg')].',';
			$yarn_prod_id_arr[$rows[csf('yarn_prod_id')]] = $rows[csf('yarn_prod_id')];
		}
	}

	$all_yarn_prod_id_arr = array_filter(array_unique(explode(",",implode(",", $yarn_prod_id_arr))));
	if(!empty($all_yarn_prod_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 180, 4,$all_yarn_prod_id_arr, $empty_arr); //yarn_prod_id

        $yarn_sql = sql_select("SELECT a.id, a.lot, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_type, a.brand  
        from GBL_TEMP_ENGINE g, product_details_master a 
        where g.ref_val=a.id and g.user_id=$user_name and g.entry_form=180 and g.ref_from=4 and a.status_active =1 ");//$all_yarn_prod_id_cond

        foreach ($yarn_sql as $row)
        {
        	$yarn_ref_arr[$row[csf("id")]]["lot"] = $row[csf("lot")];
        	$yarn_ref_arr[$row[csf("id")]]["yarn_count_id"] = $row[csf("yarn_count_id")];
        	$yarn_ref_arr[$row[csf("id")]]["yarn_comp_type1st"] = $row[csf("yarn_comp_type1st")];
        	$yarn_ref_arr[$row[csf("id")]]["yarn_type"] = $row[csf("yarn_type")];
        	$yarn_ref_arr[$row[csf("id")]]["brand"] = $row[csf("brand")];
        }
    }

	execute_query("DELETE from tmp_barcode_no where userid=$user_name and entry_form=180");
	oci_commit($con);
	?>    
    <div id="data_panel" align="center" style="width:100%">
        <fieldset style="width: 98%">
        <table width="1400" cellspacing="0" class="rpt_table" border="0" id="tbl_returnable_details" rules="all">
	        <thead>
	            <tr>
	              <th width="50">Sl</th>
	              <th width="100">Prog. No</th>
	              <th width="100">Barcode No</th>
	              <th width="100">Body Part</th>
	              <th width="100">Contruction</th>
	              <th width="100">Composition</th>
	              <th width="100">Roll. Wt</th>
	              <th width="100">Dia Type</th>
	              <th width="40">Dia</th>
	              <th width="40">GSM</th>
	              <th width="100">Yarn Count</th>
	              <th width="100">S.L</th>
	              <th width="100">Y. Brand</th>
	              <th width="100">Y. Lot</th>
	              <th width="100">Transferred No</th>
	            </tr>
	        </thead>  
                <tbody>
                <?
                $i=1;
                foreach ($batchdata as $batch) 
                {
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					if($mother_barcode_array[$batch[csf('barcode_no')]]['mom']!="")
					{
						$barcode_number= $mother_barcode_array[$batch[csf('barcode_no')]]['mom'];
					}
					else
					{
						$barcode_number= $batch[csf("barcode_no")];
					}
					// echo $barcode_number;
					$transfer_no=$transfer_info_arr[$barcode_number]['transfer_no'];

					$dtls_id = $production_info[$barcode_number]['dtls_id'];
					$stitch_length = $production_info[$barcode_number]['stitch_length'];
					$yarn_lot = $production_info[$barcode_number]['yarn_lot'];
					$yarn_count = $production_info[$barcode_number]['yarn_count'];
					$brand_id = $production_info[$barcode_number]['brand_id'];


					$desc = explode(",", $batch[csf('item_description')]);

					$grey_production_dtls_ids = array_unique(explode(",",$dtls_id));
					$yarn_prod_ids=""; $yarn_prod_id_arr = array();
                    foreach ($grey_production_dtls_ids as $dtlid)
                    {
                        $yarn_prod_ids .= $yarn_lot_arr[$dtlid][$batch[csf('prod_id')]]['yarn_prod_id'].',';
                    }

					$yarn_prod_id_arr=array_filter(array_unique(explode(",",chop($yarn_prod_ids,","))));
                    $lot_no = $yarn_count_name = $yarn_brand_name = "";
                    foreach ($yarn_prod_id_arr as $yProd)
                    {
                    	$lot_no  .= $yarn_ref_arr[$yProd]["lot"]."*";
			        	$yarn_count_name .= $yarn_count_arr[$yarn_ref_arr[$yProd]["yarn_count_id"]]."*";
			        	$yarn_brand_name .= $brand_name_arr[$yarn_ref_arr[$yProd]["brand"]]."*";
                    }
                    $lot_no=implode(",",array_filter(array_unique(explode("*",$lot_no))));
                    $yarn_count_name=implode(",",array_filter(array_unique(explode("*",$yarn_count_name))));
                    $yarn_brand_name=implode(",",array_filter(array_unique(explode("*",$yarn_brand_name))));

					?>                         
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $batch[csf('program_no')]; ?></td>
						<td width="100" align="center"><? echo $batch[csf('barcode_no')]; ?></td>
						<td width="100" align="center"><? echo $body_part[$batch[csf("body_part_id")]]; ?></td>
						<td width="100" align="center"><? echo $desc[0]; ?></td>
						<td width="100" align="center"><? echo $desc[1]; ?></td>
						<td width="100" align="right"><? echo $batch[csf('batch_qnty')]; ?></td>
						<td width="100" align="center"><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></td>
						<td width="40" align="center"><? echo $desc[3]; ?></td>
						<td width="40" align="center"><? echo $desc[2] ?></td>
						<td width="100" align="center"><? echo $yarn_count_name; ?></td>
						<td width="100" align="center"><? echo $stitch_length; ?></td>
						<td width="100" align="center"><? echo $yarn_brand_name; ?></td>
						<td width="100" align="center"><? echo $lot_no; ?></td>
						<td width="100" align="center"><? echo $transfer_no; ?></td>
					</tr>
					<?
					$i++;                                 
                }
             ?>
             </tbody>       
        </table>
      </fieldset>
    </div> 
    <?	
    
	exit();
}
?>