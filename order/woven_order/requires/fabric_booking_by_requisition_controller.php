<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Fabric Booking By Requisition
Functionality	         :
JS Functions	         :
Created by		         :	zakaria joy
Creation date 	         : 	06-06-2023
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         : 
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
include('../../../includes/class4/class.trims.php');
//echo $permission;

$userCredential = sql_select("SELECT unit_id as company_id, brand_id FROM user_passwd where id=$user_id");

$brand_id = $userCredential[0][csf('brand_id')];
$brand_cond="";

if ($brand_id !='') {
    $brand_cond = " and id in ( $brand_id)";
}
//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_suplier($paymode,$company){
	$cbo_supplier_name='';
	if($paymode==5 || $paymode==3){
		//echo "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name";
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 140, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier()",0,"" );
	}
	else{
		$suplier_data=sql_select("select a.id,a.supplier_name,a.tag_company from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name");

		foreach($suplier_data as $val){
				 $companyArr=explode(",",$val[csf('tag_company')]);
				 foreach($companyArr as $cid){
					if($company==$cid){
						$suplierArr[$val[csf('id')]]=$val[csf('supplier_name')];
					}
				 }
		}
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 140, $suplierArr,"", 1, "-- Select Supplier --", $selected, "",0 );
	}
	return $cbo_supplier_name;
}

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/fabric_booking_by_requisition_controller', this.value, 'load_drop_down_brand', 'brand_td');","","" );
	exit();
}

if ($action=="load_drop_down_buyer_popup"){
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","0","" );
	exit();
}

if ($action=="load_drop_down_suplier"){
	list($data,$company)=explode("_",$data);
	if($data==5 || $data==3){
	echo create_drop_down( "cbo_supplier_name", 140, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier()",0,"" );
	}
	else{

		$suplier_data=sql_select("select a.id,a.supplier_name,a.tag_company from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name");

		foreach($suplier_data as $val){
				 $companyArr=explode(",",$val[csf('tag_company')]);
				 foreach($companyArr as $cid){
					if($company==$cid){
						$suplierArr[$val[csf('id')]]=$val[csf('supplier_name')];
					}
				 }
		}

	echo create_drop_down( "cbo_supplier_name", 140, $suplierArr,"", 1, "-- Select Supplier --", $selected, "fill_attention(this.value)",0 );
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

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 140, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if($action=="check_conversion_rate"){
	$data=explode("**",$data);
	if($db_type==0){
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date ,$data[2]);
	echo "1"."_".$currency_rate;
	exit();
}

if($action=="check_month_maintain"){
	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0");
	//echo "select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0";
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	if($maintain_setting==1){
		echo "1"."_";
	}
	else{
		echo "0"."_";
	}
	exit();
}

if ($action=="fabric_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function set_checkvalue()
	{
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
        <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th colspan="11">
                        <?
                        // echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                        ?>
                        <input type="hidden" id="cbo_search_category">
                        </th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Booking No</th>
                        <th width="80">Job No</th>
                        <th width="80">File No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Order No</th>
                        <th width="130" colspan="2">Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Item</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                    <input type="hidden" id="selected_booking">
                    <?
					//echo "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name";
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'fabric_booking_by_requisition_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );",1);
                    ?>
                    </td>
                    <td id="buyer_td">
                    <? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" );?>
                    </td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                    <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'fabric_booking_by_requisition_controller','setFilterGrid(\'list_view\',-1)');" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td colspan="11" align="center" valign="middle">
                    	<?=load_month_buttons(1); ?>
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
		$("#cbo_company_mst").val(<?=$company; ?>);
		load_drop_down( 'fabric_booking_by_requisition_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_popup', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
	if($db_type==0) $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num='$data[6]'"; else $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'"; else $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no ='$data[10]'"; //else  $style_cond="";
		if (trim($data[9])!="") $ref_cond=" and d.grouping ='$data[9]'";// echo  $ref_cond; //else  $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number = '$data[11]'"; //else  $order_cond="";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%' $booking_year_cond "; else $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%' $year_cond "; else $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like '$data[10]%'"; //else  $style_cond="";
		if (trim($data[9])!="") $ref_cond=" and d.grouping like '$data[9]%'"; //else  $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number like '$data[11]%'  "; //else  $order_cond="";
	}

	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]' $booking_year_cond "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]' $year_cond "; else $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like'%$data[10]'"; //else  $style_cond="";
		if (trim($data[9])!="") $ref_cond=" and d.grouping like '%$data[9]'"; //else  $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number like '%$data[11]'  "; //else  $order_cond="";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%' $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like '%$data[10]%'"; //else  $style_cond="";
			if (trim($data[9])!="") $ref_cond=" and d.grouping like '%$data[9]%'"; //else  $style_cond="";
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
	$sql_po= sql_select("select a.booking_no, a.po_break_down_id, a.job_no from wo_booking_mst a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=2 and a.entry_form=108 and a.status_active=1 and a.is_deleted=0 order by a.booking_no");
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

	$approved=array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$job_prefix_num,6=>$po_array,9=>$item_category,10=>$fabric_source,11=>$suplier,12=>$approved,13=>$is_ready);
	if($data[12]==0)
	{
		 $sql="SELECT min(a.id) as id, a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.gmts_item_id, c.job_no_prefix_num, c.style_ref_no, d.po_number, d.grouping, d.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=12 and a.entry_form=630 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and $company $buyer $booking_date $booking_cond $style_cond $ref_cond $order_cond $job_cond group by a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.job_no_prefix_num, c.gmts_item_id, c.style_ref_no, d.po_number, d.grouping, d.file_no order by id DESC";
	}
	else
	{
		 $sql="SELECT min(a.id) as id, a.job_no as job_no_prefix_num, a.booking_no_prefix_num, a.pay_mode, a.booking_no, company_id, a.buyer_id, a.supplier_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.is_approved from wo_booking_mst a where a.booking_no not in ( select a.booking_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.booking_type=12 and a.entry_form=630 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and $company $buyer $booking_date $booking_cond $job_cond $file_no_cond $internal_ref_cond group by a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date) and a.booking_type=12 and a.entry_form=630 and a.status_active =1 and a.is_deleted=0 and $company $buyer $supplier_id $booking_date $booking_cond group by a.booking_no_prefix_num, a.booking_no, a.job_no, company_id, a.buyer_id, a.supplier_id, a.pay_mode, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.is_approved order by id DESC";
	}
	?>
    <div>
        <table width="1000" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="60">Booking No</th>
                    <th width="60">Booking Date</th>
                    <th width="90">Buyer</th>
                    <th width="90">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="100">Gmts Item </th>
                    <th width="80">PO number</th>
                    <th width="80">Internal Ref</th>
                    <th width="80">File No</th>
                    <th width="60">Pay Mode</th>
                    <th width="60">Supplier</th>
                    <th width="50">Approved</th>
                    <th>Ready to Approved</th>
                </tr>
            </thead>
        </table>
        <div style="width:1000px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table width="980" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="list_view">
                <tbody>
                <?
                $k=1;
				
                $result_data=sql_select($sql);
                foreach($result_data as $row)
				{
					if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr onClick="js_set_value('<? echo $row[csf("booking_no")]?>')" style="cursor:pointer" bgcolor="<? echo $bgcolor;?>" >
                        <td width="30"><? echo $k; ?></td>
                        <td width="60"><? echo $row[csf("booking_no_prefix_num")];?></td>
                        <td width="60"><? echo change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-");?></td>
                        <td width="90"><? echo $buyer_arr[$row[csf("buyer_id")]];?></td>
                        <td width="90" style="word-break:break-all"><? echo $row[csf("job_no_prefix_num")];?></td>
                        <td width="90" style="word-break:break-all"><? echo $row[csf("style_ref_no")];?></td>
                        <td width="100" style="word-break:break-all"><?
                        $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
                        foreach($gmts_item_id as $item_id)
                        {
                        	if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
                        }
                        echo $gmts_item;?> </td>
                        <td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf("po_number")];?></td>
                        <td width="80" style="word-break:break-all"><? echo $row[csf("grouping")];?></td>
                        <td width="80" style="word-break:break-all"><? echo $row[csf("file_no")];?></td>
                        <td width="60"><? echo $pay_mode[$row[csf("pay_mode")]];?></td>
                        <td width="60" style="word-break:break-all">
                        <?
                        if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) echo $comp[$row[csf("supplier_id")]];
                        else echo $suplier[$row[csf("supplier_id")]];
                        ?>
                        </td>
                        <td width="50" style="word-break:break-all"><? echo $approved[$row[csf("is_approved")]];?></td>
                        <td><? echo $is_ready[$row[csf("ready_to_approved")]];?></td>
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

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select id,booking_no, booking_date, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, booking_month, supplier_id, attention, booking_percent, delivery_date,fabric_start_date, source, booking_year, colar_excess_percent, cuff_excess_percent, is_approved, ready_to_approved, is_apply_last_update, rmg_process_breakdown, fabric_composition, uom, remarks, cbo_level, brand_id, isgreyfab_purchase, ship_mode, pay_term, tenor, shippingmark_breck_down, booking_basis from wo_booking_mst where booking_no='$data' and status_active =1 and is_deleted=0";
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		$supplier_dropdwan=load_drop_down_suplier($row[csf("pay_mode")],$row[csf("company_id")]);
		echo "document.getElementById('sup_td').innerHTML = '".$supplier_dropdwan."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_basis').value = '".$row[csf("booking_basis")]."';\n";
		echo "document.getElementById('txt_colar_excess_percent').value = '".$row[csf("colar_excess_percent")]."';\n";
		echo "document.getElementById('txt_cuff_excess_percent').value = '".$row[csf("cuff_excess_percent")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		if($row[csf("isgreyfab_purchase")]==0 || $row[csf("isgreyfab_purchase")]=="") $row[csf("isgreyfab_purchase")]=2;
		echo "document.getElementById('cbo_greyfab_purch').value = '".$row[csf("isgreyfab_purchase")]."';\n";
		echo "document.getElementById('cbo_shipmode').value = '".$row[csf("ship_mode")]."';\n";
		echo "document.getElementById('cbo_payterm').value = '".$row[csf("pay_term")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_fabriccomposition').value = '".$row[csf("fabric_composition")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('hiddshippingmark_breck_down').value = '".$row[csf("shippingmark_breck_down")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "fnc_greyFabPurchase('".$row[csf("pay_mode")]."');\n";		
		
		
		$sql_request="select MAX(id) as id, approval_cause from fabric_booking_approval_cause where entry_form=7 and user_id='$user_id' and booking_id=".$row[csf("id")]." and approval_type=2 and status_active=1 and is_deleted=0 group by approval_cause order by id desc";// page_id='$menu_id'
		//echo $sql_request;
		$nameArray_request=sql_select($sql_request);
		
		foreach($nameArray_request as $approw)
		{
			$approval_cause=$approw[csf("approval_cause")];
		}
		
		$un_sql_request="select MAX(id) as id,max(approval_no) as approved_no from fabric_booking_approval_cause where entry_form=7  and booking_id=".$row[csf("id")]." and approval_type=2 and status_active=1 and is_deleted=0  ";// page_id='$menu_id'
			
		// echo $sql_request;
		$nameArray_un_request=sql_select($un_sql_request);
		$cause_approved_no='';
		foreach($nameArray_un_request as $approw)
		{
			$cause_approved_no=$approw[csf("approved_no")];
		}
		//echo "select max(approved_no) as max_approved_no from approval_history where mst_id='".$row[csf('id')]."'  and entry_form=7 ";
		 $max_approved_no=return_field_value("max(approved_no) as max_approved_no", "approval_history", "mst_id='".$row[csf('id')]."'  and entry_form=7","max_approved_no");
		
		if($max_approved_no!=$cause_approved_no)
		{
			$approval_cause='';	
		}
		
		if($row[csf("is_approved")]==1){
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved.';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
			echo "document.getElementById('txt_un_appv_request').value = '".str_replace(array("&", "*", "(", ")", "=","'","_",",","\r", "\n",'"','#'), "", $approval_cause)."';\n";
		}
		else if($row[csf("is_approved")]==3){ //ISD-22-05697 by Kausar
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved.';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
			echo "document.getElementById('txt_un_appv_request').value = '".str_replace(array("&", "*", "(", ")", "=","'","_",",","\r", "\n",'"','#'), "", $approval_cause)."';\n";
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
		$sql_delevary=sql_select("select max(c.task_start_date) as task_finish_date from tna_process_mst c,wo_booking_dtls b where c.po_number_id=b.po_break_down_id and b.booking_no in('".$data."') and c.task_number in(73) and c.is_deleted = 0 and c.status_active=1");
		//echo "select max(c.task_start_date) as task_finish_date from tna_process_mst c,wo_booking_dtls b where c.po_number_id=b.po_break_down_id and c.b.booking_no in('".$data."') and c.task_number in(73) and c.is_deleted = 0 and c.status_active=1";
		if(count($sql_delevary)>0)
		{
		foreach($sql_delevary as $row_delevary){
				if($row_delevary[csf("task_finish_date")]!="")
				{
					echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
					echo "document.getElementById('txt_tna_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
				}
			}
		}
	}
	$booking_dtls_data=sql_select("SELECT b.job_no, b.po_break_down_id from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$data' group by b.job_no, b.po_break_down_id");
	if(count($booking_dtls_data)>0){
		foreach($booking_dtls_data as $row){
			$job_dtls_arr[$row[csf('job_no')]]=$row[csf('job_no')];
			$po_dtls_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
		echo "document.getElementById('txt_job_no_str').value = '".implode(",",$job_dtls_arr)."';\n";
		echo "document.getElementById('order_no_id_str').value = '".implode(",",$po_dtls_arr)."';\n";
	}
	
	
	 exit();
}

if ($action=="fabric_search_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$postatus="";
	if(trim($txt_booking_no)!="")
	{
		/* $sqlBooking=sql_select("Select po_break_down_id from wo_booking_dtls where booking_no='$txt_booking_no' and is_deleted = 0 and status_active=1");
		$pobooking=$sqlBooking[0][csf('po_break_down_id')];		
		$sql_result=sql_select("select is_confirmed from wo_po_break_down where id='$pobooking'");
		$postatus=$sql_result[0][csf('is_confirmed')]; */

		//$sqlBooking=sql_select("Select po_break_down_id from wo_booking_dtls where booking_no='$txt_booking_no' and is_deleted = 0 and status_active=1");
	}
	
	?>
	<script>
		var selected_id = new Array, selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count-1;
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
		var selected_booking_id=new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				var booking_id=$('#booking_id_'+str).val();
				if(jQuery.inArray( booking_id, selected_booking_id ) != -1  || selected_booking_id.length<1 ){
					toggle( document.getElementById( 'search' + str ), '#FFFFCC' );				
					if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + str).val() );
						selected_booking_id.push(booking_id);
					}
					else{
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_booking_id.splice( i, 1 );
					}
				}
				else{
					alert("Multiple Requisition Not Allowed");
					return;
				}
				
			}
			var id = '';
			var pre_cost_dtls_id='';
			var txt_po_id='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			$('#txt_selected_id').val( id );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchpofrm_1" id="searchpofrm_1">
            <table width="600"  align="center" rules="all">
                <tr>
                <td align="center" width="100%">
                <table  width="600" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th colspan="11" align="center"><?=create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                        <tr>
                            <th width="140">Company</th>
                            <th width="150">Buyer</th>
                            <th width="70">Fabric Requisition No</th>
                            <th width="170" colspan="2">Date Range</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tr>
                    	<?php 
						//echo $cbo_supplier_name;

                    		$onchange_company="";
                    		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
                    		if(empty($cbo_buyer_name))
                    		{
                    			$onchange_company="load_drop_down( 'fabric_booking_by_requisition_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );";
                    		}

                    	 ?>
                        <td><?=create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name),$onchange_company ,"1"); ?>
                        </td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "","1" ); ?>
                        </td>
                        <td><input name="txt_fabric_req" id="txt_fabric_req" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value=""/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value=""/></td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_fabric_req').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'fabric_search_list_view', 'search_div', 'fabric_booking_by_requisition_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:70px;" />
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
                	<td id="search_div" align="center"></td>
                </tr>
                <tr>
                    <td align="center">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                    </td>
                </tr>				
                <tr>
                    <td align="center" >
                        <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" />
                    </td>
                </tr>
            </table>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">$("#cbo_buyer_name").val(<?=$cbo_buyer_name; ?>);</script>     
	</html>
	<?
	exit();
}

if ($action=="fabric_search_list_view"){
	$data=explode('_',$data);
	$company=$data[0];
	$buyer=$data[1];
	$txt_req_no=$data[2];
	$date_from=$data[3];
	$date_to=$data[4];
	if ($company!=0) $company_cond=" and b.company_name='$company'"; else { echo "Please Select Company First."; die; }
	if ($buyer!=0) $buyer_cond=" and b.buyer_name='$buyer'"; else{ echo "Please Select Buyer First."; die; }
	if (str_replace("'","",$txt_req_no)=="")
	{
		echo "Please Insert Fabric Requisition NO First."; die;
	}

	if($db_type==0){
		if ($date_from!="" &&  $date_to!="") $booking_date_cond = "and c.booking_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; else $booking_date_cond ="";
	}
	else if($db_type==2){
		if ($date_from!="" &&  $date_to!="") $booking_date_cond = "and c.booking_date between '".change_date_format($date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'"; else $booking_date_cond ="";
	}
	$req_cond="";
	if($search_category==1){
		if (str_replace("'","",$txt_req_no)!="") $req_cond=" and c.booking_no_prefix_num='$txt_req_no'";
	}
	else if($search_category==2){
		if (str_replace("'","",$txt_req_no)!="") $req_cond=" and c.booking_no_prefix_num like '$txt_req_no%'";
	}
	else if($search_category==3){
		if (str_replace("'","",$txt_req_no)!="") $req_cond=" and c.booking_no_prefix_num like '%$txt_req_no'";
	}
	else if($search_category==4 || $search_category==0){
		if (str_replace("'","",$txt_req_no)!="") $req_cond=" and c.booking_no_prefix_num like '%$txt_req_no%'";
	}

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$req_sql="SELECT a.booking_mst_id, a.id as booking_dtls_id, a.job_no, a.booking_no, a.grey_fab_qnty, a.rate, b.style_ref_no, a.gsm_weight, a.dia_width, a.gmts_color_id, a.fabric_color_id,  c.body_part_id, c.color_type_id, c.construction, c.composition, c.uom from wo_booking_mst c join wo_booking_dtls a on c.id=a.booking_mst_id join wo_po_details_master b on a.job_no=b.job_no join wo_pre_cost_fabric_cost_dtls c on b.id=c.job_id and c.id=a.pre_cost_fabric_cost_dtls_id where c.booking_type=10 and c.is_short=2 and c.entry_form=611 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $booking_date_cond $req_cond";
	//echo $req_sql; die;
	$sql_data=sql_select($req_sql);

	foreach($sql_data as $row){
		$req_dtls_id[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
	}
	$cu_booking_data_arr=array();
	if(count($req_dtls_id)>0){
		$req_id_str=implode(", ", $req_dtls_id);

		$cu_booking_data=sql_select(" SELECT b.pre_cost_fabric_cost_dtls_id as req_id_dtls, sum(b.GREY_FAB_QNTY) as booking_qty from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id in ($req_id_str) and a.booking_type=12 and a.entry_form=630 group by b.pre_cost_fabric_cost_dtls_id");
		foreach($cu_booking_data as $row){
			$cu_booking_data_arr[$row[csf('req_id_dtls')]]=$row[csf('booking_qty')];
		}
	}
	
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Job No</th>
                <th width="80">Req No</th>
                <th width="80">Style Ref.</th>
                <th width="80">Body Part</th>
                <th width="80">Color Type</th>
                <th width="200">Fabric Description</th>                 
                <th width="60">Width / Dia</th>
                <th width="60">GSM/ Weight</th>
                <th width="60">GMT Color</th>
                <th width="60">Fab. Color</th>
                <th width="60">Uom</th>
                <th width="60">Req. Qty.</th>
                <th width="60">Cummulative WO Qty</th>
                <th>Balance Qty</th>
            </thead>
     	</table>
     </div>
     <div style="width:1150px; max-height:270px;overflow-y:scroll;" >
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" id="list_view">
    <?
	$i=1;
	if(count($sql_data)>0){
		foreach($sql_data as $sql_row){			
			//$balQty=1;
			$cummulative_qty=$cu_booking_data_arr[$sql_row[csf('booking_dtls_id')]];
			$balance_qty=$sql_row[csf('grey_fab_qnty')]-$cummulative_qty;
			if($balance_qty >0 ){				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr style="text-decoration:none; cursor:pointer" bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>" onClick="js_set_value(<?=$i;?>);">
					<td width="30"><?=$i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$sql_row[csf('booking_dtls_id')]; ?>"/>
						<input type="hidden" name="booking_id" id="booking_id_<?=$i; ?>" value="<?=$sql_row[csf('booking_mst_id')]; ?>"/>
					</td>
					<td width="80" style="word-break:break-all"><?=$sql_row[csf('job_no')]; ?></td>
					<td width="80" style="word-break:break-all"><?=$sql_row[csf('booking_no')]; ?></td>
					<td width="80" style="word-break:break-all"><?=$sql_row[csf('style_ref_no')]; ?></td>
					<td width="80" style="word-break:break-all"><?=$body_part[$sql_row[csf('body_part_id')]]; ?></td>
					<td width="80" style="word-break:break-all"><?=$color_type[$sql_row[csf('color_type_id')]]; ?></td>
					<td width="200" style="word-break:break-all"><?=$sql_row[csf('construction')].' '.$sql_row[csf('composition')]; ?></td>
					<td width="60" style="word-break:break-all"><?=$sql_row[csf('dia_width')]; ?></td>
					<td width="60" style="word-break:break-all"><?=$sql_row[csf('gsm_weight')]; ?></td>
					<td width="60" style="word-break:break-all"><?=$color_library[$sql_row[csf('gmts_color_id')]]; ?></td>
					<td width="60" style="word-break:break-all"><?=$color_library[$sql_row[csf('fabric_color_id')]]; ?></td>
					<td width="60"><?=$unit_of_measurement[$sql_row[csf('uom')]]; ?></td>
					<td width="60" align="right"><?=$sql_row[csf('grey_fab_qnty')]; ?></td>
					<td width="60" align="right"><?=$cummulative_qty; ?></td>
					<td align="right"><?=$balance_qty; ?></td>
				</tr>
				<?
				$i++;
			}
		}
	}
	?>
    </table>
	</div>
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
                        <td><?
						//load_drop_down( 'fabric_booking_by_requisition_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );
						 echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_mst), "","1"); ?>
                        </td>
                        <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_mst and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "","1" ); ?>
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value, 'create_job_search_list_view', 'search_div', 'fabric_booking_by_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
	  $sql= 'select a.job_no_prefix_num AS "job_no_prefix_num",a.buyer_name AS "buyer_name", a.job_no AS "job_no",a.style_ref_no AS "style_ref_no", b.id AS "id",b.po_number AS "po_number", b.po_quantity AS "po_quantity",b.shipment_date AS "shipment_date", b.grouping AS "grouping", b.file_no AS "file_no" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where   a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$shipment_date. $buyer. $company.  $job_cond. $order_cond. $style_cond. $file_no_cond. $internal_ref_cond. $year_cond.' group by a.job_no_prefix_num,a.job_no,a.buyer_name,a.style_ref_no,b.id,b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no order by b.id DESC';

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
					<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bg_color; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['job_no_prefix_num']; ?>');" >
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
			if(str_all[3]==1)
			{
				alert('This PO Without TNA Process. But TNA Integrated Yes in Merchandising Variable Settings.')
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
                        <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'fabric_booking_by_requisition_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );","1"); ?>
                        </td>
                        <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "","1" ); ?>
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value, 'create_po_search_list_view', 'search_div', 'fabric_booking_by_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data[0]' and variable_list=14 and status_active=1 and is_deleted=0");
	$maintain_setting=$sql_result[0][csf('tna_integrated')];

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

	$tna_date_arr=array();
	$sql_tna=sql_select("select po_number_id, max(task_start_date) as task_finish_date from tna_process_mst where task_number in(73) and is_deleted=0 and status_active=1 group by po_number_id");
	foreach($sql_tna as $row)
	{
		$tna_date_arr[$row[csf("po_number_id")]]=1;
	}
	unset($sql_tna);

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	 $sql= 'select a.job_no_prefix_num AS "job_no_prefix_num" ,a.buyer_name AS "buyer_name", a.job_no AS "job_no",a.style_ref_no AS "style_ref_no", b.id AS "id",b.po_number AS "po_number", b.po_quantity AS "po_quantity",b.shipment_date AS "shipment_date", b.grouping AS "grouping", b.file_no AS "file_no" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e  where   a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and a.currency_id='.$data[11].' and d.fab_nature_id='.$data[12].'  and d.fabric_source='.$data[14].'     and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and b.shiping_status not in(3) '.$shipment_date. $buyer. $company.  $job_cond. $order_cond. $style_cond. $file_no_cond. $internal_ref_cond. $year_cond. $uom_cond.' group by a.job_no_prefix_num,a.job_no,a.buyer_name,a.style_ref_no,b.id,b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no';

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
				if($maintain_setting==1 && $tna_date_arr[$row["id"]]=="") $tna_check=1;  else $tna_check=0;
				?>
					<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bg_color; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['id'].'_'.$row['po_number'].'_'.$row['job_no'].'_'.$tna_check; ?>','tr_<? echo $i; ?>');" >
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

