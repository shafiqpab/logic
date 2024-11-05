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
            $sql="select id,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter from lib_item_group where is_deleted=0 $item_category_list";
            $arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$cal_parameter);
            echo  create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Cal Parameter", "150,100,200,80,50,50,50","900","320",0, $sql, "js_set_value", "id", "'load_php_popup_to_form'", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,cal_parameter", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter", "item_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0' );
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
		$arr=array(1=>$item_category,6=>$unit_of_measurement,7=>$company_array,8=>$row_status);
	 	$sql="select a.id,a.item_account,a.item_category_id,a.sub_group_name,a.item_description, a.product_name_details,a.item_size,a.unit_of_measure,a.company_id,a.status_active,a.brand_name,a.model,a.origin,b.item_name from product_details_master a, lib_item_group b where a.is_deleted=0 and b.is_deleted=0 and a.item_group_id=b.id and a.company_id='$data[1]'  $item_cat_cond $item_group_cond $entry_cond";
		//echo $sql;
		echo  create_list_view ( "list_view", "Item Account,Item Category,Group Name,Sub Group Name,Item Description,Item Size,Cons UOM,Company,Status", "100,130,150,140,170,80,80,110,100","1110","320",1, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,item_category_id,0,0,0,0,unit_of_measure,company_id,status_active", $arr ,"item_account,item_category_id,item_name,sub_group_name,item_description,item_size,unit_of_measure,company_id,status_active", "requires/item_creation_controller", 'setFilterGrid("list_view",-1);' );
		}																																																 


