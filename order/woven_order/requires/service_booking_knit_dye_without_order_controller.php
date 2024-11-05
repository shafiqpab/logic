<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

if ($action=="load_drop_down_buyer")
{	
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_uom")
{	
	echo create_drop_down( "cbo_uom", 130, $unit_of_measurement,"", 1, "-- Select --", $data, "","","" );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	$data=explode("_",$data);
	if($data[0]==3)
	{	
		echo create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b  where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data[1]' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in (20,21,25)) order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_knit_dye_without_order_controller');",0 );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Supplier --", $data[1], "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_knit_dye_without_order_controller');",0 );
	}
	exit();
} 
if ($action=="load_drop_down_supplier_new")
{
	$data=explode("_",$data);
	if($data[0]==3)
	{	
		$sql = "SELECT DISTINCT a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b  where a.status_active =1 and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data[1]' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in (20,21,25)) UNION ALL SELECT DISTINCT a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b , WO_NON_ORD_KNITDYE_BOOKING_MST c  where a.status_active IN(1,3) and a.is_deleted=0 and b.supplier_id=a.id and b.tag_company='$data[1]' and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in (20,21,25)) and c.supplier_id = a.id order by supplier_name";
		
		echo create_drop_down( "cbo_supplier_name", 130, "$sql","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_knit_dye_without_order_controller');",0 );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Supplier --", $data[1], "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_knit_dye_without_order_controller');",0 );
	}
	exit();
} 
if($action=="load_drop_down_attention")
{
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
}

if ($action=="load_drop_down_fabric_description")
{
	$fabric_description=array();
	$data=explode("_",$data);
	//print_r($data);die;
	if($data[0]!='')
	{
		if($data[1]==1)
		{
			
			$sql=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where b.id=$data[0] and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 order by a.id");
			
			foreach($sql as $row)
			{
			$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')].",".$color_library[$row[csf('fabric_color')]];	
			}
		}
		else if($data[1]==2)
		{
			$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
			
			//echo "select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6  and a.to_order_id=$data[0] and c.item_category=13  and a.status_active=1 and a.is_deleted=0 order by c.id";
			$sql=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6  and a.to_order_id=$data[0] and c.item_category=13  and a.status_active=1 and a.is_deleted=0 order by c.id");
			foreach($sql as $row)
			{
			$fabric_description[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
			}
		}
	}
	echo create_drop_down( "cbo_fabric_description", 130, $fabric_description,"", 1, "-- Select --", $selected,"get_related_data(this.value)" );
	exit();
}

if($action=="load_drop_down_gmts_color")
{ 
	//$color_array=array();
	
	$sql=sql_select("select a.id,a.gmts_color,a.color_all_data,a.entry_form_id from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where b.id=$data and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0");
	foreach($sql as $row)
	{
		if($row[csf('entry_form_id')]==140)
		{
			$gmts_color=explode("_",$row[csf('color_all_data')]);
			$gmts_colors=$gmts_color[2];	
		}
		else
		{
			$gmts_colors=$row[csf('gmts_color')];	
		}
		$color_array[$gmts_colors]=$color_library[$gmts_colors];	
	}
	echo create_drop_down( "cbo_gmtcolor", 130, $color_array,"", 1, "-- Select --", $gmts_colors, "",1,"" );
	exit();
}

if($action=="get_related_data")
{ 
   $data=explode("_",$data);
	if($data[1]==1)
	{
		$sql=sql_select("select a.id,a.body_part,a.fabric_description,a.fabric_color,a.gmts_color,a.gsm_weight,a.dia_width,a.dia,a.color_all_data,a.entry_form_id,a.uom from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.id=$data[0] and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0");
		foreach($sql as $row)
		{
			//if($row[csf("dia_width")]) $diaCond=$row[csf("dia_width")];else $diaCond=$row[csf("dia")];
			$diaCond=$row[csf("dia")];
			echo "document.getElementById('txt_gsm').value = '".$row[csf("gsm_weight")]."';\n";  
			echo "document.getElementById('txt_fin_dia').value = '".$diaCond."';\n"; 
			echo "load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', '".$row[csf("uom")]."', 'load_drop_down_uom', 'uom_td' )\n";

			if($row[csf('entry_form_id')]==140)
			{
				$gmts_color=explode("_",$row[csf('color_all_data')]);
				$gmts_colors=$gmts_color[2];
				
			}
			else
			{
				$gmts_colors=$row[csf("gmts_color")];
			}
			echo "document.getElementById('cbo_gmtcolor').value = '".$gmts_colors."';\n"; 
		}
	}
	if($data[1]==2)
	{
	}
	exit();
}

if($action=="check_conversion_rate")
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();	
}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	?>
	<script>
		function js_set_value(booking_no)
		{
			document.getElementById('booking_id').value=booking_no;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                	<tr>
                        <th colspan="2">&nbsp;</th>
                        <th><? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                        <th colspan="3"></th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Booking No</th>
                        <th width="200">Date Range</th>
                        <th>&nbsp;</th>  
                    </tr>         
                </thead>
                <tr class="general">
                    <td><input type="hidden" id="booking_id">
                    	<? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name, "load_drop_down( 'sample_booking_non_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    </td>
                    <td id="buyer_td">
                    	<? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?>
                    </td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td> 
                    <td align="center">
                    	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value, 'create_fab_booking_search_list_view', 'search_div', 'service_booking_knit_dye_without_order_controller','setFilterGrid(\'table_body\',-1)')" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="5"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <br>
            <div id="search_div"></div>
        </form>
    </div>
    </body>           
    	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_fab_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date =""; 
	}
	else if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]'   "; else $booking_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
	}
	
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$arr=array (2=>$buyer_arr,3=>$item_category,4=>$fabric_source,5=>$suplier,6=>$style_library,7=>$approved,8=>$is_ready);

    $sql= "select a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode, b.style_id, b.style_des from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0 where $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and a.status_active=1 and a.is_deleted=0  order by a.id DESC";
	//and( a.entry_form_id is null or a.entry_form_id =0 )
	//echo $sql;
	?>
    <table class="rpt_table scroll" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" >
    	<thead>
            <th width="30">Sl</th>
            <th width="70">Booking No</th>
            <th width="70">Booking Date</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="90">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="100">Supplier</th>
            <th width="50">Style</th>
            <th width="80">Style Desc.</th>
            <th width="50">Approved</th>
            <th>Is-Ready</th>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:1000px" >
    <table width="980" class="rpt_table" id="table_body" border="1" rules="all">
        <tbody>
		<?
        $i=1;
        $sql_data=sql_select($sql);
        foreach($sql_data as $row)
        {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('booking_no')]; ?>')" style="cursor:pointer">
                <td width="30"><? echo $i;?></td>
                <td width="70" align="center"><? echo $row[csf('booking_no_prefix_num')];?></td>
                <td width="70"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>
                <td width="100"><? echo $comp[$row[csf('company_id')]];?></td>
                <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                <td width="90"><? echo $item_category[$row[csf('item_category')]];?></td>
                <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
                <td width="80"><? echo $pay_mode[$row[csf('pay_mode')]];?></td>
                <td width="100">
                	<? if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) echo $comp[$row[csf('supplier_id')]]; else echo $suplier[$row[csf('supplier_id')]]; ?>
                </td>
                <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $style_library[$row[csf('style_id')]];?></td>
                <td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_des')];?></td>
                <td width="50"><? echo $approved[$row[csf('is_approved')]];?></td>
                <td><? echo $is_ready[$row[csf('ready_to_approved')]];?></td>
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

