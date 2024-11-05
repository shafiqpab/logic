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

// ================================Print button ==============================

// if($action=="print_button_variable_setting")
// {

//     $print_report_format=0;
//     $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=136 and is_deleted=0 and status_active=1");
// 	$printButton=explode(',',$print_report_format);

// 	 foreach($printButton as $id){
// 		if($id==710)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px" value="Batch Wise" onClick="fn_dyeing_report_generated(2)" />';
// 		if($id==108)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_dyeing_report_generated(1)" />';
// 		if($id==267)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Report3" onClick="fn_dyeing_report_generated(3)" />';
// 		if($id==711)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:150px" value="Machine Wise W/C Report" onClick="fn_dyeing_report_generated(4)" />';
// 		if($id==712)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show5" onClick="fn_dyeing_report_generated(5)" />';
// 		if($id==750)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px" value="Batch Wise 2" onClick="fn_dyeing_report_generated(6)" />';
// 		if($id==161)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px" value="Report 6" onClick="fn_dyeing_report_generated(7)" />';
// 		if($id==758)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:80px" value="Report 7" onClick="fn_dyeing_report_generated(8)" />';
// 	 }
// 	 echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";

//     exit();
// }
// ======================= End Print button =================================================

//--------------------------------------------------------------------------------------------------------------------

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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'dyeing_production_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
	echo load_html_head_contents("Machine Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	$txt_process_id=$im_data[2];
?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array; var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}
		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}
		function set_all() {
			var old = document.getElementById('txt_process_row_id').value;
			if (old != "") {
				old = old.split(",");
				for (var k = 0; k < old.length; k++) {
					js_set_value(old[k]);
				}
			}
		}
		function js_set_value(str) {


			 toggle(document.getElementById('search' + str), '#FFFFCC');

			 if (jQuery.inArray($('#txt_mc_id' + str).val(), selected_id) == -1) {
			 	selected_id.push($('#txt_mc_id' + str).val());
			 	selected_name.push($('#txt_mc_name' + str).val());
			 }
			 else {
			 	for (var i = 0; i < selected_id.length; i++) {
			 		if (selected_id[i] == $('#txt_mc_id' + str).val()) break;
			 	}
			 	selected_id.splice(i, 1);
			 	selected_name.splice(i, 1);
			 }

			 var id = '';
			 var name = '';
			 for (var i = 0; i < selected_id.length; i++) {
			 	id += selected_id[i] + ',';
			 	name += selected_name[i] + ',';
			 }

			 id = id.substr(0, id.length - 1);
			 name = name.substr(0, name.length - 1);

			 $('#hid_machine_id').val(id);
			 $('#hid_machine_name').val(name);
		}
		function window_close(){
	     	var old = document.getElementById('hid_machine_id').value;
			parent.emailwindow.hide();
		}



	
    </script>
</head>
<input type="hidden" name="hid_machine_id" id="hid_machine_id" />
<input type="hidden" name="hid_machine_name" id="hid_machine_name" />
<?
	
	$company=$im_data[0];
	$w_company=$im_data[1];
	$lc_company="";
	if($company)
	{
		$lc_company="and a.company_id in($company)";
	}
	$wc_company="";
	if($w_company)
	{
		$wc_company="and a.company_id in($w_company)";
	}
	
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=2 and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 $wc_company $lc_company order by a.machine_no, b.floor_name ";
	$sql_arr=sql_select($sql);
	//echo  $sql;

	//echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;

		?>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
				<table class="rpt_table" width="430" cellspacing="0" cellpadding="0" border="0" rules="all" style="position: sticky; top: 0;" >
					<thead>
						<tr>			
					        <th width="50">SL No</th>                
					        <th width="150">Machine Name</th>
					        <th width="130">Machine Group</th>
					        <th>Floor Name</th>
					    </tr>
					</thead>
				</table>
			   <!-- <div id="" style="max-height:300px; width:<? echo $table_width2; ?>px; overflow-y:scroll"> -->
			   <table id="tbl_list_search" class="rpt_table" width="430" height="" cellspacing="0" cellpadding="0" border="1" rules="all" style="max-height:300px; overflow-y:scroll">
			        <tbody>
				       	<?
				        $i=1;
				        $process_row_id = '';
				        $hidden_process_id = explode(",", $txt_process_id);
						foreach($sql_arr as $ids=> $row)
						{
							
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";


							if (in_array($row[csf('id')], $hidden_process_id)) {
								if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
							}


							?>
				     			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">


					                <td width="50"><? echo $i; ?>
					                	<input type="hidden" name="txt_mc_id" id="txt_mc_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
										<input type="hidden" name="txt_mc_name" id="txt_mc_name<?php echo $i ?>" value="<? echo $row[csf('machine_no')]; ?>"/>
					                </td>
					                <td width="150" align="center"><? echo $row[csf('machine_no')]; ?></td>
					                <td width="130" align="center"><? echo $row[csf('machine_group')]; ?></td>
					                <td align="center"><? echo $row[csf('floor_name')]; ?></td>
					            </tr>

							<?
						    $i++;
					    }

				 		?>
			        </tbody>
			        	<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
			    </table>

				<table width="430" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%">
								<div style="width:50%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all"
									onClick="check_all_data()"/>
									Check / Uncheck All
								</div>
								<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="window_close()"
									class="formbutton" value="Close" style="width:100px"/>
								</div>
							</div>
						</td>
					</tr>
				</table>



		    </fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
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

