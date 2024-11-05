<?
header('Content-type:text/html; charset=utf-8'); 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="category_add")
{
	$result=sql_select("select user_given_code_status from variable_settings_inventory where item_category_id=$data and status_active=1 and is_deleted=0" );
	foreach ($result as $inf)
	{
		$item_category_name=$inf[csf("user_given_code_status")];
	}
	if($item_category_name==1)
	{
		echo "$('#txt_subgroup_code').removeAttr('disabled','disabled');\n";
		echo "$('#txt_item_code').removeAttr('disabled','disabled');\n";
		//echo "document.getElementById('hide_item_code').value = '1';\n"; 
	}
	else if($item_category_name=="")
	{
		echo "$('#txt_subgroup_code').removeAttr('disabled','disabled');\n";
		echo "$('#txt_item_code').removeAttr('disabled','disabled');\n";
		//echo "document.getElementById('hide_item_code').value = '1';\n"; 
	}
	else
	{			
		echo "$('#txt_subgroup_code').attr('disabled','disabled');\n";
		echo "$('#txt_item_code').attr('disabled','disabled');\n";
		//echo "document.getElementById('hide_item_code').value='0';\n";
	}
}

if ($action=="order_popup")																																																					{
	  echo load_html_head_contents("Item Creation popup", "../../../", 1, 1,'','1','');	
	  extract($_REQUEST);
?>
  <script>
  function js_set_value(id)
  {
	  document.getElementById('item_id').value=id;
	  parent.emailwindow.hide();
  }
  </script>
  </head>	
  <body>
    <div align="center" style="width:930px" >
    <fieldset style="width:930px"> 
        <form name="order_popup_1"  id="order_popup_1">
            <?
            if ($category!=0) $item_category_list=" and item_category='$category'"; else { echo "Please Select Item Category."; die; }
            $sql="select id,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,cal_parameter from lib_item_group where is_deleted=0 $item_category_list";
            $arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$cal_parameter);
            echo  create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Cal Parameter", "150,100,200,80,50,50","900","320",0, $sql, "js_set_value", "id", "'load_php_popup_to_form'", 1, "item_category,0,0,trim_type,order_uom,trim_uom,cal_parameter", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,cal_parameter", "item_creation_trims_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0' );
            ?>
        <input type="hidden" id="item_id" />
        </form>
    </fieldset>
    </div>
  </body>           
  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  </html>                                  
<? 																																																					}