if ($action=="populate_order_data_from_search_popup")
{
	//echo "select a.company_id,a.buyer_id from wo_non_ord_samp_booking_mst a where a.id in (".$data.") ";die;
	$data_array=sql_select("select a.company_id,a.buyer_id from wo_non_ord_samp_booking_mst a where a.id in (".$data.") ");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";   
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', '".$data."_1', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		echo "load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', '".$data."', 'load_drop_down_gmts_color', 'gmtcolor_td' )\n";
	}
	exit();
}

if ($action=="knit_dye_detls_list_view")
{
	$data=explode("_",$data);
	$fabric_description=array();
	$fabric_description2=array();
	$color=array();
	
	$sql=sql_select("select a.id,a.color_all_data,a.entry_form_id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where b.id=$data[1] and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 order by a.id");
	foreach($sql as $row)
	{
		$entry_form_id=$row[csf('entry_form_id')];
		if($row[csf('entry_form_id')]==140)
		{
			$gmts_color=explode("_",$row[csf('color_all_data')]);
			$gmts_colors=$gmts_color[2];	
		}
		else
		{
			$gmts_colors=$row[csf('gmts_color')];	
		}
		$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')].",".$color_library[$row[csf('fabric_color')]];
		$color[$row[csf('id')]][fabric_color]=$color_library[$row[csf('fabric_color')]];
		$color[$row[csf('id')]][gmts_color]=$color_library[$gmts_colors];
	}
	
	$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$sql=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6  and a.to_order_id=$data[1] and c.item_category=13  and a.status_active=1 and a.is_deleted=0 order by c.id");
	foreach($sql as $row)
	{
		$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
	}
	
	$sql="select id, fabric_source, fab_des_id, gsm, dia, uom, artwork_no, wo_qty, rate, amount, gmts_color, delivery_start_date, delivery_end_date, remarks, process_id from wo_non_ord_knitdye_booking_dtl where mst_id=$data[0] and status_active=1 and is_deleted=0";
	?>
	<table class="rpt_table" border="1" width="940" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="200">Fabric Description</th>
            <th width="60">Gsm</th>
            <th width="60">Dia</th>
            <th width="60">UOM</th>
            <th width="100">Artwork No</th>
            <th width="100">Gmts. Color</th>
            <th width="100">Process</th>
            <th width="70">Delivery Start Date</th>
            <th width="70">Delivery End Date</th>
            <th width="60">WO. Qty</th>
            <th width="60">Rate</th>
            <th width="60">Amount</th>
            <th>Remarks</th>
        </thead>
        <tbody>
			<?
            $dataArray=sql_select($sql);
            $i=1;
            foreach($dataArray as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="get_dtls_data(<? echo $row[csf('id')]?>)" style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="200">
                    <?
					if($row[csf('fabric_source')]==1)
					{
                    	echo $fabric_description[$row[csf('fab_des_id')]]; 
					}
					else
					{
						echo $fabric_description2[$row[csf('fab_des_id')]]; 
					}
                    ?>
                    </td>
                    <td width="60"><? echo $row[csf('gsm')]; ?></td>
                    <td width="60"><? echo $row[csf('dia')]; ?></td>
                    <td width="60"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td width="100"><? echo $row[csf('artwork_no')]; ?></td>
                    <td width="100" align="center"><? echo $color[$row[csf('fab_des_id')]][gmts_color]; ?></td>
                    <td width="100" align="center"><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('delivery_start_date')]); ?></td>
                    <td width="70"><? echo change_date_format($row[csf('delivery_end_date')]); ?></td>
                    <td width="60" align="right"><? echo $row[csf('wo_qty')]; ?></td>
                    <td width="60" align="right"><? echo $row[csf('rate')]; ?></td>
                    <td width="60" align="right"><? echo $row[csf('amount')]; ?></td>
                    <td><? echo $row[csf('remarks')]; ?></td>
				</tr>
				<?	
				$i++;
            }
            ?>
        </tbody>
	</table>
	<?
	exit();
}

