<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
{
    echo "$('#search').show()";
//    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=6 and report_id=124 and is_deleted=0 and status_active=1","format_id","format_id");
//    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}

if ($action=="load_drop_down_supplier")
{
    echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b where a.id=b.supplier_id 	 and b.tag_company=$data order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
    exit();
}

if($action=="load_drop_down_sent")
{
    $data = explode("_",$data);
    if($data[0]==1)
    {

        echo create_drop_down( "cbo_search_by", 100, "select id,buyer_name from  lib_buyer  where status_active=1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","0" );
    }
    else if($data[0]==2)
    {
        echo create_drop_down( "cbo_search_by", 100, "select id,supplier_name from  lib_supplier  where status_active=1 and is_deleted=0  order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected,"","0" );
    }
    else if($data[0]==3)
    {
        echo create_drop_down( "cbo_search_by", 100, "select id,other_party_name from  lib_other_party where status_active=1 and is_deleted=0  order by other_party_name","id,other_party_name", 1, "-- Select Other Party --", $selected,"","0" );
    }
    else
    {
        echo create_drop_down( "cbo_search_by", 100, $blank_array,"", 1, "-- Select --", $selected, "","","" );
    }

    exit();
}

//style search------------------------------//
if($action=="chalan_surch")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    //echo $style_id;die;

    ?>

    <script>
        function js_set_value(str)
        {

            var splitData = str.split("_");
            $("#hidden_chalan_id").val(splitData[0]);
            $("#hidden_chalan_no").val(splitData[1]);
            $("#hidden_search_number").val($("#cbo_search_by").val());

            parent.emailwindow.hide();
        }
    </script>

    </head>

    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="700" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                <thead>
                <tr>
                    <th width="150">Search By</th>
                    <th id="search_by_td_up" width="150">Enter Booking No</th>
                    <th width="180">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <?
                        $search_arr=array(1=>"Chalan No",2=>"System ID");
                        $dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
                        echo create_drop_down( "cbo_search_by", 170, $search_arr,"",1, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $category; ?>, 'create_chalan_search_list_view', 'search_div', 'daily_gate_pass_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td align="center" height="40" valign="middle" colspan="4">
                        <? echo load_month_buttons(1);  ?>
                        <!-- Hidden field here-------->
                        <input type="hidden" id="hidden_chalan_id" value="" />
                        <input type="hidden" id="hidden_chalan_no" value="" />
                        <input type="hidden" id="hidden_search_number" value="" />
                    </td>
                </tr>
                </tbody>
                </tr>
            </table>
            <div align="center" valign="top" id="search_div"> </div>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_chalan_search_list_view")
{

    $ex_data = explode("_",$data);
    $txt_search_by = $ex_data[0];
    $txt_search_common = trim($ex_data[1]);
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $company = $ex_data[4];
    $item_category= $ex_data[5];

    $sql_cond="";

    if(trim($txt_search_by)==0) { echo "Please select Search By";die;}

    if(trim($txt_search_common)!="")
    {
        if(trim($txt_search_by)==1) // for challan
        {
            $sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
        }
        else if(trim($txt_search_by)==2) // for System ID
        {
            $sql_cond .= " and a.sys_number LIKE '%$txt_search_common%'";
        }
    }

    if(trim($item_category)!=0) { $sql_cond .= " and b.item_category_id=$item_category"; }
    if($db_type==0)
    {
        if ($txt_date_from !='' && $txt_date_to !='') $sql_cond.=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
    }
    else
    {
        if ($txt_date_from !='' && $txt_date_to !='') $sql_cond .=" and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
    }
//    if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.out_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
    if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";


    //if($txt_search_by==1 )
    //{
    $sql = "select a.id ,a.challan_no as chalan, a.sys_number_prefix_num, a.out_date, b.sample_id from  inv_gate_pass_mst a, inv_gate_pass_dtls b 
				where a.id=b.mst_id and a.status_active=1 $sql_cond order by a.id desc";
    $result = sql_select($sql);
    $party_type_arr=array(1=>"Buyer",2=>"Supplier",3=>"Other Party");
    $sample_arr=return_library_array( "select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
    $arr=array(2=>$party_type_arr,3=>$sample_arr);
    echo  create_list_view("list_view", "System ID, Gate Pass Date, Sample","150,100,150","480","200",0, $sql , "js_set_value", "id,sys_number_prefix_num", "", 1, "0,0,sample_id", $arr, "sys_number_prefix_num,out_date,sample_id", "",'','0,3,0,0,0') ;
    exit();
}

if($action=="generate_report1")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_party_type=str_replace("'","",$cbo_party_type);
    $cbo_search_by=str_replace("'","",$cbo_search_by);
    $cbo_location=str_replace("'", "", $cbo_location);
    $cbo_within_group=str_replace("'", "", $cbo_within_group);

    $txt_challan=str_replace("'","",$txt_challan);
    $cbo_gate_type = str_replace("'", "", $cbo_gate_type);
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $sample_arr=return_library_array(" select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $supplier_name_library=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
    $other_party_name_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
    $buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $location_arr=return_library_array("select id,location_name from lib_location", "id", "location_name");
    $department_arr=return_library_array("select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 ", "id", "department_name");
    $user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');

    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);


    $gate_pass_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");	
    $gate_format_ids=explode(",",$gate_pass_print_report_format);
    $print_btn=$gate_format_ids[0];


    $company_conds='';
    $search_by_cond='';
    $challan_sys_cond='';
    $buyer_condition="";
    if($cbo_party_type==1)
    {
        if(str_replace("'","",$cbo_search_by)!="")  $buyer_condition=" and a.buyer_name='".str_replace("'","",$cbo_search_by)."'";
    }
    if ($cbo_company_name !=0) $company_conds.=" and a.company_id=$cbo_company_name";
   //    if ($cbo_party_type !=0) $search_by_cond=" and a.party_type=$cbo_party_type";
    if($txt_challan !='') $challan_sys_cond=" and a.sys_number_prefix_num = $txt_challan";
    $location_conds='';
    if($cbo_location!=0) $location_conds=" and a.com_location_id in($cbo_location)";


    if($cbo_within_group!=0) $cond_within_group=" and a.within_group in($cbo_within_group)";

    $date_cond='';
    if($db_type==0)
    {
        if ($txt_date_from !='' && $txt_date_to !='') $date_cond.=" and a.in_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
    }
    else
    {
        if ($txt_date_from !='' && $txt_date_to !='') $date_cond="and a.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
    }

    $order_array=array();
    $order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_condition";
    $order_sql_result=sql_select($order_sql);
    foreach ($order_sql_result as $row)
    {
        $order_array[$row[csf('po_number')]]['buyer']=$row[csf('buyer_name')];
        $order_array[$row[csf('po_number')]]['style']=$row[csf('style_ref_no')];
    }
    
    if($cbo_gate_type==1 || $cbo_gate_type==2){
        $table_width=2320;
    }
    else{
        $table_width=2220;
    }
    
    ob_start();
    ?>
    <style type="text/css">
        .nsbreak{word-break: break-all;}
    </style>

    <div style="height:auto; clear:both;">
        <?
        $out_date_cond=$out_date_cond_scan='';
        if($db_type==0)
        {
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond=" and a.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
            // if($txt_date_from !='' && $txt_date_to !='') $out_date_cond_scan=" and c.out_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
        }
        else
        {
            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
            //            if($txt_date_from !='' && $txt_date_to !='') $out_date_cond_scan="and c.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
        }
            // echo $out_date_cond_scan;

            //        $sql_data=sql_select("select out_date, gate_pass_id, out_time from inv_gate_out_scan where status_active=1 and is_deleted=0");
            //        $gate_scan_array=array();
            //        foreach($sql_data as $row)
            //        {
            //            $gate_scan_array[$row[csf('gate_pass_id')]]['out_date']=$row[csf('out_date')];
            //            $gate_scan_array[$row[csf('gate_pass_id')]]['out_time']=$row[csf('out_time')];
            //        }
            //
            //        $sql_gate=sql_select("select gate_pass_id from inv_gate_out_scan where status_active=1 and is_deleted=0");
            //        $i=1;
            //        foreach($sql_gate as $row_g)
            //        {
            //            if($i!==1) $row_cond.=",";
            //            $row_cond.=$row_g[csf('gate_pass_id')];
            //            $i++;
            //        }

            //        $sql_gate_in=sql_select("select gate_pass_no from inv_gate_in_mst where status_active=1 and is_deleted=0 and gate_pass_no is not null ");
            //        $k=1;
            //        $gatePassNoArr=array();
            //        foreach($sql_gate_in as $row_in)
            //        {
            //            $in_row_cond[$row_in[csf('gate_pass_no')]]="'".$row_in[csf('gate_pass_no')]."'";
            //            $gatePassNoArr[].=$row_in[csf('gate_pass_no')];
            //            $k++;
            //        }
            $gateInStatus = return_library_array("select sum(quantity) as qty, a.gate_pass_no from inv_gate_in_mst a, inv_gate_pass_mst b, inv_gate_in_dtl c where b.sys_number = a.gate_pass_no and a.id = c.mst_id and a.RETURNABLE = 1 group by a.gate_pass_no", "gate_pass_no", "qty");
            $sysNo_cond_in=where_con_using_array($in_row_cond,0,'a.sys_number not ');
            // echo $sysNo_cond_in;die;
        if($cbo_gate_type==0)  // All
        {
            $sql_out="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,	a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, b.buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_hour, a.time_minute, b.no_of_bags, a.INSERTED_BY,c.OUT_DATE as gate_out_date,c.OUT_TIME
            from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan c
            where a.id=b.mst_id and a.SYS_NUMBER=c.GATE_PASS_ID and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $company_conds $location_conds $out_date_cond $challan_sys_cond $cond_within_group
            order by b.id desc";
        }
        if($cbo_gate_type==1)  // gate in
        {
            if ($cbo_company_name !=0) $company_conds_in.=" and d.company_id=$cbo_company_name";
            if($txt_date_from !='' && $txt_date_to !='') $gate_in_date_cond="and d.in_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";

            $sql_out="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,	a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, b.buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_minute, b.no_of_bags,d.time_hour,d.inserted_by as GATE_IN_INSERTED_BY, a.INSERTED_BY,d.IN_DATE
            from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_in_dtl c,inv_gate_in_mst d
            where a.id=b.mst_id  and  c.get_pass_dtlsid=b.id and d.id=c.mst_id  and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and  d.status_active=1  and d.is_deleted=0 $company_conds_in $location_conds $gate_in_date_cond $challan_sys_cond $cond_within_group 
            order by b.id desc";
        }
        if($cbo_gate_type==2)  // gate OUT
        {
            if($txt_date_from !='' && $txt_date_to !='') $gate_out_date_cond="and e.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";

            // $sql_out="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,	a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, b.buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_minute, b.no_of_bags,e.out_time
            // from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_in_dtl c,inv_gate_in_mst d,inv_gate_out_scan e
            // where a.id=b.mst_id  and  c.get_pass_dtlsid=b.id and d.id=c.mst_id and  e.gate_pass_id=a.sys_number and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and  e.status_active=1  and e.is_deleted=0 $company_conds $location_conds $gate_out_date_cond $challan_sys_cond 
            // order by b.id desc";
            $sql_out="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,	a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, b.buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_minute, b.no_of_bags,e.out_time,e.inserted_by as GATE_OUT_INSERTED_BY, a.INSERTED_BY
            from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan e
            where a.id=b.mst_id and e.gate_pass_id=a.sys_number and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and  e.status_active=1  and e.is_deleted=0 $company_conds $location_conds $gate_out_date_cond $challan_sys_cond $cond_within_group
            order by b.id desc";
        }
        if($cbo_gate_type==3)  // gate OUT pending
        {
            if($txt_date_from !='' && $txt_date_to !='') $gate_out_pending_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";

            $sql_out="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,	a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, b.buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_minute, b.no_of_bags, a.INSERTED_BY
            from inv_gate_pass_mst a, inv_gate_pass_dtls b
            where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sys_number not in(select a.gate_pass_id from inv_gate_out_scan a where a.status_active=1 and a.is_deleted=0 ) $company_conds $location_conds $gate_out_pending_date_cond $challan_sys_cond $cond_within_group 
            order by b.id desc";
        }
        if($cbo_gate_type==4)  // return pending
        {
            if($txt_date_from !='' && $txt_date_to !='') $gate_return_pending_date_cond="and a.out_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";

            $sql_out="SELECT a.id, a.company_id, a.com_location_id, a.location_id, a.department_id, a.location_name, a.delivery_as, a.issue_id, a.sys_number, a.sys_number_prefix_num, a.sent_by, a.within_group, a.basis,	a.issue_purpose, a.sent_to, a.out_date, a.returnable, a.est_return_date, a.challan_no, a.carried_by, b.buyer_order, a.get_pass_no, b.sample_id, b.item_category_id, b.item_description, b.quantity, b.uom, b.uom_qty, b.remarks, a.time_minute, b.no_of_bags, a.INSERTED_BY
            from inv_gate_pass_mst a, inv_gate_pass_dtls b,inv_gate_out_scan c 
            where a.id=b.mst_id  and a.sys_number=c.gate_pass_id and a.returnable=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sys_number not in (select gate_pass_no from inv_gate_in_mst where status_active=1 and is_deleted=0 and gate_pass_no is not null)$company_conds $location_conds $gate_return_pending_date_cond $challan_sys_cond $cond_within_group 
            order by b.id desc";
        }
        
      //echo $sql_out;
        $get_out_data=sql_select($sql_out);

        $sql_data=sql_select(" select out_date, gate_pass_id, out_time from inv_gate_out_scan where status_active=1 and is_deleted=0");
        $gate_scan_array=array();
        foreach($sql_data as $row)
        {
            $gate_scan_array[$row[csf('gate_pass_id')]]['out_time']=$row[csf('out_time')];
        }
        //        $print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");
        //
        //        $print_button=explode(",",$print_report_format);
        //        $print_button_first=array_shift($print_button);
        //
        //        if($print_button_first==115) $getpass_button="get_out_entry_print";
        //        else if($print_button_first==116) $getpass_button="print_to_html_report";
        //        else if($print_button_first==136) $getpass_button="get_out_entry_emb_issue_print";
        //        else if($print_button_first==137) $getpass_button="print_to_html_report5";
        //        else if($print_button_first==196) $getpass_button="print_to_html_report6";
        //        else if($print_button_first==199) $getpass_button="print_to_html_report7";
        //        else if($print_button_first==206) $getpass_button="get_out_entry_print8_fashion";
        //        else if($print_button_first==207) $getpass_button="print_to_html_report9";
        //        else if($print_button_first==208) $getpass_button="print_to_html_report10";
        //        else if($print_button_first==212) $getpass_button="print_to_html_report11";
        //        else if($print_button_first==271) $getpass_button="print_to_html_report14";
        //        else if($print_button_first==42) $getpass_button="print_to_html_report_15";
        //        else if($print_button_first==362) $getpass_button="print_to_html_report_15_v2";
        //        else if($print_button_first==227) $getpass_button="print_to_html_report16";
        //        else if($print_button_first==227) $getpass_button="get_out_entry_print12";
        //        else if($print_button_first==191) $getpass_button="print_to_html_report_13";
        //        else if($print_button_first==161) $getpass_button="get_out_entry_print6";
        //        else if($print_button_first==235) $getpass_button="get_out_entry_print9";
        //        else if($print_button_first==274) $getpass_button="get_out_entry_print10";
        //        else if($print_button_first==707) $getpass_button="print_to_html_report17";
        //        else if($print_button_first==738) $getpass_button="get_out_entry_printamt";
        //        else if($print_button_first==747) $getpass_button="get_out_entry_print14";
        //        else  $getpass_button="";

        ?>
        <br/><br/>
        <?
        if(count($get_out_data)>0)
        {
            ?>
            <div style="width:<? echo $table_width; ?>px;">
                <table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                    <tr class="form_caption" style="border:none;">
                        <td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Gate Pass Report</td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="15" align="center" style="border:none; font-size:14px;">
                            <? echo $company_arr[$cbo_company_name]; ?>
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="15" align="center" style="border:none; font-size:14px;">
                            Date Range : <? echo change_date_format($txt_date_from).' to '.change_date_format($txt_date_to); ?>
                        </td>
                    </tr>
                </table>
                <br />
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table nsbreak" rules="all" id="table_header_2" align="left">
                    <thead>
                    <tr>
                        <th width="30" >SL</th>
                        <th width="120">Basis</th>
                        <th width="120">Gate Pass No</th>
                        <th width="80">Gate Pass Date</th>
                        <th width="120">Sample Type</th>
                        <th width="90">Within Group</th>
                        <th width="110">From Location</th>
                        <th width="100">Department</th>
                        <th width="120">Item Category</th>
                        <th width="150">Item Description</th>
                        <th width="80">Quantity</th>
                        <th width="60">UOM</th>
                        <th width="100">Sent By</th>
                        <th width="100">Sent to</th>
                        <th width="110">To Location</th>
                        <th width="60">Returnable</th>
                        <th width="90"> Est. Returnable Date</th>
                        <th width="90"> Gate Out Date</th>
                        <th width="90">Gate In Status</th>
                        <th width="100">Delivery As</th>
                        <th width="100" >Purpose</th>
                        <?if($cbo_gate_type==1) {?>
                        <th width="80">In date time</th>
                        <?}else if($cbo_gate_type==2 || $cbo_gate_type==3 || $cbo_gate_type==4){?>
                        <th width="80">Out Date time</th>   
                        <?}
                        if($cbo_gate_type==1) {?>
                        <th width="100">Get In User</th>
                        <?
                        }
                        else if($cbo_gate_type==2 ){
                        ?>
                        <th width="100">Get Out User</th>
                        <?
                        }
                        ?>
                        <th width="100" >Carried By</th>
                        <th width="100">Insert User/G.Pass</th>
                        <th width="100">Remarks</th>
                    </tr>
                    </thead>
                </table>
                <div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height: 380px;" id="scroll_body" align="left">
                    <table width="<? echo $table_width; ?>" class="rpt_table nsbreak" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_1" align="left">
                        <tbody>
                        <?
                        $i=$k=1;$tot_quantity=0;
                        $temp_arr=array();
                        foreach($get_out_data as $val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
                            else $bgcolor="#FFFFFF";

                            $basis=$val[csf('basis')];
                            $within_group=$val[csf('within_group')];
                            $gate_out_time=$gate_scan_array[$val[csf('sys_number')]]['out_time'];
                            if($basis==1)
                            {
                                if($within_group==1) $send_to_company=$company_arr[$val[csf('sent_to')]];
                                else $send_to_company=$val[csf('sent_to')];
                            }
                            else if($basis==8 || $basis==9)
                            {
                                $send_to_company=$val[csf('sent_to')];
                            }
                            else if($basis==12)
                            {
                                $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
                            }
                            else
                            {
                                //echo $within_group.'=='.$basis;
                                if($basis==2 || $basis==3 || $basis==4 || $basis==5 || $basis==6 || $basis==7  ||  $basis==10 || $basis==13 ||  $basis==14 ||  $basis==11 ||  $basis==15)
                                {
                                    if($within_group==2) $send_to_company=$supplier_name_library[$val[csf('sent_to')]];
                                    else $send_to_company=$company_arr[$val[csf('sent_to')]];
                                }
                                else $send_to_company=$val[csf('sent_to')];
                            }
                            ?>
                            <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trout_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trout_<? echo $i; ?>">
                                <?
                                if(!in_array($val[csf('sys_number')],$temp_arr))
                                {
                                    $temp_arr[]=$val[csf('sys_number')];
                                    ?>
                                    <td width="30" align="center"><p><? echo $k; ?>&nbsp;</p></td>
                                    <?
                                    $k++;
                                }
                                else
                                {
                                    ?>
                                    <td width="30"><p>&nbsp;</p></td>
                                    <?
                                }
                                ?>
                                <td width="120"><p><? echo $get_pass_basis[$val[csf("basis")]]; ?>&nbsp;</p></td>
                                <td width="120"><p><a href='#report_details' onClick="generate_trims_print_report('<? echo $val[csf('company_id')]?>','<? echo $val[csf('sys_number')]?>','<? echo $print_btn ?>','<? echo $val[csf('com_location_id')]?>','<? echo $val[csf('challan_no')]?>','<? echo $val[csf('basis')]?>','<? echo $val[csf('returnable')]?>')"><? echo $val[csf('sys_number')]; ?></a> &nbsp;</p></td>
                                <td width="80"><p><? echo change_date_format($val[csf("out_date")]); ?>&nbsp;</p></td>
                                <td width="120"><p><? echo $sample_arr[$val[csf("sample_id")]]; ?>&nbsp;</p></td>
                                <td width="90" align="center"><p><?=$val[csf("within_group")] == 1 ? 'Yes' : ($val[csf("within_group")] == 2 ? 'No' : '')?></p></td>
                                <td width="110"><p><?=$location_arr[$val[csf("com_location_id")]]?></p></td>
                                <td width="100"><p><?=$department_arr[$val[csf("department_id")]]?></p></td>
                                <td width="120"><p><? echo $item_category[$val[csf("item_category_id")]]; ?>&nbsp;</p></td>
                                <td width="150"><p><? echo  $val[csf("item_description")]; ?>&nbsp;</p></td>
                                <td width="80" align="right"><p><? echo number_format($val[csf("quantity")],2); ?></p></td>
                                <td width="60" align="center"><p><? echo $unit_of_measurement[$val[csf("uom")]]; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $val[csf("sent_by")]; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $send_to_company; ?>&nbsp;</p></td>
                                <td width="110"><?=$val[csf("within_group")] == 1 ? $location_arr[$val[csf("location_id")]] : $val[csf("location_name")]?></td>
                                <td width="60" align="center"><p><? echo  $yes_no[$val[csf("returnable")]]; ?>&nbsp;</p></td>
                                <td width="90"><?echo  change_date_format($val[csf("est_return_date")]);;?></td>
                                <td width="90"><?echo  change_date_format($val[csf("gate_out_date")])."<br>".$val[csf("OUT_TIME")];?></td>
                                <td width="90">
                                    <?
                                    if($val[csf("returnable")] == 1){
                                        if(isset($gateInStatus[$val[csf("sys_number")]])){
                                            if($gateInStatus[$val[csf("sys_number")]] >= $val[csf("quantity")]){
                                                echo "Complete";
                                            }else{
                                                echo "Partial";
                                            }
                                        }else{
                                            echo "Pending";
                                        }
                                    }
                                    ?>
                                </td>
                                <td width="100"><p><?=$basis_arr[$val[csf("delivery_as")]]?></p></td>
                                <td width="100" align="center"><p><? echo $val[csf("issue_purpose")]; ?>&nbsp;</p></td>
                                <?if($cbo_gate_type==1 || $cbo_gate_type==2 || $cbo_gate_type==3 || $cbo_gate_type==4){?>
                                <td width="80" align="center"><p>
								<?
                                    if($cbo_gate_type==1)  //GATE IN
                                    {
                                        echo change_date_format($val["IN_DATE"])."<br>";
                                        if($val[csf("time_hour")]==24)
                                        { 
                                            $hour=$val[csf("time_hour")]-12;
                                            $am="AM";
                                        }
                                        else if($val[csf("time_hour")]==12)
                                        { 
                                            $hour=$val[csf("time_hour")];
                                            $am="PM";
                                        }
                                        else if($val[csf("time_hour")]>12 && $val[csf("time_hour")]<24)
                                        { 
                                            $hour=$val[csf("time_hour")]-12;
                                            $am="PM";
                                        } 
                                        else
                                        {
                                            $hour=$val[csf("time_hour")];
                                            $am="AM";
                                        }
                                        echo $hour.":".$val[csf("time_minute")]." ".$am ; 
                                    }   
                                    else if($cbo_gate_type==2 || $cbo_gate_type==3 || $cbo_gate_type==4)  // GATE OUT
                                    {   echo change_date_format($val["OUT_DATE"])."<br>";
                                        echo $gate_out_time; 
                                    }
                                    ?>&nbsp;</p>
                                </td>	
                            
                                <?}?>
                
                                <?
                                if($cbo_gate_type==1)  //GATE IN
                                {
                                    ?>
                                    <td width="100" align="left"><p><? echo  $user_arr[$val['GATE_IN_INSERTED_BY']];?></p>&nbsp;</td>
                                    <?
                                }   
                                else if($cbo_gate_type==2)  // GATE OUT
                                {
                                    ?>
                                    <td width="100" align="left"><p><? echo  $user_arr[$val['GATE_OUT_INSERTED_BY']];?></p>&nbsp;</td>
                                    <?  
                                }
                                ?>
                               
                                <td width="100" align="center"><p><? echo $val[csf("carried_by")]; ?>&nbsp;</p></td>
                                <td width="100" align="center"><p><? echo $user_arr[$val[csf("inserted_by")]]; ?>&nbsp;</p></td>

                                <td width="100"><p><? echo $val[csf("remarks")]; ?>&nbsp;</p></td>
                               
                            </tr>
                            <?
                            $tot_quantity+=$val[csf("quantity")];
                            $tot_uom_qty+=$val[csf("uom_qty")];
                            $i++;
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <th colspan="10">Total</th>
                            <th><? echo number_format($tot_quantity,2);?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <?if($cbo_gate_type==1 || $cbo_gate_type==2){?>
                            <th></th>
                            <?}?>
                            <th></th>
                            <th></th>
                            <th></th>
                            <?
                            if($cbo_gate_type!=0){
                                echo "<th></th>";
                            }
                            ?>
                            
                        </tfoot>
                    </table>
                </div>
            </div>
            <?
        }
        ?>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}
?>

