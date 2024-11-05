<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Garments Fabric Booking
Functionality	         :
JS Functions	         :
Created by		         :	Monzu
Creation date 	         : 	27-12-2012
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         : From this version oracle conversion is start
						   Date 08-08-15, Merchandizing >Main Fabric booking > Fabric booking booking GR > Cuff - Color Size Breakdown in Pcs > Contrast color is not showing. Issue id=5749 update by jahid
-----------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
//echo $permission;
//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_suplier($data){
	$cbo_supplier_name='';
	if($data==5 || $data==3){
		//echo "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name";
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier()",0,"" );
	}
	else{
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
	}
	return $cbo_supplier_name;
}

/*if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","","" );
	exit();

}*/

if ($action=="load_drop_down_buyer_popup"){
	/*echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","0","" );
	exit();*/
	//load_drop_down( 'requires/partial_fabric_booking_controller', this.value, 'load_drop_down_season', 'season_td');load_drop_down( 'requires/partial_fabric_booking_controller', this.value, 'load_drop_down_brand', 'brand_td');
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
	else{
		//load_drop_down( 'requires/partial_fabric_booking_controller', this.value, 'load_drop_down_season', 'season_td');load_drop_down( 'requires/partial_fabric_booking_controller', this.value, 'load_drop_down_brand', 'brand_td');
		echo create_drop_down( "cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 130, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	 //echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 130, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_suplier"){
	if($data==5 || $data==3){
	echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier()",0,"" );
	}
	else{
	echo create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "fill_attention(this.value)",0 );
	}
	exit();
}

if($action=="get_attention_name"){
	$data=explode("_",$data);
	$contact_person='';
	if($data[1] !=5){
		$sql=sql_select("select id,contact_person from lib_supplier  where id=$data[0] and  status_active =1 and is_deleted=0");
	}
	if($data[1] ==5){
		$sql=sql_select("select id,contract_person as contact_person from  lib_company  where id=$data[0] and  status_active =1 and is_deleted=0");
	}
	foreach($sql as $row){
		$contact_person=$row[csf('contact_person')];
	}
	echo $contact_person;
}

if($action=="check_conversion_rate"){
	$data=explode("**",$data);
	if($db_type==0){
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}
if($action=="check_month_maintain"){
	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0");
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	if($maintain_setting==1){
		echo "1"."_";
	}
	else{
		echo "0"."_";
	}
	exit();
}

if ($action=="fabric_booking_popup"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_buyer_name;
	?>
	<script>
	function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value(booking_no){
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <!-- <div align="center" style="width:100%;" > -->
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1080" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr style="display: none">
                        <th colspan="10">
                        <?
                        // echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                        ?>
                        <input type="hidden" id="cbo_search_category">
                        </th>
                    </tr>
                    <tr>
                        <th width="130" class="must_entry_caption">Company Name</th>
                        <th width="130" class="must_entry_caption">Buyer Name</th>
                        <th width="80">Booking No</th>
                        <th width="80">Job No</th>
                        <th width="80">File No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Order No</th>
                        <th width="150">Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Item</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                    <input type="hidden" id="selected_booking">
                    <?
                    $call_buyer="";
                    if(empty($company))
                    {
                    	$call_buyer="load_drop_down( 'partial_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );";
                    }
                    echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,$call_buyer ,1);
                    ?>
                    </td>
                    <td <? if(empty($company)){ ?> id="buyer_td" <? }?> >
                    <?
                     echo create_drop_down( "cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$cbo_buyer_name );?>
                    </td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                    <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                    <input type="button" style="width:70px;" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'partial_fabric_booking_controller','setFilterGrid(\'list_view\',-1)')"  /></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        <!-- </div> -->
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
     <script type="text/javascript">
		$("#cbo_company_mst").val(<? echo $company; ?>);
		load_drop_down( 'partial_fabric_booking_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_popup', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}
if ($action=="advanced_booking_popup"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_buyer_name;
	?>
	<script>
	function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value(booking_no){
		document.getElementById('select_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <!-- <div align="center" style="width:100%;" > -->
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="650" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr style="display: none">
                        <th colspan="11">
                        <?
                        // echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                        ?>
                        <input type="hidden" id="cbo_search_category">
                        </th>
                    </tr>
                    <tr>
                        <th width="150" class="must_entry_caption">Company Name</th>
						<th width="100">Job No</th>
                        <th width="100">Booking No</th>
                        <th width="150">Date Range</th>
						<th width="100"></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                    <input type="hidden" id="select_booking">
                    <?
                    $call_buyer="";
                    if(empty($company))
                    {
                    	$call_buyer="load_drop_down( 'partial_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );";
                    }
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,$call_buyer ,1);
                    ?>
                    </td>
					<td><input name="txt_job_prefix" id="txt_job_prefix" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                    <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                    <input type="button" style="width:100px;" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prefix').value, 'create_advanced_booking_search_list_view', 'search_div', 'partial_fabric_booking_controller','setFilterGrid(\'list_view\',-1)')"  /></td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        <!-- </div> -->
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
     <script type="text/javascript">
		$("#cbo_company_mst").val(<? echo $company; ?>);
		load_drop_down( 'partial_fabric_booking_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_popup', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}
if ($action=="create_advanced_booking_search_list_view"){
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else $company="";
	// if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";

	if($data[3] !=="" || $data[3] > 0){
		if (str_replace("'","",$data[3])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[3]%'  $booking_year_cond  "; else  $booking_cond="";
	}

	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'"; else  $job_cond="";
	if (str_replace("'","",$data[4])!="") $job_cond2=" and a.job_no like '%$data[4]%'"; else  $job_cond2="";



	if($db_type==0){
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2){
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$po_array=array();
	$job_prefix_num=array();
	$sql_po= sql_select("select a.booking_no,a.po_break_down_id,a.job_no from wo_booking_mst  a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=2 and a.entry_form=271 and a.proceed_advanced=1 and a.status_active=1 and a.is_deleted=0 order by a.booking_no");
	foreach($sql_po as $row){
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$job_prefix_arr=explode("-",$row[csf("job_no")]);
		$po_number_string="";
		foreach($po_id as $key=> $value ){
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
		$job_prefix_num[$row[csf("job_no")]]=ltrim($job_prefix_arr[2],0);
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$suplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$job_prefix_num,6=>$po_array,9=>$item_category,10=>$fabric_source,11=>$suplier,12=>$approved,13=>$is_ready);
	if($data[12]==0){
			$sql="SELECT a.id,a.booking_no_prefix_num,a.pay_mode,a.booking_no,a.company_id,a.buyer_id,a.booking_date,a.delivery_date,
			a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.proceed_advanced,c.gmts_item_id,c.job_no_prefix_num,
			c.style_ref_no,sum(b.grey_fab_qnty) as qnty  
			FROM wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d  
			WHERE a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=1 and a.entry_form=271 and  a.proceed_advanced=1 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  $company $buyer $booking_date $booking_cond $style_cond $order_cond $job_cond 
			GROUP BY a.id,
			a.booking_no_prefix_num,a.pay_mode,a.booking_no,a.company_id,a.buyer_id,a.booking_date,a.delivery_date,a.item_category,
			a.fabric_source,a.supplier_id,a.is_approved,a.proceed_advanced,c.job_no_prefix_num,c.gmts_item_id,c.style_ref_no order by a.id DESC";
		}
	else{
		 $sql="select a.id, a.job_no as job_no_prefix_num, a.booking_no_prefix_num, a.pay_mode, a.booking_no, company_id, a.buyer_id, a.supplier_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.is_approved from wo_booking_mst a
		   where a.booking_no not in ( select a.booking_no from  wo_booking_mst a , wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and  a.booking_type=1 and a.ready_to_approved=1 and a.entry_form=271 and  a.status_active =1 and a.is_deleted=0  and  b.status_active =1 and b.is_deleted=0 $company $buyer $booking_date $booking_cond $file_no_cond $internal_ref_cond group by a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date  ) and a.booking_type=1 and a.entry_form=271 and  a.status_active =1 and a.is_deleted=0
		   $company  $buyer $supplier_id $booking_date $booking_cond $job_cond2 group by a.id, a.booking_no_prefix_num, a.booking_no,a.job_no,company_id,a.buyer_id,a.supplier_id,a.pay_mode,a.booking_date,a.delivery_date,a.item_category,a.fabric_source,a.is_approved order by a.id DESC";
	  }
	// echo $sql; die;
	  ?>
      <div style="width: 630px">
      <table width="630" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th width="30">SL</th>
					<th width="100">Job No</th>
					<th width="100">Booking Date</th>
                    <th width="100">Booking No</th>
                    <th width="100">Pay Mode</th>
					<th width="100">Supplier</th>
                    <th>Booking Qty</th>

                   
                </tr>
            </thead>
        </table>
        <div style="width:630px; max-height:240px; overflow-y:scroll" id="scroll_body">
	        <table width="610" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" id="list_view">
                <tbody>
                <?
                $k=1;
                $result_data=sql_select($sql);
                foreach($result_data as $row)
				{
                    if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr onClick="js_set_value('<? echo $row[csf("booking_no")]?>')" style="cursor:pointer"  bgcolor="<? echo $bgcolor;?>" >
                        <td width="30" align="center"><? echo $k; ?></td>
						<td width="100" align="center"><? echo $row[csf("job_no_prefix_num")];?></td>
						<td width="100" align="center"><? echo change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-");?></td>
                        <td width="100" align="center"><? echo $row[csf("booking_no")];?></td>
						<td width="100" style="word-break:break-all" align="center"><? echo $pay_mode[$row[csf("pay_mode")]];?></td>
                        <td width="100" align="center"><? if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) echo $comp[$row[csf("supplier_id")]]; else echo $suplier[$row[csf("supplier_id")]]; ?></td>
                        <td align="right"><? echo $row[csf("qnty")];?></td>

					</tr>
					<?
					$k++;
                }
                ?>
	        </tbody>
	     </table>
     </div>
    </div>
    <?
	exit();
}
if ($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		//load_drop_down( 'requires/partial_fabric_booking_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/partial_fabric_booking_controller', this.value, 'load_drop_down_season', 'season_td');
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		//load_drop_down( 'requires/partial_fabric_booking_controller', this.value, 'load_drop_down_season', 'season_td');load_drop_down( 'requires/partial_fabric_booking_controller', this.value, 'load_drop_down_brand', 'brand_td');
		exit();
	}
}

if ($action=="create_booking_search_list_view"){
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else $company="";
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
	if($db_type==0) $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num='$data[6]'    "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no ='$data[10]'"; //else  $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number = '$data[11]'  "; //else  $order_cond="";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like '$data[10]%'"; //else  $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number like '$data[11]%'  "; //else  $order_cond="";
	}

	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like'%$data[10]'"; //else  $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number like '%$data[11]'  "; //else  $order_cond="";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like '%$data[10]%'"; //else  $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number like '%$data[11]%'  "; //else  $order_cond="";
	}

	$file_no = str_replace("'","",$data[8]);
	$internal_ref = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' ";

	if($db_type==0){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$po_array=array();
	$job_prefix_num=array();
	$sql_po= sql_select("select a.booking_no,a.po_break_down_id,a.job_no from wo_booking_mst  a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=2 and a.entry_form=271 and a.status_active=1 and a.is_deleted=0 order by a.booking_no");
	foreach($sql_po as $row){
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$job_prefix_arr=explode("-",$row[csf("job_no")]);
		$po_number_string="";
		foreach($po_id as $key=> $value ){
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
		$job_prefix_num[$row[csf("job_no")]]=ltrim($job_prefix_arr[2],0);
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$job_prefix_num,6=>$po_array,9=>$item_category,10=>$fabric_source,11=>$suplier,12=>$approved,13=>$is_ready);
	if($data[12]==0){
			$sql="select a.id, a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.gmts_item_id, c.job_no_prefix_num, c.style_ref_no, d.po_number, d.grouping, d.file_no from wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d  where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=1 and a.entry_form=271 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  $company $buyer $booking_date $booking_cond $style_cond $order_cond $job_cond group by a.id, a.booking_no_prefix_num, a.pay_mode,a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.job_no_prefix_num, c.gmts_item_id, c.style_ref_no, d.po_number, d.grouping, d.file_no order by a.id DESC";
		}
	else{
		 $sql="select a.id, a.job_no as job_no_prefix_num, a.booking_no_prefix_num, a.pay_mode, a.booking_no, company_id, a.buyer_id, a.supplier_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.is_approved from wo_booking_mst a
		   where a.booking_no not in ( select a.booking_no from  wo_booking_mst a , wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and  a.booking_type=1 and a.entry_form=271 and  a.status_active =1 and a.is_deleted=0  and  b.status_active =1 and b.is_deleted=0 $company $buyer $booking_date $booking_cond $job_cond $file_no_cond $internal_ref_cond group by a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date  ) and a.booking_type=1 and a.entry_form=271 and  a.status_active =1 and a.is_deleted=0
		   $company  $buyer $supplier_id $booking_date $booking_cond group by a.id, a.booking_no_prefix_num, a.booking_no,a.job_no,company_id,a.buyer_id,a.supplier_id,a.pay_mode,a.booking_date,a.delivery_date,a.item_category,a.fabric_source,a.is_approved order by a.id DESC";
	  }
	  //echo $sql; die;
	  ?>
      <div style="width: 1080px">
      <table width="1080" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="50">Booking No</th>
                    <th width="60">Booking Date</th>
                    <th width="40">Com pany</th>
                    <th width="60">Buyer</th>
                    <th width="50">Job No</th>
                    <th width="80">Style Ref.</th>
                    <th width="120">Gmts Item </th>
                    <th width="100">PO number</th>
                    <th width="50">Internal Ref</th>
                    <th width="50">File No</th>
                    <th width="80">Fab. Nature</th>
                    <th width="70">Fab. Source</th>
                    <th width="60">Pay Mode</th>
                    <th width="60">Supplier</th>
                    <th width="40">Appr oved</th>
                    <th>Ready to App.</th>
                </tr>
            </thead>
        </table>
        <div style="width:1080px; max-height:340px; overflow-y:scroll" id="scroll_body">
	        <table width="1060" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" id="list_view">
                <tbody>
                <?
                $k=1;
                $result_data=sql_select($sql);
                foreach($result_data as $row)
				{
                    if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr onClick="js_set_value('<? echo $row[csf("booking_no")]?>')" style="cursor:pointer"  bgcolor="<? echo $bgcolor;?>" >
                        <td width="30"><? echo $k; ?></td>
                        <td width="50"><? echo $row[csf("booking_no_prefix_num")];?></td>
                        <td width="60"><? echo change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-");?></td>
                        <td width="40" style="word-break:break-all"><? echo $comp[$row[csf("company_id")]];?></td>
                        <td width="60" style="word-break:break-all"><? echo $buyer_arr[$row[csf("buyer_id")]];?></td>
                        <td width="50"><? echo $row[csf("job_no_prefix_num")];?></td>
                        <td width="80" style="word-break:break-all"><? echo $row[csf("style_ref_no")];?></td>
                        <td width="120" style="word-break:break-all"><?
                        $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
                        foreach($gmts_item_id as $item_id)
                        {
                            if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
                        }
                        echo $gmts_item;?> </td>
                        <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf("po_number")];?></td>
                        <td width="50" style="word-break:break-all"><? echo $row[csf("grouping")];?></td>
                        <td width="50"><? echo $row[csf("file_no")];?></td>
                        <td width="80"><? echo $item_category[$row[csf("item_category")]];?></td>
                        <td width="70"><? echo $fabric_source[$row[csf("fabric_source")]];?></td>
                        <td width="60" style="word-break:break-all"><? echo $pay_mode[$row[csf("pay_mode")]];?></td>
                        <td width="60"><? if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) echo $comp[$row[csf("supplier_id")]]; else echo $suplier[$row[csf("supplier_id")]]; ?></td>
                        <td width="40"><? echo $approved[$row[csf("is_approved")]];?></td>
                        <td ><? echo $is_ready[$row[csf("ready_to_approved")]];?></td>
					</tr>
					<?
					$k++;
                }
                ?>
	        </tbody>
	     </table>
     </div>
    </div>
    <?
	exit();
}


if ($action=="populate_data_from_search_popup"){
	 $sql= "select id,booking_no, booking_date, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, booking_month, supplier_id, attention, booking_percent, delivery_date, source, booking_year, colar_excess_percent, cuff_excess_percent, is_approved, ready_to_approved, is_apply_last_update, rmg_process_breakdown, fabric_composition, uom, remarks, cbo_level, delivery_address, test_req, season_year, season_id, brand_id, proceed_advanced,adv_booking_no from wo_booking_mst where booking_no='$data' and status_active =1 and is_deleted=0";

	 $data_array=sql_select($sql);
	 foreach ($data_array as $row){
	 	$delivery_address = str_replace("\n", "\\n", $row[csf("delivery_address")]);
		$test_req = str_replace("\n", "\\n", $row[csf("test_req")]);
        echo "document.getElementById('delivery_address').value = '".$delivery_address."';\n";
		echo "document.getElementById('txt_test_req').value = '".$test_req."';\n";
		echo "document.getElementById('txt_booking_num').value  = '".$row[csf("adv_booking_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";		
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_booking_percent').value = '".$row[csf("booking_percent")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('txt_colar_excess_percent').value = '".$row[csf("colar_excess_percent")]."';\n";
		echo "document.getElementById('txt_cuff_excess_percent').value = '".$row[csf("cuff_excess_percent")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('processloss_breck_down').value = '".$row[csf("rmg_process_breakdown")]."';\n";
		echo "document.getElementById('txt_fabriccomposition').value = '".$row[csf("fabric_composition")]."';\n";
		$supplier_dropdwan=load_drop_down_suplier($row[csf("pay_mode")]);
		echo "document.getElementById('sup_td').innerHTML = '".$supplier_dropdwan."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbouom').value = '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";

		if($row[csf("proceed_advanced")]==1)
		{
			echo "$('#chk_pro_advanced').prop('checked', true);\n";
			echo "$('#chk_pro_advanced').val('".$row[csf("proceed_advanced")]."');\n";
		}
		else
		{
			echo "$('#chk_pro_advanced').prop('checked', false);\n";
			echo "$('#chk_pro_advanced').val(2);\n";
		}

		if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping,
	                                    listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
		else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}

		$data_array3=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$row[csf("po_break_down_id")].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
		foreach($data_array3 as $inv){
		$grouping=implode(",",array_unique(explode(",",$inv[csf("grouping")])));
		$file_no=implode(",",array_unique(explode(",",$inv[csf("file_no")])));
			echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
			echo "document.getElementById('txt_intarnal_ref').value = '".$grouping."';\n";
		}

		if($row[csf("is_approved")]==1){
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		else{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
		}

		$colar_culff_percent=return_field_value("colar_culff_percent", "variable_order_tracking", "company_name='".$row[csf("company_id")]."'  and variable_list=40 and status_active=1 and is_deleted=0");
		if($colar_culff_percent==1){
			echo "$('#txt_colar_excess_percent').removeAttr('disabled')".";\n";
			echo "$('#txt_cuff_excess_percent').removeAttr('disabled')".";\n";
		}
		if($colar_culff_percent==2){
			echo "$('#txt_colar_excess_percent').attr('disabled','true')".";\n";
		    echo "$('#txt_cuff_excess_percent').attr('disabled','true')".";\n";
		}
		$sql_delevary=sql_select("select task_number,max(task_finish_date) as task_finish_date from tna_process_mst where po_number_id in(".$row[csf("po_break_down_id")].") and task_number in(73) and is_deleted = 0 and 	status_active=1 group by task_number");
		foreach($sql_delevary as $row_delevary){
		   echo "document.getElementById('txt_tna_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
		}
	 }
}

if ($action=="populate_data_from_adv_search_popup"){
	$sql= "select id,booking_no, booking_date, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, booking_month, supplier_id, attention, booking_percent, delivery_date, source, booking_year, colar_excess_percent, cuff_excess_percent, is_approved, ready_to_approved, is_apply_last_update, rmg_process_breakdown, fabric_composition, uom, remarks, cbo_level, delivery_address, test_req, season_year, season_id, brand_id, proceed_advanced from wo_booking_mst where booking_no='$data' and status_active =1 and is_deleted=0";

	$data_array=sql_select($sql);
	foreach ($data_array as $row){
	
	   echo "document.getElementById('txt_booking_num').value = '".$row[csf("booking_no")]."';\n";

	}
}


if ($action=="fabric_search_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
					js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		var selected_id = new Array();
		var selected_item=new Array();
		var selected_po=new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_item.push($('#pre_cost_dtls_id' + str).val());
					selected_po.push($('#txt_po_id' + str).val());
				}
				else{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_item.splice( i,1 );
					selected_po.splice( i,1 );
				}
			}
			var id = '';
			var pre_cost_dtls_id='';
			var txt_po_id='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				pre_cost_dtls_id+=selected_item[i]+ ',';
				txt_po_id+=selected_po[i]+ ',';
			}
			id = id.substr( 0, id.length - 1 );
			pre_cost_dtls_id = pre_cost_dtls_id.substr( 0, pre_cost_dtls_id.length - 1 );
			txt_po_id = txt_po_id.substr( 0, txt_po_id.length - 1 );
			$('#txt_selected_id').val( id );
			$('#txt_pre_cost_dtls_id').val( pre_cost_dtls_id );
			$('#txt_selected_po').val( txt_po_id );
		}

		function openmypage_jobsearch(page_link,title){
			//alert(title)
			var cbo_company_mst=$('#cbo_company_mst').val();
			var cbo_buyer_name=$('#cbo_buyer_name').val();
		page_link=page_link+"&cbo_company_mst="+cbo_company_mst+"&cbo_buyer_name="+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Job Search', 'width=750px,height=300px,center=1,resize=1,scrolling=0','../../')

		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("job_no");
			var year=this.contentDoc.getElementById("cbo_job_year");
			if (job_no.value!=""){
				$('#txt_job_prifix').val( job_no.value );
				$('#cbo_job_year').val( year.value );
				show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_supplier_name').value, 'fabric_search_list_view', 'search_div', 'partial_fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)');

			}
		}
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchpofrm_1" id="searchpofrm_1">
            <table width="800"  align="center" rules="all">
                <tr>
                <td align="center" width="100%">
                <table  width="800" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th colspan="11" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>

                        <tr>
                            <th width="140">Company</th>
                            <th width="150">Buyer</th>
                            <th width="60">Year</th>
                            <th width="60">Job No</th>
                            <th width="70">Internal Ref</th>
                            <th width="70">File No</th>
                            <th width="70">Style Ref </th>
                            <th width="70">Order No</th>
                            <th width="170" colspan="2" style="display: none">Date Range</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tr>
                        <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "","1");//load_drop_down( 'partial_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' ); ?>
                        </td>
                        <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "","1" ); ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                        <td><input name="txt_job_prifix" placeholder="Brws./Write" id="txt_job_prifix" class="text_boxes" style="width:50px" onDblClick="openmypage_jobsearch('partial_fabric_booking_controller.php?action=job_search_popup','Job Search')"></td>
                        <!-- <td><input name="txt_job_prifix" placeholder="write" id="txt_job_prifix" class="text_boxes" style="width:50px"></td> -->
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:60px"></td>
                        <td style="display: none"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value=""/></td>
                        <td style="display: none"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value=""/></td>
                        <td align="center">
                        <input type="hidden" name="cbo_currency" id="cbo_currency" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_currency); ?>"  />
                        <input type="hidden" name="cbo_fabric_natu" id="cbo_fabric_natu" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_fabric_natu); ?>"  />
                        <input type="hidden" name="cbouom" id="cbouom" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbouom); ?>"  />
                        <input type="hidden" name="cbo_fabric_source" id="cbo_fabric_source" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_fabric_source); ?>"  />
                        <input type="hidden" name="cbo_supplier_name" id="cbo_supplier_name" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_supplier); ?>"  />
                        <input type="hidden" id="cbo_source" value="<? echo str_replace("'","",$cbo_source); ?>"  />
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_source').value, 'fabric_search_list_view', 'search_div', 'partial_fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center">
                        <input type="hidden" class="text_boxes" readonly style="width:550px" id="txt_selected_po">
                        <input type="hidden" id="txt_selected_id">
                        <input type="hidden" id="txt_pre_cost_dtls_id">
                        </td>
                    </tr>
                </table>
                </td>
                </tr>
                <tr>
                <td align="center" >
                <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" />
                </td>
                </tr>
                <tr>
                <td id="search_div" align="center">
                </td>
                </tr>
                <tr>
                <td id="search_div" align="center">
                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                </td>
                </tr>
            </table>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="fabric_search_list_view"){
	$data=explode('_',$data);
	$company=$data[0];
	$buyer=$data[1];
	$cbo_job_year=$data[2];
	$job=$data[3];
	$internal_ref=$data[4];
	$file_no=$data[5];
	$style=$data[6];
	$order_search=$data[7];
	$date_from=$data[8];
	$date_to=$data[9];
	$cbo_currency=$data[10];
	$cbo_fabric_natu=$data[11];
	$cbouom=$data[12];
	$cbo_fabric_source=$data[13];
	$search_category=$data[14];
	$cbo_supplier=$data[15];
	$cbo_source=$data[16];
	if ($company!=0) $company_cond=" and a.company_name='$company'"; else { echo "Please Select Company First."; die; }
	if ($buyer!=0)   $buyer_cond=" and a.buyer_name='$buyer'"; else{ echo "Please Select Buyer First."; die; }
	if ($cbo_currency!="")   $currency_cond=" and a.currency_id='$cbo_currency'"; else{ echo "Please Select Currency First."; die; }
	if ($cbo_fabric_natu!="")   $fabric_natu_cond=" and d.fab_nature_id='$cbo_fabric_natu'"; else{ echo "Please Select Fabric Nature First."; die; }
	if ($cbouom!=0)   $uom_cond=" and d.uom='$cbouom'";
	if($cbo_source==3) $cbo_source=2;
	if ($cbo_source!=0)   $cbo_source_cond=" and d.source_id='$cbo_source'";
	if ($cbo_fabric_source!="")   $fabric_source_cond=" and d.fabric_source='$cbo_fabric_source'"; else{ echo "Please Select Fabric Source  First."; die; }

	if($db_type==0){
	if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2){
	if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_job_year";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_job_year";
	if (str_replace("'","",$job)=="" && str_replace("'","",$style)=="")
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please Insert Job Or Style First.";
		die;
	}


	$job_cond=""; $order_cond=""; $style_cond="";
	if($search_category==1){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num='$job'"; //else  $job_cond="";
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number = '$order_search'"; //else  $order_cond="";
		if (trim($style)!="") $style_cond=" and a.style_ref_no ='$style'"; //else  $style_cond="";
		if (trim($internal_ref) !="") $internal_ref_cond=" and b.grouping = '$internal_ref'";
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no='$file_no' ";
	}
	else if($search_category==2){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '$job%'"; //else  $job_cond="";
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '$order_search%'  "; //else  $order_cond="";
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '$style%'  "; //else  $style_cond="";
		if (trim($internal_ref) !="") $internal_ref_cond=" and b.grouping like '$internal_ref%'";
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '$file_no%' ";
	}
	else if($search_category==3){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '%$job'"; //else  $job_cond="";
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '%$order_search'  "; //else  $order_cond="";
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '%$style'"; //else  $style_cond="";
		if (trim($internal_ref) !="")  $internal_ref_cond=" and b.grouping like '%$internal_ref'";
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '%$file_no' ";
	}
	else if($search_category==4 || $search_category==0){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '%$job%'"; //else  $job_cond="";
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '%$order_search%'  "; //else  $order_cond="";
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '%$style%'"; //else  $style_cond="";
		if (trim($internal_ref)!="")  $internal_ref_cond=" and b.grouping like '%$internal_ref%'";
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '%$file_no%' ";
	}
	
	$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$company' and b.page_id in (2,37,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.approval_need=1 order by a.setup_date");
	//echo "select b.page_id, b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id in (36,37) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date asc";//die;
	$app_nessity=2; $validate_page=0; $allow_partial=2;
	foreach($sql as $row){
		$app_nessity=$row[csf('approval_need')];
		$validate_page=$row[csf('validate_page')];
		$allow_partial=$row[csf('allow_partial')];
	}
	
	if($app_nessity==1)
	{
		 if($allow_partial==1) $bomAppCond=" and b.approved in (1,3)";
		 else $bomAppCond=" and b.approved in (1)";
	} else $bomAppCond="";
	
	//echo "SELECT a.job_no, a.id from wo_po_details_master a join wo_pre_cost_mst b on a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond $style_cond $year_cond $bomAppCond"; die;
	$job_no_sql_arr=sql_select("SELECT a.job_no, a.id from wo_po_details_master a join wo_pre_cost_mst b on a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond $style_cond $year_cond $bomAppCond");
	foreach ($job_no_sql_arr as $row) {
		$job_no_arr[$row[csf('id')]] = $row[csf('id')];
	}
	$jobids = "'" . implode( "','", $job_no_arr ) . "'";
	$sql_supp="select fabric_id from wo_pre_cost_fabric_supplier where job_id in ($jobids) and supplier_id in($cbo_supplier) and is_deleted=0 and status_active=1";
	//echo $sql_supp; die;
    $sql_suppRes=sql_select( $sql_supp ); $fabric_id="";
    foreach($sql_suppRes as $row)
    {
      $fabric_id.=$row[csf('fabric_id')].",";
    }
    unset($sql_suppRes);
    $fabric_ids=chop($fabric_id,',');
    if($db_type==2)
    {
    	if($fabric_ids!="") $fabric_idCond="and (d.id in ($fabric_ids) or d.nominated_supp_multi is null)"; else $fabric_idCond=" and (d.nominated_supp_multi is null or d.nominated_supp_multi=0)";
    }
    else
    {
		if($fabric_ids!="") $fabric_idCond="and (d.id in ($fabric_ids) or d.nominated_supp_multi='')"; else $fabric_idCond=" and d.nominated_supp_multi=''";
    }

	$condition= new condition();
	if(str_replace("'","",$company) !=''){
		$condition->company_name("=$company");
	}
	if(str_replace("'","",$buyer) !=''){
		$condition->buyer_name("=$buyer");
	}
	if(str_replace("'","",$job) !=''){
		$condition->job_no_prefix_num("=$job");
	}
	$condition->init();
	$fabric= new fabric($condition);

	$req_qty_arr=$fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
	$supplier_library_fabric=return_library_array( "select a.supplier_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");

	$cu_booking_data_arr=array();
	  $sql='select b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id",b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id" ,a.id AS "booking_id",a.fin_fab_qnty AS "fin_fab_qnty",a.grey_fab_qnty AS "grey_fab_qnty",a.dia_width AS "dia_width"  from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_po_details_master c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and a.job_no=b.job_no and a.job_no=c.job_no and  c.job_no_prefix_num ='.$job.' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,b.color_number_id,a.id,a.fin_fab_qnty,a.grey_fab_qnty,a.dia_width';
	$dataArray=sql_select($sql);
	foreach($dataArray as $dataArray_row){
		$cu_booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
	}

	$sql= 'SELECT a.job_no AS "job_no", b.id AS "id", b.po_number AS "po_number", c.item_number_id AS "item_number_id", d.id AS "pre_cost_dtls_id", d.body_part_id AS "body_part_id", d.construction AS "construction", d.composition AS "composition", d.fab_nature_id AS "fab_nature_id", d.fabric_source AS "fabric_source", d.nominated_supp_multi, d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id", d.uom AS "uom", d.gsm_weight AS "gsm_weight", min(e.id) AS "eid" ,f.fabric_ref from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e,lib_yarn_count_determina_mst f where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and f.id=d.lib_yarn_count_deter_id and b.job_id in ('.$jobids.') and e.cons !=0 and  b.shiping_status!=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$company_cond . $buyer_cond. $year_cond. $job_cond. $internal_ref_cond. $file_no_cond . $style_cond. $order_cond. $shipment_date. $currency_cond. $fabric_natu_cond. $uom_cond. $fabric_source_cond." $fabric_idCond $cbo_source_cond group by a.job_no, b.id, b.po_number, c.item_number_id, d.id, d.body_part_id, d.construction, d.composition, d.fab_nature_id, d.fabric_source, d.lib_yarn_count_deter_id, d.uom, d.gsm_weight, d.nominated_supp_multi,f.fabric_ref";
	//echo $sql;die;
	$sql_data=sql_select($sql);
	?>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1053" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Job No</th>
                <th width="80">Po No</th>
                <th width="100">Item</th>
                <th width="100">Body Part</th>
                <th width="100">Construction</th>
                <th width="100">Composition</th>
				<th width="70">Fabric Ref</th>
                <th width="70">Gsm</th>
                <th width="80">Fabric Nature</th>
                <th width="70">Fabric Source</th>
                <th width="100">Nominated Supp</th>
                <th width="">Uom</th>
            </thead>
     	</table>
     <!-- <div style="width:1020px; max-height:270px;overflow-y:scroll;" > -->
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1053" class="rpt_table" id="list_view">
    <?
	$i=1;
	$booked = 0;
	foreach($sql_data as $sql_row){
		$reqQty=0;
		$$supplier_str='';
		if($sql_row[csf('nominated_supp_multi')]!='')
		{
			$supplier_arr= explode(',', $sql_row[csf('nominated_supp_multi')]);
			foreach ($supplier_arr as $value) {
				$supplier_str .= $supplier_library_fabric[$value].',';
			}
			
		}
		$supplier_str = substr($supplier_str, 0, -1);
		if($cbo_fabric_natu==2){
			$reqQty=$req_qty_arr['knit']['grey'][$sql_row['id']][$sql_row['pre_cost_dtls_id']][$sql_row['uom']];
		}
		if($cbo_fabric_natu==3){
			$reqQty=$req_qty_arr['woven']['grey'][$sql_row['id']][$sql_row['pre_cost_dtls_id']][$sql_row['uom']];
		}

		$cuBooking=$cu_booking_data_arr[$sql_row['pre_cost_dtls_id']][$sql_row['id']];
		$balQty=number_format($reqQty-$cuBooking,4,".","");

		if($balQty > 0 ){ ?>
	        <tr style="text-decoration:none; cursor:pointer;text-align:center" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
	        <td width="30">
			<? echo $i; ?>
	        <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $sql_row['eid']; ?>"/>
	        <input type="hidden" name="pre_cost_dtls_id" id="pre_cost_dtls_id<?php echo $i ?>" value="<? echo $sql_row['pre_cost_dtls_id']; ?>"/>
	        <input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $sql_row['id']; ?>"/>
	        </td>
	        <td width="80"><? echo $sql_row['job_no']; ?></td>
	        <td width="80"><? echo $sql_row['po_number']; ?></td>
	        <td width="100"><? echo $garments_item[$sql_row['item_number_id']]; ?></td>
	        <td width="100"><? echo $body_part[$sql_row['body_part_id']]; ?></td>
	        <td width="100"><? echo $sql_row['construction']; ?></td>
	        <td width="100"><? echo $sql_row['composition']; ?></td>
			<td width="70"><? echo $sql_row[csf('fabric_ref')]; ?></td>
	        <td width="70"><? echo $sql_row['gsm_weight']; ?></td>
	        <td width="80"><? echo $item_category[$sql_row['fab_nature_id']]; ?></td>
	        <td width="70"><? echo $fabric_source[$sql_row['fabric_source']]; ?></td>
	        <td width="100"><? echo $supplier_str; ?></td>
	        <td width=""><? echo $unit_of_measurement[$sql_row['uom']]; ?></td>
	        </tr>
	        <?
			$i++;
		} else{
			$booked = 1;
		 }
	 }
	if($booked == 1){
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Fabric already booked.";
	}
	?>
    </table>
	<!-- </div> -->
    <?
	exit();
}

if ($action=="job_search_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( str_data ){
			$('#job_no').val( str_data );
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchpofrm_1" id="searchpofrm_1">
            <table width="750"  align="center" rules="all">
                <tr>
                <td align="center" width="100%">
                <table  width="750" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th colspan="11" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                        <tr>
                            <th width="140">Company</th>
                            <th width="150">Buyer</th>
                            <th width="60">Year</th>
                            <!-- <th width="60">Job No</th>
                            <th width="70">Internal Ref</th>
                            <th width="70">File No</th>
                            <th width="70">Style Ref </th>
                            <th width="70">Order No</th> -->
                            <th width="100" colspan="2">Date Range</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tr>
                        <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_mst), "","1");//load_drop_down( 'partial_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' ); ?>
                        </td>
                        <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_mst and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "","1" ); ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                        <td style="display: none"><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                        <td style="display: none"><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                        <td style="display: none"><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                        <td style="display: none"><input name="txt_style" id="txt_style" class="text_boxes" style="width:60px"></td>
                        <td style="display: none"><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:60px"></td>
                        <td width="60"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value=""/></td>
                        <td width="60"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value=""/></td>
                        <td align="center">
                        <input type="hidden" name="cbo_currency" id="cbo_currency" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_currency); ?>"  />
                        <input type="hidden" name="cbo_fabric_natu" id="cbo_fabric_natu" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_fabric_natu); ?>"  />
                        <input type="hidden" name="cbouom" id="cbouom" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbouom); ?>"  />
                        <input type="hidden" name="cbo_fabric_source" id="cbo_fabric_source" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_fabric_source); ?>"  />
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value, 'create_job_search_list_view', 'search_div', 'partial_fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center">
                        <input type="hidden" id="job_no">
                        </td>
                    </tr>
                </table>
                </td>
                </tr>
                <tr>
                <td align="center" >
                <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" />
                </td>
                </tr>
                <tr>
                <td id="search_div" align="center">
                </td>
                </tr>
                <!-- <tr>
                <td id="search_div" align="center">
                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                </td>
                </tr> -->
            </table>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	//if (str_replace("'","",$data[4])==""){echo "Please Insert Job First."; die;}
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer=""; //{ echo "Please Select Buyer First."; die; }
	if ($data[13]!=0) $uom_cond=" and d.uom='$data[13]'"; else $uom_cond=""; //{ echo "Please Select Buyer First."; die; }

	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[10]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[10]";

	$job_cond="";
	$order_cond="";
	$style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number = '$data[5]'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no ='$data[6]'"; //else  $style_cond="";
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '$data[5]%'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '$data[6]%'  "; //else  $style_cond="";
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]'"; //else  $style_cond="";
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]%'"; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[8]);
	$file_no = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."%' ";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}

	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	  $sql= 'select a.job_no_prefix_num AS "job_no_prefix_num" ,a.buyer_name AS "buyer_name", a.job_no AS "job_no",a.style_ref_no AS "style_ref_no", b.id AS "id",b.po_number AS "po_number", b.po_quantity AS "po_quantity",b.shipment_date AS "shipment_date", b.grouping AS "grouping", b.file_no AS "file_no" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where   a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$shipment_date. $buyer. $company.  $job_cond. $order_cond. $style_cond. $file_no_cond. $internal_ref_cond. $year_cond.' group by a.job_no_prefix_num,a.job_no,a.buyer_name,a.style_ref_no,b.id,b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no order by a.job_no desc' ;
	  //echo $sql;

	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Buyer</th>
                <th width="80">Job No</th>
                <th width="100">Style Ref.</th>
                <th width="100">PO number</th>
                <th width="80">PO Qty</th>
                <th width="70">Shipment Date</th>
                <th width="70">Internal Ref</th>
                <th width="70">File No</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="list_view">
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bg_color; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['job_no_prefix_num']; ?>');" >
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="80"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
                        <td width="80"><? echo $row['job_no_prefix_num']; ?></td>
                        <td width="100"><? echo $row['style_ref_no'];  ?></td>
						<td width="100"><? echo $row['po_number']; ?></td>
						<td width="80" align="right"><? echo number_format($row['po_quantity']);?> </td>
                        <td width="70"><? echo change_date_format($row['shipment_date']); ?></td>
                        <td width="70"><? echo $row['grouping'];  ?></td>
						<td width="70"><? echo $row['file_no'];  ?></td>
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





if ($action=="order_search_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array();
		function check_all_data(){
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length-1;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ){
				if($("#tr_"+i).css("display") !='none'){
				document.getElementById("tr_"+i).click();
				}
			}
		}

		function toggle( x, origColor ){
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}

		function js_set_value( str_data,tr_id ){
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] ){
				alert('No Job Mix Allowed')
				return;
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('job_no').value=str_all[2];

			if( jQuery.inArray( str , selected_id ) == -1 ){
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else{
				for( var i = 0; i < selected_id.length; i++ ){
					if( selected_id[i] == str ) break;
				}

				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//alert(selected_id.length)
				if(selected_id.length==0){
					document.getElementById('job_no').value="";
				}
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ ){
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchpofrm_1" id="searchpofrm_1">
            <table width="940"  align="center" rules="all">
                <tr>
                <td align="center" width="100%">
                <table  width="940" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th colspan="11" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                        <tr>
                            <th width="140">Company</th>
                            <th width="150">Buyer</th>
                            <th width="60">Year</th>
                            <th width="60">Job No</th>
                            <th width="70">Internal Ref</th>
                            <th width="70">File No</th>
                            <th width="70">Style Ref </th>
                            <th width="70">Order No</th>
                            <th width="170" colspan="2">Date Range</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tr>
                        <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'partial_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );","1"); ?>
                        </td>
                        <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "","1" ); ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                        <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value=""/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value=""/></td>
                        <td align="center">
                        <input type="hidden" name="cbo_currency" id="cbo_currency" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_currency); ?>"  />
                        <input type="hidden" name="cbo_fabric_natu" id="cbo_fabric_natu" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_fabric_natu); ?>"  />
                        <input type="hidden" name="cbouom" id="cbouom" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbouom); ?>"  />
                        <input type="hidden" name="cbo_fabric_source" id="cbo_fabric_source" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_fabric_source); ?>"  />
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value, 'create_po_search_list_view', 'search_div', 'partial_fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center">
                        <strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes" readonly style="width:550px" id="po_number">
                        <input type="hidden" id="po_number_id">
                        <input type="hidden" id="job_no">
                        </td>
                    </tr>
                </table>
                </td>
                </tr>
                <tr>
                <td align="center" >
                <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" />
                </td>
                </tr>
                <tr>
                <td id="search_div" align="center">
                </td>
                </tr>
                <tr>
                <td id="search_div" align="center">
                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                </td>
                </tr>
            </table>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if (str_replace("'","",$data[4])==""){echo "Please Insert Job First."; die;}
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer=""; //{ echo "Please Select Buyer First."; die; }
	if ($data[13]!=0) $uom_cond=" and d.uom='$data[13]'"; else $uom_cond=""; //{ echo "Please Select Buyer First."; die; }

	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[10]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[10]";

	$job_cond="";
	$order_cond="";
	$style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number = '$data[5]'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no ='$data[6]'"; //else  $style_cond="";
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '$data[5]%'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '$data[6]%'  "; //else  $style_cond="";
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]'"; //else  $style_cond="";
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]%'"; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[8]);
	$file_no = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."%' ";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}

	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	 $sql= 'select a.job_no_prefix_num AS "job_no_prefix_num" ,a.buyer_name AS "buyer_name", a.job_no AS "job_no",a.style_ref_no AS "style_ref_no", b.id AS "id",b.po_number AS "po_number", b.po_quantity AS "po_quantity",b.shipment_date AS "shipment_date", b.grouping AS "grouping", b.file_no AS "file_no" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e  where   a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and a.currency_id='.$data[11].' and d.fab_nature_id='.$data[12].'  and d.fabric_source='.$data[14].'     and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$shipment_date. $buyer. $company.  $job_cond. $order_cond. $style_cond. $file_no_cond. $internal_ref_cond. $year_cond. $uom_cond.' group by a.job_no_prefix_num,a.job_no,a.buyer_name,a.style_ref_no,b.id,b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no';

	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Buyer</th>
                <th width="80">Job No</th>
                <th width="100">Style Ref.</th>
                <th width="100">PO number</th>
                <th width="80">PO Qty</th>
                <th width="70">Shipment Date</th>
                <th width="70">Internal Ref</th>
                <th>File No</th>
            </thead>
     	</table>
     </div>
     <div style="width:870px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="list_view">
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bg_color; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['id'].'_'.$row['po_number'].'_'.$row['job_no']; ?>','tr_<? echo $i; ?>');" >
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="80"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
                        <td width="80"><? echo $row['job_no_prefix_num']; ?></td>
                        <td width="100"><? echo $row['style_ref_no'];  ?></td>
						<td width="100"><? echo $row['po_number']; ?></td>
						<td width="80" align="right"><? echo number_format($row['po_quantity']);?> </td>
                        <td width="70"><? echo change_date_format($row['shipment_date']); ?></td>
                        <td width="70"><? echo $row['grouping'];  ?></td>
						<td><? echo $row['file_no'];  ?></td>
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

if ($action=="populate_order_data_from_search_popup"){
	$data=explode("_",$data);
	$fabric_description_array=array();
	if ($data[2]!=0) $uom_cond=" and a.uom='$data[2]'"; else $uom_cond="";
	 $sql='SELECT a.id AS "id", a.body_part_id AS "body_part_id",a.color_type_id AS "color_type_id",a.fabric_description AS "fabric_description", a.gsm_weight AS "gsm_weight" from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id='.$data[1].' and a.fabric_source='.$data[3].' and b.po_break_down_id in ('.$data[0].')'.$uom_cond.' and a.is_deleted=0 and a.status_active=1 group by a.id, a.body_part_id,a.color_type_id,a.fabric_description,a.gsm_weight';
	$sql_data=sql_select($sql);
	foreach($sql_data as $sql_row){
		$fabric_description_array[$sql_row['id']]=$body_part[$sql_row['body_part_id']].', '.$color_type[$sql_row['color_type_id']].', '.$sql_row['fabric_description'].', '.$sql_row['gsm_weight'];
	}
	//print_r($fabric_description_array);die;
	 $cbo_fabric_description=create_drop_down( "cbo_fabric_description",500, $fabric_description_array,"", 1, "-- Select --", $selected, "fnc_generate_booking()",0 );
	 echo "document.getElementById('fabric_description_td').innerHTML = '". $cbo_fabric_description."';\n";
	 exit();
}




if ($action=="generate_fabric_booking")
{
	extract($_REQUEST);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$fabric_cost_dtls_id=implode(",",array_unique(explode(",",str_replace("'","",$cbo_fabric_description))));
	$cbouom=str_replace("'","",$cbouom);
	
	$cu_booking_data_arr=array();
	$sql='select b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id",b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id" ,a.id AS "booking_id",a.fin_fab_qnty AS "fin_fab_qnty",a.grey_fab_qnty AS "grey_fab_qnty",a.dia_width AS "dia_width",TRIM(a.pre_cost_remarks) AS "pre_cost_remarks"  from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and  a.po_break_down_id in('.$txt_order_no_id.') and b.cons>0  and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,b.color_number_id,a.id,a.fin_fab_qnty,a.grey_fab_qnty,a.dia_width,a.pre_cost_remarks';
	$dataArray=sql_select($sql);
	foreach($dataArray as $dataArray_row){
		$cu_booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['cu_booking_qty'][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
	}
	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !=''){
		$condition->po_id("in($txt_order_no_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
	//echo $source_from.'='.$woven_category_id;
	/*if($source_from==1 && $woven_category_id==2)//Booking Rate Come Sorcing page from Lib setting wise
	{
		$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarksSourcing_knitAndwoven_greyAndfinish();
		$po_ids=str_replace("'","",$txt_order_no_id);
		$sql_supp="select c.fabric_id from wo_pre_cost_fabric_supplier c,wo_po_break_down b where c.job_no=b.job_no and b.id in(".$po_ids.") and c.supplier_id in($cbo_supplier_name) and c.is_deleted=0 and c.status_active=1";
		$sql_suppRes=sql_select( $sql_supp ); $fabric_id="";
		foreach($sql_suppRes as $row)
		{
			$fabric_id.=$row[csf('fabric_id')].",";
		}
		unset($sql_suppRes);
		$fabric_ids=chop($fabric_id,',');
		if($db_type==2)
		{
		if($fabric_ids!="") $fab_idCond="and (a.id in ($fabric_ids) or a.sourcing_nominated_supp is null)"; else $fab_idCond=" and (a.sourcing_nominated_supp is null or b.nominated_supp_multi=0)";
		}
		else
		{
			if($fabric_ids!="") $fab_idCond="and (a.id in ($fabric_ids) or a.sourcing_nominated_supp='')"; else $fab_idCond=" and a.sourcing_nominated_supp=''";
		}
		
	}
	else {
		$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
	}*/
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where id in($txt_order_no_id)", "id", "po_number");

	$sql='select a.id AS "id", a.job_no AS "job_no", a.uom AS "uom",a.body_part_id AS "body_part_id", a.color_type_id AS "color_type_id",a.width_dia_type AS "width_dia_type",a.construction AS "construction",a.composition AS "composition",a.gsm_weight AS "gsm_weight", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id",b.dia_width AS "dia_width", TRIM(b.remarks) AS "remarks", c.contrast_color_id AS "contrast_color_id", d.fabric_ref as "fabric_ref" from wo_pre_cost_fabric_cost_dtls a,lib_yarn_count_determina_mst d, wo_pre_cos_fab_co_avg_con_dtls b left join wo_pre_cos_fab_co_color_dtls c on  b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and b.color_number_id=gmts_color_id and c.is_deleted=0 and c.status_active=1 where a.id=b.pre_cost_fabric_cost_dtls_id and a.lib_yarn_count_deter_id=d.id and a.id in('.$fabric_cost_dtls_id.') and b.po_break_down_id in ('.$txt_order_no_id.') and b.cons>0  and a.is_deleted=0 and a.status_active=1 group by a.id,a.job_no,a.uom,a.body_part_id,a.color_type_id,a.width_dia_type,a.construction,a.composition,a.gsm_weight,b.color_number_id, b.po_break_down_id,b.dia_width, b.remarks,c.contrast_color_id,d.fabric_ref';
	//echo $sql; die;
	$sql_data=sql_select($sql);
	$job_level_arr=array();
	foreach($sql_data as $sql_row)
	{
		$pre_cost_fabric_cost_dtls_id=$sql_row['id'];
		$item_color=$sql_row['contrast_color_id'];
		if($item_color== "" || $item_color==0){
			$item_color=$sql_row['color_number_id'];
		}
		if($cbo_fabric_natu==2){
			$req_qty=$req_qty_arr['knit']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['knit']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_qty=number_format($req_qty,8,'.','');
			$req_amt=number_format($req_amt,8,'.','');
			if($req_amt>0 && $req_qty>0)
			{
				$rate=$req_amt/$req_qty;
			}
			else $rate=0;
		}
		if($cbo_fabric_natu==3){
			$req_qty=$req_qty_arr['woven']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['woven']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_qty=number_format($req_qty,8,'.','');
			$req_amt=number_format($req_amt,8,'.','');
			if($req_amt>0 && $req_qty>0)
			{
				$rate=$req_amt/$req_qty;
			}
			else $rate=0;
		}
		if($sql_row['fabric_ref']=='') $sql_row['fabric_ref']='';
		$cu_booking_qty=$cu_booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$sql_row['po_break_down_id']];
		
		$bal_qty=$req_qty-$cu_booking_qty;
		$bal_qty=number_format($bal_qty,8,'.','');
		$bal_amt=$bal_qty*$rate;
		$bal_amt=number_format($bal_amt,8,'.','');

		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['job_no'][$sql_row['po_break_down_id']]=$sql_row['job_no'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_id'][$sql_row['po_break_down_id']]=$sql_row['po_break_down_id'];

		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_number'][$sql_row['po_break_down_id']]=$po_number_arr[$sql_row['po_break_down_id']];
		//=================
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_fabric_cost_dtls_id'][$sql_row['po_break_down_id']]=$pre_cost_fabric_cost_dtls_id;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['body_part_id'][$sql_row['po_break_down_id']]=$sql_row['body_part_id'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['construction'][$sql_row['po_break_down_id']]=$sql_row['construction'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['composition'][$sql_row['po_break_down_id']]=$sql_row['composition'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['gsm_weight'][$sql_row['po_break_down_id']]=$sql_row['gsm_weight'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['dia_width'][$sql_row['po_break_down_id']]=$sql_row['dia_width'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_remarks'][$sql_row['po_break_down_id']]=$sql_row['remarks'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['fabric_ref'][$sql_row['po_break_down_id']]=$sql_row['fabric_ref'];

		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_type_id'][$sql_row['po_break_down_id']]=$sql_row['color_type_id'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['width_dia_type'][$sql_row['po_break_down_id']]=$sql_row['width_dia_type'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['uom'][$sql_row['po_break_down_id']]=$sql_row['uom'];
		//============

		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_number_id'][$sql_row['po_break_down_id']]=$sql_row['color_number_id'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['item_color'][$sql_row['po_break_down_id']]=$item_color;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_qty'][$sql_row['po_break_down_id']]=$req_qty;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_amt'][$sql_row['po_break_down_id']]=$req_amt;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_qty'][$sql_row['po_break_down_id']]=$bal_qty;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_amt'][$sql_row['po_break_down_id']]=$bal_amt;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$sql_row['po_break_down_id']]=$rate;
	}
	//print_r($job_level_arr);
	$uom_unique="";
	$total_qo_qnty=0;
	$total_ac_qnty=0;
	$flag=true;
	?>
	<table width="1780" class="rpt_table" border="0" rules="all">
		<thead>
            <th width="80">Job No</th>
            <th width="100">Po Number</th>
            <th width="100">Body Part</th>
            <th width="70">Color Type</th>
            <th width="80">Dia Width Type</th>
            <th width="100">Construction</th>
            <th width="140">Composition</th>
			<th width="80">Fabric Ref</th>
            <th width="40">Gsm</th>
            <th width="100">Gmts. Color</th>
            <th width="100">Item Color</th>
            <th width="50">Dia</th>
            <th width="100">Process</th>
            <th width="80">Balance Qty</th>
            <th width="80">WO. Qty</th>
            <th width="70">Adj. Qty</th>
            <th width="70">Ac.WO. Qty</th>
            <th width="40">UOM</th>
            <th width="60">Rate</th>
            <th width="90">Amount</th>
            <th>Remark</th>
		</thead>
	</table>
	<table width="1780" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
		<tbody>
			<?
			if(str_replace("'","",$cbo_level)==1)
			{
				$i=1;
				foreach($sql_data as $sql_row)
				{
					$pre_cost_fabric_cost_dtls_id=$sql_row['id'];
					$job_no=$sql_row['job_no'];
					$po_break_down_id=$sql_row['po_break_down_id'];
					$body_part_id=$sql_row['body_part_id'];
					$construction=$sql_row['construction'];
					$compositi=$sql_row['composition'];
					$gsm_weight=$sql_row['gsm_weight'];
					$color_type_id=$sql_row['color_type_id'];
					$width_dia_type=$sql_row['width_dia_type'];
					$fabric_ref=$sql_row['fabric_ref'];

					$color_number_id=$sql_row['color_number_id'];
					$item_color=$sql_row['contrast_color_id'];
					if($item_color== "" || $item_color==0)
					{
						$item_color=$sql_row['color_number_id'];
					}
					$dia_width=$sql_row['dia_width'];
					$pre_cost_remarks=$sql_row['remarks'];

					if($cbo_fabric_natu==2){
						$req_qty=$req_qty_arr['knit']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$req_amt=$req_amount_arr['knit']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$rate=$req_amt/$req_qty;
					}
					if($cbo_fabric_natu==3){
						$req_qty=$req_qty_arr['woven']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$req_amt=$req_amount_arr['woven']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$rate=$req_amt/$req_qty;
					}
					$cu_booking_qty=$cu_booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$sql_row['po_break_down_id']];
					$bal_qty=$req_qty-$cu_booking_qty;
					//$rate = $bal_amt/$bal_qty;
					$bal_amt=$bal_qty*$rate;
					$total_bal_amt += $bal_amt;


					$total_qo_qnty+=$bal_qty;
					$total_ac_qnty+=$bal_qty;

					$uom_i=$sql_row['uom'];
					if($uom_unique=="")
					{
						$uom_unique =$uom_i;
					}
					else if($uom_i!=$uom_unique)
					{
						$flag=false;
					}

					if(number_format($bal_qty,4,'.','') >0)
					{
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
								<td width="80">
									<? echo $job_no; ?>
									<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>"  readonly  />
								</td>
								<td width="100" style="word-break: break-all;word-wrap: break-word;">
									<? echo $po_number_arr[$po_break_down_id]; ?>
									<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_break_down_id;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $body_part[$body_part_id]; ?>
									<input type="hidden" id="txtpre_cost_fabric_cost_dtls_id_<? echo $i;?>" value="<? echo $pre_cost_fabric_cost_dtls_id;?>"  readonly  />
									<input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $body_part_id;?>"  readonly  />
								</td>
								<td width="70" style="word-break:break-all">
									<? echo $color_type[$color_type_id]; ?>
									<input type="hidden" id="txtcolortype_<? echo $i;?>" value="<? echo $color_type_id;?>"  readonly  />
								</td>
								<td width="80" style="word-break:break-all">
									<? echo $fabric_typee[$width_dia_type]; ?>
									<input type="hidden" id="txtwidthtype_<? echo $i;?>" value="<? echo $width_dia_type;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $construction; ?>
									<input type="hidden" id="txtconstruction_<? echo $i;?>" value="<? echo $construction;?>"  readonly  />
								</td>
								<td width="140" style="word-break:break-all">
									<? echo $compositi; ?>
									<input type="hidden" id="txtcompositi_<? echo $i;?>" value="<? echo $compositi;?>"  readonly  />
								</td>
								<td width="80" style="word-break:break-all"><?=$fabric_ref; ?>
									<input type="hidden" id="txtfabricref_<?=$i; ?>" value="<?=$fabric_ref; ?>" readonly />
								</td>
								<td width="40">
									<? echo $gsm_weight; ?>
									<input type="hidden" id="txtgsm_weight_<? echo $i;?>" value="<? echo $gsm_weight;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $color_library[$color_number_id]; ?>
									<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $color_number_id;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $color_library[$item_color]; ?>
									<input type="hidden" id="txtitemcolor_<? echo $i;?>" value="<? echo $item_color;?>"  readonly  />
								</td>
								<td width="50" style="word-break:break-all">
									<? echo $dia_width; ?>
									<input type="hidden" id="txtdia_<? echo $i;?>" value="<? echo $dia_width;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $pre_cost_remarks; ?>
									<input type="hidden" id="process_<? echo $i;?>" value="<? echo $pre_cost_remarks;?>"  readonly  />
								</td>
								<td width="80">	<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalqnty_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>"  readonly  />
									<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qty,4,'.','');?>"  readonly  />

									<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="cuqnty_<? echo $i;?>" value="<? echo number_format($cu_booking_qty,4,'.','');?>"  readonly  />
								</td>
								<td width="80">
									<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" onChange="claculate_acwoQty(<? echo $i; ?>)" />
									<input type="hidden"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
								</td>
								<td width="70">
									<input type="text"  style="width:60px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<? echo $i;?>" value="<? //echo number_format($adj_qty,4,'.','');?>" class="text_boxes_numeric"  onChange="claculate_acwoQty(<? echo $i; ?>)"  />
								</td>
								<td width="70">
									<input type="text"  style="width:60px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric"  readonly />
								</td>
								<td width="40"><? echo $unit_of_measurement[$sql_row['uom']]; ?></td>
								<td width="60">
									<input type="text"  style="width:55px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_amount(<? echo $i; ?>)" data-pre-cost-rate="<? echo number_format($rate,4,'.','');?>" data-current-rate="<? echo number_format($rate,4,'.','');?>"  />
								</td>
								<td width="80">
									<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($bal_amt,4,'.','');?>" class="text_boxes_numeric" readonly  />

								</td>
								<td>
									<input type="text"  style="width:100px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value="<? //echo number_format($bal_amt,4,'.','');?>" />
									<input type="hidden"   id="bookingid_<? echo $i;?>" value="<? //echo number_format($bal_amt,4,'.','');?>"  readonly  />
								</td>
							</tr>
						<?
						$i++;
					}
				}
			}
			if(str_replace("'","",$cbo_level)==2)
			{
				$i=1;
				foreach($job_level_arr as $precost_id)
				{
					foreach($precost_id as $color_id)
					{
						foreach($color_id as $diawith)
						{
							foreach($diawith as $remarks)
							{
								$job_no=implode(",",array_unique($remarks['job_no']));
								$po_break_down_id=implode(",",array_unique($remarks['po_id']));
								$po_number=implode(",",array_unique($remarks['po_number']));
								$pre_cost_fabric_cost_dtls_id=implode(",",array_unique($remarks['pre_cost_fabric_cost_dtls_id']));
								$body_part_id=implode(",",array_unique($remarks['body_part_id']));
								$construction=implode(",",array_unique($remarks['construction']));
								$compositi=implode(",",array_unique($remarks['composition']));
								$gsm_weight=implode(",",array_unique($remarks['gsm_weight']));
								$color_type_id=implode(",",array_unique($remarks['color_type_id']));
								$width_dia_type=implode(",",array_unique($remarks['width_dia_type']));
								$uom=implode(",",array_unique($remarks['uom']));
								$fabric_ref=implode(",",array_unique($remarks['fabric_ref']));
								$color_number_id=implode(",",array_unique($remarks['color_number_id']));
								$item_color=implode(",",array_unique($remarks['item_color']));
								$dia_width=implode(",",array_unique($remarks['dia_width']));
								$pre_cost_remarks=implode(",",array_unique($remarks['pre_cost_remarks']));


								$req_qty=array_sum($remarks['req_qty']);
								$rate=array_sum($remarks['rate']);
								$req_amt=array_sum($remarks['req_amt']);
								$bal_qty=array_sum($remarks['bal_qty']);
								$bal_amt=array_sum($remarks['bal_amt']);
								$rate=$req_amt/$req_qty;
								$bal_amt=$bal_qty*$rate;
								$total_bal_amt += $bal_amt;
								$cu_booking_qty=array_sum($cu_booking_data_arr[$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$pre_cost_remarks]['cu_booking_qty']);

								$total_qo_qnty+=$bal_qty;
								$total_ac_qnty+=$bal_qty;

								$uom_i=$uom;
								if($uom_unique=="")
								{
									$uom_unique =$uom_i;
								}
								else if($uom_i!=$uom_unique)
								{
									$flag=false;
								}

								if(number_format($bal_qty,4,'.','') >0)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
										<td width="80" style="word-break:break-all">
											<? echo $job_no; ?>
											<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>"  readonly  />
										</td>
										<td width="100" style="word-break: break-all;word-wrap: break-word;">
											<a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
											<? //echo $po_number; ?>
											<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_break_down_id;?>"  readonly  />
										</td>
										<td width="100" style="word-break:break-all">
											<? echo $body_part[$body_part_id]; ?>
											<input type="hidden" id="txtpre_cost_fabric_cost_dtls_id_<? echo $i;?>" value="<? echo $pre_cost_fabric_cost_dtls_id;?>"  readonly  />
											<input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $body_part_id;?>"  readonly  />
										</td>
										<td width="70" style="word-break:break-all">
											<? echo $color_type[$color_type_id]; ?>
											<input type="hidden" id="txtcolortype_<? echo $i;?>" value="<? echo $color_type_id;?>"  readonly  />
										</td>
										<td width="80" style="word-break:break-all">
											<? echo $fabric_typee[$width_dia_type]; ?>
											<input type="hidden" id="txtwidthtype_<? echo $i;?>" value="<? echo $width_dia_type;?>"  readonly  />
										</td>
										<td width="100" style="word-break:break-all">
											<? echo $construction; ?>
											<input type="hidden" id="txtconstruction_<? echo $i;?>" value="<? echo $construction;?>"  readonly  />
										</td>
										<td width="140" style="word-break:break-all">
											<? echo $compositi; ?>
											<input type="hidden" id="txtcompositi_<? echo $i;?>" value="<? echo $compositi;?>"  readonly  />
										</td>
										<td width="80" style="word-break:break-all"><?=$fabric_ref; ?>
                                    		<input type="hidden" id="txtfabricref_<?=$i;?>" value="<?=$fabric_ref;?>" readonly />
                                    	</td>
										<td width="40">
											<? echo $gsm_weight; ?>
											<input type="hidden" id="txtgsm_weight_<? echo $i;?>" value="<? echo $gsm_weight;?>"  readonly  />
										</td>
										<td width="100" style="word-break:break-all">
											<? echo $color_library[$color_number_id]; ?>
											<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $color_number_id;?>"  readonly  />
										</td>
										<td width="100" style="word-break:break-all">
											<? echo $color_library[$item_color]; ?>
											<input type="hidden" id="txtitemcolor_<? echo $i;?>" value="<? echo $item_color;?>"  readonly  />
										</td>
										<td width="50" style="word-break:break-all">
											<? echo $dia_width; ?>
											<input type="hidden" id="txtdia_<? echo $i;?>" value="<? echo $dia_width;?>"  readonly  />
										</td>
										<td width="100" style="word-break:break-all">
											<? echo $pre_cost_remarks; ?>
											<input type="hidden" id="process_<? echo $i;?>" value="<? echo $pre_cost_remarks;?>"  readonly  />
										</td>

										<td width="80">	
											<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalqnty_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>"  class="text_boxes_numeric"  readonly  />
											<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qty,4,'.','');?>"  readonly  />
											<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="cuqnty_<? echo $i;?>" value="<? echo number_format($cu_booking_qty,4,'.','');?>"  readonly  />
										</td>
										<td width="80">
											<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_acwoQty(<? echo $i; ?>)"   />
											<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" readonly />

										</td>
										<td width="70">
											<input type="text"  style="width:60px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<? echo $i;?>" value="<? //echo number_format($adj_qty,4,'.','');?>"  class="text_boxes_numeric"  onChange="claculate_acwoQty(<? echo $i; ?>)"  />
										</td>
										<td width="70">
											<input type="text"  style="width:60px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>"  class="text_boxes_numeric"  readonly  />
										</td>
										<td width="40"><? echo $unit_of_measurement[$uom]; ?></td>
										<td width="60">
											<input type="text"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_amount(<? echo $i; ?>)" data-pre-cost-rate="<? echo number_format($rate,4,'.','');?>" data-current-rate="<? echo number_format($rate,4,'.','');?>"  />
										</td>
										<td width="90">
											<input type="text"  style="width:80px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($bal_amt,4,'.','');?>"  class="text_boxes_numeric"  readonly  />
										</td>
										<td>
											<input type="text"  style="width:100px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value="<? //echo number_format($bal_amt,4,'.','');?>"    />
											<input type="hidden"   id="bookingid_<? echo $i;?>" value="<? //echo number_format($bal_amt,4,'.','');?>"  readonly  />

										</td>
									</tr>
									<?
									$i++;
								}
							}
						}
					}
				}
			}
			?>
		</tbody>
	</table>
	<table width="1780" class="rpt_table" border="0" rules="all">
		<tbody>
			<tr>
				<?

					if($flag==true)
					{
						?>
                        	<th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="140">&nbsp;</th>
							<th width="80">&nbsp;</th>
                            <th width="40">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                            <th width="100">&nbsp;</th>
							<th width="80" align="right">Total Amount: &nbsp;&nbsp;&nbsp;
								<input type="hidden" name="uniqe_uom" id="uniqe_uom" value="1">
							</th>
							<th width="80" style="justify-content: right;text-align: right;" > <input type="text" style="width:70px" id="total_qo_qnty" value="<? echo number_format($total_qo_qnty,4,'.',''); ?>" readonly   /></th>
							<th width="70"></th>
							<th width="70" style="justify-content: right;text-align: right;">  <input type="text" style="width:60px" id="total_ac_qnty" value="<? echo number_format($total_ac_qnty,4,'.',''); ?>" readonly   /></th>
							<th width="40"></th>
							<th width="60"></th>
							<th width="90" style="justify-content: right;text-align: right;"><input type="text" style="width:80px" id="total_bal_amt" value="<? echo number_format($total_bal_amt,4,'.',''); ?>" readonly   /></th>
							<th>&nbsp;</th>
						<?
					}
					else
					{
						?>
                        	<th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="140">&nbsp;</th>
							<th width="80">&nbsp;</th>
                            <th width="40">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="40">&nbsp;</th>
							<th width="60" align="right">Total Amount: &nbsp;&nbsp;&nbsp;
								<input type="hidden" name="uniqe_uom" id="uniqe_uom" value="0">
							</th>
							<th width="90" align="right"><input type="text" style="width:80px" id="total_bal_amt" value="<? echo number_format($total_bal_amt,4,'.',''); ?>" readonly   /></th>
							<th>&nbsp;</th>
						<?	
					}
				?>
			</tr>
		</tbody>
	</table>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	<input type='hidden' id='json_data' name="json_data" value='<? echo json_encode($job_level_arr); ?>'/>
	<?
	exit();
}

if ($action=="show_fabric_booking")
{
	echo load_html_head_contents("Partial Booking Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$fabric_cost_dtls_id=implode(",",array_unique(explode(",",str_replace("'","",$cbo_fabric_description))));

	$cbouom=str_replace("'","",$cbouom);

	 $cu_booking_data_arr=array();
	 $sql='select b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id",b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id" ,a.id AS "booking_id",a.fin_fab_qnty AS "fin_fab_qnty",a.grey_fab_qnty AS "grey_fab_qnty", a.adjust_qty AS "adjust_qty",a.dia_width AS "dia_width" ,a.pre_cost_remarks AS "pre_cost_remarks"  from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id  and a.dia_width=b.dia_width and  a.po_break_down_id in('.$txt_order_no_id.') and b.cons>0  and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,b.color_number_id,a.id,a.fin_fab_qnty,a.grey_fab_qnty,a.adjust_qty,a.dia_width,a.pre_cost_remarks';
	 //echo $sql;
	$dataArray=sql_select($sql);
	foreach($dataArray as $dataArray_row){
		$cu_booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['cu_booking_qty'][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
	}

	 $booking_data_arr=array();
	 $sql='select b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id",b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id" ,a.id AS "booking_id",a.fin_fab_qnty AS "fin_fab_qnty",a.grey_fab_qnty AS "grey_fab_qnty", a.rate AS "rate", a.amount AS "amount", a.adjust_qty AS "adjust_qty",a.remark AS "remark",a.dia_width AS "dia_width",a.pre_cost_remarks AS "pre_cost_remarks"  from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and  a.po_break_down_id in('.$txt_order_no_id.') and b.cons>0  and booking_no='.$txt_booking_no.' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,b.color_number_id,a.id,a.fin_fab_qnty,a.grey_fab_qnty,a.rate,a.amount, a.adjust_qty,a.remark,a.dia_width,a.pre_cost_remarks';

	$dataArray=sql_select($sql);
	foreach($dataArray as $dataArray_row){
		$booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['booking_qty'][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
		$booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['ac_booking_qty'][$dataArray_row['po_break_down_id']]+=$dataArray_row['fin_fab_qnty'];

		$booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['rate'][$dataArray_row['po_break_down_id']]=$dataArray_row['rate'];
		$booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['amount'][$dataArray_row['po_break_down_id']]+=$dataArray_row['amount'];
		$booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['adjust_qty'][$dataArray_row['po_break_down_id']]+=$dataArray_row['adjust_qty'];
		$booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['remark'][$dataArray_row['po_break_down_id']]=$dataArray_row['remark'];

		$booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['booking_id'][$dataArray_row['po_break_down_id']]=$dataArray_row['booking_id'];
	}

	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !=''){
		$condition->po_id("in($txt_order_no_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);

	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where id in($txt_order_no_id)", "id", "po_number");

	 $sql='select a.id AS "id", a.job_no AS "job_no", a.uom AS "uom",a.body_part_id AS "body_part_id", a.color_type_id AS "color_type_id",a.width_dia_type AS "width_dia_type",a.construction AS "construction",a.composition AS "composition",a.gsm_weight AS "gsm_weight", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id", b.dia_width AS "dia_width",b.remarks AS "remarks", c.contrast_color_id AS "contrast_color_id",d.fabric_ref as "fabric_ref" from wo_pre_cost_fabric_cost_dtls a,lib_yarn_count_determina_mst d, wo_pre_cos_fab_co_avg_con_dtls b left join wo_pre_cos_fab_co_color_dtls c on  b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and b.color_number_id=gmts_color_id and c.is_deleted=0 and c.status_active=1 where a.id=b.pre_cost_fabric_cost_dtls_id and a.lib_yarn_count_deter_id=d.id and  a.id in('.$fabric_cost_dtls_id.') and b.po_break_down_id in ('.$txt_order_no_id.') and b.cons>0 and a.is_deleted=0 and a.status_active=1 group by a.id,a.job_no,a.uom,a.body_part_id,a.color_type_id,a.width_dia_type,a.construction,a.composition,a.gsm_weight,b.color_number_id, b.po_break_down_id,b.dia_width,b.remarks ,c.contrast_color_id,d.fabric_ref';
    //echo $sql; die;
	$sql_data=sql_select($sql);
	$job_level_arr=array();
	foreach($sql_data as $sql_row)
	{
		$pre_cost_fabric_cost_dtls_id=$sql_row['id'];
		$item_color=$sql_row['contrast_color_id'];
		if($item_color== "" || $item_color==0){
			$item_color=$sql_row['color_number_id'];
		}
		if($cbo_fabric_natu==2){
			$req_qty=$req_qty_arr['knit']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['knit']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_qty=number_format($req_qty,8,'.','');
			$req_amt=number_format($req_amt,8,'.','');
			$rate=$req_amt/$req_qty;
		}
		if($cbo_fabric_natu==3){
			$req_qty=$req_qty_arr['woven']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['woven']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_qty=number_format($req_qty,8,'.','');
			$req_amt=number_format($req_amt,8,'.','');
			$rate=$req_amt/$req_qty;
		}
		$cu_booking_qty=$cu_booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$sql_row['po_break_down_id']];
		$booking_qty=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_qty'][$sql_row['po_break_down_id']];
		$ac_booking_qty=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['ac_booking_qty'][$sql_row['po_break_down_id']];
		$adjust_qty=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['adjust_qty'][$sql_row['po_break_down_id']];
		$remark=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['remark'][$sql_row['po_break_down_id']];
		$rate=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$sql_row['po_break_down_id']];
		$amount=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['amount'][$sql_row['po_break_down_id']];

		$booking_id=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_id'][$sql_row['po_break_down_id']];

		$bal_qty=$req_qty-$cu_booking_qty;
		$bal_qty=number_format($bal_qty,8,'.','');
		$bal_amt=$bal_qty*$rate;
		
		$bal_amt=number_format($bal_amt,8,'.','');

		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['job_no'][$sql_row['po_break_down_id']]=$sql_row['job_no'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_id'][$sql_row['po_break_down_id']]=$sql_row['po_break_down_id'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_number'][$sql_row['po_break_down_id']]=$po_number_arr[$sql_row['po_break_down_id']];
		//=================
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_fabric_cost_dtls_id'][$sql_row['po_break_down_id']]=$pre_cost_fabric_cost_dtls_id;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['body_part_id'][$sql_row['po_break_down_id']]=$sql_row['body_part_id'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['construction'][$sql_row['po_break_down_id']]=$sql_row['construction'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['composition'][$sql_row['po_break_down_id']]=$sql_row['composition'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['gsm_weight'][$sql_row['po_break_down_id']]=$sql_row['gsm_weight'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['dia_width'][$sql_row['po_break_down_id']]=$sql_row['dia_width'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_remarks'][$sql_row['po_break_down_id']]=$sql_row['remarks'];
	    $job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_type_id'][$sql_row['po_break_down_id']]=$sql_row['color_type_id'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['width_dia_type'][$sql_row['po_break_down_id']]=$sql_row['width_dia_type'];
		//============
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_number_id'][$sql_row['po_break_down_id']]=$sql_row['color_number_id'];
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['item_color'][$sql_row['po_break_down_id']]=$item_color;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_qty'][$sql_row['po_break_down_id']]=$req_qty;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_amt'][$sql_row['po_break_down_id']]=$req_amt;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_qty'][$sql_row['po_break_down_id']]=$bal_qty;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_amt'][$sql_row['po_break_down_id']]=$bal_amt;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['fabric_ref'][$sql_row['po_break_down_id']]=$sql_row['fabric_ref'];

		//$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']]['rate'][$sql_row['po_break_down_id']]=$rate;

		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_id'][$sql_row['po_break_down_id']]=$booking_id;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_qty'][$sql_row['po_break_down_id']]=$booking_qty;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['ac_booking_qty'][$sql_row['po_break_down_id']]=$ac_booking_qty;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['adjust_qty'][$sql_row['po_break_down_id']]=$adjust_qty;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['remark'][$sql_row['po_break_down_id']]=$remark;


		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$sql_row['po_break_down_id']]=$rate;
		$job_level_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['amount'][$sql_row['po_break_down_id']]=$amount;
	}

	$uom_unique="";
	$total_qo_qnty=0;
	$total_ac_qnty=0;
	$flag=true;
	?>
	<div>
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<table width="1800" class="rpt_table" border="0" rules="all">
			<thead>
				<th width="80">Job No</th>
				<th width="100">Po Number</th>
				<th width="100">Body Part</th>
				<th width="70">Color Type</th>
				<th width="80">Dia Width Type</th>
				<th width="100">Construction</th>
				<th width="140">Composition</th>
				<th width="100">Fabric Ref</th>
				<th width="40">Gsm</th>
				<th width="100">Gmts. Color</th>
				<th width="100">Item Color</th>
				<th width="50">Dia</th>
				<th width="100">Process</th>
				<th width="80">Balance Qty</th>
				<th width="80">WO. Qty</th>
				<th width="70">Adj. Qty</th>
				<th width="70">Ac.WO. Qty</th>
				<th width="40">UOM</th>
				<th width="60">Rate</th>
				<th width="90">Amount</th>
				<th>Remark</th>
			</thead>
		</table>
		<table width="1800" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
			<tbody>
				<?
				if(str_replace("'","",$cbo_level)==1)
				{
					$i=1;
					foreach($sql_data as $sql_row)
					{
						$pre_cost_fabric_cost_dtls_id=$sql_row['id'];
						$job_no=$sql_row['job_no'];
						$po_break_down_id=$sql_row['po_break_down_id'];
						$body_part_id=$sql_row['body_part_id'];
						$construction=$sql_row['construction'];
						$compositi=$sql_row['composition'];
						$gsm_weight=$sql_row['gsm_weight'];
						$color_type_id=$sql_row['color_type_id'];
						$width_dia_type=$sql_row['width_dia_type'];
						$fabric_ref=$sql_row['fabric_ref'];
						$color_number_id=$sql_row['color_number_id'];
						$item_color=$sql_row['contrast_color_id'];
						if($item_color== "" || $item_color==0){
						$item_color=$sql_row['color_number_id'];
						}
						$dia_width=$sql_row['dia_width'];
						$pre_cost_remarks=$sql_row['remarks'];


						if($cbo_fabric_natu==2){
							$req_qty=$req_qty_arr['knit']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							$req_amt=$req_amount_arr['knit']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							$rate=$req_amt/$req_qty;
						}
						if($cbo_fabric_natu==3){
							$req_qty=$req_qty_arr['woven']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							$req_amt=$req_amount_arr['woven']['grey'][$sql_row['po_break_down_id']][$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							$rate=$req_amt/$req_qty;

						}
						$cu_booking_qty=$cu_booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$sql_row['po_break_down_id']];
						$booking_qty=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_qty'][$sql_row['po_break_down_id']];
						$ac_booking_qty=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['ac_booking_qty'][$sql_row['po_break_down_id']];
						$adjust_qty=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['adjust_qty'][$sql_row['po_break_down_id']];
						$remark=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['remark'][$sql_row['po_break_down_id']];

						$rate=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$sql_row['po_break_down_id']];
						$amount=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['amount'][$sql_row['po_break_down_id']];
						$booking_id=$booking_data_arr[$pre_cost_fabric_cost_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_id'][$sql_row['po_break_down_id']];
						//$rate=$amount/$ac_booking_qty;
						$bal_qty=$req_qty-$cu_booking_qty;
						$bal_amt=$bal_qty*$amount;
						$pre_cost_rate=$req_amt/$req_qty;
						$total_amount += $amount;

						$total_qo_qnty+=$booking_qty;
						$total_ac_qnty+=$ac_booking_qty;

						$uom_i=$sql_row['uom'];
						if($uom_unique=="")
						{
							$uom_unique =$uom_i;
						}
						else if($uom_i!=$uom_unique)
						{
							$flag=false;
						}

						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
								<td width="80" style="word-break:break-all" title="<? echo $rate.'-'.$amount; ?>">
									<? echo $job_no; ?>
									<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>"  readonly  />
								</td>
								<td width="100" style="word-break: break-all;word-wrap: break-word;">
									<? echo $po_number_arr[$po_break_down_id]; ?>
									<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_break_down_id;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $body_part[$body_part_id]; ?>
									<input type="hidden" id="txtpre_cost_fabric_cost_dtls_id_<? echo $i;?>" value="<? echo $pre_cost_fabric_cost_dtls_id;?>"  readonly  />
									<input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $body_part_id;?>"  readonly  />
								</td>
								<td width="70" style="word-break:break-all">
									<? echo $color_type[$color_type_id]; ?>
									<input type="hidden" id="txtcolortype_<? echo $i;?>" value="<? echo $color_type_id;?>"  readonly  />
								</td>
								<td width="80" style="word-break:break-all">
									<? echo $fabric_typee[$width_dia_type]; ?>
									<input type="hidden" id="txtwidthtype_<? echo $i;?>" value="<? echo $width_dia_type;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $construction; ?>
									<input type="hidden" id="txtconstruction_<? echo $i;?>" value="<? echo $construction;?>"  readonly  />
								</td>
								<td width="140" style="word-break:break-all">
									<? echo $compositi; ?>
									<input type="hidden" id="txtcompositi_<? echo $i;?>" value="<? echo $compositi;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all"><?=$fabric_ref; ?>
									<input type="hidden" id="txtfabricref_<?=$i; ?>" value="<?=$fabric_ref;?>" readonly />
								</td>
								<td width="40" >
									<? echo $gsm_weight; ?>
									<input type="hidden" id="txtgsm_weight_<? echo $i;?>" value="<? echo $gsm_weight;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $color_library[$color_number_id]; ?>
									<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $color_number_id;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $color_library[$item_color]; ?>
									<input type="hidden" id="txtitemcolor_<? echo $i;?>" value="<? echo $item_color;?>"  readonly  />
								</td>
								<td width="50">
									<? echo $dia_width; ?>
									<input type="hidden" id="txtdia_<? echo $i;?>" value="<? echo $dia_width;?>"  readonly  />
								</td>
								<td width="100" style="word-break:break-all">
									<? echo $pre_cost_remarks; ?>
									<input type="hidden" id="process_<? echo $i;?>" value="<? echo $pre_cost_remarks;?>"  readonly  />
								</td>
								<td width="80">	
									<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalqnty_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric"   readonly  />
									<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qty,4,'.','');?>" class="text_boxes_numeric"  readonly  />
									<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="cuqnty_<? echo $i;?>" value="<? echo number_format($cu_booking_qty,4,'.','');?>" class="text_boxes_numeric"   readonly  />
								</td>
								<td width="80">
									<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($booking_qty,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_acwoQty(<? echo $i; ?>)"  />
									<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($booking_qty,4,'.','');?>" class="text_boxes_numeric"  readonly />
								</td>
								<td width="70">
									<input type="text"  style="width:60px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<? echo $i;?>" value="<? echo number_format($adjust_qty,4,'.','');?>" class="text_boxes_numeric"   onChange="claculate_acwoQty(<? echo $i; ?>)"  />
								</td>
								<td width="70">
									<input type="text"  style="width:60px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<? echo $i;?>" value="<? echo number_format($ac_booking_qty,4,'.','');?>" class="text_boxes_numeric"   readonly  />
								</td>
								<td width="40"><? echo $unit_of_measurement[$sql_row['uom']]; ?></td>
								<td width="60">
									<input type="text"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>" class="text_boxes_numeric"  onChange="claculate_amount(<? echo $i; ?>)" data-pre-cost-rate="<? echo number_format($pre_cost_rate,4,'.','');?>" data-current-rate="<? echo number_format($rate,4,'.','');?>"  />
								</td>
								<td width="90">
									<input type="text"  style="width:80px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>" class="text_boxes_numeric"   readonly  />

								</td>
								<td>
									<input type="text" style="width:100px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value="<? echo $remark;?>"    />
									<input type="hidden"   id="bookingid_<? echo $i;?>" value="<? echo $booking_id;?>"  readonly  />

								</td>
							</tr>
						<?
						$i++;
					}
				}
				if(str_replace("'","",$cbo_level)==2)
				{
					$i=1;
					foreach($job_level_arr as $precost_id)
					{
						foreach($precost_id as $color_id)
						{
							foreach($color_id as $diawith)
							{
								foreach($diawith as $remarks)
								{
									$job_no=implode(",",array_unique($remarks['job_no']));
									$po_break_down_id=implode(",",array_unique($remarks['po_id']));
									$po_number=implode(",",array_unique($remarks['po_number']));
									$pre_cost_fabric_cost_dtls_id=implode(",",array_unique($remarks['pre_cost_fabric_cost_dtls_id']));
									$body_part_id=implode(",",array_unique($remarks['body_part_id']));
									$construction=implode(",",array_unique($remarks['construction']));
									$compositi=implode(",",array_unique($remarks['composition']));
									$gsm_weight=implode(",",array_unique($remarks['gsm_weight']));
									$color_type_id=implode(",",array_unique($remarks['color_type_id']));
									$width_dia_type=implode(",",array_unique($remarks['width_dia_type']));
									$color_number_id=implode(",",array_unique($remarks['color_number_id']));
									$item_color=implode(",",array_unique($remarks['item_color']));
									$dia_width=implode(",",array_unique($remarks['dia_width']));
									$pre_cost_remarks=implode(",",array_unique($remarks['pre_cost_remarks']));
									$fabric_ref=implode(",",array_unique($remarks['fabric_ref']));
									$booking_id=implode(",",array_unique($remarks['booking_id']));
									$req_qty=array_sum($remarks['req_qty']);
									$req_amt=array_sum($remarks['req_amt']);
									$bal_qty=array_sum($remarks['bal_qty']);
									//$bal_amt=array_sum($color_id['bal_amt']);
									$booking_qty=array_sum($remarks['booking_qty']);
									$ac_booking_qty=array_sum($remarks['ac_booking_qty']);
									$adjust_qty=array_sum($remarks['adjust_qty']);
									$remark=implode(",",array_unique($remarks['remark']));

									$rate=array_sum($remarks['rate']);
									$amount=array_sum($remarks['amount']);
									$rate=$amount/$ac_booking_qty;

									$pre_cost_rate=$req_amt/$req_qty;
									$cu_booking_qty=array_sum($cu_booking_data_arr[$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$pre_cost_remarks]['cu_booking_qty']);
									$total_amount += $amount;

									$total_qo_qnty+=$booking_qty;
									$total_ac_qnty+=$ac_booking_qty;

									$uom_i=$sql_row['uom'];
									if($uom_unique=="")
									{
										$uom_unique =$uom_i;
									}
									else if($uom_i!=$uom_unique)
									{
										$flag=false;
									}

									?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
											<td width="80">
												<? echo $job_no; ?>
												<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>"  readonly  />
											</td>
											<td width="100" style="word-break: break-all;word-wrap: break-word;">
												<a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
												<? //echo $po_number; ?>
												<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_break_down_id;?>"  readonly  />
											</td>
											<td width="100" style="word-break:break-all">
												<? echo $body_part[$body_part_id]; ?>
												<input type="hidden" id="txtpre_cost_fabric_cost_dtls_id_<? echo $i;?>" value="<? echo $pre_cost_fabric_cost_dtls_id;?>"  readonly  />
												<input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $body_part_id;?>"  readonly  />
											</td>
											<td width="70" style="word-break:break-all">
												<? echo $color_type[$color_type_id]; ?>
												<input type="hidden" id="txtcolortype_<? echo $i;?>" value="<? echo $color_type_id;?>"  readonly  />
											</td>
											<td width="80" style="word-break:break-all">
												<? echo $fabric_typee[$width_dia_type]; ?>
												<input type="hidden" id="txtwidthtype_<? echo $i;?>" value="<? echo $width_dia_type;?>"  readonly  />
											</td>
											<td width="100" style="word-break:break-all">
												<? echo $construction; ?>
												<input type="hidden" id="txtconstruction_<? echo $i;?>" value="<? echo $construction;?>"  readonly  />
											</td>
											<td width="140" style="word-break:break-all">
												<? echo $compositi; ?>
												<input type="hidden" id="txtcompositi_<? echo $i;?>" value="<? echo $compositi;?>"  readonly  />
											</td>
											<td width="100" style="word-break:break-all"><?=$fabric_ref; ?>
												<input type="hidden" id="txtfabricref_<?=$i; ?>" value="<?=$fabric_ref; ?>" readonly />
											</td>
											<td width="40" style="word-break:break-all">
												<? echo $gsm_weight; ?>
												<input type="hidden" id="txtgsm_weight_<? echo $i;?>" value="<? echo $gsm_weight;?>"  readonly  />
											</td>
											<td width="100" style="word-break:break-all">
												<? echo $color_library[$color_number_id]; ?>
												<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $color_number_id;?>"  readonly  />
											</td>
											<td width="100" style="word-break:break-all">
												<? echo $color_library[$item_color]; ?>
												<input type="hidden" id="txtitemcolor_<? echo $i;?>" value="<? echo $item_color;?>"  readonly  />
											</td>
											<td width="50">
												<? echo $dia_width; ?>
												<input type="hidden" id="txtdia_<? echo $i;?>" value="<? echo $dia_width;?>"  readonly  />
											</td>
											<td width="100" style="word-break:break-all">
												<? echo $pre_cost_remarks; ?>
												<input type="hidden" id="process_<? echo $i;?>" value="<? echo $pre_cost_remarks;?>"  readonly  />
											</td>
											<td width="80">	
												<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalqnty_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric"  readonly  />
												<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qty,4,'.','');?>" class="text_boxes_numeric"   readonly  />
												<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="cuqnty_<? echo $i;?>" value="<? echo number_format($cu_booking_qty,4,'.','');?>" class="text_boxes_numeric"  readonly  />
											</td>
											<td width="80">
												<input type="text"  style="width:70px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($booking_qty,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_acwoQty(<? echo $i; ?>)"   />
												<input type="hidden"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($booking_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
											</td>
											<td width="70">
												<input type="text"  style="width:60px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<? echo $i;?>" value="<? echo number_format($adjust_qty,4,'.','');?>" class="text_boxes_numeric"  onChange="claculate_acwoQty(<? echo $i; ?>)" />
											</td>
											<td width="70">
												<input type="text"  style="width:60px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<? echo $i;?>" value="<? echo number_format($ac_booking_qty,4,'.','');?>" class="text_boxes_numeric" readonly   />
											</td>
											
											<td width="40"><? echo $unit_of_measurement[$sql_row['uom']]; ?></td>
											<td width="60">
												<input type="text"  style="width:50px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>" class="text_boxes_numeric"  onChange="claculate_amount(<? echo $i; ?>)" data-pre-cost-rate="<? echo number_format($pre_cost_rate,4,'.','');?>" data-current-rate="<? echo number_format($rate,4,'.','');?>" />
											</td>
											<td width="90">
												<input type="text"  style="width:80px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  class="text_boxes_numeric" readonly  />
											</td>
											<td>
												<input type="text"  style="width:100px;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value="<? echo $remark;?>"    />
												<input type="hidden"   id="bookingid_<? echo $i;?>" value="<? echo $booking_id;?>"  readonly  />

											</td>
										</tr>
									<?
									$i++;
								}
							}
						}
					}
				}
				?>
			</tbody>
		</table>
		<table width="1800" class="rpt_table" border="0" rules="all">
			<thead>
				<?
				if($flag==true)
				{
					?>
					<th width="80"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="140"></th>
					<th width="100"></th>
					<th width="40"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width="80">Total <input type="hidden" name="uniqe_uom" id="uniqe_uom" value="1"></th>
					<th width="80" style="justify-content: right;text-align: right;" ><input type="text" style="width:70px" id="total_qo_qnty" value="<? echo number_format($total_qo_qnty,4,'.',''); ?>" readonly   /></th>
					<th width="70"></th>
					<th width="70" style="justify-content: right;text-align: right;" > <input type="text" style="width:60px" id="total_ac_qnty" value="<? echo number_format($total_ac_qnty,4,'.',''); ?>" readonly   /></th>
					<th width="40"></th>
					<th width="60"></th>
					<th width="90"><input type="text" style="width:80px" id="total_bal_amt" value="<? echo number_format($total_amount,4,'.',''); ?>" readonly   /></th>
					<th></th>
					<?
				}
				else
				{
					?>
					<th width="80"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="140"></th>
					<th width="100"></th>
					<th width="40"></th>
					<th width="100"></th>
					<th width="120"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="40"></th>
					<th width="60">Total Amount <input type="hidden" name="uniqe_uom" id="uniqe_uom" value="0"></th>
					<th width="90"><input type="text" style="width:80px" id="total_bal_amt" value="<? echo number_format($total_amount,4,'.',''); ?>" readonly   /></th>
					<th></th>
					<?
				}
				?>
			</thead>
		</table>
		<input type='hidden' id='json_data' name="json_data" value='<? echo json_encode($job_level_arr); ?>'/>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	<?
	exit();
}

if($action=="show_fabric_booking_list")
{
	extract($_REQUEST);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$pre_cost_fabric_cost_dtls_id=str_replace("'","",$cbo_fabric_description);
	$cbouom=str_replace("'","",$cbouom);
	$cbo_level=str_replace("'","",$cbo_level);

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
	$job_level_arr=array();
	$fabric_description_array=array();
	$uom_list_arr=array();
	$color_Arr=array();
	$Dia_Arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");

	$composition_sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id as master_id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=426 order by a.id,b.id";
		
	$data_array=sql_select($composition_sql);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
			if(array_key_exists($row[csf('master_id')],$composition_arr))
			{
				$composition_arr[$row[csf('master_id')]]=$composition_arr[$row[csf('master_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('master_id')]]=$composition[$row[csf('copmposition_id')]].$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
		}
	}
	 $sql='select a.id AS "id",a.job_no AS "job_no", a.po_break_down_id AS "po_break_down_id",a.grey_fab_qnty AS "grey_fab_qnty",a.fin_fab_qnty AS "fin_fab_qnty",a.adjust_qty "adjust_qty",a.rate AS "rate",a.amount AS "amount",a.gmts_color_id AS "gmts_color_id",a.dia_width AS "dia_width", b.id AS "pre_cost_fabric_cost_dtls_id", b.body_part_id AS "body_part_id",b.color_type_id AS "color_type_id",b.fabric_description AS "fabric_description", b.gsm_weight AS "gsm_weight",b.uom AS "uom", c.type as "type", c.fabric_ref as "fabric_ref", c.design as "design", c.id as "copmposition_id", c.construction as "construction",c.rd_no as "rd_no" from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b,lib_yarn_count_determina_mst c  where a.pre_cost_fabric_cost_dtls_id=b.id and c.id=b.lib_yarn_count_deter_id and a.status_active =1 and a.is_deleted=0  and a.booking_no='.$txt_booking_no.'';

	$dataArray=sql_select($sql);
	foreach($dataArray as $sql_row)
	{
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['job_no'][$sql_row['id']]=$sql_row['job_no'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['po_id'][$sql_row['id']]=$sql_row['po_break_down_id'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['po_number'][$sql_row['id']]=$po_number_arr[$sql_row['po_break_down_id']];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['booking_id'][$sql_row['id']]=$sql_row['id'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['grey_fab_qnty'][$sql_row['id']]+=$sql_row['grey_fab_qnty'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['fin_fab_qnty'][$sql_row['id']]+=$sql_row['fin_fab_qnty'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['adjust_qty'][$sql_row['id']]+=$sql_row['adjust_qty'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['amount'][$sql_row['id']]+=$sql_row['amount'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['fabric_ref'][$sql_row['id']]=$sql_row['fabric_ref'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['rd_no'][$sql_row['id']]=$sql_row['rd_no'];
		//$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['amount'][$sql_row['id']]+=$sql_row['amount'];
		//$fabric_description_array[$sql_row['pre_cost_fabric_cost_dtls_id']]=$body_part[$sql_row['body_part_id']].','.$sql_row['type'].', '.$sql_row['construction'].', '.$sql_row['design'].', '.$composition_arr[$sql_row['copmposition_id']];

		 $fabric_description_array[$sql_row['pre_cost_fabric_cost_dtls_id']]=$body_part[$sql_row['body_part_id']].', '.$color_type[$sql_row['color_type_id']].', '.$sql_row['fabric_description'].', '.$sql_row['gsm_weight'].', '.$unit_of_measurement[$sql_row['uom']]; 
		$uom_list_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]=$sql_row['uom'];
		$color_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['gmts_color_id']]=$color_library[$sql_row['gmts_color_id']];
		$Dia_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['dia_width']]=$sql_row['dia_width'];
		$rd_no_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]=$sql_row['rd_no'];
	}

	$uom_unique="";
	$total_qo_qnty=0;
	$total_ac_qnty=0;
	$flag=true;
	?>
	<table width="1210" class="rpt_table" border="0" rules="all">
		<thead>
			<th width="80"></th>
			<th width="80">Job No</th>
			<th width="100">Po No</th>
			<th width="100">Fabric Ref</th>
			<th width="250">Fab. Description</th>
			<th width="80">Gmts Color</th>
			<th width="60">Dia</th>
			<th width="80">WO. Qnty</th>
			<th width="70">Adj. Qnty</th>
			<th width="70">Ac. Wo. Qnty</th>
			<th width="60">Rate</th>
			<th width="80">Amount</th>
			<th>&nbsp;</th>
		</thead>
	</table>
	<div style=" max-height:200px; overflow-y:scroll; width:1210px"  align="left">
		<table width="1193" class="rpt_table" id="tbl_fabric_booking_list" border="0" rules="all">
			<tbody>
				<?
				if($cbo_level==11)
				{
					foreach($dataArray as $row)
					{
						$job_no=$row['job_no'];
						$po_break_down_id=$row['po_break_down_id'];
						$po_number=$po_number_arr[$row['po_break_down_id']];
						$fabric_des=$fabric_description_array[$row['pre_cost_fabric_cost_dtls_id']];
						$GmtColor=$color_Arr[$row['pre_cost_fabric_cost_dtls_id']];
						$booking_id=$row['id'];
						$grey_fab_qnty=$row['grey_fab_qnty'];
						$adjust_qty=$row['adjust_qty'];
						$fin_fab_qnty=$row['fin_fab_qnty'];
						$amount=$row['amount'];
						$rate=$amount/$fin_fab_qnty;

						$total_qo_qnty+=$grey_fab_qnty;
						$total_ac_qnty+=$fin_fab_qnty;

						$uom_i=$uom_list_arr[$row['pre_cost_fabric_cost_dtls_id']];
						if($uom_unique=="") $uom_unique =$uom_i; else if($uom_i!=$uom_unique) $flag=false;
						?>
						<tr>
							<td width="80"><a href="#" onClick="set_data('<? echo $po_break_down_id;  ?>','<? echo $po_number; ?>','<? echo $row['pre_cost_fabric_cost_dtls_id']; ?>','<? echo $booking_id?>')">Edit</a></td>
							<td width="80"><? echo $job_no; ?></td>
							<td width="100" style="word-break: break-all;word-wrap: break-word;"><a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a></td>
							<td width="100"><? echo $fabric_des; ?></td>
							<td width="250"><? echo $fabric_des; ?></td>
							<td width="80" align="right"><? echo number_format($grey_fab_qnty,4,'.',''); ?></td>
							<td width="100" align="right">
								<? echo number_format($grey_fab_qnty,4,'.','');?>
							</td>
							<td width="100" align="right">
								<? echo number_format($adjust_qty,4,'.','');?>
							</td>
							<td width="100" align="right">
								<? echo number_format($fin_fab_qnty,4,'.','');?>
							</td>
							<td width="60" align="right">
								<? echo number_format($rate,4,'.','');?>
							</td>
							<td width="80" align="right">
								<? 
								echo number_format($amount,4,'.','');
								$total_amount += $amount;
								?>
							</td>
							<td>
								<a href="#" onClick="deletedata('<? echo $po_break_down_id;  ?>','<? echo $po_number; ?>','<? echo $row['pre_cost_fabric_cost_dtls_id']; ?>','<? echo $booking_id?>' )">Delete</a>
							</td>
						</tr>
						<?
					}
				}
				if($cbo_level==2 || $cbo_level==1)
				{
					$i=1;
					foreach($job_level_arr as $key=>$precost_id)
					{
						$job_no=implode(",",array_unique($precost_id['job_no']));
						$po_break_down_id=implode(",",array_unique($precost_id['po_id']));
						$po_number=implode(",",array_unique($precost_id['po_number']));
						$grey_fab_qnty=array_sum($precost_id['grey_fab_qnty']);
						$adjust_qty=array_sum($precost_id['adjust_qty']);
						$fin_fab_qnty=array_sum($precost_id['fin_fab_qnty']);
						$booking_id=implode(",",array_unique($precost_id['booking_id']));
						$fabric_ref=implode(",",array_unique($precost_id['fabric_ref']));
						//$rate=array_sum($precost_id['rate']);
						$amount=array_sum($precost_id['amount']);
						$rate=$amount/$fin_fab_qnty;

						$total_qo_qnty+=$grey_fab_qnty;
						$total_ac_qnty+=$fin_fab_qnty;

						$uom_i=$uom_list_arr[$key];
						if($uom_unique=="") $uom_unique =$uom_i; else if($uom_i!=$uom_unique) $flag=false;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" >
							<td width="80" align="center">
								<a href="#" onClick="set_data('<? echo $po_break_down_id;  ?>','<? echo $po_number; ?>','<? echo $key; ?>','<? echo $booking_id?>')">Edit</a>
							</td>
							<td width="80"><? echo $job_no; ?></td>
							<td width="100" style="word-break: break-all;word-wrap: break-word;" align="center">
								<a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
							</td>
							<td width="100"><? echo $fabric_ref; ?></td>
							<td width="250" style="word-break: break-all;word-wrap: break-word"><? echo $fabric_description_array[$key]; ?></td>
							<td width="80" style="word-break: break-all;word-wrap: break-word"><? echo implode(",",$color_Arr[$key]); ?></td>
							<td width="60" style="word-break: break-all;word-wrap: break-word"><? echo implode(",",$Dia_Arr[$key]); ?></td>
							<td width="80" align="right"><? echo number_format($grey_fab_qnty,4,'.',''); ?></td>
							<td width="70" align="right"><? echo number_format($adjust_qty,4,'.',''); ?></td>
							<td width="70" align="right"><? echo number_format($fin_fab_qnty,4,'.',''); ?></td>
							<td width="60" align="right"><? echo number_format($rate,4,'.',''); ?></td>
							<td width="80" align="right">
								<? 
								echo number_format($amount,4,'.','');
								$total_amount += $amount;
								?>
							</td>
							<td align="center">
								<a href="#" onClick="deletedata('<? echo $po_break_down_id;  ?>','<? echo $po_number; ?>','<? echo $key; ?>','<? echo $booking_id?>')">Delete</a>
							</td>
						</tr>
						<?
					
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<?php 
						if($flag==true)
						{
							?>
								<th colspan="7" align="right">Total Amout</th>
								<th align="right"><? echo number_format($total_qo_qnty,4,'.','') ?></th>
								<th></th>
								<th align="right"><? echo number_format($total_ac_qnty,4,'.','') ?></th>
								<th></th>
								<th align="right"><? echo number_format($total_amount,4,'.','') ?></th>
								<th></th>
							<?
						}
						else
						{
							?>
								<th colspan="11" align="right">Total Amout</th>
								<th><? echo number_format($total_amount,4,'.','') ?></th>
								<th></th>
							<?
						}
					 ?>
					
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$str_rep=array("/", "&", "*", "(", ")", "=","'",",",'"','#');
	$delivery_address=str_replace($str_rep,' ',str_replace("'","",$delivery_address));
	if ($operation==0){

		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if($db_type==0){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=1 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}

		if($db_type==2){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=1 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,booking_month,booking_year,supplier_id,attention,booking_percent,colar_excess_percent,cuff_excess_percent,ready_to_approved,inserted_by,insert_date,rmg_process_breakdown,fabric_composition,uom,remarks,entry_form,cbo_level,delivery_address,test_req, proceed_advanced,adv_booking_no";
		 $data_array ="(".$id.",1,2,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_fabric_natu.",".$cbo_fabric_source.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_booking_month.",".$cbo_booking_year.",".$cbo_supplier_name.",".$txt_attention.",".$txt_booking_percent.",".$txt_colar_excess_percent.",".$txt_cuff_excess_percent.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$processloss_breck_down.",".$txt_fabriccomposition.",".$cbouom.",".$txt_remark.",271,".$cbo_level.",'".$delivery_address."',".$txt_test_req.",".$chk_pro_advanced.",".$txt_booking_num.")";//cbo_season_year*cbo_season_id*cbo_brand_id
		 //echo "10**Insert into wo_booking_mst ($field_array) values $data_array"; die;
		 $rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1){
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		//$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		$pi_number = sql_select("SELECT pi_number from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0 order by b.work_order_no asc");
		foreach ($pi_number as $row) {
			$pi_number_arr[$row[csf('pi_number')]] = $row[csf('pi_number')];
		}
		if(count($pi_number)>0){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".implode(",", $pi_number_arr);
			disconnect($con);die;
		}
		//$recv_number=return_field_value( "recv_number", "inv_receive_master"," booking_no=$txt_booking_no  and status_active=1 and  is_deleted=0");
		$recv_number=sql_select("SELECT recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and  is_deleted=0 order by id asc");
		foreach ($recv_number as $row) {
			$recv_number_arr[$row[csf('recv_number')]]=$row[csf('recv_number')];
		}
		if(count($recv_number)>0){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",", $recv_number_arr);
			disconnect($con);die;
		}
		$field_array="item_category*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*booking_month*booking_year*supplier_id*attention*booking_percent*colar_excess_percent*cuff_excess_percent*ready_to_approved*updated_by*update_date*rmg_process_breakdown*fabric_composition*uom*remarks*cbo_level*delivery_address*test_req*proceed_advanced*adv_booking_no";
		 $data_array ="".$cbo_fabric_natu."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$txt_attention."*".$txt_booking_percent."*".$txt_colar_excess_percent."*".$txt_cuff_excess_percent."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$processloss_breck_down."*".$txt_fabriccomposition."*".$cbouom."*".$txt_remark."*".$cbo_level."*'".$delivery_address."'*".$txt_test_req."*".$chk_pro_advanced."*".$txt_booking_num."";
		$rID=sql_update("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_roolback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;die;
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;die;
		}
		$recv_number=return_field_value( "recv_number", "inv_receive_master"," booking_no=$txt_booking_no  and status_active=1 and  is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;die;
		}
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		$rID=execute_query("update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id=$update_id and status_active=1 and is_deleted=1",0);
		$rID1=execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where booking_mst_id=$update_id and status_active=1 and is_deleted=1",0);

		if($db_type==0)
		{
			if($rID && $rID1 ){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete_dtls"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//$json_data=json_decode(str_replace("'","",$json_data));
	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}

		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;die;
		}

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) {
		echo "15**0";disconnect($con);die; die;
		}
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id,is_short,booking_mst_id,pre_cost_fabric_cost_dtls_id,po_break_down_id,job_no,booking_no,booking_type,color_type,construction,copmposition,gsm_weight,dia_width,fabric_color_id,gmts_color_id,fin_fab_qnty,grey_fab_qnty,adjust_qty,rate,amount,remark,pre_cost_remarks,inserted_by,insert_date";
		 $j=1;
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txtjob="txtjob_".$i;
			 $txtpoid_="txtpoid_".$i;
			//=========
			$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconstruction_".$i;
			$txtcompositi_="txtcompositi_".$i;
			$txtgsm_weight_="txtgsm_weight_".$i;
			$txtdia_="txtdia_".$i;
			//============

			 $txtgmtcolor="txtgmtcolor_".$i;
			 $txtitemcolor="txtitemcolor_".$i;
			 $txtbalqnty="txtbalqnty_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtadj="txtadj_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i;

			 $precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			 $colorid=str_replace("'","",$$txtgmtcolor);
             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $rate=str_replace("'","",$$txtrate);
			 $amount=str_replace("'","",$$txtamount);
			 //foreach($json_data->$precostid->$colorid->po_id as $poId){
				 if($woq>0){
				 //$wQty=($json_data->$precostid->$colorid->req_qty->$poId/$reqqnty)*$woq;
				 //$amount=$wQty*$rate;
				 if ($j!=1) $data_array1 .=",";
				 $data_array1 .="(".$id_dtls.",2,".$update_id.",".$precostid.",".$$txtpoid_.",".$$txtjob.",".$txt_booking_no.",1,".$$txtcolortype_.",".$$txtconstruction_.",".$$txtcompositi_.",".$$txtgsm_weight_.",".$$txtdia_.",".$$txtitemcolor.",".$$txtgmtcolor.",".$acwoq.",".$woq.",".$$txtadj.",".$rate.",".$amount.",".$$txtremark.",".$$process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $id_dtls=$id_dtls+1;
				 $j++;
				 }
			 //}
		 }
		 //echo "90*Insert into wo_booking_dtls ($field_array1) values $data_array1"; die;
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 check_table_status( $_SESSION['menu_id'],0);
		 if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		 }

		 if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		 }
		 disconnect($con);
		 die;
	}


	else if ($operation==1){
		 $con = connect();
		 if($db_type==0){
			mysql_query("BEGIN");
		 }
		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		$recv_number=return_field_value( "recv_number", "inv_receive_master"," booking_no=$txt_booking_no  and status_active=1 and  is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
		}
		 $field_array_up1="color_type*construction*copmposition*gsm_weight*dia_width*fabric_color_id*gmts_color_id*fin_fab_qnty*grey_fab_qnty*adjust_qty*rate*amount*remark*pre_cost_remarks*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++){

			 $txtjob="txtjob_".$i;
			 $txtpoid_="txtpoid_".$i;
			 //=========
			$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconstruction_".$i;
			$txtcompositi_="txtcompositi_".$i;
			$txtgsm_weight_="txtgsm_weight_".$i;
			$txtdia_="txtdia_".$i;
			//============
			 $txtgmtcolor="txtgmtcolor_".$i;
			 $txtitemcolor="txtitemcolor_".$i;
			 $txtbalqnty="txtbalqnty_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtadj="txtadj_".$i;

			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $bookingid="bookingid_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i;

			 $precostid=str_replace("'","",$cbo_fabric_description);
			 $colorid=str_replace("'","",$$txtgmtcolor);
             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $rate=str_replace("'","",$$txtrate);
			 $amount=str_replace("'","",$$txtamount);
			 if(str_replace("'",'',$$bookingid)!=""){
				$id_arr[]=str_replace("'",'',$$bookingid);
				$data_array_up1[str_replace("'",'',$$bookingid)] =explode("*",("".$$txtcolortype_."*".$$txtconstruction_."*".$$txtcompositi_."*".$$txtgsm_weight_."*".$$txtdia_."*".$$txtitemcolor."*".$$txtgmtcolor."*".$acwoq."*".$woq."*".$$txtadj."*".$rate."*".$amount."*".$$txtremark."*".$$process."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			 }
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
         check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{

			if($rID==1)
			{
				oci_commit($con);
				echo "1**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}


	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	    $is_approved=0;
		$fabric_source=0;
		$sql=sql_select("select is_approved,fabric_source from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
			$fabric_source=$row[csf('fabric_source')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$pplbook=0;
		$ppl=sql_select("select b.id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no=$txt_booking_no and a.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id");
		foreach($ppl as $pplrow){
			$pplbook=$pplrow[csf('id')];
		}

		if($pplbook!=0){
			echo "PPL**".str_replace("'","",$txt_booking_no)."**".$pplbook;
			disconnect($con);die;
		}

		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;;
		}
		$lib_yarn_count_deter_id=0;
		$sql=sql_select("select lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where id=$cbo_fabric_description");
		foreach($sql as $row){
			$lib_yarn_count_deter_id=$row[csf('lib_yarn_count_deter_id')];
		}

		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		/*if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				die;
			}
		}

		$lib_yarn_count_deter_id=0;
		$sql=sql_select("select lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where id=$cbo_fabric_description");
		foreach($sql as $row){
			$lib_yarn_count_deter_id=$row[csf('lib_yarn_count_deter_id')];
		}


		if($fabric_source==1){
			$sql=sql_select("select a.recv_number from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and  a.booking_no=$txt_booking_no and a.entry_form in(2,22,58) and a.receive_basis=1 and b.febric_description_id=$lib_yarn_count_deter_id  and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
			$recv_number=0;
			foreach($sql as $row){
				$recv_number=$row[csf('recv_number')];
		    }
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				die;
			}
		}

		if($fabric_source==2){
			$sql=sql_select("select a.recv_number from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and  a.booking_no=$txt_booking_no and a.entry_form in(22,7,37,68) and a.receive_basis=1 and b.febric_description_id=$lib_yarn_count_deter_id  and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
			$recv_number=0;
			foreach($sql as $row){
				$recv_number=$row[csf('recv_number')];
		    }
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				die;
			}
		}*/
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		 for ($i=1;$i<=$total_row;$i++){

			 $txtjob="txtjob_".$i;
			 $txtpoid_="txtpoid_".$i;
			 $txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			  $twoq="txtwoq_".$i;
			 $bookingid="bookingid_".$i;
			 $precostid=str_replace("'","",$cbo_fabric_description);
			 $woq_chk=str_replace("'","",$$twoq);
			 if($woq_chk>0)
			 {
			 $rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".str_replace("'","",$$bookingid).") and booking_no=$txt_booking_no",0);
			 }


		 }
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delete_dtls_job_level"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$json_data=json_decode(str_replace("'","",$json_data));
	//print_r($json_data);
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id,is_short,booking_mst_id,pre_cost_fabric_cost_dtls_id,po_break_down_id,job_no,booking_no,booking_type,color_type,construction,copmposition,gsm_weight,dia_width,fabric_color_id,gmts_color_id,fin_fab_qnty,grey_fab_qnty,adjust_qty,rate,amount,remark,pre_cost_remarks,inserted_by,insert_date";
		 $j=1;
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txtjob="txtjob_".$i;
			 $txtpoid_="txtpoid_".$i;
			 //=========
			$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconstruction_".$i;
			$txtcompositi_="txtcompositi_".$i;
			$txtgsm_weight_="txtgsm_weight_".$i;
			$txtdia_="txtdia_".$i;
			//============
			 $txtgmtcolor="txtgmtcolor_".$i;
			 $txtitemcolor="txtitemcolor_".$i;
			 $txtbalqnty="txtbalqnty_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtadj="txtadj_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i;

			 $precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			 $colorid=str_replace("'","",$$txtgmtcolor);
			 $dia=str_replace("'","",$$txtdia_);
			 $process_p=str_replace("'","",$$process);
             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $adq=str_replace("'","",$$txtadj);
			 $rate=str_replace("'","",$$txtrate);
			//echo $dia."ppp";
			 //print_r($json_data->$precostid->$colorid->$dia);
			 foreach($json_data->$precostid->$colorid->$dia->$process_p->po_id as $poId){
				 if($woq>0){
				 $wQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$woq;
				 $AcwQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$acwoq;
				 $adjQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$adq;
				 $amount=$AcwQty*$rate;
				 if ($j!=1) $data_array1 .=",";
				 $data_array1 .="(".$id_dtls.",2,".$update_id.",".$precostid.",".$poId.",".$$txtjob.",".$txt_booking_no.",1,".$$txtcolortype_.",".$$txtconstruction_.",".$$txtcompositi_.",".$$txtgsm_weight_.",".$$txtdia_.",".$$txtitemcolor.",".$$txtgmtcolor.",".$AcwQty.",".$wQty.",".$adjQty.",".$rate.",".$amount.",".$$txtremark.",".$$process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $id_dtls=$id_dtls+1;
				 $j++;
				 }
			 }
		 }
		//echo "insert into wo_booking_dtls (".$field_array1.") values ".$data_array1; die;
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 check_table_status( $_SESSION['menu_id'],0);
		 if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		 }

		 if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		 }
		 disconnect($con);
		 die;
	}
	else if ($operation==1){
		 $con = connect();
		 if($db_type==0){
			mysql_query("BEGIN");
		 }
		 $poArr=array();$poIdWoArr=array();
		 for ($i=1;$i<=$total_row;$i++){

			$txtjob="txtjob_".$i;
			$txtpoid_="txtpoid_".$i;
			$txtwoq="txtwoq_".$i;

			$txtpoId=str_replace("'","",$$txtpoid_);
			$poArr[$txtpoId]=$txtpoId;
			$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconstruction_".$i;
			$txtcompositi_="txtcompositi_".$i;
			$txtgsm_weight_="txtgsm_weight_".$i;
			$txtdia_="txtdia_".$i;
			//============
			 $txtgmtcolor="txtgmtcolor_".$i;
			 $txtitemcolor="txtitemcolor_".$i;
			 $txtbalqnty="txtbalqnty_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtadj="txtadj_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $bookingid="bookingid_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i; 
			 $txtjob="txtjob_".$i;

			 $precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			 $colorid=str_replace("'","",$$txtgmtcolor);
			 $dia=str_replace("'","",$$txtdia_);
			 $process_p=str_replace("'","",$$process);

             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $adq=str_replace("'","",$$txtadj);
			 $rate=str_replace("'","",$$txtrate); 
			 $txtjob_no=str_replace("'","",$$txtjob);


			foreach($json_data->$precostid->$colorid->$dia->$process_p->po_id as $poId){
				$wQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$woq;
				$AcwQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$acwoq;
				$adjQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$adq;
				$reqqtycostingper=$json_data->$precostid->$colorid->$dia->$process_p->reqqtycostingper->$poId;
				$poIdWoArr[$poId]+=$wQty;
			}

		 }

		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
			//	echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				//disconnect($con);die;
			}
		}
		//pro_finish_fabric_rcv_dtls
		//$recv_number=return_field_value( "recv_number", "inv_receive_master"," booking_no=$txt_booking_no  and status_active=1 and  is_deleted=0");
		$sql_wvn_recv="select a.recv_number,c.po_breakdown_id, c.prod_id, c.color_id, c.quantity from inv_receive_master a ,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and c.dtls_id=b.id and a.entry_form=17 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0  and c.po_breakdown_id in(".implode(",",$poArr).")";
		//echo "10**=".$sql_wvn_recv;check_table_status( $_SESSION['menu_id'],0);die;
		$sql_wvn_recv_res=sql_select($sql_wvn_recv);
		$poRecvQty=0;
		foreach($sql_wvn_recv_res as $row)
		{
			$poRecvQtyArr[$row[csf('po_breakdown_id')]]+=$row[csf('quantity')];
			$RecvNoArr[$row[csf('po_breakdown_id')]].=$row[csf('recv_number')].',';
		}
		foreach($poIdWoArr as $poid=>$qty)
		{
			$wo_qty=$qty;
			$poRecvQty=$poRecvQtyArr[$poid];
			$RecvNo=rtrim($RecvNoArr[$poid],',');
			$RecvNos=implode(",",array_unique(explode(",",$RecvNo)));
			if($wo_qty<$poRecvQty)
			{
				echo "13**WO qty will not less from receive qty,RecvNo=".$RecvNos. ', RcvQty='.$poRecvQty.',WoQty='.$wo_qty;
				check_table_status( $_SESSION['menu_id'],0);
				die;
			}
		}
		//echo "10**=".$sql_wvn_recv;check_table_status( $_SESSION['menu_id'],0);die;

		if($recv_number){
			//echo "recv1s**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			//disconnect($con);die;
		}
		 $field_array_up1="is_short*color_type*construction*copmposition*gsm_weight*dia_width*fabric_color_id*gmts_color_id*fin_fab_qnty*grey_fab_qnty*adjust_qty*rate*amount*remark*pre_cost_remarks*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++){

			 $txtjob="txtjob_".$i;
			 $txtpoid_="txtpoid_".$i;
			 //=========
			$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconstruction_".$i;
			$txtcompositi_="txtcompositi_".$i;
			$txtgsm_weight_="txtgsm_weight_".$i;
			$txtdia_="txtdia_".$i;
			//============
			 $txtgmtcolor="txtgmtcolor_".$i;
			 $txtitemcolor="txtitemcolor_".$i;
			 $txtbalqnty="txtbalqnty_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtadj="txtadj_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $bookingid="bookingid_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i;

			 $precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			 $colorid=str_replace("'","",$$txtgmtcolor);
			 $dia=str_replace("'","",$$txtdia_);
			 $process_p=str_replace("'","",$$process);

             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $adq=str_replace("'","",$$txtadj);
			 $rate=str_replace("'","",$$txtrate);
			//print_r($json_data->$precostid->$colorid->$dia->$process_p);
			 foreach($json_data->$precostid->$colorid->$dia->$process_p->po_id as $poId){
				 $wQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$woq;
				 $AcwQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$acwoq;
				 $adjQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$adq;
				 $amount=$AcwQty*$rate;
				 if(str_replace("'",'',$$bookingid)!=""){
					$id_arr[]=str_replace("'",'',$json_data->$precostid->$colorid->$dia->$process_p->booking_id->$poId);
					$data_array_up1[str_replace("'",'',$json_data->$precostid->$colorid->$dia->$process_p->booking_id->$poId)] =explode("*",("2*".$$txtcolortype_."*".$$txtconstruction_."*".$$txtcompositi_."*".$$txtgsm_weight_."*".$$txtdia_."*".$$txtitemcolor."*".$$txtgmtcolor."*".number_format($AcwQty,6,'.','')."*".number_format($wQty,6,'.','')."*".number_format($adjQty,6,'.','')."*".$rate."*".$amount."*".$$txtremark."*".$$process."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				 }
			 }
		 }
		 //echo "10**". bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ); die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
         check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{

			if($rID==1)
			{
				oci_commit($con);
				echo "1**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}


	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}

		$is_approved=0;
		$fabric_source=0;
		$sql=sql_select("select is_approved,fabric_source from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
			$fabric_source=$row[csf('fabric_source')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$pplbook=0;
		$ppl=sql_select("select b.id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no=$txt_booking_no and a.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id");
		foreach($ppl as $pplrow){
			$pplbook=$pplrow[csf('id')];
		}

		if($pplbook!=0){
			echo "PPL**".str_replace("'","",$txt_booking_no)."**".$pplbook;
			disconnect($con);die;
		}

		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}
		$lib_yarn_count_deter_id=0;
		$sql=sql_select("select lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where id=$cbo_fabric_description");
		foreach($sql as $row){
			$lib_yarn_count_deter_id=$row[csf('lib_yarn_count_deter_id')];
		}

		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		/*if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				die;
			}
		}
		$lib_yarn_count_deter_id=0;
		$sql=sql_select("select lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where id=$cbo_fabric_description");
		foreach($sql as $row){
			$lib_yarn_count_deter_id=$row[csf('lib_yarn_count_deter_id')];
		}*/



	/*	$recv_number=return_field_value( "recv_number", "inv_receive_master"," booking_no=$txt_booking_no  and status_active=1 and  is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			die;
		}*/

		/*if($fabric_source==1){
			$sql=sql_select("select a.recv_number from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and  a.booking_no=$txt_booking_no and a.entry_form in(2,22,58) and a.receive_basis=1 and b.febric_description_id=$lib_yarn_count_deter_id  and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
			$recv_number=0;
			foreach($sql as $row){
				$recv_number=$row[csf('recv_number')];
		    }
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				die;
			}
		}

		if($fabric_source==2){
			$sql=sql_select("select a.recv_number from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and  a.booking_no=$txt_booking_no and a.entry_form in(22,7,37,68) and a.receive_basis=1 and b.febric_description_id=$lib_yarn_count_deter_id  and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
			$recv_number=0;
			foreach($sql as $row){
				$recv_number=$row[csf('recv_number')];
		    }
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				die;
			}
		}*/
		/*$recv_number=return_field_value( "recv_number", "inv_receive_master"," booking_no=$txt_booking_no  and status_active=1 and  is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			die;
		}*/

		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		 for ($i=1;$i<=$total_row;$i++){
			 //$txtjob="txtjob_".$i;
			 //$txtpoid_="txtpoid_".$i;
			 //$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			 $bookingid="bookingid_".$i;
			 //$precostid=str_replace("'","",$cbo_fabric_description);
			 //$rID=execute_query( "delete from wo_booking_dtls where  pre_cost_fabric_cost_dtls_id in (".str_replace("'","",$precostid).")",0);
			 //$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  pre_cost_fabric_cost_dtls_id in (".str_replace("'","",$precostid).") and booking_no=$txt_booking_no",0);
			 ///echo "10**update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".str_replace("'","",$$bookingid).") and booking_no=$txt_booking_no";
			 //die;
			 $rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".str_replace("'","",$$bookingid).") and booking_no=$txt_booking_no",0);


		 }
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		disconnect($con);
		die;


	}
}

if($action=="check_is_booking_used")
{
	$work_order_no=return_field_value("work_order_no","com_pi_item_details","work_order_no='$data' and status_active =1 and is_deleted=0");
	echo $work_order_no;
	die;
}

if($action=="delete_booking_item")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
  $rID = execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1 where  booking_no ='$data'",0);
   if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".str_replace(",","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".str_replace(",","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
}


if($action=="booking_surch_option")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", '', '', $unicode);
	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			var onclickString=paramArr=functionParam="";
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				onclickString = $('#tr_' + i).attr('onclick');
				paramArr = onclickString.split("'");
				functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

				if( jQuery.inArray( str_or, selected_no ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}

		function frm_close()
		{
			parent.emailwindow.hide();
		}
    </script>
    <?
	$select_rpt_option_arr=array(1=>"Size and Color Breakdown",2=>"Main Booking Info",3=>"Collar / Cuff -  Colour Size Brakedown in Pcs",4=>"Yarn Required Summary",5=>"Allocated Yarn",6=>"Conversion Charge",7=>"Embellishment",8=>"Approved Instructions",9=>"Special  Instruction",10=>"Comments",11=>"TNA Information");
	//echo $sql;die;
	?>

    <div style="width:500px;">
    <table width="500" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
    	<thead>
        	<tr>
                <th width="50">Sl</th>
                <th>Report Option</th>
            </tr>
        </thead>
    </table>
    <table width="500" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="list_view" align="left">
        <tbody>
			<?
            $i=1;
            foreach($select_rpt_option_arr as $id=>$val)
            {
                if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i."_".$id."_".$val; ?>')" style="cursor:pointer">
                    <td width="50"  align="center"><? echo $i; ?></td>
                    <td><? echo $val; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr>
                <td  style="vertical-align:middle; padding-left:20px;" colspan="2"><input type="checkbox" id="all_check" onClick="check_all_data('all_check')" />&nbsp;Check All / Un-Check All
                <input type='hidden' id='txt_selected_id' />
                <input type='hidden' id='txt_selected' />
                <input type='hidden' id='txt_selected_no' />
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <div style="width:100%"><p align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" value="Close" onClick="frm_close();" ></p></div>
    </div>

    <script language="javascript" type="text/javascript">
	var category_no='<? echo $booking_option_no;?>';
	var category_id='<? echo $booking_option_id;?>';
	var category_des='<? echo $booking_option;?>';
	var cate_ref="";
	if(category_no!="")
	{
		category_no_arr=category_no.split(",");
		category_id_arr=category_id.split(",");
		category_des_arr=category_des.split(",");
		var str_ref="";
		for(var k=0;k<category_no_arr.length; k++)
		{
			cate_ref=category_no_arr[k]+'_'+category_id_arr[k]+'_'+category_des_arr[k];
			js_set_value(cate_ref);
		}
	}
	</script>
    <?
	exit();
}

if($action=="terms_condition_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);

?>
	<script>
function add_break_down_tr(i)
 {
	var row_num=$('#tbl_termcondi_details tr').length-1;
	if (row_num!=i){
		return false;
	}
	else{
		i++;
		 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }
			});
		  }).end().appendTo("#tbl_termcondi_details");
		  $('#tbl_termcondi_details tr:last td:eq(0)').html(i);
		  $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		  $('#termscondition_'+i).val("");
	}
}

function fn_deletebreak_down_tr(rowNo,tr){
	/*var numRow = $('table#tbl_termcondi_details tbody tr').length;
	if(numRow==rowNo && rowNo!=1){
		$('#tbl_termcondi_details tbody tr:last').remove();
	}*/
	if(rowNo!=1){
		 var index = $(tr).closest("tr").index();
		 $("table#tbl_termcondi_details tbody tr:eq("+index+")").remove();
		 var numRow = $('table#tbl_termcondi_details tbody tr').length;
		for(i = rowNo;i <= numRow;i++)
		{
			$("table#tbl_termcondi_details  tr:eq("+i+")").find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
			'value': function(_, value) { return value }
			});
			var trr=$('table#tbl_termcondi_details tr:eq('+i+') td:eq(0)').html(i);
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",this)");
			//$('#termscondition_'+i).val("");
			})
		}
	}
}

function fnc_fabric_booking_terms_condition( operation ){
	var row_num=$('#tbl_termcondi_details tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++){

		if (form_validation('termscondition_'+i,'Term Condition')==false){
			return;
		}
		data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../",i);
	}
	var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
	freeze_window(operation);
	http.open("POST","partial_fabric_booking_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse(){
	if(http.readyState == 4){
	    var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		release_freezing();
		if(reponse[0]==0 || reponse[0]==1){
			parent.emailwindow.hide();
		}
	}
}
function open_extra_terms_popup(page_link,title){
	    var txt_booking_no=document.getElementById('txt_booking_no').value
	    page_link=page_link+'&txt_booking_no='+txt_booking_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=400px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("terms_breck_down");
			if (theemail.value!="")
			{
				var counter=$('#tbl_termcondi_details tr').length-1;
				var data=JSON.parse(theemail.value);
				for(var i=0;i<data.length;i++){
					//alert(data[i])
					counter++;
					$('#tbl_termcondi_details tbody').append(
					'<tr id="settr_1" align="center">'
					+ '<td>'+counter+'</td><td><input type="text" name="termscondition_'+counter+'" class="text_boxes" id="termscondition_'+counter+'"  style="width:95%;" value="'+data[i]+'"/></td><td><input type="button" class="formbutton" id="increase_'+counter+'"  style="width:30px;" value="+" onClick="add_break_down_tr('+counter+')"/><input type="button" class="formbutton" id="decrease_'+counter+'"  style="width:30px;" value="-" onClick="javascript:fn_deletebreak_down_tr('+counter+')"/></td>'+ '</tr>'
				);
				}
				//alert(data[0])
				//alert(JSON.parse(theemail.value).length)
			}
		}
}
    </script>

</head>
<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off" name="termscondi_1">
           <input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no); ?>"/>


            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no order by id");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>,this);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=271");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> ,this);" />
                                    </td>
                                </tr>
                    <?
						}
					}
					?>
                </tbody>
                </table>

                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%">
                        <input type="button" id="set_button4" class="image_uploader" style="width:160px;" value="Add More.." onClick="open_extra_terms_popup('partial_fabric_booking_controller.php?action=extra_terms_popup','Terms Condition')" />
                        </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
 									echo load_submit_buttons( $permissions, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",11) ;

									?>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="extra_terms_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		var selected_id = new Array();
		var selected_item=new Array();
function js_set_value(counter,id,terms){
	toggle( document.getElementById( 'search' + counter ), '#FFFFCC' );

	if( jQuery.inArray( id, selected_id ) == -1 ) {

		selected_id.push( id );
		selected_item.push(terms);
	}
	else{
		for( var i = 0; i < selected_id.length; i++ ) {
			if( selected_id[i] == id ) break;
		}
		selected_id.splice( i, 1 );
		selected_item.splice( i,1 );
	}

	var ids = '';
	var termCon='';
	for( var i = 0; i < selected_id.length; i++ ) {
		///alert(selected_id[i])
		ids += selected_id[i] + ',';
		termCon+=selected_item[i]+ ',';
	}
	ids = ids.substr( 0, ids.length - 1 );
	termCon = termCon.substr( 0, termCon.length - 1 );
	//alert(termCon);
	//alert(JSON.stringify(selected_item));
	$('#terms_breck_down').val( JSON.stringify(selected_item) );
	$('#txt_pre_cost_dtls_id').val( ids );
	//$('#txt_selected_po').val( txt_po_id );
	//parent.emailwindow.hide();
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
 <?
 //$txt_booking_no=$txt_booking_no;
 //echo $txt_booking_no;
 ?>
<fieldset>
    <form autocomplete="off">
    <input style="width:60px;" type="hidden" class="text_boxes"  name="terms_breck_down" id="terms_breck_down" />
    <input style="width:60px;" type="hidden" class="text_boxes"  name="txt_pre_cost_dtls_id" id="txt_pre_cost_dtls_id" />
    <table width="400" class="rpt_table" border="1" rules="all">
    <thead>
    <th width="40">
    SL
    </th>
    <th>
    Terms
    </th>
    </thead>
    <tbody>
    <?           $i=1;
				$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=0 and page_id=271");// quotation_id='$data'
				foreach( $data_array as $row )
				{
					 if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
               <tr style="text-decoration:none; cursor:pointer" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i ;?>,'<? echo $row[csf('id')];?>','<? echo $row[csf('terms')];?>')">
                <td width="40">
               <? echo $i;?>
                </td>
                <td>
                 <? echo $row[csf('terms')]; ?>
                </td>
                </tr>
                <?
				$i++;
						}
				?>
                </tbody>
                </table>
                <table width="400" class="rpt_table" border="1" rules="all">
                <tr>
               <td align="center"  class="button_container" colspan="2">
			    <input type="button" class="formbutton" value="Close" onClick="parent.emailwindow.hide()"/>
                 </td>
                </tr>
           </table>
    </form>
</fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="rmg_process_loss_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
function js_set_value_set()
{

	  var cutting_per=$('#cutting_per').val();
	  if(cutting_per=="")
	  {
		cutting_per=0;
	  }

	  var embbroidery_per=$('#embbroidery_per').val();
	  if(embbroidery_per=="")
	  {
		embbroidery_per=0;
	  }

	  var printing_per=$('#printing_per').val();
	  if(printing_per=="")
	  {
		printing_per=0;
	  }

	  var wash_per=$('#wash_per').val();
	  if(wash_per=="")
	  {
		wash_per=0;
	  }

	  var sew_per=$('#sew_per').val();
	  if(sew_per=="")
	  {
		sew_per=0;
	  }

	  var fin_per=$('#fin_per').val();
	  if(fin_per=="")
	  {
		fin_per=0;
	  }

	var knitt_per=$('#knitt_per').val();
	  if(knitt_per=="")
	  {
		knitt_per=0;
	  }

	  var dying_per=$('#dying_per').val();
	  if(dying_per=="")
	  {
		dying_per=0;
	  }

	  var extracutt_per=$('#extracutt_per').val();
	  if(extracutt_per=="")
	  {
		extracutt_per=0;
	  }

	  var other_per=$('#other_per').val();
	  if(other_per=="")
	  {
		other_per=0;
	  }

	  var neck_sleev_printing_per=$('#neck_sleev_printing_per').val();
	  if(neck_sleev_printing_per=="")
	  {
		neck_sleev_printing_per=0;
	  }

	  var gmt_other_per=$('#gmt_other_per').val();
	  if(gmt_other_per=="")
	  {
		gmt_other_per=0;
	  }


	  var yarn_dyeing_per=$('#yarn_dyeing_per').val();
	  if(yarn_dyeing_per=="")
	  {
		yarn_dyeing_per=0;
	  }

	  var all_over_print_per=$('#all_over_print_per').val();
	  if(all_over_print_per=="")
	  {
		all_over_print_per=0;
	  }


	  var lay_wash_per=$('#lay_wash_per').val();
	  if(lay_wash_per=="")
	  {
		lay_wash_per=0;
	  }

	  var gmtfinish_per=$('#gmtfinish_per').val();
	  if(gmtfinish_per=="")
	  {
		gmtfinish_per=0;
	  }


	 var processloss_breck_down=cutting_per+'_'+embbroidery_per+'_'+printing_per+'_'+wash_per+'_'+sew_per+'_'+fin_per+'_'+knitt_per+'_'+dying_per+'_'+extracutt_per+'_'+other_per+'_'+neck_sleev_printing_per+'_'+gmt_other_per+'_'+yarn_dyeing_per+'_'+all_over_print_per+'_'+lay_wash_per+'_'+gmtfinish_per;
	 document.getElementById('processloss_breck_down').value=processloss_breck_down;
	 parent.emailwindow.hide();
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
 <?
 $data=explode("_",$processloss_breck_down);
 ?>
<fieldset>
    <form autocomplete="off">
    <input style="width:60px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" />
    <table width="180" class="rpt_table" border="1" rules="all">
               <tr>
                <td width="130">
               Cut Panel rejection <!--  Extra Cutting %  breack Down 8-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="extracutt_per" id="extracutt_per" value="<? echo $data[8];  ?>"  />
                </td>
                </tr>
                <tr>
                <td width="130">
                 Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="printing_per" id="printing_per" value="<? echo $data[2];  ?>" />
                </td>
                </tr>


                <tr>
                <td width="130">
                 Neck/Sleeve Printing <!-- new breack Down 10-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="neck_sleev_printing_per" id="neck_sleev_printing_per" value="<? echo $data[10];  ?>" />
                </td>
                </tr>


                <tr>
                <td width="130">
                Embroidery  <!-- Embroidery  % breack Down 1-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="embbroidery_per" id="embbroidery_per" value="<? echo $data[1];  ?>"  />
                </td>
                </tr>


                <tr>
                <td width="130">
                Sewing/Input <!-- Sewing % breack Down 4-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="sew_per" id="sew_per" value="<? echo $data[4];  ?>" />
                </td>
                </tr>

                <tr>
                <td width="130">
                Garments Wash  <!-- Washing % breack Down 3-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="wash_per" id="wash_per"  value="<? echo $data[3];  ?>" />
                </td>
                </tr>

                <tr>
                <td width="130">
                Gmts Finishing  <!-- Washing % breack Down 3-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmtfinish_per" id="gmtfinish_per"  value="<? echo $data[15];  ?>" />
                </td>
                </tr>


                <tr>
                <td width="130">
                 Others  <!-- New breack Down 11-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmt_other_per" id="gmt_other_per" value="<? echo $data[11];  ?>"  />
                </td>
                </tr>

                <tr>
                <td width="130">
                 Knitting   <!-- Knitting % breack Down 6-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="knitt_per" id="knitt_per" value="<? echo $data[6];  ?>"  />
                </td>
                </tr>

                <tr>
                <td width="130">
                 Yarn Dyeing   <!-- New breack Down 12-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="yarn_dyeing_per" id="yarn_dyeing_per" value="<? echo $data[12];  ?>"  />
                </td>
                </tr>

                <tr>
                <td width="130">
                Dyeing & Finishing   <!-- Finishing % breack Down 5-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="fin_per" id="fin_per" value="<? echo $data[5];  ?>"  />
                </td>
                </tr>


                <tr>
                <td width="130">
                All Over Print  <!-- New breack Down 13-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="all_over_print_per" id="all_over_print_per" value="<? echo $data[13];  ?>"  />
                </td>
                </tr>

                <tr>
                <td width="130">
                Lay Wash (Fabric)  <!-- New breack Down 14-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="lay_wash_per" id="lay_wash_per" value="<? echo $data[14];  ?>"  />
                </td>
                </tr>


                <tr>
                <td width="130">
                 Dying  <!--breack Down 7-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="dying_per" id="dying_per" value="<? echo $data[7];  ?>"  />
                </td>
                </tr>
                <tr>
                <td width="130">
                 Cutting (Febric) <!-- Cutting % breack Down 0-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="cutting_per" id="cutting_per" value="<? echo $data[0];  ?>" />
                </td>
                </tr>
                <tr>
                <td width="130">
                 Others <!--breack Down 9-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="other_per" id="other_per" value="<? echo $data[9];  ?>"  />
                </td>
                </tr>

                <tr>
               <td align="center"  class="button_container" colspan="2">
			    <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/>
                 </td>
                </tr>
           </table>
    </form>
</fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="load_color_size_form")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$data=explode("**",$data);
	$fabric_cost_id=trim($data[0]);
	$booking_no=trim($data[1]);
	$cbo_level=trim($data[3]);
	$permission=$_SESSION['page_permission'];
	//echo $permission.'PP';
	$job_no="";
	$bodyPart=0;
	$body_part_type=0;
	$sql=sql_select("select a.job_no,a.body_part_id,b.body_part_type from wo_pre_cost_fabric_cost_dtls a, lib_body_part b   where a.body_part_id=b.id and  a.id='$fabric_cost_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($sql as $row){
		$job_no=$row[csf('job_no')];
	    $bodyPart=$row[csf('body_part_id')];
		$body_part_type=$row[csf('body_part_type')];
	}
	$book_data=array();
	 $sql="select id,booking_no,job_no,po_break_down_id, pre_cost_fabric_cost_dtls_id, gmts_color_id, size_number_id, item_size,gmts_qty,excess_per,qty  from wo_booking_colar_culff_dtls where booking_no ='$booking_no' and pre_cost_fabric_cost_dtls_id= '$fabric_cost_id' and status_active=1 and is_deleted=0 ";
	$sql_data=sql_select($sql);

	foreach($sql_data as $sql_data_row){
		$book_data[$sql_data_row[csf('po_break_down_id')]][$sql_data_row[csf('gmts_color_id')]][$sql_data_row[csf('size_number_id')]]['gmts_qty']=$sql_data_row[csf('gmts_qty')];
		$book_data[$sql_data_row[csf('po_break_down_id')]][$sql_data_row[csf('gmts_color_id')]][$sql_data_row[csf('size_number_id')]]['excess_per']=$sql_data_row[csf('excess_per')];
	   $book_data[$sql_data_row[csf('po_break_down_id')]][$sql_data_row[csf('gmts_color_id')]][$sql_data_row[csf('size_number_id')]]['qty']=$sql_data_row[csf('qty')];
	   $book_data[$sql_data_row[csf('po_break_down_id')]][$sql_data_row[csf('gmts_color_id')]][$sql_data_row[csf('size_number_id')]]['dtls_id']=$sql_data_row[csf('id')];


	}
	 $sql="select e.colar_excess_percent,e.cuff_excess_percent,e.item_size,e.body_part_id,e.po_break_down_id,f.size_number_id,f.color_number_id,f.color_order,f.size_order,sum(f.plan_cut_qnty) as plan_cut_qnty,g.po_number from wo_po_color_size_breakdown f join (select a.colar_excess_percent,a.cuff_excess_percent,b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight,d.color_number_id,d.gmts_sizes ,d.dia_width,d.item_size from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.id=d.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and d.color_number_id=b.gmts_color_id and d.dia_width=b.dia_width  and c.id = '$fabric_cost_id' and a.booking_no='$booking_no' and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1) e on e.job_no=f.job_no_mst and e.po_break_down_id=f.po_break_down_id and e.color_number_id=f.color_number_id and e.gmts_sizes=f.size_number_id and f.status_active=1 and f.is_deleted=0 join wo_po_break_down g on g.id=f.po_break_down_id and g.job_no_mst=f.job_no_mst group by e.colar_excess_percent,e.cuff_excess_percent,e.item_size,e.body_part_id,e.po_break_down_id,f.size_number_id,f.color_number_id,f.color_order,f.size_order,g.po_number  order by f.color_order,f.size_order";
	$sqldata=sql_select($sql);
		?>
        <input style="width:125px;" type="hidden" class="text_boxes"  name="txt_body_part" id="txt_body_part" value="<? echo $bodyPart;  ?>" />
        <input style="width:125px;" type="hidden" class="text_boxes"  name="txt_job" id="txt_job" value="<? echo $job_no;  ?>" />

            <table width="550" class="rpt_table" border="1" rules="all" id="colar_cuff_tbl">
            <thead>
            <tr>
                <th width="130">
                PO Number
                </th>
                <th>
                Gmts  Color
                </th>
                <th>
                Gmts  Size
                </th>
                <th>
                Item  Size
                </th>
                <th>
                Gmts Qty (Pcs)
                </th>
                <th>
                Excess %
                </th>
                <th>
                <? echo $body_part[$bodyPart]; ?>  Qty (Pcs)
                </th>
                </tr>
                </thead>
                <tbody>

            <?
			    $i=1;
				foreach($sqldata as $row){
					$excess_per=0;
					if($row[csf('body_part_id')]==2){
						$excess_per=$row[csf('colar_excess_percent')];
					}
					if($row[csf('body_part_id')]==3){
						$excess_per=$row[csf('cuff_excess_percent')];
					}
					$gmts_qty=$row[csf('plan_cut_qnty')];
					$qty=0;
					if($body_part_type==50){
						$qty=$gmts_qty*2;
					}else{
						$qty=$gmts_qty*1;
					}
			?>
                <tr>
                <td width="130">
                 <input style="width:125px;" type="text" class="text_boxes"  name="po_number_<? echo $i ?>" id="po_number_<? echo $i ?>" value="<? echo $row[csf('po_number')]; ?>" readonly />
				 <? $dtls_id=$book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['dtls_id'];?>
                 <input style="width:60px;" type="hidden" class="text_boxes"  name="po_id_<? echo $i ?>" id="po_id_<? echo $i ?>" value="<? echo $row[csf('po_break_down_id')];  ?>"  /> <input style="width:30px;" type="hidden" class="text_boxes"  name="update_dtls_id_<? echo $i ?>" id="update_dtls_id_<? echo $i ?>" value="<? echo $dtls_id;  ?>"  />
				  <input style="width:30px;" type="hidden" class="text_boxes"  name="body_part_type_id_<? echo $i ?>" id="body_part_type_id_<? echo $i ?>" value="<? echo $body_part_type;  ?>"  />
                </td>
                <td>
               <input style="width:60px;" type="text" class="text_boxes"  name="color_number_<? echo $i ?>" id="color_number_<? echo $i ?>" value="<? echo $color_library[$row[csf('color_number_id')]];  ?>" readonly />
                 <input style="width:60px;" type="hidden" class="text_boxes"  name="color_id_<? echo $i ?>" id="color_id_<? echo $i ?>" value="<? echo $row[csf('color_number_id')];  ?>" readonly  />
                </td>
                <td>
               	<input style="width:60px;" type="text" class="text_boxes"  name="size_number_<? echo $i ?>" id="size_number_<? echo $i ?>" value="<? echo $size_library[$row[csf('size_number_id')]];  ?>" readonly />
                 <input style="width:60px;" type="hidden" class="text_boxes"  name="size_id_<? echo $i ?>" id="size_id_<? echo $i ?>" value="<? echo $row[csf('size_number_id')];  ?>"  />
                </td>
                 <td>
               <input style="width:60px;" type="text" class="text_boxes"  name="item_size_<? echo $i ?>" id="item_size_<? echo $i ?>" value="<? echo $row[csf('item_size')];  ?>"  readonly/>
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmts_qty_<? echo $i ?>" id="gmts_qty_<? echo $i ?>"  onChange="calculate_qty(<? echo $i; ?>,<? echo $body_part_type ?>)" value="<? if($book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['gmts_qty']){echo $book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['gmts_qty'];}else{ echo $gmts_qty;} ?>"  />
                </td>
                 <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="excess_per_<? echo $i ?>" id="excess_per_<? echo $i ?>"  onChange="calculate_qty(<? echo $i; ?>,<? echo $body_part_type ?>);copy_value(<? echo $i; ?>,'excessper')" value="<? if( $book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['excess_per']){echo $book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['excess_per'];} else{echo $excess_per;;}?>"  />
                </td>
                <td>

                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="qty_<? echo $i ?>" id="qty_<? echo $i ?>" value="<? if($book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']){echo $book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty'];}else{echo $qty;} ?>"  readonly />
                </td>
                </tr>

                <?
				$i++;
				if($dtls_id>0) $button_id=1;else $button_id=0;
				}
				//echo $button_id.'AAAAAAAAAAAA';
				?>
                </tbody>
                </table>
                <table>
                <tr>
                <td align="center"  class="button_container" colspan="7">
              <?
			  if(count($sql_data)>0)
			  {
				 echo load_submit_buttons( $permission, "fnc_colar_culff_dtls", 1,0,"",2);
			  }
			  else
			  {
			  	echo load_submit_buttons( $permission, "fnc_colar_culff_dtls", 0,0,"",2);
			  }
			   ?>
                </td>
                </tr>
            </table>
        <?
		exit();
}
if($action=='save_update_delete_colar_culff_dtls')
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  //Insert Here
	{
		 $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }

		 $booking=trim(str_replace("'", '', $booking_no));

		 $id=return_next_id( "id", "wo_booking_colar_culff_dtls",1);
		 $field_array="id,booking_no,job_no,po_break_down_id, pre_cost_fabric_cost_dtls_id, gmts_color_id, size_number_id, item_size,gmts_qty,excess_per,qty,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $po_id="po_id_".$i;
			 $color_id="color_id_".$i;
			 $size_id="size_id_".$i;
			 $item_size="item_size_".$i;
			 $gmts_qty="gmts_qty_".$i;
			 $excess_per="excess_per_".$i;
			 $qty="qty_".$i;
			 $update_dtls_id="update_dtls_id_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".trim($booking)."',".$txt_job.",".$$po_id.",".$cbo_fabric_part.",".$$color_id.",".$$size_id.",".$$item_size.",".$$gmts_qty.",".$$excess_per.",".$$qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		 }
		 		// echo "10**insert into wo_booking_colar_culff_dtls (".$field_array.") values ".$data_array; die;
		$rID1=execute_query( "update  wo_booking_colar_culff_dtls set status_active=0,is_deleted=1  where  pre_cost_fabric_cost_dtls_id =$cbo_fabric_part",1);

		 $rID=sql_insert("wo_booking_colar_culff_dtls",$field_array,$data_array,0);
		 //echo "insert into wo_booking_colar_culff_dtls (".$field_array.") values ".$data_array;
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".$booking_no."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$booking_no."**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".$booking_no."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$booking_no."**".$rID;
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 $booking=trim(str_replace("'", '', $booking_no));
		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			die;
		}
		 $id=return_next_id( "id", "wo_booking_colar_culff_dtls",1);
		$field_array_up="job_no*po_break_down_id*pre_cost_fabric_cost_dtls_id*gmts_color_id*size_number_id*item_size*gmts_qty*excess_per*qty*updated_by*update_date*status_active*is_deleted";
		$field_array="id,booking_no,job_no,po_break_down_id, pre_cost_fabric_cost_dtls_id, gmts_color_id, size_number_id, item_size,gmts_qty,excess_per,qty,inserted_by,insert_date";
			$new_data =1;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $po_id="po_id_".$i;
			 $color_id="color_id_".$i;
			 $size_id="size_id_".$i;
			 $item_size="item_size_".$i;
			 $gmts_qty="gmts_qty_".$i;
			 $excess_per="excess_per_".$i;
			 $qty="qty_".$i;
			 $update_dtls_id="update_dtls_id_".$i;
			 if(str_replace("'",'',$$update_dtls_id)>0)
			 {
				$updateID_array[]=str_replace("'",'',$$update_dtls_id);
				$data_array_up[str_replace("'",'',$$update_dtls_id)]=explode("*",("".$txt_job."*".$$po_id."*".$cbo_fabric_part."*".$$color_id."*".$$size_id."*".$$item_size."*".$$gmts_qty."*".$$excess_per."*".$$qty."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
			}
			else
			{
				if ($new_data!=1) $data_array .=",";
				$data_array .="(".$id.",'".trim($booking)."',".$txt_job.",".$$po_id.",".$cbo_fabric_part.",".$$color_id.",".$$size_id.",".$$item_size.",".$$gmts_qty.",".$$excess_per.",".$$qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id=$id+1;
				$new_data++;
			}
		 }
		 $rID=execute_query(bulk_update_sql_statement("wo_booking_colar_culff_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
		 if($rID) $flag=1; else $flag=0;
		 if($data_array!='')
		 {
		  $rID1=sql_insert("wo_booking_colar_culff_dtls",$field_array,$data_array,0);
		  //echo "10** Insert into wo_booking_colar_culff_dtls ($field_array) values $data_array"; die;
		  if($flag==1)
			{
				if($rID1) $flag=1; else $flag=0;
			}
		 }

		//echo "10**".bulk_update_sql_statement("wo_booking_colar_culff_dtls","id",$field_array_up,$data_array_up,$updateID_array);die;

			//if($dtlsrID) $flag=1; else $flag=0;


	//============================================================================================

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$booking."**".$flag;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$booking."**".$flag;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$booking."**".$flag;
			}
			else{
				oci_rollback($con);
				echo "10**".$booking."**".$flag;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		//$field_array="status_active*is_deleted";
		//$data_array="'2'*'1'";
		//$rID=sql_delete("wo_po_color_size_breakdown",$field_array,$data_array,"id","".$hiddenid."",1);
		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			die;
		}
		$rID=execute_query( "update  wo_booking_colar_culff_dtls set status_active=0,is_deleted=1  where  pre_cost_fabric_cost_dtls_id =$cbo_fabric_part and job_no=".$txt_job." ",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$txt_job."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$txt_job."**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".$txt_job_no."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		disconnect($con);
		//echo "2****".$rID;
	}


}
if($action=="show_list_view")
{
	$FabricPart=array();
	$txt_booking_no=$data;
	$sql=sql_select("select b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,lib_body_part d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id  and c.body_part_id=d.id  and a.booking_no='$txt_booking_no' and d.body_part_type in(40,50)  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1");

	  foreach($sql as $row){
		  $FabricPart[$row[csf('id')]]=$body_part[$row[csf('body_part_id')]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")].', '.$row[csf("gsm_weight")];
	  }

	 $sql="select booking_no,job_no,pre_cost_fabric_cost_dtls_id,sum(gmts_qty) as gmts_qty,avg(excess_per) as excess_per,sum(qty) as qty  from wo_booking_colar_culff_dtls where booking_no ='$data' and status_active=1 and is_deleted=0 group by booking_no,job_no,pre_cost_fabric_cost_dtls_id";
	$sql_data=sql_select($sql);
	?>
	 <table width="550" class="rpt_table" border="1" rules="all">
            <thead>
            <tr>
                <th>
                Sl
                </th>
                <th>
                Job No
                </th>
                <th>
                Body Part
                </th>
                <th>
                Gmts Qty (Pcs)
                </th>
                <th>
                Excess %
                </th>
                <th>
                 Qty (Pcs)
                </th>
                </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($sql_data as $row){
				?>
                <tr onClick="show_sub_form_with_data('<? echo $row[csf('booking_no')]  ?>','<? echo $row[csf('pre_cost_fabric_cost_dtls_id')] ?>');">
                <td>
               <? echo $i; ?>
                </td>
                <td>
                 <? echo $row[csf('job_no')]; ?>
                </td>
                <td>
                <? echo $FabricPart[$row[csf('pre_cost_fabric_cost_dtls_id')]];  ?>
                </td>
                <td align="right">
                 <? echo $row[csf('gmts_qty')]; ?>
                </td>
                <td align="right">
                <? echo number_format($row[csf('excess_per')],2); ?>
                </td>
                <td align="right">
                 <? echo $row[csf('qty')]; ?>
                </td>
                </tr>
                <?
				}
				?>
                </tbody>
                </table>
                <?


}
if($action == "check_booking_approved"){
	$is_approved=0;
	$sql=sql_select("select is_approved from wo_booking_mst where booking_no='$data'");
	foreach($sql as $row){
		if($row[csf('is_approved')]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf('is_approved')];
		}
	}
	if($is_approved==1){
		echo "approved";
		die;
	}
}

if($action=="colur_cuff_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);

 ?>

	<script>
var permission='<? echo $permission; ?>';

function show_sub_form()
{
show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+document.getElementById('cbo_level').value+'**'+<? echo "'$permission'";?>,'load_color_size_form','form_data_con','partial_fabric_booking_controller','');
}

function show_list()
{
	//echo $txt_booking_no.'fff';
	var booking_no='<? echo $txt_booking_no;?>';

	show_list_view(booking_no,'show_list_view','list_view_con','partial_fabric_booking_controller','');
}
function show_sub_form_with_data(booking_no,fabric_cost_id){
	document.getElementById('cbo_fabric_part').value=fabric_cost_id;
	document.getElementById('booking_no').value=booking_no;
	show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+'00'+'**'+<? echo "'$permissions'";?>,'load_color_size_form','form_data_con','partial_fabric_booking_controller','');
	set_button_status(1, permission, 'fnc_colar_culff_dtls',1);

}

function calculate_qty(i,body_part_type){
	var gmts_qty=(document.getElementById('gmts_qty_'+i).value)*1;
	var excess_per=(document.getElementById('excess_per_'+i).value)*1;
	var txt_body_part=(document.getElementById('txt_body_part').value)*1;
	var qty=0;
	/*if(txt_body_part==3){
		qty=gmts_qty*2
	}else{
		qty=gmts_qty*1;
	}*/
	body_part_type=body_part_type*1;
	if(body_part_type==50){
		qty=gmts_qty*2
	}else{
		qty=gmts_qty*1;
	}

	var excess=(qty*excess_per)/100;
	qty=Math.ceil(qty+excess);
	document.getElementById('qty_'+i).value=qty;
}

function fnc_colar_culff_dtls( operation ){
		freeze_window(operation);
		var delete_cause='';
		var booking_no=document.getElementById('booking_no').value;
		var booking=return_global_ajax_value(booking_no, 'check_booking_approved', '', 'partial_fabric_booking_controller');
		if(operation == 1 || operation == 2){
			if(booking == 'approved'){
				alert("This booking is approved So Update/Delete Not Possible");
				release_freezing();
				return;
			}
		}
		if(operation==2){
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause==""){
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if(delete_cause==null){
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}

		var row_num=$('#colar_cuff_tbl tbody tr').length;
		//update_dtls_id_
		var data_all="";
		for (var i=1; i<=row_num; i++){
			data_all=data_all+get_submitted_data_string('cbo_fabric_part*booking_no*cbo_level*txt_body_part*txt_job*po_id_'+i+'*color_id_'+i+'*size_id_'+i+'*item_size_'+i+'*gmts_qty_'+i+'*excess_per_'+i+'*qty_'+i+'*update_dtls_id_'+i,"../../../",i);
		}
		var data="action=save_update_delete_colar_culff_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;

		http.open("POST","partial_fabric_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_colar_culff_dtls_reponse;
	}

	function fnc_colar_culff_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				show_list();
				reset_form('','form_data_con','','');
				release_freezing();
				//show_msg(trim(reponse[0]));
			 }
			  if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1){
			  	set_button_status(1, permission, 'fnc_colar_culff_dtls',1);
					release_freezing();
			 }
			 if(trim(reponse[0])=='approved'){
				 alert("This booking is approved So Update/Delete Not Possible");
				 release_freezing();
				 return;
			 }
			 if(trim(reponse[0])=='sal1'){
				 alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				 release_freezing();
				 return;
			 }
			 if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
		    release_freezing();
		    return;
		    }
			 release_freezing();
		}
	}

	function copy_value(row_id,type)
	{
	 	var copy_val=document.getElementById('check_excess_id').checked;

		//alert(body_part_type_id);
		var rowCount=$('#colar_cuff_tbl tbody tr').length;
		if(copy_val==true)
		  {
		 // alert(rowCount);

		   	for(var j=row_id; j<=rowCount; j++)
		  	{
				  if(type=='excessper')
				  {

					 var body_part_type_id=document.getElementById('body_part_type_id_'+j).value*1;
					  var excess_per=(document.getElementById('excess_per_'+row_id).value)*1;
					  document.getElementById('excess_per_'+j).value=excess_per;
					  calculate_qty(j,body_part_type_id);

				  }
			 } //Loop End
		  }

	 }
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
 <?
 //$sql="select a.id from lib_body_part a, wo_pre_cost_fabric_cost_dtls b ,wo_booking_dtls c   where a.id=b.body_part_id and b.job_no=c.job_no and b.id=c.pre_cost_fabric_cost_dtls_id   and a.body_part_type=40 and a.status_active=1 and a.is_deleted=0";
//echo  "select b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,lib_body_part d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id and and c.body_part_id=d.id c.body_part_id in (2,3) and a.booking_no='$txt_booking_no' and d.body_part_type=40  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1";
  $FabricPart=array();
  $sql=sql_select("select b.job_no,b.po_break_down_id,c.id, c.body_part_id,c.color_type_id ,c.fabric_description ,c.gsm_weight from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,lib_body_part d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=c.id  and c.body_part_id=d.id  and a.booking_no='$txt_booking_no' and d.body_part_type in(40,50) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1");//and c.body_part_id in (2,3)
  foreach($sql as $row){
	  $FabricPart[$row[csf('id')]]=$body_part[$row[csf('body_part_id')]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")].', '.$row[csf("gsm_weight")];
  }
  if(count($FabricPart)==0){
	  echo "No Colar Or Cullf Found in this Booking";

	  die;
  }
 ?>
<fieldset>
    <form autocomplete="off">
        <table width="550" class="rpt_table" border="1" rules="all">
            <tr>
            <td>Body Part</td>
            <td>
            <?
            echo create_drop_down( "cbo_fabric_part", 400, $FabricPart,"", 1, "-- Select--", 0, "","","");
            ?>
			 <input style="width:40px;" type="checkbox" class="text_boxes"  name="check_excess_id" id="check_excess_id" checked="checked"  />

            <input style="width:60px;" type="hidden" class="text_boxes"  name="booking_no" id="booking_no" value="<? echo trim($txt_booking_no); ?> " />
			 <input style="width:60px;" type="hidden" class="text_boxes"  name="cbo_level" id="cbo_level" value="<? echo $cbo_level; ?> " />
            </td>
            <td><input type="button" class="formbutton" value="Show" onClick="show_sub_form()"/> </td>
            </tr>
        </table>

    <div id="form_data_con">
    </div>
    </form>
    <div id="list_view_con">
    </div>
</fieldset>
</div>
</body>
<script>
show_list('<? echo $txt_booking_no?>');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
}






if($action=="show_fabric_booking_report_urmi"){
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$report_type=str_replace("'","",$report_type);
	$mail_id=str_replace("'","",$mail_id);
	$is_mail_send=str_replace("'","",$is_mail_send);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$uom=0;
	$job_data_arr=array();
	$nameArray_buyer=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
	foreach ($nameArray_buyer as $result_buy){
	$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
	$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
	$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
	$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
	$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
	$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
	$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
	$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
	$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
	$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
	$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
	$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
	$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
	$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	}

	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(",",array_unique($job_data_arr['client']));
	?>
	<div style="width:1330px" align="center">
	<?php
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0 ");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0 ");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
$path = str_replace("'", "", $path);
if ($path != "") {
	$path = $path;
}elseif($image_cond==1){ $path="../../../";} else {
	$path = "../../";
}

?>										<!--    Header Company Information         -->
	<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
	<tr>
		<td width="100">
		<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
		</td>
		<td width="1250">
	<table width="100%" cellpadding="0" cellspacing="0"  >
		<tr>
			<td align="center" style="font-size:20px;">
			<?php
            echo $company_library[$cbo_company_name];
             ?>
			</td>
			<td rowspan="3" width="250">

			<span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($job_no,"'"); ?></b></span><br/>
			<?
			if($nameArray_approved_row[csf('approved_no')]>1)
			{
			?>
			<b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
			<br/>
			Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
			<?
			}
			?>


			</td>
		</tr>
		<tr>
		<td align="center" style="font-size:14px">
		<?
		$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
		if($txt_job_no!="")
		{
		$location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
		}
		else
		{
		$location="";
		}

		foreach ($nameArray as $result)
		{
		echo  $location_name_arr[$location];
		?>

		Email Address: <? echo $result[csf('email')];?>
		Website No: <? echo $result[csf('website')]; ?>

		<?

		}

		?>
		</td>
		</tr>
		<tr>
		<td align="center" style="font-size:20px">
		<strong><? if($report_title !=""){ echo $report_title;} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
		</td>
		</tr>
	</table>
	</td>
	</tr>
	</table>
	<?
	$po_data=array();
	if($db_type==0){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	if($db_type==2){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	foreach ($nameArray_job as $result_job){
		$po_data['po_id'][$result_job[csf('id')]]=$result_job[csf('id')];
		$po_data['po_number'][$result_job[csf('id')]]=$result_job[csf('po_number')];
		$po_data['leadtime'][$result_job[csf('id')]]=$result_job[csf('date_diff')];
		$po_data['po_quantity'][$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		$po_data['po_received_date'][$result_job[csf('id')]]=change_date_format($result_job[csf('po_received_date')],'dd-mm-yyyy','-');
		$ddd=strtotime($result_job[csf('pub_shipment_date')]);
		$po_data['pub_shipment_date'][$ddd]=$ddd;
		$po_data['insert_date'][$result_job[csf('id')]]=$result_job[csf('insert_date')];

		if($result_job[csf('shiping_status')]==1){
		$shiping_status= "FP";
		}
		else if($result_job[csf('shiping_status')]==2){
		$shiping_status= "PS";
		}
		else if($result_job[csf('shiping_status')]==3){
		$shiping_status= "FS";
		}
		$po_data['shiping_status'][$result_job[csf('id')]]=$shiping_status;
		$po_data['file_no'][$result_job[csf('id')]]=$result_job[csf('file_no')];
		$po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
	}
	$txt_order_no_id=implode(",",array_unique($po_data['po_id']));
	$leadtime=implode(",",array_unique($po_data['leadtime']));
	$po_quantity=array_sum($po_data['po_quantity']);
	$po_received_date=implode(",",array_unique($po_data['po_received_date']));
	$po_number=implode(",",array_unique($po_data['po_number']));
	$shipment_date=date('d-m-Y',min($po_data['pub_shipment_date']));
	$maxshipment_date=date('d-m-Y',max($po_data['pub_shipment_date']));
	$shiping_status=implode(",",array_unique($po_data['shiping_status']));
	$file_no=implode(",",array_unique($po_data['file_no']));
	$grouping=implode(",",array_unique($po_data['grouping']));



	$colar_excess_percent=0;
	$cuff_excess_percent=0;
	$rmg_process_breakdown=0;
	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,adv_booking_no from wo_booking_mst a  where   a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 ");
	foreach ($nameArray as $result)
	{
		$total_set_qnty=$result[csf('total_set_qnty')];
		$colar_excess_percent=$result[csf('colar_excess_percent')];
		$cuff_excess_percent=$result[csf('cuff_excess_percent')];
		$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
		foreach ($po_data['po_id'] as $po_id=>$po_val){
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$po_data['pub_shipment_date'][$po_id])-1).",";
			$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
			$WOPreparedAfter.=(datediff('d',$po_data['insert_date'][$po_id],$booking_date)-1).",";
		}
	?>
	<table width="100%" style="border:1px solid black;table-layout: fixed;" >
	<tr>
	<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
	</tr>
	<tr>
	<td width="200"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
	<td width="220">:&nbsp;<span style="font-size:18px"><b><? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></b></span></td>
	<td width="200"><span style="font-size:12px"><b>Dept.</b></span></td>
	<td width="220">:&nbsp;
	<?
	echo $product_depertment ;
	if($product_code !=""){
	echo " (".$product_code.")";
	}
	if($pro_sub_dep != ""){
	echo " (".$pro_sub_dep.")";
	}
	?>
	</td>
	<td width="200"><span style="font-size:12px"><b>Order Qnty</b></span></td>
	<td>:&nbsp; <?  echo $po_quantity;//." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?> </td>
	</tr>
	<tr>

	<td style="font-size:12px"><b>Garments Item</b></td>
	<td>:&nbsp;
	<?
	$gmts_item_name="";
	$gmts_item=explode(',',$gmts_item_id);
	for($g=0;$g<=count($gmts_item); $g++)
	{
	$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
	}
	echo rtrim($gmts_item_name,',');
	?>
	</td>
	<td style="font-size:12px"><b>Booking Release Date</b></td>
	<td>:&nbsp;
	<?
	$booking_date=$result[csf('update_date')];
	if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
	{
	$booking_date=$result[csf('insert_date')];
	}
	echo change_date_format($booking_date,'dd-mm-yyyy','-','');
	?>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-size:18px"><b>Style Ref.</b>   </td>
	<td style="font-size:18px">:&nbsp;<b>
	<?
	echo $style_sting;
	?>
	</b>
	</td>
	</tr>
	<tr>
	<td style="font-size:12px"><b>Style Des.</b></td>
	<td>:&nbsp;<? echo $style_description;?></td>
	<td style="font-size:12px"><b>Season</b></td>
	<td>:&nbsp;<? echo $season_matrix; ?></td>
	<td style="font-size:12px"><b>Dealing Merchant</b></td>
	<td>:&nbsp;<? echo $dealing_marchant; ?></td>
	</tr>

	<tr>
	<td style="font-size:12px"><b>Supplier Name</b>   </td>
	<td>:&nbsp;
	<?
	if($result[csf('pay_mode')]==5){
	echo $company_library[$result[csf('supplier_id')]];
	}
	else{
	echo $supplier_name_arr[$result[csf('supplier_id')]];
	}
	?>    </td>
	<td style="font-size:12px"><b>Delivery Date</b></td>
	<td>:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
	<td style="font-size:18px"><b>Booking No </b>   </td>
	<td style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?> <? //echo "(".$unit_of_measurement[$result[csf('uom')]].")"; $uom=$result[csf('uom')];?></td>
	</tr>
	<tr>
	<td  style="font-size:12px"><b>Attention</b></td>
	<td  >:&nbsp;<? echo $result[csf('attention')]; ?></td>
	<td style="font-size:12px"><b>Lead Time </b>   </td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
	<?
	echo $leadtime;
	?>
	</td>
	<td  style="font-size:12px"><b>Po Received Date</b></td>
	<td  >:&nbsp;<? echo $po_received_date; ?></td>
	</tr>
	<tr>
	<td style="font-size:18px"><b>Order No</b></td>
	<td style="font-size:18px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="3">:&nbsp;<? echo $po_number; ?></td>
	<td  style="font-size:12px"><b>Repeat No</b></td>
	<td  >:&nbsp;<? echo $order_repeat_no; ?></td>
	</tr>
	<tr>
	<td style="font-size:12px"><b>Shipment Date</b></td>
	<td colspan="3" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> : First:&nbsp;<? echo rtrim($shipment_date,", "); //echo $max_pub_shipment_date; ?>, Last: <? echo $maxshipment_date; ?></td>
	<td  style="font-size:12px"><b>Quality Label</b></td>
	<td  >:&nbsp;<? echo $qlty_label; ?></td>
	</tr>
	</tr>
	<tr>
	<td style="font-size:12px"><b>WO Prepared After</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
	<?
	$WOPreparedAfter=implode(",",array_unique(explode(",",chop($WOPreparedAfter,","))));
	echo $WOPreparedAfter.' Days' ;
	?></td>

	<td style="font-size:12px"><b>Ship.days in Hand</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
	<?
	$daysInHand=implode(",",array_unique(explode(",",chop($daysInHand,","))));
	echo $daysInHand.' Days' ;
	?></td>

	<td style="font-size:12px"><b>Ex-factory status</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
	<?
	echo $shiping_status;
	?></td>

	</tr>
	<tr>
	<td style="font-size:18px"><b>Internal Ref No</b></td>
	<td style="font-size:18px"> :&nbsp;<b><? echo $grouping; ?></b></td>
	<td style="font-size:18px"><b>File no</b></td>
	<td style="font-size:18px"> :&nbsp;<b><? echo  $file_no;?></b></td>
	<td style="font-size:18px"><b>Currency</b></td>
	<td style="font-size:18px"> :&nbsp;<b><? echo  $currency[$result[csf("currency_id")]];?></b></td>
	</tr>
	<tr>
	<td style="font-size:18px"><b>Rmarks</b></td>
	<td style="font-size:18px"> :<? echo $result[csf('remarks')]?></td>
	<td style="font-size:18px">:&nbsp;</td>
	<td style="font-size:18px">&nbsp; </td>
	<td style="font-size:18px"><b>Advance Booking</b></td>
	<td style="font-size:18px"> :&nbsp;<b><? echo  $result[csf("adv_booking_no")];?></b></td>
	</tr>
	<tr>
	<td style="font-size:18px"><b>Fabric Composition</b></td>
	<td style="font-size:18px" colspan="5"> :<? echo $result[csf('fabric_composition')]?></td>
	</tr>

	</table>
	<?
	}

	if($cbo_fabric_source==1 || $cbo_fabric_source==2){
	$nameArray_size=sql_select( "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active!=0 group by size_number_id order by size_order");
	?>
	<table width="100%" >
	<tr>
	<td width="800">
	<div id="div_size_color_matrix" style="float:left; max-width:1000;">
	<fieldset id="div_size_color_matrix" style="max-width:1000;">
	<legend>Size and Color Breakdown</legend>
	<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
	<tr>
	<td style="border:1px solid black"><strong>Color/Size</strong></td>
	<?
	foreach($nameArray_size  as $result_size)
	{	     ?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?	}    ?>
	<td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
	<td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
	<td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
	</tr>
	<?
	$color_size_order_qnty_array=array();
	$color_size_qnty_array=array();
	$size_tatal=array();
	$size_tatal_order=array();
	for($c=0;$c<count($gmts_item); $c++)
	{
	$item_size_tatal=array();
	$item_size_tatal_order=array();
	$item_grand_total=0;
	$item_grand_total_order=0;
	$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active!=0 group by color_number_id  order by color_order");
	?>
	<tr>
	<td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>

	</tr>
	<?
	foreach($nameArray_color as $result_color)
	{
	?>
	<tr>
	<td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
	<?
	$color_total=0;
	$color_total_order=0;

	foreach($nameArray_size  as $result_size)
	{
	$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active!=0 and is_deleted =0");
	foreach($nameArray_color_size_qnty as $result_color_size_qnty)
	{
	?>
	<td style="border:1px solid black; text-align:right">
	<?
	if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
	{
	echo number_format($result_color_size_qnty[csf('order_quantity')],0);
	$color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
	$color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
	$item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
	$grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
	$grand_total_order +=$result_color_size_qnty[csf('order_quantity')];


	$color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
	{
	$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
	}
	else
	{
	$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	}
	if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
	{
	$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
	}
	else
	{
	$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	}
	}
	else echo "0";
	?>
	</td>

	<?
	}
	}
	?>
	<td style="border:1px solid black; text-align:right"><?  echo number_format(round($color_total_order),0); ?></td>

	<td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?>
	</td>
	<td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
	</tr>
	<?
	}
	?>

	<td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
	<?
	foreach($nameArray_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
	<td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
	</tr>
	<tr>
	<tr>
	<td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
	<?
	foreach($nameArray_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
	<td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
	</tr>
	</table>
	</fieldset>
	</div>
	</td>
	<td width="200" valign="top" align="left">
	<?php ob_start();?>
	<div id="div_size_color_matrix" style="float:left;">
	<?
	$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
	?>
	<fieldset>
	<legend>RMG Process Loss % </legend>
	<table width="180" class="rpt_table" border="1" rules="all">
	<?
	if(number_format($rmg_process_breakdown_arr[8],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[8],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[2],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Chest Printing <!-- Printing % breack Down 2-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[2],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[10],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Neck/Sleeve Printing <!-- New breack Down 10-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[10],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[1],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Embroidery   <!-- Embroidery  % breack Down 1-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[1],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[4],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Sewing /Input<!-- Sewing % breack Down 4-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[4],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[3],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Garments Wash <!-- Washing %breack Down 3-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[3],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[15],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Gmts Finishing <!-- Washing %breack Down 3-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[15],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[11],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Others <!-- New breack Down 11-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[11],2);
	?>
	</td>
	</tr>
	<?
	}
	$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
	if($gmts_pro_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Sub Total <!-- New breack Down 11-->
	</td>
	<td align="right">
	<?

	echo number_format($gmts_pro_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	?>
	</table>
	</fieldset>


	<fieldset>
	<legend>Fabric Process Loss % </legend>
	<table width="180" class="rpt_table" border="1" rules="all">
	<?
	if(number_format($rmg_process_breakdown_arr[6],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Knitting  <!--  Knitting % breack Down 6-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[6],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[12],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Yarn Dyeing  <!--  New breack Down 12-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[12],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[5],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Dyeing & Finishing  <!-- Finishing % breack Down 5-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[5],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[13],2)>0)
	{
	?>
	<tr>
	<td width="130">
	All Over Print <!-- new  breack Down 13-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[13],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[14],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Lay Wash (Fabric) <!-- new  breack Down 14-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[14],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[7],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Dying   <!-- breack Down 7-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[7],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[0],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Cutting (Fabric) <!-- Cutting % breack Down 0-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[0],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[9],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Others  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[9],2);
	?>
	</td>
	</tr>
	<?
	}
	$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
	if(fab_proce_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Sub Total  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?

	echo number_format($fab_proce_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Grand Total  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?
	echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	?>
	</table>
	</fieldset>
	</div>
	</td>
	<td width="330" valign="top" align="left">
	<?
	$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in($job_no_in) and file_type=1");
	?>
	<div id="div_size_color_matrix" style="float:left;">
	<fieldset>
	<legend>Image</legend>
	<table width="310">
	<tr>
	<?
	$img_counter = 0;
	foreach($nameArray_imge as $result_imge)
	{
	if($path=="")
	{
	$path='../../';
	}
	?>
	<td>
	<img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
	</td>
	<?

	$img_counter++;
	}
	?>
	</tr>
	</table>
	</fieldset>
	</div>
	</td>
	</tr>
	</table>
	<?
	}// if($cbo_fabric_source==1) end

	?>
	<br/>
	<!--  Here will be the main portion  -->
	<?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($job_no_in)");
	if($costing_per_id==1)
	{
	$costing_per="1 Dzn";
	$costing_per_qnty=12;

	}
	if($costing_per_id==2)
	{
	$costing_per="1 Pcs";
	$costing_per_qnty=1;

	}
	if($costing_per_id==3)
	{
	$costing_per="2 Dzn";
	$costing_per_qnty=24;

	}
	if($costing_per_id==4)
	{
	$costing_per="3 Dzn";
	$costing_per_qnty=36;

	}
	if($costing_per_id==5)
	{
	$costing_per="4 Dzn";
	$costing_per_qnty=48;
	}
	$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no in($job_no_in)");

	$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");
	foreach($uom_arr as $uom_id=>$uom_val){
	if($cbo_fabric_source==1){
	$nameArray_fabric_description= sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , b.dia_width,d.pre_cost_remarks, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and
	b.color_number_id=d.gmts_color_id and
	b.dia_width=d.dia_width and
	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	d.status_active=1 and
	d.is_deleted=0 and
	b.cons>0
	group by a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,d.pre_cost_remarks order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");
	if(count($nameArray_fabric_description)>0){
	?>

	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	<caption>Fabric Details in <? echo $uom_val ?></caption>
	<tr align="center">
	<th colspan="3" align="left">Item Name</th>
	<?

	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>". $garments_item[$result_fabric_description[csf('item_number_id')]]."</td>";
	}
	?>
	<td  rowspan="11" width="50"><p>Total Finish Fabric (Kg)</p></td>

	<td  rowspan="11" width="50"><p>Avg Rate <? echo "(".$unit_of_measurement[$uom].")";?></p></td>
	<td  rowspan="11" width="50"><p>Amount </p></td>

	</tr>
	<tr align="center">
	<th colspan="3" align="left">Body Part</th>
	<?

	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>".$body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Color Type</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Fabric Construction</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
	}
	?>


	</tr>
	<tr align="center"><th   colspan="3" align="left">Yarn Composition</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
	else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th  colspan="3" align="left">GSM</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('cons')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2)."</td>";
	}
	?>
	</tr>
    <tr align="center"><th   colspan="3" align="left">Remarks</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('pre_cost_remarks')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('pre_cost_remarks')]."</td>";
		}
		?>

       </tr>
	<tr>
	<th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
	</tr>
	<tr>
	<th  width="120" align="left">Fabric Color</th>
	<th  width="120" align="left">Body Color</th>
	<th  width="120" align="left">Lab Dip No</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
	}
	?>
	</tr>
	<?
	$gmt_color_library=array();
	$gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
	WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.uom=$uom_id  and a.fabric_source =$cbo_fabric_source and
	a.job_no in ($job_no_in)");
	foreach( $gmt_color_data as $gmt_color_row){
	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
	}
	$grand_total_fin_fab_qnty=0;
	$grand_total_amount=0;
	$color_wise_wo_sql=sql_select("select b.fabric_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_booking_dtls b
	WHERE
	a.id=b.pre_cost_fabric_cost_dtls_id and
	a.uom=$uom_id and
	b.booking_no =$txt_booking_no and
	b.status_active=1 and
	b.is_deleted=0
	group by b.fabric_color_id");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	?>
	<tr>
	<td  width="120" align="left">
	<?
	echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
	?>
	</td>
	<td>
	<?
	echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
	?>
	</td>
	<td  width="120" align="left">
	<?
	$lapdip_no="";
	$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
	if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
	?>
	</td>
	<?
	$total_fin_fab_qnty=0;
	$total_amount=0;

	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if($db_type==0)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.fabric_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	}
	if($db_type==2)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
	d.status_active=1 and
	d.is_deleted=0
	");
	}
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'>
	<?
	if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
	$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	if($color_wise_wo_result_qnty[csf('rate')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('rate')],5);
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	$amount=def_number_format($color_wise_wo_result_qnty[csf('amount')],2,'',0);
	if($amount!="")
	{
	echo $amount;
	$total_amount+=$amount;
	}
	?>
	</td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
	<td align="right"><? echo def_number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
	<td align="right">
	<?
	echo def_number_format($total_amount,2);

	?>
	</td>
	</tr>
	<?
	}
	?>
	<tr style=" font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Total</strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'><?  echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($grand_total_fin_fab_qnty,2);?></td>
	<td align="right"><? echo def_number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
	<td align="right">
	<?
	echo def_number_format($grand_total_amount,2);
	?>
	</td>
	</tr>
	<!--<tr style="font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.grey_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty*/
	?>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<?
	}
	?>
	<td align="right">
	<?
	//$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
	//echo number_format($consumption_per_unit_fab,2);
	?>
	</td>
	<td align="right">
	<?
	//$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
	//echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
	?>
	</td>
	<td align="right" title="Only Allow Round Figer">
	<?
	//echo number_format($consumption_per_unit_amuont,2);
	?>
	</td>
	</tr> -->
	</table>
	<br/>
	<?
	}
	}
	}
	//===========================

	foreach($uom_arr as $uom_id=>$uom_val){
	if($cbo_fabric_source==2){
	$nameArray_fabric_description= sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , b.dia_width,d.pre_cost_remarks, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and
	b.color_number_id=d.gmts_color_id and
	b.dia_width=d.dia_width and
	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	d.status_active=1 and
	d.is_deleted=0  and
	b.cons>0
	group by a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,d.pre_cost_remarks order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");
	if(count($nameArray_fabric_description)>0){
	?>
	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	<caption>Fabric Details in <? echo $uom_val;?></caption>
	<tr align="center">
	<th colspan="3" align="left">Item Name</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>". $garments_item[$result_fabric_description[csf('item_number_id')]]."</td>";
	}
	?>
	<td  rowspan="11" width="50"><p>Total Finish Fabric</p></td>

	<td  rowspan="11" width="50"><p>Avg Rate</p></td>
	<td  rowspan="11" width="50"><p>Amount </p></td>
	</tr>
	<tr align="center">
	<th colspan="3" align="left">Body Part</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>".$body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Color Type</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Fabric Construction</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th   colspan="3" align="left">Yarn Composition</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
	else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th  colspan="3" align="left">GSM</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('cons')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2)."</td>";
	}
	?>

	</tr>
    <tr align="center"><th   colspan="3" align="left">Remarks</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('pre_cost_remarks')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('pre_cost_remarks')]."</td>";
		}
		?>

       </tr>
	<tr>
	<th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
	</tr>
	<tr>
	<th  width="120" align="left">Fabric Color</th>
	<th  width="120" align="left">Body Color</th>
	<th  width="120" align="left">Lab Dip No</th>
	<?
	if($cbo_fabric_source==2)
	{
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
	}
	}
	else
	{
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th>";
	}
	}

	?>
	</tr>
	<?
	$gmt_color_library=array();

	$gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
	WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.uom=$uom_id  and a.fabric_source =$cbo_fabric_source and
	a.job_no in ($job_no_in)");
	foreach( $gmt_color_data as $gmt_color_row){
	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
	}
	$grand_total_fin_fab_qnty=0;
	$grand_total_amount=0;
	$color_wise_wo_sql=sql_select("select b.fabric_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_booking_dtls b
	WHERE
	a.id=b.pre_cost_fabric_cost_dtls_id and
	a.uom=$uom_id and
	b.booking_no =$txt_booking_no and
	b.status_active=1 and
	b.is_deleted=0
	group by b.fabric_color_id");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	?>
	<tr>
	<td  width="120" align="left">
	<?
	echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
	?>
	</td>
	<td>
	<?
	echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
	?>
	</td>
	<td  width="120" align="left">
	<?
	$lapdip_no="";
	$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
	if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
	?>
	</td>
	<?
	$total_fin_fab_qnty=0;
	$total_amount=0;

	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if($db_type==0)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.fabric_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	}
	if($db_type==2)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
	d.status_active=1 and
	d.is_deleted=0
	");
	}
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'>
	<?
	if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
	$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	if($color_wise_wo_result_qnty[csf('rate')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('rate')],5);
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	$amount=def_number_format($color_wise_wo_result_qnty[csf('amount')],2,'',0);
	if($amount!="")
	{
	echo $amount;
	$total_amount+=$amount;
	}
	?>
	</td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
	<td align="right"><? echo def_number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
	<td align="right">
	<?
	echo def_number_format($total_amount,2);

	?>
	</td>
	</tr>
	<?
	}
	?>
	<tr style=" font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Total</strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'><?  echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
	<td width='50' align='right' ></td>
	<td width='50' align='right' ></td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($grand_total_fin_fab_qnty,2);?></td>
	<td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
	<td align="right">
	<?
	echo def_number_format($grand_total_amount,2);
	?>
	</td>
	</tr>
	<!--<tr style="font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.grey_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty*/

	?>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<?
	}
	?>
	<td align="right">
	<?
	//$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
	//echo number_format($consumption_per_unit_fab,2);
	?>
	</td>
	<td align="right">
	<?
	//$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
	//echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
	?>
	</td>
	<td align="right" title="Only Allow Round Figer">
	<?
	//echo number_format($consumption_per_unit_amuont,2);
	?>
	</td>
	</tr> -->
	</table>
	<br/>
	<?
	}
	}
	}
	//===========================
	?>
    <?
	$sql_data=sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark, sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.adjust_qty)as adjust_qty,sum(b.fin_fab_qnty) as fin_fab_qnty,avg(b.rate) as rate,sum(b.grey_fab_qnty*b.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.adjust_qty>0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark order by a.id,a.body_part_id");

		?>
		<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

        <tr>
        <td colspan="7">
        <strong>Fabric Stock Adjustment Details</strong>
        </td>

        </tr>

        <tr>
        <td>
        Fabrication
        </td>
        <td>
        Process
        </td>
        <td>
        Fabric Color
        </td>
        <td>
        Required
        </td>
        <td>
        Stock Used
        </td>
        <td>
        Booking Qty
        </td><td>
        Uom
        </td>
         <td>
        Remarks
        </td>
        </tr>
        <?
		foreach($sql_data as $row){
		?>
          <tr>
        <td>
        <? echo $body_part[$row[csf('body_part_id')]].",".$color_type[$row[csf('color_type_id')]].",".$row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]].",".$row[csf('dia_width')]  ?>
        </td>
        <td>
        <? echo $row[csf('pre_cost_remarks')];  ?>
        </td>
        <td>
        <? echo $color_library[$row[csf('fabric_color_id')]];  ?>
        </td>
        <td align="right">
        <? echo number_format($row[csf('grey_fab_qnty')],4);  ?>
        </td>
        <td align="right">
         <? echo number_format($row[csf('adjust_qty')],4) ; ?>
        </td>
        <td align="right">
       <? echo number_format($row[csf('fin_fab_qnty')],4);  ?>
        </td>
         <td>
        <? echo $unit_of_measurement[$row[csf('uom')]];  ?>
        </td>
         <td>
         <? echo $row[csf('remark')];  ?>
        </td>
        </tr>
        <?
		}
		?>
        </table>


	<?
	//echo $cbo_fabric_source;
	if($cbo_fabric_source==1 || $cbo_fabric_source==2){
	?>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
	<tr>
	<?

	$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
	</tr>
	<tr>
	<td>Collar Size</td>

	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	<?
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	$color_total_collar=0;
	$color_total_collar_order_qnty=0;
	$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
	$constrast_color_arr=array();
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
	for($i=0;$i<count($constrast_color);$i++)
	{
	$constrast_color2=explode('_',$constrast_color[$i]);
	$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
	}
	}
	?>
	<tr>
	<td>
	<?
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
	$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
	}
	else
	{
	echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
	$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
	}
	?>
	</td>
	<?

	foreach($nameArray_item_size  as $result_size)
	{
		 $sql_excess_per="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
	 a.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=2 and a.status_active=1 ";
	$resultData=sql_select($sql_excess_per);
	list($excess_percent)=$resultData;
	$colar_excess_percent=$excess_percent[csf('excess_per')];

	?>
	<td align="center" style="border:1px solid black" title="<? echo $colar_excess_percentage;?>">
	<?


	$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
	list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
	$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
	$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
	echo number_format($plan_cut+$colar_excess_per,0);
	$color_total_collar+=$plan_cut+$colar_excess_per;
	$color_total_collar_order_qnty+=$plan_cut;
	$grand_total_collar+=$plan_cut+$colar_excess_per;
	$grand_total_collar_order_qnty+=$plan_cut;
	?>
	</td>
	<?
	}
	?>
	<td align="center"><? echo number_format($color_total_collar,0); ?></td>
	<td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td>Size Total</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
	<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
	</tr>
	</table>
	</td>
	<td width="2%">
	</td>
	<?
	}
	?>
	<?
	$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");

	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>

	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
	</tr>
	<tr>
	<td>Cuff Size</td>

	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	<?
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	$color_total_cuff=0;
	$color_total_cuff_order_qnty=0;
	$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
	$constrast_color_arr=array();
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
	for($i=0;$i<count($constrast_color);$i++)
	{
	$constrast_color2=explode('_',$constrast_color[$i]);
	$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
	}
	}
	?>
	<tr>
	<td>
	<?
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
	$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
	}
	else
	{
	echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
	$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
	}
	?>
	</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
		 $sql_excess_cuff="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
	 a.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=3 and a.status_active=1 ";
	$resultData_cuff=sql_select($sql_excess_cuff);
	list($cuff_excess_percent)=$resultData_cuff;
	$cuff_excess_percent=$cuff_excess_percent[csf('excess_per')];
	?>
	<td align="center" style="border:1px solid black">
	<?
	$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
	list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
	$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
	$cuff_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
	echo number_format($plan_cut*2+$cuff_excess_per,0);
	$color_total_cuff+=$plan_cut*2+$cuff_excess_per;
	$color_total_cuff_order_qnty+=$plan_cut*2;
	$grand_total_cuff+=$plan_cut*2+$cuff_excess_per;
	$grand_total_cuff_order_qnty+=$plan_cut*2;
	?>
	</td>
	<?
	}
	?>
	<td align="center"><? echo number_format($color_total_cuff,0); ?></td>
	<td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td>Size Total</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
	<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
	</tr>
	</table>
	</td>
	<?
	}
	?>
	</tr>
	</table>
	<br/>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
	<tr>
	<?

	$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar Tipping -  Colour Size Brakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
	</tr>
	<tr>
	<td>Collar Size</td>
	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	<?
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	$color_total_collar_tipping=0;
	$color_total_collar_tipping_order_qnty=0;
	$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
	$constrast_color_arr=array();
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
	for($i=0;$i<count($constrast_color);$i++)
	{
	$constrast_color2=explode('_',$constrast_color[$i]);
	$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
	}
	}
	?>
	<tr>
	<td>
	<?
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
	$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
	}
	else
	{
	echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
	$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
	}
	?>
	</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
		$sql_excess_collarTip="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
	 a.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=172 and a.status_active=1 ";
	$resultData_collar_tip=sql_select($sql_excess_collarTip);
	list($collarTip_excess_percent)=$resultData_collar_tip;
	$colar_excess_percent=$collarTip_excess_percent[csf('excess_per')];

	?>
	<td align="center" style="border:1px solid black">
	<?
	$color_tipping_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");

	list($plan_cut_qnty)=$color_tipping_wise_wo_sql_qnty;
	$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
	$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
	echo number_format($plan_cut+$colar_excess_per,0);
	$color_total_collar_tipping+=$plan_cut+$colar_excess_per;
	$color_total_collar_tipping_order_qnty+=$plan_cut;
	$grand_total_collar_tipping+=$plan_cut+$colar_excess_per;
	$grand_total_collar_tipping_order_qnty+=$plan_cut;
	?>
	</td>
	<?
	}
	?>
	<td align="center"><? echo number_format($color_total_collar_tipping,0); ?></td>
	<td align="center"><? echo number_format((($color_total_collar_tipping-$color_total_collar_tipping_order_qnty)/$color_total_collar_tipping_order_qnty)*100,2); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td>Size Total</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar_tipping,0); ?></td>
	<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar_tipping-$grand_total_collar_tipping_order_qnty)/$grand_total_collar_tipping_order_qnty)*100,2); ?></td>
	</tr>
	</table>
	</td>
	<td width="2%">
	</td>
	<?
	}
	?>
	<?
	$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff Tipping -  Colour Size Brakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
	</tr>
	<tr>
	<td>Cuff Size</td>
	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	<?
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	$color_total_cuff_tipping=0;
	$color_total_cuff_tipping_order_qnty=0;
	$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
	$constrast_color_arr=array();
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
	for($i=0;$i<count($constrast_color);$i++)
	{
	$constrast_color2=explode('_',$constrast_color[$i]);
	$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
	}
	}
	?>
	<tr>
	<td>
	<?
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
	$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
	}
	else
	{
	echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
	$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
	}
	?>
	</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
		$sql_excess_cuffTip="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
	 a.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=214 and a.status_active=1 ";
	$resultData_cuff_tip=sql_select($sql_excess_cuffTip);
	list($cuffTip_excess_percent)=$resultData_cuff_tip;
	$cuff_excess_percent=$cuffTip_excess_percent[csf('excess_per')];

	?>
	<td align="center" style="border:1px solid black">
	<?
	$cuff_tipping_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
	list($plan_cut_qnty)=$cuff_tipping_wise_wo_sql_qnty;
	$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
	$cuff_tipping_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
	echo number_format($plan_cut*2+$cuff_tipping_excess_per,0);
	$color_total_cuff_tipping+=$plan_cut*2+$cuff_tipping_excess_per;
	$color_total_cuff_tipping_order_qnty+=$plan_cut*2;
	$grand_total_cuff_tipping+=$plan_cut*2+$cuff_excess_per;
	$grand_total_cuff_tipping_order_qnty+=$plan_cut*2;
	?>
	</td>
	<?
	}
	?>
	<td align="center"><? echo number_format($color_total_cuff_tipping,0); ?></td>
	<td align="center" title="<? echo $color_total_cuff_tipping."**".$color_total_cuff_tipping_order_qnty; ?>"><? echo number_format((($color_total_cuff_tipping-$color_total_cuff_tipping_order_qnty)/$color_total_cuff_tipping_order_qnty)*100,2); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td>Size Total</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff_tipping,0); ?></td>
	<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff_tipping-$grand_total_cuff_tipping_order_qnty)/$grand_total_cuff_tipping_order_qnty)*100,2); ?></td>
	</tr>
	</table>
	</td>
	<?
	}
	?>
	</tr>
	</table>
	<br/>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
        <?
			echo get_spacial_instruction($txt_booking_no,'930px',271);
		?>
    </td>
	<td width="2%">
	</td>
	<td width="49%" valign="top">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr align="center">
	<td><b>Approved Instructions</b></td>
	</tr>
	<tr>
	<td>
	<?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
	</td>
	</tr>
	</table>
	<br />
	</td>
	</tr>
	</table>
	<br>
	<?
	//------------------------------ Query for TNA start-----------------------------------
	$po_id_all=str_replace("'","",$txt_order_no_id);
	$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
	$tna_start_sql=sql_select( "select id,po_number_id,
	(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
	(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
	(case when task_number=60 then task_start_date else null end) as knitting_start_date,
	(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
	(case when task_number=61 then task_start_date else null end) as dying_start_date,
	(case when task_number=61 then task_finish_date else null end) as dying_end_date,
	(case when task_number=73 then task_start_date else null end) as finishing_start_date,
	(case when task_number=73 then task_finish_date else null end) as finishing_end_date,
	(case when task_number=84 then task_start_date else null end) as cutting_start_date,
	(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
	(case when task_number=86 then task_start_date else null end) as sewing_start_date,
	(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
	(case when task_number=110 then task_start_date else null end) as exfact_start_date,
	(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
	(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
	(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
	from tna_process_mst
	where status_active=1 and po_number_id in($po_id_all)");
	$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
	$tna_date_task_arr=array();
	foreach($tna_start_sql as $row)
	{
	if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
	{
	if($tna_fab_start=="")
	{
	$tna_fab_start=$row[csf("fab_booking_start_date")];
	}
	}
	if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
	}
	if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
	}
	if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
	}
	if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
	}
	if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
	}
	if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
	}
	if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
	}
	}
	//------------------------------ Query for TNA end-----------------------------------
	
	?>
	<fieldset id="div_size_color_matrix" style="max-width:1000; display:none">
	<legend>TNA Information</legend>
	<table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
	<tr>
	<td rowspan="2" align="center" valign="top">SL</td>
	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
	<td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
	<td colspan="2" align="center" valign="top"><b>Knitting</b></td>
	<td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
	<td colspan="2" align="center" valign="top"><b>Finishing Fabric</b></td>
	<td colspan="2" align="center" valign="top"><b>Cutting </b></td>
	<td colspan="2" align="center" valign="top"><b>Sewing </b></td>
	<td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
	</tr>
	<tr>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	</tr>
	<?
	$i=1;
	foreach($tna_date_task_arr as $order_id=>$row)
	{
	?>
	<tr>
	<td><? echo $i; ?></td>
	<td><? echo $po_num_arr[$order_id]; ?></td>
	<td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
	<td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
	<td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
	</tr>
	<?
	$i++;
	}
	?>
	</table>
	</fieldset>
	<?
	}// fabric Source End
	$dataArray = sql_select("SELECT a.id, a.booking_no,a.inserted_by, b.id as aproved_id, b.approved_by  FROM wo_booking_mst a, approval_history b WHERE a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0");
	//print_r($dataArray);die;
	$template_id=1;
	$inserted_by=$dataArray[0][csf('inserted_by')];

	$signature_arr=return_library_array( "SELECT MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' ",'MASTER_TBLE_ID','IMAGE_LOCATION');
	$appSql="SELECT APPROVED_BY from APPROVAL_MST where ENTRY_FORM=7 and MST_ID ={$dataArray[0][csf('id')]}";
	//echo $appSql;die();
	$appSqlRes=sql_select($appSql);
	foreach($appSqlRes as $row){
		$userSignatureArr[$row['APPROVED_BY']]=base_url($signature_arr[$row['APPROVED_BY']]);
	}

	if($signature_arr[$inserted_by]){ $userSignatureArr[$inserted_by]=base_url($signature_arr[$inserted_by]); }

	echo signature_table(121, $cbo_company_name, "1330px",$template_id,20,$inserted_by,$userSignatureArr);

	// echo signature_table(121, $cbo_company_name, "1330px", 1);
	echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
	?>
	</div>
	<?

		$emailBody=ob_get_contents();
//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
	
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
	
		$to=implode(',',$mailArr);
		$subject="Partial Fabric Booking Auto Mail";
		
		if($to!=""){
			require_once('../../../mailer/class.phpmailer.php');
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}



}
if($action=="show_fabric_booking_report_urmi_per_job"){

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$uom=0;
	$joball=array();
	$nameArray_per_job=sql_select( "select  a.job_no,a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  group by a.job_no,a.style_ref_no");
	foreach ($nameArray_per_job as $row_per_job){
	$joball['job_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('job_no')];
	$joball['style_ref_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('style_ref_no')];

	$uom=0;
	$job_data_arr=array();
	$nameArray_buyer=sql_select( "select a.style_ref_no, a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0 and a.job_no='".$row_per_job[csf('job_no')]."'");
	foreach ($nameArray_buyer as $result_buy){
		$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
		$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
		$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
		$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
		$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
		$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
		$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
		$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
		$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
		$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
		$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
		$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
		$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	}

	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(",",array_unique($job_data_arr['client']));
	?>
    <style>
@media print {
    .gg {page-break-after: always;}
}
</style>
	<div style="width:1330px" align="center">

	<?php
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
$path = str_replace("'", "", $path);
if ($path != "") {
	$path = $path;
} else {
	$path = "../../";
}

?>										<!--    Header Company Information         -->
	<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
	<tr>
	<td width="100">
	<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
	</td>
	<td width="1250">
	<table width="100%" cellpadding="0" cellspacing="0"  >
	<tr>
	<td align="center" style="font-size:20px;">
	<?php
echo $company_library[$cbo_company_name];
?>
	</td>
	<td rowspan="3" width="250">

	<span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($job_no,"'"); ?></b></span><br/>
	<?
	if($nameArray_approved_row[csf('approved_no')]>1)
	{
	?>
	<b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
	<br/>
	Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
	<?
	}
	?>


	</td>
	</tr>
	<tr>
	<td align="center" style="font-size:14px">
	<?
	$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
	if($txt_job_no!="")
	{
	$location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
	}
	else
	{
	$location="";
	}

	foreach ($nameArray as $result)
	{
	echo  $location_name_arr[$location];
	?>

	Email Address: <? echo $result[csf('email')];?>
	Website No: <? echo $result[csf('website')]; ?>

	<?

	}

	?>
	</td>
	</tr>
	<tr>
	<td align="center" style="font-size:20px">
	<strong><? if($report_title !=""){ echo $report_title;} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<?


	$po_data=array();
	if($db_type==0){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 and a.job_no='".$row_per_job[csf('job_no')]."'  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	if($db_type==2){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 and a.job_no='".$row_per_job[csf('job_no')]."' group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	foreach ($nameArray_job as $result_job){
		$po_data['po_id'][$result_job[csf('id')]]=$result_job[csf('id')];
		$po_data['po_number'][$result_job[csf('id')]]=$result_job[csf('po_number')];
		$po_data['leadtime'][$result_job[csf('id')]]=$result_job[csf('date_diff')];
		$po_data['po_quantity'][$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		$po_data['po_received_date'][$result_job[csf('id')]]=change_date_format($result_job[csf('po_received_date')],'dd-mm-yyyy','-');
		$ddd=strtotime($result_job[csf('pub_shipment_date')]);
		$po_data['pub_shipment_date'][$ddd]=$ddd;
		//$po_data['pub_shipment_date'][$result_job[csf('id')]]=change_date_format($result_job[csf('pub_shipment_date')],'dd-mm-yyyy','-');
		$po_data['insert_date'][$result_job[csf('id')]]=$result_job[csf('insert_date')];

		if($result_job[csf('shiping_status')]==1){
		$shiping_status= "FP";
		}
		else if($result_job[csf('shiping_status')]==2){
		$shiping_status= "PS";
		}
		else if($result_job[csf('shiping_status')]==3){
		$shiping_status= "FS";
		}
		$po_data['shiping_status'][$result_job[csf('id')]]=$shiping_status;
		$po_data['file_no'][$result_job[csf('id')]]=$result_job[csf('file_no')];
		$po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
	}
	$txt_order_no_id=implode(",",array_unique($po_data['po_id']));
	$leadtime=implode(",",array_unique($po_data['leadtime']));
	$po_quantity=array_sum($po_data['po_quantity']);
	$po_received_date=implode(",",array_unique($po_data['po_received_date']));
	$po_number=implode(",",array_unique($po_data['po_number']));
	$shipment_date=date('d-m-Y',min($po_data['pub_shipment_date']));
	$maxshipment_date=date('d-m-Y',max($po_data['pub_shipment_date']));
	//$shipment_date=implode(",",array_unique($po_data['pub_shipment_date']));
	$shiping_status=implode(",",array_unique($po_data['shiping_status']));
	$file_no=implode(",",array_unique($po_data['file_no']));
	$grouping=implode(",",array_unique($po_data['grouping']));


	$colar_excess_percent=0;
	$cuff_excess_percent=0;
	$rmg_process_breakdown=0;
	$nameArray=sql_select( "select a.buyer_id, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.rmg_process_breakdown, a.insert_date, a.update_date, a.uom, a.remarks, a.pay_mode, a.fabric_composition from wo_booking_mst a where a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0");
	foreach ($nameArray as $result)
	{
		$total_set_qnty=$result[csf('total_set_qnty')];
		$colar_excess_percent=$result[csf('colar_excess_percent')];
		$cuff_excess_percent=$result[csf('cuff_excess_percent')];
		$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
		foreach ($po_data['po_id'] as $po_id=>$po_val){
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$po_data['pub_shipment_date'][$po_id])-1).",";
			$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
			$WOPreparedAfter.=(datediff('d',$po_data['insert_date'][$po_id],$booking_date)-1).",";
		}
	?>
	<table width="100%" style="border:1px solid black;table-layout: fixed;" >
	<tr>
	<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
	</tr>
	<tr>
	<td width="200"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
	<td width="220">:&nbsp;<span style="font-size:18px"><b><? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></b></span></td>
	<td width="200"><span style="font-size:12px"><b>Dept.</b></span></td>
	<td width="220">:&nbsp;
	<?
	echo $product_depertment ;
	if($product_code !=""){
	echo " (".$product_code.")";
	}
	if($pro_sub_dep != ""){
	echo " (".$pro_sub_dep.")";
	}
	?>
	</td>
	<td width="200"><span style="font-size:12px"><b>Order Qnty</b></span></td>
	<td>:&nbsp; <?  echo $po_quantity;//." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?> </td>
	</tr>
	<tr>

	<td style="font-size:12px"><b>Garments Item</b></td>
	<td>:&nbsp;
	<?
	$gmts_item_name="";
	$gmts_item=explode(',',$gmts_item_id);
	for($g=0;$g<=count($gmts_item); $g++)
	{
	$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
	}
	echo rtrim($gmts_item_name,',');
	?>
	</td>
	<td style="font-size:12px"><b>Booking Release Date</b></td>
	<td>:&nbsp;
	<?
	$booking_date=$result[csf('update_date')];
	if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
	{
	$booking_date=$result[csf('insert_date')];
	}
	echo change_date_format($booking_date,'dd-mm-yyyy','-','');
	?>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-size:18px"><b>Style Ref.</b>   </td>
	<td style="font-size:18px">:&nbsp;<b>
	<?
	echo $style_sting;
	?>
	</b>
	</td>
	</tr>
	<tr>
	<td style="font-size:12px"><b>Style Des.</b></td>
	<td>:&nbsp;<? echo $style_description;?></td>
	<td style="font-size:12px"><b>Season</b></td>
	<td>:&nbsp;<? echo $season_matrix; ?></td>
	<td style="font-size:12px"><b>Dealing Merchant</b></td>
	<td>:&nbsp;<? echo $dealing_marchant; ?></td>
	</tr>

	<tr>
	<td style="font-size:12px"><b>Supplier Name</b>   </td>
	<td>:&nbsp;
	<?
	if($result[csf('pay_mode')]==5){
	echo $company_library[$result[csf('supplier_id')]];
	}
	else{
	echo $supplier_name_arr[$result[csf('supplier_id')]];
	}
	?>    </td>
	<td style="font-size:12px"><b>Delivery Date</b></td>
	<td>:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
	<td style="font-size:18px"><b>Booking No </b>   </td>
	<td style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?> <? //echo "(".$unit_of_measurement[$result[csf('uom')]].")"; $uom=$result[csf('uom')];?></td>
	</tr>
	<tr>
	<td  style="font-size:12px"><b>Attention</b></td>
	<td  >:&nbsp;<? echo $result[csf('attention')]; ?></td>
	<td style="font-size:12px"><b>Lead Time </b>   </td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
	<?
	echo $leadtime;
	?>
	</td>
	<td  style="font-size:12px"><b>Po Received Date</b></td>
	<td  >:&nbsp;<? echo $po_received_date; ?></td>
	</tr>
	<tr>
	<td style="font-size:18px"><b>Order No</b></td>
	<td style="font-size:18px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="3">:&nbsp;<? echo $po_number; ?></td>
	<td  style="font-size:12px"><b>Repeat No</b></td>
	<td  >:&nbsp;<? echo $order_repeat_no; ?></td>
	</tr>
	<tr>
	<td style="font-size:12px"><b>Shipment Date</b></td>
	<td colspan="3" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> : First:&nbsp;<? echo rtrim($shipment_date,", "); //echo $max_pub_shipment_date; ?>, Last: <? echo $maxshipment_date; ?></td>
	<td  style="font-size:12px"><b>Quality Label</b></td>
	<td  >:&nbsp;<? echo $qlty_label; ?></td>
	</tr>
	</tr>
	<tr>
	<td style="font-size:12px"><b>WO Prepared After</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
	<?
	$WOPreparedAfter=implode(",",array_unique(explode(",",chop($WOPreparedAfter,","))));
	echo $WOPreparedAfter.' Days' ;
	?></td>

	<td style="font-size:12px"><b>Ship.days in Hand</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
	<?
	$daysInHand=implode(",",array_unique(explode(",",chop($daysInHand,","))));
	echo $daysInHand.' Days' ;
	?></td>

	<td style="font-size:12px"><b>Ex-factory status</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
	<?
	echo $shiping_status;
	?></td>

	</tr>
	<tr>
	<td style="font-size:18px"><b>Internal Ref No</b></td>
	<td style="font-size:18px"> :&nbsp;<b><? echo $grouping; ?></b></td>
	<td style="font-size:18px"><b>File no</b></td>
	<td style="font-size:18px"> :&nbsp;<b><? echo  $file_no;?></b></td>
	<td style="font-size:18px"><b>Currency</b></td>
	<td style="font-size:18px"> :&nbsp;<b><? echo  $currency[$result[csf("currency_id")]];?></b></td>
	</tr>
	<tr>
	<td style="font-size:18px"><b>Rmarks</b></td>
	<td style="font-size:18px" colspan="5"> :<? echo $result[csf('remarks')]?></td>
	</tr>
	<tr>
	<td style="font-size:18px"><b>Fabric Composition</b></td>
	<td style="font-size:18px" colspan="5"> :<? echo $result[csf('fabric_composition')]?></td>
	</tr>

	</table>
	<?
	}

	if($cbo_fabric_source==1 || $cbo_fabric_source==2){
	$nameArray_size=sql_select( "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and   job_no_mst='".$row_per_job[csf('job_no')]."' and 	is_deleted=0 and status_active=1 group by size_number_id order by size_order");
	?>
	<table width="100%" >
	<tr>
	<td width="800">
	<?php ob_start();?>
	<div id="div_size_color_matrix" style="float:left; max-width:1000;">
	<fieldset id="div_size_color_matrix" style="max-width:1000;">
	<legend>Size and Color Breakdown</legend>
	<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
	<tr>
	<td style="border:1px solid black"><strong>Color/Size</strong></td>
	<?
	foreach($nameArray_size  as $result_size)
	{	     ?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?	}    ?>
	<td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
	<td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
	<td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
	</tr>
	<?
	$color_size_order_qnty_array=array();
	$color_size_qnty_array=array();
	$size_tatal=array();
	$size_tatal_order=array();
	for($c=0;$c<count($gmts_item); $c++)
	{
	$item_size_tatal=array();
	$item_size_tatal_order=array();
	$item_grand_total=0;
	$item_grand_total_order=0;
	$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and job_no_mst='".$row_per_job[csf('job_no')]."' and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
	?>
	<tr>
	<td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>

	</tr>
	<?
	foreach($nameArray_color as $result_color)
	{
	?>
	<tr>
	<td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
	<?
	$color_total=0;
	$color_total_order=0;

	foreach($nameArray_size  as $result_size)
	{
	$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and job_no_mst='".$row_per_job[csf('job_no')]."' and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");
	foreach($nameArray_color_size_qnty as $result_color_size_qnty)
	{
	?>
	<td style="border:1px solid black; text-align:right">
	<?
	if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
	{
	echo number_format($result_color_size_qnty[csf('order_quantity')],0);
	$color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
	$color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
	$item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
	$grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
	$grand_total_order +=$result_color_size_qnty[csf('order_quantity')];


	$color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
	{
	$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
	}
	else
	{
	$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	}
	if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
	{
	$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
	}
	else
	{
	$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	}
	}
	else echo "0";
	?>
	</td>

	<?
	}
	}
	?>
	<td style="border:1px solid black; text-align:right"><?  echo number_format(round($color_total_order),0); ?></td>

	<td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?>
	</td>
	<td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
	</tr>
	<?
	}
	?>

	<td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
	<?
	foreach($nameArray_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
	<td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
	</tr>
	<tr>
	<tr>
	<td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
	<?
	foreach($nameArray_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
	<td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
	</tr>
	</table>
	</fieldset>
	</div>
	</td>
	<td width="200" valign="top" align="left">
	<div id="div_size_color_matrix" style="float:left;">
	<?
	$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
	?>
	<fieldset>
	<legend>RMG Process Loss % </legend>
	<table width="180" class="rpt_table" border="1" rules="all">
	<?
	if(number_format($rmg_process_breakdown_arr[8],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[8],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[2],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Chest Printing <!-- Printing % breack Down 2-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[2],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[10],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Neck/Sleeve Printing <!-- New breack Down 10-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[10],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[1],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Embroidery   <!-- Embroidery  % breack Down 1-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[1],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[4],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Sewing /Input<!-- Sewing % breack Down 4-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[4],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[3],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Garments Wash <!-- Washing %breack Down 3-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[3],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[15],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Gmts Finishing <!-- Washing %breack Down 3-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[15],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[11],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Others <!-- New breack Down 11-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[11],2);
	?>
	</td>
	</tr>
	<?
	}
	$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
	if($gmts_pro_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Sub Total <!-- New breack Down 11-->
	</td>
	<td align="right">
	<?

	echo number_format($gmts_pro_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	?>
	</table>
	</fieldset>


	<fieldset>
	<legend>Fabric Process Loss % </legend>
	<table width="180" class="rpt_table" border="1" rules="all">
	<?
	if(number_format($rmg_process_breakdown_arr[6],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Knitting  <!--  Knitting % breack Down 6-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[6],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[12],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Yarn Dyeing  <!--  New breack Down 12-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[12],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[5],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Dyeing & Finishing  <!-- Finishing % breack Down 5-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[5],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[13],2)>0)
	{
	?>
	<tr>
	<td width="130">
	All Over Print <!-- new  breack Down 13-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[13],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[14],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Lay Wash (Fabric) <!-- new  breack Down 14-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[14],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[7],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Dying   <!-- breack Down 7-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[7],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[0],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Cutting (Fabric) <!-- Cutting % breack Down 0-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[0],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[9],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Others  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[9],2);
	?>
	</td>
	</tr>
	<?
	}
	$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
	if(fab_proce_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Sub Total  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?

	echo number_format($fab_proce_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Grand Total  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?
	echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	?>
	</table>
	</fieldset>
	</div>
	</td>
	<td width="330" valign="top" align="left">
	<?
	$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in($job_no_in) and file_type=1");
	?>
	<div id="div_size_color_matrix" style="float:left;">
	<fieldset>
	<legend>Image</legend>
	<table width="310">
	<tr>
	<?
	$img_counter = 0;
	foreach($nameArray_imge as $result_imge)
	{
	if($path=="")
	{
	$path='../../';
	}
	?>
	<td>
	<img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
	</td>
	<?

	$img_counter++;
	}
	?>
	</tr>
	</table>
	</fieldset>
	</div>
	</td>
	</tr>
	</table>
	<?
	}// if($cbo_fabric_source==1) end

	?>
	<br/>
	<!--  Here will be the main portion  -->
	<?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($job_no_in)");
	if($costing_per_id==1)
	{
	$costing_per="1 Dzn";
	$costing_per_qnty=12;

	}
	if($costing_per_id==2)
	{
	$costing_per="1 Pcs";
	$costing_per_qnty=1;

	}
	if($costing_per_id==3)
	{
	$costing_per="2 Dzn";
	$costing_per_qnty=24;

	}
	if($costing_per_id==4)
	{
	$costing_per="3 Dzn";
	$costing_per_qnty=36;

	}
	if($costing_per_id==5)
	{
	$costing_per="4 Dzn";
	$costing_per_qnty=48;
	}
	$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no in($job_no_in)");

	$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");
	foreach($uom_arr as $uom_id=>$uom_val){
	if($cbo_fabric_source==1){
	$nameArray_fabric_description= sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , b.dia_width,d.pre_cost_remarks, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and
	b.color_number_id=d.gmts_color_id and
	b.dia_width=d.dia_width and
	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
	a.uom=$uom_id and
	d.status_active=1 and
	d.is_deleted=0 and
	b.cons>0
	group by a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,d.pre_cost_remarks order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");
	if(count($nameArray_fabric_description)>0){
	?>

	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	<caption>Fabric Details in <? echo $uom_val ?></caption>
	<tr align="center">
	<th colspan="3" align="left">Item Name</th>
	<?

	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>". $garments_item[$result_fabric_description[csf('item_number_id')]]."</td>";
	}
	?>
	<td  rowspan="11" width="50"><p>Total Finish Fabric (Kg)</p></td>

	<td  rowspan="11" width="50"><p>Avg Rate <? echo "(".$unit_of_measurement[$uom].")";?></p></td>
	<td  rowspan="11" width="50"><p>Amount </p></td>

	</tr>
	<tr align="center">
	<th colspan="3" align="left">Body Part</th>
	<?

	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>".$body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Color Type</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Fabric Construction</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
	}
	?>


	</tr>
	<tr align="center"><th   colspan="3" align="left">Yarn Composition</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
	else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th  colspan="3" align="left">GSM</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('cons')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2)."</td>";
	}
	?>
	</tr>
    <tr align="center"><th   colspan="3" align="left">Remarks</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('pre_cost_remarks')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('pre_cost_remarks')]."</td>";
		}
		?>

       </tr>
	<tr>
	<th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
	</tr>
	<tr>
	<th  width="120" align="left">Fabric Color</th>
	<th  width="120" align="left">Body Color</th>
	<th  width="120" align="left">Lab Dip No</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
	}
	?>
	</tr>
	<?
	$gmt_color_library=array();
	$gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
	WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.uom=$uom_id  and a.fabric_source =$cbo_fabric_source and
	a.job_no in ($job_no_in)");
	foreach( $gmt_color_data as $gmt_color_row){
	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
	}
	$grand_total_fin_fab_qnty=0;
	$grand_total_amount=0;
	$color_wise_wo_sql=sql_select("select b.fabric_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_booking_dtls b
	WHERE
	a.id=b.pre_cost_fabric_cost_dtls_id and
	a.uom=$uom_id and
	b.booking_no =$txt_booking_no and
	b.job_no='".$row_per_job[csf('job_no')]."' and
	b.status_active=1 and
	b.is_deleted=0
	group by b.fabric_color_id");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	?>
	<tr>
	<td  width="120" align="left">
	<?
	echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
	?>
	</td>
	<td>
	<?
	echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
	?>
	</td>
	<td  width="120" align="left">
	<?
	$lapdip_no="";
	$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
	if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
	?>
	</td>
	<?
	$total_fin_fab_qnty=0;
	$total_amount=0;

	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if($db_type==0)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.fabric_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	}
	if($db_type==2)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
	d.status_active=1 and
	d.is_deleted=0
	");
	}
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'>
	<?
	if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
	$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	if($color_wise_wo_result_qnty[csf('rate')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('rate')],5);
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	$amount=def_number_format($color_wise_wo_result_qnty[csf('amount')],2,'',0);
	if($amount!="")
	{
	echo $amount;
	$total_amount+=$amount;
	}
	?>
	</td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
	<td align="right"><? echo def_number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
	<td align="right">
	<?
	echo def_number_format($total_amount,2);

	?>
	</td>
	</tr>
	<?
	}
	?>
	<tr style=" font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Total</strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'><?  echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($grand_total_fin_fab_qnty,2);?></td>
	<td align="right"><? echo def_number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
	<td align="right">
	<?
	echo def_number_format($grand_total_amount,2);
	?>
	</td>
	</tr>
	<!--<tr style="font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.grey_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty*/
	?>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<?
	}
	?>
	<td align="right">
	<?
	//$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
	//echo number_format($consumption_per_unit_fab,2);
	?>
	</td>
	<td align="right">
	<?
	//$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
	//echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
	?>
	</td>
	<td align="right" title="Only Allow Round Figer">
	<?
	//echo number_format($consumption_per_unit_amuont,2);
	?>
	</td>
	</tr> -->
	</table>
	<br/>
	<?
	}
	}
	}
	//===========================

	foreach($uom_arr as $uom_id=>$uom_val){
	if($cbo_fabric_source==2){
	$nameArray_fabric_description= sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , b.dia_width,d.pre_cost_remarks, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and
	b.color_number_id=d.gmts_color_id and
	b.dia_width=d.dia_width and
	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
	a.uom=$uom_id and
	d.status_active=1 and
	d.is_deleted=0  and
	b.cons>0
	group by a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,d.pre_cost_remarks order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");
	if(count($nameArray_fabric_description)>0){
	?>
	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	<caption>Fabric Details in <? echo $uom_val;?></caption>
	<tr align="center">
	<th colspan="3" align="left">Item Name</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>". $garments_item[$result_fabric_description[csf('item_number_id')]]."</td>";
	}
	?>
	<td  rowspan="11" width="50"><p>Total Finish Fabric</p></td>

	<td  rowspan="11" width="50"><p>Avg Rate</p></td>
	<td  rowspan="11" width="50"><p>Amount </p></td>
	</tr>
	<tr align="center">
	<th colspan="3" align="left">Body Part</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>".$body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Color Type</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Fabric Construction</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)

	{
	if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th   colspan="3" align="left">Yarn Composition</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
	else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th  colspan="3" align="left">GSM</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('cons')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2)."</td>";
	}
	?>

	</tr>
    <tr align="center"><th   colspan="3" align="left">Remarks</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('pre_cost_remarks')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('pre_cost_remarks')]."</td>";
		}
		?>

       </tr>
	<tr>
	<th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
	</tr>
	<tr>
	<th  width="120" align="left">Fabric Color</th>
	<th  width="120" align="left">Body Color</th>
	<th  width="120" align="left">Lab Dip No</th>
	<?
	if($cbo_fabric_source==2)
	{
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
	}
	}
	else
	{
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th>";
	}
	}

	?>
	</tr>
	<?
	$gmt_color_library=array();

	$gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
	WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.uom=$uom_id  and a.fabric_source =$cbo_fabric_source and
	a.job_no in ($job_no_in)");
	foreach( $gmt_color_data as $gmt_color_row){
	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
	}
	$grand_total_fin_fab_qnty=0;
	$grand_total_amount=0;
	$color_wise_wo_sql=sql_select("select b.fabric_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_booking_dtls b
	WHERE
	a.id=b.pre_cost_fabric_cost_dtls_id and
	a.uom=$uom_id and
	b.booking_no =$txt_booking_no
	and b.job_no='".$row_per_job[csf('job_no')]."' and
	b.status_active=1 and
	b.is_deleted=0
	group by b.fabric_color_id");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	?>
	<tr>
	<td  width="120" align="left">
	<?
	echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
	?>
	</td>
	<td>
	<?
	echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
	?>
	</td>
	<td  width="120" align="left">
	<?
	$lapdip_no="";
	$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
	if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
	?>
	</td>
	<?
	$total_fin_fab_qnty=0;
	$total_amount=0;

	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if($db_type==0)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.fabric_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	}
	if($db_type==2)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
	d.status_active=1 and
	d.is_deleted=0
	");
	}
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'>
	<?
	if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
	$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	if($color_wise_wo_result_qnty[csf('rate')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('rate')],5);
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	$amount=def_number_format($color_wise_wo_result_qnty[csf('amount')],2,'',0);
	if($amount!="")
	{
	echo $amount;
	$total_amount+=$amount;
	}
	?>
	</td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
	<td align="right"><? echo def_number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
	<td align="right">
	<?
	echo def_number_format($total_amount,2);

	?>
	</td>
	</tr>
	<?
	}
	?>
	<tr style=" font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Total</strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'><?  echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
	<td width='50' align='right' ></td>
	<td width='50' align='right' ></td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($grand_total_fin_fab_qnty,2);?></td>
	<td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
	<td align="right">
	<?
	echo def_number_format($grand_total_amount,2);
	?>
	</td>
	</tr>
	<!--<tr style="font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.grey_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty*/

	?>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<?
	}
	?>
	<td align="right">
	<?
	//$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
	//echo number_format($consumption_per_unit_fab,2);
	?>
	</td>
	<td align="right">
	<?
	//$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
	//echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
	?>
	</td>
	<td align="right" title="Only Allow Round Figer">
	<?
	//echo number_format($consumption_per_unit_amuont,2);
	?>
	</td>
	</tr> -->
	</table>
	<br/>
	<?
	}
	}
	}
	//===========================
	?>
    <?
	$sql_data=sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark, sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.adjust_qty)as adjust_qty,sum(b.fin_fab_qnty) as fin_fab_qnty,avg(b.rate) as rate,sum(b.grey_fab_qnty*b.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no='".$row_per_job[csf('job_no')]."' and b.adjust_qty>0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark order by a.id,a.body_part_id");

		?>
		<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

        <tr>
        <td colspan="7">
        <strong>Fabric Stock Adjustment Details</strong>
        </td>

        </tr>

        <tr>
        <td>
        Fabrication
        </td>
        <td>
        Process
        </td>
        <td>
        Fabric Color
        </td>
        <td>
        Required
        </td>
        <td>
        Stock Used
        </td>
        <td>
        Booking Qty
        </td><td>
        Uom
        </td>
         <td>
        Remarks
        </td>
        </tr>
        <?
		foreach($sql_data as $row){
		?>
          <tr>
        <td>
        <? echo $body_part[$row[csf('body_part_id')]].",".$color_type[$row[csf('color_type_id')]].",".$row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]].",".$row[csf('dia_width')]  ?>
        </td>
        <td>
        <? echo $row[csf('pre_cost_remarks')];  ?>
        </td>
        <td>
        <? echo $color_library[$row[csf('fabric_color_id')]];  ?>
        </td>
        <td align="right">
        <? echo number_format($row[csf('grey_fab_qnty')],4);  ?>
        </td>
        <td align="right">
         <? echo number_format($row[csf('adjust_qty')],4) ; ?>
        </td>
        <td align="right">
       <? echo number_format($row[csf('fin_fab_qnty')],4);  ?>
        </td>
         <td>
        <? echo $unit_of_measurement[$row[csf('uom')]];  ?>
        </td>
         <td>
         <? echo $row[csf('remark')];  ?>
        </td>
        </tr>
        <?
		}
		?>
        </table>


	<?
	if($cbo_fabric_source==1 || $cbo_fabric_source==2){
	?>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
	<tr>
	<?
	$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.status_active =1 and d.is_deleted=0 and d.job_no='".$row_per_job[csf('job_no')]."'  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
	</tr>
	<tr>
	<td>Collar Size</td>

	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	<?
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.status_active =1 and d.is_deleted=0 and d.job_no='".$row_per_job[csf('job_no')]."' and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and c.color_number_id=d.gmts_color_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	$color_total_collar=0;
	$color_total_collar_order_qnty=0;
	$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
	$constrast_color_arr=array();
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
	for($i=0;$i<count($constrast_color);$i++)
	{
	$constrast_color2=explode('_',$constrast_color[$i]);
	$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
	}
	}
	?>
	<tr>
	<td>
	<?
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
	$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
	}
	else
	{
	echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
	$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
	}
	?>
	</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black">
	<?
	$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and job_no_mst='".$row_per_job[csf('job_no')]."' and  status_active=1 and is_deleted =0");
	list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
	$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
	$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
	echo number_format($plan_cut+$colar_excess_per,0);
	$color_total_collar+=$plan_cut+$colar_excess_per;
	$color_total_collar_order_qnty+=$plan_cut;
	$grand_total_collar+=$plan_cut+$colar_excess_per;
	$grand_total_collar_order_qnty+=$plan_cut;
	?>
	</td>
	<?
	}
	?>
	<td align="center"><? echo number_format($color_total_collar,0); ?></td>
	<td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td>Size Total</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
	<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
	</tr>
	</table>
	</td>
	<td width="2%">
	</td>
	<?
	}
	?>
	<?
	$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.job_no='".$row_per_job[csf('job_no')]."'  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");

	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>

	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
	</tr>
	<tr>
	<td>Cuff Size</td>

	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	<?
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.job_no='".$row_per_job[csf('job_no')]."' and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	$color_total_cuff=0;
	$color_total_cuff_order_qnty=0;
	$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
	$constrast_color_arr=array();
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
	for($i=0;$i<count($constrast_color);$i++)
	{
	$constrast_color2=explode('_',$constrast_color[$i]);
	$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
	}
	}
	?>
	<tr>
	<td>
	<?
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
	$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
	}
	else
	{
	echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
	$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
	}
	?>
	</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black">
	<?
	$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and job_no_mst='".$row_per_job[csf('job_no')]."' and  status_active=1 and is_deleted =0");
	list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
	$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
	$cuff_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
	echo number_format($plan_cut*2+$cuff_excess_per,0);
	$color_total_cuff+=$plan_cut*2+$cuff_excess_per;
	$color_total_cuff_order_qnty+=$plan_cut*2;
	$grand_total_cuff+=$plan_cut*2+$cuff_excess_per;
	$grand_total_cuff_order_qnty+=$plan_cut*2;
	?>
	</td>
	<?
	}
	?>
	<td align="center"><? echo number_format($color_total_cuff,0); ?></td>
	<td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td>Size Total</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
	<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
	</tr>
	</table>
	</td>
	<?
	}
	?>
	</tr>
	</table>
	<br/>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
	<tr>
	<?
	$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.job_no='".$row_per_job[csf('job_no')]."'  and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar Tipping -  Colour Size Brakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
	</tr>
	<tr>
	<td>Collar Size</td>
	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	<?
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.job_no='".$row_per_job[csf('job_no')]."' and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	$color_total_collar_tipping=0;
	$color_total_collar_tipping_order_qnty=0;
	$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
	$constrast_color_arr=array();
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
	for($i=0;$i<count($constrast_color);$i++)
	{
	$constrast_color2=explode('_',$constrast_color[$i]);
	$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
	}
	}
	?>
	<tr>
	<td>
	<?
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
	$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
	}
	else
	{
	echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
	$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
	}
	?>
	</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black">
	<?
	$color_tipping_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]." and job_no_mst='".$row_per_job[csf('job_no')]."'   and  status_active=1 and is_deleted =0");

	list($plan_cut_qnty)=$color_tipping_wise_wo_sql_qnty;
	$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
	$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
	echo number_format($plan_cut+$colar_excess_per,0);
	$color_total_collar_tipping+=$plan_cut+$colar_excess_per;
	$color_total_collar_tipping_order_qnty+=$plan_cut;
	$grand_total_collar_tipping+=$plan_cut+$colar_excess_per;
	$grand_total_collar_tipping_order_qnty+=$plan_cut;
	?>
	</td>
	<?
	}
	?>
	<td align="center"><? echo number_format($color_total_collar_tipping,0); ?></td>
	<td align="center"><? echo number_format((($color_total_collar_tipping-$color_total_collar_tipping_order_qnty)/$color_total_collar_tipping_order_qnty)*100,2); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td>Size Total</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar_tipping,0); ?></td>
	<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar_tipping-$grand_total_collar_tipping_order_qnty)/$grand_total_collar_tipping_order_qnty)*100,2); ?></td>
	</tr>
	</table>
	</td>
	<td width="2%">
	</td>
	<?
	}
	?>
	<?
	$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.job_no='".$row_per_job[csf('job_no')]."'  and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff Tipping -  Colour Size Brakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
	</tr>
	<tr>
	<td>Cuff Size</td>
	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	<?
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.job_no='".$row_per_job[csf('job_no')]."' and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	$color_total_cuff_tipping=0;
	$color_total_cuff_tipping_order_qnty=0;
	$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
	$constrast_color_arr=array();
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
	for($i=0;$i<count($constrast_color);$i++)
	{
	$constrast_color2=explode('_',$constrast_color[$i]);
	$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
	}
	}
	?>
	<tr>
	<td>
	<?
	if($color_wise_wo_result[csf("color_size_sensitive")]==3)
	{
	echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
	$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
	}
	else
	{
	echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
	$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
	}
	?>
	</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black">
	<?
	$cuff_tipping_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]." and job_no_mst='".$row_per_job[csf('job_no')]."'   and  status_active=1 and is_deleted =0");
	list($plan_cut_qnty)=$cuff_tipping_wise_wo_sql_qnty;
	$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
	$cuff_tipping_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
	echo number_format($plan_cut*2+$cuff_tipping_excess_per,0);
	$color_total_cuff_tipping+=$plan_cut*2+$cuff_tipping_excess_per;
	$color_total_cuff_tipping_order_qnty+=$plan_cut*2;
	$grand_total_cuff_tipping+=$plan_cut*2+$cuff_excess_per;
	$grand_total_cuff_tipping_order_qnty+=$plan_cut*2;
	?>
	</td>
	<?
	}
	?>
	<td align="center"><? echo number_format($color_total_cuff_tipping,0); ?></td>
	<td align="center" title="<? echo $color_total_cuff_tipping."**".$color_total_cuff_tipping_order_qnty; ?>"><? echo number_format((($color_total_cuff_tipping-$color_total_cuff_tipping_order_qnty)/$color_total_cuff_tipping_order_qnty)*100,2); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td>Size Total</td>
	<?
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff_tipping,0); ?></td>
	<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff_tipping-$grand_total_cuff_tipping_order_qnty)/$grand_total_cuff_tipping_order_qnty)*100,2); ?></td>
	</tr>
	</table>
	</td>
	<?
	}
	?>
	</tr>
	</table>
	<br/>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
	</tr>
	</thead>
	<tbody>
	<?
	$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");
	if ( count($data_array)>0)
	{
	$i=0;
	foreach( $data_array as $row )
	{
	$i++;
	?>
	<tr id="settr_1" valign="top">
	<td style="vertical-align:top">
	<? echo $i;?>
	</td>
	<td>
	<strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong>
	</td>
	</tr>
	<?
	}
	}
	?>
	</tbody>
	</table>
	</td>
	<td width="2%">
	</td>
	<td width="49%" valign="top">
	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr align="center">
	<td><b>Approved Instructions</b></td>
	</tr>
	<tr>
	<td>
	<?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
	</td>
	</tr>
	</table>
	<br />
	</td>
	</tr>
	</table>
	<br>
	<?
	//------------------------------ Query for TNA start-----------------------------------
	$po_id_all=str_replace("'","",$txt_order_no_id);
	$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
	$tna_start_sql=sql_select( "select id,po_number_id,
	(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
	(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
	(case when task_number=60 then task_start_date else null end) as knitting_start_date,
	(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
	(case when task_number=61 then task_start_date else null end) as dying_start_date,
	(case when task_number=61 then task_finish_date else null end) as dying_end_date,
	(case when task_number=73 then task_start_date else null end) as finishing_start_date,
	(case when task_number=73 then task_finish_date else null end) as finishing_end_date,
	(case when task_number=84 then task_start_date else null end) as cutting_start_date,
	(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
	(case when task_number=86 then task_start_date else null end) as sewing_start_date,
	(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
	(case when task_number=110 then task_start_date else null end) as exfact_start_date,
	(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
	(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
	(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
	from tna_process_mst
	where status_active=1 and po_number_id in($po_id_all) and job_no_mst='".$row_per_job[csf('job_no')]."'");
	$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
	$tna_date_task_arr=array();
	foreach($tna_start_sql as $row)
	{
	if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
	{
	if($tna_fab_start=="")
	{
	$tna_fab_start=$row[csf("fab_booking_start_date")];
	}
	}
	if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
	}
	if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
	}
	if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
	}
	if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
	}
	if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
	}
	if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
	}
	if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
	{
	$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
	$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
	}
	}
	//------------------------------ Query for TNA end-----------------------------------
	?>
	<fieldset id="div_size_color_matrix" style="max-width:1000; display:none">
	<legend>TNA Information</legend>
	<table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
	<tr>
	<td rowspan="2" align="center" valign="top">SL</td>
	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
	<td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
	<td colspan="2" align="center" valign="top"><b>Knitting</b></td>
	<td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
	<td colspan="2" align="center" valign="top"><b>Finishing Fabric</b></td>
	<td colspan="2" align="center" valign="top"><b>Cutting </b></td>
	<td colspan="2" align="center" valign="top"><b>Sewing </b></td>
	<td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
	</tr>
	<tr>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	<td width="85" align="center" valign="top"><b>Start Date</b></td>
	<td width="85" align="center" valign="top"><b>End Date</b></td>
	</tr>
	<?
	$i=1;
	foreach($tna_date_task_arr as $order_id=>$row)
	{
	?>
	<tr>
	<td><? echo $i; ?></td>
	<td><? echo $po_num_arr[$order_id]; ?></td>
	<td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
	<td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
	<td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
	<td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
	<td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
	</tr>
	<?
	$i++;
	}
	?>
	</table>
	</fieldset>
	<?
	}// fabric Source End
	?>
	<?
	//echo signature_table(1, $cbo_company_name, "1330px");
	echo signature_table(121, $cbo_company_name, "1330px", 1);
	?>
    <p class="gg"></p>

    <?
	}
	$job_no_all= implode(",",array_unique($joball['job_no']));
	$style_sting_all=implode(",",array_unique($joball['style_ref_no']));

	echo "****".custom_file_name($txt_booking_no,$style_sting_all,$job_no_all);
	?>
	</div>
	<?

$emailBody=ob_get_contents();
//ob_clean();
	if($is_mail_send==1){
	$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
	$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
	if($req_approved && $is_approved==1){
		$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
	}
	elseif($req_approved && $is_approved==0){
		$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
	}
	
	
	$mailArr=array();
	if($mail_id!=''){$mailArr[]=$mail_id;}
	if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
	
	
	$to=implode(',',$mailArr);
	$subject="Partial Fabric Booking Auto Mail";
	
	if($to!=""){
		require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
		require_once('../../../auto_mail/setting/mail_setting.php');
		$header=mailHeader();
		sendMailMailer( $to, $subject, $emailBody );
	}
	}


}

if ($action=="unapp_request_popup")
{
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$data_all=explode('_',$data);
	$booking_no=$data_all[0];
	//$unapp_request=$data_all[1];

	$wo_id=return_field_value("id", "wo_booking_mst", "booking_no='$booking_no' and status_active=1 and is_deleted=0");

	if($unapp_request=="")
	{
		$sql_request="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=7 and user_id='$user_id' and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";

			$dd="";
		$nameArray_request=sql_select($sql_request);
		foreach($nameArray_request as $row)
		{
			//$unapp_request=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf('id')]."' and status_active=1 and is_deleted=0");
				$dd=sql_select("select approval_cause from fabric_booking_approval_cause where id=".$row[csf('id')]." and status_active=1 and is_deleted=0");
				//$unapp_request=htmlentities($dd[0][csf('approval_cause')]);
		}
	}

	//echo $booking_no.'_'.$unapp_request;


	?>
    <script>

		$( document ).ready(function() {
			//document.getElementById("unappv_request").value='<? //echo str_replace("<br />","\n",$dd[0][csf('approval_cause')]); ?>';
		});

		var permission='<? echo $permission; ?>';

		function fnc_appv_entry(operation)
		{
			var unappv_request = $('#unappv_request').val();

			if (form_validation('unappv_request','Un Approval Request')==false)
			{
				if (unappv_request=='')
				{
					alert("Please write request.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_unappv_request&operation="+operation+get_submitted_data_string('unappv_request*wo_id*page_id*user_id',"../../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","partial_fabric_booking_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				// alert(http.responseText);//return;
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();

				//generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
				var returnValue=return_global_ajax_value(reponse[2], 'weven_gmts_partial_fb_unapproved_mail', '', '../../../auto_mail/pre_cost_unapproved_mail_notification');

			}
		}

		function fnc_close()
		{
			unappv_request= $("#unappv_request").val();

			document.getElementById('hidden_appv_cause').value=unappv_request;

			parent.emailwindow.hide();
		}

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character" value=""><? echo str_replace("<br />","\n",$dd[0][csf('approval_cause')]); ?></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}



if ($action=="save_update_delete_unappv_request")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	//echo "shajjad_".$unappv_request.'_'.$wo_id.'_'.$page_id.'_'.$user_id; die;

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$approved_no=return_field_value("MAX(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id");

		$unapproved_request=return_field_value("id","fabric_booking_approval_cause","page_id=$page_id and entry_form=7 and user_id=$user_id and booking_id=$wo_id and approval_type=2 and approval_no=$approved_no");



		if($unapproved_request=="")
		{
			$textToStore = nl2br(htmlentities(str_replace("'","",$unappv_request), ENT_QUOTES, 'UTF-8'));

			$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

			$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,2,'".$approved_no."','".$textToStore."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);

			//echo "10**reza".$data_array; die;

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			if($db_type==1 )
			{

				echo "0**".$rID."**".$wo_id;
			}
			disconnect($con);
			die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*7*".$user_id."*".$wo_id."*2*'".$approved_no."'*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

			 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$unapproved_request."",0);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}

			if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}



			if($db_type==1 )
			{
				echo "1**".$rID."**".str_replace("'","",$wo_id);
			}
			disconnect($con);
			die;
		}

	}

	if ($operation==1)  // Update Here
	{

	}

}
if($action == "fabric_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	
	
	$company_info=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");

	$po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
	$path=str_replace("'","",$path);
	if($path!="") $path=$path;elseif($image_cond==1) $path="../../../"; else $path="../../";
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_comments_row)=$nameArray_approved_comments;
	$uom=0;
	$job_data_arr=array();
	foreach ($po_booking_info as $result_buy){
	$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
	$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
	$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
	$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
	$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
	$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
	$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
	$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
	$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
	$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
	$job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
	$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
	$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
	$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
	$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	}
	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(",",array_unique($job_data_arr['client']));
	

	$po_data=array();
	if($db_type==0){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	if($db_type==2){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	foreach ($nameArray_job as $result_job){
		$po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
	}
	$grouping=implode(",",array_unique(array_filter($po_data['grouping'])));

	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.pay_mode, a.currency_id from wo_booking_mst a  where   a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 ");

    ?>
	<?php ob_start();?>
	<table style="border:1px solid black;table-layout: fixed; " width="100%">
		<tr>
			<td><img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' /></td>
			<td style="text-align: center;">
				<span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
				<?
                            $nameArray2=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray2 as $result)
                            {
                            ?>
                                <? echo $result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$result[csf('city')].' '.$result[csf('zip_code')].' '.$result[csf('province')].' '.$country_arr[$result[csf('country_id')]]; ?><br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website: <? echo $result[csf('website')].'<br>';
                            }
                            ?>
				<span style="font-size:16px; font-weight:bold">Fabric Purchase Order</span>
			</td>
			<td style="text-align: right; padding-right: 30px"><span style="font-size:16px; font-weight:bold;">Booking No: <? echo $nameArray[0][csf('booking_no')];?><? echo "(".$fabric_source[$nameArray[0][csf('fabric_source')]].")"?></span></td>
		</tr>
	</table>
	<? foreach ($nameArray as $result) {
		$currency_id=$result[csf('currency_id')];
		$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
	 ?>
    <table style="margin-top: 5px" class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
        <tr>
        	<th width="175" style="text-align: left">Supplier Name </th>
            <td width="175"> <?
				if($result[csf('pay_mode')]==5){
				echo $company_library[$result[csf('supplier_id')]];
				}
				else{
				echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
			?>
			</td>
			<th width="175" style="text-align: left">Dealing Merchant </th>
            <td width="175" > <? echo $dealing_marchant; ?></td>
            <th width="175" style="text-align: left">Buyer/Agent Name</th>
            <td width="175"> <? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>

        </tr>
        <tr>
        	<th style="text-align: left">Attention </th>
            <td> <? echo $result[csf('attention')]; ?></td>
            <th style="text-align: left">Merchant E-Mail id </th>
            <td> <? echo $dealing_marchant_email ?></td>
            <th style="text-align: left">Garments Item </th>
            <td> <?
	            $gmts_item_name="";
				$gmts_item=explode(',',$gmts_item_id);
				for($g=0;$g<=count($gmts_item); $g++)
				{
				$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
			?>
			</td>
        </tr>
        <tr>
            <th width="175" style="text-align: left">Booking Date </th>
            <td width="175"> <? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
            <th style="text-align: left">Fabric ETD </th>
            <td> <? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
            <th style="text-align: left">Internal Ref No. </th>
            <td> <?echo $grouping ?></td>
        </tr>
        <tr>
        	<?
        	$delivery_address=explode("\n",$result[csf('delivery_address')]);
        	?>
        	<th style="text-align: left">Delivery Address </th>
            <td> <? if(count($delivery_address)>0){
            	foreach ($delivery_address as $key => $value) { ?>
            	<? echo $value ?><br>
            	<? }
            } ?></td>
            <th style="text-align: left">Pay Mode</th>
            <td> <? echo $pay_mode[$result[csf('pay_mode')]] ?></td>
            <th style="text-align: left">Currency</th>
            <td> <? echo $currency[$result[csf('currency_id')]] ?></td>
        </tr>
        <tr>
        	<th style="text-align: left">Remarks </th>
            <td colspan="5"> <? echo $result[csf('remarks')]?></td>
        </tr>
    </table>
    <? } ?>
    <?
    $nameArray_fabric_description = "select a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia, a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no, c.style_description,  c.job_no_prefix_num, d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color, b.po_number,d.remark FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no and d.job_no=c.job_no and d.booking_no = $txt_booking_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 and b.job_no_mst=d.job_no and b.id=d.po_break_down_id and b.is_deleted=0 and b.status_active=1 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type , b.po_number, c.style_description,d.remark order by a.job_no,d.fabric_color_id";
    	//echo $nameArray_fabric_description; die;
    	$result_set=sql_select($nameArray_fabric_description);
		 foreach( $result_set as $row)
		 {
		 	$uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
			$fabric_attr = array('uom','construction','composition','c_type','gsm','dia','dia_type','gmt_color','style_ref_no','po_number','style_description','remark');
			foreach ($fabric_attr as $attr) {
				$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]][$attr][] = $row[csf($attr)];
			}

			$color_attr = array('rates');
			foreach ($color_attr as $attr) {
				$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]][$attr] = $row[csf($attr)];
			}
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['amounts'] += $row[csf('amounts')];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['fin_fab_qntys'] += $row[csf('fin_fab_qntys')];

			$summery_attr = array('body_part_id','construction','composition','po_number','rates','amounts','fin_fab_qntys','c_type','gsm','dia','dia_type','fab_color');
			foreach ($summery_attr as $attr) {
				$string = $row[csf("body_part_id")].'**'.$row[csf("construction")].'**'.$row[csf("composition")];
				if($attr == 'fab_color'){
					$fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $color_library[$row[csf($attr)]];
				}
				else{
					$fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $row[csf($attr)];
				}
			}


		 }
		 /*echo '<pre>';
		 print_r($fabric_detail_arr); die;*/

		 foreach ($fabric_detail_arr as $job_no => $uom_data_value) {
		 	foreach ($uom_data_value as $uom_key=> $po_data_arr) {
		 		foreach ($po_data_arr as $po_number => $construction_arr){
		 			foreach ($construction_arr as $cons_key => $body_part_arr) {
		 				foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {		 					
							foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
								foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
									$total_fab_color[$job_no][$uom_key]+=count($body_part_dtls['fab_color']);
								}								
							}
    					}
		 			}
    			}
		 	}
		 }

		 	/*echo '<pre>';
			print_r($total_fab_color); die;*/
			//$uom_val='';
		 	$grand_fin_fab_qty_sum =0;
			$grand_amount_sum =0;
			foreach($uom_data_arr as $uom_id=>$uom_val){?>
			    <div style="margin-top:15px">
			        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
			            <tr style="font-weight:bold">
			            	<td width="150">Job No.</td>
			            	<td width="150">Style Ref No.</td>
			            	<td width="150">Po. No.</td>
			                <td width="150">Body Part</td>
			                <td width="200">Fabric Construction</td>
			                <td width="200">Fabric Composition</td>
			                <td width="100">Color Type</td>
			                <td width="50">GSM</td>
			                <td width="100">Dia/C-Width</td>
			                <td width="100">Gmts. Color</td>
			                <td width="100">Fabric Color</td>
			                <td width="150">Lab Dip No/Mill Ref. No</td>
			                <td width="100">Fin Fab Qty(<? echo $uom_val ?>)</td>
			                <td width="50">Rate(<? echo $currency[$currency_id] ?>)</td>
			                <td width="50">Amount(<? echo $currency[$currency_id] ?>)</td>
			                <td width="100">Remarks</td>
			            </tr>
    <?
    	$fab_color_row = '';
    	foreach ($fabric_detail_arr as $job_no => $uom_data_value) {
    		$job_fin_fab_qty_sum =0;
    		$job_amount_sum =0;
    		$job_row='';

    		foreach ($uom_data_value as $uom_key=> $po_data_arr) {
    			$job_row=count($po_data_arr);
    			if($uom_id == $uom_key){
    				$poNum=1;
    				foreach ($po_data_arr as $po_number => $construction_arr) {
    					foreach ($construction_arr as $cons_key => $body_part_arr) {
    						foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {
    							//$total_fab_color_row='';
    							foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
    								foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
    									$color = 1;
				    					$fin_fab_qty_sum = 0;
				    					$amount_sum = 0;
				    					$fab_color_row = count($body_part_dtls['fab_color']);

				    					foreach ($body_part_dtls['fab_color'] as $fab_color_key => $fab_color_dtls) {
				    						if($color == 1){
				    							$fin_fab_qty_sum += $fab_color_dtls['fin_fab_qntys'];
				    							$amount_sum += $fab_color_dtls['amounts'];

				    						 	?>
				    							<tr>
				    								<? if($poNum==1){ ?>
				    								<td rowspan="<? echo $total_fab_color[$job_no][$uom_key] ?>"><? echo $job_no ?></td>
				    								<td rowspan="<? echo $total_fab_color[$job_no][$uom_key] ?>"><? echo implode(", ",array_unique($body_part_dtls['style_ref_no'])); ?></td>
				    								<? } ?>
				    								<td rowspan="<? echo $fab_color_row ?>"><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['po_number'])) ?></span></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo $body_part[$body_part_key] ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['construction'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['composition'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? 
									            	echo $color_type[$body_part_dtls['c_type'][0]];
									            	/*foreach ($body_part_dtls['c_type'] as $value) {
									            		$color_type_text[$fabric_dtls_id] = $color_type[$value];
									            	}
									            	echo implode(",",$color_type_text[$fabric_dtls_id])*/
									            	 ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['gsm'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['dia'])).','.$fabric_typee[implode(",",array_unique($body_part_dtls['dia_type']))] ?></td>
									            	<td><? echo $color_library[$gmt_color_key] ?></td>
									            	<td><? echo $color_library[$fab_color_key] ?></td>
									            	<td><? $lapdip_no="";
													$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
													if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
									            	<td><? echo number_format($fab_color_dtls['fin_fab_qntys'],3) ?></td>
									            	<td><? echo number_format($fab_color_dtls['rates'],4) ?></td>
									            	<td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
									            	<td><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['remark'])) ?></span></td>
									            </tr>
				    						<? } else{
				    						$fin_fab_qty_sum += $fab_color_dtls['fin_fab_qntys'];
				    						$amount_sum += $fab_color_dtls['amounts'];
				    					 	?>
				    							<tr>
				    								<td><? echo $color_library[$gmt_color_key] ?></td>
				    								<td><? echo $color_library[$fab_color_key] ?></td>
									            	<td><? $lapdip_no="";
													$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
													if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
									            	<td><? echo number_format($fab_color_dtls['fin_fab_qntys'],3) ?></td>
									            	<td><? echo number_format($fab_color_dtls['rates'],4) ?></td>
									            	<td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
									            	<td><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['remark'])) ?></span></td>
				    							</tr>
				    						<? }
				    						$color++;
				    					}
					    					$job_fin_fab_qty_sum += $fin_fab_qty_sum;
					        				$job_amount_sum += $amount_sum;
					        				$poNum++;
    								}			    					
				        		}
				        	}
				        }
    			 	}

    					$grand_fin_fab_qty_sum +=$job_fin_fab_qty_sum;
    					$grand_amount_sum += $job_amount_sum;
	    				?>
						<tr>
							<th colspan="11">&nbsp</th>
							<th>Job Total</th>
							<th><? echo def_number_format($job_fin_fab_qty_sum,2); ?></th>
							<th>&nbsp</th>
							<th><? echo def_number_format($job_amount_sum,2); ?></th>
							<th>&nbsp</th>
						</tr>
	    				<?

    			}

    		}

    	}

    ?>

        </table>
    </div>
    <? 		} ?>
    <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	   ?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
	    <tr>
			<th colspan="11" width="350">&nbsp</th>
			<th width="83">Grand Total</th>
			<th width="75"><? echo def_number_format($grand_fin_fab_qty_sum,2); ?></th>
			<th width="74">&nbsp</th>
			<th width="99"><? echo def_number_format($grand_amount_sum,2); ?></th>
			<th width="74">&nbsp</th>
	    </tr>
	</table>
	<div style="margin-top:15px">
		<span style="font-weight: bold; margin-bottom: 2px;">Summery:</span>
		<? foreach ($fabric_detail_summery as $uom_id => $summery_data_arr) {
		?>
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center; margin-top: 15px" rules="all">
			<tr style="font-weight:bold">
				<td>Fabric Type</td>
				<td>Construction</td>
				<td>Composition</td>
				<td>Color Type</td>
				<td>GSM</td>
				<td>Dia/C-Width</td>
				<td>Fabric Color</td>
				<td>No. of PO</td>
				<td>Consumption(<? echo $unit_of_measurement[$uom_id] ?>)</td>
				<td>Rate(<? echo $currency[$currency_id] ?>)</td>
	            <td>Amount(<? echo $currency[$currency_id] ?>)</td>
			</tr>
		<?
		foreach ($summery_data_arr as $summery_data) { ?>
			<tr>
				<td><? echo $body_part[implode(",",array_unique($summery_data['body_part_id']))] ?></td>
				<td><? echo implode(",",array_unique($summery_data['construction']))?></td>
				<td><? echo implode(",",array_unique($summery_data['composition']))?></td>
				<td><? 
					foreach ($summery_data['c_type'] as $value)
					{
	            		$color_type_text[$value] = $color_type[$value];
	            	}
	            	echo implode(",",$color_type_text);
				//echo $color_type[implode(",",array_unique($summery_data['c_type']))] 
				?></td>
				<td><? echo implode(",",array_unique($summery_data['gsm']))?></td>
				<td><? echo implode(",",array_unique($summery_data['dia'])).','.$fabric_typee[implode(",",array_unique($summery_data['dia_type']))] ?></td>
				<td><? echo implode(", ",array_unique($summery_data['fab_color'])) ?></td>
				<td><? echo count(array_unique($summery_data['po_number'])) ?></td>
				<td><? echo array_sum($summery_data['fin_fab_qntys']) ?></td>
				<td><? echo number_format(array_sum($summery_data['amounts'])/array_sum($summery_data['fin_fab_qntys']),4) ?></td>
				<td><? echo array_sum($summery_data['amounts']) ?></td>
			</tr>

		<? }
		}
		?>
		</table>
	</div>
	<div style="margin-top: 10px;">
		<table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($grand_amount_sum,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_amount_sum,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
	</div>
	
   	 <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px; ">
		<tr>
			<td width="50%" style="border:1px solid; border-color:#000;" valign="top">
				<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
						<th width="97%" align="left" height="30" colspan="2">Terms &amp; Condition</th>
						</tr>
					</thead>
					<tbody>
					<?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");
					if ( count($data_array)>0)
						{
						$i=0;
						foreach( $data_array as $row )
						{
						$i++;
						?>
						<tr id="settr_1" valign="top">
							<td><? echo $i;?>)&nbsp</td>
							<td><? echo $row[csf('terms')]; ?></td>
						</tr>
						<?
						}
						}
					?>
					</tbody>
				</table>
			</td>
			<td width="50%" valign="top" style="border:1px solid; border-color:#000;">
				<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
					<tr align="center">
						<th width="97%" align="left" height="30">Approved Instructions</th>
					</tr>
					<tr>
						<td><?  echo $nameArray_approved_comments_row[csf('comments')];  ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<? echo signature_table(121, $cbo_company_name, "1330px", 1); 

	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Partial Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}



 }

 if($action =="print_booking_7")
 {
	 extract($_REQUEST);
	 $cbo_company_name=str_replace("'","",$cbo_company_name);
	 $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	 $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	 $imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	 $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	 $pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	 $season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	 $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	 $marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
	 $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	 $supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	 $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	 $supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	 $company_info=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");
 
	 $po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
	 $path=str_replace("'","",$path);
	 if($path!="") $path=$path;elseif($image_cond==1) $path="../../../"; else $path="../../";
	 $nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
	 list($nameArray_approved_row)=$nameArray_approved;
	 $nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	 list($nameArray_approved_date_row)=$nameArray_approved_date;
	 $nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	 list($nameArray_approved_comments_row)=$nameArray_approved_comments;
	 $uom=0;
	 $job_data_arr=array();
	 foreach ($po_booking_info as $result_buy){
	 $job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
	 $job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
	 $job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
	 $job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
	 $job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
	 $job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
	 $job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
	 $job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
	 $job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
	 $job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
	 $job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
	 $job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
	 $job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
	 $job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
	 $job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	 }
	 $job_no= implode(",",array_unique($job_data_arr['job_no']));
	 $job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	 $product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	 $product_code=implode(",",array_unique($job_data_arr['product_code']));
	 $pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	 $gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	 $style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	 $style_description=implode(",",array_unique($job_data_arr['style_description']));
	 $dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	 $dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
	 $season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	 $order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	 $qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	 $client_id= implode(",",array_unique($job_data_arr['client']));
 
	 $po_data=array();
	 if($db_type==0){
	 $nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	 }
	 if($db_type==2){
	 $nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	 }
	 foreach ($nameArray_job as $result_job){
		 $po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
	 }
	 $grouping=implode(",",array_unique(array_filter($po_data['grouping'])));
 
	 $nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.pay_mode, a.currency_id from wo_booking_mst a  where   a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 ");
 
	 ?>
	 <?php ob_start();?>
	 <table style="border:1px solid black;table-layout: fixed; " width="100%">
		 <tr>
			 <td><img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' /></td>
			 <td style="text-align: center;">
				 <span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
				 <?
							 $nameArray2=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
							 foreach ($nameArray2 as $result)
							 {
							 ?>
								 <? echo $result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$result[csf('city')].' '.$result[csf('zip_code')].' '.$result[csf('province')].' '.$country_arr[$result[csf('country_id')]]; ?><br>
								 Email Address: <? echo $result[csf('email')];?>
								 Website: <? echo $result[csf('website')].'<br>';
							 }
							 ?>
				 <span style="font-size:16px; font-weight:bold">Fabric Purchase Order</span>
			 </td>
			 <td style="text-align: right; padding-right: 30px"><span style="font-size:16px; font-weight:bold;">Booking No: <? echo $nameArray[0][csf('booking_no')];?><? echo "(".$fabric_source[$nameArray[0][csf('fabric_source')]].")"?></span></td>
		 </tr>
	 </table>
	 <? foreach ($nameArray as $result) {
		 $currency_id=$result[csf('currency_id')];
		 $booking_date=$result[csf('update_date')];
			 if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			 $booking_date=$result[csf('insert_date')];
			 }
	  ?>
	 <table style="margin-top: 5px" class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
		 <tr>
			 <th width="175" style="text-align: left">Supplier Name </th>
			 <td width="175"> <?
				 if($result[csf('pay_mode')]==5){
				 echo $company_library[$result[csf('supplier_id')]];
				 }
				 else{
				 echo $supplier_name_arr[$result[csf('supplier_id')]];
				 }
			 ?>
			 </td>
			 <th width="175" style="text-align: left">Dealing Merchant </th>
			 <td width="175" > <? echo $dealing_marchant; ?></td>
			 <th width="175" style="text-align: left">Buyer/Agent Name</th>
			 <td width="175"> <? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>
 
		 </tr>
		 <tr>
			 <th style="text-align: left"><!--Attention--> </th>
			 <td> <? //echo $result[csf('attention')]; ?></td>
			 <th style="text-align: left"><!--Merchant E-Mail id--> </th>
			 <td> <? //echo $dealing_marchant_email ?></td>
			 <th style="text-align: left">Garments Item </th>
			 <td> <?
				 $gmts_item_name="";
				 $gmts_item=explode(',',$gmts_item_id);
				 for($g=0;$g<=count($gmts_item); $g++)
				 {
				 $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				 }
				 echo rtrim($gmts_item_name,',');
			 ?>
			 </td>
		 </tr>
		 <tr>
			 <th width="175" style="text-align: left">Booking Date </th>
			 <td width="175"> <? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
			 <th style="text-align: left"><!--Fabric ETD --></th>
			 <td> <? //echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
			 <th style="text-align: left"><!--Internal Ref No.--> </th>
			 <td> <? //echo $grouping ?></td>
		 </tr>
		 <tr>
			 <?
			 $delivery_address=explode("\n",$result[csf('delivery_address')]);
			 ?>
			 <th style="text-align: left">Delivery Address </th>
			 <td> <? if(count($delivery_address)>0){
				 foreach ($delivery_address as $key => $value) { ?>
				 <? echo $value ?><br>
				 <? }
			 } ?></td>
			  <th style="text-align: left"><!--Pay Mode--></th> 
			 <td> <? //echo $pay_mode[$result[csf('pay_mode')]] ?></td>
			 <th style="text-align: left"><!--Currency--></th>
			 <td> <?// echo $currency[$result[csf('currency_id')]] ?></td>
		 </tr>
		 <tr>
			 <th style="text-align: left"><!--Remarks--> </th>
			 <td colspan="5"> <? //echo $result[csf('remarks')]?></td>
		 </tr>
	 </table>
	 <? } ?>
	 <?
	
	//  $nameArray_fabric_description = "select a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, 
	//  a.gsm_weight as gsm, d.dia_width as dia, a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,
	//  sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no, c.style_description, 
	//   c.job_no_prefix_num, d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color, b.po_number,d.remark 
	//  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d 
	//  WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no and d.job_no=c.job_no and 
	//  d.booking_no = $txt_booking_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 and
	//   b.job_no_mst=d.job_no and b.id=d.po_break_down_id and b.is_deleted=0 and b.status_active=1
	//   group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id,
	//   a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type , b.po_number, c.style_description,d.remark order by a.job_no,d.fabric_color_id";
	$nameArray_fabric_description="SELECT d.job_no,d.id as fabric_cost_dtls_id, d.body_part_id, d.color_type_id as c_type, d.construction, d.composition, 
	d.gsm_weight as gsm,b.id, b.po_number, b.po_quantity, b.shipment_date, min(c.id) as color_size_table_id, min(color_order) as color_order, min(size_order) as size_order, c.color_number_id, c.size_number_id,a.style_ref_no,
c.plan_cut_qnty as plan_cut_qnty, c.order_quantity as order_quantity,d.avg_cons,d.width_dia_type
 from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d
 where a.job_no=b.job_no_mst and   a.job_no = d.job_no and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst in(".$job_no_in.") and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 
group by b.id, b.po_number, b.po_quantity, b.shipment_date, c.color_number_id, c.size_number_id,c.plan_cut_qnty, c.order_quantity,d.job_no,d.id , d.body_part_id, d.color_type_id , 
d.construction, d.composition, 
	d.gsm_weight,a.style_ref_no,d.avg_cons,d.width_dia_type order by b.id, color_order, size_order";

		//   echo $nameArray_fabric_description; die;
		 $result_set=sql_select($nameArray_fabric_description);
		  foreach( $result_set as $row)
		  {



			  $uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
			 $fabric_attr = array('uom','construction','composition','c_type','gsm','dia','dia_type','color_number_id','style_ref_no','po_number','style_description','remark','avg_cons','width_dia_type');
			 foreach ($fabric_attr as $attr) {
				 $po_data_arr[$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("color_number_id")]][$row[csf("fabric_cost_dtls_id")]][$attr][] = $row[csf($attr)];
			 }
 
			 $color_attr = array('rates');
			 foreach ($color_attr as $attr) {
				 $po_data_arr[$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("color_number_id")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]][$attr] = $row[csf($attr)];
			 }
			 $po_data_arr[$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("color_number_id")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['amounts'] += $row[csf('amounts')];
			//  $po_data_arr[$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['fin_fab_qntys'] += $row[csf('fin_fab_qntys')];
			 $po_data_arr[$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("color_number_id")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['req_qty'] += $row[csf('plan_cut_qnty')];
			 $po_data_arr[$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("color_number_id")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['gmt_qty'] += $row[csf('order_quantity')];
			 $po_data_arr[$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("color_number_id")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['avg_cons']= $row[csf('avg_cons')];
 
			 $summery_attr = array('body_part_id','construction','composition','po_number','rates','amounts','fin_fab_qntys','c_type','gsm','dia','dia_type','fab_color');
			 foreach ($summery_attr as $attr) {
				 $string = $row[csf("body_part_id")].'**'.$row[csf("construction")].'**'.$row[csf("composition")];
				 if($attr == 'fab_color'){
					 $fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $color_library[$row[csf($attr)]];
				 }
				 else{
					 $fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $row[csf($attr)];
				 }
			 }
 
 
		  }
		  /*echo '<pre>';
		  print_r($fabric_detail_arr); die;*/
 
		 	
				 
		
			  $grand_fin_fab_qty_sum =0;
			 $grand_amount_sum =0;
			 foreach($uom_data_arr as $uom_id=>$uom_val){?>
				 <div style="margin-top:15px">
					 <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
						 <tr style="font-weight:bold">
							 <td width="30">SL.</td>
							 <td width="150">Po. No.</td>
							 <td width="150">Style Ref No.</td>	
							 <td width="200">Fabric Composition</td>
							 <td width="50">GSM</td>	
							 <td width="50">Width Type </td>	
							 <td width="100">Color Type</td>													
							 <td width="100">Color</td>	
							 <td width="100">GMT Qty Pcs(<? echo $uom_val ?>)</td>		
							 <td width="100">Con/Dzn</td>							
							 <td width="100">Required Qty(<? echo $uom_val ?>)</td>					
							 <td width="100">Remarks</td>
						 </tr>
	 <?
		 $fab_color_row = '';
		
			 $job_fin_fab_qty_sum =0;
			 $job_amount_sum =0;
			 $job_row='';
 
		
				 $job_row=count($po_data_arr);
				
					 $poNum=1;
					 $i=1;
					 foreach ($po_data_arr as $po_number => $construction_arr) {
						 foreach ($construction_arr as $cons_key => $body_part_arr) {
							 foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {
								 //$total_fab_color_row='';
								 foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
									 foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
										
										
										 $amount_sum = 0;
										
										 $fab_color_row = count($body_part_dtls['fab_color']);
 
										 foreach ($body_part_dtls['fab_color'] as $fab_color_key => $fab_color_dtls) {
										
											
												 $booking_no=$nameArray[0][csf('booking_no')];
												 $po_number=implode(", ",array_unique($body_part_dtls['po_number']));
												 $style_ref=implode(", ",array_unique($body_part_dtls['style_ref_no'])); 
												 $dataArray=sql_select("select a.id, a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.gmts_item_id, c.job_no_prefix_num, c.style_ref_no, d.po_number, d.grouping, d.file_no from wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=1 and a.entry_form=271 and a.status_active =1 and a.is_deleted=0 and  c.style_ref_no='$style_ref'and  b.status_active =1 and b.is_deleted=0 and a.company_id='$cbo_company_name'  and a.booking_no='$booking_no' and d.po_number='$po_number'  group by a.id, a.booking_no_prefix_num, a.pay_mode,a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.job_no_prefix_num, c.gmts_item_id, c.style_ref_no, d.po_number, d.grouping, d.file_no order by a.id DESC");
//  print_r($dataArray);
												
													if($dataArray[0][csf('po_number')]!=''){
														$req_qty_sum += $fab_color_dtls['req_qty']*($fab_color_dtls['avg_cons'])/12;
														$gmt_qty_sum += $fab_color_dtls['gmt_qty'];
														$amount_sum += $fab_color_dtls['amounts'];
																		
												  ?>
												 <tr>
													 <td class=""><?=$i;?></td>
													 <td rowspan="<? echo $fab_color_row ?>"><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['po_number'])) ?></span></td>
													 <td><? echo implode(", ",array_unique($body_part_dtls['style_ref_no'])); ?></td>	
													 <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['composition'])) ?></td>
													 <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['gsm'])) ?></td>
													 <td rowspan="<? echo $fab_color_row ?>"><? $type=implode(",",array_unique($body_part_dtls['width_dia_type']));
													 
													 echo $fabric_typee[$type]; ?></td>	
													 <td rowspan="<? echo $fab_color_row ?>"><? 
													 echo $color_type[$body_part_dtls['c_type'][0]];
													 /*foreach ($body_part_dtls['c_type'] as $value) {
														 $color_type_text[$fabric_dtls_id] = $color_type[$value];
													 }
													 echo implode(",",$color_type_text[$fabric_dtls_id])*/
													  ?></td>
																									 
													 <td><? echo $color_library[$gmt_color_key] ?></td>													
													 <td><? echo number_format($fab_color_dtls['gmt_qty'],3) ?></td>
													 <td><? echo number_format($fab_color_dtls['avg_cons'],3) ?></td>
													 <td><?  echo  number_format($fab_color_dtls['req_qty']*($fab_color_dtls['avg_cons'])/12, 2,'.',''); ?></td>
													 <td><span style="overflow-wrap: break-word "><?  echo implode(",",array_unique($body_part_dtls['remark'])); ?></span></td>
													
												 </tr>
											 <? 
										
											 $i++;
											
												}
												$job_fin_fab_qty_sum += $req_qty_sum;
												$job_amount_sum += $amount_sum;
												$poNum++;
										 }
											 
									 }			    					
								 }
							 }
						 }
					  }
 
						
						 ?>
						 <tr>
							 <th colspan="7">&nbsp</th>
							 <th>GMT Qty Total</th>
							 <th><? echo def_number_format( $gmt_qty_sum ,2); ?></th>
							 <th>Required Qty Total</th>
							 <th><? echo def_number_format($req_qty_sum,2); ?></th>
							 <th>&nbsp</th>
						 </tr>
						 <?
 
				 
 
	 ?>
 
		 </table>
	 </div>
	 <? 		} ?>
	 <?
		$mcurrency="";
		$dcurrency="";
		if($currency_id==1)
		{
		 $mcurrency='Taka';
		 $dcurrency='Paisa';
		}
		if($currency_id==2)
		{
		 $mcurrency='USD';
		 $dcurrency='CENTS';
		}
		if($currency_id==3)
		{
		 $mcurrency='EURO';
		 $dcurrency='CENTS';
		}
		?>
	 <!-- <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
		 <tr>
			 <th colspan="2" width="350">&nbsp</th>
			 <th width="83">Grand Total</th>
			 <th width="75"><? echo def_number_format($grand_fin_fab_qty_sum,2); ?></th>
			 <th width="74">&nbsp</th>
			 <th width="99"><? echo def_number_format($grand_amount_sum,2); ?></th>
			 <th width="74">&nbsp</th>
		 </tr>
	 </table> -->

	 

	 <? echo signature_table(121, $cbo_company_name, "1330px", 1); 
 
	 $emailBody=ob_get_contents();
	 //ob_clean();
	 if($is_mail_send==1){
		 $req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		 $is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		 if($req_approved && $is_approved==1){
			 $emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		 }
		 elseif($req_approved && $is_approved==0){
			 $emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		 }
		 
		 
		 $mailArr=array();
		 if($mail_id!=''){$mailArr[]=$mail_id;}
		 if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		 
		 
		 $to=implode(',',$mailArr);
		 $subject="Partial Fabric Booking Auto Mail";
		 
		 if($to!=""){
			 require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			 require_once('../../../auto_mail/setting/mail_setting.php');
			 $header=mailHeader();
			 sendMailMailer( $to, $subject, $emailBody );
		 }
	 }
 
 
 
  }
 


if($action == "print_9") // Md. Helal Uddin
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$sql_mrcht=sql_select("SELECT team_member_name,id, member_contact_no,team_member_email from lib_mkt_team_member_info ");
	$marchant_data=array();
	foreach ($sql_mrcht as $row) 
	{
		$marchant_data[$row[csf('id')]]['team_member_name']=$row[csf('team_member_name')];
		$marchant_data[$row[csf('id')]]['member_contact_no']=$row[csf('member_contact_no')];
		$marchant_data[$row[csf('id')]]['team_member_email']=$row[csf('team_member_email')];
	}
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$company_info=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");

	$po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
	$path=str_replace("'","",$path);
	if($path!="") $path=$path;elseif($image_cond==1) $path="../../../"; else $path="../../";
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_comments_row)=$nameArray_approved_comments;
	$uom=0;
	$job_data_arr=array();
	foreach ($po_booking_info as $result_buy){
	$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
	$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
	$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
	$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
	$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
	$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
	$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
	$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
	$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
	$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['team_member_name'];
	$job_data_arr['member_contact_no'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['member_contact_no'];
	$job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['team_member_email'];
	
	$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
	$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
	$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
	$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	}
	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
	$member_contact_no=implode(",",array_unique($job_data_arr['member_contact_no']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(",",array_unique($job_data_arr['client']));
	$team_member_email=$dealing_marchant_email;

	$po_data=array();
	if($db_type==0){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	if($db_type==2){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	foreach ($nameArray_job as $result_job){
		$po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
	}
	$grouping=implode(",",array_unique(array_filter($po_data['grouping'])));

	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.pay_mode, a.currency_id,a.item_category, a.test_req from wo_booking_mst a where a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 ");

	$supplier_id=$nameArray[0][csf('supplier_id')];
	$sql_sup=sql_select("SELECT SUPPLIER_NAME,SHORT_NAME,CONTACT_NO,ADDRESS_1,email from lib_supplier where status_active=1 and is_deleted=0 and id=$supplier_id ");
	$address="";
	ob_start();
    ?>
	<table style="table-layout: fixed;width: 1200px; " >
		<tr>
			
			<td style="text-align: center;">
				<span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
				<?
                            $nameArray2=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray2 as $result)
                            {
                            	?>
                                <? echo $address=$result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$result[csf('city')].' '.$result[csf('zip_code')].' '.$result[csf('province')].' '.$country_arr[$result[csf('country_id')]]; 
                                /*

                                ?>

                                <br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website: <? echo $result[csf('website')].'<br>';
                                */
                            }
                            ?>
                            <br>
				<span style="font-size:16px; font-weight:bold">PURCHASE ORDER</span>
			</td>
			
		</tr>
	</table>
	<?

	$booking_no='';

	 foreach ($nameArray as $result) {
		$currency_id=$result[csf('currency_id')];
		$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
			$booking_no=$result[csf('booking_no')];
	 ?>
	 <table style="width: 1200px;">
		 	<tr>
		 		<th align="left" style="width: 48%">
		 			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
		 				<tr>
		 					<td>Order No</td>
		 					<td><? echo $result[csf('booking_no')];?></td>
		 				</tr>
		 				<tr>
		 					<td>Order Date</td>
		 					<td><? echo $result[csf('booking_date')];?></td>
		 				</tr>
		 			</table>
		 			
		 			
		 			
		 			
		 		</th>
		 		<th  style="width: 4%"></th>
		 		<td align="right"  style="width: 48%">
		 			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
		 				<tr>
		 					<td>Delivery Date</td>
		 					<td><? echo change_date_format($result[csf('delivery_date')]);?></td>
		 				</tr>
		 				<tr>
		 					<td>Test Requirement</td>
		 					<td><? echo $result[csf('test_req')];?></td>
		 				</tr>
		 				
		 			</table>
		 			
		 			
		 		</td>
		 	</tr>
		 	<tr>
		 		<th align="left" style="width: 48%">
			 		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%" >
			 				<caption><b>SUPPLIER</b></caption>
			 				<tr>
			 					<td width="120">Supplier Name</td>
			 					<td><? echo $sql_sup[0][csf('SUPPLIER_NAME')];?></td>
			 				</tr>
			 				<tr>
			 					<td>Supplier Code</td>
			 					<td></td>
			 				</tr>
			 				<tr>
			 					<td>Attention</td>
			 					<td><? echo $result[csf('attention')];?></td>
			 				</tr>
			 				<tr>
			 					<td>Address</td>
			 					<td><? echo $sql_sup[0][csf('ADDRESS_1')];?></td>
			 				</tr>
			 				<tr>
			 					<td>Contact No</td>
			 					<td><? echo $sql_sup[0][csf('CONTACT_NO')];?></td>
			 				</tr>
			 				<tr>
			 					<td>Email</td>
			 					<td><? echo $sql_sup[0][csf('email')];?></td>
			 				</tr>
			 			</table>
		 		</th>
		 			<th  style="width: 4%"></th>
		 			<th  align="right"  style="width: 48%">
		 				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%" >
			 				<caption><b>BUYER</b></caption>
			 				<tr width="160">
			 					<td>Purchaser Name</td>
			 					<td><? echo $company_library[$cbo_company_name];?></td>
			 				</tr>
			 				<tr>
			 					<td>Contact Person</td>
			 					<td><? echo $dealing_marchant; ?></td>
			 				</tr>
			 				<tr>
			 					<td>Contact No</td>
			 					<td><? echo $member_contact_no;?></td>
			 				</tr>
			 				<tr>
			 					<td>Email</td>
			 					<td><? echo $team_member_email;?></td>
			 				</tr>
			 				<tr>
			 					<td>Buyer/Agent Name</td>
			 					<td> <? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>
			 				</tr>
			 				<tr>
			 					<td>Garments Item</td>
			 					<td>
			 						
			 							<?
								            $gmts_item_name="";
											$gmts_item=explode(',',$gmts_item_id);
											for($g=0;$g<=count($gmts_item); $g++)
											{
											$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
											}
											echo rtrim($gmts_item_name,',');
										?>
										
									
								</td>
			 				</tr>
			 			</table>
		 			</th>
		 	</tr>
	 </table>
   
    <? } ?>
    <?
    
    $nameArray_fabric_description = "select a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia, a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no, c.style_description,  c.job_no_prefix_num, d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color, b.po_number,d.remark FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no and d.job_no=c.job_no and d.booking_no = $txt_booking_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 and b.job_no_mst=d.job_no and b.id=d.po_break_down_id and b.is_deleted=0 and b.status_active=1 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type , b.po_number, c.style_description,d.remark order by a.job_no,d.fabric_color_id";
    	//echo $nameArray_fabric_description; die;
    	$result_set=sql_select($nameArray_fabric_description);
		 foreach( $result_set as $row)
		 {
		 	$uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
			$fabric_attr = array('uom','construction','composition','c_type','gsm','dia','dia_type','gmt_color','style_ref_no','po_number','style_description','remark');
			foreach ($fabric_attr as $attr) {
				$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]][$attr][] = $row[csf($attr)];
			}

			$color_attr = array('rates');
			foreach ($color_attr as $attr) {
				$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]][$attr] = $row[csf($attr)];
			}
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['amounts'] += $row[csf('amounts')];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['fin_fab_qntys'] += $row[csf('fin_fab_qntys')];

			$summery_attr = array('body_part_id','construction','composition','po_number','rates','amounts','fin_fab_qntys','c_type','gsm','dia','dia_type','fab_color');
			foreach ($summery_attr as $attr) {
				$string = $row[csf("body_part_id")].'**'.$row[csf("construction")].'**'.$row[csf("composition")];
				if($attr == 'fab_color'){
					$fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $color_library[$row[csf($attr)]];
				}
				else{
					$fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $row[csf($attr)];
				}
			}


		 }
		 /*echo '<pre>';
		 print_r($fabric_detail_arr); die;*/

		 foreach ($fabric_detail_arr as $job_no => $uom_data_value) {
		 	foreach ($uom_data_value as $uom_key=> $po_data_arr) {
		 		foreach ($po_data_arr as $po_number => $construction_arr){
		 			foreach ($construction_arr as $cons_key => $body_part_arr) {
		 				foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {		 					
							foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
								foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
									$total_fab_color[$job_no][$uom_key]+=count($body_part_dtls['fab_color']);
								}								
							}
    					}
		 			}
    			}
		 	}
		 }

		 $fab_color_arr=array();
		 	/*echo '<pre>';
			print_r($total_fab_color); die;*/
			//$uom_val='';
		 	$grand_fin_fab_qty_sum =0;
			$grand_amount_sum =0;
			foreach($uom_data_arr as $uom_id=>$uom_val){?>
			    <div style="margin-top:15px">
			        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1"  style="text-align:center;width: 1200px;" rules="all">
			            <tr style="font-weight:bold">
			            	<td width="120">Job No.</td>
			            	<td width="130">Customer Po No</td>
			            	<td width="130">Customer Style</td>
			                <td width="170">Composition</td>
			                <td width="170">Construction</td>
			                <td width="50">Weight</td>
			                <td width="100">Dia/Width</td>
			               
			                <td width="100">Fabric Color</td>
			                <td width="100">UOM</td>
			                
			                <td width="100">Quantity</td>
			                <td width="50">Rate(<? echo $currency[$currency_id] ?>)</td>
			                <td width="50">Amount(<? echo $currency[$currency_id] ?>)</td>
			                <td width="300">Remarks</td>
			            </tr>
    <?
    	$fab_color_row = '';
    	foreach ($fabric_detail_arr as $job_no => $uom_data_value) {
    		$job_fin_fab_qty_sum =0;
    		$job_amount_sum =0;
    		$job_row='';

    		foreach ($uom_data_value as $uom_key=> $po_data_arr) {
    			$job_row=count($po_data_arr);
    			if($uom_id == $uom_key){
    				$poNum=1;
    				foreach ($po_data_arr as $po_number => $construction_arr) {
    					foreach ($construction_arr as $cons_key => $body_part_arr) {
    						foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {
    							//$total_fab_color_row='';
    							foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
    								foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
    									$color = 1;
				    					$fin_fab_qty_sum = 0;
				    					$amount_sum = 0;
				    					$fab_color_row = count($body_part_dtls['fab_color']);

				    					foreach ($body_part_dtls['fab_color'] as $fab_color_key => $fab_color_dtls) {
				    						if($color == 1){
				    							$fin_fab_qty_sum += $fab_color_dtls['fin_fab_qntys'];
				    							$amount_sum += $fab_color_dtls['amounts'];

				    						 	?>
				    							<tr>
				    								<? if($poNum==1){ ?>
				    								<td rowspan="<? echo $total_fab_color[$job_no][$uom_key] ?>"><? echo $job_no ?></td>
				    								<?} ?>
				    								<td rowspan="<? echo $fab_color_row ?>"><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['po_number'])) ?></span></td>
				    								<? if($poNum==1){ ?>
				    								<td rowspan="<? echo $total_fab_color[$job_no][$uom_key] ?>"><? echo implode(", ",array_unique($body_part_dtls['style_ref_no'])); ?></td>
				    								<? } ?>

				    								<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['composition'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['construction'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['gsm'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['dia'])).','.$fabric_typee[implode(",",array_unique($body_part_dtls['dia_type']))] ?></td>


									            	<td><? echo $color_library[$fab_color_key]   ; array_push($fab_color_arr, $color_library[$fab_color_key]);?></td>
									            	<td><? echo $uom_val ?></td>
									            	
									            	<td><? echo number_format($fab_color_dtls['fin_fab_qntys'],3) ?></td>
									            	<td><? echo number_format($fab_color_dtls['rates'],4) ?></td>
									            	<td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
									            	<td><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['remark'])) ?></span></td>
									            </tr>
				    						<? } else{
				    						$fin_fab_qty_sum += $fab_color_dtls['fin_fab_qntys'];
				    						$amount_sum += $fab_color_dtls['amounts'];
				    					 	?>
				    							<tr>
				    								
				    								<td><? echo $color_library[$fab_color_key];array_push($fab_color_arr, $color_library[$fab_color_key]); ?></td>
									            	<td><? echo $uom_val ?></td>
									            	<td><? echo number_format($fab_color_dtls['fin_fab_qntys'],3) ?></td>
									            	<td><? echo number_format($fab_color_dtls['rates'],4) ?></td>
									            	<td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
									            	<td><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['remark'])) ?></span></td>
				    							</tr>
				    						<? }
				    						$color++;
				    					}
					    					$job_fin_fab_qty_sum += $fin_fab_qty_sum;
					        				$job_amount_sum += $amount_sum;
					        				$poNum++;
    								}			    					
				        		}
				        	}
				        }
    			 	}

    					$grand_fin_fab_qty_sum +=$job_fin_fab_qty_sum;
    					$grand_amount_sum += $job_amount_sum;
	    				?>
						<tr>
							
							<th colspan="9" align="right">Job Total</th>
							<th><? echo def_number_format($job_fin_fab_qty_sum,2); ?></th>
							<th>&nbsp</th>
							<th><? echo def_number_format($job_amount_sum,2); ?></th>
							<th>&nbsp</th>
						</tr>
	    				<?

    			}

    		}

    	}

    ?>
    	<tfoot>
    			
					<th colspan="9" align="right">Grand Total</th>
					<th><? echo def_number_format($grand_fin_fab_qty_sum,2); ?></th>
					<th>&nbsp</th>
					<th><? echo def_number_format($grand_amount_sum,2); ?></th>
					<th>&nbsp</th>
				</tr>
    	</tfoot>
        </table>
    </div>
    <? 		} ?>
   
    <br>
    <?php 

    	 $sql_sample="SELECT a.booking_no,b.fabric_color_id as fab_color,a.delivery_date,b.remark,sum(b.fin_fab_qnty) as fin_fab_qntys,sum(b.grey_fab_qnty) as grey_fab_qntys,avg(b.rate) as rates,sum(b.amount) as amounts, c.body_part_id AS body_part_id,c.fabric_description AS fabric_description,c.uom AS uom FROM wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no  and b.pre_cost_fabric_cost_dtls_id=c.id and  a.tagged_booking_no='$booking_no' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
				       group by
				       	a.booking_no,
				  		b.fabric_color_id ,
				  		c.body_part_id,
				  		c.fabric_description,
				  		c.uom,
				  		a.delivery_date,
				  		b.remark
				  		";
		//echo $sql_sample;
	    $sample_res=sql_select($sql_sample);
	    if(count($sample_res))
	    {


		     ?>
		    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1"  style="text-align:center;width: 1200px;" rules="all">
		    	<caption >Sample Requirements</caption>
		    	<thead>
		    		<tr>
		    			
		    			<th>Booking No</th>
		    			<th>Fabric Description</th>
		    			<th>Fabric Color</th>
		    			<th>UOM</th>
		    			<th>Quantity</th>
		    			<th>Delivery Date</th>
		    			<th>Remarks</th>
		    		</tr>
		    	</thead>
		    	<tbody>
		    		<?php 


		    			
		    			

						   
						   $i=1;
						   foreach ($sample_res as $row) 
						   {
						   	
						   
			    		 		?>
					    		<tr>
					    			<td><p><?php echo $row[csf('booking_no')]; ?></p></td>
					    			<td><p><?php echo $body_part[$row[csf('body_part_id')]].",".$row[csf('fabric_description')] ; ?></p></td>
					    			<td><p><?php echo $color_library[$row[csf('fab_color')]] ; ?></p></td>
					    			<td><p><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					    			<td><p><?php echo number_format($row[csf('fin_fab_qntys')],2); ?></p></td>
					    			<td><p><?php echo change_date_format($row[csf('delivery_date')]); ?></p></td>
					    			<td><p><?php echo $row[csf('remark')]; ?></p></td>
					    		</tr>
					    		<?
					    	}
				    		?>
		    	</tbody>
		    </table>
		    <?php 

    	}
     ?>
    <br>
	
    <table   border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px;width: 1200px; ">
		<tr>
			<td width="40%" style="border:1px solid; border-color:#000;" valign="top">
				<?
                    echo get_spacial_instruction($txt_booking_no);
                ?>
			</td>
			<td width="20%"></td>
			<td width="40%" valign="top" >
				<table  width="100%"  >
					
					<tr align="left">
						<td><strong> Shipping Mark as below.</strong></td>
					</tr>
					<tr align="left">
						<td>Company Name : <? echo $company_library[$cbo_company_name]; ?></td>
					</tr>
					<tr align="left">
						<td>Address : <? echo $address; ?></td>
					</tr>
					<tr align="left">
						<td>Item Details : </td>
					</tr>
					<tr align="left">
						<td>Color : </td>
					</tr>
					<tr align="left">
						<td>Roll No : ____ of ____</td>
					</tr>
					<tr align="left">
						<td>Quantity :</td>
					</tr>
					<tr align="left">
						<td>Net Weight : </td>
					</tr>
					<tr align="left">
						<td>Gross Weight : </td>
						
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br>
	<h4>Note: Please mention all above description, terms and conditions in the PI</h4>
	<? echo signature_table(121, $cbo_company_name, "1200px", 1); ?>

	<? 

	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 410 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		

		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($$sql_sup[0][csf('email')]!=''){$mailArr[]=$sql_sup[0][csf('email')];}
		
		$to=implode(',',$mailArr);
		$subject="Partial Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}





}


if($action == "print_booking_10")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	//echo $show_yarn_rate.'b';
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$brand_name_arr=return_library_array( "select id,brand_name from lib_buyer_brand", "id", "brand_name");
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_info=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");

	$po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
	$path=str_replace("'","",$path);
	if($path!="") $path=$path; else $path="../../";
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_comments_row)=$nameArray_approved_comments;
	$uom=0;
	$job_data_arr=array();
	foreach ($po_booking_info as $result_buy){
	$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
	$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
	$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
	$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
	$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
	$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
	$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
	$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
	$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
	$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
	$job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
	$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
	$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
	$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
	$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	}
	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(",",array_unique($job_data_arr['client']));

	$po_data=array();
	if($db_type==0){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	if($db_type==2){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	foreach ($nameArray_job as $result_job){
		$po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
	}
	$grouping=implode(",",array_unique(array_filter($po_data['grouping'])));

	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.pay_mode, a.currency_id, season_year,season_id,brand_id from wo_booking_mst a  where   a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 ");

    ?>
	<table style="border:1px solid black;table-layout: fixed; " width="100%">
		<tr>
			<td><img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' /></td>
			<td style="text-align: center;">
				<span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
				<?
                            $nameArray2=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray2 as $result)
                            {
                            ?>
                                <? echo $result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$result[csf('city')].' '.$result[csf('zip_code')].' '.$result[csf('province')].' '.$country_arr[$result[csf('country_id')]]; ?><br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website: <? echo $result[csf('website')].'<br>';
                            }
                            ?>
				<span style="font-size:16px; font-weight:bold">Fabric Purchase Order</span>
			</td>
			<td style="text-align: right; padding-right: 30px"><span style="font-size:16px; font-weight:bold;">Booking No: <? echo $nameArray[0][csf('booking_no')];?><? echo "(".$fabric_source[$nameArray[0][csf('fabric_source')]].")"?></span></td>
		</tr>
	</table>
	<? foreach ($nameArray as $result) {
		$currency_id=$result[csf('currency_id')];
		$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
	 ?>
    <table style="margin-top: 5px" class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
        <tr>
        	<th width="175" style="text-align: left">Supplier Name </th>
            <td width="175"> <?
				if($result[csf('pay_mode')]==5){
				echo $company_library[$result[csf('supplier_id')]];
				}
				else{
				echo $supplier_name_arr[$result[csf('supplier_id')]];
				}
			?>
			</td>
			<th width="175" style="text-align: left">Dealing Merchant </th>
            <td width="175" > <? echo $dealing_marchant; ?></td>
            <th width="175" style="text-align: left">Buyer/Agent Name</th>
            <td width="175"> <? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>
            <th width="175" style="text-align: left">Season </th>
            <td width="175"> <?
				 echo $season_arr[$result[csf('season_id')]];
			?>
			</td>
            

        </tr>
        <tr>
			<th width="175" style="text-align: left">Season Year</th>
            <td width="175" > <? echo $result[csf('season_year')]; ?></td>
            <th width="175" style="text-align: left">Brand</th>
            <td width="175"> <?  echo $brand_name_arr[$result[csf('brand_id')]]; ?></td>
            <th style="text-align: left">Attention </th>
            <td> <? echo $result[csf('attention')]; ?></td>
            <th style="text-align: left">Merchant E-Mail id </th>
            <td> <? echo $dealing_marchant_email ?></td>
            

        </tr>
        
        <tr>
        	 <th width="175" style="text-align: left">Booking Date </th>
            <td width="175"> <? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
            <th style="text-align: left">Garments Item </th>
            <td> <?
	            $gmts_item_name="";
				$gmts_item=explode(',',$gmts_item_id);
				for($g=0;$g<=count($gmts_item); $g++)
				{
				$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
			?>
			</td>
              <th style="text-align: left">Fabric Delivery Date</th>
            <td> <? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
           <th style="text-align: left">Pay Mode</th>
            <td> <? echo $pay_mode[$result[csf('pay_mode')]] ?></td>
           
        </tr>
         
        <tr>
        	<?
        	$delivery_address=explode("\n",$result[csf('delivery_address')]);
        	?>
        	<th style="text-align: left">Delivery Address </th>
            <td colspan="5"> <? if(count($delivery_address)>0){
            	foreach ($delivery_address as $key => $value) { ?>
            	<? echo $value ?><br>
            	<? }
            } ?></td>
             <th style="text-align: left">Currency</th>
            <td> <? echo $currency[$result[csf('currency_id')]] ?></td>
             
        </tr>
        <tr>
        	<th style="text-align: left">Remarks </th>
            <td colspan="7"> <? echo $result[csf('remarks')]?></td>
        </tr>
    </table>
    <? } ?>
    <?
	$sql_deter="select a.fabric_ref, a.rd_no, a.id, b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=426 order by a.id,b.id";
	$result_deter=sql_select($sql_deter);
	 foreach( $result_deter as $row)
		 {
			$fab_deter_arr[$row[csf("id")]]['fabric_ref']=$row[csf("fabric_ref")]; 
			$fab_deter_arr[$row[csf("id")]]['rd_no']=$row[csf("rd_no")]; 
		 }
	
     $nameArray_fabric_description = "select a.job_no,a.id as fabric_cost_dtls_id,a.lib_yarn_count_deter_id as deter_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia, a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no, c.style_description, c.job_no_prefix_num, d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color, b.po_number,d.remark FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b, wo_po_details_master c, wo_booking_dtls d WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no and d.job_no=c.job_no and d.booking_no = $txt_booking_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 and b.job_no_mst=d.job_no and b.id=d.po_break_down_id and b.is_deleted=0 and b.status_active=1 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight,a.lib_yarn_count_deter_id, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type , b.po_number, c.style_description,d.remark order by a.job_no,d.fabric_color_id";
    	//echo $nameArray_fabric_description; die;
    	$result_set=sql_select($nameArray_fabric_description);
		 foreach( $result_set as $row)
		 {
		 	
			$uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
			$fabric_attr = array('uom','construction','composition','c_type','gsm','dia','dia_type','gmt_color','style_ref_no','po_number','style_description','remark');
			foreach ($fabric_attr as $attr) {
				$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]][$attr][] = $row[csf($attr)];
			}

			$color_attr = array('rates');
			foreach ($color_attr as $attr) {
				$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]][$attr] = $row[csf($attr)];
			}
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['amounts'] += $row[csf('amounts')];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['fin_fab_qntys'] += $row[csf('fin_fab_qntys')];
			
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf('po_number')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("fabric_cost_dtls_id")]]['fab_color'][$row[csf("fab_color")]]['deter_id'] = $row[csf('deter_id')];

			$summery_attr = array('body_part_id','construction','composition','po_number','rates','amounts','fin_fab_qntys','c_type','gsm','dia','dia_type','fab_color','deter_id');
			
						//echo $fabric_ref.', ';					
			foreach ($summery_attr as $attr) {
				$string = $row[csf("body_part_id")].'**'.$row[csf("construction")].'**'.$row[csf("composition")];
				if($attr == 'fab_color'){
					$fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $color_library[$row[csf($attr)]];
				}
				else{
					$fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $row[csf($attr)];
				}
			}


		 }
		 /*echo '<pre>';
		 print_r($fabric_detail_arr); die;*/

		 foreach ($fabric_detail_arr as $job_no => $uom_data_value) {
		 	foreach ($uom_data_value as $uom_key=> $po_data_arr) {
		 		foreach ($po_data_arr as $po_number => $construction_arr){
		 			foreach ($construction_arr as $cons_key => $body_part_arr) {
		 				foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {		 					
							foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
								foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
									$total_fab_color[$job_no][$uom_key]+=count($body_part_dtls['fab_color']);
								}								
							}
    					}
		 			}
    			}
		 	}
		 }

		 	/*echo '<pre>';
			print_r($total_fab_color); die;*/
			//$uom_val='';
		 	$grand_fin_fab_qty_sum =0;
			$grand_amount_sum =0;
			foreach($uom_data_arr as $uom_id=>$uom_val){?>
			    <div style="margin-top:15px">
			        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
			            <tr style="font-weight:bold">
			            	<td width="150">Job No.</td>
			            	<td width="150">Style Ref No.</td>
			            	<td width="150">Merch Style</td>
			                <td width="150">Body Part</td>
                            
                            <td width="100">Fabric Ref No/<br>Mill Ref. No</td>
                            <td width="100">RD Number</td>
                            
			                <td width="200">Fabric Construction</td>
			                <td width="200">Fabric Composition</td>
			                <td width="100">Color Type</td>
			                <td width="50">Weight</td>
			                <td width="100">Dia/C-Width</td>
			                <td width="100">Gmts. Color</td>
			                <td width="100">Fabric Color</td>
			               
			                <td width="100">Total Fab Qty(<? echo $uom_val ?>)</td>
                            <?
                           if($show_yarn_rate==1)
							{
							?>
			                <td width="50">Rate(<? echo $currency[$currency_id] ?>)</td>
			                <td width="50">Amount(<? echo $currency[$currency_id] ?>)</td>
                            <?
							}
							?>
			                <td width="100">Remarks</td>
			            </tr>
    <?
    	$fab_color_row = '';
    	foreach ($fabric_detail_arr as $job_no => $uom_data_value) {
    		$job_fin_fab_qty_sum =0;
    		$job_amount_sum =0;
    		$job_row='';

    		foreach ($uom_data_value as $uom_key=> $po_data_arr) {
    			$job_row=count($po_data_arr);
    			if($uom_id == $uom_key){
    				$poNum=1;
    				foreach ($po_data_arr as $po_number => $construction_arr) {
    					foreach ($construction_arr as $cons_key => $body_part_arr) {
    						foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {
    							//$total_fab_color_row='';
    							foreach ($gmt_color_data as $gmt_color_key => $fabric_dtls){
    								foreach ($fabric_dtls as $fabric_dtls_id => $body_part_dtls) {
    									$color = 1;
				    					$fin_fab_qty_sum = 0;
				    					$amount_sum = 0;
				    					$fab_color_row = count($body_part_dtls['fab_color']);

				    					foreach ($body_part_dtls['fab_color'] as $fab_color_key => $fab_color_dtls) {
				    						if($color == 1){
				    							$fin_fab_qty_sum += $fab_color_dtls['fin_fab_qntys'];
				    							$amount_sum += $fab_color_dtls['amounts'];
												$fabric_ref=$fab_deter_arr[$fab_color_dtls[("deter_id")]]['fabric_ref'];
												$rd_no=$fab_deter_arr[$fab_color_dtls[("deter_id")]]['rd_no'];//deter_id
				    						 	?>
				    							<tr>
				    								<? if($poNum==1){ ?>
				    								<td rowspan="<? echo $total_fab_color[$job_no][$uom_key] ?>"><? echo $job_no ?></td>
				    								<td rowspan="<? echo $total_fab_color[$job_no][$uom_key] ?>"><? echo implode(", ",array_unique($body_part_dtls['style_ref_no'])); ?></td>
				    								<? } ?>
				    								<td rowspan="<? echo $fab_color_row ?>"><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['po_number'])) ?></span></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo $body_part[$body_part_key] ?></td>
                                                    
                                                    <td rowspan="<? echo $fab_color_row ?>"><? echo $fabric_ref;?></td>
                                                    <td rowspan="<? echo $fab_color_row ?>"><? echo $rd_no; ?></td>
                                                    
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['construction'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['composition'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? 
									            	echo $color_type[$body_part_dtls['c_type'][0]];
									            	/*foreach ($body_part_dtls['c_type'] as $value) {
									            		$color_type_text[$fabric_dtls_id] = $color_type[$value];
									            	}
									            	echo implode(",",$color_type_text[$fabric_dtls_id])*/
									            	 ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['gsm'])) ?></td>
									            	<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['dia'])).','.$fabric_typee[implode(",",array_unique($body_part_dtls['dia_type']))] ?></td>
									            	<td><? echo $color_library[$gmt_color_key] ?></td>
									            	<td><? echo $color_library[$fab_color_key] ?></td>
									            	
									            	<td><? echo number_format($fab_color_dtls['fin_fab_qntys'],3) ?></td>
                                                     <?
													if($show_yarn_rate==1)
													{
													?>
									            	<td><? echo number_format($fab_color_dtls['rates'],4) ?></td>
									            	<td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
                                                    <?
													}
													?>
									            	<td><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['remark'])) ?></span></td>
									            </tr>
				    						<? } else{
				    						$fin_fab_qty_sum += $fab_color_dtls['fin_fab_qntys'];
				    						$amount_sum += $fab_color_dtls['amounts'];
				    					 	?>
				    							<tr>
				    								<td><? echo $color_library[$gmt_color_key] ?></td>
				    								<td><? echo $color_library[$fab_color_key] ?></td>
									            	<td><? $lapdip_no="";
													$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
													if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
									            	<td><? echo number_format($fab_color_dtls['fin_fab_qntys'],3) ?></td>
                                                      <?
													if($show_yarn_rate==1)
													{
													?>
									            	<td><? echo number_format($fab_color_dtls['rates'],4) ?></td>
									            	<td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
                                                    <?
													}
													?>
									            	<td><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['remark'])) ?></span></td>
				    							</tr>
				    						<? }
				    						$color++;
				    					}
					    					$job_fin_fab_qty_sum += $fin_fab_qty_sum;
					        				$job_amount_sum += $amount_sum;
					        				$poNum++;
    								}			    					
				        		}
				        	}
				        }
    			 	}

    					$grand_fin_fab_qty_sum +=$job_fin_fab_qty_sum;
    					$grand_amount_sum += $job_amount_sum;
	    				?>
						<tr>
							<th colspan="12">&nbsp</th>
							<th>Job Total</th>
                              <?
								if($show_yarn_rate==1)
								{
								?>
							<th><? echo def_number_format($job_fin_fab_qty_sum,2); ?></th>
							<th>&nbsp  </th>
                            <?
								}
							?>
							<th><? echo def_number_format($job_amount_sum,2); ?></th>
							<th>&nbsp </th>
						</tr>
	    				<?

    			}

    		}

    	}

    ?>

        </table>
    </div>
    <? 		} ?>
    <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	   ?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
	    <tr>
			<th colspan="11" width="350">&nbsp</th>
			<th width="83">Grand Total</th>
			<th width="75"><? echo def_number_format($grand_fin_fab_qty_sum,2); ?></th>
             <?
			if($show_yarn_rate==1)
			{
			?>
			<th width="74">&nbsp </th>
			<th width="99"><? echo def_number_format($grand_amount_sum,2); ?></th>
            <?
			}
			?>
			<th width="74">&nbsp </th>
	    </tr>
	</table>
	<div style="margin-top:15px">
		<span style="font-weight: bold; margin-bottom: 2px;">Summary:</span>
		<? foreach ($fabric_detail_summery as $uom_id => $summery_data_arr) {
		?>
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center; margin-top: 15px" rules="all">
			<tr style="font-weight:bold">
				<td>Fabric Type</td>
                 <td width="100">Fabric Ref No/<br>Mill Ref. No</td>
                 <td width="100">RD Number</td>
                            
				<td>Construction</td>
				<td>Composition</td>
				<td>Color Type</td>
				<td>GSM</td>
				<td>Dia/C-Width</td>
				<td>Fabric Color</td>
				<td>No. of PO</td>
				<td>Consumption(<? echo $unit_of_measurement[$uom_id] ?>)</td>
                 <?
				if($show_yarn_rate==1)
				{
				?>
				<td>Rate(<? echo $currency[$currency_id] ?>)</td>
	            <td>Amount(<? echo $currency[$currency_id] ?>)</td>
                <?
				}
				?>
			</tr>
		<?
		foreach ($summery_data_arr as $summery_data) { ?>
			<tr>
            <?
             $deter_id=implode(",",array_unique($summery_data['deter_id']));
			 $deter_ids=array_unique(explode(",",$deter_id));
			 $fabric_ref=""; $rd_no="";
			 foreach($deter_ids as $deterId)
			 {
				 $fabric_ref.=$fab_deter_arr[$deterId]['fabric_ref'].',';
				$rd_no.=$fab_deter_arr[$deterId]['rd_no'];//deter_id
			 }
			  $fabric_ref=rtrim($fabric_ref,',');
			  $rd_no=rtrim($rd_no,',');
			  $rd_nos=implode(",",array_unique(explode(",",$rd_no)));
			  $fabric_refs=implode(",",array_unique(explode(",",$fabric_ref)));
			?>
				<td><? echo $body_part[implode(",",array_unique($summery_data['body_part_id']))]; ?></td>
                <td><? echo $fabric_refs; ?></td>
                <td><? echo $rd_nos; ?></td>
				<td><? echo implode(",",array_unique($summery_data['construction']))?></td>
				<td><? echo implode(",",array_unique($summery_data['composition']))?></td>
				<td><? 
					foreach ($summery_data['c_type'] as $value)
					{
	            		$color_type_text[$value] = $color_type[$value];
	            	}
	            	echo implode(",",$color_type_text);
				//echo $color_type[implode(",",array_unique($summery_data['c_type']))] 
				?></td>
				<td><? echo implode(",",array_unique($summery_data['gsm']))?></td>
				<td><? echo implode(",",array_unique($summery_data['dia'])).','.$fabric_typee[implode(",",array_unique($summery_data['dia_type']))] ?></td>
				<td><? echo implode(", ",array_unique($summery_data['fab_color'])) ?></td>
				<td><? echo count(array_unique($summery_data['po_number'])) ?></td>
				<td><? echo array_sum($summery_data['fin_fab_qntys']) ?></td>
                  <?
				if($show_yarn_rate==1)
				{
				?>
				<td><? echo number_format(array_sum($summery_data['amounts'])/array_sum($summery_data['fin_fab_qntys']),4) ?></td>
				<td><? echo array_sum($summery_data['amounts']) ?></td>
                <?
				}
				?>
			</tr>

		<? }
		}
		?>
		</table>
	</div>
	<div style="margin-top: 10px;">
     <?
				if($show_yarn_rate==1)
				{
				?>
		<table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($grand_amount_sum,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_amount_sum,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
       <?
				}
	   ?>
	</div>

    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px; ">
		<tr>
			<td width="50%" style="border:1px solid; border-color:#000;" valign="top">
				<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
						<th width="97%" align="left" height="30" colspan="2">Terms &amp; Condition</th>
						</tr>
					</thead>
					<tbody>
					<?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");
					if ( count($data_array)>0)
						{
						$i=0;
						foreach( $data_array as $row )
						{
						$i++;
						?>
						<tr id="settr_1" valign="top">
							<td><? echo $i;?>)&nbsp</td>
							<td><? echo $row[csf('terms')]; ?></td>
						</tr>
						<?
						}
						}
					?>
					</tbody>
				</table>
			</td>
			<td width="50%" valign="top" style="border:1px solid; border-color:#000;">
				<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
					<tr align="center">
						<th width="97%" align="left" height="30">Approved Instructions</th>
					</tr>
					<tr>
						<td><?  echo $nameArray_approved_comments_row[csf('comments')];  ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<? echo signature_table(121, $cbo_company_name, "1330px", 1); ?>

<? }
if($action=="print_booking_5") //Aziz->>> 22-11-17
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$path=str_replace("'","",$path);
	if($path!="") $path=$path;elseif($image_cond==1) $path="../../../"; else $path="../../";

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' or form_name='knit_order_entry' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier  where status_active=1 and is_deleted=0",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier  where status_active=1 and is_deleted=0",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer  where status_active=1 and is_deleted=0",'id','buyer_name');
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;

	$job_po_arr=array();
	$ref_no='';$job_no_aarr='';
	$nameArray_per_job=sql_select( "select  a.job_no,a.style_ref_no,b.po_break_down_id,c.po_number,c.grouping  from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c where c.id=b.po_break_down_id and a.job_no=b.job_no and  a.job_no=c.job_no_mst and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  group by a.job_no,a.style_ref_no,b.po_break_down_id,c.grouping,c.po_number");
	foreach ($nameArray_per_job as $row_per_job){
	$job_no_aarr.="'".$row_per_job[csf('job_no')]."'".',';
	$job_po_arr[$row_per_job[csf('job_no')]].=$row_per_job[csf('po_number')].',';
	if($ref_no=='') $ref_no=$row_per_job[csf('grouping')]; else $ref_no.=",".$row_per_job[csf('grouping')];

	}
	$job_nos=rtrim($job_no_aarr,',');
	$job_nos=implode(",",array_unique(explode(",",$job_nos)));
	//$ref_no=$ref_no;
	$ref_nos=implode(",",array_unique(explode(",",$ref_no)));
	//$job_nos=explode(",",$job_nos);
	//print_r($job_no_aarr);
	$job_data_arr=array();
	$nameArray_buyer=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix,a.total_set_qnty,a.product_dept,a.product_code,a.pro_sub_dep,a.gmts_item_id ,a.order_repeat_no,a.qlty_label  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0 and a.job_no in(".$job_nos.") order by a.job_no ");
	foreach ($nameArray_buyer as $result_buy){
	$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
	$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
	$dealing_marchant.=$marchentrArr[$result_buy[csf('dealing_marchant')]].',';
}
//print_r($job_data_arr);
$dealing_marchant=rtrim($dealing_marchant,',');
$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchant)));
//$dealing_marchants=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.fabric_source,a.remarks,a.pay_mode,a.fabric_composition, a.delivery_address from wo_booking_mst a   where  a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0");
	foreach ($nameArray as $result_job){
		$booking_date=$result_job[csf('booking_date')];
		$buyer_id=$result_job[csf('buyer_id')];
		$currency_id=$result_job[csf('currency_id')];
		$attention=$result_job[csf('attention')];
		$delivery_date=$result_job[csf('delivery_date')];
		$supplier_id=$result_job[csf('supplier_id')];
		$remarks=$result_job[csf('remarks')];
		$pay_mode=$result_job[csf('pay_mode')];
		$delivery_address=$result_job[csf('delivery_address')];
	}
	//echo $booking_date.'ddd';

	?>
    <style>

	@media print
	{
	     .page-break { height:0; page-break-before:always; margin:0; border-top:none; }
	}
     body, p, span, td, a {font-size:10pt;font-family: Arial;}
     body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}
</style>
<?php ob_start();?>
<div style="width:1310px" align="center">
<table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
	<tr>
	<td width="100">
	<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
	</td>
	<td width="1250">
	<table width="100%" cellpadding="0" cellspacing="0"  border="0" >
	<tr>
	<td align="center">
	<?php
echo $company_library[$cbo_company_name];
?>
	</td>
	<td rowspan="3" width="250">
	<span><b> Booking No:&nbsp;&nbsp;<? echo trim($txt_booking_no,"'"); ?></b></span><br/>
    <span><b> Booking Date :&nbsp;&nbsp;<? echo change_date_format($booking_date); ?></b></span><br/>
	<?
	if($nameArray_approved_row[csf('approved_no')]>1)
	{
	?>
	<b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
	<br/>
	Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
	<?
	}
	?>
	</td>
	</tr>
	<tr>
	<td align="center">
	<?
	$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name";
	if($txt_job_no!="")
	{
	$location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
	}
	else
	{
	$location="";
	}
	foreach ($nameArray as $result)
	{
		$email=$result[csf('email')];
		$city=$result[csf('city')];
	//echo  $location_name_arr[$location];
	?>
	Email Address: <? echo $email;?>
	Website: <? echo $result[csf('website')]; ?>
	<?
	}
	?>
	</td>
	</tr>
	<tr>
	<td align="center">
	<strong><? if($report_title !=""){ echo $report_title.'-'.$fabric_source[$cbo_fabric_source];}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
	</td>
	</tr>
	</table>
	</td>
	</tr>
    </table>
    <table width="100%" style="border:0px solid black;table-layout: fixed;" >
	<tr>
	<td width="200"><span><b>To </b></span></td>
	<td width="280">&nbsp;<span></span></td>
	<td width="200"><span><b>Buyer</b></span></td>
    <td width="230"><span> :&nbsp;<b><? echo $buyer_name_arr[$buyer_id]; ?></b></span></td>
	</tr>
	<tr>
	<td width="200"><b>Supplier Name</b>   </td>
	<td width="280">:&nbsp;
	<?
	if($pay_mode==5 || $pay_mode==3){
	echo $company_library[$supplier_id];
	$suplier_address=$city.','.$email;
	}
	else{
	echo $supplier_name_arr[$supplier_id];
	$suplier_address=$supplier_address_arr[$supplier_id];
	}
	?>    </td>
	<td width="200"><b>Dealing Marchant</b></td>
	<td width="" colspan="2">:&nbsp;<? echo $dealing_marchants;?></td>
	</tr>
	<tr>
	<td width="200"><b>Address</b></td>
	<td width="280">:&nbsp;<? echo $suplier_address; ?></td>
	<td width="200"><b>Currency </b>   </td>
	<td width="230">:&nbsp;
	<?
	echo $currency[$currency_id];
	?>
	</td>
    <tr>
	<td width="200"><b>Attention</b></td>
	<td  width="280">:&nbsp;<? echo $attention; ?></td>
	<td width="200"><b>Internal Ref. No </b>   </td>
	<td colspan="2">:&nbsp;
	<?
	echo $ref_nos;
	?>
	</td>
	</tr>
	<tr>
	<td width="200"><b>Delivery Date</b></td>
	<td width="280">:&nbsp;<? echo change_date_format($delivery_date); ?></td>
	<td><b></b></td>
	<td>&nbsp;<? //echo $order_repeat_no; ?></td>
	</tr>
	<tr>
	<td width="200"><b>Remark</b></td>
	<td colspan="4"> :<? echo $remarks;?></td>
	</tr>
	<tr>
	<td width="200"><b>Delivery Address</b></td>
	<td colspan="4"> : <? echo $delivery_address;?></td>
	</tr>
	</table>
    <?
	//echo "select  a.job_no,a.body_part_id,b.color_number_id,avg(b.process_loss_percent) as loss FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls  b 	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in(".$job_nos.")  group by a.job_no,a.body_part_id,b.color_number_id";
	$color_wise_process_loss=sql_select("select  a.job_no,a.body_part_id,b.color_number_id,a.process_loss_method,b.process_loss_percent as loss FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls  b 	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in(".$job_nos.") and b.process_loss_percent>0 group by a.job_no,a.body_part_id,a.process_loss_method,b.color_number_id,b.process_loss_percent");
	foreach($color_wise_process_loss as $val)
	{
		$loss_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss']=$val[csf("loss")];
		$loss_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss_method']=$val[csf("process_loss_method")];
	}
	if($job_nos!='')
	{
		$lab_dip=sql_select("select  b.job_no,a.labtest_no FROM wo_labtest_mst a, wo_labtest_dtls  b WHERE a.id=b.mst_id and b.job_no in(".$job_nos.") ");
		foreach($lab_dip as $row)
		{
			$lab_dip_arr[$row[csf("job_no")]]['labtest_no']=$row[csf("labtest_no")];

		}
	}
	//wo_labtest_mst
   $sql_booking="select  a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id,a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no,c.job_no_prefix_num,d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color
	FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls d,wo_po_details_master c
	WHERE   a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id  and a.job_no=c.job_no  and d.job_no=c.job_no
	and d.booking_no =$txt_booking_no and d.job_no in(".$job_nos.") and d.status_active=1 and d.is_deleted=0
	group by  a.job_no,a.id, a.body_part_id,a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num  order by a.job_no,d.fabric_color_id ";
	 $result_set=sql_select($sql_booking);
	 foreach( $result_set as $row)
	 {
		$body_part_id=$body_part[$row[csf("body_part_id")]];
		$uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
		$construction=$row[csf("construction")];
		$compositions= $row[csf("composition")];
		$item_desc=$body_part_id.','.$row[csf("construction")].','.$compositions;
		/*$process_loss=$loss_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("fab_color")]]['loss'];
		$process_loss_method=$loss_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("fab_color")]]['loss_method'];*/

		$process_loss=$loss_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['loss'];
		$process_loss_method=$loss_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['loss_method'];

		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['uom']=$row[csf("uom")];
		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['style_ref_no']=$row[csf("style_ref_no")];
		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['job_prefix']=$row[csf("job_no_prefix_num")];
		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['style_ref_no']=$row[csf("style_ref_no")];

		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['fin_qty']+=$row[csf("fin_fab_qntys")];
		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['grey_qty']+=$row[csf("grey_fab_qntys")];
		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['amounts']+=$row[csf("amounts")];
		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['rates']=$row[csf("rates")];
		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['p_loss']=$process_loss;
		$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['p_loss_method']=$process_loss_method;
	 }
	 //print_r($uom_data_arr);
	 $fab_row_span_arr=array();
	foreach($fabric_detail_arr as $job_key=>$job_data)
	{
		$desc_rowspan=0;
		foreach($job_data as $desc_key=>$desc_data)
		{
			foreach($desc_data as $gsm_key=>$gsm_data)
			{
				foreach($gsm_data as $dia_key=>$dia_data)
				{
					foreach($dia_data as $c_type_key=>$color_data)
					{
						foreach($color_data as $gmt_color_key=>$gmt_color_data)
						{
							foreach($gmt_color_data as $fab_color_key=>$val)
							{
								$desc_rowspan++;
							}
							$fab_row_span_arr[$job_key]=$desc_rowspan;
						}
					}
				}
			}
		}
	}
	foreach($uom_data_arr as $uom_id=>$uom_val)
	{
	?>
    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	<caption> <strong><? //echo $uom_val;?></strong> </caption>
	<tr>
        <th  width="30" align="center">SL</th>
        <th  width="70" align="center">Job No</th>
        <th  width="120" align="center">Style Ref</th>
        <th  width="120" align="center">Order No</th>
        <th  width="300" align="center">Item Description</th>
        <th  width="50" align="center">GSM</th>
        <th  width="60" align="center">Fabric Dia</th>
        <th  width="80" align="center">Color Type</th>
        <th  width="100" align="center">Gmts Color</th>
        <th  width="100" align="center">Fabric Color</th>
        <th  width="120" align="center">Lab Dip No</th>
        <th  width="50" align="center">UOM</th>
        <th width='100' align="center">Finish Fab. Qty</th>
        <th width='100' align="center">Grey Fab. Qty</th>
        <th width='80' align="center">Avg Rate</th>
        <th width='100' align="center">Amount</th>
	</tr>
   <?
	// print_r($body_rowspan_arr);
	$k=$p=1;$total_fin_qty=$total_grey_qty=$total_amount=0;
	foreach($fabric_detail_arr as $job_key=>$job_data)
	{
		$y=1;
		foreach($job_data as $desc_key=>$desc_data)
		{
			foreach($desc_data as $gsm_key=>$gsm_data)
			{
				foreach($gsm_data as $dia_key=>$dia_data)
				{
					foreach($dia_data as $c_type_key=>$color_data)
					{
						foreach($color_data as $gmt_color_key=>$gmt_color_data)
						{
							foreach($gmt_color_data as $fab_color_key=>$val)
							{
								$po_nos=rtrim($job_po_arr[$job_key],',');
								$po_nos=implode(",",array_unique(explode(",",$po_nos)));
								$fab_row_span=$fab_row_span_arr[$job_key];
								$p_loss_method=$val['p_loss_method'];
								$process_loss=$val['p_loss'];
								$labtest_no=$lab_dip_arr[$job_key]['labtest_no'];
								if($process_loss) $process_loss=$process_loss;else $process_loss=0;
								//echo $process_loss.'d';
								if($p_loss_method==1) //markup
								{

									$fin_qty=$val['fin_qty']-(($val['fin_qty']*$process_loss)/(100+$process_loss));
								}
								else if($p_loss_method==2) //margin
								{
									$fin_qty=$val['fin_qty']-(($val['fin_qty']*$process_loss)/100);
								}
								if($uom_id==$val['uom'])
								{
								?>
								<tr>
									<?
                                    if($y==1)
									{
									?>
									<td width="30"  rowspan="<? echo $fab_row_span;?>"><? echo $p; ?></td>
									<td width="70" align="center" rowspan="<? echo $fab_row_span;?>"><p><? echo $val['job_prefix']; ?>&nbsp;</p></td>
									<td width="120" rowspan="<? echo $fab_row_span;?>"><p><? echo $val['style_ref_no']; ?>&nbsp;</p></td>
									<td  style="word-break:break-all;word-wrap: break-word;" width="120" rowspan="<? echo $fab_row_span;?>"><p><? echo $po_nos; ?>&nbsp;</p></td>
                                    <?
									}
									?>
									<td width="300"><p><? echo $desc_key; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $gsm_key; ?>&nbsp;</p></td>
									<td width="60"><p><? echo $dia_key; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $color_type[$c_type_key]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $color_library[$gmt_color_key]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $color_library[$fab_color_key]; ?>&nbsp;</p></td>
                                    <td width="120"><p><? echo $labtest_no; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $unit_of_measurement[$val['uom']]; ?>&nbsp;</p></td>
									<td width="100" align="right" title="(Markup/Margin Method) Process Loss=<? echo $process_loss;?>,Fin Qty=<? echo $val['fin_qty'];?>"><p><? echo number_format($fin_qty,2); ?>&nbsp;</p></td>
									<td width="100" align="right"><p><? echo number_format($val['grey_qty'],2); ?>&nbsp;</p></td>
									<td width="80" align="right" ><p><? echo number_format($val['amounts']/$val['grey_qty'],2); ?>&nbsp;</p></td>
									<td width="100" align="right"><p><? echo number_format($val['amounts'],2); ?>&nbsp;</p></td>
								</tr>
								<?
								$k++;$y++;
								$total_fin_qty+=$fin_qty;
								$total_grey_qty+=$val['grey_qty'];
								$total_amount+=$val['amounts'];
								}
							}
						}
					}
				}
			}
		}
		$p++;
	}
	?>
                            <tfoot>
                            <tr>
	                            <th colspan="12" align="right"> Total </th>
	                             <th align="right"> <? echo number_format($total_fin_qty,2); ?> </th>
	                             <th align="right"> <? echo number_format($total_grey_qty,2); ?> </th>
	                             <th align="right"> <? //echo number_format($total_fin_qty,2); ?> </th>
	                             <th align="right"> <? echo number_format($total_amount,2).' ('.$currency[$currency_id].')'; ?> </th>
                            </tr>
                            <tr>
                            	<? if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";} ?>
		                   		<td colspan="17" align="left"><b>In Word: <? echo number_to_words($total_amount,$currency[$currency_id],$paysa_sent); ?></b></td>
		               		</tr>
                            </tfoot>
                            </table>
                            <?
	}


                            ?><br>
						   <?
                              echo signature_table(121, $cbo_company_name, "1000px");
                           ?>

</div>
    <?

$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}

		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		$to=implode(',',$mailArr);
		$subject="Partial Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
// print_r($print_report_format);
	$print_report_format_arr=explode(",",$print_report_format);
		echo "$('#print_1').hide();\n";
		echo "$('#print_2').hide();\n";
		echo "$('#print_3').hide();\n";
		echo "$('#print_4').hide();\n";
		echo "$('#print_5').hide();\n";
		echo "$('#print_6').hide();\n";
		echo "$('#print_7').hide();\n";
		echo "$('#fabric_booking_report').hide();\n";
		echo "$('#print_9').hide();\n";
		//echo "$('#print_7').hide();\n";
	foreach($print_report_format_arr as $id){
		if($id==143){echo "$('#print_1').show();\n";}
		if($id==84){echo "$('#print_2').show();\n";}
		if($id==85){echo "$('#print_3').show();\n";}
		if($id==151){echo "$('#print_4').show();\n";}
		if($id==160){echo "$('#print_5').show();\n";}
		if($id==175){echo "$('#print_6').show();\n";}
		if($id==155){echo "$('#fabric_booking_report').show();\n";}
		if($id==235){echo "$('#print_9').show();\n";}
		if($id==191){echo "$('#print_7').show();\n";}


		
		//if($id==274){echo "$('#print_7').show();\n";}

	}
	exit();
}

if($action=="show_fabric_booking_report_advance_attire_ltd")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$fab_uomArr = return_library_array("select id,uom from wo_pre_cost_fabric_cost_dtls","id","uom");
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' or form_name='knit_order_entry' and file_type=1",'master_tble_id','image_location');

	$sql_mst="select buyer_id, booking_no, booking_date, supplier_id, currency_id, exchange_rate, attention, delivery_date, pay_mode, po_break_down_id, colar_excess_percent, cuff_excess_percent, delivery_date, is_apply_last_update, fabric_source, rmg_process_breakdown, insert_date, update_date, uom, remarks, pay_mode, fabric_composition from wo_booking_mst where booking_no=$txt_booking_no and status_active =1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);

	$booking_no=$dataArray[0][csf('booking_no')];

	if($db_type==0)
	{
		$job_no=return_field_value( "group_concat(job_no) as job_no", "wo_booking_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0", "job_no");
	}
	else
	{
		$job_no=return_field_value( "listagg(CAST(job_no as VARCHAR(4000)),',') within group (order by job_no) as job_no", "wo_booking_dtls","booking_no='$booking_no' and status_active=1 and is_deleted=0", "job_no");
	}

	$job_no=array_unique(explode(",",$job_no));
	$job_nos="'".implode("','",$job_no)."'";
	//echo $job_no;

	//$job_arr=array(); $po_arr=array();
	$season=$season_matrix=$dealing_marchant=$po_number=$pub_shipment_date='';
	$po_sql=sql_select("select a.season, a.season_matrix, a.dealing_marchant, b.po_number, b.pub_shipment_date  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no in(".$job_nos.") and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0");
	foreach($po_sql as $row)
	{
		if($row[csf('season_matrix')]!=0) $row[csf('season_matrix')]=$row[csf('season')]; else $row[csf('season_matrix')]=$season_arr[$row[csf('season_matrix')]];
		if($season=="") $season=$row[csf('season_matrix')]; else $season.=','.$row[csf('season_matrix')];
		if($dealing_marchant=="") $dealing_marchant=$marchentrArr[$row[csf('dealing_marchant')]]; else $dealing_marchant.=','.$marchentrArr[$row[csf('dealing_marchant')]];
		if($po_number=="") $po_number=$row[csf('po_number')]; else $po_number.=','.$row[csf('po_number')];
		if($pub_shipment_date=="") $pub_shipment_date=change_date_format($row[csf('pub_shipment_date')]); else $pub_shipment_date.=','.change_date_format($row[csf('pub_shipment_date')]);
	}

	$season=implode(",",array_unique(explode(",",$season)));
	$dealing_marchant=implode(",",array_unique(explode(",",$dealing_marchant)));
	$po_number=implode(",",array_unique(explode(",",$po_number)));
	$pub_shipment_date=implode(",",array_unique(explode(",",$pub_shipment_date)));
	$path=str_replace("'","",$path);
	if($path=="")
	{
		$path='../../';
	}elseif($image_cond==1){ $path='../../../';};
	ob_start();
	?>
      <html>
      <head> </head>
	<div style="width:930px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
    	<tr><td width="70">&nbsp;</td><td align="right"><strong>Po No: <? echo $dataArray[0][csf('booking_no')]; ?></strong></td></tr>
        <tr><td width="70">&nbsp;</td><td align="right"><strong>Po Date: <? echo change_date_format($dataArray[0][csf('booking_date')]); ?></strong></td></tr>
        <tr><td width="70">&nbsp;</td><td align="right"><strong>Delivery Date: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></strong></td></tr>
        <tr>
            <td width="70" align="right">
            	<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$cbo_company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px"><? echo show_company($cbo_company_name,'',''); ?></td>
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="930" cellspacing="0" align="" border="0">
    	<tr><td colspan="6" align="left">To.</td></tr>
        <tr>
            <td width="305" colspan="2"><strong></strong>
            <?	$supplier_name="";
            	if($dataArray[0][csf('pay_mode')]==1 || $dataArray[0][csf('pay_mode')]==2)
                {
                    $party_add=$dataArray[0][csf('supplier_id')];
                    $nameArray=sql_select( "select address_1, web_site, country_id from lib_supplier where id=$party_add");
                    foreach ($nameArray as $result)
                    {
                        $address="";
                         if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                    }
                    $supplier_name=$supplier_arr[$party_add].'<br>'.' : Address :- '.$address;
                }
				else
					$supplier_name=$company_library[$dataArray[0][csf('supplier_id')]];
				echo $supplier_name;
			?>
            </td>
            <td width="130"><strong>Buyer: </strong></td><td width="175px"> <? echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; ?></td>
            <td width="130"><strong>Currency :</strong></td> <td width="175"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
        </tr>
        <tr>
            <td width="130"><strong>Order No: </strong></td><td width="175px"> <? echo $po_number; ?></td>
            <td width="130"><strong>Season :</strong></td> <td width="175"><? echo $season; ?></td>
            <td width="130"><strong>Attention:</strong></td> <td width="175"><? echo $dataArray[0][csf('attention')]; ?></td>
        </tr>
        <tr>
            <td><strong>Job No.: </strong></td><td> <? echo implode(",",$job_no); ?></td>
            <td><strong>Dealing Merchant:</strong></td><td><? echo $dealing_marchant; ?></td>
            <td><strong>Shipment Date:</strong></td><td><? echo $pub_shipment_date; ?></td>
        </tr>
        <tr>
            <td><strong>Booking Remarks:</strong></td><td colspan="5"><? echo $currency[$dataArray[0][csf('remarks')]]; ?></td>
        </tr>
    </table>
    <br>
	<?php ob_start();?>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="200">Item Description</th>
               <!-- <th width="60">GSM</th>-->
                <th width="70">Width</th>
                <th width="80">Color Type</th>
                <th width="100">GMT Color</th>
                <th width="100">Fabric Color</th>
                <th width="50">UOM</th>
                <th width="80">Fabric Qty.</th>
                <th width="70">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody>
				<?
				//pre_cost_fabric_cost_dtls_id
                	$sql_result= sql_select("select construction, copmposition,pre_cost_fabric_cost_dtls_id as fab_cost_id, gsm_weight, dia_width, color_type, gmts_color_id, fabric_color_id, uom, fin_fab_qnty, rate, amount from wo_booking_dtls where booking_no ='$booking_no' and status_active=1 and is_deleted=0");
					/*a.id as fabric_cost_dtls_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, a.uom, sum(d.fin_fab_qnty) as fin_fab_qntys, avg(d.rate) as rates, sum(d.amount) as amounts, d.fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and
	b.color_number_id=d.gmts_color_id and
	b.dia_width=d.dia_width and
	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id
	and
	d.booking_no =$txt_booking_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and

	d.status_active=1 and
	d.is_deleted=0 and
	b.cons>0
	group by a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,d.pre_cost_remarks,a.uom ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down  order by a.body_part_id,d.fabric_color_id,a.uom  ");*/
                $i=1;
                foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$uom_id=$fab_uomArr[$row[csf('fab_cost_id')]];
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $row[csf('construction')].' '.$row[csf('copmposition')]; ?></td>
                        <!--<td><? //echo $row[csf('gsm_weight')]; ?></td>-->
                        <td><? echo $row[csf('dia_width')]; ?></td>
                        <td><? echo $color_type[$row[csf('color_type')]]; ?></td>
                        <td><? echo $color_arr[$row[csf('gmts_color_id')]]; ?></td>
                        <td><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
                        <td><? echo $unit_of_measurement[$uom_id]; ?></td>
                        <td align="right"><? echo number_format($row[csf('fin_fab_qnty')],2); ?></td>
                        <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                        <td align="right"><? echo number_format($row[csf('amount')],2); ?></td>
					</tr>
					<?
					$i++;
					$grand_tot_qty+=$row[csf('fin_fab_qnty')];
					$grand_tot_amount+=$row[csf('amount')];
				}
				$carrency_id=$dataArray[0][csf('currency_id')];
				if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				?>
            </tbody>
            <tfoot>
                <tr>
                    <td align="right" colspan="7"><strong>Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty,2); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_amount,2); ?>&nbsp;</td>
                </tr>
               <tr>
                   <td colspan="11" align="left"><b>In Word: <? echo number_to_words(def_number_format($grand_tot_amount,2,""),$currency[$carrency_id],$paysa_sent); ?></b></td>
               </tr>
           </tfoot>
        </table>
        <?
        $sql_terms="select id, terms from  wo_booking_terms_condition where booking_no='$booking_no' order by id ASC";
		$result_sql_terms =sql_select($sql_terms);

		$k=1;
		if(count($result_sql_terms)>0)
		{
			?>
			<table width="930" align="left" >
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2" align="center"><b>Special Instraction</b></td></tr>
				<?
				foreach($result_sql_terms as $rows)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30"><? echo $k; ?></td>
						<td><p><? echo $rows[csf('terms')]; ?></p></td>
					</tr>
					<?
					$k++;
				}
				?>
            </table>
            <?
		}
		?>
        <br>
		 <?
            echo signature_table(121, $cbo_company_name, "930px");
         ?>
        </div>
		<?php

	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
	
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
	
		$to=implode(',',$mailArr);
		$subject="Partial Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}

	?>
    </div>
    </html>
    
	<?
	$user_id=$_SESSION['logic_erp']['user_id'];
	$report_cat=100;
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename****$html****$report_cat";
	//exit();
}


if($action=="print_booking_3") // rehan for northern
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' or form_name='knit_order_entry' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country  where status_active=1 and is_deleted=0",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier  where status_active=1 and is_deleted=0",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier  where status_active=1 and is_deleted=0",'id','address_1');


	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer  where status_active=1 and is_deleted=0",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location  where status_active=1 and is_deleted=0",'id','location_name');

	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season  where status_active=1 and is_deleted=0","id","season_name");
	$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where status_active=1 and is_deleted=0 and id in(select po_break_down_id from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no=$txt_booking_no)",'id','po_number');

	$uom=0;
	$joball=array();
	$nameArray_per_job=sql_select( "SELECT  a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept,max(c.booking_date) as booking_date ,max(c.delivery_date) as delivery_date,max(c.is_approved) as is_approved,max(c.buyer_id) as buyer_id,max(c.pay_mode) as pay_mode,max(supplier_id) as supplier_id,max(a.dealing_marchant) as dealing_marchant,max(c.attention) as attention,max(c.currency_id) as currency_id ,max(c.remarks)  as remarks from wo_po_details_master a , wo_booking_dtls b,wo_booking_mst c  where c.booking_no=b.booking_no and c.status_active=1 and c.is_deleted=0 and  a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  group by  a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept ");
	foreach ($nameArray_per_job as $vals)
	{
		$joball['job_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('job_no')];
		$joball['style_ref_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('style_ref_no')];
		$all_job_arr[$vals[csf("job_no")]]["job"]="'".$vals[csf("job_no")]."'";
		$all_job_arr[$vals[csf("job_no")]]["style"]=$vals[csf("style_ref_no")];
		$all_job_arr[$vals[csf("job_no")]]["item"]=$vals[csf("gmts_item_id")];
		$all_job_arr[$vals[csf("job_no")]]["season"]=$vals[csf("season_matrix")];
		$all_job_arr[$vals[csf("job_no")]]["dept"]=$vals[csf("product_dept")];
		$all_jobs[$vals[csf("job_no")]]="'".$vals[csf("job_no")]."'";
	}
	$all_jobs_id=implode(",",$all_jobs );
 	$po_qnty_all_style=sql_select("SELECT sum(po_quantity) as qnty from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in ($all_jobs_id)");

 	$all_shipdates_arr=sql_select("SELECT min(b.pub_shipment_date)  as shipdate,job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 and b.job_no_mst in ($all_jobs_id) group by job_no");
 	$shipment_dates="";
 	foreach($all_shipdates_arr as $key=>$data)
 	{
 		if($shipment_dates=="")
 		{
 			$shipment_dates=change_date_format($data[csf("shipdate")]);
 		}
 		else
 		{
 			$shipment_dates .=','.change_date_format($data[csf("shipdate")]);
 		}
 	}


	$uom=0;
	$job_data_arr=array();
	$nameArray_buyer=sql_select( "SELECT  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix,a.total_set_qnty,a.product_dept,a.product_code,a.pro_sub_dep,a.gmts_item_id ,a.order_repeat_no,a.qlty_label  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0 and a.job_no='".$row_per_job[csf('job_no')]."'");
	foreach ($nameArray_buyer as $result_buy)
	{
		$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
		$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
		$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
		$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
		$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
		$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
		$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
		$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
		$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
		$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
		$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
		$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
	}
	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	$path=str_replace("'","",$path);
	if($path=="")
	{
	$path='../../';
	}elseif($image_cond==1){ $path='../../../';};
	ob_start();
	?>
	<style type="text/css">

		body, p, span, td, a {font-size:10pt;font-family: Arial;}
		body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}

	</style>
<?php ob_start();?>
	<div style="width:1330px" align="center" >

		<table id="tb_header" width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
			<thead>
				<tr>
					<td width="150" rowspan="4">
						<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' />
					</td>
					<td width="200">&nbsp;</td>
					<td width="400" align="left"><strong><?php echo $company_library[$cbo_company_name]; ?></strong></td>
					<td width="100">&nbsp;</td>
					<td width="300" align="left"><b>Booking No: </b><?php echo str_replace("'", "", $txt_booking_no); ?></td>
				</tr>

				<tr>
					<td width="200">&nbsp;</td>
					<td width="400" align="left">
						<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
						foreach ($nameArray as $result)
						{
							?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')]?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')];?>
							City No: <? echo $result[csf('city')];?>
							Zip Code: <? echo $result[csf('zip_code')]; ?>
							Province No: <?php echo $result[csf('province')]; ?>
							Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
							Email Address: <? echo $result[csf('email')];?>
							Website No: <? echo $result[csf('website')];
						}
							?>

					</td>
					<td width="100">&nbsp;</td>
					<td width="300" align="left"><b>Booking Date: </b><?php echo change_date_format($nameArray_per_job[0][csf("booking_date")]); ?></td>
				</tr>

				<tr>
					<td width="200">&nbsp;</td>
					<td width="400" align="left"><strong> Main Fabric Booking  </strong></td>
					<td width="100">&nbsp;</td>
					<td width="300" align="left"><b>Delivery Date: </b><?php echo change_date_format($nameArray_per_job[0][csf("delivery_date")]); ?></td>
				</tr>

				<tr>
					<td colspan="3" width="700">&nbsp;</td>
					<td width="300" align="left"><b>Approval Status: </b><?php if ($nameArray_per_job[0][csf("is_approved")] == 1) {echo "Approved";}?></td>
				</tr>
				<tr>
					<td colspan="4" height="10">&nbsp;</td>
				</tr>

				<tr>
					<td width="300"><b>To</b></td>

					<td width="400" align="left"><b>Buyer Name :   </b> <? echo $buyer_name_arr[$nameArray_per_job[0][csf("buyer_id")]]; ?></td>


				</tr>
				<tr>
					<td height="2">&nbsp;</td>
				</tr>

				<tr>
					<td width="300"><?
					$pay_mode=$nameArray_per_job[0][csf("pay_mode")];//pay_mode
					if($pay_mode!=3 && $pay_mode!=5)
					{
						echo $supplier_name_arr[$nameArray_per_job[0][csf("supplier_id")]];
					}
					else
					{
						echo $company_library[$nameArray_per_job[0][csf("supplier_id")]];
					}?></td>

					<td width="400" align="left"><b>Order Qnty :   </b> <? echo $po_qnty_all_style[0][csf("qnty")]; ?></td>


				</tr>
				<tr>
					<td height="2">&nbsp;</td>
				</tr>

				<tr>
					<td width="300" ><p><b>Address: </b><? echo $supplier_address_arr[$nameArray_per_job[0][csf("supplier_id")]]; ?> </p></td>

					<td width="400" align="left"><b>Dealing Merchant:   </b> <? echo $marchentrArr[$nameArray_per_job[0][csf("dealing_marchant")]]; ?></td>


				</tr>

				<tr>
					<td height="2">&nbsp;</td>
				</tr>

				<tr>
				   <td width="300"><b>Attention: </b>   <? echo $nameArray_per_job[0][csf("attention")];?></td>

				   <td width="400" align="left"><b>Currency:   </b> <? echo $currency[$nameArray_per_job[0][csf("currency_id")]]; ?></td>


				</tr>
				<tr>
					<td height="2">&nbsp;</td>
				</tr>

				<tr>
				   <td colspan="2" width="800"><b>Shipment Date: </b><? echo trim($shipment_dates);?></td>

				</tr>


				<tr>
					<td height="2">&nbsp;</td>
				</tr>

				<tr>
				   <td width="300"><b>WO Prepared After:   </b>    </td>

				   <td width="400" align="left"><b>Remarks:   </b> <? echo $nameArray_per_job[0][csf("remarks")]; ?></td>


				</tr>
			</thead>
		</table>


			<?php
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;

$po_data = array();
if ($db_type == 0) {
	$nameArray_job = sql_select("select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 and a.job_no in($all_jobs_id)  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
}
if ($db_type == 2) {
	$nameArray_job = sql_select("select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 and a.job_no in($all_jobs_id) group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
}
foreach ($nameArray_job as $result_job) {
	$po_data['po_id'][$result_job[csf('id')]] = $result_job[csf('id')];
	$po_data['po_number'][$result_job[csf('id')]] = $result_job[csf('po_number')];
	$po_data['leadtime'][$result_job[csf('id')]] = $result_job[csf('date_diff')];
	$po_data['po_quantity'][$result_job[csf('id')]] = $result_job[csf('po_quantity')];
	$po_data['po_received_date'][$result_job[csf('id')]] = change_date_format($result_job[csf('po_received_date')], 'dd-mm-yyyy', '-');
	$ddd = strtotime($result_job[csf('pub_shipment_date')]);
	$po_data['pub_shipment_date'][$ddd] = $ddd;

	$po_data['insert_date'][$result_job[csf('id')]] = $result_job[csf('insert_date')];

	if ($result_job[csf('shiping_status')] == 1) {
		$shiping_status = "FP";
	} else if ($result_job[csf('shiping_status')] == 2) {
		$shiping_status = "PS";
	} else if ($result_job[csf('shiping_status')] == 3) {
		$shiping_status = "FS";
	}
	$po_data['shiping_status'][$result_job[csf('id')]] = $shiping_status;
	$po_data['file_no'][$result_job[csf('id')]] = $result_job[csf('file_no')];
	$po_data['grouping'][$result_job[csf('id')]] = $result_job[csf('grouping')];
}
$txt_order_no_id = implode(",", array_unique($po_data['po_id']));
$leadtime = implode(",", array_unique($po_data['leadtime']));
$po_quantity = array_sum($po_data['po_quantity']);
$po_received_date = implode(",", array_unique($po_data['po_received_date']));
$po_number = implode(",", array_unique($po_data['po_number']));
$shipment_date = date('d-m-Y', min($po_data['pub_shipment_date']));
$maxshipment_date = date('d-m-Y', max($po_data['pub_shipment_date']));
//$shipment_date=implode(",",array_unique($po_data['pub_shipment_date']));
$shiping_status = implode(",", array_unique($po_data['shiping_status']));
$file_no = implode(",", array_unique($po_data['file_no']));
$grouping = implode(",", array_unique($po_data['grouping']));

$colar_excess_percent = 0;
$cuff_excess_percent = 0;
$rmg_process_breakdown = 0;
$nameArray = sql_select("select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition from wo_booking_mst a  where   a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0");
foreach ($nameArray as $result) {
	$total_set_qnty = $result[csf('total_set_qnty')];
	$colar_excess_percent = $result[csf('colar_excess_percent')];
	$cuff_excess_percent = $result[csf('cuff_excess_percent')];
	$rmg_process_breakdown = $result[csf('rmg_process_breakdown')];
	foreach ($po_data['po_id'] as $po_id => $po_val) {
		$daysInHand .= (datediff('d', date('d-m-Y', time()), $po_data['pub_shipment_date'][$po_id]) - 1) . ",";
		$booking_date = $result[csf('update_date')];
		if ($booking_date == "" || $booking_date == "0000-00-00 00:00:00") {
			$booking_date = $result[csf('insert_date')];
		}
		$WOPreparedAfter .= (datediff('d', $po_data['insert_date'][$po_id], $booking_date) - 1) . ",";
	}

}

?>
			<br/>
			<!--  Here will be the main portion  -->
			<?
			$costing_per="";
			$costing_per_qnty=0;
			$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($job_no_in)");
			if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;

			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;

			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;

			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;

			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;
			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no in($job_no_in)");
			$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");
			$p=1;
		/*foreach($uom_arr as $uom_id=>$uom_val)
		{ */
		if($cbo_fabric_source==1 or $cbo_fabric_source==2 or $cbo_fabric_source==3)
		{
			$nameArray_fabric_description= sql_select("SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , b.dia_width,d.pre_cost_remarks,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down,c.job_no_mst  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_number_id=d.gmts_color_id and
				b.dia_width=d.dia_width and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				a.job_no=d.job_no and
				a.id=d.pre_cost_fabric_cost_dtls_id
				and
				d.booking_no =$txt_booking_no and
				d.job_no in ($all_jobs_id) and

				d.status_active=1 and
				d.is_deleted=0 and
				b.cons>0
				group by a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,d.pre_cost_remarks,a.uom ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down,c.job_no_mst  order by c.job_no_mst   ");


			$uom_data_arr=array();
			foreach($nameArray_fabric_description as $row)
			{
				$uom_data_arr[$row[csf('uom')]]=$row[csf('uom')];
			}

			foreach($uom_data_arr as $uom_id=>$uom_val)
			{
				if(count($nameArray_fabric_description)>0)
				{

					?>

						<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
									<caption> <strong>Fabric Booking Details </strong> </caption>
									<tr>
										<th  width="30" align="center">SL</th>
										<th  width="150" align="center">Style/Season</th>
										<th  width="140" align="center">Gmts Item/Dept</th>
										<th  width="390" align="left">Item Description</th>
										<th  width="40" align="center">GSM</th>
										<th  width="90" align="center">Fabric Dia</th>
										<th  width="100" align="center">Color Type</th>
										<th  width="100" align="center">Gmts Color</th>
										<th  width="110" align="center">Fabric Color</th>
										<th  width="60" align="center">UOM</th>
										<th width='120' align="center">Finish Fab. Qty</th>
										<th width='120' align="center">Grey Fab. Qty</th>
										<th width='100' align="center">Avg Rate</th>
										<th width='60' align="center">Amount</th>

									</tr>
									<?
									$color_wise_process_loss=sql_select("SELECT  a.body_part_id,b.color_number_id,avg(b.process_loss_percent) as loss FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls  b 	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in ($all_jobs_id)   group by a.body_part_id,b.color_number_id
									");
									foreach($color_wise_process_loss as $val)
									{
										$loss_arr[$val[csf("body_part_id")]][$val[csf("color_number_id")]]=$val[csf("loss")];
									}

									$total_fin_fab_qnty=0;  $total_amount=0;  $total_grey_fab_qnty=0 ;$tot_avg=0;
									foreach($nameArray_fabric_description as $val)
									{
										if($val[csf('pre_cost_remarks')]!='') $remark_cond=" and d.pre_cost_remarks='".$val[csf('pre_cost_remarks')]."' "; else $remark_cond=" and d.pre_cost_remarks is null";
										$color_wise_wo_sql_qnty=sql_select("SELECT  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
											WHERE
											a.job_no=d.job_no and
											a.id=d.pre_cost_fabric_cost_dtls_id and
											d.booking_no =$txt_booking_no and
											d.job_no = '".$val[csf('job_no_mst')]."' and
											a.item_number_id='".$val[csf('item_number_id')]."' and
											a.body_part_id='".$val[csf('body_part_id')]."' and
											a.color_type_id='".$val[csf('color_type_id')]."' and
											a.construction='".$val[csf('construction')]."' and
											a.composition='".$val[csf('composition')]."' and
											a.gsm_weight='".$val[csf('gsm_weight')]."' and
											d.dia_width='".$val[csf('dia_width')]."' and

											d.fabric_color_id='".$val[csf('fabric_color_id')]."' and
											d.status_active=1 and
											d.is_deleted=0 $remark_cond
											");
										if($uom_id==$val[csf('uom')])
										{
											if($val[csf('pre_cost_remarks')]=='no remarks')  $pre_cost_remarks="";else  $pre_cost_remarks=$val[csf('pre_cost_remarks')];
											?>
											<tr>
												<td align="center"> <? echo $p; $p++;?></td>


												<td align="center"> <? echo $all_job_arr[$val[csf("job_no_mst")]]["style"];if($all_job_arr[$val[csf("job_no_mst")]]["season"]){echo ','.$season_arr[$all_job_arr[$val[csf("job_no_mst")]]["season"]];}?></td>
												<td align="center"> <? echo $garments_item[$all_job_arr[$val[csf("job_no_mst")]]["item"]].','.$product_dept[$all_job_arr[$val[csf("job_no_mst")]]["dept"]];?></td>
												<td align="left"> <? echo $body_part[$val[csf('body_part_id')]].','. $val[csf('construction')].','.$val[csf('composition')].', '.$pre_cost_remarks; ?> </td>

												<td  align="center">
													<?
													echo $val[csf('gsm_weight')];
													?>
												</td>

												<td  align="center">
													<?
													echo $val[csf('dia_width')];
													?>
												</td>

												<td  align="center">
													<?
													echo $color_type[$val[csf('color_type_id')]];
													?>
												</td>
												<td  align="center">
													<?
													if($val[csf('color_size_sensitive')]==1 or $val[csf('color_size_sensitive')]==2 or $val[csf('color_size_sensitive')]==4)
													{
														echo $color_library[$val[csf('fabric_color_id')]];
													}

													else
													{
														$color_break_down=$val[csf('color_break_down')];
														if($color_break_down)
														{
															$gmts_color="";
															if (strpos($color_break_down, '__') !== false)
															{
																$color_break_down=explode('__', $color_break_down);
																foreach ($color_break_down as $key => $value)
																{
																	$cols=explode('_', $value);
																	if(trim(strtolower($color_library[$val[csf('fabric_color_id')]]))==trim(strtolower($cols[2])))
																	{
																		if($gmts_color=="")
																		{
																			$gmts_color=$cols[1];
																		}
																		else
																		{
																			$gmts_color .=','.$cols[1];
																		}

																	}
																}
															}
															else
															{
																$cols=explode('_', $color_break_down);
																if(trim(strtolower($color_library[$val[csf('fabric_color_id')]]))==trim(strtolower($cols[2])))
																{
																	if($gmts_color=="")
																	{
																		$gmts_color=$cols[1];
																	}
																	else
																	{
																		$gmts_color .=','.$cols[1];
																	}

																}
															}

															echo $gmts_color;
														}

													}
													?>
												</td>


												<td  align="center">
													<?
													echo $color_library[$val[csf('fabric_color_id')]];
													?>
												</td>


												<td align="center"> <? echo $unit_of_measurement[$val[csf('uom')]]; ?></td>

												<td align="center"> <?   $greys= $color_wise_wo_sql_qnty[0][csf("grey_fab_qnty")] /(1+($loss_arr[$val[csf("body_part_id")]][$val[csf("fabric_color_id")]]/100)) ;
													echo def_number_format($greys,2);

													?> </td>



													<td align="center"> <? echo def_number_format($color_wise_wo_sql_qnty[0][csf("grey_fab_qnty")],2); ?> </td>
													<td align="center"> <? echo def_number_format($color_wise_wo_sql_qnty[0][csf("rate")],2); ?> </td>
													<td align="center"> <? echo def_number_format($color_wise_wo_sql_qnty[0][csf("amount")],2); ?> </td>


													<?

													$total_fin_fab_qnty +=str_replace(",", "", def_number_format($greys,2));
													$total_amount +=str_replace(",", "",def_number_format($color_wise_wo_sql_qnty[0][csf("amount")],2));
													$total_grey_fab_qnty +=str_replace(",", "",def_number_format($color_wise_wo_sql_qnty[0][csf("grey_fab_qnty")],2));
													$total_rate +=str_replace(",", "",def_number_format($color_wise_wo_sql_qnty[0][csf("rate")],2));
													$tot_avg=$total_amount/$total_grey_fab_qnty;


													?>



												</tr>
												<?
										}

									}
										?>
									<tr style=" font-weight:bold">


									<td  align="right" colspan="10"><strong>Total</strong></td>
									<td align="center"><? echo def_number_format($total_fin_fab_qnty,2);?></td>
									<td align="center"><? echo def_number_format($total_grey_fab_qnty,2);?></td>
									<td align="center"><? echo def_number_format($tot_avg,2);?></td>
									<td align="center"><? echo def_number_format($total_amount,2);?></td>

									</tr>

						</table>

					<?
				}
			}
		}


		$sql_data=sql_select("SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark, sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.adjust_qty)as adjust_qty FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in($all_jobs_id) and b.adjust_qty>0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark order by a.id,a.body_part_id");
		if(count($sql_data)>0)
		{

					?>
					<br/>
			<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

					<tr>
						<td colspan="7" align="center">
							<strong>Fabric Stock Adjustment Details</strong>
						</td>

					</tr>

					<tr>
						<td align="center"> SL </td>
						<td align="center"> Item Description </td>
						<td align="center"> Fabric Color </td>
						<td align="center">UOM</td>
						<td align="center">Adjusted Qty </td>


					</tr>
					<?
					$p=1;
					foreach($sql_data as $row)
					{
						?>
						<tr>
							<td align="center"><? echo $p;?></td>
							<td align="center">
								<? echo $body_part[$row[csf('body_part_id')]].",".$row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')].','.$fabric_typee[$row[csf('width_dia_type')]].",".$color_type[$row[csf('color_type_id')]] ?>
							</td>

							<td align="center">
								<? echo $color_library[$row[csf('fabric_color_id')]];  ?>
							</td>
							<td align="center">
								<? echo $unit_of_measurement[$row[csf('uom')]];  ?>
							</td>


							<td align="center">
								<? echo number_format($row[csf('adjust_qty')],4) ; ?>
							</td>

						</tr>
						<?
						$p++;
					}
		}
					?>
			</table>



	        <?
	       $sql_collar_cuff= "SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id,b.size_number_id,b.item_size,b.gmts_qty,b.excess_per,b.qty,b.po_break_down_id as po_id,b.id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id)  and b.status_active=1 and b.is_deleted=0  order by a.body_part_id,b.gmts_color_id,b.id " ;
	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
	          if(count($sql_data_collar_cuff)>0)
	          {

					?>
			  		<br>
			  		<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

			  			<tr>
			  				<td colspan="9" align="center">
			  					<strong>Collar and  Cuff Breakdown</strong>
			  				</td>

			  			</tr>

			  			<tr>
			  				<td align="center"> SL </td>
			  				<td align="left">   &nbsp;&nbsp;Item Description </td>
			  				<td align="center"> PO Number   </td>
			  				<td align="center"> Gmts Color  </td>
			  				<td align="center"> Gmts Size</td>
			  				<td align="center"> Item Size  </td>
			  				<td align="center"> Gmts Qty (Pcs)  </td>
			  				<td align="center"> Excess %   </td>
			  				<td align="center"> Collar & Cuff Qty (Pcs) </td>


			  			</tr>
			  			<?
			  			$p=1;
			  			foreach($sql_data_collar_cuff as $row)
			  			{
			  				?>
			  				<tr>
			  					<td align="center"><? echo $p;?></td>
			  					<td align="left"> &nbsp;
			  						<? echo $body_part[$row[csf('body_part_id')]].",".$row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]].",".$color_type[$row[csf('color_type_id')]] ?>
			  					</td>


			  					<td align="center">
			  						<? echo $po_num_arr[$row[csf("po_id")]] ;?>
			  					</td>


			  					<td align="center">
			  						<? echo $color_library[$row[csf('gmts_color_id')]];  ?>
			  					</td>
			  					<td align="center">
			  						<? echo $size_library[$row[csf('size_number_id')]];  ?>
			  					</td>


			  					<td align="center">
			  						<? echo $row[csf('item_size')];  ?>
			  					</td>

			  					<td align="right">
			  						<? echo $row[csf('gmts_qty')];  ?>
			  					</td>

			  					<td align="right">
			  						<? echo $row[csf('excess_per')];  ?>
			  					</td>

			  					<td align="right">
			  						<? echo $row[csf('qty')];  ?>
			  					</td>

			  				</tr>
			  				<?
			  				$p++;
			  			}
			  	}
			  		?>
			  	</table>
			<br>


			<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

				<tr>
					<td colspan="10" align="center">
						<strong>Comments</strong>
					</td>

				</tr>

				<tr>
					<td align="center"> SL </td>
					<td align="center"> PO NO </td>
					<td align="center"> Ship Date </td>
					<td align="center">BOM Qty</td>
					<td align="center"> Booking Qty </td>
					<td align="center"> Short Booking Qty </td>
					<td align="center"> Total Booking Qty </td>
					<td align="center"> Balance </td>
					<td align="center"> Comments </td>


				</tr>
				<?
				$is_short_data=sql_select("SELECT a.id, sum(b.grey_fab_qnty) as booking_qty from wo_po_break_down a,wo_booking_dtls b  where a.job_no_mst =b.job_no and  a.id=b.po_break_down_id   and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and b.is_short =1 group by  a.id ");
				foreach($is_short_data as $vals)
				{
					$short_qty_arr[$vals[csf("id")]]=$vals[csf("booking_qty")];
				}

				$booking_data=sql_select("SELECT a.id, sum(b.grey_fab_qnty) as booking_qty from wo_po_break_down a,wo_booking_dtls b  where a.job_no_mst =b.job_no and  a.id=b.po_break_down_id   and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.is_short !=1 group by  a.id  order by a.id");
				foreach($booking_data as $vals)
				{
					$booking_arr[$vals[csf("id")]]=$vals[csf("booking_qty")];
				}

				$po_date=return_library_array("select id,shipment_date from wo_po_break_down where status_active=1 and is_deleted=0",'id','shipment_date');

				$comments_data=sql_select("SELECT min(a.id) as ids,b.po_break_down_id as po_number,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,SUM(b.requirment) as precost_grey_qty FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
					WHERE a.job_no=b.job_no and
					a.id=b.pre_cost_fabric_cost_dtls_id
					and
					b.po_break_down_id=d.po_break_down_id and
					b.color_number_id=d.gmts_color_id and
					b.dia_width=d.dia_width and
					b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
					a.job_no=d.job_no and
					a.id=d.pre_cost_fabric_cost_dtls_id
					and
					d.booking_no =$txt_booking_no and
					d.job_no in($all_jobs_id) and

					d.status_active=1 and
					d.is_deleted=0 and
					b.cons>0
					group by b.po_break_down_id order by b.po_break_down_id");



				$job_no=$all_jobs_id;
				$condition= new condition();
				if(str_replace("'","",$job_no) !='')
				{
					$condition->job_no("in ($job_no)");
				}
				$condition->init();
				$fabric= new fabric($condition);
				$fabric_costing_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();

				$j=1;
				$total_bom=0;
				$total_book=0;
				$total_short=0;
				$total_short_full=0;
				$total_balance=0;
				foreach($comments_data as $val)
				{
					$po_id=$val[csf('po_number')];
					$woven_qty=array_sum($fabric_costing_qty_arr['woven']['grey'][$po_id]);
					$knit_qty=array_sum($fabric_costing_qty_arr['knit']['grey'][$po_id]);
					$sum_woven_knit=$woven_qty + $knit_qty;


					?>
					<tr>
						<td align="center"><? echo $j;?></td>

						<td align="center"> <? echo $po_num_arr[$val[csf("po_number")]] ;?> </td>
						<td align="center"> <? echo change_date_format($po_date[$val[csf("po_number")]], "yyyy-mm-dd", "-");?> </td>
						<td align="center"><?  echo $pre= def_number_format($sum_woven_knit,2);  ?> </td>
						<td align="center"><?  echo $bookings= def_number_format($booking_arr[$val[csf("po_number")]],2);  ?> </td>
						<td align="center"> <?echo $short=def_number_format($short_qty_arr[$val[csf("po_number")]],2); ?> </td>
						<td align="center"> <?   $tot_short_book= str_replace(',','',$bookings) +  str_replace(',','',$short) ; echo def_number_format($tot_short_book,2); ?>  </td>
						<td align="center"> <?  $bal =str_replace(',','',$pre)-str_replace(',','',$tot_short_book) ; echo def_number_format($bal,2);  ?> </td>
						<td align="center"> <? if($bal!=0){ if($pre>$tot_short_book){echo "Less ";} else{ echo "Over";} }?> </td>


					</tr>
					<?
					$total_bom +=str_replace(',','',$pre);
					$total_book +=str_replace(',','',$bookings);
					$total_short +=str_replace(',','',$short);
					$total_short_full += str_replace(',','',$tot_short_book);
					$total_balance += str_replace(',','',$bal);

					$j++;
				}
				?>
				<tr>
					<td colspan="3" align="right"> <b> Total </b></td>
					<td align="center"><strong><? echo def_number_format($total_bom,2);?> </strong> </td>
					<td align="center"><strong><? echo def_number_format($total_book,2);?> </strong> </td>
					<td align="center"><strong><? echo def_number_format($total_short,2);?> </strong> </td>
					<td align="center"><strong><? echo def_number_format($total_short_full,2);?> </strong> </td>
					<td align="center"><strong><? echo def_number_format($total_balance,2);?> </strong> </td>
					<td>&nbsp;</td>
				</tr>
			</table>



			<?
			if($cbo_fabric_source==1 || $cbo_fabric_source==2 || $cbo_fabric_source==3){
			?>
			<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
				<tr>
					<?
					$nameArray_item_size=sql_select( "SELECT min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.status_active =1 and d.is_deleted=0 and d.job_no in($all_jobs_id)  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
					if(count($nameArray_item_size)>0)
					{
						?>
						<td width="49%">

						</td>
						<td width="2%">
						</td>
						<?
					}
					?>
					<?
					$nameArray_item_size=sql_select( "SELECT min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.job_no in ($all_jobs_id)  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");

					if(count($nameArray_item_size)>0)
					{
						?>
						<td width="49%">

						</td>
						<?
					}
					?>
				</tr>
			</table>
			<br/>

			<br/>



	<?
	//------------------------------ Query for TNA start-----------------------------------
	$po_id_all=str_replace("'","",$txt_order_no_id);
	$po_num_arr=return_library_array("SELECT id,po_number from wo_po_break_down where job_no_mst in($all_jobs_id)",'id','po_number');
	$tna_start_sql=sql_select( "SELECT id,po_number_id,
	(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
	(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
	(case when task_number=60 then task_start_date else null end) as knitting_start_date,
	(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
	(case when task_number=61 then task_start_date else null end) as dying_start_date,
	(case when task_number=61 then task_finish_date else null end) as dying_end_date,
	(case when task_number=64 then task_start_date else null end) as finishing_start_date,
	(case when task_number=64 then task_finish_date else null end) as finishing_end_date,
	(case when task_number=84 then task_start_date else null end) as cutting_start_date,
	(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
	(case when task_number=86 then task_start_date else null end) as sewing_start_date,
	(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
	(case when task_number=110 then task_start_date else null end) as exfact_start_date,
	(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
	(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
	(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
	from tna_process_mst
	where status_active=1 and po_number_id in($po_id_all) and job_no in ($all_jobs_id) order by po_number_id");
 	$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
	$tna_date_task_arr=array();
	foreach($tna_start_sql as $row)
	{
		if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
		{
			if($tna_fab_start=="")
			{
				$tna_fab_start=$row[csf("fab_booking_start_date")];
			}
		}
		if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
		}
		if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
		}
		if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
		}
		if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
		}
		if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
		}
		if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
		}
		if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
		}
	}
	//------------------------------ Query for TNA end-----------------------------------
	if(count($tna_start_sql)>0 )
	{
		?>
		<br>
		<fieldset id="div_size_color_matrix" style="max-width:1000;">
			<legend>TNA Information</legend>
			<table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
				<tr>
					<td rowspan="2" align="center" valign="top">SL</td>
					<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
					<td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
					<td colspan="2" align="center" valign="top"><b>Knitting</b></td>
					<td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
					<td colspan="2" align="center" valign="top"><b>Finishing Fabric</b></td>
					<td colspan="2" align="center" valign="top"><b>Cutting </b></td>
					<td colspan="2" align="center" valign="top"><b>Sewing </b></td>
					<td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
				</tr>
				<tr>
					<td width="85" align="center" valign="top"><b>Start Date</b></td>
					<td width="85" align="center" valign="top"><b>End Date</b></td>
					<td width="85" align="center" valign="top"><b>Start Date</b></td>
					<td width="85" align="center" valign="top"><b>End Date</b></td>
					<td width="85" align="center" valign="top"><b>Start Date</b></td>
					<td width="85" align="center" valign="top"><b>End Date</b></td>
					<td width="85" align="center" valign="top"><b>Start Date</b></td>
					<td width="85" align="center" valign="top"><b>End Date</b></td>
					<td width="85" align="center" valign="top"><b>Start Date</b></td>
					<td width="85" align="center" valign="top"><b>End Date</b></td>
					<td width="85" align="center" valign="top"><b>Start Date</b></td>
					<td width="85" align="center" valign="top"><b>End Date</b></td>
					<td width="85" align="center" valign="top"><b>Start Date</b></td>
					<td width="85" align="center" valign="top"><b>End Date</b></td>
				</tr>
				<?
				$i=1;
				foreach($tna_date_task_arr as $order_id=>$row)
				{
					?>
					<tr>
						<td><? echo $i; ?></td>
						<td><? echo $po_num_arr[$order_id]; ?></td>
						<td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
						<td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
						<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
						<td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
						<td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
						<td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
						<td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
						<td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
						<td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
						<td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
						<td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
						<td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
						<td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
						<td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</fieldset>
		<?
	}
	}// fabric Source End
	?>
	<br>

	<table width="220"  border="0" cellpadding="2" cellspacing="0" rules="all" style="float:left">
		<tr>
			<td width="200" valign="top" align="left">

				<div id="div_size_color_matrix" style="float:left;">
					<?
					$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
					?>
					<fieldset>
						<legend>RMG Process Loss % </legend>
						<table width="180" class="rpt_table" border="1" rules="all">
							<?
							if(number_format($rmg_process_breakdown_arr[8],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[8],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[2],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Chest Printing <!-- Printing % breack Down 2-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[2],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[10],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Neck/Sleeve Printing <!-- New breack Down 10-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[10],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[1],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Embroidery   <!-- Embroidery  % breack Down 1-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[1],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[4],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Sewing /Input<!-- Sewing % breack Down 4-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[4],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[3],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Garments Wash <!-- Washing %breack Down 3-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[3],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[15],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Gmts Finishing <!-- Washing %breack Down 3-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[15],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[11],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Others <!-- New breack Down 11-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[11],2);
										?>
									</td>
								</tr>
								<?
							}
							$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
							if($gmts_pro_sub_tot>0)
							{
								?>
								<tr>
									<td width="130">
										Sub Total <!-- New breack Down 11-->
									</td>
									<td align="right">
										<?

										echo number_format($gmts_pro_sub_tot,2);
										?>
									</td>
								</tr>
								<?
							}
							?>
						</table>
					</fieldset>


					<fieldset>
						<legend>Fabric Process Loss % </legend>
						<table width="180" class="rpt_table" border="1" rules="all">
							<?
							if(number_format($rmg_process_breakdown_arr[6],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Knitting  <!--  Knitting % breack Down 6-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[6],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[12],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Yarn Dyeing  <!--  New breack Down 12-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[12],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[5],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Dyeing & Finishing  <!-- Finishing % breack Down 5-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[5],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[13],2)>0)
							{
								?>
								<tr>
									<td width="130">
										All Over Print <!-- new  breack Down 13-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[13],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[14],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Lay Wash (Fabric) <!-- new  breack Down 14-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[14],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[7],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Dying   <!-- breack Down 7-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[7],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[0],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Cutting (Fabric) <!-- Cutting % breack Down 0-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[0],2);
										?>
									</td>
								</tr>
								<?
							}
							if(number_format($rmg_process_breakdown_arr[9],2)>0)
							{
								?>
								<tr>
									<td width="130">
										Others  <!-- Others% breack Down 9-->
									</td>
									<td align="right">
										<?
										echo number_format($rmg_process_breakdown_arr[9],2);
										?>
									</td>
								</tr>
								<?
							}
							$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
							if($fab_proce_sub_tot>0)
							{
								?>
								<tr>
									<td width="130">
										Sub Total  <!-- Others% breack Down 9-->
									</td>
									<td align="right">
										<?

										echo number_format($fab_proce_sub_tot,2);
										?>
									</td>
								</tr>
								<?
							}
							if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
							{
								?>
								<tr>
									<td width="130">
										Grand Total  <!-- Others% breack Down 9-->
									</td>
									<td align="right">
										<?
										echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
										?>
									</td>
								</tr>
								<?
							}
							?>
						</table>
					</fieldset>
				</div>
			</td>
		</tr>
	</table>

	<table width="400"  border="0" cellpadding="2" cellspacing="0" rules="all" style="float:left">
		<tr>
			<td colspan="6" align="left"><img  src='<? echo $path.$imge_arr[$job_no]; ?>' height='155' width='200' /></td>
		</tr>
	</table>
	<br>
	<br>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
				<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
						</tr>
					</thead>
					<tbody>
						<?
						$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");
						if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$i++;
								?>
								<tr id="settr_1" valign="top">
									<td style="vertical-align:top">
										<? echo $i;?>
									</td>
									<td>
										<strong style="font-size:16px"> <? echo $row[csf('terms')]; ?></strong>
									</td>
								</tr>
								<?
							}
						}
						?>
					</tbody>
				</table>
			</td>

		</tr>
	</table>



		    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
		    	<tr>
		            <td>  <?
		                    echo signature_table(121, $cbo_company_name, "1330px",1,'');
							$job_no_all= implode(",",array_unique($joball['job_no']));
							$style_sting_all=implode(",",array_unique($joball['style_ref_no']));
							echo "****".custom_file_name($txt_booking_no,$style_sting_all,$job_no_all);
		                ?>
		            </td>
		        </tr>
		    </table>
	</div>

	<?
$emailBody=ob_get_contents();
//ob_clean();
if($is_mail_send==1){
	$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
	$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
	if($req_approved && $is_approved==1){
		$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
	}
	elseif($req_approved && $is_approved==0){
		$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
	}
	

	$mailArr=array();
	if($mail_id!=''){$mailArr[]=$mail_id;}
	if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
	
	
	$to=implode(',',$mailArr);
	$subject="Partial Fabric Booking Auto Mail";
	
	if($to!=""){
		require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
		require_once('../../../auto_mail/setting/mail_setting.php');
		$header=mailHeader();
		sendMailMailer( $to, $subject, $emailBody );
	}
}









	exit();

}

  if($action=="print_booking_6") //Rehan for chaity
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$path=str_replace("'","",$path);
	if($path!="") $path=$path;elseif($image_cond==1) $path="../../../"; else $path="../../";

	$imge_arr=return_library_array( "SELECT master_tble_id,image_location from   common_photo_library where form_name='company_details' or form_name='knit_order_entry' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "SELECT id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$color_library=return_library_array( "SELECT id,color_name from lib_color ", "id", "color_name");
	$supplier_name_arr=return_library_array( "SELECT id,supplier_name from   lib_supplier  where status_active=1 and is_deleted=0",'id','supplier_name');
	$supplier_address_arr=return_library_array( "SELECT id,address_1 from   lib_supplier  where status_active=1 and is_deleted=0",'id','address_1');
	$marchentrArr = return_library_array("SELECT id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$buyer_name_arr=return_library_array( "SELECT id,buyer_name from lib_buyer  where status_active=1 and is_deleted=0",'id','buyer_name');


	$nameArray_approved=sql_select( "SELECT max(b.approved_no) as approved_no,a.is_approved, count(b.id) as revised_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 group by a.is_approved");
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no,a.is_approved, count(b.id) as revised_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 group by a.is_approved");
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_comments_row)=$nameArray_approved_comments;



	$job_po_arr=array();
	$ref_no='';$job_no_aarr='';
	$nameArray_per_job=sql_select( "SELECT  a.job_no,a.style_ref_no,b.po_break_down_id,c.po_number,c.grouping  from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c where c.id=b.po_break_down_id and a.job_no=b.job_no and  a.job_no=c.job_no_mst and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  group by a.job_no,a.style_ref_no,b.po_break_down_id,c.grouping,c.po_number");
	foreach ($nameArray_per_job as $row_per_job)
	{
		$job_no_aarr.="'".$row_per_job[csf('job_no')]."'".',';
		$all_po_id_arr[$row_per_job[csf('po_break_down_id')]]=$row_per_job[csf('po_break_down_id')];
		$job_po_arr[$row_per_job[csf('job_no')]].=$row_per_job[csf('po_number')].',';
		if($ref_no=='') $ref_no=$row_per_job[csf('grouping')]; else $ref_no.=",".$row_per_job[csf('grouping')];

	}

	$job_nos=rtrim($job_no_aarr,',');
	$txt_order_no_id=implode(",",$all_po_id_arr);
	$job_nos=implode(",",array_unique(explode(",",$job_nos)));
	$ref_nos=implode(",",array_unique(explode(",",$ref_no)));
	$job_data_arr=array();
	$nameArray_buyer=sql_select( "SELECT  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix,a.total_set_qnty,a.product_dept,a.product_code,a.pro_sub_dep,a.gmts_item_id ,a.order_repeat_no,a.qlty_label  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0 and a.job_no in(".$job_nos.") order by a.job_no ");
	foreach ($nameArray_buyer as $result_buy)
	{
		$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
		$dealing_marchant.=$marchentrArr[$result_buy[csf('dealing_marchant')]].',';
	}

	$dealing_marchant=rtrim($dealing_marchant,',');
	$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchant)));
 	$nameArray=sql_select( "SELECT a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.fabric_source,a.remarks,a.pay_mode,a.fabric_composition,a.booking_percent from wo_booking_mst a   where  a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0");
 	foreach ($nameArray as $result_job)
 	{
 		$booking_date=$result_job[csf('booking_date')];
 		$buyer_id=$result_job[csf('buyer_id')];
 		$currency_id=$result_job[csf('currency_id')];
 		$attention=$result_job[csf('attention')];
 		$delivery_date=$result_job[csf('delivery_date')];
 		$supplier_id=$result_job[csf('supplier_id')];
 		$remarks=$result_job[csf('remarks')];
 		$pay_mode=$result_job[csf('pay_mode')];
 		$booking_percent=$result_job[csf('booking_percent')];
 	}

 	$lapdip_no_sql=sql_select("SELECT job_no_mst, lapdip_no,color_name_id from wo_po_lapdip_approval_info where  job_no_mst in ($job_nos) and status_active = 1 and approval_status = 3 ");
 	foreach($lapdip_no_sql as $key=>$vals)
 	{
 		$lapdip_no_arr[$vals[csf("job_no_mst")]][$vals[csf("color_name_id")]]=$vals[csf("lapdip_no")];
 	}


	?>
	<style>

		@media print
		{
			.page-break { height:0; page-break-before:always; margin:0; border-top:none; }
		}
		body, p, span, td, a {font-size:10pt;font-family: Arial;}
		body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}
	</style>
	<?php ob_start();?>
  <div style="width:1310px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
			<tr>
				<td width="100">
					<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
				</td>
				<td width="1250">
					<table width="100%" cellpadding="0" cellspacing="0"  border="0" >
						<tr>
							<td align="center">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
							<td rowspan="3" width="250">
								<span><b> Booking No:&nbsp;&nbsp;<? echo trim($txt_booking_no,"'"); ?></b></span><br/>
								<span><b> Booking Date :&nbsp;&nbsp;<? echo change_date_format($booking_date); ?></b></span><br/>
								 <?
								if($nameArray_approved_row[csf('approved_no')]==1 && $nameArray_approved_row[csf('is_approved')]==0){
									?>
                                    <b> Revised No :  <? echo $nameArray_approved_row[csf('revised_no')]; ?></b>
                                      <br/>
                                      Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                                    <?

								}
								 if($nameArray_approved_row[csf('approved_no')]>1 && $nameArray_approved_row[csf('is_approved')]==0)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('revised_no')];?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                                <?
                                if($nameArray_approved_row[csf('approved_no')]>1 && $nameArray_approved_row[csf('is_approved')]==1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('revised_no')]-1;?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
							</td>
						</tr>
						<tr>
							<td align="center">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");

								if($txt_job_no!="")
								{
									$location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
								}
								else
								{
									$location="";
								}
								foreach ($nameArray as $result)
								{
									$email=$result[csf('email')];
									$city=$result[csf('city')];

									?>
									Email Address: <? echo $email;?>
									Website: <? echo $result[csf('website')]; ?>
									<?
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="center">
								<strong><? if($report_title !=""){ echo $report_title.'-'.$fabric_source[$cbo_fabric_source];}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" style="border:0px solid black;table-layout: fixed;" >
			<tr>
				<td width="200"><span><b>To </b></span></td>
				<td width="280">&nbsp;<span></span></td>
				<td width="200"><span><b>Buyer</b></span></td>
				<td width="230"><span> :&nbsp;<b><? echo $buyer_name_arr[$buyer_id]; ?></b></span></td>
			</tr>
			<tr>
				<td width="200"><b>Supplier Name</b>   </td>
				<td width="280">:&nbsp;
					<?
					if($pay_mode==5 || $pay_mode==3){
						echo $company_library[$supplier_id];
						$suplier_address=$city.','.$email;
					}
					else{
						echo $supplier_name_arr[$supplier_id];
						$suplier_address=$supplier_address_arr[$supplier_id];
					}
					?>    </td>
					<td width="200"><b>Dealing Marchant</b></td>
					<td width="" colspan="2">:&nbsp;<? echo $dealing_marchants;?></td>
			</tr>
			<tr>
				<td width="200"><b>Address</b></td>
				<td width="280">:&nbsp;<? echo $suplier_address; ?></td>
				<td width="200"><b>Currency </b>   </td>
				<td width="230">:&nbsp;
					<?
					echo $currency[$currency_id];
					?>
				</td>
			</tr>
			<tr>
				<td width="200"><b>Attention</b></td>
				<td  width="280">:&nbsp;<? echo $attention; ?></td>
				<td width="200"><b>Internal Ref. No </b>   </td>
				<td colspan="2">:&nbsp;
					<?
					echo $ref_nos;
					?>
				</td>
			</tr>
			<tr>
				<td width="200"><b>Delivery Date</b></td>
				<td width="280">:&nbsp;<? echo change_date_format($delivery_date); ?></td>
				<td><b></b></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="200"><b>Remark</b></td>
				<td colspan="4"> :<? echo $remarks;?></td>
			</tr>
	    </table>
	    <?

	    $color_wise_process_loss=sql_select("SELECT  a.job_no,a.body_part_id,b.color_number_id,a.process_loss_method,b.process_loss_percent as loss FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls  b 	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in(".$job_nos.") and b.process_loss_percent>0 group by a.job_no,a.body_part_id,a.process_loss_method,b.color_number_id,b.process_loss_percent");
	    foreach($color_wise_process_loss as $val)
	    {
	    	$loss_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss']=$val[csf("loss")];
	    	$loss_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss_method']=$val[csf("process_loss_method")];
	    }


	    $sql_booking="SELECT  a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id,a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no,c.job_no_prefix_num,d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color
	    FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls d,wo_po_details_master c
	    WHERE   a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id  and a.job_no=c.job_no  and d.job_no=c.job_no
	    and d.booking_no =$txt_booking_no and d.job_no in(".$job_nos.") and d.status_active=1 and d.is_deleted=0
	    group by  a.job_no,a.id, a.body_part_id,a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num  order by a.job_no,d.fabric_color_id ";
	    $result_set=sql_select($sql_booking);
	    foreach( $result_set as $row)
	    {
	    	$body_part_id=$body_part[$row[csf("body_part_id")]];
	    	$uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
	    	$construction=$row[csf("construction")];
	    	$compositions= $row[csf("composition")];
	    	$item_desc=$body_part_id.','.$row[csf("construction")].','.$compositions;


	    	$process_loss=$loss_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['loss'];
	    	$process_loss_method=$loss_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['loss_method'];

	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['uom']=$row[csf("uom")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['style_ref_no']=$row[csf("style_ref_no")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['job_prefix']=$row[csf("job_no_prefix_num")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['full_job']=$row[csf("job_no")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['style_ref_no']=$row[csf("style_ref_no")];

	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['fin_qty']=$row[csf("fin_fab_qntys")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['grey_qty']=$row[csf("grey_fab_qntys")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['amounts']=$row[csf("amounts")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['rates']=$row[csf("rates")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['p_loss']=$process_loss;
	    	$fabric_detail_arr[$row[csf("job_no")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("c_type")]][$row[csf("gmt_color")]][$row[csf("fab_color")]]['p_loss_method']=$process_loss_method;
	    }
	    $fab_row_span_arr=array();
	    foreach($fabric_detail_arr as $job_key=>$job_data)
	    {
	    	$desc_rowspan=0;
	    	foreach($job_data as $desc_key=>$desc_data)
	    	{
	    		foreach($desc_data as $gsm_key=>$gsm_data)
	    		{
	    			foreach($gsm_data as $dia_key=>$dia_data)
	    			{
	    				foreach($dia_data as $c_type_key=>$color_data)
	    				{
	    					foreach($color_data as $gmt_color_key=>$gmt_color_data)
	    					{
	    						foreach($gmt_color_data as $fab_color_key=>$val)
	    						{
	    							$desc_rowspan++;
	    						}
	    						$fab_row_span_arr[$job_key]=$desc_rowspan;
	    					}
	    				}
	    			}
	    		}
	    	}
	    }
	foreach($uom_data_arr as $uom_id=>$uom_val)
	{
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >

			<tr>
				<th  width="30" align="center" style="word-break: break-all;word-wrap: break-word;">SL</th>
				<th  width="70" align="center" style="word-break: break-all;word-wrap: break-word;">Job No</th>
				<th  width="120" align="center" style="word-break: break-all;word-wrap: break-word;">Style Ref</th>
				<th  width="120" align="center" style="word-break: break-all;word-wrap: break-word;">Order No</th>
				<th  width="300" align="center" style="word-break: break-all;word-wrap: break-word;">Item Description</th>
				<th  width="50" align="center" style="word-break: break-all;word-wrap: break-word;">GSM</th>
				<th  width="60" align="center" style="word-break: break-all;word-wrap: break-word;">Fabric Dia</th>
				<th  width="80" align="center" style="word-break: break-all;word-wrap: break-word;">Color Type</th>
				<th  width="100" align="center" style="word-break: break-all;word-wrap: break-word;">Gmts Color</th>
				<th  width="100" align="center" style="word-break: break-all;word-wrap: break-word;">Fabric Color</th>
				<th  width="120" align="center" style="word-break: break-all;word-wrap: break-word;">Lab Dip No</th>
				<th  width="50" align="center" style="word-break: break-all;word-wrap: break-word;">UOM</th>
				<th width='100' align="center" style="word-break: break-all;word-wrap: break-word;">Finish Fab. Qty</th>
				<th width='100' align="center" style="word-break: break-all;word-wrap: break-word;">Grey Fab. Qty</th>
				<th width='80' align="center" style="word-break: break-all;word-wrap: break-word;">Avg Rate</th>
				<th width='100' align="center" style="word-break: break-all;word-wrap: break-word;">Amount</th>
			</tr>
			<?
 			$k=$p=1;$total_fin_qty=$total_grey_qty=$total_amount=0;


			foreach($fabric_detail_arr as $job_key=>$job_data)
			{
				$y=1;
				foreach($job_data as $desc_key=>$desc_data)
				{
					foreach($desc_data as $gsm_key=>$gsm_data)
					{
						foreach($gsm_data as $dia_key=>$dia_data)
						{
							foreach($dia_data as $c_type_key=>$color_data)
							{
								foreach($color_data as $gmt_color_key=>$gmt_color_data)
								{
									foreach($gmt_color_data as $fab_color_key=>$val)
									{
										$po_nos=rtrim($job_po_arr[$job_key],',');
										$po_nos=implode(",",array_unique(explode(",",$po_nos)));
										$fab_row_span=$fab_row_span_arr[$job_key];
										$p_loss_method=$val['p_loss_method'];
										$process_loss=$val['p_loss'];
										$labtest_no=$lab_dip_arr[$job_key]['labtest_no'];
										if($process_loss) $process_loss=$process_loss;else $process_loss=0;
		 								if($p_loss_method==1) //markup
										{

											$fin_qty=$val['fin_qty']-(($val['fin_qty']*$process_loss)/(100+$process_loss));
										}
										else if($p_loss_method==2) //margin
										{
											$fin_qty=$val['fin_qty']-(($val['fin_qty']*$process_loss)/100);
										}
										if($uom_id==$val['uom'])
										{
											?>
											<tr>
												<?
												if($y==1)
												{
													?>
													<td style="word-break: break-all;word-wrap: break-word;" width="30"  rowspan="<? echo $fab_row_span;?>"><? echo $p; ?></td>
													<td style="word-break: break-all;word-wrap: break-word;" width="70" align="center" rowspan="<? echo $fab_row_span;?>"><p><? echo $val['job_prefix']; ?>&nbsp;</p></td>
													<td style="word-break: break-all;word-wrap: break-word;" width="120" rowspan="<? echo $fab_row_span;?>"><p><? echo $val['style_ref_no']; ?>&nbsp;</p></td>
													<td style="word-break: break-all;word-wrap: break-word;" width="120" rowspan="<? echo $fab_row_span;?>">&nbsp;<p><? echo $po_nos; ?>&nbsp;</p></td>
													<?
												}
												?>
												<td style="word-break: break-all;word-wrap: break-word;" width="300"><p><? echo $desc_key; ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="50"><p><? echo $gsm_key; ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="60"><p><? echo $dia_key; ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="80"><p><? echo $color_type[$c_type_key]; ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="100"><p><? echo $color_library[$gmt_color_key]; ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="100"><p><? echo $color_library[$fab_color_key]; ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="120"><p><? echo $lapdip_no_arr[$val['full_job']][$fab_color_key]; ?>&nbsp;</p></td>
												<td  style="word-break: break-all;word-wrap: break-word;"width="50"><p><? echo $unit_of_measurement[$val['uom']]; ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="100" align="right" title="(Markup/Margin Method) Process Loss=<? echo $process_loss;?>,Fin Qty=<? echo $val['fin_qty'];?>"><p><? echo number_format($fin_qty,2); ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="100" align="right"><p><? echo number_format($val['grey_qty'],2); ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;" width="80" align="right"><p><? echo number_format($val['rates'],2); ?>&nbsp;</p></td>
												<td style="word-break: break-all;word-wrap: break-word;"width="100" align="right"><p><? echo number_format($val['amounts'],2); ?>&nbsp;</p></td>
											</tr>
											<?
											$k++;$y++;
											$total_fin_qty+=$fin_qty;
											$total_grey_qty+=$val['grey_qty'];
											$total_amount+=$val['amounts'];
										}
							        }
						        }
					        }
				        }
			        }
		        }
				$p++;
	        }
			?>
			<tfoot>
				<tr>
					<th colspan="12" align="right"> Total </th>
					<th align="right"> <? echo number_format($total_fin_qty,2); ?> </th>
					<th align="right"> <? echo number_format($total_grey_qty,2); ?> </th>
					<th align="right"> <? //echo number_format($total_fin_qty,2); ?> </th>
					<th align="right"> <? echo number_format($total_amount,2); ?> </th>
				</tr>
			</tfoot>
		</table>
		<?
    }

	?>
	<br>




		<?
		    $size_lib_arr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		    $color_lib_arr = return_library_array("select id,color_name from  lib_color ","id","color_name");
			$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "SELECT job_no_mst, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in ($txt_order_no_id) and status_active=1 and is_deleted =0 group by job_no_mst,color_number_id, size_number_id");
 			$is_collar_cuff_exists=sql_select("SELECT job_no from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 and body_part_id in(2,3) and job_no in ($job_nos) ");
 			foreach($is_collar_cuff_exists as $k=>$v)
 			{
 				$job_wise_collar_cuff[$v[csf("job_no")]]=$v[csf("job_no")];
 			}

 			if(count($is_collar_cuff_exists)>0)
 			{
 				foreach($color_wise_wo_sql_qnty as $row)
 				{
 					if($job_wise_collar_cuff[$row[csf("job_no_mst")]])
 					{
 						$order_plan_qty_arr[$row[csf('job_no_mst')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
 						$order_plan_qty_arr[$row[csf('job_no_mst')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
 						$size_number_id_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
 						$job_no_mst_arr[$row[csf('job_no_mst')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
 						$job_no_sizetotal_arr[$row[csf('job_no_mst')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
 						$job_wise_colortotal[$row[csf('job_no_mst')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
 					}

 				}
 			}


			foreach( sql_select($collar_sql_dtls) as $key=>$vals)
			{
				$job_wise_size_collar[$vals[csf("job_no")]][$vals[csf("size_number_id")]]=$vals[csf("qty")];
			}

			foreach($order_plan_qty_arr as $job_no=>$color_number_data)
			{
				$m=0;
				foreach($color_number_data as $color_number_id=>$size_number_data)
				{
					$m++;
					foreach($size_number_data as $size_number_id=>$data)
					{


					}
				}

					$job_wise_span[$job_no]=$m+2;


			}
			$head_col_span=count($size_number_id_arr)+3;



            $colar_percent_size_wise_array=array();
            $colar_percent_size_wise_sql=sql_select( "select a.colar_cuff_per,b.color_number_id,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_booking_no and a.booking_type=1 and c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0");
            $colar_excess_percent_arr=array();
            foreach($colar_percent_size_wise_sql as $colar_percent_size_wise_row)
            {
            	$colar_percent_size_wise_array[$colar_percent_size_wise_row[csf('color_number_id')]][$colar_percent_size_wise_row[csf('gmts_sizes')]]=$colar_percent_size_wise_row[csf('colar_cuff_per')];

            }

            $nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id,c.size_order FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id,c.size_order order by  c.size_order,id");


            if(count($job_no_mst_arr)>0)
            {


	            ?>
	            <div style="float: left;width: 450px;">

	            	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		            	<tr>
		            		<td><strong>Job No</strong> </td>
		            		<td colspan="<? echo $head_col_span; ?>"> <strong>Collar-Color Size Breakdown in Pcs.</strong></td>
		            	</tr>
		            	<?
		            		$kk=0;
		            		$nn=0;
			            	foreach($job_no_mst_arr as $job_no=>$color_data)
						    {
						    	if($kk==0)
						    		{


							    		?>
								    	<tr>

								    		<td rowspan="<? echo $job_wise_span[$job_no]; ?>"><? echo $job_no;?></td>
								    		<td><strong> Size </strong></td>
								    		 <?
								    		 foreach($size_number_id_arr as $key=>$val)
								    		 {
								    		 	?>
								    		 		<td> <strong><? echo $size_lib_arr[$val]; ?></strong> </td>
								    		 	<?
								    		 }
								    		 ?>
								    		 <td rowspan="2"> <strong> Total</strong></td>
								    		 <td rowspan="2"><strong>Extra %</strong></td>
								    	</tr>
								    	<?
								    }
								$pp=0;

						    	foreach($color_data as $color_number_id=>$data)
						    	{


								    if($pp==0)
								    {


								    	?>
								    	<tr>


								    		<td><strong>Collar Size</strong> </td>
								    		 <?
								    		 foreach($size_number_id_arr as $key=>$val)
								    		 {
								    		 	?>
								    		 		<td> <? echo $job_wise_size_collar[$job_no][$key]; ?> </td>
								    		 	<?
								    		 }
								    		 ?>

								    	</tr>
								    	<?
								    }
								    $pp++;
								    	?>

						    	<tr>


						    		<td><? echo $color_lib_arr[$data]; ?></td>
						    		 <?
						    		 foreach($size_number_id_arr as $key=>$val)
						    		 {
						    		 	?>
						    		 		<td> <? echo $order_plan_qty_arr[$job_no][$color_number_id][$key]["order"]; ?> </td>
						    		 	<?
						    		 }
						    		 ?>
						    		 <td> <? echo $job_wise_colortotal[$job_no][$color_number_id]; ?> </td>
						    		 <td> <? echo $job_wise_colortotal[$job_no][$color_number_id]["extra"]; ?> </td>
						    	</tr>


						    	<?


						    	}

						    	?>
						    	<tr>
						    	<td colspan="2" align="right"><strong>Size Total</strong></td>
						    	<?
						    		foreach($size_number_id_arr as $key=>$val)
						    		 {
						    		 	?>
						    		 		<td> <? echo $job_no_sizetotal_arr[$job_no][$key]; ?> </td>
						    		 	<?
						    		 }
						    	?>

						    	</tr>

						    	<?


			 			    }
		            	?>
		            	<tr>

		            	</tr>

	            	</table>
	            </div>
	            <div style="float: left;width: 10px;" >&nbsp;</div>
	            <div style="float: left;width: 450px;">
	            	<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		            	<tr>
		            		<td><strong>Job No</strong> </td>
		            		<td colspan="<? echo $head_col_span; ?>"> <strong>Cuff-Color Size Breakdown in Pcs.</strong></td>
		            	</tr>
		            	<?
		            		$kk=0;
		            		$nn=0;
			            	foreach($job_no_mst_arr as $job_no=>$color_data)
						    {
						    	if($kk==0)
						    		{


							    		?>
								    	<tr>

								    		<td rowspan="<? echo $job_wise_span[$job_no]; ?>"><? echo $job_no;?></td>
								    		<td><strong> Size </strong></td>
								    		 <?
								    		 foreach($size_number_id_arr as $key=>$val)
								    		 {
								    		 	?>
								    		 		<td> <strong><? echo $size_lib_arr[$val]; ?></strong> </td>
								    		 	<?
								    		 }
								    		 ?>
								    		 <td rowspan="2"><strong>Total</strong></td>
								    		 <td rowspan="2"><strong>Extra %</strong></td>
								    	</tr>
								    	<?
								    }
								$pp=0;

						    	foreach($color_data as $color_number_id=>$data)
						    	{


								    if($pp==0)
								    {


								    	?>
								    	<tr>


								    		<td><strong>Collar Size</strong> </td>
								    		 <?
								    		 foreach($size_number_id_arr as $key=>$val)
								    		 {
								    		 	?>
								    		 		<td> <? echo $job_wise_size_collar[$job_no][$key]; ?> </td>
								    		 	<?
								    		 }
								    		 ?>

								    	</tr>
								    	<?
								    }
								    $pp++;
								    	?>

						    	<tr>


						    		<td><? echo $color_lib_arr[$data]; ?></td>
						    		 <?
						    		 foreach($size_number_id_arr as $key=>$val)
						    		 {
						    		 	?>
						    		 		<td> <? echo $order_plan_qty_arr[$job_no][$color_number_id][$key]["order"]; ?> </td>
						    		 	<?
						    		 }
						    		 ?>
						    		 <td> <? echo $job_wise_colortotal[$job_no][$color_number_id]; ?> </td>
						    		 <td> <? echo $job_wise_colortotal[$job_no][$color_number_id]["extra"]; ?> </td>
						    	</tr>


						    	<?


						    	}

						    	?>
						    	<tr>
						    	<td colspan="2" align="right"><strong>Size Total</strong></td>
						    	<?
						    		foreach($size_number_id_arr as $key=>$val)
						    		 {
						    		 	?>
						    		 		<td> <? echo $job_no_sizetotal_arr[$job_no][$key]; ?> </td>
						    		 	<?
						    		 }
						    	?>

						    	</tr>

						    	<?


			 			    }
		            	?>
		            	<tr>

		            	</tr>

	            	</table>
	            </div>
	            <br>
	            <br>

            	<?
       		}

	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !='')
	{
		$condition->po_id("in($txt_order_no_id)");
	}

	$condition->init();
	$cos_per_arr=$condition->getCostingPerArr();
	$yarn= new yarn($condition);
	$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
 	$yarn_count_arr=return_library_array( "SELECT id,yarn_count from lib_yarn_count",'id','yarn_count');
 	$po_qnty_tot=return_field_value("sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");

 	if($db_type==0)
 	{
 		$job_listagg=" group_concat(a.job_no) as job_no";
 	}
 	else
 	{

 		$job_listagg=" listagg(cast(a.job_no as varchar2(4000)),',') within group (order by a.job_no) as job_no";
 	}
	$yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, a.rate,$job_listagg    from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no in ($job_nos) and b.booking_no=$txt_booking_no  and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one,a.color,a.type_id,a.rate ");


	?>
	<br/>
	<br/>
	<br/>
	<table style="margin-top: 10px;" class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >

		<tr align="center">
             <td colspan="7"><b>Yarn Required Summary (Pre Cost) </b></td>
        </tr>

        <tr align="center">
        	<td width="25" style="word-wrap: break-word;word-break: break-all;">Sl</td>

        	<td width="400" style="word-wrap: break-word;word-break: break-all;">Yarn Description</td>
        	<td width="60" style="word-wrap: break-word;word-break: break-all;">Brand</td>
        	<td width="60" style="word-wrap: break-word;word-break: break-all;">Lot</td>
        	<td width="50" style="word-wrap: break-word;word-break: break-all;">Rate</td>
        	<td width="120" style="word-wrap: break-word;word-break: break-all;">Cons for Dzn Gmts</td>
        	<td width="110" style="word-wrap: break-word;word-break: break-all;">Total (KG)</td>
        </tr>

        <?
		$i=0;
		$total_yarn=0;
		foreach($yarn_sql_array  as $row)
        {

			$i++;
			$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
			$rowcons_Amt = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];


			$rate=$rowcons_Amt/$rowcons_qnty;
			$rowcons_qnty =($rowcons_qnty/100)*$booking_percent;
			$job_no=$row[csf("job_no")];
			$cos_per_value=0;
			foreach(explode(",", $job_no) as $keys=>$vals)
			{
				$cos_per_value=$cos_per_arr[$vals];
			}
			?>
            <tr align="center">
            	<td width="25" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>

            	<td width="400" style="word-wrap: break-word;word-break: break-all;" align="left">
            		<?
            		$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
            		$yarn_des.=$color_library[$row[csf('color')]]." ";
            		$yarn_des.=$yarn_type[$row[csf('type_id')]];
            		echo $yarn_des;
            		?>
            	</td>
            	<td width="60" style="word-wrap: break-word;word-break: break-all;"></td>
            	<td width="60" style="word-wrap: break-word;word-break: break-all;"></td>
            	<td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo number_format($row[csf('rate')],4);  $cos_per_arr[$job_no].' c'; ?></td>
            	<td width="120" style="word-wrap: break-word;word-break: break-all;"><? echo number_format(($rowcons_qnty/$po_qnty_tot)*$cos_per_value,4);?></td>

            	<td align="right" width="110" style="word-wrap: break-word;word-break: break-all;"><? echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
            </tr>
            <?
		}
		?>
       <tr align="center">
	       	<td colspan="6" align="right">Total</td>
	       	<td align="right"><? echo number_format($total_yarn,4); ?></td>
        </tr>
	</table>

	<br>
	<?
	$color_name_arr=return_library_array( "SELECT id,color_name from lib_color",'id','color_name');
	$sql_stripe="SELECT c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom,b.job_no  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no in($job_nos)  and d.job_no in($job_nos) and b.booking_no=$txt_booking_no  and c.color_type_id in (2,6,33,34) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction,b.dia_width ,b.job_no order by b.job_no ";
	$result_data=sql_select($sql_stripe);
	foreach($result_data as $row)
	{
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
		$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
		$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
	}

	if(count($stripe_arr)>0)
	{
		?>

		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
	             <td colspan="9" align="center"><b>Stripe Details</b></td>
	        </tr>

	        <tr align="center">
	        	<th width="30"> SL</th>
	        	<th width="50"> Job</th>
	            <th width="100"> Body Part</th>
	            <th width="80"> Fabric Color</th>
	            <th width="70"> Fabric Qty(KG)</th>
	            <th width="70"> Stripe Color</th>
	            <th width="70"> Stripe Measurement</th>
	            <th width="70"> Stripe Uom</th>
	            <th  width="70"> Qty.(KG)</th>
	            <th  width="70"> Y/D Req.</th>
	        </tr>

	        <?
			$i=1;$total_fab_qty=0;
			$total_fabreqtotkg=0;
			$fab_data_array=array();
			$stripe_wise_fabkg_arr=array();

			$stripe_wise_fabkg_sql="SELECT a.job_no,a.body_part_id,a.color_type_id,b.fabric_color_id, sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty  from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where  a.color_type_id=2 and a.job_no=b.job_no  and b.booking_no=$txt_booking_no
			and a.id=b.pre_cost_fabric_cost_dtls_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no,a.body_part_id,a.color_type_id,b.fabric_color_id";
			foreach(sql_select($stripe_wise_fabkg_sql) as $keys=>$vals)
			{
				$stripe_wise_fabkg_arr[$vals[csf("job_no")]][$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("fabric_color_id")]] +=$vals[csf("grey_fab_qnty")];
			}
	        foreach($stripe_arr as $body_id=>$body_data)
	        {
				foreach($body_data as $color_id=>$color_val)
				{
					$rowspan=count($color_val['stripe_color']);
					$composition=$stripe_arr2[$body_id][$color_id]['composition'];
					$construction=$stripe_arr2[$body_id][$color_id]['construction'];
					$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
					$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
					$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];

					if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
					else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";

					$color_wise_wo_sql_qnty=sql_select("SELECT a.job_no, sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
						WHERE a.job_no=b.job_no and
						a.id=b.pre_cost_fabric_cost_dtls_id and
						c.job_no_mst=a.job_no and
						c.id=b.color_size_table_id and
						b.po_break_down_id=d.po_break_down_id and
						b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
						d.booking_no =$txt_booking_no and
						a.body_part_id='".$body_id."' and
						a.color_type_id='".$color_type_id."' and
						a.construction='".$construction."' and
						a.composition='".$composition."' and
						a.gsm_weight='".$gsm_weight."' and
						$color_cond and
						d.status_active=1 and
						d.is_deleted=0 group by a.job_no
						");
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;
					?>
					<tr>
						<?

						//$color_qty=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
						$jobs=$stripe_arr2[$body_id][$color_id]['job_no'];
						$color_qty=$stripe_wise_fabkg_arr[$jobs][$body_id][$color_type_id][$color_id];
						?>
						<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $jobs; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
						<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?></td>
						<?
						//$total_fab_qty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
						$total_fab_qty+=$color_qty;
						foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						{
							$measurement=$color_val['measurement'][$strip_color_id];
							$uom=$color_val['uom'][$strip_color_id];
							$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
							$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
							?>
							<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
							<td align="right"> <? echo  number_format($measurement,2); ?></td>
		                    <td> <? echo  $unit_of_measurement[$uom]; ?></td>
							<td align="right"> <? echo  number_format($fabreqtotkg,2); ?></td>
							<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
					</tr>
							<?
							$total_fabreqtotkg+=$fabreqtotkg;
						}
							$i++;
				}
			}
			?>
	        <tfoot>
	        	<tr>
	        		<td colspan="4">Total </td>
	        		<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
	        		<td></td>
	        		<td></td>
	        		<td>   </td>
	        		<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	        	</tr>
	        </tfoot>
		</table>
		<?
	}
	?>


	<br/>
    <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
    	<tr align="center">
    		<td colspan="10"><b>Comments</b></td>
    	</tr>
    	<tr align="center">
    		<td>Sl</td>
    		<td>Po NO</td>
    		<td>Ship Date</td>
    		<td>Pre-Cost Qty</td>
    		<td>Mn.Book Qty</td>
    		<td>Sht.Book Qty</td>
    		<td>Smp.Book Qty</td>
    		<td>Tot.Book Qty</td>
    		<td>Balance</td>
    		<td>Comments</td>
    	</tr>
        <?
        $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
        $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
        if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
        if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
        $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
        $item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no in($job_nos)", "gmts_item_id", "set_item_ratio");

        $nameArray=sql_select("
        select
        a.id,
        a.item_number_id,
        a.costing_per,
        b.po_break_down_id,
        b.color_size_table_id,
        b.requirment,
        c.po_number
        FROM
        wo_pre_cost_fabric_cost_dtls a,
        wo_pre_cos_fab_co_avg_con_dtls b,
        wo_po_break_down c
        WHERE
        a.job_no=b.job_no and
        a.job_no=c.job_no_mst and
        a.id=b.pre_cost_fabric_cost_dtls_id and
        b.po_break_down_id=c.id and
        b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
        order by id");

        $count=0;
        $tot_grey_req_as_pre_cost_arr=array();
        foreach ($nameArray as $result)
        {
        	if (count($nameArray)>0 )
        	{
        		if($result[csf("costing_per")]==1)
        		{
        			$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        		}
        		if($result[csf("costing_per")]==2)
        		{
        			$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        		}
        		if($result[csf("costing_per")]==3)
        		{
        			$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        		}
        		if($result[csf("costing_per")]==4)
        		{
        			$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        		}
        		if($result[csf("costing_per")]==5)
        		{
        			$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        		}
        		$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        	}
        }

        $total_pre_cost=0;
        $total_booking_qnty_main=0;
        $total_booking_qnty_short=0;
        $total_booking_qnty_sample=0;
        $total_tot_bok_qty=0;
        $tot_balance=0;
        $booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

        $booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
        $booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
        $sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
        foreach($sql_data  as $row)
        {
        	$col++;
        	?>
        	<tr align="center">
        		<td><? echo $col; ?></td>
        		<td><? echo $row[csf("po_number")]; ?></td>
        		<td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
        		<td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
        		<td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
        		<td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
        		<td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
        		<td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
        		<td align="right">
        			<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
        		</td>
        		<td>
        			<?
        			if( $balance>0)
        			{
        				echo "Less Booking";
        			}
        			else if ($balance<0)
        			{
        				echo "Over Booking";
        			}
        			else
        			{
        				echo "";
        			}
        			?>
        		</td>
        	</tr>
        	<?
        }
   		 ?>
   		 <tfoot>
   		 	<tr>
   		 		<td colspan="3">Total:</td>
   		 		<td align="right"><? echo number_format($total_pre_cost,2); ?></td>
   		 		<td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
   		 		<td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
   		 		<td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
   		 		<td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
   		 		<td align="right"><? echo number_format($tot_balance,2); ?></td>
   		 		<td></td>
   		 	</tr>
   		 </tfoot>
    </table>


    <fieldset id="div_size_color_matrix" style="max-width:1000;">
		<?
	    //Query for TNA start-
		$po_id_all=str_replace("'","",$txt_order_no_id);
		$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
		$tna_start_sql=sql_select( "select id,po_number_id,
						(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
						(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
						(case when task_number=60 then task_start_date else null end) as knitting_start_date,
						(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
						(case when task_number=61 then task_start_date else null end) as dying_start_date,
						(case when task_number=61 then task_finish_date else null end) as dying_end_date,
						(case when task_number=64 then task_start_date else null end) as finishing_start_date,
						(case when task_number=64 then task_finish_date else null end) as finishing_end_date,
						(case when task_number=84 then task_start_date else null end) as cutting_start_date,
						(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
						(case when task_number=86 then task_start_date else null end) as sewing_start_date,
						(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
						(case when task_number=110 then task_start_date else null end) as exfact_start_date,
						(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
						(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
						(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
						from tna_process_mst
						where status_active=1 and po_number_id in($po_id_all)");
		$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
		$tna_date_task_arr=array();
		foreach($tna_start_sql as $row)
		{
			if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
			{
				if($tna_fab_start=="")
				{
					$tna_fab_start=$row[csf("fab_booking_start_date")];
				}
			}


			if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
			}
			if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
			}
			if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
			}
			if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
			}

			if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
			}
			if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
			}
			if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
			}
		}


		?>
        <legend>TNA Information</legend>

        <table width="100%" style="border:1px solid black;font-size:12px; font-family:Arial Narrow;" border="1" cellpadding="2" cellspacing="0" rules="all">
        	<tr>
        		<td rowspan="2" align="center" valign="top">SL</td>
        		<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
        		<td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
        		<td colspan="2" align="center" valign="top"><b>Knitting</b></td>
        		<td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
        		<td colspan="2" align="center" valign="top"><b>Finish Fabric Prod.</b></td>
        		<td colspan="2" align="center" valign="top"><b>Cutting </b></td>
        		<td colspan="2" align="center" valign="top"><b>Sewing </b></td>
        		<td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
        	</tr>
        	<tr>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>

        	</tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{

				?>
				<tr>
					<td><? echo $i; ?></td>
					<td><? echo $po_num_arr[$order_id]; ?></td>
					<td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
					<td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
					<td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
				</tr>
                <?
				$i++;
			}
			?>

        </table>
    </fieldset>

    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
            <thead>
                <tr>
                    <th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                </tr>
            </thead>
            <tbody>
            <?
            $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
            if ( count($data_array)>0)
            {
                $i=0;
                foreach( $data_array as $row )
                {
                    $i++;
                    ?>
                        <tr id="settr_1" valign="top">
                            <td style="vertical-align:top">
                            <? echo $i;?>
                            </td>
                            <td>
                           <strong style="font-size:14px"> <? echo $row[csf('terms')]; ?></strong>
                            </td>
                        </tr>
                    <?
                }
            }
            ?>
        </tbody>
    </table>


	<br>
	<?
		echo signature_table(121, $cbo_company_name, "1000px");
	?>

  </div>
	<?
$emailBody=ob_get_contents();
//ob_clean();
if($is_mail_send==1){
	$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
	$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
	if($req_approved && $is_approved==1){
		$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
	}
	elseif($req_approved && $is_approved==0){
		$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
	}
	

	$mailArr=array();
	if($mail_id!=''){$mailArr[]=$mail_id;}
	if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
	

	$to=implode(',',$mailArr);
	$subject="Partial Fabric Booking Auto Mail";
	
	if($to!=""){
		require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
		require_once('../../../auto_mail/setting/mail_setting.php');
		$header=mailHeader();
		sendMailMailer( $to, $subject, $emailBody );
	}
}
}

if ($action=="get_first_selected_print_report"){
	
	list($company_id,$mail_id)=explode('**',$data);
	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(',',$print_report_format);
	$button_id=$print_report_format_arr[0];
		if($button_id==143){
			echo "generate_fabric_report('show_fabric_booking_report_urmi',1,'".$mail_id."',1);";
		}
        elseif($button_id==84){
            echo "generate_fabric_report('show_fabric_booking_report_urmi_per_job',1,'".$mail_id."',1);";
        }
        elseif($button_id==85){
            echo "generate_fabric_report('print_booking_3',1,'".$mail_id."',1);";
        }
		elseif($button_id==151){
            echo "generate_fabric_report('show_fabric_booking_report_advance_attire_ltd',1,'".$mail_id."',1);";
        }
		elseif($button_id==160){
            echo "generate_fabric_report('print_booking_5',1,'".$mail_id."',1);";
		}
		elseif($button_id==175){
            echo "generate_fabric_report('print_booking_6',1,'".$mail_id."',1);";
		}
		elseif($button_id==155){
            echo "generate_fabric_report('fabric_booking_report',1,'".$mail_id."',1);";
		}
		elseif($button_id==235){
            echo "generate_fabric_report('print_9',1,'".$mail_id."',1);";
        }	elseif($button_id==191){
            echo "generate_fabric_report('print_booking_7',1,'".$mail_id."',1);";
        }
	exit();
}


?>