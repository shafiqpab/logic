<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if ($action=="item_group_list_view")
{
	$arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$dec_place_other_item,8=>$dec_place_other_item,9=>$cal_parameter,10=>$row_status);
	echo  create_list_view ( "list_view", "Item Catagory,Item Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Order UOM After DEC,Cons UOM After DEC,Cal Parameter,Status", "120,100,150,80,50,50,50,60,60,80","940","220",0, "select id,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,order_uom_decimal_point,cons_uom_decimal_point,cal_parameter,status_active from  lib_item_group where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,order_uom_decimal_point,cons_uom_decimal_point,cal_parameter,status_active", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,order_uom_decimal_point,cons_uom_decimal_point,cal_parameter,status_active", "../item_details/requires/item_group_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,1,1' ) ;
}

if($action=="main_group_popup")
{
  	echo load_html_head_contents("Main Group Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
    ?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidden_main_group_id').value=id;
			document.getElementById('hidden_main_group_name').value=name;
			parent.emailwindow.hide();
		}
    </script>

	</head>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_main_group_id" id="hidden_main_group_id" class="text_boxes" value="">
	        <input type="hidden" name="hidden_main_group_name" id="hidden_main_group_name" class="text_boxes" value="">
	        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Main Group Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
						<?
	                    $sql_main_group_name=sql_select("select id, main_group_name from lib_main_group where item_category_id=$cbo_item_category and status_active =1 and is_deleted=0 order by main_group_name");
	                    $i=1;                 
	                    foreach($sql_main_group_name as $row)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF";
	                        else $bgcolor="#FFFFFF";
	                        ?>
	                        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('main_group_name')]; ?>')">
	                            <td width="50" align="center"><?=$i; ?></td>
	                            <td style="word-break:break-all"><?=$row[csf('main_group_name')]; ?></td>
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
	<script>setFilterGrid('tbl_list_search',-1);</script>
	</html>
	<?
	exit();
}

