<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}


$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
$country_library = return_library_array("select id,country_name from lib_country", "id", "country_name");

if ($action == "load_drop_down_location") {
    echo create_drop_down("cbo_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "", 0);
    exit();
}

if ($action=="load_drop_down_working_location")
{
    echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
    exit();   
}

if ($action == "load_drop_down_floor") {
    /*  echo create_drop_down( "cbo_floor", 180, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (9) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', $('#cbo_company_name').val()+'_'+$('#cbo_location').val()+'_'+this.value, 'load_drop_down_line', 'line_td' );",0 );
	*/
    echo create_drop_down("cbo_floor", 180, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', $('#cbo_emb_company').val()+'_'+$('#cbo_location').val()+'_'+this.value+'_'+$('#txt_issue_date').val(), 'load_drop_down_line', 'line_td' );", 0);

    exit();
}


if ($action == "load_drop_down_line") {
    
    list($company_id, $location, $floor,$issue_date) = explode("_", $data);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");

    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    $cond = "";
    
    if ($prod_reso_allocation == 1) {
        $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
        $line_array = array();

        if ($floor == 0 && $location != 0) $cond = " and a.location_id= $location";
        if ($floor != 0) $cond = " and a.floor_id= $floor";

        
        
        if($db_type==0)
        {
            $issue_date = date("Y-m-d",strtotime($issue_date));
        }
        else
        {
            $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1);
        }
        
        $cond.=" and b.pr_date='".$issue_date."'";


        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.prod_resource_num asc, a.id asc");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.prod_resource_num asc, a.id asc");
        }
        
        
         $line_merge=9999; 
        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if(count($line_number)>1)
                {
                    $line_merge++;
                    $new_arr[$line_merge]=$row[csf('id')];
                }
                else
                {
                    if($new_arr[$line_library[$val]])
                    $new_arr[$line_library[$val]." "]=$row[csf('id')];
                    else
                        $new_arr[$line_library[$val]]=$row[csf('id')];
                } 
                    
                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }
        ksort($new_arr);
        foreach($new_arr as $key=>$v)
        {
            $line_array_new[$v]=$line_array[$v];
        }
        echo create_drop_down( "cbo_line_no", 180,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
        
    } else {
        if ($floor == 0 && $location != 0) $cond = " and location_name= $location";
        if ($floor != 0) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

        echo create_drop_down("cbo_line_no", 160, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name", "id,line_name", 1, "--- Select ---", $selected, "", 0, 0);
    }
    exit();
}


if ($action == "load_variable_settings") {
    echo "$('#sewing_production_variable').val(0);\n";
    $sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
    foreach ($sql_result as $result) {
        echo "$('#sewing_production_variable').val(" . $result[csf("printing_emb_production")] . ");\n";
        echo "$('#styleOrOrderWisw').val(" . $result[csf("production_entry")] . ");\n";
    }

    $delivery_basis = return_field_value("cut_panel_delevery", "variable_settings_production", "company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
    if ($delivery_basis == 3 || $delivery_basis == 2) $delivery_basis = 3; else $delivery_basis = 1;
    echo "$('#delivery_basis').val(" . $delivery_basis . ");\n";
    exit();
}

if ($action=="load_variable_settings_for_working_company")
{
    $sql_result = sql_select("select working_company_mandatory from variable_settings_production where company_name=$data and variable_list=41 and status_active=1");
    
    $working_company="";
    foreach($sql_result as $row)
    {
        $working_company=$row[csf("working_company_mandatory")];
    }
    echo $working_company;
    
    exit();
}


/*
if ($action == "load_drop_down_embro_issue_source") {
    $explode_data = explode("**", $data);
    $data = $explode_data[0];
    $selected_company = $explode_data[1];

    if ($data == 3) {
        if ($db_type == 0) {
            echo create_drop_down("cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' );");
        } else {
            echo create_drop_down("cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' );");
        }
    } else if ($data == 1)
        echo create_drop_down("cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' );", 0, 0);
    else
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' );", 0);

    exit();
}

*/
if ($action == "load_drop_down_embro_issue_source") {
    $explode_data = explode("**", $data);
    $data = $explode_data[0];
    $selected_company =0;// $explode_data[1];

    if ($data == 3)
    {
        if ($db_type == 0) {
             echo create_drop_down("cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' );location_select();get_php_form_data(document.getElementById('cbo_company_name').value,'load_variable_settings','requires/bundle_wise_cutting_delevar_to_input_controller');get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/bundle_wise_cutting_delevar_to_input_controller');load_html();");
        } else {
            echo create_drop_down("cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' );location_select();get_php_form_data(document.getElementById('cbo_company_name').value,'load_variable_settings','requires/bundle_wise_cutting_delevar_to_input_controller');get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/bundle_wise_cutting_delevar_to_input_controller');load_html();");
        }
    } 
    else if ($data == 1)
    {
        echo create_drop_down("cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' );location_select();get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_cutting_delevar_to_input_controller');get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/bundle_wise_cutting_delevar_to_input_controller');load_html();", 0, 0);
    
    }
    else
    {
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', this.value, 'load_drop_down_location', 'location_td' );location_select();get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_cutting_delevar_to_input_controller');get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/bundle_wise_cutting_delevar_to_input_controller');load_html();", 0);
    }

    exit();
}

if ($action=="service_booking_popup")
{
    echo load_html_head_contents("Booking Search","../../", 1, 1, $unicode);
    extract($_REQUEST);


    $preBookingNos = 0;
    ?>

    <script>
        
        function js_set_value(booking_no)
        {
            // alert(booking_no);
            document.getElementById('selected_booking').value=booking_no; //return;
            parent.emailwindow.hide();
        }
        
    </script>

    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                <tr>
                    <td align="center" width="100%">
                        <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                             <input type="text" id="selected_batchDtls" class="text_boxes" style="width:70px" value="<? echo $txt_batch_dtls;?>">
                              <input type="text" id="booking_no" class="text_boxes" style="width:70px" value="">
                              <input type="text" id="booking_id" class="text_boxes" style="width:70px">
                             
                             
                            <thead>
                                <th  colspan="11">
                                    <?
                                    echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
                                    ?>
                                </th>
                            </thead>
                            <thead>                  
                                <th width="150">Company Name</th>
                                <th width="150">Supplier Name</th>
                                <th width="150">Buyer  Name</th>
                                <th width="100">Job  No</th>
                                <th width="100">Order No</th>
                                <th width="100">Internal Ref.</th>
                                <th width="100">File No</th>
                                <th width="100">Style No.</th>
                                <th width="100">WO No</th>
                                <th width="200">Date Range</th>
                                <th></th>           
                            </thead>
                            <tr>
                                <td> <input type="hidden" id="selected_booking">
                                    <? 
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$company_id."", "load_drop_down( 'fabric_issue_to_finishing_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if($cbo_service_source==3)
                                    {
                                        echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
                                    }
                                    else
                                    {
                                        echo create_drop_down( "cbo_supplier_name", 152, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", "".$supplier_id."", "",1 );
                                    }
                                    ?>
                                </td>
                                <td id="buyer_td">
                                    <? 
                                    echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                                    ?>
                                </td>
                                <td>
                                    <input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px">
                                </td> 


                                <td>
                                    <input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:70px">
                                </td> 
                                <td>
                                    <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
                                </td> 
                                <td>
                                    <input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px">
                                </td> 



                                <td>
                                    <input name="txt_style" id="txt_style" class="text_boxes" style="width:70px">
                                </td>
                                <td>
                                    <input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px">
                                </td> 
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
                                </td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $preBookingNos;?>+'_'+document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $cbo_service_source;?>, 'create_booking_search_list_view', 'search_div', 'bundle_wise_sewing_output_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
                        </td>
                    </tr>
         
   </table>    
   <div style="width:100%; margin-top:5px" id="search_div" align="left"></div>
   
    </form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}



if ($action=="create_booking_search_list_view")
{

    $data=explode('_',$data);
    // echo "<pre>";print_r($data);
    if ($data[0]!=0) $company=" and c.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer="";
    
    if($db_type==0)
    {
        if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $wo_date ="";
    }
    
    if($db_type==2)
    {
        if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $wo_date ="";
    }
    //echo $data[8];
    if($data[6]==1)
    {
        if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num='$data[5]'    "; else  $wo_cond="";
        if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
        if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no='$data[8]'  "; else  $style_cond=""; 
    }
    if($data[6]==4 || $data[6]==0)
    {
        if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
        if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
        if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==2)
    {
        if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
        if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
        if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==3)
    {
        if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $wo_cond="";
        if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
        if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'  "; else  $style_cond="";  
    } 

    if ($data[9]!="")
    {
        foreach(explode(",", $data[9]) as $bok){
            $bookingnos .= "'".$bok."',";
        }
        $bookingnos = chop($bookingnos,",");
        if( $service_source!=1)
        {
        $preBookingNos_1 = " and a.booking_no not in (".$bookingnos.")";
        $preBookingNos_2 = " and a.wo_no not in (".$bookingnos.")";
        }
    }
    if ($data[10]!="")
    {
        $po_number_cond = " and d.po_number = '$data[10]'";     
    }
    if ($data[11]!="")
    {       
        $internal_ref_cond = " and d.grouping = '$data[11]'";
    }
    if ($data[12]!="")
    {
        $file_cond = " and d.file_no = '$data[12]'";
    }


    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
    
    $arr=array (2=>$comp,3=>$conversion_cost_head_array,4=>$buyer_arr,7=>$po_no,8=>$item_category,9=>$fabric_source,10=>$suplier);
         
    $sql= "SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping  
    from garments_service_wo_mst a, garments_service_wo_dtls b, wo_po_details_master c ,wo_po_break_down d 
    where a.id = b.mst_id  and b.po_id=d.id and c.id=d.job_id  $company $buyer $wo_date $wo_cond $style_cond $file_cond $po_number_cond $internal_ref_cond  and  a.status_active=1 and a.is_deleted=0  $job_cond
    group by a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping";    
    // echo $sql;   
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
        <thead>
            <tr>
                <th width="20">SL No.</th>
                <th width="120">WO No</th>
                <th width="60">WO Date</th>
                <th width="80">Company</th>
                <th width="100">Buyer</th>
                <th width="50">Job No</th>

                <th width="70">Internal Ref.</th>
                <th width="70">File No</th>


                <th width="100">Style No.</th>
                <th width="100">PO number</th>
            </tr>
        </thead>
    </table>
    <div style="width:1288px; max-height:400px; overflow-y:scroll;" >    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_list_search" >  
            <tbody>
                <?
                $result = sql_select($sql);         
                $i=1; 
                foreach($result as $row)
                {                   
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                     <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('sys_number')]; ?>');"> 
                    
                        <td width="20"><? echo $i; ?></td>
                        <td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
                        <td width="60"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
                        <td width="80"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>

                        <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                        <td width="50"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>


                        <td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>

                        <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
                        
                    </tr>
                    <?
                    $i++;                   
                }
                ?>
            </tbody>
        </table>
    </div>
    <script type="text/javascript">
        setFilterGrid("tbl_list_search",-1);
    </script>
    <?  

    exit();
}

if ($action == "order_popup") {
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
    ?>
    <script>
        $(document).ready(function (e) {
            $("#txt_search_common").focus();
        });

        function search_populate(str) {
            //alert(str);
            if (str == 0) {
                document.getElementById('search_by_th_up').innerHTML = "Order No";
                document.getElementById('search_by_td').innerHTML = '<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"  value=""  />';
            }
            else if (str == 1) {
                document.getElementById('search_by_th_up').innerHTML = "Style Ref. Number";
                document.getElementById('search_by_td').innerHTML = '<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"  value="" />';
            }
            else //if(str==2)
            {
                var buyer_name = '<option value="0">--- Select Buyer ---</option>';
                <?php
                if ($db_type == 0) {
                    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name", 'id', 'buyer_name');
                } else {
                    $buyer_arr = return_library_array("select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name", 'id', 'buyer_name');
                }
                foreach ($buyer_arr as $key => $val) {
                    echo "buyer_name += '<option value=\"$key\">" . ($val) . "</option>';";
                }
                ?>
                document.getElementById('search_by_th_up').innerHTML = "Select Buyer Name";
                document.getElementById('search_by_td').innerHTML = '<select name="txt_search_common" style="width:230px" class="combo_boxes" id="txt_search_common">' + buyer_name + '</select>';
            }
        }

        function js_set_value(id, item_id, po_qnty, plan_qnty, country_id) {
            $("#hidden_mst_id").val(id);
            $("#hidden_grmtItem_id").val(item_id);
            $("#hidden_po_qnty").val(po_qnty);
            $("#hidden_country_id").val(country_id);
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;">
        <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                <tr>
                    <td align="center" width="100%">
                        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                            <thead>
                            <th width="130">Search By</th>
                            <th width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                            <th width="200">Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset"
                                                  style="width:100px;"/></th>
                            </thead>
                            <tr>
                                <td width="130">
                                    <?
                                    $searchby_arr = array(0 => "Order No", 1 => "Style Ref. Number", 2 => "Buyer Name");
                                    echo create_drop_down("txt_search_by", 130, $searchby_arr, "", 1, "-- Select Sample --", $selected, "search_populate(this.value)", 0);
                                    ?>
                                </td>
                                <td width="180" align="center" id="search_by_td">
                                    <input type="text" style="width:230px" class="text_boxes" name="txt_search_common"
                                           id="txt_search_common"
                                           onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()"/>
                                </td>
                                <td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker"
                                           style="width:70px"> To
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                </td>
                                <td align="center">
                                    <input type="button" id="btn_show" class="formbutton" value="Show"
                                           onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'bundle_wise_cutting_delevar_to_input_controller', 'setFilterGrid(\'tbl_po_list\',-1)')"
                                           style="width:100px;"/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="40" valign="middle">
                        <? echo load_month_buttons(1); ?>
                        <input type="hidden" id="hidden_mst_id">
                        <input type="hidden" id="hidden_grmtItem_id">
                        <input type="hidden" id="hidden_po_qnty">
                        <input type="hidden" id="hidden_country_id">
                    </td>
                </tr>
            </table>
            <div style="margin-top:10px" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if ($action == "create_po_search_list_view") {
    $ex_data = explode("_", $data);
    $txt_search_by = $ex_data[0];
    $txt_search_common = $ex_data[1];
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $company = $ex_data[4];
    $garments_nature = $ex_data[5];

    $sql_cond = "";
    if (trim($txt_search_common) != "") {
        if (trim($txt_search_by) == 0)
            $sql_cond = " and b.po_number like '%" . trim($txt_search_common) . "%'";
        else if (trim($txt_search_by) == 1)
            $sql_cond = " and a.style_ref_no like '%" . trim($txt_search_common) . "%'";
        else if (trim($txt_search_by) == 2)
            $sql_cond = " and a.buyer_name=trim('$txt_search_common')";
    }
    if ($txt_date_from != "" || $txt_date_to != "") {
        if ($db_type == 0) {
            $sql_cond .= " and b.shipment_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
        }
        if ($db_type == 2 || $db_type == 1) {
            $sql_cond .= " and b.shipment_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "'";
        }
    }

    if (trim($company) != "") $sql_cond .= " and a.company_name='$company'";

    $sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut
    from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
    where
    a.job_no = b.job_no_mst and a.job_no = c.job_no and a.status_active=1 and  a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature and c.emb_name=2
    $sql_cond group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut order by b.id DESC";
    //echo $sql;die;
    $result = sql_select($sql);
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

    //$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
    if ($db_type == 0) {
        $po_country_arr = return_library_array("select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 group by po_break_down_id", 'po_break_down_id', 'country');
    } else {
        $po_country_arr = return_library_array("select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 group by po_break_down_id", 'po_break_down_id', 'country');
    }

    $po_country_data_arr = array();
    $poCountryData = sql_select("select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id");

    foreach ($poCountryData as $row) {
        $po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty'] = $row[csf('qnty')];
        $po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty'] = $row[csf('plan_cut_qnty')];
    }

    $total_issu_qty_data_arr = array();
    $total_issu_qty_arr = sql_select("select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 group by po_break_down_id, item_number_id, country_id");

    foreach ($total_issu_qty_arr as $row) {
        $total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] = $row[csf('production_quantity')];
    }

    ?>
    <div style="width:1030px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
            <th width="30">SL</th>
            <th width="70">Shipment Date</th>
            <th width="100">Order No</th>
            <th width="100">Buyer</th>
            <th width="120">Style</th>
            <th width="140">Item</th>
            <th width="100">Country</th>
            <th width="80">Order Qty</th>
            <th width="80">Total Issue Qty</th>
            <th width="80">Balance</th>
            <th>Company Name</th>
            </thead>
        </table>
    </div>
    <div style="width:1030px; max-height:240px;overflow-y:scroll;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1012" class="rpt_table" id="tbl_po_list">
            <?
            $i = 1;
            foreach ($result as $row) {
                $exp_grmts_item = explode("__", $row[csf("set_break_down")]);
                $numOfItem = count($exp_grmts_item);
                $set_qty = "";
                $grmts_item = "";

                $country = array_unique(explode(",", $po_country_arr[$row[csf("id")]]));
                //$country=explode(",",$po_country_arr[$row[csf("id")]]);
                $numOfCountry = count($country);

                for ($k = 0; $k < $numOfItem; $k++) {
                    if ($row["total_set_qnty"] > 1) {
                        $grmts_item_qty = explode("_", $exp_grmts_item[$k]);
                        $grmts_item = $grmts_item_qty[0];
                        $set_qty = $grmts_item_qty[1];
                    } else {
                        $grmts_item_qty = explode("_", $exp_grmts_item[$k]);
                        $grmts_item = $grmts_item_qty[0];
                        $set_qty = $grmts_item_qty[1];
                    }

                    foreach ($country as $country_id) {
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

                        //$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
                        $po_qnty = $po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
                        $plan_cut_qnty = $po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];

                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer"
                            onClick="js_set_value(<? echo $row[csf("id")]; ?>,'<? echo $grmts_item; ?>','<? echo $po_qnty; ?>','<? echo $plan_cut_qnty; ?>','<? echo $country_id; ?>');">
                            <td width="30" align="center"><?php echo $i; ?></td>
                            <td width="70"
                                align="center"><?php echo change_date_format($row[csf("shipment_date")]); ?></td>
                            <td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
                            <td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                            <td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            <td width="140"><p><?php echo $garments_item[$grmts_item]; ?></p></td>
                            <td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
                            <td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;
                                ?>&nbsp;</td>
                            <td width="80" align="right">
                                <?php
                                echo $total_cut_qty = $total_issu_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]
                                ?> &nbsp;
                            </td>
                            <td width="80" align="right">
                                <?php
                                $balance = $po_qnty - $total_cut_qty;
                                echo $balance;
                                ?>&nbsp;
                            </td>
                            <td><?php echo $company_arr[$row[csf("company_name")]]; ?> </td>
                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            ?>
        </table>
    </div>
    <?
    exit();
}

if ($action == "populate_data_from_search_popup") {
    $dataArr = explode("**", $data);
    $po_id = $dataArr[0];
    $item_id = $dataArr[1];
    $embel_name = $dataArr[2];
    $country_id = $dataArr[3];

    $res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name
        from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c
        where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id=$po_id group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name");

    foreach ($res as $result) {
        echo "$('#txt_order_no').val('" . $result[csf('po_number')] . "');\n";
        echo "$('#hidden_po_break_down_id').val('" . $result[csf('id')] . "');\n";
        echo "$('#cbo_buyer_name').val('" . $result[csf('buyer_name')] . "');\n";
        echo "$('#txt_style_no').val('" . $result[csf('style_ref_no')] . "');\n";

        $dataArray = sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=9 and embel_name='$embel_name' THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=" . $result[csf('id')] . " and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
        foreach ($dataArray as $row) {
            echo "$('#txt_cutting_qty').val('" . $row[csf('totalcutting')] . "');\n";
            echo "$('#txt_cumul_issue_qty').attr('placeholder','" . $row[csf('totalprinting')] . "');\n";
            echo "$('#txt_cumul_issue_qty').val('" . $row[csf('totalprinting')] . "');\n";
            $yet_to_produced = $row[csf('totalcutting')] - $row[csf('totalprinting')];
            echo "$('#txt_yet_to_issue').attr('placeholder','" . $yet_to_produced . "');\n";
            echo "$('#txt_yet_to_issue').val('" . $yet_to_produced . "');\n";
        }
    }
    exit();
}

if($action=="bundle_popup_rescan")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
    <script>
    
        function check_all_data() {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
            for( var i = 1; i <= tbl_row_count; i++ ) {
                if($("#search"+i).css("display") !='none'){
                 js_set_value( i );
                }
            }
        }
        
        
        var selected_id = new Array();
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function js_set_value( str) 
        {
            toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
            
            if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
                selected_id.push( $('#txt_individual' + str).val() );
                
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == $('#txt_individual' + str).val() ) break;
                }
                selected_id.splice( i, 1 );
            }
            var id = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            
            $('#hidden_bundle_nos').val( id );
         
        }
        
        function fnc_close()
        {   
            document.getElementById('hidden_source_cond').value=document.getElementById('source_cond').value;
            //alert(document.getElementById('hidden_source_cond').value)
            parent.emailwindow.hide();
        }
        
        function reset_hide_field()
        {
            $('#hidden_bundle_nos').val( '' );
            $('#hidden_source_cond').val( '' );
            selected_id = new Array();
        }
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchwofrm"  id="searchwofrm">
	        <fieldset style="width:810px;">
	        <legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" checked> is exact</legend>           
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <th>Cut Year</th>
	                    <th>Job No</th>
	                    <th>Order No</th>
	                    <th class="must_entry_caption">Cut No</th>
	                    <th>Bundle No</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
	                        <input type="hidden" name="hidden_source_cond" id="hidden_source_cond">  
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td align="center">             
	                    <?
	                        echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
	                    ?>
	                    </td>               
	                    <td align="center">             
	                        <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />  
	                    </td>               
	                    <td align="center" id="search_by_td">               
	                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />  
	                    </td>               
	                    <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes" /></td>         
	                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>       
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked'), 'create_bundle_rescan_search_list_view', 'search_div', 'bundle_wise_cutting_delevar_to_input_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
	        </fieldset>
	    </form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}