if ($action=="populate_order_data_from_search_popup")
{
	$data=explode("_",$data);
	//$fabric_description_array=array();
	/*if ($data[2]!=0) $uom_cond=" and a.uom='$data[2]'"; else $uom_cond="";
	 $sql='select a.id AS "id", a.body_part_id AS "body_part_id",a.color_type_id AS "color_type_id",a.fabric_description AS "fabric_description", a.gsm_weight AS "gsm_weight" from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id='.$data[1].' and a.fabric_source='.$data[3].' and b.po_break_down_id in ('.$data[0].')'.$uom_cond.' and a.is_deleted=0 and a.status_active=1 group by a.id, a.body_part_id,a.color_type_id,a.fabric_description,a.gsm_weight';
	$sql_data=sql_select($sql);
	foreach($sql_data as $sql_row){
		$fabric_description_array[$sql_row['id']]=$body_part[$sql_row['body_part_id']].', '.$color_type[$sql_row['color_type_id']].', '.$sql_row['fabric_description'].', '.$sql_row['gsm_weight'];
	}
	 $cbo_fabric_description=create_drop_down( "cbo_fabric_description",500, $fabric_description_array,"", 1, "-- Select --", $selected, "fnc_generate_booking()",0 );
	 echo "document.getElementById('fabric_description_td').innerHTML = '". $cbo_fabric_description."';\n";*/

	 //$sql_delevary=sql_select("select task_number,max(task_finish_date) as task_finish_date from tna_process_mst where po_number_id in(".$data.") and task_number in(73) and is_deleted = 0 and 	status_active=1 group by task_number"); for group
	$sql_delevary=sql_select("select task_number,min(task_start_date) as task_finish_date from tna_process_mst where po_number_id in(".$data[0].") and task_number in(73) and is_deleted = 0 and 	status_active=1 group by task_number");// added for urmi req from fakhrul
	//echo "select task_number,min(task_start_date) as task_finish_date from tna_process_mst where po_number_id in(".$data[0].") and task_number in(73) and is_deleted = 0 and 	status_active=1 group by task_number";
	foreach($sql_delevary as $row_delevary)
	{
	   echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
	   echo "document.getElementById('txt_tna_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
	}
}

