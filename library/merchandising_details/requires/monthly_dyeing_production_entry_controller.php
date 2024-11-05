<?

include('../../../includes/common.php');
session_start();

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$from_date=$_REQUEST['f_date'];
$companyId=$_REQUEST['companyId'];
//$monthArr = array("January"=>"January", "February"=>"February", "March"=>"March","April"=>"April","May"=>"May","June"=>"June","July"=>"July","August"=>"August","September"=>"September","October"=>"October","November"=>"November","December"=>"December");

if ($_SESSION['logic_erp']['user_id'] == "") {
    header('location:login.php');
    die;
}

// duplicate date found
if(isset($from_date)){

     
    $dateArr=explode("-",$from_date);
    $swap=$dateArr[0];$dateArr[0]=$dateArr[1];$dateArr[1]=$swap;
    $from_date=implode("/",$dateArr);
     
    $from_date= date('d/M/Y', strtotime(str_replace('-', '/', $from_date)));
    //$to_date= date('d/m/Y', strtotime(str_replace('-', '/', $to_date)));
    $sql="SELECT a.id, a.company, a.years, a.month, a.from_date, a.to_date, a.p_qty FROM monthly_dyeing_production_entry a WHERE from_date='$from_date' AND company=$companyId AND a.status_active =1 AND a.is_deleted =0  order by a.id";

    $fetch_arr=sql_select($sql) ;
    $length= sizeof($fetch_arr);

    if($length==1){
        echo $length."*".$fetch_arr[0]['ID'];    
    }
    else{
        echo $length;    
    }
    die;
}

if($action=="sub_department_list_view")
{ 
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");     
    //$arr = array(0 => $company_library, 2 => $monthArr);
    $arr = array(0 => $company_library);
    echo  create_list_view("list_view", "Company,Year,From Date,To Date, Production Qty.", "140,70,110,110,70", "600", "220", 1, "SELECT a.id, a.company, a.years, a.from_date, a.to_date, a.p_qty FROM monthly_dyeing_production_entry a WHERE a.status_active =1 AND a.is_deleted =0  order by a.id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company", $arr, "company,years,from_date,to_date,p_qty", "requires/monthly_dyeing_production_entry_controller", 'setFilterGrid("list_view",-1);', '');

}


if ($action=="load_php_data_to_form")
{	 

    //$nameArray=sql_select( "SELECT a.id, a.company, a.years, a.month, a.from_date, a.to_date, a.p_qty FROM monthly_dyeing_production_entry a WHERE id='$data'");
    $nameArray=sql_select( "SELECT a.id, a.company, a.years, a.from_date, a.to_date, a.p_qty FROM monthly_dyeing_production_entry a WHERE id='$data'");
	
	foreach ($nameArray as $inf)
	{
		
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company")])."';\n";    
		echo "document.getElementById('cbo_year').value  = '".($inf[csf("years")])."';\n";
        //echo "document.getElementById('cbo_month_id').value  = '".($inf[csf("month")])."';\n";		 
        $date=explode(" ",$inf[csf("from_date")]);         
		echo "document.getElementById('txt_from_date').value  = '".change_date_format($date[0],"dd-mm-yyyy","-")."';\n";         
        $date=change_date_format($date[0],"dd-mm-yyyy","-");
        $month=explode("-",$date); 
        echo "document.getElementById('cbo_month_id').value  = '".$month[1]."';";         
        $date=explode(" ",$inf[csf("to_date")]);
        echo "document.getElementById('txt_to_date').value  = '".change_date_format($date[0],"dd-mm-yyyy","-")."';\n";		 
        //echo "document.getElementById('txt_to_date').value  = '".change_date_format("10-12-2023","dd-mm-yyyy","-")."';\n";		 
		echo "document.getElementById('txt_P_Qty').value = ".($inf[csf("p_qty")]).";\n"; 
        echo "document.getElementById('update_id').value = ".($inf[csf("id")]).";\n";       		 

        echo "document.getElementById('cbo_company_name').disabled = '".true."';";    
        echo "document.getElementById('cbo_year').disabled = '".true."';";    
        echo "document.getElementById('cbo_month_id').disabled = '".true."';";    
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_monthly_dyeing_production_entry',1);\n";  
	}

}


if ($action == "save_update_delete") {

 
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) {
        $con = connect();

        $id_mst = return_next_id("id", "monthly_dyeing_production_entry", 1);
        $fied_array = "id, company, years, from_date, to_date, p_qty, inserted_by,insert_date,status_active,is_deleted";
        $data_array = "(" . $id_mst . "," . $cbo_company_name . "," . $cbo_year . "," . $txt_from_date ."," . $txt_to_date ."," . $txt_P_Qty . "," . $_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0 )";
       

       
      //echo "10**insert into  monthly_dyeing_production_entry (".$fied_array.") Values ".$data_array;die;
        $rID = sql_insert("monthly_dyeing_production_entry", $fied_array, $data_array, 1);
        
        if ($rID ) {
            oci_commit($con);
            echo "0**" . $id_mst;
        } else {
            oci_rollback($con);
            echo "10**" . $id_mst;
        }

        disconnect($con);
        die;
    }
    
    elseif ($operation == 1) {
        $con = connect();
        $field_array = "company*years*from_date*to_date*p_qty*updated_by*update_date";         
        $data_array = "" . $cbo_company_name . "*" . $cbo_year . "*" . $txt_from_date ."*" . $txt_to_date ."*" . $txt_P_Qty . "*" . $_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";



        $rID = sql_update("monthly_dyeing_production_entry", $field_array, $data_array, "id", "" . $update_id . "", 1);

        // echo "UPDATE bundle_instruction_mst SET " . $field_array . " = " . $data_array . " WHERE id = " . $update_id;
        // die;
         
        
        if ($rID) {
            oci_commit($con);
            echo "1**" . $id_mst;
        } else {
            oci_rollback($con);
            echo "10**" . $id_mst;
        }

        disconnect($con);
        die;
    } 
    
    // delete data
    /*elseif ($operation == 2) {
        $con = connect();
        $field_array = "updated_by*update_date*status_active*is_deleted";
        $data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";
        $rID = sql_delete("bundle_instruction_mst", $field_array, $data_array, "id", "" . $update_id . "", 1);

        if ($rID) {
            oci_commit($con);
            echo "2**" . $rID;
        } else {
            oci_rollback($con);
            echo "10**" . $rID;
        }

        disconnect($con);
        die;
    }*/
}

 
