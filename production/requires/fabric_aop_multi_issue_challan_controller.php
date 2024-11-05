<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="load_drop_down_delivery_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_delivery_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Delivery To
        --", "$company_id", "fnc_load_address(this.value);partyChange();","" );
	}
	else if($data[0]==3)
	{
        echo create_drop_down( "cbo_delivery_name", 160, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$company_id and b.party_type=25 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Delivery To--",$selected,"fnc_load_address(this.value);partyChange();",0 );
	}
	else
	{
		echo create_drop_down( "cbo_delivery_name", 160, $blank_array,"",1, "--Select Delivery To --", 1, "partyChange();" );
	}
	exit();
}

if($action=="return_deli_com_address")
{
    $data = explode("_",$data);
    if($data[1]== 3)
    {
        $address=return_field_value( "address_1","lib_supplier","id='$data[0]'");
        echo $address;
        exit();	
    }else{
        $address=return_field_value( "city","lib_company","id='$data[0]'");
        echo $address;
        exit();
    }
}
	
//====================Location ACTION========


if ($action == "load_drop_down_party") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		echo create_drop_down("cbo_party_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 0, "--Select Party--", "$company_id", "", "");
	} else if ($data[0] == 2) {

		/*$partysQl = sql_select("select id,tag_company,party_type from lib_buyer where status_active=1 and is_deleted=0 and tag_company='".$company_id."'");

		$buyerId = "";
		foreach ($partysQl as $row) {

			$partyTypeArr = explode(",", $row[csf('party_type')]);

			foreach ($partyTypeArr as $partyType) {
				if($partyType == 3)
				{
					$buyerId .=  $row[csf('id')].",";
				}
			}
		}

		$buyerIds = chop($buyerId,",");

		if($buyerIds!="")
		{
			echo create_drop_down("cbo_party_id", 160, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 and id in($buyerIds)", "id,buyer_name", 0, "--Select Party--", 1, "");
		}*/

		echo create_drop_down("cbo_party_id", 160, "select c.id, c.buyer_name from lib_buyer_tag_company a, lib_buyer_party_type b, lib_buyer c where a.buyer_id  = b.buyer_id and a.buyer_id = c.id and a.tag_company=$company_id and b.party_type=3 group by c.id, c.buyer_name order by c.buyer_name", "id,buyer_name", 0, "--Select Party--", 1, "");
	}
	exit();
}

