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
	if($db_type==0) $year_field_grpby="GROUP BY batch_no";
	else if($db_type==2) $year_field_grpby=" GROUP BY batch_no,id,batch_no,batch_for,booking_no,color_id,batch_weight order by batch_no desc";
	$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and is_deleted = 0 $year_field_grpby ";
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value, 'create_booking_no_search_list_view', 'search_div', 'batch_ledger_report_for_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
		$sql="select a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num as job_prefix,$year_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company_id $buyer_name_cond $year_cond and a.is_deleted=0 $year_field_grpby";
	}
	else
	{
		$sql="select a.id,a.party_id as buyer_name,c.item_id as gmts_item_id,b.cust_style_ref as style_ref_no,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field from  subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id  $sub_buyer_name_cond $year_cond and a.is_deleted=0 group by a.id,a.party_id,b.cust_style_ref,b.order_no ,a.job_no_prefix_num,a.insert_date,c.item_id";
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

if($action=="batch_report")
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

	$knitting_party_arr=return_library_array( "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name", "id", "supplier_name"  );

	ob_start();

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

		if((($all_booking_no_cond != "" || $sales_orders_cond != "" || $style_no_cond != "") && $all_fso_no_cond != "") || ($all_booking_no_cond == "" && $sales_orders_cond == "" && $style_no_cond =="" && $all_fso_no_cond == ""))
	   	{
			$sql="SELECT x.*, e.load_unload_id
			from
			( SELECT a.id,a.batch_against,a.entry_form,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.booking_no,a.extention_no, b.item_description,b.prod_id,b.po_id,b.program_no,b.batch_qnty,c.job_no as fso_no,c.style_ref_no,a.dyeing_machine, d.dtls_id, d.barcode_no, c.po_buyer,c.buyer_id,c.within_group,b.body_part_id,b.id as roll_no, a.remarks, a.process_id
			from pro_batch_create_mst a, pro_batch_create_dtls b, fabric_sales_order_mst c, pro_roll_details d
			where a.company_id=$company and a.status_active=1 $dates_com $batch_num $ext_no $all_fso_no_cond $batch_against_cond $style_no_cond_2 and a.is_sales=1 and a.id=b.mst_id and b.po_id=c.id and b.barcode_no=d.barcode_no and b.is_deleted=0 and d.entry_form in(2,22) and d.status_active=1 and c.status_active=1
			) x  left join pro_fab_subprocess e on x.id = e.batch_id and e.status_active =1";
			$batchdata=sql_select($sql);
	   	}
		// echo $sql;
		$grey_production_dtls_id_arr = array();
		if($batch_type==0 || $batch_type==1)
		{
			foreach($batchdata as $batch)
			{
				$grey_production_dtls_id_arr[] = $batch[csf("dtls_id")];
				$sales_ord_wise_fso_arr[$batch[csf("po_id")]]=$batch[csf("po_id")];
				$program_no_arr[$batch[csf("program_no")]]=$batch[csf("program_no")];
				$all_batch_arr[$batch[csf("id")]]=$batch[csf("id")];
			}
			$fso_nos = implode(",", $sales_ord_wise_fso_arr);
			$all_program_no = implode(",", $program_no_arr);
		}

		

		$yarn_lot_arr=array();
		$grey_production_dtls_id_arr = array_filter(array_unique(explode(",",implode(",",$grey_production_dtls_id_arr))));

		if(!empty($grey_production_dtls_id_arr))
		{
			$all_grey_production_dtls_ids = implode(",", $grey_production_dtls_id_arr);
	        $all_grey_production_dtls_id_cond=""; $DtlsIdCond="";
	        if($db_type==2 && count($grey_production_dtls_id_arr)>999)
	        {
	        	$grey_production_dtls_id_arr_chunk=array_chunk($grey_production_dtls_id_arr,999) ;
	        	foreach($grey_production_dtls_id_arr_chunk as $chunk_arr)
	        	{
	        		$chunk_arr_value=implode(",",$chunk_arr);
	        		$DtlsIdCond.="  a.id in($chunk_arr_value) or ";
	        	}

	        	$all_grey_production_dtls_id_cond.=" and (".chop($DtlsIdCond,'or ').")";
	        }
	        else
	        {
	        	$all_grey_production_dtls_id_cond=" and a.id in($all_grey_production_dtls_ids)";
	        }

			if($db_type==0)
			{
				$yarn_lot_data=sql_select("SELECT a.id dtls_id,b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot,a.yarn_prod_id,a.machine_no_id, a.machine_dia, a.machine_gg from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $all_grey_production_dtls_id_cond group by a.id,a.prod_id,a.yarn_lot, b.po_breakdown_id,a.yarn_prod_id,a.machine_no_id,a.machine_dia, a.machine_gg");
				//and a.yarn_prod_id!='0'
			}
			else if($db_type==2)
			{
				$yarn_lot_data=sql_select("SELECT a.id dtls_id,b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot,a.yarn_prod_id, a.machine_no_id, a.machine_dia, a.machine_gg from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $all_grey_production_dtls_id_cond group by a.id,a.prod_id,a.yarn_lot, b.po_breakdown_id,a.yarn_prod_id,a.machine_no_id,a.machine_dia, a.machine_gg");
				//and a.yarn_prod_id is not null

			}
			foreach($yarn_lot_data as $rows)
			{
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['yarn_prod_id'] 	.= $rows[csf('yarn_prod_id')].',';
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_no_id'] 	.= $rows[csf('machine_no_id')].',';
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_dia'] 		.= $rows[csf('machine_dia')].',';
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]]['machine_gg'] 		.= $rows[csf('machine_gg')].',';
				$yarn_prod_id_arr[$rows[csf('yarn_prod_id')]] = $rows[csf('yarn_prod_id')];

				$machine_id_arr[$rows[csf('dtls_id')]] = $rows[csf('machine_no_id')];
			}
		}
		// echo "<pre>";print_r($yarn_lot_arr);

		

		if($db_type==2)
		{
			$machine_name_arr  = return_library_array("select id,machine_no || '-' || brand as machine_name from lib_machine_name where status_active in (1,2) and is_deleted=0 and is_locked=0 order by seq_no","id","machine_name");
		}
		else if($db_type==0)
		{
			$machine_name_arr  = return_library_array("select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where status_active in (1,2) and is_deleted=0 and is_locked=0 order by seq_no","id","machine_name");
		}

		$all_yarn_prod_id_arr = array_filter(array_unique(explode(",",implode(",", $yarn_prod_id_arr))));
		if(!empty($all_yarn_prod_id_arr))
		{
	        $all_yarn_prod_ids = implode(",", $all_yarn_prod_id_arr);
	        $all_yarn_prod_id_cond=""; $yProdCond="";
	        if($db_type==2 && count($all_yarn_prod_id_arr)>999)
	        {
	        	$all_yarn_prod_id_arr_chunk=array_chunk($all_yarn_prod_id_arr,999) ;
	        	foreach($all_yarn_prod_id_arr_chunk as $chunk_arr)
	        	{
	        		$chunk_arr_value=implode(",",$chunk_arr);
	        		$yProdCond.="  id in($chunk_arr_value) or ";
	        	}

	        	$all_yarn_prod_id_cond.=" and (".chop($yProdCond,'or ').")";
	        }
	        else
	        {
	        	$all_yarn_prod_id_cond=" and id in($all_yarn_prod_ids)";
	        }

	        $yarn_sql = sql_select("select id, lot, yarn_count_id, yarn_comp_type1st,  yarn_type, brand  from product_details_master where status_active =1 $all_yarn_prod_id_cond");
	        // echo "select id, lot, yarn_count_id, yarn_comp_type1st,  yarn_type, brand  from product_details_master where status_active =1 $all_yarn_prod_id_cond";

	        foreach ($yarn_sql as $row)
	        {
	        	$yarn_ref_arr[$row[csf("id")]]["lot"] = $row[csf("lot")];
	        	$yarn_ref_arr[$row[csf("id")]]["yarn_count_id"] = $row[csf("yarn_count_id")];
	        	$yarn_ref_arr[$row[csf("id")]]["yarn_comp_type1st"] = $row[csf("yarn_comp_type1st")];
	        	$yarn_ref_arr[$row[csf("id")]]["yarn_type"] = $row[csf("yarn_type")];
	        	$yarn_ref_arr[$row[csf("id")]]["brand"] = $row[csf("brand")];
	        }
	    }

		$fso_no_cond="";
		if($fso_nos)
		{
			$fso_nos = implode(",",array_filter(array_unique(explode(",", $fso_nos))));
			$fso_nos_arr = explode(",", $fso_nos);
			if($db_type==0)
			{
				$fso_no_cond = " and a.id in ($fso_nos )";
			}
			else
			{
				if(count($fso_nos_arr)>999)
				{
					$fso_nos_chunk_arr=array_chunk($fso_nos_arr, 999);
					$fso_no_cond=" and (";
					foreach ($fso_nos_chunk_arr as $value)
					{
						$fso_no_cond .="a.id in (".implode(",", $value).") or ";
					}
					$fso_no_cond=chop($fso_no_cond,"or ");
					$fso_no_cond.=")";
				}
				else
				{
					$fso_no_cond = " and a.id in ($fso_nos )";
				}
			}
		}

		$job_fso_chk=array();$job_from_fso_arr=array();
		$job_from_fso =  sql_select("SELECT c.booking_no,c.booking_type,c.is_short, b.job_no_prefix_num,b.job_no, a.job_no as fso_no, d.short_booking_type from fabric_sales_order_mst a, wo_booking_dtls c,wo_po_details_master b, wo_booking_mst d where a.sales_booking_no=c.booking_no and c.job_no=b.job_no and a.company_id=$company $fso_no_cond and a.within_group=1 and a.booking_id = d.id and c.booking_no = d.booking_no
		union all
		select b.booking_no,4 as booking_type,0 as is_short,0 as job_no_prefix_num,null as job_no, a.job_no as fso_no, null as short_booking_type from  fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b where a.within_group=1 and a.sales_booking_no=b.booking_no and  a.company_id=$company $fso_no_cond");
		foreach ($job_from_fso as $val)
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
			}
		}

		$program_cond="";
		if($all_program_no)
		{
			$all_program_no = implode(",",array_filter(array_unique(explode(",", $all_program_no))));
			$fso_nos_arr = explode(",", $all_program_no);
			if($db_type==0)
			{
				$program_cond = " and a.id in ($all_program_no )";
			}
			else
			{
				if(count($fso_nos_arr)>999)
				{
					$fso_nos_chunk_arr=array_chunk($fso_nos_arr, 999);
					$program_cond=" and (";
					foreach ($fso_nos_chunk_arr as $value)
					{
						$program_cond .="a.id in (".implode(",", $value).") or ";
					}
					$program_cond=chop($program_cond,"or ");
					$program_cond.=")";
				}
				else
				{
					$program_cond = " and a.id in ($all_program_no )";
				}
			}
		}

		$knitting_company_arr=array();
		$program_sql =  sql_select("SELECT a.id, a.knitting_source, a.knitting_party from ppl_planning_info_entry_dtls a where  a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 $program_cond ");

		foreach ($program_sql as $val)
		{
			$knitting_company_arr[$val[csf("id")]]['knitting_party'] = $knitting_party_arr[$val[csf("knitting_party")]];
			$knitting_company_arr[$val[csf("id")]]['knitting_source'] = $val[csf("knitting_source")];
		}

		$data_arr=array();$batch_check=array();
		foreach($batchdata as $batch)
		{
			$knitting_source=$knitting_company_arr[$batch[csf("program_no")]]['knitting_source'];
			if ($knitting_source==3) 
			{
				$machine_or_company=$knitting_company_arr[$batch[csf("program_no")]]['knitting_party'];
			}
			else
			{
				$machine_or_company=$machine_name_arr[$machine_id_arr[$batch[csf("dtls_id")]]];
			}			

			$str_ref=$batch[csf("booking_no")].'*'.$batch[csf("program_no")].'*'.$batch[csf("fso_no")].'*'.$batch[csf("body_part_id")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['batch_against']=$batch[csf("batch_against")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['batch_no']=$batch[csf("batch_no")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['batch_date']=$batch[csf("batch_date")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['batch_weight']=$batch[csf("batch_weight")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['total_trims_weight']=$batch[csf("total_trims_weight")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['color_id']=$batch[csf("color_id")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['extention_no']=$batch[csf("extention_no")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['item_description']=$batch[csf("item_description")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['po_id']=$batch[csf("po_id")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['batch_qnty']+=$batch[csf("batch_qnty")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['style_ref_no']=$batch[csf("style_ref_no")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['dyeing_machine']=$batch[csf("dyeing_machine")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['dtls_id']=$batch[csf("dtls_id")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['po_buyer']=$batch[csf("po_buyer")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['buyer_id']=$batch[csf("buyer_id")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['roll_no']++;
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['remarks']=$batch[csf("remarks")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['process_id']=$batch[csf("process_id")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['load_unload_id']=$batch[csf("load_unload_id")];
			$data_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company][$str_ref]['within_group']=$batch[csf("within_group")];
			
			// $batch_wise_mc_count_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company]++;
			$batch_wise_mc_arr[$batch[csf("id")]][$batch[csf("prod_id")]][$machine_or_company]=$machine_or_company;

			if ($batch_check[$batch[csf("id")]]=="") 
			{
				$total_no_of_batch[$batch[csf("id")]]++;
				$batch_check[$batch[csf("id")]]=$batch[csf("id")];
			}
		}
		// echo "<pre>";print_r($batch_wise_mc_arr);die;

		$batch_check=array();
		foreach ($batch_wise_mc_arr as $batch => $batch_data) 
		{
			foreach ($batch_data as $prod_id => $row) 
			{
				if ($batch_check[$batch]=="") 
				{
					$batch_check[$batch]=$batch;
				
					if(count($row) > 1 )
					{
						$no_of_mc_mixed_batch++; // Red color
					}
					else
					{
						$no_of_fresh_batch++; // normal color
					}
				}
			}
		}
		// echo $no_of_mc_mixed_batch;
		// echo "<pre>";print_r($total_batch);
	}
	
	if ($operation==1) // Show
	{
		$yarn_count_arr  = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0","id","yarn_count");
		$brand_name_arr  = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0","id","brand_name");
		$load_unload_arr = array(1=>"Loading",2=>"Un-loading",3=>"Waiting for loading");
		
		?>
		<style type="text/css">
			.word_wrap_break {
				word-break: break-all;
				word-wrap: break-word;
			}
		</style>
	    <div align="left">
	        <fieldset style="width:2285px;">
	        	<?
	        	if(count($batchdata)>0)
	        	{
	        		?>
		            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong>
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
	                { ?>

	                    <div align="center"> <b>Self Batch </b></div>
	                    <table class="rpt_table" width="2565" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	                        <thead>
	                            <tr>
	                                <th width="30" class="word_wrap_break">SL</th>
	                                <th width="75" class="word_wrap_break">Batch Date</th>
	                                <th width="60" class="word_wrap_break">Batch No</th>
	                                <th width="40" class="word_wrap_break">Ext. No</th>
	                                <th width="80" class="word_wrap_break">Batch Against</th>
	                                <th width="80" class="word_wrap_break">Batch Color</th>
	                                <th width="80" class="word_wrap_break">Buyer</th>
	                                <th width="60" class="word_wrap_break">Job No</th>
	                                <th width="70" class="word_wrap_break">Style No</th>
	                                <th width="110" class="word_wrap_break">FSO No</th>
	                                <th width="120" class="word_wrap_break">Fabric Booking No.</th>
	                                <th width="50" class="word_wrap_break">Booking Type</th>
	                                <th width="70" class="word_wrap_break">Short Booking Type</th>
	                                <th width="70" class="word_wrap_break">Body Part</th>
	                                <th width="100" class="word_wrap_break">Construction</th>
	                                <th width="150" class="word_wrap_break">Fab. Composition</th>
	                               <!--  <th width="50">Booking Dia</th> -->
	                                <th width="50" class="word_wrap_break">Dia/ Width</th>
	                                <!-- <th width="50">Booking GSM</th> -->
	                                <th width="50" class="word_wrap_break">GSM</th>
	                                <th width="50" class="word_wrap_break">Program No</th>
	                                <th width="100" class="word_wrap_break">M/C No/Knitting Company</th>
	                                <th width="50" class="word_wrap_break">M/C Dia & Gauge</th>
	                                <th width="70" class="word_wrap_break">Yarn Count</th>
	                                <th width="120" class="word_wrap_break">Yarn Composition</th>
	                                <th width="70" class="word_wrap_break">Yarn Type</th>
	                                <th width="70" class="word_wrap_break">Yarn Brand</th>
	                                <th width="100" class="word_wrap_break">Y.Lot No</th>
	                                <th width="70" class="word_wrap_break">Dyeing Machine</th>
	                                <th width="70" class="word_wrap_break">Fabric Weight.</th>
	                                <th width="70" class="word_wrap_break">Trims Weight</th>
	                                <th width="70" class="word_wrap_break">Total Batch Weight</th>
	                                <th width="40" class="word_wrap_break">No of Roll</th>
	                                <th width="70" class="word_wrap_break">Batch Status</th>
	                                <th class="word_wrap_break" width="100">Remarks</th>
	                                <th class="word_wrap_break" width="100">Process Name</th>
	                            </tr>
	                        </thead>
	                    </table>
	                    <div style=" max-height:350px; width:2585px; overflow-y:scroll;" id="scroll_body">
	                        <table class="rpt_table" id="table_body" width="2565" cellpadding="0" cellspacing="0" border="1" rules="all">
	                            <tbody>
	                                <?
	                                $i=1;$btq=0;
	
	                                foreach($data_arr as $batch_id => $batch_value)
	                                {
	                                	foreach ($batch_value as $prod_id => $prod_id_value) 
	                                	{
	                                		foreach ($prod_id_value as $machineCompany => $machineCompany_v) 
	                                		{
		                                		foreach ($machineCompany_v as $str_ref => $batch) 
		                                		{
		                                			$data=explode("*", $str_ref);
		                                			$booking_no=$data[0];
		                                			$program_no=$data[1];
		                                			$fso_no=$data[2];
		                                			$body_part_id=$data[3];

				                                    $grey_production_dtls_ids = array_unique(explode(",",$batch['dtls_id']));
				                                    $yarn_prod_ids=$machine_gg_no=$machine_dia=$machine_no_id=$machine_names=$batch_status=""; $yarn_prod_id_arr = array();
				                                    foreach ($grey_production_dtls_ids as $dtlid)
				                                    {
				                                        $yarn_prod_ids .= $yarn_lot_arr[$dtlid][$prod_id]['yarn_prod_id'].',';
				                                        $machine_gg_no .= $yarn_lot_arr[$dtlid][$prod_id]['machine_gg'].',';
				                                        $machine_dia .= $yarn_lot_arr[$dtlid][$prod_id]['machine_dia'].',';
				                                        $machine_no_id .= $yarn_lot_arr[$dtlid][$prod_id]['machine_no_id'].',';
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
				                                    // print_r($yarn_prod_id_arr);
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
				                                    $batch_status = ($batch["load_unload_id"]) ? $batch["load_unload_id"] : "3";

				                                    $desc = explode(",", $batch['item_description']);

				                                    $process_name = '';
													$process_id_array = explode(",", $batch["process_id"]);
													foreach ($process_id_array as $val)
													{
														if ($process_name == ""){
															$process_name = $conversion_cost_head_array[$val];
														}
														else{
															$process_name .= "," . $conversion_cost_head_array[$val];
														}
													}

				                                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				                                    // Same Batch same product different M/C No/Knitting Company then show red color
				                                    if(count($batch_wise_mc_arr[$batch_id][$prod_id]) > 1 )
				                                    {
				                                    	$bgcolor="red";
				                                    	if ($mixed_btch_ck[$batch_id]=="") 
				                                    	{
				                                    		$mixed_btch_ck[$batch_id]=$batch_id;
				                                    	}
				                                    }

				                                    ?>
				                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				                                        <td class="word_wrap_break" width="30"><? echo $i; ?></td>
				                                        <td class="word_wrap_break" align="center" width="75" title="<? echo change_date_format($batch['batch_date']); ?>"><p><? echo change_date_format($batch['batch_date']); ?></p></td>
				                                        <td class="word_wrap_break" width="60" title="<? echo $batch_id; ?>"><p><? echo $batch['batch_no']; ?></p></td>
				                                        <td class="word_wrap_break"  width="40" title="<? echo $batch['extention_no']; ?>"><p><? echo $batch['extention_no']; ?></p></td>
				                                        <td  width="80"><p><? echo $batch_against[$batch['batch_against']]; ?></p></td>
				                                        <td width="80"><p class="word_wrap_break"><? echo $color_library[$batch['color_id']]; ?></p></td>
				                                        <td width="80">
				                                        	<p class="word_wrap_break">
				                                        	<?
				                                        		if($batch['within_group'] == 1)
				                                        		{
																	$buyer_id = $batch['po_buyer'];
																}
																else
																{
																	$buyer_id = $batch['buyer_id'];
																}
																	echo $buyer_arr[$buyer_id];
				                                        	?>
				                                        	</p>
				                                        </td>
				                                        <td class="word_wrap_break" width="60" align="center"><p><? echo chop($job_from_fso_arr[$fso_no]["job_no"],","); ?></p></td>
				                                        <td width="70"><p class="word_wrap_break"><? echo $batch['style_ref_no']; ?></p></td>
				                                        <td class="word_wrap_break" width="110"><p><?  echo $fso_no; ?></p></td>
				                                        <td class="word_wrap_break" width="120"><p><? echo $booking_no; ?></p></td>
				                                        <td class="word_wrap_break" width="50"><p><? echo $booking_type_arr[$booking_no]; ?></p></td>
				                                        <td class="word_wrap_break" width="70"><p><? echo $short_booking_type_arr[$booking_no]; ?></p></td>
				                                        <td class="word_wrap_break" width="70"><p><? echo $body_part[$body_part_id]; ?></p></td>
				                                        <td width="100" align="center" title=" Prod ID:<? echo $prod_id;?>"><p class="word_wrap_break"><? echo $desc[0]; ?></p></td>
				                                        <td width="150" align="center"><p class="word_wrap_break"><? echo $desc[1]; ?></p></td>
				                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $desc[3]; ?></p></td>
				                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $desc[2]; ?></p></td>
				                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $program_no; ?></p></td>
				                                        <td width="100" align="center"><p class="word_wrap_break"><? echo $machineCompany;
				                                        //.'<br>'.$knitting_company_arr[$batch[csf('program_no')]]; ?></p></td>
				                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $machine_dia_gg; ?></p></td>

				                                        <td width="70" align="center"><p class="word_wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_name; ?></p></td>
				                                        <td width="120" align="center"><p class="word_wrap_break"><? echo $yarn_comp_name; ?></p></td>
				                                        <td width="70" align="center"><p class="word_wrap_break"><? echo $yarn_type_name; ?></p></td>
				                                        <td width="70" align="center"><p class="word_wrap_break"><? echo $yarn_brand_name; ?></p></td>

				                                        <td class="word_wrap_break" width="100" title="<? echo 'Prod Id'.$batch['prod_id'].'=PO ID'.$order_id;?>" align="left"><div style="width:60px; word-wrap:break-word;"><? echo $lot_no; ?></div></td>
				                                        <td class="word_wrap_break" width="70" align="center"><? echo $machine_name_arr[$batch['dyeing_machine']];?></td>
				                                        <td class="word_wrap_break" align="right" width="70" title="<? echo $batch['batch_qnty'];  ?>"><? echo number_format($batch['batch_qnty'],2);  ?></td>
				                                        <?
				                                        if($batch_chk[$batch_id]=="")
				                                        {
				                                            ?>
				                                            <td class="word_wrap_break" align="right" width="70" title="<? echo $batch['total_trims_weight']; ?>">
				                                                <?
				                                                echo $batch['total_trims_weight'];
				                                                ?>
				                                            </td>
				                                            <td class="word_wrap_break" align="right" width="70" title="<? echo $batch['batch_weight']; ?>">
				                                                <?
				                                                echo number_format(($batch['batch_qnty']+$batch['total_trims_weight']),2);
				                                                ?>
				                                            </td>
				                                            <?
				                                            $bttw+=$batch['total_trims_weight'];
				                                            $bw+=($batch['batch_qnty']+$batch['total_trims_weight']);
				                                        }
				                                        else
				                                        {
				                                            ?>
				                                            <td class="word_wrap_break" align="right" width="70">&nbsp;</td>
				                                            <td class="word_wrap_break" align="right" width="70">
				                                                <?
				                                                echo number_format($batch['batch_qnty'],2);
				                                                ?>
				                                            </td>
				                                            <?
				                                            $bw+=$batch['batch_qnty'];
				                                        }
				                                        ?>
				                                        <td class="word_wrap_break" width="40" align="center"><? echo $batch["roll_no"];?></td>
				                                        <td class="word_wrap_break" align="center" width="70"><? echo $load_unload_arr[$batch_status];?></td>
				                                        <td width="100" class="word_wrap_break" align="center"><? echo $batch["remarks"];?></td>
				                                        <td width="100" class="word_wrap_break" align="center"><? echo $process_name;?></td>
				                                    </tr>
				                                    <?
				                                    $i++;
				                                    $btq+=$batch['batch_qnty'];
				                                    $batch_chk[$batch_id] = $batch_id;
				                                    $tot_batch_roll_no += $batch["roll_no"];
				                                }
				                            }
				                        }
				                    }
	                                ?>
	                            </tbody>
	                        </table>
	                    </div>
	                    <table class="rpt_table" width="2565" cellpadding="0" cellspacing="0" border="1" rules="all">
	                        <tfoot>
	                            <tr>
	                            	<th width="30">&nbsp;</th>
	                                <th width="75">&nbsp;</th>
	                                <th width="60">&nbsp;</th>
	                                <th width="40">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="80">&nbsp;</th>
	                                <th width="60">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="110">&nbsp;</th>
	                                <th width="120">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="150">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="50">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="120">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th width="100">&nbsp;</th>
	                                <th width="70">&nbsp;</th>
	                                <th class="word_wrap_break" width="70" id="value_batch_qnty" style="text-align: right"><? echo number_format($btq,2); ?></th>
	                                <th class="word_wrap_break" width="70" id="value_total_trims_weight" style="text-align: right"><? echo number_format($bttw,2); ?></th>
	                                <th class="word_wrap_break" width="70" id="value_batch_weight" style="text-align: right"><? echo number_format($bw,2); ?></th>
	                                <th class="word_wrap_break" width="40" id="value_roll_no"><? echo $tot_batch_roll_no;?></th>
	                                <th class="word_wrap_break" width="70">&nbsp;</th>
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
	else // Summary
	{
    	if(count($batchdata)>0)
    	{
    		?>
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong>
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
		<table cellpadding="0" style="margin:5px;"  width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">                            
			<thead>
				<tr>
					<th colspan="2">Summary</th>
				</tr>
			</thead>
			<tbody>
				<?
				foreach($data_arr as $batch_id => $batch_value)
                {
                	foreach ($batch_value as $prod_id => $prod_id_value) 
                	{
                		foreach ($prod_id_value as $machineCompany => $machineCompany_v) 
                		{
                    		foreach ($machineCompany_v as $str_ref => $batch) 
                    		{
                    			// Same Batch same product different machin/knitting company then show red color
                                if(count($batch_wise_mc_arr[$batch_id][$prod_id]) > 1 )
                                {
                                	$bgcolor="red";
                                	if ($mixed_btch_ck[$batch_id]=="") 
                                	{
                                		$mixed_btch_ck[$batch_id]=$batch_id;
                                	}				                                    	
                                }
                    		}
                    	}
                    }
                }
				$bgcolor_dyeing="#E9F3FF";  $bgcolor_dyeing2="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor_dyeing2;?>">						 
					<td width="130">Total No of Batch</td>
                    <td width="80" align="right"><? echo count($total_no_of_batch);?></td>
				</tr>
                <tr bgcolor="<? echo $bgcolor_dyeing;?>">						 
					<td title="Normal Color">No of Fresh Batch</td>
                    <td align="right"><? echo count($total_no_of_batch)-count($mixed_btch_ck);//$no_of_fresh_batch;?></td>
				</tr>
				<tr bgcolor="<? echo $bgcolor_dyeing2;?>">						 
					<td title="Red Color">No of M/C Mixed Batch</td>
                    <td align="right"><? echo count($mixed_btch_ck);//echo $no_of_mc_mixed_batch;?></td>
				</tr>
				<?
				?>
			</tbody>
		</table>
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
    echo "$html**$filename**$report_type";
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'batch_ledger_report_for_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
?>