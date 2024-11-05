<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$item_id=$_REQUEST['item_id'];



if($action == "load_drop_down_item_group"){
  $queryForItemGroup = "select id, item_name from lib_item_group where item_category='$data' and status_active = 1 and is_deleted = 0 order by item_name";

  echo  create_drop_down( "cbo_item_group", 155, $queryForItemGroup,'id,item_name', 1, "-- Select Item Group --", 0, "" );

  exit;
}

if($action == "load_drop_down_search_item_group"){
  $queryForItemGroup = "select id, item_name from lib_item_group where item_category='$data' and status_active = 1 and is_deleted = 0 order by item_name";

  echo  create_drop_down( "search_item_group", 155, $queryForItemGroup,'id,item_name', 1, "-- Select Item Group --",0, "getItemDescription(this.value)" );

  exit;
}

if($action == "load_drop_down_item_description"){

  $ids = explode(',',$data);
  $itemGroupId = $ids[0];
  $itemCategoryId = $ids[1];

  $queryForItemDescription = "select id, item_description from lib_item_details where item_category_id='$itemCategoryId' and item_group_id = '$itemGroupId' and is_deleted = 0";
  echo  create_drop_down( "search_item_description", 155, $queryForItemDescription,'id,item_description', 1, "-- Select--" );

  exit;
}
if($action == "save_update_delete_supplier_rate")
{
	$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
	$process = array( &$_POST );	
	extract(check_magic_quote_gpc( $process ));
	// $field_array_up = "supplier_id*rate*update_date*effective_from";
	$field_array_up = "supplier_id*rate*update_date";
	// $field_array = "id,supplier_id,rate,item_category_id,item_group_id,insert_date,item_details_id,inserted_by,is_deleted,effective_from";
	$field_array = "id,supplier_id,rate,item_category_id,item_group_id,insert_date,item_details_id,inserted_by,is_deleted";
	$add_comma=0;
	$id=return_next_id( "id", "lib_supplier_wise_rate", 1 ) ;
	$data_array = explode("_",$data);
	$save_array = "";
	$total_row = count($data_array);
	

	$zero = 0;
	foreach ($data_array as $key => $value) {
		$supplier_data = explode("*",$value);
		
	
		if($supplier_data[0] != 0)
			{
				
				
				//$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
				//$effective_date =  date('d-M-Y h:i:s',strtotime($supplier_data[3]));

		// $srQuery = "select id, effective_from from lib_supplier_wise_rate where id = '$supplier_data[0]'";

		// $srToUpdate = sql_select($srQuery); 
		// $effective_from_db = date('d-M-Y h:i:s',strtotime($srToUpdate[0]['EFFECTIVE_FROM']));
				
				 
				 	$id_arr[]=$supplier_data[0];

				$data_array_up[str_replace("'",'',$supplier_data[0])] = explode("*",("".$supplier_data[1]."*".$supplier_data[2]."*'".$pc_date_time."'"));
				 // $data_array_up[str_replace("'",'',$supplier_data[0])] = explode("*",("".$supplier_data[1]."*".$supplier_data[2]."*'".$pc_date_time."'*'".$effective_date."' "));
				  
			}
			
				
			if($supplier_data[0] == 0 )
			{
				if ($add_comma!=0) $save_array .=",";
				// $save_array .="(".$id.",".$supplier_data[1].",".$supplier_data[2].",".$supplier_data[4].",".$supplier_data[5].",'".$pc_date_time."',".$supplier_data[6].",".$_SESSION['logic_erp']['user_id'].",".$zero.",'".$effective_date."')";
				$save_array .="(".$id.",".$supplier_data[1].",".$supplier_data[2].",".$supplier_data[4].",".$supplier_data[5].",'".$pc_date_time."',".$supplier_data[6].",".$_SESSION['logic_erp']['user_id'].",".$zero.")";
				$id=$id+1;
				$add_comma++;
			}

	}
	


	$rID_up=execute_query(bulk_update_sql_statement( "lib_supplier_wise_rate", "id", $field_array_up, $data_array_up, $id_arr ));
	 if($save_array !="")
	 {
	 	//var_dump($data_array);die;
		 $rID=sql_insert("lib_supplier_wise_rate",$field_array,$save_array,0);
	 }
	 if($db_type==2 || $db_type==1 )
		{
			if($rID_up || $rID){
				oci_commit($con);  
				echo "1**".$rID_up."_".$rID;
				
				/*$itemCategoryId = $supplier_data[4];
				$itemGroupId = $supplier_data[5];
				$itemId = $supplier_data[6];*/

			}
			else{
				oci_rollback($con);
				echo "10**".$rID_up."_".$rID;;
			}
		}
		disconnect($con);
		die;

}
if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
	$query_for_supplier_rate = "select sr.id, sr.effective_from, sr.rate, sr.insert_date, supplier.supplier_name, supplier.id supplier_id, item_description.id item_id from lib_supplier_wise_rate sr join lib_supplier supplier on supplier.id = sr.supplier_id join lib_item_details item_description on sr.item_details_id = item_description.id where sr.item_details_id = $item_id order by supplier.supplier_name, sr.id desc";

	$item_wise_supplier_rates = sql_select($query_for_supplier_rate);
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name", "id", "supplier_name");
	$item_details = sql_select("select item_category_id, item_group_id from lib_item_details where id=$item_id");

	$item_category_id = $item_details[0]['ITEM_CATEGORY_ID'];
	$item_group_id = $item_details[0]['ITEM_GROUP_ID'];

	
