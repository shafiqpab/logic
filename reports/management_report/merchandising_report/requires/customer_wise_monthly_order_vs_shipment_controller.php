<?
header('Content-type:text/html; charset=utf-8');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.others.php');
$date=date('Y-m-d');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data']; 
$action=$_REQUEST['action'];

$userCredential = sql_select("SELECT brand_id, single_user_id FROM user_passwd where id=$user_id");
$userbrand_id = $userCredential[0][csf('brand_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];

$userbrand_idCond = ""; $filterBrandId = "";
if ($userbrand_id !='' && $single_user_id==1) {
    $userbrand_idCond = "and id in ($userbrand_id)";
	$filterBrandId=$userbrand_id;
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data'
	order by location_name","id,location_name", 1, "-- All --", $selected, "",0 );
	exit();
}


if($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 150, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
	}
	exit();
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$buyer_name=str_replace("'","",$cbo_buyer_name);

	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	//$rpt_type=str_replace("'","",$rpt_type);
	
	
	$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
	
	if($buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else { $buyer_id_cond=""; $buyer_id_cond2=""; }
		}
		else { $buyer_id_cond="";$buyer_id_cond2=""; }
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$buyer_name";
	}
	

	if(trim($date_from)!="") $start_date=$date_from;
	if(trim($date_to)!="") $end_date=$date_to;

	
	if(trim($company_name)=="0") $company_name="%%"; else $company_name="$company_name";
	if($cbo_location_id!=0){$location_con=" AND a.location_name=$cbo_location_id";}
	
	
	if($db_type==0)
	{
		$start_date=change_date_format($date_from,'yyyy-mm-dd','-');
		$end_date=change_date_format($date_to,'yyyy-mm-dd','-');

		if ($start_date!="" && $end_date!="") $date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
	}
	if($db_type==2)
	{
		$start_date=change_date_format($date_from,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($date_to,'yyyy-mm-dd','-',1);

		if ($start_date!="" && $end_date!="") $date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
	}

	$user_name_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	if($rpt_type==1)//Show
	{
		$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		
		
		
		if($db_type==2) 
		{ 
			$date=date('d-m-Y');
			$year_select="to_char(a.insert_date,'YYYY') as year";
			$days_on=" (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2,(b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4";
		}
		else
		{ 
			$date=date('d-m-Y');
			$year_select="YEAR(a.insert_date) as year";
			$days_on="DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2,DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4";
		}

		

		$condition= new condition();
			$condition->company_name("=$company_name");
		  if(str_replace("'","",$buyer_name)>0){
			  $condition->buyer_name("=$buyer_name");
		 }
		 
		 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				  //$condition->country_ship_date(" between '$start_date' and '$end_date'");
				  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");	
			 }
			 
		$condition->init();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order(); 
				
		/*$sql_data="SELECT a.buyer_name,a.location_name,b.id as po_id,b.po_number,b.pub_shipment_date,b.po_quantity,b.unit_price,b.po_total_price,sum(c.ex_factory_qnty) as ex_factory_qnty,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.company_name like '$company_name' $buyer_id_cond $date_cond $location_con and a.status_active=1 and b.status_active=1 group by a.buyer_name,a.location_name,b.id,b.po_number,b.pub_shipment_date,b.po_quantity,b.unit_price,b.po_total_price order by b.pub_shipment_date";*/


		
	  $sql_data="SELECT a.company_name, a.location_name,a.total_set_qnty, a.buyer_name, to_char(a.insert_date,'YYYY') as year,  b.id as po_id,  b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.unit_price, b.po_total_price, c.ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date,$year_select,$days_on from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 
	  where  a.job_no=b.job_no_mst and a.company_name like '$company_name' $location_con $buyer_id_cond $date_cond and a.status_active=1 and b.status_active=1  group by a.company_name,a.total_set_qnty, a.location_name, a.buyer_name, a.insert_date, b.id,  b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.unit_price, b.po_total_price, c.ex_factory_qnty order by b.pub_shipment_date,b.id";

	  
	  //echo $sql_data; //die;
	  $data_array=sql_select( $sql_data);
	  $all_po_id="";
	  foreach($data_array as $row) //
	  {
	  	
		
		if($all_po_id=="") $all_po_id=$row[csf('po_id')];else $all_po_id.=",".$row[csf('po_id')];
		
		$pub_date_key=date("M-Y",strtotime($row[csf('pub_shipment_date')]));
		//Sumary
		$month_wise_arr[$pub_date_key]=$pub_date_key;
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_quantity']+=$row[csf('po_quantity')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_quantity_pcs']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_total_price']+=$row[csf('po_total_price')];

		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['unit_price']+=$row[csf('unit_price')];


		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['ex_factory_value']+=$row[csf('ex_factory_qnty')]*$row[csf('unit_price')];

		
		$comp_buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]=$row[csf('company_name')];
		
	  }
	 // asort($month_wise_arr);
		//  print_r($month_wise_arr);
		




		$tot_width=820+count($month_wise_arr)*600;		
		ob_start();
		?>
		<div  style=" max-height:380px; overflow-y:scroll;" width="<? echo $tot_width;?>"  align="left" id="scroll_body">
				<table width="<? echo $tot_width; ?>"  cellspacing="0" cellpadding="0" rules="all" align="center" border="0">
	            <tr style="border:none;">
	                <td  align="center" style="border:none; font-size:18px; font-weight: bold;">
	                	Customer Wise Monthly Order VS Shipment.                               
	             </td>
	            </tr>
	            <tr style="border:none;">
	                <td  align="center" style="border:none;font-size:16px; font-weight:bold">
	                 <? echo $company_name_arr[$company_name]; ?>
	                </td>
	            </tr>
	            <tr style="border:none;">
	                <td  align="center" style="border:none; font-size:12px; font-weight: bold;">
	               	<? echo $start_date !="" ? "   Date   From ".$start_date."   To   ".$end_date : "" ;?>                          
	             </td>
	            </tr>
	            
	       		</table>
			
			<table width="<? echo $tot_width;?>" border="1" class="rpt_table" rules="all" id="scroll_body">
							<thead>
                            <tr>
                            <th colspan="2">&nbsp; </th>
                              <?
                                foreach($month_wise_arr as $date_key=>$val_data)
								{
								?>
								<th title="<? echo count($val_data);?>"  colspan="7"><? echo $date_key;?></th>
                                <?
								}
								?>
								<th colspan="7">Total</th>
                            </tr>
                            
                             <tr>
								<th width="20">SL</th>
                                <th width="100">Buyer/Customer Name</th>
								
                                <?
                                foreach($month_wise_arr as $date_key=>$val)
								{
								?>
								<th width="100">Received Order Qty</th>
                                <th width="100">Received Order Qty Pcs</th>
								<th width="100">Received Order Value</th>
								<th width="100">Shipped Order Qty</th>
								<th width="100">Shipped Order Value</th>
								<th width="100">Average Unit Price Against Shipment</th>
								<th width="100">Order % Against Shipment Value</th>
                                <?
								}
								?>
								<th width="100">Received Order Qty</th>
                                <th width="100">Received Order Qty Pcs </th>
								<th width="100">Received Order Value</th>
								<th width="100">Shipped Order Qty</th>
								<th width="100">Shipped Order Value</th>
								<th width="100">Average Unit Price Against Shipment</th>
								<th width="100">Order % Against Shipment Value</th>
							</tr>
							</thead>
                           <tbody>
						<?
					
						 $k=1;
						foreach($comp_buyer_wise_arr as $company_key=> $comp_data)
						{

							$receive_total_total=0;$receive_total_total_pcs=0;
							$receive_value_total=0;
							$shipped_total_total=0;
							$shipped_value_total=0;
							foreach($comp_data as $buyer_key=> $row)
							{
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>"  onclick="change_color('trsum_<? echo $k; ?>','<? echo $bgcolor;?>')" id="trsum_<? echo $k; ?>">
								<td width="20" align="center"><? echo $k;//echo $company_name; ?></td>
								<td width="100" align="center"><? echo $buyer_name_arr[$buyer_key];//echo $company_name; ?>
								</td>
                                <?
                                $total_po_row=0; $total_po_row_pcs=0;
                                $total_po_value_row=0;
                                $total_ex_factory_row=0;
                                $total_ex_factory_value_row=0;
                                $avarage_unit_row=0;
                                $order_agaisnt_value_row=0;
                                foreach($month_wise_arr as $date_key=>$val)
								{
									$po_quantity=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_quantity'];
									$po_quantity_pcs=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_quantity_pcs'];
									$po_total_price=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['po_total_price'];
									$ex_factory_qnty=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['ex_factory_qnty'];
									$ex_factory_value=$buyer_summary_mon_arr[$company_key][$buyer_key][$date_key]['ex_factory_value'];
								?>
								<td width="100" align="right"><? echo number_format($po_quantity,0); $total_po_qnty_arr[$date_key]+=$po_quantity;?>
								</td>
                                <td width="100" align="right"><? echo number_format($po_quantity_pcs,0); $total_po_qnty_pcs_arr[$date_key]+=$po_quantity_pcs;?>
								</td>
								<td width="100" align="right"><? echo number_format($po_total_price,2); $total_po_value_arr[$date_key]+=$po_total_price;?>
								</td>
								<td width="100" align="right"><? echo number_format($ex_factory_qnty,0); $total_ex_factory_qnty_arr[$date_key]+=$ex_factory_qnty;?>
								</td>
								<td width="100" align="right"><? echo number_format($ex_factory_value,2); $total_ex_factory_value_arr[$date_key]+=$ex_factory_value;?>
								</td>
								<td width="100" align="right" title="Order Qty*CM value"><?
								if($ex_factory_value>0)
								{
									$avarage_unit=$ex_factory_value/$ex_factory_qnty;
									echo number_format($avarage_unit,2);
								}
								else echo "";
								 
								  $total_avarage_unit_arr[$date_key]+=$avarage_unit; ?> 
								</td>
								<td width="100" align="right" title="Order Qty*SMV"><?
								if($ex_factory_value>0)
								{
								$order_agaisnt_value=($ex_factory_value/$po_total_price)*100;
								}
								else $order_agaisnt_value=0;
								 $total_order_agaisnt_value_arr[$date_key]+=$order_agaisnt_value; echo number_format($order_agaisnt_value,2); ?>
								</td>
                                <?
                                $total_po_row+=$po_quantity; 
								$total_po_row_pcs+=$po_quantity_pcs;
                                $total_po_value_row+=$po_total_price;
                                $total_ex_factory_row+=$ex_factory_qnty;
                                $total_ex_factory_value_row+=$ex_factory_value;
                                $avarage_unit_row+=$avarage_unit;
                                $order_agaisnt_value_row+=$order_agaisnt_value;
								}
								?>
								<td width="100" align="right"><? echo number_format($total_po_row,2); ?>
								</td>
                                <td width="100" align="right"><? echo number_format($total_po_row_pcs,2); ?>
								</td>
								<td width="100" align="right"><? echo number_format($total_po_value_row,2); ?>
								</td>
								<td width="100" align="right"><? echo number_format($total_ex_factory_row,2); ?>
								</td>
								<td width="100" align="right"><? echo number_format($total_ex_factory_value_row,2); ?>
								</td>
								<td width="100" align="right"><? if($total_ex_factory_value_row>0) echo number_format($total_ex_factory_value_row/$total_ex_factory_row,2);else echo ""; ?>
								</td>
								<td width="100" align="right"><? if($total_ex_factory_value_row>0) echo number_format(($total_ex_factory_value_row/$total_po_value_row)*100,2);else echo ""; ?>
								</td>
							</tr>
							<?
							$k++;
							$receive_total_total+=$total_po_row;$receive_total_total_pcs+=$total_po_row_pcs;
							$receive_value_total+=$total_po_value_row;
							$shipped_total_total+=$total_ex_factory_row;
							$shipped_value_total+=$total_ex_factory_value_row;
							}
						}
						?>
                        </tbody>
						<tfoot>
							<tr>
								<th align="center" colspan="2">Total:</th>
                                 <?
                                foreach($month_wise_arr as $date_key=>$val)
								{
								?>
								<th align="right"><? echo number_format($total_po_qnty_arr[$date_key],0); ?></th>
                                <th align="right"><? echo number_format($total_po_qnty_pcs_arr[$date_key],0); ?></th>
								<th align="right"><? echo number_format($total_po_value_arr[$date_key],2); ?></th>
								<th align="right"><? echo number_format($total_ex_factory_qnty_arr[$date_key],2); ?></th>
								<th align="right"><? echo number_format($total_ex_factory_value_arr[$date_key],2); ?></th>
								<th align="right">&nbsp;</th>
								<th align="right">&nbsp;</th>
                                <?
								}
								?>
								<th align="right"><? echo number_format($receive_total_total,2); ?></th>
                                <th align="right"><? echo number_format($receive_total_total_pcs,2); ?></th>
								<th align="right"><? echo number_format($receive_value_total,2); ?></th>
								<th align="right"><? echo number_format($shipped_total_total,2); ?></th>
								<th align="right"><? echo number_format($shipped_value_total,2); ?></th>
								<th align="right">&nbsp;</th>
								<th align="right">&nbsp;</th>
							</tr>
							
						</tfoot>
			</table>
		</div>
		<?
	}

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
	echo "$total_data####$filename####$rpt_type####$search_by";
	disconnect($con);
	exit();
}



?>