if($action=='list_view_garments')
{
	$process = array( &$_POST );
	//var_dump($process);
	extract(check_magic_quote_gpc( $process ));

	$company_name=str_replace("'","",$cbo_company_id);
	$cbo_party_id=str_replace("'","",$cbo_party_id);
	$txt_fso_no=str_replace("'","",$txt_fso_no);
	$hdn_challan_id=str_replace("'","",$hdn_challan_id);


	//$hdn_fso_id=str_replace("'","",$hdn_fso_id);
	$txt_po_job=str_replace("'","",$txt_po_job);
	$list_view_type=str_replace("'","",$list_view_type);

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
	$company_arr 	= return_library_array("select id,company_name from lib_company", 'id', 'company_name');

	$fsoids= "'".implode("','",array_unique(explode(",", $hdn_fso_id)))."'";

	if($fsoids!="")
	{
		$fso_id_cond= "and g.order_id in($fsoids)";
		$rcv_fso_id_cond= "and fso_id in($fsoids)";
	}else {
		$fso_id_cond = "";
		$rcv_fso_id_cond="";
	}

	if($hdn_challan_id!="")
	{
		$challan_id_cond= "and g.mst_id in($hdn_challan_id)";
	}else{
		$challan_id_cond="";
	}

	// === All ready Received ommit here ==== start //
	$currenitDeliverCond = "";
	if($update_id!="")
	{
		$currenitDeliverCond = "and mst_id not in ($update_id)";
	}else{
		$currenitDeliverCond = "";
	}

	$sql_receieved  = sql_select("select delivery_dtls_id from aop_multi_issue_dtls where status_active=1 and is_deleted=0 $currenitDeliverCond $rcv_fso_id_cond");
	foreach ($sql_receieved as $row) {
		$delivery_dtls_id .= $row[csf('delivery_dtls_id')].",";
	}

	$all_receieved_id = chop($delivery_dtls_id,",");
	if($all_receieved_id!="")
	{
		$all_receieved_idsArr=array_unique(explode(",",$all_receieved_id));
		if($db_type==2 && count($all_receieved_idsArr)>999)
		{
			$received_cond=" and (";
			$all_receieved_idsArr=array_chunk($all_receieved_idsArr,999);
			foreach($all_receieved_idsArr as $receieved_id)
			{
				$receieved_ids=implode(",",$receieved_id);
				$received_cond.="g.id not in($receieved_ids) and ";
			}

			$received_cond=chop($received_cond,'and ');
			$received_cond.=")";
		}
		else
		{
			$received_cond=" and g.id not in (".implode(",",$all_receieved_idsArr).")";
		}

	}

	$batch_dtls_id_sql = "select g.batch_dtls_id from wo_fabric_aop_dtls g where  g.is_deleted=0 and g.status_active=1 $challan_id_cond ";
	//echo $batch_dtls_id_sql;
	$batch_dtls_sqlArray=sql_select($batch_dtls_id_sql); $prev_issue_qty_arr=array();
	foreach ($batch_dtls_sqlArray as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$prev_issue_qty_arr[$row[csf("batch_id")]][$row[csf("batch_dtls_id")]]["quantity"] +=$row[csf("quantity")];
		$batch_dtls_id .=$row[csf("batch_dtls_id")].',';
	}
	$batch_dtls_id=chop($batch_dtls_id,',');
	$batch_dtls_id=implode(",",array_unique(explode(",",$batch_dtls_id)));
	if($batch_dtls_id!=''){
		$batch_dtls_id_cond =" and d.id in ($batch_dtls_id)";
	}

	$mainQuery= "SELECT c.id, c.company_id, c.po_buyer AS buyer_id, c.style_ref_no, c.job_no AS fso_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description AS fabric_desc, h.gsm AS gsm_weight, h.dia_width AS dia, g.batch_id, g.id AS dtls_id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,g.prod_id, a.sys_no as challan_no,a.sys_date,g.order_id, d.body_part_id, a.id as delivery_id
	FROM  fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_fabric_aop_dtls g, product_details_master h, wo_fabric_aop_mst a
	WHERE  a.company_id=$company_name and a.id=g.mst_id and c.id = d.po_id AND d.mst_id = e.id AND g.batch_id = e.id AND d.prod_id = h.id and d.mst_id=e.id and g.batch_id=e.id and d.prod_id=h.id and d.prod_id = g.prod_id $challan_id_cond $fso_id_cond $received_cond $batch_dtls_id_cond and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.status_active=1 and g.is_deleted=0 and h.is_deleted=0 and h.status_active=1 group by  c.id, c.company_id, c.po_buyer, c.style_ref_no, c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description, h.gsm, h.dia_width, g.batch_id, g.id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,g.prod_id, a.sys_no,a.sys_date,g.order_id, d.body_part_id, a.id order by a.id desc";

	//echo $mainQuery;//die;

	$mainQueryResult = sql_select($mainQuery);

	if(empty($mainQueryResult))
	{
		echo "<span style='color:red; font-weight:bold; font-size:14px;'><center>No Data Found</center></span>";
		exit();
	}

	$maniDataArr = array();
	foreach ($mainQueryResult as  $row)
	{
		$batch_id_arr[] = $row[csf("batch_id")];
		$color_id_arr[] = $row[csf("color_id")];
		$salesOrderIds .= $row[csf('order_id')].",";
		$challan_ids .= $row[csf('delivery_id')].",";
	}

	$salesOrderIds = implode(",", array_filter(array_unique(explode(",",chop($salesOrderIds,",")))));

	$all_challan_id = chop($challan_ids,",");
	if($all_challan_id!="")
	{
		$all_challan_idsArr=array_unique(explode(",",$all_challan_id));
		if($db_type==2 && count($all_challan_idsArr)>999)
		{
			$challan_cond=" and (";
			$all_challan_idsArr=array_chunk($all_challan_idsArr,999);
			foreach($all_challan_idsArr as $challan_id)
			{
				$challanids=implode(",",$challan_id);
				$challan_cond.="delivery_id in($challanids) or ";
			}

			$challan_cond=chop($challan_cond,'or ');
			$challan_cond.=")";
		}
		else
		{
			$challan_cond=" and delivery_id in (".implode(",",$all_challan_idsArr).")";
		}
	}

	if($update_id=="")
	{
		// echo "select delivery_id,delivery_dtls_id,remarks as detailsremarks from aop_multi_issue_dtls where status_active=1 and is_deleted=0 $challan_cond";
		$sql_challan=sql_select("select delivery_id,delivery_dtls_id,remarks as detailsremarks from aop_multi_issue_dtls where entry_form=522 and status_active=1 and is_deleted=0 $challan_cond");

		foreach ($sql_challan as $row)
		{
			$receFabric[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['challan'] = $row[csf('delivery_dtls_id')];
			$receFabric[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['detailsremarks'] = $row[csf('detailsremarks')];
			$chkExistngDelvChaln[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['delivery_dtls_id']=$row[csf('delivery_dtls_id')];

		}
		
	}
	else
	{
		$sql_challan=sql_select("select delivery_id,delivery_dtls_id,remarks as detailsremarks from aop_multi_issue_dtls where entry_form=522 and status_active=1 and is_deleted=0 and mst_id=$update_id  $challan_cond");
		$sql_challan_Exis=sql_select("select delivery_id,delivery_dtls_id,remarks as detailsremarks from aop_multi_issue_dtls where entry_form=522 and status_active=1 and is_deleted=0 and mst_id not in($update_id)  $challan_cond");

		foreach ($sql_challan as $row)
		{
			$receFabric[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['challan'] = $row[csf('delivery_dtls_id')];
			$receFabric[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['detailsremarks'] = $row[csf('detailsremarks')];
		}
		foreach ($sql_challan_Exis as $row)
		{
			$chkExistngDelvChaln[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['delivery_dtls_id']=$row[csf('delivery_dtls_id')];

		}

	}
	
	
	/*echo "<pre>";
	print_r($receFabric);*/
	// echo "select id,job_no_prefix_num,season,style_ref_no,po_company_id,company_id,po_buyer,buyer_id,within_group from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in($salesOrderIds)";
	$fso_sql = sql_select("select id,job_no_prefix_num,season,style_ref_no,po_company_id,company_id,po_buyer,buyer_id,within_group from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in($salesOrderIds)");
	$salesOrderData = array();
	foreach ($fso_sql as $row) {
		$salesOrderData[$row[csf('id')]]['po_buyer'] 		=  $row[csf('po_buyer')];
		$salesOrderData[$row[csf('id')]]['buyer_id'] 		=  $row[csf('buyer_id')];
		$salesOrderData[$row[csf('id')]]['within_group'] 	=  $row[csf('within_group')];
	}
	//var_dump($salesOrderData);

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	if(!empty($batch_id_arr)){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).") and status_active=1 and is_deleted=0","id","batch_no");
	}

	$color_arr=array();
	if(!empty($color_id_arr)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).") and status_active=1 and is_deleted=0",'id','color_name');
	}
	?>

        <table width="1775" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
            <thead>
            	<th width="60">Check All <input id="all_check" onClick="check_all('all_check')" type="checkbox"> </th>
                <th width="40">SL</th>
                <th width="80">Challan No</th>
                <th width="80">Issue Date </th>
                <th width="80">Buyer</th>
                <th width="100">Booking No</th>
                <th width="100">FSO No</th>
                <th width="80">Style Reff</th>
                <th width="80">Process Type</th>
                <th width="80">Batch No</th>
                <th width="80">Fab Color</th>
                <th width="80">Bodypart</th>
                <th width="100">Fabric Description</th>
                <th width="80">Actual GSM</th>
                <th width="80">Actual DIA</th>
                <th width="80">No. Of Roll</th>
                <th width="80">Issue Qty</th>
                <th width="80">Rate</th>
                <th width="80">Amount </th>
                <th width="">Remarks</th>
            </thead>

        </table>

        <div style="width:1780px; overflow-y:scroll; max-height:350px;" id="scroll_body">
        <table width="1763" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="tbl_list_search">
        	<tbody>
        	<?php
        	$i=1;
        	$buyerName = "";
        	foreach ($mainQueryResult as  $row)
        	{
    			if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";

				if($salesOrderData[$row[csf('order_id')]]['within_group']==1)
				{
					$partyName = $row[csf('po_company_id')];
					$buyerName = $buyer_arr[$salesOrderData[$row[csf('order_id')]]['po_buyer']];
					$buyerId   = $salesOrderData[$row[csf('order_id')]]['po_buyer'];

				}else{
					$partyName = $row[csf('company_id')];
					$buyerName = $buyer_arr[$salesOrderData[$row[csf('order_id')]]['buyer_id']];
					$buyerId   = $salesOrderData[$row[csf('order_id')]]['buyer_id'];
				}
				if($update_id!="")
				{

					if($chkExistngDelvChaln[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['delivery_dtls_id']!=  $row[csf('dtls_id')])
					{



						if( $receFabric[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan']!="" &&  ($receFabric[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan']) ==  $row[csf('dtls_id')] && $update_id!="")
						{
							$checkedRow = "checked='checked'";
						}else {
							$checkedRow = "";
						}
	        			?>
				            <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
				                <td width="60" align="center">
				                	<input id="check<? echo $i ?>_<? echo  $row[csf('challan_no')];?>"  type="checkbox"  <? echo $checkedRow;?>

				                	  value="<? echo $row[csf('order_id')]."**".$row[csf('within_group')]."**".$row[csf('sales_booking_no')]."**".$row[csf('delivery_id')]."**".$row[csf('dtls_id')]."**".$row[csf('prod_id')]."**".$row[csf('batch_id')]."**".$row[csf('color_id')]."**".$row[csf('rate')]."**".$row[csf('fabric_desc')]."**".$row[csf('gsm_weight')]."**".$row[csf('dia')]."**".$row[csf('body_part_id')]."**".$row[csf('process_type_id')]."**".$row[csf('quantity')]."**".$row[csf('number_of_roll')]."**".$row[csf('amount')]."**".$buyerId; ?>"
				                	>
				                </td>

				                <td width="40"><p><? echo $i;?></p> </td>
				                <td width="80" align="center"><p><? echo $row[csf('challan_no')];?></p></td>
				                <td width="80" align="center"><p><? echo change_date_format($row[csf('sys_date')]);?></p></td>
				          
				                <td width="80"><p><? echo $buyerName;?></p></td>
				                <td width="100" align="center"><p><? echo $row[csf('sales_booking_no')];?></p></td>
				                <td width="100" align="center"><p><? echo $row[csf('fso_no')];?></p></td>
				                <td width="80"><p><? echo $row[csf('style_ref_no')];?></p></td>
				                <td width="80" align="center"><p><? echo $emblishment_print_type[$row[csf('process_type_id')]];?></p></td>
				                <td width="80" align="center"><p><? echo $batch_arr[$row[csf('batch_id')]];?></p></td>
				                <td width="80"><p><? echo $color_arr[$row[csf('color_id')]];?></p></td>
				                <td width="80"><p><? echo $body_part[$row[csf('body_part_id')]];?></p></td>
				                <td width="100"><p><? echo $row[csf('fabric_desc')];?></p>  </td>
				                <td width="80" align="center"> <p><? echo $row[csf('gsm_weight')] ; ?></p></td>
				                <td width="80" align="center"><p><? echo $row[csf('dia')];?></p></td>
				                <td width="80" align="center"><p><?php echo $row[csf('number_of_roll')];?></p>  </td>
				                <td width="80" align="right"><p><? echo $row[csf('quantity')];?></p></td>
				                <td width="80" align="center"><p><?php echo $row[csf('rate')];?></p></td>
				                <td width="80" align="center"><p><?php echo $row[csf('amount')];?></p></td>
				                <td width=""><p><input type="text" name="text_dtls_remarks[]" id="text_dtls_remarks_<? echo $i;?>" value="<? echo $receFabric[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['detailsremarks'];?>"  style="width: 200px;" placeholder="write"></p></td>
				            </tr>

	        			<?php

		        		$i++;
		        	}
	        	}
	        	else
	        	{
	        		if($chkExistngDelvChaln[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['delivery_dtls_id']!=$row[csf('dtls_id')] && $update_id=="")
	        		{
	        			if( $receFabric[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan']!="" &&  ($receFabric[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan']) ==  $row[csf('dtls_id')] && $update_id!="")
						{
							$checkedRow = "checked='checked'";
						}else 
						{
							$checkedRow = "";
						}
        				?>
			            <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
			                <td width="60" align="center">
			                	<input id="check<? echo $i ?>_<? echo  $row[csf('challan_no')];?>"  type="checkbox"  <? echo $checkedRow;?>

			                	  value="<? echo $row[csf('order_id')]."**".$row[csf('within_group')]."**".$row[csf('sales_booking_no')]."**".$row[csf('delivery_id')]."**".$row[csf('dtls_id')]."**".$row[csf('prod_id')]."**".$row[csf('batch_id')]."**".$row[csf('color_id')]."**".$row[csf('rate')]."**".$row[csf('fabric_desc')]."**".$row[csf('gsm_weight')]."**".$row[csf('dia')]."**".$row[csf('body_part_id')]."**".$row[csf('process_type_id')]."**".$row[csf('quantity')]."**".$row[csf('number_of_roll')]."**".$row[csf('amount')]."**".$buyerId; ?>"
			                	>
			                </td>

			                <td width="40"><p><? echo $i;?></p> </td>
			                <td width="80" align="center"><p><? echo $row[csf('challan_no')];?></p></td>
			                <td width="80" align="center"><p><? echo change_date_format($row[csf('sys_date')]);?></p></td>
			         
			                <td width="80"><p><? echo $buyerName;?></p></td>
			                <td width="100" align="center"><p><? echo $row[csf('sales_booking_no')];?></p></td>
			                <td width="100" align="center"><p><? echo $row[csf('fso_no')];?></p></td>
			                <td width="80"><p><? echo $row[csf('style_ref_no')];?></p></td>
			                <td width="80" align="center"><p><? echo $emblishment_print_type[$row[csf('process_type_id')]];;?></p></td>
			                <td width="80" align="center"><p><? echo $batch_arr[$row[csf('batch_id')]];?></p></td>
			                <td width="80"><p><? echo $color_arr[$row[csf('color_id')]];?></p></td>
			                <td width="80"><p><? echo $body_part[$row[csf('body_part_id')]];?></p></td>
			                <td width="100"><p><? echo $row[csf('fabric_desc')];?></p>  </td>
			                <td width="80" align="center"> <p><? echo $row[csf('gsm_weight')] ; ?></p></td>
			                <td width="80" align="center"><p><? echo $row[csf('dia')];?></p></td>
			                <td width="80" align="center"><p><?php echo $row[csf('number_of_roll')];?></p>  </td>
			                <td width="80" align="right"><p><? echo $row[csf('quantity')];?></p></td>
			                <td width="80" align="center"><p><?php echo $row[csf('rate')];?></p></td>
			                <td width="80" align="center"><p><?php echo $row[csf('amount')];?></p></td>
			                <td width=""><p><input type="text" name="text_dtls_remarks[]" id="text_dtls_remarks_<? echo $i;?>" value="<? echo $receFabric[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['detailsremarks'];?>"  style="width: 200px;" placeholder="write"></p></td>
			            </tr>

	        			<?php

		        		$i++;

	        		}
	        	}
    		}
        	?>
        	</tbody>
        </table>
   	</div>

    <?
	exit;
}


if ($action=="save_update_delete")
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

		$aop_multi_issue_num=''; $aop_multi_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("AOP_MULTI_ISS_MST_PK_SEQ", "aop_multi_issue_mst", $con);//FAB_AOP_MULTI_ISSUE_CHALLAN_MST , FAB_AOP_MULTI_CH_MST_PK_SEQ
			
			$new_fab_aop_multi_issue_system_id = explode("*", return_next_id_by_sequence("AOP_MULTI_ISS_MST_PK_SEQ", "aop_multi_issue_mst",$con,1,$company_id,'FAMIC',522,date("Y",time()),2 ));

			if($db_type==0){
				$multi_challan_date=change_date_format(str_replace("'",'',$multi_challan_date),'yyyy-mm-dd');
			}else{
				$multi_challan_date=change_date_format(str_replace("'",'',$multi_challan_date), "", "",1);
			}

			$field_array="id, sys_number_prefix, sys_number_prefix_num, sys_number, entry_form, company_id, vehicle_no, driver_name, dl_no, transport,mobile_no, gate_pass_no, remarks,delivery_to,delivery_address, issue_purpose,service_source, delevery_date, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_fab_aop_multi_issue_system_id[1]."',".$new_fab_aop_multi_issue_system_id[2].",'".$new_fab_aop_multi_issue_system_id[0]."',522,".$company_id.",'".$vehicle_no."','".$driver_name."','".$dl_no."','".$transport."','".$mobile_no."','".$gate_pass_no."','".$remarks."','".$delivery_to."','".$delivery_address."','".$issue_purpose."','".$service_source."','".$multi_challan_date."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$field_details_array = "id, mst_id, fso_id, within_group, booking_no, delivery_id, delivery_dtls_id, product_id, entry_form, batch_id, color_id, is_sales, item_description,gsm, dia, bodypart_id, issue_qnty, remarks, roll_no,process_type_id, rate, amount, buyer_id, inserted_by, insert_date";

			if($detailsData!="")
			{
				$detailsDataArr = explode("___",$detailsData);

				foreach ($detailsDataArr as $data_string)
				{
					$dataArr = explode("**",$data_string);
					$delivery_dtls_Id =  $dataArr[4];

					$all_delivery_dtls_Ids .= $delivery_dtls_Id.",";
				}

				$all_delivery_dtls_Ids = chop($all_delivery_dtls_Ids,",");
				$pre_multi_deli = sql_select("select a.id, a.sys_number from aop_multi_issue_mst a, aop_multi_issue_dtls b  where a.id = b.mst_id and a.entry_form=522 and b.entry_form=522 and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 and b.delivery_dtls_id in ($all_delivery_dtls_Ids)");

				if($pre_multi_deli[0][csf("sys_number")])
				{
					echo "20**Reference Attached with Another Multi Issue Challan.\Multi Issue Challan No: ".$pre_multi_deli[0][csf("sys_number")];
					disconnect($con);die;
				}
				
				//$dtls_id =return_next_id( "id","aop_multi_issue_dtls", 1 ) ;

				
				
				$data_array_dtls = ""; $all_delivery_dtls_Ids="";
				$k=1;
				foreach ($detailsDataArr as $data_string)
				{
					$dtls_id = return_next_id_by_sequence("AOP_MULTI_ISS_DTLS_PK_SEQ", "aop_multi_issue_dtls", $con); 
					$dataArr = explode("**",$data_string);

				    $fso = $dataArr[0];
				    $within_group = $dataArr[1];
				    $booking_no = $dataArr[2];
				    $delivery_id = $dataArr[3];
				    $delivery_dtls_Id =  $dataArr[4];
				    $product_id =  $dataArr[5];
				    $entry_form = 522;
				    $batch_id = $dataArr[6];
				    $color_id = $dataArr[7];
				    $is_sales = 1;
				    $rate = $dataArr[8];
				    $fabric_desc = $dataArr[9];
				    $gsm = $dataArr[10];
				    $dia = $dataArr[11];
				    $bodypart_id = $dataArr[12];
				    $process_type = $dataArr[13];
				    $issue_qty = $dataArr[14];
				    $roll_no = $dataArr[15];
				    $amount = $dataArr[16];
				    $buyer_id = $dataArr[17];
				    $detailsremarks = "details_remarks_".$k;
					//echo "5**".$buyer_id;die;
				
					if ($data_array_dtls != "") $data_array_dtls .= ",";

					$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'".$fso."'," . $within_group . ",'" . $booking_no . "','" . $delivery_id . "','" . $delivery_dtls_Id . "','" . $product_id . "','" . $entry_form . "','" . $batch_id . "','" . $color_id . "','" . $is_sales . "','" . $fabric_desc . "','" . $gsm . "','" . $dia . "','" . $bodypart_id . "','" . $issue_qty . "','" . $$detailsremarks . "','" . $roll_no . "','" . $process_type . "','" . $rate . "','" . $amount  . "','" . $buyer_id  . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$k++;
				}
			}

			$aop_multi_issue_num=$new_fab_aop_multi_issue_system_id[0];
			$aop_multi_update_id=$id;
		}

		if(str_replace("'","",$update_id)=="")
		{
			//echo "5**insert into aop_multi_issue_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("aop_multi_issue_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;

			//echo "5**insert into aop_multi_issue_dtls (".$field_details_array.") values ".$data_array_dtls;die;
			$rID2=sql_insert("aop_multi_issue_dtls",$field_details_array,$data_array_dtls,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}

		}

		 //echo "5**".$rID."**".$rID2;die;
		// echo "10**".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$aop_multi_update_id."**".$aop_multi_issue_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0**$list_view_type";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$aop_multi_update_id."**".$aop_multi_issue_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0**$list_view_type";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
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
	
		if($db_type==0){
			$multi_challan_date=change_date_format(str_replace("'",'',$multi_challan_date),'yyyy-mm-dd');
		}else{
			$multi_challan_date=change_date_format(str_replace("'",'',$multi_challan_date), "", "",1);
		}

		$field_array_update="company_id*vehicle_no*driver_name*dl_no*transport*mobile_no*gate_pass_no*remarks*delivery_to*delivery_address*issue_purpose*service_source*delevery_date*updated_by*update_date";

		$data_array_update = $company_id."*'".$vehicle_no."'*'".$driver_name."'*'".$dl_no."'*'".$transport."'*'".$mobile_no."'*'".$gate_pass_no."'*'".$remarks."'*'".$delivery_to."'*'".$delivery_address."'*'".$issue_purpose."'*'".$service_source."'*'".$multi_challan_date."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$aop_multi_issue_num=str_replace("'","",$txt_system_id);
		$aop_multi_update_id=str_replace("'","",$update_id);

		$field_details_array = "id, mst_id, fso_id, within_group, booking_no, delivery_id, delivery_dtls_id, product_id, entry_form, batch_id, color_id, is_sales, rate, item_description,gsm, dia, bodypart_id, issue_qnty, remarks, roll_no,process_type_id,  amount, buyer_id, inserted_by, insert_date";

		if($detailsData!="")
		{
			$detailsDataArr = explode("___",$detailsData);

			
			//$dtls_id =return_next_id( "id","pro_fin_deli_multy_challa_dtls", 1 ) ;

			foreach ($detailsDataArr as $data_string)
			{
				$dataArr = explode("**",$data_string);
				$delivery_dtls_Id =  $dataArr[4];

				$all_delivery_dtls_Ids .= $delivery_dtls_Id.",";
			}

			$all_delivery_dtls_Ids = chop($all_delivery_dtls_Ids,",");
			$pre_multi_deli = sql_select("select a.id, a.sys_number from aop_multi_issue_mst a, aop_multi_issue_dtls b  where a.id = b.mst_id and a.entry_form=522 and b.entry_form=522 and a.status_active = 1 and a.is_deleted and b.status_active = 1 and b.is_deleted and a.id != $aop_multi_update_id and b.delivery_dtls_id in ($all_delivery_dtls_Ids)");

			if($pre_multi_deli[0][csf("sys_number")])
			{
				echo "20**Referece Attached with Another Multi Issue Challan.\Multi Issue Challan No: ".$pre_multi_deli[0][csf("sys_number")];
				disconnect($con);die;
			}

			$data_array_dtls = "";
			$k = 1;

			foreach ($detailsDataArr as $data_string)
			{
				$dtls_id = return_next_id_by_sequence("AOP_MULTI_ISS_DTLS_PK_SEQ", "aop_multi_issue_dtls", $con);
				$dataArr = explode("**",$data_string);

			    $fso = $dataArr[0];
			    $within_group = $dataArr[1];
			    $booking_no = $dataArr[2];
			    $delivery_id = $dataArr[3];
			    $delivery_dtls_Id =  $dataArr[4];
			    $product_id =  $dataArr[5];
			    $entry_form = 522;
			    $batch_id = $dataArr[6];
			    $color_id = $dataArr[7];
			    $is_sales = 1;
			    $rate = $dataArr[8];
			    $fabric_desc = $dataArr[9];
			    $gsm = $dataArr[10];
			    $dia = $dataArr[11];
			    $bodypart_id = $dataArr[12];
			    $process_type = $dataArr[13];
			    $issue_qty = $dataArr[14];
			    $roll_no = $dataArr[15];
			    $amount = $dataArr[16];
				$buyer_id = $dataArr[17];
			    $detailsremarks = "details_remarks_".$k;
				//echo "5**".$buyer_id;die;
				if ($data_array_dtls != "") $data_array_dtls .= ",";
			
				$data_array_dtls .= "(" . $dtls_id . "," . $aop_multi_update_id . ",'".$fso."'," . $within_group . ",'" . $booking_no . "','" . $delivery_id . "','" . $delivery_dtls_Id . "','" . $product_id . "','" . $entry_form . "','" . $batch_id . "','" . $color_id . "','" . $is_sales . "','" . $rate . "','" . $fabric_desc . "','" . $gsm . "','" . $dia . "','" . $bodypart_id . "','" . $issue_qty . "','" . $$detailsremarks . "','" . $roll_no . "','" . $process_type . "','" . $amount . "','" . $buyer_id . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				//$dtls_id = $dtls_id+1;
				$k++;
			}
		}
		
		$rID = sql_update("aop_multi_issue_mst",$field_array_update,$data_array_update,"id",$aop_multi_update_id,0);

		$rID_Delete = execute_query("delete FROM aop_multi_issue_dtls WHERE mst_id = $aop_multi_update_id",1);

		if($rID) $flag=1; else $flag=0;

		//echo "5**insert into aop_multi_issue_dtls (".$field_details_array.") values ".$data_array_dtls;die;

		$rID2=sql_insert("aop_multi_issue_dtls",$field_details_array,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		//echo "10**$rID** $rID2**$rID_Delete";die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $aop_multi_issue_num)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**0**1**$list_view_type";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $aop_multi_issue_num)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1**$list_view_type";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$aop_multi_issue_num=str_replace("'","",$txt_system_id);
		$aop_multi_update_id=str_replace("'","",$update_id);

		$aop_multi_update_id = str_replace("'","",$aop_multi_update_id);
		if( str_replace("'","",$aop_multi_update_id) == "" )
		{
			echo "20**Delete not allowed. Problem occurred";disconnect($con); die;
		}
		else
		{
			$field_array = "updated_by*update_date*status_active*is_deleted";
			$data_array = "'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*0*1";
			$is_mst_del = sql_update("aop_multi_issue_mst", $field_array, $data_array, "id", $aop_multi_update_id, 1);
			if($is_mst_del) $flag=1; else $flag=0;

			$field_array_dtls="updated_by*update_date*status_active*is_deleted";
			$data_array_dtls="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID=sql_update("aop_multi_issue_dtls",$field_array_dtls,$data_array_dtls,"mst_id",$aop_multi_update_id,1);
			if($rID) $flag=1; else $flag=0;
		}

		// echo "10**$rID##$is_mst_del**$flag";
		// oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $aop_multi_issue_num)."**0"."**".$is_mst_del;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1**$list_view_type";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $aop_multi_issue_num)."**0"."**".$is_mst_del;
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1**$list_view_type";
			}
		}
		disconnect($con);
		die;
	}	
}