if ($action=="populate_data_dtls_from_search_popup")
{
	$ex_data=explode('__',$data);
	$sql="select a.id, a.fab_des_id, a.fabric_source, a.gsm, a.dia, a.uom, a.artwork_no, a.wo_qty, a.rate, a.amount, a.mc_gauge, a.mc_dia, a.dia_width_type, a.stitch_length, a.gmts_color, a.delivery_start_date, a.delivery_end_date, a.remarks, a.process_id , b.knit_dye_source,b.company_id from wo_non_ord_knitdye_booking_dtl a , WO_NON_ORD_KNITDYE_BOOKING_MST b  where a.id=$ex_data[0]  and b.id = a.mst_id and a.status_active=1 and a.is_deleted=0";
	 
	 $dataArray=sql_select($sql);
	 foreach( $dataArray as $row)
	 {

		echo "load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', '".$row[csf('knit_dye_source')]."_".$row[csf("company_id")]."', 'load_drop_down_supplier_new', 'suplier_td' )\n";


		echo "load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', '".$ex_data[1]."_".$row[csf("fabric_source")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		echo "load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', '".$ex_data[1]."', 'load_drop_down_gmts_color', 'gmtcolor_td' )\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";  
		echo "document.getElementById('txt_gsm').value = '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('cbo_fabric_description').value = '".$row[csf("fab_des_id")]."';\n";  
		echo "document.getElementById('txt_fin_dia').value = '".$row[csf("dia")]."';\n"; 
		echo "document.getElementById('txt_mc_gg').value = '".$row[csf("mc_gauge")]."';\n"; 
		echo "document.getElementById('txt_mc_dia').value = '".$row[csf("mc_dia")]."';\n";
		
		echo "document.getElementById('txt_stitchLength').value = '".$row[csf("stitch_length")]."';\n"; 
		echo "document.getElementById('cbo_diaType').value = '".$row[csf("dia_width_type")]."';\n";
		 
		echo "document.getElementById('cbo_uom').value = '".$row[csf("uom")]."';\n"; 
		echo "document.getElementById('txt_art_work').value = '".$row[csf("artwork_no")]."';\n";
		echo "document.getElementById('cbo_gmtcolor').value = '".$row[csf("gmts_color")]."';\n"; 
		echo "document.getElementById('txt_wo_qty').value = '".$row[csf("wo_qty")]."';\n";  
		echo "document.getElementById('txt_rate').value = '".$row[csf("rate")]."';\n"; 
		echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n"; 
		echo "document.getElementById('cbo_knitdye_type').value = '".$row[csf("process_id")]."';\n";  
		echo "document.getElementById('txt_dev_start_date').value = '".change_date_format($row[csf("delivery_start_date")],"dd-mm-yyyy","-")."';\n"; 
		echo "document.getElementById('txt_dev_end_date').value = '".change_date_format($row[csf("delivery_end_date")],"dd-mm-yyyy","-")."';\n";  
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n"; 
		echo "document.getElementById('dtls_id').value = '".$row[csf("id")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_knit_dye_non_ord_booking',1);\n"; 
	 }
	 exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$fabric_descriptionId=str_replace("'","",$cbo_fabric_description);
	$mst_id=str_replace("'","",$txt_mst_id);
	$sm_booking_id=str_replace("'","",$txt_fabric_booking_id);
	$wo_qty=str_replace("'","",$txt_wo_qty);
	$cbo_knitdye_type=str_replace("'","",$cbo_knitdye_type);
	$sql_non_booking="select b.id,b.grey_fabric from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.id=b.booking_mst_id and a.booking_type=4  and a.company_id=$cbo_company_name and a.booking_no=$txt_fabric_booking and b.id=$fabric_descriptionId and a.status_active=1 and b.status_active=1  ";
	 //echo "10**".$sql_non_booking;die;
	$sql_non_booking_res=sql_select($sql_non_booking);
	$sample_req_qty=0;
	foreach($sql_non_booking_res as $row)
	{
		$sample_req_qty+=$row[csf('grey_fabric')];
	}
	$smp_mst_cond="";
	if ($operation==1)// Insert Here
	{
		$smp_mst_cond="and a.id!=$mst_id";
	}
	
	$sql_service_priv_booking="select a.booking_no,b.fab_des_id,b.wo_qty from wo_non_ord_knitdye_booking_mst a,wo_non_ord_knitdye_booking_dtl b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.fab_booking_id=$sm_booking_id and b.fab_des_id=$fabric_descriptionId and b.process_id=$cbo_knitdye_type and a.status_active=1 and b.status_active=1 $smp_mst_cond ";
	 //echo "10**".$sql_service_priv_booking;die;
	$sql_non_booking_res_priv=sql_select($sql_service_priv_booking);
	$service_priv_wo_qty=0;
	if(count($sql_non_booking_res_priv)>0)
	{
		foreach($sql_non_booking_res_priv as $row)
		{
			$service_priv_wo_qty+=$row[csf('wo_qty')];
			$service_priv_woArr[$row[csf('booking_no')]]=$row[csf('booking_no')];
		}
		$wo_qty_chk=$wo_qty+$service_priv_wo_qty;
	}
	// $msg="Service Qty is greater than Sample Wo Req.Qty";
	//	echo "17**".$msg.'**'.$sample_req_qty.'**'.$wo_qty.'**'.$service_priv_wo_qty;
	if ($operation!=2)// Delete no need  
	{
		if($wo_qty_chk>$sample_req_qty)
		{
			$msg="Service Qty is greater than Sample Wo Req. Qty.,Previous Booking NO-".implode(",",$service_priv_woArr);;
			echo "17**".$msg.'**'.$sample_req_qty.'**'.$wo_qty.'**'.$service_priv_wo_qty;
			
			disconnect($con);die;
		}
	}
	
	if ($operation==0)// Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		$response_booking_no="";
		
		
		
		if(str_replace("'","",$txt_booking_no)=="")
		{
			$new_wo_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SBKD', date("Y",time()), 5,"select id, booking_no_prefix, prefix_num from wo_non_ord_knitdye_booking_mst where company_id=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "prefix_num" ));
			
			$id=return_next_id("id","wo_non_ord_knitdye_booking_mst",1);
			//echo "10**$id##".jahid ;die;
			$field_array="id, booking_no_prefix, prefix_num, booking_no, fab_booking_id, company_id, buyer_id, booking_date, currency_id, exchange_rate, pay_mode, source, knit_dye_source, supplier_id, attention, tenor, inserted_by, insert_date, status_active, is_deleted";
			//$field_array="id,wo_no_prefix, wo_no_prefix_num, wo_no,fab_booking_id, fab_booking_no, company_id, buyer_id, booking_date, currency_id, exchange_rate, pay_mode, source, aop_source, supplier_id, attention, is_approved, ready_to_approved, is_deleted, status_active, inserted_by, insert_date";
			$data_array ="(".$id.",'".$new_wo_no[1]."',".$new_wo_no[2].",'".$new_wo_no[0]."',".$txt_fabric_booking_id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_booking_date.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$cbo_knitdye_source.",".$cbo_supplier_name.",".$txt_attention.",".$txt_tenor.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$response_wo_no=$new_wo_no[0];
			$mst_id=$id;
			
			$id_dtls=return_next_id( "id", "wo_non_ord_knitdye_booking_dtl", 1 ) ;
			$field_array_d="id,	mst_id, booking_no, fabric_source, fab_des_id, gsm, dia, mc_dia, mc_gauge, dia_width_type, stitch_length, uom, artwork_no, gmts_color, wo_qty, rate, amount, delivery_start_date, delivery_end_date, remarks, inserted_by, insert_date, process_id";
			$data_array_d ="(".$id_dtls.",".$id.",'".$new_wo_no[0]."',".$cbo_fabric_source.",".$cbo_fabric_description.",".$txt_gsm.",".$txt_fin_dia.",".$txt_mc_dia.",".$txt_mc_gg.",".$cbo_diaType.",".$txt_stitchLength.",".$cbo_uom.",".$txt_art_work.",".$cbo_gmtcolor.",".$txt_wo_qty.",".$txt_rate.",".$txt_amount.",".$txt_dev_start_date.",".$txt_dev_end_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_knitdye_type.")";
			//echo "10**insert into wo_non_ord_aop_booking_dtls($field_array_d)values".$data_array_d; die;
			$rID=sql_insert("wo_non_ord_knitdye_booking_mst",$field_array,$data_array,0);
			$rID1=sql_insert("wo_non_ord_knitdye_booking_dtl",$field_array_d,$data_array_d,0);
		}
		else
		{
			$field_array_up="booking_date*currency_id*exchange_rate*pay_mode*source*knit_dye_source*supplier_id*attention*tenor*updated_by*update_date";
			$data_array_up ="".$txt_booking_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_knitdye_source."*".$cbo_supplier_name."*".$txt_attention."*".$txt_tenor."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$response_wo_no=str_replace("'","",$txt_booking_no);
			$mst_id=str_replace("'","",$txt_mst_id);
			$id_dtls=return_next_id( "id", "wo_non_ord_knitdye_booking_dtl", 1 ) ;
			$field_array_d="id, mst_id, booking_no, fabric_source, fab_des_id, gsm, dia, mc_dia, mc_gauge, dia_width_type, stitch_length, uom, artwork_no, gmts_color, wo_qty, rate, amount, delivery_start_date, delivery_end_date, remarks, inserted_by, insert_date, process_id";
			$data_array_d ="(".$id_dtls.",".$mst_id.",'".$response_wo_no."',".$cbo_fabric_source.",".$cbo_fabric_description.",".$txt_gsm.",".$txt_fin_dia.",".$txt_mc_dia.",".$txt_mc_gg.",".$cbo_diaType.",".$txt_stitchLength.",".$cbo_uom.",".$txt_art_work.",".$cbo_gmtcolor.",".$txt_wo_qty.",".$txt_rate.",".$txt_amount.",".$txt_dev_start_date.",".$txt_dev_end_date.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_knitdye_type.")";
			//echo "10**".$mst_id.'**'.$field_array_d.'**'.$data_array_d; die;
			$rID=sql_update("wo_non_ord_knitdye_booking_mst",$field_array_up,$data_array_up,"id","".$txt_mst_id."",0);
			$rID1=sql_insert("wo_non_ord_knitdye_booking_dtl",$field_array_d,$data_array_d,0);
		}
		
		check_table_status( $_SESSION['menu_id'],0); 
		
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".$response_wo_no."**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$response_wo_no."**".$mst_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);  
				echo "0**".$response_wo_no."**".$mst_id;
			}
			else{
				oci_rollback($con);  
				echo "10**".$response_wo_no."**".$mst_id;
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
		//========Issue to Fin Process Found====================
		 $updtlsid=str_replace("'",'',$dtls_id);
		//$updtlsid=implode(",",array_filter(array_unique(explode(",",$updtlsid))));
		$dtlsidCond="";
		if($updtlsid!="") $dtlsidCond="and b.booking_dtls_id in ($updtlsid)";
		$issueTofinProcess_mrr=0;
		$sqlissueFinProcess=sql_select("select a.entry_form,a.recv_number,a.receive_basis,
		b.batch_issue_qty,
		b.booking_dtls_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$response_wo_no  and a.entry_form in(554,91) and a.status_active=1 $dtlsidCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		//and a.receive_basis=2
		//echo "issFinPrcess**select a.recv_number from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no and a.receive_basis=2 and a.entry_form=91 and a.status_active=1 $dtlsidCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";die;
		$tot_batch_issue_qty=0;$issueTofinProcess_mrr="";
		foreach($sqlissueFinProcess as $rows){
			if($rows[csf('entry_form')]==91)
			{
			$issueTofinProcess_mrr.=$rows[csf('recv_number')].',';
			$tot_batch_issue_qtyArr[$rows[csf('booking_dtls_id')]]+=$rows[csf('batch_issue_qty')];
			}
			else
			{
			$issueTofinProcess_mrr.=$rows[csf('recv_number')].',';
			$tot_batch_issue_ret_qtyArr[$rows[csf('booking_dtls_id')]]+=$rows[csf('batch_issue_qty')];
			}
		}
		//print_r($tot_batch_issue_qtyArr);
		//echo "10**=A";die;
		//For Validation Check //-----issue Id=18501
			 
				 //$updtlsid="updateid_".$i;
				 $txt_woqnty="txt_woqnty_".$i;
				 $updateId=str_replace("'",'',$dtls_id);
				 $woqnty_chk=str_replace("'",'',$txt_wo_qty);
				if(trim($updateId)!="")
				{
					  $tot_batch_issue_qty=$tot_batch_issue_qtyArr[$updateId];
					  $tot_batch_issue_ret_qty=$tot_batch_issue_ret_qtyArr[$updateId];
					  $tot_batch_issue_bal_qty=$tot_batch_issue_qty-$tot_batch_issue_ret_qty;
					  if($tot_batch_issue_bal_qty>0 && ($tot_batch_issue_bal_qty>$woqnty_chk)) // $tot_batch_issue_bal_qty>$woqnty_chk ||
					  {
						 $issueTofinProcess_noAll=rtrim($issueTofinProcess_mrr,',');
						  $issue_mrr_no=implode(",",array_unique(explode(",", $issueTofinProcess_noAll)));
						  $msg="You can revised up to issue qty.";
						  echo "issFinPrcess**".str_replace("'","",$response_wo_no)."**".$issue_mrr_no.'**'.$msg.'**'.$tot_batch_issue_qty.'='.$tot_batch_issue_ret_qty.'='.$woqnty_chk;
							disconnect($con);die;
					  }
				}
			 
			 //========End===Issue to Fin Process Found====================
			 
		$field_array_up="booking_date*currency_id*exchange_rate*pay_mode*source*knit_dye_source*supplier_id*attention*tenor*updated_by*update_date";
		$data_array_up ="".$txt_booking_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_knitdye_source."*".$cbo_supplier_name."*".$txt_attention."*".$txt_tenor."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 
		$field_array_up_d="fab_des_id*gsm*dia*mc_dia*mc_gauge*dia_width_type*stitch_length*uom*artwork_no*wo_qty*rate*amount*delivery_start_date*delivery_end_date*remarks*updated_by*update_date*process_id";
		$data_array_up_d ="".$cbo_fabric_description."*".$txt_gsm."*".$txt_fin_dia."*".$txt_mc_dia."*".$txt_mc_gg."*".$cbo_diaType."*".$txt_stitchLength."*".$cbo_uom."*".$txt_art_work."*".$txt_wo_qty."*".$txt_rate."*".$txt_amount."*".$txt_dev_start_date."*".$txt_dev_end_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_knitdye_type."";
		
		$rID=sql_update("wo_non_ord_knitdye_booking_mst",$field_array_up,$data_array_up,"id","".$txt_mst_id."",0);
		$rID1=sql_update("wo_non_ord_knitdye_booking_dtl",$field_array_up_d,$data_array_up_d,"id","".$dtls_id."",0);
		//echo "10**".$rID.'**'.$rID1.'**'.$data_array_up_d;die;
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$txt_mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$txt_mst_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$txt_mst_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$txt_mst_id);
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
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_non_ord_knitdye_booking_dtl",$field_array,$data_array,"id","".$dtls_id."",1);
        
		if($db_type==0)
		{
			if($rID){
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
			if($rID){
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

if ($action=="knit_dye_without_order_booking_search")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	?>
	<script>
		function js_set_value(wo_id)
		{
			document.getElementById('selected_booking').value=wo_id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
            	<tr>
                    <th colspan="7"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
            	</tr>
                <tr>
                    <th width="150">Company Name</th>
                    <th width="150">Buyer Name</th>
                    <th width="100">WO No</th>
                    <th width="100">Fab. Booking No</th>
                    <th width="130" colspan="2">WO Date Range</th>
                    <th>&nbsp;</th>           
                </tr>
            </thead>
            <tr class="general">
                <td><input type="hidden" id="selected_booking">
                    <? echo create_drop_down( "cbo_company_mst", 150, "select id, company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name, "load_drop_down( 'service_booking_knit_dye_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                </td>
                <td id="buyer_td">
                	<? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?>
                </td>
                <td><input name="txt_wo_prifix" id="txt_wo_prifix" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_fabbooking" id="txt_fabbooking" class="text_boxes" style="width:90px"></td>
                 
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_wo_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_fabbooking').value, 'create_booking_search_list_view', 'search_div', 'service_booking_knit_dye_without_order_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="7"><? echo load_month_buttons(1); ?></td>
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
	if ($data[0]!=0) $company=" a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[5]==1)
	{
		if (str_replace("'","",$data[4])!="") $wo_cond=" and a.prefix_num='$data[4]'"; else  $wo_cond="";
		if (str_replace("'","",$data[6])!="") $fabBookingCond=" and b.booking_no_prefix_num='$data[6]'"; else $fabBookingCond="";
	}
	else if($data[5]==4 || $data[5]==0)
	{
		if (str_replace("'","",$data[4])!="") $wo_cond=" and a.prefix_num like '%$data[4]%'"; else  $wo_cond="";
		if (str_replace("'","",$data[6])!="") $fabBookingCond=" and b.booking_no_prefix_num like '%$data[6]%'"; else  $fabBookingCond="";
	}
	else if($data[5]==2)
	{
		if (str_replace("'","",$data[4])!="") $wo_cond=" and a.prefix_num like '$data[4]%'"; else  $wo_cond="";
		if (str_replace("'","",$data[6])!="") $fabBookingCond=" and b.booking_no_prefix_num like '$data[6]%'"; else  $fabBookingCond="";
	}
	else if($data[5]==3)
	{
		if (str_replace("'","",$data[4])!="") $wo_cond=" and a.prefix_num like '%$data[4]'"; else  $wo_cond="";
		if (str_replace("'","",$data[6])!="") $fabBookingCond=" and b.booking_no_prefix_num like '%$data[6]'"; else  $fabBookingCond="";
	} 
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	//$non_ord_booking_arr=return_library_array( "select id, booking_no from wo_non_ord_samp_booking_mst",'id','booking_no');
	
	$sql= "select a.id, a.prefix_num, a.booking_no, a.fab_booking_id, a.buyer_id, a.booking_date, a.currency_id, a.exchange_rate, a.source, a.knit_dye_source, a.supplier_id, a.attention, b.booking_no as smnbooking_no from wo_non_ord_knitdye_booking_mst a, wo_non_ord_samp_booking_mst b where $company $buyer $wo_cond $booking_date and b.id=a.fab_booking_id $fabBookingCond  order by a.id DESC";

	foreach (sql_select($sql) as $val) 
	{
		if($val[csf("knit_dye_source")]==1) $supplier_array[$val[csf("id")]]=$comp[$val[csf("supplier_id")]];
		else if($val[csf("knit_dye_source")]==3) $supplier_array[$val[csf("id")]]=$suplier[$val[csf("supplier_id")]];
	}

	$arr=array (2=>$buyer_arr,4=>$source,5=>$supplier_array);
	//echo $sql;
	echo  create_list_view("list_view", "WO No,WO Date,Buyer,Booking No,Source,Supplier", "100,70,100,100,80,100","750","320",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_id,0,source,id", $arr , "prefix_num,booking_date,buyer_id,smnbooking_no,source,id", '','','0,3,0,0,0,0','','');
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$non_ord_booking_arr=return_library_array( "select id, booking_no from wo_non_ord_samp_booking_mst",'id','booking_no');
	$sql= "select id, company_id, prefix_num, booking_no, fab_booking_id, buyer_id, booking_date, currency_id, exchange_rate, source, pay_mode, knit_dye_source, supplier_id, attention,tenor from wo_non_ord_knitdye_booking_mst where id=$data"; 
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/service_booking_knit_dye_without_order_controller', '".$row[csf('knit_dye_source')]."_".$row[csf("company_id")]."', 'load_drop_down_supplier', 'suplier_td' )\n";
		echo "document.getElementById('txt_mst_id').value = '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('txt_fabric_booking').value = '".$non_ord_booking_arr[$row[csf("fab_booking_id")]]."';\n";  
		echo "document.getElementById('txt_fabric_booking_id').value = '".$row[csf("fab_booking_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_knitdye_source').value = '".$row[csf("knit_dye_source")]."';\n";
		//echo "document.getElementById('cbo_knitdye_type').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
	}
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
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_termcondi_details");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
			}
		}

		function fn_deletebreak_down_tr(rowNo) 
		{   
			var numRow = $('table#tbl_termcondi_details tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}
		}

		function fnc_fabric_booking_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}
				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../",i);
			}
			var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
			//freeze_window(operation);
			http.open("POST","trims_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
		}

		function fnc_fabric_booking_terms_condition_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					parent.emailwindow.hide();
				}
			}
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<? echo load_freeze_divs ("../../../",$permission);  ?>
    <fieldset>
    <form id="termscondi_1" autocomplete="off">
        <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
        <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
            <thead>
            	<th width="50">Sl</th><th width="530">Terms</th><th>&nbsp;</th>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
                if( count($data_array)>0)
                {
					$i=0;
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="settr_1" align="center">
                            <td><? echo $i; ?></td>
                            <td>
                            	<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                            </td>
                            <td> 
                                <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                            </td>
						</tr>
						<?
					}
				}
				else
				{
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="settr_1" align="center">
                            <td><? echo $i; ?></td>
                            <td>
                            	<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                            </td>
                            <td>
                                <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
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
                <td align="center" height="15" width="100%"> </td>
            </tr>
            <tr>
                <td align="center" width="100%" class="button_container">
                	<? echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1); ?>
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

if($action=="service_booking_print")
{
	extract($_REQUEST);
	$data=explode("*",$data);
	//print_r($data);
	$cbo_company_name=str_replace("'","",$data[0]);
	//echo "dgdf".$cbo_company_name;
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//wo_non_ord_samp_booking_mst
	$sql="select id from electronic_approval_setup where company_id=$cbo_company_name and page_id in(1120,451) and is_deleted=0";
	$res_result_arr = sql_select($sql);
	$approval_arr=array();
	foreach($res_result_arr as $row){
		$approval_arr[$row["ID"]]["ID"]=$row["ID"];
	}
	?>
	<div style="width:1135px; margin:0 auto"  align="left">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">                                     
                    <table width="90%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
							//echo return_field_value("location_name", "lib_location", "company_id='".$cbo_company_name."'");
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							foreach ($nameArray as $result)
                            {
							$com_address=$result[csf('city')].','.$country_arr[$result[csf('country_id')]];
							 echo  $location_name_arr[$location]; 
                            ?>
                             
                               <? echo $result[csf('plot_no')]; ?> 
                                <? echo $result[csf('level_no')]?>
                                <? echo $result[csf('road_no')]; ?> 
                                <? echo $result[csf('block_no')];?> 
                                <? echo $result[csf('city')];?> 
                                <? echo $result[csf('zip_code')]; ?> 
                                <?php echo $result[csf('province')];?> 
                                <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                <? echo $result[csf('email')];?> 
                                <? echo $result[csf('website')]; ?>
                             
                                <?
                            }
                            ?>
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong><? echo $data[4];?></strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                 
              
               </td>      
            </tr>
       </table>
		<?
		//echo "select a.wo_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.source,a.fab_booking_no  from wo_non_ord_aop_booking_mst a, where  a.wo_no=$txt_booking_no";
		$fabric_description=array();
	    $fabric_description2=array();
	    $color=array();
		$buyer_id=0;
		//$style_ref=array();
	
		$sql=sql_select("select booking_no, fab_booking_id, knit_dye_source, buyer_id, booking_date, currency_id, exchange_rate, source, supplier_id, attention, item_category,is_approved from wo_non_ord_knitdye_booking_mst where booking_no='$data[1]' and status_active=1 and is_deleted=0");

		 
        foreach ($sql as $result)
        {
			
			$knit_dye_source=$result[csf('knit_dye_source')];
			if($knit_dye_source==1)//Company
			{
				$company_supp=$company_library[$result[csf('supplier_id')]];
				$address=$com_address;
			}
			else
			{
				$company_supp=$supplier_name_arr[$result[csf('supplier_id')]];
				$address=$supplier_address_arr[$result[csf('supplier_id')]];
			}
			 $booking_no= $result[csf('booking_no')];
			 $is_approved= $result[csf('is_approved')];
			
        ?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]];$currency_id=$result[csf('currency_id')]; ?></td>
            </tr>
            <tr>
                
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                 <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $company_supp;?>    </td>
            </tr> 
             <tr>
               
                 <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $address;?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td width="100" style="font-size:12px"><b>Buyer Name</b>   </td>
                <td width="110">:&nbsp;<? echo $buyer_arr[$result[csf('buyer_id')]];?>    </td>
            </tr>  
            <tr>
                
                <td width="100" style="font-size:12px"><b>Process</b>   </td>
                <td width="110">:&nbsp;<? echo $conversion_cost_head_array[$result[csf('item_category')]];?>    </td>
                
                 <td width="100" style="font-size:12px"><b>Fab. Booking No.</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo  $data[5];
				?>
                </td>
            </tr> 
            <tr>
                
                <td width="100" style="font-size:12px"><b>Style</b>  </td>
                <td width="110" colspan="3" ><p style="word-break: break-all;">:
				<? 
				$style=sql_select("select booking_no,style_des from wo_non_ord_samp_booking_dtls where booking_no='$data[5]' and style_des is not null and status_active=1 and is_deleted=0 group by booking_no,style_des");
			$style_ref="";
				foreach($style as $row)
				{
					if($style_ref=="") $style_ref=$row[csf('style_des')]; else $style_ref.=",".$row[csf('style_des')];
					
				}
				echo $style_ref;
				?></p>
                </td>
            </tr>  
        </table>  
		<?
        }
        ?>
          
		<?
		//========================================
		
	//$data=explode("_",$data);
	
	$sql=sql_select("select a.id, a.entry_form_id, a.color_all_data, a.body_part, a.fabric_description, fabric_color, a.gmts_color, a.gsm_weight, a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where b.id=$data[2] and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 order by a.id");
	foreach($sql as $row)
	{
		$entry_form_id=$row[csf('entry_form_id')];
		if($row[csf('entry_form_id')]==140)
		{
			$gmts_color=explode("_",$row[csf('color_all_data')]);
			$gmts_colors=$gmts_color[2];	
		}
		else
		{
			$gmts_colors=$row[csf('gmts_color')];	
		}
		
		$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')].",".$color_library[$row[csf('fabric_color')]];
		$color[$row[csf('id')]][fabric_color]=$color_library[$row[csf('fabric_color')]];
		$color[$row[csf('id')]][gmts_color]=$color_library[$gmts_colors];
	}
	
	
	$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$sql=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6  and a.to_order_id=$data[3] and c.item_category=13  and a.status_active=1 and a.is_deleted=0 order by c.id");
	foreach($sql as $row)
	{
		$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
	}
	
	$sql="select id, fabric_source, fab_des_id, gsm, dia, uom, artwork_no, mc_gauge, mc_dia, dia_width_type, stitch_length, wo_qty, rate, amount, gmts_color, delivery_start_date, delivery_end_date, remarks, process_id from wo_non_ord_knitdye_booking_dtl where mst_id=$data[3] and status_active=1 and is_deleted=0";
	?>
	<table class="rpt_table" border="1" width="100%" cellpadding="0" cellspacing="0" rules="all" style="margin-top:20px; font-size:11px">
        <thead>
            <th width="20">SL</th>
            <th width="150">Fabric Description</th>
            <th width="60">Gsm</th>
            <th width="60">F.Dia</th>
            <th width="60">M. Dia X Gauge</th>
            <th width="60">Stitch Length</th>
            <th width="60">Dia/ W. Type</th>
            <th width="60">UOM</th>
            <th width="80">Artwork No</th>
            <th width="80">Gmts. Color</th>
            <th width="100">Process</th>
            <th width="70">Delivery Start Date</th>
            <th width="70">Delivery End Date</th>
            <th width="60">WO. Qty</th>
            <? if($data[6]==1){?>
            <th width="60">Rate</th>
            <th width="60">Amount</th>
            <? }?>
            <th>Remarks</th>
        </thead>
        <tbody>
			<?
            $dataArray=sql_select($sql);
            $i=1;
			$Two_qty="";$Trate="";$Tamount="";
            foreach($dataArray as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="get_dtls_data(<? echo $row[csf('id')]?>)" style="cursor:pointer" >
                    <td style="word-break:break-all"><?=$i; ?></td>
                    <td style="word-break:break-all">
                    <?
					if($row[csf('fabric_source')]==1)
					{
                    	echo $fabric_description[$row[csf('fab_des_id')]]; 
					}
					else
					{
						echo $fabric_description2[$row[csf('fab_des_id')]]; 
					}
					
					if($row[csf('mc_dia')]!="" && $row[csf('mc_gauge')]!="")
					{
						$mc_dia_gg= $row[csf('mc_dia')].'X'.$row[csf('mc_gauge')];
					}
					else if($row[csf('mc_dia')]!="" && $row[csf('mc_gauge')]=="")
					{
						$mc_dia_gg= $row[csf('mc_dia')];
					}
					else if($row[csf('mc_dia')]=="" && $row[csf('mc_gauge')]!="")
					{
						$mc_dia_gg= $row[csf('mc_gauge')];
					} else $mc_dia_gg="";
                    ?>
                    </td>
                    <td style="word-break:break-all"><? echo $row[csf('gsm')]; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('dia')]; ?></td>
                    <td style="word-break:break-all"><? echo $mc_dia_gg; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('stitch_length')]; ?></td>
                    <td style="word-break:break-all"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                    <td style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('artwork_no')]; ?></td>
                    <td style="word-break:break-all"><? echo $color[$row[csf('fab_des_id')]][gmts_color]; ?></td>
                    <td style="word-break:break-all"> <? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?> </td>
                    <td style="word-break:break-all"><? echo change_date_format($row[csf('delivery_start_date')]); ?></td>
                    <td style="word-break:break-all"><? echo change_date_format($row[csf('delivery_end_date')]); ?></td>
                    <td style="word-break:break-all" align="right"><? echo number_format($row[csf('wo_qty')],2); $Two_qty+=$row[csf('wo_qty')]; ?></td>
                     <? if($data[6]==1){?>
                    <td style="word-break:break-all" align="right"><? echo number_format($row[csf('rate')],2); $Trate+=$row[csf('rate')];?></td>
                    <td style="word-break:break-all" align="right"><? echo number_format($row[csf('amount')],2); $Tamount+=$row[csf('amount')]; ?></td>
                    <? }?>
                    <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
				</tr>
				<?	
				$i++;
            }
            ?>
        </tbody>
        <tfoot>
            <td colspan="13" align="right">Total:</td>
            <td align="right"><? echo number_format($Two_qty,2);?></td>
            <? if($data[6]==1){?>
            <td align="right"><? //echo number_format($Trate,2);?></td>
            <td align="right"><? echo number_format($Tamount,2);?></td>
            <? }?>
        </tfoot>
	</table>
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
       <? if($data[6]==1){?>
       <table  width="100%" class="rpt_table" style="border:1px solid black;margin-top:20px;"   border="0" cellpadding="0" cellspacing="0" rules="all" >
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($Tamount,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th  width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($Tamount,2,""),$mcurrency,$dcurrency);?></td>
            </tr>
       </table>
       <? }?>
          &nbsp;
        	
    	<? //echo get_spacial_instruction($data[5]); ?>
		<? echo get_spacial_instruction($booking_no,'',194);?>
		    <br>
				<table width="780" align="center">
						<tr>
							<div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
									<?
									if(count($approval_arr)>0)
									{				
										if($is_approved == 0){echo "Draft";}else{}
									}		
									?>
							</div>
						</tr>
				</table>
			<br
  		<?	
            echo signature_table(180, $cbo_company_name, "900px","",1);
         ?>

    </div>
        
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    
<?
exit();
}

