<?
/**
 * Created by Mohammad Shafiqur Rahman.
 * User: shafiq-sumon
 * Date: 6/7/2018
 * Time: 10:17 AM
 */
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
	//var_dump($_REQUEST);die;
	$result = sql_select("select id,pi_number from com_pi_master_details where importer_id=$company and is_deleted=0 and status_active=1");
	echo load_html_head_contents("PI Number", "../../../", 1, 1,'','','');

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
                        <td width="200" style="text-align:center"><p><? echo $row[csf('pi_number')];?></p></td>
						<td style="text-align:center"><p><? echo $row[csf('id')];?>
                        <input type="hidden" name="pi_id_<?php echo $i ?>" id="pi_id_<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                        <input type="hidden" name="pi_number_<?php echo $i ?>" id="pi_number_<?php echo $i ?>" value="<? echo $row[csf('pi_number')]; ?>"/></p>
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

if($action=="openmypage_sys_id")
{

	extract($_REQUEST);
	//var_dump($_REQUEST);die;
	$result = sql_select("select id,pi_number from com_pi_master_details where importer_id=$company and is_deleted=0 and status_active=1");
	echo load_html_head_contents("PI Number", "../../../", 1, 1,'','','');

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
                    <th width="50">SL</th>
                    <th width="200">System ID</th>
                    <th>PI Number</th>
                </thead>
            </table>
            <div style="width:420px; overflow-y:scroll; max-height:320px;" id="sys_id_no_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" id="tbl_list_search" >
				<?
                $i=1;
                foreach($result as $row)
				{
					($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                        <td width="50" style="text-align:center"><? echo $i;?></td>
                        <td width="200" style="text-align:center"><p><? echo $row[csf('id')];?>
                        <input type="hidden" name="pi_id_<?php echo $i ?>" id="pi_id_<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                        <input type="hidden" name="pi_number_<?php echo $i ?>" id="pi_number_<?php echo $i ?>" value="<? echo $row[csf('pi_number')]; ?>"/></p>
                        </td>
                        <td style="text-align:center"><p><? echo $row[csf('pi_number')];?></p></td>
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
	$item_category_arr;
	//var_dump($_REQUEST);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$pi_no_id=str_replace("'","", $pi_no_id);
	$pi_no=str_replace("'","", $pi_no);
	$sys_id=str_replace("'","", $sys_id);
	$sys_id=str_replace("'","", $sys_id);
	$cbo_company_name=str_replace("'","", $cbo_company_name);
	//print$txt_date_from;die; importer_id
	if($pi_no_id!=""){
		// echo "1** $pi_no_id";//die;
		if($db_type==2)
		{
			if($txt_date_from=='' || $txt_date_to=='')
			{
				$date_range_cond='';
			}
			else
			{
				$date_range_cond =" and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}
		}
		if ($db_type==0){
			if($txt_date_from=='' || $txt_date_to=='')
		  {
				$date_range_cond='';
			}
			else
			{
				$date_range_cond =" and a.pi_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
		}

		$pi_sql_cond = " and b.pi_id=$pi_no_id";
		//echo $pi_sql_cond."sumonnn"; die;
	}
	else if($pi_no != "" || $sys_id != "")
	{
		if($db_type==2)
		{
			if($txt_date_from=='' || $txt_date_to=='')
			{
				$date_range_cond='';
			}
			else
			{
				$date_range_cond =" and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}
		}
		if ($db_type==0){
			if($txt_date_from=='' || $txt_date_to=='')
		  {
				$date_range_cond='';
			}
			else
			{
				$date_range_cond =" and a.pi_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
		}
		if($pi_no != ""){
			$pi_sql_cond = " and a.pi_number='$pi_no'";
		}else if($sys_id != ""){
			$pi_sql_cond = " and b.pi_id=$sys_id";
		}

		//echo $pi_no . "__". $sys_id;
		//echo $pi_sql_cond."sumon2"; die;
	}
	else
	{
		if($db_type==2){
			if($txt_date_from=='' || $txt_date_to=='')
			{
				$date_range_cond='';
			}
			else
			{
				$date_range_cond =" and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}
		}
		if ($db_type==0){
			if($txt_date_from=='' || $txt_date_to=='')
			{
				$date_range_cond='';
			}
			else
			{
				$date_range_cond =" and a.pi_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
		}
		$pi_sql_cond='';
		//echo $pi_sql_cond."sumon3"; die;
	}

	if($cbo_company_name !=""){
		$importer_cond = " and a.importer_id = $cbo_company_name";
	}

	$sql="select b.pi_id,a.id,a.item_category_id,a.pi_number,a.pi_date,a.importer_id, b.work_order_no,b.work_order_id,b.item_group,b.item_prod_id,
	b.item_color,b.size_id,b.item_size,b.count_name,b.yarn_composition_item1,b.yarn_composition_percentage1,
	b.yarn_composition_item2,b.fabric_composition,b.fabric_construction,b.yarn_type,b.dia_width,b.weight,b.gsm,
	b.item_description,b.item_size,b.uom,b.quantity,b.rate,b.amount
	from com_pi_master_details  a,  com_pi_item_details b
	where a.id=b.pi_id $importer_cond $pi_sql_cond $date_range_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	and b.is_deleted=0";
	  //echo $sql ; // die;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$piDataArr[$row[csf("pi_number")].'_'.$row[csf("uom")]][]=$row;

	}




	$yarn_count_name = sql_select("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0");
	foreach ($yarn_count_name as $row){
		$yarn_count_name_array[$row[csf("id")]] =  $row[csf("yarn_count")];
	}
	$item_group=sql_select("select id,item_name from lib_item_group where status_active=1 and is_deleted=0");
	foreach ($item_group as $row){
		$item_group_array[$row[csf("id")]] =  $row[csf("item_name")];
	}
	$item_product=sql_select("select id,product_name_details from product_details_master where status_active=1 and is_deleted=0");
	foreach ($item_product as $row){
		$item_product_array[$row[csf("id")]] =  $row[csf("product_name_details")];
	}
	$item_color_result = sql_select("select id, color_name from lib_color where status_active=1 and is_deleted=0");
	foreach ($item_color_result as $row){
		$item_color_array[$row[csf("id")]] =  $row[csf("color_name")];
	}

	//var_dump($item_color_array);
	
	
	$image_arr = return_library_array( "select MASTER_TBLE_ID, IMAGE_LOCATION from common_photo_library where FORM_NAME='proforma_invoice' and IS_DELETED=0","MASTER_TBLE_ID","IMAGE_LOCATION");
	
	
	
    ob_start();
	?>

	<div style="height:auto; width:1400px; margin:15px auto; padding:0;">
        <fieldset>
        <table width="1380" align="center">
            <tr>
                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
            </tr>
            <tr>
                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <? $com_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name"); echo $com_name; ?></strong></td>
            </tr>
        </table>
        <table width="1380" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
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
        </table>
        <div style="width:1400px; max-height:350px; overflow-x:hidden; overflow-y: scroll;" id="scroll_body">
            <table width="1380" align="left" height="auto" class="rpt_table" rules="all" id="table_body_id" cellspacing="0" cellpadding="2" border="1" style="word-break: break-all;">
                <tbody>
            <?
            $i=1;
            foreach($piDataArr as $result){
			$sub_total_qty=0;
			$sub_total_amount=0;
			foreach($result as $row)
            {
                ($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
                //$item_cat_id = $row[csf("item_category_id")]*1;
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="120" align="center"><? echo $row[csf("work_order_no")];?></td>
                    <td width="80" align="center"><? echo $row[csf("pi_number")];?></td>
                    <td width="100" align="center"><? echo $row[csf("id")];?></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf("pi_date")]);?></td>
                    <td width="100"><? echo $item_category[$row[csf("item_category_id")]];?></td>
                    <td width="150"><? echo $item_group_array[$row[csf("item_group")]];?></td>
                    <?
                    if($row[csf("item_category_id")]==1){
						$description= $yarn_count_name_array[$row[csf("count_name")]]." ".$composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."% ";

                    }elseif ($row[csf("item_category_id")]==2 || $row[csf("item_category_id")]==3 || $row[csf("item_category_id")]==13 || $row[csf("item_category_id")]==14){
                        $description= $row[csf("color_id")]." ".$row[csf("fabric_composition")]." ".$row[csf("fabric_construction")];
                    }elseif (!in_array($item_category[$row[csf("item_category_id")]], $general_item_category)){
                        //echo $item_category[$item_cat_id];die;
                        $description= $item_product_array[$row[csf("item_prod_id")]]; //die;
                    }else{
                        $description = $row[csf("item_description")];
                    }
                        ?>
                    <td width="180"><? echo $description;?></td>
                    <td width="80"><? echo  $item_color_array[$row[csf("item_color")]];?></td>
                    <td width="50" align="center"><? echo $row[csf("item_size")];?></td>
                    <td width="50" align="center"><? echo $unit_of_measurement[$row[csf("uom")]];?></td>
                    <td width="80" align="right"><? echo $row[csf("quantity")];?></td>
                    <td width="80" align="right"><? echo $row[csf("rate")];?></td>
                    <td width="100" align="right"><? echo $row[csf("amount")];?></td>
                    <td><a href="javascript:openmypage_image(<?= $row[csf("pi_id")];?>)"><?= ($image_arr[$row[csf("pi_id")]])?"View File":"No File";?></a></td>
                </tr>
                <?
                $total_qty += $row[csf("quantity")];
                $total_amount += $row[csf("amount")];
				$sub_total_qty+=$row[csf("quantity")];
				$sub_total_amount+=$row[csf("amount")];
			   
			   
			    $i++;
            }
			
				
				echo "<tr bgcolor='#999999'>
					<td colspan='11' align='right'><strong>Sub Total :</strong></td>
					<td align='right'><strong>".number_format($sub_total_qty,2)."</strong></td>
					<td></td>
					<td align='right'><strong>".number_format($sub_total_amount,2)."</strong></td>
					<td></td>
				</tr>";
			
			}
            ?>
                </tbody>
                <tfoot>
				<tr>
					<td colspan="11" align="right"><strong>Grand Total:</strong> </td>
					<td align="right"><strong><? echo number_format($total_qty,2); ?></strong></td>
					<td align="right"></td>
					<td align="right"><strong><? echo number_format($total_amount,2); ?></strong></td>
                    <td></td>
				</tr>
			</tfoot>
            </table>
        </div>
        </fieldset>
	</div>
	<?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("$user_id*.xls") as $filename) {
        //if( @filemtime($filename) < (time()-$seconds_old) )
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


if($action=="image_file")
{
	extract($_REQUEST);
	$image_arr = return_library_array( "select ID, IMAGE_LOCATION from common_photo_library where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID='$pi_id' and IS_DELETED=0","ID","IMAGE_LOCATION");
	
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
