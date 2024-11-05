<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$buyer_short_name_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name  order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;
if( $action=="report_generate" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $txt_style;die;

	$company_name=str_replace("'","",$cbo_company_name);
	if($company_name==0) $company_cond=""; else $company_cond="and a.company_id=$company_name ";
	if($company_name==0) $company_cond2=""; else $company_cond2="and a.id=$company_name ";
	if($company_name==0) $job_company_cond=""; else $job_company_cond="and a.company_name=$company_name ";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=str_replace("'","",$txt_date_from);
		$end_date=str_replace("'","",$txt_date_to); 
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
		$poDataArray=sql_select("select b.id,b.pub_shipment_date, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $job_company_cond $date_cond $buyer_id_cond  ");// and a.season like '$txt_season'
		
		$po_array=array(); $all_po_id='';
		foreach($poDataArray as $row)
		{
		
		if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}//echo $all_po_id;die;
		//if( $all_po_id!="") $po_id_all=" and d.po_breakdown_id in($all_po_id)";else $po_id_all="";
		
		$po_id=array_chunk(explode(",",$all_po_id),999, true);
		$order_cond2="";
		   $ji=0;
		   foreach($po_id as $key=> $value)
		   {
			   if($ji==0)
			   {
				//$order_cond=" and b.po_break_down_id  in(".implode(",",$value).")"; 
				$order_cond1=" and b.po_break_down_id  in(".implode(",",$value).")"; 
				 $order_cond2=" and d.po_breakdown_id  in(".implode(",",$value).")";
			   }
			   else
			   {
				//$order_cond.=" or b.po_break_down_id  in(".implode(",",$value).")";
				$order_cond1.=" or b.po_break_down_id  in(".implode(",",$value).")";
				$order_cond2.=" or d.po_breakdown_id  in(".implode(",",$value).")";
			   }
			   $ji++;
		   }//echo $order_cond2;die;
		  // echo $order_cond2;
				
	//echo $po_id_all;
	if($template==1)
	{
		
		$company_arr=array();$company_id=array();$all_company_id="";
		$sql_data=sql_select("select a.id, a.company_name  from  lib_company a  where   a.status_active=1 and a.is_deleted=0 $company_cond2 ");
		foreach($sql_data as $com_row)
		{
			$company_arr[$com_row[csf('id')]]=$com_row[csf('company_name')];
			//$company_id[$com_row[csf('id')]]=$com_row[csf('id')];
			if($all_company_id=="") $all_company_id=$com_row[csf('id')]; else $all_company_id.=','.$com_row[csf('id')];
		}//var_dump($company_id);die;
		
		if($all_company_id!="") $company_cond="and a.company_id in($all_company_id)";else $company_cond="";
		
		if($all_company_id!="") $company_cond2="and b.company_id in($all_company_id)";else $company_cond2="";
		if($all_company_id!="") $company_cond3="and a.importer_id in($all_company_id)";else $company_cond3="";
		
		if($all_company_id!="") $company_cond4="and b.company_name in($all_company_id)";else $company_cond4="";
		
		
			$fabriccostArray=array();	$costing_per_arr=array();
			$fabriccostDataArray=sql_select("select b.company_name, a.costing_per,c.cons_qnty as cons_qnty,c.count_id,c.copm_one_id as yarn_comp_type1st,c.type_id as yarn_type from  wo_pre_cost_mst a,wo_po_details_master b,wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=c.job_no and c.job_no=b.job_no   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond4 group by b.company_name, a.costing_per,c.count_id,c.copm_one_id,c.type_id,c.cons_qnty  ");
			foreach($fabriccostDataArray as $fabRow)
			{
				$fabriccostArray[$fabRow[csf('company_name')]][$fabRow[csf('count_id')]][$fabRow[csf('yarn_comp_type1st')]][$fabRow[csf('yarn_type')]]['cons_qty']=$fabRow[csf('cons_qnty')];
				$costing_per_arr[$fabRow[csf('company_name')]]['costing_per']=$fabRow[csf('costing_per')];
			
			} 
		
		
		$booking_qnty_arr=array();
		$sql_data=sql_select("select a.company_id,c.count_id,c.copm_one_id,c.type_id ,  b.grey_fab_qnty   as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_yarn_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.fabric_cost_dtls_id and c.job_no=b.job_no and a.job_no=b.job_no   and a.item_category in(2,13)  and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0  $company_cond $order_cond1 group by a.company_id,c.count_id,c.copm_one_id,c.type_id,b.grey_fab_qnty");
		foreach($sql_data as $row)
		{
			$booking_qnty_arr[$row[csf('company_id')]][$row[csf('count_id')]][$row[csf('copm_one_id')]][$row[csf('type_id')]]['req_qty']=$row[csf('grey_fab_qnty')];
			
		} //var_dump($booking_qnty_arr);die;
		$company_data_arr=array();$issue_qty_arr=array();
	$sql="select  b.company_id, sum(CASE WHEN d.entry_form ='3' and b.transaction_type=2 THEN b.cons_quantity ELSE 0 END) AS issue_qnty,sum(CASE WHEN d.entry_form ='9' and b.transaction_type=4 THEN b.cons_quantity ELSE 0 END) AS issue_return_qnty,   c.yarn_type, c.yarn_count_id as count_id,c.yarn_comp_type1st as yarn_comp_type1st,c.yarn_comp_type2nd,c.yarn_comp_percent1st as yarn_comp_percent1st,c.yarn_comp_percent2nd from inv_transaction b, product_details_master c,order_wise_pro_details d where b.id=d.trans_id and d.trans_type in(2,4) and d.entry_form in(3,9) and b.item_category=1   and   b.transaction_type in(2,4) and b.prod_id=c.id and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond2  $order_cond2  group by c.yarn_type, c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_type2nd,c.yarn_comp_percent1st,c.yarn_comp_percent2nd,b.company_id  order by   c.yarn_count_id,c.yarn_comp_type1st, c.yarn_type";
		$result=sql_select($sql);
		foreach($result as $row)
		{
			$issue_qty_arr[$row[csf('company_id')]][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['issue_qnty']=$row[csf('issue_qnty')];
			$issue_qty_arr[$row[csf('company_id')]][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['issue_ret_qnty']=$row[csf('issue_return_qnty')];
			$company_data_arr[$row[csf('company_id')]]=$row[csf('company_id')];
			
		} //var_dump($issue_qty_arr);die;
		$stock_qty_arr=array();
		$sql_stock=sql_select("select a.yarn_count_id, a.company_id,a.yarn_comp_type1st,a.yarn_type,sum(a.current_stock) as current_stock  from  product_details_master a  where   a.status_active=1 and a.is_deleted=0 $company_cond group by a.yarn_count_id, a.company_id,a.yarn_comp_type1st,a.yarn_type ");
		foreach($sql_stock as $row)
		{
			$stock_qty_arr[$row[csf('company_id')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['stock']=$row[csf('current_stock')];
			
		}//var_dump($company_id);die
		
		$recv_qty_arr=array();
		$sql_recv=sql_select("select b.yarn_count_id, b.company_id,b.yarn_comp_type1st,b.yarn_type,sum(CASE WHEN c.entry_form ='1' and a.transaction_type=1 THEN a.cons_quantity ELSE 0 END) as recv_qty  from  inv_transaction a ,product_details_master b,inv_receive_master c  where    b.id=a.prod_id and a.transaction_type in(1) and c.entry_form in(1) and a.item_category=1 and c.receive_basis=1  and c.id=a.mst_id and  a.status_active=1 and a.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 $company_cond group by b.yarn_count_id, b.company_id,b.yarn_comp_type1st,b.yarn_type ");
		foreach($sql_recv as $row)
		{
			$recv_qty_arr[$row[csf('company_id')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['recv']=$row[csf('recv_qty')];
			//$recv_qty_arr[$row[csf('company_id')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['return_qty']=$row[csf('recv_return_qty')];
			
		}//var_dump($company_id);die
		$recv_return_qty_arr=array();
		$sql_recv_ret=sql_select("select b.yarn_count_id, b.company_id,b.yarn_comp_type1st,b.yarn_type,sum(CASE WHEN c.entry_form ='8' and a.transaction_type=3 THEN a.cons_quantity ELSE 0 END) as recv_return_qty  from  inv_transaction a ,product_details_master b, inv_issue_master c  where    b.id=a.prod_id and a.transaction_type in(3) and c.entry_form in(8) and a.item_category=1   and c.id=a.mst_id and  a.status_active=1 and a.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 $company_cond group by b.yarn_count_id, b.company_id,b.yarn_comp_type1st,b.yarn_type ");
		foreach($sql_recv_ret as $row)
		{
			$recv_return_qty_arr[$row[csf('company_id')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['recv_ret']=$row[csf('recv_return_qty')];
		}
		
		$wo_pi_qty_arr=array();
		$sql_pi=sql_select("select b.count_name as yarn_count_id, a.importer_id as company_id,b.yarn_composition_item1 as yarn_comp_type1st,b.yarn_type,sum(b.quantity) as quantity  from  com_pi_master_details a ,com_pi_item_details b where    a.id=b.pi_id  and a.item_category_id=1 and  a.status_active=1 and a.is_deleted=0  and  b.status_active=1 and b.is_deleted=0 $company_cond3 group by b.count_name, a.importer_id,b.yarn_composition_item1,b.yarn_type ");
		foreach($sql_pi as $row)
		{
			$wo_pi_qty_arr[$row[csf('company_id')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['pi_qty']=$row[csf('quantity')];
		}
	
//c.copm_two_id as yarn_comp_type2nd,c.percent_two,c.percent_one as yarn_comp_percent1st
	$company_data_arr=array();
	$sql_data="select a.company_name as company_id,sum(b.po_quantity*a.total_set_qnty) as po_quantity, c.count_id,c.copm_one_id as yarn_comp_type1st,c.type_id as yarn_type from wo_po_break_down b,wo_po_details_master a,wo_pre_cost_fab_yarn_cost_dtls c where  c.job_no=a.job_no and c.job_no=b.job_no_mst and a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0  $job_company_cond $date_cond $buyer_id_cond group by a.company_name,c.count_id,c.copm_one_id,c.type_id order by  a.company_name, c.count_id,c.copm_one_id,c.type_id"; 
	
	$result_data=sql_select($sql_data);
		foreach($result_data as $row)
		{
			//$yarn_data_arr[$row[csf('company_id')]][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['issue_qnty']=$row[csf('issue_qnty')];
			$company_data_arr[$row[csf('company_id')]]=$row[csf('company_id')];
			
		} 
ob_start();	
			$div_width=960+count($company_data_arr)*300;
	?>
        <div style="width:<? echo $div_width ?>px" align="center">
        <fieldset style="width:100%;" align="center">	
            <table width="<? echo $div_width ?>">
                <tr class="form_caption">
                    <td colspan="6" align="center">Consolidate Yarn Required and Issue Status</td>
                </tr>
                <tr class="form_caption">
                    <td colspan="6" align="center"><? echo $company_library[$company_name]; ?></td>
                </tr>
            </table>
           <? //echo $div_width;?>
            <table id="table_header_1" class="rpt_table" width="<? echo $div_width ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                    <th width="40" rowspan="2">SL</th>
                    <th width="100" rowspan="2">Count</th>
                    <th width="200" rowspan="2">Composition</th>
                    <th width="120" rowspan="2">Type</th>
                      <?
					foreach($company_data_arr as $value)
					{
					?>
                      <th width="300"  colspan="3"><? echo  $company_library[$value];?></th>
                    <?
					}
					?>
                  
                    <th width="300" colspan="3">Total</th>
                    <th width="200" colspan="2" >Status</th>
                </tr>
                <tr>
                <?
               for($z=0;$z<count($company_data_arr);$z++)
					{
					?>
                     <th width="100">Required</th> 
                    <th width="100">Issued</th>
                    <th width="100">Balance</th>
                    <? 
					}
					?>
                 <th width="100">Required</th> 
                <th width="100">Issued</th>
                <th width="100">Balance</th>
                 <th width="100">Stock Qty(AS On)</th>
                <th width="">LC Balance(AS On)</th>
                </tr>
                </thead>
            </table>
            <? //echo $div_width+18;//echo die; ?>
            <div style="width:<? echo $div_width+20 ?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="<? echo $div_width ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
              	 $i=1; $company_required_array=array(); $company_issue_array=array(); $company_bl_array=array();  //$total_order_qnty=0;
				foreach( $result_data as $row)
				{
					//echo $com_val;
					//foreach($com_val as )
						//echo $composition[$row[csf('yarn_comp_type2nd')]].'d';		  
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="100"  align="center"><p><? echo $count_arr[$row[csf('count_id')]]; ?></p></td>
                        <td width="200"><? echo $composition[$row[csf('yarn_comp_type1st')]].' '.$row[csf('yarn_comp_percent1st')].' '.$composition[$row[csf('yarn_comp_type2nd')]];?></td>
                        <td width="120"><? echo $yarn_type[$row[csf('yarn_type')]];?></td>
                     <? 
                        // for($z=0;$z<count($company_arr);$z++)
						//{ 
						//echo $z;
						$req_qty=0;$balance_qty=0;$tot_req=0;$tot_issue_qty=0;$tot_balance_qty=0;$company_required_qnty=0;
						foreach($company_data_arr as $key=>$value)
						{
							//echo $company_id=$key;//[$row[csf('company_id')]];
						
						//$issue_return_qty=$row[csf('issue_return_qnty')];
						$costing_per_id= $costing_per_arr[$key]['costing_per'];
						$cons_qty_per= $fabriccostArray[$key][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['cons_qty'];
						$dzn_qnty=0;
						
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						$po_qty=$row[csf('po_quantity')];
						$ratio=$row[csf('total_set_qnty')];
						$yarn_req_qty=$dzn_qnty;
						$req_qty=($po_qty/$yarn_req_qty)*$cons_qty_per;//$booking_qnty_arr[$key][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['req_qty'];
						$current_stock=$stock_qty_arr[$key][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['stock'];
						
						$issue_qty=$issue_qty_arr[$key][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['issue_qnty'];
						$issue_return_qty=$issue_qty_arr[$key][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['issue_ret_qnty'];
						
						$recv_qty=$recv_qty_arr[$key][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['recv'];
						$recv_return_qty=$recv_return_qty_arr[$key][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['recv_ret'];
						$pi_qty=$wo_pi_qty_arr[$key][$row[csf('count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_type')]]['pi_qty'];
						//$booking_qnty_arr[$key][$row[csf('po_breakdown_id')]]['req_qty'];
						$tot_pi_qnty=$pi_qty-$recv_qty+$recv_return_qty;
						$tot_issue=$issue_qty-$issue_return_qty;
						$balance_qty=$req_qty-$tot_issue;
						$tot_req=$req_qty;
						$tot_issue_qty=$tot_issue;
						$tot_balance_qty=$balance_qty;
						
						$total_pi_balance+=$tot_pi_qnty;
						
						if (array_key_exists($key, $company_required_array)) 
						{
						 	$company_required_array[$key]+=$tot_req;
						}
						else
						{
							$company_required_array[$key]=$tot_req;	
						}
						if (array_key_exists($key, $company_issue_array)) 
						{
						 	$company_issue_array[$key]+=$tot_issue_qty;
						}
						else
						{
							$company_issue_array[$key]=$tot_issue_qty;	
						}
						
						if (array_key_exists($key, $company_bl_array)) 
						{
						 	$company_bl_array[$key]+=$tot_balance_qty;
						}
						else
						{
							$company_bl_array[$key]=$tot_balance_qty;	
						}
						?>
                       
                          <td width="100" align="right"><? echo number_format($req_qty,2); //$row[csf('issue_qnty')]; ?></td> 
                          <td width="100" align="right"><? echo number_format($tot_issue,2); ?></td> 
                          <td width="100" align="right"><? echo number_format($balance_qty,2); ?></td>  
                      	 <?   
                    	} ?>
                         <td width="100"  align="right"><? echo number_format($tot_req,2); ?></td>
                         <td width="100"  align="right"><? echo number_format($tot_issue_qty,2); ?></td>
                         <td width="100"  align="right"><? echo number_format($tot_balance_qty,2); ?></td>
                              
                        <td width="100"  align="right"><? echo number_format($current_stock,2); ?></td>
                        <td width=""  align="right"><? echo number_format($tot_pi_qnty,2); ?></td>
                    </tr>
                <?
				$total_req_qty+=$tot_req;
				$total_issue_qty+=$tot_issue_qty;
				$total_balance_qty+=$tot_balance_qty;
				
				$total_current_stock+=$current_stock;
				$total_tot_pi_balance+=$tot_pi_qnty;
               $i++;
				}
				//echo $div_width;
                ?>
                </table>
               <table class="rpt_table" rules="all" border="1" cellpadding="0" cellspacing="0" width="<? echo $div_width ?>" >
        		<tr bgcolor="#EFEFEF">
                    <td width="40">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="200">&nbsp;</td>
                    <td width="120" align="right"><strong>Total&nbsp;&nbsp;</strong></td>
                    <?
                    $zz=1;
                    foreach($company_data_arr as $key=>$value)
                    {
                    ?>
                        <td align="right" width="100"><strong><? echo number_format($company_required_array[$key],2); ?></strong></td>
                        <td align="right" width="100"><strong><? echo number_format($company_issue_array[$key],2); ?></strong></td>
                        <td align="right" width="100"><strong><? echo number_format($company_bl_array[$key],2); ?></strong></td>
                    <?	
                    $zz++;
                    }
                    ?>
                    <td align="right" width="100" id="value_grand_tot_req_qnty"><strong><? echo number_format($total_req_qty,2); ?></strong></td>
                    <td align="right" width="100" id="value_grand_tot_iss_qnty"><strong><? echo number_format($total_issue_qty,2); ?></strong></td>
                    <td align="right" width="100" id="value_grand_tot_bl_qnty"><strong><? echo number_format($total_balance_qty,2); ?></strong></td>
                    
                    <td align="right" width="100" ><strong><? echo number_format($total_current_stock,2); ?></strong></td>
                    <td align="right" width="" ><strong><? echo number_format($total_tot_pi_balance,2); ?></strong></td>
            </tr>
     	   </table>
               
            </div>
            </fieldset>
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
    echo "$html####$filename"; 
	exit();	
}
?>