<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');

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
	echo create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,70","520","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,0,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}//batchnumbershow;

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=3 and company_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/batch_creation_controller',this.value, 'load_drop_machine', 'td_dyeing_machine' );",0 );

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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'batch_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
}//bookingnumbershow;

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

if($action=="jobnumbershow")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
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
//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

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
}//batchnumbershow;

if($action=="batch_report") // Show
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
	$floor_no = str_replace("'","",$cbo_floor);
	//echo $cbo_batch_type;die;
	/*echo $floor_no;die;*/
	$batch_number_hidden = str_replace("'","",$batch_number);
	$booking_no = str_replace("'","",$txt_booking_no);
	$booking_number_hidden = str_replace("'","",$txt_hide_booking_id);

	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$year = str_replace("'","",$cbo_year);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	//echo $order_no;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$process=33;
	//echo str_replace("'","",$batch_no);die;

	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='$buyer'";
	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_name='$buyer'";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".str_replace("'","",$batch_no)."'";
	if ($booking_no=="") $booking_num=""; else $booking_num="  and a.booking_no like '%".str_replace("'","",$booking_no)."%'";
	if ($floor_no==0) $floor_num=""; else $floor_num="  and a.floor_id='".$floor_no."'";
	if ($file_no=="") $file_cond=""; else $file_cond="  and b.file_no='".$file_no."'";

	if ($batch_no=="") $batch_num2=""; else $batch_num2="  and batch_no='".str_replace("'","",$batch_no)."'";
	if ($ref_no=="")
	{
		$ref_cond="";
		$ref_cond2="";

	}
	else
	{
	$ref_cond="  and b.grouping='$ref_no'";
	$ref_cond2="  and c.grouping='$ref_no'";
	}
	if ($company==0) $comp_cond=""; else $comp_cond=" and a.company_id=$company";
	if ($working_company==0) $working_comp_cond=""; else $working_comp_cond=" and a.working_company_id=$working_company";
	//a.company_id=$company

	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if($batch_type==0 || $batch_type==1 ||  $batch_type==3)
	{
		if ($txt_order=="") $order_no_cond=""; else $order_no_cond="  and b.po_number='$txt_order'";
		if ($txt_order=="") $order_no2=""; else $order_no2="  and c.po_number='$txt_order'";
		if ($job_number_id=="") $jobdata=""; else $jobdata="  and a.job_no_prefix_num in($job_number_id)";
		if ($job_number_id=="") $jobdata3=""; else $jobdata3="  and d.job_no_prefix_num in($job_number_id)";
	}
	//echo $jobdata;
	if($batch_type==0 || $batch_type==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num in(".$job_number_id.") "; else $sub_job_cond="";
		if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	}
	if ($buyer==0) $samp_buyercond=""; else $samp_buyercond=" and c.buyer_id=".$buyer." ";

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
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}

	$po_array=array();
	$po_sql=sql_select("select a.job_no_prefix_num 	as job_no,a.buyer_name,a.style_ref_no, b.file_no,b.grouping as ref,b.id, b.po_number,b.pub_shipment_date,c.mst_id as batch_id,d.is_sales,d.sales_order_id from wo_po_details_master a, wo_po_break_down b,pro_batch_create_dtls c,pro_batch_create_mst d where a.id=b.job_id and c.po_id=b.id and d.id=c.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0  and c.status_active in (1) and c.is_deleted=0 and d.status_active in (1) and d.is_deleted=0 $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2");



	$poid='';
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$po_array[$row[csf('id')]]['style_no']=$row[csf('style_ref_no')];
		$po_array[$row[csf('id')]]['ref']=$row[csf('ref')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];

		$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
		if($row[csf('is_sales')]==1)
		{
			$sales_id_array[$row[csf('sales_order_id')]]=$row[csf('sales_order_id')];
		}
		$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		if($poid=='') $poid=$row[csf('id')]; else $poid.=",".$row[csf('id')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
	}
	//echo 'DD';;die;
	//$batch_cond_for_in=where_con_using_array($batch_id_arr,0,"a.id");
	//	$sales_cond_for_in=where_con_using_array($sales_id_array,0,"a.id");
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4,5,6,7) and ENTRY_FORM=15");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 1, $batch_id_arr, $empty_arr);//Batch ID Ref from=1
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 2, $sales_id_array, $empty_arr);//Sales id Ref from=2

	//echo $poid.'SSSSSSSSSSSS';;die;
	//for sales order entry
	if ($buyer==0) $buyer_cond2=""; else $buyer_cond2="  and b.buyer_id='$buyer'";
	$sales_po_array=array();
	$sales_po_sql=sql_select("select  a.job_no as po_number,a.buyer_id as buyer_name,a.style_ref_no,a.id from fabric_sales_order_mst a, pro_batch_create_mst b,gbl_temp_engine g where a.id=b.sales_order_id and b.is_sales=1  and  a.id=g.ref_val and b.sales_order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2 and g.entry_form=15  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond2 $year_cond  ");
	$sales_poid='';
	foreach($sales_po_sql as $row)
	{
		$sales_po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$sales_po_array[$row[csf('id')]]['style_no']=$row[csf('style_ref_no')];
		$sales_po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		if($sales_poid=='') $sales_poid=$row[csf('id')]; else $sales_poid.=",".$row[csf('id')];
	} //echo $sales_poid;die;




	/*echo "select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d  where d.subcon_job=c.job_no_mst  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond";die;*/
	/*$sub_po_array=array();
	$sub_po_sql=sql_select("select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d,gbl_temp_engine g  where d.subcon_job=c.job_no_mst and  a.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=15 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond");
	$sub_poid='';
	foreach($sub_po_sql as $row)
	{
		$sub_po_array[$row[csf('id')]]['po_no']=$row[csf('order_no')];
		$sub_po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$sub_po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
		if($sub_poid=='') $sub_poid=$row[csf('id')]; else $sub_poid.=",".$row[csf('id')];
	}*/
	 // GETTING BUYER NAME
	$non_order_arr=array();
	$sql_non_order="SELECT c.id,c.company_id,c.grouping as samp_ref_no,c.style_desc, c.buyer_id as buyer_name, b.booking_no, b.bh_qty, b.style_id
	from wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls b ,pro_batch_create_mst d,gbl_temp_engine g
	where c.booking_no=b.booking_no and d.booking_no_id=c.id and  d.id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=15  and c.booking_type=4 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 $ref_cond2";
	// echo $sql_non_order;die;
	$result_sql_order=sql_select($sql_non_order);
	foreach($result_sql_order as $row)
	{

		$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
		$non_order_arr[$row[csf('booking_no')]]['samp_ref_no']=$row[csf('samp_ref_no')];
		$non_order_arr[$row[csf('booking_no')]]['style_desc']=$row[csf('style_desc')];
		$non_order_arr[$row[csf('booking_no')]]['style_id']=$row[csf('style_id')];
		$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
		$non_booking_id_r_arr[$row[csf('id')]]=$row[csf('id')];
		$sample_reqIdArr[$row[csf('style_id')]]=$row[csf('style_id')];
		$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 7, $sample_reqIdArr, $empty_arr);//Sample Req  id Ref from=7
	// echo "<pre>";
	// print_r($non_order_arr);
	$style_ref_no_lib=return_library_array( "select a.id,a.style_ref_no from sample_development_mst a,wo_non_ord_samp_booking_dtls b,gbl_temp_engine g  where a.id=b.style_id and a.id=g.ref_val  and b.style_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=7 and g.entry_form=15 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ", "id", "style_ref_no"  );
	if($ref_no!="")
	{
		$booking_ids=count($non_booking_id_r_arr);
		if($db_type==2 && $booking_ids>1000)
		{
			$non_booking_cond_for=" and (";
			$bookIdsArr=array_chunk($non_booking_id_r_arr,999);
			foreach($bookIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$non_booking_cond_for.=" a.booking_no_id in($ids) or";
			}
			$non_booking_cond_for=chop($non_booking_cond_for,'or ');
			$non_booking_cond_for.=")";
		}
		else
		{
			$non_booking_cond_for=" and a.booking_no_id in(".implode(",",$non_booking_id_r_arr).")";
		}
	}
	//echo $non_booking_cond_for.'DDDDDDDDD';die;
	//if($sub_poid=="") $sub_poid=0;else $sub_poid=$sub_poid;
	//echo $sub_poid.'gfgf';
	$po_id="";
	if($txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
	{
		$po_id=$poid;
	}
	//echo $po_id;
	$sub_po_id="";
	if($txt_order!="" || $job_number_id!=""  || $year!=0)
	{
		$sub_po_id=$sub_poid;
	}

	$po_id_cond="";
	if($po_id!="")
	{
		//echo $po_id=substr($po_id,0,-1);
		$po_id=chop($po_id,',');
		if($db_type==0) $po_id_cond="and b.po_id in(".$po_id.")";
		else
		{
			$po_ids=array_unique(explode(",",$po_id));
			if(count($po_ids)>990)
			{
				$po_id_cond="and (";
				$po_ids=array_chunk($po_ids,990);
				$z=0;
				foreach($po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $po_id_cond.=" b.po_id in(".$id.")";
					else $po_id_cond.=" or b.po_id in(".$id.")";
					$z++;
				}
				$po_id_cond.=")";
			}
			else{
			$po_id=implode(",",array_unique(explode(",",$po_id)));
			$po_id_cond="and b.po_id in(".$po_id.")";}
		}
	}
	//echo $po_id_cond;die;
	$sub_po_id_cond="";
	if($sub_po_id!="")
	{
		//$sub_po_id=substr($sub_po_id,0,-1);
		$sub_po_id=chop($sub_po_id,',');
		if($db_type==0) $sub_po_id_cond="and b.po_id in(".$sub_po_id.")";
		else
		{
			$sub_po_ids=array_unique(explode(",",$sub_po_id));
			if(count($sub_po_ids)>990)
			{
				$sub_po_id_cond="and (";
				$sub_po_ids=array_chunk($sub_po_ids,990);
				$z=0;
				foreach($sub_po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $sub_po_id_cond.=" b.po_id in(".$id.")";
					else $sub_po_id_cond.=" or b.po_id in(".$id.")";
					$z++;
				}
				$sub_po_id_cond.=")";
			}
			else {
			$sub_po_id=implode(",",array_unique(explode(",",$sub_po_id)));
			$sub_po_id_cond="and b.po_id in(".$sub_po_id.")";}
		}
	}
	//echo  $sub_po_id_cond;
	//echo $po_id.'aaas';

	$sql_dyeing_subcon=sql_select("select c.batch_id,c.entry_form from  pro_fab_subprocess c,pro_batch_create_mst a,gbl_temp_engine g where a.id=c.batch_id  and  a.id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=15 and c.entry_form in(38,35,32,47,31,48) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.batch_id>0  $batch_num2 $dates_com $batch_num ");
	//echo "select c.batch_id,c.entry_form from  pro_fab_subprocess c,pro_batch_create_mst a where a.id=c.batch_id and c.entry_form in(38,35,32,47,31,48) and c.status_active=1 and c.is_deleted=0 and batch_id>0 $batch_num2 $dates_com $batch_num ";
	//echo "select batch_id,entry_form from  pro_fab_subprocess where entry_form in(38,35,32,47,31,48) and status_active=1 and is_deleted=0 and batch_id>0 $batch_num2";//die;
	//die;
	$k=1;$i=1;$m=1;$n=1;$p=1;$j=1;$h=1;
	foreach($sql_dyeing_subcon as $row_sub)
	{
		if($row_sub[csf('entry_form')]==38)
		{
		if($k!==1) $sub_cond_d.=",";
		$sub_cond_d.=$row_sub[csf('batch_id')];
		$k++;
		}
		if($row_sub[csf('entry_form')]==35)
		{
		if($i!==1) $row_d.=",";
		$row_d.=$row_sub[csf('batch_id')];
		$i++;
		}
		if($row_sub[csf('entry_form')]==32)
		{
		if($m!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$m++;
		}
		if($row_sub[csf('entry_form')]==47)
		{
		if($n!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$n++;
		}
		if($row_sub[csf('entry_form')]==31)
		{
		if($p!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$p++;
		}
		if($row_sub[csf('entry_form')]==48)
		{
		if($j!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$j++;
		}

	}//echo $sub_cond;die;

	/*$sql_batch_dyeing=sql_select("select batch_id from  pro_fab_subprocess where entry_form=35 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dyeing as $row_dyeing)
	{
		if($i!==1) $row_d.=",";
		$row_d.=$row_dyeing[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_h)
	{
		if($i!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$i++;
	}
	/*$sub_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sub_batch_h as $subrow_h)
	{
		if($i!==1) $subrow_heat.=",";
		$subrow_heat.=$subrow_h[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=47 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_s)
	{
		if($i!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$i++;
	}
	$sql_batch_dry=sql_select("select batch_id from  pro_fab_subprocess where entry_form=31 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dry as $rowdry)
	{
		if($i!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$i++;
	}
	$sql_batch_stenter=sql_select("select batch_id from  pro_fab_subprocess where entry_form=48 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_stenter as $row_sten)//Stentering
	{
		if($i!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$i++;
	}*/

	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/
	if($db_type==0)
	{
		if($cbo_type==1) //Date Wise Report
		{
			if($batch_type==1) // Self
			{
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					$sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.style_ref_no,a.floor_id,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=0 and a.batch_against!=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $floor_num $year_cond $booking_num  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks order by a.batch_date)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against!=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $po_id_cond  $year_cond $booking_num  GROUP BY a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and b.po_id=0 and a.entry_form=0 and a.batch_against!=3   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
				}
			}
			else if($batch_type==2) //SubCon
			{
				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type,b.grey_dia, a.process_id, a.remarks order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where   a.id=b.mst_id  and a.entry_form=36 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.grey_dia, a.process_id, a.remarks)
				union
				(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where    a.id=b.mst_id and a.entry_form=36 and  b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,b.grey_dia, a.process_id, a.remarks) order by batch_date";
				}
			}
			else if($batch_type==3) // Sample batch
			{
				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
				}
				else
				{*/
					$sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id  and  a.entry_form=0 and a.batch_against=3 and b.po_id>0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $non_booking_cond_for $working_comp_cond $dates_com  $batch_num  $ext_no $floor_num $year_cond $booking_num $po_id_cond GROUP BY a.id,a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c where a.id=b.mst_id and c.booking_no=a.booking_no  and  a.entry_form=0 and a.batch_against=3 and b.po_id=0 and b.status_active=1 and c.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $samp_buyercond $ext_no $floor_num $year_cond $non_booking_cond_for $booking_num  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.width_dia_type, a.process_id, a.remarks)  order by batch_date";
				//}
			}
			else if($batch_type==0) // All batch
			{
				// Self
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					$sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id  and  a.entry_form=0 and a.batch_against!=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $floor_num $year_cond $booking_num  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id order by a.batch_date, a.process_id, a.remarks)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against!=3  and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $po_id_cond  $year_cond $booking_num  GROUP BY a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id  and a.entry_form=0 and a.batch_against!=3  and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
				}

				// Subcon

				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=36  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type,b.grey_dia order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b, pro_batch_create_mst a where    a.id=b.mst_id and a.entry_form=36  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type)
				union
				(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id  from pro_batch_create_dtls b,pro_batch_create_mst a where    a.id=b.mst_id and  a.entry_form=36 and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,b.grey_dia) order by batch_date";
				}

				// Sample

				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
				}
				else
				{*/
					 $sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $non_booking_cond_for $working_comp_cond $dates_com  $batch_num  $ext_no $floor_num $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c  where a.id=b.mst_id  and c.booking_no=a.booking_no and  a.entry_form=0 and a.batch_against=3 and b.po_id=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and a.status_active=1 and a.is_deleted=0 $comp_cond  $samp_buyercond $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $year_cond $non_booking_cond_for $po_id_cond $booking_num  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.width_dia_type)  order by batch_date";
				//}
			}
		}
		else if($cbo_type==2) //wait for Heat Setting
		{
			if($batch_type==0 || $batch_type==1)
			{
				if($row_h!=0)
				{
					$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $comp_cond  $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
				}
			}
			if($batch_type==0 || $batch_type==2)
			{
				if($row_h!=0)
				{
					$sub_cond="( select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst $dates_com $comp_cond   $batch_num $booking_num  $working_comp_cond  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,null,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond GROUP BY a.id, a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
				}
			}
		}
		else if($cbo_type==3) // Wait For Dyeing
		{
			//$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
			//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
			$find_inset="and  FIND_IN_SET(33,a.process_id)";
			$find_inset_not="and not FIND_IN_SET(33,a.process_id)";
			//else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
			if($batch_type==0 || $batch_type==1)
			{
				$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32  and a.id not in($row_d) $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $comp_cond $working_comp_cond  $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $find_inset GROUP BY  a.id,a.batch_no, b.po_id,b.prod_id,b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no,b.po_id,b.prod_id, b.item_description, a.process_id, a.remarks ) order by batch_date";
			}
			if($batch_type==0 || $batch_type==2) //SubCon Deying
			{
				$sub_cond="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks  from pro_batch_create_mst a,pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d where  a.id=b.mst_id and b.po_id=c.id  and d.subcon_job=c.job_no_mst    and a.id not in($sub_cond_d) $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond and a.entry_form=36 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   GROUP BY a.batch_no, b.po_id,b.prod_id,b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num $booking_num $ext_no $floor_num  GROUP BY a.id, a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no, a.extention_no,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
			}
		}
		else if($cbo_type==4) //Wait For Re-Dyeing
		{
			if($batch_type==0 || $batch_type==1)
			{
				$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
				from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks
				from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and a.batch_against=2 and b.po_id=0 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $batch_num $booking_num $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
			}

			if($batch_type==0 || $batch_type==2) //SubCon Batch
			{

				$sql_subcon="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
				from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num $booking_num $floor_num $ext_no   GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";

			}
		}
		else if($cbo_type==5) //Wait For Singeing
		{
			if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)";
			else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
			if($batch_type==0 || $batch_type==1)
			{
				$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
				if($w_sing_arr!=0)
				{
					$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where   a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond $comp_cond ";
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no,a.floor_id, b.item_description,a.batch_date, a.batch_weight, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type, a.process_id, a.remarks) order by batch_date ";
				}//W-end
				//echo $sql;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		//echo $row_d."=GGGGGGGGG";die;
		/*
		|--------------------------------------------------------------------------
		| Date Wise Report
		|--------------------------------------------------------------------------
		|
		*/
		if($cbo_type==1)
		{
			/*
			|--------------------------------------------------------------------------
			| All Batch
			|--------------------------------------------------------------------------
			|
			*/
			if($batch_type==0)
			{
				if($job_number_id!="" || $txt_order!="")
				{
			  		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,count(b.roll_no) as roll_no,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks order by a.batch_date";
			  		/*echo $sql;die;*/
				}
				else
				{
					//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS
					//echo $po_id_cond ;
					$sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS batch_qnty, b.item_description, listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, b.prod_id, b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $batch_num $booking_num $po_id_cond $ext_no $floor_num $year_cond $comp_cond GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type, a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS batch_qnty, b.item_description, listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, b.prod_id, b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $booking_num $po_id_cond $batch_num $floor_num $ext_no $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}

				//Sub
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
					$sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a
					where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.grey_dia, a.entry_form,b.rec_challan, a.process_id, a.remarks order by a.batch_date ";
				}
				else
				{
					$sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,b.gsm,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.gsm,b.grey_dia,a.entry_form,b.rec_challan,a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,b.gsm,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,b.gsm,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id,b.prod_id,b.width_dia_type,b.grey_dia,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				}

				//Sam

				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="" )
				{
					//echo $order_no;
			 		$sql_sam="SELECT a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $booking_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
				}
				else
				{*/
					//echo $$job_number_id .'aaaa';
				$sql_sam="(SELECT a.id,a.batch_against,a.entry_form, a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $non_booking_cond_for $comp_cond $dates_com  $batch_num $booking_num $po_id_cond  $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no,a.booking_without_order, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c where a.id=b.mst_id  and a.booking_no=c.booking_no and  a.entry_form=0 and a.batch_against=2 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $non_booking_cond_for $samp_buyercond $ref_cond2  $batch_num $booking_num $ext_no $floor_num $floor_num $year_cond GROUP BY a.id,a.batch_against,a.batch_no,a.booking_without_order, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
					//echo $sql_sam;//and b.po_id=0
				//}
				// echo $sql_sam;
			}

			/*
			|--------------------------------------------------------------------------
			| Self Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==1)
			{
				if($job_number_id!="" || $txt_order!="")
				{
					//echo $order_no;
			  		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks order by a.batch_date";
				}
				else
				{
					//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS
					//echo $po_id_cond ;
					$sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $po_id_cond $ext_no $floor_num $year_cond $comp_cond  GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $booking_num  $batch_num $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}
			}

			/*
			|--------------------------------------------------------------------------
			| Subcond Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==2)
			{
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
					$sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, a.entry_form,b.rec_challan, a.process_id, a.remarks order by a.batch_date ";
				}
				else
				{
					$sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";

				}
			// echo $sub_cond;die;
			}

			/*
			|--------------------------------------------------------------------------
			| Sample Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==3)
			{
				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="" )
				{
					//echo $order_no;
			 		$sql_sam="SELECT a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and c.id=b.po_id and c.job_no_mst=d.job_no and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $booking_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
				}
				else
				{*/
					//echo $$job_number_id .'aaaa';
				  	$sql_sam="(SELECT a.id,a.batch_against,a.entry_form, a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $po_id_cond $comp_cond $dates_com  $batch_num $booking_num $po_id_cond  $non_booking_cond_for $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no,a.booking_without_order, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a ,wo_non_ord_samp_booking_mst c where  a.id=b.mst_id and c.booking_no=a.booking_no  and a.entry_form=0 and a.batch_against=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $samp_buyercond  $comp_cond $dates_com  $ref_cond2 $batch_num $booking_num $ext_no $floor_num $year_cond $non_booking_cond_for GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.booking_without_order,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
					//echo $sql_sam;die;//and b.po_id=0
				//}
				// echo $sql_sam;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| wait for Heat Setting
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==2)
		{
			if($batch_type==1)// Self batch
			{
				//echo "dsd";
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				if($w_heat_arr!=0)
				{
					$sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks order by a.batch_date ";
				} //echo $sql;

			} //Batch Type End
			if($batch_type==2) //Subcond batch
			{
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				if($w_heat_arr!=0)
				{
					$sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
					$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan, a.process_id, a.remarks order by a.batch_date";

				}
				//echo $sub_cond;
			}//Batch type End

			if($batch_type==0) // Self and Subcond batch
			{
				// Self batch
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				//echo $row_heat.'dd';;
				//if($row_heat)
				//{
					$sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					if($row_heat)
					{
					$p=1;
						foreach($w_heat_arr as $h_batch_id)
						{
							if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
							$p++;
						}
						$sql .=")";
					}

					$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks order by a.batch_date ";
				//}
				// echo $sql;

				//	Subcond batch
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);

					$sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
				if($w_heat_arr!=0)
				{
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
				}

					$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan order by a.batch_date";

				//}
				//echo $sub_cond;
			} // end
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Dyeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==3)
		{
			if($batch_type==1)//Self Batch
			{
				//echo $row_d.'sdd';
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
						$sql .=")";
						$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
						union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date ";
						//echo $sql;die;
				}
			}//Self batch End
			if($batch_type==2) //SubCon Batch
			{

				$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				if($w_dyeing_arr!=0)
				{
					//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,count(b.roll_no) as roll_no,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
					$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";
					$p2=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p2++;
					}
					$sub_cond .=")";
					$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id,b.item_description,b.po_id,b.prod_id,
					b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by a.batch_date ";

				}//echo $sub_cond;
			}
			// echo $sub_cond;//die;
			if($batch_type==0) //Self Batch and SubCon Batch
			{
				// Self
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				//print_r($w_dyeing_arr);
				///echo $row_d.'DSDS';
				//die;
				if($w_dyeing_arr!=0)
				{
					$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					if($row_d>0)
					{
						$p=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p++;
						}
						$sql .=")";
					}


						$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
						union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not
						";

						if($row_d)
						{
							$p1=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p1==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p1++;
						}
						$sql .=")";
						}
						$sql .="  GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date
						";
					//echo $sql;
					/*

					union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not
						";

						if($row_d)
						{
							$p1=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p1==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p1++;
						}
						$sql .=")";
						}
						$sql .="  GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date


					union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) */
						//ISsue id=23236
					//echo $sql;//die;
				}

				// Subcon
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				if($w_dyeing_arr!=0)
				{
					//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
				if($sub_cond_d>0)
					{
						$p=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p++;
						}
						$sub_cond .=")";
					}
					$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";

					if($sub_cond_d)
					{
						$p2=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p2++;
					}
					$sub_cond .=")";
					}
					$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id,b.item_description,b.po_id,b.prod_id,
					b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				} //echo $sub_cond;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Re-Dyeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==4)
		{

			if($batch_type==1)//Self Batch
			{
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 { //echo $d_batch_id;die;
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,d.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num ";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 {
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}
			}
			if($batch_type==2) //SubCon Batch
			{
				$w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				// print_r( $w_dyeing_subcon);
				if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d,
					$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $subcon_batch_id)
				 	{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
						$p++;
				 	}
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $booking_num $sub_buyer_cond  $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $d_batch_id)
					{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}

					 //echo $sql;
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				}
			}
			//echo $sql_subcon;

			if($batch_type==0) //Self Batch with SubCon Batch
			{
				//Self Batch
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 { //echo $d_batch_id;die;
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,d.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num ";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 {
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}

				//SubCon Batch
				$w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				// print_r( $w_dyeing_subcon);
				if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d,
					$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $subcon_batch_id)
				 	{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
						$p++;
				 	}
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $booking_num $sub_buyer_cond  $suborder_no $ext_no $floor_num $year_cond $comp_cond";

					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $d_batch_id)
					{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
					$sql_subcon .=")";
					}

					 //echo $sql;

					$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";

				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Singeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==5)
		{
			if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)";
			else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
			$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
			if($w_sing_arr!=0)
			{
				$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond  ";
				if($row_sin>0)
					{
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					}
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num $year_cond ";
				if($row_sin>0)
					{
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					}
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				//echo $sql;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Date Wise Report
	|--------------------------------------------------------------------------
	|
	*/
	//echo $sql;
	if($cbo_type==1)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
			$sam_batchdata=sql_select($sql_sam);
		}
		else if($batch_type==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
			//print_r($batchdata);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;die;
			$sub_batchdata=sql_select($sub_cond);
			// echo $sub_cond;die;
		}
		else if($batch_type==3)
		{
			//echo $sql_sam;die;
			$sam_batchdata=sql_select($sql_sam);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| wait for Heat Setting
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==2)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			$sub_batchdata=sql_select($sub_cond);
			// echo $sub_cond;die;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Dyeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==3)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Re-Dyeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==4)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sql_subcon);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sql_subcon);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Singeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==5)
	{
		if($batch_type==1)
		{
		$batchdata=sql_select($sql);
		}
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
		}
		/*else if($batch_type==0 || $batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}*/
	}

	/*
	|--------------------------------------------------------------------------
	| for self batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$batch_id_arr = array();$po_id_arr = array();
	foreach ($batchdata as $val)
	{
		$batch_id_arr[$val[csf('id')]]=$val[csf('id')];
		$po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
		//$batch_color_arr[$val[csf('id')]]=$val[csf('color_id')];
	}
	//print_r($batch_color_arr);
	$batchIds = implode(",", $batch_id_arr);


	/*
	|--------------------------------------------------------------------------
	| for subcon batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$subcon_batch_id_arr = array();
	foreach ($sub_batchdata as $val)
	{
		$subcon_batch_id_arr[$val[csf('id')]]=$val[csf('id')];
	}
	$subconBatchIds = implode(",", $subcon_batch_id_arr);



	/*
	|--------------------------------------------------------------------------
	| for sample batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$sample_batch_id_arr = array();
	foreach ($sam_batchdata as $val)
	{
		$sample_batch_id_arr[$val[csf('id')]]=$val[csf('id')];
		$po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
	}
	$sampleBatchIds = implode(",", $sample_batch_id_arr);



	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 3, $batch_id_arr, $empty_arr);//Batch id Ref from=3
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 4, $po_id_arr, $empty_arr);//Po id Ref from=4
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 5, $subcon_batch_id_arr, $empty_arr);//Po id Ref from=5
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 6, $sample_batch_id_arr, $empty_arr);//Po id Ref from=6


	$sub_po_array=array();
	$sub_po_sql=sql_select("select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d,gbl_temp_engine g  where d.subcon_job=c.job_no_mst and  a.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=5 and g.entry_form=15 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond");
	$sub_poid='';
	foreach($sub_po_sql as $row)
	{
		$sub_po_array[$row[csf('id')]]['po_no']=$row[csf('order_no')];
		$sub_po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$sub_po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
		if($sub_poid=='') $sub_poid=$row[csf('id')]; else $sub_poid.=",".$row[csf('id')];
	}


	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/
	if($db_type==0)
	{
		if($batchIds!="")
		{
			//$sql_yarn_lot = "SELECT a.id, group_concat(d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  GROUP BY a.id";
			$sql_yarn_lot = "SELECT b.prod_id, b.po_id, group_concat(d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  GROUP BY a.id";
			$sql_yarn_lot_res = sql_select($sql_yarn_lot);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		/*
		|--------------------------------------------------------------------------
		| for self batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($batchIds != '')
		{
			//$sql_yarn_lot = "SELECT a.id,  LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id";
			/*$sql_yarn_lot = "SELECT b.prod_id, b.po_id, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY b.prod_id, b.po_id"; */
			$sql_yarn_lot = "SELECT b.mst_id as id,b.prod_id, b.po_id, d.yarn_lot AS yarn_lot FROM  pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,gbl_temp_engine g WHERE  b.roll_id = c.id AND c.dtls_id = d.id and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=15  AND b.status_active = 1 AND b.is_deleted = 0  GROUP BY b.mst_id,b.prod_id, b.po_id,d.yarn_lot";
			$sql_yarn_lot_res = sql_select($sql_yarn_lot);
		}

		/*
		|--------------------------------------------------------------------------
		| for subcon batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($subconBatchIds != '')
		{
		/* $subconYarnLotSql = "SELECT b.prod_id, b.po_id,d.fabric_description, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_production_dtls d WHERE a.id = b.mst_id AND TO_CHAR(b.po_id) = d.order_id  and b.item_description=d.fabric_description  AND a.company_id = ".$company." AND a.id IN(".$subconBatchIds.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.po_id,d.fabric_description"; */
		 $subconYarnLotSql = "SELECT a.id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_production_dtls d,gbl_temp_engine g WHERE a.id = b.mst_id AND TO_CHAR(b.po_id) = d.order_id  and b.item_description=d.fabric_description and a.id=g.ref_val and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=5 and g.entry_form=15  AND a.company_id = ".$company."  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.po_id,a.id";
			$subconYarnLotRslt = sql_select($subconYarnLotSql);
		}

		/*
		|--------------------------------------------------------------------------
		| for sample batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($sampleBatchIds != '')
		{
			/*$sampleYarnLotSql = "SELECT b.prod_id, b.po_id, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = ".$company." AND a.id in(".$sampleBatchIds.") AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY b.prod_id, b.po_id"; */
			$sampleYarnLotSql = "SELECT b.mst_id as id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot FROM  pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,gbl_temp_engine g WHERE   b.roll_id = c.id AND c.dtls_id = d.id and b.mst_id=g.ref_val   and g.user_id = ".$user_id." and g.ref_from=6 and g.entry_form=15  AND b.status_active = 1 AND b.is_deleted = 0   GROUP BY b.mst_id,b.prod_id, b.po_id,d.yarn_lot";
			$sampleYarnLotRslt = sql_select($sampleYarnLotSql);
		}
	}
	 // inv_receive_master e  AND e.entry_form IN (2, 22) AND a.id = 65338

	/*
	|--------------------------------------------------------------------------
	| for self batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$yarn_lot_arr=array();
	foreach($sql_yarn_lot_res as $rows)
	{
		//$yarn_lot_arr[$rows[csf('id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
	}
	/*echo "<pre>";
	print_r($yarn_lot_arr);
	echo "</pre>";*/

	/*
	|--------------------------------------------------------------------------
	| for subcon batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$subcon_yarn_lot_arr=array();
	foreach($subconYarnLotRslt as $rows)
	{
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
	}
	//echo "<pre>";
	//print_r($subcon_yarn_lot_arr);

	/*
	|--------------------------------------------------------------------------
	| for sample batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$sample_yarn_lot_arr=array();
	foreach($sampleYarnLotRslt as $rows)
	{
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
	}
	//echo "<pre>";
	//print_r($sample_yarn_lot_arr);
	$batch_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company."' and module_id=7 and report_id=56 and is_deleted=0 and status_active=1");
    $batch_format_ids=explode(",",$batch_print_report_format);
    $print_btn=$batch_format_ids[0];

	$roll_level= sql_select("select fabric_roll_level from variable_settings_production where company_name='$company' and item_category_id=50 and variable_list=3 and status_active=1 and is_deleted= 0 order by id");

	foreach($roll_level as $row)
	{
		$roll_maintained = $row[csf('fabric_roll_level')];
	}

	if ($roll_maintained == "" || $roll_maintained == 2) $roll_maintained = 0; else $roll_maintained = $roll_maintained;
	//fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 6, $sample_batch_id_arr, $empty_arr);//Po id Ref from=6
	$re_dyeing_from = return_library_array("select b.re_dyeing_from from pro_batch_create_mst b,gbl_temp_engine g where  b.re_dyeing_from=g.ref_val   and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=15 and b.re_dyeing_from <>0 and b.status_active = 1 and b.is_deleted = 0","re_dyeing_from","re_dyeing_from");
	$load_unload = return_library_array("select b.id, b.batch_id from pro_fab_subprocess b,gbl_temp_engine g where  b.batch_id=g.ref_val   and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=15 and b.load_unload_id=2 and b.entry_form=35 and b.status_active=1","batch_id","batch_id");

	$booking_qnty_arr=array();
    $queryFab=sql_select("select a.booking_type,b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine g where a.booking_no=b.booking_no and b.po_break_down_id=g.ref_val   and g.user_id = ".$user_id." and g.ref_from=4 and g.entry_form=15  and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0   group by a.booking_type, b.po_break_down_id,a.booking_no, b.fabric_color_id");



    foreach($queryFab as $row)
    {
		$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
		if($row[csf('booking_type')]==4)
		{
		 $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
		}
		//$booking_qnty_arr2[$row[csf('po_break_down_id')]][$row[csf('booking_no')]]+=$row[csf('grey_fab_qnty')];
    }
	unset($queryFab);

	$sql_non_order="SELECT c.id,c.company_id,c.grouping as samp_ref_no,c.style_desc, c.buyer_id as buyer_name, b.booking_no, b.bh_qty, b.style_id
	from wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls b ,pro_batch_create_mst d,gbl_temp_engine g
	where c.booking_no=b.booking_no and d.booking_no_id=c.id and  d.id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=6 and g.entry_form=15  and c.booking_type=4 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
	// echo $sql_non_order;die;
	$result_sql_order=sql_select($sql_non_order);
	foreach($result_sql_order as $row)
	{
		$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
	}

	ob_start();
	?>
	<div align="center">
	<fieldset style="width:1375px;">
	<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type]; ?> </strong>
		<br><b>
		<?
		//echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
		echo ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
		?> </b>
 	</div>
 	<div align="center">
  	<?php
	/*
	|--------------------------------------------------------------------------
	| All Batch
	|--------------------------------------------------------------------------
	|
	*/
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4,5,6,7,8,9) and ENTRY_FORM=15");
	oci_commit($con);

	if($batch_type==0)
  	{
  		?>
	 	<div align="left"><b>Self Batch</b>
		 	<table class="rpt_table" width="1990" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Against</th>
		                <th width="80">Batch Color</th>
		                <th width="80">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
                    	<th width="100">Pub ship date</th>
		                <th width="70">File No</th>
		                <th width="70">Ref. No</th>
		                <th width="80">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="100">Construction</th>
		                <th width="150">Composition</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
		                <th width="50">Trims Weight</th>
										<th width="50">Total Roll</th>
		                <th width="100">Booking Grey Req.Qty</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
		 	</table>
			<div style=" max-height:350px; width:1990px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
					<?
				//$po_cond_in=where_con_using_array($po_id_arr,0,'b.po_break_down_id');



              /*  $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($smn_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($sam_query as $row)
                {
                  $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }*/
				/*$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4,5,6,7) and ENTRY_FORM=15");
				oci_commit($con);*/
                $i=1;
                $f=0; $bb=0;
                $b=0;
                $btq=0;$re_dyeing_batch_qty=0;$without_ext_btq=0;
                $tot_book_qty=0;  $tot_batch_wgt=0;$tot_trims_wgt=0;$total_tot_batch_wgt=0;$total_tot_trims_wgt=0;
                $tot_grey_req_qty=0;
                $batch_chk_arr=array();
                $booking_chk_arr=array();
                foreach($batchdata as $batch)
                {
                  	// echo $batch[csf('booking_no')].'dd';
			    	$sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
                    //if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
                    // echo $sam_booking.'!='.$batch[csf('booking_no')].'<br>';
                    if($sam_booking!=$batch[csf('booking_no')])
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $order_id=$batch[csf('po_id')];
                        $color_id=$batch[csf('color_id')];
                        $booking_no=$batch[csf('booking_no')];
                        $booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
                        $desc=explode(",",$batch[csf('item_description')]);
                        $entry_form=$batch[csf('entry_form')];
                        $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
                        $po_num='';
                        $po_file='';
                        $po_ref='';
                        $job_num='';
                        $job_buyers='';
                        $yarn_lot_num='';
                        $grey_booking_qty=0;
                        $buyer_style=''; $ship_DateCond='';
                        $tot_book_qty=0;

                        /*print_r($po_ids);die;*/
                        foreach($po_ids as $p_id)
                        {
                        	//echo $batch[csf('prod_id')]."=".$p_id."<br>";
                        	//echo $yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot']."<br><br>";
                        	if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
							if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
							if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
							if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
							if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
							//$ylot=rtrim($yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'],',');
							$ylot=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
							if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
							$booking_color=$p_id."**".$booking_no."**".$color_id;//$order_id.$booking_no.$color_id;
							if (!in_array($booking_color,$booking_chk_arr))
							{
							    $bb++;
							    $booking_chk_arr[]=$booking_color;
							    $grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];
							}




							//echo $p_id.'='.$booking_no.'='.$color_id.',';
							if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
							$pub_shipment_date=$po_array[$p_id]['pub_shipment_date'];

							if($ship_DateCond=='') $ship_DateCond=$pub_shipment_date;else $ship_DateCond.=",".$pub_shipment_date;
                        }
                        $yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));

						/* $buyer_po=""; $buyer_style="";
						$buyer_po_id=explode(",",$row[csf('po_id')]);*/
						/*foreach($po_ids as $p_id)
						{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
						}
						$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
						$buyer_style=implode(",",array_unique(explode(",",$buyer_style)))*/;//add by samiur

						// $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')])));
						// $book_qty+=$booking_qty;
						$tot_book_qty=$grey_booking_qty;


					 	$batch_id=$batch[csf('id')];
                        if (!in_array($batch_id,$batch_wgt_chk_arr))
                        {
                            $b++;
                            $batch_wgt_chk_arr[]=$batch_id;
                            $tot_batch_wgt=$batch[csf('batch_weight')];
                            $tot_trims_wgt=$batch[csf('total_trims_weight')];
                        }
                        else
                        {
                            $tot_batch_wgt=0;
                            $tot_trims_wgt=0;
                        }

                      	// echo  $batch[csf('id')].'dd';;

                        $process_name = '';
						$process_id_array = explode(",", $batch[csf("process_id")]);
						foreach ($process_id_array as $val)
						{
							if ($process_name == ""){
								$process_name = $conversion_cost_head_array[$val];
							}
							else{
								$process_name .= "," . $conversion_cost_head_array[$val];
							}
						}

                       ?>
                       <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            <?
							if (!in_array($batch[csf('id')],$batch_chk_arr) )
				            {
								if($re_dyeing_from[$batch[csf('id')]])
								{
									$ext_from = $re_dyeing_from[$batch[csf('id')]];
								}else{
									$ext_from = "0";
								}
								if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
								{
									$exists_extention_no = $batch[csf("extention_no")];
									if($exists_extention_no>0)
									{
										$extention_no = $exists_extention_no+1;
									}
									else
									{
										$extention_no = 1;
									}
								}
								else
								{
									if ($batch[csf("extention_no")] == 0)
										$extention_no = '';
									else
										$extention_no = $batch[csf("extention_no")];
								}
	                            $f++;
	                            ?>
	                            <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
	                            <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
								<td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>">
								<A href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>','<? echo $batch[csf('entry_form')]?>')"><?echo $batch[csf('batch_no')]; ?></A>
								<!-- <p><? echo $batch[csf('batch_no')]; ?></p> -->
								</td>
	                            <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
	                             <td style="word-break:break-all;"  width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
	                            <td style="word-break:break-all;" title="<? echo $order_id.'='.$batch[csf('color_id')];?>" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
	                            <td style="word-break:break-all;" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers; ?></div></td>
	                            <?
	                            $batch_chk_arr[]=$batch[csf('id')];
	                           // $book_qty+=$booking_qty;
	                        }
	                        else
	                        {
	                            ?>
	                            <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
	                            <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
	                            <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
	                            <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
	                            <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                            <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                            <td style="word-break:break-all;"  width="80"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
	                            <?
	                        }
	                        ?>

	                        <td style="word-break:break-all;" width="80"><p><? echo $buyer_style; ?></p></td>
	                        <td style="word-break:break-all;" width="120"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
	                        <td style="word-break:break-all;" width="100"><p><? echo $ship_DateCond;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
	                        <td style="word-break:break-all;" width="70"><p><? echo $po_file; ?></p></td>
	                        <td style="word-break:break-all;" width="70"><p><?  echo $po_ref; ?></p></td>
	                        <td style="word-break:break-all;" width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                        <td style="word-break:break-all;" width="70"><p><? echo $job_num; ?></p></td>
	                        <td style="word-break:break-all;" width="100"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
	                        <td style="word-break:break-all;" width="150"><div style="width:150px; word-wrap:break-word;"><?

	                        if($desc[4]!="")
	                        {
	                        	$compositions= $desc[1].' ' . $desc[2];
	                        	$gsms= $desc[3];
	                        }
	                        else
	                        {
	                        	$compositions= $desc[1];
	                        	$gsms= $desc[2];
	                        }

	                        echo $compositions;

	                        ?></div></td>
	                        <td style="word-break:break-all;"  width="50"><p><? echo end($desc);//$desc[3]; ?></p></td>
	                        <td style="word-break:break-all;"  width="50"><p><? echo  $gsms;//$desc[2]; ?></p></td>
	                        <td style="word-break:break-all;"  title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
	                        <td style="word-break:break-all;" align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><?
	                        if ($batch[csf('extention_no')]!="")
	                        {
	                         	$re_dyeing_batch_qty+=$batch[csf('batch_qnty')];
	                        }
	                        else
	                        {
	                        	$without_ext_btq+=$batch[csf('batch_qnty')];
	                        }
	                        echo number_format($batch[csf('batch_qnty')],2);
	                        ?></td>
	                        <td style="word-break:break-all;"  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($tot_batch_wgt,2); ?></td>
	                        <td style="word-break:break-all;"  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($tot_trims_wgt,2); ?></td>
							<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
	                        <td style="word-break:break-all;"  align="right" width="100" title="Booking Color Wise Qty"><? echo number_format($tot_book_qty,2);?></td>
	                        <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')];?></td>
	                        <td style="word-break:break-all;"><? echo $process_name;?></td>
	                    </tr>
                        <?
                        $i++;
                        $btq+=$batch[csf('batch_qnty')];
                        $tot_grey_req_qty+=$tot_book_qty;
					 	$total_tot_batch_wgt+=$tot_batch_wgt;
						$total_tot_trims_wgt+=$tot_trims_wgt;
                        $balance=$tot_grey_req_qty-$btq;
                        $bal_qty=$balance;
                        if($bal_qty>0)
                        {
                        	$color="";
                        	$txt="Over Batch Qty";
                        }
                        else if($bal_qty<0)
                        {
                        	$color="red";
                        	$txt="Below Batch Qty";
                        }
                    }
                }
                ?>
			 	</tbody>
				</table>
				<table class="rpt_table" width="1970"  cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
                             <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="80">&nbsp;</th>

			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" title="<?=$btq?>" id="value_total_btq"><? echo number_format($btq,2); ?></th>
			                <th width="50"  align="right"><? echo number_format($total_tot_batch_wgt,2); ?></th>
			                <th width="50"  align="right"><? echo number_format($total_tot_trims_wgt,2); ?></th>
											<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="10" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			            <tr>
			                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="10" align="left">&nbsp; <? echo number_format($without_ext_btq,2); ?> </td>
			            </tr>
			            <tr>
			                <td colspan="13" align="right" style="border:none;"> <b>Redyeing Batch Qty.</b></td>
			                <td colspan="10" align="left">&nbsp; <? echo number_format($re_dyeing_batch_qty,2); ?> </td>
			            </tr>
			            <tr>
			                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="10" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
				</table>
			</div> <br/>
		</div>

		<div align="left"> <b>SubCond Batch </b>
		 	<table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Aganist</th>
		                <th width="80">Batch Color</th>
		                <th width="50">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
		                <th width="80">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="80">Recv. Challan</th>
		                <th width="150">Fabrics Desc.</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
						<th width="50">Total Roll</th>
		                <th width="100">Material Recv Grey Req. Qty</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
			</table>
			<div style=" max-height:350px; width:1650px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body2" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tbody>
						<?
						/*$booking_qnty_arr=array();
						$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
						foreach($query as $row)
						{
							$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
						}*/


						$sub_material_recv_arr=array();$sub_material_description_arr=array();
						$subcon_sql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, sum(b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0 group by b.order_id, a.chalan_no,b.color_id,a.trans_type,b.material_description,b.gsm");
						foreach($subcon_sql as $row)
						{
							$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
						}

						//var_dump($sub_material_description_arr);die;
						$i=1;
						$f=0;
						$btq_subcon=0; $k=0;$re_dyeing_batch_qty=0;$without_ext_sub_btq=0;
						$book_qty_subcon=0;$subcon_tot_book_qty=$sub_tot_batch_wgt=0;$sub_batch_wgt_chk_arr=array();
						$total_sub_tot_batch_wgt=0;
						$batch_chk_arr=array();$sub_qty_chk_arr=array();
						// print_r($sub_batchdata);
						foreach($sub_batchdata as $batch)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$order_id_sub=$batch[csf('po_id')];
							$color_id=$batch[csf('color_id')];
							$booking_no=$batch[csf('booking_no')];
							$sub_challan=$batch[csf('rec_challan')];
							$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
							$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
							$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
							$desc=explode(",",$batch[csf('item_description')]);
							$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
							$entry_form=$batch[csf('entry_form')];
							$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
							$sub_po_num='';
							$sub_job_buyers='';
							$sub_job_buyers='';$subcon_yarn_lot_num='';
							$subcon_booking_qty=0;
							$sub_buyer_style='';
							foreach($po_ids as $p_id)
							{
								if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
								if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
								if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$sub_po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$sub_po_array[$p_id]['buyer']];
								$subcon_booking_qty+=$sub_material_recv_arr[$p_id];

								//for yarn lot
								//echo $subcon_yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'].', ';
								$subconYlot=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');

								if($sub_buyer_style=="") $sub_buyer_style=$sub_po_array[$p_id]['style_no']; else $sub_buyer_style.=','.$sub_po_array[$p_id]['style_no'];
								//echo $subconYlot.', ';
								/*if($subcon_yarn_lot_num=='')
									$subcon_yarn_lot_num=$subconYlot;
								else
									$subcon_yarn_lot_num.=",".$subconYlot;*/
									  if($subcon_yarn_lot_num=='') $subcon_yarn_lot_num=$subconYlot;else $subcon_yarn_lot_num.=",".$subconYlot;
							}
							$subcon_yarn_lots=implode(",",array_unique(explode(",",$subcon_yarn_lot_num)));
							$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
							if (!in_array($booking_color2,$sub_qty_chk_arr))
							{
								$k++;
								//echo $subcon_booking_qty;
								$sub_qty_chk_arr[]=$booking_color2;
								$subcon_tot_book_qty=$subcon_booking_qty;
							}
							else
							{
								 $subcon_tot_book_qty=0;
							}

							 $batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$sub_batch_wgt_chk_arr))
                            {
                                $k++;
                                $sub_batch_wgt_chk_arr[]=$batch_id;
                                $sub_tot_batch_wgt=$batch[csf('batch_weight')];
                            }
                            else
                            {
                                $sub_tot_batch_wgt=0;
                            }

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}

							//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot'];
							//echo "<pre>". $batch[csf('prod_id')].'='.$order_id_sub;
							//print_r($yarn_lot_arr);
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            	<? if (!in_array($batch[csf('id')],$batch_chk_arr) )
								{
									if($re_dyeing_from[$batch[csf('id')]])
									{
										$ext_from = $re_dyeing_from[$batch[csf('id')]];
									}else{
										$ext_from = "0";
									}
									if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
									{
										$exists_extention_no = $batch[csf("extention_no")];
										if($exists_extention_no>0)
										{
											$extention_no = $exists_extention_no+1;
										}
										else
										{
											$extention_no = 1;
										}
									}
									else
									{
										if ($batch[csf("extention_no")] == 0)
											$extention_no = '';
										else
											$extention_no = $batch[csf("extention_no")];
									}
									$f++;
											?>
					                <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
					                <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
					                <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><A href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>','<? echo $batch[csf('entry_form')]?>')"><?echo $batch[csf('batch_no')]; ?></A></td>
					                <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
					                 <td style="word-break:break-all;" width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
					                <td style="word-break:break-all;" width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;" width="50"><p><? echo $sub_job_buyers; ?></p></td>
									<?
					                $batch_chk_arr[]=$batch[csf('id')];

				                }
								else
								{ ?>
					                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
					                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
					                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
					                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
					                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;"  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
									<?
								}
								?>
								<td style="word-break:break-all;" width="80"><p><? echo $sub_buyer_style; ?></p></td>
				                <td style="word-break:break-all;" width="120"><p><? echo $sub_po_num; ?></p></td>
				                <td style="word-break:break-all;" width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? echo implode(",",array_unique(explode(",",$sub_job_num))); ?></p></td>
				                <td style="word-break:break-all;" width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
				                <td width="150" ><p><? echo $batch[csf('item_description')];//$item_descript; ?></p></td>
				                <td style="word-break:break-all;"  width="50" title="<? echo $desc[2];  ?>"><p><? echo $batch[csf('grey_dia')]; ?></p></td>
				                <td  style="word-break:break-all;" width="50"><p><? echo $batch[csf('gsm')]; ?></p></td>
				                 <td style="word-break:break-all;" align="left" width="60"><p><? echo $subcon_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>
				                <td style="word-break:break-all;" align="right" width="70" ><?
				                if ($batch[csf('extention_no')]!="")
		                        {
		                         	$re_dyeing_batch_qty+=$batch[csf('sub_batch_qnty')];
		                        }
		                        else
		                        {
		                        	$without_ext_sub_btq+=$batch[csf('sub_batch_qnty')];
		                        }
		                        echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
				                <td style="word-break:break-all;"  align="right" width="50" title="<? echo $sub_tot_batch_wgt; ?>"><? echo number_format($sub_tot_batch_wgt,2); ?></td>
								<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
				                <td style="word-break:break-all;" align="right" width="100" title="SunCon Material Recv Qty"><? echo number_format($subcon_tot_book_qty,2); ?></td>
				                <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')]; ?></td>
				                <td style="word-break:break-all;"><? echo $process_name; ?></td>
				            </tr>
							<?
			                $i++;
			                $btq_subcon+=$batch[csf('sub_batch_qnty')];
							$book_qty_subcon+=$subcon_tot_book_qty;
							$total_sub_tot_batch_wgt+=$sub_tot_batch_wgt;
			                $balance=$book_qty_subcon-$btq_subcon;
			                $bal_qty_subcon=$balance;
			                if($bal_qty_subcon>0)
			                {
			                $color="";
			                $txt="Over Batch Qty";
			                }
			                else if($bal_qty_subcon<0)
			                {
			                $color="red";
			                $txt="Below Batch Qty";
			                }
			            }
			        	 ?>
			    </tbody>
				</table>
				<table class="rpt_table" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="80">&nbsp;</th>

			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_btq_subcon"><? echo $btq_subcon; ?></th>
			                <th width="50" align="right"><? echo $total_sub_tot_batch_wgt; ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo $book_qty_subcon; ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" title="SunCon Material Recv Qty" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="9" align="left">&nbsp;
			                 <? echo number_format($book_qty_subcon,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="9" align="left">&nbsp; <? echo number_format($without_ext_sub_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Redyeing Batch Qty.</b></td>
			                <td colspan="9" align="left">&nbsp; <? echo number_format($re_dyeing_batch_qty,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="9" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
				</table>
			</div>
		</div>

		<div align="left"><b>Sample Batch</b>
			<table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Aganist</th>
		                <th width="80">Batch Color</th>
		                <th width="50">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
		                <th width="70">File No</th>
		                <th width="70">Ref No</th>
		                <th width="100">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="100">Construction</th>
		                <th width="150">Composition</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
		                <th width="50">Trims Weight</th>
										<th width="50">Total Roll</th>
		                <th width="100">Booking Grey Req.Qty</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
			</table>
			<div style=" max-height:350px; width:1880px; overflow-y:scroll;" id="scroll_body">
			 	<table class="rpt_table" id="table_body3" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
						<?
						$sam_booking_qnty_arr=array();
						$sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
						foreach($sam_query as $row)
						{
							$sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
						}

						$smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
						foreach($smn_query as $row)
						{
							$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}
						$sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
						foreach($sam_query as $row)
						{
							$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}

						// GETTING BUYER NAME
						/*$non_order_arr=array();
			            $sql_non_order="SELECT a.company_id,a.grouping as smp_ref, a.buyer_id as buyer_name, b.booking_no, b.bh_qty
			            from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			            where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			            $result_sql_order=sql_select($sql_non_order);
			            foreach($result_sql_order as $row)
			            {

							$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
							$non_order_arr[$row[csf('booking_no')]]['smp_ref']=$row[csf('smp_ref')];
							$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
			            }*/
			            // echo "<pre>";
			            // print_r($non_order_arr);

						$i=1;
						$f=0;
						$b=0;
						$btq=0;$bb=0;$re_dyeing_batch_qty=0;$without_ext_samp_btq=0;
						$tot_book_qty2=0;
						$tot_grey_req_qty=$samp_tot_batch_wgt=$samp_tot_trims_wgt=0;
						$batch_chk_arr=array();
						$booking_chk_arr2=array();$samp_batch_wgt_chk_arr=array();
						// print_r($sam_batchdata );
						foreach($sam_batchdata as $batch)
						{
							$sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
							if($sam_booking==$batch[csf('booking_no')])
							{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
							$po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$sam_booking_qty=0;
							$sample_yarn_lot_num="";$buyer_style='';
							foreach($po_ids as $p_id)
							{
								if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
								if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
								if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
								if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
								if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];

								if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];

								$sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
								//for yarn lot
								$sampleYlot=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
								/*if($sample_yarn_lot_num=='')
									$sample_yarn_lot_num=$sampleYlot;
								else
									$sample_yarn_lot_num.=",".$sampleYlot;*/
									if($sample_yarn_lot_num=='') $sample_yarn_lot_num=$sampleYlot;else $sample_yarn_lot_num.=",".$sampleYlot;
							}

							$sample_yarn_lots=implode(",",array_unique(explode(",",$sample_yarn_lot_num)));
							$order_id=$batch[csf('po_id')];
							$color_id=$batch[csf('color_id')];
							$booking_no=$batch[csf('booking_no')];
							//$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];

							$desc=explode(",",$batch[csf('item_description')]);
							$entry_form=$batch[csf('entry_form')];
							//$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
							//if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
							//if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
							//$po_file=$po_array[$order_id]['file'];
							//$po_ref=$po_array[$order_id]['ref'];
							// $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
							$booking_color=$booking_no;
							if (!in_array($booking_color,$booking_chk_arr2))
							{
								$bb++;
								$booking_chk_arr2[]=$booking_color;
								$tot_book_qty2=$sam_booking_qty;
							}
							else
							{
								$tot_book_qty2=0;
							}
							$batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$samp_batch_wgt_chk_arr))
                            {
                                $b++;
                                $samp_batch_wgt_chk_arr[]=$batch_id;
                                $samp_tot_batch_wgt=$batch[csf('batch_weight')];
                                $samp_tot_trims_wgt=$batch[csf('total_trims_weight')];
                            }
                            else
                            {
                                $samp_tot_batch_wgt=0;
                                $samp_tot_trims_wgt=0;
                            }

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            <? if (!in_array($batch[csf('id')],$booking_chk_arr2))
								{
									if($re_dyeing_from[$batch[csf('id')]])
									{
										$ext_from = $re_dyeing_from[$batch[csf('id')]];
									}else{
										$ext_from = "0";
									}
									if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
									{
										$exists_extention_no = $batch[csf("extention_no")];
										if($exists_extention_no>0)
										{
											$extention_no = $exists_extention_no+1;
										}
										else
										{
											$extention_no = 1;
										}
									}
									else
									{
										if ($batch[csf("extention_no")] == 0)
											$extention_no = '';
										else
											$extention_no = $batch[csf("extention_no")];
									}
									$f++;
									?>
                                    <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
                                    <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                    <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><A href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>','<? echo $batch[csf('entry_form')]?>')"><?echo $batch[csf('batch_no')]; ?></A></td>
                                    <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                    <td style="word-break:break-all;" width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                    <td style="word-break:break-all;" width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                    <td style="word-break:break-all;" width="50"><p><? echo $job_buyers.$buyer_arr[$non_order_arr[$batch[csf('booking_no')]]['buyer_name']]; ?></p></td>
                                    <?
                                    $batch_chk_arr[]=$batch[csf('id')];
				               		// $book_qty+=$booking_qty;
				                  }
								else
								  { ?>
				                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
				                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
				                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
				                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
				                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td style="word-break:break-all;"  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
								<? }
								$samp_ref_no=$non_order_arr[$batch[csf('booking_no')]]['samp_ref_no'];
								$booking_without_order=$batch[csf('booking_without_order')];
								?>
								<td style="word-break:break-all;" width="80"><p><? if($booking_without_order==1) echo $non_order_arr[$batch[csf('booking_no')]]['style_desc'];else echo $buyer_style; ?></p></td>
				                <td style="word-break:break-all;" width="120" ><p><? echo $po_num; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? echo $po_file; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? if($booking_without_order==1) echo $samp_ref_no; else echo $po_ref; ?></p></td>
				                <td style="word-break:break-all;" width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
				                <td style="word-break:break-all;" width="70" title="<? echo $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $job_num; ?></p></td>
				                <td style="word-break:break-all;" width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0]; ?></p></td>
				                <td style="word-break:break-all;" width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
				                <td style="word-break:break-all;" width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
				                <td style="word-break:break-all;" width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
				                 <td style="word-break:break-all;" align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $sample_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
				                <td style="word-break:break-all;" align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><?
				                if ($batch[csf('extention_no')]!="")
		                        {
		                         	$re_dyeing_batch_qty+=$batch[csf('batch_qnty')];
		                        }
		                        else
		                        {
		                        	$without_ext_samp_btq+=$batch[csf('batch_qnty')];
		                        }
				                echo number_format($batch[csf('batch_qnty')],2);  ?></td>
				                <td style="word-break:break-all;" align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($samp_tot_batch_wgt,2); ?></td>
				                <td style="word-break:break-all;" align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($samp_tot_trims_wgt,2); ?></td>
								<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
				                <td title="Booking Color Wise Qty" style="word-break:break-all;" align="right" width="100"><? echo number_format($tot_book_qty2,2);?></td>
				                <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')];?></td>
				                <td style="word-break:break-all;"><? echo $process_name;?></td>
				            </tr>
							<?
				                $i++;
				                $sam_btq+=$batch[csf('batch_qnty')];
								$tot_grey_req_qty+=$tot_book_qty2;
								$total_samp_tot_batch_wgt+=$samp_tot_batch_wgt;
								$total_samp_tot_trims_wgt+=$samp_tot_trims_wgt;
				                $sam_balance=$tot_grey_req_qty-$sam_btq;
				                $sam_bal_qty=$sam_balance;
				                if($sam_bal_qty>0)
				                {
				                $color="";
				                $txt="Over Batch Qty";
				                }
				                else if($sam_bal_qty<0)
				                {
				                $color="red";
				                $txt="Below Batch Qty";
				                }
				                }
			                }
			        	 ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_sam_btq"><? echo number_format($sam_btq,2); ?></th>
			                <th width="50" align="right"><? echo number_format($total_samp_tot_batch_wgt,2); ?></th>
			                <th width="50" align="right"><? echo number_format($total_samp_tot_trims_wgt,2); ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="15" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="15" align="left">&nbsp; <? echo number_format($without_ext_samp_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Redyeing Batch Qty.</b></td>
			                <td colspan="15" align="left">&nbsp; <? echo number_format($re_dyeing_batch_qty,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="15" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
			 	</table>
			</div><br/>
		</div>
		<?
  	}

	/*
	|--------------------------------------------------------------------------
	| Self Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==1)
	{
		?>
	 	<div align="left"> <b>Self Batch </b></div>
	 	<table class="rpt_table" width="1990" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="75">Batch Date</th>
	                <th width="60">Batch No</th>
	                <th width="40">Ext. No</th>
	                <th width="80">Batch Against</th>
	                <th width="80">Batch Color</th>
	                <th width="80">Buyer</th>
	                <th width="80">Style Ref</th>
	                <th width="120">PO No</th>
                    <th width="100">Pub Ship date</th>
	                <th width="70">File No</th>
	                <th width="70">Ref. No</th>
	                <th width="80">W/O NO.</th>
	                 <th width="70">Job</th>
	                <th width="100">Construction</th>
	                <th width="150">Composition</th>
	                <th width="50">Dia/ Width</th>
	                <th width="50">GSM</th>
	                <th width="60">Lot No</th>
	                <th width="70">Batch Qty.</th>
	                <th width="50">Batch Weight</th>
	                <th width="50">Trims Weight</th>
									<th width="50">Total Roll</th>
	                <th width="100">Grey Req.Qty</th>
	                <th width="100">Remarks</th>
	                <th>Process Name</th>
	            </tr>
	        </thead>
	 	</table>
		<div style=" max-height:350px; width:1990px; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" id="table_body" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
		 		<tbody>
				<?
                $booking_qnty_arr=array();
                $query=sql_select("select a.booking_type,b.po_break_down_id,a.booking_no, b.fabric_color_id, (b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0");
                foreach($query as $row)
                {
                    $booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]+=$row[csf('grey_fab_qnty')];
					if($row[csf('booking_type')]==4)
					{
					 $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
					}
                }

                $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($smn_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                /*$sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($sam_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }*/
                $i=1;
                $f=0;
                $b=0;
                $btq=0;$re_dyeing_batch_qty=0;$without_ext_btq=0;
                $tot_book_qty=0;
                $tot_grey_req_qty=0;
                $batch_chk_arr=array();$booking_chk_arr=array();
                foreach($batchdata as $batch)
                {
                    $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
                    //if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
                    if($sam_booking!=$batch[csf('booking_no')])
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $order_id=$batch[csf('po_id')];
                            $order_id_ex = array_unique(explode(",", $order_id));
                            $order_id = implode(",", $order_id_ex);
                            $color_id=$batch[csf('color_id')];
                            $booking_no=$batch[csf('booking_no')];
                            $booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
                            $desc=explode(",",$batch[csf('item_description')]);
                            $entry_form=$batch[csf('entry_form')];
                            $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
                            $po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$yarn_lot_num='';
                            $grey_booking_qty=0;$buyer_style='';$ship_DateCond='';
                            foreach($po_ids as $p_id)
                            {
                                if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
                                if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
                                if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
                                if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
                                if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
                                $ylot=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
                                if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
                                $booking_color=$order_id.$booking_no.$color_id;
		                            if (!in_array($booking_color,$booking_chk_arr))
		                            {
		                              $b++;
		                              $grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];
		                              $booking_chk_arr[]=$booking_color;
		                            }

                                if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
								$pub_shipment_date=$po_array[$p_id]['pub_shipment_date'];
								if($ship_DateCond=='') $ship_DateCond=$pub_shipment_date;else $ship_DateCond.=",".$pub_shipment_date;
                            }
                            $yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));
                            // $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                            // $book_qty+=$booking_qty;

                           $tot_book_qty=$grey_booking_qty;

                           //echo  $book_qty;


                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
                           ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                            <? if (!in_array($batch[csf('id')],$batch_chk_arr) )
                            {
								if($re_dyeing_from[$batch[csf('id')]])
								{
									$ext_from = $re_dyeing_from[$batch[csf('id')]];
								}else{
									$ext_from = "0";
								}
								if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
								{
									$exists_extention_no = $batch[csf("extention_no")];
									if($exists_extention_no>0)
									{
										$extention_no = $exists_extention_no+1;
									}
									else
									{
										$extention_no = 1;
									}
								}
								else
								{
									if ($batch[csf("extention_no")] == 0)
										$extention_no = '';
									else
										$extention_no = $batch[csf("extention_no")];
								}
                                $f++;
                                ?>
                                <td width="30" style="word-break:break-all;"><? echo $f; ?></td>
                                <td align="center" width="75" style="word-break:break-all;" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                <td width="60" style="word-break:break-all;" title="<? echo $batch[csf('batch_no')]; ?>"><A href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[csf('batch_no')]; ?></A></td>
                                <td  width="40" style="word-break:break-all;" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                 <td  width="80" style="word-break:break-all;"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                                <td width="80" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers;//; ?></div></td>
                                <?
                                $batch_chk_arr[]=$batch[csf('id')];
                               // $book_qty+=$booking_qty;
                            }
                            else
                            {
                                ?>
                                <td width="30" style="word-break:break-all;"><? //echo $sl; ?></td>
                                <td width="75" style="word-break:break-all;"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
                                <td width="60" style="word-break:break-all;"><p><? //echo $booking_qty; ?></p></td>
                                <td width="40" style="word-break:break-all;"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                <?
                            }
                            ?>
                            <td width="80" style="word-break:break-all;"><p><? echo $buyer_style; ?></p></td>
                            <td width="120" style="word-break:break-all;"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $ship_DateCond; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo $po_file; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><?  echo $po_ref; ?></p></td>
                            <td width="80" style="word-break:break-all;"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo $job_num; ?></p></td>
                            <td width="100" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
                            <td width="150" style="word-break:break-all;"><div style="width:150px; word-wrap:break-word;"><?
                            if($desc[4]!="")
                            {
                            	$compositions= $desc[1].' ' . $desc[2];
                            	$gsms= $desc[3];
                            }
                            else
                            {
                            	$compositions= $desc[1];
                            	$gsms= $desc[2];
                            }
                            echo $compositions;

                            ?></div></td>
                            <td width="50" style="word-break:break-all;"><p><? echo end($desc);//$desc[3]; ?></p></td>
                            <td width="50" style="word-break:break-all;"><p><? echo $gsms;//$desc[2]; ?></p></td>
                            <td style="word-break:break-all;" title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
                            <td align="right" width="70" style="word-break:break-all;" title="<? echo $batch[csf('batch_qnty')];  ?>"><?
                            if ($batch[csf('extention_no')]!="")
	                        {
	                         	$re_dyeing_batch_qty+=$batch[csf('batch_qnty')];
	                        }
	                        else
	                        {
	                        	$without_ext_btq+=$batch[csf('batch_qnty')];
	                        }
	                        echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                            <td align="right" width="50" style="word-break:break-all;" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
                            <td align="right" width="50" style="word-break:break-all;" title="<? echo $batch[csf('total_trims_weight')]; ?>"><? echo number_format($batch[csf('total_trims_weight')],2); ?></td>
							<td width="50" align="right" style="word-break:break-all;"><? echo $batch[csf('roll_no')];?></td>
                            <td align="right" width="100" style="word-break:break-all;"><? echo number_format($tot_book_qty,2);?></td>
                            <td width="100" style="word-break:break-all;"><? echo $batch[csf('')];?></td>
                            <td style="word-break:break-all;"><? echo $process_name;?></td>
                            </tr>
                            <?
                            $i++;
                            $btq+=$batch[csf('batch_qnty')];
                            $tot_grey_req_qty+=$tot_book_qty;
                            $balance=$tot_grey_req_qty-$btq;
                            $bal_qty=$balance;
                            if($bal_qty>0)
                            {
                                $color="";
                                $txt="Over Batch Qty";
                            }
                            else if($bal_qty<0)
                            {
                                $color="red";
                                $txt="Below Batch Qty";
                            }
                        }
                    }
                 ?>
		 		</tbody>
			</table>
			<table class="rpt_table" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <tr>
		                <th width="30">&nbsp;</th>
		                <th width="75">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="40">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>

		                <th width="70">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="150">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="70" id="value_total_btq"><? echo number_format($btq,2); ?></th>
		                <th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
		                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
		                <th width="100">&nbsp;</th>
		                <th>&nbsp;</th>
		            </tr>
		            <tr>
		                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
		                <td colspan="12" align="left">&nbsp;
		                 <? echo number_format($tot_grey_req_qty,2); ?>
		                </td>
		            </tr>
		             <tr>
		                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
		                <td colspan="12" align="left">&nbsp; <? echo number_format($without_ext_btq,2); ?> </td>
		            </tr>
		            <tr>
		                <td colspan="13" align="right" style="border:none;"> <b>Redyeing Batch Qty.</b></td>
		                <td colspan="10" align="left">&nbsp; <? echo number_format($re_dyeing_batch_qty,2); ?> </td>
		            </tr>
		             <tr>
		                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
		                <td colspan="12" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
		            </tr>
		        </tfoot>
			</table>
		</div><br/>
		<?
	}
	/*
	|--------------------------------------------------------------------------
	| Subcon Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==2)
	{
		?>
		<div align="left"> <b>SubCond Batch</b></div>
	 	<table class="rpt_table" width="1650" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="75">Batch Date</th>
	                <th width="60">Batch No</th>
	                <th width="40">Ext. No</th>
	                <th width="80">Batch Aganist</th>
	                <th width="80">Batch Color</th>
	                <th width="50">Buyer</th>
	                <th width="80">Style Ref</th>
	                <th width="120">PO No</th>
	                <th width="80">W/O NO.</th>
	                <th width="70">Job</th>
	                <th width="80">Recv. Challan</th>
	                <th width="150">Fabrics Desc.</th>
	                <th width="50">Dia/ Width</th>
	                <th width="50">GSM</th>
	                <th width="60">Lot No</th>
	                <th width="70">Batch Qty.</th>
	                <th width="50">Batch Weight</th>
					<th width="50">Total Roll</th>
	                <th width="100">Grey Req. Qty</th>
	                <th width="100">Remarks</th>
	                <th>Process Name</th>
	            </tr>
	        </thead>
		</table>
		<div style=" max-height:350px; width:1650px; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" id="table_body2" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tbody>
					<?
					/*$booking_qnty_arr=array();
					$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
					foreach($query as $row)
					{
						$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
					}*/


					$sub_material_recv_arr=array();$sub_material_description_arr=array();
					$subconsql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, sum(b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0 group by b.order_id, a.chalan_no,b.color_id,a.trans_type,b.material_description,b.gsm");
					foreach($subconsql as $row)
					{
						$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
					}

					//var_dump($sub_material_description_arr);die;
					$i=1;
					$f=0;
					$btq_subcon=0; $k=0;$re_dyeing_batch_qty=0;$without_ext_sub_btq=0;
					$book_qty_subcon=0;$subcon_tot_book_qty=0;
					$batch_chk_arr=array();$sub_qty_chk_arr=array();
					 //print_r($sub_batchdata);
					foreach($sub_batchdata as $batch)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_id_sub=$batch[csf('po_id')];
						$color_id=$batch[csf('color_id')];
						$booking_no=$batch[csf('booking_no')];
						$sub_challan=$batch[csf('rec_challan')];
						$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
						$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
						$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
						$desc=explode(",",$batch[csf('item_description')]);
						$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));

						$entry_form=$batch[csf('entry_form')];

						$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
						$sub_po_num='';	$sub_job_buyers='';$sub_job_buyers='';
						$subcon_booking_qty=0;$sub_buyer_style='';
						foreach($po_ids as $p_id)
						{
							if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
							if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
							if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$sub_po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$sub_po_array[$p_id]['buyer']];
							$subcon_booking_qty+=$sub_material_recv_arr[$p_id];
							//for yarn lot
							$subconYlot=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
							if($subcon_yarn_lot_num=='')
								$subcon_yarn_lot_num=$subconYlot;
							else
								$subcon_yarn_lot_num.=",".$subconYlot;
							if($sub_buyer_style=="") $sub_buyer_style=$sub_po_array[$p_id]['style_no']; else $sub_buyer_style.=','.$sub_po_array[$p_id]['style_no'];
						}

						$subcon_yarn_lots=implode(",",array_unique(explode(",",$subcon_yarn_lot_num)));
						$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
						if (!in_array($booking_color2,$sub_qty_chk_arr))
						{ $k++;

							//echo $subcon_booking_qty;
							 $sub_qty_chk_arr[]=$booking_color2;
							  $subcon_tot_book_qty=$subcon_booking_qty;
						}
						else
						{
							 $subcon_tot_book_qty=0;
						}

						$process_name = '';
						$process_id_array = explode(",", $batch[csf("process_id")]);
						foreach ($process_id_array as $val)
						{
							if ($process_name == ""){
								$process_name = $conversion_cost_head_array[$val];
							}
							else{
								$process_name .= "," . $conversion_cost_head_array[$val];
							}
						}
						?>
			            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
			            	<? if (!in_array($batch[csf('id')],$batch_chk_arr) )
							{
								if($batch[csf('entry_form')] == 36)
								{
									$batch_sl_no = $batch[csf('id')];
								}
								else
								{
									$batch_sl_no = $batch[csf('batch_sl_no')];
								}

								if($re_dyeing_from[$batch[csf('id')]])
								{
									$ext_from = $re_dyeing_from[$batch[csf('id')]];
								}else{
									$ext_from = "0";
								}
								if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
								{
									$exists_extention_no = $batch[csf("extention_no")];
									if($exists_extention_no>0)
									{
										$extention_no = $exists_extention_no+1;
									}
									else
									{
										$extention_no = 1;
									}
								}
								else
								{
									if ($batch[csf("extention_no")] == 0)
										$extention_no = '';
									else
										$extention_no = $batch[csf("extention_no")];
								}
								$f++;
										?>
				                <td width="30"><? echo $f; ?></td>
				                <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
				                <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><a href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch_sl_no;?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>','<? echo $batch[csf('entry_form')]?>')"><?echo $batch[csf('batch_no')]; ?></a></td>
				                <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
				                 <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
				                <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td width="50"><p><? echo $sub_job_buyers; ?></p></td>
								<?
				                $batch_chk_arr[]=$batch[csf('id')];

			                }
							else
							{ ?>
				                <td width="30"><? //echo $sl; ?></td>
				                <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
				                <td   width="60"><p><? //echo $booking_qty; ?></p></td>
				                <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
				                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
								<?
							}
							?>
							<td width="80"><p><? echo $sub_buyer_style; ?></p></td>
			                <td width="120"><p><? echo $sub_po_num; ?></p></td>
			                <td width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
			                <td width="70"><p><? echo $sub_job_num; ?></p></td>
			                <td width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
			                <td width="150" ><p><? echo $item_descript; ?></p></td>
			                <td  width="50" title="<? echo $desc[2];  ?>"><p><? echo $desc[3]; ?></p></td>
			                <td  width="50"><p><? echo $gsm_subcon; ?></p></td>
			                <td align="left" width="60"><p><? echo $subcon_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>
			                <td align="right" width="70" ><?
			                if ($batch[csf('extention_no')]!="")
	                        {
	                         	$re_dyeing_batch_qty+=$batch[csf('sub_batch_qnty')];
	                        }
	                        else
	                        {
	                        	$without_ext_sub_btq+=$batch[csf('sub_batch_qnty')];
	                        }
			                echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
			                <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
							<td width="50" align="right"><? echo $batch[csf('roll_no')]; ?></td>
			                <td width="100"><? echo number_format($subcon_tot_book_qty,2); ?></td>
			                <td width="100"><? echo $batch[csf('remarks')]; ?></td>
			                <td><? echo $process_name; ?></td>
			            </tr>
						<?
		                $i++;
		                $btq_subcon+=$batch[csf('sub_batch_qnty')];
						$book_qty_subcon+=$subcon_tot_book_qty;
		                $balance=$book_qty_subcon-$btq_subcon;
		                $bal_qty_subcon=$balance;
		                if($bal_qty_subcon>0)
		                {
		                $color="";
		                $txt="Over Batch Qty";
		                }
		                else if($bal_qty_subcon<0)
		                {
		                $color="red";
		                $txt="Below Batch Qty";
		                }
		            }
		        	 ?>
		       </tbody>
			</table>
			<table class="rpt_table" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <tr>
		                <th width="30">&nbsp;</th>
		                <th width="75">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="40">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="120">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>

		                <th width="150">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="70" id="value_total_btq_subcon"><? echo $btq_subcon; ?></th>
		                <th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
		                <th width="100"><? //echo $book_qty_subcon; ?></th>
		                <th width="100">&nbsp;</th>
		                <th>&nbsp;</th>
		            </tr>
		            <tr>
		                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
		                <td colspan="9" align="left">&nbsp;
		                 <? echo number_format($book_qty_subcon,2); ?>
		                </td>
		            </tr>
		             <tr>
		                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
		                <td colspan="9" align="left">&nbsp; <? echo number_format($without_ext_sub_btq,2); ?> </td>
		            </tr>
		            <tr>
		                <td colspan="12" align="right" style="border:none;"> <b>Redyeing Batch Qty.</b></td>
		                <td colspan="9" align="left">&nbsp; <? echo number_format($re_dyeing_batch_qty,2); ?> </td>
		            </tr>
		             <tr>
		                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
		                <td colspan="9" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
		            </tr>
		        </tfoot>
			</table>
		</div>
		<?
	}

	/*
	|--------------------------------------------------------------------------
	| Sample Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==3)
	{
		if($cbo_type==1)
		{
			?>
			<div align="left"> <b>Sample Batch </b></div>
			<table class="rpt_table" id="table_header_1" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="75">Batch Date</th>
                        <th width="60">Batch No</th>
                        <th width="40">Ext. No</th>
                        <th width="80">Batch Aganist</th>
                        <th width="80">Batch Color</th>
                        <th width="50">Buyer</th>
                        <th width="80">Style Ref</th>
                        <th width="120">PO No</th>
                        <th width="70">File No</th>
                        <th width="70">Ref No</th>
                        <th width="100">W/O NO.</th>
                        <th width="70">Job</th>
                        <th width="100">Construction</th>
                        <th width="150">Composition</th>
                        <th width="50">Dia/ Width</th>
                        <th width="50">GSM</th>
                        <th width="60">Lot No</th>
                        <th width="70">Batch Qty.</th>
                        <th width="50">Batch Weight</th>
                        <th width="50">Trims Weight</th>
												<th width="50">Total Roll</th>
                        <th width="100">Grey Req.Qty</th>
                        <th width="100">Remarks</th>
                        <th>Process Name</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1880px; overflow-y:scroll;" id="scroll_body">
			 	<table class="rpt_table" id="table_body3" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
					<?
                    $sam_booking_qnty_arr=array();
                    $sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
                    }

                    $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($smn_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }
                    $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }


                    $i=1;
                    $f=0;
                    $b=0;
                    $btq=0;$re_dyeing_batch_qty=0;$without_ext_samp_btq=0;
                    $tot_book_qty2=0;
                    $tot_grey_req_qty=0;
                    $batch_chk_arr=array();$booking_chk_arr2=array();
                    // print_r($sam_batchdata );
                    foreach($sam_batchdata as $batch)
                    {
                        $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];

                        if($sam_booking==$batch[csf('booking_no')])
                        {
	                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
	                        $po_num='';	$po_file='';$po_ref='';$samp_job_num='';$job_buyers='';$sam_booking_qty=0;$buyer_style='';
	                        foreach($po_ids as $p_id)
	                        {
	                            if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
	                            if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
	                            if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
	                            if($samp_job_num=='') $samp_job_num=$po_array[$p_id]['job_no'];else $samp_job_num.=",".$po_array[$p_id]['job_no'];
	                            if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
	                            $sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
	                            //for yarn lot
	                            $sampleYlot=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
	                            if($sample_yarn_lot_num=='')
	                                $sample_yarn_lot_num=$sampleYlot;
	                            else
	                                $sample_yarn_lot_num.=",".$sampleYlot;
	                            if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
	                        }

	                        $sample_yarn_lots=implode(",",array_unique(explode(",",$sample_yarn_lot_num)));
	                        $order_id=$batch[csf('po_id')];
	                        $color_id=$batch[csf('color_id')];
	                        $booking_no=$batch[csf('booking_no')];
	                        //$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];

	                        $desc=explode(",",$batch[csf('item_description')]);
	                        $entry_form=$batch[csf('entry_form')];
	                        //$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
	                        //if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
	                        //if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
	                        //$po_file=$po_array[$order_id]['file'];
	                        //$po_ref=$po_array[$order_id]['ref'];
	                        // $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
	                        $booking_color=$order_id.$booking_no.$color_id;
	                        if (!in_array($booking_color,$booking_chk_arr2))
	                        {
	                            $b++;
	                            $booking_chk_arr2[]=$booking_color;
	                            $tot_book_qty2=$sam_booking_qty;
	                        }
	                        else
	                        {
	                            $tot_book_qty2=0;
	                        }
	                        //echo  $batch[csf('po_id')].', ';

	                        $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
		                        <?
		                        if (!in_array($batch[csf('id')],$booking_chk_arr2))
		                        {
									if($re_dyeing_from[$batch[csf('id')]])
									{
										$ext_from = $re_dyeing_from[$batch[csf('id')]];
									}else{
										$ext_from = "0";
									}
									if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
									{
										$exists_extention_no = $batch[csf("extention_no")];
										if($exists_extention_no>0)
										{
											$extention_no = $exists_extention_no+1;
										}
										else
										{
											$extention_no = 1;
										}
									}
									else
									{
										if ($batch[csf("extention_no")] == 0)
											$extention_no = '';
										else
											$extention_no = $batch[csf("extention_no")];
									}
		                            $f++;
		                            ?>
		                            <td width="30"><? echo $f; ?></td>
		                            <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
		                            <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><A href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[csf('batch_no')]; ?></A></td>
		                            <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
		                            <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
		                            <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td width="50"><p><? echo $buyer_arr[$non_order_arr[$batch[csf('booking_no')]]['buyer_name']]; ?></p></td>
		                            <?
		                            $batch_chk_arr[]=$batch[csf('id')];
		                            // $book_qty+=$booking_qty;
		                        }
	                            else
	                            { ?>
		                            <td width="30"><? //echo $sl; ?></td>
		                            <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
		                            <td   width="60"><p><? //echo $booking_qty; ?></p></td>
		                            <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
		                            <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
		                            <?
		                        }
								$samp_ref_no= $non_order_arr[$batch[csf('booking_no')]]['samp_ref_no'];
								$booking_without_order=$batch[csf('booking_without_order')];
	                            ?>
	                            <td width="80" title="<? echo $booking_without_order; ?>"><p><? if($booking_without_order==1) echo $style_ref_no_lib[$non_order_arr[$batch[csf('booking_no')]]['style_id']]; else echo $buyer_style; ?></p></td>
	                            <td width="120" ><p><? if($batch[csf('po_id')]>1) echo $po_num;else echo ""; ?></p></td>
	                            <td width="70"><p><? echo $po_file; ?></p></td>
	                             <td width="70"><p><? if($booking_without_order==1) echo $samp_ref_no;else echo $po_ref; ?></p></td>
	                            <td width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                            <td width="70" title="poID=<? echo $batch[csf('po_id')]; ?>"><p><? if($batch[csf('po_id')]>1) echo $samp_job_num;else echo ""; ?></p></td>
	                            <td width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0];//$batch[csf('grey_dia')];; ?></p></td>
	                            <td width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
	                            <td  width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
	                            <td  width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
	                             <td align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $sample_yarn_lots; //$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
	                            <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><?
	                            if ($batch[csf('extention_no')]!="")
		                        {
		                         	$re_dyeing_batch_qty+=$batch[csf('batch_qnty')];
		                        }
		                        else
		                        {
		                        	$without_ext_samp_btq+=$batch[csf('batch_qnty')];
		                        }
		                        echo number_format($batch[csf('batch_qnty')],2);  ?></td>
	                            <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
	                            <td  align="right" width="50" title="<? echo $batch[csf('total_trims_weight')]; ?>"><? echo number_format($batch[csf('total_trims_weight')],2); ?></td>
								<td width="50" align="right"><? echo $batch[csf('roll_no')];?></td>
	                            <td align="right" width="100"><? echo number_format($tot_book_qty2,2);?></td>
	                            <td width="100"><? echo $batch[csf('remarks')];?></td>
	                            <td><p><? echo $process_name;?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                        $sam_btq+=$batch[csf('batch_qnty')];
	                        $tot_grey_req_qty+=$tot_book_qty2;
	                        $sam_balance=$tot_grey_req_qty-$sam_btq;
	                        $sam_bal_qty=$sam_balance;
	                        if($sam_bal_qty>0)
	                        {
	                        	$color="";
	                        	$txt="Over Batch Qty";
	                        }
	                        else if($sam_bal_qty<0)
	                        {
		                        $color="red";
		                        $txt="Below Batch Qty";
	                        }
                        }
                    }
                    ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_sam_btq"><? echo number_format($sam_btq,2); ?></th>
			                <th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="10" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="10" align="left">&nbsp; <? echo number_format($without_ext_samp_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Redyeing Batch Qty.</b></td>
			                <td colspan="10" align="left">&nbsp; <? echo number_format($re_dyeing_batch_qty,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="10" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
			 	</table>
			</div><br/>
		 	<?
		}
  	}
   	?>
	</div>
	</fieldset>
	</div>
	<?

	/*$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$total_data####$filename####$batch_type"; */

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
	echo "$total_data####$filename####$batch_type";

	disconnect($con);
	exit();
}	//BatchReport

if($action=="batch_report_show2") // Show2
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
	$floor_no = str_replace("'","",$cbo_floor);
	//echo $cbo_batch_type;die;
	/*echo $floor_no;die;*/
	$batch_number_hidden = str_replace("'","",$batch_number);
	$booking_no = str_replace("'","",$txt_booking_no);
	$booking_number_hidden = str_replace("'","",$txt_hide_booking_id);

	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$year = str_replace("'","",$cbo_year);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	//echo $order_no;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$process=33;
	//echo str_replace("'","",$batch_no);die;

	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='$buyer'";
	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_name='$buyer'";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".str_replace("'","",$batch_no)."'";
	if ($booking_no=="") $booking_num=""; else $booking_num="  and a.booking_no like '%".str_replace("'","",$booking_no)."%'";
	if ($floor_no==0) $floor_num=""; else $floor_num="  and a.floor_id='".$floor_no."'";
	if ($file_no=="") $file_cond=""; else $file_cond="  and b.file_no='".$file_no."'";

	if ($batch_no=="") $batch_num2=""; else $batch_num2="  and batch_no='".str_replace("'","",$batch_no)."'";
	if ($ref_no=="")
	{
		$ref_cond="";
		$ref_cond2="";

	}
	else
	{
	$ref_cond="  and b.grouping='$ref_no'";
	$ref_cond2="  and c.grouping='$ref_no'";
	}
	if ($company==0) $comp_cond=""; else $comp_cond=" and a.company_id=$company";
	if ($working_company==0) $working_comp_cond=""; else $working_comp_cond=" and a.working_company_id=$working_company";
	//a.company_id=$company

	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if($batch_type==0 || $batch_type==1 ||  $batch_type==3)
	{
		if ($txt_order=="") $order_no_cond=""; else $order_no_cond="  and b.po_number='$txt_order'";
		if ($txt_order=="") $order_no2=""; else $order_no2="  and c.po_number='$txt_order'";
		if ($job_number_id=="") $jobdata=""; else $jobdata="  and a.job_no_prefix_num in($job_number_id)";
		if ($job_number_id=="") $jobdata3=""; else $jobdata3="  and d.job_no_prefix_num in($job_number_id)";
	}
	//echo $jobdata;
	if($batch_type==0 || $batch_type==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num in(".$job_number_id.") "; else $sub_job_cond="";
		if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	}
	if ($buyer==0) $samp_buyercond=""; else $samp_buyercond=" and c.buyer_id=".$buyer." ";

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
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}

	$po_array=array();
	$po_sql=sql_select("select a.job_no_prefix_num 	as job_no,a.buyer_name,a.style_ref_no, b.file_no,b.grouping as ref,b.id, b.po_number,b.pub_shipment_date,c.mst_id as batch_id,d.is_sales,d.sales_order_id from wo_po_details_master a, wo_po_break_down b,pro_batch_create_dtls c,pro_batch_create_mst d where a.id=b.job_id and c.po_id=b.id and d.id=c.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0  and c.status_active in (1) and c.is_deleted=0 and d.status_active in (1) and d.is_deleted=0 $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2");



	$poid='';
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$po_array[$row[csf('id')]]['style_no']=$row[csf('style_ref_no')];
		$po_array[$row[csf('id')]]['ref']=$row[csf('ref')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];

		$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
		if($row[csf('is_sales')]==1)
		{
			$sales_id_array[$row[csf('sales_order_id')]]=$row[csf('sales_order_id')];
		}
		$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		if($poid=='') $poid=$row[csf('id')]; else $poid.=",".$row[csf('id')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
	}
	//echo 'DD';;die;
	//	$batch_cond_for_in=where_con_using_array($batch_id_arr,0,"a.id");
	//$sales_cond_for_in=where_con_using_array($sales_id_array,0,"a.id");
	//echo $poid.'SSSSSSSSSSSS';;die;
	//for sales order entry
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4,5,6,7,8,9) and ENTRY_FORM=15");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 1, $batch_id_arr, $empty_arr);//Batch ID Ref from=1
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 2, $sales_id_array, $empty_arr);//Sales id Ref from=2

	if ($buyer==0) $buyer_cond2=""; else $buyer_cond2="  and b.buyer_id='$buyer'";
	$sales_po_array=array();
	$sales_po_sql=sql_select("select  a.job_no as PO_NUMBER,A.buyer_id AS BUYER_NAME,A.STYLE_REF_NO,a.ID from fabric_sales_order_mst a, pro_batch_create_mst b ,gbl_temp_engine g where a.id=b.sales_order_id and  a.id=g.ref_val and b.sales_order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2 and g.entry_form=15  and b.is_sales=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond2 $year_cond $sales_cond_for_in ");
	$sales_poid='';
	foreach($sales_po_sql as $row)
	{
		$sales_po_array[$row['ID']]['po_no']=$row['PO_NUMBER'];
		$sales_po_array[$row['ID']]['style_no']=$row['STYLE_REF_NO'];
		$sales_po_array[$row['ID']]['buyer']=$row['BUYER_NAME'];
		if($sales_poid=='') $sales_poid=$row['ID']; else $sales_poid.=",".$row['ID'];
	} //echo $sales_poid;die;




	/*echo "select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d  where d.subcon_job=c.job_no_mst  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond";die;*/
	/*$sub_po_array=array();
	$sub_po_sql=sql_select("select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d  where d.subcon_job=c.job_no_mst  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond");
	$sub_poid='';
	foreach($sub_po_sql as $row)
	{
		$sub_po_array[$row[csf('id')]]['po_no']=$row[csf('order_no')];
		$sub_po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$sub_po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
		if($sub_poid=='') $sub_poid=$row[csf('id')]; else $sub_poid.=",".$row[csf('id')];
	}*/
	 // GETTING BUYER NAME
	$non_order_arr=array();
	$sql_non_order="SELECT c.id,c.company_id,c.grouping as samp_ref_no,c.style_desc, c.buyer_id as buyer_name, b.booking_no, b.bh_qty, b.style_id
	from wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls b
	where c.booking_no=b.booking_no and c.booking_type=4 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 $ref_cond2";
	// echo $sql_non_order;
	$result_sql_order=sql_select($sql_non_order);
	foreach($result_sql_order as $row)
	{

		$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
		$non_order_arr[$row[csf('booking_no')]]['samp_ref_no']=$row[csf('samp_ref_no')];
		$non_order_arr[$row[csf('booking_no')]]['style_desc']=$row[csf('style_desc')];
		$non_order_arr[$row[csf('booking_no')]]['style_id']=$row[csf('style_id')];
		$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
		$non_booking_id_r_arr[$row[csf('id')]]=$row[csf('id')];
		$sample_reqIdArr[$row[csf('style_id')]]=$row[csf('style_id')];
		 $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
	}
	// echo "<pre>";
	// print_r($non_order_arr);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 7, $sample_reqIdArr, $empty_arr);//Sample Req  id Ref from=7
	// echo "<pre>";
	// print_r($non_order_arr);
	$style_ref_no_lib=return_library_array( "select a.id,a.style_ref_no from sample_development_mst a,wo_non_ord_samp_booking_dtls b,gbl_temp_engine g  where a.id=b.style_id and a.id=g.ref_val  and b.style_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=7 and g.entry_form=15 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ", "id", "style_ref_no"  );
	//$style_ref_no_lib=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	if($ref_no!="")
	{
		$booking_ids=count($non_booking_id_r_arr);
		if($db_type==2 && $booking_ids>1000)
		{
			$non_booking_cond_for=" and (";
			$bookIdsArr=array_chunk($non_booking_id_r_arr,999);
			foreach($bookIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$non_booking_cond_for.=" a.booking_no_id in($ids) or";
			}
			$non_booking_cond_for=chop($non_booking_cond_for,'or ');
			$non_booking_cond_for.=")";
		}
		else
		{
			$non_booking_cond_for=" and a.booking_no_id in(".implode(",",$non_booking_id_r_arr).")";
		}
	}
	//echo $non_booking_cond_for.'DDDDDDDDD';die;
	//if($sub_poid=="") $sub_poid=0;else $sub_poid=$sub_poid;
	//echo $sub_poid.'gfgf';
	$po_id="";
	if($txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
	{
		$po_id=$poid;
	}
	//echo $po_id;
	$sub_po_id="";
	if($txt_order!="" || $job_number_id!=""  || $year!=0)
	{
		$sub_po_id=$sub_poid;
	}

	$po_id_cond="";
	if($po_id!="")
	{
		//echo $po_id=substr($po_id,0,-1);
		$po_id=chop($po_id,',');
		if($db_type==0) $po_id_cond="and b.po_id in(".$po_id.")";
		else
		{
			$po_ids=array_unique(explode(",",$po_id));
			if(count($po_ids)>990)
			{
				$po_id_cond="and (";
				$po_ids=array_chunk($po_ids,990);
				$z=0;
				foreach($po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $po_id_cond.=" b.po_id in(".$id.")";
					else $po_id_cond.=" or b.po_id in(".$id.")";
					$z++;
				}
				$po_id_cond.=")";
			}
			else{
			$po_id=implode(",",array_unique(explode(",",$po_id)));
			$po_id_cond="and b.po_id in(".$po_id.")";}
		}
	}
	//echo $po_id_cond;die;
	$sub_po_id_cond="";
	if($sub_po_id!="")
	{
		//$sub_po_id=substr($sub_po_id,0,-1);
		$sub_po_id=chop($sub_po_id,',');
		if($db_type==0) $sub_po_id_cond="and b.po_id in(".$sub_po_id.")";
		else
		{
			$sub_po_ids=array_unique(explode(",",$sub_po_id));
			if(count($sub_po_ids)>990)
			{
				$sub_po_id_cond="and (";
				$sub_po_ids=array_chunk($sub_po_ids,990);
				$z=0;
				foreach($sub_po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $sub_po_id_cond.=" b.po_id in(".$id.")";
					else $sub_po_id_cond.=" or b.po_id in(".$id.")";
					$z++;
				}
				$sub_po_id_cond.=")";
			}
			else {
			$sub_po_id=implode(",",array_unique(explode(",",$sub_po_id)));
			$sub_po_id_cond="and b.po_id in(".$sub_po_id.")";}
		}
	}
	//echo  $sub_po_id_cond;
	//echo $po_id.'aaas';

	$sql_dyeing_subcon=sql_select("select c.batch_id,c.entry_form from  pro_fab_subprocess c,pro_batch_create_mst a,gbl_temp_engine g where a.id=c.batch_id and  a.id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=15  and c.entry_form in(38,35,32,47,31,48) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.batch_id>0   $batch_num2 $dates_com $batch_num ");
	//echo "select c.batch_id,c.entry_form from  pro_fab_subprocess c,pro_batch_create_mst a where a.id=c.batch_id and c.entry_form in(38,35,32,47,31,48) and c.status_active=1 and c.is_deleted=0 and batch_id>0 $batch_num2 $dates_com $batch_num ";
	//echo "select batch_id,entry_form from  pro_fab_subprocess where entry_form in(38,35,32,47,31,48) and status_active=1 and is_deleted=0 and batch_id>0 $batch_num2";//die;
	//die;
	$k=1;$i=1;$m=1;$n=1;$p=1;$j=1;$h=1;
	foreach($sql_dyeing_subcon as $row_sub)
	{
		if($row_sub[csf('entry_form')]==38)
		{
		if($k!==1) $sub_cond_d.=",";
		$sub_cond_d.=$row_sub[csf('batch_id')];
		$k++;
		}
		if($row_sub[csf('entry_form')]==35)
		{
		if($i!==1) $row_d.=",";
		$row_d.=$row_sub[csf('batch_id')];
		$i++;
		}
		if($row_sub[csf('entry_form')]==32)
		{
		if($m!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$m++;
		}
		if($row_sub[csf('entry_form')]==47)
		{
		if($n!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$n++;
		}
		if($row_sub[csf('entry_form')]==31)
		{
		if($p!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$p++;
		}
		if($row_sub[csf('entry_form')]==48)
		{
		if($j!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$j++;
		}

	}//echo $sub_cond;die;

	/*$sql_batch_dyeing=sql_select("select batch_id from  pro_fab_subprocess where entry_form=35 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dyeing as $row_dyeing)
	{
		if($i!==1) $row_d.=",";
		$row_d.=$row_dyeing[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_h)
	{
		if($i!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$i++;
	}
	/*$sub_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sub_batch_h as $subrow_h)
	{
		if($i!==1) $subrow_heat.=",";
		$subrow_heat.=$subrow_h[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=47 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_s)
	{
		if($i!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$i++;
	}
	$sql_batch_dry=sql_select("select batch_id from  pro_fab_subprocess where entry_form=31 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dry as $rowdry)
	{
		if($i!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$i++;
	}
	$sql_batch_stenter=sql_select("select batch_id from  pro_fab_subprocess where entry_form=48 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_stenter as $row_sten)//Stentering
	{
		if($i!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$i++;
	}*/

	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/
	if($db_type==0)
	{
		if($cbo_type==1) //Date Wise Report
		{
			if($batch_type==1) // Self
			{
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					$sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.style_ref_no,a.floor_id,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=0 and a.batch_against!=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $floor_num $year_cond $booking_num  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks order by a.batch_date)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against!=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $po_id_cond  $year_cond $booking_num  GROUP BY a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and b.po_id=0 and a.entry_form=0 and a.batch_against!=3   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
				}
			}
			else if($batch_type==2) //SubCon
			{
				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type,b.grey_dia, a.process_id, a.remarks order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where   a.id=b.mst_id  and a.entry_form=36 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.grey_dia, a.process_id, a.remarks)
				union
				(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where    a.id=b.mst_id and a.entry_form=36 and  b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,b.grey_dia, a.process_id, a.remarks) order by batch_date";
				}
			}
			else if($batch_type==3) // Sample batch
			{
				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
				}
				else
				{*/
					$sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id  and  a.entry_form=0 and a.batch_against=3 and b.po_id>0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $non_booking_cond_for $working_comp_cond $dates_com  $batch_num  $ext_no $floor_num $year_cond $booking_num $po_id_cond GROUP BY a.id,a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c where a.id=b.mst_id and c.booking_no=a.booking_no  and  a.entry_form=0 and a.batch_against=3 and b.po_id=0 and b.status_active=1 and c.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $samp_buyercond $ext_no $floor_num $year_cond $non_booking_cond_for $booking_num  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.width_dia_type, a.process_id, a.remarks)  order by batch_date";
				//}
			}
			else if($batch_type==0) // All batch
			{
				// Self
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					$sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id  and  a.entry_form=0 and a.batch_against!=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $floor_num $year_cond $booking_num  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id order by a.batch_date, a.process_id, a.remarks)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against!=3  and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $po_id_cond  $year_cond $booking_num  GROUP BY a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id  and a.entry_form=0 and a.batch_against!=3  and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
				}

				// Subcon

				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=36  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type,b.grey_dia order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b, pro_batch_create_mst a where    a.id=b.mst_id and a.entry_form=36  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type)
				union
				(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id  from pro_batch_create_dtls b,pro_batch_create_mst a where    a.id=b.mst_id and  a.entry_form=36 and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,b.grey_dia) order by batch_date";
				}

				// Sample

				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
				}
				else
				{*/
					 $sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $non_booking_cond_for $working_comp_cond $dates_com  $batch_num  $ext_no $floor_num $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c  where a.id=b.mst_id  and c.booking_no=a.booking_no and  a.entry_form=0 and a.batch_against=3 and b.po_id=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and a.status_active=1 and a.is_deleted=0 $comp_cond  $samp_buyercond $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $year_cond $non_booking_cond_for $po_id_cond $booking_num  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.width_dia_type)  order by batch_date";
				//}
			}
		}
		else if($cbo_type==2) //wait for Heat Setting
		{
			if($batch_type==0 || $batch_type==1)
			{
				if($row_h!=0)
				{
					$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $comp_cond  $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
				}
			}
			if($batch_type==0 || $batch_type==2)
			{
				if($row_h!=0)
				{
					$sub_cond="( select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst $dates_com $comp_cond   $batch_num $booking_num  $working_comp_cond  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,null,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond GROUP BY a.id, a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
				}
			}
		}
		else if($cbo_type==3) // Wait For Dyeing
		{
			//$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
			//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
			$find_inset="and  FIND_IN_SET(33,a.process_id)";
			$find_inset_not="and not FIND_IN_SET(33,a.process_id)";
			//else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
			if($batch_type==0 || $batch_type==1)
			{
				$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32  and a.id not in($row_d) $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $comp_cond $working_comp_cond  $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $find_inset GROUP BY  a.id,a.batch_no, b.po_id,b.prod_id,b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no,b.po_id,b.prod_id, b.item_description, a.process_id, a.remarks ) order by batch_date";
			}
			if($batch_type==0 || $batch_type==2) //SubCon Deying
			{
				$sub_cond="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks  from pro_batch_create_mst a,pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d where  a.id=b.mst_id and b.po_id=c.id  and d.subcon_job=c.job_no_mst    and a.id not in($sub_cond_d) $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond and a.entry_form=36 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   GROUP BY a.batch_no, b.po_id,b.prod_id,b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num $booking_num $ext_no $floor_num  GROUP BY a.id, a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no, a.extention_no,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
			}
		}
		else if($cbo_type==4) //Wait For Re-Dyeing
		{
			if($batch_type==0 || $batch_type==1)
			{
				$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
				from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks
				from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and a.batch_against=2 and b.po_id=0 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $batch_num $booking_num $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
			}

			if($batch_type==0 || $batch_type==2) //SubCon Batch
			{

				$sql_subcon="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
				from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num $booking_num $floor_num $ext_no   GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";

			}
		}
		else if($cbo_type==5) //Wait For Singeing
		{
			if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)";
			else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
			if($batch_type==0 || $batch_type==1)
			{
				$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
				if($w_sing_arr!=0)
				{
					$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where   a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond $comp_cond ";
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no,a.floor_id, b.item_description,a.batch_date, a.batch_weight, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type, a.process_id, a.remarks) order by batch_date ";
				}//W-end
				//echo $sql;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		//echo $row_d."=GGGGGGGGG";die;
		/*
		|--------------------------------------------------------------------------
		| Date Wise Report
		|--------------------------------------------------------------------------
		|
		*/
		if($cbo_type==1)
		{
			/*
			|--------------------------------------------------------------------------
			| All Batch
			|--------------------------------------------------------------------------
			|
			*/
			if($batch_type==0)
			{
				if($job_number_id!="" || $txt_order!="")
				{
			  		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,count(b.roll_no) as roll_no,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks order by a.batch_date";
			  		/*echo $sql;die;*/
				}
				else
				{
					//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS
					//echo $po_id_cond ;
					$sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, SUM(b.batch_qnty) AS batch_qnty, b.item_description, listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, b.prod_id, b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $batch_num $booking_num $po_id_cond $ext_no $floor_num $year_cond $comp_cond GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type, a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, SUM(b.batch_qnty) AS batch_qnty, b.item_description, listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, b.prod_id, b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $booking_num $po_id_cond $batch_num $floor_num $ext_no $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}

				//Sub
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
					$sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a
					where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.grey_dia, a.entry_form,b.rec_challan, a.process_id, a.remarks order by a.batch_date ";
				}
				else
				{
					$sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,b.gsm,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.gsm,b.grey_dia,a.entry_form,b.rec_challan,a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,b.gsm,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,b.gsm,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,b.grey_dia,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				}

				//Sam

				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="" )
				{
					//echo $order_no;
			 		$sql_sam="SELECT a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $booking_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
				}
				else
				{*/
					//echo $$job_number_id .'aaaa';
				$sql_sam="(SELECT a.id,a.batch_against,a.entry_form, a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $non_booking_cond_for $comp_cond $dates_com  $batch_num $booking_num $po_id_cond  $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no,a.booking_without_order, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c where a.id=b.mst_id  and a.booking_no=c.booking_no and  a.entry_form=0 and a.batch_against=2 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $non_booking_cond_for $samp_buyercond $ref_cond2  $batch_num $booking_num $ext_no $floor_num $floor_num $year_cond GROUP BY a.id,a.batch_against,a.batch_no,a.booking_without_order, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
					//echo $sql_sam;//and b.po_id=0
				//}
				// echo $sql_sam;
			}

			/*
			|--------------------------------------------------------------------------
			| Self Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==1)
			{
				if($job_number_id!="" || $txt_order!="")
				{
					//echo $order_no;
			  		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks order by a.batch_date";
				}
				else
				{
					//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS
					//echo $po_id_cond ;
					$sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $po_id_cond $ext_no $floor_num $year_cond $comp_cond  GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $booking_num  $batch_num $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}
			}

			/*
			|--------------------------------------------------------------------------
			| Subcond Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==2)
			{
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
					$sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, a.entry_form,b.rec_challan, a.process_id, a.remarks order by a.batch_date ";
				}
				else
				{
					$sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";

				}
			// echo $sub_cond;die;
			}

			/*
			|--------------------------------------------------------------------------
			| Sample Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==3)
			{
				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="" )
				{
					//echo $order_no;
			 		$sql_sam="SELECT a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and c.id=b.po_id and c.job_no_mst=d.job_no and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $booking_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
				}
				else
				{*/
					//echo $$job_number_id .'aaaa';
				  	$sql_sam="(SELECT a.id,a.batch_against,a.entry_form, a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $po_id_cond $comp_cond $dates_com  $batch_num $booking_num $po_id_cond  $non_booking_cond_for $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no,a.booking_without_order, a.extention_no, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a ,wo_non_ord_samp_booking_mst c where  a.id=b.mst_id and c.booking_no=a.booking_no  and a.entry_form=0 and a.batch_against=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $samp_buyercond  $comp_cond $dates_com  $ref_cond2 $batch_num $booking_num $ext_no $floor_num $year_cond $non_booking_cond_for GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.booking_without_order,a.extention_no,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
					//echo $sql_sam;die;//and b.po_id=0
				//}
				// echo $sql_sam;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| wait for Heat Setting
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==2)
		{
			if($batch_type==1)// Self batch
			{
				//echo "dsd";
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				if($w_heat_arr!=0)
				{
					$sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks order by a.batch_date ";
				} echo $sql;

			} //Batch Type End
			if($batch_type==2) //Subcond batch
			{
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				if($w_heat_arr!=0)
				{
					$sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
					$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan, a.process_id, a.remarks order by a.batch_date";

				}
				//echo $sub_cond;
			}//Batch type End

			if($batch_type==0) // Self and Subcond batch
			{
				// Self batch
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				//echo $row_heat.'dd';;
				//if($row_heat)
				//{
					$sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					if($row_heat)
					{
					$p=1;
						foreach($w_heat_arr as $h_batch_id)
						{
							if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
							$p++;
						}
						$sql .=")";
					}

					$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks order by a.batch_date ";
				//}
				// echo $sql;

				//	Subcond batch
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);

					$sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
				if($w_heat_arr!=0)
				{
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
				}

					$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan order by a.batch_date";

				//}
				//echo $sub_cond;
			} // end
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Dyeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==3)
		{
			if($batch_type==1)//Self Batch
			{
				//echo $row_d.'sdd';
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
						$sql .=")";
						$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
						union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date ";
						//echo $sql;die;
				}
			}//Self batch End
			if($batch_type==2) //SubCon Batch
			{

				$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				if($w_dyeing_arr!=0)
				{
					//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,count(b.roll_no) as roll_no,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
					$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";
					$p2=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p2++;
					}
					$sub_cond .=")";
					$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.extention_no,d.party_id,b.item_description,b.po_id,b.prod_id,
					b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by a.batch_date ";

				}//echo $sub_cond;
			}
			// echo $sub_cond;//die;
			if($batch_type==0) //Self Batch and SubCon Batch
			{
				// Self
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				//print_r($w_dyeing_arr);
				///echo $row_d.'DSDS';
				//die;
				if($w_dyeing_arr!=0)
				{
					$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					if($row_d>0)
					{
						$p=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p++;
						}
						$sql .=")";
					}


						$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
						union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not
						";

						if($row_d)
						{
							$p1=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p1==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p1++;
						}
						$sql .=")";
						}
						$sql .="  GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date
						";
					//echo $sql;
					/*

					union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not
						";

						if($row_d)
						{
							$p1=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p1==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p1++;
						}
						$sql .=")";
						}
						$sql .="  GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date


					union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) */
						//ISsue id=23236
					//echo $sql;//die;
				}

				// Subcon
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				if($w_dyeing_arr!=0)
				{
					//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
				if($sub_cond_d>0)
					{
						$p=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p++;
						}
						$sub_cond .=")";
					}
					$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";

					if($sub_cond_d)
					{
						$p2=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p2++;
					}
					$sub_cond .=")";
					}
					$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no,d.party_id,b.item_description,b.po_id,b.prod_id,
					b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				} //echo $sub_cond;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Re-Dyeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==4)
		{

			if($batch_type==1)//Self Batch
			{
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 { //echo $d_batch_id;die;
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,d.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num ";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 {
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}
			}
			if($batch_type==2) //SubCon Batch
			{
				$w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				// print_r( $w_dyeing_subcon);
				if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d,
					$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $subcon_batch_id)
				 	{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
						$p++;
				 	}
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $booking_num $sub_buyer_cond  $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $d_batch_id)
					{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}

					 //echo $sql;
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				}
			}
			//echo $sql_subcon;

			if($batch_type==0) //Self Batch with SubCon Batch
			{
				//Self Batch
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 { //echo $d_batch_id;die;
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,d.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num ";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 {
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}

				//SubCon Batch
				$w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				// print_r( $w_dyeing_subcon);
				if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d,
					$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $subcon_batch_id)
				 	{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
						$p++;
				 	}
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $booking_num $sub_buyer_cond  $suborder_no $ext_no $floor_num $year_cond $comp_cond";

					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $d_batch_id)
					{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
					$sql_subcon .=")";
					}

					 //echo $sql;

					$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";

				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Singeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==5)
		{
			if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)";
			else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
			$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
			if($w_sing_arr!=0)
			{
				$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond  ";
				if($row_sin>0)
					{
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					}
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num $year_cond ";
				if($row_sin>0)
					{
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					}
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				//echo $sql;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Date Wise Report
	|--------------------------------------------------------------------------
	|
	*/
	//echo $sql;
	if($cbo_type==1)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
			$sam_batchdata=sql_select($sql_sam);
		}
		else if($batch_type==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
			//print_r($batchdata);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;die;
			$sub_batchdata=sql_select($sub_cond);
			// echo $sub_cond;die;
		}
		else if($batch_type==3)
		{
			//echo $sql_sam;die;
			$sam_batchdata=sql_select($sql_sam);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| wait for Heat Setting
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==2)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			$sub_batchdata=sql_select($sub_cond);
			// echo $sub_cond;die;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Dyeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==3)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Re-Dyeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==4)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sql_subcon);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sql_subcon);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Singeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==5)
	{
		if($batch_type==1)
		{
		$batchdata=sql_select($sql);
		}
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
		}
		/*else if($batch_type==0 || $batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}*/
	}

	/*
	|--------------------------------------------------------------------------
	| for self batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$batch_id_arr = array();$po_id_arr = array();
	foreach ($batchdata as $val)
	{
		$batch_id_arr[$val[csf('id')]]=$val[csf('id')];
		$po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
		//$batch_color_arr[$val[csf('id')]]=$val[csf('color_id')];
	}
	//print_r($batch_color_arr);
	$batchIds = implode(",", $batch_id_arr);

	/*
	|--------------------------------------------------------------------------
	| for subcon batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$subcon_batch_id_arr = array();
	foreach ($sub_batchdata as $val)
	{
		$subcon_batch_id_arr[$val[csf('id')]]=$val[csf('id')];
	}
	$subconBatchIds = implode(",", $subcon_batch_id_arr);

	/*
	|--------------------------------------------------------------------------
	| for sample batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$sample_batch_id_arr = array();
	foreach ($sam_batchdata as $val)
	{
		$sample_batch_id_arr[$val[csf('id')]]=$val[csf('id')];
		$po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
	}
	$sampleBatchIds = implode(",", $sample_batch_id_arr);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 3, $batch_id_arr, $empty_arr);//Batch id Ref from=3
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 4, $po_id_arr, $empty_arr);//Batch id Ref from=3
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 5, $subcon_batch_id_arr, $empty_arr);//Po id Ref from=5
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 6, $sample_batch_id_arr, $empty_arr);//Po id Ref from=5
	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/
	$sub_po_array=array();
	$sub_po_sql=sql_select("select d.job_no_prefix_num as JOB_NO,d.PARTY_ID,C.CUST_BUYER, C.ID, C.ORDER_NO from subcon_ord_dtls c, subcon_ord_mst d,gbl_temp_engine g   where d.subcon_job=c.job_no_mst and  a.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=5 and g.entry_form=15  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond");
	$sub_poid='';
	foreach($sub_po_sql as $row)
	{
		$sub_po_array[$row[('ID')]]['po_no']=$row[('ORDER_NO')];
		$sub_po_array[$row[('ID')]]['job_no']=$row[('JOB_NO')];
		$sub_po_array[$row[('ID')]]['buyer']=$row[('PARTY_ID')];
		if($sub_poid=='') $sub_poid=$row[('id')]; else $sub_poid.=",".$row[('ID')];
	}



	$merg_batch_id='';
	if($batchIds!="")
	{
		$merg_batch_id = $batchIds;
	}
	else if($batchIds!="" && $subconBatchIds!="")
	{
		$merg_batch_id = $batchIds.','.$subconBatchIds;
	}else
	{
		$merg_batch_id = $batchIds.','.$subconBatchIds.','.$sampleBatchIds;
	}
	//var_dump($merg_batch_id);
	if($db_type==0)
	{
		if($batchIds!="")
		{
			//$sql_yarn_lot = "SELECT a.id, group_concat(d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  GROUP BY a.id";
			$sql_yarn_lot = "SELECT b.prod_id, b.po_id, group_concat(d.yarn_lot) AS yarn_lot, group_concat(d.brand_id) as brand_id, group_concat(d.yarn_count) as yarn_count, group_concat(d.stitch_length) as stitch_length ,group_concat(d.width) as width FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  GROUP BY a.id";

			$sql_yarn_lot_res = sql_select($sql_yarn_lot);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		/*
		|--------------------------------------------------------------------------
		| for self batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/

		if($batchIds != '')
		{
			//$sql_yarn_lot = "SELECT a.id,  LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id";
			/*$sql_yarn_lot = "SELECT b.prod_id, b.po_id, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY b.prod_id, b.po_id"; */
			//$sql_yarn_lot = "SELECT a.id,b.prod_id, b.po_id, d.yarn_lot AS yarn_lot, d.brand_id as brand_id, d.yarn_count as yarn_count, d.stitch_length as stitch_length, d.width as width FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id,b.prod_id, b.po_id,d.yarn_lot, d.brand_id, d.yarn_count, d.stitch_length, d.width";
			$sql_yarn_lot = "SELECT b.mst_id as id,b.prod_id, b.po_id, d.yarn_lot AS yarn_lot,d.brand_id as brand_id, d.yarn_count as yarn_count, d.stitch_length as stitch_length, d.width as width FROM  pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,gbl_temp_engine g WHERE  b.roll_id = c.id AND c.dtls_id = d.id and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=15  AND b.status_active = 1 AND b.is_deleted = 0  GROUP BY b.mst_id,b.prod_id, b.po_id,d.yarn_lot,d.brand_id,d.yarn_count,d.stitch_length,d.width";
			//echo $sql_yarn_lot;
			$sql_yarn_lot_res = sql_select($sql_yarn_lot);

		}

		/*
		|--------------------------------------------------------------------------
		| for subcon batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($subconBatchIds != '')
		{

		 /*$subconYarnLotSql = "SELECT a.id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot, d.brand AS brand, d.yrn_count_id as yrn_count_id, d.stitch_len as stitch_len, d.dia_width_type as dia_width_type FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_production_dtls d WHERE a.id = b.mst_id AND TO_CHAR(b.po_id) = d.order_id  and b.item_description=d.fabric_description  AND a.company_id = ".$company." AND a.id IN(".$subconBatchIds.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.po_id,a.id"; */
		  $subconYarnLotSql = "SELECT a.id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot,d.brand AS brand, d.yrn_count_id as yrn_count_id, d.stitch_len as stitch_len, d.dia_width_type as dia_width_type FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_production_dtls d,gbl_temp_engine g WHERE a.id = b.mst_id AND TO_CHAR(b.po_id) = d.order_id  and b.item_description=d.fabric_description and a.id=g.ref_val and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=5 and g.entry_form=15  AND a.company_id = ".$company."  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.po_id,a.id,d.brand, d.yrn_count_id, d.stitch_len,d.dia_width_type";
			$subconYarnLotRslt = sql_select($subconYarnLotSql);
		}

		/*
		|--------------------------------------------------------------------------
		| for sample batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($sampleBatchIds != '')
		{

			//$sampleYarnLotSql = "SELECT a.id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot, d.brand_id as brand_id, d.yarn_count as yarn_count, d.stitch_length as stitch_length, d.width as width FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = ".$company." AND a.id in(".$sampleBatchIds.") AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id,b.prod_id, b.po_id,d.yarn_lot";
			$sampleYarnLotSql = "SELECT b.mst_id as id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot,d.brand_id as brand_id, d.yarn_count as yarn_count, d.stitch_length as stitch_length, d.width as width  FROM  pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,gbl_temp_engine g WHERE   b.roll_id = c.id AND c.dtls_id = d.id and b.mst_id=g.ref_val   and g.user_id = ".$user_id." and g.ref_from=6 and g.entry_form=15  AND b.status_active = 1 AND b.is_deleted = 0   GROUP BY b.mst_id,b.prod_id, b.po_id,d.yarn_lot,d.brand_id, d.yarn_count, d.stitch_length, d.width";
			$sampleYarnLotRslt = sql_select($sampleYarnLotSql);
		}
	}
	 // inv_receive_master e  AND e.entry_form IN (2, 22) AND a.id = 65338

	/*
	|--------------------------------------------------------------------------
	| for self batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$yarn_lot_arr=array();
	foreach($sql_yarn_lot_res as $rows)
	{
		//$yarn_lot_arr[$rows[csf('id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand'].=$rows[csf('brand_id')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['yarn_count'].=$rows[csf('yarn_count')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['stitch_length'].=$rows[csf('stitch_length')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['width'].=$rows[csf('width')].',';
	}
	// echo "<pre>";
	// print_r($yarn_lot_arr);
	// echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for subcon batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$subcon_yarn_lot_arr=array();
	foreach($subconYarnLotRslt as $rows)
	{
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand'].=$rows[csf('brand')].',';
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['yrn_count_id'].=$rows[csf('yrn_count_id')].',';
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['stitch_len'].=$rows[csf('stitch_len')].',';
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['dia_width_type'].=$rows[csf('dia_width_type')].',';
	}
	//echo "<pre>";
	//print_r($subcon_yarn_lot_arr);

	/*
	|--------------------------------------------------------------------------
	| for sample batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$sample_yarn_lot_arr=array();
	foreach($sampleYarnLotRslt as $rows)
	{
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand'].=$rows[csf('brand_id')].',';
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['yarn_count'].=$rows[csf('yarn_count')].',';
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['stitch_length'].=$rows[csf('stitch_length')].',';
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['width'].=$rows[csf('width')].',';
	}

	//echo "<pre>";
	//print_r($sample_yarn_lot_arr);
	$merg_batch_id_arr=explode(",",$merg_batch_id);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 8, $merg_batch_id_arr, $empty_arr);//Po id Ref from=8

	$sql_rcv_dtls = "SELECT b.mst_id as id,b.prod_id, b.po_id,e.receive_basis,e.booking_id, d.machine_dia,d.machine_gg from pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e,gbl_temp_engine g
	where   b.roll_id = c.id and c.dtls_id=d.id and d.mst_id=e.id and b.mst_id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=8 and g.entry_form=15 and e.company_id = $company  and b.status_active=1 and b.is_deleted=0 and  e.entry_form in(2,22,84) and c.entry_form in(2,22)
	group by b.mst_id,b.prod_id, b.po_id,e.receive_basis,e.booking_id, d.machine_dia,d.machine_gg";

	//echo $sql_rcv_dtls;
	$sql_rcv_dtls_res = sql_select($sql_rcv_dtls);
	$book_id_arr=array();
	foreach ($sql_rcv_dtls_res as $rows)
	{
		array_push($book_id_arr,$rows[csf('booking_id')]);
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 15, 9, $book_id_arr, $empty_arr);//Po id Ref from=9
	// echo "select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where status_active=1 and is_deleted=0 ".where_con_using_array($book_id_arr,0,'id')." ";
	$program_data = sql_select("select b.width_dia_type, b.machine_dia, b.machine_gg, b.machine_id from ppl_planning_info_entry_dtls b,gbl_temp_engine g  where  b.id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=9 and g.entry_form=15  b.status_active=1 and b.is_deleted=0  ");

	$dia_gauge_arr=array();
	foreach($sql_rcv_dtls_res as $rows)
	{
		if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 4 || $row[csf('receive_basis')] == 11) //from Entry page
		{
			$dia_gauge_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['machine_dia'] =$rows[csf('machine_dia')];
			$dia_gauge_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['machine_gg']=$rows[csf('machine_gg')];
		}
		else if ($row[csf('receive_basis')] == 2) //Knitting Plan
		{
			$dia_gauge_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['machine_dia'] = $program_data[0][csf('machine_dia')];
			$dia_gauge_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['machine_gg']=$program_data[0][csf('machine_gg')];

		}

	}

	// echo "<pre>";
	// print_r($dia_gauge_arr);


	ob_start();
	?>
	<div align="center">
	<fieldset style="width:1375px;">
	<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type]; ?> </strong>
		<br><b>
		<?
		//echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
		echo ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
		?> </b>
 	</div>
 	<div align="center">
  	<?php
	/*
	|--------------------------------------------------------------------------
	| All Batch
	|--------------------------------------------------------------------------
	|
	*/
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4,5,6,7,8,9) and ENTRY_FORM=15");
	oci_commit($con);
	if($batch_type==0)
  	{

  		?>
	 	<div align="left"><b>Self Batch</b>
		 	<table class="rpt_table" width="2490" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Against</th>
		                <th width="80">Batch Color</th>
		                <th width="80">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
                        <th width="100">Pub ship date</th>
		                <th width="70">File No</th>
		                <th width="70">Ref. No</th>
		                <th width="80">W/O NO.</th>
		                 <th width="70">Job</th>
		                <th width="100">Construction</th>
		                <th width="150">Composition</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
		                <th width="50">Trims Weight</th>
						<th width="50">Total Roll</th>
		                <th width="100">Booking Grey Req.Qty</th>
		                <th width="100">Dia*Gauge</th>
		                <th width="100">Finish Dia</th>
		                <th width="100">Stitch Length</th>
		                <th width="100">Yarn Count</th>
		                <th width="100">Brand</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
		 	</table>
			<div style=" max-height:350px; width:2490px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body" width="2470" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
					<?
				//	$po_cond_in=where_con_using_array($po_id_arr,0,'b.po_break_down_id');

                    $booking_qnty_arr=array();
                    $queryFab=sql_select("select a.booking_type, b.po_break_down_id,a.booking_no, b.fabric_color_id, (b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine g  where a.booking_no=b.booking_no and b.po_break_down_id=g.ref_val   and g.user_id = ".$user_id." and g.ref_from=4 and g.entry_form=15 and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  ");



                    foreach($queryFab as $row)
                    {
                        $booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]+=$row[csf('grey_fab_qnty')];
						if($row[csf('booking_type')]==4)
						{
						 $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}
						//$booking_qnty_arr2[$row[csf('po_break_down_id')]][$row[csf('booking_no')]]+=$row[csf('grey_fab_qnty')];
                    }
					unset($queryFab);

                   /* $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($smn_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }*/
                    /*$sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }*/
                    $i=1;
                    $f=0; $bb=0;
                    $b=0;
                    $btq=0;
                    $tot_book_qty=0;  $tot_batch_wgt=0;$tot_trims_wgt=0;$total_tot_batch_wgt=0;$total_tot_trims_wgt=0;
                    $tot_grey_req_qty=0;
                    $batch_chk_arr=array();
                    $booking_chk_arr=array();
                    foreach($batchdata as $batch)
                    {
                      // echo $batch[csf('booking_no')].'dd';
					    $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
                        //if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
                        if($sam_booking!=$batch[csf('booking_no')])
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $order_id=$batch[csf('po_id')];
                            $color_id=$batch[csf('color_id')];
                            $booking_no=$batch[csf('booking_no')];
                            $booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
                            $desc=explode(",",$batch[csf('item_description')]);
                            $entry_form=$batch[csf('entry_form')];
                            $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
                            $po_num='';
                            $po_file='';
                            $po_ref='';
                            $job_num='';
                            $job_buyers='';
                            $yarn_lot_num='';
                            $yarn_brand='';
                            $yarn_count_num='';
                            $yarn_stitch='';
                            $yarn_fin_dia='';
                            $grey_booking_qty=0;
                            $buyer_style=''; $ship_DateCond='';
							$machine_dia='';
							$machine_gg='';
							$dia_gauge='';
                            /*print_r($po_ids);die;*/
                            foreach($po_ids as $p_id)
                            {
                            	//echo $batch[csf('id')]."=".$batch[csf('prod_id')]."=".$p_id."<br>";
                            	//echo $yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot']."<br><br>";
                            	if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
								if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
								if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
								if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
								if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
								//$ylot=rtrim($yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'],',');
								$ylot=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
								$ybrand=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['brand'],',');
								$yyarn_count=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['yarn_count'],',');
								$ystitch_length=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['stitch_length'],',');
								$yfin_dia=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['width'],',');



								if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
								if($yarn_brand=='') $yarn_brand=$ybrand;else $yarn_brand.=",".$ybrand;
								if($yarn_count_num=='') $yarn_count_num=$yyarn_count;else $yarn_count_num.=",".$yyarn_count;
								if($yarn_stitch=='') $yarn_stitch=$ystitch_length;else $yarn_stitch.=",".$ystitch_length;
								if($yarn_fin_dia=='') $yarn_fin_dia=$yfin_dia;else $yarn_fin_dia.=",".$yfin_dia;

								$grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];


								//echo $p_id.'='.$booking_no.'='.$color_id.',';
								if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
								$pub_shipment_date=$po_array[$p_id]['pub_shipment_date'];

								if($ship_DateCond=='') $ship_DateCond=$pub_shipment_date;else $ship_DateCond.=",".$pub_shipment_date;


								if($machine_dia=='') $machine_dia = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_dia']; else $machine_dia='';
								if($machine_gg=='') $machine_gg = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_gg']; else $machine_gg='';



                            }
                            $yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));
                            $yarn_brands=implode(",",array_unique(explode(",",$yarn_brand)));
                            $yarn_counts=implode(",",array_unique(explode(",",$yarn_count_num)));
                            $yarn_stitchs=implode(",",array_unique(explode(",",$yarn_stitch)));
                            $yarn_fin_dias=implode(",",array_unique(explode(",",$yarn_fin_dia)));


							if( $machine_dia !='' && $machine_gg !='')
							{
								$dia_gauge = $machine_dia.'x'.$machine_gg;
							}
							else if( $machine_gg !='' )
							{
								$dia_gauge = $machine_dia;
							}
							else
							{
								$dia_gauge = $machine_gg;
							}


                           /* $buyer_po=""; $buyer_style="";
				            $buyer_po_id=explode(",",$row[csf('po_id')]);*/
							/*foreach($po_ids as $p_id)
							{
							if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
							if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
							}
							$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
							$buyer_style=implode(",",array_unique(explode(",",$buyer_style)))*/;//add by samiur

                            // $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                            // $book_qty+=$booking_qty;

                            $booking_color=$booking_no;//$order_id.$booking_no.$color_id;
                            if (!in_array($booking_color,$booking_chk_arr))
                            {
                                $bb++;
                                $booking_chk_arr[]=$booking_color;
                                $tot_book_qty=$grey_booking_qty;
                            }
                            else
                            {
                                $tot_book_qty=0;
                            }

							 $batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$batch_wgt_chk_arr))
                            {
                                $b++;
                                $batch_wgt_chk_arr[]=$batch_id;
                                $tot_batch_wgt=$batch[csf('batch_weight')];
                                $tot_trims_wgt=$batch[csf('total_trims_weight')];
                            }
                            else
                            {
                                $tot_batch_wgt=0;
                                $tot_trims_wgt=0;
                            }

                          // echo  $batch[csf('id')].'dd';;

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}

                           ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                            <?
							if (!in_array($batch[csf('id')],$batch_chk_arr) )
                            {
                                $f++;
                                ?>
                                <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
                                <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                                <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                 <td style="word-break:break-all;"  width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                <td style="word-break:break-all;" title="<? echo $order_id.'='.$batch[csf('color_id')];?>" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                                <td style="word-break:break-all;" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers; ?></div></td>
                                <?
                                $batch_chk_arr[]=$batch[csf('id')];
                               // $book_qty+=$booking_qty;
                            }
                            else
                            {
                                ?>
                                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
                                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
                                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
                                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
                                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td style="word-break:break-all;"  width="80"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                <?
                            }
                            ?>

                            <td style="word-break:break-all;" width="80"><p><? echo $buyer_style; ?></p></td>
                            <td style="word-break:break-all;" width="120"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
                            <td style="word-break:break-all;" width="100"><p><? echo $ship_DateCond;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
                            <td style="word-break:break-all;" width="70"><p><? echo $po_file; ?></p></td>
                            <td style="word-break:break-all;" width="70"><p><?  echo $po_ref; ?></p></td>
                            <td style="word-break:break-all;" width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                            <td style="word-break:break-all;" width="70"><p><? echo $job_num; ?></p></td>
                            <td style="word-break:break-all;" width="100"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
                            <td style="word-break:break-all;" width="150"><div style="width:150px; word-wrap:break-word;"><?

                            if($desc[4]!="")
                            {
                            	$compositions= $desc[1].' ' . $desc[2];
                            	$gsms= $desc[3];
                            }
                            else
                            {
                            	$compositions= $desc[1];
                            	$gsms= $desc[2];
                            }

                            echo $compositions;

                            ?></div></td>
                            <td style="word-break:break-all;"  width="50"><p><? echo end($desc);//$desc[3]; ?></p></td>
                            <td style="word-break:break-all;"  width="50"><p><? echo  $gsms;//$desc[2]; ?></p></td>
                            <td style="word-break:break-all;"  title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
                            <td style="word-break:break-all;" align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                            <td style="word-break:break-all;"  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($tot_batch_wgt,2); ?></td>
                            <td style="word-break:break-all;"  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($tot_trims_wgt,2); ?></td>
							<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
                            <td style="word-break:break-all;"  align="right" width="100" title="Booking Color Wise Qty"><? echo number_format($tot_book_qty,2);?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $dia_gauge; ?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $yarn_fin_dias; ?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $yarn_stitchs;?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $yarncount[$yarn_counts];?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $brand_name_arr[$yarn_brands];?></td>
                            <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')];?></td>
                            <td style="word-break:break-all;"><? echo $process_name;?></td>
                            </tr>
                            <?
                            $i++;
                            $btq+=$batch[csf('batch_qnty')];
                            $tot_grey_req_qty+=$tot_book_qty;
							 $total_tot_batch_wgt+=$tot_batch_wgt;
							$total_tot_trims_wgt+=$tot_trims_wgt;
                            $balance=$tot_grey_req_qty-$btq;
                            $bal_qty=$balance;
                            if($bal_qty>0)
                            {
                            $color="";
                            $txt="Over Batch Qty";
                            }
                            else if($bal_qty<0)
                            {
                            $color="red";
                            $txt="Below Batch Qty";
                            }
                        }
                    }
                    ?>
			 		</tbody>
				</table>
				<table class="rpt_table" width="2470"  cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
                             <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="80">&nbsp;</th>

			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_btq"><? echo number_format($btq,2); ?></th>
			                <th width="50"  align="right"><? echo number_format($total_tot_batch_wgt,2); ?></th>
			                <th width="50"  align="right"><? echo number_format($total_tot_trims_wgt,2); ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100"></th>
			                <th width="100"></th>
			                <th width="100"></th>
			                <th width="100"></th>

			                <th width="100"></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="18" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="18" align="left">&nbsp; <? echo number_format($btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="18" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
				</table>
			</div> <br/>
		</div>


		<div align="left"> <b>SubCond Batch </b>
		 	<table class="rpt_table" width="1950" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Aganist</th>
		                <th width="80">Batch Color</th>
		                <th width="50">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
		                <th width="80">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="80">Recv. Challan</th>
		                <th width="150">Fabrics Desc.</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
						<th width="50">Total Roll</th>
		                <th width="100">Material Recv Grey Req. Qty</th>
						<th width="60">Dia*Gauge</th>
		                <th width="60">Finish Dia</th>
		                <th width="60">Stitch Length</th>
		                <th width="60">Yarn Count</th>
		                <th width="60">Brand</th>

		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
			</table>
			<div style=" max-height:350px; width:1950px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body2" width="1930" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tbody>
						<?
						/*$booking_qnty_arr=array();
						$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
						foreach($query as $row)
						{
							$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
						}*/


						$sub_material_recv_arr=array();$sub_material_description_arr=array();
						$subcon_sql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, (b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0");
						foreach($subcon_sql as $row)
						{
							$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
						}

						//var_dump($sub_material_description_arr);die;
						$i=1;
						$f=0;
						$btq=0; $k=0;
						$book_qty_subcon=0;$subcon_tot_book_qty=$sub_tot_batch_wgt=0;$sub_batch_wgt_chk_arr=array();
						$total_sub_tot_batch_wgt=0;
						$batch_chk_arr=array();$sub_qty_chk_arr=array();
						// print_r($sub_batchdata);
						foreach($sub_batchdata as $batch)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$order_id_sub=$batch[csf('po_id')];
							$color_id=$batch[csf('color_id')];
							$booking_no=$batch[csf('booking_no')];
							$sub_challan=$batch[csf('rec_challan')];
							$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
							$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
							$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
							$desc=explode(",",$batch[csf('item_description')]);
							$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
							$entry_form=$batch[csf('entry_form')];
							$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
							$sub_po_num='';
							$sub_job_buyers='';
							$sub_job_buyers='';$subcon_yarn_lot_num='';
							$subcon_booking_qty=0;
							$sub_buyer_style='';
							$machine_dia='';
							$machine_gg='';
							$dia_gauge='';
							foreach($po_ids as $p_id)
							{
								if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
								if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
								if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$sub_po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$sub_po_array[$p_id]['buyer']];
								$subcon_booking_qty+=$sub_material_recv_arr[$p_id];

								//for yarn lot
								//echo $subcon_yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'].', ';
								$subconYlot=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
								$subconYbrand=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['brand'],',');
								$subconYrnCount=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['yrn_count_id'],',');
								$subconYstitchlen=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['stitch_len'],',');
								$subconYdiawidth=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['dia_width_type'],',');


								if($sub_buyer_style=="") $sub_buyer_style=$sub_po_array[$p_id]['style_no']; else $sub_buyer_style.=','.$sub_po_array[$p_id]['style_no'];
								//echo $subconYlot.', ';
								/*if($subcon_yarn_lot_num=='')
									$subcon_yarn_lot_num=$subconYlot;
								else
									$subcon_yarn_lot_num.=",".$subconYlot;*/
									  if($subcon_yarn_lot_num=='') $subcon_yarn_lot_num=$subconYlot;else $subcon_yarn_lot_num.=",".$subconYlot;
									  if($subcon_yarn_brand=='') $subcon_yarn_brand=$subconYbrand;else $subcon_yarn_brand.=",".$subconYbrand;
									  if($subcon_yarn_count=='') $subcon_yarn_count=$subconYrnCount;else $subcon_yarn_count.=",".$subconYrnCount;
									  if($subcon_yarn_stitchlen=='') $subcon_yarn_stitchlen=$subconYstitchlen;else $subcon_yarn_stitchlen.=",".$subconYstitchlen;
									  if($subcon_yarn_diawidth=='') $subcon_yarn_diawidth=$subconYdiawidth;else $subcon_yarn_diawidth.=",".$subconYdiawidth;


								if($machine_dia=='') $machine_dia = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_dia']; else $machine_dia='';
								if($machine_gg=='') $machine_gg = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_gg']; else $machine_gg='';

							}
							$subcon_yarn_lots=implode(",",array_unique(explode(",",$subcon_yarn_lot_num)));
							$subcon_yarn_brands=implode(",",array_unique(explode(",",$subcon_yarn_brand)));
							$subcon_yarn_counts=implode(",",array_unique(explode(",",$subcon_yarn_count)));
							$subcon_yarn_stitchlens=implode(",",array_unique(explode(",",$subcon_yarn_stitchlen)));
							$subcon_yarn_diawidths=implode(",",array_unique(explode(",",$subcon_yarn_diawidth)));

							if( $machine_dia !='' && $machine_gg !='')
							{
								$dia_gauge = $machine_dia.'x'.$machine_gg;
							}
							else if( $machine_gg !='' )
							{
								$dia_gauge = $machine_dia;
							}
							else
							{
								$dia_gauge = $machine_gg;
							}

							$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
							if (!in_array($booking_color2,$sub_qty_chk_arr))
							{
								$k++;
								//echo $subcon_booking_qty;
								$sub_qty_chk_arr[]=$booking_color2;
								$subcon_tot_book_qty=$subcon_booking_qty;
							}
							else
							{
								 $subcon_tot_book_qty=0;
							}

							 $batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$sub_batch_wgt_chk_arr))
                            {
                                $k++;
                                $sub_batch_wgt_chk_arr[]=$batch_id;
                                $sub_tot_batch_wgt=$batch[csf('batch_weight')];
                            }
                            else
                            {
                                $sub_tot_batch_wgt=0;
                            }

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}

							//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot'];
							//echo "<pre>". $batch[csf('prod_id')].'='.$order_id_sub;
							//print_r($yarn_lot_arr);
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            	<? if (!in_array($batch[csf('id')],$batch_chk_arr) )
								{
									$f++;
											?>
					                <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
					                <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
					                <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
					                <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
					                 <td style="word-break:break-all;" width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
					                <td style="word-break:break-all;" width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;" width="50"><p><? echo $sub_job_buyers; ?></p></td>
									<?
					                $batch_chk_arr[]=$batch[csf('id')];

				                }
								else
								{ ?>
					                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
					                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
					                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
					                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
					                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;"  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
									<?
								}
								?>
								<td style="word-break:break-all;" width="80"><p><? echo $sub_buyer_style; ?></p></td>
				                <td style="word-break:break-all;" width="120"><p><? echo $sub_po_num; ?></p></td>
				                <td style="word-break:break-all;" width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? echo implode(",",array_unique(explode(",",$sub_job_num))); ?></p></td>
				                <td style="word-break:break-all;" width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
				                <td width="150" ><p><? echo $batch[csf('item_description')];//$item_descript; ?></p></td>
				                <td style="word-break:break-all;"  width="50" title="<? echo $desc[2];  ?>"><p><? echo $batch[csf('grey_dia')]; ?></p></td>
				                <td  style="word-break:break-all;" width="50"><p><? echo $batch[csf('gsm')]; ?></p></td>
				                 <td style="word-break:break-all;" align="left" width="60"><p><? echo $subcon_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>

				                <td style="word-break:break-all;" align="right" width="70" ><? echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
				                <td style="word-break:break-all;"  align="right" width="50" title="<? echo $sub_tot_batch_wgt; ?>"><? echo number_format($sub_tot_batch_wgt,2); ?></td>
								<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
				                <td style="word-break:break-all;" align="right" width="100" title="SunCon Material Recv Qty"><? echo number_format($subcon_tot_book_qty,2); ?></td>
								<td style="word-break:break-all;" align="center" width="60"><p><? echo $dia_gauge; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60"><p><? echo $subcon_yarn_diawidths; ?></p></td>
								 <td style="word-break:break-all;" align="center" width="60"><p><? echo $subcon_yarn_stitchlens; ?></p></td>
								 <td style="word-break:break-all;" align="center" width="60"><p><? echo $subcon_yarn_counts; ?></p></td>
								 <td style="word-break:break-all;" align="center" width="60"><p><? echo $subcon_yarn_brands;?></p></td>
				                <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')]; ?></td>
				                <td style="word-break:break-all;"><? echo $process_name; ?></td>
				            </tr>
							<?
			                $i++;
			                $btq_subcon+=$batch[csf('sub_batch_qnty')];
							$book_qty_subcon+=$subcon_tot_book_qty;
							$total_sub_tot_batch_wgt+=$sub_tot_batch_wgt;
			                $balance=$book_qty_subcon-$btq_subcon;
			                $bal_qty_subcon=$balance;
			                if($bal_qty_subcon>0)
			                {
			                $color="";
			                $txt="Over Batch Qty";
			                }
			                else if($bal_qty_subcon<0)
			                {
			                $color="red";
			                $txt="Below Batch Qty";
			                }
			            }
			        	 ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="1930" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>

			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="80">&nbsp;</th>

			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_btq_subcon"><? echo $btq_subcon; ?></th>
			                <th width="50" align="right"><? echo $total_sub_tot_batch_wgt; ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo $book_qty_subcon; ?></th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" title="SunCon Material Recv Qty" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="15" align="left">&nbsp;
			                 <? echo number_format($book_qty_subcon,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="15" align="left">&nbsp; <? echo number_format($btq_subcon,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="15" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
				</table>
			</div>
		</div>
		<div align="left"><b>Sample Batch</b>
			<table class="rpt_table" width="2180" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Aganist</th>
		                <th width="80">Batch Color</th>
		                <th width="50">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
		                <th width="70">File No</th>
		                <th width="70">Ref No</th>
		                <th width="100">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="100">Construction</th>
		                <th width="150">Composition</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
		                <th width="50">Trims Weight</th>
						<th width="50">Total Roll</th>
		                <th width="100">Booking Grey Req.Qty</th>
		                <th width="60">Dia*Gauge</th>
		                <th width="60">Finish Dia</th>
		                <th width="60">Stitch Length</th>
		                <th width="60">Yarn Count</th>
		                <th width="60">Brand</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
			</table>
			<div style=" max-height:350px; width:2180px; overflow-y:scroll;" id="scroll_body">
			 	<table class="rpt_table" id="table_body3" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
						<?
						$booking_qnty_arr=array();


						$sam_booking_qnty_arr=array();
						//$sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, (b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 ");
						 $sam_query=sql_select("select a.booking_type, b.po_break_down_id,a.booking_no, b.fabric_color_id, (b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine g  where a.booking_no=b.booking_no and b.po_break_down_id=g.ref_val   and g.user_id = ".$user_id." and g.ref_from=4 and g.entry_form=15 and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  ");
						foreach($sam_query as $row)
						{
							$sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]+=$row[csf('grey_fab_qnty')];
							if($row[csf('booking_type')]==4)
							{
							$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
							}
						}

						/*$smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
						foreach($smn_query as $row)
						{
							$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}
						$sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
						foreach($sam_query as $row)
						{
							 $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}*/

						// GETTING BUYER NAME
						/*$non_order_arr=array();
			            $sql_non_order="SELECT a.company_id,a.grouping as smp_ref, a.buyer_id as buyer_name, b.booking_no, b.bh_qty
			            from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			            where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			            $result_sql_order=sql_select($sql_non_order);
			            foreach($result_sql_order as $row)
			            {

							$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
							$non_order_arr[$row[csf('booking_no')]]['smp_ref']=$row[csf('smp_ref')];
							$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
			            }*/
			            // echo "<pre>";
			            // print_r($non_order_arr);

						$i=1;
						$f=0;
						$b=0;
						$btq=0;$bb=0;
						$tot_book_qty2=0;
						$tot_grey_req_qty=$samp_tot_batch_wgt=$samp_tot_trims_wgt=0;
						$batch_chk_arr=array();
						$booking_chk_arr2=array();$samp_batch_wgt_chk_arr=array();
						// print_r($sam_batchdata );
						$machine_dia='';
						$machine_gg='';
						$dia_gauge='';
						foreach($sam_batchdata as $batch)
						{
							$sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
							if($sam_booking==$batch[csf('booking_no')])
							{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
							$po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$sam_booking_qty=0;
							$sample_yarn_lot_num="";$buyer_style='';
							foreach($po_ids as $p_id)
							{
								if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
								if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
								if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
								if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
								if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];

								if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];

								$sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
								//for yarn lot
								$sampleYlot=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
								$sampleYbrand=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['brand'],',');
								$sampleYyarn_count=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['yarn_count'],',');
								$sampleYstitch_length=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['stitch_length'],',');
								$sampleYwidth=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['width'],',');


								/*if($sample_yarn_lot_num=='')
									$sample_yarn_lot_num=$sampleYlot;
								else
									$sample_yarn_lot_num.=",".$sampleYlot;*/
									if($sample_yarn_lot_num=='') $sample_yarn_lot_num=$sampleYlot;else $sample_yarn_lot_num.=",".$sampleYlot;
									if($sample_yarn_brand=='') $sample_yarn_brand=$sampleYbrand;else $sample_yarn_brand.=",".$sampleYbrand;
									if($sample_yarn_yarn_count=='') $sample_yarn_yarn_count=$sampleYyarn_count;else $sample_yarn_yarn_count.=",".$sampleYyarn_count;
									if($sample_yarn_s_length=='') $sample_yarn_s_length=$sampleYstitch_length;else $sample_yarn_s_length.=",".$sampleYstitch_length;
									if($sample_yarn_width=='') $sample_yarn_width=$sampleYwidth;else $sample_yarn_width.=",".$sampleYwidth;


								if($machine_dia=='') $machine_dia = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_dia']; else $machine_dia='';
								if($machine_gg=='') $machine_gg = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_gg']; else $machine_gg='';
							}

							$sample_yarn_lots=implode(",",array_unique(explode(",",$sample_yarn_lot_num)));
							$sample_yarn_brands=implode(",",array_unique(explode(",",$sample_yarn_brand)));
							$sample_yarn_yarn_counts=implode(",",array_unique(explode(",",$sample_yarn_yarn_count)));
							$sample_yarn_s_lengths=implode(",",array_unique(explode(",",$sample_yarn_s_length)));
							$sample_yarn_widths=implode(",",array_unique(explode(",",$sample_yarn_width)));

							if( $machine_dia !='' && $machine_gg !='')
							{
								$dia_gauge = $machine_dia.'x'.$machine_gg;
							}
							else if( $machine_gg !='' )
							{
								$dia_gauge = $machine_dia;
							}
							else
							{
								$dia_gauge = $machine_gg;
							}

							$order_id=$batch[csf('po_id')];
							$color_id=$batch[csf('color_id')];
							$booking_no=$batch[csf('booking_no')];
							//$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];

							$desc=explode(",",$batch[csf('item_description')]);
							$entry_form=$batch[csf('entry_form')];
							//$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
							//if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
							//if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
							//$po_file=$po_array[$order_id]['file'];
							//$po_ref=$po_array[$order_id]['ref'];
							// $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
							$booking_color=$booking_no;
							if (!in_array($booking_color,$booking_chk_arr2))
							{
								$bb++;
								$booking_chk_arr2[]=$booking_color;
								$tot_book_qty2=$sam_booking_qty;
							}
							else
							{
								$tot_book_qty2=0;
							}
							$batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$samp_batch_wgt_chk_arr))
                            {
                                $b++;
                                $samp_batch_wgt_chk_arr[]=$batch_id;
                                $samp_tot_batch_wgt=$batch[csf('batch_weight')];
                                $samp_tot_trims_wgt=$batch[csf('total_trims_weight')];
                            }
                            else
                            {
                                $samp_tot_batch_wgt=0;
                                $samp_tot_trims_wgt=0;
                            }

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            <? if (!in_array($batch[csf('id')],$booking_chk_arr2))
								{
									$f++;
									?>
                                    <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
                                    <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                    <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                                    <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                    <td style="word-break:break-all;" width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                    <td style="word-break:break-all;" width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                    <td style="word-break:break-all;" width="50"><p><? echo $job_buyers.$buyer_arr[$non_order_arr[$batch[csf('booking_no')]]['buyer_name']]; ?></p></td>
                                    <?
                                    $batch_chk_arr[]=$batch[csf('id')];
				               		// $book_qty+=$booking_qty;
				                  }
								else
								  { ?>
				                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
				                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
				                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
				                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
				                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td style="word-break:break-all;"  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
								<? }
								$samp_ref_no=$non_order_arr[$batch[csf('booking_no')]]['samp_ref_no'];
								$booking_without_order=$batch[csf('booking_without_order')];
								?>
								<td style="word-break:break-all;" width="80"><p><? if($booking_without_order==1) echo $non_order_arr[$batch[csf('booking_no')]]['style_desc'];else echo $buyer_style; ?></p></td>
				                <td style="word-break:break-all;" width="120" ><p><? echo $po_num; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? echo $po_file; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? if($booking_without_order==1) echo $samp_ref_no; else echo $po_ref; ?></p></td>
				                <td style="word-break:break-all;" width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
				                <td style="word-break:break-all;" width="70" title="<? echo $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $job_num; ?></p></td>
				                <td style="word-break:break-all;" width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0]; ?></p></td>
				                <td style="word-break:break-all;" width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
				                <td style="word-break:break-all;" width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
				                <td style="word-break:break-all;" width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
				                 <td style="word-break:break-all;" align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $sample_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
				                <td style="word-break:break-all;" align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
				                <td style="word-break:break-all;" align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($samp_tot_batch_wgt,2); ?></td>
				                <td style="word-break:break-all;" align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($samp_tot_trims_wgt,2); ?></td>
								<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
				                <td title="Booking Color Wise Qty" style="word-break:break-all;" align="right" width="100"><? echo number_format($tot_book_qty2,2);?></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $dia_gauge; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $sample_yarn_widths; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $sample_yarn_s_lengths; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $sample_yarn_yarn_counts; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $sample_yarn_brands; ?></p></td>
				                <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')];?></td>
				                <td style="word-break:break-all;"><? echo $process_name;?></td>
				            </tr>
							<?
				                $i++;
				                $sam_btq+=$batch[csf('batch_qnty')];
								$tot_grey_req_qty+=$tot_book_qty2;
								$total_samp_tot_batch_wgt+=$samp_tot_batch_wgt;
								$total_samp_tot_trims_wgt+=$samp_tot_trims_wgt;
				                $sam_balance=$tot_grey_req_qty-$sam_btq;
				                $sam_bal_qty=$sam_balance;
				                if($sam_bal_qty>0)
				                {
				                $color="";
				                $txt="Over Batch Qty";
				                }
				                else if($sam_bal_qty<0)
				                {
				                $color="red";
				                $txt="Below Batch Qty";
				                }
				                }
			                }
			        	 ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_sam_btq"><? echo number_format($sam_btq,2); ?></th>
			                <th width="50" align="right"><? echo number_format($total_samp_tot_batch_wgt,2); ?></th>
			                <th width="50" align="right"><? echo number_format($total_samp_tot_trims_wgt,2); ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="20" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="20" align="left">&nbsp; <? echo number_format($sam_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="20" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
			 	</table>
			</div><br/>
		</div>
		<?
  	}

	/*
	|--------------------------------------------------------------------------
	| Self Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==1)
	{
		?>
	 	<div align="left"> <b>Self Batch </b></div>
	 	<table class="rpt_table" width="1990" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="75">Batch Date</th>
	                <th width="60">Batch No</th>
	                <th width="40">Ext. No</th>
	                <th width="80">Batch Against</th>
	                <th width="80">Batch Color</th>
	                <th width="80">Buyer</th>
	                <th width="80">Style Ref</th>
	                <th width="120">PO No</th>
                    <th width="100">Pub Ship date</th>
	                <th width="70">File No</th>
	                <th width="70">Ref. No</th>
	                <th width="80">W/O NO.</th>
	                 <th width="70">Job</th>
	                <th width="100">Construction</th>
	                <th width="150">Composition</th>
	                <th width="50">Dia/ Width</th>
	                <th width="50">GSM</th>
	                <th width="60">Lot No</th>
	                <th width="70">Batch Qty.</th>
	                <th width="50">Batch Weight</th>
	                <th width="50">Trims Weight</th>
					<th width="50">Total Roll</th>
	                <th width="100">Grey Req.Qty</th>
	                <th width="100">Remarks</th>
	                <th>Process Name</th>
	            </tr>
	        </thead>
	 	</table>
		<div style=" max-height:350px; width:1990px; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" id="table_body" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
		 		<tbody>
				<?
                $booking_qnty_arr=array();
               // $query=sql_select("select a.booking_type, b.po_break_down_id,a.booking_no, b.fabric_color_id, (b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 ");
			    $queryFab=sql_select("select a.booking_type,b.po_break_down_id,a.booking_no, b.fabric_color_id, (b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b,gbl_temp_engine g where a.booking_no=b.booking_no and b.po_break_down_id=g.ref_val   and g.user_id = ".$user_id." and g.ref_from=4 and g.entry_form=15  and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  ");
                foreach($queryFab as $row)
                {
                    $booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]+=$row[csf('grey_fab_qnty')];
					if($row[csf('booking_type')]==4)
					{
					 $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
					}
                }

               /* $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($smn_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($sam_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }*/
                $i=1;
                $f=0;
                $b=0;
                $btq=0;
                $tot_book_qty=0;
                $tot_grey_req_qty=0;
                $batch_chk_arr=array();$booking_chk_arr=array();
                foreach($batchdata as $batch)
                {
                    $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
                    //if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
                    if($sam_booking!=$batch[csf('booking_no')])
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $order_id=$batch[csf('po_id')];
                            $order_id_ex = array_unique(explode(",", $order_id));
                            $order_id = implode(",", $order_id_ex);
                            $color_id=$batch[csf('color_id')];
                            $booking_no=$batch[csf('booking_no')];
                            $booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
                            $desc=explode(",",$batch[csf('item_description')]);
                            $entry_form=$batch[csf('entry_form')];
                            $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
                            $po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$yarn_lot_num='';
                            $grey_booking_qty=0;$buyer_style='';$ship_DateCond='';
                            foreach($po_ids as $p_id)
                            {
                                if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
                                if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
                                if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
                                if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
                                if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
                                $ylot=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
                                if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
                                $grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];
                                if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
								$pub_shipment_date=$po_array[$p_id]['pub_shipment_date'];
								if($ship_DateCond=='') $ship_DateCond=$pub_shipment_date;else $ship_DateCond.=",".$pub_shipment_date;
                            }
                            $yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));
                            // $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                            // $book_qty+=$booking_qty;
                            $booking_color=$order_id.$booking_no.$color_id;
                            if (!in_array($booking_color,$booking_chk_arr))
                            {
                                $b++;
                                $booking_chk_arr[]=$booking_color;
                                $tot_book_qty=$grey_booking_qty;
                            }
                            else
                            {
                                $tot_book_qty=0;
                            }

                           //echo  $book_qty;


                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
                           ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                            <? if (!in_array($batch[csf('id')],$batch_chk_arr) )
                            {
                                $f++;
                                ?>
                                <td width="30" style="word-break:break-all;"><? echo $f; ?></td>
                                <td align="center" width="75" style="word-break:break-all;" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                <td width="60" style="word-break:break-all;" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                                <td  width="40" style="word-break:break-all;" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                 <td  width="80" style="word-break:break-all;"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                                <td width="80" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers;//; ?></div></td>
                                <?
                                $batch_chk_arr[]=$batch[csf('id')];
                               // $book_qty+=$booking_qty;
                            }
                            else
                            {
                                ?>
                                <td width="30" style="word-break:break-all;"><? //echo $sl; ?></td>
                                <td width="75" style="word-break:break-all;"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
                                <td width="60" style="word-break:break-all;"><p><? //echo $booking_qty; ?></p></td>
                                <td width="40" style="word-break:break-all;"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                <?
                            }
                            ?>
                            <td width="80" style="word-break:break-all;"><p><? echo $buyer_style; ?></p></td>
                            <td width="120" style="word-break:break-all;"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $ship_DateCond; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo $po_file; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><?  echo $po_ref; ?></p></td>
                            <td width="80" style="word-break:break-all;"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo $job_num; ?></p></td>
                            <td width="100" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
                            <td width="150" style="word-break:break-all;"><div style="width:150px; word-wrap:break-word;"><?
                            if($desc[4]!="")
                            {
                            	$compositions= $desc[1].' ' . $desc[2];
                            	$gsms= $desc[3];
                            }
                            else
                            {
                            	$compositions= $desc[1];
                            	$gsms= $desc[2];
                            }
                            echo $compositions;

                            ?></div></td>
                            <td width="50" style="word-break:break-all;"><p><? echo end($desc);//$desc[3]; ?></p></td>
                            <td width="50" style="word-break:break-all;"><p><? echo $gsms;//$desc[2]; ?></p></td>
                            <td style="word-break:break-all;" title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
                            <td align="right" width="70" style="word-break:break-all;" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                            <td align="right" width="50" style="word-break:break-all;" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
                            <td align="right" width="50" style="word-break:break-all;" title="<? echo $batch[csf('total_trims_weight')]; ?>"><? echo number_format($batch[csf('total_trims_weight')],2); ?></td>
							<td width="50" align="right" style="word-break:break-all;"><? echo $batch[csf('roll_no')];?></td>
                            <td align="right" width="100" style="word-break:break-all;"><? echo number_format($tot_book_qty,2);?></td>
                            <td width="100" style="word-break:break-all;"><? echo $batch[csf('')];?></td>
                            <td style="word-break:break-all;"><? echo $process_name;?></td>
                            </tr>
                            <?
                            $i++;
                            $btq+=$batch[csf('batch_qnty')];
                            $tot_grey_req_qty+=$tot_book_qty;
                            $balance=$tot_grey_req_qty-$btq;
                            $bal_qty=$balance;
                            if($bal_qty>0)
                            {
                                $color="";
                                $txt="Over Batch Qty";
                            }
                            else if($bal_qty<0)
                            {
                                $color="red";
                                $txt="Below Batch Qty";
                            }
                        }
                    }
                 ?>
		 		</tbody>
			</table>
			<table class="rpt_table" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <tr>
		                <th width="30">&nbsp;</th>
		                <th width="75">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="40">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>

		                <th width="70">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="150">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="70" id="value_total_btq"><? echo number_format($btq,2); ?></th>
		                <th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
		                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
		                <th width="100">&nbsp;</th>
		                <th>&nbsp;</th>
		            </tr>
		            <tr>
		                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
		                <td colspan="12" align="left">&nbsp;
		                 <? echo number_format($tot_grey_req_qty,2); ?>
		                </td>
		            </tr>
		             <tr>
		                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
		                <td colspan="12" align="left">&nbsp; <? echo number_format($btq,2); ?> </td>
		            </tr>
		             <tr>
		                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
		                <td colspan="12" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
		            </tr>
		        </tfoot>
			</table>
		</div><br/>
		<?
	}
	/*
	|--------------------------------------------------------------------------
	| Subcon Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==2)
	{
		?>
		<div align="left"> <b>SubCond Batch</b></div>
	 	<table class="rpt_table" width="1650" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="75">Batch Date</th>
	                <th width="60">Batch No</th>
	                <th width="40">Ext. No</th>
	                <th width="80">Batch Aganist</th>
	                <th width="80">Batch Color</th>
	                <th width="50">Buyer</th>
	                <th width="80">Style Ref</th>
	                <th width="120">PO No</th>
	                <th width="80">W/O NO.</th>
	                <th width="70">Job</th>
	                <th width="80">Recv. Challan</th>
	                <th width="150">Fabrics Desc.</th>
	                <th width="50">Dia/ Width</th>
	                <th width="50">GSM</th>
	                <th width="60">Lot No</th>
	                <th width="70">Batch Qty.</th>
	                <th width="50">Batch Weight</th>
					<th width="50">Total Roll</th>
	                <th width="100">Grey Req. Qty</th>
	                <th width="100">Remarks</th>
	                <th>Process Name</th>
	            </tr>
	        </thead>
		</table>
		<div style=" max-height:350px; width:1650px; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" id="table_body2" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tbody>
					<?
					/*$booking_qnty_arr=array();
					$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
					foreach($query as $row)
					{
						$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
					}*/


					$sub_material_recv_arr=array();$sub_material_description_arr=array();
					$subconsql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, sum(b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0 group by b.order_id, a.chalan_no,b.color_id,a.trans_type,b.material_description,b.gsm");
					foreach($subconsql as $row)
					{
						$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
					}

					//var_dump($sub_material_description_arr);die;
					$i=1;
					$f=0;
					$btq=0; $k=0;
					$book_qty_subcon=0;$subcon_tot_book_qty=0;
					$batch_chk_arr=array();$sub_qty_chk_arr=array();
					 //print_r($sub_batchdata);
					foreach($sub_batchdata as $batch)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_id_sub=$batch[csf('po_id')];
						$color_id=$batch[csf('color_id')];
						$booking_no=$batch[csf('booking_no')];
						$sub_challan=$batch[csf('rec_challan')];
						$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
						$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
						$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
						$desc=explode(",",$batch[csf('item_description')]);
						$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));

						$entry_form=$batch[csf('entry_form')];

						$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
						$sub_po_num='';	$sub_job_buyers='';$sub_job_buyers='';
						$subcon_booking_qty=0;$sub_buyer_style='';
						foreach($po_ids as $p_id)
						{
							if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
							if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
							if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$sub_po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$sub_po_array[$p_id]['buyer']];
							$subcon_booking_qty+=$sub_material_recv_arr[$p_id];
							//for yarn lot
							$subconYlot=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
							if($subcon_yarn_lot_num=='')
								$subcon_yarn_lot_num=$subconYlot;
							else
								$subcon_yarn_lot_num.=",".$subconYlot;
							if($sub_buyer_style=="") $sub_buyer_style=$sub_po_array[$p_id]['style_no']; else $sub_buyer_style.=','.$sub_po_array[$p_id]['style_no'];
						}

						$subcon_yarn_lots=implode(",",array_unique(explode(",",$subcon_yarn_lot_num)));
						$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
						if (!in_array($booking_color2,$sub_qty_chk_arr))
						{ $k++;

							//echo $subcon_booking_qty;
							 $sub_qty_chk_arr[]=$booking_color2;
							  $subcon_tot_book_qty=$subcon_booking_qty;
						}
						else
						{
							 $subcon_tot_book_qty=0;
						}

						$process_name = '';
						$process_id_array = explode(",", $batch[csf("process_id")]);
						foreach ($process_id_array as $val)
						{
							if ($process_name == ""){
								$process_name = $conversion_cost_head_array[$val];
							}
							else{
								$process_name .= "," . $conversion_cost_head_array[$val];
							}
						}
						?>
			            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
			            	<? if (!in_array($batch[csf('id')],$batch_chk_arr) )
							{
								$f++;
										?>
				                <td width="30"><? echo $f; ?></td>
				                <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
				                <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
				                <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
				                 <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
				                <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td width="50"><p><? echo $sub_job_buyers; ?></p></td>
								<?
				                $batch_chk_arr[]=$batch[csf('id')];

			                }
							else
							{ ?>
				                <td width="30"><? //echo $sl; ?></td>
				                <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
				                <td   width="60"><p><? //echo $booking_qty; ?></p></td>
				                <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
				                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
								<?
							}
							?>
							<td width="80"><p><? echo $sub_buyer_style; ?></p></td>
			                <td width="120"><p><? echo $sub_po_num; ?></p></td>
			                <td width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
			                <td width="70"><p><? echo $sub_job_num; ?></p></td>
			                <td width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
			                <td width="150" ><p><? echo $item_descript; ?></p></td>
			                <td  width="50" title="<? echo $desc[2];  ?>"><p><? echo $desc[3]; ?></p></td>
			                <td  width="50"><p><? echo $gsm_subcon; ?></p></td>
			                <td align="left" width="60"><p><? echo $subcon_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>
			                <td align="right" width="70" ><? echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
			                <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
							<td width="50" align="right"><? echo $batch[csf('roll_no')]; ?></td>
			                <td width="100"><? echo number_format($subcon_tot_book_qty,2); ?></td>
			                <td width="100"><? echo $batch[csf('remarks')]; ?></td>
			                <td><? echo $process_name; ?></td>
			            </tr>
						<?
		                $i++;
		                $btq_subcon+=$batch[csf('sub_batch_qnty')];
						$book_qty_subcon+=$subcon_tot_book_qty;
		                $balance=$book_qty_subcon-$btq_subcon;
		                $bal_qty_subcon=$balance;
		                if($bal_qty_subcon>0)
		                {
		                $color="";
		                $txt="Over Batch Qty";
		                }
		                else if($bal_qty_subcon<0)
		                {
		                $color="red";
		                $txt="Below Batch Qty";
		                }
		            }
		        	 ?>
		       </tbody>
			</table>
			<table class="rpt_table" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <tr>
		                <th width="30">&nbsp;</th>
		                <th width="75">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="40">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="120">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>

		                <th width="150">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="70" id="value_total_btq_subcon"><? echo $btq_subcon; ?></th>
		                <th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
		                <th width="100"><? //echo $book_qty_subcon; ?></th>
		                <th width="100">&nbsp;</th>
		                <th>&nbsp;</th>
		            </tr>
		            <tr>
		                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
		                <td colspan="9" align="left">&nbsp;
		                 <? echo number_format($book_qty_subcon,2); ?>
		                </td>
		            </tr>
		             <tr>
		                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
		                <td colspan="9" align="left">&nbsp; <? echo number_format($btq_subcon,2); ?> </td>
		            </tr>
		             <tr>
		                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
		                <td colspan="9" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
		            </tr>
		        </tfoot>
			</table>
		</div>
		<?
	}

	/*
	|--------------------------------------------------------------------------
	| Sample Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==3)
	{
		if($cbo_type==1)
		{
			?>
			<div align="left"> <b>Sample Batch </b></div>
			<table class="rpt_table" id="table_header_1" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="75">Batch Date</th>
                        <th width="60">Batch No</th>
                        <th width="40">Ext. No</th>
                        <th width="80">Batch Aganist</th>
                        <th width="80">Batch Color</th>
                        <th width="50">Buyer</th>
                        <th width="80">Style Ref</th>
                        <th width="120">PO No</th>
                        <th width="70">File No</th>
                        <th width="70">Ref No</th>
                        <th width="100">W/O NO.</th>
                        <th width="70">Job</th>
                        <th width="100">Construction</th>
                        <th width="150">Composition</th>
                        <th width="50">Dia/ Width</th>
                        <th width="50">GSM</th>
                        <th width="60">Lot No</th>
                        <th width="70">Batch Qty.</th>
                        <th width="50">Batch Weight</th>
                        <th width="50">Trims Weight</th>
						<th width="50">Total Roll</th>
                        <th width="100">Grey Req.Qty</th>
                        <th width="100">Remarks</th>
                        <th>Process Name</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1880px; overflow-y:scroll;" id="scroll_body">
			 	<table class="rpt_table" id="table_body3" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
					<?
                    $sam_booking_qnty_arr=array();
                    $sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
                    }

                    $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($smn_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }
                    $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }


                    $i=1;
                    $f=0;
                    $b=0;
                    $btq=0;
                    $tot_book_qty2=0;
                    $tot_grey_req_qty=0;
                    $batch_chk_arr=array();$booking_chk_arr2=array();
                    // print_r($sam_batchdata );
                    foreach($sam_batchdata as $batch)
                    {
                        $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];

                        if($sam_booking==$batch[csf('booking_no')])
                        {
	                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
	                        $po_num='';	$po_file='';$po_ref='';$samp_job_num='';$job_buyers='';$sam_booking_qty=0;$buyer_style='';
	                        foreach($po_ids as $p_id)
	                        {
	                            if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
	                            if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
	                            if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
	                            if($samp_job_num=='') $samp_job_num=$po_array[$p_id]['job_no'];else $samp_job_num.=",".$po_array[$p_id]['job_no'];
	                            if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
	                            $sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
	                            //for yarn lot
	                            $sampleYlot=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
	                            if($sample_yarn_lot_num=='')
	                                $sample_yarn_lot_num=$sampleYlot;
	                            else
	                                $sample_yarn_lot_num.=",".$sampleYlot;
	                            if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
	                        }

	                        $sample_yarn_lots=implode(",",array_unique(explode(",",$sample_yarn_lot_num)));
	                        $order_id=$batch[csf('po_id')];
	                        $color_id=$batch[csf('color_id')];
	                        $booking_no=$batch[csf('booking_no')];
	                        //$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];

	                        $desc=explode(",",$batch[csf('item_description')]);
	                        $entry_form=$batch[csf('entry_form')];
	                        //$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
	                        //if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
	                        //if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
	                        //$po_file=$po_array[$order_id]['file'];
	                        //$po_ref=$po_array[$order_id]['ref'];
	                        // $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
	                        $booking_color=$order_id.$booking_no.$color_id;
	                        if (!in_array($booking_color,$booking_chk_arr2))
	                        {
	                            $b++;
	                            $booking_chk_arr2[]=$booking_color;
	                            $tot_book_qty2=$sam_booking_qty;
	                        }
	                        else
	                        {
	                            $tot_book_qty2=0;
	                        }
	                        //echo  $batch[csf('po_id')].', ';

	                        $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
		                        <?
		                        if (!in_array($batch[csf('id')],$booking_chk_arr2))
		                        {
		                            $f++;
		                            ?>
		                            <td width="30"><? echo $f; ?></td>
		                            <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
		                            <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
		                            <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
		                            <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
		                            <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td width="50"><p><? echo $buyer_arr[$non_order_arr[$batch[csf('booking_no')]]['buyer_name']]; ?></p></td>
		                            <?
		                            $batch_chk_arr[]=$batch[csf('id')];
		                            // $book_qty+=$booking_qty;
		                        }
	                            else
	                            { ?>
		                            <td width="30"><? //echo $sl; ?></td>
		                            <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
		                            <td   width="60"><p><? //echo $booking_qty; ?></p></td>
		                            <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
		                            <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
		                            <?
		                        }
								$samp_ref_no= $non_order_arr[$batch[csf('booking_no')]]['samp_ref_no'];
								$booking_without_order=$batch[csf('booking_without_order')];
	                            ?>
	                            <td width="80" title="<? echo $booking_without_order; ?>"><p><? if($booking_without_order==1) echo $style_ref_no_lib[$non_order_arr[$batch[csf('booking_no')]]['style_id']]; else echo $buyer_style; ?></p></td>
	                            <td width="120" ><p><? if($batch[csf('po_id')]>1) echo $po_num;else echo ""; ?></p></td>
	                            <td width="70"><p><? echo $po_file; ?></p></td>
	                             <td width="70"><p><? if($booking_without_order==1) echo $samp_ref_no;else echo $po_ref; ?></p></td>
	                            <td width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                            <td width="70" title="poID=<? echo $batch[csf('po_id')]; ?>"><p><? if($batch[csf('po_id')]>1) echo $samp_job_num;else echo ""; ?></p></td>
	                            <td width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0];//$batch[csf('grey_dia')];; ?></p></td>
	                            <td width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
	                            <td  width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
	                            <td  width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
	                             <td align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $sample_yarn_lots; //$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
	                            <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
	                            <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
	                            <td  align="right" width="50" title="<? echo $batch[csf('total_trims_weight')]; ?>"><? echo number_format($batch[csf('total_trims_weight')],2); ?></td>
								<td width="50" align="right"><? echo $batch[csf('roll_no')];?></td>
	                            <td align="right" width="100"><? echo number_format($tot_book_qty2,2);?></td>
	                            <td width="100"><? echo $batch[csf('remarks')];?></td>
	                            <td><p><? echo $process_name;?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                        $sam_btq+=$batch[csf('batch_qnty')];
	                        $tot_grey_req_qty+=$tot_book_qty2;
	                        $sam_balance=$tot_grey_req_qty-$sam_btq;
	                        $sam_bal_qty=$sam_balance;
	                        if($sam_bal_qty>0)
	                        {
	                        	$color="";
	                        	$txt="Over Batch Qty";
	                        }
	                        else if($sam_bal_qty<0)
	                        {
		                        $color="red";
		                        $txt="Below Batch Qty";
	                        }
                        }
                    }
                    ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_sam_btq"><? echo number_format($sam_btq,2); ?></th>
			                <th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="10" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="10" align="left">&nbsp; <? echo number_format($sam_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="10" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
			 	</table>
			</div><br/>
		 	<?
		}
  	}
   	?>
	</div>
	</fieldset>
	</div>
	<?

	/*$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$total_data####$filename####$batch_type"; */

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
	echo "$total_data####$filename####$batch_type";

	disconnect($con);
	exit();
}

if($action=="batch_report__old")
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
	$floor_no = str_replace("'","",$cbo_floor);
	//echo $cbo_batch_type;die;
	/*echo $floor_no;die;*/
	$batch_number_hidden = str_replace("'","",$batch_number);
	$booking_no = str_replace("'","",$txt_booking_no);
	$booking_number_hidden = str_replace("'","",$txt_hide_booking_id);

	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$year = str_replace("'","",$cbo_year);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	//echo $order_no;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$process=33;
	//echo str_replace("'","",$batch_no);die;

	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='$buyer'";
	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_name='$buyer'";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".str_replace("'","",$batch_no)."'";
	if ($booking_no=="") $booking_num=""; else $booking_num="  and a.booking_no like '%".str_replace("'","",$booking_no)."%'";
	if ($floor_no==0) $floor_num=""; else $floor_num="  and a.floor_id='".$floor_no."'";
	if ($file_no=="") $file_cond=""; else $file_cond="  and b.file_no='".$file_no."'";

	if ($batch_no=="") $batch_num2=""; else $batch_num2="  and batch_no='".str_replace("'","",$batch_no)."'";
	if ($ref_no=="")
	{
		$ref_cond="";
		$ref_cond2="";

	}
	else
	{
	$ref_cond="  and b.grouping='$ref_no'";
	$ref_cond2="  and c.grouping='$ref_no'";
	}
	if ($company==0) $comp_cond=""; else $comp_cond=" and a.company_id=$company";
	if ($working_company==0) $working_comp_cond=""; else $working_comp_cond=" and a.working_company_id=$working_company";
	//a.company_id=$company

	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if($batch_type==0 || $batch_type==1 ||  $batch_type==3)
	{
		if ($txt_order=="") $order_no_cond=""; else $order_no_cond="  and b.po_number='$txt_order'";
		if ($txt_order=="") $order_no2=""; else $order_no2="  and c.po_number='$txt_order'";
		if ($job_number_id=="") $jobdata=""; else $jobdata="  and a.job_no_prefix_num in($job_number_id)";
		if ($job_number_id=="") $jobdata3=""; else $jobdata3="  and d.job_no_prefix_num in($job_number_id)";
	}
	//echo $jobdata;
	if($batch_type==0 || $batch_type==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num in(".$job_number_id.") "; else $sub_job_cond="";
		if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	}
	if ($buyer==0) $samp_buyercond=""; else $samp_buyercond=" and c.buyer_id=".$buyer." ";

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
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}

	$po_array=array();
	$po_sql=sql_select("select a.job_no_prefix_num 	as job_no,a.buyer_name,a.style_ref_no, b.file_no,b.grouping as ref,b.id, b.po_number,b.pub_shipment_date,c.mst_id as batch_id,d.is_sales,d.sales_order_id from wo_po_details_master a, wo_po_break_down b,pro_batch_create_dtls c,pro_batch_create_mst d where a.id=b.job_id and c.po_id=b.id and d.id=c.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0  and c.status_active in (1) and c.is_deleted=0 and d.status_active in (1) and d.is_deleted=0 $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2");



	$poid='';
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$po_array[$row[csf('id')]]['style_no']=$row[csf('style_ref_no')];
		$po_array[$row[csf('id')]]['ref']=$row[csf('ref')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];

		$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
		if($row[csf('is_sales')]==1)
		{
			$sales_id_array[$row[csf('sales_order_id')]]=$row[csf('sales_order_id')];
		}
		$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		if($poid=='') $poid=$row[csf('id')]; else $poid.=",".$row[csf('id')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
	}
	//echo 'DD';;die;
	$batch_cond_for_in=where_con_using_array($batch_id_arr,0,"a.id");
	$sales_cond_for_in=where_con_using_array($sales_id_array,0,"a.id");
	//echo $poid.'SSSSSSSSSSSS';;die;
	//for sales order entry
	if ($buyer==0) $buyer_cond2=""; else $buyer_cond2="  and b.buyer_id='$buyer'";
	$sales_po_array=array();
	$sales_po_sql=sql_select("select  a.job_no as po_number,a.buyer_id as buyer_name,a.style_ref_no,a.id from fabric_sales_order_mst a, pro_batch_create_mst b where a.id=b.sales_order_id and b.is_sales=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond2 $year_cond $sales_cond_for_in ");
	$sales_poid='';
	foreach($sales_po_sql as $row)
	{
		$sales_po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$sales_po_array[$row[csf('id')]]['style_no']=$row[csf('style_ref_no')];
		$sales_po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		if($sales_poid=='') $sales_poid=$row[csf('id')]; else $sales_poid.=",".$row[csf('id')];
	} //echo $sales_poid;die;



	$sub_po_array=array();
	$sub_po_sql=sql_select("select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d  where d.subcon_job=c.job_no_mst  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond");
	/*echo "select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d  where d.subcon_job=c.job_no_mst  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond";die;*/
	$sub_poid='';
	foreach($sub_po_sql as $row)
	{
		$sub_po_array[$row[csf('id')]]['po_no']=$row[csf('order_no')];
		$sub_po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$sub_po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
		if($sub_poid=='') $sub_poid=$row[csf('id')]; else $sub_poid.=",".$row[csf('id')];
	}
	 // GETTING BUYER NAME
	$non_order_arr=array();
	$sql_non_order="SELECT c.id,c.company_id,c.grouping as samp_ref_no,c.style_desc, c.buyer_id as buyer_name, b.booking_no, b.bh_qty, b.style_id
	from wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls b
	where c.booking_no=b.booking_no and c.booking_type=4 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 $ref_cond2";
	// echo $sql_non_order;
	$result_sql_order=sql_select($sql_non_order);
	foreach($result_sql_order as $row)
	{

		$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
		$non_order_arr[$row[csf('booking_no')]]['samp_ref_no']=$row[csf('samp_ref_no')];
		$non_order_arr[$row[csf('booking_no')]]['style_desc']=$row[csf('style_desc')];
		$non_order_arr[$row[csf('booking_no')]]['style_id']=$row[csf('style_id')];
		$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
		$non_booking_id_r_arr[$row[csf('id')]]=$row[csf('id')];
	}
	// echo "<pre>";
	// print_r($non_order_arr);
	$style_ref_no_lib=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	if($ref_no!="")
	{
		$booking_ids=count($non_booking_id_r_arr);
		if($db_type==2 && $booking_ids>1000)
		{
			$non_booking_cond_for=" and (";
			$bookIdsArr=array_chunk($non_booking_id_r_arr,999);
			foreach($bookIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$non_booking_cond_for.=" a.booking_no_id in($ids) or";
			}
			$non_booking_cond_for=chop($non_booking_cond_for,'or ');
			$non_booking_cond_for.=")";
		}
		else
		{
			$non_booking_cond_for=" and a.booking_no_id in(".implode(",",$non_booking_id_r_arr).")";
		}
	}
	//echo $non_booking_cond_for.'DDDDDDDDD';die;
	//if($sub_poid=="") $sub_poid=0;else $sub_poid=$sub_poid;
	//echo $sub_poid.'gfgf';
	$po_id="";
	if($txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
	{
		$po_id=$poid;
	}
	//echo $po_id;
	$sub_po_id="";
	if($txt_order!="" || $job_number_id!=""  || $year!=0)
	{
		$sub_po_id=$sub_poid;
	}

	$po_id_cond="";
	if($po_id!="")
	{
		//echo $po_id=substr($po_id,0,-1);
		$po_id=chop($po_id,',');
		if($db_type==0) $po_id_cond="and b.po_id in(".$po_id.")";
		else
		{
			$po_ids=array_unique(explode(",",$po_id));
			if(count($po_ids)>990)
			{
				$po_id_cond="and (";
				$po_ids=array_chunk($po_ids,990);
				$z=0;
				foreach($po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $po_id_cond.=" b.po_id in(".$id.")";
					else $po_id_cond.=" or b.po_id in(".$id.")";
					$z++;
				}
				$po_id_cond.=")";
			}
			else{
			$po_id=implode(",",array_unique(explode(",",$po_id)));
			$po_id_cond="and b.po_id in(".$po_id.")";}
		}
	}
	//echo $po_id_cond;die;
	$sub_po_id_cond="";
	if($sub_po_id!="")
	{
		//$sub_po_id=substr($sub_po_id,0,-1);
		$sub_po_id=chop($sub_po_id,',');
		if($db_type==0) $sub_po_id_cond="and b.po_id in(".$sub_po_id.")";
		else
		{
			$sub_po_ids=array_unique(explode(",",$sub_po_id));
			if(count($sub_po_ids)>990)
			{
				$sub_po_id_cond="and (";
				$sub_po_ids=array_chunk($sub_po_ids,990);
				$z=0;
				foreach($sub_po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $sub_po_id_cond.=" b.po_id in(".$id.")";
					else $sub_po_id_cond.=" or b.po_id in(".$id.")";
					$z++;
				}
				$sub_po_id_cond.=")";
			}
			else {
			$sub_po_id=implode(",",array_unique(explode(",",$sub_po_id)));
			$sub_po_id_cond="and b.po_id in(".$sub_po_id.")";}
		}
	}
	//echo  $sub_po_id_cond;
	//echo $po_id.'aaas';

	$sql_dyeing_subcon=sql_select("select c.batch_id,c.entry_form from  pro_fab_subprocess c,pro_batch_create_mst a where a.id=c.batch_id and c.entry_form in(38,35,32,47,31,48) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.batch_id>0 $batch_cond_for_in $batch_num2 $dates_com $batch_num ");
	//echo "select c.batch_id,c.entry_form from  pro_fab_subprocess c,pro_batch_create_mst a where a.id=c.batch_id and c.entry_form in(38,35,32,47,31,48) and c.status_active=1 and c.is_deleted=0 and batch_id>0 $batch_num2 $dates_com $batch_num ";
	//echo "select batch_id,entry_form from  pro_fab_subprocess where entry_form in(38,35,32,47,31,48) and status_active=1 and is_deleted=0 and batch_id>0 $batch_num2";//die;
	//die;
	$k=1;$i=1;$m=1;$n=1;$p=1;$j=1;$h=1;
	foreach($sql_dyeing_subcon as $row_sub)
	{
		if($row_sub[csf('entry_form')]==38)
		{
		if($k!==1) $sub_cond_d.=",";
		$sub_cond_d.=$row_sub[csf('batch_id')];
		$k++;
		}
		if($row_sub[csf('entry_form')]==35)
		{
		if($i!==1) $row_d.=",";
		$row_d.=$row_sub[csf('batch_id')];
		$i++;
		}
		if($row_sub[csf('entry_form')]==32)
		{
		if($m!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$m++;
		}
		if($row_sub[csf('entry_form')]==47)
		{
		if($n!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$n++;
		}
		if($row_sub[csf('entry_form')]==31)
		{
		if($p!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$p++;
		}
		if($row_sub[csf('entry_form')]==48)
		{
		if($j!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$j++;
		}

	}//echo $sub_cond;die;

	/*$sql_batch_dyeing=sql_select("select batch_id from  pro_fab_subprocess where entry_form=35 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dyeing as $row_dyeing)
	{
		if($i!==1) $row_d.=",";
		$row_d.=$row_dyeing[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_h)
	{
		if($i!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$i++;
	}
	/*$sub_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sub_batch_h as $subrow_h)
	{
		if($i!==1) $subrow_heat.=",";
		$subrow_heat.=$subrow_h[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=47 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_s)
	{
		if($i!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$i++;
	}
	$sql_batch_dry=sql_select("select batch_id from  pro_fab_subprocess where entry_form=31 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dry as $rowdry)
	{
		if($i!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$i++;
	}
	$sql_batch_stenter=sql_select("select batch_id from  pro_fab_subprocess where entry_form=48 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_stenter as $row_sten)//Stentering
	{
		if($i!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$i++;
	}*/

	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/
	if($db_type==0)
	{
		if($cbo_type==1) //Date Wise Report
		{
			if($batch_type==1) // Self
			{
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					$sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.style_ref_no,a.floor_id,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=0 and a.batch_against!=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $floor_num $year_cond $booking_num  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks order by a.batch_date)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against!=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $po_id_cond  $year_cond $booking_num  GROUP BY a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and b.po_id=0 and a.entry_form=0 and a.batch_against!=3   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
				}
			}
			else if($batch_type==2) //SubCon
			{
				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type,b.grey_dia, a.process_id, a.remarks order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where   a.id=b.mst_id  and a.entry_form=36 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.grey_dia, a.process_id, a.remarks)
				union
				(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where    a.id=b.mst_id and a.entry_form=36 and  b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,b.grey_dia, a.process_id, a.remarks) order by batch_date";
				}
			}
			else if($batch_type==3) // Sample batch
			{
				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
				}
				else
				{*/
					$sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id  and  a.entry_form=0 and a.batch_against=3 and b.po_id>0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $non_booking_cond_for $working_comp_cond $dates_com  $batch_num  $ext_no $floor_num $year_cond $booking_num $po_id_cond GROUP BY a.id,a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c where a.id=b.mst_id and c.booking_no=a.booking_no  and  a.entry_form=0 and a.batch_against=3 and b.po_id=0 and b.status_active=1 and c.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $samp_buyercond $ext_no $floor_num $year_cond $non_booking_cond_for $booking_num  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.width_dia_type, a.process_id, a.remarks)  order by batch_date";
				//}
			}
			else if($batch_type==0) // All batch
			{
				// Self
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					$sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id  and  a.entry_form=0 and a.batch_against!=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $floor_num $year_cond $booking_num  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id order by a.batch_date, a.process_id, a.remarks)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against!=3  and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $po_id_cond  $year_cond $booking_num  GROUP BY a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id  and a.entry_form=0 and a.batch_against!=3  and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
				}

				// Subcon

				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=36  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type,b.grey_dia order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b, pro_batch_create_mst a where    a.id=b.mst_id and a.entry_form=36  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type)
				union
				(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id  from pro_batch_create_dtls b,pro_batch_create_mst a where    a.id=b.mst_id and  a.entry_form=36 and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,b.grey_dia) order by batch_date";
				}

				// Sample

				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
				}
				else
				{*/
					 $sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $non_booking_cond_for $working_comp_cond $dates_com  $batch_num  $ext_no $floor_num $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c  where a.id=b.mst_id  and c.booking_no=a.booking_no and  a.entry_form=0 and a.batch_against=3 and b.po_id=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and a.status_active=1 and a.is_deleted=0 $comp_cond  $samp_buyercond $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $year_cond $non_booking_cond_for $po_id_cond $booking_num  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.width_dia_type)  order by batch_date";
				//}
			}
		}
		else if($cbo_type==2) //wait for Heat Setting
		{
			if($batch_type==0 || $batch_type==1)
			{
				if($row_h!=0)
				{
					$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $comp_cond  $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
				}
			}
			if($batch_type==0 || $batch_type==2)
			{
				if($row_h!=0)
				{
					$sub_cond="( select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst $dates_com $comp_cond   $batch_num $booking_num  $working_comp_cond  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,null,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond GROUP BY a.id, a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
				}
			}
		}
		else if($cbo_type==3) // Wait For Dyeing
		{
			//$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
			//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
			$find_inset="and  FIND_IN_SET(33,a.process_id)";
			$find_inset_not="and not FIND_IN_SET(33,a.process_id)";
			//else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
			if($batch_type==0 || $batch_type==1)
			{
				$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32  and a.id not in($row_d) $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $comp_cond $working_comp_cond  $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $find_inset GROUP BY  a.id,a.batch_no, b.po_id,b.prod_id,b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no,b.po_id,b.prod_id, b.item_description, a.process_id, a.remarks ) order by batch_date";
			}
			if($batch_type==0 || $batch_type==2) //SubCon Deying
			{
				$sub_cond="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks  from pro_batch_create_mst a,pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d where  a.id=b.mst_id and b.po_id=c.id  and d.subcon_job=c.job_no_mst    and a.id not in($sub_cond_d) $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond and a.entry_form=36 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   GROUP BY a.batch_no, b.po_id,b.prod_id,b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num $booking_num $ext_no $floor_num  GROUP BY a.id, a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no, a.extention_no,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
			}
		}
		else if($cbo_type==4) //Wait For Re-Dyeing
		{
			if($batch_type==0 || $batch_type==1)
			{
				$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
				from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks
				from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and a.batch_against=2 and b.po_id=0 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $batch_num $booking_num $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
			}

			if($batch_type==0 || $batch_type==2) //SubCon Batch
			{

				$sql_subcon="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
				from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num $booking_num $floor_num $ext_no   GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";

			}
		}
		else if($cbo_type==5) //Wait For Singeing
		{
			if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)";
			else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
			if($batch_type==0 || $batch_type==1)
			{
				$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
				if($w_sing_arr!=0)
				{
					$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where   a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond $comp_cond ";
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no,a.floor_id, b.item_description,a.batch_date, a.batch_weight, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type, a.process_id, a.remarks) order by batch_date ";
				}//W-end
				//echo $sql;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		//echo $row_d."=GGGGGGGGG";die;
		/*
		|--------------------------------------------------------------------------
		| Date Wise Report
		|--------------------------------------------------------------------------
		|
		*/
		if($cbo_type==1)
		{
			/*
			|--------------------------------------------------------------------------
			| All Batch
			|--------------------------------------------------------------------------
			|
			*/
			if($batch_type==0)
			{
				if($job_number_id!="" || $txt_order!="")
				{
			  		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,count(b.roll_no) as roll_no,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks order by a.batch_date";
			  		/*echo $sql;die;*/
				}
				else
				{
					//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS
					//echo $po_id_cond ;
					$sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS batch_qnty, b.item_description, listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, b.prod_id, b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $batch_num $booking_num $po_id_cond $ext_no $floor_num $year_cond $comp_cond GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type, a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS batch_qnty, b.item_description, listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, b.prod_id, b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $booking_num $po_id_cond $batch_num $floor_num $ext_no $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}

				//Sub
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
					$sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a
					where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.grey_dia, a.entry_form,b.rec_challan, a.process_id, a.remarks order by a.batch_date ";
				}
				else
				{
					$sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,b.gsm,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.gsm,b.grey_dia,a.entry_form,b.rec_challan,a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,b.gsm,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,b.gsm,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id,b.prod_id,b.width_dia_type,b.grey_dia,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				}

				//Sam

				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="" )
				{
					//echo $order_no;
			 		$sql_sam="SELECT a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $booking_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
				}
				else
				{*/
					//echo $$job_number_id .'aaaa';
				$sql_sam="(SELECT a.id,a.batch_against,a.entry_form, a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $non_booking_cond_for $comp_cond $dates_com  $batch_num $booking_num $po_id_cond  $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no,a.booking_without_order, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c where a.id=b.mst_id  and a.booking_no=c.booking_no and  a.entry_form=0 and a.batch_against=2 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $non_booking_cond_for $samp_buyercond $ref_cond2  $batch_num $booking_num $ext_no $floor_num $floor_num $year_cond GROUP BY a.id,a.batch_against,a.batch_no,a.booking_without_order, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
					//echo $sql_sam;//and b.po_id=0
				//}
				// echo $sql_sam;
			}

			/*
			|--------------------------------------------------------------------------
			| Self Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==1)
			{
				if($job_number_id!="" || $txt_order!="")
				{
					//echo $order_no;
			  		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks order by a.batch_date";
				}
				else
				{
					//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS
					//echo $po_id_cond ;
					$sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $po_id_cond $ext_no $floor_num $year_cond $comp_cond  GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $booking_num  $batch_num $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}
			}

			/*
			|--------------------------------------------------------------------------
			| Subcond Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==2)
			{
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
					$sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, a.entry_form,b.rec_challan, a.process_id, a.remarks order by a.batch_date ";
				}
				else
				{
					$sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";

				}
			// echo $sub_cond;die;
			}

			/*
			|--------------------------------------------------------------------------
			| Sample Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==3)
			{
				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="" )
				{
					//echo $order_no;
			 		$sql_sam="SELECT a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and c.id=b.po_id and c.job_no_mst=d.job_no and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $booking_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
				}
				else
				{*/
					//echo $$job_number_id .'aaaa';
				  	$sql_sam="(SELECT a.id,a.batch_against,a.entry_form, a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $po_id_cond $comp_cond $dates_com  $batch_num $booking_num $po_id_cond  $non_booking_cond_for $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no,a.booking_without_order, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a ,wo_non_ord_samp_booking_mst c where  a.id=b.mst_id and c.booking_no=a.booking_no  and a.entry_form=0 and a.batch_against=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $samp_buyercond  $comp_cond $dates_com  $ref_cond2 $batch_num $booking_num $ext_no $floor_num $year_cond $non_booking_cond_for GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.booking_without_order,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
					//echo $sql_sam;die;//and b.po_id=0
				//}
				// echo $sql_sam;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| wait for Heat Setting
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==2)
		{
			if($batch_type==1)// Self batch
			{
				//echo "dsd";
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				if($w_heat_arr!=0)
				{
					$sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks order by a.batch_date ";
				} echo $sql;

			} //Batch Type End
			if($batch_type==2) //Subcond batch
			{
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				if($w_heat_arr!=0)
				{
					$sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
					$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan, a.process_id, a.remarks order by a.batch_date";

				}
				//echo $sub_cond;
			}//Batch type End

			if($batch_type==0) // Self and Subcond batch
			{
				// Self batch
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				//echo $row_heat.'dd';;
				//if($row_heat)
				//{
					$sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					if($row_heat)
					{
					$p=1;
						foreach($w_heat_arr as $h_batch_id)
						{
							if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
							$p++;
						}
						$sql .=")";
					}

					$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks order by a.batch_date ";
				//}
				// echo $sql;

				//	Subcond batch
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);

					$sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
				if($w_heat_arr!=0)
				{
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
				}

					$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan order by a.batch_date";

				//}
				//echo $sub_cond;
			} // end
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Dyeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==3)
		{
			if($batch_type==1)//Self Batch
			{
				//echo $row_d.'sdd';
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
						$sql .=")";
						$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
						union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date ";
						//echo $sql;die;
				}
			}//Self batch End
			if($batch_type==2) //SubCon Batch
			{

				$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				if($w_dyeing_arr!=0)
				{
					//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,count(b.roll_no) as roll_no,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
					$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";
					$p2=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p2++;
					}
					$sub_cond .=")";
					$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id,b.item_description,b.po_id,b.prod_id,
					b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by a.batch_date ";

				}//echo $sub_cond;
			}
			// echo $sub_cond;//die;
			if($batch_type==0) //Self Batch and SubCon Batch
			{
				// Self
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				//print_r($w_dyeing_arr);
				///echo $row_d.'DSDS';
				//die;
				if($w_dyeing_arr!=0)
				{
					$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					if($row_d>0)
					{
						$p=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p++;
						}
						$sql .=")";
					}


						$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
						union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not
						";

						if($row_d)
						{
							$p1=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p1==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p1++;
						}
						$sql .=")";
						}
						$sql .="  GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date
						";
					//echo $sql;
					/*

					union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not
						";

						if($row_d)
						{
							$p1=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p1==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p1++;
						}
						$sql .=")";
						}
						$sql .="  GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date


					union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) */
						//ISsue id=23236
					//echo $sql;//die;
				}

				// Subcon
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				if($w_dyeing_arr!=0)
				{
					//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
				if($sub_cond_d>0)
					{
						$p=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p++;
						}
						$sub_cond .=")";
					}
					$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";

					if($sub_cond_d)
					{
						$p2=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p2++;
					}
					$sub_cond .=")";
					}
					$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,d.party_id,b.item_description,b.po_id,b.prod_id,
					b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				} //echo $sub_cond;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Re-Dyeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==4)
		{

			if($batch_type==1)//Self Batch
			{
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 { //echo $d_batch_id;die;
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,d.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num ";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 {
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}
			}
			if($batch_type==2) //SubCon Batch
			{
				$w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				// print_r( $w_dyeing_subcon);
				if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d,
					$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $subcon_batch_id)
				 	{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
						$p++;
				 	}
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $booking_num $sub_buyer_cond  $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $d_batch_id)
					{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}

					 //echo $sql;
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				}
			}
			//echo $sql_subcon;

			if($batch_type==0) //Self Batch with SubCon Batch
			{
				//Self Batch
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 { //echo $d_batch_id;die;
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,d.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num ";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 {
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}

				//SubCon Batch
				$w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				// print_r( $w_dyeing_subcon);
				if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d,
					$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $subcon_batch_id)
				 	{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
						$p++;
				 	}
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $booking_num $sub_buyer_cond  $suborder_no $ext_no $floor_num $year_cond $comp_cond";

					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $d_batch_id)
					{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
					$sql_subcon .=")";
					}

					 //echo $sql;

					$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";

				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Singeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==5)
		{
			if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)";
			else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
			$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
			if($w_sing_arr!=0)
			{
				$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond  ";
				if($row_sin>0)
					{
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					}
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num $year_cond ";
				if($row_sin>0)
					{
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					}
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.booking_no_id, a.working_company_id, a.batch_sl_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				//echo $sql;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Date Wise Report
	|--------------------------------------------------------------------------
	|
	*/
	//echo $sql;
	if($cbo_type==1)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
			$sam_batchdata=sql_select($sql_sam);
		}
		else if($batch_type==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
			//print_r($batchdata);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;die;
			$sub_batchdata=sql_select($sub_cond);
			// echo $sub_cond;die;
		}
		else if($batch_type==3)
		{
			//echo $sql_sam;die;
			$sam_batchdata=sql_select($sql_sam);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| wait for Heat Setting
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==2)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			$sub_batchdata=sql_select($sub_cond);
			// echo $sub_cond;die;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Dyeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==3)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Re-Dyeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==4)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sql_subcon);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sql_subcon);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Singeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==5)
	{
		if($batch_type==1)
		{
		$batchdata=sql_select($sql);
		}
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
		}
		/*else if($batch_type==0 || $batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}*/
	}

	/*
	|--------------------------------------------------------------------------
	| for self batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$batch_id_arr = array();$po_id_arr = array();
	foreach ($batchdata as $val)
	{
		$batch_id_arr[$val[csf('id')]]=$val[csf('id')];
		$po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
		//$batch_color_arr[$val[csf('id')]]=$val[csf('color_id')];
	}
	//print_r($batch_color_arr);
	$batchIds = implode(",", $batch_id_arr);

	/*
	|--------------------------------------------------------------------------
	| for subcon batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$subcon_batch_id_arr = array();
	foreach ($sub_batchdata as $val)
	{
		$subcon_batch_id_arr[$val[csf('id')]]=$val[csf('id')];
	}
	$subconBatchIds = implode(",", $subcon_batch_id_arr);

	/*
	|--------------------------------------------------------------------------
	| for sample batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$sample_batch_id_arr = array();
	foreach ($sam_batchdata as $val)
	{
		$sample_batch_id_arr[$val[csf('id')]]=$val[csf('id')];
		$po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
	}
	$sampleBatchIds = implode(",", $sample_batch_id_arr);

	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/
	if($db_type==0)
	{
		if($batchIds!="")
		{
			//$sql_yarn_lot = "SELECT a.id, group_concat(d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  GROUP BY a.id";
			$sql_yarn_lot = "SELECT b.prod_id, b.po_id, group_concat(d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  GROUP BY a.id";
			$sql_yarn_lot_res = sql_select($sql_yarn_lot);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		/*
		|--------------------------------------------------------------------------
		| for self batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($batchIds != '')
		{
			//$sql_yarn_lot = "SELECT a.id,  LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id";
			/*$sql_yarn_lot = "SELECT b.prod_id, b.po_id, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY b.prod_id, b.po_id"; */
			$sql_yarn_lot = "SELECT a.id,b.prod_id, b.po_id, d.yarn_lot AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id,b.prod_id, b.po_id,d.yarn_lot";
			$sql_yarn_lot_res = sql_select($sql_yarn_lot);
		}

		/*
		|--------------------------------------------------------------------------
		| for subcon batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($subconBatchIds != '')
		{
		/* $subconYarnLotSql = "SELECT b.prod_id, b.po_id,d.fabric_description, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_production_dtls d WHERE a.id = b.mst_id AND TO_CHAR(b.po_id) = d.order_id  and b.item_description=d.fabric_description  AND a.company_id = ".$company." AND a.id IN(".$subconBatchIds.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.po_id,d.fabric_description"; */
		 $subconYarnLotSql = "SELECT a.id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_production_dtls d WHERE a.id = b.mst_id AND TO_CHAR(b.po_id) = d.order_id  and b.item_description=d.fabric_description  AND a.company_id = ".$company." AND a.id IN(".$subconBatchIds.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.po_id,a.id";
			$subconYarnLotRslt = sql_select($subconYarnLotSql);
		}

		/*
		|--------------------------------------------------------------------------
		| for sample batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($sampleBatchIds != '')
		{
			/*$sampleYarnLotSql = "SELECT b.prod_id, b.po_id, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = ".$company." AND a.id in(".$sampleBatchIds.") AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY b.prod_id, b.po_id"; */
			$sampleYarnLotSql = "SELECT a.id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = ".$company." AND a.id in(".$sampleBatchIds.") AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id,b.prod_id, b.po_id,d.yarn_lot";
			$sampleYarnLotRslt = sql_select($sampleYarnLotSql);
		}
	}
	 // inv_receive_master e  AND e.entry_form IN (2, 22) AND a.id = 65338

	/*
	|--------------------------------------------------------------------------
	| for self batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$yarn_lot_arr=array();
	foreach($sql_yarn_lot_res as $rows)
	{
		//$yarn_lot_arr[$rows[csf('id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
	}
	/*echo "<pre>";
	print_r($yarn_lot_arr);
	echo "</pre>";*/

	/*
	|--------------------------------------------------------------------------
	| for subcon batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$subcon_yarn_lot_arr=array();
	foreach($subconYarnLotRslt as $rows)
	{
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
	}
	//echo "<pre>";
	//print_r($subcon_yarn_lot_arr);

	/*
	|--------------------------------------------------------------------------
	| for sample batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$sample_yarn_lot_arr=array();
	foreach($sampleYarnLotRslt as $rows)
	{
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
	}
	//echo "<pre>";
	//print_r($sample_yarn_lot_arr);
	$batch_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company."' and module_id=7 and report_id=56 and is_deleted=0 and status_active=1");
    $batch_format_ids=explode(",",$batch_print_report_format);
    $print_btn=$batch_format_ids[0];

	$roll_level= sql_select("select fabric_roll_level from variable_settings_production where company_name='$company' and item_category_id=50 and variable_list=3 and status_active=1 and is_deleted= 0 order by id");

	foreach($roll_level as $row)
	{
		$roll_maintained = $row[csf('fabric_roll_level')];
	}

	if ($roll_maintained == "" || $roll_maintained == 2) $roll_maintained = 0; else $roll_maintained = $roll_maintained;

	$re_dyeing_from = return_library_array("select re_dyeing_from from pro_batch_create_mst where re_dyeing_from <>0 and status_active = 1 and is_deleted = 0","re_dyeing_from","re_dyeing_from");
	$load_unload = return_library_array("select id, batch_id from pro_fab_subprocess where load_unload_id=2 and entry_form=35 and status_active=1","batch_id","batch_id");

	ob_start();
	?>
	<div align="center">
	<fieldset style="width:1375px;">
	<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type]; ?> </strong>
		<br><b>
		<?
		//echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
		echo ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
		?> </b>
 	</div>
 	<div align="center">
  	<?php
	/*
	|--------------------------------------------------------------------------
	| All Batch
	|--------------------------------------------------------------------------
	|
	*/

	if($batch_type==0)
  	{
  		?>
	 	<div align="left"><b>Self Batch</b>
		 	<table class="rpt_table" width="1990" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Against</th>
		                <th width="80">Batch Color</th>
		                <th width="80">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
                    	<th width="100">Pub ship date</th>
		                <th width="70">File No</th>
		                <th width="70">Ref. No</th>
		                <th width="80">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="100">Construction</th>
		                <th width="150">Composition</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
		                <th width="50">Trims Weight</th>
										<th width="50">Total Roll</th>
		                <th width="100">Booking Grey Req.Qty</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
		 	</table>
			<div style=" max-height:350px; width:1990px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
					<?
				$po_cond_in=where_con_using_array($po_id_arr,0,'b.po_break_down_id');

                $booking_qnty_arr=array();
                $queryFab=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $po_cond_in  group by b.po_break_down_id,a.booking_no, b.fabric_color_id");



                foreach($queryFab as $row)
                {
					$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
					//$booking_qnty_arr2[$row[csf('po_break_down_id')]][$row[csf('booking_no')]]+=$row[csf('grey_fab_qnty')];
                }
				unset($queryFab);

                $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($smn_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($sam_query as $row)
                {
                  $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                $i=1;
                $f=0; $bb=0;
                $b=0;
                $btq=0;
                $tot_book_qty=0;  $tot_batch_wgt=0;$tot_trims_wgt=0;$total_tot_batch_wgt=0;$total_tot_trims_wgt=0;
                $tot_grey_req_qty=0;
                $batch_chk_arr=array();
                $booking_chk_arr=array();
                foreach($batchdata as $batch)
                {
                  	// echo $batch[csf('booking_no')].'dd';
			    	$sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
                    //if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
                    if($sam_booking!=$batch[csf('booking_no')])
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $order_id=$batch[csf('po_id')];
                        $color_id=$batch[csf('color_id')];
                        $booking_no=$batch[csf('booking_no')];
                        $booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
                        $desc=explode(",",$batch[csf('item_description')]);
                        $entry_form=$batch[csf('entry_form')];
                        $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
                        $po_num='';
                        $po_file='';
                        $po_ref='';
                        $job_num='';
                        $job_buyers='';
                        $yarn_lot_num='';
                        $grey_booking_qty=0;
                        $buyer_style=''; $ship_DateCond='';
                        $tot_book_qty=0;

                        /*print_r($po_ids);die;*/
                        foreach($po_ids as $p_id)
                        {
                        	//echo $batch[csf('prod_id')]."=".$p_id."<br>";
                        	//echo $yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot']."<br><br>";
                        	if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
							if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
							if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
							if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
							if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
							//$ylot=rtrim($yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'],',');
							$ylot=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
							if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
							$booking_color=$p_id."**".$booking_no."**".$color_id;//$order_id.$booking_no.$color_id;
							if (!in_array($booking_color,$booking_chk_arr))
							{
							    $bb++;
							    $booking_chk_arr[]=$booking_color;
							    $grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];
							}




							//echo $p_id.'='.$booking_no.'='.$color_id.',';
							if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
							$pub_shipment_date=$po_array[$p_id]['pub_shipment_date'];

							if($ship_DateCond=='') $ship_DateCond=$pub_shipment_date;else $ship_DateCond.=",".$pub_shipment_date;
                        }
                        $yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));

						/* $buyer_po=""; $buyer_style="";
						$buyer_po_id=explode(",",$row[csf('po_id')]);*/
						/*foreach($po_ids as $p_id)
						{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
						}
						$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
						$buyer_style=implode(",",array_unique(explode(",",$buyer_style)))*/;//add by samiur

						// $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')])));
						// $book_qty+=$booking_qty;
						$tot_book_qty=$grey_booking_qty;


					 	$batch_id=$batch[csf('id')];
                        if (!in_array($batch_id,$batch_wgt_chk_arr))
                        {
                            $b++;
                            $batch_wgt_chk_arr[]=$batch_id;
                            $tot_batch_wgt=$batch[csf('batch_weight')];
                            $tot_trims_wgt=$batch[csf('total_trims_weight')];
                        }
                        else
                        {
                            $tot_batch_wgt=0;
                            $tot_trims_wgt=0;
                        }

                      	// echo  $batch[csf('id')].'dd';;

                        $process_name = '';
						$process_id_array = explode(",", $batch[csf("process_id")]);
						foreach ($process_id_array as $val)
						{
							if ($process_name == ""){
								$process_name = $conversion_cost_head_array[$val];
							}
							else{
								$process_name .= "," . $conversion_cost_head_array[$val];
							}
						}

                       ?>
                       <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            <?
							if (!in_array($batch[csf('id')],$batch_chk_arr) )
				            {
								if($re_dyeing_from[$batch[csf('id')]])
								{
									$ext_from = $re_dyeing_from[$batch[csf('id')]];
								}else{
									$ext_from = "0";
								}
								if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
								{
									$exists_extention_no = $batch[csf("extention_no")];
									if($exists_extention_no>0)
									{
										$extention_no = $exists_extention_no+1;
									}
									else
									{
										$extention_no = 1;
									}
								}
								else
								{
									if ($batch[csf("extention_no")] == 0)
										$extention_no = '';
									else
										$extention_no = $batch[csf("extention_no")];
								}
	                            $f++;
	                            ?>
	                            <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
	                            <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
								<td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>">
								<a href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[csf('batch_no')]; ?></a>
								<!-- <p><? echo $batch[csf('batch_no')]; ?></p> -->
								</td>
	                            <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
	                             <td style="word-break:break-all;"  width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
	                            <td style="word-break:break-all;" title="<? echo $order_id.'='.$batch[csf('color_id')];?>" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
	                            <td style="word-break:break-all;" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers; ?></div></td>
	                            <?
	                            $batch_chk_arr[]=$batch[csf('id')];
	                           // $book_qty+=$booking_qty;
	                        }
	                        else
	                        {
	                            ?>
	                            <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
	                            <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
	                            <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
	                            <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
	                            <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                            <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
	                            <td style="word-break:break-all;"  width="80"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
	                            <?
	                        }
	                        ?>

	                        <td style="word-break:break-all;" width="80"><p><? echo $buyer_style; ?></p></td>
	                        <td style="word-break:break-all;" width="120"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
	                        <td style="word-break:break-all;" width="100"><p><? echo $ship_DateCond;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
	                        <td style="word-break:break-all;" width="70"><p><? echo $po_file; ?></p></td>
	                        <td style="word-break:break-all;" width="70"><p><?  echo $po_ref; ?></p></td>
	                        <td style="word-break:break-all;" width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                        <td style="word-break:break-all;" width="70"><p><? echo $job_num; ?></p></td>
	                        <td style="word-break:break-all;" width="100"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
	                        <td style="word-break:break-all;" width="150"><div style="width:150px; word-wrap:break-word;"><?

	                        if($desc[4]!="")
	                        {
	                        	$compositions= $desc[1].' ' . $desc[2];
	                        	$gsms= $desc[3];
	                        }
	                        else
	                        {
	                        	$compositions= $desc[1];
	                        	$gsms= $desc[2];
	                        }

	                        echo $compositions;

	                        ?></div></td>
	                        <td style="word-break:break-all;"  width="50"><p><? echo end($desc);//$desc[3]; ?></p></td>
	                        <td style="word-break:break-all;"  width="50"><p><? echo  $gsms;//$desc[2]; ?></p></td>
	                        <td style="word-break:break-all;"  title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
	                        <td style="word-break:break-all;" align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
	                        <td style="word-break:break-all;"  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($tot_batch_wgt,2); ?></td>
	                        <td style="word-break:break-all;"  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($tot_trims_wgt,2); ?></td>
							<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
	                        <td style="word-break:break-all;"  align="right" width="100" title="Booking Color Wise Qty"><? echo number_format($tot_book_qty,2);?></td>
	                        <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')];?></td>
	                        <td style="word-break:break-all;"><? echo $process_name;?></td>
	                    </tr>
                        <?
                        $i++;
                        $btq+=$batch[csf('batch_qnty')];
                        $tot_grey_req_qty+=$tot_book_qty;
					 							$total_tot_batch_wgt+=$tot_batch_wgt;
												$total_tot_trims_wgt+=$tot_trims_wgt;
                        $balance=$tot_grey_req_qty-$btq;
                        $bal_qty=$balance;
                        if($bal_qty>0)
                        {
                        	$color="";
                        	$txt="Over Batch Qty";
                        }
                        else if($bal_qty<0)
                        {
                        	$color="red";
                        	$txt="Below Batch Qty";
                        }
                    }
                }
                ?>
			 		</tbody>
				</table>
				<table class="rpt_table" width="1970"  cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
                             <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="80">&nbsp;</th>

			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" title="<?=$btq?>" id="value_total_btq"><? echo number_format($btq,2); ?></th>
			                <th width="50"  align="right"><? echo number_format($total_tot_batch_wgt,2); ?></th>
			                <th width="50"  align="right"><? echo number_format($total_tot_trims_wgt,2); ?></th>
											<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="10" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="10" align="left">&nbsp; <? echo number_format($btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="10" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
				</table>
			</div> <br/>
		</div>

		<div align="left"> <b>SubCond Batch </b>
		 	<table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Aganist</th>
		                <th width="80">Batch Color</th>
		                <th width="50">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
		                <th width="80">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="80">Recv. Challan</th>
		                <th width="150">Fabrics Desc.</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
						<th width="50">Total Roll</th>
		                <th width="100">Material Recv Grey Req. Qty</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
			</table>
			<div style=" max-height:350px; width:1650px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body2" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tbody>
						<?
						/*$booking_qnty_arr=array();
						$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
						foreach($query as $row)
						{
							$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
						}*/


						$sub_material_recv_arr=array();$sub_material_description_arr=array();
						$subcon_sql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, sum(b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0 group by b.order_id, a.chalan_no,b.color_id,a.trans_type,b.material_description,b.gsm");
						foreach($subcon_sql as $row)
						{
							$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
						}

						//var_dump($sub_material_description_arr);die;
						$i=1;
						$f=0;
						$btq=0; $k=0;
						$book_qty_subcon=0;$subcon_tot_book_qty=$sub_tot_batch_wgt=0;$sub_batch_wgt_chk_arr=array();
						$total_sub_tot_batch_wgt=0;
						$batch_chk_arr=array();$sub_qty_chk_arr=array();
						// print_r($sub_batchdata);
						foreach($sub_batchdata as $batch)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$order_id_sub=$batch[csf('po_id')];
							$color_id=$batch[csf('color_id')];
							$booking_no=$batch[csf('booking_no')];
							$sub_challan=$batch[csf('rec_challan')];
							$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
							$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
							$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
							$desc=explode(",",$batch[csf('item_description')]);
							$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
							$entry_form=$batch[csf('entry_form')];
							$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
							$sub_po_num='';
							$sub_job_buyers='';
							$sub_job_buyers='';$subcon_yarn_lot_num='';
							$subcon_booking_qty=0;
							$sub_buyer_style='';
							foreach($po_ids as $p_id)
							{
								if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
								if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
								if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$sub_po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$sub_po_array[$p_id]['buyer']];
								$subcon_booking_qty+=$sub_material_recv_arr[$p_id];

								//for yarn lot
								//echo $subcon_yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'].', ';
								$subconYlot=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');

								if($sub_buyer_style=="") $sub_buyer_style=$sub_po_array[$p_id]['style_no']; else $sub_buyer_style.=','.$sub_po_array[$p_id]['style_no'];
								//echo $subconYlot.', ';
								/*if($subcon_yarn_lot_num=='')
									$subcon_yarn_lot_num=$subconYlot;
								else
									$subcon_yarn_lot_num.=",".$subconYlot;*/
									  if($subcon_yarn_lot_num=='') $subcon_yarn_lot_num=$subconYlot;else $subcon_yarn_lot_num.=",".$subconYlot;
							}
							$subcon_yarn_lots=implode(",",array_unique(explode(",",$subcon_yarn_lot_num)));
							$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
							if (!in_array($booking_color2,$sub_qty_chk_arr))
							{
								$k++;
								//echo $subcon_booking_qty;
								$sub_qty_chk_arr[]=$booking_color2;
								$subcon_tot_book_qty=$subcon_booking_qty;
							}
							else
							{
								 $subcon_tot_book_qty=0;
							}

							 $batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$sub_batch_wgt_chk_arr))
                            {
                                $k++;
                                $sub_batch_wgt_chk_arr[]=$batch_id;
                                $sub_tot_batch_wgt=$batch[csf('batch_weight')];
                            }
                            else
                            {
                                $sub_tot_batch_wgt=0;
                            }

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}

							//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot'];
							//echo "<pre>". $batch[csf('prod_id')].'='.$order_id_sub;
							//print_r($yarn_lot_arr);
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            	<? if (!in_array($batch[csf('id')],$batch_chk_arr) )
								{
									if($re_dyeing_from[$batch[csf('id')]])
									{
										$ext_from = $re_dyeing_from[$batch[csf('id')]];
									}else{
										$ext_from = "0";
									}
									if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
									{
										$exists_extention_no = $batch[csf("extention_no")];
										if($exists_extention_no>0)
										{
											$extention_no = $exists_extention_no+1;
										}
										else
										{
											$extention_no = 1;
										}
									}
									else
									{
										if ($batch[csf("extention_no")] == 0)
											$extention_no = '';
										else
											$extention_no = $batch[csf("extention_no")];
									}
									$f++;
											?>
					                <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
					                <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
					                <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><a href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[csf('batch_no')]; ?></a></td>
					                <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
					                 <td style="word-break:break-all;" width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
					                <td style="word-break:break-all;" width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;" width="50"><p><? echo $sub_job_buyers; ?></p></td>
									<?
					                $batch_chk_arr[]=$batch[csf('id')];

				                }
								else
								{ ?>
					                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
					                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
					                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
					                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
					                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;"  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
									<?
								}
								?>
								<td style="word-break:break-all;" width="80"><p><? echo $sub_buyer_style; ?></p></td>
				                <td style="word-break:break-all;" width="120"><p><? echo $sub_po_num; ?></p></td>
				                <td style="word-break:break-all;" width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? echo implode(",",array_unique(explode(",",$sub_job_num))); ?></p></td>
				                <td style="word-break:break-all;" width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
				                <td width="150" ><p><? echo $batch[csf('item_description')];//$item_descript; ?></p></td>
				                <td style="word-break:break-all;"  width="50" title="<? echo $desc[2];  ?>"><p><? echo $batch[csf('grey_dia')]; ?></p></td>
				                <td  style="word-break:break-all;" width="50"><p><? echo $batch[csf('gsm')]; ?></p></td>
				                 <td style="word-break:break-all;" align="left" width="60"><p><? echo $subcon_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>
				                <td style="word-break:break-all;" align="right" width="70" ><? echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
				                <td style="word-break:break-all;"  align="right" width="50" title="<? echo $sub_tot_batch_wgt; ?>"><? echo number_format($sub_tot_batch_wgt,2); ?></td>
								<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
				                <td style="word-break:break-all;" align="right" width="100" title="SunCon Material Recv Qty"><? echo number_format($subcon_tot_book_qty,2); ?></td>
				                <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')]; ?></td>
				                <td style="word-break:break-all;"><? echo $process_name; ?></td>
				            </tr>
							<?
			                $i++;
			                $btq_subcon+=$batch[csf('sub_batch_qnty')];
							$book_qty_subcon+=$subcon_tot_book_qty;
							$total_sub_tot_batch_wgt+=$sub_tot_batch_wgt;
			                $balance=$book_qty_subcon-$btq_subcon;
			                $bal_qty_subcon=$balance;
			                if($bal_qty_subcon>0)
			                {
			                $color="";
			                $txt="Over Batch Qty";
			                }
			                else if($bal_qty_subcon<0)
			                {
			                $color="red";
			                $txt="Below Batch Qty";
			                }
			            }
			        	 ?>
			    </tbody>
				</table>
				<table class="rpt_table" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="80">&nbsp;</th>

			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_btq_subcon"><? echo $btq_subcon; ?></th>
			                <th width="50" align="right"><? echo $total_sub_tot_batch_wgt; ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo $book_qty_subcon; ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" title="SunCon Material Recv Qty" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="9" align="left">&nbsp;
			                 <? echo number_format($book_qty_subcon,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="9" align="left">&nbsp; <? echo number_format($btq_subcon,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="9" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
				</table>
			</div>
		</div>

		<div align="left"><b>Sample Batch</b>
			<table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Aganist</th>
		                <th width="80">Batch Color</th>
		                <th width="50">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
		                <th width="70">File No</th>
		                <th width="70">Ref No</th>
		                <th width="100">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="100">Construction</th>
		                <th width="150">Composition</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
		                <th width="50">Trims Weight</th>
										<th width="50">Total Roll</th>
		                <th width="100">Booking Grey Req.Qty</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
			</table>
			<div style=" max-height:350px; width:1880px; overflow-y:scroll;" id="scroll_body">
			 	<table class="rpt_table" id="table_body3" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
						<?
						$sam_booking_qnty_arr=array();
						$sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
						foreach($sam_query as $row)
						{
							$sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
						}

						$smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
						foreach($smn_query as $row)
						{
							$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}
						$sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
						foreach($sam_query as $row)
						{
							$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}

						// GETTING BUYER NAME
						/*$non_order_arr=array();
			            $sql_non_order="SELECT a.company_id,a.grouping as smp_ref, a.buyer_id as buyer_name, b.booking_no, b.bh_qty
			            from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			            where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			            $result_sql_order=sql_select($sql_non_order);
			            foreach($result_sql_order as $row)
			            {

							$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
							$non_order_arr[$row[csf('booking_no')]]['smp_ref']=$row[csf('smp_ref')];
							$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
			            }*/
			            // echo "<pre>";
			            // print_r($non_order_arr);

						$i=1;
						$f=0;
						$b=0;
						$btq=0;$bb=0;
						$tot_book_qty2=0;
						$tot_grey_req_qty=$samp_tot_batch_wgt=$samp_tot_trims_wgt=0;
						$batch_chk_arr=array();
						$booking_chk_arr2=array();$samp_batch_wgt_chk_arr=array();
						// print_r($sam_batchdata );
						foreach($sam_batchdata as $batch)
						{
							$sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
							if($sam_booking==$batch[csf('booking_no')])
							{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
							$po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$sam_booking_qty=0;
							$sample_yarn_lot_num="";$buyer_style='';
							foreach($po_ids as $p_id)
							{
								if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
								if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
								if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
								if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
								if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];

								if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];

								$sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
								//for yarn lot
								$sampleYlot=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
								/*if($sample_yarn_lot_num=='')

									$sample_yarn_lot_num=$sampleYlot;
								else
									$sample_yarn_lot_num.=",".$sampleYlot;*/
									if($sample_yarn_lot_num=='') $sample_yarn_lot_num=$sampleYlot;else $sample_yarn_lot_num.=",".$sampleYlot;
							}

							$sample_yarn_lots=implode(",",array_unique(explode(",",$sample_yarn_lot_num)));
							$order_id=$batch[csf('po_id')];
							$color_id=$batch[csf('color_id')];
							$booking_no=$batch[csf('booking_no')];
							//$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];

							$desc=explode(",",$batch[csf('item_description')]);
							$entry_form=$batch[csf('entry_form')];
							//$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
							//if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
							//if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
							//$po_file=$po_array[$order_id]['file'];
							//$po_ref=$po_array[$order_id]['ref'];
							// $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
							$booking_color=$booking_no;
							if (!in_array($booking_color,$booking_chk_arr2))
							{
								$bb++;
								$booking_chk_arr2[]=$booking_color;
								$tot_book_qty2=$sam_booking_qty;
							}
							else
							{
								$tot_book_qty2=0;
							}
							$batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$samp_batch_wgt_chk_arr))
                            {
                                $b++;
                                $samp_batch_wgt_chk_arr[]=$batch_id;
                                $samp_tot_batch_wgt=$batch[csf('batch_weight')];
                                $samp_tot_trims_wgt=$batch[csf('total_trims_weight')];
                            }
                            else
                            {
                                $samp_tot_batch_wgt=0;
                                $samp_tot_trims_wgt=0;
                            }

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            <? if (!in_array($batch[csf('id')],$booking_chk_arr2))
								{
									if($re_dyeing_from[$batch[csf('id')]])
									{
										$ext_from = $re_dyeing_from[$batch[csf('id')]];
									}else{
										$ext_from = "0";
									}
									if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
									{
										$exists_extention_no = $batch[csf("extention_no")];
										if($exists_extention_no>0)
										{
											$extention_no = $exists_extention_no+1;
										}
										else
										{
											$extention_no = 1;
										}
									}
									else
									{
										if ($batch[csf("extention_no")] == 0)
											$extention_no = '';
										else
											$extention_no = $batch[csf("extention_no")];
									}
									$f++;
									?>
                                    <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
                                    <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                    <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><a href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[csf('batch_no')]; ?></a></td>
                                    <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                    <td style="word-break:break-all;" width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                    <td style="word-break:break-all;" width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                    <td style="word-break:break-all;" width="50"><p><? echo $job_buyers.$buyer_arr[$non_order_arr[$batch[csf('booking_no')]]['buyer_name']]; ?></p></td>
                                    <?
                                    $batch_chk_arr[]=$batch[csf('id')];
				               		// $book_qty+=$booking_qty;
				                  }
								else
								  { ?>
				                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
				                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
				                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
				                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
				                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td style="word-break:break-all;"  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
								<? }
								$samp_ref_no=$non_order_arr[$batch[csf('booking_no')]]['samp_ref_no'];
								$booking_without_order=$batch[csf('booking_without_order')];
								?>
								<td style="word-break:break-all;" width="80"><p><? if($booking_without_order==1) echo $non_order_arr[$batch[csf('booking_no')]]['style_desc'];else echo $buyer_style; ?></p></td>
				                <td style="word-break:break-all;" width="120" ><p><? echo $po_num; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? echo $po_file; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? if($booking_without_order==1) echo $samp_ref_no; else echo $po_ref; ?></p></td>
				                <td style="word-break:break-all;" width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
				                <td style="word-break:break-all;" width="70" title="<? echo $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $job_num; ?></p></td>
				                <td style="word-break:break-all;" width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0]; ?></p></td>
				                <td style="word-break:break-all;" width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
				                <td style="word-break:break-all;" width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
				                <td style="word-break:break-all;" width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
				                 <td style="word-break:break-all;" align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $sample_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
				                <td style="word-break:break-all;" align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
				                <td style="word-break:break-all;" align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($samp_tot_batch_wgt,2); ?></td>
				                <td style="word-break:break-all;" align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($samp_tot_trims_wgt,2); ?></td>
								<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
				                <td title="Booking Color Wise Qty" style="word-break:break-all;" align="right" width="100"><? echo number_format($tot_book_qty2,2);?></td>
				                <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')];?></td>
				                <td style="word-break:break-all;"><? echo $process_name;?></td>
				            </tr>
							<?
				                $i++;
				                $sam_btq+=$batch[csf('batch_qnty')];
								$tot_grey_req_qty+=$tot_book_qty2;
								$total_samp_tot_batch_wgt+=$samp_tot_batch_wgt;
								$total_samp_tot_trims_wgt+=$samp_tot_trims_wgt;
				                $sam_balance=$tot_grey_req_qty-$sam_btq;
				                $sam_bal_qty=$sam_balance;
				                if($sam_bal_qty>0)
				                {
				                $color="";
				                $txt="Over Batch Qty";
				                }
				                else if($sam_bal_qty<0)
				                {
				                $color="red";
				                $txt="Below Batch Qty";
				                }
				                }
			                }
			        	 ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_sam_btq"><? echo number_format($sam_btq,2); ?></th>
			                <th width="50" align="right"><? echo number_format($total_samp_tot_batch_wgt,2); ?></th>
			                <th width="50" align="right"><? echo number_format($total_samp_tot_trims_wgt,2); ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="15" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="15" align="left">&nbsp; <? echo number_format($sam_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="15" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
			 	</table>
			</div><br/>
		</div>
		<?
  	}

	/*
	|--------------------------------------------------------------------------
	| Self Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==1)
	{
		?>
	 	<div align="left"> <b>Self Batch </b></div>
	 	<table class="rpt_table" width="1990" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="75">Batch Date</th>
	                <th width="60">Batch No</th>
	                <th width="40">Ext. No</th>
	                <th width="80">Batch Against</th>
	                <th width="80">Batch Color</th>
	                <th width="80">Buyer</th>
	                <th width="80">Style Ref</th>
	                <th width="120">PO No</th>
                    <th width="100">Pub Ship date</th>
	                <th width="70">File No</th>
	                <th width="70">Ref. No</th>
	                <th width="80">W/O NO.</th>
	                 <th width="70">Job</th>
	                <th width="100">Construction</th>
	                <th width="150">Composition</th>
	                <th width="50">Dia/ Width</th>
	                <th width="50">GSM</th>
	                <th width="60">Lot No</th>
	                <th width="70">Batch Qty.</th>
	                <th width="50">Batch Weight</th>
	                <th width="50">Trims Weight</th>
									<th width="50">Total Roll</th>
	                <th width="100">Grey Req.Qty</th>
	                <th width="100">Remarks</th>
	                <th>Process Name</th>
	            </tr>
	        </thead>
	 	</table>
		<div style=" max-height:350px; width:1990px; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" id="table_body" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
		 		<tbody>
				<?
                $booking_qnty_arr=array();
                $query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
                foreach($query as $row)
                {
                    $booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
                }

                $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($smn_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($sam_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                $i=1;
                $f=0;
                $b=0;
                $btq=0;
                $tot_book_qty=0;
                $tot_grey_req_qty=0;
                $batch_chk_arr=array();$booking_chk_arr=array();
                foreach($batchdata as $batch)
                {
                    $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
                    //if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
                    if($sam_booking!=$batch[csf('booking_no')])
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $order_id=$batch[csf('po_id')];
                            $order_id_ex = array_unique(explode(",", $order_id));
                            $order_id = implode(",", $order_id_ex);
                            $color_id=$batch[csf('color_id')];
                            $booking_no=$batch[csf('booking_no')];
                            $booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
                            $desc=explode(",",$batch[csf('item_description')]);
                            $entry_form=$batch[csf('entry_form')];
                            $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
                            $po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$yarn_lot_num='';
                            $grey_booking_qty=0;$buyer_style='';$ship_DateCond='';
                            foreach($po_ids as $p_id)
                            {
                                if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
                                if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
                                if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
                                if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
                                if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
                                $ylot=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
                                if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
                                $booking_color=$order_id.$booking_no.$color_id;
		                            if (!in_array($booking_color,$booking_chk_arr))
		                            {
		                              $b++;
		                              $grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];
		                              $booking_chk_arr[]=$booking_color;
		                            }

                                if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
																$pub_shipment_date=$po_array[$p_id]['pub_shipment_date'];
																if($ship_DateCond=='') $ship_DateCond=$pub_shipment_date;else $ship_DateCond.=",".$pub_shipment_date;
                            }
                            $yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));
                            // $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                            // $book_qty+=$booking_qty;

                           $tot_book_qty=$grey_booking_qty;

                           //echo  $book_qty;


                            $process_name = '';
													$process_id_array = explode(",", $batch[csf("process_id")]);
													foreach ($process_id_array as $val)
													{
														if ($process_name == ""){
															$process_name = $conversion_cost_head_array[$val];
														}
														else{
															$process_name .= "," . $conversion_cost_head_array[$val];
														}
													}
                           ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                            <? if (!in_array($batch[csf('id')],$batch_chk_arr) )
                            {
																if($re_dyeing_from[$batch[csf('id')]])
																{
																	$ext_from = $re_dyeing_from[$batch[csf('id')]];
																}else{
																	$ext_from = "0";
																}
																if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
																{
																	$exists_extention_no = $batch[csf("extention_no")];
																	if($exists_extention_no>0)
																	{
																		$extention_no = $exists_extention_no+1;
																	}
																	else
																	{
																		$extention_no = 1;
																	}
																}
																else
																{
																	if ($batch[csf("extention_no")] == 0)
																		$extention_no = '';
																	else
																		$extention_no = $batch[csf("extention_no")];
																}
                                $f++;
                                ?>
                                <td width="30" style="word-break:break-all;"><? echo $f; ?></td>
                                <td align="center" width="75" style="word-break:break-all;" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                <td width="60" style="word-break:break-all;" title="<? echo $batch[csf('batch_no')]; ?>"><a href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[csf('batch_no')]; ?></a></td>
                                <td  width="40" style="word-break:break-all;" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                 <td  width="80" style="word-break:break-all;"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                                <td width="80" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers;//; ?></div></td>
                                <?
                                $batch_chk_arr[]=$batch[csf('id')];
                               // $book_qty+=$booking_qty;
                            }
                            else
                            {
                                ?>
                                <td width="30" style="word-break:break-all;"><? //echo $sl; ?></td>
                                <td width="75" style="word-break:break-all;"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
                                <td width="60" style="word-break:break-all;"><p><? //echo $booking_qty; ?></p></td>
                                <td width="40" style="word-break:break-all;"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                <?
                            }
                            ?>
                            <td width="80" style="word-break:break-all;"><p><? echo $buyer_style; ?></p></td>
                            <td width="120" style="word-break:break-all;"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $ship_DateCond; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo $po_file; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><?  echo $po_ref; ?></p></td>
                            <td width="80" style="word-break:break-all;"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo $job_num; ?></p></td>
                            <td width="100" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
                            <td width="150" style="word-break:break-all;"><div style="width:150px; word-wrap:break-word;"><?
                            if($desc[4]!="")
                            {
                            	$compositions= $desc[1].' ' . $desc[2];
                            	$gsms= $desc[3];
                            }
                            else
                            {
                            	$compositions= $desc[1];
                            	$gsms= $desc[2];
                            }
                            echo $compositions;

                            ?></div></td>
                            <td width="50" style="word-break:break-all;"><p><? echo end($desc);//$desc[3]; ?></p></td>
                            <td width="50" style="word-break:break-all;"><p><? echo $gsms;//$desc[2]; ?></p></td>
                            <td style="word-break:break-all;" title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
                            <td align="right" width="70" style="word-break:break-all;" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                            <td align="right" width="50" style="word-break:break-all;" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
                            <td align="right" width="50" style="word-break:break-all;" title="<? echo $batch[csf('total_trims_weight')]; ?>"><? echo number_format($batch[csf('total_trims_weight')],2); ?></td>
													<td width="50" align="right" style="word-break:break-all;"><? echo $batch[csf('roll_no')];?></td>
                            <td align="right" width="100" style="word-break:break-all;"><? echo number_format($tot_book_qty,2);?></td>
                            <td width="100" style="word-break:break-all;"><? echo $batch[csf('')];?></td>
                            <td style="word-break:break-all;"><? echo $process_name;?></td>
                            </tr>
                            <?
                            $i++;
                            $btq+=$batch[csf('batch_qnty')];
                            $tot_grey_req_qty+=$tot_book_qty;
                            $balance=$tot_grey_req_qty-$btq;
                            $bal_qty=$balance;
                            if($bal_qty>0)
                            {
                                $color="";
                                $txt="Over Batch Qty";
                            }
                            else if($bal_qty<0)
                            {
                                $color="red";
                                $txt="Below Batch Qty";
                            }
                        }
                    }
                 ?>
		 		</tbody>
			</table>
			<table class="rpt_table" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <tr>
		                <th width="30">&nbsp;</th>
		                <th width="75">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="40">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>

		                <th width="70">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="150">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="70" id="value_total_btq"><? echo number_format($btq,2); ?></th>
		                <th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
		                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
		                <th width="100">&nbsp;</th>
		                <th>&nbsp;</th>
		            </tr>
		            <tr>
		                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
		                <td colspan="12" align="left">&nbsp;
		                 <? echo number_format($tot_grey_req_qty,2); ?>
		                </td>
		            </tr>
		             <tr>
		                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
		                <td colspan="12" align="left">&nbsp; <? echo number_format($btq,2); ?> </td>
		            </tr>
		             <tr>
		                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
		                <td colspan="12" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
		            </tr>
		        </tfoot>
			</table>
		</div><br/>
		<?
	}
	/*
	|--------------------------------------------------------------------------
	| Subcon Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==2)
	{
		?>
		<div align="left"> <b>SubCond Batch</b></div>
	 	<table class="rpt_table" width="1650" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="75">Batch Date</th>
	                <th width="60">Batch No</th>
	                <th width="40">Ext. No</th>
	                <th width="80">Batch Aganist</th>
	                <th width="80">Batch Color</th>
	                <th width="50">Buyer</th>
	                <th width="80">Style Ref</th>
	                <th width="120">PO No</th>
	                <th width="80">W/O NO.</th>
	                <th width="70">Job</th>
	                <th width="80">Recv. Challan</th>
	                <th width="150">Fabrics Desc.</th>
	                <th width="50">Dia/ Width</th>
	                <th width="50">GSM</th>
	                <th width="60">Lot No</th>
	                <th width="70">Batch Qty.</th>
	                <th width="50">Batch Weight</th>
					<th width="50">Total Roll</th>
	                <th width="100">Grey Req. Qty</th>
	                <th width="100">Remarks</th>
	                <th>Process Name</th>
	            </tr>
	        </thead>
		</table>
		<div style=" max-height:350px; width:1650px; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" id="table_body2" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tbody>
					<?
					/*$booking_qnty_arr=array();
					$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
					foreach($query as $row)
					{
						$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
					}*/


					$sub_material_recv_arr=array();$sub_material_description_arr=array();
					$subconsql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, sum(b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0 group by b.order_id, a.chalan_no,b.color_id,a.trans_type,b.material_description,b.gsm");
					foreach($subconsql as $row)
					{
						$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
					}

					//var_dump($sub_material_description_arr);die;
					$i=1;
					$f=0;
					$btq=0; $k=0;
					$book_qty_subcon=0;$subcon_tot_book_qty=0;
					$batch_chk_arr=array();$sub_qty_chk_arr=array();
					 //print_r($sub_batchdata);
					foreach($sub_batchdata as $batch)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_id_sub=$batch[csf('po_id')];
						$color_id=$batch[csf('color_id')];
						$booking_no=$batch[csf('booking_no')];
						$sub_challan=$batch[csf('rec_challan')];
						$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
						$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
						$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
						$desc=explode(",",$batch[csf('item_description')]);
						$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));

						$entry_form=$batch[csf('entry_form')];

						$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
						$sub_po_num='';	$sub_job_buyers='';$sub_job_buyers='';
						$subcon_booking_qty=0;$sub_buyer_style='';
						foreach($po_ids as $p_id)
						{
							if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
							if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
							if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$sub_po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$sub_po_array[$p_id]['buyer']];
							$subcon_booking_qty+=$sub_material_recv_arr[$p_id];
							//for yarn lot
							$subconYlot=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
							if($subcon_yarn_lot_num=='')
								$subcon_yarn_lot_num=$subconYlot;
							else
								$subcon_yarn_lot_num.=",".$subconYlot;
							if($sub_buyer_style=="") $sub_buyer_style=$sub_po_array[$p_id]['style_no']; else $sub_buyer_style.=','.$sub_po_array[$p_id]['style_no'];
						}

						$subcon_yarn_lots=implode(",",array_unique(explode(",",$subcon_yarn_lot_num)));
						$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
						if (!in_array($booking_color2,$sub_qty_chk_arr))
						{ $k++;

							//echo $subcon_booking_qty;
							 $sub_qty_chk_arr[]=$booking_color2;
							  $subcon_tot_book_qty=$subcon_booking_qty;
						}
						else
						{
							 $subcon_tot_book_qty=0;
						}

						$process_name = '';
						$process_id_array = explode(",", $batch[csf("process_id")]);
						foreach ($process_id_array as $val)
						{
							if ($process_name == ""){
								$process_name = $conversion_cost_head_array[$val];
							}
							else{
								$process_name .= "," . $conversion_cost_head_array[$val];
							}
						}
						?>
			            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
			            	<? if (!in_array($batch[csf('id')],$batch_chk_arr) )
							{
								if($re_dyeing_from[$batch[csf('id')]])
								{
									$ext_from = $re_dyeing_from[$batch[csf('id')]];
								}else{
									$ext_from = "0";
								}
								if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
								{
									$exists_extention_no = $batch[csf("extention_no")];
									if($exists_extention_no>0)
									{
										$extention_no = $exists_extention_no+1;
									}
									else
									{
										$extention_no = 1;
									}
								}
								else
								{
									if ($batch[csf("extention_no")] == 0)
										$extention_no = '';
									else
										$extention_no = $batch[csf("extention_no")];
								}
								$f++;
										?>
				                <td width="30"><? echo $f; ?></td>
				                <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
				                <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><a href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[csf('batch_no')]; ?></a></td>
				                <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
				                 <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
				                <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td width="50"><p><? echo $sub_job_buyers; ?></p></td>
								<?
				                $batch_chk_arr[]=$batch[csf('id')];

			                }
							else
							{ ?>
				                <td width="30"><? //echo $sl; ?></td>
				                <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
				                <td   width="60"><p><? //echo $booking_qty; ?></p></td>
				                <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
				                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
								<?
							}
							?>
							<td width="80"><p><? echo $sub_buyer_style; ?></p></td>
			                <td width="120"><p><? echo $sub_po_num; ?></p></td>
			                <td width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
			                <td width="70"><p><? echo $sub_job_num; ?></p></td>
			                <td width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
			                <td width="150" ><p><? echo $item_descript; ?></p></td>
			                <td  width="50" title="<? echo $desc[2];  ?>"><p><? echo $desc[3]; ?></p></td>
			                <td  width="50"><p><? echo $gsm_subcon; ?></p></td>
			                <td align="left" width="60"><p><? echo $subcon_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>
			                <td align="right" width="70" ><? echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
			                <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
							<td width="50" align="right"><? echo $batch[csf('roll_no')]; ?></td>
			                <td width="100"><? echo number_format($subcon_tot_book_qty,2); ?></td>
			                <td width="100"><? echo $batch[csf('remarks')]; ?></td>
			                <td><? echo $process_name; ?></td>
			            </tr>
						<?
		                $i++;
		                $btq_subcon+=$batch[csf('sub_batch_qnty')];
						$book_qty_subcon+=$subcon_tot_book_qty;
		                $balance=$book_qty_subcon-$btq_subcon;
		                $bal_qty_subcon=$balance;
		                if($bal_qty_subcon>0)
		                {
		                $color="";
		                $txt="Over Batch Qty";
		                }
		                else if($bal_qty_subcon<0)
		                {
		                $color="red";
		                $txt="Below Batch Qty";
		                }
		            }
		        	 ?>
		       </tbody>
			</table>
			<table class="rpt_table" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <tr>
		                <th width="30">&nbsp;</th>
		                <th width="75">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="40">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="120">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>

		                <th width="150">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="70" id="value_total_btq_subcon"><? echo $btq_subcon; ?></th>
		                <th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
		                <th width="100"><? //echo $book_qty_subcon; ?></th>
		                <th width="100">&nbsp;</th>
		                <th>&nbsp;</th>
		            </tr>
		            <tr>
		                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
		                <td colspan="9" align="left">&nbsp;
		                 <? echo number_format($book_qty_subcon,2); ?>
		                </td>
		            </tr>
		             <tr>
		                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
		                <td colspan="9" align="left">&nbsp; <? echo number_format($btq_subcon,2); ?> </td>
		            </tr>
		             <tr>
		                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
		                <td colspan="9" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
		            </tr>
		        </tfoot>
			</table>
		</div>
		<?
	}

	/*
	|--------------------------------------------------------------------------
	| Sample Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==3)
	{
		if($cbo_type==1)
		{
			?>
			<div align="left"> <b>Sample Batch </b></div>
			<table class="rpt_table" id="table_header_1" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="75">Batch Date</th>
                        <th width="60">Batch No</th>
                        <th width="40">Ext. No</th>
                        <th width="80">Batch Aganist</th>
                        <th width="80">Batch Color</th>
                        <th width="50">Buyer</th>
                        <th width="80">Style Ref</th>
                        <th width="120">PO No</th>
                        <th width="70">File No</th>
                        <th width="70">Ref No</th>
                        <th width="100">W/O NO.</th>
                        <th width="70">Job</th>
                        <th width="100">Construction</th>
                        <th width="150">Composition</th>
                        <th width="50">Dia/ Width</th>
                        <th width="50">GSM</th>
                        <th width="60">Lot No</th>
                        <th width="70">Batch Qty.</th>
                        <th width="50">Batch Weight</th>
                        <th width="50">Trims Weight</th>
												<th width="50">Total Roll</th>
                        <th width="100">Grey Req.Qty</th>
                        <th width="100">Remarks</th>
                        <th>Process Name</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1880px; overflow-y:scroll;" id="scroll_body">
			 	<table class="rpt_table" id="table_body3" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
					<?
                    $sam_booking_qnty_arr=array();
                    $sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
                    }

                    $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($smn_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }
                    $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }


                    $i=1;
                    $f=0;
                    $b=0;
                    $btq=0;
                    $tot_book_qty2=0;
                    $tot_grey_req_qty=0;
                    $batch_chk_arr=array();$booking_chk_arr2=array();
                    // print_r($sam_batchdata );
                    foreach($sam_batchdata as $batch)
                    {
                        $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];

                        if($sam_booking==$batch[csf('booking_no')])
                        {
	                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
	                        $po_num='';	$po_file='';$po_ref='';$samp_job_num='';$job_buyers='';$sam_booking_qty=0;$buyer_style='';
	                        foreach($po_ids as $p_id)
	                        {
	                            if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
	                            if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
	                            if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
	                            if($samp_job_num=='') $samp_job_num=$po_array[$p_id]['job_no'];else $samp_job_num.=",".$po_array[$p_id]['job_no'];
	                            if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
	                            $sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
	                            //for yarn lot
	                            $sampleYlot=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
	                            if($sample_yarn_lot_num=='')
	                                $sample_yarn_lot_num=$sampleYlot;
	                            else
	                                $sample_yarn_lot_num.=",".$sampleYlot;
	                            if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
	                        }

	                        $sample_yarn_lots=implode(",",array_unique(explode(",",$sample_yarn_lot_num)));
	                        $order_id=$batch[csf('po_id')];
	                        $color_id=$batch[csf('color_id')];
	                        $booking_no=$batch[csf('booking_no')];
	                        //$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];

	                        $desc=explode(",",$batch[csf('item_description')]);
	                        $entry_form=$batch[csf('entry_form')];
	                        //$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
	                        //if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
	                        //if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
	                        //$po_file=$po_array[$order_id]['file'];
	                        //$po_ref=$po_array[$order_id]['ref'];
	                        // $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
	                        $booking_color=$order_id.$booking_no.$color_id;
	                        if (!in_array($booking_color,$booking_chk_arr2))
	                        {
	                            $b++;
	                            $booking_chk_arr2[]=$booking_color;
	                            $tot_book_qty2=$sam_booking_qty;
	                        }
	                        else
	                        {
	                            $tot_book_qty2=0;
	                        }
	                        //echo  $batch[csf('po_id')].', ';

	                        $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
		                        <?
		                        if (!in_array($batch[csf('id')],$booking_chk_arr2))
		                        {
									if($re_dyeing_from[$batch[csf('id')]])
									{
										$ext_from = $re_dyeing_from[$batch[csf('id')]];
									}else{
										$ext_from = "0";
									}
									if($batch[csf('batch_against')]==2 && $load_unload[$batch[csf('id')]]!="" && $ext_from==0)
									{
										$exists_extention_no = $batch[csf("extention_no")];
										if($exists_extention_no>0)
										{
											$extention_no = $exists_extention_no+1;
										}
										else
										{
											$extention_no = 1;
										}
									}
									else
									{
										if ($batch[csf("extention_no")] == 0)
											$extention_no = '';
										else
											$extention_no = $batch[csf("extention_no")];
									}
		                            $f++;
		                            ?>
		                            <td width="30"><? echo $f; ?></td>
		                            <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
		                            <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><a href="##" onClick="generate_batch_print_report('<? echo $print_btn;?>','<? echo $company;?>','<? echo $batch[csf('id')]?>','<? echo $batch[csf('batch_no')]?>','<? echo $batch[csf('working_company_id')]?>','<? echo $extention_no;?>','<? echo $batch[csf('batch_sl_no')]?>','<? echo $batch[csf('booking_no_id')]?>','<? echo $roll_maintained;?>')"><?echo $batch[csf('batch_no')]; ?></a></td>
		                            <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
		                            <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
		                            <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td width="50"><p><? echo $buyer_arr[$non_order_arr[$batch[csf('booking_no')]]['buyer_name']]; ?></p></td>
		                            <?
		                            $batch_chk_arr[]=$batch[csf('id')];
		                            // $book_qty+=$booking_qty;
		                        }
	                            else
	                            { ?>
		                            <td width="30"><? //echo $sl; ?></td>
		                            <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
		                            <td   width="60"><p><? //echo $booking_qty; ?></p></td>
		                            <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
		                            <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
		                            <?
		                        }
								$samp_ref_no= $non_order_arr[$batch[csf('booking_no')]]['samp_ref_no'];
								$booking_without_order=$batch[csf('booking_without_order')];
	                            ?>
	                            <td width="80" title="<? echo $booking_without_order; ?>"><p><? if($booking_without_order==1) echo $style_ref_no_lib[$non_order_arr[$batch[csf('booking_no')]]['style_id']]; else echo $buyer_style; ?></p></td>
	                            <td width="120" ><p><? if($batch[csf('po_id')]>1) echo $po_num;else echo ""; ?></p></td>
	                            <td width="70"><p><? echo $po_file; ?></p></td>
	                             <td width="70"><p><? if($booking_without_order==1) echo $samp_ref_no;else echo $po_ref; ?></p></td>
	                            <td width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                            <td width="70" title="poID=<? echo $batch[csf('po_id')]; ?>"><p><? if($batch[csf('po_id')]>1) echo $samp_job_num;else echo ""; ?></p></td>
	                            <td width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0];//$batch[csf('grey_dia')];; ?></p></td>
	                            <td width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
	                            <td  width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
	                            <td  width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
	                             <td align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $sample_yarn_lots; //$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
	                            <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
	                            <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
	                            <td  align="right" width="50" title="<? echo $batch[csf('total_trims_weight')]; ?>"><? echo number_format($batch[csf('total_trims_weight')],2); ?></td>
								<td width="50" align="right"><? echo $batch[csf('roll_no')];?></td>
	                            <td align="right" width="100"><? echo number_format($tot_book_qty2,2);?></td>
	                            <td width="100"><? echo $batch[csf('remarks')];?></td>
	                            <td><p><? echo $process_name;?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                        $sam_btq+=$batch[csf('batch_qnty')];
	                        $tot_grey_req_qty+=$tot_book_qty2;
	                        $sam_balance=$tot_grey_req_qty-$sam_btq;
	                        $sam_bal_qty=$sam_balance;
	                        if($sam_bal_qty>0)
	                        {
	                        	$color="";
	                        	$txt="Over Batch Qty";
	                        }
	                        else if($sam_bal_qty<0)
	                        {
		                        $color="red";
		                        $txt="Below Batch Qty";
	                        }
                        }
                    }
                    ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_sam_btq"><? echo number_format($sam_btq,2); ?></th>
			                <th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="10" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="10" align="left">&nbsp; <? echo number_format($sam_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="10" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
			 	</table>
			</div><br/>
		 	<?
		}
  	}
   	?>
	</div>
	</fieldset>
	</div>
	<?

	/*$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$total_data####$filename####$batch_type"; */

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
	echo "$total_data####$filename####$batch_type";

	disconnect($con);
	exit();
}	//BatchReport
if($action=="batch_report_show2__old")
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
	$floor_no = str_replace("'","",$cbo_floor);
	//echo $cbo_batch_type;die;
	/*echo $floor_no;die;*/
	$batch_number_hidden = str_replace("'","",$batch_number);
	$booking_no = str_replace("'","",$txt_booking_no);
	$booking_number_hidden = str_replace("'","",$txt_hide_booking_id);

	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	$year = str_replace("'","",$cbo_year);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	//echo $order_no;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$process=33;
	//echo str_replace("'","",$batch_no);die;

	if ($buyer==0) $buyerdata=""; else $buyerdata="  and d.buyer_name='$buyer'";
	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_name='$buyer'";
	if ($batch_no=="") $batch_num=""; else $batch_num="  and a.batch_no='".str_replace("'","",$batch_no)."'";
	if ($booking_no=="") $booking_num=""; else $booking_num="  and a.booking_no like '%".str_replace("'","",$booking_no)."%'";
	if ($floor_no==0) $floor_num=""; else $floor_num="  and a.floor_id='".$floor_no."'";
	if ($file_no=="") $file_cond=""; else $file_cond="  and b.file_no='".$file_no."'";

	if ($batch_no=="") $batch_num2=""; else $batch_num2="  and batch_no='".str_replace("'","",$batch_no)."'";
	if ($ref_no=="")
	{
		$ref_cond="";
		$ref_cond2="";

	}
	else
	{
	$ref_cond="  and b.grouping='$ref_no'";
	$ref_cond2="  and c.grouping='$ref_no'";
	}
	if ($company==0) $comp_cond=""; else $comp_cond=" and a.company_id=$company";
	if ($working_company==0) $working_comp_cond=""; else $working_comp_cond=" and a.working_company_id=$working_company";
	//a.company_id=$company

	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if($batch_type==0 || $batch_type==1 ||  $batch_type==3)
	{
		if ($txt_order=="") $order_no_cond=""; else $order_no_cond="  and b.po_number='$txt_order'";
		if ($txt_order=="") $order_no2=""; else $order_no2="  and c.po_number='$txt_order'";
		if ($job_number_id=="") $jobdata=""; else $jobdata="  and a.job_no_prefix_num in($job_number_id)";
		if ($job_number_id=="") $jobdata3=""; else $jobdata3="  and d.job_no_prefix_num in($job_number_id)";
	}
	//echo $jobdata;
	if($batch_type==0 || $batch_type==2)
	{
		if ($txt_order!='') $suborder_no="and c.order_no='$txt_order'"; else $suborder_no="";
		if ($job_number_id!='') $sub_job_cond="  and d.job_no_prefix_num in(".$job_number_id.") "; else $sub_job_cond="";
		if ($buyer==0) $sub_buyer_cond=""; else $sub_buyer_cond="  and d.party_id='".$buyer."' ";
	}
	if ($buyer==0) $samp_buyercond=""; else $samp_buyercond=" and c.buyer_id=".$buyer." ";

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
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}

	$po_array=array();
	$po_sql=sql_select("select a.job_no_prefix_num 	as job_no,a.buyer_name,a.style_ref_no, b.file_no,b.grouping as ref,b.id, b.po_number,b.pub_shipment_date,c.mst_id as batch_id,d.is_sales,d.sales_order_id from wo_po_details_master a, wo_po_break_down b,pro_batch_create_dtls c,pro_batch_create_mst d where a.id=b.job_id and c.po_id=b.id and d.id=c.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0  and c.status_active in (1) and c.is_deleted=0 and d.status_active in (1) and d.is_deleted=0 $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2");



	$poid='';
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$po_array[$row[csf('id')]]['style_no']=$row[csf('style_ref_no')];
		$po_array[$row[csf('id')]]['ref']=$row[csf('ref')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];

		$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$po_array[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
		if($row[csf('is_sales')]==1)
		{
			$sales_id_array[$row[csf('sales_order_id')]]=$row[csf('sales_order_id')];
		}
		$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		if($poid=='') $poid=$row[csf('id')]; else $poid.=",".$row[csf('id')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
	}
	//echo 'DD';;die;
	$batch_cond_for_in=where_con_using_array($batch_id_arr,0,"a.id");
	$sales_cond_for_in=where_con_using_array($sales_id_array,0,"a.id");
	//echo $poid.'SSSSSSSSSSSS';;die;
	//for sales order entry
	if ($buyer==0) $buyer_cond2=""; else $buyer_cond2="  and b.buyer_id='$buyer'";
	$sales_po_array=array();
	$sales_po_sql=sql_select("select  a.job_no as po_number,a.buyer_id as buyer_name,a.style_ref_no,a.id from fabric_sales_order_mst a, pro_batch_create_mst b where a.id=b.sales_order_id and b.is_sales=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond2 $year_cond $sales_cond_for_in ");
	$sales_poid='';
	foreach($sales_po_sql as $row)
	{
		$sales_po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$sales_po_array[$row[csf('id')]]['style_no']=$row[csf('style_ref_no')];
		$sales_po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		if($sales_poid=='') $sales_poid=$row[csf('id')]; else $sales_poid.=",".$row[csf('id')];
	} //echo $sales_poid;die;



	$sub_po_array=array();
	$sub_po_sql=sql_select("select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d  where d.subcon_job=c.job_no_mst  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond");
	/*echo "select d.job_no_prefix_num 	as job_no,d.party_id,c.cust_buyer, c.id, c.order_no from subcon_ord_dtls c, subcon_ord_mst d  where d.subcon_job=c.job_no_mst  and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sub_job_cond $suborder_no $sub_buyer_cond $ref_cond $file_cond";die;*/
	$sub_poid='';
	foreach($sub_po_sql as $row)
	{
		$sub_po_array[$row[csf('id')]]['po_no']=$row[csf('order_no')];
		$sub_po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$sub_po_array[$row[csf('id')]]['buyer']=$row[csf('party_id')];
		if($sub_poid=='') $sub_poid=$row[csf('id')]; else $sub_poid.=",".$row[csf('id')];
	}
	 // GETTING BUYER NAME
	$non_order_arr=array();
	$sql_non_order="SELECT c.id,c.company_id,c.grouping as samp_ref_no,c.style_desc, c.buyer_id as buyer_name, b.booking_no, b.bh_qty, b.style_id
	from wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls b
	where c.booking_no=b.booking_no and c.booking_type=4 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 $ref_cond2";
	// echo $sql_non_order;
	$result_sql_order=sql_select($sql_non_order);
	foreach($result_sql_order as $row)
	{

		$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
		$non_order_arr[$row[csf('booking_no')]]['samp_ref_no']=$row[csf('samp_ref_no')];
		$non_order_arr[$row[csf('booking_no')]]['style_desc']=$row[csf('style_desc')];
		$non_order_arr[$row[csf('booking_no')]]['style_id']=$row[csf('style_id')];
		$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
		$non_booking_id_r_arr[$row[csf('id')]]=$row[csf('id')];
	}
	// echo "<pre>";
	// print_r($non_order_arr);
	$style_ref_no_lib=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	if($ref_no!="")
	{
		$booking_ids=count($non_booking_id_r_arr);
		if($db_type==2 && $booking_ids>1000)
		{
			$non_booking_cond_for=" and (";
			$bookIdsArr=array_chunk($non_booking_id_r_arr,999);
			foreach($bookIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$non_booking_cond_for.=" a.booking_no_id in($ids) or";
			}
			$non_booking_cond_for=chop($non_booking_cond_for,'or ');
			$non_booking_cond_for.=")";
		}
		else
		{
			$non_booking_cond_for=" and a.booking_no_id in(".implode(",",$non_booking_id_r_arr).")";
		}
	}
	//echo $non_booking_cond_for.'DDDDDDDDD';die;
	//if($sub_poid=="") $sub_poid=0;else $sub_poid=$sub_poid;
	//echo $sub_poid.'gfgf';
	$po_id="";
	if($txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
	{
		$po_id=$poid;
	}
	//echo $po_id;
	$sub_po_id="";
	if($txt_order!="" || $job_number_id!=""  || $year!=0)
	{
		$sub_po_id=$sub_poid;
	}

	$po_id_cond="";
	if($po_id!="")
	{
		//echo $po_id=substr($po_id,0,-1);
		$po_id=chop($po_id,',');
		if($db_type==0) $po_id_cond="and b.po_id in(".$po_id.")";
		else
		{
			$po_ids=array_unique(explode(",",$po_id));
			if(count($po_ids)>990)
			{
				$po_id_cond="and (";
				$po_ids=array_chunk($po_ids,990);
				$z=0;
				foreach($po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $po_id_cond.=" b.po_id in(".$id.")";
					else $po_id_cond.=" or b.po_id in(".$id.")";
					$z++;
				}
				$po_id_cond.=")";
			}
			else{
			$po_id=implode(",",array_unique(explode(",",$po_id)));
			$po_id_cond="and b.po_id in(".$po_id.")";}
		}
	}
	//echo $po_id_cond;die;
	$sub_po_id_cond="";
	if($sub_po_id!="")
	{
		//$sub_po_id=substr($sub_po_id,0,-1);
		$sub_po_id=chop($sub_po_id,',');
		if($db_type==0) $sub_po_id_cond="and b.po_id in(".$sub_po_id.")";
		else
		{
			$sub_po_ids=array_unique(explode(",",$sub_po_id));
			if(count($sub_po_ids)>990)
			{
				$sub_po_id_cond="and (";
				$sub_po_ids=array_chunk($sub_po_ids,990);
				$z=0;
				foreach($sub_po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $sub_po_id_cond.=" b.po_id in(".$id.")";
					else $sub_po_id_cond.=" or b.po_id in(".$id.")";
					$z++;
				}
				$sub_po_id_cond.=")";
			}
			else {
			$sub_po_id=implode(",",array_unique(explode(",",$sub_po_id)));
			$sub_po_id_cond="and b.po_id in(".$sub_po_id.")";}
		}
	}
	//echo  $sub_po_id_cond;
	//echo $po_id.'aaas';

	$sql_dyeing_subcon=sql_select("select c.batch_id,c.entry_form from  pro_fab_subprocess c,pro_batch_create_mst a where a.id=c.batch_id and c.entry_form in(38,35,32,47,31,48) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.batch_id>0 $batch_cond_for_in $batch_num2 $dates_com $batch_num ");
	//echo "select c.batch_id,c.entry_form from  pro_fab_subprocess c,pro_batch_create_mst a where a.id=c.batch_id and c.entry_form in(38,35,32,47,31,48) and c.status_active=1 and c.is_deleted=0 and batch_id>0 $batch_num2 $dates_com $batch_num ";
	//echo "select batch_id,entry_form from  pro_fab_subprocess where entry_form in(38,35,32,47,31,48) and status_active=1 and is_deleted=0 and batch_id>0 $batch_num2";//die;
	//die;
	$k=1;$i=1;$m=1;$n=1;$p=1;$j=1;$h=1;
	foreach($sql_dyeing_subcon as $row_sub)
	{
		if($row_sub[csf('entry_form')]==38)
		{
		if($k!==1) $sub_cond_d.=",";
		$sub_cond_d.=$row_sub[csf('batch_id')];
		$k++;
		}
		if($row_sub[csf('entry_form')]==35)
		{
		if($i!==1) $row_d.=",";
		$row_d.=$row_sub[csf('batch_id')];
		$i++;
		}
		if($row_sub[csf('entry_form')]==32)
		{
		if($m!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$m++;
		}
		if($row_sub[csf('entry_form')]==47)
		{
		if($n!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$n++;
		}
		if($row_sub[csf('entry_form')]==31)
		{
		if($p!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$p++;
		}
		if($row_sub[csf('entry_form')]==48)
		{
		if($j!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$j++;
		}

	}//echo $sub_cond;die;

	/*$sql_batch_dyeing=sql_select("select batch_id from  pro_fab_subprocess where entry_form=35 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dyeing as $row_dyeing)
	{
		if($i!==1) $row_d.=",";
		$row_d.=$row_dyeing[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_h)
	{
		if($i!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$i++;
	}
	/*$sub_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sub_batch_h as $subrow_h)
	{
		if($i!==1) $subrow_heat.=",";
		$subrow_heat.=$subrow_h[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=47 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_h as $row_s)
	{
		if($i!==1) $row_sin.=",";
		$row_sin.=$row_s[csf('batch_id')];
		$i++;
	}
	$sql_batch_dry=sql_select("select batch_id from  pro_fab_subprocess where entry_form=31 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_dry as $rowdry)
	{
		if($i!==1) $row_dry.=",";
		$row_dry.=$rowdry[csf('batch_id')];
		$i++;
	}
	$sql_batch_stenter=sql_select("select batch_id from  pro_fab_subprocess where entry_form=48 and status_active=1 and is_deleted=0 and batch_id>0");
	$i=1;
	foreach($sql_batch_stenter as $row_sten)//Stentering
	{
		if($i!==1) $row_stentering.=",";
		$row_stentering.=$row_sten[csf('batch_id')];
		$i++;
	}*/

	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/
	if($db_type==0)
	{
		if($cbo_type==1) //Date Wise Report
		{
			if($batch_type==1) // Self
			{
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					$sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.style_ref_no,a.floor_id,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=0 and a.batch_against!=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $floor_num $year_cond $booking_num  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks order by a.batch_date)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against!=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $po_id_cond  $year_cond $booking_num  GROUP BY a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and b.po_id=0 and a.entry_form=0 and a.batch_against!=3   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
				}
			}
			else if($batch_type==2) //SubCon
			{
				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.color_id,a.floor_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type,b.grey_dia, a.process_id, a.remarks order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where   a.id=b.mst_id  and a.entry_form=36 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.grey_dia, a.process_id, a.remarks)
				union
				(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where    a.id=b.mst_id and a.entry_form=36 and  b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,b.grey_dia, a.process_id, a.remarks) order by batch_date";
				}
			}
			else if($batch_type==3) // Sample batch
			{
				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
				}
				else
				{*/
					$sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id  and  a.entry_form=0 and a.batch_against=3 and b.po_id>0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $non_booking_cond_for $working_comp_cond $dates_com  $batch_num  $ext_no $floor_num $year_cond $booking_num $po_id_cond GROUP BY a.id,a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c where a.id=b.mst_id and c.booking_no=a.booking_no  and  a.entry_form=0 and a.batch_against=3 and b.po_id=0 and b.status_active=1 and c.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $samp_buyercond $ext_no $floor_num $year_cond $non_booking_cond_for $booking_num  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.width_dia_type, a.process_id, a.remarks)  order by batch_date";
				//}
			}
			else if($batch_type==0) // All batch
			{
				// Self
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					$sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id  and  a.entry_form=0 and a.batch_against!=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $dates_com  $batch_num $po_id_cond $ext_no $floor_num $year_cond $booking_num  GROUP BY a.id, a.batch_no, b.item_description,b.prod_id order by a.batch_date, a.process_id, a.remarks)";
				}
				else
				{
				  $sql="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against!=3  and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $po_id_cond  $year_cond $booking_num  GROUP BY a.batch_no, b.item_description,b.prod_id, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id  and a.entry_form=0 and a.batch_against!=3  and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
				}

				// Subcon

				//select a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.company_id=$company and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond   GROUP BY a.batch_no, d.party_id, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
				 $sub_cond="select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.booking_no,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where   a.id=b.mst_id and a.entry_form=36  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond   $dates_com  $batch_num  $year_cond $sub_po_id_cond  $ref_cond $file_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.prod_id, b.width_dia_type,b.grey_dia order by a.batch_no";
				}
				else
				{
				$sub_cond="(select a.id,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b, pro_batch_create_mst a where    a.id=b.mst_id and a.entry_form=36  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $working_comp_cond  $batch_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type)
				union
				(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,group_concat( distinct b.po_id ) AS po_id  from pro_batch_create_dtls b,pro_batch_create_mst a where    a.id=b.mst_id and  a.entry_form=36 and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $sub_po_id_cond  $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,b.grey_dia) order by batch_date";
				}

				// Sample

				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!=""  || $job_number_id!=""  || $year!=0 || $buyer!=0)
				{
					 $sql_sam="select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where  a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com  $batch_num  $ext_no $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id order by a.batch_date";
				}
				else
				{*/
					 $sql_sam="(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,group_concat( distinct b.po_id ) AS po_id from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and  a.entry_form=0 and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $non_booking_cond_for $working_comp_cond $dates_com  $batch_num  $ext_no $floor_num $year_cond $po_id_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description,b.prod_id)
					union
					(select a.id,a.batch_against,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,group_concat( distinct b.po_id ) AS po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c  where a.id=b.mst_id  and c.booking_no=a.booking_no and  a.entry_form=0 and a.batch_against=3 and b.po_id=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and a.status_active=1 and a.is_deleted=0 $comp_cond  $samp_buyercond $working_comp_cond $dates_com  $batch_num $ext_no $floor_num $year_cond $non_booking_cond_for $po_id_cond $booking_num  GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.width_dia_type)  order by batch_date";
				//}
			}
		}
		else if($cbo_type==2) //wait for Heat Setting
		{
			if($batch_type==0 || $batch_type==1)
			{
				if($row_h!=0)
				{
					$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $buyerdata $order_no2 $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $comp_cond  $ext_no $floor_num $year_cond $booking_num GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
				}
			}
			if($batch_type==0 || $batch_type==2)
			{
				if($row_h!=0)
				{
					$sub_cond="( select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst $dates_com $comp_cond   $batch_num $booking_num  $working_comp_cond  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,null,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond GROUP BY a.id, a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
				}
			}
		}
		else if($cbo_type==3) // Wait For Dyeing
		{
			//$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
			//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
			$find_inset="and  FIND_IN_SET(33,a.process_id)";
			$find_inset_not="and not FIND_IN_SET(33,a.process_id)";
			//else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
			if($batch_type==0 || $batch_type==1)
			{
				$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32  and a.id not in($row_d) $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $comp_cond $working_comp_cond  $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $find_inset GROUP BY  a.id,a.batch_no, b.po_id,b.prod_id,b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(distinct c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no,b.po_id,b.prod_id, b.item_description, a.process_id, a.remarks ) order by batch_date";
			}
			if($batch_type==0 || $batch_type==2) //SubCon Deying
			{
				$sub_cond="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.order_no ) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks  from pro_batch_create_mst a,pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d where  a.id=b.mst_id and b.po_id=c.id  and d.subcon_job=c.job_no_mst    and a.id not in($sub_cond_d) $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num  $sub_buyer_cond $sub_job_cond $suborder_no $year_cond and a.entry_form=36 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   GROUP BY a.batch_no, b.po_id,b.prod_id,b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num $booking_num $ext_no $floor_num  GROUP BY a.id, a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no, a.extention_no,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type, a.process_id, a.remarks) order by batch_date";
			}
		}
		else if($cbo_type==4) //Wait For Re-Dyeing
		{
			if($batch_type==0 || $batch_type==1)
			{
				$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
				from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks
				from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and a.batch_against=2 and b.po_id=0 and a.id not in($row_d) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $comp_cond  $working_comp_cond $dates_com $batch_num $booking_num $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";
			}

			if($batch_type==0 || $batch_type==2) //SubCon Batch
			{

				$sql_subcon="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat(c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
				from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks)
				union
				(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and b.po_id=0 and a.id=b.mst_id  and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id not in($sub_cond_d)  $comp_cond $working_comp_cond  $dates_com  $batch_num $booking_num $floor_num $ext_no   GROUP BY a.id,a.batch_no, b.item_description, a.process_id, a.remarks) order by batch_date";

			}
		}
		else if($cbo_type==5) //Wait For Singeing
		{
			if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)";
			else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
			if($batch_type==0 || $batch_type==1)
			{
				$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
				if($w_sing_arr!=0)
				{
					$sql="(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,group_concat( distinct c.po_number ) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $order_no2 $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name, a.process_id, a.remarks)
					union
					(select a.id,a.batch_no,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,count(b.roll_no) as roll_no,b.width_dia_type,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where   a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond $comp_cond ";
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no,a.floor_id, b.item_description,a.batch_date, a.batch_weight, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type, a.process_id, a.remarks) order by batch_date ";
				}//W-end
				//echo $sql;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		//echo $row_d."=GGGGGGGGG";die;
		/*
		|--------------------------------------------------------------------------
		| Date Wise Report
		|--------------------------------------------------------------------------
		|
		*/
		if($cbo_type==1)
		{
			/*
			|--------------------------------------------------------------------------
			| All Batch
			|--------------------------------------------------------------------------
			|
			*/
			if($batch_type==0)
			{
				if($job_number_id!="" || $txt_order!="")
				{
			  		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,count(b.roll_no) as roll_no,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks order by a.batch_date";
			  		/*echo $sql;die;*/
				}
				else
				{
					//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS
					//echo $po_id_cond ;
					$sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, SUM(b.batch_qnty) AS batch_qnty, b.item_description, listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, b.prod_id, b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $batch_num $booking_num $po_id_cond $ext_no $floor_num $year_cond $comp_cond GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type, a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, SUM(b.batch_qnty) AS batch_qnty, b.item_description, listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, b.prod_id, b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $booking_num $po_id_cond $batch_num $floor_num $ext_no $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}

				//Sub
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
					$sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a
					where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.grey_dia, a.entry_form,b.rec_challan, a.process_id, a.remarks order by a.batch_date ";
				}
				else
				{
					$sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,b.gsm,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,b.gsm,b.grey_dia,a.entry_form,b.rec_challan,a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,b.grey_dia,b.gsm,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,b.gsm,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,b.grey_dia,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				}

				//Sam

				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="" )
				{
					//echo $order_no;
			 		$sql_sam="SELECT a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $booking_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
				}
				else
				{*/
					//echo $$job_number_id .'aaaa';
				$sql_sam="(SELECT a.id,a.batch_against,a.entry_form, a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $non_booking_cond_for $comp_cond $dates_com  $batch_num $booking_num $po_id_cond  $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no,a.booking_without_order, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a,wo_non_ord_samp_booking_mst c where a.id=b.mst_id  and a.booking_no=c.booking_no and  a.entry_form=0 and a.batch_against=2 and  b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $non_booking_cond_for $samp_buyercond $ref_cond2  $batch_num $booking_num $ext_no $floor_num $floor_num $year_cond GROUP BY a.id,a.batch_against,a.batch_no,a.booking_without_order, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
					//echo $sql_sam;//and b.po_id=0
				//}
				// echo $sql_sam;
			}

			/*
			|--------------------------------------------------------------------------
			| Self Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==1)
			{
				if($job_number_id!="" || $txt_order!="")
				{
					//echo $order_no;
			  		$sql="select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks order by a.batch_date";
				}
				else
				{
					//echo $$job_number_id .'aaaa';listagg(b.po_id ,',') within group (order by b.po_id) AS
					//echo $po_id_cond ;
					$sql="(select a.id,a.batch_against,a.entry_form, a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $po_id_cond $ext_no $floor_num $year_cond $comp_cond  GROUP BY a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.batch_against!=3 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com $booking_num  $batch_num $ext_no $floor_num $year_cond GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}
			}

			/*
			|--------------------------------------------------------------------------
			| Subcond Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==2)
			{
				if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="")
				{
					$sub_cond="select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,booking_no,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=36 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond  $sub_po_id_cond $ref_cond $file_cond $working_comp_cond $comp_cond  GROUP BY  a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, a.entry_form,b.rec_challan, a.process_id, a.remarks order by a.batch_date ";
				}
				else
				{
					$sub_cond="(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no,a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $year_cond $sub_po_id_cond $ref_cond $file_cond $working_comp_cond  $comp_cond GROUP BY a.id,a.batch_against,a.batch_no, a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,b.rec_challan,a.batch_no, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where   a.entry_form=36 and a.id=b.mst_id and b.po_id=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $comp_cond $dates_com  $batch_num $booking_num $ext_no $floor_num $year_cond  GROUP BY  a.id,a.batch_against, a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.extention_no,b.po_id,b.prod_id,b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";

				}
			// echo $sub_cond;die;
			}

			/*
			|--------------------------------------------------------------------------
			| Sample Batch
			|--------------------------------------------------------------------------
			|
			*/
			else if($batch_type==3)
			{
				/*if($job_number_id!="" || $txt_order!="" || $file_no!="" || $ref_no!="" )
				{
					//echo $order_no;
			 		$sql_sam="SELECT a.id,a.batch_against,a.entry_form,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and c.id=b.po_id and c.job_no_mst=d.job_no and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond  $dates_com  $batch_num $booking_num $ext_no $year_cond $po_id_cond  GROUP BY a.id,a.batch_against,a.batch_no,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no, a.extention_no,b.prod_id, b.width_dia_type,a.entry_form order by a.batch_date";
				}
				else
				{*/
					//echo $$job_number_id .'aaaa';
				  	$sql_sam="(SELECT a.id,a.batch_against,a.entry_form, a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks  from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.batch_against=3 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $po_id_cond $comp_cond $dates_com  $batch_num $booking_num $po_id_cond  $non_booking_cond_for $ext_no $floor_num $year_cond  GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight,a.total_trims_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no,a.booking_without_order, a.extention_no, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.batch_against,a.entry_form,a.batch_no,a.booking_without_order, a.batch_date,a.batch_weight,a.total_trims_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a ,wo_non_ord_samp_booking_mst c where  a.id=b.mst_id and c.booking_no=a.booking_no  and a.entry_form=0 and a.batch_against=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $samp_buyercond  $comp_cond $dates_com  $ref_cond2 $batch_num $booking_num $ext_no $floor_num $year_cond $non_booking_cond_for GROUP BY a.id,a.batch_against,a.batch_no, b.item_description,b.prod_id,a.batch_date,a.batch_weight,a.total_trims_weight,a.color_id,a.floor_id,a.style_ref_no,a.booking_no,a.booking_without_order,a.extention_no,b.prod_id,b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
					//echo $sql_sam;die;//and b.po_id=0
				//}
				// echo $sql_sam;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| wait for Heat Setting
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==2)
		{
			if($batch_type==1)// Self batch
			{
				//echo "dsd";
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				if($w_heat_arr!=0)
				{
					$sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sql .=")";
					$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks order by a.batch_date ";
				} echo $sql;

			} //Batch Type End
			if($batch_type==2) //Subcond batch
			{
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				if($w_heat_arr!=0)
				{
					$sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
					$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan, a.process_id, a.remarks order by a.batch_date";

				}
				//echo $sub_cond;
			}//Batch type End

			if($batch_type==0) // Self and Subcond batch
			{
				// Self batch
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);
				//echo $row_heat.'dd';;
				//if($row_heat)
				//{
					$sql="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $find_inset  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond $comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					if($row_heat)
					{
					$p=1;
						foreach($w_heat_arr as $h_batch_id)
						{
							if($p==1) $sql .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$h_batch_id).")";
							$p++;
						}
						$sql .=")";
					}

					$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks order by a.batch_date ";
				//}
				// echo $sql;

				//	Subcond batch
				$w_heat_arr=array_chunk(array_unique(explode(",",$row_heat)),999);

					$sub_cond="select a.id,a.entry_form,a.batch_against,a.batch_no,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com $comp_cond  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no   $working_comp_cond $year_cond ";
				if($w_heat_arr!=0)
				{
					$p=1;
					foreach($w_heat_arr as $h_batch_id)
					{
						if($p==1) $sub_cond .="and (a.id not in(".implode(',',$h_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$h_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
				}

					$sub_cond .="   GROUP BY a.id, a.batch_no,a.batch_against,d.party_id,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no, a.batch_against, b.item_description, b.po_id, b.prod_id, b.width_dia_type, d.subcon_job, d.job_no_prefix_num, d.party_id,a.entry_form ,b.rec_challan order by a.batch_date";

				//}
				//echo $sub_cond;
			} // end
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Dyeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==3)
		{
			if($batch_type==1)//Self Batch
			{
				//echo $row_d.'sdd';
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
						$sql .=")";
						$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
						union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date ";
						//echo $sql;die;
				}
			}//Self batch End
			if($batch_type==2) //SubCon Batch
			{

				$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				if($w_dyeing_arr!=0)
				{
					//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,count(b.roll_no) as roll_no,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
					$sub_cond .=")";
					$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";
					$p2=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p2++;
					}
					$sub_cond .=")";
					$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.extention_no,d.party_id,b.item_description,b.po_id,b.prod_id,
					b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by a.batch_date ";

				}//echo $sub_cond;
			}
			// echo $sub_cond;//die;
			if($batch_type==0) //Self Batch and SubCon Batch
			{
				// Self
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				//print_r($w_dyeing_arr);
				///echo $row_d.'DSDS';
				//die;
				if($w_dyeing_arr!=0)
				{
					$find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_fab_subprocess e,pro_fab_subprocess_dtls f where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and  e.batch_id=a.id and e.id=f.mst_id and b.prod_id=f.prod_id and e.entry_form=32 and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond  $comp_cond $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond ";
					if($row_d>0)
					{
						$p=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p++;
						}
						$sql .=")";
					}


						$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
						union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata3 $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not
						";

						if($row_d)
						{
							$p1=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p1==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p1++;
						}
						$sql .=")";
						}
						$sql .="  GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date
						";
					//echo $sql;
					/*

					union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not
						";

						if($row_d)
						{
							$p1=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p1==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p1++;
						}
						$sql .=")";
						}
						$sql .="  GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) order by batch_date


					union
						(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d where  a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst  $working_comp_cond $dates_com $jobdata $batch_num $booking_num $comp_cond $buyerdata $po_id_cond $ext_no $floor_num $year_cond and a.entry_form=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not GROUP BY a.id,a.batch_no, a.batch_against,b.item_description,a.batch_date, a.batch_weight,floor_id, a.color_id,d.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num,d.buyer_name,a.entry_form, a.process_id, a.remarks) */
						//ISsue id=23236
					//echo $sql;//die;
				}

				// Subcon
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				if($w_dyeing_arr!=0)
				{
					//and a.id not in($sub_cond_d) $find_cond="and  ',' || a.process_id || ',' LIKE '%33%'";
					//$find_cond_not="and  ',' || a.process_id || ',' not LIKE '%33%'";
					$sub_cond="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no,d.party_id as buyer_name, SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,d.subcon_job,d.job_no_prefix_num,d.party_id, a.process_id, a.remarks  from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond  $working_comp_cond  $comp_cond    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond ";
				if($sub_cond_d>0)
					{
						$p=1;
						foreach($w_dyeing_arr as $d_batch_id)
						{
							if($p==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
							$p++;
						}
						$sub_cond .=")";
					}
					$sub_cond .=" GROUP BY a.id, a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.extention_no,a.booking_no,d.party_id,b.item_description,b.po_id,b.prod_id,b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.booking_no,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,null,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) AS po_number,c.job_no_mst,d.job_no_prefix_num,null, a.process_id, a.remarks from pro_batch_create_dtls b,  subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond_not  $working_comp_cond $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $year_cond $dates_com  $comp_cond  ";

					if($sub_cond_d)
					{
						$p2=1;
					foreach($w_dyeing_arr as $d_batch_id)
					{
						if($p2==1) $sub_cond .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sub_cond .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p2++;
					}
					$sub_cond .=")";
					}
					$sub_cond .=" GROUP BY a.id,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.color_id,a.floor_id,a.style_ref_no,a.extention_no,d.party_id,b.item_description,b.po_id,b.prod_id,
					b.width_dia_type,d.subcon_job, d.job_no_prefix_num,d.party_id,a.booking_no,c.job_no_mst,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				} //echo $sub_cond;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Re-Dyeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==4)
		{

			if($batch_type==1)//Self Batch
			{
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 { //echo $d_batch_id;die;
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,d.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num ";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 {
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}
			}
			if($batch_type==2) //SubCon Batch
			{
				$w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				// print_r( $w_dyeing_subcon);
				if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d,
					$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $subcon_batch_id)
				 	{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
						$p++;
				 	}
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $booking_num $sub_buyer_cond  $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $d_batch_id)
					{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}

					 //echo $sql;
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				}
			}
			//echo $sql_subcon;

			if($batch_type==0) //Self Batch with SubCon Batch
			{
				//Self Batch
				$w_dyeing_arr=array_chunk(array_unique(explode(",",$row_d)),999);
				if($w_dyeing_arr!=0)
				{
					$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) as po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 { //echo $d_batch_id;die;
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,d.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where a.entry_form=0 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $working_comp_cond  $comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num ";
					if($row_d>0)
					{
					$p=1;
					foreach($w_dyeing_arr as $d_batch_id)
					 {
						if($p==1) $sql .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					 }
					$sql .=")";
					}
					$sql .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form, a.process_id, a.remarks) order by batch_date ";
				}

				//SubCon Batch
				$w_dyeing_subcon=array_chunk(array_unique(explode(",",$sub_cond_d)),999);
				// print_r( $w_dyeing_subcon);
				if($w_dyeing_subcon!=0)
				{ // subcon_ord_dtls c, subcon_ord_mst d,
					$sql_subcon="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number,d.subcon_job,d.job_no_prefix_num,d.party_id as buyer_name, a.process_id, a.remarks
					from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a where  a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond $dates_com  $batch_num $booking_num $sub_buyer_cond $sub_job_cond $suborder_no $ext_no $floor_num $year_cond $comp_cond";
					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $subcon_batch_id)
				 	{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$subcon_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$subcon_batch_id).")";
						$p++;
				 	}
					$sql_subcon .=")";
					}
					$sql_subcon .="  GROUP BY a.id, a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,d.subcon_job,d.job_no_prefix_num,d.party_id,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS sub_batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=36 and b.po_id=0 and a.id=b.mst_id and a.batch_against=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $working_comp_cond  $dates_com $sub_job_cond $batch_num $booking_num $sub_buyer_cond  $suborder_no $ext_no $floor_num $year_cond $comp_cond";

					if($sub_cond_d>0)
					{
					$p=1;
					foreach($w_dyeing_subcon as $d_batch_id)
					{
						if($p==1) $sql_subcon .=" and (a.id not in(".implode(',',$d_batch_id).")"; else  $sql_subcon .=" OR a.id not in(".implode(',',$d_batch_id).")";
						$p++;
					}
					$sql_subcon .=")";
					}

					 //echo $sql;

					$sql_subcon .="  GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";

				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Wait For Singeing
		|--------------------------------------------------------------------------
		|
		*/
		else if($cbo_type==5)
		{
			if($db_type==0) $find_cond="and  FIND_IN_SET(94,a.process_id)";
			else if($db_type==2) $find_cond="and  ',' || a.process_id || ',' LIKE '%94%'";
			$w_sing_arr=array_chunk(array_unique(explode(",",$row_sin)),999);
			if($w_sing_arr!=0)
			{
				$sql="(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,d.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by c.po_number) AS po_number,c.job_no_mst,d.job_no_prefix_num,d.buyer_name, a.process_id, a.remarks from pro_batch_create_dtls b,wo_po_break_down c,wo_po_details_master d,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id and b.po_id=c.id and d.job_no=c.job_no_mst    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $working_comp_cond $dates_com $jobdata $batch_num $booking_num $buyerdata $po_id_cond $ext_no $floor_num $year_cond $comp_cond  ";
				if($row_sin>0)
					{
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					}
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight,a.floor_id, a.color_id,a.style_ref_no,a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.buyer_name,a.entry_form,b.rec_challan, a.process_id, a.remarks)
					union
					(select a.id,a.entry_form,a.batch_no,a.batch_against,a.batch_date,a.batch_weight,a.floor_id,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.rec_challan,b.po_id,b.prod_id,b.width_dia_type,count(b.roll_no) as roll_no,null,null,null,null, a.process_id, a.remarks from pro_batch_create_dtls b,pro_batch_create_mst a where  a.entry_form=0 and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $find_cond $comp_cond $working_comp_cond $dates_com  $batch_num $booking_num $buyerdata $ext_no $floor_num $year_cond ";
				if($row_sin>0)
					{
					$p=1;
					foreach($w_sing_arr as $sing_row)
					{
						if($p==1) $sql .="and (a.id not in(".implode(',',$sing_row).")"; else  $sql .=" OR a.id not in(".implode(',',$sing_row).")";
						$p++;
					}
					$sql .=")";
					}
				$sql .=" GROUP BY a.id,a.batch_no,a.batch_against, b.item_description,a.batch_date, a.batch_weight, a.color_id,a.floor_id,a.style_ref_no, a.booking_no, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,a.entry_form,b.rec_challan, a.process_id, a.remarks) order by batch_date ";
				//echo $sql;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Date Wise Report
	|--------------------------------------------------------------------------
	|
	*/
	//echo $sql;
	if($cbo_type==1)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
			$sam_batchdata=sql_select($sql_sam);
		}
		else if($batch_type==1)
		{
			//echo $sql;
			$batchdata=sql_select($sql);
			//print_r($batchdata);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;die;
			$sub_batchdata=sql_select($sub_cond);
			// echo $sub_cond;die;
		}
		else if($batch_type==3)
		{
			//echo $sql_sam;die;
			$sam_batchdata=sql_select($sql_sam);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| wait for Heat Setting
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==2)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			$sub_batchdata=sql_select($sub_cond);
			// echo $sub_cond;die;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Dyeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==3)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sub_cond);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Re-Dyeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==4)
	{
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
			$sub_batchdata=sql_select($sql_subcon);
		}
		else if($batch_type==1)
		{
			$batchdata=sql_select($sql);
		}
		else if($batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sql_subcon);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Wait For Singeing
	|--------------------------------------------------------------------------
	|
	*/
	else if($cbo_type==5)
	{
		if($batch_type==1)
		{
		$batchdata=sql_select($sql);
		}
		if($batch_type==0)
		{
			$batchdata=sql_select($sql);
		}
		/*else if($batch_type==0 || $batch_type==2)
		{
			//echo $sub_cond;
			$sub_batchdata=sql_select($sub_cond);
		}*/
	}

	/*
	|--------------------------------------------------------------------------
	| for self batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$batch_id_arr = array();$po_id_arr = array();
	foreach ($batchdata as $val)
	{
		$batch_id_arr[$val[csf('id')]]=$val[csf('id')];
		$po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
		//$batch_color_arr[$val[csf('id')]]=$val[csf('color_id')];
	}
	//print_r($batch_color_arr);
	$batchIds = implode(",", $batch_id_arr);

	/*
	|--------------------------------------------------------------------------
	| for subcon batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$subcon_batch_id_arr = array();
	foreach ($sub_batchdata as $val)
	{
		$subcon_batch_id_arr[$val[csf('id')]]=$val[csf('id')];
	}
	$subconBatchIds = implode(",", $subcon_batch_id_arr);

	/*
	|--------------------------------------------------------------------------
	| for sample batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$sample_batch_id_arr = array();
	foreach ($sam_batchdata as $val)
	{
		$sample_batch_id_arr[$val[csf('id')]]=$val[csf('id')];
		$po_id_arr[$val[csf('po_id')]]=$val[csf('po_id')];
	}
	$sampleBatchIds = implode(",", $sample_batch_id_arr);

	/*
	|--------------------------------------------------------------------------
	| MYSQL Database
	|--------------------------------------------------------------------------
	|
	*/


	$merg_batch_id='';
	if($batchIds!="")
	{
		$merg_batch_id = $batchIds;
	}
	else if($batchIds!="" && $subconBatchIds!="")
	{
		$merg_batch_id = $batchIds.','.$subconBatchIds;
	}else
	{
		$merg_batch_id = $batchIds.','.$subconBatchIds.','.$sampleBatchIds;
	}
	//var_dump($merg_batch_id);
	if($db_type==0)
	{
		if($batchIds!="")
		{
			//$sql_yarn_lot = "SELECT a.id, group_concat(d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  GROUP BY a.id";
			$sql_yarn_lot = "SELECT b.prod_id, b.po_id, group_concat(d.yarn_lot) AS yarn_lot, group_concat(d.brand_id) as brand_id, group_concat(d.yarn_count) as yarn_count, group_concat(d.stitch_length) as stitch_length ,group_concat(d.width) as width FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  GROUP BY a.id";

			$sql_yarn_lot_res = sql_select($sql_yarn_lot);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ORACLE Database
	|--------------------------------------------------------------------------
	|
	*/
	else
	{
		/*
		|--------------------------------------------------------------------------
		| for self batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/

		if($batchIds != '')
		{
			//$sql_yarn_lot = "SELECT a.id,  LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id";
			/*$sql_yarn_lot = "SELECT b.prod_id, b.po_id, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY b.prod_id, b.po_id"; */
			$sql_yarn_lot = "SELECT a.id,b.prod_id, b.po_id, d.yarn_lot AS yarn_lot, d.brand_id as brand_id, d.yarn_count as yarn_count, d.stitch_length as stitch_length, d.width as width FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = $company and a.id in($batchIds) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id,b.prod_id, b.po_id,d.yarn_lot, d.brand_id, d.yarn_count, d.stitch_length, d.width";
			//echo $sql_yarn_lot;
			$sql_yarn_lot_res = sql_select($sql_yarn_lot);

		}

		/*
		|--------------------------------------------------------------------------
		| for subcon batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($subconBatchIds != '')
		{
		/* $subconYarnLotSql = "SELECT b.prod_id, b.po_id,d.fabric_description, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_production_dtls d WHERE a.id = b.mst_id AND TO_CHAR(b.po_id) = d.order_id  and b.item_description=d.fabric_description  AND a.company_id = ".$company." AND a.id IN(".$subconBatchIds.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.po_id,d.fabric_description"; */
		 $subconYarnLotSql = "SELECT a.id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot, d.brand AS brand, d.yrn_count_id as yrn_count_id, d.stitch_len as stitch_len, d.dia_width_type as dia_width_type FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_production_dtls d WHERE a.id = b.mst_id AND TO_CHAR(b.po_id) = d.order_id  and b.item_description=d.fabric_description  AND a.company_id = ".$company." AND a.id IN(".$subconBatchIds.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 GROUP BY b.prod_id, b.po_id,a.id";
			$subconYarnLotRslt = sql_select($subconYarnLotSql);
		}

		/*
		|--------------------------------------------------------------------------
		| for sample batch yarn lot
		|--------------------------------------------------------------------------
		|
		*/
		if($sampleBatchIds != '')
		{
			/*$sampleYarnLotSql = "SELECT b.prod_id, b.po_id, LISTAGG (CAST (d.yarn_lot AS VARCHAR2 (4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) AS yarn_lot FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = ".$company." AND a.id in(".$sampleBatchIds.") AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY b.prod_id, b.po_id"; */
			$sampleYarnLotSql = "SELECT a.id,b.prod_id, b.po_id,d.yarn_lot AS yarn_lot, d.brand_id as brand_id, d.yarn_count as yarn_count, d.stitch_length as stitch_length, d.width as width FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id AND a.company_id = ".$company." AND a.id in(".$sampleBatchIds.") AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 GROUP BY a.id,b.prod_id, b.po_id,d.yarn_lot";
			$sampleYarnLotRslt = sql_select($sampleYarnLotSql);
		}
	}
	 // inv_receive_master e  AND e.entry_form IN (2, 22) AND a.id = 65338

	/*
	|--------------------------------------------------------------------------
	| for self batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$yarn_lot_arr=array();
	foreach($sql_yarn_lot_res as $rows)
	{
		//$yarn_lot_arr[$rows[csf('id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand'].=$rows[csf('brand_id')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['yarn_count'].=$rows[csf('yarn_count')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['stitch_length'].=$rows[csf('stitch_length')].',';
		$yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['width'].=$rows[csf('width')].',';
	}
	// echo "<pre>";
	// print_r($yarn_lot_arr);
	// echo "</pre>";

	/*
	|--------------------------------------------------------------------------
	| for subcon batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$subcon_yarn_lot_arr=array();
	foreach($subconYarnLotRslt as $rows)
	{
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand'].=$rows[csf('brand')].',';
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['yrn_count_id'].=$rows[csf('yrn_count_id')].',';
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['stitch_len'].=$rows[csf('stitch_len')].',';
		$subcon_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['dia_width_type'].=$rows[csf('dia_width_type')].',';
	}
	//echo "<pre>";
	//print_r($subcon_yarn_lot_arr);

	/*
	|--------------------------------------------------------------------------
	| for sample batch yarn lot
	|--------------------------------------------------------------------------
	|
	*/
	$sample_yarn_lot_arr=array();
	foreach($sampleYarnLotRslt as $rows)
	{
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand'].=$rows[csf('brand_id')].',';
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['yarn_count'].=$rows[csf('yarn_count')].',';
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['stitch_length'].=$rows[csf('stitch_length')].',';
		$sample_yarn_lot_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['width'].=$rows[csf('width')].',';
	}

	//echo "<pre>";
	//print_r($sample_yarn_lot_arr);


	$sql_rcv_dtls = "SELECT a.id,b.prod_id, b.po_id,a.batch_no, a.booking_no, a.booking_no_id,e.receive_basis,e.booking_id, d.machine_dia,d.machine_gg from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
	where a.id=b.mst_id and b.roll_id = c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id = $company and a.id in($merg_batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  e.entry_form in(2,22,84) and c.entry_form in(2,22)
	group by a.id,b.prod_id, b.po_id,a.batch_no, a.booking_no, a.booking_no_id,e.receive_basis,e.booking_id, d.machine_dia,d.machine_gg";

	//echo $sql_rcv_dtls;
	$sql_rcv_dtls_res = sql_select($sql_rcv_dtls);
	$book_id_arr=array();
	foreach ($sql_rcv_dtls_res as $rows)
	{
		array_push($book_id_arr,$rows[csf('booking_id')]);
	}

	// echo "select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where status_active=1 and is_deleted=0 ".where_con_using_array($book_id_arr,0,'id')." ";
	$program_data = sql_select("select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where status_active=1 and is_deleted=0 ".where_con_using_array($book_id_arr,0,'id')." ");

	$dia_gauge_arr=array();
	foreach($sql_rcv_dtls_res as $rows)
	{
		if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 4 || $row[csf('receive_basis')] == 11) //from Entry page
		{
			$dia_gauge_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['machine_dia'] =$rows[csf('machine_dia')];
			$dia_gauge_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['machine_gg']=$rows[csf('machine_gg')];
		}
		else if ($row[csf('receive_basis')] == 2) //Knitting Plan
		{
			$dia_gauge_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['machine_dia'] = $program_data[0][csf('machine_dia')];
			$dia_gauge_arr[$rows[csf('id')]][$rows[csf('prod_id')]][$rows[csf('po_id')]]['machine_gg']=$program_data[0][csf('machine_gg')];

		}

	}

	// echo "<pre>";
	// print_r($dia_gauge_arr);


	ob_start();
	?>
	<div align="center">
	<fieldset style="width:1375px;">
	<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type]; ?> </strong>
		<br><b>
		<?
		//echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
		echo ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
		?> </b>
 	</div>
 	<div align="center">
  	<?php
	/*
	|--------------------------------------------------------------------------
	| All Batch
	|--------------------------------------------------------------------------
	|
	*/

	if($batch_type==0)
  	{

  		?>
	 	<div align="left"><b>Self Batch</b>
		 	<table class="rpt_table" width="2490" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Against</th>
		                <th width="80">Batch Color</th>
		                <th width="80">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
                        <th width="100">Pub ship date</th>
		                <th width="70">File No</th>
		                <th width="70">Ref. No</th>
		                <th width="80">W/O NO.</th>
		                 <th width="70">Job</th>
		                <th width="100">Construction</th>
		                <th width="150">Composition</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
		                <th width="50">Trims Weight</th>
						<th width="50">Total Roll</th>
		                <th width="100">Booking Grey Req.Qty</th>
		                <th width="100">Dia*Gauge</th>
		                <th width="100">Finish Dia</th>
		                <th width="100">Stitch Length</th>
		                <th width="100">Yarn Count</th>
		                <th width="100">Brand</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
		 	</table>
			<div style=" max-height:350px; width:2490px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body" width="2470" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
					<?
					$po_cond_in=where_con_using_array($po_id_arr,0,'b.po_break_down_id');

                    $booking_qnty_arr=array();
                    $queryFab=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $po_cond_in  group by b.po_break_down_id,a.booking_no, b.fabric_color_id");



                    foreach($queryFab as $row)
                    {
                        $booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
						//$booking_qnty_arr2[$row[csf('po_break_down_id')]][$row[csf('booking_no')]]+=$row[csf('grey_fab_qnty')];
                    }
					unset($queryFab);

                    $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($smn_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }
                    $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }
                    $i=1;
                    $f=0; $bb=0;
                    $b=0;
                    $btq=0;
                    $tot_book_qty=0;  $tot_batch_wgt=0;$tot_trims_wgt=0;$total_tot_batch_wgt=0;$total_tot_trims_wgt=0;
                    $tot_grey_req_qty=0;
                    $batch_chk_arr=array();
                    $booking_chk_arr=array();
                    foreach($batchdata as $batch)
                    {
                      // echo $batch[csf('booking_no')].'dd';
					    $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
                        //if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
                        if($sam_booking!=$batch[csf('booking_no')])
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $order_id=$batch[csf('po_id')];
                            $color_id=$batch[csf('color_id')];
                            $booking_no=$batch[csf('booking_no')];
                            $booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
                            $desc=explode(",",$batch[csf('item_description')]);
                            $entry_form=$batch[csf('entry_form')];
                            $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
                            $po_num='';
                            $po_file='';
                            $po_ref='';
                            $job_num='';
                            $job_buyers='';
                            $yarn_lot_num='';
                            $yarn_brand='';
                            $yarn_count_num='';
                            $yarn_stitch='';
                            $yarn_fin_dia='';
                            $grey_booking_qty=0;
                            $buyer_style=''; $ship_DateCond='';
							$machine_dia='';
							$machine_gg='';
							$dia_gauge='';
                            /*print_r($po_ids);die;*/
                            foreach($po_ids as $p_id)
                            {
                            	//echo $batch[csf('id')]."=".$batch[csf('prod_id')]."=".$p_id."<br>";
                            	//echo $yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot']."<br><br>";
                            	if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
								if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
								if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
								if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
								if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
								//$ylot=rtrim($yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'],',');
								$ylot=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
								$ybrand=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['brand'],',');
								$yyarn_count=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['yarn_count'],',');
								$ystitch_length=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['stitch_length'],',');
								$yfin_dia=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['width'],',');



								if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
								if($yarn_brand=='') $yarn_brand=$ybrand;else $yarn_brand.=",".$ybrand;
								if($yarn_count_num=='') $yarn_count_num=$yyarn_count;else $yarn_count_num.=",".$yyarn_count;
								if($yarn_stitch=='') $yarn_stitch=$ystitch_length;else $yarn_stitch.=",".$ystitch_length;
								if($yarn_fin_dia=='') $yarn_fin_dia=$yfin_dia;else $yarn_fin_dia.=",".$yfin_dia;

								$grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];


								//echo $p_id.'='.$booking_no.'='.$color_id.',';
								if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
								$pub_shipment_date=$po_array[$p_id]['pub_shipment_date'];

								if($ship_DateCond=='') $ship_DateCond=$pub_shipment_date;else $ship_DateCond.=",".$pub_shipment_date;


								if($machine_dia=='') $machine_dia = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_dia']; else $machine_dia='';
								if($machine_gg=='') $machine_gg = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_gg']; else $machine_gg='';



                            }
                            $yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));
                            $yarn_brands=implode(",",array_unique(explode(",",$yarn_brand)));
                            $yarn_counts=implode(",",array_unique(explode(",",$yarn_count_num)));
                            $yarn_stitchs=implode(",",array_unique(explode(",",$yarn_stitch)));
                            $yarn_fin_dias=implode(",",array_unique(explode(",",$yarn_fin_dia)));


							if( $machine_dia !='' && $machine_gg !='')
							{
								$dia_gauge = $machine_dia.'x'.$machine_gg;
							}
							else if( $machine_gg !='' )
							{
								$dia_gauge = $machine_dia;
							}
							else
							{
								$dia_gauge = $machine_gg;
							}


                           /* $buyer_po=""; $buyer_style="";
				            $buyer_po_id=explode(",",$row[csf('po_id')]);*/
							/*foreach($po_ids as $p_id)
							{
							if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
							if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
							}
							$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
							$buyer_style=implode(",",array_unique(explode(",",$buyer_style)))*/;//add by samiur

                            // $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                            // $book_qty+=$booking_qty;

                            $booking_color=$booking_no;//$order_id.$booking_no.$color_id;
                            if (!in_array($booking_color,$booking_chk_arr))
                            {
                                $bb++;
                                $booking_chk_arr[]=$booking_color;
                                $tot_book_qty=$grey_booking_qty;
                            }
                            else
                            {
                                $tot_book_qty=0;
                            }

							 $batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$batch_wgt_chk_arr))
                            {
                                $b++;
                                $batch_wgt_chk_arr[]=$batch_id;
                                $tot_batch_wgt=$batch[csf('batch_weight')];
                                $tot_trims_wgt=$batch[csf('total_trims_weight')];
                            }
                            else
                            {
                                $tot_batch_wgt=0;
                                $tot_trims_wgt=0;
                            }

                          // echo  $batch[csf('id')].'dd';;

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}

                           ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                            <?
							if (!in_array($batch[csf('id')],$batch_chk_arr) )
                            {
                                $f++;
                                ?>
                                <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
                                <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                                <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                 <td style="word-break:break-all;"  width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                <td style="word-break:break-all;" title="<? echo $order_id.'='.$batch[csf('color_id')];?>" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                                <td style="word-break:break-all;" width="80"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers; ?></div></td>
                                <?
                                $batch_chk_arr[]=$batch[csf('id')];
                               // $book_qty+=$booking_qty;
                            }
                            else
                            {
                                ?>
                                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
                                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
                                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
                                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
                                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td style="word-break:break-all;"  width="80"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                <?
                            }
                            ?>

                            <td style="word-break:break-all;" width="80"><p><? echo $buyer_style; ?></p></td>
                            <td style="word-break:break-all;" width="120"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
                            <td style="word-break:break-all;" width="100"><p><? echo $ship_DateCond;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
                            <td style="word-break:break-all;" width="70"><p><? echo $po_file; ?></p></td>
                            <td style="word-break:break-all;" width="70"><p><?  echo $po_ref; ?></p></td>
                            <td style="word-break:break-all;" width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                            <td style="word-break:break-all;" width="70"><p><? echo $job_num; ?></p></td>
                            <td style="word-break:break-all;" width="100"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
                            <td style="word-break:break-all;" width="150"><div style="width:150px; word-wrap:break-word;"><?

                            if($desc[4]!="")
                            {
                            	$compositions= $desc[1].' ' . $desc[2];
                            	$gsms= $desc[3];
                            }
                            else
                            {
                            	$compositions= $desc[1];
                            	$gsms= $desc[2];
                            }

                            echo $compositions;

                            ?></div></td>
                            <td style="word-break:break-all;"  width="50"><p><? echo end($desc);//$desc[3]; ?></p></td>
                            <td style="word-break:break-all;"  width="50"><p><? echo  $gsms;//$desc[2]; ?></p></td>
                            <td style="word-break:break-all;"  title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
                            <td style="word-break:break-all;" align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                            <td style="word-break:break-all;"  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($tot_batch_wgt,2); ?></td>
                            <td style="word-break:break-all;"  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($tot_trims_wgt,2); ?></td>
							<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
                            <td style="word-break:break-all;"  align="right" width="100" title="Booking Color Wise Qty"><? echo number_format($tot_book_qty,2);?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $dia_gauge; ?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $yarn_fin_dias; ?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $yarn_stitchs;?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $yarncount[$yarn_counts];?></td>
                            <td style="word-break:break-all;" width="100" align='center'><? echo $brand_name_arr[$yarn_brands];?></td>
                            <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')];?></td>
                            <td style="word-break:break-all;"><? echo $process_name;?></td>
                            </tr>
                            <?
                            $i++;
                            $btq+=$batch[csf('batch_qnty')];
                            $tot_grey_req_qty+=$tot_book_qty;
							 $total_tot_batch_wgt+=$tot_batch_wgt;
							$total_tot_trims_wgt+=$tot_trims_wgt;
                            $balance=$tot_grey_req_qty-$btq;
                            $bal_qty=$balance;
                            if($bal_qty>0)
                            {
                            $color="";
                            $txt="Over Batch Qty";
                            }
                            else if($bal_qty<0)
                            {
                            $color="red";
                            $txt="Below Batch Qty";
                            }
                        }
                    }
                    ?>
			 		</tbody>
				</table>
				<table class="rpt_table" width="2470"  cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
                             <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="80">&nbsp;</th>

			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_btq"><? echo number_format($btq,2); ?></th>
			                <th width="50"  align="right"><? echo number_format($total_tot_batch_wgt,2); ?></th>
			                <th width="50"  align="right"><? echo number_format($total_tot_trims_wgt,2); ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100"></th>
			                <th width="100"></th>
			                <th width="100"></th>
			                <th width="100"></th>
			                <th width="100"></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="18" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="18" align="left">&nbsp; <? echo number_format($btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="18" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
				</table>
			</div> <br/>
		</div>


		<div align="left"> <b>SubCond Batch </b>
		 	<table class="rpt_table" width="1950" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Aganist</th>
		                <th width="80">Batch Color</th>
		                <th width="50">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
		                <th width="80">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="80">Recv. Challan</th>
		                <th width="150">Fabrics Desc.</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
						<th width="50">Total Roll</th>
		                <th width="100">Material Recv Grey Req. Qty</th>
						<th width="60">Dia*Gauge</th>
		                <th width="60">Finish Dia</th>
		                <th width="60">Stitch Length</th>
		                <th width="60">Yarn Count</th>
		                <th width="60">Brand</th>

		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
			</table>
			<div style=" max-height:350px; width:1950px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body2" width="1930" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tbody>
						<?
						/*$booking_qnty_arr=array();
						$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
						foreach($query as $row)
						{
							$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
						}*/


						$sub_material_recv_arr=array();$sub_material_description_arr=array();
						$subcon_sql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, sum(b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0 group by b.order_id, a.chalan_no,b.color_id,a.trans_type,b.material_description,b.gsm");
						foreach($subcon_sql as $row)
						{
							$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
							$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
						}

						//var_dump($sub_material_description_arr);die;
						$i=1;
						$f=0;
						$btq=0; $k=0;
						$book_qty_subcon=0;$subcon_tot_book_qty=$sub_tot_batch_wgt=0;$sub_batch_wgt_chk_arr=array();
						$total_sub_tot_batch_wgt=0;
						$batch_chk_arr=array();$sub_qty_chk_arr=array();
						// print_r($sub_batchdata);
						foreach($sub_batchdata as $batch)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$order_id_sub=$batch[csf('po_id')];
							$color_id=$batch[csf('color_id')];
							$booking_no=$batch[csf('booking_no')];
							$sub_challan=$batch[csf('rec_challan')];
							$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
							$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
							$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
							$desc=explode(",",$batch[csf('item_description')]);
							$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
							$entry_form=$batch[csf('entry_form')];
							$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
							$sub_po_num='';
							$sub_job_buyers='';
							$sub_job_buyers='';$subcon_yarn_lot_num='';
							$subcon_booking_qty=0;
							$sub_buyer_style='';
							$machine_dia='';
							$machine_gg='';
							$dia_gauge='';
							foreach($po_ids as $p_id)
							{
								if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
								if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
								if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$sub_po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$sub_po_array[$p_id]['buyer']];
								$subcon_booking_qty+=$sub_material_recv_arr[$p_id];

								//for yarn lot
								//echo $subcon_yarn_lot_arr[$batch[csf('prod_id')]][$p_id]['lot'].', ';
								$subconYlot=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
								$subconYbrand=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['brand'],',');
								$subconYrnCount=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['yrn_count_id'],',');
								$subconYstitchlen=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['stitch_len'],',');
								$subconYdiawidth=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['dia_width_type'],',');


								if($sub_buyer_style=="") $sub_buyer_style=$sub_po_array[$p_id]['style_no']; else $sub_buyer_style.=','.$sub_po_array[$p_id]['style_no'];
								//echo $subconYlot.', ';
								/*if($subcon_yarn_lot_num=='')
									$subcon_yarn_lot_num=$subconYlot;
								else
									$subcon_yarn_lot_num.=",".$subconYlot;*/
									  if($subcon_yarn_lot_num=='') $subcon_yarn_lot_num=$subconYlot;else $subcon_yarn_lot_num.=",".$subconYlot;
									  if($subcon_yarn_brand=='') $subcon_yarn_brand=$subconYbrand;else $subcon_yarn_brand.=",".$subconYbrand;
									  if($subcon_yarn_count=='') $subcon_yarn_count=$subconYrnCount;else $subcon_yarn_count.=",".$subconYrnCount;
									  if($subcon_yarn_stitchlen=='') $subcon_yarn_stitchlen=$subconYstitchlen;else $subcon_yarn_stitchlen.=",".$subconYstitchlen;
									  if($subcon_yarn_diawidth=='') $subcon_yarn_diawidth=$subconYdiawidth;else $subcon_yarn_diawidth.=",".$subconYdiawidth;


								if($machine_dia=='') $machine_dia = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_dia']; else $machine_dia='';
								if($machine_gg=='') $machine_gg = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_gg']; else $machine_gg='';

							}
							$subcon_yarn_lots=implode(",",array_unique(explode(",",$subcon_yarn_lot_num)));
							$subcon_yarn_brands=implode(",",array_unique(explode(",",$subcon_yarn_brand)));
							$subcon_yarn_counts=implode(",",array_unique(explode(",",$subcon_yarn_count)));
							$subcon_yarn_stitchlens=implode(",",array_unique(explode(",",$subcon_yarn_stitchlen)));
							$subcon_yarn_diawidths=implode(",",array_unique(explode(",",$subcon_yarn_diawidth)));

							if( $machine_dia !='' && $machine_gg !='')
							{
								$dia_gauge = $machine_dia.'x'.$machine_gg;
							}
							else if( $machine_gg !='' )
							{
								$dia_gauge = $machine_dia;
							}
							else
							{
								$dia_gauge = $machine_gg;
							}

							$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
							if (!in_array($booking_color2,$sub_qty_chk_arr))
							{
								$k++;
								//echo $subcon_booking_qty;
								$sub_qty_chk_arr[]=$booking_color2;
								$subcon_tot_book_qty=$subcon_booking_qty;
							}
							else
							{
								 $subcon_tot_book_qty=0;
							}

							 $batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$sub_batch_wgt_chk_arr))
                            {
                                $k++;
                                $sub_batch_wgt_chk_arr[]=$batch_id;
                                $sub_tot_batch_wgt=$batch[csf('batch_weight')];
                            }
                            else
                            {
                                $sub_tot_batch_wgt=0;
                            }

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}

							//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot'];
							//echo "<pre>". $batch[csf('prod_id')].'='.$order_id_sub;
							//print_r($yarn_lot_arr);
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            	<? if (!in_array($batch[csf('id')],$batch_chk_arr) )
								{
									$f++;
											?>
					                <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
					                <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
					                <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
					                <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
					                 <td style="word-break:break-all;" width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
					                <td style="word-break:break-all;" width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;" width="50"><p><? echo $sub_job_buyers; ?></p></td>
									<?
					                $batch_chk_arr[]=$batch[csf('id')];

				                }
								else
								{ ?>
					                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
					                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
					                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
					                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
					                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
					                <td style="word-break:break-all;"  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
									<?
								}
								?>
								<td style="word-break:break-all;" width="80"><p><? echo $sub_buyer_style; ?></p></td>
				                <td style="word-break:break-all;" width="120"><p><? echo $sub_po_num; ?></p></td>
				                <td style="word-break:break-all;" width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? echo implode(",",array_unique(explode(",",$sub_job_num))); ?></p></td>
				                <td style="word-break:break-all;" width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
				                <td width="150" ><p><? echo $batch[csf('item_description')];//$item_descript; ?></p></td>
				                <td style="word-break:break-all;"  width="50" title="<? echo $desc[2];  ?>"><p><? echo $batch[csf('grey_dia')]; ?></p></td>
				                <td  style="word-break:break-all;" width="50"><p><? echo $batch[csf('gsm')]; ?></p></td>
				                 <td style="word-break:break-all;" align="left" width="60"><p><? echo $subcon_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>

				                <td style="word-break:break-all;" align="right" width="70" ><? echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
				                <td style="word-break:break-all;"  align="right" width="50" title="<? echo $sub_tot_batch_wgt; ?>"><? echo number_format($sub_tot_batch_wgt,2); ?></td>
								<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
				                <td style="word-break:break-all;" align="right" width="100" title="SunCon Material Recv Qty"><? echo number_format($subcon_tot_book_qty,2); ?></td>
								<td style="word-break:break-all;" align="center" width="60"><p><? echo $dia_gauge; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60"><p><? echo $subcon_yarn_diawidths; ?></p></td>
								 <td style="word-break:break-all;" align="center" width="60"><p><? echo $subcon_yarn_stitchlens; ?></p></td>
								 <td style="word-break:break-all;" align="center" width="60"><p><? echo $subcon_yarn_counts; ?></p></td>
								 <td style="word-break:break-all;" align="center" width="60"><p><? echo $subcon_yarn_brands;?></p></td>
				                <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')]; ?></td>
				                <td style="word-break:break-all;"><? echo $process_name; ?></td>
				            </tr>
							<?
			                $i++;
			                $btq_subcon+=$batch[csf('sub_batch_qnty')];
							$book_qty_subcon+=$subcon_tot_book_qty;
							$total_sub_tot_batch_wgt+=$sub_tot_batch_wgt;
			                $balance=$book_qty_subcon-$btq_subcon;
			                $bal_qty_subcon=$balance;
			                if($bal_qty_subcon>0)
			                {
			                $color="";
			                $txt="Over Batch Qty";
			                }
			                else if($bal_qty_subcon<0)
			                {
			                $color="red";
			                $txt="Below Batch Qty";
			                }
			            }
			        	 ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="1930" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="80">&nbsp;</th>

			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_btq_subcon"><? echo $btq_subcon; ?></th>
			                <th width="50" align="right"><? echo $total_sub_tot_batch_wgt; ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo $book_qty_subcon; ?></th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" title="SunCon Material Recv Qty" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="15" align="left">&nbsp;
			                 <? echo number_format($book_qty_subcon,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="15" align="left">&nbsp; <? echo number_format($btq_subcon,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="15" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
				</table>
			</div>
		</div>
		<div align="left"><b>Sample Batch</b>
			<table class="rpt_table" width="2180" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="75">Batch Date</th>
		                <th width="60">Batch No</th>
		                <th width="40">Ext. No</th>
		                <th width="80">Batch Aganist</th>
		                <th width="80">Batch Color</th>
		                <th width="50">Buyer</th>
		                <th width="80">Style Ref</th>
		                <th width="120">PO No</th>
		                <th width="70">File No</th>
		                <th width="70">Ref No</th>
		                <th width="100">W/O NO.</th>
		                <th width="70">Job</th>
		                <th width="100">Construction</th>
		                <th width="150">Composition</th>
		                <th width="50">Dia/ Width</th>
		                <th width="50">GSM</th>
		                <th width="60">Lot No</th>
		                <th width="70">Batch Qty.</th>
		                <th width="50">Batch Weight</th>
		                <th width="50">Trims Weight</th>
						<th width="50">Total Roll</th>
		                <th width="100">Booking Grey Req.Qty</th>
		                <th width="60">Dia*Gauge</th>
		                <th width="60">Finish Dia</th>
		                <th width="60">Stitch Length</th>
		                <th width="60">Yarn Count</th>
		                <th width="60">Brand</th>
		                <th width="100">Remarks</th>
		                <th>Process Name</th>
		            </tr>
		        </thead>
			</table>
			<div style=" max-height:350px; width:2180px; overflow-y:scroll;" id="scroll_body">
			 	<table class="rpt_table" id="table_body3" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
						<?
						$sam_booking_qnty_arr=array();
						$sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
						foreach($sam_query as $row)
						{
							$sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
						}

						$smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
						foreach($smn_query as $row)
						{
							$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}
						$sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
						foreach($sam_query as $row)
						{
							$sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}

						// GETTING BUYER NAME
						/*$non_order_arr=array();
			            $sql_non_order="SELECT a.company_id,a.grouping as smp_ref, a.buyer_id as buyer_name, b.booking_no, b.bh_qty
			            from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			            where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			            $result_sql_order=sql_select($sql_non_order);
			            foreach($result_sql_order as $row)
			            {

							$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
							$non_order_arr[$row[csf('booking_no')]]['smp_ref']=$row[csf('smp_ref')];
							$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
			            }*/
			            // echo "<pre>";
			            // print_r($non_order_arr);

						$i=1;
						$f=0;
						$b=0;
						$btq=0;$bb=0;
						$tot_book_qty2=0;
						$tot_grey_req_qty=$samp_tot_batch_wgt=$samp_tot_trims_wgt=0;
						$batch_chk_arr=array();
						$booking_chk_arr2=array();$samp_batch_wgt_chk_arr=array();
						// print_r($sam_batchdata );
						$machine_dia='';
						$machine_gg='';
						$dia_gauge='';
						foreach($sam_batchdata as $batch)
						{
							$sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
							if($sam_booking==$batch[csf('booking_no')])
							{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
							$po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$sam_booking_qty=0;
							$sample_yarn_lot_num="";$buyer_style='';
							foreach($po_ids as $p_id)
							{
								if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
								if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
								if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
								if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
								if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];

								if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];

								$sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
								//for yarn lot
								$sampleYlot=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
								$sampleYbrand=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['brand'],',');
								$sampleYyarn_count=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['yarn_count'],',');
								$sampleYstitch_length=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['stitch_length'],',');
								$sampleYwidth=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['width'],',');


								/*if($sample_yarn_lot_num=='')
									$sample_yarn_lot_num=$sampleYlot;
								else
									$sample_yarn_lot_num.=",".$sampleYlot;*/
									if($sample_yarn_lot_num=='') $sample_yarn_lot_num=$sampleYlot;else $sample_yarn_lot_num.=",".$sampleYlot;
									if($sample_yarn_brand=='') $sample_yarn_brand=$sampleYbrand;else $sample_yarn_brand.=",".$sampleYbrand;
									if($sample_yarn_yarn_count=='') $sample_yarn_yarn_count=$sampleYyarn_count;else $sample_yarn_yarn_count.=",".$sampleYyarn_count;
									if($sample_yarn_s_length=='') $sample_yarn_s_length=$sampleYstitch_length;else $sample_yarn_s_length.=",".$sampleYstitch_length;
									if($sample_yarn_width=='') $sample_yarn_width=$sampleYwidth;else $sample_yarn_width.=",".$sampleYwidth;


								if($machine_dia=='') $machine_dia = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_dia']; else $machine_dia='';
								if($machine_gg=='') $machine_gg = $dia_gauge_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['machine_gg']; else $machine_gg='';
							}

							$sample_yarn_lots=implode(",",array_unique(explode(",",$sample_yarn_lot_num)));
							$sample_yarn_brands=implode(",",array_unique(explode(",",$sample_yarn_brand)));
							$sample_yarn_yarn_counts=implode(",",array_unique(explode(",",$sample_yarn_yarn_count)));
							$sample_yarn_s_lengths=implode(",",array_unique(explode(",",$sample_yarn_s_length)));
							$sample_yarn_widths=implode(",",array_unique(explode(",",$sample_yarn_width)));

							if( $machine_dia !='' && $machine_gg !='')
							{
								$dia_gauge = $machine_dia.'x'.$machine_gg;
							}
							else if( $machine_gg !='' )
							{
								$dia_gauge = $machine_dia;
							}
							else
							{
								$dia_gauge = $machine_gg;
							}

							$order_id=$batch[csf('po_id')];
							$color_id=$batch[csf('color_id')];
							$booking_no=$batch[csf('booking_no')];
							//$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];

							$desc=explode(",",$batch[csf('item_description')]);
							$entry_form=$batch[csf('entry_form')];
							//$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
							//if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
							//if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
							//$po_file=$po_array[$order_id]['file'];
							//$po_ref=$po_array[$order_id]['ref'];
							// $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
							$booking_color=$booking_no;
							if (!in_array($booking_color,$booking_chk_arr2))
							{
								$bb++;
								$booking_chk_arr2[]=$booking_color;
								$tot_book_qty2=$sam_booking_qty;
							}
							else
							{
								$tot_book_qty2=0;
							}
							$batch_id=$batch[csf('id')];
                            if (!in_array($batch_id,$samp_batch_wgt_chk_arr))
                            {
                                $b++;
                                $samp_batch_wgt_chk_arr[]=$batch_id;
                                $samp_tot_batch_wgt=$batch[csf('batch_weight')];
                                $samp_tot_trims_wgt=$batch[csf('total_trims_weight')];
                            }
                            else
                            {
                                $samp_tot_batch_wgt=0;
                                $samp_tot_trims_wgt=0;
                            }

                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				            <? if (!in_array($batch[csf('id')],$booking_chk_arr2))
								{
									$f++;
									?>
                                    <td style="word-break:break-all;" width="30"><? echo $f; ?></td>
                                    <td style="word-break:break-all;" align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                    <td style="word-break:break-all;"  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                                    <td style="word-break:break-all;"  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                    <td style="word-break:break-all;" width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                    <td style="word-break:break-all;" width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                    <td style="word-break:break-all;" width="50"><p><? echo $job_buyers.$buyer_arr[$non_order_arr[$batch[csf('booking_no')]]['buyer_name']]; ?></p></td>
                                    <?
                                    $batch_chk_arr[]=$batch[csf('id')];
				               		// $book_qty+=$booking_qty;
				                  }
								else
								  { ?>
				                <td style="word-break:break-all;" width="30"><? //echo $sl; ?></td>
				                <td style="word-break:break-all;"   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
				                <td style="word-break:break-all;"   width="60"><p><? //echo $booking_qty; ?></p></td>
				                <td style="word-break:break-all;"   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
				                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td style="word-break:break-all;"  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td style="word-break:break-all;"  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
								<? }
								$samp_ref_no=$non_order_arr[$batch[csf('booking_no')]]['samp_ref_no'];
								$booking_without_order=$batch[csf('booking_without_order')];
								?>
								<td style="word-break:break-all;" width="80"><p><? if($booking_without_order==1) echo $non_order_arr[$batch[csf('booking_no')]]['style_desc'];else echo $buyer_style; ?></p></td>
				                <td style="word-break:break-all;" width="120" ><p><? echo $po_num; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? echo $po_file; ?></p></td>
				                <td style="word-break:break-all;" width="70"><p><? if($booking_without_order==1) echo $samp_ref_no; else echo $po_ref; ?></p></td>
				                <td style="word-break:break-all;" width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
				                <td style="word-break:break-all;" width="70" title="<? echo $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $job_num; ?></p></td>
				                <td style="word-break:break-all;" width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0]; ?></p></td>
				                <td style="word-break:break-all;" width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
				                <td style="word-break:break-all;" width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
				                <td style="word-break:break-all;" width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
				                 <td style="word-break:break-all;" align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $sample_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
				                <td style="word-break:break-all;" align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
				                <td style="word-break:break-all;" align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($samp_tot_batch_wgt,2); ?></td>
				                <td style="word-break:break-all;" align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($samp_tot_trims_wgt,2); ?></td>
								<td style="word-break:break-all;" align="right" width="50"><? echo $batch[csf('roll_no')];?></td>
				                <td title="Booking Color Wise Qty" style="word-break:break-all;" align="right" width="100"><? echo number_format($tot_book_qty2,2);?></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $dia_gauge; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $sample_yarn_widths; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $sample_yarn_s_lengths; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $sample_yarn_yarn_counts; ?></p></td>
								<td style="word-break:break-all;" align="center" width="60" ><p><? echo $sample_yarn_brands; ?></p></td>
				                <td style="word-break:break-all;" width="100"><? echo $batch[csf('remarks')];?></td>
				                <td style="word-break:break-all;"><? echo $process_name;?></td>
				            </tr>
							<?
				                $i++;
				                $sam_btq+=$batch[csf('batch_qnty')];
								$tot_grey_req_qty+=$tot_book_qty2;
								$total_samp_tot_batch_wgt+=$samp_tot_batch_wgt;
								$total_samp_tot_trims_wgt+=$samp_tot_trims_wgt;
				                $sam_balance=$tot_grey_req_qty-$sam_btq;
				                $sam_bal_qty=$sam_balance;
				                if($sam_bal_qty>0)
				                {
				                $color="";
				                $txt="Over Batch Qty";
				                }
				                else if($sam_bal_qty<0)
				                {
				                $color="red";
				                $txt="Below Batch Qty";
				                }
				                }
			                }
			        	 ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_sam_btq"><? echo number_format($sam_btq,2); ?></th>
			                <th width="50" align="right"><? echo number_format($total_samp_tot_batch_wgt,2); ?></th>
			                <th width="50" align="right"><? echo number_format($total_samp_tot_trims_wgt,2); ?></th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="20" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="20" align="left">&nbsp; <? echo number_format($sam_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="20" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
			 	</table>
			</div><br/>
		</div>
		<?
  	}

	/*
	|--------------------------------------------------------------------------
	| Self Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==1)
	{
		?>
	 	<div align="left"> <b>Self Batch </b></div>
	 	<table class="rpt_table" width="1990" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="75">Batch Date</th>
	                <th width="60">Batch No</th>
	                <th width="40">Ext. No</th>
	                <th width="80">Batch Against</th>
	                <th width="80">Batch Color</th>
	                <th width="80">Buyer</th>
	                <th width="80">Style Ref</th>
	                <th width="120">PO No</th>
                    <th width="100">Pub Ship date</th>
	                <th width="70">File No</th>
	                <th width="70">Ref. No</th>
	                <th width="80">W/O NO.</th>
	                 <th width="70">Job</th>
	                <th width="100">Construction</th>
	                <th width="150">Composition</th>
	                <th width="50">Dia/ Width</th>
	                <th width="50">GSM</th>
	                <th width="60">Lot No</th>
	                <th width="70">Batch Qty.</th>
	                <th width="50">Batch Weight</th>
	                <th width="50">Trims Weight</th>
					<th width="50">Total Roll</th>
	                <th width="100">Grey Req.Qty</th>
	                <th width="100">Remarks</th>
	                <th>Process Name</th>
	            </tr>
	        </thead>
	 	</table>
		<div style=" max-height:350px; width:1990px; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" id="table_body" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
		 		<tbody>
				<?
                $booking_qnty_arr=array();
                $query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
                foreach($query as $row)
                {
                    $booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
                }

                $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($smn_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                foreach($sam_query as $row)
                {
                    $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                }
                $i=1;
                $f=0;
                $b=0;
                $btq=0;
                $tot_book_qty=0;
                $tot_grey_req_qty=0;
                $batch_chk_arr=array();$booking_chk_arr=array();
                foreach($batchdata as $batch)
                {
                    $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];
                    //if (!in_array($batch[csf('booking_no')],$sam_booking_arr))
                    if($sam_booking!=$batch[csf('booking_no')])
                        {
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $order_id=$batch[csf('po_id')];
                            $order_id_ex = array_unique(explode(",", $order_id));
                            $order_id = implode(",", $order_id_ex);
                            $color_id=$batch[csf('color_id')];
                            $booking_no=$batch[csf('booking_no')];
                            $booking_qty=$booking_qnty_arr[$order_id][$booking_no][$color_id];
                            $desc=explode(",",$batch[csf('item_description')]);
                            $entry_form=$batch[csf('entry_form')];
                            $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
                            $po_num='';	$po_file='';$po_ref='';$job_num='';$job_buyers='';$yarn_lot_num='';
                            $grey_booking_qty=0;$buyer_style='';$ship_DateCond='';
                            foreach($po_ids as $p_id)
                            {
                                if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
                                if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
                                if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
                                if($job_num=='') $job_num=$po_array[$p_id]['job_no'];else $job_num.=",".$po_array[$p_id]['job_no'];
                                if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
                                $ylot=rtrim($yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
                                if($yarn_lot_num=='') $yarn_lot_num=$ylot;else $yarn_lot_num.=",".$ylot;
                                $grey_booking_qty+=$booking_qnty_arr[$p_id][$booking_no][$color_id];
                                if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
								$pub_shipment_date=$po_array[$p_id]['pub_shipment_date'];
								if($ship_DateCond=='') $ship_DateCond=$pub_shipment_date;else $ship_DateCond.=",".$pub_shipment_date;
                            }
                            $yarn_lots=implode(",",array_unique(explode(",",$yarn_lot_num)));
                            // $po_number=$po_array[$batch[csf('po_id')]]['no'];//implode(",",array_unique(explode(",",$batch[csf('po_number')])));
                            // $book_qty+=$booking_qty;
                            $booking_color=$order_id.$booking_no.$color_id;
                            if (!in_array($booking_color,$booking_chk_arr))
                            {
                                $b++;
                                $booking_chk_arr[]=$booking_color;
                                $tot_book_qty=$grey_booking_qty;
                            }
                            else
                            {
                                $tot_book_qty=0;
                            }

                           //echo  $book_qty;


                            $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
                           ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                            <? if (!in_array($batch[csf('id')],$batch_chk_arr) )
                            {
                                $f++;
                                ?>
                                <td width="30" style="word-break:break-all;"><? echo $f; ?></td>
                                <td align="center" width="75" style="word-break:break-all;" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                                <td width="60" style="word-break:break-all;" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                                <td  width="40" style="word-break:break-all;" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                                 <td  width="80" style="word-break:break-all;"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $color_library[$batch[csf('color_id')]]; ?></div></td>
                                <td width="80" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $job_buyers;//; ?></div></td>
                                <?
                                $batch_chk_arr[]=$batch[csf('id')];
                               // $book_qty+=$booking_qty;
                            }
                            else
                            {
                                ?>
                                <td width="30" style="word-break:break-all;"><? //echo $sl; ?></td>
                                <td width="75" style="word-break:break-all;"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
                                <td width="60" style="word-break:break-all;"><p><? //echo $booking_qty; ?></p></td>
                                <td width="40" style="word-break:break-all;"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                                <td width="80" style="word-break:break-all;"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
                                <?
                            }
                            ?>
                            <td width="80" style="word-break:break-all;"><p><? echo $buyer_style; ?></p></td>
                            <td width="120" style="word-break:break-all;"><p><? echo $po_num;//$po_array[$batch[csf('po_id')]]['no']; ?></p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $ship_DateCond; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo $po_file; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><?  echo $po_ref; ?></p></td>
                            <td width="80" style="word-break:break-all;"><p><? echo $batch[csf('booking_no')]; ?></p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo $job_num; ?></p></td>
                            <td width="100" style="word-break:break-all;"><div style="width:80px; word-wrap:break-word;"><? echo $desc[0]; ?></div></td>
                            <td width="150" style="word-break:break-all;"><div style="width:150px; word-wrap:break-word;"><?
                            if($desc[4]!="")
                            {
                            	$compositions= $desc[1].' ' . $desc[2];
                            	$gsms= $desc[3];
                            }
                            else
                            {
                            	$compositions= $desc[1];
                            	$gsms= $desc[2];
                            }
                            echo $compositions;

                            ?></div></td>
                            <td width="50" style="word-break:break-all;"><p><? echo end($desc);//$desc[3]; ?></p></td>
                            <td width="50" style="word-break:break-all;"><p><? echo $gsms;//$desc[2]; ?></p></td>
                            <td style="word-break:break-all;" title="<? echo 'Prod Id'.$batch[csf('prod_id')].'=PO ID'.$order_id;?>" align="left" width="60"><div style="width:60px; word-wrap:break-word;"><? echo $yarn_lots; ?></div></td>
                            <td align="right" width="70" style="word-break:break-all;" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
                            <td align="right" width="50" style="word-break:break-all;" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
                            <td align="right" width="50" style="word-break:break-all;" title="<? echo $batch[csf('total_trims_weight')]; ?>"><? echo number_format($batch[csf('total_trims_weight')],2); ?></td>
							<td width="50" align="right" style="word-break:break-all;"><? echo $batch[csf('roll_no')];?></td>
                            <td align="right" width="100" style="word-break:break-all;"><? echo number_format($tot_book_qty,2);?></td>
                            <td width="100" style="word-break:break-all;"><? echo $batch[csf('')];?></td>
                            <td style="word-break:break-all;"><? echo $process_name;?></td>
                            </tr>
                            <?
                            $i++;
                            $btq+=$batch[csf('batch_qnty')];
                            $tot_grey_req_qty+=$tot_book_qty;
                            $balance=$tot_grey_req_qty-$btq;
                            $bal_qty=$balance;
                            if($bal_qty>0)
                            {
                                $color="";
                                $txt="Over Batch Qty";
                            }
                            else if($bal_qty<0)
                            {
                                $color="red";
                                $txt="Below Batch Qty";
                            }
                        }
                    }
                 ?>
		 		</tbody>
			</table>
			<table class="rpt_table" width="1970" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <tr>
		                <th width="30">&nbsp;</th>
		                <th width="75">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="40">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>

		                <th width="70">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="150">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="70" id="value_total_btq"><? echo number_format($btq,2); ?></th>
		                <th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
		                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
		                <th width="100">&nbsp;</th>
		                <th>&nbsp;</th>
		            </tr>
		            <tr>
		                <td colspan="13" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
		                <td colspan="12" align="left">&nbsp;
		                 <? echo number_format($tot_grey_req_qty,2); ?>
		                </td>
		            </tr>
		             <tr>
		                <td colspan="13" align="right" style="border:none;"> <b>Batch Qty.</b></td>
		                <td colspan="12" align="left">&nbsp; <? echo number_format($btq,2); ?> </td>
		            </tr>
		             <tr>
		                <td colspan="13" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
		                <td colspan="12" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
		            </tr>
		        </tfoot>
			</table>
		</div><br/>
		<?
	}
	/*
	|--------------------------------------------------------------------------
	| Subcon Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==2)
	{
		?>
		<div align="left"> <b>SubCond Batch</b></div>
	 	<table class="rpt_table" width="1650" id="table_header_1" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="75">Batch Date</th>
	                <th width="60">Batch No</th>
	                <th width="40">Ext. No</th>
	                <th width="80">Batch Aganist</th>
	                <th width="80">Batch Color</th>
	                <th width="50">Buyer</th>
	                <th width="80">Style Ref</th>
	                <th width="120">PO No</th>
	                <th width="80">W/O NO.</th>
	                <th width="70">Job</th>
	                <th width="80">Recv. Challan</th>
	                <th width="150">Fabrics Desc.</th>
	                <th width="50">Dia/ Width</th>
	                <th width="50">GSM</th>
	                <th width="60">Lot No</th>
	                <th width="70">Batch Qty.</th>
	                <th width="50">Batch Weight</th>
					<th width="50">Total Roll</th>
	                <th width="100">Grey Req. Qty</th>
	                <th width="100">Remarks</th>
	                <th>Process Name</th>
	            </tr>
	        </thead>
		</table>
		<div style=" max-height:350px; width:1650px; overflow-y:scroll;" id="scroll_body">
			<table class="rpt_table" id="table_body2" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tbody>
					<?
					/*$booking_qnty_arr=array();
					$query=sql_select("select b.po_break_down_id, b.fabric_color_id,a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no,b.fabric_color_id");
					foreach($query as $row)
					{
						$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
					}*/


					$sub_material_recv_arr=array();$sub_material_description_arr=array();
					$subconsql=sql_select("select b.order_id as po_break_down_id,b.gsm,b.material_description, b.color_id,a.chalan_no, sum(b.quantity ) as grey_fab_qnty from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in(1,2,3,13,14) and a.trans_type=1  and a.is_deleted=0 and a.status_active=1  and b.status_active=2 and b.is_deleted=0 group by b.order_id, a.chalan_no,b.color_id,a.trans_type,b.material_description,b.gsm");
					foreach($subconsql as $row)
					{
						$sub_material_recv_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['desc']=$row[csf('material_description')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
						$sub_material_description_arr[$row[csf('po_break_down_id')]][$row[csf('chalan_no')]]['gsm']=$row[csf('gsm')];
					}

					//var_dump($sub_material_description_arr);die;
					$i=1;
					$f=0;
					$btq=0; $k=0;
					$book_qty_subcon=0;$subcon_tot_book_qty=0;
					$batch_chk_arr=array();$sub_qty_chk_arr=array();
					 //print_r($sub_batchdata);
					foreach($sub_batchdata as $batch)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$order_id_sub=$batch[csf('po_id')];
						$color_id=$batch[csf('color_id')];
						$booking_no=$batch[csf('booking_no')];
						$sub_challan=$batch[csf('rec_challan')];
						$subcon_booking_qty=$sub_material_recv_arr[$order_id_sub];//$booking_qnty_arr[$order_id][$booking_no][$color_id];
						$item_descript=$sub_material_description_arr[$order_id_sub][$sub_challan]['desc'];
						$gsm_subcon=$sub_material_description_arr[$order_id_sub][$sub_challan]['gsm'];
						$desc=explode(",",$batch[csf('item_description')]);
						$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));

						$entry_form=$batch[csf('entry_form')];

						$po_ids=array_unique(explode(",",$batch[csf('po_id')]));
						$sub_po_num='';	$sub_job_buyers='';$sub_job_buyers='';
						$subcon_booking_qty=0;$sub_buyer_style='';
						foreach($po_ids as $p_id)
						{
							if($sub_po_num=='') $sub_po_num=$sub_po_array[$p_id]['po_no'];else $sub_po_num.=",".$sub_po_array[$p_id]['po_no'];
							if($sub_job_num=='') $sub_job_num=$sub_po_array[$p_id]['job_no'];else $sub_job_num.=",".$sub_po_array[$p_id]['job_no'];
							if($sub_job_buyers=='') $sub_job_buyers=$buyer_arr[$sub_po_array[$p_id]['buyer']];else $sub_job_buyers.=",".$buyer_arr[$sub_po_array[$p_id]['buyer']];
							$subcon_booking_qty+=$sub_material_recv_arr[$p_id];
							//for yarn lot
							$subconYlot=rtrim($subcon_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
							if($subcon_yarn_lot_num=='')
								$subcon_yarn_lot_num=$subconYlot;
							else
								$subcon_yarn_lot_num.=",".$subconYlot;
							if($sub_buyer_style=="") $sub_buyer_style=$sub_po_array[$p_id]['style_no']; else $sub_buyer_style.=','.$sub_po_array[$p_id]['style_no'];
						}

						$subcon_yarn_lots=implode(",",array_unique(explode(",",$subcon_yarn_lot_num)));
						$booking_color2=$order_id_sub.$batch[csf('booking_no')].$batch[csf('color_id')];
						if (!in_array($booking_color2,$sub_qty_chk_arr))
						{ $k++;

							//echo $subcon_booking_qty;
							 $sub_qty_chk_arr[]=$booking_color2;
							  $subcon_tot_book_qty=$subcon_booking_qty;
						}
						else
						{
							 $subcon_tot_book_qty=0;
						}

						$process_name = '';
						$process_id_array = explode(",", $batch[csf("process_id")]);
						foreach ($process_id_array as $val)
						{
							if ($process_name == ""){
								$process_name = $conversion_cost_head_array[$val];
							}
							else{
								$process_name .= "," . $conversion_cost_head_array[$val];
							}
						}
						?>
			            <tr bgcolor="<? echo $bgcolor; ?>" id="trd_<? echo $i; ?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor; ?>')">
			            	<? if (!in_array($batch[csf('id')],$batch_chk_arr) )
							{
								$f++;
										?>
				                <td width="30"><? echo $f; ?></td>
				                <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
				                <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
				                <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
				                 <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
				                <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td width="50"><p><? echo $sub_job_buyers; ?></p></td>
								<?
				                $batch_chk_arr[]=$batch[csf('id')];

			                }
							else
							{ ?>
				                <td width="30"><? //echo $sl; ?></td>
				                <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
				                <td   width="60"><p><? //echo $booking_qty; ?></p></td>
				                <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
				                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
				                <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
								<?
							}
							?>
							<td width="80"><p><? echo $sub_buyer_style; ?></p></td>
			                <td width="120"><p><? echo $sub_po_num; ?></p></td>
			                <td width="80"><p><? echo $batch[csf('booking_no')]; ?></p></td>
			                <td width="70"><p><? echo $sub_job_num; ?></p></td>
			                <td width="80" ><p><? echo $batch[csf('rec_challan')]; ?></p></td>
			                <td width="150" ><p><? echo $item_descript; ?></p></td>
			                <td  width="50" title="<? echo $desc[2];  ?>"><p><? echo $desc[3]; ?></p></td>
			                <td  width="50"><p><? echo $gsm_subcon; ?></p></td>
			                <td align="left" width="60"><p><? echo $subcon_yarn_lots;//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id_sub]['lot']; ?></p></td>
			                <td align="right" width="70" ><? echo number_format($batch[csf('sub_batch_qnty')],2);  ?></td>
			                <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
							<td width="50" align="right"><? echo $batch[csf('roll_no')]; ?></td>
			                <td width="100"><? echo number_format($subcon_tot_book_qty,2); ?></td>
			                <td width="100"><? echo $batch[csf('remarks')]; ?></td>
			                <td><? echo $process_name; ?></td>
			            </tr>
						<?
		                $i++;
		                $btq_subcon+=$batch[csf('sub_batch_qnty')];
						$book_qty_subcon+=$subcon_tot_book_qty;
		                $balance=$book_qty_subcon-$btq_subcon;
		                $bal_qty_subcon=$balance;
		                if($bal_qty_subcon>0)
		                {
		                $color="";
		                $txt="Over Batch Qty";
		                }
		                else if($bal_qty_subcon<0)
		                {
		                $color="red";
		                $txt="Below Batch Qty";
		                }
		            }
		        	 ?>
		       </tbody>
			</table>
			<table class="rpt_table" width="1630" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <tr>
		                <th width="30">&nbsp;</th>
		                <th width="75">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="40">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="120">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>

		                <th width="150">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="50">&nbsp;</th>
		                <th width="60">&nbsp;</th>
		                <th width="70" id="value_total_btq_subcon"><? echo $btq_subcon; ?></th>
		                <th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
		                <th width="100"><? //echo $book_qty_subcon; ?></th>
		                <th width="100">&nbsp;</th>
		                <th>&nbsp;</th>
		            </tr>
		            <tr>
		                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
		                <td colspan="9" align="left">&nbsp;
		                 <? echo number_format($book_qty_subcon,2); ?>
		                </td>
		            </tr>
		             <tr>
		                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
		                <td colspan="9" align="left">&nbsp; <? echo number_format($btq_subcon,2); ?> </td>
		            </tr>
		             <tr>
		                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
		                <td colspan="9" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty_subcon,2); ?> &nbsp;&nbsp;</font></b> </td>
		            </tr>
		        </tfoot>
			</table>
		</div>
		<?
	}

	/*
	|--------------------------------------------------------------------------
	| Sample Batch
	|--------------------------------------------------------------------------
	|
	*/
	if($batch_type==3)
	{
		if($cbo_type==1)
		{
			?>
			<div align="left"> <b>Sample Batch </b></div>
			<table class="rpt_table" id="table_header_1" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="75">Batch Date</th>
                        <th width="60">Batch No</th>
                        <th width="40">Ext. No</th>
                        <th width="80">Batch Aganist</th>
                        <th width="80">Batch Color</th>
                        <th width="50">Buyer</th>
                        <th width="80">Style Ref</th>
                        <th width="120">PO No</th>
                        <th width="70">File No</th>
                        <th width="70">Ref No</th>
                        <th width="100">W/O NO.</th>
                        <th width="70">Job</th>
                        <th width="100">Construction</th>
                        <th width="150">Composition</th>
                        <th width="50">Dia/ Width</th>
                        <th width="50">GSM</th>
                        <th width="60">Lot No</th>
                        <th width="70">Batch Qty.</th>
                        <th width="50">Batch Weight</th>
                        <th width="50">Trims Weight</th>
						<th width="50">Total Roll</th>
                        <th width="100">Grey Req.Qty</th>
                        <th width="100">Remarks</th>
                        <th>Process Name</th>
                    </tr>
                </thead>
			</table>
			<div style=" max-height:350px; width:1880px; overflow-y:scroll;" id="scroll_body">
			 	<table class="rpt_table" id="table_body3" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			 		<tbody>
					<?
                    $sam_booking_qnty_arr=array();
                    $sam_query=sql_select("select b.po_break_down_id,a.booking_no, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.booking_no, b.fabric_color_id");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
                    }

                    $smn_query=sql_select("select booking_no from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($smn_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }
                    $sam_query=sql_select("select booking_no from wo_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
                    foreach($sam_query as $row)
                    {
                        $sam_booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
                    }


                    $i=1;
                    $f=0;
                    $b=0;
                    $btq=0;
                    $tot_book_qty2=0;
                    $tot_grey_req_qty=0;
                    $batch_chk_arr=array();$booking_chk_arr2=array();
                    // print_r($sam_batchdata );
                    foreach($sam_batchdata as $batch)
                    {
                        $sam_booking=$sam_booking_arr[$batch[csf('booking_no')]];

                        if($sam_booking==$batch[csf('booking_no')])
                        {
	                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                        $po_ids=array_unique(explode(",",$batch[csf('po_id')]));
	                        $po_num='';	$po_file='';$po_ref='';$samp_job_num='';$job_buyers='';$sam_booking_qty=0;$buyer_style='';
	                        foreach($po_ids as $p_id)
	                        {
	                            if($po_num=='') $po_num=$po_array[$p_id]['po_no'];else $po_num.=",".$po_array[$p_id]['po_no'];
	                            if($po_file=='') $po_file=$po_array[$p_id]['file'];else $po_file.=",".$po_array[$p_id]['file'];
	                            if($po_ref=='') $po_ref=$po_array[$p_id]['ref'];else $po_ref.=",".$po_array[$p_id]['ref'];
	                            if($samp_job_num=='') $samp_job_num=$po_array[$p_id]['job_no'];else $samp_job_num.=",".$po_array[$p_id]['job_no'];
	                            if($job_buyers=='') $job_buyers=$buyer_arr[$po_array[$p_id]['buyer']];else $job_buyers.=",".$buyer_arr[$po_array[$p_id]['buyer']];
	                            $sam_booking_qty+=$sam_booking_qnty_arr[$p_id][$booking_no][$color_id];
	                            //for yarn lot
	                            $sampleYlot=rtrim($sample_yarn_lot_arr[$batch[csf('id')]][$batch[csf('prod_id')]][$p_id]['lot'],',');
	                            if($sample_yarn_lot_num=='')
	                                $sample_yarn_lot_num=$sampleYlot;
	                            else
	                                $sample_yarn_lot_num.=",".$sampleYlot;
	                            if($buyer_style=="") $buyer_style=$po_array[$p_id]['style_no']; else $buyer_style.=','.$po_array[$p_id]['style_no'];
	                        }

	                        $sample_yarn_lots=implode(",",array_unique(explode(",",$sample_yarn_lot_num)));
	                        $order_id=$batch[csf('po_id')];
	                        $color_id=$batch[csf('color_id')];
	                        $booking_no=$batch[csf('booking_no')];
	                        //$sam_booking_no = $sam_booking_arr[$booking_no]['booking_no'];

	                        $desc=explode(",",$batch[csf('item_description')]);
	                        $entry_form=$batch[csf('entry_form')];
	                        //$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')])));
	                        //if($po_file=='') $po_file=$po_array[$order_id]['file'];else $po_file.=",".$po_array[$order_id]['file'];
	                        //if($po_ref=='') $po_ref=$po_array[$order_id]['ref'];else $po_ref.=",".$po_array[$order_id]['ref'];
	                        //$po_file=$po_array[$order_id]['file'];
	                        //$po_ref=$po_array[$order_id]['ref'];
	                        // $book_qty+=$booking_qty;.$batch[csf('booking_no')].$batch[csf('color_id')].$batch[csf('prod_id')]
	                        $booking_color=$order_id.$booking_no.$color_id;
	                        if (!in_array($booking_color,$booking_chk_arr2))
	                        {
	                            $b++;
	                            $booking_chk_arr2[]=$booking_color;
	                            $tot_book_qty2=$sam_booking_qty;
	                        }
	                        else
	                        {
	                            $tot_book_qty2=0;
	                        }
	                        //echo  $batch[csf('po_id')].', ';

	                        $process_name = '';
							$process_id_array = explode(",", $batch[csf("process_id")]);
							foreach ($process_id_array as $val)
							{
								if ($process_name == ""){
									$process_name = $conversion_cost_head_array[$val];
								}
								else{
									$process_name .= "," . $conversion_cost_head_array[$val];
								}
							}
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr3_<? echo $i; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor; ?>')">
		                        <?
		                        if (!in_array($batch[csf('id')],$booking_chk_arr2))
		                        {
		                            $f++;
		                            ?>
		                            <td width="30"><? echo $f; ?></td>
		                            <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
		                            <td  width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
		                            <td  width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
		                            <td width="80"><p><? echo $batch_against[$batch[csf('batch_against')]]; ?></p></td>
		                            <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td width="50"><p><? echo $buyer_arr[$non_order_arr[$batch[csf('booking_no')]]['buyer_name']]; ?></p></td>
		                            <?
		                            $batch_chk_arr[]=$batch[csf('id')];
		                            // $book_qty+=$booking_qty;
		                        }
	                            else
	                            { ?>
		                            <td width="30"><? //echo $sl; ?></td>
		                            <td   width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
		                            <td   width="60"><p><? //echo $booking_qty; ?></p></td>
		                            <td   width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
		                            <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
		                            <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
		                            <?
		                        }
								$samp_ref_no= $non_order_arr[$batch[csf('booking_no')]]['samp_ref_no'];
								$booking_without_order=$batch[csf('booking_without_order')];
	                            ?>
	                            <td width="80" title="<? echo $booking_without_order; ?>"><p><? if($booking_without_order==1) echo $style_ref_no_lib[$non_order_arr[$batch[csf('booking_no')]]['style_id']]; else echo $buyer_style; ?></p></td>
	                            <td width="120" ><p><? if($batch[csf('po_id')]>1) echo $po_num;else echo ""; ?></p></td>
	                            <td width="70"><p><? echo $po_file; ?></p></td>
	                             <td width="70"><p><? if($booking_without_order==1) echo $samp_ref_no;else echo $po_ref; ?></p></td>
	                            <td width="100"><p><? echo $batch[csf('booking_no')]; ?></p></td>
	                            <td width="70" title="poID=<? echo $batch[csf('po_id')]; ?>"><p><? if($batch[csf('po_id')]>1) echo $samp_job_num;else echo ""; ?></p></td>
	                            <td width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0];//$batch[csf('grey_dia')];; ?></p></td>
	                            <td width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
	                            <td  width="50" title="<? echo $desc[3];  ?>"><p><? echo $desc[3]; ?></p></td>
	                            <td  width="50" title="<? echo $desc[2]; ?>"><p><? echo $desc[2]; ?></p></td>
	                             <td align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $sample_yarn_lots; //$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]['lot']; ?></p></td>
	                            <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo number_format($batch[csf('batch_qnty')],2);  ?></td>
	                            <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo number_format($batch[csf('batch_weight')],2); ?></td>
	                            <td  align="right" width="50" title="<? echo $batch[csf('total_trims_weight')]; ?>"><? echo number_format($batch[csf('total_trims_weight')],2); ?></td>
								<td width="50" align="right"><? echo $batch[csf('roll_no')];?></td>
	                            <td align="right" width="100"><? echo number_format($tot_book_qty2,2);?></td>
	                            <td width="100"><? echo $batch[csf('remarks')];?></td>
	                            <td><p><? echo $process_name;?></p></td>
	                        </tr>
	                        <?
	                        $i++;
	                        $sam_btq+=$batch[csf('batch_qnty')];
	                        $tot_grey_req_qty+=$tot_book_qty2;
	                        $sam_balance=$tot_grey_req_qty-$sam_btq;
	                        $sam_bal_qty=$sam_balance;
	                        if($sam_bal_qty>0)
	                        {
	                        	$color="";
	                        	$txt="Over Batch Qty";
	                        }
	                        else if($sam_bal_qty<0)
	                        {
		                        $color="red";
		                        $txt="Below Batch Qty";
	                        }
                        }
                    }
                    ?>
			       </tbody>
				</table>
				<table class="rpt_table" width="1860" cellpadding="0" cellspacing="0" border="1" rules="all">
			        <tfoot>
			            <tr>
			                <th width="30">&nbsp;</th>
			                <th width="75">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="70">&nbsp;</th>
			                <th width="100">&nbsp;</th>
			                <th width="150">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="50">&nbsp;</th>
			                <th width="60">&nbsp;</th>
			                <th width="70" id="value_total_sam_btq"><? echo number_format($sam_btq,2); ?></th>
			                <th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="50">&nbsp;</th>
			                <th width="100"><? //echo number_format($tot_grey_req_qty,2); ?></th>
			                <th width="100">&nbsp;</th>
			                <th>&nbsp;</th>
			            </tr>
			            <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
			                <td colspan="10" align="left">&nbsp;
			                 <? echo number_format($tot_grey_req_qty,2); ?>
			                </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"> <b>Batch Qty.</b></td>
			                <td colspan="10" align="left">&nbsp; <? echo number_format($sam_btq,2); ?> </td>
			            </tr>
			             <tr>
			                <td colspan="12" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
			                <td colspan="10" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($sam_bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
			            </tr>
			        </tfoot>
			 	</table>
			</div><br/>
		 	<?
		}
  	}
   	?>
	</div>
	</fieldset>
	</div>
	<?

	/*$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$total_data####$filename####$batch_type"; */

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
	echo "$total_data####$filename####$batch_type";

	disconnect($con);
	exit();
}	//BatchReport
?>