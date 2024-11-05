<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
$company_cond1=set_user_lavel_filtering(' and company_id','company_id');

if ($action=="load_drop_down_buyer_search")
{
    if($data != 0){
        echo create_drop_down( "cbo_buyer_name", 142, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "" );
        exit();
    }
    else{
        echo create_drop_down( "cbo_buyer_name", 142, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "" );
        exit();
    }
}

if($action=="last_count_invoice")
{
	$data=explode("**",$data);
	//and BUYER_ID=$data[1]
    $sql="SELECT ID, INVOICE_NO from lib_invoice_creation where status_active =1 and is_deleted=0 and COMPANY_ID=$data[0] and INVOICE_YEAR=$data[2] order by ID desc";
    $data=sql_select($sql);
    echo  $invoice_count =  ($data[0]["INVOICE_NO"]!="")?$data[0]["INVOICE_NO"]:0;
	exit();	
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $count_of_invoice = str_replace("'","",$count_of_invoice);

    if ($operation==0)  // Insert Here
    {
		$con = connect();
 
        $user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
        $company_short_name = return_field_value("company_short_name", "lib_company", "id=$cbo_company_name", "company_short_name");
        $buyer_short_name = return_field_value("short_name", "lib_buyer", "id=$cbo_buyer_name", "short_name");
  
        $field_array = "id, invoice_no, company_id, buyer_id, invoice_year, invoice_count, invoice_status, inserted_by, insert_date, status_active, is_deleted";

        $count_of_invoice = $count_of_invoice*1;
        $id = return_next_id("id", "lib_invoice_creation", 1);

        // echo "10**".$txt_year."__".""; disconnect($con); die;  

        if($txt_year!=""){
            $sql_con=sql_select("SELECT ID, TO_NUMBER(REGEXP_SUBSTR(INVOICE_NO, '\d{5}$')) AS INVOICE_NO FROM lib_invoice_creation WHERE INVOICE_YEAR=$txt_year ORDER BY TO_NUMBER(REGEXP_SUBSTR(INVOICE_NO, '\d+$')) DESC
            FETCH FIRST 1 ROW ONLY");
                if($sql_con[0]["ID"]==""){
                    $new_id=1;
                }else{
                    $new_id=$sql_con[0]["INVOICE_NO"];
                    $new_id++;
                }
        }
        // echo  "10**".$sql_con[0]["ID"]."__".$sql_con[0]["INVOICE_NO"]."__".$new_id."__"."SELECT ID, TO_NUMBER(REGEXP_SUBSTR(INVOICE_NO, '\d{5}$')) AS INVOICE_NO FROM lib_invoice_creation WHERE INVOICE_YEAR=$txt_year ORDER BY TO_NUMBER(REGEXP_SUBSTR(INVOICE_NO, '\d+$')) DESC FETCH FIRST 1 ROW ONLY"; disconnect($con); die; 
        $data_array='';
        for ($i=1; $i <=  $count_of_invoice;$i++) {                    
            // Invoice No SET            
            $invoice_no = $company_short_name . "/" . $buyer_short_name . "/" . substr(str_replace("'", "", $txt_year), 2, 4) . "/" . str_pad($new_id,5,"0",STR_PAD_LEFT);;

            if($data_array != ''){$data_array .=',';}
            $data_array .= "(" . $id . ",'" . $invoice_no . "'," . $cbo_company_name . "," . $cbo_buyer_name . "," . $txt_year . "," . $i . "," . $cbo_invoice_status . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
            $id++; 
            $new_id++;
        }
        // echo "10**insert into lib_invoice_creation ($field_array) values $data_array"; die;disconnect($con);
        $rID = sql_insert("lib_invoice_creation", $field_array, $data_array, 1);

        if($rID)
        {
            oci_commit($con);
            echo "0**".$invoice_no."**".$cbo_buyer_name."**".$cbo_company_name;
        }
        else{
            oci_rollback($con);
            echo "10**".$rID;
        }
        disconnect($con); die;         
    }
    elseif ($operation == 1)   // Update Here
    {
        $con = connect();

        $field_array_update = "invoice_status*updated_by*update_date";
        $data_array_update = "" . $cbo_invoice_status . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "' ";

        $rID = sql_update("lib_invoice_creation", $field_array_update, $data_array_update, "id", "" .$update_id."");

        if ($rID) {
            oci_commit($con);
            echo "1**".$invoice_no."**".$cbo_buyer_name."**".$cbo_company_name;
        } else {
            oci_rollback($con);
            echo "10**".$rID;
        }
        disconnect($con); die;           
    }
}

if ($action == "show_list_view")
{
    $data=explode("_",$data);
	$buyer_name=return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0","id","buyer_name");
	$company_name=return_library_array("select id, company_name from lib_company where status_active = 1 and is_deleted = 0","id","company_name");
	$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	//and BUYER_ID=$data[1]  
    $sql = "SELECT ID, COMPANY_ID, BUYER_ID, INVOICE_YEAR, INSERT_DATE, INVOICE_NO, INVOICE_STATUS, INSERTED_BY, INVOICE_STATUS from lib_invoice_creation where STATUS_ACTIVE=1 and IS_DELETED=0 and COMPANY_ID=$data[0] order by id desc";
	$result = sql_select($sql);
    ?>
    <div style="width:900px;" >	
        <table class="rpt_table"  rules="all" width="880" cellspacing="0" cellpadding="0" border="1">
            <thead>
                <tr>
                    <th width="50">SL No</th>
                    <th width="150">Company</th>
                    <th width="150"> Buyer</th>
                    <th width="80"> Invoice Year</th>
                    <th width="120"> Invoice No</th>
                    <th width="120"> Insert By</th>
                    <th width="70"> Insert Date</th>
                    <th width="70">Status</th>
                </tr>
            </thead>
        </table>
        <div style="width:900px; max-height:220px; overflow-y:scroll" align="left" id="scroll_body">
            <table class="rpt_table" id="table_body"  rules="all" width="880" cellspacing="2" cellpadding="0" border="1">
                <tbody style="height: 400px;">
                    <?
                    $sl = 1;
                    foreach($result as $row)
                    {
                        $bgcolor=($sl%2==0)? "#E9F3FF":"#FFFFFF";
                        ?>
                        <tr style="cursor:pointer" bgcolor="<?=$bgcolor; ?>" onClick="get_php_form_data('<? echo $row[csf('id')]."_".$row[csf('company_id')]; ?>','populate_invoice_details_form_data', 'requires/invoice_creation_system_controller');">
                            <td width="50"><? echo $sl; ?></td>
                            <td width="150"><? echo $company_name[$row['COMPANY_ID']]; ?></td>
                            <td width="150"><? echo $buyer_name[$row['BUYER_ID']]; ?></td>
                            <td width="80"><? echo $row['INVOICE_YEAR']; ?></td>
                            <td width="120"><? echo $row['INVOICE_NO']; ?></td>
                            <td width="120"><? echo $user_arr[$row['INSERTED_BY']]; ?></td>
                            <td width="70"><? echo change_date_format($row['INSERT_DATE']); ?></td>
                            <td width="70"><? echo $row_status[$row['INVOICE_STATUS']]; ?></td>
                        </tr>
                        <?
                        $sl++;
                    }
                    ?>
                </tbody>
            </table>
       </div>
    </div>
    <script type="text/javascript">
		setFilterGrid('table_body',-1);
        set_button_status(0, ".$_SESSION['page_permission'].", 'fnc_invoice_creation',0,0);
	</script>
    <?
    exit();
}

if($action=='populate_invoice_details_form_data')
{
	$data=explode("_", $data);
	$data_array=sql_select("select id, company_id, buyer_id, invoice_year, insert_date, invoice_count, invoice_no, invoice_status, inserted_by from lib_invoice_creation where status_active=1 and is_deleted=0 and company_id=$data[1] and id=$data[0]");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_invoice_status').value 		= '".$row[csf("invoice_status")]."';\n";
		echo "document.getElementById('txt_last_invoice_no').value 		= '".$row[csf("invoice_no")]."';\n";
		echo "document.getElementById('update_id').value 		= '".$row[csf("id")]."';\n";
        echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_invoice_creation',1,1);\n"; 
		exit();
	}
}


