<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 120, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b  where a.id=b.supplier_id and b.tag_company in ($data) and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select Store --", 0, "" );
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$report_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_supplier_name=str_replace("'","",$cbo_supplier_name);
	$cbo_pi_year=str_replace("'","",$cbo_pi_year);
	$cbo_pi_status=str_replace("'","",$cbo_pi_status);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
    if ($cbo_company_name!=''){ $search_cond.=" and g.importer_id in ($cbo_company_name)"; }
    if ($cbo_supplier_name!=''){ $search_cond.=" and g.supplier_id in ($cbo_supplier_name)"; }

    if($db_type==0){ $search_cond.=" and year(g.insert_date)=".$cbo_pi_year.""; }
    else{ $search_cond.=" and to_char(g.insert_date,'YYYY')=".$cbo_pi_year.""; }

    // $companyArr = return_library_array("SELECT id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
    $companyShortArr = return_library_array("SELECT id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
    $lib_supplier=return_library_array( "SELECT id, supplier_name from lib_supplier",'id','supplier_name');
    $item_group_arr = return_library_array("SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");

    if($date_from != '' && $date_to != '')
	{
		if ($db_type==0){$search_cond.= " and g.pi_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";}
		else{$search_cond.= " and g.pi_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";}
	}
    if($db_type==0){ 
        $requ_no_clm=" group_concat(distinct(a.requ_no)) "; 
        $requ_date_clm=" group_concat(distinct(a.requisition_date)) "; 
        $wo_no_clm=" group_concat(distinct(d.wo_number)) "; 
        $wo_date_clm=" group_concat(distinct(d.wo_date)) "; 
    }
    else{ 
        $requ_no_clm=" rtrim(xmlagg(xmlelement(e,a.requ_no,', ').extract('//text()') order by a.id).GetClobVal(),', ') "; 
        $requ_date_clm=" rtrim(xmlagg(xmlelement(e,a.requisition_date,', ').extract('//text()') order by a.id).GetClobVal(),', ') "; 
        $wo_no_clm=" rtrim(xmlagg(xmlelement(e,d.wo_number,', ').extract('//text()') order by d.id).GetClobVal(),', ') "; 
        $wo_date_clm=" rtrim(xmlagg(xmlelement(e,d.wo_date,', ').extract('//text()') order by d.id).GetClobVal(),', ') "; 
    }
    $main_sql="SELECT $requ_no_clm as REQU_NO, $requ_date_clm as REQU_DATE, $wo_no_clm as WO_NUMBER, $wo_date_clm as WO_DATE, e.item_description as ITEM_DESCRIPTION, e.unit_of_measure as UOM, e.item_group_id as ITEM_GROUP_ID, e.sub_group_name as SUB_GROUP_NAME, e.item_size as ITEM_SIZE, e.model as MODEL, e.item_number as ITEM_NUMBER, e.item_code as ITEM_CODE, g.id as PI_ID, g.importer_id as IMPORTER_ID, g.pi_number as PI_NUMBER, g.pi_date as PI_DATE, g.supplier_id as  SUPPLIER_ID, g.item_category_id as CATEGORY_ID, sum(f.quantity) as PI_QNTY, i.lc_number as LC_NUMBER, i.lc_date as LC_DATE
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, wo_non_order_info_dtls c, wo_non_order_info_mst d, com_pi_item_details f, product_details_master e, com_pi_master_details g
    left join com_btb_lc_pi h on g.id=h.pi_id and h.status_active=1
    left join com_btb_lc_master_details i on h.com_btb_lc_master_details_id=i.id and i.status_active=1
	where a.id=b.mst_id and b.id=c.requisition_dtls_id and c.mst_id=d.id and d.id=f.work_order_id and c.id=f.work_order_dtls_id and f.item_prod_id=e.id and f.pi_id=g.id and a.entry_form=69 and d.entry_form = 145 and g.item_category_id in(5,6,7,23) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 $search_cond 
    group by e.item_description,e.unit_of_measure,e.item_group_id,e.sub_group_name, e.item_size, e.model, e.item_number, e.item_code,g.id,g.importer_id,g.pi_number,g.pi_date,g.supplier_id,g.item_category_id,i.lc_number,i.lc_date
    order by g.id desc";
	// echo $main_sql;die;

	$main_data=sql_select($main_sql);
	foreach ($main_data as $row) 
	{
		$pi_id_arr[$row['PI_ID']]=$row['PI_ID'];
        // $pi_count[$row['PI_ID']]++;
	}

    $pi_id_in=where_con_using_array($pi_id_arr,0,'a.booking_id');
    $rcv_sql="SELECT a.booking_id as PI_ID, b.prod_id as PROD_ID, sum(b.order_qnty) as QNTY, c.item_group_id as ITEM_GROUP_ID, c.sub_group_name as SUB_GROUP_NAME, c.item_description as ITEM_DESCRIPTION, c.item_size as ITEM_SIZE, c.model as MODEL, c.item_number as ITEM_NUMBER, c.item_code as ITEM_CODE
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=4 and a.receive_basis=1 and b.receive_basis=1 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 $pi_id_in 
    group by a.booking_id, b.prod_id,c.item_group_id,c.sub_group_name,c.item_description,c.item_size,c.model,c.item_number,c.item_code";
    // echo $rcv_sql;
    $rcv_data=sql_select($rcv_sql);
    foreach ($rcv_data as $row) 
	{
        $key=$row['ITEM_GROUP_ID']."**".$row['SUB_GROUP_NAME']."**".$row['ITEM_DESCRIPTION']."**".$row['ITEM_SIZE']."**".$row['MODEL']."**".$row['ITEM_NUMBER']."**".$row['ITEM_CODE'];
		$rcv_data_info[$row['PI_ID']][$key]['qnty']+=$row['QNTY'];
		$rcv_data_info[$row['PI_ID']][$key]['prod_id'].=$row['PROD_ID'].',';
	}

    foreach ($main_data as $row) 
	{
        $key=$row['ITEM_GROUP_ID']."**".$row['SUB_GROUP_NAME']."**".$row['ITEM_DESCRIPTION']."**".$row['ITEM_SIZE']."**".$row['MODEL']."**".$row['ITEM_NUMBER']."**".$row['ITEM_CODE'];
        $rcv_qnty=$rcv_data_info[$row['PI_ID']][$key]['qnty'];
        $pi_balance=$row['PI_QNTY']-$rcv_qnty;
        if((($cbo_pi_status==0 || $cbo_pi_status==4) && $pi_balance!=0) || ($cbo_pi_status==1 && $rcv_qnty=='') || ($cbo_pi_status==2 && $rcv_qnty!='' && $pi_balance!=0) || ($cbo_pi_status==3 && $pi_balance==0) || ($cbo_pi_status==5))
        {
            $pi_count[$row['PI_ID']]++;
        }
	}

    $tbl_width=1700;
    ob_start();
    ?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
    <div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:<?=$tbl_width+20;?>px">
        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?=$tbl_width+18;?>" rules="all" id="rpt_table_header">
            <caption><b><?  echo $report_title; ?></b></caption>
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="80">Company</th>
                    <th width="150">Supplier Name</th>
                    <th width="80">PI No</th>
                    <th width="80">PI Date</th>
                    <th width="150">BTB LC No</th>
                    <th width="80">LC Date</th>
                    <th width="80">Item Category</th>
                    <th width="80">Item Group</th>
                    <th width="150">Item Descriptions</th>
                    <th width="80">UOM</th>
                    <th width="80">PI Qty</th>
                    <th width="80">MRR Qnty</th>
                    <th width="80">MRR Balance Qty</th>
                    <th width="100">Req. No</th>
                    <th width="100">Req Date</th>
                    <th width="100">Po No</th>
                    <th >Po Date</th>
                </tr>
            </thead>
        </table>
        <div style="width:<?=$tbl_width+18;?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
            <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?=$tbl_width;?>" rules="all" align="left">
                <?
                    $row_chk=array();
                    $i=1;$z=1;
                    foreach ($main_data as $row) 
                    {
                        $key=$row['ITEM_GROUP_ID']."**".$row['SUB_GROUP_NAME']."**".$row['ITEM_DESCRIPTION']."**".$row['ITEM_SIZE']."**".$row['MODEL']."**".$row['ITEM_NUMBER']."**".$row['ITEM_CODE'];
                        $rcv_qnty=$rcv_data_info[$row['PI_ID']][$key]['qnty'];
                        $pi_balance=$row['PI_QNTY']-$rcv_qnty;
                        $prod_id=chop($rcv_data_info[$row['PI_ID']][$key]['prod_id'],',');
                        
                        if((($cbo_pi_status==0 || $cbo_pi_status==4) && $pi_balance!=0) || ($cbo_pi_status==1 && $rcv_qnty=='') || ($cbo_pi_status==2 && $rcv_qnty!='' && $pi_balance!=0) || ($cbo_pi_status==3 && $pi_balance==0) || ($cbo_pi_status==5))
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $rowspan=$pi_count[$row['PI_ID']];	
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                
                                <?
                                    if(!in_array($row['PI_ID'],$row_chk))
                                    {
                                        $row_chk[]=$row['PI_ID'];
                                        ?>
                                            <td width="30" rowspan="<?=$rowspan;?>" class="wrd_brk center"><? echo $z; ?></td>
                                            <td width="80" rowspan="<?=$rowspan;?>" class="wrd_brk center"><p><? echo $companyShortArr[$row['IMPORTER_ID']]; ?>&nbsp;</p></td>
                                            <td width="150" rowspan="<?=$rowspan;?>"  class="wrd_brk "><p><? echo $lib_supplier[$row['SUPPLIER_ID']]; ?>&nbsp;</p></td>
                                            <td width="80" rowspan="<?=$rowspan;?>" class="wrd_brk center"><p><? echo $row['PI_NUMBER'];?>&nbsp;</p></td>
                                            <td width="80" rowspan="<?=$rowspan;?>" class="wrd_brk "><p><? echo change_date_format($row['PI_DATE']); ?>&nbsp;</p></td>
                                            <td width="150" rowspan="<?=$rowspan;?>"  class="wrd_brk "><p><? echo $row['LC_NUMBER']; ?>&nbsp;</p></td>
                                            <td width="80" rowspan="<?=$rowspan;?>" class="wrd_brk "><p><? echo change_date_format($row['LC_DATE']); ?>&nbsp;</p></td>
                                            <td width="80" rowspan="<?=$rowspan;?>" class="wrd_brk"><p><? echo $item_category[$row['CATEGORY_ID']]; ?>&nbsp;</p></td>
                                        <?
                                        $z++;
                                    }
                                ?>
                                <td width="80" class="wrd_brk"><p><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?>&nbsp;</p></td>
                                <td width="150" class="wrd_brk"><p><? echo $row['ITEM_DESCRIPTION']; ?>&nbsp;</p></td>
                                <td width="80" class="wrd_brk center"><p><? echo $unit_of_measurement[$row['UOM']]; ?>&nbsp;</p></td>
                                <td width="80" class="wrd_brk right"><? echo number_format($row['PI_QNTY'],2); ?></td>
                                <td width="80" class="wrd_brk right">
                                    <a href='##' onclick="fnc_rcv_details('<? echo $row['PI_ID'];?>','<? echo $prod_id; ?>','Receive Details','rcv_popup_details')"><? echo number_format($rcv_qnty,2); ?></a>
                                </td>
                                <td width="80" class="wrd_brk right"><? echo number_format($pi_balance,2); ?></td>
                                <td width="100" class="wrd_brk"><p><? echo implode(", ",array_unique(explode(", ",$row['REQU_NO']->load()))); ?>&nbsp;</p></td>
                                <td width="100" class="wrd_brk"><p><? echo implode(", ",array_unique(explode(", ",$row['REQU_DATE']->load()))) ;?>&nbsp;</p></td>
                                <td width="100" class="wrd_brk"><p><? echo implode(", ",array_unique(explode(", ",$row['WO_NUMBER']->load()))); ?>&nbsp;</p></td>
                                <td class="wrd_brk"><p><? echo implode(", ",array_unique(explode(", ",$row['WO_DATE']->load()))); ?>&nbsp;</p></td>
                                <?		                        
                            $i++;							
                        }
                    }
                ?>
            </table>
        </div>
    </div>
    <?

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
    echo "$html**$filename**$report_type";
    exit();
}