if ($action=="load_php_data_to_form")
{
	
	$item_group_query= "select a.item_category_id,a.item_group_id, b.item_name from product_details_master a, lib_item_group b where a.item_group_id = b.id and b.is_deleted=0 and b.status_active=1 and b.id in($data)";
	$item_group_result = sql_select($item_group_query);
	foreach ($item_group_result as $items) {
		$item_group_arr[$items[csf("item_category_id")]]["item_category_id"] = $items[csf("item_category_id")];
		$item_group_arr[$items[csf("item_category_id")]]["item_group_id"] = $items[csf("item_group_id")];
	}
	
	$trims_group_query= "select a.trim_group, b.item_name from wo_pre_cost_trim_cost_dtls a, lib_item_group b where a.trim_group = b.id and a.is_deleted=0 and b.status_active=1 and b.id in($data)";//die;
	$trims_group_result = sql_select($trims_group_query);
	foreach ($trims_group_result as $trims) {
		//$trims_group_arr[$trims[csf("item_category_id")]]["item_category_id"] = $items[csf("item_category_id")];
		$trims_group_arr[$trims[csf("trim_group")]]["trim_group"] = $trims[csf("trim_group")];
	}

	$price_quot_query= "select distinct(a.trim_group), b.item_name from wo_pri_quo_trim_cost_dtls a, lib_item_group b where a.trim_group = b.id and a.is_deleted=0 and b.status_active=1 and b.id in($data)";//die;
	$price_quot_result = sql_select($price_quot_query);
	foreach ($price_quot_result as $price_quote) {
		//$trims_group_arr[$trims[csf("item_category_id")]]["item_category_id"] = $items[csf("item_category_id")];
		$price_quot_arr[$price_quote[csf("trim_group")]]["trim_group"] = $price_quote[csf("trim_group")];
	}
	//var_dump($item_group_result);
	//wo_pri_quo_trim_cost_dtls
	$main_group_arr=return_library_array("select id, main_group_name from lib_main_group where status_active=1","id","main_group_name");
	$sql_item_group = "select id, item_category, item_group_code, item_name, main_group_id, trim_type, order_uom, trim_uom, conversion_factor, fancy_item, cal_parameter, order_uom_decimal_point_qty, order_uom_decimal_point, order_uom_decimal_point_amt, cons_uom_decimal_point_qty, cons_uom_decimal_point, cons_uom_decimal_point_amt, rate_cal_parameter, status_active, hs_code, is_zipper, section from lib_item_group where id='$data'" ;//die;
	$nameArray=sql_select($sql_item_group);
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category")])."';\n";    
		//echo "item_category_add(".$inf[csf("item_category")].", '7' );\n";
		echo "document.getElementById('txt_group_code').value  = '".($inf[csf("item_group_code")])."';\n";
		echo "document.getElementById('txt_item_name').value = '".($inf[csf("item_name")])."';\n";
		echo "document.getElementById('txt_hs_code').value = '".($inf[csf("hs_code")])."';\n";

		echo "document.getElementById('txt_main_group_id').value = '".($inf[csf("main_group_id")])."';\n";    
		echo "document.getElementById('txt_main_group').value = '".($main_group_arr[$inf[csf("main_group_id")]])."';\n";  

		echo "document.getElementById('cbo_trim_type').value  = '".($inf[csf("trim_type")])."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".($inf[csf("order_uom")])."';\n";    
		echo "document.getElementById('cbo_cons_uom').value  = '".($inf[csf("trim_uom")])."';\n";
		echo "document.getElementById('txt_conversion_factor').value = '".($inf[csf("conversion_factor")])."';\n";    
		echo "document.getElementById('cbo_fancy_item').value  = '".($inf[csf("fancy_item")])."';\n";
		echo "document.getElementById('cbo_ordUOMDecPlaceQnt').value  = '".($inf[csf("order_uom_decimal_point_qty")])."';\n";
		echo "document.getElementById('cbo_ordUOMDecPlaceRate').value  = '".($inf[csf("order_uom_decimal_point")])."';\n";
		echo "document.getElementById('cbo_ordUOMDecPlaceAmt').value  = '".($inf[csf("order_uom_decimal_point_amt")])."';\n";
		echo "document.getElementById('cbo_consUOMDecPlaceQnt').value  = '".($inf[csf("cons_uom_decimal_point_qty")])."';\n";
		echo "document.getElementById('cbo_consUOMDecPlaceRate').value  = '".($inf[csf("cons_uom_decimal_point")])."';\n";
		echo "document.getElementById('cbo_consUOMDecPlaceAmt').value  = '".($inf[csf("cons_uom_decimal_point_amt")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('cbo_cal_parameter').value  = '".($inf[csf("cal_parameter")])."';\n";
		echo "document.getElementById('cbo_ratecal_parameter').value  = '".($inf[csf("rate_cal_parameter")])."';\n";
		echo "document.getElementById('cbo_section').value  = '".($inf[csf("section")])."';\n";
		
		if($inf[csf("is_zipper")]==1){
			echo "document.getElementById('chk_zipper').checked = true;\n"; 
		}else{
			echo "document.getElementById('chk_zipper').checked = false;\n"; 
			$inf[csf("is_zipper")]=2;
		}
		
		echo "document.getElementById('update_id').value  = '".($inf[csf("is_zipper")])."';\n"; 
		
		
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_item_group',1);\n";  
		if(!empty ($item_group_arr[$inf[csf("item_category")]]["item_group_id"] )){
			if ($inf[csf("item_category")]== 4) {
				if (!empty($trims_group_arr[$inf[csf("id")]]["trim_group"]) || !empty($price_quot_arr[$inf[csf("id")]]["trim_group"])) {
					echo "document.getElementById('cbo_item_category').setAttribute('disabled', 'disabled');\n";
					echo "document.getElementById('cbo_order_uom').setAttribute('disabled', 'disabled');\n";
					echo "document.getElementById('cbo_cons_uom').setAttribute('disabled', 'disabled');\n";
				} else {
					echo "document.getElementById('cbo_item_category').removeAttribute('disabled', 'disabled');\n";
					echo "document.getElementById('cbo_order_uom').removeAttribute('disabled', 'disabled');\n";
					echo "document.getElementById('cbo_cons_uom').removeAttribute('disabled', 'disabled');\n";
				}				
			} else {
				echo "document.getElementById('cbo_item_category').setAttribute('disabled', 'disabled');\n";
				echo "document.getElementById('cbo_order_uom').setAttribute('disabled', 'disabled');\n";
				echo "document.getElementById('cbo_cons_uom').setAttribute('disabled', 'disabled');\n";
			}
		}else{
			echo "document.getElementById('cbo_item_category').removeAttribute('disabled', 'disabled');\n";
			//echo "document.getElementById('cbo_order_uom').removeAttribute('disabled', 'disabled');\n";
			//echo "document.getElementById('cbo_cons_uom').removeAttribute('disabled', 'disabled');\n";
		}
		echo "$('#cbo_ordUOMDecPlaceQnt').attr('disabled','true')".";\n";
		echo "$('#cbo_ordUOMDecPlaceRate').attr('disabled','true')".";\n";
		echo "$('#cbo_ordUOMDecPlaceAmt').attr('disabled','true')".";\n";
		echo "$('#cbo_consUOMDecPlaceQnt').attr('disabled','true')".";\n";
		echo "$('#cbo_consUOMDecPlaceRate').attr('disabled','true')".";\n";
		echo "$('#cbo_consUOMDecPlaceAmt').attr('disabled','true')".";\n";
		
	}
}

if ($action=="save_update_delete")
{  

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if(is_duplicate_field( "item_name", "lib_item_group", "item_category=$cbo_item_category and item_name=$txt_item_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_item_group", 1 ) ;
			$field_array="id, item_name, main_group_id, trim_type, remark, order_uom, trim_uom, inserted_by, insert_date, status_active, is_deleted, item_category, item_group_code, conversion_factor, fancy_item, cal_parameter, order_uom_decimal_point_qty, order_uom_decimal_point, order_uom_decimal_point_amt, cons_uom_decimal_point_qty, cons_uom_decimal_point, cons_uom_decimal_point_amt, rate_cal_parameter, hs_code, is_zipper, section";
			
			$data_array="(".$id.",".$txt_item_name.",".$txt_main_group_id.",".$cbo_trim_type.",'',".$cbo_order_uom.",".$cbo_cons_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0,".$cbo_item_category.",".$txt_group_code.",".$txt_conversion_factor.",".$cbo_fancy_item.",".$cbo_cal_parameter.",".$cbo_ordUOMDecPlaceQnt.",".$cbo_ordUOMDecPlaceRate.",".$cbo_ordUOMDecPlaceAmt.",".$cbo_consUOMDecPlaceQnt.",".$cbo_consUOMDecPlaceRate.",".$cbo_consUOMDecPlaceAmt.",".$cbo_ratecal_parameter.", ".$txt_hs_code.", ".$chk_zipper.", ".$cbo_section.")";
			//echo "10**INSERT into lib_item_group $field_array values $data_array"; die;
			$rID=sql_insert("lib_item_group",$field_array,$data_array,1);
		   
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
			if($rID )
				{
					oci_commit($con);   
					echo "0**".$rID;
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
		
	else if ($operation==1)   // Update Here
	{
		//$txt_item_group_id
		if(is_duplicate_field( "id", "product_details_master", "item_group_id=$update_id and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "30**Item Creation Found, So Update Not Allow"; die;
		}
		
		if(is_duplicate_field( "item_name", "lib_item_group", "item_category=$cbo_item_category and item_name=$txt_item_name and id!=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$return_msg='';
			if(is_duplicate_field( "a.id", "inv_transaction a, product_details_master b", "b.item_group_id=$update_id and a.prod_id=b.id and b.item_category_id=$cbo_item_category and a.status_active=1 and a.is_deleted=0" ) == 1)
			{
				if($db_type==0)
				{
					$prev_item_info=return_field_value("concat(item_name,'_',item_category,'_',order_uom,'_',trim_uom,'_',conversion_factor) as des","lib_item_group","id=$update_id","des");
				}
				else
				{
					$prev_item_info=return_field_value("(item_name || '_' || item_category || '_' || order_uom || '_' || trim_uom || '_' || conversion_factor) as des","lib_item_group","id=$update_id","des");
				}
				$revised_item=str_replace("'","",$txt_item_name)."_".str_replace("'","",$cbo_item_category)."_".str_replace("'","",$cbo_order_uom)."_".str_replace("'","",$cbo_cons_uom)."_".str_replace("'","",$txt_conversion_factor);

				if($prev_item_info !=$revised_item) 
				{
					$prev_item_info=explode('_', $prev_item_info);
					if($prev_item_info[0]!=str_replace("'","",$txt_item_name)) $return_msg.='Item Group ';
					if($prev_item_info[1]!=str_replace("'","",$cbo_item_category)) $return_msg.='Item Category ';
					if($prev_item_info[2]!=str_replace("'","",$cbo_order_uom)) $return_msg.='Order UOM ';
					if($prev_item_info[3]!=str_replace("'","",$cbo_cons_uom)) $return_msg.='Cons UOM ';
					if($prev_item_info[4]!=str_replace("'","",$txt_conversion_factor)) $return_msg.='Conv. Factor ';
				}

				$field_array="trim_type*remark*updated_by*update_date*status_active*is_deleted*item_group_code*fancy_item*cal_parameter*order_uom_decimal_point*order_uom_decimal_point_amt*cons_uom_decimal_point*cons_uom_decimal_point_amt*rate_cal_parameter*hs_code*is_zipper*section";
				$data_array="".$cbo_trim_type."*''*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0*".$txt_group_code."*".$cbo_fancy_item."*".$cbo_cal_parameter."*".$cbo_ordUOMDecPlaceRate."*".$cbo_ordUOMDecPlaceAmt."*".$cbo_consUOMDecPlaceRate."*".$cbo_consUOMDecPlaceAmt."*".$cbo_ratecal_parameter."*".$txt_hs_code."*".$chk_zipper."*".$cbo_section."";
			}
			else
			{
				$field_array="item_name*main_group_id*trim_type*remark*order_uom*trim_uom*updated_by*update_date*status_active*is_deleted*item_category*item_group_code*conversion_factor*fancy_item*cal_parameter*order_uom_decimal_point*order_uom_decimal_point_amt*cons_uom_decimal_point*cons_uom_decimal_point_amt*rate_cal_parameter*hs_code*is_zipper*section";
				$data_array="".$txt_item_name."*".$txt_main_group_id."*".$cbo_trim_type."*''*".$cbo_order_uom."*".$cbo_cons_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0*".        $cbo_item_category."*".$txt_group_code."*".$txt_conversion_factor."*".$cbo_fancy_item."*".$cbo_cal_parameter."*".$cbo_ordUOMDecPlaceRate."*".$cbo_ordUOMDecPlaceAmt."*".$cbo_consUOMDecPlaceRate."*".$cbo_consUOMDecPlaceAmt."*".$cbo_ratecal_parameter."*".$txt_hs_code."*".$chk_zipper."*".$cbo_section."";
			}
			$rID=sql_update("lib_item_group",$field_array,$data_array,"id","".$update_id."",1);
			//echo $rID;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID."**".$return_msg;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			  if($rID )
			    {
					oci_commit($con);   
					echo "1**".$rID."**".$return_msg;
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
	else if ($operation==2)   // Delete Here
	{
		/*$unique_check1 = is_duplicate_field( "id", "com_pi_item_details", "item_group=$update_id and status_active=1" );
		$unique_check2 = is_duplicate_field( "id", "wo_po_cost_trims_dtls", "item_code=$update_id and status_active=1" );
		$unique_check3 = is_duplicate_field( "id", "wo_order_info", "item_id=$update_id and status_active=1" );
		$unique_check4 = is_duplicate_field( "id", "inv_product_info_master", "item_group=$update_id and status_active=1" );
		$unique_check5 = is_duplicate_field( "id", "wo_non_order_info_dtls", "item_id=$update_id and status_active=1" );*/
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$price_quat_check = return_field_value( "id", "wo_pri_quo_trim_cost_dtls", "trim_group=$update_id and status_active=1","id");
		$budge_check = return_field_value( "id", "wo_pre_cost_trim_cost_dtls", "trim_group=$update_id and status_active=1","id");
		$product_check = return_field_value( "id", "product_details_master", "item_group_id=$update_id and status_active=1","id");
		if($price_quat_check >0 || $budge_check >0 || $product_check >0)
		{
			echo "5555**".str_replace("'", "", $update_id);disconnect($con);die;
		}
		if(is_duplicate_field( "a.id", "inv_transaction a, product_details_master b", "b.item_group_id=$update_id and a.prod_id=b.id and b.item_category_id=$cbo_item_category and a.status_active=1 and a.is_deleted=0" ) == 1)
			{
				echo "5555**".str_replace("'", "", $update_id);disconnect($con);die;
			}


		
		$item_id=return_field_value("particular_type_id", "qc_cons_rate_dtls", "particular_type_id=$update_id","particular_type_id");
		if(!empty($item_id))
		{
			echo "50**Some Entries Found For This Item, Deleting Not Allowed.";	
			disconnect($con);
			die;
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_item_group",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			  if($rID )
				{
					oci_commit($con);   
					echo "2**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
		die;
		
		/*
		$cat_id=return_field_value("item_category", "lib_item_group", "id=".$update_id." and status_active=1 and is_deleted=0 ","item_category");
		$exists_creation_sql=sql_select("select id,item_group_id from product_details_master where item_group_id=$update_id and item_category_id='$cat_id'");
 		if(count($exists_creation_sql)>0)
		{
			echo "5555**".str_replace("'", "", $update_id);disconnect($con);die;
		}
		else
		{
			$field_array="updated_by*update_date*status_active*is_deleted";
		    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("lib_item_group",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
		          if($rID )
				    {
						oci_commit($con);   
						echo "2**".$rID;
					}
				else{
						oci_rollback($con);
						echo "10**".$rID;
					}
			}
			disconnect($con);
			die;
		
		}*/	

	}	
}

if($action=="open_rate_popup_view")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	var permission='<? echo $permission; ?>';
	function fnc_rate_entry(operation)
		{
			//alert('bbb')
			var tot_row=$('#tbl_rate_details tr').length-1;
			var tot_row_company=$('#tbl_rate_details_company tr').length-1;
			var mst_id=document.getElementById('mst_id').value;
			var txt_item_category=document.getElementById('txt_item_category').value;
			var data_all='';
			var data_all_company='';
			for(i=1; i<=tot_row; i++)
			{
			    //data_all+=get_submitted_data_string('supplierid_'+i+'*txtrate_'+i+'*rateupid_'+i,"../../../",i);
				data_all+="&supplierid_" + i + "='" + $('#supplierid_'+i).val()+"'"+"&txtrate_" + i + "='" + $('#txtrate_'+i).val()+"'"+"&rateupid_" + i + "='" + $('#rateupid_'+i).val()+"'";
				
			}
			for(z=1; z<=tot_row_company; z++)
			{
			    //data_all_company+=get_submitted_data_string('csupplierid_'+i+'*ctxtrate_'+i+'*crateupid_'+i,"../../../",i);
				data_all_company+="&csupplierid_" + z + "='" + $('#csupplierid_'+z).val()+"'"+"&ctxtrate_" + z + "='" + $('#ctxtrate_'+z).val()+"'"+"&crateupid_" + z + "='" + $('#crateupid_'+z).val()+"'";
				
			}
			if(data_all=='' || data_all_company=='')
			{
				alert("No Data Select");	
				return;
			}
			//alert(data_all);return;
			var data="action=save_update_delete_rate&operation="+operation+data_all+'&tot_row='+tot_row+'&mst_id='+mst_id+'&txt_item_category='+txt_item_category+'&tot_row_comp='+tot_row_company+data_all_company;
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","item_group_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_rate_entry_response;
		}
		
		function fnc_rate_entry_response()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				set_button_status(1, permission, 'fnc_rate_entry',1);
				release_freezing();	
			}
		}
	</script>
    </head>

    <body>
     <form name="rate_1" id="rate_1">
      <? echo load_freeze_divs ("../../../",$permission);  ?>
	<table width="350" cellspacing="0" class="rpt_table" border="0" rules="all">
    <thead>
    <tr>
    <th width="30">SL</th><th width="230">Company Name</th><th width="60">rate</th>
    </tr>
    </thead>
    </table>
	<table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_rate_details_company" rules="all">
		<?
			$sql_up_data_comp=sql_select(" select id, mst_id,item_category, supplier_id, rate from lib_item_group_rate where mst_id=$mst_id and is_company=1");
			foreach($sql_up_data_comp as $row)
			{
				$array_up_data_company[$row[csf('mst_id')]][$row[csf('supplier_id')]]['rate']=$row[csf('rate')];	
			}
			$compnay_sql=sql_select(" select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name");
			$z=1;
			foreach($compnay_sql as $row_data)
			{
				$rate=$array_up_data_company[$mst_id][$row_data[csf('id')]]['rate']
			?>
			<tr>
				<td width="30"><?echo $z;?>	</td>
				<td width="230"><?	echo $row_data[csf('company_name')];?></td>
				<td width="60"><input type="hidden" id="csupplierid_<? echo $z; ?>" name="csupplierid_<? echo $z; ?>" class="text_boxes" value="<? echo $row_data[csf('id')];?>"/>
				<input type="text" id="ctxtrate_<? echo $z ?>" name="ctxtrate_<? echo $z ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $rate; ?>"/> 
				<input type="hidden" id="crateupid_<? echo $z; ?>" name="crateupid_<? echo $z; ?>" class="text_boxes" value="<?= $row_data[csf('id')]?>"/>
				</td>
			</tr>
			<?
			$z++;
			}
		?>
	</table>

    <table width="350" cellspacing="0" class="rpt_table" border="0" rules="all">
    <thead>
    <tr>
    <th width="30">SL</th><th width="230">Supplier</th><th width="60">rate</th>
    </tr>
    </thead>
    </table>
    <table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_rate_details" rules="all">
    <?
	$array_up_data=array();
	$sql_up_data=sql_select(" select id, mst_id,item_category, supplier_id, rate from lib_item_group_rate where mst_id=$mst_id and item_category=$cbo_item_category");
	//print_r($sql_up_data);
	foreach($sql_up_data as $row_up_data)
	{
	$array_up_data[$row_up_data[csf('mst_id')]][$row_up_data[csf('item_category')]][$row_up_data[csf('supplier_id')]][rate]=$row_up_data[csf('rate')];	
	}
	if($cbo_item_category==4)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==5 || $cbo_item_category==6 || $cbo_item_category==7)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(3) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==5 || $cbo_item_category==6 || $cbo_item_category==7)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(3) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==8)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(6,7) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==9)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(6,7) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==10)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(1) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==11)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(8) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==14)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(10) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==15 ||$cbo_item_category==16 )
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(6,7,8) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==17 || $cbo_item_category==18 || $cbo_item_category==19)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(8) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==20)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(1,8) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==21)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(21,23) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==22)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(23) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	if($cbo_item_category==32)
	{
     $sql="select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(92) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";	
	}
	$sql_data=sql_select($sql);
	$i=1;
	foreach($sql_data as $row_data)
	{
		$rate=$array_up_data[$mst_id][$cbo_item_category][$row_data[csf('id')]][rate]
	?>
    <tr>
    <td width="30">
    <?
	echo $i;
	?>
    </td>
     <td width="230">
    <?
	echo $row_data[csf('supplier_name')];
	?>
    
    </td>
    <td width="60">
    <input type="hidden" id="supplierid_<? echo $i; ?>" name="supplierid_<? echo $i; ?>" class="text_boxes" value="<? echo $row_data[csf('id')];?>"/>
    <input type="text" id="txtrate_<? echo $i ?>" name="txtrate_<? echo $i ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $rate; ?>"/> 
    <input type="hidden" id="rateupid_<? echo $i; ?>" name="rateupid_<? echo $i; ?>" class="text_boxes" value="<? $row_data[csf('id')]?>"/>
    </td>
    </tr>
    <?
	$i++;
    }
	?>
    
    </table>
    <table width="350" cellspacing="0" class="rpt_table" border="0"rules="all">
    
    <tr>
    <td colspan="3">
    <input type="hidden" id="mst_id" name="mst_id" class="text_boxes" value="<? echo $mst_id?>"/>
    <input type="hidden" id="txt_item_category" name="txt_item_category" class="text_boxes" value="<? echo $cbo_item_category?>"/>
	<?
	if(count($sql_up_data)==0)
	{
	echo load_submit_buttons($permission, "fnc_rate_entry", 0,0,"reset_form('rate_1','','','','','');",1);
	}
	else
	{
		echo load_submit_buttons($permission, "fnc_rate_entry", 1,0,"reset_form('rate_1','','','','','');",1);
	}
	?>
    </td>
    </tr>
    </table>
    </form>
    </body>  
