<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$report_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$cbo_req_year=str_replace("'","",$cbo_req_year);
	$cbo_req_status=str_replace("'","",$cbo_req_status);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$report_type=str_replace("'","",$report_type);

    $companyShortArr = return_library_array("SELECT id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
    $item_group_arr = return_library_array("SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
    $userArr = return_library_array("SELECT id,user_name from user_passwd ","id","user_name");
	
    if($report_type==1)
    {
        if ($cbo_company_name!=''){ $search_cond.=" and a.company_id in ($cbo_company_name)"; }
        if ($cbo_item_category!=''){ $search_cond.=" and b.item_category in ($cbo_item_category)"; }

        if($db_type==0){ $search_cond.=" and year(a.insert_date)=".$cbo_req_year.""; }
        else{ $search_cond.=" and to_char(a.insert_date,'YYYY')=".$cbo_req_year.""; }

        if($date_from != '' && $date_to != '')
        {
            if ($db_type==0){$search_cond.= " and a.requisition_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";}
            else{$search_cond.= " and a.requisition_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";}
        }
        if($db_type==0){ 
            $wo_id_clm=" group_concat(distinct(e.id)) "; 
            $wo_no_clm=" group_concat(distinct(e.wo_number)) "; 
            $wo_date_clm=" group_concat(distinct(e.wo_date)) "; 
            $req_year=" year(a.insert_date)"; 
        }
        else{ 
            $wo_id_clm=" rtrim(xmlagg(xmlelement(e,e.id,', ').extract('//text()') order by e.id).GetClobVal(),', ') "; 
            $wo_no_clm=" rtrim(xmlagg(xmlelement(e,e.wo_number,', ').extract('//text()') order by e.id).GetClobVal(),', ') "; 
            $wo_date_clm=" rtrim(xmlagg(xmlelement(e,e.wo_date,', ').extract('//text()') order by e.id).GetClobVal(),', ') "; 
            $req_year=" to_char(a.insert_date,'YYYY') ";
        }
        $main_sql="SELECT a.id as REQ_ID,a.company_id as COMPANY_ID, a.requ_no as REQU_NO, a.requisition_date as REQU_DATE,$req_year  as REQ_YEAR,a.inserted_by as INSERTED_BY, b.item_category as CATEGORY_ID, sum(b.quantity) as REQU_QNTY,$wo_id_clm as WO_ID, $wo_no_clm as WO_NUMBER, $wo_date_clm as WO_DATE, sum(d.supplier_order_quantity) as WO_QNTY, c.id as PROD_ID, c.item_description as ITEM_DESCRIPTION, c.unit_of_measure as UOM, c.item_group_id as ITEM_GROUP_ID , c.sub_group_name as SUB_GROUP_NAME, c.item_size as ITEM_SIZE, c.model as MODEL, c.item_number as ITEM_NUMBER, c.item_code as ITEM_CODE
        from inv_purchase_requisition_mst a, product_details_master c, inv_purchase_requisition_dtls b
        left join wo_non_order_info_dtls d on b.id=d.requisition_dtls_id and b.product_id=d.item_id and d.status_active=1
        left join wo_non_order_info_mst e on d.mst_id=e.id and e.entry_form = 145 and e.status_active=1
        where a.id=b.mst_id and b.product_id=c.id and a.entry_form=69 and a.pay_mode<>4 and b.item_category in(5,6,7,23) and a.status_active=1 and b.status_active=1 and c.status_active=1 $search_cond 
        group by a.id,a.company_id,a.requ_no, a.requisition_date, $req_year,a.inserted_by, b.item_category, c.id, c.item_description, c.unit_of_measure, c.item_group_id, c.sub_group_name, c.item_size, c.model, c.item_number, c.item_code
        order by a.id desc";
        // echo $main_sql;

        $main_data=sql_select($main_sql);
        foreach ($main_data as $row) 
        {
            $wo_id=$row['WO_ID']->load(); 
            $key=$row['ITEM_GROUP_ID']."**".$row['SUB_GROUP_NAME']."**".$row['ITEM_DESCRIPTION']."**".$row['ITEM_SIZE']."**".$row['MODEL']."**".$row['ITEM_NUMBER']."**".$row['ITEM_CODE'];
            $req_wo_id_arr[$row['REQ_ID']][$key]=$wo_id; 
            $wo_id_all.=$wo_id.', ';  
        }

        $wo_id_all=array_unique(explode(", ",chop($wo_id_all,', ')));
        foreach($wo_id_all as $row)
        {
            if($row)
            {
                $wo_id_arr[$row]=$row;
            }
        }
        $wo_id_in=where_con_using_array($wo_id_arr,0,'a.booking_id');
        $rcv_sql="SELECT a.booking_id as BOOKING_ID, b.prod_id as PROD_ID, sum(b.cons_quantity) as QNTY, c.item_group_id as ITEM_GROUP_ID, c.sub_group_name as SUB_GROUP_NAME, c.item_description as ITEM_DESCRIPTION, c.item_size as ITEM_SIZE, c.model as MODEL, c.item_number as ITEM_NUMBER, c.item_code as ITEM_CODE
        from inv_receive_master a, inv_transaction b, product_details_master c 
        where a.id=b.mst_id and a.entry_form=4 and b.prod_id=c.id and a.receive_basis=2 and b.receive_basis=2 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 $wo_id_in 
        group by a.booking_id, b.prod_id, c.item_group_id,c.sub_group_name,c.item_description,c.item_size,c.model,c.item_number,c.item_code";
        // echo $rcv_sql;
        $rcv_data=sql_select($rcv_sql);
        foreach ($rcv_data as $row) 
        {
            $key=$row['ITEM_GROUP_ID']."**".$row['SUB_GROUP_NAME']."**".$row['ITEM_DESCRIPTION']."**".$row['ITEM_SIZE']."**".$row['MODEL']."**".$row['ITEM_NUMBER']."**".$row['ITEM_CODE'];
            $rcv_data_info[$row['BOOKING_ID']][$key]['rcv_qnty']+=$row['QNTY'];
            $rcv_data_info[$row['BOOKING_ID']][$key]['prod_id'].=$row['PROD_ID'].',';
        }

        $tbl_width=1400;
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
                        <th width="50">Req Year</th>
                        <th width="120">Req. No</th>
                        <th width="80">Req Date</th>
                        <th width="100">Category</th>
                        <th width="80">Item Group</th>
                        <th width="150">Item Descriptions</th>
                        <th width="50">UOM</th>
                        <th width="80">Req. Qty</th>
                        <th width="80">PO Qty.</th>
                        <th width="80">PO. Balance</th>
                        <th width="100">PO No</th>
                        <th width="100">PO Date</th>
                        <th width="80">MRR Qty</th>
                        <th >Req Insert User</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<?=$tbl_width+18;?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
                <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?=$tbl_width;?>" rules="all" align="left">
                    <?
                        $i=1;
                        foreach ($main_data as $row) 
                        {
                            $req_balance=$row['REQU_QNTY']-$row['WO_QNTY'];
                            if((($cbo_req_status==0 || $cbo_req_status==3) && $req_balance!=0) || ($cbo_req_status==1 && $row['WO_QNTY']=='') || ($cbo_req_status==2 && $row['WO_QNTY']!='' && $req_balance!=0) || ($cbo_req_status==4 && $req_balance==0) || ($cbo_req_status==5))
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                                $key=$row['ITEM_GROUP_ID']."**".$row['SUB_GROUP_NAME']."**".$row['ITEM_DESCRIPTION']."**".$row['ITEM_SIZE']."**".$row['MODEL']."**".$row['ITEM_NUMBER']."**".$row['ITEM_CODE'];

                                $wo_id=array_unique(explode(",",$req_wo_id_arr[$row['REQ_ID']][$key]));
                                $rcv_qnty=0;$prod_id='';
                                foreach($wo_id as $val)
                                {
                                    $rcv_qnty+=$rcv_data_info[$val][$key]['rcv_qnty'];
                                    $prod_id.=$rcv_data_info[$val][$key]['prod_id'];
                                }
                                $prod_id=implode(",",array_unique(explode(",",chop($prod_id,','))));
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    
                                    <td width="30" class="wrd_brk center"><? echo $i; ?></td>
                                    <td width="80" class="wrd_brk center"><p><? echo $companyShortArr[$row['COMPANY_ID']]; ?>&nbsp;</p></td>
                                    <td width="50"  class="wrd_brk center"><p><? echo $row['REQ_YEAR']; ?>&nbsp;</p></td>
                                    <td width="120" class="wrd_brk center"><p><? echo $row['REQU_NO'];?>&nbsp;</p></td>
                                    <td width="80" class="wrd_brk center"><p><? echo change_date_format($row['REQU_DATE']); ?>&nbsp;</p></td>
                                    <td width="100"  class="wrd_brk center"><p><? echo $item_category[$row['CATEGORY_ID']]; ?>&nbsp;</p></td>
                                    <td width="80" class="wrd_brk "><p><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?>&nbsp;</p></td>
                                    <td width="150" class="wrd_brk"><p><? echo $row['ITEM_DESCRIPTION']; ?>&nbsp;</p></td>
                                    <td width="50" class="wrd_brk center"><p><? echo $unit_of_measurement[$row['UOM']]; ?>&nbsp;</p></td>
                                    <td width="80" class="wrd_brk right"><p><? echo number_format($row['REQU_QNTY'],2);?></p></td>
                                    <td width="80" class="wrd_brk right"><p><?echo number_format($row['WO_QNTY'],2); ?>&nbsp;</p></td>
                                    <td width="80" class="wrd_brk right"><? echo number_format($req_balance,2); ?></td>
                                    <td width="100" class="wrd_brk"><p><? echo $row['WO_NUMBER']->load(); ?>&nbsp;</p></td>
                                    <td width="100" class="wrd_brk"><p><? echo $row['WO_DATE']->load(); ?>&nbsp;</p></td>
                                    <td width="80" class="wrd_brk right">
                                        <a href='##' onclick="fnc_rcv_details('<? echo $row['WO_ID']->load();?>','<? echo $prod_id; ?>','Receive Details','rcv_popup_details')"><? echo number_format($rcv_qnty,2); ?></a>
                                    </td>                                
                                    <td class="wrd_brk"><p><? echo $userArr[$row['INSERTED_BY']]; ?>&nbsp;</p></td>
                                    <?		                        
                                $i++;							
                            }
                        }
                    ?>
                </table>
            </div>
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
    echo "$html####$filename####$report_type";
    exit();
}

if ($action=="rcv_popup_details") 
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
    $userArr = return_library_array("SELECT id,user_name from user_passwd ","id","user_name");

    $sql="SELECT a.recv_number as RECV_NUMBER, a.receive_basis as RECEIVE_BASIS, a.challan_no as CHALLAN_NO, a.receive_date as RECEIVE_DATE, b.cons_quantity as RCV_QNTY, b.cons_uom as CONS_UOM, b.cons_rate as CONS_RATE, b.cons_amount as CONS_AMOUNT, b.remarks as REMARKS, b.inserted_by as INSERTED_BY
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and a.booking_id in($wo_id) and b.prod_id in($prod_id) and a.entry_form=4 and a.receive_basis=2 and b.receive_basis=2 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 ";
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
				<th width="50">UOM</th>
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
                        <td class="wrd_brk center"><p><? echo $unit_of_measurement[$row["CONS_UOM"]]; ?>&nbsp;</p></td>
                        <td class="wrd_brk right"><p><? echo number_format($row["RCV_QNTY"],2); ?></p></td>
						<td class="wrd_brk right"><? echo number_format($row["CONS_RATE"],2); ?></td>
						<td class="wrd_brk right"><? echo number_format($row["CONS_AMOUNT"],2); ?></td>
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