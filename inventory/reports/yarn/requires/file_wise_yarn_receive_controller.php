<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );


if($action=="load_drop_down_file_year")
{
	//$sql="select a.lc_year from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by a.lc_year";
 $sql="select a.lc_year from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by a.lc_year
    union
    select a.sc_year as lc_year from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by  a.sc_year
    order by lc_year";


	echo create_drop_down( "cbo_file_year", 150, $sql,"lc_year,lc_year", 1, "---- Year ----", 0, "" );
	exit();
}

if($action=="file_search")
{

	echo load_html_head_contents("Export LC Form", "../../../../", 1, 1,'','1','');
	extract($_REQUEST);
	$sql="select a.internal_file_no, a.lc_year, 1 as type from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$company_id and a.lc_year='$file_year' group by a.internal_file_no, a.lc_year
	union
	select a.internal_file_no, a.sc_year as lc_year, 2 as type from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$company_id and a.sc_year='$file_year' group by a.internal_file_no, a.sc_year
	order by internal_file_no";



	?>

	<script>

	 var selected_id = new Array, selected_name = new Array();
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			var file=$("#td_"+str).html();
			if( jQuery.inArray(file, selected_id ) == -1 ) {
				selected_id.push(file);

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == file) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#txt_selected_id').val( id );
		}
    </script>

