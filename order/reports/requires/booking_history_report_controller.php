<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer_popup"){
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","0","" );
	exit();
}

if ($action=="booking_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 	?>
	<script>
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	
	function fnc_data_show()
	{
		if(form_validation("cbo_company_mst","Company")==false){
			return;
		}
		else
		{
			show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value, 'create_booking_search_list_view', 'search_div', 'booking_history_report_controller','setFilterGrid(\'list_view\',-1)');
		}
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th colspan="11"><input type="hidden" id="cbo_search_category"></th>
                    </tr>
                    <tr>
                        <th width="150" class="must_entry_caption">Company Name</th>
                        <th width="150" class="must_entry_caption">Buyer Name</th>
                        <th width="80" class="must_entry_caption">Booking No</th>
                        <th width="80">Job No</th>
                        <th width="80">File No</th>
                        <th width="80">Internal Booking</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Order No</th>
                        <th width="130" class="must_entry_caption" colspan="2">Date Range</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="hidden" id="selected_booking">
                        <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'booking_history_report_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );",0); ?>
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                    <td><input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_data_show();" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="11"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num='$data[6]'    "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num='$data[4]'  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no ='$data[10]'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number = '$data[11]'  ";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like '$data[10]%'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '$data[11]%'  ";
	}

	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like'%$data[10]'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '%$data[11]'  ";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like '%$data[10]%'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '%$data[11]%'  ";
	}

	$file_no = str_replace("'","",$data[8]);
	$internal_ref = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping='".trim($internal_ref)."' ";

	if($db_type==0){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
	$po_array=array();
	$job_prefix_num=array();
	$sql_po= sql_select("select a.booking_no, a.po_break_down_id, a.job_no from wo_booking_mst a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=2 and   a.status_active=1 and a.is_deleted=0 order by a.booking_no");
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
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$job_prefix_num,6=>$garments_item,7=>$po_array,10=>$item_category,11=>$fabric_source,12=>$suplier,13=>$approved,14=>$is_ready);

	$sql= "select a.booking_no_prefix_num, c.file_no, c.grouping, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.pay_mode, d.gmts_item_id, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.booking_no, a.ready_to_approved, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_po_details_mas_set_details d where $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond $style_cond $order_cond ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no and a.booking_type=1 and a.status_active=1 and a.is_deleted=0  order by a.id DESC";//and a.entry_form=118 and a.is_short=2
	?>
    <table width="1160" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="60">Booking No</th>
                <th width="60">Booking Date</th>
                <th width="80">Buyer</th>
                <th width="60">Job No</th>
                <th width="90">Style Ref.</th>
                <th width="90">Gmts Item </th>
                <th width="100">PO number</th>
                <th width="80">Internal Ref</th>
                <th width="80">File No</th>
                <th width="80">Fabric Nature</th>
                <th width="80">Fabric Source</th>
                <th width="50">Pay Mode</th>
                <th width="50">Supplier</th>
                <th width="50">Approved</th>
                <th>Ready to Approved</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:1160px" >
        <table width="1140" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="list_view">
            <tbody>
            <?
            $sl=1;
            $data=sql_select($sql);
            foreach($data as $row)
            {
				if ($sl%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]?>')" style="cursor:pointer">
                    <td width="30"><? echo $sl; ?></td>
                    <td width="60"><? echo $row[csf("booking_no_prefix_num")];?></td>
                    <td width="60"><? echo change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-"); ?></td>
                    <td width="80" style="word-break:break-all"><? echo $buyer_arr[$row[csf("buyer_id")]];?></td>
                    <td width="60"><? echo $job_prefix_num[$row[csf("job_no")]];?></td>
                    <td width="90" style="word-break:break-all"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td width="90" style="word-break:break-all"><? echo $garments_item[$row[csf("gmts_item_id")]];?> </td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $po_array[$row[csf("po_break_down_id")]];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("grouping")];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("file_no")];?></td>
                    <td width="80" style="word-break:break-all"><? echo $item_category[$row[csf("item_category")]];?></td>
                    <td width="80" style="word-break:break-all"><? echo $fabric_source[$row[csf("fabric_source")]];?></td>
                    <td width="50" style="word-break:break-all"><? echo $pay_mode[$row[csf("pay_mode")]];?></td>
                    <td width="50" style="word-break:break-all">
                    <?
                    if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) echo $comp[$row[csf("supplier_id")]]; else echo $suplier[$row[csf("supplier_id")]];
                    ?>
                    </td>
                    <td width="50"><? echo $approved[$row[csf("is_approved")]];?></td>
                    <td><? echo $is_ready[$row[csf("ready_to_approved")]];?></td>
				</tr>
				<?
				$sl++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <?
	exit();
}


