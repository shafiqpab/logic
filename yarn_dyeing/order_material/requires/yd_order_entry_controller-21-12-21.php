<?php
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
$color_arr = return_library_array("select id,color_name from lib_color", "id", "color_name");
$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
$brand_arr = return_library_array("Select id, brand_name from  lib_brand where  status_active=1", 'id', 'brand_name');
$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();
}



if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
    //company+'_'+1
	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//$company_cond 
	if($data[1]==1)
	{
		//echo  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 



if ($action=="job_popup")
{
    echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
    ?>
    <script>
        function js_set_value(id)
        { 
            $("#hidden_mst_id").val(id);
            document.getElementById('selected_job').value=id;
            parent.emailwindow.hide();
        }
        
        function fnc_load_party_popup(type,within_group)
        {
            var company = $('#cbo_company_name').val();
            var party_name = $('#cbo_party_name').val();
            var location_name = $('#cbo_location_name').val();
            load_drop_down( 'yd_order_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
        }
        function search_by(val)
        {
            $('#txt_search_string').val('');
            if(val==1 || val==0) $('#search_by_td').html('Dyeing Job No');
            else if(val==2) $('#search_by_td').html('W/O No');
            else if(val==3) $('#search_by_td').html('Buyer Job');
            else if(val==4) $('#search_by_td').html('Buyer Po');
            else if(val==5) $('#search_by_td').html('Buyer Style');
        }
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>                 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100" style="display: none;">Search By</th>
                    <th width="100" id="search_by_td">YD Job No</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 1, "-- Select --", '', "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <?
                        //select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name 
                        echo create_drop_down( "cbo_party_name", 150,$blank_array,"", 1, "-- Select Party --",'', "" );      
                        ?>
                    </td>
                    <td style="display: none;">
                        <?
                            $search_by_arr=array(1=>"Yarn Dyeing Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_yd_search_list_view', 'search_div', 'yd_order_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_yd_search_list_view")
{
    $contact_person = return_library_array("select id, contact_person from lib_supplier", 'id', 'contact_person');
    $ex_data    = explode("_", $data);
    $company    = $ex_data[0];
    $party      = $ex_data[1];
    $fromDate   = $ex_data[2];
    $toDate     = $ex_data[3];
    $withinGroup = $ex_data[4];

    $sql_cond='';
    if($company!=0) $sql_cond.=" and a.company_id=$company"; else { echo "Please Select Company First."; die; }
    if($withinGroup != 0) $sql_cond.= " and a.within_group=$withinGroup"; else $sql_cond='';
    if($party != 0) $sql_cond.= " and a.party_id='$party'";
    if($db_type==0){ 
        if ($fromDate!="" &&  $toDate!="") $sql_cond .= "and a.receive_date between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
        $ins_year_cond="year(a.insert_date)";
    }else{
        if ($fromDate!="" &&  $toDate!="") $sql_cond .= "and a.receive_date between '".change_date_format($fromDate, "", "",1)."' and '".change_date_format($toDate, "", "",1)."'";
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
    $sql= "select a.id, a.yd_job, a.within_group, $ins_year_cond as year, a.party_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type from yd_ord_mst a where a.entry_form=374 $sql_cond  order by a.id DESC";
    $data_array=sql_select($sql);
    ?>
    <div style="width:730px;" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150">Job no</th>
                <th width="150">WO No.</th>
                <th width="150">Party</th>
                <th width="80">Ord. Receive Date</th>
                <th>Delevary Date</th>
            </thead>
        </table>
        <div style="width:730px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="710" class="rpt_table"
            id="tbl_list_search">
            <?
            $i = 1;
            foreach ($data_array as $selectResult)
            {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('yd_job')]; ?>'+'_'+'<? echo $selectResult[csf('within_group')]; ?>'+'_'+'<? echo $selectResult[csf('order_no')]; ?>'); ">
                    <td width="30" align="center"><p><? echo $i; ?></p></td>
                    <td width="150" align="center"><p> <? echo $selectResult[csf('yd_job')]; ?></p></td>
                    <td width="150" align="center"><p> <? echo $selectResult[csf('order_no')]; ?></p></td>
                    <td width="150" align="center"><p> <? echo $company_library[$selectResult[csf('party_id')]]; ?></p></td>
                    <td width="80"><p><? echo change_date_format($selectResult[csf('receive_date')]); ?></p></td>
                    <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        </div>
    </div>
    <? exit();
}

if ($action == "order_popup") 
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
    extract($_REQUEST);

    /*if ($db_type == 0) $select_field_grp = "group by a.id order by supplier_name";
    else if ($db_type == 2) $select_field_grp = "group by a.id,a.supplier_name order by supplier_name";

    $current_date = date('d-m-Y');
    $previous_day = date("d-m-Y", strtotime(date("d-m-Y") . '-60 days'));*/
    ?>

    <script>
        /*function set_checkvalue() {
            if (document.getElementById('chk_job_wo_po').value == 0)
                document.getElementById('chk_job_wo_po').value = 1;
            else
                document.getElementById('chk_job_wo_po').value = 0;
        }*/

        function js_set_value(id) {
            $("#hidden_sys_number").val(id);
            //$("#hidden_id").val(id);
            parent.emailwindow.hide();
        }
        var permission = '<? echo $permission; ?>';
    </script>
</head>
<body>
    <div style="width:900px;" align="center">
        <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th colspan="2"></th>
                        <th>
                            <?
                            echo create_drop_down("cbo_search_category", 130, $string_search_type, '', 1, "-- Search Catagory --");
                            ?>
                        </th>
                        <th colspan="2"></th>
                        <th colspan="2" style="text-align:right; display: none"><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">WO Without Job
                        </th>
                    </tr>
                    <tr>
                        <th width="170">Supplier Name</th>
                        <th width="100">Booking No</th>
                        <th width="100">Sales Order No</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"/>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?
                            echo create_drop_down("cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and c.tag_company=$company and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected, "", 0);
                            ?>
                        </td>
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"/>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date"/>
                        </td>
                        <td >
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company; ?>'+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_sys_search_list_view', 'search_div', 'yd_order_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="middle" colspan="5">
                            <? echo load_month_buttons(1); ?>
                            <input type="hidden" id="hidden_sys_number" value="hidden_sys_number"/>
                            <input type="hidden" id="hidden_id" value="hidden_id"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div id="search_div"></div>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_sys_search_list_view_old")
{
    $contact_person = return_library_array("select id, contact_person from lib_supplier", 'id', 'contact_person');
    $ex_data = explode("_", $data);
    $supplier = $ex_data[0];
    $fromDate = $ex_data[1];
    $toDate = $ex_data[2];
    $company = $ex_data[3];
    //$buyer_val=$ex_data[4];
    $chk_job_wo_po = trim($ex_data[8]);

    if ($supplier != 0) $supplier = "and a.supplier_id='$supplier'"; else  $supplier = "";
    if ($company != 0) $company = " and a.company_id='$company'"; else  $company = "";
    if ($buyer_val != 0) $buyer_cond = "and d.buyer_name='$buyer_val'"; else  $buyer_cond = "";
    if ($db_type == 0) {
        $booking_year_cond = " and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[7]";
        $year_cond = " and SUBSTRING_INDEX(d.insert_date, '-', 1)=$ex_data[7]";
        if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
    }
    if ($db_type == 2) {
        $booking_year_cond = " and to_char(a.insert_date,'YYYY')=$ex_data[7]";
        $year_cond = " and to_char(d.insert_date,'YYYY')=$ex_data[7]";
        if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'mm-dd-yyyy', '/', 1) . "' and '" . change_date_format($toDate, 'mm-dd-yyyy', '/', 1) . "'";
    }

    if ($ex_data[4] == 4 || $ex_data[4] == 0) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[6]%' $year_cond "; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[5]%'  $booking_year_cond  "; else $booking_cond = "";
    }
    if ($ex_data[4] == 1) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num ='$ex_data[6]' "; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num ='$ex_data[5]'   "; else $booking_cond = "";
    }
    if ($ex_data[4] == 2) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '$ex_data[6]%'  $year_cond"; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '$ex_data[5]%'  $booking_year_cond  "; else $booking_cond = "";
    }
    if ($ex_data[4] == 3) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[6]'  $year_cond"; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[5]'  $booking_year_cond  "; else $booking_cond = "";
    }

    if ($db_type == 0) $select_year = "year(a.insert_date) as year"; else $select_year = "to_char(a.insert_date,'YYYY') as year";
    if ($chk_job_wo_po == 1) {
        $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number  
        from wo_yarn_dyeing_mst a
        where a.status_active=1 and a.is_deleted=0 and a.entry_form=135 and a.id not in(select mst_id from wo_yarn_dyeing_dtls where job_no_id>0 and entry_form=135  and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
    } else {

        $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id
            from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b 
            where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=41 and b.entry_form=41 $company $supplier $sql_cond  $buyer_cond $job_cond $booking_cond
            group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date order by a.id DESC";


       
       /*if ($db_type == 0) {
            $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id, group_concat(distinct b.job_no) as job_no,d.buyer_name, group_concat(distinct e.po_number) as po_number,d.within_group  
            from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst d
            where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=135 and b.entry_form=135 $company $supplier $sql_cond  $buyer_cond $job_cond $booking_cond
            group by a.id order by a.id DESC";
        } //LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
        else if ($db_type == 2) {
            $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id,d.job_no as sales_job,d.buyer_id,d.within_group
            from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst d
            where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.entry_form=135 and b.entry_form=135  $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond
            group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.job_no,d.buyer_id,d.within_group order by a.id DESC";
        }*/
        //echo $sql;
        $nameArray = sql_select($sql);
        $all_job_id = "";
        foreach ($nameArray as $row) {
            $all_job_id .= $row[csf("job_no_id")] . ",";
            $sales_no_arr[$row[csf("job_no_id")]] = $row[csf("job_no_id")];
        }
        //echo $all_job_id;die;
        $all_job_id = array_chunk(array_unique(explode(",", chop($all_job_id, ","))), 999);

        $po_sql = "select p.mst_id as mst_id, b.id, b.po_number from wo_yarn_dyeing_dtls p, fabric_sales_order_mst a, wo_po_break_down b where p.job_no_id=a.id and a.job_no=b.job_no_mst";
        $p = 1;
        foreach ($all_job_id as $job_id) {
            //$po_sql
            if ($p == 1) $po_sql .= " and (a.id in(" . implode(',', $job_id) . ")"; else $po_sql .= " or a.id in(" . implode(',', $job_id) . ")";
            $p++;
        }
        $po_sql .= ")";

        //echo $po_sql;die;

        $po_result = sql_select($po_sql);
        $po_data = array();
        foreach ($po_result as $row) {
            $po_data[$row[csf("mst_id")]] .= $row[csf("po_number")] . ",";
        }
    }
    $supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
    ?>
    <div style="width:930px;" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Booking no Prefix</th>
                <th width="40">Year</th>
                <th width="150">Booking No</th>
                <th width="200">Sales Order No</th>
                <th width="150">Supplier Name</th>
                <th width="100">Booking Date</th>
                <th>Delevary Date</th>
            </thead>
        </table>
        <div style="width:930px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table"
            id="tbl_list_search">
            <?

            $i = 1;
            //$nameArray = sql_select($sql);
                //var_dump($nameArray);die;
            foreach ($nameArray as $selectResult)
            {
                $job_no = implode(",", array_unique(explode(",", $selectResult[csf("job_no")])));
                $job_no_id = implode(",", array_unique(explode(",", $selectResult[csf("job_no_id")])));
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+'<? echo $selectResult[csf('within_group')]; ?>'); ">

                    <td width="30" align="center"><p><? echo $i; ?></p></td>
                    <td width="60" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
                    <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
                    <td width="150"><p><? echo $selectResult[csf("ydw_no")]; ?></p></td>
                    <td width="200" style="word-break:break-all"> <? echo $job_no ;//$selectResult[csf('sales_job')]; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?></td>
                    <td width="100"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
                    <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        </div>
    </div>
    <? exit();
}


if ($action == "create_sys_search_list_view")
{
    $contact_person = return_library_array("select id, contact_person from lib_supplier", 'id', 'contact_person');
    $ex_data = explode("_", $data);
    $supplier = $ex_data[0];
    $fromDate = $ex_data[1];
    $toDate = $ex_data[2];
    $company = $ex_data[3];
    //$buyer_val=$ex_data[4];
    $chk_job_wo_po = trim($ex_data[8]);

    if ($supplier != 0) $supplier = "and a.supplier_id='$supplier'"; else  $supplier = "";
    if ($company != 0) $company = " and a.company_id='$company'"; else  $company = "";
    if ($buyer_val != 0) $buyer_cond = "and d.buyer_name='$buyer_val'"; else  $buyer_cond = "";
    if ($db_type == 0) {
        $booking_year_cond = " and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[7]";
        $year_cond = " and SUBSTRING_INDEX(d.insert_date, '-', 1)=$ex_data[7]";
        if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
    }
    if ($db_type == 2) {
        $booking_year_cond = " and to_char(a.insert_date,'YYYY')=$ex_data[7]";
        $year_cond = " and to_char(d.insert_date,'YYYY')=$ex_data[7]";
        if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'mm-dd-yyyy', '/', 1) . "' and '" . change_date_format($toDate, 'mm-dd-yyyy', '/', 1) . "'";
    }

    if ($ex_data[4] == 4 || $ex_data[4] == 0) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[6]%' $year_cond "; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[5]%'  $booking_year_cond  "; else $booking_cond = "";
    }
    if ($ex_data[4] == 1) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num ='$ex_data[6]' "; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num ='$ex_data[5]'   "; else $booking_cond = "";
    }
    if ($ex_data[4] == 2) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '$ex_data[6]%'  $year_cond"; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '$ex_data[5]%'  $booking_year_cond  "; else $booking_cond = "";
    }
    if ($ex_data[4] == 3) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[6]'  $year_cond"; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[5]'  $booking_year_cond  "; else $booking_cond = "";
    }

    if ($db_type == 0) $select_year = "year(a.insert_date) as year"; else $select_year = "to_char(a.insert_date,'YYYY') as year";
    if ($chk_job_wo_po == 1) {
        $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number  
        from wo_yarn_dyeing_mst a
        where a.status_active=1 and a.is_deleted=0 and a.entry_form=135 and a.id not in(select mst_id from wo_yarn_dyeing_dtls where job_no_id>0 and entry_form=135  and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
    } else {
        if ($db_type == 0) {
            $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id, group_concat(distinct b.job_no) as job_no,d.buyer_name, group_concat(distinct e.po_number) as po_number,d.within_group  
            from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst d
            where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=135 and b.entry_form=135 $company $supplier  $sql_cond  $buyer_cond $job_cond $booking_cond
            group by a.id order by a.id DESC";
        } //LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
        else if ($db_type == 2) {
            $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id,d.job_no as sales_job,d.buyer_id,d.within_group
            from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst d
            where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.entry_form=135 and b.entry_form=135 $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond
            group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.job_no,d.buyer_id,d.within_group order by a.id DESC";
        }
        $nameArray = sql_select($sql);
        $all_job_id = "";
        foreach ($nameArray as $row) {
            $all_job_id .= $row[csf("job_no_id")] . ",";
            $sales_no_arr[$row[csf("job_no_id")]] = $row[csf("job_no_id")];
        }
        //echo $all_job_id;die;
        $all_job_id = array_chunk(array_unique(explode(",", chop($all_job_id, ","))), 999);

        $po_sql = "select p.mst_id as mst_id, b.id, b.po_number from wo_yarn_dyeing_dtls p, fabric_sales_order_mst a, wo_po_break_down b where p.job_no_id=a.id and a.job_no=b.job_no_mst";
        $p = 1;
        foreach ($all_job_id as $job_id) {
            //$po_sql
            if ($p == 1) $po_sql .= " and (a.id in(" . implode(',', $job_id) . ")"; else $po_sql .= " or a.id in(" . implode(',', $job_id) . ")";
            $p++;
        }
        $po_sql .= ")";

        //echo $po_sql;die;

        $po_result = sql_select($po_sql);
        $po_data = array();
        foreach ($po_result as $row) {
            $po_data[$row[csf("mst_id")]] .= $row[csf("po_number")] . ",";
        }
    }

    ?>
    <div style="width:930px;" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Booking no Prefix</th>
                <th width="40">Year</th>
                <th width="120">Booking No</th>
                <th width="200">Sales Order No</th>
                <th width="130">PO Company</th>
                <th width="130">Supplier Name</th>
                <th width="70">Booking Date</th>
                <th>Delevary Date</th>
            </thead>
        </table>
        <div style="width:930px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table"
            id="tbl_list_search">
            <?

            $i = 1;
            $nameArray = sql_select($sql);
                //var_dump($nameArray);die;
            foreach ($nameArray as $selectResult)
            {
                $job_no = implode(",", array_unique(explode(",", $selectResult[csf("job_no")])));
                $job_no_id = implode(",", array_unique(explode(",", $selectResult[csf("job_no_id")])));
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                $supplier_or_company="";
                if($selectResult[csf("pay_mode")]==3 || $selectResult[csf("pay_mode")]==5) $supplier_or_company=$company_library[$selectResult[csf("supplier_id")]];
                else $supplier_or_company=$supplier_arr[$row[csf("supplier_id")]];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+'<? echo $selectResult[csf('within_group')]; ?>'); ">

                    <td width="30" align="center"><p><? echo $i; ?></p></td>
                    <td width="60" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
                    <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $selectResult[csf("ydw_no")]; ?></p></td>
                    <td width="200" style="word-break:break-all"> <? echo $selectResult[csf('sales_job')]; ?></td>
                    <td width="130"><p> <? echo $company_library[$selectResult[csf('buyer_id')]]; ?></p></td>
                    <td width="130" style="word-break:break-all"><?=$supplier_or_company; ?></td>
                    <td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
                    <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        </div>
    </div>
    <? exit();
}

