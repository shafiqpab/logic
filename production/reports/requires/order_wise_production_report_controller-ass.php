<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );     	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  ); 
 	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

	$garments_nature=str_replace("'","",$cbo_garments_nature);
	if($garments_nature==1)$garments_nature="";
	$type = str_replace("'","",$cbo_type);
	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_name=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_name="";
		}
		else
		{
			$buyer_name="";
		}
	}
	else
	{
		$buyer_name=" and b.buyer_name=$cbo_buyer_name";
	}
	
	//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and b.buyer_name=$cbo_buyer_name";
	
	if(str_replace("'","",$cbo_location)==0) $location=""; else $location=" and c.location=$cbo_location";
	if(str_replace("'","",$cbo_floor)==0) $floor=""; else $floor=" and c.floor_id=$cbo_floor";
	
	if(str_replace("'","",trim($txt_order_no))!="") $search_string="%".str_replace("'","",trim($txt_order_no))."%"; else $search_string="%%";
	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")
	 {
		  $txt_date=""; $txt_country_date=""; $txt_country_location_date=""; 
	 }
	else 
	{
		$txt_date=" and a.pub_shipment_date between $txt_date_from and $txt_date_to";
		$txt_country_date=" and c.country_ship_date between $txt_date_from and $txt_date_to";
		$txt_country_location_date=" and d.country_ship_date between $txt_date_from and $txt_date_to";
	}
	
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		
	ob_start(); 
    if($type==1)
    {
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, MAX(ex_factory_date) AS ex_factory_date,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[107]);die;
		
		if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, 
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id";
		}
		
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['special']=$gmtsRow[csf('special')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['special']=$gmtsRow[csf('specialr')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];
		}
		
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");
		
		$buyer_array=array(); 
		$order_sql="select a.id, b.job_no_prefix_num, a.po_number, a.po_quantity, a.unit_price, a.po_total_price, a.job_no_mst, a.pub_shipment_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.po_number like '$search_string' and b.status_active=1 and b.is_deleted=0 $txt_date $company_name $buyer_name $garmentsNature order by a.pub_shipment_date, a.id";
		$result=sql_select($order_sql);
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   $i++;
		   if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   $setArr = explode("__",$orderRes[csf("set_break_down")] );
		   $countArr = count($setArr); 
		   if($countArr==0) $countArr=1; 
		   for($j=0;$j<$countArr;$j++)
		   {
			   $setItemArr = explode("_",$setArr[$j]);
			   $item_id=$setItemArr[0];
			   $set_qnty=$setItemArr[1];
			   if($item_id>0)
			   {
				   	$k++;
				   	$po_quantity_in_pcs = $orderRes[csf("po_quantity")]*$set_qnty;
					$unit_price=$orderRes[csf("unit_price")]/$set_qnty;
				   	$ex_factory_date=$ex_factory_arr[$orderRes[csf("id")]][$item_id]['date'];
					$ex_factory_qnty=$ex_factory_arr[$orderRes[csf("id")]][$item_id]['qty'];
					$color=""; $days_remian="";
					if($orderRes[csf("shiping_status")]==1 || $orderRes[csf("shiping_status")]==2)
					{
						$days_remian=datediff("d",$date,$orderRes[csf("shipment_date")]); 
						if($orderRes[csf("shipment_date")] > $date) 
						{
							$color="";
						}
						else if($orderRes[csf("shipment_date")] < $date) 
						{
							$color="red";
						}														
						else if($orderRes[csf("shipment_date")] >= $date && $days_remian<=5 ) 
						{
							$color="orange";
						}
					} 
					else if($orderRes[csf("shiping_status")]==3)
					{
						$days_remian=datediff("d",$ex_factory_date,$orderRes[csf("shipment_date")]);
						if($orderRes[csf("shipment_date")] >= $ex_factory_date) 
						{ 
							$color="green";
						}
						else if($orderRes[csf("shipment_date")] < $ex_factory_date) 
						{ 
							$color="#2A9FFF";
						}
						
					}//end if condition
					
					$cutting_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][1]['cQty'];
					$embl_recv_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][3]['prQty'];
					$sewingin_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][4]['sQty'];
					$sewingout_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][5]['soQty'];
					$iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][7]['iQty'];
					$re_iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][7]['riQty'];
					$finish_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][8]['fQty'];
					
					$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$po_quantity_in_pcs;
					$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$po_quantity_in_pcs*$unit_price;
					$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
					
					$actual_exces_cut = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][1]['cQty'];
					if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format( (($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2)."%";

					$issue_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][2]['print'];
					$issue_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][2]['emb'];
					$issue_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][2]['wash'];
					$issue_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][2]['special'];
					
					$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special;
					
					$embl_issue_total="";
					if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
					if($issue_emb!=0) 
					{
						if($embl_issue_total=="") $embl_issue_total .= 'EM='.$issue_emb; else $embl_issue_total .= ', EM='.$issue_emb;
					}
					if($issue_wash!=0) 
					{
						if($embl_issue_total=="") $embl_issue_total .= 'WA='.$issue_wash; else $embl_issue_total .= ', WA='.$issue_wash;
					}
					if($issue_special!=0) 
					{
						if($embl_issue_total=="") $embl_issue_total .= 'SP='.$issue_special; else $embl_issue_total .= ', SP='.$issue_special;
					}

					$rcv_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][3]['print'];
					$rcv_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][3]['emb'];
					$rcv_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][3]['wash'];
					$rcv_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][3]['special'];
					
					$embl_receive_total="";	
					if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
					
					if($rcv_emb!=0) 
					{
						if($embl_receive_total=="") $embl_receive_total .= 'EM='.$rcv_emb; else $embl_receive_total .= ', EM='.$rcv_emb;
					}
					
					if($rcv_wash!=0) 
					{
						if($embl_receive_total=="") $embl_receive_total .= 'WA='.$rcv_wash; else $embl_receive_total .= ', WA='.$rcv_wash;
					}
					
					if($rcv_special!=0) 
					{
						if($embl_receive_total=="") $embl_receive_total .= 'SP='.$rcv_special; else $embl_receive_total .= ', SP='.$rcv_special;
					}
					
					$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special;
					
					//$rej_value=$proRes[csf("finish_rej_qnty")]+$proRes[csf("sewingout_rej_qnty")];	
					$rej_value=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][1]['crQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][5]['sorQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][8]['frQty'];
					$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
					$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
					
					if($j==0) 
					{
						$display_font_color="";
						$font_end="";
					}
					else 
					{
						$display_font_color="&nbsp;<font style='display:none'>";
						$font_end="</font>";
					}
					
					$company_name=$orderRes[csf("company_name")];
					$po_id=$orderRes[csf("id")];
					
					if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
					if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
					if(round($actual_exces_cut) > round($orderRes[csf("excess_cut")])) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
					
					$total_rej_value+=$rej_value; 
					
					$template_id=$template_id_arr[$orderRes[csf('id')]];
					 
				   	$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
					$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
					
								<td width="130"><p><a href="##" onclick="progress_comment_popup('.$orderRes[csf("id")].",'".$template_id."',".$tna_process_type.')">'.$display_font_color.$orderRes[csf("po_number")].$font_end.'</a></p></td>
								
								<td width="60"><p>'.$display_font_color.$buyer_short_library[$orderRes[csf("buyer_name")]].$font_end.'</p></td>
								<td width="80" align="center"><p>'.$display_font_color.$orderRes[csf("job_no_prefix_num")].$font_end.'</p></td>
								<td width="120"><p>'.$display_font_color.$orderRes[csf("style_ref_no")].$font_end.'</p></td>
								<td width="130"><p>'.$garments_item[$item_id].'</p></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_order('.$po_id.",".$company_name.",".$item_id.",0,'OrderPopup'".')">'.$po_quantity_in_pcs.'</a></td>
								<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($orderRes[csf("shipment_date")]).'&nbsp;</td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'exfactory','','','','0'".')">'.$ex_factory_date.'</a></td>
								<td width="80" align="center">'.$days_remian.'&nbsp;</td>
								<td width="80" align="right">&nbsp;'.number_format($orderRes[csf("excess_cut")],2)." %".'</td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'1','','','$type',''".')">'.$cutting_qnty.'</a></td>
								<td width="80" align="right" '.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut.'</td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'2','','','$type',''".')">'.$embl_issue_total.'</a></td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'3','','','$type',''".')">'.$embl_receive_total.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'4','','','$type',''".')">'.$sewingin_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'5','','','$type',''".')">'.$sewingout_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'7','','','$type',''".')">'.$iron_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'9','','','$type',''".')">'.$re_iron_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'8','','','$type',''".')">'.$finish_qnty.'</a></td>
								<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage_rej('.$po_id.",".$item_id.",'reject_qty','','','$type',''".')">'.$rej_value.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'10','','','$type',''".')">'.$ex_factory_qnty.'</a></td>
								<td width="80" align="right">&nbsp;'.$shortage.'</td>
								<td width="85">'.$shipment_status[$orderRes[csf("shiping_status")]].'</td>
								<td>&nbsp;<a href="##" onclick="openmypage_remark('.$po_id.",".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
							</tr>';
				}
			} //end for loop
		}// end main foreach 
    ?>
    <div>
    	<table width="1500" cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? 
                        if($type==1) echo "Order Wise Production Report";
                        else if($type==2) echo "Order Location & Floor Wise Production Report";
                        else if($type==3) echo "Order Country Wise Production Report";
                        else if($type==4)echo "Order Country Location & Floor Wise Production Report";
                        else echo "Style Wise Production Report";
                    ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $fromDate To $toDate" ;
                        }
                    ?>
                </td>
            </tr>
        </table>
		<div style="float:left; width:1150px">
            <table width="1100" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th colspan="14" >In-House Order Production </th>
                    </tr>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Total Embl. Rcv. Qty</th>
                        <th width="80">Total Sew Input Qty</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Total Re-Iron Qty</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Fin Goods Status %</th>
                        <th width="80">Ex-Fac</th>
                        <th>Ex-Fac%</th>
                     </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:1120px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1100" rules="all" id="" >
                <?
					$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
					$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
					foreach($buyer_array as $buyer_id=>$value)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="80"><? echo $buyer_short_library[$buyer_id]; ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]);?></td>
                            <td width="80" align="right"><? echo number_format($value["poVal"],2);?></td>
                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
                            <td width="80" align="right"><? echo number_format($finish_gd_status,2); ?></td>
                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
                            <td width="" align="right"><? echo  number_format($ex_gd_status,2); ?></td>
                        </tr>	
                        <?		
                            $total_po_quantity+=$value["poQty"];
                            $total_po_value+=$value["poVal"];
                            $total_cut+=$value["cQty"];
                            $total_print_re+=$value["prQty"];
                            $total_sew_input+=$value["sQty"];
                            $total_sew_out+=$value["soQty"];
                            $total_iron+=$value["iQty"];
                            $total_re_iron+=$value["reiQty"];
                            $total_finish+=$value["fQty"];
                            $total_ex_factory+=$value["ex"];
                           
                        $i++;
                    }//end foreach 1st
                    
                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                ?>
                </table>
                <table border="1" class="tbl_bottom"  width="1100" rules="all" id="" >
                    <tr> 
                        <td width="30">&nbsp;</td> 
                        <td width="80" align="right">Total</td> 
                        <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?></td> 
                        <td width="80" id="tot_po_value"><? echo number_format($total_po_value,2); ?></td> 
                        <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
                        <td width="80" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
                        <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
                        <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                        <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                        <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
                        <td width="80"><? echo number_format($total_finish_gd_status,2); ?></td >
                        <td width="80"><? echo number_format($total_ex_factory); ?></td >
                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                        <td width=""><? echo number_format($total_ex_status,2); ?></td>
                    </tr>
                 </table>
                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
            </div>
        </div>
        <div style="float:left; width:600px">   
            <table>
                <tr>
                    <td height="21" width="600"><div id="chartdiv"> </div></td>
                </tr>    
            </table>
        </div> 
        <div style="clear:both"></div>
        <table width="2280" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <tr>
                    <th width="30">SL</th>    
                    <th width="130">Order Number</th>
                    <th width="60">Buyer Name</th>
                    <th width="80">Job Number</th>
                    <th width="120">Style Name</th>
                    <th width="130">Item Name</th>
                    <th width="80">Order Qty.</th>
                    <th width="80">Ship Date</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="80">Delay</th>
                    <th width="80">Stan. Exc. Cut %</th>
                    <th width="80">Total Cut Qty</th>
                    <th width="80">Actual Exc. Cut %</th>
                    <th width="80">Total Embl. Issue Qty</th>
                    <th width="80">Total Embl. Rcv. Qty</th>
                    <th width="80">Total Sew Input Qty</th>
                    <th width="80">Total Sew Output Qty</th>
                    <th width="80">Total Iron Qty</th>
                    <th width="80">Total Re-Iron Qty</th>
                    <th width="80">Total Finish Qty</th>
                    <th width="80">Fin Goods Status</th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Total Out</th>
                    <th width="80">Shortage/ Excess</th>
                    <th width="85">Status</th>
                    <th>Remarks</th>
                 </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:2300px" id="scroll_body">
            <table border="1" class="rpt_table" width="2280" rules="all" id="table_body">
				<? echo $html; ?>  
            </table>	
            <table border="1" class="tbl_bottom" width="2280" rules="all" id="report_table_footer_1" >
                <tr>
                    <td width="30"></td>
                    <td width="130"></td>
                    <td width="60"></td>
                    <td width="80"></td>
                    <td width="120"></td>
                    <td width="130">Total</td>
                    <td width="80" id="total_order_quantity"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80" id="total_cutting"></td>
                    <td width="80"></td>
                    <td width="80" id="total_emb_issue"></td>
                    <td width="80" id="total_emb_receive"></td>
                    <td width="80" id="total_sewing_input"></td>
                    <td width="80" id="total_sewing_out"></td>
                    <td width="80" id="total_iron_qnty"></td>
                    <td width="80" id="total_re_iron_qnty"></td>
                    <td width="80" id="total_finish_qnty"></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_rej_value_td"></td>
                    <td width="80" id="total_out"></td>
                    <td width="80" id="total_shortage"></td>
                    <td width="85"></td>
                    <td></td>
                 </tr>
			</table>
        </div>
     </div>   
    <?
    }
	else if($type==2)
    {
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, location, MAX(ex_factory_date) AS ex_factory_date,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, location");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('location')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('location')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[107]);die;
		
		if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id as po_id, c.item_number_id as item_id, location, floor_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id, location, floor_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id as po_id, c.item_number_id as item_id, location, floor_id, 
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id, location, floor_id";
		}
		
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['special']=$gmtsRow[csf('special')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['special']=$gmtsRow[csf('specialr')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_id')]][$gmtsRow[csf('item_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];
		}
		
		$buyer_array=array(); 
		$order_sql="select a.id, b.job_no_prefix_num, a.po_number, a.po_quantity, a.unit_price, a.po_total_price, a.job_no_mst, a.pub_shipment_date as shipment_date,a.shiping_status,a.excess_cut,a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, c.location, c.floor_id from wo_po_details_master b,wo_po_break_down a left join pro_garments_production_mst c on c.po_break_down_id=a.id and c.status_active=1 and c.is_deleted=0 $location $floor where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1 and a.po_number like '$search_string' $txt_date $company_name $buyer_name $garmentsNature group by a.id, b.job_no_prefix_num, a.po_number, a.po_quantity, a.job_no_mst, a.pub_shipment_date, a.shiping_status, a.excess_cut, a.unit_price, a.po_total_price, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, c.location, c.floor_id order by a.id, c.location, c.floor_id";
		$result=sql_select($order_sql);
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   	$i++;
		   	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   	$setArr = explode("__",$orderRes[csf("set_break_down")] );
		  	$countArr = count($setArr); 
		   	if($countArr==0) $countArr=1; 
		   	for($j=0;$j<$countArr;$j++)
			{
				$company_name=$orderRes[csf("company_name")];
				$po_id=$orderRes[csf("id")];
				$location=$orderRes[csf("location")];
				$floor_id=$orderRes[csf("floor_id")];
					
			   $setItemArr = explode("_",$setArr[$j]);
			   $item_id=$setItemArr[0];
			   $set_qnty=$setItemArr[1];
			   if($item_id>0)
			   {
				   	$k++;
				   	$po_quantity_in_pcs = $orderRes[csf("po_quantity")]*$set_qnty;
					$unit_price=$orderRes[csf("unit_price")]/$set_qnty;
				   	$ex_factory_date=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$location]['date'];
					$ex_factory_qnty=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$location]['qty'];
					$color=""; $days_remian="";
					if($orderRes[csf("shiping_status")]==1 || $orderRes[csf("shiping_status")]==2)
					{
						$days_remian=datediff("d",$date,$orderRes[csf("shipment_date")]); 
						if($orderRes[csf("shipment_date")] > $date) 
						{
							$color="";
						}
						else if($orderRes[csf("shipment_date")] < $date) 
						{
							$color="red";
						}														
						else if($orderRes[csf("shipment_date")] >= $date && $days_remian<=5 ) 
						{
							$color="orange";
						}
					} 
					else if($orderRes[csf("shiping_status")]==3)
					{
						$days_remian=datediff("d",$ex_factory_date,$orderRes[csf("shipment_date")]);
						if($orderRes[csf("shipment_date")] >= $ex_factory_date) 
						{ 
							$color="green";
						}
						else if($orderRes[csf("shipment_date")] < $ex_factory_date) 
						{ 
							$color="#2A9FFF";
						}
						
					}//end if condition
					
					$cutting_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][1]['cQty'];
					$embl_recv_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['prQty'];
					$sewingin_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][4]['sQty'];
					$sewingout_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][5]['soQty'];
					$iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][7]['iQty'];
					$re_iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][7]['riQty'];
					$finish_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][8]['fQty'];
					
					$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$po_quantity_in_pcs;
					$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$po_quantity_in_pcs*$unit_price;
					$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
					
					$actual_exces_cut = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][1]['cQty'];
					if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format( (($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2)."%";

					$issue_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][2]['print'];
					$issue_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][2][$location][$floor_id]['emb'];
					$issue_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][2][$location][$floor_id]['wash'];
					$issue_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][2]['special'];
					
					$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special;
					
					$embl_issue_total="";
					if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
					if($issue_emb!=0) $embl_issue_total .= ', EM='.$issue_emb;
					if($issue_wash!=0) $embl_issue_total .= ', WA='.$issue_wash;
					if($issue_special!=0) $embl_issue_total .= ', SP='.$issue_special;

					$rcv_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['print'];
					$rcv_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['emb'];
					$rcv_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['wash'];
					$rcv_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][3]['special'];
					
					$embl_receive_total="";	
					if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
					if($rcv_emb!=0) $embl_receive_total .= ', EM='.$rcv_emb;
					if($rcv_wash!=0) $embl_receive_total .= ', WA='.$rcv_wash;
					if($rcv_special!=0) $embl_receive_total .= ', SP='.$rcv_special;
					
					$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special;
					
					//$rej_value=$proRes[csf("finish_rej_qnty")]+$proRes[csf("sewingout_rej_qnty")];	
					$rej_value=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][1]['crQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][5]['sorQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$location][$floor_id][8]['frQty'];
					$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
					$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
					
					if($j==0) 
					{
						$display_font_color="";
						$font_end="";
					}
					else 
					{
						$display_font_color="&nbsp;<font style='display:none'>";
						$font_end="</font>";
					}
					
					if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
					if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
					if(round($actual_exces_cut) > round($orderRes[csf("excess_cut")])) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
					
					$total_rej_value+=$rej_value; 
				   	$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
					$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
								<td width="130"><p>'.$display_font_color.$orderRes[csf("po_number")].$font_end.'</p></td>
								<td width="60"><p>'.$display_font_color.$buyer_short_library[$orderRes[csf("buyer_name")]].$font_end.'</p></td>
								<td width="80" align="center"><p>'.$display_font_color.$orderRes[csf("job_no_prefix_num")].$font_end.'</p></td>
								<td width="120"><p>'.$display_font_color.$orderRes[csf("style_ref_no")].$font_end.'</p></td>
								<td width="130"><p>'.$garments_item[$item_id].'</p></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_order('.$po_id.",".$company_name.",".$item_id.",0,'OrderPopup'".')">'.$po_quantity_in_pcs.'</a></td>
								<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($orderRes[csf("shipment_date")]).'&nbsp;</td>
                                <td width="80"><p>'.$location_library[$orderRes[csf("location")]].'&nbsp;</p></td>
                                <td width="80"><p>'.$floor_library[$orderRes[csf("floor_id")]].'&nbsp;</p></td>
								<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'exfactory','$location','','','0'".')">'.$ex_factory_date.'</a></td>
								<td width="80" align="center">'.$days_remian.'&nbsp;</td>
								<td width="80" align="right">&nbsp;'.number_format($orderRes[csf("excess_cut")],2)." %".'</td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'1','$location','$floor_id','$type',''".')">'.$cutting_qnty.'</a></td>
								<td width="80" align="right" '.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut.'</td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'2','','','$type',''".')">'.$embl_issue_total.'</a></td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'3','','','$type',''".')">'.$embl_receive_total.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'4','$location','$floor_id','$type',''".')">'.$sewingin_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'5','$location','$floor_id','$type',''".')">'.$sewingout_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'7','$location','$floor_id','$type',''".')">'.$iron_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'9','$location','$floor_id','$type',''".')">'.$re_iron_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'8','$location','$floor_id','$type',''".')">'.$finish_qnty.'</a></td>
								<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage_rej('.$po_id.",".$item_id.",'reject_qty','$location','$floor_id','$type',''".')">'.$rej_value.'</a></td>
								<td width="80" align="right">&nbsp;'.$ex_factory_qnty.'</td>
								<td width="80" align="right">&nbsp;'.$shortage.'</td>
								<td width="85">'.$shipment_status[$orderRes[csf("shiping_status")]].'</td>
								<td>&nbsp;<a href="##" onclick="openmypage_remark('.$po_id.",".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
							</tr>';
				}
			} //end for loop
		}// end main foreach 
    ?>
    <div>
    	<table width="1500" cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? echo "Order Location & Floor Wise Production Report"; ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="28" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="28" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $fromDate To $toDate" ;
                        }
                    ?>
                </td>
            </tr>
        </table>
		<div style="float:left; width:1150px">
            <table width="1100" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th colspan="14" >In-House Order Production</th>
                    </tr>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Total Embl. Rcv. Qty</th>
                        <th width="80">Total Sew Input Qty</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Total Re-Iron Qty</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Fin Goods Status</th>
                        <th width="80">Ex-Fac</th>
                        <th>Ex-Fac%</th>
                     </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:1120px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1100" rules="all" id="" >
                <?
					$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
					$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
					foreach($buyer_array as $buyer_id=>$value)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="80"><? echo $buyer_short_library[$buyer_id]; ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]);?></td>
                            <td width="80" align="right"><? echo number_format($value["poVal"],2);?></td>
                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
                        </tr>	
                        <?		
                            $total_po_quantity+=$value["poQty"];
                            $total_po_value+=$value["poVal"];
                            $total_cut+=$value["cQty"];
                            $total_print_re+=$value["prQty"];
                            $total_sew_input+=$value["sQty"];
                            $total_sew_out+=$value["soQty"];
                            $total_iron+=$value["iQty"];
                            $total_re_iron+=$value["reiQty"];
                            $total_finish+=$value["fQty"];
                            $total_ex_factory+=$value["ex"];
                           
                        $i++;
                    }//end foreach 1st
                    
                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                ?>
                </table>
                <table border="1" class="tbl_bottom" width="1100" rules="all" id="" >
                    <tr> 
                        <td width="30">&nbsp;</td> 
                        <td width="80" align="right">Total</td> 
                        <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?></td> 
                        <td width="80" id="tot_po_value"><? echo number_format($total_po_value,2); ?></td> 
                        <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
                        <td width="80" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
                        <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
                        <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                        <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                        <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
                        <td width="80"><? echo number_format($total_finish_gd_status,2); ?></td >
                        <td width="80"><? echo number_format($total_ex_factory); ?></td >
                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                        <td width=""><? echo number_format($total_ex_status,2); ?></td>
                    </tr>
                 </table>
                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
            </div>
        </div>
        <div style="float:left; width:600px">   
            <table>
                <tr>
                    <td height="21" width="600"><div id="chartdiv"> </div></td>
                </tr>    
            </table>
        </div> 
        <div style="clear:both"></div>
        <table width="2380" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <tr>
                    <th width="30">SL</th>    
                    <th width="130">Order Number</th>
                    <th width="60">Buyer Name</th>
                    <th width="80">Job Number</th>
                    <th width="120">Style Name</th>
                    <th width="130">Item Name</th>
                    <th width="80">Order Qty.</th>
                    <th width="80">Ship Date</th>
                    <th width="80">Location</th>
                    <th width="80">Floor</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="80">Delay</th>
                    <th width="80">Stan. Exc. Cut %</th>
                    <th width="80">Total Cut Qty</th>
                    <th width="80">Actual Exc. Cut %</th>
                    <th width="80">Total Embl. Issue Qty</th>
                    <th width="80">Total Embl. Rcv. Qty</th>
                    <th width="80">Total Sew Input Qty</th>
                    <th width="80">Total Sew Output Qty</th>
                    <th width="80">Total Iron Qty</th>
                    <th width="80">Total Re-Iron Qty</th>
                    <th width="80">Total Finish Qty</th>
                    <th width="80">Fin Goods Status</th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Total Out</th>
                    <th width="80">Shortage/ Excess</th>
                    <th width="85">Status</th>
                    <th>Remarks</th>
                 </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:2400px" id="scroll_body">
            <table border="1" class="rpt_table" width="2380" rules="all" id="table_body">
				<? echo $html; ?>  
            </table>	
            <table border="1" class="tbl_bottom" width="2380" rules="all" id="report_table_footer_1" >
                <tr>
                    <td width="30"></td>
                    <td width="130"></td>
                    <td width="60"></td>
                    <td width="80"></td>
                    <td width="120"></td>
                    <td width="130">Total</td>
                    <td width="80" id="total_order_quantity"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80" id="total_cutting"></td>
                    <td width="80"></td>
                    <td width="80" id="total_emb_issue"></td>
                    <td width="80" id="total_emb_receive"></td>
                    <td width="80" id="total_sewing_input"></td>
                    <td width="80" id="total_sewing_out"></td>
                    <td width="80" id="total_iron_qnty"></td>
                    <td width="80" id="total_re_iron_qnty"></td>
                    <td width="80" id="total_finish_qnty"></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_rej_value_td"></td>
                    <td width="80" id="total_out"></td>
                    <td width="80" id="total_shortage"></td>
                    <td width="85"></td>
                    <td></td>
                 </tr>
			</table>
        </div>
     </div>   
    <?
    }
	else if($type==5)
	{
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, MAX(ex_factory_date) AS ex_factory_date,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[107]);die;
		
		if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, 
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id";
		}
		
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][2]['special']=$gmtsRow[csf('special')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][3]['special']=$gmtsRow[csf('specialr')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];
		}
		
		$buyer_array=array(); 
		if($db_type==0)
		{
			$order_sql="select group_concat(distinct(a.id)) as id, b.job_no_prefix_num, group_concat(distinct(a.po_number)) as po_number, group_concat(concat_ws('**',a.id,a.po_quantity,a.unit_price)) as po_data, sum(a.po_quantity) as po_quantity, a.job_no_mst, MAX(a.pub_shipment_date) as shipment_date, a.shiping_status, sum(a.excess_cut) as excess_cut, sum(a.plan_cut) as plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.style_ref_no like '$search_string' and b.status_active=1 and b.is_deleted=0 $txt_date $company_name $buyer_name $garmentsNature group by b.id";		
		}
		else
		{
			$order_sql="select LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as id, LISTAGG(a.id || '**' || a.po_quantity || '**' || a.unit_price, ',') WITHIN GROUP (ORDER BY a.id) as po_data, b.job_no_prefix_num, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number, sum(a.po_quantity) as po_quantity, MAX(a.pub_shipment_date) as shipment_date, b.job_no as job_no_mst, min(a.shiping_status) as shiping_status, sum(a.excess_cut) as excess_cut, sum(a.plan_cut) as plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.style_ref_no like '$search_string' and b.status_active=1 and b.is_deleted=0 $txt_date $company_name $buyer_name $garmentsNature group by b.id, b.job_no, b.job_no_prefix_num, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no";	
		}
		$result=sql_select($order_sql);
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   $i++;
		   if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   $setArr = explode("__",$orderRes[csf("set_break_down")] );
		   $countArr = count($setArr); 
		   if($countArr==0) $countArr=1; 
		   for($j=0;$j<$countArr;$j++)
		   {
			   $setItemArr = explode("_",$setArr[$j]);
			   $item_id=$setItemArr[0];
			   $set_qnty=$setItemArr[1];
			   
			   if($item_id>0)
			   {
				   	$k++;
					$po_data=explode(",",$orderRes[csf("po_data")]); 
					$po_quantity_in_pcs=0; $poValue=0; $ex_factory_date=''; $ex_factory_qnty=0; $po_id='';
					$cutting_qnty=0; $embl_recv_qnty=0; $sewingin_qnty=0; $sewingout_qnty=0; $iron_qnty=0; $re_iron_qnty=0; $finish_qnty=0;
					$issue_print=0; $issue_emb=0; $issue_wash=0; $issue_special=0; $rcv_print=0; $rcv_emb=0; $rcv_wash=0; $rcv_special=0; $rej_value=0;
					foreach($po_data as $value)
					{
						$po_value=explode("**",$value);
						$order_id=$po_value[0];
						$po_quantity=$po_value[1];
						$unit_price=$po_value[2]/$set_qnty;
						$po_quantity_in_pcs+=$po_quantity*$set_qnty;
						$poValue+=$po_quantity*$set_qnty*$unit_price;
						
						if($po_id=='') $po_id=$order_id; else $po_id.=",".$order_id;
						
						$exDate=$ex_factory_arr[$order_id][$item_id]['date'];
						if($exDate > $ex_factory_date) $ex_factory_date=$exDate; 
						$ex_factory_qnty+=$ex_factory_arr[$order_id][$item_id]['qty'];
						
						$cutting_qnty+=$gmts_prod_arr[$order_id][$item_id][1]['cQty'];
						$embl_recv_qnty+=$gmts_prod_arr[$order_id][$item_id][3]['prQty'];
						$sewingin_qnty+=$gmts_prod_arr[$order_id][$item_id][4]['sQty'];
						$sewingout_qnty+=$gmts_prod_arr[$order_id][$item_id][5]['soQty'];
						$iron_qnty+=$gmts_prod_arr[$order_id][$item_id][7]['iQty'];
						$re_iron_qnty+=$gmts_prod_arr[$order_id][$item_id][7]['riQty'];
						$finish_qnty+=$gmts_prod_arr[$order_id][$item_id][8]['fQty'];
						
						$issue_print += $gmts_prod_arr[$order_id][$item_id][2]['print'];
						$issue_emb += $gmts_prod_arr[$order_id][$item_id][2]['emb'];
						$issue_wash += $gmts_prod_arr[$order_id][$item_id][2]['wash'];
						$issue_special += $gmts_prod_arr[$order_id][$item_id][2]['special'];
						
						$rcv_print += $gmts_prod_arr[$order_id][$item_id][3]['print'];
						$rcv_emb += $gmts_prod_arr[$order_id][$item_id][3]['emb'];
						$rcv_wash += $gmts_prod_arr[$order_id][$item_id][3]['wash'];
						$rcv_special += $gmts_prod_arr[$order_id][$item_id][3]['special'];
						
						$rej_value+=$gmts_prod_arr[$order_id][$item_id][1]['crQty']+$gmts_prod_arr[$order_id][$item_id][5]['sorQty']+$gmts_prod_arr[$order_id][$item_id][8]['frQty'];
					}
				   
					$color=""; $days_remian="";
					if($orderRes[csf("shiping_status")]==1 || $orderRes[csf("shiping_status")]==2)
					{
						$days_remian=datediff("d",$date,$orderRes[csf("shipment_date")]); 
						if($orderRes[csf("shipment_date")] > $date) 
						{
							$color="";
						}
						else if($orderRes[csf("shipment_date")] < $date) 
						{
							$color="red";
						}														
						else if($orderRes[csf("shipment_date")] >= $date && $days_remian<=5 ) 
						{
							$color="orange";
						}
					} 
					else if($orderRes[csf("shiping_status")]==3)
					{
						$days_remian=datediff("d",$ex_factory_date,$orderRes[csf("shipment_date")]);
						if($orderRes[csf("shipment_date")] >= $ex_factory_date) 
						{ 
							$color="green";
						}
						else if($orderRes[csf("shipment_date")] < $ex_factory_date) 
						{ 
							$color="#2A9FFF";
						}
						
					}//end if condition
					
					$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$po_quantity_in_pcs;
					$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$poValue;
					$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
					$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
					
					$actual_exces_cut = $cutting_qnty;
					if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format( (($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2)."%";

					$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special;
					$embl_issue_total="";	
					if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
					if($issue_emb!=0) $embl_issue_total .= ', EM='.$issue_emb;
					if($issue_wash!=0) $embl_issue_total .= ', WA='.$issue_wash;
					if($issue_special!=0) $embl_issue_total .= ', SP='.$issue_special;

					$embl_receive_total="";	
					if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
					if($rcv_emb!=0) $embl_receive_total .= ', EM='.$rcv_emb;
					if($rcv_wash!=0) $embl_receive_total .= ', WA='.$rcv_wash;
					if($rcv_special!=0) $embl_receive_total .= ', SP='.$rcv_special;
					$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special;
					
					$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
					$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
					
					if($j==0) 
					{
						$display_font_color="";
						$font_end="";
					}
					else 
					{
						$display_font_color="&nbsp;<font style='display:none'>";
						$font_end="</font>";
					}
					
					$company_name=$orderRes[csf("company_name")];
					$po_id=$orderRes[csf("id")];
					
					if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
					if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
					if(round($actual_exces_cut) > round($orderRes[csf("excess_cut")])) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
					
					$total_rej_value+=$rej_value; 
					 	
				   	$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
					$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
								<td width="120"><p>'.$display_font_color.$orderRes[csf("style_ref_no")].$font_end.'</p></td>
								<td width="80" align="center"><p>'.$display_font_color.$orderRes[csf("job_no_prefix_num")].$font_end.'</p></td>
								<td width="130"><p>'.$display_font_color.$orderRes[csf("po_number")].$font_end.'</p></td>
								<td width="60"><p>'.$display_font_color.$buyer_short_library[$orderRes[csf("buyer_name")]].$font_end.'</p></td>
								<td width="130"><p>'.$garments_item[$item_id].'</p></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_order('."'".$po_id."',".$company_name.",".$item_id.",0,'OrderPopup'".')">'.$po_quantity_in_pcs.'</a></td>
								<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($orderRes[csf("shipment_date")]).'&nbsp;</td>
								<td width="80" align="right"><a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'exfactory','','','','0'".')">'.$ex_factory_date.'</a></td>
								<td width="80" align="center">'.$days_remian.'&nbsp;</td>
								<td width="80" align="right">&nbsp;'.number_format($orderRes[csf("excess_cut")],2)." %".'</td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'1','','','$type',''".')">'.$cutting_qnty.'</a></td>
								<td width="80" align="right" '.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut.'</td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'2','','','$type',''".')">'.$embl_issue_total.'</a></td>
								<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'3','','','$type',''".')">'.$embl_receive_total.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'4','','','$type',''".')">'.$sewingin_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'5','','','$type',''".')">'.$sewingout_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'7','','','$type',''".')">'.$iron_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'9','','','$type',''".')">'.$re_iron_qnty.'</a></td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('."'".$po_id."',".$item_id.",'8','','','$type',''".')">'.$finish_qnty.'</a></td>
								<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
								<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage_rej('."'".$po_id."',".$item_id.",'reject_qty','','','$type',''".')">'.$rej_value.'</a></td>
								<td width="80" align="right">&nbsp;'.$ex_factory_qnty.'</td>
								<td width="80" align="right">&nbsp;'.$shortage.'</td>
								<td width="85">'.$shipment_status[$orderRes[csf("shiping_status")]].'</td>
								<td>&nbsp;<a href="##" onclick="openmypage_remark('."'".$po_id."',".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
							</tr>';
				}
			} //end for loop
		}// end main foreach 
    ?>
	<div>
    	<table width="1500" cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? echo "Style Wise Production Report"; ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $fromDate To $toDate" ;
                        }
                    ?>
                </td>
            </tr>
        </table>
		<div style="float:left; width:1150px">
            <table width="1100" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th colspan="14" >In-House Order Production </th>
                    </tr>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Total Embl. Rcv. Qty</th>
                        <th width="80">Total Sew Input Qty</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Total Re-Iron Qty</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Fin Goods Status</th>
                        <th width="80">Ex-Fac</th>
                        <th>Ex-Fac%</th>
                     </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:1120px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1100" rules="all" id="" >
                <?
					$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
					$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
					foreach($buyer_array as $buyer_id=>$value)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="80"><? echo $buyer_short_library[$buyer_id]; ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]);?></td>
                            <td width="80" align="right"><? echo number_format($value["poVal"],2);?></td>
                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
                        </tr>	
                        <?		
                            $total_po_quantity+=$value["poQty"];
                            $total_po_value+=$value["poVal"];
                            $total_cut+=$value["cQty"];
                            $total_print_re+=$value["prQty"];
                            $total_sew_input+=$value["sQty"];
                            $total_sew_out+=$value["soQty"];
                            $total_iron+=$value["iQty"];
                            $total_re_iron+=$value["reiQty"];
                            $total_finish+=$value["fQty"];
                            $total_ex_factory+=$value["ex"];
                           
                        $i++;
                    }//end foreach 1st
                    
                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                ?>
                </table>
                <table border="1" class="tbl_bottom"  width="1100" rules="all" id="" >
                    <tr> 
                        <td width="30">&nbsp;</td> 
                        <td width="80" align="right">Total</td> 
                        <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?></td> 
                        <td width="80" id="tot_po_value"><? echo number_format($total_po_value,2); ?></td> 
                        <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
                        <td width="80" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
                        <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
                        <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                        <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                        <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
                        <td width="80"><? echo number_format($total_finish_gd_status,2); ?></td >
                        <td width="80"><? echo number_format($total_ex_factory); ?></td >
                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                        <td width=""><? echo number_format($total_ex_status,2); ?></td>
                    </tr>
                 </table>
                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
            </div>
        </div>
        <div style="float:left; width:600px">   
            <table>
                <tr>
                    <td height="21" width="600"><div id="chartdiv"> </div></td>
                </tr>    
            </table>
        </div> 
        <div style="clear:both"></div>
        <table width="2280" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="120">Style Name</th>  
                    <th width="80">Job Number</th>  
                    <th width="130">Order Number</th>
                    <th width="60">Buyer Name</th>
                    <th width="130">Item Name</th>
                    <th width="80">Order Qty.</th>
                    <th width="80">Ship Date</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="80">Delay</th>
                    <th width="80">Stan. Exc. Cut %</th>
                    <th width="80">Total Cut Qty</th>
                    <th width="80">Actual Exc. Cut %</th>
                    <th width="80">Total Embl. Issue Qty</th>
                    <th width="80">Total Embl. Rcv. Qty</th>
                    <th width="80">Total Sew Input Qty</th>
                    <th width="80">Total Sew Output Qty</th>
                    <th width="80">Total Iron Qty</th>
                    <th width="80">Total Re-Iron Qty</th>
                    <th width="80">Total Finish Qty</th>
                    <th width="80">Fin Goods Status</th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Total Out</th>
                    <th width="80">Shortage/ Excess</th>
                    <th width="85">Status</th>
                    <th>Remarks</th>
                 </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:2300px" id="scroll_body">
            <table border="1" class="rpt_table" width="2280" rules="all" id="table_body">
				<? echo $html; ?>  
            </table>	
            <table border="1" class="tbl_bottom" width="2280" rules="all" id="report_table_footer_1" >
                <tr>
                    <td width="30"></td>
                    <td width="120"></td>
                    <td width="80"></td>
                    <td width="130"></td>
                    <td width="60"></td>
                    <td width="130">Total</td>
                    <td width="80" id="total_order_quantity"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80" id="total_cutting"></td>
                    <td width="80"></td>
                    <td width="80" id="total_emb_issue"></td>
                    <td width="80" id="total_emb_receive"></td>
                    <td width="80" id="total_sewing_input"></td>
                    <td width="80" id="total_sewing_out"></td>
                    <td width="80" id="total_iron_qnty"></td>
                    <td width="80" id="total_re_iron_qnty"></td>
                    <td width="80" id="total_finish_qnty"></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_rej_value_td"></td>
                    <td width="80" id="total_out"></td>
                    <td width="80" id="total_shortage"></td>
                    <td width="85"></td>
                    <td></td>
                 </tr>
			</table>
		</div>
	</div>       
	<?
	}
	else if($type==3)
	{
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, item_number_id, country_id, MAX(ex_factory_date) AS ex_factory_date,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[107]);die;
		
		if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id, country_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id, country_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, country_id,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id, country_id";
		}
		
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][2]['special']=$gmtsRow[csf('special')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][3]['special']=$gmtsRow[csf('specialr')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];
		}
		
		$po_country_arr=array(); $po_country_data_arr=array();
		$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(order_total) as value from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
		foreach($poCountryData as $row)
		{
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['qnty']=$row[csf('qnty')];
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['value']=$row[csf('value')];
			$po_country_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].",";
		}
		
		$buyer_array=array(); 
		$order_sql="select a.id, b.job_no_prefix_num, a.po_number ,sum(order_quantity) as qnty, sum(order_total) as value,c.country_id,  a.unit_price, a.po_total_price, a.job_no_mst, c.country_ship_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no from wo_po_details_master b,wo_po_break_down a,wo_po_color_size_breakdown c where a.job_no_mst=b.job_no and b.job_no=c.job_no_mst and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and a.po_number like '$search_string' and b.status_active=1 and b.is_deleted=0 $txt_country_date $company_name $buyer_name $garmentsNature  group by c.country_ship_date,c.country_id,a.id, b.job_no_prefix_num, a.po_number,  a.unit_price, a.po_total_price, a.job_no_mst,a.shiping_status, a.excess_cut, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no order by c.country_ship_date, a.id";
		//echo $order_sql;die;
		$result=sql_select($order_sql);
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   $i++;
		   if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   $setArr = explode("__",$orderRes[csf("set_break_down")] );
		   $countArr = count($setArr); 
		   if($countArr==0) $countArr=1; $s=0;
		   for($j=0;$j<$countArr;$j++)
		   {
			  	$setItemArr = explode("_",$setArr[$j]);
			   	$item_id=$setItemArr[0];
			   	$set_qnty=$setItemArr[1];
			   	if($item_id>0)
			   	{
					$country=array_unique(explode(",",substr($po_country_arr[$orderRes[csf("id")]],0,-1)));
					foreach($country as $country_id)
					{
						$k++;
						$po_quantity_in_pcs = $po_country_data_arr[$orderRes[csf("id")]][$item_id][$country_id]['qnty'];
						$po_value = $po_country_data_arr[$orderRes[csf("id")]][$item_id][$country_id]['value'];
					   // $po_quantity_in_pcs = $orderRes[csf("qnty")];
					    //$po_value = $orderRes[csf("value")];
						$unit_price=$orderRes[csf("unit_price")]/$set_qnty;
						$ex_factory_date=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$country_id]['date'];
						$ex_factory_qnty=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$country_id]['qty'];
						$color=""; $days_remian="";
						if($orderRes[csf("shiping_status")]==1 || $orderRes[csf("shiping_status")]==2)
						{
							$days_remian=datediff("d",$date,$orderRes[csf("shipment_date")]); 
							if($orderRes[csf("shipment_date")] > $date) 
							{
								$color="";
							}
							else if($orderRes[csf("shipment_date")] < $date) 
							{
								$color="red";
							}														
							else if($orderRes[csf("shipment_date")] >= $date && $days_remian<=5 ) 
							{
								$color="orange";
							}
						} 
						else if($orderRes[csf("shiping_status")]==3)
						{
							$days_remian=datediff("d",$ex_factory_date,$orderRes[csf("shipment_date")]);
							if($orderRes[csf("shipment_date")] >= $ex_factory_date) 
							{ 
								$color="green";
							}
							else if($orderRes[csf("shipment_date")] < $ex_factory_date) 
							{ 
								$color="#2A9FFF";
							}
							
						}//end if condition
						
						$cutting_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][1]['cQty'];
						$embl_recv_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][3]['prQty'];
						$sewingin_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][4]['sQty'];
						$sewingout_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][5]['soQty'];
						$iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][7]['iQty'];
						$re_iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][7]['riQty'];
						$finish_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][8]['fQty'];
						
						$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$po_quantity_in_pcs;
						$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$po_value;
						$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
						
						$actual_exces_cut = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][1]['cQty'];
						if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format( (($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2)."%";
	
						$issue_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][2]['print'];
						$issue_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][2]['emb'];
						$issue_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][2]['wash'];
						$issue_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][2]['special'];
						
						$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special;
						
						$embl_issue_total="";
						if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
						if($issue_emb!=0) $embl_issue_total .= ', EM='.$issue_emb;
						if($issue_wash!=0) $embl_issue_total .= ', WA='.$issue_wash;
						if($issue_special!=0) $embl_issue_total .= ', SP='.$issue_special;
	
						$rcv_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][3]['print'];
						$rcv_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][3]['emb'];
						$rcv_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][3]['wash'];
						$rcv_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][3]['special'];
						
						$embl_receive_total="";	
						if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
						if($rcv_emb!=0) $embl_receive_total .= ', EM='.$rcv_emb;
						if($rcv_wash!=0) $embl_receive_total .= ', WA='.$rcv_wash;
						if($rcv_special!=0) $embl_receive_total .= ', SP='.$rcv_special;
						
						$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special;
						
						//$rej_value=$proRes[csf("finish_rej_qnty")]+$proRes[csf("sewingout_rej_qnty")];	
						$rej_value=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][1]['crQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][5]['sorQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][8]['frQty'];
						$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
						$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
						
						if($s==0) 
						{
							$display_font_color="";
							$font_end="";
						}
						else 
						{
							$display_font_color="&nbsp;<font style='display:none'>";
							$font_end="</font>";
						}
						
						$company_name=$orderRes[csf("company_name")];
						$po_id=$orderRes[csf("id")];
						
						if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
						if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
						if(round($actual_exces_cut) > round($orderRes[csf("excess_cut")])) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
						
						$total_rej_value+=$rej_value; 
						 
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
						$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
									<td width="130"><p>'.$display_font_color.$orderRes[csf("po_number")].$font_end.'</p></td>
									<td width="60"><p>'.$display_font_color.$buyer_short_library[$orderRes[csf("buyer_name")]].$font_end.'</p></td>
									<td width="80" align="center"><p>'.$display_font_color.$orderRes[csf("job_no_prefix_num")].$font_end.'</p></td>
									<td width="120"><p>'.$display_font_color.$orderRes[csf("style_ref_no")].$font_end.'</p></td>
									<td width="130"><p>'.$garments_item[$item_id].'</p></td>
									<td width="80"><p>'.$country_library[$country_id].'&nbsp;</p></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage_order('.$po_id.",".$company_name.",".$item_id.",$country_id,'OrderPopupCountry'".')">'.$po_quantity_in_pcs.'</a></td>
									<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($orderRes[csf("shipment_date")]).'&nbsp;</td>
									<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'exfactoryCountry','','','','$country_id'".')">'.$ex_factory_date.'</a></td>
									<td width="80" align="center">'.$days_remian.'&nbsp;</td>
									<td width="80" align="right">&nbsp;'.number_format($orderRes[csf("excess_cut")],2)." %".'</td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'1','','','$type','$country_id'".')">'.$cutting_qnty.'</a></td>
									<td width="80" align="right" '.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut.'</td>
									<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'2','','','$type','$country_id'".')">'.$embl_issue_total.'</a></td>
									<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'3','','','$type','$country_id'".')">'.$embl_receive_total.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'4','','','$type','$country_id'".')">'.$sewingin_qnty.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'5','','','$type','$country_id'".')">'.$sewingout_qnty.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'7','','','$type','$country_id'".')">'.$iron_qnty.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'9','','','$type','$country_id'".')">'.$re_iron_qnty.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'8','','','$type','$country_id'".')">'.$finish_qnty.'</a></td>
									<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage_rej('.$po_id.",".$item_id.",'reject_qty','','','$type','$country_id'".')">'.$rej_value.'</a></td>
									<td width="80" align="right">&nbsp;'.$ex_factory_qnty.'</td>
									<td width="80" align="right">&nbsp;'.$shortage.'</td>
									<td width="85">'.$shipment_status[$orderRes[csf("shiping_status")]].'</td>
									<td>&nbsp;<a href="##" onclick="openmypage_remark('.$po_id.",".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
								</tr>';
						 $s++;		
					}
				}
			} //end for loop
		}// end main foreach 
    ?>
    <div>
    	<table width="1500" cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? echo "Order Country Wise Production Report"; ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $fromDate To $toDate" ;
                        }
                    ?>
                </td>
            </tr>
        </table>
		<div style="float:left; width:1150px">
            <table width="1100" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th colspan="14" >In-House Order Production </th>
                    </tr>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Total Embl. Rcv. Qty</th>
                        <th width="80">Total Sew Input Qty</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Total Re-Iron Qty</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Fin Goods Status</th>
                        <th width="80">Ex-Fac</th>
                        <th>Ex-Fac%</th>
                     </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:1120px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1100" rules="all" id="" >
                <?
					$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
					$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
					foreach($buyer_array as $buyer_id=>$value)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="80"><? echo $buyer_short_library[$buyer_id]; ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]);?></td>
                            <td width="80" align="right"><? echo number_format($value["poVal"],2);?></td>
                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
                        </tr>	
                        <?		
                            $total_po_quantity+=$value["poQty"];
                            $total_po_value+=$value["poVal"];
                            $total_cut+=$value["cQty"];
                            $total_print_re+=$value["prQty"];
                            $total_sew_input+=$value["sQty"];
                            $total_sew_out+=$value["soQty"];
                            $total_iron+=$value["iQty"];
                            $total_re_iron+=$value["reiQty"];
                            $total_finish+=$value["fQty"];
                            $total_ex_factory+=$value["ex"];
                           
                        $i++;
                    }//end foreach 1st
                    
                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                ?>
                </table>
                <table border="1" class="tbl_bottom"  width="1100" rules="all" id="" >
                    <tr> 
                        <td width="30">&nbsp;</td> 
                        <td width="80" align="right">Total</td> 
                        <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?></td> 
                        <td width="80" id="tot_po_value"><? echo number_format($total_po_value,2); ?></td> 
                        <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
                        <td width="80" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
                        <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
                        <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                        <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                        <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
                        <td width="80"><? echo number_format($total_finish_gd_status,2); ?></td >
                        <td width="80"><? echo number_format($total_ex_factory); ?></td >
                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                        <td width=""><? echo number_format($total_ex_status,2); ?></td>
                    </tr>
                 </table>
                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
            </div>
        </div>
        <div style="float:left; width:600px">   
            <table>
                <tr>
                    <td height="21" width="600"><div id="chartdiv"> </div></td>
                </tr>    
            </table>
        </div> 
        <div style="clear:both"></div>
        <table width="2350" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <tr>
                    <th width="30">SL</th>    
                    <th width="130">Order Number</th>
                    <th width="60">Buyer Name</th>
                    <th width="80">Job Number</th>
                    <th width="120">Style Name</th>
                    <th width="130">Item Name</th>
                    <th width="80">Country</th>
                    <th width="80">Order Qty.</th>
                    <th width="80">Ship Date</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="80">Delay</th>
                    <th width="80">Stan. Exc. Cut %</th>
                    <th width="80">Total Cut Qty</th>
                    <th width="80">Actual Exc. Cut %</th>
                    <th width="80">Total Embl. Issue Qty</th>
                    <th width="80">Total Embl. Rcv. Qty</th>
                    <th width="80">Total Sew Input Qty</th>
                    <th width="80">Total Sew Output Qty</th>
                    <th width="80">Total Iron Qty</th>
                    <th width="80">Total Re-Iron Qty</th>
                    <th width="80">Total Finish Qty</th>
                    <th width="80">Fin Goods Status</th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Total Out</th>
                    <th width="80">Shortage/ Excess</th>
                    <th width="85">Status</th>
                    <th>Remarks</th>
                 </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:2370px" id="scroll_body">
            <table border="1" class="rpt_table" width="2350" rules="all" id="table_body">
				<? echo $html; ?>  
            </table>	
            <table border="1" class="tbl_bottom" width="2350" rules="all" id="report_table_footer_1" >
                <tr>
                    <td width="30"></td>
                    <td width="130"></td>
                    <td width="60"></td>
                    <td width="80"></td>
                    <td width="120"></td>
                    <td width="130">Total</td>
                    <td width="80"></td>
                    <td width="80" id="total_order_quantity"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80" id="total_cutting"></td>
                    <td width="80"></td>
                    <td width="80" id="total_emb_issue"></td>
                    <td width="80" id="total_emb_receive"></td>
                    <td width="80" id="total_sewing_input"></td>
                    <td width="80" id="total_sewing_out"></td>
                    <td width="80" id="total_iron_qnty"></td>
                    <td width="80" id="total_re_iron_qnty"></td>
                    <td width="80" id="total_finish_qnty"></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_rej_value_td"></td>
                    <td width="80" id="total_out"></td>
                    <td width="80" id="total_shortage"></td>
                    <td width="85"></td>
                    <td></td>
                 </tr>
			</table>
        </div>
     </div>   
    <?
	}
	else
	{
		
		
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id as po_id, item_number_id, country_id, location, MAX(ex_factory_date) AS ex_factory_date,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id, location");
		foreach($ex_factory_data as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$exRow[csf('location')]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$exRow[csf('po_id')]][$exRow[csf('item_number_id')]][$exRow[csf('country_id')]][$exRow[csf('location')]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		//print_r($ex_factory_arr[3834]);die;
		
		if($db_type==0)
		{
			$prod_sql="SELECT c.po_break_down_id, c.item_number_id, country_id, location, floor_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				IFNULL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				IFNULL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 group by c.po_break_down_id, c.item_number_id, country_id, location, floor_id";
		}
		else
		{
			$prod_sql= "SELECT c.po_break_down_id, c.item_number_id, country_id, location, floor_id,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_quantity ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' THEN c.production_quantity ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS print,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS emb,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS wash,
				NVL(sum(CASE WHEN c.production_type ='2' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS special,
				NVL(sum(CASE WHEN c.production_type ='3' THEN c.production_quantity ELSE 0 END),0) AS printreceived_qnty, 
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=1 THEN c.production_quantity ELSE 0 END),0) AS printr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=2 THEN c.production_quantity ELSE 0 END),0) AS embr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=3 THEN c.production_quantity ELSE 0 END),0) AS washr,
				NVL(sum(CASE WHEN c.production_type ='3' and embel_name=4 THEN c.production_quantity ELSE 0 END),0) AS specialr,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_quantity ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_quantity ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_quantity ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.re_production_qty ELSE 0 END),0) AS re_iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_quantity ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.reject_qnty ELSE 0 END),0) AS cutting_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.reject_qnty ELSE 0 END),0) AS finish_rej_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.reject_qnty ELSE 0 END),0) AS sewingout_rej_qnty
			from 
				pro_garments_production_mst c
			where  
				c.status_active=1 and c.is_deleted=0 and c.po_break_down_id=3884 group by c.po_break_down_id, c.item_number_id, country_id, location, floor_id";
		}
		
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][1]['cQty']=$gmtsRow[csf('cutting_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['pQty']=$gmtsRow[csf('printing_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['print']=$gmtsRow[csf('print')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['emb']=$gmtsRow[csf('emb')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['wash']=$gmtsRow[csf('wash')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][2]['special']=$gmtsRow[csf('special')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['prQty']=$gmtsRow[csf('printreceived_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['print']=$gmtsRow[csf('printr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['emb']=$gmtsRow[csf('embr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['wash']=$gmtsRow[csf('washr')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][3]['special']=$gmtsRow[csf('specialr')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][4]['sQty']=$gmtsRow[csf('sewingin_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][5]['soQty']=$gmtsRow[csf('sewingout_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][7]['iQty']=$gmtsRow[csf('iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][7]['riQty']=$gmtsRow[csf('re_iron_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][8]['fQty']=$gmtsRow[csf('finish_qnty')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][1]['crQty']=$gmtsRow[csf('cutting_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][5]['sorQty']=$gmtsRow[csf('sewingout_rej_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('item_number_id')]][$gmtsRow[csf('country_id')]][$gmtsRow[csf('location')]][$gmtsRow[csf('floor_id')]][8]['frQty']=$gmtsRow[csf('finish_rej_qnty')];
		}
		
		$po_country_arr=array(); $po_country_data_arr=array();
		$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(order_total) as value from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
		foreach($poCountryData as $row)
		{
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['qnty']=$row[csf('qnty')];
			$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['value']=$row[csf('value')];
			$po_country_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].",";
		}
		
		$buyer_array=array(); 
			/*	$order_sql="select a.id, b.job_no_prefix_num, a.po_number ,sum(order_quantity) as qnty, sum(order_total) as value,c.country_id,  a.unit_price, a.po_total_price, a.job_no_mst, c.country_ship_date as shipment_date, a.shiping_status, a.excess_cut, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no from wo_po_details_master b,wo_po_break_down a,wo_po_color_size_breakdown c where a.job_no_mst=b.job_no and b.job_no=c.job_no_mst and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and a.po_number like '$search_string' and b.status_active=1 and b.is_deleted=0 $txt_country_date $company_name $buyer_name $garmentsNature  group by c.country_ship_date,c.country_id,a.id, b.job_no_prefix_num, a.po_number,  a.unit_price, a.po_total_price, a.job_no_mst,a.shiping_status, a.excess_cut, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no order by c.country_ship_date, a.id";*/
		
		
		$order_sql="select a.id, b.job_no_prefix_num, a.po_number, a.po_quantity, a.unit_price, a.po_total_price, a.job_no_mst, a.shiping_status,a.excess_cut,a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, c.location, c.floor_id,d.country_ship_date as shipment_date from wo_po_details_master b,wo_po_color_size_breakdown d,wo_po_break_down a left join pro_garments_production_mst c on c.po_break_down_id=a.id and c.status_active=1 and c.is_deleted=0 $location $floor where a.job_no_mst=b.job_no and b.job_no=d.job_no_mst and a.id=d.po_break_down_id and a.status_active=1 and b.status_active=1 and a.po_number like '$search_string' $txt_country_location_date $company_name $buyer_name $garmentsNature group by a.id, b.job_no_prefix_num, a.po_number, a.po_quantity, a.job_no_mst, d.country_ship_date, a.shiping_status, a.excess_cut, a.unit_price, a.po_total_price, a.plan_cut, b.company_name, b.buyer_name, b.set_break_down, b.style_ref_no, c.location, c.floor_id order by a.id, c.location, c.floor_id";
		//echo $order_sql;
		$result=sql_select($order_sql);
		$i=0; $k=0; $date=date("Y-m-d"); 
		foreach($result as $orderRes)
		{ 
		   	$i++;
		   	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		   	$setArr = explode("__",$orderRes[csf("set_break_down")] );
		   	$countArr = count($setArr); 
		   	if($countArr==0) $countArr=1; $s=0;
		   	for($j=0;$j<$countArr;$j++)
		   	{
			   	$company_name=$orderRes[csf("company_name")];
				$po_id=$orderRes[csf("id")];
				$location=$orderRes[csf("location")];
				$floor_id=$orderRes[csf("floor_id")];
			  	$setItemArr = explode("_",$setArr[$j]);
			   	$item_id=$setItemArr[0];
			   	$set_qnty=$setItemArr[1];
			   	if($item_id>0)
			   	{
					$country=array_unique(explode(",",substr($po_country_arr[$orderRes[csf("id")]],0,-1)));
					foreach($country as $country_id)
					{
						$k++;
						$po_quantity_in_pcs = $po_country_data_arr[$orderRes[csf("id")]][$item_id][$country_id]['qnty'];
						$po_value = $po_country_data_arr[$orderRes[csf("id")]][$item_id][$country_id]['value'];
						$unit_price=$orderRes[csf("unit_price")]/$set_qnty;
						$ex_factory_date=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$country_id][$location]['date'];
						$ex_factory_qnty=$ex_factory_arr[$orderRes[csf("id")]][$item_id][$country_id][$location]['qty'];
						
						$color=""; $days_remian="";
						if($orderRes[csf("shiping_status")]==1 || $orderRes[csf("shiping_status")]==2)
						{
							$days_remian=datediff("d",$date,$orderRes[csf("shipment_date")]); 
							if($orderRes[csf("shipment_date")] > $date) 
							{
								$color="";
							}
							else if($orderRes[csf("shipment_date")] < $date) 
							{
								$color="red";
							}														
							else if($orderRes[csf("shipment_date")] >= $date && $days_remian<=5 ) 
							{
								$color="orange";
							}
						} 
						else if($orderRes[csf("shiping_status")]==3)
						{
							$days_remian=datediff("d",$ex_factory_date,$orderRes[csf("shipment_date")]);
							if($orderRes[csf("shipment_date")] >= $ex_factory_date) 
							{ 
								$color="green";
							}
							else if($orderRes[csf("shipment_date")] < $ex_factory_date) 
							{ 
								$color="#2A9FFF";
							}
							
						}//end if condition
						
						$cutting_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][1]['cQty'];
						$embl_recv_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['prQty'];
						$sewingin_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][4]['sQty'];
						$sewingout_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][5]['soQty'];
						$iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][7]['iQty'];
						$re_iron_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][7]['riQty'];
						$finish_qnty=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][8]['fQty'];
						
						$buyer_array[$orderRes[csf("buyer_name")]]['poQty']+=$po_quantity_in_pcs;
						$buyer_array[$orderRes[csf("buyer_name")]]['poVal']+=$po_value;
						$buyer_array[$orderRes[csf("buyer_name")]]['ex']+=$ex_factory_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['cQty']+=$cutting_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['prQty']+=$embl_recv_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['sQty']+=$sewingin_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['soQty']+=$sewingout_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['iQty']+=$iron_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['reiQty']+=$re_iron_qnty;
						$buyer_array[$orderRes[csf("buyer_name")]]['fQty']+=$finish_qnty;
						
						$actual_exces_cut = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][1]['cQty'];
						if($actual_exces_cut < $po_quantity_in_pcs) $actual_exces_cut=""; else $actual_exces_cut=number_format( (($actual_exces_cut-$po_quantity_in_pcs)/$po_quantity_in_pcs)*100,2)."%";
	
						$issue_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['print'];
						$issue_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['emb'];
						$issue_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['wash'];
						$issue_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][2]['special'];
						
						$embl_iss_qty=$issue_print+$issue_emb+$issue_wash+$issue_special;
						
						$embl_issue_total="";
						if($issue_print!=0) $embl_issue_total .= 'PR='.$issue_print;
						if($issue_emb!=0) $embl_issue_total .= ', EM='.$issue_emb;
						if($issue_wash!=0) $embl_issue_total .= ', WA='.$issue_wash;
						if($issue_special!=0) $embl_issue_total .= ', SP='.$issue_special;
	
						$rcv_print = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['print'];
						$rcv_emb = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['emb'];
						$rcv_wash = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['wash'];
						$rcv_special = $gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][3]['special'];
						
						$embl_receive_total="";	
						if($rcv_print!=0) $embl_receive_total .= 'PR='.$rcv_print;
						if($rcv_emb!=0) $embl_receive_total .= ', EM='.$rcv_emb;
						if($rcv_wash!=0) $embl_receive_total .= ', WA='.$rcv_wash;
						if($rcv_special!=0) $embl_receive_total .= ', SP='.$rcv_special;
						
						$embl_recv_qty=$rcv_print+$rcv_emb+$rcv_wash+$rcv_special;
						
						//$rej_value=$proRes[csf("finish_rej_qnty")]+$proRes[csf("sewingout_rej_qnty")];	
						$rej_value=$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][1]['crQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][5]['sorQty']+$gmts_prod_arr[$orderRes[csf("id")]][$item_id][$country_id][$location][$floor_id][8]['frQty'];
						$shortage = $po_quantity_in_pcs-$ex_factory_qnty;
						$finish_status = $finish_qnty*100/$po_quantity_in_pcs;
						
						if($s==0) 
						{
							$display_font_color="";
							$font_end="";
						}
						else 
						{
							$display_font_color="&nbsp;<font style='display:none'>";
							$font_end="</font>";
						}

						if(($ex_factory_date=="" || $ex_factory_date=="0000-00-00")) $ex_factory_date="&nbsp;"; else $ex_factory_date=change_date_format($ex_factory_date);
						if($orderRes[csf("shiping_status")]==3) $days_remian=$days_remian; else $days_remian="---";
						if(round($actual_exces_cut) > round($orderRes[csf("excess_cut")])) $excess_bgcolor="bgcolor='#FF0000'"; else $excess_bgcolor="";
						
						$total_rej_value+=$rej_value; 
						 
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_2nd$k','$bgcolor') id=tr_2nd$k>";
						$html.='<td width="30">'.$display_font_color.$i.$font_end.'</td>
									<td width="130"><p>'.$display_font_color.$orderRes[csf("po_number")].$font_end.'</p></td>
									<td width="60"><p>'.$display_font_color.$buyer_short_library[$orderRes[csf("buyer_name")]].$font_end.'</p></td>
									<td width="80" align="center"><p>'.$display_font_color.$orderRes[csf("job_no_prefix_num")].$font_end.'</p></td>
									<td width="120"><p>'.$display_font_color.$orderRes[csf("style_ref_no")].$font_end.'</p></td>
									<td width="130"><p>'.$garments_item[$item_id].'</p></td>
									<td width="80"><p>'.$country_library[$country_id].'&nbsp;</p></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage_order('.$po_id.",".$company_name.",".$item_id.",$country_id,'OrderPopupCountry'".')">'.$po_quantity_in_pcs.'</a></td>
									<td width="80" align="center" bgcolor="'.$color.'">'.change_date_format($orderRes[csf("shipment_date")]).'&nbsp;</td>
									<td width="80"><p>'.$location_library[$orderRes[csf("location")]].'&nbsp;</p></td>
                                	<td width="80"><p>'.$floor_library[$orderRes[csf("floor_id")]].'&nbsp;</p></td>
									<td width="80" align="right"><a href="##" onclick="openmypage('.$po_id.",".$item_id.",'exfactoryCountry','$location','','','$country_id'".')">'.$ex_factory_date.'</a></td>
									<td width="80" align="center">'.$days_remian.'&nbsp;</td>
									<td width="80" align="right">&nbsp;'.number_format($orderRes[csf("excess_cut")],2)." %".'</td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'1','$location','$floor_id','$type','$country_id'".')">'.$cutting_qnty.'</a></td>
									<td width="80" align="right" '.$excess_bgcolor.'>&nbsp;'.$actual_exces_cut.'</td>
									<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_iss_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'2','$location','$floor_id','$type','$country_id'".')">'.$embl_issue_total.'</a></td>
									<td width="80" align="right"><font color="$bgcolor" style="display:none">'.$embl_recv_qty.'</font>&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'3','$location','$floor_id','$type','$country_id'".')">'.$embl_receive_total.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'4','$location','$floor_id','$type','$country_id'".')">'.$sewingin_qnty.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'5','$location','$floor_id','$type','$country_id'".')">'.$sewingout_qnty.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'7','$location','$floor_id','$type','$country_id'".')">'.$iron_qnty.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'9','$location','$floor_id','$type','$country_id'".')">'.$re_iron_qnty.'</a></td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage('.$po_id.",".$item_id.",'8','$location','$floor_id','$type','$country_id'".')">'.$finish_qnty.'</a></td>
									<td width="80" align="right">&nbsp;'.number_format($finish_status,2)." %".'</td>
									<td width="80" align="right">&nbsp;<a href="##" onclick="openmypage_rej('.$po_id.",".$item_id.",'reject_qty','$location','$floor_id','$type','$country_id'".')">'.$rej_value.'</a></td>
									<td width="80" align="right">&nbsp;'.$ex_factory_qnty.'</td>
									<td width="80" align="right">&nbsp;'.$shortage.'</td>
									<td width="85">'.$shipment_status[$orderRes[csf("shiping_status")]].'</td>
									<td>&nbsp;<a href="##" onclick="openmypage_remark('.$po_id.",".$item_id.",0,'date_wise_production_report'".')">Veiw</a></td>
								</tr>';
						 $s++;		
					}
				}
			} //end for loop
		}// end main foreach 
    ?>
    <div>
    	<table width="1500" cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? echo "Order Country Wise Production Report"; ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $fromDate To $toDate" ;
                        }
                    ?>
                </td>
            </tr>
        </table>
		<div style="float:left; width:1150px">
            <table width="1100" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th colspan="14" >In-House Order Production </th>
                    </tr>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Total Embl. Rcv. Qty</th>
                        <th width="80">Total Sew Input Qty</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Total Re-Iron Qty</th>
                        <th width="80">Total Finish Qty</th>
                        <th width="80">Fin Goods Status</th>
                        <th width="80">Ex-Fac</th>
                        <th>Ex-Fac%</th>
                     </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:1120px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1100" rules="all" id="" >
                <?
					$i=1; $total_po_quantity=0;$total_po_value=0; $total_cut=0; $total_print_iss=0;
					$total_print_re=0; $total_sew_input=0; $total_sew_out=0; $total_iron=0; $total_re_iron=0; $total_finish=0;$total_ex_factory=0;
					foreach($buyer_array as $buyer_id=>$value)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="30"><? echo $i;?></td>
                            <td width="80"><? echo $buyer_short_library[$buyer_id]; ?></td>
                            <td width="80" align="right"><? echo number_format($value["poQty"]);?></td>
                            <td width="80" align="right"><? echo number_format($value["poVal"],2);?></td>
                            <td width="80" align="right"><? echo number_format($value["cQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["prQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["sQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["soQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["iQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["reiQty"]); ?></td>
                            <td width="80" align="right"><? echo number_format($value["fQty"]); ?></td>
                            <? $finish_gd_status = ($value["fQty"]/$value["poQty"])*100; ?>
                            <td width="80" align="right"><? echo number_format($finish_gd_status,2)." %"; ?></td>
                            <td width="80" align="right"><? echo number_format($value["ex"]); ?></td>
                            <? $ex_gd_status = ($value["ex"]/$value["poQty"])*100; ?>
                            <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?></td>
                        </tr>	
                        <?		
                            $total_po_quantity+=$value["poQty"];
                            $total_po_value+=$value["poVal"];
                            $total_cut+=$value["cQty"];
                            $total_print_re+=$value["prQty"];
                            $total_sew_input+=$value["sQty"];
                            $total_sew_out+=$value["soQty"];
                            $total_iron+=$value["iQty"];
                            $total_re_iron+=$value["reiQty"];
                            $total_finish+=$value["fQty"];
                            $total_ex_factory+=$value["ex"];
                           
                        $i++;
                    }//end foreach 1st
                    
                    $chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew In;".$total_sew_input."\n"."Sew Out ;".$total_sew_out."\n"."Iron ;".$total_iron."\n"."Finish ;".$total_finish."\n"."Ex-Fact;".$total_ex_factory."\n";
                ?>
                </table>
                <table border="1" class="tbl_bottom"  width="1100" rules="all" id="" >
                    <tr> 
                        <td width="30">&nbsp;</td> 
                        <td width="80" align="right">Total</td> 
                        <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?></td> 
                        <td width="80" id="tot_po_value"><? echo number_format($total_po_value,2); ?></td> 
                        <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?></td>
                        <td width="80" id="tot_emb_rcv"><? echo number_format($total_print_re); ?></td> 
                        <td width="80" id="tot_sew_in"><? echo number_format($total_sew_input); ?></td> 
                        <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?></td>   
                        <td width="80" id="tot_iron"><? echo number_format($total_iron); ?></td> 
                        <td width="80" id="tot_re_iron"><? echo number_format($total_re_iron); ?></td> 
                        <td width="80" id="tot_finish"><? echo number_format($total_finish); ?></td>
                        <? $total_finish_gd_status = ($total_finish/$total_po_quantity)*100; ?>
                        <td width="80"><? echo number_format($total_finish_gd_status,2); ?></td >
                        <td width="80"><? echo number_format($total_ex_factory); ?></td >
                        <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                        <td width=""><? echo number_format($total_ex_status,2); ?></td>
                    </tr>
                 </table>
                 <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
            </div>
        </div>
        <div style="float:left; width:600px">   
            <table>
                <tr>
                    <td height="21" width="600"><div id="chartdiv"> </div></td>
                </tr>    
            </table>
        </div> 
        <div style="clear:both"></div>
        <table width="2480" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <tr>
                    <th width="30">SL</th>    
                    <th width="130">Order Number</th>
                    <th width="60">Buyer Name</th>
                    <th width="80">Job Number</th>
                    <th width="120">Style Name</th>
                    <th width="130">Item Name</th>
                    <th width="80">Country</th>
                    <th width="80">Order Qty.</th>
                    <th width="80">Ship Date</th>
                    <th width="80">Location</th>
					<th width="80">Floor</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="80">Delay</th>
                    <th width="80">Stan. Exc. Cut %</th>
                    <th width="80">Total Cut Qty</th>
                    <th width="80">Actual Exc. Cut %</th>
                    <th width="80">Total Embl. Issue Qty</th>
                    <th width="80">Total Embl. Rcv. Qty</th>
                    <th width="80">Total Sew Input Qty</th>
                    <th width="80">Total Sew Output Qty</th>
                    <th width="80">Total Iron Qty</th>
                    <th width="80">Total Re-Iron Qty</th>
                    <th width="80">Total Finish Qty</th>
                    <th width="80">Fin Goods Status</th>
                    <th width="80">Reject Qty</th>
                    <th width="80">Total Out</th>
                    <th width="80">Shortage/ Excess</th>
                    <th width="85">Status</th>
                    <th>Remarks</th>
                 </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:2500px" id="scroll_body">
            <table border="1" class="rpt_table" width="2480" rules="all" id="table_body">
				<? echo $html; ?>  
            </table>	
            <table border="1" class="tbl_bottom" width="2480" rules="all" id="report_table_footer_1" >
                <tr>
                    <td width="30"></td>
                    <td width="130"></td>
                    <td width="60"></td>
                    <td width="80"></td>
                    <td width="120"></td>
                    <td width="130">Total</td>
                    <td width="80"></td>
                    <td width="80" id="total_order_quantity"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="80" id="total_cutting"></td>
                    <td width="80"></td>
                    <td width="80" id="total_emb_issue"></td>
                    <td width="80" id="total_emb_receive"></td>
                    <td width="80" id="total_sewing_input"></td>
                    <td width="80" id="total_sewing_out"></td>
                    <td width="80" id="total_iron_qnty"></td>
                    <td width="80" id="total_re_iron_qnty"></td>
                    <td width="80" id="total_finish_qnty"></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_rej_value_td"></td>
                    <td width="80" id="total_out"></td>
                    <td width="80" id="total_shortage"></td>
                    <td width="85"></td>
                    <td></td>
                 </tr>
			</table>
        </div>
	</div>   
	<?
	}

	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	echo "$html";
	exit();	
}
//-------------------------------------------END Show Date Wise------------------------
//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
//-------------------------------------------end-----------------------------------------------------------------------------//
		
if($action=='date_wise_production_report') 
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
	?>
    <div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
            <? 
                 
                 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='1' and is_deleted=0 and status_active=1";
                 //echo $sql;
                 echo  create_list_view ( "list_view_1", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
                
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='2' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_2", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='3' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_3", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='4' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_4", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset>
        <legend style="width:480px">Sewing Output</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='5' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_5", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Finish Input</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='6' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_6", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Finish Output</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='8' and is_deleted=0 and status_active=1";
                 
                  echo  create_list_view ( "list_view_7", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
	</div>  
<?
exit();
}//end if 


if($action=='date_wise_production_report_country') 
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 ?>
 	<div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
            <? 
                 
                 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id  and country_id='$country_id' and production_type='1' and is_deleted=0 and status_active=1";
                 //echo $sql;
                 echo  create_list_view ( "list_view_1", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
                
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='2' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_2", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='3' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_3", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='4' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_4", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Sewing Output</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='5' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_5", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Finish Input</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='6' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_6", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Finish Output</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and production_type='8' and is_deleted=0 and status_active=1";
                 
                  echo  create_list_view ( "list_view_7", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');
            ?>
        </fieldset>
	</div>
<?
exit();
}//end if 

  
if ($action=='OrderPopup')
{
	echo load_html_head_contents("Order Wise Production Report", "../../../", 1, 1,$unicode,'','');
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$company_name=str_replace("'","",$_REQUEST['company_name']);
	$color_variable_setting=return_field_value("ex_factory","variable_settings_production","company_name='$company_name' and variable_list=1 and status_active=1 and is_deleted=0","ex_factory");
	$ex_fact_qty_arr=array();
	if($color_variable_setting==2 || $color_variable_setting==3)
	{
		$sql_exfect="select c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_break_down_id) and a.status_active=1 and a.is_deleted=0 group by  c.color_number_id, c.size_number_id";
		$sql_result_exfact=sql_select($sql_exfect);
		foreach($sql_result_exfact as $row)
		{
			$ex_fact_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]=$row[csf("production_qnty")];
		}
	}
	//var_dump($ex_fact_qty_arr);
	
?>
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 </div>
    
<div style="width:700px" align="center" id="details_reports"> 
 	<!--<fieldset style="width:700px">-->
    <span style="font-size:18px; font-weight:bold">Color And Size Wise Summary</span><br />
  	<!--<legend>Color And Size Wise Summary</legend>-->
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="100">Buyer</th>
                <th width="100">Job Number</th>
                <th width="100">Style Name</th>
                <th width="300">Order Number</th>
                <th width="100">Ship Date</th>
                <th width="100">Item Name</th>
                <th width="100">Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;die;
			$resultRow=sql_select($sql);
				
			$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
			
 		?> 
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
         <?
         $prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
		 foreach($prod_sewing_sql as $sewingRow);
		?> 	
        <tr>
        	<td colspan="2">Total Alter Sewing Qty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
        	<td colspan="2">Total Reject Sewing Qty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
            <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
        </tr>
    </table>
    
    <?
				  
	  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	  $color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
	  
	  $color_library=sql_select("select distinct(color_number_id) as color_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1 ");
	  $size_library=sql_select("select distinct(size_number_id) as size_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_mst_id!=0 and status_active=1");
	  $count = count($size_library);	
	  $width= $count*70+350; 		
	  	  
	?>
    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
	 	<thead>
        	<tr>
            	<th width="100">Color Name</th>
                <th width="170">Production Type</th>
 				<?
				foreach($size_library as $sizeRes)
				{
				 	?><th width="80"><? echo $size_Arr_library[$sizeRes[csf("size_number_id")]]; ?></th><?
				}
				?>
     		    <th width="60">Total</th>
           </tr>
        </thead>
        <?
		  
		  foreach($color_library as $colorRes)
		  {
			  if($color_variable_setting==2 || $color_variable_setting==3) $row_span=17; else $row_span=16;  
			?>	  
			<tr>
				<td rowspan="<? echo $row_span; ?>"><? echo $color_Arr_library[$colorRes[csf("color_number_id")]]; ?></td>
			
 			<?
            	  $i=0;$j=0;$sqlPart="";
				  foreach($size_library as $sizeRes)
				  {
					  $i++;$j++;
					  if($i>1) $sqlPart .=",";
					  $sqlPart .= 'SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN order_quantity ELSE 0 END ) as '."col".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as '."pcut".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN excess_cut_perc ELSE 0 END ) as '."excess_cut".$i;
				  }
				  if($j>1)
				  {
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN order_quantity ELSE 0 END ) as totalorderqnty';
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as totalplancutqnty';
				  }
				$sql = sql_select("select avg(excess_cut_perc) as avg_excess_cut_perc,max(excess_cut_perc) as excess_cut_perc,". $sqlPart ." from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id");
				//echo $sql;die;
				foreach($sql as $resRow); 
 					$bgcolor1="#E9F3FF"; 
					$bgcolor2="#FFFFFF";
				?>
					</tr>
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><b>Order Quantity</b></td>	
                        <? for($k=1;$k<=$i;$k++) {	$col = 'col'.$k; ?>	
                         	<td><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalorderqnty")]; ?></td> 
					</tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Plan To Cut (AVG <? echo number_format($resRow[csf("avg_excess_cut_perc")],2); ?>)% </b></td>	
                        <? for($k=1;$k<=$i;$k++){ $col = 'pcut'.$k;$excess_cut = 'excess_cut'.$k;	?>	
                         	<td title="Excess Cut <? echo $resRow[csf($excess_cut)]; ?>%"><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalplancutqnty")]; ?></td> 
                    </tr>
					
                <?
 				$total_cutting=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0; $total_exfact_qnty=0;
				$total_print_issue=0;$total_print_rcv=0;$total_embro_issue=0;$total_embro_rcv=0; $total_sp_issue=0;$total_sp_rcv=0; $total_wash_issue=0;$total_wash_rcv=0;
				$cutting_html='';$sewin_html='';$sewout_html='';$finisin_html='';$finisout_html='';$iron_html=''; $exfact_html='';
				$printiss_html=''; $printrcv_html=''; $embroiss_html=''; $embrorcv_html=''; $spiss_html=''; $sprcv_html=''; $washiss_html=''; $washrcv_html='';
				$k=0;
				foreach($size_library as $sizeRes)
				{ 
					$k++;
					if($db_type==0)
					{
						$prod_sql= sql_select("SELECT  
								IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
							from 
								pro_garments_production_mst a, pro_garments_production_dtls c,wo_po_color_size_breakdown d
							where  
								a.id=c.mst_id and d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");
								/*IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, */
					}
					else
					{
						$prod_sql=sql_select("SELECT  
								NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
								NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
								NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
								NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
								NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
								NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
								NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
								NVL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
								NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
								NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
							from 
								pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d
							where  
								a.id=c.mst_id and d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");	
								/*NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
								NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, */
					}
					//echo $prod_sql;
					foreach($prod_sql as $prodRow);  
					$col = 'col'.$k;
                    if($prodRow[csf("cutting_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("cutting_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("cutting_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
					$cutting_html .='<td '.$bgCol.'>'.$prodRow[csf("cutting_qnty")].'</td>';
                    $total_cutting+=$prodRow[csf("cutting_qnty")];
                 	
					if($cons_embr>0)
					{
						if($prodRow[csf("printing_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printing_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printing_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $printiss_html .='<td '.$bgCol.'>'.$prodRow[csf("printing_qnty")].'</td>';
                    $total_print_issue+=$prodRow[csf("printing_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("printreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $printrcv_html .='<td '.$bgCol.'>'.$prodRow[csf("printreceived_qnty")].'</td>';
                    $total_print_rcv+=$prodRow[csf("printreceived_qnty")];
					
					if($cons_embr>0)
					{
						if($prodRow[csf("emb_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("emb_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("emb_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $embroiss_html .='<td '.$bgCol.'>'.$prodRow[csf("emb_qnty")].'</td>';
                    $total_embro_issue+=$prodRow[csf("emb_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("embreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("embreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("embreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $embrorcv_html .='<td '.$bgCol.'>'.$prodRow[csf("embreceived_qnty")].'</td>';
                    $total_embro_rcv+=$prodRow[csf("embreceived_qnty")];
					
					if($cons_embr>0)
					{
						if($prodRow[csf("sp_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("sp_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("sp_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $spiss_html .='<td '.$bgCol.'>'.$prodRow[csf("sp_qnty")].'</td>';
                    $total_sp_issue+=$prodRow[csf("sp_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("spreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("spreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("spreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $sprcv_html .='<td '.$bgCol.'>'.$prodRow[csf("spreceived_qnty")].'</td>';
                    $total_sp_rcv+=$prodRow[csf("spreceived_qnty")];
					
					if($cons_embr>0)
					{
						if($prodRow[csf("wash_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("wash_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("wash_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $washiss_html .='<td '.$bgCol.'>'.$prodRow[csf("wash_qnty")].'</td>';
                    $total_wash_issue+=$prodRow[csf("wash_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("washreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("washreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("washreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $washrcv_html .='<td '.$bgCol.'>'.$prodRow[csf("washreceived_qnty")].'</td>';
                    $total_wash_rcv+=$prodRow[csf("washreceived_qnty")];
                    
					if($prodRow[csf("sewingin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $sewin_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingin_qnty")].'</td>';
                    $total_sew_in+=$prodRow[csf("sewingin_qnty")];
                    
					if($prodRow[csf("sewingout_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingout_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingout_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
                    $sewout_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingout_qnty")].'</td>';
                    $total_sew_out+=$prodRow[csf("sewingout_qnty")];
                    
					/*if($prodRow[csf("finishin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("finishin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("finishin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisin_html .='<td '.$bgCol.'>'.$prodRow[csf("finishin_qnty")].'</td>';
                    $total_fin_in+=$prodRow[csf("finishin_qnty")];*/
                    
					if($prodRow[csf("finish_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("finish_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("finish_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisout_html .='<td '.$bgCol.'>'.$prodRow[csf("finish_qnty")].'</td>';
                    $total_fin_out+=$prodRow[csf("finish_qnty")];
					
					if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $iron_html .='<td '.$bgCol.'>'.$prodRow[csf("iron_qnty")].'</td>';
                    $total_iron_out+=$prodRow[csf("iron_qnty")];
					
					//if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					//else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					//else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					if($color_variable_setting==2 || $color_variable_setting==3)
					{ 
						$bgCol=="bgcolor='#FFFFFF'";
						$exfact_html.='<td>'.$ex_fact_qty_arr[$colorRes[csf("color_number_id")]][$sizeRes[csf("size_number_id")]].'&nbsp;</td>';
						
						$total_exfact_qnty+=$ex_fact_qty_arr[$colorRes[csf("color_number_id")]][$sizeRes[csf("size_number_id")]];
					}
					
 				 
				}// end size foreach loop	
				
				?>
					<tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Cutting</b></td>
                        <? echo $cutting_html; ?> 
                        <td><? echo $total_cutting; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Print Issue</b></td>
                        <? echo $printiss_html; ?> 
                        <td><? echo $total_print_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Print Received</b></td>
                        <? echo $printrcv_html; ?> 
                        <td><? echo $total_print_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Embro Issue</b></td>
                        <? echo $embroiss_html; ?> 
                        <td><? echo $total_embro_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Embro Received</b></td>
                        <? echo $embrorcv_html; ?> 
                        <td><? echo $total_embro_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Issue For Special Works</b></td>
                        <? echo $spiss_html; ?> 
                        <td><? echo $total_sp_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Recv. From Special Works</b></td>
                        <? echo $sprcv_html; ?> 
                        <td><? echo $total_sp_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Sewing Input</b></td>
                       <? echo $sewin_html; ?> 
                        <td><? echo $total_sew_in; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Sewing Output</b></td>
                        <? echo $sewout_html; ?> 
                        <td><? echo $total_sew_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Issue For Wash</b></td>
                        <? echo $washiss_html; ?> 
                        <td><? echo $total_wash_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Recv. From Wash</b></td>
                        <? echo $washrcv_html; ?> 
                        <td><? echo $total_wash_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Iron Output</b></td>
                        <? echo $iron_html; ?> 
                        <td><? echo $total_iron_out; ?></td> 
                    </tr>
                   <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Finishing Output</b></td>
                       <? echo $finisout_html; ?> 
                        <td><? echo $total_fin_out; ?></td> 
                    </tr>
                    <? 
					if($color_variable_setting==2 || $color_variable_setting==3)
					{
						?>
						<tr>
							<td><b>Ex-Factory Qty.</b></td>
							 <? echo $exfact_html; ?> 
							<td><? echo $total_exfact_qnty; ?>&nbsp;</td> 
						</tr>
						<?
					}
					?>
			<?	
			}// end color foreach loop
			?>
           
		 
 </table>
<!--</fieldset>-->
    
</div>    


<?
exit();

}// end if condition

if ($action=='OrderPopupCountry')
{
	echo load_html_head_contents("Order Wise Production Report", "../../../", 1, 1,$unicode,'','');
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$country_id=$_REQUEST['country_id'];
	
?>

	
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 </div>
  
<div style="width:700px" align="center" id="details_reports"> 
 	<fieldset style="width:700px">
  	<legend>Color And Size Wise Summary</legend>
    <table id="tbl_id" class="rpt_table" width="680" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="100">Buyer</th>
                <th width="100">Job Number</th>
                <th width="100">Style Name</th>
                <th width="100">Order Number</th>
                <th width="100">Ship Date</th>
                <th width="100">Item Name</th>
                <th width="100">Order Qnty.</th>
                <th>Country</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
 			$sql = sql_select("select a.job_no_mst,a.po_number,a.pub_shipment_date,a.po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio 
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c 
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			//echo $sql;
			foreach($sql as $resultRow);
			
			$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
			
			$po_qnty=return_field_value("sum(order_quantity) as qnty","wo_po_color_size_breakdown","po_break_down_id in ($po_break_down_id) and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0","qnty");	
 		?> 
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[csf("style_ref_no")]; ?></p></td>
            <td><? echo $resultRow[csf("po_number")]; ?></td>
            <td><? echo change_date_format($resultRow[csf("pub_shipment_date")]); ?></td>
             <td><p><? echo $garments_item[$item_id]; ?></p></td>
            <td><? echo $po_qnty; ?></td>
            <td><? echo $country_library[$country_id]; ?></td>
        </tr>
         <?
         $prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and is_deleted=0 and status_active=1");
		 foreach($prod_sewing_sql as $sewingRow);
		?> 	
        <tr>
        	<td colspan="2">Total Alter Sewing Qnty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
        	<td colspan="2">Total Reject Sewing Qnty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
            <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
        </tr>
    </table>
	</fieldset>
    
    <?
				  
	  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	  $color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
	  
	  $color_library=sql_select("select distinct(color_number_id) from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1 ");
	  $size_library=sql_select("select distinct(size_number_id) from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_mst_id!=0 and status_active=1");
	  $count = count($size_library);	
	  $width= $count*70+350; 		
	?>
    <fieldset style="width:700px">
    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
	 	<thead>
        	<tr>
            	<th width="100">Color Name</th>
                <th width="200">Production Type</th>
 				<?
				foreach($size_library as $sizeRes)
				{
				 	?><th width="80"><? echo $size_Arr_library[$sizeRes[csf("size_number_id")]]; ?></th><?
				}
				?>
     		    <th width="60">Total</th>
           </tr>
        </thead>
        <?
		  
		  foreach($color_library as $colorRes)
		  {
			?>	  
			<tr>
				<td rowspan="10"><? echo $color_Arr_library[$colorRes[csf("color_number_id")]]; ?></td>
			
 			<?
            	  $i=0;$j=0;$sqlPart="";
				  foreach($size_library as $sizeRes)
				  {
					  $i++;$j++;
					  if($i>1) $sqlPart .=",";
					  $sqlPart .= 'SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN order_quantity ELSE 0 END ) as '."col".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as '."pcut".$i;
					  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' and size_number_id='.$sizeRes[csf("size_number_id")].' THEN excess_cut_perc ELSE 0 END ) as '."excess_cut".$i;
				  }
				  if($j>1)
				  {
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN order_quantity ELSE 0 END ) as totalorderqnty';
					 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes[csf("color_number_id")].' THEN plan_cut_qnty ELSE 0 END ) as totalplancutqnty';
				  }
		  
				$sql = sql_select("select avg(excess_cut_perc) as avg_excess_cut_perc,excess_cut_perc,". $sqlPart ." from wo_po_color_size_breakdown where status_active=1 and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id'");
				//echo $sql;die;
				foreach($sql as $resRow); 
 					$bgcolor1="#E9F3FF"; 
					$bgcolor2="#FFFFFF";
				?>
					 
					<tr bgcolor="<? echo $bgcolor1; ?>">
						<td><b>Order Quantity</b></td>	
                        <? for($k=1;$k<=$i;$k++) {	$col = 'col'.$k; ?>	
                         	<td><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalorderqnty")]; ?></td> 
					</tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Plan To Cut (AVG <? echo $resRow[csf("avg_excess_cut_perc")]; ?>)% </b></td>	
                        <? for($k=1;$k<=$i;$k++){ $col = 'pcut'.$k;$excess_cut = 'excess_cut'.$k;	?>	
                         	<td title="Excess Cut <? echo $resRow[csf($excess_cut)]; ?>%"><? echo $resRow[csf($col)]; ?></td>
						<? } ?>
                         <td><? echo $resRow[csf("totalplancutqnty")]; ?></td> 
                    </tr>
					
                <?
 				$total_cutting=0;$total_emb_issue=0;$total_emb_rcv=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0;
				$cutting_html='';$embiss_html='';$embrcv_html='';$sewin_html='';$sewout_html='';$finisin_html='';$finisout_html=''; $iron_html='';
				$k=0;
				foreach($size_library as $sizeRes)
				{
					$k++;
					if($db_type==0)
					{
						$prod_sql= sql_select("SELECT  
								IFNULL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS sewingin_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='5' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
								IFNULL(sum(CASE WHEN c.production_type ='6' THEN  c.production_qnty  ELSE 0 END),0) AS finishin_qnty, 
								IFNULL(sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END),0) AS iron_qnty,
								
								IFNULL(sum(CASE WHEN c.production_type ='8' THEN  c.production_qnty  ELSE 0 END),0) AS finish_qnty 
							from 
								pro_garments_production_dtls c,wo_po_color_size_breakdown d
							where  
								d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.country_id='$country_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");
					}
					else
					{
						$prod_sql= sql_select("SELECT  
							NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
							NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
							NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, 
							NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS sewingin_qnty,
							NVL(sum(CASE WHEN c.production_type ='5' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
							NVL(sum(CASE WHEN c.production_type ='6' THEN  c.production_qnty  ELSE 0 END),0) AS finishin_qnty, 
							NVL(sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END),0) AS iron_qnty,
							NVL(sum(CASE WHEN c.production_type ='8' THEN  c.production_qnty  ELSE 0 END),0) AS finish_qnty 
						from 
							pro_garments_production_dtls c,wo_po_color_size_breakdown d
						where  
							d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.country_id='$country_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");	
					}
					
					foreach($prod_sql as $prodRow);  
					$col = 'col'.$k;
                    if($prodRow[csf("cutting_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("cutting_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("cutting_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
					$cutting_html .='<td '.$bgCol.'>'.$prodRow[csf("cutting_qnty")].'</td>';
                    $total_cutting+=$prodRow[csf("cutting_qnty")];
                 	
					if($cons_embr>0)
					{
						if($prodRow[csf("printing_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printing_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printing_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
                    $embiss_html .='<td '.$bgCol.'>'.$prodRow[csf("printing_qnty")].'</td>';
                    $total_emb_issue+=$prodRow[csf("printing_qnty")];
                    
					if($cons_embr>0)
					{
						if($prodRow[csf("printreceived_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("printreceived_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("printreceived_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
					}
					else $bgCol='';
					
                    $embrcv_html .='<td '.$bgCol.'>'.$prodRow[csf("printreceived_qnty")].'</td>';
                    $total_emb_rcv+=$prodRow[csf("printreceived_qnty")];
                    
					if($prodRow[csf("sewingin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $sewin_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingin_qnty")].'</td>';
                    $total_sew_in+=$prodRow[csf("sewingin_qnty")];
                    
					if($prodRow[csf("sewingout_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("sewingout_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("sewingout_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
                    $sewout_html .='<td '.$bgCol.'>'.$prodRow[csf("sewingout_qnty")].'</td>';
                    $total_sew_out+=$prodRow[csf("sewingout_qnty")];
                    
					if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $iron_html .='<td '.$bgCol.'>'.$prodRow[csf("iron_qnty")].'</td>';
                    $total_iron_out+=$prodRow[csf("iron_qnty")];
					
				
					if($prodRow[csf("finish_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
					else if($prodRow[csf("finish_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
					else if($prodRow[csf("finish_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
                    $finisout_html .='<td '.$bgCol.'>'.$prodRow[csf("finish_qnty")].'</td>';
                    $total_fin_out+=$prodRow[csf("finish_qnty")];
					
					
                    
				}// end size foreach loop	
				
				?>
					<tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Cutting</b></td>
                        <? echo $cutting_html; ?> 
                        <td><? echo $total_cutting; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Print/Embro Issue</b></td>
                        <? echo $embiss_html; ?> 
                        <td><? echo $total_emb_issue; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Print/Embro Received</b></td>
                        <? echo $embrcv_html; ?> 
                        <td><? echo $total_emb_rcv; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Sewing Input</b></td>
                       <? echo $sewin_html; ?> 
                        <td><? echo $total_sew_in; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Sewing Output</b></td>
                        <? echo $sewout_html; ?> 
                        <td><? echo $total_sew_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                    	<td><b>Iron Output</b></td>
                        <? echo $iron_html; ?> 
                        <td><? echo  $total_iron_out; ?></td> 
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                    	<td><b>Finishing Output</b></td>
                       <? echo $finisout_html; ?> 
                        <td><? echo $total_fin_out; ?></td> 
                    </tr> 
			<?	
			}// end color foreach loop
			?>
           
		 
 </table>
</fieldset>
    
</div>    


<?
exit();

}// end if condition


//cutting-1,sewing ouput-5--------------------popup-----------//
if ($action==1 || $action==5) 
{
	 
 	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
 	?>
    <script>
		function openmypage(po_break_down_id,item_id,prod_type,location_id,floor_id,dateOrLocWise,country_id,prod_date,action)
		{
			var popupWidth = "width=550px,height=320px,";	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'order_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id+'&prod_date='+prod_date+'&prod_type='+prod_type, 'Production Quantity', popupWidth+'center=1,resize=0,scrolling=0','../../');
		}
	</script>
    <fieldset>
    <div style="margin-left:50px">
        <table width="620" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                 <? if($action==1){ ?>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="100">Cutting Date</th>
                        <th width="160">Cutt. Qty(In-house)</th>
                        <th width="160">Cutt. Qty(Out-bound)</th>
                        <th width="">Cutting Company</th>
                 	</tr>
				<? } else if($action==5){ ?>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Output Date</th>
                        <th width="80">PO No</th>
                        <th width="80">Sewing Line</th>
                        <th width="80">Sew.Qty</th>
                        <th width="100">Source</th>
                        <th width="">Sewing Company</th>
                    </tr>
 				<? } ?>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:638px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table" width="620" rules="all" id="table_body" >
            <?
             $total_in_quantity=0;$total_out_quantity=0;
             $i=1;
 			 $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
 			 $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
			 $po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
			 $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
			 $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			
			 $location="";$floor="";
			 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
			 {
				 if($location_id!="") $location=" and location=$location_id";
				 if($floor_id!="") $floor=" and floor_id=$floor_id";
			 }
			 if($action==5)
			 {
				 $sql=sql_select("select po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source,serving_company,
					  SUM(production_quantity) as production_quantity
					  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$action and status_active=1 $location $floor $country_cond group by serving_company,production_date,po_break_down_id,prod_reso_allo,sewing_line,production_source"); 
			 }
			 else
			 {
				 $sql=sql_select("select po_break_down_id,production_date,production_source,serving_company,
					  SUM(CASE WHEN production_source=1 THEN production_quantity ELSE 0 END) as in_house_cut_qnty,
					  SUM(CASE WHEN production_source=3 THEN production_quantity ELSE 0 END) as out_bound_cut_qnty
					  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$action and status_active=1 $location $floor $country_cond group by po_break_down_id,serving_company,production_date,production_source");
			 }
				  
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($action==5)
				 {
					$sewing_line='';
					if($resultRow[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$resultRow[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_library[$val]; else $sewing_line.=",".$sewing_library[$val];
						}
					}
					else $sewing_line=$sewing_library[$resultRow[csf('sewing_line')]];
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="80"><a href="##" onclick="openmypage('<? echo $po_break_down_id ?>','<? echo $item_id ?>','<? echo $action ?>','<? echo $location_id ?>','<? echo $floor_id ?>','<? echo $dateOrLocWise ?>','<? echo $country_id ?>','<? echo $resultRow[csf("production_date")]; ?>','challanPopup')"><? echo change_date_format($resultRow[csf("production_date")]); ?></a></td>
                    <td width="80"><p><? echo $po_array[$resultRow[csf("po_break_down_id")]]; ?></p></td>
                    <td width="80"><? echo $sewing_line; ?></td>
                    <td width="80" align="right"><? echo number_format($resultRow[csf("production_quantity")]); ?>&nbsp;</td>
                    <td width="100"><? echo $knitting_source[$resultRow[csf("production_source")]]; ?></td>
                    <?
                    	$source= $resultRow[csf('production_source')];
					    if($source==3)
						{
							$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
						}
						else
						{
							$serving_company= $company_library[$resultRow[csf('serving_company')]];
						}
					?>
                    <td width=""><p><? echo $serving_company; ?></p></td>
                 </tr>	
                 <?	
				 }
				 else
				 {
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="100"><a href="##" onclick="openmypage('<? echo $po_break_down_id ?>','<? echo $item_id ?>','<? echo $action ?>','<? echo $location_id ?>','<? echo $floor_id ?>','<? echo $dateOrLocWise ?>','<? echo $country_id ?>','<? echo $resultRow[csf("production_date")]; ?>','challanPopup')"><? echo change_date_format($resultRow[csf("production_date")]); ?></a></td>
                    <td width="160" align="right"><? echo number_format($resultRow[csf("in_house_cut_qnty")]); ?></td>
                    <td width="160" align="right"><? echo number_format($resultRow[csf("out_bound_cut_qnty")]); ?></td>
                    <?
                    	$source= $resultRow[csf('production_source')];
					    if($source==3)
						{
							$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
						}
						else
						{
							$serving_company= $company_library[$resultRow[csf('serving_company')]];
						}
					?>
                    <td width=""><p><? echo $serving_company; ?></p></td>
                 </tr>	
                 <?	
				 }
				 	
				$total_sewing_quantity+=$resultRow[csf("production_quantity")];
				$total_in_quantity+=$resultRow[csf("in_house_cut_qnty")];
				$total_out_quantity+=$resultRow[csf("out_bound_cut_qnty")];
				$i++;
			}//end foreach 1st
				
			if($action==5)
			{
			?>
            	<tfoot class="tbl_bottom">
                     <tr> 
                        <td width="30">&nbsp;</td> 
                        <td width="80">&nbsp;</td> 
                        <td width="80">&nbsp;</td> 
                        <td width="80">Total</td> 
                        <td width="80"><? echo number_format($total_sewing_quantity); ?>&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td>&nbsp;</td> 
                     </tr>
                 </tfoot>
			 <?
			 }
			 else
			 {
			?>
            	<tfoot class="tbl_bottom">
                     <tr> 
                        <td width="50">&nbsp;</td> 
                        <td width="100">Total</td> 
                        <td width="160"><? echo number_format($total_in_quantity); ?> </td>
                        <td width="160"><? echo number_format($total_out_quantity); ?></td>
                        <td width="">&nbsp;</td> 
                     </tr>
                 </tfoot>
			 <?
			 }
			 ?>
			</table>
		</div>
	</div>
	</fieldset>
    <?
 exit();
 
}


//---- sewing input-4, iron input-7, finish-8, re_iron input-9-----------popup--------// 
if ($action==4 || $action==7 || $action==8 || $action==9) // popup
{
	 
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
				<? if($action==2){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Print/ Emb. Issue Date</th>
                        <th width="">Print/ Emb. Issue Qnty</th>
                    </tr>
                
				<? } else if($action==3){ ?>
               
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Print/ Emb. Receive Date</th>
                        <th width="">Print/ Emb. Receive Qnty</th>
                    </tr>
                
				<? } else if($action==4){ ?>
                
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="70">Sewing Date</th>
                        <th width="80">PO No</th>
                        <th width="80">Sewing Line</th>
                        <th width="80">Sewing Qty</th>
                        <th width="">Source</th>
                    </tr>
                <? } else if($action==7){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Iron Output Date</th>
                        <th width="">Iron Output Qnty</th>
                    </tr>
                <? } else if($action==8){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Finish Date</th>
                        <th width="">Finish Qty</th>
                    </tr>
                <? } else if($action==9){ ?>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Iron Output Date</th>
                        <th width="">Re-Iron Output Qty</th>
                    </tr>
               <? } ?> 
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body" >
            <?
				$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
				$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
				$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
             $total_quantity=0;
             $i=1;
			 $location="";$floor="";
			 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
			 {
				 if($location_id!="") $location=" and location=$location_id";
				 if($floor_id!="") $floor=" and floor_id=$floor_id";
			 }
			 if ($action==9)
			 {
				 $sql=sql_select("select production_date, sum(re_production_qty) as production_quantity	  
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and re_production_qty!=0 and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=7 $location $floor $country_cond group by production_date");
			 }
			 else if ($action==4)
			 {
				 $sql=sql_select("select po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source,sum(production_quantity) as production_quantity
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=$action $location $floor $country_cond group by po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source");
			 }
			 else
			 {
				 $sql=sql_select("select production_date,sum(production_quantity) as production_quantity
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=$action $location $floor $country_cond group by production_date"); 
			 }
 
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if ($action==4)
				 {
					 $sewing_line='';
					if($resultRow[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$resultRow[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_library[$val]; else $sewing_line.=",".$sewing_library[$val];
						}
					}
					else $sewing_line=$sewing_library[$resultRow[csf('sewing_line')]];
             	?>
                     <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="30"><? echo $i;?></td>
                        <td width="70" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                        <td width="80" align="center"><p><? echo $po_array[$resultRow[csf("po_break_down_id")]]; ?></p></td>
                        <td width="80" align="center"><? echo $sewing_line; ?></td>
                        <td width="80" align="right"><? echo number_format($resultRow[csf("production_quantity")],0); ?>&nbsp;</td>
                        <td><? echo $knitting_source[$resultRow[csf("production_source")]]; ?></td>
                     </tr>	
                 <?	
				 }
				 else
				 {
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("production_quantity")],0); ?>&nbsp;</td>
                 </tr>	
                 <?	
				 }
                    $total_quantity+=$resultRow[csf("production_quantity")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table width="500" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="body_bottom" >
        	<?
			if ($action==4)
			{
			?>
                 <tr> 
                    <td width="30">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">Total</td> 
                    <td width="80"><? echo number_format($total_quantity,0); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                 </tr>
             <?
			}
			else
			{
			?>
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td><? echo number_format($total_quantity,0); ?>&nbsp;</td>
                 </tr>
             <?
			}
			 ?>
         </table>
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 
}



if ($action=='exfactory')  // exfactory date popup
{
	 
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Ex Factory Date</th>
                        <th width="">Ex Factory Qnty</th>
               		</tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="500" rules="all" id="table_body" >
            <?
             $total_quantity=0;
             $sql=sql_select("select sum(ex_factory_qnty) as ex_factory_qnty, ex_factory_date from pro_ex_factory_mst where po_break_down_id in ($po_break_down_id) and item_number_id='$item_id' and status_active=1 and is_deleted=0 group by ex_factory_date"); 
            //echo $sql; 
			$i=1;
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? if($resultRow[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($resultRow[csf("ex_factory_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("ex_factory_qnty")]); ?></td>
                 </tr>	
                 <?		
                    $total_quantity+=$resultRow[csf("ex_factory_qnty")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table cellspacing="0" border="1" class="tbl_bottom"  width="500" rules="all" id="body_bottom" >
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td width=""><? echo number_format($total_quantity); ?></td>
                  </tr>
         </table>
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 
}

if ($action=='exfactoryCountry')  // exfactory date popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($location_id=="") $location_mst=""; else $location_mst=" and location='$location_id'";
	if($floor_id=="") $floor_mst=""; else $floor_mst=" and floor_id='$floor_id'";
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Ex Factory Date</th>
                        <th width="">Ex Factory Qnty</th>
               		</tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="500" rules="all" id="table_body" >
            <?
             $total_quantity=0;
             
             $sql=sql_select("select sum(ex_factory_qnty) as ex_factory_qnty, ex_factory_date 		  
				  from pro_ex_factory_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and country_id='$country_id' and status_active=1 and is_deleted=0 group by ex_factory_date"); 
            //echo $sql; 
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? if($resultRow[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($resultRow[csf("ex_factory_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("ex_factory_qnty")]); ?></td>
                 </tr>	
                 <?		
                    $total_quantity+=$resultRow[csf("ex_factory_qnty")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table cellspacing="0" border="1" class="tbl_bottom"  width="500" rules="all" id="body_bottom" >
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td width=""><? echo number_format($total_quantity); ?></td>
                  </tr>
         </table>
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 
}

//--print/emb issue-2,print/emb receive-3,
if ($action==2 || $action==3)
{
	 
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
 	?>
    <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
          </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 	</div>
    <div id="details_reports">
        <table width="1040" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                   
                 <? if ($action==2) { ?>  
                   <tr>
                        <th width="30" rowspan="2">Sl.</th>    
                        <th width="80" rowspan="2">Date</th>
                        <th colspan="3">Printing Issue</th>
                        <th colspan="3">Embroidery Issue</th>
                        <th colspan="3">Wash Issue</th>
                        <th colspan="3">Special Work Issue</th>
                    </tr> 
                 <? } else {?>
                 	<tr>
                        <th width="30" rowspan="2">Sl.</th>    
                        <th width="70" rowspan="2">Date</th>
                        <th colspan="3">Printing Receive</th>
                        <th colspan="3">Embroidery Receive</th>
                        <th colspan="3">Wash Receive</th>
                        <th colspan="3">Special Work Receive</th>
                    </tr> 
                 <? } ?>   
                    
                    <tr>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Company</th>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Company</th>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Company</th>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th>Embl. Company</th>
                    </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:1040px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="1022" rules="all" id="table_body" >
            <?
			$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name" );
 			$supplier_library=return_library_array( "select id,short_name from  lib_supplier", "id", "short_name" );	
 			 
			$sql = sql_select("SELECT production_date,production_source,serving_company,
						SUM(CASE WHEN production_source =1 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod11,  
						SUM(CASE WHEN production_source =1 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod12,
						SUM(CASE WHEN production_source =1 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod13,
						SUM(CASE WHEN production_source =1 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod14,
						
						SUM(CASE WHEN production_source =3 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod31,  
						SUM(CASE WHEN production_source =3 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod32,
						SUM(CASE WHEN production_source =3 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod33,
						SUM(CASE WHEN production_source =3 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod34
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$action and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id $country_cond
					GROUP BY production_date,production_source,serving_company");
			// echo $sql; die;
			
		   	$printing_in_qnty=0;$emb_in_qnty=0;$wash_in_qnty=0;$special_in_qnty=0;
			$printing_out_qnty=0;$emb_out_qnty=0;$wash_out_qnty=0;$special_out_qnty=0;
			$dataArray=array();$companyArray=array();
            $i=1;
			foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 
				 if($resultRow[csf('production_source')]==3)
					$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
				else
					$serving_company= $company_library[$resultRow[csf('serving_company')]];
				$td_count = 2;	
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="80" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod11")];$printing_in_qnty+=$resultRow[csf("prod11")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod31")];$printing_out_qnty+=$resultRow[csf("prod31")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod11')]>0 || $resultRow[csf('prod31')]>0) echo $serving_company; ?></p></td>
                    <? 
					$companyArray[$serving_company]=$serving_company;
					$dataArray[1][$serving_company]+=$resultRow[csf("prod11")]+$resultRow[csf("prod31")] ?>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod12")];$emb_in_qnty+=$resultRow[csf("prod12")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod32")];$emb_out_qnty+=$resultRow[csf("prod32")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod12')]>0 || $resultRow[csf('prod32')]>0) echo $serving_company; ?></p></td>
                    <? 
 					$dataArray[2][$serving_company]+=$resultRow[csf("prod12")]+$resultRow[csf("prod32")] ?>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod13")];$wash_in_qnty+=$resultRow[csf("prod13")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod33")];$wash_out_qnty+=$resultRow[csf("prod33")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod13')]>0 || $resultRow[csf('prod33')]>0) echo $serving_company; ?></p></td>
                    <? 
 					$dataArray[3][$serving_company]+=$resultRow[csf("prod13")]+$resultRow[csf("prod33")] ?>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod14")];$special_in_qnty+=$resultRow[csf("prod14")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod34")];$special_out_qnty+=$resultRow[csf("prod34")];}else echo "0"; ?></td>
                    <td><p>&nbsp;<? if($resultRow[csf('prod14')]>0 || $resultRow[csf('prod34')]>0) echo $serving_company; ?></p></td>
                    <? 
 					$dataArray[4][$serving_company]+=$resultRow[csf("prod14")]+$resultRow[csf("prod34")] ?>
                  </tr> 
 				 <?		
             	$i++;
            
        }//end foreach 1st
        ?>
        		<tfoot>
                    <tr>
                       <th align="right" colspan="2">Grand Total</th>
                       <th align="right"><? echo $printing_in_qnty; ?></th>
                       <th align="right"><? echo $printing_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right"><? echo $emb_in_qnty; ?></th>
                       <th align="right"><? echo $emb_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right"><? echo $wash_in_qnty; ?></th>
                       <th align="right"><? echo $wash_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       <th align="right"><? echo $special_in_qnty; ?></th>
                       <th align="right"><? echo $special_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                     </tr>
               </tfoot>      
        </table>
       </div>
       
       <div style="clear:both">&nbsp;</div>
       
       <div style="width:450px; float:left"> 
       <table width="400" cellspacing="0" border="1" class="rpt_table" rules="all" > 
       		<? if($action==2){?> <label><h3>Issue Summary</h3></label><? } else {?> <label><h3>Receive Summary</h3></label> <? } ?>               	
             <thead> 
                <tr>
                    <th>SL</th>
                    <th>Emb.Company</th>
                    <th>Print</th>
                    <th>Embroidery</th>
                    <th>Emb	Wash</th>
                    <th>Special Work</th>
                 </tr>
              </thead>  
			 <?
			 $printing_total=0;$emb_total=0;$wash_total=0;$special_total=0;
			 $i=1;	 
			 foreach($companyArray as $com){
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			 ?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                 		<td><? echo $i; ?></td>
                        <td><? echo $com; ?></td>
                        <td align="right"><? echo number_format($dataArray[1][$com]);$printing_total+=$dataArray[1][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[2][$com]);$emb_total+=$dataArray[2][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[3][$com]);$wash_total+=$dataArray[3][$com]; ?></td>
                        <td align="right"><? echo number_format($dataArray[4][$com]);$special_total+=$dataArray[4][$com]; ?></td>
                 </tr>   
              <? $i++; } ?>
              <tfoot>
                    <tr>
                       <th align="right" colspan="2">Grand Total</th>
                       <th align="right"><? echo number_format($printing_total); ?></th>
                       <th align="right"><? echo number_format($emb_total); ?></th>
                       <th align="right"><? echo number_format($wash_total); ?></th>
                       <th align="right"><? echo number_format($special_total); ?></th>
                    </tr>
              </tfoot>          
    	 </table>
     </div>
     
     <div style="width:450px; float:left; "> 
     	<? if($action!=2) //only for receive
		 { 
			?> 	
			<table width="400" cellspacing="0" border="1" class="rpt_table" rules="all" > 
            <label><h3>Balance</h3></label>
              <thead> 
                <tr>
                    <th>SL</th>
                    <th>Particulers</th>
                    <th>Print</th>
                    <th>Embroidery</th>
                    <th> Wash</th>
                    <th>Special Work</th>
                 </tr>
              </thead>  
 			<?
 				$sql_order = sql_select("SELECT 
						SUM(CASE WHEN b.emb_name=1 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS print,  
						SUM(CASE WHEN b.emb_name=2 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS emb,
						SUM(CASE WHEN b.emb_name=3 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS wash,
						SUM(CASE WHEN b.emb_name=4 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS special
   					FROM
						wo_po_break_down a, wo_pre_cost_embe_cost_dtls b 
					WHERE
						a.id in ($po_break_down_id) and a.job_no_mst=b.job_no");
				foreach($sql_order as $resultRow);	
						
				$sql_mst = sql_select("SELECT 
						SUM(CASE WHEN embel_name=1 THEN production_quantity ELSE 0 END) AS print_issue,  
						SUM(CASE WHEN embel_name=2 THEN production_quantity ELSE 0 END) AS emb_issue,
						SUM(CASE WHEN embel_name=3 THEN production_quantity ELSE 0 END) AS wash_issue,
						SUM(CASE WHEN embel_name=4 THEN production_quantity ELSE 0 END) AS special_issue
 					FROM
						pro_garments_production_mst
					WHERE
						po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=2 $country_cond
					");		
				//echo $sql_mst;die;
				foreach($sql_mst as $resultMst);
				//echo $sql;die;
				$i=1;		
				 
					 ?>
						 <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
								<td>Req Qnty</td>
								<td align="right"><? echo number_format($resultRow[csf('print')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('emb')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('wash')]); ?></td>
								<td align="right"><? echo number_format($resultRow[csf('special')]); ?></td>
						 </tr> 
                         <tr bgcolor="#FFFFFF">
								<td><? echo $i++; ?></td>
                                <td>Total Sent for</td>
 								<td align="right"><? echo number_format($resultMst[csf('print_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('emb_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('wash_issue')]); ?></td>
								<td align="right"><? echo number_format($resultMst[csf('special_issue')]); ?></td>
						 </tr>
                         <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
                                <td>Total Receive</td>
 								<td align="right"><? echo number_format($printing_total); ?></td>
								<td align="right"><? echo number_format($emb_total); ?></td>
								<td align="right"><? echo number_format($wash_total); ?></td>
								<td align="right"><? echo number_format($special_total); ?></td>
						 </tr>
                         <tr bgcolor="#FFFFFF">
								<td><? echo $i++; ?></td>
                                <td>Receive Balance</td>
                                <? $rcv_print_balance = $resultMst[csf('print_issue')]-$printing_total; ?>
 								<td align="right"><? echo number_format($rcv_print_balance); ?></td>
								<? $rcv_emb_balance = $resultMst[csf('emb_issue')]-$emb_total; ?>
 								<td align="right"><? echo number_format($rcv_emb_balance); ?></td>
								<? $rcv_wash_balance = $resultMst[csf('wash_issue')]-$wash_total; ?>
 								<td align="right"><? echo number_format($rcv_wash_balance); ?></td>
								<? $rcv_special_balance = $resultMst[csf('special_issue')]-$special_total; ?>
 								<td align="right"><? echo number_format($rcv_special_balance); ?></td>
						 </tr> 
                         <tr bgcolor="#E9F3FF">
								<td><? echo $i++; ?></td>
 								<td>Issue Balance</td>
 								<td align="right"><? echo  number_format($resultRow[csf('print')]-$resultMst[csf('print_issue')]); ?></td>
								<td align="right"><? echo  number_format($resultRow[csf('emb')]-$resultMst[csf('emb_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('wash')]-$resultMst[csf('wash_issue')]); ?></td>
                                <td align="right"><? echo  number_format($resultRow[csf('special')]-$resultMst[csf('special_issue')]); ?></td>
 						 </tr>  
					 <? 
 				} 
			?>
            </table> 
     </div>
 </div>    
<?
  exit();
}

if ($action=="reject_qty")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	?>
     <div style="width:500px;" align="center"> 
       <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" > 
             <thead>
             	<tr>
                	<th colspan="5">Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Cutting Reject Qty</th>
                    <th width="110">Sewing Out Reject Qty</th>
                    <th width="110">Finish Reject Qty.</th>
                    <th width="110">Total Reject Qty.</th>
                 </tr>
              </thead>
              <tbody> 
			 <?
			 
			if($reportType==1 || $reportType==5)
			{
				$location_cond=""; 
				$floor_cond="";
				$country_cond="";
			}
			else if($reportType==2)
			{
				$location_cond=" and location=$location_id"; 
				$floor_cond=" and floor_id=$floor_id";
				$country_cond="";
			}
			else if($reportType==3)
			{
				$location_cond=""; 
				$floor_cond="";
				$country_cond=" and country_id='$country_id'";	
			}
			else
			{
				$location_cond=" and location=$location_id"; 
				$floor_cond=" and floor_id=$floor_id";
				$country_cond=" and country_id='$country_id'";	
			}
			 
			$sql_qry="Select sum(CASE WHEN production_type ='1' THEN reject_qnty ELSE 0 END) AS cutting_rej_qnty,
			 				sum(CASE WHEN production_type ='8' THEN reject_qnty ELSE 0 END) AS finish_rej_qnty,
							sum(CASE WHEN production_type ='5' THEN reject_qnty ELSE 0 END) AS sewingout_rej_qnty
							from pro_garments_production_mst 
							where po_break_down_id in ($po_id) and item_number_id='$item_id' and status_active=1 and is_deleted=0 $location_cond $floor_cond $country_cond group by po_break_down_id";
			//echo $sql_qry;
			$sql_result=sql_select($sql_qry);

			$i=1;	 
			foreach($sql_result as $row)
			{
			 	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			?>
                 <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo $row[csf('cutting_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('sewingout_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? echo $row[csf('finish_rej_qnty')]; ?>&nbsp;</td>
                    <td align="right"><? $total_reject=$row[csf('cutting_rej_qnty')]+$row[csf('sewingout_rej_qnty')]+$row[csf('finish_rej_qnty')]; echo $total_reject; ?>&nbsp;</td>
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

//cutting-1,sewing ouput-5--------------------popup-----------//
if ($action=="challanPopup") 
{
 	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
 	?>
    <script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="260px";
		}	
		
	</script>
    <div style="width:530px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:530px; margin-left:5px">
        <div id="report_container">
            <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <th width="50">Sl.</th>    
                    <th width="150">Production Date</th>
                    <th width="160">Challan No</th>
                    <th>Quantity</th>
                </thead>
            </table>
            <div style="max-height:260px; overflow-y:scroll; width:520px;" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table" width="500" rules="all" id="table_body" >
                <?
					 $i=1; $total_quantity=0; $location="";$floor="";
					 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
					 {
						 if($location_id!="") $location=" and location=$location_id";
						 if($floor_id!="") $floor=" and floor_id=$floor_id";
					 }
					 
					 $sql=sql_select("select production_date, challan_no, SUM(production_quantity) as production_quantity from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$prod_type and status_active=1 and production_date='$prod_date' $location $floor $country_cond group by production_date, challan_no");
					foreach($sql as $resultRow)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                         <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="50"><? echo $i;?></td>
                            <td width="150" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                            <td width="160"><? echo $resultRow[csf("challan_no")]; ?>&nbsp;</td>
                            <td align="right" style="padding-right:2px"><? echo number_format($resultRow[csf("production_quantity")]); ?></td>
                         </tr>	
                        <?	
                        $total_sewing_quantity+=$resultRow[csf("production_quantity")];
                        $i++;
                    }
                    ?>
                   <tfoot class="tbl_bottom">
                        <td>&nbsp;</td> 
                        <td>&nbsp;</td> 
                        <td align="right">Total</td> 
                        <td align="right" style="padding-right:2px"><? echo number_format($total_sewing_quantity); ?></td>
					</tfoot>
                </table>
            </div>
        </div>
	</fieldset>
    <?
 exit();
 
}

if($action=="update_tna_progress_comment")
{
	//echo load_html_head_contents("TNA Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date="";

	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task",'task_name','task_short_name');
	
	$tna_task_id=array(); $plan_start_array=array(); $plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	
	$notice_start_array=array();
	$notice_finish_array=array();
	
	$task_sql= sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.template_id=$template_id and a.po_number_id=$po_id order by b.task_sequence_no asc");
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	}
	
	
	
	$comments_array=array(); $responsible_array=array();
	$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id=$template_id and order_id=$po_id");
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	$execution_time_array=array();
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id=$template_id");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("tna_task_id")]] =$row_execution_time[csf("execution_days")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time","task_template_id","lead_time");
?>


	<fieldset style="width:1010px"> 
        <div class="form_caption" align="center"><strong>TNA Progress Comment</strong></div>
        <table style="margin-top:10px" width="1000" border="1" rules="all" class="rpt_table">
            <?php
			$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date from wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
			$result=sql_select($sql);
            foreach($result as $row)
            {
            ?>
            	<thead>
                    <tr bgcolor="#E9F3FF">
                        <th width="130">Company</th>
                        <td width="196" style="padding-left:5px"><?php echo $company_short_name_arr[$row[csf('company_name')]];  ?></td>
                        <th width="130">Buyer</th>
                        <td width="186" style="padding-left:5px"><?php echo $buyer_short_name_arr[$row[csf('buyer_name')]];  ?></td>
                        <th width="130">Order No</th>
                        <td width="186" style="padding-left:5px"><p><?php echo $row[csf('po_number')]; ?></p></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <th>Style Ref.</th>
                        <td style="padding-left:5px"><p><?php echo $row[csf('style_ref_no')]; ?></p></td>
                        <th>RMG Item</th>
                        <td style="padding-left:5px"><p><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                        <th>Order Recv. Date</th>
                        <td style="padding-left:5px"><?php echo change_date_format($row[csf('po_received_date')]); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <th>Ship Date</th>
                        <td style="padding-left:5px"><?php echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <th>Lead Time</th>
                        <td style="padding-left:5px">
                            <?
								$template_id=str_replace("'","",$template_id);
								
								if($tna_process_type==1)
								{
									$lead_timee=$lead_time[$template_id];
								}
								else
								{
									$lead_timee=$template_id;
								}
								//echo $lead_time=return_field_value("lead_time","tna_task_template_details", "task_template_id='$template_id' and status_active=1 and is_deleted=0");
								echo $lead_timee;
							?>
                        </td>
                        <th>Job Number</th>
                        <td style="padding-left:5px">
							<? echo $row[csf('job_no')];   ?>
                        </td>
                    </tr>
                </thead>
            <?php
            }
            ?>
        </table>
        <table style="margin-top:5px" cellpadding="0" width="1000" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="50">Task No</th>
                <th width="150">Task Name</th>
                <th width="60">Allowed Days</th>
                <th width="80">Plan Start Date</th>
                <th width="80">Plan Finish Date</th>
                <th width="80">Actual Start Date</th>
                <th width="80">Actual Finish Date</th>
                <th width="80">Start Delay/ Early By</th>
                <th width="80">Finish Delay/ Early By</th>
                <th width="100">Responsible</th>
                <th>Comments</th>
            </thead> 	 	
        </table>
        
          
        
            <table cellpadding="0" width="1000" cellspacing="0" border="1" rules="all" class="rpt_table">
                <? 
				
				
				$i=1;
                foreach($tna_task_id as $key)
                { 
                    if($i%2==0) $trcolor="#E9F3FF"; else $trcolor="#FFFFFF";
					
					$bgcolor1=""; $bgcolor="";
									
					if ($plan_start_array[$key]!=$blank_date) 
					{
						if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
						else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";
						else $bgcolor="";
						
					}
					 
					if ($plan_finish_array[$key]!=$blank_date) {
						if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
						else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
					}
					
					if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
					if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
					
					// Delay / Early............
									
					$bgcolor5=""; $bgcolor6="";
					$delay=""; $early="";
					
					if($actual_start_array[$key]!=$blank_date)
					{
						$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
						$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);
						
						$start_diff=$start_diff1-1;
						$finish_diff=$finish_diff1-1;
						
						if($start_diff<0)
						{
							$bgcolor5="#2A9FFF";	//Blue
							$start="(Delay)";
						}
						if($start_diff>0)
						{
							$bgcolor5="";
							$start="(Early)";
							
						}
						if($finish_diff<0)
						{
							$bgcolor6="#2A9FFF";
							$finish="(Delay)";
						}
						if($finish_diff>0)
						{	
							$bgcolor6="";
							$finish="(Early)";
						}
						
						
					}
					else
					{
						if(date("Y-m-d")>$plan_start_array[$key])
						{
							$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
							$start_diff=$start_diff1-1;
							$bgcolor5="#FF0000";		//Red
							$start="(Delay)";
						}
						if(date("Y-m-d")>$plan_finish_array[$key])
						{
							$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
							$finish_diff=$finish_diff1-1;
							$bgcolor6="#FF0000";
							$finish="(Delay)";
						}
						if(date("Y-m-d")<=$plan_start_array[$key])
						{
							$start_diff = "";
							$bgcolor5="";
							$start="(Ac. Start Dt. Not Found)";
						}
						if(date("Y-m-d")<=$plan_finish_array[$key])
						{
							$finish_diff = "";
							$bgcolor6="";
							$finish="(Ac. Finish Dt. Not Found)";
							
						}
					}
							
                    ?>
                    <tr bgcolor="<? echo $trcolor; ?>" id="tr_<? echo $i; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')">
                        <td align="center" width="50"><? echo $i; ?></td>
                        <td width="150"><? echo $tna_task_arr[$key]; ?></td>
                        <td align="center" width="60"><? echo $execution_time_array[$key]; ?></td>
                        <td align="center" width="80"><? echo change_date_format($plan_start_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80"><? echo change_date_format($plan_finish_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor;  ?>">
                            <? 
                                if($actual_start_array[$key]=="0000-00-00" || $actual_start_array[$key]=="") echo "&nbsp;";
                                else echo change_date_format($actual_start_array[$key]);
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor1;  ?>">
                            <?  
                                 if($actual_finish_array[$key]=="0000-00-00" || $actual_finish_array[$key]=="") echo "&nbsp;";
                                 else echo change_date_format($actual_finish_array[$key]);
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor5;  ?>">
							<?  
                                echo $start_diff." ".$start;
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor6;  ?>">
                            <?  
                                echo $finish_diff." ".$finish;
                            ?>
                        </td>
                        <td width="100"><p><?php echo $responsible_array[$key]; ?>&nbsp;</p></td>
                        <td><p><?php echo $comments_array[$key]; ?>&nbsp;</p></td>
                    </tr>
              	<? 
                    $i++;
                }
                ?>
            </table>
    </fieldset>
<?
exit();
}

if ($action==10)
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
 	?>
    <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
          </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 	</div>
    <div id="details_reports" align="center">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
               <tr>
                    <th width="30">Sl.</th>
                    <th width="120">Sys. Challan</th> 
                    <th width="100">Ex. Date</th>
                    <th>Ex-Factory Qty</th>
                </tr> 
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:500px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="482" rules="all" id="table_body" >
            <?
				$sys_challan_arr=return_library_array( "select id, sys_number from pro_ex_factory_delivery_mst",'id','sys_number');

				$sql=sql_select("select delivery_mst_id, challan_no, ex_factory_date, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst  where po_break_down_id in ($po_break_down_id) and status_active=1 and is_deleted=0 group by delivery_mst_id, challan_no, ex_factory_date order by delivery_mst_id, ex_factory_date");
			//echo "elect delivery_mst_id, challan_no, ex_factory_date, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst  where po_break_down_id in ($po_break_down_id) and status_active=1 and is_deleted=0 group by delivery_mst_id, challan_no, ex_factory_date order by delivery_mst_id, ex_factory_date"; //die;
			
            $i=1;
			foreach($sql as $resultRow)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="120" align="center"><? if($resultRow[csf("delivery_mst_id")]!=0) echo $sys_challan_arr[$resultRow[csf("delivery_mst_id")]]; else echo $resultRow[csf("challan_no")] ?></td>
                    <td width="100" align="center"><? echo change_date_format($resultRow[csf("ex_factory_date")]); ?></td>
                    <td align="right"><? echo $resultRow[csf("ex_factory_qnty")]; $ex_factory_qnty+=$resultRow[csf("ex_factory_qnty")]; ?></td>
                  </tr> 
 				 <?		
             	$i++;
			}
        ?>
            <tfoot>
                <tr>
                   <th align="right" colspan="3">Grand Total</th>
                   <th align="right"><? echo $ex_factory_qnty; ?></th>
                 </tr>
           </tfoot>      
        </table>
       </div>
       </div>
<?
  exit();
}
?>