if ($action=="report_generate")
{
	extract($_REQUEST);
	$booking_no=str_replace("'","",$txt_booking_no);
	
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$colorArr = return_library_array("select id,color_name from lib_color ","id","color_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$fsoArr = return_library_array("select sales_booking_no, job_no from fabric_sales_order_mst where sales_booking_no='$booking_no'","sales_booking_no","job_no");
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	ob_start();
	?>
     <div align="center">
    <table width="950px" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="10" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
        </tr>
        <tr class="form_caption" style="border:none;">
            <td colspan="10" align="center" style="border:none; font-size:16px; font-weight:bold">For Booking No: '<? echo $booking_no; ?>'</td>
        </tr>
    </table>
    <?
		$sql_mst="select booking_id, booking_no, company_id, buyer_id, job_no from wo_booking_mst_hstry where booking_no='$booking_no'";
		$sql_mst_res=sql_select($sql_mst);
		$job_no=$sql_mst_res[0][csf('job_no')];
		$booking_id=$sql_mst_res[0][csf('booking_id')];
		
		$sql_po="select style_ref_no, season_buyer_wise from wo_po_details_master where job_no='$job_no'";
		$sql_po_res=sql_select($sql_po);

		$sql_inter="select grouping from wo_po_break_down where job_no_mst='$job_no'";
		$sql_inter_res=sql_select($sql_inter);
		
		$approval_app_arr=array();
		$sql_app="select approved_no, approved_date from approval_history where entry_form in (7,12) and mst_id='$booking_id'";
		$sql_app_res=sql_select($sql_app);
		foreach($sql_app_res as $row)
		{
			$approval_app_arr[$row[csf('approved_no')]]=$row[csf('approved_date')];
		}
		unset($sql_app_res);
		
		$bom_dtls_arr=array();
		$bom_fab_dtls_sql="select id, body_part_id, width_dia_type, item_number_id, uom, construction, composition from wo_pre_cost_fabric_cost_dtls where job_no='$job_no'";
		$bom_fab_dtls_res=sql_select($bom_fab_dtls_sql);
		
		foreach($bom_fab_dtls_res as $row)
		{
			$bom_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
			$bom_dtls_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
			$bom_dtls_arr[$row[csf('id')]]['item_number_id']=$row[csf('item_number_id')];
			$bom_dtls_arr[$row[csf('id')]]['uom']=$row[csf('uom')];
			$bom_dtls_arr[$row[csf('id')]]['construction']=$row[csf('construction')];
			$bom_dtls_arr[$row[csf('id')]]['composition']=$row[csf('composition')];
		}
		unset($bom_fab_dtls_res);
		
		$sql_dtls="select approved_no, pre_cost_fabric_cost_dtls_id, construction, copmposition, gsm_weight, dia_width, fabric_color_id, color_type, uom, fin_fab_qnty, grey_fab_qnty, rate, amount from wo_booking_dtls_hstry where booking_no='$booking_no' and status_active=1 and is_deleted=0 order by approved_no, pre_cost_fabric_cost_dtls_id";
		//echo $sql_dtls;
		$dtls_arr=array(); $approve_arr=array();
		$sql_dtls_res=sql_select($sql_dtls);
		foreach($sql_dtls_res as $row)
		{
			$approve_arr[$row[csf('approved_no')]]=$row[csf('approved_no')];
			$fab=""; $gmt_item=0; $body_partdtls=''; $uom=0;
			
			$gmt_item=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['item_number_id'];
			$body_partdtls=$body_part[$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part_id']];
			$uom=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['uom'];
			
			if($row[csf('construction')]=="") $row[csf('construction')]=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['construction'];
			if($row[csf('copmposition')]=="") $row[csf('copmposition')]=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['composition'];
			
			$fab=$row[csf('construction')].' '.$row[csf('copmposition')].', '.$row[csf('gsm_weight')].', '.$row[csf('dia_width')].', '.$fabric_typee[$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['width_dia_type']];
			
			$dtls_arr[$row[csf('approved_no')]][$fab][$body_partdtls][$gmt_item][$row[csf('fabric_color_id')]][$row[csf('color_type')]][$uom]['finqty']+=$row[csf('fin_fab_qnty')];
			$dtls_arr[$row[csf('approved_no')]][$fab][$body_partdtls][$gmt_item][$row[csf('fabric_color_id')]][$row[csf('color_type')]][$uom]['greyqty']+=$row[csf('grey_fab_qnty')];
			$dtls_arr[$row[csf('approved_no')]][$fab][$body_partdtls][$gmt_item][$row[csf('fabric_color_id')]][$row[csf('color_type')]][$uom]['amt']+=$row[csf('amount')];
		}
		unset($sql_dtls_res);
		?>
        <table width="950px" cellpadding="0" cellspacing="0" >  
            <tr>
                <td width="130"><strong>Company:</strong></td>
                <td width="175"><? echo $companyArr[$sql_mst_res[0][csf('company_id')]]; ?></td>
                <td width="130"><strong>Buyer: </strong></td>
                <td width="175px"> <? echo $buyerArr[$sql_mst_res[0][csf('buyer_id')]]; ?></td>
                <td width="130"><strong>Style Ref: </strong></td>
                <td width="175"><? echo $sql_po_res[0][csf('style_ref_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Job No:</strong></td>
                <td><? echo $sql_mst_res[0][csf('job_no')]; ?></td>
                <td><strong>Season:</strong></td>
                <td> <? echo $seasonArr[$sql_po_res[0][csf('season_buyer_wise')]]; ?></td>
                <td><strong>FSO No:</strong></td>
                <td><? echo $fsoArr[$booking_no]; ?></td>
            </tr>
			<tr>
                <td><strong> Internal Booking no:</strong></td>
                <td><? 
				 $ref_no=trim($sql_inter_res[0][csf('grouping')],"'");
				 $ref_nos=implode(", ",array_unique(explode(",",$ref_no)));
				  echo $ref_nos;
				?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        
		<table width="950px" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr style="font-size:13px">
                    <th width="30">SL.</th> 
                    <th width="100">Gmts. Item</th>
                    <th width="100">Body Part</th>   
                    <th width="210">Fabric Description</th>
                    <th width="120">Fabric Color</th>
                    <th width="80">Color Type</th>
                    <th width="60">UOM</th>
                    <th width="80">Booking Qty</th>
                    <th width="70">Rate</th>
                    <th>Amount</th>
                 </tr>
            </thead>
        </table>
        <div style="width:950px; max-height:300px; overflow-y:scroll" id="scroll_body"> 
        <table width="933px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		<?
		$i=1; 
		
		$revise_arr=array(1 => "1st Booking", 2 => "Revised-1", 3 => "Revised-2", 4 => "Revised-3", 5 => "Revised-4", 6 => "Revised-5", 7 => "Revised-6", 8 => "Revised-7", 9 => "Revised-8", 10 => "Revised-9", 11 => "Revised-10", 12 => "Revised-11", 13 => "Revised-12", 14 => "Revised-13", 15 => "Revised-14", 16 => "Revised-15", 17 => "Revised-16", 18 => "Revised-17", 19 => "Revised-18", 20 => "Revised-19");
		foreach($dtls_arr as $appno=>$fabd)
		{
			?>
           <tr bgcolor="#EEEFF0">
                <td colspan="10" align="center"><b style="font-size:14px"><? echo $revise_arr[$appno]; ?>: (<? echo $approval_app_arr[$appno]; ?>)</b></td>
           </tr>
            <?
			foreach($fabd as $fabdsc=>$bodypartdtls)
			{
				foreach($bodypartdtls as $bodypartname=>$itemdtls)
				{
					foreach($itemdtls as $gmtitem=>$fabcolordtls)
					{
						foreach($fabcolordtls as $fabcolor=>$colortypedtls)
						{
							foreach($colortypedtls as $colortype=>$uomdtls)
							{
								foreach($uomdtls as $uom=>$exdata)
								{
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									
									$rate=0;
									$rate=$exdata['amt']/$exdata['finqty'];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>" style="font-size:12px">
										<td width="30"><? echo $i; ?></td>   
										<td width="100" style="word-break:break-all"><? echo $garments_item[$gmtitem]; ?></td>
                                        <td width="100" style="word-break:break-all"><? echo $bodypartname; ?></td>
										<td width="210" style="word-break:break-all"><? echo $fabdsc; ?></td>
                                        <td width="120" style="word-break:break-all"><? echo $colorArr[$fabcolor]; ?></td>
                                        <td width="80" style="word-break:break-all"><? echo $color_type[$colortype]; ?></td>
                                        <td width="60" style="word-break:break-all"><? echo $unit_of_measurement[$uom]; ?></td>
                                        
										<td width="80" align="right"><? echo number_format($exdata['finqty'],2); ?></td>
										<td width="70" align="right"><? echo number_format($rate,4); ?></td>
										<td align="right" ><? echo number_format($exdata['amt'],4); ?></td>
									 </tr>
									<?
									$i++; 
									$sub_qty+=$exdata['finqty'];
									$sub_amt+=$exdata['amt'];
								}
							}
						}
					}
				}
			}
			?>
            <tr bgcolor="#EEEFF0">
                <td colspan="7" align="right"><b><? echo $revise_arr[$appno]; ?> Total : </b></td>
                <td align="right"><? echo number_format($sub_qty,2); $sub_qty=0; ?></td>
                <td align="right">&nbsp;</td>
                <td align="right"><? echo number_format($sub_amt,4); $sub_amt=0; ?></td>
            </tr>
			<?
		}
		?>
        </table>
    </div>
    <?
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
	echo "$total_data####$filename";
	exit();
}

if ($action=="report_generate2")
{
	extract($_REQUEST);
	$booking_no=str_replace("'","",$txt_booking_no);
	
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$colorArr = return_library_array("select id,color_name from lib_color ","id","color_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$fsoArr = return_library_array("select sales_booking_no, job_no from fabric_sales_order_mst where sales_booking_no='$booking_no'","sales_booking_no","job_no");
	$user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	ob_start();
	?>
     <div align="center">
    <table width="950px" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="10" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
        </tr>
        <tr class="form_caption" style="border:none;">
            <td colspan="10" align="center" style="border:none; font-size:16px; font-weight:bold">For Booking No: '<? echo $booking_no; ?>'</td>
        </tr>
    </table>
    <?
		$sql_mst="select booking_id, booking_no, company_id, buyer_id, job_no from wo_booking_mst_hstry where booking_no='$booking_no'";
		$sql_mst_res=sql_select($sql_mst);
		$job_no=$sql_mst_res[0][csf('job_no')];
		$booking_id=$sql_mst_res[0][csf('booking_id')];
		
		$sql_po="select style_ref_no, season_buyer_wise from wo_po_details_master where job_no='$job_no'";
		$sql_po_res=sql_select($sql_po);

		$sql_inter="select grouping from wo_po_break_down where job_no_mst='$job_no'";
		$sql_inter_res=sql_select($sql_inter);
		
		$approval_app_arr=array();
		$sql_app="select approved_no, approved_date from approval_history where entry_form in (7,12) and mst_id='$booking_id'";
		$sql_app_res=sql_select($sql_app);
		foreach($sql_app_res as $row)
		{
			$approval_app_arr[$row[csf('approved_no')]]=$row[csf('approved_date')];
		}
		unset($sql_app_res);
		
		$bom_dtls_arr=array();
		$bom_fab_dtls_sql="select id, body_part_id, width_dia_type, item_number_id, uom, construction, composition from wo_pre_cost_fabric_cost_dtls where job_no='$job_no'";
		$bom_fab_dtls_res=sql_select($bom_fab_dtls_sql);
		
		foreach($bom_fab_dtls_res as $row)
		{
			$bom_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
			$bom_dtls_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
			$bom_dtls_arr[$row[csf('id')]]['item_number_id']=$row[csf('item_number_id')];
			$bom_dtls_arr[$row[csf('id')]]['uom']=$row[csf('uom')];
			$bom_dtls_arr[$row[csf('id')]]['construction']=$row[csf('construction')];
			$bom_dtls_arr[$row[csf('id')]]['composition']=$row[csf('composition')];
		}
		unset($bom_fab_dtls_res);
		
		$sql_dtls="select a.approved_no, a.pre_cost_fabric_cost_dtls_id, a.construction, a.copmposition, a.gsm_weight, a.dia_width, a.fabric_color_id,a.gmts_color_id, a.color_type, a.uom, a.fin_fab_qnty, a.grey_fab_qnty, a.rate, a.amount,a.inserted_by,a.insert_date,a.updated_by,a.update_date,b.cons from wo_booking_dtls_hstry a, wo_pre_cos_fab_co_avg_con_dtls b where a.booking_no='$booking_no' and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id  and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by a.approved_no, a.pre_cost_fabric_cost_dtls_id";
		//echo $sql_dtls;
		$dtls_arr=array(); $approve_arr=array();
		$sql_dtls_res=sql_select($sql_dtls);
		foreach($sql_dtls_res as $row)
		{
			$approve_arr[$row[csf('approved_no')]]=$row[csf('approved_no')];
			$fab=""; $gmt_item=0; $body_partdtls=''; $uom=0;
			
			$gmt_item=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['item_number_id'];
			$body_partdtls=$body_part[$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part_id']];
			$uom=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['uom'];
			
			if($row[csf('construction')]=="") $row[csf('construction')]=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['construction'];
			if($row[csf('copmposition')]=="") $row[csf('copmposition')]=$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['composition'];
			
			$fab=$row[csf('construction')].' '.$row[csf('copmposition')].', '.$row[csf('gsm_weight')].', '.$row[csf('dia_width')].', '.$fabric_typee[$bom_dtls_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['width_dia_type']];
			
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['finqty']+=$row[csf('fin_fab_qnty')];
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['cons']=$row[csf('cons')];
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['gmts_color_id']=$colorArr[$row[csf('gmts_color_id')]];
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['updated_by']=$user_name_arr[$row[csf('updated_by')]];
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['update_date']=$row[csf('update_date')];
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['inserted_by']=$user_name_arr[$row[csf('inserted_by')]];
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['insert_date']=$row[csf('insert_date')];
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['greyqty']+=$row[csf('grey_fab_qnty')];
			$dtls_arr[$row[csf('approved_no')]][$gmt_item][$row[csf('gmts_color_id')]][$fab][$body_partdtls][$row[csf('color_type')]][$uom]['amt']+=$row[csf('amount')];
		}
		unset($sql_dtls_res);
		?>
        <table width="950px" cellpadding="0" cellspacing="0" >  
            <tr>
                <td width="130"><strong>Company:</strong></td>
                <td width="175"><? echo $companyArr[$sql_mst_res[0][csf('company_id')]]; ?></td>
                <td width="130"><strong>Buyer: </strong></td>
                <td width="175px"> <? echo $buyerArr[$sql_mst_res[0][csf('buyer_id')]]; ?></td>
                <td width="130"><strong>Style Ref: </strong></td>
                <td width="175"><? echo $sql_po_res[0][csf('style_ref_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Job No:</strong></td>
                <td><? echo $sql_mst_res[0][csf('job_no')]; ?></td>
                <td><strong>Season:</strong></td>
                <td> <? echo $seasonArr[$sql_po_res[0][csf('season_buyer_wise')]]; ?></td>
                <td><strong>FSO No:</strong></td>
                <td><? echo $fsoArr[$booking_no]; ?></td>
            </tr>
			<tr>
                <td><strong> Internal Booking no:</strong></td>
                <td><? 
				 $ref_no=trim($sql_inter_res[0][csf('grouping')],"'");
				 $ref_nos=implode(", ",array_unique(explode(",",$ref_no)));
				  echo $ref_nos;
				?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        
		<table width="1330px" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr style="font-size:13px">
                    <th width="30">SL.</th> 
                    <th width="100">Gmts. Item</th>
					<th width="100">Gmts. color</th>
                    <th width="100">Body Part</th>   
					<th width="120">Fabric Color</th>
                    <th width="210">Fabric Description</th>
                    <th width="80">Color Type</th>
					<th width="80">Finish Cons</th>
                    <th width="80">Booking Qty</th>
					<th width="60">UOM</th>
                    <th width="70">Rate</th>
                    <th width="100">Amount</th>
					<th width="100">Insert/Update By</th>
                    <th>Insert/Update Date</th>   
                 </tr>
            </thead>
        </table>
        <div style="width:1330px;"> 
        <table width="1330px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		<?
		$i=1; 
		
		$revise_arr=array(1 => "1st Booking", 2 => "Revised-1", 3 => "Revised-2", 4 => "Revised-3", 5 => "Revised-4", 6 => "Revised-5", 7 => "Revised-6", 8 => "Revised-7", 9 => "Revised-8", 10 => "Revised-9", 11 => "Revised-10", 12 => "Revised-11", 13 => "Revised-12", 14 => "Revised-13", 15 => "Revised-14", 16 => "Revised-15", 17 => "Revised-16", 18 => "Revised-17", 19 => "Revised-18", 20 => "Revised-19");
		foreach($dtls_arr as $appno=>$itemdtls){
			foreach($itemdtls as $gmtitem=>$fabcolordtls){
				foreach($fabcolordtls as $fabcolor=>$fabdesc){
					foreach($fabdesc as $fabdes=>$bodypart){
						foreach($bodypart as $bodypartname=>$colortypedtls){
							foreach($colortypedtls as $colortype=>$uomdtls){
								foreach($uomdtls as $uom=>$exdata){
									$gmts_wise_rowspan[$appno][$gmtitem]+=1;
									$color_wise_rowspan[$appno][$gmtitem][$fabcolor]+=1;
								}
							}
						}
					}
				}
			}
		}
		 /* echo '<pre>';
			print_r($color_wise_rowspan); die;  */ 
		foreach($dtls_arr as $appno=>$itemdtls)
		{
			?>
           <tr bgcolor="#EEEFF0">
                <td colspan="14" align="center"><b style="font-size:14px"><? echo $revise_arr[$appno]; ?>: (<? echo $approval_app_arr[$appno]; ?>)</b></td>
           </tr>
            <?
			foreach($itemdtls as $gmtitem=>$fabcolordtls)
			{
				$k=1;
				foreach($fabcolordtls as $fabcolor=>$fabdesc)
				{
					$p=1;
					foreach($fabdesc as $fabdes=>$bodypart)
					{
						foreach($bodypart as $bodypartname=>$colortypedtls)
						{
							foreach($colortypedtls as $colortype=>$uomdtls)
							{
								foreach($uomdtls as $uom=>$exdata)
								{
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									
									$rate=0;
									$rate=$exdata['amt']/$exdata['finqty'];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>" style="font-size:12px">
									<?
										if($k==1){?>
											<td width="30" rowspan="<?=$gmts_wise_rowspan[$appno][$gmtitem]; ?>"><? echo $i; ?></td>   
											<td width="100" rowspan="<?=$gmts_wise_rowspan[$appno][$gmtitem]; ?>" style="word-break:break-all"><? echo $garments_item[$gmtitem]; ?></td>
										<? $k++;}
										if($p==1){ ?>
										<td width="100" style="word-break:break-all" rowspan="<?=$color_wise_rowspan[$appno][$gmtitem][$fabcolor];?>"><? echo $colorArr[$fabcolor]; ?></td>
										<? $p++;} ?>
                                        <td width="100" style="word-break:break-all"><? echo $bodypartname; ?></td>
										<td width="120" style="word-break:break-all"><? echo $colorArr[$exdata['fabric_color_id']]; ?></td>
										<td width="210" style="word-break:break-all"><? echo $fabdes; ?></td>
                                        <td width="80" style="word-break:break-all"><? echo $color_type[$colortype]; ?></td>
                                      
                                        <td width="80" align="right"><? echo number_format($exdata['cons'],2); ?></td>
										<td width="80" align="right"><? echo number_format($exdata['finqty'],2); ?></td>
										<td width="60" style="word-break:break-all"><? echo $unit_of_measurement[$uom]; ?></td>
										<td width="70" align="right"><? echo number_format($rate,4); ?></td>
										<td width="100" align="right" ><? echo number_format($exdata['amt'],4); ?></td>
										<td width="100" align="center" ><? 
										if($exdata['updated_by']!=""){echo $exdata['updated_by'];}else{echo $exdata['inserted_by'];} ?></td>
										<td align="center" ><? if($exdata['update_date']!=""){echo $exdata['update_date'];}else{echo $exdata['insert_date'];} ?></td>
									 </tr>
									<?
									$i++;$k++;$p++;
									$sub_qty+=$exdata['finqty'];
									$sub_bok_qty+=$exdata['cons'];
									$sub_amt+=$exdata['amt'];
								}
							}
						}
						
					}
					
				}
			}
			?>
            <tr bgcolor="#EEEFF0">
                <td colspan="7" align="right"><b><? echo $revise_arr[$appno]; ?> Total : </b></td>
                <td align="right"><? echo number_format($sub_bok_qty,2); $sub_bok_qty=0; ?></td>
				<td align="right"><? echo number_format($sub_qty,2); $sub_qty=0; ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
                <td align="right"><? echo number_format($sub_amt,4); $sub_amt=0; ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
            </tr>
			<?
		}
		?>
        </table>
    </div>
    <?
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
	echo "$total_data####$filename";
	exit();
}
?>