if($action=="create_bundle_rescan_search_list_view")
{
    $ex_data = explode("_",$data);
    $txt_order_no = "%".trim($ex_data[0])."%";
    $company = $ex_data[1];
    if(trim($ex_data[2])){$bundle_no = "".trim($ex_data[2])."";}
    else{ $bundle_no = "%".trim($ex_data[2])."%";}
    $selectedBuldle=$ex_data[3];
    $job_no=$ex_data[4];
    $cut_no=$ex_data[5];
    $order_no =str_replace("'","", $ex_data[0]);
    $bndl_no =str_replace("'","", $ex_data[2]);
    $syear = substr($ex_data[6],2); 
    $is_exact=$ex_data[7];
    
    foreach(explode(",",$selectedBuldle) as $bn)
    {
        $curentscanned_bundle_arr[$bn]=$bn; 
    }
    
    if($db_type==2) $not_null_bundle=" and bundle_no is not null";
    else $not_null_bundle=" and bundle_no!=''";
    
    
    
    
    
    
    /*if( trim($ex_data[5])=='' ){echo "<h2 style='color:#D00; text-align:center;'>Please Select  Cut No</h2>";exit();}*/
    
    //$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
     

    
    $company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    //  $cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "barcode_no in ($bundle_nos)");
    $cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $last_operation=gmt_production_validation_script( 4, 1,'',$cutting_no, $production_squence);
    
    if ($cut_no != '') 
    {
        if($is_exact=='true')
        {
         $cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
         $cutCon_a = " and b.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
        }
        else
        {
            $cutCon = " and c.cut_no like '%".$cut_no."'";
            $cutCon_a = " and b.cut_no like '%".$cut_no."'";
        }
    }
    //if($job_no!='') $jobCon=" and f.job_no_prefix_num = $job_no"; else $jobCon="";
    //print_r($scanned_bundle_qty_arr);die;
    if($job_no!='')
    {
        if($is_exact=='true') $jobCon=" and f.job_no = '$job_no'";
        else  $jobCon=" and f.job_no like '%$job_no%'";
         
    }
    $orderCon="";
    if($order_no)
    {
        if($is_exact=='true') $orderCon=" and e.po_number = '$order_no'";
        else  $orderCon=" and e.po_number like '%$order_no%'";
         
    }

    $bndlCon="";
    if($bndl_no)
    {
        if($is_exact=='true') $bndlCon=" and c.bundle_no = '$bndl_no'";
        else  $bndlCon=" and c.bundle_no like '%$bndl_no%'";
         
    }


    $sql_scan_bundle=sql_select(" select b.barcode_no,sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=9 and b.status_active=1 and b.is_deleted=0 $not_null_bundle  $cutCon_a  group by b.barcode_no");
    foreach($sql_scan_bundle as $val)
    {
        $scanned_bundle_qty_arr[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
        $scanned_bundle_arr[]=$val[csf('barcode_no')];
    }


     
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="50">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
    </table>
    <div style="width:850px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            $last_operation_string='';  
            foreach($last_operation as  $item_id=>$operation_cond)
            {
                if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
                else
                {
                    $sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  and c.production_type in (3)  $orderCon $bndlCon     $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
                    foreach($sqld as $arows)
                    {
                        $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                        $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                        $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spt_qty')];
                        $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
                    }
                }
                 
                $sql="SELECT c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  $orderCon $bndlCon   $operation_conds  $item_id $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
                 //echo $sql;
                $result = sql_select($sql); 
                foreach ($result as $row)  
                {  
                    
                     $bundle_qty = ($row[csf('qty')] + $replace_qty[$row[csf('bundle_no')]])  - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]])-$scanned_bundle_qty_arr[$row[csf('barcode_no')]];
                    
                    //$bundle_qty=$row[csf('qty')]-$scanned_bundle_qty_arr[$row[csf('barcode_no')]]+$replace_qty[$row[csf('barcode_no')]];
                 
                    //if($row[csf('barcode_no')]=='17990000005901') 
                //  echo  $row[csf('qty')]."=".$reject_qty[$row[csf('bundle_no')]]."=".$scanned_bundle_qty_arr[$row[csf('barcode_no')]]."*";
                    if($bundle_qty>0 && in_array($row[csf('barcode_no')],$scanned_bundle_arr) && !in_array($row[csf('bundle_no')],$curentscanned_bundle_arr))
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                            <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                            <td width="50"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="right"><? echo $bundle_qty; ?>&nbsp;&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
                }
            }
            
            if(empty($last_operation))
            {
                $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and  c.production_type=1 $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc"; 
                // order by c.cut_no, c.bundle_no DESC
                
                $last_operation_string='';
                $result = sql_select($sql); 
                foreach ($result as $row)
                {  
                    $bundle_qty=$row[csf('qty')]-$scanned_bundle_qty_arr[$row[csf('barcode_no')]];
                    if($bundle_qty>0 && in_array($row[csf('barcode_no')],$scanned_bundle_arr) && !in_array($row[csf('bundle_no')],$curentscanned_bundle_arr))
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                            <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                            <td width="70"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
                }
            }
            ?>
            <input type="hidden" name="source_cond"  value="<?php echo $last_operation_string; ?>" id="source_cond"  />
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
               <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?  
    exit(); 
}

if($action=="populate_bundle_data_rescan")
{
    
    $ex_data = explode("**",$data);
    $bundle=explode(",",$ex_data[0]);
    $mst_id=explode(",",$ex_data[2]);
    $bundle_nos="'".implode("','",$bundle)."'";
    $vscan=$ex_data[4];
    $source_cond=$ex_data[5];
    //echo $bundle_nos;die;
    //echo $vscan;die;
	
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
		
		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";
		
		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}
    
    $scanned_bundle_arr=return_library_array( "select b.bundle_no, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=9 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond group by b.bundle_no",'bundle_no','production_qnty');
    //print_r($scanned_bundle_arr);die;
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    
    $year_field="";
    if($db_type==0) 
    {
        $year_field="YEAR(f.insert_date)"; 
    }
    else if($db_type==2) 
    {
        $year_field="to_char(f.insert_date,'YYYY')";
    }
    $cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
    //$cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $last_operation=gmt_production_validation_script( 4, 1,'', $cutting_no, $production_squence);
    
    //$last_operation=gmt_production_validation_script( 4, 1); 
     //print_r($last_operation);die;
    foreach($last_operation as  $item_id=>$operation_cond)
    {
        //echo $item_id; die;  17990000000898
        if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
        else
        {
         
            $sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id='$ex_data[3]'  and c.production_type in (3) $bundle_nos_cond $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
            foreach($sqld as $arows)
            {
                $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spt_qty')];
                $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
            }
        }
        
        $sql="SELECT max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no $bundle_nos_cond $operation_conds  and c.status_active=1 and c.is_deleted=0 $item_id group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, e.po_number,c.barcode_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
    //echo $sql;
    $result = sql_select($sql);
    $count=count($result);
    $i=$ex_data[1]+$count;
    foreach ($result as $row)
    { 
    //echo $row[csf('production_qnty')]."=".$scanned_bundle_arr[$row[csf('bundle_no')]]; 5=15 15=15
        //$qty=$row[csf('production_qnty')]-$scanned_bundle_arr[$row[csf('bundle_no')]];
         $qty = ($row[csf('production_qnty')] + $replace_qty[$row[csf('bundle_no')]]) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]])-$scanned_bundle_arr[$row[csf('bundle_no')]];
        if(($qty*1)>0 && $scanned_bundle_arr[$row[csf('bundle_no')]]!="")
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>"> 
                <td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    
                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                   
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"  value="<? echo $row[csf('prdid')]; ?>"/>
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="1"/>
                </td>
            </tr>
            <?
            $i--;
            }
        }
    }
    
    if(empty($last_operation))
    {
          $sql="SELECT max(c.id)  as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and c.production_type=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
          
    $result = sql_select($sql);
    $count=count($result);
    $i=$ex_data[1]+$count;
    foreach ($result as $row)
    { 
        $qty=$row[csf('production_qnty')]-$scanned_bundle_arr[$row[csf('bundle_no')]];
        if(($qty*1)>0)
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>"> 
                <td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    
                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                   
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"value="<? echo $row[csf('prdid')]; ?>"/> 
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="1"/>
                </td>
            </tr>
            <?
            $i--;
            }
        }
    }
    exit(); 
}