if($action=="dyeing_production_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name";
	else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
	$company = str_replace("'","",$cbo_company_name);
	$working_company = str_replace("'","",$cbo_working_company_name);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$cbo_prod_type = str_replace("'","",$cbo_prod_type);
	$cbo_party_id = str_replace("'","",$cbo_party_id);

	$date_search_type = str_replace("'","",$search_type);
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
	$hide_booking_id = str_replace("'","",$txt_hide_booking_id);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$year = str_replace("'","",$cbo_year);
	$shift = str_replace("'","",$cbo_shift_name);
	//echo $cbo_type;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="and d.party_id='".$buyer."' ";
	if ($cbo_prod_type>0 && $cbo_party_id>0) $cbo_prod_type_cond="and f.service_company in($cbo_party_id) "; else $cbo_prod_type_cond="";
	if ($cbo_prod_type>0) $cbo_prod_source_cond="and f.service_source in($cbo_prod_type) "; else $cbo_prod_source_cond="";
	//echo $cbo_prod_type;
	if ($cbo_result_name==0) $result_name_cond=""; else $result_name_cond="  and f.result in ( $cbo_result_name ) ";
	if ($shift==0) $shift_name_cond=""; else $shift_name_cond="  and f.shift_name='".$shift."' ";
	if ($machine=="") $machine_cond=""; else $machine_cond =" and f.machine_id in ( $machine ) ";
	if ($floor_name==0 || $floor_name=='') $floor_id_cond=""; else $floor_id_cond=" and f.floor_id=$floor_name";

	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='".$buyer."' ";
	if ($buyer==0) $buyerdata2=""; else $buyerdata2="  and h.buyer_id='".$buyer."' ";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".trim($batch_no)."' ";

	if ($batch_no=="") $unload_batch_cond=""; else $unload_batch_cond="  and batch_no='".trim($batch_no)."' ";
	if ($batch_no=="") $unload_batch_cond2=""; else $unload_batch_cond2="  and f.batch_no='".trim($batch_no)."' ";

	if ($working_company==0) 
	{ 
		$workingCompany_name_cond2=""; $workingCompany_name_condSub=""; 
	}
	else { 
	 	$workingCompany_name_cond2="  and a.working_company_id=".$working_company." ";
	 	$workingCompany_name_condSub="  and a.company_id=".$working_company." ";
	}

	if ($company==0) 
	{ 
	$companyCond=""; $companyCondSub=""; 
	}
	else 
	{
		$companyCond="  and a.company_id=$company";
		$companyCondSub="  and a.company_id=$company";
	}

	// if ($working_company==0) $workingCompany_name_cond2=""; else $workingCompany_name_cond2="  and a.working_company_id='".$working_company."' ";
	// if ($company==0) $companyCond=""; else $companyCond="  and a.company_id=$company";

	//echo $cbo_batch_type;
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if(trim($txt_booking_no)!="") $ext_no_search="%".trim($txt_booking_no)."%"; else $ext_no_search="%%";

	if ($txt_booking_no!='') $booking_no_cond="  and a.booking_no LIKE '%$txt_booking_no%'"; else $booking_no_cond="";
	if ($hide_booking_id!='') $booking_id_cond="  and a.booking_no_id in($hide_booking_id) "; else $booking_id_cond="";

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
	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{

			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');

			$start_dd= strtotime(change_date_format($date_from,"yyyy-mm-dd","-"));
			$end_dd= strtotime(change_date_format($date_from,"yyyy-mm-dd","-"));
			$start_d=date("Y-m-d",$start_dd);
			$end_d=date("Y-m-d",$end_dd);
			$date_from2 = strtotime($start_d);
			$date_to2 = strtotime($end_d);
			$date_start = date("Y-m-d",strtotime("-5 day", $date_from2));
			$date_end = date("Y-m-d",strtotime("+5 day", $date_to2));
			$date_start= change_date_format($date_start,"yyyy-mm-dd","-",1);
			$date_end= change_date_format($date_end,"yyyy-mm-dd","-",1);

			$second_month_ldate=date("Y-m-t",strtotime($date_to));
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$prod_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
			$prod_date_upto=" and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day' ";
			if($date_search_type==1)
			{
				$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
				$dates_com2=" and  f.process_end_date BETWEEN '$date_start' AND '$date_end' ";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
				$dates_com2=" and f.insert_date between '".$date_start."' and '".$date_end." 23:59:59' ";
			}


		}
		elseif($db_type==2)
		{

			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);

			$start_dd= strtotime(change_date_format($date_from,"dd-mm-yyyy","-"));
			$end_dd= strtotime(change_date_format($date_from,"dd-mm-yyyy","-"));
			$start_d=date("Y-m-d",$start_dd);
			$end_d=date("Y-m-d",$end_dd);
			$date_from2 = strtotime($start_d);
			$date_to2 = strtotime($end_d);
			$date_start = date("Y-m-d",strtotime("-5 day", $date_from2));
			$date_end = date("Y-m-d",strtotime("+5 day", $date_to2));
			$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
			$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);

			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
		    $prod_last_day=change_date_format($second_month_ldate,'','',1);
			$prod_date_upto="and  f.process_end_date BETWEEN '$fromdate' AND '$prod_last_day'";
			if($date_search_type==1)
			{
				$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
				$dates_com2="and  f.process_end_date BETWEEN '$date_start' AND '$date_end'";
			}
			else
			{
				$dates_com=" and f.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
				$dates_com2=" and f.insert_date between '".$date_start."' and '".$date_end." 11:59:59 PM' ";

			}

		}
	}

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

	if($db_type==0)
	{
	
		if($cbo_type==2)//   For Order Wise Dyeing Production
		{
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
			{
				$sql="SELECT f.insert_date,a.batch_no,a.id, a.batch_weight,a.booking_no, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, group_concat( distinct c.po_number) AS po_number,c.grouping,c.file_no, d.job_no as job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.remarks, f.result  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $cbo_prod_type_cond $cbo_prod_source_cond $booking_no_cond $booking_id_cond  and a.entry_form=0 and  g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0
				group by a.batch_no, a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.job_no, d.buyer_name,c.grouping,c.file_no, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.remarks, f.result,f.insert_date $order_by";
			}
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
			{
			 $sql_subcon="select f.insert_date,a.batch_no,a.id, a.batch_weight,a.booking_no, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS sub_batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, group_concat(c.order_no) AS po_number, d.subcon_job as job_no_prefix_num, d.party_id as buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.result from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where  f.batch_id=a.id and a.entry_form=36 and g.id=a.color_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,2)  and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $dates_com $sub_job_cond $batch_num $sub_buyer_cond  $workingCompany_name_cond2 $companyCond $suborder_no $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $booking_no_cond $booking_id_cond  GROUP BY a.batch_no, a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.party_id , f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.result,f.insert_date $order_by";
			}
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) // Batch Against- Sample
			{
				$sql_sam="(select f.insert_date,a.batch_no,a.id, a.batch_weight,a.booking_no, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, group_concat( distinct c.po_number) AS po_number, d.job_no as job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where  f.batch_id=a.id $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $file_cond $ref_cond $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $booking_no_cond $booking_id_cond  and a.entry_form=0 and  g.id=a.color_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
				group by a.batch_no, a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.result,f.insert_date)
			union
			 (
			 select f.insert_date,a.batch_no, a.id,a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, 'null', 'null', 'null', f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.result from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where f.batch_id=a.id $companyCond $workingCompany_name_cond2  $dates_com  $batch_num  $buyerdata2 $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $booking_no_cond $booking_id_cond  and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
			 GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no, b.item_description, b.po_id, b.prod_id, b.width_dia_type, f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type, f.result,f.insert_date)  $order_by2";
			}
		}
	}
	else if($db_type==2)// Oracle start here
	{
		
		if($cbo_type==2)//   For Order Wise Dyeing Production
		{
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
			{

			 $sql="(SELECT f.insert_date,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, LISTAGG(CAST(c.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY c.po_number) AS po_number,c.grouping,c.file_no, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order, f.ltb_btb_id from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_color g, pro_batch_create_mst a where f.batch_id=a.id $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $cbo_prod_type_cond $cbo_prod_source_cond $booking_no_cond $booking_id_cond  and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0
			 GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.po_id, b.prod_id, b.width_dia_type,c.grouping,c.file_no,d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order,f.insert_date,f.ltb_btb_id)
			 union
			 (
				select  f.insert_date, a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty,b.item_description, b.po_id, b.prod_id, b.width_dia_type,null, null,null, null, h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date,
			    f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order, f.ltb_btb_id from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond  $workingCompany_name_cond2 and f.batch_id=a.id  and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $dates_com  $batch_num  $color_name  $buyerdata2 $result_name_cond $shift_name_cond $booking_no_cond $booking_id_cond  $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.po_id, b.prod_id, b.width_dia_type,
			              f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter,
			              f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks,f.insert_date, f.ltb_btb_id
						 ) $order_by2";
			}
			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
			{
				$sql_subcon="select f.insert_date,a.batch_no,a.id, a.batch_weight, a.color_id,a.booking_no, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS sub_batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type, LISTAGG(CAST(c.order_no AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY c.order_no) AS po_number, d.subcon_job as job_no_prefix_num, d.party_id as buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type, f.result from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $workingCompany_name_cond2 and  a.entry_form=36 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,2)  and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $dates_com $sub_job_cond $batch_num  $sub_buyer_cond $suborder_no  $result_name_cond $year_cond $color_name $shift_name_cond $booking_no_cond $booking_id_cond  $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond
				GROUP BY a.batch_no, a.id,a.batch_weight, a.color_id,a.booking_no, a.extention_no,a.total_trims_weight, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.party_id, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,f.result,f.insert_date $order_by";
			}

			if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) // Sample Production
			{
				$sql_sam="(select f.insert_date,a.booking_without_order,a.booking_no,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type,c.file_no,c.grouping, LISTAGG(CAST(c.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY c.po_number) AS po_number, d.job_no as job_no_prefix_num, d.buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, fabric_type,f.result from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $workingCompany_name_cond2 $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $booking_no_cond $booking_id_cond  $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $cbo_prod_type_cond $cbo_prod_source_cond and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
				GROUP BY a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.job_no, d.buyer_name, f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,a.booking_without_order,a.booking_no,c.file_no,c.grouping, f.result,f.insert_date )
				union
				(
				select f.insert_date,a.booking_without_order,a.booking_no,a.batch_no, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, b.item_description, b.po_id, b.prod_id, b.width_dia_type,null, null,null, null, h.buyer_id as buyer_name,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id, f.fabric_type,f.result from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g,wo_non_ord_samp_booking_mst h where h.booking_no=a.booking_no $companyCond $workingCompany_name_cond2 and f.batch_id=a.id and f.batch_id=b.mst_id $dates_com  $batch_num  $buyerdata2 $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $booking_no_cond $booking_id_cond  and a.entry_form=0 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
				GROUP BY a.batch_no, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.po_id, b.prod_id, b.width_dia_type, f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,a.booking_without_order,a.booking_no, f.result,f.insert_date)  $order_by2";
			}
		}
		
	}
	//$batchdata=sql_select($sql);
		//echo $sql_subcon; die;sql_subcon_ltb
		//echo $sql;
	
	if($cbo_type==2) // Dyeing Production
	{

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
			$batch_ids='';$po_ids='';
			foreach($batchdata as $row)
			{
				if($batch_ids=='') $batch_ids=$row[csf('id')]; else $batch_ids.=",".$row[csf('id')];
				if($po_ids=='') $po_ids=$row[csf('po_id')]; else $po_ids.=",".$row[csf('po_id')];
			}
			$batch_idss=implode(",",array_unique(explode(",",$batch_ids)));
			$po_idss=implode(",",array_unique(explode(",",$po_ids)));
			if($db_type==0)
			{
				if($batch_idss!='') $batch_id_cond="and a.id in($batch_idss)"; else $batch_id_cond="";
				if($batch_idss!='') $knit_batch_id_cond="and c.mst_id in($batch_idss)"; else $knit_batch_id_cond="";
				if($batch_idss!='') $dyeing_batch_id_cond="and f.batch_id in($batch_idss)"; else $dyeing_batch_id_cond="";
				if($po_idss!='') $dyeing_po_id_cond="and b.po_breakdown_id in($po_idss)"; else $dyeing_po_id_cond="";
			}
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			//echo $sql_subcon;
			$sql_subcon_data=sql_select($sql_subcon);
			$subcon_batch_ids='';
			foreach($sql_subcon_data as $row)
			{
				if($subcon_batch_ids=='') $subcon_batch_ids=$row[csf('id')]; else $subcon_batch_ids.=",".$row[csf('id')];
			}
			$subcon_batch_idss=implode(",",array_unique(explode(",",$subcon_batch_ids)));
			if($db_type==0)
			{
				if($subcon_batch_idss!='') $sub_batch_id_cond="and a.id in($subcon_batch_idss)"; else $sub_batch_id_cond="";
				if($subcon_batch_idss!='') $dye_sub_batch_id_cond="and f.batch_id in($subcon_batch_idss)"; else $dye_sub_batch_id_cond="";
			}
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==3) //Batch Aganist - Sample
		{
			//echo $sql_sam;
			$batchdata_sam=sql_select($sql_sam);
			$sam_batch_ids='';
			foreach($batchdata_sam as $row)
			{
				if($sam_batch_ids=='') $sam_batch_ids=$row[csf('id')]; else $sam_batch_ids.=",".$row[csf('id')];
			}
			$sam_batch_idss=implode(",",array_unique(explode(",",$sam_batch_ids)));
			if($db_type==0)
			{
				if($sam_batch_idss!='') $sam_batch_id_cond="and a.id in($sam_batch_idss)"; else $sam_batch_id_cond="";
			}

		}
	//print_r($sql_subcon_data);
	}
	if($cbo_type==1)
	{

		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			$batchdata=sql_select($sql);
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			//echo $sql_subcon.'xdf';
			$sql_subcon_data=sql_select($sql_subcon);
		}
	//print_r($sql_subcon_data);
	}
	if($cbo_type==4)
	{
		$batchdata=sql_select($sql);
		/*if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			$batchdata=sql_select($sql);
		}
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==2)
		{
			//echo $sql_subcon.'xdf';
			$sql_subcon_data=sql_select($sql_subcon);
		}*/
	//print_r($sql_subcon_data);
	}

	if($date_search_type==1)
	{
		$date_type_msg="Dyeing Date";
	}
	else
	{
		$date_type_msg="Insert Date";
	}
	$yarn_lot_arr=array();
	if($db_type==0)
	{
		$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='' $dyeing_po_id_cond  group by a.prod_id, b.po_breakdown_id");
		/*
		$yarn_lot_data=sql_select("select b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot from pro_grey_prod_entry_dtls a, order_wise_pro_details b, pro_batch_create_dtls c,pro_fab_subprocess f where a.id=b.dtls_id  and c.prod_id=a.prod_id and b.po_breakdown_id=c.po_id and f.batch_id=c.mst_id and f.entry_form=35 and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yarn_lot!='' $unload_batch_cond2 $dates_com $cbo_prod_source_cond $shift_name_cond $cbo_prod_type_cond  $knit_batch_id_cond group by a.prod_id, b.po_breakdown_id");*/
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
	if ($working_company==0) $workingCompany_name_cond1=""; else $workingCompany_name_cond1="  and service_company='".$working_company."' ";
	if ($working_company==0) $workingCompany_name_cond13=""; else $workingCompany_name_cond13="  and f.service_company='".$working_company."' ";
	if ($company==0) $companyCond1=""; else $companyCond1="  and f.company_id=$company";


	$load_time_data=sql_select("select f.batch_id,f.water_flow_meter,f.batch_no,f.load_unload_id,f.process_end_date,f.end_hours,f.hour_load_meter,f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=35 $companyCond1 and  f.status_active=1  and f.is_deleted=0 $workingCompany_name_cond13 $dyeing_batch_id_cond");
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
	$subcon_load_time_data=sql_select("select f.batch_id,f.water_flow_meter,f.batch_no,f.load_unload_id,f.process_end_date,f.end_hours,f.hour_load_meter,f.end_minutes from pro_fab_subprocess f where f.load_unload_id=1 and f.entry_form=38 $companyCond1  and f.status_active=1  and f.is_deleted=0 $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $workingCompany_name_cond1 $dyeing_batch_id_cond");
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
	$subcon_unload_time_data=sql_select("select f.batch_id,f.batch_no,f.load_unload_id,f.production_date,f.end_hours,f.end_minutes from pro_fab_subprocess f where f.load_unload_id=2 and f.entry_form=38 $companyCond1 $workingCompany_name_cond1 and f.status_active=1  and f.is_deleted=0 $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond");
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

	$sql_batch_id=sql_select("select f.batch_id from  pro_fab_subprocess f where f.entry_form=35 $companyCond1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0  $unload_batch_cond2  $cbo_prod_source_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $dyeing_batch_id_cond");
	$tot_row=1;$batch_id='';
	foreach($sql_batch_id as $row_batch)
	{
	if($batch_id=='') $batch_id=$row_batch[csf('batch_id')];else $batch_id.=",".$row_batch[csf('batch_id')];
	$batch_unload_check[$row_batch[csf('batch_id')]]=$row_batch[csf('batch_id')];

	}//echo $batch_id;die;
		unset($sql_batch_id);

		if($batch_id!='')
		{
			$batchIds=chop($batch_id,','); $batchIds_cond="";
			$tot_ids=count(array_unique(explode(",",$batch_id)));
			//echo $tot_ids.'d';
			if($db_type==2 && $tot_ids>999)
			{
				$batchIds_cond=" and (";
				$batchIdsArr=array_chunk(explode(",",$batchIds),999);
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
				$batchIds=implode(",",array_unique(explode(",",$batchIds)));
				$batchIds_cond=" and a.id not in($batchIds)";
			}
		}
		//echo $batchIds_cond;
	
 ob_start();
	?>
	
	<?
	
	if($cbo_type==2) //  Dyeing Production
	{
		?>
		<div>
		<style>
    	.wrap_break
		{
			word-break:break-all;
			word-wrap:break-word;
		}
    	</style>
		<fieldset style="width:1350px;">
		<div align="center"><strong> <? echo $company_library[$company]; ?> </strong><br> <strong> Daily Dyeing Production </strong><br>
		<?
			//echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
			echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
			?>
		 </div>

		 <?
				
			// if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1 || str_replace("'",'',$cbo_batch_type)==3)
			// {
			// 	$sql="SELECT a.id, f.load_unload_id, SUM(b.batch_qnty) AS batch_qnty, f.fabric_type, f.ltb_btb_id, f.result from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $workingCompany_name_cond2 and  a.entry_form=0 and  g.id=a.color_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2)  and b.po_id=c.id and c.job_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and f.status_active=1 and f.is_deleted=0  $batch_id_cond $prod_date_upto $jobdata $batch_num $buyerdata $order_no $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond $cbo_prod_type_cond $cbo_prod_source_cond $booking_no_cond $booking_id_cond GROUP BY a.id, f.load_unload_id, f.fabric_type, f.ltb_btb_id, f.result";
			
			// 	echo  $sql;
			// 	$sql_datas=sql_select($sql);
			// }
			$batchIdArr = array();
			foreach($batchdata as $row)
			{
				if($batch_id_chk[$row[csf('id')]]=='')
				{
					$batch_id_chk[$row[csf('id')]] = $row[csf('id')];
					array_push($batchIdArr,$row[csf('id')]);
				}
			}

			
			// echo "SELECT a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
			// from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
			// where a.id = b.mst_id and a.load_unload_id in(1,2) and b.load_unload_id in(1,2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1 ".where_con_using_array($batchIdArr,0,'a.batch_id')."";

			$sql_prod_ref= sql_select("SELECT a.id,a.batch_id,a.multi_batch_load_id,a.load_unload_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
			from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
			where a.id = b.mst_id and a.load_unload_id in(1,2) and b.load_unload_id in(1,2) and b.entry_page=35 and a.status_active = 1 and b.status_active=1 ".where_con_using_array($batchIdArr,0,'a.batch_id')."");

			foreach ($sql_prod_ref as $val) 
			{
				if($val[csf("load_unload_id")]==2)
				{
					$program_no=$prog_colorType_arr[$val[csf("batch_id")]][$val[csf("prod_id")]];
					$colortype=$prog_sales_arr[$program_no];
					//$colortype=$sales_batch_colortype_arr[$val[csf("batch_id")]][$val[csf("prod_id")]];
					//echo $colortype.'X,';
					$batch_product_arr[$val[csf("batch_id")]][$val[csf("prod_id")]] += $val[csf("production_qty")];
					$batch_product_arr2[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
					$batch_product_arr3[$val[csf("batch_id")]][$val[csf("prod_id")]][$colortype] += $val[csf("production_qty")];
				}
				else
				{
					$multi_batch_arr[$val[csf("batch_id")]]= $val[csf("multi_batch_load_id")];
				}
				//	$batch_product_Addingarr[$val[csf("batch_id")]][$val[csf("prod_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}

			$add_tp_stri_batch_sql="SELECT  a.entry_form,a.batch_id, a.dyeing_re_process,b.prod_id,b.ratio,c.item_category_id 
			from pro_recipe_entry_mst a,pro_recipe_entry_dtls b,product_details_master c 
			where a.id=b.mst_id and c.id=b.prod_id   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category_id in(5,7,6) ".where_con_using_array($batchIdArr,0,'a.batch_id')." ";
			//echo $add_tp_stri_batch_sql;
			$add_tp_stri_batch_rslt=sql_select($add_tp_stri_batch_sql);

			$add_tp_stri_batch_arr = array();
			foreach ($add_tp_stri_batch_rslt as $val) 
			{
				$entry_formId=$val[csf("entry_form")];
				if($entry_formId==60)
				{
					$all_categry_add_tp_stri_batch_arr[$val[csf("batch_id")]].=$val[csf("item_category_id")].',';

					$add_tp_stri_batch_arr[$val[csf("batch_id")]]= $val[csf("dyeing_re_process")];
				}
			}
			//var_dump($all_categry_add_tp_stri_batch_arr);


			//$unload_qty_arr=array();
			$ok_main_arr=array();
			$not_ok_main_arr=array();
			$no_of_batch_arr=array();
			$ok_main_adding_arr = array();
			$not_ok_main_adding_arr = array();
			$ok_main_topping_arr = array();
			$not_ok_main_topping_arr = array();
			$main_rft_arr = array();
			//$plan_cycle_time=0;
			foreach($batchdata as $row)
			{
				
				if($row[csf('result')]==1)
				{
					$ok_main_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('ltb_btb_id')]]['qty']+=$row[csf('batch_qnty')];
				}
				else
				{
					$not_ok_main_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('ltb_btb_id')]]['qty']+=$row[csf('batch_qnty')];
				}

				if($duplicate_batch_id_chk[$row[csf('id')]]=='')
				{
					$duplicate_batch_id_chk[$row[csf('id')]] = $row[csf('id')];

					$no_of_batch_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('ltb_btb_id')]]++; 	
				}


				//========== Start adding
				//$row[csf("extention_no")]=='' && 				
				if($row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("id")]]==2)
				{

					$all_category_id=rtrim($all_categry_add_tp_stri_batch_arr[$row[csf("id")]],',');
					$all_category_Arr=array_unique(explode(",",$all_category_id));
					

					if($chkBatch_1[$row[csf("id")]] =="")
					{
						$chemical_arr_diff=array_diff($all_category_Arr,$chemical_cat_arr);
						if(count($all_category_Arr)>=2 && ($chemical_arr_diff))
						{
							$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_chemical_adding_qnty'] +=$batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(6,$all_category_Arr)) //====Dyes =6
						{
							$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_adding_qnty'] += $batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(5,$all_category_Arr) || in_array(7,$all_category_Arr)) //Dyes ,//Chemical =5,7 
						{
							$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['chemical_adding_qnty'] +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];

						}
						$chkBatch_1[$row[csf("id")]] =$row[csf("id")];
					}
				}
				//$row[csf("extention_no")]=='' && 
				else if($row[csf("result")] !=1 && $add_tp_stri_batch_arr[$row[csf("id")]]==2)
				{

					$all_category_id=rtrim($all_categry_add_tp_stri_batch_arr[$row[csf("id")]],',');
					$all_category_Arr=array_unique(explode(",",$all_category_id));
					

					if($chkBatch_11[$row[csf("id")]] =="")
					{
						$chemical_arr_diff=array_diff($all_category_Arr,$chemical_cat_arr);
						if(count($all_category_Arr)>=2 && ($chemical_arr_diff))
						{
							$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_chemical_adding_qnty'] +=$batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(6,$all_category_Arr)) //====Dyes =6
						{
							$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_adding_qnty'] += $batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						}
						else if(in_array(5,$all_category_Arr) || in_array(7,$all_category_Arr)) //Dyes ,//Chemical =5,7 
						{
							$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['chemical_adding_qnty'] +=$batch_product_arr2[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];

						}
						$chkBatch_11[$row[csf("id")]] =$row[csf("id")];
					}
				}

				//========== Start Topping
				//$row[csf("extention_no")]=='' && 
				if($row[csf("result")] ==1 && $add_tp_stri_batch_arr[$row[csf("id")]]==1) 
				{
					if($chkBatch_2[$row[csf("id")]] =="")
					{
						
						$ok_main_topping_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['topping_qnty'] += $batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];

						$chkBatch_2[$row[csf("id")]] =$row[csf("id")];
					}
				}
				//$row[csf("extention_no")]=='' && 
				else if($row[csf("result")] !=1 && $add_tp_stri_batch_arr[$row[csf("id")]]==1) 
				{
					if($chkBatch_22[$row[csf("id")]] =="")
					{
						$not_ok_main_topping_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['topping_qnty'] += $batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];

						$chkBatch_22[$row[csf("id")]] =$row[csf("id")];
					}
				}


				//============ Start Rft
				//$row[csf("extention_no")]=='' &&
				if( $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("id")]]=="")
				{
					if($chkBatch_3[$row[csf("id")]] =="")
					{
						$main_rft_arr[$row[csf('id')]][$row[csf('fabric_type')]][$row[csf('ltb_btb_id')]]['rft_qnty'] += $batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];

						$chkBatch_3[$row[csf("id")]] =$row[csf("id")];
					}
				}

				// if($row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("id")]]=="")
				// {
				// 	if($chkBatch_3[$row[csf("id")]] =="")
				// 	{
				// 		$total_rft_qnty += $batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
				// 		$chkBatch_3[$row[csf("id")]] =$row[csf("id")];
				// 	}
				// }
				// else if($row[csf("result")]==2 && $add_tp_stri_batch_arr[$row[csf("id")]]=="")
				// {
				// 	if($chkBatch_3[$row[csf("id")]] =="")
				// 	{
				// 		$total_rft_qnty += $batch_product_arr2[$row[csf("id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
				// 		$chkBatch_3[$row[csf("id")]] =$row[csf("id")];
				// 	}
				// }

				
			}
			// echo "<pre>";
			// print_r($main_rft_arr);


			
			//================ Start Total Dyeing

			$td_btb_ok_batch_qty = 0;
			$td_btb_not_ok_batch_qty = 0;
			$td_ltb_ok_batch_qty = 0;
			$td_ltb_not_ok_batch_qty = 0;
			$td_no_of_batch = 0;
			$g_total_ltb_ok = 0;
			$g_total_ltb_not_ok = 0;
			$g_total_btb_ok = 0;
			$g_total_btb_not_ok = 0;
			$total_production = 0;
			$g_total_production = 0;
			$g_total_no_of_batch = 0;
			$g_percent_of_total = 0;
			$g_rft_percent_of_total = 0;
			$total_dyed_produc =0;
			$g_total_dyed_produc=0;

			$tot_ltd_ok_adding=0;
			$tot_ltd_not_ok_adding=0;
			$tot_btd_ok_adding=0;
			$tot_btd_not_ok_adding=0;
			$total_adding_production=0;
			$tot_btd_ok_topping=0;
			$tot_btd_not_ok_topping=0;
			$tot_ltd_ok_topping=0;
			$tot_ltd_not_ok_topping=0;
			$total_btb_rft=0;
			$total_ltb_rft=0;

			$total_dyes_chemical_adding_qnty = 0;
			
			$duplicate_b_id_chk = array();
			$total_dyeing_f_type = array(1,2,3,4,8,9);
			$chemical_cat_arr=array(5,7);
			foreach($batchdata as $row)
			{
				if($duplicate_b_id_chk[$row[csf('id')]]=='')
				{
					$duplicate_b_id_chk[$row[csf('id')]] = $row[csf('id')];

					foreach ($total_dyeing_f_type as $f_type_id) 
					{
						
						if($row[csf('ltb_btb_id')]==1)
						{
							if($row[csf('result')]==1)
							{
								$td_btb_ok_batch_qty += $ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
							else
							{
								$td_btb_not_ok_batch_qty += $not_ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}

							//================= Start RFT =========

							$total_btb_rft += $main_rft_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['rft_qnty'];
						}
						else
						{
							if($row[csf('result')]==1)
							{
								$td_ltb_ok_batch_qty += $ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
							else
							{
								$td_ltb_not_ok_batch_qty += $not_ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}

							//============ Start RFT =================

							$total_ltb_rft += $main_rft_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['rft_qnty'];
						}

						$td_no_of_batch += $no_of_batch_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]];
						
					}

					if($row[csf('ltb_btb_id')]==1)
					{
						if($row[csf('result')]==1)
						{
							$tot_btd_ok_adding +=$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_chemical_adding_qnty']+$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_adding_qnty']+$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['chemical_adding_qnty'];

							//=======================

							$tot_btd_ok_topping += $ok_main_topping_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['topping_qnty'];

						}
						else
						{
							$tot_btd_not_ok_adding +=$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_chemical_adding_qnty']+$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_adding_qnty']+$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['chemical_adding_qnty'];

							//=======================

							$tot_btd_not_ok_topping += $not_ok_main_topping_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['topping_qnty'];
						}
					}
					else
					{
						if($row[csf('result')]==1)
						{
							$tot_ltd_ok_adding +=$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_chemical_adding_qnty']+$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_adding_qnty']+$ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['chemical_adding_qnty'];

							//====================

							$tot_ltd_ok_topping += $ok_main_topping_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['topping_qnty'];
						}
						else
						{
							$tot_ltd_not_ok_adding +=$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_chemical_adding_qnty']+$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['dyes_adding_qnty']+$not_ok_main_adding_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['chemical_adding_qnty'];

							//==============================

							$tot_ltd_not_ok_topping += $not_ok_main_topping_arr[$row[csf('id')]][$row[csf('ltb_btb_id')]]['topping_qnty'];
						}
					}
				}
				
			}

			//var_dump($total_dyes_chemical_adding_qnty);

			
			$g_total_ltb_ok += $td_ltb_ok_batch_qty;
			$g_total_ltb_not_ok +=$td_ltb_not_ok_batch_qty;
			$g_total_btb_ok +=$td_btb_ok_batch_qty;
			$g_total_btb_not_ok +=$td_btb_not_ok_batch_qty;

			$total_production =($td_ltb_ok_batch_qty+$td_ltb_not_ok_batch_qty+$td_btb_ok_batch_qty+$td_btb_not_ok_batch_qty);
			$g_total_production +=$total_production;

			$total_dyed_produc =($td_ltb_ok_batch_qty+$td_btb_ok_batch_qty);
			$g_total_dyed_produc +=$total_dyed_produc;

			$total_adding_production =($tot_btd_ok_adding+$tot_btd_not_ok_adding+$tot_ltd_ok_adding+$tot_ltd_not_ok_adding);

			$total_topping_production =($tot_btd_ok_topping+$tot_btd_not_ok_topping+$tot_ltd_ok_topping+$tot_ltd_not_ok_topping);
		
			$total_rft = ($total_ltb_rft+$total_btb_rft);

			$total_rft_percentage = ($total_rft/$total_dyed_produc)*100;
			

			//================ Start Total Wash/Enzyme

			$td_wash_btb_ok_batch_qty = 0;
			$td_wash_btb_not_ok_batch_qty = 0;
			$td_wash_ltb_ok_batch_qty = 0;
			$td_wash_ltb_not_ok_batch_qty = 0;
			$td_wash_no_of_batch = 0;
			$percent_of_wash_total = 0;

			$duplicate_b_wash_id_chk= array();
			$total_wash_f_type = array(6,7,10,11,12);

			foreach($batchdata as $row)
			{
				
				if($duplicate_b_wash_id_chk[$row[csf('id')]]=='')
				{
					$duplicate_b_wash_id_chk[$row[csf('id')]] = $row[csf('id')];

					foreach ($total_wash_f_type as $f_type_id) 
					{
						
						if($row[csf('ltb_btb_id')]==1)
						{
							if($row[csf('result')]==1)
							{
								$td_wash_btb_ok_batch_qty += $ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
							else
							{
								$td_wash_btb_not_ok_batch_qty += $not_ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
						}
						else
						{
							if($row[csf('result')]==1)
							{
								$td_wash_ltb_ok_batch_qty += $ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
							else
							{
								$td_wash_ltb_not_ok_batch_qty += $not_ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
						}

						$td_wash_no_of_batch += $no_of_batch_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]];
						
					}
				}
				
			}

			$g_total_ltb_ok += $td_wash_ltb_ok_batch_qty;
			$g_total_ltb_not_ok +=$td_wash_ltb_not_ok_batch_qty;
			$g_total_btb_ok +=$td_wash_btb_ok_batch_qty;
			$g_total_btb_not_ok +=$td_wash_btb_not_ok_batch_qty;

			$total_wash_production =($td_wash_ltb_ok_batch_qty+$td_wash_ltb_not_ok_batch_qty+$td_wash_btb_ok_batch_qty+$td_wash_btb_not_ok_batch_qty);
			$g_total_production +=$total_wash_production;

			$total_wash_produc =($td_wash_ltb_ok_batch_qty+$td_wash_btb_ok_batch_qty);
			$g_total_dyed_produc +=$total_wash_produc;

			//================ Start Total White/Off-White


			$td_white_btb_ok_batch_qty = 0;
			$td_white_btb_not_ok_batch_qty = 0;
			$td_white_ltb_ok_batch_qty = 0;
			$td_white_ltb_not_ok_batch_qty = 0;
			$td_white_no_of_batch = 0;
			$percent_of_white_total = 0;

			$duplicate_b_white_id_chk= array();
			$total_white_f_type = array(5);

			foreach($batchdata as $row)
			{
				
				if($duplicate_b_white_id_chk[$row[csf('id')]]=='')
				{
					$duplicate_b_white_id_chk[$row[csf('id')]] = $row[csf('id')];

					foreach ($total_white_f_type as $f_type_id) 
					{
						
						if($row[csf('ltb_btb_id')]==1)
						{
							if($row[csf('result')]==1)
							{
								$td_white_btb_ok_batch_qty += $ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
							else
							{
								$td_white_btb_not_ok_batch_qty += $not_ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
						}
						else
						{
							if($row[csf('result')]==1)
							{
								$td_white_ltb_ok_batch_qty += $ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
							else
							{
								$td_white_ltb_not_ok_batch_qty += $not_ok_main_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]]['qty'];
							}
						}

						$td_white_no_of_batch += $no_of_batch_arr[$row[csf('id')]][$f_type_id][$row[csf('ltb_btb_id')]];
						
					}
				}
				
			}

			$g_total_ltb_ok += $td_white_ltb_ok_batch_qty;
			$g_total_ltb_not_ok +=$td_white_ltb_not_ok_batch_qty;
			$g_total_btb_ok +=$td_white_btb_ok_batch_qty;
			$g_total_btb_not_ok +=$td_white_btb_not_ok_batch_qty;

			$total_white_production =($td_white_ltb_ok_batch_qty+$td_white_ltb_not_ok_batch_qty+$td_white_btb_ok_batch_qty+$td_white_btb_not_ok_batch_qty);
			$g_total_production +=$total_white_production;

			$total_white_produc =($td_white_ltb_ok_batch_qty+$td_white_btb_ok_batch_qty);
			$g_total_dyed_produc +=$total_white_produc;

		

			//var_dump($td_no_of_batch);



		?>

		 <div>
			<table cellpadding="0"  width="300" cellspacing="0" align="left"  class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th colspan="3">Total Dyeing Production Summary(Shade Match)</th>
					</tr>
					<tr>
						<th>Production Type </th>
						<th>Total Production </th>
						<th>% of Total</th>
					</tr>
				</thead>
				<tbody>
			
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td>Total Dyeing Production</td>
						<td align="right"><? echo number_format($total_dyed_produc,2); ?></td>
						<td align="right">
							<? 
								$per_of_total =($total_dyed_produc/$g_total_dyed_produc)*100;
								echo number_format($per_of_total,2);
								$g_percent_of_total +=$per_of_total;
							?>
						</td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td>Total Wash/Enzyme</td>
						<td align="right"><? echo number_format($total_wash_produc,2); ?></td>
						<td align="right">
						<?
							$per_of_wash_total =($total_wash_produc/$g_total_dyed_produc)*100;
							echo number_format($per_of_wash_total,2);
							$g_percent_of_total +=$per_of_wash_total;
						?>
						</td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>">
						<td>Total White/Off-White</td>
						<td align="right"><? echo number_format($total_white_produc,2); ?></td>
						<td align="right">
							<?
								$per_of_white_total =($total_white_produc/$g_total_dyed_produc)*100;
								echo number_format($per_of_white_total,2);
								$g_percent_of_total +=$per_of_white_total;
							?>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr bgcolor="<? //echo $bgcolor;?>">
						<th>Grand Total:</th>
						<th align="right"><? echo number_format($g_total_dyed_produc,2);?></th>
						<th align="right"><? echo number_format($g_percent_of_total,2);?></th>
					</tr>
					<tr>
						<th align="right">Total RFT Production :</th>
						<th align="right"><b><? echo number_format($total_rft,2);?></b> </th>
						<th align="right"><? echo number_format($total_rft_percentage,2);?></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<br />
		<div style="margin-top: 135px">
			<table cellpadding="0" width="620" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
				<thead>
					<tr>
						<th colspan="8">Total Dyeing Production RFT Process Summary</th>
					</tr>
					<tr bgcolor="#E9F3FF" >
						<th width="100" rowspan="2">Process Type</th>
						<th width="140" colspan="2">LTB	</th>
						<th width="140"  colspan="2">BTB	</th>
						<th width="80" rowspan="2">Total Production</th>
						<th width="80" rowspan="2">% of Total</th>
						<th width="80" rowspan="2">No Of Batch </th>
					</tr>
					<tr bgcolor="#E9F3FF">
						<th width="70">OK</th>
						<th width="70">Not OK</th>
						<th width="70">OK</th>
						<th width="70">Not OK</th>
					</tr>
				</thead>
				<tbody>

					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td>Total Dyeing</td>
						<td align="right"><? echo number_format($td_ltb_ok_batch_qty,2);?></td>
						<td align="right"><? echo number_format($td_ltb_not_ok_batch_qty,2);?></td>
						<td align="right"><? echo number_format($td_btb_ok_batch_qty,2);?></td>
						<td align="right"><? echo number_format($td_btb_not_ok_batch_qty,2);?></td>
						<td align="right"><? echo number_format($total_production,2);?></td>
						<td align="right">
							<? 
								$percent_of_total =($total_production/$g_total_production)*100;
								$g_rft_percent_of_total +=$percent_of_total;
								echo number_format($percent_of_total,2);
							?>
						</td>
						<td align="right">
							<? echo $td_no_of_batch;
							$g_total_no_of_batch += $td_no_of_batch;
							?></td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td>Wash/Enzyme</td>
						<td align="right"><? echo number_format($td_wash_ltb_ok_batch_qty,2); ?></td>
						<td align="right"><? echo number_format($td_wash_ltb_not_ok_batch_qty,2); ?></td>
						<td align="right"><? echo number_format($td_wash_btb_ok_batch_qty,2); ?></td>
						<td align="right"><? echo number_format($td_wash_btb_not_ok_batch_qty,2); ?></td>
						<td align="right"><? echo number_format($total_wash_production,2);?></td>
						<td align="right">
							<? 
								$percent_of_wash_total =($total_wash_production/$g_total_production)*100;
								$g_rft_percent_of_total +=$percent_of_wash_total;
								echo number_format($percent_of_wash_total,2);
							?>
						</td>
						<td align="right">
							<? echo $td_wash_no_of_batch;
							$g_total_no_of_batch += $td_wash_no_of_batch;
							?>
						</td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td>White/Off-White</td>
						<td align="right"><? echo number_format($td_white_ltb_ok_batch_qty,2);?></td>
						<td align="right"><? echo number_format($td_white_ltb_not_ok_batch_qty,2);?></td>
						<td align="right"><? echo number_format($td_white_btb_ok_batch_qty,2);?></td>
						<td align="right"><? echo number_format($td_white_btb_not_ok_batch_qty,2);?></td>
						<td align="right"><? echo number_format($total_white_production,2);?></td>
						<td align="right">
							<? 
								$percent_of_white_total =($total_white_production/$g_total_production)*100;
								$g_rft_percent_of_total +=$percent_of_white_total;
								echo number_format($percent_of_white_total,2);
							?>
						</td>
						<td align="right">
							<?
							 echo $td_white_no_of_batch;
							 $g_total_no_of_batch += $td_white_no_of_batch;
							
							?>
						</td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td><b>Grand Total:</b></td>
						<td align="right"><b><? echo number_format($g_total_ltb_ok,2);?></b></td>
						<td align="right"><b><? echo number_format($g_total_ltb_not_ok,2);?></b></td>
						<td align="right"><b><? echo number_format($g_total_btb_ok,2);?></b></td>
						<td align="right"><b><? echo number_format($g_total_btb_not_ok,2);?></b></td>
						<td align="right"><b><? echo number_format($g_total_production,2);?></b></td>
						<td align="right"><b>
							<?
							echo number_format($g_rft_percent_of_total,2);
							?>
						</b></td>
						<td align="right"><b><? echo $g_total_no_of_batch;?></b></td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td>Total Toping</td>
						<td align="right"><? echo number_format($tot_ltd_ok_topping,2);?>&nbsp;</td>
						<td align="right"><? echo number_format($tot_ltd_not_ok_topping,2);?>&nbsp;</td>
						<td align="right"><? echo number_format($tot_btd_ok_topping,2);?>&nbsp;</td>
						<td align="right"><? echo number_format($tot_btd_not_ok_topping,2);?>&nbsp;</td>
						<td align="right"><? echo number_format($total_topping_production,2); ?>&nbsp;</td>
						<td align="right">
							<?
							$total_topping_percentage =($total_topping_production/$g_total_production)*100;
							echo number_format($total_topping_percentage,2);
							?>&nbsp;
						</td>
						<td align="right">&nbsp;</td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td>Total Adding</td>
						<td align="right"><? echo number_format($tot_ltd_ok_adding,2);?>&nbsp;</td>
						<td align="right"><? echo number_format($tot_ltd_not_ok_adding,2);?>&nbsp;</td>
						<td align="right"><? echo number_format($tot_btd_ok_adding,2);?>&nbsp;</td>
						<td align="right"><? echo number_format($tot_btd_not_ok_adding,2);?>&nbsp;</td>
						<td align="right"><? echo number_format($total_adding_production,2);?>&nbsp;</td>
						<td align="right">
						<?
						$total_adding_percentage =($total_adding_production/$g_total_production)*100;
						echo number_format($total_adding_percentage,2);
						?>&nbsp;
						</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tbody>
				<tfoot>
					<tr bgcolor="<? //echo $bgcolor;?>">
						<th><b>Total RFT Dyeing : </b></th>
						<th  colspan="2" style="text-align: center;"><b><? echo number_format($total_ltb_rft,2);?>&nbsp;</b></th>
						<th  colspan="2" style="text-align: center;"><b><? echo number_format($total_btb_rft,2);?>&nbsp;</b></th>
						<th align="right"><b>
						<? 
						echo number_format($total_rft,2);
						?>&nbsp;</b></th>
						<th align="right"><b><? echo number_format($total_rft_percentage,2);?>&nbsp;</b></th>
						<th align="right"><b>&nbsp;</b></th>
					</tr>
				</tfoot>
			</table>

			<table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
				<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			</table>

			<table cellpadding="0" width="480" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">

			<?

			//===============  For Inhouse (Self Order)

			if (count($batchdata)>0  || count($batchdata_sam)>0) //$batchdata_order_and_sample
			{
				$sql_qty = " (select a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight,
				sum(case when f.service_source=1 then  a.batch_weight end) as batch_weight,
				SUM(case when f.service_source=1 and a.batch_against!=3 and b.is_sales!=1 then b.batch_qnty end) AS production_qty_inhouse,
				SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
				SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 and b.is_sales!=1  then b.batch_qnty end) AS prod_qty_sample_without_order,
				SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 and b.is_sales!=1  then b.batch_qnty end) AS prod_qty_sample_with_order,
				SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty,a.batch_against
				from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a
				where f.batch_id=a.id $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $booking_no_cond  $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  and f.result=1
				group by a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight,a.batch_against)
				union 
				( select a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight,
				sum(case when f.service_source=1 then  a.batch_weight end) as batch_weight,
				SUM(case when f.service_source=1 and a.batch_against in(1,2) and b.is_sales!=1  then b.batch_qnty end) AS production_qty_inhouse,
				SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
				SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 and b.is_sales!=1 then b.batch_qnty end) AS prod_qty_sample_without_order,
				SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 and b.is_sales!=1 then b.batch_qnty end) AS prod_qty_sample_with_order,
				SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty,a.batch_against
				from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h
				where  h.booking_no=a.booking_no $companyCond $booking_no_cond $workingCompany_name_cond2 and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name $non_ref_cond $buyerdata2 $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond and f.result=1
				group by a.id,a.working_company_id,a.company_id,a.batch_no,a.total_trims_weight,a.batch_against ) 
				union
				(select a.working_company_id,a.company_id,a.id,a.batch_no, SUM(b.trims_wgt_qnty) AS total_trims_weight, 0 as batch_weight, 0 AS production_qty_inhouse, 0 AS production_qty_outbound, 0 AS prod_qty_sample_without_order, 0 AS prod_qty_sample_with_order, 0 AS fabric_sales_order_qty,a.batch_against
				from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g 
				where f.batch_id=a.id and a.id=b.mst_id and f.batch_id=b.mst_id and g.id=f.machine_id and d.job_no=a.job_no and a.entry_form=136 and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.result=1 and f.status_active=1 and f.is_deleted=0 $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $booking_no_cond  $cbo_prod_source_cond $cbo_prod_type_cond 
				GROUP BY a.working_company_id,a.company_id,a.id,a.batch_no,a.batch_against) ";

				//echo $sql_qty;
				$sql_result=sql_select( $sql_qty);
			}

			$production_qty_inhouse=0;
			$production_qty_outbound=0;
			$prod_qty_sample_without_order=0;
			$prod_qty_sample_with_order=0;
			$fabric_sales_order_qty=0;$total_trims_weight_qty=0;
			$batchIDs="";$j=1;$self_trims_wgt_check_array=array();

			$repro_production_qty_inhouse=0;
			$repro_production_qty_outbound=0;
			$repro_prod_qty_sample_without_order=0;
			$repro_prod_qty_sample_with_order=0;
			$repro_fabric_sales_order_qty=0;
			$repro_total_trims_weight_qty=0;
			
			$repro_self_trims_wgt_check_array=array();
			$k=1;

			foreach($sql_result as $row)
			{
				if($row[csf('batch_against')]==1)
				{
					$production_qty_inhouse+=$row[csf('production_qty_inhouse')];
					$production_qty_outbound+=$row[csf('production_qty_outbound')];
					$prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
					$prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
					$fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
					
					$batch_noSelf=$row[csf('id')];
					if (!in_array($batch_noSelf,$self_trims_wgt_check_array))
					{ 
						$j++;

						$self_trims_wgt_check_array[]=$batch_noSelf;
						$tot_trim_qty=$row[csf('total_trims_weight')];
						
					}
					else
					{
						$tot_trim_qty=0;
					}
					
					$total_trims_weight_qty+=$tot_trim_qty;
				}
				else
				{
					$repro_production_qty_inhouse+=$row[csf('production_qty_inhouse')];
					$repro_production_qty_outbound+=$row[csf('production_qty_outbound')];
					$repro_prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
					$repro_prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
					$repro_fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
					$repro_batch_noSelf=$row[csf('id')];
					if (!in_array($repro_batch_noSelf,$repro_self_trims_wgt_check_array))
					{ 
						$k++;

						$repro_self_trims_wgt_check_array[]=$repro_batch_noSelf;
						$repro_tot_trim_qty=$row[csf('total_trims_weight')];
					
					}
					else
					{
						$repro_tot_trim_qty=0;
					}
					
					$repro_total_trims_weight_qty+=$repro_tot_trim_qty;
				} 
					
			}
			
			
			if($subcon_batch_ids!='') // for subcon
			{
				if (count($sql_subcon_data)>0) // for subcon
				{
					$sql_subcontact_qty=sql_select("select a.batch_no,a.id, a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS production_qty_subcontact, b.item_description, b.prod_id, $grp_sub_con, d.subcon_job as job_no_prefix_num, d.party_id as buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,f.remarks,g.seq_no, a.batch_against 
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  a.batch_against in(1,2) and f.result=1 $workingCompany_name_condSub $companyCondSub  $booking_no_cond and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2  and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $dates_com $sub_job_cond $batch_num  $sub_buyer_cond $suborder_no  $result_name_cond $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_no, a.id,a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, b.item_description,b.prod_id, d.subcon_job, d.party_id, f.shift_name, f.production_date,g.seq_no,a.batch_against,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,f.result,f.remarks $order_by");
					//and a.batch_against!=2
				}
		

				$production_qty_subcontact=0;
				$repro_production_qty_subcontact=0;
				$sub_trims_wgt_check_array=array();
				$repro_sub_trims_wgt_check_array=array();
				$jj=1;
				$kk=1;
				foreach($sql_subcontact_qty as $row) // for subcon
				{
					if($row[csf('batch_against')]==1)
					{
						$production_qty_subcontact+=$row[csf('production_qty_subcontact')];
						$batch_noSub=$row[csf('id')];
						if (!in_array($batch_noSub,$sub_trims_wgt_check_array))
						{ $jj++;
	
							$sub_trims_wgt_check_array[]=$batch_noSub;
							$tot_trim_qty=$row[csf('total_trims_weight')];
						}
						else
						{
							$tot_trim_qty=0;
						}
						
						$total_trims_weight_qty+=$tot_trim_qty;
					}
					else if($row[csf('batch_against')]==2)
					{
						$repro_production_qty_subcontact+=$row[csf('production_qty_subcontact')];
						$repro_batch_noSub=$row[csf('id')];
						if (!in_array($repro_batch_noSub,$repro_sub_trims_wgt_check_array))
						{ $kk++;
	
							$repro_sub_trims_wgt_check_array[]=$repro_batch_noSub;
							$repro_tot_trim_qty=$row[csf('total_trims_weight')];
						}
						else
						{
							$repro_tot_trim_qty=0;
						}
						
						$repro_total_trims_weight_qty+=$repro_tot_trim_qty;
					}
					
				}
				//echo $sql_qty;
			}
			?>
				<thead>
					<tr>
						<th colspan="6">Total Dyeing Production Summary (Shade Match)</th>
					</tr>
					<tr bgcolor="#E9F3FF" >
						<th width="30">SL</th>
						<th width="100">Production Type	</th>
						<th width="70">Fresh</th>
						<th width="70">Re-Process</th>
						<th width="70">Total Production</th>
						<th width="70">% of Total</th>
					</tr>
				</thead>
				<tbody>
				<?
								
					$k=1;$total_summary_prod_qty=0;
					$total_production_sammary=array(1=>'Inhouse (Self Order)',2=>'Outbound-Subcon',3=>'Sample With Order',4=>'Sample Without Order',5=>'Inbound Subcontract',6=>'Fabric Sales Order',7=>'Trims Wgt');
					$total_prod_sammaryQty=$production_qty_inhouse+$production_qty_outbound+$prod_qty_sample_with_order+$prod_qty_sample_without_order+$production_qty_subcontact+$fabric_sales_order_qty+$total_trims_weight_qty;

					$total_repo_prod_sammaryQty=$repro_production_qty_inhouse+$repro_production_qty_outbound+$repro_prod_qty_sample_without_order+$repro_prod_qty_sample_with_order+$repro_production_qty_subcontact+$repro_fabric_sales_order_qty+$repro_total_trims_weight_qty;

					$tot_production = ($total_prod_sammaryQty+$total_repo_prod_sammaryQty);

					$grnd_tot_production_qty=0;
					$grnd_total_prod_per=0;
					//	echo "DDD";die;
					foreach($total_production_sammary as $type_id=>$val)
					{
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($type_id==1) //Inhouse
						{
							$tot_production_qty=$production_qty_inhouse;
							$tot_repro_production_qty=$repro_production_qty_inhouse;
							$total_production_qnty = $tot_production_qty+$tot_repro_production_qty;
						}
						else  if($type_id==2) //OutBound
						{
							$tot_production_qty=$production_qty_outbound;
							$tot_repro_production_qty=$repro_production_qty_outbound;
							$total_production_qnty = $tot_production_qty+$tot_repro_production_qty;
						}
						else  if($type_id==3) //With Order
						{
							$tot_production_qty=$prod_qty_sample_with_order;
							$tot_repro_production_qty=$repro_prod_qty_sample_without_order;
							$total_production_qnty = $tot_production_qty+$tot_repro_production_qty;
						}
						else  if($type_id==4) //Without Order
						{
							$tot_production_qty=$prod_qty_sample_without_order;
							$tot_repro_production_qty=$repro_prod_qty_sample_with_order;
							$total_production_qnty = $tot_production_qty+$tot_repro_production_qty;
						}
						else  if($type_id==5) //SubCon Order
						{
							$tot_production_qty=$production_qty_subcontact;
							$tot_repro_production_qty=$repro_production_qty_subcontact;
							$total_production_qnty = $tot_production_qty+$tot_repro_production_qty;
						}
						else  if($type_id==6) //Sales Order
						{
							$tot_production_qty=$fabric_sales_order_qty;
							$tot_repro_production_qty=$repro_fabric_sales_order_qty;
							$total_production_qnty = $tot_production_qty+$tot_repro_production_qty;
						}
						else  if($type_id==7) //Trims Wgt
						{
							$tot_production_qty=$total_trims_weight_qty;
							$tot_repro_production_qty=$repro_total_trims_weight_qty;
							$total_production_qnty = $tot_production_qty+$tot_repro_production_qty;
						}
						$total_prod_per=fn_number_format($total_production_qnty/$tot_production,6,'.','');
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="100"><? echo $val; ?></td>
							<td width="70"  align="right"><? echo fn_number_format($tot_production_qty,2,'.',''); ?></td>
							<td width="70"  align="right"><? echo fn_number_format($tot_repro_production_qty,2,'.',''); ?></td>
							<td width="70"  align="right">
								<? echo fn_number_format($total_production_qnty,2,'.',''); 
								?></td>
							<td width="70" align="right"><? echo  fn_number_format(($total_prod_per*100),4,'.',''); ?>&nbsp;</td>
							
						</tr>
						<?
						$total_summary_prod_qty+=$tot_production_qty;
						//unset($subcon_buyer_samary[$rows[csf('buyer_id')]]);

					$grnd_tot_production_qty+=$tot_production_qty;
					$grnd_tot_repro_production_qty+=$tot_repro_production_qty;
					$grnd_total_production_qnty+=$total_production_qnty;
					$grnd_total_prod_per+=$total_prod_per;
					$k++;
					}
						//echo "TTT";die;
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold;">
						<td colspan="2" width="40" align="right">Total :</td>
						<td width="70"  align="right"><? echo fn_number_format($grnd_tot_production_qty,2,'.',''); ?></td>
						<td width="70"  align="right"><? echo fn_number_format($grnd_tot_repro_production_qty,2,'.',''); ?></td>
						<td width="70"  align="right"><? echo fn_number_format($grnd_total_production_qnty,2,'.',''); ?></td>
						<td width="70" align="right"><? echo  fn_number_format(($grnd_total_prod_per*100),4,'.',''); ?>&nbsp;</td>
					</tr>
				</tbody>
			</table>

			<table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
				<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			</table>

			<table cellpadding="0" width="490" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
				<thead>
				<?
					$shift_count=count($shift_name);
					$colspan=4+$shift_count;
				?>
					<tr>
						<th colspan="<? echo $colspan; ?>">Dyeing Production Summary (Shade Match)	</th>
					</tr>
					<tr bgcolor="#E9F3FF" >
						<th width="70">Details</th>
						<?
						foreach ($shift_name as $key => $value) {
							?>
							<th><? echo $value .' Shift'; ?></th>
							<?
						}
						?>
						<th width="70">Without Shift</th>
						<th width="70">Prod. Qty.</th>
						<th width="70">%</th>
					</tr>
				</thead>
				<tbody>

					<?
						//echo $prod_date_upto;
						if ($cbo_result_name==0 || $cbo_result_name==1) $result_name_cond_summary=" and f.result=1"; else $result_name_cond_summary=" ";
						if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1 || str_replace("'",'',$cbo_batch_type)==3)
						{
							$summary_sql="(select a.working_company_id,a.company_id,a.batch_no,a.batch_against, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight,
							SUM(b.batch_qnty) AS batch_qnty,
							sum(CASE WHEN a.batch_against in(1,3)  THEN b.batch_qnty ELSE 0 END) AS curr_batch_qnty,
							sum(CASE WHEN a.batch_against in(2) THEN b.batch_qnty ELSE 0 END) AS re_curr_batch_qnty,
							b.item_description, b.prod_id, b.width_dia_type, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no 
							from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d,  pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a 
							where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $workingCompany_name_cond2   $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $cbo_prod_source_cond  $booking_no_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2,3) and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0 $prod_date_upto
							GROUP BY a.working_company_id,a.company_id,a.batch_no,a.batch_against, a.batch_weight,a.id, a.color_id,a.total_trims_weight, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,d.job_no, d.buyer_name, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.remarks,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,f.result,a.booking_no,a.booking_without_order)
							UNION
							(select a.working_company_id,a.company_id,a.batch_no,a.batch_against, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS batch_qnty, sum(CASE WHEN a.batch_against in(3)  THEN b.batch_qnty ELSE 0 END) AS curr_batch_qnty, sum(CASE WHEN a.batch_against in(2)  THEN b.batch_qnty ELSE 0 END) AS re_curr_batch_qnty, b.item_description, b.prod_id, b.width_dia_type,null as job_no_prefix_num,h.buyer_id as buyer_name,f.remarks,f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id,f.load_unload_id, f.fabric_type,f.result,a.booking_no,a.booking_without_order,g.seq_no 
							from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h 
							where h.booking_no=a.booking_no $companyCond  $booking_no_cond $workingCompany_name_cond2 and f.batch_id=a.id and f.batch_id=b.mst_id  and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(2,3) and f.result=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com $non_ref_cond $batch_num  $color_name  $buyerdata2 $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond $prod_date_upto 
							GROUP BY a.working_company_id,a.company_id,a.batch_no,a.batch_against, a.batch_weight,a.id,h.buyer_id, a.color_id, a.extention_no, b.item_description, b.prod_id, b.width_dia_type,g.seq_no, f.shift_name,a.total_trims_weight, f.production_date,f.process_end_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id,g.seq_no, f.load_unload_id,f.fabric_type,a.booking_without_order,a.booking_no, f.result,f.remarks) 
							UNION
							(select a.working_company_id,a.company_id,a.batch_no,a.batch_against, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.trims_wgt_qnty) AS batch_qnty, sum(CASE WHEN a.batch_against in(1,3) THEN b.trims_wgt_qnty ELSE 0 END) AS curr_batch_qnty, sum(CASE WHEN a.batch_against in(2) THEN b.trims_wgt_qnty ELSE 0 END) AS re_curr_batch_qnty, b.item_description, null as prod_id, null as width_dia_type, d.job_no as job_no_prefix_num, d.buyer_name,f.remarks, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no 
							from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g 
							where f.batch_id=a.id and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and a.entry_form=136 and f.entry_form=35 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $companyCond $workingCompany_name_cond2   $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_source_cond  $booking_no_cond $cbo_prod_type_cond $prod_date_upto
							GROUP BY a.working_company_id,a.company_id,a.batch_no,a.batch_against, a.batch_weight,a.id, a.color_id, a.extention_no,a.total_trims_weight, b.item_description, d.job_no , d.buyer_name,f.remarks, f.shift_name, f.production_date,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,a.booking_no,a.booking_without_order,g.seq_no)";
							// echo $summary_sql;
						}
						if (count($batchdata)>0 || count($batchdata_sam)>0) //$batchdata_sam
						{
								$sql_datas_summary=sql_select($summary_sql);
						}
						
						$unload_qty_arr=array();$plan_cycle_time=0;$reprocess_qty_arr=array();$tot_reprocess_qty=0;$summary_trims_check_array=array();
						$zz=1;
						foreach($sql_datas_summary as $row)
						{
							$batch_againstId=$row[csf('batch_against')];
							$batch_noId=$row[csf('id')];
							if(!in_array($batch_noId,$summary_trims_check_array))
							{ 
								$zz++;
								$summary_trims_check_array[]=$batch_noId;
								$tot_trim_qty=$row[csf('total_trims_weight')];
							}
							else
							{
								$tot_trim_qty=0;
							}
							
							if($batch_againstId==1 || $batch_againstId==3)
							{
								//$tot_row=count($row[csf('process_end_date')]);
								$unload_qty_arr[$row[csf('load_unload_id')]]['qty']+=$row[csf('curr_batch_qnty')]+$tot_trim_qty;
								//$reprocess_qty_arr[2]['bqty']+=$row[csf('batch_qnty')];
								// $tot_reprocess_qty+=$row[csf('re_batch_qnty')];
								$unload_qty_arr[$row[csf('load_unload_id')]]['count']+=$tot_row;
								$unload_qty_arr[$row[csf('load_unload_id')]]['process_end_date'].=$row[csf('process_end_date')].',';
								
								$main_process_end_date_arr[$row[csf('process_end_date')]]=$row[csf('process_end_date')];
							}
							if($batch_againstId==2) //Re dyeing
							{
								//echo $row[csf('re_curr_batch_qnty')]."=";
								$tot_reprocess_qty+=$row[csf('re_curr_batch_qnty')]+$tot_trim_qty;
							}
						}
						unset($sql_datas_summary);

						//print_r($unload_qty_arr);
						$total_current_mon_qty1=$unload_qty_arr[2]['qty'];
						$total_count1=$unload_qty_arr[2]['count'];
						//$total_reprocess_qty1=$tot_reprocess_qty;

						if (count($sql_subcon_data)>0) //$batchdata_sam //subcontact sql
						{
							$sql_subcontact_summary=sql_select("select a.batch_no,a.id, a.batch_against,a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, 
							SUM(b.batch_qnty) AS production_qty_subcontact,
							sum(CASE WHEN a.batch_against in(1)  THEN b.batch_qnty ELSE 0 END) AS curr_batch_qnty,
							sum(CASE WHEN a.batch_against in(2)  THEN b.batch_qnty ELSE 0 END) AS re_curr_batch_qnty,
							b.item_description, b.prod_id, b.width_dia_type, d.subcon_job as job_no_prefix_num, d.party_id as buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,f.remarks,g.seq_no from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  a.batch_against in(1,2) and f.result=1  and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2   and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $workingCompany_name_condSub $companyCondSub  $booking_no_cond $prod_date_upto $sub_job_cond $batch_num  $sub_buyer_cond $suborder_no  $result_name_cond $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $cbo_prod_type_cond $cbo_prod_source_cond
							GROUP BY a.batch_no, a.id,a.batch_weight,a.batch_against, a.color_id, a.extention_no,a.total_trims_weight, b.item_description,b.prod_id, b.width_dia_type, d.subcon_job, d.party_id, f.shift_name, f.production_date,g.seq_no,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,f.result,f.remarks $order_by");
						}
						$summary_trims_wgt_check_array=array();$d=1;
						foreach($sql_subcontact_summary as $row)
						{
							$batch_noSubcon=$row[csf('id')];
							if (!in_array($batch_noSubcon,$summary_trims_wgt_check_array))
							{ $d++;

									$summary_trims_wgt_check_array[]=$batch_noSubcon;
									$tot_trim_qty=$row[csf('total_trims_weight')];
							}
							else
							{
									$tot_trim_qty=0;
							}
							$batch_againstId=$row[csf('batch_against')];
							if($batch_againstId==1)
							{
								$sub_unload_qty_arr[$row[csf('load_unload_id')]]['qty']+=$row[csf('curr_batch_qnty')]+$tot_trim_qty;
								$main_process_end_date_arr[$row[csf('process_end_date')]]=$row[csf('process_end_date')];
							}
							if($batch_againstId==2) //Re dyeing
							{
								//echo $row[csf('re_curr_batch_qnty')]."=";
								$sub_tot_reprocess_qty+=$row[csf('re_curr_batch_qnty')]+$tot_trim_qty;
							}
						}
						unset($sql_subcontact_summary);
						//echo $sub_unload_qty_arr[2]['qty'].'=';;
						$tot_avg_days=count($main_process_end_date_arr);
						$total_current_mon_qty=$unload_qty_arr[2]['qty']+$sub_unload_qty_arr[2]['qty'];//$unload_qty_arr2[2]['qty']+$total_current_mon_qty1;
						$total_count=$tot_avg_days;//$total_count1+$unload_qty_arr2[2]['count'];
						$total_reprocess_qty=$tot_reprocess_qty+$sub_tot_reprocess_qty;
						if (count($batchdata_sam)>0) //$batchdata_samp_booking
						{
							$sql_result_sample="select a.id,f.fabric_type,f.process_end_date,
							SUM(b.batch_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,sum(distinct a.total_trims_weight) as total_trims_weight from pro_batch_create_dtls b, wo_non_ord_samp_booking_mst h, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and  a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and f.fabric_type>0  and a.batch_against in(3)  and h.booking_no=a.booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   and  f.status_active=1 and f.is_deleted=0  $dates_com  $batch_num $buyerdata2  $color_name $shift_name_cond $booking_no_cond $machine_cond $floor_id_cond $non_ref_cond $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY  a.id,f.fabric_type,f.process_end_date  ";
							$sql_result_sam=sql_select($sql_result_sample);
						}
						$fabric_batch_arr=array();$tot_batch_qty_type=array();//$trims_wgt_check_array=array();
						$kk=1;
						foreach($sql_result_sam as $row)
						{
							$batch_noSam=$row[csf('id')];
							if (!in_array($batch_noSam,$trims_wgt_check_array))
							{ $kk++;

									$trims_wgt_check_array[]=$batch_noSam;
									$tot_trim_qty=$row[csf('total_trims_weight')];
							}
							else
							{
									$tot_trim_qty=0;
							}
							//echo $tot_trim_qty.'f';

							$tot_trim_qty=$row[csf('total_trims_weight')];
							$fabric_batch_arr[$row[csf('fabric_type')]]['qty']+=$row[csf('batch_qnty')]+$tot_trim_qty;
							$fabric_batch_arr[$row[csf('fabric_type')]]['weight']+=$row[csf('batch_weight')];
							$tot_batch_qty_type[1]+=$row[csf('batch_qnty')]+$tot_trim_qty;
						}
						unset($sql_result_sam);
						if (count($batchdata)>0) //$batchdata_order
						{
							$sql_result="(select a.id,f.fabric_type,f.process_end_date,
							SUM(b.batch_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,sum(distinct a.total_trims_weight) as total_trims_weight,f.shift_name 
							from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g 
							where f.batch_id=a.id $companyCond $workingCompany_name_cond2  and a.entry_form=0 and  g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and f.fabric_type>0 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0    and  f.status_active=1 and f.is_deleted=0 $dates_com $jobdata $batch_num $buyerdata $order_no $ref_cond $year_cond $booking_no_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond 
							GROUP BY a.id, f.fabric_type,f.process_end_date,f.shift_name)
							union
							(select a.id,f.fabric_type,f.process_end_date, SUM(b.trims_wgt_qnty) AS batch_qnty,sum(a.batch_weight) as batch_weight,sum(distinct a.total_trims_weight) as total_trims_weight ,f.shift_name 
							from pro_batch_trims_dtls b, wo_po_details_master d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g 
							where f.batch_id=a.id $companyCond $workingCompany_name_cond2 and a.entry_form=136 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,3) and d.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.fabric_type>0 $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $booking_no_cond $color_name $shift_name_cond $machine_cond $floor_id_cond $result_name_cond_summary $cbo_prod_type_cond $cbo_prod_source_cond 
							GROUP BY a.id, f.fabric_type,f.process_end_date,f.shift_name)";
							// echo $sql_result;
							$sql_result=sql_select($sql_result);$trims_wgt_check_array2=array();
						}
						$mm=1;
						foreach($sql_result as $row)
						{
							$tot_trim_qty=$row[csf('total_trims_weight')];
							$batch_noSam=$row[csf('id')];
							if (!in_array($batch_noSam,$trims_wgt_check_array2))
							{ $mm++;

								$trims_wgt_check_array2[]=$batch_noSam;
								$tot_trim_qty=$row[csf('total_trims_weight')];
							}
							else
							{
									$tot_trim_qty=0;
							}
									
							$fabric_batch_arr[$row[csf('fabric_type')]]['qty']+=$row[csf('batch_qnty')]+$tot_trim_qty;
							$fabric_batch_arr[$row[csf('fabric_type')]]['weight']+=$row[csf('batch_weight')];
							$fabric_batch_arr[$row[csf('fabric_type')]]['shfit_name']+=$row[csf('shfit_name')];
							$tot_batch_qty_type[1]+=$row[csf('batch_qnty')]+$tot_trim_qty;

							$fabric_batch_arr_shift[$row[csf('fabric_type')]][$row[csf('shift_name')]]['qty']+=$row[csf('batch_qnty')]+$tot_trim_qty;
						}
						unset($sql_result);
						//print_r($fabric_batch_arr);
					?>

					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td colspan="<? echo 2+$shift_count; ?>">Dyeing Fresh Production Current Month</td>
						<td align="right" title="SubCon=<? echo $sub_unload_qty_arr[2]['qty'];?>"><? echo fn_number_format($total_current_mon_qty,2);?></td>
						<td align="right"><?  ?></td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td colspan="<? echo 2+$shift_count; ?>">Re-Process Current Month</td>
						<td align="right" title="SubCon=<? echo $sub_tot_reprocess_qty;?>"><?   echo fn_number_format($total_reprocess_qty,2); ?></td>
						<td align="right"></td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td colspan="<? echo 2+$shift_count; ?>">Dyeing Production On Date</td>
						<td align="right"><? echo number_format($total_dyed_produc,2); ?></td>
						<td align="right"></td>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>" style="cursor:pointer;">
						<td colspan="<? echo 2+$shift_count; ?>">Avg. Prod. Per Day [Only Fresh]</td>
						<td align="right" title="<? echo 'Total Day: '.$total_count;?>"><? echo fn_number_format($total_current_mon_qty/$total_count,2); ?></td>
						<td align="right"></td>
					</tr>
					<tr>
						<td colspan="7">&nbsp;</td>
					</tr>

					<? $k=1;	$tot_batch_qty=0;
					//$fabric_type_for_dyeing2=array(1=>'Cotton',2=>'Polyster',3=>'Lycra',4=>'Both Part',5=>'White',6=>'Wash');

					//print_r($fabric_type_for_dyeingnn);
					foreach($fabric_batch_arr as $typekey=>$val)
					{
						if ($k%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
					
						$total_reporcess=$tot_batch_qty_type[1];
						?>
						<tr bgcolor="<? echo $bgcolor;?>">
							<? //print_r($fabric_type_for_dyeing);?>
							<td><?php echo $fabric_type_for_dyeing[$typekey]; ?></td>
							<?
							foreach ($shift_name as $key => $value) 
							{
								?>
									
								<td><? echo fn_number_format($fabric_batch_arr_shift[$typekey][$key]['qty'],2); ?></td>
								<?
							}
							?>
							<td><? echo fn_number_format($fabric_batch_arr_shift[$typekey][0]['qty'],2); ?></td>
							<td align="right" title="<? echo $val['weight']?>"><? echo fn_number_format($val['qty'],2); ?></td>
							<td align="right"><? echo fn_number_format(($val['qty']/$total_reporcess)*100,2); ?></td>
						</tr>
						<? 		
						$tot_batch_qty+=$val['qty'];
						$k++;
					}

					?>
				</tbody>
				<tfoot>
					<tr bgcolor="<? //echo $bgcolor;?>">
					<tr>
						<th colspan="<? echo 2+$shift_count; ?>" align="right">Grand Total :</th>
						<th align="right"><b><? echo fn_number_format($tot_batch_qty,2,'.','');?></b> </th>
						<th align="right"><? echo fn_number_format(($tot_batch_qty/$total_reporcess*100),2,'.','').'%'; ?></th>
					</tr>
					</tr>
				</tfoot>
			</table>

			<table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
				<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
			</table>

			<table cellpadding="0" width="600" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
			<?

			//===============  For Inhouse (Self Order)

			if (count($batchdata)>0  || count($batchdata_sam)>0) //$batchdata_order_and_sample
			{
				$sql_buyer_qty = " (select a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight,
				sum(case when f.service_source=1 then  a.batch_weight end) as batch_weight,
				SUM(case when f.service_source=1 and a.batch_against!=3 and b.is_sales!=1 then b.batch_qnty end) AS production_qty_inhouse,
				SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
				SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 and b.is_sales!=1  then b.batch_qnty end) AS prod_qty_sample_without_order,
				SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 and b.is_sales!=1  then b.batch_qnty end) AS prod_qty_sample_with_order,
				SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty,a.batch_against,d.buyer_name
				from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a
				where f.batch_id=a.id $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $file_cond $ref_cond $booking_no_cond  $cbo_prod_source_cond $cbo_prod_type_cond and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2)  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  and f.result=1
				group by a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight,a.batch_against,d.buyer_name)
				union 
				( select a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight,
				sum(case when f.service_source=1 then  a.batch_weight end) as batch_weight,
				SUM(case when f.service_source=1 and a.batch_against in(1,2) and b.is_sales!=1  then b.batch_qnty end) AS production_qty_inhouse,
				SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
				SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 and b.is_sales!=1 then b.batch_qnty end) AS prod_qty_sample_without_order,
				SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 and b.is_sales!=1 then b.batch_qnty end) AS prod_qty_sample_with_order,
				SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty,a.batch_against, h.buyer_id AS buyer_name
				from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h
				where  h.booking_no=a.booking_no $companyCond $booking_no_cond $workingCompany_name_cond2 and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $dates_com  $batch_num  $color_name $non_ref_cond $buyerdata2 $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $cbo_prod_type_cond $cbo_prod_source_cond and f.result=1
				group by a.id,a.working_company_id,a.company_id,a.batch_no,a.total_trims_weight,a.batch_against,h.buyer_id ) 
				union
				(select a.working_company_id,a.company_id,a.id,a.batch_no, SUM(b.trims_wgt_qnty) AS total_trims_weight, 0 as batch_weight, 0 AS production_qty_inhouse, 0 AS production_qty_outbound, 0 AS prod_qty_sample_without_order, 0 AS prod_qty_sample_with_order, 0 AS fabric_sales_order_qty,a.batch_against,d.buyer_name
				from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g 
				where f.batch_id=a.id and a.id=b.mst_id and f.batch_id=b.mst_id and g.id=f.machine_id and d.job_no=a.job_no and a.entry_form=136 and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.result=1 and f.status_active=1 and f.is_deleted=0 $companyCond $workingCompany_name_cond2  $dates_com $jobdata $batch_num $buyerdata $order_no $year_cond $color_name  $result_name_cond $shift_name_cond $machine_cond $floor_id_cond $booking_no_cond  $cbo_prod_source_cond $cbo_prod_type_cond 
				GROUP BY a.working_company_id,a.company_id,a.id,a.batch_no,a.batch_against,d.buyer_name ) ";

				//echo $sql_buyer_qty;
				$sql_buyer_result=sql_select( $sql_buyer_qty);
			}

			$production_qty_inhouse=0;
			$production_qty_outbound=0;
			$prod_qty_sample_without_order=0;
			$prod_qty_sample_with_order=0;
			$fabric_sales_order_qty=0;$total_trims_weight_qty=0;
			$batchIDs="";$j=1;$self_trims_wgt_check_array=array();

			$repro_production_qty_inhouse=0;
			$repro_production_qty_outbound=0;
			$repro_prod_qty_sample_without_order=0;
			$repro_prod_qty_sample_with_order=0;
			$repro_fabric_sales_order_qty=0;
			$repro_total_trims_weight_qty=0;

			$buyer_wise_arr = array();
			
			$repro_self_trims_wgt_check_array=array();
			$k=1;

			foreach($sql_buyer_result as $row)
			{
				if($row[csf('batch_against')]==1)
				{
					$production_qty_inhouse+=$row[csf('production_qty_inhouse')];
					$production_qty_outbound+=$row[csf('production_qty_outbound')];
					$prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
					$prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
					$fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
					
					$batch_noSelf=$row[csf('id')];
					if (!in_array($batch_noSelf,$self_trims_wgt_check_array))
					{ 
						$j++;

						$self_trims_wgt_check_array[]=$batch_noSelf;
						$tot_trim_qty=$row[csf('total_trims_weight')];

						$buyer_wise_arr[$row[csf('buyer_name')]][1]['total_trims_weight']+=$row[csf('total_trims_weight')];
						
					}
					else
					{
						$tot_trim_qty=0;
					}
					
					$total_trims_weight_qty+=$tot_trim_qty;

					$buyer_wise_arr[$row[csf('buyer_name')]][1]['production_qty_inhouse']+=$row[csf('production_qty_inhouse')];
					$buyer_wise_arr[$row[csf('buyer_name')]][1]['production_qty_outbound']+=$row[csf('production_qty_outbound')];
					$buyer_wise_arr[$row[csf('buyer_name')]][1]['prod_qty_sample_without_order']+=$row[csf('prod_qty_sample_without_order')];
					$buyer_wise_arr[$row[csf('buyer_name')]][1]['prod_qty_sample_with_order']+=$row[csf('prod_qty_sample_with_order')];

				}
				else
				{
					$repro_production_qty_inhouse+=$row[csf('production_qty_inhouse')];
					$repro_production_qty_outbound+=$row[csf('production_qty_outbound')];
					$repro_prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
					$repro_prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
					$repro_fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
					$repro_batch_noSelf=$row[csf('id')];
					if (!in_array($repro_batch_noSelf,$repro_self_trims_wgt_check_array))
					{ 
						$k++;

						$repro_self_trims_wgt_check_array[]=$repro_batch_noSelf;
						$repro_tot_trim_qty=$row[csf('total_trims_weight')];

						$buyer_wise_arr[$row[csf('buyer_name')]][2]['total_trims_weight']+=$row[csf('total_trims_weight')];
					
					}
					else
					{
						$repro_tot_trim_qty=0;
					}
					
					$repro_total_trims_weight_qty+=$repro_tot_trim_qty;

					$buyer_wise_arr[$row[csf('buyer_name')]][2]['production_qty_inhouse']+=$row[csf('production_qty_inhouse')];
					$buyer_wise_arr[$row[csf('buyer_name')]][2]['production_qty_outbound']+=$row[csf('production_qty_outbound')];
					$buyer_wise_arr[$row[csf('buyer_name')]][2]['prod_qty_sample_order']+=$row[csf('prod_qty_sample_without_order')]+$row[csf('prod_qty_sample_with_order')];
					//$buyer_wise_arr[$row[csf('buyer_name')]][2]['prod_qty_sample_with_order']+=$row[csf('prod_qty_sample_with_order')];
				} 
					
			}

			
			
			
			if($subcon_batch_ids!='') // for subcon
			{
				if (count($sql_subcon_data)>0) // for subcon
				{
					$sql_subcontact_buyer_qty=sql_select("select a.batch_no,a.id, a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, SUM(b.batch_qnty) AS production_qty_subcontact, b.item_description, b.prod_id, $grp_sub_con, d.subcon_job as job_no_prefix_num, d.party_id as buyer_name, f.shift_name, f.production_date as process_end_date,f.process_end_date as production_date, f.end_hours, f.floor_id,f.hour_unload_meter, f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,f.fabric_type, f.result,f.remarks,g.seq_no, a.batch_against 
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g where  a.batch_against in(1,2) and f.result=1 $workingCompany_name_condSub $companyCondSub  $booking_no_cond and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2  and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  $dates_com $sub_job_cond $batch_num  $sub_buyer_cond $suborder_no  $result_name_cond $year_cond $color_name $shift_name_cond $machine_cond $floor_id_cond  $cbo_prod_type_cond $cbo_prod_source_cond GROUP BY a.batch_no, a.id,a.batch_weight, a.color_id, a.extention_no,a.total_trims_weight, b.item_description,b.prod_id,  d.subcon_job, d.party_id, f.shift_name, f.production_date,g.seq_no,a.batch_against,f.process_end_date, f.end_hours, f.floor_id, f.hour_unload_meter,f.water_flow_meter, f.end_minutes, f.machine_id, f.load_unload_id,fabric_type,f.result,f.remarks");
					//and a.batch_against!=2 
				}
		

				$production_qty_subcontact=0;
				$repro_production_qty_subcontact=0;
				$sub_trims_wgt_check_array=array();
				$repro_sub_trims_wgt_check_array=array();
				$jj=1;
				$kk=1;
				foreach($sql_subcontact_buyer_qty as $row) // for subcon
				{
					if($row[csf('batch_against')]==1)
					{
						$production_qty_subcontact+=$row[csf('production_qty_subcontact')];
						$batch_noSub=$row[csf('id')];
						if (!in_array($batch_noSub,$sub_trims_wgt_check_array))
						{ $jj++;
	
							$sub_trims_wgt_check_array[]=$batch_noSub;
							$tot_trim_qty=$row[csf('total_trims_weight')];

							$buyer_wise_arr[$row[csf('buyer_name')]][1]['total_trims_weight']+=$row[csf('total_trims_weight')];
						}
						else
						{
							$tot_trim_qty=0;
						}
						
						$total_trims_weight_qty+=$tot_trim_qty;

						$buyer_wise_arr[$row[csf('buyer_name')]][1]['production_qty_subcontact']+=$row[csf('production_qty_subcontact')];
					}
					else
					{
						$repro_production_qty_subcontact+=$row[csf('production_qty_subcontact')];
						$repro_batch_noSub=$row[csf('id')];
						if (!in_array($repro_batch_noSub,$repro_sub_trims_wgt_check_array))
						{ $kk++;
	
							$repro_sub_trims_wgt_check_array[]=$repro_batch_noSub;
							$repro_tot_trim_qty=$row[csf('total_trims_weight')];

							$buyer_wise_arr[$row[csf('buyer_name')]][2]['total_trims_weight']+=$row[csf('total_trims_weight')];
						}
						else
						{
							$repro_tot_trim_qty=0;
						}
						
						$repro_total_trims_weight_qty+=$repro_tot_trim_qty;

						$buyer_wise_arr[$row[csf('buyer_name')]][2]['production_qty_subcontact']+=$row[csf('production_qty_subcontact')];
					}
					
				}
				//echo $sql_qty;
			}

			//var_dump($buyer_wise_arr);

			$main_buyer_wise_arr = array();
			$main_buyer_wise_arrr = array();
			foreach ($buyer_wise_arr as $buyer_id => $buyer_data) 
			{
				foreach ($buyer_data as $p_type => $row) 
				{
					if($p_type==1)
					{
						$main_buyer_wise_arr[$buyer_id][1]['production_qty_inhouse']+=$row['production_qty_inhouse'];
						$main_buyer_wise_arr[$buyer_id][1]['prod_qty_sample_order']+=$row['prod_qty_sample_order'];
						$main_buyer_wise_arr[$buyer_id][1]['total_trims_weight']+=$row['total_trims_weight'];
						$main_buyer_wise_arr[$buyer_id][1]['production_qty_outbound']+=$row['production_qty_outbound'];
						$main_buyer_wise_arr[$buyer_id][1]['production_qty_subcontact']+=$row['production_qty_subcontact'];

						$main_buyer_wise_arr[$buyer_id][1]['buyer_total']+=$row['production_qty_inhouse']+$row['prod_qty_sample_order']+$row['total_trims_weight']+$row['production_qty_outbound']+$row['production_qty_subcontact'];

						
					}
					else
					{
						$main_buyer_wise_arr[$buyer_id][2]['production_qty_inhouse']+=$row['production_qty_inhouse'];
						$main_buyer_wise_arr[$buyer_id][2]['prod_qty_sample_order']+=$row['prod_qty_sample_order'];
						$main_buyer_wise_arr[$buyer_id][2]['total_trims_weight']+=$row['total_trims_weight'];
						$main_buyer_wise_arr[$buyer_id][2]['production_qty_outbound']+=$row['production_qty_outbound'];
						$main_buyer_wise_arr[$buyer_id][2]['production_qty_subcontact']+=$row['production_qty_subcontact'];

						$main_buyer_wise_arr[$buyer_id][2]['buyer_total']+=$row['production_qty_inhouse']+$row['prod_qty_sample_order']+$row['total_trims_weight']+$row['production_qty_outbound']+$row['production_qty_subcontact'];
					}
					
				}
			}
			//var_dump($main_buyer_wise_arrr);

			?>	

				<thead>
					<tr>
						<th colspan="10">Batch Summary Total (Shade Match)</th>
					</tr>
					<tr bgcolor="#E9F3FF" >
						<th width="30">SL</th>
						<th width="120">Buyer</th>
						<th width="90">Process Type</th>
						<th width="90">Self Batch</th>
						<th width="90">Sample Batch</th>
						<th width="90">Trims Weight</th>
						<th width="90">SubCon Batch (Inbound)</th>
						<th width="90">SubCon Batch (Outbound)</th>
						<th width="90">Total</th>
					</tr>
				</thead>
				<tbody>
				<?
				foreach ($main_buyer_wise_arr as $m_buyer_id => $m_buyer_data) 
				{
					foreach ($m_buyer_data as $m_p_type => $row) 
					{
						$buyer_count[$m_buyer_id]++;
						$process_count[$m_buyer_id][$m_p_type]++;
					}
				}

				$i=1;
				$production_qty_inh=0;
				$prod_qty_sam=0;
				$total_trims=0;
				$production_qty_outb=0;
				$production_qty_subc=0;
				$buyer_tot=0;
				foreach ($main_buyer_wise_arr as $m_buyer_id => $m_buyer_data) 
				{
					foreach ($m_buyer_data as $m_p_type => $row) 
					{
						//var_dump( $row);
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

						$buyer_span = $buyer_count[$m_buyer_id]*2;
						$process_span = $process_count[$m_buyer_id][$m_p_type]*2;

						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							
							<?
							if(!in_array($m_buyer_id,$buyer_chk))
							{
								$buyer_chk[]=$m_buyer_id;

								?>
							<td valign="middle" rowspan="<? echo $buyer_span;?>"><? echo $i; ?></td>
							<td  valign="middle" rowspan="<? echo $buyer_span;?>"><? echo $buyer_arr[$m_buyer_id]; ?></td>
							
							<? }?>

							<?
							if(!in_array($m_buyer_id."**".$m_p_type,$process_chk))
							{
								$process_chk[]=$m_buyer_id."**".$m_p_type;

								?>
							<td align="" valign="middle" rowspan="<? echo $process_span;?>">
								<?
								if($m_p_type==1)
								{
									echo "Fresh"; 
								}
								else
								{
									echo "Re-Process"; 
								}
								 
								?>
							</td>
							<? }?>
							<td align="right"><? echo number_format($row['production_qty_inhouse'],2); $production_qty_inh +=$row['production_qty_inhouse'];?></td>
							<td align="right"><? echo number_format($row['prod_qty_sample_order'],2); $prod_qty_sam +=$row['prod_qty_sample_order'];?></td>
							<td align="right"><? echo number_format($row['total_trims_weight'],2); $total_trims +=$row['total_trims_weight'];?></td>
							<td align="right"><? echo number_format($row['production_qty_outbound'],2); $production_qty_outb +=$row['production_qty_outbound']; ?></td>
							<td align="right"><? echo number_format($row['production_qty_subcontact'],2); $production_qty_subc +=$row['production_qty_subcontact']; ?></td>
							<td align="right"><? echo number_format($row['buyer_total'],2); $buyer_tot +=$row['buyer_total']; ?></td>
						</tr>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							
							<td align="right"><? echo number_format($row['production_qty_inhouse']/$row['buyer_total']*100,2).'%';?></td>
							<td align="right"><? echo number_format($row['prod_qty_sample_order']/$row['buyer_total']*100,2).'%';?></td>
							<td align="right"><? echo number_format($row['total_trims_weight']/$row['buyer_total']*100,2).'%';?></td>
							<td align="right"><? echo number_format($row['production_qty_outbound']/$row['buyer_total']*100,2).'%';?></td>
							<td align="right"><? echo number_format($row['production_qty_subcontact']/$row['buyer_total']*100,2).'%';?></td>
							<td align="right"><? echo number_format($row['buyer_total']/$row['buyer_total']*100,2).'%';?></td>
						</tr>

						<?
						
					}
					$i++;

					
					
				}
				?>	
					
				</tbody>
				<tfoot>
					<tr bgcolor="<? //echo $bgcolor;?>">
						<th  colspan="3" ><b>Grand Total:</b></th>
						<th style="text-align: center;"><b><? echo number_format($production_qty_inh,2);?></b></th>
						<th style="text-align: center;"><b><? echo number_format($prod_qty_sam,2);?></b></th>
						<th align="right"><b><? echo number_format($total_trims,2);?></b></th>
						<th align="right"><b><? echo number_format($production_qty_outb,2);?></b></th>
						<th align="right"><b><? echo number_format($production_qty_subc,2);?></b></th>
						<th align="right"><b><? echo number_format($buyer_tot,2);?></b></th>
					</tr>
					<tr bgcolor="<? //echo $bgcolor;?>">
						<th  colspan="3" ><b>%</b></th>
						<th style="text-align: center;"><b>100.00%</b></th>
						<th style="text-align: center;"><b>100.00%</b></th>
						<th align="right"><b>100.00%</b></th>
						<th align="right"><b>100.00%</b></th>
						<th align="right"><b>100.00%</b></th>
						<th align="right"><b>100.00%</b></th>
					</tr>
				</tfoot>
			</table>

		</div> 
		<br> 
		 <?

		
		
		if(str_replace("'",'',$cbo_batch_type)==0 || str_replace("'",'',$cbo_batch_type)==1)
		{
			if (count($batchdata)>0)
			{
				$group_by=str_replace("'",'',$cbo_group_by);

			 ?>
			 <div align="left">


			 <table class="rpt_table" width="2310" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_1">
              <caption> <b>Self batch  </b></caption>
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80"><? echo $date_type_msg;?></th>

                       <? if($group_by==3 || $group_by==0){ ?>
                    	 <th width="80">M/C No</th>
                   		 <? } ?>
                        <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">Floor</th>
                         <? } if($group_by==1 || $group_by==0){ ?>
                        <th width="80">Shift Name</th>
                         <? } ?>
                        <th width="60">File No</th>
                        <th width="70">Ref. No</th>
                        <th width="100">Buyer</th>
                        <th width="80">Job</th>
                        <th width="90">PO No</th>
                        <th width="100">Fabrics Desc</th>
                        <th width="70" class="wrap_break">Dia/Width Type</th>
                        <th width="80">Color Name</th>
                        <th width="90">Batch No</th>
						 <th width="100">Booking No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Wgt.</th>
                        <th width="60">Hour Load Meter</th>
                        <th width="60">Hour unLoad Meter</th>
                        <th width="60">Total Time</th>
                        <th width="50">Lot No</th>
                        <th width="60">Water Loading Flow</th>
                        <th width="60" class="wrap_break">Water UnLoading Flow</th>
                        <th width="70">Water Cons.</th>


                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab. Type</th>
                        <th width="110">Result</th>
                        <th width="">Remark</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:2330; overflow-y:scroll;;" id="scroll_body_self">
            <table class="rpt_table" id="table_body" width="2310" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <tbody>
                <?
                $i=1; $btq=0; $k=1;$z=1;$total_water_cons_load=0;$total_water_cons_unload=0;
                $batch_chk_arr=array(); $group_by_arr=array();$tot_trims_qnty=0;$trims_check_array=array();
                foreach($batchdata as $batch)
				{
					if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";

					if($date_search_type==1)
					{
						$date_type_cond=$batch[csf('production_date')];//Dyeing Date
					}
					else
					{
						$date_typecond=explode(" ",$batch[csf('insert_date')]);//Insert Date
						$date_type_cond=$date_typecond[0];
					}

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
                                    <td width="80">&nbsp;</td>

                                    <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>
                                     <? if($group_by==2 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } if($group_by==1 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>
                                    <td width="60">&nbsp;</td>
                                    <td width="70">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
                                    <td width="80">&nbsp;</td>
                                    <td width="90">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
                                    <td width="70">&nbsp;</td>
                                    <td width="80">&nbsp;</td>
                                    <td width="230" colspan="3"><strong>Sub. Total : </strong></td>
                                    <td width="70"><? echo fn_number_format($batch_qnty,2); ?></td>
                                    <td width="70"><? echo fn_number_format($trims_btq,2); ?></td>
                                    <td width="60">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="50">&nbsp;</td>
                                    <td width="60"><? echo $total_water_cons_load;?></td>

                                    <td width="70"><? //echo fn_number_format($btq,2); ?></td>
                                    <td width="60">&nbsp;</td>
                                    <td width="75">&nbsp;</td>
                                    <td width="75">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
                                    <td width="110">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
								<?
								unset($batch_qnty);unset($trims_btq);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="31" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
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
                        <td width="80"><p><? echo change_date_format($date_type_cond); $unload_date=$batch[csf('process_end_date')]; ?></p></td>
                         <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <?
						 }
						 if($group_by==2 || $group_by==0){ ?>
                        <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                        <? } if($group_by==1 || $group_by==0){ ?>
                        <td width="80"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                        <? } ?>
                        <td align="center" width="60"><p><? echo $batch[csf('file_no')]; ?></p></td>
                        <td align="center" width="70"><p><? echo $batch[csf('grouping')]; ?></p></td>
                        <td width="100"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                        <td width="80"><p class="wrap_break"><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="90"><p class="wrap_break"><? echo $po_number; ?></p></td>
                        <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $batch[csf('item_description')]; ?></div></td>
                        <td width="70"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
                        <td width="80"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td width="90"><p class="wrap_break"><? echo $batch[csf('batch_no')]; ?></p></td>
						 <td width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                        <td width="40"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo fn_number_format($batch[csf('batch_qnty')],2);  ?></td>

                       <td align="right" width="70"><? echo fn_number_format($tot_trim_qty,2);  ?></td>

                        <td width="60" align="right"><p><? echo fn_number_format($load_hour_meter,2);  ?></p></td>
                        <td width="60" align="right"><p><? echo fn_number_format($batch[csf('hour_unload_meter')],2);  ?></p></td>
                       <td width="60" align="right" ><p><? echo fn_number_format($batch[csf('hour_unload_meter')]-$load_hour_meter,2);  ?></p></td>
                        <td width="50"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                        <td width="60" align="right"><p><? echo $water_cons_load;  ?></p></td>
                        <td width="60" align="right"><p><? echo $water_cons_unload;  ?></p></td>
                          <td align="right" width="70" title="<? echo "Water Cons Unload-Water Cons Load*1000/Batch Qnty"?>" ><? echo fn_number_format((($water_cons_unload-$water_cons_load)*1000)/$batch[csf('batch_qnty')],2);//fn_number_format($water_cons_diff,2);  ?></td>


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
							else if($batch[csf('fabric_type')]==7)
							{
								$fab_type="Melange";
							}
							else if($batch[csf('fabric_type')]==8)
							{
								$fab_type="Viscose";
							}
							else if($batch[csf('fabric_type')]==9)
							{
								$fab_type="CVC 1 Part";
							}
							else if($batch[csf('fabric_type')]==10)
							{
								$fab_type="Scouring";
							}

							else if($batch[csf('fabric_type')]==11)
							{
								$fab_type="AOP Wash";
							}
							else if($batch[csf('fabric_type')]==12)
							{
								$fab_type="Y/D Wash";
							}

						echo $fab_type;//$fabric_type_for_dyeing[$batch[csf('fabric_type')]]; ?></p> </td>
                        <td align="center" width="110"><p><? echo $dyeing_result[$batch[csf('result')]]; ?></p> </td>
                         <td align="center"><p><? echo $batch[csf('remarks')]; ?></p> </td>
					</tr>
					<?
					$i++;
					$batch_qnty+=$batch[csf('batch_qnty')];
					$trims_btq+=$tot_trim_qty;
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
                    <td width="80">&nbsp;</td>
                    <? if($group_by==3 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>
                     <? if($group_by==2 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } if($group_by==1 || $group_by==0){ ?>
                    <td width="80">&nbsp;</td>
                     <? } ?>
                    <td width="60">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="230" colspan="3"><strong>Sub. Total : </strong></td>
                    <td width="70"><? echo fn_number_format($batch_qnty,2); ?></td>

                    <td width="70"><? echo fn_number_format($trims_btq,2); ?></td>
                    <td width="60">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="60"><? echo $total_water_cons_load;?></td>

                    <td width="70"><? //echo fn_number_format($btq,2); ?></td>
                    <td width="60">&nbsp;</td>
                    <td width="75">&nbsp;</td>
                    <td width="75">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                     <td width="100">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
			<? } ?>
			</tbody>
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <? if($group_by==2 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } if($group_by==1 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <th width="60">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="230" colspan="3"><strong>Trims Total : </strong></th>
                    <th width="70"><? echo fn_number_format($tot_trims_qnty,2); ?></th>
                    <th colspan="14">&nbsp; </th>

                </tr>

                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <? if($group_by==2 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } if($group_by==1 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <th width="60">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80"><? //echo $total_water_cons_load;?></th>
                    <th width="230" colspan="3"><strong>Grand Total : </strong></th>
                    <th width="70"><? echo fn_number_format($grand_total_batch_qty+$tot_trims_qnty,2); ?></th>

                    <th colspan="14"> </th>

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

		 <table class="rpt_table" width="1990" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_2">
            <caption> <b> Subcon batch</b> </caption>
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="80"> <? echo $date_type_msg; ?></th>
						<th width="80">M/C No</th>
						<th width="80">Floor</th>
						<th width="80">Shift Name</th>
						<th width="100">Buyer</th>
						<th width="80">Job</th>
						<th width="90">PO No</th>
						<th width="100">Fabrics Desc</th>
						<th width="70" class="wrap_break">Dia/Width Type</th>
						<th width="80">Color Name</th>
						<th width="90">Batch No</th>
						<th width="100">Booking No</th>
						<th width="40">Extn. No</th>
						<th width="70">Dyeing Qty.</th>
                        <th width="60">Hour Load Meter</th>
                        <th width="60">Hour unLoad Meter</th>
						<th width="60">Total Time</th>
						<th width="50">Lot No</th>
                        <th width="60">Water Loading Flow</th>
                        <th width="60" class="wrap_break">Water UnLoading Flow</th>
                        <th width="70">Water Cons.</th>


						<th width="75">Load Date & Time</th>
						<th width="75">UnLoad Date Time</th>

                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fab Type</th>
						<th>Result</th>
					</tr>
				</thead>
		</table>
		<div style=" max-height:350px; width:2010px; overflow-y:scroll;;" id="scroll_body">
		<table class="rpt_table" id="table_body2" width="1990" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
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
					if($date_search_type==1)
					{
						$date_type_cond=$batch[csf('production_date')];//Dyeing Date
					}
					else
					{
						$date_typecond=explode(" ",$batch[csf('insert_date')]);//Insert Date
						$date_type_cond=$date_typecond[0];
					}

					//echo $load_hr[$batch[csf('id')]].'ddddd';
					//echo $batch[csf('id')];
					?>
					<tr bgcolor="<? echo $bgcolor_sub2; ?>" id="trsub_<? echo $i; ?>" onClick="change_color('trsub_<? echo $i; ?>','<? echo $bgcolor; ?>')">
						<td width="30"><? echo $i; ?></td>
						<td width="80"><p><? echo ($date_type_cond == '0000-00-00' || $date_type_cond == '' ? '' : change_date_format($date_type_cond)); $unload_date=$batch[csf('process_end_date')]; ?></p></td>
						<td  align="center" width="80" title="<? echo $machine_arr[$batch[csf('machine_id')]]; ?>"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
						<td  align="center" width="80" title="<? echo $floor_arr[$batch[csf('floor_id')]];  ?>"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
						<td  align="center" width="80" title="<? echo $shift_name[$batch[csf('shift_name')]];  ?>"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
						<td  width="100" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
						<td  width="80" title="<? echo  $batch[csf('job_no_prefix_num')]; ?>"><p class="wrap_break"><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
						<td  width="90" title="<? echo $po_number; ?>"><p class="wrap_break"><? echo $po_number; ?></p></td>
						<td  width="100" title="<? echo $batch[csf('item_description')];?>"><p class="wrap_break"><? echo $batch[csf('item_description')]; ?></p></td>
						<td  width="70" title="<? echo  $fabric_typee[$batch[csf('width_dia_type')]]; ?>"><p><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
						<td  width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
						<td  align="center" width="90" title="<? echo $batch[csf('batch_no')]; ?>"><p class="wrap_break"><? echo $batch[csf('batch_no')]; ?></p></td>
						<td  align="center" width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
						<td  align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
						<td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo fn_number_format($batch[csf('sub_batch_qnty')],2);  ?></td>

                          <td width="60" align="right"><p><? echo $load_hour_meter_sub;  ?></p></td>
                           <td width="60" align="right"><p><? echo $batch[csf('hour_unload_meter')];  ?></p></td>
                          <td width="60" align="right"><p><? echo $batch[csf('hour_unload_meter')]-$load_hour_meter_sub;  ?></p></td>
						<td align="left" width="50" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]]; ?>"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                        <td width="60"><p><? echo $water_cons_load_sub;  ?></p></td>
                        <td width="60"><p><? echo $water_cons_unload_sub;  ?></p></td>

                        <td align="right" width="70" title="<? echo fn_number_format($water_cons_diff_sub,2);  ?>"><? echo fn_number_format($water_cons_diff_sub,2);  ?></td>


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
		<table class="rpt_table" width="1990" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
				<thead>

                    <tr class="tbl_bottom">
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
						<th width="100">&nbsp;</th>
						<th width="40">Total Trims</th>
						<th width="70"><? echo fn_number_format($trims_btq_sub,2); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70"><? //echo fn_number_format($btq_sub,2); ?></th>

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
						<th width="100">&nbsp;</th>
						<th width="40">Grand Total</th>
						<th width="70"><? echo fn_number_format($btq_sub+$trims_btq_sub,2); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						 <th width="60">&nbsp;</th>
						<th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="70"><? //echo fn_number_format($btq_sub,2); ?></th>

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
			 <table class="rpt_table" width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="table_header_3">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80"><? echo $date_type_msg; ?></th>
                         <? if($group_by==3 || $group_by==0){ ?>
                        <th width="80">M/C No</th>
                        <? }
						?>
                        <? if($group_by==2 || $group_by==0){ ?>
                        <th width="80">Floor</th>
                         <? } if($group_by==1 || $group_by==0){ ?>
                        <th width="80">Shift Name</th>
                         <? } ?>
                        <th width="70">File No</th>
                        <th width="80">Ref. No</th>
                        <th width="100">Buyer</th>

                        <th width="80">Job</th>
                        <th width="90">PO No</th>
                        <th width="100">Fabrics Desc</th>
                        <th width="70" class="wrap_break">Dia/ Width Type</th>
                        <th width="80">Color Name</th>

                        <th width="90">Batch No</th>
						<th width="110">Booking No</th>
                        <th width="40">Extn. No</th>
                        <th width="70">Dyeing Qty.</th>
                        <th width="70">Trims Qty.</th>

                        <th width="60">Hour Load Meter</th>
 						<th width="60">Hour unLoad Meter</th>
                         <th width="60">Total Time</th>
                        <th width="50">Lot No</th>
                        <th width="60">Water Loading Flow</th>
                        <th width="60" class="wrap_break">Water UnLoading Flow</th>
                        <th width="70">Water Cons.</th>


                        <th width="75">Load Date & Time</th>
                        <th width="75">UnLoad Date Time</th>
                        <th width="60">Time Used</th>
                        <th width="100">Dyeing Fabrics<br>Type</th>
                        <th width="100">Result</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:2320; overflow-y:scroll;;" id="scroll_body">
            <table class="rpt_table" id="table_body3" width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <tbody>
                <?
                $i=1; $btq=0; $k=1;$z=1;$grand_total_batch_qty_sam=0;$batch_qnty_sam=0;$total_trim_qty=0;
                $batch_chk_arr=array(); $group_by_arr=array();$trims_check_array=array();
                foreach($batchdata_sam as $batch)
				{
					if ($i%2==0)  $bgcolor_dyeing="#E9F3FF"; else $bgcolor_dyeing="#FFFFFF";

					if($date_search_type==1)
					{
						$date_type_cond=$batch[csf('production_date')];//Dyeing Date
					}
					else
					{
						$date_typecond=explode(" ",$batch[csf('insert_date')]);//Insert Date
						$date_type_cond=$date_typecond[0];
					}
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
                                    <td width="80">&nbsp;</td>
                                     <? if($group_by==3 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <?
									 }
									 if($group_by==2 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } if($group_by==1 || $group_by==0){ ?>
                                    <td width="80">&nbsp;</td>
                                     <? } ?>
                                    <td width="70">&nbsp;</td>
                                    <td width="80">&nbsp;</td>
                                    <td width="100">&nbsp;</td>

                                    <td width="80">&nbsp;</td>
                                    <td width="90">&nbsp;</td>

                                    <td width="70">&nbsp;</td>
                    				<td width="70">&nbsp;</td>
									<td width="80">&nbsp;</td>

                                    <td width="240" colspan="3"><strong>Sub. Total : </strong></td>
                                    <td width="70"><? echo fn_number_format($batch_qnty_sam,2); ?></td>
                                    <td width="60"><? echo fn_number_format($batch_qnty_sam_trims,2); ?></td>
                                    <td width="60">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="70"><? //echo fn_number_format($btq,2); ?></td>
                                    <td width="50">&nbsp;</td>
                                     <td width="60">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                     <td width="70"><? //echo fn_number_format($btq,2); ?></td>

                                    <td width="75">&nbsp;</td>
                                    <td width="75">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
                                    <td width="100">&nbsp;</td>

                                    <td>&nbsp;r</td>
                                </tr>
								<?
								unset($batch_qnty_sam);unset($batch_qnty_sam_trims);
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="29" align="left" ><b><? echo $group_name; ?> : <? echo $group_dtls_value; ?></b></td>
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
                        <td width="80"><p><? echo change_date_format($date_type_cond); $unload_date=$batch[csf('process_end_date')]; ?></p></td>
                          <? if($group_by==3 || $group_by==0){ ?>
                        <td align="center" width="80"><p><? echo $machine_arr[$batch[csf('machine_id')]]; ?></p></td>
                        <? } if($group_by==2 || $group_by==0){ ?>
                        <td width="80"><p><? echo $floor_arr[$batch[csf('floor_id')]]; ?></p></td>
                        <? } if($group_by==1 || $group_by==0){ ?>
                        <td width="80"><p><? echo $shift_name[$batch[csf('shift_name')]]; ?></p></td>
                        <? } ?>
                        <td width="70"><p><? echo $batch[csf('file_no')]; ?></p></td>
                        <td width="80"><p><? echo $batch[csf('grouping')]; ?></p></td>
                        <td width="100"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>

                        <td width="80"><p class="wrap_break"><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="90"><p class="wrap_break"><? echo $po_number; ?></p></td>
                        <td width="100"><p class="wrap_break"><? echo $batch[csf('item_description')]; ?></p></td>
                        <td width="70"><p class="wrap_break"><? echo $fabric_typee[$batch[csf('width_dia_type')]]; ?></p></td>
                        <td width="80"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                        <td width="90"><p class="wrap_break"><? echo $batch[csf('batch_no')]; ?></p></td>
						<td width="110"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                        <td width="40"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                        <td align="right" width="70"><? echo fn_number_format($batch[csf('batch_qnty')],2);  ?></td>
                        <td align="right" width="70"><? echo fn_number_format($tot_trim_qty,2);  ?></td>
                        <td width="60"><p><? echo fn_number_format($hour_meter_load,2);  ?></p></td>
                        <td width="60"><p><? echo fn_number_format($batch[csf('hour_unload_meter')],2);  ?></p></td>

                        <td width="60"><p><? echo fn_number_format($batch[csf('hour_unload_meter')]-$hour_meter_load,2);  ?></p></td>
                        <td width="50"><p><? echo $yarn_lot_arr[$batch[csf('prod_id')]][$batch[csf('po_id')]];  ?></p></td>
                         <td width="60"><p><? echo $water_cons_load;  ?></p></td>
                        <td width="60"><p><? echo $water_cons_unload;  ?></p></td>
                          <td align="right" width="70" ><? echo fn_number_format($water_cons_diff,2);  ?></td>


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
					$batch_qnty_sam_trims+=$tot_trim_qty;
					$total_trim_qty+=$tot_trim_qty;
					$grand_total_batch_qty_sam+=$batch[csf('batch_qnty')];
				} //batchdata froeach
				if($group_by!=0)
				{
			?>
                 <tr class="tbl_bottom">
                    <th width="30">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } if($group_by==2 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } if($group_by==1 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                     <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="80">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="240" colspan="3"><strong>Sub. Total : </strong></th>
                    <th width="70"><? echo fn_number_format($batch_qnty_sam,2); ?></th>
                  	<th width="60"><? echo fn_number_format($batch_qnty_sam_trims,2); ?></th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                     <th width="70"><? //echo fn_number_format($btq,2); ?></th>
                    <th width="50">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="70"><? //echo fn_number_format($btq,2); ?></th>

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
                    <th width="80">&nbsp;</th>
                     <? if($group_by==3 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } if($group_by==2 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } if($group_by==1 || $group_by==0){ ?>
                    <th width="80">&nbsp;</th>
                    <? } ?>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="80">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="240" colspan="3"><strong>Grand Total : </strong></th>
                    <th width="70"><? echo fn_number_format($grand_total_batch_qty_sam,2); ?></th>
                    <th width="60"><? echo fn_number_format($total_trim_qty,2);?></th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="70"><? //echo fn_number_format($btq,2); ?></th>
                    <th width="50">&nbsp;</th>
                   <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="70"><? //echo fn_number_format($btq,2); ?></th>

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
		//disconnect($con);
		//exit();
		} //Dyeing Production End

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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
	disconnect($con);
	exit();
}//Dyeing Report end


?>