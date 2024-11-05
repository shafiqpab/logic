<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------


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
	if($cbo_company_id){$where_con.=" and g.company_id='$cbo_company_id'";} 
	
	if($cbo_customer_source){
		$where_con.=" and g.party_source='$cbo_customer_source'";
		$where_con_ord.=" and g.party_source='$cbo_customer_source'";
	} 
	if($cbo_customer_name){
		$where_con.=" and g.party_id='$cbo_customer_name'";
		$where_con_ord.=" and g.party_id='$cbo_customer_name'";
	} 
	if($cbo_location_name){$where_con.=" and g.location_id='$cbo_location_name'";} 
	
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
			$where_con.=" and g.bill_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		//main query

		$bill_sql="select b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.main_process_id, c.color_id, g.id as billvID, g.company_id, g.location_id, g.party_source, g.party_id, g.prefix_no_num, f.delivery_qty
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f ,subcon_inbound_bill_mst g
		where a.entry_form in(204,311) and a.embellishment_job=b.job_no_mst  and b.job_no_mst=c.job_no_mst   and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and g.id=f.mst_id and f.process_id in(13,14) and g.entry_form in(395,332) and c.qnty>0  and d.entry_form in(254,325) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and b.id=c.mst_id $where_con order by g.prefix_no_num ASC";
				//and a.embellishment_job='$jobno' //and f.mst_id='$update_id'


		//echo $bill_sql; die;
	  
		$result = sql_select($bill_sql);
        $date_array=array();
        $bill_ids=array();
        $po_ids=array();
		
        foreach($result as $row)
        {
			
			$bill_ids[$row[csf("billvID")]]=$row[csf("billvID")];
			$po_ids[$row[csf("po_id")]]=$row[csf("po_id")];
			
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['prefix_no_num']=$row[csf('prefix_no_num')];
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['billvID']=$row[csf('billvID')];

       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['po_id']=$row[csf('po_id')];
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['main_process_id']=$row[csf('main_process_id')];
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['color_id']=$row[csf('color_id')];

       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['location_id']=$row[csf('location_id')];
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['party_source']=$row[csf('party_source')];
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['party_id']=$row[csf('party_id')];
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['buyer_po_id']=$row[csf('buyer_po_id')];
       	 	$date_array[$row[csf('billvID')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
			
 		}

 		/*echo "<pre>";
 		print_r($date_array);
 		echo "</pre>"; die;*/

 		$bill_id=implode(',', $bill_ids);
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
                    <th width="100">Bill ID</th>
                    <th width="100">Buyer PO</th>
                    <th width="100">Style Ref.</th>
                    <th width="100">Gmts Item</th>
                    <th width="100">Embel. Name</th>
                    <th width="100">Color</th>
                    <th width="">Bill Qty</th>
                    
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
				$i=1;
				$delivery_qty=0;
				//$date_array[$row[csf('prefix_no_num')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['prefix_no_num']=$row[csf('prefix_no_num')];
				foreach($date_array as $bill_id=>$bill_id_arr)
				{
					foreach($bill_id_arr as $po_id=>$po_id_arr)
					{
						foreach($po_id_arr as $gmts_item_id=>$gmts_item_id_arr)
						{
							foreach($gmts_item_id_arr as $main_process_id=>$main_process_id_data)
							{
								foreach($main_process_id_data as $color_id=>$row)
								{
								
									//$trims_del=implode(",",array_unique(explode(",",chop($row[trims_del],','))));
									
									?>
				                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				                		<td width="30" align="center" valign="middle">
											<input type="checkbox" id="chkPrint_<? echo $i; ?>" name="chkPrint" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" />
											<input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid" value="<? echo $row['billvID']."__".$po_id."__".$gmts_item_id."__".$main_process_id."__".$color_id; ?>"/>
										</td>

				                		<td width="35"  align="center"><? echo $i;?></td>
				                    	<td width="100" align="center"><? echo $row['prefix_no_num'];?></td>
				                    	<td width="100" align="center"><? echo $buyer_po_arr[$po_id]['po'];?></td>
				                    	<td width="100" align="center"><? echo $buyer_po_arr[$po_id]['style'];?></td>
				                    	<td width="100" align="center"><? echo $garments_item[$gmts_item_id];?></td>
				                    	<td width="100" align="center"><? echo $emblishment_name_array[$main_process_id];?></td>
				                    	<td width="100" align="center"><? echo $color_arrey[$color_id];?></td>
				                    	<td width="" align="right"><? echo number_format($row['delivery_qty'],2); ?></td>
				                    
				                	</tr>
				                	<? 	
									$i++;

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
						
						<td colspan="10" align="center">
							<input type="button" name="search" id="search" value="Print" onClick="print_formet()" style="width:80px" class="formbutton" /></td>
						
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




if($action=="embl_bill_issue_print")
{
	extract($_REQUEST);
	//$row['billvID']."__".$po_id."__".$gmts_item_id."__".$main_process_id."__".$color_id;
	//echo $data; die;

	$data = explode(',', $data);

	$bill_mst_id2=array();
	$order_dtls_id2=array();
	$gmts_item_id2=array();
	$main_process_id2=array();
	$color_id2=array();
	foreach($data as $s){
		$asdf = explode('__', $s);
		$bill_mst_id2[$asdf[0]]=$asdf[0];
		$order_dtls_id2[$asdf[1]]=$asdf[1];
		$gmts_item_id2[$asdf[2]]=$asdf[2];
		$main_process_id2[$asdf[3]]=$asdf[3];
		$color_id2[$asdf[4]]=$asdf[4];

	}


	
	$bill_mst_id = implode(",",$bill_mst_id2);
	$order_dtls_id = implode(",",$order_dtls_id2);
	$gmts_item_id = implode(",",$gmts_item_id2);
	$main_process_id = implode(",",$main_process_id2);
	$color_id = implode(",",$color_id2);
	//echo "<pre>";
	//print_r($bill_mst_id); die;
	

	//echo $color_id; die; 
	
	$company_library = return_library_array("SELECT id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$buyer_library = return_library_array("SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$size_arr=return_library_array( "SELECT id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$imge_arr=return_library_array( "SELECT master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	//$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	$location_arr = return_library_array("SELECT id, location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$bill_id_arr = return_library_array("SELECT id, prefix_no_num from subcon_inbound_bill_mst where status_active=1 and is_deleted=0", 'id', 'prefix_no_num');
	$bill_date_arr = return_library_array("SELECT id, bill_date from subcon_inbound_bill_mst where status_active=1 and is_deleted=0", 'id', 'bill_date');

	$sql_mst="SELECT id, prefix_no_num, bill_no, company_id, location_id, bill_date, party_id, party_source, process_id, party_location_id, remarks from subcon_inbound_bill_mst where process_id in(13,14)  and entry_form in(395,332) and status_active=1 and is_deleted=0 and id in($bill_mst_id)";
	//echo $sql_mst; die;
	$dataArray = sql_select($sql_mst); 

	$company=$dataArray[0][csf('company_id')];
	$location=$dataArray[0][csf('location_id')];

	
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no,a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$buyer_library[$row[csf("buyer_name")]];
		$buyer_po_arr[$row[csf("id")]]['file']=$row[csf("grouping")];
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

	
	$party_name=""; 
	$party_address=""; 
	$party_address="";
	if( $dataArray[0][csf('party_source')]==1)
	{
		$party_name=$company_library[$dataArray[0][csf('party_id')]];
		
		$party_address=show_company($dataArray[0][csf('party_id')],'','');
		
		//if($party_address!="") $party_address=$party_name.', '.$party_address;
	}
	else if($dataArray[0][csf('party_source')]==2) 
	{
		$party_name=$buyer_library[$dataArray[0][csf('party_id')]];
		$party_id=$dataArray[0][csf('party_id')];
		$nameArray=sql_select( "SELECT address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_id"); 
		foreach ($nameArray as $result)
		{ 
			if($result!="") $party_address=$result['address_1'];
		}
		//if($address!="") $party_address=$party_name.', '.$party_address;
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
    <div style="width:1020px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right"> 
                    <img  src='../../<? echo $com_dtls[2]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $com_dtls[0]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <? echo $com_dtls[1];?> 
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo "Embellishment Bill Entry"; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
            	<td width="130"><strong>Party: </strong></td>
                <td width="200"><? echo $party_name; ?></td>
                <td width="130"><strong>Party Location:</strong></td>
                <td><? echo $location_arr[$dataArray[0][csf('party_location_id')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Party Address: </strong></td>
                <td colspan="3"><? echo $party_address; ?></td>
            </tr>
            
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="1020" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="right">
                    <th width="30">SL</th>
                    <th width="50">Bill Id</th>
                    <th width="60">Bill date</th>
                    <th width="90">Buyer PO</th>
                    <th width="90">Buyer Style</th>
                    <th width="90">Gmts Item</th>
                    <th width="80">Body Part</th>
                    <th width="80">Embel. Name</th>
                    <th width="70">Process/Type</th>
                    <th width="80">Color</th>
                    <th width="70">Size</th>
                    <th width="60">Bill Qty</th>
                    <th width="50">Rate</th>
                    <th width="60">Amount</th>
                    <th>Remarks</th>
                </thead>
				<?
				
				$com_id = $dataArray[0][csf('company_id')];
				$job_no = $dataArray[0][csf('job_no')];

				//report query
				$sql="select a.id, a.embellishment_job,a.currency_id, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, c.qnty as qty, g.party_source, g.bill_date, g.prefix_no_num, f.delivery_qty, f.rate, f.amount, f.remarks, f.delivery_id, f.delivery_date, f.mst_id 
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f ,subcon_inbound_bill_mst g
				where  a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and d.id=e.mst_id and c.id=e.color_size_id and e.id=f.delivery_id and g.id=f.mst_id and b.id=c.mst_id and g.id in($bill_mst_id) and b.id in($order_dtls_id) and b.gmts_item_id in($gmts_item_id) and c.color_id in($color_id) and b.main_process_id in($main_process_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  order by g.prefix_no_num ASC";
				// a.entry_form in(204,311) and and g.company_id=$com_id and f.process_id in(13,14) and g.entry_form in(395,332) and c.qnty>0  and d.entry_form in(254,325)

				//echo $sql; die;
				$sql_res=sql_select($sql);
				foreach($sql_res as $row)
		        {
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['mst_id']=$row[csf('mst_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['embellishment_job']=$row[csf('embellishment_job')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['bill_date']=$row[csf('bill_date')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['prefix_no_num']=$row[csf('prefix_no_num')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['party_source']=$row[csf('party_source')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['currency_id']=$row[csf('currency_id')];
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
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['remarks']=$row[csf('remarks')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['rate']=$row[csf('rate')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['amount']+=$row[csf('amount')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_id']=$row[csf('delivery_id')];
		       	 	$date_array[$row[csf('mst_id')]][$row[csf('order_id')]][$row[csf('gmts_item_id')]][$row[csf('main_process_id')]][$row[csf('color_id')]]['delivery_date']=$row[csf('delivery_date')];
		 		}

		 		/*echo "<pre>";
		 		print_r($date_array);
		 		echo "</pre>";die;*/
 				$i=1; $grand_tot_qty=0; $k=1;  
				foreach($date_array as $mst_id=>$mst_id_arr)
				{
					foreach($mst_id_arr as $order_id=>$order_id_arr)
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
												<td>&nbsp;</td>
				                                <td align="right"><b><? echo number_format($sub_total_amt,2); ?></b></td>
				                                <td>&nbsp;</td>
											</tr>
										<?
										unset($sub_total_qty);
										unset($sub_total_amt);
										}
										?>
											<tr bgcolor="#dddddd">
												<td colspan="15" align="left" style="font-size:12px"><b>Embl. Job No: <? echo $row["embellishment_job"]; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>WO No: <? echo $row["order_no"]; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Int.Ref. No: <? echo $buyer_po_arr[$row["buyer_po_id"]]['file']; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Cust. Buyer: <? echo $buyer_po_arr[$row["buyer_po_id"]]['buyer']; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Order Currency: <? echo $currency[$row["currency_id"]]; ?>;</b>&nbsp;&nbsp;&nbsp;&nbsp;<b>Buyer Style: <? echo $buyer_po_arr[$row["buyer_po_id"]]['style']; ?>;</b></td>
				                                
											</tr>
										<?
										$order_array[]=$row['order_no'];  
										$k++;
									}

									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
				                    <tr bgcolor="<? echo $bgcolor; ?>">
				                    	<td ><? echo $i; ?></td>
				                        <td style="word-break:break-all"><? echo $row['prefix_no_num']; ?></td>
				                        <td style="word-break:break-all"><? echo $row['bill_date']; ?></td>
				                        <td style="word-break:break-all"><? echo $emb_buyer_po_arr[$order_id]['po']; ?></td>
				                        <td style="word-break:break-all"><? echo $emb_buyer_po_arr[$order_id]['style']; ?></td>
				                        <td style="word-break:break-all"><? echo $garments_item[$row['gmts_item_id']]; ?></td>
				                        <td style="word-break:break-all"><? echo $body_part[$row['body_part']]; ?>&nbsp;</td>
				                        <td style="word-break:break-all"><? echo $emblishment_name_array[$embl_name]; ?>&nbsp;</td>
				                        <td style="word-break:break-all"><? echo $new_subprocess_array[$row['embl_type']]; ?>&nbsp;</td>
				                        <td style="word-break:break-all"><? echo $color_arr[$row['color_id']]; ?>&nbsp;</td>
				                        <td style="word-break:break-all" align="center"><? echo $size_arr[$row['size_id']]; ?>&nbsp;</td>
				                        <td align="right"><? echo number_format($row['delivery_qty'], 2, '.', ''); ?>&nbsp;</td>
				                        <td align="right"><? echo number_format($row['rate'], 4, '.', ''); ?>&nbsp;</td>
				                        <td align="right"><? echo number_format($row['amount'], 2, '.', ''); ?>&nbsp;</td>
				                        <td style="word-break:break-all"><? echo $row['remarks']; ?>&nbsp;</td>
				                    </tr>
									<?
									$i++;
									$sub_total_qty+=$row['delivery_qty'];
									$grand_tot_qty+=$row['delivery_qty'];
									
									$sub_total_amt+=$row['amount'];
									$grand_tot_amt+=$row['amount'];
								}
							}
						}
					}				
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="11" align="right"><b>Order Total:</b></td>
                    <td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($sub_total_amt,2); ?></b></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr class="tbl_bottom">
                    <td align="right" colspan="11"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_tot_amt, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            
            <br>
			<? echo signature_table(171, $com_id, "1020px",$data[3]); ?>
        </div>
    </div>
	<?
	exit();
}






?>