if ($action == "bundle_popup") {
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);

    ?>
    <script>

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            for (var i = 1; i <= tbl_row_count; i++) {
                if ($("#search" + i).css("display") != 'none') {
                    js_set_value(i);
                }
            }
        }
        var selected_id = new Array();

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }

        function js_set_value(str) {
            toggle(document.getElementById('search' + str), '#FFFFCC');

            if (jQuery.inArray($('#txt_individual' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual' + str).val());

            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual' + str).val()) break;
                }
                selected_id.splice(i, 1);
            }
            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
            }
            id = id.substr(0, id.length - 1);

            $('#hidden_bundle_nos').val(id);
        }

        function fnc_close() {
            document.getElementById('hidden_source_cond').value=document.getElementById('source_cond').value;
            parent.emailwindow.hide();
        }

        function reset_hide_field() {
            $('#hidden_bundle_nos').val('');
            selected_id = new Array();
        }

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:810px;">
                <legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" > is exact</legend>
                <table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th>Cut Year</th>
                    <th>Job No</th>
                    <th class="must_entry_caption">Style No</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Cut No</th>
                    <th>Bundle No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                        <input type="hidden" name="hidden_source_cond" id="hidden_source_cond"> 
                    </th>
                    </thead>

                <tr class="general">
                    <td align="center">             
                    <?
                        echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
                    ?>
                    </td>               
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_job_no"
                                   id="txt_job_no"/>
                                   
                        </td>
                        <td align="center">
                             <input type="text" name="txt_style_no" id="txt_style_no" style="width:120px" class="text_boxes"/>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_order_no"
                                   id="txt_order_no"/>
                        </td>
                        <td>
                            <input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes"/>
                                </td>
                        <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes"/>
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo $bundleNo; ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+document.getElementById('txt_style_no').value+'_'+$('#is_exact').is(':checked')+'<? echo $company_id;?>'+'_'+document.getElementById('txt_style_no').value, 'create_bundle_search_list_view', 'search_div', 'bundle_wise_cutting_delevar_to_input_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;"/>
                        </td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if ($action == "create_bundle_search_list_view") {
    $ex_data = explode("_", $data);
    $txt_order_no = "%" . trim($ex_data[0]) . "%";
    $company = $ex_data[1];
    //$bundle_no = "%".trim($ex_data[2])."%";
    if (trim($ex_data[2])) {
        $bundle_no = "" . trim($ex_data[2]) . "";
    } else {
        $bundle_no = "%" . trim($ex_data[2]) . "%";
    }
    
    $selectedBuldle = $ex_data[3];
    $job_no = $ex_data[4];
    $style_no=$ex_data[9];
    $order_no =str_replace("'","", $ex_data[0]);
    $bndl_no =str_replace("'","", $ex_data[2]);
    $cut_no = $ex_data[5];
    $syear = substr($ex_data[6],2); 
    $is_exact=$ex_data[7];
    /*if (trim($ex_data[5]) == '') 
    {
        echo "<h2 style='color:#D00; text-align:center;'><u>Please Select-Cut No</u></h2>";
        exit();
    }*/

    
    $company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    //list($short_name)=explode('-',$company_short_arr[$company]);
    $cutConvertToInt = convertToInt('c.cut_no', array($company_short_arr[$company], '-'), 'cut_no');
    $bundleConvertToInt = convertToInt('c.bundle_no', array($company_short_arr[$company], '-', "/"), 'order_bundle_no');

    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
    

   // $scanned_bundle_arr = return_library_array("select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4 and  b.status_active=1 and b.is_deleted=0", 'bundle_no', 'bundle_no');
    $cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $where_con = '';
    if ($ex_data[2]) 
    {
        $where_con .= " and c.bundle_no like '%" . trim($ex_data[2]) . "'";
    }

    if ($ex_data[0]) 
    {
        if($is_exact=='true') $where_con .= " and e.po_number='" . trim($ex_data[0]) . "'";
        else $where_con .= " and e.po_number like  '%" . trim($ex_data[0]) . "%'";    


    }
    $tmp_cut=trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    if ($cut_no != '')
     {
        if($is_exact=='true')
        {
            //$cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
           // $cutCon_a = " and a.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
            $cutCon = " and c.cut_no = '$cut_no'";
            $cutCon_a = " and b.cut_no = '$cut_no'";
        }
        else
        {
            $cutCon = " and c.cut_no like '%".$cut_no."%'";
            $cutCon_a = " and b.cut_no like '%".$cut_no."%'";
        }
    }
    if($job_no!='')
    {
        if($is_exact=='true') $jobCon=" and f.job_no = '$job_no'";
        else  $jobCon=" and f.job_no like '%$job_no%'";
         
    }
    if($style_no!='')
    {
        if($is_exact=='true') $styleCon=" and f.style_ref_no = '$style_no'";
        else  $styleCon=" and f.style_ref_no like '%$style_no%'";
         
    }
    $orderCon="";
    if($order_no)
    {
        if($is_exact=='true') $orderCon=" and e.po_number = '$order_no'";
        else  $orderCon=" and e.po_number like '%$order_no%'";
         
    }

    $bndlCon="";
    if($bndl_no)
    {
        if($is_exact=='true') $bndlCon=" and c.bundle_no = '$bndl_no'";
        else  $bndlCon=" and c.bundle_no like '%$bndl_no%'";
         
    }
    $year_cond="";
    /*if($syear)
    {
        $year_cond .= " and c.cut_no like '%-$syear-%' ";
    }*/

    if($syear)
    {
        $year_cond .= " and d.job_no_mst like '%-$syear-%' ";
    }
    
    
    
    
  // echo $tmp_cut;
   $scanned_bundle_arr = return_library_array("select bundle_no, bundle_no from pro_garments_production_dtls where production_type=9 and cut_no='".$tmp_cut."' and status_active=1 and is_deleted=0", 'bundle_no', 'bundle_no');
    foreach (explode(",", $selectedBuldle) as $bn) {
        $scanned_bundle_arr[$bn] = $bn;
    }

    $scanne=sql_select( "select b.bundle_no, sum(b.production_qnty) as production_qnty,a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=9  and b.status_active=1 and b.is_deleted=0 $cutCon_a group by b.bundle_no,a.sewing_line");
    foreach($scanne as $row)
    {
        $duplicate_bundle[$row[csf("bundle_no")]] +=$row[csf("production_qnty")];
        
    }


    //print_r($scanned_bundle_arr);
    
   
 
    //$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "barcode_no in ($bundle_nos)");
     
    // echo $cutting_no;
    $last_operation=gmt_production_validation_script( 4, 1,'', $cutting_no, $production_squence);
    //$last_operation=gmt_production_validation_script( 4, 1 );
    // print_r($last_operation);

    ?>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="80">Style No</th>

            <th width="50">Size</th>
            <th width="70">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
    </table>
    <div style="width:910px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            $last_operation_string='';  
            //foreach($last_operation as  $item_id=>$operation_cond)
            foreach($last_operation as  $item_id=>$operation_cond)
            {
                 if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
                 else
                {
                    ///echo "select c.bundle_no, SUM(c.reject_qty) as raj_qty, SUM(c.alter_qty) as alt_qty, SUM(c.spot_qty) as spt_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  and c.production_type in (3)  and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no'   $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no";
                    $sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  and c.production_type in (3)  $orderCon $bndlCon $year_cond   $jobCon $styleCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
                    foreach($sqld as $arows)
                    {
                        $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                        $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                        $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spot_qty')];
                        $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
                    }
                }
                
                //echo $last_operation_string;
                $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, f.style_ref_no, c.bundle_no, sum(c.production_qnty) as qty, sum(c.replace_qty) as replace_qty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company $orderCon $bndlCon $year_cond  $item_id $jobCon $styleCon $cutCon  and a.status_active=1 and a.is_deleted=0 $operation_conds group by c.cut_no, f.style_ref_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by  f.style_ref_no, c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";
                
                 //echo $sql;die;
                $result = sql_select($sql); 
                foreach ($result as $row)
                {  
                // echo $row[csf('qty')]."=".$row[csf('bundle_no')]."*";  -$row[csf('replace_qty')]
                    $row[csf('qty')] = (($row[csf('qty')]) ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
                    //+ $replace_qty[$row[csf('bundle_no')]]
                    $balance_qnty=$row[csf('qty')]-$duplicate_bundle[$row[csf("bundle_no")]];
                    if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $row[csf('qty')]>0 && $balance_qnty>0)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>

                            <td width="80"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                            
                            <td width="50"><p><? echo $row[csf('size_number_id')]; ?></p></td>
                            <td width="70"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            if(empty($last_operation))
            {
                die;
                $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, f.style_ref_no, sum(c.production_qnty) as qty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and  c.production_type=1 $jobCon $styleCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by f.style_ref_no, c.cut_no,c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by c.cut_no, f.style_ref_no, length(c.bundle_no) asc, c.bundle_no asc";
                
                //order by c.cut_no, c.bundle_no DESC
                $last_operation_string='';
                $result = sql_select($sql); 
                foreach ($result as $row)
                {  
                    if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>

                            <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>

                            <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                            <td width="70"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
                }
            }
            ?>
            <input type="hidden" name="source_cond"  value="<?php echo $last_operation_string; ?>" id="source_cond"  />
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
               <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
    <?
    exit();
}


if ($action == "challan_duplicate_check") 
{
    $data=explode("__",$data);
    $result=sql_select("select a.sys_number,b.bundle_no from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.barcode_no='$data[0]' and b.production_type=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number, b.bundle_no");
    foreach ($result as $row) 
    {
       echo "Bundle No " . $row[csf('bundle_no')] . " Found in Challan No " . $row[csf('sys_number')] . ".";
      //echo "2_".$row[csf('bundle_no')]."**".$row[csf('sys_number')];
      // die;
    }
    
    exit();
}

if ($action == "populate_bundle_data") 
{
    $ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = explode(",", $ex_data[2]);
    $bundle_nos = "'" . implode("','", $bundle) . "'";
    $vscan=$ex_data[4];
    $source_cond=$ex_data[5];
	
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
		
		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";
		
		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}
	
    $scanned_bundle_arr = return_library_array("select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=9 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond", 'bundle_no', 'bundle_no');
    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
    
    $year_field = "";
    if ($db_type == 0) {
        $year_field = "YEAR(a.insert_date)";
    } else if ($db_type == 2) {
        $year_field = "to_char(a.insert_date,'YYYY')";
    }
     
    $last_operation=array();
    //$last_operation=gmt_production_validation_script( 4, 1);

    $cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
    //$cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $last_operation=gmt_production_validation_script( 4, 1,'', $cutting_no, $production_squence);

    foreach($last_operation as  $item_id=>$operation_cond)
    {
        //echo $item_id;die;
        if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
        else
        {
            $sqld = sql_select( "SELECT  c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id  and a.company_id=$ex_data[3] and c.production_type in(3) and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
            foreach($sqld as $arows)
            {
                $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spot_qty')];
                $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
            }
        }
        
        //  $sql="SELECT max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond $item_id $operation_conds  group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
        $sql="SELECT max(c.id) as prdid, d.id as colorsizeid, d.po_break_down_id as po_id, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d where a.company_id='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $bundle_nos_cond $item_id $operation_conds  group by d.id, d.po_break_down_id, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

     //c.delivery_mst_id =a.delivery_mst_id 
    // echo $sql; die;
        $result = sql_select($sql);
        $po_id_arr = array();
        foreach ($result as $v) 
        {
            $po_id_arr[$v['PO_ID']] = $v['PO_ID'];
        }
        $po_ids = implode(",",$po_id_arr);
       
        $order_sql = "SELECT a.job_no_prefix_num, $year_field as year, a.buyer_name,b.po_number,b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.status_active=1 and b.id in($po_ids)";
        // echo $order_sql;die;
        $res = sql_select($order_sql);
        $order_data_array = array();
        foreach ($res as $val) 
        {
            $order_data_array[$val['ID']]['job_prefix'] = $val['JOB_NO_PREFIX_NUM'];
            $order_data_array[$val['ID']]['year'] = $val['YEAR'];
            $order_data_array[$val['ID']]['buyer_name'] = $val['BUYER_NAME'];
            $order_data_array[$val['ID']]['po_number'] = $val['PO_NUMBER'];
        }
        $count=count($result);
        $i=$ex_data[1]+$count;
        foreach ($result as $row)
        { 
         
            if(trim($scanned_bundle_arr[$row[csf('bundle_no')]])=="")
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $qty = ($row[csf('production_qnty')] ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
                
            //  $qty=$row[csf('production_qnty')];
                //+ $replace_qty[$row[csf('bundle_no')]]
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>"> 
                    <td width="30"><? echo $i; ?></td>
                    <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                    <td width="50" align="center"><? echo $order_data_array[$row[csf('po_id')]]['year']; ?></td>
                    <td width="60" align="center"><? echo $order_data_array[$row[csf('po_id')]]['job_prefix']; ?></td>
                    <td width="65"><? echo $buyer_arr[$order_data_array[$row[csf('po_id')]]['buyer_name']]; ?></td>
                    <td width="90" style="word-break:break-all;" align="left"><? echo $order_data_array[$row[csf('po_id')]]['po_number']; ?></td>
                    <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                    <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                    <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                    <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                    <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
                    <td id="button_1" align="center">
                        <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        
                        <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                        <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                        <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                        <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                        <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                        <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                        <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                        <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                        <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prdid')]; ?>"/> 
                        <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="0"/>
                    </td>
                </tr>
                <?
                $i--;
            }
        }
    }
    
    if(empty($last_operation))
    {
          $sql="SELECT d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and c.production_type=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
        
    }
    
    exit(); 
}

if ($action == "populate_bundle_data_check") 
{
    
    $ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = explode(",", $ex_data[2]);
    $bundle_nos = "'" . implode("','", $bundle) . "'";
    
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
	}
	
    $year_field = "";
    if ($db_type == 0) {
        $year_field = "YEAR(f.insert_date)";
    } else if ($db_type == 2) {
        $year_field = "to_char(f.insert_date,'YYYY')";
    }
    
    if( $ex_data[4]!=0 )
        $str_cond=" and a.sewing_line= $ex_data[4]";
    else
        $str_cond="";

    $sql = "SELECT d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, f.company_name, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, SUM(c.reject_qty) as raj_qty, SUM(c.alter_qty) as alt_qty, SUM(c.spot_qty) as spt_qty, SUM(c.replace_qty) as replace_qty, e.po_number from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type=1 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, f.company_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
    $result = sql_select($sql);
    $msg_type=1;
    if(count($result)>0){
        foreach($result as $row)
        {
            if($row[csf('company_name')]!=$ex_data[3]){
                $msg_type=2;
            }
        }
    }
    else $msg_type=3;
    
    echo $msg_type;
    exit();
}

if ($action == "bundle_nos") {

    
    /*  if($db_type==0)
    {
        $bundle_nos=return_field_value("group_concat(b.bundle_no order by b.id desc) as bundle_no", "pro_garments_production_mst a, pro_garments_production_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
    }
    else if($db_type==2)
    {
        $bundle_nos=return_field_value("LISTAGG(b.bundle_no, ',') WITHIN GROUP (ORDER BY b.id desc) as bundle_no", "pro_garments_production_mst a, pro_garments_production_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
    }*/

    $bundle_nosww = return_library_array("select b.barcode_no, b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "bundle_no", "barcode_no");
    $bundle_nos = implode(",", $bundle_nosww);
	
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
	}
    
    $output_bundles=return_library_array( "select b.bundle_no, sum(b.production_qnty) as issue_qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where  a.id=b.delivery_mst_id $bundle_nos_cond and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no",'bundle_no','issue_qty');
    
    if(count($output_bundles)>0)
        echo $bundle_nos."**1";
    else
        echo $bundle_nos."**0";
    exit();
}

if ($action == "color_and_size_level") {
    $dataArr = explode("**", $data);
    $po_id = $dataArr[0];
    $item_id = $dataArr[1];
    $variableSettings = $dataArr[2];
    $styleOrOrderWisw = $dataArr[3];
    $embelName = $dataArr[4];
    $country_id = $dataArr[5];

    $color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
    $size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

    //#############################################################################################//
    //order wise - color level, color and size level

    //$variableSettings=2;

    if ($variableSettings == 2) // color level
    {
        if ($db_type == 0) {
            $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, 
            (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) 
            from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, 
            (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END)
            from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName'
            and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
            from wo_po_color_size_breakdown
            where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
            group by color_number_id";
        } else {
            $sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
            sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
            sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty
            from wo_po_color_size_breakdown a 
            left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
            left join pro_garments_production_mst c on c.id=b.mst_id
            where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and 
            a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";

        }

        $colorResult = sql_select($sql);
    } else if ($variableSettings == 3) //color and size level
    {


        $dtlsData = sql_select("select a.color_size_break_down_id,
            sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
            sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty 
            from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and
            b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0
            and a.production_type in(1,2) group by a.color_size_break_down_id");

        foreach ($dtlsData as $row) {
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
        }
        //print_r($color_size_qnty_array);

        $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
        from wo_po_color_size_breakdown
        where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
        order by color_number_id, id";

        $colorResult = sql_select($sql);
    }

    $colorHTML = "";
    $colorID = '';
    $chkColor = array();
    $i = 0;
    $totalQnty = 0;
    foreach ($colorResult as $color) {
        if ($variableSettings == 2) // color level
        {
            $colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:80px"  class="text_boxes_numeric" placeholder="' . ($color[csf("production_qnty")] - $color[csf("cur_production_qnty")]) . '" onblur="fn_colorlevel_total(' . ($i + 1) . ')"></td></tr>';
            $totalQnty += $color[csf("production_qnty")] - $color[csf("cur_production_qnty")];
            $colorID .= $color[csf("color_number_id")] . ",";
        } else //color and size level
        {
            if (!in_array($color[csf("color_number_id")], $chkColor)) {
                if ($i != 0) $colorHTML .= "</table></div>";
                $i = 0;
                $colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span> </h3>';
                $colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><table id="table_' . $color[csf("color_number_id")] . '">';
                $chkColor[] = $color[csf("color_number_id")];
            }
            //$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
            $colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";

            $iss_qnty = $color_size_qnty_array[$color[csf('id')]]['iss'];
            $rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['rcv'];


            $colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" placeholder="' . ($iss_qnty - $rcv_qnty) . '" onblur="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')"></td></tr>';
        }
        $i++;
    }
    //echo $colorHTML;die;
    if ($variableSettings == 2) {
        $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>';
    }
    echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
    $colorList = substr($colorID, 0, -1);
    echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
    //#############################################################################################//
    exit();
}

if ($action == "show_dtls_listview") {
    $location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    $supplier_arr = return_library_array("select id, supplier_name from  lib_supplier", 'id', 'supplier_name');

    ?>
    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
            <th width="50">SL</th>
            <th width="150" align="center">Item Name</th>
            <th width="120" align="center">Country</th>
            <th width="80" align="center">Production Date</th>
            <th width="80" align="center">Production Qnty</th>
            <th width="150" align="center">Serving Company</th>
            <th width="120" align="center">Location</th>
            <th align="center">Challan No</th>
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <?php
            $i = 1;
            $total_production_qnty = 0;
            $sqlResult = sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,
                serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1
                and is_deleted=0 order by id");
            foreach ($sqlResult as $selectResult) {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                else $bgcolor = "#FFFFFF";
                $total_production_qnty += $selectResult[csf('production_quantity')];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_issue_form_data','requires/bundle_wise_cutting_delevar_to_input_controller');">
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center">
                        <p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p>
                    </td>
                    <td width="80"
                        align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                    <td width="80" align="center"><?php echo $selectResult[csf('production_quantity')]; ?></td>
                    <?php
                    $source = $selectResult[csf('production_source')];
                    if ($source == 3) $serving_company = $supplier_arr[$selectResult[csf('serving_company')]];
                    else $serving_company = $company_arr[$selectResult[csf('serving_company')]];
                    ?>
                    <td width="150" align="center"><p><?php echo $serving_company; ?></p></td>
                    <td width="120" align="center"><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
                    <td align="center"><p><?php echo $selectResult[csf('challan_no')]; ?></p></td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
    </div>
    <?
    exit();
}

if ($action == "show_country_listview") {
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
        <th width="30">SL</th>
        <th width="110">Item Name</th>
        <th width="80">Country</th>
        <th width="75">Shipment Date</th>
        <th>Order Qty.</th>
        </thead>
        <?
        $i = 1;

        $sqlResult = sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as
            order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0 
            group by po_break_down_id, item_number_id, country_id");
        foreach ($sqlResult as $row) {
            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                onClick="put_country_data(<? echo $row[csf('po_break_down_id')] . "," . $row[csf('item_number_id')] . "," . $row[csf('country_id')] . "," . $row[csf('order_qnty')] . "," . $row[csf('plan_cut_qnty')]; ?>);">
                <td width="30"><? echo $i; ?></td>
                <td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                <td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
                <td width="75"
                    align="center"><? if ($row[csf('country_ship_date')] != "0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>
                    &nbsp;</td>
                <td align="right"><?php echo $row[csf('order_qnty')]; ?></td>
            </tr>
            <?
            $i++;
        }
        ?>
    </table>
    <?
    exit();
}

if($action=="populate_bundle_data_update")
{
    $ex_data = explode("**",$data);
    $bundle=explode(",",trim($ex_data[0]));
    $mst_id=$ex_data[2];
    $bundle_nos="'".implode("','",$bundle)."'";
    
    $bundle_count=count(explode(",",trim($ex_data[0]))); $bundle_nos_cond=""; $recbundle_nos_cond="";
    if($db_type==2 && $bundle_count>400)
    {
        $recbundle_nos_cond=" and (";
        $bundle_nos_cond=" and (";
        $bundleArr=array_chunk(explode(",",trim($ex_data[0])),399);
        foreach($bundleArr as $bundleNos)
        {
            $bundleNos=implode(",",$bundleNos);
            $recbundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
            $bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
        }
        $recbundle_nos_cond=chop($recbundle_nos_cond,'or ');
        $recbundle_nos_cond.=")";
        
        $bundle_nos_cond=chop($bundle_nos_cond,'or ');
        $bundle_nos_cond.=")";
    }
    else
    {
        $recbundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
        $bundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
    }
    
    //$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=2 and a.embel_name=1 and b.status_active=1 and b.is_deleted=0",'bundle_no','bundle_no');
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    
    $output_bundles=return_library_array( "select b.bundle_no, sum(b.production_qnty) as issue_qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where  a.id=b.delivery_mst_id and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by b.bundle_no",'bundle_no','issue_qty');
    //echo  "select b.bundle_no, sum(b.production_qnty) as issue_qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where and b.barcode_no in ($bundle_nos)  and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.bundle_no";
    //$receive_qty=return_field_value("","","a.id=b.delivery_mst_id and b.barcode_no in ($bundle_no","issue_qty");
    
    //print_r($output_bundles);
    
    $year_field="";
    if($db_type==0) 
    {
        $year_field="YEAR(f.insert_date)"; 
    }
    else if($db_type==2) 
    {
        $year_field="to_char(f.insert_date,'YYYY')";
    }
    
    $sql="SELECT c.id as prdid,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and c.production_type=9 and c.delivery_mst_id=".$mst_id." and c.status_active=1 and c.is_deleted=0 $recbundle_nos_cond order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
    //echo $sql;die;
    $result = sql_select($sql);
    $count=count($result);
    $i=$ex_data[1]+$count;
    foreach ($result as $row)
    { 
        if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" || $mst_id[0]!="")
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            //$qty=($bundle_qty_arr[$row[csf('bundle_no')]]+$row[csf('replace_qty')])-($row[csf('raj_qty')]+$row[csf('alt_qty')]+$row[csf('spt_qty')]);
            $qty=$row[csf('production_qnty')];
        	?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>"> 
                <td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>"align="right"><? echo $qty; ?>&nbsp;</td>
                 <? 
				$str_col='';
				$onclick=' onClick="fn_deleteRow('.$i.');" ';
				if($output_bundles[$row[csf('bundle_no')]]!='') 
				{
					$str_col=' bgcolor="#FE6569" ';
					$onclick='';
				}
				?>
                <td id="button_1" align="center" <? echo $str_col; ?> >
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" <? echo $onclick; ?>/>
                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"  value="<? echo $row[csf('prdid')]; ?>"/> 
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo $row[csf('is_rescan')]; ?>"/>
                </td>
            </tr>
        	<?
			$totalQty += $qty;
            $i--;
        }
    }
	?>
    <!-- <tr style="font-weight:bold;">
    	<td colspan="10" align="right">Total</td>
        <td id="tdTotal" align="right"><?php echo $totalQty; ?></td>
    </tr> -->
    <?php
    exit(); 
}
if ($action == "populate_issue_form_data") {
    //production type=2 come from array
    $sqlResult = sql_select("SELECT id,garments_nature,challan_no,po_break_down_id,item_number_id,country_id,production_source,serving_company,location,
        embel_name,embel_type,production_date,production_quantity,production_source,production_type,entry_break_down_type,production_hour,sewing_line,
        supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced,wo_order_id,wo_order_no from pro_garments_production_mst where id='$data' 
        and production_type='9' and status_active=1 and is_deleted=0 order by id");
    //echo "sdfds".$sqlResult;die;
    foreach ($sqlResult as $result) {
        //echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
        echo "$('#txt_issue_qty').val('" . $result[csf('production_quantity')] . "');\n";
        echo "$('#txt_challan').val('" . $result[csf('challan_no')] . "');\n";
        echo "$('#txt_iss_id').val('" . $result[csf('id')] . "');\n";
        echo "$('#txt_remark').val('" . $result[csf('remarks')] . "');\n";
        echo "$('#txt_wo_id').val('" . $result[csf('wo_order_id')] . "');\n";
        echo "$('#txt_wo_no').val('" . $result[csf('wo_order_no')] . "');\n";

        $dataArray = sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=9 and embel_name=" . $result[csf('embel_name')] . " THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=" . $result[csf('po_break_down_id')] . " and item_number_id=" . $result[csf('item_number_id')] . " and country_id=" . $result[csf('country_id')] . " and is_deleted=0");
        foreach ($dataArray as $row) {
            echo "$('#txt_cutting_qty').val('" . $row[csf('totalCutting')] . "');\n";
            echo "$('#txt_cumul_issue_qty').val('" . $row[csf('totalPrinting')] . "');\n";
            $yet_to_produced = $row[csf('totalCutting')] - $row[csf('totalPrinting')];
            echo "$('#txt_yet_to_issue').val('" . $yet_to_produced . "');\n";
        }

        echo "get_php_form_data(" . $result[csf('po_break_down_id')] . "+'**'+" . $result[csf("item_number_id")] . "+'**'+" . $result[csf("embel_name")] . "+'**'+" . $result[csf("country_id")] . ", 'populate_data_from_search_popup', 'requires/bundle_wise_cutting_delevar_to_input_controller' );\n";

        echo "$('#cbo_item_name').val('" . $result[csf('item_number_id')] . "');\n";
        echo "$('#cbo_country_name').val('" . $result[csf('country_id')] . "');\n";

        echo "show_list_view('" . $result[csf('po_break_down_id')] . "','show_country_listview','list_view_country','requires/bundle_wise_cutting_delevar_to_input_controller','');\n";

        echo "$('#txt_mst_id').val('" . $result[csf('id')] . "');\n";
        echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";

        //break down of color and size------------------------------------------
        //#############################################################################################//
        // order wise - color level, color and size level
        $color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

        $variableSettings = $result[csf('entry_break_down_type')];
        if ($variableSettings != 1) // gross level
        {
            $po_id = $result[csf('po_break_down_id')];
            $item_id = $result[csf('item_number_id')];
            $country_id = $result[csf('country_id')];

            $sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id 
                from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id
                and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
            foreach ($sql_dtls as $row) {
                if ($variableSettings == 2) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')] . $row[csf('color_number_id')];
                $amountArr[$index] = $row[csf('production_qnty')];
            }

            //$variableSettings=2;


            if ($variableSettings == 2) // color level
            {
                if ($db_type == 0) {
                    $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty,
                    (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END)
                    from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty,
                    (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) 
                    from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
                    from wo_po_color_size_breakdown
                    where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
                    group by color_number_id";
                } else {
                    $sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
                    sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
                    sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as cur_production_qnty
                    from wo_po_color_size_breakdown a 
                    left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
                    left join pro_garments_production_mst c on c.id=b.mst_id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) and b.is_deleted=0 and b.status_active=1 group by a.item_number_id, a.color_number_id";

                }
            } else if ($variableSettings == 3) //color and size level
            {
                $dtlsData = sql_select("select a.color_size_break_down_id,
                    sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
                    sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty 
                    from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and  b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id'
                    and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) 
                    group by a.color_size_break_down_id");

                foreach ($dtlsData as $row) {
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
                }
                //print_r($color_size_qnty_array);

                $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
                from wo_po_color_size_breakdown
                where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) 
                order by color_number_id";

            } else // by default color and size level
            {
                $dtlsData = sql_select("select a.color_size_break_down_id,
                    sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
                    sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty 
                    from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id'
                    and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2)
                    group by a.color_size_break_down_id");

                foreach ($dtlsData as $row) {
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
                }
                //print_r($color_size_qnty_array);

                $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
                from wo_po_color_size_breakdown
                where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
                order by color_number_id";
            }

            $colorResult = sql_select($sql);
            //print_r($sql);die;
            $colorHTML = "";
            $colorID = '';
            $chkColor = array();
            $i = 0;
            $totalQnty = 0;
            $colorWiseTotal = 0;
            foreach ($colorResult as $color) {
                if ($variableSettings == 2) // color level
                {
                    $amount = $amountArr[$color[csf("color_number_id")]];
                    $colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:80px"  class="text_boxes_numeric" placeholder="' . ($color[csf("production_qnty")] - $color[csf("cur_production_qnty")] + $amount) . '" value="' . $amount . '" onblur="fn_colorlevel_total(' . ($i + 1) . ')"></td></tr>';
                    $totalQnty += $amount;
                    $colorID .= $color[csf("color_number_id")] . ",";
                } else //color and size level
                {
                    $index = $color[csf("size_number_id")] . $color[csf("color_number_id")];
                    $amount = $amountArr[$index];
                    if (!in_array($color[csf("color_number_id")], $chkColor)) {
                        if ($i != 0) $colorHTML .= "</table></div>";
                        $i = 0;
                        $colorWiseTotal = 0;
                        $colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span></h3>';
                        $colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><table id="table_' . $color[csf("color_number_id")] . '">';
                        $chkColor[] = $color[csf("color_number_id")];
                        $totalFn .= "fn_total(" . $color[csf("color_number_id")] . ");";
                    }
                    $colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";

                    $iss_qnty = $color_size_qnty_array[$color[csf('id')]]['iss'];
                    $rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['rcv'];


                    $colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" placeholder="' . ($iss_qnty - $rcv_qnty + $amount) . '" onblur="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')" value="' . $amount . '" ></td></tr>';
                    $colorWiseTotal += $amount;
                }
                $i++;
            }
            //echo $colorHTML;die;
            if ($variableSettings == 2) {
                $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" value="' . $totalQnty . '" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>';
            }
            echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
            if ($variableSettings == 3) echo "$totalFn;\n";
            $colorList = substr($colorID, 0, -1);
            echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
        }//end if condtion
        //#############################################################################################//
    }
    exit();
}

//pro_garments_production_mst
if ($action == "save_update_delete") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $prod_reso_allocation = return_field_value("auto_update", "variable_settings_production", "company_name=$cbo_company_name and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = ($prod_reso_allocation=="") ? 2 : $prod_reso_allocation; // 2 means NO

    if ($operation == 0) // Insert Here----------------------------------------------------------
    {
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }
        //table lock here  entry form 160 =all production pages
        // if ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}

        if (str_replace("'", "", $txt_system_id) == "") 
        {
            // $mst_id = return_next_id("id", "pro_gmts_delivery_mst", 1);
            if ($db_type == 0) $year_cond = "YEAR(insert_date)";
            else if ($db_type == 2) $year_cond="to_char(insert_date,'YYYY')";
            else $year_cond = "";//defined Later
          
            $new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'CDI',0,date("Y",time()),0,0,4,0,0 ));
            $field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, production_source, serving_company, floor_id,sewing_line, organic, delivery_date,entry_form,working_company_id,working_location_id,remarks, inserted_by, insert_date";
            $mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
            $data_array_delivery = "(" . $mst_id . ",'" . $new_sys_number[1] . "','" .(int) $new_sys_number[2] . "','" . $new_sys_number[0] . "', " . $cbo_company_name . ",9," . $cbo_location . "," . $delivery_basis . "," . $cbo_source . "," . $cbo_emb_company . ",0,0," . $txt_organic . "," . $txt_issue_date . ",347,".$cbo_working_company_name.",".$cbo_working_location.",".$txt_remark_mst."," . $user_id . ",'" . $pc_date_time . "')";
            $challan_no =(int) $new_sys_number[2];
            $txt_challan_no = $new_sys_number[0];
        } 
        else 
        {
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_challan_no));
            $challan_no = (int)$txt_chal_no[3];

            $field_array_delivery = "company_id*location_id*delivery_basis*production_source*serving_company*floor_id*sewing_line*organic*delivery_date*working_company_id*working_location_id*remarks*updated_by*update_date";
            $data_array_delivery = "" . $cbo_company_name . "*" . $cbo_location . "*" . $delivery_basis . "*" . $cbo_source . "*" . $cbo_emb_company . "*0*0*" . $txt_organic . "*" . $txt_issue_date . "*" . $cbo_working_company_name . "*" . $cbo_working_location . "*" . $txt_remark_mst . "*" . $user_id . "*'" . $pc_date_time . "'";
        }
        for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;
            $cutNo="cutNo_".$j;
            $is_rescan="isRescan_".$j;
            if($$is_rescan!=1)
            {
              $bundleCheckArr[trim($$bundleCheck)]=trim($$bundleCheck); 
            }
            $all_cut_no_arr[$$cutNo]=$$cutNo;
        }
        $cut_nums="'".implode("','", $all_cut_no_arr)."'";
        $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
        $bundle_wise_type_array=array();
        $bundle_wise_data=sql_select($bundle_wise_type_sql);
        foreach($bundle_wise_data as $vals)
        {
            $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
        }
         

        $bundle="'".implode("','",$bundleCheckArr)."'";
        $receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_dtls c where  c.bundle_no  in ($bundle)  and c.production_type=9 and c.status_active=1 and c.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null)"; ;
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {
            
            $duplicate_bundle[trim($row[csf('bundle_no')])]=trim($row[csf('bundle_no')]);
        }
        if (str_replace("'", "", $delivery_basis) == 3) 
        {
            //$id = return_next_id("id", "pro_garments_production_mst", 1);

            $field_array_mst = "id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type,wo_order_id,wo_order_no, remarks, floor_id,sewing_line,prod_reso_allo,entry_form, inserted_by, insert_date";
            //echo "10**"; 
            $mstArr = array();
            $dtlsArr = array();
            $colorSizeArr = array();
            $mstIdArr = array();
            $colorSizeIdArr = array();
            for ($j = 1; $j <= $tot_row; $j++) {
                $cutNo="cutNo_".$j;
                $bundleNo = "bundleNo_" . $j;
                $barcodeNo="barcodeNo_".$j;
                $orderId = "orderId_" . $j;
                $gmtsitemId = "gmtsitemId_" . $j;
                $countryId = "countryId_" . $j;
                $colorId = "colorId_" . $j;
                $sizeId = "sizeId_" . $j;
                $colorSizeId = "colorSizeId_" . $j;
                $qty = "qty_" . $j;
                $checkRescan="isRescan_".$j;
                if($duplicate_bundle[trim($$bundleNo)]=='')
                {
                    $bundleCutArr[$$bundleNo]=$$cutNo;
                    $cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
                    $mstArr[$$orderId][$$gmtsitemId][$$countryId] += $$qty;
                    $colorSizeArr[$$bundleNo] = $$orderId . "**" . $$gmtsitemId . "**" . $$countryId;
                    $dtlsArr[$$bundleNo] += $$qty;
                    $dtlsArrColorSize[$$bundleNo] = $$colorSizeId;
                    $bundleRescanArr[$$bundleNo]=$$checkRescan;
                    $bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
                }
               
            }

            foreach ($mstArr as $orderId => $orderData) {
                foreach ($orderData as $gmtsItemId => $gmtsItemIdData) {
                    foreach ($gmtsItemIdData as $countryId => $qty) {
                        $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
                        if ($data_array_mst != "") $data_array_mst .= ",";
                        $data_array_mst .= "(" . $id . "," . $mst_id . ",'" . $cutArr[$orderId][$gmtsItemId][$countryId] . "'," . $cbo_company_name . "," . $garments_nature . ",'" . $challan_no . "'," . $orderId . ", " . $gmtsItemId . "," . $countryId . ", " . $cbo_source . "," . $cbo_emb_company . "," . $cbo_location . "," . $txt_issue_date . "," . $qty . ",9," . $sewing_production_variable . "," . $txt_wo_id . "," . $txt_wo_no . ",'" . $txt_remark . "',0,0," . $prod_reso_allocation . ",347," . $user_id . ",'" . $pc_date_time . "')";
                        $mstIdArr[$orderId][$gmtsItemId][$countryId] = $id;
                        
                    }
                }
            }

          //  $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
            $field_array_dtls ="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id, production_qnty, cut_no, bundle_no,entry_form,barcode_no,is_rescan,color_type_id";

            foreach ($dtlsArr as $bundle_no => $qty) {

                $colorSizedData = explode("**", $colorSizeArr[$bundle_no]);
                $gmtsMstId = $mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                //$cut_no=$cutArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                $cut_no=$bundleCutArr[$bundle_no];
                $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

                if ($data_array_dtls != "") $data_array_dtls .= ",";
                $data_array_dtls .= "(" . $dtls_id . "," . $mst_id . "," . $gmtsMstId . ",9,'" . $dtlsArrColorSize[$bundle_no] . "','" . $qty . "','" . $cut_no . "','" . $bundle_no . "',347,'".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."')";
                //$colorSizeIdArr[$colorSizeId]=$dtls_id;
                 
            }

 
            if (str_replace("'", "", $txt_system_id) == "") {
                $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
            } else {
                $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            }
            $rID = sql_insert("pro_garments_production_mst", $field_array_mst, $data_array_mst, 1);
            $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array_dtls, $data_array_dtls, 1);

         //echo "10**insert into pro_garments_production_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

            // echo "10**".$data_array_dtls;die;
           // echo $challanrID ."&&b". $rID ."&&a". $dtlsrID ;die;
            //release lock table
           
            // echo "10**".$challanrID.$rID.$dtlsrID;die;
            if ($db_type == 0) {
                if ($challanrID && $rID && $dtlsrID) {
                    mysql_query("COMMIT");
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($challanrID && $rID && $dtlsrID) {
                    oci_commit($con);
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        } 
        else 
        {
            //$id = return_next_id("id", "pro_garments_production_mst", 1);
             $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

            $field_array1 = "id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type,wo_order_id,wo_order_no, remarks, floor_id,sewing_line,prod_reso_allo,entry_form, inserted_by, insert_date";

            $data_array1 = "(" . $id . "," . $mst_id . "," . $cbo_company_name . "," . $garments_nature . ",'" . $challan_no . "'," . $hidden_po_break_down_id . ", " . $cbo_item_name . "," . $cbo_country_name . ", " . $cbo_source . "," . $cbo_emb_company . "," . $cbo_location . "," . $txt_issue_date . "," . $txt_issue_qty . ",9," . $sewing_production_variable . "," . $txt_wo_id . "," . $txt_wo_no . "," . $txt_remark . "," . $cbo_floor . "," . $cbo_line_no . "," . $prod_reso_allocation . ",347," . $user_id . ",'" . $pc_date_time . "')";


            //echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
            // pro_garments_production_dtls table entry here ----------------------------------///
            $field_array = "id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,entry_form";
            $dtlsrID = true;
            if (str_replace("'", "", $sewing_production_variable) == 2)//color level wise
            {
                $color_sizeID_arr = sql_select("select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id");
                $colSizeID_arr = array();
                foreach ($color_sizeID_arr as $val) {
                    $index = $val[csf("color_number_id")];
                    $colSizeID_arr[$index] = $val[csf("id")];
                }
                // $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
                $rowEx = explode("**", $colorIDvalue);
               // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                $data_array = "";
                $j = 0;
                foreach ($rowEx as $rowE => $val) {
                    $colorSizeNumberIDArr = explode("*", $val);
                    //2 for Issue to Print / Emb Entry
                     $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                    if ($j == 0) $data_array = "(" . $dtls_id . "," . $mst_id . "," . $id . ",9,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "',347)";
                    else $data_array .= ",(" . $dtls_id . "," . $mst_id . "," . $id . ",9,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "',347)";
                   
                    $j++;
                }
            }

            if (str_replace("'", "", $sewing_production_variable) == 3)//color and size wise
            {
                $color_sizeID_arr = sql_select("select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id");
                $colSizeID_arr = array();
                foreach ($color_sizeID_arr as $val) {
                    $index = $val[csf("size_number_id")] . $val[csf("color_number_id")];
                    $colSizeID_arr[$index] = $val[csf('id')];
                }

                //  colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value --------------------------//
                $rowEx = explode("***", $colorIDvalue);
                //$dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                $data_array = "";
                $j = 0;
                foreach ($rowEx as $rowE => $valE) {
                    $colorAndSizeAndValue_arr = explode("*", $valE);
                    $sizeID = $colorAndSizeAndValue_arr[0];
                    $colorID = $colorAndSizeAndValue_arr[1];
                    $colorSizeValue = $colorAndSizeAndValue_arr[2];
                    $index = $sizeID . $colorID;

                    //2 for Issue to Print / Emb Entry
                     $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

                    if ($j == 0) $data_array = "(" . $dtls_id . "," . $mst_id . "," . $id . ",9,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "')";
                    else $data_array .= ",(" . $dtls_id . "," . $mst_id . "," . $id . ",9,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "')";
                     
                    $j++;
                }
            }
            //echo "10** insert into pro_garments_production_mst($field_array1)values".$data_array1;die;
            $rID = sql_insert("pro_garments_production_mst", $field_array1, $data_array1, 1);
            if (str_replace("'", "", $txt_system_id) == "") {
                $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
            } else {
                $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            }

            $dtlsrID = true;
            if (str_replace("'", "", $sewing_production_variable) == 2 || str_replace("'", "", $sewing_production_variable) == 3) {
                $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array, $data_array, 1);
            }

            

            // echo "10**".$rID."**".$challanrID."**".$dtlsrID."**".$challanrID;die('hhhhh');
            if ($db_type == 0) {
                if ($rID && $challanrID && $dtlsrID) {
                    mysql_query("COMMIT");
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($rID && $challanrID && $dtlsrID) {
                    oci_commit($con);
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }
        //check_table_status(160, 0);
        disconnect($con);
        die;      
    } 

    else if ($operation == 1) // Update Here End------------------------------------------------------
    {
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }
        //table lock here
        //   if  ( check_table_status( 160, 1 )==0 ) { echo "15**1"; die;}
        $mst_id = str_replace("'", "", $txt_system_id);
        $txt_chal_no = explode("-", str_replace("'", "", $txt_challan_no));
        $challan_no = (int)$txt_chal_no[3];

        $cbo_floor =str_replace("'", "", $cbo_floor);
        $cbo_line_no = str_replace("'", "", $cbo_line_no);
        if($cbo_floor==""){$cbo_floor=0;}
        if($cbo_line_no==""){ $cbo_line_no=0;}

        $field_array_delivery = "company_id*location_id*production_source*serving_company*floor_id*sewing_line*organic*delivery_date*working_company_id*working_location_id*remarks*updated_by*update_date";
        $data_array_delivery = "" . $cbo_company_name . "*" . $cbo_location . "*" . $cbo_source . "*" . $cbo_emb_company . "*" . $cbo_floor . "*" . $cbo_line_no . "*" . $txt_organic . "*" . $txt_issue_date . "*".$cbo_working_company_name."*".$cbo_working_location."*".$txt_remark_mst."*" . $user_id . "*'" . $pc_date_time . "'";

        if (str_replace("'", "", $delivery_basis) == 3)
        {
             
            //$id = return_next_id("id", "pro_garments_production_mst", 1);
           // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
            $non_delete_arr=production_validation($mst_id,5);
            $issue_data_arr=production_data($mst_id,4);
            
            for($j=1;$j<=$tot_row;$j++)
            {   
                $bundleCheck="bundleNo_".$j;  
                $is_rescanww="isRescan_".$j;
                if($$is_rescanww!=1)
                {                              
                    $bundleCheckArr[trim($$bundleCheck)]=trim($$bundleCheck); 
                }
                $cutNo="cutNo_".$j;
                $all_cut_no_arr[$$cutNo]=$$cutNo;
            }
            $cut_nums="'".implode("','", $all_cut_no_arr)."'";
            $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
            $bundle_wise_type_array=array();
            $bundle_wise_data=sql_select($bundle_wise_type_sql);
            foreach($bundle_wise_data as $vals)
            {
                $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
            }

            $bundle="'".implode("','",$bundleCheckArr)."'";
            $receive_sql="SELECT c.barcode_no,c.bundle_no from pro_garments_production_dtls c where  c.bundle_no  in ($bundle)  and c.production_type=9 and c.status_active=1 and c.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null) and delivery_mst_id<>$mst_id"; 
            $receive_result = sql_select($receive_sql);
            foreach ($receive_result as $row)
            {
                $duplicate_bundle[trim($row[csf('bundle_no')])]=trim($row[csf('bundle_no')]);
            }           
            

            $field_array_mst = "id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type,wo_order_id,wo_order_no, remarks, floor_id,sewing_line,prod_reso_allo,entry_form, inserted_by, insert_date,updated_by,update_date";
            //echo "0**";
            $mstArr = array();
            $dtlsArr = array();
            $colorSizeArr = array();
            $mstIdArr = array();
            $colorSizeIdArr = array();
            for ($j = 1; $j <= $tot_row; $j++) {
                $cutNo="cutNo_".$j;
                $bundleNo = "bundleNo_" . $j;
                $barcodeNo="barcodeNo_".$j;
                $orderId = "orderId_" . $j;
                $gmtsitemId = "gmtsitemId_" . $j;
                $countryId = "countryId_" . $j;
                $colorId = "colorId_" . $j;
                $sizeId = "sizeId_" . $j;
                $colorSizeId = "colorSizeId_" . $j;
                $qty = "qty_" . $j;
                $checkRescan="isRescan_".$j;
                
                if($non_delete_arr[$$bundleNo]=="" && $duplicate_bundle[trim($$bundleNo)]==''){
                    $bundleCutArr[$$bundleNo]=$$cutNo;
                    $cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
                    $mstArr[$$orderId][$$gmtsitemId][$$countryId] += $$qty;
                    $colorSizeArr[$$bundleNo] = $$orderId . "**" . $$gmtsitemId . "**" . $$countryId;
                    $dtlsArr[$$bundleNo] += $$qty;
                    $dtlsArrColorSize[$$bundleNo] = $$colorSizeId;
                    $bundleRescanArr[$$bundleNo]=$$checkRescan;
                    $bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
                }
            }
            
            // Not Delete Data...............................start;


            foreach($non_delete_arr as $bi)
            {


            	if($duplicate_bundle[trim($bi)]=='')
            	{
            		 
	                $bundleCutArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
	                $bundleCutArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
	                $cutArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
	                $mstArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
	                $colorSizeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('po_break_down_id')]."**".$issue_data_arr[trim($bi)][csf('item_number_id')]."**".$issue_data_arr[trim($bi)][csf('country_id')];
	                
	                $dtlsArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
	                //$issue_data_arr[trim($bi)][csf('bundle_no')]
	                $dtlsArrColorSize[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('color_size_break_down_id')];
	                $bundleBarcodeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('barcode_no')];
	                $bundleBarcodeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('barcode_no')];
	                $bundleRescanArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('is_rescan')];
	            }


            }  
            // Not Delete Data...............................end;
            //echo "10**";
            //print_r($bundleBarcodeArr);
            // die;
            foreach ($mstArr as $orderId => $orderData)
             {
                if($orderId)
                {


                    foreach ($orderData as $gmtsItemId => $gmtsItemIdData)
                     {
                        foreach ($gmtsItemIdData as $countryId => $qty) 
                        {
                             $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
                            if ($data_array_mst != "") $data_array_mst .= ",";
                            $data_array_mst .= "(" . $id . "," . $mst_id . ",'" . $cutArr[$orderId][$gmtsItemId][$countryId] . "'," . $cbo_company_name . "," . $garments_nature . ",'" . $challan_no . "'," . $orderId . ", " . $gmtsItemId . "," . $countryId . ", " . $cbo_source . "," . $cbo_emb_company . "," . $cbo_location . "," . $txt_issue_date . "," . $qty . ",9," . $sewing_production_variable . "," . $txt_wo_id . "," . $txt_wo_no . ",'" . $txt_remark . "'," . $cbo_floor . "," . $cbo_line_no . "," . $prod_reso_allocation . ",347," . $user_id . ",'" . $pc_date_time . "'," . $user_id . ",'" . $pc_date_time . "')";
                            $mstIdArr[$orderId][$gmtsItemId][$countryId] = $id;
                             
                        }
                    }
                }
            }

            $field_array_dtls = "id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,cut_no,bundle_no, entry_form,barcode_no,is_rescan,color_type_id";

            foreach ($dtlsArr as $bundle_no => $qty)
             {
                if($bundle_no)
                {


                    $colorSizedData = explode("**", $colorSizeArr[$bundle_no]);
                    $gmtsMstId = $mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                    //$cut_no=$cutArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                     $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                    $cut_no=$bundleCutArr[$bundle_no];
                    if ($data_array_dtls != "") $data_array_dtls .= ",";
                    $data_array_dtls .= "(" . $dtls_id . "," . $mst_id . "," . $gmtsMstId . ",9,'" . $dtlsArrColorSize[$bundle_no] . "','" . $qty . "','" . $cut_no . "','" . $bundle_no . "',347,'".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."')";
                    //$colorSizeIdArr[$colorSizeId]=$dtls_id;
                }
                 
            }   
                     
            if($data_array_mst!="" && $data_array_dtls!="")
            {
                $delete = execute_query("UPDATE pro_garments_production_mst SET updated_by=$user_id,update_date='$pc_date_time',is_deleted=1, status_active=0  WHERE delivery_mst_id=$mst_id and production_type=9", 0);
                $delete_dtls = execute_query("UPDATE pro_garments_production_dtls SET is_deleted=1, status_active=0 WHERE delivery_mst_id=$mst_id and production_type=9", 0); 
            }
         
            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            $rID = sql_insert("pro_garments_production_mst", $field_array_mst, $data_array_mst, 1);
            $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array_dtls, $data_array_dtls, 1);

            // echo "10**insert into pro_garments_production_mst (".$field_array_mst.") values ".$data_array_mst;die;
            //echo $challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID ."&&". $delete ."&&". $delete_dtls ."&&". $delete_bundle;die;
            //release lock table
             if ($db_type == 0) {
                if ($challanrID && $rID && $dtlsrID && $delete && $delete_dtls) {
                    mysql_query("COMMIT");
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no)."**".implode(',',$non_delete_arr);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($challanrID && $rID && $dtlsrID && $delete && $delete_dtls) {
                    oci_commit($con);
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no)."**".implode(',',$non_delete_arr);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        } 
        else 
        {
            // pro_garments_production_mst table data entry here
            $field_array1 = "production_source*serving_company*location*production_date*production_quantity*production_type*entry_break_down_type*challan_no*wo_order_id*wo_order_no*remarks*floor_id*sewing_line*total_produced*yet_to_produced*prod_reso_allo*updated_by*update_date";

            $data_array1 = "" . $cbo_source . "*" . $cbo_emb_company . "*" . $cbo_location . "*" . $txt_issue_date . "*" . $txt_issue_qty . "*9*" . $sewing_production_variable . "*'" . $challan_no . "'*" . $txt_wo_id . "*" . $txt_wo_no . "*" . $txt_remark . "*" . $cbo_floor . "*" . $cbo_line_no . "*" . $txt_cumul_issue_qty . "*" . $txt_yet_to_issue . "*" . $prod_reso_allocation . "*" . $user_id . "*'" . $pc_date_time . "'";
            // pro_garments_production_dtls table data entry here

            if (str_replace("'", "", $sewing_production_variable) != 1 && str_replace("'", "", $txt_mst_id) != '')// check is not gross level
            {
                //$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
                $dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls SET is_deleted=1, status_active=0  where mst_id=$txt_mst_id", 1);
                $field_array = "id, mst_id, production_type, color_size_break_down_id, production_qnty";

                if (str_replace("'", "", $sewing_production_variable) == 2)//color level wise
                {
                    $color_sizeID_arr = sql_select("select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id");
                    $colSizeID_arr = array();
                    foreach ($color_sizeID_arr as $val) {
                        $index = $val[csf("color_number_id")];
                        $colSizeID_arr[$index] = $val[csf("id")];
                    }

                    // $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
                    $rowEx = explode("**", $colorIDvalue);
                   // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                    $data_array = "";
                    $j = 0;
                    foreach ($rowEx as $rowE => $val) {
                        $colorSizeNumberIDArr = explode("*", $val);
                        //2 for Issue to Print / Emb Entry
                         $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                        if ($j == 0) $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",9,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "')";
                        else $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",9,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "')";
                         
                        $j++;
                    }
                }

                if (str_replace("'", "", $sewing_production_variable) == 3)//color and size wise
                {
                    $color_sizeID_arr = sql_select("select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id");
                    $colSizeID_arr = array();
                    foreach ($color_sizeID_arr as $val) {
                        $index = $val[csf("size_number_id")] . $val[csf("color_number_id")];
                        $colSizeID_arr[$index] = $val[csf("id")];
                    }

                    //  colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value --------------------------//
                    $rowEx = explode("***", $colorIDvalue);
                   // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                    $data_array = "";
                    $j = 0;
                    foreach ($rowEx as $rowE => $valE) {
                        $colorAndSizeAndValue_arr = explode("*", $valE);
                        $sizeID = $colorAndSizeAndValue_arr[0];
                        $colorID = $colorAndSizeAndValue_arr[1];
                        $colorSizeValue = $colorAndSizeAndValue_arr[2];
                        $index = $sizeID . $colorID;
                        //2 for Issue to Print / Emb Entry
                         $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                        if ($j == 0) $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",9,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "')";
                        else $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",9,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "')";
                        
                        $j++;
                    }
                }

                //$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
            }//end cond


            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            $rID = sql_update("pro_garments_production_mst", $field_array1, $data_array1, "id", "" . $txt_mst_id . "", 1);//echo $rID;die;
            $dtlsrID = true;
            if (str_replace("'", "", $sewing_production_variable) != 1 && str_replace("'", "", $txt_mst_id) != '')// check is not gross level
            {
                $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array, $data_array, 1);
            }

            //release lock table
          //  check_table_status($_SESSION['menu_id'], 0);

            //echo "10**-".$field_array;die;


            //echo '10**'.$rID .'&&'. $challanrID .'&&'. $dtlsrID .'&&'. $dtlsrDelete;die;

            if ($db_type == 0) {
                if ($rID && $challanrID && $dtlsrID && $dtlsrDelete) {
                    mysql_query("COMMIT");
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 2 || $db_type == 1) {
                if ($rID && $challanrID && $dtlsrID && $dtlsrDelete) {
                    oci_commit($con);
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }
        //check_table_status(160, 0);
        disconnect($con);
        die;  
    } 

    else if ($operation == 2) // Delete Here  ------------------------------------------------------
    {
        $con = connect();
        if ($db_type == 0)
         {
            mysql_query("BEGIN");
         }
        for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;             
            $bundleCheckArr[$$bundleCheck]=$$bundleCheck;             
        }
        $bundle="'".implode("','",$bundleCheckArr)."'";
        $mst_id = str_replace("'", "", $txt_system_id);
        $txt_chal_no = explode("-", str_replace("'", "", $txt_challan_no));
        $challan_no = (int)$txt_chal_no[3];
        $is_output=sql_select("SELECT bundle_no from pro_garments_production_dtls where status_active=1 and is_deleted=0 and production_type=4 and bundle_no in($bundle) order by bundle_no");
        foreach ($is_output as $key => $value)
         {
            $output_bundle[$key]=$value[csf("bundle_no")];
         }
        $all_output_bundle= "'".implode("','",$output_bundle)."'";
        if(count($is_output)<=0)
        {  
             $delete_deliver_mst = execute_query("UPDATE pro_gmts_delivery_mst SET is_deleted=1, status_active=0 WHERE id=$mst_id", 0);
            $delete_mst = execute_query("UPDATE pro_garments_production_mst SET updated_by=$user_id,update_date='$pc_date_time',is_deleted=1, status_active=0  WHERE delivery_mst_id=$mst_id and production_type=9", 0);
            $delete_dtls = execute_query("UPDATE pro_garments_production_dtls SET is_deleted=1, status_active=0 WHERE delivery_mst_id=$mst_id and production_type=9", 0);

            if ($db_type == 0) 
             {
                if ($delete_mst && $delete_dtls && $delete_deliver_mst)
                 {
                    mysql_query("COMMIT");
                    echo "2**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                 }
                else 
                {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } 
             
            else if ($db_type == 1 || $db_type == 2) 
            {
                if ($delete_mst && $delete_dtls && $delete_deliver_mst)
                 {
                    oci_commit($con);
                    echo "2**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                 } 
                else
                 {
                    oci_rollback($con);
                    echo "10**";
                 }
            }
         
        }   
        else
        {
            echo "141**$all_output_bundle" ;
        } 
         
        disconnect($con);
        die;  
    } 
}

if ($action == "challan_no_popup") {
   echo load_html_head_contents("Challan Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>

    <script>
        
        function js_set_value(id) {
            $('#hidden_mst_id').val(id);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:830px;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:820px;">
                <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th>Sewing Company</th>
                    <th>Order No</th>
                    <th>Challan No</th>
                    <th>Cutting No</th>
                    <th>Line No</th>
                    <th>Input Date</th>
                    
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
                               class="formbutton"/>
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
                               value="<? echo $cbo_company_name; ?>">
                        <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">
                    </th>
                    </thead>
                    <tr class="general">
                        <td align="center" id="emb_company_td">
                            <?
                            echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "");

                            ?>
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes" name="txt_order_no" id="txt_order_no"/>
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes" name="txt_search_common"
                                   id="txt_search_common"/></td>
                         <td><input type="text" style="width:100px" class="text_boxes"  name="txt_cut_no" id="txt_cut_no" /></td>
                        <td>
                            <?
                            $line_library = return_library_array("select id,line_name from lib_sewing_line where company_name=$cbo_emb_company", "id", "line_name");
                            echo create_drop_down("cbo_line_no", 100, $line_library, "", 1, "--- Select ---", $selected, "");
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_issue_date" id="txt_issue_date" value="" class="datepicker"
                                   style="width:107px;"/>
                        </td>
                        
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_line_no').value+'_'+document.getElementById('txt_issue_date').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_emb_company').value+'_'+document.getElementById('txt_order_no').value+'_<?php echo $cbo_source; ?>'+'_'+document.getElementById('txt_cut_no').value, 'create_challan_search_list_view', 'search_div', 'bundle_wise_cutting_delevar_to_input_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                                   style="width:100px;"/>
                        </td>

                    </tr>
                </table>
                <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        load_drop_down('bundle_wise_cutting_delevar_to_input_controller','<?php echo $cbo_source; ?>_<?php echo $cbo_emb_company; ?>'  , 'load_drop_down_sewing_company', 'emb_company_td');
    </script>

    </html>
    
    <?
}

if ($action == "load_drop_down_sewing_company") {
    $explode_data = explode("_", $data);
    $data = $explode_data[0];
    $serving_company =$explode_data[1];// $explode_data[1];

    if ($data == 3)
    {
        if ($db_type == 0)
        {
            echo create_drop_down("cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $serving_company,"",1);
        }
        else
        {
            echo create_drop_down("cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", $serving_company,"",1);
        }
    } 
    else if ($data == 1)
    {
        echo create_drop_down("cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "--- Select ---", $serving_company,"",1);
    
    }
    else
    {
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "", 0);
    }

    exit();
}

if ($action == "create_challan_search_list_view") {
    //echo $data;die;
    //echo $data;die;
    list($challan, $line_no, $issue_date, $company_id, $sew_company, $order_no, $cbo_source,$cutting_no) = explode("_", $data);

    $search_string = "%" . trim($data[0]) . "%";
    if ($challan != '') {
        $challan_con = " and b.challan_no ='$challan'";
    }

    //echo $company_id;die;

    if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2) $year_field = "MAX(to_char(a.insert_date,'YYYY')) as year";
    else $year_field = "";//defined Later

    if ($order_no != '') {
        $order_con = " and c.po_number like('%$order_no%')";
    } else {
        $order_con = "";
    }

    if ($db_type == 0) {
        if ($issue_date != '') $issue_date_con = "and a.delivery_date = '" . change_date_format($issue_date, "yyyy-mm-dd", "-") . "'"; else $issue_date_con = "";
    }
    if ($db_type == 2) {
        if ($issue_date != '') $issue_date_con = "and a.delivery_date = '" . change_date_format($issue_date, '', '', 1) . "'"; else $issue_date_con = "";
    }

    if ($sew_company != 0) {
        $sew_company_con = " and b.serving_company=$sew_company";
    }
    $cutting_no_cond=($cutting_no)? " and d.cut_no like '%".$cutting_no ."%'" : " ";


    $sql = "SELECT a.id, $year_field, a.sys_number_prefix_num, a.sys_number, a.delivery_date, a.production_source, a.serving_company, a.location_id, a.floor_id,a.sewing_line,c.po_number,d.cut_no from pro_gmts_delivery_mst a,pro_garments_production_mst b  ,wo_po_break_down c,pro_garments_production_dtls d where  b.po_break_down_id=c.id and a.id=b.delivery_mst_id and b.id=d.mst_id and a.production_type=9 and b.production_type=9 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.production_source=$cbo_source $order_con $challan_con $issue_date_con $sew_company_con $cutting_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 group by a.id , a.sys_number_prefix_num, a.sys_number, a.delivery_date, a.production_source, a.serving_company,a.location_id, a.floor_id,a.sewing_line,c.po_number,d.cut_no order by a.id DESC";
    // echo $sql; 

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    //echo $sql;//die;
    $result = sql_select($sql);
    $floor_arr = return_library_array("select id,floor_name from lib_prod_floor", 'id', 'floor_name');
    $location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    $supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $resource_alocate_line = return_library_array("select id, line_number from prod_resource_mst", "id", "line_number");
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
        <th width="30">SL</th>
        <th width="45">Challan</th>
        <th width="80">Cut No</th>
        <th width="40">Year</th>
        <th width="60">Input Date</th>
        <th width="60">Source</th>
        <th width="110">Sewing Company</th>
        <th width="110">Location</th>
        <th width="100">Floor</th>
        <th width="45">Line</th>
        <th>Order No</th>
        </thead>
    </table>
    <div style="width:800px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
               id="tbl_list_search">
            <?
            $i = 1;
            foreach ($result as $row) {
                if ($i % 2 == 0)
                    $bgcolor = "#E9F3FF";
                else
                    $bgcolor = "#FFFFFF";

                if ($row[csf('production_source')] == 1)
                    $serv_comp = $company_arr[$row[csf('serving_company')]];
                else
                    $serv_comp = $supllier_arr[$row[csf('serving_company')]];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
                    <td width="30"><? echo $i; ?></td>
                    <td width="45"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo $row[csf('cut_no')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="60"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="60"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td width="45"><p>
                            <?
                            if ($prod_reso_allocation == 1) {
                                $sewing_line = $resource_alocate_line[$row[csf('sewing_line')]];
                                $sewing_line_arr = explode(",", $sewing_line);
                                $sewing_line_name = "";
                                foreach ($sewing_line_arr as $line_id) {
                                    $sewing_line_name .= $line_library[$line_id] . ",";
                                }
                                $sewing_line_name = chop($sewing_line_name, ",");
                                echo $sewing_line_name;
                            } else {
                                echo $line_library[$row[csf('sewing_line')]];
                            }
                            ?></p>
                    </td>
                    <td><p><? echo $row[csf('po_number')]; ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
    <?
    exit();
}

if ($action == 'populate_data_from_challan_popup') {
    
    
    
    $data_array = sql_select("SELECT a.id, a.company_id, a.sys_number, a.embel_type, a.embel_name, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic, a.delivery_date,a.sewing_line,a.working_company_id,a.working_location_id,b.wo_order_id,b.wo_order_no from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.id=b.delivery_mst_id and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
    foreach ($data_array as $row) {

        echo "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', '" . $row[csf('location_id')] . "', 'load_drop_down_floor', 'floor_td' );\n";
       
        echo "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller',".$row[csf("serving_company")]."+'_'+" . $row[csf('location_id')] . "+'_'+" . $row[csf('floor_id')] . "+'_'+'" . change_date_format($row[csf('delivery_date')]) . "', 'load_drop_down_line', 'line_td' );\n";



        echo "document.getElementById('txt_challan_no').value               = '" . $row[csf("sys_number")] . "';\n";
        echo "document.getElementById('cbo_company_name').value             = '" . $row[csf("company_id")] . "';\n";
        echo "$('#cbo_company_name').attr('disabled','true')" . ";\n";
        echo "$('#cbo_line_no').attr('disabled','true')" . ";\n";
        echo "$('#cbo_source').val('" . $row[csf('production_source')] . "');\n";
        echo "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', '" . $row[csf('production_source')] . "', 'load_drop_down_embro_issue_source', 'emb_company_td' );\n";
        echo "load_drop_down( 'requires/bundle_wise_cutting_delevar_to_input_controller', '".$row[csf('serving_company')]."', 'load_drop_down_location', 'location_td' );\n";
        
    
        echo "$('#cbo_emb_company').val('" . $row[csf('serving_company')] . "');\n";
        echo "$('#cbo_location').val('" . $row[csf('location_id')] . "');\n";
        
        echo "$('#cbo_floor').val('" . $row[csf('floor_id')] . "');\n";
        //echo "$('#cbo_embel_name').val('".$row[csf('embel_name')]."');\n";
        //echo "$('#cbo_embel_type').val('".$row[csf('embel_type')]."');\n";
       
        echo "$('#cbo_line_no').val('" . $row[csf('sewing_line')] . "');\n";
       
        echo "$('#txt_organic').val('" . $row[csf('organic')] . "');\n";
        echo "$('#txt_system_id').val('" . $row[csf('id')] . "');\n";
        echo "$('#txt_issue_date').val('" . change_date_format($row[csf('delivery_date')]) . "');\n";
        echo "$('#cbo_working_company_name').val('".$row[csf('working_company_id')]."');\n";
        echo "$('#cbo_working_location').val('".$row[csf('working_location_id')]."');\n";
        echo "$('#txt_wo_no').val('".$row[csf('wo_order_no')]."');\n";
        echo "$('#txt_wo_id').val('".$row[csf('wo_order_id')]."');\n";
         
        
        echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_issue_print_embroidery_entry',1,1);\n";
        exit();
    }
}
 
if ($action == "emblishment_issue_print") {
    extract($_REQUEST);
    $data = explode('*', $data);
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $order_array = array();
    $order_sql = "SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0";
    $order_sql_result = sql_select($order_sql);
    foreach ($order_sql_result as $row) {
        $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
        $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
        $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
        $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
        $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
        $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
    }

    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=9 and id='$data[1]' and 
    status_active=1 and is_deleted=0 ";
    $dataArray = sql_select($sql);
    $delivery_mst_id = $dataArray[0][csf('id')];
    
    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }
    
    ?>
    <div style="width:930px;">
        <table cellspacing="0" style="font: 12px tahoma; width: 100%;">
            <tr>
                <td colspan="6" align="center" style="font-size:24px">
                    <strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                    <?

                    $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                    foreach ($nameArray as $result) {
                        ?>
                        Plot No: <? echo $result[csf('plot_no')]; ?>
                        Level No: <? echo $result[csf('level_no')] ?>
                        Road No: <? echo $result[csf('road_no')]; ?>
                        Block No: <? echo $result[csf('block_no')]; ?>
                        City No: <? echo $result[csf('city')]; ?>
                        Zip Code: <? echo $result[csf('zip_code')]; ?>
                        Province No: <?php echo $result[csf('province')]; ?>
                        Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                        Email Address: <? echo $result[csf('email')]; ?>
                        Website No: <? echo $result[csf('website')];

                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate
                            Pass</strong></u></td>
            </tr>
            
            <tr>
                <td width="95"><strong>Challan No</strong></td>
                <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                <td width="80"><strong>Source</strong></td>
                <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                <td width="120"><strong>Sew. Company</strong></td>
                <td>
                    <?
                    if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                    else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Location </strong></td>
                <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                <td><strong>Floor  </strong></td>
                <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                <td><strong>Line </strong></td>
                <td><? echo ": ".$line; ?></td>
            </tr>
            <tr>
            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
            <td><strong>Input Date  </strong></td>
             <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>WO No </strong></td><td>: <? echo return_field_value("wo_order_no", "pro_garments_production_mst", "delivery_mst_id=$delivery_mst_id and status_active=1 and is_deleted=0"); ?></td>
                
                <td><strong>Remarks</strong></td>
                <td colspan="3">:<? //echo $dataArray[0][csf('sewing_line')];
                    ?></td>
            </tr>            
			       
        <tr>
            <td  colspan="4" id="barcode_img_id"></td>
            
        </tr>

        </table>
        <?
        

        if ($data[2] == 3) {
            $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, d.color_number_id,
            count(b.id) as  num_of_bundle
            from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
            where a.delivery_mst_id ='$data[1]' 
            and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.production_type=9 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 
            group by a.po_break_down_id,a.item_number_id,a.country_id,d.color_number_id
            order by a.po_break_down_id,d.color_number_id ";
        } else {
            $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id            
            from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c
            where c.delivery_mst_id ='$data[1]' 
            and c.id=a.mst_id and a.color_size_break_down_id=b.id and b.production_type=9 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 
            group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ";
        }
        $result = sql_select($sql);
        ?>

        <div style="width:100%;">
            <table cellspacing="0" width="900" border="1" rules="all" class="rpt_table"
                   style=" margin-top:20px; font: 12px tahoma;">
                <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80" align="center">Buyer</th>
                <th width="80" align="center">Job</th>
                <th width="80" align="center">Style Ref</th>
                <th width="100" align="center">Style Des</th>
                <th width="80" align="center">Order No.</th>
                <th width="80" align="center">Gmt. Item</th>
                <th width="80" align="center">Country</th>
                <th width="80" align="center">Color</th>
                <th width="80" align="center">Gmt. Qty</th>
                <? if ($data[2] == 3) { ?>
                    <th align="center">No of Bundle</th>
                <? } ?>
                </thead>
                <tbody>
                <?

                $i = 1;
                $tot_qnty = array();
                foreach ($result as $val) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $color_count = count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                        <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                        <td align="center"><? echo $country_library[$val[csf('country_id')]]; ?></td>
                        <td align="center"><? echo $color_library[$val[csf('color_number_id')]]; ?></td>
                        <td align="right"><? echo $val[csf('production_qnty')]; ?></td>
                        <? if ($data[2] == 3) { ?>
                            <td align="center"> <? echo $val[csf('num_of_bundle')]; ?></td>
                            <?
                        }
                        $color_qty_arr[$val[csf('color_number_id')]] += $val[csf('production_qnty')];
                        $color_wise_bundle_no_arr[$val[csf('color_number_id')]] += $val[csf('num_of_bundle')];
                        ?>
                    </tr>
                    <?
                    $total_bundle += $val[csf('num_of_bundle')];
                    $production_quantity += $val[csf('production_qnty')];
                    $i++;
                }
                ?>
                </tbody>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <? if ($data[3] == 3) $colspan = 8; else $colspan = 7; ?>
                    <td colspan="9" align="right"><strong>Grand Total :</strong></td>
                    <td align="right"><? echo $production_quantity; ?></td>
                    <? if ($data[2] == 3) { ?>
                        <td align="center"> <? echo $total_bundle; ?></td>
                    <? } ?>
                </tr>
            </table>
            <br clear="all">
            <table cellspacing="0" border="1" rules="all" class="rpt_table"
                   style=" margin-top:20px; font: 12px tahoma;">
                <thead>
                <tr>
                    <td colspan="4"><strong>Color Wise Summary</strong></td>
                </tr>
                <tr bgcolor="#dddddd" align="center">
                    <td>SL</td>
                    <td>Color</td>
                    <td>No Of Bundle</td>
                    <td>Quantity (Pcs)</td>
                </tr>
                </thead>
                <tbody>
                <? $i = 1;
                foreach ($color_qty_arr as $color_id => $color_qty):
                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <td align="center"><? echo $color_wise_bundle_no_arr[$color_id]; ?></td>
                        <td align="right"><? echo $color_qty; ?></td>
                    </tr>
                    <?
                    $i++;
                endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="2" align="right">Total =</td>
                    <td align="center"><? echo $total_bundle; ?></td>
                    <td align="right"><? echo $production_quantity; ?></td>
                </tr>
                </tfoot>
            </table>
            <br>
            <?
            echo signature_table(28, $data[0], "900px");
            ?>
        </div>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 30,
                moduleSize: 5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };

            value = {code: value, rect: false};
            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
    </script>
    <?
    exit();
}

if ($action == "emblishment_issue_print_2") 
{
    extract($_REQUEST);
    $data = explode('*', $data);
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $size_library = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 ", 'id', 'buyer_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    
    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=9 and id='$data[1]' and 
    status_active=1 and is_deleted=0 ";

    $dataArray = sql_select($sql);
    $delivery_mst_id = $dataArray[0][csf('id')];

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
     {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } 
    else
     {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
     }
    unset($line_data);
    $page_id_arr=[1,2,3,4];
    $copy_name=array(1=>'st',2=>'nd',3=>'rd',4=>'th');

    $kk=0;
    foreach($page_id_arr as $vals)
    {
        $production_quantity  =0;
        $total_bundle  = 0;
        $size_qty_arr=array();
        $size_wise_bundle_no_arr=array();
        ?>
        <style type="text/css">
            @media print 
            {
                #footer_id {page-break-after: always;}
            }
        </style>


            <div style="width:930px;">
                <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
                    <tr>
                        <td colspan="5" align="center" style="font-size:24px">
                            <strong style="margin-left: 300px !important;"><? echo $company_library[$data[0]]; ?></strong>
                            
                            </td>
                            <td><span style="font-size:x-large;  "><? echo $vals;?><sup><? echo $copy_name[$vals]; ?></sup> Copy</span></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="6" align="center" style="font-size:14px">
                            <?

                            $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                            foreach ($nameArray as $result) {

                                echo $result[csf('city')];

                            }
                            unset($nameArray);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
                    </tr>
                    <tr>
                        <td width="95"><strong>Challan No</strong></td>
                        <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                        <td width="80"><strong>Source</strong></td>
                        <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                        <td width="120"><strong>Sew. Company</strong></td>
                        <td>
                            <?
                            if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                            else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Location </strong></td>
                        <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                        <td><strong>Floor  </strong></td>
                        <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                        <td><strong>Line </strong></td>
                        <td><? echo ": ".$line; ?></td>
                    </tr>
                    <tr>
                    <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
                    <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
                    <td><strong>Input Date  </strong></td>
                     <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                    </tr>
                    <tr>
                        <td><strong>WO No </strong></td><td>: <? echo return_field_value("wo_order_no", "pro_garments_production_mst", "delivery_mst_id=$delivery_mst_id and status_active=1 and is_deleted=0"); ?></td>
                        
                        <td><strong>Remarks</strong></td>
                        <td colspan="3"> : <? //echo $dataArray[0][csf('sewing_line')];
                            ?></td>
                    </tr>


                    <tr>
                        <td colspan="6" id="barcode_img_id_<? echo $kk;?>"></td>
                    </tr>

                </table>
                <br>
                <?

                

                if ($data[2] == 3) {

                    $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id, d.color_number_id,
                    count(b.id) as  num_of_bundle
                    
                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
                    where a.delivery_mst_id ='$data[1]' 
                    and a.id=b.mst_id and b.production_type=9 and b.color_size_break_down_id=d.id and  b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3)
                    and d.is_deleted=0 
                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id,d.color_number_id
                    order by  length(b.bundle_no) asc, b.bundle_no  asc";//a.po_break_down_id,d.color_number_id 
                }
                 else {
                    $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id, b.color_number_id
                    
                    from pro_garments_production_mst c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
                    where c.delivery_mst_id ='$data[1]' 
                    and c.id=a.mst_id and a.color_size_break_down_id=b.id and a.production_type=9 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 
                    group by c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id,b.color_number_id order by  length(a.bundle_no) asc, a.bundle_no asc";
                }


               // echo $sql;
                //die;

                $result = sql_select($sql);
                $all_cut_arr=array();
                $all_po_arr=array();

                foreach($result as $v)
                {
                    $all_cut_arr[$v[csf("cut_no")]]=$v[csf("cut_no")];
                    $all_po_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
                }
                $all_cut_nos="'".implode("','", $all_cut_arr)."'";
                $all_po_nos="'".implode("','", $all_po_arr)."'";
                $ppl_mst_sql=sql_select("SELECT cutting_no,batch_id from ppl_cut_lay_mst where status_active=1 and cutting_no in($all_cut_nos) ");
                $ppl_mst_arr=array();
                foreach($ppl_mst_sql as $p_val)
                {
                    $ppl_mst_arr[$p_val[csf("cutting_no")]]=$p_val[csf("batch_id")];
                }

                $order_array = array();
                $order_sql = "SELECT a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.id in($all_po_nos)";
                $order_sql_result = sql_select($order_sql);
                foreach ($order_sql_result as $row) 
                {
                    $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                    $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                    $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
                    $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
                    $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
                    $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
                }
                unset($order_sql_result);


                $sql_cut="SELECT bundle_no, number_start as number_start, number_end as number_end  from ppl_cut_lay_bundle where status_active=1 and is_deleted=0 and order_id in($all_po_nos)";
                $sql_cut_res = sql_select($sql_cut); $number_arr=array();
                foreach($sql_cut_res as $row)
                {
                    $number_arr[$row[csf('bundle_no')]]['number_start']=$row[csf('number_start')];
                    $number_arr[$row[csf('bundle_no')]]['number_end']=$row[csf('number_end')];
                }
                unset($sql_cut_res);


                ?>

                <div style="width:100%;">
                    <table cellspacing="0" width="1000" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
                        <thead bgcolor="#dddddd" align="center">
                        <th width="30">SL</th>
                        <th width="60" align="center">Bundle No</th>
                        <th width="60" align="center">Job</th>
                        <th width="80" align="center">Buyer</th>
                        <th width="80" align="center">Style Ref</th>
                        <th width="100" align="center">Style Des</th>
                        <th width="80" align="center">Order No.</th>
                        <th width="80" align="center">Gmt. Item</th>
                        <th width="80" align="center">Color</th>
                        <th width="100" align="center">Batch</th>
                        <th width="80" align="center">Size</th>
                        <th width="60" align="center">Reject Qty</th>
                        <th width="60" align="center">RMG Qty</th>
                        <th width="60" align="center">Gmt. Qty</th>
                        </thead>
                        <tbody>
                        <?
                        $i = 1;
                        $size_wise_bundle_no = 0;
                        $num_of_bundle = 0;
                        $tot_qnty = array();
                        foreach ($result as $val) 
                        {
                            

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            $color_count = count($cid);
                            $number_start=''; $number_end='';
                            $number_start=$number_arr[$val[csf('bundle_no')]]['number_start'];
                            $number_end=$number_arr[$val[csf('bundle_no')]]['number_end'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td><? echo $i; ?></td>
                                <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                                <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                                <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                                <td align="center"><? echo $color_library[$val[csf('color_number_id')]]; ?></td>
                                <td align="center"><? echo  $ppl_mst_arr[$val[csf("cut_no")]]; ?></td>
                                <td align="center"><? echo $size_library[$val[csf('size_number_id')]]; ?></td>
                                <td></td>
                                <td align="center"><? echo $number_start.'-'.$number_end; ?></td>
                                <td align="right"><? echo $val[csf('production_qnty')]; ?></td>

                            </tr>
                            <?
                            $production_quantity += $val[csf('production_qnty')];
                            $total_bundle += $val[csf('num_of_bundle')];
                            $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
                            $size_wise_bundle_no_arr[$val[csf('size_number_id')]] += $val[csf('num_of_bundle')];
                            $i++;
                        }
                        ?>
                        </tbody>
                        <tr>
                            <td colspan="10"></td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>No. Of Bundle :<? echo $total_bundle; ?></strong></td>
                            <td colspan="10" align="right"><strong>Grand Total </strong></td>
                            <td align="right"><? echo $production_quantity; ?></td>
                        </tr>
                    </table>

                    <br>
                    <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
                        <thead>
                        <tr>
                            <td colspan="4"><strong>Size Wise Summary</strong></td>
                        </tr>
                        <tr bgcolor="#dddddd" align="center">
                            <td>SL</td>
                            <td>Size</td>
                            <td>No Of Bundle</td>
                            <td>Quantity (Pcs)</td>
                        </tr>
                        </thead>
                        <tbody>
                        <? $i = 1;
                        foreach ($size_qty_arr as $size_id => $size_qty):
                            $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $i; ?></td>
                                <td align="center"><? echo $size_library[$size_id]; ?></td>
                                <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                                <td align="right"><? echo $size_qty; ?></td>
                            </tr>
                            <?
                            $i++;
                        endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right"><strong>Total </strong></td>
                            <td align="center"><? echo $total_bundle; ?></td>
                            <td align="right"><? echo $production_quantity; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                    <br>
                    <?
                    echo signature_table(28, $data[0], "900px");
                    ?>
                </div>
            </div>
            <div id="footer_id"></div>

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess) {
                var ids='<? echo $kk;?>';
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };

                value = {code: value, rect: false};
                $("#barcode_img_id_"+ids).show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
        </script>
    <?
        $kk++;
    }
    exit();
}

if($action=="emblishment_issue_print_3") 
{
    extract($_REQUEST);
    $data=explode('*',$data);
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
    $order_array=array();
    //$order_sql="select a.job_no, a.buyer_name,a.style_ref_no, b.id, b.po_number, b.po_quantity,c.cutting_no  from  wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d where  c.id=d.mst_id and d.order_id=b.id and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";//c.entry_form=77 and
    $order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_name=$data[0]";//c.entry_form=77 and 
    
    $order_sql_result=sql_select($order_sql);
    foreach ($order_sql_result as $row)
    {
        $order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
        $order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
        $order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
        $order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
        $order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
        $order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
        $order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
    }
    
    $sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=9 and id='$data[1]' and 
    status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);
    
    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px"> 
	                <?
	                
	                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
	                    
	                
	                    
	                    foreach ($nameArray as $result)
	                    { 
	                    
	                         echo $result[csf('city')];
	                        
	                    }
	                ?> 
	            </td>  
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>
	        
	            <tr>
	                <td width="95"><strong>Challan No</strong></td>
	                <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
	                <td width="80"><strong>Source</strong></td>
	                <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
	                <td width="120"><strong>Sew. Company</strong></td>
	                <td>
	                    <?
	                    if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
	                    else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

	                    ?>
	                </td>
	            </tr>
	            <tr>
	                <td><strong>Location</strong></td>
	                <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
	                <td><strong>Floor </strong></td>
	                <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	                <td><strong>Line </strong></td>
	                <td><? echo ": ".$line; ?></td>
	            </tr>
	            <tr>
	            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
	            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
	            <td><strong>Input Date </strong></td>
	             <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            </tr>
	            <tr>
	                
	                <td>Remarks</td>
	                <td colspan="3"><? //echo $dataArray[0][csf('sewing_line')];
	                    ?></td>
	            </tr>
	        
	        
	<!--        <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
	            <td width="105"><strong>Emb. Type</strong></td><td width="155px">: 
	            <? 
	                if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
	                elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
	                elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; 
	                elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
	             ?>
	            </td>
	        </tr>
	        <tr>
	            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
	            <td><strong>Emb. Company</strong></td><td>: 
	                <? 
	                    if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]];
	                 
	                ?>
	            </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	        </tr>
	        <tr>
	         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
	            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
	            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
	        </tr>
	-->        <tr>
	            <td  colspan="4" id="barcode_img_id"></td>
	            
	        </tr>
	       
	    </table><br />
	        <?
	if($db_type==2) $group_concat="  listagg(cast(b.cut_no AS VARCHAR2(4000)),',') within group (order by b.cut_no) as cut_no" ;
	    else if($db_type==0) $group_concat=" group_concat(b.cut_no) as cut_no" ;
	        
	            $delivery_mst_id =$dataArray[0][csf('id')];
	            // base on Embel. Name
	            if($data[2]==3)
	            {
	                if($db_type==0)
	                {
	                    $sql="SELECT  sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
	                    count(b.id) as  num_of_bundle,
	                    (select sum(c.number_start) from ppl_cut_lay_bundle c where  b.bundle_no = c.bundle_no) number_start,
	                    (select sum(e.number_end) from ppl_cut_lay_bundle e where  b.bundle_no = e.bundle_no) number_end
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d 
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3)
	                    and d.is_deleted=0 and b.bundle_no <> ''
	                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id
	                    order by b.bundle_no ";
	                }
	                else
	                {
	                    /*$sql="SELECT $group_concat,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
	                    count(b.id) as  num_of_bundle, sum(C.NUMBER_START) number_start, sum(C.NUMBER_END) number_end 
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d, ppl_cut_lay_bundle  c  
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.bundle_no = c.bundle_no and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
	                    and d.is_deleted=0  and b.bundle_no is not null
	                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id
	                    order by a.po_break_down_id,d.color_number_id ";*/

	                 $sql="SELECT $group_concat,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
	                    count(b.id) as  num_of_bundle
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d 
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id  and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3)
	                    and d.is_deleted=0  and b.bundle_no is not null
	                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id
	                    order by  d.size_number_id ";

	                }
	            }
	            else
	            {
	                if($db_type==0)
	                {
	                    $sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id,b.size_number_id 
	                    from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' 
	                    and c.id=a.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.bundle_no!=''
	                    group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id ";             
	                }
	                else
	                {                   
	                    $sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ,b.size_number_id
	                    from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id  
	                    and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.bundle_no is not null
	                    group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id ";
	                }
	                
	            }
	            
	            //echo $sql;
	            $result=sql_select($sql);
	            $rows=array();
	            $po_cutlay_id_arr=array();
	            $po_wise_ordcutno_arr=array();
	           
	            foreach($result as $value)
	            {
	                $po_cutlay_id_arr[$value[csf('po_break_down_id')]]= $value[csf('po_break_down_id')];
	            }
	            $order_id_cut=implode(",", $po_cutlay_id_arr);
	            if(!$order_id_cut)$order_id_cut=0;

	            $sql_order_cut="SELECT c.cutting_no, a.order_cut_no ,b.order_id from ppl_cut_lay_mst c, ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where c.id=b.mst_id  and c.status_active=1 and c.is_deleted=0 and  a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in($order_id_cut) group by c.cutting_no, a.order_cut_no ,b.order_id " ;
	            foreach(sql_select($sql_order_cut) as $vals)
	            {
	                $po_ids=explode(",", $vals[csf("order_id")]);
	                foreach($po_ids as $val)
	                {
	                    $po_wise_ordcutno_arr[$vals[csf("cutting_no")]][$val]=$vals[csf("order_cut_no")];
	                }
	                
	            }
	            //print_r($po_wise_ordcutno_arr);die;

	            foreach($result as $val)
	            {
	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['val']+=$val[csf('production_qnty')];

	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['order_no']=$val[csf('po_break_down_id')];

	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['count']++;
	                
	                
	                $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
	                $size_wise_bundle_no_arr[$val[csf('size_number_id')]] ++;
	            }
	           
	           // print_r($po_wise_ordcutno_arr);
	            //die;
	            unset($result); 
	        ?> 
	         
	    
	    <div style="width:100%;">
	    <table cellspacing="0" width="900" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="80" align="center">Buyer</th>
	            <th width="80" align="center">Job</th>
	            <th width="80" align="center">Style Ref</th>
	            <th width="100" align="center">Style Des</th>
	            <th width="80" align="center">Order No.</th>
	            <th width="120" align="center">Order Cut No</th> 
	            <th width="80" align="center">Cutting Number</th>
	            <th width="80" align="center">Gmt. Item</th>
	            <th width="80" align="center">Country</th>
	            <th width="80" align="center">Color</th>
	            <th width="80" align="center">Gmt. Qty</th>
	            <? if($data[2]==3)  {  ?>
	            <th align="center">No of Bundle</th>
	            <? }   ?>
	        </thead>
	        <tbody>
	            <?
	          //  $size_qty_arr=array();
	            $i=1;
	            $tot_qnty=array();
	             foreach($rows as $buyer=>$brows)
	             {
	                 foreach($brows as $job=>$jrows)
	                 {
	                     foreach($jrows as $styleref=>$srrows)
	                     {
	                         foreach($srrows as $styledes=>$sdrows)
	                         {
	                             foreach($sdrows as $order=>$orows)
	                             {
	                                 foreach($orows as $cutn=>$ctrows)
	                                 {
	                                    foreach($ctrows as $gmtitm=>$girows)
	                                     {
	                                        foreach($girows as $Country=>$cntrows)
	                                         {
	                                             foreach($cntrows as $color=>$cdata)
	                                             {
	                                                if ($i%2==0)  
	                                                    $bgcolor="#E9F3FF";
	                                                else
	                                                    $bgcolor="#FFFFFF";
	                                                $color_count=count($cid);
	                                                ?>
	                                                <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                                                    <td><? echo $i;  ?></td>
	                                                    <td align="center"><? echo $buyer; ?></td>
	                                                    <td align="center"><? echo $job; ?></td>
	                                                    <td align="center"><? echo $styleref; ?></td>
	                                                    <td align="center"><? echo $styledes; ?></td>
	                                                    <td align="center"><? echo $order; ?></td>
	                                                    <td align="center"><? echo $po_wise_ordcutno_arr[$cutn][$cdata['order_no']]; ?></td>
	                                                    <td align="center"><? echo $cutn; ?></td>
	                                                    <td align="center"><? echo $gmtitm; ?></td>
	                                                    <td align="center"><? echo $Country; ?></td>
	                                                    <td align="center"><? echo $color;?></td>
	                                                    <td align="right"><?  echo $cdata['val']; ?></td>
	                                                    <? if($data[2]==3) 
	                                                     {  ?>
	                                                    <td  align="center"> <?  echo $cdata['count']; ?></td>
	                                                    <? 
	                                                    $color_qty_arr[$color] += $cdata['val'];
	                                                    $color_wise_bundle_no_arr[$color] += $cdata['count'];
	                                                    }   
	                                                    ?>
	                                                    
	                                                </tr>
	                                                <?
	                                                $production_quantity += $cdata['val'];
	                                                $total_bundle += $cdata['count'];
	                                                $i++;
	                                             } 
	                                         }  
	                                     } 
	                                 } 
	                             } 
	                         }
	                     }
	                 }
	             }
	                    
	                ?>
	        </tbody>
	        <tr>
	            <td colspan="11" align="right"><strong>Grand Total </strong></td>
	            <td align="right"><?  echo $production_quantity; ?></td>
	            <td align="center"><?  echo $total_bundle; ?></td>
	        </tr>                           
	    </table>


	 <br>
	            <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
	                <thead>
	                <tr>
	                    <td colspan="4"><strong>Size Wise Summary</strong></td>
	                </tr>
	                <tr bgcolor="#dddddd" align="center">
	                    <td>SL</td>
	                    <td>Size</td>
	                    <td>No Of Bundle</td>
	                    <td>Quantity (Pcs)</td>
	                </tr>
	                </thead>
	                <tbody>
	                <? $i = 1;
	                foreach ($size_qty_arr as $size_id => $size_qty):
	                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td align="center"><? echo $i; ?></td>
	                        <td align="center"><? echo $size_library[$size_id]; ?></td>
	                        <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
	                        <td align="right"><? echo $size_qty; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                endforeach; ?>
	                </tbody>
	                <tfoot>
	                <tr>
	                    <td colspan="4"></td>
	                </tr>
	                <tr>
	                    <td colspan="2" align="right"><strong>Total </strong></td>
	                    <td align="center"><? echo $total_bundle; ?></td>
	                    <td align="right"><? echo $production_quantity; ?></td>
	                </tr>
	                </tfoot>
	            </table>
	            <br>
	         <?
	            echo signature_table(28, $data[0], "500px");
	         ?>
	    </div>
	    </div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess ){
	            var value = valuess;//$("#barcodeValue").val();
	            var btype = 'code39';//$("input[name=btype]:checked").val();
	            var renderer ='bmp';// $("input[name=renderer]:checked").val();
	            var settings = {
	              output:renderer,
	              bgColor: '#FFFFFF',
	              color: '#000000',
	              barWidth: 1,
	              barHeight: 30,
	              moduleSize:5,
	              posX: 10,
	              posY: 20,
	              addQuietZone: 1
	            };
	        
	             value = {code:value, rect: false};
	            $("#barcode_img_id").show().barcode(value, btype, settings);
	        } 
	        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	     </script>
	<?
	exit(); 
}