?>



</head>

<body>
<div align="center" style="width:100%;">
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table id="tbl_supplier_rate" cellspacing="0" width="480" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
           
            <tr>                	 
                <th width="120">Supplier Name</th>
                <th width="120">Rate</th>
                <th width="120">Effective From</th>
                 <th width="60">&nbsp;</th>   
            </tr>          
        </thead>
        <tbody>

        	<?php 

        		$supp_ids = array();
        		$totalRow = 0;
        		foreach ($item_wise_supplier_rates as $key => $supplier_rate): 

        		if ($supplier_rate['ITEM_ID'] == $item_id && !in_array($supplier_rate['SUPPLIER_ID'],$supp_ids)) {
        				$supp_ids[] = $supplier_rate['SUPPLIER_ID'];

        				$totalRow ++;	
        				$row =  $key + 1;
        		
        	?>


        		<tr>
	       			<td>
	       				<? 

	       					echo create_drop_down( "suppliername_".$row, 120,$supplier_library,"", '1', '---- Select ----', $supplier_rate['SUPPLIER_ID'], "" );

	       				 ?>
	       				 <input type="hidden" id="supplierwiserate_<? echo $row; ?>" value="<? echo $supplier_rate['ID'] ?>" name="">
	       			</td>
	       			<td>
	       				<input id="rate_<? echo $row; ?>"  type="text" class="text_boxes" name="rate_<? echo $row; ?>" value="<? echo $supplier_rate['RATE']; ?>">
	       			</td>
	       			<td>
	       				
	       				<input  id="effectivedate_<? echo $row; ?>" type="text" class="datepicker" name="insertdate_<? echo $row; ?>" value="<? echo date('d-m-Y', strtotime($supplier_rate['EFFECTIVE_FROM'])); ?>">
	       				
	       			</td>
	       			<td>
	       				<input type="button" id="addbtn_<? echo $row; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_supplier_rate(<? echo $item_id; ?> )" name="addbtn_<? echo $row; ?>"/>
                            <input id="cancelbtn_<? echo $row; ?>" type="button" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $row; ?> ,'tbl_supplier_rate' );"  name="cancelbtn_<? echo $row; ?>" />
	       			</td>
       			</tr>
       			
        		
        	<?php 
        			}
        	 endforeach ?>
        	<?
        		if(count($item_wise_supplier_rates)<1){

        			$totalRow = 1;
        	?>
        		        	<tr>
        		       				<td>
        		       					<? 

        			       					echo create_drop_down( "suppliername_1", 120,$supplier_library,"", '1', '---- Select ----');
        			       				?>
        			       				<input type="hidden" id="supplierwiserate_1" value="0" name="">
        		       				</td>
        		       				<td>
        		       					<input id="rate_1" type="text" class="text_boxes" name="rate_1" value="0">
        		       				</td>
        		       				<td>
        		       					<!-- <input readonly="readonly" id="insertdate_1" type="text" class="datepicker" name="insertdate_1" > -->
        		       					<input  id="effectivedate_1" type="text" class="datepicker"  name="insertdate_1" >
        		       				</td>
        		       				<td>
	       				<input type="button" id="addbtn_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_supplier_rate(<? echo $item_id; ?> )" name = "addbtn_1"/>
                            <input id="cancelbtn_1" type="button" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1 ,'tbl_supplier_rate' );" name="cancelbtn_1" />
                          
                            
		
	       			</td>
        		       				
        		       			</tr>

        		<?}
        	?>
      
       		
       </tbody>
      	
    </table>  

    <table>
    	<tr>
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

	

	function addOrUpdateSupplierRate(){

		parent.emailwindow.hide();
	}
	
	function add_break_down_tr_supplier_rate( num_tbl ){

	var row_num = $("#tbl_supplier_rate tr").length-1;
	// alert(row_num);
	 var totalRow = row_num+1;
	
	
		$("#tbl_supplier_rate tr:last").clone().find("input,select").each(function() {
			$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ (totalRow);},
				'name': function(_, name) {var name = name.split("_"); return name[0] +"_"+ totalRow;},
				'value': function(_, value) {
					var inputPartialId = $(this).attr('id').split("_")[0];
				 	return  inputPartialId == 'addbtn' ||  inputPartialId == 'cancelbtn' || inputPartialId == 'insertdate'? value: 0; 
				}          
			});
		}).end().appendTo("#tbl_supplier_rate");

			$('#effectivedate_'+ totalRow ).removeClass("hasDatepicker");
			
			$('#rate_'+ totalRow ).removeAttr("data-rateid");
		 // $('#increaseset_'+ (row_num + 1)).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
		  $('#cancelbtn_'+ totalRow ).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+ totalRow+",'tbl_supplier_rate')");
		  $('#supplier_rate_row_num').val(totalRow);
		   set_all_onclick();
			
}