// =========== Sales order popup ================//
if($action=="fabric_sales_order_popup")
{
	echo load_html_head_contents("Fabric Sales Order Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
			

		function fn_show_check()
		{
			var txt_sale_order_no = $("#txt_sale_order_no").val().trim();
			var txt_booking_no = $("#txt_booking_no").val().trim();
			var txt_style_no = $("#txt_style_no").val().trim();

			if(txt_sale_order_no =="" && txt_booking_no =="" && txt_style_no ==""){
			var valid_id = "txt_date_from*txt_date_to";
			var valid_msg = "*From Date*To Date";
			}else
			{
				var valid_id = "cbo_company_id";
				var valid_msg = "Company Name";
			}
			if( form_validation(valid_id,valid_msg)==false )
			{
				return;
			}
			
			show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_sale_order_no').value+'_'+document.getElementById('chk_sample').value, 'create_fso_search_list_view', 'search_div', 'fabric_aop_multi_issue_challan_controller', 'setFilterGrid(\'tbl_list_search\',-1);')
		}

        var selected_id = new Array();
        var selected_name = new Array();
        var selected_fso = new Array();

		function check_all_data(str) {
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}

		function toggle(x,origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				selected_fso.push( $('#txt_individual_fso_id' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_fso.splice( i, 1 );


			}
			var id ='';
			var name = '';
			var fsoid='';

			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				fsoid += selected_fso[i] + ',';

			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			fsoid = fsoid.substr( 0, fsoid.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_fso').val( fsoid );



		}
		function set_checkvalue(){
			if(document.getElementById('chk_sample').value==0) document.getElementById('chk_sample').value=1;
			else document.getElementById('chk_sample').value=0;
		}
	
    </script>
	</head>
	<body>
		<div align="center" style="width:1550px;">
			<form name="searchwofrm"  id="searchwofrm">
				<fieldset style="width:1450px; margin-left:3px">
					<legend>Enter search words</legend>

					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="1410" class="rpt_table">
						<thead>
						<tr>
							<th colspan="11"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
						</tr>
						<tr>
								<th>FSO Company Name</th>
								<th>Within Group</th>
								<th>Buyer Name</th>
								<th>Sub Con Supplier</th>
								<th>Sales Order No</th>
								<th>Fabric Booking No</th>
								<th>Style Ref. No</th>
								<th> Date Range</th>
								<th>
								<input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_sample">Sample Without Order
									<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
									

									<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
									<input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />

									<input type="hidden" name="txt_selected_fso"  id="txt_selected_fso" width="650px" value="" />

								</th>
							</tr>
						</thead>
						<tr>
                            <td> 
                                <? 
                                echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$cbo_company_id."", "",1);
                                ?>
                            </td>
							<td align="center">
								<? echo create_drop_down("cbo_within_group", 150, $yes_no, "", 0, "--  --", 0, '', ''); ?>
							</td>
                            <td id="buyer_td">
									<? 
                                    echo create_drop_down( "cbo_buyer_name", 150 , "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
									?>
								</td>
                            <td>
                            <?php 

                            if($cbo_service_source==3)
                            {
                                echo create_drop_down( "cbo_supplier_name", 150, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$cbo_company_id and b.party_type=25 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Delivery To--","".$cbo_delivery_name."","",1 );
                                
                                // echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$cbo_delivery_name."", "",1 );
                            }
                            else
                            {
                                echo create_drop_down( "cbo_supplier_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Delivery To
                                --", "".$cbo_delivery_name."","",1 );

                                // echo create_drop_down( "cbo_supplier_name", 152, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", "".$cbo_delivery_name."", "",1 );
                            }
                            ?>
                        </td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_sale_order_no" id="txt_sale_order_no" />
							</td>
							<td align="center" >
								<input type="text" style="width:130px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_style_no" id="txt_style_no" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px" >To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"
								style="width:70px" >
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check();" style="width:70px;" />
							</td>
						</tr>
						<tr>
							<td colspan="10" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px;" id="search_div" align="center"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	
	</html>
	<?
	exit();
}

if($action=="create_fso_search_list_view")
{
  
	$data=explode('_',$data);
	//var_dump($data);
	$within_group=$data[8];
	$without_order=$data[10];
	if ($data[0]!=0)
	{
		if($within_group==1 )
		{
			if($without_order ==1){
				$company="  h.company_id='$data[0]'";
				if ($data[4]!=0) $supplier=" and h.supplier_id='$data[4]'"; else $supplier="";
			}else{
				$company="  h.company_id='$data[0]'";
				$supplier = "and h.supplier_id = '$data[4]' ";
			}
			
		}
		else{
			$company="  h.company_id='$data[0]'";
			$supplier = "and c.buyer_id = '$data[4]' ";
		}
	}
	else 
	{ 
		echo "Please Select Company First."; die;
	}

	//if ($data[4]!=0) $supplier=" and f.supplier_id='$data[4]'"; else $supplier="";

	if ($data[8]!=0) $within_group_cond=" and c.within_group='$data[8]'"; else $within_group_cond="";

	if($within_group==1  && $without_order ==1)
	{
		if($db_type==0)
		{
			if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and h.sys_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
			$year_cond=" and SUBSTRING_INDEX(h.insert_date, '-', 1)=$data[7]";
		}
		else if($db_type==2)
		{
			if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and h.sys_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
			$year_cond=" and to_char(h.insert_date,'YYYY')=$data[7]";
		}
	}else{
		if($db_type==0)
		{
			if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and h.sys_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
			$year_cond=" and SUBSTRING_INDEX(h.insert_date, '-', 1)=$data[7]";
		}
		else if($db_type==2)
		{
			if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and h.sys_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
			$year_cond=" and to_char(h.insert_date,'YYYY')=$data[7]";
		}
	}

	if($data[6]==1){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and c.sales_booking_no='$data[5]'"; else  $booking_cond="";

		if (trim($data[7])!="") $style_cond=" and c.style_ref_no ='$data[7]'"; else $style_cond="";
		if (trim($data[9])!="") $so_cond=" and c.job_no ='$data[9]'"; else $so_cond="";
	}
	else if($data[6]==4 || $data[6]==0){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and c.sales_booking_no like '%$data[5]%'  $booking_year_cond "; else  $booking_cond="";

		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like '%$data[7]%'"; else $style_cond="";
		if (trim($data[9])!="") $so_cond=" and c.job_no like '%$data[9]%'"; else $so_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and c.sales_booking_no like '$data[5]%' $booking_year_cond "; else  $booking_cond="";
		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like'$data[7]%'"; else $style_cond="";
		if (trim($data[9])!="") $so_cond=" and c.job_no like'$data[9]%'"; else $so_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and c.sales_booking_no like '%$data[5]' $booking_year_cond "; else  $booking_cond="";
		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like '%$data[7]'"; else $style_cond="";
		if (trim($data[9])!="") $so_cond=" and c.job_no like '%$data[9]'";  else $so_cond="";
	}

	/*$file_no = str_replace("'","",$data[8]);
	$internal_ref = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' ";*/

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	if($within_group==1)
	{
		
		if($without_order !=1){
			
			//if ($data[1]!=0) $buyer=" and c.buyer_id='$data[1]'"; else $buyer="";
			if ($data[1]!=0) $buyer=" and c.po_buyer='$data[1]'"; else $buyer="";
			$sql= "SELECT c.id as sales_order_id, c.job_no as booking_no,c.po_company_id as company_id, c.po_buyer as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, h.id as aop_id from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h  where $company and h.id=g.mst_id  and  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.batch_id=e.id and e.color_id=b.color_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.is_deleted=0 and g.status_active=1 and h.is_deleted=0 and h.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond and c.booking_type=1 group by  c.id,c.job_no ,c.po_company_id, c.po_buyer ,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no,h.id order by c.id desc";


		}else{
			if ($data[1]!=0) $buyer=" and c.po_buyer='$data[1]'"; else $buyer="";
			$sql= "SELECT f.id,f.booking_no,f.company_id, c.po_buyer as buyer_id, c.id as sales_order_id,c.job_no as booking_no,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, h.id as aop_id from 
			 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f, wo_fabric_aop_dtls g, wo_fabric_aop_mst h  where $company and h.id=g.mst_id and  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.batch_id=e.id and f.booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and g.is_deleted=0 and g.status_active=1 and h.is_deleted=0 and h.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond group by  f.id,f.booking_no,f.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.id, c.within_group, c.sales_booking_no,h.id order by f.id desc";
		}
	}else{
		if ($data[1]!=0) $buyer=" and c.buyer_id='$data[1]'"; else $buyer="";
		if($without_order !=1){
			$sql= "SELECT c.id as sales_order_id, c.job_no as booking_no,c.company_id, c.buyer_id as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, h.id as aop_id from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where $company and h.id=g.mst_id and  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.batch_id=e.id and e.color_id=b.color_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.is_deleted=0 and g.status_active=1 and h.is_deleted=0 and h.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond group by  c.id,c.job_no ,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no,h.id order by c.id desc";
		}else{
			$sql= "";
		}
	}
	
	//echo $sql; //die;
	$result=sql_select($sql);

	?>
	<style type="text/css">
	.rpt_table tr{ text-decoration:none; cursor:pointer; }
	.rpt_table tr td{ text-align: center; }
	</style>
	<div style="width:1000px;">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table">
		<thead> 
			<th width="30">SL</th>
			<th width="170">PO Company Name</th>
			<?
			if($within_group==1)
			{
			?>
				<th width="170">PO Buyer Name</th>
			<?
			}else{
			?>
				<th width="170">Buyer Name</th>
			<?
			}?>
			
			<th width="170">Style Ref. No</th>
			<th width="140">FSO No.</th>
			<th width="140">Fabric Booking No</th>
			<?
			if($within_group==1 && $without_order==0)
			{
			?>
				<th>WO No</th>
			<?
			}?>
		</thead>
	</table>
	</div>
	<div style="width:1000px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		if(!empty($result)){
			foreach ($result as $row) {
				//var_dump($row);
			
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			

				$data = $row[csf('sales_order_id')].'**'. $row[csf('booking_no')];

				$id_arr[]=$row[csf('aop_id')];
				//if ($chln_count_exist_arr[$row[csf('issue_number')]]!=$chln_count_arr[$row[csf('issue_number')]]) 
				
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('aop_id')]; ?>');" id="search<? echo $row[csf('aop_id')];?>">
							<td width="40"><? echo $i; ?>

								<input type="hidden" name="txt_individual" id="txt_individual<? echo $row[csf('aop_id')];?>" value="<?php echo $row[csf('booking_no')]; ?>"/>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $row[csf('aop_id')];?>" value="<?php echo $row[csf('aop_id')]; ?>"/>

							    <input type="hidden" name="txt_individual_fso_id" id="txt_individual_fso_id<? echo $row[csf('aop_id')];?>" value="<?php echo $row[csf('sales_order_id')]; ?>"/>
							</td>
							<td width="170" style="word-break:break-all"><?php echo $comp[$row[csf("company_id")]]; ?></td>
							<td width="170" style="word-break:break-all"><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
							<td width="170" style="word-break:break-all"><?php echo $row[csf("style_ref_no")]; ?></td>
							<td width="140" style="word-break:break-all"><?php echo $row[csf("fso_number")]; ?></td>
							<td width="140" style="word-break:break-all"><?php echo $row[csf("sales_booking_no")]; ?></td>
							<?
							if($within_group==1 && $without_order==0)
							{
							?>
								<td style="word-break:break-all"><?php echo $row[csf("booking_no")]; ?></td>
							<?
							}?>
						</tr>
					<?
				$i++;
				
			}
		}else{
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<th colspan="9">No data found</th>
			</tr>
			<?
		}
		?>
	</table>

	<div style="width:625px;" align="left">
	    <table width="100%">
	        <tr>
	            <td align="center" colspan="6" height="30" valign="bottom">
	                <div style="width:100%">
	                        <div style="width:50%; float:left" align="left">
	                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
	                        </div>
	                        <div style="width:50%; float:left" align="left">
	                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
	                        </div>
	                </div>
	            </td>
	        </tr>
	    </table>
	</div>

	</div>
<?
exit();
}

//====================SYSTEM ID POPUP========
if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,sys_number)
		{
			$('#hidden_sys_id').val(id);
			$("#hidden_sys_no").val(sys_number);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:700px;">
			<form name="searchsystemidfrm"  id="searchsystemidfrm">
				<fieldset style="width:700px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Delivery Date Range</th>
							<th>Search By</th>
							<th id="search_by_td_up">Please Enter System Id</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_sys_no" id="hidden_sys_no" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
							</td>

							<td>
								<?
								$search_by_arr=array(1=>"System ID",2=>"Batch No");
								$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_multi_aop_search_list_view', 'search_div', 'fabric_aop_multi_issue_challan_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px; margin-left:3px;" id="search_div" align="center"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_multi_aop_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$cbo_year_selection =$data[5];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			//$date_cond="and a.insert_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			$date_cond="and a.insert_date between '".change_date_format($start_date,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($end_date,"yyyy-mm-dd")." 23:59:59' ";
		}
		else
		{
			//$date_cond="and a.insert_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			$date_cond="and a.insert_date between '".date("j-M-Y",strtotime($start_date))."   01:00:01 AM' and '".date("j-M-Y",strtotime($end_date))."  11:59:59 PM' ";
		}

		$year_condition = "";
	}
	else
	{
		$date_cond="";

		if($db_type==0)
		{
			if($cbo_year_selection>0)
			{
				$year_condition=" and YEAR(a.insert_date)=$cbo_year_selection";
			}
		}else
		{
			if($cbo_year_selection>0)
			{
				$year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
			}
		}
	}

	if(trim($data[0])!="")
	{
		if($search_by==1)
		{
			$search_field_cond="and a.sys_number like '$search_string'";
		}
		else if($search_by==2)
		{
			$search_batch_cond="and batch_no like '$search_string'";

			if($search_batch_cond!="")
			{
				$batch_sql = sql_select("select id from pro_batch_create_mst where status_active=1 and is_deleted=0 $search_batch_cond");
				foreach ($batch_sql as $row)
				{
					$batch_cond_ids .= $row[csf('id')].",";
				}

				$batch_cond_ids = implode(",", array_filter(array_unique(explode(",",chop($batch_cond_ids,",")))));

				if($batch_cond_ids!="")
				{
					$search_field_cond = "and b.batch_id in ($batch_cond_ids)";
				}
			}
		}
		else
			$search_field_cond="and c.id in($all_batch_id)";
	}
	else
	{
		$search_field_cond="";
	}

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY')";
	}
	else
	{
		$year_field="null";
	}

	if ($db_type == 0) {
		$batch_id_list = "group_concat(distinct(batch_id)) as batch_id";
		//$fso_id_lis = "group_concat(distinct(fso_id)) as fso_id";
	} else {
		$batch_id_list = "LISTAGG(cast(batch_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY batch_id) as batch_id";
		//$fso_id_lis = "LISTAGG(fso_id, ',') WITHIN GROUP (ORDER BY fso_id) as fso_id";
	}

   $sql ="select a.id, a.sys_number_prefix_num, a.sys_number, $year_field as year , $batch_id_list,sum(b.issue_qnty) as issue_qnty from aop_multi_issue_mst a,aop_multi_issue_dtls b where a.id=b.mst_id and a.entry_form=522 and b.entry_form=522 and company_id=$company_id $date_cond $year_condition $search_field_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id, a.sys_number_prefix_num, a.sys_number, a.insert_date order by a.id DESC";
	//echo $sql;
	$result = sql_select($sql);

	foreach ($result as $row)
	{
		$salesOrderIds .= $row[csf('fso_id')].",";
		$batch_ids .= $row[csf('batch_id')].",";
		//$party_ids .= $row[csf('party_id')].",";
	}

	$batch_ids = implode(",", array_filter(array_unique(explode(",",chop($batch_ids,",")))));

	if($batch_ids!=""){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in ($batch_ids) and status_active=1 and is_deleted=0","id","batch_no");
	}

	$company_arr=return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0",'id','company_short_name');


	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="470" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="50">Year</th>
			<th width="70">Sys No</th>
			<th width="200">Batch No</th>
			<th width="">Issue Qnty</th>
		</thead>
	</table>
	<div style="width:475px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="455" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
			

				$batchsids = explode(',', $row[csf('batch_id')]);
				$batchNo = "";
				foreach ($batchsids as $batchid) {
					$batchNo .= $batch_arr[$batchid].",";
				}
				$batchNo = chop($batchNo,",");
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('sys_number')]; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
					<td width="200"><p><? echo $batchNo; ?>&nbsp;</p></td>
					<td width="" align="right"><? echo number_format($row[csf('issue_qnty')],2); ?>&nbsp;</td>
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

