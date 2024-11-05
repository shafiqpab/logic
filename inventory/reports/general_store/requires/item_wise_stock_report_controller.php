<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

if ($action=="load_drop_down_store")
{     	 
	echo create_drop_down( "cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in (".implode(',',array_keys($general_item_category)).") and a.status_active=1 and a.is_deleted=0 and a.company_id in ($data)  group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "", "","" ); 
    exit();
}

//item search------------------------------//
if($action == "load_drop_down_group")
{
	echo create_drop_down( "txt_item_group", 130,"select a.item_name,a.id from lib_item_group a where a.item_category=$data and a.status_active=1 and a.is_deleted=0 group by a.item_name, a.id order by a.id","id,item_name", 1, "-- Select --", $selected, "" );
}

if ($action == "item_description_popup") 
{
	echo load_html_head_contents("Item Details Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(str)
		{
			$("#txt_selected_id").val(str);
			parent.emailwindow.hide(); 
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="item_detailsfrm" id="item_detailsfrm">
            <fieldset style="width:580px;">
                <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                       class="rpt_table" id="tbl_list_search">
                    <thead>
                    <th>Item Category</th>
                    <th>Item Group</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"
                            onClick="reset_form('item_detailsfrm','search_div','','','','');"></th>
                    </thead>
                    <tbody>
                    <tr>

                        <td align="center">
							<?php
							echo create_drop_down("cbo_item_category_id", 160, $general_item_category, "", 1, "-- Select --", $selected, "load_drop_down( 'item_wise_stock_report_controller', this.value, 'load_drop_down_group','group_td');", "", $cred_item_cate_id_cond, "", "", "");
							?>
                        </td> 
                        <td align="center" id="group_td">
                            <?
                    			echo create_drop_down("txt_item_group",130,$blank_array,"",1,"-- Select --",$selected, "" );
                    		?> 
                    	</td>
						<td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code"/></td>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description"/></td>
                        <td align="center"><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('txt_item_group').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+'<? echo $cbo_company_name; ?>', 'item_description_popup_list_view', 'search_div', 'item_wise_stock_report_controller', 'setFilterGrid(\'tbl_list\',-1,\'tableFilters\')');" style="width:100px;"/>
                            <input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
            <div style="margin-top:15px" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    
    </html>
	<?
	exit();
}

if ($action == "item_description_popup_list_view") 
{
	echo load_html_head_contents("Item Creation popup", "../../", 1, 1,'','1','');
	$data = explode('**', $data);
	
	$group = trim($data[1]);
	$description = trim($data[2]);
	$code = trim($data[3]);
	$company = str_replace("'","",$data[4]);

	$item_category_id = $item_group = $item_description = $company_cond = "";
	if ($data[0] != 0) $item_category_id = " and a.item_category_id='$data[0]'";
	if ($data[1] != 0) $item_group = " and a.item_group_id ='$group'";
	if ($data[2] != "") $item_description = " and upper(a.item_description) LIKE upper('%$description%')";
	if ($data[3] != "") $item_code = " and upper(a.item_code) LIKE upper('%$code%') ";
	if ($company != "") $company_cond = " and a.company_id ='$company'";
	
	$sql = "SELECT a.id, a.item_account, a.item_category_id, a.item_description,a.item_code, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, b.item_name
	from lib_item_group b, product_details_master a
	where a.item_group_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id in (".implode(',',array_keys($general_item_category)).") $company_cond $item_category_id $item_group $item_description $item_code 
	";
	$arr=array(2=>$general_item_category,6=>$unit_of_measurement);
	echo create_list_view("list_view", "Item Account,Item Code,Item Category,Item Description,Item Size,Item Group,UOM","100,90,120,150,100,100","800","260",0, $sql , "js_set_value", "id,item_description", "", 1, "0,0,item_category_id,0,0,0,unit_of_measure", $arr, "item_account,item_code,item_category_id,item_description,item_size,item_name,unit_of_measure", '','','0,0,0,0,0,0,0');
	// echo $sql;			
	?>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit(); 
}

if ($action=="item_group_popup")																												
{
	echo load_html_head_contents("Item Group popup", "../../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$("#item_id").val(str);
			parent.emailwindow.hide(); 
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:930px" >
	<fieldset style="width:930px">
		<form name="order_popup_1"  id="order_popup_1">
			<?
			if ($category!=0) {$item_category_list=" and item_category='$category'";}
			$sql="SELECT id,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter from lib_item_group where is_deleted=0 and status_active=1 and item_category in (".implode(',',array_keys($general_item_category)).") $item_category_list";
			$arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$cal_parameter);
			echo create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Cal Parameter", "150,100,200,80,50,50,50","900","320",0, $sql, "js_set_value", "id,item_name", "", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,cal_parameter", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter", "", '','0,0,0,0,0,0,0,0' );
			?>
		<input type="hidden" id="item_id" />
		</form>
	</fieldset>
	</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<? 																																		
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_category_id=str_replace("'","",$cbo_category_id);
	// $txt_product_id=str_replace("'","",$txt_product_id);
	// $txt_item_group_id=str_replace("'","",$txt_item_group_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_item_code=str_replace("'","",$txt_item_code);
	$txt_description=str_replace("'","",$txt_description);
	$txt_item_group=str_replace("'","",$txt_item_group);

 	$lib_company_arr=return_library_array("select id,company_name from lib_company",'id','company_name');
 	$store_name_arr=return_library_array("select id,store_name from lib_store_location",'id','store_name');
 	$group_name_arr=return_library_array("select id,item_name from lib_item_group",'id','item_name');
 	//$sub_group_name_arr=return_library_array("select id,sub_group_name from lib_item_sub_group",'id','sub_group_name');
	$search_cond="";
	if($cbo_company_id!='') $search_cond.=" and a.company_id in ($cbo_company_id)";
	if($cbo_category_id!='') $search_cond.=" and a.item_category_id in ($cbo_category_id)";
	// if($txt_product_id!='') $search_cond.=" and a.id=$txt_product_id";
	// if($txt_item_group_id!='') $search_cond.=" and a.item_group_id=$txt_item_group_id";
	if($txt_item_code!='') $search_cond.=" and a.item_code like '%$txt_item_code%'";	
	//if($txt_description!='') $search_cond.=" and a.item_description='%$txt_description%'";
	if($txt_item_group!='') $search_cond.=" and b.item_name like '%$txt_item_group%'";
	if($cbo_store_name!=0) $search_cond.=" and c.store_id=$cbo_store_name";

	$item_description_cond="";
	$item_description_lower=strtolower($txt_description);
    if($txt_description != "") {$item_description_cond =" and lower(a.item_description) like ('%$item_description_lower%')";}

	$sql = "SELECT a.company_id as COMPANY_ID, a.item_category_id as ITEM_CATEGORY_ID, a.item_description as ITEM_DESCRIPTION, a.item_code as ITEM_CODE, a.item_size as ITEM_SIZE, a.item_group_id as ITEM_GROUP_ID, a.sub_group_name as SUB_GROUP_NAME, a.unit_of_measure as UOM, b.item_name as ITEM_NAME, sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in (2,3,6) then c.cons_quantity else 0 end)) as BALANCE_QNTY, c.store_id as STORE_ID
	from product_details_master a, lib_item_group b, inv_transaction c
	where a.item_group_id=b.id and c.prod_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category_id in (".implode(',',array_keys($general_item_category)).") $search_cond $item_description_cond 
	group by a.company_id, a.item_category_id, a.item_description, a.item_code, a.item_size, a.item_group_id, a.sub_group_name, a.unit_of_measure, b.item_name, c.store_id";
	//echo $sql;//die;
	$sql_result=sql_select($sql);
	$data_array=$com_data_array=$com_array=$com_store_array=array();
	foreach($sql_result as $row)
	{
		$key=$row['ITEM_CODE'].'_'.$row['ITEM_CATEGORY_ID'].'_'.$row['ITEM_GROUP_ID'].'_'.$row['SUB_GROUP_NAME'].'_'.$row['ITEM_DESCRIPTION'].'_'.$row['ITEM_SIZE'].'_'.$row['UOM'];
		$data_array[$key]['ITEM_CODE']=$row['ITEM_CODE'];
		$data_array[$key]['ITEM_CATEGORY_ID']=$row['ITEM_CATEGORY_ID'];
		$data_array[$key]['ITEM_GROUP_ID']=$row['ITEM_GROUP_ID'];
		$data_array[$key]['SUB_GROUP_NAME']=$row['SUB_GROUP_NAME'];
		$data_array[$key]['ITEM_NAME']=$row['ITEM_NAME'];
		$data_array[$key]['ITEM_DESCRIPTION']=$row['ITEM_DESCRIPTION'];
		$data_array[$key]['ITEM_SIZE']=$row['ITEM_SIZE'];
		$data_array[$key]['UOM']=$row['UOM'];

		$com_array[$row['COMPANY_ID']]=$row['COMPANY_ID'];
		$com_store_array[$row['COMPANY_ID']][$row['STORE_ID']]=$row['STORE_ID'];
		$com_data_array[$key][$row['COMPANY_ID']][$row['STORE_ID']]=$row['BALANCE_QNTY'];
	}
	$store_count=0;
	foreach($com_store_array as $company_id)
	{
		foreach($company_id as $val)
		{
			$store_count++;
		}
	}
	$table_width=750+$store_count*100;
	ob_start();	
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}	
	</style>
	<div style="width: <? echo $table_width; ?>px">
		<table width="<?= $table_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1"> 
			<thead>
				<tr>
					<th width="30" rowspan="3">SL</th>
					<th rowspan="2" colspan="7">Description</th>				
					<th colspan="<?=$store_count;?>">Unit and store wise stock</th>
					<th rowspan="3">Item Wise Total Stock</th>                  
				</tr>
				<tr>
					<?
						foreach($com_array as $row)
						{
							?>
								<th colspan="<?=count($com_store_array[$row]);?>"><?=$lib_company_arr[$row];?></th>
							<?
						}
					?>
				</tr>
				<tr>
					<th width="70">Item Code</th>
					<th width="90">Item Category</th>
					<th width="100">Item Group</th>
					<th width="80">Item Sub-group</th>
					<th width="150">Item Description</th>
					<th width="60">Item Size</th>
					<th width="50">UOM</th>
					<?
						foreach($com_array as $row)
						{
							foreach($com_store_array[$row] as $val)
							{
								?>
									<th width="100"><?=$store_name_arr[$val];?></th>
								<?
							}
						}
					?>
				</tr>
			</thead>
		</table>  
		<div style="width:<?= $table_width+20;?>px; overflow-y:scroll; max-height:250px" id="scroll_body" align="center"> 
			<table width="<?= $table_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"> 
				<!-- <tbody> -->
					<?
						$i=1;
						foreach($data_array as $key=>$val)
						{
							$total_stock=0;
							?>
								<tr>
									<td width="30" class="wrd_brk center"><?= $i; ?></td>
									<td width="70" class="wrd_brk">&nbsp;<? echo $val['ITEM_CODE'];?></td>
									<td width="90" class="wrd_brk"><? echo $general_item_category[$val['ITEM_CATEGORY_ID']];?></td>
									<td width="100" class="wrd_brk"><? echo $group_name_arr[$val['ITEM_GROUP_ID']];?></td>
									<td width="80" class="wrd_brk"><? echo $val['SUB_GROUP_NAME'];?></td>
									<td width="150" class="wrd_brk"><? echo $val['ITEM_DESCRIPTION'];?></td>
									<td width="60" class="wrd_brk">&nbsp;<? echo $val['ITEM_SIZE'];?></td>
									<td width="50" class="wrd_brk center"><? echo $unit_of_measurement[$val['UOM']];?></td>
									<?
										foreach($com_array as $row)
										{
											foreach($com_store_array[$row] as $val)
											{
												?>
													<td width="100" class="wrd_brk right">
														<?
															echo $com_data_array[$key][$row][$val];
															$total_stock+=$com_data_array[$key][$row][$val];
														?>
													</td>
												<?
											}
										}
									?>
									<td class="wrd_brk right"><?echo fn_number_format($total_stock,2);?></td>
								</tr>
							<?
							$i++;
						}
					?>
				<!-- </tbody>   -->
			</table> 
		</div>
	</fieldset>	
	<?
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
	echo "$html**$filename"; 
	exit();
	 
}

?>

