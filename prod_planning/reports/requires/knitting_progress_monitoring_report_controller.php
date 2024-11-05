<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

require_once('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


//$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
$company_short_arr = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
//$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
//$color_type_arr = return_library_array("select id, color_type_id from wo_pre_cost_fabric_cost_dtls", 'id', 'color_type_id');


if ($action == "load_drop_down_buyer") 
{
    echo create_drop_down("cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "- All Buyer -", $selected, "");
    exit();
}

if ($action == "load_drop_down_party_type") 
{
    $explode_data = explode("**", $data);
    $data = $explode_data[0];
    $selected_company = $explode_data[1];

    if ($data == 3) 
    {
        echo create_drop_down("cbo_party_type", 110, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$selected_company' and b.party_type =20 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "");
    } 
    else if ($data == 1) 
    {
        echo create_drop_down("cbo_party_type", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--- Select ---", $selected_company, "", 0, 0);
    } 
    else 
    {
        echo create_drop_down("cbo_party_type", 110, $blank_array, "", 1, "--- Select ---", $selected, "", 1);
    }
    exit();
}

if ($action == "order_no_search_popup") 
{
    echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>

        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data()
        {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            tbl_row_count = tbl_row_count - 1;

            for (var i = 1; i <= tbl_row_count; i++)
            {
                $('#tr_' + i).trigger('click');
            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function js_set_value(str) {

            if (str != "")
                str = str.split("_");

            toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

            if (jQuery.inArray(str[1], selected_id) == -1) {
                selected_id.push(str[1]);
                selected_name.push(str[2]);

            } else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == str[1])
                        break;
                }
                selected_id.splice(i, 1);
                selected_name.splice(i, 1);
            }
            var id = ''; var name = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + '*';
            }

            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);

            $('#hide_order_id').val(id);
            $('#hide_order_no').val(name);
        }

    </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:780px;">
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Please Enter Order No</th>
                        <th>Shipment Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                        <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <?
                                        echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
                                    ?>
                                </td>                 
                                <td align="center"> 
                                    <?
                                        $search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No");
                                        $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                                        echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                                    ?>
                                </td>     
                                <td align="center" id="search_by_td">               
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                </td> 
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                                </td>   
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'knitting_progress_monitoring_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top:15px" id="search_div"></div>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_order_no_search_list_view") 
{
    $data = explode('**', $data);
    $company_id = $data[0];

    if ($data[1] == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            else
                $buyer_id_cond = "";
        }
        else {
            $buyer_id_cond = "";
        }
    } else {
        $buyer_id_cond = " and a.buyer_name=$data[1]";
    }

    $search_by = $data[2];
    $search_string = "%" . trim($data[3]) . "%";

    if ($search_by == 1)
        $search_field = "b.po_number";
    else if ($search_by == 2)
        $search_field = "a.style_ref_no";
    else
        $search_field = "a.job_no";

    $start_date = $data[4];
    $end_date = $data[5];

    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
        } else {
            $date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
        }
    } else {
        $date_cond = "";
    }
    $company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
    $arr = array(0 => $company_library, 1 => $buyer_arr);

    if ($db_type == 0)
        $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2)
        $year_field = "to_char(a.insert_date,'YYYY') as year";
    else
        $year_field = ""; //defined Later

    $sql = "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '', 1);
    exit();
}

if ($action == "booking_no_search_popup") 
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode('_',$data);
    ?>  
    <script>

        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data()
        {
            var tbl_row_count = document.getElementById('list_view').rows.length;
            tbl_row_count = tbl_row_count - 1;

            for (var i = 1; i <= tbl_row_count; i++)
            {
                $('#tr_' + i).trigger('click');
            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function js_set_value(str) {

            if (str != "")
                str = str.split("_");

            toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

            if (jQuery.inArray(str[1], selected_id) == -1) {
                selected_id.push(str[1]);
                selected_name.push(str[2]);

            } else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == str[1])
                        break;
                }
                selected_id.splice(i, 1);
                selected_name.splice(i, 1);
            }
            var id = ''; var name = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + '*';
            }

            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);

            $('#selected_booking').val(id);
            $('#selected_booking_no').val(name);
        }
        /*function js_set_value(booking_no)
        {
            document.getElementById('selected_booking').value=booking_no;
            //alert(booking_no);
            parent.emailwindow.hide();
        }*/
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <tr>
                <td align="center" width="100%">
                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                        <thead>                  
                            <th width="150">Company Name</th>
                            <th width="140">Buyer Name</th>
                            <th width="80">Booking No</th>
                            <th width="180">Short Booking Date</th>
                            <th>&nbsp;</th>
                        </thead>
                        <tr>
                            <td>
                                <input type="hidden" id="selected_booking">
                                <input type="hidden" id="selected_booking_no">

                                <input type="hidden" id="job_no" value="<? echo $data[2];?>">
                                <? 
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'knitting_progress_monitoring_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --" ); ?></td>
                            <td>
                                <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('txt_booking_no').value, 'create_booking_search_list_view', 'search_div', 'knitting_progress_monitoring_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                             </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? 
                    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );        
                    echo load_month_buttons();  ?>
                </td>
            </tr>
            <tr>
                <td align="center"valign="top" id="search_div"></td>
            </tr>
        </table>    
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}

if ($action=="create_booking_search_list_view")
{
    $data=explode('_',$data);
    if ($data[0]!=0) $company="  company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";
    if ($data[4]!=0) $job_no=" and job_no='$data[4]'"; else $job_no='';
    if ($data[5]!=0) $booking_no=" and booking_no_prefix_num='$data[5]'"; else $booking_no='';
    if($db_type==0)
    {
        if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
    }
    if($db_type==2)
    {
        if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
    }
    $po_array=array();
    $sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst where $company $buyer $booking_no $booking_date and booking_type=1 and is_short=2 and status_active=1  and  is_deleted=0 order by booking_no");
    foreach($sql_po as $row)
    {
        $po_id=explode(",",$row[csf("po_break_down_id")]);
        //print_r( $po_id);
        $po_number_string="";
        foreach($po_id as $key=> $value )
        {
            $po_number_string.=$order_arr[$value].",";
        }
        $po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
    }
    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No");
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);

    $sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and $company $buyer $booking_no $booking_date and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0  group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved order by a.booking_no_prefix_num Desc";

    echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,70,100,90,200,80,80,50,50","1020","250",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','',1);
    
    exit(); 
}

