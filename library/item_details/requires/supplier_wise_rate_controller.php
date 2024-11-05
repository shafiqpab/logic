<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$item_id=$_REQUEST['item_id'];
$supplier_id=$_REQUEST['supplier_id'];
$supplier_name=$_REQUEST['supplier_name'];
asort($unit_of_measurement);



if($action == "load_drop_down_item_group"){
  $queryForItemGroup = "select id, item_name from lib_item_group where item_category='$data' and status_active = 1 and is_deleted = 0 order by item_name";

  echo  create_drop_down( "cbo_item_group", 155, $queryForItemGroup,'id,item_name', 1, "-- Select Item Group --", 0, "getItemGroupUom(this.value)" );

  exit;
}

if($action == "load_drop_down_search_item_group"){
  $queryForItemGroup = "select id, item_name from lib_item_group where item_category='$data' and status_active = 1 and is_deleted = 0 order by item_name";

  //echo  create_drop_down( "search_item_group", 155, $queryForItemGroup,'id,item_name', 1, "-- Select Item Group --",0, "getItemDescription(this.value)" );
  echo  create_drop_down( "search_item_group", 155, $queryForItemGroup,'id,item_name', 1, "-- Select Item Group --",0, "clearItemDescription()" );

  exit;
}
if($action == "openpopup_item_description"){
	echo load_html_head_contents("Item Description Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <script>
        var selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'item_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if(document.getElementById('check_all').checked){
				for( var i = 1; i <= tbl_row_count; i++ ) {
				document.getElementById( 'search'+i ).style.backgroundColor = 'yellow';
				if( jQuery.inArray( $('#txtdescription_' + i).val(), selected_name ) == -1 ) {
					selected_name.push($('#txtdescription_' + i).val());

				}

				}
                var trimgroupdata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    trimgroupdata += selected_name[i] + '**';
                }
                trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );
                $('#itemdescription').val( trimgroupdata );
			}else{
				for( var i = 1; i <= tbl_row_count; i++ ) {
					if(i%2==0  ){
						document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
					}
					if(i%2!=0 ){
						document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';
					}
					for( var j = 0; j < selected_name.length; j++ ) {
							if( selected_name[j] == $('#txtdescription_' + i).val() ) break;
					}
					selected_name.splice( j,1 );
				}
                var trimgroupdata='';
                for( var i = 0; i < selected_name.length; i++ ) {
                    trimgroupdata += selected_name[i] + '**';
                }
                trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );
                $('#itemdescription').val( trimgroupdata );

			}

		}

		function toggle( x, origColor) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;

			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}


		function js_set_value( str) {
			var tbl_row_count = document.getElementById( 'item_table' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			if($("#search"+str).css("display") !='none'){
				if(str%2==0  ){
					toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				}
				if(str%2!=0 ){
					toggle( document.getElementById( 'search' + str ), '#E9F3FF');
				}
				if( jQuery.inArray( $('#txtdescription_' + str).val(), selected_name ) == -1 ) {
					selected_name.push($('#txtdescription_' + str).val());
				}
				else{
					for( var i = 0; i < selected_name.length; i++ ) {
						if( selected_name[i] == $('#txtdescription_' + str).val() ) break;
					}
					selected_name.splice( i,1 );
				}
			}
			var trimgroupdata='';
			for( var i = 0; i < selected_name.length; i++ ) {
				trimgroupdata += selected_name[i] + '**';
			}
			if(selected_name.length == tbl_row_count){
				document.getElementById("check_all").checked = true;
			}
			else{
				document.getElementById("check_all").checked = false;
			}
			trimgroupdata = trimgroupdata.substr( 0, trimgroupdata.length - 1 );

			$('#itemdescription').val( trimgroupdata );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="itemdescription" name="itemdescription"/>
        <? $sql_tgroup=sql_select( "select id, item_description,item_code from lib_item_details where item_category_id='$item_category' and item_group_id = '$item_group' and is_deleted = 0 order by item_description"); ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            	<th width="40">SL</th><th width="195">Item Description</th><th width="160">Item Code</th>
            </thead>
        </table>
        <div style="width:420px; overflow-y:scroll; max-height:340px;" >
        <table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
				<?
                $i=1;
                foreach($sql_tgroup as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$str="";
					$str=$row[csf('id')].'__'.$row[csf('item_description')];
					?>
					<tr id="search<? echo $i;?>" class="itemdata" onClick="js_set_value(<? echo $i; ?>)" bgcolor="<? echo $bgcolor; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="220"><? echo $row[csf('item_description')]; ?>
                        <input type="hidden" name="txtdescription_<? echo $i; ?>" id="txtdescription_<? echo $i; ?>" value="<? echo $str ?>"/>
                        </td>
                        <td width="160"><? echo $row[csf('item_code')]; ?></td>
					</tr>
					<?
					$i++;
                }
                ?>
            </tbody>
        </table>
        </div>
        <table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
    </div>
    </body>
	<script>setFilterGrid('item_table',-1);</script>
	<!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
	</html>
	<?
	exit();
}

if($action == "load_drop_down_item_group_order_uom"){

  		$orderUomArr = sql_select("select order_uom from lib_item_group where id = $data");
  		$orderUom =  $orderUomArr[0]['ORDER_UOM'] ;

  echo  'Order UOM: '.create_drop_down( "cbo_order_uom", 85, $unit_of_measurement,'', '', '', $orderUom, "", 1 );

  exit;
}
if($action == "load_drop_down_item_group_cons_uom"){
 $orderConsArr = sql_select("select trim_uom from lib_item_group where id = $data");
  		$consUom =  $orderConsArr[0]['TRIM_UOM'] ;

  echo  'Cons UOM:  '.create_drop_down( "cbo_cons_uom", 85, $unit_of_measurement,'', '', '', $consUom, "", 1 );



  exit;
}

if($action == "load_drop_down_item_description"){

  $ids = explode(',',$data);
  $itemGroupId = $ids[0];
  $itemCategoryId = $ids[1];

  $queryForItemDescription = "select id, item_description from lib_item_details where item_category_id='$itemCategoryId' and item_group_id = '$itemGroupId' and is_deleted = 0 order by item_description";
  echo  create_drop_down( "search_item_description", 155, $queryForItemDescription,'id,item_description', 1, "-- Select--" );

  exit;
}
if($action == "save_update_delete_supplier_rate")
{
	$con = connect();
	if($db_type==0){
		mysql_query("BEGIN");
	}
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$field_array_up = "supplier_id*rate*item_group_id*update_date*effective_from*supplier_code*remarks";
	$field_del_up = "is_deleted*update_date";
	$field_array = "id,supplier_id,rate,item_category_id,item_group_id,insert_date,item_details_id,inserted_by,is_deleted,effective_from,supplier_code, remarks";
	$add_comma=0;
	$id=return_next_id( "id", "lib_supplier_wise_rate", 1 ) ;
	$data_array = explode("_",$data);
	$save_array = "";
	$total_row = count($data_array);
	$idsForDelete = array();


	$zero = 0;
	foreach ($data_array as $key => $value) {
		$supplier_data = explode("*",$value);
		$rowsToDeleteArr = explode(',', $supplier_data[7]);

		foreach ($rowsToDeleteArr as $key => $rowToDelete) {
			if($rowToDelete != 0 && $rowToDelete != ''){
				$idsForDelete[] = $rowToDelete;
				$del_array_up[str_replace("'",'',$rowToDelete)] = explode("*",(" 1*'".$pc_date_time."' "));
			}
		}
		//echo $supplier_data[0]; die;
		if($supplier_data[0] != 0)
			{
				$effective_date =  date('d-M-Y h:i:s',strtotime($supplier_data[3]));
				$srQuery = "select id, effective_from from lib_supplier_wise_rate where id = '$supplier_data[0]'";
				$srToUpdate = sql_select($srQuery);
				$effective_from_db = date('d-M-Y h:i:s',strtotime($srToUpdate[0]['EFFECTIVE_FROM']));
				//echo $effective_date .'=='. $effective_from_db; die;
				if($effective_date == $effective_from_db){
					$id_arr[]=$supplier_data[0];
					$data_array_up[str_replace("'",'',$supplier_data[0])] = explode("*",("".$supplier_data[1]."*".$supplier_data[2]."*".$supplier_data[5]."*'".$pc_date_time."'*'".$effective_date."'*'".$supplier_data[8]."'*'".$supplier_data[9]."' "));
				}else{

					if ($add_comma!=0) $save_array .=",";
					$save_array .="(".$id.",".$supplier_data[1].",".$supplier_data[2].",".$supplier_data[4].",".$supplier_data[5].",'".$pc_date_time."',".$supplier_data[6].",".$_SESSION['logic_erp']['user_id'].",".$zero.",'".$effective_date."','".$supplier_data[8]."','".$supplier_data[9]."')";
					$id=$id+1;
					$add_comma++;
				}


			}
			//var_dump($data_array_up); die;

			if($supplier_data[0] == 0)
			{
				$effective_date =  date('d-M-Y h:i:s',strtotime($supplier_data[3]));
				if ($add_comma!=0) $save_array .=",";
				$save_array .="(".$id.",".$supplier_data[1].",".$supplier_data[2].",".$supplier_data[4].",".$supplier_data[5].",'".$pc_date_time."',".$supplier_data[6].",".$_SESSION['logic_erp']['user_id'].",".$zero.",'".$effective_date."','".$supplier_data[8]."','".$supplier_data[9]."')";
				$id=$id+1;
				$add_comma++;
			}

	}
	//var_dump($data_array_up);die;
	$rID_del=execute_query(bulk_update_sql_statement( "lib_supplier_wise_rate", "id", $field_del_up, $del_array_up, $idsForDelete ));

	$rID_up=execute_query(bulk_update_sql_statement( "lib_supplier_wise_rate", "id", $field_array_up, $data_array_up, $id_arr));
	//echo $save_array; die;
	 if($save_array !="")
	 {
	 	//echo "INSERT into lib_supplier_wise_rate ($field_array) values $save_array"; die;
		 $rID=sql_insert("lib_supplier_wise_rate ",$field_array,$save_array,0);
	 }
	 if($db_type==2 || $db_type==1 )
		{
			if($rID_up || $rID || $rID_del){
				oci_commit($con);
				echo "1**".$rID_up."_".$rID."_".$rID_del;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID_up."_".$rID;;
			}
		}
		disconnect($con);
		die;

}

if($action == 'rate_popup'){
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$suppliersRateHistory = sql_select("select sr.id, sr.effective_from, sr.rate, sr.insert_date ,sr.is_deleted,sr.supplier_code from lib_supplier_wise_rate sr where sr.item_details_id = $item_id and sr.supplier_id = $supplier_id order by sr.effective_from asc");

	// var_dump($suppliersRateHistory);die;
?>

</head>

	<body>

		 <table id="tbl_supplier_rate_history" cellspacing="0" width="400" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">

		 	<thead>
		 		<tr>
                 	<th width="100">Supplier Code</th>
                	<th width="50">Rate($)</th>
                	<th width="120">Insert Date</th>
                	<th width="120">Effective From</th>

            </tr>
		 	</thead>

		 		<tbody>
		 		<?php if (count($suppliersRateHistory) > 0): ?>
		 			<?php foreach ($suppliersRateHistory as $key => $suplier_rate_history): ?>

		 				<tr>
		 					<td style="text-align: right;">
		 						<? echo  $suplier_rate_history[csf('supplier_code')] ?>
		 					</td>
		 					<td style="text-align: right;">
		 						<? echo  $suplier_rate_history['RATE'] ?>
		 					</td>
		 					<td style="text-align: center;">
		 						<? echo  $suplier_rate_history['INSERT_DATE']? date('d-M-Y', strtotime($suplier_rate_history['INSERT_DATE'])): ''; ?>
		 					</td>
		 					<td style="text-align: center;">
		 						<? echo  $suplier_rate_history['EFFECTIVE_FROM']?date('d-M-Y', strtotime($suplier_rate_history['EFFECTIVE_FROM'])): ''; ?>
		 					</td>


		 				</tr>
		 			<?php endforeach?>
		 		<?php else: ?>

		 			<h4 style="text-align: center; color: red;">

		 				There is no selected supplier or no history for it.
		 			</h4>

		 		<?php endif?>
		 		</tbody>

		 </table>

	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?

}


if ($action=="supplier_rate_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
	$query_for_supplier_rate = "select sr.id, sr.effective_from, sr.rate, sr.insert_date,sr.supplier_code,sr.remarks, supplier.supplier_name, supplier.id supplier_id, item_description.id item_id from lib_supplier_wise_rate sr join lib_supplier supplier on supplier.id = sr.supplier_id join lib_item_details item_description on sr.item_details_id = item_description.id where sr.item_details_id = $item_id and sr.is_deleted=0 order by supplier.supplier_name, sr.id desc";
	//echo $query_for_supplier_rate; die;

	$item_wise_supplier_rates = sql_select($query_for_supplier_rate);
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name", "id", "supplier_name");
	//echo "select item_category_id, item_group_id,order_uom from lib_item_details where id=$item_id"; die;
	//$item_details = sql_select("select item_category_id, item_group_id,order_uom from lib_item_details where id=$item_id");

	$item_details = sql_select("select lib_item_details.item_category_id, lib_item_details.item_group_id,lib_group.order_uom from lib_item_details join lib_item_group lib_group on lib_group.id = lib_item_details.item_group_id  where lib_item_details.id=$item_id");

	$item_category_id = $item_details[0][csf('item_category_id')];
	$item_group_id = $item_details[0][csf('item_group_id')];
	$order_uom = $item_details[0][csf('order_uom')];
	$categoryWisePrtyType = array(
									"3" => "(9)",
									"4" => "(1,4,5)",
									"11" => "(8)",
									"57" => "(23)",
									"23" => "(3)"
							);
	$queryForsuppliersForItemCategory = "select id, supplier_name from lib_supplier where id in (select supplier_id from lib_supplier_party_type where party_type in $categoryWisePrtyType[$item_category_id]) and status_active=1 and is_deleted=0 order by supplier_name asc";

?>



</head>

<body>
<div align="center" style="width:100%;">
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table id="tbl_supplier_rate" cellspacing="0" width="730" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>

            <tr>
                 <th width="120">Supplier Name</th>
                 <th width="70">Supplier Code</th>
                <th width="50">Rate($)</th>
                <th width="60">Order Uom</th>
                <th width="100">Effective From</th>
                <th width="150">Remarks</th>
                 <th width="70">&nbsp;</th>

            </tr>
        </thead>
        <tbody>

        	<?php

$supp_ids = array();
$totalRow = 0;
// var_dump($item_wise_supplier_rates); die;
$row = 0;
foreach ($item_wise_supplier_rates as $key => $supplier_rate):
	$row++;

	?><tr id="row_<? echo $row;  ?>" class="row_supplier">
															<td>
																<?	echo create_drop_down( "suppliername_".$row, 120,$queryForsuppliersForItemCategory,"id,supplier_name", '1', '---- Select ----', $supplier_rate[csf('supplier_id')], "" );?><input type="hidden" id="supplierwiserate_<? echo $row; ?>" value="<? echo $supplier_rate[csf('id')] ?>" name="">
															</td>
															<td width="60">
																<input style="width: 60px" id="supplierCode_<? echo $row; ?>"  type="text" class="text_boxes" name="supplier_code_<? echo $row; ?>" value="<? echo $supplier_rate[csf('supplier_code')]; ?>">
															</td>
															<td width="50">
																<input style="width: 50px" id="rate_<? echo $row; ?>"  type="text" class="text_boxes" name="rate_<? echo $row; ?>" value="<? echo $supplier_rate[csf('rate')]; ?>">
															</td>
															<td width="20">
																<input style="text-align:center; width: 30px" id="uom_<? echo $row; ?>"  type="text" class="text_boxes" name="uom_<? echo $row; ?>" value="<? echo $unit_of_measurement[$order_uom];?>" disabled >
															</td>
															<td>
																<input readonly="readonly"  id="effectivedate_<? echo $row; ?>" type="text" class="datepicker" name="effectivedate_<? echo $row; ?>" value="<? echo isset($supplier_rate['EFFECTIVE_FROM'])?date('d-m-Y', strtotime($supplier_rate['EFFECTIVE_FROM'])):''; ?>">
															</td>
															<td width="150">
																<input style="width: 150px" id="remarks_<? echo $row; ?>"  type="text" class="text_boxes" name="remarks_<? echo $row; ?>" value="<? echo $supplier_rate[csf('remarks')]; ?>">
															</td>
															<td>
																<input type="button" id="addbtn_<? echo $row; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_supplier_rate(<? echo $item_id; ?>, <? echo $row; ?> )" name="addbtn_<? echo $row; ?>"/>
																<input id="cancelbtn_<? echo $row; ?>" type="button" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $row; ?> ,'tbl_supplier_rate', this);"  name="cancelbtn_<? echo $row; ?>" />
																<!-- <input type="button" id="viewhistory_<? echo $row; ?>" onClick="viewHistory('<? echo $row; ?>')" name="" style="" class="formbutton" value="view"> -->
															</td>
														</tr>
																<?php
	//}
endforeach?>
<? if(count($item_wise_supplier_rates)<1){
	$totalRow = 1;
?>
<tr id="row_1" class="row_supplier">
   				<td>
   					<?
       					echo create_drop_down( "suppliername_1", 120,$queryForsuppliersForItemCategory,"id,supplier_name", '1', '---- Select ----', "", "" );
       				?>
       				<input type="hidden" id="supplierwiserate_1" value="0" name="">
   				</td>
   				<td width="60">
       				<input style="width: 60px" id="supplierCode_1"  type="text" class="text_boxes" name="supplier_code_1" >
       			</td>
   				<td width="60">
   					<input style="width: 60px" id="rate_1" type="text" class="text_boxes" name="rate_1" value="0">
   				</td>
   				<td width="20"><input style="text-align:center; width: 30px" id="uom_1"  type="text" class="text_boxes" name="uom_1" value="<? echo $unit_of_measurement[$order_uom];?>" disabled ></td>
   				<td width="50">
   					<input readonly="readonly"  id="effectivedate_1" type="text" class="datepicker"  name="effectivedate_1" >
   				</td>
   				<td width="150">
       				<input style="width: 150px" id="remarks_1"  type="text" class="text_boxes" name="remarks_1" >
       			</td>
   				<td>
		<input type="button" id="addbtn_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_supplier_rate(<? echo $item_id; ?>, 1 )" name = "addbtn_1"/>
        <input id="cancelbtn_1" type="button" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1 ,'tbl_supplier_rate', this );" name="cancelbtn_1" />
        <!-- <input type="button" id="viewhistory_1" onClick="viewHistory(1)" name="" style="" class="formbutton" value="view"> -->



	</td>


        		       			</tr>

        		<?}
        	?>


       </tbody>

    </table>

    <table>
    	<tr >
    		<td>
    			<input type="hidden" id="item_id" value="<? echo $item_id; ?>"  name="item_id">
    		</td>
    		<td>
    			<input type="hidden" id="item_cat_id" value="<? echo $item_category_id; ?>"  name="item_cat_id">
    		</td>
    		<td>
    			<input type="hidden" id="item_group_id" value="<? echo $item_group_id; ?>"  name="item_group_id">
    		</td>
    		<td>
    			<input type="hidden" id="add_row" name="">
    			<input type="hidden" id="supplier_ids" name="">
    			<input type="hidden" id="deleted_db_ids" name="">
    			<input type="hidden" id="supplier_rate_row_num" value="<? echo $totalRow; ?>"  name="supplier_rate_row_num">
    		</td>
    	</tr>
    </table>

    <table>
    		<tr>
        		<td></td>
        		<td>
        			<input type="button" class="formbutton" style="width: 80px;" value="Save" name="" onClick="addOrUpdateSupplierRate()">
        		</td>
        		<td></td>
        	</tr>

    </table>
    <div id="search_div" align="center"></div>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">

	var tableRows = $("#tbl_supplier_rate tr").length-1;
	// alert(row_num);

	function viewHistory(row){

		var supplierId = $('#suppliername_'+row).val();
		var supplierName = $('#suppliername_'+row + ' :selected').text();
		var itemId = $('#item_id').val();

		openmypage('supplier_wise_rate_controller.php?action=rate_popup&supplier_id='+supplierId + '&item_id=' + itemId+ '&supplier_name=' + supplierName,supplierName);
		// alert(supplierId);
	}
	function openmypage(page_link, title){
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose = function(){}
	}
	function addOrUpdateSupplierRate(){

			var countSupplierRow = $('.row_supplier').length;
			var supplierRateIds = '';

			$('.row_supplier').each(function(index, value){

				supplierRateIds += (($(this).attr('id')).split('_'))[1]

				if(index < (countSupplierRow - 1)){
					supplierRateIds += ','
				}

				$('#supplier_ids').val(supplierRateIds);
		   });

		parent.emailwindow.hide();
	}

	function add_break_down_tr_supplier_rate__( num_tbl ){

	var row_num = $("#tbl_supplier_rate tr").length-1;
	// alert(row_num);
	 var totalRow = row_num+1;


		$("#tbl_supplier_rate tr:last").clone().find("input,select").each(function() {
			$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ (totalRow);},
				'name': function(_, name) {var name = name.split("_"); return name[0] +"_"+ totalRow;},
				'value': function(_, value) {
					var inputPartialId = $(this).attr('id').split("_")[0];
						if(inputPartialId == 'addbtn' ||  inputPartialId == 'cancelbtn'){

							return value;
						}
						if(inputPartialId == 'effectivedate'){

							return '';
						}

				 		return 0;
				}
			});
		}).end().appendTo("#tbl_supplier_rate");

			$('#effectivedate_'+ totalRow ).removeClass("hasDatepicker");

			$('#rate_'+ totalRow ).removeAttr("data-rateid");
		 // $('#increaseset_'+ (row_num + 1)).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
		  $('#cancelbtn_'+ totalRow ).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+ totalRow+",'tbl_supplier_rate')");

		  $('#supplier_rate_row_num').val(totalRow);
		   set_all_onclick();