if($action=="sewing_input_challan_print") 
{
    extract($_REQUEST);
    $data=explode('*',$data);
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
    
    
    
    $sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type, production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id,remarks from pro_gmts_delivery_mst where production_type=9 and id='$data[1]' and 
    status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);
    
    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px"> 
	                <?
	                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
	                
	                    
	                    foreach ($nameArray as $result)
	                    { 
	                    
	                         echo $result[csf('city')];
	                        
	                    }
	                ?> 
	            </td>  
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong> Bundle Wise Cutting Delivery To Input Challan </strong></u></td>
	        </tr>
	     <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Input Date</strong></td><td width="175px"> : <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            <td width="105"><strong>Barcode</strong></td><td  colspan="4" id="barcode_img_id"></td>
	        </tr>
	        <tr>
	            
	            <td><strong>Sewing Source</strong></td><td>: 
	                <? 
	                    echo $knitting_source[$dataArray[0][csf('production_source')]]; 
	                    
	                 
	                ?>
	            </td>
	            <td><strong>Sewing Company</strong></td><td>: <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?> </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Line </strong></td><td><? echo ": ".$line; ?></td>
	            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	        </tr>
	        <tr>
	         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
	           
	        </tr>
	       <tr>
	            
	            <td><strong>Remarks  </strong></td><td>: <? echo $dataArray[0][csf('remarks')]; ?></td>
	        </tr>
	       
	    </table>
	         <br>
	        <?
	                
	        $delivery_mst_id =$dataArray[0][csf('id')];        
	       //$cut_no_all=sql_select("select listagg(cast(a.cut_no as varchar2(4000)),',') within group (order by a.cut_no) as cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' ");
	        $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='".$delivery_mst_id."' and production_type=9 group by cut_no");
	        $cut_number_string="";
	        foreach($cut_nos_all as $cut_val)
	        {
	            if($cut_number_string=="")
	            {
	                $val=$cut_val[csf('cut_no')];
	                 $cut_number_string.="'$val'";
	            }
	            else
	            {
	                $val=$cut_val[csf('cut_no')];
	                $cut_number_string.=','."'$val'";
	            }
	        }
	        //echo $cut_number_string;


		   /* $sqls="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,h.order_cut_no,b.bundle_no
			from 
				pro_garments_production_mst a, 
				pro_garments_production_dtls b,
				wo_po_color_size_breakdown d, 
				wo_po_details_master e, 
				wo_po_break_down f,
				ppl_cut_lay_mst g,
				ppl_cut_lay_dtls h,
				ppl_cut_lay_bundle i
			where 
				a.delivery_mst_id ='$data[1]' 

				and e.job_no=f.job_no_mst 
				and f.id=a.po_break_down_id 
				and a.id=b.mst_id 
				and b.color_size_break_down_id=d.id 
				and d.status_active=1 
				and d.is_deleted=0 
				and g.id=h.mst_id
				and i.mst_id=g.id
				and i.dtls_id=h.id
				and i.bundle_no=b.bundle_no
				and g.cutting_no=b.cut_no
				and h.color_id=d.color_number_id 
				and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0
				and a.status_active=1 and a.is_deleted=0
				and b.status_active=1 
				and b.is_deleted=0 
				and f.status_active=1 
				and f.is_deleted=0 
				 and d.status_active=1 
				and d.is_deleted=0 
				and e.status_active=1 
				and e.is_deleted=0 
				and i.status_active=1 
				and i.is_deleted=0 
			order by e.job_no,d.size_order";*/
			$sqls="SELECT a.item_number_id, b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where 
				a.delivery_mst_id ='$data[1]' and a.production_type=9 and b.production_type=9  and e.id=f.job_id  and f.id=a.po_break_down_id 
				and a.id=b.mst_id  and b.color_size_break_down_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
				and b.is_deleted=0  and f.status_active in(1,2,3)  and f.is_deleted=0 and d.status_active in(1,2,3) 
				and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";
			
		//echo $sqls;
		$result=sql_select($sqls);
		//unset($sqls);
		/*$cut_nos=$cut_no_all[0][csf("cut_no")];
		$cut_nos=explode(",", $cut_nos);
		$cut_no_string="";
		foreach($cut_nos as $val)
		{
			if($cut_no_string=="")
			{
				$cut_no_string.="'$val'";
			}
			else
			{
				$cut_no_string.=','."'$val'";
			}
		}*/

		$order_cut_no_sql=sql_select("select b.order_id as po, b.order_cut_no as ord_cut, a.batch_id, a.cutting_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in (".$cut_number_string.")");
		$batchArr = array();
		$order_cut_no_arr = array();
		foreach($order_cut_no_sql as $rows)
		{
			 if(strpos($rows[csf("po")], ",")==false)
			 { 
				 $order_cut_no_arr[$rows[csf("cutting_no")]][$rows[csf("po")]]=$rows[csf("ord_cut")];
			 }
			 else
			 {
				$po_ids=$rows[csf("po")];
				$po_ids=explode(",", $po_ids);
				foreach($po_ids as $po_val)
				{
					$order_cut_no_arr[$rows[csf("cutting_no")]][$po_val]=$rows[csf("ord_cut")];
				}
			 }
			 
			 $batchArr[$rows[csf('cutting_no')]] = $rows[csf('batch_id')];
		}
		//echo "<pre>";
		//print_r($batchArr);

		 /*$batch_sql="select a.roll_data, b.bundle_no, b.roll_no from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where c.id=a.mst_id and c.id= b.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and c.cutting_no in (".$cut_number_string." ) group by  a.roll_data, b.bundle_no,b.roll_no";
		//echo $batch_sql;die;
		$batcharr_sql=sql_select($batch_sql);
		$batch_array=array();
		foreach($batcharr_sql as $row )
			{ 
				$roll_data=explode("**",$row[csf("roll_data")]); 
				foreach ($roll_data as $roll_data_value)
				{
					$roll_data_single_row=explode("=",$roll_data_value);
					if ($roll_data_single_row[1]==$row[csf("roll_no")]) {
					$batch_array[$row[csf("bundle_no")]] .=",".$roll_data_single_row[5];  
				}
			}   
		}
		echo "<pre>";
		print_r($batch_array);*/

		foreach($result as $rows)
		{
			
			$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];
			
			$bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
			$dataArr[$key]=array(
				country_id=>$rows[csf('country_id')],
				buyer_name=>$rows[csf('buyer_name')],
				po_id=>$rows[csf('po_id')],
				po_number=>$rows[csf('po_number')],
				item_number_id=>$rows[csf('item_number_id')],
				color_number_id=>$rows[csf('color_number_id')],
				size_number_id=>$rows[csf('size_number_id')],
				style_ref_no=>$rows[csf('style_ref_no')],
				style_description=>$rows[csf('style_description')],
				job_no=>$rows[csf('job_no')],
				cut_no=>$rows[csf('cut_no')],
				order_cut_no=>$rows[csf('order_cut_no')]
			);
			//$batch_wise_qty_array[$batch_array[$rows[csf("bundle_no")]]]+=$rows[csf('production_qnty')];
			$batch_wise_qty_array[$batchArr[$rows[csf("cut_no")]]]+=$rows[csf('production_qnty')];
			$productionQtyArr[$key]+=$rows[csf('production_qnty')];
			$sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
			$bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
		}  
		
		$table_width=900+(count($bundle_size_arr)*50);
		//echo "<pre>";
		//print_r($dataArr);				
		?> 
	    <div style="width:100%;">
	    <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th width="30" rowspan="2">SL</th>
	                <th width="80" align="center" rowspan="2">Buyer</th>
	                <th width="80" align="center" rowspan="2">Job No</th>
	                <th width="80" align="center" rowspan="2">Style Ref</th>
	                <th width="80" align="center" rowspan="2">PO Number</th>
	                <th width="80" align="center" rowspan="2">Garments Item</th>
	                <th width="100" align="center" rowspan="2">Style Des</th>
	                <th width="80" align="center" rowspan="2">Country</th>
	                <th width="80" align="center" rowspan="2">Color</th>
	                <th width="80" align="center" rowspan="2">Cutting No</th>
	                <th width="80" align="center" rowspan="2">Order Cut</th>
	                <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
	                <th width="80" align="center" rowspan="2">Total Issue Qty</th>
	                <th width="80" align="center" rowspan="2">No of Bundle</th>
	                <th width= align="center" rowspan="2">Remarks</th>
	              </tr>
	              <tr>
	                <?
	                $i=0;
	                foreach($bundle_size_arr as $inf)
	                {
	                ?>
	                <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
	                <?  
	                }
	                ?>
	             </tr>
	        </thead>
	        <tbody>
	            <?
	            $i=1;
	            $tot_qnty=array();
	            foreach($dataArr as $key=>$row)
	            {
	                
	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                        <td><? echo $i;  ?></td>
	                        <td align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
	                        <td align="center"><? echo $row['job_no']; ?></td>
	                        <td align="center"><? echo $row['style_ref_no']; ?></td>
	                        <td align="center"><? echo $row['po_number'];?></td>
	                        <td align="center"><? echo $garments_item[$row['item_number_id']];?></td>
	                        <td align="center"><? echo $row['style_description']; ?></td>
	                        <td align="center"><? echo $country_library[$row['country_id']]; ?></td>
	                        <td align="center"><? echo $color_library[$row['color_number_id']]; ?></td>
	                        <td align="center"><? echo $row['cut_no']; ?></td>
	                        <td align="center"><? echo $order_cut_no_arr[$row['cut_no']][$row['po_id']]; ?></td>
	                        <?
	                        foreach($bundle_size_arr as $size_id)
	                        {
	                            $size_qty=0;
	                            $size_qty=$sizeQtyArr[$key][$size_id];
	                            ?>
	                            <td align="center" width="50"><? echo $size_qty; ?></td>
	                            <?  
	                            $grand_total_size_arr[$size_id]+=$size_qty;
	                        }
	                        ?>
	                        <td align="center"><? echo $productionQtyArr[$key]; ?></td>
	                        <td align="center"><? echo count($bundleArr[$key]); ?></td>
	                        <td align="center"> <?  //echo $val[csf('num_of_bundle')]; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $grand_total_qty+=$productionQtyArr[$key];
	                    $grand_total_bundle_num+=count($bundleArr[$key]);
	                    $grand_total_reject_qty+=$val['reject_qty'];
	           }
	           ?>
	        </tbody>
	        <tr bgcolor="#DDDDDD">
	      
	            <td colspan="11" align="right"><strong>Grand Total :</strong></td>
	            <?
	            foreach($bundle_size_arr as $size_id)
	            {
	                ?>
	                <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
	                <?  
	            }
	            ?>
	             <td align="center"><? echo $grand_total_qty; ?></td>
	            <td align="center"><? echo $grand_total_bundle_num; ?></td>
	            <td  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
	        </tr>                           
	    </table>
	 </div> &nbsp; <br>
	 
	 <table align="left" cellspacing="0"  border="1" rules="all" class="rpt_table" style=" margin-top:50px; width: 400px; margin-left: 50px;"  >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th width="200">Lot/Batch Number</th>
	                <th width="100">Qty</th>
	                <th width="100">UOM</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?

	            $j=1;
	           /* echo "<pre>";
	            print_r($batch_wise_qty_array);
	            echo "</pre>";*/
	            foreach($batch_wise_qty_array as $batch=>$batch_qty)
	            {
	                $bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
	                $batch=array_unique(explode(",",ltrim($batch,',')));
	                
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                        <td width="200" align="center"><? echo implode(",",$batch); ?></td>
	                        <td width="100" align="center"><? echo $batch_qty; ?></td>
	                        <td width="100" align="center">Pcs</td>
	                    </tr>
	                <?
	            $j++;
	            $total_batch_qty+=$batch_qty;
	            }
	            ?>
	        </tbody>
	            <tr bgcolor="#DDDDDD">
	                <td width="200" align="right"><strong>Total:</strong></td>
	                <td width="100" align="center"><? echo $total_batch_qty; ?></td>
	                <td width="100" align="center">Pcs</td>
	            </tr>
	    </table>

	</div>  
	     <? 
	        echo signature_table(26, $data[0], "900px");
	     ?>
	     <br>
	</div>

	  
	        
	        <br>
	         <?
	           // echo signature_table(26, $data[0], "900px");
	         ?>
	    </div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess ){
	            var value = valuess;//$("#barcodeValue").val();
	            var btype = 'code39';//$("input[name=btype]:checked").val();
	            var renderer ='bmp';// $("input[name=renderer]:checked").val();
	            var settings = {
	              output:renderer,
	              bgColor: '#FFFFFF',
	              color: '#000000',
	              barWidth: 1,
	              barHeight: 30,
	              moduleSize:5,
	              posX: 10,
	              posY: 20,
	              addQuietZone: 1
	            };
	        
	             value = {code:value, rect: false};
	            $("#barcode_img_id").show().barcode(value, btype, settings);
	        } 
	        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	     </script>
	<?
	exit(); 

}


