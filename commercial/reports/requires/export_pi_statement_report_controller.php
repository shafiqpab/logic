<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="pi_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($title, "../../../", 1, 1,'','','');
	//var_dump($_REQUEST);die;
	$category_ids=str_replace("'","",$category_ids);
	if($category_ids!='') $cat_cond =" and item_category_id in ($category_ids)"; else $cat_cond='';
	$result = sql_select("SELECT id as ID, pi_number as PI_NUMBER from com_export_pi_mst where exporter_id=$company $cat_cond and is_deleted=0 and status_active=1 order by id desc");
	?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function js_set_value( row_id )
		{
			//alert(row_id);
			var pi_id = $('#pi_id_' + row_id).val()
			var pi_number= $('#pi_number_' + row_id).val()
			$('#hidden_pi_id').val(pi_id);
			$('#hidden_pi_number').val(pi_number);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div style="text-align:center;">
        <fieldset style="width:420px;margin-left:5px">
            <input type="hidden" name="hidden_pi_id" id="hidden_pi_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_pi_number" id="hidden_pi_number" class="text_boxes" value="">
            <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" >
                <thead>
                    <th width="50" style="text-align:center;">SL</th>
                    <th width="200" style="text-align:center">PI Number</th>
                    <th style="text-align:center">System ID Number</th>
                </thead>
            </table>
            <div style="width:420px; overflow-y:scroll; max-height:320px;" id="pi_number_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" id="tbl_list_search" >
				<?
                $i=1;
                foreach($result as $row)
				{
					($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                        <td width="50" style="text-align:center"><? echo $i;?></td>
                        <td width="200" style="text-align:center"><p><? echo $row['PI_NUMBER'];?></p></td>
						<td style="text-align:center"><p><? echo $row['ID'];?>
                        <input type="hidden" name="pi_id_<?php echo $i ?>" id="pi_id_<?php echo $i ?>" value="<? echo $row['ID']; ?>"/>
                        <input type="hidden" name="pi_number_<?php echo $i ?>" id="pi_number_<?php echo $i ?>" value="<? echo $row['PI_NUMBER']; ?>"/></p>
                        </td>
					</tr>
					<?
					$i++;
                }
                ?>
            </table>
            </div>
            </form>
        </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="generate_report")
{
	extract($_REQUEST);
	//var_dump($_REQUEST);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$pi_no=str_replace("'","", $pi_no);
	$sys_id=str_replace("'","", $sys_id);
	$cbo_company_name=str_replace("'","", $cbo_company_name);
	$cbo_item_category_id=str_replace("'","", $cbo_item_category_id);
	$rpt_type=str_replace("'","", $rpt_type);

	$search_cond.='';	
	if($cbo_company_name){ $search_cond.= " and a.exporter_id = $cbo_company_name"; }
	if($cbo_item_category_id !=""){ $search_cond.= " and a.item_category_id in ($cbo_item_category_id)"; }

	if($pi_no != "" || $sys_id != "")
	{
		if($sys_id != ""){
			$search_cond.= " and a.id=$sys_id";
		}
		else
		{
			$search_cond.= " and a.pi_number='$pi_no'";
		}
	}

	if($txt_date_from!='' && $txt_date_to!='')
	{
		if($db_type==0)
		{
			$search_cond.=" and a.pi_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$search_cond.=" and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}
	}


	if ($rpt_type==1) 
	{
		$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.item_category_id as ITEM_CATEGORY_ID, a.pi_date as PI_DATE, a.exporter_id as EXPORTER_ID, b.work_order_no as WORK_ORDER_NO, b.construction as CONSTRUCTION, b.composition as COMPOSITION, b.color_id as COLOR_ID,
		b.item_desc as ITEM_DESC, b.gmts_item_id as ITEM_GROUP, b.item_size as ITEM_SIZE, b.uom as UOM, b.quantity as QUANTITY, b.rate as RATE, b.amount as AMOUNT 
		from com_export_pi_mst a, com_export_pi_dtls b
		where a.id=b.pi_id $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc,b.work_order_no";
		//   echo $sql ; die;
		$sql_result = sql_select($sql);
		foreach($sql_result as $row)
		{
			$piDataArr[$row["PI_NUMBER"].'_'.$row["UOM"]][]=$row;

		}
		$com_name = return_library_array( "SELECT id, company_name from lib_company where status_active=1","id","company_name");
		// $yarn_count_name_array = return_library_array( "SELECT id, yarn_count from lib_yarn_count where status_active=1","id","yarn_count");
		// $item_group_array = return_library_array( "SELECT id, item_name from lib_item_group where status_active=1","id","item_name");
		// $item_product_array = return_library_array( "SELECT id, product_name_details from product_details_master where status_active=1","id","product_name_details");
		$item_color_array = return_library_array( "SELECT id, color_name from lib_color where status_active=1","id","color_name");	
		$image_arr = return_library_array( "SELECT master_tble_id, image_location from common_photo_library where form_name='export_pro_forma_invoice' and is_deleted=0","master_tble_id","image_location");
		$tbl_width=1380;
	    ob_start();
		?>

		<div style="height:auto; width:<?=$tbl_width+18;?>px; margin:15px auto; padding:0;">
	        <fieldset>
	        <table width="<?=$tbl_width;?>" align="center">
	            <tr>
	                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
	            </tr>
	            <tr>
	                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <?echo $com_name[$cbo_company_name]; ?></strong></td>
	            </tr>
	        </table>
	        <table width="<?=$tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
	            <thead>
	                <tr>
	                    <th width="40">SL</th>
	                    <th width="120">WO</th>
	                    <th width="80">PI</th>
	                    <th width="100">System ID</th>
	                    <th width="80">PI Date</th>
	                    <th width="100">Item Category</th>
	                    <th width="150">Item Group</th>
	                    <th width="180">Item Description</th>
	                    <th width="80">Gmts Color</th>
	                    <th width="50">Item Size</th>
	                    <th width="50">UOM</th>
	                    <th width="80">Quantity</th>
	                    <th width="80">Rate</th>
	                    <th width="100">Amount</th>
	                    <th>Image/File</th>
	                </tr>
	            </thead>
				<tbody>
					<?
					$i=1;
					foreach($piDataArr as $result)
					{
						$sub_total_qty=0;
						$sub_total_amount=0;
						foreach($result as $row)
						{
							($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i;?></td>
								<td width="120" align="center"><? echo $row["WORK_ORDER_NO"];?></td>
								<td width="80" align="center"><? echo $row["PI_NUMBER"];?></td>
								<td width="100" align="center"><? echo $row["PI_ID"];?></td>
								<td width="80" align="center"><? echo change_date_format($row["PI_DATE"]);?></td>
								<td width="100"><? echo $export_item_category[$row["ITEM_CATEGORY_ID"]];?></td>
								<td width="150"><? echo $garments_item[$row["ITEM_GROUP"]];?></td>
								<?
								$description ='';
								if($row["ITEM_CATEGORY_ID"]==45){
									$description = $row["ITEM_DESC"];
								}elseif($row["ITEM_CATEGORY_ID"]==1 || $row["ITEM_CATEGORY_ID"]==10 || $row["ITEM_CATEGORY_ID"]==20 || $row["ITEM_CATEGORY_ID"]==22){
									$description = $row["CONSTRUCTION"].', '.$row["COMPOSITION"];
								}
									?>
								<td width="180"><? echo $description;?></td>
								<td width="80"><? echo  $item_color_array[$row["COLOR_ID"]];?></td>
								<td width="50" align="center"><? echo $row["ITEM_SIZE"];?></td>
								<td width="50" align="center"><? echo $unit_of_measurement[$row["UOM"]];?></td>
								<td width="80" align="right"><? echo number_format($row["QUANTITY"],2);?></td>
								<td width="80" align="right"><? echo number_format($row["RATE"],2);?></td>
								<td width="100" align="right"><? echo number_format($row["AMOUNT"],2);?></td>
								<td><a href="javascript:openmypage_image(<?= $row["PI_ID"];?>)"><?= ($image_arr[$row["PI_ID"]])?"View File":"No File";?></a></td>
							</tr>
							<?
							$total_qty += $row["QUANTITY"];
							$total_amount += $row["AMOUNT"];
							$sub_total_qty+=$row["QUANTITY"];
							$sub_total_amount+=$row["AMOUNT"];						   
							$i++;
						}
						?>
							<tr bgcolor='#999999'>
								<td colspan='11' align='right'><strong>Sub Total :</strong></td>
								<td align='right'><strong><?echo number_format($sub_total_qty,2);?></strong></td>
								<td></td>
								<td align='right'><strong><?echo number_format($sub_total_amount,2);?></strong></td>
								<td></td>
							</tr>
						<?
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="11" ><strong>Grand Total:</strong> </th>
						<th ><strong><? echo number_format($total_qty,2); ?></strong></th>
						<th ></th>
						<th ><strong><? echo number_format($total_amount,2); ?></strong></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
	        </fieldset>
		</div>
		<?
	}

	if ($rpt_type==2) 
	{
		$sql="SELECT a.id as PI_ID, a.pi_number as PI_NUMBER, a.item_category_id as ITEM_CATEGORY_ID, a.pi_date as PI_DATE, a.exporter_id as EXPORTER_ID, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID,a.inserted_by as INSERTED_BY, a.insert_date as INSERT_DATE, sum(b.amount) as AMOUNT
		from com_export_pi_mst a, com_export_pi_dtls b
		where a.id=b.pi_id $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id,a.pi_number,a.item_category_id,a.pi_date,a.exporter_id,a.within_group,a.buyer_id,a.inserted_by,a.insert_date
		order by a.id desc";
		// echo $sql; die;
		$sql_result = sql_select($sql);
		foreach($sql_result as $row){
			$piDataArr[$row[csf("item_category_id")]][]=$row;
		}
		$com_name = return_library_array( "SELECT id, company_name from lib_company","id","company_name");
		$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active=1", "id", "buyer_name"  );
		// $supplier_arr=return_library_array( "SELECT id, supplier_name from lib_supplier where status_active=1", "id", "supplier_name"  );
		$image_arr = return_library_array( "SELECT master_tble_id, image_location from common_photo_library where form_name='export_pro_forma_invoice' and is_deleted=0","master_tble_id","image_location");
		$user_arr = return_library_array( "SELECT id, user_name from user_passwd","id","user_name");
		$tbl_width=1100;
	    ob_start();
		?>

		<div style="height:auto; width:<?=$tbl_width+18;?>px; margin:15px auto; padding:0;">
	        <fieldset>
	        <table width="<?=$tbl_width;?>" align="center">
	            <tr>
	                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
	            </tr>
	            <tr>
	                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <? echo $com_name[$cbo_company_name]; ?></strong></td>
	            </tr>
	        </table>
	        <table width="<?=$tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
	            <thead>
	                <tr> 
	                    <th width="40">SL</th>
	                    <th width="120">Item Category</th>
	                    <th width="50">WO</th>
	                    <th width="140">PI</th>
	                    <th width="80">System ID</th>
	                    <th width="80">PI Date</th>
	                    <th width="150">Buyer Name</th>
	                    <th width="150">Insert By</th>
	                    <th width="80">Insert Date</th>
	                    <th width="100">Amount</th>
	                    <th>Image/File</th>
	                </tr>
	            </thead>
				<tbody>
					<?
					$i=1;
					foreach($piDataArr as $result)
					{
						$sub_total_amount=0;
						foreach($result as $row)
						{
							($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i;?></td>
								<td width="120"><? echo $export_item_category[$row["ITEM_CATEGORY_ID"]];?></td>
								<td width="50" align="center"><p><a href="##" onClick="openmypage_wo('<? echo $row["PI_ID"]; ?>')" > View </a></p></td>
								<td width="140" align="center"><? echo $row["PI_NUMBER"];?></td>
								<td width="80" align="center"><? echo $row["PI_ID"];?></td>
								<td width="80" align="center"><? echo change_date_format($row["PI_DATE"]);?></td>
								<td width="150">
									<? 
										if($row["WITHIN_GROUP"]==1){echo $com_name[$row["BUYER_ID"]];}
										else{echo $buyer_arr[$row["BUYER_ID"]];}										
									?>
								</td>
								<td width="150"><? echo  $user_arr[$row["INSERTED_BY"]];?></td>
								<td width="80" align="center"><? echo change_date_format($row["INSERT_DATE"]);?></td>
								<td width="100" align="right"><? echo number_format($row["AMOUNT"],2);?></td>
								<td><a href="javascript:openmypage_image(<?= $row["PI_ID"];?>)"><?= ($image_arr[$row["PI_ID"]])?"View File":"No File";?></a></td>
							</tr>
							<?
							$total_amount += $row["AMOUNT"];
							$sub_total_amount+=$row["AMOUNT"];
							$i++;
						}
						?>
							<tr bgcolor='#999999'>
								<td colspan="9" align="right"><strong>Sub Total :</strong> </td>
								<td align="right"><strong><? echo number_format($sub_total_amount,2); ?></strong></td>
								<td></td>
							</tr>
						<?
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="9" align="right"><strong>Grand Total:</strong> </td>
						<td align="right"><strong><? echo number_format($total_amount,2); ?></strong></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
	        </fieldset>
		</div>
		<?
	}
	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
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

if($action=="wo_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$pi_id=str_replace("'","",$pi_id);
	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}	
	
	</script>	
    <div id="report_container" align="center" style="width:200px">
	<fieldset style="width:200px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="200" cellpadding="0" cellspacing="0">
             	<thead>
                    <th width="50">Sl.</th>
                    <th>WO No.</th>
                    
                </thead>
                <tbody>
					<?
						$sql="SELECT work_order_no as WORK_ORDER_NO from com_export_pi_dtls  where pi_id=$pi_id and status_active=1 and is_deleted=0 group by work_order_no";
						// echo $sql;
						$result=sql_select($sql); 
						$i=1;
						foreach($result as $row)  
						{
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><p><? echo $row['WORK_ORDER_NO']; ?></p></td>
								
							</tr>
							<?
							$i++;
						}
					?>
                </tbody>   
            </table>
			<br />
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        </fieldset>
    </div>
	<?
    exit();

}

if($action=="image_file")
{
	extract($_REQUEST);
	$image_arr = return_library_array( "select id, image_location from common_photo_library where form_name='export_pro_forma_invoice' and master_tble_id='$pi_id' and is_deleted=0","id","image_location");
	
	if(count($image_arr)==''){ echo "<h1>File Not Found</h1>";exit();}
	foreach($image_arr as $file){
	?>
    	<a href="../../../<?= $file;?>" title="click to download">
        	<img src="../../../file_upload/blank_file.png" width="89" height="97">
        </a>
    <?
	}
	exit();
}
?>
