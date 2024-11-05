<?php
include('../../../../includes/common.php');  
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_party")
{
    $data=explode("_",$data);
    if($data[1]==1)
    {
        echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "$load_function");
    }
    else
    {
        echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
    }   
    exit();  
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 

    if ($operation==0) // Insert Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $cbo_company_name           = str_replace("'",'',$cbo_company_name);
        $cbo_within_group           = str_replace("'",'',$cbo_within_group);
        $cbo_party_name             = str_replace("'",'',$cbo_party_name);
        $txt_remarks                = $txt_remarks;

        if($db_type==0){
            $txt_applicable_up_to_date=change_date_format(str_replace("'",'',$txt_applicable_up_to_date),'yyyy-mm-dd');
        }else{
            $txt_applicable_up_to_date=change_date_format(str_replace("'",'',$txt_applicable_up_to_date), "", "",1);
        }

        $shade_data = return_field_value("id","shade_entry_mst","company_id=$cbo_company_name and applicable_upto_date='$txt_applicable_up_to_date' and is_deleted=0 and status_active=1");

        if($shade_data)
        {
            echo "14**Duplicate Applicable Date Up Date Not Allow!!!";
            die;
        }

        $id=return_next_id("id","shade_entry_mst",1);
        $id1=return_next_id( "id", "shade_entry_dtls",1);

        $rID1=true;

        $field_array1="id, entry_form, company_id, within_group, party_id, applicable_upto_date, remarks, status_active, is_deleted, inserted_by, insert_date";
        
        $field_array2="id, mst_id, color_range_id, lower_limit, uper_limit, price, status_active, is_deleted, inserted_by, insert_date";

        $data_array1="(".$id.", 569, ".$cbo_company_name.", ".$cbo_within_group.", ".$cbo_party_name.", '".$txt_applicable_up_to_date."', '".$txt_remarks."',1,0,".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

        $data_array2=""; $add_commaa=0;
        for($i=1; $i<=$total_row; $i++)
        { 
            $txtItemColor           = "txtItemColor_".$i;
            $shadeLowerLimit        = "shadeLowerLimit_".$i;
            $shadeUperLimit         = "shadeUperLimit_".$i;
            $shadePrice             = "shadePrice_".$i;

            if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

            $data_array2 .="(".$id1.",".$id.",".$$txtItemColor.",".$$shadeLowerLimit.",".$$shadeUperLimit.",".$$shadePrice.",1,0,".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

            $id1=$id1+1; $add_commaa++;
        }

        $flag=true;
        //echo "10**INSERT INTO shade_entry_mst (".$field_array1.") VALUES ".$data_array1; die;
        //echo "10**INSERT INTO shade_entry_dtls (".$field_array2.") VALUES ".$data_array2; die;
        $rID=sql_insert("shade_entry_mst",$field_array1,$data_array1,1);
        if($rID==1) $flag=1; else $flag=0;

        if($flag==1){
            $rID2=sql_insert("shade_entry_dtls",$field_array2,$data_array2,1);
            if($rID2==1) $flag=1; else $flag=0;
        }

        if($db_type==0){
            if($flag==1){
                mysql_query("COMMIT");  
                echo "0**".str_replace("'",'',$id);
            }else{
                mysql_query("ROLLBACK"); 
                echo "10**".str_replace("'",'',$id);
            }
        }else if($db_type==2){
            if($flag==1){
                oci_commit($con);
                echo "0**".str_replace("'",'',$id);
            }else{
                oci_rollback($con);
                echo "10**".str_replace("'",'',$id);
            }
        }
    }

    if ($operation==1) // Update Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $update_id                  = str_replace("'",'',$txt_update_id);
        $txt_deleted_id             = str_replace("'",'',$txt_deleted_id);
        $cbo_company_name           = str_replace("'",'',$cbo_company_name);
        $cbo_within_group           = str_replace("'",'',$cbo_within_group);
        $cbo_party_name             = str_replace("'",'',$cbo_party_name);
        $txt_remarks                = $txt_remarks;

        if($db_type==0){
            $txt_applicable_up_to_date=change_date_format(str_replace("'",'',$txt_applicable_up_to_date),'yyyy-mm-dd');
        }else{
            $txt_applicable_up_to_date=change_date_format(str_replace("'",'',$txt_applicable_up_to_date), "", "",1);
        }

        $shade_data = return_field_value("id","shade_entry_mst","company_id=$cbo_company_name and applicable_upto_date='$txt_applicable_up_to_date' and is_deleted=0 and status_active=1 and id!=$update_id");

        if($shade_data)
        {
            echo "14**Duplicate Applicable Date Up Date Not Allow!!!";
            die;
        }

        $field_array1="company_id*within_group*party_id*applicable_upto_date*remarks*updated_by*update_date";
        
        $field_array2="color_range_id*lower_limit*uper_limit*price*updated_by*update_date*status_active*is_deleted";

        $field_array3="id, mst_id, color_range_id, lower_limit, uper_limit, price, status_active, is_deleted";

        $data_array1="".$cbo_company_name."*".$cbo_within_group."*".$cbo_party_name."*'".$txt_applicable_up_to_date."'*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $id1=return_next_id( "id", "shade_entry_dtls",1);

        $data_array2=array(); $hdn_dtls_id_arr = array(); $add_commaa1=0;$data_array3 ='';
        for($i=1; $i<=$total_row; $i++)
        {
            $txtItemColor           = "txtItemColor_".$i;
            $shadeLowerLimit        = "shadeLowerLimit_".$i;
            $shadeUperLimit         = "shadeUperLimit_".$i;
            $shadePrice             = "shadePrice_".$i;
            $hdnDtlsUpdateId        = "hdnDtlsUpdateId_".$i;

            $dtlsUpdateId =str_replace("'",'',$$hdnDtlsUpdateId);

            if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
            {
                $data_array2[$dtlsUpdateId]=explode("*",("".$$txtItemColor."*".$$shadeLowerLimit."*".$$shadeUperLimit."*".$$shadePrice."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"));
                $hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
            }
            else
            {
                if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

                $data_array3 .="(".$id1.",".$update_id.",".$$txtItemColor.",".$$shadeLowerLimit.",".$$shadeUperLimit.",".$$shadePrice.",1,0)";

                $id1=$id1+1; $add_commaa++;
            }
        }

        $flag==1;
        //echo $update_id."10**INSERT INTO shade_entry_mst (".$field_array1.") VALUES ".$data_array1; die;
        $rID=sql_update("shade_entry_mst",$field_array1,$data_array1,"id",$update_id,0);

        if($rID) $flag=1; else $flag=0;

        if($data_array3!="" && $flag==1)
        {
            
            //echo "10**INSERT INTO shade_entry_dtls (".$field_array3.") VALUES ".$data_array3; die;
            $rID3=sql_insert("shade_entry_dtls",$field_array3,$data_array3,1);
            if($rID3==1) $flag=1; else $flag=0;
        }

        if($txt_deleted_id!="" && $flag==1)
        {
            $field_array_status="updated_by*update_date*status_active*is_deleted";
            $data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

            $rID4=sql_multirow_update("shade_entry_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
            if($flag==1)
            {
                if($rID4) $flag=1; else $flag=0; 
            }
        }

        if($data_array2!="" && $flag==1)
        {
            $rID2=execute_query(bulk_update_sql_statement( "shade_entry_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
            if($rID2) $flag=1; else $flag=0;
        }

        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "1**".str_replace("'",'',$update_id);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".str_replace("'",'',$update_id);
            }
        }
        else if($db_type==2)
        {  
            if($flag==1)
            {
                oci_commit($con);
                echo "1**".str_replace("'",'',$update_id);
            }
            else
            {
                oci_rollback($con);
                echo "10**".str_replace("'",'',$update_id);
            }
        }
        disconnect($con);
        die;
    }
    if ($operation==2) // Update Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");  
        }

        $update_id   = str_replace("'",'',$txt_update_id);

        $sql = "select shade_dtls_id, shade_mst_id from yd_ord_dtls a where is_deleted=0 and status_active=1 and shade_mst_id=$update_id";

        $result = sql_select($sql);

        if(count($result)>0)
        {
            echo "20**Order Entry Found So Delete Not Possible";
            die;
        }

        $flag=0;
        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
        $rID=sql_update("shade_entry_mst",$field_array,$data_array,"id",$update_id,0);

        if($rID) $flag=1; else $flag=0; 
        
        if($flag==1)
        {
            $rID1=sql_update("shade_entry_dtls",$field_array,$data_array,"mst_id",$update_id,1);
            if($rID1) $flag=1; else $flag=0; 
        }

        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "2**".str_replace("'",'',$update_id);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**";
            }
        }
        else if($db_type==2)
        {
            if($rID)
            {
                oci_commit($con);
                echo "2**".str_replace("'",'',$update_id);
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

if ($action=="load_php_data_to_form")
{

    $sql = "select a.id, a.company_id,  a.within_group,  a.party_id,  a.applicable_upto_date,  a.remarks from shade_entry_mst a where a.status_active=1 and a.is_deleted=0 and a.id=$data";

    $result = sql_select($sql);

    foreach ($result as $row)
    {


        echo "document.getElementById('cbo_company_name').value                = '".$row[csf("company_id")]."';\n";
        echo "document.getElementById('cbo_within_group').value               = '".$row[csf("within_group")]."';\n";

       echo "load_drop_down( 'requires/buyer_wise_shade_entry_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );\n";
        
        echo "document.getElementById('txt_applicable_up_to_date').value            = '".change_date_format($row[csf("applicable_upto_date")])."';\n"; 
        echo "document.getElementById('cbo_party_name').value                = ".$row[csf("party_id")].";\n";
        echo "document.getElementById('txt_remarks').value                = '".$row[csf("remarks")]."';\n";
        echo "document.getElementById('txt_update_id').value                = '".$row[csf("id")]."';\n";
        
        $update_id = "'".$row[csf("id")]."'";

        echo "show_list_view(".$update_id.",'buyer_wise_shade_list_view','buyer_wise_shade_entry_details_container','requires/buyer_wise_shade_entry_controller','');\n";

        echo "set_button_status(1, permission, 'fnc_buyer_wise_shade_entry',1);\n";
    }

}

if ($action=="buyer_wise_shade_list_view")
{

    $sql = "select b.id, b.color_range_id,  b.uper_limit,  b.lower_limit, b.price from shade_entry_mst a, shade_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$data";

    $sql1 = "select shade_dtls_id, shade_mst_id from yd_ord_dtls where is_deleted=0 and status_active=1 and shade_mst_id=$data";

    $shade_result = sql_select($sql1);

    $shade_arr = array();

    foreach($shade_result  as $data)
    {
        $shade_arr[$data[csf('shade_dtls_id')]] = $data[csf('shade_dtls_id')];
    }

    $result = sql_select($sql);

    $i=1;


    if(count($result)>0)
    {

        foreach ($result as $row)
        {
            $color_range_id = $row[csf("color_range_id")];

            $disabled = "";
            $disabled1 = "";
            if($shade_arr[$row[csf('id')]]==$row[csf('id')])
            {
                $disabled = "disabled";
                $disabled1 = 1;
            }
            ?>
            <tr id="row_<?php echo $i;?>">
                <td align="center">
                    <?php echo $i;?>
                </td>
                <td align="center">
                    <? echo   create_drop_down( "txtItemColor_".$i, 170, $color_range,"", 1, "-- Select --",$color_range_id,"fnc_check_duplicate(this.value, this.id)",$disabled1,'','','','','','',"txtItemColor[]")   ?>
                </td>
                <td align="center">
                    <input <?php echo $disabled;?> style="width:150px;" onchange="check_shade_limit(this.id);" type="text" id="shadeLowerLimit_<?php echo $i;?>" name="shadeLowerLimit[]" style="width:30px" class="text_boxes_numeric" value="<?php echo $row[csf("lower_limit")];?>" />
                </td>
                <td align="center">
                    <input <?php echo $disabled;?> style="width:150px;" onchange="check_shade_limit(this.id);" type="text" id="shadeUperLimit_<?php echo $i;?>" name="shadeUperLimit[]" class="text_boxes_numeric" value="<?php echo $row[csf("uper_limit")];?>" />
                </td>
                <td align="center">
                    <input <?php echo $disabled;?> onkeyup="set_max_lenght(this.value,this.id);" style="width:150px;" type="text" id="shadePrice_<?php echo $i;?>" name="shadePrice[]" class="text_boxes_numeric" value="<?php echo $row[csf("price")];?>" />
                </td>
                <td align="center" width="50">
                    <input <?php echo $disabled;?> type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<?php echo $i;?>" class="text_boxes_numeric" style="width:50px" value="<?php echo $row[csf("id")];?>"  readonly />

                    <input type="button" id="increase_<?php echo $i;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<?php echo $i;?>,'tbl_dtls_buyer_wise_shade_entry','row_')" />
                    <input <?php echo $disabled;?> type="button" id="decrease_<?php echo $i;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<?php echo $i;?>,'tbl_dtls_buyer_wise_shade_entry','row_');" />
                </td>
            </tr>
            <?php
            $i++;
        }
    }
    else
    {
        ?>
        <tr id="row_1">
            <td align="center">
                1
            </td>
            <td align="center">
                <? echo   create_drop_down( "txtItemColor_1", 170, $color_range,"", 1, "-- Select --",0,"fnc_check_duplicate(this.value, this.id)",0,'','','','','','',"txtItemColor[]")   ?>
            </td>
            <td align="center">
                <input onkeyup="set_max_lenght(this.value,this.id);" style="width:150px;" onchange="check_shade_limit(this.id);" type="text" id="shadeLowerLimit_1" name="shadeLowerLimit[]" style="width:30px" class="text_boxes_numeric" value="" />
            </td>
            <td align="center">
                <input onkeyup="set_max_lenght(this.value,this.id);" style="width:150px;" onchange="check_shade_limit(this.id);" type="text" id="shadeUperLimit_1" name="shadeUperLimit[]" class="text_boxes_numeric" value="" />
            </td>
            <td align="center">
                <input onkeyup="set_max_lenght(this.value,this.id);" style="width:150px;" type="text" id="shadePrice_1" name="shadePrice[]" class="text_boxes_numeric" value="" />
            </td>
            <td align="center" width="50">
                <input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1" class="text_boxes_numeric" style="width:50px"  readonly />

                <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_buyer_wise_shade_entry','row_')" />
                <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_buyer_wise_shade_entry','row_');" />
            </td>
        </tr>
        <?php
    }
}

if ($action=="buyer_wise_shade_details_list_view")
{
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
        $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

        $sql = "select a.id, a.company_id,  a.within_group,  a.party_id,  a.applicable_upto_date,  a.remarks from shade_entry_mst a where a.status_active=1 and a.is_deleted=0";

        $result = sql_select($sql);
        $i=1;
        foreach($result as $data)
        {
            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

            if($data[csf('within_group')]==1)
            {
                $party_name = $company_library[$data[csf('party_id')]];
            }
            else
            {
                $party_name = $party_arr[$data[csf('party_id')]];
            }
    ?>
        <tr style="cursor: pointer;" bgcolor="<? echo $bgcolor; ?>" onclick="get_php_form_data(<?php echo $data[csf('id')];?>,'load_php_data_to_form','requires/buyer_wise_shade_entry_controller')">
            <td align="center" width="35" ><?php echo $i;?></td>
            <td align="center" width="150" ><?php echo $company_library[$data[csf('company_id')]];?></td>
            <td align="center" width="150" ><?php echo $yes_no[$data[csf('within_group')]];?></td>
            <td align="center" width="150" ><?php echo $party_name;?></td>
            <td align="center" width="150" ><?php echo $data[csf('applicable_upto_date')];?></td>
            <td align="center" width="" ><?php echo $data[csf('remarks')];?></td>
        </tr>
    <?php

        $i++;
    }
}