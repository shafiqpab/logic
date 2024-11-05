<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );
	exit();     	 
}

if ($action=="load_drop_down_party_name")
{
	$data=explode('_',$data);
	if($data[1]==3)
	{
		echo create_drop_down( "cbo_party_name", 120, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (9,21)) order by supplier_name","id,supplier_name", 1, "--Select--", $selected, "","","","","","",5 );
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "--Select--", $selected, "","","","","","",5 ); 
	}
	else if($data[1]==1)
	{	
		echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select--", $selected, "","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "--Select--", $selected, "",0,"","","","",5);
	}
	exit();
}

if($action=="report_generate")
{ 
	//Search value...............................................................
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$search_type=str_replace("'","",$cbo_search_type);
	$cbo_party_source=str_replace("'","",$cbo_party_source);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$cbo_bill_type=str_replace("'","",$cbo_bill_type);
	$txt_bill_no=str_replace("'","",$txt_bill_no);
	$cbo_bill_for=str_replace("'","",$cbo_bill_for);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	ob_start();
	
	 $conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","");
	// echo  $conversion_rate;

	$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
	$user_arr=return_library_array( "select id,user_full_name from user_passwd",'id','user_full_name');
	//Where con.................................................................
	if($search_type==1)// In-Bound Bill
	{
		if(trim($txt_date_from)=="" && trim($txt_date_to)=="") $where_cond=""; else  $where_cond=" and a.bill_date between '$txt_date_from' and '$txt_date_to'";
		if($cbo_company_id==0) $where_cond.=""; else  $where_cond.=" and a.company_id=$cbo_company_id";
		if($cbo_location_id==0) $where_cond.=""; else  $where_cond.=" and a.location_id=$cbo_location_id";
		if($cbo_party_source==0) $where_cond.=""; else  $where_cond.=" and a.party_source=$cbo_party_source";
		if($cbo_party_name==0) $where_cond.=""; else  $where_cond.=" and a.party_id=$cbo_party_name";
		if($cbo_bill_type==0) $where_cond.=""; else  $where_cond.=" and a.process_id=$cbo_bill_type";
		if($txt_bill_no==0) $where_cond.=""; else  $where_cond.=" and a.prefix_no_num=$txt_bill_no";
		if($cbo_bill_for==0) $where_cond.=""; else  $where_cond.=" and a.bill_for=$cbo_bill_for";
		
		$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
		
		//Query-----------------------------------------------------------------------------------
		 $sql="select a.id, a.process_id, a.party_source, a.party_id, a.bill_for, a.prefix_no_num, a.bill_no, a.bill_date, a.inserted_by, a.insert_date, sum(b.delivery_qty) as delivery_qty, sum(b.amount) as amount,min(b.CURRENCY_ID) as CURRENCY_ID
		from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $where_cond and a.party_source in(1,2) 
		group by a.id, a.process_id, a.party_source, a.party_id, a.bill_for, a.prefix_no_num, a.bill_no, a.bill_date, a.inserted_by, a.insert_date order by a.id ASC";
		
		//echo $sql;die;
		
		$sql_result=sql_select($sql);
		$dataArr=array();
		foreach ($sql_result as $row)
		{
			if($row[csf('party_source')]==1){
				$party_name_arr=$company_short_name_arr;
			}
			else
			{
				$party_name_arr=$party_arr;
			}
			
			
			if($row[CURRENCY_ID]!=1){$row[csf('amount')]=$row[csf('amount')]*$conversion_rate;}
			
			$bill_arr[$row[csf('prefix_no_num')]]['qty']+=$row[csf('delivery_qty')];
			$bill_arr[$row[csf('prefix_no_num')]]['amt']+=$row[csf('amount')];
			
			
			$dataArr[$row[csf('prefix_no_num')]]=array(
				id				=> $row[csf('id')],
				process_id		=> $row[csf('process_id')],
				party_source 	=> $row[csf('party_source')],
				party_id		=> $party_name_arr[$row[csf('party_id')]],
				bill_for		=> $row[csf('bill_for')],
				prefix_no_num	=> $row[csf('prefix_no_num')],
				bill_no			=> $row[csf('bill_no')],
				bill_date		=> $row[csf('bill_date')],
				delivery_qty	=> $row[csf('delivery_qty')],
				amount			=> ($row[csf('amount')]),
				inserted_by		=> $row[csf('inserted_by')],
				insert_date		=> $row[csf('insert_date')]
			);
		}
		?>
		<div style="width:1080px; margin:0 auto;">
        <fieldset style="width:1080px;">
            <table cellpadding="0" cellspacing="0" width="880">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="11" style="font-size:20px"><strong><? echo 'Bill Statement- In-Bound'; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="11" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
			<table width="1080" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="100">Bill Type</th>
					<th width="120">Source</th>
					<th width="100">Party</th>
					<th width="100">Bill For</th>                            
					<th width="120">Bill No</th>
					<th width="90">Bill Date</th>
					<th width="100">Bill Qty.(Kg)</th>
					<th width="100" title="Current Conversion Rate: <?= $conversion_rate;?>">Bill Amount (TK)</th>
                    <th width="100">Entry User</th>
					<th>Insert Date & Time</th>
				</thead>
			</table>
		<div style="max-height:300px; overflow-y:scroll; width:1080px" id="scroll_body">
			<table width="1060" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
			<?
			$i=1;
			foreach ($dataArr as $bill_id=>$row)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$bill_no="";
				if($row[process_id]==2 || $row[process_id]==4)
				{
					$bill_no="<a href='##' style='color:#000' onclick=\"generate_bill_report('".$row[process_id]."','".str_replace("'","",$cbo_company_id)."','".$row[id]."','".$row[bill_no]."','1')\"><font style='font-weight:bold' color='#0000FF'>".$row[bill_no]."</font></a><br>";
				}
				else $bill_no=$row[bill_no];
				$delivery_qty=$bill_arr[$bill_id]['qty'];
				$delivery_qty=$bill_arr[$bill_id]['qty'];
				$delivery_amt=$bill_arr[$bill_id]['amt'];
			?>
				<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
					<td width="30"><? echo $i; ?></td>
					<td width="100"><? echo $production_process[$row[process_id]]; ?></td>
					<td width="120"><? echo $knitting_source[$row[party_source]]; ?></td>
					<td width="100"><? echo $row[party_id]; ?></td>
					<td width="100"><? echo $bill_for[$row[bill_for]]; ?></td>
					<td width="120"><? echo $bill_no; ?></td>
					<td width="90" align="center"><? echo '&nbsp;'.change_date_format($row[bill_date]); ?></td>
					<td width="100" align="right"><? echo number_format($delivery_qty,2); ?></td>
					<td width="100" align="right"><? echo number_format($delivery_amt,2); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $user_arr[$row[inserted_by]]; ?></td>
					<td style="word-break:break-all"><? echo $row[insert_date]; ?></td>
				</tr>
			<?							
				$grand_qty+=$delivery_qty;
				$grand_amu+=$delivery_amt;
				$i++;		
			}
			?>
		</table> 
		</div>
		<table width="1080" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
				<td width="30">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>                            
				<td width="120">&nbsp;</td>
				<td width="90">Total:</td>
				<td width="100" id="bill_qty_value"><? echo number_format($grand_qty,2);?></td>
				<td width="100" id="value_bill_amu_value"><? echo number_format($grand_amu,2);?></td>
                <td width="100">&nbsp;</td>
				<td>&nbsp;</td> 
			</tr>
		</table> 
		</div>
        </fieldset>
		<?
	}
	else if($search_type==2)// Out-Bound Bill
	{
		$where_cond="";
		if(trim($txt_date_from)=="" && trim($txt_date_to)=="") $where_cond=""; else  $where_cond=" and a.bill_date between '$txt_date_from' and '$txt_date_to'";
		if($cbo_company_id==0) $where_cond.=""; else  $where_cond.=" and a.company_id=$cbo_company_id";
		if($cbo_location_id==0) $where_cond.=""; else  $where_cond.=" and a.location_id=$cbo_location_id";
		if($cbo_party_name==0) $where_cond.=""; else  $where_cond.=" and a.supplier_id=$cbo_party_name";
		if($cbo_bill_type==0) $where_cond.=""; else  $where_cond.=" and a.process_id=$cbo_bill_type";
		if($txt_bill_no=="") $where_cond.=""; else  $where_cond.=" and a.prefix_no_num=$txt_bill_no";
		if($cbo_bill_for==0) $where_cond.=""; else  $where_cond.=" and a.bill_for=$cbo_bill_for";
		
		$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
		$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
		
		//Query-----------------------------------------------------------------------------------
		 $sql="select a.id, a.process_id, a.supplier_id, a.bill_for, a.prefix_no_num, a.bill_no, a.bill_date, a.party_bill_no, a.inserted_by, a.insert_date, sum(b.receive_qty) as bill_qty, sum(b.amount) as amount
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $where_cond
		group by a.id, a.process_id, a.supplier_id, a.bill_for, a.prefix_no_num, a.bill_no, a.bill_date, a.party_bill_no, a.inserted_by, a.insert_date order by a.id ASC ";
		$sql_result=sql_select($sql);
		?>
		<div style="width:1200px; margin:0 auto;">
        	<table cellpadding="0" cellspacing="0" width="880">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Bill Statement- Out-Bound'; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
			<table width="1200" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="100">Bill Type</th>
					<th width="120">Source</th>
					<th width="100">Party</th>
					<th width="100">Bill For</th>                            
					<th width="120">Bill No</th>
                    <th width="120">Party Bill No</th>
					<th width="90">Bill Date</th>
					<th width="100">Bill Qty.(Kg)</th>
					<th width="100">Bill Amount (TK)</th>
                    <th width="100">Entry User</th>
					<th>Insert Date & Time</th>
				</thead>
			</table>
			
		<div style="max-height:300px; overflow-y:scroll; width:1200px" id="scroll_body">
			<table width="1180" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
			<?
			$i=1;
			foreach ($sql_result as $bill_id=>$row)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				//echo $row[csf('process_id')].'DDDD';
				$bill_no="";
				if($row[csf('process_id')]==2 || $row[csf('process_id')]==4)
				{
					$bill_no="<a href='##' style='color:#000' onclick=\"generate_bill_report('".$row[csf('process_id')]."','".str_replace("'","",$cbo_company_id)."','".$row[csf('id')]."','".$row[csf('bill_no')]."',2)\"><font style='font-weight:bold' color='#0000FF'>".$row[csf('bill_no')]."</font></a><br>";
				}
				else $bill_no=$row[csf('bill_no')];
				
			?>
				<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
					<td width="30"><? echo $i; ?></td>
					<td width="100"><? echo $production_process[$row[csf('process_id')]]; ?></td>
					<td width="120"><? echo "Out-Bound"; ?></td>
					<td width="100"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
					<td width="100"><? echo $bill_for[$row[csf('bill_for')]]; ?></td>
					<td width="120"><? echo $bill_no; ?></td>
                    <td width="120"><? echo $row[csf('party_bill_no')]; ?></td>
					<td width="90" align="center"><? echo '&nbsp;'.change_date_format($row[csf('bill_date')]); ?></td>
					<td width="100" align="right"><? echo number_format($row[csf('bill_qty')],2); ?></td>
					<td width="100" align="right"><? echo number_format($row[csf('amount')],2); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
					<td style="word-break:break-all"><? echo $row[csf('insert_date')]; ?></td>
				</tr>
				<?							
				$grand_qty+=$row[csf('bill_qty')];
				$grand_amu+=$row[amount];
				$i++;		
			}
			?>
	
		</table> 
		</div>
		<table width="1200" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom"> 
			<tr class="tbl_bottom">
				<td width="30">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>                            
				<td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
				<td width="90">Total: </td>
				<td width="100" id="bill_qty"><? echo number_format($grand_qty);?></td>
				<td width="100" id="value_bill_amu"><? echo number_format($grand_amu,2);?></td>
                <td width="100">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table> 
		</div>
		<?
	}
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}


?>