// $('#suppliername_'+row).val();
// 		viewhistory_
}
 var addRow = 0;


function add_break_down_tr_supplier_rate( num_tbl, tblRow ){

	var firstAppearedTotalRows = <? echo $row == 0? 1: $row; ?>
		// var tableRows = $("#tbl_supplier_rate tr").length - 1;
		var tableRows = parseInt(firstAppearedTotalRows);
		addRow++;
		var newRowNum = addRow + tableRows;
		// alert(newRowNum);

		$("#tbl_supplier_rate tr:last").clone().find("input,select").each(function() {
			$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ (newRowNum);},
				'name': function(_, name) {var name = name.split("_"); return name[0] +"_"+ newRowNum;},
				'value': function(_, value) {
					var inputPartialId = $(this).attr('id').split("_")[0];
						if(inputPartialId == 'addbtn' ||  inputPartialId == 'cancelbtn' || inputPartialId == 'viewhistory' || inputPartialId =="uom"){
							return value;
						}
						if(inputPartialId == 'effectivedate'){
							return '';
						}
				 		return 0;
				}
			});
		}).end().appendTo("#tbl_supplier_rate");

			$("#tbl_supplier_rate tr:last").attr('id', 'row_'+ newRowNum);
			$('#effectivedate_'+ newRowNum ).removeClass("hasDatepicker");
		  	$('#cancelbtn_'+ newRowNum ).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+ newRowNum+",'tbl_supplier_rate', $('#cancelbtn_"+ newRowNum+"'))");
		  	$('#viewhistory_'+ newRowNum ).removeAttr("onClick").attr("onClick","viewHistory("+ newRowNum+")");
		  	$('#supplier_rate_row_num').val(tableRows);
		   	set_all_onclick();


}