if ($action=="load_php_data_to_form")
{
	/*$nameArray=sql_select("select a.id,a.company_id,a.item_category_id,a.item_group_id,a.sub_group_code,a.sub_group_name,a.item_code,a.item_description,a.item_size,a.re_order_label,a.minimum_label,a.maximum_label,a.unit_of_measure,a.item_account,b.id as group_id,b.item_name,b.item_group_code,b.order_uom,a.status_active,a.brand_name,a.origin from product_details_master a,lib_item_group b where a.item_group_id=b.id and a.id='$data'");
	*/
	/*.....additional code-----*/
	$nameArray=sql_select("select a.id,a.company_id,a.item_category_id,a.item_group_id,a.sub_group_code,a.sub_group_name,a.item_code,a.item_description,a.item_size,a.re_order_label,a.minimum_label,a.maximum_label,a.unit_of_measure,a.item_account,b.id as group_id,b.item_name,b.item_group_code,b.order_uom,a.status_active,a.brand_name,a.model,a.origin from product_details_master a,lib_item_group b where a.item_group_id=b.id and a.id='$data'");
	
	foreach ($nameArray as $inf)
	{
		
		$nameArray1=sql_select( "select id,product_id from inv_purchase_requisition_dtls where product_id=$data and status_active=1 and is_deleted=0" ); 
	 	foreach ($nameArray1 as $row)
	  	{
		 $item_table_id=$row['product_id'];
		}
		/*if($inf[csf("status_active")]==1)
		{
		$trans_id=return_field_value("id", "inv_transaction", "prod_id=".$data." and status_active=1 and is_deleted=0 ","id");
		}
		*/
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
			echo "document.getElementById('item_group_id').value  				= '".($inf[csf("group_id")])."';\n";
			echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
                        echo "document.getElementById('txt_brand').value  = '".($inf[csf("brand_name")])."';\n";
            /*-----additional code------*/         
            echo "document.getElementById('txt_model_name').value  = '".($inf[csf("model")])."';\n";            
                        echo "document.getElementById('cbo_origin').value  = '".($inf[csf("origin")])."';\n";
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
			echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
                        echo "document.getElementById('txt_brand').value  = '".($inf[csf("brand_name")])."';\n";
            /*------additional code------*/            
            echo "document.getElementById('txt_model_name').value  = '".($inf[csf("model")])."';\n";            
                        echo "document.getElementById('cbo_origin').value  = '".($inf[csf("origin")])."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_item_creation',1);\n"; 
		}
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
	
	$item_cat=str_replace("'","",$cbo_item_category); 
	$item_group=str_replace("'","",$item_group_id);
	$group_categry=return_field_value("item_category","lib_item_group","status_active=1 and is_deleted=0 and id=$item_group","item_category");
	if($item_cat!=$group_categry)
	{
		echo "11**Item Group Category and Item Category Not Match.";die;
	}
	
	
	if ($operation==0)  // Insert Here=======================================================
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
			
			$duplicate = is_duplicate_field("id","product_details_master","company_id=$cbo_company_name and item_category_id=$cbo_item_category and item_group_id=$item_group_id $duplicate_cond and is_deleted=0 "); 
			//and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size
		}
		else 
		{
			
			$duplicate = is_duplicate_field("id","product_details_master","company_id=$cbo_company_name and item_category_id=$cbo_item_category and item_group_id=$item_group_id and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size and is_deleted=0 "); 
		}
		 
		if($duplicate==1) 
		{
			echo "11**Duplicate Product is Not Allow in Same Return Number.";
			die;
		}
		
		$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');	
		$productname = $item_group_arr[str_replace("'", '', $item_group_id)]." ".str_replace("'", '', $txt_description)." ".str_replace("'", '', $txt_item_size);
		if(str_replace("'","",$cbo_item_category)==4) $entry_form_lib=20; else $entry_form_lib=0;
		//$id=return_next_id("id","product_details_master",1);
		$id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
		
		/*$field_array="id,company_id,item_category_id,entry_form,item_group_id,sub_group_code,sub_group_name,item_code,item_description,product_name_details,item_size,re_order_label,minimum_label,maximum_label,unit_of_measure,item_account,inserted_by,insert_date,status_active,brand_name,origin,is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_item_category.",".$entry_form_lib.",".$item_group_id.",".$txt_subgroup_code.",".$txt_subgroup_name.",".$txt_item_code.",".$txt_description.",'".$productname."',".$txt_item_size.",".$txt_reorder_label.",".$txt_min_label.",".$txt_max_label.",".$cbo_cons_uom.",".$txt_item_account.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",".$txt_brand.",".$cbo_origin.",0)";*/
		/*-------additional code-------*/
		
		$field_array="id,company_id,item_category_id,entry_form,item_group_id,sub_group_code,sub_group_name,item_code,item_description,product_name_details,item_size,re_order_label,minimum_label,maximum_label,unit_of_measure,item_account,inserted_by,insert_date,status_active,brand_name,model,origin,is_deleted";
		$data_array="(".$id.",".$cbo_company_name.",".$cbo_item_category.",".$entry_form_lib.",".$item_group_id.",".$txt_subgroup_code.",".$txt_subgroup_name.",".$txt_item_code.",".$txt_description.",'".$productname."',".$txt_item_size.",".$txt_reorder_label.",".$txt_min_label.",".$txt_max_label.",".$cbo_cons_uom.",".$txt_item_account.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",".$txt_brand.",".$txt_model_name.",".$cbo_origin.",0)";
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
			
			$duplicate = is_duplicate_field("id","product_details_master","company_id=$cbo_company_name and item_category_id=$cbo_item_category and item_group_id=$item_group_id and id<>$update_id $duplicate_cond and is_deleted=0 "); 
			//and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size
		}
		else 
		{
			
			$duplicate = is_duplicate_field("id","product_details_master","company_id=$cbo_company_name and item_category_id=$cbo_item_category and item_group_id=$item_group_id and id<>$update_id and sub_group_name=$txt_subgroup_name and item_description=$txt_description and item_size=$txt_item_size and is_deleted=0 "); 
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
		
		if(str_replace("'","",$cbo_item_category)==4) $entry_form_lib=20; else $entry_form_lib=0;
		$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');	
		$productname = $item_group_arr[str_replace("'", '', $item_group_id)]." ".str_replace("'", '', $txt_description)." ".str_replace("'", '', $txt_item_size);
		
		/*$field_array="company_id*item_category_id*entry_form*sub_group_code*sub_group_name*item_code*item_description*product_name_details*item_size*re_order_label*minimum_label*maximum_label*unit_of_measure*item_account*updated_by*update_date*status_active*brand_name*origin*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_item_category."*".$entry_form_lib."*".$txt_subgroup_code."*".$txt_subgroup_name."*".$txt_item_code."*".$txt_description."*'".$productname."'*".$txt_item_size."*".$txt_reorder_label."*".$txt_min_label."*".$txt_max_label."*".$cbo_cons_uom."*".$txt_item_account."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*".$txt_brand."*".$cbo_origin."*0";*/
		/*-------additional code------*/
		
		$field_array="company_id*item_category_id*entry_form*sub_group_code*sub_group_name*item_code*item_description*product_name_details*item_size*re_order_label*minimum_label*maximum_label*unit_of_measure*item_account*updated_by*update_date*status_active*brand_name*model*origin*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_item_category."*".$entry_form_lib."*".$txt_subgroup_code."*".$txt_subgroup_name."*".$txt_item_code."*".$txt_description."*'".$productname."'*".$txt_item_size."*".$txt_reorder_label."*".$txt_min_label."*".$txt_max_label."*".$cbo_cons_uom."*".$txt_item_account."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*".$txt_brand."*".$txt_model_name."*".$cbo_origin."*0";
		
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
	
	$sql_dtls="select id, company_id, item_category_id, item_group_id, sub_group_code, sub_group_name, item_code, item_description, item_size from product_details_master where status_active=1 and company_id=$data_value[0] and item_category_id=$data_value[1] and item_group_id=$data_value[2] $sub_group_code and sub_group_name like '$data_value[4]'  $item_code  and item_description like '$data_value[6]' and item_size like '$data_value[7]'";
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

?>