if($action=="show_trim_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	?>
	<div style="width:1035px; margin:0 auto"  align="left">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">                                     
                    <table width="90%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
							//echo return_field_value("location_name", "lib_location", "company_id='".$cbo_company_name."'");
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							foreach ($nameArray as $result)
                            {
							 echo  $location_name_arr[$location]; 
                            ?>
                             
                               Plot No: <? echo $result[csf('plot_no')]; ?> 
                                Level No: <? echo $result[csf('level_no')]?>
                                Road No: <? echo $result[csf('road_no')]; ?> 
                                Block No: <? echo $result[csf('block_no')];?> 
                                City No: <? echo $result[csf('city')];?> 
                                Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                Province No: <?php echo $result[csf('province')];?> 
                                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')]; ?>
                             
                                <?
                            }
                            ?>
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong><? echo $report_title;?></strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                 <td width="250" id="barcode_img_id"> 
              
               </td>      
            </tr>
       </table>
		<?
		//echo "select a.wo_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.source,a.fab_booking_no  from wo_non_ord_aop_booking_mst a, where  a.wo_no=$txt_booking_no";
		$fabric_description=array();
	    $fabric_description2=array();
	    $color=array();
		$buyer_id=0;
		$style_ref=array();
	
		$sql=sql_select("select a.id,b.buyer_id,a.style_id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where b.id=$txt_fabric_booking_id and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 order by a.id");
		foreach($sql as $row)
		{
		$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')].",".$color_library[$row[csf('fabric_color')]];
		$color[$row[csf('id')]][fabric_color]=$color_library[$row[csf('fabric_color')]];
		$color[$row[csf('id')]][gmts_color]=$color_library[$row[csf('gmts_color')]];
		$buyer_id=$row[csf('buyer_id')];
		$style_ref[$row[csf('style_id')]]=$row[csf('style_id')];
		}
	
	
		$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
		$sql=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6  and a.to_order_id=$txt_fabric_booking_id and c.item_category=13  and a.status_active=1 and a.is_deleted=0 order by c.id");
		foreach($sql as $row)
		{
		$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
		}
		$style_name_str="";
		$style_name_arr=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
		foreach($style_ref as $key=> $value)
		{
		$style_name_str.=$style_name_arr[$value].",";	
		}

		
		
		$booking_grand_total=0;
		$currency_id=0;
        $nameArray=sql_select( "select a.wo_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.source,a.fab_booking_no  from wo_non_ord_aop_booking_mst a where  a.wo_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('wo_no')];
        ?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:12px"><b>Wo No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('wo_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Wo Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:12px"><b>Fab. Booking No</b>   </td>
                <td width="110">:&nbsp;<? echo $fab_booking_no=$result[csf('fab_booking_no')];?>    </td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]];$currency_id=$result[csf('currency_id')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                 <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Buyer Name</b>   </td>
                <td width="110">:&nbsp;<? echo $buyer_arr[$buyer_id];?>    </td>
                 
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Style Ref</b>   </td>
                <td width="110">:&nbsp;<? echo $style_sting=rtrim($style_name_str,",");?>    </td>
                 
            </tr>   
            
            
        </table>  
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		
	//$data=explode("_",$data);
	
		
	
	
	
	$sql="select id,wo_id,wo_no,fab_booking_id,fab_booking_no,fabric_source,fabric_description,aop_gsm,aop_dia,uom,artwork_no,wo_qty,rate,amount,dev_start_date ,dev_end_date,remarks from wo_non_ord_aop_booking_dtls where wo_id=$mst_id";
		?>
        <br/>
        <table class="rpt_table" border="1" width="100%" cellpadding="0" cellspacing="0" rules="all" id="">
            <thead>
                <th width="40">SL</th>
                <th width="100">Fabric Source</th>
                <th width="200">Fabric Description</th>
                <th width="60">Aop.Gsm</th>
                <th width="60">Aop.Dia</th>
                <th width="60">UOM</th>
                <th width="100">Artwork No</th>
                <th width="100">Gmts. Color</th>
                <th width="100">Item Color</th>
                <th width="100">Delivery Start Date</th>
                <th width="100">Delivery End Date</th>
                <th width="60">WO. Qnty</th>
                <th width="60">Rate</th>
                <th width="60">Amount</th>
                <th width="100">Remarks</th>
                <th></th>
            </thead>
            <tbody>
            <?
            $dataArray=sql_select($sql);
            $wo_qty_tot=0;
			$amount_tot=0;
            $i=1;
            foreach($dataArray as $row)
            {
				
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_dtls_data(<? echo $row[csf('id')]?>)" style="cursor:pointer" >
                <td width="40"><? echo $i; ?></td>
                <td width="100"><? echo $aop_nonor_fabric_source[$row[csf('fabric_source')]]; ?></td>
                <td width="200">
				<?
				if($row[csf('fabric_source')]==1)
				{
				echo $fabric_description[$row[csf('fabric_description')]]; 
				}
				if($row[csf('fabric_source')]==2)
				{
				echo $fabric_description2[$row[csf('fabric_description')]]; 
				}
				?>
                </td>
                <td width="60"><? echo $row[csf('aop_gsm')]; ?></td>
                <td width="60"><? echo $row[csf('aop_dia')]; ?></td>
                <td width="60"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td width="100"><? echo $row[csf('artwork_no')]; ?></td>
                <td width="100"><? echo $color[$row[csf('fabric_description')]][gmts_color]; ?></td>
                <td width="100"><? echo $color[$row[csf('fabric_description')]][fabric_color]; ?></td>
                <td width="100"><? echo change_date_format($row[csf('dev_start_date')],'dd-mm-yyyy','-'); ?></td>
                <td width="100"><? echo change_date_format($row[csf('dev_end_date')],'dd-mm-yyyy','-'); ?></td>
                <td width="60" align="right"><? echo number_format($row[csf('wo_qty')],2);$wo_qty_tot+=$row[csf('wo_qty')]; ?></td>
                <td width="60" align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                <td width="60" align="right"><? echo number_format($row[csf('amount')],2);$amount_tot+=$row[csf('amount')]; ?></td>
                <td width="100"><? echo $row[csf('remarks')]; ?></td>
                <td></td>
                </tr>
            <?	
            $i++;
            }
            ?>
            </tbody>
            <tfoot>
                <th width="40"></th>
                <th width="100"></th>
                <th width="200"></th>
                <th width="60"></th>
                <th width="60"></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="60" align="right"><? echo number_format($wo_qty_tot,2); ?></th>
                <th width="60" align="right"><? echo number_format($amount_tot/$wo_qty_tot,2); ?></th>
                <th width="60" align="right"><? echo number_format($amount_tot,2); ?></th>
                <th  width="100"></th>
                <th></th>
            </tfoot>
        </table>
		<br/>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        <?
		 $sql_img=sql_select("select a.id,a.wo_id,a.fabric_description,b.image_location from wo_non_ord_aop_booking_dtls a, common_photo_library b where a.id=b.details_tble_id and a.wo_id=b.master_tble_id and a.wo_id=$mst_id and b.form_name='aop_non_order_booking'");
		 if(count($sql_img)>0)
		 {
		 
		$maxcols = 3;
		$i = 0;
		
		//Open the table and its first row
		echo "<table class='rpt_table' border='1' width='100%' cellpadding='0' cellspacing='0' rules='all' id=''>";
		echo "<tr><td colspan='3' style='font-size:24px'>Image</td></tr>";
		echo "<tr>";
		foreach ($sql_img as $sql_row) {
		
		if ($i == $maxcols) {
		$i = 0;
		echo "</tr><tr>";
		}
		echo "<td width='33%' align='center'><img  src=\"../../" . $sql_row[csf('image_location')] . "\" /><p>".$fabric_description[$sql_row[csf('fabric_description')]]."</p></td>";
		$i++;
		
		}
		//Add empty <td>'s to even up the amount of cells in a row:
		while ($i < $maxcols) {
		echo "<td width='33%'>&nbsp;</td>";
		$i++;
		}
		 }
		 
		?>
        </table>
       <br/>
       &nbsp;
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
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($amount_tot,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th  width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words($amount_tot,$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
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
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                        
                    </tr>
        <? 
            }
        } 
        ?>
    </tbody>
    </table>
    
         <br/>
		 <?
		
            echo signature_table(92, $cbo_company_name, "1035px");
			echo "****".custom_file_name($fab_booking_no,$style_sting,implode(',',$all_job_arr));
         ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
<?
exit();
}