function fn_deletebreak_down_tr__(rowNo,table_id)
{
	if(table_id=='tbl_supplier_rate')
	{
		var numRow = $('table#tbl_supplier_rate tbody tr').length;
		var totalRow = numRow - 1;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_supplier_rate tbody tr:last').remove();
		}

		$('#supplier_rate_row_num').val(totalRow);
	}
}

var deletedIds = [];
var idsToDelete = "";
function fn_deletebreak_down_tr(rowNo,table_id, rowToCancel)
{


	if(table_id=='tbl_supplier_rate')
	{
		var numRow = $('table#tbl_supplier_rate tbody tr').length;
		var totalRow = numRow - 1;
		// if(numRow==rowNo && rowNo!=1)

		if(numRow>1)
		{
			deletedIds.push($('#supplierwiserate_'+ rowNo).val());
			idsToDelete += deletedIds[deletedIds.length - 1] + ",";
			// $('#tbl_supplier_rate tbody tr:last').remove();
			// $('#tbl_supplier_rate tbody tr:nth-child('+rowNo+')').remove();
			 $('#row_'+rowNo).remove();

		}

		$('#deleted_db_ids').val(idsToDelete);
		$('#supplier_rate_row_num').val(totalRow);

	}
}
</script>
</html>
<?
}