if($action=='populate_data_from_finish_fabric')
{
	// echo "select a.id,a.sys_number_prefix, a.sys_number_prefix_num, a.sys_number, a.company_id, a.vehicle_no, a.driver_name, a.dl_no, a.transport, a.mobile_no, a.gate_pass_no, a.remarks as master_remarks,b.fso_id,b.within_group, b.booking_no, b.delivery_id, b.delivery_dtls_id, b.product_id, b.batch_id, b.color_id, b.is_sales, b.rate, b.item_description, b.gsm, b.dia, b.bodypart_id, b.process_type_id, b.issue_qnty, b.remarks as dtls_remarks,a.delivery_to,a.delivery_address, a.issue_purpose, a.service_source from aop_multi_issue_mst a,aop_multi_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.id=$data";
	$data_array = sql_select("SELECT a.id,a.sys_number_prefix, a.sys_number_prefix_num, a.sys_number, a.company_id, a.vehicle_no, a.driver_name, a.dl_no, a.transport, a.mobile_no, a.gate_pass_no, a.remarks as master_remarks,b.fso_id,b.within_group, b.booking_no, b.delivery_id, b.delivery_dtls_id, b.product_id, b.batch_id, b.color_id, b.is_sales, b.rate, b.item_description, b.gsm, b.dia, b.bodypart_id, b.process_type_id, b.issue_qnty, b.remarks as dtls_remarks,a.delivery_to,a.delivery_address, a.issue_purpose, a.service_source,a.delevery_date from aop_multi_issue_mst a,aop_multi_issue_dtls b where a.id=b.mst_id and a.entry_form=522 and b.entry_form=522 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data");
	$j = 1;
	foreach ($data_array as $row)
	{
		$salesOrderIds .= $row[csf('fso_id')].",";
		$delivery_ids .= $row[csf('delivery_id')].",";

		echo "document.getElementById('update_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_system_id').value 		= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('txt_vehicle_no').value 		= '".$row[csf("vehicle_no")]."';\n";
		echo "document.getElementById('txt_driver_name').value 		= '".$row[csf("driver_name")]."';\n";
		echo "document.getElementById('txt_dl_no').value 			= '".$row[csf("dl_no")]."';\n";
		echo "document.getElementById('hdn_fso_id').value 			= '".$row[csf("fso_id")]."';\n";
		echo "document.getElementById('txt_transport').value 		= '".$row[csf("transport")]."';\n";
		echo "document.getElementById('txt_mobile_no').value 		= '".$row[csf("mobile_no")]."';\n";
		echo "document.getElementById('txt_gate_pass_no').value 	= '".$row[csf("gate_pass_no")]."';\n";
		echo "document.getElementById('txt_remarks').value 			= '".$row[csf("master_remarks")]."';\n";
		echo "document.getElementById('txt_delivery_address').value = '".$row[csf("delivery_address")]."';\n";
		echo "document.getElementById('cbo_issue_purpose').value 	= '".$row[csf("issue_purpose")]."';\n";
		echo "document.getElementById('cbo_service_source').value 	= '".$row[csf("service_source")]."';\n";
		echo "document.getElementById('txt_multi_challan_date').value 	= '".change_date_format($row[csf("delevery_date")])."';\n";

		//echo "load_drop_down( 'requires/fabric_aop_multi_issue_challan_controller', " . $row[csf("within_group")] . "+'_'+" . $row[csf("company_id")] . ", 'load_drop_down_party','cbo_party');\n";
		echo "load_drop_down( 'requires/fabric_aop_multi_issue_challan_controller', " . $row[csf("service_source")] . "+'_'+" . $row[csf("company_id")] . ", 'load_drop_down_delivery_com','delivery_td');\n";

		echo "document.getElementById('cbo_delivery_name').value 		= '".$row[csf("delivery_to")]."';\n";
		
		$j++;
	}

	$salesOrderIds = implode(",", array_filter(array_unique(explode(",",chop($salesOrderIds,",")))));
	$delivery_ids = chop($delivery_ids,",");

	if($salesOrderIds!="")
	{
		$salse_sql = sql_select("select id,job_no from fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and id in ($salesOrderIds)");
		foreach ($salse_sql as $row) {

			$salesNo .= $row[csf('job_no')].",";
		}
	}

	$salesNos = implode("*", array_filter(array_unique(explode(",",chop($salesNo,",")))));

	echo "document.getElementById('hdn_challan_id').value 	= '".$delivery_ids."';\n";
	echo "document.getElementById('hdn_fso_id').value 	= '".$salesOrderIds."';\n";
	echo "document.getElementById('txt_fso_no').value 	= '".$salesNos."';\n";

	exit();
}


