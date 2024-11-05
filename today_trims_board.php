<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Knit Finish Fabrics Display Board </title>
<script src="js/jquery_latest.js" ></script>
</head>
<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$po_buyer=return_library_array( "select a.id, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no", "id", "buyer_name"  );
$prod_group=return_library_array( "select a.id, b.item_name from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24", "id", "item_name"  );
$rack_shalf_bin_library=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name"  );

$sql="select a.id as prod_id, a.Item_Group_Id, a.unit_of_measure, b.rack, b.self, b.bin_box, c.po_breakdown_id, sum(case when c.trans_type in(1,4,5) then c.quantity else 0 end) as rcv_qnty, sum(case when c.trans_type in(2,3,6) then c.quantity else 0 end) as issue_qnty
from product_details_master a, inv_transaction b, order_wise_pro_details c 
where a.id=b.prod_id and b.id=c.trans_id and b.prod_id=c.prod_id and a.item_category_id=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
group by a.id, a.Item_Group_Id, a.unit_of_measure, b.rack, b.self, b.bin_box, c.po_breakdown_id
order by a.id, a.Item_Group_Id";
$result=sql_select($sql);
$details_data=array();
foreach($result as $row)
{
	//&& $row[csf("bin_box")] > 0
	if($row[csf("rack")] > 0 && $row[csf("self")] > 0 )
	{
		$details_data[$po_buyer[$row[csf("po_breakdown_id")]]][$row[csf("Item_Group_Id")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["buyer_id"]=$po_buyer[$row[csf("po_breakdown_id")]];
		$details_data[$po_buyer[$row[csf("po_breakdown_id")]]][$row[csf("Item_Group_Id")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["item_group"]=$prod_group[$row[csf("prod_id")]];
		$details_data[$po_buyer[$row[csf("po_breakdown_id")]]][$row[csf("Item_Group_Id")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
		$details_data[$po_buyer[$row[csf("po_breakdown_id")]]][$row[csf("Item_Group_Id")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["rack"]=$row[csf("rack")];
		$details_data[$po_buyer[$row[csf("po_breakdown_id")]]][$row[csf("Item_Group_Id")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["self"]=$row[csf("self")];
		$details_data[$po_buyer[$row[csf("po_breakdown_id")]]][$row[csf("Item_Group_Id")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["bin_box"]=$row[csf("bin_box")];
		$details_data[$po_buyer[$row[csf("po_breakdown_id")]]][$row[csf("Item_Group_Id")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["rcv_qnty"]+=$row[csf("rcv_qnty")];
		$details_data[$po_buyer[$row[csf("po_breakdown_id")]]][$row[csf("Item_Group_Id")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["issue_qnty"]+=$row[csf("issue_qnty")];
	}
	
}

//echo "<pre>";print_r($details_data);die;
?>
<body>
<fieldset style="width:98%;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
    	<tr><td style="font-size:20px; font-weight:bold; text-align:center;">Blue Planet Fashion Ltd</td></tr>
        <tr><td style="font-size:18px; font-weight:bold; text-align:center;">Location Board</td></tr>
        <tr><td>&nbsp;</td></tr>
    </table>
    <table border="1" class="rpt_table" rules="all" width="98%" cellpadding="0" cellspacing="0">
    	<tr bgcolor="#FFFFCC">
        	<td colspan="10" style="font-size:16px; font-weight:bold;">Item Category : Trims & Accessories</td>
        </tr>
        <thead>
            <tr>
            	<th width="13%">Buyer</th>
                <th width="15%">Item</th>
                <th width="8%">Rack No</th>
                <th width="8%">Shelf No</th>
                <th width="8%">Bin No</th>
                <th width="8%">UOM</th>
                <th width="10%">Receive Qnty</th>
                <th width="10%">Issue Qnty</th>
                <th width="10%">In Hand</th>
                <th>Remarks</th>
            </tr>
        </thead>
    </table>
    <div style="overflow-y:scroll; max-height:450px;font-size:12px; overflow-x:hidden; width:100%;" id="scroll_body">
    <table border="1" class="rpt_table" rules="all" width="99%" cellpadding="0" cellspacing="0">
        <tbody>
        <?
		$i=1;
        foreach($details_data as $buyer_id=>$buyer_val)
        {
			foreach($buyer_val as $item_group_id=>$group_val)
			{
				foreach($group_val as $rack_id=>$rack_val)
				{
					foreach($rack_val as $self_id=>$self_val)
					{
						foreach($self_val as $bin_id=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$in_hand=$val['rcv_qnty']-$val['issue_qnty'];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="13%"><p><? echo $buyer_library[$buyer_id]; ?></p></td>
								<td width="15%"><p><? echo $val['item_group'];  ?></p></td>
								<td width="8%" align="center" title="<? echo $val['rack']; ?>"><p><? echo $rack_shalf_bin_library[$val['rack']]; ?></p></td>
								<td width="8%" align="center" title="<? echo $val['self']; ?>"><p><? echo $rack_shalf_bin_library[$val['self']]; ?></p></td>
								<td width="8%" align="center" title="<? echo $val['bin_box']; ?>"><p><? echo $rack_shalf_bin_library[$val['bin_box']]; ?></p></td>
                                <td width="8%" align="center"><p><? echo $unit_of_measurement[$val['unit_of_measure']];  ?></p></td>
								<td width="10%" align="right"><? echo number_format($val['rcv_qnty'],2); ?></td>
								<td width="10%" align="right"><? echo number_format($val['issue_qnty'],2); ?></td>
								<td width="10%" align="right"><? echo number_format($in_hand,2); ?></td>
								<td align="center"><? //echo $val['rack']."=".$val['self'] ."=".$val['bin_box']; ?>&nbsp;</td>
							</tr>
							<?
							$i++;
						}
					}
				}
			}
            
        }
		?>
        </tbody>
	</table>
    </div>
</fieldset>
</body>
</html> 
<script type="text/javascript">
var rowpos =$('#scroll_body table').height();
$("#scroll_body").animate({scrollTop:rowpos}, 440000, 'linear', function() { 
	//alert("Finished animating");
	window.location.reload(true);
	return;
});
</script>