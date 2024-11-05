<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	$sql = "SELECT	importer_id,issuing_bank_id,lc_value,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,lc_type_id as LC_TYPE_ID,lcaf_no as LCAF_NO from com_btb_lc_master_details where id='$data'";
	$data_array=sql_select($sql); 
	// echo "dsf";die;
	//echo "select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1";
	$is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	/* if($data_array[0]['LC_TYPE_ID']==1){
		if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
	} */

	$lc_id_arr=$sc_id_arr=array();
	foreach($is_lc_sc_sql as $row)
	{
		if($row[csf('is_lc_sc')]==0){
			$sql_lc=sql_select("SELECT id,export_lc_no FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
			foreach($sql_lc as $lc_row)
			{
				$export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")];
			}
			$lc_id_arr[]=$row[csf('lc_sc_id')];
		}
		else{
			$sql_sc=sql_select("SELECT id,contract_no FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
			foreach($sql_sc as $sc_row)
			{
				$export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")];
			}
			$sc_id_arr[]=$row[csf('lc_sc_id')];
		}
		
		$lc_sc_id_arr[]=$row[csf('lc_sc_id')];

	}


	$lc_order_pcs_qty=$sc_order_pcs_qty=0;
	if(count($lc_id_arr)>0)
	{
		$lc_order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_id_arr).")","order_quantity");
	}
	if(count($sc_id_arr)>0)
	{
		$sc_order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_sales_contract_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_sales_contract_id in(".implode(',',$sc_id_arr).")","order_quantity");
	}
	$order_pcs_qty = $lc_order_pcs_qty+$sc_order_pcs_qty;


	//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0][csf("importer_id")],"company_name");
	$company_add = return_field_value("city","lib_company","id=".$data_array[0][csf("importer_id")],"city");
	$address = sql_select("SELECT id,irc_no,bin_no from lib_company where id = ".$data_array[0][csf('importer_id')]."");
	foreach($address as $row){
		$company_info[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
		$company_info[$row[csf('id')]]['bin_no'] = $row[csf('bin_no')];
	}

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
	
	
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");
	
	
	$pi_number_arr=return_library_array( "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');
	
	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");
	
	
	
	$total_pi=count($pi_number_arr);

	if($total_pi>1){
		foreach($pi_number_arr as $row){
			$pi_number .= $row.", ";
		}
		$pi_number=chop($pi_number,', ');
		foreach($pi_cate_arr as $row){
			$pi_category .= $item_category[$row].",";
		}
		$pi_category=implode(", ",array_unique(explode(",",chop($pi_category,','))));
		$pi_date='';
	}
	else
	{
		$pi_number = $pi_number_arr[$data_array[0][csf("pi_id")]];
		$pi_category= $item_category[$pi_cate_arr[$data_array[0][csf("pi_id")]]];
		$pi_date=date('d.m.Y',strtotime($pi_date));
	}

	if($data_array[0][csf("last_shipment_date")]!=''){
		$last_shipment_date=date('d.m.Y',strtotime($data_array[0][csf("last_shipment_date")]));
	}
	else
	{
		$last_shipment_date='';
	}
	
	
	if($data_array[0][csf("lc_expiry_date")]!=''){
		$lc_expiry_date=date('d.m.Y',strtotime($data_array[0][csf("lc_expiry_date")]));
	}
	else
	{
		$lc_expiry_date='';
	}
	$origin = return_field_value("country_name"," lib_country","id=".$data_array[0][csf("origin")],"country_name");
	
	$inco_term_id=$incoterm[$data_array[0][csf("inco_term_id")]];
	
	
	
	if($data_array[0][csf("cover_note_date")]!=''){
		$cover_note_date=date('d.m.Y',strtotime($data_array[0][csf("cover_note_date")]));
	}
	else
	{
		$cover_note_date='';
	}

	$nature=return_field_value("business_nature","lib_company","id=".$data_array[0][csf("importer_id")],"business_nature");
	$business_nature_arr= explode(",", $nature);
	$business_nature;
	if(count($business_nature_arr)>0){
		foreach($business_nature_arr as $row)
		{
			if($row==2){$business_nature .= "Knit ";}
			elseif($row==3){$business_nature .= "Woven ";}
			elseif($row==4){ $business_nature .= "Trims "; }
			elseif($row==5){ $business_nature .= "Print "; }
			elseif($row==6){ $business_nature .= "Embroidery "; }
			elseif($row==7){ $business_nature .= "Wash "; }
			elseif($row==8){ $business_nature .= "Yarn Dyeing "; }
			elseif($row==9){ $business_nature .= "AOP "; }
			elseif($row==100){ $business_nature .= "Sweater "; }
		}
	}
	?>
    
	<style>
		body {
            margin: 0;
            padding: 0;
            font-size: 90%;
            background: url("../application_form/form_image/topCut.gif");
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }
		#position1 {position: absolute; margin-top: 75px; margin-left: 250px; }
        #position2 {position: absolute;margin-top: 240px;margin-left: 50px;width:350px;}
        #position3 {position: absolute;margin-top: 285px;margin-left: 470px; }
        #position4 {position: absolute;margin-top: 420px;margin-left: 180px;}
        #position5 {position: absolute;margin-top: 420px;margin-left: 460px;}
        #position6 {position: absolute;margin-top: 530px;margin-left: 220px;}
        #position7 {position: absolute;margin-top: 540px;margin-left: 295px;}
        #position8 {position: absolute;margin-top: 560px;margin-left: 420px;}
        #position9 {position: absolute;margin-top: 597px;margin-left: 180px; }
        #position10 {position: absolute;margin-top: 597px;margin-left: 470px;}
        #position11 {position: absolute;margin-top: 597px; margin-left: 740px;}
        #position12 {position: absolute;margin-top: 610px;margin-left: 110px;}
        #position13 {position: absolute;margin-top: 610px;margin-left: 420px;}
        #position14 {position: absolute;margin-top: 610px;margin-left: 640px;}
        #position15 {position: absolute;margin-top: 645px;margin-left: 250px;}
        #position16 {position: absolute;margin-top: 695px; margin-left: 450px; }
        #position17 {position: absolute;margin-top: 770px;margin-left: 795px;width: 100px;}
        #position18 {position: absolute;margin-top: 785px;margin-left: 690px;}
        #position19 {position: absolute;margin-top: 805px;margin-left: 580px;}
        #position20 {position: absolute;margin-top: 820px;margin-left: 325px;}
        #position21 {position: absolute;margin-top: 835px;margin-left: 220px;}
        #position22 {position: absolute;margin-top: 905px;margin-left: 180px;}
        #position23 {position: absolute;margin-top: 960px;margin-left: 120px;}
        #position24 {position: absolute;margin-top: 960px;margin-left: 290px;}
        #position25 {position: absolute;margin-top: 1000px;margin-left: 200px;}
        #position26 {position: absolute;margin-top: 1160px;margin-left: 100px;}
        #position27 {position: absolute;margin-top: 1160px;margin-left: 350px;}
        #position28 {position: absolute;margin-top: 1200px;margin-left: 150px;}

	</style>
	<body>
		<div id="position1"><? echo $branch;?></div>
		<div id="position2"><? echo $company_name;?><br /><? echo $company_add;?></div>
		<div id="position3">
		<? echo $currency_sign." ".number_format($data_array[0][csf('lc_value')],2)."<br>".number_to_words($data_array[0][csf('lc_value')]);;?>
		</div>
		<div id="position4"><? echo $data_array[0][csf("tenor")];?> Day's</div>
		<div id="position5"><? echo $supplier_name;?><br /><? echo $supplier_add;?></div>
		<div id="position6">
			<!-- <? echo $pi_category;?> -->
			100% Export Oriented <? echo $business_nature; ?> Garments 
		</div>
		<div id="position7"><? if($total_pi>1){ echo $pi_number;}else{ echo "<br>".$pi_number;}?></div>
		<div id="position8"><? echo $pi_date;?></div>
		<div id="position9"><? echo $data_array[0][csf("port_of_loading")];?></div>
		<div id="position10"><? echo $data_array[0][csf("port_of_discharge")];?></div>
		<div id="position11"><? echo $shipment_mode[$data_array[0][csf("delivery_mode_id")]];?></div>
		<div id="position12"><? echo $last_shipment_date;?></div>
		<div id="position13"><? echo $lc_expiry_date;?></div>
		<div id="position14"><? echo $data_array[0][csf("inco_term_place")];?></div>
		<div id="position15"><? echo $data_array[0][csf("doc_presentation_days")];?></div>
		<div id="position16"><? echo $origin;?></div>
		<div id="position17"><? echo $branch;?></div>
		<div id="position18"><? echo $branch;?></div>
		<div id="position19"><? echo $inco_term_id;?></div>
		<div id="position20"><? echo $branch;?></div>
		<div id="position21"><? echo $inco_term_id;?></div>
		<div id="position22"><? echo $data_array[0][csf("insurance_company_name")];?></div>
		<div id="position23"><? echo $data_array[0][csf("cover_note_no")];?></div>
		<div id="position24"><? echo $cover_note_date;?></div>
		<div id="position25">LC/SC: <? echo implode(', ',$export_lc_sc_no_arr);?></div>
		<div id="position26"><? echo $company_info[$data_array[0][csf("importer_id")]]['irc_no'];?> </div>
		<div id="position27"><? echo $data_array[0]["LCAF_NO"];?></div>
		<div id="position28">Gmts Qty: <? echo number_format($order_pcs_qty);?> pcs </div>

	</body>

    <?
	exit();
}

?>