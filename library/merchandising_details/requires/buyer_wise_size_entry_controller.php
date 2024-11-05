<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer wise Size					
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy 
Creation date 	: 	17-02-2024	
Comments		:
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_dept", 150, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id=$data[0] and	department_id='$data[1]' and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Sub Dep --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 150, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if ($action=="color_list_view")
{
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
    $pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
    $arr=array (0=>$buyer_arr,1=>$brand_arr,2=>$product_dept,3=>$pro_sub_dept_array,6=>$row_status);
    echo  create_list_view ( "list_view", "Buyer,Brand,Product Dept.,Sub Dept.,Size Name,Sequence,Status", "150,150,150,150,150,100,100","950","220",0, "select  buyer_id, brand_id, product_dept, pro_sub_dep,size_name,sequence,status_active,id from   buyer_wise_size where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "buyer_id,brand_id,product_dept,pro_sub_dep,0,0,status_active", $arr , "buyer_id,brand_id,product_dept,pro_sub_dep,size_name,sequence,status_active", "requires/buyer_wise_size_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
}
if ($action=="load_php_data_to_form")
{
	
	$nameArray=sql_select( "SELECT buyer_id, brand_id, product_dept, pro_sub_dep,size_name,sequence,status_active,id from buyer_wise_size where is_deleted=0 and id='$data'" );
	foreach ($nameArray as $inf)
	{
        $sub_dept_data=$inf[csf("buyer_id")].'_'.$inf[csf("product_dept")];
		echo "document.getElementById('cbo_buyer_name').value = '".($inf[csf("buyer_id")])."';\n"; 
        echo "load_drop_down( 'requires/buyer_wise_size_entry_controller', '".$inf[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td' ) ;\n";
		echo "document.getElementById('cbo_brand_id').value = '".($inf[csf("brand_id")])."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".($inf[csf("product_dept")])."';\n"; 
        echo "load_drop_down( 'requires/buyer_wise_size_entry_controller', '".$sub_dept_data."', 'load_drop_down_sub_dep', 'sub_td' ) ;\n"; 
        echo "document.getElementById('cbo_sub_dept').value = '".($inf[csf("pro_sub_dep")])."';\n";  
		echo "document.getElementById('txt_size_name').value = '".($inf[csf("size_name")])."';\n";  		
		echo "document.getElementById('txt_sequence').value = '".($inf[csf("sequence")])."';\n";   
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 		 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_size_info',1);\n";  
	}
}

if ($action=="save_update_delete")
{  

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$str_replace_check=array("<?","?>","::","_","&", "*", "(", ")", "=","  ","'","\r", "\n",'"','#');
	if ($operation==0)
    {
        if(is_duplicate_field( "size_name", " buyer_wise_size", "LOWER(size_name)=LOWER($txt_size_name) and buyer_id=$cbo_buyer_name and brand_id=$cbo_brand_id and product_dept=$cbo_product_department and pro_sub_dep=$cbo_sub_dept  and is_deleted=0" ) == 1)
        {
            echo "11**0"; die;
        }
        else
        {
            $con = connect();
            $id=return_next_id( "id", "buyer_wise_size", 1 ) ;
            $field_array="id, buyer_id, brand_id, product_dept, pro_sub_dep, size_name, sequence, inserted_by, insert_date, status_active, is_deleted";
            $txt_size_name="'".trim(str_replace($str_replace_check,' ',$txt_size_name))."'";
            $data_array="(".$id.",".$cbo_buyer_name.",".$cbo_brand_id.",".$cbo_product_department.",".$cbo_sub_dept.",".trim(strtoupper($txt_size_name)).",".$txt_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";				
            $rID=sql_insert("buyer_wise_size",$field_array,$data_array,0);			
            //----------------------------------------------------------------------------------
            
            if($db_type==2 || $db_type==1 )
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
    }		
	else if ($operation==1)
	{
		
		if(is_duplicate_field( "size_name", " buyer_wise_size", "LOWER(size_name)=LOWER($txt_size_name) and buyer_id=$cbo_buyer_name and brand_id=$cbo_brand_id and product_dept=$cbo_product_department and pro_sub_dep=$cbo_sub_dept  and is_deleted=0 and id<>$update_id" ) == 1)
        {
            echo "11**0"; die;
        }
        else
        {
            $con = connect();
            $field_array="buyer_id*brand_id*product_dept*pro_sub_dep*size_name*sequence*updated_by*update_date*status_active";
            $data_array="".$cbo_buyer_name."*".$cbo_brand_id."*".$cbo_product_department."*".$cbo_sub_dept."*".trim(strtoupper($txt_size_name))."*".$txt_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
            $rID=sql_update("buyer_wise_size",$field_array,$data_array,"id","".$update_id."",0);

            if($db_type==2 || $db_type==1 )
            {
                if($rID)
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
        }
	}
    else if ($operation==2)
    {
        
        $con = connect();		
        $field_array="updated_by*update_date*status_active*is_deleted";
        $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";		
        $rID=sql_delete("buyer_wise_size",$field_array,$data_array,"id","".$update_id."",1);
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
    }		
	
}


?>