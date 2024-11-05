<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//-----------------------------------------------------------------------------------------------------------
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





if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	////////////////////////////////////////////////
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	
	//d.company_id, d.location_id, d.within_group, d.party_id, d.delivery_prefix_num, d.delivery_date,
	if($cbo_company_id){$where_con.=" and d.company_id='$cbo_company_id'";} 
	
	if($cbo_customer_source){
		$where_con.=" and d.within_group='$cbo_customer_source'";
		$where_con_ord.=" and d.within_group='$cbo_customer_source'";
	} 
	if($cbo_customer_name){
		$where_con.=" and d.party_id='$cbo_customer_name'";
		$where_con_ord.=" and d.party_id='$cbo_customer_name'";
	} 
	if($cbo_location_name){$where_con.=" and d.location_id='$cbo_location_name'";} 
	
	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 ","id","supplier_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	 
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	$color_arrey=return_library_array( "SELECT id,color_name from lib_color  where status_active =1 and is_deleted=0",'id','color_name');
	
	//////////////////////////////////////////////////////////////////
	
		if($txt_date_from!="" and $txt_date_to!="")
		{	
			$where_con.=" and d.delivery_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
	/*if(trim($txt_order_no)!="")
	{
			$sql_cond="and a.trims_bill like '%$txt_order_no%'";
	}*/
	
	
 		
	//$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate)" , "conversion_rate" );
	
	
	//echo $currency_rate; die;

		/*$sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date,a.currency_id, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty , c.rate as colorSizeRate,d.id as delvID,d.company_id, d.delivery_prefix_num, d.delivery_date, e.id as delivery_id, e.delivery_qty, f.id as upid, f.rate, f.amount,f.domestic_amount, f.remarks,g.variable_status,f.delivery_qty as bil_qty,e.color_size_id
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f ,subcon_inbound_bill_mst g
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst  and b.job_no_mst=c.job_no_mst   and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and g.id=f.mst_id and f.process_id=13  and f.entry_form=395 and c.qnty>0 and f.mst_id='$update_id' and d.entry_form=254 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and b.id=c.mst_id and a.embellishment_job='$jobno' order by delivery_prefix_num ASC";*/


		/*$field_array_mst="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, location_id, within_group, party_id, party_location, deli_party, deli_party_location, delivery_date, remarks, job_no, challan_no , entry_form, inserted_by, insert_date, status_active, is_deleted";

		$field_array_dtls="id, mst_id, buyer_po_id, order_id, remarks, color_size_id, delivery_qty, sort_qty,reject_qty,cutting_number,delivery_status,defect_qty,fabric_reject_qty, print_reject_qty,inserted_by, insert_date, status_active, is_deleted";*/


		

		$delivery_sql="select b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.main_process_id, c.color_id, d.id as delvID, d.company_id, d.location_id, d.within_group, d.party_id, d.delivery_prefix_num, e.delivery_qty
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
		where a.entry_form in(204,311) and a.embellishment_job=b.job_no_mst  and b.job_no_mst=c.job_no_mst   and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and c.qnty>0 and d.entry_form in(254,325) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.id=c.mst_id $where_con order by d.delivery_prefix_num ASC";


		//echo $delivery_sql; die;
	  
		$result = sql_select($delivery_sql);
        $date_array=array();
        $deli_ids=array();
        $po_ids=array();
		
        foreach($result as $row)
        {
			
			$deli_ids[$row[csf("deli_id")]]=$row[csf("deli_id")];
			$po_ids[$row[csf("po_id")]]=$row[csf("po_id")];
			
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_prefix_num']=$row[csf('delivery_prefix_num')];
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delvID']=$row[csf('delvID')];

       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['po_id']=$row[csf('po_id')];
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['main_process_id']=$row[csf('main_process_id')];
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['color_id']=$row[csf('color_id')];

       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['location_id']=$row[csf('location_id')];
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['within_group']=$row[csf('within_group')];
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['party_id']=$row[csf('party_id')];
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['buyer_po_id']=$row[csf('buyer_po_id')];
       	 	$date_array[$row[csf('delvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
			
 		}


 		$deli_id=implode(',', $deli_ids);
 		$po_id=implode(',', $po_ids);
 
 

	   $po_sql="select a.within_group, a.party_id, b.id, b.order_no,b.order_quantity,b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
	   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where b.id in($po_id) and a.embellishment_job=b.job_no_mst and a.entry_form in(204,311)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	   //echo $po_sql; die;
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
			$buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		}
		unset($po_sql_res);
 
	
 
 
	
	$width=765;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="35" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="15" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="15" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="30">&nbsp;</th>
                    <th width="35">SL</th>
                    <th width="100">Delivery ID</th>
                    <th width="100">Buyer PO</th>
                    <th width="100">Style Ref.</th>
                    <th width="100">Gmts Item</th>
                    <th width="100">Embel. Name</th>
                    <th width="100">Color</th>
                    <th width="">Delivery Qty</th>
                    
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
				$i=1;
				$delivery_qty=0;
				foreach($date_array as $deli_id=>$deli_id_arr)
				{
					foreach($deli_id_arr as $po_id=>$po_id_arr)
					{
						foreach($po_id_arr as $gmts_item_id=>$gmts_item_id_arr)
						{
							foreach($gmts_item_id_arr as $main_process_id=>$main_process_id_data)
							{
								foreach($main_process_id_data as $color_id=>$row)
								{
								
									//$trims_del=implode(",",array_unique(explode(",",chop($row[trims_del],','))));
									//$trims_del=implode(",",array_unique(explode(",",chop($row[trims_del]),',')));
									//echo $row[received_id];	
									?>
				                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				                		<td width="30" align="center" valign="middle">
											<input type="checkbox" id="chkPrint_<? echo $i; ?>" name="chkPrint" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" />
											<input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid" value="<? echo $row['delvID']."__".$po_id."__".$gmts_item_id."__".$main_process_id."__".$color_id; ?>"/>
										</td>

				                		<td width="35"  align="center"><? echo $i;?></td>
				                    	<td width="100" align="center"><? echo $row['delivery_prefix_num'];?></td>
				                    	<td width="100" align="center"><? echo $buyer_po_arr[$po_id]['po'];?></td>
				                    	<td width="100" align="center"><? echo $buyer_po_arr[$po_id]['style'];?></td>
				                    	<td width="100" align="center"><? echo $garments_item[$gmts_item_id];?></td>
				                    	<td width="100" align="center"><? echo $emblishment_name_array[$main_process_id];?></td>
				                    	<td width="100" align="center"><? echo $color_arrey[$color_id];?></td>
				                    	<td width="" align="right"><? echo number_format($row['delivery_qty'],2); ?></td>
				                    
				                	</tr>
				                	<? 	
									$i++;
									//$delivery_qty+=$row['delivery_qty'];
								
								}
							}
						}
					}
				}
				?> 
       		 </table>
        </div>


        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width+20; ?>" class="rpt_table" id="footer_line">
				<tfoot>
					<tr>
						<!-- <td width="30">&nbsp;</td>
						<td width="35">&nbsp;</td> -->
						<!-- <td width="100" align="center" ><input type="checkbox" id="all_check" onclick="check_all()" /></td> -->
						<td colspan="10" align="center">
							<input type="button" name="search" id="search" value="Print" onClick="print_formet()" style="width:80px" class="formbutton" /></td>
						<!-- <input type="button" value="<? //if($cbo_audit_type==1) echo "Un-Audited"; else echo "Audited"; ?>" class="formbutton" style="width:100px" onclick="fn_audited_un_audited()"/> -->
						
					</tr>
				</tfoot>
			</table>
    </div>
	<?
	
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



if($action=="report_voucher_print")
{
	extract($_REQUEST);
	//$row['delvID']."__".$po_id."__".$gmts_item_id."__".$main_process_id."__".$color_id;
	//echo $data; die;

	$data = explode(',', $data);

	$deli_mst_id2=array();
	$order_dtls_id2=array();
	$gmts_item_id2=array();
	$main_process_id2=array();
	$color_id2=array();
	foreach($data as $s){
		$asdf = explode('__', $s);
		$deli_mst_id2[$asdf[0]]=$asdf[0];
		$order_dtls_id2[$asdf[1]]=$asdf[1];
		$gmts_item_id2[$asdf[2]]=$asdf[2];
		$main_process_id2[$asdf[3]]=$asdf[3];
		$color_id2[$asdf[4]]=$asdf[4];

	}


	
	$deli_mst_id = implode(",",$deli_mst_id2);
	$order_dtls_id = implode(",",$order_dtls_id2);
	$gmts_item_id = implode(",",$gmts_item_id2);
	$main_process_id = implode(",",$main_process_id2);
	$color_id = implode(",",$color_id2);
	//echo "<pre>";
	//print_r($bill_mst_id); die;
	

	//echo $color_id; die; 
	$company_library = return_library_array("SELECT id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$buyer_library = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$imge_arr=return_library_array( "SELECT master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("SELECT id, location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$delivery_id_arr = return_library_array("SELECT id, delivery_prefix_num from subcon_delivery_mst where status_active=1 and is_deleted=0", 'id', 'delivery_prefix_num');
	
	$buyer_po_arr=array();
	$po_sql ="SELECT a.buyer_name, a.style_ref_no, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
	}
	unset($po_sql_res);


	$emb_po_sql="select a.within_group, a.party_id, b.id, b.order_no,b.order_quantity,b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
	   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where b.id in($order_dtls_id) and a.embellishment_job=b.job_no_mst and a.entry_form in(204,311)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
   //echo $emb_po_sql; die;
	$emb_po_sql_res=sql_select($emb_po_sql);
	$emb_buyer_po_arr=array();
	foreach ($emb_po_sql_res as $row)
	{
		$emb_buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$emb_buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$emb_buyer_po_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
	}
	unset($emb_po_sql_res);
	
	
	$sql_mst = "SELECT id, delivery_no, company_id, location_id, within_group, party_id, delivery_date, job_no, challan_no, remarks from subcon_delivery_mst where entry_form in(254,325) and id in($deli_mst_id) and status_active=1 and is_deleted=0";
	//echo $sql_mst; die;
	$dataArray = sql_select($sql_mst); 

	$company=$dataArray[0][csf('company_id')];
	$location=$dataArray[0][csf('location_id')];

	$party_name=""; $party_address=""; $party_address="";
	if( $dataArray[0][csf('within_group')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
		
		//if($party_address!="") $party_address=$party_name.', '.$party_address;
	}
	else if($dataArray[0][csf('within_group')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=".$dataArray[0][csf('party_id')].""); 
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
		//if($address!="") $party_address=$party_name.', '.$party_address;
	}

	$delivery_id=$dataArray[0][csf('delivery_no')];

	$gate_pass_id="select sys_number from inv_gate_pass_mst where challan_no='$delivery_id'"; //basis=49 and
	//echo $gate_pass_id; die;
	$gate_pass_sql=sql_select($gate_pass_id);

	foreach ($gate_pass_sql as $row_data)
		{ 
			if($row_data[csf("sys_number")]!="") $sys_delivery_no.=$row_data[csf("sys_number")].',';
		}




	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
    <div style="width:1230px; font-size:6px">
        <!--<table width="100%" cellpadding="0" cellspacing="0" >-->
        <table align="center" cellspacing="0" width="1020"   class="rpt_table">
            <tr>
                <td width="300" align="left"> 
                    <img  src='../../<? echo $com_dtls[2]; ?>' height='50%' width='50%' />
                </td>
                <td>
                    <table width="900" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $com_dtls[0]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <? echo $com_dtls[1]; ?> 
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong>Embellishment Delivery</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
       <table align="center" cellspacing="0" width="1020"   class="rpt_table" style="font-size:12px">
            <tr>
            	<td width="130" align="left"><strong>Party: </strong></td>
                <td width="175"><? echo $party_name; ?></td>
                <td width="130" align="left"><strong>Party Location:</strong></td>
                <td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                <!-- <td align="left"><strong>Delivery Date: </strong></td> -->
                <!-- <td><? //echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td> -->
            </tr>
            <tr>
            	<td align="left"><strong>Party Address: </strong></td>
                <td colspan="3"><? echo $party_address; ?></td>
                <!-- <td align="left"><strong>Remarks: </strong></td>
                <td colspan=""><? //echo $dataArray[0][csf('remarks')]; ?></td> -->
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="center" cellspacing="0" width="1230" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="50">Delivery Id</th>
                    <th width="80">Delivery Date</th>
                    <th width="90">Buyer PO</th>
                    <th width="90">Style Ref.</th>
                    <th width="90">Gmts Item</th>
                    <th width="80">Body Part</th>
                    <th width="80">Embel. Name</th>
                    <th width="90">Process/Type</th>
                    <th width="80">Color</th>
                    <th width="80">Size</th>
                    <th width="60">Current Delv (Pcs)</th>
                    <th width="60">Sort Qty.</th>
                    <th width="60">Reject Qty.</th>
                     <th width="60">Cutting Number</th>
                    <th>Remarks</th>
                </thead>
				<?
				
				$mst_id = $data;
				$com_id = $dataArray[0][csf('company_id')];
				$job_no = $dataArray[0][csf('job_no')];


				$sql="select a.id, a.embellishment_job, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, c.qnty as qty, d.delivery_prefix_num, d.delivery_date, e.delivery_qty, e.sort_qty, e.reject_qty, e.remarks,e.cutting_number,e.mst_id
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
				where  a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and d.id in($deli_mst_id) and b.id in($order_dtls_id) and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by d.delivery_prefix_num ASC";
				//a.entry_form in(204,311) and and c.qnty>0 and d.entry_form in(254,325)
				

				 //$sql= "SELECT  a.id, a.embellishment_job, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, c.qnty as qty, d.delivery_qty, d.sort_qty, d.reject_qty, d.remarks,d.cutting_number,d.mst_id  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_dtls d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and c.id=d.color_size_id and a.entry_form in(204,311) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id='$com_id' and d.mst_id in($deli_mst_id) order by c.id ASC"; //and a.embellishment_job='$job_no'
				//echo $sql; die;
				$sql_res=sql_select($sql);

				//$date_array[$row[csf('delvID')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_prefix_num']=$row[csf('delivery_prefix_num')];

				foreach($sql_res as $row)
		        {
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['mst_id']=$row[csf('mst_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_prefix_num']=$row[csf('delivery_prefix_num')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_date']=$row[csf('delivery_date')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['embellishment_job']=$row[csf('embellishment_job')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['order_id']=$row[csf('order_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['order_no']=$row[csf('order_no')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['buyer_po_id']=$row[csf('buyer_po_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['main_process_id']=$row[csf('main_process_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['embl_type']=$row[csf('embl_type')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['body_part']=$row[csf('body_part')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['color_size_id']=$row[csf('color_size_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['color_id']=$row[csf('color_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['size_id']=$row[csf('size_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['qty']+=$row[csf('qty')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['sort_qty']+=$row[csf('sort_qty')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['reject_qty']+=$row[csf('reject_qty')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['remarks']=$row[csf('remarks')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['cutting_number']=$row[csf('cutting_number')];
		 		}

		 		/*echo "<pre>";
		 		print_r($date_array);
		 		echo "</pre>";die;*/


		 		/*$dtls_sql= "SELECT d.delivery_qty, d.sort_qty, d.reject_qty, d.color_size_id, d.remarks,d.cutting_number,d.mst_id  from subcon_delivery_dtls d where d.mst_id in($deli_mst_id) and d.status_active=1 and d.is_deleted=0 order by d.id ASC"; 
				//echo $dtls_sql; //die;
				$dtls_sql_res=sql_select($dtls_sql);
				$dtls_data_array=array();
				foreach($dtls_sql_res as $row)
		        {
		        	$dtls_data_array[$row[csf('mst_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
		        	$dtls_data_array[$row[csf('mst_id')]]['sort_qty']+=$row[csf('sort_qty')];
		        	$dtls_data_array[$row[csf('mst_id')]]['reject_qty']+=$row[csf('reject_qty')];
		        }*/

				
 				$i=1; $grand_tot_qty=0; $k=1;

				
				foreach($date_array as $deli_id=>$deli_id_arr)
				{
					foreach($deli_id_arr as $order_id=>$order_id_arr)
					{
						foreach($order_id_arr as $gmts_item_id=>$gmts_item_id_arr)
						{
							foreach($gmts_item_id_arr as $main_process_id=>$main_process_id_data)
							{
								foreach($main_process_id_data as $color_id=>$row)
								{

									$embl_name=$row['main_process_id'];
									if($embl_name==1) $new_subprocess_array= $emblishment_print_type;
									else if($embl_name==2) $new_subprocess_array= $emblishment_embroy_type;
									else if($embl_name==3) $new_subprocess_array= $emblishment_wash_type;
									else if($embl_name==4) $new_subprocess_array= $emblishment_spwork_type;
									else if($embl_name==5) $new_subprocess_array= $emblishment_gmts_type;
									else $new_subprocess_array=$blank_array;
									if (!in_array($row["order_no"],$order_array) )
									{
										if($k!=1)
										{
										?>
											<tr class="tbl_bottom">
												<td colspan="11" align="right"><b>Order Total:</b></td>
												<td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
				                                <td align="right"><b><? echo number_format($sub_total_sort_qty,2); ?></b></td>
				                                <td align="right"><b><? echo number_format($sub_total_reject_qty,2); ?></b></td>
				                                <td>&nbsp;</td>
				                                <td>&nbsp;</td>
											</tr>
										<?
											unset($sub_total_qty);
											unset($sub_total_sort_qty);
											unset($sub_total_reject_qty);
										}
										?>
											<tr bgcolor="#dddddd">
												<td colspan="15" align="left" style="font-size:12px"><b>Embl. Job No: <? echo $row["embellishment_job"]; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Work Order No: <? echo $row["order_no"]; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Buyer Style: <? echo $buyer_po_arr[$row["buyer_po_id"]]['style']; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Buyer Name: <? echo $buyer_library[$buyer_po_arr[$row["buyer_po_id"]]['buyer']]; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Internal Ref. No.: <? echo $buyer_po_arr[$row["buyer_po_id"]]['grouping']; ?></b>
				                                </td>
											</tr>
										<?
										$order_array[]=$row['order_no'];  
										$k++;
									}
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
				                    <tr bgcolor="<? echo $bgcolor; ?>">
				                        <td><? echo $i; ?></td>
				                        <td style="word-break:break-all"><? echo $row['delivery_prefix_num']; ?></td>
				                        <td style="word-break:break-all"><? echo $row['delivery_date']; ?></td>
				                        <td style="word-break:break-all"><? echo $emb_buyer_po_arr[$order_id]['po']; ?></td>
				                        <td style="word-break:break-all"><? echo $emb_buyer_po_arr[$order_id]['style']; ?></td>
				                        <td style="word-break:break-all"><? echo $garments_item[$row['gmts_item_id']]; ?></td>
				                        <td style="word-break:break-all"><? echo $body_part[$row['body_part']]; ?>&nbsp;</td>
				                        <td style="word-break:break-all"><? echo $emblishment_name_array[$embl_name]; ?>&nbsp;</td>
				                        <td style="word-break:break-all"><? echo $new_subprocess_array[$row['embl_type']]; ?>&nbsp;</td>
				                        <td style="word-break:break-all"><? echo $color_arr[$row['color_id']]; ?>&nbsp;</td>
				                        <td style="word-break:break-all" align="center"><? echo $size_arr[$row['size_id']]; ?>&nbsp;</td>
				                        <td align="right"><? $delivery_qty=$dtls_data_array[$row['mst_id']]['delivery_qty']; echo number_format($row['delivery_qty'], 2, '.', ''); ?>&nbsp;</td>
				                        <td align="right"><? $sort_qty=$dtls_data_array[$row['mst_id']]['sort_qty']; echo number_format($row['sort_qty'], 2, '.', ''); ?>&nbsp;</td>
				                        <td align="right"><? $reject_qty=$dtls_data_array[$row['mst_id']]['reject_qty']; echo number_format($row['reject_qty'], 2, '.', ''); ?>&nbsp;</td>
				                        <td align="right"><? echo $row['cutting_number']; ?>&nbsp;</td>
				                        <td style="word-break:break-all"><? echo $row['remarks']; ?>&nbsp;</td>
				                    </tr>
									<?
									$i++;
									$sub_total_qty+=$row['delivery_qty'];
									$sub_total_sort_qty+=$row['sort_qty'];
									$sub_total_reject_qty+=$row['reject_qty'];
									
									$grand_tot_qty+=$row['delivery_qty'];
									$grand_tot_sort_qty+=$row['sort_qty'];
									$grand_tot_reject_qty+=$row['reject_qty'];
								}	
							}		
						}			
					}				
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="11" align="right"><b>Order Total:</b></td>
                    <td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($sub_total_sort_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($sub_total_reject_qty,2); ?></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="tbl_bottom">
                    <td align="right" colspan="11"><strong>Grand Total</strong></td>
                    <td align="right"><b><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</b></td>
                    <td align="right"><b><? echo number_format($grand_tot_sort_qty, 2, '.', ''); ?>&nbsp;</b></td>
                    <td align="right"><b><? echo number_format($grand_tot_reject_qty, 2, '.', ''); ?>&nbsp;</b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <br>
            	<? echo signature_table(154, $com_id, "1020px"); ?>
        </div>
    </div>
	<?
	exit();
}




?>