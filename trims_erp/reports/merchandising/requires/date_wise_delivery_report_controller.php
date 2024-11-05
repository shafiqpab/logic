<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
} 

if($action=="load_drop_down_subsection")
{
	$data=explode('_',$data);
	if($data[0]==1) $subID='1,2,3,23';
	else if($data[0]==3) $subID='4,5,18';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
	else if($data[0]==10) $subID='14,15';
	else if($data[0]==7) $subID='19,20,21,25,26';
	else if($data[0]==9) $subID='22';
	else $subID='0';
	//echo $data[0]."**".$subID;
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"",1, "-- Select Sub-Section --","","",0,$subID,'','','','','',"");
	exit();
}

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			//alert(booking_no); 
			document.getElementById('hidd_booking_data').value=id;
			parent.emailwindow.hide();
		}
	
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>                	 
                        <th width="120">Delv Chlln No</th>
                        <th colspan="2" width="160">Delv Chlln Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
                    <td><input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:90px"></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>, 'create_booking_search_list_view', 'search_div', 'date_wise_delivery_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	//print_r($data);
	
	if ($data[3]!=0) $company=" and a.company_id='$data[3]'";
	if ($data[4]!=0) $party=" and a.party_id='$data[4]'";
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="")  $delivery_date= "and a.delivery_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	} 
	
	if ($data[0]!="") $woorder_cond=" and a.trims_del like '%$data[0]%' "; 
	
	$sql= "select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no from trims_delivery_mst a, trims_delivery_dtls b where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $woorder_cond  $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id, a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no order by a.id DESC";
	
	

	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="440" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Delv Chlln No</th>
            <th width="70">Delv Chlln Date</th>
        </thead>
        </table>
        <div style="width:440px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_del')].'_'.$row[csf('currency_id')].'_'.$row[csf('within_group')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('trims_del')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
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

if($action=="print_delv_challan") 
{
		extract($_REQUEST);
		//echo $data;die;
		$data=explode('*',$data);
		$cbo_template_id=$data[6];
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name" );
		$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1",'id','size_name');
		$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$sql_company = sql_select("select * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	  	foreach($sql_company as $company_data) 
	  	{
			if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
			if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
			if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
			if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
			if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
			if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
			if($company_data[csf('country_id')]!=0)$country = $country_arr[$company_data[csf('country_id')]];else $country='';
			
			$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
		}
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
		$buyer_po_arr=array();
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			?>
		<style type="text/css">
			.opacity_1
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}	
			.opacity_2
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}
			/* .opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			} */					
			
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
			
			#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
			
		</style>
		<?
		//echo "select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]";
		//$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by,cust_location from trims_delivery_mst where id= $data[1]");
		
		$sql_mst = sql_select("SELECT a.id, a.entry_form, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no, a.remarks,a.inserted_by,a.cust_location, b.receive_dtls_id
		FROM trims_delivery_mst a, trims_delivery_dtls b
		where a.id=b.mst_id and a.id=$data[1]");
		$jobDtlsId = $sql_mst[0][csf("receive_dtls_id")];

		$jobData = sql_select("SELECT e.buyer_tb
		FROM subcon_ord_dtls c, subcon_ord_mst e
		where e.id=c.mst_id and c.id=$jobDtlsId");

		$inserted_by=$sql_mst[0][csf("inserted_by")]; 
		
		if($data[2]==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
			$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
		}
		
		$fac_merchant_arr=return_library_array( "select id, team_marchant from subcon_ord_mst",'id','team_marchant');
		$fac_merchant=$fac_merchant_arr[$sql_mst[0][csf("received_id")]];

		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
		//$copy_print = 3;
		//for($k=1; $k <= $copy_print; $k++)
		//{
	$k=0;	
	$copy_no=$data[7]; //for Dynamic Copy here 
	for($cid=1; $cid<=$copy_no; $cid++)
	{
			 $k++;
			 if($cid==1){
				$st="st Copy";
			 }elseif($cid==2){
				$st="nd Copy";
			 }elseif($cid==3){
				$st="rd Copy";
			 }else{
				$st="th Copy";
			 }
		?>
	        
	    <div style="width:1250px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="2" width="200">
						<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
					</td>
	            	<td style="font-size:20px;" align="center"><strong>
						<? echo $company_arr[$data[0]]; ?></strong>
	                </td>
	                <td align="right" width="100">
						<? 
						echo "<b><h2> $cid $st</h2></b>";
						/*else if($k==3){
						echo "3rd Copy";
						}*/
						?> 
					</td>
	            </tr>
	            <tr>
	            	<td style="font-size:large" align="center"><? echo $company_address; ?></td>
					<!-- <td align="center">
						<?
						/*$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
							?>
							<? echo $result[csf('city')]; ?><br>
							<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
						}*/
						?> 
					</td> -->
	        		<td id="barcode_img_id_<? echo $k; ?>"></td>
				</tr>
				<tr>
	            	<td>&nbsp;</td>
	            	<td style="font-size:20px;" align="center"> <strong><? //echo $data[3]." Challan"; ?>Accessories Challan</strong></td>
	                <td>&nbsp;</td>
	            </tr> 
	            <tr>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100"><strong> Delivery To</strong></td>
	                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="120"><strong>Challan No. </strong></td>
	                <td valign="top"><strong>: <? echo $data[5]; ?></strong></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120">Address</td>
	                <td valign="top">: <? echo $party_location; ?> </td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Delivery Date</td>
	                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100">WO NO.</td>
	                <td valign="top" width="150">: <? echo $data[4];//$order_no_trims_arr[$sql_mst[0][csf("received_id")]]['order_no']; ?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Remarks</td>
	                <td valign="top" width="150">: <? echo $sql_mst[0][csf("remarks")]; ?></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="100">Customer Location</td>
	                <td valign="top" width="150">:<?=$sql_mst[0][csf("cust_location")];?></td>
	                <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Factory Merchant.</td>
	                <td valign="top" width="150">: <? echo $fac_merchant; ?></td>
	            </tr>
				<tr>
	            	<td valign="top" width="100">Trims Booking</td>
	                <td valign="top" width="150">:<?=$jobData[0][csf("buyer_tb")];?></td>
	                <!-- <td valign="top" width="250">&nbsp;</td>
	                <td valign="top" width="100">Factory Merchant.</td>
	                <td valign="top" width="150">: <? echo $fac_merchant; ?></td> -->
	            </tr>
	      	</table>
	         <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
	      		<thead>
		            <tr>
		            	<th width="40">SL</th>
	                    <th width="130">Cust. PO</th>
	                    <th width="160">Buyers Style Ref.</th>
	                    <th width="130">Buyer's Buyer </th>
	                    <th width="90">Style Name</th>
	                    <th width="80">Section</th>
		                <th width="90">Item Group</th>
		                <th width="140">Item Description</th>	
						<th width="80">Gmts Color </th>
		                <th width="70">Gmts Size</th>
	                    <th width="80">Item Color </th>
		                <th width="70">Item Size</th>				
		                <th width="60">Order UOM</th>
	                    <th width="70">WO Qty.</th>
		                <th width="80">Cum. Delv Qty</th>
		                <th width="80">Curr. Delv Qty</th>
		                <th width="80">No of Roll/Bag</th>
		                <th width="80">Delv Balance Qty</th>
		                <th>Remarks</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
				$i = 1;
				//$remarks_arr=return_library_array( "select id,remarks from trims_delivery_mst", "id", "remarks" );
				$total_quantity=0;$total_delevery_quantity=0;$curr_delevery_quantity=0;$delevery_Balance_quantity=0; $total_roll_bag=0;
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
			    $sql = "SELECT a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id,  a.order_id, a.order_no, a.buyer_po_id, a.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,   a.delevery_qty, a.claim_qty, a.remarks, a.gmts_color_id, a.gmts_size_id,a.color_id, a.size_id, a.no_of_roll_bag, a.description, a.delevery_status, a.color_name,a.size_name, a.workoder_qty,break_down_details_id, b.style from trims_delivery_dtls a, subcon_ord_breakdown b where a.mst_id='$data[1]' and a.break_down_details_id = b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC";
			  	/* 	$delevery_qty_trims_arr=array();
				$pre_sql ="select job_dtls_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by job_dtls_id";
				$pre_sql_res=sql_select($pre_sql);
				foreach ($pre_sql_res as $row)
				{
					$delevery_qty_trims_arr[$row[csf("job_dtls_id")]]['delevery_qty']=$row[csf("delevery_qty")];
					
				}
				unset($pre_sql_res);
				*/
		
				$delevery_qty_trims_arr=array();
				$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
				$pre_sql_res=sql_select($pre_sql);
				foreach ($pre_sql_res as $row)
				{
					$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
				}
				unset($pre_sql_res);
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
				?>
	                <tr>
	                <td><?php echo $i; ?></td>
	                <td><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
	                <td style="word-break: break-all"><p><?php echo $row[csf('buyer_style_ref')]; ?></p></td>
	                <td><p><?php if($data[2]==1)
					{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
	                <td><?php echo $row[csf("style")]; ?></td>
	                <td><?php echo $trims_section[$row[csf('section')]]; ?></td>
	                <td><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
	                <td><p><?php echo $row[csf('description')]; ?></p></td>	
					<td><p><?php echo $color_library[$row[csf('gmts_color_id')]]; ?></p> </td>
	                <td><p><?php echo $size_arr[$row[csf('gmts_size_id')]]; ?></p></td>
	                <td><p><?php echo $row[csf('color_name')]; ?></p> </td>
	                <td><p><?php echo $row[csf('size_name')]; ?></p></td>				
	                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
	                <td align="right"><?php echo number_format($row[csf('workoder_qty')],4); $total_quantity += $row[csf('workoder_qty')]; ?></td>
	                <td align="right"><?php echo  
					$cumDelvQty=$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];  
					$total_delevery_quantity += $delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];
					 ?></td>
	                <td align="right"><?php echo $row[csf('delevery_qty')];  $curr_delevery_quantity += $row[csf('delevery_qty')];  ?></td>
	                <td align="center"><?php $total_roll_bag+=$row[csf('no_of_roll_bag')]; echo  $row[csf('no_of_roll_bag')]; ?></td>
	                <td align="right"><?php echo number_format($row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty),4); $delevery_Balance_quantity += $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$cumDelvQty);  ?></td>
	                
	                <td><?php echo  $row[csf('remarks')]; ?></td>
	                </tr>
				<?
				$i++;
	            } 
	         	if(count($unique_uom)==1){ 
				?>
	            <tr> 
					<td colspan="12"><strong>&nbsp;&nbsp;</strong></td>
					<td align="right"><strong>Total:</strong></td>
					<td align="right"><strong><? echo number_format($total_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($total_delevery_quantity,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($curr_delevery_quantity,2); ?></strong></td>
					 <td align="right"><strong><? echo number_format($total_roll_bag,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($delevery_Balance_quantity,2); ?></strong></td>
	               
	                <td><strong>&nbsp;&nbsp;</strong></td>
				</tr>
	            <? } ?>
	        </tbody> 
	    </table>
		<?
			$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
			echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	    ?>	
	    </div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	    <script>
	    function generateBarcode( valuess )
	    {
	        var value = valuess;//$("#barcodeValue").val();
	        var btype = 'code39';//$("input[name=btype]:checked").val();
	        var renderer ='bmp';// $("input[name=renderer]:checked").val();
	        var settings = {
	          output:renderer,
	          bgColor: '#FFFFFF',
	          color: '#000000',
	          barWidth: 1,
	          barHeight: 30,
	          moduleSize:5,
	          posX: 10,
	          posY: 20,
	          addQuietZone: 1
	        };
	        $("#barcode_img_id_<? echo $k; ?>").html('11');
	         value = {code:value, rect: false};
	        $("#barcode_img_id_<? echo $k; ?> ").show().barcode(value, btype, settings);
	    } 
	    generateBarcode("<? echo $data[5]; ?>");
	    </script>
	   <?
		}
	 	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	////////////////////////////////////////////////
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$hid_order_id=str_replace("'","", $hid_order_id);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$internal_no=str_replace("'","", $txt_internal_no);
	$report_type=str_replace("'","", $report_type);
	
	if($cbo_company_id){$where_con.=" and a.company_id='$cbo_company_id'";} 
	if($cbo_section_id){
		$where_con.=" and b.section='$cbo_section_id'";
		$where_con_ord.=" and b.section='$cbo_section_id'";
		$where_con_del.=" and b.section='$cbo_section_id'";

	} 
	if($cbo_sub_section_id){
		$where_con.=" and c.sub_section='$cbo_sub_section_id'";
		$where_con_ord.=" and b.sub_section='$cbo_sub_section_id'";
		$where_con_del.=" and d.sub_section='$cbo_sub_section_id'";
	} 
	if($cbo_customer_source){
		$where_con.=" and a.within_group='$cbo_customer_source'";
		$where_con_ord.=" and a.within_group='$cbo_customer_source'";
		$where_con_del.=" and a.within_group='$cbo_customer_source'";
	} 
	if($cbo_customer_name){
		$where_con.=" and a.party_id='$cbo_customer_name'";
		$where_con_ord.=" and a.party_id='$cbo_customer_name'";
		$where_con_del.=" and a.party_id='$cbo_customer_name'";
	} 
	if($cbo_location_name){

		$where_con.=" and a.location_id='$cbo_location_name'";

		$where_con_del.=" and a.location_id='$cbo_location_name'";
	} 
	
	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 ","id","supplier_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	 
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	$item_name_arr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1 order by item_name","id","item_name");
	//////////////////////////////////////////////////////////////////
	
		if($txt_date_from!="" and $txt_date_to!="")
		{	
			$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
			$where_con_del.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
	if(trim($txt_order_no)!="")
	{
			$sql_cond="and a.trims_del like '%$txt_order_no%'";
			$where_con_del="and a.trims_del like '%$txt_order_no%'";
	}
			
	$buyer_po_id_cond = '';
    if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		 $po_sql ="Select id,po_number,grouping from  wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
		 
		 // $po_sql ="Select b.idb.po_number,b.grouping from  wo_po_details_master a, wo_po_break_down b  where  a.job_no=b.job_no_mst and is_deleted=0 and status_active=1 $internal_no_cond ";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("po_number")]]['grouping']=$row[csf("grouping")];
			$buyer_po_id_arr[]="'".$row[csf("po_number")]."'";
		}
		unset($po_sql_res);
		
		//print_r($buyer_po_arr);
        //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
		if($internal_no !="")
		{
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_no in($buyer_po_id)";
		}
    }
 		
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2 and status_active=1)" , "conversion_rate" );
	
	
	//echo $currency_rate; die;