<script>
var tableFilters = 	{					
					col_0: "none",
					col_2: "none",
				};
setFilterGrid("tbl_rate_details",tableFilters,-1)
setFilterGrid("tbl_rate_details_company",tableFilters,-1)
</script>         
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
}

if ($action=="save_update_delete_rate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id","lib_item_group_rate", 1 ) ;
		$field_array_size="id, mst_id,item_category, supplier_id, rate,is_company, inserted_by, insert_date";
		$add_comma="";
		$add_comma_comp="";
		$data_array_size='';
		$k=1;
		for($i=1;$i<=$tot_row; $i++)
		{
			$supplierid='supplierid_'.$i;
			$txtrate='txtrate_'.$i;
			$rateupid='rateupid_'.$i;
			if($k==1) $add_comma=""; else $add_comma=",";
			$data_array_size.="$add_comma(".$id.",".$mst_id.",".$txt_item_category.",".$$supplierid.",".$$txtrate.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
			$k++;
		}
		for($z=1;$z<=$tot_row_comp; $z++)
		{
			$supplierid='csupplierid_'.$z;
			$txtrate='ctxtrate_'.$z;
			$rateupid='crateupid_'.$z;
			if($k==1) $add_comma=""; else $add_comma=",";
			$data_array_size.="$add_comma(".$id.",".$mst_id.",".$txt_item_category.",".$$supplierid.",".$$txtrate.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
			$k++;
		}
		//echo $data_array_size; die;
		//echo "insert into sample_development_size (".$field_array_size.") Values ".$data_array_size."";die;
		$rIDs=sql_insert("lib_item_group_rate",$field_array_size,$data_array_size,1);
		
		if($db_type==0)
		{
			if($rIDs)
			{
				mysql_query("COMMIT");
				echo "0**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rIDs)
			{
				oci_commit($con); 
				echo "0**";
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id","lib_item_group_rate", 1 ) ;
		$field_array_size="id, mst_id,item_category, supplier_id, rate, is_company,inserted_by, insert_date";
		$add_comma="";
		$data_array_size='';
		$k=1;
		for($i=1;$i<=$tot_row; $i++)
		{
			$supplierid='supplierid_'.$i;
			$txtrate='txtrate_'.$i;
			$rateupid='rateupid_'.$i;
			if($k==1) $add_comma=""; else $add_comma=",";
			$data_array_size.="$add_comma(".$id.",".$mst_id.",".$txt_item_category.",".$$supplierid.",".$$txtrate.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
			$k++;
		}
		for($z=1;$z<=$tot_row_comp; $z++)
		{
			$supplierid='csupplierid_'.$z;
			$txtrate='ctxtrate_'.$z;
			$rateupid='crateupid_'.$z;
			if($k==1) $add_comma=""; else $add_comma=",";
			$data_array_size.="$add_comma(".$id.",".$mst_id.",".$txt_item_category.",".$$supplierid.",".$$txtrate.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
			$k++;
		}
		//echo $data_array_size; die;
		//echo "insert into lib_item_group_rate (".$field_array_size.") Values ".$data_array_size."";die;
		$rID=execute_query("delete from lib_item_group_rate where mst_id=$mst_id");
		$rIDs=sql_insert("lib_item_group_rate",$field_array_size,$data_array_size,1);
		if($db_type==0)
		{
			if( $rID && $rIDs )
			{
				mysql_query("COMMIT");  
				echo "1**";
			}
			
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**";
			}
		}
		if($db_type==2 || $db_type==1)
		{
			if( $rID && $rIDs )
			{
				oci_commit($con);  
				echo "1**";
			}
			
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID=execute_query("delete from lib_item_group_rate where mst_id=$mst_id");
		if($db_type==0)
		{
			if( $rID)
			{
				mysql_query("COMMIT");  
				echo "2**";
			}
			
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**";
			}
		}
		if($db_type==2 || $db_type==1)
		{
			if( $rID)
			{
				oci_commit($con);  
				echo "2**";
			}
			
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}
?>