<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');

$ltb_btb=array(1=>'BTB',2=>'LTB');
//if (is_duplicate_field( "template_name", "lib_report_template", "template_name=$cbo_company_id  and module_id=$cbo_module_name and report_id=$cbo_report_name  and is_deleted=0" ) == 1)
//--------------------------------------------------------------------------------------------------------------------
if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	//echo "select format_id where template_name ='".$data."' and module_id=6 and report_id=95 and is_deleted=0 and status_active=1";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=95 and is_deleted=0 and status_active=1","format_id");
	//echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}
if($action=="check_color_id")
{	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
      function js_set_value(id)
      { //alert(id);
		  document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
      }
    </script>
    <input type="hidden" id="selected_id" name="selected_id" />
    <?
        $sql="select id, color_name from lib_color where is_deleted=0 and status_active=1 order by id";
        $arr=array(1=>$color_library);
        echo  create_list_view("list_view", "ID,Color Name", "50,200","300","300",0, $sql, "js_set_value", "id,color_name", "", 1, "0,0", $arr , "id,color_name", "",'setFilterGrid("list_view",-1);','0') ;
	exit();
}

if ($action=="load_drop_down_knitting_com") {
	//$data = explode("**", $data);
	if ($data[0] == 1) {
		echo create_drop_down("cbo_party_id", 70, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", "", "", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_party_id", 70, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "");
	} else {
		echo create_drop_down("cbo_party_id", 70, $blank_array, "", 1, "-- Select --", 0, "");
	}
	exit();
}

if($action=="batchnumbershow")
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
<? if($db_type==0) $field_grpby=" GROUP BY batch_no";
else if($db_type==2) $field_grpby="GROUP BY batch_no,id,batch_no,batch_for,booking_no,color_id,batch_weight";
$batch_type = str_replace("'","",$batch_type);
if ($batch_type==0 || $batch_type==1)
{
	$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and entry_form in(0) and is_deleted = 0   $field_grpby ";
} // and booking_no is not null
if ($batch_type==0 || $batch_type==2)
{

$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and entry_form in(36) and is_deleted = 0  $field_grpby ";
}
$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,170","620","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}
if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]);
			$("#hide_booking_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:740px;">
					<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="170">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:150px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
								</td>
								<td align="center">
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'dyeing_production_report_controller_v3', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0]!=0) $company="  a.company_id='$data[0]'";
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and s.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	//pro_batch_create_mst//wo_non_ord_samp_booking_mst
 	 $sql= "(select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,pro_batch_create_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0
	union all
	 select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a ,pro_batch_create_mst b where $company $buyer $booking_no $booking_date  and a.booking_no=b.booking_no and a.booking_type=4 and b.status_active=1 and b.is_deleted=0) order by id Desc
	";
	//echo "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_non_ord_samp_booking_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 ";
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit();
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";

	echo create_drop_down( "cbo_floor_id", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
  exit();
}

if($action=="machine_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	//print_r ($im_data);
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
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hid_machine_id').val( id );
			$('#hid_machine_name').val( name );
		}

		function hidden_field_reset()
		{
			$('#hid_machine_id').val('');
			$('#hid_machine_name').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
    </script>
</head>
<input type="hidden" name="hid_machine_id" id="hid_machine_id" />
<input type="hidden" name="hid_machine_name" id="hid_machine_name" />
<?
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=2 and a.company_id=$im_data[0] and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
	//echo  $sql;

	echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;

   exit();
}
if($action=="load_drop_down_buyer")
{
	//echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	//extract($_REQUEST);
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
}//cbo_buyer_name_td


if($action=="jobnumbershow")
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
$year_job = str_replace("'","",$year);
$batch_type = str_replace("'","",$batch_type);
if($db_type==0)
{
	$year_field_by="and YEAR(a.insert_date)";
	$year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	$field_grpby="GROUP BY a.job_no order by b.id desc";
}
else if($db_type==2)
{
$year_field_by=" and to_char(a.insert_date,'YYYY')";
$year_field="to_char(a.insert_date,'YYYY') as year";
$field_grpby=" GROUP BY a.job_no,a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num,a.insert_date,b.id order by b.id,a.job_no_prefix_num  desc ";
}