if ($action=="populate_master_from_data")
{
    //echo $action."nazim"; die;
    $data=explode('_',$data);
    /*$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );*/
    //echo "select id, ydw_no, supplier_id, company_id, currency, booking_without_order from  wo_yarn_dyeing_mst where and status_active=1 and is_deleted=0 and id='$data[0]'"; 
    $nameArray=sql_select( "select id, ydw_no, supplier_id, company_id, currency, booking_without_order from  wo_yarn_dyeing_mst where status_active=1 and is_deleted=0 and id='$data[0]'" );
    $booking_type=1;
    foreach ($nameArray as $row)
    {   
        echo "document.getElementById('txt_order_no').value         = '".$row[csf("ydw_no")]."';\n";  
        
        echo "document.getElementById('hid_order_id').value         = '".$row[csf("id")]."';\n";
        echo "document.getElementById('hid_is_without_order').value = '".$row[csf("booking_without_order")]."';\n";
        echo "document.getElementById('cbo_party_name').value       = '".$row[csf("supplier_id")]."';\n";
        echo "document.getElementById('cbo_currency').value         = '".$row[csf("currency")]."';\n";
        echo "document.getElementById('hid_booking_type').value     = '".$booking_type."';\n";
    }
    exit(); 
}

if ($action=="populate_job_master_from_data")
{
    //echo $action."nazim"; die;
    //$data=explode('_',$data);
    $sql="select id, entry_form, yd_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no, booking_without_order, booking_type, remarks from  yd_ord_mst where entry_form=374 and status_active=1 and is_deleted=0 and id='$data'";
    //echo $sql;
    $nameArray=sql_select( $sql );

    //$data_array="(".$id.", 374, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_order_receive_date."', '".$txt_delivery_date."','".$txt_rec_start_date."','".$txt_rec_end_date."', '".$hid_order_id."', '".$txt_order_no."', '".$is_without_order."','".$hid_booking_type."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

    foreach ($nameArray as $row)
    {   
        echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
        echo "document.getElementById('txt_job_no').value           = '".$row[csf("yd_job")]."';\n";
        echo "document.getElementById('hid_order_id').value         = '".$row[csf("order_id")]."';\n";
        echo "document.getElementById('txt_order_no').value         = '".$row[csf("order_no")]."';\n";

        echo "document.getElementById('cbo_company_name').value     = '".$row[csf("company_id")]."';\n";  
        echo "fnc_load_party(1,".$row[csf("within_group")].");\n";  
        echo "document.getElementById('cbo_location_name').value    = '".$row[csf("location_id")]."';\n";
        echo "document.getElementById('cbo_party_name').value       = '".$row[csf("party_id")]."';\n";
        echo "fnc_load_party(2,".$row[csf("within_group")].");\n";   
        echo "document.getElementById('cbo_party_location').value   = '".$row[csf("party_location")]."';\n";

        echo "document.getElementById('txt_order_receive_date').value   = '".change_date_format($row[csf("receive_date")])."';\n"; 
        echo "document.getElementById('txt_delivery_date').value        = '".change_date_format($row[csf("delivery_date")])."';\n"; 
        echo "document.getElementById('txt_rec_start_date').value       = '".change_date_format($row[csf("rec_start_date")])."';\n"; 
        echo "document.getElementById('txt_rec_end_date').value         = '".change_date_format($row[csf("rec_end_date")])."';\n"; 

        echo "document.getElementById('cbo_currency').value         = '".$row[csf("currency_id")]."';\n";
        echo "document.getElementById('hid_is_without_order').value = '".$row[csf("booking_without_order")]."';\n";
        echo "document.getElementById('hid_booking_type').value     = '".$row[csf("booking_type")]."';\n";
        echo "$('#cbo_company_name').attr('disabled','true')".";\n";
        echo "$('#within_group').attr('disabled','true')".";\n";
        echo "$('#txt_delivery_date').attr('disabled','true')".";\n";
        echo "$('#txt_order_no').attr('disabled','true')".";\n";
        echo "$('#cbo_party_name').attr('disabled','true')".";\n";
        echo "$('#cbo_party_location').attr('disabled','true')".";\n";
        echo "$('#txt_order_receive_date').attr('disabled','true')".";\n";
    }
    exit(); 
}