</head>
<body>
    <div align="center" style="width:520px;">
        <form name="searchexportlcfrm" id="searchexportlcfrm">
            <fieldset style="width:500px; margin-left:3px">
            	<input type="hidden" id="txt_selected_id" >
                <table cellpadding="0" cellspacing="0" width="100%" class="rpt_table"  border="1" rules="all">
                	<thead>
                        <th width="50">Sl</th>
                        <th width="80">Year</th>
                        <th>File No</th>
                    </thead>
                </table>
                <div style="width:500px; max-height:290px; overflow:auto;" >
                	<table cellpadding="0" cellspacing="0" width="480" class="rpt_table" id="table_body" border="1" rules="all">
                        <tbody>
                        <?
						$sql_result=sql_select($sql);$i=1;
						foreach($sql_result as $row)
						{	if(!in_array($row[csf("internal_file_no")],$temp_file_arr)){
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
                        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $i; ?>');" style="cursor:pointer;" id="tr_<? echo $i;?>">
                                <td width="50" align="center"><? echo $i; ?></td>
                                <td width="80"><? echo $row[csf("lc_year")]; ?></td>
                                <td id="td_<? echo $i;?>"><? echo $row[csf("internal_file_no")]; ?></td>
                            </tr>
                            <?
							$i++;
							$temp_file_arr[]=$row[csf("internal_file_no")];
							}
						}
						?>
                        </tbody>
                    </table>
                    <script>setFilterGrid('table_body',-1);</script>
                </div>
            </fieldset>
            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
        </form>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();

}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
		$txt_internal_file_no.="'".$file_no."',";
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	//echo $txt_internal_file_no.test;die;
	if($txt_internal_file_no){$file_con=" and a.internal_file_no in($txt_internal_file_no)";}else{$file_con="";}
	//echo $file_con.test;die;
	
    $sql="
    SELECT
    	a.booking_id as pi_id,
    	b.remarks as rec_remarks,
    	a.recv_number as mrr_number,
    	a.receive_date,
    	a.supplier_id,
    	b.order_qnty as rec_qty,
    	b.order_rate as rec_rate,
    	b.order_amount as rec_amount,
    	c.product_name_details,
    	c.lot
    FROM
    	inv_receive_master a,
    	inv_transaction b,
    	product_details_master c
    WHERE
    	a.id=b.mst_id and
    	a.entry_form=1 and
    	a.receive_basis=1 and
    	b.prod_id=c.id and b.item_category=1 and c.item_category_id=1 and
    	a.company_id=$cbo_company_name and
    	a.status_active=1 and
    	a.is_deleted=0
    ";
    //echo $sql;
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$rec_data_arr[$row[csf('pi_id')]][$row[csf('product_name_details')]][]=array(
			'receive_date'=>$row[csf('receive_date')],
			'mrr_number'=>$row[csf('mrr_number')],
			'lot'=>$row[csf('lot')],
			'supplier_id'=>$row[csf('supplier_id')],
			'rec_qty'=>$row[csf('rec_qty')],
			'rec_rate'=>$row[csf('rec_rate')],
			'yarn_type'=>$row[csf('yarn_type')],
			'pi_qty'=>$row[csf('pi_qty')],
			'net_pi_rate'=>$row[csf('net_pi_rate')],
			'rec_amount'=>$row[csf('rec_amount')],
			'rec_remarks'=>$row[csf('rec_remarks')]
		);

	}



	$sqlRtn = 'select b.id, b.issue_number, a.order_rate,a.cons_quantity,a.cons_rate,a.order_qnty,b.received_mrr_no,c.lot,a.id as trans_id
	from inv_transaction a, inv_issue_master b, product_details_master c
	where a.mst_id = b.id and a.transaction_type = 3 and a.status_active = 1 and a.is_deleted = 0
	and b.status_active = 1 and b.is_deleted = 0 and a.prod_id = c.id  and a.item_category=1 and c.item_category_id=1';
	$resultRtn=sql_select($sqlRtn); $receive_return_arr = array(); $transIdChk =array();
	foreach($resultRtn as $row)
	{
		if($transIdChk[$row[csf('trans_id')]] == "")
		{
			$transIdChk[$row[csf('trans_id')]] = $row[csf('trans_id')];
			$receive_return_arr[$row[csf('received_mrr_no')]][$row[csf('lot')]]["qnty"] += $row[csf('cons_quantity')];
			$receive_return_arr[$row[csf('received_mrr_no')]][$row[csf('lot')]]["rtn_no"] .= $row[csf('issue_number')].",";
			$receive_return_arr[$row[csf('received_mrr_no')]][$row[csf('lot')]]["amount"] += $row[csf('order_rate')]*$row[csf('cons_quantity')];
		}
	}

    // for lc---------------
    $sql="SELECT a.internal_file_no, a.export_lc_no, a.buyer_name, e.pi_number, e.supplier_id, d.pi_id, d.color_id, d.count_name, d.yarn_type, d.net_pi_rate, d.quantity as pi_qty, d.net_pi_amount, d.yarn_composition_item1 as yarn_composition_id, d.yarn_composition_percentage2, f.bank_code, f.lc_year, f.lc_category, f.lc_serial, e.remarks
    	FROM com_export_lc a, com_btb_export_lc_attachment b, com_btb_lc_pi c, com_pi_item_details d, com_pi_master_details e, com_btb_lc_master_details f
    	WHERE a.id=b.lc_sc_id and b.import_mst_id=c.com_btb_lc_master_details_id and b.is_lc_sc=0 and c.pi_id=d.pi_id and c.pi_id=e.id and b.import_mst_id=f.id and a.status_active=1 and  a.is_deleted=0 and d.status_active=1 and  d.is_deleted=0 and e.item_category_id=1 and
    	f.item_category_id=1 and a.beneficiary_name=$cbo_company_name and  a.lc_year='$cbo_file_year' $file_con";
    	//echo $sql;
    	$result=sql_select($sql);
    	foreach($result as $row)
    	{

    		$yarn_composition=$count_arr[$row[csf('count_name')]].' '.$composition[$row[csf('yarn_composition_id')]].' 100 '.$yarn_type[$row[csf('yarn_type')]].' '.$color_arr[$row[csf('color_id')]];

    		$file_data_arr[$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]=array(
    			'buyer_name'=>$row[csf('buyer_name')],
    			'export_lc_no'=>$row[csf('export_lc_no')],
    			'btb_lc'=>$row[csf('bank_code')].$row[csf('lc_year')].$row[csf('lc_category')].$row[csf('lc_serial')],
    			'pi_number'=>$row[csf('pi_number')],
    			'color_id'=>$row[csf('color_id')],
    			'supplier_id'=>$row[csf('supplier_id')],
    			'count_name'=>$row[csf('count_name')],
    			'yarn_composition'=>$yarn_composition,
    			'yarn_type'=>$row[csf('yarn_type')],
    			'pi_qty'=>$row[csf('pi_qty')],
    			'net_pi_rate'=>$row[csf('net_pi_rate')],
    			'net_pi_amount'=>$row[csf('net_pi_amount')],
    			'remarks'=>$row[csf('remarks')]
    		);

    		$amountArr[$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]+=$row[csf('net_pi_amount')];
    		$qtyArr[$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]+=$row[csf('pi_qty')];
    	}


    // for sc---------------
    $sql="SELECT a.internal_file_no, a.contract_no as export_lc_no, a.buyer_name, e.pi_number, e.supplier_id, d.pi_id, d.color_id, d.count_name, d.yarn_type, d.net_pi_rate, d.quantity as pi_qty, d.net_pi_amount, d.yarn_composition_item1 as yarn_composition_id, d.yarn_composition_percentage2, f.bank_code, f.lc_year, f.lc_category, f.lc_serial, f.remarks
    	FROM com_sales_contract a, com_btb_export_lc_attachment b, com_btb_lc_pi c, com_pi_item_details	 d, com_pi_master_details e, com_btb_lc_master_details f
    	WHERE a.id=b.lc_sc_id and b.import_mst_id=c.com_btb_lc_master_details_id and b.is_lc_sc=1 and c.pi_id=d.pi_id and c.pi_id=e.id and b.import_mst_id=f.id and a.status_active=1 and  a.is_deleted=0 and d.status_active=1 and  d.is_deleted=0 and e.item_category_id=1 and f.item_category_id=1 and  a.beneficiary_name=$cbo_company_name and a.sc_year='$cbo_file_year' $file_con";
    	$result=sql_select($sql);
    	foreach($result as $row)
    	{

    		$yarn_composition=$count_arr[$row[csf('count_name')]].' '.$composition[$row[csf('yarn_composition_id')]].' 100 '.$yarn_type[$row[csf('yarn_type')]].' '.$color_arr[$row[csf('color_id')]];

    		$sc_file_data_arr[$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]=array(
    			'buyer_name'=>$row[csf('buyer_name')],
    			'export_lc_no'=>$row[csf('export_lc_no')],
    			'btb_lc'=>$row[csf('bank_code')].$row[csf('lc_year')].$row[csf('lc_category')].$row[csf('lc_serial')],
    			'pi_number'=>$row[csf('pi_number')],
    			'color_id'=>$row[csf('color_id')],
    			'supplier_id'=>$row[csf('supplier_id')],
    			'count_name'=>$row[csf('count_name')],
    			'yarn_composition'=>$yarn_composition,
    			'yarn_type'=>$row[csf('yarn_type')],
    			'pi_qty'=>$row[csf('pi_qty')],
    			'net_pi_rate'=>$row[csf('net_pi_rate')],
    			'net_pi_amount'=>$row[csf('net_pi_amount')],
    			'remarks'=>$row[csf('remarks')]
    		);
    		$sc_amountArr[$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]+=$row[csf('net_pi_amount')];
    		$sc_qtyArr[$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]+=$row[csf('pi_qty')];
    	}




    //var_dump($file_data_arr);


    	ob_start();
    	?>
        <!--<fieldset style="width:1120px">-->
        	<div style="width:100%; margin-left:10px;" align="left">
                <table width="2150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <thead>
                    	 <tr>
                            <th colspan="14">LC Information</th>
                            <th colspan="8">MRR Details</th>
                        </tr>
                    	<tr>
                            <th width="35">SL</th>
                            <th width="80">File No</th>
                            <th width="100">Buyer</th>
                            <th width="100">BTB LC No</th>
                            <th width="100">PI NO</th>
                            <th width="100">Supplier Name</th>
                            <th width="100">Color</th>
                            <th width="60">Count</th>
                            <th width="140">Yarn Composition</th>
                            <th width="100">Yarn Type</th>
                            <th width="100">PI Qty/Kg</th>
                            <th width="100">Unit Price</th>
                            <th width="100">PI Value</th>
                            <th width="100">Remarks</th>

                            <th width="100">Received Date</th>
                            <th width="100">MRR No</th>
                            <th width="100">Lot No</th>
                            <th width="100">MRR Qty.</th>
                            <th width="100">Unit Price</th>
                            <th width="100">MRR Value</th>
                            <th width="100">Remarks</th>
                            <th>LC Balance</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:350px; overflow-y:auto; width:2167px;">
                <table width="2150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <? 	$i=1;
    				//lc........
                        foreach($file_data_arr as $file_no_key=>$pi_data_arr)
                        {

    						foreach($pi_data_arr as $lc_key=>$lc_data_arr){
    						foreach($lc_data_arr as $pi_key=>$composition_data_arr){
    						$tot_pi_qty=0;$tot_pi_amount=0;$tot_rec_qty=0;$tot_rec_amount=0;
    						foreach($composition_data_arr as $row){
    						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
    						$rs=count($rec_data_arr[$pi_key][$row['yarn_composition']]);
    						//$supplier_id=$rec_data_arr[$pi_key][$row['yarn_composition']][0]['supplier_id'];
                            //echo "__s__".$rec_data_arr[$pi_key][$row['yarn_composition']]."__u__";

    						if($rs){$r_sapan=' rowspan="'.$rs.'"';}else{$r_sapan='';}
    					?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="35" <? echo $r_sapan;?>><? echo $i; ?></td>
                                <td width="80" <? echo $r_sapan;?>><p><? echo $file_no_key; ?></p></td>
                                <td width="100" <? echo $r_sapan;?>><? echo $buyer_arr[$row['buyer_name']]; ?></td>
                                <td width="100" <? echo $r_sapan;?> align="center"><p>&nbsp;<? echo $row['btb_lc']; ?></p></td>
                                <td width="100" <? echo $r_sapan;?>><p><? echo $row[pi_number]; ?></p></td>
                                <td width="100" <? echo $r_sapan;?>><p><? echo $supplier_arr[$row['supplier_id']]; ?></p></td>
                                <td width="100" <? echo $r_sapan;?>><p>&nbsp;<? echo $color_arr[$row['color_id']]; ?></p></td>
                                <td width="60" <? echo $r_sapan;?> ><? echo $count_arr[$row['count_name']]; ?>&nbsp;</td>
                                <td width="140" <? echo $r_sapan;?>><? echo $row['yarn_composition']; ?>&nbsp;</td>
                                <td width="100" <? echo $r_sapan;?>><? echo $yarn_type[$row['yarn_type']]; ?>&nbsp;</td>
                                <td width="100" <? echo $r_sapan;?>  align="right">
    							<?
    							//$qtyArr[$row[csf('internal_file_no')]][$row[csf('export_lc_no')]][$row[csf('pi_id')]][$yarn_composition]
    							echo $qtyArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']];
    							$tot_pi_qty+=$qtyArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']];
    							//$row['pi_qty']; $tot_pi_qty+=$row['pi_qty'];
    							?>&nbsp;</td>
                                <td width="100" align="right" <? echo $r_sapan;?>><?
    							echo number_format($amountArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']]/$qtyArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']],2)


    							//echo $row['net_pi_rate']; ?>&nbsp;</td>
                                <td width="100" align="right" <? echo $r_sapan;?>><?
    							echo $amountArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']];
    							$tot_pi_amount+=$amountArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']];


    							//echo $row['net_pi_amount'];$tot_pi_amount+=$row['net_pi_amount']; ?>&nbsp;</td>
                                <td width="100" <? echo $r_sapan;?>><p><? echo $row['remarks']; ?>&nbsp;</p></td>
                            <? if($rs==0){?>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                               <td width="100" <? echo $r_sapan;?>></td>
                               <td></td>
                            </tr>

    							<?
    								}
    							$r=1;
    							foreach($rec_data_arr[$pi_key][$row['yarn_composition']] as $rows){
    								if($r!=1){echo'<tr>';}
                                                                   $amount = $rows['rec_amount'] - $receive_return_arr[$rows['mrr_number']][$rows['lot']]['amount'];
                                                                   $rcvQnty = $rows['rec_qty'] - $receive_return_arr[$rows['mrr_number']][$rows['lot']]['qnty'];
    								?>

                                <td width="100" align="center"><p><? echo $rows['receive_date']; ?>&nbsp; fiq al kik</p></td>
                                <td width="100"><p><? echo $rows['mrr_number']; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $rows['lot']; ?>&nbsp;</p></td>
                                <td width="100" align="right"><p><a href="##" onClick="open_mypage_rcvRtn('<? echo $rows['mrr_number'];?>','<? echo $receive_return_arr[$rows['mrr_number']][$rows['lot']]["rtn_no"];?>','<? echo $rows['lot'];?>');"><? echo $rcvQnty;$tot_rec_qty+=$rcvQnty;//$rows['rec_qty']; $tot_rec_qty+=$rows['rec_qty']; ?></a></p></td>
                                <td width="100" align="right"><p><? echo $rows['rec_rate']; ?>&nbsp;</p></td>
                                <td width="100" align="right"><p><? echo $amount; $tot_rec_amount+=$amount;//$rows['rec_amount']; ?></p></td>
                               <td width="100"><p><? echo $rows['rec_remarks']; ?>&nbsp;</p></td>
                               <td></td>
                            </tr>

                                <? $r++;} ?>
                        <?
                            $i++;
    						}

    						?>
                                <tr bgcolor="#CCCCCC">
                                    <td align="right" colspan="9">LC Total</td>
                                    <td>&nbsp;</td>
                                    <td align="right"><?php echo $tot_pi_qty;$gtot_pi_qty+=$tot_pi_qty;?></td>
                                    <td></td>
                                    <td align="right"><?php echo number_format($tot_pi_amount,2,'.','');$gtot_pi_amount+=$tot_pi_amount;?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="right"><?php echo $tot_rec_qty;$gtot_rec_qty+=$tot_rec_qty;?></td>
                                    <td>&nbsp;</td>
                                    <td align="right"><?php echo number_format($tot_rec_amount,2,'.','');$gtot_rec_amount+=$tot_rec_amount?></td>
                                    <td>&nbsp;</td>
                                    <td align="right"><?php echo number_format($tot_pi_amount-$tot_rec_amount,2,'.','');$gtot_blance+=$tot_pi_amount-$tot_rec_amount;?></td>
                                </tr>

    						<?

    						}
    						}

                        }
    					//sc.................................

                        foreach($sc_file_data_arr as $file_no_key=>$pi_data_arr)
                        {

    						foreach($pi_data_arr as $lc_key=>$lc_data_arr){
    						foreach($lc_data_arr as $pi_key=>$composition_data_arr){
    						$tot_pi_qty=0;$tot_pi_amount=0;$tot_rec_qty=0;$tot_rec_amount=0;
    						foreach($composition_data_arr as $row){
    						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
    						$rs=count($rec_data_arr[$pi_key][$row['yarn_composition']]);
    						//$supplier_id=$rec_data_arr[$pi_key][$row['yarn_composition']][0]['supplier_id'];

    						if($rs){$r_sapan=' rowspan="'.$rs.'"';}else{$r_sapan='';}
    					?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                <td width="35" <? echo $r_sapan;?>><? echo $i; ?></td>
                                <td width="80" <? echo $r_sapan;?>><p><? echo $file_no_key; ?></p></td>
                                <td width="100" <? echo $r_sapan;?>><? echo $buyer_arr[$row['buyer_name']]; ?></td>
                                <td width="100" <? echo $r_sapan;?> align="center"><p>&nbsp;<? echo $row['btb_lc']; ?></p></td>
                                <td width="100" <? echo $r_sapan;?>><p><? echo $row[pi_number]; ?></p></td>
                                <td width="100" <? echo $r_sapan;?>><p><? echo $supplier_arr[$row[supplier_id]]; ?></p></td>
                                <td width="100" <? echo $r_sapan;?>><p>&nbsp;<? echo $color_arr[$row['color_id']]; ?></p></td>
                                <td width="60" <? echo $r_sapan;?> ><? echo $count_arr[$row['count_name']]; ?>&nbsp;</td>
                                <td width="140" <? echo $r_sapan;?>><? echo $row['yarn_composition']; ?>&nbsp;</td>
                                <td width="100" <? echo $r_sapan;?>><? echo $yarn_type[$row['yarn_type']]; ?>&nbsp;</td>
                                <td width="100" <? echo $r_sapan;?>  align="right"><?
    							echo $sc_qtyArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']];
    							$tot_pi_qty+=$sc_qtyArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']];

    							//echo $row['pi_qty']; $tot_pi_qty+=$row['pi_qty'];?>&nbsp;</td>
                                <td width="100" align="right" <? echo $r_sapan;?>><?
    							echo number_format($sc_amountArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']]/$sc_qtyArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']],2)
    							//echo $row['net_pi_rate']; ?>&nbsp;</td>
                                <td width="100" align="right" <? echo $r_sapan;?>><?
    							echo $sc_amountArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']];
    							$tot_pi_amount+=$sc_amountArr[$file_no_key][$lc_key][$pi_key][$row['yarn_composition']];

    							//echo $row['net_pi_amount'];$tot_pi_amount+=$row['net_pi_amount']; ?>&nbsp;</td>
                                <td width="100" <? echo $r_sapan;?>><p><? echo $row['remarks']; ?>&nbsp;</p></td>
                            <? if($rs==0){?>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                                <td width="100" <? echo $r_sapan;?>></td>
                               <td width="100" <? echo $r_sapan;?>></td>
                               <td></td>
                            </tr>

    							<?
    								}
    							$r=1;
    							foreach($rec_data_arr[$pi_key][$row['yarn_composition']] as $rows){
    								if($r!=1){echo'<tr>';}
                                                                    $amount = $rows['rec_amount'] - $receive_return_arr[$rows['mrr_number']][$rows['lot']]['amount'];
                                                                    $rcvQnty = $rows['rec_qty'] - $receive_return_arr[$rows['mrr_number']][$rows['lot']]['qnty'];
    								?>

                                <td width="100" align="center"><p><? echo $rows['receive_date']; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $rows['mrr_number']; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $rows['lot']; ?>&nbsp;</p></td>
                                <td width="100" align="right"><p><a href="##" onClick="open_mypage_rcvRtn('<? echo $rows['mrr_number'];?>','<? echo $receive_return_arr[$rows['mrr_number']][$rows['lot']]["rtn_no"];?>','<? echo $rows['lot'];?>');"><? echo $rcvQnty;$tot_rec_qty+=$rcvQnty;// $rows['rec_qty']; $tot_rec_qty+=$rows['rec_qty']; ?></a></p></td>
                                <td width="100" align="right"><p><? echo $rows['rec_rate']; ?>&nbsp;</p></td>
                                <td width="100" align="right"><p><? echo $amount; $tot_rec_amount+=$amount;//$rows['rec_amount']; ?></p></td>
                               <td width="100"><p><? echo $rows['rec_remarks']; ?>&nbsp;</p></td>
                               <td></td>
                            </tr>

                                <? $r++;} ?>
                        <?
                            $i++;
    						}

    						?>
                                <tr bgcolor="#CCCCCC">
                                    <td align="right" colspan="9">LC Total</td>
                                    <td>&nbsp;</td>
                                    <td align="right"><?php echo $tot_pi_qty;$gtot_pi_qty+=$tot_pi_qty;?></td>
                                    <td></td>
                                    <td align="right"><?php echo number_format($tot_pi_amount,2,'.','');$gtot_pi_amount+=$tot_pi_amount;?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="right"><?php echo $tot_rec_qty;$gtot_rec_qty+=$tot_rec_qty;?></td>
                                    <td>&nbsp;</td>
                                    <td align="right"><?php echo number_format($tot_rec_amount,2,'.','');$gtot_rec_amount+=$tot_rec_amount?></td>
                                    <td>&nbsp;</td>
                                    <td align="right"><?php echo number_format($tot_pi_amount-$tot_rec_amount,2,'.','');$gtot_blance+=$tot_pi_amount-$tot_rec_amount;?></td>
                                </tr>

    						<?

    						}
    						}

                        }



                    ?>
                </table>
                </div>
                <table width="2150" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <tfoot>
                        <th colspan="9" align="right">Grand LC Total</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right"> <?php echo $gtot_pi_qty;?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right"> <?php echo number_format($gtot_pi_amount,2,'.','');?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100" align="right"> <?php echo $gtot_rec_qty;?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right"> <?php echo number_format($gtot_rec_amount,2,'.','');?></th>
                        <th width="100">&nbsp;</th>
                        <th width="112" ><?php echo number_format($gtot_blance,2,'.','');?></th>
                    </tfoot>
                </table>

            	 <?
    				echo signature_table(3, str_replace("'","",$cbo_company_name), "1100px");
    			 ?>
            </div>
        <!--</fieldset>-->
    	<?
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
    	echo "$total_data####$filename";
    	exit();
    }
    if($action == 'receive_return_popup')
    {
    	echo load_html_head_contents("Export LC Form", "../../../../", 1, 1,'','1','');
    	extract($_REQUEST);

            //echo $mrr_no ."--".$return_no; die;

            $sqlRcv = "select a.recv_number, b.order_qnty, b.order_rate,a.receive_date
            from inv_receive_master a, inv_transaction b, product_details_master c
            where a.id = b.mst_id and b.prod_id = c.id
            and b.transaction_type = 1 and a.recv_number = '$mrr_no' and c.lot = '$lot'
            and b.status_active = 1 and b.is_deleted = 0";

            //echo $sqlRcv;die;
    	?>
    </head>
    <body>
        <div align="center" style="width:520px;">
            <form name="searchexportlcfrm" id="searchexportlcfrm">
                <fieldset style="width:500px; margin-left:3px">
                	<input type="hidden" id="txt_selected_id" >
                    <table cellpadding="0" cellspacing="0" width="100%" class="rpt_table"  border="1" rules="all">
                    	<thead>
                            <th width="50">Sl</th>
                            <th width="100">Receive No.</th>
                            <th width="100">Receive Date</th>
                            <th width="100">Receive Qnty</th>
                            <th>Receive Amount</th>
                        </thead>

                    </table>
                    <div style="width:500px; max-height:290px; overflow:auto;" >
                    	<table cellpadding="0" cellspacing="0" width="500" class="rpt_table" id="table_body" border="1" rules="all">
                            <tbody>
                            <?
                                $sql_result=sql_select($sqlRcv);$i=1;
                                foreach($sql_result as $row)
                                {
                                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')">
                                    <td width="50" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $row[csf("recv_number")]; ?></td>
                                    <td width="100"><? echo $row[csf("receive_date")]; ?></td>
                                    <td width="100"><? echo number_format($row[csf("order_qnty")],2); ?></td>
                                    <td><? echo number_format($row[csf("order_qnty")]*$row[csf("order_rate")],2); ?></td>
                                </tr>
                                <?
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                        <script>//setFilterGrid('table_body',-1);</script>
                    </div>
                </fieldset>
                <br />
                <? //echo $return_no;die;
                $rcvRtnNo = "";
                foreach( array_filter(explode(",",(chop($return_no,",")))) as $rtnNo)
                {
                    $rcvRtnNo .= "'".$rtnNo."',";
                }
                $rcvRtnNo = chop($rcvRtnNo,",");

                $sql_rcvRtn = "select b.issue_number, b.issue_date, a.cons_quantity, a.order_rate ,c.lot
                    from inv_transaction a, inv_issue_master b, product_details_master c
                    where a.mst_id = b.id and a.transaction_type = 3 and a.prod_id = c.id
                    and a.status_active = 1 and a.is_deleted = 0  and c.lot = '$lot' and b.issue_number in ($rcvRtnNo)
                    and b.status_active = 1 and b.is_deleted=0"; ?>
                <fieldset style="width:500px; margin-left:3px">
                	<input type="hidden" id="txt_selected_id" >
                    <table cellpadding="0" cellspacing="0" width="100%" class="rpt_table"  border="1" rules="all">
                    	<thead>
                            <th width="50">Sl</th>
                            <th width="100">Receive Return No.</th>
                            <th width="100">Receive Return Date</th>
                            <th width="100">Receive Return Qnty</th>
                            <th>Receive Return Amount</th>
                        </thead>
                    </table>
                    <div style="width:500px; max-height:290px; overflow:auto;" >
                    	<table cellpadding="0" cellspacing="0" width="500" class="rpt_table" id="table_body" border="1" rules="all">
                            <tbody>
                            <?
                                $sql_result=sql_select($sql_rcvRtn);$i=1;
                                foreach($sql_result as $row)
                                {
                                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" id="tr<? echo $i;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')">
                                    <td width="50" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $row[csf("issue_number")]; ?></td>
                                    <td width="100"><? echo $row[csf("issue_date")]; ?></td>
                                    <td width="100"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                                    <td><? echo number_format($row[csf("cons_quantity")]*$row[csf("order_rate")],2); ?></td>
                                </tr>
                                <?
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                        <script>//setFilterGrid('table_body',-1);</script>
                    </div>
                </fieldset>

            </form>
        </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?
	exit();
}
?>
