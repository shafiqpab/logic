<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Fabric Requisition
Functionality	         :
JS Functions	         :
Created by		         :	zakaria joy
Creation date 	         : 	28-02-2023
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
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/fabric_requisition_controller', this.value, 'load_drop_down_brand', 'brand_td');","","" );
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
	echo load_html_head_contents("Requisition Search","../../../", 1, 1, $unicode);
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
                        <th width="80">Requisition No</th>
                        <th width="80">Job No</th>
                        <th width="80">File No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Order No</th>
                        <th width="130" colspan="2">Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">REQ. Without Item</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                    <input type="hidden" id="selected_booking">
                    <?
					//echo "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name";
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'fabric_requisition_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );",1);
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
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'fabric_requisition_controller','setFilterGrid(\'list_view\',-1)');" style="width:100px;" /></td>
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
		load_drop_down( 'fabric_requisition_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_popup', 'buyer_td' );
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
	$sql_po= sql_select("select a.booking_no, a.po_break_down_id, a.job_no from wo_booking_mst a where $company $buyer $booking_date and a.booking_type=10 and a.is_short=2 and a.entry_form=611 and a.status_active=1 and a.is_deleted=0 order by a.booking_no");
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
		 $sql="select min(a.id) as id, a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.gmts_item_id, c.job_no_prefix_num, c.style_ref_no, d.po_number, d.grouping, d.file_no
		from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d
		where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=10 and a.entry_form=611 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and $company $buyer $booking_date $booking_cond $style_cond $ref_cond $order_cond $job_cond
		group by a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.job_no_prefix_num, c.gmts_item_id, c.style_ref_no, d.po_number, d.grouping, d.file_no order by id DESC";
	}
	else
	{
		 $sql="select min(a.id) as id, a.job_no as job_no_prefix_num, a.booking_no_prefix_num, a.pay_mode, a.booking_no, company_id, a.buyer_id, a.supplier_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.is_approved
		 from wo_booking_mst a
		 where a.booking_no not in ( select a.booking_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.booking_type=10 and a.entry_form=611 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and $company $buyer $booking_date $booking_cond $job_cond $file_no_cond $internal_ref_cond group by a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date) and a.booking_type=10 and a.entry_form=611 and a.status_active =1 and a.is_deleted=0 and $company $buyer $supplier_id $booking_date $booking_cond
		 group by a.booking_no_prefix_num, a.booking_no, a.job_no, company_id, a.buyer_id, a.supplier_id, a.pay_mode, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.is_approved order by id DESC";
	}
	?>
    <div>
        <table width="1160" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="60">Requisition No</th>
                    <th width="60">Requisition Date</th>
                    <th width="90">Buyer</th>
                    <th width="90">Job No</th>
                    <th width="90">Style Ref.</th>
                    <th width="100">Gmts Item </th>
                    <th width="80">PO number</th>
                    <th width="80">Internal Ref</th>
                    <th width="80">File No</th>
                    <th width="80">Fab. Nature</th>
                    <th width="80">Fab. Source</th>
                    <th width="60">Pay Mode</th>
                    <th width="60">Supplier</th>
                    <th width="50">Approved</th>
                    <th>Ready to Approved</th>
                </tr>
            </thead>
        </table>
        <div style="width:1160px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table width="1140" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="list_view">
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
                        <td width="80" style="word-break:break-all"><? echo $item_category[$row[csf("item_category")]];?></td>
                        <td width="80"><? echo $fabric_source[$row[csf("fabric_source")]];?></td>
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
	 $sql= "select id,booking_no, booking_date, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, booking_month, supplier_id, attention, booking_percent, delivery_date, source, booking_year, colar_excess_percent, cuff_excess_percent, is_approved, ready_to_approved, is_apply_last_update, rmg_process_breakdown, fabric_composition, uom, remarks, cbo_level, brand_id, isgreyfab_purchase, ship_mode, pay_term, tenor, shippingmark_breck_down from wo_booking_mst where booking_no='$data' and status_active =1 and is_deleted=0";

	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "load_drop_down('requires/fabric_requisition_controller', '".$row[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td');\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
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
		echo "fnc_greyFabPurchase('".$row[csf("pay_mode")]."');\n";
		if($row[csf("isgreyfab_purchase")]==0 || $row[csf("isgreyfab_purchase")]=="") $row[csf("isgreyfab_purchase")]=2;
		echo "document.getElementById('cbo_greyfab_purch').value = '".$row[csf("isgreyfab_purchase")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('txt_colar_excess_percent').value = '".$row[csf("colar_excess_percent")]."';\n";
		echo "document.getElementById('txt_cuff_excess_percent').value = '".$row[csf("cuff_excess_percent")]."';\n";
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}
		else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('processloss_breck_down').value = '".$row[csf("rmg_process_breakdown")]."';\n";
		echo "document.getElementById('txt_fabriccomposition').value = '".$row[csf("fabric_composition")]."';\n";
		$supplier_dropdwan=load_drop_down_suplier($row[csf("pay_mode")],$row[csf("company_id")]);
		echo "document.getElementById('sup_td').innerHTML = '".$supplier_dropdwan."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbouom').value = '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('cbo_shipmode').value = '".$row[csf("ship_mode")]."';\n";
		echo "document.getElementById('cbo_payterm').value = '".$row[csf("pay_term")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('hiddshippingmark_breck_down').value = '".$row[csf("shippingmark_breck_down")]."';\n";
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
	echo load_html_head_contents("Fabric Requisition Job Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$postatus="";
	if(trim($txt_booking_no)!="")
	{
		$sqlBooking=sql_select("Select po_break_down_id from wo_booking_dtls where booking_no='$txt_booking_no' and is_deleted = 0 and status_active=1");
		$pobooking=$sqlBooking[0][csf('po_break_down_id')];
		
		$sql_result=sql_select("select is_confirmed from wo_po_break_down where id='$pobooking'");
		$postatus=$sql_result[0][csf('is_confirmed')];
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

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				var postr=$('#txt_po_id' + str).val();
				
				//$sql_row['id'].'_'.$sql_row['is_confirmed'].'_'.$sql_row['fabric_source'].'_'.$maintain_textile; ?>
				var podata=postr.split("_");
				podata[3]=podata[3].replace("'", "");
				//alert( $('#txtpostatus').val()+'_'+podata[1]+'_'+podata[2]+'_'+podata[3] )
				if( $('#txtpostatus').val()!="" && ($('#txtpostatus').val()!=podata[1] && podata[2]==1 && podata[3]==2))
				{
					alert('PO Status Mixing Not Allowed.');
					return;
				}
				else
				{
					document.getElementById('txtpostatus').value=podata[1];
				}
				
				var txt_tna_date_check=$('#txt_tna_date_check' + str).val();
				if(txt_tna_date_check==1)
				{
					alert('TNA process not found for this PO');
				}
				else{
					toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + str).val() );
						selected_item.push($('#pre_cost_dtls_id' + str).val());
						selected_po.push(podata[0]);
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

		/*function fnc_job_pop(page_link,title){
			//alert(title)
			var cbo_company_mst=$('#cbo_company_mst').val();
			var cbo_buyer_name=$('#cbo_buyer_name').val();
		page_link=page_link+"&cbo_company_mst="+cbo_company_mst+"&cbo_buyer_name="+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Job Search', 'width=1200px,height=300px,center=1,resize=1,scrolling=0','../../')

		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("job_no");
			var year=this.contentDoc.getElementById("cbo_job_year");
			if (job_no.value!=""){
				$('#txt_job_prifix').val( job_no.value );
				$('#cbo_job_year').val( year.value );
				show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+document.getElementById('cbo_string_search_type').value, 'fabric_search_list_view', 'search_div', 'fabric_requisition_controller', 'setFilterGrid(\'list_view\',-1)');
			}
		}
	}*/
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
                            <th colspan="11" align="center"><?=create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
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
                    	<?php 

                    		$onchange_company="";
                    		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
                    		if(empty($cbo_buyer_name))
                    		{
                    			$onchange_company="load_drop_down( 'fabric_requisition_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );";
                    		}

                    	 ?>
                        <td><?=create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name),$onchange_company ,"1"); ?>
                        </td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "","1" ); ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                        <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px" onDblClick="fnc_jobpopup();"></td>
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value=""/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value=""/></td>
                        <td align="center">
                            <input type="hidden" name="cbo_currency" id="cbo_currency" class="text_boxes" style="width:60px" value="<?=str_replace("'","",$cbo_currency); ?>"  />
                            <input type="hidden" name="cbo_fabric_natu" id="cbo_fabric_natu" class="text_boxes" style="width:60px" value="<?=str_replace("'","",$cbo_fabric_natu); ?>"  />
                            <input type="hidden" name="cbouom" id="cbouom" class="text_boxes" style="width:60px" value="<?=str_replace("'","",$cbouom); ?>"  />
                            <input type="hidden" name="cbo_fabric_source" id="cbo_fabric_source" class="text_boxes" style="width:60px" value="<?=str_replace("'","",$cbo_fabric_source); ?>"  />
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<?=$cbo_brand_id; ?>', 'fabric_search_list_view', 'search_div', 'fabric_requisition_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center">
                            <input type="hidden" class="text_boxes" readonly style="width:550px" id="txt_selected_po">
                            <input type="hidden" id="txt_selected_id">
                            <input type="hidden" id="txt_pre_cost_dtls_id">
                            <input type="hidden" id="txtpostatus" value="<?=$postatus; ?>">
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
                	<td id="search_div" align="center"></td>
                </tr>
                <tr>
                    <td align="center">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                    </td>
                </tr>
            </table>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">$("#cbo_buyer_name").val(<?=$cbo_buyer_name; ?>);</script>
    <script type="text/javascript">
	function fnc_jobpopup()
	{
		var cbo_company_mst=$('#cbo_company_mst').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		
		var page_link='fabric_requisition_controller.php?action=job_search_popup';
		//alert(page_link);
		page_link=page_link+"&cbo_company_mst="+cbo_company_mst+"&cbo_buyer_name="+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Job Search', 'width=1200px,height=300px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("job_no");
			var year=this.contentDoc.getElementById("cbo_job_year");
			
			if (job_no.value!=""){
				$('#txt_job_prifix').val( job_no.value );
				$('#cbo_job_year').val( year.value );
				show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+document.getElementById('cbo_string_search_type').value, 'fabric_search_list_view', 'search_div', 'fabric_requisition_controller', 'setFilterGrid(\'list_view\',-1)');
			}
		}
	}
	</script>  
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
	$cbo_brand_id=$data[15];
	if ($company!=0) $company_cond=" and a.company_name='$company'"; else { echo "Please Select Company First."; die; }
	if ($buyer!=0) $buyer_cond=" and a.buyer_name='$buyer'"; else{ echo "Please Select Buyer First."; die; }
	if ($cbo_currency!="") $currency_cond=" and a.currency_id='$cbo_currency'"; else{ echo "Please Select Currency First."; die; }
	if ($cbo_fabric_natu!="") $fabric_natu_cond=" and d.fab_nature_id='$cbo_fabric_natu'"; else{ echo "Please Select Fabric Nature First."; die; }
	if ($cbouom!=0) $uom_cond=" and d.uom='$cbouom'";
	if ($cbo_fabric_source!="") $fabric_source_cond=" and d.fabric_source='$cbo_fabric_source'"; else{ echo "Please Select Fabric Source  First."; die; }
	if ($cbo_brand_id=="" || $cbo_brand_id==0) $brandCond=""; else $brandCond=" and a.brand_id='$cbo_brand_id'";

	if($db_type==0){
	if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2){
	if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_job_year";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_job_year";
	if (str_replace("'","",$job)=="" && str_replace("'","",$style)=="" && str_replace("'","",$internal_ref)=="")
	{
		//echo $job.'='.$style;
		echo "Please Insert Job Or Style/Ref No First."; die;
	}
	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$company' and variable_list=14 and status_active=1 and is_deleted=0");
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	
	$sqltextileref=sql_select("select production_entry from variable_settings_production where company_name='$company' and variable_list=66 and status_active=1 and is_deleted=0");
	$maintain_textile=$sqltextileref[0][csf('production_entry')];

	$job_cond=""; $order_cond=""; $style_cond="";
	if($search_category==1){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num='$job'";
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number = '$order_search'";
		if (trim($style)!="") $style_cond=" and a.style_ref_no ='$style'";
		if (trim($internal_ref) !="") $internal_ref_cond=" and b.grouping = '$internal_ref'";
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no='$file_no' ";
	}
	else if($search_category==2){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '$job%'";
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '$order_search%'  ";
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '$style%'  ";
		if (trim($internal_ref) !="") $internal_ref_cond=" and b.grouping like '$internal_ref%'";
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '$file_no%' ";
	}
	else if($search_category==3){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '%$job'";
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '%$order_search'  ";
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '%$style'";
		if (trim($internal_ref) !="")  $internal_ref_cond=" and b.grouping like '%$internal_ref'";
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '%$file_no' ";
	}
	else if($search_category==4 || $search_category==0){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '%$job%'";
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '%$order_search%'  ";
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '%$style%'";
		if (trim($internal_ref)!="")  $internal_ref_cond=" and b.grouping like '%$internal_ref%'";
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '%$file_no%' ";
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
	if(str_replace("'","",$internal_ref) !=''){
		$condition->grouping("='$internal_ref'");
	}
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery();die;

	$req_qty_arr=$fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();

	$fabric_source_sql=sql_select("SELECT b.id as fabric_id, b.fabric_source  from wo_po_details_master a join wo_pre_cost_fabric_cost_dtls b on a.id=b.job_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".$company_cond . $buyer_cond. $year_cond.$job_cond.$style_cond."");
	foreach ($fabric_source_sql as $row) {
		$fabric_source_arr[$row[csf('fabric_id')]]=$row[csf('fabric_source')];
	}
	unset($fabric_source_sql);

	$cu_booking_data_arr=array();
	$sql='SELECT b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id",b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id" ,a.id AS "booking_id",a.fin_fab_qnty AS "fin_fab_qnty",a.grey_fab_qnty AS "grey_fab_qnty",a.dia_width AS "dia_width" from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_details_master c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and a.job_no=b.job_no and a.job_no=c.job_no and  c.job_no_prefix_num ='.$job.' and a.booking_type=10 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, a.id, a.fin_fab_qnty, a.grey_fab_qnty, a.dia_width';
	$dataArray=sql_select($sql);
	foreach($dataArray as $dataArray_row){
		$cu_booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
	}
	unset($dataArray);

	$sql= 'SELECT a.job_no AS "job_no", b.id AS "id", b.po_number AS "po_number", b.is_confirmed as "is_confirmed", c.item_number_id AS "item_number_id", d.id AS "pre_cost_dtls_id", d.body_part_id AS "body_part_id", d.construction AS "construction", d.composition AS "composition", d.fab_nature_id AS "fab_nature_id", d.fabric_source AS "fabric_source", d.color_type_id AS "color_type_id", d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id", d.uom AS "uom", d.gsm_weight AS "gsm_weight", min(e.id) AS "eid" from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$company_cond . $buyer_cond. $year_cond. $job_cond. $internal_ref_cond. $file_no_cond . $style_cond. $order_cond. $shipment_date. $currency_cond. $fabric_natu_cond. $uom_cond. $fabric_source_cond.$brandCond ." group by a.job_no, b.id, b.po_number, b.is_confirmed, c.item_number_id, d.id, d.color_type_id, d.body_part_id, d.construction, d.composition, d.fab_nature_id, d.fabric_source, d.lib_yarn_count_deter_id, d.uom, d.gsm_weight";
	//echo $sql;
	$sql_data=sql_select($sql);

	$po_id_arr=array(); $tna_data=array();
	if($maintain_setting==1)
	{
		foreach ($sql_data as $row) 
		{
			array_push($po_id_arr, $row['id']);
		}
	
		$po_id_cond=where_con_using_array($po_id_arr,0,"po_number_id");
	
		$tan_sql=sql_select("select po_number_id, task_start_date from tna_process_mst where status_active=1 and is_deleted=0 $po_id_cond");	
		foreach ($tan_sql as $tna_value) {
			//$tna_data[$tna_value[csf('id')]]=$tna_value[csf('task_start_date')];
			$tna_data[$tna_value[csf('po_number_id')]]=1;
		}
		unset($tan_sql);
	}
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Job No</th>
                <th width="80">Po No</th>
                <th width="80">Order Status</th>
                <th width="100">Item</th>
                <th width="100">Body Part</th>
                <th width="100">Color Type</th>
                 
                <th width="100">Construction</th>
                <th width="180">Composition</th>
                <th width="70">Gsm</th>
                <th width="80">Fabric Nature</th>
                <th width="70">Fabric Soutce</th>
                <th>UOM</th>
            </thead>
     	</table>
     </div>
     <div style="width:1150px; max-height:270px;overflow-y:scroll;" >
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" id="list_view">
    <?
	$i=1;
	if(count($sql_data)>0){
		foreach($sql_data as $sql_row){
			$reqQty=0;
			if($cbo_fabric_natu==2){
				$reqQty=$req_qty_arr['knit']['grey'][$sql_row['id']][$sql_row['pre_cost_dtls_id']][$sql_row['uom']];
			}
			if($cbo_fabric_natu==3){
				$reqQty=$req_qty_arr['woven']['grey'][$sql_row['id']][$sql_row['pre_cost_dtls_id']][$sql_row['uom']];
			}

			$cuBooking=$cu_booking_data_arr[$sql_row['pre_cost_dtls_id']][$sql_row['id']];
			$balQty=number_format($reqQty-$cuBooking,4,".","");

			$tna_date=$tna_data[$sql_row['id']];
			if($maintain_setting==1 && $tna_date=="") $tna_check=1;  else $tna_check=0;

			if($balQty >0 ){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr style="text-decoration:none; cursor:pointer" bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
					<td width="30"><?=$i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$sql_row['eid']; ?>"/>
						<input type="hidden" name="pre_cost_dtls_id" id="pre_cost_dtls_id<?=$i; ?>" value="<?=$sql_row['pre_cost_dtls_id']; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?=$i; ?>" value="'<?=$sql_row['id'].'_'.$sql_row['is_confirmed'].'_'.$sql_row['fabric_source'].'_'.$maintain_textile; ?>'"/>
						<input type="hidden" name="txt_tna_date_check" id="txt_tna_date_check<?=$i; ?>" value="<?=$tna_check; ?>"/>
					</td>
					<td width="80" style="word-break:break-all"><?=$sql_row['job_no']; ?></td>
					<td width="80" style="word-break:break-all"><?=$sql_row['po_number']; ?></td>
					<td width="80" style="word-break:break-all"><?=$order_status[$sql_row['is_confirmed']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$garments_item[$sql_row['item_number_id']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$body_part[$sql_row['body_part_id']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$color_type[$sql_row['color_type_id']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$sql_row['construction']; ?></td>
					<td width="180" style="word-break:break-all"><?=$sql_row['composition']; ?></td>
					<td width="70" style="word-break:break-all"><?=$sql_row['gsm_weight']; ?></td>
					<td width="80" style="word-break:break-all"><?=$item_category[$sql_row['fab_nature_id']]; ?></td>
					<td width="70" style="word-break:break-all"><?=$fabric_source[$sql_row['fabric_source']]; ?></td>
					<td><?=$unit_of_measurement[$sql_row['uom']]; ?></td>
				</tr>
				<?
				$i++;
			}
		}
	}
	else{
		foreach ($fabric_source_arr as $fs_id) {
			if($fs_id != $cbo_fabric_source){
				echo "<span style='font-size:24px; font-weight:bold; color:#FF0000; margin-top:10px'>Mismatch Fabric Source or Related Components with budget</span>";
				exit();
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
						//load_drop_down( 'fabric_requisition_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value, 'create_job_search_list_view', 'search_div', 'fabric_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
                        <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'fabric_requisition_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );","1"); ?>
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value, 'create_po_search_list_view', 'search_div', 'fabric_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$fabric_cost_dtls_id=implode(",",array_unique(explode(",",str_replace("'","",$cbo_fabric_description))));
	$cbouom=str_replace("'","",$cbouom); 
	
	 $poidCond=implode(",",$poIdChkArr);
		
		 
	
	$cu_booking_data_arr=array();
	$sql='select b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id", a.id AS "booking_id", a.fin_fab_qnty AS "fin_fab_qnty", a.grey_fab_qnty AS "grey_fab_qnty", a.dia_width AS "dia_width", a.pre_cost_remarks AS "pre_cost_remarks" from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and  a.po_break_down_id in('.$txt_order_no_id.') and b.cons>0  and a.booking_type=10 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, a.id, a.fin_fab_qnty, a.grey_fab_qnty, a.dia_width, a.pre_cost_remarks';
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
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	//$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where id in($txt_order_no_id)", "id", "po_number");
	
	$job_sql=sql_select("select a.company_name, a.job_no, b.id, b.po_number, min(c.id) as cid, sum(c.plan_cut_qnty) as plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where b.id in(".$txt_order_no_id.") and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.company_name, a.job_no, b.id, b.po_number, c.color_number_id, c.size_number_id, c.item_number_id");
	 $po_number_arr=array(); $paln_cut_qnty_array=array(); $popaln_cut_qnty_array=array();
	foreach($job_sql as $jrow)
	{
		$po_number_arr[$jrow[csf("id")]]=$jrow[csf("po_number")];
		$paln_cut_qnty_array[$jrow[csf("cid")]]=$jrow[csf("plan_cut_qnty")];
		$popaln_cut_qnty_array[$jrow[csf("id")]]+=$jrow[csf("plan_cut_qnty")];
		$txt_job_no=$jrow[csf("job_no")];
	}
	unset($job_sql);
	$item_ratio_array=return_library_array("select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");
	
	$sql='SELECT a.id AS "id", a.job_no AS "job_no", a.uom AS "uom", a.item_number_id AS "item_number_id", a.body_part_id AS "body_part_id", a.color_type_id AS "color_type_id", a.width_dia_type AS "width_dia_type", a.construction AS "construction", a.composition AS "composition", a.gsm_weight AS "gsm_weight", a.costing_per AS "costing_per", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id", b.dia_width AS "dia_width", b.remarks AS "remarks", c.contrast_color_id AS "contrast_color_id", a.color_size_sensitive AS "color_size_sensitive", d.hs_code as "hs_code", a.fabric_source as "fabric_source", a.fab_nature_id as "fab_nature_id", a.lib_yarn_count_deter_id as "lib_yarn_id"
	
	from wo_pre_cost_fabric_cost_dtls a join wo_pre_cos_fab_co_avg_con_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join lib_yarn_count_determina_mst d on d.id=a.lib_yarn_count_deter_id left join wo_pre_cos_fab_co_color_dtls c on  b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and b.color_number_id=c.gmts_color_id   where a.id in('.$fabric_cost_dtls_id.') and b.po_break_down_id in ('.$txt_order_no_id.') and b.cons>0  and a.is_deleted=0 and a.status_active=1 and  d.is_deleted=0 and d.status_active=1 group by a.id, a.job_no, a.uom, a.item_number_id, a.body_part_id, a.color_type_id, a.width_dia_type, a.construction, a.composition, a.gsm_weight, a.costing_per, b.po_break_down_id, b.color_number_id, b.dia_width, b.remarks, c.contrast_color_id, a.color_size_sensitive, d.hs_code, a.fabric_source,a.fab_nature_id, a.lib_yarn_count_deter_id ';
	//echo $sql; die;
	//echo $txt_order_no_id;
	$sql_data=sql_select($sql);
	
	$powiseCostingPerReqQtyArr=array();
	foreach ($req_qty_arr as $knitwoven=>$knitwovendata)
	{
		foreach ($knitwovendata['grey'] as $poid=>$podata)
		{
			foreach ($podata as $bomid=>$bomdata)
			{
				foreach ($bomdata as $colorid=>$colordata)
				{
					foreach ($colordata as $diawidth=>$diawidthdata)
					{
						foreach ($diawidthdata as $remarks=>$remarksdata)
						{
							foreach ($remarksdata as $uom=>$uomdata)
							{
								$powiseCostingPerReqQtyArr[$bomid][$poid]['greyreqqty']+=$uomdata;
							}
						}
					}
				}
			}
		}
	}
	//print_r($powiseCostingPerReqQtyArr[34805][60978]);
	$job_level_arr=array();
	foreach($sql_data as $sql_row){
		$bom_fabric_dtls_id=$sql_row['id'];
		$poid=$sql_row['po_break_down_id'];
		if($sql_row['color_size_sensitive'] == 3) $item_color=$sql_row['contrast_color_id']; else $item_color=0;
		
		if($item_color== "" || $item_color==0) $item_color=$sql_row['color_number_id'];
		
		if($cbo_fabric_natu==2){
			$req_qty=$req_qty_arr['knit']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$finishreq_qty=$req_qty_arr['knit']['finish'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['knit']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			 if($req_amt>0)
			{
			$rate=$req_amt/$req_qty;
			}
			else $rate=0;
			
			if($req_amt) $req_amt=$req_amt;else $req_amt=0;
			if($finishreq_qty) $finishreq_qty=$finishreq_qty;else $finishreq_qty=0;
			if($req_qty) $req_qty=$req_qty;else $req_qty=0;
		}
		if($cbo_fabric_natu==3){
			$req_qty=$req_qty_arr['woven']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$finishreq_qty=$req_qty_arr['woven']['finish'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['woven']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			 if($req_amt>0)
			{
			$rate=$req_amt/$req_qty;
			}
			else $rate=0;
			 
			if($req_amt) $req_amt=$req_amt;else $req_amt=0;
			if($finishreq_qty) $finishreq_qty=$finishreq_qty;else $finishreq_qty=0;
			if($req_qty) $req_qty=$req_qty;else $req_qty=0;
		}
		$cu_booking_qty=$cu_booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$poid];
		$bal_qty=$req_qty-$cu_booking_qty;
		$bal_amt=$bal_qty*$rate;
		
		if($bal_amt) $bal_amt=$bal_amt;else $bal_amt=0;
		if($bal_qty) $bal_qty=$bal_qty;else $bal_qty=0;
		
		if($sql_row['hs_code']=="") $sql_row['hs_code']="";else $sql_row['hs_code']=$sql_row['hs_code'];
		
		$costing_per=0;
		if($sql_row["costing_per"]==1) $costing_per=12;
		else if($sql_row["costing_per"]==2) $costing_per=1;
		else if($sql_row["costing_per"]==3) $costing_per=24;
		else if($sql_row["costing_per"]==4) $costing_per=36;
		else if($sql_row["costing_per"]==5) $costing_per=48;
		else $costing_per=0;
		
		$itempoWiseReqQty=0;
			
		$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$bom_fabric_dtls_id][$poid]['greyreqqty']/($popaln_cut_qnty_array[$poid]/$item_ratio_array[$sql_row['item_number_id']]))*$costing_per;
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['job_no'][$poid]=$sql_row['job_no'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_id'][$poid]=$poid;
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_number'][$poid]=$po_number_arr[$poid];
		//=================
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_fabric_cost_dtls_id'][$poid]=$bom_fabric_dtls_id;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['body_part_id'][$poid]=$sql_row['body_part_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['construction'][$poid]=$sql_row['construction'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['composition'][$poid]=$sql_row['composition'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['gsm_weight'][$poid]=$sql_row['gsm_weight'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['dia_width'][$poid]=$sql_row['dia_width'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_remarks'][$poid]=$sql_row['remarks'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['hs_code'][$poid]=$sql_row['hs_code'];
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_type_id'][$poid]=$sql_row['color_type_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['width_dia_type'][$poid]=$sql_row['width_dia_type'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['uom'][$poid]=$sql_row['uom'];
		//============
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_number_id'][$poid]=$sql_row['color_number_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['item_color'][$poid]=$item_color;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_qty'][$poid]=$req_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['finreq_qty'][$poid]=$finishreq_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_amt'][$poid]=$req_amt;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_qty'][$poid]=$bal_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_amt'][$poid]=$bal_amt;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$poid]=$rate;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['reqqtycostingper'][$poid]=$itempoWiseReqQty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_size_sensitive'][$poid]=$sql_row['color_size_sensitive'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['fabric_source'][$poid]=$sql_row['fabric_source'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['fab_nature_id'][$poid]=$sql_row['fab_nature_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['lib_yarn_id'][$poid]=$sql_row['lib_yarn_id'];
	}
	//print_r($job_level_arr);
	?>
	<table width="1750" class="rpt_table" border="0" rules="all">
        <thead>
            <th width="80">Job No</th>
            <th width="70">Po No</th>
            <th width="100">Body Part</th>
            <th width="80">Color Type</th>
            <th width="80">Dia Width Type</th>
            <th width="100">Construction</th>
            <th width="150">Composition</th>
            <th width="60">Gsm</th>
            <th width="90">Gmts. Color</th>
            <th width="110">Item Color</th>
            <th width="60">Dia</th>
            <th width="80">HS Code</th>
            <th width="80">Process</th>
            <th width="80">Balance Qty</th>
            <th width="80">Req. Qty</th>
            <th width="70">Adj. Qty</th>
            <th width="80">Ac. Req. Qty</th>
            <th width="50">UOM</th>
            <th width="60">Rate</th>
            <th width="100">Amount</th>
            <th>Remark</th>
        </thead>
	</table>
	<table width="1750" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
        <tbody>
        <?
        if(str_replace("'","",$cbo_level)==2){
			$i=1;
			foreach($job_level_arr as $precost_id){
				foreach($precost_id as $color_id){
					foreach($color_id as $diawith){
						foreach($diawith as $remarks){
							$job_no=implode(",",array_unique($remarks['job_no']));
							$po_break_down_id=implode(",",array_unique($remarks['po_id']));
							$po_number=implode(",",array_unique($remarks['po_number']));
							$bom_fabric_dtls_id=implode(",",array_unique($remarks['pre_cost_fabric_cost_dtls_id']));
							$body_part_id=implode(",",array_unique($remarks['body_part_id']));
							$construction=implode(",",array_unique($remarks['construction']));
							$compositi=implode(",",array_unique($remarks['composition']));
							$gsm_weight=implode(",",array_unique($remarks['gsm_weight']));
							$color_type_id=implode(",",array_unique($remarks['color_type_id']));
							$width_dia_type=implode(",",array_unique($remarks['width_dia_type']));
							$uom=implode(",",array_unique($remarks['uom']));
							
							$color_number_id=implode(",",array_unique($remarks['color_number_id']));
							$item_color=implode(",",array_unique($remarks['item_color']));
							$dia_width=implode(",",array_unique($remarks['dia_width']));
							$pre_cost_remarks=implode(",",array_unique($remarks['pre_cost_remarks']));
							$hscode=implode(",",array_unique($remarks['hs_code']));
							$size_sensitive=implode(",",array_unique($remarks['color_size_sensitive']));
							$fab_nature_id=implode(",",array_unique($remarks['fab_nature_id']));
							$fabric_source=implode(",",array_unique($remarks['fabric_source']));
							$lib_yarn_id=implode(",",array_unique($remarks['lib_yarn_id']));
							
							$req_qty=array_sum($remarks['req_qty']);
							$finishreq_qty=array_sum($remarks['finreq_qty']);
							$rate=array_sum($remarks['rate']);
							$req_amt=array_sum($remarks['req_amt']);
							$bal_qty=array_sum($remarks['bal_qty']);
							$bal_amt=array_sum($remarks['bal_amt']);
							$rate=$req_amt/$req_qty;
							$bal_amt=$bal_qty*$rate;
							$cu_booking_qty=array_sum($cu_booking_data_arr[$bom_fabric_dtls_id][$color_number_id][$dia_width][$pre_cost_remarks]['cu_booking_qty']);
							
							if(number_format($bal_qty,4,'.','') >0){
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>">
                                    <td width="80" style="word-break:break-all"><?=$job_no; ?>
                                    	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>"  readonly  />
                                    </td>
                                    <td width="70" style="word-break: break-all;word-wrap: break-word;"><a href="#" onClick="setdata('<?=$po_number; ?>');">View</a>
                                        <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_break_down_id;?>"  readonly  />
                                    </td>
                                    <td width="100" style="word-break:break-all"><? echo $body_part[$body_part_id]; ?>
                                        <input type="hidden" id="txtpre_cost_fabric_cost_dtls_id_<? echo $i;?>" value="<? echo $bom_fabric_dtls_id;?>"  readonly  />
                                        <input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $body_part_id;?>"  readonly  />
                                        <input type="hidden" id="txtsizesensitive_<? echo $i;?>" value="<? echo $size_sensitive;?>"  readonly  />
                                        <input type="hidden" id="txtfabricsource_<? echo $i;?>" value="<? echo $fabric_source;?>"  readonly  />
                                        <input type="hidden" id="txtfabnatureid_<? echo $i;?>" value="<? echo $fab_nature_id;?>"  readonly  />
                                        <input type="hidden" id="txtlibyarnid_<? echo $i;?>" value="<? echo $lib_yarn_id;?>"  readonly  />
                                    </td>
                                    <td width="80" style="word-break:break-all"><? echo $color_type[$color_type_id]; ?>
                                        <input type="hidden" id="txtcolortype_<? echo $i;?>" value="<? echo $color_type_id;?>"  readonly  />
                                    </td>
                                    <td width="80" style="word-break:break-all"><? echo $fabric_typee[$width_dia_type]; ?>
                                        <input type="hidden" id="txtwidthtype_<? echo $i;?>" value="<? echo $width_dia_type;?>"  readonly  />
                                    </td>
                                    <td width="100" style="word-break:break-all"><? echo $construction; ?>
                                        <input type="hidden" id="txtconstruction_<? echo $i;?>" value="<? echo $construction;?>"  readonly  />
                                    </td>
                                    <td width="150" style="word-break:break-all"><? echo $compositi; ?>
                                        <input type="hidden" id="txtcompositi_<? echo $i;?>" value="<? echo $compositi;?>"  readonly  />
                                    </td>
                                    <td width="60" style="word-break:break-all"><? echo $gsm_weight; ?>
                                    	<input type="hidden" id="txtgsm_weight_<? echo $i;?>" value="<? echo $gsm_weight;?>"  readonly  />
                                    </td>
                                    <td width="90" style="word-break:break-all"><? echo $color_library[$color_number_id]; ?>
                                    	<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $color_number_id;?>"  readonly  />
                                    </td>
                                    <td width="110" style="word-break:break-all"><? echo $color_library[$item_color]; ?>
                                    	<input type="hidden" id="txtitemcolor_<? echo $i;?>" value="<? echo $item_color;?>"  readonly  />
                                    </td>
                                    <td width="60" style="word-break:break-all"><? echo $dia_width; ?>
                                   		<input type="hidden" id="txtdia_<? echo $i;?>" value="<? echo $dia_width;?>"  readonly  />
                                    </td>
									<td width="80">
										<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right;" id="txthscode_<?=$i;?>" value="<?=$hscode;?>" class="text_boxes" />
									</td>
                                    <td width="80" style="word-break:break-all"><? echo $pre_cost_remarks; ?>
                                    	<input type="hidden" id="process_<? echo $i;?>" value="<? echo $pre_cost_remarks;?>"  readonly  />
                                    </td>
                                    <td width="80">	
                                        <input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right;" id="txtbalqnty_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
                                        <input type="hidden" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qty,4,'.','');?>"  readonly  />
                                        <input type="hidden" id="txtfinreqqnty_<?=$i;?>" value="<?=number_format($finishreq_qty,4,'.','');?>" readonly />
                                        <input type="hidden" id="cuqnty_<? echo $i;?>" value="<? echo number_format($cu_booking_qty,4,'.','');?>"  readonly  />
                                        <input type="hidden" id="preconskg_<?=$i; ?>" style="width:20px;" value=""/>
                                    </td>
                                    <td width="80">
                                        <input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_acwoQty(<? echo $i; ?>)"   />
                                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" readonly />
                                    </td>
                                    <td width="70"><input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<? echo $i;?>" value=""  class="text_boxes_numeric" onChange="claculate_acwoQty(<? echo $i; ?>)"  />
                                    </td>
                                    <td width="80"><input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>"  class="text_boxes_numeric"  readonly  /></td>
                                    <td width="50" align="center"><? echo $unit_of_measurement[$uom]; ?></td>
                                    <td width="60"><input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_amount(<? echo $i; ?>)" data-pre-cost-rate="<? echo number_format($rate,4,'.','');?>" data-current-rate="<? echo number_format($rate,4,'.','');?>" disabled  /></td>
                                    <td width="100">
                                    	<input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($bal_amt,4,'.','');?>"  class="text_boxes_numeric"  readonly  />
                                    </td>
                                    <td>
                                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value=""    />
                                        <input type="hidden"   id="bookingid_<?=$i;?>" value=""  readonly  />
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<input type='hidden' id='json_data' name="json_data" value='<?=json_encode($job_level_arr); ?>'/>
	<?
	//ISD-22-30488 for Urmi
	 $sql_pre=sql_select("select a.job_id,a.update_by_fabrice from wo_pre_cost_mst a,wo_po_break_down b where a.job_id=b.job_id and  b.id in($txt_order_no_id) and a.update_by_fabrice>0 and a.status_active=1 and b.status_active=1");
		foreach($sql_pre as $row){
			$job_idArr[$row[csf('job_id')]]=$row[csf('job_id')];
		}
		unset($sql_pre);
		$job_ids=implode(",",$job_idArr);
		$con = connect();
		 $update_rID1= execute_query( "update wo_pre_cost_mst set update_by_fabrice=0 where job_id in($job_ids) and status_active=1 and is_deleted=0",0);
		 oci_commit($con);
	exit();
}

if ($action=="show_fabric_booking"){
	echo load_html_head_contents("Partial Booking Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$fabric_cost_dtls_id=implode(",",array_unique(explode(",",str_replace("'","",$cbo_fabric_description))));

	$cbouom=str_replace("'","",$cbouom);

	$cu_booking_data_arr=array();
	$sql_cu='SELECT a.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", a.po_break_down_id AS "po_break_down_id", a.gmts_color_id AS "color_number_id", a.id AS "booking_id", a.fin_fab_qnty AS "fin_fab_qnty", a.grey_fab_qnty AS "grey_fab_qnty", a.adjust_qty AS "adjust_qty", a.dia_width AS "dia_width", a.pre_cost_remarks AS "pre_cost_remarks" from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id   and  a.po_break_down_id in('.$txt_order_no_id.')   and a.booking_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ';
	//group by a.pre_cost_fabric_cost_dtls_id,a.po_break_down_id,a.gmts_color_id,a.id,a.fin_fab_qnty,a.grey_fab_qnty,a.adjust_qty,a.dia_width,a.pre_cost_remarks
	$dataArray_cu=sql_select($sql_cu);
	foreach($dataArray_cu as $dataArray_row){
		$cu_booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['cu_booking_qty'][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
	}
	unset($dataArray_cu);
	
	$companyId=return_field_value("company_id", "wo_booking_mst", "booking_no=".$txt_booking_no." and status_active=1 and is_deleted=0");
	
	$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name='$companyId' and variable_list=66 and status_active=1");

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;

	$booking_data_arr=array();
	 $sql_pre='select a.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", a.po_break_down_id AS "po_break_down_id", a.gmts_color_id AS "color_number_id", a.id AS "booking_id", a.fin_fab_qnty AS "fin_fab_qnty", a.grey_fab_qnty AS "grey_fab_qnty", a.rate AS "rate", a.amount AS "amount", a.adjust_qty AS "adjust_qty", a.remark AS "remark", a.dia_width AS "dia_width", a.pre_cost_remarks AS "pre_cost_remarks", a.hs_code as "hs_code" from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in('.$txt_order_no_id.')  and a.booking_no='.$txt_booking_no.' and a.booking_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0';
	$result_dataArray=sql_select($sql_pre);
	$booking_data_arr=array();
	foreach($result_dataArray as $row)
	{
		$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['booking_qty'][$row['po_break_down_id']]+=$row['grey_fab_qnty'];
		
		
		//echo $dataArray_row['grey_fab_qnty'].'<br>';
		if($textile_sales_maintain==1)
		{
			$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['ac_booking_qty'][$row['po_break_down_id']]+=$row['grey_fab_qnty']-$row['adjust_qty'];
		}
		else
		{
			$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['ac_booking_qty'][$row['po_break_down_id']]+=$row['fin_fab_qnty']-$row['adjust_qty'];
		}
		
			$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['rate'][$row['po_break_down_id']]=$row['rate'];
		$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['amount'][$row['po_break_down_id']]+=$row['amount'];
		$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['adjust_qty'][$row['po_break_down_id']]+=$row['adjust_qty'];
		$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['remark'][$row['po_break_down_id']]=$row['remark'];
		$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['hscode'][$row['po_break_down_id']]=$row['hs_code'];

		$booking_data_arr[$row['pre_cost_fabric_cost_dtls_id']][$row['color_number_id']][$row['dia_width']][$row['pre_cost_remarks']]['booking_id'][$row['po_break_down_id']]=$row['booking_id'];

	}
	unset($result_dataArray);	
	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !=''){
		$condition->po_id("in($txt_order_no_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);

	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	
	$job_sql=sql_select("select a.company_name, a.job_no, b.id, b.po_number, min(c.id) as cid, sum(c.plan_cut_qnty) as plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where b.id in(".$txt_order_no_id.") and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.company_name, a.job_no, b.id, b.po_number, c.color_number_id, c.size_number_id, c.item_number_id");
	 $po_number_arr=array(); $paln_cut_qnty_array=array(); $popaln_cut_qnty_array=array();
	foreach($job_sql as $jrow)
	{
		$po_number_arr[$jrow[csf("id")]]=$jrow[csf("po_number")];
		$paln_cut_qnty_array[$jrow[csf("cid")]]=$jrow[csf("plan_cut_qnty")];
		$popaln_cut_qnty_array[$jrow[csf("id")]]+=$jrow[csf("plan_cut_qnty")];
		$txt_job_no=$jrow[csf("job_no")];
	}
	unset($job_sql);
	$item_ratio_array=return_library_array("select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");

	$sql='select a.id AS "id", a.job_no AS "job_no", a.uom AS "uom", a.item_number_id as "item_number_id", a.body_part_id AS "body_part_id", a.color_type_id AS "color_type_id", a.width_dia_type AS "width_dia_type", a.construction AS "construction", a.composition AS "composition", a.gsm_weight AS "gsm_weight", a.costing_per AS "costing_per", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id", b.dia_width AS "dia_width", b.remarks AS "remarks",b.itrm_ref AS "itrm_ref", c.contrast_color_id AS "contrast_color_id" from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b left join wo_pre_cos_fab_co_color_dtls c on  b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and b.color_number_id=gmts_color_id  where a.id=b.pre_cost_fabric_cost_dtls_id  and  a.id in('.$fabric_cost_dtls_id.') and b.po_break_down_id in ('.$txt_order_no_id.') and b.cons>0  and a.is_deleted=0 and a.status_active=1 group by a.id, a.job_no, a.uom, a.item_number_id, a.body_part_id, a.color_type_id, a.width_dia_type, a.construction, a.composition, a.gsm_weight, a.costing_per, b.color_number_id, b.po_break_down_id, b.dia_width,b.itrm_ref, b.remarks, c.contrast_color_id';
	//echo $sql;
	$sql_data=sql_select($sql);
	
	$powiseCostingPerReqQtyArr=array();
	foreach ($req_qty_arr as $knitwoven=>$knitwovendata)
	{
		foreach ($knitwovendata['grey'] as $poid=>$podata)
		{
			foreach ($podata as $bomid=>$bomdata)
			{
				foreach ($bomdata as $colorid=>$colordata)
				{
					foreach ($colordata as $diawidth=>$diawidthdata)
					{
						foreach ($diawidthdata as $remarks=>$remarksdata)
						{
							foreach ($remarksdata as $uom=>$uomdata)
							{
								$powiseCostingPerReqQtyArr[$bomid][$poid]['greyreqqty']+=$uomdata;
							}
						}
					}
				}
			}
		}
	}
	
	$job_level_arr=array();
	foreach($sql_data as $sql_row){
		$bom_fabric_dtls_id=$sql_row['id'];
		$item_color=$sql_row['contrast_color_id'];
		$poid=$sql_row['po_break_down_id'];
		if($item_color== "" || $item_color==0) $item_color=$sql_row['color_number_id'];
		
		if($cbo_fabric_natu==2){
			$req_qty=$req_qty_arr['knit']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$finishreq_qty=$req_qty_arr['knit']['finish'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['knit']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$rate=$req_amt/$req_qty;
		}
		if($cbo_fabric_natu==3){
			$req_qty=$req_qty_arr['woven']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$finishreq_qty=$req_qty_arr['woven']['finish'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['woven']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$rate=$req_amt/$req_qty;
		}
		$cu_booking_qty=$cu_booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$poid];
		$booking_qty=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_qty'][$poid];
		$ac_booking_qty=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['ac_booking_qty'][$poid];
		$adjust_qty=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['adjust_qty'][$poid];
		$remark=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['remark'][$poid];
		$hscode=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['hscode'][$poid];
		$rate=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$poid];
		$amount=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['amount'][$poid];

		$booking_id=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_id'][$poid];

		$bal_qty=$req_qty-$cu_booking_qty;
		$bal_amt=$bal_qty-$rate;
		
		$costing_per=0;
		if($sql_row["costing_per"]==1) $costing_per=12;
		else if($sql_row["costing_per"]==2) $costing_per=1;
		else if($sql_row["costing_per"]==3) $costing_per=24;
		else if($sql_row["costing_per"]==4) $costing_per=36;
		else if($sql_row["costing_per"]==5) $costing_per=48;
		else $costing_per=0;
		
		$itempoWiseReqQty=0;
			
		$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$bom_fabric_dtls_id][$poid]['greyreqqty']/($popaln_cut_qnty_array[$poid]/$item_ratio_array[$sql_row['item_number_id']]))*$costing_per;

		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['job_no'][$poid]=$sql_row['job_no'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_id'][$poid]=$poid;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_number'][$poid]=$po_number_arr[$poid];
		//=================
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_fabric_cost_dtls_id'][$poid]=$bom_fabric_dtls_id;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['body_part_id'][$poid]=$sql_row['body_part_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['construction'][$poid]=$sql_row['construction'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['itrm_ref'][$poid]=$sql_row['itrm_ref'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['composition'][$poid]=$sql_row['composition'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['gsm_weight'][$poid]=$sql_row['gsm_weight'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['dia_width'][$poid]=$sql_row['dia_width'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_remarks'][$poid]=$sql_row['remarks'];
	    $job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_type_id'][$poid]=$sql_row['color_type_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['width_dia_type'][$poid]=$sql_row['width_dia_type'];
		//============
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_number_id'][$poid]=$sql_row['color_number_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['item_color'][$poid]=$item_color;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_qty'][$poid]=$req_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['finreq_qty'][$poid]=$finishreq_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_amt'][$poid]=$req_amt;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_qty'][$poid]=$bal_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_amt'][$poid]=$bal_amt;
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_id'][$poid]=$booking_id;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_qty'][$poid]=$booking_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['ac_booking_qty'][$poid]=$ac_booking_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['adjust_qty'][$poid]=$adjust_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['remark'][$poid]=$remark;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['hscode'][$poid]=$hscode;

		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$poid]=$rate;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['amount'][$poid]=$amount;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['reqqtycostingper'][$poid]=$itempoWiseReqQty;
	}
	?>
	<div>
	<? echo load_freeze_divs ("../../../",$permission);  ?>
        <table width="1840" class="rpt_table" border="0" rules="all">
            <thead>
                <th width="80">Job No</th>
                <th width="70">Po No</th>
                <th width="100">Body Part</th>
                <th width="80">Color Type</th>
                <th width="80">Dia Width Type</th>
                <th width="100">Construction</th>
                <th width="150">Composition</th>
                <th width="60">Gsm</th>
                <th width="90">Gmts. Color</th>
                <th width="110">Item Color</th>
				<th width="90">Item Ref.</th>
                <th width="60">Dia</th>
                <th width="80">Process</th>
                <th width="80">HS Code</th>
                <th width="80">Balance Qty</th>
                <th width="80">Req. Qty</th>
                <th width="70">Adj. Qty</th>
                <th width="80">Ac. Req. Qty</th>
                <th width="50">UOM</th>
                <th width="60">Rate</th>
                <th width="100">Amount</th>
                <th>Remark</th>
            </thead>
        </table>
        <table width="1840" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
            <tbody>
            <?
            if(str_replace("'","",$cbo_level)==2){
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
								$bom_fabric_dtls_id=implode(",",array_unique($remarks['pre_cost_fabric_cost_dtls_id']));
								$body_part_id=implode(",",array_unique($remarks['body_part_id']));
								$construction=implode(",",array_unique($remarks['construction']));
								$compositi=implode(",",array_unique($remarks['composition']));
								$gsm_weight=implode(",",array_unique($remarks['gsm_weight']));
								$color_type_id=implode(",",array_unique($remarks['color_type_id']));
								$width_dia_type=implode(",",array_unique($remarks['width_dia_type']));
								$color_number_id=implode(",",array_unique($remarks['color_number_id']));
								$item_color=implode(",",array_unique($remarks['item_color']));
								$itrm_ref=implode(",",array_unique($remarks['itrm_ref']));
								$dia_width=implode(",",array_unique($remarks['dia_width']));
								$pre_cost_remarks=implode(",",array_unique($remarks['pre_cost_remarks']));
								$hscode=implode(",",array_unique($remarks['hscode']));
								
								$booking_id=implode(",",array_unique($remarks['booking_id']));
								$req_qty=array_sum($remarks['req_qty']);
								$finishreq_qty=array_sum($remarks['finreq_qty']);
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
								$cu_booking_qty=array_sum($cu_booking_data_arr[$bom_fabric_dtls_id][$color_number_id][$dia_width][$pre_cost_remarks]['cu_booking_qty']);
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>">
                                    <td width="80" style="word-break:break-all"><?=$job_no; ?><input type="hidden" id="txtjob_<?=$i;?>" value="<?=$job_no;?>" readonly /></td>
                                    <td width="70" style="word-break: break-all;word-wrap: break-word;">
                                        <a href="#" onClick="setdata('<?=$po_number;?>' )">View</a>
                                        <input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$po_break_down_id;?>" readonly />
                                    </td>
                                    <td width="100" style="word-break:break-all"><?=$body_part[$body_part_id]; ?>
                                        <input type="hidden" id="txtpre_cost_fabric_cost_dtls_id_<?=$i;?>" value="<?=$bom_fabric_dtls_id;?>" readonly />
                                        <input type="hidden" id="txtbodypart_<?=$i;?>" value="<?=$body_part_id;?>" readonly />
										<input type="hidden" id="txtsizesensitive_<? echo $i;?>" value=""  readonly  />
										<input type="hidden" id="txtfabricsource_<? echo $i;?>" value=""  readonly  />
										<input type="hidden" id="txtfabnatureid_<? echo $i;?>" value=""  readonly  />
										<input type="hidden" id="txtlibyarnid_<? echo $i;?>" value=""  readonly  />
                                    </td>
                                    <td width="80" style="word-break:break-all"><?=$color_type[$color_type_id]; ?>
                                        <input type="hidden" id="txtcolortype_<?=$i;?>" value="<?=$color_type_id;?>" readonly />
                                    </td>
                                    <td width="80" style="word-break:break-all"><?=$fabric_typee[$width_dia_type]; ?>
                                        <input type="hidden" id="txtwidthtype_<?=$i;?>" value="<?=$width_dia_type;?>"  readonly  />
                                    </td>
                                    <td width="100" style="word-break:break-all"><?=$construction; ?>
                                        <input type="hidden" id="txtconstruction_<?=$i;?>" value="<?=$construction;?>"  readonly  />
                                    </td>
                                    <td width="150" style="word-break:break-all"><?=$compositi; ?>
                                        <input type="hidden" id="txtcompositi_<?=$i;?>" value="<?=$compositi;?>"  readonly  />
                                    </td>
                                    <td width="60" style="word-break:break-all"><?=$gsm_weight; ?>
                                        <input type="hidden" id="txtgsm_weight_<?=$i;?>" value="<?=$gsm_weight;?>"  readonly  />
                                    </td>
                                    <td width="90" style="word-break:break-all"><?=$color_library[$color_number_id]; ?>
                                        <input type="hidden" id="txtgmtcolor_<?=$i;?>" value="<?=$color_number_id;?>"  readonly  />
                                    </td>
                                    <td width="110" style="word-break:break-all"><?=$color_library[$item_color]; ?>
                                        <input type="hidden" id="txtitemcolor_<?=$i;?>" value="<?=$item_color;?>"  readonly  />
                                    </td>
									<td width="90" style="word-break:break-all"><?=$itrm_ref; ?>
                                        <input type="hidden" id="txtitrmref_<?=$i;?>" value="<?=$itrm_ref;?>"  readonly  />
                                    </td>
                                    <td width="60" style="word-break:break-all"><?=$dia_width; ?>
                                        <input type="hidden" id="txtdia_<?=$i;?>" value="<?=$dia_width;?>"  readonly  />
                                    </td>
                                    <td width="80" style="word-break:break-all"><?=$pre_cost_remarks; ?>
                                        <input type="hidden" id="process_<?=$i;?>" value="<?=$pre_cost_remarks;?>"  readonly  />
                                    </td>
                                    <td width="80">
										<input type="text"  style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txthscode_<?=$i;?>" value="<?=$hscode;?>" />
			                        </td>
                                    <td width="80">
                                        <input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalqnty_<?=$i;?>" value="<?=number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
                                        <input type="hidden" id="txtreqqnty_<?=$i;?>" value="<?=number_format($req_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
                                        <input type="hidden" id="txtfinreqqnty_<?=$i;?>" value="<?=number_format($finishreq_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
                                        <input type="hidden" id="cuqnty_<?=$i;?>" value="<?=number_format($cu_booking_qty,4,'.',''); ?>" class="text_boxes_numeric" readonly />
                                        <input type="hidden" id="preconskg_<?=$i; ?>" style="width:20px;" value=""/>
                                    </td>
                                    <td width="80">
                                        <input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i;?>" value="<?=number_format($booking_qty,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_acwoQty(<?=$i; ?>)" />
                                        <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<?=$i;?>" value="<?=number_format($booking_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
                                     </td>
                                    <td width="70">
                                        <input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<?=$i;?>" value="<?=number_format($adjust_qty,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_acwoQty(<?=$i; ?>)" />
                                    </td>
                                    <td width="80">
                                        <input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  =text-align:right; background-color:#FFC" id="txtacwoq_<?=$i;?>" value="<?=number_format($ac_booking_qty,4,'.','');?>" class="text_boxes_numeric" readonly   />
                                    </td>
                                    </td>
                                    <td width="50" align="center"><?=$unit_of_measurement[$sql_row['uom']]; ?></td>
                                    <td width="60"><input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i;?>" value="<?=number_format($rate,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_amount(<?=$i; ?>)" data-pre-cost-rate="<?=number_format($pre_cost_rate,4,'.','');?>" data-current-rate="<?=number_format($rate,4,'.','');?>" disabled /></td>
                                    <td width="100">
                                        <input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i;?>" value="<?=number_format($amount,4,'.','');?>" class="text_boxes_numeric" readonly  />
                                    </td>
                                    <td>
                                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="txtremark_<?=$i;?>" value="<?=$remark;?>" />
                                        <input type="hidden" id="bookingid_<?=$i;?>" value="<?=$booking_id;?>" readonly />
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
        <input type='hidden' id='json_data' name="json_data" value='<?=json_encode($job_level_arr); ?>'/>
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
	$sql='select a.id AS "id",a.job_no AS "job_no", a.po_break_down_id AS "po_break_down_id",a.grey_fab_qnty AS "grey_fab_qnty",a.fin_fab_qnty AS "fin_fab_qnty",a.adjust_qty "adjust_qty",a.rate AS "rate",a.amount AS "amount",a.gmts_color_id AS "gmts_color_id",a.dia_width AS "dia_width", b.id AS "pre_cost_fabric_cost_dtls_id", b.body_part_id AS "body_part_id",b.color_type_id AS "color_type_id",b.fabric_description AS "fabric_description", b.gsm_weight AS "gsm_weight",b.uom AS "uom" from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active =1 and a.is_deleted=0  and a.booking_no='.$txt_booking_no.'';
	
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
            <th width="100">Req. Qty.</th>
            <th width="100">Adj. Qty.</th>
            <th width="100">Ac. Req. Qty.</th>
            <th width="60">Rate</th>
            <th width="80">Amount</th>
            <th><input type="checkbox" name="chkdeleteall" id="chkdeleteall" value="2" ><a href="#" onClick="deletedata();">Delete All</a></th>
        </thead>
	</table>
	<div style="max-height:200px; overflow-y:scroll; width:1200px"  align="left">
        <table width="1183" class="rpt_table" id="tbl_fabric_booking_list" border="0" rules="all">
            <tbody>
            <? $i=1;
            if($cbo_level==11){
                foreach($dataArray as $row){
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
                    ?>
                    <tr>
                        <td width="50">
                            <a href="#" onClick="set_data('<?=$po_break_down_id; ?>','<?=$po_number; ?>','<?=$row['pre_cost_fabric_cost_dtls_id']; ?>','<?=$booking_id; ?>');">Edit</a>
                        </td>
                        <td width="80"><?=$job_no; ?></td>
                        <td width="80" style="word-break: break-all;word-wrap: break-word;"><a href="#" onClick="setdata('<?=$po_number; ?>');">View</a><? //echo $po_number; ?></td>
                        <td width="250"><?=$fabric_des; ?></td>
                        <td width="100" align="right"><?=number_format($grey_fab_qnty,4,'.',''); ?></td>
                        <td width="100" align="right"><?=number_format($grey_fab_qnty,4,'.',''); ?></td>
                        <td width="100" align="right"><?=number_format($adjust_qty,4,'.',''); ?></td>
                        <td width="100" align="right"><?=number_format($fin_fab_qnty,4,'.',''); ?></td>
                        <td width="60" align="right"><?=number_format($rate,4,'.','');?></td>
                        <td width="80" align="right"><?=number_format($amount,4,'.','');?></td>
                        <td>
                        <a href="#" onClick="deletedata('<?=$po_break_down_id; ?>','<?=$po_number; ?>','<?=$row['pre_cost_fabric_cost_dtls_id']; ?>','<?=$booking_id; ?>');">Delete</a>
                        </td>
                    </tr>
                    <?
                }
            }
            if($cbo_level==2 || $cbo_level==1){
                $i=1;
                foreach($job_level_arr as $key=>$precost_id){
                    $job_no=implode(",",array_unique($precost_id['job_no']));
                    $po_break_down_id=implode(",",array_unique($precost_id['po_id']));
                    $po_number=implode(",",array_unique($precost_id['po_number']));
                    $grey_fab_qnty=array_sum($precost_id['grey_fab_qnty']);
                    $adjust_qty=array_sum($precost_id['adjust_qty']);
                    $fin_fab_qnty=array_sum($precost_id['fin_fab_qnty']);
                    $booking_id=implode(",",array_unique($precost_id['booking_id']));
                    //$rate=array_sum($precost_id['rate']);
                    $amount=array_sum($precost_id['amount']);
                    $rate=$amount/$fin_fab_qnty;
                    ?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" >
                        <td width="50" align="center"><a href="#" onClick="set_data('<?=$po_break_down_id; ?>','<?=$po_number; ?>','<?=$key; ?>','<?=$booking_id; ?>');">Edit</a></td>
                        <td width="80"><?=$job_no; ?></td>
                        <td width="80" style="word-break: break-all;word-wrap: break-word;" align="center"><a href="#" onClick="setdata('<?=$po_number; ?>' )">View</a></td>
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
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FR', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=10 and $date_cond=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1);
		$field_array="id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, ready_to_approved, inserted_by, insert_date, rmg_process_breakdown, fabric_composition, uom, remarks, entry_form, cbo_level, brand_id, isgreyfab_purchase, ship_mode, pay_term, tenor, shippingmark_breck_down";
		 $data_array ="(".$id.",10,2,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_fabric_natu.",".$cbo_fabric_source.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_booking_month.",".$cbo_booking_year.",".$cbo_supplier_name.",".$txt_attention.",".$txt_booking_percent.",".$txt_colar_excess_percent.",".$txt_cuff_excess_percent.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$processloss_breck_down.",".$txt_fabriccomposition.",".$cbouom.",".$txt_remark.",611,".$cbo_level.",".$cbo_brand_id.",".$cbo_greyfab_purch.",".$cbo_shipmode.",".$cbo_payterm.",".$txt_tenor.",".$hiddshippingmark_breck_down.")";
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
		$booking_number=sql_select("SELECT a.booking_no from wo_booking_mst a,wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=12 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and b.req_id=$update_id group by a.booking_no");

		if(count($booking_number)>0){
			foreach($booking_number as $row){
				$booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
			}
			$booking_str=implode(",",$booking_arr);
			echo "booking**".str_replace("'","",$txt_booking_no)."**".$booking_str;
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

		$field_array="item_category*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*booking_month*booking_year*supplier_id*attention*booking_percent*colar_excess_percent*cuff_excess_percent*ready_to_approved*updated_by*update_date*rmg_process_breakdown*fabric_composition*uom*remarks*cbo_level*brand_id*isgreyfab_purchase*ship_mode*pay_term*tenor*shippingmark_breck_down";
		 $data_array ="".$cbo_fabric_natu."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$txt_attention."*".$txt_booking_percent."*".$txt_colar_excess_percent."*".$txt_cuff_excess_percent."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$processloss_breck_down."*".$txt_fabriccomposition."*".$cbouom."*".$txt_remark."*".$cbo_level."*".$cbo_brand_id."*".$cbo_greyfab_purch."*".$cbo_shipmode."*".$cbo_payterm."*".$txt_tenor."*".$hiddshippingmark_breck_down."";
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
		$booking_number=sql_select("SELECT a.booking_no from wo_booking_mst a,wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=12 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and b.req_id=$update_id group by a.booking_no");

		if(count($booking_number)>0){
			foreach($booking_number as $row){
				$booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
			}
			$booking_str=implode(",",$booking_arr);
			echo "booking**".str_replace("'","",$txt_booking_no)."**".$booking_str;
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
	//$json_data=json_decode(str_replace("'","",$json_data));
	$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name=$cbo_company_name and variable_list=66 and status_active=1");

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;
	$jobnoArr=array();
	for ($i=1; $i<=$total_row; $i++)
	{
		$txtjob="txtjob_".$i;
		$txtpoid_="txtpoid_".$i;
		
		$jobnoArr[]="'".str_replace("'","",$$txtjob)."'";
	}
	
	$sqljob=sql_select("select id from wo_po_details_master where job_no in (".implode(",",$jobnoArr).") and status_active=1 and is_deleted=0");
	foreach($sqljob as $row)
	{
		$jobidArr[$row[csf('id')]]=$row[csf('id')];
	}
	
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
		 $field_array1="id, is_short, booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, color_type, construction, copmposition, gsm_weight, dia_width, fabric_color_id, gmts_color_id, fin_fab_qnty, grey_fab_qnty, adjust_qty, rate, amount, process_loss_percent, remark, pre_cost_remarks, hs_code, precons, inserted_by, insert_date, status_active, is_deleted";
		 $j=1;
		 $new_array_color=array(); $poIdChkArr=array();
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
			 $txtfinreqqnty="txtfinreqqnty_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtadj="txtadj_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i;
			 $hscode="hscode_".$i;
			 $preconskg="preconskg_".$i;

			 $precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			 $colorid=str_replace("'","",$$txtgmtcolor);
             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $finreqqnty=str_replace("'","",$$txtfinreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $rate=str_replace("'","",$$txtrate);
			 $amount=str_replace("'","",$$txtamount);
			 $poId=str_replace("'","",$$txtpoid_);
			 
			 $poIdChkArr[$poId]=$poId;
			 
			 $processLossPer=0;
			 $processLossPer=(($reqqnty-$finreqqnty)/$finreqqnty)*100;
			 
			 $avgFinKg=$finreqqnty/$reqqnty;
			 
			 $finAcWoQty=$acwoq*$avgFinKg;
			$finWoQty=$greyWoQty=$pLossPer=0;
			if($textile_sales_maintain==1)
			{
				$finWoQty=$finAcWoQty;
				$greyWoQty=$woq;
				$pLossPer=number_format($processLossPer,2,'.','');
			}
			else
			{
				$finWoQty=$acwoq;
				$greyWoQty=$woq;
				$pLossPer=number_format($processLossPer,2,'.','');
			}
			 
			 //foreach($json_data->$precostid->$colorid->po_id as $poId){
				 if($woq>0){
				 if ($j!=1) $data_array1 .=",";
					 $data_array1 .="(".$id_dtls.",2,".$update_id.",".$precostid.",".$$txtpoid_.",".$$txtjob.",".$txt_booking_no.",10,".$$txtcolortype_.",".$$txtconstruction_.",".$$txtcompositi_.",".$$txtgsm_weight_.",".$$txtdia_.",".$$txtitemcolor.",".$$txtgmtcolor.",".$finWoQty.",".$greyWoQty.",".$$txtadj.",".$rate.",".$amount.",".$pLossPer.",".$$txtremark.",".$$process.",".$$hscode.",".$$preconskg.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					 if($str_po_id=="") $str_po_id=$poId; else $str_po_id.='*'.$poId;
					 $id_dtls=$id_dtls+1;
					 $j++;
				 }
			 //}
		 }
		 //echo $data_array1;
		 $poidCond=implode(",",$poIdChkArr);
		 $sql_pre=sql_select("select a.update_by_fabrice from wo_pre_cost_mst a,wo_po_break_down b where a.job_id=b.job_id and  b.id in($poidCond) and a.update_by_fabrice>0 and a.status_active=1 and b.status_active=1");
		foreach($sql_pre as $row){
			$update_by_fabrice=$row[csf('update_by_fabrice')];
		}
		if($update_by_fabrice==1)
		{
			echo "budget_updated**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;	
		}
		
		 $flag=1;
		 if(str_replace("'","",$lib_tna_intregrate)==1)
		 {
			 $delivery_date=fnc_delivery_date($str_po_id,str_replace("'","",$txt_booking_no));
			 //$rID_date."**".$delivery_date;
			 $ex_date=explode("**",$delivery_date);
			 $rID_date=$ex_date[0];
			 //$delivery_date
			 if($ex_date[0]==1) $flag=1; else $flag=0;
		 }

		 //echo "10**".$delivery_date; die;
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 if($rID==1 && $flag==1) $flag=1; else $flag=0;

		 if($flag==1)
		 {
			 $rID1= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no ='".str_replace("'", "", $txt_booking_no)."' and status_active=1 and is_deleted=0",0);
			 if( $rID1==1 && $flag==1) $flag=1; else $flag=0;
		 }

		 check_table_status( $_SESSION['menu_id'],0);
		 if($db_type==0){
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		 }
		 else if($db_type==2 || $db_type==1 ){
			if($flag==1){
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
		
		
		$po_arr=array(); $olddtlsidArr=array(); $preDataArr=array();
		if(str_replace("'","",$txt_booking_no)!=''){
			$sql_po= sql_select("select b.id as dtlsid, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, b.color_type, b.construction, b.copmposition, b.gsm_weight, b.dia_width from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=10 and a.is_short=2 and a.entry_form=611 and b.booking_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
			foreach($sql_po as $pdata){
				$po_arr[$pdata[csf('po_break_down_id')]]=$pdata[csf('po_break_down_id')];
				$olddtlsidArr[$pdata[csf('dtlsid')]]=$pdata[csf('dtlsid')];
				$strold=$pdata[csf('construction')].$pdata[csf('fabric_color_id')].$pdata[csf('copmposition')].$pdata[csf('color_type')].$pdata[csf('gsm_weight')].$pdata[csf('dia_width')];
				$preDataArr[$pdata[csf('pre_cost_fabric_cost_dtls_id')]][$pdata[csf('po_break_down_id')]][$strold]=$strold;
			}
		}
		
		$field_array_up1="color_type*construction*copmposition*gsm_weight*dia_width*fabric_color_id*gmts_color_id*fin_fab_qnty*grey_fab_qnty*adjust_qty*rate*amount*process_loss_percent*remark*pre_cost_remarks*hs_code*precons*updated_by*update_date";
		$new_array_color=array(); $str_po_id=""; $plandatachnage=0;
		for ($i=1;$i<=$total_row;$i++){

			$txtjob="txtjob_".$i;
			$txtpoid="txtpoid_".$i;
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
			 $txtfinreqqnty="txtfinreqqnty_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtadj="txtadj_".$i;

			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $bookingid="bookingid_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i;
			 $hscode="hscode_".$i;
			 $preconskg="preconskg_".$i;

			 $precostid=str_replace("'","",$cbo_fabric_description);
			 $colorid=str_replace("'","",$$txtgmtcolor);
             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $finreqqnty=str_replace("'","",$$txtfinreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $rate=str_replace("'","",$$txtrate);
			 $amount=str_replace("'","",$$txtamount);
			 
			 $processLossPer=0;
			 $processLossPer=(($reqqnty-$finreqqnty)/$finreqqnty)*100;
			 
			 $avgFinKg=$finreqqnty/$reqqnty;
			 
			 $finAcWoQty=$acwoq*$avgFinKg;
			$finWoQty=$greyWoQty=$pLossPer=0;
			if($textile_sales_maintain==1)
			{
				$finWoQty=$finAcWoQty;
				$greyWoQty=$woq;
				$pLossPer=number_format($processLossPer,2,'.','');
			}
			else
			{
				$finWoQty=$acwoq;
				$greyWoQty=$woq;
				$pLossPer=number_format($processLossPer,2,'.','');
			}
			
			$strnew=str_replace("'",'',$$txtconstruction_).str_replace("'",'',$$txtitemcolor).str_replace("'",'',$$txtcompositi_).str_replace("'",'',$$txtcolortype_).str_replace("'",'',$$txtgsm_weight_).str_replace("'",'',$$txtdia_);
			 
			 if($preDataArr[str_replace("'",'',$$txtpre_cost_fabric_cost_dtls_id)][str_replace("'",'',$$txtpoid)][$strnew]=="")
			 {
				$plandatachnage=1; 
			 }

			$poId=str_replace("'","",$$txtpoid);
			 if(str_replace("'",'',$$bookingid)!=""){
				$id_arr[]=str_replace("'",'',$$bookingid);
				$data_array_up1[str_replace("'",'',$$bookingid)] =explode("*",("".$$txtcolortype_."*".$$txtconstruction_."*".$$txtcompositi_."*".$$txtgsm_weight_."*".$$txtdia_."*".$$txtitemcolor."*".$$txtgmtcolor."*".$finWoQty."*".$greyWoQty."*".$$txtadj."*".$rate."*".$amount."*".$pLossPer."*".$$txtremark."*".$$process."*".$$hscode."*".$$preconskg."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				if($str_po_id=="") $str_po_id=$poId; else $str_po_id.='*'.$poId;
			 }
		 }
		 $flag=1;
		 if(str_replace("'","",$lib_tna_intregrate)==1)
		 {
			 $delivery_date=fnc_delivery_date($str_po_id,str_replace("'","",$txt_booking_no));
			 //$rID_date."**".$delivery_date;
			 $ex_date=explode("**",$delivery_date);
			 $rID_date=$ex_date[0];
			 //$delivery_date
			 if($ex_date[0]==1) $flag=1; else $flag=0;
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
		 if($rID==1 && $flag==1) $flag=1; else $flag=0;

		 if($flag==1)
		 {
			 $rID1= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no ='".str_replace("'", "", $txt_booking_no)."' and status_active=1 and is_deleted=0",0);
			 if( $rID1==1 && $flag==1) $flag=1; else $flag=0;
		 }
		// $plandatachnage=1;
		if($plandatachnage==1)
		{
			fnc_isdyeingplan("WO_PO_DETAILS_MASTER", $jobidArr);
			fnc_isdyeingplan("WO_BOOKING_DTLS", $newidarr);
		}

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

		/*$recv_number=return_field_value( "recv_number", "inv_receive_master"," booking_no=$txt_booking_no  and status_active=1 and  is_deleted=0");
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

		/*$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$txt_all_update_id=str_replace("*",",",str_replace("'","",$txt_all_update_id));
		$rID=sql_multirow_update("wo_booking_dtls",$field_array,$data_array,"id","".$txt_all_update_id."",1);*/
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		 for ($i=1;$i<=$total_row;$i++){

			// $txtjob="txtjob_".$i;
			 //$txtpoid_="txtpoid_".$i;
			 //$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			 $bookingid="bookingid_".$i;
			 //$precostid=str_replace("'","",$cbo_fabric_description);
			// $rID=execute_query( "delete from wo_booking_dtls where  pre_cost_fabric_cost_dtls_id in (".str_replace("'","",$precostid).")",0);
			 //$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  pre_cost_fabric_cost_dtls_id in (".str_replace("'","",$precostid).") and booking_no=$txt_booking_no",0);
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

if ($action=="save_update_delete_dtls_job_level")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$json_data=json_decode(str_replace("'","",$json_data));
	
	$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name=$cbo_company_name and variable_list=66 and status_active=1");

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;
	
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

		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, is_short, booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, color_type, construction,itrm_ref, copmposition, gsm_weight, dia_width, fabric_color_id, gmts_color_id, fin_fab_qnty, grey_fab_qnty, adjust_qty, rate, amount, process_loss_percent, remark, pre_cost_remarks, hs_code, precons,sensitivity,body_part,fabric_source,fab_nature, lib_yarn_id, inserted_by, insert_date, status_active, is_deleted";
		 $j=1;
		 $new_array_color=array(); $str_po_id="";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			$txtjob="txtjob_".$i;
			$txtpoid_="txtpoid_".$i;
			 //=========
			$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconstruction_".$i;
			$txtitrmref_="txtitrmref_".$i;	
			$txtcompositi_="txtcompositi_".$i;
			$txtgsm_weight_="txtgsm_weight_".$i;
			$txtdia_="txtdia_".$i;
			//============
			 $txtgmtcolor="txtgmtcolor_".$i;
			 $txtitemcolor="txtitemcolor_".$i;
			 $txtbalqnty="txtbalqnty_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtfinreqqnty="txtfinreqqnty_".$i;
			 $txtadj="txtadj_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i;
			 $hscode="hscode_".$i;
			 $sensitivity="txtsizesensitive_".$i;
			 $body_part="txtbodypart_".$i;
			 $fabric_source="txtfabricsource_".$i;
			 $fab_nature="txtfabnatureid_".$i;
			 $lib_yarn_id="txtlibyarnid_".$i;

			 

			 $precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			 $colorid=str_replace("'","",$$txtgmtcolor);
			 $dia=str_replace("'","",$$txtdia_);
			 $process_p=str_replace("'","",$$process);
             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $finreqqnty=str_replace("'","",$$txtfinreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $adq=str_replace("'","",$$txtadj);
			 $rate=str_replace("'","",$$txtrate);
			 $poids=str_replace("'","",$$txtpoid_);
			 $poIdChkArr[$poids]=$poids;
			 
			 $processLossPer=0;
			 $processLossPer=(($reqqnty-$finreqqnty)/$finreqqnty)*100;
			 
			 $avgFinKg=$finreqqnty/$reqqnty;

			 foreach($json_data->$precostid->$colorid->$dia->$process_p->po_id as $poId){
				 if($woq>0){
					$wQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$woq;
					$AcwQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$acwoq;
					$adjQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$adq;
					$reqqtycostingper=$json_data->$precostid->$colorid->$dia->$process_p->reqqtycostingper->$poId;
					 
					$finAcWoQty=$AcwQty*$avgFinKg;
					$finWoQty=$greyWoQty=$pLossPer=0;
					if($textile_sales_maintain==1)
					{
						$finWoQty=$finAcWoQty;
						$greyWoQty=$wQty;
						$pLossPer=number_format($processLossPer,2,'.','');
					}
					else
					{
						$finWoQty=$AcwQty;
						$greyWoQty=$wQty;
						$pLossPer=number_format($processLossPer,2,'.','');
					}
					 
					 $amount=$AcwQty*$rate;
					 if ($j!=1) $data_array1 .=",";
					 $data_array1 .="(".$id_dtls.",2,".$update_id.",".$precostid.",".$poId.",".$$txtjob.",".$txt_booking_no.",10,".$$txtcolortype_.",".$$txtconstruction_.",".$$txtitrmref_.",".$$txtcompositi_.",".$$txtgsm_weight_.",".$$txtdia_.",".$$txtitemcolor.",".$$txtgmtcolor.",".$finWoQty.",".$wQty.",".$adjQty.",".$rate.",".$amount.",".$pLossPer.",".$$txtremark.",".$$process.",".$$hscode.",'".$reqqtycostingper."',".$$sensitivity.",".$$body_part.",".$$fabric_source.", ".$$fab_nature.",".$$lib_yarn_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					 if($str_po_id=="") $str_po_id=$poId; else $str_po_id.='*'.$poId;
					 $id_dtls=$id_dtls+1;
					 $j++;
				 }
			 }
		 }
		$poidCond=implode(",",$poIdChkArr);
		 $sql_pre=sql_select("select a.update_by_fabrice from wo_pre_cost_mst a,wo_po_break_down b where a.job_id=b.job_id and  b.id in($poidCond) and a.update_by_fabrice>0 and a.status_active=1 and b.status_active=1");
		foreach($sql_pre as $row){
			$update_by_fabrice=$row[csf('update_by_fabrice')];
		}
		if($update_by_fabrice==1)
		{
			echo "budget_updated**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;	
		}
		
		/* $flag=1;
		if(str_replace("'","",$lib_tna_intregrate)==1)
		{
			$delivery_date=fnc_delivery_date($str_po_id,str_replace("'","",$txt_booking_no));
			$ex_date=explode("**",$delivery_date);
			$rID_date=$ex_date[0];
			if($ex_date[0]==1) $flag=1; else $flag=0;
		} */

		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 if($rID==1) $flag=1; else $flag=0;
		
		/*  if($flag==1)
		 {
			 $rID1= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no =".$txt_booking_no." and status_active=1 and is_deleted=0",0);
			 if( $rID1==1 && $flag==1) $flag=1; else $flag=0;
		 } */
		 check_table_status( $_SESSION['menu_id'],0);
		 if($db_type==0){
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		 }
		 else if($db_type==2 || $db_type==1 ){
			if($flag==1){
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
		$booking_number=sql_select("SELECT a.booking_no from wo_booking_mst a,wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=12 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and b.req_id=$update_id group by a.booking_no");

		if(count($booking_number)>0){
			foreach($booking_number as $row){
				$booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
			}
			$booking_str=implode(",",$booking_arr);
			echo "booking**".str_replace("'","",$txt_booking_no)."**".$booking_str;
			disconnect($con);die;
		}
		//$field_array1="id, is_short, booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, color_type, construction,itrm_ref, copmposition, gsm_weight, dia_width, fabric_color_id, gmts_color_id, fin_fab_qnty, grey_fab_qnty, adjust_qty, rate, amount, process_loss_percent, remark, pre_cost_remarks, hs_code, precons,sensitivity,body_part,fabric_source,fab_nature, lib_yarn_id, inserted_by, insert_date, status_active, is_deleted";
		
		 $field_array_up1="is_short*color_type*construction*itrm_ref*copmposition*gsm_weight*dia_width*fabric_color_id*gmts_color_id*fin_fab_qnty*grey_fab_qnty*adjust_qty*rate*amount*process_loss_percent*remark*pre_cost_remarks*hs_code*precons*updated_by*update_date";
		 $new_array_color=array(); $str_po_id=""; $plandatachnage=0; //echo "10**";
		 for ($i=1;$i<=$total_row;$i++){
			 $txtjob="txtjob_".$i;
			 $txtpoid_="txtpoid_".$i;
			 //=========
			$txtpre_cost_fabric_cost_dtls_id="txtpre_cost_fabric_cost_dtls_id_".$i;
			$txtcolortype_="txtcolortype_".$i;
			$txtconstruction_="txtconstruction_".$i;
			$txtitrmref_="txtitrmref_".$i;
			$txtcompositi_="txtcompositi_".$i;
			$txtgsm_weight_="txtgsm_weight_".$i;
			$txtdia_="txtdia_".$i;
			//============
			 $txtgmtcolor="txtgmtcolor_".$i;
			 $txtitemcolor="txtitemcolor_".$i;
			 $txtbalqnty="txtbalqnty_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtfinreqqnty="txtfinreqqnty_".$i;
			 $txtadj="txtadj_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $bookingid="bookingid_".$i;
			 $txtremark="txtremark_".$i;
			 $txtacwoq="txtacwoq_".$i;
			 $process="process_".$i;
			 $hscode="hscode_".$i;

			 $precostid=str_replace("'","",$$txtpre_cost_fabric_cost_dtls_id);
			 $colorid=str_replace("'","",$$txtgmtcolor);
			 $dia=str_replace("'","",$$txtdia_);
			 $process_p=str_replace("'","",$$process);

             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $finreqqnty=str_replace("'","",$$txtfinreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $adq=str_replace("'","",$$txtadj);
			 $rate=str_replace("'","",$$txtrate);
			 
			 $processLossPer=0;
			 $processLossPer=(($reqqnty-$finreqqnty)/$finreqqnty)*100;
			 
			 $avgFinKg=$finreqqnty/$reqqnty;
			 
			 $strnew=str_replace("'",'',$$txtconstruction_).str_replace("'",'',$$txtitemcolor).str_replace("'",'',$$txtcompositi_).str_replace("'",'',$$txtcolortype_).str_replace("'",'',$$txtgsm_weight_).str_replace("'",'',$$txtdia_);
			 
			 if($preDataArr[str_replace("'",'',$$txtpre_cost_fabric_cost_dtls_id)][str_replace("'",'',$$txtpoid_)][$strnew]=="")
			 {
				$plandatachnage=1; 
			 }
			 
			//print_r($json_data->$precostid->$colorid->$dia->$process_p);
			 foreach($json_data->$precostid->$colorid->$dia->$process_p->po_id as $poId){
				 $wQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$woq;
				 $AcwQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$acwoq;
				 $adjQty=($json_data->$precostid->$colorid->$dia->$process_p->req_qty->$poId/$reqqnty)*$adq;
				 $reqqtycostingper=$json_data->$precostid->$colorid->$dia->$process_p->reqqtycostingper->$poId;
				 
				 $finAcWoQty=$AcwQty*$avgFinKg;
				// echo $json_data->$precostid->$colorid->$dia->$process_p->finreq_qty->$poId.'='.number_format($AcwQty).'='.number_format($finAcWoQty).'<br>';
				$finWoQty=$greyWoQty=$pLossPer=0;
				if($textile_sales_maintain==1)
				{
					$finWoQty=$finAcWoQty;
					$greyWoQty=$wQty;
					$pLossPer=number_format($processLossPer,2,'.','');
				}
				else
				{
					$finWoQty=$AcwQty;
					$greyWoQty=$wQty;
					$pLossPer=number_format($processLossPer,2,'.','');
				}
				 
				$amount=$AcwQty*$rate;
				if(str_replace("'",'',$$bookingid)!="")
				{
					$newidarr[]=str_replace("'",'',$json_data->$precostid->$colorid->$dia->$process_p->booking_id->$poId);
					$id_arr[]=str_replace("'",'',$json_data->$precostid->$colorid->$dia->$process_p->booking_id->$poId);
					$data_array_up1[str_replace("'",'',$json_data->$precostid->$colorid->$dia->$process_p->booking_id->$poId)] =explode("*",("2*".$$txtcolortype_."*".$$txtconstruction_."*".$$txtitrmref_."*".$$txtcompositi_."*".$$txtgsm_weight_."*".$$txtdia_."*".$$txtitemcolor."*".$$txtgmtcolor."*".$finWoQty."*".$wQty."*".$adjQty."*".$rate."*".$amount."*".$pLossPer."*".$$txtremark."*".$$process."*".$$hscode."*'".$reqqtycostingper."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					if($str_po_id=="") $str_po_id=$poId; else $str_po_id.='*'.$poId;
				}
			}
		}
		//echo "10**".bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ); die;
		//print_r($data_array_up1); die;
		$rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
		if($rID==1) $flag=1; else $flag=0;
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
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$booking_number=sql_select("SELECT a.booking_no from wo_booking_mst a,wo_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=12 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and b.req_id=$update_id group by a.booking_no");

		if(count($booking_number)>0){
			foreach($booking_number as $row){
				$booking_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
			}
			$booking_str=implode(",",$booking_arr);
			echo "booking**".str_replace("'","",$txt_booking_no)."**".$booking_str;
			disconnect($con);die;
		}

		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);
		//echo "10**";
		for ($i=1;$i<=$total_row;$i++){
			 $bookingid="bookingid_".$i;
			 if(trim(str_replace("'","",$$bookingid))!="")
			 {
			 	$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".str_replace("'","",$$bookingid).") and booking_no=$txt_booking_no",0);
			 }
		 }
		 //die;
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
	http.open("POST","fabric_requisition_controller.php",true);
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
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=611");// quotation_id='$data'
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
                        <input type="button" id="set_button4" class="image_uploader" style="width:160px;" value="Add More.." onClick="open_extra_terms_popup('fabric_requisition_controller.php?action=extra_terms_popup','Terms Condition')" />
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
            $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=0 and page_id=611");// quotation_id='$data'
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
show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+document.getElementById('cbo_level').value+'**'+<? echo "'$permission'";?>,'load_color_size_form','form_data_con','fabric_requisition_controller','');
}

function show_list()
{
	//echo $txt_booking_no.'fff';
	var booking_no='<? echo $txt_booking_no;?>';

	show_list_view(booking_no,'show_list_view','list_view_con','fabric_requisition_controller','');
}
function show_sub_form_with_data(booking_no,fabric_cost_id){
	document.getElementById('cbo_fabric_part').value=fabric_cost_id;
	document.getElementById('booking_no').value=booking_no;
	show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+'00'+'**'+<? echo "'$permissions'";?>,'load_color_size_form','form_data_con','fabric_requisition_controller','');
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
		var booking=return_global_ajax_value(booking_no, 'check_booking_approved', '', 'fabric_requisition_controller');
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

		http.open("POST","fabric_requisition_controller.php",true);
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

if($action=="show_fabric_booking_report_urmi"){
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
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$uom=0;
	$job_data_arr=array();
	$nameArray_buyer=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label,a.season_buyer_wise from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
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
	$job_data_arr['season_buyer_wise'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_buyer_wise')]];
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
	$season_buyer_wise=implode(",",array_unique($job_data_arr['season_buyer_wise']));
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
	} else {
		$path = "../../";
	}
	ob_start();
	?>										<!--    Header Company Information         -->
	<style type="text/css">
		 .table_valign { vertical-align: top;word-break: break-all;word-wrap: break-word; }
	</style>
	<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black"  >
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
		$po_data['pub_shipment_date_po'][$result_job[csf('id')]]=$result_job[csf('pub_shipment_date')];
		
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
	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition from wo_booking_mst a  where   a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0 ");
	foreach ($nameArray as $result)
	{
		$total_set_qnty=$result[csf('total_set_qnty')];
		$colar_excess_percent=$result[csf('colar_excess_percent')];
		$cuff_excess_percent=$result[csf('cuff_excess_percent')];
		$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
		foreach ($po_data['po_id'] as $po_id=>$po_val){
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$po_data['pub_shipment_date_po'][$po_id])-1).",";
			$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
			$WOPreparedAfter.=(datediff('d',$po_data['insert_date'][$po_id],$booking_date)-1).",";
		}
	?>
	<table width="100%" style="border:1px solid black;table-layout: fixed;" id="table_h" >
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
	<td>:&nbsp;<?  if($season_matrix!="") echo $season_matrix; else echo $season_buyer_wise; ?></td>
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
		<td  class="table_valign"><p><b>Attention</b></p></td>
		<td class="table_valign" ><p>:&nbsp;<? echo $result[csf('attention')]; ?></p></td>
		<td class="table_valign"><p><b>Lead Time </b> </p>  </td>
		<td class="table_valign"><p>:&nbsp;
			<?
			echo $leadtime;
			?> </p>
		</td>
		<td class="table_valign" ><p><b>Po Received Date</b></p></td>
		<td class="table_valign" width="150" ><p>:&nbsp;<? echo $po_received_date; ?></p></td>
	</tr>
	<tr>
		<td class="table_valign"><p><b>Order No</b></p></td>
		<td class="table_valign" width="350"  colspan="3"><p>:&nbsp;<? echo $po_number; ?></p></td>
		<td class="table_valign"><p><b>Repeat No</b></p></td>
		<td class="table_valign"><p>:&nbsp;<? echo $order_repeat_no; ?></p></td>
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
	<div id="div_size_color_matrix" style="float:left;">
	<?
	$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown);
	 
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
	//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no in($job_no_in)");
	$sql_pre=sql_select("select process_loss_method,uom from wo_pre_cost_fabric_cost_dtls where job_no in($job_no_in) and status_active=1");
	//echo "select process_loss_method,uom from wo_pre_cost_fabric_cost_dtls where job_no in($job_no_in) and status_active=1";die;
	foreach($sql_pre as $row)
	{
		$process_loss_method= $row[csf('process_loss_method')];
		$uom_arr[$row[csf('uom')]]=$unit_of_measurement[$row[csf('uom')]];
	}
	unset($sql_pre);
	$sql_lab=sql_select("select lapdip_no,job_no_mst,color_name_id from wo_po_lapdip_approval_info where job_no_mst in($job_no_in)  and approval_status=3 and status_active=1");
	//echo "select process_loss_method,uom from wo_pre_cost_fabric_cost_dtls where job_no in($job_no_in) and status_active=1";die;
	foreach($sql_lab as $row)
	{
		//$process_loss_method= $row[csf('process_loss_method')];
		$labdip_arr[$row[csf('job_no_mst')]][$row[csf('color_name_id')]]=$row[csf('lapdip_no')];
	}
	unset($sql_lab);

	//$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");
	 
	$color_wise_wo_sql=sql_select("select  a.uom,a.item_number_id as item,a.body_part_id as body_id,a.color_type_id as c_type,a.construction,a.composition,a.gsm_weight,d.dia_width,d.pre_cost_remarks,d.fabric_color_id,(d.fin_fab_qnty) as fin_fab_qnty,(d.grey_fab_qnty) as grey_fab_qnty,(d.rate) as rate,(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and a.job_no in($job_no_in) and
	d.status_active=1 and
	d.is_deleted=0
	");
	
	$fab_qty_arr=array();
	foreach($color_wise_wo_sql as $row)
	{
		//$process_loss_method= $row[csf('process_loss_method')];
	$fab_qty_arr[$row[csf('uom')]][$row[csf('item')]][$row[csf('body_id')]][$row[csf('c_type')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('pre_cost_remarks')]][$row[csf('fabric_color_id')]]['fin_qty']+=$row[csf('fin_fab_qnty')];
	$fab_qty_arr[$row[csf('uom')]][$row[csf('item')]][$row[csf('body_id')]][$row[csf('c_type')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('pre_cost_remarks')]][$row[csf('fabric_color_id')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
	$fab_qty_arr[$row[csf('uom')]][$row[csf('item')]][$row[csf('body_id')]][$row[csf('c_type')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('pre_cost_remarks')]][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];
	
	$fab_tot_qty_arr[$row[csf('uom')]][$row[csf('item')]][$row[csf('body_id')]][$row[csf('c_type')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('pre_cost_remarks')]]['fin_qty']+=$row[csf('fin_fab_qnty')];
	$fab_tot_qty_arr[$row[csf('uom')]][$row[csf('item')]][$row[csf('body_id')]][$row[csf('c_type')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('pre_cost_remarks')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
	$fab_tot_qty_arr[$row[csf('uom')]][$row[csf('item')]][$row[csf('body_id')]][$row[csf('c_type')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('pre_cost_remarks')]]['amount']+=$row[csf('amount')];
	}
	unset($sql_lab);
	
	
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
	/*if($db_type==0)
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
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;*/
	$uom=$uom_id;
	$itemid=$result_fabric_description[csf('item_number_id')];
	$body_id=$result_fabric_description[csf('body_part_id')];
	$c_type=$result_fabric_description[csf('color_type_id')];
	$construction=$result_fabric_description[csf('construction')];
	$composition=$result_fabric_description[csf('composition')];
	$gsm_weight=$result_fabric_description[csf('gsm_weight')];
	$dia_width=$result_fabric_description[csf('dia_width')];
	$pre_remarks=$result_fabric_description[csf('pre_cost_remarks')];
	$fab_color=$color_wise_wo_result[csf('fabric_color_id')];
	
	$fin_qty=$fab_qty_arr[$uom][$itemid][$body_id][$c_type][$construction][$composition][$gsm_weight][$dia_width][$pre_remarks][$fab_color]['fin_qty'];
	$amount=$fab_qty_arr[$uom][$itemid][$body_id][$c_type][$construction][$composition][$gsm_weight][$dia_width][$pre_remarks][$fab_color]['amount'];
	
	?>
	<td width='50' align='right'>
	<?
	if($fin_qty!="")
	{
	echo def_number_format($fin_qty,2) ;
	$total_fin_fab_qnty+=$fin_qty;
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	if($amount!="")
	{
	echo def_number_format($amount/$fin_qty,5);
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	$amount=def_number_format($amount,2,'',0);
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
	/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
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
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty*/
	$uom=$uom_id;
	$itemid=$result_fabric_description[csf('item_number_id')];
	$body_id=$result_fabric_description[csf('body_part_id')];
	$c_type=$result_fabric_description[csf('color_type_id')];
	$construction=$result_fabric_description[csf('construction')];
	$composition=$result_fabric_description[csf('composition')];
	$gsm_weight=$result_fabric_description[csf('gsm_weight')];
	$dia_width=$result_fabric_description[csf('dia_width')];
	$pre_remarks=$result_fabric_description[csf('pre_cost_remarks')];
	//$fab_color=$color_wise_wo_result[csf('fabric_color_id')];
	
	$fin_qty=$fab_tot_qty_arr[$uom][$itemid][$body_id][$c_type][$construction][$composition][$gsm_weight][$dia_width][$pre_remarks]['fin_qty'];
//	$amount=$fab_tot_qty_arr[$uom][$itemid][$body_id][$c_type][$construction][$composition][$gsm_weight][$dia_width][$pre_remarks]['amount'];
	
	?>
	<td width='50' align='right'><?  echo def_number_format($fin_qty,2) ;?></td>
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
	d.booking_no =$txt_booking_no and a.job_no in($job_no_in) and
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
	b.booking_no =$txt_booking_no and a.job_no in($job_no_in) and
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
	$lapdip_no=$labdip_arr[$job_no][$color_wise_wo_result[csf('fabric_color_id')]];
	//$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
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
	/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and a.job_no in($job_no_in) and
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
	");*/
	}
	if($db_type==2)
	{
	/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and a.job_no in($job_no_in) and
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
	");*/
	}
	
	//list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	$uom=$uom_id;
	$itemid=$result_fabric_description[csf('item_number_id')];
	$body_id=$result_fabric_description[csf('body_part_id')];
	$c_type=$result_fabric_description[csf('color_type_id')];
	$construction=$result_fabric_description[csf('construction')];
	$composition=$result_fabric_description[csf('composition')];
	$gsm_weight=$result_fabric_description[csf('gsm_weight')];
	$dia_width=$result_fabric_description[csf('dia_width')];
	$pre_remarks=$result_fabric_description[csf('pre_cost_remarks')];
	$fab_color=$color_wise_wo_result[csf('fabric_color_id')];
	
	$fin_qty=$fab_qty_arr[$uom][$itemid][$body_id][$c_type][$construction][$composition][$gsm_weight][$dia_width][$pre_remarks][$fab_color]['fin_qty'];
	$amount=$fab_qty_arr[$uom][$itemid][$body_id][$c_type][$construction][$composition][$gsm_weight][$dia_width][$pre_remarks][$fab_color]['amount'];
	?>
	<td width='50' align='right'>
	<?
	if($fin_qty!="")
	{
	echo def_number_format($fin_qty,2) ;
	$total_fin_fab_qnty+=$fin_qty;
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	if($amount!="")
	{
	echo def_number_format($amount/$fin_qty,5);
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	$amount=def_number_format($amount,2,'',0);
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
	/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_booking_no and a.job_no in($job_no_in) and
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
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty*/
	$uom=$uom_id;
	$itemid=$result_fabric_description[csf('item_number_id')];
	$body_id=$result_fabric_description[csf('body_part_id')];
	$c_type=$result_fabric_description[csf('color_type_id')];
	$construction=$result_fabric_description[csf('construction')];
	$composition=$result_fabric_description[csf('composition')];
	$gsm_weight=$result_fabric_description[csf('gsm_weight')];
	$dia_width=$result_fabric_description[csf('dia_width')];
	$pre_remarks=$result_fabric_description[csf('pre_cost_remarks')];
	//$fab_color=$color_wise_wo_result[csf('fabric_color_id')];
	
	$fin_qty=$fab_tot_qty_arr[$uom][$itemid][$body_id][$c_type][$construction][$composition][$gsm_weight][$dia_width][$pre_remarks]['fin_qty'];
	$amount=$fab_tot_qty_arr[$uom][$itemid][$body_id][$c_type][$construction][$composition][$gsm_weight][$dia_width][$pre_remarks]['amount'];
	
	?>
	<td width='50' align='right'><?  echo def_number_format($fin_qty,2) ;?></td>
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
	
	$sql_data=sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark, sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.adjust_qty)as adjust_qty,sum(b.fin_fab_qnty) as fin_fab_qnty,avg(b.rate) as rate,sum(b.grey_fab_qnty*b.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and a.job_no in ($job_no_in) and b.adjust_qty>0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark order by a.id,a.body_part_id");

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
		if(count($sql_data)>0)
		{
		foreach($sql_data as $row){
			//die;
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
		
		} //Empty eheck end
		?>
        </table>


	<?
	//echo $cbo_fabric_source;
	if($cbo_fabric_source==1 || $cbo_fabric_source==2){
	?>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
	<tr>
	<?
 $sql_excess_per="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
	   a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no =$txt_booking_no and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).")  and b.body_part_id in(2,3) and a.status_active=1 ";
	$resultData=sql_select($sql_excess_per);
	foreach($resultData as $row)
	{
		if($row[csf("body_part_id")]==2)
		{
		$color_ex_arr[$row[csf("gmts_color_id")]][$row[csf("size_number_id")]]=$row[csf("excess_per")];	
		}
		if($row[csf("body_part_id")]==3) //cuff
		{
		$color_cuff_ex_arr[$row[csf("gmts_color_id")]][$row[csf("size_number_id")]]=$row[csf("excess_per")];	
		}
	}
	unset($resultData);
	
		$color_wise_wo_sql_size=sql_select( "select size_number_id,color_number_id,(plan_cut_qnty) as plan_cut_qnty, (order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).")     and  status_active!=0 and is_deleted =0");
	foreach($color_wise_wo_sql_size as $row)
	{
	$color_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]['plan']+=$row[csf("plan_cut_qnty")];
	$color_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]['qty']+=$row[csf("order_quantity")];	
	}
	unset($color_wise_wo_sql_size);
	
		
	
	$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.job_no in ($job_no_in)  and a.body_part_id=2  and c.job_id=a.job_id and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");//and c.job_no_mst=a.job_no
	 
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
	//echo "shakil****"; die;
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
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.job_no in ($job_no_in)  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
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
		 /*$sql_excess_per="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
	   a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no =$txt_booking_no and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=2 and a.status_active=1 ";
	$resultData=sql_select($sql_excess_per);
	list($excess_percent)=$resultData;*/
	$colar_excess_percent=$color_ex_arr[$color_wise_wo_result[csf("color_number_id")]][$result_size[csf('size_number_id')]];//$excess_percent[csf('excess_per')];

	?>
	<td align="center" style="border:1px solid black" title="<? echo $colar_excess_percentage;?>">
	<?


	/*$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
	list($plan_cut_qnty)=$color_wise_wo_sql_qnty;*/
	$plan_cut=$color_qty_arr[$color_wise_wo_result[csf("color_number_id")]][$result_size[csf("size_number_id")]]['plan'];//$plan_cut_qnty[csf('plan_cut_qnty')];
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
	$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.job_no in ($job_no_in)   and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");

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
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.job_no in ($job_no_in)  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
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
		 /*$sql_excess_cuff="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
	 a.booking_no =$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=3 and a.status_active=1 ";
	$resultData_cuff=sql_select($sql_excess_cuff);*/
	//list($cuff_excess_percent)=$resultData_cuff;
	$cuff_excess_percent=$color_cuff_ex_arr[$color_wise_wo_result[csf("color_number_id")]][$result_size[csf("size_number_id")]];//$cuff_excess_percent[csf('excess_per')];
	?>
	<td align="center" style="border:1px solid black">
	<?
	/*$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
	list($plan_cut_qnty)=$color_wise_wo_sql_qnty;*/
	$plan_cut=$color_qty_arr[$color_wise_wo_result[csf("color_number_id")]][$result_size[csf("size_number_id")]]['plan'];//$plan_cut_qnty[csf('plan_cut_qnty')];
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

	$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.job_no in ($job_no_in)   and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
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
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.job_no in ($job_no_in) and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
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
	/*$color_tipping_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");

	list($plan_cut_qnty)=$color_tipping_wise_wo_sql_qnty;*/
	$plan_cut=$color_qty_arr[$color_wise_wo_result[csf("color_number_id")]][$result_size[csf("size_number_id")]]['plan'];//$plan_cut_qnty[csf('plan_cut_qnty')];
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
	$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.job_no in ($job_no_in)  and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
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
	$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=214 and a.job_no in ($job_no_in) and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
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
	/*$cuff_tipping_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
	list($plan_cut_qnty)=$cuff_tipping_wise_wo_sql_qnty;*/
	$plan_cut=$color_qty_arr[$color_wise_wo_result[csf("color_number_id")]][$result_size[csf("size_number_id")]]['plan'];//$plan_cut_qnty[csf('plan_cut_qnty')];
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
      <br>
        <?
    
	 $desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=7 order by b.id asc");
	?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="50%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')].'/'.$desg_name[$row[csf('designation')]];?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); //echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
        
        <?
		//$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=7 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
		$booking_id=return_field_value( "id", "wo_booking_mst","booking_no=$txt_booking_no");
		$user_nameArr=return_library_array( "select id, user_name from user_passwd ", "id", "user_name"  );
	//	$un_approve_reques_data_array=sql_select("select a.id,a.cause_id,a.approval_cause, a.user_id, a.insert_date from approval_cause_refusing_his a, fabric_booking_approval_cause b where b.id=a.cause_id and b.booking_id=$booking_id and b.entry_form=7 and a.approval_type=2 order by a.id");
		$un_approve_reques_data_array=sql_select("select b.id,b.approval_cause, b.user_id, b.insert_date from fabric_booking_approval_cause b where  b.booking_id=$booking_id and b.entry_form=7 and b.approval_type=2 order by b.id");
	 
	foreach($un_approve_reques_data_array as $hrow)
		{
			$approval_data=$hrow[csf('id')];
			$unapprove_cause_arr[$approval_data]['insert_date']=$hrow[csf('insert_date')];
			$unapprove_cause_arr[$approval_data]['user_id']=$hrow[csf('user_id')];
			$unapprove_cause_arr[$approval_data]['approval_cause']=$hrow[csf('approval_cause')];
			$causeArrChk[$hrow[csf('approval_cause')].'_'.$hrow[csf('id')]]=$hrow[csf('approval_cause')].'_'.$hrow[csf('id')];
		}
		
		/*$un_approve_reques_data_cause_array=sql_select("select b.id,b.approval_cause,b.user_id,b.insert_date,b.update_date from fabric_booking_approval_cause b where  b.booking_id=$booking_id and b.entry_form=7 and b.approval_type=2 and b.status_active=1 order by b.id");
		//echo "select b.id,b.approval_cause,b.user_id,b.insert_date,b.update_date from fabric_booking_approval_cause b where  b.booking_id=$booking_id and b.entry_form=12 and b.approval_type=2 and b.status_active=1 order by b.id";
		foreach($un_approve_reques_data_cause_array as $hrow)
		{
			if($causeArrChk[$hrow[csf('approval_cause')].'_'.$hrow[csf('id')]]=='')
			{
			$insert_date=$hrow[csf('insert_date')];
			if(strtotime($hrow[csf('update_date')])>0)
			{
				$insert_date=$hrow[csf('update_date')];	
			}
			$approval_data=$hrow[csf('id')];
			$unapprove_cause_arr[$approval_data]['insert_date']=$insert_date;
			$unapprove_cause_arr[$approval_data]['user_id']=$hrow[csf('user_id')];
			$unapprove_cause_arr[$approval_data]['approval_cause']=$hrow[csf('approval_cause')];
			}
		}*/
		
		
		?>
         <br>
		<table align="center" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
                <th width="200">Rev. Date</th>
                <th width="200">Rev. By</th>
				<th>Reason of Revision</th>
			</thead>
		 <tbody>
			<?
			$i=1;
			foreach($unapprove_cause_arr as $cause=>$hrow)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$causeArr=explode("_",$cause);
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
                  	<td width="30"><?=$i; ?></td>
					<td width="200"><?=$hrow[('insert_date')]; ?></td>
                    <td width="200"><?=$user_nameArr[$hrow[('user_id')]]; ?></td>
					<td style="word-break:break-all"><?=$hrow[('approval_cause')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
            </tbody>
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
	?>
	<?
	//echo signature_table(1, $cbo_company_name, "1330px");
	echo signature_table(121, $cbo_company_name, "1330px", 1);
	echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
	?>
	</div>
	<?

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
				http.open("POST","fabric_requisition_controller.php",true);
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

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	//echo "$('#print_12').hide();\n";
	foreach($print_report_format_arr as $id){
		if($id==269){echo "$('#print_12').show();\n";}
	}

	exit();
}


if ($action=="send_mail_report_setting_first_select"){
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id =35 and is_deleted=0 and status_active=1");
	echo $print_report_format;
	exit();
}


if($action=="print_booking_northern_new") // Aziz for northern
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
	$nameArray_per_job=sql_select( "SELECT  a.season_buyer_wise,  a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept,max(c.booking_date) as booking_date ,max(c.delivery_date) as delivery_date,max(c.is_approved) as is_approved,max(c.buyer_id) as buyer_id,max(c.pay_mode) as pay_mode,max(supplier_id) as supplier_id,max(a.dealing_marchant) as dealing_marchant,max(c.attention) as attention,max(c.currency_id) as currency_id ,max(c.remarks)  as remarks from wo_po_details_master a , wo_booking_dtls b,wo_booking_mst c  where c.booking_no=b.booking_no and c.status_active=1 and c.is_deleted=0 and  a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  group by  a.season_buyer_wise, a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept ");
	$po_job_no="";
	foreach ($nameArray_per_job as $vals)
	{
		$joball['job_no'][$vals[csf('job_no')]]=$vals[csf('job_no')];
		$joball['style_ref_no'][$vals[csf('job_no')]]=$vals[csf('style_ref_no')];
		$all_job_arr[$vals[csf("job_no")]]["job"]="'".$vals[csf("job_no")]."'";
		$all_job_arr[$vals[csf("job_no")]]["style"]=$vals[csf("style_ref_no")];
		$all_job_arr[$vals[csf("job_no")]]["item"]=$vals[csf("gmts_item_id")];
		$all_job_arr[$vals[csf("job_no")]]["season"]=$vals[csf("season_matrix")];

		$all_job_arr_season[$vals[csf("job_no")]]=$season_arr[$vals[csf("season_buyer_wise")]];

		$all_job_arr[$vals[csf("job_no")]]["dept"]=$vals[csf("product_dept")];
		$all_job_arr_dept[$vals[csf("job_no")]]=$product_dept[$vals[csf("product_dept")]];
		$all_jobs[$vals[csf("job_no")]]="'".$vals[csf("job_no")]."'";
		$job_wise_style[$vals[csf("job_no")]]= $vals[csf("style_ref_no")];
		$po_job_no=$vals[csf('job_no')];
	}
	//echo $po_job_no.'DDD';die;
	$all_jobs_id=implode(",",$all_jobs );
	$seasons=implode(",",$all_job_arr_season );
	$depts=implode(",",$all_job_arr_dept );
 	$po_qnty_all_style=sql_select("SELECT sum(po_quantity) as qnty from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in ($all_jobs_id)");

 	$all_shipdates_arr=sql_select("SELECT min(b.pub_shipment_date)  as shipdate,job_no,a.order_uom as uom from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 and b.job_no_mst in ($all_jobs_id) group by job_no,a.order_uom");
 	$shipment_dates="";
 	$job_uom_array=array();
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
 		$job_uom_array[$data[csf("job_no")]]=$unit_of_measurement[$data[csf("uom")]];


 	}
 	$unites=implode(",", $job_uom_array);


	$uom=0;
	$job_data_arr=array();
	$nameArray_buyer=sql_select( "SELECT  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix,a.total_set_qnty,a.product_dept,a.product_code,a.pro_sub_dep,a.gmts_item_id ,a.order_repeat_no,a.qlty_label  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  and a.job_no in ($all_jobs_id) ");

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
	}
	ob_start();
	?>
	<style type="text/css">

		body, p, span, td, a {font-size:10pt;font-family: Arial;}
		body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}

	</style>

	<div style="width:1330px" align="center" >

		<table id="tb_header" width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
			<thead>
				<tr>
					<td width="150" rowspan="4">
						<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' />
					</td>
					<td width="200">&nbsp;</td>
					<td width="400" align="left" style="font-size: 16px;"><b><?php echo $company_library[$cbo_company_name]; ?></b></td>
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
					<td width="400" align="left" style="font-size: 16px;"><b>Fabric Booking Sheet </b></td>
					<td width="100">&nbsp;</td>
					<td width="300" align="left"><b>Delivery Date: </b><?php echo change_date_format($nameArray_per_job[0][csf("delivery_date")]); ?></td>
				</tr>

				<tr>
					<td colspan="3" width="700">&nbsp;</td>
					<td width="300" align="left"><b>Approval Status: </b><?php if ($nameArray_per_job[0][csf("is_approved")] == 1 || $nameArray_per_job[0][csf("is_approved")] == 3) {echo "Approved";}?></td>
				</tr>
				<tr>
					<td colspan="4" height="10">&nbsp;</td>
				</tr>

				<tr>
					<td width="300"><b>To</b></td>

					<td width="400" align="left"><b>Buyer Name :   </b> <? echo $buyer_name_arr[$nameArray_per_job[0][csf("buyer_id")]]; ?></td>
					<td align="left"><b>Season : </b>&nbsp;<? echo $seasons;?></td>



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

					<td width="400" align="left"><b>Order Qnty :   </b> <? echo $po_qnty_all_style[0][csf("qnty")]; ?>&nbsp;<? echo $unites;?></td>
					<td><b>Dept : </b> &nbsp;<? echo $depts; ?></td>
					<td></td>


				</tr>
				<tr>
					<td height="2">&nbsp;</td>
				</tr>

				<tr>
					<!-- <td width="300" ><p><b>Address: </b><? echo $supplier_address_arr[$nameArray_per_job[0][csf("supplier_id")]]; ?> </p></td> -->

					<td width="400" align="left"><b>Dealing Merchandiser:   </b> <? echo $marchentrArr[$nameArray_per_job[0][csf("dealing_marchant")]]; ?></td>

					<td width="300"><b>Attention: </b>   <? echo $nameArray_per_job[0][csf("attention")];?></td>

				   <td width="400" align="left"><b>Currency:   </b> <? echo $currency[$nameArray_per_job[0][csf("currency_id")]]; ?></td>
				</tr>
				<tr>
					<td height="2">&nbsp;</td>
				</tr>

				<!-- <tr>
				   <td colspan="2" width="800"><b>Shipment Date: </b><? echo trim($shipment_dates);?></td>

				</tr> -->


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

		if($cbo_fabric_source==1 || $cbo_fabric_source==2 || $cbo_fabric_source==3)
		{
			$nameArray_qty_arr = sql_select("select b.job_no_mst,b.id,a.style_ref_no,c.item_number_id,c.color_number_id,c.order_quantity   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no_mst and a.status_active =1 and b.status_active =1 and c.status_active =1 and a.is_deleted=0 and a.job_no in($all_jobs_id) and b.id in($txt_order_no_id)  ");
		//echo "select b.job_no_mst,b.id,a.style_ref_no,c.item_number_id,c.color_number_id,c.order_quantity   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where  a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.status_active =1 and c.status_active =1 and a.is_deleted=0 and a.job_no in($all_jobs_id) and b.id in($txt_order_no_id)  ";
			foreach($nameArray_qty_arr as $row)
			{
				$gmts_qty_data_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][40]+=$row[csf('order_quantity')];
				$gmts_qty_data_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][50]+=$row[csf('order_quantity')];
				$fab_gmts_qty_data_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
				$gmts_color_data_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]]['color'].=$row[csf('color_number_id')].',';
			}

			$nameArray_fabric_description= sql_select("SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.body_part_type,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , b.dia_width,d.pre_cost_remarks,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down,min(c.color_order) as color_order,c.job_no_mst,sum(c.order_quantity) as order_quantity_pcs,avg(b.cons) as cad_consumption,avg(b.requirment) as consumption,avg(b.process_loss_percent) as wastage,sum(d.fin_fab_qnty) as fin_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
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
				c.status_active=1 and
				d.is_deleted=0 and
				b.cons>0
				group by a.id,a.item_number_id,a.body_part_id,a.body_part_type,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,d.pre_cost_remarks,a.uom ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down,c.job_no_mst  order by a.body_part_id,d.fabric_color_id ");


			$uom_data_arr=array();$b_part_array_not=array(40,50);
			foreach($nameArray_fabric_description as $row)
			{
				if(!in_array($row[csf('body_part_type')],$b_part_array_not))
				{
					$uom_data_arr[$row[csf('uom')]]=$row[csf('uom')];
				}
				if($row[csf('body_part_type')]==40 || $row[csf('body_part_type')]==50)
				{
					//$gmts_qty_data_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('fabric_color_id')]][$row[csf('body_part_type')]]+=$row[csf('order_quantity_pcs')];
					$gmts_qty_data_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('gmts_color_id')]][$row[csf('body_part_type')]]=$row[csf('order_quantity_pcs')];
				}
			}

			foreach($uom_data_arr as $uom_id=>$uom_val)
			{
				if(count($nameArray_fabric_description)>0)
				{

					?>

						<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
									<caption> <strong>Fabric Booking Summary </strong> </caption>
									<tr>
										<th  width="30" align="center">SL</th>
										<th  width="150" align="center">Style</th>
										<th  width="140" align="center">Gmts Item</th>
										<th  width="130" align="center">Body Parts</th>
										<th  width="100" align="center">Gmts Qty (Pcs)</th>
										<th  width="390" align="left">Fabric Composition</th>
										<th  width="40" align="center">GSM</th>
										<th  width="90" align="center">Fabric Dia</th>
										<th  width="100" align="center">Color Type</th>
										<th  width="100" align="center">Combo</th>
										<th  width="110" align="center">Fabric Color</th>
										<th  width="80" align="center">CAD Consumption (Kg/Dzn)</th>
										<th  width="80" align="center">Wastage %</th>
										<th  width="80" align="center">Consumption (Kg/Dzn)</th>
										<th  width="60" align="center">UOM</th>
										<!-- <th width='120' align="center"></th> -->
										<th width='120' align="center" title="Based on avg grey consumption">Finish Fab. Qty</th>
										<th width='100' align="center">Avg Rate/<? echo $unit_of_measurement[$uom_id];?></th>
										<th width='60' align="center">Amount</th>

									</tr>
									<? //wo_pre_cos_fab_co_color_dtls
									$color_wise_process_loss=sql_select("SELECT  a.body_part_id,b.color_number_id,avg(b.process_loss_percent) as loss FROM  wo_pre_cos_fab_co_avg_con_dtls  b,wo_pre_cost_fabric_cost_dtls a WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in ($all_jobs_id)  group by a.body_part_id,b.color_number_id order by a.body_part_id,b.color_number_id");
									//echo "SELECT  a.body_part_id,b.color_number_id,avg(b.process_loss_percent) as loss,g.contrast_color_id FROM  wo_pre_cos_fab_co_avg_con_dtls  b,wo_pre_cost_fabric_cost_dtls a left join wo_pre_cos_fab_co_color_dtls g on a.job_no=g.job_no and a.id=g.pre_cost_fabric_cost_dtls_id 	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in ($all_jobs_id)  group by a.body_part_id,b.color_number_id,g.contrast_color_id";

									foreach($color_wise_process_loss as $val)
									{
										$loss_arr[$val[csf("body_part_id")]][$val[csf("color_number_id")]]=$val[csf("loss")];

									}

									$total_fin_fab_qnty=$total_gmt_qty_pcs=0;  $total_amount=0;  $total_grey_fab_qnty=0 ;$tot_avg=0;
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
											a.id='".$val[csf('fabric_cost_dtls_id')]."' and
											a.uom='".$uom_id."' and

											d.fabric_color_id='".$val[csf('fabric_color_id')]."' and
											d.status_active=1 and
											d.is_deleted=0 $remark_cond
											");
										if($uom_id==$val[csf('uom')])
										{
											if($val[csf('pre_cost_remarks')]=='no remarks')  $pre_cost_remarks="";else  $pre_cost_remarks=$val[csf('pre_cost_remarks')];

											//$order_quantity_pcs=$fab_gmts_qty_data_arr[$val[csf('job_no_mst')]][$val[csf('item_number_id')]][$val[csf('fabric_color_id')]];
											//$order_quantity_pcs=$fab_gmts_qty_data_arr[$val[csf('job_no_mst')]][$val[csf('item_number_id')]][$val[csf('gmts_color_id')]];
											$order_quantity_pcs=0;
											if($val[csf('color_size_sensitive')]==1 or $val[csf('color_size_sensitive')]==2 or $val[csf('color_size_sensitive')]==4)
											{
												$gmt_colorName=$color_library[$val[csf('fabric_color_id')]];
												$order_quantity_pcs+=$fab_gmts_qty_data_arr[$val[csf('job_no_mst')]][$val[csf('item_number_id')]][$val[csf('fabric_color_id')]];
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
																$order_quantity_pcs=$fab_gmts_qty_data_arr[$val[csf('job_no_mst')]][$val[csf('item_number_id')]][$cols[0]];

																}
																else
																{
																	$gmts_color .=','.$cols[1];
																$order_quantity_pcs+=$fab_gmts_qty_data_arr[$val[csf('job_no_mst')]][$val[csf('item_number_id')]][$cols[0]];

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
																$order_quantity_pcs=$fab_gmts_qty_data_arr[$val[csf('job_no_mst')]][$val[csf('item_number_id')]][$cols[0]];

															}
															else
															{
																$gmts_color .=','.$cols[1];
																$order_quantity_pcs+=$fab_gmts_qty_data_arr[$val[csf('job_no_mst')]][$val[csf('item_number_id')]][$cols[0]];

															}

														}
													}

													$gmt_colorName=$gmts_color;
												}

											}

											?>
											<tr>
												<td align="center"> <? echo $p; $p++;?></td>


												<td align="center"> <? echo $all_job_arr[$val[csf("job_no_mst")]]["style"];?></td>
												<td align="center"> <? echo $garments_item[$val[csf("item_number_id")]];?></td>
												<td align="center"><? echo $body_part[$val[csf('body_part_id')]];?></td>
												<td align="center"><? echo number_format($order_quantity_pcs,2);?></td>
												<td align="left"> <? echo $val[csf('construction')].','.$val[csf('composition')].', '.$pre_cost_remarks; ?> </td>

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
													echo $gmt_colorName;
													?>
												</td>


												<td  align="center">
													<?
													echo $color_library[$val[csf('fabric_color_id')]];
													?>
												</td>


												<td align="center"> <? echo $val[csf('cad_consumption')]; ?></td>
												<td align="center"> <? echo $val[csf('wastage')]; ?>%</td>
												<td align="center"> <? echo def_number_format($val[csf('consumption')],2); ?></td>
												<td align="center"> <? echo $unit_of_measurement[$val[csf('uom')]]; ?></td>

												<!-- <td align="center"> <?   //$greys= $color_wise_wo_sql_qnty[0][csf("grey_fab_qnty")] /(1+($loss_arr[$val[csf("body_part_id")]][$val[csf("fabric_color_id")]]/100)) ;
													//echo def_number_format($greys,2);
													
													$val[csf('fin_fab_qnty')]='';
													$val[csf('fin_fab_qnty')]=$color_wise_wo_sql_qnty[0][csf("fin_fab_qnty")];

													?> </td> -->



													<td align="center"> <? echo def_number_format($val[csf('fin_fab_qnty')],2); ?> </td>
													<td align="center"> <? echo def_number_format($color_wise_wo_sql_qnty[0][csf("amount")]/$val[csf('fin_fab_qnty')],2); ?> </td>
													<td align="center"> <? echo def_number_format($color_wise_wo_sql_qnty[0][csf("amount")],2); ?> </td>


													<?

													$total_fin_fab_qnty +=str_replace(",", "", def_number_format($greys,2));
													$total_amount +=str_replace(",", "",def_number_format($color_wise_wo_sql_qnty[0][csf("amount")],2));

													$total_grey_fab_qnty +=str_replace(",", "",def_number_format($val[csf('fin_fab_qnty')],2));
													$total_rate +=str_replace(",", "",def_number_format($color_wise_wo_sql_qnty[0][csf("rate")],2));
													$tot_avg=$total_amount/$total_grey_fab_qnty;
													$total_gmt_qty_pcs +=$order_quantity_pcs;


													?>



												</tr>
												<?
										}

									}
										?>
									<tr style=" font-weight:bold">

									<td  align="right" colspan="4"><strong>Total</strong></td>
									 <td align="right"><? //echo def_number_format($total_gmt_qty_pcs,2);?></td>
									<td  align="right" colspan="10">&nbsp;</td>

									<td align="center"><? echo def_number_format($total_grey_fab_qnty,2);?></td>
									<td align="center"><? echo def_number_format($tot_avg,2);?></td>
									<td align="center"><? echo def_number_format($total_amount,2);?></td>

									</tr>

						</table>

					<?
				}
			}
		}


		$sql_data=sql_select("SELECT a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark, sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.adjust_qty)as adjust_qty FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in($all_jobs_id) and b.adjust_qty>0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark and a.body_part_type not in(40,50) order by a.id,a.body_part_id");
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
			<br>
				<div style="width:1330px" align="center" >
			  		<table  width="100%"  border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

			  			<tr>
			  				<td colspan="10" align="center">
			  					<strong>Collar and Cuff & Other Flat Knit Fabric Breakdown</strong>
			  				</td>

			  			</tr>
			  		</table>

	        <?
	     	  $sql_collar_cuff= "SELECT a.rate, a.amount, a.job_no , a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, a.width_dia_type, b.gmts_color_id, b.size_number_id, b.item_size, b.gmts_qty, b.excess_per, b.qty, b.po_break_down_id as po_id, b.id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=40 and b.status_active=1 and b.is_deleted=0  order by a.body_part_id,b.gmts_color_id,b.id " ;
	     	  //echo $sql_collar_cuff; die;
	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
	         $size_id_array=array();
	         $size_with_item_size_array=array();
	         foreach($sql_data_collar_cuff as $vals)
	         {
	         	$body_part_cuff_array[$vals[csf("body_part_id")]]=$vals[csf("body_part_id")];
				$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	         	$size_with_item_size_array[$vals[csf("size_number_id")]][$vals[csf("body_part_id")]]=$vals[csf("item_size")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["grey"]+=$vals[csf("qty")];

	         	//$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]=$vals[csf("excess_per")];
				if($vals[csf("excess_per")]>0)
				{
				$color_wise_excess_per_arr[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]+=$vals[csf("excess_per")];
				$color_wise_excess_per_arr[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_excess_per"]+=1;
				}
	         	$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["grey"]+=$vals[csf("qty")];
	         	/*$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];*/

	         	//$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["amount"]=$vals[csf("amount")];
				$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_rate"]=1;

	         }
			unset($sql_data_collar_cuff);
	         $sql_collar_cuff3 = sql_select("SELECT c.rate, a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, c.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls c WHERE a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and  a.id=c.pre_cost_fabric_cost_dtls_id and c.booking_no =$txt_booking_no and c.job_no in ($all_jobs_id) and a.body_part_type=40 and c.status_active=1 and c.is_deleted=0  order by a.body_part_id");

	         foreach($sql_collar_cuff3 as $vals)
	         {

	         	$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];

	         }


	         $counts=count($size_id_array);


	         $size_wise_qnty_array=array();
	         ?>


			 <?
	          if(count($body_part_cuff_array)>0)
	          {		$grand_size_wise_qnty_array=array();
			  		$gr_collor_qty=0;
					 $gr_col_avg_price=0;
					 $gr_col_avg_row=0;
					 $gr_amount=0;
					 $gr_excess=0;
					foreach($body_part_cuff_array as $bpart_id=>$val)
					{
					// $sql_collar_cuff2= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id,a.color_size_sensitive,a.color_break_down, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.body_part_id=$bpart_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=40  and b.status_active=1 and b.is_deleted=0  group by  a.rate,a.amount, a.job_no , a.id ,a.color_size_sensitive,a.color_break_down, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition,a.gsm_weight,a.width_dia_type,b.gmts_color_id  order by a.body_part_id,b.gmts_color_id  " ;

					$sql_collar_cuff2= " SELECT a.rate,
									         a.amount,
									         a.job_no,
									         a.id as fabric_cost_dtls_id,
									         a.color_size_sensitive,
									         a.item_number_id,
									         a.body_part_id,
									         a.color_type_id,
									         a.construction,
									         a.composition,
									         a.gsm_weight,
									         a.width_dia_type,
									         b.gmts_color_id
									    from wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b,wo_booking_dtls c ,wo_pre_cos_fab_co_avg_con_dtls d
									    where     a.job_no = b.job_no
									         and a.id = b.pre_cost_fabric_cost_dtls_id
									         and b.booking_no=c.booking_no
									         and b.po_break_down_id=c.po_break_down_id
									         and b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id
									         and b.gmts_color_id=c.gmts_color_id
									        and  a.id=d.pre_cost_fabric_cost_dtls_id
									        and d.gmts_sizes=b.size_number_id
									        and c.status_active = 1
									        and c.is_deleted = 0
								         and a.body_part_id=$bpart_id 
								         and b.booking_no =$txt_booking_no 
								         and b.job_no in ($all_jobs_id) 
								         and a.body_part_type=40  
								         and b.status_active=1 
								         and b.is_deleted=0 
								    group by  a.rate,a.amount, a.job_no , a.id ,a.color_size_sensitive,a.color_break_down, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition,a.gsm_weight,a.width_dia_type,b.gmts_color_id  order by a.body_part_id,b.gmts_color_id  " ;
	          $sql_data_collar_cuff2=sql_select($sql_collar_cuff2);
					?>
					 <table align="left"  width="100%" style="margin-top: 5px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
			  			<tr>
			  				<td colspan="<? echo $counts+13;?>" align="center">
			  					<strong>Collar Details(<? echo $body_part[$bpart_id];?>)</strong>
			  				</td>

			  			</tr>
			  			<tr>
			  				<td  width="30"  rowspan="2" align="center"><strong>SL</strong> </td>
			  				<td  width="150" rowspan="2" align="center"><strong>Style</strong></td>
 			  				<td  width="140"  rowspan="2" align="center"><strong>Gmts Item</strong></td>
 			  				<td  width="100"  rowspan="2" align="center"><strong>Body Part</strong></td>
							<td  width="100" rowspan="2" align="center"><strong>Actual Qty(Pcs)</strong></td>
			  				<td  width="390" rowspan="2" align="left">&nbsp;&nbsp;<strong>Collar Composition</strong></td>
			  				<td  width="100"  rowspan="2" align="center"><strong>Color Type</strong></td>
 			  				<td  width="100"  rowspan="2"  align="center"><strong>Combo</strong></td>
							<td  width="100"  rowspan="2"  align="center"><strong>Fabric Color</strong></td>
 			  				<?
 			  				foreach($size_id_array as $id=>$val)
 			  				{
 			  					?>
 			  					<td width="40" align="center"><strong><? echo $size_library[$id];?></strong></td>

 			  					<?
 			  				}
 			  				?>
			  				<td width="60"  rowspan="2" align="center"><strong> Req Qty (Pcs)</strong> </td>
			  				<td  width="50" rowspan="2"  align="center"><strong> Excess %  </strong> </td>
			  				<td width="50" rowspan="2"  align="center"><strong>Price/Pcs</strong></td>
			  				<td  width="40" rowspan="2" align="center"><strong>Amount</strong></td>


			  			</tr>
			  			<tr>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					//$size_with_item=rtrim($size_with_item_size_array[$id],',');
								//$size_with_item=implode(",",array_unique(explode(",",$size_with_item)));
								?>
			  					<td  align="center"><? echo $size_with_item_size_array[$id][$bpart_id];?></td>

			  					<?
			  				}
			  				?>

			  			</tr>
			  			<?
			  			$p=1;
			  			foreach($sql_data_collar_cuff2 as $row)
			  			{

							?>
			  				<tr>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="30" align="center"><? echo $p;?></td>
			  					<td style="word-break: break-all;word-wrap: break-word;" width="150" align="center"><? echo $job_wise_style[$row[csf("job_no")]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="140" align="center"><?  $item_id= $row[csf('item_number_id')];echo $garments_item[$item_id]; ?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? echo $body_part[$row[csf("body_part_id")]];?></td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? $gmt_actual_qty=$gmts_qty_data_arr[$row[csf('job_no')]][$item_id][$row[csf('gmts_color_id')]][40];echo number_format($gmt_actual_qty,0);$total_gmt_actual_qty+=$gmt_actual_qty;?></td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="390" align="left"> &nbsp;
			  						<? echo $row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]] ;?>
			  					</td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $color_type[$row[csf('color_type_id')]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
			  						<? echo $color_library[$row[csf('gmts_color_id')]];  ?>
			  					</td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
			  						<?
										if($row[csf('color_size_sensitive')]==1 or $row[csf('color_size_sensitive')]==2 or $row[csf('color_size_sensitive')]==4)
										{
											$gmt_fab_color=$color_library[$row[csf('gmts_color_id')]];
										}
										else
										{
											$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row[csf('fabric_cost_dtls_id')]."  and b.gmts_color_id=".$row[csf('gmts_color_id')]." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
											if($contrast_color_id>0)
											{
												$gmt_fab_color=$color_library[$contrast_color_id];
											}
										}
									echo $gmt_fab_color;  ?>
			  					</td>


			  					<?
								$color_qty=0; $greyQty=0;
			  					foreach($size_id_array as $id=>$val)
			  					{
			  						?>
			  						<td  style="word-break: break-all;word-wrap: break-word;" width="40"  align="center"><? echo $cuff_grey_qty=$size_item_wise_qnty[$row[csf("body_part_id")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]][$id][$size_with_item_size_array[$id][$bpart_id]]["grey"];//.'K '.$row[csf("fabric_cost_dtls_id")].'='.$row[csf("gmts_color_id")].'='.$id.'='.$size_with_item_size_array[$id];
									?></td>

			  						<?
			  						$size_wise_qnty_array[$id]+=$val;
									$grand_size_wise_qnty_array[$id]+=$cuff_grey_qty;$sub_size_wise_qnty_array[$bpart_id][$id]+=$cuff_grey_qty;
									$color_qty+=$cuff_grey_qty;

			  					}
								$row_excess_per= $color_wise_excess_per_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["row_excess_per"];
			  					?>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
			  						<? echo $grey_qnty=$color_qty;//$color_wise_qnty[$row[csf('gmts_color_id')]][$item_id]["grey"];  ?>
			  					</td>

			  					<td  style="word-break: break-all;word-wrap: break-word;" title="<? echo $row_excess_per;?>" width="50" align="center">
			  						<?  $excess= $color_wise_excess_per_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["excess_per"]/$row_excess_per;
									echo number_format($excess,2);
									  ?>
			  					</td>


			  					<td style="word-break: break-all;word-wrap: break-word;" title="<? echo $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];?>"  width="50" align="center">
			  						<? echo  $cuff_price=$color_wise_qnty2[$row[csf("fabric_cost_dtls_id")]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["rate"];
										//$tot_row_rate=$color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];  ?>
			  					</td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="40" align="center">
			  						<? echo $amount=$grey_qnty*$cuff_price;   ?>
			  					</td>
			  				</tr>
			  				<?

							$sub_collor_qty_arr[$row[csf("body_part_id")]]+=$grey_qnty;
							$sub_collor_amount_arr[$row[csf("body_part_id")]]+=$amount;

							$sub_excess_arr[$row[csf("body_part_id")]]+=$excess;
							$sub_price_arr[$row[csf("body_part_id")]]+=$cuff_price;
							$cuff_body_wise_avg_row[$row[csf("body_part_id")]]+=1;

							$gr_fin_qty+=$fin_qty;
			  				$gr_collor_qty+=$grey_qnty;
							$gr_amount+=$amount;
			  				$gr_excess+=$excess ;
			  				$gr_col_avg_price+=$cuff_price;
			  				$gr_col_avg_row++;



			  				$p++;
			  			}
			  			?>
						<tr class="tbl_bottom">
							<td colspan="9" align="right">Total </td>
							<?
							foreach($size_id_array as $id=>$val)
							{
								?>
								<td  align="center"><? echo $sub_size_wise_qnty_array[$bpart_id][$id];?></td>

								<?
							}
							$cuff_sub_excess=$sub_excess_arr[$bpart_id];
							$cuff_sub_price=$sub_price_arr[$bpart_id];
							$cuff_body_wise_avg_tot_row=$cuff_body_wise_avg_row[$bpart_id];
							?>
							<td align="center">
								<? echo $sub_collor_qty_arr[$bpart_id];  ?>
							</td>
							<td align="center" title="Sub Exess & sub Row=<? echo $cuff_sub_excess.'='.$cuff_body_wise_avg_tot_row ?>">
									<? echo number_format($cuff_sub_excess/$cuff_body_wise_avg_tot_row,2); ?></td>
							<td align="center"><? echo number_format($cuff_sub_price/$cuff_body_wise_avg_tot_row,2); ?></td>

							<td align="center">
								<? echo $sub_collor_amount_arr[$bpart_id];   ?>
							</td>
			  			</tr>
						<!--Sub TOT End-->


			  			<?
						} //Body Part End
						?>

						<tr>
			  				<td colspan="9" align="right">Grand Total </td>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $grand_size_wise_qnty_array[$id];?></td>

			  					<?
			  				}
			  				?>
			  				<td align="center">
			  					<? echo $gr_collor_qty;  ?>
			  				</td>
			  				<td align="center" title="Sub Exess & Tot Row=<? echo $gr_excess.'='.$gr_avg_row ?>"><? echo number_format($gr_excess/$gr_col_avg_row,2); ?></td>
							<td align="center"  title="Tot Price  & Tot Row=<? echo $gr_col_avg_price.'='.$gr_col_avg_row.'**'.$p?>"><? //echo number_format($gr_col_avg_price/$gr_col_avg_row,2); ?></td>

			  				<td align="center">
			  					<? echo $gr_amount;   ?>
			  				</td>
			  			</tr>

						</table>
						<?
			  		}
			  	?>


					<br/><br/>

						<?
	     	  $sql_collar_cuff= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id,b.size_number_id,b.item_size,b.gmts_qty,b.excess_per,b.qty,b.po_break_down_id as po_id,b.id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=50 and b.status_active=1 and b.is_deleted=0  order by a.body_part_id,b.gmts_color_id,b.id " ;
	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
	         $size_id_array=array();
	         $size_with_item_size_array=array();
	         $size_item_wise_qnty=array();
	         $color_wise_qnty=array();
	         foreach($sql_data_collar_cuff as $vals)
	         {
	         	$body_part_array[$vals[csf("body_part_id")]]=$vals[csf("body_part_id")];
				$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	         	$size_with_item_size_array[$vals[csf("size_number_id")]][$vals[csf("body_part_id")]]=$vals[csf("item_size")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["grey"]+=$vals[csf("qty")];

	         	//$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]=$vals[csf("excess_per")];
				if($vals[csf("excess_per")]>0)
				{
				$color_wise_excess_per_cuff_arr[$vals[csf('fabric_cost_dtls_id')]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]+=$vals[csf("excess_per")];
				$color_wise_excess_per_cuff_arr[$vals[csf('fabric_cost_dtls_id')]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_excess_per"]+=1;
				}
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["grey"]+=$vals[csf("qty")];
	         	/*$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];*/
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["amount"]=$vals[csf("amount")];
				$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_rate"]=1;

	         }
				unset($sql_data_collar_cuff);
	         $sql_collar_cuff3 = sql_select("SELECT c.rate, a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, c.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls c WHERE a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and  a.id=c.pre_cost_fabric_cost_dtls_id and c.booking_no =$txt_booking_no and c.job_no in ($all_jobs_id) and a.body_part_type=50 and c.status_active=1 and c.is_deleted=0  order by a.body_part_id");

	         foreach($sql_collar_cuff3 as $vals)
	         {

	         	$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];

	         }


	         $counts=count($size_id_array);$grand_size_wise_qnty_array=array();
	          if(count($body_part_array)>0)
	          {
					$gr_collor_qty=$gr_amount=$gr_avg_price=$gr_avg_row=$gr_excess=0;
					foreach($body_part_array as $bpart_id=>$val)
					{
					// $sql_collar_cuff2= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id,a.color_size_sensitive, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and a.body_part_id=$bpart_id and b.job_no in ($all_jobs_id) and a.body_part_type=50  and b.status_active=1 and b.is_deleted=0  group by  a.rate,a.amount, a.job_no , a.id , a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition,a.gsm_weight,a.width_dia_type,b.gmts_color_id,a.color_size_sensitive  order by a.body_part_id,b.gmts_color_id  " ;

					$sql_collar_cuff2= "SELECT a.rate,
								         a.amount,
								         a.job_no,
								         a.id as fabric_cost_dtls_id,
								         a.color_size_sensitive,
								         a.item_number_id,
								         a.body_part_id,
								         a.color_type_id,
								         a.construction,
								         a.composition,
								         a.gsm_weight,
								         a.width_dia_type,
								         b.gmts_color_id
								    from wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b,wo_booking_dtls c ,wo_pre_cos_fab_co_avg_con_dtls d
								    where     a.job_no = b.job_no
								         and a.id = b.pre_cost_fabric_cost_dtls_id
								         and b.booking_no=c.booking_no
								         and b.po_break_down_id=c.po_break_down_id
								         and b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id
								         and b.gmts_color_id=c.gmts_color_id
								        and  a.id=d.pre_cost_fabric_cost_dtls_id
								        and d.gmts_sizes=b.size_number_id
								        and c.status_active = 1
								        and c.is_deleted = 0
								         and b.booking_no =$txt_booking_no 
								         and a.body_part_id=$bpart_id 
								         and b.job_no in ($all_jobs_id) 
								         and a.body_part_type=50  
								         and b.status_active=1 
								         and b.is_deleted=0  
								   group by  
								         a.rate,a.amount, a.job_no ,
								          a.id , a.item_number_id, 
								          a.body_part_id,a.color_type_id,
								           a.construction, a.composition,a.gsm_weight,a.width_dia_type,b.gmts_color_id,a.color_size_sensitive  order by a.body_part_id,b.gmts_color_id  " ;
	          $sql_data_collar_cuff2=sql_select($sql_collar_cuff2);
					?>

			  	 <table align="left"  width="100%" style="margin-top: 5px; margin-bottom:10px;"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
			  			<tr>
			  				<td colspan="<? echo $counts+13;?>" align="center">
			  					<strong>Cuff Details &nbsp;(<? echo  $body_part[$bpart_id];?>) </strong>
			  				</td>
			  			</tr>
			  			<tr>
			  				<td  width="30"  rowspan="2" align="center"><strong>SL</strong> </td>
			  				<td  width="150" rowspan="2" align="center"><strong>Style</strong></td>
 			  				<td  width="140" rowspan="2" align="center"><strong>Gmts Item</strong></td>
 			  				<td  width="100" rowspan="2" align="center"><strong>Body Part</strong></td>
							<td  width="100" rowspan="2" align="center"><strong>Actual Qty(Pcs)</strong></td>
			  				<td  width="390" rowspan="2" align="left">&nbsp;&nbsp;<strong>Cuff Composition</strong></td>
			  				<td  width="100" rowspan="2" align="center"><strong>Color Type</strong></td>
 			  				<td  width="100" rowspan="2"  align="center"><strong>Combo</strong></td>
							<td  width="100" rowspan="2"  align="center"><strong>Fabric Color</strong></td>
 			  				<?
 			  				foreach($size_id_array as $id=>$val)
 			  				{
 			  					?>
 			  					<td width="40" align="center"><strong><? echo $size_library[$id];?></strong></td>

 			  					<?
 			  				}
 			  				?>

			  				<td width="60"  rowspan="2" align="center"><strong> Req Qty(Pcs) </strong> </td>
			  				<td  width="50" rowspan="2"  align="center"><strong> Excess %  </strong> </td>

			  				<td width="50" rowspan="2"  align="center"><strong>Price/Pcs</strong></td>
			  				<td  width="40" rowspan="2" align="center"><strong>Amount</strong></td>
			  			</tr>
			  			<tr>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td width="40"  align="center"><strong><? echo $size_with_item_size_array[$id][$bpart_id];?></strong></td>
			  					<?
			  				}
			  				?>
			  			</tr>
			  			<?
			  			$p=1;
			  			$size_wise_qnty_array=array();
			  			foreach($sql_data_collar_cuff2 as $row)
			  			{
							?>
			  				<tr>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="30" align="center"><? echo $p;?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="150" align="center"><? echo $job_wise_style[$row[csf("job_no")]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="140" align="center"><?  $item_id= $row[csf('item_number_id')];echo $garments_item[$item_id]; ?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $body_part[$row[csf("body_part_id")]];?></td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? $gmt_actual_qty=$gmts_qty_data_arr[$row[csf('job_no')]][$item_id][$row[csf('gmts_color_id')]][50];echo number_format($gmt_actual_qty,0);$total_gmt_actual_qty+=$gmt_actual_qty;?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="390" align="left"> &nbsp;
			  						<? echo $row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]] ;?>
			  					</td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $color_type[$row[csf('color_type_id')]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center">
			  						<? echo $color_library[$row[csf('gmts_color_id')]];  ?>
			  					</td>
								<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center">
			  						<?
									if($row[csf('color_size_sensitive')]==1 or $row[csf('color_size_sensitive')]==2 or $row[csf('color_size_sensitive')]==4)
										{
											$gmt_fab_color=$color_library[$row[csf('gmts_color_id')]];
										}
										else
										{
											$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row[csf('fabric_cost_dtls_id')]."  and b.gmts_color_id=".$row[csf('gmts_color_id')]." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
											if($contrast_color_id>0)
											{
												$gmt_fab_color=$color_library[$contrast_color_id];
											}
										}
									echo $gmt_fab_color;   ?>
			  					</td>

			  					<?
								$tot_size_qty=0;
			  					foreach($size_id_array as $id=>$val)
			  					{
			  						?>
			  						<td  style="word-break: break-all;word-wrap: break-word;"  width="40"  align="center"><? echo $size_grey_qty=$size_item_wise_qnty[$row[csf("body_part_id")]][$row[csf('fabric_cost_dtls_id')]][$row[csf("gmts_color_id")]][$id][$size_with_item_size_array[$id][$row[csf("body_part_id")]]]["grey"];?></td>

			  						<?
			  						$size_wise_qnty_array[$id]+=$size_grey_qty;$sub_size_wise_qnty_array[$row[csf("body_part_id")]][$id]+=$size_grey_qty;
									$grand_size_wise_qnty_array[$id]+=$size_grey_qty;
									$tot_size_qty+=$size_grey_qty;

			  					}
			  					?>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="60" align="center">
			  						<? echo $grey_qnty=$tot_size_qty;//$color_wise_qnty[$row[csf('gmts_color_id')]][$item_id]["grey"];
									$row_excess_per=$color_wise_excess_per_cuff_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["row_excess_per"];
									 ?>
			  					</td>

			  					<td  style="word-break: break-all;word-wrap: break-word;" title="excess tot=<? echo $row_excess_per; ?>"  width="50" align="center">
			  						<?  $avg_excess=$color_wise_excess_per_cuff_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["excess_per"]/$row_excess_per;echo number_format($avg_excess,2);   ?>
			  					</td>
			  					<td   style="word-break: break-all;word-wrap: break-word;" width="50" title="<? echo $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];?>" align="center">
			  						<? echo $price= $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["rate"];
									$tot_row_rate= $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"]; ?>

			  					</td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="40" align="center">
			  						<? echo  $amount=$grey_qnty* $price;   ?>
			  					</td>

			  				</tr>
			  				<?
			  				$sub_collor_qty_arr[$row[csf("body_part_id")]]+=$grey_qnty;
							$sub_collor_amount_arr[$row[csf("body_part_id")]]+=$amount;
							$sub_excess_arr[$row[csf("body_part_id")]]+=$avg_excess;
							$sub_price_arr[$row[csf("body_part_id")]]+=$price;
							$body_wise_avg_row[$row[csf("body_part_id")]]+=1;

							$gr_fin_qty+=$fin_qty;
			  				$gr_collor_qty+=$grey_qnty;
			  				$gr_avg_price+=$price;
							$gr_amount+=$amount;
			  				$gr_excess+=$avg_excess;

			  				$gr_avg_row++;

			  				$p++;
			  			}
			  			?>

			  			<tr>
			  				<td colspan="9" align="right">Total </td>
			  				<?
							//$sub_size_wise_qnty_array=array();
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $sub_size_wise_qnty_array[$bpart_id][$id];
								//$sub_size_wise_qnty_array[$row[csf("body_part_id")]][$id]=0;
								/*$grand_size_wise_qnty_array[$id]+=$sub_size_wise_qnty_array[$bpart_id][$id];*/
								?></td>
			  					<?
			  				}
							$tot_avg_row=$body_wise_avg_row[$bpart_id];
							$tot_sub_excess=$sub_excess_arr[$bpart_id];
							$tot_sub_price=$sub_price_arr[$bpart_id];
			  				?>
			  				<td align="center">
			  					<? echo $sub_collor_qty_arr[$bpart_id];  ?>
			  				</td>
			  				<td align="center" title="Tot Excess & Tot Row=<? echo $tot_sub_excess.'='.$tot_avg_row;?>"><? echo number_format($tot_sub_excess/$tot_avg_row,2); ?></td>
			  				<td align="center">
			  					<? echo number_format($tot_sub_price/$tot_avg_row,2);  ?>
			  				</td>
			  				<td align="center">
			  					<? echo $sub_collor_amount_arr[$bpart_id];   ?>
			  				</td>
			  			</tr>
			  			<?
						} //Body Part End

			  		?>
					<tr>
			  				<td colspan="9" align="right">Grand Total </td>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $grand_size_wise_qnty_array[$id];?></td>
			  					<?
			  				}
			  				?>
			  				<td align="center">
			  					<? echo $gr_collor_qty;  ?>
			  				</td>
			  				<td align="center" title="grand Excess &tot row=<? echo $gr_excess.'='.$gr_avg_row;?>"><? echo number_format($gr_excess/$gr_avg_row,2); ?></td>
			  				<td align="center">
			  					<? //echo number_format($gr_avg_price/$gr_avg_row,2);  ?>
			  				</td>
			  				<td align="center">
			  					<? echo $gr_amount;   ?>
			  				</td>
			  			</tr>
			  	</table>
				<?
					}
				?>

				</div>
				<br>
				<?
		$color_name_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
		$sql_stripe=("select c.id,c.job_no,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.uom,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,c.uom as type_uom from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and d.job_no in($all_jobs_id) and b.booking_no=$txt_booking_no  and c.color_type_id in (2,6) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and b.is_deleted=0  group by c.id,c.job_no,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.uom,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,c.composition,c.construction,b.dia_width,c.uom order by c.job_no,c.id,d.id ");
		$result_data=sql_select($sql_stripe);
		foreach($result_data as $row)
		{
		$style_ref_no=$job_data_arr['style_ref_no'][$row[csf('job_no')]];
		//echo $style_ref_no.'XXXX';
			//$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['style_ref_no'][$row[csf('did')]]=$style_ref_no;
			if($row[csf('type_uom')]==12){
				$type_uom_arr[$row[csf('type_uom')]]='kg';
			}elseif($row[csf('type_uom')]==1){
				$type_uom_arr[$row[csf('type_uom')]]='pcs';
			}
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]]['type_uom']=$row[csf('type_uom')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]]['stripe_color']=$row[csf('stripe_color')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]]['measurement']=$row[csf('measurement')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
			//$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]]['yarn_dyed']=$row[csf('yarn_dyed')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]]['fabric_description']=$row[csf('fabric_description')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]]['uom']=$row[csf('uom')];
		
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]]['style_ref_no']=$style_ref_no;

			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabric_description']=$row[csf('fabric_description')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['style_ref_no']=$style_ref_no;
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['type_uom']=$row[csf('type_uom')];
		}
	
	// print_r($type_uom_arr);

		foreach($type_uom_arr as $uom_id=>$uom_arr){
		?>
			

			<table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
       		 <caption> <strong style="float:left"> Stripe Details (<?=$uom_arr;?>)</strong></caption>
      

            <tr>
                <th width="30"> SL</th>
                <th width="100">Style</th>
				<th width="100">Body Part</th>
				<th width="100">Fab. Desc.</th>
                <th width="80">Gmts Color</th>
                <th width="70">Fabric Qty(<?=$uom_arr;?>)</th>
                <th width="70">Stripe Color</th>
                <th width="70">Stripe Measurement</th>
				<th  width="70">Stripe Uom</th>
                <th  width="70">Qty.(<?=$uom_arr;?>)</th>

				<th  width="70">Y/D Req.</th>
            </tr>
            <?
				$job_strip_color_rowspan=array();$fab_strip_color_rowspan=array();$color_strip_color_rowspan=array();
			  foreach($stripe_arr as $job_id=>$job_data)
			  {	 $job_row_span=0;
					 foreach($job_data as $body_id=>$body_data)
					 {
						   $fab_row_span=0;
						   foreach($body_data as $color_id=>$color_data)
						   {
								$color_row_span=0;
								foreach($color_data as $strip_color_id=>$color_val)
								{
									$job_row_span++;$fab_row_span++;$color_row_span++;

								}
								$job_strip_color_rowspan[$job_id]=$job_row_span;
								$fab_strip_color_rowspan[$job_id][$body_id]=$fab_row_span;
								$color_strip_color_rowspan[$job_id][$body_id][$color_id]=$color_row_span;
								$color_strip_color_qty_arr[$job_id][$body_id][$color_id]=$stripe_arr2[$job_id][$body_id][$color_id]['fabreqtotkg'];


						   }
					 }
			  }
			 // print_r($job_strip_color_rowspan);


			$i=1;$total_fab_qty=0;$total_fabreqtotkg=$total_color_qty=0;$fab_data_array=array();
            foreach($stripe_arr as $job_id=>$job_data)	{
			 $job_span=1;
			 foreach($job_data as $body_id=>$body_data)    {
			  $fab_span=1;
			   foreach($body_data as $color_id=>$color_data)  {
			     $color_span=1;
				foreach($color_data as $strip_color_id=>$color_val)   {
					//$rowspan=count($color_val['stripe_color']);

					if($uom_id==$color_val['type_uom'])
					{
					?>
					<tr>
					<?

					if($job_span==1)
					{
					?>
                        <td align="center" rowspan="<? echo $job_strip_color_rowspan[$job_id];?>"> <? echo $i; ?></td>
                        <td align="center" title="<? echo $job_id;?>" rowspan="<? echo $job_strip_color_rowspan[$job_id];?>"> <? echo $color_val['style_ref_no']; ?></td>
						<?
					}
					if($fab_span==1)
					{
						?>
						<td align="center" rowspan="<? echo $fab_strip_color_rowspan[$job_id][$body_id];?>"> <? echo $body_part[$body_id]; ?></td>
						<td align="center" rowspan="<? echo $fab_strip_color_rowspan[$job_id][$body_id];?>"> <? echo $color_val['fabric_description']; ?></td>
						<?
					}
					if($color_span==1)
					{
					$color_qty= $color_strip_color_qty_arr[$job_id][$body_id][$color_id];//$color_val['fabreqtotkg'];
					$total_color_qty+=$color_qty;
					?>
                        <td rowspan="<? echo $color_strip_color_rowspan[$job_id][$body_id][$color_id];?>" align="center"> <? echo $color_name_arr[$color_id]; ?></td>
                        <td rowspan="<? echo $color_strip_color_rowspan[$job_id][$body_id][$color_id];?>" align="center"> <? echo number_format($color_qty,2); ?></td>
					<?
					}

						?>
						<td align="center"><?  echo  $color_name_arr[$strip_color_id]; ?></td>
						<td align="center"> <? echo  number_format($color_val['measurement'],2); ?></td>
						<td align="center"> <? echo  $unit_of_measurement[$color_val['uom']]; ?></td>
						<td align="center" title="Stripe Measurement/Tot Stripe Measurement*Fabric Qty(KG)"> <? echo  number_format($color_val['fabreqtotkg'],2); ?></td>

						<td align="center"> <? echo  $yes_no[$color_val['yarn_dyed']]; ?></td>
					</tr>
						<?
					
						$total_fabreqtotkg+=$color_val['fabreqtotkg'];

					$job_span++;$fab_span++;$color_span++;
					}
				  }
				}
				$i++;
			}
			}
			?>
        <tfoot>
        <tr>
            <td align="right" colspan="5">Total </td>
            <td align="center"><? echo  number_format($total_color_qty,2); ?> </td>
            <td></td>
            <td></td>

            <td> </td>
			 <td align="center"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
			<td> </td>
        </tr>
        </tfoot>
   </table>
   <?}?>
   <br/>
			<table  style="margin-top: 5px;float: left;"    width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

				<tr>
					<td colspan="10" align="center">
						<strong>Comments</strong>
					</td>
				</tr>
				<tr>
					<td align="center"> SL </td>
					<td align="center" width="200"  style="word-wrap: break-word;word-break: break-all;"> PO NO </td>
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

				$booking_data=sql_select("SELECT a.id, sum(b.grey_fab_qnty) as booking_qty from wo_po_break_down a,wo_booking_dtls b  where a.job_no_mst =b.job_no and  a.id=b.po_break_down_id   and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.is_short !=1 and b.booking_type=10 group by  a.id  order by a.id");
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

						<td align="center"  style="word-wrap: break-word;word-break: break-all;"> <? echo $po_num_arr[$val[csf("po_number")]] ;?> </td>
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
			<td colspan="6" align="left"><img  src='<? echo $path.$imge_arr[$po_job_no]; ?>' height='155' width='200' /></td>
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
	<br/><br/><br/><br/>
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

if($action=="print_booking_northern_9") // Aziz for Yunusco
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


	$po_sql=sql_select("select id,po_number,grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in(select po_break_down_id from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no=$txt_booking_no)");
	$int_ref_no="";
	foreach ($po_sql as $row)
	{
		$po_num_arr[$row[csf("id")]]=$row[csf("po_number")];
		$int_ref_no.=$row[csf("grouping")].',';
	}

	$uom=0;
	$joball=array();
	$nameArray_per_job=sql_select( "SELECT  a.season_buyer_wise,  a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept,max(c.booking_date) as booking_date ,max(c.delivery_date) as delivery_date,max(c.is_approved) as is_approved,max(c.buyer_id) as buyer_id,max(c.pay_mode) as pay_mode,max(c.source) as source,max(supplier_id) as supplier_id,max(a.dealing_marchant) as dealing_marchant,max(c.attention) as attention,max(c.currency_id) as currency_id,max(c.fabric_source) as fabric_source ,max(c.remarks)  as remarks from wo_po_details_master a , wo_booking_dtls b,wo_booking_mst c  where c.booking_no=b.booking_no and c.status_active=1 and c.is_deleted=0 and  a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  group by  a.season_buyer_wise, a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept ");
	foreach ($nameArray_per_job as $vals)
	{
		$joball['job_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('job_no')];
		$joball['style_ref_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('style_ref_no')];
		$all_job_arr[$vals[csf("job_no")]]["job"]="'".$vals[csf("job_no")]."'";
		$all_job_arr[$vals[csf("job_no")]]["style"]=$vals[csf("style_ref_no")];
		$all_job_arr[$vals[csf("job_no")]]["item"]=$vals[csf("gmts_item_id")];
		$all_jobs[$vals[csf("job_no")]]="'".$vals[csf("job_no")]."'";
		$all_jobs_no_arr[$vals[csf("job_no")]]=$vals[csf("job_no")];
		$job_wise_style[$vals[csf("job_no")]]= $vals[csf("style_ref_no")];
	}
	$all_jobs_id=implode(",",$all_jobs );
	$all_jobs_nos=implode(",",$all_jobs_no_arr );

 //	echo $all_jobs_id.'SSSA';
	$int_ref_no=rtrim($int_ref_no,',');
	if($int_ref_no!='')
	{
		$job_int_ref=implode(",", array_unique(explode(",",$int_ref_no)));
	}




	$path=str_replace("'","",$path);
	if($path=="")
	{
	$path='../../';
	}
	ob_start();
	?>
	<style type="text/css">

		body, p, span, td, a {font-size:10pt;font-family: Arial;}
		body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}

	</style>

	<div style="width:1330px" align="center" >

		<table id="tb_header" width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
			<thead>
				<tr>
					<td width="110" rowspan="3">
						<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' />
					</td>

					<td width="500" colspan="2" align="left" style="font-size: 16px;"><b><?php echo $company_library[$cbo_company_name]; ?></b></td>
					<td width="200">&nbsp;</td>
					<td width="300" align="left"><b>Booking No: </b><?php echo str_replace("'", "", $txt_booking_no); ?></td>
				</tr>

				<tr>

					<td  width="500" colspan="2" align="left">
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
					<td width="200">&nbsp;</td>
					<td width="300" align="left"><b>Booking Date: </b><?php echo change_date_format($nameArray_per_job[0][csf("booking_date")]); ?></td>
				</tr>

				<tr>

					<td  width="500" colspan="2" align="left" style="font-size: 16px;"><b>Multi-Job Fabric Booking-<? echo $fabric_source[$nameArray_per_job[0][csf("fabric_source")]];?></b></td>
					<td width="200">&nbsp;</td>
					<td width="300" align="left"><b>Delivery Date: </b><?php echo change_date_format($nameArray_per_job[0][csf("delivery_date")]); ?></td>
				</tr>

				<tr>
					<td colspan="4" width="700">&nbsp;</td>
					<td width="300" align="left"><b>Approval Status: </b><?php if ($nameArray_per_job[0][csf("is_approved")] == 1 || $nameArray_per_job[0][csf("is_approved")] == 3) {echo "Approved";}?></td>
				</tr>
				<tr>
					<td colspan="4" height="11">&nbsp;</td>
				</tr>
				<tr>
					<td width="300"><b>To</b></td>
				</tr>

				<tr>
					<td width="300"><?
					$pay_mode_id=$nameArray_per_job[0][csf("pay_mode")];//pay_mode
					if($pay_mode_id!=3 && $pay_mode_id!=5)
					{
						echo $supplier_name_arr[$nameArray_per_job[0][csf("supplier_id")]];
					}
					else
					{
						echo $company_library[$nameArray_per_job[0][csf("supplier_id")]];
					}?></td>
					<td colspan="3" width="700">&nbsp;</td>
					<td width="300" align="left"><b>Dealing Merchandiser:   </b> <? echo $marchentrArr[$nameArray_per_job[0][csf("dealing_marchant")]]; ?></td>

				</tr>

				<tr>
					<td width="300" ><p><b>Address: </b><? echo $supplier_address_arr[$nameArray_per_job[0][csf("supplier_id")]]; ?> </p></td>
					<td colspan="3" width="700">&nbsp;</td>
					<td width="300" align="left"><b>Buyer Name :   </b> <? echo $buyer_name_arr[$nameArray_per_job[0][csf("buyer_id")]]; ?></td>
				</tr>



				<tr>
				   <td width="300"><b>Attention: </b>   <? echo $nameArray_per_job[0][csf("attention")];?></td>
				   <td colspan="3" width="700">&nbsp;</td>
				   <td width="300" align="left"><b>Inter. Ref. No:   </b> <? echo $job_int_ref; ?></td>
				</tr>

				<tr>
				   <td width="300" align="left"><b>Pay Mode:   </b> <? echo $pay_mode[$nameArray_per_job[0][csf("pay_mode")]]; ?></td>
				    <td colspan="3" width="700">&nbsp;</td>
				   <td width="300"><b>Job No : </b>   <? echo $all_jobs_nos;?></td>

				</tr>
				<tr>
				   <td colspan="2" width="800"><b>Currency : </b><? echo $currency[$nameArray_per_job[0][csf("currency_id")]];?></td>
				</tr>

				<tr>
				   <td width="300"><b>Source :  <? echo $source[$nameArray_per_job[0][csf("source")]]; ?> </b>    </td>
				</tr>
				<tr>
				   <td width="400" align="left"><b>Remarks:   </b> <? echo $nameArray_per_job[0][csf("remarks")]; ?></td>
				</tr>
			</thead>
		</table>


			<?php

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

?>
			<br/>
			<!--  Here will be the main portion  -->
			<?
			$costing_per="";
			$costing_per_qnty=0;
			$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($all_jobs_id)");
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
			//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no in($all_jobs_id)");
			//wo_labtest_dtls

			//$labdip_no_arr=return_library_array( "select job_no,labtest_no from wo_labtest_mst a,wo_labtest_dtls b  where a.id=b.mst_id  and b.job_no in($all_jobs_id) and  b.status_active=1 and b.is_deleted=0",'job_no','labtest_no');
			$lab_dip_no_arr=array();
			$lab_dip_no_sql=sql_select("select lapdip_no, color_name_id from wo_po_lapdip_approval_info where job_no_mst in($all_jobs_id) and status_active=1 and is_deleted=0 and approval_status=3");
			foreach($lab_dip_no_sql as $row)
			{
				$lab_dip_no_arr[$row[csf('color_name_id')]]=$row[csf('lapdip_no')];
			}
			unset($lab_dip_no_sql);
			//echo "select job_no,labtest_no from wo_labtest_mst a,wo_labtest_dtls b  where a.id=b.mst_id  and b.job_no in($all_jobs_id) and  b.status_active=1 and b.is_deleted=0";
		$data_img=sql_select("select image_location,master_tble_id  from common_photo_library  where   form_name='fabric_color_img' and is_deleted=0 and file_type=1");
	   $system_img_arr=array();
	  foreach($data_img as $row)
	   {
		  $system_img_arr[$row[csf('master_tble_id')]]['img']=$row[csf('image_location')];
	   }

			$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");
			$p=1;

		if($cbo_fabric_source==1 || $cbo_fabric_source==2 || $cbo_fabric_source==3)
		{
			$nameArray_fabric_description= sql_select("SELECT a.id as fabric_cost_dtls_id,d.dia_width as dia,a.construction,a.composition,a.fab_nature_id as fab_nat_id,a.gsm_weight,a.fabric_description as fab_desc, a.item_number_id as item_id, a.body_part_id,a.color_type_id as color_type,a.width_dia_type as width_dia,d.po_break_down_id as po_id,d.remark as fab_remark,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down,d.gmts_color_id as color_id,d.job_no
			  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
				WHERE a.job_no=d.job_no and
				a.id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no =$txt_booking_no and
				d.job_no in ($all_jobs_id) and
				d.status_active=1 and
				d.is_deleted=0 and
				d.fin_fab_qnty>0   and a.body_part_type not in(40,50)
				group by a.id,a.item_number_id,a.gsm_weight,a.fab_nature_id,a.fabric_description ,a.body_part_id,a.color_type_id,d.gmts_color_id,a.width_dia_type,d.po_break_down_id,d.remark,a.uom ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down,d.job_no,d.dia_width,a.construction,a.composition  order by a.body_part_id,d.fabric_color_id ");

			foreach($nameArray_fabric_description as $row)
			{
				$item_desc= $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("fab_desc")].','.$row[csf('dia')];
				$uom_data_arr[$row[csf('uom')]]=$row[csf('uom')];
				if($row[csf("color_type_id")]==2 || $row[csf("color_type_id")]==6)
				{
					$strip_color_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]=$row[csf('fin_fab_qntys')];
				}
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['dia']=$row[csf('dia')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['color_type']=$row[csf('color_type')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['width_dia']=$row[csf('width_dia')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['uom']=$row[csf('uom')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['item_id']=$row[csf('item_id')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['dia_width']=$row[csf('dia_width')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['construction']=$row[csf('construction')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['composition']=$row[csf('composition')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['fab_color_id']=$row[csf('fabric_color_id')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['body_part_id']=$row[csf('body_part_id')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['fab_remark']=$row[csf('fab_remark')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['amount']=$row[csf('amounts')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['fin_fab_qnty']=$row[csf('fin_fab_qntys')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['color_size_sensitive']=$row[csf('color_size_sensitive')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['color_break_down']=$row[csf('color_break_down')];
				$fabric_data_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['fabric_cost_dtls_id']=$row[csf('fabric_cost_dtls_id')];

			}
					?>
						<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
									<caption> <strong>Fabric Booking Detail </strong> </caption>
									<tr>
										<th  width="30" align="center">SL</th>
										<th  width="100" align="center">Style Ref</th>
										<th  width="100" align="center">Order No</th>
										<th  width="70" align="center">Fabric Nature</th>
										<th  width="300" align="center">Fabric Description</th>
										<th  width="35" align="center">GSM</th>
										<th  width="120" align="center">FabricDia/ Width</th>
										<th  width="70" align="center">Color Type</th>
										<th  width="60" align="center">Gmts Color</th>
										<th  width="65" align="center">Fabric Color</th>
										<th  width="80" align="center">Lab Dip No</th>
										<th  width="45" align="center">UOM</th>
										<th  width="70" align="center">Finish Fab. Qty</th>
										<th  width="240" align="center">Image</th>
										<th width="80" align="center" title="">Avg Rate</th>
										<th width="80" align="center">Amount</th>
										<th width="100" align="center">Remark</th>

									</tr>
									<? //wo_pre_cos_fab_co_color_dtls
										$total_fin_fab_qnty=$total_amount=0;
										$k=1;
									foreach($fabric_data_arr as $style_id=>$style_data)
									{
									  foreach($style_data as $po_id=>$po_data)
									  {
									  	 foreach($po_data as $nat_id=>$nat_data)
									     {
										 	foreach($nat_data as $desc_id=>$desc_data)
									        {
												foreach($desc_data as $color_id=>$val)
									        	{
											if($val[('fab_remark')]=='no remarks')  $pre_cost_remarks="";else  $pre_cost_remarks=$val[('fab_remark')];
											$diaWidth=$val[('dia')];
											if($diaWidth!='') $diaWidth=$diaWidth.",";else $diaWidth="";
											?>
											<tr>
												<td align="center"> <? echo $k;?></td>
												<td align="center" title="<? echo $style_id;?>"> <? echo $all_job_arr[$style_id]["style"];?></td>
												<td align="center"> <? echo $po_num_arr[$po_id];?></td>
												<td align="center"><? echo $item_category[$nat_id];?></td>
												<td align="center"><? echo $desc_id;?></td>
												<td align="center"> <? echo $val[('gsm_weight')]; ?> </td>

												<td  align="center">
													<?
													echo $diaWidth.$fabric_typee[$val[('width_dia')]];
													?>
												</td>
												<td  align="center">
													<?
													echo $color_type[$val[('color_type')]];
													?>
												</td>
												<td  align="center">
													<?
													if($val[('color_size_sensitive')]==1 or $val[('color_size_sensitive')]==2 or $val[('color_size_sensitive')]==4)
													{
														echo $color_library[$val[('fab_color_id')]];
													}

													else
													{
														$color_break_down=$val[('color_break_down')];
														if($color_break_down)
														{
															$gmts_color="";
															if (strpos($color_break_down, '__') !== false)
															{
																$color_break_down=explode('__', $color_break_down);
																foreach ($color_break_down as $key => $value)
																{
																	$cols=explode('_', $value);
																	if(trim(strtolower($color_library[$val[('fab_color_id')]]))==trim(strtolower($cols[2])))
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
																if(trim(strtolower($color_library[$val[('fab_color_id')]]))==trim(strtolower($cols[2])))
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

													echo $color_library[$val[('fab_color_id')]];
													?>
												</td>
												<td align="center"><? echo $lab_dip_no_arr[$val[('fab_color_id')]];//$labdip_no_arr[$style_id]; ?></td>
												<td align="center"> <? echo $unit_of_measurement[$val[('uom')]]; ?></td>
												<td align="center"> <? echo def_number_format($val[("fin_fab_qnty")],2); ?></td>
													<td align="center"> <?
													$img_ref_id=$val[('fabric_cost_dtls_id')].'_'.$val[('fab_color_id')];
													$fab_color_img=$system_img_arr[$img_ref_id]['img'];

													//echo def_number_format($val[("fin_fab_qntys")],2);
													if($fab_color_img!='')
													{
													?>
													<img src='../../<? echo $fab_color_img; ?>' height='50' width='70' align="middle" />
													<?
													}
													else
													{
													?>
														<img src='../../<? echo $fab_color_img; ?>' height='50' width='70' align="middle" />
													<?
													}
													?>
													</td>
													<td align="center"> <? echo def_number_format($val[("amount")]/$val[("fin_fab_qnty")],2); ?> </td>
													<td align="center"> <? echo def_number_format($val[("amount")],2); ?> </td>
													<td align="center"> <? echo $pre_cost_remarks; ?> </td>
													<?
													$total_fin_fab_qnty +=$val[("fin_fab_qnty")];
													$total_amount +=$val[("amount")];

												?>

												</tr>
												<?
												$k++;
															} // Color End

														} // Desc End
													} // Nature End
												} // Po End

										} // Style End

										?>
									<tr style="font-weight:bold">
									    <td  align="right" colspan="12"><strong>Total&nbsp;</strong></td>
									    <td align="center"><? echo def_number_format($total_fin_fab_qnty,2);?></td>
									    <td align="center"></td>
									    <td align="center"></td>
									    <td align="center"><? echo def_number_format($total_amount,2);?></td>
									    <td align="center"></td>
									</tr>
									<!-- <tr style=" font-weight:bold">
									<td  align="right" colspan="5"><strong>Total</strong></td>
									<td align="right"><? //echo def_number_format($total_gmt_qty_pcs,2);?></td>
									<td  align="right" colspan="8">&nbsp;</td>

								<td align="center"><? //echo def_number_format($total_fin_fab_qnty,2);?></td>
									<td align="center"><? //echo def_number_format($total_fin_fab_qnty,2);?></td>
									<td align="center"><? //echo def_number_format($total_fin_fab_qnty,2);?></td>

								<td align="center"><? //echo def_number_format($total_amount,2);?></td>
									<td align="center"><? //echo def_number_format($total_amount,2);?></td>
									</tr> -->
						</table>
					<br/>

		<table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
       		 <caption> <strong style="float:left"> Stripe Details</strong></caption>
        <?
		$color_name_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
		$sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.uom,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and d.job_no in($all_jobs_id) and b.booking_no=$txt_booking_no  and c.color_type_id in (2,6) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.uom,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,c.composition,c.construction,b.dia_width order by c.id,d.id ");
		//echo $sql_stripe;


		$result_data=sql_select($sql_stripe);
		foreach($result_data as $row){
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
			//$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
		}
		?>
            <tr>
                <th width="30"> SL</th>
                <th width="100"> Body Part</th>
                <th width="80"> Fabric Color</th>
                <th width="70"> Fin Fabric Qty </th>
                <th width="70"> Stripe Color</th>
                <th width="70"> Stripe Measurement</th>
                <th  width="70"> Qty.(KG)</th>
                <th  width="70"> Uom</th>
            </tr>
            <?
			$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
            foreach($stripe_arr as $body_id=>$body_data){
				foreach($body_data as $color_id=>$color_val){
					$rowspan=count($color_val['stripe_color']);
					$composition=$stripe_arr2[$body_id][$color_id]['composition'];
					$construction=$stripe_arr2[$body_id][$color_id]['construction'];
					$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
					$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
					$uom_id=$stripe_arr2[$body_id][$color_id]['uom'];
					$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];
					/*if($db_type==0) $color_id_cond="IFNULL(d.fabric_color_id,0)=IFNULL('".$color_id."',0) and";
					else if ( $db_type==2) $color_id_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0) and";
					$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
						WHERE a.job_no=b.job_no and
						a.id=b.pre_cost_fabric_cost_dtls_id and
						c.job_no_mst=a.job_no and
						c.id=b.color_size_table_id and
						b.po_break_down_id=d.po_break_down_id and
						 b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
						d.booking_no =$txt_booking_no and
						a.body_part_id='".$body_id."' and
						a.color_type_id='".$color_type_id."' and
						a.construction='".$construction."' and
						a.composition='".$composition."' and
						a.gsm_weight='".$gsm_weight."' and
						$color_id_cond
						d.status_active=1 and
						d.is_deleted=0
						");

						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;*/

						//$color_qty=$strip_color_arr[$body_id][$color_id];
						$color_qty=$stripe_arr2[$body_id][$color_id]['fabreqtotkg'];
						$total_color_qty+=$color_qty;
					?>
					<tr>
					<?
					//$color_qty=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
					?>
                        <td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
                        <td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
                        <td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
                        <td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?></td>
					<?
					//$total_fab_qty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
					{
						$measurement=$color_val['measurement'][$strip_color_id];
						$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
						$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
						?>
						<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
						<td align="right"> <? echo  number_format($measurement,2); ?></td>
						<td align="right"> <? echo  number_format($fabreqtotkg,2); ?></td>
						<td> <? echo  $unit_of_measurement[$uom_id]; ?></td>
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
            <td colspan="3">Total </td>
            <td align="right"><? echo  number_format($total_color_qty,2); ?> </td>
            <td></td>
            <td></td>
            <td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
            <td> </td>
        </tr>
        </tfoot>
   </table>

					<?
			//End

		}
					?>
			</table>
			<br>
				<div style="width:1330px" align="center" >
			  		<table  width="100%"  border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

			  			<tr>
			  				<td colspan="10" align="center">
			  					<strong>Collar and Cuff & Other Flat Knit Fabric Breakdown</strong>
			  				</td>

			  			</tr>
			  		</table>

	        <?
	     	  $sql_collar_cuff= "SELECT a.rate, a.amount, a.job_no , a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, a.width_dia_type, b.gmts_color_id, b.size_number_id, b.item_size, b.gmts_qty, b.excess_per, b.qty, b.po_break_down_id as po_id, b.id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=40 and b.status_active=1 and b.is_deleted=0  order by a.body_part_id,b.gmts_color_id,b.id " ;
	     	  //echo $sql_collar_cuff; die;
	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
	         $size_id_array=array();
	         $size_with_item_size_array=array();
	         foreach($sql_data_collar_cuff as $vals)
	         {
	         	$body_part_cuff_array[$vals[csf("body_part_id")]]=$vals[csf("body_part_id")];
				$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	         	$size_with_item_size_array[$vals[csf("size_number_id")]][$vals[csf("body_part_id")]]=$vals[csf("item_size")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["grey"]+=$vals[csf("qty")];

	         	//$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]=$vals[csf("excess_per")];
				if($vals[csf("excess_per")]>0)
				{
				$color_wise_excess_per_arr[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]+=$vals[csf("excess_per")];
				$color_wise_excess_per_arr[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_excess_per"]+=1;
				}
	         	$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["grey"]+=$vals[csf("qty")];
	         	/*$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];*/

	         	//$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["amount"]=$vals[csf("amount")];
				$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_rate"]=1;

	         }
			unset($sql_data_collar_cuff);
	         $sql_collar_cuff3 = sql_select("SELECT c.rate, a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, c.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls c WHERE a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and  a.id=c.pre_cost_fabric_cost_dtls_id and c.booking_no =$txt_booking_no and c.job_no in ($all_jobs_id) and a.body_part_type=40 and c.status_active=1 and c.is_deleted=0  order by a.body_part_id");

	         foreach($sql_collar_cuff3 as $vals)
	         {

	         	$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];

	         }


	         $counts=count($size_id_array);


	         $size_wise_qnty_array=array();
	         ?>


			 <?
	          if(count($body_part_cuff_array)>0)
	          {		$grand_size_wise_qnty_array=array();
			  		$gr_collor_qty=0;
					 $gr_col_avg_price=0;
					 $gr_col_avg_row=0;
					 $gr_amount=0;
					 $gr_excess=0;
					foreach($body_part_cuff_array as $bpart_id=>$val)
					{
					 $sql_collar_cuff2= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id,a.color_size_sensitive,a.color_break_down, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.body_part_id=$bpart_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=40  and b.status_active=1 and b.is_deleted=0  group by  a.rate,a.amount, a.job_no , a.id ,a.color_size_sensitive,a.color_break_down, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition,a.gsm_weight,a.width_dia_type,b.gmts_color_id  order by a.body_part_id,b.gmts_color_id  " ;
	          $sql_data_collar_cuff2=sql_select($sql_collar_cuff2);
					?>
					 <table align="left"  width="100%" style="margin-top: 5px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
			  			<tr>
			  				<td colspan="<? echo $counts+13;?>" align="center">
			  					<strong>Collar Details(<? echo $body_part[$bpart_id];?>)</strong>
			  				</td>

			  			</tr>
			  			<tr>
			  				<td  width="30"  rowspan="2" align="center"><strong>SL</strong> </td>
			  				<td  width="150" rowspan="2" align="center"><strong>Style</strong></td>
 			  				<td  width="140"  rowspan="2" align="center"><strong>Gmts Item</strong></td>
 			  				<td  width="100"  rowspan="2" align="center"><strong>Body Part</strong></td>
							<td  width="100" rowspan="2" align="center"><strong>Actual Qty(Pcs)</strong></td>
			  				<td  width="390" rowspan="2" align="left">&nbsp;&nbsp;<strong>Collar Composition</strong></td>
			  				<td  width="100"  rowspan="2" align="center"><strong>Color Type</strong></td>
 			  				<td  width="100"  rowspan="2"  align="center"><strong>Combo</strong></td>
							<td  width="100"  rowspan="2"  align="center"><strong>Fabric Color</strong></td>
 			  				<?
 			  				foreach($size_id_array as $id=>$val)
 			  				{
 			  					?>
 			  					<td width="40" align="center"><strong><? echo $size_library[$id];?></strong></td>

 			  					<?
 			  				}
 			  				?>
			  				<td width="60"  rowspan="2" align="center"><strong> Req Qty (Pcs)</strong> </td>
			  				<td  width="50" rowspan="2"  align="center"><strong> Excess %  </strong> </td>
			  				<td width="50" rowspan="2"  align="center"><strong>Price/Pcs</strong></td>
			  				<td  width="40" rowspan="2" align="center"><strong>Amount</strong></td>


			  			</tr>
			  			<tr>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					//$size_with_item=rtrim($size_with_item_size_array[$id],',');
								//$size_with_item=implode(",",array_unique(explode(",",$size_with_item)));
								?>
			  					<td  align="center"><? echo $size_with_item_size_array[$id][$bpart_id];?></td>

			  					<?
			  				}
			  				?>

			  			</tr>
			  			<?
			  			$p=1;
			  			foreach($sql_data_collar_cuff2 as $row)
			  			{

							?>
			  				<tr>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="30" align="center"><? echo $p;?></td>
			  					<td style="word-break: break-all;word-wrap: break-word;" width="150" align="center"><? echo $job_wise_style[$row[csf("job_no")]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="140" align="center"><?  $item_id= $row[csf('item_number_id')];echo $garments_item[$item_id]; ?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? echo $body_part[$row[csf("body_part_id")]];?></td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? $gmt_actual_qty=$gmts_qty_data_arr[$row[csf('job_no')]][$item_id][$row[csf('gmts_color_id')]];echo number_format($gmt_actual_qty,0);$total_gmt_actual_qty+=$gmt_actual_qty;?></td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="390" align="left"> &nbsp;
			  						<? echo $row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]] ;?>
			  					</td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $color_type[$row[csf('color_type_id')]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
			  						<? echo $color_library[$row[csf('gmts_color_id')]];  ?>
			  					</td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
			  						<?
										if($row[csf('color_size_sensitive')]==1 or $row[csf('color_size_sensitive')]==2 or $row[csf('color_size_sensitive')]==4)
										{
											$gmt_fab_color=$color_library[$row[csf('gmts_color_id')]];
										}
										else
										{
											$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row[csf('fabric_cost_dtls_id')]."  and b.gmts_color_id=".$row[csf('gmts_color_id')]." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
											if($contrast_color_id>0)
											{
												$gmt_fab_color=$color_library[$contrast_color_id];
											}
										}
									echo $gmt_fab_color;  ?>
			  					</td>


			  					<?
								$color_qty=0; $greyQty=0;
			  					foreach($size_id_array as $id=>$val)
			  					{
			  						?>
			  						<td  style="word-break: break-all;word-wrap: break-word;" width="40"  align="center"><? echo $cuff_grey_qty=$size_item_wise_qnty[$row[csf("body_part_id")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]][$id][$size_with_item_size_array[$id][$bpart_id]]["grey"];//.'K '.$row[csf("fabric_cost_dtls_id")].'='.$row[csf("gmts_color_id")].'='.$id.'='.$size_with_item_size_array[$id];
									?></td>

			  						<?
			  						$size_wise_qnty_array[$id]+=$val;
									$grand_size_wise_qnty_array[$id]+=$cuff_grey_qty;$sub_size_wise_qnty_array[$bpart_id][$id]+=$cuff_grey_qty;
									$color_qty+=$cuff_grey_qty;

			  					}
								$row_excess_per= $color_wise_excess_per_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["row_excess_per"];
			  					?>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
			  						<? echo $grey_qnty=$color_qty;//$color_wise_qnty[$row[csf('gmts_color_id')]][$item_id]["grey"];  ?>
			  					</td>

			  					<td  style="word-break: break-all;word-wrap: break-word;" title="<? echo $row_excess_per;?>" width="50" align="center">
			  						<?  $excess= $color_wise_excess_per_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["excess_per"]/$row_excess_per;
									echo number_format($excess,2);
									  ?>
			  					</td>


			  					<td style="word-break: break-all;word-wrap: break-word;" title="<? echo $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];?>"  width="50" align="center">
			  						<? echo  $cuff_price=$color_wise_qnty2[$row[csf("fabric_cost_dtls_id")]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["rate"];
										//$tot_row_rate=$color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];  ?>
			  					</td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="40" align="center">
			  						<? $amount=$grey_qnty*$cuff_price; echo number_format( $amount,4); ?>
			  					</td>
			  				</tr>
			  				<?

							$sub_collor_qty_arr[$row[csf("body_part_id")]]+=$grey_qnty;
							$sub_collor_amount_arr[$row[csf("body_part_id")]]+=$amount;

							$sub_excess_arr[$row[csf("body_part_id")]]+=$excess;
							$sub_price_arr[$row[csf("body_part_id")]]+=$cuff_price;
							$cuff_body_wise_avg_row[$row[csf("body_part_id")]]+=1;

							$gr_fin_qty+=$fin_qty;
			  				$gr_collor_qty+=$grey_qnty;
							$gr_amount+=$amount;
			  				$gr_excess+=$excess ;
			  				$gr_col_avg_price+=$cuff_price;
			  				$gr_col_avg_row++;



			  				$p++;
			  			}
			  			?>
						<tr class="tbl_bottom">
							<td colspan="9" align="right">Total </td>
							<?
							foreach($size_id_array as $id=>$val)
							{
								?>
								<td  align="center"><? echo $sub_size_wise_qnty_array[$bpart_id][$id];?></td>

								<?
							}
							$cuff_sub_excess=$sub_excess_arr[$bpart_id];
							$cuff_sub_price=$sub_price_arr[$bpart_id];
							$cuff_body_wise_avg_tot_row=$cuff_body_wise_avg_row[$bpart_id];
							?>
							<td align="center">
								<? echo $sub_collor_qty_arr[$bpart_id];  ?>
							</td>
							<td align="center" title="Sub Exess & sub Row=<? echo $cuff_sub_excess.'='.$cuff_body_wise_avg_tot_row ?>">
									<? echo number_format($cuff_sub_excess/$cuff_body_wise_avg_tot_row,2); ?></td>
							<td align="center"><? echo number_format($cuff_sub_price/$cuff_body_wise_avg_tot_row,2); ?></td>

							<td align="center">
								<? echo number_format($sub_collor_amount_arr[$bpart_id],4);   ?>
							</td>
			  			</tr>
						<!--Sub TOT End-->


			  			<?
						} //Body Part End
						?>

						<tr>
			  				<td colspan="9" align="right">Grand Total </td>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $grand_size_wise_qnty_array[$id];?></td>

			  					<?
			  				}
			  				?>
			  				<td align="center">
			  					<? echo $gr_collor_qty;  ?>
			  				</td>
			  				<td align="center" title="Sub Exess & Tot Row=<? echo $gr_excess.'='.$gr_avg_row ?>"><? echo number_format($gr_excess/$gr_col_avg_row,2); ?></td>
							<td align="center"  title="Tot Price  & Tot Row=<? echo $gr_col_avg_price.'='.$gr_col_avg_row.'**'.$p?>"><? //echo number_format($gr_col_avg_price/$gr_col_avg_row,2); ?></td>

			  				<td align="center">
			  					<? echo number_format($gr_amount,4);   ?>
			  				</td>
			  			</tr>

						</table>
						<?
			  		}
			  	?>


					<br/><br/>

						<?
	     	  $sql_collar_cuff= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id,b.size_number_id,b.item_size,b.gmts_qty,b.excess_per,b.qty,b.po_break_down_id as po_id,b.id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=50 and b.status_active=1 and b.is_deleted=0  order by a.body_part_id,b.gmts_color_id,b.id " ;
	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
	         $size_id_array=array();
	         $size_with_item_size_array=array();
	         $size_item_wise_qnty=array();
	         $color_wise_qnty=array();
	         foreach($sql_data_collar_cuff as $vals)
	         {
	         	$body_part_array[$vals[csf("body_part_id")]]=$vals[csf("body_part_id")];
				$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	         	$size_with_item_size_array[$vals[csf("size_number_id")]][$vals[csf("body_part_id")]]=$vals[csf("item_size")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["grey"]+=$vals[csf("qty")];

	         	//$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]=$vals[csf("excess_per")];
				if($vals[csf("excess_per")]>0)
				{
				$color_wise_excess_per_cuff_arr[$vals[csf('fabric_cost_dtls_id')]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]+=$vals[csf("excess_per")];
				$color_wise_excess_per_cuff_arr[$vals[csf('fabric_cost_dtls_id')]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_excess_per"]+=1;
				}
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["grey"]+=$vals[csf("qty")];
	         	/*$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];*/
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["amount"]=$vals[csf("amount")];
				$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_rate"]=1;

	         }
				unset($sql_data_collar_cuff);
	         $sql_collar_cuff3 = sql_select("SELECT c.rate, a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, c.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls c WHERE a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and  a.id=c.pre_cost_fabric_cost_dtls_id and c.booking_no =$txt_booking_no and c.job_no in ($all_jobs_id) and a.body_part_type=50 and c.status_active=1 and c.is_deleted=0  order by a.body_part_id");

	         foreach($sql_collar_cuff3 as $vals)
	         {

	         	$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];

	         }


	         $counts=count($size_id_array);$grand_size_wise_qnty_array=array();
	          if(count($body_part_array)>0)
	          {
					$gr_collor_qty=$gr_amount=$gr_avg_price=$gr_avg_row=$gr_excess=0;
					foreach($body_part_array as $bpart_id=>$val)
					{
					 $sql_collar_cuff2= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id,a.color_size_sensitive, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and a.body_part_id=$bpart_id and b.job_no in ($all_jobs_id) and a.body_part_type=50  and b.status_active=1 and b.is_deleted=0  group by  a.rate,a.amount, a.job_no , a.id , a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition,a.gsm_weight,a.width_dia_type,b.gmts_color_id,a.color_size_sensitive  order by a.body_part_id,b.gmts_color_id  " ;
	          $sql_data_collar_cuff2=sql_select($sql_collar_cuff2);
					?>

			  	 <table align="left"  width="100%" style="margin-top: 5px; margin-bottom:10px;"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
			  			<tr>
			  				<td colspan="<? echo $counts+13;?>" align="center">
			  					<strong>Cuff Details &nbsp;(<? echo  $body_part[$bpart_id];?>) </strong>
			  				</td>
			  			</tr>
			  			<tr>
			  				<td  width="30"  rowspan="2" align="center"><strong>SL</strong> </td>
			  				<td  width="150" rowspan="2" align="center"><strong>Style</strong></td>
 			  				<td  width="140" rowspan="2" align="center"><strong>Gmts Item</strong></td>
 			  				<td  width="100" rowspan="2" align="center"><strong>Body Part</strong></td>
							<td  width="100" rowspan="2" align="center"><strong>Actual Qty(Pcs)</strong></td>
			  				<td  width="390" rowspan="2" align="left">&nbsp;&nbsp;<strong>Cuff Composition</strong></td>
			  				<td  width="100" rowspan="2" align="center"><strong>Color Type</strong></td>
 			  				<td  width="100" rowspan="2"  align="center"><strong>Combo</strong></td>
							<td  width="100" rowspan="2"  align="center"><strong>Fabric Color</strong></td>
 			  				<?
 			  				foreach($size_id_array as $id=>$val)
 			  				{
 			  					?>
 			  					<td width="40" align="center"><strong><? echo $size_library[$id];?></strong></td>

 			  					<?
 			  				}
 			  				?>

			  				<td width="60"  rowspan="2" align="center"><strong> Req Qty(Pcs) </strong> </td>
			  				<td  width="50" rowspan="2"  align="center"><strong> Excess %  </strong> </td>

			  				<td width="50" rowspan="2"  align="center"><strong>Price/Pcs</strong></td>
			  				<td  width="40" rowspan="2" align="center"><strong>Amount</strong></td>
			  			</tr>
			  			<tr>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td width="40"  align="center"><strong><? echo $size_with_item_size_array[$id][$bpart_id];?></strong></td>
			  					<?
			  				}
			  				?>
			  			</tr>
			  			<?
			  			$p=1;
			  			$size_wise_qnty_array=array();
			  			foreach($sql_data_collar_cuff2 as $row)
			  			{
							?>
			  				<tr>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="30" align="center"><? echo $p;?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="150" align="center"><? echo $job_wise_style[$row[csf("job_no")]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="140" align="center"><?  $item_id= $row[csf('item_number_id')];echo $garments_item[$item_id]; ?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $body_part[$row[csf("body_part_id")]];?></td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? $gmt_actual_qty=$gmts_qty_data_arr[$row[csf('job_no')]][$item_id][$row[csf('gmts_color_id')]];echo number_format($gmt_actual_qty,0);$total_gmt_actual_qty+=$gmt_actual_qty;?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="390" align="left"> &nbsp;
			  						<? echo $row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]] ;?>
			  					</td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $color_type[$row[csf('color_type_id')]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center">
			  						<? echo $color_library[$row[csf('gmts_color_id')]];  ?>
			  					</td>
								<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center">
			  						<?
									if($row[csf('color_size_sensitive')]==1 or $row[csf('color_size_sensitive')]==2 or $row[csf('color_size_sensitive')]==4)
										{
											$gmt_fab_color=$color_library[$row[csf('gmts_color_id')]];
										}
										else
										{
											$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row[csf('fabric_cost_dtls_id')]."  and b.gmts_color_id=".$row[csf('gmts_color_id')]." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
											if($contrast_color_id>0)
											{
												$gmt_fab_color=$color_library[$contrast_color_id];
											}
										}
									echo $gmt_fab_color;   ?>
			  					</td>

			  					<?
								$tot_size_qty=0;
			  					foreach($size_id_array as $id=>$val)
			  					{
			  						?>
			  						<td  style="word-break: break-all;word-wrap: break-word;"  width="40"  align="center"><? echo $size_grey_qty=$size_item_wise_qnty[$row[csf("body_part_id")]][$row[csf('fabric_cost_dtls_id')]][$row[csf("gmts_color_id")]][$id][$size_with_item_size_array[$id][$row[csf("body_part_id")]]]["grey"];?></td>

			  						<?
			  						$size_wise_qnty_array[$id]+=$size_grey_qty;$sub_size_wise_qnty_array[$row[csf("body_part_id")]][$id]+=$size_grey_qty;
									$grand_size_wise_qnty_array[$id]+=$size_grey_qty;
									$tot_size_qty+=$size_grey_qty;

			  					}
			  					?>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="60" align="center">
			  						<? echo $grey_qnty=$tot_size_qty;//$color_wise_qnty[$row[csf('gmts_color_id')]][$item_id]["grey"];
									$row_excess_per=$color_wise_excess_per_cuff_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["row_excess_per"];
									 ?>
			  					</td>

			  					<td  style="word-break: break-all;word-wrap: break-word;" title="excess tot=<? echo $row_excess_per; ?>"  width="50" align="center">
			  						<?  $avg_excess=$color_wise_excess_per_cuff_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["excess_per"]/$row_excess_per;echo number_format($avg_excess,2);   ?>
			  					</td>
			  					<td   style="word-break: break-all;word-wrap: break-word;" width="50" title="<? echo $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];?>" align="center">
			  						<? echo $price= $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["rate"];
									$tot_row_rate= $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"]; ?>
			  					</td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="40" align="center">
			  						<? $amount=$grey_qnty*$price; echo number_format($amount,4); ?>
			  					</td>

			  				</tr>
			  				<?
			  				$sub_collor_qty_arr[$row[csf("body_part_id")]]+=$grey_qnty;
							$sub_collor_amount_arr[$row[csf("body_part_id")]]+=$amount;
							$sub_excess_arr[$row[csf("body_part_id")]]+=$avg_excess;
							$sub_price_arr[$row[csf("body_part_id")]]+=$price;
							$body_wise_avg_row[$row[csf("body_part_id")]]+=1;

							$gr_fin_qty+=$fin_qty;
			  				$gr_collor_qty+=$grey_qnty;
			  				$gr_avg_price+=$price;
							$gr_amount+=$amount;
			  				$gr_excess+=$avg_excess;

			  				$gr_avg_row++;

			  				$p++;
			  			}
			  			?>

			  			<tr>
			  				<td colspan="9" align="right">Total </td>
			  				<?
							//$sub_size_wise_qnty_array=array();
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $sub_size_wise_qnty_array[$bpart_id][$id];
								//$sub_size_wise_qnty_array[$row[csf("body_part_id")]][$id]=0;
								/*$grand_size_wise_qnty_array[$id]+=$sub_size_wise_qnty_array[$bpart_id][$id];*/
								?></td>
			  					<?
			  				}
							$tot_avg_row=$body_wise_avg_row[$bpart_id];
							$tot_sub_excess=$sub_excess_arr[$bpart_id];
							$tot_sub_price=$sub_price_arr[$bpart_id];
			  				?>
			  				<td align="center">
			  					<? echo $sub_collor_qty_arr[$bpart_id];  ?>
			  				</td>
			  				<td align="center" title="Tot Excess & Tot Row=<? echo $tot_sub_excess.'='.$tot_avg_row;?>"><? echo number_format($tot_sub_excess/$tot_avg_row,2); ?></td>
			  				<td align="center">
			  					<? echo number_format($tot_sub_price/$tot_avg_row,2);  ?>
			  				</td>
			  				<td align="center">
			  					<? echo number_format($sub_collor_amount_arr[$bpart_id],4);   ?>
			  				</td>
			  			</tr>
			  			<?
						} //Body Part End

			  		?>
					<tr>
			  				<td colspan="9" align="right">Grand Total </td>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $grand_size_wise_qnty_array[$id];?></td>
			  					<?
			  				}
			  				?>
			  				<td align="center">
			  					<? echo $gr_collor_qty;  ?>
			  				</td>
			  				<td align="center" title="grand Excess &tot row=<? echo $gr_excess.'='.$gr_avg_row;?>"><? echo number_format($gr_excess/$gr_avg_row,2); ?></td>
			  				<td align="center">
			  					<? //echo number_format($gr_avg_price/$gr_avg_row,2);  ?>
			  				</td>
			  				<td align="center">
			  					<? echo number_format($gr_amount,4);   ?>
			  				</td>
			  			</tr>
			  	</table>
				<?
					}
				?>

				</div>
			<br>
			<table  style="margin-top: 5px;float: left; display:none"    width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

				<tr>
					<td colspan="10" align="center">
						<strong>Comments</strong>
					</td>
				</tr>
				<tr>
					<td align="center"> SL </td>
					<td align="center" width="200"  style="word-wrap: break-word;word-break: break-all;"> PO NO </td>
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

						<td align="center"  style="word-wrap: break-word;word-break: break-all;"> <? echo $po_num_arr[$val[csf("po_number")]] ;?> </td>
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
			<br>


			<?
			if($cbo_fabric_source==1 || $cbo_fabric_source==2 || $cbo_fabric_source==3){
			?>


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
		<fieldset id="div_size_color_matrix" style="max-width:1000; display:none;">
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
	<br/><br/><br/><br/>
		    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
		    	<tr>
		            <td>  <?
		                    echo signature_table(121, $cbo_company_name, "1330px",1,'');
						//	$job_no_all= implode(",",array_unique($joball['job_no']));
							//$style_sting_all=implode(",",array_unique($joball['style_ref_no']));
							//echo "****".custom_file_name($txt_booking_no,$style_sting_all,$job_no_all);
		                ?>
		            </td>
		        </tr>
		    </table>
	</div>
	<?
			
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

if($action=="print_booking_16") // md mamun -09-06-2022-ISD-12897
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
	$contact_no_arr=return_library_array( "select id,contact_no from   lib_supplier  where status_active=1 and is_deleted=0",'id','contact_no');
	$designation_arr=return_library_array( "select id,designation from   lib_supplier  where status_active=1 and is_deleted=0",'id','designation');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier  where status_active=1 and is_deleted=0",'id','address_1');
	$supplier_contact_person_arr=return_library_array( "select id,contact_person from   lib_supplier  where status_active=1 and is_deleted=0",'id','contact_person');
	$comp_contact_person_arr=return_library_array( "select id,contract_person from lib_company where status_active=1 and is_deleted=0", "id", "contract_person");
	$employee_code_arr=return_library_array( "select id,employee_code from user_passwd  where status_active=1 ",'id','employee_code');
	$user_email_arr=return_library_array( "select id,user_email from user_passwd  where status_active=1 ",'id','user_email');


	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer  where status_active=1 and is_deleted=0",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location  where status_active=1 and is_deleted=0",'id','location_name');
	$user_name_arr=return_library_array( "select id,user_name from user_passwd  where status_active=1 ",'id','user_name');

	$user_name_sql=sql_select( "select d.id,d.user_name,d.user_email,d.employee_code,c.inserted_by from user_passwd d,wo_booking_mst c  where d.status_active=1 and d.is_deleted=0 and d.id=c.inserted_by");
	foreach ($user_name_sql as $row)
	{
		$user_email[$row[csf("id")]][$row[csf("user_name")]]=$row[csf("user_email")];
		$employee_code[$row[csf("id")]][$row[csf("employee_code")]]=$row[csf("employee_code")];
	}
	$po_sql=sql_select("select id,po_number,grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in(select po_break_down_id from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no=$txt_booking_no)");
	$int_ref_no="";
	foreach ($po_sql as $row)
	{
		$po_num_arr[$row[csf("id")]]=$row[csf("po_number")];
		$int_ref_no.=$row[csf("grouping")].',';
	}

	$uom=0;
	$joball=array();
	
	// $nameArray_per_job=sql_select( "SELECT  a.season_buyer_wise,  a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept,d.id,max(c.booking_date) as booking_date ,max(c.delivery_date) as delivery_date,max(c.is_approved) as is_approved,max(c.buyer_id) as buyer_id,max(c.pay_mode) as pay_mode,max(c.source) as source,max(supplier_id) as supplier_id,max(a.dealing_marchant) as dealing_marchant,max(c.attention) as attention,max(c.currency_id) as currency_id,max(c.fabric_source) as fabric_source ,max(c.remarks)  as remarks,max(c.inserted_by)  as inserted_by from wo_po_details_master a , wo_booking_dtls b,wo_booking_mst c where c.booking_no=b.booking_no and  c.status_active=1 and c.is_deleted=0 and  a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  group by  a.season_buyer_wise, a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept ");
	$nameArray_per_job=sql_select( "SELECT  a.season_buyer_wise,  a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept,max(c.booking_date) as booking_date ,max(c.delivery_date) as delivery_date,max(c.is_approved) as is_approved,max(c.buyer_id) as buyer_id,max(c.pay_mode) as pay_mode,max(c.source) as source,max(supplier_id) as supplier_id,max(a.dealing_marchant) as dealing_marchant,max(c.attention) as attention,max(c.currency_id) as currency_id,max(c.fabric_source) as fabric_source ,max(c.remarks)  as remarks,max(c.inserted_by)  as inserted_by from wo_po_details_master a , wo_booking_dtls b,wo_booking_mst c  where c.booking_no=b.booking_no and c.status_active=1 and c.is_deleted=0 and  a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0  group by  a.season_buyer_wise, a.job_no,a.style_ref_no ,a.season_matrix,a.gmts_item_id,a.product_dept ");
	foreach ($nameArray_per_job as $vals)
	{
		$joball['job_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('job_no')];
		$joball['style_ref_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('style_ref_no')];
		$all_job_arr[$vals[csf("job_no")]]["job"]="'".$vals[csf("job_no")]."'";
		$all_job_arr[$vals[csf("job_no")]]["style"]=$vals[csf("style_ref_no")];
		$all_job_arr[$vals[csf("job_no")]]["item"]=$vals[csf("gmts_item_id")];
		$all_jobs[$vals[csf("job_no")]]="'".$vals[csf("job_no")]."'";
		$all_jobs_no_arr[$vals[csf("job_no")]]=$vals[csf("job_no")];
		$job_wise_style[$vals[csf("job_no")]]= $vals[csf("style_ref_no")];
	}
	$all_jobs_id=implode(",",$all_jobs );
	$all_jobs_nos=implode(",",$all_jobs_no_arr );

 //	echo $all_jobs_id.'SSSA';
	$int_ref_no=rtrim($int_ref_no,',');
	if($int_ref_no!='')
	{
		$job_int_ref=implode(",", array_unique(explode(",",$int_ref_no)));
	}




	$path=str_replace("'","",$path);
	if($path=="")
	{
	$path='../../';
	}
	
	?>
	<style type="text/css">

		body, p, span, td, a {font-size:10pt;font-family: Arial;}
		body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}

	</style>
	<? ob_start(); ?>
	<div style="width:1330px" align="center" >

		<table id="tb_header" width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
			<thead>
				<tr>
					<td width="110" rowspan="3">
						<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' />
					</td>

					<td width="500" colspan="2" align="center" style="font-size: 16px;"><b><?php echo $company_library[$cbo_company_name]; ?></b></td>
					<td width="200">&nbsp;</td>
					
				</tr>

				<tr>

					<td  width="500" colspan="2" align="left">
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
					<td width="200">&nbsp;</td>
					
				</tr>

				<tr>

					<!-- <td  width="500" colspan="2" align="left" style="font-size: 16px;"><b>Multi-Job Fabric Booking-<? echo $fabric_source[$nameArray_per_job[0][csf("fabric_source")]];?></b></td> -->
					<td  width="500" colspan="2" align="center" style="font-size: 16px;"><b>Fabric Booking</b></td>
					<td width="200">&nbsp;</td>
					
				</tr>

				<tr>
					<td colspan="4" width="700">&nbsp;</td>
					
				</tr>
				<tr>
					<td colspan="4" height="11">&nbsp;</td>
				</tr>
				<tr>
					<td width="300"><b>To</b></td>
					<td colspan="3" width="700">&nbsp;</td>
					<td width="300" align="left"><b>Booking No: </b><?php echo str_replace("'", "", $txt_booking_no); ?></td>
				</tr>

				<tr>
					<td width="300"><?
					$attention="";
					$pay_mode_id=$nameArray_per_job[0][csf("pay_mode")];//pay_mode
					if($pay_mode_id!=3 && $pay_mode_id!=5)
					{
						echo $supplier_name_arr[$nameArray_per_job[0][csf("supplier_id")]];
						$attention=$supplier_contact_person_arr[$nameArray_per_job[0][csf("supplier_id")]];
					}
					else
					{
						echo $company_library[$nameArray_per_job[0][csf("supplier_id")]];
						$attention=$comp_contact_person_arr[$nameArray_per_job[0][csf("supplier_id")]];
					}?></td>
					<td colspan="3" width="700">&nbsp;</td>
					<td width="300" align="left"><b>Booking Date: </b><?php echo change_date_format($nameArray_per_job[0][csf("booking_date")]); ?></td>
				</tr>
				
				<tr>
					<td width="300" ><p><b>Address: </b><? echo $supplier_address_arr[$nameArray_per_job[0][csf("supplier_id")]]; ?> </p></td>
					<td colspan="3" width="700">&nbsp;</td>
					
					<td width="300" align="left"><b>Approval Status: </b><?php if ($nameArray_per_job[0][csf("is_approved")] == 1 || $nameArray_per_job[0][csf("is_approved")] == 3) {echo "Approved";}?></td>
					
				</tr>

				

				<tr>
				   <td width="300"><b>Attention: </b>   <? echo $attention; ?></td>
				   <td colspan="3" width="700">&nbsp;</td>
				   <td width="300"><b>Job No : </b>   <? echo $all_jobs_nos;?></td>

				</tr>
				<tr>
				   <td width="300" align="left"><b>Designation:   </b> <? echo $designation_arr[$nameArray_per_job[0][csf("supplier_id")]];; ?></td>
				   <td colspan="3" width="700">&nbsp;</td>
					<td width="300" align="left"><b>Responsible Merchandiser:   </b> <? echo $user_name_arr[$nameArray_per_job[0][csf("inserted_by")]]; ?></td>

				</tr>
				<tr>
				   <td width="300" align="left"><b>Contact Number:   </b> <? echo $contact_no_arr[$nameArray_per_job[0][csf("supplier_id")]];; ?></td>
				   <td colspan="3" width="700">&nbsp;</td>
				   <td width="300" align="left"><b>Contact No:   </b> 0<? echo $employee_code_arr[$nameArray_per_job[0][csf("inserted_by")]]; ?></td>

				</tr>

				<tr>
				   <td width="300" align="left"><b>Pay Mode:   </b> <? echo $pay_mode[$nameArray_per_job[0][csf("pay_mode")]]; ?></td>
				   <td colspan="3" width="700">&nbsp;</td>
				   <td width="300" align="left"><b>Email Id:   </b> <? echo $user_email_arr[$nameArray_per_job[0][csf("inserted_by")]]; ?></td>

				</tr>
				<tr>
				   <td width="300"><b>Currency : </b><? echo $currency[$nameArray_per_job[0][csf("currency_id")]];?></td>
				   <td colspan="3" width="700">&nbsp;</td>
				   <td width="300" align="left"><b>Buyer Name :   </b> <? echo $buyer_name_arr[$nameArray_per_job[0][csf("buyer_id")]]; ?></td>
				</tr>

				<tr>
				   <td width="300"><b>Source :  <? echo $source[$nameArray_per_job[0][csf("source")]]; ?> </b>    </td>
				   <td colspan="3" width="700">&nbsp;</td>
				   <td width="300" align="left"><b>Delivery Date: </b><?php echo change_date_format($nameArray_per_job[0][csf("delivery_date")]); ?></td>
				</tr>
				<tr>
				   <td width="400" align="left"><b>Remarks:   </b> <? echo $nameArray_per_job[0][csf("remarks")]; ?></td>
				</tr>
			</thead>
		</table>


			<?php

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

 			?>
			<br/>
			<!--  Here will be the main portion  -->
			<?
			$costing_per="";
			$costing_per_qnty=0;
			$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($all_jobs_id)");
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
			//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no in($all_jobs_id)");
			//wo_labtest_dtls

			//$labdip_no_arr=return_library_array( "select job_no,labtest_no from wo_labtest_mst a,wo_labtest_dtls b  where a.id=b.mst_id  and b.job_no in($all_jobs_id) and  b.status_active=1 and b.is_deleted=0",'job_no','labtest_no');
			$lab_dip_no_arr=array();
			$lab_dip_no_sql=sql_select("select lapdip_no, color_name_id,job_no_mst,po_break_down_id from wo_po_lapdip_approval_info where job_no_mst in($all_jobs_id) and status_active=1 and is_deleted=0 and approval_status=3 group by lapdip_no, color_name_id,job_no_mst,po_break_down_id ");
			foreach($lab_dip_no_sql as $row)
			{
				// $lab_dip_no_arr[$row[csf('color_name_id')]].=$row[csf('lapdip_no')].",";
				$lab_dip_no_arr[$row[csf('job_no_mst')]][$row[csf('po_break_down_id')]][$row[csf('color_name_id')]]=$row[csf('lapdip_no')];
			}
			unset($lab_dip_no_sql);
			//echo "select job_no,labtest_no from wo_labtest_mst a,wo_labtest_dtls b  where a.id=b.mst_id  and b.job_no in($all_jobs_id) and  b.status_active=1 and b.is_deleted=0";
		$data_img=sql_select("select image_location,master_tble_id  from common_photo_library  where   form_name='fabric_color_img' and is_deleted=0 and file_type=1");
	   $system_img_arr=array();
	  foreach($data_img as $row)
	   {
		  $system_img_arr[$row[csf('master_tble_id')]]['img']=$row[csf('image_location')];
	   }

			$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");
			$p=1;

		if($cbo_fabric_source==1 || $cbo_fabric_source==2 || $cbo_fabric_source==3)
		{
			$nameArray_fabric_description= sql_select("SELECT a.id as fabric_cost_dtls_id,d.dia_width as dia,a.construction,a.composition,a.fab_nature_id as fab_nat_id,a.gsm_weight,a.fabric_description as fab_desc, a.item_number_id as item_id, a.body_part_id,a.color_type_id as color_type,a.width_dia_type as width_dia,d.po_break_down_id as po_id,d.remark as fab_remark,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down,d.gmts_color_id as color_id,d.job_no
			  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
				WHERE a.job_no=d.job_no and
				a.id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no =$txt_booking_no and
				d.job_no in ($all_jobs_id) and
				d.status_active=1 and
				d.is_deleted=0 and
				d.fin_fab_qnty>0   and a.body_part_type not in(40,50)
				group by a.id,a.item_number_id,a.gsm_weight,a.fab_nature_id,a.fabric_description ,a.body_part_id,a.color_type_id,d.gmts_color_id,a.width_dia_type,d.po_break_down_id,d.remark,a.uom ,d.fabric_color_id,a.color_size_sensitive,a.color_break_down,d.job_no,d.dia_width,a.construction,a.composition  order by a.body_part_id,d.fabric_color_id ");

			foreach($nameArray_fabric_description as $row)
			{
				$item_desc= $body_part[$row[csf("body_part_id")]].",".$color_type[$row[csf("color_type_id")]].",".$row[csf("fab_desc")].','.$row[csf('dia')];
				$uom_data_arr[$row[csf('uom')]]=$row[csf('uom')];
				if($row[csf("color_type_id")]==2 || $row[csf("color_type_id")]==6)
				{
					$strip_color_arr[$row[csf('body_part_id')]][$row[csf('color_id')]]=$row[csf('fin_fab_qntys')];
				}
				$lab_dib=$lab_dip_no_arr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]];
				$lab_dip_arr[$lab_dib]=$lab_dib;
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['dia']=$row[csf('dia')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['color_type']=$row[csf('color_type')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['width_dia']=$row[csf('width_dia')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['uom']=$row[csf('uom')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['item_id']=$row[csf('item_id')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['dia_width']=$row[csf('dia_width')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['construction']=$row[csf('construction')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['composition']=$row[csf('composition')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['fab_color_id']=$row[csf('fabric_color_id')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['body_part_id']=$row[csf('body_part_id')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['fab_remark']=$row[csf('fab_remark')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['amount']=$row[csf('amounts')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['fin_fab_qnty']=$row[csf('fin_fab_qntys')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['color_size_sensitive']=$row[csf('color_size_sensitive')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['color_break_down']=$row[csf('color_break_down')];
				$fabric_data_arr[$lab_dib][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fab_nat_id')]][$item_desc][$row[csf('color_id')]]['fabric_cost_dtls_id']=$row[csf('fabric_cost_dtls_id')];

			}

			
					?>
					<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
									<caption> <strong style="font-size:20px">Fabric Booking Detail </strong> </caption>
									<tr>
										<th  width="30" align="center">SL</th>
										<th  width="100" align="center">Style Ref</th>
										<th  width="100" align="center">Order No</th>
										<th  width="70" align="center">Fabric Nature</th>
										<th  width="300" align="center">Fabric Description</th>
										<th  width="35" align="center">GSM</th>
										<th  width="120" align="center">FabricDia/ Width</th>
										<th  width="70" align="center">Color Type</th>
										<th  width="60" align="center">Gmts Color</th>
										<th  width="65" align="center">Fabric Color</th>
										<th  width="130" align="center">Lab Dip No</th>
										<th  width="45" align="center">UOM</th>
										<th  width="70" align="center">Finish Fab. Qty</th>
										<!-- <th  width="240" align="center">Image</th> -->
										<th width="80" align="center" title="">Avg Rate</th>
										<th width="80" align="center">Amount</th>
										<th width="100" align="center">Remark</th>

									</tr>
									<? //wo_pre_cos_fab_co_color_dtls
										$total_fin_fab_qnty=$total_amount=0;
										$k=1;
									foreach($fabric_data_arr as $lab_dip_id=>$fabric_data)
									{
										$sub_total_fin_fab_qnty=0;
										$sub_total_amount=0;									
									foreach($fabric_data as $style_id=>$style_data)
									{
									  foreach($style_data as $po_id=>$po_data)
									  {
									  	 foreach($po_data as $nat_id=>$nat_data)
									     {
										 	foreach($nat_data as $desc_id=>$desc_data)
									        {
												foreach($desc_data as $color_id=>$val)
									        	{

													
													
									
													
											if($val[('fab_remark')]=='no remarks')  $pre_cost_remarks="";else  $pre_cost_remarks=$val[('fab_remark')];
											$diaWidth=$val[('dia')];
											if($diaWidth!='') $diaWidth=$diaWidth.",";else $diaWidth="";
											?>
											<tr>
												<td align="center"> <? echo $k;?></td>
												<td align="center" title="<? echo $style_id;?>"> <? echo $all_job_arr[$style_id]["style"];?></td>
												<td align="center"> <? echo $po_num_arr[$po_id];?></td>
												<td align="center"><? echo $item_category[$nat_id];?></td>
												<td align="center"><? echo $desc_id;?></td>
												<td align="center"> <? echo $val[('gsm_weight')]; ?> </td>

												<td  align="center">
													<?
													echo $diaWidth.$fabric_typee[$val[('width_dia')]];
													?>
												</td>
												<td  align="center">
													<?
													echo $color_type[$val[('color_type')]];
													?>
												</td>
												<td  align="center">
													<?
													if($val[('color_size_sensitive')]==1 or $val[('color_size_sensitive')]==2 or $val[('color_size_sensitive')]==4)
													{
														echo $color_library[$val[('fab_color_id')]];
													}

													else
													{
														$color_break_down=$val[('color_break_down')];
														if($color_break_down)
														{
															$gmts_color="";
															if (strpos($color_break_down, '__') !== false)
															{
																$color_break_down=explode('__', $color_break_down);
																foreach ($color_break_down as $key => $value)
																{
																	$cols=explode('_', $value);
																	if(trim(strtolower($color_library[$val[('fab_color_id')]]))==trim(strtolower($cols[2])))
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
																if(trim(strtolower($color_library[$val[('fab_color_id')]]))==trim(strtolower($cols[2])))
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
												<td  align="center" title="<?=$val[('fab_color_id')];?>">
													<?

													echo $color_library[$val[('fab_color_id')]];
													?>
												</td>
												<td align="center"><? echo $lab_dip_id;//$labdip_no_arr[$style_id]; ?></td>
												<td align="center"> <? echo $unit_of_measurement[$val[('uom')]]; ?></td>
												<td align="center"> <? echo def_number_format($val[("fin_fab_qnty")],2); ?></td>
													<!-- <td align="center"> <?
													$img_ref_id=$val[('fabric_cost_dtls_id')].'_'.$val[('fab_color_id')];
													$fab_color_img=$system_img_arr[$img_ref_id]['img'];

													//echo def_number_format($val[("fin_fab_qntys")],2);
													if($fab_color_img!='')
													{
													?>
													<img src='../../<? echo $fab_color_img; ?>' height='50' width='70' align="middle" />
													<?
													}
													else
													{
													?>
														<img src='../../<? echo $fab_color_img; ?>' height='50' width='70' align="middle" />
													<?
													}
													?>
													</td> -->
													<td align="center"> <? echo def_number_format($val[("amount")]/$val[("fin_fab_qnty")],2); ?> </td>
													<td align="center"> <? echo def_number_format($val[("amount")],2); ?> </td>
													<td align="center"> <? echo $pre_cost_remarks; ?> </td>
													<?
													$total_fin_fab_qnty +=$val[("fin_fab_qnty")];
													$total_amount +=$val[("amount")];
													$sub_total_fin_fab_qnty +=$val[("fin_fab_qnty")];
													$sub_total_amount +=$val[("amount")];


												?>

												</tr>
												<?
												$k++;
															}	
															} // Color End

														} // Desc End
													} // Nature End
												} // Po End

												?>
												<tr style="font-weight:bold">
									    <td  align="right" colspan="12"><strong>Sub Total&nbsp;</strong></td>
									    <td align="center"><? echo def_number_format($sub_total_fin_fab_qnty,2);?></td>
									    <!-- <td align="center"></td> -->
									    <td align="center"></td>
									    <td align="center"><? echo def_number_format($sub_total_amount,2);?></td>
									    <td align="center"></td>
									</tr>
									<?

										} // Style End
										$currency_id=$nameArray_per_job[0][csf("currency_id")];
										if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="Cents";}
										?>
									<tr style="font-weight:bold">
									    <td  align="right" colspan="12"><strong> Grand Total&nbsp;</strong></td>
									    <td align="center"><? echo def_number_format($total_fin_fab_qnty,2);?></td>
									    <!-- <td align="center"></td> -->
									    <td align="center"></td>
									    <td align="center"><? echo def_number_format($total_amount,2);?></td>
									    <td align="center"></td>
									</tr>
									<tr>
										<th colspan="12" align="left" style="font-size:16px;">Grand Total Booking Amount (in words):<? echo str_replace("-"," ",number_to_words(def_number_format($total_amount,2,""),$currency[$currency_id],$paysa_sent)); ?></th>
										<th colspan="5" align="left"></th>
									</tr>
									
									<!-- <tr style=" font-weight:bold">
									<td  align="right" colspan="5"><strong>Total</strong></td>
									<td align="right"><? //echo def_number_format($total_gmt_qty_pcs,2);?></td>
									<td  align="right" colspan="8">&nbsp;</td>

								<td align="center"><? //echo def_number_format($total_fin_fab_qnty,2);?></td>
									<td align="center"><? //echo def_number_format($total_fin_fab_qnty,2);?></td>
									<td align="center"><? //echo def_number_format($total_fin_fab_qnty,2);?></td>

								<td align="center"><? //echo def_number_format($total_amount,2);?></td>
									<td align="center"><? //echo def_number_format($total_amount,2);?></td>
									</tr> -->
						</table>
					<br/>

					<?
			//End

		}
					?>
			</table>
			<br>
				<div style="width:1330px" align="center" >
			  		<!-- <table  width="100%"  border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

			  			<tr>
			  				<td colspan="10" align="center">
			  					<strong>Collar and Cuff & Other Flat Knit Fabric Breakdown</strong>
			  				</td>

			  			</tr>
			  		</table> -->

	        <?
	     	  $sql_collar_cuff= "SELECT a.rate, a.amount, a.job_no , a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, a.width_dia_type, b.gmts_color_id, b.size_number_id, b.item_size, b.gmts_qty, b.excess_per, b.qty, b.po_break_down_id as po_id, b.id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=40 and b.status_active=1 and b.is_deleted=0  order by a.body_part_id,b.gmts_color_id,b.id " ;
	     	  //echo $sql_collar_cuff; die;
	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
	         $size_id_array=array();
	         $size_with_item_size_array=array();
	         foreach($sql_data_collar_cuff as $vals)
	         {
	         	$body_part_cuff_array[$vals[csf("body_part_id")]]=$vals[csf("body_part_id")];
				$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	         	$size_with_item_size_array[$vals[csf("size_number_id")]][$vals[csf("body_part_id")]]=$vals[csf("item_size")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["grey"]+=$vals[csf("qty")];

	         	//$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]=$vals[csf("excess_per")];
				if($vals[csf("excess_per")]>0)
				{
				$color_wise_excess_per_arr[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]+=$vals[csf("excess_per")];
				$color_wise_excess_per_arr[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_excess_per"]+=1;
				}
	         	$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["grey"]+=$vals[csf("qty")];
	         	/*$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];*/

	         	//$color_wise_qnty[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["amount"]=$vals[csf("amount")];
				$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_rate"]=1;

	         }
			unset($sql_data_collar_cuff);
	         $sql_collar_cuff3 = sql_select("SELECT c.rate, a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, c.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls c WHERE a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and  a.id=c.pre_cost_fabric_cost_dtls_id and c.booking_no =$txt_booking_no and c.job_no in ($all_jobs_id) and a.body_part_type=40 and c.status_active=1 and c.is_deleted=0  order by a.body_part_id");

	         foreach($sql_collar_cuff3 as $vals)
	         {

	         	$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];

	         }


	         $counts=count($size_id_array);


	         $size_wise_qnty_array=array();
	         ?>


			 <?
	          if(count($body_part_cuff_array)>0)
	          {		$grand_size_wise_qnty_array=array();
			  		$gr_collor_qty=0;
					 $gr_col_avg_price=0;
					 $gr_col_avg_row=0;
					 $gr_amount=0;
					 $gr_excess=0;
					foreach($body_part_cuff_array as $bpart_id=>$val)
					{
					 $sql_collar_cuff2= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id,a.color_size_sensitive,a.color_break_down, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.body_part_id=$bpart_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=40  and b.status_active=1 and b.is_deleted=0  group by  a.rate,a.amount, a.job_no , a.id ,a.color_size_sensitive,a.color_break_down, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition,a.gsm_weight,a.width_dia_type,b.gmts_color_id  order by a.body_part_id,b.gmts_color_id  " ;
	          $sql_data_collar_cuff2=sql_select($sql_collar_cuff2);
					?>
					 <table align="left"  width="100%" style="margin-top: 5px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
			  			<tr>
			  				<td colspan="<? echo $counts+13;?>" align="center">
			  					<strong>Collar Details(<? echo $body_part[$bpart_id];?>)</strong>
			  				</td>

			  			</tr>
			  			<tr>
			  				<td  width="30"  rowspan="2" align="center"><strong>SL</strong> </td>
			  				<td  width="150" rowspan="2" align="center"><strong>Style</strong></td>
 			  				<td  width="140"  rowspan="2" align="center"><strong>Gmts Item</strong></td>
 			  				<td  width="100"  rowspan="2" align="center"><strong>Body Part</strong></td>
							<td  width="100" rowspan="2" align="center"><strong>Actual Qty(Pcs)</strong></td>
			  				<td  width="390" rowspan="2" align="left">&nbsp;&nbsp;<strong>Collar Composition</strong></td>
			  				<td  width="100"  rowspan="2" align="center"><strong>Color Type</strong></td>
 			  				<td  width="100"  rowspan="2"  align="center"><strong>Combo</strong></td>
							<td  width="100"  rowspan="2"  align="center"><strong>Fabric Color</strong></td>
 			  				<?
 			  				foreach($size_id_array as $id=>$val)
 			  				{
 			  					?>
 			  					<td width="40" align="center"><strong><? echo $size_library[$id];?></strong></td>

 			  					<?
 			  				}
 			  				?>
			  				<td width="60"  rowspan="2" align="center"><strong> Req Qty (Pcs)</strong> </td>
			  				<td  width="50" rowspan="2"  align="center"><strong> Excess %  </strong> </td>
			  				<td width="50" rowspan="2"  align="center"><strong>Price/Pcs</strong></td>
			  				<td  width="40" rowspan="2" align="center"><strong>Amount</strong></td>


			  			</tr>
			  			<tr>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					//$size_with_item=rtrim($size_with_item_size_array[$id],',');
								//$size_with_item=implode(",",array_unique(explode(",",$size_with_item)));
								?>
			  					<td  align="center"><? echo $size_with_item_size_array[$id][$bpart_id];?></td>

			  					<?
			  				}
			  				?>

			  			</tr>
			  			<?
			  			$p=1;
			  			foreach($sql_data_collar_cuff2 as $row)
			  			{

							?>
			  				<tr>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="30" align="center"><? echo $p;?></td>
			  					<td style="word-break: break-all;word-wrap: break-word;" width="150" align="center"><? echo $job_wise_style[$row[csf("job_no")]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="140" align="center"><?  $item_id= $row[csf('item_number_id')];echo $garments_item[$item_id]; ?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? echo $body_part[$row[csf("body_part_id")]];?></td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? $gmt_actual_qty=$gmts_qty_data_arr[$row[csf('job_no')]][$item_id][$row[csf('gmts_color_id')]];echo number_format($gmt_actual_qty,0);$total_gmt_actual_qty+=$gmt_actual_qty;?></td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="390" align="left"> &nbsp;
			  						<? echo $row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]] ;?>
			  					</td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $color_type[$row[csf('color_type_id')]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
			  						<? echo $color_library[$row[csf('gmts_color_id')]];  ?>
			  					</td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
			  						<?
										if($row[csf('color_size_sensitive')]==1 or $row[csf('color_size_sensitive')]==2 or $row[csf('color_size_sensitive')]==4)
										{
											$gmt_fab_color=$color_library[$row[csf('gmts_color_id')]];
										}
										else
										{
											$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row[csf('fabric_cost_dtls_id')]."  and b.gmts_color_id=".$row[csf('gmts_color_id')]." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
											if($contrast_color_id>0)
											{
												$gmt_fab_color=$color_library[$contrast_color_id];
											}
										}
									echo $gmt_fab_color;  ?>
			  					</td>


			  					<?
								$color_qty=0; $greyQty=0;
			  					foreach($size_id_array as $id=>$val)
			  					{
			  						?>
			  						<td  style="word-break: break-all;word-wrap: break-word;" width="40"  align="center"><? echo $cuff_grey_qty=$size_item_wise_qnty[$row[csf("body_part_id")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]][$id][$size_with_item_size_array[$id][$bpart_id]]["grey"];//.'K '.$row[csf("fabric_cost_dtls_id")].'='.$row[csf("gmts_color_id")].'='.$id.'='.$size_with_item_size_array[$id];
									?></td>

			  						<?
			  						$size_wise_qnty_array[$id]+=$val;
									$grand_size_wise_qnty_array[$id]+=$cuff_grey_qty;$sub_size_wise_qnty_array[$bpart_id][$id]+=$cuff_grey_qty;
									$color_qty+=$cuff_grey_qty;

			  					}
								$row_excess_per= $color_wise_excess_per_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["row_excess_per"];
			  					?>
			  					<td  style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
			  						<? echo $grey_qnty=$color_qty;//$color_wise_qnty[$row[csf('gmts_color_id')]][$item_id]["grey"];  ?>
			  					</td>

			  					<td  style="word-break: break-all;word-wrap: break-word;" title="<? echo $row_excess_per;?>" width="50" align="center">
			  						<?  $excess= $color_wise_excess_per_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["excess_per"]/$row_excess_per;
									echo number_format($excess,2);
									  ?>
			  					</td>


			  					<td style="word-break: break-all;word-wrap: break-word;" title="<? echo $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];?>"  width="50" align="center">
			  						<? echo  $cuff_price=$color_wise_qnty2[$row[csf("fabric_cost_dtls_id")]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["rate"];
										//$tot_row_rate=$color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];  ?>
			  					</td>
			  					<td style="word-break: break-all;word-wrap: break-word;"  width="40" align="center">
			  						<? $amount=$grey_qnty*$cuff_price; echo number_format( $amount,4); ?>
			  					</td>
			  				</tr>
			  				<?

							$sub_collor_qty_arr[$row[csf("body_part_id")]]+=$grey_qnty;
							$sub_collor_amount_arr[$row[csf("body_part_id")]]+=$amount;

							$sub_excess_arr[$row[csf("body_part_id")]]+=$excess;
							$sub_price_arr[$row[csf("body_part_id")]]+=$cuff_price;
							$cuff_body_wise_avg_row[$row[csf("body_part_id")]]+=1;

							$gr_fin_qty+=$fin_qty;
			  				$gr_collor_qty+=$grey_qnty;
							$gr_amount+=$amount;
			  				$gr_excess+=$excess ;
			  				$gr_col_avg_price+=$cuff_price;
			  				$gr_col_avg_row++;



			  				$p++;
			  			}
			  			?>
						<tr class="tbl_bottom">
							<td colspan="9" align="right">Total </td>
							<?
							foreach($size_id_array as $id=>$val)
							{
								?>
								<td  align="center"><? echo $sub_size_wise_qnty_array[$bpart_id][$id];?></td>

								<?
							}
							$cuff_sub_excess=$sub_excess_arr[$bpart_id];
							$cuff_sub_price=$sub_price_arr[$bpart_id];
							$cuff_body_wise_avg_tot_row=$cuff_body_wise_avg_row[$bpart_id];
							?>
							<td align="center">
								<? echo $sub_collor_qty_arr[$bpart_id];  ?>
							</td>
							<td align="center" title="Sub Exess & sub Row=<? echo $cuff_sub_excess.'='.$cuff_body_wise_avg_tot_row ?>">
									<? echo number_format($cuff_sub_excess/$cuff_body_wise_avg_tot_row,2); ?></td>
							<td align="center"><? echo number_format($cuff_sub_price/$cuff_body_wise_avg_tot_row,2); ?></td>

							<td align="center">
								<? echo number_format($sub_collor_amount_arr[$bpart_id],4);   ?>
							</td>
			  			</tr>
						<!--Sub TOT End-->


			  			<?
						} //Body Part End
						?>

						<tr>
			  				<td colspan="9" align="right">Grand Total </td>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $grand_size_wise_qnty_array[$id];?></td>

			  					<?
			  				}
			  				?>
			  				<td align="center">
			  					<? echo $gr_collor_qty;  ?>
			  				</td>
			  				<td align="center" title="Sub Exess & Tot Row=<? echo $gr_excess.'='.$gr_avg_row ?>"><? echo number_format($gr_excess/$gr_col_avg_row,2); ?></td>
							<td align="center"  title="Tot Price  & Tot Row=<? echo $gr_col_avg_price.'='.$gr_col_avg_row.'**'.$p?>"><? //echo number_format($gr_col_avg_price/$gr_col_avg_row,2); ?></td>

			  				<td align="center">
			  					<? echo number_format($gr_amount,4);   ?>
			  				</td>
			  			</tr>

						</table>
						<?
			  		}
			  	?>


					<br/><br/>

						<?
	     	  $sql_collar_cuff= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id,b.size_number_id,b.item_size,b.gmts_qty,b.excess_per,b.qty,b.po_break_down_id as po_id,b.id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and b.job_no in ($all_jobs_id) and a.body_part_type=50 and b.status_active=1 and b.is_deleted=0  order by a.body_part_id,b.gmts_color_id,b.id " ;
	         $sql_data_collar_cuff=sql_select($sql_collar_cuff);
	         $size_id_array=array();
	         $size_with_item_size_array=array();
	         $size_item_wise_qnty=array();
	         $color_wise_qnty=array();
	         foreach($sql_data_collar_cuff as $vals)
	         {
	         	$body_part_array[$vals[csf("body_part_id")]]=$vals[csf("body_part_id")];
				$size_id_array[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	         	$size_with_item_size_array[$vals[csf("size_number_id")]][$vals[csf("body_part_id")]]=$vals[csf("item_size")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$size_item_wise_qnty[$vals[csf("body_part_id")]][$vals[csf("fabric_cost_dtls_id")]][$vals[csf("gmts_color_id")]][$vals[csf("size_number_id")]][$vals[csf("item_size")]]["grey"]+=$vals[csf("qty")];

	         	//$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]=$vals[csf("excess_per")];
				if($vals[csf("excess_per")]>0)
				{
				$color_wise_excess_per_cuff_arr[$vals[csf('fabric_cost_dtls_id')]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["excess_per"]+=$vals[csf("excess_per")];
				$color_wise_excess_per_cuff_arr[$vals[csf('fabric_cost_dtls_id')]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_excess_per"]+=1;
				}
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["fin"]+=$vals[csf("gmts_qty")];
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["grey"]+=$vals[csf("qty")];
	         	/*$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];*/
	         	$color_wise_qnty[$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["amount"]=$vals[csf("amount")];
				$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["row_rate"]=1;

	         }
				unset($sql_data_collar_cuff);
	         $sql_collar_cuff3 = sql_select("SELECT c.rate, a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id, c.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls c WHERE a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and  a.id=c.pre_cost_fabric_cost_dtls_id and c.booking_no =$txt_booking_no and c.job_no in ($all_jobs_id) and a.body_part_type=50 and c.status_active=1 and c.is_deleted=0  order by a.body_part_id");

	         foreach($sql_collar_cuff3 as $vals)
	         {

	         	$color_wise_qnty2[$vals[csf("fabric_cost_dtls_id")]][$vals[csf("body_part_id")]][$vals[csf("gmts_color_id")]][$vals[csf("item_number_id")]]["rate"]=$vals[csf("rate")];

	         }


	         $counts=count($size_id_array);$grand_size_wise_qnty_array=array();
	          if(count($body_part_array)>0)
	          {
					$gr_collor_qty=$gr_amount=$gr_avg_price=$gr_avg_row=$gr_excess=0;
					foreach($body_part_array as $bpart_id=>$val)
					{
					 $sql_collar_cuff2= "SELECT a.rate,a.amount, a.job_no , a.id as fabric_cost_dtls_id,a.color_size_sensitive, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,b.gmts_color_id  FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no =$txt_booking_no and a.body_part_id=$bpart_id and b.job_no in ($all_jobs_id) and a.body_part_type=50  and b.status_active=1 and b.is_deleted=0  group by  a.rate,a.amount, a.job_no , a.id , a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition,a.gsm_weight,a.width_dia_type,b.gmts_color_id,a.color_size_sensitive  order by a.body_part_id,b.gmts_color_id  " ;
	          $sql_data_collar_cuff2=sql_select($sql_collar_cuff2);
					?>

			  	 <table align="left"  width="100%" style="margin-top: 5px; margin-bottom:10px;"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
			  			<tr>
			  				<td colspan="<? echo $counts+13;?>" align="center">
			  					<strong>Cuff Details &nbsp;(<? echo  $body_part[$bpart_id];?>) </strong>
			  				</td>
			  			</tr>
			  			<tr>
			  				<td  width="30"  rowspan="2" align="center"><strong>SL</strong> </td>
			  				<td  width="150" rowspan="2" align="center"><strong>Style</strong></td>
 			  				<td  width="140" rowspan="2" align="center"><strong>Gmts Item</strong></td>
 			  				<td  width="100" rowspan="2" align="center"><strong>Body Part</strong></td>
							<td  width="100" rowspan="2" align="center"><strong>Actual Qty(Pcs)</strong></td>
			  				<td  width="390" rowspan="2" align="left">&nbsp;&nbsp;<strong>Cuff Composition</strong></td>
			  				<td  width="100" rowspan="2" align="center"><strong>Color Type</strong></td>
 			  				<td  width="100" rowspan="2"  align="center"><strong>Combo</strong></td>
							<td  width="100" rowspan="2"  align="center"><strong>Fabric Color</strong></td>
 			  				<?
 			  				foreach($size_id_array as $id=>$val)
 			  				{
 			  					?>
 			  					<td width="40" align="center"><strong><? echo $size_library[$id];?></strong></td>

 			  					<?
 			  				}
 			  				?>

			  				<td width="60"  rowspan="2" align="center"><strong> Req Qty(Pcs) </strong> </td>
			  				<td  width="50" rowspan="2"  align="center"><strong> Excess %  </strong> </td>

			  				<td width="50" rowspan="2"  align="center"><strong>Price/Pcs</strong></td>
			  				<td  width="40" rowspan="2" align="center"><strong>Amount</strong></td>
			  			</tr>
			  			<tr>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td width="40"  align="center"><strong><? echo $size_with_item_size_array[$id][$bpart_id];?></strong></td>
			  					<?
			  				}
			  				?>
			  			</tr>
			  			<?
			  			$p=1;
			  			$size_wise_qnty_array=array();
			  			foreach($sql_data_collar_cuff2 as $row)
			  			{
							?>
			  				<tr>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="30" align="center"><? echo $p;?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="150" align="center"><? echo $job_wise_style[$row[csf("job_no")]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="140" align="center"><?  $item_id= $row[csf('item_number_id')];echo $garments_item[$item_id]; ?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $body_part[$row[csf("body_part_id")]];?></td>
								<td  style="word-break: break-all;word-wrap: break-word;" width="100" align="center"><? $gmt_actual_qty=$gmts_qty_data_arr[$row[csf('job_no')]][$item_id][$row[csf('gmts_color_id')]];echo number_format($gmt_actual_qty,0);$total_gmt_actual_qty+=$gmt_actual_qty;?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="390" align="left"> &nbsp;
			  						<? echo $row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]] ;?>
			  					</td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center"><? echo $color_type[$row[csf('color_type_id')]];?></td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center">
			  						<? echo $color_library[$row[csf('gmts_color_id')]];  ?>
			  					</td>
								<td  style="word-break: break-all;word-wrap: break-word;"  width="100" align="center">
			  						<?
									if($row[csf('color_size_sensitive')]==1 or $row[csf('color_size_sensitive')]==2 or $row[csf('color_size_sensitive')]==4)
										{
											$gmt_fab_color=$color_library[$row[csf('gmts_color_id')]];
										}
										else
										{
											$contrast_color_id=return_field_value("b.contrast_color_id as contrast_color_id", "wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b", "a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.job_no in ($all_jobs_id) and a.id=".$row[csf('fabric_cost_dtls_id')]."  and b.gmts_color_id=".$row[csf('gmts_color_id')]." ","contrast_color_id");//pre_cost_fabric_cost_dtls_id
											if($contrast_color_id>0)
											{
												$gmt_fab_color=$color_library[$contrast_color_id];
											}
										}
									echo $gmt_fab_color;   ?>
			  					</td>

			  					<?
								$tot_size_qty=0;
			  					foreach($size_id_array as $id=>$val)
			  					{
			  						?>
			  						<td  style="word-break: break-all;word-wrap: break-word;"  width="40"  align="center"><? echo $size_grey_qty=$size_item_wise_qnty[$row[csf("body_part_id")]][$row[csf('fabric_cost_dtls_id')]][$row[csf("gmts_color_id")]][$id][$size_with_item_size_array[$id][$row[csf("body_part_id")]]]["grey"];?></td>

			  						<?
			  						$size_wise_qnty_array[$id]+=$size_grey_qty;$sub_size_wise_qnty_array[$row[csf("body_part_id")]][$id]+=$size_grey_qty;
									$grand_size_wise_qnty_array[$id]+=$size_grey_qty;
									$tot_size_qty+=$size_grey_qty;

			  					}
			  					?>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="60" align="center">
			  						<? echo $grey_qnty=$tot_size_qty;//$color_wise_qnty[$row[csf('gmts_color_id')]][$item_id]["grey"];
									$row_excess_per=$color_wise_excess_per_cuff_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["row_excess_per"];
									 ?>
			  					</td>

			  					<td  style="word-break: break-all;word-wrap: break-word;" title="excess tot=<? echo $row_excess_per; ?>"  width="50" align="center">
			  						<?  $avg_excess=$color_wise_excess_per_cuff_arr[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$item_id]["excess_per"]/$row_excess_per;echo number_format($avg_excess,2);   ?>
			  					</td>
			  					<td   style="word-break: break-all;word-wrap: break-word;" width="50" title="<? echo $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"];?>" align="center">
			  						<? echo $price= $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["rate"];
									$tot_row_rate= $color_wise_qnty2[$row[csf('fabric_cost_dtls_id')]][$row[csf("body_part_id")]][$row[csf('gmts_color_id')]][$item_id]["row_rate"]; ?>
			  					</td>
			  					<td  style="word-break: break-all;word-wrap: break-word;"  width="40" align="center">
			  						<? $amount=$grey_qnty*$price; echo number_format($amount,4); ?>
			  					</td>

			  				</tr>
			  				<?
			  				$sub_collor_qty_arr[$row[csf("body_part_id")]]+=$grey_qnty;
							$sub_collor_amount_arr[$row[csf("body_part_id")]]+=$amount;
							$sub_excess_arr[$row[csf("body_part_id")]]+=$avg_excess;
							$sub_price_arr[$row[csf("body_part_id")]]+=$price;
							$body_wise_avg_row[$row[csf("body_part_id")]]+=1;

							$gr_fin_qty+=$fin_qty;
			  				$gr_collor_qty+=$grey_qnty;
			  				$gr_avg_price+=$price;
							$gr_amount+=$amount;
			  				$gr_excess+=$avg_excess;

			  				$gr_avg_row++;

			  				$p++;
			  			}
			  			?>

			  			<tr>
			  				<td colspan="9" align="right">Total </td>
			  				<?
							//$sub_size_wise_qnty_array=array();
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $sub_size_wise_qnty_array[$bpart_id][$id];
								//$sub_size_wise_qnty_array[$row[csf("body_part_id")]][$id]=0;
								/*$grand_size_wise_qnty_array[$id]+=$sub_size_wise_qnty_array[$bpart_id][$id];*/
								?></td>
			  					<?
			  				}
							$tot_avg_row=$body_wise_avg_row[$bpart_id];
							$tot_sub_excess=$sub_excess_arr[$bpart_id];
							$tot_sub_price=$sub_price_arr[$bpart_id];
			  				?>
			  				<td align="center">
			  					<? echo $sub_collor_qty_arr[$bpart_id];  ?>
			  				</td>
			  				<td align="center" title="Tot Excess & Tot Row=<? echo $tot_sub_excess.'='.$tot_avg_row;?>"><? echo number_format($tot_sub_excess/$tot_avg_row,2); ?></td>
			  				<td align="center">
			  					<? echo number_format($tot_sub_price/$tot_avg_row,2);  ?>
			  				</td>
			  				<td align="center">
			  					<? echo number_format($sub_collor_amount_arr[$bpart_id],4);   ?>
			  				</td>
			  			</tr>
			  			<?
						} //Body Part End

			  		?>
					<tr>
			  				<td colspan="9" align="right">Grand Total </td>
			  				<?
			  				foreach($size_id_array as $id=>$val)
			  				{
			  					?>
			  					<td  align="center"><? echo $grand_size_wise_qnty_array[$id];?></td>
			  					<?
			  				}
			  				?>
			  				<td align="center">
			  					<? echo $gr_collor_qty;  ?>
			  				</td>
			  				<td align="center" title="grand Excess &tot row=<? echo $gr_excess.'='.$gr_avg_row;?>"><? echo number_format($gr_excess/$gr_avg_row,2); ?></td>
			  				<td align="center">
			  					<? //echo number_format($gr_avg_price/$gr_avg_row,2);  ?>
			  				</td>
			  				<td align="center">
			  					<? echo number_format($gr_amount,4);   ?>
			  				</td>
			  			</tr>
			  	</table>
				<?
					}
				?>

				</div>
			<br>
			<table  style="margin-top: 5px;float: left; display:none"    width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

				<tr>
					<td colspan="10" align="center">
						<strong>Comments</strong>
					</td>
				</tr>
				<tr>
					<td align="center"> SL </td>
					<td align="center" width="200"  style="word-wrap: break-word;word-break: break-all;"> PO NO </td>
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

						<td align="center"  style="word-wrap: break-word;word-break: break-all;"> <? echo $po_num_arr[$val[csf("po_number")]] ;?> </td>
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
			<br>


			<?
			if($cbo_fabric_source==1 || $cbo_fabric_source==2 || $cbo_fabric_source==3){
			?>


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
		<fieldset id="div_size_color_matrix" style="max-width:1000; display:none;">
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
	<br>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
				<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="3%"></th><th width="97%" align="left" style="font-size:16px"><strong><u>Special Instruction</u></strong></th>
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
										<span style="font-size:12px"> <? echo $row[csf('terms')]; ?></span>
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
	<br/><br/><br/><br/>
		    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
		    	<tr>
		            <td>  <?
		                    echo signature_table(121, $cbo_company_name, "1330px",1,'');
						//	$job_no_all= implode(",",array_unique($joball['job_no']));
							//$style_sting_all=implode(",",array_unique($joball['style_ref_no']));
							//echo "****".custom_file_name($txt_booking_no,$style_sting_all,$job_no_all);
		                ?>
		            </td>
		        </tr>
		    </table>
	</div>
	<?
			
			$html = ob_get_contents();
			ob_clean();
			list($is_mail_send,$mail)=explode('___',$mail_send_data);
		
			if($is_mail_send==1){
				
				
				//pdf att file..............................................
				$REAL_FILE_NAME = custom_file_name($txt_booking_no,$style_sting,$job_no);
				$user_id=$_SESSION['logic_erp']['user_id'];
				foreach (glob("../../../auto_mail/tmp/".$REAL_FILE_NAME) as $filename) {			
					@unlink($filename);
				}
				$att_file_arr=array();
				require('../../../ext_resource/mpdf60/mpdf.php');
				$mpdf = new mPDF('', 'A4-L', '', '', 10, 10, 10, 20, 3, 3);	
				$mpdf->WriteHTML($html,2);
				//$REAL_FILE_NAME = 'trims_booking_multy_job_'.$user_id.'.pdf';
				$file_path='../../../auto_mail/tmp/'.$REAL_FILE_NAME;
				$mpdf->Output($file_path, 'F');
				$att_file_arr[]='../../../auto_mail/tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
				//..............................................pdf att file;
				
				//echo $html;die;			
				
				
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
	  	<td width="100"><input class="text_boxes" type="text" style="width:100px;"  name="trims_<? echo $i; ?>" id="trims_<? echo $i; ?>" value="<? echo $dtm_arr[$row[csf('id')]][$row[csf('fabric_color_id')]] ?>" onDblClick="trims_popup('fabric_requisition_controller.php?action=trims_popup','Trims Item',<? echo $i ?>)" readonly/></td>
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

if($action=="print_booking") //ISD-21-22116 4H
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





	$sqlBookDtls="Select a.job_no,a.id as fabric_cost_dtls_id , a.id, a.item_number_id, a.body_part_id, a.fabric_description, a.uom, b.gsm_weight, b.dia_width, b.color_type, b.gmts_color_id, b.fabric_color_id, b.fin_fab_qnty,b.grey_fab_qnty, b.rate, b.amount, b.remark from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no=$txt_booking_no  and a.body_part_type not in  (40,50) and b.status_active =1 and b.is_deleted=0";

	
	
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

	$sqlBookDtls="Select a.job_no,a.id as fabric_cost_dtls_id , a.id, a.item_number_id, a.body_part_id, a.fabric_description, a.uom, b.gsm_weight, b.dia_width, b.color_type, b.gmts_color_id, b.fabric_color_id, b.fin_fab_qnty,b.grey_fab_qnty, b.rate, b.amount, b.remark from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no=$txt_booking_no  and a.body_part_type not in  (40,50) and b.status_active =1 and b.is_deleted=0";	
	
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
?>