if($action=="sewing_input_challan_print_5") 
{
    
    extract($_REQUEST);
    $data=explode('*',$data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
    
    
    
    $sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=9 and id='$data[1]' and 
    status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);
    
    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px"> 
	                <?
	                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
	                
	                    
	                    foreach ($nameArray as $result)
	                    { 
	                    
	                         echo $result[csf('city')];
	                        
	                    }
	                ?> 
	            </td>  
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong> Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>
	     <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Input Date</strong></td><td width="175px"> : <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            <td width="105"><strong>Barcode</strong></td><td  colspan="4" id="barcode_img_id"></td>
	        </tr>
	        <tr>
	            
	            <td><strong>Sewing Source</strong></td><td>: 
	                <? 
	                    echo $knitting_source[$dataArray[0][csf('production_source')]]; 
	                ?>
	            </td>
	            <td><strong>Sewing Company</strong></td><td>: <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?> </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Line </strong></td><td><? echo ": ".$line; ?></td>
	            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	        </tr>
	        <tr>
	         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
	           
	        </tr>
	       <tr>
	        </tr>
	    </table>
	         <br>
	        <?
	                
	        $delivery_mst_id =$dataArray[0][csf('id')];        
	        $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=9 group by cut_no");
	        $cut_number_string="";
	        foreach($cut_nos_all as $cut_val)
	        {
	            if($cut_number_string=="")
	            {
	                $val=$cut_val[csf('cut_no')];
	                 $cut_number_string.="'$val'";
	            }
	            else
	            {
	                $val=$cut_val[csf('cut_no')];
	                $cut_number_string.=','."'$val'";
	            }
	        }
	         
	                $sqls="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where 
	                    a.delivery_mst_id ='$data[1]' and a.production_type=9 and b.production_type=9  and e.id=f.job_id  and f.id=a.po_break_down_id 
	                    and a.id=b.mst_id  and b.color_size_break_down_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
	                    and b.is_deleted=0  and f.status_active in(1,2,3)  and f.is_deleted=0 and d.status_active in(1,2,3) 
	                    and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";
	                
	               $result=sql_select($sqls);
	              

	            $order_cut_no_sql=sql_select("select a.batch_id,b.order_ids as po,b.order_cut_no as ord_cut,cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in ($cut_number_string)");
	            foreach($order_cut_no_sql as $rows)
	            {
	                 if(strpos($rows[csf("po")], ",")==false)
	                 { 
	                     $order_cut_no_arr[$rows[csf("cutting_no")]][$rows[csf("po")]]=$rows[csf("ord_cut")];
	                 }
	                 else
	                 {
	                    $po_ids=$rows[csf("po")];
	                    $po_ids=explode(",", $po_ids);
	                    foreach($po_ids as $po_val)
	                    {
	                        $order_cut_no_arr[$rows[csf("cutting_no")]][$po_val]=$rows[csf("ord_cut")];
	                    }
	                 }
					 
	                     $batch_id_arr[$rows[csf("cutting_no")]]=$rows[csf("batch_id")];
					 
					 
	               
	            }

	             $batch_sql="select a.roll_data, b.bundle_no,b.roll_no from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b,ppl_cut_lay_mst c where c.id=a.mst_id and c.id= b.mst_id and a.id=b.dtls_id   and a.status_active=1 and a.is_deleted=0 and c.cutting_no in ($cut_number_string ) group by  a.roll_data, b.bundle_no,b.roll_no";


	                 //echo $batch_sql;die;
	                     $batcharr_sql=sql_select($batch_sql);
	                    $batch_array=array();
	                    foreach($batcharr_sql as $row )
	                    { 
	                        $roll_data=explode("**",$row[csf("roll_data")]); 
	                        foreach ($roll_data as $roll_data_value)
	                        {
	                            $roll_data_single_row=explode("=",$roll_data_value);
	                            if ($roll_data_single_row[1]==$row[csf("roll_no")]) {
	                              $batch_array[$row[csf("bundle_no")]] .=",".$roll_data_single_row[5];  
	                          }
	                        }   
	                           
	                           
	                    }
	                 
	                
	                foreach($result as $rows)
	                {
	                    
	                    $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];
	                    
	                    $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
	                    $dataArr[$key]=array(
	                        country_id=>$rows[csf('country_id')],
	                        buyer_name=>$rows[csf('buyer_name')],
	                        po_id=>$rows[csf('po_id')],
	                        po_number=>$rows[csf('po_number')],
	                        color_number_id=>$rows[csf('color_number_id')],
	                        size_number_id=>$rows[csf('size_number_id')],
	                        style_ref_no=>$rows[csf('style_ref_no')],
	                        style_description=>$rows[csf('style_description')],
	                        job_no=>$rows[csf('job_no')],
	                        cut_no=>$rows[csf('cut_no')],
	                        order_cut_no=>$rows[csf('order_cut_no')]
							
	                    );
	                    $batch_wise_qty_array[$batch_array[$rows[csf("bundle_no")]]]+=$rows[csf('production_qnty')];
	                    $productionQtyArr[$key]+=$rows[csf('production_qnty')];
	                    $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
	                    $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
	                    
	                }  
	                 
	            
	            $table_width=900+(count($bundle_size_arr)*50);
	        ?> 
	    <div style="width:100%;">
	    <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th width="30" rowspan="2">SL</th>
	                <th width="80" align="center" rowspan="2">Buyer</th>
	                <th width="80" align="center" rowspan="2">Job No</th>
	                <th width="80" align="center" rowspan="2">Style Ref</th>
	                <th width="80" align="center" rowspan="2">PO Number</th>
	                <th width="80" align="center" rowspan="2">Country</th>
	                <th width="80" align="center" rowspan="2">Cutting No</th>
	                <th width="80" align="center" rowspan="2">Order Cut</th>
	                <th width="100" align="center" rowspan="2">Batch No</th>
	                <th width="80" align="center" rowspan="2">Color</th>
	                <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
	                <th width="80" align="center" rowspan="2">Total Issue Qty</th>
	                <th width="80" align="center" rowspan="2">No of Bundle</th>
	                <th width= align="center" rowspan="2">Remarks</th>
	              </tr>
	              <tr>
	                <?
	                $i=0;
	                foreach($bundle_size_arr as $inf)
	                {
	                ?>
	                <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
	                <?  
	                }
	                ?>
	             </tr>
	        </thead>
	        <tbody>
	            <?
	            $i=1;
	            $tot_qnty=array();
	            foreach($dataArr as $key=>$row)
	            {
	                
	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
	                        <td><? echo $i; ?></td>
	                        <td align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
	                        <td align="center"><? echo $row['job_no']; ?></td>
	                        <td align="center"><? echo $row['style_ref_no']; ?></td>
	                        <td align="center"><? echo $row['po_number'];?></td>
	                        <td align="center"><? echo $country_library[$row['country_id']]; ?></td>
	                        <td align="center"><? echo $row['cut_no']; ?></td>
	                        <td align="center"><? echo $order_cut_no_arr[$row['cut_no']][$row['po_id']]; ?></td>
	                        <td align="center"><? echo $batch_id_arr[$row['cut_no']]; ?></td>
	                        <td align="center"><? echo $color_library[$row['color_number_id']]; ?></td>
	                        <?
	                        foreach($bundle_size_arr as $size_id)
	                        {
	                            $size_qty=0;
	                            $size_qty=$sizeQtyArr[$key][$size_id];
	                            ?>
	                            <td align="center" width="50"><? echo $size_qty; ?></td>
	                            <?  
	                            $grand_total_size_arr[$size_id]+=$size_qty;
	                        }
	                        ?>
	                        <td align="center"><? echo $productionQtyArr[$key]; ?></td>
	                        <td align="center"><? echo count($bundleArr[$key]); ?></td>
	                        <td align="center"></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $grand_total_qty+=$productionQtyArr[$key];
	                    $grand_total_bundle_num+=count($bundleArr[$key]);
	                    $grand_total_reject_qty+=$val['reject_qty'];
	                
	           }
	           ?>
	        </tbody>
	        <tr bgcolor="#DDDDDD">
	      
	            <td colspan="10" align="right"><strong>Grand Total :</strong></td>
	            <?
	            foreach($bundle_size_arr as $size_id)
	            {
	                ?>
	                <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
	                <?  
	            }
	            ?>
	             <td align="center"><? echo $grand_total_qty; ?></td>
	            <td align="center"><? echo $grand_total_bundle_num; ?></td>
	            <td  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
	        </tr>                           
	    </table>
	 </div> &nbsp; <br>
	 
	 

	</div>  
	     <?
	        echo signature_table(26, $data[0], "900px");
	     ?>
	     <br>
	</div>

	    </div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess ){
	            var value = valuess;//$("#barcodeValue").val();
	            var btype = 'code39';//$("input[name=btype]:checked").val();
	            var renderer ='bmp';// $("input[name=renderer]:checked").val();
	            var settings = {
	              output:renderer,
	              bgColor: '#FFFFFF',
	              color: '#000000',
	              barWidth: 1,
	              barHeight: 30,
	              moduleSize:5,
	              posX: 10,
	              posY: 20,
	              addQuietZone: 1
	            };
	        
	             value = {code:value, rect: false};
	            $("#barcode_img_id").show().barcode(value, btype, settings);
	        } 
	        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	     </script>
	<?
	exit(); 

}