if($action == "get_search_rate"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	 $search_item_category; $search_item_group; $hidden_item_description;
	// echo $search_item_description. ' '.$search_item_category. ' '. $search_item_group. ' '.$search_item_description; die();
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name", "id", "supplier_name");
	$serch_item_library = return_library_array( "select id, item_description from lib_item_details where id in (".str_replace("'","",$hidden_item_description).") and is_deleted=0 order by item_description", "id", "item_description");
	$query_for_supplier_rate = "select sr.id, sr.effective_from, sr.rate, sr.insert_date,sr.supplier_code, sr.remarks, supplier.supplier_name, supplier.id supplier_id, item_description.id item_id,item_description.order_uom as uom, lib_item_group.order_uom from lib_supplier_wise_rate sr join lib_supplier supplier on supplier.id = sr.supplier_id join lib_item_details item_description on sr.item_details_id = item_description.id left outer join lib_item_group on sr.item_group_id=lib_item_group.id where sr.item_details_id in (".str_replace("'","",$hidden_item_description).") and sr.item_category_id = $search_item_category and sr.is_deleted=0 and sr.item_group_id = $search_item_group  order by supplier.supplier_name, sr.id desc";
	//echo $query_for_supplier_rate; die;

	$item_wise_supplier_rates = sql_select($query_for_supplier_rate);
	$i = 1;
  ?>
    <fieldset >
      <legend>List View</legend>
<?
	$get_unique_supp_ids = array(); // needed for getting a particular supplier's last rate.

	foreach ($serch_item_library as $search_item_id => $search_item_value) {?>

		<input id="submit" class="formbutton" value="Edit" name="submit" onclick="updateSupplierRate('<? echo $search_item_id ?>', '<? echo $search_item_value ?>')" style="width:60px; float:right" type="button">

      <div style="float:left; font-weight:bold;"><span style="color: #444; ">Item Description:</span> <span style="color:Blue;"><? echo  $search_item_value;?></span></div>

      <? $supplierForItemArr = array(); // needed for displaying a table for an item having suppliers' rates. Otherwise, a message will be displayed. ?>

      <?php foreach ($item_wise_supplier_rates as $supplier_rate): ?>

      	<?php if ($search_item_id == $supplier_rate[csf('item_id')]): ?>

      		<? $supplierForItemArr[$search_item_id][] = $supplier_rate[csf('supplier_id')];?>

      	<?php endif?>

      <?php endforeach?>

      	<?php if (count($supplierForItemArr[$search_item_id]) > 0): ?>

      		 <table class="rpt_table" rules="all" id="tbl_supplier_rate_<? echo $search_item_id;?>"  cellpadding="0" border="0" width="700" align="center">
          <thead>
            <tr>
                <th width="150">Supplier Name</th>
                <th width="50">Supplier Code</th>
               <th width="50">Rate($)</th>
               <th width="50">Order Uom</th>
                <th width="100">Insert Date</th>
                <th width="100">Effective from</th>
                <th width="180">Remarks</th>
                <th>&nbsp;</th>
           </tr>
          </thead>
        <table class="rpt_table" rules="all" id="list_supplier_rate_<? echo $search_item_id;?>"  cellpadding="0" border="0" width="700" align="center">
            <tbody style="max-height:200px;display:block;overflow:scroll">
            	<?
            	foreach ($item_wise_supplier_rates as  $supplier_rate) {
            		if($search_item_id == $supplier_rate[csf('item_id')] && !in_array($supplier_rate[csf('supplier_id')], $get_unique_supp_ids[$search_item_id])){
								 //$get_unique_supp_ids[$search_item_id][] = $supplier_rate[csf('supplier_id')];

            				?>
            			<tr>
			                  <td width="150"><? echo $supplier_rate[csf('supplier_name')]?></td>
			                  <td width="50"><? echo $supplier_rate[csf('supplier_code')]?></td>
			                  <td width=50 align="right"><? echo $supplier_rate[csf('rate')]?></td>
			                  <td width="50" align="right"><?echo $unit_of_measurement[$supplier_rate[csf('order_uom')]]; ?></td>
			                  <td width="100" align="center"><? echo date('d-m-Y', strtotime($supplier_rate[csf('insert_date')]))?></td>
			                  <td width="100"><? echo $supplier_rate[csf('effective_from')]?date('d-m-Y', strtotime($supplier_rate[csf('effective_from')])): 'Effective date not given'; ?>
			                  </td>
			                  <td width="180"><? echo $supplier_rate[csf('remarks')]?></td>

              			</tr>

            			<?}
            		}

            	?>

            </tbody>
        </table>
        <br>
        <? $table_id = 'list_supplier_rate_'.$search_item_id.'';?>
        <script type="text/javascript">
		setFilterGrid('<?php echo $table_id ?>',-1);
		</script>
    	<?php else: ?>
			<br><br>
    			<h4 style="margin-left: 150px; color: red;">There are no suppliers' rates for this item.</h4>
    		<br>

      	<?php endif?>


<?
	}
?>

    </fieldset>
  <?
}