if ($action=="load_php_popup_to_form")
{
	$data=explode("_",$data);
	//if ($data[2]!=0) $item_category_list=" and item_category_id='$data[2]'"; else { echo "Please Select Item Category."; }
	if ($data[1]!="blur") $data =" and id='$data[0]'"; else $data =" and item_group_code like '$data[0]'";
	$nameArray=sql_select( "select id,item_name,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,fancy_item,cal_parameter,status_active from  lib_item_group where status_active=1 $data" );
	if (count($nameArray)>0)
	{
		foreach ($nameArray as $inf)
		{
			$item_name=$inf[csf("item_name")];
			$group_code=$inf[csf("item_group_code")];
			if($group_code!="")$item_group=$group_code.'-'.$item_name;
			else $item_group=$item_name;
			
			echo "document.getElementById('txt_item_group').value 	= '".($item_group)."';\n";
			//echo "document.getElementById('txt_item_group').value 	= '".($inf[csf("item_group_code")])."';\n"; 
			echo "document.getElementById('cbo_order_uom').value  	= '".($inf[csf("order_uom")])."';\n";
			echo "document.getElementById('cbo_cons_uom').value 		= '".($inf[csf("trim_uom")])."';\n"; 
			echo "document.getElementById('item_group_id').value  		= '".($inf[csf("id")])."';\n"; 
			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_item_creation',1);\n";  
		}
	}
	else
	{
		//echo "document.getElementById('message').value 			= 'Please Browse from Popup';\n"; 
		echo "document.getElementById('demo_message').innerHTML='Please Browse from Popup'";
		//else echo "alert('Please Browse from Popup')";
	}
	exit();
} 
	if ($action=="item_creation_list_view")	
	{
		
	   $data=str_replace("'","",$data);
	   $data=explode('**', $data);
	   //print_r($data);
	   
		$entry_cond="";
		if($data[0]==4) $entry_cond=" and a.entry_form=20";	
		$item_cat_cond=($data[0]==0)? "" : " and a.item_category_id='$data[0]' ";
		$item_group_cond=($data[2]==0)? "" : " and a.item_group_id='$data[2]' ";

		$company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$arr=array(7=>$unit_of_measurement,9=>$trims_section,10=>$company_array,11=>$row_status);
		$sql="SELECT a.id,a.item_account,a.item_category_id,a.sub_group_name,a.item_description, a.product_name_details,a.item_size,a.unit_of_measure,a.company_id,a.status_active,a.brand_name,a.model,a.origin,a.re_order_label,a.maximum_label,a.conversion_factor, a.section_id,b.item_name from product_details_master a, lib_item_group b where a.entry_form=334 and a.is_deleted=0 and b.is_deleted=0 and a.item_group_id=b.id and a.company_id='$data[1]'  $item_cat_cond $item_group_cond $entry_cond";
		//echo $sql;
		echo  create_list_view ( "list_view", "Item Account,Group Name,Sub Group Name,Item Description,Re-Order Level,Max Level,Item Size,Cons UOM,Conv. Factor,Section,Company,Status", "100,130,150,140,100,80,60,60,60,110,130,100","1270","320",1, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,0,0,0,0,0,unit_of_measure,0,section_id,company_id,status_active", $arr ,"item_account,item_name,sub_group_name,item_description,re_order_label,maximum_label,item_size,unit_of_measure,conversion_factor,section_id,company_id,status_active", "requires/item_creation_trims_controller", 'setFilterGrid("list_view",-1);' );
		}																																																 


if ($action=="load_php_data_to_form")
{
	/*$nameArray=sql_select("select a.id,a.company_id,a.item_category_id,a.item_group_id,a.sub_group_code,a.sub_group_name,a.item_code,a.item_description,a.item_size,a.re_order_label,a.minimum_label,a.maximum_label,a.unit_of_measure,a.item_account,b.id as group_id,b.item_name,b.item_group_code,b.order_uom,a.status_active,a.brand_name,a.origin from product_details_master a,lib_item_group b where a.item_group_id=b.id and a.id='$data'");
	*/
	/*.....additional code-----*/
	$nameArray=sql_select("select a.id,a.company_id,a.item_category_id,a.item_group_id,a.sub_group_code,a.sub_group_name,a.item_code,a.item_description,a.item_size,a.re_order_label,a.minimum_label,a.maximum_label,a.unit_of_measure,a.item_account,a.order_uom ,a.conversion_factor,b.id as group_id,b.item_name,b.item_group_code,a.status_active,a.brand_name,a.model,a.origin,a.fixed_asset,a.bond_status,a.section_id,a.order_uom_decimal_point,a.cons_uom_decimal_point 
	from product_details_master a, lib_item_group b where a.entry_form=334 and a.item_group_id=b.id and a.id='$data'");
	
	foreach ($nameArray as $inf)
	{
		
		$nameArray1=sql_select( "select id,product_id from inv_purchase_requisition_dtls where product_id=$data and status_active=1 and is_deleted=0" ); 
	 	foreach ($nameArray1 as $row)
	  	{
		 $item_table_id=$row['product_id'];
		}
		if($inf[csf("status_active")]==1)
		{
			$trans_id=return_field_value("id", "inv_transaction", "prod_id=".$data." and status_active=1 and is_deleted=0 ","id");
		}
		//echo $trans_id.'=='.$data.'=='.$item_table_id; die;
	    if($data==$item_table_id)
		{
			$item_name=$inf[csf("item_name")];
			$group_code=$inf[csf("item_group_code")];
			if($group_code!="")$item_group=$group_code.'-'.$item_name;
			else $item_group=$item_name;
			
			echo "document.getElementById('cbo_company_name').value 		= '".($inf[csf("company_id")])."';\n";  
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";  
			echo "document.getElementById('cbo_item_category').value 	 	= '".($inf[csf("item_category_id")])."';\n";
			echo "$('#cbo_item_category').attr('disabled','true')".";\n"; 
			echo "document.getElementById('txt_item_group').value 			= '".($item_group)."';\n";
			echo "$('#txt_item_group').attr('disabled','true')".";\n";   
			echo "document.getElementById('txt_subgroup_code').value  		= '".($inf[csf("sub_group_code")])."';\n";
			echo "$('#txt_subgroup_code').attr('disabled','true')".";\n"; 
			echo "document.getElementById('txt_subgroup_name').value 		= '".($inf[csf("sub_group_name")])."';\n";    
			echo "document.getElementById('txt_item_code').value  			= '".($inf[csf("item_code")])."';\n";
			echo "$('#txt_item_code').attr('disabled','true')".";\n"; 
			echo "document.getElementById('txt_description').value 			= '".($inf[csf("item_description")])."';\n";
			echo "document.getElementById('txt_item_size').value 			= '".($inf[csf("item_size")])."';\n";     
			echo "document.getElementById('txt_reorder_label').value  		= '".($inf[csf("re_order_label")])."';\n";
			echo "document.getElementById('txt_min_label').value  			= '".($inf[csf("minimum_label")])."';\n";
			echo "document.getElementById('txt_max_label').value  			= '".($inf[csf("maximum_label")])."';\n";
			echo "document.getElementById('cbo_cons_uom').value  			= '".($inf[csf("unit_of_measure")])."';\n";
			echo "$('#cbo_cons_uom').attr('disabled','true')".";\n"; 
			echo "document.getElementById('cbo_order_uom').value  			= '".($inf[csf("order_uom")])."';\n";
			echo "document.getElementById('txt_item_account').value  		= '".($inf[csf("item_account")])."';\n";
			echo "$('#txt_item_account').attr('disabled','true')".";\n"; 
			echo "document.getElementById('item_group_id').value  			= '".($inf[csf("group_id")])."';\n";
			echo "document.getElementById('cbo_status').value  				= '".($inf[csf("status_active")])."';\n";
			echo "document.getElementById('cbo_bond_status').value  		= '".($inf[csf("bond_status")])."';\n";
			echo "document.getElementById('cbo_section').value  			= '".($inf[csf("section_id")])."';\n";
			if($trans_id!=""){  echo "$('#cbo_section').attr('disabled','true')".";\n";   }
		  	else{ echo "document.getElementById('cbo_section').disabled = '".false."';\n";}
            echo "document.getElementById('txt_brand').value  				= '".($inf[csf("brand_name")])."';\n";
            /*-----additional code------*/         
            echo "document.getElementById('txt_model_name').value  			= '".($inf[csf("model")])."';\n";            
            echo "document.getElementById('cbo_origin').value  				= '".($inf[csf("origin")])."';\n";
			echo "document.getElementById('cbo_fixed_asset').value  		= '".($inf[csf("fixed_asset")])."';\n";
			echo "document.getElementById('cbo_order_uom_decimal_point').value  = '".($inf[csf("order_uom_decimal_point")])."';\n";
			echo "document.getElementById('cbo_cons_uom_decimal_point').value  = '".($inf[csf("cons_uom_decimal_point")])."';\n";
			echo "document.getElementById('cbo_order_uom').value  			= '".($inf[csf("order_uom")])."';\n";
			echo "document.getElementById('txt_conversion_factor').value  	= '".($inf[csf("conversion_factor")])."';\n";
			echo "document.getElementById('update_id').value  				= '".($inf[csf("id")])."';\n"; 
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_item_creation',1);\n"; 
			/*if($inf[csf("status_active")]==1)
		      {  echo "$('#cbo_status').attr('disabled','true')".";\n";   }*/
		}
	    else
		{
			$item_name=$inf[csf("item_name")];
			$group_code=$inf[csf("item_group_code")];
			if($group_code!="")$item_group=$group_code.'-'.$item_name;
			else $item_group=$item_name;
			
			echo "document.getElementById('cbo_company_name').value 		= '".($inf[csf("company_id")])."';\n";  
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";  
			echo "document.getElementById('cbo_item_category').value 	 	= '".($inf[csf("item_category_id")])."';\n";
			echo "$('#cbo_item_category').attr('disabled','true')".";\n"; 
			echo "document.getElementById('txt_item_group').value 			= '".($item_group)."';\n";
			echo "$('#txt_item_group').attr('disabled','true')".";\n";   
			echo "document.getElementById('txt_subgroup_code').value  		= '".($inf[csf("sub_group_code")])."';\n";
			echo "document.getElementById('txt_subgroup_name').value 		= '".($inf[csf("sub_group_name")])."';\n";    
			echo "document.getElementById('txt_item_code').value  			= '".($inf[csf("item_code")])."';\n";
			echo "document.getElementById('txt_description').value 			= '".($inf[csf("item_description")])."';\n";
			echo "document.getElementById('txt_item_size').value 			= '".($inf[csf("item_size")])."';\n";     
			echo "document.getElementById('txt_reorder_label').value  		= '".($inf[csf("re_order_label")])."';\n";
			echo "document.getElementById('txt_min_label').value  			= '".($inf[csf("minimum_label")])."';\n";
			echo "document.getElementById('txt_max_label').value  			= '".($inf[csf("maximum_label")])."';\n";
			echo "document.getElementById('cbo_cons_uom').value  			= '".($inf[csf("unit_of_measure")])."';\n";
			echo "$('#cbo_cons_uom').attr('disabled','true')".";\n"; 
			echo "document.getElementById('cbo_order_uom').value  			= '".($inf[csf("order_uom")])."';\n";
			echo "$('#cbo_order_uom').attr('disabled','true')".";\n"; 
			echo "document.getElementById('txt_item_account').value  		= '".($inf[csf("item_account")])."';\n";
			echo "document.getElementById('item_group_id').value  			= '".($inf[csf("group_id")])."';\n";
			echo "document.getElementById('update_id').value  				= '".($inf[csf("id")])."';\n"; 
			echo "document.getElementById('cbo_status').value  				= '".($inf[csf("status_active")])."';\n";
			echo "document.getElementById('cbo_bond_status').value  		= '".($inf[csf("bond_status")])."';\n";
			echo "document.getElementById('cbo_section').value  			= '".($inf[csf("section_id")])."';\n";
			if($trans_id !=""){  echo "$('#cbo_section').attr('disabled','true')".";\n";   }
		  	else{ echo "document.getElementById('cbo_section').disabled = '".false."';\n";}
            echo "document.getElementById('txt_brand').value  				= '".($inf[csf("brand_name")])."';\n";
            /*------additional code------*/            
            echo "document.getElementById('txt_model_name').value  			= '".($inf[csf("model")])."';\n";            
            echo "document.getElementById('cbo_origin').value  				= '".($inf[csf("origin")])."';\n";
			echo "document.getElementById('cbo_fixed_asset').value  		= '".($inf[csf("fixed_asset")])."';\n"; 
			echo "document.getElementById('cbo_order_uom_decimal_point').value  = '".($inf[csf("order_uom_decimal_point")])."';\n";
			echo "document.getElementById('cbo_cons_uom_decimal_point').value  = '".($inf[csf("cons_uom_decimal_point")])."';\n";
			echo "document.getElementById('cbo_order_uom').value  			= '".($inf[csf("order_uom")])."';\n";
			echo "document.getElementById('txt_conversion_factor').value  	= '".($inf[csf("conversion_factor")])."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_item_creation',1);\n"; 
		}
		
		
		echo "$('#cbo_order_uom').attr('disabled','true')".";\n";
		echo "$('#cbo_cons_uom').attr('disabled','true')".";\n";
		echo "$('#txt_conversion_factor').attr('disabled','true')".";\n";
		echo "$('#cbo_order_uom_decimal_point').attr('disabled','true')".";\n";
		echo "$('#cbo_cons_uom_decimal_point').attr('disabled','true')".";\n";
		
		echo "document.getElementById('update_status_active').value  = '".($inf[csf("status_active")])."';\n";
		if($trans_id!="") echo "$('#cbo_status').attr('disabled','true')".";\n"; 
	}
}


if($action=="product_id_check")
{
  $p_no_check=sql_select("select prod_id from inv_transaction where  prod_id='$data'  and status_active=1 and is_deleted=0");
  
  //echo "select prod_id from inv_transaction where  prod_id='$data'  and status_active=1 and is_deleted=0";
  $prod_number="";
  foreach($p_no_check as $prod_no)
  {
	  if( $prod_number=="")  $prod_number=$prod_no[csf('prod_id')]; else $prod_number.="_".$prod_no[csf('prod_id')];
  	
  }
  echo $prod_number;
  
}


if ($action=="save_update_delete")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	
	$item_cat=str_replace("'","",$cbo_item_category); 
	$item_group=str_replace("'","",$item_group_id);
	$group_categry=return_field_value("item_category","lib_item_group","status_active=1 and is_deleted=0 and id=$item_group","item_category");
	if($item_cat!=$group_categry)
	{
		echo "11**Item Group Category and Item Category Not Match.";disconnect($con);die;
	}
	
	
	if ($operation==0)  // Insert Here=======================================================
	{
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==2)
		{
			$duplicate_cond='';
			if(str_replace("'","",$txt_subgroup_name)=='') $duplicate_cond.=" and sub_group_name is null"; else $duplicate_cond.=" and sub_group_name=$txt_subgroup_name";
			if(str_replace("'","",$txt_description)=='') $duplicate_cond .=" and item_description is null"; else $duplicate_cond.=" and item_description=$txt_description";
			if(str_replace("'","",$txt_item_size)=='') $duplicate_cond .=" and item_size is null"; else $duplicate_cond.=" and item_size=$txt_item_size";
			
			$duplicate = is_duplicate_field("id","product_details_master","entry_form=334 and company_id=$cbo_company_name and item_category_id=$cbo_item_category and item_group_id=$item_group_id $duplicate_cond and is_deleted=0 "); 
			//and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size
		}
		else 
		{
			$duplicate = is_duplicate_field("id","product_details_master","entry_form=334 and company_id=$cbo_company_name and item_category_id=$cbo_item_category and item_group_id=$item_group_id and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size and is_deleted=0 "); 
		}
		
		if($duplicate==1)
		{
			echo "11**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}
		
		$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');	
		$productname = $item_group_arr[str_replace("'", '', $item_group_id)]." ".str_replace("'", '', $txt_description)." ".str_replace("'", '', $txt_item_size);
		$entry_form_lib=334;
		
		//$id=return_next_id("id","product_details_master",1);
		// $id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
		//$id=return_next_id( "id", "  product_details_master", 0 ) ;
		$id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
		
		/*$field_array="id,company_id,item_category_id,entry_form,item_group_id,sub_group_code,sub_group_name,item_code,item_description,product_name_details,item_size,re_order_label,minimum_label,maximum_label,unit_of_measure,item_account,inserted_by,insert_date,status_active,brand_name,origin,is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_item_category.",".$entry_form_lib.",".$item_group_id.",".$txt_subgroup_code.",".$txt_subgroup_name.",".$txt_item_code.",".$txt_description.",'".$productname."',".$txt_item_size.",".$txt_reorder_label.",".$txt_min_label.",".$txt_max_label.",".$cbo_cons_uom.",".$txt_item_account.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",".$txt_brand.",".$cbo_origin.",0)";*/
		/*-------additional code-------*/
		
		$field_array="id,company_id,item_category_id,entry_form,item_group_id,sub_group_code,sub_group_name,item_code,item_description,product_name_details,item_size,re_order_label,minimum_label,maximum_label,unit_of_measure,item_account,inserted_by,insert_date,status_active,bond_status,brand_name,model,origin,fixed_asset,order_uom,conversion_factor,section_id,is_deleted,order_uom_decimal_point,cons_uom_decimal_point";
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_item_category.",".$entry_form_lib.",".$item_group_id.",".$txt_subgroup_code.",".$txt_subgroup_name.",".$txt_item_code.",".$txt_description.",'".$productname."',".$txt_item_size.",".$txt_reorder_label.",".$txt_min_label.",".$txt_max_label.",".$cbo_cons_uom.",".$txt_item_account.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",".$cbo_bond_status.",".$txt_brand.",".$txt_model_name.",".$cbo_origin.",".$cbo_fixed_asset.",".$cbo_order_uom.",".$txt_conversion_factor.",".$cbo_section.",0,".$cbo_order_uom_decimal_point.",".$cbo_cons_uom_decimal_point.")";
		//echo $data_array;die;
		$rID=sql_insert("product_details_master",$field_array,$data_array,1);
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
		
		if($db_type==2)
		{
			$duplicate_cond='';
			if(str_replace("'","",$txt_subgroup_name)=='') $duplicate_cond.=" and sub_group_name is null"; else $duplicate_cond.=" and sub_group_name=$txt_subgroup_name";
			if(str_replace("'","",$txt_description)=='') $duplicate_cond .=" and item_description is null"; else $duplicate_cond.=" and item_description=$txt_description";
			if(str_replace("'","",$txt_item_size)=='') $duplicate_cond .=" and item_size is null"; else $duplicate_cond.=" and item_size=$txt_item_size";
			
			$duplicate = is_duplicate_field("id","product_details_master","entry_form=334 and company_id=$cbo_company_name and item_category_id=$cbo_item_category and item_group_id=$item_group_id and id<>$update_id $duplicate_cond and is_deleted=0 "); 
			//and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size
		}
		else 
		{
			
			$duplicate = is_duplicate_field("id","product_details_master","entry_form=334 and company_id=$cbo_company_name and item_category_id=$cbo_item_category and item_group_id=$item_group_id and id<>$update_id and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size and is_deleted=0 "); 
		}
		 
		if($duplicate==1) 
		{
			echo "11**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}
		
		 
		if(str_replace("'", '',$cbo_status)!=1)
		{
			$prod_stock=return_field_value("current_stock","product_details_master","prod_id=".$update_id." and status_active=1 and is_deleted=0","current_stock");
			if($prod_stock>0)
			{
				echo "11**Stock Found, Update Not Allow.";
				disconnect($con);die;
			}
			
			
			/*$trans_id=return_field_value("id","inv_transaction","prod_id=".$update_id." and status_active=1 and is_deleted=0","id");
			$parce_req_id=return_field_value("id", "inv_purchase_requisition_dtls", "product_id=".$update_id." and status_active=1 and is_deleted=0 ","id");
			if($trans_id!="") { echo 101;die;}
			if($parce_req_id!="") { echo 102;die;}*/
		}
		if(is_duplicate_field( "a.id", "inv_transaction a, product_details_master b", "b.id=$update_id and a.prod_id=b.id and a.status_active=1 and a.is_deleted=0" ) == 1)
			{
				$field_array="company_id*item_category_id*entry_form*sub_group_code*sub_group_name*item_code*item_description*product_name_details*item_size*re_order_label*minimum_label*maximum_label*unit_of_measure*item_account*updated_by*update_date*status_active*bond_status*brand_name*model*origin*fixed_asset*order_uom*conversion_factor*is_deleted";
				$data_array="".$cbo_company_name."*".$cbo_item_category."*".$entry_form_lib."*".$txt_subgroup_code."*".$txt_subgroup_name."*".$txt_item_code."*".$txt_description."*'".$productname."'*".$txt_item_size."*".$txt_reorder_label."*".$txt_min_label."*".$txt_max_label."*".$cbo_cons_uom."*".$txt_item_account."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*".$cbo_bond_status."*".$txt_brand."*".$txt_model_name."*".$cbo_origin."*".$cbo_fixed_asset."*".$cbo_order_uom."*".$txt_conversion_factor."*0";
			}
			else
			{
				$field_array="company_id*item_category_id*entry_form*sub_group_code*sub_group_name*item_code*item_description*product_name_details*item_size*re_order_label*minimum_label*maximum_label*unit_of_measure*item_account*updated_by*update_date*status_active*bond_status*brand_name*model*origin*fixed_asset*order_uom*conversion_factor*section_id*is_deleted";
				$data_array="".$cbo_company_name."*".$cbo_item_category."*".$entry_form_lib."*".$txt_subgroup_code."*".$txt_subgroup_name."*".$txt_item_code."*".$txt_description."*'".$productname."'*".$txt_item_size."*".$txt_reorder_label."*".$txt_min_label."*".$txt_max_label."*".$cbo_cons_uom."*".$txt_item_account."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*".$cbo_bond_status."*".$txt_brand."*".$txt_model_name."*".$cbo_origin."*".$cbo_fixed_asset."*".$cbo_order_uom."*".$txt_conversion_factor."*".$cbo_section."*0";

			}
		
		$entry_form_lib=334;
		$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');	
		$productname = $item_group_arr[str_replace("'", '', $item_group_id)]." ".str_replace("'", '', $txt_description)." ".str_replace("'", '', $txt_item_size);
		
		/*$field_array="company_id*item_category_id*entry_form*sub_group_code*sub_group_name*item_code*item_description*product_name_details*item_size*re_order_label*minimum_label*maximum_label*unit_of_measure*item_account*updated_by*update_date*status_active*brand_name*origin*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_item_category."*".$entry_form_lib."*".$txt_subgroup_code."*".$txt_subgroup_name."*".$txt_item_code."*".$txt_description."*'".$productname."'*".$txt_item_size."*".$txt_reorder_label."*".$txt_min_label."*".$txt_max_label."*".$cbo_cons_uom."*".$txt_item_account."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*".$txt_brand."*".$cbo_origin."*0";*/
		/*-------additional code------*/
		
		$field_array="company_id*item_category_id*entry_form*sub_group_code*sub_group_name*item_code*item_description*product_name_details*item_size*re_order_label*minimum_label*maximum_label*unit_of_measure*item_account*updated_by*update_date*status_active*bond_status*brand_name*model*origin*fixed_asset*order_uom*conversion_factor*section_id*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_item_category."*".$entry_form_lib."*".$txt_subgroup_code."*".$txt_subgroup_name."*".$txt_item_code."*".$txt_description."*'".$productname."'*".$txt_item_size."*".$txt_reorder_label."*".$txt_min_label."*".$txt_max_label."*".$cbo_cons_uom."*".$txt_item_account."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*".$cbo_bond_status."*".$txt_brand."*".$txt_model_name."*".$cbo_origin."*".$cbo_fixed_asset."*".$cbo_order_uom."*".$txt_conversion_factor."*".$cbo_section."*0";
		
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
		
		$trans_id=return_field_value("id","inv_transaction","prod_id=".$update_id." and status_active=1 and is_deleted=0","id");
		$parce_req_id=return_field_value("id", "inv_purchase_requisition_dtls", "product_id=".$update_id." and status_active=1 and is_deleted=0 ","id");
		$po_id=return_field_value("id","wo_non_order_info_dtls","item_id=".$update_id." and item_id>0 and status_active=1 and is_deleted=0","id");
		if($trans_id!="" || $parce_req_id!="" || $po_id!="")
		{
			echo "11**Some Entries Found For This Item Account, Deleting Not Allowed.";	
			disconnect($con);
			die;
		}
			
		//echo $all_received_master=("select min(a.recv_number) as recv_number,min(a.entry_form) as entry_form from  inv_receive_master a,inv_transaction b where a.id=b.mst_id and b.prod_id=$update_id  and a.status_active=1 and a.is_deleted=0 and  a.entry_form in (4)");die;
		/*$dyes_chemical_received_no=return_field_value("min(a.recv_number) as recv_number", "inv_receive_master a,inv_transaction b", "a.id=b.mst_id and b.prod_id=$update_id  and a.status_active=1 and a.is_deleted=0 and  a.entry_form in (4)","recv_number");
		if($dyes_chemical_received_no!="")
		{
			echo "50**Some Entries Found For This Item Account, Deleting Not Allowed, \n Dyes Chemical Recv: ".$dyes_chemical_received_no;	
			
		}
			
		$nameArray=sql_select( "select id,product_id from inv_purchase_requisition_dtls where product_id=$update_id" ); 
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
				echo "2**".$rID."**".str_replace("'",'',$cbo_item_category)."**".str_replace("'",'',$update_id);
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
				echo "2**".$rID."**".str_replace("'",'',$cbo_item_category)."**".str_replace("'",'',$update_id);
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



if($action=="load_check_value_to_form")
{
	$data_value=explode("**",$data);
	//print_r($data_value);
	//if ($data_value[0]==0) $company =""; else $company =" and company_id=$data_value[0]";
	//if ($data_value[1]==0) $category =""; else $category =" and item_category_id=$data_value[1]";
	//if ($data_value[2]=="") $item_group =""; else $item_group =" and item_group_id=$data_value[2]";
	if ($data_value[3]=="") $sub_group_code =""; else $sub_group_code =" and sub_group_code like '$data_value[3]'";
	//if ($data_value[4]=="") $sub_group_name =""; else $sub_group_name =" and sub_group_name like '$data_value[4]'";
	if ($data_value[5]=="") $item_code =""; else $item_code =" and item_code like '$data_value[5]'";
	//if ($data_value[6]=="") $item_description =""; else $item_description =" and item_description like '$data_value[6]'";
	//if ($data_value[7]=="") $item_size =""; else $item_size =" and item_size like '$data_value[7]'";
	
	$sql_dtls="select id, company_id, item_category_id, item_group_id, sub_group_code, sub_group_name, item_code, item_description, item_size from product_details_master where status_active=1 and entry_form=334 and company_id=$data_value[0] and item_category_id=$data_value[1] and item_group_id=$data_value[2] $sub_group_code and sub_group_name like '$data_value[4]'  $item_code  and item_description like '$data_value[6]' and item_size like '$data_value[7]'";
	//echo $sql_dtls;
	$chack_sql=sql_select($sql_dtls);
	if (count($chack_sql)!=0)
	{
		echo "12";
	}
/*	else
	{
	 	echo "12";
	}*/
	exit();
}


if($action=="test_parameter_popup")
{
  	echo load_html_head_contents("Test Parameter Info","../../../", 1, 1, $unicode,'','');
  	//echo load_html_head_contents("AOP production", "../../",1, 1,$unicode,1,'');
	$permission=$_SESSION['page_permission'];
	//echo $permission."==="; 
	extract($_REQUEST);
	//$data=explode("_",$data);
	?>
	<script>
		var permission='<? echo $permission; ?>';
		//alert(permission)
		
		function fnc_parameter_save( operation )
		{
			if ( form_validation('mst_update_id','Item')==false )
			{
				return;
			}
			var j=0; var check_field=0; data_all=""; var i=0;
			var txt_deleted_id 			= $('#txt_deleted_id').val();
			var mst_update_id 			= $('#mst_update_id').val();
			$("#tbl_dtls_emb tbody tr").each(function()
			{
				var txtTechChar 		= $(this).find('input[name="txtTechChar[]"]').val();
				var txtStandard 		= $(this).find('input[name="txtStandard[]"]').val();
				var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
				j++

				if(txtTechChar=='' || txtStandard=='')
				{	 				
					if(txtTechChar=='')
					{
						alert('Please Write Technical Charecteristics');
						check_field=1 ; return;
					}
					else
					{
						alert('Please Write Standard Value');
						check_field=1 ; return;
					}
				}
				i++;
				data_all += "&txtTechChar_" + j + "='" + txtTechChar + "'&txtStandard_" + j + "='" + txtStandard + "'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId + "'";
			});
			
			if(check_field==0)
			{
				var data="action=save_update_delete_parameter&operation="+operation+'&total_row='+i+'&mst_update_id='+mst_update_id+'&txt_deleted_id='+txt_deleted_id+data_all;
				//alert (data); //return;
				freeze_window(operation);
				http.open("POST","item_creation_trims_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_parameter_save_reponse;
			}
			else
			{
				return;
			}
		}

		function fnc_parameter_save_reponse()
		{
			if(http.readyState == 4) 
			{
			    var reponse=trim(http.responseText).split('**');
				//alert(http.responseText);
				release_freezing();
				show_msg(trim(reponse[0]));
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					parent.emailwindow.hide();
				}
			}
		}

		/*function fnc_close()
		{
			parent.emailwindow.hide();
		}*/
		
		function fnc_addRow( i, table_id, tr_id )
		{ 
			var prefix=tr_id.substr(0, tr_id.length-1);
			var row_num = $('#tbl_dtls_emb tbody tr').length; 
			//alert(i+"**"+table_id+"**"+tr_id+"**"+row_num);
			row_num++;
			var clone= $("#"+tr_id+i).clone();
			clone.attr({
				id: tr_id + row_num,
			});
			
			clone.find("input,select").each(function(){
				$(this).attr({ 
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					'name': function(_, name) { return name },
					'value': function(_, value) { return value }
				});
			}).end();
			$("#"+tr_id+i).after(clone);
			$('#hdnDtlsUpdateId_'+row_num).removeAttr("value").attr("value","");
			$('#txtTechChar_'+row_num).removeAttr("value").attr("value","");
			$('#txtStandard_'+row_num).removeAttr("value").attr("value","");
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
			set_all_onclick();
		}

		function fnc_deleteRow(rowNo,table_id,tr_id) 
		{ 
			//var numRow = $('#'+table_id+' tbody tr').length; 
			var prefix=tr_id.substr(0, tr_id.length-1);
			var total_row=$('#'+prefix+'_tot_row').val();
			
			var numRow = $('table#tbl_dtls_emb tbody tr').length; 
			if(numRow!=1)
			{
				var updateIdDtls=$('#hdnDtlsUpdateId_'+rowNo).val();
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';
				
				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id );
				}
				
				$("#"+tr_id+rowNo).remove();
				$('#'+prefix+'_tot_row').val(total_row-1);
				//calculate_total_amount(1);
			}
			else
			{
				return false;
			}
		}
    </script>
</head>
<body onLoad="set_hotkey()">
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
	<fieldset style="width:700px;margin-left:10px">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" >
                <thead>
                	<tr>
                		<th colspan="5">Test Parameter</th>
                	</tr>
                	<tr>
                    	<th width="90">Item Group</th>
                    	<th width="150">Item Description</th>
                    	<th width="180">Technical Charecteristics</th>
                    	<th width="180">Standard Value</th>
                    	<th></th>
                	</tr>
                </thead>
            </table>
            <div style="width:700px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_dtls_emb" >
                	<tbody>
	                	<?
	                	$data=explode("_",$data); $i=1;
	                	$group_array=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	                	//$group_desc=return_field_value("item_group_id || '_' || item_description AS group_desc","product_details_master", "id=$data[0]","group_desc" );
	                	$sqldtls=sql_select("select id,item_id,tech_charecteristics,standard_value from  product_details_test_parameter where item_id=$data[0] and status_active=1 and is_deleted=0");
	                	if(count($sqldtls)>0)
	                	{
	                		foreach ($sqldtls as $row)
	                		{
	                			?>
			                	<tr id="row_<? echo $i; ?>" align="center">
									<td width="90"><p><? echo $group_array[$data[1]]; ?></p></td>
									<td width="150"><p><? echo $data[2]; ?></p></td>
									<td width="180">
										<input type="text" name="txtTechChar[]" id="txtTechChar_<?php echo $i ?>" class="text_boxes" value="<? echo $row[csf("tech_charecteristics")]; ?>" style="width:167px"/>
									</td>
									<td width="180">
										<input type="text" name="txtStandard[]" id="txtStandard_<?php echo $i ?>" class="text_boxes" value="<? echo $row[csf("standard_value")]; ?>"  style="width:167px"/>
									</td>
									<td> 
						               	<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<? echo $i; ?>,'tbl_dtls_emb','row_')" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $i; ?>,'tbl_dtls_emb','row_');" />
										<input id="hdnDtlsUpdateId_<? echo $i; ?>" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("id")]; ?>" />
						                <!-- <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />  --> 
						            </td> 
								</tr>
								<?
								$i++;
	                		}
	                	}
	                	else
	                	{
	                		?>
							<tr id="row_<? echo $i; ?>" align="center">
								<td width="90"><p><? echo $group_array[$data[1]]; ?></p></td>
								<td width="150"><p><? echo $data[2]; ?></p></td>
								<td width="180">
									<input type="text" name="txtTechChar[]" id="txtTechChar_<?php echo $i ?>" class="text_boxes" value="" style="width:167px"/>
								</td>
								<td width="180">
									<input type="text" name="txtStandard[]" id="txtStandard_<?php echo $i ?>" class="text_boxes" value=""  style="width:167px"/>
								</td>
								<td> 
					               	<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<? echo $i; ?>,'tbl_dtls_emb','row_')" />
									<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $i; ?>,'tbl_dtls_emb','row_');" />
									<input id="hdnDtlsUpdateId_<? echo $i; ?>" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" value=""; />
					                <!-- <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />  --> 
					            </td> 
							</tr>
							<?
	                	}
	                    ?>
                    </tbody>
                </table>
            </div>
            <table width="700" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="">
            <tr>
                <td align="center" colspan="5" valign="middle" class="button_container">
                <? 
                //echo load_submit_buttons($permission, "fnc_parameter_save", 0,0,"",2);

                if(count($sqldtls)>0)
                {
                	echo load_submit_buttons($permission, "fnc_parameter_save", 1,0,"",2);
                }
                else
                {
                	echo load_submit_buttons($permission, "fnc_parameter_save", 0,0,"",2);
                }
				//echo load_submit_buttons( $permission, "fnc_reject_operationnnnnn",0,1,"",2);
				?>
                <input type="hidden" id="mst_update_id" name="mst_update_id" value="<?php echo $data[0]; ?>" readonly />
                <input type="hidden" name="txt_deleted_id[]" id="txt_deleted_id" class="text_boxes_numeric" readonly />	
            </tr>
        </table>
        </form>
    </fieldset>