if($action=="show_trim_booking_report1")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            echo return_field_value("location_name", "lib_location", "company_id='".$cbo_company_name."'");
                            ?>
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet</strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no,a.buyer_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
		
	    $buyer_name=$nameArray_job[0][csf('buyer_id')];
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
		}
		/*$po_no="";
		$nameArray_job=sql_select( "select distinct b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
		}*/
		
		$po_no=""; $po_id='';
		
		$nameArray_job=sql_select( "select b.id, b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no group by b.id, b.po_number"); 
		
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
			$po_id.=$result_job[csf('id')].",";
		}
		
		// PO ID Different But Po No Same Then Following Code (Added By Fuad)
		//$po_no=implode(",",array_unique(explode(",",substr($po_no,0,-1)))); 
		
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		//echo  "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")";
		//$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")", "po_break_down_id", "article_number"  );
		
		$po_id=substr($po_id,0,-1);//(Added By Fuad)
		$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".$po_id.")", "po_break_down_id", "article_number");
		//print_r($article_number_arr);
		
        foreach ($nameArray as $result)
        {
        ?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="110" align="center"><b>IMAGE</b></td>
                	
            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>	
                <td  width="110" rowspan="6" align="center">
                
                <? 
			$nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id='".$result[csf('booking_no')]."' and file_type=1");
			?>
            
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
						<!--<img src="../../<? //echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />-->
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
                       <? 
					   $img=explode('.',$result_imge[csf('real_file_name')]);
					   echo $img[0];
					   ?>
					</td>
					<?
					
					$img_counter++;
				}
				?>
                </tr>
           </table>   
                </td>	
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id =$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                
            </tr> 
             <tr>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
            </tr>  
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110" colspan="3">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" >:&nbsp;
				<? 
				echo rtrim($job_no,',');
				?> 
                </td>
                <td width="100" style="font-size:12px"><b>Buyer Name</b>   </td>
                <td width="110" >:&nbsp;
				<? 
				echo $buyer_name_arr[$buyer_name];
				?> 
                </td>
            </tr> 
            <tr>
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr> 
        </table> 
        <br/> 
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
			
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
		
							
	}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=1  and status_active=1 and is_deleted=0");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=1 and status_active=1 and is_deleted=0"); //and sensitivity=1
		
       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong>As Per Gmts Color</strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			
			 $total_amount_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description='".$result_item[csf('description')]."' and wo_qnty !=0 and sensitivity=1 and status_active=1 and is_deleted=0  group by po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=3 and status_active=1 and is_deleted=0");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=3 and status_active=1 and is_deleted=0"); //and sensitivity=1
		
       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong>As Per Constrast Color</strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_constrast_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=3 and status_active=1 and is_deleted=0 group by po_break_down_id,fabric_color_id,gmts_color_id,description,rate,artwork_no,dia_width");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_constrast_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_constrast_color,4);
                $total_constrast_color+=$amount_constrast_color;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_constrast_color,4);
                $booking_grand_total+=$total_constrast_color;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Constrast COLOR END=========================================  -->
        <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=2 and status_active=1 and is_deleted=0");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct item_size from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=2 and status_active=1 and is_deleted=0"); //and sensitivity=1
		
       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong> As Per Size Sensitive</strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Size</strong> </td>
                <td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_size_sensitive=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,item_size,gmts_size,description,rate,artwork_no,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=2 and status_active=1 and is_deleted=0 group by po_break_down_id,item_size,gmts_size,description,rate,artwork_no,dia_width");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $size_library[$result_itemdescription[csf('gmts_size')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_size_sensitive = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_size_sensitive,4);
                $total_amount_size_sensitive+=$amount_size_sensitive;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_size_sensitive,4);
                $booking_grand_total+=$total_amount_size_sensitive;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Size Sensitive END=========================================  -->
        
         <?
        //=================================================
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=4 and status_active=1 and is_deleted=0");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct item_size from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0 and sensitivity=4 and status_active=1 and is_deleted=0"); //and sensitivity=1
		
       if(count($nameArray_color)>0)
		{
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                 <strong> As Per Color & Size Sensitive</strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>GMT Size</strong> </td>
                <td style="border:1px solid black"><strong>Item Size</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$total_amount_color_and_size_sensitive=0;
            $nameArray_item_description=sql_select( "select po_break_down_id,fabric_color_id,gmts_color_id,item_size,gmts_size,description,rate,artwork_no,
			dia_width,sum(wo_qnty) as cons 
			from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." 
			and description=".$result_item[csf('description')]." and wo_qnty !=0 and sensitivity=4 and status_active=1 and is_deleted=0
			group by po_break_down_id,fabric_color_id,gmts_color_id,item_size,gmts_size,description,rate,artwork_no,dia_width");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_itemdescription[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $size_library[$result_itemdescription[csf('gmts_size')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('item_size')]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_color_and_size_sensitive = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_color_and_size_sensitive,4);
                $total_amount_color_and_size_sensitive+=$amount_size_sensitive;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="10"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_color_and_size_sensitive,4);
                $booking_grand_total+=$total_amount_color_and_size_sensitive;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
		?>
        <!--==============================================Size Sensitive END=========================================  -->
        
        <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 
		and wo_qnty !=0 and status_active=1 and is_deleted=0" ); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="7" align="">
                <strong> As Per No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and wo_qnty !=0 and status_active=1 and is_deleted=0"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
			
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and status_active=1 and is_deleted=0 ");                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                ?>
                <td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);  
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
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
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
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
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                        
                    </tr>
        <? 
            }
        } 
        ?>
    </tbody>
    </table>
       <br><br>
    <table border="0" cellpadding="0" cellspacing="0"  width="90%" class="rpt_table"  style="border:1px solid black;" >
                <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
                <tr style="border:1px solid black;" align="center">
                    <th style="border:1px solid black;" width="40">SL</th>
                    <th style="border:1px solid black;" width="200">Job No</th>
                    <th style="border:1px solid black;" width="200">PO No</th>
                    <th style="border:1px solid black;" width="80">Ship Date</th>
                    <th style="border:1px solid black;" width="80">Pre-Cost/Budget Value</th>
                    <th style="border:1px solid black;" width="80">WO Value 2</th>
                   
                    <th style="border:1px solid black;" width="80">Balance</th>
                    <th style="border:1px solid black;" width=""> Comments </th>
                </tr>
       <tbody>
       <?
					$pre_cost_item_id_arr=return_library_array( "select id,item_number_id from wo_pre_cost_fabric_cost_dtls", "id", "item_number_id"  );
					$ship_date_arr=return_library_array( "select id,pub_shipment_date from wo_po_break_down", "id", "pub_shipment_date"  );
					$gmtsitem_ratio_array=array();
					$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  ");// where job_no ='FAL-14-01157'
					foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row)
					{
					$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];	
					}
					$po_qty_arr=array();$aop_data_arr=array();
					//$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.plan_cut) as order_quantity,(sum(b.plan_cut)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
					$sql_po_qty=sql_select("select a.po_break_down_id as po_id, a.item_number_id,sum(a.plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown a  where     a.is_deleted=0  and a.status_active=1 group by a.po_break_down_id,a.item_number_id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]['order_quantity']=$row[csf("plan_cut_qnty")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS aop_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=35 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];	
					}
					$i=1; $total_balance_aop=0;$tot_aop_cost=0;$tot_pre_cost=0;
				
					$sql_aop=( "select listagg(cast(c.fabric_description as varchar2(4000)),',') within group (order by c.fabric_description) as pre_cost_fabric_cost_dtls_id,b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c    where a.job_no=b.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  and c.status_active=1  and c.is_deleted=0  and b.status_active=1  and b.is_deleted=0   group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_aop );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}
						$pre_cost_item=explode(",",$selectResult[csf('pre_cost_fabric_cost_dtls_id')]);
						foreach($pre_cost_item as $item)
						{
							$set_ratio=$gmtsitem_ratio_array[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]];
							$po_qty=$po_qty_arr[$selectResult[csf('po_id')]][$pre_cost_item_id_arr[$item]]['order_quantity'];
						}
						$tot_per_ratio=$costing_per_qty*$set_ratio;
						$pre_cost_aop=($aop_data_arr[$selectResult[csf('job_no')]]['aop']/$tot_per_ratio)*$po_qty;
						$aop_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
						$ship_date=$ship_date_arr[$selectResult[csf("po_id")]];
	   ?>
                    <tr>
                    <td style="border:1px solid black;" width="40"><? echo $i;?></td>
                    <td style="border:1px solid black;" width="200">
					<? echo $selectResult[csf('job_no')];?> 
                    </td>
                    <td style="border:1px solid black;" width="200">
					<? echo $po_number[$selectResult[csf('po_id')]];?> 
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
					<? echo change_date_format($ship_date);?> 
                    
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                     <? echo number_format($pre_cost_aop,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($aop_charge,2); ?>
                    </td>
                  
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_aop-$aop_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_aop>$aop_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_aop<$aop_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_aop==$aop_charge) 
						{
							echo "As Per";
						} 
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	  	 $tot_pre_cost+=$pre_cost_aop;
	  	 $tot_aop_cost+=$aop_charge;
		 $total_balance_aop+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_aop_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_aop,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
          
         <br/>
        
		 <?
            echo signature_table(79, $cbo_company_name, "1313px");
         ?>
    </div>
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
		else if($db_type==2 || $db_type==1 )
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


function sql_deleted($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)//please check the function
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}
	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
			global $con;
	 echo $strQuery;
	 $stid =  oci_parse($con, $strQuery);
	 $exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT );
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error( $stid))
		{
			oci_commit($con);
			return "2";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;


}



?>