if( $action=='order_dtls_list_view' ) 
{
    //echo $data; die;2_FAL-TOR-19-00092_1_3895
    $data=explode('_',$data);
    $operationMood=$data[0];
    $id=$data[1];
    $job_no=$data[2];
    $within_group=$data[3];
    $order_no=$data[4];

    // $sqlss = "select id, color_name from lib_color";
    //$data_arrayss=sql_select($sqlss);
    
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    //echo "<pre>";print_r($color_arr); die;
    if($operationMood==1){
        $sql = "select a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type,b.available_qnty,b.lot  from wo_yarn_dyeing_dtls a,product_details_master b where a.product_id=b.id and a.mst_id='$id'";
    }else{
        $sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, style_ref, sales_order_no, sales_order_id, product_id, process_id, lot, count_id, yarn_type_id, yarn_composition_id, item_color_id, yd_color_id, csp, no_bag, cone_per_bag, no_cone, avg_wgt, uom, order_quantity,  rate, amount, process_loss, total_order_quantity from yd_ord_dtls where mst_id='$id' and status_active=1 and is_deleted=0";
    }
    //echo $sql;
    $data_array=sql_select($sql); $del_date_arr=array();
    //print_r($data_array);
    $ind=0;
    
    $readonlySts='readonly';
    $disableSts='disabled';
    foreach($data_array as $row)
    {
        if($operationMood==1){
            $styleRef='';
            $saleOrderNo=$row[csf("job_no")];
            $saleOrderID=$row[csf("job_no_id")];
            $prodID=$row[csf("product_id")];
            $process='';
            $lot=$row[csf("lot")];
            $count=$row[csf("count")];
            $yarnType=$row[csf("yarn_type")];
            $yarn_composition=$row[csf("yarn_comp_type1st")];
            $itemColor=$row[csf("color_range")];
            $yarn_color=$row[csf("yarn_color")];
            $csp='';
            $no_of_bag=$row[csf("no_of_bag")];
            $no_of_cone=$row[csf("no_of_cone")];
            $coneBag='';
            $avg='';
            $uomId=$row[csf("uom")];
            $orderQty=$row[csf("yarn_wo_qty")];
            $amount=$row[csf("amount")];
            $rate=$amount/$orderQty;
            $processLoss=0;
            $totalQty=$orderQty;
            $hdnDtlsUpdateId='';
        }
        else
        {
            $styleRef=$row[csf("style_ref")];
            $saleOrderNo=$row[csf("sales_order_no")];
            $saleOrderID=$row[csf("sales_order_id")];
            $prodID=$row[csf("product_id")];
            $process=$row[csf("process_id")];
            $lot=$row[csf("lot")];
            $count=$row[csf("count_id")];
            $yarnType=$row[csf("yarn_type_id")];
            $yarn_composition=$row[csf("yarn_composition_id")];
            $itemColor=$row[csf("item_color_id")];
            $yarn_color=$row[csf("yd_color_id")];
            $csp=$row[csf("csp")];
            $no_of_bag=$row[csf("no_bag")];
            $no_of_cone=$row[csf("no_cone")];
            $coneBag=$row[csf("cone_per_bag")];
            $avg=$row[csf("avg_wgt")];
            $uomId=$row[csf("uom")];
            $orderQty=$row[csf("order_quantity")];
            $amount=$row[csf("amount")];
            $rate=$amount/$orderQty;
            $processLoss=$row[csf("process_loss")];
            $totalQty=($orderQty/100)+$orderQty+$processLoss;
            $hdnDtlsUpdateId=$row[csf("id")];
        }
        $tblRow++;
        //$dtls_id=0; $order_uom=0; $wo_qnty=0; $disabled_conv=''; 
        //$yarn_descirption = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%";  
        ?>
        <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
            <td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" type="text" value="" class="text_boxes" style="width:80px" value="<? echo $styleRef; ?>" <? echo $disableSts; ?> /></td>

            <td><input id="txtsaleOrder_<? echo $tblRow; ?>" name="txtsaleOrder[]" type="text" value="<? echo $saleOrderNo; ?>" class="text_boxes_numeric" style="width:80px" placeholder=""/>
            <input id="txtsaleOrderID_<? echo $tblRow; ?>" name="txtsaleOrderID[]" type="hidden" value="<? echo $saleOrderID; ?>" class="text_boxes_numeric" style="width:80px" placeholder=""/>
            <input id="txtProductID_<? echo $tblRow; ?>" name="txtProductID[]" type="hidden" value="<? echo $prodID; ?>" class="text_boxes_numeric" style="width:80px" placeholder=""/></td>
            <td><? 
                echo create_drop_down( "txtprocess_".$tblRow, 100, $yarn_dyeing_process,"", 1, "-- Select --",$process,"",0,'','','','','','',"txtprocess[]"); ?></td> 
            <td><input id="txtlot_<? echo $tblRow; ?>" name="txtlot[]" type="text" class="text_boxes" style="width:80px" value="<? echo $lot; ?>" /></td>
            
            <td id="count_td">
               <? echo create_drop_down( "cboCount_".$tblRow, 80, "select distinct(b.id) as id,b.yarn_count from fabric_sales_order_yarn_dtls a, lib_yarn_count b where  a.yarn_count_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id,yarn_count", 1, "-- Select --",$count,"",0,'','','','','','',"cboCount[]"); ?>
            </td>
            
            <td id="yarn_type_td">
               <? echo create_drop_down( "cboYarnType_".$tblRow, 80, $yarn_type,"", 1, "-- Select --",$yarnType,"",0,'','','','','','',"cboYarnType[]"); ?>
            </td>
            <td align="center" id="composition_td"><? echo create_drop_down( "cboComposition_".$tblRow, 80, $composition,"", 1, "-- Select --",$yarn_composition,"",0,'','','','','','',"cboComposition[]"); ?></td> 
            <td id="item_color_td">
                <input id="txtItemColor_<? echo $tblRow; ?>" type="text"  name="txtItemColor[]" class="text_boxes" style="width:70px" value="<? echo $color_arr[$itemColor]; ?>"/>
                <input id="txtItemColorID_<? echo $tblRow; ?>" type="hidden"  name="txtItemColorID[]" class="text_boxes" style="width:70px" value="<? echo $itemColor; ?>"/>
            </td>
            <td id="color_td">
                <input id="txtYarnColor_<? echo $tblRow; ?>" type="text"  name="txtYarnColor[]" class="text_boxes" style="width:70px" value="<? echo $color_arr[$yarn_color]; ?>"/>
                <input id="txtYarnColorID_<? echo $tblRow; ?>" type="hidden"  name="txtYarnColorID[]" class="text_boxes" style="width:70px" value="<? echo $yarn_color; ?>"/>
            </td>
            <td><input id="txtCSP_<? echo $tblRow; ?>" name="txtCSP[]" type="text" class="text_boxes_numeric" style="width:70px" value="<? echo $csp; ?>" /></td>
            <td><input name="txtnoBag[]" id="txtnoBag_<? echo $tblRow; ?>" type="text" class="text_boxes_numeric" style="width:70px" value="<? echo $no_of_bag; ?>" readonly="readonly"/></td>
            <td><input name="txtConeBag[]" id="txtConeBag_<? echo $tblRow; ?>" type="text" value="" class="text_boxes_numeric" style="width:50px" value="<? echo $coneBag; ?>" /></td>
            <td><input name="txtNoCone[]" id="txtNoCone_<? echo $tblRow; ?>" type="text" class="text_boxes_numeric" style="width:50px" value="<? echo $no_of_cone; ?>"  /></td>
            <td><input name="txtAVG[]" id="txtAVG_<? echo $tblRow; ?>" type="text" value=""  class="text_boxes_numeric" style="width:50px" value="<? echo $avg; ?>" /></td>
            <td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$uomId,"", 1,'','','','','','',"cboUom[]"); ?></td>
            <td><input name="txtOrderqty[]" id="txtOrderqty_<? echo $tblRow; ?>" type="text" style="width:50px"  class="text_boxes_numeric" value="<? echo $orderQty; ?>" onKeyUp="sum_total_qnty(<? echo $tblRow;?>);" readonly="readonly" /></td> 
            <td><input name="txtRate[]" id="txtRate_<? echo $tblRow; ?>" type="text" style="width:50px"  class="text_boxes_numeric"  value="<? echo $rate; ?>" /></td> 
            <td><input name="txtAmount[]" id="txtAmount_<? echo $tblRow; ?>" type="text" style="width:50px"  class="text_boxes_numeric"  value="<? echo $amount; ?>" readonly="readonly" /></td> 
            <td><input name="txtProcessLoss[]" id="txtProcessLoss_<? echo $tblRow; ?>" type="text"  class="text_boxes_numeric" style="width:50px" placeholder="%" value="<? echo $processLoss; ?>" onKeyUp="sum_total_qnty(<? echo $tblRow;?>);"  /></td> 
            <td><input type="text" name="txtTotalqty[]" id="txtTotalqty_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $totalQty; ?>" readonly="readonly" />
                <input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $hdnDtlsUpdateId; ?>" readonly="readonly" />
            </td>
        </tr> 
        <?
    }
    exit();
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    /*echo '<pre>';
    print_r($cbo_company_name);die;*/
    $user_id=$_SESSION['logic_erp']['user_id'];
    
    if ($operation==0) // Insert Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        $receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
        $delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));
        /* $current_date=strtotime(date("d-m-Y"));
        if($receive_date>$delivery_date)
        {
            echo "26**"; die;
        }
        else if($receive_date != $current_date)
        {
            echo "25**"; die;
        }*/

        
        if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
        else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
        
        $new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'YDOE', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from yd_ord_mst where entry_form=374 and company_id=$cbo_company_name $insert_date_con and status_active=1 and is_deleted=0 order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
        /*if(str_replace("'",'',$txt_wo_no)==""){
            $txt_wo_no=$new_job_no[0];
        }else{
            $txt_wo_no=str_replace("'",'',$txt_wo_no);
        }*/

        if (is_duplicate_field( "order_no", "yd_ord_mst", "order_no='$txt_wo_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0" ) == 1)
        {
            echo "11**0"; die;
        }
        else
        {
            //echo "10**select order_no from subcon_ord_mst where order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0 and id !=$update_id"; die;
            if($db_type==0){
                $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
                $txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date),'yyyy-mm-dd');
                $txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date),'yyyy-mm-dd');
                $txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
            }else{
                $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
                $txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date), "", "",1);
                $txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date), "", "",1);
                $txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
            }
            $id=return_next_id("id","yd_ord_mst",1);
            $id1=return_next_id( "id", "yd_ord_dtls",1);
            // $id3=return_next_id( "id", "subcon_ord_breakdown", 1 );
            $rID3=true;
            $field_array="id, entry_form, yd_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no, booking_without_order, booking_type, remarks, inserted_by, insert_date";
            $data_array="(".$id.", 374, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_order_receive_date."', '".$txt_delivery_date."','".$txt_rec_start_date."','".$txt_rec_end_date."', '".$hid_order_id."', '".$txt_order_no."', '".$is_without_order."','".$hid_booking_type."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
            
            $txt_job_no=$new_job_no[0];
            $field_array2="id, mst_id, job_no_mst, order_id, order_no, style_ref, sales_order_no, sales_order_id, product_id, process_id, lot, count_id, yarn_type_id, yarn_composition_id, item_color_id, yd_color_id, csp, no_bag, cone_per_bag, no_cone, avg_wgt, uom, order_quantity,  rate, amount, process_loss, total_order_quantity,  inserted_by, insert_date";
            //$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount, booked_qty";

            // $size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
            //$add_commadtls=0; $data_array3="";
            $color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

            $data_array2=""; $add_commaa=0;
            for($i=1; $i<=$total_row; $i++)
            {  
                $txtstyleRef            = "txtstyleRef_".$i;
                $txtsaleOrder           = "txtsaleOrder_".$i;
                $txtsaleOrderID         = "txtsaleOrderID_".$i;
                $txtProductID           = "txtProductID_".$i;
                $txtprocess             = "txtprocess_".$i;
                $txtlot                 = "txtlot_".$i;
                $cboCount               = "cboCount_".$i;
                $cboYarnType            = "cboYarnType_".$i;
                $cboComposition         = "cboComposition_".$i;

                $txtItemColor           = "txtItemColor_".$i;
                $txtYarnColor           = "txtYarnColor_".$i;
                $txtItemColorID         = "txtItemColorID_".$i;
                $txtYarnColorID         = "txtYarnColorID_".$i;

                $txtCSP                 = "txtCSP_".$i;
                $txtnoBag               = "txtnoBag_".$i;          
                $txtConeBag             = "txtConeBag_".$i;
                $txtNoCone              = "txtNoCone_".$i;
                $txtAVG                 = "txtAVG_".$i;
                $cboUom                 = "cboUom_".$i;
                $txtOrderqty            = "txtOrderqty_".$i;
                $txtRate                = "txtRate_".$i;
                $txtAmount              = "txtAmount_".$i;
                $txtProcessLoss         = "txtProcessLoss_".$i;
                $txtTotalqty            = "txtTotalqty_".$i;
                /*$hdnDtlsUpdateId      = "hdnDtlsUpdateId_".$i;
                $hdnbookingDtlsId       = "hdnbookingDtlsId_".$i;
                $txtIsWithOrder         = "txtIsWithOrder_".$i;
                
                $orddelivery_date=strtotime(str_replace("'",'',$$txtOrderDeliveryDate));
                if($receive_date>$orddelivery_date){
                    echo "26**"; die;
                }
                if($db_type==0){
                    $orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
                } else {
                    $orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
                }if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);*/

                if (str_replace("'", "", trim($$txtItemColor)) != "") {
                    if (!in_array(str_replace("'", "", trim($$txtItemColor)),$new_array_color)){
                        $color_id = return_id( str_replace("'", "", trim($$txtItemColor)), $color_arr, "lib_color", "id,color_name","374");
                        $new_array_color[$color_id]=str_replace("'", "", trim($$txtItemColor));
                    }
                    else $color_id =  array_search(str_replace("'", "", trim($$txtItemColor)), $new_array_color);
                } else $color_id = 0;

                if (str_replace("'", "", trim($$txtYarnColor)) != "") {
                    if (!in_array(str_replace("'", "", trim($$txtYarnColor)),$new_array_color)){
                        $yd_color_id = return_id( str_replace("'", "", trim($$txtYarnColor)), $color_arr, "lib_color", "id,color_name","374");
                        $new_array_color[$yd_color_id]=str_replace("'", "", trim($$txtYarnColor));
                    }
                    else $yd_color_id =  array_search(str_replace("'", "", trim($$txtYarnColor)), $new_array_color);
                } else $yd_color_id = 0;

                if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
                
                $data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."','".$hid_order_id."','".$txt_order_no."',".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$txtProductID.",".$$txtprocess.",".$$txtlot.",".$$cboCount.",".$$cboYarnType.",".$$cboComposition.",".$color_id.",".$yd_color_id.",".$$txtCSP.",".$$txtnoBag.",".$$txtConeBag.",".$$txtNoCone.",".$$txtAVG.",".$$cboUom.",".str_replace(",",'',$$txtOrderqty).",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtProcessLoss).",".str_replace(",",'',$$txtTotalqty).",'".$user_id."','".$pc_date_time."')";
                
                //$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."',".$hid_order_id.",".$txt_order_no.",".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtprocess.",".$$txtlot.",".$$cboCount.",".$$cboYarnType.",".$$cboComposition.",".$$txtItemColor.",".$$txtYarnColor.",".$$txtCSP.",".$$txtnoBag.",".$$txtConeBag.",".$$txtNoCone.",".$$txtAVG.",".$$cboUom.",".str_replace(",",'',$$txtOrderqty).",".$$txtRate.",".str_replace(",",'',$$txtAmount).",'".str_replace(",",'',$$txtProcessLoss)."',".str_replace(",",'',$$txtTotalqty).",'','".$user_id."','".$pc_date_time."')";

                $id1=$id1+1; $add_commaa++;
                //echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;            
            }

            //echo "10**INSERT INTO yd_ord_mst (".$field_array.") VALUES ".$data_array; die;
            //echo "10**INSERT INTO yd_ord_dtls (".$field_array2.") VALUES ".$data_array2; die;
            $flag=true;
            $rID=sql_insert("yd_ord_mst",$field_array,$data_array,1);
            if($rID==1) $flag=1; else $flag=0;
            if($flag==1){
                $rID2=sql_insert("yd_ord_dtls",$field_array2,$data_array2,1);
                if($rID2==1) $flag=1; else $flag=0;
            }
            /*if(str_replace("'","",$cbo_within_group)==1){
                if($flag==1){
                    $rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
                    if($rIDBooking==1) $flag=1; else $flag=0;
                }
            }*/
            //echo "10**$rID**$rID2**$flag"; die;
            if($db_type==0){
                if($flag==1){
                    mysql_query("COMMIT");  
                    echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
                }else{
                    mysql_query("ROLLBACK"); 
                    echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
                }
            }else if($db_type==2){
                if($flag==1){
                    oci_commit($con);
                    echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
                }else{
                    oci_rollback($con);
                    echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
                }
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
        
        $receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
        $delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));

        if($db_type==0){
            $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
            $txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date),'yyyy-mm-dd');
            $txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date),'yyyy-mm-dd');
            $txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
        }else{
            $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
            $txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date), "", "",1);
            $txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date), "", "",1);
            $txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
        }

        $field_array="party_id*party_location*currency_id*receive_date*delivery_date*rec_start_date*rec_end_date*order_id*order_no*booking_without_order*booking_type* remarks*updated_by*update_date";
        $data_array="'".$cbo_party_name."'*'".$cbo_party_location."'*'".$cbo_currency."'*'".$txt_order_receive_date."'*'".$txt_delivery_date."'*'".$txt_rec_start_date."'*'".$txt_rec_end_date."'*'".$hid_order_id."'*'".$txt_order_no."'*'".$is_without_order."'*'".$hid_booking_type."'*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            
       $field_array2="order_id*order_no*style_ref*sales_order_no*sales_order_id*product_id*process_id*lot*count_id*yarn_type_id*yarn_composition_id*item_color_id*yd_color_id*csp*no_bag*cone_per_bag*no_cone*avg_wgt*uom*order_quantity*rate*amount*process_loss*total_order_quantity*updated_by*update_date";

        $data_array2=""; $add_commaa=0;
        for($i=1; $i<=$total_row; $i++)
        {  
            $txtstyleRef            = "txtstyleRef_".$i; 
            $txtsaleOrder           = "txtsaleOrder_".$i;
            $txtsaleOrderID         = "txtsaleOrderID_".$i;
            $txtProductID           = "txtProductID_".$i;
            $txtprocess             = "txtprocess_".$i;
            $txtlot                 = "txtlot_".$i;
            $cboCount               = "cboCount_".$i;
            $cboYarnType            = "cboYarnType_".$i;
            $cboComposition         = "cboComposition_".$i;
            $txtItemColor           = "txtItemColor_".$i;
            $txtYarnColor           = "txtYarnColor_".$i;
            $txtItemColorID         = "txtItemColorID_".$i;
            $txtYarnColorID         = "txtYarnColorID_".$i;
            $txtCSP                 = "txtCSP_".$i;
            $txtnoBag               = "txtnoBag_".$i;          
            $txtConeBag             = "txtConeBag_".$i;
            $txtNoCone              = "txtNoCone_".$i;
            $txtAVG                 = "txtAVG_".$i;
            $cboUom                 = "cboUom_".$i;
            $txtOrderqty            = "txtOrderqty_".$i;
            $txtRate                = "txtRate_".$i;
            $txtAmount              = "txtAmount_".$i;
            $txtProcessLoss         = "txtProcessLoss_".$i;
            $txtTotalqty            = "txtTotalqty_".$i;
            $hdnDtlsUpdateId        = "hdnDtlsUpdateId_".$i;
            $dtlsUpdateId =str_replace("'",'',$$hdnDtlsUpdateId);

            if (str_replace("'", "", trim($$txtItemColor)) != "") {
                if (!in_array(str_replace("'", "", trim($$txtItemColor)),$new_array_color)){
                    $color_id = return_id( str_replace("'", "", trim($$txtItemColor)), $color_arr, "lib_color", "id,color_name","374");
                    $new_array_color[$color_id]=str_replace("'", "", trim($$txtItemColor));
                }
                else $color_id =  array_search(str_replace("'", "", trim($$txtItemColor)), $new_array_color);
            } else $color_id = 0;

            if (str_replace("'", "", trim($$txtYarnColor)) != "") {
                if (!in_array(str_replace("'", "", trim($$txtYarnColor)),$new_array_color)){
                    $yd_color_id = return_id( str_replace("'", "", trim($$txtYarnColor)), $color_arr, "lib_color", "id,color_name","374");
                    $new_array_color[$yd_color_id]=str_replace("'", "", trim($$txtYarnColor));
                }
                else $yd_color_id =  array_search(str_replace("'", "", trim($$txtYarnColor)), $new_array_color);
            } else $yd_color_id = 0;


            $data_array2[$dtlsUpdateId]=explode("*",("".$hid_order_id."*'".$txt_order_no."'*".$$txtstyleRef."*".$$txtsaleOrder."*".$$txtsaleOrderID."*".$$txtProductID."*".$$txtprocess."*".$$txtlot."*".$$cboCount."*".$$cboYarnType."*".$$cboComposition."*".$color_id."*".$yd_color_id."*".$$txtCSP."*".$$txtnoBag."*".$$txtConeBag."*".$$txtNoCone."*".$$txtAVG."*".$$cboUom."*".str_replace(",",'',$$txtOrderqty)."*".$$txtRate."*".str_replace(",",'',$$txtAmount)."*".str_replace(",",'',$$txtProcessLoss)."*".str_replace(",",'',$$txtTotalqty)."*".$user_id."*'".$pc_date_time."'"));
            $hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
        }

        $rID=sql_update("yd_ord_mst",$field_array,$data_array,"id",$update_id,0);  
        if($rID) $flag=1; else $flag=0;
        //echo "10**".bulk_update_sql_statement( "yd_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
        if($data_array2!="" && $flag==1)
        {
            $rID2=execute_query(bulk_update_sql_statement( "yd_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
            if($rID2) $flag=1; else $flag=0;
        }
        //echo "10**$rID**$rID2**$flag"; die;
        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
        }
        else if($db_type==2)
        {  
            if($flag==1)
            {
                oci_commit($con);
                echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
            else
            {
                oci_rollback($con);
                echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==2)   // delete here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");  
        }
        
        /*$next_process=return_field_value( "id", "trims_job_card_mst"," entry_form=257 and $update_id=received_id and status_active=1 and is_deleted=0");
        if($next_process!=''){
            echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            die;
        }
        $job_no="'".$txt_job_no."'";
        $order_no="'".$txt_order_no."'";*/
        $flag=0;
        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
        $rID=sql_update("yd_ord_mst",$field_array,$data_array,"id",$update_id,0);
        
        if($rID) $flag=1; else $flag=0; 
        
        if($flag==1)
        {
            $rID1=sql_update("yd_ord_dtls",$field_array,$data_array,"mst_id",$update_id,1);
            if($rID1) $flag=1; else $flag=0; 
        }   
        
        //echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$flag; die;
        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
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
                echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
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