if ($action=="generate_fabric_booking") 
{
	extract($_REQUEST);
	$txt_select_item=str_replace("'","",$txt_select_item);
	$company_id=str_replace("'","",$cbo_company_name);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$sql_data=sql_select("SELECT c.id as req_id,a.id as booking_dtls_id, a.po_break_down_id, a.job_no, a.booking_no, a.grey_fab_qnty, a.rate, b.style_ref_no, a.gsm_weight, a.dia_width, a.gmts_color_id, a.fabric_color_id,  c.body_part_id, c.color_type_id, c.construction, c.composition, c.uom from wo_booking_mst c join wo_booking_dtls a on c.id=a.booking_mst_id join wo_po_details_master b on a.job_no=b.job_no join wo_pre_cost_fabric_cost_dtls c on b.id=c.job_id and c.id=a.pre_cost_fabric_cost_dtls_id where c.booking_type=10 and c.is_short=2 and c.entry_form=611 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in ($txt_select_item)");

	$cu_booking_data=sql_select(" SELECT b.pre_cost_fabric_cost_dtls_id as req_id_dtls, sum(b.GREY_FAB_QNTY) as booking_qty from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id in ($txt_select_item) and a.booking_type=12 and a.entry_form=630 group by b.pre_cost_fabric_cost_dtls_id");
	foreach($cu_booking_data as $row){
		$cu_booking_data_arr[$row[csf('req_id_dtls')]]=$row[csf('booking_qty')];
	}	
		 
	
	
	?>
	<table width="1320" class="rpt_table" border="0" rules="all">
        <thead>
			<th width="30">SL</th>
			<th width="80">Job No</th>
			<th width="80">Req No</th>
			<th width="80">Style Ref.</th>
			<th width="80">Body Part</th>
			<th width="80">Color Type</th>
			<th width="150">Fabric Description</th>                 
			<th width="60">Width / Dia</th>
			<th width="60">GSM/ Weight</th>
			<th width="60">GMT Color</th>
			<th width="60">Fab. Color</th>
			<th width="60">Uom</th>
			<th width="80">WO Qty</th>
			<th width="80">Rate</th>
			<th width="80">Amount</th>
			<th width="60">Cum. WO Qty</th>
			<th>Balance Qty</th>
        </thead>
	</table>
	<table width="1320" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
        <tbody>
        <?
		$i=1;
		foreach($sql_data as $sql_row){         
            $cummulative_qty=$cu_booking_data_arr[$sql_row[csf('booking_dtls_id')]];
			$balance_qty=$sql_row[csf('grey_fab_qnty')]-$cummulative_qty;
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr style="text-decoration:none; cursor:pointer" bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
				<td width="30"><?=$i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$sql_row[csf('booking_dtls_id')]; ?>"/>
				</td>
				<td width="80" style="word-break:break-all"><?=$sql_row[csf('job_no')]; ?>
					<input type="hidden" id="txtjobno_<?= $i?>" value="<?= $sql_row[csf('job_no')] ?>" >
					<input type="hidden" id="txtpoid_<?= $i?>" value="<?= $sql_row[csf('po_break_down_id')] ?>" >
					<input type="hidden" id="txtconst_<?= $i?>" value="<?= $sql_row[csf('construction')] ?>" >
					<input type="hidden" id="txtcomp_<?= $i?>" value="<?= $sql_row[csf('composition')] ?>" >
					<input type="hidden" id="txtcolortype_<?= $i?>" value="<?= $sql_row[csf('color_type_id')] ?>" >
					<input type="hidden" id="bookingid_<?= $i?>" value="" >
				</td>
				<td width="80" style="word-break:break-all"><?=$sql_row[csf('booking_no')]; ?>
					<input type="hidden" id="dtlsid_<?= $i?>" value="<?= $sql_row[csf('booking_dtls_id')] ?>" >
					<input type="hidden" id="reqid_<?= $i?>" value="<?= $sql_row[csf('req_id')] ?>" >
				</td>
				<td width="80" style="word-break:break-all"><?=$sql_row[csf('style_ref_no')]; ?></td>
				<td width="80" style="word-break:break-all"><?=$body_part[$sql_row[csf('body_part_id')]]; ?></td>
				<td width="80" style="word-break:break-all"><?=$color_type[$sql_row[csf('color_type_id')]]; ?></td>
				<td width="150" style="word-break:break-all"><?=$sql_row[csf('construction')].' '.$sql_row[csf('composition')]; ?></td>
				<td width="60" style="word-break:break-all"><?=$sql_row[csf('dia_width')]; ?>
					<input type="hidden" id="diawidth_<?= $i?>" value="<?= $sql_row[csf('dia_width')] ?>" >
				</td>
				<td width="60" style="word-break:break-all"><?=$sql_row[csf('gsm_weight')]; ?>
					<input type="hidden" id="gsmweight_<?= $i?>" value="<?= $sql_row[csf('gsm_weight')] ?>" >
				</td>
				<td width="60" style="word-break:break-all"><?=$color_library[$sql_row[csf('gmts_color_id')]]; ?>
					<input type="hidden" id="gmtscolorid_<?= $i?>" value="<?= $sql_row[csf('gmts_color_id')] ?>" >
				</td>
				<td width="60" style="word-break:break-all"><?=$color_library[$sql_row[csf('fabric_color_id')]]; ?>
					<input type="hidden" id="fabriccolorid_<?= $i?>" value="<?= $sql_row[csf('fabric_color_id')] ?>" >
				</td>
				<td width="60"><?=$unit_of_measurement[$sql_row[csf('uom')]]; ?></td>
				<td width="80">
					<input type="text" style="width:60px" class="text_boxes" id="txt_wo_qty_<?=$i?>" onchange="calculate_amount(<?= $i ?>,1,this.value)" value="<?= $balance_qty; ?>">
				</td>
				<td width="80"><input type="text"  style="width:60px" class="text_boxes" id="txt_rate_<?=$i?>" onchange="calculate_amount(<?= $i ?>,2,this.value)" value="<?= $sql_row[csf('rate')]; ?>"></td>
				<td width="80"><input type="text"  style="width:60px" class="text_boxes" id="txt_amount_<?= $i ?>" value="<?=$sql_row[csf('grey_fab_qnty')]*$sql_row[csf('rate')]; ?>" readonly></td>
				<td width="60" align="center"><?=$cummulative_qty; ?></td>
				<td><input type="text"  style="width:60px" class="text_boxes" id="txt_balance_<?=$i?>" value="<?=$balance_qty; ?>" readonly></td>
			</tr>
			<?
			$i++;
        }
        ?>
        </tbody>
	</table>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if ($action=="show_fabric_booking"){
	echo load_html_head_contents("Booking Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$cbo_currency=str_replace("'","",$cbo_currency);
	$company_id=str_replace("'","",$cbo_company_name);
	$txt_booking_date=str_replace("'","",$txt_booking_date);
	$fabric_cost_dtls_id=implode(",",array_unique(explode(",",str_replace("'","",$cbo_fabric_description))));
	$cbouom=str_replace("'","",$cbouom);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$sql_booking_dtls='SELECT a.booking_no, a.id as booking_dtls_id, a.grey_fab_qnty as req_qty, c.id as bookingid, c.job_no, c.po_break_down_id, c.grey_fab_qnty, c.fin_fab_qnty,c.rate,c.amount,c.gmts_color_id, c.fabric_color_id,c.dia_width, c.pre_cost_fabric_cost_dtls_id as req_id_dts, b.id AS pre_cost_fabric_cost_dtls_id, b.body_part_id, b.color_type_id,	b.fabric_description, c.gsm_weight, b.uom, d.style_ref_no, c.req_id from wo_booking_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_pre_cost_fabric_cost_dtls b  on a.pre_cost_fabric_cost_dtls_id=b.id join wo_po_details_master d on b.job_id=d.id where a.status_active =1 and a.is_deleted=0  and c.booking_no='.$txt_booking_no.' and c.id in ('.$fabric_cost_dtls_id.')';
	//echo $sql_booking_dtls; die;
	$sql_data=sql_select($sql_booking_dtls);


	foreach($sql_data as $row){
        $req_dtls_id[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
    }
    $cu_booking_data_arr=array();
    if(count($req_dtls_id)>0){
        $req_id_str=implode(", ", $req_dtls_id);

        $cu_booking_data=sql_select("SELECT b.pre_cost_fabric_cost_dtls_id as req_id_dtls, sum(b.GREY_FAB_QNTY) as booking_qty from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id in ($req_id_str) and a.booking_type=12 and a.entry_form=630 and a.booking_no<>$txt_booking_no group by b.pre_cost_fabric_cost_dtls_id");
        foreach($cu_booking_data as $row){
            $cu_booking_data_arr[$row[csf('req_id_dtls')]]=$row[csf('booking_qty')];
        }
    }
	?>
	<div>
	<? echo load_freeze_divs ("../../../",$permission);  ?>
        <table width="1320" class="rpt_table" border="0" rules="all">
			<thead>
				<th width="30">SL</th>
				<th width="80">Job No</th>
				<th width="80">Req No</th>
				<th width="80">Style Ref.</th>
				<th width="80">Body Part</th>
				<th width="80">Color Type</th>
				<th width="150">Fabric Description</th>                 
				<th width="60">Width / Dia</th>
				<th width="60">GSM/ Weight</th>
				<th width="60">GMT Color</th>
				<th width="60">Fab. Color</th>
				<th width="60">Uom</th>
				<th width="80">WO Qty</th>
				<th width="80">Rate</th>
				<th width="80">Amount</th>
				<th width="60">Cum. WO Qty</th>
				<th>Balance Qty</th>
			</thead>
        </table>
        <table width="1320" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
            <tbody>
            <?
				$i=1;
				foreach($sql_data as $sql_row){
					$cummulative_qty=0;
					if(array_key_exists($sql_row[csf('req_id_dts')],$cu_booking_data_arr)){
						$cummulative_qty=$cu_booking_data_arr[$sql_row[csf('req_id_dts')]];
					}					
					$balance_qty=$sql_row[csf('req_qty')]-$cummulative_qty;
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr style="text-decoration:none; cursor:pointer" bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
						<td width="30"><?=$i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$sql_row[csf('booking_dtls_id')]; ?>"/>
						</td>
						<td width="80" style="word-break:break-all"><?=$sql_row[csf('job_no')]; ?>
							<input type="hidden" id="txtjobno_<?= $i?>" value="<?= $sql_row[csf('job_no')] ?>" >
							<input type="hidden" id="txtpoid_<?= $i?>" value="<?= $sql_row[csf('po_break_down_id')] ?>" >
							<input type="hidden" id="txtconst_<?= $i?>" value="<?= $sql_row[csf('construction')] ?>" >
							<input type="hidden" id="txtcomp_<?= $i?>" value="<?= $sql_row[csf('composition')] ?>" >
							<input type="hidden" id="txtcolortype_<?= $i?>" value="<?= $sql_row[csf('color_type_id')] ?>" >
							<input type="hidden" id="bookingid_<?= $i?>" value="<?= $sql_row[csf('bookingid')] ?>" >
						</td>
						<td width="80" style="word-break:break-all"><?=$sql_row[csf('booking_no')]; ?>
							<input type="hidden" id="dtlsid_<?= $i?>" value="<?= $sql_row[csf('booking_dtls_id')] ?>" >
							<input type="hidden" id="reqid_<?= $i?>" value="<?= $sql_row[csf('req_id')] ?>" >
						</td>
						<td width="80" style="word-break:break-all"><?=$sql_row[csf('style_ref_no')]; ?></td>
						<td width="80" style="word-break:break-all"><?=$body_part[$sql_row[csf('body_part_id')]]; ?></td>
						<td width="80" style="word-break:break-all"><?=$color_type[$sql_row[csf('color_type_id')]]; ?></td>
						<td width="150" style="word-break:break-all"><?=$sql_row[csf('fabric_description')]; ?></td>
						<td width="60" style="word-break:break-all"><?=$sql_row[csf('dia_width')]; ?>
							<input type="hidden" id="diawidth_<?= $i?>" value="<?= $sql_row[csf('dia_width')] ?>" >
						</td>
						<td width="60" style="word-break:break-all"><?=$sql_row[csf('gsm_weight')]; ?>
							<input type="hidden" id="gsmweight_<?= $i?>" value="<?= $sql_row[csf('gsm_weight')] ?>" >
						</td>
						<td width="60" style="word-break:break-all"><?=$color_library[$sql_row[csf('gmts_color_id')]]; ?>
							<input type="hidden" id="gmtscolorid_<?= $i?>" value="<?= $sql_row[csf('gmts_color_id')] ?>" >
						</td>
						<td width="60" style="word-break:break-all"><?=$color_library[$sql_row[csf('fabric_color_id')]]; ?>
							<input type="hidden" id="fabriccolorid_<?= $i?>" value="<?= $sql_row[csf('fabric_color_id')] ?>" >
						</td>
						<td width="60"><?=$unit_of_measurement[$sql_row[csf('uom')]]; ?></td>
						<td width="80">
							<input type="text" style="width:60px" class="text_boxes" id="txt_wo_qty_<?=$i?>" onchange="calculate_amount(<?= $i ?>,1,this.value)" value="<?= $sql_row[csf('grey_fab_qnty')]; ?>">
						</td>
						<td width="80"><input type="text"  style="width:60px" class="text_boxes" id="txt_rate_<?=$i?>" onchange="calculate_amount(<?= $i ?>,2,this.value)" value="<?= $sql_row[csf('rate')]; ?>"></td>
						<td width="80"><input type="text"  style="width:60px" class="text_boxes" id="txt_amount_<?= $i ?>" value="<?=$sql_row[csf('grey_fab_qnty')]*$sql_row[csf('rate')]; ?>" readonly></td>
						<td width="60" align="center"><?=$cummulative_qty; ?></td>
						<td><input type="text"  style="width:60px" class="text_boxes" id="txt_balance_<?=$i?>" value="<?=$balance_qty; ?>" readonly></td>
					</tr>
					<?
					$i++;
				}
            ?>
            </tbody>
        </table>
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
	
	$companyId=return_field_value("company_id", "wo_booking_mst", "booking_no=".$txt_booking_no." and status_active=1 and is_deleted=0");
	//echo "select company_id from wo_booking_mst where booking_no=".$txt_booking_no." and status_active=1 and is_deleted=0";
	
	$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name='$companyId' and variable_list=66 and status_active=1");
	//echo "select production_entry from variable_settings_production where company_name='$companyId' and variable_list=66 and status_active=1";

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;
	
	$job_level_arr=array(); $fabric_description_array=array(); $color_Arr=array(); $Dia_Arr=array();
	$sql='SELECT a.id as "req_dtls", c.id AS "id",c.job_no AS "job_no", c.po_break_down_id AS "po_break_down_id",c.grey_fab_qnty AS "grey_fab_qnty",
	c.fin_fab_qnty AS "fin_fab_qnty",c.adjust_qty "adjust_qty",c.rate AS "rate",c.amount AS "amount",c.gmts_color_id AS "gmts_color_id",
	c.dia_width AS "dia_width", b.id AS "pre_cost_fabric_cost_dtls_id", b.body_part_id AS "body_part_id",b.color_type_id AS "color_type_id",
	b.fabric_description AS "fabric_description", b.gsm_weight AS "gsm_weight",b.uom AS "uom" from wo_booking_dtls a join wo_booking_dtls c on 
	a.id=c.pre_cost_fabric_cost_dtls_id join wo_pre_cost_fabric_cost_dtls b  on a.pre_cost_fabric_cost_dtls_id=b.id
	where a.status_active =1 and a.is_deleted=0 and c.status_active =1 and c.is_deleted=0  and c.booking_no='.$txt_booking_no.'';
	
	$dataArray=sql_select($sql); $poidArr=array();
	foreach($dataArray as $sql_row){
		$poidArr[$sql_row['po_break_down_id']]=$sql_row['po_break_down_id'];
	}
	
	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where id in (".implode(",",$poidArr).")", "id", "po_number");
	
	foreach($dataArray as $sql_row){
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['job_no'][$sql_row['id']]=$sql_row['job_no'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['po_id'][$sql_row['id']]=$sql_row['po_break_down_id'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['po_number'][$sql_row['id']]=$po_number_arr[$sql_row['po_break_down_id']];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['booking_id'][$sql_row['id']]=$sql_row['id'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['req_dtls'][$sql_row['id']]=$sql_row['req_dtls'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['grey_fab_qnty'][$sql_row['id']]+=$sql_row['grey_fab_qnty'];
		
		if($textile_sales_maintain==1)
		{
			$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['fin_fab_qnty'][$sql_row['id']]+=$sql_row['grey_fab_qnty']-$sql_row['adjust_qty'];
		}
		else
		{
			$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['fin_fab_qnty'][$sql_row['id']]+=$sql_row['fin_fab_qnty']-$sql_row['adjust_qty'];
		}
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['adjust_qty'][$sql_row['id']]+=$sql_row['adjust_qty'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['amount'][$sql_row['id']]+=$sql_row['amount'];
	
		//$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['amount'][$sql_row['id']]+=$sql_row['amount'];
	
		$fabric_description_array[$sql_row['pre_cost_fabric_cost_dtls_id']]=$body_part[$sql_row['body_part_id']].', '.$color_type[$sql_row['color_type_id']].', '.$sql_row['fabric_description'].', '.$sql_row['gsm_weight'].', '.$unit_of_measurement[$sql_row['uom']];
		$color_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['gmts_color_id']]=$color_library[$sql_row['gmts_color_id']];
		$Dia_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['dia_width']]=$sql_row['dia_width'];
	}
	?>
	<table width="1200" class="rpt_table" border="0" rules="all">
        <thead>
            <th width="50"></th>
            <th width="80">Job No</th>
            <th width="80">Po Number</th>
            <th width="250">Fab. Description</th>
            <th width="130">Gmts Color</th>
            <th width="70">Dia</th>
            <th width="100">WO. Qty.</th>
            <th width="100">Adj. Qty.</th>
            <th width="100">Ac. Wo. Qty.</th>
            <th width="60">Rate</th>
            <th width="80">Amount</th>
            <th><input type="checkbox" name="chkdeleteall" id="chkdeleteall" value="2" ><A href="#" onClick="deletedata();">Delete All</A></th>
        </thead>
	</table>
	<div style="max-height:200px; overflow-y:scroll; width:1200px"  align="left">
        <table width="1183" class="rpt_table" id="tbl_fabric_booking_list" border="0" rules="all">
            <tbody>
            <? $i=1;
            $i=1;
			foreach($job_level_arr as $key=>$precost_id){
				$job_no=implode(",",array_unique($precost_id['job_no']));
				$po_break_down_id=implode(",",array_unique($precost_id['po_id']));
				$po_number=implode(",",array_unique($precost_id['po_number']));
				$grey_fab_qnty=array_sum($precost_id['grey_fab_qnty']);
				$adjust_qty=array_sum($precost_id['adjust_qty']);
				$fin_fab_qnty=array_sum($precost_id['fin_fab_qnty']);
				$booking_id=implode(",",array_unique($precost_id['booking_id']));
				$req_dtls_id=implode(",",array_unique($precost_id['req_dtls']));
				//$rate=array_sum($precost_id['rate']);
				$amount=array_sum($precost_id['amount']);
				$rate=$amount/$fin_fab_qnty;
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" >
					<td width="50" align="center"><A href="#" onClick="set_data('<?=$po_break_down_id; ?>','<?=$po_number; ?>','<?=$req_dtls_id; ?>','<?=$booking_id; ?>');">Edit</A></td>
					<td width="80"><?=$job_no; ?></td>
					<td width="80" style="word-break: break-all;word-wrap: break-word;" align="center"><A href="#" onClick="setdata('<?=$po_number; ?>' )">View</A></td>
					<td width="250" style="word-break: break-all;word-wrap: break-word"><?=$fabric_description_array[$key]; ?></td>
					<td width="130" style="word-break: break-all;word-wrap: break-word"><?=implode(",",$color_Arr[$key]); ?></td>
					<td width="70" style="word-break: break-all;word-wrap: break-word" align="center"><?=implode(",",$Dia_Arr[$key]); ?></td>
					<td width="100" align="right"><?=number_format($grey_fab_qnty,4,'.','');?></td>
					<td width="100" align="right"><?=number_format($adjust_qty,4,'.','');?></td>
					<td width="100" align="right"><?=number_format($fin_fab_qnty,4,'.',''); ?></td>
					<td width="60" align="right"><?=number_format($rate,4,'.',''); ?></td>
					<td width="80" align="right"><?=number_format($amount,4,'.','');?></td>
					<td align="center">
						   <input type="checkbox" name="chkdelete_<?=$i; ?>" id="chkdelete_<?=$i; ?>" value="2" ><input type="hidden" id="txtdelete<?=$i; ?>" name="txtdelete<?=$i; ?>" value="<?=$booking_id?>"/> 
					</td>
				</tr>
				<?
				$i++;
			}
            ?>
            </tbody>
        </table>
	</div>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $date_cond=" YEAR(insert_date)";
		else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FBBR', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=12 and $date_cond=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1);

		$field_array="id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, booking_date, delivery_date, pay_mode, supplier_id, source, booking_basis, colar_excess_percent, cuff_excess_percent, currency_id, exchange_rate, isgreyfab_purchase, ship_mode, pay_term, tenor, fabric_composition, attention, remarks, ready_to_approved, shippingmark_breck_down, entry_form, inserted_by, insert_date";
		$data_array ="(".$id.",12,2,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_supplier_name.",".$cbo_source.",".$cbo_basis.",".$txt_colar_excess_percent.",".$txt_cuff_excess_percent.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_greyfab_purch.",".$cbo_shipmode.",".$cbo_payterm.",".$txt_tenor.",".$txt_fabriccomposition.",".$txt_attention.",".$txt_remark.",".$cbo_ready_to_approved.",".$hiddshippingmark_breck_down.",630,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

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
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
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
		$field_array="booking_date*delivery_date*pay_mode*supplier_id*source*colar_excess_percent*cuff_excess_percent*currency_id*exchange_rate*isgreyfab_purchase*ship_mode*pay_term*tenor*fabric_composition*attention*remarks*ready_to_approved*shippingmark_breck_down*updated_by*update_date";
		$data_array ="".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$cbo_source."*".$txt_colar_excess_percent."*".$txt_cuff_excess_percent."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_greyfab_purch."*".$cbo_shipmode."*".$cbo_payterm."*".$txt_tenor."*".$txt_fabriccomposition."*".$txt_attention."*".$txt_remark."*".$cbo_ready_to_approved."*".$hiddshippingmark_breck_down."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		
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
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
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
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		$rID=execute_query( "update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
		$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);

		if($db_type==0)
		{
			if($rID ){
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
			if($rID ){
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

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}

		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die; }
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, is_short, booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, color_type, construction, copmposition, gsm_weight, dia_width, fabric_color_id, gmts_color_id, fin_fab_qnty, grey_fab_qnty, rate, amount, process_loss_percent, req_id, entry_form_id, inserted_by, insert_date, status_active, is_deleted";
		 $j=1;
		 $new_array_color=array(); $poIdChkArr=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			$txtjob="txtjobno_".$i;
			$txtpoid_="txtpoid_".$i;
			$txtpre_cost_fabric_cost_dtls_id="dtlsid_".$i;

			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconst_".$i;
			$txtcompositi_="txtcomp_".$i;
			$txtgsm_weight_="gsmweight_".$i;
			$txtdia_="diawidth_".$i;
			$txtgmtcolor="gmtscolorid_".$i;
			$txtitemcolor="fabriccolorid_".$i;
			$txtwoq="txtwoq_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$reqid="reqid_".$i;
			
			$precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			$colorid=str_replace("'","",$$txtgmtcolor);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$finreqqnty=str_replace("'","",$$txtfinreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);
			$amount=str_replace("'","",$$txtamount);
			$poId=str_replace("'","",$$txtpoid_);
			
			//$poIdChkArr[$poId]=$poId;
			 
			$pLossPer=0;
			$finWoQty=$woq;
			$greyWoQty=$woq;

			if($woq>0){
				if ($j!=1) $data_array1 .=",";
				$data_array1 .="(".$id_dtls.",2,".$update_id.",".$precostid.",".$$txtpoid_.",".$$txtjob.",".$txt_booking_no.",12,".$$txtcolortype_.",".$$txtconstruction_.",".$$txtcompositi_.",".$$txtgsm_weight_.",".$$txtdia_.",".$$txtitemcolor.",".$$txtgmtcolor.",".$finWoQty.",".$greyWoQty.",".$rate.",".$amount.",".$pLossPer.",".$$reqid.",630,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$pre_cost_id_arr[$precostid]=$precostid;
				$id_dtls=$id_dtls+1;
				$j++;
			}
		 }
		 if(count($pre_cost_id_arr)>0) $pre_cost_cond="and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_cost_id_arr).")"; else $pre_cost_cond="";
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "booking_type=12 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $pre_cost_cond") == 1)
		{
			echo "11**0";
			check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}
		 //echo "10**Insert into $field_array1 values $data_array1"; die;
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 //if($rID==1 && $flag==1) $flag=1; else $flag=0;

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
		 else if($db_type==2 || $db_type==1 ){
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
	else if ($operation==1)
	{
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		$field_array_up1="color_type*construction*copmposition*gsm_weight*dia_width*fabric_color_id*gmts_color_id*fin_fab_qnty*grey_fab_qnty*adjust_qty*rate*amount*updated_by*update_date";
		$new_array_color=array(); $str_po_id=""; $plandatachnage=0;
		for ($i=1;$i<=$total_row;$i++){

			$txtjob="txtjobno_".$i;
			$txtpoid_="txtpoid_".$i;
			$txtpre_cost_fabric_cost_dtls_id="dtlsid_".$i;

			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconst_".$i;
			$txtcompositi_="txtcomp_".$i;
			$txtgsm_weight_="gsmweight_".$i;
			$txtdia_="diawidth_".$i;
			$txtgmtcolor="gmtscolorid_".$i;
			$txtitemcolor="fabriccolorid_".$i;
			$txtwoq="txtwoq_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$bookingid="bookingid_".$i;
			
			$precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			$colorid=str_replace("'","",$$txtgmtcolor);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$finreqqnty=str_replace("'","",$$txtfinreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);
			$amount=str_replace("'","",$$txtamount);
			$poId=str_replace("'","",$$txtpoid_);			
			 
			$processLossPer=0;
			$finWoQty=$woq;
			$greyWoQty=$woq;
			$pLossPer=number_format($processLossPer,2,'.','');		

			$poId=str_replace("'","",$$txtpoid);
			 if(str_replace("'",'',$$bookingid)!=""){
				$id_arr[]=str_replace("'",'',$$bookingid);
				$data_array_up1[str_replace("'",'',$$bookingid)] =explode("*",("".$$txtcolortype_."*".$$txtconstruction_."*".$$txtcompositi_."*".$$txtgsm_weight_."*".$$txtdia_."*".$$txtitemcolor."*".$$txtgmtcolor."*".$finWoQty."*".$greyWoQty."*".$$txtadj."*".$rate."*".$amount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				if($str_po_id=="") $str_po_id=$poId; else $str_po_id.='*'.$poId;
			 }
		 }
		 $flag=1;
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
		 if($rID==1 && $flag==1) $flag=1; else $flag=0;		 

        check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
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
	else if($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$fabric_source=0;

	    $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		for ($i=1;$i<=$total_row;$i++){
			$bookingid="bookingid_".$i;
			if(trim(str_replace("'","",$$bookingid))!="")
			{
			 	$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".str_replace("'","",$$bookingid).") and booking_no=$txt_booking_no",0);
			}
		}
		//$rID1=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
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
		else if($db_type==2 || $db_type==1 )
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

function fnc_delivery_date($str_po_id,$bookingNo)
{
	$sqlbook=sql_select("select po_break_down_id from wo_booking_dtls where booking_no='$bookingNo' and status_active=1 and is_deleted=0");
	foreach($sqlbook as $rows)
	{
		if($str_po_id=="") $str_po_id=$rows[csf('po_break_down_id')]; else $str_po_id.='*'.$rows[csf('po_break_down_id')];
	}
	$poids=implode(",",array_filter(array_unique(explode('*',$str_po_id))));

	$sql_tna=sql_select("select min(task_start_date) as task_start_date from tna_process_mst where po_number_id in(".$poids.") and task_number in(73) and is_deleted = 0 and status_active=1");

	$delivery_date="'".$sql_tna[0][csf('task_start_date')]."'";
	$rID_date=execute_query( "update wo_booking_mst set delivery_date=$delivery_date where booking_no='$bookingNo'",0);
	return $rID_date."**".$delivery_date;
	
	
}
if($action=="check_is_booking_used")
{
	$txt_booking_no="'".$data."'";
	if($data!="")
	{
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
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

		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/

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
	}
	exit();
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

if($action=="terms_condition_popup")
{
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
	http.open("POST","fabric_booking_by_requisition_controller.php",true);
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
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=108");// quotation_id='$data'
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
                        <input type="button" id="set_button4" class="image_uploader" style="width:160px;" value="Add More.." onClick="open_extra_terms_popup('fabric_booking_by_requisition_controller.php?action=extra_terms_popup','Terms Condition')" />
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
<fieldset>
    <form autocomplete="off">
    <input style="width:60px;" type="hidden" class="text_boxes"  name="terms_breck_down" id="terms_breck_down" />
    <input style="width:60px;" type="hidden" class="text_boxes"  name="txt_pre_cost_dtls_id" id="txt_pre_cost_dtls_id" />
    <table width="400" class="rpt_table" border="1" rules="all">
        <thead>
            <th width="40">SL</th>
            <th>Terms</th>
        </thead>
        <tbody>
			<?
            $i=1;
            $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=0 and page_id=108");// quotation_id='$data'
            foreach( $data_array as $row )
            {
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr style="text-decoration:none; cursor:pointer" bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>" onClick="js_set_value(<?=$i ;?>,'<?=$row[csf('id')];?>','<?=$row[csf('terms')]; ?>');">
                    <td width="40"><? echo $i;?></td>
                    <td><?=$row[csf('terms')]; ?></td>
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
                <input type="button" class="formbutton" value="Close" onClick="parent.emailwindow.hide();"/>
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

if($action=="shipping_mark_popup")
{
	echo load_html_head_contents("Shipping Mark ","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function js_set_value()
	{
		var shippingmark_breck_down=$('#txt_company_name').val()+'~!~'+$('#txt_address').val()+'~!~'+$('#txt_phone').val()+'~!~'+$('#txt_pax').val()+'~!~'+$('#txt_mail').val()+'~!~'+$('#txt_web').val();
		var shippingmark_breck_down=shippingmark_breck_down.replace(/[ $ & () * ' " \r \n ]/g, '');//replace(/&()'"*<>{}/g,'');
		document.getElementById('hiddshippingmark_breck_down').value=shippingmark_breck_down;
		parent.emailwindow.hide();
	}
    </script>
    </head>
    
    <body>
    <div align="center" style="width:100%;" >
    <div style="display:none"><?=load_freeze_divs ("../../../",$permission); ?></div>
	<? 
	//echo $shippingmark_breck_down;
		if($shippingmark_breck_down=="")
		{
			$compnay_id=$compnay_id;
			$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
			
			$sqlCom="select company_name, email, website, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, contact_no from lib_company where id='$compnay_id' and status_active=1 and is_deleted=0";
			$sqlComRes=sql_select($sqlCom);
			$comName=$comAdd=$comPhone=$comPax=$comEmail=$comWeb="";
			foreach( $sqlComRes as $row)
			{
				$comName=$row[csf("company_name")];
				
				if($row[csf("level_no")])		$level_no 	= $row[csf("level_no")].', ';
				if($row[csf("plot_no")])		$plot_no 	=$row[csf("plot_no")].', ';
				if($row[csf("road_no")]) 		$road_no 	=$row[csf("road_no")].', ';
				if($row[csf("block_no")]!='')	$block_no 	=$row[csf("block_no")].', ';
				if($row[csf("zip_code")]!='')	$zip_code 	= ' -'.$row[csf("zip_code")].' , ';
				if($row[csf("city")]!='') 		$city 		= ($zip_code!="")?$row[csf("city")]:$row[csf("city")]." ,";
				if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
				
				$comAdd=$level_no.$plot_no.$road_no.$block_no.$city.$zip_code.$country;
				$comPhone=$row[csf("contact_no")];
				$comEmail=$row[csf("email")];
				$comWeb=$row[csf("website")];
			}
			$shippingmark_breck_down=$comName.'~!~'.$comAdd.'~!~'.$comPhone.'~!~'.$comPax.'~!~'.$comEmail.'~!~'.$comWeb;
		}
		$data=explode("~!~",$shippingmark_breck_down); 
    ?>
    <fieldset>
        <form autocomplete="off">
        <input style="width:60px;" type="hidden" class="text_boxes" name="hiddshippingmark_breck_down" id="hiddshippingmark_breck_down" />
            <table width="400" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Shipping Marks [One Side]</th>
                </thead>
                <tr>
                    <td><input style="width:380px;" type="text" class="text_boxes" name="txt_company_name" id="txt_company_name" value="<?=$data[0]; ?>" placeholder="Company Name" /></td>
                </tr>
                <tr>
                    <td><input style="width:380px;" type="text" class="text_boxes" name="txt_address" id="txt_address" value="<?=$data[1]; ?>" placeholder="Address" /></td>
                </tr>
                <tr>
                    <td><input style="width:380px;" type="text" class="text_boxes" name="txt_phone" id="txt_phone" value="<?=$data[2]; ?>" placeholder="Phone" /></td>
                </tr>
                <tr>
                    <td><input style="width:380px;" type="text" class="text_boxes" name="txt_pax" id="txt_pax" value="<?=$data[3]; ?>" placeholder="Pax" /></td>
                </tr>
                <tr>
                    <td><input style="width:380px;" type="text" class="text_boxes" name="txt_mail" id="txt_mail" value="<?=$data[4]; ?>" placeholder="Mail" /></td>
                </tr>
                <tr>
                    <td><input style="width:380px;" type="text" class="text_boxes" name="txt_web" id="txt_web" value="<?=$data[5]; ?>" placeholder="Web" /></td>
                </tr>
                <tr>
                    <td align="center"  class="button_container">
                    	<input type="button" class="formbutton" value="Close" onClick="js_set_value();"/>
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

if($action=="rmg_process_loss_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function js_set_value_set()
	{
		var cutting_per=$('#cutting_per').val();
		if(cutting_per=="") cutting_per=0;
		
		var embbroidery_per=$('#embbroidery_per').val();
		if(embbroidery_per=="") embbroidery_per=0;
		
		var printing_per=$('#printing_per').val();
		if(printing_per=="") printing_per=0;
		
		var wash_per=$('#wash_per').val();
		if(wash_per=="") wash_per=0;
		
		var sew_per=$('#sew_per').val();
		if(sew_per=="") sew_per=0;
		
		var fin_per=$('#fin_per').val();
		if(fin_per=="") fin_per=0;
		
		var knitt_per=$('#knitt_per').val();
		if(knitt_per=="") knitt_per=0;
		
		var dying_per=$('#dying_per').val();
		if(dying_per=="") dying_per=0;
		
		var extracutt_per=$('#extracutt_per').val();
		if(extracutt_per=="") extracutt_per=0;
		
		var other_per=$('#other_per').val();
		if(other_per=="") other_per=0;
		
		var neck_sleev_printing_per=$('#neck_sleev_printing_per').val();
		if(neck_sleev_printing_per=="") neck_sleev_printing_per=0;
		
		var gmt_other_per=$('#gmt_other_per').val();
		if(gmt_other_per=="") gmt_other_per=0;
		
		var yarn_dyeing_per=$('#yarn_dyeing_per').val();
		if(yarn_dyeing_per=="") yarn_dyeing_per=0;
		
		var all_over_print_per=$('#all_over_print_per').val();
		if(all_over_print_per=="") all_over_print_per=0;
		
		var lay_wash_per=$('#lay_wash_per').val();
		if(lay_wash_per=="") lay_wash_per=0;
		
		var gmtfinish_per=$('#gmtfinish_per').val();
		if(gmtfinish_per=="") gmtfinish_per=0;
		
		var processloss_breck_down=cutting_per+'_'+embbroidery_per+'_'+printing_per+'_'+wash_per+'_'+sew_per+'_'+fin_per+'_'+knitt_per+'_'+dying_per+'_'+extracutt_per+'_'+other_per+'_'+neck_sleev_printing_per+'_'+gmt_other_per+'_'+yarn_dyeing_per+'_'+all_over_print_per+'_'+lay_wash_per+'_'+gmtfinish_per;
		document.getElementById('processloss_breck_down').value=processloss_breck_down;
		parent.emailwindow.hide();
	}
    </script>
    </head>
    
    <body>
    <div align="center" style="width:100%;" >
     <? echo load_freeze_divs ("../../../",$permission);
     $data=explode("_",$processloss_breck_down);
     ?>
    <fieldset>
        <form autocomplete="off">
        <input style="width:60px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" />
            <table width="180" class="rpt_table" border="1" rules="all">
                <tr>
                    <td width="130"> Cut Panel rejection <!--  Extra Cutting %  breack Down 8--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="extracutt_per" id="extracutt_per" value="<?=$data[8]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Chest Printing <!-- Printing % breack Down 2--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="printing_per" id="printing_per" value="<?=$data[2]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Neck/Sleeve Printing <!-- new breack Down 10--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="neck_sleev_printing_per" id="neck_sleev_printing_per" value="<?=$data[10]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Embroidery  <!-- Embroidery  % breack Down 1--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="embbroidery_per" id="embbroidery_per" value="<?=$data[1]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Sewing/Input <!-- Sewing % breack Down 4--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="sew_per" id="sew_per" value="<?=$data[4]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Garments Wash  <!-- Washing % breack Down 3--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="wash_per" id="wash_per" value="<?=$data[3]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Gmts Finishing  <!-- Washing % breack Down 3--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="gmtfinish_per" id="gmtfinish_per" value="<?=$data[15]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Others  <!-- New breack Down 11--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="gmt_other_per" id="gmt_other_per" value="<?=$data[11]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Knitting   <!-- Knitting % breack Down 6--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="knitt_per" id="knitt_per" value="<?=$data[6]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Yarn Dyeing   <!-- New breack Down 12--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="yarn_dyeing_per" id="yarn_dyeing_per" value="<?=$data[12]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Dyeing & Finishing   <!-- Finishing % breack Down 5--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="fin_per" id="fin_per" value="<?=$data[5]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">All Over Print  <!-- New breack Down 13--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="all_over_print_per" id="all_over_print_per" value="<?=$data[13]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Lay Wash (Fabric)  <!-- New breack Down 14--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="lay_wash_per" id="lay_wash_per" value="<?=$data[14]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Dying  <!--breack Down 7--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric"  name="dying_per" id="dying_per" value="<?=$data[7]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Cutting (Febric) <!-- Cutting % breack Down 0--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric"  name="cutting_per" id="cutting_per" value="<?=$data[0]; ?>" /></td>
                </tr>
                <tr>
                    <td width="130">Others <!--breack Down 9--></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric"  name="other_per" id="other_per" value="<?=$data[9]; ?>" /></td>
                </tr>
                <tr>
                    <td align="center"  class="button_container" colspan="2">
                    	<input type="button" class="formbutton" value="Close" onClick="js_set_value_set();"/>
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
                        <th width="130">PO Number</th>
                        <th>Gmts Color</th>
                        <th>Gmts Size</th>
                        <th>Item Size</th>
                        <th>Gmts Qty (Pcs)</th>
                        <th>Excess %</th>
                        <th><? echo $body_part[$bodyPart]; ?> Qty (Pcs)</th>
                    </tr>
                </thead>
                <tbody>
            <?
			    $i=1;
				$gmt_tot_pcs=0;
				$gmt_tot = 0;
				foreach($sqldata as $row){
					$excess_per=0;
					if($row[csf('body_part_id')]==2){
						$excess_per=$row[csf('colar_excess_percent')];
					}
					if($row[csf('body_part_id')]==3){
						$excess_per=$row[csf('cuff_excess_percent')];
					}
					$gmts_qty=$row[csf('plan_cut_qnty')];
					//$totQtyPcs+=$gmts_qty;
					$qty=0;
					if($body_part_type==50){
						$qty=$gmts_qty*2;
					}else{
						$qty=$gmts_qty*1;
					}

					//$totQty+=$qty;
					
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
				$gmt_tot_pcs+=$book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['gmts_qty'];
				$gmt_tot+=$book_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty'];;
				if($dtls_id>0) $button_id=1;else $button_id=0;
				}
				//echo $button_id.'AAAAAAAAAAAA';
				?>
                </tbody>
				<tfoot>
					<th colspan="4">Total</th>
					<th style="text-align:center"><input type="text" name="txtTotQtyPcs" id="txtTotQtyPcs" class="text_boxes_numeric" style="width:60px" value=" <? echo $gmt_tot_pcs; ?>" disabled/></th>
					<th></th>
					<th style="text-align:center"><input type="text" name="txtTotQty" id="txtTotQty" class="text_boxes_numeric" style="width:60px" value="<? echo $gmt_tot_pcs; ?>" disabled/></th>							
				</tfoot>
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
		disconnect($con);	die;
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
		 $flag=1;
		 if(count($data_array_up)>0){
		 	$rID=execute_query(bulk_update_sql_statement("wo_booking_colar_culff_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
		 	if($rID) $flag=1; else $flag=0;
		 }
		 
		 if($data_array!='')
		 {
		  $rID1=sql_insert("wo_booking_colar_culff_dtls",$field_array,$data_array,0);
		  if($rID1) $flag=1; else $flag=0;
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
			disconnect($con);die;
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
                 <? echo $row[csf('gmts_qty')]; 
				 $tot_gmt_qty+=$row[csf('gmts_qty')];
				 ?>
                </td>
                <td align="right">
                <? echo number_format($row[csf('excess_per')],2); ?>
                </td>
                <td align="right">
                 <? echo $row[csf('qty')]; 
				 $tot_qty+=$row[csf('qty')];
				 ?>
                </td>
                </tr>
                <?
				}
				?>
                </tbody>
				<tfoot>
					<tr>
					<td colspan="3" align="right">Total</td>
					<td align="right"><? echo $tot_gmt_qty; ?></td>
					<td align="right">&nbsp;</td>
					<td align="right"><? echo $tot_qty; ?></td>
					</tr>
				</tfoot>
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
		disconnect($con);die;
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
show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+document.getElementById('cbo_level').value+'**'+<? echo "'$permission'";?>,'load_color_size_form','form_data_con','fabric_booking_by_requisition_controller','');
}

function show_list()
{
	//echo $txt_booking_no.'fff';
	var booking_no='<? echo $txt_booking_no;?>';

	show_list_view(booking_no,'show_list_view','list_view_con','fabric_booking_by_requisition_controller','');
}
function show_sub_form_with_data(booking_no,fabric_cost_id){
	document.getElementById('cbo_fabric_part').value=fabric_cost_id;
	document.getElementById('booking_no').value=booking_no;
	show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+'00'+'**'+<? echo "'$permissions'";?>,'load_color_size_form','form_data_con','fabric_booking_by_requisition_controller','');
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
	calculate_tot_qnty();
}
function calculate_tot_qnty() {
			var rowCount=$('#colar_cuff_tbl tbody tr').length;
			var tot_gmtQty=0;
			var tot_gmtpartQty=0;
			for(var j=1; j<=rowCount; j++)
			{
					var gmtQty=$("#gmts_qty_"+j).val()*1;
					var gmtpartQty=$("#qty_"+j).val()*1;
					var tot_gmtQty=gmtQty+tot_gmtQty;
					var tot_gmtpartQty=gmtpartQty+tot_gmtpartQty;
			}
			//alert(tot_gmtpartQty);
			$('#txtTotQtyPcs').val(tot_gmtQty);
			$('#txtTotQty').val(tot_gmtpartQty);
		}

function fnc_colar_culff_dtls( operation ){
		freeze_window(operation);
		var delete_cause='';
		var booking_no=document.getElementById('booking_no').value;
		var booking=return_global_ajax_value(booking_no, 'check_booking_approved', '', 'fabric_booking_by_requisition_controller');
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
		//$('#txt_tot_row').val(row_num);
		var data="action=save_update_delete_colar_culff_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;

		http.open("POST","fabric_booking_by_requisition_controller.php",true);
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

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
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
	
	 $up_sql="select approval_cause,id from fabric_booking_approval_cause where booking_id=".$wo_id." and status_active=1 and is_deleted=0 order by id desc";
	//echo "select approval_cause from fabric_booking_approval_cause where booking_id=".$wo_id." and status_active=1 and is_deleted=0";
	$up_nameArray_request=sql_select($up_sql);
	foreach($up_nameArray_request as $row)
	{
		$unappv_req=$row[csf('approval_cause')];
	}
	
	$un_sql_request="select MAX(id) as id,max(approval_no) as approved_no from fabric_booking_approval_cause where entry_form=7  and booking_id=".$wo_id." and approval_type=2 and status_active=1 and is_deleted=0";// page_id='$menu_id'
		//echo $sql_request;
		$nameArray_un_request=sql_select($un_sql_request);
		$cause_approved_no='';
		foreach($nameArray_un_request as $approw)
		{
			$cause_approved_no=$approw[csf("approved_no")];
		}
		
		$max_approved_no=return_field_value("max(approved_no) as max_approved_no", "approval_history", "mst_id='".$wo_id."'  and entry_form=7","max_approved_no");
		
	 // echo $max_approved_no.'_'.$cause_approved_no.'_'.$unappv_req;
	 if(count($up_nameArray_request)>0 && $max_approved_no!=$cause_approved_no)
	{ 
		$unappv_req='';$button_chk=0;
	}
	else { $unappv_req=$unappv_req;$button_chk=1;}
	


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
				http.open("POST","fabric_booking_by_requisition_controller.php",true);
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

				//set_button_status(1, permission, 'fnc_appv_entry',1);
				set_button_status(1, permission, 'fnc_appv_entry',1);
				document.getElementById('hidden_appv_cause').value=reponse[3];
				parent.emailwindow.hide();
				
				release_freezing();

				//generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
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
                    	<textarea name="unappv_request" id="unappv_request" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character" ><? echo str_replace("<br />","\n",$unappv_req); ?></textarea>
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
                            if(count($up_nameArray_request)>0 && $button_chk==1)
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
              <? //fabric_booking_approval_cause
			// $sqlHis="select approval_cause from fabric_booking_approval_cause where entry_form=7 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
			 $sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=7 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
			$sqlHisRes=sql_select($sqlHis);
			//$sqlHisRes=sql_select($sqlHis);
		?>
		<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th>Unapproved Request History</th>
			</thead>
		</table>
		<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
			<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
			<?
			$i=1;
			foreach($sqlHisRes as $hrow)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
					<td width="30"><?=$i; ?></td>
					<td style="word-break:break-all"><?=$hrow[csf('approval_cause')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			</table>
		</div>
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
	
	//=============Issue Id=29451 for  Urmi=====================
	
	$approved_no=return_field_value("MAX(approved_no) as approved_no","approval_history","entry_form=7 and mst_id=$wo_id","approved_no");
	//echo "10**select approval_cause from approval_cause_refusing_his where approval_cause='".str_replace("'", "", $unappv_request)."' and entry_form=12 and booking_id='".str_replace("'", "", $wo_id)."' and approval_type=2 and status_active=1 and is_deleted=0"; die;
	$flag=1;
	/*if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".str_replace("'", "", $unappv_request)."' and entry_form=7 and booking_id='".str_replace("'", "", $wo_id)."' and approval_type=2 and status_active=1 and is_deleted=0" )==1)
	{
		//
	}
	else
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
		$idpre=return_field_value("max(id) as id", "fabric_booking_approval_cause", "booking_id=".$wo_id." and entry_form=7 and approval_type=2 group by booking_id","id");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, not_approval_cause)
			select '', id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, not_approval_cause from fabric_booking_approval_cause where booking_id=".$wo_id." and approval_type=2 and entry_form=7 and id ='$idpre'";
		
		if(count($sqlHis)>0)
		{
			$rID3=execute_query($sqlHis,0);
			if($flag==1)
			{
				if($rID3==1) $flag=1; else $flag=0;
			}
		}
	}*/
	

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$approved_no=return_field_value("MAX(approved_no) as approved_no","approval_history","entry_form=7 and mst_id=$wo_id","approved_no");

		$unapproved_request=return_field_value("id","fabric_booking_approval_cause","page_id=$page_id and entry_form=7 and user_id=$user_id and booking_id=$wo_id and approval_type=2 and approval_no=$approved_no");

		if($unapproved_request=="")
		{
			$textToStore = nl2br(htmlentities(str_replace("'","",$unappv_request), ENT_QUOTES, 'UTF-8'));

			$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

			$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,2,".$approved_no.",'".$textToStore."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
			//echo $rID; die;

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
			$update_id=return_field_value("max(id) as id", "fabric_booking_approval_cause", "booking_id=".$wo_id." and entry_form=7 and approval_type=2 group by booking_id","id");

			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*7*".$user_id."*".$wo_id."*2*".$approved_no."*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

			 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$update_id."",0);

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
			else if($db_type==2)
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
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$update_id=return_field_value("max(id) as id", "fabric_booking_approval_cause", "booking_id=".$wo_id." and entry_form=7 and approval_type=2 group by booking_id","id");

			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*7*".$user_id."*".$wo_id."*2*".$approved_no."*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

			 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$update_id."",0);

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
if ($action=="save_update_delete_unappv_request_not")
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
			$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,2,".$approved_no.",'".$textToStore."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
			//echo $rID; die;

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
			$data_array="".$page_id."*7*".$user_id."*".$wo_id."*2*".$approved_no."*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	exit();
}


if ($action=="send_mail_report_setting_first_select"){
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id =35 and is_deleted=0 and status_active=1");
	echo $print_report_format;
	exit();
}

if($action=="dtm_popup")
{
	echo load_html_head_contents("DTM Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	?>
	<script>

	function trims_popup(page_link,title,i)
	{
		//var job_no=$('#txt_job_no').val();
		var booking_no=$('#txt_booking_no').val();
		//var selected_no=$('#txt_order_no_id').val();
		var selected_no=$('#poid_'+i).val();
		var job_no=$('#jobno_'+i).val();
		var fabric=$('#fabric_'+i).val();
		var color=$('#color_'+i).val();
		var fabric_cost_id=$('#fabric_cost_id_'+i).val();


		if(booking_no=='')
		{
			alert('Booking  Not Found.');
			$('#txt_booking_no').focus();
			return;
		}

		page_link=page_link+'&job_no='+job_no+'&booking_no='+booking_no+'&selected_no='+selected_no+'&fabric='+fabric+'&color='+color+'&fabric_cost_id='+fabric_cost_id+'&index='+i;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../../')

	}
	</script>
	<?
	$dtm_arr=array();
	$sql=sql_select("select pre_cost_fabric_cost_id,fabric_color,sum(qty) as qty  from wo_dye_to_match where booking_no='$booking_no' and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_id,fabric_color");
	foreach($sql as $row){
		$dtm_arr[$row[csf('pre_cost_fabric_cost_id')]][$row[csf('fabric_color')]]=$row[csf('qty')];
	}


	$trims_matches_sql=sql_select("SELECT a.id, a.body_part_id, a.composition, a.construction, a.gsm_weight, d.fabric_color_id, d.gmts_color_id, min(c.id) as cid, sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty, d.job_no, d.po_break_down_id FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and  b.po_break_down_id=d.po_break_down_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no ='$booking_no' and d.status_active=1 and  c.status_active=1  and a.status_active=1 and d.is_deleted=0 group by a.id, a.body_part_id, a.composition, a.construction, a.gsm_weight, d.fabric_color_id , d.gmts_color_id, d.job_no, d.po_break_down_id order by a.id, cid ");
	?>



	</head>
	<body>
	<div align="center" style="width:100%;" >
	<input type="hidden" id="txt_job_no" name="txt_job_no" value="<? echo $job_no;  ?>"/>
	<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo $booking_no;  ?>"/>
	<input type="hidden" id="txt_order_no_id" name="txt_order_no_id" value="<? echo $selected_no;  ?>"/>
	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_trims_dyes_match1" rules="all">
	<thead>
	  <tr>
		<th width="40">S/L</th>
		<th width="60">Job NO</th>
		<th width="300">Fabric Driscription</th>
		<th width="90">Color</th>
		<th width="100">Fabric Qnty.</th>
		<th width="100">Trims</th>
	  </tr>
	</thead>
	<tbody>
	<?

	$i=1;
	foreach($trims_matches_sql as $row)
	 {
	 ?>
	  <tr>
	  	<td width="40"><? echo $i; ?></td>
	  	<td width="60">
			<? echo $row[csf('job_no')]; ?>
			<input type="hidden" id="jobno_<?= $i ?>" value="<?= $row[csf('job_no')]  ?>" >
			<input type="hidden" id="poid_<?= $i ?>" value="<?= $row[csf('po_break_down_id')]  ?>" >
		</td>
	  	<td width="300">
        <input class="text_boxes" type="text" style="width:300px;"  name="fabric_<? echo $i; ?>" id="fabric_<? echo $i; ?>" value="<? echo $body_part[$row[csf('body_part_id')]].",".$row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')];?>" readonly/>
         <input class="text_boxes" type="hidden" style="width:300px;"  name="fabric_cost_id_<? echo $i; ?>" id="fabric_cost_id_<? echo $i; ?>" value="<? echo $row[csf('id')];?>" readonly/>
        </td>
	  	<td width="90">
        <? echo $color_library[$row[csf('fabric_color_id')]];?>
        <input class="text_boxes" type="hidden" style="width:150px;"  name="color_<? echo $i; ?>" id="color_<? echo $i; ?>" value="<? echo $row[csf('gmts_color_id')];?>" readonly/>
        </td>
	  	<td width="100" align="right">
		<? echo number_format($row[csf('fin_fab_qnty')],4);?>
        </td>
	  	<td width="100"><input class="text_boxes" type="text" style="width:100px;"  name="trims_<? echo $i; ?>" id="trims_<? echo $i; ?>" value="<? echo $dtm_arr[$row[csf('id')]][$row[csf('fabric_color_id')]] ?>" onDblClick="trims_popup('fabric_booking_by_requisition_controller.php?action=trims_popup','Trims Item',<? echo $i ?>)" readonly/></td>
	  </tr>
	  <? $i++;

	  } ?>
	</tbody>
	</table>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="trims_popup")
{
	echo load_html_head_contents("DTM Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function fnc_fabric_dye_to_match( operation )
	{

		var job_no=$('#txt_job_no').val();
		var booking_no=$('#txt_booking_no').val();
		var selected_no=$('#txt_order_no_id').val();
		var fabric=$('#fabric').val();
		var color=$('#color').val();
		var fabric_cost_id=$('#fabric_cost_id').val();
		var index=$('#index').val();

			var row_num=$('#tbl_trims_dyes_match tbody tr').length;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{

				data_all=data_all+get_submitted_data_string('trim_group_'+i+'*pre_cost_trim_cost_id_'+i+'*dyeqty_'+i+'*color_'+i,"../../../",i);

			}
			//alert(data_all);
			var data="action=save_update_delete_dye_to_match&operation="+operation+'&total_row='+row_num+data_all+'&booking_no='+booking_no+'&fabric='+fabric+'&color='+color+'&fabric_cost_id='+fabric_cost_id;
			freeze_window(operation);
			http.open("POST","fabric_booking_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}

	function fnc_fabric_booking_terms_condition_reponse()
	{

		if(http.readyState == 4)
		{
				var reponse=trim(http.responseText).split('**');
				if(trim(reponse[0])=='approved')
				{
					alert("This booking is approved");
					release_freezing();
					return;
				}

				if(trim(reponse[0])=='papproved'){
					alert("This booking is Partial approved");
					release_freezing();
					return;
				}

				if (reponse[0].length>2) reponse[0]=10;
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					var index=$('#index').val();
					parent.document.getElementById('trims_'+index).value=reponse[1];
					parent.emailwindow.hide();
				}
		}
	}
	</script>
	<?
	$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$dtm_arr=array();
	$dtm_arr_item_color=array();
	$sql=sql_select("select pre_cost_fabric_cost_id,fabric_color,precost_trim_cost_id,item_color,sum(qty) as qty from wo_dye_to_match where booking_no='$booking_no' and pre_cost_fabric_cost_id='$fabric_cost_id' and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_id,fabric_color,item_color,precost_trim_cost_id");

	foreach($sql as $row){
		$dtm_arr[$row[csf('pre_cost_fabric_cost_id')]][$row[csf('fabric_color')]][$row[csf('precost_trim_cost_id')]]=$row[csf('qty')];
		$dtm_arr_item_color[$row[csf('pre_cost_fabric_cost_id')]][$row[csf('fabric_color')]][$row[csf('precost_trim_cost_id')]]=$row[csf('item_color')];
	}
	$trims_matches_sql=sql_select("select a.id,a.job_no,a.trim_group,a.description,a.cons_uom FROM wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b,  wo_booking_dtls c WHERE a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id=c.po_break_down_id and c.booking_no ='$booking_no' and b.po_break_down_id in($selected_no) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.job_no,a.trim_group,a.description,a.cons_uom");

		$condition= new condition();
		if(str_replace("'","",$job_no) !=''){
		$condition->job_no("='$job_no'");
		}
		if(str_replace("'","",$selected_no) !=''){
			$condition->po_id("in($selected_no)");
		}
		$condition->init();
		$trim= new trims($condition);
		//echo $trim->getQuery();
		$totalqtyarray_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
		$totalqtyarray_arr2=$trim->getQtyArray_by_jobItemidDescriptionGmtcolorAndSizeid();
	?>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<? echo load_freeze_divs ("../../../",$permission);  ?>
	<fieldset>
	<form id="dtm_1">
	<input type="hidden" id="txt_job_no" name="txt_job_no" value="<? echo $job_no;  ?>"/>
	<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo $booking_no;  ?>"/>
	<input type="hidden" id="txt_order_no_id" name="txt_order_no_id" value="<? echo $selected_no;  ?>"/>
	<input type="hidden" id="fabric" name="txt_order_no_id" value="<? echo $fabric;  ?>"/>
	<input type="hidden" id="color" name="color" value="<? echo $color;  ?>"/>
	<input type="hidden" id="fabric_cost_id" name="fabric_cost_id" value="<? echo $fabric_cost_id;  ?>"/>
	<input type="hidden" id="index" name="index" value="<? echo $index;  ?>"/>
	<table width="700" cellspacing="0" class="rpt_table" border="0" id="tbl_trims_dyes_match" rules="all">
	<thead>
	  <tr>
		<th width="40">S/L</th>
		<th width="150">Item Group</th>
		<th width="150">Item Color</th>
        <th width="150">Item Driscription</th>
		<th width="100">Req. Qty.</th>
		<th width="60">Uom</th>
        <th width="60">Dye Qnty</th>
	  </tr>
	</thead>
	<tbody>
	<?

	$i=1;
	foreach($trims_matches_sql as $row)
	 {
	 $item_color=$dtm_arr_item_color[$fabric_cost_id][$color][$row[csf('id')]];
	 if(empty($item_color)){
		 $item_color=$color;
	 }
	 $qnty=array_sum($totalqtyarray_arr2[$row[csf('job_no')]][$row[csf('trim_group')]][$row[csf('description')]][$item_color]);
	 ?>
	  <tr>
	  	<td width="40"><? echo $i; ?></td>
	  	<td width="150">
        <? echo $lib_item_group_arr[$row[csf('trim_group')]];?>
        <input class="text_boxes" type="hidden" style="width:150px;"  name="trim_group_<? echo $i; ?>" id="trim_group_<? echo $i; ?>" value="<? echo $row[csf('trim_group')];?>" readonly/>
         <input class="text_boxes" type="hidden" style="width:150px;"  name="pre_cost_trim_cost_id_<? echo $i; ?>" id="pre_cost_trim_cost_id_<? echo $i; ?>" value="<? echo $row[csf('id')];?>" readonly/>
        </td>
	  	<td width="150">
        <? //echo $color_library[$color];?>
        <input class="text_boxes" type="text" style="width:150px;"  name="color_<? echo $i; ?>" id="color_<? echo $i; ?>" value="<? echo $color_library[$item_color];?>"/>
        </td>
	  	<td width="120">
		<? echo $row[csf('description')];?>
        </td>
	  	<td width="100" title="Job and trim item wise=<?=$totalqtyarray_arr[$row[csf('job_no')]][$row[csf('id')]]?>">
        <input class="text_boxes_numeric" type="text" style="width:100px;"  name="reqqty_<? echo $i; ?>" id="reqqty_<? echo $i; ?>" value="<? echo $qnty;//$totalqtyarray_arr[$row[csf('job_no')]][$row[csf('id')]]; ?>" readonly/>
        </td>
        <td width="60">
        <input class="text_boxes" type="text" style="width:60px;"  name="uom_<? echo $i; ?>" id="uom_<? echo $i; ?>" value="<? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>" readonly/>
        </td>
        <td width="60">
        <input class="text_boxes_numeric" type="text" style="width:60px;"  name="dyeqty_<? echo $i; ?>" id="dyeqty_<? echo $i; ?>" value="<? echo $dtm_arr[$fabric_cost_id][$color][$row[csf('id')]] ?>"/>
        </td>
	  </tr>
	  <? $i++;

	  } ?>
	</tbody>
	</table>
    </form>
    <table width="650" cellspacing="0" class="" border="0">
        <tr>
            <td align="center" width="100%" class="button_container">
            <?
            echo load_submit_buttons( $permission, "fnc_fabric_dye_to_match", 0,0 ,"reset_form('dtm_1','','','','')",1) ;
            ?>
            </td>
        </tr>
    </table>
    </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="save_update_delete_dye_to_match")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$booking_id=return_field_value( "id", "wo_booking_mst","booking_no ='$booking_no'");

		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no='$booking_no'");
		 foreach($sql as $row){
           // if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		    $is_approved=$row[csf('is_approved')];
        }
        if($is_approved==1) { echo "approved**".str_replace("'","",$txt_booking_no); disconnect($con);die; }
		else if($is_approved==3) { echo "papproved**".str_replace("'","",$txt_booking_no); disconnect($con);die; }

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		$id=return_next_id( "id", "wo_dye_to_match", 1 ) ;
		$field_array="id,booking_id,booking_no,pre_cost_fabric_cost_id,fabric_color,item_color,precost_trim_cost_id,item_group,qty";//,status_active,is_deleted
		$total_dye_qty=0;
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++)
		{
			$trim_group="trim_group_".$i;
			$pre_cost_trim_cost_id="pre_cost_trim_cost_id_".$i;
			$dyeqty="dyeqty_".$i;
			$item_color="color_".$i;
			
			if(str_replace("'","",$$item_color)!="")
			{
				if (!in_array(str_replace("'","",$$item_color),$new_array_color)){
					$color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","118");
					$new_array_color[$color_id]=str_replace("'","",$$item_color);
				}
				else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			}
			else $color_id=0;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$booking_id.",'".$booking_no."','".$fabric_cost_id."','".$color."','".$color_id."',".$$pre_cost_trim_cost_id.",".$$trim_group.",".$$dyeqty.")";
			$total_dye_qty+=str_replace("'"," ",$$dyeqty);
			$id=$id+1;
		}
		//echo "10**insert into wo_dye_to_match (".$field_array.") values ".$data_array;die;
		$rID_de3=execute_query( "delete from wo_dye_to_match where  pre_cost_fabric_cost_id ='".$fabric_cost_id."' and fabric_color= '".$color."'",0);
		$rID=sql_insert("wo_dye_to_match",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_de3){
			mysql_query("COMMIT");
			echo "0**".$total_dye_qty;
			}
			else{
			mysql_query("ROLLBACK");
			echo "10**".$total_dye_qty;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3){
			oci_commit($con);
			echo "0**".$total_dye_qty;
			}
			else{
			oci_rollback($con);
			echo "10**".$total_dye_qty;
			}
		}
		disconnect($con);
		die;
	}
}
if($action=="print_booking") //ISD-24-00198 4H
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	//$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$path=str_replace("'","",$path);
	if($path!="") $path=$path; else $path="../../";

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' or form_name='knit_order_entry' and file_type=1",'master_tble_id','image_location');
	$fabimge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' or form_name='fabric_color_img' and file_type=1",'master_tble_id','image_location');
	
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	//$marchentrArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$teamArr =$marchentrArr=array();
	$teamSql = sql_select("select a.id, b.id as bid, a.team_name, a.team_leader_name, a.team_leader_email, a.user_tag_id, b.user_tag_id as user_id, b.team_member_name, b.team_member_email from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.status_active=1 and a.is_deleted=0 ");
	foreach($teamSql as $trow)
	{
		$teamArr[$trow[csf('id')]]['teamname']=$trow[csf('team_name')];
		$teamArr[$trow[csf('id')]]['team_leader_name']=$trow[csf('team_leader_name')];
		$teamArr[$trow[csf('id')]]['team_email']=$trow[csf('team_leader_email')];
		$marchentrArr[$trow[csf('bid')]]['team_member_name']=$trow[csf('team_member_name')];
		$marchentrArr[$trow[csf('bid')]]['team_member_email']=$trow[csf('team_member_email')];
		if($trow[csf('user_tag_id')]==$trow[csf('user_id')])
		{
			$teamArr[$trow[csf('id')]]['team_leader_email']=$trow[csf('team_member_email')];
		}
	}
	unset($teamSql);
	
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer  where status_active=1 and is_deleted=0",'id','buyer_name');
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;

	$job_data_arr=array();
	$nameArray_buyer=sql_select( "select a.style_ref_no, a.style_description, a.job_no, a.buyer_name, a.team_leader, a.dealing_marchant, a.total_set_qnty, c.po_number from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c where a.job_no=b.job_no and a.id=c.job_id and b.po_break_down_id=c.id and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0 order by a.job_no ");
	$jobAll=$styleAll=$poAll="";
	foreach ($nameArray_buyer as $result_buy)
	{
		if($jobAll=="") $jobAll=$result_buy[csf('job_no')]; else $jobAll.=','.$result_buy[csf('job_no')];
		if($styleAll=="") $styleAll=$result_buy[csf('style_ref_no')]; else $styleAll.='**'.$result_buy[csf('style_ref_no')];
		if($poAll=="") $poAll=$result_buy[csf('po_number')]; else $poAll.='**'.$result_buy[csf('po_number')];
		
		$job_data_arr[$result_buy[csf('job_no')]]['style']=$result_buy[csf('style_ref_no')];
		$job_data_arr[$result_buy[csf('job_no')]]['job_no_in']="'".$result_buy[csf('job_no')]."'";
		$dealing_marchant.=$marchentrArr[$result_buy[csf('dealing_marchant')]]['team_member_name'].',';
		$team_name.=$teamArr[$result_buy[csf('team_leader')]]['teamname'].',';
		$team_email.=$teamArr[$result_buy[csf('team_leader')]]['team_email'].',';
		$team_leader_email.=$teamArr[$result_buy[csf('team_leader')]]['team_leader_email'].',';
		$team_leader.=$teamArr[$result_buy[csf('team_leader')]]['team_leader_name'].',';
		$team_member_email.=$marchentrArr[$result_buy[csf('dealing_marchant')]]['team_member_email'].',';
		$all_jobs[$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
 	}
	unset($nameArray_buyer);
	
	$jobAll=implode(", ",array_unique(explode(",",$jobAll)));
	$styleAll=implode(", ",array_unique(explode("**",$styleAll)));
	$poAll=implode(", ",array_unique(explode("**",$poAll)));
	$all_jobs_id=implode(",",$all_jobs );

  	//print_r($job_data_arr);
	$dealing_marchant=rtrim($dealing_marchant,',');
	$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchant)));
	
	$team_member_email=rtrim($team_member_email,',');
	$team_member_emails=implode(",",array_unique(explode(",",$team_member_email)));
	
	$team_name=rtrim($team_name,',');
	$team_names=implode(",",array_unique(explode(",",$team_name)));
	
	$team_email=rtrim($team_email,',');
	$team_emails=implode(",",array_unique(explode(",",$team_email)));
	
	$team_leader=rtrim($team_leader,',');
	$team_leaders=implode(",",array_unique(explode(",",$team_leader)));
	
	$team_leader_email=rtrim($team_leader_email,',');
	$team_leader_emails=implode(",",array_unique(explode(",",$team_leader_email)));
	
	$nameArray=sql_select( "select a.buyer_id, a.booking_date, a.supplier_id, a.ship_mode, a.delivery_date, a.fabric_source, a.remarks, a.pay_mode, a.attention, a.pay_term, a.tenor, a.is_approved, a.shippingmark_breck_down from wo_booking_mst a where a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");
	foreach ($nameArray as $result_job){
		$booking_date=$result_job[csf('booking_date')];
		$buyer_id=$result_job[csf('buyer_id')];
		$ship_mode=$result_job[csf('ship_mode')];
		$delivery_date=$result_job[csf('delivery_date')];
		$supplier_id=$result_job[csf('supplier_id')];
		$pay_mode=$result_job[csf('pay_mode')];
		$shippingmark_breck_down=$result_job[csf('shippingmark_breck_down')];
		$mstremarks=$result_job[csf('remarks')];
		$attention=$result_job[csf('attention')];
		$paytermTenor=$pay_term[$result_job[csf('pay_term')]].', '.$result_job[csf('tenor')].' Days';
		$is_approved=$result_job[csf('is_approved')];
	}
	unset($nameArray);
	//echo $booking_date.'ddd';
	
	$shippingmark_data=explode("~!~",$shippingmark_breck_down);
	$shippingmark_str="<b>".$shippingmark_data[0]."</b><br>".$shippingmark_data[1]."<br>".$shippingmark_data[2]."<br>".$shippingmark_data[3]."<br>".$shippingmark_data[4]."<br>".$shippingmark_data[5];
	
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
			
	$sqlCom="select company_name, email, website, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, contact_no from lib_company where id='$cbo_company_name' and status_active=1 and is_deleted=0";
	$sqlComRes=sql_select($sqlCom);
	$comName=$comAdd=$comPhone=$comPax=$comEmail=$comWeb=""; $consigneeDtls="";
	foreach( $sqlComRes as $row)
	{
		$comName=$row[csf("company_name")];
		
		if($row[csf("level_no")])		$level_no 	= $row[csf("level_no")].', ';
		if($row[csf("plot_no")])		$plot_no 	=$row[csf("plot_no")].', ';
		if($row[csf("road_no")]) 		$road_no 	=$row[csf("road_no")].', ';
		if($row[csf("block_no")]!='')	$block_no 	=$row[csf("block_no")].', ';
		if($row[csf("zip_code")]!='')	$zip_code 	= ' -'.$row[csf("zip_code")].' , ';
		if($row[csf("city")]!='') 		$city 		= ($zip_code!="")?$row[csf("city")]:$row[csf("city")]." ,";
		if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
		
		$comAdd=$level_no.$plot_no.$road_no.$block_no.$city.$zip_code.$country;
		$comPhone=$row[csf("contact_no")];
		$comEmail=$row[csf("email")];
		$comWeb=$row[csf("website")];
	}
	$consigneeDtls="<b>Consignee:</b> <br>".$comName."<br>".$comAdd."<br>".$comPhone."<br>".$comPax."<br>".$comEmail."<br>".$comWeb;
	
	$beneficiaryDtls="";
	if($pay_mode==3 || $pay_mode==5)
	{
		$sqlSupp="select company_name, email, website, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, contact_no from lib_company where id='$supplier_id' and status_active=1 and is_deleted=0";
		$sqlSuppRes=sql_select($sqlSupp);
		$suppName=$suppAdd=$suppPhone=$suppPax=$suppEmail=$suppWeb="";
		foreach( $sqlSuppRes as $row)
		{
			$suppName=$row[csf("company_name")];
			
			if($row[csf("level_no")])		$level_no 	= $row[csf("level_no")].', ';
			if($row[csf("plot_no")])		$plot_no 	=$row[csf("plot_no")].', ';
			if($row[csf("road_no")]) 		$road_no 	=$row[csf("road_no")].', ';
			if($row[csf("block_no")]!='')	$block_no 	=$row[csf("block_no")].', ';
			if($row[csf("zip_code")]!='')	$zip_code 	= ' -'.$row[csf("zip_code")].' , ';
			if($row[csf("city")]!='') 		$city 		= ($zip_code!="")?$row[csf("city")]:$row[csf("city")]." ,";
			if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
			
			$suppAdd=$level_no.$plot_no.$road_no.$block_no.$city.$zip_code.$country;
			$suppPhone=$row[csf("contact_no")];
			$suppEmail=$row[csf("email")];
			$suppWeb=$row[csf("website")];
		}
		$beneficiaryDtls="<b>Beneficiary:</b> <br>".$suppName."<br>".$suppAdd."<br>".$suppPhone."<br>".$suppPax."<br>".$suppEmail."<br>".$suppWeb;
	}
	else
	{
		$sqlSupp="select supplier_name, email, web_site, address_1, contact_no from lib_supplier where id='$supplier_id' and status_active=1 and is_deleted=0";
		$sqlSuppRes=sql_select($sqlSupp);
		$suppName=$suppAdd=$suppPhone=$suppPax=$suppEmail=$suppWeb="";
		$level_no=$plot_no=$road_no=$block_no=$city=$zip_code=$country="";
		foreach( $sqlSuppRes as $row)
		{
			$suppName=$row[csf("supplier_name")];
			$suppAdd=$row[csf("address_1")];
			$suppPhone=$row[csf("contact_no")];
			$suppEmail=$row[csf("email")];
			$suppWeb=$row[csf("web_site")];
		}
		$beneficiaryDtls="<b>Beneficiary:</b> <br>".$suppName."<br>".$suppAdd."<br>".$suppPhone."<br>".$suppPax."<br>".$suppEmail."<br>".$suppWeb;
	}
	ob_start();
	?>
    <style>
	@media print
	{
	     .page-break { height:0; page-break-before:always; margin:0; border-top:none; }
	}
     body, p, span, td, a {font-size:10pt;font-family: Arial;}
     body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}
	</style>
	<div style="width:930px" align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
            <tr>
                <td width="100">
                	<img src='<?=$path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                </td>
                <td width="830">
                    <table width="100%" cellpadding="0" cellspacing="0"  border="0" >
                        <tr>
                            <td align="center" colspan="2" style="font-size:20px"><?=$comName; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px"><?=show_company($cbo_company_name,'',''); ?></td>  
                        </tr>
                        <tr>
                            <td align="center" colspan="2" style="font-size:16px">
                            	<span><b>Requisition Booking No:&nbsp;&nbsp;<?=trim($txt_booking_no,"'"); ?></span>
                                <span style="color:#FF0000"><?php if ($is_approved==1 || $is_approved==3) {echo "(Approved)";} ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
            	<td width="830" colspan="2">
                	<table width="100%" cellpadding="0" cellspacing="0"  border="1" rules="all" >
                        <tr>
                            <td><?=$beneficiaryDtls; ?></td>
                            <td><?=$consigneeDtls; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
	<div>
    <div style="width:930px" align="center">
        <table class="rpt_table" width="100%" border="1" cellpadding="0" cellspacing="0" rules="all" >
        	<thead>
            	<th width="60">Issue Date</th>
                <th width="60">Delivery Date</th>
                <th width="70">Fabric Source</th>
                <th width="60">Mode of Shipment </th>
                <th width="100">Pay Term & Tenor</th>
                <th width="70">Contact Person</th>
                <th width="90">Buyer</th>
                <th width="150">Job No</th>
                <th>Style</th>
            </thead>
            <tbody>
            	<tr>
                    <td align="center"><?=change_date_format($booking_date); ?></td>
                    <td align="center"><?=change_date_format($delivery_date); ?></td>
                    <td align="center" style="word-break:break-all"><?=$fabric_source[$cbo_fabric_source]; ?></td>
                    <td align="center" style="word-break:break-all"><?=$shipment_mode[$ship_mode]; ?></td>
                    <td align="center" style="word-break:break-all"><?=$paytermTenor; ?></td>
                    <td align="center" style="word-break:break-all"><?=$attention; ?></td>
                    <td style="word-break:break-all"><?=$buyer_name_arr[$buyer_id]; ?></td>
                    <td style="word-break:break-all"><?=$jobAll; ?></td>
                    <td style="word-break:break-all"><?=$styleAll; ?></td>
                </tr>
                <tr>
                	<td align="center">Order No</td>
                    <td style="word-break:break-all" colspan="8"><?=$poAll; ?></td>
                </tr>
            </tbody>
        </table>
	<div>
    <br>
    <?
	$color_wise_process_loss=sql_select("select  a.id, a.job_no,a.body_part_id,b.color_number_id,a.process_loss_method,b.process_loss_percent as loss FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls  b 	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in(".$all_jobs_id.") and b.process_loss_percent>0 group by a.id, a.job_no,a.body_part_id,a.process_loss_method,b.color_number_id,b.process_loss_percent");
	foreach($color_wise_process_loss as $val)
	{
		$loss_arr[$val[csf("id")]][$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss']=$val[csf("loss")];
		$loss_arr[$val[csf("id")]][$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss_method']=$val[csf("process_loss_method")];
	}





	$sqlBookDtls="SELECT a.job_no,a.id as fabric_cost_dtls_id , a.id, a.item_number_id, a.body_part_id, a.fabric_description, 
	a.uom, c.gsm_weight, c.dia_width, c.color_type, c.gmts_color_id, c.fabric_color_id, c.fin_fab_qnty,c.grey_fab_qnty, 
	c.rate, c.amount, c.remark from wo_pre_cost_fabric_cost_dtls a join  wo_booking_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join wo_booking_dtls c on b.id=c.pre_cost_fabric_cost_dtls_id where c.booking_no=$txt_booking_no  and a.body_part_type not in  (40,50) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0";
	//echo $sqlBookDtls; die;

	
	
	$sqlBookDtlsRes=sql_select($sqlBookDtls);
	$dtlsDataArr=array();
	foreach($sqlBookDtlsRes as $brow)
	{
		$img_ref=$brow[csf('id')].'_'.$brow[csf('fabric_color_id')];

		$process_loss=$loss_arr[$brow[csf("fabric_cost_dtls_id")]][$brow[csf("job_no")]][$brow[csf("body_part_id")]][$brow[csf("gmts_color_id")]]['loss'];
		$process_loss_method=$loss_arr[$brow[csf("fabric_cost_dtls_id")]][$brow[csf("job_no")]][$brow[csf("body_part_id")]][$brow[csf("gmts_color_id")]]['loss_method'];
		if($process_loss=='') $process_loss=0;else $process_loss=$process_loss;
		if($process_loss_method=='') $process_loss_method=0;else $process_loss_method=$process_loss_method;
		
		$dtlsDataArr[$brow[csf('item_number_id')]][$brow[csf('body_part_id')]][$brow[csf('fabric_description')]][$brow[csf('gsm_weight')]][$brow[csf('dia_width')]][$brow[csf('color_type')]][$brow[csf('gmts_color_id')]][$brow[csf('fabric_color_id')]][$brow[csf('uom')]]['finQty']+=$brow[csf('fin_fab_qnty')];
		$dtlsDataArr[$brow[csf('item_number_id')]][$brow[csf('body_part_id')]][$brow[csf('fabric_description')]][$brow[csf('gsm_weight')]][$brow[csf('dia_width')]][$brow[csf('color_type')]][$brow[csf('gmts_color_id')]][$brow[csf('fabric_color_id')]][$brow[csf('uom')]]['greyQty']+=$brow[csf('grey_fab_qnty')];
		$dtlsDataArr[$brow[csf('item_number_id')]][$brow[csf('body_part_id')]][$brow[csf('fabric_description')]][$brow[csf('gsm_weight')]][$brow[csf('dia_width')]][$brow[csf('color_type')]][$brow[csf('gmts_color_id')]][$brow[csf('fabric_color_id')]][$brow[csf('uom')]]['amt']+=$brow[csf('amount')];
		$dtlsDataArr[$brow[csf('item_number_id')]][$brow[csf('body_part_id')]][$brow[csf('fabric_description')]][$brow[csf('gsm_weight')]][$brow[csf('dia_width')]][$brow[csf('color_type')]][$brow[csf('gmts_color_id')]][$brow[csf('fabric_color_id')]][$brow[csf('uom')]]['remarks'].=$brow[csf('remark')]."__";
		$dtlsDataArr[$brow[csf('item_number_id')]][$brow[csf('body_part_id')]][$brow[csf('fabric_description')]][$brow[csf('gsm_weight')]][$brow[csf('dia_width')]][$brow[csf('color_type')]][$brow[csf('gmts_color_id')]][$brow[csf('fabric_color_id')]][$brow[csf('uom')]]['fabimg']=$img_ref;
		$dtlsDataArr[$brow[csf('item_number_id')]][$brow[csf('body_part_id')]][$brow[csf('fabric_description')]][$brow[csf('gsm_weight')]][$brow[csf('dia_width')]][$brow[csf('color_type')]][$brow[csf('gmts_color_id')]][$brow[csf('fabric_color_id')]][$brow[csf('uom')]]['p_loss']=$process_loss;
		$dtlsDataArr[$brow[csf('item_number_id')]][$brow[csf('body_part_id')]][$brow[csf('fabric_description')]][$brow[csf('gsm_weight')]][$brow[csf('dia_width')]][$brow[csf('color_type')]][$brow[csf('gmts_color_id')]][$brow[csf('fabric_color_id')]][$brow[csf('uom')]]['p_loss_method']=$process_loss_method;

	}
	unset($sqlBookDtlsRes);
	asort($dtlsDataArr);
	
	$fabspanArr=array();
	foreach($dtlsDataArr as $itemid=>$itemData)
	{
		
		foreach($itemData as $bodyid=>$bodyData)
		{
			foreach($bodyData as $fabric=>$fabricData)	
			{
				foreach($fabricData as $gsm=>$gsmData)
				{
					foreach($gsmData as $dia=>$diaData)
					{
						$fabspan=0;
						foreach($diaData as $colortyp=>$colortypData)
						{
							foreach($colortypData as $gmtcolor=>$gmtcolorData)
							{
								foreach($gmtcolorData as $fabcolor=>$fabcolorData)
								{
									foreach($fabcolorData as $uom=>$uomData)
									{
										$fabspan++;
									}
								}
							}
						}
						$fabspanArr[$itemid][$bodyid][$fabric][$gsm][$dia]=$fabspan;
					}
				}
			}
		}
	}
	/*echo "<pre>";
	print_r($fabspanArr);*/
	?>
    
    <div style="width:930px" align="center">
        <table class="rpt_table" width="100%" border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr><th colspan="14">Fabric Details</th></tr>
        	<tr>
            	<th width="70">GMT Item Name</th>
                <th width="70">Body Part</th>
                <th width="130">Fab Description</th>
                <th width="40">GSM</th>
                <th width="40">Fab Dia</th>
                <th width="60">Color Type</th>
                <th width="80">Gmts Color</th>
                <th width="80">Fab Color</th>
                <th width="50">Fab Design IMG</th>    
				<th width="80">Grey Fab Qty</th>            
                <th width="80">Finish Fab Qty</th>			
                <th width="50">UOM</th>
                <th width="50">Rate</th>
                <th width="80">Amount</th>
                <th>Remarks</th>
			</tr>
           
            <?
			foreach($dtlsDataArr as $itemid=>$itemData)
			{
				foreach($itemData as $bodyid=>$bodyData)
				{
					foreach($bodyData as $fabric=>$fabricData)	
					{
						foreach($fabricData as $gsm=>$gsmData)
						{
							foreach($gsmData as $dia=>$diaData)
							{
								$k=1; $finQtyTotal=0;$greyQtyTotal=0; $bookingAmtTotal=0;
								$fabrowspan=$fabspanArr[$itemid][$bodyid][$fabric][$gsm][$dia]+1;
								foreach($diaData as $colortyp=>$colortypData)
								{
									foreach($colortypData as $gmtcolor=>$gmtcolorData)
									{
										foreach($gmtcolorData as $fabcolor=>$fabcolorData)
										{
											foreach($fabcolorData as $uom=>$uomData)
											{
												
												$remarks=""; $avgRate=0;
												$remarks=implode(",",array_filter(array_unique(explode("__",$uomData['remarks']))));
												$avgRate=$uomData['amt']/$uomData['finQty'];

												$p_loss_method=$uomData['p_loss_method'];
												$process_loss=$uomData['p_loss'];
											if($process_loss) $process_loss=$process_loss;else $process_loss=0;


												
												if($p_loss_method==1) //markup
												{
				
													$fin_qty=$uomData['finQty']-(($uomData['finQty']*$process_loss)/(100+$process_loss));
												}
												else if($p_loss_method==2) //margin
												{
													$fin_qty=$uomData['finQty']-(($uomData['finQty']*$process_loss)/100);
												}
												else $fin_qty=$uomData['finQty'];

												?>
                                                <tr>
                                                    <?
                                                    if($k==1)
                                                    {
														?>
														<td style="word-break:break-all" rowspan="<?=$fabrowspan; ?>"><?=$garments_item[$itemid]; ?></td>
														<td style="word-break:break-all" rowspan="<?=$fabrowspan; ?>" title="<?=$bodyid;?>"><?=$body_part[$bodyid]; ?></td>
														<td style="word-break:break-all" rowspan="<?=$fabrowspan; ?>"><?=$fabric; ?></td>
														<td style="word-break:break-all" rowspan="<?=$fabrowspan; ?>" align="center"><?=$gsm; ?></td>
														<td style="word-break:break-all" rowspan="<?=$fabrowspan; ?>" align="center"><?=$dia; ?></td>
														<?
                                                    }
                                                    ?>
                                                    <td style="word-break:break-all"><?=$color_type[$colortyp]; ?></td>
                                                    <td style="word-break:break-all"><?=$color_library[$gmtcolor]; ?></td>
                                                    <td style="word-break:break-all"><?=$color_library[$fabcolor]; ?></td>
                                                    <td style="word-break:break-all" width="50"><img src='<?=$path.$fabimge_arr[$uomData['fabimg']]; ?>' height='100%' width='100%' /></td>
                                                    <td style="word-break:break-all" align="right"><?=number_format($uomData['greyQty'],2); ?></td>
													<td style="word-break:break-all" align="right" title="process loss:<?=$process_loss;?>"><?=number_format($fin_qty,2); ?></td>
                                                    <td style="word-break:break-all"><?=$unit_of_measurement[$uom]; ?></td>
                                                    <td style="word-break:break-all" align="right"><?=number_format($avgRate,2); ?></td>
                                                    <td style="word-break:break-all" align="right"><?=number_format($uomData['amt'],2); ?></td>
                                                    <td style="word-break:break-all"><?=$remarks; ?></td>
                                                </tr>
                                                <?
												$k++;
												
												$greyQtyTotal+=$uomData['greyQty']; 
												$finQtyTotal+=$fin_qty; 
												$bookingAmtTotal+=$uomData['amt'];
												$ggreyQty+=$uomData['greyQty']; 
												$gfinQty+=$fin_qty; 
												$gbookingAmt+=$uomData['amt'];
											}
										}
									}
								}
								
								?>
                                	<tr style="background-color:#E3FDE6">
                                        <td style="word-break:break-all" colspan="4" align="right">Fabric Total : </td>
                                        <td style="word-break:break-all" align="right"><?=number_format($greyQtyTotal,2); ?></td>
										<td style="word-break:break-all" align="right"><?=number_format($finQtyTotal,2); ?></td>
                                        <td style="word-break:break-all">&nbsp;</td>
                                        <td style="word-break:break-all" align="right">&nbsp;</td>
                                        <td style="word-break:break-all" align="right"><?=number_format($bookingAmtTotal,2); ?></td>
                                        <td style="word-break:break-all">&nbsp;</td>
                                    </tr>
                                <?
							}
						}
					}
				}
			}
			?>
			 <tr style="background-color:#CCC">
                    <td style="word-break:break-all" colspan="9" align="right">Grand Total : </td>
					<td style="word-break:break-all" align="right"><?=number_format($ggreyQty,2); ?></td>
                    <td style="word-break:break-all" align="right"><?=number_format($gfinQty,2); ?></td>
                    <td style="word-break:break-all">&nbsp;</td>
                    <td style="word-break:break-all" align="right">&nbsp;</td>
                    <td style="word-break:break-all" align="right"><?=number_format($gbookingAmt,2); ?></td>
                    <td style="word-break:break-all">&nbsp;</td>
                </tr>
         
        
        </table>
	<div>
    <br>




	
		<?

			$sqlBookDtls="Select a.id, a.item_number_id, a.body_part_id, a.fabric_description, a.uom, b.gsm_weight, b.dia_width, b.color_type, b.gmts_color_id, b.fabric_color_id, b.fin_fab_qnty,b.grey_fab_qnty, b.rate, b.amount, b.remark,a.body_part_type from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no=$txt_booking_no  and a.body_part_type  in  (40,50) and b.status_active =1 and b.is_deleted=0";

				
				$sqlBookDtlsRes=sql_select($sqlBookDtls);
				$dtlsDataArr=array();
				foreach($sqlBookDtlsRes as $brow)
				{
					
					$string=$brow[csf('item_number_id')]."*".$brow[csf('body_part_id')]."*".$brow[csf('body_part_type')]."*".$brow[csf('fabric_description')]."*".$brow[csf('gsm_weight')]."*".$brow[csf('color_type')]."*".$brow[csf('gmts_color_id')];

					$booking_collar_cuf_rate[$string]['rate']=$brow[csf('amount')]/$brow[csf('fin_fab_qnty')];
				}
				unset($sqlBookDtlsRes);





			$po_num_arr=return_library_array("SELECT id,po_number from wo_po_break_down where job_no_mst in($all_jobs_id)",'id','po_number');
			$size_library=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name");
			$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");

	       $sql_collar_cuff= "SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id,b.size_number_id,b.item_size,b.gmts_qty,
		   b.excess_per,b.qty,b.id,a.job_no,a.rate,a.amount,a.color_size_sensitive, a.fabric_description,a.body_part_type  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=40 and b.status_active=1 and b.is_deleted=0   order by a.body_part_id,b.gmts_color_id,b.id  " ;

		   
			
		  


	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
			 $color_rows=array();
			 foreach($sql_data_collar_cuff as $row)
			 {
				$style=$job_data_arr[$row[csf('job_no')]]['style'];
			
				$color_id[$row[csf('gmts_color_id')]]=$row[csf('gmts_color_id')];
				
				$composition=$row[csf('construction')].",".$row[csf('composition')];
				$strings=$row[csf('item_number_id')]."*".$row[csf('body_part_id')]."*".$row[csf('body_part_type')]."*".$row[csf('fabric_description')]."*".$row[csf('gsm_weight')]."*".$row[csf('color_type_id')]."*".$row[csf('gmts_color_id')];
				
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['gmts_qty']+=$row[csf('gmts_qty')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['fabric_cost_dtls_id']=$row[csf('fabric_cost_dtls_id')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['color_size_sensitive']=$row[csf('color_size_sensitive')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('qty')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['amount']=$row[csf('amount')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['rate']=$booking_collar_cuf_rate[$strings]['rate'];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['item_size']=$row[csf('item_size')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['excess_per']=$row[csf('excess_per')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['job_no']=$row[csf('job_no')];


			 }
			 

			//  echo "<pre>";
			//  print_r($job_wise_booking_rate);
			$color_rows=array();
			foreach($style_wise_data as $styleId=>$item_data){
				foreach($item_data as $itemId=>$body_part_data){
				   foreach($body_part_data as $bodyId=>$comp_data){
					  foreach($comp_data as $compId=>$colortype_data){

						foreach($colortype_data as $colortypeId=>$color_data){
						  foreach($color_data as $colorId=>$size_data){
							foreach($size_data as $sizeId=>$row){


									$color_rows[$styleId][$colorId]+=1;
									if($row['excess_per']>0){
									$style_wise_qty[$styleId][$colorId]+=$row['gmts_qty']+(($row['gmts_qty']*$row['excess_per'])/100);
									}else{
										$style_wise_qty[$styleId][$colorId]+=$row['gmts_qty'];
									}
									}
								}
							}
						}
					}
				}
			}
			//  echo "<pre>";
			//  print_r($color_rows);

		?>
		


			<?

	          if(count($sql_data_collar_cuff)>0)

	          {

					?>
			  		<br>
			  		<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					  <tr>
						<td colspan="16" align="center">
							<strong>Collar and Cuff Breakdown</strong>
						</td>

					</tr>
			  			<tr>
			  				<td colspan="16" align="center">
			  					<strong>Collar Details</strong>
			  				</td>

			  			</tr>

			  			<tr>
			  				<td align="center"> SL </td>
							<td align="center" width="80">Style </td>
							<td align="center">Gmts Item </td>
							<td align="center">Body Part </td>
			  				<td align="center" width="150">Collar Composition</td>
							<td align="center">GSM </td>
							<td align="center">Color Type </td>			  			
			  				<td align="center"> Fabric  Color  </td>
			  				<td align="center"> Gmts Size</td>
			  				<td align="center"> Item Size  </td>
			  				<td align="center"> Gmts Qty (Pcs)  </td>
			  				<td align="center"> Excess %   </td>
			  				<td align="center"> Collar Qty (Pcs) </td>
							<td align="center">  Rate </td>
							<td align="center"> Amount </td>
			  			</tr>
			  			<?
			  			$p=1;$color_id="";$grand_total_amount=0;
			  			foreach($style_wise_data as $styleId=>$item_data){
						  foreach($item_data as $itemId=>$body_part_data){
							 foreach($body_part_data as $bodyId=>$comp_data){
								foreach($comp_data as $compId=>$colortype_data){									
								  foreach($colortype_data as $colortypeId=>$color_data){									
									foreach($color_data as $colorId=>$size_data){
										$c=1;
									  foreach($size_data as $sizeId=>$row){

			  				?>
			  				<tr>
			  					<td align="center"><? echo $p;?></td>	
								<td align="left" title="<?=$row["job_no"];?>"><? echo $styleId ;?></td>
								<td align="left"><? echo $garments_item[$itemId] ;?></td>
								<td align="left"><? echo $body_part[$bodyId] ;?></td>
			  					<td align="left"> &nbsp;<? echo $compId; ?></td>
								<td align="center"><? echo $row["gsm_weight"] ;?></td>
								<td align="center"><? echo $color_type[$colortypeId] ;?></td>
			  					<td align="center">
								  <?
										if($row['color_size_sensitive']==1 or $row['color_size_sensitive']==2 or $row['color_size_sensitive']==4)
										{
											$gmt_fab_color=$color_library[$colorId];
										}
										else
										{
											$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row['fabric_cost_dtls_id']."  and b.gmts_color_id=".$colorId." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
											if($contrast_color_id>0)
											{
												$gmt_fab_color=$color_library[$contrast_color_id];
											}
										}
									echo $gmt_fab_color;  ?>
			  					</td>
			  					<td align="center"><? echo $size_library[$sizeId];  ?></td>
			  					<td align="center"><? echo $row['item_size'];  ?></td>
			  					<td align="right"><? echo $row['gmts_qty']; $total_gmts_qty+=$row['gmts_qty']; ?></td>
			  					<td align="right"><? echo $row['excess_per'];$total_excess_per+=$row['excess_per'];?></td>
			  					<td align="right"><? echo $row['gmts_qty']+(($row['gmts_qty']*$row['excess_per'])/100);   $total_qty+=$row['qty']; ?></td>
								  <?php
								//   $r=1;
								 	if($c==1){					
								  ?>
								<td align="right" rowspan="<?=$color_rows[$styleId][$colorId];?>"><? echo number_format($row['rate'],2);  ?></td>
								<td align="right" rowspan="<?=$color_rows[$styleId][$colorId];?>"><? echo number_format($style_wise_qty[$styleId][$colorId]*$row['rate'],2);;  $sub_total_amount+=$style_wise_qty[$styleId][$colorId]*$row['rate']; 
								$grand_total_amount+=$style_wise_qty[$styleId][$colorId]*$row['rate'];

								?></td>

								<?
								 }
								?>
								
			  				</tr>
			  				<?
			  				$p++;  $r++;  $c++;
							 $sub_total_gmts_qty+=$row['gmts_qty'];
							 $sub_total_collar_qty+=$row['gmts_qty']+(($row['gmts_qty']*$row['excess_per'])/100);
							
							  
			  		      }
					    }
				      }
				    }
				  }
			     }
                }
		    
			  		?>
					    <tr>
			  				<td align="right" colspan="10"><b>Sub Total</b> </td>
			  				<td align="right"><?= $sub_total_gmts_qty;?></td>
			  				<td align="right"></td>
			  				<td align="right"><?=$sub_total_collar_qty;?></td>
							<td align="right"></td>
							<td align="right"><?=number_format($sub_total_amount,2);?> </td>
			  			</tr>

						  <?}?>
					  
			  	</table>
			  <br>

	   <?



				$sql_cuff_data=sql_select( "SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id,b.size_number_id,b.item_size,b.gmts_qty,
				b.excess_per,b.qty,b.id,a.job_no,a.rate,a.amount,a.color_size_sensitive,a.fabric_description,a.body_part_type  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=50 and b.status_active=1 and b.is_deleted=0   order by a.body_part_id,b.gmts_color_id,b.id  ") ;


				$color_rows=array();
				$strings="";
				foreach($sql_cuff_data as $row)
				{
					$style=$job_data_arr[$row[csf('job_no')]]['style'];
				
					$color_id[$row[csf('gmts_color_id')]]=$row[csf('gmts_color_id')];
					
					$composition=$row[csf('construction')].",".$row[csf('composition')];

					
					$strings=$row[csf('item_number_id')]."*".$row[csf('body_part_id')]."*".$row[csf('body_part_type')]."*".$row[csf('fabric_description')]."*".$row[csf('gsm_weight')]."*".$row[csf('color_type_id')]."*".$row[csf('gmts_color_id')];
					
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['gmts_qty']+=$row[csf('gmts_qty')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['fabric_cost_dtls_id']=$row[csf('fabric_cost_dtls_id')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['color_size_sensitive']=$row[csf('color_size_sensitive')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('qty')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['amount']=$row[csf('amount')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['rate']=$booking_collar_cuf_rate[$strings]['rate'];;
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['item_size']=$row[csf('item_size')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['excess_per']=$row[csf('excess_per')];


				}
				

				//  echo "<pre>";
				//  print_r($style_wise_data);
				$color_rows2=array();$style_wise_qty2=array();
				foreach($style_wise_data2 as $styleId=>$item_data){
					foreach($item_data as $itemId=>$body_part_data){
						foreach($body_part_data as $bodyId=>$comp_data){
						foreach($comp_data as $compId=>$colortype_data){
							foreach($colortype_data as $colortypeId=>$color_data){
							foreach($color_data as $colorId=>$size_data){
								foreach($size_data as $sizeId=>$row){

										$color_rows2[$styleId][$colorId]+=1;
										
											$style_wise_qty2[$styleId][$colorId]+=$row['qty'];
										
										
										}
									}
								}
							}
						}
					}
				}
	   


			 if(count($sql_cuff_data)>0)

			 {

				   ?>
					 <br>
					 <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

						 <tr>
							 <td colspan="16" align="center">
								 <strong>Cuff Details</strong>
							 </td>
						 </tr>
						 <tr>
						   <td align="center"> SL </td>
						   <td align="center" width="80">Style </td>
						   <td align="center">Gmts Item </td>
						   <td align="center">Body Part </td>
						   <td align="center" width="150">Cuff Composition </td>
						   <td align="center">GSM </td>
						   <td align="center">Color Type </td>						
						   <td align="center"> Fabric  Color  </td>
						   <td align="center"> Gmts Size</td>
						   <td align="center"> Item Size  </td>
						   <td align="center"> Gmts Qty (Pcs)  </td>
						   <td align="center"> Excess %   </td>
						   <td align="center"> Cuff Qty (Pcs) </td>
						   <td align="center">  Rate </td>
						   <td align="center"> Amount </td>
						 </tr>
						 <?
			  			$p=1;$color_id="";$sub_total_gmts_qty=0; $sub_total_collar_qty=0; $sub_total_amount=0;
			  			foreach($style_wise_data2 as $styleId=>$item_data){
						  foreach($item_data as $itemId=>$body_part_data){
							 foreach($body_part_data as $bodyId=>$comp_data){
								foreach($comp_data as $compId=>$colortype_data){									
								  foreach($colortype_data as $colortypeId=>$color_data){									
									foreach($color_data as $colorId=>$size_data){
										$c=1;
									  foreach($size_data as $sizeId=>$row){

			  				?>
								<tr>
									<td align="center"><? echo $p;?></td>
									<td align="left"><? echo $styleId ;?></td>
									<td align="left"><? echo $garments_item[$itemId] ;?></td>
									<td align="left"><? echo $body_part[$bodyId] ;?></td>
									<td align="left"><? echo $compId; ?></td>
									<td align="center"><? echo $row["gsm_weight"] ;?></td>
									<td align="center">	<? echo $color_type[$colortypeId] ;?></td>
									<td align="center">
									<?
											if($row['color_size_sensitive']==1 or $row['color_size_sensitive']==2 or $row['color_size_sensitive']==4)
											{
												$gmt_fab_color=$color_library[$colorId];
											}
											else
											{
												$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row['fabric_cost_dtls_id']."  and b.gmts_color_id=".$colorId." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
												if($contrast_color_id>0)
												{
													$gmt_fab_color=$color_library[$contrast_color_id];
												}
											}
										echo $gmt_fab_color;  ?>
									</td>
									<td align="center"><? echo $size_library[$sizeId];  ?></td>
									<td align="center"><? echo $row['item_size'];  ?></td>
									<td align="right"><? echo $row['gmts_qty'];	$total_gmts_qty+=$row['gmts_qty'];?></td>
									<td align="right"><? echo $row['excess_per']; $total_excess_per+=$row['excess_per'];?></td>
									<td align="right"><? echo $row['qty'];    ?></td>
									<?php
									//   $r=1;
									if($c==1){					
									?>
										<td align="right" rowspan="<?=$color_rows2[$styleId][$colorId];?>"><? echo number_format($row['rate'],2);  ?></td>
										<td align="right" rowspan="<?=$color_rows2[$styleId][$colorId];?>"><? echo number_format($style_wise_qty2[$styleId][$colorId]*$row['rate'],2);; $sub_total_amount+=$style_wise_qty2[$styleId][$colorId]*$row['rate']; 
										$grand_total_amount+=$style_wise_qty2[$styleId][$colorId]*$row['rate'];;					
										?>
										</td>

									<?
									}
									?>
									
								</tr>
			  				<?
			  				$p++;  $r++; $c++;
							 
							  $sub_total_gmts_qty+=$row['gmts_qty'];
							  $sub_total_cuff_qty+=$row['qty'];
			  		      }
					    }
				      }
				    }
				  }
			     }
                }
				?>
				<tr>
					<td align="right" colspan="10"><b>Sub Total</b> </td>
					<td align="right"><?= $sub_total_gmts_qty;?></td>
					<td align="right"></td>
					<td align="right"><?=$sub_total_cuff_qty;?></td>
					<td align="right"></td>
					<td align="right"><?=number_format($sub_total_amount,2);;?></td>
				</tr>
				<?
		    }

				if(count($style_wise_data)>0 || count($style_wise_data2)>0){

				
			  		?>
				
				<tr>
					<td align="right" colspan="14"><b>Grand Total</b> </td>				
					<td align="right"><?=number_format($grand_total_amount,2);;?></td>
				</tr>
				<?}
				?>
			 
		  </table>
			<br>



    <div style="width:930"><?=get_spacial_instruction($txt_booking_no); ?></div>
    <br>
    <div style="width:930px" align="center">
        <table class="rpt_table" width="100%" border="1" cellpadding="0" cellspacing="0" rules="all" >
        	<thead>
            	<th width="300">Shipping Marks [One Side]</th>
                <th width="200" colspan="2">Shipping Marks [Other Side]</th>
                <th width="200" colspan="2">Req. Issued By</th>
                <th>Special Instruction /Remarks</th>
            </thead>
            <tbody>
            	<tr>
                    <td style="word-break:break-all" rowspan="6"><?=$shippingmark_str; ?></td>
                    <td align="center">Customer Name :</td>
                    <td align="center"><?=$buyer_name_arr[$buyer_id]; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_names; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_emails; ?></td>
                    <td align="center" style="word-break:break-all" rowspan="6"><?=$mstremarks; ?></td>
                </tr>
                <tr>
                    <td align="center">Four H Req. :</td>
                    <td align="center"><?=$txt_booking_no; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_leaders; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_leader_emails; ?></td>
                </tr>
                <tr>
                    <td align="center">Quantity :</td>
                    <td align="center"><?=number_format($gfinQty,2); ?></td>
                    <td align="center" style="word-break:break-all"><?=$dealing_marchants; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_member_emails; ?></td>
                </tr>
                <tr>
                    <td align="center">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                </tr>
            </tbody>
        </table>
	<div>
    <?=signature_table(121, $cbo_company_name, "930px"); 
			
	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail)=explode('___',$mail_send_data);

	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html); 
		$mailBody="<br>".$mail_body	;
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no=$txt_booking_no";
	//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=99 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		if($mail!=''){$mailToArr[]=$mail;}

		$to=implode(',',$mailToArr);
		$subject="Partial fabric booking";
		$header=mailHeader();
		echo $to;
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
	exit();
}
if($action=="print_booking_2")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$path=str_replace("'","",$path);
	if($path!="") $path=$path; else $path="../../";

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' or form_name='knit_order_entry' and file_type=1",'master_tble_id','image_location');
	$fabimge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' or form_name='fabric_color_img' and file_type=1",'master_tble_id','image_location');
	
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	//$marchentrArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$teamArr =$marchentrArr=array();
	$teamSql = sql_select("select a.id, b.id as bid, a.team_name, a.team_leader_name, a.team_leader_email, a.user_tag_id, b.user_tag_id as user_id, b.team_member_name, b.team_member_email from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.status_active=1 and a.is_deleted=0 ");
	foreach($teamSql as $trow)
	{
		$teamArr[$trow[csf('id')]]['teamname']=$trow[csf('team_name')];
		$teamArr[$trow[csf('id')]]['team_leader_name']=$trow[csf('team_leader_name')];
		$teamArr[$trow[csf('id')]]['team_email']=$trow[csf('team_leader_email')];
		$marchentrArr[$trow[csf('bid')]]['team_member_name']=$trow[csf('team_member_name')];
		$marchentrArr[$trow[csf('bid')]]['team_member_email']=$trow[csf('team_member_email')];
		if($trow[csf('user_tag_id')]==$trow[csf('user_id')])
		{
			$teamArr[$trow[csf('id')]]['team_leader_email']=$trow[csf('team_member_email')];
		}
	}
	unset($teamSql);
	
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer  where status_active=1 and is_deleted=0",'id','buyer_name');
	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;

	$job_data_arr=array();
	$nameArray_buyer=sql_select( "select a.style_ref_no, a.style_description, a.job_no, a.buyer_name, a.team_leader, a.dealing_marchant, a.total_set_qnty, c.po_number from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c where a.job_no=b.job_no and a.id=c.job_id and b.po_break_down_id=c.id and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0 order by a.job_no ");
	$jobAll=$styleAll=$poAll="";
	foreach ($nameArray_buyer as $result_buy)
	{
		if($jobAll=="") $jobAll=$result_buy[csf('job_no')]; else $jobAll.=','.$result_buy[csf('job_no')];
		if($styleAll=="") $styleAll=$result_buy[csf('style_ref_no')]; else $styleAll.='**'.$result_buy[csf('style_ref_no')];
		if($poAll=="") $poAll=$result_buy[csf('po_number')]; else $poAll.='**'.$result_buy[csf('po_number')];
		
		$job_data_arr[$result_buy[csf('job_no')]]['style']=$result_buy[csf('style_ref_no')];
		$job_data_arr[$result_buy[csf('job_no')]]['job_no_in']="'".$result_buy[csf('job_no')]."'";
		$dealing_marchant.=$marchentrArr[$result_buy[csf('dealing_marchant')]]['team_member_name'].',';
		$team_name.=$teamArr[$result_buy[csf('team_leader')]]['teamname'].',';
		$team_email.=$teamArr[$result_buy[csf('team_leader')]]['team_email'].',';
		$team_leader_email.=$teamArr[$result_buy[csf('team_leader')]]['team_leader_email'].',';
		$team_leader.=$teamArr[$result_buy[csf('team_leader')]]['team_leader_name'].',';
		$team_member_email.=$marchentrArr[$result_buy[csf('dealing_marchant')]]['team_member_email'].',';
		$all_jobs[$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
 	}
	unset($nameArray_buyer);
	
	$jobAll=implode(", ",array_unique(explode(",",$jobAll)));
	$styleAll=implode(", ",array_unique(explode("**",$styleAll)));
	$poAll=implode(", ",array_unique(explode("**",$poAll)));
	$all_jobs_id=implode(",",$all_jobs );

  	//print_r($job_data_arr);
	$dealing_marchant=rtrim($dealing_marchant,',');
	$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchant)));
	
	$team_member_email=rtrim($team_member_email,',');
	$team_member_emails=implode(",",array_unique(explode(",",$team_member_email)));
	
	$team_name=rtrim($team_name,',');
	$team_names=implode(",",array_unique(explode(",",$team_name)));
	
	$team_email=rtrim($team_email,',');
	$team_emails=implode(",",array_unique(explode(",",$team_email)));
	
	$team_leader=rtrim($team_leader,',');
	$team_leaders=implode(",",array_unique(explode(",",$team_leader)));
	
	$team_leader_email=rtrim($team_leader_email,',');
	$team_leader_emails=implode(",",array_unique(explode(",",$team_leader_email)));
	
	$nameArray=sql_select( "select a.buyer_id, a.booking_date, a.supplier_id, a.ship_mode, a.delivery_date, a.fabric_source, a.remarks, a.pay_mode, a.attention, a.pay_term, a.tenor, a.is_approved, a.shippingmark_breck_down from wo_booking_mst a where a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");
	foreach ($nameArray as $result_job){
		$booking_date=$result_job[csf('booking_date')];
		$buyer_id=$result_job[csf('buyer_id')];
		$ship_mode=$result_job[csf('ship_mode')];
		$delivery_date=$result_job[csf('delivery_date')];
		$supplier_id=$result_job[csf('supplier_id')];
		$pay_mode=$result_job[csf('pay_mode')];
		$shippingmark_breck_down=$result_job[csf('shippingmark_breck_down')];
		$mstremarks=$result_job[csf('remarks')];
		$attention=$result_job[csf('attention')];
		$paytermTenor=$pay_term[$result_job[csf('pay_term')]].', '.$result_job[csf('tenor')].' Days';
		$is_approved=$result_job[csf('is_approved')];
	}
	unset($nameArray);
	//echo $booking_date.'ddd';
	
	$shippingmark_data=explode("~!~",$shippingmark_breck_down);
	$shippingmark_str="<b>".$shippingmark_data[0]."</b><br>".$shippingmark_data[1]."<br>".$shippingmark_data[2]."<br>".$shippingmark_data[3]."<br>".$shippingmark_data[4]."<br>".$shippingmark_data[5];
	
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
			
	$sqlCom="select company_name, email, website, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, contact_no from lib_company where id='$cbo_company_name' and status_active=1 and is_deleted=0";
	$sqlComRes=sql_select($sqlCom);
	$comName=$comAdd=$comPhone=$comPax=$comEmail=$comWeb=""; $consigneeDtls="";
	foreach( $sqlComRes as $row)
	{
		$comName=$row[csf("company_name")];
		
		if($row[csf("level_no")])		$level_no 	= $row[csf("level_no")].', ';
		if($row[csf("plot_no")])		$plot_no 	=$row[csf("plot_no")].', ';
		if($row[csf("road_no")]) 		$road_no 	=$row[csf("road_no")].', ';
		if($row[csf("block_no")]!='')	$block_no 	=$row[csf("block_no")].', ';
		if($row[csf("zip_code")]!='')	$zip_code 	= ' -'.$row[csf("zip_code")].' , ';
		if($row[csf("city")]!='') 		$city 		= ($zip_code!="")?$row[csf("city")]:$row[csf("city")]." ,";
		if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
		
		$comAdd=$level_no.$plot_no.$road_no.$block_no.$city.$zip_code.$country;
		$comPhone=$row[csf("contact_no")];
		$comEmail=$row[csf("email")];
		$comWeb=$row[csf("website")];
	}
	$consigneeDtls="<b>Consignee:</b> <br>".$comName."<br>".$comAdd."<br>".$comPhone."<br>".$comPax."<br>".$comEmail."<br>".$comWeb;
	
	$beneficiaryDtls="";
	if($pay_mode==3 || $pay_mode==5)
	{
		$sqlSupp="select company_name, email, website, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, contact_no from lib_company where id='$supplier_id' and status_active=1 and is_deleted=0";
		$sqlSuppRes=sql_select($sqlSupp);
		$suppName=$suppAdd=$suppPhone=$suppPax=$suppEmail=$suppWeb="";
		foreach( $sqlSuppRes as $row)
		{
			$suppName=$row[csf("company_name")];
			
			if($row[csf("level_no")])		$level_no 	= $row[csf("level_no")].', ';
			if($row[csf("plot_no")])		$plot_no 	=$row[csf("plot_no")].', ';
			if($row[csf("road_no")]) 		$road_no 	=$row[csf("road_no")].', ';
			if($row[csf("block_no")]!='')	$block_no 	=$row[csf("block_no")].', ';
			if($row[csf("zip_code")]!='')	$zip_code 	= ' -'.$row[csf("zip_code")].' , ';
			if($row[csf("city")]!='') 		$city 		= ($zip_code!="")?$row[csf("city")]:$row[csf("city")]." ,";
			if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
			
			$suppAdd=$level_no.$plot_no.$road_no.$block_no.$city.$zip_code.$country;
			$suppPhone=$row[csf("contact_no")];
			$suppEmail=$row[csf("email")];
			$suppWeb=$row[csf("website")];
		}
		$beneficiaryDtls="<b>Beneficiary:</b> <br>".$suppName."<br>".$suppAdd."<br>".$suppPhone."<br>".$suppPax."<br>".$suppEmail."<br>".$suppWeb;
	}
	else
	{
		$sqlSupp="select supplier_name, email, web_site, address_1, contact_no from lib_supplier where id='$supplier_id' and status_active=1 and is_deleted=0";
		$sqlSuppRes=sql_select($sqlSupp);
		$suppName=$suppAdd=$suppPhone=$suppPax=$suppEmail=$suppWeb="";
		$level_no=$plot_no=$road_no=$block_no=$city=$zip_code=$country="";
		foreach( $sqlSuppRes as $row)
		{
			$suppName=$row[csf("supplier_name")];
			$suppAdd=$row[csf("address_1")];
			$suppPhone=$row[csf("contact_no")];
			$suppEmail=$row[csf("email")];
			$suppWeb=$row[csf("web_site")];
		}
		$beneficiaryDtls="<b>Beneficiary:</b> <br>".$suppName."<br>".$suppAdd."<br>".$suppPhone."<br>".$suppPax."<br>".$suppEmail."<br>".$suppWeb;
	}
	ob_start();
	?>
    <style>
	@media print
	{
	     .page-break { height:0; page-break-before:always; margin:0; border-top:none; }
	}
     body, p, span, td, a {font-size:10pt;font-family: Arial;}
     body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}
	</style>
	<div style="width:930px" align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
            <tr>
                <td width="100">
                	<img src='<?=$path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                </td>
                <td width="830">
                    <table width="100%" cellpadding="0" cellspacing="0"  border="0" >
                        <tr>
                            <td align="center" colspan="2" style="font-size:20px"><?=$comName; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px"><?=show_company($cbo_company_name,'',''); ?></td>  
                        </tr>
                        <tr>
                            <td align="center" colspan="2" style="font-size:16px">
                            	<span><b>Fabric <?=$fabric_source[$cbo_fabric_source]; ?> Requisition No:&nbsp;&nbsp;<?=trim($txt_booking_no,"'"); ?></span>
                                <span style="color:#FF0000"><?php if ($is_approved==1 || $is_approved==3) {echo "(Approved)";} ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
            	<td width="830" colspan="2">
                	<table width="100%" cellpadding="0" cellspacing="0"  border="1" rules="all" >
                        <tr>
                            <td><?=$beneficiaryDtls; ?></td>
                            <td><?=$consigneeDtls; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
	<div>
    <div style="width:930px" align="center">
        <table class="rpt_table" width="100%" border="1" cellpadding="0" cellspacing="0" rules="all" >
        	<thead>
            	<th width="60">Issue Date</th>
                <th width="60">Delivery Date</th>
                <th width="70">Fabric Source</th>
                <th width="60">Mode of Shipment </th>
                <th width="100">Pay Term & Tenor</th>
                <th width="70">Contact Person</th>
                <th width="90">Buyer</th>
                <th width="150">Job No</th>
                <th>Style</th>
            </thead>
            <tbody>
            	<tr>
                    <td align="center"><?=change_date_format($booking_date); ?></td>
                    <td align="center"><?=change_date_format($delivery_date); ?></td>
                    <td align="center" style="word-break:break-all"><?=$fabric_source[$cbo_fabric_source]; ?></td>
                    <td align="center" style="word-break:break-all"><?=$shipment_mode[$ship_mode]; ?></td>
                    <td align="center" style="word-break:break-all"><?=$paytermTenor; ?></td>
                    <td align="center" style="word-break:break-all"><?=$attention; ?></td>
                    <td style="word-break:break-all"><?=$buyer_name_arr[$buyer_id]; ?></td>
                    <td style="word-break:break-all"><?=$jobAll; ?></td>
                    <td style="word-break:break-all"><?=$styleAll; ?></td>
                </tr>
                <tr>
                	<td align="center">Order No</td>
                    <td style="word-break:break-all" colspan="8"><?=$poAll; ?></td>
                </tr>
            </tbody>
        </table>
	<div>
    <br>
    <?
	$color_wise_process_loss=sql_select("select  a.id, a.job_no,a.body_part_id,b.color_number_id,a.process_loss_method,b.process_loss_percent as loss FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls  b 	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in(".$all_jobs_id.") and b.process_loss_percent>0 group by a.id, a.job_no,a.body_part_id,a.process_loss_method,b.color_number_id,b.process_loss_percent");
	foreach($color_wise_process_loss as $val)
	{
		$loss_arr[$val[csf("id")]][$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss']=$val[csf("loss")];
		$loss_arr[$val[csf("id")]][$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss_method']=$val[csf("process_loss_method")];
	}

	//$sqlBookDtls="Select a.job_no,a.id as fabric_cost_dtls_id , a.id, a.item_number_id, a.body_part_id, a.fabric_description, a.uom, b.gsm_weight, b.dia_width, b.color_type, b.gmts_color_id, b.fabric_color_id, b.fin_fab_qnty,b.grey_fab_qnty, b.rate, b.amount, b.remark from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no=$txt_booking_no  and a.body_part_type not in  (40,50) and b.status_active =1 and b.is_deleted=0";
	$sqlBookDtls="SELECT a.job_no,a.id as fabric_cost_dtls_id , a.id, a.item_number_id, a.body_part_id, a.fabric_description, 
	a.uom, c.gsm_weight, c.dia_width, c.color_type, c.gmts_color_id, c.fabric_color_id, c.fin_fab_qnty,c.grey_fab_qnty, 
	c.rate, c.amount, c.remark from wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls b on 
	a.id=b.pre_cost_fabric_cost_dtls_id join wo_booking_dtls c on b.id=c.pre_cost_fabric_cost_dtls_id where 
	c.booking_no=$txt_booking_no and a.body_part_type not in (40,50) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0";
	
	$sqlBookDtlsRes=sql_select($sqlBookDtls);
	$dtlsDataArr=array();
	foreach($sqlBookDtlsRes as $brow)
	{
		$img_ref=$brow[csf('id')].'_'.$brow[csf('fabric_color_id')];

		$process_loss=$loss_arr[$brow[csf("fabric_cost_dtls_id")]][$brow[csf("job_no")]][$brow[csf("body_part_id")]][$brow[csf("gmts_color_id")]]['loss'];
		$process_loss_method=$loss_arr[$brow[csf("fabric_cost_dtls_id")]][$brow[csf("job_no")]][$brow[csf("body_part_id")]][$brow[csf("gmts_color_id")]]['loss_method'];
		if($process_loss=='') $process_loss=0;else $process_loss=$process_loss;
		if($process_loss_method=='') $process_loss_method=0;else $process_loss_method=$process_loss_method;
		$index_key=$brow[csf('job_no')];
		$uom=$brow[csf('uom')];
		$boby_part=$brow[csf('body_part_id')];
		$fab_des=$brow[csf('fabric_description')];
		$fabric_color=$brow[csf('fabric_color_id')];
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['finQty']+=$brow[csf('fin_fab_qnty')];
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['greyQty']+=$brow[csf('grey_fab_qnty')];
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['amt']+=$brow[csf('amount')];
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['remarks'].=$brow[csf('remark')]."__";
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['fabimg']=$img_ref;
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['p_loss']=$process_loss;
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['p_loss_method']=$process_loss_method;
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['gsm_weight']=$brow[csf('gsm_weight')];
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['dia_width']=$brow[csf('dia_width')];
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['color_type']=$brow[csf('color_type')];
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['job_no']=$brow[csf('job_no')];
		$dtlsDataArr[$boby_part][$fab_des][$fabric_color][$index_key][$uom]['fabric_color']=$brow[csf('fabric_color_id')];

	}
	/* echo '<pre>';
	print_r($dtlsDataArr); die; */
	unset($sqlBookDtlsRes);
	asort($dtlsDataArr);
	
	$fabspanArr=array();
	foreach($dtlsDataArr as $bodyid=>$bodyData)
	{
		foreach($bodyData as $fabric=>$fabricData)	
		{

			$fabspan=0;
			foreach($fabricData as $fabcolor=>$fabcolorData)
			{
				foreach($fabcolorData as $jobno=>$jobData)
				{
					foreach($jobData as $uom=>$uomData)
					{
						$fabspan++;
					}
				}
			}
			$fabspanArr[$bodyid][$fabric]=$fabspan;
		}
	}
	/*echo "<pre>";
	print_r($fabspanArr);*/
	?>
    
    <div style="width:930px" align="center">
        <table class="rpt_table" width="100%" border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr><th colspan="14">Fabric Details</th></tr>
        	<tr>            	
                <th width="70">Body Part</th>
                <th width="130">Fab Description</th>
                <th width="40">GSM</th>
                <th width="40">Fab Dia</th>
                <th width="60">Color Type</th>
				<th width="70">Job NO</th>
                <th width="80">Fab Color</th>
                <th width="50">Fab Design IMG</th>    
				<th width="80">Grey Fab Qty</th>            
                <th width="80">Finish Fab Qty</th>			
                <th width="50">UOM</th>
                <th width="50">Rate</th>
                <th width="80">Amount</th>
                <th>Remarks</th>
			</tr>
           
            <?
			foreach($dtlsDataArr as $bodyid=>$bodyData)
			{
				foreach($bodyData as $fabric=>$fabricData)	
				{
					
						$k=1; $finQtyTotal=0;$greyQtyTotal=0; $bookingAmtTotal=0;
						$fabrowspan=$fabspanArr[$bodyid][$fabric]+1;
						foreach($fabricData as $fabcolor=>$fabcolorData)
						{	$fabrictotalgreyqty=0; $fabrictotalfinQty=0; $fabrictotalAmt=0;
							foreach($fabcolorData as $jobno=>$jobData)
							{
								foreach($jobData as $uom=>$uomData)
								{
									
									$remarks=""; $avgRate=0;
									$remarks=implode(",",array_filter(array_unique(explode("__",$uomData['remarks']))));
									$avgRate=$uomData['amt']/$uomData['finQty'];

									$p_loss_method=$uomData['p_loss_method'];
									$process_loss=$uomData['p_loss'];
									if($process_loss) $process_loss=$process_loss;else $process_loss=0;
									
									if($p_loss_method==1) //markup
									{

										$fin_qty=$uomData['finQty']-(($uomData['finQty']*$process_loss)/(100+$process_loss));
									}
									else if($p_loss_method==2) //margin
									{
										$fin_qty=$uomData['finQty']-(($uomData['finQty']*$process_loss)/100);
									}
									else $fin_qty=$uomData['finQty'];

									?>
									<tr>
										<?
										if($k==1)
										{
											?>
											<td style="word-break:break-all" rowspan="<?=$fabrowspan+count($fabricData); ?>" title="<?=$bodyid;?>"><?=$body_part[$bodyid]; ?></td>
											<td style="word-break:break-all" rowspan="<?=$fabrowspan+count($fabricData); ?>"><?=$fabric; ?></td>										
											<?
										}
										?>
										<td style="word-break:break-all" align="center"><?=$uomData['gsm_weight']; ?></td>
										<td style="word-break:break-all" align="center"><?=$uomData['dia_width']; ?></td>
										<td style="word-break:break-all"><?=$color_type[$uomData['color_type']]; ?></td>
										<td style="word-break:break-all"><?=$uomData['job_no']; ?></td>
										<td style="word-break:break-all"><?=$color_library[$uomData['fabric_color']]; ?></td>
										<td style="word-break:break-all" width="50"><img src='<?=$path.$fabimge_arr[$uomData['fabimg']]; ?>' height='100%' width='100%' /></td>
										<td style="word-break:break-all" align="right"><?=number_format($uomData['greyQty'],2); ?></td>
										<td style="word-break:break-all" align="right" title="process loss:<?=$process_loss;?>"><?=number_format($fin_qty,2); ?></td>
										<td style="word-break:break-all"><?=$unit_of_measurement[$uom]; ?></td>
										<td style="word-break:break-all" align="right"><?=number_format($avgRate,2); ?></td>
										<td style="word-break:break-all" align="right"><?=number_format($uomData['amt'],2); ?></td>
										<td style="word-break:break-all"><?=$remarks; ?></td>
									</tr>
									<?
									$k++;												
									$greyQtyTotal+=$uomData['greyQty']; 
									$finQtyTotal+=$fin_qty; 
									$bookingAmtTotal+=$uomData['amt'];
									$ggreyQty+=$uomData['greyQty']; 
									$gfinQty+=$fin_qty; 
									$gbookingAmt+=$uomData['amt'];

									$fabrictotalgreyqty+=$uomData['greyQty']; 
									$fabrictotalfinQty+=$fin_qty; 
									$fabrictotalAmt+=$uomData['amt'];
								}
							}
							?>
							<tr style="background-color:#E3FDE6">
								<td style="word-break:break-all" colspan="6" align="right">Sub Total</td>
								<td style="word-break:break-all" align="right"><?=number_format($fabrictotalgreyqty,2); ?></td>
								<td style="word-break:break-all" align="right"><?=number_format($fabrictotalfinQty,2); ?></td>
								<td style="word-break:break-all">&nbsp;</td>
								<td style="word-break:break-all" align="right">&nbsp;</td>
								<td style="word-break:break-all" align="right"><?=number_format($fabrictotalAmt,2); ?></td>
								<td style="word-break:break-all">&nbsp;</td>
							</tr>
							<?
						}
						
						?>
							<tr style="background-color:#F1E5AC">
								<td style="word-break:break-all" colspan="6" align="right">Fabric Total</td>
								<td style="word-break:break-all" align="right"><?=number_format($greyQtyTotal,2); ?></td>
								<td style="word-break:break-all" align="right"><?=number_format($finQtyTotal,2); ?></td>
								<td style="word-break:break-all">&nbsp;</td>
								<td style="word-break:break-all" align="right">&nbsp;</td>
								<td style="word-break:break-all" align="right"><?=number_format($bookingAmtTotal,2); ?></td>
								<td style="word-break:break-all">&nbsp;</td>
							</tr>
						<?
				}
			}
			?>
			 <tr style="background-color:#CCC">
                    <td style="word-break:break-all" colspan="8" align="right">Grand Total : </td>
					<td style="word-break:break-all" align="right"><?=number_format($ggreyQty,2); ?></td>
                    <td style="word-break:break-all" align="right"><?=number_format($gfinQty,2); ?></td>
                    <td style="word-break:break-all">&nbsp;</td>
                    <td style="word-break:break-all" align="right">&nbsp;</td>
                    <td style="word-break:break-all" align="right"><?=number_format($gbookingAmt,2); ?></td>
                    <td style="word-break:break-all">&nbsp;</td>
                </tr>
         
        
        </table>
	<div>
    <br>


	
		<?

			$sqlBookDtls="Select a.id, a.item_number_id, a.body_part_id, a.fabric_description, a.uom, b.gsm_weight, b.dia_width, b.color_type, b.gmts_color_id, b.fabric_color_id, b.fin_fab_qnty,b.grey_fab_qnty, b.rate, b.amount, b.remark,a.body_part_type from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no=$txt_booking_no  and a.body_part_type  in  (40,50) and b.status_active =1 and b.is_deleted=0";

				
				$sqlBookDtlsRes=sql_select($sqlBookDtls);
				$dtlsDataArr=array();
				foreach($sqlBookDtlsRes as $brow)
				{
					
					$string=$brow[csf('item_number_id')]."*".$brow[csf('body_part_id')]."*".$brow[csf('body_part_type')]."*".$brow[csf('fabric_description')]."*".$brow[csf('gsm_weight')]."*".$brow[csf('color_type')]."*".$brow[csf('gmts_color_id')];

					$booking_collar_cuf_rate[$string]['rate']=$brow[csf('amount')]/$brow[csf('fin_fab_qnty')];
				}
				unset($sqlBookDtlsRes);





			$po_num_arr=return_library_array("SELECT id,po_number from wo_po_break_down where job_no_mst in($all_jobs_id)",'id','po_number');
			$size_library=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name");
			$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");

	       $sql_collar_cuff= "SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id,b.size_number_id,b.item_size,b.gmts_qty,
		   b.excess_per,b.qty,b.id,a.job_no,a.rate,a.amount,a.color_size_sensitive, a.fabric_description,a.body_part_type  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=40 and b.status_active=1 and b.is_deleted=0   order by a.body_part_id,b.gmts_color_id,b.id  " ;

		   
			
		  


	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
			 $color_rows=array();
			 foreach($sql_data_collar_cuff as $row)
			 {
				$style=$job_data_arr[$row[csf('job_no')]]['style'];
			
				$color_id[$row[csf('gmts_color_id')]]=$row[csf('gmts_color_id')];
				
				$composition=$row[csf('construction')].",".$row[csf('composition')];
				$strings=$row[csf('item_number_id')]."*".$row[csf('body_part_id')]."*".$row[csf('body_part_type')]."*".$row[csf('fabric_description')]."*".$row[csf('gsm_weight')]."*".$row[csf('color_type_id')]."*".$row[csf('gmts_color_id')];
				
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['gmts_qty']+=$row[csf('gmts_qty')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['fabric_cost_dtls_id']=$row[csf('fabric_cost_dtls_id')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['color_size_sensitive']=$row[csf('color_size_sensitive')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('qty')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['amount']=$row[csf('amount')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['rate']=$booking_collar_cuf_rate[$strings]['rate'];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['item_size']=$row[csf('item_size')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['excess_per']=$row[csf('excess_per')];
				 $style_wise_data[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['job_no']=$row[csf('job_no')];


			 }
			 

			//  echo "<pre>";
			//  print_r($job_wise_booking_rate);
			$color_rows=array();
			foreach($style_wise_data as $styleId=>$item_data){
				foreach($item_data as $itemId=>$body_part_data){
				   foreach($body_part_data as $bodyId=>$comp_data){
					  foreach($comp_data as $compId=>$colortype_data){

						foreach($colortype_data as $colortypeId=>$color_data){
						  foreach($color_data as $colorId=>$size_data){
							foreach($size_data as $sizeId=>$row){


									$color_rows[$styleId][$colorId]+=1;
									if($row['excess_per']>0){
									$style_wise_qty[$styleId][$colorId]+=$row['gmts_qty']+(($row['gmts_qty']*$row['excess_per'])/100);
									}else{
										$style_wise_qty[$styleId][$colorId]+=$row['gmts_qty'];
									}
									}
								}
							}
						}
					}
				}
			}
			//  echo "<pre>";
			//  print_r($color_rows);

		?>
		


			<?

	          if(count($sql_data_collar_cuff)>0)

	          {

					?>
			  		<br>
			  		<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					  <tr>
						<td colspan="16" align="center">
							<strong>Collar and Cuff Breakdown</strong>
						</td>

					</tr>
			  			<tr>
			  				<td colspan="16" align="center">
			  					<strong>Collar Details</strong>
			  				</td>

			  			</tr>

			  			<tr>
			  				<td align="center"> SL </td>
							<td align="center" width="80">Style </td>
							<td align="center">Gmts Item </td>
							<td align="center">Body Part </td>
			  				<td align="center" width="150">Collar Composition</td>
							<td align="center">GSM </td>
							<td align="center">Color Type </td>			  			
			  				<td align="center"> Fabric  Color  </td>
			  				<td align="center"> Gmts Size</td>
			  				<td align="center"> Item Size  </td>
			  				<td align="center"> Gmts Qty (Pcs)  </td>
			  				<td align="center"> Excess %   </td>
			  				<td align="center"> Collar Qty (Pcs) </td>
							<td align="center">  Rate </td>
							<td align="center"> Amount </td>
			  			</tr>
			  			<?
			  			$p=1;$color_id="";$grand_total_amount=0;
			  			foreach($style_wise_data as $styleId=>$item_data){
						  foreach($item_data as $itemId=>$body_part_data){
							 foreach($body_part_data as $bodyId=>$comp_data){
								foreach($comp_data as $compId=>$colortype_data){									
								  foreach($colortype_data as $colortypeId=>$color_data){									
									foreach($color_data as $colorId=>$size_data){
										$c=1;
									  foreach($size_data as $sizeId=>$row){

			  				?>
			  				<tr>
			  					<td align="center"><? echo $p;?></td>	
								<td align="left" title="<?=$row["job_no"];?>"><? echo $styleId ;?></td>
								<td align="left"><? echo $garments_item[$itemId] ;?></td>
								<td align="left"><? echo $body_part[$bodyId] ;?></td>
			  					<td align="left"> &nbsp;<? echo $compId; ?></td>
								<td align="center"><? echo $row["gsm_weight"] ;?></td>
								<td align="center"><? echo $color_type[$colortypeId] ;?></td>
			  					<td align="center">
								  <?
										if($row['color_size_sensitive']==1 or $row['color_size_sensitive']==2 or $row['color_size_sensitive']==4)
										{
											$gmt_fab_color=$color_library[$colorId];
										}
										else
										{
											$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row['fabric_cost_dtls_id']."  and b.gmts_color_id=".$colorId." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
											if($contrast_color_id>0)
											{
												$gmt_fab_color=$color_library[$contrast_color_id];
											}
										}
									echo $gmt_fab_color;  ?>
			  					</td>
			  					<td align="center"><? echo $size_library[$sizeId];  ?></td>
			  					<td align="center"><? echo $row['item_size'];  ?></td>
			  					<td align="right"><? echo $row['gmts_qty']; $total_gmts_qty+=$row['gmts_qty']; ?></td>
			  					<td align="right"><? echo $row['excess_per'];$total_excess_per+=$row['excess_per'];?></td>
			  					<td align="right"><? echo $row['gmts_qty']+(($row['gmts_qty']*$row['excess_per'])/100);   $total_qty+=$row['qty']; ?></td>
								  <?php
								//   $r=1;
								 	if($c==1){					
								  ?>
								<td align="right" rowspan="<?=$color_rows[$styleId][$colorId];?>"><? echo number_format($row['rate'],2);  ?></td>
								<td align="right" rowspan="<?=$color_rows[$styleId][$colorId];?>"><? echo number_format($style_wise_qty[$styleId][$colorId]*$row['rate'],2);;  $sub_total_amount+=$style_wise_qty[$styleId][$colorId]*$row['rate']; 
								$grand_total_amount+=$style_wise_qty[$styleId][$colorId]*$row['rate'];

								?></td>

								<?
								 }
								?>
								
			  				</tr>
			  				<?
			  				$p++;  $r++;  $c++;
							 $sub_total_gmts_qty+=$row['gmts_qty'];
							 $sub_total_collar_qty+=$row['gmts_qty']+(($row['gmts_qty']*$row['excess_per'])/100);
							
							  
			  		      }
					    }
				      }
				    }
				  }
			     }
                }
		    
			  		?>
					    <tr>
			  				<td align="right" colspan="10"><b>Sub Total</b> </td>
			  				<td align="right"><?= $sub_total_gmts_qty;?></td>
			  				<td align="right"></td>
			  				<td align="right"><?=$sub_total_collar_qty;?></td>
							<td align="right"></td>
							<td align="right"><?=number_format($sub_total_amount,2);?> </td>
			  			</tr>

						  <?}?>
					  
			  	</table>
			  <br>

	   			<?
				$sql_cuff_data=sql_select( "SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id,b.size_number_id,b.item_size,b.gmts_qty,
				b.excess_per,b.qty,b.id,a.job_no,a.rate,a.amount,a.color_size_sensitive,a.fabric_description,a.body_part_type  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=50 and b.status_active=1 and b.is_deleted=0   order by a.body_part_id,b.gmts_color_id,b.id  ") ;

				$color_rows=array();
				$strings="";
				foreach($sql_cuff_data as $row)
				{
					$style=$job_data_arr[$row[csf('job_no')]]['style'];
				
					$color_id[$row[csf('gmts_color_id')]]=$row[csf('gmts_color_id')];
					
					$composition=$row[csf('construction')].",".$row[csf('composition')];

					
					$strings=$row[csf('item_number_id')]."*".$row[csf('body_part_id')]."*".$row[csf('body_part_type')]."*".$row[csf('fabric_description')]."*".$row[csf('gsm_weight')]."*".$row[csf('color_type_id')]."*".$row[csf('gmts_color_id')];
					
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['gmts_qty']+=$row[csf('gmts_qty')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['fabric_cost_dtls_id']=$row[csf('fabric_cost_dtls_id')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['color_size_sensitive']=$row[csf('color_size_sensitive')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('qty')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['amount']=$row[csf('amount')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['rate']=$booking_collar_cuf_rate[$strings]['rate'];;
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['item_size']=$row[csf('item_size')];
					$style_wise_data2[$style][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$composition][$row[csf('color_type_id')]][$row[csf('gmts_color_id')]][$row[csf('size_number_id')]]['excess_per']=$row[csf('excess_per')];


				}
				$color_rows2=array();$style_wise_qty2=array();
				foreach($style_wise_data2 as $styleId=>$item_data){
					foreach($item_data as $itemId=>$body_part_data){
						foreach($body_part_data as $bodyId=>$comp_data){
						foreach($comp_data as $compId=>$colortype_data){
							foreach($colortype_data as $colortypeId=>$color_data){
							foreach($color_data as $colorId=>$size_data){
								foreach($size_data as $sizeId=>$row){

										$color_rows2[$styleId][$colorId]+=1;
										
											$style_wise_qty2[$styleId][$colorId]+=$row['qty'];
										
										
										}
									}
								}
							}
						}
					}
				}
	   


			 if(count($sql_cuff_data)>0)
			 {

				   ?>
					 <br>
					 <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

						 <tr>
							 <td colspan="16" align="center">
								 <strong>Cuff Details</strong>
							 </td>
						 </tr>
						 <tr>
						   <td align="center"> SL </td>
						   <td align="center" width="80">Style </td>
						   <td align="center">Gmts Item </td>
						   <td align="center">Body Part </td>
						   <td align="center" width="150">Cuff Composition </td>
						   <td align="center">GSM </td>
						   <td align="center">Color Type </td>						
						   <td align="center"> Fabric  Color  </td>
						   <td align="center"> Gmts Size</td>
						   <td align="center"> Item Size  </td>
						   <td align="center"> Gmts Qty (Pcs)  </td>
						   <td align="center"> Excess %   </td>
						   <td align="center"> Cuff Qty (Pcs) </td>
						   <td align="center">  Rate </td>
						   <td align="center"> Amount </td>
						 </tr>
						 <?
			  			$p=1;$color_id="";$sub_total_gmts_qty=0; $sub_total_collar_qty=0; $sub_total_amount=0;
			  			foreach($style_wise_data2 as $styleId=>$item_data){
						  foreach($item_data as $itemId=>$body_part_data){
							 foreach($body_part_data as $bodyId=>$comp_data){
								foreach($comp_data as $compId=>$colortype_data){									
								  foreach($colortype_data as $colortypeId=>$color_data){									
									foreach($color_data as $colorId=>$size_data){
										$c=1;
									  foreach($size_data as $sizeId=>$row){

			  				?>
								<tr>
									<td align="center"><? echo $p;?></td>
									<td align="left"><? echo $styleId ;?></td>
									<td align="left"><? echo $garments_item[$itemId] ;?></td>
									<td align="left"><? echo $body_part[$bodyId] ;?></td>
									<td align="left"><? echo $compId; ?></td>
									<td align="center"><? echo $row["gsm_weight"] ;?></td>
									<td align="center">	<? echo $color_type[$colortypeId] ;?></td>
									<td align="center">
									<?
											if($row['color_size_sensitive']==1 or $row['color_size_sensitive']==2 or $row['color_size_sensitive']==4)
											{
												$gmt_fab_color=$color_library[$colorId];
											}
											else
											{
												$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row['fabric_cost_dtls_id']."  and b.gmts_color_id=".$colorId." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
												if($contrast_color_id>0)
												{
													$gmt_fab_color=$color_library[$contrast_color_id];
												}
											}
										echo $gmt_fab_color;  ?>
									</td>
									<td align="center"><? echo $size_library[$sizeId];  ?></td>
									<td align="center"><? echo $row['item_size'];  ?></td>
									<td align="right"><? echo $row['gmts_qty'];	$total_gmts_qty+=$row['gmts_qty'];?></td>
									<td align="right"><? echo $row['excess_per']; $total_excess_per+=$row['excess_per'];?></td>
									<td align="right"><? echo $row['qty'];    ?></td>
									<?php
									//   $r=1;
									if($c==1){					
									?>
										<td align="right" rowspan="<?=$color_rows2[$styleId][$colorId];?>"><? echo number_format($row['rate'],2);  ?></td>
										<td align="right" rowspan="<?=$color_rows2[$styleId][$colorId];?>"><? echo number_format($style_wise_qty2[$styleId][$colorId]*$row['rate'],2);; $sub_total_amount+=$style_wise_qty2[$styleId][$colorId]*$row['rate']; 
										$grand_total_amount+=$style_wise_qty2[$styleId][$colorId]*$row['rate'];;					
										?>
										</td>

									<?
									}
									?>
									
								</tr>
			  				<?
			  				$p++;  $r++; $c++;
							 
							  $sub_total_gmts_qty+=$row['gmts_qty'];
							  $sub_total_cuff_qty+=$row['qty'];
			  		      }
					    }
				      }
				    }
				  }
			     }
                }
				?>
				<tr>
					<td align="right" colspan="10"><b>Sub Total</b> </td>
					<td align="right"><?= $sub_total_gmts_qty;?></td>
					<td align="right"></td>
					<td align="right"><?=$sub_total_cuff_qty;?></td>
					<td align="right"></td>
					<td align="right"><?=number_format($sub_total_amount,2);;?></td>
				</tr>
				<?
		     }

				if(count($style_wise_data)>0 || count($style_wise_data2)>0){

				
			  		?>
				
				<tr>
					<td align="right" colspan="14"><b>Grand Total</b> </td>				
					<td align="right"><?=number_format($grand_total_amount,2);;?></td>
				</tr>
				<?}
				?>
			 
		  </table>
			<br>



    <div style="width:930"><?=get_spacial_instruction($txt_booking_no); ?></div>
    <br>
    <div style="width:930px" align="center">
        <table class="rpt_table" width="100%" border="1" cellpadding="0" cellspacing="0" rules="all" >
        	<thead>
            	<th width="300">Shipping Marks [One Side]</th>
                <th width="200" colspan="2">Shipping Marks [Other Side]</th>
                <th width="200" >Req. Issued By</th>
                <th>Special Instruction /Remarks</th>
            </thead>
            <tbody>
            	<tr>
                    <td style="word-break:break-all" rowspan="6"><?=$shippingmark_str; ?></td>
                    <td align="center">Customer Name :</td>
                    <td align="center"><?=$buyer_name_arr[$buyer_id]; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_names; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_emails; ?></td>
                    <td align="center" style="word-break:break-all" rowspan="6"><?=$mstremarks; ?></td>
                </tr>
                <tr>
                    <td align="center">Four H Req. :</td>
                    <td align="center"><?=$txt_booking_no; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_leaders; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_leader_emails; ?></td>
                </tr>
                <tr>
                    <td align="center">Quantity :</td>
                    <td align="center"><?=number_format($gfinQty,2); ?></td>
                    <td align="center" style="word-break:break-all"><?=$dealing_marchants; ?></td>
                    <td align="center" style="word-break:break-all"><?=$team_member_emails; ?></td>
                </tr>
                <tr>
                    <td align="center">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                    <td align="center" style="word-break:break-all">&nbsp;</td>
                </tr>
            </tbody>
        </table>
	<div>
    <?=signature_table(121, $cbo_company_name, "930px"); 
			
	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail)=explode('___',$mail_send_data);

	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html); 
		$mailBody="<br>".$mail_body	;
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no=$txt_booking_no";
	//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=99 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		if($mail!=''){$mailToArr[]=$mail;}

		$to=implode(',',$mailToArr);
		$subject="Partial fabric booking";
		$header=mailHeader();
		echo $to;
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
	exit();
}
?>