if ($action=="rcv_popup_details") 
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
    $userArr = return_library_array("SELECT id,user_name from user_passwd ","id","user_name");

    $sql="SELECT a.recv_number as RECV_NUMBER, a.receive_basis as RECEIVE_BASIS, a.challan_no as CHALLAN_NO, a.receive_date as RECEIVE_DATE, b.order_qnty as RCV_QNTY, b.order_uom as ORDER_UOM, b.order_rate as ORDER_RATE, b.order_amount as ORDER_AMOUNT, b.remarks as REMARKS, b.inserted_by as INSERTED_BY
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and a.booking_id=$pi_id and b.prod_id in ($prod_id) and a.entry_form=4 and a.receive_basis=1 and b.receive_basis=1 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 ";
    // echo $sql;
    $result = sql_select($sql);
	?>
    <style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<fieldset style="width:650px">
		<legend>Receive Details</legend>
		<table width="910" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<th width="30">SL</th>
				<th width="80">Receive Basis</th>
				<th width="100">MRR No.</th>
				<th width="80">Challan No</th>
				<th width="80">Receive Date</th>
				<th width="80">UOM</th>
                <th width="80">Qty</th>
				<th width="80">Rate</th>
                <th width="80">Value</th>
                <th width="100">Remarks</th>
                <th>Insert user</th>
			</thead>
            <tbody>
				<?
				$i = 1;
				foreach ($result as $row) 
                {
					if($i % 2 == 0){ $bgcolor = "#E9F3FF"; }else{ $bgcolor = "#FFFFFF"; }				
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td class="center"><p><? echo $i; ?>&nbsp;</p></td>
						<td class="wrd_brk"><p><? echo $receive_basis_arr[$row["RECEIVE_BASIS"]]; ?>&nbsp;</p></td>
						<td class="wrd_brk"><p><? echo $row["RECV_NUMBER"]; ?>&nbsp;</p></td>
						<td class="wrd_brk"><p><? echo $row["CHALLAN_NO"]; ?>&nbsp;</p></td>
						<td class="wrd_brk center"><p><? echo change_date_format($row["RECEIVE_DATE"]); ?>&nbsp;</p></td>
                        <td class="wrd_brk center"><p><? echo $unit_of_measurement[$row["ORDER_UOM"]]; ?>&nbsp;</p></td>
                        <td class="wrd_brk right"><p><? echo number_format($row["RCV_QNTY"],2); ?></p></td>
						<td class="wrd_brk right"><? echo number_format($row["ORDER_RATE"],2); ?></td>
						<td class="wrd_brk right"><? echo number_format($row["ORDER_AMOUNT"],2); ?></td>
						<td class="wrd_brk"><? echo $row["REMARKS"]; ?>&nbsp;</td>
						<td class="wrd_brk"><? echo $userArr[$row["INSERTED_BY"]]; ?>&nbsp;</td>
					</tr>
					<?
					$i++;
				}
				?>
            </tbody>
        </table>
	</fieldset>
	<?
	exit();
}