if ($action == "fabric_aop_multi_issue_print")
{

	extract($_REQUEST);
	$data = explode('*', $data);

	$companysql = sql_select("select id,company_name,city,group_id from lib_company where status_active=1 and is_deleted=0");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');


	$company_arr = array();
	foreach ($companysql as $row) {
		$company_arr[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_arr[$row[csf('id')]]['city_town'] = $row[csf('city')];
	}

	

	// $mainQueryResult = sql_select("select a.sys_number_prefix, a.sys_number_prefix_num, a.sys_number, a.delevery_date, a.company_id, a.party_id,a.vehicle_no,a.driver_name, a.dl_no, a.transport, a.mobile_no, a.gate_pass_no, a.remarks as master_remarks,a.insert_date as delivery_date, b.fso_id, b.within_group, b.booking_no, b.delivery_id, b.delivery_dtls_id, b.product_id, b.batch_id, b.color_id, b.is_sales, b.uom,b.roll_no,b.fabric_shade, b.determination_id, b.gsm, b.dia, b.bodypart_id, b.width_type, b.delivery_qnty, b.remarks as dtls_remarks, c.issue_number,a.delivery_to,a.delivery_address from pro_fin_deli_multy_challan_mst a,pro_fin_deli_multy_challa_dtls b, inv_issue_master c where a.id=b.mst_id and a.id=$data[1] and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.delivery_id = c.id");
	$mainQuery = "SELECT a.sys_number_prefix, a.sys_number_prefix_num, a.sys_number, a.delevery_date, a.company_id, a.party_id,a.vehicle_no,a.driver_name, 
	a.dl_no, a.transport, a.mobile_no, a.gate_pass_no, a.remarks as master_remarks,a.insert_date as delivery_date, b.fso_id, b.within_group, 
	b.booking_no, b.delivery_id, b.delivery_dtls_id, b.product_id, b.batch_id, b.color_id, b.is_sales, b.rate,b.roll_no, 
	b.determination_id, b.gsm, b.dia, b.bodypart_id, b.issue_qnty, b.remarks as dtls_remarks, c.sys_no,a.delivery_to,
	a.delivery_address, a.service_source, c.sys_date, b.item_description, b.process_type_id from aop_multi_issue_mst a,aop_multi_issue_dtls b, wo_fabric_aop_mst c where a.id=b.mst_id and 
	a.id=$data[1]  and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.delivery_id = c.id ";
	
	//echo $mainQuery;
	$mainQueryResult = sql_select($mainQuery);

	foreach ($mainQueryResult as $row)
	{
		$salesOrderIds .= $row[csf('fso_id')].",";
		$batch_ids .= $row[csf('batch_id')].",";
		$party_ids .= $row[csf('party_id')].",";
		$determination_ids .= $row[csf('determination_id')].",";
		$color_ids .= $row[csf('color_id')].",";

		$sys_number = $row[csf('sys_number')];
		$service_source = $row[csf('service_source')];
		$delivery_date = change_date_format($row[csf('delivery_date')]);
		$gate_pass_no = $row[csf('gate_pass_no')];
		$mobile_no = $row[csf('mobile_no')];
		$dl_no = $row[csf('dl_no')];
		$transport = $row[csf('transport')];
		$vehicle_no = $row[csf('vehicle_no')];
		$driver_name = $row[csf('driver_name')];
		$master_remarks = $row[csf('master_remarks')];
		$delivery_to = $row[csf('delivery_to')];
		$delivery_address = $row[csf('delivery_address')];
		$company_id = $row[csf('company_id')];

		if($row[csf('service_source')]==1)
    	{
    		$DeliveryTo = $company_arr[$row[csf('delivery_to')]]['name'];
    	}else{
    		$DeliveryTo = $supplier_arr[$row[csf('delivery_to')]];
    	}

	}

	$salesOrderIds = implode(",", array_filter(array_unique(explode(",",chop($salesOrderIds,",")))));
	$batch_ids = implode(",", array_filter(array_unique(explode(",",chop($batch_ids,",")))));
	$party_ids = implode(",", array_filter(array_unique(explode(",",chop($party_ids,",")))));
	$determination_ids = implode(",", array_filter(array_unique(explode(",",chop($determination_ids,",")))));
	$color_ids = implode(",", array_filter(array_unique(explode(",",chop($color_ids,",")))));

	$fso_sql = sql_select("select id,job_no_prefix_num,season,style_ref_no,po_company_id,company_id,po_buyer,buyer_id,within_group ,job_no from FABRIC_SALES_ORDER_MST where status_active=1 and is_deleted=0 and id in($salesOrderIds)");

	$salesOrderData = array();
	foreach ($fso_sql as $row) {

		$salesOrderData[$row[csf('id')]]['job_no'] 			=  $row[csf('job_no')];
		$salesOrderData[$row[csf('id')]]['fso_no'] 			=  $row[csf('job_no_prefix_num')];
		$salesOrderData[$row[csf('id')]]['season'] 			=  $row[csf('season')];
		$salesOrderData[$row[csf('id')]]['style_ref_no'] 	=  $row[csf('style_ref_no')];
		$salesOrderData[$row[csf('id')]]['po_company_id'] 	=  $row[csf('po_company_id')];
		$salesOrderData[$row[csf('id')]]['company_id'] 		=  $row[csf('company_id')];
		$salesOrderData[$row[csf('id')]]['po_buyer'] 		=  $row[csf('po_buyer')];
		$salesOrderData[$row[csf('id')]]['buyer_id'] 		=  $row[csf('buyer_id')];
		$salesOrderData[$row[csf('id')]]['within_group'] 	=  $row[csf('within_group')];
	}

	if($determination_ids!="")
	{
		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($determination_ids)";

		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
	}

	if($batch_ids!=""){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in ($batch_ids) and status_active=1 and is_deleted=0","id","batch_no");
	}

	$color_arr=array();
	if($color_ids!=""){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in($color_ids) and status_active=1 and is_deleted=0",'id','color_name');
	}
	$com_dtls = fnc_company_location_address($data[0], 0, 2);

    ?>
    <div style="width:1400px;">
    	<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
		<tr>
            	<td  align="left"><img src="../../<? echo $com_dtls[2]; ?>" height="70" width="200"><span style="font-size:xx-large; text-align:left;padding-left:50px">
				<strong ><? echo $com_dtls[0]; ?></strong>
				</span></td>
            	
        	</tr>
    	
    		<tr>
    			<td align="center">
    				<strong><? echo $com_dtls[1]; //$company_arr[$data[0]]['city_town']; ?></strong>
    			</td>
    			<td id="barcode_img_id" align="right"></td>
    		<tr>

    		<tr>
    			<td style="font-size:16px;" align="center">
    				<strong><u>Fabric Delivery Challan For AOP</u></strong>
    			</td>
    		</tr>

    	</table>

    	<br>
    	<table width="1350" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="100">System ID:</td>
				<td><? echo $sys_number;?></strong></td>
				<td style="font-size:14px; font-weight:bold;" width="100">Service Source:</td>
				<td><? echo $knitting_source[$service_source];?></strong></td>
				<td style="font-size:14px; font-weight:bold;" width="100">Delivery To:</td>
				<td><? echo $DeliveryTo;?></strong></td>
				<td style="font-size:14px; font-weight:bold;" width="100">Delivery Address:</td>
				<td><? echo $delivery_address;?></strong></td>
			</tr>
			<tr>
    			<td style="font-size:14px; font-weight:bold;" width="100">Company Name</td>
    			<td><? echo $company_arr[$company_id]['name'];?></strong></td>
    			<td style="font-size:14px; font-weight:bold;" width="80">Currency</td>
    			<td>&nbsp;</td>
    			<td style="font-size:14px; font-weight:bold;">Driver Name</td>
    			<td><? echo $driver_name;?></td>
    			<td style="font-size:14px; font-weight:bold;">Mobile No</td>
    			<td><? echo $mobile_no; ?></td>
    		</tr>

    		<tr>
    			<td style="font-size:14px; font-weight:bold;">Transport</td>
    			<td><? echo $transport; ?></td>
    			<td style="font-size:14px; font-weight:bold;">Vahical No</td>
    			<td><? echo $vehicle_no; ?></td>

    			<td style="font-size:14px; font-weight:bold;">Gate Pass No</td>
    			<td><? echo $gate_pass_no; ?></td>
    			<td style="font-size:14px; font-weight:bold;">DL No</td>
    			<td><? echo $dl_no; ?></td>
    			
    		</tr>

    		<tr>
    			<td style="font-size:14px; font-weight:bold;">Remarks</td>
    			<td colspan="7"><? echo $master_remarks; ?></td>
    		</tr>


    	</table>
    		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1710" class="rpt_table" style="font-family: tahoma; font-size: 14px;">
	    	<thead>
	    		<tr>
	    			<th width="30">SL</th>
	    			<th width="120">Issue Date</th>
	    			<th width="150">Challan No</th>
	    			<th width="80">Buyer</th>
	    			<th width="120">Fab. Booking No</th>
	    			<th width="80">Style Ref.</th>
	    			<th width="150">FSO No</th>
					<th width="100">Bodypart</th>
	    			<th width="150">Fabric Description</th>
	    			<th width="80"> GSM</th>
	    			<th width="80"> DIA</th>
	    			<th width="80">Batch No</th>
	    			<th width="80">Fab Color</th>
	    			<th width="120">Process Type</th>
	    			<th width="80">Issue Qty.</th>
	    			<th width="80">No. Of Roll</th>
	    			<th width="">Remarks</th>
	    		</tr>
	    	</thead>
	    	<?
	    	$i = 1;
	    	foreach ($mainQueryResult as $row)
	    	{

		    	if($salesOrderData[$row[csf('fso_id')]]['within_group']==1)
		    	{
		    		$buyerName = $salesOrderData[$row[csf('fso_id')]]['po_buyer'];
		    	}else{
		    		$buyerName = $salesOrderData[$row[csf('fso_id')]]['buyer_id'];
		    	}


	    	?>
			<tr>
				<td width="30"><? echo $i; ?></td>
				<td width="120"><p><? echo change_date_format($row[csf('sys_date')]); ?></p></td>
				<td width="150" style="word-break:break-all; font-size: 14"><? echo $row[csf('sys_no')]; ?></td>
				<td width="80" style="word-break:break-all;"><? echo $buyer_arr[$buyerName]; ?></td>
				<td width="120" style="word-break:break-all;"><? echo $row[csf('booking_no')];?></td>
				<td width="80" style="word-break:break-all; font-size: 14"><? echo $salesOrderData[$row[csf('fso_id')]]['style_ref_no']; ?></td>
				<td width="150" style="word-break:break-all;"><? echo $salesOrderData[$row[csf('fso_id')]]['job_no']; ?></td>
				<td width="100" style="word-break:break-all;" align="right"><? echo  $body_part[$row[csf('bodypart_id')]]; ?></td>
				<td width="150" style="word-break:break-all;"><? echo $row[csf('item_description')];?></td>
				<td width="80" style="word-break:break-all; font-size: 14" align="center"><? echo $row[csf('gsm')];?></td>
				<td width="80" style="word-break:break-all;" align="center"><? echo $row[csf('dia')];?></td>
				<td width="80" style="word-break:break-all;"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
				<td width="80" style="word-break:break-all; font-size: 14"><? echo  $color_arr[$row[csf('color_id')]]; ?></td>
				
				<td width="120" style="word-break:break-all; font-size: 14" align="center"><? echo  $emblishment_print_type[$row[csf('process_type_id')]]; ?></td>
				<td width="80" style="word-break:break-all;" align="right"><?  echo  number_format($row[csf('issue_qnty')],2);?></td>
				<td width="80" style="word-break:break-all; font-size: 14" align="right"><? echo  $row[csf('roll_no')]; ?></td>
				<td width="" style="word-break:break-all;"><? echo $row[csf('dtls_remarks')]; ?></td>
			</tr>
			<?
			$i++;
			$tot_qty += $row[csf('issue_qnty')];
			$total_roll += $row[csf('roll_no')];
		
			}
			?>
	        <tr>
            	<td align="right" colspan="14"><strong>Total</strong></td>
            	<td align="right"><strong><? echo number_format($tot_qty, 2, '.', ''); ?></strong></td>
            	<td align="right"><strong><? echo number_format($total_roll, 2, '.', ''); ?></strong></td>
            	<td align="right">&nbsp;</td>
            </tr>
        </table>

    </div>
    <div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(264, $data[0], "1600px"); ?></div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
	function generateBarcode(valuess)
	{
        var value = valuess;
        //alert(value)
        var btype = 'code39';
        var renderer = 'bmp';

        var settings = {
        	output: renderer,
        	bgColor: '#FFFFFF',
        	color: '#000000',
        	barWidth: 1,
        	barHeight: 40,
        	moduleSize: 5,
        	posX: 10,
        	posY: 20,
        	addQuietZone: 1
        };

        value = {code: value, rect: false};

        $("#barcode_img_id").show().barcode(value, btype, settings);
    }
    generateBarcode('<? echo $sys_number; ?>');
    document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}
?>

