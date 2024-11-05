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
$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");

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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'trims_batch_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
	$floor_no = str_replace("'","",$cbo_floor);
	// echo $cbo_batch_type;die;
	/*echo $floor_no;die;*/
	$batch_number_hidden = str_replace("'","",$batch_number);
	$booking_no = str_replace("'","",$txt_booking_no);
	$booking_number_hidden = str_replace("'","",$txt_hide_booking_id);

	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order = str_replace("'","",$hidden_order_no);
	$cbo_type = str_replace("'","",$cbo_type);
	// echo $cbo_type;die;
	$year_id = str_replace("'","",$cbo_year);
	$file_no = str_replace("'","",$file_no);
	$ref_no = str_replace("'","",$ref_no);
	//echo $order_no;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$process=33;
	//echo str_replace("'","",$batch_no);die;
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(c.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(c.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";
	}
	
	ob_start();
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
		if ($job_number_id=="") $jobdata3=""; else $jobdata3="  and c.job_no_prefix_num in($job_number_id)";
	}
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
			$dates_com2="and d.batch_date BETWEEN '$date_from' AND '$date_to'";
			$dates_com3="and b.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}
	// echo $jobdata;
    if($cbo_type==1) //Date Wise Report
	{
		if($batch_type==0) // All
		{
		 // ================================= Self Batch Start ===============================================
			$sql_self_batch="SELECT    a.id,a.batch_date,a.batch_no,
					a.color_id,
					b.item_description,
					b.trims_wgt_qnty,
					a.batch_weight,
					b.remarks,
					
					a.booking_no,
					a.process_id,
					a.batch_against,
					a.entry_form
					FROM  pro_batch_create_mst a,pro_batch_trims_dtls b,wo_po_details_master c
					WHERE     
					 a.id = b.mst_id AND
					 a.job_no=c.job_no AND
					a.entry_form = 136
					AND a.batch_against != 3
					
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND a.status_active = 1
					AND c.status_active = 1
					AND a.is_deleted = 0
					$comp_cond $working_comp_cond $jobdata3 $jobdata3 $year_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond
					 GROUP BY a.id,
					a.batch_date,
					a.batch_no,
					a.color_id,
					b.item_description,
					b.trims_wgt_qnty,
					a.batch_weight,
					b.remarks,
					
					a.booking_no,
					a.process_id,
					a.batch_against,
					a.entry_form";
					// echo $sql_self_batch;
	
					$sql_self_batch_result=sql_select($sql_self_batch);
					$self_batch_data_array = array();
					foreach($sql_self_batch_result as $row){
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_no'] = $row[csf('batch_no')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['color_id'] = $row[csf('color_id')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['item_description'] = $row[csf('item_description')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['trims_wgt_qnty'] = $row[csf('trims_wgt_qnty')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_weight'] += $row[csf('batch_weight')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['remarks'] = $row[csf('remarks')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_date'] = change_date_format($row[csf('batch_date')]);

					}
					// echo "<pre>";
					// print_r($self_batch_data_array);
                    // ================================= Order Query ===============================================
					$order_array=array(); 
	                $order_sql="SELECT 
					a.job_no,
					a.buyer_name,
					a.style_ref_no,
					c.mst_id       AS batch_id,
					d.batch_no,
					c.item_description,
					d.batch_date
			        FROM wo_po_details_master  a,
					pro_batch_trims_dtls  c,
					pro_batch_create_mst  d
			        WHERE     a.job_no = d.job_no
					AND d.id = c.mst_id
					AND d.entry_form = 136
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND c.status_active IN (1)
					AND c.is_deleted = 0
					AND d.status_active IN (1)
					AND d.is_deleted = 0
                    $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2";
					// echo $order_sql;
					$order_sql_result=sql_select($order_sql);
					$order_array=array(); 
					foreach($order_sql_result as $row)
					{
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['style_ref_no']=$row[csf('style_ref_no')];
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['job_no']=$row[csf('job_no')];
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['buyer_name']=$row[csf('buyer_name')];
		
						$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
					} 
                //    echo "<pre>";
				//    print_r($order_array);
				 // ================================= Booking Query ===============================================
				    $booking_sql="SELECT a.booking_no,b.batch_no,c.item_description 
					FROM wo_booking_mst a, pro_batch_create_mst b, pro_batch_trims_dtls c
					WHERE     a.job_no = b.job_no
					AND b.id = c.mst_id
					AND a.booking_type = 1
					AND a.is_short = 2
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND c.status_active = 1
					AND c.is_deleted = 0
					  $dates_com3";
					// echo $order_sql;
					$booking_sql_result=sql_select($booking_sql);

					$booking_array=array(); 
					foreach($booking_sql_result as $row)
					{
						$booking_array[$row[csf('batch_no')]][$row[csf('item_description')]]['booking_no']=$row[csf('booking_no')];
					} 
                   //  echo "<pre>";
				   //  print_r($booking_array);
				   // ================================= Trim Pre-cost Query ===============================================
				   $trim_pre_cost_sql="SELECT a.trim_group,a.description,b.batch_no,c.item_description 
				   FROM  wo_pre_cost_trim_cost_dtls a,pro_batch_create_mst b, pro_batch_trims_dtls c
				     WHERE 
						a.job_no = b.job_no 
						AND b.id = c.mst_id 
						AND a.status_active = 1
						AND a.is_deleted = 0
						AND b.status_active = 1
						AND b.is_deleted = 0
						AND c.status_active = 1
						AND c.is_deleted = 0
					 $dates_com3";
				  
				//    echo $trim_pre_cost_sql;
				   $trim_pre_cost_sq_result=sql_select($trim_pre_cost_sql);
				   
				   $trim_pre_cost_array=array(); 
				   foreach($trim_pre_cost_sq_result as $row)
				   {
					   $trim_pre_cost_array[$row[csf('batch_no')]][$row[csf('item_description')]]['trim_group']=$row[csf('trim_group')];
					   $trim_pre_cost_array[$row[csf('batch_no')]][$row[csf('item_description')]]['description']=$row[csf('description')];
				   } 
				//   echo "<pre>";
				//   print_r($trim_pre_cost_array);
	
	
			?>
				<fieldset style="width:1570px">
					<table width="1570" cellpadding="0" cellspacing="0"> 
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo $company_library[$company]; ?><p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo $search_by_arr[$cbo_type]; ?><p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;">Trims Batch Report</p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to)); ?></p></td> 
						</tr>
					</table>
					<br />
					 <!-- ========= Details Part ======== -->
					 <table id="table_header_1" class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<P align="left" style="font-weight:bold;">Self Batch</p>
							<tr>
								<th width="30">SL</th>
								<th width="100">Batch Date</th>
								<th width="100">Batch No</th>
								<th width="80">Batch Color</th>
								<th width="80">Buyer</th>
								<th width="80">Style Ref</th>
								<th width="80">Booking Number</th> 
								<th width="100">Job</th>
								<th width="100">Item Group</th>
								<th width="150">Item Description</th>
								<th width="70">Batch Qty.</th>
								<th width="50">Batch Weight</th>
								<th width="100">Remarks</th>
						    </tr>
						</thead>
					</table>
					<table class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody id='scroll_body'>
						<?
							$k=1;
							foreach($self_batch_data_array as $batch_key=>$batch_value)
							{
								foreach($batch_value as $item_key=>$item_value)
							    {
									if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')"  id="tr_<? echo $k; ?>">
										<td width="30"><? echo $k; ?></td>
										<td width="100"><? echo  $item_value['batch_date']; ?></td>
										<td width="100"><? echo $item_value['batch_no'];?></td>
										<td width="80"><? echo $color_library[$item_value['color_id']];?></td>
										<td width="80"><? echo $buyer_arr[$order_array[$batch_key][$item_key]['buyer_name']]; ?></td>
										<td width="80"><? echo $order_array[$batch_key][$item_key]['style_ref_no']; ?></td>
										<td width="80"><? echo $booking_array[$batch_key][$item_key]['booking_no'];  ?></td> 
										<td width="100"><? echo $order_array[$batch_key][$item_key]['job_no']; ?></td>
										<td width="100">
											<? 
											$description = explode("-",$item_value['item_description']);
											echo $description['0']; 
											?>
										</td> 
										<td width="150">
											<?
												$description = explode("-",$item_value['item_description']);
												// echo"<pre>";
												// print_r($description);
												echo $description['1'];
										    ?>
										 </td>
										<td width="70" align="right"><? echo $item_value['trims_wgt_qnty'];?></td> 
										<td width="50" align="right"><? echo $item_value['batch_weight'];?></td> 
										<td width="100"><? echo $item_value['remarks'];?></td>
									</tr>
									<?
									$k++;
									$total_trims_wgt_qnty   += $item_value['trims_wgt_qnty'];
								}	
								
						    }
							
						    ?>
							<tr>
								<td width="150" colspan="10" align="right" style="font-weight:bold;">Total</td>
								<td width="70" align="right" style="font-weight:bold;"><? echo $total_trims_wgt_qnty; ?></td>
								<td width="50"></td>
								<td width="100"></td>
							</tr> 
						</tbody>                   
					</table>
				</fieldset>  
			<?

		 // ================================= Sample Batch Query ===============================================
		 $sql_sample_batch="SELECT    a.id,a.batch_date,a.batch_no,
		 a.color_id,
		 b.item_description,
		 b.trims_wgt_qnty,
		 a.batch_weight,
		 b.remarks,
		 
		 a.booking_no,
		 a.process_id,
		 a.batch_against,
		 a.entry_form
		 FROM  pro_batch_create_mst a,pro_batch_trims_dtls b
		 WHERE     a.entry_form = 136
		 AND a.batch_against = 3
		 AND a.id = b.mst_id
		 AND b.status_active = 1
		 AND b.is_deleted = 0
		 AND a.status_active = 1
		 AND a.is_deleted = 0
		 $comp_cond $working_comp_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond
		  GROUP BY a.id,
		 a.batch_date,
		 a.batch_no,
		 a.color_id,
		 b.item_description,
		 b.trims_wgt_qnty,
		 a.batch_weight,
		 b.remarks,
		 
		 a.booking_no,
		 a.process_id,
		 a.batch_against,
		 a.entry_form";
		 // echo $sql_self_batch;

		 $sql_sample_batch_result=sql_select($sql_sample_batch);
		 $sample_batch_data_array = array();
		 foreach($sql_sample_batch_result as $row){
			 $sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_no'] = $row[csf('batch_no')];
			 $sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['color_id'] = $row[csf('color_id')];
			 $sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['item_description'] = $row[csf('item_description')];
			 $sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['trims_wgt_qnty'] = $row[csf('trims_wgt_qnty')];
			 $sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_weight'] += $row[csf('batch_weight')];
			 $sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['remarks'] = $row[csf('remarks')];
			 $sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_date'] = change_date_format($row[csf('batch_date')]);

		 }
		 // echo "<pre>";
		 // print_r($sample_batch_data_array);
		 // ================================= Order Query ===============================================
		 $order_array=array(); 
		 $order_sql="SELECT 
		 a.job_no,
		 a.buyer_name,
		 a.style_ref_no,
		 c.mst_id       AS batch_id,
		 d.batch_no,
		 c.item_description,
		 d.batch_date
		 FROM wo_po_details_master  a,
		 pro_batch_trims_dtls  c,
		 pro_batch_create_mst  d
		 WHERE     a.job_no = d.job_no
		 AND d.id = c.mst_id
		 AND d.entry_form = 136
		 AND d.batch_against = 3
		 AND a.status_active = 1
		 AND a.is_deleted = 0
		 AND c.status_active IN (1)
		 AND c.is_deleted = 0
		 AND d.status_active IN (1)
		 AND d.is_deleted = 0
		 $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2";
		 // echo $order_sql;
		 $order_sql_result=sql_select($order_sql);

		 $order_array=array(); 
		 foreach($order_sql_result as $row)
		 {
			 $order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['style_ref_no']=$row[csf('style_ref_no')];
			 $order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['job_no']=$row[csf('job_no')];
			 $order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['buyer_name']=$row[csf('buyer_name')];

			 $batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		 } 
	 //    echo "<pre>";
	 //    print_r($order_array);
	  // ================================= Booking Query ===============================================
		 $booking_sql="SELECT a.booking_no,b.batch_no,c.item_description 
		 FROM wo_booking_mst a, pro_batch_create_mst b, pro_batch_trims_dtls c
		 WHERE     a.job_no = b.job_no
		 AND b.id = c.mst_id
		 AND a.booking_type = 4
		 AND a.is_short = 2
		 AND a.status_active = 1
		 AND a.is_deleted = 0
		 AND b.status_active = 1
		 AND b.is_deleted = 0
		 AND c.status_active = 1
		 AND c.is_deleted = 0
		   $dates_com3";
		 // echo $order_sql;
		 $booking_sql_result=sql_select($booking_sql);

		 $booking_array=array(); 
		 foreach($booking_sql_result as $row)
		 {
			 $booking_array[$row[csf('batch_no')]][$row[csf('item_description')]]['booking_no']=$row[csf('booking_no')];
		 } 
		//  echo "<pre>";
		//  print_r($booking_array);
		// ================================= Trim Pre-cost Query ===============================================
		$trim_pre_cost_sql="SELECT a.trim_group,a.description,b.batch_no,c.item_description 
		FROM  wo_pre_cost_trim_cost_dtls a,pro_batch_create_mst b, pro_batch_trims_dtls c
		  WHERE 
			 a.job_no = b.job_no 
			 AND b.id = c.mst_id 
			 AND a.status_active = 1
			 AND a.is_deleted = 0
			 AND b.status_active = 1
			 AND b.is_deleted = 0
			 AND c.status_active = 1
			 AND c.is_deleted = 0
		  $dates_com3";
	   
	 //    echo $trim_pre_cost_sql;
		$trim_pre_cost_sq_result=sql_select($trim_pre_cost_sql);
		
		$trim_pre_cost_array=array(); 
		foreach($trim_pre_cost_sq_result as $row)
		{
			$trim_pre_cost_array[$row[csf('batch_no')]][$row[csf('item_description')]]['trim_group']=$row[csf('trim_group')];
			$trim_pre_cost_array[$row[csf('batch_no')]][$row[csf('item_description')]]['description']=$row[csf('description')];
		} 
	 //   echo "<pre>";
	 //   print_r($trim_pre_cost_array);


		?>
			<fieldset style="width:1570px">
				<!-- ========= Details Part ======== -->
				<table id="table_header_1" class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<P align="left" style="font-weight:bold;">Sample Batch</p>
						<tr>
							<th width="30">SL</th>
							<th width="100">Batch Date</th>
							<th width="100">Batch No</th>
							<th width="80">Batch Color</th>
							<th width="80">Buyer</th>
							<th width="80">Style Ref</th>
							<th width="80">Booking Number</th> 
							<th width="100">Job</th>
							<th width="100">Item Group</th>
							<th width="150">Item Description</th>
							<th width="70">Batch Qty.</th>
							<th width="50">Batch Weight</th>
							<th width="100">Remarks</th>
						</tr>
					</thead>
				</table>
				<table class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
					<tbody id='scroll_body'>
					<?
						$k=1;
						foreach($sample_batch_data_array as $batch_key=>$batch_value)
						{
							foreach($batch_value as $item_key=>$item_value)
							{
								if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')"  id="tr_<? echo $k; ?>">
									<td width="30"><? echo $k; ?></td>
									<td width="100"><? echo  $item_value['batch_date']; ?></td>
									<td width="100"><? echo $item_value['batch_no'];?></td>
									<td width="80"><? echo $color_library[$item_value['color_id']];?></td>
									<td width="80"><? echo $buyer_arr[$order_array[$batch_key][$item_key]['buyer_name']]; ?></td>
									<td width="80"><? echo $order_array[$batch_key][$item_key]['style_ref_no']; ?></td>
									<td width="80"><? echo $booking_array[$batch_key][$item_key]['booking_no'];  ?></td> 
									<td width="100"><? echo $order_array[$batch_key][$item_key]['job_no']; ?></td>
									<td width="100">
										<? 
										$description = explode("-",$item_value['item_description']);
										echo $description['0']; 
										?>
									</td> 
									<td width="150">
										<?
											$description = explode("-",$item_value['item_description']);
											// echo"<pre>";
											// print_r($description);
											echo $description['1'];
										?>
									</td>
									<td width="70" align="right"><? echo $item_value['trims_wgt_qnty'];?></td> 
									<td width="50" align="right"><? echo $item_value['batch_weight'];?></td> 
									<td width="100"><? echo $item_value['remarks'];?></td>
								</tr>
								<?
								$k++;
								$total_sam_trims_wgt_qnty   += $item_value['trims_wgt_qnty'];
							}	
							
						}
						
						?>
						<tr>
							<td width="150" colspan="10" align="right" style="font-weight:bold;">Total</td>
							<td width="70" align="right" style="font-weight:bold;"><? echo $total_sam_trims_wgt_qnty; ?></td>
							<td width="50"></td>
							<td width="100"></td>
						</tr> 
					</tbody>                   
				</table>
			</fieldset>  
		<?

		}
		if($batch_type==1) // Self
		{
			// ================================= Self Batch Query ===============================================
			$sql_self_batch="SELECT    a.id,a.batch_date,a.batch_no,
					a.color_id,
					b.item_description,
					b.trims_wgt_qnty,
					a.batch_weight,
					b.remarks,
					
					a.booking_no,
					a.process_id,
					a.batch_against,
					a.entry_form
					FROM  pro_batch_create_mst a,pro_batch_trims_dtls b,wo_po_details_master c
					WHERE     a.id = b.mst_id AND c.job_no=a.job_no AND
					 a.entry_form = 136
					AND a.batch_against != 3
					
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND c.status_active = 1
					AND a.status_active = 1
					AND a.is_deleted = 0
					$comp_cond $working_comp_cond  $jobdata3 $year_cond $dates_com $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond
					 GROUP BY a.id,
					a.batch_date,
					a.batch_no,
					a.color_id,
					b.item_description,
					b.trims_wgt_qnty,
					a.batch_weight,
					b.remarks,
					
					a.booking_no,
					a.process_id,
					a.batch_against,
					a.entry_form";
					  // echo $sql_self_batch;
	
					$sql_self_batch_result=sql_select($sql_self_batch);
					$self_batch_data_array = array();
					foreach($sql_self_batch_result as $row){
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_no'] = $row[csf('batch_no')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['color_id'] = $row[csf('color_id')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['item_description'] = $row[csf('item_description')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['trims_wgt_qnty'] = $row[csf('trims_wgt_qnty')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_weight'] += $row[csf('batch_weight')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['remarks'] = $row[csf('remarks')];
						$self_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_date'] = change_date_format($row[csf('batch_date')]);

					}
					// echo "<pre>";
					// print_r($self_batch_data_array);
                    // ================================= Order Query ===============================================
					$order_array=array(); 
	                $order_sql="SELECT 
					a.job_no,
					a.buyer_name,
					a.style_ref_no,
					c.mst_id       AS batch_id,
					d.batch_no,
					c.item_description,
					d.batch_date
			        FROM wo_po_details_master  a,
					pro_batch_trims_dtls  c,
					pro_batch_create_mst  d
			        WHERE     a.job_no = d.job_no
					AND d.id = c.mst_id
					AND d.entry_form = 136
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND c.status_active IN (1)
					AND c.is_deleted = 0
					AND d.status_active IN (1)
					AND d.is_deleted = 0
                    $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2";
					// echo $order_sql;
					$order_sql_result=sql_select($order_sql);
	                // $po_sql=sql_select("select a.job_no_prefix_num 	as job_no,a.buyer_name,a.style_ref_no, b.file_no,b.grouping as ref,b.id, b.po_number,b.pub_shipment_date,c.mst_id as batch_id,d.is_sales,d.sales_order_id from wo_po_details_master a, wo_po_break_down b,pro_batch_create_dtls c,pro_batch_create_mst d where a.id=b.job_id and c.po_id=b.id and d.id=c.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0  and c.status_active in (1) and c.is_deleted=0 and d.status_active in (1) and d.is_deleted=0 $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2");
					$order_array=array(); 
					foreach($order_sql_result as $row)
					{
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['style_ref_no']=$row[csf('style_ref_no')];
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['job_no']=$row[csf('job_no')];
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['buyer_name']=$row[csf('buyer_name')];
		
						$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
					} 
                //    echo "<pre>";
				//    print_r($order_array);
				 // ================================= Booking Query ===============================================
				    $booking_sql="SELECT a.booking_no,b.batch_no,c.item_description 
					FROM wo_booking_mst a, pro_batch_create_mst b, pro_batch_trims_dtls c
					WHERE     a.job_no = b.job_no
					AND b.id = c.mst_id
					AND a.booking_type = 1
					AND a.is_short = 2
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND c.status_active = 1
					AND c.is_deleted = 0
					  $dates_com3";
					// echo $order_sql;
					$booking_sql_result=sql_select($booking_sql);

					$booking_array=array(); 
					foreach($booking_sql_result as $row)
					{
						$booking_array[$row[csf('batch_no')]][$row[csf('item_description')]]['booking_no']=$row[csf('booking_no')];
					} 
                   //  echo "<pre>";
				   //  print_r($booking_array);
				   // ================================= Trim Pre-cost Query ===============================================
				   $trim_pre_cost_sql="SELECT a.trim_group,a.description,b.batch_no,c.item_description 
				   FROM  wo_pre_cost_trim_cost_dtls a,pro_batch_create_mst b, pro_batch_trims_dtls c
				     WHERE 
						a.job_no = b.job_no 
						AND b.id = c.mst_id 
						AND a.status_active = 1
						AND a.is_deleted = 0
						AND b.status_active = 1
						AND b.is_deleted = 0
						AND c.status_active = 1
						AND c.is_deleted = 0
					 $dates_com3";
				  
				//    echo $trim_pre_cost_sql;
				   $trim_pre_cost_sq_result=sql_select($trim_pre_cost_sql);
				   
				   $trim_pre_cost_array=array(); 
				   foreach($trim_pre_cost_sq_result as $row)
				   {
					   $trim_pre_cost_array[$row[csf('batch_no')]][$row[csf('item_description')]]['trim_group']=$row[csf('trim_group')];
					   $trim_pre_cost_array[$row[csf('batch_no')]][$row[csf('item_description')]]['description']=$row[csf('description')];
				   } 
				//   echo "<pre>";
				//   print_r($trim_pre_cost_array);
	
	
			?>
				<fieldset style="width:1570px">
					<table width="1570" cellpadding="0" cellspacing="0"> 
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo $company_library[$company]; ?><p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo $search_by_arr[$cbo_type]; ?><p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;">Trims Batch Report</p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to)); ?></p></td> 
						</tr>
					</table>
					<br />
					 <!-- ========= Details Part ======== -->
					 <table id="table_header_1" class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<P align="left" style="font-weight:bold;">Self Batch</p>
							<tr>
								<th width="30">SL</th>
								<th width="100">Batch Date</th>
								<th width="100">Batch No</th>
								<th width="80">Batch Color</th>
								<th width="80">Buyer</th>
								<th width="80">Style Ref</th>
								<th width="80">Booking Number</th> 
								<th width="100">Job</th>
								<th width="100">Item Group</th>
								<th width="150">Item Description</th>
								<th width="70">Batch Qty.</th>
								<th width="50">Batch Weight</th>
								<th width="100">Remarks</th>
						    </tr>
						</thead>
					</table>
					<table class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody id='scroll_body'>
						<?
							$k=1;
							foreach($self_batch_data_array as $batch_key=>$batch_value)
							{
								foreach($batch_value as $item_key=>$item_value)
							    {
									if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')"  id="tr_<? echo $k; ?>">
										<td width="30"><? echo $k; ?></td>
										<td width="100"><? echo  $item_value['batch_date']; ?></td>
										<td width="100"><? echo $item_value['batch_no'];?></td>
										<td width="80"><? echo $color_library[$item_value['color_id']];?></td>
										<td width="80"><? echo $buyer_arr[$order_array[$batch_key][$item_key]['buyer_name']]; ?></td>
										<td width="80"><? echo $order_array[$batch_key][$item_key]['style_ref_no']; ?></td>
										<td width="80"><? echo $booking_array[$batch_key][$item_key]['booking_no'];  ?></td> 
										<td width="100"><? echo $order_array[$batch_key][$item_key]['job_no']; ?></td>
										<td width="100">
											<? 
											$description = explode("-",$item_value['item_description']);
											echo $description['0']; 
											?>
										</td> 
										<td width="150">
											<?
												$description = explode("-",$item_value['item_description']);
												// echo"<pre>";
												// print_r($description);
												echo $description['1'];
										    ?>
										 </td>
										<td width="70" align="right"><? echo $item_value['trims_wgt_qnty'];?></td> 
										<td width="50" align="right"><? echo $item_value['batch_weight'];?></td> 
										<td width="100"><? echo $item_value['remarks'];?></td>
									</tr>
									<?
									$k++;
									$total_trims_wgt_qnty   += $item_value['trims_wgt_qnty'];
								}	
								
						    }
							
						    ?>
							<tr>
								<td width="150" colspan="10" align="right" style="font-weight:bold;">Total</td>
								<td width="70" align="right" style="font-weight:bold;"><? echo $total_trims_wgt_qnty; ?></td>
								<td width="50"></td>
								<td width="100"></td>
							</tr> 
						</tbody>                   
					</table>
				</fieldset>  
			<?
		}
		if($batch_type==3) // Sample
		{
			// ================================= Sample Batch Query ===============================================
			$sql_sample_batch="SELECT    a.id,a.batch_date,a.batch_no,
					a.color_id,
					b.item_description,
					b.trims_wgt_qnty,
					a.batch_weight,
					b.remarks,
					
					a.booking_no,
					a.process_id,
					a.batch_against,
					a.entry_form
					FROM  pro_batch_create_mst a,pro_batch_trims_dtls b,wo_po_details_master c
					WHERE    a.id = b.mst_id AND
					  a.job_no=c.job_no AND
					 a.entry_form = 136
					 AND a.batch_against = 3
					  
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND a.status_active = 1
					AND a.is_deleted = 0
					$comp_cond $working_comp_cond $dates_com $jobdata3 $po_id_cond $batch_num $booking_num $ext_no $floor_num $year_cond
					 GROUP BY a.id,
					a.batch_date,
					a.batch_no,
					a.color_id,
					b.item_description,
					b.trims_wgt_qnty,
					a.batch_weight,
					b.remarks,
					
					a.booking_no,
					a.process_id,
					a.batch_against,
					a.entry_form";
					// echo $sql_self_batch;
	
					$sql_sample_batch_result=sql_select($sql_sample_batch);
					$sample_batch_data_array = array();
					foreach($sql_sample_batch_result as $row){
						$sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_no'] = $row[csf('batch_no')];
						$sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['color_id'] = $row[csf('color_id')];
						$sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['item_description'] = $row[csf('item_description')];
						$sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['trims_wgt_qnty'] = $row[csf('trims_wgt_qnty')];
						$sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_weight'] += $row[csf('batch_weight')];
						$sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['remarks'] = $row[csf('remarks')];
						$sample_batch_data_array[$row[csf('batch_no')]][$row[csf('item_description')]]['batch_date'] = change_date_format($row[csf('batch_date')]);

					}
					// echo "<pre>";
					// print_r($sample_batch_data_array);
                    // ================================= Order Query ===============================================
					$order_array=array(); 
	                $order_sql="SELECT 
					a.job_no,
					a.buyer_name,
					a.style_ref_no,
					c.mst_id       AS batch_id,
					d.batch_no,
					c.item_description,
					d.batch_date
			        FROM wo_po_details_master  a,
					pro_batch_trims_dtls  c,
					pro_batch_create_mst  d
			        WHERE     a.job_no = d.job_no
					AND d.id = c.mst_id
					AND d.entry_form = 136
					AND d.batch_against = 3
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND c.status_active IN (1)
					AND c.is_deleted = 0
					AND d.status_active IN (1)
					AND d.is_deleted = 0
                    $buyer_cond $order_no_cond $year_cond  $jobdata $ref_cond $file_cond $dates_com2";
					// echo $order_sql;
					$order_sql_result=sql_select($order_sql);

					$order_array=array(); 
					foreach($order_sql_result as $row)
					{
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['style_ref_no']=$row[csf('style_ref_no')];
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['job_no']=$row[csf('job_no')];
						$order_array[$row[csf('batch_no')]][$row[csf('item_description')]]['buyer_name']=$row[csf('buyer_name')];
		
						$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
					} 
                //    echo "<pre>";
				//    print_r($order_array);
				 // ================================= Booking Query ===============================================
				    $booking_sql="SELECT a.booking_no,b.batch_no,c.item_description 
					FROM wo_booking_mst a, pro_batch_create_mst b, pro_batch_trims_dtls c
					WHERE     a.job_no = b.job_no
					AND b.id = c.mst_id
					AND a.booking_type = 4
					AND a.is_short = 2
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND c.status_active = 1
					AND c.is_deleted = 0
					  $dates_com3";
					// echo $order_sql;
					$booking_sql_result=sql_select($booking_sql);

					$booking_array=array(); 
					foreach($booking_sql_result as $row)
					{
						$booking_array[$row[csf('batch_no')]][$row[csf('item_description')]]['booking_no']=$row[csf('booking_no')];
					} 
                   //  echo "<pre>";
				   //  print_r($booking_array);
				   // ================================= Trim Pre-cost Query ===============================================
				   $trim_pre_cost_sql="SELECT a.trim_group,a.description,b.batch_no,c.item_description 
				   FROM  wo_pre_cost_trim_cost_dtls a,pro_batch_create_mst b, pro_batch_trims_dtls c
				     WHERE 
						a.job_no = b.job_no 
						AND b.id = c.mst_id 
						AND a.status_active = 1
						AND a.is_deleted = 0
						AND b.status_active = 1
						AND b.is_deleted = 0
						AND c.status_active = 1
						AND c.is_deleted = 0
					 $dates_com3";
				  
				//    echo $trim_pre_cost_sql;
				   $trim_pre_cost_sq_result=sql_select($trim_pre_cost_sql);
				   
				   $trim_pre_cost_array=array(); 
				   foreach($trim_pre_cost_sq_result as $row)
				   {
					   $trim_pre_cost_array[$row[csf('batch_no')]][$row[csf('item_description')]]['trim_group']=$row[csf('trim_group')];
					   $trim_pre_cost_array[$row[csf('batch_no')]][$row[csf('item_description')]]['description']=$row[csf('description')];
				   } 
				//   echo "<pre>";
				//   print_r($trim_pre_cost_array);
	
	
			?>
				<fieldset style="width:1570px">
					<table width="1570" cellpadding="0" cellspacing="0"> 
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo $company_library[$company]; ?><p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo $search_by_arr[$cbo_type]; ?><p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;">Trims Batch Report</p></td> 
						</tr>
						<tr class="form_caption">
							<td align="center" colspan="13"><p style=" font-weight:bold;"><? echo ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to)); ?></p></td> 
						</tr>
					</table>
					<br />
					 <!-- ========= Details Part ======== -->
					 <table id="table_header_1" class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<P align="left" style="font-weight:bold;">Sample Batch</p>
							<tr>
								<th width="30">SL</th>
								<th width="100">Batch Date</th>
								<th width="100">Batch No</th>
								<th width="80">Batch Color</th>
								<th width="80">Buyer</th>
								<th width="80">Style Ref</th>
								<th width="80">Booking Number</th> 
								<th width="100">Job</th>
								<th width="100">Item Group</th>
								<th width="150">Item Description</th>
								<th width="70">Batch Qty.</th>
								<th width="50">Batch Weight</th>
								<th width="100">Remarks</th>
						    </tr>
						</thead>
					</table>
					<table class="rpt_table" width="1570" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody id='scroll_body'>
						<?
							$k=1;
							foreach($sample_batch_data_array as $batch_key=>$batch_value)
							{
								foreach($batch_value as $item_key=>$item_value)
							    {
									if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')"  id="tr_<? echo $k; ?>">
										<td width="30"><? echo $k; ?></td>
										<td width="100"><? echo  $item_value['batch_date']; ?></td>
										<td width="100"><? echo $item_value['batch_no'];?></td>
										<td width="80"><? echo $color_library[$item_value['color_id']];?></td>
										<td width="80"><? echo $buyer_arr[$order_array[$batch_key][$item_key]['buyer_name']]; ?></td>
										<td width="80"><? echo $order_array[$batch_key][$item_key]['style_ref_no']; ?></td>
										<td width="80"><? echo $booking_array[$batch_key][$item_key]['booking_no'];  ?></td> 
										<td width="100"><? echo $order_array[$batch_key][$item_key]['job_no']; ?></td>
										<td width="100">
											<? 
											$description = explode("-",$item_value['item_description']);
											echo $description['0']; 
											?>
										</td> 
										<td width="150">
											<?
												$description = explode("-",$item_value['item_description']);
												// echo"<pre>";
												// print_r($description);
												echo $description['1'];
										    ?>
										 </td>
										<td width="70" align="right"><? echo $item_value['trims_wgt_qnty'];?></td> 
										<td width="50" align="right"><? echo $item_value['batch_weight'];?></td> 
										<td width="100"><? echo $item_value['remarks'];?></td>
									</tr>
									<?
									$k++;
									$total_sam_trims_wgt_qnty   += $item_value['trims_wgt_qnty'];
								}	
								
						    }
							
						    ?>
							<tr>
								<td width="150" colspan="10" align="right" style="font-weight:bold;">Total</td>
								<td width="70" align="right" style="font-weight:bold;"><? echo $total_sam_trims_wgt_qnty; ?></td>
								<td width="50"></td>
								<td width="100"></td>
							</tr> 
						</tbody>                   
					</table>
				</fieldset>  
			<?
		}
	}	
	
	/*$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$total_data####$filename####$batch_type"; */

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
	echo "$total_data####$filename####$batch_type";
	
	disconnect($con);
	exit();
}	//BatchReport
?>