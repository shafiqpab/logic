<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Garments Fabric Booking
Functionality	         :
JS Functions	         :
Created by		         :	Zakaria Joy
Creation date 	         : 	05-02-2023
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
function load_drop_down_supplier($data){
	$data=explode("_",$data);
	$pay_mode_id=$data[0];
	$tag_buyer_id=$data[1];
	$tag_comp_id=$data[2];
	if($pay_mode_id==5 || $pay_mode_id==3){
	   echo create_drop_down( "cbo_supplier_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-Select Company-", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/multi_job_additional_fabric_booking_controller');",0,"" );
	}
	else
	{
		$tag_buyer=return_field_value("tag_buyer as tag_buyer", "lib_supplier_tag_buyer", "tag_buyer=$tag_buyer_id","tag_buyer");
		if($tag_buyer!='')
		{
			$tag_by_buyer=sql_select("SELECT supplier_id from lib_supplier_tag_buyer where tag_buyer = $tag_buyer_id group by supplier_id");
			foreach ($tag_by_buyer as $row) {
				$supplier_arr2[$row[csf('supplier_id')]] = $row[csf('supplier_id')];
			}
			$supplier_string2=implode(',', $supplier_arr2);
			$tag_another_buyer=sql_select("SELECT supplier_id from lib_supplier_tag_buyer where tag_buyer != $tag_buyer_id and supplier_id not in ($supplier_string2) group by supplier_id");
			foreach ($tag_another_buyer as $row) {
				$supplier_arr[$row[csf('supplier_id')]] = $row[csf('supplier_id')];
			}
			function where_con_not_in_using_array($arrayData,$dataType=0,$table_coloum){
				$chunk_list_arr=array_chunk($arrayData,999);
				$p=1;
				foreach($chunk_list_arr as $process_arr)
				{
					if($dataType==0){
						if($p==1){$sql .=" and (".$table_coloum." not in(".implode(',',$process_arr).")"; }
						else {$sql .=" or ".$table_coloum." not in(".implode(',',$process_arr).")";}
					}
					else{
						if($p==1){$sql .=" and (".$table_coloum." not in('".implode("','",$process_arr)."')"; }
						else {$sql .=" or ".$table_coloum." not in('".implode("','",$process_arr)."')";}
					}
					$p++;
				}
				
				$sql.=") ";
				return $sql;
			}
			$supplier_string='';
			if(count($supplier_arr))
			{
				$supplier_string=where_con_not_in_using_array($supplier_arr,0,"c.id");
			}
			$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0 $supplier_string group by c.id, c.supplier_name order by c.supplier_name";
		}
		else
		{
			$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name";
		}
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 120, $tag_buy_supp,"id,supplier_name", 1, "-Select Supplier-",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/multi_job_additional_fabric_booking_controller');","");
	}
	return $cbo_supplier_name;
	exit();
}

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/multi_job_additional_fabric_booking_controller', this.value, 'load_drop_down_brand', 'brand_td');","","" );
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
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'multi_job_additional_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );",1);
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
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'multi_job_additional_fabric_booking_controller','setFilterGrid(\'list_view\',-1)');" style="width:100px;" /></td>
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
		load_drop_down('multi_job_additional_fabric_booking_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_popup', 'buyer_td' );
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
	$sql_po= sql_select("select a.booking_no, a.po_break_down_id, a.job_no from wo_booking_mst a where $company $buyer $booking_date and a.booking_type=8 and a.is_short=2 and a.entry_form=608 and a.status_active=1 and a.is_deleted=0 order by a.booking_no");
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
		where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=8 and a.entry_form=608 and  a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and $company $buyer $booking_date $booking_cond $style_cond $ref_cond $order_cond $job_cond
		group by a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.job_no_prefix_num, c.gmts_item_id, c.style_ref_no, d.po_number, d.grouping, d.file_no order by id DESC";
	}
	else
	{
		 $sql="select min(a.id) as id, a.job_no as job_no_prefix_num, a.booking_no_prefix_num, a.pay_mode, a.booking_no, company_id, a.buyer_id, a.supplier_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.is_approved
		 from wo_booking_mst a
		 where a.booking_no not in ( select a.booking_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.booking_type=8 and a.entry_form=608 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and $company $buyer $booking_date $booking_cond $job_cond $file_no_cond $internal_ref_cond group by a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date) and a.booking_type=8 and a.entry_form=608 and a.status_active =1 and a.is_deleted=0 and $company $buyer $supplier_id $booking_date $booking_cond
		 group by a.booking_no_prefix_num, a.booking_no, a.job_no, company_id, a.buyer_id, a.supplier_id, a.pay_mode, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.is_approved order by id DESC";
	}
	?>
    <div>
        <table width="1160" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
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
	$sql= "select id, booking_no, company_id, buyer_id, item_category, uom, fabric_source, booking_date, pay_mode, supplier_id, currency_id, exchange_rate, delivery_date, source, attention, delivery_address, tenor, item_from_precost, cbo_level, ready_to_approved, remarks from wo_booking_mst where booking_no='$data' and status_active =1 and is_deleted=0 and booking_type=8 and entry_form=608";

	$data_array=sql_select($sql);
	foreach ($data_array as $row){
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/multi_job_additional_fabric_booking_controller' );\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbouom').value = '".$row[csf("uom")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		$paymodeData=$row[csf("pay_mode")].'_'.$row[csf("buyer_id")].'_'.$row[csf("company_id")];
		echo "load_drop_down( 'requires/multi_job_additional_fabric_booking_controller', '".$paymodeData."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		if($row[csf("delivery_address")]!="")
		{
		$d_address=preg_replace('/\s+/', ' ', trim($row[csf("delivery_address")]))."";
		}
		echo "document.getElementById('txtdelivery_address').value = '".($d_address)."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('cbo_item_from').value = '".$row[csf("item_from_precost")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_item_from').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		
		//echo "fnc_show_booking_list();\n";

		if($row[csf("is_approved")]==1)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		elseif($row[csf("is_approved")]==3)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		else
		{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
		}
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
                    			$onchange_company="load_drop_down( 'multi_job_additional_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );";
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
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<?=$cbo_brand_id; ?>'+'_'+'<?=$cbo_item_from; ?>', 'fabric_search_list_view', 'search_div', 'multi_job_additional_fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:70px;" />
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
		
		var page_link='multi_job_additional_fabric_booking_controller.php?action=job_search_popup';
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
				show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+document.getElementById('cbo_string_search_type').value, 'fabric_search_list_view', 'search_div', 'multi_job_additional_fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)');
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
	$item_from=$data[16];
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

	if($item_from==1){
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
		$sql='SELECT b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id",b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id" ,a.id AS "booking_id",a.fin_fab_qnty AS "fin_fab_qnty",a.grey_fab_qnty AS "grey_fab_qnty",a.dia_width AS "dia_width" from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_details_master c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and a.job_no=b.job_no and a.job_no=c.job_no and  c.job_no_prefix_num ='.$job.' and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, a.id, a.fin_fab_qnty, a.grey_fab_qnty, a.dia_width';
		$dataArray=sql_select($sql);
		foreach($dataArray as $dataArray_row){
			$cu_booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
		}
		unset($dataArray);
		$sql= 'SELECT a.job_no AS "job_no", b.id AS "id", b.po_number AS "po_number", b.is_confirmed as "is_confirmed", c.item_number_id AS "item_number_id", d.id AS "pre_cost_dtls_id", d.body_part_id AS "body_part_id", d.construction AS "construction", d.composition AS "composition", d.fab_nature_id AS "fab_nature_id", d.fabric_source AS "fabric_source", d.color_type_id AS "color_type_id", d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id", d.uom AS "uom", d.gsm_weight AS "gsm_weight", min(e.id) AS "eid" from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$company_cond . $buyer_cond. $year_cond. $job_cond. $internal_ref_cond. $file_no_cond . $style_cond. $order_cond. $shipment_date. $currency_cond. $fabric_natu_cond. $uom_cond. $fabric_source_cond.$brandCond ." group by a.job_no, b.id, b.po_number, b.is_confirmed, c.item_number_id, d.id, d.color_type_id, d.body_part_id, d.construction, d.composition, d.fab_nature_id, d.fabric_source, d.lib_yarn_count_deter_id, d.uom, d.gsm_weight";
	}
	if($item_from==2){
		$sql= 'SELECT a.job_no AS "job_no", b.id AS "id", b.po_number AS "po_number", b.is_confirmed as "is_confirmed", c.item_number_id AS "item_number_id", min(c.id) AS "eid" from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$company_cond . $buyer_cond. $year_cond. $job_cond. $internal_ref_cond. $file_no_cond . $style_cond. $order_cond. $shipment_date. $currency_cond. " group by a.job_no, b.id, b.po_number, b.is_confirmed, c.item_number_id";
	}
	
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
				<?if($item_from==1){?>
                <th width="100">Body Part</th>
                <th width="100">Color Type</th>                 
                <th width="100">Construction</th>
                <th width="180">Composition</th>
                <th width="70">Gsm</th>
                <th width="80">Fabric Nature</th>
                <th width="70">Fabric Soutce</th>
                <th>UOM</th>
				<? }?>
            </thead>
     	</table>
     </div>
     <div style="width:1150px; max-height:270px;overflow-y:scroll;" >
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" id="list_view">
    <?
	$i=1;
	if(count($sql_data)>0){
		foreach($sql_data as $sql_row){
			if($item_from==1){
				$reqQty=0;
				if($cbo_fabric_natu==2){
					$reqQty=$req_qty_arr['knit']['grey'][$sql_row['id']][$sql_row['pre_cost_dtls_id']][$sql_row['uom']];
				}
				if($cbo_fabric_natu==3){
					$reqQty=$req_qty_arr['woven']['grey'][$sql_row['id']][$sql_row['pre_cost_dtls_id']][$sql_row['uom']];
				}

				$cuBooking=$cu_booking_data_arr[$sql_row['pre_cost_dtls_id']][$sql_row['id']];
				$balQty=number_format($reqQty-$cuBooking,4,".","");				
			}			
			if($item_from==2){
				$balQty=1;
			}
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
					<?if($item_from==1){?>
					<td width="100" style="word-break:break-all"><?=$body_part[$sql_row['body_part_id']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$color_type[$sql_row['color_type_id']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$sql_row['construction']; ?></td>
					<td width="180" style="word-break:break-all"><?=$sql_row['composition']; ?></td>
					<td width="70" style="word-break:break-all"><?=$sql_row['gsm_weight']; ?></td>
					<td width="80" style="word-break:break-all"><?=$item_category[$sql_row['fab_nature_id']]; ?></td>
					<td width="70" style="word-break:break-all"><?=$fabric_source[$sql_row['fabric_source']]; ?></td>
					<td><?=$unit_of_measurement[$sql_row['uom']]; ?></td>
					<? } ?>
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
						//load_drop_down( 'multi_job_additional_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value, 'create_job_search_list_view', 'search_div', 'multi_job_additional_fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
                        <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'multi_job_additional_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );","1"); ?>
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value, 'create_po_search_list_view', 'search_div', 'multi_job_additional_fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
	$cbo_item_from=str_replace("'","",$cbo_item_from);
	$fabric_cost_dtls_id=implode(",",array_unique(explode(",",str_replace("'","",$cbo_fabric_description))));
	$cbouom=str_replace("'","",$cbouom); 
	
	$poidCond=implode(",",$poIdChkArr);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
		
	if($cbo_item_from==1){	
		$cu_booking_data_arr=array();
		$sql='select b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id", a.id AS "booking_id", a.fin_fab_qnty AS "fin_fab_qnty", a.grey_fab_qnty AS "grey_fab_qnty", a.dia_width AS "dia_width", a.pre_cost_remarks AS "pre_cost_remarks" from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and  a.po_break_down_id in('.$txt_order_no_id.') and b.cons>0  and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, a.id, a.fin_fab_qnty, a.grey_fab_qnty, a.dia_width, a.pre_cost_remarks';
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
		
		$sql='SELECT a.id AS "id", a.job_no AS "job_no", a.uom AS "uom", a.item_number_id AS "item_number_id", a.body_part_id AS "body_part_id", a.color_type_id AS "color_type_id", a.width_dia_type AS "width_dia_type", a.construction AS "construction", a.composition AS "composition", a.gsm_weight AS "gsm_weight", a.costing_per AS "costing_per", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id", b.dia_width AS "dia_width", b.remarks AS "remarks", c.contrast_color_id AS "contrast_color_id", a.color_size_sensitive AS "color_size_sensitive", d.hs_code as "hs_code" 
		
		from wo_pre_cost_fabric_cost_dtls a join wo_pre_cos_fab_co_avg_con_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join lib_yarn_count_determina_mst d on d.id=a.lib_yarn_count_deter_id left join wo_pre_cos_fab_co_color_dtls c on  b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and b.color_number_id=c.gmts_color_id   where a.id in('.$fabric_cost_dtls_id.') and b.po_break_down_id in ('.$txt_order_no_id.') and b.cons>0  and a.is_deleted=0 and a.status_active=1 and  d.is_deleted=0 and d.status_active=1 group by a.id, a.job_no, a.uom, a.item_number_id, a.body_part_id, a.color_type_id, a.width_dia_type, a.construction, a.composition, a.gsm_weight, a.costing_per, b.po_break_down_id, b.color_number_id, b.dia_width, b.remarks, c.contrast_color_id, a.color_size_sensitive, d.hs_code';
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
		}
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
				<th width="80">WO. Qty</th>
				<th width="70">Adj. Qty</th>
				<th width="80">Ac.WO. Qty</th>
				<th width="50">UOM</th>
				<th width="60">Rate</th>
				<th width="100">Amount</th>
				<th>Remark</th>
			</thead>
		</table>
		<table width="1750" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
			<tbody>
			<?
			if(str_replace("'","",$cbo_level)==1){
				$i=1;
				foreach($sql_data as $sql_row){
					$bom_fabric_dtls_id=$sql_row['id'];
					$job_no=$sql_row['job_no'];
					$po_break_down_id=$sql_row['po_break_down_id'];
					$body_part_id=$sql_row['body_part_id'];
					$construction=$sql_row['construction'];
					$compositi=$sql_row['composition'];
					$gsm_weight=$sql_row['gsm_weight'];
					$color_type_id=$sql_row['color_type_id'];
					$width_dia_type=$sql_row['width_dia_type'];
					$hscode=$sql_row['hs_code'];
					
					$color_number_id=$sql_row['color_number_id'];
					if($sql_row['color_size_sensitive'] == 3) $item_color=$sql_row['contrast_color_id']; else $item_color=0;		
					if($item_color== "" || $item_color==0) $item_color=$sql_row['color_number_id'];
					
					$dia_width=$sql_row['dia_width'];
					$pre_cost_remarks=$sql_row['remarks'];
					
					if($cbo_fabric_natu==2){
						$req_qty=$req_qty_arr['knit']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$finishreq_qty=$req_qty_arr['knit']['finish'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$req_amt=$req_amount_arr['knit']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$rate=$req_amt/$req_qty;
					}
					if($cbo_fabric_natu==3){
						$req_qty=$req_qty_arr['woven']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$finishreq_qty=$req_qty_arr['woven']['finish'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$req_amt=$req_amount_arr['woven']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
						$rate=$req_amt/$req_qty;
					}
					$cu_booking_qty=$cu_booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$sql_row['po_break_down_id']];
					$bal_qty=$req_qty-$cu_booking_qty;
					$bal_amt=$bal_qty*$rate;
					
					$costing_per=0;
					if($sql_row["costing_per"]==1) $costing_per=12;
					else if($sql_row["costing_per"]==2) $costing_per=1;
					else if($sql_row["costing_per"]==3) $costing_per=24;
					else if($sql_row["costing_per"]==4) $costing_per=36;
					else if($sql_row["costing_per"]==5) $costing_per=48;
					else $costing_per=0;
					
					$itempoWiseReqQty=0;
						
					$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$bom_fabric_dtls_id][$sql_row['po_break_down_id']]['greyreqqty']/($popaln_cut_qnty_array[$sql_row['po_break_down_id']]/$item_ratio_array[$sql_row['item_number_id']]))*$costing_per;
					
					if(number_format($bal_qty,4,'.','') >0){
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
							<td width="80" style="word-break:break-all"><?=$job_no; ?>
								<input type="hidden" id="txtjob_<?=$i;?>" value="<?=$job_no;?>" readonly />
							</td>
							<td width="70" style="word-break: break-all;word-wrap: break-word;"><?=$po_number_arr[$po_break_down_id]; ?>
								<input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$po_break_down_id;?>"  readonly  />
							</td>
							<td width="100" style="word-break:break-all"><?=$body_part[$body_part_id]; ?>
								<input type="hidden" id="txtpre_cost_fabric_cost_dtls_id_<? echo $i;?>" value="<? echo $bom_fabric_dtls_id;?>"  readonly  />
								<input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $body_part_id;?>"  readonly  />
							</td>
							<td width="80" style="word-break:break-all"><?=$color_type[$color_type_id]; ?>
								<input type="hidden" id="txtcolortype_<? echo $i;?>" value="<? echo $color_type_id;?>"  readonly  />
							</td>
							<td width="80" style="word-break:break-all"><?=$fabric_typee[$width_dia_type]; ?>
								<input type="hidden" id="txtwidthtype_<? echo $i;?>" value="<? echo $width_dia_type;?>"  readonly  />
							</td>
							<td width="100" style="word-break:break-all"><?=$construction; ?>
								<input type="hidden" id="txtconstruction_<? echo $i;?>" value="<? echo $construction;?>"  readonly  />
							</td>
							<td width="150" style="word-break:break-all"><?=$compositi; ?>
								<input type="hidden" id="txtcompositi_<? echo $i;?>" value="<? echo $compositi;?>"  readonly  />
							</td>
							<td width="60" style="word-break:break-all"><?=$gsm_weight; ?>
								<input type="hidden" id="txtgsm_weight_<? echo $i;?>" value="<? echo $gsm_weight;?>"  readonly  />
							</td>
							<td width="90" style="word-break:break-all"><?=$color_library[$color_number_id]; ?>
								<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $color_number_id;?>"  readonly  />
							</td>
							<td width="110" style="word-break:break-all"><?=$color_library[$item_color]; ?>
								<input type="hidden" id="txtitemcolor_<? echo $i;?>" value="<? echo $item_color;?>"  readonly  />
							</td>
							<td width="60" style="word-break:break-all"><?=$dia_width; ?>
								<input type="hidden" id="txtdia_<? echo $i;?>" value="<? echo $dia_width;?>"  readonly  />
							</td>
							<td width="80">
								<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txthscode_<?=$i;?>" value="<?=$hscode;?>" />
							</td>
							<td width="80" style="word-break:break-all"><?=$pre_cost_remarks; ?>
								<input type="hidden" id="process_<? echo $i;?>" value="<? echo $pre_cost_remarks;?>"  readonly  />
							</td>
							<td width="80">	
								<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalqnty_<?=$i;?>" value="<?=number_format($bal_qty,4,'.','');?>" readonly />
								<input type="hidden" id="txtreqqnty_<?=$i;?>" value="<?=number_format($req_qty,4,'.','');?>"  readonly  />
								<input type="hidden" id="txtfinreqqnty_<?=$i;?>" value="<?=number_format($finishreq_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
								<input type="hidden" id="cuqnty_<?=$i;?>" value="<?=number_format($cu_booking_qty,4,'.','');?>"  readonly  />
								<input type="hidden" id="preconskg_<?=$i; ?>" style="width:20px;" value="<?=$itempoWiseReqQty; ?>"/>
							</td>
							<td width="80">
								<input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" onChange="claculate_acwoQty(<? echo $i; ?>)" />
								<input type="hidden"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
							</td>
							<td width="70">
								<input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<? echo $i;?>" value="<? //echo number_format($adj_qty,4,'.','');?>" class="text_boxes_numeric"  onChange="claculate_acwoQty(<? echo $i; ?>)"  />
							</td>
							<td width="80">
								<input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric"  readonly />
							</td>
							<td width="50" align="center"><? echo $unit_of_measurement[$sql_row['uom']]; ?></td>
							<td width="60">
								<input type="text"  style="width:70%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_amount(<? echo $i; ?>)" data-pre-cost-rate="<? echo number_format($rate,4,'.','');?>" data-current-rate="<? echo number_format($rate,4,'.','');?>"  />
							</td>
							<td width="100">
								<input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($bal_amt,4,'.','');?>" class="text_boxes_numeric" readonly  />
							</td>
							<td>
								<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value="" />
								<input type="hidden"   id="bookingid_<? echo $i;?>" value=""  readonly  />
							</td>
						</tr>
						<?
						$i++;
					}
				}
			}
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
										<td width="60"><input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_amount(<? echo $i; ?>)" data-pre-cost-rate="<? echo number_format($rate,4,'.','');?>" data-current-rate="<? echo number_format($rate,4,'.','');?>"  /></td>
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
	if($cbo_item_from==2){
		$sql_data=sql_select("SELECT a.job_no, b.id as po_id, b.po_number, c.color_number_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id in(".$txt_order_no_id.") group by a.job_no, b.po_number, c.color_number_id, b.id");
		?>
		<table width="1750" class="rpt_table" border="0" rules="all">
			<thead>
				<th width="80">Job No</th>
				<th width="70">Po No</th>
				<th width="100" style="color:blue">Body Part</th>
				<th width="80" style="color:blue">Color Type</th>
				<th width="80" style="color:blue">Dia Width Type</th>
				<th width="100" style="color:blue">Construction</th>
				<th width="150" style="color:blue">Composition</th>
				<th width="60">Gsm</th>
				<th width="90" style="color:blue">Gmts. Color</th>
				<th width="110" style="color:blue">Item Color</th>
				<th width="60">Dia</th>
				<th width="80">HS Code</th>
				<th width="80">Process</th>
				<th width="80">Balance Qty</th>
				<th width="80">WO. Qty</th>
				<th width="70">Adj. Qty</th>
				<th width="80">Ac.WO. Qty</th>
				<th width="50">UOM</th>
				<th width="60">Rate</th>
				<th width="100">Amount</th>
				<th>Remark</th>
			</thead>
		</table>
		<table width="1750" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
			<tbody>
				<?
				$i=1;
				foreach($sql_data as $row){
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
						<td width="80" style="word-break:break-all"><?= $row[csf('job_no')]; ?>
							<input type="hidden" id="txtjob_<?=$i;?>" value="<?=$row[csf('job_no')];?>" readonly />
						</td>
						<td width="70" style="word-break: break-all;word-wrap: break-word;"><?= $row[csf('po_number')]; ?>
							<input type="hidden" id="txtpoid_<?=$i;?>" value="<?= $row[csf('po_id')];?>"  readonly  />
						</td>
						<td width="100" style="word-break:break-all"><input type="text" id="txtbodyparttext_<?=$i; ?>" name="txtbodyparttext_<?=$i; ?>" class="text_boxes" style="width:88px" placeholder="Double Click" onDblClick="open_body_part_popup(<?=$i; ?>);" value="" readonly/>
							
							<input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $body_part_id;?>"  readonly  />
						</td>
						<td width="80" style="word-break:break-all"><?=create_drop_down( "txtcolortype_".$i, 80, $color_type,"", 1, "-- Select --", "", "",$disabled,"" ); ?>
						</td>
						<td width="80" style="word-break:break-all"><? asort($fabric_typee); echo create_drop_down( "txtwidthtype_".$i, 80, $fabric_typee,"", 1, "-- Select --", $row[csf("width_dia_type")], "",$disabled,"" ); ?>
						</td>
						<td width="100" style="word-break:break-all">
							<input type="text" id="txtconstruction_<?=$i; ?>" name="txtconstruction_<?=$i; ?>" class="text_boxes" style="width:88px; background-color:<?=$changedmsgcolor; ?>" placeholder="Double Click" onDblClick="open_fabric_decription_popup(<?=$i; ?>);" value="" readonly/>
						</td>
						<td width="150" style="word-break:break-all">
							<input type="text" id="txtcompositi_<?=$i; ?>" name="txtcompositi_<?=$i; ?>" class="text_boxes" style="width:138px;" onDblClick="open_fabric_decription_popup(<?=$i; ?>);" placeholder="Double Click" value="" readonly/>
							<input type="hidden" id="lib_yarn_id_<? echo $i;?>" value=""  readonly  />
						</td>
						<td width="60" style="word-break:break-all">
							<input type="text" id="txtgsm_weight_<? echo $i;?>" class="text_boxes" style="width:48px;"  value=""/>
						</td>
						<td width="90" style="word-break:break-all"><?= $color_library[$row[csf('color_number_id')]]; ?>
							<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $row[csf('color_number_id')];?>"  readonly  />
						</td>
						<td width="110" style="word-break:break-all">
							<input type="text" id="txtitemcolor_<? echo $i;?>" class="text_boxes" style="width:98px;" value="" />
						</td>
						<td width="60" style="word-break:break-all">
							<input type="text" id="txtdia_<? echo $i;?>" class="text_boxes" style="width:48px;" value=""   />	
						</td>
						<td width="80">
							<input type="text" class="text_boxes" style="width:68px; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right;" id="txthscode_<?=$i;?>" value="" />
						</td>
						<td width="80" style="word-break:break-all">
							<input type="text" class="text_boxes" style="width:68px;" id="process_<? echo $i;?>" value="<? echo $pre_cost_remarks;?>"  readonly  />
						</td>
						<td width="80">	
							<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalqnty_<?=$i;?>" value="0" readonly />
							<input type="hidden" id="txtreqqnty_<?=$i;?>" value="<?=number_format($req_qty,4,'.','');?>"  readonly  />
							<input type="hidden" id="txtfinreqqnty_<?=$i;?>" value="<?=number_format($finishreq_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
							<input type="hidden" id="cuqnty_<?=$i;?>" value="<?=number_format($cu_booking_qty,4,'.','');?>"  readonly  />
							<input type="hidden" id="preconskg_<?=$i; ?>" style="width:20px;" value="<?=$itempoWiseReqQty; ?>"/>
						</td>
						<td width="80">
							<input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" onChange="claculate_acwoQty(<? echo $i; ?>)" />
							<input type="hidden"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
						</td>
						<td width="70">
							<input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<? echo $i;?>" value="<? //echo number_format($adj_qty,4,'.','');?>" class="text_boxes_numeric"  onChange="claculate_acwoQty(<? echo $i; ?>)"  />
						</td>
						<td width="80">
							<input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<? echo $i;?>" value="<? echo number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric"  readonly />
						</td>
						<td width="50" align="center"><?=create_drop_down( "uom_".$i, 50, $unit_of_measurement,'', 1, '-select-', $uom, "",$disabled,"1,12,23,27" ); ?></td>
						<td width="60">
							<input type="text"  style="width:70%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_amount(<? echo $i; ?>)" data-pre-cost-rate="<? echo number_format($rate,4,'.','');?>" data-current-rate="<? echo number_format($rate,4,'.','');?>"  />
						</td>
						<td width="100">
							<input type="text"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($bal_amt,4,'.','');?>" class="text_boxes_numeric" readonly  />
						</td>
						<td>
							<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value="" />
							<input type="hidden"   id="bookingid_<? echo $i;?>" value=""  readonly  />
						</td>
					</tr>
					<?
					$i++;
				}

				?>
			</tbody>
		</table>
		<?
	}
}

if ($action=="show_fabric_booking"){
	echo load_html_head_contents("Partial Booking Details","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$cbo_item_from=str_replace("'","",$cbo_item_from);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$fabric_cost_dtls_id=implode(",",array_unique(explode(",",str_replace("'","",$cbo_fabric_description))));

	$cbouom=str_replace("'","",$cbouom);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	if($cbo_item_from==1){
		$cu_booking_data_arr=array();
		$sql_cu='SELECT a.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", a.po_break_down_id AS "po_break_down_id", a.gmts_color_id AS "color_number_id", a.id AS "booking_id", a.fin_fab_qnty AS "fin_fab_qnty", a.grey_fab_qnty AS "grey_fab_qnty", a.adjust_qty AS "adjust_qty", a.dia_width AS "dia_width", a.pre_cost_remarks AS "pre_cost_remarks" from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id   and  a.po_break_down_id in('.$txt_order_no_id.')   and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ';
		$dataArray_cu=sql_select($sql_cu);
		foreach($dataArray_cu as $dataArray_row){
			$cu_booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['cu_booking_qty'][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
		}
		unset($dataArray_cu);
		
		$companyId=return_field_value("company_id", "wo_booking_mst", "booking_no=".$txt_booking_no." and status_active=1 and is_deleted=0");
		
		$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name='$companyId' and variable_list=66 and status_active=1");

		if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;

		$booking_data_arr=array();
		$sql_pre='select a.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", a.po_break_down_id AS "po_break_down_id", a.gmts_color_id AS "color_number_id", a.id AS "booking_id", a.fin_fab_qnty AS "fin_fab_qnty", a.grey_fab_qnty AS "grey_fab_qnty", a.rate AS "rate", a.amount AS "amount", a.adjust_qty AS "adjust_qty", a.remark AS "remark", a.dia_width AS "dia_width", a.pre_cost_remarks AS "pre_cost_remarks", a.hs_code as "hs_code" from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in('.$txt_order_no_id.')  and a.booking_no='.$txt_booking_no.' and a.booking_type=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0';
		//echo $sql_pre; die;
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
	}
	if($cbo_item_from==2){
		$library_booking_data=sql_select("SELECT a.id as po_id, a.po_number, a.job_no_mst, c.id as booking_id, c.fabric_color_id, c.fin_fab_qnty, c.rate, c.amount, c.color_type, c.construction, c.copmposition, c.gsm_weight, c.dia_width, c.gmts_color_id, c.pre_cost_remarks, c.hs_code, c.lib_yarn_id, c.booking_mst_id, c.body_part, c.uom, c.grey_fab_qnty, c.adjust_qty, c.process, c.width_dia_type from wo_po_break_down a join wo_booking_dtls c on a.id=c.po_break_down_id where c.booking_no=$txt_booking_no and a.id=$txt_order_no_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.lib_yarn_id in ($fabric_cost_dtls_id)");
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
				<? if($cbo_item_from==1){ ?>
				<th width="90">Item Ref.</th>
				<? } ?>
                <th width="60">Dia</th>
                <th width="80">Process</th>
                <th width="80">HS Code</th>
                <th width="80">Balance Qty</th>
                <th width="80">WO. Qty</th>
                <th width="70">Adj. Qty</th>
                <th width="80">Ac.WO. Qty</th>
                <th width="50">UOM</th>
                <th width="60">Rate</th>
                <th width="100">Amount</th>
                <th>Remark</th>
            </thead>
        </table>
        <table width="1840" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
            <tbody>
            <?
			if($cbo_item_from==1){
				if(str_replace("'","",$cbo_level)==1)
				{
					$i=1;
					foreach($sql_data as $sql_row)
					{
						$bom_fabric_dtls_id=$sql_row['id'];
						$job_no=$sql_row['job_no'];
						$po_break_down_id=$sql_row['po_break_down_id'];
						$body_part_id=$sql_row['body_part_id'];
						$construction=$sql_row['construction'];
						$compositi=$sql_row['composition'];
						$gsm_weight=$sql_row['gsm_weight'];
						$color_type_id=$sql_row['color_type_id'];
						$width_dia_type=$sql_row['width_dia_type'];
						$itrm_ref=$sql_row['itrm_ref'];
						
						
						$color_number_id=$sql_row['color_number_id'];
						$item_color=$sql_row['contrast_color_id'];
						if($item_color== "" || $item_color==0) $item_color=$sql_row['color_number_id'];
						
						$dia_width=$sql_row['dia_width'];
						$pre_cost_remarks=$sql_row['remarks'];
						
						
						if($cbo_fabric_natu==2)
						{
							$req_qty=$req_qty_arr['knit']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							$finishreq_qty=$req_qty_arr['knit']['finish'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							$req_amt=$req_amount_arr['knit']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							//$rate=$req_amt/$req_qty;
						}
						if($cbo_fabric_natu==3)
						{
							$req_qty=$req_qty_arr['woven']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							$finishreq_qty=$req_qty_arr['woven']['finish'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							$req_amt=$req_amount_arr['woven']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
							//$rate=$req_amt/$req_qty;
						}
						$cu_booking_qty=$cu_booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$sql_row['po_break_down_id']];
						$booking_qty=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_qty'][$sql_row['po_break_down_id']];
						$ac_booking_qty=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['ac_booking_qty'][$sql_row['po_break_down_id']];
						$adjust_qty=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['adjust_qty'][$sql_row['po_break_down_id']];
						$remark=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['remark'][$sql_row['po_break_down_id']];
						$hscode=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['hscode'][$sql_row['po_break_down_id']];
						if($remark=="") $remark=$pre_cost_remarks;
						$rate=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$sql_row['po_break_down_id']];
						$amount=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['amount'][$sql_row['po_break_down_id']];
						$booking_id=$booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['booking_id'][$sql_row['po_break_down_id']];
						$rate=$amount/$ac_booking_qty;
						$bal_qty=$req_qty-$cu_booking_qty;
						$bal_amt=$bal_qty-$amount;
						$pre_cost_rate=$req_amt/$req_qty;
						
						$costing_per=0;
						if($sql_row["costing_per"]==1) $costing_per=12;
						else if($sql_row["costing_per"]==2) $costing_per=1;
						else if($sql_row["costing_per"]==3) $costing_per=24;
						else if($sql_row["costing_per"]==4) $costing_per=36;
						else if($sql_row["costing_per"]==5) $costing_per=48;
						else $costing_per=0;
						
						$itempoWiseReqQty=0;
							
						$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$bom_fabric_dtls_id][$sql_row['po_break_down_id']]['greyreqqty']/($popaln_cut_qnty_array[$sql_row['po_break_down_id']]/$item_ratio_array[$sql_row['item_number_id']]))*$costing_per;
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
							<td width="80" style="word-break:break-all"><?=$job_no; ?><input type="hidden" id="txtjob_<?=$i;?>" value="<?=$job_no;?>" readonly /></td>
							<td width="70" style="word-break: break-all;word-wrap: break-word;"><?=$po_number_arr[$po_break_down_id]; ?>
								<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_break_down_id;?>"  readonly  />
							</td>
							<td width="100" style="word-break:break-all"><?=$body_part[$body_part_id]; ?>
								<input type="hidden" id="txtpre_cost_fabric_cost_dtls_id_<? echo $i;?>" value="<? echo $bom_fabric_dtls_id;?>"  readonly  />
								<input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $body_part_id;?>"  readonly  />
							</td>
							<td width="80" style="word-break:break-all"><?=$color_type[$color_type_id]; ?>
								<input type="hidden" id="txtcolortype_<? echo $i;?>" value="<? echo $color_type_id;?>"  readonly  />
							</td>
							<td width="80" style="word-break:break-all"><?=$fabric_typee[$width_dia_type]; ?>
								<input type="hidden" id="txtwidthtype_<? echo $i;?>" value="<? echo $width_dia_type;?>"  readonly  />
							</td>
							<td width="100" style="word-break:break-all"><?=$construction; ?>
								<input type="hidden" id="txtconstruction_<? echo $i;?>" value="<? echo $construction;?>"  readonly  />
							</td>
							<td width="150" style="word-break:break-all"><?=$compositi; ?>
								<input type="hidden" id="txtcompositi_<? echo $i;?>" value="<? echo $compositi;?>"  readonly  />
							</td>
							<td width="60" style="word-break:break-all"><?=$gsm_weight; ?>
								<input type="hidden" id="txtgsm_weight_<? echo $i;?>" value="<? echo $gsm_weight;?>"  readonly  />
							</td>
							<td width="90" style="word-break:break-all"><?=$color_library[$color_number_id]; ?>
								<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $color_number_id;?>"  readonly  />
							</td>
							<td width="110" style="word-break:break-all"><?=$color_library[$item_color]; ?>
								<input type="hidden" id="txtitemcolor_<? echo $i;?>" value="<? echo $item_color;?>"  readonly  />
							</td>
							<td width="90" style="word-break:break-all"><?=$itrm_ref; ?>
								<input type="hidden" id="txtitrmref_<? echo $i;?>" value="<? echo $itrm_ref;?>"  readonly  />
							</td>
							<td width="60" style="word-break:break-all"><?=$dia_width; ?>
								<input type="hidden" id="txtdia_<? echo $i;?>" value="<? echo $dia_width;?>"  readonly  />
							</td>
							<td width="80" style="word-break:break-all"><?=$pre_cost_remarks; ?>
								<input type="hidden" id="process_<? echo $i;?>" value="<? echo $pre_cost_remarks;?>"  readonly  />
							</td>
	
							<td width="80">
								<input type="text"  style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right;" class="text_boxes" id="txthscode_<?=$i;?>" value="<?=$hscode;?>" />
							</td>
							<td width="80">
								<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right;" id="txtbalqnty_<?=$i;?>" value="<?=number_format($bal_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
								<input type="hidden" id="txtreqqnty_<?=$i;?>" value="<?=number_format($req_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
								<input type="hidden" id="txtfinreqqnty_<?=$i;?>" value="<?=number_format($finishreq_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
								<input type="hidden" id="cuqnty_<?=$i;?>" value="<?=number_format($cu_booking_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
								<input type="hidden" id="preconskg_<?=$i; ?>" style="width:20px;" value="<?=$itempoWiseReqQty; ?>"/>
							</td>
							<td width="80">
								<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i;?>" value="<?=number_format($booking_qty,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_acwoQty(<?=$i; ?>)"  />
								<input type="hidden" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($booking_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
							</td>
							<td width="70">
								<input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtadj_<?=$i;?>" value="<?=number_format($adjust_qty,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_acwoQty(<?=$i; ?>)"  />
							</td>
							<td width="80"><input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<?=$i;?>" value="<?=number_format($ac_booking_qty,4,'.','');?>" class="text_boxes_numeric" readonly />
							</td>
							<td width="50" align="center"><?=$unit_of_measurement[$sql_row['uom']]; ?></td>
							<td width="60"><input type="text" style="width:70%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i;?>" value="<?=number_format($rate,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_amount(<?=$i; ?>)" data-pre-cost-rate="<?=number_format($pre_cost_rate,4,'.','');?>" data-current-rate="<?=number_format($rate,4,'.','');?>" /></td>
							<td width="100">
								<input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i;?>" value="<?=number_format($amount,4,'.','');?>" class="text_boxes_numeric" readonly />
							</td>
							<td>
								<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value="<? echo $remark;?>"    />
								<input type="hidden"   id="bookingid_<? echo $i;?>" value="<? echo $booking_id;?>"  readonly  />
							</td>
						</tr>
						<?
						$i++;
					}
				}
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
										<td width="60"><input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i;?>" value="<?=number_format($rate,4,'.','');?>" class="text_boxes_numeric" onChange="claculate_amount(<?=$i; ?>)" data-pre-cost-rate="<?=number_format($pre_cost_rate,4,'.','');?>" data-current-rate="<?=number_format($rate,4,'.','');?>" /></td>
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
			}
			if($cbo_item_from==2){
				$body_part_arr = return_library_array("select id,body_part_full_name from  lib_body_part where status_active=1 and is_deleted=0 and is_emplishment=1 order by body_part_full_name", "id", "body_part_full_name");
				$i=1;
				foreach($library_booking_data as $row){
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
						<td width="80" style="word-break:break-all"><?= $row[csf('job_no_mst')]; ?>
							<input type="hidden" id="txtjob_<?=$i;?>" value="<?=$row[csf('job_no_mst')];?>" readonly />
						</td>
						<td width="70" style="word-break: break-all;word-wrap: break-word;"><?= $row[csf('po_number')]; ?>
							<input type="hidden" id="txtpoid_<?=$i;?>" value="<?= $row[csf('po_id')];?>"  readonly  />
						</td>
						<td width="100" style="word-break:break-all"><input type="text" id="txtbodyparttext_<?=$i; ?>" name="txtbodyparttext_<?=$i; ?>" class="text_boxes" style="width:88px" placeholder="Double Click" onDblClick="open_body_part_popup(<?=$i; ?>);" value="<? echo $body_part_arr[$row[csf('body_part')]];?>" readonly/>							
							<input type="hidden" id="txtbodypart_<? echo $i;?>" value="<? echo $row[csf('body_part')];?>"  readonly  />
						</td>
						<td width="80" style="word-break:break-all"><?= create_drop_down( "txtcolortype_".$i, 80, $color_type,"", 1, "-- Select --", $row[csf('color_type')], "",$disabled,"" ); ?>
						</td>
						<td width="80" style="word-break:break-all"><? asort($fabric_typee); echo create_drop_down( "txtwidthtype_".$i, 80, $fabric_typee,"", 1, "-- Select --", $row[csf("width_dia_type")], "",$disabled,"" ); ?>
						</td>
						<td width="100" style="word-break:break-all">
							<input type="text" id="txtconstruction_<?=$i; ?>" name="txtconstruction_<?=$i; ?>" class="text_boxes" style="width:88px; background-color:<?=$changedmsgcolor; ?>" placeholder="Double Click" onDblClick="open_fabric_decription_popup(<?=$i; ?>);" value="<?= $row[csf("construction")] ?>" readonly/>
						</td>
						<td width="150" style="word-break:break-all">
							<input type="text" id="txtcompositi_<?=$i; ?>" name="txtcompositi_<?=$i; ?>" class="text_boxes" style="width:138px;" onDblClick="open_fabric_decription_popup(<?=$i; ?>);" placeholder="Double Click" value="<?= $row[csf("copmposition")] ?>" readonly/>
							<input type="hidden" id="lib_yarn_id_<? echo $i;?>" value="<?= $row[csf("lib_yarn_id")] ?>"  readonly  />
						</td>
						<td width="60" style="word-break:break-all">
							<input type="text" id="txtgsm_weight_<? echo $i;?>" class="text_boxes" style="width:48px;"  value="<?= $row[csf("gsm_weight")] ?>"/>
						</td>
						<td width="90" style="word-break:break-all"><?= $color_library[$row[csf('gmts_color_id')]]; ?>
							<input type="hidden" id="txtgmtcolor_<? echo $i;?>" value="<? echo $row[csf('gmts_color_id')];?>"  readonly  />
						</td>
						<td width="110" style="word-break:break-all">
							<input type="text" id="txtitemcolor_<? echo $i;?>" class="text_boxes" style="width:98px;" value="<?= $color_library[$row[csf('fabric_color_id')]]; ?>" />
						</td>
						<td width="60" style="word-break:break-all">
							<input type="text" id="txtdia_<? echo $i;?>" class="text_boxes" style="width:48px;" value="<? echo $row[csf('dia_width')];?>"   />	
						</td>
						<td width="80" style="word-break:break-all">
							<input type="text" class="text_boxes" style="width:68px;" id="process_<? echo $i;?>" value="<? echo $row[csf('process')];?>" />
						</td>
						<td width="80">
							<input type="text" class="text_boxes" style="width:68px; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right;" id="txthscode_<?=$i;?>" value="<? echo $row[csf('hs_code')];?>" />
						</td>						
						<td width="80">	
							<input type="text"  class="text_boxes" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalqnty_<?=$i;?>" value="0" readonly />
							<input type="hidden" id="txtreqqnty_<?=$i;?>" value="0"  readonly  />
							<input type="hidden" id="txtfinreqqnty_<?=$i;?>" value="0" class="text_boxes_numeric" readonly />
							<input type="hidden" id="cuqnty_<?=$i;?>" value="0"  readonly  />
							<input type="hidden" id="preconskg_<?=$i; ?>" style="width:20px;" value="0"/>
						</td>
						<td width="80">
							<input type="text"  class="text_boxes" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($row[csf('fin_fab_qnty')],4,'.','');?>" onChange="claculate_acwoQty(<? echo $i; ?>)" />
							<input type="hidden"  style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<? echo $i;?>" value="<? echo number_format($row[csf('fin_fab_qnty')],4,'.','');?>" class="text_boxes_numeric" readonly />
						</td>
						<td width="70">
							<input type="text"   class="text_boxes" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<? echo $i;?>" value="<? echo number_format($row[csf('adjust_qty')],4,'.','');?>" class="text_boxes_numeric"  onChange="claculate_acwoQty(<? echo $i; ?>)"  />
						</td>
						<td width="80">
							<input type="text"   class="text_boxes" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<? echo $i;?>" value="<? echo number_format($row[csf('fin_fab_qnty')],4,'.','');?>" class="text_boxes_numeric"  readonly />
						</td>
						<td width="50" align="center"><?=create_drop_down( "uom_".$i, 50, $unit_of_measurement,'', 1, '-select-', $row[csf('uom')], "",$disabled,"1,12,23,27" ); ?></td>
						<td width="60">
							<input type="text"   class="text_boxes" style="width:70%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($row[csf('rate')],4,'.','');?>"  class="text_boxes_numeric" onChange="claculate_amount(<? echo $i; ?>)"/>
						</td>
						<td width="100">
							<input type="text"   class="text_boxes" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($row[csf('amount')],4,'.','');?>" class="text_boxes_numeric" readonly  />
						</td>
						<td>
							<input type="text"   class="text_boxes" style="width:150px;  background-color:<? echo $bgcolor; ?>" id="txtremark_<? echo $i;?>" value="<? echo $row[csf('pre_cost_remarks')];?>" />
							<input type="hidden"   id="bookingid_<? echo $i;?>" value="<?= $row[csf('booking_id')] ?>"  readonly  />
						</td>
					</tr>
					<?
					$i++;
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
	$cbo_item_from=str_replace("'","",$cbo_item_from);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$pre_cost_fabric_cost_dtls_id=str_replace("'","",$cbo_fabric_description);
	$cbouom=str_replace("'","",$cbouom);
	$cbo_level=str_replace("'","",$cbo_level);
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	if($cbo_item_from==1){
		$companyId=return_field_value("company_id", "wo_booking_mst", "booking_no=".$txt_booking_no." and status_active=1 and is_deleted=0");
	
		$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name='$companyId' and variable_list=66 and status_active=1");

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
			$fabric_description_array[$sql_row['pre_cost_fabric_cost_dtls_id']]=$body_part[$sql_row['body_part_id']].', '.$color_type[$sql_row['color_type_id']].', '.$sql_row['fabric_description'].', '.$sql_row['gsm_weight'].', '.$unit_of_measurement[$sql_row['uom']];
			$color_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['gmts_color_id']]=$color_library[$sql_row['gmts_color_id']];
			$Dia_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['dia_width']]=$sql_row['dia_width'];
		}
	}
	if($cbo_item_from==2){
		$library_booking_data=sql_select("SELECT a.id as po_id, a.po_number, a.job_no_mst, c.id as booking_id, c.fabric_color_id, c.fin_fab_qnty, c.rate, c.amount, c.color_type, c.construction, c.copmposition, c.gsm_weight, c.dia_width, c.gmts_color_id, c.pre_cost_remarks, c.hs_code, c.lib_yarn_id, c.booking_mst_id, c.body_part, c.uom, c.grey_fab_qnty, c.adjust_qty from wo_po_break_down a join wo_booking_dtls c on a.id=c.po_break_down_id where c.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		foreach($library_booking_data as $row){
			$key=$row[csf('po_id')].'*'.$row[csf('lib_yarn_id')];
			$library_booking_dtls[$key]['po_id']=$row[csf('po_id')];
			$library_booking_dtls[$key]['po_number']=$row[csf('po_number')];
			$library_booking_dtls[$key]['lib_yarn_id']=$row[csf('lib_yarn_id')];
			$library_booking_dtls[$key]['booking_id']=$row[csf('booking_id')];
			$library_booking_dtls[$key]['job_no_mst']=$row[csf('job_no_mst')];
			$library_booking_dtls[$key]['body_part']=$row[csf('body_part')];
			$library_booking_dtls[$key]['color_type']=$row[csf('color_type')];
			$library_booking_dtls[$key]['construction']=$row[csf('construction')];
			$library_booking_dtls[$key]['copmposition']=$row[csf('copmposition')];
			$library_booking_dtls[$key]['gsm_weight']=$row[csf('gsm_weight')];
			$library_booking_dtls[$key]['uom']=$row[csf('uom')];
			$library_booking_dtls[$key]['gmts_color_id'][$row[csf('gmts_color_id')]]=$color_library[$row[csf('gmts_color_id')]];
			$library_booking_dtls[$key]['dia_width'][$row[csf('dia_width')]]=$row[csf('dia_width')];
			$library_booking_dtls[$key]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			$library_booking_dtls[$key]['adjust_qty']+=$row[csf('adjust_qty')];
			$library_booking_dtls[$key]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			$library_booking_dtls[$key]['amount']+=$row[csf('amount')];
		}
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
            <th><input type="checkbox" name="chkdeleteall" id="chkdeleteall" value="2" ><a href="#" onClick="deletedata();">Delete All</a></th>
        </thead>
	</table>
	<div style="max-height:200px; overflow-y:scroll; width:1200px"  align="left">
        <table width="1183" class="rpt_table" id="tbl_fabric_booking_list" border="0" rules="all">
            <tbody>
            <? 
			if($cbo_item_from==1){
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
			}
			if($cbo_item_from==2){
				$i=1;
				foreach($library_booking_dtls as $row){					
					$po_break_down_id=$row['po_id'];
					$po_number=$row['po_number'];
					$key=$row['lib_yarn_id'];
					$booking_id=$row['booking_id'];
					$job_no=$row['job_no_mst'];
					$rate=$row['amount']/$row['fin_fab_qnty'];

					$description=$body_part[$row['body_part']].', '.$color_type[$row['color_type']].', '.$row['construction'].', '.$row['copmposition'].','.$row['gsm_weight'].', '.$unit_of_measurement[$row['uom']];
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" >
						<td width="50" align="center"><a href="#" onClick="set_data('<?=$po_break_down_id; ?>','<?=$po_number; ?>','<?=$key; ?>','<?=$booking_id; ?>');">Edit</a></td>
						<td width="80"><?=$job_no; ?></td>
						<td width="80" style="word-break: break-all;word-wrap: break-word;" align="center"><?=$po_number; ?></td>
						<td width="250" style="word-break: break-all;word-wrap: break-word"><?= $description; ?></td>
						<td width="130" style="word-break: break-all;word-wrap: break-word"><?= implode(",",$row['gmts_color_id']); ?></td>
						<td width="70" style="word-break: break-all;word-wrap: break-word" align="center"><?= implode(",",$row['dia_width']); ?></td>
						<td width="100" align="right"><?=number_format($row['grey_fab_qnty'],4,'.','');?></td>
						<td width="100" align="right"><?=number_format($row['adjust_qty'],4,'.','');?></td>
						<td width="100" align="right"><?=number_format($row['fin_fab_qnty'],4,'.',''); ?></td>
						<td width="60" align="right"><?=number_format($rate,4,'.',''); ?></td>
						<td width="80" align="right"><?=number_format($row['amount'],4,'.','');?></td>
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
if ($action=="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)
	{
		$con = connect();
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'AFB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type in(8) and entry_form=608 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1);
		
		$field_array="id, booking_type, entry_form, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, item_category, uom, fabric_source, booking_date, pay_mode, supplier_id, currency_id, exchange_rate, delivery_date, source, attention, delivery_address, tenor, item_from_precost, cbo_level, ready_to_approved, remarks, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array ="(".$id.",8,608,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_fabric_natu.",".$cbouom.",".$cbo_fabric_source.",".$txt_booking_date.",".$cbo_pay_mode.",".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_delivery_date.",".$cbo_source.",".$txt_attention.",".$txtdelivery_address.",".$txt_tenor.",".$cbo_item_from.",".$cbo_level.",".$cbo_ready_to_approved.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		//echo "10**insert into wo_booking_mst (".$field_array.") values ".$data_array; disconnect($con); die;
		
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1){
		$con = connect();
		
		$update_id=str_replace("'","",$update_id);
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3) $is_approved=1;
	
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a, com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
		}
		
		$flag=1;
		
		$field_array_up="uom*fabric_source*booking_date*pay_mode*supplier_id*currency_id*exchange_rate*delivery_date*source*attention*delivery_address*tenor* ready_to_approved*remarks*updated_by*update_date*revised_no";

		$data_array_up =$cbouom."*".$cbo_fabric_source."*".$txt_booking_date."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_delivery_date."*".$cbo_source."*".$txt_attention."*".$txtdelivery_address."*".$txt_tenor."*".$cbo_ready_to_approved."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*revised_no+1";
		if($data_array_up!='')
		{
			$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
			if($rID) $flag=1; else $flag=0;
		}

		//echo "10**".$rID_rec."**".$rID; die;
		if($db_type==2 || $db_type==1 ){
			if($rID==1 && $flag==1){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".$update_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2){
		$con = connect();
		
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}
	
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
		}
		
		
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		//$rID=execute_query( "delete from wo_booking_mst where  booking_no =".$txt_booking_no."",0);
		//$rID1=execute_query( "delete from wo_booking_dtls where  booking_no =".$txt_booking_no."",0);
		//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  booking_no =".$txt_booking_no."",0);
		if(str_replace("'","",$delete_type)==1)
		{
			$rID=execute_query("update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where booking_no=$txt_booking_no",0);
			$rID1=execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0",0);
		}
		else
		{
			$rID=1;
			$rID1=execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0",0);
		}

		if($db_type==2 || $db_type==1 ){
			if($rID  && $rID1){
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
		 $field_array1="id, is_short, booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, entry_form_id,color_type, construction, copmposition, gsm_weight, dia_width, fabric_color_id, gmts_color_id, fin_fab_qnty, grey_fab_qnty, adjust_qty, rate, amount, process_loss_percent, remark, pre_cost_remarks, hs_code, precons, inserted_by, insert_date, status_active, is_deleted";
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
					 $data_array1 .="(".$id_dtls.",2,".$update_id.",".$precostid.",".$$txtpoid_.",".$$txtjob.",".$txt_booking_no.",8,608,".$$txtcolortype_.",".$$txtconstruction_.",".$$txtcompositi_.",".$$txtgsm_weight_.",".$$txtdia_.",".$$txtitemcolor.",".$$txtgmtcolor.",".$finWoQty.",".$greyWoQty.",".$$txtadj.",".$rate.",".$amount.",".$pLossPer.",".$$txtremark.",".$$process.",".$$hscode.",".$$preconskg.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
			 $ex_date=explode("**",$delivery_date);
			 $rID_date=$ex_date[0];
			 if($ex_date[0]==1) $flag=1; else $flag=0;
		 }
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
			$sql_po= sql_select("select b.id as dtlsid, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, b.color_type, b.construction, b.copmposition, b.gsm_weight, b.dia_width from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=8 and a.is_short=2 and a.entry_form=608 and b.booking_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
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
		 $field_array1="id, is_short, booking_mst_id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type,entry_form_id, color_type, construction,itrm_ref, copmposition, gsm_weight, dia_width, fabric_color_id, gmts_color_id, fin_fab_qnty, grey_fab_qnty, adjust_qty, rate, amount, process_loss_percent, remark, pre_cost_remarks, hs_code, precons, inserted_by, insert_date, status_active, is_deleted";
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
			//echo $dia."ppp";
			 //print_r($json_data->$precostid->$colorid->$dia);
			 
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
					 $data_array1 .="(".$id_dtls.",2,".$update_id.",".$precostid.",".$poId.",".$$txtjob.",".$txt_booking_no.",8,608,".$$txtcolortype_.",".$$txtconstruction_.",".$$txtitrmref_.",".$$txtcompositi_.",".$$txtgsm_weight_.",".$$txtdia_.",".$$txtitemcolor.",".$$txtgmtcolor.",".$finWoQty.",".$wQty.",".$adjQty.",".$rate.",".$amount.",".$pLossPer.",".$$txtremark.",".$$process.",".$$hscode.",'".$reqqtycostingper."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
		$flag=1;
		if(str_replace("'","",$lib_tna_intregrate)==1)
		{
			$delivery_date=fnc_delivery_date($str_po_id,str_replace("'","",$txt_booking_no));
			$ex_date=explode("**",$delivery_date);
			$rID_date=$ex_date[0];
			if($ex_date[0]==1) $flag=1; else $flag=0;
		}

		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		 if($flag==1)
		 {
			 $rID1= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no =".$txt_booking_no." and status_active=1 and is_deleted=0",0);
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
		 $flag=1;
		 if(str_replace("'","",$lib_tna_intregrate)==1)
		 {
			 $delivery_date=fnc_delivery_date($str_po_id,str_replace("'","",$txt_booking_no));
			 $ex_date=explode("**",$delivery_date);
			 $rID_date=$ex_date[0];
			 if($ex_date[0]==1) $flag=1; else $flag=0;
		 }

		 //echo "10**".$delivery_date; die;
		 //echo "10**". bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ); die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
		 if($rID==1 && $flag==1) $flag=1; else $flag=0;

		 if($flag==1)
		 {
			 $rID1= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no =".$txt_booking_no." and status_active=1 and is_deleted=0",0);
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
	else if ($operation==2)   // Delete Here
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
		disconnect($con);	die;
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
			//echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			//disconnect($con);die;
		}
		$lib_yarn_count_deter_id=0;
		$sql=sql_select("select lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where id=$cbo_fabric_description");
		foreach($sql as $row){
			$lib_yarn_count_deter_id=$row[csf('lib_yarn_count_deter_id')];
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
if($action=="save_update_delete_library_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0 $color_nameCond", "id", "color_name");
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
		 $field_array1="id, is_short, booking_mst_id, lib_yarn_id, po_break_down_id, job_no, booking_no, booking_type, entry_form_id,color_type, construction, copmposition, gsm_weight, dia_width, fabric_color_id, gmts_color_id, fin_fab_qnty, grey_fab_qnty, adjust_qty, rate, amount, remark, hs_code, body_part, width_dia_type, uom, process, inserted_by, insert_date, status_active, is_deleted";
		 $j=1;
		 $new_array_color=array(); $poIdChkArr=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txtjob="txtjob_".$i;
			 $txtpoid_="txtpoid_".$i;
			//=========
			$lib_yarn_id="lib_yarn_id_".$i;
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
			 $txtbodypart="txtbodypart_".$i;
			 $txtwidthtype="txtwidthtype_".$i;
			 $uom="uom_".$i;

			 $libyarn_id=str_replace("'","",$$lib_yarn_id);
			 $colorid=str_replace("'","",$$txtgmtcolor);
             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $finreqqnty=str_replace("'","",$$txtfinreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $rate=str_replace("'","",$$txtrate);
			 $amount=str_replace("'","",$$txtamount);
			 $poId=str_replace("'","",$$txtpoid_);
			if(str_replace("'","",$$txtitemcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtitemcolor),$new_array_color, TRUE))
				{
					$item_color_id = return_id_lib_common( str_replace("'","",$$txtitemcolor), $color_library, "lib_color", "id,color_name","608");
					$new_array_color[$item_color_id]=str_replace("'","",$$txtitemcolor);
				}
				else $item_color_id =  array_search(str_replace("'","",$$txtitemcolor), $new_array_color);
			} else $item_color_id=0;			 
				if($woq>0){
					if ($j!=1) $data_array1 .=",";
					$data_array1 .="(".$id_dtls.",2,".$update_id.",".$$lib_yarn_id.",".$$txtpoid_.",".$$txtjob.",".$txt_booking_no.",8,608,".$$txtcolortype_.",".$$txtconstruction_.",".$$txtcompositi_.",".$$txtgsm_weight_.",".$$txtdia_.",".$item_color_id.",".$$txtgmtcolor.",".$woq.",".$woq.",".$$txtadj.",".$rate.",".$amount.",".$$txtremark.",".$$hscode.",".$$txtbodypart.",".$$txtwidthtype.",".$$uom.",".$$process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					//if($str_po_id=="") $str_po_id=$poId; else $str_po_id.='*'.$poId;
					$id_dtls=$id_dtls+1;
					$j++;
				}
		 }
		 //echo "10**INSERT INTO wo_booking_dtls ($field_array1) values $data_array1"; die;	
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 if($rID==1) $flag=1; else $flag=0;
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
			$sql_po= sql_select("select b.id as dtlsid, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, b.color_type, b.construction, b.copmposition, b.gsm_weight, b.dia_width from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=8 and a.is_short=2 and a.entry_form=608 and b.booking_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
			foreach($sql_po as $pdata){
				$po_arr[$pdata[csf('po_break_down_id')]]=$pdata[csf('po_break_down_id')];
				$olddtlsidArr[$pdata[csf('dtlsid')]]=$pdata[csf('dtlsid')];
				$strold=$pdata[csf('construction')].$pdata[csf('fabric_color_id')].$pdata[csf('copmposition')].$pdata[csf('color_type')].$pdata[csf('gsm_weight')].$pdata[csf('dia_width')];
				$preDataArr[$pdata[csf('pre_cost_fabric_cost_dtls_id')]][$pdata[csf('po_break_down_id')]][$strold]=$strold;
			}
		}		
		
		$field_array_up1="color_type*construction*copmposition*gsm_weight*dia_width*fabric_color_id*gmts_color_id*fin_fab_qnty*grey_fab_qnty*adjust_qty*rate*amount*remark*hs_code*lib_yarn_id*body_part*width_dia_type*uom*updated_by*update_date";
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
			 $lib_yarn_id="lib_yarn_id_".$i;
			 $txtbodypart="txtbodypart_".$i;
			 $txtwidthtype="txtwidthtype_".$i;
			 $uom="uom_".$i;

			 $precostid=str_replace("'","",$cbo_fabric_description);
			 $colorid=str_replace("'","",$$txtgmtcolor);
             $reqqnty=str_replace("'","",$$txtreqqnty);
			 $finreqqnty=str_replace("'","",$$txtfinreqqnty);
			 $woq=str_replace("'","",$$txtwoq);
			 $acwoq=str_replace("'","",$$txtacwoq);
			 $rate=str_replace("'","",$$txtrate);
			 $amount=str_replace("'","",$$txtamount);

			 if(str_replace("'","",$$txtitemcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtitemcolor),$new_array_color, TRUE))
				{
					$item_color_id = return_id_lib_common( str_replace("'","",$$txtitemcolor), $color_library, "lib_color", "id,color_name","608");
					$new_array_color[$item_color_id]=str_replace("'","",$$txtitemcolor);
				}
				else $item_color_id =  array_search(str_replace("'","",$$txtitemcolor), $new_array_color);
			} else $item_color_id=0;
			 
			 

			$poId=str_replace("'","",$$txtpoid);
			 if(str_replace("'",'',$$bookingid)!=""){
				$id_arr[]=str_replace("'",'',$$bookingid);
				$data_array_up1[str_replace("'",'',$$bookingid)] =explode("*",("".$$txtcolortype_."*".$$txtconstruction_."*".$$txtcompositi_."*".$$txtgsm_weight_."*".$$txtdia_."*".$item_color_id."*".$$txtgmtcolor."*".$woq."*".$woq."*".$$txtadj."*".$rate."*".$amount."*".$$txtremark."*".$$hscode."*".$$lib_yarn_id."*".$$txtbodypart."*".$$txtwidthtype."*".$$uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				if($str_po_id=="") $str_po_id=$poId; else $str_po_id.='*'.$poId;
			 }
		 }
		
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
		 if($rID==1) $flag=1; else $flag=0;

		/*  if($flag==1)
		 {
			 $rID1= execute_query( "update fabric_sales_order_mst set is_apply_last_update=2 where sales_booking_no ='".str_replace("'", "", $txt_booking_no)."' and status_active=1 and is_deleted=0",0);
			 if( $rID1==1 && $flag==1) $flag=1; else $flag=0;
		 } */

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
		http.open("POST","multi_job_additional_fabric_booking_controller.php",true);
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
						$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=608");// quotation_id='$data'
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
							<input type="button" id="set_button4" class="image_uploader" style="width:160px;" value="Add More.." onClick="open_extra_terms_popup('multi_job_additional_fabric_booking_controller.php?action=extra_terms_popup','Terms Condition')" />
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
				$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=0 and page_id=608");// quotation_id='$data'
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
show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+document.getElementById('cbo_level').value+'**'+<? echo "'$permission'";?>,'load_color_size_form','form_data_con','multi_job_additional_fabric_booking_controller','');
}

function show_list()
{
	//echo $txt_booking_no.'fff';
	var booking_no='<? echo $txt_booking_no;?>';

	show_list_view(booking_no,'show_list_view','list_view_con','multi_job_additional_fabric_booking_controller','');
}
function show_sub_form_with_data(booking_no,fabric_cost_id){
	document.getElementById('cbo_fabric_part').value=fabric_cost_id;
	document.getElementById('booking_no').value=booking_no;
	show_list_view(document.getElementById('cbo_fabric_part').value+'**'+document.getElementById('booking_no').value+'**'+'00'+'**'+<? echo "'$permissions'";?>,'load_color_size_form','form_data_con','multi_job_additional_fabric_booking_controller','');
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
		var booking=return_global_ajax_value(booking_no, 'check_booking_approved', '', 'multi_job_additional_fabric_booking_controller');
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

		http.open("POST","multi_job_additional_fabric_booking_controller.php",true);
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
				http.open("POST","multi_job_additional_fabric_booking_controller.php",true);
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
	//echo "$('#print').hide();\n";
	echo "$('#print').show();\n";
	foreach($print_report_format_arr as $id){
		//if($id==143){echo "$('#print').show();\n";}
	}

	exit();
}


if ($action=="send_mail_report_setting_first_select"){
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id =35 and is_deleted=0 and status_active=1");
	echo $print_report_format;
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
	//$fab_uomArr = return_library_array("select id,uom from wo_pre_cost_fabric_cost_dtls","id","uom");
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' or form_name='knit_order_entry' and file_type=1",'master_tble_id','image_location');

	$sql_mst="select buyer_id, booking_no, booking_date, supplier_id, currency_id, exchange_rate, attention, delivery_date, pay_mode, po_break_down_id, colar_excess_percent, cuff_excess_percent, delivery_date, is_apply_last_update, fabric_source, rmg_process_breakdown, insert_date, update_date, uom, remarks, pay_mode, fabric_composition from wo_booking_mst where booking_no=$txt_booking_no and status_active =1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);

	$booking_no=$dataArray[0][csf('booking_no')];
	$booking_uom=$dataArray[0][csf('uom')];

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
	$style_ref="";
	//$job_arr=array(); $po_arr=array();
	$season=$season_matrix=$dealing_marchant=$po_number=$pub_shipment_date=$style_ref='';
	$po_sql=sql_select("select a.season,a.style_ref_no, a.season_matrix, a.dealing_marchant, b.po_number, b.pub_shipment_date  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no in(".$job_nos.") and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0");
	foreach($po_sql as $row)
	{
		if($row[csf('season_matrix')]!=0) $row[csf('season_matrix')]=$row[csf('season')]; else $row[csf('season_matrix')]=$season_arr[$row[csf('season_matrix')]];
		if($season=="") $season=$row[csf('season_matrix')]; else $season.=','.$row[csf('season_matrix')];
		if($dealing_marchant=="") $dealing_marchant=$marchentrArr[$row[csf('dealing_marchant')]]; else $dealing_marchant.=','.$marchentrArr[$row[csf('dealing_marchant')]];
		if($po_number=="") $po_number=$row[csf('po_number')]; else $po_number.=','.$row[csf('po_number')];
		if($pub_shipment_date=="") $pub_shipment_date=change_date_format($row[csf('pub_shipment_date')]); else $pub_shipment_date.=','.change_date_format($row[csf('pub_shipment_date')]);
			if($style_ref=="") $style_ref=$row[csf('style_ref_no')]; else $style_ref.=','.$row[csf('style_ref_no')];
	}

	$season=implode(",",array_unique(explode(",",$season)));
	$dealing_marchant=implode(",",array_unique(explode(",",$dealing_marchant)));
	$po_number=implode(",",array_unique(explode(",",$po_number)));
	$pub_shipment_date=implode(",",array_unique(explode(",",$pub_shipment_date)));
	$style_ref=implode(",",array_unique(explode(",",$style_ref)));
	$path=str_replace("'","",$path);
	if($path=="")
	{
		$path='../../';
	}
	ob_start();
	?>
    <html>
	 
	<div style="width:950px;">
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
    <table width="950" cellspacing="0" align="" border="0">
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
            <td width="130"><strong>Buyer: </strong></td><td width="175px"> <p><? echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; ?></p></td>
            <td width="130"><strong>Currency :</strong></td> <td width="175"><p><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></p></td>
        </tr>
        <tr>
            <td width="130"><strong>Order No: </strong></td><td  width="175px" style="word-break:break-all"><p> <? echo $po_number; ?></p></td>
            <td><strong>Season :</strong></td> <td><p><? echo $season; ?></p></td>
            <td><strong>Attention:</strong></td> <td><p><? echo $dataArray[0][csf('attention')]; ?></p></td>
        </tr>
        <tr>
            <td><strong>Job No.: </strong></td><td style="word-break:break-all"><p> <? echo implode(",",$job_no); ?></p></td>
            <td><strong>Dealing Merchandiser:</strong></td><td><p><? echo $dealing_marchant; ?></p></td>
            <!-- <td><strong>Ship Date:</strong></td><td><p><? //echo $pub_shipment_date; ?></p></td> -->
            <td><strong>Style No.: </strong></td><td style="word-break:break-all"> <p><? echo $style_ref; ?></p></td>
        </tr>
        <tr>

			<td><strong>Booking Remarks:</strong></td><td colspan="3"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
		<table style="margin-left:3px;" cellspacing="0" width="1010"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="200">Item Description D</th>
                <th width="60">GSM</th>
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
                $sql_result= sql_select("SELECT a.construction, a.copmposition, a.gsm_weight, a.dia_width, a.color_type, a.gmts_color_id, a.fabric_color_id, sum(a.fin_fab_qnty) as fin_fab_qnty, avg(a.rate) as rate, sum(a.amount) as amount,b.uom from wo_booking_dtls a join wo_pre_cost_fabric_cost_dtls b on a.pre_cost_fabric_cost_dtls_id=b.id where a.booking_no ='$booking_no' and a.status_active=1 and a.is_deleted=0 group by a.construction, a.copmposition, a.dia_width, a.color_type, a.gmts_color_id, a.fabric_color_id,a.gsm_weight, b.uom order by  a.construction, a.copmposition ");
                $i=1;$desc_by_arr=array();$k=1;$sub_tot_qty=$sub_tot_amount=0;
                foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					//$uom_id=$fab_uomArr[$row[csf('fab_cost_id')]];

					$desc_value=$row[csf('construction')].$row[csf('copmposition')];
					if (!in_array($desc_value,$desc_by_arr) )
						{
							if($k!=1)
							{
							?>
							 <tr class="tbl_bottom" style="background:#CCCCCC">
                              <td colspan="8"  align="right"><b>Sub Total</b></td>
							   <td align="right"><? echo number_format($sub_tot_qty,2); ?></td>
							    <td align="right">&nbsp;</td>
								<td align="right"><? echo number_format($sub_tot_amount,2); ?></td>
							  </tr>


							<?
								unset($sub_tot_qty);
								unset($sub_tot_amount);
							}
							$desc_by_arr[]=$desc_value;
							$k++;
						}
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $row[csf('construction')].' '.$row[csf('copmposition')]; ?></td>
                        <td><? echo $row[csf('gsm_weight')]; ?></td>
                        <td><? echo $row[csf('dia_width')]; ?></td>
                        <td><? echo $color_type[$row[csf('color_type')]]; ?></td>
                        <td><? echo $color_arr[$row[csf('gmts_color_id')]]; ?></td>
                        <td><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
                        <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('fin_fab_qnty')],2); ?></td>
                        <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                        <td align="right"><? echo number_format($row[csf('amount')],2); ?></td>
					</tr>
					<?
					$i++;
					$grand_tot_qty+=$row[csf('fin_fab_qnty')];
					$sub_tot_qty+=$row[csf('fin_fab_qnty')];
					$grand_tot_amount+=$row[csf('amount')];
					$sub_tot_amount+=$row[csf('amount')];
				}
				$carrency_id=$dataArray[0][csf('currency_id')];
				if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				?>
				<tr class="tbl_bottom" style="background:#CCCCCC">
				  <td colspan="8"  align="right"><b>Sub Total</b></td>
				   <td align="right"><? echo number_format($sub_tot_qty,2); ?></td>
					<td align="right">&nbsp;</td>
					<td align="right"><? echo number_format($sub_tot_amount,2); ?></td>
				 </tr>

            </tbody>
            <tfoot>
                <tr>
                    <td align="right" colspan="8"><strong>Grand Total</strong></td>
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
			echo get_spacial_instruction($booking_no,'930px',608);
		?>
        <br>
		 <?
            echo signature_table(121, $cbo_company_name, "930px");
         ?>
        </div>
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
			echo "$filename****$html****$report_cat";
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
	  	<td width="100"><input class="text_boxes" type="text" style="width:100px;"  name="trims_<? echo $i; ?>" id="trims_<? echo $i; ?>" value="<? echo $dtm_arr[$row[csf('id')]][$row[csf('fabric_color_id')]] ?>" onDblClick="trims_popup('multi_job_additional_fabric_booking_controller.php?action=trims_popup','Trims Item',<? echo $i ?>)" readonly/></td>
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
if ($action=="populate_variable_setting_data"){
	$data_array=sql_select("select exeed_budge_qty,exeed_budge_amount,amount_exceed_level from variable_order_tracking where company_name='$data' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row){
		echo "document.getElementById('exeed_budge_qty').value = '".$row[csf("exeed_budge_qty")]."';\n";
		echo "document.getElementById('exeed_budge_amount').value = '".$row[csf("exeed_budge_amount")]."';\n";
		echo "document.getElementById('amount_exceed_level').value = '".$row[csf("amount_exceed_level")]."';\n";
	}
	$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type in (9) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "-Select Supplier-",$selected,"get_php_form_data( this.value, \'load_drop_down_attention\', \'requires/multi_job_additional_fabric_booking_controller\');","");
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"check_paymode(this.value);","");

	echo "document.getElementById('supplier_td').innerHTML = '".$cbo_supplier_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=26 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	//echo "print_report_button_setting('".$print_report_format."');\n";
	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0");
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	if($maintain_setting==1) {
		echo "document.getElementById('lib_tna_intregrate').value = '1';\n";
	}
	else {
		echo "document.getElementById('lib_tna_intregrate').value = '0';\n";
	}
	exit();
}

if ($action=="load_drop_down_supplier"){
	echo $action($data);
	exit();
}
if($action=="body_part_popup")
{
	echo load_html_head_contents("Item Group Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id, name,type)
	{
		document.getElementById('gid').value=id;
		document.getElementById('gname').value=name;
		document.getElementById('gtype').value=type;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="gname" name="gname"/>
        <input type="hidden" id="gtype" name="gtype"/>
        <?
        $sql_tgroup=sql_select( "select body_part_full_name,body_part_short_name,body_part_type,id from lib_body_part where  is_deleted=0  and  status_active=1 order by body_part_full_name ASC");
        ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th width="300">Item Group</th><th>Type</th>
            </thead>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
            <?
            $i=1;
            foreach($sql_tgroup as $row_tgroup)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr onClick="js_set_value(<? echo $row_tgroup[csf('id')]; ?>, '<? echo $row_tgroup[csf('body_part_full_name')]; ?>', '<? echo $row_tgroup[csf('body_part_type')]; ?>')" bgcolor="<? echo $bgcolor; ?>">
					<td width="40"><? echo $i; ?></td><td width="300"><? echo $row_tgroup[csf('body_part_full_name')]; ?></td><td width=""><? echo $body_part_type[$row_tgroup[csf('body_part_type')]]; ?></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </tbody>
        </table>
        </div>
	</body>
	<script>
	setFilterGrid('item_table',-1)
	</script>
	</html>
	<?
	exit();
}
if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$fabricSource=0;
	?>
	<script>
		var fabricSource='<?=$fabricSource; ?>';
		function js_set_value(data)
		{
			var data=data.split('_');
			var fabric_yarn_description=return_global_ajax_value(data[0], 'fabric_yarn_description', '', 'pre_cost_entry_controller_v2');
			var fabric_yarn_description_arr=fabric_yarn_description.split("**");
			var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
			document.getElementById('fab_des_id').value=data[0];
			document.getElementById('fab_nature_id').value=data[1];
			document.getElementById('construction').value=trim(data[2]);
			document.getElementById('fab_gsm').value=trim(data[3]);
			document.getElementById('process_loss').value=trim(data[4]);
			document.getElementById('fab_desctiption').value=trim(fabric_description);
			document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
			var yarn =fabric_yarn_description_arr[1].split("_");
			if(yarn[1]*1==0 || yarn[1]==""){
				alert("Composition not set in yarn count determination");
				return;
			}
			document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
			</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="4" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th>Construction</th>
                        <th>GSM/Weight</th>
                        <th>RD/Ref. No</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                         <td align="center"><input type="text" style="width:100px" class="text_boxes" name="txt_rd_ref" id="txt_rd_ref" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$fabric_nature; ?>'+'**'+'<?=$libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+'<?=$fabricSource; ?>'+'**'+'<?=$txtbodypart; ?>'+'**'+'<?=$cbocolortype; ?>'+'**'+'<?=$txt_job_no; ?>'+'**'+document.getElementById('txt_rd_ref').value, 'fabric_description_popup_search_list_view', 'search_div', 'pre_cost_entry_controller_v2', 'setFilterGrid(\'list_view\',-1)'); toggle('tr_'+'<?=$libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_description_popup_search_list_view")
{
	//echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type,$fabricSource,$txtbodypart,$cbocolortype,$txt_job_no,$txt_rd_ref)=explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$search_con='';
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
		if($txt_rd_ref!='') {$search_con .= " and a.rd_no='".trim($txt_rd_ref)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
		if($txt_rd_ref!='') {$search_con .= " and a.rd_no like ('".trim($txt_rd_ref)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
		if($txt_rd_ref!='') {$search_con .= " and a.rd_no like ('%".trim($txt_rd_ref)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
		if($txt_rd_ref!='') {$search_con .= " and a.rd_no like ('%".trim($txt_rd_ref)."%')";}
	}
	
	

	$fabric_composition = return_library_array("select id, fabric_composition_name from  lib_fabric_composition where status_active=1 and is_deleted=0 order by fabric_composition_name", "id", "fabric_composition_name");
	?>
	</head>
	<body>

		<div align="center">
			<form>
				<input type="hidden" id="fab_des_id" name="fab_des_id" />
				<input type="hidden" id="fab_nature_id" name="fab_des_id" />
				<input type="hidden" id="construction" name="construction" />
				<input type="hidden" id="composition" name="composition" />
				<input type="hidden" id="fab_gsm" name="fab_gsm" />
				<input type="hidden" id="process_loss" name="process_loss" />
				<input type="hidden" id="fab_desctiption" name="fab_desctiption" />
				<input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
			</form>
		<?
		$composition_arr=array();
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
		$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
		$group_short_name=$lib_group_short[1];
		//$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
		if($fabricSource==0)
		{
			$sql="select a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, b.id as bid from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and (a.entry_form=184 or a.entry_form is null) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
			$data_array=sql_select($sql);
			$sysCodeArr=array();
			if (count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					$sys_code=$group_short_name.'-'.$row[csf('id')];
					$sysCodeArr[$row[csf('id')]]=$sys_code;
				}
			}
			?>
			<table class="rpt_table" width="1050" cellspacing="0" cellpadding="0" border="0" rules="all">
				<thead>
					<tr>
						<th width="50">SL No</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="50">Sys No</th>
						<th width="80">RD/Ref. No</th>
						<th width="80">Sequence No</th>
						<th width="100">Fab Nature</th>
						<th width="100">Construction</th>
						<th width="100">GSM/Weight</th>
						<th width="100">Color Range</th>
						<th width="90">Stich Length</th>
						<th width="50">Process Loss</th>
						<th width="80">Fabric Composition</th>
						<th>Composition</th>
					</tr>
			</thead>
		</table>
		<div id="" style="max-height:300px; width:1050px; overflow-y:scroll">
		<table id="list_view" class="rpt_table" width="1030" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
				<tbody>
			<?
			$sql_data=sql_select("SELECT a.fab_nature_id, a.construction,a.rd_no, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.id, a.sequence_no, a.fabric_composition_id  from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and (a.entry_form=184 or a.entry_form is null) $search_con group by a.id,a.rd_no, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.sequence_no, a.fabric_composition_id order by a.id");
			
			$i=1;
			foreach($sql_data as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')] ?>')">
					<td width="50"><? echo $i; ?></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="50" align="left"><? echo $sysCodeArr[$row[csf('id')]]; ?></td>
					<td width="80" align="left"><? echo $row[csf('rd_no')]; ?></td>
					<td width="80" align="left"><? echo $row[csf('sequence_no')]; ?></td>
					<td width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
					<td width="100" align="left"><? echo $row[csf('construction')]; ?></td>
					<td width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
					<td width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
					<td width="90" align="right"><? echo $row[csf('stich_length')]; ?></td>
					<td width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
					<td width="80" align="right"><? echo $fabric_composition[$row[csf('fabric_composition_id')]]; ?></td>
					<td><? echo $composition_arr[$row[csf('id')]]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
				</tbody>
			</table>
		</div>
		</div>
		<? } else if($fabricSource==1){ 
			$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id, b.id as bid from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
			$data_array=sql_select($sql);
			$sysCodeArr=array();
			if (count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
					}
					$sys_code=$group_short_name.'-'.$row[csf('id')];
					$sysCodeArr[$row[csf('id')]]=$sys_code;
				}
			}
			?>
			<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" border="0" rules="all">
				<thead>
					<tr>
						<th width="30">SL No</th>
						<th width="120">Fab. Name</th>
						<th width="70">Fab. Source</th>
						<th width="80">Body Part</th>
						<th width="70">Body Part Type</th>
						<th width="70">Color Type</th>
						<th width="60">Count Range</th>
						<th width="70">Color Range</th>
						<th width="70">No. Of Color</th>
						<th width="80">Coverage %</th>
						<th width="60">AOP Type</th>
						<th width="70">AOP Process Upto</th>
						<th>Rate[$]</th>
					</tr>
			</thead>
		</table>
		<div style="max-height:300px; width:948px; overflow-y:scroll">
		<table id="list_view" class="rpt_table" width="930" cellspacing="0" cellpadding="0" border="1" rules="all">
				<tbody>
				<?				
				$sql_data=sql_select("select a.id, a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.fabric_composition_id, b.fabric_source, b.body_part_id, b.body_part_type, b.color_type, b.count_range_from, b.count_range_to, b.color_range, b.no_of_color, b.coverage_range_from, b.coverage_range_to, b.aop_type, b.aop_process_upto, b.rate_usd from lib_yarn_count_determina_mst a, process_finish_fabric_rate_chat b where a.id=b.fabric_description and a.fab_nature_id='$fabric_nature' and b.body_part_id='$txtbodypart' and b.color_type='$cbocolortype' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_con order by a.id");
			
				$i=1;
				foreach($sql_data as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$fab_desc=$row[csf('construction')].','.$composition_arr[$row[csf('id')]];
					$countRange=$row[csf('count_range_from')].'-'.$row[csf('count_range_to')];
					$covPer=$row[csf('coverage_range_from')].'-'.$row[csf('coverage_range_to')];
					?>
					<tr id="tr_<?=$row[csf('id')]; ?>" bgcolor="<?=$bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')]."_".$fab_desc; ?>');">
						<td width="30"><?=$i; ?></td>
						<td width="120" style="word-break:break-all"><?=$fab_desc; ?></td>
						<td width="70"><?=$fabric_source[$row[csf('fabric_source')]]; ?></td>
						<td width="80"><?=$body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="70"><?=$body_part_type[$row[csf('body_part_type')]]; ?></td>
						
						<td width="70"><?=$color_type[$row[csf('color_type')]]; ?></td>
						<td width="60"><?=$countRange; ?></td>
						<td width="70"><?=$color_range[$row[csf('color_range')]]; ?></td>
						
						<td width="70"><?=$no_color_arr[$row[csf('no_of_color')]]; ?></td>
						<td width="80"><?=$covPer; ?></td>
						<td width="60"><?=$conversion_cost_head_array[$row[csf('aop_type')]]; ?></td>
						
						<td width="70"><?=$aop_process_arr[$row[csf('aop_process_upto')]]; ?></td>
						<td align="right"><?=$row[csf('rate_usd')]; ?></td>
					</tr>
					<?
					$i++;
				}
				?>
				</tbody>
			</table>
		</div>
		</div>
		<? } ?>
	</body>
	</html>
	<?
	exit();
}
if($action=="print_booking_report")
{
    extract($_REQUEST);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
    $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
    $cbo_item_from=str_replace("'","",$cbo_item_from);

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
	$compnay_info=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code from lib_company where id=$cbo_company_name");
	$company_address="";
	foreach ($compnay_info as $result)
	{
		if($result[csf('plot_no')]!='') $company_address.=$result[csf('plot_no')];
		if($result[csf('level_no')]!='') $company_address.=",".$result[csf('level_no')];
		if($result[csf('road_no')]!='') $company_address.=",".$result[csf('road_no')];
		if($result[csf('block_no')]!='') $company_address.=",".$result[csf('block_no')];
		if($result[csf('city')]!='') $company_address.=",".$result[csf('city')];
		if($result[csf('zip_code')]!='') $company_address.="-".$result[csf('zip_code')];
		if($result[csf('country_id')]!='') $company_address.=",".$country_arr[$result[csf('country_id')]];
	}

	$booking_dtls_data= sql_select("SELECT b.style_ref_no , c.po_number, d.lib_yarn_id, d.dia_width,d.construction,d.copmposition,a.source as fab_nat_id, d.gsm_weight, d.body_part, d.color_type, d.po_break_down_id , d.remark, d.uom, d.fin_fab_qnty, d.grey_fab_qnty, d.rate as rates, d.amount , d.fabric_color_id, d.gmts_color_id as color_id, d.job_no, d.width_dia_type, a.buyer_id, a.supplier_id, a.booking_date, a.delivery_date, a.pay_mode from wo_booking_mst a, wo_booking_dtls d , wo_po_break_down c, wo_po_details_master b  where a.id=d.booking_mst_id and c.id=d.po_break_down_id and d.job_no=b.job_no and d.booking_no =$txt_booking_no and d.status_active=1 and d.is_deleted=0 and d.fin_fab_qnty>0   and d.body_part not in(40,50) order by d.job_no");
	$booking_dtls_arr=array();
	foreach($booking_dtls_data as $row){
		$sub_key=$row[csf('po_break_down_id')].'*'.$row[csf('lib_yarn_id')].'*'.$row[csf('fabric_color_id')];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['po_number']=$row[csf('po_number')];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['style_ref_no']=$row[csf('style_ref_no')];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['copmposition']=$row[csf('copmposition')];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['construction']=$row[csf('construction')];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['gsm_weight']=$row[csf('gsm_weight')];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['dia']=$row[csf('dia_width')].','.$fabric_typee[$row[csf('width_dia_type')]];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['fabric_color_id']=$color_library[$row[csf('fabric_color_id')]];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['uom']=$unit_of_measurement[$row[csf('uom')]];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['amount']+=$row[csf('amount')];
		$booking_dtls_arr[$row[csf('job_no')]][$sub_key]['remark']=$row[csf('remark')];
		$po_id_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		$buyer_id=$row[csf('buyer_id')];
		$supplier_id=$row[csf('supplier_id')];
		$booking_date=$row[csf('booking_date')];
		$delivery_date=$row[csf('delivery_date')];
		$pay_mode=$row[csf('pay_mode')];
	}

	$buyer_info=sql_select("SELECT buyer_name, contact_person, buyer_email, address_1, address_2, address_3, address_4 from lib_buyer where status_active=1 and is_deleted=0 and id=$buyer_id");
	foreach($buyer_info as $row){
		$buyer_name=$row[csf('buyer_name')];
		$buyer_contact_person=$row[csf('contact_person')];
		$buyer_email=$row[csf('buyer_email')];
		$buyer_address=$row[csf('address_1')].''.$row[csf('address_2')].''.$row[csf('address_3')].''.$row[csf('address_3')];
	}
	if($pay_mode==3 || $pay_mode==5){
		$supplier_info=sql_select("SELECT a.company_name as supplier_name, a.contract_person, a.email, a.contact_no, rtrim(xmlagg(xmlelement(address, a.plot_no || ',' || a.level_no || ',' || a.road_no|| ',' ||a.block_no || ',' || a.city || ',' || a.zip_code|| ',' || b.country_name,', ').extract('//text()') order by a.id).getclobval(),', ') as address from lib_company a join lib_country b on a.country_id =b.id where a.id=$supplier_id group by a.company_name, a.contract_person, a.email, a.contact_no");
	}
	else{
		$supplier_info=sql_select("SELECT supplier_name, contact_person as contract_person, contact_no, email, rtrim(xmlagg(xmlelement(address, address_1 || ',' || address_2 || ',' || address_3 || ',' || address_4,', ').extract('//text()') order by id).getclobval(),', ') as address from lib_supplier  where id=$supplier_id group by supplier_name, contact_person, contact_no, email");
	}
	foreach($supplier_info as $row){
		$supplier_name=$row[csf('supplier_name')];
		$supplier_contact_person=$row[csf('contract_person')];
		$supplier_contact_no=$row[csf('contact_no')];
		$supplier_email=$row[csf('email')];
		$supplier_address=$row[csf('address')]->load();
	}
	$po_id_str=implode(",",$po_id_arr);
	$gmts_item_data=sql_select("SELECT item_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_id_str) and status_active=1 and is_deleted=0 group by item_number_id");
	foreach($gmts_item_data as $row){
		$gmts_item_arr[$row[csf('item_number_id')]]=$garments_item[$row[csf('item_number_id')]];
	}
    ob_start();
    ?>

    <div style="width:1250px" align="center" >
		<table id="tb_header" width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
			<tr>
				<td align="center"><strong><?php echo $company_library[$cbo_company_name]; ?></strong></td>				
			</tr>
			<tr>
				<td align="center"><?php echo $company_address; ?></td>
			</tr>
			<tr>
				<td align="center"><strong>Purchase Order</strong></td>
			</tr>
		</table>
		<table style="margin-top:20px" class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
				<th align="left" width="300">Order No.</th>			
				<td width="300"><?= str_replace("'","",$txt_booking_no)  ?></td>
				<th align="left" width="300">Delivary Date</th>			
				<td width="300"><?= change_date_format($delivery_date);  ?></td>			
			</tr>
			<tr>
				<th align="left">Order Date</th>			
				<td><?= change_date_format($booking_date);  ?></td>
				<th align="left">Test Requirement</th>			
				<td></td>			
			</tr>
			<tr>
				<th align="center" colspan="2">SUPPLIER</th>			
				<th align="center" colspan="2">BUYER</th>			
			</tr>
			<tr>
				<th align="left">Supplier name</th>			
				<td><?= $supplier_name  ?></td>
				<th align="left">Purchaser Name</th>			
				<td><?= $company_library[$cbo_company_name]; ?></td>			
			</tr>
			<tr>
				<th align="left">Supplier Code</th>			
				<td><?= ''  ?></td>
				<th align="left">Contact Person</th>			
				<td><?= $buyer_contact_person ?></td>			
			</tr>
			<tr>
				<th align="left">Attention</th>			
				<td><?= $supplier_contact_person  ?></td>
				<th align="left">Contact No.</th>			
				<td></td>			
			</tr>
			<tr>
				<th align="left">Address</th>			
				<td><?= $supplier_address  ?></td>
				<th align="left">Email</th>			
				<td><?= $buyer_email ?></td>			
			</tr>
			<tr>
				<th align="left">Contact No.</th>			
				<td><?= $supplier_contact_no  ?></td>
				<th align="left">Buyer/Agent Name</th>			
				<td><?= $buyer_name ?></td>			
			</tr>
			<tr>
				<th align="left">Email</th>			
				<td><?= $supplier_email  ?></td>
				<th align="left">Garments Item</th>			
				<td><?= implode(", ",$gmts_item_arr) ?></td>			
			</tr>
		</table>		
		<table style="margin-top:50px" class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
				<th  width="100" align="center">Job No.</th>
				<th  width="100" align="center">Customer PO No.</th>
				<th  width="100" align="center">Customer Style</th>
				<th  width="150" align="center">Composition</th>
				<th  width="150" align="center">Construction</th>
				<th  width="60" align="center">Weight</th>
				<th  width="70" align="center">Dia/Width</th>
				<th  width="70" align="center">Fabric Color</th>
				<th  width="70" align="center">UOM</th>
				<th  width="70" align="center">Quantity</th>
				<th width="70" align="center">Rate(USD)</th>
				<th width="70" align="center">Amount(USD)</th>
				<th width="100" align="center">Remark</th>
			</tr>
			<?
				/* echo '<pre>';
				print_r($booking_dtls_arr); die; */
				foreach($booking_dtls_arr as $job_no=>$dtls_data){
					$i=1; $job_qty=$job_amount=0;
					foreach($dtls_data as $value){ ?>
					<tr>
						<? if ($i==1){?>
						<td rowspan="<?= count($dtls_data)?>"><?= $job_no ?></td>
						<? } ?>
						<td><?= $value['po_number'] ?></td>
						<? if ($i==1){?>
						<td rowspan="<?= count($dtls_data)?>"><?= $value['style_ref_no'] ?></td>
						<? } ?>
						<td><?= $value['copmposition'] ?></td>
						<td><?= $value['construction'] ?></td>
						<td><?= $value['gsm_weight'] ?></td>
						<td><?= $value['dia'] ?></td>
						<td><?= $value['fabric_color_id'] ?></td>
						<td><?= $value['uom'] ?></td>
						<td align="right"><?= fn_number_format($value['fin_fab_qnty'],3) ?></td>
						<td align="right"><?= fn_number_format($value['amount']/$value['fin_fab_qnty'],3) ?></td>
						<td align="right"><?= fn_number_format($value['amount'],3) ?></td>
						<td><?= $value['remark'] ?></td>
					</tr>
				<? 
					$i++;
					$job_qty+=$value['fin_fab_qnty'];
					$job_amount+=$value['amount'];
					$total_job_qty+=$value['fin_fab_qnty'];
					$total_job_amount+=$value['amount'];
					}
					?>
						<tr>
							<th colspan="9" align="right">Job Total</th>
							<th align="right"><?= fn_number_format($job_qty) ?></th>
							<th></th>
							<th align="right"><?= fn_number_format($job_amount) ?></th>
							<th></th>
						</tr>
					<?
				}
			?>
			<tr>
				<th colspan="9" align="right">Grand Total</th>
				<th align="right"><?= fn_number_format($total_job_qty) ?></th>
				<th></th>
				<th align="right"><?= fn_number_format($total_job_amount) ?></th>
				<th></th>
			</tr>
		</table>
		<table  style="margin-top:10px" width="100%"  border="0" cellpadding="0" cellspacing="0">
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
		<table  style="margin-top:10px" width="100%"  border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>  <?
						echo signature_table(121, $cbo_company_name, "1330px",1,'');
					?>
				</td>
			</tr>
		</table>
    </div>
    <?
    exit();
}
?>