if($action=="sewing_input_challan_print_8") 
{
    
    extract($_REQUEST);
    $data=explode('*',$data);
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
    
    
    
    $sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id 
    from pro_gmts_delivery_mst 
    where production_type=9 and id='$data[1]' and status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);
    
    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) 
    {
        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) 
        {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } 
    else 
    {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

    // =============================== getting cut no ====================================   
	                
    $delivery_mst_id =$dataArray[0][csf('id')];  
    $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=9 group by cut_no");
    $cut_number_string="";
    foreach($cut_nos_all as $cut_val)
    {        
        if($cut_number_string=="")
        {
            $val=$cut_val[csf('cut_no')];
             $cut_number_string.="'$val'";
        }
        else
        {
            $val=$cut_val[csf('cut_no')];
            $cut_number_string.=','."'$val'";
        }
    }
     // echo $cut_number_string;
    // ==================================== order cut no ==================================
        
    $order_cut_no_sql=sql_select("SELECT b.order_ids as po,b.order_cut_no as ord_cut,cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in($cut_number_string)");
    $ordcut_number_string="";
    foreach($order_cut_no_sql as $ordCut_val)
    {        
        if($ordcut_number_string=="")
        {
            $val=$ordCut_val[csf('ord_cut')];
             $ordcut_number_string.="'$val'";
        }
        else
        {
            $val=$ordCut_val[csf('ord_cut')];
            $ordcut_number_string.=','."'$val'";
        }
    }
	?>
	<style type="text/css">  
  		table.details-view { 
	    border-bottom:1px solid #000;  
	    font-size: 12px;
	    }    
	    th.outer,  
	    td.outer {  
	    border-left: 1px solid #000;  
	    border-right: 1px solid #000;  
	    border-top: 1px solid #000;  
	    padding: 0.1em;  
	    }  
	      
	    th.inner,  
	    td.inner {  
	    border-top: 1px solid #000;  
	    padding: 1px;  
	    }  

	</style>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px"> 
	                <?
	                    $nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");                 
	                    
	                    foreach ($nameArray as $result)
	                    { 	                    
	                         echo $result[csf('city')];	                        
	                    }
	                ?> 
	            </td>  
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:18px"><u><strong> Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>
	     	<tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Source</strong></td><td width="175px"> : <? echo $knitting_source[$dataArray[0][csf('production_source')]];?></td>
	            <td width="105"><strong>Sewing Company</strong></td>
	            <td>:
	            	<? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?>
	                    	
	            </td>
	        </tr>
	        <tr>
	            
	            <td><strong>Location</strong></td><td>: 
	                <? echo $location_library[$dataArray[0][csf('location_id')]]; ?>
	            </td>
	            <td><strong>Floor</strong></td><td> <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?> </td>
	            <td><strong>Line</strong></td><td> <? echo ": ".$line; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Company </strong></td><td>: <? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
	            <td><strong>Input Date</strong></td><td> <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            <td><strong>Cutting No. </strong></td><td>: <? echo str_replace("'", "", $cut_number_string); ?></td>
	        </tr>
	        <tr>	            
	            <td><strong>Order Cut No. </strong></td><td>: <? echo str_replace("'", "", $ordcut_number_string); ?></td>
	            <td></td><td></td>
	            <td></td><td></td>
	            
	        </tr>
	       
	    </table>
	        
	        <?	            
	        $sqls="SELECT a.item_number_id, b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where 
	                    a.delivery_mst_id ='$data[1]' and a.production_type=9 and b.production_type=9 and e.id=f.job_id  and f.id=a.po_break_down_id 
	                    and a.id=b.mst_id  and b.color_size_break_down_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
	                    and b.is_deleted=0  and f.status_active in(1,2,3)  and f.is_deleted=0 and d.status_active in(1,2,3) 
	                    and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";
	                
	        $result=sql_select($sqls);
	                
            foreach($result as $rows)
            {
                
                $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];
                
                $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
                $size_wise_bundle_num_arr[$rows[csf('size_number_id')]] ++;
                $dataArr[$key]=array(
                    country_id=>$rows[csf('country_id')],
                    buyer_name=>$rows[csf('buyer_name')],
                    po_id=>$rows[csf('po_id')],
                    po_number=>$rows[csf('po_number')],
                    item_number_id=>$rows[csf('item_number_id')],
                    color_number_id=>$rows[csf('color_number_id')],
                    size_number_id=>$rows[csf('size_number_id')],
                    style_ref_no=>$rows[csf('style_ref_no')],
                    style_description=>$rows[csf('style_description')],
                    job_no=>$rows[csf('job_no')],
                    cut_no=>$rows[csf('cut_no')],
                    order_cut_no=>$rows[csf('order_cut_no')]
                );
                $batch_wise_qty_array[$batch_array[$rows[csf("bundle_no")]]]+=$rows[csf('production_qnty')];
                $productionQtyArr[$key]+=$rows[csf('production_qnty')];
                $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
                $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
                
            }
            $width = 868;
            $others_col_width = 720;
            $dif = $width - $others_col_width;                 
	        $total_size = count($bundle_size_arr);
            $size_width = $dif / $total_size;     
	        $table_width=720+($total_size*$size_width);
	        // ksort($bundle_size_arr);
	        ?> 
	    <div style="width:100%;">
	    <table align="left" cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>" border="1" class="details-view rpt_table" style=" margin-top:5px;" >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th class="outer" width="30" rowspan="2">SL</th>
	                <th class="inner" width="100" align="center" rowspan="2">Buyer</th>
	                <th class="inner" width="100" align="center" rowspan="2">PO Number</th>
	                <th class="inner" width="100" align="center" rowspan="2">Style Ref</th>
	                <th class="inner" width="100" align="center" rowspan="2">Style Des</th>
	                <th class="inner" width="100" align="center" rowspan="2">Color</th>
	                <th class="inner" align="center" width="<? echo $size_width;?>" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
	                <th class="inner" width="80" align="center" rowspan="2">Total Qty</th>
	                <th class="outer" width= align="center" rowspan="2">Remarks</th>
	              </tr>
	              <tr>
	                <?
	                $i=0;
	                foreach($bundle_size_arr as $inf)
	                {
		                ?>
		                <th class="inner" align="center" width="<? echo $size_width;?>" rowspan="2"><? echo $size_library[$inf]; ?></th>
		                <?  
	                }
	                ?>
	             </tr>
	        </thead>
	        <tbody>
	            <?
	            $i=1;
	            $tot_qnty=array();
	            foreach($dataArr as $key=>$row)
	            {
	                
	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                        <td class="outer"><? echo $i;  ?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $row['po_number'];?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $row['style_ref_no']; ?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $row['style_description']; ?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $color_library[$row['color_number_id']]; ?></td>

	                        <?
	                        foreach($bundle_size_arr as $size_id)
	                        {
	                            $size_qty=0;
	                            $size_qty=$sizeQtyArr[$key][$size_id];
	                            ?>
	                            <td class="inner" align="center" width="<? echo $size_width;?>"><? echo $size_qty; ?></td>
	                            <?  
	                            $grand_total_size_arr[$size_id]+=$size_qty;
	                        }
	                        ?>
	                        <td class="inner" align="center"><? echo $productionQtyArr[$key]; ?></td>
	                        <td class="outer" align="center"> <?  //echo $val[csf('num_of_bundle')]; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $grand_total_qty+=$productionQtyArr[$key];
	                    $grand_total_bundle_num+=count($bundleArr[$key]);
	                    $grand_total_reject_qty+=$val['reject_qty'];
	                
	           }
	           ?>
	        </tbody>
	        <tr bgcolor="#DDDDDD">
	      
	            <td class="outer" colspan="6" align="right"><strong>Bundle No :</strong></td>
	            <?
	            foreach($bundle_size_arr as $size_id)
	            {
	                ?>
	                <td class="inner" align="center" width="<? echo $size_width;?>"><? echo $size_wise_bundle_num_arr[$size_id]; ?></td>
	                <?  
	            }
	            ?>
	            <td class="inner" align="center"><? echo $grand_total_bundle_num; ?></td>
	            <td class="outer"  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
	        </tr>                           
	    </table>
	 </div>
	 <br clear="all">
	 <!-- ==================================== DETAILS PART START ====================================== -->
	<?
	if($db_type==0)
    {
        $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qnty,a.bundle_no,c.po_break_down_id,b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id  
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,c.po_break_down_id,b.size_number_id order by a.bundle_no,b.size_number_id asc";             
    }
    else
    {                   
        $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qnty,a.bundle_no,a.id,b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id  
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,b.size_number_id,a.id order by a.id, b.size_number_id asc";
    }
    $sql_res = sql_select($sql);
    $bundleNoArr = "";
    $bundleNo = "";
    foreach($sql_res as $val)
    {
        $size_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]] 		+= $val[csf('production_qnty')];
        $reject_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]] 	+= $val[csf('reject_qnty')];
        $size_wise_bundle_no_arr[$val[csf('size_number_id')]] ++;
        $bundleNoArr = $val[csf('bundle_no')];
        $bundleNo .= "'$bundleNoArr',";
    }
    $bundleNo =  chop($bundleNo,",");
    // ===================================== CUTTING REJECT ==========================
    $cut_sql = "SELECT sum(case when a.production_type =1 then a.reject_qty else 0 end) as cut_reject_qnty,
        sum(case when a.production_type in(2,3) and embel_name in(1,2) then a.reject_qty else 0 end) as emb_reject_qnty, 
        a.bundle_no,a.id,b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where a.bundle_no in($bundleNo) and c.id=a.mst_id  
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,b.size_number_id,a.id order by a.id, b.size_number_id asc ";
    $rej_qty_arr = array();
    $cut_sql_res = sql_select($cut_sql);
    foreach ($cut_sql_res as $val) 
    {
        $rej_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]['cut_reject_qnty'] += $val[csf('cut_reject_qnty')];
        $rej_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]['emb_reject_qnty'] += $val[csf('emb_reject_qnty')];
    } 
    // ================================ CUTTING QC REJECT QNTY ==============================
    $cutting_qc_sql="SELECT a.bundle_no,a.size_id, sum(d.reject_qty) as reject_qty,c.cutting_no
    from pro_gmts_cutting_qc_dtls a
    where a.status_active=1 and a.bundle_no in($bundleNo)
    group by a.bundle_no,a.size_id";
    $cutting_qc_sql_res = sql_select($cutting_qc_sql);
    $cutting_rej_array = array();
    foreach ($cutting_qc_sql_res as $row) 
    {
        $cutting_rej_array[$row[csf('bundle_no')]][$row[csf('size_id')]] += $row[csf('reject_qty')];
    }

   /* echo "<pre>";
    print_r($rej_qty_arr);
    echo "</pre>";
    die();*/
    ?>
    <div class="details">
    <?
    $num_item = 52; //we set number of item in each col
	$current_col = 0;
	$column_data = '';
    $i = 1;
    	foreach ($size_qty_arr as $bundle_no => $bundle_data)
	    {
	        foreach ($bundle_data as $size_id => $size_qty)
	        {
	            $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
	        	if($current_col == 0)	 
	        	{      
					
					$column_data .='<div style="margin-top: 10px; width: 31.6%; float: left;padding: 0 3px; text-align:center;">
						 <table width="100%" cellpadding="0" cellspacing="0" class="details-view" border="1" style="margin-left:auto;margin-right:auto;">
					        <thead>
						        <tr>
						            <td width="100%" colspan="5" align="center"><strong>Size Wise Summary</strong></td>
						        </tr>
						        <tr bgcolor="#dddddd" align="center">
						            <td width="30%" class="inner">Bundle No</td>
						            <td width="13%" class="inner">Size</td>
						            <td width="19%" class="inner">Bundle Qty</td>
						            <td width="19%" class="inner">Reject Qty</td>
						            <td width="19%" class="outer">Actual Qty</td>
						        </tr>
					        </thead>
					        <tbody>';
					        	 				        	 
				} 
					        						        
						$column_data .='<tr bgcolor='.$bgcolor.'>
						                <td width="30%" class="inner" align="center">'.substr($bundle_no, 7).'</td>
						                <td width="13%" class="inner" align="center">'.$size_library[$size_id].'</td>
						                <td width="19%" class="inner" align="right">'.$size_qty.'</td>
						                <td width="19%" class="inner" align="right">'.($rej_qty_arr[$bundle_no][$size_id]['cut_reject_qnty']+$rej_qty_arr[$bundle_no][$size_id]['emb_reject_qnty']+$cutting_rej_array[$bundle_no][$size_id]).'</td>
						                <td width="19%" class="outer" align="right">'.($size_qty-($rej_qty_arr[$bundle_no][$size_id]['cut_reject_qnty']+$rej_qty_arr[$bundle_no][$size_id]['emb_reject_qnty']+$cutting_rej_array[$bundle_no][$size_id])).'</td>
						            </tr>';
					            if ($current_col == $num_item - 1)  // Close the row if $current_col equals to 2 in the ($num_cols -1)
					            {
							        $current_col = 0;
							        $column_data .= '</tbody></table></div>';    
							    } 
							    else 
							    {
							        $current_col++;            
							    }
					$i++;	 
				}
			}
			echo $column_data;
			?>
		</div>
		</div>  
		     <?
		        // echo signature_table(26, $data[0], "730px");
		     ?>
		    
		</div>
		    </div>
		    <script type="text/javascript" src="../../js/jquery.js"></script>
		    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		
		<?
		exit(); 	
}
if ($action == "emblishment_issue_print_7") 
{
    extract($_REQUEST);
    $data = explode('*', $data);
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $size_library = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 ", 'id', 'buyer_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    
    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=9 and id='$data[1]' and 
    status_active=1 and is_deleted=0 ";

    $dataArray = sql_select($sql);
    $delivery_mst_id = $dataArray[0][csf('id')];

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
     {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } 
    else
     {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
     }
    unset($line_data);
    $page_id_arr=[1,2];
    $copy_name=array(1=>'st',2=>'nd',3=>'rd',4=>'th');

    $kk=0;
    foreach($page_id_arr as $vals)
    {
        $production_quantity  =0;
        $total_bundle  = 0;
        $size_qty_arr=array();
        $size_wise_bundle_no_arr=array();
        ?>
        <style type="text/css">
            @media print 
            {
                #footer_id {page-break-after: always;}
            }
        </style>


            <div style="width:930px;">
                <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
                    <tr>
                        <td colspan="5" align="center" style="font-size:24px">
                            <strong style="margin-left: 300px !important;"><? echo $company_library[$data[0]]; ?></strong>
                            
                            </td>
                            <td><span style="font-size:x-large;  "><? echo $vals;?><sup><? echo $copy_name[$vals]; ?></sup> Copy</span></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="6" align="center" style="font-size:14px">
                            <?

                            $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                            foreach ($nameArray as $result) {

                                echo $result[csf('city')];

                            }
                            unset($nameArray);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Cutting Delivery To Supershop</strong></u></td>
                    </tr>
                    <tr>
                        <td width="95"><strong>Challan No</strong></td>
                        <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                        <td width="80"><strong>Source</strong></td>
                        <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                        <td width="120"><strong>Sew. Company</strong></td>
                        <td>
                            <?
                            if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                            else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Location </strong></td>
                        <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                        <!-- <td><strong>Floor  </strong></td>
                        <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                        <td><strong>Line </strong></td>
                        <td><? echo ": ".$line; ?></td> -->
                    </tr>
                    <tr>
                    <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
                    <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
                    <td><strong>Input Date  </strong></td>
                     <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                    </tr>
                    <tr>
                        <!-- <td><strong>WO No </strong></td><td>: <? echo return_field_value("wo_order_no", "pro_garments_production_mst", "delivery_mst_id=$delivery_mst_id and status_active=1 and is_deleted=0"); ?></td> -->
                        
                        <td><strong>Remarks</strong></td>
                        <td colspan="3"> : <? //echo $dataArray[0][csf('sewing_line')];
                            ?></td>
                    </tr>


                    <!-- <tr>
                        <td colspan="6" id="barcode_img_id_<? echo $kk;?>"></td>
                    </tr> -->

                </table>
                <br>
                <?

                

                if ($data[2] == 3) {

                    $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id, d.color_number_id, b.cut_no,b.reject_qty,
                    count(b.id) as  num_of_bundle
                    
                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
                    where a.delivery_mst_id ='$data[1]' 
                    and a.id=b.mst_id and b.production_type=9 and b.color_size_break_down_id=d.id and  b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3)
                    and d.is_deleted=0 
                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id,d.color_number_id,b.cut_no,b.reject_qty
                    order by  length(b.bundle_no) asc, b.bundle_no  asc";//a.po_break_down_id,d.color_number_id 
                }
                 else {
                    $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id, b.color_number_id
                    
                    from pro_garments_production_mst c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
                    where c.delivery_mst_id ='$data[1]' 
                    and c.id=a.mst_id and a.color_size_break_down_id=b.id and a.production_type=9 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 
                    group by c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id,b.color_number_id order by  length(a.bundle_no) asc, a.bundle_no asc";
                }


             //  echo $sql;
                //die;

                $result = sql_select($sql);
                $all_cut_arr=array();
                $all_po_arr=array();

                foreach($result as $v)
                {
                    $all_cut_arr[$v[csf("cut_no")]]=$v[csf("cut_no")];
                    $all_po_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
                }
                $all_cut_nos="'".implode("','", $all_cut_arr)."'";
                $all_po_nos="'".implode("','", $all_po_arr)."'";
                $ppl_mst_sql=sql_select("SELECT cutting_no,batch_id from ppl_cut_lay_mst where status_active=1 and cutting_no in($all_cut_nos) ");
                $ppl_mst_arr=array();
                foreach($ppl_mst_sql as $p_val)
                {
                    $ppl_mst_arr[$p_val[csf("cutting_no")]]=$p_val[csf("batch_id")];
                }

                $order_array = array();
                $order_sql = "SELECT a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity,b.grouping from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.id in($all_po_nos)";
                $order_sql_result = sql_select($order_sql);
                foreach ($order_sql_result as $row) 
                {
                    $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                    $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                    $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
                    $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
                    $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
                    $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
                    $order_array[$row[csf('id')]]['int_ref'] = $row[csf('grouping')];
                }
                unset($order_sql_result);


                $sql_cut="SELECT a.order_cut_no, b.bundle_no, b.number_start as number_start, b.number_end as number_end  from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where a.id=b.dtls_id and b.status_active=1 and b.is_deleted=0 and b.order_id in($all_po_nos)";
                $sql_cut_res = sql_select($sql_cut); $number_arr=array();
                foreach($sql_cut_res as $row)
                {
                    $number_arr[$row[csf('bundle_no')]]['number_start']=$row[csf('number_start')];
                    $number_arr[$row[csf('bundle_no')]]['number_end']=$row[csf('number_end')];
                    $number_arr[$row[csf('bundle_no')]]['order_cut_no']=$row[csf('order_cut_no')];
                }
                unset($sql_cut_res);


                ?>

                <div style="width:100%;">
                    <table cellspacing="0" width="1000" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
                        <thead bgcolor="#dddddd" align="center">
                        <th width="30">SL</th>
                        <th width="60" align="center">Cutting Number</th>
                        <th width="60" align="center">Bundle No</th>
                        <th width="60" align="center">Job No</th>
                        <th width="80" align="center">Buyer</th>
                        <th width="80" align="center">Style Ref</th>
                        <th width="100" align="center">Style Des</th>
                        <th width="80" align="center">Internal Ref.</th>
                        <th width="80" align="center">Order No.</th>
                        <th width="80" align="center">Order Cut</th>
                        <th width="80" align="center">Gmt. Item</th>
                        <th width="80" align="center">Color</th>
                        <th width="80" align="center">Size</th>
                        <th width="60" align="center">Reject Qty</th>
                        <th width="60" align="center">RMG Qty</th>
                        <th width="60" align="center">Gmt. Qty</th>
                        </thead>
                        <tbody>
                        <?
                        $i = 1;
                        $size_wise_bundle_no = 0;
                        $num_of_bundle = 0;
                        $tot_qnty = array();
                        foreach ($result as $val) 
                        {
                            

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            $color_count = count($cid);
                            $number_start=''; $number_end='';
                            $number_start=$number_arr[$val[csf('bundle_no')]]['number_start'];
                            $number_end=$number_arr[$val[csf('bundle_no')]]['number_end'];
                            $order_cut_no=$number_arr[$val[csf('bundle_no')]]['order_cut_no'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td><? echo $i; ?></td>
                                <td align="center"><? echo $val[csf('cut_no')]; ?></td>
                                <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                                <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['int_ref']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                                <td align="center"><? echo  $order_cut_no; ?></td>
                                <td align="center"><? echo  $garments_item[$val[csf("item_number_id")]]; ?></td>
                                <td align="center"><? echo $color_library[$val[csf('color_number_id')]]; ?></td>
                                <td align="center"><? echo $size_library[$val[csf('size_number_id')]]; ?></td>
                               
                                
                                <td align="right"><? 
                                if($val[csf('reject_qty')]!=0){
                                echo $val[csf('reject_qty')]; 
                                }else{
                                echo  "&nbsp;"; 
                                }
                                ?>
                                </td>
                                <td align="center"><? echo $number_start.'-'.$number_end; ?></td>
                                <td align="right"><? echo $val[csf('production_qnty')]; ?></td>

                            </tr>
                            <?
                            $production_quantity += $val[csf('production_qnty')];
                            $total_bundle += $val[csf('num_of_bundle')];
                            $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
                            $size_wise_bundle_no_arr[$val[csf('size_number_id')]] += $val[csf('num_of_bundle')];
                            $i++;
                        }
                        ?>
                        </tbody>
                        <tr>
                            <td colspan="10"></td>
                        </tr>
                        <tr>
                            <!-- <td colspan="3"><strong>No. Of Bundle :<? echo $total_bundle; ?></strong></td> -->
                            <td colspan="15" align="right"><strong>Grand Total </strong></td>
                            <td align="right"><? echo $production_quantity; ?></td>
                        </tr>
                    </table>

                    <br>
                    <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
                        <thead>
                        <tr>
                            <td colspan="4"><strong>Size Wise Summary</strong></td>
                        </tr>
                        <tr bgcolor="#dddddd" align="center">
                            <td>SL</td>
                            <td>Size</td>
                            <td>No Of Bundle</td>
                            <td>Quantity (Pcs)</td>
                        </tr>
                        </thead>
                        <tbody>
                        <? $i = 1;
                        foreach ($size_qty_arr as $size_id => $size_qty):
                            $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $i; ?></td>
                                <td align="center"><? echo $size_library[$size_id]; ?></td>
                                <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                                <td align="right"><? echo $size_qty; ?></td>
                            </tr>
                            <?
                            $i++;
                        endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right"><strong>Total </strong></td>
                            <td align="center"><? echo $total_bundle; ?></td>
                            <td align="right"><? echo $production_quantity; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                    <br>
                    <?
                    echo signature_table(28, $data[0], "900px");
                    ?>
                </div>
            </div>
            <div id="footer_id"></div>

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess) {
                var ids='<? echo $kk;?>';
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };

                value = {code: value, rect: false};
                $("#barcode_img_id_"+ids).show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
        </script>
    <?
        $kk++;
    }
    exit();
}
?>