if ($action == "report_generate") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $type = str_replace("'", "", $type);
    $cbo_company_name = str_replace("'", "", $cbo_company_name);
    $cbo_knitting_source = str_replace("'", "", $cbo_knitting_source);
    $cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
    $cbo_party_type = str_replace("'", "", $cbo_party_type);
    $cbo_year = str_replace("'", "", $cbo_year);
    $txt_job_no = str_replace("'", "", $txt_job_no);
    $txt_order_no = str_replace("'", "", $txt_order_no);
    $hide_order_id = str_replace("'", "", $hide_order_id);
    $txt_booking_no = str_replace("'", "", $txt_booking_no);
    $txt_booking_id = str_replace("'", "", $txt_booking_id);
    $txt_program_no = str_replace("'", "", $txt_program_no);
    $cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
    $cbo_shipment_status = str_replace("'", "", $cbo_shipment_status);
    $cbo_based_on = str_replace("'", "", $cbo_based_on);
    $txt_date_from = str_replace("'", "", $txt_date_from);
    $txt_date_to = str_replace("'", "", $txt_date_to);

    if(str_replace("'","",trim($txt_date))=="") $prod_date_cond=""; 
    else $prod_date_cond=" ,case when a.receive_date = $txt_date then b.grey_receive_qnty else 0 end as current_prod_qty";

    $buyer_arr = return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name",'id','buyer_name');
    $machine_arr = return_library_array( "select id,machine_no from lib_machine_name where status_active =1 and is_deleted=0",'id','machine_no');
    //$color_arr = return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
    $yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

    $program_no_cond=""; $program_status_cond=""; $booking_cond=""; $buyer_cond=""; $job_no_cond=""; $order_id_cond=""; $party_cond="";
    $shipment_status_cond="";
    $shipment_status_cond1="";

    if($txt_program_no){ $program_no_cond=" and b.id=$txt_program_no"; }
    if($cbo_knitting_status) { $program_status_cond=" and b.status in ($cbo_knitting_status)"; }
    
    if($txt_booking_no)
    { 
        $txt_booking_nos="'".implode("','", explode("*", $txt_booking_no))."'";
        $booking_cond=" and a.booking_no in ($txt_booking_nos)"; 
    }

    if($cbo_knitting_source >0){ 
        $knitting_source_cond=" and b.knitting_source=$cbo_knitting_source"; 
    }else{
        $knitting_source_cond=" and b.knitting_source in (1,3)";
    }

    if($cbo_buyer_name > 0) { $buyer_cond=" and a.buyer_id=$cbo_buyer_name"; }
    if($cbo_shipment_status > 0){
        
         $shipment_status_cond= " and b.shiping_status=$cbo_shipment_status";
         $shipment_status_cond1= " and e.shiping_status=$cbo_shipment_status";
        
        }

    if ($db_type == 0){
        $year_field_cond = " and YEAR(d.insert_date)=$cbo_year";
    }else if ($db_type == 2){
        $year_field_cond = " and to_char(d.insert_date,'YYYY')=$cbo_year";
    }

    if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") 
    {
        if ($cbo_based_on == 2) {
            $date_cond = " and b.program_date between '" . trim($txt_date_from) . "' and '" . trim($txt_date_to) . "'";
        } else {
            $date_cond = " and b.start_date between '" . trim($txt_date_from) . "' and '" . trim($txt_date_to) . "'";
        }
    } else {
        $date_cond = "";
    }



    if($txt_job_no){ $job_no_cond=" and d.job_no_prefix_num=$txt_job_no"; }
    if($hide_order_id){ $order_id_cond= " and c.po_break_down_id in ($hide_order_id)"; }
    if($cbo_party_type){ $party_cond=" and b.knitting_party=$cbo_party_type"; }

    $booking_item_array = array();
    if ($db_type == 0) {
        $booking_item_array = return_library_array("select a.booking_no, group_concat(distinct(b.item_id)) as prod_id from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no", 'booking_no', 'prod_id');
    } else {
        $booking_item_array = return_library_array("select a.booking_no, LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id) as prod_id from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no", 'booking_no', 'prod_id');
    }

    $yarn_desc_array = array();
    $sql = "select id, lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type from product_details_master where item_category_id=1";
    $result = sql_select($sql);
    foreach ($result as $row) 
    {
        $compostion = '';
        if ($row[csf('yarn_comp_percent2nd')] != 0) {
            $compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
        } else {
            $compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
        }

        $yarn_desc = $row[csf('lot')] . " " . $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion . " " . $yarn_type[$row[csf('yarn_type')]];
        $yarn_desc_array[$row[csf('id')]] = $yarn_desc;
    }

    if($type==1)
    {
        //Finding all po_id, booking_no, program_id, booking_color

        $sql="SELECT a.id as plan_id, a.booking_no, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as prog_id, c.po_break_down_id, c.fabric_color_id as color_number_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_dtls c, wo_po_details_master d where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and company_id=$cbo_company_name $buyer_cond $program_no_cond $program_status_cond $booking_cond $knitting_source_cond $party_cond $date_cond and a.booking_no=c.booking_no and c.status_active=1 and c.is_deleted=0 and c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 $year_field_cond $job_no_cond $order_id_cond group by a.id, a.booking_no, c.po_break_down_id, b.id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, c.fabric_color_id";
        
        foreach (sql_select($sql) as $data) 
        {
           $po_id_arr[$data[csf("po_break_down_id")]]=$data[csf("po_break_down_id")];
           $booking_no_arr[$data[csf("booking_no")]]=$data[csf("booking_no")];
           $prog_id_arr[$data[csf("prog_id")]]=$data[csf("prog_id")];
           //$booking_color_arr[$data[csf("booking_no")]][$data[csf("fabric_desc")]][$data[csf("gsm_weight")]][$data[csf("dia")]][$data[csf("width_dia_type")]][$data[csf("po_break_down_id")]]["color"]=$data[csf("color_number_id")];
           
           //$booking_color_arr[$data[csf("booking_no")]][$data[csf("fabric_desc")]][$data[csf("gsm_weight")]][$data[csf("dia")]][$data[csf("po_break_down_id")]]["color"]=$data[csf("color_number_id")];
        }

        //Finding Program qty, yarn_desc id, all_programmed po
        $all_prog_ids=implode(",", $prog_id_arr); 
        $plan_sql="SELECT mst_id, dtls_id, yarn_desc, po_id, program_qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and dtls_id in ($all_prog_ids)";
        foreach (sql_select($plan_sql) as $data) 
        {
            $pre_cost_fabric_cost_dtls_id_arr[$data[csf("mst_id")]][$data[csf("dtls_id")]].= $data[csf("yarn_desc")].",";
            $program_qnty_arr[$data[csf("mst_id")]][$data[csf("dtls_id")]][$data[csf("po_id")]][$data[csf("yarn_desc")]]=$data[csf("program_qnty")];
            $prog_qty_arr[$data[csf("dtls_id")]]["tot_prog_gty"] +=$data[csf("program_qnty")];
            $all_plan_po_ids[$data[csf("po_id")]]=$data[csf("po_id")];
        }
        //echo "<pre>";print_r($pre_cost_fabric_cost_dtls_id_arr);die;
        //Finding Booking informations
        $all_booking_ids="'".implode("','", $booking_no_arr)."'";
        $booking_sql="SELECT a.booking_no_prefix_num, a.booking_no, b.job_no, b.po_break_down_id,b.pre_cost_fabric_cost_dtls_id, a.booking_type, a.item_category, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.grey_fab_qnty, b.fabric_color_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($all_booking_ids)";
        foreach (sql_select($booking_sql) as $data) 
        {
            $booking_arr[$data[csf("booking_no")]]["booking_no"]=$data[csf("booking_no_prefix_num")];
            $booking_qty_arr[$data[csf("booking_no")]][$data[csf("job_no")]][$data[csf("po_break_down_id")]][$data[csf("gsm_weight")]][$data[csf("dia_width")]][$data[csf("fabric_color_id")]]["booking_qty"] +=$data[csf("grey_fab_qnty")];
            $tot_booking_qty_arr[$data[csf("booking_no")]]["booking_qty"] +=$data[csf("grey_fab_qnty")];
            $booking_job_arr[$data[csf("job_no")]]=$data[csf("job_no")];
        }

        //Finding_allocation_qty

        /*$allocation_sql="SELECT a.booking_no, sum(a.qnty) as alloc_qty from inv_material_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and a.booking_no in ($all_booking_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_dyied_yarn!=1 group by a.booking_no";
        foreach (sql_select($allocation_sql) as $value) 
        {
            $allocation_ar[$value[csf("booking_no")]]["alloc_qty"]=$value[csf("alloc_qty")];
        }*/

        //Finding Order Informations
        $all_po_ids=implode(",", $po_id_arr);
        $order_sql="SELECT b.id, b.po_number, b.shiping_status, b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $shipment_status_cond and b.id in ($all_po_ids)";
        foreach (sql_select($order_sql) as $data) 
        {
            $order_arr[$data[csf("id")]]["po_number"]=$data[csf("po_number")];
            $order_arr[$data[csf("id")]]["shiping_status"]=$data[csf("shiping_status")];
            $order_arr[$data[csf("id")]]["shipment_date"]=$data[csf("shipment_date")];
        }

        //Finding Requisition Informations
        $requsition_sql="SELECT knit_id as prog_id, requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 and knit_id in ($all_prog_ids)";
        foreach (sql_select($requsition_sql) as $data) 
        {
            $reqstn_arr[$data[csf("prog_id")]]["requ_qty"] += $data[csf("yarn_qnty")];
            $reqstn_arr[$data[csf("prog_id")]]["requ_no"] .= $data[csf("requisition_no")].",";
            $reqstn_arr[$data[csf("prog_id")]]["prod_id"] .= $data[csf("prod_id")].",";
        }

        //Finding_issue_data
        $issue_sql="select b.requisition_no, b.cons_quantity from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=3 and a.entry_form=3 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no in (SELECT distinct(requisition_no) from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 and knit_id in ($all_prog_ids))";
        foreach (sql_select($issue_sql) as $data) 
        {
            $requ_wise_iss[$data[csf("requisition_no")]]["issue_qty"] +=$data[csf("cons_quantity")];
        }

        //Production details
        $sql_production="select a.id, b.id as dtls_id, a.booking_id as prog_id, b.no_of_roll, b.grey_receive_qnty as prod_qty $prod_date_cond from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=2 and a.receive_basis=2 and a.booking_id in ($all_prog_ids)"; 
        foreach (sql_select($sql_production) as $data) 
        {
           $production_data_arr[$data[csf("prog_id")]]["tot_prod_qty"] +=$data[csf("prod_qty")];
           $production_data_arr[$data[csf("prog_id")]]["no_of_roll"] +=$data[csf("no_of_roll")];
           $production_data_arr[$data[csf("prog_id")]]["current_prod_qty"] +=$data[csf("current_prod_qty")];
        }

        //Finding_knitting_charge

        $booking_jobs="'".implode("','", $booking_job_arr)."'";
        $sql_knit_charge="SELECT job_no, fabric_description, charge_unit from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 and job_no in ($booking_jobs)";
        foreach (sql_select($sql_knit_charge) as $data) 
        {
            $knitting_charge_arr[$data[csf("job_no")]][$data[csf("fabric_description")]]=$data[csf("charge_unit")];
        }

        //-----------------------  Making Data Array ---------------------------------------------------------------------


        $all_planed_po=implode(",", $all_plan_po_ids);
        $prog_check_array=array();
        /*$chunked_program = array_chunk(explode(",", $all_prog_ids), 50);
        foreach ($chunked_program as $part_prog_ar) 
        {
            $data=implode(",", $part_prog_ar);*/
        
            $sql_data="SELECT a.id as plan_id, a.booking_no, a.buyer_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as prog_id, b.stitch_length, b.machine_gg, b.machine_dia, b.machine_id, b.status, b.remarks, b.color_id, b.knitting_source, c.po_break_down_id, c.job_no, d.job_no_prefix_num, d.style_ref_no, c.fabric_color_id, a.body_part_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_dtls c, wo_po_details_master d where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.id in ($all_prog_ids) and a.booking_no=c.booking_no and c.status_active=1 and c.is_deleted=0 and c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and c.po_break_down_id in ($all_planed_po) group by a.id, a.booking_no, c.po_break_down_id, c.job_no, d.job_no_prefix_num, a.buyer_id, d.style_ref_no, b.id, b.stitch_length, b.machine_gg, b.machine_dia, b.machine_id, b.status, b.remarks, a.fabric_desc, b.color_id, a.gsm_weight, a.dia, a.width_dia_type,b.knitting_source, c.fabric_color_id, a.body_part_id order by a.id, b.id";

            $temp_arr=array();
            $sql_results=sql_select($sql_data);
            foreach ($sql_results as $value) 
            {
                $booking       =$value[csf("booking_no")];
                $buyer         =$value[csf("buyer_id")];
                $fabric_desc   =$value[csf("fabric_desc")];
                $gsm           =$value[csf("gsm_weight")];
                $dia           =$value[csf("dia")];
                $width_dia_type=$value[csf("width_dia_type")];
                $po_id         =$value[csf("po_break_down_id")];
                $job_no        =$value[csf("job_no")];
                $job_prefix_num=$value[csf("job_no_prefix_num")];
                $style         =$value[csf("style_ref_no")];
                $color         =$value[csf("color_id")];
            
                $yarn_desc_ids=chop($pre_cost_fabric_cost_dtls_id_arr[$value[csf("plan_id")]][$value[csf("prog_id")]],",");

                $booking_color=$value[csf("fabric_color_id")];

               //$booking_color=$booking_color_arr[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id]["color"];

               //$booking_color=$booking_color_arr[$booking][$fabric_desc][$gsm][$dia][$po_id]["color"];

               //echo $booking_color."**".$color." ";    
               if(in_array($booking_color, explode(",", $color)))
               {
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["job"]=$value[csf("job_no_prefix_num")];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["booking_no"]=$booking_arr[$booking]["booking_no"];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["buyer"]=$buyer_arr[$buyer];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["po_num"]=$order_arr[$po_id]["po_number"];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["style"]=$style;
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["Shipping_status"]=$shipment_status[$order_arr[$po_id]["shiping_status"]];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["fabric_desc"]=$fabric_desc;
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["dia"]=$dia;
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["width_dia_type"]=$fabric_typee[$width_dia_type];

                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["gsm"]=$gsm;
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["color"]=$color;

                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["prog_id"] .=$value[csf("prog_id")].",";

                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["stitch_length"] .=$value[csf("stitch_length")].", ";
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["machine_gg"] .=$value[csf("machine_gg")].", ";
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["machine_no"] .=$machine_arr[$value[csf("machine_id")]].",";
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["machine_dia"] .=$value[csf("machine_dia")].",";
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["status"] .= $knitting_program_status[$value[csf("status")]].","; 
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["body_part_id"] .= $body_part[$value[csf("body_part_id")]]."*";

                    foreach (array_filter(explode(",",$yarn_desc_ids)) as $yarn_desc_id) 
                    {
                       
                        if($value[csf("knitting_source")]==1)
                        {
                            $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["inside_prog_qty"] +=$program_qnty_arr[$value[csf("plan_id")]][$value[csf("prog_id")]][$po_id][$yarn_desc_id];
                        }
                        else
                        {
                            $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["outside_prog_qty"] +=$program_qnty_arr[$value[csf("plan_id")]][$value[csf("prog_id")]][$po_id][$yarn_desc_id];
                        }   
                    }
                    if(in_array($yarn_desc_id, $temp_arr[$job_no][$fabric_desc])==false)
                    {   
                        $count=1;
                        $knitting_data[$booking][$job_no][$fabric_desc]["knitting_charge"]+=$knitting_charge_arr[$job_no][$yarn_desc_id];
                        $knitting_data[$booking][$job_no][$fabric_desc]["count"]+=$count;
                        $temp_arr[$job_no][$fabric_desc][$yarn_desc_id]=$yarn_desc_id;
                    }
               }
            }//die;
      //  }
        
        //--------------------------------------- Main Grouping Sql --------------------------------------------------------------------

        $all_planned_po_ids=implode(",", $all_plan_po_ids);
        //$sql_main="SELECT a.booking_no, a.buyer_id, a.fabric_desc, a.gsm_weight, c.dia_width as dia, a.width_dia_type, c.po_break_down_id, c.job_no, d.job_no_prefix_num, d.style_ref_no, e.color_number_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_dtls c, wo_po_details_master d, wo_po_color_size_breakdown e where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and company_id=$cbo_company_name $buyer_cond $program_no_cond $program_status_cond $booking_cond $knitting_source_cond $party_cond and a.booking_no=c.booking_no and c.status_active=1 and c.is_deleted=0 and c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and b.id in ($all_prog_ids) and c.po_break_down_id in ($all_planned_po_ids) $year_field_cond $job_no_cond $order_id_cond and e.id=c.color_size_table_id and e.job_no_mst=d.job_no and c.po_break_down_id=e.po_break_down_id and e.status_active=1 group by a.booking_no, c.po_break_down_id, c.job_no, d.job_no_prefix_num, a.buyer_id, d.style_ref_no, a.fabric_desc, a.gsm_weight, c.dia_width, a.width_dia_type, e.color_number_id";

        $sql_main="SELECT a.booking_no, a.buyer_id, a.fabric_desc, a.gsm_weight, c.dia_width as dia, a.width_dia_type, c.po_break_down_id, c.job_no, c.fabric_color_id as color_number_id, d.job_no_prefix_num, d.style_ref_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_dtls c, wo_po_details_master d,wo_po_break_down e where e.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and company_id=$cbo_company_name $buyer_cond $program_no_cond $program_status_cond $booking_cond $knitting_source_cond $party_cond $date_cond $shipment_status_cond1 and a.booking_no=c.booking_no and c.status_active=1 and c.is_deleted=0 and c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and b.id in ($all_prog_ids) and c.po_break_down_id in ($all_planned_po_ids) $year_field_cond $job_no_cond $order_id_cond and a.gsm_weight=c.gsm_weight and c.dia_width=a.dia group by a.booking_no, c.po_break_down_id, c.job_no, d.job_no_prefix_num, a.buyer_id, d.style_ref_no, a.fabric_desc, a.gsm_weight, c.dia_width, a.width_dia_type, c.fabric_color_id order by c.job_no, c.po_break_down_id";
            //echo $sql_main;die;
        $sql_result_main=sql_select($sql_main);

        ob_start();
        ?>
        <fieldset style="width:3900px;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3900" class="rpt_table" id="table_header_1">
                <thead>
                    <th width="40">SL</th>
                    <th width="70">Priority</th>
                    <th width="100">Company</th>
                    <th width="60">Job No.</th>
                    <th width="70">Booking No.</th>
                    <th width="150">Buyer Name</th>
                    <th width="110">Order No</th>
                    <th width="130">Style Name</th>
                    <th width="150">Body Part</th>
                    <th width="120">Shipment Status</th>
                    <th width="70">Shipment Date</th>
                    <th width="150">Fabrics Design</th>
                    <th width="80">Fin. Dia</th>
                    <th width="80">OP. TU.</th>
                    <th width="80">Fin. Gsm</th>
                    <th width="80">Color</th>
                    <th width="80">Req.Qty</th>
                    <th width="110">Program No.</th>
                    <th width="80">Stitch Length</th>
                    <th width="80">GG</th>
                    <th width="80">M/C No.</th>
                    <th width="80">M/C Dia</th>
                    <th width="100">Program Qty (Inside)</th>
                    <th width="100">Program Qty (Outside)</th>
                    <th width="100">Total Program</th>
                    <th width="80">Requisition No</th>
                    <th width="260">Yarn Details</th>
                    <th width="90">Requisition Qty</th>
                    <th width="100">Requisition Balance</th>
                    <th width="90">Yarn Issue</th>
                    <th width="100">Yarn Issue Balance</th>
                    <th width="90">Today Production</th>
                    <th width="90">No of Roll</th>
                    <th width="90">Total Production</th>
                    <th width="100">Balance</th>
                    <th width="100">Knitting Charge</th>
                    <th width="90">Amount</th>
                    <th width="100">Remarks</th>
                    <th>Program Status</th>
                </thead>
            </table>
            <div style="width:3918px; overflow-y:scroll; max-height:350px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3900" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1; $k=1; $po_exist_arr=array();
                            foreach ($sql_result_main as $value) 
                            {
                                $booking       =$value[csf("booking_no")];
                                $buyer         =$value[csf("buyer_id")];
                                $fabric_desc   =$value[csf("fabric_desc")];
                                $gsm           =$value[csf("gsm_weight")];
                                $dia           =$value[csf("dia")];
                                $width_dia_type=$value[csf("width_dia_type")];
                                $po_id         =$value[csf("po_break_down_id")];
                                $job_no        =$value[csf("job_no")];
                                $job_prefix_num=$value[csf("job_no_prefix_num")];
                                $style         =$value[csf("style_ref_no")];
                                $color_id      =$value[csf("color_number_id")];

                                if ($i % 2 == 0){ $bgcolor = "#E9F3FF"; }
                                else{ $bgcolor = "#FFFFFF"; }

                                /*$prod_id = $booking_item_array[$booking];
                                $yarn_desc = '';
                                $prod_id = array_unique(explode(",", $prod_id));
                                foreach ($prod_id as $val) 
                                {
                                    if ($yarn_desc == '') $yarn_desc = $yarn_desc_array[$val]; 
                                    else $yarn_desc .= ", " . $yarn_desc_array[$val];
                                }*/
                                $color_name = $color_library[$color_id];
                                $program_no=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["prog_id"];
                                $program_status=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["status"];
                                $body_part_id=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["body_part_id"];
                                $stitch_length=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["stitch_length"];
                                $machine_gg=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["machine_gg"];
                                $machine_no=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["machine_no"];
                                $machine_dia=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["machine_dia"];
                                $inside_prog_qty=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["inside_prog_qty"];
                                $outside_prog_qty=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["outside_prog_qty"];
                                $booking_qty=$booking_qty_arr[$booking][$job_no][$po_id][$gsm][$dia][$color_id]["booking_qty"];
                                $tot_booking_qty=$tot_booking_qty_arr[$booking]["booking_qty"];
                                $knitting_charge=$knitting_data[$booking][$job_no][$fabric_desc]["knitting_charge"]/$knitting_data[$booking][$job_no][$fabric_desc]["count"];

                                $requisition_no=""; $requisition_qty=0; $issue_qty=0; $no_of_roll=0; $tot_prod_qty=0; $today_prod_qty=0;$prod_id_for_yarn_desc="";
                                foreach (array_filter(explode(",", $program_no)) as $prog) 
                                {
                                    $requisition_no .=implode(",", array_unique(explode(",", chop($reqstn_arr[$prog]["requ_no"],",")))).",";
                                    $requisition_qty +=$reqstn_arr[$prog]["requ_qty"];

                                    $tot_prod_qty += $production_data_arr[$prog]["tot_prod_qty"];
                                    $no_of_roll += $production_data_arr[$prog]["no_of_roll"];
                                    $today_prod_qty += $production_data_arr[$prog]["current_prod_qty"];

                                    $prod_id_for_yarn_desc .=implode(",", array_unique(explode(",", chop($reqstn_arr[$prog]["prod_id"],",")))).",";
                                }
                                $requisition_no=chop($requisition_no,",");

                                foreach (array_filter(explode(",", $requisition_no)) as $req) 
                                {
                                    $issue_qty += $requ_wise_iss[$req]["issue_qty"];
                                }

                                $yarn_desc = '';$prod_id_for_yarn_arr=array();
                                $prod_id_for_yarn_desc=chop($prod_id_for_yarn_desc,",");
                                $prod_id_for_yarn_arr = array_filter(array_unique(explode(",", $prod_id_for_yarn_desc)));
                                foreach ($prod_id_for_yarn_arr as $product_id) 
                                {
                                    if ($yarn_desc == '') $yarn_desc = $yarn_desc_array[$product_id]; 
                                    else $yarn_desc .= ", " . $yarn_desc_array[$product_id];
                                }

                                if (!$po_exist_arr[$po_id]) 
                                {   
                                    $po_exist_arr[$po_id]=$po_id;
                                    if($k>1)
                                    {
                                        ?>
                                        <tr> <td colspan="38" style="background-color: #E6E6E6">&nbsp; </td> </tr>
                                        <?
                                    }
                                    $k++;
                                }

                                $program_status = implode(",",array_filter(array_unique(explode(",", $program_status))));
                                $body_part_id = implode(",",array_filter(array_unique(explode("*", $body_part_id))));
                                ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" >
                                        <td width="40" align="center"><? echo $i; ?></td>
                                        <td width="70" align="center"> </td>
                                        <td width="100" align="center"><? echo $company_short_arr[$cbo_company_name]; ?></td>
                                        <td width="60" align="center"> <? echo $job_prefix_num; ?> </td>
                                        <td width="70" align="center" title="<? echo $booking; ?>"> 
                                            <? echo $booking_arr[$booking]["booking_no"]; ?> 
                                        </td>
                                        <td width="150" align="center"> <? echo $buyer_arr[$buyer]; ?> </td>
                                        <td width="110" align="center" style="word-wrap: break-word;word-break: break-all;"> 
                                            <? echo $order_arr[$po_id]["po_number"]; ?> 
                                        </td>
                                        <td width="130" align="center"> <? echo $style; ?> </td>
                                        <td width="150" align="center"> <p><? echo $body_part_id; ?></p> </td>
                                        <td width="120" align="center" style="word-wrap: break-word;word-break: break-all;"> 
                                            <? echo $shipment_status[$order_arr[$po_id]["shiping_status"]]; ?> 
                                        </td>
                                        <td width="70" align="center"><? echo change_date_format($order_arr[$po_id]["shipment_date"]); ?></td>
                                        
                                        <td width="150" align="center" style="word-wrap: break-word;word-break: break-all;"> 
                                            <? echo $fabric_desc; ?>
                                        </td>
                                        <td width="80" align="center"> <? echo $dia; ?></td>
                                        <td width="80" align="center"> <? echo $fabric_typee[$width_dia_type]; ?> </td>
                                        <td width="80" align="center"> <? echo $gsm; ?> </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;">  
                                            <? echo $color_name; ?>
                                        </td>
                                        <td width="80" align="right"> <? echo number_format($booking_qty, 2); ?> </td>

                                        <td width="110" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($program_no,","); ?> 
                                        </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($stitch_length,", "); ?> 
                                        </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($machine_gg,", "); ?> 
                                        </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($machine_no,","); ?> 
                                        </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($machine_dia,","); ?> 
                                        </td>
                                        <td width="100" align="right">
                                            <? echo number_format($inside_prog_qty,2); ?>
                                        </td>
                                        <td width="100" align="right">
                                            <?  echo number_format($outside_prog_qty,2);  ?>
                                        </td>
                                        <td width="100" align="right"> 
                                            <? 
                                                $tot_prog_qty=$inside_prog_qty+$outside_prog_qty;
                                                echo number_format($tot_prog_qty,2); 
                                            ?>
                                        </td>
                                        <td width="80" align="center" style="word-wrap: break-word; word-break: break-all;"> 
                                            <? echo $requisition_no; ?> 
                                        </td>
                                        <td width="260" align="center" style="word-wrap: break-word; word-break: break-all;">
                                            <? echo $yarn_desc; ?>
                                        </td>
                                        <td width="90" align="right">
                                            <a href="#"> 
                                                <? echo number_format($requisition_qty,2); ?> 
                                            </a>
                                        </td>
                                        <td width="100" align="right">
                                            <?
                                                $requ_bance=$tot_prog_qty-$requisition_qty;
                                                echo number_format($requ_bance,2);
                                            ?>
                                        </td>
                                        <td width="90" align="right"> <a href="#"> <? echo number_format($issue_qty,2); ?> </a> </td>
                                        <td width="100" align="right">
                                            <?
                                                $issue_balance= $requisition_qty-$issue_qty;
                                                echo number_format($issue_balance,2);
                                            ?>
                                        </td>
                                        <td width="90" align="right"> <? echo number_format($today_prod_qty,2); ?> </td>
                                        <td width="90" align="center"><? echo $no_of_roll; ?></td>
                                        <td width="90" align="right"><? echo number_format($tot_prod_qty,2); ?></td>
                                        <td width="100" align="right">
                                            <?
                                                $prod_balance=$tot_prog_qty-$tot_prod_qty;
                                                echo number_format($prod_balance,2);
                                            ?>
                                        </td>
                                        <td width="100" align="right"> 
                                            <? 
                                                if(!is_nan($knitting_charge))
                                                {
                                                    echo number_format($knitting_charge,2); 
                                                }
                                                else
                                                {
                                                    $knitting_charge=0;
                                                    echo number_format($knitting_charge,2); 
                                                }
                                            ?> 
                                        </td>
                                        <td width="90" align="right">
                                            <?
                                                $amount=$tot_prod_qty*$knitting_charge;
                                                echo number_format($amount,2);
                                            ?>
                                        </td>
                                        <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"></td>
                                        <td align="center"><? echo $program_status;?></td>
                                    </tr>

                                    <?
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>

        </fieldset>
        <?
    }
    exit();
}



