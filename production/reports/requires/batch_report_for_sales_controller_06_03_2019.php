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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_booking_type').value, 'create_booking_no_search_list_view', 'search_div', 'batch_report_for_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
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
	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_id);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
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
	$booking_no = str_replace("'","",$txt_booking_no_show);
	$process=33;
	
	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='$buyer'";
	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_name='$buyer'";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".str_replace("'","",$batch_no)."'";	
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";	
	if ($job_number_id=="") $jobdata=""; else $jobdata="  a.job_no_prefix_num in($job_number_id)";
	
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $find_inset="and  FIND_IN_SET(33,a.process_id)"; else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
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
		if($booking_no !="")
		{
			$booking_no = "'".implode("','",explode(",", $booking_no))."'";
		}
	
		$booking="";$fso_nos="";
		if ($jobdata != "" || $year_cond != "" || $buyer_cond != "")
		{
			$jobNo_arr=sql_select("select c.booking_no from wo_po_details_master a,wo_po_break_down b,wo_booking_dtls c where $jobdata $year_cond $buyer_cond and a.job_no=b.job_no_mst and c.po_break_down_id=c.po_break_down_id and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1");
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
	
		if($all_booking_no_cond != "" || $sales_orders_cond != ""){
			$sql_sales_order= "SELECT a.id,a.job_no from  fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sales_orders_cond $all_booking_no_cond group by a.id,a.job_no";
			$result_data=sql_select($sql_sales_order);
			foreach ($result_data as $value) 
			{
				$sales_ord_wise_fso_arr[$value[csf("id")]]=$value[csf("id")];
				//$sales_ord_wise_fso_arr[$value[csf("job_no")]]=$value[csf("job_no")];
			}	
	
			$fso_nos = implode(",", $sales_ord_wise_fso_arr);
		}
		//==============================booking type===================
		/*$sql_booking_type="SELECT booking_no, booking_type, is_short from wo_booking_mst where status_active=1 and is_deleted=0
		union all select booking_no, booking_type, is_short from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0";
		$sql_booking_type_data=sql_select($sql_booking_type);
		foreach ($sql_booking_type_data as $value) 
		{
			if($value[csf("booking_type")]==1 && $value[csf("is_short")]==2)
			{
				$booking_type_arr[$value[csf("booking_no")]]="Main";
			}
			else if($value[csf("booking_type")]==1 && $value[csf("is_short")]==1)
			{
				$booking_type_arr[$value[csf("booking_no")]]="Short";
			}
			else if($value[csf("booking_type")]==4)
			{
				$booking_type_arr[$value[csf("booking_no")]]="Sample";
			}
		}*/
		//==============================booking type===================
	
		if($db_type==2)
		{
			$machine_name_arr  = return_library_array("select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id","machine_name");
		}
		else if($db_type==0)
		{
			$machine_name_arr  = return_library_array("select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id","machine_name");
		}
	
		/*$sql="select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id, a.booking_no, a.extention_no, b.item_description, b.prod_id,b.po_id, sum(b.batch_qnty) as batch_qnty,c.job_no as fso_no,
		c.style_ref_no ,listagg(cast(e.yarn_lot as varchar2(4000)), ',') within group (order by e.yarn_lot) as yarn_lot,a.dyeing_machine
		from pro_batch_create_dtls b,pro_batch_create_mst a, fabric_sales_order_mst c , pro_roll_details d,pro_grey_prod_entry_dtls e , inv_receive_master f
		where b.mst_id = a.id and c.id = b.po_id and a.is_sales = 1 and   b.roll_id = d.id and d.mst_id = f.id and e.mst_id = f.id and d.dtls_id = e.id
		and b.is_deleted = 0 and a.company_id = $company $dates_com $batch_num $ext_no and f.entry_form in (2,22)
		group by a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no,a.extention_no, b.item_description, b.prod_id,b.po_id,c.job_no,c.style_ref_no,a.dyeing_machine,a.total_trims_weight";*/
		$fso_cond = (trim($fso_nos)!="")?" and c.id in($fso_nos)":"";
		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.booking_no,a.extention_no, b.item_description,b.prod_id,b.po_id,sum(b.batch_qnty) as batch_qnty,c.job_no as fso_no,c.style_ref_no,a.dyeing_machine,listagg(d.dtls_id, ',') within group (order by d.dtls_id) as dtls_ids,c.po_buyer from pro_batch_create_mst a,pro_batch_create_dtls b,fabric_sales_order_mst c,pro_roll_details d
		where a.company_id=$company and a.status_active=1 $dates_com $batch_num $ext_no $fso_cond and a.is_sales=1 and a.id=b.mst_id and b.po_id=c.id and b.roll_id=d.id and b.is_deleted=0 and d.entry_form in(2,22) and d.status_active=1 and c.status_active=1
		group by a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no,a.extention_no, b.item_description, b.prod_id,b.po_id,c.job_no,c.style_ref_no,a.dyeing_machine,a.total_trims_weight,c.po_buyer";
	
		if($batch_type==0 || $batch_type==1)
		{
			$batchdata=sql_select($sql);
			foreach($batchdata as $batch)
			{
				$grey_production_dtls_id_arr[] = $batch[csf("dtls_ids")];
				$sales_ord_wise_fso_arr[$batch[csf("po_id")]]=$batch[csf("po_id")];
			}
			$fso_nos = implode(",", $sales_ord_wise_fso_arr);
		}
	
		$yarn_lot_arr=array();
		if(!empty($grey_production_dtls_id_arr)){
			$grey_production_dtls_id_arr = implode(",",array_unique(explode(",",implode(",",$grey_production_dtls_id_arr))));
			if($db_type==0)
			{
				$yarn_lot_data=sql_select("select a.id dtls_id,b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' and a.id in(". $grey_production_dtls_id_arr .") group by a.id,a.prod_id,a.yarn_lot, b.po_breakdown_id");
			}
			else if($db_type==2)
			{
				$yarn_lot_data=sql_select("select a.id dtls_id,b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot is not null and a.id in(". $grey_production_dtls_id_arr .") group by a.id,a.prod_id,a.yarn_lot, b.po_breakdown_id");
			}
			foreach($yarn_lot_data as $rows)
			{
				$yarn_lot_arr[$rows[csf('dtls_id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarnlot')].',';
			}
		}
	
		/*$buyer_from_sales= return_library_array("select a.job_no , (case when a.within_group = 2 then a.buyer_id when a.within_group = 1 and b.id is not null then b.buyer_id when a.within_group = 1 and c.id is not null then c.buyer_id end) buyer_id 
			from fabric_sales_order_mst a left join wo_booking_mst b on a.sales_booking_no = b.booking_no and b.status_active = 1 left join   wo_non_ord_samp_booking_mst c on a.sales_booking_no = c.booking_no 
			and c.status_active = 1 where a.status_active = 1 and a.company_id= $company",'job_no','buyer_id');*/
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
		$job_from_fso =  sql_select("select c.booking_no,c.booking_type,c.is_short, b.job_no_prefix_num,b.job_no, a.job_no as fso_no from fabric_sales_order_mst a, wo_booking_dtls c,wo_po_details_master b where a.sales_booking_no=c.booking_no and c.job_no=b.job_no and a.company_id=$company $fso_no_cond
			union all
			select b.booking_no,4 as booking_type,0 as is_short,0 as job_no_prefix_num,null as job_no, a.job_no as fso_no from  fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b where a.sales_booking_no=b.booking_no and  a.company_id=$company $fso_no_cond");
		foreach ($job_from_fso as $val) 
		{
			if($job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] == "")
			{
				$job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
				$job_from_fso_arr[$val[csf("fso_no")]]["job_no"] .= $val[csf("job_no_prefix_num")].",";
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
	}
	if($batch_type==0 || $batch_type==2)
	{
		/*if($job_number_id!=0 || $txt_order!="" || $file_no!="" || $ref_no!="")
		{
			$sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.color_id,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type order by a.batch_no";
		}
		else
		{
			$sub_cond="(select a.id, a.batch_against, a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b, pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type)
			union
			(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type) order by batch_date";	
		}*/
		$sub_ord_arr=array();
		$sub_sql="select a.subcon_job, a.party_id, b.id, b.order_no, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst";
		$sub_sql_res=sql_select($sub_sql);
		foreach($sub_sql_res as $row)
		{
			$sub_ord_arr[$row[csf('id')]]['job']=$row[csf('subcon_job')];
			$sub_ord_arr[$row[csf('id')]]['party']=$row[csf('party_id')];
			$sub_ord_arr[$row[csf('id')]]['order']=$row[csf('order_no')];
			$sub_ord_arr[$row[csf('id')]]['style_ref']=$row[csf('cust_style_ref')];
		}
		unset($sub_sql_res);
		$sub_cond="select a.id, a.batch_against, a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.total_trims_weight, a.color_id, a.booking_no, a.extention_no, b.item_description, b.prod_id, b.po_id, sum(b.batch_qnty) as batch_qnty, '' as fso_no, '' as style_ref_no, a.dyeing_machine, '' as dtls_ids, 0 as po_buyer from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.company_id=$company and a.status_active=1 $dates_com $batch_num $ext_no and a.id=b.mst_id and b.is_deleted=0 and a.entry_form =36
		group by a.id, a.batch_against, a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, b.item_description, b.prod_id, b.po_id,  a.dyeing_machine, a.total_trims_weight";
		$subbatchdata=sql_select($sub_cond);
	}
	ob_start();
	?>
    <div align="left">
        <fieldset style="width:1365px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong>
            <br><b>
                <?
                echo ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
                ?> </b>
            </div>
            <div align="left">
				<? 
                if($batch_type==0 || $batch_type==1)
                { ?>
    
                    <div align="center"> <b>Self Batch </b></div>
                    <table class="rpt_table" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th width="30">SL</th>
                                <th width="75">Batch Date</th>
                                <th width="60">Batch No</th>
                                <th width="40">Ext. No</th>
                                <th width="80">Batch Against</th>
                                <th width="80">Batch Color</th>
                                <th width="80">Buyer</th>
                                <th width="60">Job No</th>
                                <th width="70">Style No</th>
                                <th width="110">FSO No</th>
                                <th width="120">Fabric Booking No.</th> 
                                <th width="50">Booking Type</th>
                                <th width="100">Construction</th>
                                <th width="150">Composition</th>
                                <th width="50">Dia/ Width</th>
                                <th width="50">GSM</th>
                                <th width="60">Y.Lot No</th>
                                <th width="70">Dyeing Machine</th>
                                <th width="70">Fabric Weight.</th>
                                <th width="70">Trims Weight</th>
                                <th width="">Total Batch Weight</th>
                            </tr>
                        </thead>
                    </table>
                    <div style=" max-height:350px; width:1598px; overflow-y:scroll;" id="scroll_body">
                        <table class="rpt_table" id="table_body" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <tbody>
                                <? 
                                $i=1;$btq=0;
                                foreach($batchdata as $batch)
                                { 	
                                    $grey_production_dtls_ids = array_unique(explode(",",$batch[csf('dtls_ids')]));
                                    foreach ($grey_production_dtls_ids as $dtlid) {
                                        $ylot=chop($yarn_lot_arr[$dtlid][$batch[csf('prod_id')]][$batch[csf('po_id')]]['lot'],',');
                                    }
                                    $yarn_lots=implode(",",array_unique(explode(",",$ylot)));
                                    $desc = explode(",", $batch[csf('item_description')]);
    
                                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                        <td width="30"><? echo $i; ?></td>
                                        <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                        <td  width="60" title="<? echo $batch[csf('id')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                                        <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                        <td  width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $buyer_arr[$batch[csf('po_buyer')]];//; ?></div></td>					
                                        <td width="60" align="center"><p><? echo chop($job_from_fso_arr[$batch[csf('fso_no')]]["job_no"],","); ?></p></td>
                                        <td width="70"><p><? echo $batch[csf('style_ref_no')]; ?></p></td>
                                        <td width="110"><p><?  echo $batch[csf('fso_no')]; ?></p></td>
                                        <td width="120"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                                        <td width="50"><p><? echo $booking_type_arr[$batch[csf('booking_no')]]; ?></p></td>
                                        <td width="100"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
                                        <td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $desc[1]; ?></div></td>
                                        <td width="50"><p><? echo $desc[3]; ?></p></td>
                                        <td width="50"><p><? echo $desc[2]; ?></p></td>
                                        <td width="60" title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
                                        <td width="70" align="center"><? echo $machine_name_arr[$batch[csf('dyeing_machine')]];?></td>
                                        <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                                        <? 
                                        if($batch_chk[$batch[csf('id')]]=="")
                                        {
                                            ?>
                                            <td align="right" width="70" title="<? echo $batch[csf('total_trims_weight')]; ?>">
                                                <? 
                                                echo $batch[csf('total_trims_weight')];  
                                                ?>
                                            </td>
                                            <td align="right" width="" title="<? echo $batch[csf('batch_weight')]; ?>">
                                                <? 
                                                echo number_format(($batch[csf('batch_qnty')]+$batch[csf('total_trims_weight')]),2); 
                                                ?>
                                            </td>
                                            <? 
                                            $bttw+=$batch[csf('total_trims_weight')];
                                            $bw+=($batch[csf('batch_qnty')]+$batch[csf('total_trims_weight')]);
                                        }
                                        else 
                                        {
                                            ?>
                                            <td align="right" width="70">&nbsp;</td>
                                            <td align="right">
                                                <? 
                                                echo number_format($batch[csf('batch_qnty')],2); 
                                                ?>
                                            </td>
                                            <?
                                            $bw+=$batch[csf('batch_qnty')];
                                        }
                                        ?>
                                    </tr>
                                    <? 
                                    $i++;
                                    $btq+=$batch[csf('batch_qnty')];
                                    $batch_chk[$batch[csf('id')]] = $batch[csf('id')];
                                } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table class="rpt_table" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all">
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
                                <th width="100">&nbsp;</th>
                                <th width="150">&nbsp;</th>
                                <th width="50">&nbsp;</th>
                                <th width="50">&nbsp;</th>
                                <th width="60">&nbsp;</th>
                                <th width="70">&nbsp;</th>
                                <th width="70" id="value_batch_qnty" style="text-align: right"><? echo number_format($btq,2); ?></th>
                                <th width="70" id="value_total_trims_weight" style="text-align: right"><? echo number_format($bttw,2); ?></th>
                                <th id="value_batch_weight" style="text-align: right"><? echo number_format($bw,2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <? 
                } 
                if($batch_type==0 || $batch_type==2)
                { ?>
                    <div align="center"><b>SubCon Batch</b></div>
                    <table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th width="30">SL</th>
                                <th width="75">Batch Date</th>
                                <th width="60">Batch No</th>
                                <th width="40">Ext. No</th>
                                <th width="80">Batch Against</th>
                                <th width="80">Batch Color</th>
                                <th width="80">Party</th>
                                <th width="100">Job No</th>
                                <th width="80">Cust. Style Ref.</th>
                                <th width="110">Order No</th>
                                
                                <th width="100">Construction</th>
                                <th width="150">Composition</th>
                                <th width="50">Dia/ Width</th>
                                <th width="50">GSM</th>
                                <th width="70">Dyeing Machine</th>
                                <th width="70">Fabric Weight.</th>
                                <th width="70">Trims Weight</th>
                                <th>Total Batch Weight</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="max-height:350px; width:1420px; overflow-y:scroll;" id="scroll_body">
                        <table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <tbody>
                                <? 
                                $i=1;$btq=0;
                                foreach($subbatchdata as $row)
                                { 	
                                    $desc = explode(",", $row[csf('item_description')]);
    
                                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                        <td width="30"><? echo $i; ?></td>
                                        <td align="center" width="75" title="<? echo change_date_format($row[csf('batch_date')]); ?>"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
                                        <td width="60" title="<? echo $row[csf('id')]; ?>"><p><? echo $row[csf('batch_no')]; ?></p></td>
                                        <td width="40" title="<? echo $row[csf('extention_no')]; ?>"><? echo $row[csf('extention_no')]; ?></td>
                                        <td width="80" style="word-break:break-all"><? echo $batch_against[$row[csf('batch_against')]]; ?></td>
                                        <td width="80" style="word-break:break-all"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                        <td width="80" style="word-break:break-all"><? echo $buyer_arr[$sub_ord_arr[$row[csf('po_id')]]['party']];//; ?></td>					
                                        <td width="100" style="word-break:break-all"><? echo $sub_ord_arr[$row[csf('po_id')]]['job']; ?></td>
                                        <td width="80" style="word-break:break-all"><? echo $sub_ord_arr[$row[csf('po_id')]]['style_ref']; ?></td>
                                        <td width="110" style="word-break:break-all"><? echo $sub_ord_arr[$row[csf('po_id')]]['order']; ?></td>
                                        
                                        <td width="100" style="word-break:break-all"><? echo $desc[0]; ?></td>
                                        <td width="150" style="word-break:break-all"><? echo $desc[1]; ?></td>
                                        <td width="50"><p><? echo $desc[3]; ?></p></td>
                                        <td width="50"><p><? echo $desc[2]; ?></p></td>
                                        
                                        <td width="70" align="center"><? echo $machine_name_arr[$row[csf('dyeing_machine')]];?></td>
                                        <td align="right" width="70" title="<? echo $row[csf('batch_qnty')];  ?>"><? echo number_format($row[csf('batch_qnty')],2);  ?></td>
                                        <? 
                                        if($sbatch_chk[$row[csf('id')]]=="")
                                        {
                                            ?>
                                            <td align="right" width="70" title="<? echo $row[csf('total_trims_weight')];  ?>">
                                                <? 
                                                echo $row[csf('total_trims_weight')];  
                                                ?>
                                            </td>
                                            <td  align="right" width="" title="<? echo $row[csf('batch_weight')]; ?>">
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
                                            <td align="right" width="70">&nbsp;</td>
                                            <td  align="right">
                                                <? 
                                                echo number_format($row[csf('batch_qnty')],2); 
                                                ?>
                                            </td>
                                            <?
                                            $bw+=$row[csf('batch_qnty')];
                                        }
                                        ?>
                                    </tr>
                                    <? 
                                    $i++;
                                    $sbtq+=$row[csf('batch_qnty')];
                                    $sbatch_chk[$row[csf('id')]] = $row[csf('id')];
                                } 
                                ?>
                            </tbody>
                        </table>
                        </div>
                        <table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <tfoot>
                                <tr>
                                    <th width="30">&nbsp;</th>
                                    <th width="75">&nbsp;</th>
                                    <th width="60">&nbsp;</th>
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
                                    <th width="70" id="value_sbatch_qnty" style="text-align: right"><? echo number_format($sbtq,2); ?></th>
                                    <th width="70" id="value_stotal_trims_weight" style="text-align: right"><? echo number_format($sbttw,2); ?></th>
                                    <th id="value_sbatch_weight" style="text-align: right"><? echo number_format($sbw,2); ?></th>
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

if($action=="batch_report_old")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_id);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);
	$batch_type = str_replace("'","",$cbo_batch_type);

	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$year = str_replace("'","",$cbo_year);
//echo $order_no;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$fso_number_show = str_replace("'","",$fso_number_show);
	$booking_no = str_replace("'","",$txt_booking_no_show);
	$process=33;
//echo str_replace("'","",$batch_no);die;

	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='$buyer'";
	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_name='$buyer'";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".str_replace("'","",$batch_no)."'";

	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";

	if($batch_type==0 || $batch_type==1 ||  $batch_type==3)
	{
		if ($job_number_id=="") $jobdata=""; else $jobdata="  and a.job_no_prefix_num in($job_number_id)";
	}
//echo $jobdata;
	if($batch_type==0 || $batch_type==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num in(".$job_number_id.") "; else $sub_job_cond="";
		if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";

	}

	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $find_inset="and  FIND_IN_SET(33,a.process_id)"; 
	else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
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

	if($booking_no)
	{
		$booking_no = "'".implode("','",explode(",", $booking_no))."'";

	}




	$booking="";$fso_nos="";
	if ($jobdata)
	{

		$jobNo_arr=sql_select("select e.booking_no from wo_po_details_master a, wo_po_break_down d, wo_booking_mst e where a.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.job_no=e.job_no  and e.status_active=1 and e.is_deleted=0 $jobdata $year_cond $buyer_cond");
		foreach ($jobNo_arr as $value) 
		{
			$booking_no .= ($booking_no=="")? "'".$value[csf("booking_no")]."'" : ",'".$value[csf("booking_no")]."'";
	//$booking .= ($booking=="")? " and a.sales_booking_no in ('".$value[csf("booking_no")]."'" : ",'".$value[csf("booking_no")]."'";
		}
//$booking.=")";

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
//if($all_booking_no_cond){}
	$sql_sales_order= "SELECT a.job_no from  fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sales_orders_cond $all_booking_no_cond group by a.job_no";
	$result_data=sql_select($sql_sales_order);
	foreach ($result_data as $value) 
	{
		$sales_ord_wise_fso_arr[$value[csf("job_no")]]=$value[csf("job_no")];
	}	
//print_r($sales_ord_wise_fso_arr);die;
	$fso_nos = "'".implode("','", $sales_ord_wise_fso_arr)."'";

//==============================booking type===================
	$sql_booking_type="SELECT booking_no, booking_type, is_short from wo_booking_mst where status_active=1 and is_deleted=0
	union all select booking_no, booking_type, is_short from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0";
	$sql_booking_type_data=sql_select($sql_booking_type);
	foreach ($sql_booking_type_data as $value) 
	{
		if($value[csf("booking_type")]==1 && $value[csf("is_short")]==2)
		{
			$booking_type_arr[$value[csf("booking_no")]]="Main";
		}
		else if($value[csf("booking_type")]==1 && $value[csf("is_short")]==1)
		{
			$booking_type_arr[$value[csf("booking_no")]]="Short";
		}
		else if($value[csf("booking_type")]==4)
		{
			$booking_type_arr[$value[csf("booking_no")]]="Sample";
		}
	}

//==============================booking type===================


	$yarn_lot_arr=array();
	if($db_type==0)
	{
		$yarn_lot_data=sql_select("select b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' group by a.prod_id,a.yarn_lot, b.po_breakdown_id");
	}
	else if($db_type==2)
	{
		$yarn_lot_data=sql_select("select b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot is not null group by a.prod_id,a.yarn_lot, b.po_breakdown_id");
	}
	foreach($yarn_lot_data as $rows)
	{

		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarnlot')].',';
	}

	if($db_type==2)
	{
		$machine_name_arr  = return_library_array("select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id","machine_name");
	}
	else if($db_type==0)
	{
		$machine_name_arr  = return_library_array("select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id","machine_name");
	}

//$sql= " select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.color_id, a.booking_no, a.extention_no, b.item_description, b.prod_id,b.po_id, sum(b.batch_qnty) as batch_qnty,c.job_no as fso_no,c.style_ref_no from pro_batch_create_dtls b,pro_batch_create_mst a, fabric_sales_order_mst c where b.mst_id = a.id and c.id = b.po_id and a.is_sales = 1 and b.is_deleted = 0 and a.company_id = $company $dates_com $batch_num $ext_no group by a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no,a.extention_no, b.item_description, b.prod_id,b.po_id,c.job_no,c.style_ref_no";


	$sql="select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id, a.booking_no, a.extention_no, b.item_description, b.prod_id,b.po_id, sum(b.batch_qnty) as batch_qnty,c.job_no as fso_no,
	c.style_ref_no ,listagg(cast(e.yarn_lot as varchar2(4000)), ',') within group (order by e.yarn_lot) as yarn_lot,a.dyeing_machine
	from pro_batch_create_dtls b,pro_batch_create_mst a, fabric_sales_order_mst c , pro_roll_details d,pro_grey_prod_entry_dtls e , inv_receive_master f
	where b.mst_id = a.id and c.id = b.po_id and a.is_sales = 1 and   b.roll_id = d.id and d.mst_id = f.id and e.mst_id = f.id and d.dtls_id = e.id
	and b.is_deleted = 0 and a.company_id = $company $dates_com $batch_num $ext_no and f.entry_form in (2,22)
	group by a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.color_id, a.booking_no,a.extention_no, b.item_description, b.prod_id,b.po_id,c.job_no,c.style_ref_no,a.dyeing_machine,a.total_trims_weight";




	if($batch_type==0 || $batch_type==1)
	{
//echo $sql;
		$batchdata=sql_select($sql);
//print_r($batchdata);
	}
	else if($batch_type==0 || $batch_type==2)
	{

		$sub_batchdata=sql_select($sub_cond);
	}
	else if($batch_type==0 || $batch_type==3)
	{

		$sam_batchdata=sql_select($sql_sam);
	}

	$buyer_from_sales= return_library_array("select a.job_no , (case when a.within_group = 2 then a.buyer_id when a.within_group = 1 and b.id is not null then b.buyer_id when a.within_group = 1 and c.id is not null then c.buyer_id end) buyer_id 
		from fabric_sales_order_mst a left join wo_booking_mst b on a.sales_booking_no = b.booking_no and b.status_active = 1 left join   wo_non_ord_samp_booking_mst c on a.sales_booking_no = c.booking_no 
		and c.status_active = 1 where a.status_active = 1 and a.company_id= $company",'job_no','buyer_id');
	$fso_no_cond="";

/*if($fso_nos){
$fso_no_cond = " and a.job_no in ($fso_nos )";
}*/

if($fso_nos)
{
$fso_nos = implode(",",array_filter(array_unique(explode(",", $fso_nos))));
$fso_nos_arr = explode(",", $fso_nos);
if($db_type==0)
{
$fso_no_cond = " and a.job_no in ($fso_nos )";
}
else
{
if(count($fso_nos_arr)>999)
{
	$fso_nos_chunk_arr=array_chunk($fso_nos_arr, 999);
	$fso_no_cond=" and (";
	foreach ($fso_nos_chunk_arr as $value) 
	{
		$fso_no_cond .="a.job_no in (".implode(",", $value).") or ";
	}
	$fso_no_cond=chop($fso_no_cond,"or ");
	$fso_no_cond.=")";
}
else
{
	$fso_no_cond = " and a.job_no in ($fso_nos )";
}
}
}

$job_fso_chk=array();$job_from_fso_arr=array();
$job_from_fso =  sql_select("select c.booking_no, b.job_no_prefix_num,b.job_no, a.job_no as fso_no from  fabric_sales_order_mst a, wo_booking_dtls c, wo_po_details_master b where a.sales_booking_no = c.booking_no and  c.job_no = b.job_no and a.company_id =  $company $fso_no_cond");
foreach ($job_from_fso as $val) 
{
if($job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] == "")
{
$job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
$job_from_fso_arr[$val[csf("fso_no")]]["job_no"] .= $val[csf("job_no_prefix_num")].",";
}
}


/*$yarn_lot_arr=array();
if($db_type==0)
{
$yarn_lot_data=sql_select("select b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' group by a.prod_id,a.yarn_lot, b.po_breakdown_id");
}
else if($db_type==2)
{
$yarn_lot_data=sql_select("select b.po_breakdown_id as po_id, a.prod_id,a.yarn_lot as yarnlot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot is not null group by a.prod_id,a.yarn_lot, b.po_breakdown_id");
}
foreach($yarn_lot_data as $rows)
{
$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarnlot')].',';
}*/
ob_start();
?>
<div align="left">
<fieldset style="width:1365px;">
<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type]; ?> </strong>
	<br><b>
		<?
		echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
		?> </b>
	</div>
	<div align="left">
		<? 
		if($batch_type==0 || $batch_type==1)
			{ ?>

				<div align="center"> <b>Self Batch </b></div>
				<table class="rpt_table" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="75">Batch Date</th>
							<th width="60">Batch No</th>
							<th width="40">Ext. No</th>
							<th width="80">Batch Against</th>
							<th width="80">Batch Color</th>
							<th width="80">Buyer</th>
							<th width="120">Job No</th>
							<th width="70">Style No</th>
							<th width="70">FSO No</th>
							<th width="80">Fabric Booking No.</th> 
							<th width="70">Booking Type</th>
							<th width="100">Construction</th>
							<th width="150">Composition</th>
							<th width="50">Dia/ Width</th>
							<th width="50">GSM</th>
							<th width="60">Y.Lot No</th>
							<th width="70">Dyeing Machine</th>
							<th width="70">Fabric Weight.</th>
							<th width="70">Trims Weight</th>
							<th width="">Total Batch Weight</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:350px; width:1598px; overflow-y:scroll;" id="scroll_body">
					<table class="rpt_table" id="table_body" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tbody>
							<? 
							$i=1;$btq=0;
							foreach($batchdata as $batch)
							{ 	
								if($sales_ord_wise_fso_arr[$batch[csf('fso_no')]] != "")
								{ 
									$desc = explode(",", $batch[csf('item_description')]);
		//$ylot=chop($yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]['lot'],',');
		//$yarn_lots=implode(",",array_unique(explode(",",$ylot)));
									$yarn_lots=implode(",",array_unique(explode(",",$batch[csf('yarn_lot')])));
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
										<td width="30"><? echo $i; ?></td>
										<td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
										<td  width="60" title="<? echo $batch[csf('id')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
										<td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
										<td  width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
										<td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
										<td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $buyer_arr[$buyer_from_sales[$batch[csf('fso_no')]]];//; ?></div></td>					
										<td width="120" align="center"><p><? echo chop($job_from_fso_arr[$batch[csf('fso_no')]]["job_no"],","); ?></p></td>
										<td width="70"><p><? echo $batch[csf('style_ref_no')]; ?></p></td>
										<td width="70"><p><?  echo $batch[csf('fso_no')]; ?></p></td>
										<td width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
										<td width="70"><p><? echo $booking_type_arr[$batch[csf('booking_no')]]; ?></p></td>
										<td width="100"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
										<td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $desc[1]; ?></div></td>
										<td width="50"><p><? echo $desc[3]; ?></p></td>
										<td width="50"><p><? echo $desc[2]; ?></p></td>
										<td width="60" title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
										<td width="70" align="center"><? echo $machine_name_arr[$batch[csf('dyeing_machine')]];?></td>
										<td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
										<? 
										if($batch_chk[$batch[csf('id')]]=="")
										{
											?>
											<td align="right" width="70" title="<? echo $batch[csf('total_trims_weight')];  ?>">
												<? 
												echo $batch[csf('total_trims_weight')];  
												?>
											</td>
											<td  align="right" width="" title="<? echo $batch[csf('batch_weight')]; ?>">
												<? 
												echo number_format(($batch[csf('batch_qnty')]+$batch[csf('total_trims_weight')]),2); 
												?>
											</td>
											<? 
											$bttw+=$batch[csf('total_trims_weight')];
											$bw+=($batch[csf('batch_qnty')]+$batch[csf('total_trims_weight')]);
										}
										else 
										{
											?>
											<td align="right" width="70"></td>
											<td  align="right" width="">
												<? 
												echo number_format($batch[csf('batch_qnty')],2); 
												?>
											</td>
											<?
											$bw+=$batch[csf('batch_qnty')];
										}
										?>
									</tr>
									<? 
									$i++;
									$btq+=$batch[csf('batch_qnty')];
									$batch_chk[$batch[csf('id')]] = $batch[csf('id')];
								}

							} 
							?>
						</tbody>
					</table>
					<table class="rpt_table" width="1580" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<tr>
								<th width="30">&nbsp;</th>
								<th width="75">&nbsp;</th>
								<th width="60">&nbsp;</th>
								<th width="40">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="120">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="80">&nbsp;</th>

								<th width="70">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="150">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="60">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="70" id="value_batch_qnty" style="text-align: right"><? echo number_format($btq,2); ?></th>
								<th width="70" id="value_total_trims_weight" style="text-align: right"><? echo number_format($bttw,2); ?></th>
								<th width="" id="value_batch_weight" style="text-align: right"><? echo number_format($bw,2); ?></th>
							</tr>
						</tfoot>
					</table>
				</div> <br/>
				<? 
				$html = ob_get_contents();
				ob_clean();
//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
				foreach (glob("*.xls") as $filename) {
//if( @filemtime($filename) < (time()-$seconds_old) )
					@unlink($filename);
				}
//---------end------------//
				$name=time();
				$filename=$user_name."_".$name.".xls";
				$create_new_doc = fopen($filename, 'w');	
				$is_created = fwrite($create_new_doc, $html);
				echo "$html**$filename**$report_type"; 
				exit();
			} 

			?>
		</div>
	</fieldset>
</div>
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'batch_report_for_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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