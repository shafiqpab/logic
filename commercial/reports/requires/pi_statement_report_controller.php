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

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=205 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){
		if($id==108)$buttonHtml.='<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:50px" class="formbutton" />';	
		if($id==195)$buttonHtml.='<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:50px" class="formbutton" />';	
		if($id==455)$buttonHtml2.='<input type="button" name="search" id="search" value="PI Status" onClick="generate_report(3)" style="width:60px" class="formbutton" />';	
		if($id==456)$buttonHtml2.='<input type="button" name="search" id="search" value="PI Details" onClick="generate_report(4)" style="width:60px" class="formbutton" />';	
		if($id==242)$buttonHtml2.='<input type="button" name="search" id="search" value="Show 3" onClick="generate_report(5)" style="width:50px" class="formbutton" />';	
		if($id==359)$buttonHtml2.='<input type="button" name="search" id="search" value="Show 4" onClick="generate_report(6)" style="width:50px" class="formbutton" />';	
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
   echo "document.getElementById('button_data_panel2').innerHTML = '".$buttonHtml2."';\n";
    exit();
}

if($action=="pi_number_popup")
{

	extract($_REQUEST);
	//var_dump($_REQUEST);die;
	$category_ids=str_replace("'","",$category_ids);
	if($category_ids!='') $cat_cond =" and b.item_category_id in ($category_ids)"; else $cat_cond='';
	$result = sql_select("select a.id,a.pi_number from com_pi_master_details a, com_pi_item_details b where a.importer_id=$company $cat_cond and a.id=b.pi_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
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
	// var_dump($_REQUEST);
	$cbo_based_on = str_replace("'","", $cbo_based_on);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$pi_no_id=str_replace("'","", $pi_no_id);
	$pi_no=str_replace("'","", $pi_no);
	$sys_id=str_replace("'","", $sys_id);
	$sys_id=str_replace("'","", $sys_id);
	$cbo_company_name=str_replace("'","", $cbo_company_name);
	$cbo_approval_status=str_replace("'","", $cbo_approval_status);
	$cbo_item_category_id=str_replace("'","", $cbo_item_category_id);
	$cbo_supplier_id=str_replace("'","", $cbo_supplier);
	//echo $cbo_supplier;die;
	
	$type=str_replace("'","", $type);
	$importer_cond=''; $category_cond=''; $date_range_cond='';  $pi_sql_cond='';$supplier_cond='' ;
	//print$txt_date_from;die; importer_id
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	if($pi_no_id!="")
	{
		// echo "1** $pi_no_id";//die;
		if($db_type==2)
		{
			if($txt_date_from=='' || $txt_date_to=='')
			{
				$date_range_cond='';
			}
			else
			{
				if($cbo_based_on==1)
				{
					$date_range_cond =" and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
				}
				else if($cbo_based_on==2)
				{
					$date_range_cond=" and a.insert_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'";
				}
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
				if($cbo_based_on==1)
				{
					$date_range_cond =" and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
				}
				else if($cbo_based_on==2)
				{
					$date_range_cond=" and a.insert_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'";
				}
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
				if($cbo_based_on==1)
				{
					$date_range_cond =" and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
				}
				else if($cbo_based_on==2)
				{
					$date_range_cond=" and a.insert_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'";
				}
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
	// echo "Hello-". $type; die;
	if ($type==1) //show
	{
		if($cbo_item_category_id !=""){
			$category_cond = " and a.item_category_id in ($cbo_item_category_id)";
		}
		 if($cbo_supplier_id !=""){
			$supplier_cond = " and a.supplier_id in ($cbo_supplier_id)";
		}
		$sql="SELECT b.pi_id,a.id,a.item_category_id,a.supplier_id,a.pi_number,a.pi_date, a.insert_date, a.importer_id, b.work_order_no,b.work_order_id,b.item_group,b.item_prod_id,
		b.item_color,b.size_id,b.item_size,b.count_name,b.yarn_composition_item1,b.yarn_composition_percentage1,
		b.yarn_composition_item2,b.fabric_composition,b.fabric_construction,b.yarn_type,b.dia_width,b.weight,b.gsm,
		b.item_description,b.item_size,b.uom,b.quantity,b.rate,b.amount
		from com_pi_master_details  a,  com_pi_item_details b
		where a.id=b.pi_id $importer_cond $pi_sql_cond $date_range_cond $category_cond $supplier_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0";
		 //echo $sql ;die; 
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

		<div style="height:auto; width:1500px; margin:15px auto; padding:0;">
	        <fieldset>
	        <table width="1480" align="center">
	            <tr>
	                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
	            </tr>
	            <tr>
	                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <? $com_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name"); echo $com_name; ?></strong></td>
	            </tr>
	        </table>
	        <table width="1480" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
	            <thead>
	                <tr>
	                    <th width="40">SL</th>
	                    <th width="120">WO</th>
	                    <th width="80">PI</th>
	                    <th width="100">System ID</th>
						<? if($cbo_based_on ==1) : ?>
	                    <th width="80">PI Date</th>
						<? elseif($cbo_based_on ==2) : ?>
	                    <th width="80">PI Insert Date</th>
						<? endif; ?>
	                    <th width="100">Item Category</th>
						<th width="100">Supplier Name</th>
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
	        <div style="width:1500px; max-height:350px; overflow-x:hidden; overflow-y: scroll;" id="scroll_body">
	            <table width="1480" align="left" height="auto" class="rpt_table" rules="all" id="table_body_id" cellspacing="0" cellpadding="2" border="1" style="word-break: break-all;">
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
			                //$item_cat_id = $row[csf("item_category_id")]*1;
			                ?>
			                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                    <td width="40" ><? echo $i;?></td>
			                    <td width="120"  style="word-break: break-all;" align="center"><? echo $row[csf("work_order_no")];?></td>
			                    <td width="80"  style="word-break: break-all;" align="center"><? echo $row[csf("pi_number")];?></td>
			                    <td width="100"  style="word-break: break-all;" align="center"><? echo $row[csf("id")];?></td>
								<? if($cbo_based_on ==1) : ?>
			                    	<td width="80"  style="word-break: break-all;" align="center"><? echo change_date_format($row[csf("pi_date")]);?></td>
								<? elseif($cbo_based_on ==2) : ?>
									<td width="80"  style="word-break: break-all;" align="center"><? echo change_date_format($row[csf("insert_date")]);?></td>
								<? endif; ?>
			                    <td width="100"  style="word-break: break-all;"><? echo $item_category[$row[csf("item_category_id")]];?></td>
								<td width="100" style="word-break: break-all;"><? echo $supplier_arr[$row[csf("supplier_id")]];?></td>
			                    <td width="150"  style="word-break: break-all;"><? echo $item_group_array[$row[csf("item_group")]];?></td>
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
			                    <td width="180" style="word-break: break-all;"><? echo $description;?></td>
			                    <td width="80" style="word-break: break-all;"><? echo  $item_color_array[$row[csf("item_color")]];?></td>
			                    <td width="50" style="word-break: break-all;" align="center"><? echo $row[csf("item_size")];?></td>
			                    <td width="50"  style="word-break: break-all;"align="center"><? echo $unit_of_measurement[$row[csf("uom")]];?></td>
			                    <td width="80" style="word-break: break-all;" align="right"><? echo $row[csf("quantity")];?></td>
			                    <td width="80"  style="word-break: break-all;" align="right"><? echo $row[csf("rate")];?></td>
			                    <td width="100"  style="word-break: break-all;" align="right"><? echo $row[csf("amount")];?></td>
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
							<td colspan='12' align='right'><strong>Sub Total :</strong></td>
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
	}
	if ($type==2) //show 2
	{
		if($cbo_item_category_id !=""){
			$category_cond = " and b.item_category_id in ($cbo_item_category_id)";
		}
		$approve_cond ='';
		if($cbo_approval_status !=0){
			if($cbo_approval_status ==1){
				$approve_cond = " and a.approved = 0";
			}
			else if($cbo_approval_status ==2){
				$approve_cond = " and a.approved = 3";
			}
			else if($cbo_approval_status ==3){
				$approve_cond = " and a.approved = 1";
			}
			else{
				$approve_cond ='';
			}
		}

		//$work_order_no_str=",listagg(cast(b.work_order_no as varchar(4000)),',') within group (order by b.work_order_no) as work_order_no";
		//$work_order_id_str=",listagg(b.work_order_id,',') within group (order by b.id) as work_order_id";


		$sql="SELECT b.pi_id,a.id,a.pi_number,a.pi_date,a.importer_id,a.approved,a.supplier_id,a.upcharge,a.discount,a.net_total_amount,b.item_category_id,b.inserted_by,a.insert_date, sum (b.amount) as amount $work_order_id_str $work_order_no_str
		from com_pi_master_details  a,  com_pi_item_details b
		where a.id=b.pi_id $importer_cond $pi_sql_cond $date_range_cond $category_cond $approve_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 group by b.pi_id,a.id,a.pi_number,a.pi_date,a.importer_id,a.approved,a.supplier_id,a.upcharge,a.discount,a.net_total_amount,b.item_category_id,b.inserted_by,a.insert_date";
		//echo $sql ; // die;
		$sql_result = sql_select($sql);
		foreach($sql_result as $row){
			//$piDataArr[$row[csf("item_category_id")].'_'.$row[csf("pi_number")]][]=$row;
			$piDataArr[$row[csf("item_category_id")]][]=$row;
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
		$user_arr = return_library_array( "select id, user_name from user_passwd","id","user_name");
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	    ob_start();
		?>

		<div style="height:auto; width:1400px; margin:15px auto; padding:0;">
	        <fieldset>
	        <table width="1600" align="center">
	            <tr>
	                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
	            </tr>
	            <tr>
	                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <? $com_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name"); echo $com_name; ?></strong></td>
	            </tr>
	        </table>
	        <table width="1600" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
	            <thead>
	                <tr> 
	                    <th width="40">SL</th>
	                    <th width="100">Item Category</th>
	                    <th width="120">WO</th>
	                    <th width="80">PI</th>
	                    <th width="100">System ID</th>
	                    <th width="80">PI Date</th>
	                    <th width="150">Buyer Name</th>
	                    <th width="80">Insert By</th>
	                    <th width="80">Insert Date</th>
	                    <th width="80">Amount</th>
	                    <th width="80">Upcharge</th>
	                    <th width="80">Discount</th>
	                    <th width="80">Net Amount</th>
	                    <th width="80">Is Approved</th>
	                    <th width="100">Approval Status</th>
	                    <th>Image/File</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:1620px; max-height:350px; overflow-x:hidden; overflow-y: scroll;" id="scroll_body">
	            <table width="1600" align="left" height="auto" class="rpt_table" rules="all" id="table_body_id" cellspacing="0" cellpadding="2" border="1" style="word-break: break-all;">
	                <tbody>
		            <?
		            $i=1;
		            foreach($piDataArr as $result)
		            {
						$sub_total_qty=0;
						$sub_total_amount=$sub_total_upcharge=$sub_total_discount=$sub_total_net_amount=0;
						foreach($result as $row)
			            {
			                ($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
			                if ($row[csf("approved")]==1) { 
			                	$approved='Yes';
			                }else if ($row[csf("approved")]==3) { 
			                	$approved='Partial';
			                }
			                else { 
			                	$approved='No';
			                }
			                //$item_cat_id = $row[csf("item_category_id")]*1;
			                ?>
			                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                    <td width="40"><? echo $i;?></td>
			                    <td width="100"><? echo $item_category[$row[csf("item_category_id")]];?></td>
			                    <td width="120" align="center"><p><a href="##" onClick="openmypage_wo('<? echo $row[csf("pi_id")]; ?>')" > View </a></p></td>
			                    <td width="80" align="center"><? echo $row[csf("pi_number")];?></td>
			                    <td width="100" align="center"><? echo $row[csf("id")];?></td>
			                    <td width="80" align="center"><? echo change_date_format($row[csf("pi_date")]);?></td>
			                    
			                    <td width="150"><? echo $supplier_arr[$row[csf("supplier_id")]];?></td>
			                    <td width="80"><? echo  $user_arr[$row[csf("inserted_by")]];?></td>
			                    <td width="80" align="center"><? echo change_date_format($row[csf("insert_date")]);?></td>
			                    <td width="80" align="right"><? echo $row[csf("amount")];?></td>
			                    <td width="80" align="right"><? echo $row[csf("upcharge")];?></td>
			                    <td width="80" align="right"><? echo $row[csf("discount")];?></td>
			                    <td width="80" align="right"><? echo $row[csf("net_total_amount")];?></td>
			                    <td width="80" align="center"><? echo $approved;?></td>
			                    <td width="100" align="center"><p><a href="##" onClick="openmypage_approve('<? echo $row[csf("pi_id")]; ?>')" > View </a></p></td>
			                    <td><a href="javascript:openmypage_image(<?= $row[csf("pi_id")];?>)"><?= ($image_arr[$row[csf("pi_id")]])?"View File":"No File";?></a></td>
			                </tr>
			                <?
			                $total_amount += $row[csf("amount")];
							$sub_total_amount+=$row[csf("amount")];
			                $total_upcharge += fn_number_format($row[csf("upcharge")],2);
							$sub_total_upcharge+=fn_number_format($row[csf("upcharge")],2);
			                $total_discount += fn_number_format($row[csf("discount")],2);
							$sub_total_discount+=fn_number_format($row[csf("discount")],2);
			                $total_net_amount += $row[csf("net_total_amount")];
							$sub_total_net_amount+=$row[csf("net_total_amount")];
						    $i++;
			            }
						echo "<tr bgcolor='#999999'>
							<td colspan='9' align='right'><strong>Sub Total :</strong></td>
							<td align='right'><strong>".number_format($sub_total_amount,2)."</strong></td>
							<td align='right'><strong>".fn_number_format($sub_total_upcharge,2)."</strong></td>
							<td align='right'><strong>".fn_number_format($sub_total_discount,2)."</strong></td>
							<td align='right'><strong>".number_format($sub_total_net_amount,2)."</strong></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>";
					}
		            ?>
		                </tbody>
		                <tfoot>
						<tr>
							<td colspan="9" align="right"><strong>Grand Total:</strong> </td>
							<td align="right"><strong><? echo number_format($total_amount,2); ?></strong></td>
							<td align="right"><strong><? echo fn_number_format($total_upcharge,2); ?></strong></td>
							<td align="right"><strong><? echo fn_number_format($total_discount,2); ?></strong></td>
							<td align="right"><strong><? echo number_format($total_net_amount,2); ?></strong></td>
		                    <td></td>
		                    <td></td>
		                    <td></td>
						</tr>
					</tfoot>
	            </table>
	        </div>
	        </fieldset>
		</div>
		<?
	}
	if ($type==3) //pi status
	{
		if($cbo_item_category_id !=""){
			$category_cond = " and b.item_category_id in ($cbo_item_category_id)";
		}
		$approve_cond ='';
		if($cbo_approval_status !=0){
			if($cbo_approval_status ==1){
				$approve_cond = " and a.approved = 0";
			}
			else if($cbo_approval_status ==2){
				$approve_cond = " and a.approved = 3";
			}
			else if($cbo_approval_status ==3){
				$approve_cond = " and a.approved = 1";
			}
			else{
				$approve_cond ='';
			}
		}

		//$work_order_no_str=",listagg(cast(b.work_order_no as varchar(4000)),',') within group (order by b.work_order_no) as work_order_no";
		//$work_order_id_str=",listagg(b.work_order_id,',') within group (order by b.id) as work_order_id";
		
		$sql="SELECT b.pi_id,a.id,a.pi_number,a.pi_date,a.importer_id,a.approved,a.supplier_id,a.pi_inhand_date,a.goods_rcv_status,a.source,b.item_category_id,b.inserted_by,a.insert_date,a.upcharge,a.discount,a.net_total_amount, sum (b.amount) as amount $work_order_id_str $work_order_no_str from com_pi_master_details  a, com_pi_item_details b where a.id=b.pi_id $importer_cond $pi_sql_cond $pi_sql_btb $date_range_cond $category_cond $approve_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.pi_id,a.id,a.pi_number,a.pi_date,a.importer_id,a.approved,a.supplier_id,a.pi_inhand_date,a.goods_rcv_status,a.source,b.item_category_id,b.inserted_by,a.insert_date,a.upcharge,a.discount,a.net_total_amount";
	
		// echo $sql ; 
		//  die;
		$sql_result = sql_select($sql);
		foreach($sql_result as $row){
			//$piDataArr[$row[csf("item_category_id")].'_'.$row[csf("pi_number")]][]=$row;
			$piDataArr[$row[csf("item_category_id")]][]=$row;
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
	
		// var_dump($lc_value);
		$image_arr = return_library_array( "select MASTER_TBLE_ID, IMAGE_LOCATION from common_photo_library where FORM_NAME='proforma_invoice' and IS_DELETED=0","MASTER_TBLE_ID","IMAGE_LOCATION");
		$user_arr = return_library_array( "select id, user_name from user_passwd","id","user_name");
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	    ob_start();
		?>

		<div style="height:auto; width:1400px; margin:15px auto; padding:0;">
	        <fieldset>
	        <table width="1600" align="center">
	            <tr>
	                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
	            </tr>
	            <tr>
	                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <? $com_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name"); echo $com_name; ?></strong></td>
	            </tr>
	        </table>
	        <table width="1600" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
	            <thead>
	                <tr> 
					<th width="40">SL No</th>
						<th width="100">System ID</th>
						<th width="80">PI No</th>
						<th width="80">PI Value</th>
						<th width="80">Upcharge</th>
	                    <th width="80">Discount</th>
	                    <th width="80">Net Amount</th>
						<th width="80">PI Date</th>
						<th width="80">PI In Hand Date</th>
						<th width="80">PI Insert Date</th>
						<th width="100">Insert User Name</th>
						<th width="100">Item Category</th>
						<th width="120">Goods Rcv Status</th>
						<th width="100">Supplier Name</th>
						<th width="50">WO</th>
						<th width="100">LC Number</th>
						<th width="80">LC Date</th>
						<th width="80">Supply Source</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:1620px; max-height:350px; overflow-x:hidden; overflow-y: scroll;" id="scroll_body">
	            <table width="1600" align="left" height="auto" class="rpt_table" rules="all" id="table_body_id" cellspacing="0" cellpadding="2" border="1" style="word-break: break-all;">
	                <tbody>
		            <?
		            $i=1;
		            foreach($piDataArr as $result)
		            {
						$sub_total_qty=0;
						$sub_total_amount=$sub_total_upcharge=$sub_total_discount=$sub_total_net_amount=0;
						foreach($result as $row)
			            {
			                ($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
			                if ($row[csf("approved")]==1) { 
			                	$approved='Yes';
			                }else if ($row[csf("approved")]==3) { 
			                	$approved='Partial';
			                }
			                else { 
			                	$approved='No';
			                }
			                //$item_cat_id = $row[csf("item_category_id")]*1;
			                ?>
			                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                    <td width="40"><? echo $i;?></td>
								<td width="100" align="center"><? echo $row[csf("id")];?></td>
								<td width="80" align="center"><? echo $row[csf("pi_number")];?></td>
								<td width="80" align="center"><? echo $row[csf("amount")];?></td>
								<td width="80" align="center"><? echo $row[csf("upcharge")];?></td>
								<td width="80" align="center"><? echo $row[csf("discount")];?></td>
								<td width="80" align="center"><? echo $row[csf("net_total_amount")];?></td>
								<td width="80" align="center"><? echo change_date_format($row[csf("pi_date")]);?></td>
								<td width="80" align="center"><? echo change_date_format($row[csf("pi_inhand_date")]);?></td>
								<td width="80" align="center"><? echo change_date_format($row[csf("insert_date")]);?></td>
								<td width="100" align="center"><? echo  $user_arr[$row[csf("inserted_by")]];?></td>
								<td width="100" align="center"><? echo $item_category[$row[csf("item_category_id")]];?></td>
								<td width="120" align="center"><? echo $acceptance_time[$row[csf("goods_rcv_status")]];?></td>
								<td width="100" align="center"><? echo $supplier_arr[$row[csf("supplier_id")]];?></td>
								<td width="50" align="center"><p><a href="##" onClick="openmypage_pi_wo('<? echo $row[csf("pi_id")]; ?>')" > View </a></p></td>
								<?
		$lc_value=sql_select("select c.id,d.pi_id,d.com_btb_lc_master_details_id,c.lc_number,c.lc_date
		from com_btb_lc_master_details c, com_btb_lc_pi d where d.com_btb_lc_master_details_id=c.id and d.pi_id='".$row[csf('id')]."' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by c.lc_number,c.lc_date,d.pi_id,d.com_btb_lc_master_details_id,c.id");
		$lc_num_array;
		$lc_date_array;
		if(empty($lc_value)){
			?>
						<td width="100" align="center"></td>
						<td width="80" align="center"></td>
						<td width="80" align="center"><? echo $source[$row[csf("source")]];?></td>
						<?
		}else{
		foreach ($lc_value as $rows){
			$lc_num_array[$rows[csf("id")]]["lc_number"] =  $rows[csf("lc_number")];
			$lc_date_array[$rows[csf("id")]]["lc_date"] =  $rows[csf("lc_date")];
			// var_dump($lc_value);
			?>
						<td width="100" align="center"><? echo $lc_num_array[$rows[csf("id")]]["lc_number"];?></td>
						<td width="80" align="center"><? echo change_date_format($lc_date_array[$rows[csf("id")]]["lc_date"]);?></td>
						<td width="80" align="center"><? echo $source[$row[csf("source")]];?></td>
						<?
				}
		}
		// die;
									?>
								</tr>
								<?
								$total_amount += $row[csf("amount")];
								$sub_total_amount+=$row[csf("amount")];
								$total_upcharge += fn_number_format($row[csf("upcharge")],2);
								$sub_total_upcharge+=fn_number_format($row[csf("upcharge")],2);
								$total_discount += fn_number_format($row[csf("discount")],2);
								$sub_total_discount+=fn_number_format($row[csf("discount")],2);
								$total_net_amount += $row[csf("net_total_amount")];
								$sub_total_net_amount+=$row[csf("net_total_amount")];
								$i++;
							}
							echo "<tr bgcolor='#999999'>
								<td colspan='3' align='right'><strong>Sub Total :</strong></td>
								<td align='right'><strong>".number_format($sub_total_amount,2)."</strong></td>
								<td align='right'><strong>".fn_number_format($sub_total_upcharge,2)."</strong></td>
								<td align='right'><strong>".fn_number_format($sub_total_discount,2)."</strong></td>
								<td align='right'><strong>".number_format($sub_total_net_amount,2)."</strong></td>
								<td colspan='11'></td>

							</tr>";
						}
						?>
							</tbody>
							<tfoot>
							<tr>
								<td colspan="3" align="right"><strong>Grand Total:</strong> </td>
								<td align="right"><strong><? echo number_format($total_amount,2); ?></strong></td>
								<td align="right"><strong><? echo fn_number_format($total_upcharge,2); ?></strong></td>
								<td align="right"><strong><? echo fn_number_format($total_discount,2); ?></strong></td>
								<td align="right"><strong><? echo number_format($total_net_amount,2); ?></strong></td>
								<td colspan="11"></td>

							</tr>
						</tfoot>
					</table>
				</div>
				</fieldset>
			</div>
			<?
	}
	if ($type==4) //pi details
	{
		if($cbo_item_category_id !=""){
			$category_cond = " and a.item_category_id in ($cbo_item_category_id)";
		}

		$sql="SELECT a.id as ID,a.item_category_id as ITEM_CATEGORY_ID,a.pi_number as PI_NUMBER,a.pi_date as PI_DATE, a.insert_date as INSERT_DATE,b.work_order_no as WORK_ORDER_NO, sum(b.amount) as AMOUNT, c.job_no as JOB_NO,d.style_ref_no as STYLE_REF_NO,d.buyer_name as BUYER_NAME
		from com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c,wo_po_details_master d
		where a.id=b.pi_id and a.item_category_id=4 and b.work_order_dtls_id=c.id and c.job_no=d.job_no and a.pi_basis_id in(1,3,4) $importer_cond $pi_sql_cond $date_range_cond $category_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  group by a.id,a.item_category_id,a.pi_number,a.pi_date,a.insert_date, b.work_order_no, c.job_no,d.style_ref_no,d.buyer_name
		union all
		SELECT a.id as ID,a.item_category_id as ITEM_CATEGORY_ID,a.pi_number as PI_NUMBER,a.pi_date as PI_DATE, a.insert_date as INSERT_DATE,b.work_order_no as WORK_ORDER_NO, sum(b.amount) as AMOUNT, c.job_no as JOB_NO,c.style_no as STYLE_REF_NO,c.buyer_id as BUYER_NAME
		from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c
		where a.id=b.pi_id and a.item_category_id=1 and b.work_order_dtls_id=c.id and a.pi_basis_id in(1,3,4) $importer_cond $pi_sql_cond $date_range_cond $category_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0
		and b.is_deleted=0 group by a.id,a.item_category_id,a.pi_number,a.pi_date,a.insert_date, b.work_order_no, c.job_no,c.style_no,c.buyer_id";
		// echo $sql ; die;

		$sql_result = sql_select($sql);
		foreach($sql_result as $row)
		{
			$piDataArr[$row["PI_NUMBER"].'_'.$row["BUYER_NAME"]][]=$row;

		}

		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	    ob_start();
		?>

		<div style="height:auto; width:900px; margin:15px auto; padding:0;">
		 <span style="color:red"> This Report Is Not Contant Independent Basis </span>
	        <fieldset>
	        <table width="850" align="center">
	            <tr>
	                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
	            </tr>
	            <tr>
	                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <? $com_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name"); echo $com_name; ?></strong></td>
	            </tr>
	        </table>
	        <table width="850" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
	            <thead>
	                <tr>
	                    <th width="40">SL</th>
	                    <th width="120">Buyer</th>
	                    <th width="120">Style</th>
	                    <th width="80">Job No</th>
	                    <th width="100">WO No</th>
	                    <th width="100">PI</th>
						<? if($cbo_based_on ==1) : ?>
	                    <th width="60">PI Date</th>
						<? elseif($cbo_based_on ==2) : ?>
	                    <th width="60">PI Insert Date</th>
						<? endif; ?>
	                    <th width="100">Item Category</th>
	                    <th >Value</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:900px; max-height:350px; overflow-x:hidden; overflow-y: scroll;" id="scroll_body">
	            <table width="850" align="left" height="auto" class="rpt_table" rules="all" id="table_body_id" cellspacing="0" cellpadding="2" border="1" style="word-break: break-all;">
	                <tbody>
		            <?
		            $i=1;
					$total_amount=0;
		            foreach($piDataArr as $result)
		            {
						$sub_total_amount=0;
						foreach($result as $row)
			            {
			                ($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
			                //$item_cat_id = $row[csf("item_category_id")]*1;
			                ?>
			                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                    <td width="40" ><? echo $i;?></td>
			                    <td width="120" ><? echo $buyer_arr[$row["BUYER_NAME"]];?></td>
			                    <td width="120" ><? echo $row["STYLE_REF_NO"];?></td>
			                    <td width="80" ><? echo $row["JOB_NO"];?></td>
			                    <td width="100" ><? echo $row["WORK_ORDER_NO"];?></td>
			                    <td width="100" align="center"><? echo $row["PI_NUMBER"];?></td>
								<? if($cbo_based_on ==1) : ?>
			                    	<td width="60" align="center"><? echo change_date_format($row["PI_DATE"]);?></td>
								<? elseif($cbo_based_on ==2) : ?>
									<td width="60" align="center"><? echo change_date_format($row["INSERT_DATE"]);?></td>
								<? endif; ?>
								<td width="100" ><? echo $item_category[$row["ITEM_CATEGORY_ID"]];?></td>
			                    <td align="right"><? echo $row["AMOUNT"];?></td>

			                </tr>
			                <?
			                $total_amount += $row["AMOUNT"];
							$sub_total_amount+=$row["AMOUNT"];
						    $i++;
			            }
						echo "<tr bgcolor='#999999'>
							<td colspan='8' align='right'><strong>Sub Total :</strong></td>
							<td align='right'><strong>".number_format($sub_total_amount,2)."</strong></td>
						</tr>";
					}
		            ?>
	                </tbody>
		                <tfoot>
						<tr>
							<td colspan="8" align="right"><strong>Grand Total:</strong> </td>
							<td align="right"><strong><? echo number_format($total_amount,2); ?></strong></td>
						</tr>
					</tfoot>
	            </table>
	        </div>
	        </fieldset>
		</div>
		<?
	}
	if ($type==5) //show 3
	{
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
		$itemGroupArr=return_library_array( "SELECT id,item_name FROM lib_item_group WHERE item_category=4 AND status_active = 1",'id','item_name');

		if($cbo_item_category_id !=""){
			$category_cond = " and a.item_category_id in ($cbo_item_category_id) ";
		}

		if($db_type==0)
		{
			$col_add_sql=", group_concat(distinct b.item_group) as ITEM_GROUP, group_concat(distinct b.YARN_COMPOSITION_ITEM1) as YARN_COMPOSITION_ITEM1, group_concat(concat(b.body_part_id,'_',b.fab_type,'_',b.fabric_construction,'_',b.fab_design,'_',b.fabric_composition)) as FABRIC_ITEM";
		}
		else
		{
			$col_add_sql=", listagg(b.item_group ,',') within group (order by b.id) as ITEM_GROUP,  listagg(b.YARN_COMPOSITION_ITEM1 ,',') within group (order by b.id) as YARN_COMPOSITION_ITEM1, listagg((b.body_part_id || '_' || b.fab_type || '_' || b.fabric_construction || '_' || b.fab_design || '_' || b.fabric_composition) ,',') within group (order by b.id) as FABRIC_ITEM";
		}

		$sql="SELECT a.id as ID, a.item_category_id as ITEM_CATEGORY_ID, a.pi_number as PI_NUMBER, a.pi_date as PI_DATE , a.insert_date as INSERT_DATE, a.supplier_id as SUPPLIER_ID, b.work_order_no as WORK_ORDER_NO, sum(b.amount) as AMOUNT, c.job_no as JOB_NO, d.style_ref_no as STYLE_REF_NO, d.buyer_name as BUYER_NAME $col_add_sql, to_char(c.booking_no) as BOOKING_NO
		from com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c,wo_po_details_master d
		where a.id=b.pi_id and a.item_category_id=4 and b.work_order_dtls_id=c.id and c.job_no=d.job_no and a.pi_basis_id in(1,3,4) $importer_cond $pi_sql_cond $date_range_cond $category_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
		group by a.id,a.item_category_id,a.pi_number,a.pi_date,a.insert_date, a.supplier_id, b.work_order_no, c.job_no,d.style_ref_no,d.buyer_name,c.booking_no
		union all
		SELECT a.id as ID, a.item_category_id as ITEM_CATEGORY_ID, a.pi_number as PI_NUMBER, a.pi_date as PI_DATE , a.insert_date as INSERT_DATE, a.supplier_id as SUPPLIER_ID, b.work_order_no as WORK_ORDER_NO, sum(b.amount) as AMOUNT, c.job_no as JOB_NO, c.style_no as STYLE_REF_NO, c.buyer_id as BUYER_NAME $col_add_sql, to_char(c.booking_no) as BOOKING_NO
		from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c
		where a.id=b.pi_id and a.item_category_id=1 and b.work_order_dtls_id=c.id and a.pi_basis_id in(1,3,4) $importer_cond $pi_sql_cond $date_range_cond $category_cond and c.requisition_dtls_id>0 and a.status_active=1 and b.status_active=1 and c.status_active=1 
		group by a.id,a.item_category_id,a.pi_number,a.pi_date,a.insert_date, a.supplier_id, b.work_order_no, c.job_no,c.style_no,c.buyer_id,c.booking_no
		union all
		SELECT a.id as ID, a.item_category_id as ITEM_CATEGORY_ID, a.pi_number as PI_NUMBER, a.pi_date as PI_DATE , a.insert_date as INSERT_DATE, a.supplier_id as SUPPLIER_ID, b.work_order_no as WORK_ORDER_NO, sum(b.amount) as AMOUNT, d.job_no as JOB_NO, e.style_ref_no as STYLE_REF_NO, e.buyer_name as BUYER_NAME $col_add_sql, to_char(c.booking_no) as BOOKING_NO
		from com_pi_master_details a, com_pi_item_details b, wo_booking_mst c, wo_booking_dtls d, wo_po_details_master e
		where a.id=b.pi_id and a.item_category_id in (2,3) and d.job_no=e.job_no and a.pi_basis_id in(1,3,4) and c.booking_no=d.booking_no and c.booking_type in(1,2,6) and b.work_order_id=c.id and d.fabric_color_id=b.color_id and d.construction=b.fabric_construction and d.copmposition=b.fabric_composition and d.dia_width=b.dia_width $importer_cond $pi_sql_cond $date_range_cond $category_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
		group by a.id,a.item_category_id,a.pi_number,a.pi_date,a.insert_date, a.supplier_id, b.work_order_no, d.job_no,e.style_ref_no,e.buyer_name,c.booking_no";
		// echo $sql ; die;

		$sql_result = sql_select($sql);
		if(count($sql_result)==0)
		{
			?><div style="text-align:center; width:50%; margin-left:25%" class="text-center alert alert-danger">NO DATA FOUND</div><?
			die();
		}

		foreach($sql_result as $row)
		{
			$piDataArr[$row["PI_NUMBER"].'_'.$row["BUYER_NAME"]][]=$row;
			$pi_id_arr[$row["ID"]]=$row["ID"];
			$booking_no_arr[$row["BOOKING_NO"]]=$row["BOOKING_NO"];
			$job_no_arr[$row["JOB_NO"]]=$row["JOB_NO"];
		}
		unset($sql_result);

		$pi_id_in=where_con_using_array($pi_id_arr,0,'b.pi_id');
		$btb_sql="SELECT a.id, a.LC_VALUE, c.PI_ID, sum(c.current_acceptance_value) as ACCEPTANCE_VALUE
		from COM_BTB_LC_MASTER_DETAILS a, COM_BTB_LC_PI b, com_import_invoice_dtls c
		where a.id=b.com_btb_lc_master_details_id and a.id=c.btb_lc_id and b.pi_id=c.pi_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $pi_id_in 
		group by a.id, a.lc_value, c.pi_id";
		$btb_result=sql_select($btb_sql);
		$btbDataArr=array();
		foreach($btb_result as $row)
		{
			$btbDataArr[$row["PI_ID"]]["LC_VALUE"]=$row["LC_VALUE"];
			$btbDataArr[$row["PI_ID"]]["ACCEPTANCE_VALUE"]=$row["ACCEPTANCE_VALUE"];
		}
		unset($btb_result);

		$booking_no_in=where_con_using_array($booking_no_arr,1,'a.booking_no');
		$job_no_in=where_con_using_array($job_no_arr,1,'a.job_no');
		$po_sql="SELECT a.job_no,a.booking_no,b.id,b.po_number,b.po_total_price
		from wo_booking_dtls a, wo_po_break_down b
		where a.po_break_down_id=b.id and a.status_active=1 and b.status_active=1 $booking_no_in $job_no_in
		group by a.job_no,a.booking_no,b.id,b.po_number,b.po_total_price";
		// echo $po_sql;
		$po_result=sql_select($po_sql);
		$poDataArr=array();
		foreach($po_result as $row)
		{
			$poDataArr[$row["JOB_NO"]][$row["BOOKING_NO"]]["po_number"].=$row["PO_NUMBER"].", ";
			$poDataArr[$row["JOB_NO"]][$row["BOOKING_NO"]]["po_value"]+=$row["PO_TOTAL_PRICE"];
		}
		unset($po_result);
		$tbl_width=1500;
	    ob_start();
		?>
		<style>
			.wrd_brk{word-break: break-all;}
		</style>

		<div style="height:auto; width:<?=$tbl_width;?>px; margin:15px auto; padding:0;">
		 <span style="color:red"> This Report Is Not Contant Independent Basis </span>
	        <fieldset>
	        <table width="<?=$tbl_width;?>" align="center">
	            <tr>
	                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
	            </tr>
	            <tr>
	                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <? $com_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name"); echo $com_name; ?></strong></td>
	            </tr>
	        </table>
	        <table width="<?=$tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
	            <thead>
	                <tr>
	                    <th width="40">SL</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Export PO</th>
	                    <th width="120">Style</th>
	                    <th width="80">Job No</th>
	                    <th width="120">Supplier Name</th>
	                    <th width="100">WO No</th>
	                    <th width="100">PI</th>
						<? if($cbo_based_on ==1) : ?>
	                    <th width="60">PI Date</th>
						<? elseif($cbo_based_on ==2) : ?>
						<th width="60">PI Insert Date</th>
						<? endif; ?>
	                    <th width="100">Item Name</th>
	                    <th width="100">Item Category</th>
	                    <th width="100">Export Order Value</th>
	                    <th width="80">PI Value</th>
	                    <th width="80">LC Issue</th>
	                    <th width="80">Acceptance Value</th>
	                    <th width="120">LC% On Export Order Value</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:<?=$tbl_width+18;?>px; max-height:350px; overflow-x:hidden; overflow-y: scroll;" id="scroll_body">
	            <table width="<?=$tbl_width;?>" align="left" height="auto" class="rpt_table" rules="all" id="table_body_id" cellspacing="0" cellpadding="2" border="1" style="word-break: break-all;">
	                <tbody>
		            <?
		            $i=1;
					$total_amount=0;
		            foreach($piDataArr as $result)
		            {
						$sub_total_amount=0;
						foreach($result as $row)
			            {
			                ($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
							$item_name="";
							if($row["ITEM_CATEGORY_ID"]==1)
							{
								$item_group_arr=array_unique(explode(",",$row["YARN_COMPOSITION_ITEM1"]));
								foreach($item_group_arr as $val)
								{
									$item_name.=$composition[$val].", ";
								}
							}
							if($row["ITEM_CATEGORY_ID"]==4)
							{
								$item_group_arr=array_unique(explode(",",$row["ITEM_GROUP"]));
								foreach($item_group_arr as $val)
								{
									$item_name.=$itemGroupArr[$val].", ";
								}								
							}
							if($row["ITEM_CATEGORY_ID"]==3)
							{
								$item_group_arr=array_unique(explode(",",$row["FABRIC_ITEM"]));
								foreach($item_group_arr as $val)
								{
									$item_name_arr=explode("_",$val);
									$item_name.=$lib_body_part_arr[$item_name_arr[0]]." ".$item_name_arr[1]." ".$item_name_arr[2]." ".$item_name_arr[3]." ".$item_name_arr[4].", ";
								}								
							}
							
			                ?>
			                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
			                    <td width="40" ><?=$i;?></td>
			                    <td width="120" class="wrd_brk" ><?=$buyer_arr[$row["BUYER_NAME"]];?></td>
			                    <td width="100" class="wrd_brk" ><?=rtrim($poDataArr[$row["JOB_NO"]][$row["BOOKING_NO"]]["po_number"],", ");?></td>
			                    <td width="120" class="wrd_brk" ><?=$row["STYLE_REF_NO"];?></td>
			                    <td width="80" class="wrd_brk" ><?=$row["JOB_NO"];?></td>
								<td width="120" class="wrd_brk" ><?=$supplier_arr[$row["SUPPLIER_ID"]];?></td>
			                    <td width="100" class="wrd_brk" ><?=$row["WORK_ORDER_NO"];?></td>
			                    <td width="100" class="wrd_brk" align="center"><?=$row["PI_NUMBER"];?></td>
								<? if($cbo_based_on ==1) : ?>
			                    	<td width="60" align="center"><?=change_date_format($row["PI_DATE"]);?></td>
								<? elseif($cbo_based_on ==2) : ?>
									<td width="60" align="center"><?=change_date_format($row["INSERT_DATE"]);?></td>
								<? endif; ?>
								<td width="100" class="wrd_brk"><?=chop($item_name,", ");?></td>
								<td width="100" ><?=$item_category[$row["ITEM_CATEGORY_ID"]];?></td>
								<td width="100" ><?=number_format($poDataArr[$row["JOB_NO"]][$row["BOOKING_NO"]]["po_value"],2,'.','');?></td>
			                    <td width="80" align="right"><?=number_format($row["AMOUNT"],2,'.','');?></td>
			                    <td width="80" align="right"><?=number_format($btbDataArr[$row["ID"]]["LC_VALUE"],2,'.','');?></td>
			                    <td width="80" align="right"><?=number_format($btbDataArr[$row["ID"]]["ACCEPTANCE_VALUE"],2,'.','');?></td>
			                    <td width="120" align="right"><?=fn_number_format($btbDataArr[$row["ID"]]["LC_VALUE"]/$poDataArr[$row["JOB_NO"]][$row["BOOKING_NO"]]["po_value"],2,'.','');?></td>
			                </tr>
			                <?
			                $total_amount += $row["AMOUNT"];
							$sub_total_amount+=$row["AMOUNT"];
						    $i++;
			            }
						?>
						<tr bgcolor='#999999'>
							<td colspan='12' align='right'><strong>Sub Total :</strong></td>
							<td align='right'><strong><?=number_format($sub_total_amount,2);?></strong></td>
							<td ></td>
							<td ></td>
							<td ></td>
						</tr>
						<?
					}
		            ?>
	                </tbody>
		                <tfoot>
						<tr>
							<td colspan="12" align="right"><strong>Grand Total:</strong> </td>
							<td align="right"><strong><?=number_format($total_amount,2); ?></strong></td>
							<td ></td>
							<td ></td>
							<td ></td>
						</tr>
					</tfoot>
	            </table>
	        </div>
	        </fieldset>
		</div>
		<?
	}
	if ($type==6) //show 4
	{
		$supplierArr=return_library_array( "SELECT id, supplier_name from lib_supplier where status_active=1", "id", "supplier_name"  );
		$buyerArr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active=1", "id", "buyer_name"  );
		$yarn_count_name_array=return_library_array("SELECT id, yarn_count from lib_yarn_count where status_active=1", "id", "yarn_count");
		$item_group_array=return_library_array("SELECT id,item_name from lib_item_group where status_active=1", "id", "item_name");
		$item_product_array=return_library_array("SELECT id,product_name_details from product_details_master where status_active=1", "id", "product_name_details");
		$item_color_array=return_library_array("SELECT id, color_name from lib_color where status_active=1", "id", "color_name");

		if($cbo_item_category_id !=""){
			$category_cond = " and a.item_category_id in ($cbo_item_category_id)";
		}

		$main_sql="SELECT a.id,a.item_category_id,a.pi_number,a.pi_date,a.insert_date,a.importer_id,a.supplier_id, a.pay_term, a.tenor, a.ready_to_approved, a.remarks, a.goods_rcv_status,
		b.id as PI_DTLS_ID,b.work_order_no,b.work_order_id,b.work_order_dtls_id,b.item_group,b.item_prod_id,b.item_color,b.size_id,b.item_size,b.count_name,b.yarn_composition_item1,b.yarn_composition_percentage1, b.yarn_composition_item2,b.fabric_composition,b.fabric_construction,b.yarn_type,b.dia_width,b.weight,b.gsm, b.item_description,b.item_size,b.uom,b.quantity,b.rate,b.amount, 
		d.lc_number, d.lc_date, d.lc_expiry_date, d.last_shipment_date, 
		f.invoice_no, f.invoice_date, f.bill_no, f.bill_date
		from com_pi_master_details a, com_pi_item_details b, com_btb_lc_pi c, com_btb_lc_master_details d, com_import_invoice_dtls e, com_import_invoice_mst f
		where a.id=b.pi_id and a.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and d.id=e.btb_lc_id and a.id=e.pi_id and e.import_invoice_id=f.id $importer_cond $pi_sql_cond $date_range_cond $category_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 ";
		// echo $main_sql;
		$sql_result = sql_select($main_sql);
		$piDataArr=$yarn_wo_dtls_id_arr=array();
		foreach($sql_result as $row)
		{
			$piDataArr[$row["ITEM_CATEGORY_ID"]][]=$row;

			if($row["ITEM_CATEGORY_ID"]==1)
			{
				$yarn_wo_dtls_id_arr[$row["WORK_ORDER_DTLS_ID"]]=$row["WORK_ORDER_DTLS_ID"];
			}

			if($row["GOODS_RCV_STATUS"]==1)
			{
				$wo_id_all.=$row["WORK_ORDER_ID"].',';
			}
			else
			{
				$pi_id_all.=$row["ID"].',';
			}
		}
		// echo "<pre>"; print_r($piDataArr); die;
		$wo_id_all=implode(",",array_unique(explode(",",chop($wo_id_all,','))));
		$pi_id_all=implode(",",array_unique(explode(",",chop($pi_id_all,','))));

		$yarn_data_arr=array();
		if(count($yarn_wo_dtls_id_arr)>0)
		{
			$yarn_wo_dtls_in=where_con_using_array($yarn_wo_dtls_id_arr,0,'g.id');
			$yarn_sql="SELECT a.sales_booking_no, a.booking_date, a.customer_buyer, c.yarn_data, g.id as wo_dtls_id, g.number_of_lot
			from fabric_sales_order_mst a, fabric_sales_order_dtls b, fabric_sales_order_yarn c, inv_purchase_requisition_mst d, inv_purchase_requisition_dtls e, wo_non_order_info_mst f, wo_non_order_info_dtls g
			where a.id=b.mst_id and a.id=c.mst_id and b.determination_id=c.deter_id and b.gsm_weight=c.gsm and a.id=e.job_id and d.id=e.mst_id and e.id=g.requisition_dtls_id and f.id=g.mst_id and d.entry_form=70 and f.entry_form=144 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 $yarn_wo_dtls_in";
			// echo $yarn_sql;
			$yarn_result = sql_select($yarn_sql);
			foreach($yarn_result as $row)
			{
				$yarn_data_arr[$row["WO_DTLS_ID"]]["SALES_BOOKING_NO"]=$row["SALES_BOOKING_NO"];
				$yarn_data_arr[$row["WO_DTLS_ID"]]["BOOKING_DATE"]=$row["BOOKING_DATE"];
				$yarn_data_arr[$row["WO_DTLS_ID"]]["CUSTOMER_BUYER"]=$row["CUSTOMER_BUYER"];
				$yarn_data_arr[$row["WO_DTLS_ID"]]["YARN_DATA"]=$row["YARN_DATA"];
				$yarn_data_arr[$row["WO_DTLS_ID"]]["NUMBER_OF_LOT"]=$row["NUMBER_OF_LOT"];
			}
		}

		if ($pi_id_all!='')
		{
			$sql_recv="SELECT a.id,a.receive_basis,a.item_category,a.booking_id,b.pi_wo_dtls_id, a.receive_date, b.order_qnty
			from inv_receive_master a, inv_transaction b
			where a.company_id=$cbo_company_name and a.receive_basis=1 and a.booking_id in($pi_id_all) and a.id=b.mst_id and b.transaction_type=1 and a.status_active=1  and b.status_active=1  ";
		}
		if($wo_id_all!='')
		{
			if($sql_recv!=''){$sql_recv.=" union all ";}
			$sql_recv.="SELECT a.id,a.receive_basis,a.item_category,a.booking_id,b.pi_wo_dtls_id, a.receive_date, b.order_qnty
			from inv_receive_master a, inv_transaction b, product_details_master c 
			where a.company_id=$cbo_company_name and a.receive_basis=2 and a.booking_id in($wo_id_all) and a.id=b.mst_id and b.transaction_type=1 and a.status_active=1 and b.status_active=1 ";
		}
		// echo $sql_recv;
                        
		$sql_recv_result=sql_select($sql_recv);
		$rcv_data_arr=array();
		foreach($sql_recv_result as $row)
		{
			$rcv_data_arr[$row["ITEM_CATEGORY"]][$row["RECEIVE_BASIS"]][$row["BOOKING_ID"]][$row["PI_WO_DTLS_ID"]]['RCV_QNTY']+=$row["ORDER_QNTY"];
			$rcv_data_arr[$row["ITEM_CATEGORY"]][$row["RECEIVE_BASIS"]][$row["BOOKING_ID"]][$row["PI_WO_DTLS_ID"]]['RCV_DATE']=$row["RECEIVE_DATE"];
		}

		$image_arr = return_library_array( "SELECT MASTER_TBLE_ID, IMAGE_LOCATION from common_photo_library where FORM_NAME='proforma_invoice' and IS_DELETED=0","MASTER_TBLE_ID","IMAGE_LOCATION");
	    ob_start();
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>
		<div style="height:auto; width:3430px; margin:15px auto; padding:0;">
	        <fieldset>
	        <table width="3400" align="center">
	            <tr>
	                <td colspan="15" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
	            </tr>
	            <tr>
	                <td colspan="15" align="center" style="font-size:22px"><strong>Company Name:  <? $com_name=return_field_value("company_name","lib_company","id=$cbo_company_name","company_name"); echo $com_name; ?></strong></td>
	            </tr>
	        </table>
	        <table width="3400" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" id="rpt_table_header" border="1" align="left">
	            <thead>
	                <tr>
	                    <th width="40">SL</th>
	                    <th width="100">System ID</th>
	                    <th width="120">WO</th>
	                    <th width="80">PI No</th>
						<? if($cbo_based_on ==1) : ?>
	                    <th width="80">PI Date</th>
						<? elseif($cbo_based_on ==2) : ?>
						<th width="80">PI Insert Date</th>
						<? endif; ?>
	                    <th width="100">Sales Job/Booking No</th>
	                    <th width="80">Booking Date</th>
	                    <th width="100">Cust. Buyer</th>
	                    <th width="100">Supplier Name</th>
	                    <th width="100">Item Category</th>
	                    <th width="150">Item Group</th>
	                    <th width="180">Item Description</th>
	                    <th width="80">Gmts Color</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">Item Size</th>
	                    <th width="50">UOM</th>
	                    <th width="80">Quantity</th>
	                    <th width="80">Rate</th>
	                    <th width="80">Amount</th>
	                    <th width="100">Pay Term</th>
	                    <th width="80">Tenor</th>
	                    <th width="100">Ready To Approved</th>
	                    <th width="100">LC. NO.</th>
	                    <th width="80">LC Date</th>
	                    <th width="80">LC Expiry Date</th>
	                    <th width="80">Last Shipment Date</th>
	                    <th width="80">Booking Qty (KG)</th>
	                    <th width="80">Total Value</th>
	                    <th width="100">Invoice No.</th>
	                    <th width="80">Invoice Date</th>
	                    <th width="80">Received Qty.</th>
	                    <th width="80">Received Date</th>
	                    <th width="100">BL Number</th>
	                    <th width="80">BL DATE</th>
	                    <th width="100">No. of Lot</th>
	                    <th width="80">Image/File</th>
	                    <th >Remarks</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:3420px; max-height:350px; overflow-x:hidden; overflow-y: scroll;" id="scroll_body">
	            <table width="3400" align="left" height="auto" class="rpt_table" rules="all" id="table_body_id" cellspacing="0" cellpadding="2" border="1" style="word-break: break-all;">
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
							$sales_booking_no=$booking_date=$customer_buyer=$number_of_lot=$rcv_date="";
							$sales_booking_qnty=$sales_booking_amount=0;
							if($row["ITEM_CATEGORY_ID"]==1)
							{
								$description= $yarn_count_name_array[$row["COUNT_NAME"]]." ".$composition[$row["YARN_COMPOSITION_ITEM1"]]." ".$row["YARN_COMPOSITION_PERCENTAGE1"]."% ";
								$sales_booking_no=$yarn_data_arr[$row["WORK_ORDER_DTLS_ID"]]["SALES_BOOKING_NO"];
								$booking_date=$yarn_data_arr[$row["WORK_ORDER_DTLS_ID"]]["BOOKING_DATE"];
								$customer_buyer=$buyerArr[$yarn_data_arr[$row["WORK_ORDER_DTLS_ID"]]["CUSTOMER_BUYER"]];
								$number_of_lot=$yarn_data_arr[$row["WORK_ORDER_DTLS_ID"]]["NUMBER_OF_LOT"];

								$yarn_data = explode("|",$yarn_data_arr[$row["WORK_ORDER_DTLS_ID"]]["YARN_DATA"]);
								foreach ($yarn_data as $val) 
								{
									$yarnData_details = explode("_",$val);
									$sales_booking_qnty+=$yarnData_details[6];
									$sales_booking_amount+=$yarnData_details[10];
								}
							}
							else if($row["ITEM_CATEGORY_ID"]==2 || $row["ITEM_CATEGORY_ID"]==3 || $row["ITEM_CATEGORY_ID"]==13 || $row["ITEM_CATEGORY_ID"]==14)
							{
								$description= $row["COLOR_ID"]." ".$row["FABRIC_COMPOSITION"]." ".$row["FABRIC_CONSTRUCTION"];
							}
							else if(!in_array($item_category[$row["ITEM_CATEGORY_ID"]], $general_item_category))
							{
								$description= $item_product_array[$row["ITEM_PROD_ID"]];
							}
							else{
								$description = $row["ITEM_DESCRIPTION"];
							}
							$rcv_qnty=0;
							if($row["GOODS_RCV_STATUS"]==1)
							{
								$rcv_qnty=$rcv_data_arr[$row["ITEM_CATEGORY_ID"]][2][$row["WORK_ORDER_ID"]][$row["WORK_ORDER_DTLS_ID"]]['RCV_QNTY'];
								$rcv_date=$rcv_data_arr[$row["ITEM_CATEGORY_ID"]][2][$row["WORK_ORDER_ID"]][$row["WORK_ORDER_DTLS_ID"]]['RCV_DATE'];
							}
							else
							{
								$rcv_qnty=$rcv_data_arr[$row["ITEM_CATEGORY_ID"]][1][$row["ID"]][$row["PI_DTLS_ID"]]['RCV_QNTY'];
								$rcv_date=$rcv_data_arr[$row["ITEM_CATEGORY_ID"]][1][$row["ID"]][$row["PI_DTLS_ID"]]['RCV_DATE'];
							}
			                ?>
			                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td class="center" width="40"><?=$i;?></td>
								<td class="center" width="100"><?=$row["ID"];?></td>
								<td class="wrd_brk" width="120"><?=$row["WORK_ORDER_NO"];?></td>
								<td class="wrd_brk" width="80"><?=$row["PI_NUMBER"];?></td>
								<? if($cbo_based_on ==1) : ?>
			                    	<td class="center" width="80"><?=change_date_format($row["PI_DATE"]);?></td>
								<? elseif($cbo_based_on ==2) : ?>
									<td class="center" width="80"><?=change_date_format($row["INSERT_DATE"]);?></td>
								<? endif; ?>
								<td class="wrd_brk" width="100"><?=$sales_booking_no;?></td>
								<td class="center" width="80">&nbsp;<?=change_date_format($booking_date);?></td>
								<td class="wrd_brk" width="100"><?=$customer_buyer;?></td>
								<td class="wrd_brk" width="100"><?=$supplierArr[$row["SUPPLIER_ID"]]?></td>
								<td class="center" width="100"><?=$item_category[$row["ITEM_CATEGORY_ID"]];?></td>
								<td class="center" width="150"><?=$item_group_array[$row["ITEM_GROUP"]];?></td>
								<td class="wrd_brk" width="180"><?=$description;?></td>
								<td class="center" width="80"><?=$item_color_array[$row["ITEM_COLOR"]];?></td>
								<td class="center" width="80"><?=$item_color_array[$row["YARN_TYPE"]];?></td>
								<td class="center" width="50"><?=$row["ITEM_SIZE"];?></td>
								<td class="center" width="50"><?=$unit_of_measurement[$row["UOM"]];?></td>
								<td class="right" width="80"><?=number_format($row["QUANTITY"],2);?></td>
								<td class="right" width="80"><?=$row["RATE"];?></td>
								<td class="right" width="80"><?=number_format($row["AMOUNT"],2);?></td>
								<td class="wrd_brk" width="100"><?=$pay_term[$row["PAY_TERM"]];?></td>
								<td class="wrd_brk" width="80"><?=$row["TENOR"];?></td>
								<td class="center" width="100"><?=($row["READY_TO_APPROVED"]==1)?"Yes":"No";?></td>
								<td class="wrd_brk" width="100"><?=$row["LC_NUMBER"];?></td>
								<td class="center" width="80">&nbsp;<?=change_date_format($row["LC_DATE"]);?></td>
								<td class="center" width="80">&nbsp;<?=change_date_format($row["LC_EXPIRY_DATE"]);?></td>
								<td class="center" width="80">&nbsp;<?=change_date_format($row["LAST_SHIPMENT_DATE"]);?></td>
								<td class="right" width="80"><?=number_format($sales_booking_qnty,2);?></td>
								<td class="right" width="80"><?=number_format($sales_booking_amount,2);?></td>
								<td class="wrd_brk" width="100"><?=$row["INVOICE_NO"];?></td>
								<td class="center" width="80">&nbsp;<?=change_date_format($row["INVOICE_DATE"]);?></td>
								<td class="right" width="80"><?=number_format($rcv_qnty,2);?></td>
								<td class="center" width="80">&nbsp;<?=change_date_format($rcv_date);?></td>
								<td class="wrd_brk" width="100"><?=$row["BILL_NO"];?></td>
								<td class="center" width="80">&nbsp;<?=change_date_format($row["BILL_DATE"]);?></td>
								<td class="center" width="100"><?=$number_of_lot;?></td>
								<td class="center" width="80"><a href="javascript:openmypage_image(<?=$row["PI_ID"];?>)"><?=($image_arr[$row["PI_ID"]])?"View File":"No File";?></a></td>
								<td class="wrd_brk" ><?=$row["REMARKS"];?></td>
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
							<td colspan='16' align='right'><strong>Sub Total :</strong></td>
							<td align='right'><strong><?=number_format($sub_total_qty,2);?></strong></td>
							<td></td>
							<td align='right'><strong><?=number_format($sub_total_amount,2);?></strong></td>
							<td colspan="18"></td>
						</tr>
						<?
					}
		            ?>
	                </tbody>
		            <tfoot>
						<tr>
							<th colspan="16" align="right"><strong>Grand Total:</strong> </th>
							<th align="right"><strong><? echo number_format($total_qty,2); ?></strong></th>
							<th align="right"></th>
							<th align="right"><strong><? echo number_format($total_amount,2); ?></strong></th>
		                    <th colspan="18"></th>
						</tr>
					</tfoot>
	            </table>
	        </div>
	        </fieldset>
		</div>
		<?
	}
	
    $html = ob_get_contents();
    ob_clean();
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
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" align="center" style="width:200px">
	<fieldset style="width:200px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="200" cellpadding="0" cellspacing="0">
             	<thead>
                    <th width="50">Sl.</th>
                    <th>WO No.</th>
                    
                </thead>
                <tbody>
                <?
				$sql="SELECT pi_id,work_order_no from com_pi_item_details  where pi_id=$pi_id and status_active=1 and is_deleted=0 order by id";
				$result=sql_select($sql); $i=1;
				foreach($result as $row)  
				{
					if ($row[csf('work_order_no')]!='') 
					{
						?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td><p><? echo $row[csf('work_order_no')]; ?></p></td>
							
						</tr>
						<?
						$i++;
					}
					
				}
				?>
                </tbody>   
            </table>
        </fieldset>
    </div>
	<?
    exit();

}
if($action=="page_pi_wo")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$pi_id=str_replace("'","",$pi_id);
	$item_product=sql_select("select id,product_name_details from product_details_master where status_active=1 and is_deleted=0");
		foreach ($item_product as $row){
			$item_product_array[$row[csf("id")]] =  $row[csf("product_name_details")];
		}
	?>
	<script>
	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" align="center" style="width:600px">
	<fieldset style="width:200px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
             	<thead>
                    <th width="50">Sl.</th>
                    <th width="100">Item Category</th>
                    <th width="150">Work Order Number</th>
                    <th width="180">Discription</th>
                    <th width="80">UOM</th>
                    <th width="80">Qty</th>
                    <th width="80">Rate</th>
                    <th width="80">Value</th>
                    
                </thead>
                <tbody>
                <?
				$sql="SELECT pi_id,item_category_id,work_order_no,item_description,uom,quantity,rate,amount,count_name,yarn_composition_item1,yarn_composition_percentage1,color_id,fabric_composition,fabric_construction,item_prod_id from com_pi_item_details  where pi_id=$pi_id and status_active=1 and is_deleted=0 order by id";
				$result=sql_select($sql); $i=1;
				foreach($result as $row)  
				{
					// if ($row[csf('work_order_no')]!='') 
					// {
						?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td align="center"><p><? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('work_order_no')]; ?></p></td>
							<?
			                    if($row[csf("item_category_id")]==1){
									$description= $yarn_count_name_array[$row[csf("count_name")]]." ".$composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."% ";

			                    }elseif ($row[csf("item_category_id")]==2 || $row[csf("item_category_id")]==3 || $row[csf("item_category_id")]==13 || $row[csf("item_category_id")]==14){
			                        $description= $row[csf("fabric_composition")]." ".$row[csf("fabric_construction")];
			                    }elseif (!in_array($item_category[$row[csf("item_category_id")]], $general_item_category)){
			                        //echo $item_category[$item_cat_id];die;
			                        $description= $item_product_array[$row[csf("item_prod_id")]]; //die;
			                    }else{
			                        $description = $row[csf("item_description")];
			                    }
			                        ?>
			                    <td align="center"><? echo $description;?></td>
							<!-- <td align="center"><p><? echo $row[csf('item_description')]; ?></p></td> -->
							<td align="center"><p><? echo $row[csf('uom')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('quantity')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('rate')]; ?></p></td>
							<td align="center"><p><? echo number_format($row[csf('amount')],2); ?></p></td>
							
						</tr>
						<?
						$i++;
					// }
					
				}
				?>
                </tbody>   
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

if($action=="approve_details")
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
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" align="center" style="width:500px">
	<fieldset style="width:500px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
             	<thead> 
                    <th width="50">Sl.</th>
                    <th width="100">Approval Authority</th>
                    <th width="100">Designation</th>
                    <th width="60">Approve Date</th>
                    <th width="60">Approve Time</th>
                    <th width="60">Approve No</th>
                    <th>Remarks</th>
                </thead>
                <tbody>
                <?
				$sql="SELECT mst_id, approved_no, approved_by, approved_date, un_approved_by, un_approved_date, sequence_no, current_approval_status, comments, user_ip, higher_othorized_approved, inserted_by, insert_date, updated_by, update_date, un_approved_reason, full_approved from approval_history  where mst_id=$pi_id and entry_form=27 order by id";
				//and entry_form in ( )
				$result=sql_select($sql); $i=1;
				$user_arr = return_library_array( "select id, user_name from user_passwd","id","user_name");
				$designation_array = return_library_array("select id,custom_designation from lib_designation","id","custom_designation");
				$user_desg = return_library_array( "select id, designation from user_passwd","id","designation");
				foreach($result as $row)
				{
					/*$date1=$row[csf('approved_date')];
					$format = 'd-m-Y H:i:s A';
					$date = DateTime::createFromFormat($format, $date1);*/
					//echo $date->format('H:i:s A') . "\n";
					?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td width="100"><p><? echo $user_arr[$row[csf('approved_by')]]; ?></p></td>
						<td width="100"><p><? echo $designation_array[$user_desg[$row[csf('approved_by')]]]; ?></p></td>
						<td width="60"><p><? echo  change_date_format($row[csf("approved_date")]); ?></p></td>
						<td width="60"><p><? echo date("H:i:s",strtotime($row[csf('approved_date')]));; ?></p></td>
						<td width="60" align="center"><p><? echo $row[csf('approved_no')]; ?></p></td>
						<td ><p><? echo $row[csf('comments')]; ?></p></td>
						
					</tr>
					<?
					$i++;
				}
				?>
                </tbody>   
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

if($action=="image_file")
{
	extract($_REQUEST);
	$image_arr = return_library_array( "select ID, IMAGE_LOCATION from common_photo_library where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID='$pi_id' and IS_DELETED=0","ID","IMAGE_LOCATION");
	
	if(count($image_arr)==''){ echo "<h1>File Not Found</h1>";exit();}
	
	
	foreach($image_arr as $file){
	?>
    	<a href="../../../<?= $file;?>" title="click to download" target="_blank">
        	<img src="../../../file_upload/blank_file.png" width="89" height="97">
        </a>
    <?
	}
exit();
}
?>