if($report_type==1)
{
	$order_sql="select a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section, b.item_group,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.qnty,c.order_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id,b.delivery_date as delivery_target_date, a.trims_ref
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id $where_con_ord and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $sql_order_no";
		$order_sql_result = sql_select($order_sql);
		$order_array=array();
		$booked_array=array();
		$booked_sammary_array=array();
		foreach($order_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['receive_date']=$row[csf('receive_date')]; 
			$order_array[$row[csf('id')]]['delivery_date']=$row[csf('delivery_date')];
			$order_array[$row[csf('id')]]['delivery_target_date']=$row[csf('delivery_target_date')];
			$order_array[$row[csf('id')]]['cust_order_no']=$row[csf('cust_order_no')];
			$order_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
			$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			$order_array[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$order_array[$row[csf('id')]]['within_group']=$row[csf('within_group')];
			$order_array[$row[csf('id')]]['exchange_rate']=$row[csf('exchange_rate')];
			$order_array[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
			$order_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
			$order_array[$row[csf('id')]]['item_group']=$row[csf('item_group')];
			$order_array[$row[csf('id')]]['trims_ref']=$row[csf('trims_ref')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['order_quantity']+=$row[csf('qnty')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['amount']+=$row[csf('amount')];
			
			$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['order_quantity']+=$row[csf('qnty')];
			$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['amount']+=$row[csf('amount')];
		}
		/*echo "<pre>";
		print_r($booked_array);*/
	
	 
	$trims_order_sql= "select a.id,a.trims_del,a.company_id,a.within_group,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.break_down_details_id,b.order_no,b.buyer_po_no,c.section as section_id,b.item_group,b.delevery_qty,b.remarks,b.description as item_description, b.color_id, b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac as conv_factor ,d.qnty,d.order_id ,d.qnty,d.rate,d.amount,d.booked_qty,d.description as item_description,d.id as breakdown_id from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d where c.id=d.mst_id and d.id=b.break_down_details_id and  a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con  $company $buyer_po_id_cond  order by a.id DESC"; 
	  
		$result = sql_select($trims_order_sql);
        $date_array=array();
		$deli_id_arr=array();
        foreach($result as $row)
        {
			$deli_id_arr[$row[csf("id")]]=$row[csf("id")];
			
       	 	$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['id']=$row[csf('id')];
       	 	$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['company_id']=$row[csf('company_id')];
       	 	$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['within_group']=$row[csf('WITHIN_GROUP')];
       	 	$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['section_id']=$row[csf('section_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['sub_section']=$row[csf('sub_section')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['item_group']=$row[csf('item_group')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['production_date']=$row[csf('production_date')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['delivery_date']=$row[csf('delivery_date')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['received_id']=$row[csf('received_id')];
       		$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['job_quantity']=$row[csf('job_quantity')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['uom']=$row[csf('uom')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['conv_factor']=$row[csf('conv_factor')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['buyer_po_no']=$row[csf('buyer_po_no')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['item_description']=$row[csf('item_description')];			            
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['job_no_mst']=$row[csf('job_no_mst')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['qc_qty']+=$row[csf('qc_qty')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['break_id']=$row[csf('break_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['product_uom']=$row[csf('product_uom')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['machine_id']=$row[csf('machine_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['order_no']=$row[csf('order_no')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['delevery_qty'] +=$row[csf('delevery_qty')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['remarks']=$row[csf('remarks')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['color_id'].=$row[csf('color_id')].",";
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]]['trims_del'].=$row[csf('trims_del')].',';
 		}

 		$deli_id_arr = array_unique($deli_id_arr);
 
 
 	//echo "<pre>";
	//print_r($date_array);
	//die;
 
	

	$trims_delevery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,c.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id,b.order_no from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 

	// echo $trims_delevery_sql;

	$trims_delivery_data_arr=array();
	$result_trims_delevery_sql = sql_select($trims_delevery_sql);
	foreach($result_trims_delevery_sql as $row)
	{
		$total_delevery_qty_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]]['deleveryqty']+=$row[csf('delevery_qty')];
	}
 
	//echo "<pre>";
	//print_r($total_delevery_qty_array);

	$delivery_con=where_con_using_array($deli_id_arr,0,"c.mst_id");



	$trims_bill_sql = "select a.id, a.trims_bill, a.within_group, a.currency_id, a.exchange_rate, a.bill_date, b.quantity, b.bill_rate, b.bill_amount, c.mst_id, d.section, c.description, c.order_no, d.job_no_mst, d.sub_section
	from trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c, subcon_ord_dtls d where  a.entry_form=276 and a.id=b.mst_id and c.id=b.production_dtls_id and d.id=c.receive_dtls_id $delivery_con and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id ASC"; //e.id=c.mst_id and //trims_delivery_mst e //e.delivery_date

	//echo $trims_bill_sql; die;

	$result_bill_sql=sql_select($trims_bill_sql);
	$trims_bill_data_arr=array();
	foreach($result_bill_sql as $row)
	{
		$disc=TRIM($row[csf('description')]);
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('section')]][$row[csf('sub_section')]][$disc][$row[csf('order_no')]][$row[csf('job_no_mst')]]['trims_bill'].=$row[csf('trims_bill')].',';
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('section')]][$row[csf('sub_section')]][$disc][$row[csf('order_no')]][$row[csf('job_no_mst')]]['currency_id']=$row[csf('currency_id')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('section')]][$row[csf('sub_section')]][$disc][$row[csf('order_no')]][$row[csf('job_no_mst')]]['bill_amount']+=$row[csf('bill_amount')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('section')]][$row[csf('sub_section')]][$disc][$row[csf('order_no')]][$row[csf('job_no_mst')]]['quantity']+=$row[csf('quantity')];
		//[$row[csf('bill_date')]]
	}
 
	/*echo "<pre>";
	print_r($trims_bill_data_arr);
	echo "</pre>"; die;*/
 
 
	
	$width=3100;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="30" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="30" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="30" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Trims WO No.</th>
                    <th width="100">Trims Group</th>
                    <th width="100">Section</th>
                    <th width="100">Sub-Section</th>
                    <th width="100">Customer WO No</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">Trims Ref</th>
                    <th width="100">Customer Buyer</th>
                    <th width="100">Party Name</th>
                    <th width="100">Order Rcv date</th>
                    <th width="100">Tgt.Trims Delv.date</th>
                    <th width="100">Actual Delivery Date</th>
                    <th width="100" class="delivery_challan">Delv Chlln No</th>
                    <th width="150">Item Description</th>
                    <th width="100">Item Color</th>
                    <th width="100">WO UOM</th>
                    <th width="100">WO Qty</th>
                    <th width="100">U/Price (TK)</th>
                    <th width="100">U/Price ($)</th>
                    <th width="100">WO Value ($)</th>
                    <th width="100">Current Delv Qty</th>
                    <th width="100">Curr. Delv Value ($)</th>
                    <th width="100">Total Delv Qty</th>
                    <th width="100">Total Delv Value ($)</th>
                    <th width="100">Bill No.</th>
                    <th width="100">Bill Value ($)</th>
                    <th width="100">Bill Balance Value ($)</th>
                    <th width="100">Delv.  Bal.Qty</th>
                    <th width="100">Delv. Bal. Value ($)</th>
                    <th>Remarks</th>
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
				$i=1;
				$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
				$total_bill_amount_usd=0;
				$total_bill_balance=0;
				
				foreach($date_array as $section_key_id=>$section_data)
				{
					foreach($section_data as $sub_section_key_id=>$sub_section_key_data)
					{
						foreach($sub_section_key_data as $item_description_id=>$item_description_data)
						{
							foreach($item_description_data as $order_no_id=>$order_no_data)
							{
								foreach($order_no_data as $job_no_mst_id=>$deleverydate_data)
								{
									$trims_del="";
									
									foreach($deleverydate_data as $dev_date_id=>$row)
									{
										$color_name="";
                      
										$color=explode(",",$row["color_id"]);
										foreach($color as $colors){
				
											$color_name.=$colorNameArr[$colors].", ";
										}
										//$total_delevery_qty[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['delevery_qty']
										$delevery_quantity=$row[delevery_qty];
										//$delevery_quantity=$total_delevery_qty_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]]['deleveryqty'];	
										//echo $row[section_id].'++<br>'.		
										$orderquantity=$booked_array[$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$row[received_id]]['order_quantity'];
										$orderamount=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]]['amount'];
										$rate=number_format($orderamount/$orderquantity,4);
										$currency_id=$order_array[$row[received_id]]['currency_id'];

										if($currency_id==1)
										{
											 $takarate=$rate;
											 $orderamounttaka=$orderamount;
											 $usdrate=number_format($rate/$currency_rate,4);
											 $orderamountusd=$orderamount/$currency_rate;
											 $delevery_valu_taka=$row[delevery_qty]/$row[conv_factor]*$takarate;
											 $delevery_valu_usd=$row[delevery_qty]/$row[conv_factor]*$usdrate;
											 
											 $total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
											 $total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
										}
										elseif($currency_id==2)
										{
											$takarate=number_format($rate*$currency_rate,4);
											$orderamounttaka=$orderamount*$currency_rate;
											$usdrate=$rate;
											$orderamountusd=$orderamount;
											$delevery_valu_taka=$row[delevery_qty]/$row[conv_factor]*$takarate;
											$delevery_valu_usd=$row[delevery_qty]/$row[conv_factor]*$usdrate;
											$total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
											$total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
										}
										$trims_del=implode(",",array_unique(explode(",",chop($row[trims_del],','))));
										
										$item_description_id=TRIM($item_description_id);
										$trims_bill=$trims_bill_data_arr[$row[id]][$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$job_no_mst_id]['trims_bill'];
										//$trims_bill=chop($trims_bill,',');
										$trims_bill=implode(",",array_unique(explode(",",chop($trims_bill,','))));
										$bill_amount=$trims_bill_data_arr[$row[id]][$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$job_no_mst_id]['bill_amount'];
										$bill_quantity=$trims_bill_data_arr[$row[id]][$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$job_no_mst_id]['quantity'];
										$bill_currency_id=$trims_bill_data_arr[$row[id]][$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$job_no_mst_id]['currency_id'];
										

									?>
					                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					                	<td width="35"  align="center"><? echo $i;?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $order_array[$row[received_id]]['subcon_job'] ;?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $item_name_arr[$row[item_group]];?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$row[section_id]];?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $trims_sub_section[$row[sub_section]];?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $order_array[$row[received_id]]['cust_order_no'] ;?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $buyer_po_arr[$row[buyer_po_no]]['grouping'] ;?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $order_array[$row[received_id]]['trims_ref']; ?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $buyer_buyer=($order_array[$row[received_id]]['within_group']==1)?$buyerArr[$order_array[$row[received_id]]['buyer_buyer']]:$row['buyer_buyer'];?></td>
					                    <td width="100" style="word-break: break-all;" align="left"><? echo $party=($order_array[$row[received_id]]['within_group']==1)?$companyArr[$order_array[$row[received_id]]['party_id']]:$buyerArr[$order_array[$row[received_id]]['party_id']];?></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $order_array[$row[received_id]]['receive_date'] ;?></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $order_array[$row[received_id]]['delivery_target_date']; ?></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $row[delivery_date]; ?></td>
					                    <td class="delivery_challan" width="100" style="word-break: break-all;" align="center">
											<a href="#" onclick="print_delv_challan(<?=$row['company_id']?>,<?=$row['id']?>,'<?=$trims_del?>', '<?=$row['within_group']?>', '<?=$order_array[$row[received_id]]['cust_order_no']?>');"><? echo $trims_del; ?></a>
										</td>
					                   	 <td width="150" style="word-break: break-all;" align="left"><p><? echo $row[item_description];?></p></td>
					                   	 <td width="100" style="word-break: break-all;" align="left"><p><? echo rtrim( $color_name,", ");?></p></td>
					                    <td width="100" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$order_array[$row[received_id]]['order_uom']]//$unit_of_measurement[$row[uom]];?></td>  			 
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderquantity,2);?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($takarate,4);?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($usdrate,4);?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderamountusd); $total_orderamount_usd+=$orderamountusd; ?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($row[delevery_qty],2); $total_delevery_qty+=number_format($row[delevery_qty],2,".","") ;?></td>

					                     <td width="100" style="word-break: break-all;" align="right"><? //echo $row[delevery_qty]*$usdrate;
										 if ($row[conv_factor])echo number_format($row[delevery_qty]*$usdrate,4,".","") ;else echo $delevery_valu_usd=0; $total_delevery_valu_usd+=number_format($row[delevery_qty]*$usdrate,4,".","") ;  ?></td>

					                    <td width="100" style="word-break: break-all;" align="right"><? echo  number_format($delevery_quantity);  //$usdrate ?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? //echo $delevery_quantity*$usdrate;
										if ($row[conv_factor])echo number_format($delevery_quantity*$usdrate,4);else echo $total_delevery_valu_usd2=0; $total_delevery_valu_usd2+=number_format($delevery_quantity*$usdrate,4,".",""); ?></td>

					                    <td width="100" style="word-break: break-all;" align="center"><? echo $trims_bill;?></td>
					                    <td width="100" style="word-break: break-all;" align="right" title="<? echo $bill_quantity."_".$usdrate; ?>"><? $bill_amount_usd=$bill_quantity*$usdrate; echo number_format($bill_amount_usd,4);?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? $deli_amount_usd=$row[delevery_qty]*$usdrate;
					                    $bill_balance=$deli_amount_usd-$bill_amount_usd;
					                     echo number_format($bill_balance,4);?></td>

					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderquantity-$delevery_quantity);?></td>
					                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format((($orderquantity-$delevery_quantity)*$usdrate),4,".","");?></td>
					                    <td style="word-break: break-all;"><? echo $row[remarks]; ?></td>
					                </tr>
					                <? 
										$prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty']+=($row[delevery_qty]*$usdrate);
										$i++;

										$total_bill_amount_usd+=$bill_amount_usd;
										$total_bill_balance+=$bill_balance;

									}
							}
						}
					}
				}
			}
		
			
			//print_r($prod_sammary_array);
				?>
                
                
       		 </table>
        </div>
        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
                	<th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100" class="delivery_challan"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100">Total:</th>
                    <th width="100" id="value_tot_delevery_qty" ><? echo number_format($total_delevery_qty);?></th>
                    <th width="100" id="value_tot_delevery_valu_usd" ><? echo number_format($total_delevery_valu_usd,4);?></th>
                    <th width="100"></th>
                    <th width="100" id="value_tot_delevery_valu_usd2" ><? echo number_format($total_delevery_valu_usd2,4);?></th>

                    <th width="100"></th>
                    <th width="100" id="value_tot_bill_amount_usd" ><? echo number_format($total_bill_amount_usd,4);?></th>
                    <th width="100" id="value_tot_bill_balance" ><? echo number_format($total_bill_balance,4);?></th>

                    <th width="100"></th>
                    <th width="100"></th>
                    <th></th>
				</tfoot>
			</table>
       
    </div>
    <div align="center" style="height:auto; width:500px; margin:0 auto; padding:0;">
    	<table width="500" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead class="form_caption" >
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold" ><? echo "Summary"; ?></td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="500" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Section</th>
                    <th width="100">Sub-Section</th>
                    <th>Curr. Delv Value ($)</th>
				</thead>
			</table>
        
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="500" rules="all" align="left">
             <? 
			 
			 /* $sammary_sql=" select a.party_id,a.production_date,a.received_id,a.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.sub_section,b.machine_id,c.conv_factor,c.job_quantity,c.uom ,c.buyer_po_no,c.item_description,c.job_no_mst,c.break_id,d.order_no from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d  where  a.id=b.mst_id and c.id=b.job_dtls_id  and   c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con $sql_cond  group by a.party_id,a.production_date,a.received_id,a.section_id,b.job_dtls_id,c.sub_section,c.conv_factor,c.job_quantity,c.uom,c.buyer_po_no,c.item_description,c.job_no_mst,b.qc_qty,c.break_id,b.uom,b.machine_id,d.order_no"; 
			  */
			/*  $sammary_sql= "select a.id,a.trims_del,a.del_no_prefix,a.del_no_prefix_num,a.party_id,a.currency_id,a.delivery_date,a.challan_no,b.received_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id,b.order_id,b.order_no,b.buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section as section_id,b.item_group,b.order_uom, b.order_quantity, b.delevery_qty,b.claim_qty,b.remarks,b.description as item_description, b.color_id, b.size_id,b.color_name,b.size_name,b.delevery_status,b.workoder_qty,b.order_receive_rate,b.break_down_details_id,c.sub_section,c.job_no_mst,c.conv_factor from trims_delivery_mst a, trims_delivery_dtls b,trims_job_card_dtls c where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.id =b.job_dtls_id $sql_cond $where_con  $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id,a.trims_del,a.del_no_prefix,a.del_no_prefix_num,a.party_id,a.currency_id,a.delivery_date,a.challan_no,b.received_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id,b.order_id,b.order_no,b.buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section,b.item_group,b.order_uom, b.order_quantity, b.delevery_qty,b.claim_qty,b.remarks,b.description, b.color_id, b.size_id,b.color_name,b.size_name,b.delevery_status,b.workoder_qty,b.order_receive_rate,b.break_down_details_id,c.sub_section,c.job_no_mst,c.conv_factor  order by a.id DESC"; 
			  */
			$sammary_sql= "select a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,c.section as section_id,b.delevery_qty,b.remarks,b.description as item_description, b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac as conv_factor from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c where  a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con  $company $party_id_cond $withinGroup $search_com $withinGroup   group by a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,c.section, b.delevery_qty,b.remarks,b.description,b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac  order by a.id DESC";
			  
			$sammary_result = sql_select($sammary_sql);
	        $sammary_array=array();
	        foreach($sammary_result as $row)
	        {
	       	 	$sammary_array[$row[csf('section_id')]][$row[csf('sub_section')]]['section_id']=$row[csf('section_id')];
				$sammary_array[$row[csf('section_id')]][$row[csf('sub_section')]]['sub_section']=$row[csf('sub_section')];
	 		}
		
				$t=1;
				foreach($sammary_array as $section_key_id=>$section_data)
				{
					$section_total=0;
					foreach($section_data as $sub_section_key_id=>$row)
					{
						?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
                            <td width="35"  align="center"><? echo $t;?></td>
                            <td width="100" align="center"><? echo $trims_section[$row[section_id]];?></td>
                            <td width="100" align="center"><? echo $trims_sub_section[$row[sub_section]];?></td>
                            <td align="right"><? echo $product_valu_usd=number_format($prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty'],2);$section_total+=$prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty'];?></td>
                           </tr>
						<? 
					$t++;
					}
					?>
                    <tr style="background-color:#CCC">
                   	 	<td colspan="3" align="right"><b><? echo $trims_section[$row[section_id]];?> Total</b></td>
                    	<td align="right"><b><? echo number_format($section_total,2);?></b></td>
                    </tr>
                 	<?
					$grand_section_total+=$section_total;
				} 
				?>
				<tfoot>
					<tr style="background-color:#CCC">
                   	 	<td colspan="3" align="right"><b>Grand Total</b></td>
                    	<td align="right"><b><? echo number_format($grand_section_total,2);?></b></td>
                    </tr>
				</tfoot>
       		 </table>
    </div>
	<?
}
else if($report_type==2)
{

	$order_sql="select a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section, b.item_group,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.qnty,c.order_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id,b.delivery_date as delivery_target_date, a.trims_ref
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id $where_con_ord and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $sql_order_no";
		$order_sql_result = sql_select($order_sql);
		$order_array=array();
		$booked_array=array();
		$booked_sammary_array=array();
		foreach($order_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['receive_date']=$row[csf('receive_date')]; 
			$order_array[$row[csf('id')]]['delivery_date']=$row[csf('delivery_date')];
			$order_array[$row[csf('id')]]['delivery_target_date']=$row[csf('delivery_target_date')];
			$order_array[$row[csf('id')]]['cust_order_no']=$row[csf('cust_order_no')];
			$order_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
			$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			$order_array[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$order_array[$row[csf('id')]]['within_group']=$row[csf('within_group')];
			$order_array[$row[csf('id')]]['exchange_rate']=$row[csf('exchange_rate')];
			$order_array[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
			$order_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
			$order_array[$row[csf('id')]]['item_group']=$row[csf('item_group')];
			$order_array[$row[csf('id')]]['trims_ref']=$row[csf('trims_ref')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['order_quantity']+=$row[csf('qnty')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['amount']+=$row[csf('amount')];
			
			$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['order_quantity']+=$row[csf('qnty')];
			$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['amount']+=$row[csf('amount')];
		}
		/*echo "<pre>";
		print_r($booked_array);*/
	
	 
	$trims_order_sql= "select a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.break_down_details_id,b.order_no,b.buyer_po_no,c.section as section_id,b.item_group,b.delevery_qty,b.remarks,b.description as item_description, b.color_id, b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac as conv_factor ,d.qnty,d.order_id ,d.qnty,d.rate,d.amount,d.booked_qty,d.description as item_description,d.id as breakdown_id from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d where c.id=d.mst_id and d.id=b.break_down_details_id and  a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con  $company $buyer_po_id_cond  order by a.id DESC"; 
	  
		$result = sql_select($trims_order_sql);
        $date_array=array();
		$deli_id_arr=array();
        foreach($result as $row)
        {
			$deli_id_arr[$row[csf("id")]]=$row[csf("id")];
			
       	 	$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['id']=$row[csf('id')];
       	 	$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['section_id']=$row[csf('section_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['sub_section']=$row[csf('sub_section')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['item_group']=$row[csf('item_group')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['production_date']=$row[csf('production_date')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['delivery_date']=$row[csf('delivery_date')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['received_id']=$row[csf('received_id')];
       		$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['job_quantity']=$row[csf('job_quantity')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['uom']=$row[csf('uom')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['conv_factor']=$row[csf('conv_factor')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['item_description']=$row[csf('item_description')];			            
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['job_no_mst']=$row[csf('job_no_mst')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['qc_qty']+=$row[csf('qc_qty')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['break_id']=$row[csf('break_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['product_uom']=$row[csf('product_uom')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['machine_id']=$row[csf('machine_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['order_no']=$row[csf('order_no')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['delevery_qty'] +=$row[csf('delevery_qty')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['remarks']=$row[csf('remarks')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['color_id']=$row[csf('color_id')];
			$date_array[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]][$row[csf('delivery_date')]][$row[csf('color_id')]]['trims_del'].=$row[csf('trims_del')].',';
 		}

 		$deli_id_arr = array_unique($deli_id_arr);
 
 
 	//echo "<pre>";
	//print_r($date_array);
	//die;
 
	

	$trims_delevery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,c.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id,b.order_no from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 

	// echo $trims_delevery_sql;

	$trims_delivery_data_arr=array();
	$result_trims_delevery_sql = sql_select($trims_delevery_sql);
	foreach($result_trims_delevery_sql as $row)
	{
		$total_delevery_qty_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]]['deleveryqty']+=$row[csf('delevery_qty')];
	}
 
	//echo "<pre>";
	//print_r($total_delevery_qty_array);

	$delivery_con=where_con_using_array($deli_id_arr,0,"c.mst_id");



	$trims_bill_sql = "select a.id, a.trims_bill, a.within_group, a.currency_id, a.exchange_rate, a.bill_date, b.quantity, b.bill_rate, b.bill_amount, c.mst_id, d.section, c.description, c.order_no, d.job_no_mst, d.sub_section
	from trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c, subcon_ord_dtls d where  a.entry_form=276 and a.id=b.mst_id and c.id=b.production_dtls_id and d.id=c.receive_dtls_id $delivery_con and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id ASC"; //e.id=c.mst_id and //trims_delivery_mst e //e.delivery_date

	//echo $trims_bill_sql; die;

	$result_bill_sql=sql_select($trims_bill_sql);
	$trims_bill_data_arr=array();
	foreach($result_bill_sql as $row)
	{
		$disc=TRIM($row[csf('description')]);
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('section')]][$row[csf('sub_section')]][$disc][$row[csf('order_no')]][$row[csf('job_no_mst')]]['trims_bill'].=$row[csf('trims_bill')].',';
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('section')]][$row[csf('sub_section')]][$disc][$row[csf('order_no')]][$row[csf('job_no_mst')]]['currency_id']=$row[csf('currency_id')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('section')]][$row[csf('sub_section')]][$disc][$row[csf('order_no')]][$row[csf('job_no_mst')]]['bill_amount']+=$row[csf('bill_amount')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('section')]][$row[csf('sub_section')]][$disc][$row[csf('order_no')]][$row[csf('job_no_mst')]]['quantity']+=$row[csf('quantity')];
		//[$row[csf('bill_date')]]
	}
 
	/*echo "<pre>";
	print_r($trims_bill_data_arr);
	echo "</pre>"; die;*/
 
 
	
	$width=3100;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="30" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="30" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="30" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Trims WO No.</th>
                    <th width="100">Trims Group</th>
                    <th width="100">Section</th>
                    <th width="100">Sub-Section</th>
                    <th width="100">Customer WO No</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">Trims Ref</th>
                    <th width="100">Customer Buyer</th>
                    <th width="100">Party Name</th>
                    <th width="100">Order Rcv date</th>
                    <th width="100">Tgt.Trims Delv.date</th>
                    <th width="100">Actual Delivery Date</th>
                    <th width="100">Delv Chlln No</th>
                    <th width="150">Item Description</th>
                    <th width="100">Item Color</th>
                    <th width="100">WO UOM</th>
                    <th width="100">WO Qty</th>
                    <th width="100">U/Price (TK)</th>
                    <th width="100">U/Price ($)</th>
                    <th width="100">WO Value ($)</th>
                    <th width="100">Current Delv Qty</th>
                    <th width="100">Curr. Delv Value ($)</th>
                    <th width="100">Total Delv Qty</th>
                    <th width="100">Total Delv Value ($)</th>
                    <th width="100">Bill No.</th>
                    <th width="100">Bill Value ($)</th>
                    <th width="100">Bill Balance Value ($)</th>
                    <th width="100">Delv.  Bal.Qty</th>
                    <th width="100">Delv. Bal. Value ($)</th>
                    <th>Remarks</th>
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
				$i=1;
				$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
				$total_bill_amount_usd=0;
				$total_bill_balance=0;
				
				foreach($date_array as $section_key_id=>$section_data)
				{
					foreach($section_data as $sub_section_key_id=>$sub_section_key_data)
					{
						foreach($sub_section_key_data as $item_description_id=>$item_description_data)
						{
							foreach($item_description_data as $order_no_id=>$order_no_data)
							{
								foreach($order_no_data as $job_no_mst_id=>$deleverydate_data)
								{
									$trims_del="";
									
									foreach($deleverydate_data as $color_data)
									{
											foreach($color_data as $color_data_id=>$row)
										{
											// $color_name="";
						
											// $color=explode(",",$row["color_id"]);
											// foreach($color as $colors){
					
											// 	$color_name.=$colorNameArr[$colors].", ";
											// }
											//$total_delevery_qty[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['delevery_qty']
											$delevery_quantity=$row[delevery_qty];
											//$delevery_quantity=$total_delevery_qty_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]]['deleveryqty'];	
											//echo $row[section_id].'++<br>'.		
											$orderquantity=$booked_array[$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$row[received_id]]['order_quantity'];
											$orderamount=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]]['amount'];
											$rate=number_format($orderamount/$orderquantity,4);
											$currency_id=$order_array[$row[received_id]]['currency_id'];

											if($currency_id==1)
											{
												$takarate=$rate;
												$orderamounttaka=$orderamount;
												$usdrate=number_format($rate/$currency_rate,4);
												$orderamountusd=$orderamount/$currency_rate;
												$delevery_valu_taka=$row[delevery_qty]/$row[conv_factor]*$takarate;
												$delevery_valu_usd=$row[delevery_qty]/$row[conv_factor]*$usdrate;
												
												$total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
												$total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
											}
											elseif($currency_id==2)
											{
												$takarate=number_format($rate*$currency_rate,4);
												$orderamounttaka=$orderamount*$currency_rate;
												$usdrate=$rate;
												$orderamountusd=$orderamount;
												$delevery_valu_taka=$row[delevery_qty]/$row[conv_factor]*$takarate;
												$delevery_valu_usd=$row[delevery_qty]/$row[conv_factor]*$usdrate;
												$total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
												$total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
											}
											$trims_del=implode(",",array_unique(explode(",",chop($row[trims_del],','))));
											
											$item_description_id=TRIM($item_description_id);
											$trims_bill=$trims_bill_data_arr[$row[id]][$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$job_no_mst_id]['trims_bill'];
											//$trims_bill=chop($trims_bill,',');
											$trims_bill=implode(",",array_unique(explode(",",chop($trims_bill,','))));
											$bill_amount=$trims_bill_data_arr[$row[id]][$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$job_no_mst_id]['bill_amount'];
											$bill_quantity=$trims_bill_data_arr[$row[id]][$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$job_no_mst_id]['quantity'];
											$bill_currency_id=$trims_bill_data_arr[$row[id]][$section_key_id][$sub_section_key_id][$item_description_id][$order_no_id][$job_no_mst_id]['currency_id'];
											

										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="35"  align="center"><? echo $i;?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $order_array[$row[received_id]]['subcon_job'] ;?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $item_name_arr[$row[item_group]];?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$row[section_id]];?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $trims_sub_section[$row[sub_section]];?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $order_array[$row[received_id]]['cust_order_no'] ;?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $buyer_po_arr[$row[buyer_po_no]]['grouping'] ;?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $order_array[$row[received_id]]['trims_ref']; ?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $buyer_buyer=($order_array[$row[received_id]]['within_group']==1)?$buyerArr[$order_array[$row[received_id]]['buyer_buyer']]:$row['buyer_buyer'];?></td>
											<td width="100" style="word-break: break-all;" align="left"><? echo $party=($order_array[$row[received_id]]['within_group']==1)?$companyArr[$order_array[$row[received_id]]['party_id']]:$buyerArr[$order_array[$row[received_id]]['party_id']];?></td>
											<td width="100" style="word-break: break-all;" align="center"><? echo $order_array[$row[received_id]]['receive_date'] ;?></td>
											<td width="100" style="word-break: break-all;" align="center"><? echo $order_array[$row[received_id]]['delivery_target_date']; ?></td>
											<td width="100" style="word-break: break-all;" align="center"><? echo $row[delivery_date]; ?></td>
											<td width="100" style="word-break: break-all;" align="center"><? echo $trims_del; ?></td>
											<td width="150" style="word-break: break-all;" align="left"><p><? echo $row[item_description];?></p></td>
											<td width="100" style="word-break: break-all;" align="left"><p><? echo $colorNameArr[$row["color_id"]];?></p></td>
											<td width="100" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$order_array[$row[received_id]]['order_uom']]//$unit_of_measurement[$row[uom]];?></td>  			 
											<td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderquantity,2);?></td>
											<td width="100" style="word-break: break-all;" align="right"><? echo number_format($takarate,4);?></td>
											<td width="100" style="word-break: break-all;" align="right"><? echo number_format($usdrate,4);?></td>
											<td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderamountusd); $total_orderamount_usd+=$orderamountusd; ?></td>
											<td width="100" style="word-break: break-all;" align="right"><? echo number_format($row[delevery_qty],2); $total_delevery_qty+=number_format($row[delevery_qty],2,".","") ;?></td>

											<td width="100" style="word-break: break-all;" align="right"><? //echo $row[delevery_qty]*$usdrate;
											if ($row[conv_factor])echo number_format($row[delevery_qty]*$usdrate,4,".","") ;else echo $delevery_valu_usd=0; $total_delevery_valu_usd+=number_format($row[delevery_qty]*$usdrate,4,".","") ;  ?></td>

											<td width="100" style="word-break: break-all;" align="right"><? echo  number_format($delevery_quantity);  //$usdrate ?></td>
											<td width="100" style="word-break: break-all;" align="right"><? //echo $delevery_quantity*$usdrate;
											if ($row[conv_factor])echo number_format($delevery_quantity*$usdrate,4);else echo $total_delevery_valu_usd2=0; $total_delevery_valu_usd2+=number_format($delevery_quantity*$usdrate,4,".",""); ?></td>

											<td width="100" style="word-break: break-all;" align="center"><? echo $trims_bill;?></td>
											<td width="100" style="word-break: break-all;" align="right" title="<? echo $bill_quantity."_".$usdrate; ?>"><? $bill_amount_usd=$bill_quantity*$usdrate; echo number_format($bill_amount_usd,4);?></td>
											<td width="100" style="word-break: break-all;" align="right"><? $deli_amount_usd=$row[delevery_qty]*$usdrate;
											$bill_balance=$deli_amount_usd-$bill_amount_usd;
											echo number_format($bill_balance,4);?></td>

											<td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderquantity-$delevery_quantity);?></td>
											<td width="100" style="word-break: break-all;" align="right"><? echo number_format((($orderquantity-$delevery_quantity)*$usdrate),4,".","");?></td>
											<td style="word-break: break-all;"><? echo $row[remarks]; ?></td>
										</tr>
										<? 
											$prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty']+=($row[delevery_qty]*$usdrate);
											$i++;

											$total_bill_amount_usd+=$bill_amount_usd;
											$total_bill_balance+=$bill_balance;

										}
								}
							}
						}
					}
				}
			}
		
			
			//print_r($prod_sammary_array);
				?>
                
                
       		 </table>
        </div>
        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
                	<th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100">Total:</th>
                    <th width="100" id="value_tot_delevery_qty" ><? echo number_format($total_delevery_qty);?></th>
                    <th width="100" id="value_tot_delevery_valu_usd" ><? echo number_format($total_delevery_valu_usd,4);?></th>
                    <th width="100"></th>
                    <th width="100" id="value_tot_delevery_valu_usd2" ><? echo number_format($total_delevery_valu_usd2,4);?></th>

                    <th width="100"></th>
                    <th width="100" id="value_tot_bill_amount_usd" ><? echo number_format($total_bill_amount_usd,4);?></th>
                    <th width="100" id="value_tot_bill_balance" ><? echo number_format($total_bill_balance,4);?></th>

                    <th width="100"></th>
                    <th width="100"></th>
                    <th></th>
				</tfoot>
			</table>
       
    </div>
    <div align="center" style="height:auto; width:500px; margin:0 auto; padding:0;">
    	<table width="500" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead class="form_caption" >
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold" ><? echo "Summary"; ?></td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="500" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Section</th>
                    <th width="100">Sub-Section</th>
                    <th>Curr. Delv Value ($)</th>
				</thead>
			</table>
        
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="500" rules="all" align="left">
             <? 
			 
			 /* $sammary_sql=" select a.party_id,a.production_date,a.received_id,a.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.sub_section,b.machine_id,c.conv_factor,c.job_quantity,c.uom ,c.buyer_po_no,c.item_description,c.job_no_mst,c.break_id,d.order_no from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d  where  a.id=b.mst_id and c.id=b.job_dtls_id  and   c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con $sql_cond  group by a.party_id,a.production_date,a.received_id,a.section_id,b.job_dtls_id,c.sub_section,c.conv_factor,c.job_quantity,c.uom,c.buyer_po_no,c.item_description,c.job_no_mst,b.qc_qty,c.break_id,b.uom,b.machine_id,d.order_no"; 
			  */
			/*  $sammary_sql= "select a.id,a.trims_del,a.del_no_prefix,a.del_no_prefix_num,a.party_id,a.currency_id,a.delivery_date,a.challan_no,b.received_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id,b.order_id,b.order_no,b.buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section as section_id,b.item_group,b.order_uom, b.order_quantity, b.delevery_qty,b.claim_qty,b.remarks,b.description as item_description, b.color_id, b.size_id,b.color_name,b.size_name,b.delevery_status,b.workoder_qty,b.order_receive_rate,b.break_down_details_id,c.sub_section,c.job_no_mst,c.conv_factor from trims_delivery_mst a, trims_delivery_dtls b,trims_job_card_dtls c where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.id =b.job_dtls_id $sql_cond $where_con  $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id,a.trims_del,a.del_no_prefix,a.del_no_prefix_num,a.party_id,a.currency_id,a.delivery_date,a.challan_no,b.received_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id,b.order_id,b.order_no,b.buyer_po_id,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section,b.item_group,b.order_uom, b.order_quantity, b.delevery_qty,b.claim_qty,b.remarks,b.description, b.color_id, b.size_id,b.color_name,b.size_name,b.delevery_status,b.workoder_qty,b.order_receive_rate,b.break_down_details_id,c.sub_section,c.job_no_mst,c.conv_factor  order by a.id DESC"; 
			  */
			$sammary_sql= "select a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,c.section as section_id,b.delevery_qty,b.remarks,b.description as item_description, b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac as conv_factor from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c where  a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con  $company $party_id_cond $withinGroup $search_com $withinGroup   group by a.id,a.trims_del,a.delivery_date,b.received_id, b.job_dtls_id,b.production_dtls_id,b.order_no,b.buyer_po_no,c.section, b.delevery_qty,b.remarks,b.description,b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac  order by a.id DESC";
			  
			$sammary_result = sql_select($sammary_sql);
	        $sammary_array=array();
	        foreach($sammary_result as $row)
	        {
	       	 	$sammary_array[$row[csf('section_id')]][$row[csf('sub_section')]]['section_id']=$row[csf('section_id')];
				$sammary_array[$row[csf('section_id')]][$row[csf('sub_section')]]['sub_section']=$row[csf('sub_section')];
	 		}
		
				$t=1;
				foreach($sammary_array as $section_key_id=>$section_data)
				{
					$section_total=0;
					foreach($section_data as $sub_section_key_id=>$row)
					{
						?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
                            <td width="35"  align="center"><? echo $t;?></td>
                            <td width="100" align="center"><? echo $trims_section[$row[section_id]];?></td>
                            <td width="100" align="center"><? echo $trims_sub_section[$row[sub_section]];?></td>
                            <td align="right"><? echo $product_valu_usd=number_format($prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty'],2);$section_total+=$prod_sammary_array[$row[section_id]][$row[sub_section]]['delevery_qty'];?></td>
                           </tr>
						<? 
					$t++;
					}
					?>
                    <tr style="background-color:#CCC">
                   	 	<td colspan="3" align="right"><b><? echo $trims_section[$row[section_id]];?> Total</b></td>
                    	<td align="right"><b><? echo number_format($section_total,2);?></b></td>
                    </tr>
                 	<?
					$grand_section_total+=$section_total;
				} 
				?>
				<tfoot>
					<tr style="background-color:#CCC">
                   	 	<td colspan="3" align="right"><b>Grand Total</b></td>
                    	<td align="right"><b><? echo number_format($grand_section_total,2);?></b></td>
                    </tr>
				</tfoot>
       		 </table>
    </div>
	<?

}
else if($report_type==3)
{

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");


	$order_sql="select a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section, b.item_group,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.qnty,c.order_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id,b.delivery_date as delivery_target_date, a.trims_ref
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id $where_con_ord and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $sql_order_no";
	$order_sql_result = sql_select($order_sql);

	$booked_array=array();
	$booked_sammary_array=array();
	foreach($order_sql_result as $row)
	{
		$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['order_quantity']+=$row[csf('qnty')];
		$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['amount']+=$row[csf('amount')];
		
		$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['order_quantity']+=$row[csf('qnty')];
		$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['amount']+=$row[csf('amount')];
	}

	$trims_order_sql= "select a.within_group, a.party_id, a.currency_id, c.section as section_id,c.sub_section, d.description as item_description,d.order_id,b.order_no,  b.received_id, b.delevery_qty,c.booked_conv_fac as conv_factor , d.qnty,d.rate,d.amount,d.booked_qty,d.id as breakdown_id,c.rate,b.order_receive_rate as order_rate from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d where c.id=d.mst_id and d.id=b.break_down_details_id and a.id=b.mst_id and c.id=b.receive_dtls_id and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con  $company $buyer_po_id_cond  order by a.id DESC";

	$trims_order_sql_result = sql_select($trims_order_sql);
	$delevery_data_arr = array();
	$section_arr = array();
	
	foreach($trims_order_sql_result as $data)
	{
		$section_arr[]=$data[csf('section_id')];

		$currency_id = $data[csf('currency_id')];

		$rate = $data[csf('order_rate')];

		$orderquantity=$booked_array[$data[csf('section_id')]][$data[csf('sub_section')]][$data[csf('item_description')]][$data[csf('order_no')]][$data[csf('received_id')]]['order_quantity'];
		$orderamount=$booked_array[$data[csf('section_id')]][$data[csf('sub_section')]][$data[csf('item_description')]][$data[csf('order_no')]][$data[csf('received_id')]]['amount'];
		//$rate=number_format($orderamount/$orderquantity,4);
		

		if($currency_id==1)
		{

			$usdrate=number_format($rate/$currency_rate,4);
			$delevery_valu_usd=$data[csf('delevery_qty')]/$data[csf('conv_factor')]*$usdrate;
			
		}
		elseif($currency_id==2)
		{
			$usdrate=$rate;
			$delevery_valu_usd=$data[csf('delevery_qty')]*$usdrate;
		}
		
 		$delevery_data_arr[$data[csf('within_group')]][$data[csf('party_id')]][$data[csf('section_id')]]['delevery_qty']+= $delevery_valu_usd;
	}

	$section_arr = array_unique($section_arr);

	//$delivery_sql = "select a.party_id, a.within_group, a.currency_id, b.section, delevery_qty as delevery_qty, d.rate, c.exchange_rate, d.booked_conv_fac as conv_factor from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_mst c, subcon_ord_dtls d where a.company_id =$cbo_company_id and a.id=b.mst_id and a.entry_form=208 and c.id=a.received_id and c.id=d.mst_id and d.id=b.RECEIVE_DTLS_ID and d.order_no=b.order_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $where_con_del $buyer_po_id_cond";

	//$sql_result = sql_select($delivery_sql);


	//$delevery_data_arr = array();
	/*
	foreach($sql_result as $data)
	{
		$section_arr[]=$data[csf('section')];

		$currency_id = $data[csf('currency_id')];

		$rate = $data[csf('rate')];

		if($currency_id==1)
		{
			$usdrate=number_format($rate/$currency_rate,4);
			$delevery_valu_usd=$data[csf('delevery_qty')]/$data[csf('conv_factor')]*$usdrate;
			
		}
		elseif($currency_id==2)
		{
			$usdrate=$rate;
			$delevery_valu_usd=$data[csf('delevery_qty')]*$usdrate;
		}

		//$delevery_data_arr[$data[csf('within_group')]][$data[csf('party_id')]][$data[csf('section')]]['delevery_qty']+= $delevery_valu_usd;

		//$delevery_data_arr[$data[csf('within_group')]][$data[csf('party_id')]][$data[csf('section')]]['delevery_qty']+= $data[csf('delevery_qty')];

	}
	*/

	$section_num = count($section_arr);

	$width=300+100*$section_num;

	ob_start();
	?>
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="30" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="30" align="center" style="font-size:14px; font-weight:bold" >Date Wise Delivery Report(Sales Summary)</td>
				</tr>
				<tr>
					<td colspan="30" align="center" style="font-size:14px; font-weight:bold">
						<?
							if($txt_date_from!='')
							{
								echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;
							}
							
						?>
					</td>
				</tr>
			</thead>
		</table>
		<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
				<tr>
					<th width="35" rowspan="2">SL</th>
	                <th width="200" rowspan="2">Customer</th>
	                <th width="<?php echo 100*$section_num;?>" colspan="<?php echo $section_num; ?>">Section</th>
	                <th width="100" rowspan="2">Total ($)</th>
				</tr>
				<tr>
					<?php
						foreach($section_arr as $data)
						{
							?>
								<th width="100"><?php echo $trims_section[$data]; ?></th>
							<?
						}
					?>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
				<thead>
					<?php
						$i =1;
						$customer_name = '';
						$grand_section_total = 0;
						
						foreach($delevery_data_arr as $key=>$within_group_data)
						{
							foreach($within_group_data as $buyerid=>$buyer_data)
							{

								$delivery_qty = 0;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								if($key==1)
								{
									$customer_name = $companyArr[$buyerid];
								}
								else
								{
								$customer_name = $buyerArr[$buyerid];
								}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center" width="35"><?php echo $i;?></td>
							<td align="center" width="200"><?php echo $customer_name;?></td>
							<?php
								foreach($section_arr as $section_id)
								{
									?>
										<td align="right" width="100">
											<?php 

												foreach($buyer_data as $section=>$data)
												{

													if($section==$section_id){ 

														echo number_format($data['delevery_qty'],2);
														$delivery_qty +=$data['delevery_qty'];

														$section_total = "section_".$section_id;

														$$section_total += $data['delevery_qty'];

													} 
												}

											?>
										</td>
									<?
								}
							?>
							<td align="right" width="100"><?php echo number_format($delivery_qty,2);?></td>
						</tr>
						<?php
								$i++;

								$grand_section_total += $delivery_qty;
							}
						}
						?>
			</thead>
			<tfoot>
				<tr>
					<td align="right" colspan="2"><strong>Total</strong></td>
					<?php
						foreach($section_arr as $section_id)
						{
							$total = '';
							$section_total = "section_".$section_id;
							$total = $$section_total;
							?>
								<td align="right"><strong><?php echo number_format($total,2);?></strong></td>
							<?php
						}
					?>
					<td align="right"><strong><?php echo number_format($grand_section_total,2);?></strong></td>
				</tr>
			</tfoot>
			</table>
		</div>
	</div>
	<?
}
elseif($report_type==4)
{
	
	 $order_sql="SELECT e.id,e.subcon_job,e.receive_date,e.exchange_rate,e.currency_id,e.delivery_date,e.order_no as cust_order_no,e.party_id,e.within_group,c.buyer_buyer,c.section,c.sub_section, c.item_group,c.job_no_mst,c.order_no,c.booked_uom,c.order_uom,d.qnty,d.order_id,d.color_id,d.qnty,d.rate,d.amount,d.booked_qty,d.description,d.id as breakdown_id,c.delivery_date as delivery_target_date, e.trims_ref,c.buyer_style_ref
	from subcon_ord_mst e, subcon_ord_dtls c, subcon_ord_breakdown d
	where e.subcon_job=c.job_no_mst and c.job_no_mst=d.job_no_mst and c.id=d.mst_id and e.entry_form=255 and e.company_id =$cbo_company_id and e.is_deleted=0 and e.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1";  
		$order_sql_result = sql_select($order_sql);
		$order_array=array();
		$booked_array=array();
		$booked_sammary_array=array();
		foreach($order_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['receive_date']=$row[csf('receive_date')]; 
			$order_array[$row[csf('id')]]['delivery_date']=$row[csf('delivery_date')];
			$order_array[$row[csf('id')]]['delivery_target_date']=$row[csf('delivery_target_date')];
			$order_array[$row[csf('id')]]['cust_order_no']=$row[csf('cust_order_no')];
			$order_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
			$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			$order_array[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$order_array[$row[csf('id')]]['within_group']=$row[csf('within_group')];
			$order_array[$row[csf('id')]]['exchange_rate']=$row[csf('exchange_rate')];
			$order_array[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
			$order_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
			$order_array[$row[csf('id')]]['item_group']=$row[csf('item_group')];
			$order_array[$row[csf('id')]]['trims_ref']=$row[csf('trims_ref')];
			
			
			$booked_array[$row[csf('subcon_job')]][$row[csf('item_group')]][$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf("color_id")]]['order_quantity']+=number_format($row[csf('qnty')],4,'.',''); 
			
			$booked_array[$row[csf('subcon_job')]][$row[csf('item_group')]][$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf("color_id")]]['order_amount']+=number_format($row[csf('amount')],4,'.',''); 			
		}
		//echo "<pre>";
		//print_r($booked_array);

		if($txt_date_from!="" and $txt_date_to!="")
		{	
			$where_con_receive_date=" and e.receive_date between '$txt_date_from' and '$txt_date_to'";
			 
		}
		if($cbo_section_id)
		{
			$where_con_section=" and c.section='$cbo_section_id'"; 
		} 
		if($cbo_sub_section_id)
		{
		 
		  $where_con_sub_section=" and c.sub_section='$cbo_sub_section_id'";
		} 
		 
	 
	  $trims_order_sql= "SELECT a.id,a.trims_del,a.delivery_date as del_date,b.received_id,b.id as del_dtls_id, b.job_dtls_id,b.production_dtls_id,b.break_down_details_id,b.order_no,b.buyer_po_no,c.section as section_id,b.item_group,b.delevery_qty,b.remarks, b.color_id, b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac as conv_factor ,d.qnty,d.order_id, d.rate, d.amount,d.booked_qty,d.description as item_description,d.id as breakdown_id , e.subcon_job, e.order_no as cust_order_no, e.trims_ref,e.party_id,e.receive_date,c.delivery_date as delivery_target_date,c.order_uom,e.currency_id, c.buyer_style_ref,e.within_group from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d ,subcon_ord_mst e  where c.id=d.mst_id and d.id=b.break_down_details_id and  a.id=b.mst_id and c.id=b.receive_dtls_id and e.id=c.mst_id $trimsreceiveid_cond and b.delevery_qty>0 and e.entry_form=255  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con_receive_date  $company $buyer_po_id_cond  $where_con_section $where_con_sub_section  order by e.subcon_job,b.item_group,c.section,c.sub_section,d.description DESC"; 
 	 
 		$result = sql_select($trims_order_sql);
		
		$trims_delv_date_array=array();
		 foreach($result as $row)
         {
   	 
	 	$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["subcon_job"]=$row[csf("subcon_job")]; 

	 	$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["within_group"]=$row[csf("within_group")]; 
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["item_group"]=$row[csf("item_group")];

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["del_date"]=$row[csf("del_date")];

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["buyer_style_ref"]=$row[csf("buyer_style_ref")];
		 
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["section_id"]=$row[csf("section_id")]; 
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["sub_section"]=$row[csf("sub_section")]; 
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["color_id"]=$row[csf("color_id")]; 
			
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["item_description"]=$row[csf("item_description")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["trims_del"]=$row[csf("trims_del")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["order_no"]=$row[csf("order_no")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["buyer_po_no"]=$row[csf("buyer_po_no")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["conv_factor"]=$row[csf("conv_factor")]; 
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["rate"]=$row[csf("rate")]; 
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["booked_qty"]=$row[csf("booked_qty")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["item_description"]=$row[csf("item_description")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["delivery_target_date"]=$row[csf("delivery_target_date")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["order_uom"]=$row[csf("order_uom")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["currency_id"]=$row[csf("currency_id")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["receive_date"]=$row[csf("receive_date")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["cust_order_no"]=$row[csf("cust_order_no")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["trims_ref"]=$row[csf("trims_ref")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["party_id"]=$row[csf("party_id")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["receive_date"]=$row[csf("receive_date")]; 
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["del_dtls_id"]=$row[csf("del_dtls_id")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["job_dtls_id"]=$row[csf("job_dtls_id")];
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["production_dtls_id"]=$row[csf("production_dtls_id")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["break_down_details_id"]=$row[csf("break_down_details_id")]; 

		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["buyer_buyer"]=$row[csf("buyer_buyer")]; 
	
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["remarks"]=$row[csf("remarks")];
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["delevery_qty"]+=$row[csf("delevery_qty")];
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["order_qty"]+=$row[csf("qnty")];
		
		$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["order_amount"]+=$row[csf("amount")]; 
	 
 		}
		
		$count_row=array();
		
		    foreach($trims_delv_date_array as $subcon_job=>$subcon_job_data)
			{
				foreach($subcon_job_data as $item_group=>$item_group_data)
				{
			
					foreach($item_group_data as $section_id=>$section_data)
					{
						foreach($section_data as $sub_section=>$sub_section_data)
						{
			
							foreach($sub_section_data as $item_description=>$item_description_data)
							{
								foreach($item_description_data as $color_id=>$color_id_data)
								{
									
 									foreach($color_id_data as $trims_del=>$row)
									{
											$order_qty=$booked_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["order_quantity"];  
							 
											$order_ammount=$booked_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["order_amount"]; 
										$currency_id=$row["currency_id"];
							            $rate=number_format($order_ammount,4,'.','')/number_format($order_qty,4,'.','');
										if($currency_id==1)
										{
										$takarate=$rate;
										$usdrate=$rate/$currency_rate;
										$orderamountusd=number_format($order_ammount,4,'.','')/number_format($currency_rate,4,'.',''); ;
										}
										elseif($currency_id==2)
										{
												$takarate=$rate*$currency_rate;
												$usdrate=$rate;
												$orderamountusd=$order_ammount;
										}	
										
										$count_row[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]++; 
										
										$date_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["delevery_qty"]+=number_format($row["delevery_qty"],4,'.','');
										
										//$date_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["order_qty"]+=number_format($row["order_qty"],4,'.','');
											
										$date_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["delevery_value"]+=number_format($row["delevery_qty"],4,'.','')*$usdrate; ;
										 
									}
								}
							}
						}
					}
				}
			}
		
 //echo "<pre>";
	 //print_r($count_row);  
	$width=3100;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="30" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="30" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="30" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Job No</th>
                    <th width="100">Trims Group</th>
                    <th width="100">Section</th>
                    <th width="100">Sub-Section</th>
                    <th width="100">Customer WO No</th>
                    <th width="100">Buyer's Style</th>
                    <th width="100">Trims Ref</th>
                    <th width="100">Customer Buyer</th>
                    <th width="100">Party Name</th>
                    <th width="100">Order Rcv date</th>
                    <th width="100">Tgt.Trims Delv.date</th>
                    <th width="100">Actual Delivery Date</th>
                    <th width="100">Delv Chlln No</th>
                    <th width="150">Item Description</th>
                    <th width="100">Item Color</th>
                    <th width="100">WO UOM</th>
                    <th width="100">Job Order Qty</th>
                    <th width="100">U/Price ($)</th>
                    <th width="100">Order Value ($)</th>
                    <th width="100">Current Delv Qty</th>
                    <th width="100">Curr. Delv Value ($)</th>
                    <th width="100">Total Delv Qty</th>
                    <th width="100">Total Delv Value ($)</th>
                    <th width="100">Delv.  Bal.Qty</th>
                    <th width="100">Delv. Bal. Value ($)</th>
                    <th width="100">Remarks</th>
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_ids" width="<? echo $width;?>" rules="all" align="left">
            <? 
				$i=1;
				$total_order_amm=0;$total_del_qty=0;$total_del_value=0;$total_production_qty=0;
				$row_check=array();
				$total_bill_balance=0; 
				
				//$trims_delv_date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("item_description")]][$row[csf("color_id")]][$row[csf("trims_del")]]["trims_del"]=$row[csf("trims_del")]; 
								
				foreach($trims_delv_date_array as $subcon_job=>$subcon_job_data)
				{
					foreach($subcon_job_data as $item_group=>$item_group_data)
   				    {  

						foreach($item_group_data as $section_id=>$section_data)
						{
							foreach($section_data as $sub_section=>$sub_section_data)
							{

								foreach($sub_section_data as $item_description=>$item_description_data)
								{
									foreach($item_description_data as $color_id=>$color_id_data)
									{
										$row_check=0;
										foreach($color_id_data as $trims_del=>$row)
										{			
							 
			
							
							$order_qty=$booked_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["order_quantity"];  
							 
							$order_ammount=$booked_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["order_amount"]; 
							
							$currency_id=$row["currency_id"];
							$rate=number_format($order_ammount,4,'.','')/number_format($order_qty,4,'.','');
							
							//echo $rate;  
							//$delevery_quantity=$date_array[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("trims_del")]][$row[csf("item_description")]]["delevery_qty"];
							
							$total_delevery_quantity=$date_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["delevery_qty"]; 
							$total_delevery_value=$date_array[$subcon_job][$item_group][$section_id][$sub_section][$item_description][$color_id]["delevery_value"];
							
							 
							
							
							$delevery_quantity=number_format($row["delevery_qty"],4,'.',''); 
							
							if($currency_id==1)
							{
							$takarate=$rate;
							$usdrate=$rate/$currency_rate;
							$orderamountusd=number_format($order_ammount,4,'.','')/number_format($currency_rate,4,'.',''); ;
							}
							elseif($currency_id==2)
							{
							$takarate=$rate*$currency_rate;
							$usdrate=$rate;
							$orderamountusd=$order_ammount;
							}			
							//$row_count = count($location_data)+1;
							//$rowspan = 'rowspan="'.$row_count.'"';
							//$con = '';
							///echo $count_row[$row[csf("subcon_job")]][$row[csf("item_group")]][$row[csf("section_id")]][$row[csf("sub_section")]][$row[csf("color_id")]][$row[csf("item_description")]];
							
							?>
							
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">		
							<td width="35" style="word-break: break-all;" align="left"><? echo $i ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $row['subcon_job'] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $item_name_arr[$row["item_group"]] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$row["section_id"]];?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $trims_sub_section[$row["sub_section"]];?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $row['cust_order_no'] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $row['buyer_style_ref'];?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $row['trims_ref'] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $buyerArr[$row['buyer_buyer']] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><?php
								if($row['within_group']==1) {
									echo $companyArr[$row['party_id']];
								} else {
									echo $buyerArr[$row['party_id']];
								}
								?>
							</td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $row['receive_date'] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $row['delivery_target_date'] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo change_date_format($row['del_date']) ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $row['trims_del'] ;?></td>
							<td width="150" style="word-break: break-all;" align="left"><? echo $row['item_description'] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $colorNameArr[$row['color_id']] ;?></td>
							<td width="100" style="word-break: break-all;" align="left"><? echo $unit_of_measurement[$row['order_uom']] ;?></td>			
							<?
							if($row_check==0)
							{			 			
							?>
							<td width="100"  style="word-break: break-all;" align="right" rowspan="<? echo $count_row[$row["subcon_job"]][$row["item_group"]][$row["section_id"]][$row["sub_section"]][$row["item_description"]][$row["color_id"]]; ?>"><? echo number_format($order_qty,4,'.','')?></td>  
							<td width="100"  style="word-break: break-all;" align="right" rowspan="<? echo $count_row[$row["subcon_job"]][$row["item_group"]][$row["section_id"]][$row["sub_section"]][$row["item_description"]][$row["color_id"]]; ?>"><? echo number_format($usdrate,4);?></td> 
							<td width="100" style="word-break: break-all;" align="right"  rowspan="<? echo $count_row[$row["subcon_job"]][$row["item_group"]][$row["section_id"]][$row["sub_section"]][$row["item_description"]][$row["color_id"]];?>"><? echo number_format($order_ammount,4,'.','') ;?></td>
							
							<?
							//$row_check++;
							}
							?>					 			 
							<td width="100" style="word-break: break-all;" align="right"><? echo number_format($row['delevery_qty'],4) ;?></td>
							<td width="100" style="word-break: break-all;" align="right"><? echo number_format($row['delevery_qty']*$usdrate,4) ;?></td>
							<?
							if($row_check==0)
							{
										
							?>
							<td width="100" style="word-break: break-all;" align="right" rowspan="<? echo  $count_row[$row["subcon_job"]][$row["item_group"]][$row["section_id"]][$row["sub_section"]][$row["item_description"]][$row["color_id"]];?>"><? echo number_format($total_delevery_quantity,4,'.','') ;?></td>
							<td width="100" style="word-break: break-all;" align="right" rowspan="<? echo $count_row[$row["subcon_job"]][$row["item_group"]][$row["section_id"]][$row["sub_section"]][$row["item_description"]][$row["color_id"]];?>"><? echo number_format($total_delevery_value,4,'.','') ; 
							$section_subsection_array[$section_id][$sub_section]+=number_format($total_delevery_value,4,'.','') ;
							
							?></td>
							<td width="100" style="word-break: break-all;" align="right"  rowspan="<? echo $count_row[$row["subcon_job"]][$row["item_group"]][$row["section_id"]][$row["sub_section"]][$row["item_description"]][$row["color_id"]];?>"><? echo number_format($order_qty-$total_delevery_quantity,4,'.','') ;?></td>
							<td width="100" style="word-break: break-all;" align="right"  rowspan="<? echo $count_row[$row["subcon_job"]][$row["item_group"]][$row["section_id"]][$row["sub_section"]][$row["item_description"]][$row["color_id"]];?>"><? echo  number_format((($order_qty-$total_delevery_quantity)*$usdrate),4,'.','');?></td> 
							<?
							$total_order_amm+=number_format($total_delevery_value,4,'.','');
							}
							
							?> 
							<td width="100" style="word-break: break-all;" align="left"><? echo $row['remarks'] ;?></td>
							</tr>	
							<?
							$i++;
							$row_check++;
							
							$total_del_qty+=number_format($row['delevery_qty'],4,'.','');
							$total_del_value+=number_format($row['delevery_qty'],4,'.','')*$usdrate;
						
						}
				
						}
					}
				}
			}
		}
	}
			//print_r($prod_sammary_array);
				?>             
       		 </table>
        </div>
        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
                	<th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100">Total:</th>
                    <th width="100"><? echo number_format($total_del_qty,4,'.','')?></th>
                    <th width="100"><? echo number_format($total_del_value,4,'.','');?></th>
                    <th width="100"></th>
                    <th width="100"><? echo number_format($total_order_amm,4,'.','')?></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
				</tfoot>
			</table>
       
    </div>

	<div align="center" style="height:auto; width:500px; margin:0 auto; padding:0;">
    	<table width="500" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead class="form_caption" >
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold" ><? echo "Summary"; ?></td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="500" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Section</th>
                    <th width="100">Sub-Section</th>
                    <th>Curr. Delv Value ($)</th>
				</thead>
			</table>
        
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="500" rules="all" align="left">
             <? 
			$sammary_sql= "SELECT a.id,a.trims_del,a.delivery_date,b.received_id,b.id as del_dtls_id, b.job_dtls_id,b.production_dtls_id,b.break_down_details_id,b.order_no,b.buyer_po_no,c.section as section_id,b.item_group,b.delevery_qty,b.remarks, b.color_id, b.buyer_buyer,c.sub_section,c.job_no_mst,c.booked_conv_fac as conv_factor ,d.qnty,d.order_id, d.rate, d.amount,d.booked_qty,d.description as item_description,d.id as breakdown_id , e.subcon_job, e.order_no as cust_order_no, e.trims_ref,e.party_id,e.receive_date,c.delivery_date as delivery_target_date,c.order_uom,e.currency_id from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d ,subcon_ord_mst e  where c.id=d.mst_id and d.id=b.break_down_details_id and  a.id=b.mst_id and c.id=b.receive_dtls_id and e.id=c.mst_id $trimsreceiveid_cond and b.delevery_qty>0  and a.entry_form=208 and e.entry_form=255 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con_receive_date  $company $buyer_po_id_cond  $where_con_section $where_con_sub_section  order by e.subcon_job,b.item_group,c.section,c.sub_section,d.description DESC"; 
			  
			$sammary_result = sql_select($sammary_sql);
	        $sammary_array=array(); $sammary_received_id=array(); $sammary_break_id=array();
	        foreach($sammary_result as $row)
	        {
	       	 	$sammary_array[$row[csf('section_id')]][$row[csf('sub_section')]]['section_id']=$row[csf('section_id')];
				$sammary_array[$row[csf('section_id')]][$row[csf('sub_section')]]['sub_section']=$row[csf('sub_section')];
  	 		}
 				$t=1;
				foreach($sammary_array as $section_key_id=>$section_data)
				{
					$section_total=0;
					foreach($section_data as $sub_section_key_id=>$row)
					{
 						?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
                            <td width="35"  align="center"><? echo $t;?></td>
                            <td width="100" align="center"><? echo $trims_section[$row["section_id"]];?></td>
                            <td width="100" align="center"><? echo $trims_sub_section[$row["sub_section"]];?></td>
                            <td align="right"><? 
							echo number_format($product_valu_usd=$section_subsection_array[$section_key_id][$sub_section_key_id],4);
							$section_total+=$product_valu_usd;?></td>
                           </tr>
						<? 
					$t++;
					}
					?>
                    <tr style="background-color:#CCC">
                   	 	<td colspan="3" align="right"><b><? echo $trims_section[$row["section_id"]];?> Total</b></td>
                    	<td align="right"><b><? echo number_format($section_total,4,'.','');?></b></td>
                    </tr>
                 	<?
					$grand_section_total+=$section_total;
				} 
				?>
				<tfoot>
					<tr style="background-color:#CCC">
                   	 	<td colspan="3" align="right"><b>Grand Total</b></td>
                    	<td align="right"><b><? echo number_format($grand_section_total,4,'.','');?></b></td>
                    </tr>
				</tfoot>
       		 </table>
    </div>
	<?

}
 
	
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
	
}

if($action=="print_button_variable_setting")
{

    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=17 and report_id=240 and is_deleted=0 and status_active=1");
	$printButton=explode(',',$print_report_format);

	 foreach($printButton as $id){
		if($id==147)$buttonHtml.='<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />';
		if($id==195)$buttonHtml.='<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:80px" class="formbutton" />';
		if($id==495)$buttonHtml.='<input type="button" name="search" id="search" value="Sales Summary" onClick="generate_report(3)" style="width:80px" class="formbutton" />';
		if($id==242)$buttonHtml.='<input type="button" name="search" id="search" value="Show 3" onClick="generate_report(4)" style="width:80px" class="formbutton" />';
	 }
	 echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";

    exit();
}

?>

	
									