function fn_deletebreak_down_tr(rowNo,table_id) 
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
</script>
</html>
<?
}

if($action == "get_search_rate"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 
	 $search_item_category; $search_item_group; $search_item_description;
	// echo $search_item_description. ' '.$search_item_category. ' '. $search_item_group. ' '.$search_item_description; die();
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name", "id", "supplier_name");
	$serch_item_library = return_library_array( "select id, item_description from lib_item_details where id in (".str_replace("'","",$search_item_description).") and is_deleted=0 order by item_description", "id", "item_description");
	$query_for_supplier_rate = "select sr.id, sr.effective_from, sr.rate, sr.insert_date, supplier.supplier_name, supplier.id supplier_id, item_description.id item_id from lib_supplier_wise_rate sr join lib_supplier supplier on supplier.id = sr.supplier_id join lib_item_details item_description on sr.item_details_id = item_description.id where sr.item_details_id in (".str_replace("'","",$search_item_description).") and sr.item_category_id = $search_item_category and sr.item_group_id = $search_item_group  order by supplier.supplier_name, sr.id desc";


	$item_wise_supplier_rates = sql_select($query_for_supplier_rate);
	$i = 1;
// $search_items = explode(',',$search_item_description);
// var_dump(str_replace("'","",$hello[0]));
	// echo $supplier_library;
  ?>
    <fieldset >
      <legend>List View</legend>
<? 
	$get_unique_supp_ids = array(); // needed for getting a particular supplier's last rate.

	foreach ($serch_item_library as $search_item_id => $search_item_value) {?>

		<input id="submit" class="formbutton" value="Edit" name="submit" onclick="updateSupplierRate('<? echo $search_item_id ?>', '<? echo $search_item_value ?>')" style="width:60px; float:right" type="button">
      <span style="float:left; margin-left: 250px; color:Blue; font-weight:bold;"><? echo  $search_item_value;?></span>
    
      <? $supplierForItemArr = array(); // needed for displaying a table for an item having suppliers' rates. Otherwise, a message will be displayed. ?>

      <?php foreach ($item_wise_supplier_rates as  $supplier_rate):  ?>

      	<?php if ($search_item_id == $supplier_rate['ITEM_ID']): ?>

      		<? $supplierForItemArr[$search_item_id][] = $supplier_rate['SUPPLIER_ID'];?>
      		
      	<?php endif ?>
      	
      <?php endforeach ?>

      	<?php if (count($supplierForItemArr[$search_item_id]) > 0): ?>

      		 <table class="rpt_table" rules="all" id="tbl_supplier_rate_<? echo $search_item_id;?>"  cellpadding="0" border="0" width="500" align="center">
          <thead>
            <tr>
               <th width=130>Supplier Name</th>
               <th width=130>Supplier Rate($)</th>
                <th width=70>Insert Date</th>
                <th width=60>Effective from</th>
           </tr>
          </thead>
            <tbody>
            	<?
					
            		foreach ($item_wise_supplier_rates as  $supplier_rate) {
            			if($search_item_id == $supplier_rate['ITEM_ID'] && !in_array($supplier_rate['SUPPLIER_ID'], $get_unique_supp_ids[$search_item_id])){
            		
								 $get_unique_supp_ids[$search_item_id][] = $supplier_rate['SUPPLIER_ID'];
        
            				?>

            			<tr>

			                  <td width=200>
			                  	<? echo $supplier_rate['SUPPLIER_NAME']?>
			                  </td>
			                  <td width=60 align="right">
			                  	<? echo $supplier_rate['RATE']?>
			                  </td>
			                  <td width=180 align="center">
			                  	<? echo date('d-m-Y H:i:s A', strtotime($supplier_rate['INSERT_DATE']))?>
			                  </td>
			                  <td width=120>
			                  	<? echo $supplier_rate['EFFECTIVE_FROM']?date('d-m-Y', strtotime($supplier_rate['EFFECTIVE_FROM'])): 'Effective date not given'; ?>
			                  </td>
              			</tr>

            			<?}
            		}

            	?>
              
            </tbody>


        </table>
        <br>

    	<?php else: ?>
			<br><br>
    			<h4 style="margin-left: 150px; color: red;">There are no suppliers' rates for this item.</h4>
    		<br>
      		
      	<?php endif ?>
      
       
<?        
	}
?>
      
    </fieldset>
  <?
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

		$duplicate = is_duplicate_field("id","item_details","item_category_id=$cbo_item_category and item_group_id=$cbo_item_group and item_description=$txt_item_description");
		if($duplicate==1)
		{
			echo "11**Duplicate Entry is Not Allowed for Same Item Description.";
			die;
		}

		$id=return_next_id("id","lib_item_details",1);
		$field_array="id,item_category_id,item_description,item_group_id,order_uom,cons_uom,insert_date,inserted_by,is_deleted";
		$data_array="(".$id.",".$cbo_item_category.",".$txt_item_description.",".$cbo_item_group.",".$cbo_order_uom.",".$cbo_cons_uom.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",0)";
		//echo $data_array;die;
		$rID=sql_insert("lib_item_details",$field_array,$data_array,1);
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

	else if ($operation==1)   // Update Here==========================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
		mysql_query("BEGIN");
		}


		  if(str_replace("'", '',$cbo_status)!=1)
		  {

			$trans_id=return_field_value("id", "inv_transaction", "prod_id=".$update_id." and status_active=1 and is_deleted=0 ","id");
			$parce_req_id=return_field_value("id", "inv_purchase_requisition_dtls", "product_id=".$update_id." and status_active=1 and is_deleted=0 ","id");
			if($trans_id!="") { echo 101;die;}
			if($parce_req_id!="") { echo 102;die;}
		  }

		if(str_replace("'","",$cbo_item_category)==4) $entry_form_lib=20; else $entry_form_lib=0;
		$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
		$productname = $item_group_arr[str_replace("'", '', $item_group_id)]." ".str_replace("'", '', $txt_description)." ".str_replace("'", '', $txt_item_size);

		$field_array="company_id*item_category_id*entry_form*sub_group_code*sub_group_name*item_code*item_description*product_name_details*item_size*re_order_label*minimum_label*maximum_label*unit_of_measure*item_account*updated_by*update_date*status_active*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_item_category."*".$entry_form_lib."*".$txt_subgroup_code."*".$txt_subgroup_name."*".$txt_item_code."*".$txt_description."*'".$productname."'*".$txt_item_size."*".$txt_reorder_label."*".$txt_min_label."*".$txt_max_label."*".$cbo_cons_uom."*".$txt_item_account."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";

		$rID=sql_update("product_details_master",$field_array,$data_array,"id","".$update_id."",1);
		//$rID=sql_update("inv_purchase_requisition_mst",$field_array,$data_array,"id",$update_id,1);

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$cbo_item_category);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
			{
			 if($rID )
			    {
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'",'',$cbo_item_category);
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

			//echo $all_received_master=("select min(a.recv_number) as recv_number,min(a.entry_form) as entry_form from  inv_receive_master a,inv_transaction b where a.id=b.mst_id and b.prod_id=$update_id  and a.status_active=1 and a.is_deleted=0 and  a.entry_form in (4)");die;
			$dyes_chemical_received_no=return_field_value("min(a.recv_number) as recv_number", "inv_receive_master a,inv_transaction b", "a.id=b.mst_id and b.prod_id=$update_id  and a.status_active=1 and a.is_deleted=0 and  a.entry_form in (4)","recv_number");
			if($dyes_chemical_received_no!="")
			{
				echo "50**Some Entries Found For This Item Account, Deleting Not Allowed, \n Dyes Chemical Recv: ".$dyes_chemical_received_no;

			}

		/*$nameArray=sql_select( "select id,product_id from inv_purchase_requisition_dtls where product_id=$update_id" );
		if($nameArray)
			{
			echo "13**";die;
			}*/

			$field_array="updated_by*update_date*status_active*is_deleted";
	    	$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$rID=sql_delete("product_details_master",$field_array,$data_array,"id","".$update_id."",1);

		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "1**".$rID."**".str_replace("'",'',$cbo_item_category)."**".str_replace("'",'',$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
			{
			 if($rID )
			    {
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'",'',$cbo_item_category)."**".str_replace("'",'',$update_id);
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



?>