if ($action == "report_generate2") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $type = str_replace("'", "", $type);
    $cbo_company_name = str_replace("'", "", $cbo_company_name);
    $cbo_knitting_source = str_replace("'", "", $cbo_knitting_source);
    $cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
    $cbo_party_type = str_replace("'", "", $cbo_party_type);
    $cbo_year = str_replace("'", "", $cbo_year);
    $txt_job_no = str_replace("'", "", $txt_job_no);
    $txt_order_no = str_replace("'", "", $txt_order_no);
    $hide_order_id = str_replace("'", "", $hide_order_id);
    $txt_booking_no = str_replace("'", "", $txt_booking_no);
    $txt_booking_id = str_replace("'", "", $txt_booking_id);
    $txt_program_no = str_replace("'", "", $txt_program_no);
    $cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
    $cbo_shipment_status = str_replace("'", "", $cbo_shipment_status);
    $cbo_based_on = str_replace("'", "", $cbo_based_on);
    $txt_date_from = str_replace("'", "", $txt_date_from);
    $txt_date_to = str_replace("'", "", $txt_date_to);

    if(str_replace("'","",trim($txt_date))=="") $prod_date_cond=""; 
    else $prod_date_cond=" ,case when a.receive_date = $txt_date then b.grey_receive_qnty else 0 end as current_prod_qty";

    $buyer_arr = return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name",'id','buyer_name');
    $machine_arr = return_library_array( "select id,machine_no from lib_machine_name where status_active =1 and is_deleted=0",'id','machine_no');


    $yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

    $program_no_cond=""; $program_status_cond=""; $booking_cond=""; $buyer_cond=""; $job_no_cond=""; $order_id_cond=""; $party_cond="";
    $shipment_status_cond="";

    if($txt_program_no){ $program_no_cond=" and b.id=$txt_program_no"; }
    if($cbo_knitting_status) { $program_status_cond=" and b.status in ($cbo_knitting_status)"; }
    
    if($txt_booking_no)
    { 
        $txt_booking_nos="'".implode("','", explode("*", $txt_booking_no))."'";
        $booking_cond=" and a.booking_no in ($txt_booking_nos)"; 
    }

    if($cbo_knitting_source >0){ 
        $knitting_source_cond=" and b.knitting_source=$cbo_knitting_source"; 
    }else{
        $knitting_source_cond=" and b.knitting_source in (1,3)";
    }

    if($cbo_buyer_name > 0) { $buyer_cond=" and a.buyer_id=$cbo_buyer_name"; }
    if($cbo_shipment_status > 0){ $shipment_status_cond= ""; }

    if ($db_type == 0){
        $year_field_cond = " and YEAR(d.insert_date)=$cbo_year";
    }else if ($db_type == 2){
        $year_field_cond = " and to_char(d.insert_date,'YYYY')=$cbo_year";
    }

    if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") 
    {
        if ($cbo_based_on == 2) {
            $date_cond = " and b.program_date between '" . trim($txt_date_from) . "' and '" . trim($txt_date_to) . "'";
        } else {
            $date_cond = " and b.start_date between '" . trim($txt_date_from) . "' and '" . trim($txt_date_to) . "'";
        }
    } else {
        $date_cond = "";
    }



    if($txt_job_no){ $job_no_cond=" and d.job_no_prefix_num=$txt_job_no"; }
    if($hide_order_id){ $order_id_cond= " and c.po_break_down_id in ($hide_order_id)"; }
    if($cbo_party_type){ $party_cond=" and b.knitting_party=$cbo_party_type"; }

    $booking_item_array = array();
    if ($db_type == 0) {
        $booking_item_array = return_library_array("select a.booking_no, group_concat(distinct(b.item_id)) as prod_id from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no", 'booking_no', 'prod_id');
    } else {
        $booking_item_array = return_library_array("select a.booking_no, LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id) as prod_id from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no", 'booking_no', 'prod_id');
    }

    $yarn_desc_array = array();
    $sql = "select id, lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type from product_details_master where item_category_id=1";
    $result = sql_select($sql);
    foreach ($result as $row) 
    {
        $compostion = '';
        if ($row[csf('yarn_comp_percent2nd')] != 0) {
            $compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
        } else {
            $compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
        }

        $yarn_desc = $row[csf('lot')] . " " . $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion . " " . $yarn_type[$row[csf('yarn_type')]];
        $yarn_desc_array[$row[csf('id')]] = $yarn_desc;
    }

    if($type==2)
    {
        //Finding all po_id, booking_no, program_id, booking_color

        $sql="SELECT a.id as plan_id, a.booking_no, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as prog_id, c.po_break_down_id, c.fabric_color_id as color_number_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_dtls c, wo_po_details_master d where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and company_id=$cbo_company_name $buyer_cond $program_no_cond $program_status_cond $booking_cond $knitting_source_cond $party_cond $date_cond and a.booking_no=c.booking_no and c.status_active=1 and c.is_deleted=0 and c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 $year_field_cond $job_no_cond $order_id_cond group by a.id, a.booking_no, c.po_break_down_id, b.id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, c.fabric_color_id";
        
        foreach (sql_select($sql) as $data) 
        {
           $po_id_arr[$data[csf("po_break_down_id")]]=$data[csf("po_break_down_id")];
           $booking_no_arr[$data[csf("booking_no")]]=$data[csf("booking_no")];
           $prog_id_arr[$data[csf("prog_id")]]=$data[csf("prog_id")];
        }

        //Finding Program qty, yarn_desc id, all_programmed po
        $all_prog_ids=implode(",", $prog_id_arr); 
        $plan_sql="SELECT mst_id, dtls_id, yarn_desc, po_id, program_qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and dtls_id in ($all_prog_ids)";
        foreach (sql_select($plan_sql) as $data) 
        {
            $pre_cost_fabric_cost_dtls_id_arr[$data[csf("mst_id")]][$data[csf("dtls_id")]].= $data[csf("yarn_desc")].",";
            $program_qnty_arr[$data[csf("mst_id")]][$data[csf("dtls_id")]][$data[csf("po_id")]][$data[csf("yarn_desc")]]=$data[csf("program_qnty")];
            $prog_qty_arr[$data[csf("dtls_id")]]["tot_prog_gty"] +=$data[csf("program_qnty")];
            $all_plan_po_ids[$data[csf("po_id")]]=$data[csf("po_id")];
        }
        //Finding Booking informations
        $all_booking_ids="'".implode("','", $booking_no_arr)."'";
        $booking_sql="SELECT a.booking_no_prefix_num, a.booking_no, b.job_no, b.po_break_down_id,b.pre_cost_fabric_cost_dtls_id, a.booking_type, a.item_category, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.grey_fab_qnty, b.fabric_color_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($all_booking_ids)";
        foreach (sql_select($booking_sql) as $data) 
        {
            $booking_arr[$data[csf("booking_no")]]["booking_no"]=$data[csf("booking_no_prefix_num")];
            $booking_qty_arr[$data[csf("booking_no")]][$data[csf("job_no")]][$data[csf("po_break_down_id")]][$data[csf("gsm_weight")]][$data[csf("dia_width")]][$data[csf("fabric_color_id")]]["booking_qty"] +=$data[csf("grey_fab_qnty")];
            $tot_booking_qty_arr[$data[csf("booking_no")]]["booking_qty"] +=$data[csf("grey_fab_qnty")];
            $booking_job_arr[$data[csf("job_no")]]=$data[csf("job_no")];
        }


        //Finding Order Informations
        $all_po_ids=implode(",", $po_id_arr);
        $order_sql="SELECT b.id, b.po_number, b.shiping_status, b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in ($all_po_ids)";
        foreach (sql_select($order_sql) as $data) 
        {
            $order_arr[$data[csf("id")]]["po_number"]=$data[csf("po_number")];
            $order_arr[$data[csf("id")]]["shiping_status"]=$data[csf("shiping_status")];
            $order_arr[$data[csf("id")]]["shipment_date"]=$data[csf("shipment_date")];
        }

        //Finding Requisition Informations
        $requsition_sql="SELECT knit_id as prog_id, requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 and knit_id in ($all_prog_ids)";
        foreach (sql_select($requsition_sql) as $data) 
        {
            $reqstn_arr[$data[csf("prog_id")]]["requ_qty"] += $data[csf("yarn_qnty")];
            $reqstn_arr[$data[csf("prog_id")]]["requ_no"] .= $data[csf("requisition_no")].",";
            $reqstn_arr[$data[csf("prog_id")]]["prod_id"] .= $data[csf("prod_id")].",";
        }

        //Finding_issue_data
        $issue_sql="select b.requisition_no, b.cons_quantity from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=3 and a.entry_form=3 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no in (SELECT distinct(requisition_no) from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 and knit_id in ($all_prog_ids))";
        foreach (sql_select($issue_sql) as $data) 
        {
            $requ_wise_iss[$data[csf("requisition_no")]]["issue_qty"] +=$data[csf("cons_quantity")];
        }

        //Production details
        $sql_production="select a.id, b.id as dtls_id, a.booking_id as prog_id, b.no_of_roll, b.grey_receive_qnty as prod_qty $prod_date_cond from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=2 and a.receive_basis=2 and a.booking_id in ($all_prog_ids)"; 
        foreach (sql_select($sql_production) as $data) 
        {
           $production_data_arr[$data[csf("prog_id")]]["tot_prod_qty"] +=$data[csf("prod_qty")];
           $production_data_arr[$data[csf("prog_id")]]["no_of_roll"] +=$data[csf("no_of_roll")];
           $production_data_arr[$data[csf("prog_id")]]["current_prod_qty"] +=$data[csf("current_prod_qty")];
        }

        //Finding_knitting_charge

        $booking_jobs="'".implode("','", $booking_job_arr)."'";
        $sql_knit_charge="SELECT job_no, fabric_description, charge_unit from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 and job_no in ($booking_jobs)";
        foreach (sql_select($sql_knit_charge) as $data) 
        {
            $knitting_charge_arr[$data[csf("job_no")]][$data[csf("fabric_description")]]=$data[csf("charge_unit")];
        }

        //-----------------------  Making Data Array ----------------------


        $all_planed_po=implode(",", $all_plan_po_ids);
        $prog_check_array=array();
       
        
            $sql_data="SELECT a.id as plan_id, a.booking_no, a.buyer_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as prog_id, b.stitch_length, b.machine_gg, b.machine_dia, b.machine_id, b.status, b.remarks, b.color_id, b.knitting_source, c.po_break_down_id, c.job_no, d.job_no_prefix_num, d.style_ref_no, c.fabric_color_id, a.body_part_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_dtls c, wo_po_details_master d where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.id in ($all_prog_ids) and a.booking_no=c.booking_no and c.status_active=1 and c.is_deleted=0 and c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and c.po_break_down_id in ($all_planed_po) group by a.id, a.booking_no, c.po_break_down_id, c.job_no, d.job_no_prefix_num, a.buyer_id, d.style_ref_no, b.id, b.stitch_length, b.machine_gg, b.machine_dia, b.machine_id, b.status, b.remarks, a.fabric_desc, b.color_id, a.gsm_weight, a.dia, a.width_dia_type,b.knitting_source, c.fabric_color_id, a.body_part_id order by a.id, b.id";

            $temp_arr=array();
            $sql_results=sql_select($sql_data);
            foreach ($sql_results as $value) 
            {
                $booking       =$value[csf("booking_no")];
                $buyer         =$value[csf("buyer_id")];
                $fabric_desc   =$value[csf("fabric_desc")];
                $gsm           =$value[csf("gsm_weight")];
                $dia           =$value[csf("dia")];
                $width_dia_type=$value[csf("width_dia_type")];
                $po_id         =$value[csf("po_break_down_id")];
                $job_no        =$value[csf("job_no")];
                $job_prefix_num=$value[csf("job_no_prefix_num")];
                $style         =$value[csf("style_ref_no")];
                $color         =$value[csf("color_id")];
            
                $yarn_desc_ids=chop($pre_cost_fabric_cost_dtls_id_arr[$value[csf("plan_id")]][$value[csf("prog_id")]],",");

                $booking_color=$value[csf("fabric_color_id")];
  
               if(in_array($booking_color, explode(",", $color)))
               {
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["job"]=$value[csf("job_no_prefix_num")];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["booking_no"]=$booking_arr[$booking]["booking_no"];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["buyer"]=$buyer_arr[$buyer];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["po_num"]=$order_arr[$po_id]["po_number"];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["style"]=$style;
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["Shipping_status"]=$shipment_status[$order_arr[$po_id]["shiping_status"]];
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["fabric_desc"]=$fabric_desc;
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["dia"]=$dia;
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["width_dia_type"]=$fabric_typee[$width_dia_type];

                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["gsm"]=$gsm;
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["color"]=$color;

                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["prog_id"] .=$value[csf("prog_id")].",";

                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["stitch_length"] .=$value[csf("stitch_length")].", ";
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["machine_gg"] .=$value[csf("machine_gg")].", ";
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["machine_no"] .=$machine_arr[$value[csf("machine_id")]].",";
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["machine_dia"] .=$value[csf("machine_dia")].",";
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["status"] .= $knitting_program_status[$value[csf("status")]].","; 
                    $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["body_part_id"] .= $body_part[$value[csf("body_part_id")]]."*";

                    foreach (array_filter(explode(",",$yarn_desc_ids)) as $yarn_desc_id) 
                    {
                       
                        if($value[csf("knitting_source")]==1)
                        {
                            $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["inside_prog_qty"] +=$program_qnty_arr[$value[csf("plan_id")]][$value[csf("prog_id")]][$po_id][$yarn_desc_id];
                        }
                        else
                        {
                            $booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$booking_color]["outside_prog_qty"] +=$program_qnty_arr[$value[csf("plan_id")]][$value[csf("prog_id")]][$po_id][$yarn_desc_id];
                        }   
                    }
                    if(in_array($yarn_desc_id, $temp_arr[$job_no][$fabric_desc])==false)
                    {   
                        $count=1;
                        $knitting_data[$booking][$job_no][$fabric_desc]["knitting_charge"]+=$knitting_charge_arr[$job_no][$yarn_desc_id];
                        $knitting_data[$booking][$job_no][$fabric_desc]["count"]+=$count;
                        $temp_arr[$job_no][$fabric_desc][$yarn_desc_id]=$yarn_desc_id;
                    }
               }
            }//die;
     
        
        //--------------------------------------- Main Grouping Sql ------------------------------------------

        $all_planned_po_ids=implode(",", $all_plan_po_ids);
       

        $sql_main="SELECT a.booking_no, a.buyer_id, a.fabric_desc, a.gsm_weight, c.dia_width as dia, a.width_dia_type, c.po_break_down_id, c.job_no, c.fabric_color_id as color_number_id, d.job_no_prefix_num, d.style_ref_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_dtls c, wo_po_details_master d where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and company_id=$cbo_company_name $buyer_cond $program_no_cond $program_status_cond $booking_cond $knitting_source_cond $party_cond $date_cond and a.booking_no=c.booking_no and c.status_active=1 and c.is_deleted=0 and c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and b.id in ($all_prog_ids) and c.po_break_down_id in ($all_planned_po_ids) $year_field_cond $job_no_cond $order_id_cond and a.gsm_weight=c.gsm_weight and c.dia_width=a.dia group by a.booking_no, c.po_break_down_id, c.job_no, d.job_no_prefix_num, a.buyer_id, d.style_ref_no, a.fabric_desc, a.gsm_weight, c.dia_width, a.width_dia_type, c.fabric_color_id order by c.job_no, c.po_break_down_id";

        $sql_result_main=sql_select($sql_main);
		$width=3200;
        ob_start();
        ?>
        <fieldset style="width:<?= $width;?>px;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" id="table_header_1">
                <thead>
                    <th width="40">SL</th>
                    <th width="70">Priority</th>
                    <th width="100">Company</th>
                    <th width="60">Job No.</th>
                    <th width="70">Booking No.</th>
                    <th width="150">Buyer Name</th>
                    <th width="110">Order No</th>
                    <th width="130">Style Name</th>
                    <th width="150">Body Part</th>
                    <th width="70">Shipment Date</th>
                    <th width="150">Fabrics Design</th>
                    <th width="80">Fin. Dia</th>
                    <th width="80">OP. TU.</th>
                    <th width="80">Fin. Gsm</th>
                    <th width="80">Color</th>
                    <th width="80">Req.Qty</th>
                    <th width="110">Program No.</th>
                    <th width="80">Stitch Length</th>
                    <th width="80">GG</th>
                    <th width="80">M/C No.</th>
                    <th width="80">M/C Dia</th>
                    <th width="100">Program Qty (Inside)</th>
                    <th width="100">Program Qty (Outside)</th>
                    <th width="100">Total Program</th>
                    <th width="80">Requisition No</th>
                    <th width="260">Yarn Details</th>
                    <th width="90">Today Production</th>
                    <th width="90">No of Roll</th>
                    <th width="90">Total Production</th>
                    <th width="100">Balance</th>
                    <th width="100">Remarks</th>
                    <th>Program Status</th>
                </thead>
            </table>
            <div style="width:<?= $width+18;?>px; overflow-y:scroll; max-height:350px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1; $k=1; $po_exist_arr=array();
                            foreach ($sql_result_main as $value) 
                            {
                                $booking       =$value[csf("booking_no")];
                                $buyer         =$value[csf("buyer_id")];
                                $fabric_desc   =$value[csf("fabric_desc")];
                                $gsm           =$value[csf("gsm_weight")];
                                $dia           =$value[csf("dia")];
                                $width_dia_type=$value[csf("width_dia_type")];
                                $po_id         =$value[csf("po_break_down_id")];
                                $job_no        =$value[csf("job_no")];
                                $job_prefix_num=$value[csf("job_no_prefix_num")];
                                $style         =$value[csf("style_ref_no")];
                                $color_id      =$value[csf("color_number_id")];

                                if ($i % 2 == 0){ $bgcolor = "#E9F3FF"; }
                                else{ $bgcolor = "#FFFFFF"; }

                               
                                $color_name = $color_library[$color_id];
                                $program_no=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["prog_id"];
                                $program_status=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["status"];
                                $body_part_id=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["body_part_id"];
                                $stitch_length=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["stitch_length"];
                                $machine_gg=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["machine_gg"];
                                $machine_no=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["machine_no"];
                                $machine_dia=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["machine_dia"];
                                $inside_prog_qty=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["inside_prog_qty"];
                                $outside_prog_qty=$booking_data[$booking][$fabric_desc][$gsm][$dia][$width_dia_type][$po_id][$color_id]["outside_prog_qty"];
                                $booking_qty=$booking_qty_arr[$booking][$job_no][$po_id][$gsm][$dia][$color_id]["booking_qty"];
                                $tot_booking_qty=$tot_booking_qty_arr[$booking]["booking_qty"];
                                $knitting_charge=$knitting_data[$booking][$job_no][$fabric_desc]["knitting_charge"]/$knitting_data[$booking][$job_no][$fabric_desc]["count"];

                                $requisition_no=""; $requisition_qty=0; $issue_qty=0; $no_of_roll=0; $tot_prod_qty=0; $today_prod_qty=0;$prod_id_for_yarn_desc="";
                                foreach (array_filter(explode(",", $program_no)) as $prog) 
                                {
                                    $requisition_no .=implode(",", array_unique(explode(",", chop($reqstn_arr[$prog]["requ_no"],",")))).",";
                                    $requisition_qty +=$reqstn_arr[$prog]["requ_qty"];

                                    $tot_prod_qty += $production_data_arr[$prog]["tot_prod_qty"];
                                    $no_of_roll += $production_data_arr[$prog]["no_of_roll"];
                                    $today_prod_qty += $production_data_arr[$prog]["current_prod_qty"];

                                    $prod_id_for_yarn_desc .=implode(",", array_unique(explode(",", chop($reqstn_arr[$prog]["prod_id"],",")))).",";
                                }
                                $requisition_no=chop($requisition_no,",");

                                foreach (array_filter(explode(",", $requisition_no)) as $req) 
                                {
                                    $issue_qty += $requ_wise_iss[$req]["issue_qty"];
                                }

                                $yarn_desc = '';$prod_id_for_yarn_arr=array();
                                $prod_id_for_yarn_desc=chop($prod_id_for_yarn_desc,",");
                                $prod_id_for_yarn_arr = array_filter(array_unique(explode(",", $prod_id_for_yarn_desc)));
                                foreach ($prod_id_for_yarn_arr as $product_id) 
                                {
                                    if ($yarn_desc == '') $yarn_desc = $yarn_desc_array[$product_id]; 
                                    else $yarn_desc .= ", " . $yarn_desc_array[$product_id];
                                }

                                if (!$po_exist_arr[$po_id]) 
                                {   
                                    $po_exist_arr[$po_id]=$po_id;
                                    if($k>1)
                                    {
                                        ?>
                                        <tr> <td colspan="38" style="background-color: #E6E6E6">&nbsp; </td> </tr>
                                        <?
                                    }
                                    $k++;
                                }

                                $program_status = implode(",",array_filter(array_unique(explode(",", $program_status))));
                                $body_part_id = implode(",",array_filter(array_unique(explode("*", $body_part_id))));
                                ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" >
                                        <td width="40" align="center"><? echo $i; ?></td>
                                        <td width="70" align="center"> </td>
                                        <td width="100" align="center"><? echo $company_short_arr[$cbo_company_name]; ?></td>
                                        <td width="60" align="center"> <? echo $job_prefix_num; ?> </td>
                                        <td width="70" align="center" title="<? echo $booking; ?>"> 
                                            <? echo $booking_arr[$booking]["booking_no"]; ?> 
                                        </td>
                                        <td width="150" align="center"> <? echo $buyer_arr[$buyer]; ?> </td>
                                        <td width="110" align="center" style="word-wrap: break-word;word-break: break-all;"> 
                                            <? echo $order_arr[$po_id]["po_number"]; ?> 
                                        </td>
                                        <td width="130" align="center"> <? echo $style; ?> </td>
                                        <td width="150" align="center"> <p><? echo $body_part_id; ?></p> </td>
                                        
                                        <td width="70" align="center"><? echo change_date_format($order_arr[$po_id]["shipment_date"]); ?></td>
                                        
                                        <td width="150" align="center" style="word-wrap: break-word;word-break: break-all;"> 
                                            <? echo $fabric_desc; ?>
                                        </td>
                                        <td width="80" align="center"> <? echo $dia; ?></td>
                                        <td width="80" align="center"> <? echo $fabric_typee[$width_dia_type]; ?> </td>
                                        <td width="80" align="center"> <? echo $gsm; ?> </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;">  
                                            <? echo $color_name; ?>
                                        </td>
                                        <td width="80" align="right"> <? echo number_format($booking_qty, 2); ?> </td>

                                        <td width="110" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($program_no,","); ?> 
                                        </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($stitch_length,", "); ?> 
                                        </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($machine_gg,", "); ?> 
                                        </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($machine_no,","); ?> 
                                        </td>
                                        <td width="80" align="center" style="word-break: break-all;word-wrap: break-word;"> 
                                            <? echo chop($machine_dia,","); ?> 
                                        </td>
                                        <td width="100" align="right">
                                            <? echo number_format($inside_prog_qty,2); ?>
                                        </td>
                                        <td width="100" align="right">
                                            <?  echo number_format($outside_prog_qty,2);  ?>
                                        </td>
                                        <td width="100" align="right"> 
                                            <? 
                                                $tot_prog_qty=$inside_prog_qty+$outside_prog_qty;
                                                echo number_format($tot_prog_qty,2); 
                                            ?>
                                        </td>
                                        <td width="80" align="center" style="word-wrap: break-word; word-break: break-all;"> 
                                            <? echo $requisition_no; ?> 
                                        </td>
                                        <td width="260" align="center" style="word-wrap: break-word; word-break: break-all;">
                                            <? echo $yarn_desc; ?>
                                        </td>
                                        
                                        <td width="90" align="right"> <? echo number_format($today_prod_qty,2); ?> </td>
                                        <td width="90" align="center"><? echo $no_of_roll; ?></td>
                                        <td width="90" align="right"><? echo number_format($tot_prod_qty,2); ?></td>
                                        <td width="100" align="right">
                                            <?
                                                $prod_balance=$tot_prog_qty-$tot_prod_qty;
                                                echo number_format($prod_balance,2);
                                            ?>
                                        </td>
                                      
                                        <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"></td>
                                        <td align="center"><? echo $program_status;?></td>
                                    </tr>

                                    <?
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>

        </fieldset>
        <?
    }
    exit();
}



?>