if ($action=="load_php_data_to_form") {
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$data = sql_select("select lib_item_details.id,lib_item_details.item_category_id,lib_item_details.item_description,lib_item_details.item_code, lib_item_details.item_group_id, lib_item_group.trim_uom as cons_uom, lib_item_group.order_uom,lib_item_details.tag_buyer from lib_item_details left outer join lib_item_group on lib_item_group.id = lib_item_details.item_group_id where lib_item_details.id=$data and lib_item_details.is_deleted=0");
	foreach ($data as $key => $value) {
		$description_id=$value[csf("id")];
		$group_id = $value[csf('item_group_id')];
		$cat_id= $value[csf("item_category_id")];
		$suppliers_rate_by_group=sql_select("select supplier_id from lib_supplier_wise_rate where item_group_id=$group_id and is_deleted=0 and item_category_id=$cat_id and item_details_id=$description_id");
		$buyer_name='';
		$buyer_id_array=explode(",",$value[csf("tag_buyer")]);
		foreach($buyer_id_array as $val)
		{
			if($buyer_name=="") $buyer_name=$buyer_library[$val]; else $buyer_name.=",".$buyer_library[$val];
		}

		echo "load_drop_down( 'requires/supplier_wise_rate_controller','".$value[csf("item_category_id")]."', 'load_drop_down_item_group', 'td_item_group') ;";
		echo "document.getElementById('update_id').value  = '".($value[csf("id")])."';\n";
		echo "document.getElementById('cbo_item_category').value  = '".($value[csf("item_category_id")])."';\n";
		echo "document.getElementById('cbo_item_group').value  = '".($value[csf("item_group_id")])."';\n";
		echo "document.getElementById('txt_item_description').value  = '".($value[csf("item_description")])."';\n";
		echo "document.getElementById('cbo_order_uom').value  = '".($value[csf("order_uom")])."';\n";
		echo "document.getElementById('cbo_cons_uom').value  = '".($value[csf("cons_uom")])."';\n";
		echo "document.getElementById('txt_item_code').value  = '".($value[csf("item_code")])."';\n";
		echo "document.getElementById('txt_tag_buyer_id').value  = '".($value[csf("tag_buyer")])."';\n";
		echo "document.getElementById('cbo_tag_buyer').value  = '".$buyer_name."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_item_details',1);\n";
		if(count($suppliers_rate_by_group) > 0){
			echo "$('#cbo_item_group').attr('disabled',true);\n";
			echo "$('#cbo_item_category').attr('disabled',true);\n";
		}
		else{
			echo "$('#cbo_item_group').attr('disabled',false);\n";
			echo "$('#cbo_item_category').attr('disabled',false);\n";
		}
	}
	exit;
}
if($action=="buyer_name_popup")
{
	echo load_html_head_contents("Party Type Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $party_type_id;
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function toggle( x, origColor) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;
            }
        }

		function check_all_data()
		{
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length-1;
            tbl_row_count = tbl_row_count;
			//alert(tbl_row_count);

            if(document.getElementById('check_all').checked)
			{
                for( var i = 1; i <= tbl_row_count; i++ ) {
	                document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
	                if( jQuery.inArray( $('#txt_individual_id' + i).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + i).val() );
						selected_name.push( $('#txt_individual' + i).val() );
					}
                }

                var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#hidden_buyer_id').val(id);
				$('#hidden_buyer_name').val(name);
            }
			else
			{				
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    if(i%2==0 ) document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
                    else document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';

					for( var j = 0; j < selected_id.length; j++ ) {
                        if( selected_id[j] == $('#txt_individual_id' + i).val() ) break;
                    }
                    selected_id.splice( j,1 );
                }

				var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#hidden_buyer_id').val(id);
				$('#hidden_buyer_name').val(name);
            }
        }

		function js_set_value( str) 
		{
        	var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count-1;
            if ($("#search"+str).css("display") !='none')
			{
                if ( str%2==0 ) toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				else toggle( document.getElementById( 'search' + str ), '#E9F3FF');

				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
				}
				else
				{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
            }

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidden_buyer_id').val(id);
			$('#hidden_buyer_name').val(name);

            if (selected_id.length == tbl_row_count) document.getElementById("check_all").checked = true;
			else document.getElementById("check_all").checked = false;
        }

		function set_all()
        {
            var old=document.getElementById('txt_party_row_id').value; 
            if(old!="")
            {   
                old=old.split(",");
                for(var k=0; k<old.length; k++)
                {   
                    js_set_value( old[k] )
                } 
            }
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			if (old.length == tbl_row_count) document.getElementById("check_all").checked = true;
			else document.getElementById("check_all").checked = false;
        }
	</script>	
	</head>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
		    	<input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">
		        <input type="hidden" name="hidden_buyer_name" id="hidden_buyer_name" class="text_boxes" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
		                <thead>
		                    <th width="50">SL</th>
		                    <th>Buyer Name</th>
		                </thead>
		            </table>
		            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
		                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
		                <?
		                    $i=1; $party_row_id=''; 
							$hidden_party_id=explode(",",$txt_tag_buyer_id);
							$sql_buyer=sql_select("SELECT DISTINCT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active = 1 AND buy.is_deleted = 0 AND b.buyer_id = buy.id AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (1, 3,20, 21, 90)) ORDER BY buyer_name");
		                    foreach($sql_buyer as $row_buyer)
		                    {
								
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if(in_array($row_buyer[csf('id')],$hidden_party_id)) 
									{ 
										if($party_row_id=="") $party_row_id=$i; else $party_row_id.=",".$i;
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
										<td width="50" align="center"><?php echo "$i"; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row_buyer[csf('id')]; ?>"/>	
											<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row_buyer[csf('buyer_name')]; ?>"/>
										</td>	
										<td><p><? echo $row_buyer[csf('buyer_name')]; ?></p></td>
									</tr>
									<?
									$i++;
						
		                    }
		                ?>
		                <input type="hidden" name="txt_party_row_id" id="txt_party_row_id" value="<?php echo $party_row_id; ?>"/>
		                </table>
		            </div>
		             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
		                <tr>
		                    <td align="center" height="30" valign="bottom">
		                        <div style="width:100%"> 
		                            <div style="width:50%; float:left" align="left">
		                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
		                            </div>
		                            <div style="width:50%; float:left" align="left">
		                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
		                            </div>
		                        </div>
		                    </td>
		                </tr>
		            </table>
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
  // echo 2; die();
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here=======================================================
	{
		$con = connect();
		if($db_type==0)
		{
		mysql_query("BEGIN");
		}

		$duplicate = is_duplicate_field("id","lib_item_details","item_category_id=$cbo_item_category and item_group_id=$cbo_item_group and item_description=$txt_item_description");
		if($duplicate==1)
		{
			echo "11**duplicate";
			disconnect($con);
			die;
		}

		$id=return_next_id("id","lib_item_details",1);
		$field_array="id,item_category_id,item_description,item_group_id,order_uom,cons_uom,insert_date,inserted_by,is_deleted,item_code,tag_buyer";
		$data_array="(".$id.",".$cbo_item_category.",".$txt_item_description.",".$cbo_item_group.",".$cbo_order_uom.",".$cbo_cons_uom.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",0,".$txt_item_code.",".$txt_tag_buyer_id.")";
		//echo $data_array;die;
		$rID=sql_insert("lib_item_details",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$cbo_item_category);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id);
			}
		}

		if($db_type==2 || $db_type==1 )
			{
			    if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'",'',$cbo_item_category);
					}
				else{
						oci_rollback($con);
						echo "10**".$rID;
					}
			}

		disconnect($con);
		die;
	}

	else if ($operation == 1)   // Update Here==========================================================================================
	{

		$con = connect();
		if($db_type==0)
		{
		mysql_query("BEGIN");
		}

		/*$duplicate = is_duplicate_field("id","lib_item_details","item_category_id=$cbo_item_category and item_group_id=$cbo_item_group and item_description=$txt_item_description");
		if($duplicate==1)
		{
			echo "11**Duplicate Entry is Not Allowed for Same Item Description.";
			die;
		}*/

		$field_array="item_category_id*item_description*item_group_id*order_uom*cons_uom*update_date*updated_by*is_deleted*item_code*tag_buyer";
		$data_array="".$cbo_item_category."*".$txt_item_description."*".$cbo_item_group."*".$cbo_order_uom."*".$cbo_cons_uom."*'".$pc_date_time."'*".$_SESSION['logic_erp']['user_id']."*'0'*".$txt_item_code."*".$txt_tag_buyer_id."";
		//echo $data_array;die;
		$rID=sql_update("lib_item_details",$field_array,$data_array,"id","".$update_id."",0);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$cbo_item_category);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id);
			}
		}

		if($db_type==2 || $db_type==1 )
			{
			    if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".$update_id;
					}
				else{
						oci_rollback($con);
						echo "10**".$rID;
					}
			}

		disconnect($con);
		die;


	}

	else if ($operation==2)   // Delete Here=======================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			$field_array="updated_by*update_date*is_deleted";
	    	$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'";
			$rID=sql_delete("lib_item_details",$field_array,$data_array,"id","".$update_id."",1);

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$cbo_item_category);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id);
			}
		}

		if($db_type==2 || $db_type==1 )
			{
			    if($rID )
					{
						oci_commit($con);
						echo "2**".$rID."**".$update_id;
					}
				else{
						oci_rollback($con);
						echo "10**".$rID;
					}
			}
		disconnect($con);
		die;
	}
}

if($action == "get_item_rate_list")
{
	$itemGroup = return_library_array( "select id,item_name from lib_item_group order by item_name",'id','item_name');

					$idToDesArr =  array(0=>$itemGroup, 3=>$unit_of_measurement, 4=>$unit_of_measurement);
				  echo  create_list_view ( "list_view", "Item Group,Item Description,Item Code,Order Uom,Cons Uom", "80,100,70,50,50","420","220",0, "select lib_item_details.id, lib_item_details.item_description, lib_item_details.item_code, lib_item_details.item_group_id, lib_item_group.trim_uom as cons_uom, lib_item_group.order_uom  from lib_item_details left outer join lib_item_group on lib_item_group.id = lib_item_details.item_group_id  where lib_item_details.is_deleted=0 order by lib_item_details.item_group_id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_group_id,0,0,order_uom,cons_uom", $idToDesArr , "item_group_id,item_description,item_code,order_uom,cons_uom", "requires/supplier_wise_rate_controller", 'setFilterGrid("list_view",-1);' ) ;
	exit;
}

?>