if(trim($year)!=0) $year_cond=" $year_field_by=$year_job"; else $year_cond="";
//echo $year_job;
//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
if(trim($cbo_buyer_name)==0) $buyer_name_cond=""; else $buyer_name_cond=" and a.buyer_name=$cbo_buyer_name";
if(trim($cbo_buyer_name)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$cbo_buyer_name";
if ($batch_type==0 || $batch_type==1)
{
$sql="select a.id,a.buyer_name,a.style_ref_no,a.gmts_item_id,b.po_number,a.job_no_prefix_num as job_prefix,$year_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company_id  $buyer_name_cond $year_cond and a.is_deleted = 0 $field_grpby";
}
else
{
$sql="select a.id,a.party_id as buyer_name,c.item_id as gmts_item_id,b.cust_style_ref as style_ref_no,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field from  subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id  $sub_buyer_name_cond $year_cond and a.is_deleted = 0 group by a.id,a.party_id,b.cust_style_ref,b.order_no ,a.job_no_prefix_num,a.insert_date,c.item_id";

}
//echo $sql;
$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
?>
<table width="580" border="1" rules="all" class="rpt_table">
	<thead>
         <tr><th colspan="7"><? if($batch_type==0 || $batch_type==1)
		 { echo "Self Batch Order";} else if($batch_type==2) { echo "SubCon Batch Order";}?>  </th></tr>

        <tr>
            <th width="35">SL</th>
            <th width="100">Po Number</th>
            <th width="100">Job no</th>
            <th width="50">Year</th>
            <th width="80">Buyer</th>
            <th width="100">Style</th>
            <th>Item Name</th>
        </tr>
   </thead>
</table>
<div style="max-height:300px; overflow-y:scroll; width:600px;">
<table id="table_body2" width="580" border="1" rules="all" class="rpt_table">
 <? $rows=sql_select($sql);
$i=1;
 foreach($rows as $data)
 {  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
  ?>
	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('job_prefix')]; ?>')" style="cursor:pointer;">
		<td width="35"><? echo $i; ?></td>
		<td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
		<td width="100"><p><? echo $data[csf('job_prefix')]; ?></p></td>
        <td width="50"><p><? echo $data[csf('year')]; ?></p></td>
		<td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
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
	exit();
}//JobNumberShow

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
	$year_job = str_replace("'","",$year);
	if($db_type==0)
	{
		$year_field_by=" and YEAR(b.insert_date)";
		$year_field="SUBSTRING_INDEX(b.insert_date, '-', 1) as year ";
	}
	else if($db_type==2)
	{
	$year_field_by=" and to_char(b.insert_date,'YYYY')";
	$year_field="to_char(b.insert_date,'YYYY') as year";
	}
	if ($company_name==0) $company=""; else $company=" and b.company_name=$company_name";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	//echo $buyer;die;
	if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	if ($batch_type==0 || $batch_type==1)
	{
	$sql = "select distinct a.id,b.job_no,a.po_number,b.company_name,b.buyer_name,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company $buyername $year_cond order by a.id asc";
	}
	else
	{
	$sql="select distinct a.id,b.job_no_mst as job_no ,a.party_id as buyer_name,a.company_id as company_name ,b.order_no as po_number,a.job_no_prefix_num as job_prefix,$year_field from  subcon_ord_mst a , subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_name  $sub_buyer_name_cond $year_cond and a.is_deleted =0 group by a.id,a.party_id,b.job_no_mst,b.order_no ,a.job_no_prefix_num,a.company_id,b.insert_date";
	}
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="490" border="1" rules="all" class="rpt_table">
		<thead>
		 <tr><th colspan="5"><? if($batch_type==0 || $batch_type==1) echo "Self Batch Order"; else echo "SubCon Batch Order";?>  </th></tr>
			<tr>
			<th width="30">SL</th>
			<th width="80">Order Number</th>
			<th width="50">Job no</th>
			<th width="80">Buyer</th>
			<th width="40">Year</th>
			</tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="490" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
	 foreach($rows as $data)
	 {
		  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	  ?>
		<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('po_number')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
			<td width="80"><p><? echo $data[csf('po_number')]; ?></p></td>
			<td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
			<td width="40" align="center"><? echo $data[csf('year')]; ?></p></td>
		</tr>
		<? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if($action=="generate_report") //report4
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name";
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	
	if($db_type==0) $field_concat2="machine_no as machine_no";
	else if($db_type==2) $field_concat2="machine_no as machine_no";
	// machine_no || '-' || brand as machine_name
	$cbo_prod_type = str_replace("'","",$cbo_prod_type);
	$cbo_party_id = str_replace("'","",$cbo_party_id);
	$report_type = str_replace("'","",$operation);

	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_name);

	$buyer = str_replace("'","",$cbo_buyer_name);
	$date_search_type = str_replace("'","",$search_type);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);
	
	$hide_booking_id = str_replace("'","",$txt_hide_booking_id);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	//echo $company;die;
	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_result_name = str_replace("'","",$cbo_result_name);
	$year = str_replace("'","",$cbo_year);
	
	//echo $cbo_type;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	if ($txt_booking_no!='') $booking_no_cond="  and a.booking_no LIKE '%$txt_booking_no%'"; else $booking_no_cond="";
	if ($hide_booking_id!='') $booking_no_cond.="  and a.booking_no_id in($hide_booking_id) "; else $booking_no_cond.="";

	if ($cbo_prod_type>0 && $cbo_party_id>0) $cbo_prod_type_cond="and f.service_company in($cbo_party_id) "; else $cbo_prod_type_cond="";
	if ($cbo_prod_type>0) $cbo_prod_source_cond="and f.service_source in($cbo_prod_type) "; else $cbo_prod_source_cond="";

	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result='".$cbo_result_name."' ";


	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='".$buyer."' ";
	if ($buyer==0) $buyerdata2=""; else $buyerdata2="  and h.buyer_id='".$buyer."' ";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";

	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";
	if ($working_company==0) $workingCompany_name_cond2=""; else $workingCompany_name_cond2="  and a.working_company_id='".$working_company."' ";
	if ($company==0) $companyCond=""; else $companyCond="  and a.company_id=$company";


	//$buyerdata=($buyer)?' and d.buyer_name='.$buyer : '';
	//$batch_num=($batch_no)?" and a.batch_no='".$batch_no."'" : '';

	//echo $cbo_batch_type;
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
	{
		if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
	
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}

	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num='".$job_number_id."' "; else $sub_job_cond="";
	}
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3)
	{
	if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
	
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}

	//echo $order_no;die;
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	//echo date("Y-n-j", strtotime("first day of previous month"));
	//echo date("Y-n-j", strtotime("last day of previous month"));
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{

			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-m',strtotime($date_to));
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			//$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
			$prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
			if($date_search_type==1)
			{
				$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
			}
		}
		elseif($db_type==2)
		{

			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dateFrom= explode("-",$date_from);
			//echo $dateto[1];
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$prod_date_to=change_date_format($today_date,'','',1);
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
			//$last_day= date("t", strtotime($date_to));
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-M',strtotime($date_to));
		    $prod_last_day=change_date_format($second_month_ldate,'','',1);
			//$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			if($date_search_type==1)
			{
				$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
			}
		}
	}
	if($date_search_type==1)
		{
			$date_type_msg="Dyeing Date";
		}
		else
		{
			$date_type_msg="Insert Date";
		}

		//print_r($yarn_lot_arr);
	$load_hr=array();
	$load_min=array();
	$load_date=array();
	$water_flow_arr=array();$load_hour_meter_arr=array();
	if ($working_company==0) $workingCompany_name_cond1=""; else $workingCompany_name_cond1="  and service_company='".$working_company."' ";
	if ($company==0) $companyCond1=""; else $companyCond1="  and company_id=$company ";
	$load_time_data=sql_select("select batch_id,water_flow_meter,batch_no,load_unload_id,process_end_date,end_hours,hour_load_meter,end_minutes from pro_fab_subprocess where load_unload_id=1 and entry_form=35 $companyCond1 $workingCompany_name_cond1 $unload_batch_cond and status_active=1  and is_deleted=0 ");
	foreach($load_time_data as $row_time)// for Loading time
	{
		$load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
		$load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
		$load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
		$water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
		$load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
	}
	$subcon_load_hr=array();
	$subcon_load_min=array();
	$subcon_load_date=array();$subcon_load_hour_meter_arr=array();
	$subcon_water_flow_arr=array();
	$subcon_load_time_data=sql_select("select batch_id,water_flow_meter,batch_no,load_unload_id,process_end_date,end_hours,hour_load_meter,end_minutes from pro_fab_subprocess where load_unload_id=1 and entry_form=38 $companyCond1 $workingCompany_name_cond1  and status_active=1  and is_deleted=0 ");
	foreach($subcon_load_time_data as $row_time)// for Loading time
	{
		$subcon_load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
		$subcon_load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
		$subcon_load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
		$subcon_water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
		$subcon_load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
	}
	$subcon_unload_hr=array();
	$subcon_unload_min=array();
	$subcon_unload_date=array();
	$subcon_unload_time_data=sql_select("select f.batch_id,f.batch_no,f.load_unload_id,f.production_date,f.end_hours,f.end_minutes from pro_fab_subprocess f  where f.load_unload_id=2 and f.entry_form=38 $companyCond1 $workingCompany_name_cond1 and f.status_active=1  and f.is_deleted=0 $cbo_prod_type_cond $result_name_cond $shift_name_cond machine_cond $floor_id_cond");
	foreach($subcon_load_time_data as $row_time)// for Loading time
	{
	$subcon_unload_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
	$subcon_unload_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
	$subcon_unload_time_data[$row_time[csf('batch_id')]]=$row_time[csf('production_date')];
	}
	//var_dump($load_hr);

	$m_capacity=array();
	$unload_min=array();
	//$machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name order by seq_no ",'id','machine_name');
	$machine_capacity_data=sql_select("select id,prod_capacity as m_capacity,$field_concat,$field_concat2  from lib_machine_name where status_active=1  and is_deleted=0  order by seq_no ");

	$color_id = return_field_value("distinct(a.id) as id", "lib_color a ", "a.color_name='$color'", "id");
		//echo $color_id;
		if($color_id!='') $color=$color_id;else $color="";
		if ($color=="") $color_name=""; else $color_name="  and a.color_id=$color";
	foreach($machine_capacity_data as $capacity)// for Un-Loading time machine_no
	{
		$m_capacity[$capacity[csf('id')]]=$capacity[csf('m_capacity')];
		$machine_arr[$capacity[csf('id')]]=$capacity[csf('machine_name')];
		$machine_no_arr[$capacity[csf('id')]]=$capacity[csf('machine_no')];
	}//f.load_unload_id,f.process_end_date
	$sql_batch_dyeing=sql_select("select a.booking_no,a.batch_against,f.load_unload_id,f.process_end_date,f.batch_id,f.shift_name,f.fabric_type,t.prod_id,t.production_qty from  pro_batch_create_mst a ,pro_fab_subprocess f,lib_machine_name g,pro_fab_subprocess_dtls t where a.id=f.batch_id and f.id=t.mst_id and g.id=f.machine_id  and f.entry_form=35 and  a.batch_against in(1,2,3) and t.entry_page=35 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  and a.status_active=1 $unload_batch_cond2 $prod_date_upto $cbo_prod_type_cond $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $companyCond $workingCompany_name_cond2");
	  
	
	
	$tot_row=1;
	foreach($sql_batch_dyeing as $row_batch)
	{
		if($tot_row!=1) $batch_id.=",";
		$batch_id.=$row_batch[csf('batch_id')];
		$fabric_batch_arr[$row_batch[csf('fabric_type')]]=$row_batch[csf('fabric_type')];
		$fabric_batch_id_arr[$row_batch[csf('load_unload_id')]][$row_batch[csf('process_end_date')]].=$row_batch[csf('batch_id')].',';
		
		$shift_fabric_batch_arr[$row_batch[csf('fabric_type')]][$row_batch[csf('shift_name')]]+=$row_batch[csf('production_qty')];
		
		$shift_wise_prod_arr[$row_batch[csf('batch_id')]][$row_batch[csf('fabric_type')]][$row_batch[csf('prod_id')]]['prod_qty']+=$row_batch[csf('production_qty')];
		$batch_wise_prod_qty_arr[$row_batch[csf('batch_id')]][$row_batch[csf('fabric_type')]]['prod_qty']+=$row_batch[csf('production_qty')];
		$booking_noArr=explode("-",$row_batch[csf('booking_no')]);
		if($booking_noArr[1]=='Fb' || $booking_noArr[1]=='FB')
		{
			if($row_batch[csf('batch_against')]==2)
			{
			$batch_wise_re_prod_qty_arr2[$row_batch[csf('batch_id')]]['prod_qty']+=$row_batch[csf('production_qty')];
			}
			else //batch_wise_prod_qty_arr2
			{
			$batch_wise_prod_qty_arr2[$row_batch[csf('batch_id')]]['prod_qty']+=$row_batch[csf('production_qty')];
			}
		}
		
		$booking_noSampArr=explode("-",$row_batch[csf('booking_no')]);
		if($booking_noSampArr[1]=='SM' || $booking_noSampArr[1]=='SMN')
		{
			$samp_fabric_batch_id_arr[$row_batch[csf('load_unload_id')]][$row_batch[csf('process_end_date')]].=$row_batch[csf('batch_id')].',';
			if($row_batch[csf('batch_against')]==2)
			{
			$samp_batch_wise_re_prod_qty_arr2[$row_batch[csf('batch_id')]]['prod_qty']+=$row_batch[csf('production_qty')];
			}
			else //batch_wise_prod_qty_arr2
			{
			$samp_batch_wise_prod_qty_arr2[$row_batch[csf('batch_id')]]['prod_qty']+=$row_batch[csf('production_qty')];
			}
		}
		
		
		$shift_wise_arr[$row_batch[csf('batch_id')]][$row_batch[csf('fabric_type')]][$row_batch[csf('shift_name')]]['prod_qty']+=$row_batch[csf('production_qty')];
		if($row_batch[csf('shift_name')]==0)
		{
		$fabric_batch_arr_withOutshift[$row_batch[csf('batch_id')]][$row_batch[csf('prod_id')]]['prod_qty']+=$row_batch[csf('production_qty')];
		$fabric_batch_wise_arr_withOutshift[$row_batch[csf('batch_id')]]['prod_qty']+=$row_batch[csf('production_qty')];
		}
		
		$tot_row++;
	}//echo $batch_id;die;
	//print_r($fabric_batch_arr_withOutshift);
		unset($sql_batch_id);
		$batchIds=chop($batch_id,','); $batchIds_cond="";
		if($db_type==2 && count($tot_row)>990)
		{
			$batchIds_cond=" and (";
			$batchIdsArr=array_chunk(explode(",",$batchIds),990);
			foreach($batchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$batchIds_cond.=" a.id not in($ids) or ";
			}
			$batchIds_cond=chop($batchIds_cond,'or ');
			$batchIds_cond.=")";
		}
		else
		{
			$batchIds_cond=" and a.id not in($batchIds)";
		}
		//echo $batchIds_cond;
	$sub_sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form=38 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0 $cbo_prod_type_cond $result_name_cond $shift_name_cond machine_cond $floor_id_cond");
	$k=1;
	foreach($sub_sql_batch_id as $row_batch)
	{
		if($k!=1) $sub_batch_id.=",";
		$sub_batch_id.=$row_batch[csf('batch_id')];

		$k++;
	}
	if($batch_id=="") $batch_id=0;
	if($sub_batch_id=="") $sub_batch_id=0;
	$group_by=str_replace("'",'',$cbo_group_by);
	if($group_by==1)
	{
		$order_by="order by f.floor_id";
		$order_by2="order by floor_id";
	}
	else if($group_by==2)
	{
		$order_by="order by f.shift_name";
		$order_b2y="order by shift_name";
	}
	else if($group_by==3)
	{
		$order_by="order by g.seq_no,f.process_end_date";
		$order_by2="order by seq_no,process_end_date";
	}
	else
	{
		$order_by="order by f.process_end_date,f.machine_id";
		$order_by2="order by process_end_date,end_hours,machine_id";
	}

		if($db_type==2)
		{
		// $grp_con="LISTAGG(CAST(c.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number,LISTAGG(CAST(b.po_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id,LISTAGG(CAST(c.grouping AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS grouping,LISTAGG(CAST(c.file_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS file_no";
		  $grp_sub_con="LISTAGG(CAST(c.order_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number,LISTAGG(CAST(b.po_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id";
		}
		else if($db_type==0)
		{
			// $grp_con="group_concat(distinct c.po_number) AS po_number,group_concat(distinct b.po_id) AS po_id,group_concat(distinct c.grouping) AS grouping,group_concat(distinct c.file_no) AS file_no";
			  $grp_sub_con="group_concat(distinct c.order_no) AS po_number,group_concat(distinct b.po_id) AS po_id";
		}
		if($cbo_type==2 || $report_type==3)//   For Order Wise Dyeing Production
		{
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
			{

			    $sql="(select a.working_company_id,a.company_id,a.batch_against,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type,d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result, f.water_flow_meter, a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a where f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $cbo_prod_source_cond  $booking_no_cond $cbo_prod_type_cond )
			 union 
			 (
			 	select a.working_company_id,a.company_id,a.batch_against,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, (b.batch_qnty) AS batch_qnty,b.item_description, b.prod_id, b.width_dia_type,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
    f.end_hours, f.floor_id, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,f.water_flow_meter,a.booking_no,a.booking_without_order,g.seq_no from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no  and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com $companyCond  $booking_no_cond $workingCompany_name_cond2 $batch_num  $color_name  $buyerdata2 $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond 
			 ) $order_by2";

			}
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
			{
				//echo $group_by.'DD';
				 $sql_subcon="select a.batch_no,a.id,a.batch_against, a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, SUM(distinct b.batch_qnty) AS sub_batch_qnty, b.item_description, b.prod_id, b.width_dia_type,$grp_sub_con, d.subcon_job as job_no_prefix_num, d.party_id as buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,f.remarks,g.seq_no from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  a.batch_against in(1,2) $companyCond  $booking_no_cond and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2   and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $dates_com $sub_job_cond $batch_num  $sub_buyer_cond $suborder_no  $result_name_cond $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $cbo_prod_type_cond $cbo_prod_source_cond
				GROUP BY a.batch_no, a.id,a.batch_weight,a.batch_against, a.color_id, a.extention_no,a.total_trims_weight, b.item_description,b.prod_id, b.width_dia_type, d.subcon_job, d.party_id, f.shift_name, f.production_date,g.seq_no,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,f.result,f.remarks $order_by";

				//echo $sql_subcon;
			}

			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) // Sample Production
			{
				
				$sql_sam="(select a.booking_without_order,a.booking_no,a.batch_no,a.batch_against, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, (b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type,d.buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, fabric_type,f.result,f.remarks,g.seq_no from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where a.batch_against in(3,2)  $companyCond $booking_no_cond $workingCompany_name_cond2 and f.batch_id=a.id $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $cbo_prod_type_cond $cbo_prod_source_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.batch_id=b.mst_id and  b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0
			 )
			 union all
			 (
			 select a.booking_without_order,a.booking_no,a.batch_no,a.batch_against, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, (b.batch_qnty) AS batch_qnty, b.item_description,b.prod_id, b.width_dia_type,h.buyer_id as buyer_name,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.result,f.remarks,g.seq_no from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond $workingCompany_name_cond2 and f.batch_id=a.id $dates_com  $batch_num  $buyerdata2 $color_name  $result_name_cond $shift_name_cond $machine_cond $booking_no_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0
			)  $order_by2";
			}
		}
		//echo $sql;
		//$batchdata=sql_select($sql);
		//echo $sql_subcon; die;sql_subcon_ltb

	if($cbo_type==2 && $report_type!=3) // Dyeing Production
	{

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			//echo $sql;
			$b=0;
			$batchdata=sql_select($sql);$batch_aganist_arr=array(2,3);$batch_qty_check_array=array();
			$batch_ids='';$all_po_id='';$total_re_dyeing_qty=$total_shadematched_qty=0;
			foreach($batchdata as $row)
			{
				if($batch_ids=='') $batch_ids=$row[csf('id')]; else $batch_ids.=",".$row[csf('id')];
				if($all_po_id=='') $all_po_id=$row[csf('po_id')]; else $all_po_id.=",".$row[csf('po_id')];
				
				/*$batch_noData=$batch[('id')].$row[csf('prod_id')];
				if (!in_array($batch_noData,$batch_qty_check_array))
					{ $b++;


						 $batch_qty_check_array[]=$batch_noData;
						 // $batch_qty+=$row[csf('batch_qnty')];
					}
					else
					{
						// $batch_qty=0;
					}*/
					 // $batch_qty=$row[csf('batch_qnty')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['id']=$row[csf('id')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['working_company_id']=$row[csf('working_company_id')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['company_id']=$row[csf('company_id')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_weight']=$row[csf('batch_weight')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['booking_without_order']=$row[csf('booking_without_order')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['extention_no']=$row[csf('extention_no')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['width_dia_type']=$row[csf('width_dia_type')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['po_number']=$row[csf('po_number')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['po_id']=$row[csf('po_id')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['buyer_name']=$row[csf('buyer_name')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_against']=$row[csf('batch_against')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['shift_name']=$row[csf('shift_name')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['process_end_date']=$row[csf('process_end_date')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['production_date']=$row[csf('production_date')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['end_hours']=$row[csf('end_hours')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['hour_unload_meter']=$row[csf('hour_unload_meter')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['end_minutes']=$row[csf('end_minutes')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['machine_id']=$row[csf('machine_id')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['load_unload_id']=$row[csf('load_unload_id')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['fabric_type']=$row[csf('fabric_type')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['result']=$row[csf('result')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['seq_no']=$row[csf('seq_no')];
				
				$batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['shift_wise_qty']=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty'];//buyer_trims_self_arr
				
				$batch_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['without_shift_qty']+=$fabric_withOutshift;
				$batch_all_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['all_shift_qty']+=$shift_fabric_batch_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]];
				
				
				if($row[csf('batch_against')]==2)//batch_qnty
				{
					if($row[csf('result')]==1)
					{
						$total_shadematched_shelf_reprocess_qty+=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty'];
						$total_shadematched_shelf_trim_reprocess_qty+=$row[csf('total_trims_weight')];
					}
				}
						
					if($row[csf('result')]==1 && (!in_array($row[csf('batch_against')],$batch_aganist_arr)))
					{
						//if($row[csf('batch_against')]==3) echo "X";else echo "";
						$buyer_shadematched_arr[$row[csf('buyer_name')]]['batch_qty']+=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty']; 
						$buyer_shadematched_arr[$row[csf('buyer_name')]]['trim']+=$row[csf('total_trims_weight')];
						$total_shadematched_qty+=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty']+$row[csf('total_trims_weight')];
						if($row[csf('batch_against')]!=2)//batch_qnty
						{
						$total_shadematched_shelf_qty+=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty'];
						$total_shadematched_shelf_trim_qty+=$row[csf('total_trims_weight')];
						}
					}
				
			//	2-4-2019=== 2
			}
			$po_idsid=implode(",",(array_unique(explode(",",$all_po_id))));
			$batch_idss=implode(",",(array_unique(explode(",",$batch_ids))));

			$poIds=chop($po_idsid,','); $po_cond_for_in="";
			$po_ids=count(array_unique(explode(",",$po_idsid)));
			if($db_type==2 && $po_ids>999)
			{
				$po_cond_for_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
				$ids=implode(",",$ids);
				$po_cond_for_in.="b.po_breakdown_id in($ids) or";
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
			}
			else
			{
			$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
			}

			$batchIds=chop($batch_idss,','); $batch_cond_for_in="";
			$batch_ids=count(array_unique(explode(",",$batchIds)));
			if($db_type==2 && $batch_ids>999)
			{
				$batch_cond_for_in=" and (";
				$batchIdsArr=array_chunk(explode(",",$batchIds),999);
				foreach($batchIdsArr as $ids)
				{
				$ids=implode(",",$ids);
				$batch_cond_for_in.="a.id in($ids) or";
				}
				$batch_cond_for_in=chop($batch_cond_for_in,'or ');
				$batch_cond_for_in.=")";
			}
			else
			{
			$batch_cond_for_in=" and a.id in($batchIds)";
			}

		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			//echo $sql_subcon;
			$sql_subcon_data=sql_select($sql_subcon);
			$batchdata_subcn=sql_select($sql_sam);
			$subcn_batch_ids='';$total_subcon_summary=0;
			foreach($sql_subcon_data as $row)
			{
				if($subcn_batch_ids=='') $subcn_batch_ids=$row[csf('id')]; else $subcn_batch_ids.=",".$row[csf('id')];
				//if($sam_all_po_id=='') $sam_all_po_id=$row[csf('po_id')]; else $sam_all_po_id.=",".$row[csf('po_id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['id']=$row[csf('id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['working_company_id']=$row[csf('working_company_id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['company_id']=$row[csf('company_id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_weight']=$row[csf('batch_weight')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['booking_without_order']=$row[csf('booking_without_order')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['extention_no']=$row[csf('extention_no')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['sub_batch_qnty']+=$row[csf('sub_batch_qnty')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['width_dia_type']=$row[csf('width_dia_type')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['po_number']=$row[csf('po_number')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['po_id']=$row[csf('po_id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['buyer_name']=$row[csf('buyer_name')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['shift_name']=$row[csf('shift_name')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['process_end_date']=$row[csf('process_end_date')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['production_date']=$row[csf('production_date')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['end_hours']=$row[csf('end_hours')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['hour_unload_meter']=$row[csf('hour_unload_meter')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['end_minutes']=$row[csf('end_minutes')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['machine_id']=$row[csf('machine_id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['load_unload_id']=$row[csf('load_unload_id')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['fabric_type']=$row[csf('fabric_type')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['result']=$row[csf('result')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
				$subcon_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['seq_no']=$row[csf('seq_no')];
				
				/*For SunCon Summary*/
				
				$subcon_summary_arr[$row[csf('buyer_name')]]['sub_batch_qnty']+=$row[csf('sub_batch_qnty')];
				$total_subcon_summary+=$row[csf('sub_batch_qnty')];
				
					if($row[csf('shift_name')]==0)
					{
						$batch_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['without_shift_qty']+=$row[csf('sub_batch_qnty')];
					}
					else
					{
						$batch_all_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['all_shift_qty']+=$row[csf('sub_batch_qnty')];
					}
					
					if($row[csf('result')]==1)
					{
						if($row[csf('batch_against')]==2)//batch_qnty
						{
						
							$total_shadematched_subcon_re_qty+=$row[csf('sub_batch_qnty')];
							$total_shadematched_subcon_re_trim_qty+=$row[csf('total_trims_weight')];
						}
						if($row[csf('batch_against')]!=2)//batch_qnty
						{
							$total_shadematched_subcon_qty+=$row[csf('sub_batch_qnty')];
							$total_shadematched_subcon_trim_qty+=$row[csf('total_trims_weight')];
						}
						
					}
			}
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) //Batch Aganist - Sample
		{
			//echo $sql_sam;die;
			$batchdata_sam=sql_select($sql_sam);
			$sam_batch_ids='';$sam_all_po_id='';
			foreach($batchdata_sam as $row)
			{
				if($sam_batch_ids=='') $sam_batch_ids=$row[csf('id')]; else $sam_batch_ids.=",".$row[csf('id')];
				//if($sam_all_po_id=='') $sam_all_po_id=$row[csf('po_id')]; else $sam_all_po_id.=",".$row[csf('po_id')];
				
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['id']=$row[csf('id')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['working_company_id']=$row[csf('working_company_id')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['company_id']=$row[csf('company_id')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_weight']=$row[csf('batch_weight')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['booking_without_order']=$row[csf('booking_without_order')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['booking_no']=$row[csf('booking_no')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['extention_no']=$row[csf('extention_no')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['width_dia_type']=$row[csf('width_dia_type')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['po_number']=$row[csf('po_number')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['po_id']=$row[csf('po_id')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['buyer_name']=$row[csf('buyer_name')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['shift_name']=$row[csf('shift_name')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['process_end_date']=$row[csf('process_end_date')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['production_date']=$row[csf('production_date')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['end_hours']=$row[csf('end_hours')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['floor_id']=$row[csf('floor_id')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['hour_unload_meter']=$row[csf('hour_unload_meter')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['end_minutes']=$row[csf('end_minutes')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['machine_id']=$row[csf('machine_id')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['load_unload_id']=$row[csf('load_unload_id')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['fabric_type']=$row[csf('fabric_type')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['result']=$row[csf('result')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['seq_no']=$row[csf('seq_no')];
				$samp_batch_detail_arr[$row[csf('id')]][$row[csf('prod_id')]]['shift_wise_qty']+=$row[csf('batch_qnty')];
				$batch_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['without_shift_qty']+=$fabric_withOutshift;
				$batch_all_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['all_shift_qty']+=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty'];
				if($row[csf('result')]==1)
					{
						if($row[csf('batch_against')]==2)//batch_qnty
						{
						$total_shadematched_samp_re_qty+=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty'];
						$total_shadematched_samp_re_trim_qty+=$row[csf('total_trims_weight')];
						}
						if($row[csf('batch_against')]!=2)//batch_qnty
						{
						$total_shadematched_samp_trim_qty+=$row[csf('total_trims_weight')];
						}
					}
			}
			if(count($batchdata_sam)>0)
			{
			$sam_batchIds=chop($sam_batch_ids,','); $sam_batch_cond_for_in="";
			$sam_batch_ids=count(array_unique(explode(",",$sam_batchIds)));
				if($db_type==2 && $sam_batch_ids>999)
				{
					$sam_batch_cond_for_in=" and (";
					$sam_batchIdsArr=array_chunk(explode(",",$sam_batchIds),999);
					foreach($sam_batchIdsArr as $ids)
					{
					$ids=implode(",",$ids);
					$sam_batch_cond_for_in.="a.id in($ids) or";
					}
					$sam_batch_cond_for_in=chop($sam_batch_cond_for_in,'or ');
					$sam_batch_cond_for_in.=")";
				}
				else
				{
					$sam_batch_cond_for_in=" and a.id in($sam_batchIds)";
				}
			}
		}
		//print_r($sql_subcon_data);
		$yarn_lot_arr=array();
		if($db_type==0)
		{
			$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!=''  $po_cond_for_in  group by a.prod_id, b.po_breakdown_id");
		}
		else if($db_type==2)
		{
			$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id,  a.yarn_lot as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' $po_cond_for_in group by a.prod_id, b.po_breakdown_id,a.yarn_lot");
		}
		foreach($yarn_lot_data as $rows)
		{
			$yarn_lot_arr[$rows['prod_id']][$rows['po_breakdown_id']].=$rows[csf('yarn_lot')].',';
		}

	}
	ob_start();
	?>
	<div>
	<?
	
	if($cbo_type==2 && $report_type!=3) //  Dyeing Production
	{
		?>
		<div style="width:1350px;">
		 <?
		 if ($cbo_result_name==1 || $cbo_result_name==0)
		 {
		$m=1;$trims_buyer_check_array=array();$tot_shadeMatchQty=$total_without_fabric_shift_qty=$total_batch_trims_weight=0;
		 foreach($batch_detail_arr as $batch_id=>$prod_data)
		  {
			foreach($prod_data as $prod_id=>$row)
			{
				$batchid=$row[('id')]; //fabric_batch_arr
				if (!in_array($batchid,$trims_buyer_check_array))
					{ $m++;
						 $trims_buyer_check_array[]=$batchid;
						  $tot_buyer_trim_qty=$row[('total_trims_weight')];
					}
					else
					{
						 $tot_buyer_trim_qty=0;
					}
				$buyer_dyeing_prod_qty=$shift_wise_prod_arr[$batch_id][$row[('fabric_type')]][$prod_id]['prod_qty'];
				//echo $buyer_dyeing_prod_qty.'DDD';
				if($row[('batch_against')]==2)
				{
					$buyer_re_process_arr[$row['buyer_name']]['re_batch_qty']+=$buyer_dyeing_prod_qty;
					$total_re_dyeing_qty+=$buyer_dyeing_prod_qty;
				}
				// echo $total_re_dyeing_qty.'DDD';
				$fabric_type_batch_arr[$row['fabric_type']]['all_shift_qty']=$row['fabric_type'];
				if($row['shift_name']==0)
				{
					$without_shiftQty=$fabric_batch_arr_withOutshift[$batch_id][$prod_id]['prod_qty'];
					//echo $without_shiftQty.'<br>'.$row['shift_name'];
					$without_fabric_shift_batch_arr[$row['fabric_type']][$row['shift_name']]['out_shift_qty']+=$without_shiftQty+$tot_buyer_trim_qty;
					$total_without_fabric_shift_qty+=$without_shiftQty+$tot_buyer_trim_qty;
				}
				else
				{
					$fabric_shift_batch_arr[$row['fabric_type']][$row['shift_name']]['all_shift_qty']+=$buyer_dyeing_prod_qty+$tot_buyer_trim_qty;
				}
				if($row['result']==1 && (!in_array($row[('batch_against')],$batch_aganist_arr)))
				{
					$buyer_wise_shadematched_arr[$row['buyer_name']]['batch_qty']+=$buyer_dyeing_prod_qty;
					$buyer_wise_shadematched_arr[$row['buyer_name']]['trim']+=$tot_buyer_trim_qty;
					$tot_shadeMatchQty+=$buyer_dyeing_prod_qty;
					$total_batch_trims_weight+=$tot_buyer_trim_qty;
				}
			}
		}
		$n=1;$samp_trims_buyer_check_array=array();
		 foreach($samp_batch_detail_arr as $batch_id=>$prod_data)
		  {
			foreach($prod_data as $prod_id=>$row)
			{
				
				$samp_batchid=$row[('id')]; //fabric_batch_arr
				if (!in_array($samp_batchid,$samp_trims_buyer_check_array))
					{ $n++;
						 $samp_trims_buyer_check_array[]=$samp_batchid;
						  $samp_tot_buyer_trim_qty=$row[('total_trims_weight')];
					}
					else
					{
						 $samp_tot_buyer_trim_qty=0;
					}
				$samp_buyer_dyeing_prod_qty=$shift_wise_prod_arr[$batch_id][$row[('fabric_type')]][$prod_id]['prod_qty'];
				 $fabric_type_batch_arr[$row['fabric_type']]['all_shift_qty']=$row['fabric_type'];
				if($row['shift_name']==0)
				{
					$samp_without_shiftQty=$fabric_batch_arr_withOutshift[$batch_id][$prod_id]['prod_qty'];
					//echo $without_shiftQty.'<br>'.$row['shift_name'];
					$without_fabric_shift_batch_arr[$row['fabric_type']][$row['shift_name']]['out_shift_qty']+=$samp_without_shiftQty+$samp_tot_buyer_trim_qty;
					$total_without_fabric_shift_qty+=$samp_without_shiftQty+$samp_tot_buyer_trim_qty;
				}
				else
				{
					$fabric_shift_batch_arr[$row['fabric_type']][$row['shift_name']]['all_shift_qty']+=$samp_buyer_dyeing_prod_qty+$samp_tot_buyer_trim_qty;
				}
				 
				if($row['result']==1 && (!in_array($row[('batch_against')],$batch_aganist_arr)))
				{
					$total_shadematched_samp_qty+=$shift_wise_prod_arr[$batch_id][$row[('fabric_type')]][$prod_id]['prod_qty'];
					
				}
			}
			
		 }
		
			if($report_type==1)
			{
			 ?>
			 <div id="dyeing_prod_report_print">
			 <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
			<?
				echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
				?>
			 </div>
			
				<div align="left" >
				<input type="button" id="dyeing_prod_print_button" class="formbutton" value="Print" onClick=	
				"print_report_part_by_part('dyeing_prod_report_print','#dyeing_prod_print_button')"/>
				</div>
				<table cellpadding="0"  width="500" cellspacing="0" align="left" style="margin-left:20px;" id="scroll_body" >
				<tr>
				<td width="300" valign="top" align="left" colspan="5" >
					<table style="width:300px;border:1px solid #000;margin:5px" align="center"  class="rpt_table" rules="all" border="1" >
							   <thead>
								 <tr>
								   <th colspan="6">Summary Total(Shade Match)</th>
								 </tr>
								 <tr>
								   <th>Self Batch</th>
								   <th>Re-Process</th>
								   <th>Sample Batch</th>
								   <th>Trims Weight</th>
								   <th>SubCon Batch</th>
								   <th>Total</th>
								 </tr>
							   </thead>
							   <tbody>
								 <?
									$total_batch_qty_shelf=0;$total_batch_qty_sample=0;$total_batch_qty_subcon=0;
									//echo array_sum($buyer_re_process_arr);
									 if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									 $total_trim_qty=$tot_buyer_trim_qty;//$total_shadematched_shelf_trim_qty+$total_shadematched_subcon_trim_qty+$total_shadematched_samp_trim_qty;
									 $total_shadeMatch_qty=$total_shadematched_shelf_qty+$total_shadematched_samp_qty+$total_trim_qty+$total_shadematched_subcon_qty;
									  
									//echo $tot_reprocess_qty.'DD';
									$tot_reprocess_qty=$total_re_dyeing_qty;
								//$title_hd="Self Qty($total_shadematched_shelf_reprocess_qty),SubconQty($total_shadematched_subcon_re_qty),SampleQty($total_shadematched_samp_re_qty)";
									
									?>
								 <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
								   <td align="right"><? echo number_format($tot_shadeMatchQty,0); ?></td>
									<td align="right" title="<? echo $title_hd;?>"><? echo number_format($tot_reprocess_qty,0); ?></td>
								   <td align="right"><? echo number_format($total_shadematched_samp_qty,0); ?></td>
								   <td align="right"><? echo number_format($total_batch_trims_weight,0,'.',''); ?></td>
								   <td align="right"> <? echo number_format($total_shadematched_subcon_qty,0,'.',''); ?></td>
								   <td align="right"><? $grandtotal_shade_match_qty=$tot_shadeMatchQty+$tot_reprocess_qty+$total_shadematched_samp_qty+$total_batch_trims_weight+$total_shadematched_subcon_qty;
									echo number_format($grandtotal_shade_match_qty,0,'.',''); ?></td>
								   
								 </tr>
								
							   </tbody>
							   <tfoot>
								 <tr bgcolor="#CCCCCC" style="font-weight:bold;" >
								  <th align="left"><b><? echo number_format(($tot_shadeMatchQty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="left"><b><? echo number_format(($tot_reprocess_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="left"><b><? echo number_format(($total_shadematched_samp_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="left"><b><? echo number_format(($total_trim_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								<th align="left"><b><? echo number_format(($total_shadematched_subcon_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="left"><b><? echo number_format(($grandtotal_shade_match_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								 </tr>
							   </tfoot>
							 </table>
				</td>
				</tr>
				 <tr>
				 <td  valign="top">
					 <table cellpadding="0"  width="180" style="margin:5px;" cellspacing="0" align="left"  class="rpt_table" border="1" rules="all">
								<thead>
									<?
										$shift_count=count($shift_name);
										$colspan=2+$shift_count;
									?>
									<tr>
										<th colspan="2">Monthly Production Summary</th>
									</tr>
									<tr>
										<th width="160">Details </th>
										<th width="60">Prod. Qty. </th>
									</tr>
								</thead>
								<tbody>
								<?
								if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
								if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1 || str_replace("'",'',$cbo_batch_type)==3)
								{
								 $sql="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								 sum(CASE WHEN a.batch_against in(1) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								  sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
								 sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty
	
								  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id  $companyCond $workingCompany_name_cond2 and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto $booking_no_cond $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY f.load_unload_id,f.process_end_date  ";
								$sql_datas=sql_select($sql);
								}
								$unload_qty_arr=array();$plan_cycle_time=0;$reprocess_qty_arr=array();$tot_reprocess_qty=0;
								foreach($sql_datas as $row)
								{
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty+=$row[csf('re_batch_qnty')];
									$unload_qty_arr[$row[csf('load_unload_id')]]['count']+=$tot_row;
									$unload_qty_arr[$row[csf('load_unload_id')]]['process_end_date'].=$row[csf('process_end_date')].',';
								}
								unset($sql_datas);
	
								//print_r($unload_qty_arr);
								 $total_current_mon_qty1=$unload_qty_arr[2]['qty'];
								$total_count1=$unload_qty_arr[2]['count'];
								$total_reprocess_qty1=$tot_reprocess_qty;
	
								  $sql_sample_currMon="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								 sum(CASE WHEN a.batch_against in(3) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								  sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
								 sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty
								  from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  f.batch_id=a.id $companyCond  $workingCompany_name_cond2 and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1  and a.batch_against in(2,3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto  $batch_num $buyerdata2  $color_name $booking_no_cond $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY  f.load_unload_id,f.process_end_date  ";
								 $sql_result_samp_currMon=sql_select($sql_sample_currMon);
								 $tot_reprocess_qty2=0;
								  $process_enddate=rtrim($unload_qty_arr[2]['process_end_date'],',');
								 $process_enddates=array_unique(explode(",",$process_enddate));
								 foreach($sql_result_samp_currMon as $row)
								{
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr2[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty2+=$row[csf('re_batch_qnty')];
	
									  $isval=array_diff($row[csf('process_end_date')],$edate);
									  $tot_rows=0;
									 foreach($process_enddates as $edate)
									 {
	
										  $tot_rows=count($row[csf('process_end_date')]);
										  $isval=array_diff($row[csf('process_end_date')],$edate);
										 if($isval)
										 {
											$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_rows;
										 }
	
									}
	
									//$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_row;
								}
								unset($sql_result_samp_currMon);
								 $total_current_mon_qty=$unload_qty_arr2[2]['qty']+$total_current_mon_qty1;
								$total_count=$total_count1+$unload_qty_arr2[2]['count'];
								$total_reprocess_qty=$total_reprocess_qty1+$tot_reprocess_qty2;
	
									?>
									  <tr bgcolor="#E9F3FF" style="cursor:pointer;">
										<td>Current Month</td>
										<td align="right"><? echo number_format($total_current_mon_qty,0);?></td>
									  </tr>
									  <tr bgcolor="#D8D8D8" style="cursor:pointer;">
										  <td>Avg. Prod. Per Day</td>
										  <td align="right" title="<? echo 'Total Day: '.$total_count;?>"><? echo number_format($total_current_mon_qty/$total_count,0); ?></td>
									  </tr>
									   <tr bgcolor="#D8D8D8" style="cursor:pointer; ">
										  <td>ReProcess Current Month</td>
										  <td align="right"><?   echo number_format($total_reprocess_qty,0);//round( 1040.56789, 4, PHP_ROUND_HALF_EVEN) ?></td>
									  </tr>
									  <tr bgcolor="#D8D8D8" style="cursor:pointer;">
										  <td>Avg. Re-Process Per Day</td>
										  <td align="right" title="<? echo 'Total Day: '.$total_count;?>"><? echo number_format($total_reprocess_qty/$total_count,0); ?></td>
									  </tr>
								</tbody>
								
							</table>
							</td>
							<td  valign="top">
							<table cellpadding="0"  width="340" style="margin:5px;;border:1px solid #000;" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="<? echo $colspan+1; ?>">Production Summary</th>
									</tr>
									<tr>
										<th>Prod. Type </th>
										<?
											foreach ($shift_name as $key => $value) {
												?>
												<th><? echo $value .' Shift'; ?></th>
												<?
											}
											
										 ?>
										<th width="70">Without Shift Qty </th>
										<th>Total Prod. Qty. </th>
									</tr>
								</thead>
								<tbody>
								<?
									 $k=1;$g=1;	$total_batch_qty=$with_out_shift=0;
									// echo count($fabric_type_batch_arr).'ff';
										foreach($fabric_type_batch_arr as $typekey=>$val)
										{
											if ($k%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
											//echo $val['row_shift_qty'];
									  ?>
									   <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
										 
										  <td title="<? echo $typekey; ?>"><?php echo $fabric_type_for_dyeing[$typekey]; ?></td>
										   <?
											$tot_shift_wise=0;
											foreach ($shift_name as $key => $value) {
											$shift_wise_qty=$fabric_shift_batch_arr[$typekey][$key]['all_shift_qty'];
												
												?>
												<td><? echo number_format($shift_wise_qty,0); ?></td>
												<?
												$tot_shift_arr[$key]+=$shift_wise_qty;
												$tot_shift_wise+=$shift_wise_qty;
											}
											
											if($g==1)
											{
											$tot_colspan=count($fabric_type_batch_arr);
											$with_out_shift=$total_without_fabric_shift_qty;
											
										 ?>
										   <td align="right" width="70" rowspan="<? echo count($fabric_type_batch_arr);?>" title="<? echo $with_out_shift?>"><? echo number_format($with_out_shift,0); ?></td>
										   <?
										   }
										   
										   ?>
										   <td align="right" title=""><? echo number_format($tot_shift_wise,0); ?></td>
									  </tr>
								<? 		$total_batch_qty+=$tot_shift_wise+$with_out_shift;$tot_without_shift_qty=$with_out_shift;
										$k++;$g++;
										}
										 
								?>
								</tbody>
								<tfoot>
									<tr bgcolor="#CCCCCC" style="font-weight:bold;">
									<th  align="right">Total </th>
									 <?
									 $grd_total_shift_qty=0;
										foreach ($shift_name as $key => $value) {
												 $html_summary.="<th  align='right'>".number_format($tot_shift_arr[$key],0,'.','')." </th>
												 
												 ";
												?>
										<th  align="right"><? echo number_format($tot_shift_arr[$key],0,'.','');?> </th>
										<?
										$grd_total_shift_qty+=$tot_shift_arr[$key];
										}
										
										?>
										<th align="right"><b><? echo number_format($tot_without_shift_qty,0,'.','');?></b> </th>
										<th align="right"><b><? echo number_format($grd_total_shift_qty+$tot_without_shift_qty,0,'.','');?></b> </th>
										
									</tr>
								</tfoot>
							</table>
							
							</td>
							 <td  valign="top">
								 <table cellpadding="0"  style="margin:5px" width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="5">Self & Trims Batch (Shade Match)</th>
									</tr>
									<tr>
										<th>Buyer</th>
										<th>Batch Qty</th>
										<th>Trims Qty</th>
										<th>Total</th>
										<th>%</th>
									</tr>
								</thead>
								<tbody>
								<?
								
								$st=1;$total_batch_qty_shade=0;$tot_batch_per_shade=0;
									//echo array_sum($buyer_re_process_arr);
								foreach($buyer_wise_shadematched_arr as $key=>$val)
								{ 
									 if ($st%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									  <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
										  <td width="30"><? echo $buyer_arr[$key]; ?></td>
										  <td width="30"><? echo number_format($val["batch_qty"],0); ?></td>
										  <td width="30"><? echo number_format($val["trim"],0); ?></td>
										   <td width="30"><? $total_shade=$val["batch_qty"]+$val["trim"];echo number_format($total_shade,0); ?></td>
										  <td title="Total Shade<? echo $tot_shadeMatchQty;?>"><?  echo number_format(($total_shade/($tot_shadeMatchQty+$total_batch_trims_weight))*100,0).'%'; ?></td>
								  </tr>
								  <?
								  $st++;
								  $total_batch_qty_shade+=number_format($val["batch_qty"],0,".","");
								  $total_batch_qty_trim+=$val["trim"];
								 }
								  ?>
								</tbody>
								<tfoot>
									<tr bgcolor="#CCCCCC" style="font-weight:bold;">
									<th> Total</th>
									 <th width="30"><? echo number_format($total_batch_qty_shade,0); ?></th>
									  <th width="30"><? echo number_format($total_batch_qty_trim,0); ?></th>
									 <th width="30"><? echo number_format($total_batch_qty_trim+$total_batch_qty_shade,0); ?></th>
									 <th width="30"><? echo '100%'; ?></th>
									</tr>
								</tfoot>
							</table>
							</td>
							 <td  valign="top" >
							 <table style="width:140px;border:1px solid #000;margin:5px" align="center"  class="rpt_table" rules="all" border="1" >
							   <thead>
								 <tr>
								   <th colspan="3">Re Process Summary</th>
								 </tr>
								 <tr>
								   <th>SL</th>
								   <th>Buyer</th>
								   <th>Batch Qty.</th>
								 </tr>
							   </thead>
							   <tbody>
								 <?
									$k=1;$total_batch_qty_reproc=0;$tot_batch_per_re=0;
									//echo array_sum($buyer_re_process_arr);
								foreach($buyer_re_process_arr as $key=>$val)
								{
	
									 if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									//$trims_qty=$party_batch_arr[$key]['trims_weight'];
									 $trims_qty_sum=$val['trim']; //total_batch_qty_re
									
									?>
								 <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
								   <td><? echo $k; ?></td>
								   <td><? echo $buyer_arr[$key] ?></td>
								   <td align="right" title="Batch Qty"><? echo number_format($val['re_batch_qty'],0,'.',''); $total_batch_qty_reproc+=$val['re_batch_qty']; ?></td>
								   
								 </tr>
								 <?
								$tot_batch_per_re+=$batch_per;
								$k++;
	
								}
									  ?>
							   </tbody>
							   <tfoot>
								 <tr bgcolor="#CCCCCC" style="font-weight:bold;">
								   <th colspan="2" align="right">Total </th>
								   <th align="left"><b><? echo number_format($total_batch_qty_reproc,0,'.','');?></b></th>
								  
								 </tr>
							   </tfoot>
							 </table>
							 </td>
						 <?
								?>
					   <td valign="top"  >
							 <table cellpadding="0"  width="140" style="margin:5px" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="2">Sub Contract Summary</th>
									</tr>
									<tr>
										<th>Party</th>
										<th>Batch Qty</th>
									</tr>
								</thead>
								<tbody>
										<?
										$total_summary_prod_qty=0;$ss=1;
										foreach($subcon_summary_arr as $party_id=>$val)
										{
											if($ss%2==0) $subbgcolor="#E9F3FF"; else $subbgcolor="#FFFFFF";
										   ?>
										   <tr bgcolor="<? echo $subbgcolor; ?>"  style="cursor:pointer;">
											<td width="120"><? echo $buyer_arr[$party_id]; ?></td>
											<td width="100"  align="right"><? echo number_format($val['sub_batch_qnty'],0,'.',''); ?></td>
											
										   </tr>
										   <?
										   $total_summary_prod_qty+=$val['sub_batch_qnty'];
										$ss++;
										}
										?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold;">
											<td align="right">Total</td>
											<td width="100"  align="right"><? echo number_format($total_summary_prod_qty,0,'.',''); ?></td>
										   
										</tr>
							</tbody>
	
							</table>
					   </td>
					   <!-- END Total Dyeing Production Summary -->
				  </tr>
				 </table>
				 <?
				//$summary_content="ob_get_contents()";
				
			/*foreach (glob("$user_name*.xls") as $filename_summ)
			{
	
				if( @filemtime($filename_summ) < (time()-$seconds_old) )
				@unlink($filename_summ);
			}
			//---------end------------//
			$name=time();
			$filename_summ=$user_name."_".$name.'summary'.".xls";
			$create_new_doc_summ = fopen($filename_summ, 'w');
			$is_created_summ = fwrite($create_new_doc_summ,ob_get_contents());
			//$filename=$user_id."_".$name.".xls";
			echo "$total_data****$filename****$filename_summ****$report_type";*/
			foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
			{
				if( @filemtime($filename) < (time()-$seconds_old) )
				@unlink($filename);
			}
		//---------end------------//
		$name=time();
		$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
		//$filename_short="../../../ext_resource/tmp_report/".$user_name."_".$name."summ.xls";
		
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		echo "$total_data****$filename****$report_type";
		/*$html = ob_get_contents();
        ob_clean();
        $new_link = create_delete_report_file($html, 1, 1, "../../../");

        echo "$html********".$report_type;*/
			
			?>
			 </div>
			 <br />
			 <?
			 exit();
			 } //Summary End
		  else {
			 ?>
			 <div id="dyeing_prod_report_print">
			 <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
			<?
				echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
				
				
				?>
			 </div>
			
				<div align="left">
				<input type="hidden" id="dyeing_prod_print_button" class="formbutton" value="Print" onClick=	
				"print_report_part_by_part('dyeing_prod_report_print','#dyeing_prod_print_button')"/>
				</div>
				<table cellpadding="0"  width="820" cellspacing="0" align="left" style="margin-left:20px;" >
				<tr>
				<td width="450" valign="top" align="left" style="margin:5px" colspan="5" >
					<table style="width:450px;border:1px solid #000;" align="center"  class="rpt_table" rules="all" >
							   <thead>
								 <tr>
								   <th colspan="6">Summary Total(Shade Match)</th>
								 </tr>
								 <tr>
								   <th>Self Batch</th>
								   <th>Re-Process</th>
								   <th>Sample Batch</th>
								   <th>Trims Weight</th>
								   <th>SubCon Batch</th>
								   <th>Total</th>
								 </tr>
							   </thead>
							   <tbody>
								 <?
									$total_batch_qty_shelf=0;$total_batch_qty_sample=0;$total_batch_qty_subcon=0;
									//echo array_sum($buyer_re_process_arr);
									 if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									 $total_trim_qty=$tot_buyer_trim_qty;//$total_shadematched_shelf_trim_qty+$total_shadematched_subcon_trim_qty+$total_shadematched_samp_trim_qty;
									 $total_shadeMatch_qty=$total_shadematched_shelf_qty+$total_shadematched_samp_qty+$total_trim_qty+$total_shadematched_subcon_qty;
									  
									//echo $tot_reprocess_qty.'DD';
									$tot_reprocess_qty=$total_re_dyeing_qty;
								//$title_hd="Self Qty($total_shadematched_shelf_reprocess_qty),SubconQty($total_shadematched_subcon_re_qty),SampleQty($total_shadematched_samp_re_qty)";
									
									?>
								 <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
								   <td align="right"><? echo number_format($tot_shadeMatchQty,0); ?></td>
									<td align="right" title="<? echo $title_hd;?>"><? echo number_format($tot_reprocess_qty,0); ?>ffD</td>
								   <td align="right"><? echo number_format($total_shadematched_samp_qty,0); ?></td>
								   <td align="right"><? echo number_format($total_batch_trims_weight,0,'.',''); ?></td>
								   <td align="right"> <? echo number_format($total_shadematched_subcon_qty,0,'.',''); ?></td>
								   <td align="right"><? $grandtotal_shade_match_qty=$tot_shadeMatchQty+$tot_reprocess_qty+$total_shadematched_samp_qty+$total_batch_trims_weight+$total_shadematched_subcon_qty;
									echo number_format($grandtotal_shade_match_qty,0,'.',''); ?></td>
								   
								 </tr>
								 <?
								//$total_batch_qty_shelf+=$batch_per;
								
									  ?>
							   </tbody>
							   <tfoot>
								 <tr bgcolor="#CCCCCC" style="font-weight:bold;">
								  <th align="left"><b><? echo number_format(($tot_shadeMatchQty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="left"><b><? echo number_format(($tot_reprocess_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="left"><b><? echo number_format(($total_shadematched_samp_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="left"><b><? echo number_format(($total_trim_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								<th align="left"><b><? echo number_format(($total_shadematched_subcon_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="left"><b><? echo number_format(($grandtotal_shade_match_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								 </tr>
							   </tfoot>
							 </table>
							
				</td>
				</tr>
				 <tr>
				 <td  valign="top" width="230">
					 <table cellpadding="0"  width="230" style="margin:5px" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
								<thead>
									<?
										$shift_count=count($shift_name);
										$colspan=2+$shift_count;
									?>
									<tr>
										<th colspan="2">Monthly Production Summary</th>
									</tr>
									<tr>
										<th width="160">Details </th>
										<th width="60">Prod. Qty. </th>
									</tr>
								</thead>
								<tbody>
								<?
								if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
								if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1 || str_replace("'",'',$cbo_batch_type)==3)
								{
								 $sql="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								 sum(CASE WHEN a.batch_against in(1) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								  sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
								 sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty
	
								  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id  $companyCond $workingCompany_name_cond2 and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto $booking_no_cond $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY f.load_unload_id,f.process_end_date  ";
								$sql_datas=sql_select($sql);
								}
								$unload_qty_arr=array();$plan_cycle_time=0;$reprocess_qty_arr=array();$tot_reprocess_qty=0;
								foreach($sql_datas as $row)
								{
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty+=$row[csf('re_batch_qnty')];
									$unload_qty_arr[$row[csf('load_unload_id')]]['count']+=$tot_row;
									$unload_qty_arr[$row[csf('load_unload_id')]]['process_end_date'].=$row[csf('process_end_date')].',';
								}
								unset($sql_datas);
	
								//print_r($unload_qty_arr);
								 $total_current_mon_qty1=$unload_qty_arr[2]['qty'];
								$total_count1=$unload_qty_arr[2]['count'];
								$total_reprocess_qty1=$tot_reprocess_qty;
	
								  $sql_sample_currMon="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								 sum(CASE WHEN a.batch_against in(3) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								  sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS total_trims_weight,
								 sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty
								  from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  f.batch_id=a.id $companyCond  $workingCompany_name_cond2 and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1  and a.batch_against in(2,3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto  $batch_num $buyerdata2  $color_name $booking_no_cond $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY  f.load_unload_id,f.process_end_date  ";
								 $sql_result_samp_currMon=sql_select($sql_sample_currMon);
								 $tot_reprocess_qty2=0;
								  $process_enddate=rtrim($unload_qty_arr[2]['process_end_date'],',');
								 $process_enddates=array_unique(explode(",",$process_enddate));
								 foreach($sql_result_samp_currMon as $row)
								{
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr2[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('total_trims_weight')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty2+=$row[csf('re_batch_qnty')];
	
									  $isval=array_diff($row[csf('process_end_date')],$edate);
									  $tot_rows=0;
									 foreach($process_enddates as $edate)
									 {
	
										  $tot_rows=count($row[csf('process_end_date')]);
										  $isval=array_diff($row[csf('process_end_date')],$edate);
										 if($isval)
										 {
											$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_rows;
										 }
	
									}
	
									//$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_row;
								}
								unset($sql_result_samp_currMon);
								 $total_current_mon_qty=$unload_qty_arr2[2]['qty']+$total_current_mon_qty1;
								$total_count=$total_count1+$unload_qty_arr2[2]['count'];
								$total_reprocess_qty=$total_reprocess_qty1+$tot_reprocess_qty2;
	
									?>
									  <tr bgcolor="#E9F3FF" style="cursor:pointer;">
										<td>Current Month</td>
										<td align="right"><? echo number_format($total_current_mon_qty,0);?></td>
									  </tr>
									  <tr bgcolor="#D8D8D8" style="cursor:pointer;">
										  <td>Avg. Prod. Per Day</td>
										  <td align="right" title="<? echo 'Total Day: '.$total_count;?>"><? echo number_format($total_current_mon_qty/$total_count,0); ?></td>
									  </tr>
									   <tr bgcolor="#D8D8D8" style="cursor:pointer; ">
										  <td>ReProcess Current Month</td>
										  <td align="right"><?   echo number_format($total_reprocess_qty,0);//round( 1040.56789, 4, PHP_ROUND_HALF_EVEN) ?></td>
									  </tr>
									  <tr bgcolor="#D8D8D8" style="cursor:pointer;">
										  <td>Avg. Re-Process Per Day</td>
										  <td align="right" title="<? echo 'Total Day: '.$total_count;?>"><? echo number_format($total_reprocess_qty/$total_count,0); ?></td>
									  </tr>
								</tbody>
								<tfoot>
									<tr>
										<th align="right" colspan="2"> &nbsp;&nbsp;&nbsp;</th>
									</tr>
								</tfoot>
							</table>
							</td>
							<td  valign="top">
							<table cellpadding="0"  width="350" style="margin:5px" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="<? echo $colspan+1; ?>">Production Summary</th>
									</tr>
									<tr>
										<th>Prod. Type </th>
										<?
											foreach ($shift_name as $key => $value) {
												?>
												<th><? echo $value .' Shift'; ?></th>
												<?
											}
											
										 ?>
										<th width="70">Without Shift Qty </th>
										<th>Total Prod. Qty. </th>
									</tr>
								</thead>
								<tbody>
								<?
									 $k=1;$g=1;	$total_batch_qty=$with_out_shift=0;
									// echo count($fabric_type_batch_arr).'ff';
										foreach($fabric_type_batch_arr as $typekey=>$val)
										{
											if ($k%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
											//echo $val['row_shift_qty'];
									  ?>
									   <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
										 
										  <td title="<? echo $typekey; ?>"><?php echo $fabric_type_for_dyeing[$typekey]; ?></td>
										   <?
											$tot_shift_wise=0;
											foreach ($shift_name as $key => $value) {
											$shift_wise_qty=$fabric_shift_batch_arr[$typekey][$key]['all_shift_qty'];
												
												?>
												<td><? echo number_format($shift_wise_qty,0); ?></td>
												<?
												$tot_shift_arr[$key]+=$shift_wise_qty;
												$tot_shift_wise+=$shift_wise_qty;
											}
											
											if($g==1)
											{
											$tot_colspan=count($fabric_type_batch_arr);
											$with_out_shift=$total_without_fabric_shift_qty;
											
										 ?>
										   <td align="right" width="70" rowspan="<? echo count($fabric_type_batch_arr);?>" title="<? echo $with_out_shift?>"><? echo number_format($with_out_shift,0); ?></td>
										   <?
										   }
										   
										   ?>
										   <td align="right" title=""><? echo number_format($tot_shift_wise,0); ?></td>
									  </tr>
								<? 		$total_batch_qty+=$tot_shift_wise+$with_out_shift;$tot_without_shift_qty=$with_out_shift;
										$k++;$g++;
										}
										 
								?>
								</tbody>
								<tfoot>
									<tr bgcolor="#CCCCCC" style="font-weight:bold;">
									<th  align="right">Total </th>
									 <?
									 $grd_total_shift_qty=0;
										foreach ($shift_name as $key => $value) {
												 $html_summary.="<th  align='right'>".number_format($tot_shift_arr[$key],0,'.','')." </th>
												 
												 ";
												?>
										<th  align="right"><? echo number_format($tot_shift_arr[$key],0,'.','');?> </th>
										<?
										$grd_total_shift_qty+=$tot_shift_arr[$key];
										}
										
										?>
										<th align="right"><b><? echo number_format($tot_without_shift_qty,0,'.','');?></b> </th>
										<th align="right"><b><? echo number_format($grd_total_shift_qty+$tot_without_shift_qty,0,'.','');?></b> </th>
										
									</tr>
								</tfoot>
							</table>
							
							</td>
							 <td  valign="top">
								 <table cellpadding="0"  style="margin:5px" width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="5">Self & Trims Batch (Shade Match)</th>
									</tr>
									<tr>
										<th>Buyer</th>
										<th>Batch Qty</th>
										<th>Trims Qty</th>
										<th>Total</th>
										<th>%</th>
									</tr>
								</thead>
								<tbody>
								<?
								
								$st=1;$total_batch_qty_shade=0;$tot_batch_per_shade=0;
									//echo array_sum($buyer_re_process_arr);
								foreach($buyer_wise_shadematched_arr as $key=>$val)
								{ 
									 if ($st%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									  <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
										  <td width="30"><? echo $buyer_arr[$key]; ?></td>
										  <td width="30"><? echo number_format($val["batch_qty"],0); ?></td>
										  <td width="30"><? echo number_format($val["trim"],0); ?></td>
										   <td width="30"><? $total_shade=$val["batch_qty"]+$val["trim"];echo number_format($total_shade,0); ?></td>
										  <td title="Total Shade<? echo $tot_shadeMatchQty;?>"><?  echo number_format(($total_shade/($tot_shadeMatchQty+$total_batch_trims_weight))*100,0).'%'; ?></td>
								  </tr>
								  <?
								  $st++;
								  $total_batch_qty_shade+=number_format($val["batch_qty"],0,".","");
								  $total_batch_qty_trim+=$val["trim"];
								 }
								  ?>
								</tbody>
								<tfoot>
									<tr bgcolor="#CCCCCC" style="font-weight:bold;">
									<th> Total</th>
									 <th width="30"><? echo number_format($total_batch_qty_shade,0); ?></th>
									  <th width="30"><? echo number_format($total_batch_qty_trim,0); ?></th>
									 <th width="30"><? echo number_format($total_batch_qty_trim+$total_batch_qty_shade,0); ?></th>
									 <th width="30"><? echo '100%'; ?></th>
									</tr>
								</tfoot>
							</table>
							</td>
							 <td  valign="top" >
							 <table style="width:210px;border:1px solid #000;margin:5px" align="center"  class="rpt_table" rules="all" >
							   <thead>
								 <tr>
								   <th colspan="3">Re Process Summary</th>
								 </tr>
								 <tr>
								   <th>SL</th>
								   <th>Buyer</th>
								   <th>Batch Qty.</th>
								 </tr>
							   </thead>
							   <tbody>
								 <?
									$k=1;$total_batch_qty_reproc=0;$tot_batch_per_re=0;
									//echo array_sum($buyer_re_process_arr);
								foreach($buyer_re_process_arr as $key=>$val)
								{
	
									 if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									//$trims_qty=$party_batch_arr[$key]['trims_weight'];
									 $trims_qty_sum=$val['trim']; //total_batch_qty_re
									
									?>
								 <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
								   <td><? echo $k; ?></td>
								   <td><? echo $buyer_arr[$key] ?></td>
								   <td align="right" title="Batch Qty"><? echo number_format($val['re_batch_qty'],0,'.',''); $total_batch_qty_reproc+=$val['re_batch_qty']; ?></td>
								   
								 </tr>
								 <?
								$tot_batch_per_re+=$batch_per;
								$k++;
	
								}
									  ?>
							   </tbody>
							   <tfoot>
								 <tr bgcolor="#CCCCCC" style="font-weight:bold;">
								   <th colspan="2" align="right">Total </th>
								   <th align="left"><b><? echo number_format($total_batch_qty_reproc,0,'.','');?></b></th>
								  
								 </tr>
							   </tfoot>
							 </table>
							 </td>
						 <?
								?>
					   <td valign="top"  >
							 <table cellpadding="0"  width="210" style="margin:5px" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="2">Sub Contract Summary</th>
									</tr>
									<tr>
										<th>Party</th>
										<th>Batch Qty</th>
									</tr>
								</thead>
								<tbody>
										<?
										$total_summary_prod_qty=0;$ss=1;
										foreach($subcon_summary_arr as $party_id=>$val)
										{
											if($ss%2==0) $subbgcolor="#E9F3FF"; else $subbgcolor="#FFFFFF";
										   ?>
										   <tr bgcolor="<? echo $subbgcolor; ?>"  style="cursor:pointer;">
											<td width="120"><? echo $buyer_arr[$party_id]; ?></td>
											<td width="100"  align="right"><? echo number_format($val['sub_batch_qnty'],0,'.',''); ?></td>
											
										   </tr>
										   <?
										   $total_summary_prod_qty+=$val['sub_batch_qnty'];
										$ss++;
										}
										?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold;">
											<td align="right">Total</td>
											<td width="100"  align="right"><? echo number_format($total_summary_prod_qty,0,'.',''); ?></td>
										   
										</tr>
							</tbody>
	
							</table>
					   </td>
					   <!-- END Total Dyeing Production Summary -->
				  </tr>
				 </table>
				 <?
				
			?>
			 </div>
			 <br />
			 <?
			 } 
		}

		 if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		 {
			if (count($batch_detail_arr)>0)
			{
				$group_by=str_replace("'",'',$cbo_group_by);

			 ?>
			 <div align="left" style="float:left; clear:both;">
				
			 <table class="rpt_table" width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_1">
              <caption> <b>Self batch  </b></caption>
              <!--working hereeeeeeeeeeeee self batch -->
                <thead>
                    <tr>
                        <th width="30">SL</th>
                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                       <th width="100">Buyer</th>
                        <th width="100">F.Booking No.</th>
                        <th width="150">Fabrics Desc</th>
                        <th width="80">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Wgt.</th>
                        <th width="70">Water Flow</th>

                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
						<th width="50">Shift</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1540px; overflow-y:scroll; float:left; clear:both;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
                <?
				$tot_sum_trims_qnty=0;
                $i=1; $btq=0; $k=1;$z=1;$total_water_cons_load=0;$total_water_cons_unload=$grand_total_batch_qty=0;
                $batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();
              foreach($batch_detail_arr as $batch_id=>$prod_data)
			  {
				foreach($prod_data as $prod_id=>$batch)
				{
					if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
					if($group_by!=0)
					{
						if($group_by==1)
						{
							$group_value=$batch[('floor_id')];
							$group_name="Floor";
							$group_dtls_value=$floor_arr[$batch[('floor_id')]];
						}
						else if($group_by==2)
						{
							$group_value=$batch[('shift_name')];
							$group_name="Shift";
							$group_dtls_value=$shift_name[$batch[('shift_name')]];
						}
						else if($group_by==3)
						{
							$group_value=$batch[('machine_id')];
							$group_name="machine";
							$group_dtls_value=$machine_arr[$batch[('machine_id')]];
						}
						if (!in_array($group_value,$group_by_arr) )
						{
							if($k!=1)
							{
							?>
                                <tr class="tbl_bottom">
                                    <td width="30">&nbsp;</td>


                                    <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>


                                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total :</strong></td>
                                    <td width="70"><? //echo number_format($batch_qnty,2); ?></td>
                                    <td width="40"><? //echo number_format($batch_qnty_trims,2); ?></td>

									<td width="70"><? echo number_format($batch_qnty,0); ?></td>
                                    <td width="70"><? echo number_format($batch_qnty_trims,0); ?></td>


                                    <td width="75" colspan="9">&nbsp;</td>

                                </tr>
								<?
								unset($batch_qnty);unset($batch_qnty_trims);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="27"><b style="margin-left:0px"><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
							</tr>
							<?
							$group_by_arr[]=$group_value;
							$k++;
						}
					}


					$order_id=$batch[('po_id')];

					$batch_weight=$batch[('batch_weight')];
					$water_cons_unload=$batch[('water_flow_meter')];
					$water_cons_load=$water_flow_arr[$batch_id];
					$load_hour_meter=$load_hour_meter_arr[$batch_id];
					//echo $water_cons_load.'=='.$water_cons_unload;
					//$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

					$desc=explode(",",$batch[('item_description')]);
					
					$dyeing_prod_qty=$shift_wise_prod_arr[$batch_id][$batch[('fabric_type')]][$prod_id]['prod_qty'];
					
					$batch_no=$batch[('id')];
				if (!in_array($batch_no,$trims_check_array))
					{ $z++;


						 $trims_check_array[]=$batch_no;
						  $tot_trim_qty=$batch[('total_trims_weight')];
					}
					else
					{
						 $tot_trim_qty=0;
					}
					

					?>
					<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
                        <td width="30"><? echo $i; ?></td>


                         <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$batch[('machine_id')]]; ?></div></td>
                        <?
						 }
						 if($group_by==2 || $group_by==0){ ?>

                        <? } if($group_by==1 || $group_by==0){ ?>

                        <? } ?>
                       
                       
                        <td width="100"><p><? echo $buyer_arr[$batch[('buyer_name')]]; ?></p></td>
						<td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $batch[('booking_no')]; ?></div></td>

                        <td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $batch[('item_description')]; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $fabric_typee[$batch[('width_dia_type')]]; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[('color_id')]]; ?></div></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $batch[('batch_no')]; ?></div></td>
                        <td width="40"><p><? echo $batch[('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo number_format($dyeing_prod_qty,2,".","");  ?></td>
                       	<td align="right" width="70"><? echo number_format($tot_trim_qty,0);  ?></td>
                       	<td align="right" width="70"><? echo $water_cons_load;  ?></td>

                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $load_t=$load_hr[$batch_id].':'.$load_min[$batch_id]; echo  ($load_date[$batch_id] == '0000-00-00' || $load_date[$batch_id] == '' ? '' : change_date_format($load_date[$batch_id])).' <br> '.$load_t;
						//echo $batch[csf('id')];
                        ?></div></td>
                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $hr=strtotime($unload_date,$load_t); $min=($batch[('end_minutes')])-($load_min[$batch_id]);
						echo  ($batch[('process_end_date')] == '0000-00-00' || $batch[('process_end_date')] == '' ? '' : change_date_format($batch[('process_end_date')])).'<br>'.$unload_time=$batch[('end_hours')].':'.$batch[('end_minutes')];
						$unloaded_date=change_date_format($batch[('process_end_date')]);
						 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></div></td>
                        <td align="center" width="60">
							<div style="width:60px; word-wrap:break-word;"><?
                            $new_date_time_unload=($unloaded_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($load_date[$batch_id].' '.$load_t.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
                            echo floor($total_time/60).":".$total_time%60;
                            ?>
							</div>
                        </td>
                        <td align="center" width="100" title="<? echo $batch[('fabric_type')];?>"><div style="width:100px; word-wrap:break-word;"><? echo $fabric_type_for_dyeing[$batch[('fabric_type')]]; ?> </div> </td>
						<td align="center" width="50"><p><? echo $shift_name[$batch[('shift_name')]]; ?></p> </td>
                        <td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $dyeing_result[$batch[('result')]]; ?></div> </td>
                         <td align="center"><p><? echo $batch[('remarks')]; ?></p> </td>
					</tr>
					<?
					
					$i++;
					$batch_qnty+=$dyeing_prod_qty;
					$batch_qnty_trims+=$tot_trim_qty;
					$total_water_cons_load+=$water_cons_load;
					$total_water_cons_unload+=$water_cons_unload;
					$tot_trims_qnty+=$tot_trim_qty;
					$trims_summary+=$tot_trim_qty;
					$grand_total_batch_qty+=$dyeing_prod_qty;
					} //batchdata froeach
				}
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    
                    <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>
                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total: </strong></td>
                    <td width="70"><? //echo number_format($batch_qnty,2); ?></td>
                    <td width="40"><? //echo number_format($batch_qnty_trims,2); ?></td>
					 <td width="70"><? echo number_format($batch_qnty,0); ?></td>
                    <td width="70"><? echo number_format($batch_qnty_trims,0); ?></td>
                    <td width="75" colspan="9">&nbsp;</td>
                </tr>
			<? } ?>
			</tbody>
			<table  class="rpt_table" width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer">
				<tfoot>
                <tr>
					<td width="30">&nbsp;</td>
					 <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                    <? }  ?>
					<td width="100"></td>
					<td width="100"></td>
					<td width="150"></td>
					<td width="80"></td>
					<td width="80"></td>
					<td width="90" align="right">Grand</td>
					<td width="40" align="right" title="Shade Match=<? echo $tot_shadeMatchQty;?>">Total:</td>
					<td width="70" id="grand_total_td_batch_qty" align="right"><? echo number_format($grand_total_batch_qty,2); ?></td>
					<td width="70"  id="value_total_batch_trim_qty" align="right"><? echo number_format($tot_trims_qnty,0); ?></td>
					<td width="70"></td>
					<td width="75"><? //echo $grand_total_batch_qty;?></td>
					<td width="75"></td>
					<td width="60"></td>
					<td width="100"></td>
					<td width="50"></td>
					<td width="100"></td>
					<td width=""></td>

                </tr>
				</tfoot>
                
			</table>
            

		</div>
			
			
			
			</div>
			<br/>
		<? }
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			if(count($subcon_batch_detail_arr)>0)
			{
			$group_by=str_replace("'",'',$cbo_group_by);
		?>
		 	<div align="left" style="float:left; clear:both;">
			 <table class="rpt_table" width="1530" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_2">
              <caption> <b>SubCon batch  </b></caption>
              <!--working hereeeeeeeeeeeee SubCon batch -->
                <thead>
                    <tr>
                        <th width="30">SL</th>
                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                       <th width="100">Buyer</th>
                        <th width="100">F.Booking No.</th>
                        <th width="150">Fabrics Desc</th>
                        <th width="80">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Wgt.</th>
                        <th width="70">Water Flow</th>

                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
						<th width="50">Shift</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1550px; overflow-y:scroll; float:left; clear:both;" id="scroll_body_sub">
            <table class="rpt_table" id="table_body2" width="1530" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
                <?
				$tot_sum_trims_qnty=0;
                $i=1; $btq=0; $k=1;$z=1;$sub_total_water_cons_load=0;$sub_total_water_cons_unload=0;
                $sub_batch_chk_arr=array(); $sub_group_by_arr=array();$sub_tot_trims_qnty=0;$sub_trims_check_array=array();
              foreach($subcon_batch_detail_arr as $batch_id=>$prod_data)
			  {
				foreach($prod_data as $prod_id=>$sub_batch)
				{
					if ($i%2==0)  $bgcolor_sub_dyeing="#E9F3FF"; else $bgcolor_sub_dyeing="#FFFFFF";
					if($group_by!=0)
					{
						if($group_by==1)
						{
							$group_value=$sub_batch[('floor_id')];
							$group_name="Floor";
							$group_dtls_value=$floor_arr[$sub_batch[('floor_id')]];
						}
						else if($group_by==2)
						{
							$group_value=$batch[('shift_name')];
							$group_name="sub_batch";
							$group_dtls_value=$shift_name[$sub_batch[('shift_name')]];
						}
						else if($group_by==3)
						{
							$group_value=$sub_batch[('machine_id')];
							$group_name="machine";
							$sub_group_dtls_value=$machine_arr[$sub_batch[('machine_id')]];
						}
						if (!in_array($group_value,$sub_group_by_arr) )
						{
							if($k!=1)
							{
							?>
                                <tr class="tbl_bottom">
                                    <td width="30">&nbsp;</td>


                                    <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>


                                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total :</strong></td>
                                    <td width="70"><? //echo number_format($batch_qnty,2); ?></td>
                                    <td width="40"><? //echo number_format($batch_qnty_trims,2); ?></td>

									<td width="70"><? echo number_format($sub_batch_qnty,0); ?></td>
                                    <td width="70"><? echo number_format($sub_batch_qnty_trims,0); ?></td>


                                    <td width="75" colspan="9">&nbsp;</td>

                                </tr>
								<?
								unset($sub_batch_qnty);unset($sub_batch_qnty_trims);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="27"><b style="margin-left:0px"><? echo $group_name; ?> : <? echo $sub_group_dtls_value; ?></b></td>
							</tr>
							<?
							$sub_group_by_arr[]=$group_value;
							$k++;
						}
					}


					$order_id=$sub_batch[('po_id')];

					$batch_weight=$sub_batch[('batch_weight')];
					$water_cons_unload=$sub_batch[('water_flow_meter')];
					$water_cons_load=$water_flow_arr[$batch_id];
					$load_hour_meter=$load_hour_meter_arr[$batch_id];
					//echo $water_cons_load.'=='.$water_cons_unload;
					$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

					$desc=explode(",",$sub_batch[('item_description')]);
					$po_number=implode(",",array_unique(explode(",",$sub_batch[('po_number')])));
					$file_no=implode(",",array_unique(explode(",",$sub_batch[('file_no')])));
					$ref_no=implode(",",array_unique(explode(",",$sub_batch[('grouping')])));
					$sub_dyeing_prod_qty=$sub_batch[('sub_batch_qnty')];//$shift_wise_prod_arr[$batch_id][$batch[('fabric_type')]][$prod_id]['prod_qty'];
					$batch_no=$sub_batch[('id')];
				if (!in_array($batch_no,$sub_trims_check_array))
					{ $z++;


						 $sub_trims_check_array[]=$batch_no;
						  $sub_tot_trim_qty=$sub_batch[('total_trims_weight')];
					}
					else
					{
						 $sub_tot_trim_qty=0;
					}

					?>
					<tr bgcolor="<? echo $bgcolor_sub_dyeing; ?>"  id="trsub_<? echo $i; ?>" onClick="change_color('trsub_<? echo $i; ?>','<? echo $bgcolor_sub_dyeing; ?>')">
                        <td width="30"><? echo $i; ?></td>


                         <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$sub_batch[('machine_id')]]; ?></div></td>
                        <?
						 }
						 if($group_by==2 || $group_by==0){ ?>

                        <? } if($group_by==1 || $group_by==0){ ?>

                        <? } ?>
                       
                       
                        <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $buyer_arr[$sub_batch[('buyer_name')]]; ?></div></td>
						<td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $sub_batch[('booking_no')]; ?></div></td>

                        <td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $batch[('item_description')]; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $fabric_typee[$sub_batch[('width_dia_type')]]; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$sub_batch[('color_id')]]; ?></div></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $sub_batch[('batch_no')]; ?></div></td>
                        <td width="40"><p><? echo $sub_batch[('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo number_format($sub_dyeing_prod_qty,2);  ?></td>
                       	<td align="right" width="70"><? echo number_format($sub_tot_trim_qty,0);  ?></td>
                       	<td align="right" width="70"><? echo $water_cons_load;  ?></td>

                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $sub_load_t=$subcon_load_hr[$batch_id].':'.$subcon_load_min[$batch_id]; echo  ($subcon_load_date[$batch_id] == '0000-00-00' || $subcon_load_date[$batch_id] == '' ? '' : change_date_format($subcon_load_date[$batch_id])).' <br> '.$sub_load_t;
						//echo $batch[csf('id')];
                        ?></div></td>
                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $hr=strtotime($unload_date,$sub_load_t); $min=($sub_batch[('end_minutes')])-($load_min[$batch_id]);
						echo  ($sub_batch[('process_end_date')] == '0000-00-00' || $sub_batch[('process_end_date')] == '' ? '' : change_date_format($sub_batch[('process_end_date')])).'<br>'.$unload_time=$sub_batch[('end_hours')].':'.$sub_batch[('end_minutes')];
						$unloaded_date=change_date_format($sub_batch[('process_end_date')]);
						 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></div></td>
                        <td align="center" width="60">
							<?
                            $new_date_time_unload=($unloaded_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($subcon_load_date[$batch_id].' '.$sub_load_t.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
                            echo floor($total_time/60).":".$total_time%60;
                            ?>
                        </td>
                        <td align="center" width="100" title="<? echo $sub_batch[('fabric_type')];?>"><div style="width:100px; word-wrap:break-word;"><? echo $fabric_type_for_dyeing[$sub_batch[('fabric_type')]]; ?> </div> </td>
						<td align="center" width="50"><p><? echo $shift_name[$sub_batch[('shift_name')]]; ?></p> </td>
                        <td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $dyeing_result[$sub_batch[('result')]]; ?></div> </td>
                         <td align="center"><p><? echo $sub_batch[('remarks')]; ?></p> </td>
					</tr>
					<?
					if($sub_batch[('result')]==1){
						$sub_tot_trims_qnty+=$sub_tot_trim_qty;
						 }
						  else {
						 $sub_tot_trims_qnty+=0;
						  }
					$i++;
					$sub_batch_qnty+=$sub_dyeing_prod_qty;
					$sub_batch_qnty_trims+=$sub_tot_trim_qty;
					$sub_total_water_cons_load+=$water_cons_load;
					$total_water_cons_unload+=$water_cons_unload;
					$sub_tot_trims_qnty+=$sub_tot_trim_qty;
					$trims_summary+=$sub_tot_trim_qty;
					$sub_grand_total_batch_qty+=$sub_dyeing_prod_qty;
					} //batchdata froeach
				}
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    
                    <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>
                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total: </strong></td>
                    <td width="70"><? //echo number_format($batch_qnty,2); ?></td>
                    <td width="40"><? //echo number_format($batch_qnty_trims,2); ?></td>
					 <td width="70"><? echo number_format($sub_batch_qnty,0); ?></td>
                    <td width="70"><? echo number_format($sub_batch_qnty_trims,0); ?></td>
                    <td width="75" colspan="9">&nbsp;</td>
                </tr>
			<? } ?>
			</tbody>
           

        </table>
			</div>
			<table class="rpt_table" width="1530" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				 <tfoot>
                <tr>
                
					
					<th width="30">&nbsp;</th>
					 <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? }  ?>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="90">Grand</th>
					<th width="40">Total:</th>
					<th width="70"  id="value_total_batch_qty2"><? echo number_format($sub_grand_total_batch_qty,2); ?></th>
					<th width="70"  id="value_total_batch_trim_qty2"><? echo number_format($sub_tot_trims_qnty,0); ?></th>
					<th width="70"></th>
					
					<th width="75"></th>
					<th width="75"></th>
					<th width="60"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width=""></th>

                </tr>

                
                </tfoot>
			</table>
			</div>
		<?
			}
		} // Sub Cond End

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) // Sample Production
		{
			if (count($samp_batch_detail_arr)>0)
			{
				$group_by=str_replace("'",'',$cbo_group_by);

			 ?>
		 <div align="left" style="float:left; clear:both;">
				
			 <table class="rpt_table" width="1530" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_3">
              <caption> <b> Sample Dyeing Production   </b></caption>
              
                <thead>
                    <tr>
                        <th width="30">SL</th>
                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                       <th width="100">Buyer</th>
                        <th width="100">F.Booking No.</th>
                        <th width="150">Fabrics Desc</th>
                        <th width="80">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Wgt.</th>
                        <th width="70">Water Flow</th>

                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
						<th width="50">Shift</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1550px; overflow-y:scroll; float:left; clear:both;" id="scroll_body_samp">
            <table class="rpt_table" id="table_body3" width="1530" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
                <?
				$tot_sum_trims_qnty=0;
                $i=1; $btq=0; $k=1;$z=1;$total_water_cons_load=0;$total_water_cons_unload=0;
                $samp_batch_chk_arr=array(); $group_by_arr=array();$samp_tot_trims_qnty=0;$samp_trims_check_array=array();
              foreach($samp_batch_detail_arr as $batch_id=>$prod_data)
			  {
				foreach($prod_data as $prod_id=>$samp_batch)
				{
					if ($i%2==0)  $bgcolor_sam_dyeing="#E9F3FF"; else $bgcolor_sam_dyeing="#FFFFFF";
					if($group_by!=0)
					{
						if($group_by==1)
						{
							$group_value=$samp_batch[('floor_id')];
							$group_name="Floor";
							$group_dtls_value=$floor_arr[$samp_batch[('floor_id')]];
						}
						else if($group_by==2)
						{
							$group_value=$samp_batch[('shift_name')];
							$group_name="Shift";
							$group_dtls_value=$shift_name[$samp_batch[('shift_name')]];
						}
						else if($group_by==3)
						{
							$group_value=$samp_batch[('machine_id')];
							$group_name="machine";
							$samp_group_dtls_value=$machine_arr[$samp_batch[('machine_id')]];
						}
						if (!in_array($group_value,$samp_batch_chk_arr) )
						{
							if($k!=1)
							{
							?>
                                <tr class="tbl_bottom">
                                    <td width="30">&nbsp;</td>


                                    <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>


                                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total :</strong></td>
                                    <td width="70"><? //echo number_format($batch_qnty,2); ?></td>
                                    <td width="40"><? //echo number_format($batch_qnty_trims,2); ?></td>

									<td width="70"><? echo number_format($samp_batch_qnty,0); ?></td>
                                    <td width="70"><? echo number_format($samp_batch_qnty_trims,0); ?></td>


                                    <td width="75" colspan="9">&nbsp;</td>

                                </tr>
								<?
								unset($samp_batch_qnty);unset($samp_batch_qnty_trims);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="27"><b style="margin-left:0px"><? echo $group_name; ?> : <? echo $samp_group_dtls_value; ?></b></td>
							</tr>
							<?
							$samp_batch_chk_arr[]=$group_value;
							$k++;
						}
					}


					$order_id=$batch[('po_id')];

					$batch_weight=$samp_batch[('batch_weight')];
					$water_cons_unload=$samp_batch[('water_flow_meter')];
					$water_cons_load=$water_flow_arr[$batch_id];
					$load_hour_meter=$load_hour_meter_arr[$batch_id];
					//echo $water_cons_load.'=='.$water_cons_unload;
					$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;
					$desc=explode(",",$samp_batch[('item_description')]);
					$po_number=implode(",",array_unique(explode(",",$samp_batch[('po_number')])));
					//$dyeing_prod_qty=$shift_wise_prod_arr[$batch_id][$batch[('fabric_type')]][$prod_id]['prod_qty'];
					$samp_dyeing_prod_qty=$shift_wise_prod_arr[$batch_id][$samp_batch[('fabric_type')]][$prod_id]['prod_qty'];
					$batch_no=$samp_batch[('id')];
				if (!in_array($batch_no,$samp_trims_check_array))
					{ $z++;


						 $samp_trims_check_array[]=$batch_no;
						  $samp_tot_trim_qty=$samp_batch[('total_trims_weight')];
					}
					else
					{
						 $samp_tot_trim_qty=0;
					}

					?>
					<tr bgcolor="<? echo $bgcolor_sam_dyeing; ?>"  id="trsam_<? echo $i; ?>" onClick="change_color('trsam_<? echo $i; ?>','<? echo $bgcolor_sam_dyeing; ?>')">
                        <td width="30"><? echo $i; ?></td>


                         <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_arr[$samp_batch[('machine_id')]]; ?></div></td>
                        <?
						 }
						 if($group_by==2 || $group_by==0){ ?>

                        <? } if($group_by==1 || $group_by==0){ ?>

                        <? } ?>
                       
                       
                        <td width="100"><p><? echo $buyer_arr[$samp_batch[('buyer_name')]]; ?></p></td>
						<td align="center" width="100"><p><? echo $samp_batch[('booking_no')]; ?></p></td>

                        <td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $samp_batch[('item_description')]; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $fabric_typee[$samp_batch[('width_dia_type')]]; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$samp_batch[('color_id')]]; ?></div></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $samp_batch[('batch_no')]; ?></div></td>
                        <td width="40"><p><? echo $samp_batch[('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo number_format($samp_dyeing_prod_qty,2);  ?></td>
                       	<td align="right" width="70"><? echo number_format($samp_tot_trim_qty,0);  ?></td>
                       	<td align="right" width="70"><? echo $water_cons_load;  ?></td>

                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $load_t=$load_hr[$batch_id].':'.$load_min[$batch_id]; echo  ($load_date[$batch_id] == '0000-00-00' || $load_date[$batch_id] == '' ? '' : change_date_format($load_date[$batch_id])).' <br> '.$load_t;
						//echo $batch[csf('id')];
                        ?></div></td>
                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $hr=strtotime($unload_date,$load_t); $min=($samp_batch[('end_minutes')])-($load_min[$batch_id]);
						echo  ($samp_batch[('process_end_date')] == '0000-00-00' || $samp_batch[('process_end_date')] == '' ? '' : change_date_format($samp_batch[('process_end_date')])).'<br>'.$unload_time=$samp_batch[('end_hours')].':'.$samp_batch[('end_minutes')];
						$unloaded_date=change_date_format($samp_batch[('process_end_date')]);
						 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></div></td>
                        <td align="center" width="60">
						<div style="width:60px; word-wrap:break-word;">	<?
                            $new_date_time_unload=($unloaded_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($load_date[$batch_id].' '.$load_t.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
                            echo floor($total_time/60).":".$total_time%60;
                            ?>
							</div>
                        </td>
                        <td align="center" width="100" title="<? echo $samp_batch[('fabric_type')];?>"><div style="width:100px; word-wrap:break-word;"><? echo $fabric_type_for_dyeing[$samp_batch[('fabric_type')]]; ?> </div> </td>
						<td align="center" width="50"><p><? echo $shift_name[$samp_batch[('shift_name')]]; ?></p> </td>
                        <td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $dyeing_result[$samp_batch[('result')]]; ?></div> </td>
                         <td align="center"><p><? echo $samp_batch[('remarks')]; ?></p> </td>
					</tr>
					<?
					
					$i++;
					$samp_batch_qnty+=$samp_dyeing_prod_qty;
					$batch_qnty_trims+=$samp_tot_trim_qty;
					$total_water_cons_load+=$water_cons_load;
					$total_water_cons_unload+=$water_cons_unload;
					$samp_tot_trims_qnty+=$samp_tot_trim_qty;
				
					$samp_grand_total_batch_qty+=$samp_dyeing_prod_qty;
					} //batchdata froeach
				}
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    
                    <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>
                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total: </strong></td>
                    <td width="70"><? //echo number_format($batch_qnty,2); ?></td>
                    <td width="40"><? //echo number_format($batch_qnty_trims,2); ?></td>
					 <td width="70"><? echo number_format($samp_batch_qnty,0); ?></td>
                    <td width="70"><? echo number_format($samp_batch_qnty_trims,0); ?></td>
                    <td width="75" colspan="9">&nbsp;</td>
                </tr>
			<? } ?>
			</tbody>
           

        </table>
			</div>
			<table class="rpt_table" width="1530" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				 <tfoot>
                <tr>
                
					
					<th width="30">&nbsp;</th>
					 <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? }  ?>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="90">Grand</th>
					<th width="40">Total:</th>
					<th width="70"  id="value_total_batch_qty3"><? echo number_format($samp_grand_total_batch_qty,2); ?></th>
					<th width="70"  id="value_total_batch_trim_qty3"><? echo number_format($samp_tot_trims_qnty,0); ?></th>
					<th width="70"></th>
					
					<th width="75"></th>
					<th width="75"></th>
					<th width="60"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width=""></th>

                </tr>
                </tfoot>
			</table>
			
			</div>
			<br/>
		<? }
			
		}
		?>
		</div>
		</div>
		<?
		/*	foreach (glob("$user_name*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_name."_".$name.".xls";
		$filename_summary=$user_name."_".$name."summ.xls";
		$create_new_doc = fopen($filename, 'w');
		$create_new_doc_summary = fopen($filename_summary, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		$is_created_summary= fwrite($create_new_doc_summary,$html_summary);*/
		//$filename=$user_id."_".$name.".xls";
		foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
		//$filename_short="../../../ext_resource/tmp_report/".$user_name."_".$name."summ.xls";
		
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		echo "$total_data****$filename****$report_type";

		
		//==================================================================================end====================
	} //Dyeing Production End
	//echo $cbo_group_by;die;
	$cbo_group_by = str_replace("'","",$cbo_group_by);

	if($cbo_type==2 && $cbo_group_by==3 && $report_type==3) //  Dyeing Production-Machine Wise
	{
		//echo $report_type.'SDD';die;
		
		
			
		if($cbo_type==2) // Dyeing Production
		{

			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
			{
				//echo $sql;
				$b=0;$kk=0;
				$batchdata=sql_select($sql);$batch_aganist_arr=array(2,3);$batch_qty_check_array=array();$batch_double_check_array=array();
				$batch_ids='';$all_po_id='';$total_re_dyeing_qty=$total_shadematched_qty=$total_shadematched_shelf_trim_qty=$total_shadematched_shelf_qty=$total_water_flow_ltr=0;
				foreach($batchdata as $row)
				{
					if($batch_ids=='') $batch_ids=$row[csf('id')]; else $batch_ids.=",".$row[csf('id')];
					if($all_po_id=='') $all_po_id=$row[csf('po_id')]; else $all_po_id.=",".$row[csf('po_id')];
					
					$item_desc=explode(",",$row[csf('item_description')]);
					$booking_noArr=explode("-",$row[csf('booking_no')]);
					if($booking_noArr[1]=='Fb' || $booking_noArr[1]=='FB')
					{
					$batch_detail_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$batch_detail_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$batch_detail_arr[$row[csf('id')]]['working_company_id']=$row[csf('working_company_id')];
					$batch_detail_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
					$batch_detail_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
					$batch_detail_arr[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
					$batch_detail_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					$batch_detail_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
					$batch_detail_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
					$batch_detail_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
					$batch_detail_arr[$row[csf('id')]]['batch_qnty']+=$row[csf('batch_qnty')];
					$batch_detail_arr[$row[csf('id')]]['width_dia_type'].=$fabric_typee[$row[csf('width_dia_type')]].',';
					$batch_detail_arr[$row[csf('id')]]['item_description'].=$item_desc[0].',';
					$batch_detail_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$batch_detail_arr[$row[csf('id')]]['po_id']=$row[csf('po_id')];
					$batch_detail_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$batch_detail_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$batch_detail_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
					$batch_detail_arr[$row[csf('id')]]['shift_name']=$row[csf('shift_name')];
					$batch_detail_arr[$row[csf('id')]]['process_end_date']=$row[csf('process_end_date')];
					$batch_detail_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
					$batch_detail_arr[$row[csf('id')]]['end_hours']=$row[csf('end_hours')];
					$batch_detail_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
					$batch_detail_arr[$row[csf('id')]]['hour_unload_meter']=$row[csf('hour_unload_meter')];
					$batch_detail_arr[$row[csf('id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
					$batch_detail_arr[$row[csf('id')]]['end_minutes']=$row[csf('end_minutes')];
					$batch_detail_arr[$row[csf('id')]]['machine_id']=$row[csf('machine_id')];
					$batch_detail_arr[$row[csf('id')]]['load_unload_id']=$row[csf('load_unload_id')];
					$batch_detail_arr[$row[csf('id')]]['fabric_type']=$row[csf('fabric_type')];
					$batch_detail_arr[$row[csf('id')]]['result']=$row[csf('result')];
					$batch_detail_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];
					$batch_detail_arr[$row[csf('id')]]['seq_no']=$row[csf('seq_no')];
					//$batch_detail_arr[$row[csf('id')]]['water_flow']=$water_flow_arr[$row[csf('id')]];
					
					$batch_detail_arr[$row[csf('id')]]['shift_wise_qty']=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty'];//buyer_trims_self_arr
					
					$batch_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['without_shift_qty']+=$fabric_withOutshift;
					$batch_all_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['all_shift_qty']+=$shift_fabric_batch_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]];
					
					
					if($row[csf('batch_against')]==2)//batch_qnty
					{
						if($row[csf('result')]==1)
						{ //batch_wise_prod_qty_arr
							$total_shadematched_shelf_reprocess_qty+=$batch_wise_prod_qty_arr[$row[csf('id')]][$row[csf('fabric_type')]]['prod_qty'];
							$total_shadematched_shelf_trim_reprocess_qty+=$row[csf('total_trims_weight')];
						}
					}
							
						if($row[csf('result')]==1 && (!in_array($row[csf('batch_against')],$batch_aganist_arr)))
						{
							//if($row[csf('batch_against')]==3) echo "X";else echo "";
							$buyer_shadematched_arr[$row[csf('buyer_name')]]['batch_qty']+=$batch_wise_prod_qty_arr[$row[csf('id')]][$row[csf('fabric_type')]]['prod_qty']; 
							$buyer_shadematched_arr[$row[csf('buyer_name')]]['trim']+=$row[csf('total_trims_weight')];
							
						
							$total_shadematched_qty+=$batch_wise_prod_qty_arr[$row[csf('id')]][$row[csf('fabric_type')]]['prod_qty']+$row[csf('total_trims_weight')];
							if($row[csf('batch_against')]!=2)//batch_qnty
							{
							$total_shadematched_shelf_qty+=$batch_wise_prod_qty_arr[$row[csf('id')]][$row[csf('fabric_type')]]['prod_qty'];
							$total_shadematched_shelf_trim_qty+=$row[csf('total_trims_weight')];
							}
						}
						if($row[csf('result')]==1)
						{
							if (!in_array($row[csf('id')],$batch_double_check_array))
							{ $kk++;
								 $batch_double_check_array[]=$row[csf('id')];
								  $water_flow_cons=$water_flow_arr[$row[csf('id')]];
							}
							else
							{
								 $water_flow_cons=0;
							}
						}
							$total_water_flow_ltr+=$water_flow_cons;
					
				//	2-4-2019=== 2
					}
				}//Loop End
				//echo $total_water_flow_ltr.'D';;
				$po_idsid=implode(",",(array_unique(explode(",",$all_po_id))));
				$batch_idss=implode(",",(array_unique(explode(",",$batch_ids))));

				$poIds=chop($po_idsid,','); $po_cond_for_in="";
				$po_ids=count(array_unique(explode(",",$po_idsid)));
				if($db_type==2 && $po_ids>999)
				{
					$po_cond_for_in=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
					$ids=implode(",",$ids);
					$po_cond_for_in.="b.po_breakdown_id in($ids) or";
					}
					$po_cond_for_in=chop($po_cond_for_in,'or ');
					$po_cond_for_in.=")";
				}
				else
				{
				$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
				}

				$batchIds=chop($batch_idss,','); $batch_cond_for_in="";
				$batch_ids=count(array_unique(explode(",",$batchIds)));
				if($db_type==2 && $batch_ids>999)
				{
					$batch_cond_for_in=" and (";
					$batchIdsArr=array_chunk(explode(",",$batchIds),999);
					foreach($batchIdsArr as $ids)
					{
					$ids=implode(",",$ids);
					$batch_cond_for_in.="a.id in($ids) or";
					}
					$batch_cond_for_in=chop($batch_cond_for_in,'or ');
					$batch_cond_for_in.=")";
				}
				else
				{
				$batch_cond_for_in=" and a.id in($batchIds)";
				}

			}
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
			{
				//echo $sql_subcon;
				$sql_subcon_data=sql_select($sql_subcon);
				$batchdata_subcn=sql_select($sql_sam);
				$batch_sub_check_array=array();$batch_sub_check_array2=array();$s=$m=1;$total_re_batch_trims_weight=$total_without_fabric_shift_qty=0;
				$subcn_batch_ids='';$total_subcon_summary=$total_shadematched_subcon_qty=$total_shadematched_subcon_re_trim_qty=$total_shadematched_subcon_re_trim_qty=0;
				$total_batch_trims_weight=0;
				foreach($sql_subcon_data as $row)
				{
					if($subcn_batch_ids=='') $subcn_batch_ids=$row[csf('id')]; else $subcn_batch_ids.=",".$row[csf('id')];
					$item_desc=explode(",",$row[csf('item_description')]);
					$subcon_batch_detail_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$subcon_batch_detail_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$subcon_batch_detail_arr[$row[csf('id')]]['working_company_id']=$row[csf('working_company_id')];
					$subcon_batch_detail_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
					$subcon_batch_detail_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
					$subcon_batch_detail_arr[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
					$subcon_batch_detail_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					$subcon_batch_detail_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
					$subcon_batch_detail_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
					$subcon_batch_detail_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
					$subcon_batch_detail_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
					$subcon_batch_detail_arr[$row[csf('id')]]['sub_batch_qnty']=$row[csf('sub_batch_qnty')];
					$subcon_batch_detail_arr[$row[csf('id')]]['item_description'].=$item_desc[0].',';
					$subcon_batch_detail_arr[$row[csf('id')]]['width_dia_type'].=$fabric_typee[$row[csf('width_dia_type')]].',';
					
					$subcon_batch_detail_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$subcon_batch_detail_arr[$row[csf('id')]]['po_id']=$row[csf('po_id')];
					$subcon_batch_detail_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$subcon_batch_detail_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$subcon_batch_detail_arr[$row[csf('id')]]['shift_name']=$row[csf('shift_name')];
					$subcon_batch_detail_arr[$row[csf('id')]]['process_end_date']=$row[csf('process_end_date')];
					$subcon_batch_detail_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
					$subcon_batch_detail_arr[$row[csf('id')]]['end_hours']=$row[csf('end_hours')];
					$subcon_batch_detail_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
					$subcon_batch_detail_arr[$row[csf('id')]]['hour_unload_meter']=$row[csf('hour_unload_meter')];
					$subcon_batch_detail_arr[$row[csf('id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
					$subcon_batch_detail_arr[$row[csf('id')]]['end_minutes']=$row[csf('end_minutes')];
					$subcon_batch_detail_arr[$row[csf('id')]]['machine_id']=$row[csf('machine_id')];
					$subcon_batch_detail_arr[$row[csf('id')]]['load_unload_id']=$row[csf('load_unload_id')];
					$subcon_batch_detail_arr[$row[csf('id')]]['fabric_type']=$row[csf('fabric_type')];
					$subcon_batch_detail_arr[$row[csf('id')]]['result']=$row[csf('result')];
					$subcon_batch_detail_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];
					$subcon_batch_detail_arr[$row[csf('id')]]['seq_no']=$row[csf('seq_no')];
					
					$fabric_type_batch_arr[$row[csf('fabric_type')]]['all_shift_qty']=$row[csf('fabric_type')]; 
					/*For SunCon Summary*/
						if (!in_array($row[csf('id')],$batch_sub_check_array2))
							{ 	$m++;
								 $batch_sub_check_array2[]=$row[csf('id')];
								 if($row[csf('batch_against')]==2)//batch_qnty
								{
									$tot_sub_re_trim_qty=$row[csf('total_trims_weight')];
									//$tot_sub_re_sub_batch_qnty=$row[csf('sub_batch_qnty')];
								}
								$tot_sub_re_trim_qty2=$row[csf('total_trims_weight')];
							}
							else
							{
								 $tot_sub_re_trim_qty=0; $tot_sub_re_trim_qty2=0;
							}
							if($row[csf('shift_name')]==0)
							{
								//$without_shiftQty=$fabric_batch_wise_arr_withOutshift[$batch_id]['prod_qty'];
								//echo $without_shiftQty.'<br>'.$row['shift_name'];
								$without_fabric_shift_batch_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['out_shift_qty']+=$row[csf('sub_batch_qnty')]+$tot_sub_re_trim_qty2;
								$total_without_fabric_shift_qty+=$row[csf('sub_batch_qnty')]+$tot_sub_re_trim_qty2;
							}
							else
							{
								$fabric_shift_batch_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['all_shift_qty']+=$row[csf('sub_batch_qnty')]+$tot_sub_re_trim_qty2;
							}
					
						if($row[csf('batch_against')]==2)//batch_qnty
						{
							$subcon_summary_arr[$row[csf('buyer_name')]]['sub_re_batch_qnty']+=$row[csf('sub_batch_qnty')];
							$subcon_summary_arr[$row[csf('buyer_name')]]['sub_re_trim_batch_qnty']+=$tot_sub_re_trim_qty;
							$total_subcon_summary+=$row[csf('sub_batch_qnty')];
						}
						else
						{
							$subcon_summary_arr[$row[csf('buyer_name')]]['sub_batch_qnty']+=$row[csf('sub_batch_qnty')];
						}
						if($row[csf('shift_name')]==0)
						{
							//echo $row[csf('sub_batch_qnty')].'DD';
							$batch_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['without_shift_qty']+=$row[csf('sub_batch_qnty')];
						}
						else
						{
							$batch_all_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['all_shift_qty']+=$row[csf('sub_batch_qnty')];
						}
						if($row[csf('result')]==1)
						{
							if($row[csf('batch_against')]==2)//batch_qnty
							{
								$total_shadematched_subcon_re_qty+=$row[csf('sub_batch_qnty')];
								$total_shadematched_subcon_re_trim_qty+=$row[csf('total_trims_weight')];
							}
							if($row[csf('batch_against')]!=2)//batch_qnty
							{
								$total_shadematched_subcon_qty+=$row[csf('sub_batch_qnty')];
								$total_shadematched_subcon_trim_qty+=$row[csf('total_trims_weight')];
							}
							if (!in_array($row[csf('id')],$batch_sub_check_array))
							{ 	$s++;
								 $batch_sub_check_array[]=$row[csf('id')];
								if($row[csf('batch_against')]==2)//batch_qnty
								{
									$tot_shade_subcon_re_trim_qty=$row[csf('total_trims_weight')];
								}
								  $water_flow_cons=$subcon_water_flow_arr[$row[csf('id')]];
								if($row[csf('batch_against')]!=2)
								{
									if($row[csf('total_trims_weight')]>0)
									{
								 	 $tot_shade_subcon_trim_qty=$row[csf('total_trims_weight')];
									 $total_batch_trims_weight+=$tot_shade_subcon_trim_qty;
									 //echo  $tot_shade_subcon_trim_qty.'=';
									}
									//else $tot_shade_subcon_trim_qty=0;
								
								}
							}
							else
							{
								 $water_flow_cons=0; $tot_shade_subcon_re_trim_qty=0; $total_batch_trims_weight+=0;
							}
								$total_water_flow_ltr+=$water_flow_cons;
								$total_re_batch_trims_weight+=$tot_shade_subcon_re_trim_qty;
								 // echo $tot_shade_subcon_trim_qty.'DD'.$row[csf('batch_against')].'<br>';
								
						}
							
							
				}
			}
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) //Batch Aganist - Sample
			{
				// echo $sql_sam;//die;
				$batchdata_sam=sql_select($sql_sam);
				$sam_batch_ids='';$sam_all_po_id='';
				$batch_sam_check_array=array();
				$samp=1;
				foreach($batchdata_sam as $row)
				{
					if($sam_batch_ids=='') $sam_batch_ids=$row[csf('id')]; else $sam_batch_ids.=",".$row[csf('id')];
					$item_desc=explode(",",$row[csf('item_description')]);
					$booking_noSampArr=explode("-",$row[csf('booking_no')]);
					if($booking_noSampArr[1]=='SM' || $booking_noSampArr[1]=='SMN')
					{
					$samp_batch_detail_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$samp_batch_detail_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$samp_batch_detail_arr[$row[csf('id')]]['working_company_id']=$row[csf('working_company_id')];
					$samp_batch_detail_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
					$samp_batch_detail_arr[$row[csf('id')]]['batch_weight']=$row[csf('batch_weight')];
					$samp_batch_detail_arr[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
					$samp_batch_detail_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
					$samp_batch_detail_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];
					$samp_batch_detail_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
					$samp_batch_detail_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
					$samp_batch_detail_arr[$row[csf('id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
					$samp_batch_detail_arr[$row[csf('id')]]['batch_qnty']+=$row[csf('batch_qnty')];
					$samp_batch_detail_arr[$row[csf('id')]]['item_description'].=$item_desc[0].',';
					$samp_batch_detail_arr[$row[csf('id')]]['width_dia_type'].=$fabric_typee[$row[csf('width_dia_type')]].',';
					$samp_batch_detail_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$samp_batch_detail_arr[$row[csf('id')]]['po_id']=$row[csf('po_id')];
					$samp_batch_detail_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$samp_batch_detail_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
					$samp_batch_detail_arr[$row[csf('id')]]['shift_name']=$row[csf('shift_name')];
					$samp_batch_detail_arr[$row[csf('id')]]['process_end_date']=$row[csf('process_end_date')];
					$samp_batch_detail_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
					$samp_batch_detail_arr[$row[csf('id')]]['end_hours']=$row[csf('end_hours')];
					$samp_batch_detail_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
					$samp_batch_detail_arr[$row[csf('id')]]['hour_unload_meter']=$row[csf('hour_unload_meter')];
					$samp_batch_detail_arr[$row[csf('id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
					$samp_batch_detail_arr[$row[csf('id')]]['end_minutes']=$row[csf('end_minutes')];
					$samp_batch_detail_arr[$row[csf('id')]]['machine_id']=$row[csf('machine_id')];
					$samp_batch_detail_arr[$row[csf('id')]]['load_unload_id']=$row[csf('load_unload_id')];
					$samp_batch_detail_arr[$row[csf('id')]]['fabric_type']=$row[csf('fabric_type')];
					$samp_batch_detail_arr[$row[csf('id')]]['result']=$row[csf('result')];
					$samp_batch_detail_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];
					$samp_batch_detail_arr[$row[csf('id')]]['seq_no']=$row[csf('seq_no')];
					$samp_batch_detail_arr[$row[csf('id')]]['shift_wise_qty']+=$row[csf('batch_qnty')];
					$batch_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['without_shift_qty']+=$fabric_withOutshift;
					$batch_all_shift_arr[$row[csf('fabric_type')]][$row[csf('shift_name')]]['all_shift_qty']+=$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty'];
					if($row[csf('result')]==1)
						{
							if($row[csf('batch_against')]==2)//batch_qnty
							{
								$total_shadematched_samp_re_qty+=$row[csf('batch_qnty')];//$shift_wise_prod_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('prod_id')]]['prod_qty'];
								 //echo $row[csf('batch_qnty')].',';
								$total_shadematched_samp_re_trim_qty+=$row[csf('total_trims_weight')];
							}
							if($row[csf('batch_against')]!=2)//batch_qnty
							{
							$total_shadematched_samp_trim_qty+=$row[csf('total_trims_weight')];
							}
							if (!in_array($row[csf('id')],$batch_sam_check_array))
							{ 	$samp++;
								 $batch_sam_check_array[]=$row[csf('id')];
								  $water_flow_cons=$water_flow_arr[$row[csf('id')]];
							}
							else
							{
								 $water_flow_cons=0;
							}
								$total_water_flow_ltr+=$water_flow_cons;
						}
					}
				} //Loop End
				if(count($batchdata_sam)>0)
				{
				$sam_batchIds=chop($sam_batch_ids,','); $sam_batch_cond_for_in="";
				$sam_batch_ids=count(array_unique(explode(",",$sam_batchIds)));
					if($db_type==2 && $sam_batch_ids>999)
					{
						$sam_batch_cond_for_in=" and (";
						$sam_batchIdsArr=array_chunk(explode(",",$sam_batchIds),999);
						foreach($sam_batchIdsArr as $ids)
						{
						$ids=implode(",",$ids);
						$sam_batch_cond_for_in.="a.id in($ids) or";
						}
						$sam_batch_cond_for_in=chop($sam_batch_cond_for_in,'or ');
						$sam_batch_cond_for_in.=")";
					}
					else
					{
						$sam_batch_cond_for_in=" and a.id in($sam_batchIds)";
					}
				}
			}
		}
		ob_start();
		?>
		<div style="width:1350px;">
		 <?
		 if ($cbo_result_name==1 || $cbo_result_name==0)
		 {
		$m=1;$trims_buyer_check_array=array();$tot_shadeMatchQty=0;
		
		 foreach($batch_detail_arr as $batch_id=>$row)
		  {
				$batchid=$batch_id; //fabric_batch_arr
				if (!in_array($batchid,$trims_buyer_check_array))
					{ $m++;
						 $trims_buyer_check_array[]=$batchid;
						  $tot_buyer_trim_qty=$row[('total_trims_weight')];
					}
					else
					{
						 $tot_buyer_trim_qty=0;
					}
				$buyer_dyeing_prod_qty=$batch_wise_prod_qty_arr[$batch_id][$row[('fabric_type')]]['prod_qty'];
				if($row[('batch_against')]==2)
				{
					$buyer_re_process_arr[$row['buyer_name']]['re_batch_qty']+=$buyer_dyeing_prod_qty;
					$buyer_re_process_arr[$row['buyer_name']]['trims_re_batch_qty']+=$tot_buyer_trim_qty;
					$total_re_dyeing_qty+=$buyer_dyeing_prod_qty;
				}
				$fabric_type_batch_arr[$row['fabric_type']]['all_shift_qty']=$row['fabric_type']; 
				if($row['shift_name']==0)
				{
					$without_shiftQty=$fabric_batch_wise_arr_withOutshift[$batch_id]['prod_qty'];
					//echo $without_shiftQty.'<br>'.$row['shift_name'];
					$without_fabric_shift_batch_arr[$row['fabric_type']][$row['shift_name']]['out_shift_qty']+=$without_shiftQty+$tot_buyer_trim_qty;
					$total_without_fabric_shift_qty+=$without_shiftQty+$tot_buyer_trim_qty;
				}
				else
				{
					$fabric_shift_batch_arr[$row['fabric_type']][$row['shift_name']]['all_shift_qty']+=$buyer_dyeing_prod_qty+$tot_buyer_trim_qty;
				}
				if($row['result']==1 && (!in_array($row[('batch_against')],$batch_aganist_arr)))
				{
					$buyer_wise_shadematched_arr[$row['buyer_name']]['batch_qty']+=$buyer_dyeing_prod_qty;
					$buyer_wise_shadematched_arr[$row['buyer_name']]['trim']+=$tot_buyer_trim_qty;
					$tot_shadeMatchQty+=$buyer_dyeing_prod_qty;
					$total_batch_trims_weight+=$tot_buyer_trim_qty;
					
				}
				if($row['result']==1 && $row[('batch_against')]==2)
				{
					$total_re_batch_trims_weight+=$tot_buyer_trim_qty;
				}
			
		}
		$n=1;$samp_trims_buyer_check_array=array();
		 foreach($samp_batch_detail_arr as $batch_id=>$row)
		  {
			
				$samp_batchid=$batch_id; //fabric_batch_arr
				if (!in_array($samp_batchid,$samp_trims_buyer_check_array))
					{ $n++;
						 $samp_trims_buyer_check_array[]=$samp_batchid;
						 //echo $row[('total_trims_weight')].'DD';
						  $samp_tot_buyer_trim_qty=$row[('total_trims_weight')];
					}
					else
					{
						 $samp_tot_buyer_trim_qty=0;
					}
				$samp_buyer_dyeing_prod_qty=$batch_wise_prod_qty_arr[$batch_id][$row[('fabric_type')]]['prod_qty'];
				 $fabric_type_batch_arr[$row['fabric_type']]['all_shift_qty']=$row['fabric_type'];
				if($row['shift_name']==0)
				{
					$samp_without_shiftQty=$fabric_batch_wise_arr_withOutshift[$batch_id]['prod_qty'];
					//echo $without_shiftQty.'<br>'.$row['shift_name'];
					$without_fabric_shift_batch_arr[$row['fabric_type']][$row['shift_name']]['out_shift_qty']+=$samp_without_shiftQty+$samp_tot_buyer_trim_qty;
					$total_without_fabric_shift_qty+=$samp_without_shiftQty+$samp_tot_buyer_trim_qty;
				}
				else
				{
					$fabric_shift_batch_arr[$row['fabric_type']][$row['shift_name']]['all_shift_qty']+=$samp_buyer_dyeing_prod_qty+$samp_tot_buyer_trim_qty;
				}
				 
				if($row['result']==1 && $row[('batch_against')]!=2) //$batch_aganist_arr=array(2,3);
				{
					$total_batch_trims_weight+=$samp_tot_buyer_trim_qty;
				}
				if($row['result']==1 && $row[('batch_against')]==3) //$batch_aganist_arr=array(2,3);
				{
						$total_shadematched_samp_qty+=$batch_wise_prod_qty_arr[$batch_id][$row[('fabric_type')]]['prod_qty'];
				}
				if($row['result']==1 && $row[('batch_against')]==2)
				{
					$total_re_batch_trims_weight+=$samp_tot_buyer_trim_qty;
				}
				$samp_buyer_dyeing_prod_qty=$batch_wise_prod_qty_arr[$batch_id][$row[('fabric_type')]]['prod_qty'];
				//echo $row[('batch_against')].'XX';
				if($row[('batch_against')]==2)
				{
					$buyer_re_process_arr[$row['buyer_name']]['re_batch_qty']+=$samp_buyer_dyeing_prod_qty;
					$buyer_re_process_arr[$row['buyer_name']]['trims_re_batch_qty']+=$samp_tot_buyer_trim_qty;
				}
		 }
		 
			 ?>
			 <div id="dyeing_prod_report_print">
			 <div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production-Machine Wise </strong><br>
			<?
				echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
				
				?>
			 </div>
			
				<div align="left">
				<input type="button" id="dyeing_prod_print_button" class="formbutton" value="Print" onClick=	
				"print_report_part_by_part('dyeing_prod_report_print','#dyeing_prod_print_button')"/>
				</div>
				<table cellpadding="0"  width="820" cellspacing="0" align="left" style="margin-left:20px;margin:10px" >
				<tr>
				<td width="450" valign="top" align="left" style="margin:10px" colspan="8" >
					<table style="width:500px;border:1px solid #000;margin:10px"  align="center" border="1"  class="rpt_table" rules="all" >
							   <thead>
								 <tr>
								   <th colspan="8">Summary Total(Shade Match)</th>
								 </tr>
								 <tr>
								   <th>Self Batch</th>
								   <th>Re-Process</th>
								   <th>Sample Batch</th>
								   <th>Trims Weight</th>
                                   <th>Re-Pro. Trims Weight</th>
								   <th>SubCon Batch</th>
								   <th>Total</th>
                                   <th>Water Flow Total(L)</th>
								 </tr>
							   </thead>
							   <tbody>
								 <?
									$total_batch_qty_shelf=0;$total_batch_qty_sample=0;$total_batch_qty_subcon=0;
									//echo array_sum($buyer_re_process_arr);
									 if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									 $total_trim_qty=$tot_buyer_trim_qty;//$total_shadematched_shelf_trim_qty+$total_shadematched_subcon_trim_qty+$total_shadematched_samp_trim_qty;
									 $total_shadeMatch_qty=$total_shadematched_shelf_qty+$total_shadematched_samp_qty+$total_trim_qty+$total_shadematched_subcon_qty;
									  
									// echo $total_re_dyeing_qty.'DSZ'.$total_shadematched_samp_re_qty;
									$tot_reprocess_qty=$total_re_dyeing_qty+$total_shadematched_samp_re_qty+$total_shadematched_subcon_re_qty; 
								//$title_hd="Self Qty($total_shadematched_shelf_reprocess_qty),SubconQty($total_shadematched_subcon_re_qty),SampleQty($total_shadematched_samp_re_qty)";
									
									?>
								 <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
								   <td align="right"><? echo number_format($tot_shadeMatchQty,0); ?></td>
									<td align="right" title="<? echo $title_hd;?>"><? echo number_format($tot_reprocess_qty,0); ?></td>
								   <td align="right"><? echo number_format($total_shadematched_samp_qty,0); ?></td>
								   <td align="right"><? echo number_format($total_batch_trims_weight,0,'.',''); ?></td>
                                   <td align="right"><? echo number_format($total_re_batch_trims_weight,0,'.',''); ?></td>
								   <td align="right"> <? echo number_format($total_shadematched_subcon_qty,0,'.',''); ?></td>
								   <td align="right"><? $grandtotal_shade_match_qty=$tot_shadeMatchQty+$tot_reprocess_qty+$total_shadematched_samp_qty+$total_batch_trims_weight+$total_shadematched_subcon_qty+$total_re_batch_trims_weight;
									echo number_format($grandtotal_shade_match_qty,0,'.',''); ?></td>
                                    <td align="right"> <? echo number_format($total_water_flow_ltr,0,'.',''); ?></td>
								   
								 </tr>
								 <?
								//$total_batch_qty_shelf+=$batch_per;
								
									  ?>
							   </tbody>
							   <tfoot>
								 <tr bgcolor="#CCCCCC" style="font-weight:bold;">
								  <th align="right"><b><? echo number_format(($tot_shadeMatchQty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="right"><b><? echo number_format(($tot_reprocess_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="right"><b><? echo number_format(($total_shadematched_samp_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="right"><b><? echo number_format(($total_batch_trims_weight/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
                                   <th align="right"><b><? echo number_format(($total_re_batch_trims_weight/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="right"><b><? echo number_format(($total_shadematched_subcon_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
								  <th align="right"><b><? echo number_format(($grandtotal_shade_match_qty/$grandtotal_shade_match_qty)*100,0,'.','').'%';?></b></th>
                                   <th align="right" title="WaterFlow/Total Prod"><b><? echo number_format($total_water_flow_ltr/$grandtotal_shade_match_qty,0,'.','');?></b></th>
								 </tr>
							   </tfoot>
							 </table>
                             <br>
							
				</td>
				</tr>
				 <tr style="margin:10px">
				 <td  valign="top" width="230"  >
					 <table cellpadding="0"  width="230" style="margin:10px" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
								<thead>
									<?
										$shift_count=count($shift_name);
										$colspan=2+$shift_count;
									?>
									<tr>
										<th colspan="2">Monthly Production Summary</th>
									</tr>
									<tr>
										<th width="160">Details </th>
										<th width="60">Prod. Qty. </th>
									</tr>
								</thead>
								<tbody>
								<?
								if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
								if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1 || str_replace("'",'',$cbo_batch_type)==3)
								{
									
								if($db_type==0) $group_cond_batch="group_concat( distinct a.id) AS batch_id";
								if($db_type==2) $group_cond_batch="listagg(a.id ,',') within group (order by a.id) AS batch_id";
	
								$sql="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								 sum(CASE WHEN a.batch_against in(1) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								 sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS trim_wgt, 
								 sum(distinct CASE WHEN a.batch_against in(2) THEN a.total_trims_weight ELSE 0 END) AS re_trims_wgt,
								 sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty
	
								  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id  $companyCond $workingCompany_name_cond2 and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto $booking_no_cond $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY f.load_unload_id,f.process_end_date  ";
								$sql_datas=sql_select($sql);
								}
								$unload_qty_arr=array();$plan_cycle_time=0;$reprocess_qty_arr=array();$tot_reprocess_qty=0;
								foreach($sql_datas as $row)
								{
									$fabric_batch_id=rtrim($fabric_batch_id_arr[$row[csf('load_unload_id')]][$row[csf('process_end_date')]],',');
									$batch_ids=array_unique(explode(",",$fabric_batch_id));
									$self_prod_qty=$re_self_prod_qty=0;
									foreach($batch_ids as $bid)
									{
										$self_prod_qty+=$batch_wise_prod_qty_arr2[$bid]['prod_qty'];
										$re_self_prod_qty+=$batch_wise_re_prod_qty_arr2[$bid]['prod_qty'];
									}
									//echo $re_self_prod_qty.'CC';
									//$batch_wise_prod_qty_arr2[$row[csf('batch_id')]]['prod_qty'];
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr[$row[csf('load_unload_id')]]['qty']+=$self_prod_qty+$row[csf('trim_wgt')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty+=$re_self_prod_qty+$row[csf('re_trims_wgt')];
									$unload_qty_arr[$row[csf('load_unload_id')]]['count']+=$tot_row;
									$unload_qty_arr[$row[csf('load_unload_id')]]['process_end_date'].=$row[csf('process_end_date')].',';
									$unload_date_count_arr[$row[csf('process_end_date')]]=$row[csf('process_end_date')];
								}
								unset($sql_datas);
	
								//print_r($unload_qty_arr);
								 $total_current_mon_qty1=$unload_qty_arr[2]['qty'];
								$total_count1=$unload_qty_arr[2]['count'];
								$total_reprocess_qty1=$tot_reprocess_qty;
	
								 $sql_sample_currMon="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								 sum(CASE WHEN a.batch_against in(3) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								 sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty,
								 sum(distinct CASE WHEN a.batch_against in(3) THEN a.total_trims_weight ELSE 0 END) AS trim_wgt, 
								 sum(distinct CASE WHEN a.batch_against in(2) THEN a.total_trims_weight ELSE 0 END) AS re_trims_wgt
								  from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  f.batch_id=a.id $companyCond  $workingCompany_name_cond2 and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1  and a.batch_against in(2,3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $prod_date_upto  $batch_num $buyerdata2  $color_name $booking_no_cond $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY  f.load_unload_id,f.process_end_date  ";
								 $sql_result_samp_currMon=sql_select($sql_sample_currMon);
								 $tot_reprocess_qty2=0;
								  $process_enddate=rtrim($unload_qty_arr[2]['process_end_date'],',');
								 $process_enddates=array_unique(explode(",",$process_enddate));
								 foreach($sql_result_samp_currMon as $row)
								{
									$fabric_batch_id=rtrim($fabric_batch_id_arr[$row[csf('load_unload_id')]][$row[csf('process_end_date')]],',');
									$batch_ids=array_unique(explode(",",$fabric_batch_id));
									$samp_prod_qty=$samp_re_self_prod_qty=0;
									foreach($batch_ids as $bid)
									{
										$samp_prod_qty+=$samp_batch_wise_prod_qty_arr2[$bid]['prod_qty'];
										$samp_re_self_prod_qty+=$samp_batch_wise_re_prod_qty_arr2[$bid]['prod_qty'];
									}
									//echo $row[csf('trim_wgt')].'TDDD';
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr2[$row[csf('load_unload_id')]]['qty']+=$samp_prod_qty+$row[csf('trim_wgt')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty2+=$samp_re_self_prod_qty+$row[csf('re_trims_wgt')];
									  $isval=array_diff($row[csf('process_end_date')],$edate);
									  $tot_rows=0;
									 foreach($process_enddates as $edate)
									 {
										  $tot_rows=count($row[csf('process_end_date')]);
										  $isval=array_diff($row[csf('process_end_date')],$edate);
										 if($isval)
										 {
											$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_rows;
										 }
									}
									$unload_date_count_arr[$row[csf('process_end_date')]]=$row[csf('process_end_date')];
									//$unload_qty_arr2[$row[csf('load_unload_id')]]['count']+=$tot_row;
								}
								//echo $unload_qty_arr2[2]['qty'].'='.$total_current_mon_qty1;
								unset($sql_result_samp_currMon);
								 $sql_subcon="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
								 sum(CASE WHEN a.batch_against in(1) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
								 sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty,
								 sum(distinct CASE WHEN a.batch_against in(1) THEN a.total_trims_weight ELSE 0 END) AS trim_wgt, 
								 sum(distinct CASE WHEN a.batch_against in(2) THEN a.total_trims_weight ELSE 0 END) AS re_trims_wgt
								  from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  a.batch_against in(1,2) $companyCond  $booking_no_cond and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2  and f.result=1  and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 $sub_job_cond $batch_num  $sub_buyer_cond $suborder_no  $result_name_cond $year_cond $prod_date_upto  $color_name $shift_name_cond $machine_cond $floor_id_cond  $cbo_prod_type_cond $cbo_prod_source_cond
				GROUP BY  f.load_unload_id,f.process_end_date";
								$result_sub_currMon=sql_select($sql_subcon);
								 $tot_reprocess_qty3=0;
								foreach($result_sub_currMon as $row)
								{
									//$batch_wise_prod_qty_arr2[$row[csf('batch_id')]]['prod_qty'];
									$tot_row=count($row[csf('process_end_date')]);
									$unload_qty_arr3[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')]+$row[csf('trim_wgt')];
									//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
									 $tot_reprocess_qty3+=$row[csf('re_batch_qnty')]+$row[csf('re_trims_wgt')];
									$unload_qty_arr3[$row[csf('load_unload_id')]]['count']+=$tot_row;
									$unload_qty_arr3[$row[csf('load_unload_id')]]['process_end_date'].=$row[csf('process_end_date')].',';
									$unload_date_count_arr[$row[csf('process_end_date')]]=$row[csf('process_end_date')];
								}
								unset($result_sub_currMon);
								 $total_current_mon_qty3=$unload_qty_arr3[2]['qty'];
								 
								 
								 $total_current_mon_qty=$unload_qty_arr2[2]['qty']+$total_current_mon_qty1+$total_current_mon_qty3;
								$total_count=count($unload_date_count_arr);//$total_count1+$unload_qty_arr2[2]['count']+$unload_qty_arr3[2]['count'];
								$total_reprocess_qty=$total_reprocess_qty1+$tot_reprocess_qty2+$tot_reprocess_qty3;
	
									?>
									  <tr bgcolor="#E9F3FF" style="cursor:pointer;">
										<td>Current Month</td>
										<td align="right"><? echo number_format($total_current_mon_qty,0);?></td>
									  </tr>
									  <tr bgcolor="#D8D8D8" style="cursor:pointer;">
										  <td>Avg. Prod. Per Day</td>
										  <td align="right" title="<? echo 'Total Day: '.$total_count;?>"><? echo number_format($total_current_mon_qty/$total_count,0); ?></td>
									  </tr>
									   <tr bgcolor="#D8D8D8" style="cursor:pointer; ">
										  <td>ReProcess Current Month</td>
										  <td align="right"><?   echo number_format($total_reprocess_qty,0);//round( 1040.56789, 4, PHP_ROUND_HALF_EVEN) ?></td>
									  </tr>
									  <tr bgcolor="#D8D8D8" style="cursor:pointer;">
										  <td>Avg. Re-Process Per Day</td>
										  <td align="right" title="<? echo 'Total Day: '.$total_count;?>"><? echo number_format($total_reprocess_qty/$total_count,0); ?></td>
									  </tr>
								</tbody>
								<tfoot>
									<tr>
										<th align="right" colspan="2"> &nbsp;&nbsp;&nbsp;</th>
									</tr>
								</tfoot>
							</table>
				</td>
                <td  valign="top" width="20">&nbsp;  </td>
				<td  valign="top">
							<table cellpadding="0"  width="350" style="margin:10px" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="<? echo $colspan+1; ?>">Production Summary</th>
									</tr>
									<tr>
										<th>Prod. Type </th>
										<?
											foreach ($shift_name as $key => $value) {
												?>
												<th><? echo $value .' Shift'; ?></th>
												<?
											}
											
										 ?>
										<th width="70">Without Shift Qty </th>
										<th>Total Prod. Qty. </th>
									</tr>
								</thead>
								<tbody>
								<?
									 $k=1;$g=1;	$total_batch_qty=$with_out_shift=0;
									// echo count($fabric_type_batch_arr).'ff';
										foreach($fabric_type_batch_arr as $typekey=>$val)
										{
											if ($k%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
											//echo $val['row_shift_qty'];
									  ?>
									   <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
										 
										  <td title="<? echo $typekey; ?>"><?php echo $fabric_type_for_dyeing[$typekey]; ?></td>
										   <?
											$tot_shift_wise=0;
											foreach ($shift_name as $key => $value) {
											$shift_wise_qty=$fabric_shift_batch_arr[$typekey][$key]['all_shift_qty'];
												
												?>
												<td align="right"><? echo number_format($shift_wise_qty,0); ?></td>
												<?
												$tot_shift_arr[$key]+=$shift_wise_qty;
												$tot_shift_wise+=$shift_wise_qty;
											}
											
											if($g==1)
											{
											$tot_colspan=count($fabric_type_batch_arr);
											$with_out_shift=$total_without_fabric_shift_qty;
											
										 ?>
										   <td align="right" width="70" rowspan="<? echo count($fabric_type_batch_arr);?>" title="<? echo $with_out_shift?>"><? echo number_format($with_out_shift,0); ?></td>
										   <?
										   }
										   
										   ?>
										   <td align="right" title=""><? echo number_format($tot_shift_wise,0); ?></td>
									  </tr>
								<? 		$total_batch_qty+=$tot_shift_wise+$with_out_shift;$tot_without_shift_qty=$with_out_shift;
										$k++;$g++;
										}
										 
								?>
								</tbody>
								<tfoot>
									<tr bgcolor="#CCCCCC" style="font-weight:bold;">
									<th  align="right">Total </th>
									 <?
									 $grd_total_shift_qty=0;
										foreach ($shift_name as $key => $value) {
												 $html_summary.="<th  align='right'>".number_format($tot_shift_arr[$key],0,'.','')." </th> ";
												?>
										<th  align="right"><? echo number_format($tot_shift_arr[$key],0,'.','');?> </th>
										<?
										$grd_total_shift_qty+=$tot_shift_arr[$key];
										}
										
										?>
										<th align="right"><b><? echo number_format($tot_without_shift_qty,0,'.','');?></b> </th>
										<th align="right"><b><? echo number_format($grd_total_shift_qty+$tot_without_shift_qty,0,'.','');?></b> </th>
										
									</tr>
								</tfoot>
							</table>
							
						</td>
                        <td  valign="top" width="20">&nbsp;  </td>
						 <td  valign="top">
								 <table cellpadding="0"  style="margin:10px" width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="5">Self & Trims Batch (Shade Match)</th>
									</tr>
									<tr>
										<th>Buyer</th>
										<th>Batch Qty</th>
										<th>Trims Qty</th>
										<th>Total</th>
										<th>%</th>
									</tr>
								</thead>
								<tbody>
								<?
								
								$st=1;$total_batch_qty_shade=0;$tot_batch_per_shade=0;
									//echo array_sum($buyer_re_process_arr);
								foreach($buyer_wise_shadematched_arr as $key=>$val)
								{ 
									 if ($st%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									  <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
										  <td width="30"><? echo $buyer_arr[$key]; ?></td>
										  <td width="30" align="right"><? echo number_format($val["batch_qty"],0); ?></td>
										  <td width="30" align="right"><? echo number_format($val["trim"],0); ?></td>
										   <td width="30" align="right"><? $total_shade=$val["batch_qty"]+$val["trim"];echo number_format($total_shade,0); ?></td>
										  <td align="right" title="Total Shade<? echo $tot_shadeMatchQty;?>"><?  echo number_format(($total_shade/($tot_shadeMatchQty+$total_batch_trims_weight))*100,0).'%'; ?></td>
								  </tr>
								  <?
								  $st++;
								  $total_batch_qty_shade+=number_format($val["batch_qty"],0,".","");
								  $total_batch_qty_trim+=$val["trim"];
								 }
								  ?>
								</tbody>
								<tfoot>
									<tr bgcolor="#CCCCCC" style="font-weight:bold;">
									<th> Total</th>
									 <th width="30" align="right"><? echo number_format($total_batch_qty_shade,0); ?></th>
									  <th width="30" align="right"><? echo number_format($total_batch_qty_trim,0); ?></th>
									 <th width="30" align="right"><? echo number_format($total_batch_qty_trim+$total_batch_qty_shade,0); ?></th>
									 <th width="30" align="right"><? echo '100%'; ?></th>
									</tr>
								</tfoot>
							</table>
					</td>
                    <td  valign="top" width="20">&nbsp;  </td>
					<td  valign="top" >
							 <table style="width:250px;border:1px solid #000;margin:10px" align="center"  border="1" class="rpt_table" rules="all" >
							   <thead>
								 <tr>
								   <th colspan="4">Self & Sample Re Process Summary</th>
								 </tr>
								 <tr>
								   <th>SL</th>
								   <th>Buyer</th>
								   <th>Batch Qty.</th>
                                   <th>Trims Weight</th>
								 </tr>
							   </thead>
							   <tbody>
								 <?
									$k=1;$total_batch_qty_reproc=$total_trim_re_batch_qty_reproc=0;$tot_batch_per_re=0;
									//echo array_sum($buyer_re_process_arr);
								foreach($buyer_re_process_arr as $key=>$val)
								{
	
									 if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									//$trims_qty=$party_batch_arr[$key]['trims_weight'];
									 $trims_qty_sum=$val['trim']; //total_batch_qty_re
									
									?>
								 <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
								   <td><? echo $k; ?></td>
								   <td><? echo $buyer_arr[$key] ?></td>
								   <td align="right" title="Batch Qty"><? echo number_format($val['re_batch_qty'],0,'.',''); $total_batch_qty_reproc+=$val['re_batch_qty']; ?></td>
                                   <td align="right" title="Trims Wgt"><? echo number_format($val['trims_re_batch_qty'],0,'.',''); $total_trim_re_batch_qty_reproc+=$val['trims_re_batch_qty']; ?></td>
								   
								 </tr>
								 <?
								$tot_batch_per_re+=$batch_per;
								$k++;
	
								}
									  ?>
							   </tbody>
							   <tfoot>
								 <tr bgcolor="#CCCCCC" style="font-weight:bold;">
								   <th colspan="2" align="right">Total </th>
								   <th align="right"><b><? echo number_format($total_batch_qty_reproc,0,'.','');?></b></th>
                                   <th align="right"><b><? echo number_format($total_trim_re_batch_qty_reproc,0,'.','');?></b></th>
								  
								 </tr>
							   </tfoot>
							 </table>
							 </td>
                        <td  valign="top" width="20">&nbsp;  </td>
					   <td valign="top"  >
							 <table cellpadding="0"  width="310" style="margin:10px" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
								<thead>
									<tr>
										<th colspan="4">Sub Contract Summary</th>
									</tr>
									<tr>
										<th>Party</th>
										<th>Batch Qty</th>
                                        <th>Re.Pro Batch Qty</th>
                                        <th>Re.Pro Trims Wgt</th>
									</tr>
								</thead>
								<tbody>
										<?
										$total_summary_prod_qty=$total_summary_re_prod_qty=$total_summary_trim_re_prod_qty=0;$ss=1;
										foreach($subcon_summary_arr as $party_id=>$val)
										{
											if($ss%2==0) $subbgcolor="#E9F3FF"; else $subbgcolor="#FFFFFF";
										   ?>
										   <tr bgcolor="<? echo $subbgcolor; ?>"  style="cursor:pointer;">
											<td width="120"><? echo $buyer_arr[$party_id]; ?></td>
											<td width="80"  align="right"><? echo number_format($val['sub_batch_qnty'],0,'.',''); ?></td>
                                            <td width="80"  align="right"><? echo number_format($val['sub_re_batch_qnty'],0,'.',''); ?></td>
                                            <td width="80"  align="right"><? echo number_format($val['sub_re_trim_batch_qnty'],0,'.',''); ?></td>
											
										   </tr>
										   <?
										   $total_summary_prod_qty+=$val['sub_batch_qnty']; 
										   $total_summary_re_prod_qty+=$val['sub_re_batch_qnty'];
										   $total_summary_trim_re_prod_qty+=$val['sub_re_trim_batch_qnty'];
										$ss++;
										}
										?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold;">
											<td align="right">Total</td>
											<td width="80"  align="right"><? echo number_format($total_summary_prod_qty,0,'.',''); ?></td>
                                            <td width="80"  align="right"><?  echo number_format($total_summary_re_prod_qty,0,'.',''); ?></td>
                                            <td width="80"  align="right"><? echo number_format($total_summary_trim_re_prod_qty,0,'.',''); ?></td>
										   
										</tr>
							</tbody>
							</table>
					   </td>
					   <!-- END Total Dyeing Production Summary -->
				  </tr>
				 </table>
			 </div>
			 <br />
			 <?
		}
		 if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		 {
		
			if (count($batch_detail_arr)>0)
			{
				$group_by=str_replace("'",'',$cbo_group_by);
			 ?>
			 <div align="left" style="float:left; clear:both;">
			 <table class="rpt_table" width="1590" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_1">
              <caption> <b>Self batch  </b></caption>
                <thead>
                    <tr>
                        <th width="30">SL</th>
                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                       <th width="100">Buyer</th>
                        <th width="100">F.Booking No.</th>
                        <th width="150">Fabrics Construction</th>
                        <th width="80">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Re-Pro.Qty.</th>
                        <th width="70">Trims Wgt.</th>
                        <th width="70">Water Flow</th>

                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
						<th width="50">Shift</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1610px; overflow-y:scroll; float:left; clear:both;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1590" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
                <?
				$tot_sum_trims_qnty=$grand_total_water_cons_load_qty=$grand_total_re_batch_qty=0;
              $i=1;$sl=1; $btq=0; $k=1;$z=1;$total_water_cons_load=0;$total_water_cons_unload=$grand_total_batch_qty=0;
                $batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();
              foreach($batch_detail_arr as $batch_id=>$batch)
			  {
					if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
					if($group_by!=0)
					{
						if($group_by==1)
						{
							$group_value=$batch[('floor_id')];
							$group_name="Floor";
							$group_dtls_value=$floor_arr[$batch[('floor_id')]];
						}
						else if($group_by==2)
						{
							$group_value=$batch[('shift_name')];
							$group_name="Shift";
							$group_dtls_value=$shift_name[$batch[('shift_name')]];
						}
						else if($group_by==3)
						{
							$group_value=$batch[('machine_id')];
							$group_name="Machine";
							$group_dtls_value=$machine_no_arr[$batch[('machine_id')]];
						}
						
						if (!in_array($group_value,$group_by_arr) )
						{
							if($k!=1)
							{
								$sl=1;
							?>
                                <tr class="tbl_bottom">
                                    <td width="30">&nbsp;</td>
                                    <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>
                                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total :</strong></td>
                                    <td width="70"><? echo number_format($batch_qnty,2); ?></td>
                                    <td width="70"><? echo number_format($re_batch_qnty,2); ?></td>
                                    <td width="40"><? echo number_format($batch_qnty_trims,2); ?></td>
									<td width="70"><? echo number_format($sub_water_cons_load,0); ?></td>
                                    <td width="70"><? //echo number_format($batch_qnty_trims,0); ?></td>
                                    <td width="75" colspan="6">&nbsp;</td>
                                </tr>
								<?
								unset($batch_qnty);unset($batch_qnty_trims);unset($re_batch_qnty);unset($sub_water_cons_load);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="20"><b style="margin-left:0px"><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
							</tr>
							<?
							$group_by_arr[]=$group_value;
							$k++;
						}
					}
					$order_id=$batch[('po_id')];$batch_against=$batch[('batch_against')];
					$batch_weight=$batch[('batch_weight')];
					$water_cons_unload=$batch[('water_flow_meter')];
					$water_cons_load=$water_flow_arr[$batch_id];
					$load_hour_meter=$load_hour_meter_arr[$batch_id];
					//echo $water_cons_load.'=='.$water_cons_unload;
					//$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;
					$fab_cons=rtrim($batch[('item_description')],',');
					$dyeing_prod_qty=$batch_wise_prod_qty_arr[$batch_id][$batch[('fabric_type')]]['prod_qty'];
					
					$batch_no=$batch_id;
				if (!in_array($batch_no,$trims_check_array))
					{ $z++;


						 $trims_check_array[]=$batch_no;
						  $tot_trim_qty=$batch[('total_trims_weight')];
						   $water_cons_load=$water_cons_load;
					}
					else
					{
						 $tot_trim_qty=0; $water_cons_load=0;
					}
					
					if($batch_against==2)
					{
						$re_dyeing_prod=$dyeing_prod_qty;
						$dyeing_prod_qty=0;
					}
					else { $dyeing_prod_qty=$dyeing_prod_qty;$re_dyeing_prod=0;}

					?>
					<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
                        <td width="30"><? echo $sl; ?></td>


                         <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_no_arr[$batch[('machine_id')]]; ?></div></td>
                        <?
						 }
						 if($group_by==2 || $group_by==0){ ?>

                        <? } if($group_by==1 || $group_by==0){ ?>

                        <? } 
						
						 $fab_const=implode(",",array_unique(explode(",",$fab_cons)));
						 $width_dia_types=implode(",",array_unique(explode(",",rtrim($batch[('width_dia_type')],','))));
						
						?>
                       
                       
                        <td width="100"><p><? echo $buyer_arr[$batch[('buyer_name')]]; ?></p></td>
						<td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $batch[('booking_no')]; ?></div></td>

                        <td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $fab_const; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $width_dia_types; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[('color_id')]]; ?></div></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $batch[('batch_no')]; ?></div></td>
                        <td width="40"><p><? echo $batch[('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo number_format($dyeing_prod_qty,2,".","");  ?></td>
                        <td align="right" width="70"><? echo number_format($re_dyeing_prod,2,".","");  ?></td>
                       	<td align="right" width="70"><? echo number_format($tot_trim_qty,0);  ?></td>
                       	<td align="right" width="70"><? echo $water_cons_load;  ?></td>

                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $load_t=$load_hr[$batch_id].':'.$load_min[$batch_id]; echo  ($load_date[$batch_id] == '0000-00-00' || $load_date[$batch_id] == '' ? '' : change_date_format($load_date[$batch_id])).' <br> '.$load_t;
						//echo $batch[csf('id')];
                        ?></div></td>
                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $hr=strtotime($unload_date,$load_t); $min=($batch[('end_minutes')])-($load_min[$batch_id]);
						echo  ($batch[('process_end_date')] == '0000-00-00' || $batch[('process_end_date')] == '' ? '' : change_date_format($batch[('process_end_date')])).'<br>'.$unload_time=$batch[('end_hours')].':'.$batch[('end_minutes')];
						$unloaded_date=change_date_format($batch[('process_end_date')]);
						 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></div></td>
                        <td align="center" width="60">
							<div style="width:60px; word-wrap:break-word;"><?
                            $new_date_time_unload=($unloaded_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($load_date[$batch_id].' '.$load_t.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
                            echo floor($total_time/60).":".$total_time%60;
                            ?>
							</div>
                        </td>
                        <td align="center" width="100" title="<? echo $batch[('fabric_type')];?>"><div style="width:100px; word-wrap:break-word;"><? echo $fabric_type_for_dyeing[$batch[('fabric_type')]]; ?> </div> </td>
						<td align="center" width="50"><p><? echo $shift_name[$batch[('shift_name')]]; ?></p> </td>
                        <td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $dyeing_result[$batch[('result')]]; ?></div> </td>
                         <td align="center"><p><? echo $batch[('remarks')]; ?></p> </td>
					</tr>
					<?
					
					$i++;$sl++;
					$batch_qnty+=$dyeing_prod_qty;
					$re_batch_qnty+=$re_dyeing_prod;
					$sub_water_cons_load+=$water_cons_load;
					$batch_qnty_trims+=$tot_trim_qty;
					$total_water_cons_load+=$water_cons_load;
					$total_water_cons_unload+=$water_cons_unload;
					$tot_trims_qnty+=$tot_trim_qty;
					$trims_summary+=$tot_trim_qty;
					$grand_total_batch_qty+=$dyeing_prod_qty;
					$grand_total_re_batch_qty+=$re_dyeing_prod;
					$grand_total_water_cons_load_qty+=$water_cons_load;
					 //batchdata froeach
			}
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    
                    <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>
                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total: </strong></td>
                    <td width="70"><? echo number_format($batch_qnty,2); ?></td>
                     <td width="70"><? echo number_format($re_batch_qnty,2); ?></td>
                    <td width="40"><? echo number_format($batch_qnty_trims,2); ?></td>
					 <td width="70"><? echo number_format($sub_water_cons_load,0); ?></td>
                    <td width="70"><? //echo number_format($batch_qnty_trims,0); ?></td>
                    <td width="75" colspan="6">&nbsp;</td>
                </tr>
			<? } ?>
			</tbody>
			<table  class="rpt_table" width="1590" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer">
				<tfoot>
                <tr>
					<th width="30">&nbsp;</th>
					 <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? }  ?>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="90" align="right">Grand</th>
					<th width="40" align="right" title="Shade Match=<? echo $tot_shadeMatchQty;?>">Total:</th>
					<th width="70" id="grand_total_td_batch_qty" align="right"><? echo number_format($grand_total_batch_qty,2); ?></th>
                    <th width="70" id="grand_total_td_re_batch_qty" align="right"><? echo number_format($grand_total_re_batch_qty,2); ?></th>
					<th width="70"  id="value_total_batch_trim_qty" align="right"><? echo number_format($tot_trims_qnty,0); ?></th>
					<th width="70"  align="right"><? echo number_format($grand_total_water_cons_load_qty,0); ?></th>
					<th width="75"><? //echo $grand_total_batch_qty;?></th>
					<th width="75"></th>
					<th width="60"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width=""></th>

                </tr>
				</tfoot>
			</table>
		</div>
		</div>
			<br/>
		<? }
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			if(count($subcon_batch_detail_arr)>0)
			{
			$group_by=str_replace("'",'',$cbo_group_by);
		?>
		 	<div align="left" style="float:left; clear:both;">
			 <table class="rpt_table" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_2">
              <caption> <b>SubCon batch  </b></caption>
              <!--working hereeeeeeeeeeeee SubCon batch -->
                <thead>
                    <tr>
                        <th width="30">SL</th>
                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                       <th width="100">Buyer</th>
                        <th width="100">F.Booking No.</th>
                        <th width="150">Fabrics Construction</th>
                        <th width="80">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Re-Pro.Qty.</th>
                        <th width="70">Trims Wgt.</th>
                        <th width="70">Water Flow</th>

                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
						<th width="50">Shift</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1620px; overflow-y:scroll; float:left; clear:both;" id="scroll_body_sub">
            <table class="rpt_table" id="table_body2" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
                <?
				$tot_sum_trims_qnty=0;
              $sub=1;  $i=1; $btq=0; $k=1;$z=1;$sub_total_water_cons_load=$sub_grand_total_re_batch_qty=0;$sub_total_water_cons_unload=0;$sub_grand_total_trim_batch_qty=0;
                $sub_batch_chk_arr=array(); $sub_group_by_arr=array();$sub_tot_trims_qnty=0;$sub_trims_check_array=array();
              foreach($subcon_batch_detail_arr as $batch_id=>$sub_batch)
			  {
				
				
					if ($i%2==0)  $bgcolor_sub_dyeing="#E9F3FF"; else $bgcolor_sub_dyeing="#FFFFFF";
					if($group_by!=0)
					{
						if($group_by==1)
						{
							$group_value=$sub_batch[('floor_id')];
							$group_name="Floor";
							$group_dtls_value=$floor_arr[$sub_batch[('floor_id')]];
						}
						else if($group_by==2)
						{
							$group_value=$batch[('shift_name')];
							$group_name="sub_batch";
							$group_dtls_value=$shift_name[$sub_batch[('shift_name')]];
						}
						else if($group_by==3)
						{
							$group_value=$sub_batch[('machine_id')];
							$group_name="Machine";
							$sub_group_dtls_value=$machine_no_arr[$sub_batch[('machine_id')]];
						}
						if (!in_array($group_value,$sub_group_by_arr) )
						{
							if($k!=1)
							{
								$sub=1;
							?>
                                <tr class="tbl_bottom">
                                    <td width="30">&nbsp;</td>
                                    <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                          <? } ?>

                                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total :</strong></td>
                                    <td width="70"><? echo number_format($sub_batch_qnty,2); ?></td>
                                     <td width="70"><? echo number_format($sub_re_batch_qnty,2); ?></td>
                                    <td width="40"><? echo number_format($sub_batch_qnty_trims,2); ?></td>
									<td width="70"><? echo number_format($subcon_water_cons_load,0); ?></td>
                                    <td width="70"><? //echo number_format($sub_batch_qnty_trims,0); ?></td>
                                    <td width="75" colspan="6">&nbsp;</td>
                                </tr>
								<?
								unset($sub_batch_qnty);unset($sub_re_batch_qnty);unset($sub_batch_qnty_trims);unset($subcon_water_cons_load);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="20"><b style="margin-left:0px"><? echo $group_name; ?> : <? echo $sub_group_dtls_value; ?></b></td>
							</tr>
							<?
							$sub_group_by_arr[]=$group_value;
							$k++;
						}
					}

					$batch_against=$sub_batch[('batch_against')];
					$order_id=$sub_batch[('po_id')];

					$batch_weight=$sub_batch[('batch_weight')]; 
					//$subwater_cons_unload=$sub_batch[('water_flow_meter')];
					//$water_cons_load=$water_flow_arr[$batch_id];
						
					$load_hour_meter=$load_hour_meter_arr[$batch_id];
					//echo $water_cons_load.'=='.$water_cons_unload;
					$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;
					//$desc=explode(",",$sub_batch[('item_description')]);
					$po_number=implode(",",array_unique(explode(",",$sub_batch[('po_number')])));
					$file_no=implode(",",array_unique(explode(",",$sub_batch[('file_no')])));
					$ref_no=implode(",",array_unique(explode(",",$sub_batch[('grouping')])));
					$sub_dyeing_prod_qty=$sub_batch[('sub_batch_qnty')];//$shift_wise_prod_arr[$batch_id][$batch[('fabric_type')]][$prod_id]['prod_qty'];
					$batch_no=$sub_batch[('id')];
				if (!in_array($batch_no,$sub_trims_check_array))
					{ $z++;
						 $sub_trims_check_array[]=$batch_no;
						 $sub_tot_trim_qty=$sub_batch[('total_trims_weight')];
						  $subwater_cons_unload=$subcon_water_flow_arr[$batch_id];
					}
					else
					{
						 $sub_tot_trim_qty=0; $subwater_cons_unload=0;
					}
					if($batch_against==2)
					{
						$sub_re_dyeing_prod_qty=$sub_dyeing_prod_qty;
						$sub_dyeing_prod_qty=0;
					}
					else {$sub_dyeing_prod_qty=$sub_dyeing_prod_qty;$sub_re_dyeing_prod_qty=0;}
					
					$fab_cons=rtrim($sub_batch[('item_description')],',');
					$fab_const=implode(",",array_unique(explode(",",$fab_cons)));
					$width_dia_types=implode(",",array_unique(explode(",",rtrim($sub_batch[('width_dia_type')],','))));
					?>
					<tr bgcolor="<? echo $bgcolor_sub_dyeing; ?>"  id="trsub_<? echo $i; ?>" onClick="change_color('trsub_<? echo $i; ?>','<? echo $bgcolor_sub_dyeing; ?>')">
                        <td width="30"><? echo $sub; ?></td>
                         <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_no_arr[$sub_batch[('machine_id')]]; ?></div></td>
                        <?
						 }
						 if($group_by==2 || $group_by==0){ ?>

                        <? } if($group_by==1 || $group_by==0){ ?>

                        <? } ?>
                       
                        <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $buyer_arr[$sub_batch[('buyer_name')]]; ?></div></td>
						<td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $sub_batch[('booking_no')]; ?></div></td>

                        <td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $fab_const; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $width_dia_types; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$sub_batch[('color_id')]]; ?></div></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $sub_batch[('batch_no')]; ?></div></td>
                        <td width="40"><p><? echo $sub_batch[('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo number_format($sub_dyeing_prod_qty,2);  ?></td>
                         <td align="right" width="70"><? echo number_format($sub_re_dyeing_prod_qty,2);  ?></td>
                       	<td align="right" width="70"><? echo number_format($sub_tot_trim_qty,0);  ?></td>
                       	<td align="right" width="70"><? echo $subwater_cons_unload;  ?></td>

                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $sub_load_t=$subcon_load_hr[$batch_id].':'.$subcon_load_min[$batch_id]; echo  ($subcon_load_date[$batch_id] == '0000-00-00' || $subcon_load_date[$batch_id] == '' ? '' : change_date_format($subcon_load_date[$batch_id])).' <br> '.$sub_load_t;
						//echo $batch[csf('id')];
                        ?></div></td>
                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $hr=strtotime($unload_date,$sub_load_t); $min=($sub_batch[('end_minutes')])-($load_min[$batch_id]);
						echo  ($sub_batch[('process_end_date')] == '0000-00-00' || $sub_batch[('process_end_date')] == '' ? '' : change_date_format($sub_batch[('process_end_date')])).'<br>'.$unload_time=$sub_batch[('end_hours')].':'.$sub_batch[('end_minutes')];
						$unloaded_date=change_date_format($sub_batch[('process_end_date')]);
						 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></div></td>
                        <td align="center" width="60">
							<?
                            $new_date_time_unload=($unloaded_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($subcon_load_date[$batch_id].' '.$sub_load_t.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
                            echo floor($total_time/60).":".$total_time%60;
                            ?>
                        </td>
                        <td align="center" width="100" title="<? echo $sub_batch[('fabric_type')];?>"><div style="width:100px; word-wrap:break-word;"><? echo $fabric_type_for_dyeing[$sub_batch[('fabric_type')]]; ?> </div> </td>
						<td align="center" width="50"><p><? echo $shift_name[$sub_batch[('shift_name')]]; ?></p> </td>
                        <td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $dyeing_result[$sub_batch[('result')]]; ?></div> </td>
                         <td align="center"><p><? echo $sub_batch[('remarks')]; ?></p> </td>
					</tr>
					<?
					if($sub_batch[('result')]==1){
						$sub_tot_trims_qnty+=$sub_tot_trim_qty;
						 }
						  else {
						 $sub_tot_trims_qnty+=0;
						  }
					$i++;$sub++;
					$sub_batch_qnty+=$sub_dyeing_prod_qty;	
					$sub_re_batch_qnty+=$sub_re_dyeing_prod_qty;
					$subcon_water_cons_load+=$subwater_cons_unload;
					$sub_batch_qnty_trims+=$sub_tot_trim_qty;
					$sub_total_water_cons_load+=$subwater_cons_unload;
					$total_water_cons_unload+=$subwater_cons_unload;
					$sub_tot_trims_qnty+=$sub_tot_trim_qty;
					$trims_summary+=$sub_tot_trim_qty;
					$sub_grand_total_batch_qty+=$sub_dyeing_prod_qty;
					$sub_grand_total_re_batch_qty+=$sub_re_dyeing_prod_qty;
					$sub_grand_total_trim_batch_qty+=$sub_tot_trim_qty;
					//$sub_grand_water_cons_load+=$water_cons_load;
					 //batchdata froeach
			  }
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>
                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total: </strong></td>
                    <td width="70"><? echo number_format($sub_batch_qnty,2); ?></td>
                     <td width="70"><? echo number_format($sub_re_batch_qnty,2); ?></td>
                    <td width="40"><? echo number_format($sub_batch_qnty_trims,2); ?></td>
					 <td width="70"><? echo number_format($subcon_water_cons_load,0); ?></td>
                    <td width="70"><? //echo number_format($sub_batch_qnty_trims,0); ?></td>
                    <td width="75" colspan="6">&nbsp;</td>
                </tr>
			<? } ?>
			</tbody>
        </table>
			</div>
			<table class="rpt_table" width="1600" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				 <tfoot>
                <tr>
					<th width="30">&nbsp;</th>
					 <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? }  ?>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="90">Grand</th>
					<th width="40">Total:</th>
					<th width="70"  id="value_total_batch_qty2"><? echo number_format($sub_grand_total_batch_qty,2); ?></th>
                    <th width="70"  id="value_total_re_batch_qty2"><? echo number_format($sub_grand_total_re_batch_qty,2); ?></th>
					<th width="70"  id="value_total_batch_trim_qty2"><? echo number_format($sub_grand_total_trim_batch_qty,0); ?></th>
                    <th width="70"  id="value_total_water"><? echo number_format($sub_total_water_cons_load,0); ?></th>
					
					<th width="75"></th>
					<th width="75"></th>
					<th width="60"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width=""></th>

                </tr>

                
                </tfoot>
			</table>
			</div>
		<?
			}
		} // Sub Cond End

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) // Sample Production
		{
			if (count($samp_batch_detail_arr)>0)
			{
				$group_by=str_replace("'",'',$cbo_group_by);

			 ?>
		 <div align="left" style="float:left; clear:both;">
				
			 <table class="rpt_table" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_header_3">
              <caption> <b> Sample Dyeing Production   </b></caption>
              
                <thead>
                    <tr>
                        <th width="30">SL</th>
                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                       <th width="100">Buyer</th>
                        <th width="100">F.Booking No.</th>
                        <th width="150">Fabrics Desc</th>
                        <th width="80">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Re. Pro.Qty.</th>
                        <th width="70">Trims Wgt.</th>
                        <th width="70">Water Flow</th>

                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
						<th width="50">Shift</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1620px; overflow-y:scroll; float:left; clear:both;" id="scroll_body_samp">
            <table class="rpt_table" id="table_body3" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <tbody>
                <?
				$tot_sum_trims_qnty=0;
              $sampl=1; $i=1; $btq=0; $k=1;$z=1;$total_water_cons_load=$samp_grand_total_re_batch_qty=0;$total_water_cons_unload=0;
                $samp_batch_chk_arr=array(); $group_by_arr=array();$samp_tot_trims_qnty=0;$samp_trims_check_array=array();
              foreach($samp_batch_detail_arr as $batch_id=>$samp_batch)
			  {
				
				
					if ($i%2==0)  $bgcolor_sam_dyeing="#E9F3FF"; else $bgcolor_sam_dyeing="#FFFFFF";
					if($group_by!=0)
					{
						if($group_by==1)
						{
							$group_value=$samp_batch[('floor_id')];
							$group_name="Floor";
							$group_dtls_value=$floor_arr[$samp_batch[('floor_id')]];
						}
						else if($group_by==2)
						{
							$group_value=$samp_batch[('shift_name')];
							$group_name="Shift";
							$group_dtls_value=$shift_name[$samp_batch[('shift_name')]];
						}
						else if($group_by==3)
						{
							$group_value=$samp_batch[('machine_id')];
							$group_name="Machine";
							$samp_group_dtls_value=$machine_no_arr[$samp_batch[('machine_id')]];
						}
						if (!in_array($group_value,$samp_batch_chk_arr) )
						{
							if($k!=1)
							{
								$sampl=1;
							?>
                                <tr class="tbl_bottom">
                                    <td width="30">&nbsp;</td>


                                    <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>


                                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total :</strong></td>
                                    <td width="70"><? echo number_format($samp_batch_qnty,2); ?></td>
                                    <td width="70"><? echo number_format($samp_re_batch_qnty,2); ?></td>
                                    <td width="40"><? echo number_format($samp_batch_qnty_trims,2); ?></td>
									<td width="70"><? echo number_format($sub_samp_water_cons_load,0); ?></td>
                                    <td width="70"><? //echo number_format($sub_samp_water_cons_load,0); ?></td>
                                    <td width="75" colspan="6">&nbsp;</td>
                                </tr>
								<?
								unset($samp_batch_qnty);unset($samp_batch_qnty_trims);unset($samp_re_batch_qnty);unset($sub_samp_water_cons_load);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="20"><b style="margin-left:0px"><? echo $group_name; ?> : <? echo $samp_group_dtls_value; ?></b></td>
							</tr>
							<?
							$samp_batch_chk_arr[]=$group_value;
							$k++;
						}
					}

					$order_id=$batch[('po_id')];
					$batch_weight=$samp_batch[('batch_weight')];
					$water_cons_unload=$samp_batch[('water_flow_meter')];
					$batch_against=$samp_batch[('batch_against')];
					//$water_cons_load=$water_flow_arr[$batch_id];
					$load_hour_meter=$load_hour_meter_arr[$batch_id];
					//echo $water_cons_load.'=='.$water_cons_unload;
					$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;
					$desc=explode(",",$samp_batch[('item_description')]);
					$po_number=implode(",",array_unique(explode(",",$samp_batch[('po_number')])));
					//$dyeing_prod_qty=$shift_wise_prod_arr[$batch_id][$batch[('fabric_type')]][$prod_id]['prod_qty'];
					$samp_dyeing_prod_qty=$batch_wise_prod_qty_arr[$batch_id][$samp_batch[('fabric_type')]]['prod_qty'];
					$batch_no=$samp_batch[('id')];
				if (!in_array($batch_no,$samp_trims_check_array))
					{ $z++;

						 $samp_trims_check_array[]=$batch_no;
						  $samp_tot_trim_qty=$samp_batch[('total_trims_weight')];
						  $samp_water_cons_load=$water_flow_arr[$batch_id];
					}
					else
					{
						 $samp_tot_trim_qty=0; $samp_water_cons_load=0;
					}
					if($batch_against==2)
					{
						$samp_re_dyeing_prod_qty=$samp_dyeing_prod_qty;
						$samp_dyeing_prod_qty=0;
					}
					else {$samp_dyeing_prod_qty=$samp_dyeing_prod_qty;$samp_re_dyeing_prod_qty=0;}
					
					$fab_const=rtrim($samp_batch[('item_description')],',');
					$fab_const=implode(",",array_unique(explode(",",$fab_const)));
					$width_dia_types=implode(",",array_unique(explode(",",rtrim($samp_batch[('width_dia_type')],','))));
					?>
					<tr bgcolor="<? echo $bgcolor_sam_dyeing; ?>"  id="trsam_<? echo $i; ?>" onClick="change_color('trsam_<? echo $i; ?>','<? echo $bgcolor_sam_dyeing; ?>')">
                        <td width="30"><? echo $sampl; ?></td>
                         <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $machine_no_arr[$samp_batch[('machine_id')]]; ?></div></td>
                        <?
						 }
						 if($group_by==2 || $group_by==0){ ?>

                        <? } if($group_by==1 || $group_by==0){ ?>

                        <? } ?>
                       
                        <td width="100"><p><? echo $buyer_arr[$samp_batch[('buyer_name')]]; ?></p></td>
						<td align="center" width="100"><p><? echo $samp_batch[('booking_no')]; ?></p></td>
                        <td width="150"><div style="width:150px; word-wrap:break-word;"><? echo $fab_const; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $width_dia_types; ?></div></td>
                        <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$samp_batch[('color_id')]]; ?></div></td>
                        <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $samp_batch[('batch_no')]; ?></div></td>
                        <td width="40"><p><? echo $samp_batch[('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo number_format($samp_dyeing_prod_qty,2);  ?></td>
                        <td align="right" width="70"><? echo number_format($samp_re_dyeing_prod_qty,2);  ?></td>
                       	<td align="right" width="70"><? echo number_format($samp_tot_trim_qty,0);  ?></td>
                       	<td align="right" width="70"><? echo $samp_water_cons_load;  ?></td>

                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $load_t=$load_hr[$batch_id].':'.$load_min[$batch_id]; echo  ($load_date[$batch_id] == '0000-00-00' || $load_date[$batch_id] == '' ? '' : change_date_format($load_date[$batch_id])).' <br> '.$load_t;
						//echo $batch[csf('id')];
                        ?></div></td>
                        <td width="75"><div style="width:75px; word-wrap:break-word;"><? $hr=strtotime($unload_date,$load_t); $min=($samp_batch[('end_minutes')])-($load_min[$batch_id]);
						echo  ($samp_batch[('process_end_date')] == '0000-00-00' || $samp_batch[('process_end_date')] == '' ? '' : change_date_format($samp_batch[('process_end_date')])).'<br>'.$unload_time=$samp_batch[('end_hours')].':'.$samp_batch[('end_minutes')];
						$unloaded_date=change_date_format($samp_batch[('process_end_date')]);
						 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></div></td>
                        <td align="center" width="60">
						<div style="width:60px; word-wrap:break-word;">	<?
                            $new_date_time_unload=($unloaded_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($load_date[$batch_id].' '.$load_t.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
                            echo floor($total_time/60).":".$total_time%60;
                            ?>
							</div>
                        </td>
                        <td align="center" width="100" title="<? echo $samp_batch[('fabric_type')];?>"><div style="width:100px; word-wrap:break-word;"><? echo $fabric_type_for_dyeing[$samp_batch[('fabric_type')]]; ?> </div> </td>
						<td align="center" width="50"><p><? echo $shift_name[$samp_batch[('shift_name')]]; ?></p> </td>
                        <td align="center" width="100"><div style="width:100px; word-wrap:break-word;"><? echo $dyeing_result[$samp_batch[('result')]]; ?></div> </td>
                         <td align="center"><p><? echo $samp_batch[('remarks')]; ?></p> </td>
					</tr>
					<?
					$i++;$sampl++;
					$samp_batch_qnty+=$samp_dyeing_prod_qty;
					$samp_re_batch_qnty+=$samp_re_dyeing_prod_qty;
					$sub_samp_water_cons_load+=$samp_water_cons_load;
					$samp_batch_qnty_trims+=$samp_tot_trim_qty;
					$total_water_cons_load+=$samp_water_cons_load;
					$total_water_cons_unload+=$water_cons_unload;
					$samp_tot_trims_qnty+=$samp_tot_trim_qty;
					$samp_grand_total_batch_qty+=$samp_dyeing_prod_qty;
					$samp_grand_total_re_batch_qty+=$samp_re_dyeing_prod_qty;
					$samp_grand_water_cons_load_qty+=$samp_water_cons_load;
					 //batchdata froeach
			}
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>
                    
                    <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>
                    <td width="130" colspan="7" style="text-align:right;"><strong>Sub. Total: </strong></td>
                    <td width="70"><? echo number_format($samp_batch_qnty,2); ?></td>
                    <td width="70"><? echo number_format($samp_re_batch_qnty,2); ?></td>
                    <td width="40"><? echo number_format($samp_batch_qnty_trims,2); ?></td>
					 <td width="70"><? echo number_format($sub_samp_water_cons_load,0); ?></td>
                    <td width="70"><? //echo number_format($sub_samp_water_cons_load,0); ?></td>
                    <td width="75" colspan="6">&nbsp;</td>
                </tr>
			<? } ?>
			</tbody>
           

        </table>
			</div>
			<table class="rpt_table" width="1600" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				 <tfoot>
                <tr>
					<th width="30">&nbsp;</th>
					 <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? }  ?>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="90">Grand</th>
					<th width="40">Total:</th>
					<th width="70"  align="right" id="value_total_batch_qty3"><? echo number_format($samp_grand_total_batch_qty,2); ?></th>
                    <th width="70" align="right" id="value_total_re_batch_qty3"><? echo number_format($samp_grand_total_re_batch_qty,2); ?></th>
					<th width="70" align="right" id="value_total_batch_trim_qty3"><? echo number_format($samp_tot_trims_qnty,0); ?></th>
					<th width="70" align="right"> <? echo number_format($samp_grand_water_cons_load_qty,0); ?></th>
					
					<th width="75"></th>
					<th width="75"></th>
					<th width="60"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width=""></th>

                </tr>
                </tfoot>
			</table>
			
			</div>
			<br/>
		<? }
			
		}
		?>
		</div>
		</div>
		<?
	
		foreach (glob("$user_name*.xls") as $filename) 
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
		echo "$total_data****$filename****$report_type";
		//echo "$total_data****$filename****$report_type";
		//==================================================================================end====================
	} //Dyeing Production Machine Wise End	
	exit();
}
//Dyeing Report Reprocess end

if($action=="report_generate_construction_wise")
{
	extract($_REQUEST);
	
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name";
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	
	if($db_type==0) $field_concat2="machine_no as machine_no";
	else if($db_type==2) $field_concat2="machine_no as machine_no";
	// machine_no || '-' || brand as machine_name
	$cbo_prod_type = str_replace("'","",$cbo_prod_type);
	$cbo_party_id = str_replace("'","",$cbo_party_id);
	$report_type = str_replace("'","",$operation);

	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_name);

	$buyer = str_replace("'","",$cbo_buyer_name);
	$date_search_type = str_replace("'","",$search_type);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);
	
	$hide_booking_id = str_replace("'","",$txt_hide_booking_id);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	//echo $company;die;
	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_result_name = str_replace("'","",$cbo_result_name);
	$year = str_replace("'","",$cbo_year);
	
	//echo $cbo_type;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	if ($txt_booking_no!='') $booking_no_cond="  and a.booking_no LIKE '%$txt_booking_no%'"; else $booking_no_cond="";
	if ($hide_booking_id!='') $booking_no_cond.="  and a.booking_no_id in($hide_booking_id) "; else $booking_no_cond.="";

	if ($cbo_prod_type>0 && $cbo_party_id>0) $cbo_prod_type_cond="and f.service_company in($cbo_party_id) "; else $cbo_prod_type_cond="";
	if ($cbo_prod_type>0) $cbo_prod_source_cond="and f.service_source in($cbo_prod_type) "; else $cbo_prod_source_cond="";

	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result='".$cbo_result_name."' ";


	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='".$buyer."' ";
	if ($buyer==0) $buyerdata2=""; else $buyerdata2="  and h.buyer_id='".$buyer."' ";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";

	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";
	if ($working_company==0) $workingCompany_name_cond2=""; else $workingCompany_name_cond2="  and a.working_company_id='".$working_company."' ";
	if ($company==0) $companyCond=""; else $companyCond="  and a.company_id=$company";


	//$buyerdata=($buyer)?' and d.buyer_name='.$buyer : '';
	//$batch_num=($batch_no)?" and a.batch_no='".$batch_no."'" : '';

	//echo $cbo_batch_type;
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
	{
		if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
	
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}

	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num='".$job_number_id."' "; else $sub_job_cond="";
	}
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3)
	{
	if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
	
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}

	//echo $order_no;die;
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	//echo date("Y-n-j", strtotime("first day of previous month"));
	//echo date("Y-n-j", strtotime("last day of previous month"));
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{

			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-m',strtotime($date_to));
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			//$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
			$prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
			if($date_search_type==1)
			{
				$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
			}
		}
		elseif($db_type==2)
		{

			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dateFrom= explode("-",$date_from);
			//echo $dateto[1];
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$prod_date_to=change_date_format($today_date,'','',1);
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
			//$last_day= date("t", strtotime($date_to));
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-M',strtotime($date_to));
		    $prod_last_day=change_date_format($second_month_ldate,'','',1);
			//$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			if($date_search_type==1)
			{
				$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
			}
		}
	}
	if($date_search_type==1)
	{
		$date_type_msg="Dyeing Date";
	}
	else
	{
		$date_type_msg="Insert Date";
	}

	//============================ creating date range =======================
	// $dateRange = new DatePeriod(new DateTime($txt_date_from),new DateInterval('P1D'),new DateTime($txt_date_to));
	function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y') 
	{
	    $dates = array();
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last ) {

	        $dates[] = date($output_format, $current);
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}
	$dateRange = get_date_range($txt_date_from,$txt_date_to);
	// echo print_r($dateRange);die();
	// ==========================================================================
	$construction_arr=array();
	$sql_deter="SELECT a.id, a.construction from lib_yarn_count_determina_mst a where a.status_active=1 and a.is_deleted=0";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];				
		}
	}
	// echo "<pre>"; print_r($construction_arr);die();	
	//================================== MAIN QUERY ===============================
	 $sql="SELECT d.id as JOB_ID,d.job_no as JOB_NO,(b.batch_qnty) AS QNTY, f.process_end_date as DATES,e.detarmination_id as DTR_ID
	 	FROM pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, 
	 	pro_batch_create_mst a,product_details_master e where f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=0 and g.id=f.machine_id and 
	 	a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and e.id=b.prod_id and e.status_active=1 and e.is_deleted=0 and b.po_id=c.id and d.job_no=c.job_no_mst 
	 	and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0
	 	  $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  
	 	  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $cbo_prod_source_cond  $booking_no_cond 
	 	  $cbo_prod_type_cond
			";
	// echo $sql;die();
	$sqlRes = sql_select($sql);
	$dataArray = array();
	$qntyArray = array();
	$poWiseQntyArray = array();
	$jobIdArray = array();
	foreach ($sqlRes as $val) 
	{
		$date = date('d-M-Y',strtotime($val['DATES']));
		$dataArray[$construction_arr[$val['DTR_ID']]]['qty'] += $val['QNTY'];
		$qntyArray[$construction_arr[$val['DTR_ID']]][$date]['qty'] += $val['QNTY'];
		$poWiseQntyArray[$construction_arr[$val['DTR_ID']]][$val['JOB_NO']][$date]['qty'] += $val['QNTY'];
		$jobIdArray[$val['JOB_ID']] = $val['JOB_ID'];
	}
	$jobIds = implode(",", $jobIdArray);
	// echo "<pre>"; print_r($qntyArray);die();

	// ============================== getting rate from budget =================================
	$sqlRate = "SELECT a.JOB_NO,a.lib_yarn_count_deter_id as DETER_ID, b.charge_unit as RATE 
	from wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b 
	where b.cons_process=1 and a.id=b.fabric_description and a.job_id in($jobIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo $sqlRate;
	$sqlRateRes = sql_select($sqlRate);
	$rateArray = array();
	foreach ($sqlRateRes as $val) 
	{
		$rateArray[$val['JOB_NO']][$construction_arr[$val['DETER_ID']]] = $val['RATE'];
	}
	// echo "<pre>"; print_r($rateArray);die();
	//============================= calculate value ============================
	foreach ($poWiseQntyArray as $dtr_id => $dtr_data) 
	{
		foreach ($dtr_data as $job_no => $job_data) 
		{
			foreach ($job_data as $date => $val) 
			{
				$rate = $rateArray[$job_no][$dtr_id];
				$qntyArray[$dtr_id][$date]['value'] += $val['qty']*$rate;
			}
		}
	}
	// echo "<pre>"; print_r($qntyArray);die();	
	$tbl_width=380+count($dateRange)*70;
	// $col_span=25+count($shift_name)*2;
	ob_start();
	?>
	<fieldset style="width:<? echo $tbl_width+20; ?>px;margin: 0 auto;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td colspan="<? echo count($dateRange)+4;?>" align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:18px">Dyeing Production Report-V3</td>
			</tr>
			<tr>
				<td colspan="<? echo count($dateRange)+4;?>" align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
			</tr>
			<tr>
				<td colspan="<? echo count($dateRange)+4;?>" align="center" width="100%" colspan="<? echo $col_span; ?>" class="form_caption" style="font-size:12px" ><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
			</tr>
		</table>
		
		<div id="scroll_body" class="tableFixHead" style="max-height: 250px;overflow-y: auto;width: <? echo $tbl_width+20; ?>px;">
			<table cellpadding="0" cellspacing="0" border="1" width="<? echo $tbl_width; ?>" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="150">Fabric Type/Construction</th>
						<th width="60">Qty/Value</th>
						<?
							foreach ($dateRange as $date_key => $date_val) 
							{
								?>
								<th width="60"><? echo date('d-M',strtotime($date_val)); ?></th>
								<?
							}
						?>
						<th width="80">Total</th>
					</tr>
				</thead>
				<tbody>
					<? 
					$sl = 1;
					$i = 1;
					$grndTotalArray = array();
					$grndTotalQty = 0;
					$grndTotalVal = 0;
					foreach ($dataArray as $dtr_id => $dtr_data) 
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
						?>
						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
							<td valign="middle" rowspan="2"><? echo $sl;?></td>
							<td valign="middle" title="<?echo $dtr_id;?>" rowspan="2"><? echo $dtr_id;?></td>
							<td>Qty</td>
							<?
							$totQnty = 0;
							foreach ($dateRange as $date_key => $date_val) 
							{
								?>
								<td align="right" width="60"><? echo number_format($qntyArray[$dtr_id][$date_val]['qty'],0); ?></td>
								<?
								$totQnty += $qntyArray[$dtr_id][$date_val]['qty'];
								$grndTotalArray[$date_val]['qty'] += $qntyArray[$dtr_id][$date_val]['qty'];
							}
							$i++; // here increment for 2nd row
							?>
							<td align="right"><? echo number_format($totQnty,0); ?></td>
						</tr>
						<tr onClick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
							<td>Value</td>
							<?
							$totVal = 0;
							foreach ($dateRange as $date_key => $date_val) 
							{
								?>
								<td align="right" width="60"><? echo number_format($qntyArray[$dtr_id][$date_val]['value'],2); ?></td>
								<?
								$totVal += $qntyArray[$dtr_id][$date_val]['value'];
								$grndTotalArray[$date_val]['value'] += $qntyArray[$dtr_id][$date_val]['value'];
							}
							?>
							<td align="right"><? echo number_format($totVal,2); ?></td>
						</tr>
						<?
						$sl++;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th rowspan="2"></th>
						<th rowspan="2">Total</th>
						<th>Qnty</th>
						<?
						foreach ($dateRange as $date_key => $date_val) 
						{
							?>
							<th width="60"><? echo number_format($grndTotalArray[$date_val]['qty'],0); ?></th>
							<?
							$grndTotalQty += $grndTotalArray[$date_val]['qty'];
						}
						?>
						<th><? echo number_format($grndTotalQty,0); ?></th>
					</tr>
					<tr>
						<th>Value</th>
						<?
						foreach ($dateRange as $date_key => $date_val) 
						{
							?>
							<th width="60"><? echo number_format($grndTotalArray[$date_val]['value'],2); ?></th>
							<?
							$grndTotalVal += $grndTotalArray[$date_val]['value'];
						}

						?>
						<th><? echo number_format($grndTotalVal,2); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename****$report_type";
	
}

if($action=='batch_fabric_dtls_popup')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name";
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	// machine_no || '-' || brand as machine_name
	$company = str_replace("'","",$cbo_company_name);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$job_number = str_replace("'","",$job_number);
	$job_number_id = str_replace("'","",$job_number_show);
	$batch_no = str_replace("'","",$batch_number_show);
	$color = str_replace("'","",$txt_color);
	$machine=str_replace("'","",$txt_machine_id);
	$floor_name=str_replace("'","",$cbo_floor_id);

	//echo $batch_no;die;
	$batch_number_hidden = str_replace("'","",$batch_number);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_result_name = str_replace("'","",$cbo_result_name);
	$year = str_replace("'","",$cbo_year);
	$shift = str_replace("'","",$cbo_shift_name);
	//echo $cbo_type;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	//$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	//if ($job_number==0) $sub_job_cond=""; else $sub_job_cond="  and d.job_no_prefix_num='".$job_number."' ";
	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result='".$cbo_result_name."' ";
	if ($shift==0) $shift_name_cond=""; else $shift_name_cond="  and f.shift_name='".$shift."' ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and f.machine_id in ( $machine ) ";
	if ($floor_name==0 || $floor_name=='') $floor_id_cond=""; else $floor_id_cond=" and f.floor_id=$floor_name";

	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='".$buyer."' ";
	if ($buyer==0) $buyerdata2=""; else $buyerdata2="  and h.buyer_id='".$buyer."' ";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";

	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";

	//$buyerdata=($buyer)?' and d.buyer_name='.$buyer : '';
	//$batch_num=($batch_no)?" and a.batch_no='".$batch_no."'" : '';

	//echo $cbo_batch_type;
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
	{
		if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
		if ($file_no!='') $file_cond="  and c.file_no=$file_no"; else $file_cond="";
		if ($ref_no!='') $ref_cond="  and c.grouping='$ref_no'"; else $ref_cond="";
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}

	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num='".$job_number_id."' "; else $sub_job_cond="";
	}
	if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3)
	{
	if ($txt_order!='') $order_no="  and c.po_number='$txt_order'"; else $order_no="";
	if ($file_no!='') $file_cond="  and c.file_no=$file_no"; else $file_cond="";
	if ($ref_no!='') $ref_cond="  and c.grouping='$ref_no'"; else $ref_cond="";
		$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	}
	if ($color=="") $color_name=""; else $color_name="  and g.color_name='$color'";
	//echo $order_no;die;
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	//echo date("Y-n-j", strtotime("first day of previous month"));
	//echo date("Y-n-j", strtotime("last day of previous month"));
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{

			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-m',strtotime($date_to));
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
			$prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
		}
		elseif($db_type==2)
		{

			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dateFrom= explode("-",$date_from);
			//echo $dateto[1];
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			//$prod_date_to=change_date_format($today_date,'','',1);
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
			//$last_day= date("t", strtotime($date_to));
			//$last_day= date('d',strtotime($date_to));
			//$last_date=date('Y-M',strtotime($date_to));
		    $prod_last_day=change_date_format($second_month_ldate,'','',1);
			$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
			$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
		}
	}
	$yarn_lot_arr=array();
	if($db_type==0)
	{
		$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!=''  group by a.prod_id, b.po_breakdown_id");
	}
	else if($db_type==2)
	{
		$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot)  as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='0' group by a.prod_id, b.po_breakdown_id");
	}
	foreach($yarn_lot_data as $rows)
	{
		$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
		$yarn_lot_arr[$rows['prod_id']][$rows['po_breakdown_id']]=implode(",",array_unique($yarn_lot));
	}
		//print_r($yarn_lot_arr);
	$load_hr=array();
	$load_min=array();
	$load_date=array();
	$water_flow_arr=array();$load_hour_meter_arr=array();
	$load_time_data=sql_select("select batch_id,water_flow_meter,batch_no,load_unload_id,process_end_date,end_hours,hour_load_meter,end_minutes from pro_fab_subprocess where load_unload_id=1 and entry_form=35 and company_id=$company $unload_batch_cond and status_active=1  and is_deleted=0 ");
	foreach($load_time_data as $row_time)// for Loading time
	{
		$load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
		$load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
		$load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
		$water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
		$load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
	}
	$subcon_load_hr=array();
	$subcon_load_min=array();
	$subcon_load_date=array();$subcon_load_hour_meter_arr=array();
	$subcon_water_flow_arr=array();
	$subcon_load_time_data=sql_select("select batch_id,water_flow_meter,batch_no,load_unload_id,process_end_date,end_hours,hour_load_meter,end_minutes from pro_fab_subprocess where load_unload_id=1 and entry_form=38 and company_id=$company  and status_active=1  and is_deleted=0 ");
	foreach($subcon_load_time_data as $row_time)// for Loading time
	{
		$subcon_load_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
		$subcon_load_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
		$subcon_load_date[$row_time[csf('batch_id')]]=$row_time[csf('process_end_date')];
		$subcon_water_flow_arr[$row_time[csf('batch_id')]]=$row_time[csf('water_flow_meter')];
		$subcon_load_hour_meter_arr[$row_time[csf('batch_id')]]=$row_time[csf('hour_load_meter')];
	}
	$subcon_unload_hr=array();
	$subcon_unload_min=array();
	$subcon_unload_date=array();
	$subcon_unload_time_data=sql_select("select batch_id,batch_no,load_unload_id,production_date,end_hours,end_minutes from pro_fab_subprocess where load_unload_id=2 and entry_form=38 and company_id=$company and status_active=1  and is_deleted=0 ");
	foreach($subcon_load_time_data as $row_time)// for Loading time
	{
	$subcon_unload_hr[$row_time[csf('batch_id')]]=$row_time[csf('end_hours')];
	$subcon_unload_min[$row_time[csf('batch_id')]]=$row_time[csf('end_minutes')];
	$subcon_unload_time_data[$row_time[csf('batch_id')]]=$row_time[csf('production_date')];
	}
	//var_dump($load_hr);

	$m_capacity=array();
	$unload_min=array();
	$machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name order by seq_no ",'id','machine_name');
	$machine_capacity_data=sql_select("select id,prod_capacity as m_capacity  from lib_machine_name where status_active=1  and is_deleted=0 ");
	foreach($machine_capacity_data as $capacity)// for Un-Loading time
	{
		$m_capacity[$capacity[csf('id')]]=$capacity[csf('m_capacity')];
	}

	$sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form=35 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2 $dates_com ");
	$tot_row=1;
	foreach($sql_batch_id as $row_batch)
	{
		if($tot_row!=1) $batch_id.=",";
		$batch_id.=$row_batch[csf('batch_id')];

		$tot_row++;
	}//echo $batch_id;die;
		unset($sql_batch_id);
		$batchIds=chop($batch_id,','); $batchIds_cond="";
		if($db_type==2 && count($tot_row)>990)
		{
			$batchIds_cond=" and (";
			$batchIdsArr=array_chunk(explode(",",$batchIds),990);
			foreach($batchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$batchIds_cond.=" a.id not in($ids) or ";
			}
			$batchIds_cond=chop($batchIds_cond,'or ');
			$batchIds_cond.=")";
		}
		else
		{
			$batchIds_cond=" and a.id not in($batchIds)";
		}
		//echo $batchIds_cond;
	$sub_sql_batch_id=sql_select("select batch_id from  pro_fab_subprocess where entry_form=38 and load_unload_id=2 and status_active=1 and is_deleted=0");
	$k=1;
	foreach($sub_sql_batch_id as $row_batch)
	{
		if($k!=1) $sub_batch_id.=",";
		$sub_batch_id.=$row_batch[csf('batch_id')];

		$k++;
	}
	if($batch_id=="") $batch_id=0;
	if($sub_batch_id=="") $sub_batch_id=0;
	$group_by=str_replace("'",'',$cbo_group_by);
	if($group_by==1)
	{
		$order_by="order by f.floor_id";
		$order_by2="order by floor_id";
	}
	else if($group_by==2)
	{
		$order_by="order by f.shift_name";
		$order_b2y="order by shift_name";
	}
	else if($group_by==3)
	{
		$order_by="order by f.machine_id";
		$order_by2="order by machine_id";
	}
	else
	{
		$order_by="order by f.process_end_date,f.machine_id";
		$order_by2="order by process_end_date,machine_id";
	}

		if($db_type==2)
		{
		 $grp_con="LISTAGG(CAST(c.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number,LISTAGG(CAST(b.po_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id,LISTAGG(CAST(c.grouping AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS grouping,LISTAGG(CAST(c.file_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS file_no";
		  $grp_sub_con="LISTAGG(CAST(c.order_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_number,LISTAGG(CAST(b.po_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS po_id";
		}
		else if($db_type==0)
		{
			 $grp_con="group_concat(distinct c.po_number) AS po_number,group_concat(distinct b.po_id) AS po_id,group_concat(distinct c.grouping) AS grouping,group_concat(distinct c.file_no) AS file_no";
			  $grp_sub_con="group_concat(distinct c.order_no) AS po_number,group_concat(distinct b.po_id) AS po_id";
		}
			//if($db_type==2) $group_con2="LISTAGG(CAST(b.width_dia_type AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS width_dia_type,LISTAGG(CAST(b.item_description AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.id) AS item_description";
			//else if($db_type==0) $group_con2=",group_concat(b.width_dia_type) AS width_dia_type,group_concat(b.item_description) AS item_description";

		if($cbo_type==2)//   For Order Wise Dyeing Production
		{
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
			{
			$sql="(select a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type, $grp_con, d.job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_color g, pro_batch_create_mst a where a.company_id=$company and f.batch_id=a.id $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
			 GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order)
			 union
			 (
			 	select a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,b.item_description, b.prod_id, b.width_dia_type,null as po_number ,null as po_id,null as grouping, null as file_no, null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
    f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no and a.company_id=$company and f.batch_id=a.id  and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond $shift_name_cond $machine_cond $floor_id_cond GROUP BY a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,
              f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter,
              f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks
			 ) $order_by2";
			}
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
			{
				 $sql_subcon="select a.batch_no,a.id, a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS sub_batch_qnty, b.item_description, b.prod_id, b.width_dia_type,$grp_sub_con, d.job_no_prefix_num, d.party_id as buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type, f.result from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where a.company_id=$company and f.batch_id=a.id and a.entry_form=36 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,2)  and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $sub_job_cond $batch_num  $sub_buyer_cond $suborder_no  $result_name_cond $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond
				GROUP BY a.batch_no, a.id,a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, b.item_description,b.prod_id, b.width_dia_type, d.job_no_prefix_num, d.party_id, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,f.result $order_by";
			}

			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) // Sample Production
			{
				  $sql_sam="(select a.booking_without_order,a.booking_no,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.prod_id, b.width_dia_type,$grp_con, d.job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, fabric_type,f.result from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where a.company_id=$company and f.batch_id=a.id $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
			 GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type, d.job_no_prefix_num, d.buyer_name, f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,a.booking_without_order,a.booking_no,c.file_no,c.grouping, f.result )
			 union
			 (
			 select a.booking_without_order,a.booking_no,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description,b.prod_id, b.width_dia_type,null as po_number, null as po_id,null as grouping, null as file_no,null as job_no_prefix_num, h.buyer_id as buyer_name,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.result from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no and a.company_id=$company and f.batch_id=a.id $dates_com  $batch_num  $buyerdata2 $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
			 GROUP BY a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type, f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,a.booking_without_order,a.booking_no, f.result)  $order_by2";
			}
		}

		//$batchdata=sql_select($sql);
		//echo $sql_subcon; die;sql_subcon_ltb

	if($cbo_type==2) // Dyeing Production
	{

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			//echo $sql_subcon;
			$sql_subcon_data=sql_select($sql_subcon);
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) //Batch Aganist - Sample
		{
			//echo $sql_sam;
			$batchdata_sam=sql_select($sql_sam);
		}
	//print_r($sql_subcon_data);
	}



	ob_start();
	if($cbo_type==2) //  Dyeing Production
	{
		?>
		<div>
		<fieldset style="width:1350px;">
		<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
		<?
			//echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
			echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
			?>
		 </div>
		 <?
		 if ($cbo_result_name==1 || $cbo_result_name==0)
		 {
		 ?>
		 <div>
         <table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
							<thead>
								<tr>
									<th colspan="3">Production Summary</th>
								</tr>
								<tr>
									<th>Details </th>
									<th>Prod. Qty. </th>
									<th>%</th>
								</tr>
							</thead>
							<tbody>
							<?
							if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
							if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1 || str_replace("'",'',$cbo_batch_type)==3)
							{
							 $sql="select f.load_unload_id,f.process_end_date,count(f.process_end_date) as row_count,
							 sum(CASE WHEN a.batch_against in(1,2) THEN b.batch_qnty ELSE 0 END) AS batch_qnty,
							 sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_batch_qnty

							  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where a.company_id=$company and f.batch_id=a.id and a.entry_form=0 and  g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $prod_date_upto $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary GROUP BY f.load_unload_id,f.process_end_date  ";
							$sql_datas=sql_select($sql);
							}
							$unload_qty_arr=array();$plan_cycle_time=0;$reprocess_qty_arr=array();$tot_reprocess_qty=0;
							foreach($sql_datas as $row)
							{
								$tot_row=count($row[csf('process_end_date')]);
								$unload_qty_arr[$row[csf('load_unload_id')]]['qty']+=$row[csf('batch_qnty')];
								//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
								 $tot_reprocess_qty+=$row[csf('re_batch_qnty')];
								$unload_qty_arr[$row[csf('load_unload_id')]]['count']+=$tot_row;
							}
							//print_r($unload_qty_arr);
							$total_current_mon_qty=$unload_qty_arr[2]['qty'];
							$total_count=$unload_qty_arr[2]['count'];
							$total_reprocess_qty=$tot_reprocess_qty;

							 $sql_result="select f.fabric_type,f.process_end_date,
							 SUM(b.batch_qnty) AS batch_qnty from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where a.company_id=$company and f.batch_id=a.id and a.entry_form=0 and  g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.fabric_type in(1,4,5,6) $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary GROUP BY f.fabric_type,f.process_end_date  ";
							$sql_result=sql_select($sql_result);
							$fabric_batch_arr=array();
							foreach($sql_result as $row)
							{
								$fabric_batch_arr[$row[csf('fabric_type')]]['qty']+=$row[csf('batch_qnty')];
								//$tot_batch_qty+=$row[csf('batch_qnty')];
							}
							//print_r($fabric_batch_arr);

								?>
								  <tr bgcolor="#E9F3FF" style="cursor:pointer;">

									  <td>Current Month</td>
                                       <td align="right"><? echo number_format($total_current_mon_qty,2);?></td>
									  <td align="right"><?  ?></td>
								  </tr>
                                  <tr bgcolor="#D8D8D8" style="cursor:pointer;">

									  <td>Avg. Prod. Per Day</td>
                                       <td align="right"><? echo number_format($total_current_mon_qty/$total_count,2); ?></td>
									  <td align="right"><? //echo $total_current_mon_qty/$total_count; ?></td>
								  </tr>
                                   <tr bgcolor="#D8D8D8" style="cursor:pointer;">

									  <td>ReProcess Current Month</td>
                                       <td align="right"><? echo number_format($total_reprocess_qty,2); ?></td>
									  <td align="right"><? //echo $total_current_mon_qty/$total_count; ?></td>
								  </tr>
                                  <? $k=1;	$tot_batch_qty=0;

								
								  foreach($fabric_batch_arr as $typekey=>$val)
								  	{
										if ($k%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
										if($typekey==1)
										{
											$fab_type="Cotton";
										}
										else if($typekey==2)
										{
											$fab_type="Polyster";
										}
										else if($typekey==3)
										{
											$fab_type="Lycra";
										}
										else if($typekey==4)
										{
											$fab_type="Both Part";
										}
										else if($typekey==5)
										{
											$fab_type="White";
										}
										else if($typekey==6)
										{
											$fab_type="Wash";
										}
											  ?>
                                   <tr bgcolor="<? echo $bgcolor;?>">
									 <? //print_r($fabric_type_for_dyeing);?>
									  <td><?php echo $fab_type; //$fabric_type_for_dyeing[$typekey];   ?></td>
                                       <td align="right"><? echo number_format($val['qty'],2); ?></td>
									  <td align="right"><? echo number_format(($val['qty']/$total_current_mon_qty)*100,2); ?></td>
								  </tr>
							<? 		$tot_batch_qty+=$val['qty'];
								  	$k++;
									}

							?>
							</tbody>
							<tfoot>
								<tr>
									<th align="right">Total </th>
									<th align="right"><b><? echo number_format($tot_batch_qty,2,'.','');?></b> </th>
									<th align="right"><? echo number_format(($tot_batch_qty/$total_current_mon_qty*100),2,'.','').'%'; ?></th>
								</tr>
							</tfoot>
						</table>

			<table cellpadding="0"  width="1210" cellspacing="0" align="left" >
				 <tr>

						 <!-- kaiyum self n trims batch (shade match)-->

						 <!-- kaiyum subcontact batch (shade match)-->
					 <td width="300">
						 <table cellpadding="0"  width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
							<thead>
								<tr>
									<th colspan="5">Re Process Summary</th>
								</tr>
								<tr>

                                    <th>SL</th>
                                    <th>Buyer</th>
									<th>Batch Total</th>
                                    <th>%</th>

								</tr>
							</thead>
							<tbody>
                            <?
                            	$k=1;$total_batch_qty_reproc=0;$tot_batch_per_re=0;
							foreach($buyer_re_process_arr as $key=>$val)
							{

								 if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//$trims_qty=$party_batch_arr[$key]['trims_weight'];
								 $trims_qty_sum=$val['trims']; //total_batch_qty_re
								?>
								  <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;">
									  <td><? echo $k; ?></td>
									  <td><? echo $buyer_arr[$key] ?></td>
									  <td align="right" title="Batch And Trims Qty"><? echo number_format($val['re_qty']+$trims_qty_sum,2,'.',''); $total_batch_qty_reproc+=$val['re_qty']+$trims_qty_sum; ?></td>
									  <td align="right"><? $batch_per=(($val['re_qty']+$trims_qty_sum)/$total_batch_qty_re)*100; echo number_format($batch_per,2,'.','').'%'; ?></td>
								  </tr>
							<?
							$tot_batch_per_re+=$batch_per;
							$k++;

							}
								  ?>
							</tbody>
                            <tfoot>
								<tr>
									<th colspan="2" align="right">Total </th>
									<th align="left"><b><? echo number_format($total_batch_qty_reproc,2,'.','');?></b> </th>
									<th align="right"><? echo number_format($tot_batch_per_re,2,'.','').'%'; ?></th>
								</tr>
							</tfoot>
						</table>
					 </td>

					 <td width="300">
						 <table cellpadding="0"  width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
							<thead>
								<tr>
									<th colspan="5">Summary Total(Shade Match)</th>
								</tr>
								<tr>
									<th>Self Batch</th>
									<th>Sample Batch</th>
                                    <th>SubCon Batch</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								 <tr bgcolor="#FFFFFF"  style="cursor:pointer;">
									  <td width="30"><? echo $total_batch_qty; ?></td>
                                      <td width="30"><? echo $total_batch_qty_sam; ?></td>
									  <td width="30"><? echo $total_sub_batch_qty; ?></td>
									  <td width="30"><?  echo $grand_total=$total_batch_qty+$total_sub_batch_qty+$total_batch_qty_sam; ?></td>
								  </tr>
								  <tr bgcolor="#E9F3FF">
								   <td><?  echo number_format(($total_batch_qty/$grand_total)*100,2).'%'; ?></td>
                                   <td><?  echo number_format(($total_batch_qty_sam/$grand_total)*100,2).'%'; ?></td>
									  <td><?  echo number_format(($total_sub_batch_qty/$grand_total)*100,2).'%'; ?></td>
									  <td><?  echo '100%'; ?></td>
								  </tr>
							</tbody>
						</table>
					 </td>

				 </tr>
			 </table>
		 </div>
		 <br />
		 <?
		}
		 if ($cbo_result_name!=1 || $cbo_result_name==0)
		 {
		 ?>
		  <div>
          	<table cellpadding="0" style="border:0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="0">

            <tr>
                <th colspan="4"></th>
            </tr>

			</table>
			 <table cellpadding="0"  width="1210" cellspacing="0" align="left" >
				 <tr>
					 <td width="300">
						 <!-- kaiyum Self n Trims Batch (Not Shade Match) -->
					 </td>
                     <td width="50"></td>
					 <td width="300">
                     <td width="300">
						 <!-- kaiyum Sample n Trims Batch(Not Shade Match) -->
					 </td>
					 <td width="50"></td>
					 <td width="300">
						 <!-- kaiyum SubContact Batch (Not Shade Match) -->
					 </td>
                     <td width="50"></td>
					 <td width="300">
						 <table cellpadding="0"  width="300" cellspacing="0" align="center"  class="rpt_table" rules="all" border="1">
							<thead style="display:none">
								<tr>
									<th colspan="5"></th>
								</tr>
								<tr>
									<th></th>
                                    <th></th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody style="display:none">
								 <tr bgcolor="#FFFFFF"  style="cursor:pointer;">
									  <td width="30"><? //echo $total_batch_qty_not; ?></td>
                                      <td width="30"><? //echo $total_batch_qty_not_samp; ?></td>
									  <td width="30"><? //echo $total_sub_batch_qty_not; ?></td>
									  <td width="30"><?  //echo $grand_total_not=$total_batch_qty_not+$total_sub_batch_qty_not+$total_batch_qty_not_samp; ?></td>
								  </tr>
								  <tr bgcolor="#E9F3FF">
								   <td><?  //echo number_format(($total_batch_qty_not/$grand_total_not)*100,2).'%'; ?></td>
                                   <td><?  //echo number_format(($total_batch_qty_not_samp/$grand_total_not)*100,2).'%'; ?></td>
									  <td><?  //echo number_format(($total_sub_batch_qty_not/$grand_total_not)*100,2).'%'; ?></td>
									  <td><? // echo '100%'; ?></td>
								  </tr>
							</tbody>
						</table>
					 </td>
					 <td width="50"></td>
					 <td width="300">
						<!-- kaiyum Summary Total( Not Shade Match) -->
					 </td>
				 </tr>
			 </table>
		 </div>
		 <br/>
		 <?
		 }
		 if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		 {
			if (count($batchdata)>0)
			{
				$group_by=str_replace("'",'',$cbo_group_by);

			 ?>
			 <div align="left">


			 <table class="rpt_table" width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
              <caption> <b>Self batch  </b></caption>
              <!--working hereeeeeeeeeeeee self batch -->
                <thead>
                    <tr>
                        <th width="30">SL</th>


                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>

                        <th width="60">File No</th>
                        <th width="70">Ref. No</th>
                        <th width="100">Buyer</th>

                        <th width="100">Fabrics Desc</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Wgt.</th>



                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
                        <th width="100">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1390px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <tbody>
                <?
                $i=1; $btq=0; $k=1;$z=1;$total_water_cons_load=0;$total_water_cons_unload=0;
                $batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();
                foreach($batchdata as $batch)
				{
					if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
					if($group_by!=0)
					{
						if($group_by==1)
						{
							$group_value=$batch[csf('floor_id')];
							$group_name="Floor";
							$group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
						}
						else if($group_by==2)
						{
							$group_value=$batch[csf('shift_name')];
							$group_name="Shift";
							$group_dtls_value=$shift_name[$batch[csf('shift_name')]];
						}
						else if($group_by==3)
						{
							$group_value=$batch[csf('machine_id')];
							$group_name="machine";
							$group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
						}
						if (!in_array($group_value,$group_by_arr) )
						{
							if($k!=1)
							{
							?>
                                <tr class="tbl_bottom">
                                    <td width="30">&nbsp;</td>


                                    <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>

                                    <td width="60">&nbsp;</td>
                                    <td width="70">&nbsp;</td>
                                    <td width="100">&nbsp;</td>

                                    <td width="100">&nbsp;</td>
                                    <td width="70">&nbsp;</td>
                                    <td width="80">&nbsp;</td>
                                    <td width="130" colspan="8"><strong>Sub. Total : </strong></td>
                                    <td width="70"><? echo number_format($batch_qnty,2); ?></td>
                                    <td width="70"><? //echo number_format($btq,2); ?></td>


                                    <td width="75">&nbsp;</td>
                                    <td width="75">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
								<?
								unset($batch_qnty);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="30"  ><b style="margin-left:900px"><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
							</tr>
							<?
							$group_by_arr[]=$group_value;
							$k++;
						}
					}


					$order_id=$batch[csf('po_id')];

					$batch_weight=$batch[csf('batch_weight')];
					$water_cons_unload=$batch[csf('water_flow_meter')];
					$water_cons_load=$water_flow_arr[$batch[csf('id')]];
					$load_hour_meter=$load_hour_meter_arr[$batch[csf('id')]];
					//echo $water_cons_load.'=='.$water_cons_unload;
					$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

					$desc=explode(",",$batch[csf('item_description')]);
					$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
					$file_no=implode(",",array_unique(explode(",",$batch[csf('file_no')])));
					$ref_no=implode(",",array_unique(explode(",",$batch[csf('grouping')])));

					$batch_no=$batch[csf('id')];
				if (!in_array($batch_no,$trims_check_array))
					{ $z++;


						 $trims_check_array[]=$batch_no;
						  $tot_trim_qty=$batch[csf('total_trims_weight')];
					}
					else
					{
						 $tot_trim_qty=0;
					}

					?>
					<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
                        <td width="30"><? echo $i; ?></td>

                         <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <?
						 }
						 if($group_by==2 || $group_by==0){ ?>

                        <? } if($group_by==1 || $group_by==0){ ?>

                        <? } ?>
                        <td align="center" width="60"><p><? echo $file_no; ?></p></td>
                        <td align="center" width="70"><p><? echo $ref_no; ?></p></td>
                        <td width="100"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>

                        <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $batch[csf('item_description')]; ?></div></td>
                        <td width="70"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
                        <td width="80"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td width="90"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                        <td width="40"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>

                       <td align="right" width="70"><? echo number_format($tot_trim_qty,2);  ?></td>




                        <td width="75"><p><? $load_t=$load_hr[$batch[csf('id')]].':'.$load_min[$batch[csf('id')]]; echo  ($load_date[$batch[csf('id')]] == '0000-00-00' || $load_date[$batch[csf('id')]] == '' ? '' : change_date_format($load_date[$batch[csf('id')]])).' <br> '.$load_t;
						//echo $batch[csf('id')];
                        ?></p></td>
                        <td width="75"><p><? $hr=strtotime($unload_date,$load_t); $min=($batch[csf('end_minutes')])-($load_min[$batch[csf('id')]]);
						echo  ($batch[csf('process_end_date')] == '0000-00-00' || $batch[csf('process_end_date')] == '' ? '' : change_date_format($batch[csf('process_end_date')])).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
						 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></p></td>
                        <td align="center" width="60">
							<?
                            $new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($load_date[$batch[csf('id')]].' '.$load_t.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
							//echo $new_date_time_unload.'=='.$new_date_time_load;
                            echo floor($total_time/60).":".$total_time%60;
                            //echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
                            ?>
                        </td>
                        <td align="center" width="100"><p><?
						if($batch[csf('fabric_type')]==1)
							{
								$fab_type="Cotton";
							}
							else if($batch[csf('fabric_type')]==2)
							{
								$fab_type="Polyster";
							}
							else if($batch[csf('fabric_type')]==3)
							{
								$fab_type="Lycra";
							}
							else if($batch[csf('fabric_type')]==4)
							{
								$fab_type="Both Part";
							}
							else if($batch[csf('fabric_type')]==5)
							{
								$fab_type="White";
							}
							else if($batch[csf('fabric_type')]==6)
							{
								$fab_type="Wash";
							}
						echo $fab_type;//$fabric_type_for_dyeing[$batch[csf('fabric_type')]]; ?></p> </td>
                        <td align="center" width="100"><p><? echo $dyeing_result[$batch[csf('result')]]; ?></p> </td>
                         <td align="center"><p><? echo $batch[csf('remarks')]; ?></p> </td>
					</tr>
					<?
					$i++;
					$batch_qnty+=$batch[csf('batch_qnty')];
					$total_water_cons_load+=$water_cons_load;
					$total_water_cons_unload+=$water_cons_unload;
					$tot_trims_qnty+=$tot_trim_qty;
					$grand_total_batch_qty+=$batch[csf('batch_qnty')];
				} //batchdata froeach
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <td width="30">&nbsp;</td>

                    <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>

                    <td width="60">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="130" colspan="2"><strong>Sub. Total : </strong></td>
                    <td width="70"><? echo number_format($batch_qnty,2); ?></td>
                    <td width="70"><? //echo number_format($btq,2); ?></td>
                    <td width="75">&nbsp;</td>
                    <td width="75">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                     <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
			<? } ?>
			</tbody>
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>

                    <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>

                    <? } ?>
                    <th width="60">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="130" colspan="8"><strong>Trims Total : </strong></th>
                    <th width="70"><? echo number_format($tot_trims_qnty,2); ?></th>
                    <th colspan="10">&nbsp; </th>

                </tr>
                <tr>
                    <th width="30">&nbsp;</th>
                    <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>

                    <th width="60">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80"><? //echo $total_water_cons_load;?></th>
                    <th width="130" colspan="8"><strong>Grand Total : </strong></th>
                    <th width="70"><? echo number_format($grand_total_batch_qty+$tot_trims_qnty,2); ?></th>
                    <th colspan="10"> </th>
                </tr>
            </tfoot>
        </table>
			</div>
			</div>
			<br/>
		<? }
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			if(count($sql_subcon_data)>0)
			{
		?>
		 <div align="left">
		  <div> <b> Subcon batch</b> </div>
		 <table class="rpt_table" width="1890" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_2">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="80">Prod. Date</th>
						<th width="80">M/C No</th>
						<th width="80">Floor</th>
						<th width="80">Shift Name</th>
						<th width="100">Buyer</th>
						<th width="80">Job</th>
						<th width="90">PO No</th>
						<th width="100">Fabrics Desc</th>
						<th width="70">Dia/Width Type</th>
						<th width="80">Color Name</th>
						<th width="90">Batch No</th>
						<th width="40">Extn. No</th>
						<th width="70">Dyeing Qty.</th>
                        <th width="60">Hour Load Meter</th>
                        <th width="60">Hour unLoad Meter</th>
						<th width="60">Total Time</th>
						<th width="50">Lot No</th>
                        <th width="60">Water Loading Flow</th>
                        <th width="60">Water UnLoading Flow</th>
                        <th width="70">Water Cons.</th>
						<th width="75">Load Date & Time</th>
						<th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab Type</th>
						<th>Result</th>
					</tr>
				</thead>
		</table>
		<div style=" max-height:350px; width:1910px; overflow-y:scroll;;" id="scroll_body">
		<table class="rpt_table" id="table_body2" width="1890" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
		<tbody>
					<?
					$i=1;
					$btq=0;$trims_btq_sub=0;
					$batch_chk_arr=array();
					foreach($sql_subcon_data as $batch)
					{
					if ($i%2==0)
					$bgcolor_sub2="#E9F3FF";
					else
					$bgcolor_sub2="#FFFFFF";
					$order_id=$batch[csf('po_id')];
					$color_id=$batch[csf('color_id')];
					$batch_weight_sub=$batch[csf('batch_weight')];
					$water_cons_unload_sub=$batch[csf('water_flow_meter')];
					$water_cons_load_sub=$subcon_water_flow_arr[$batch[csf('id')]];
					$load_hour_meter_sub=$subcon_load_hour_meter_arr[$batch[csf('id')]];
					//echo $water_cons_load.'=='.$water_cons_unload;
					$water_cons_diff_sub=($water_cons_unload_sub-$water_cons_load_sub)/$batch_weight_sub*1000;
					$desc=explode(",",$batch[csf('item_description')]);
					$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));

					//echo $load_hr[$batch[csf('id')]].'ddddd';
					//echo $batch[csf('id')];
					?>
					<tr bgcolor="<? echo $bgcolor_sub2; ?>" id="trsub_<? echo $i; ?>" onClick="change_color('trsub_<? echo $i; ?>','<? echo $bgcolor; ?>')">
						<td width="30"><? echo $i; ?></td>
						<td width="80"><p><? echo ($batch[csf('production_date')] == '0000-00-00' || $batch[csf('production_date')] == '' ? '' : change_date_format($batch[csf('production_date')])); $unload_date=$batch[csf('process_end_date')]; ?></p></td>
						<td  align="center" width="80" title="<? echo $machine_arr[$batch[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
						<td  align="center" width="80" title="<? echo $floor_arr[$batch[csf('floor_id')]];  ?>"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
						<td  align="center" width="80" title="<? echo $shift_name[$batch[csf('shift_name')]];  ?>"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
						<td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
						<td  width="80" title="<? echo  $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
						<td  width="90" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
						<td  width="100" title="<? echo $batch[csf('item_description')];?>"><div style="width:100px; word-wrap:break-word;"><? echo $batch[csf('item_description')]; ?></div></td>
						<td  width="70" title="<? echo  $fabric_typee[$batch[csf('width_dia_type')]]; ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
						<td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
						<td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
						<td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
						<td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>

                          <td width="60" align="right"><p><? echo $load_hour_meter_sub;  ?></p></td>
                           <td width="60" align="right"><p><? echo $batch[csf('hour_unload_meter')];  ?></p></td>
                          <td width="60" align="right"><p><? echo $batch[csf('hour_unload_meter')]-$load_hour_meter_sub;  ?></p></td>
						<td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                        <td width="60"><p><? echo $water_cons_load_sub;  ?></p></td>
                        <td width="60"><p><? echo $water_cons_unload_sub;  ?></p></td>

                        <td align="right" width="70" title="<? echo number_format($water_cons_diff_sub,2);  ?>"><? echo number_format($water_cons_diff_sub,2);  ?></td>


						<td width="75" title="<?  $sub_load_t=$subcon_load_hr[$batch[csf('id')]].':'.$subcon_load_min[$batch[csf('id')]];  echo ($subcon_load_date[$batch[csf('id')]] == '0000-00-00' || $subcon_load_date[$batch[csf('id')]]== '' ? '' : change_date_format($subcon_load_date[$batch[csf('id')]])).'  & '.$sub_load_t; ?>"><p><?   $sub_load_t=$subcon_load_hr[$batch[csf('id')]].':'.$subcon_load_min[$batch[csf('id')]]; echo  ($subcon_load_date[$batch[csf('id')]] == '0000-00-00' || $subcon_load_date[$batch[csf('id')]]== '' ? '' : change_date_format($subcon_load_date[$batch[csf('id')]])).' <br> '.$sub_load_t;
						 ?></p></td>
						<td width="75" title="<? $hr=strtotime($unload_date,$load_t); $min=($batch[csf('end_minutes')])-($load_min[$batch[csf('id')]]); echo  $unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; ?>"><p><? $hr=strtotime($unload_date,$load_t); $min=($batch[csf('end_minutes')])-($load_min[$batch[csf('id')]]); echo ($batch[csf('process_end_date')] == '0000-00-00' || $batch[csf('process_end_date')]== '' ? '' : change_date_format($batch[csf('process_end_date')])).' &amp;'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')]; // $unloadd=change_date_format($batch[csf('process_end_date')]).' &amp;'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></p></td>
						<td align="center" width="60">
					   <?
						$new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
						$new_date_time_load=($subcon_load_date[$batch[csf('id')]].' '.$sub_load_t.':'.'00');
						$total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
						echo floor($total_time/60).":".$total_time%60;
						//echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
							?>
						 </td>
                         <td align="center" width="100"><p><?
						 if($batch[csf('fabric_type')]==1)
							{
								$fab_type="Cotton";
							}
							else if($batch[csf('fabric_type')]==2)
							{
								$fab_type="Polyster";
							}
							else if($batch[csf('fabric_type')]==3)
							{
								$fab_type="Lycra";
							}
							else if($batch[csf('fabric_type')]==4)
							{
								$fab_type="Both Part";
							}
							else if($batch[csf('fabric_type')]==5)
							{
								$fab_type="White";
							}
							else if($batch[csf('fabric_type')]==6)
							{
								$fab_type="Wash";
							}
						 echo $fab_type;//$fabric_type_for_dyeing[$batch[csf('fabric_type')]]; ?></p> </td>
						<td align="center"><p><? echo $dyeing_result[$batch[csf('result')]]; ?></p> </td>
					</tr>
		<?
			$i++;
			$btq_sub+=$batch[csf('sub_batch_qnty')];
			$trims_btq_sub+=$batch[csf('total_trims_weight')];
		} //batchdata froeach
		 ?>
			</tbody>
		</table>
		<table class="rpt_table" width="1890" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
				<thead>

                    <tr>
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="40">Total Trims</th>
						<th width="70"><? echo number_format($trims_btq_sub,2); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70"><? //echo number_format($btq_sub,2); ?></th>

						<th width="75">&nbsp;</th>
						<th width="75">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
                    <tr>
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="40">Grand Total</th>
						<th width="70"><? echo number_format($btq_sub+$trims_btq_sub,2); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						 <th width="60">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70"><? //echo number_format($btq_sub,2); ?></th>

						<th width="75">&nbsp;</th>
						<th width="75">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
		</table>
		</div>
		</div>
		<?
		}
		} // Sub Cond End

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) // Sample Production
		{
			if (count($batchdata_sam)>0)
			{
				$group_by=str_replace("'",'',$cbo_group_by);

			 ?>
			 <div align="left">
			 <div> <b>Sample Dyeing Production </b></div>
			 <table class="rpt_table" width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_3">
                <thead>
                    <tr>
                        <th width="30">SL</th>

                         <? if($group_by==3 || $group_by==0){ ?>
                        <th width="80">M/C No</th>
                        <? }
						?>
                        <th width="70">File No</th>
                        <th width="80">Ref. No</th>
                        <th width="100">Buyer</th>


                        <th width="100">Fabrics Desc</th>
                        <th width="70">Dia/ Width Type</th>
                        <th width="80">Color Name</th>

                        <th width="90">Batch No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Qty.</th>

                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fabrics<br>Type</th>
                        <th width="100">Result</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1390px; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body3" width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <tbody>
                <?
                $i=1; $btq=0; $k=1;$z=1;$grand_total_batch_qty_sam=0;$batch_qnty_sam=0;$total_trim_qty=0;
                $batch_chk_arr=array(); $group_by_arr=array();$trims_check_array=array();
                foreach($batchdata_sam as $batch)
				{
					if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";
					if($group_by!=0)
					{
						if($group_by==1)
						{
							$group_value=$batch[csf('floor_id')];
							$group_name="Floor";
							$group_dtls_value=$floor_arr[$batch[csf('floor_id')]];
						}
						else if($group_by==2)


						{
							$group_value=$batch[csf('shift_name')];
							$group_name="Shift";
							$group_dtls_value=$shift_name[$batch[csf('shift_name')]];
						}
						else if($group_by==3)
						{
							$group_value=$batch[csf('machine_id')];
							$group_name="machine";
							$group_dtls_value=$machine_arr[$batch[csf('machine_id')]];
						}
						if (!in_array($group_value,$group_by_arr) )
						{
							if($k!=1)
							{
							?>
                                <tr class="tbl_bottom">
                                    <td width="30">&nbsp;</td>

                                     <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <?
									 }
									 ?>
                                    <td width="70">&nbsp;</td>
                                    <td width="80">&nbsp;</td>
                                    <td width="100">&nbsp;</td>


                                    <td width="100">&nbsp;</td>
                                    <td width="70">&nbsp;</td>
                    				<td width="80">&nbsp;</td>
                                    <td width="130" colspan="2"><strong>Sub. Total : </strong></td>
                                    <td width="70"><? echo number_format($batch_qnty_sam,2); ?></td>
                                    <td width="60">&nbsp;</td>


                                    <td width="75">&nbsp;</td>
                                    <td width="75">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
                                    <td width="100">&nbsp;</td>

                                    <td>&nbsp;r</td>
                                </tr>
								<?
								unset($batch_qnty_sam);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="28" align="left" ><b style="margin-left:900px"><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
							</tr>
							<?
							$group_by_arr[]=$group_value;
							$k++;
						}
					}
					$order_id=$batch[csf('po_id')];
					$batch_weight=$batch[csf('batch_weight')];
					$water_cons_unload=$batch[csf('water_flow_meter')];
					$water_cons_load=$water_flow_arr[$batch[csf('id')]];
					$hour_meter_load=$load_hour_meter_arr[$batch[csf('id')]];
					//echo $water_cons_load.'=='.$water_cons_unload;
					$water_cons_diff=($water_cons_unload-$water_cons_load)/$batch_weight*1000;

					$desc=explode(",",$batch[csf('item_description')]);
					$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
					$file_no=implode(",",array_unique(explode(",",$batch[csf('file_no')])));
					$ref_no=implode(",",array_unique(explode(",",$batch[csf('grouping')])));
					$batch_no=$batch[csf('id')];
				if (!in_array($batch_no,$trims_check_array))
					{ $z++;


						 $trims_check_array[]=$batch_no;
						  $tot_trim_qty=$batch[csf('total_trims_weight')];
					}
					else
					{
						 $tot_trim_qty=0;
					}
					?>
					<tr bgcolor="<? echo $bgcolor_dyeing; ?>"  id="trsam_<? echo $i; ?>" onClick="change_color('trsam_<? echo $i; ?>','<? echo $bgcolor_dyeing; ?>')">
                        <td width="30"><? echo $i; ?></td>

                          <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <? } if($group_by==2 || $group_by==0){ ?>

                        <? } ?>
                        <td width="70"><p><? echo $file_no; ?></p></td>
                        <td width="80"><p><? echo $ref_no; ?></p></td>
                        <td width="100"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>


                        <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $batch[csf('item_description')]; ?></div></td>
                        <td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></div></td>
                        <td width="80"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td width="90"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                        <td width="40"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                        <td align="right" width="70"><? echo number_format($tot_trim_qty,2);  ?></td>



                        <td width="75"><p><? $load_t=$load_hr[$batch[csf('id')]].':'.$load_min[$batch[csf('id')]]; echo  ($load_date[$batch[csf('id')]] == '0000-00-00' || $load_date[$batch[csf('id')]] == '' ? '' : change_date_format($load_date[$batch[csf('id')]])).' <br> '.$load_t;
						//echo $batch[csf('id')];
                        ?></p></td>
                        <td width="75"><p><? $hr=strtotime($unload_date,$load_t); $min=($batch[csf('end_minutes')])-($load_min[$batch[csf('id')]]);
						echo  ($batch[csf('process_end_date')] == '0000-00-00' || $batch[csf('process_end_date')] == '' ? '' : change_date_format($batch[csf('process_end_date')])).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
						 //$unloadd=change_date_format($batch[csf('process_end_date')]).'<br>'.$unload_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];?></p></td>
                        <td align="center" width="60">
							<?
                            $new_date_time_unload=($unload_date.' '.$unload_time.':'.'00');
                            $new_date_time_load=($load_date[$batch[csf('id')]].' '.$load_t.':'.'00');
                            $total_time=datediff(n,$new_date_time_load,$new_date_time_unload);
							//echo $new_date_time_unload.'=='.$new_date_time_load;
                            echo floor($total_time/60).":".$total_time%60;
                            //echo ($total_time/60 - $total_time%3600/60)/60 .':'.$total_time%3600/60;
                            ?>
                        </td>
                          <td align="center" width="100"><p><?
						 if($batch[csf('fabric_type')]==1)
							{
								$fab_type="Cotton";
							}
							else if($batch[csf('fabric_type')]==2)
							{
								$fab_type="Polyster";
							}
							else if($batch[csf('fabric_type')]==3)
							{
								$fab_type="Lycra";
							}
							else if($batch[csf('fabric_type')]==4)
							{
								$fab_type="Both Part";
							}
							else if($batch[csf('fabric_type')]==5)
							{
								$fab_type="White";
							}
							else if($batch[csf('fabric_type')]==6)
							{
								$fab_type="Wash";
							}
						  echo $fab_type;//$fabric_type_for_dyeing[$batch[csf('fabric_type')]]; ?></p> </td>
                        <td width="100" align="center"><p><? echo $dyeing_result[$batch[csf('result')]]; ?></p> </td>
                         <td align="center" title="<?  echo $batch[csf('remarks')];   ?>"><p><? echo $batch[csf('remarks')]; ?></p> </td>
					</tr>
					<?
					$i++;
					$batch_qnty_sam+=$batch[csf('batch_qnty')];
					$total_trim_qty+=$tot_trim_qty;
					$grand_total_batch_qty_sam+=$batch[csf('batch_qnty')];
				} //batchdata froeach
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <th width="30">&nbsp;</th>

                    <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } if($group_by==2 || $group_by==0){ ?>

                    <? } ?>
                     <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>


                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="130" colspan="2"><strong>Sub. Total : </strong></th>
                    <th width="70"><? echo number_format($batch_qnty_sam,2); ?></th>
                  	<th width="60">&nbsp;</th>


                    <th width="75">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                     <th width="100">&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
			<? } ?>
			</tbody>
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>

                     <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } if($group_by==2 || $group_by==0){ ?>

                    <? } ?>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>


                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="130" colspan="2"><strong>Grand Total : </strong></th>
                    <th width="70"><? echo number_format($grand_total_batch_qty_sam,2); ?></th>
                    <th width="60"><? echo number_format($total_trim_qty,2);?></th>


                    <th width="75">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                     <th width="100">&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
			</div>
			</div>
			<br/>
		<? }
			?>
            <?
		}
		?>
		</fieldset>
		</div>
		<?
		} //Dyeing Production End

	exit();
}
?>