</div>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>    
</body>           

<!-- <script>
	set_all();
</script> -->
</html>
<?
exit();
}


if ($action=="save_update_delete_parameter")
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
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$id=return_next_id( "id", "product_details_test_parameter", 1 );
		$field_array= "id,item_id,tech_charecteristics,standard_value,inserted_by,insert_date,status_active,is_deleted";
		$item_dup_chk_arr=array();
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtTechChar			= "txtTechChar_".$i;
			$txtStandard			= "txtStandard_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$item=str_replace("'","",$$txtTechChar)."_".str_replace("'","",$$txtStandard)."_".str_replace("'","",$mst_update_id);

			if(!in_array($item, $item_dup_chk_arr, true))
			{
        		array_push( $item_dup_chk_arr, $item);
    		}
    		else
    		{
    			echo "26**"; disconnect($con);die;
    		}
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$mst_update_id.",".$$txtTechChar.",".$$txtStandard.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id++;
		}
		//echo "10**INSERT INTO lib_booked_uom_setup(".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("product_details_test_parameter",$field_array,$data_array,0);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID){
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
			if($rID)
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
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "product_details_test_parameter", 1 );
		$field_array_update="item_id*tech_charecteristics*standard_value*update_by*update_date";
		$field_array= "id,item_id,tech_charecteristics,standard_value,inserted_by,insert_date,status_active,is_deleted";
		$item_dup_chk_arr=array();
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtTechChar			= "txtTechChar_".$i;
			$txtStandard			= "txtStandard_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$item=str_replace("'","",$$txtTechChar)."_".str_replace("'","",$$txtStandard)."_".str_replace("'","",$mst_update_id);
			if(!in_array($item, $item_dup_chk_arr, true))
			{
        		array_push( $item_dup_chk_arr, $item);
    		}
    		else
    		{
    			echo "26**"; disconnect($con); die;
    		}
			
			if(str_replace("'","",$$hdnDtlsUpdateId)!="")
			{
				$id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
				$data_array_update[str_replace("'",'',$$hdnDtlsUpdateId)] = explode("*",("'".str_replace("'","",$mst_update_id)."'*'".str_replace("'","",$$txtTechChar)."'*'".str_replace("'","",$$txtStandard)."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
			else
			{
				if ($i!=1) $data_array .=",";
				$data_array .="(".$id.",".$mst_update_id.",".$$txtTechChar.",".$$txtStandard.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$id=$id+1;
			}
		}
		//echo "10**vhv"; die;
		$rID=true; $rID2=true; $rID3=true; unset($item_dup_chk_arr);
		if(count($data_array_update)>0)
		{
			//echo "10**".bulk_update_sql_statement( "product_details_test_parameter", "id", $field_array_update, $data_array_update, $id_arr ); die;
			$rID=execute_query(bulk_update_sql_statement( "product_details_test_parameter", "id", $field_array_update, $data_array_update, $id_arr ));
		}

		if($data_array!="")
		{
			$rID2=sql_insert("product_details_test_parameter",$field_array,$data_array,0);
		}

		if($txt_deleted_id!="")
		{
			$field_array_status="update_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID3=sql_multirow_update("product_details_test_parameter",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}

		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "1**".$rID;
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
		
		//}
		
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array_status="update_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID=sql_multirow_update("product_details_test_parameter",$field_array_status,$data_array_status,"item_id",$mst_update_id,0);

		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		 if($rID)
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
	}
}

?>