<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');
//require_once('../../../includes/class3/class.fabrics.php');

$user_name = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];



if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
    exit();
}

$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");




$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
$report_format_arr = array(0=>"",1 => "show_fabric_booking_report_gr", 2 => "show_fabric_booking_report", 3 => "show_fabric_booking_report3", 4 => "show_fabric_booking_report1", 5 => "show_fabric_booking_report2", 6 => "show_fabric_booking_report4", 7 => "show_fabric_booking_report5", 8 => "show_fabric_booking_report", 9 => "show_fabric_booking_report3", 10 => "show_fabric_booking_report4", 28 => "show_fabric_booking_report_akh",46=>"show_fabric_booking_report_urmi",136=>"print_booking_3",244=>"show_fabric_booking_report_ntg",38=>"show_fabric_booking",39=>"show_fabric_booking_report2",64=>"show_fabric_booking_report3",84=>"show_fabric_booking_report_islam"); //8,9 for short
//--------------------------------------------------------------------------------------------------------------------

$tmplte = explode("**", $data);

if ($tmplte[0] == "viewtemplate")
    $template = $tmplte[1];
else
    $template = $lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template == "")
    $template = 1;

if($action=="booking_no_popup")
{
    echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
     
    <script>
        $(function(){
            load_drop_down( 'yarn_purchase_requisition_follow_up_report_v2_controller',<? echo $companyID;?>, 'load_drop_down_buyer', 'buyer_td' );
        });
        
        var selected_id = new Array; var selected_name = new Array;
        
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
            }
        }
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function js_set_value( str ) {
            if (str!="") str=str.split("_");
             
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
             
            if( jQuery.inArray( str[1], selected_id ) == -1 ) {
                selected_id.push( str[1] );
                selected_name.push( str[2] );
                
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str[1] ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id = ''; var name = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );
            
            $('#txt_booking_no').val( name );
            $('#txt_booking_id').val( id );
            //$('#txt_order_id').val( name );
        }
    
    </script>

    </head>

    <body>
    <div align="center">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
             <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                        <thead>                  
                            <th width="150">Company Name</th>
                            <th width="140">Buyer Name</th>
                            <th width="80">Booking No</th>
                            <th>Booking Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </thead>
                        <tr>
                            <td>
                                <input type="hidden" id="txt_booking_no">
                                <input type="hidden" id="txt_booking_id">
                                <input type="hidden" id="txt_order_id">
                                <input type="hidden" id="job_no">
                                <input type="hidden" id="cbo_year" value="<? echo $cbo_year;?>">
                                <? 
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $companyID, "load_drop_down( 'yarn_purchase_requisition_follow_up_report_v2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --" ); ?></td>
                            <td>
                                <input type="text" id="booking_no_prefix_num" name="booking_no_prefix_num" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('booking_no_prefix_num').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'yarn_purchase_requisition_follow_up_report_v2_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                             </td>
                        </tr>
                        <tr>
                            <td colspan="5"  align="center">
                                <? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </table>
            <div style="margin-top:5px" id="search_div"></div>    
        </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}

if($action=="create_booking_search_list_view")
{
    $data=explode('_',$data);
    if ($data[0]!=0) $company=" and  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
    if ($data[4]!=0) $job_no=" and a.job_no='$data[4]'"; else $job_no='';
    if ($data[5]!=0) $booking_no=" and a.booking_no_prefix_num='$data[5]'"; else $booking_no='';
    if ($data[6]!=0) $cbo_year_con=" and to_char(b.insert_date,'YYYY')=$data[6]"; else $cbo_year_con='';

    
    
    
    //$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
    if($db_type==0)
    {
        if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
    }
    if($db_type==2)
    {
        if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
    }
    $po_array=array();
    $sql_po= sql_select("select b.booking_no,c.po_number from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id $company $buyer $booking_no $booking_date and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
    foreach($sql_po as $row)
    {
        $po_no_array[$row[csf("booking_no")]][$row[csf("po_number")]]=$row[csf("po_number")];
    }
    
    foreach($po_no_array as $booking_number=>$po_no_arr){
        $po_array[$booking_number]=implode(',',$po_no_arr);
    }
    
    //print_r($po_array);die; 
     
     
    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No");
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $arr=array (2=>$comp,3=>$buyer_arr,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
    
    $sql= "SELECT a.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no $company $buyer $booking_no $booking_date $cbo_year_con and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 order by a.id Desc";
    // echo $sql; die;
        
    
    echo  create_list_view("tbl_list_search", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Approved,Is-Ready", "100,80,70,100,80,220,110,60,60","1020","230",0, $sql , "js_set_value", "id,booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,booking_no,item_category,fabric_source,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,booking_no,item_category,fabric_source,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','',1);
   exit(); 
}

if ($action == "report_generate") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    // echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">This report is under QC. Please be patience.</div>';
    if ($template == 1) 
    {
        $type = str_replace("'", "", $cbo_type);
        $company_name = str_replace("'", "", $cbo_company_name);
        $bookingNo = str_replace("'", "", $txt_booking_no);
        $bookingId = str_replace("'", "", $txt_booking_id);
        $txt_ref_no = str_replace("'", "", $txt_ref_no);

        if($bookingNo !="")
        {
            if($bookingId !=""){$bookingIdCond = " and a.id in($bookingId)";}
            $sql_booking = "SELECT b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.booking_no_prefix_num in($bookingNo) $bookingIdCond";
            $sql_res = sql_select($sql_booking);
            $poIdArray = array();
            foreach ($sql_res as $val) 
            {
                $poIdArray[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
            }
            $bookingPoIds = implode(",", $poIdArray);
        } 
        // echo $bookingPoIds;
        // die();
        //var_dump($lapdip_arr); die; 
       
        //if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
        if (str_replace("'", "", $cbo_buyer_name) == 0) 
        {
            if ($_SESSION['logic_erp']["data_level_secured"] == 1) 
            {
                if ($_SESSION['logic_erp']["buyer_id"] != "")
                    $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
                else
                    $buyer_id_cond = "";
            }
            else {
                $buyer_id_cond = "";
            }
        } 
        else 
        {
            $buyer_id_cond = " and a.buyer_name=$cbo_buyer_name";
        }

        
        $discrepancy_td_color = "";
       

        $txt_search_string = str_replace("'", "", $txt_search_string);
        if (trim($txt_search_string) != "")
            $search_string = "%" . trim($txt_search_string) . "%";
        else
            $search_string = "%%";
        
        if($bookingPoIds !="")  // check booking po
        {
            $po_style_cond=" and b.id in($bookingPoIds)";
        }
        else
        {
              
            if ( $txt_search_string!="") $po_style_cond=" and LOWER(b.po_number) like LOWER('$search_string')"; else $po_style_cond="";
            
            
            if ( $txt_ref_no!="") $po_style_cond.=" and LOWER(a.style_ref_no) like LOWER('%$txt_ref_no%')"; else $po_style_cond="";
            
        }
        
       
        $file_no = "";
        $ref_no = "";
        if (str_replace("'", "", trim($cbo_order_status)) != 0)
            $is_confirmed_cond = " and b.is_confirmed = '" . str_replace("'", "", trim($cbo_order_status)) . "'";
        else
            $is_confirmed_cond = "";
        $order_status = array(0 => "ALL", 1 => "Confirmed", 2 => "Projected");

        $start_date = str_replace("'", "", trim($txt_date_from));
        $end_date = str_replace("'", "", trim($txt_date_to));

        if ($start_date != "" && $end_date != "") {
            $str_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
        } else
            $str_cond = "";

        $txt_job_no = str_replace("'", "", $txt_job_no);
        $job_no_cond = "";
        if (trim($txt_job_no) != "") {
            $job_no = trim($txt_job_no);
            $job_no_cond = " and a.job_no_prefix_num=$job_no";
        }

        $cbo_year = str_replace("'", "", $cbo_year);
        if (trim($cbo_year) != 0) 
        {
            if ($db_type == 0)
                $year_cond = " and YEAR(a.insert_date)=$cbo_year";
            else if ($db_type == 2)
                $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
            else
                $year_cond = "";
        } else
            $year_cond = "";

       
        $shipping_status = "%%";
       

        
        $fab_color = "%%";

        $start_date_po = str_replace("'", "", trim($txt_date_from_po));
        $end_date_po = str_replace("'", "", trim($txt_date_to_po));

        if ($end_date_po == "")
            $end_date_po = $start_date_po;
        else
            $end_date_po = $end_date_po;

        if ($start_date_po != "" && $end_date_po != "") 
        {
            if ($db_type == 0) {
                $str_cond_insert = " and b.insert_date between '" . $start_date_po . "' and '" . $end_date_po . " 23:59:59'";
            } else {
                $str_cond_insert = " and b.insert_date between '" . $start_date_po . "' and '" . $end_date_po . " 11:59:59 PM'";
            }
        } else
            $str_cond_insert = "";

        $txt_fab_color="";

        if ($txt_fab_color == "") {
            $color_cond = "";
            $color_cond_prop = "";
        } 
        else 
        {
            if ($db_type == 0) {
                $color_id = return_field_value("group_concat(id) as color_id", "lib_color", "color_name like '$fab_color'", "color_id");
            } else {
                $color_id = return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as color_id", "lib_color", "color_name like '$fab_color'", "color_id");
            }
            if ($color_id == "") {
                $color_cond_search = "";
                $color_cond_prop = "";
            } else {
                $color_cond_search = " and b.fabric_color_id in ($color_id)";
                $color_cond_prop = " and color_id in ($color_id)";
            }
        }
        if($type==1) 
        {
            $table_width = "2000";
            $colspan = "18";
        } 
        else 
        {
            $table_width = "1800";
            $colspan = "12";
        }
        
        if($chk_no_boking == 1)
        {
            $sql="SELECT a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.file_no, b.grouping, b.is_confirmed,c.requisition_date as req_date,c.requ_no as  requ_no 
            from wo_po_details_master a, wo_po_break_down b ,inv_purchase_requisition_dtls d,inv_purchase_requisition_mst c
            where a.id=b.job_id and a.job_no=d.job_no and c.id=d.mst_id and a.company_name='$company_name'  and b.shiping_status like '$shipping_status' $is_confirmed_cond and a.is_deleted=0 and a.status_active=1 and c.status_active=1 and d.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $job_no_cond $po_style_cond $str_cond $str_cond_insert $year_cond $file_no $ref_no order by  a.id, b.id,b.pub_shipment_date";
        }
        else
        {
            $sql="SELECT a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.file_no, b.grouping, b.is_confirmed,'No Requisition' as requ_no   
            from wo_po_details_master a, wo_po_break_down b 
            where a.id=b.job_id and a.job_no not in (select d.job_no from inv_purchase_requisition_dtls d,inv_purchase_requisition_mst c where c.id=d.mst_id and a.job_no=d.job_no and c.status_active=1 and d.status_active=1) and a.company_name='$company_name'  and b.shiping_status like '$shipping_status' $is_confirmed_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $job_no_cond $po_style_cond $str_cond $str_cond_insert $year_cond $file_no $ref_no order by  a.id, b.id,b.pub_shipment_date";
        }
        
       
        //echo $sql;die();
        $nameArray=sql_select($sql); 
        $po_data_arr=array(); 
        $job_data_arr=array(); 
        $job_allData_arr=array(); 
        $job_arr=array(); 
        $tot_rows=0; 
        $poIds='';
        if(count($nameArray)>0)
        {
            foreach($nameArray as $row)
            {
                $tot_rows++;
                $poIds.=$row[csf("po_id")].",";
                $job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
                
                $po_data_arr[$row[csf("po_id")]]=$row[csf("company_name")]."##".$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("job_no")]."##".$row[csf("style_ref_no")]."##".$row[csf("gmts_item_id")]."##".$row[csf("order_uom")]."##".$row[csf("ratio")]."##".$row[csf("po_number")]."##".$row[csf("po_qnty")]."##".$row[csf("pub_shipment_date")]."##".$row[csf("shiping_status")]."##".$row[csf("insert_date")]."##".$row[csf("po_received_date")]."##".$row[csf("plan_cut")]."##".$row[csf("file_no")]."##".$row[csf("grouping")]."##".$row[csf("is_confirmed")]."##".$row[csf('requ_no')]."##".$row[csf('req_date')];
                
                
            }
        }
        else
        {
            echo "3**".'Data Not Found'; die;
        }
        unset($nameArray);
        
        /*$auto_store_arr=array();
        $auto_store_sql=sql_select("select item_category_id, auto_update from variable_settings_production where company_name ='$company_name' and variable_list = 15");
        
        foreach($auto_store_sql as $row)
        {
            $auto_store_arr[$row[csf("item_category_id")]]=$row[csf("auto_update")];
        }
        unset($auto_store_sql);*/
        
         
        
        $style_id_cond="";
        if($type==2)
        {   
            if (trim($txt_search_string) != "") $style_id_cond=implode(",",array_filter(array_unique(explode(',',$poIds)))); else $style_id_cond="";
        }
        //print_r($job_allData_arr);
        //die;
        $poIds=chop($poIds,','); 
        $yarn_iss_po_cond=""; $purchase_po_cond=""; $trans_po_cond=""; $batch_po_cond=""; $dye_po_cond=""; $wo_po_cond="";$yarn_po_cond="";
        
        //$yarn_allo_po_cond="";  $grey_delivery_po_cond=""; $fin_delivery_po_cond="";  $fin_purchase_po_cond=""; $po_color_po_cond=""; $cons_po_cond=""; $tna_po_cond="";
        if($db_type==2 && $tot_rows>1000)
        {
            $yarn_iss_po_cond=" and (";
            $purchase_po_cond=" and (";
            $trans_po_cond=" and (";
            $batch_po_cond=" and (";
            $dye_po_cond=" and (";
            $wo_po_cond=" and (";
            $yarn_po_cond=" and (";
            
            $poIdsArr=array_chunk(explode(",",$poIds),999);
            foreach($poIdsArr as $ids)
            {
                $ids=implode(",",$ids);
                $yarn_iss_po_cond.=" a.po_breakdown_id in($ids) or ";
                $purchase_po_cond.=" c.po_breakdown_id in($ids) or ";
                $trans_po_cond.=" po_breakdown_id in($ids) or ";
                $batch_po_cond.=" b.po_id in($ids) or ";
                $dye_po_cond.=" b.po_id in($ids) or ";
                $wo_po_cond.=" b.po_break_down_id in($ids) or ";
                $yarn_po_cond.=" b.id in($ids) or ";
            }
            
            $yarn_iss_po_cond=chop($yarn_iss_po_cond,'or ');
            $yarn_iss_po_cond.=")";
            
            $purchase_po_cond=chop($purchase_po_cond,'or ');
            $purchase_po_cond.=")";
            
            $trans_po_cond=chop($trans_po_cond,'or ');
            $trans_po_cond.=")";
            
            $batch_po_cond=chop($batch_po_cond,'or ');
            $batch_po_cond.=")";
            
            $dye_po_cond=chop($dye_po_cond,'or ');
            $dye_po_cond.=")";
            
            $wo_po_cond=chop($wo_po_cond,'or ');
            $wo_po_cond.=")";
            
            $yarn_po_cond=chop($yarn_po_cond,'or ');
            $yarn_po_cond.=")";
        }
        else
        {
            $yarn_iss_po_cond=" and a.po_breakdown_id in ($poIds)";
            $purchase_po_cond=" and c.po_breakdown_id in ($poIds)";
            $trans_po_cond=" and po_breakdown_id in ($poIds)";
            $batch_po_cond=" and b.po_id in ($poIds)";
            $dye_po_cond=" and b.po_id in ($poIds)";
            $wo_po_cond=" and b.po_break_down_id in ($poIds)";
            $yarn_po_cond=" and b.id in ($poIds)";
        }

        //============================ load library data =================================
        // print_r($job_arr);
        $jobNo = "'".implode("','", $job_arr)."'";
        if ($db_type == 0) 
        {
        $fabric_desc_details = return_library_array("select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls where job_no in($jobNo) group by job_no", "job_no", "fabric_description");
        } 
        else 
        {
            $fabric_desc_details = return_library_array("select job_no, LISTAGG(cast(fabric_description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as fabric_description from wo_pre_cost_fabric_cost_dtls where job_no in($jobNo) group by job_no", "job_no", "fabric_description");
        }

    
        $batch_details = return_library_array("SELECT a.id, a.batch_no from pro_batch_create_mst a ,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $batch_po_cond", "id", "batch_no");

        $costing_per_id_library = array();
        $costing_date_library = array();
        $costing_sql = sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst where job_no in($jobNo)");
        foreach ($costing_sql as $row) {
            $costing_per_id_library[$row[csf('job_no')]] = $row[csf('costing_per')];
            $costing_date_library[$row[csf('job_no')]] = $row[csf('costing_date')];
        }    
            
        //----------------------------------------------------------------------    
        $poIdsArr=array_chunk(explode(",",$poIds),999);
        $p=1;
        foreach($poIdsArr as $ids)
        {

            if($p==1) $po_con .=" and (po_break_down_id in(".implode(',',$ids).")"; else  $po_con .=" OR po_break_down_id in(".implode(',',$ids).")";
            $p++;
        }
        $po_con .=")";
        $lapdipDataEc=sql_select("select job_no_mst, po_break_down_id, color_name_id, lapdip_no from wo_po_lapdip_approval_info where is_deleted=0 and status_active=1 $po_con");
        // echo "select job_no_mst, po_break_down_id, color_name_id, lapdip_no from wo_po_lapdip_approval_info where is_deleted=0 and status_active=1 $po_con";die();
        foreach($lapdipDataEc as $row)
        {
            $key=$row[csf('job_no_mst')].$row[csf('po_break_down_id')].$row[csf('color_name_id')];
            $lapdip_arr[$key]= $row[csf('lapdip_no')];
        }     
        unset($lapdipDataEc) ;
        //------------------------------------------------------------------------- 
        
        
        
        $booking_print_arr = array();
        $booking_print_sql = sql_select("select report_id, format_id from lib_report_template where template_name='$company_name' and module_id=2 and report_id in (1,2,3) and is_deleted=0 and status_active=1");
        foreach ($booking_print_sql as $print_id) 
        {
            $arr=explode(",",$print_id[csf('format_id')]);
            $cnt=count($arr);
            if($cnt>0){
                $cnt--;
               $booking_print_arr[$print_id[csf('report_id')]] = $arr[$cnt];
            }else{
                $booking_print_arr[$print_id[csf('report_id')]] =0;
            }
            //$booking_print_arr[$print_id[csf('report_id')]] = (int) $print_id[csf('format_id')];
        }
        // echo "<pre>";
        // print_r($booking_print_arr);
        // echo "</pre>";die;
        unset($booking_print_sql);

        $dataArrayYarn = array();
        $dataArrayYarnIssue = array();
        $yarn_sql = "SELECT a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, sum(a.avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls a,wo_po_break_down b where a.job_id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $yarn_po_cond group by a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id"; //, sum(cons_qnty) as qnty
        // echo $yarn_sql;die();
        $resultYarn = sql_select($yarn_sql);

        foreach ($resultYarn as $yarnRow) 
        {
            $dataArrayYarn[$yarnRow[csf('job_no')]] .= $yarnRow[csf('count_id')] . "**" . $yarnRow[csf('copm_one_id')] . "**" . $yarnRow[csf('percent_one')] . "**" . $yarnRow[csf('copm_two_id')] . "**" . $yarnRow[csf('percent_two')] . "**" . $yarnRow[csf('type_id')] . "**" . $yarnRow[csf('qnty')] . ",";
        }
        unset($resultYarn);

        $sql_yarn_iss = "select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
        sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
        sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
        from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 $yarn_iss_po_cond group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
                // echo $sql_yarn_iss;die();
        $dataArrayIssue = sql_select($sql_yarn_iss);
        foreach ($dataArrayIssue as $row_yarn_iss) 
        {
            $dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]] .= $row_yarn_iss[csf('yarn_count_id')] . "**" . $row_yarn_iss[csf('yarn_comp_type1st')] . "**" . $row_yarn_iss[csf('yarn_comp_percent1st')] . "**" . $row_yarn_iss[csf('yarn_comp_type2nd')] . "**" . $row_yarn_iss[csf('yarn_comp_percent2nd')] . "**" . $row_yarn_iss[csf('yarn_type')] . "**" . $row_yarn_iss[csf('issue_qnty')] . "**" . $row_yarn_iss[csf('return_qnty')] . ",";
        }
        unset($dataArrayIssue); 
        
        $greyPurchaseQntyArray = array(); $finish_purchase_qnty_arr=array();
        $sql_grey_purchase="select c.po_breakdown_id, c.quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22,2) and c.entry_form in (22,2) and c.trans_id !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $purchase_po_cond";
        // echo $sql_grey_purchase;die();
        
        $dataGreyArrayPurchase = sql_select($sql_grey_purchase);
        foreach ($dataGreyArrayPurchase as $purGreyRow)
        {
            $greyPurchaseQntyArray[$purGreyRow[csf('po_breakdown_id')]] +=$purGreyRow[csf('quantity')];
        }
        unset($dataGreyArrayPurchase);
        
        $sql_fin_purchase="select c.po_breakdown_id, c.color_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $purchase_po_cond group by c.po_breakdown_id, c.color_id";
                //and a.receive_basis<>9
        // echo $sql_fin_purchase;die();
        $dataArrayFinPurchase=sql_select($sql_fin_purchase);
        foreach($dataArrayFinPurchase as $finRow)
        {
            $finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]=$finRow[csf('finish_purchase')];
        }
        unset($dataArrayFinPurchase);
        
        $grey_receive_qnty_arr=array(); $grey_receive_return_qnty_arr=array(); $grey_issue_return_qnty_arr=array(); $grey_issue_qnty_arr=array(); $trans_qnty_arr=array();
        $trans_qnty_fin_arr=array(); $finish_receive_qnty_arr=array(); $finish_issue_qnty_arr=array(); $finish_recv_rtn_qnty_arr=array(); $finish_issue_rtn_qnty_arr=array();
        $grey_available_arr=array(); $finish_available_arr=array();
        $dataArrayTrans = sql_select("select trans_id, po_breakdown_id, color_id, entry_form, trans_type, quantity from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in (2,7,11,13,14,15,16,18,37,45,46,51,52,61,66,71,82,83,84,134) $trans_po_cond");
        foreach ($dataArrayTrans as $row) 
        {
            //knit
            if($row[csf('entry_form')]==2 || $row[csf('entry_form')]==45 && $row[csf('trans_type')]==3 || $row[csf('entry_form')]==51 && $row[csf('trans_type')]==4 || $row[csf('entry_form')]==16 || $row[csf('entry_form')]==61 || $row[csf('entry_form')]==11 || $row[csf('entry_form')]==13 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83)
            {
                if($row[csf('entry_form')]==2)  $grey_receive_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
                if($row[csf('entry_form')]==45 && $row[csf('trans_type')]==3) $grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
                if(($row[csf('entry_form')]==51 || $row[csf('entry_form')]==84) && $row[csf('trans_type')]==4) $grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
                if($row[csf('entry_form')]==16 || $row[csf('entry_form')]==61) $grey_issue_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
                
                if($row[csf('entry_form')]==11) 
                {
                    if($row[csf('trans_type')]==5 || $row[csf('trans_type')]==6)
                    {
                        $trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans'] += $row[csf('quantity')];
                    }
                }
                $grey_trns_out=0; $grey_trns_in=0;
                if($row[csf('entry_form')]==13 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83) 
                {
                    /*if($row[csf('trans_type')]==5 || $row[csf('trans_type')]==6)
                    {
                        $trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] += $row[csf('quantity')];
                    }*/
                    
                    if($row[csf('trans_type')]==5)
                    {
                        $trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] += $row[csf('quantity')];
                        if($row[csf('trans_id')]!=0) $grey_trns_in=$row[csf('quantity')];
                    }
                    if($row[csf('trans_type')]==6)
                    {
                        $trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] -= $row[csf('quantity')];
                        if($row[csf('trans_id')]!=0) $grey_trns_out=$row[csf('quantity')];
                    }              
                }
                
                $knit_avail=0; $grey_rec=0; $grey_purchase=0; $grey_rec_return=0; $net_grey_trns=0;
                if($row[csf('trans_id')]!=0) 
                {
                    if($row[csf('entry_form')]==2) $grey_rec= $row[csf('quantity')];
                    if($row[csf('entry_form')]==45 && $row[csf('trans_type')]==3) $grey_rec_return= $row[csf('quantity')];
                    $grey_purchase=$greyPurchaseQntyArray[$row[csf('po_breakdown_id')]]-$grey_rec_return;
                }
                $net_grey_trns=$grey_trns_in-$grey_trns_out;
                $knit_avail=$grey_rec+$grey_purchase+$net_grey_trns;
                $grey_available_arr[$row[csf('po_breakdown_id')]]+=$knit_avail;
            }
            //finish
            if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==134 || $row[csf('entry_form')]==66 || $row[csf('entry_form')]==18 || $row[csf('entry_form')]==71 || ($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) || ($row[csf('entry_form')]==52 && $row[csf('trans_type')]==4) || $row[csf('entry_form')]==37)
            {
                $finish_trns_out=0; $finish_trns_in=0;
                if($row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==134) 
                {
                    
                    if($row[csf('trans_type')]==5)
                    {
                        $trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']+= $row[csf('quantity')];
                        if($row[csf('trans_id')]!=0) $finish_trns_in=$row[csf('quantity')];
                    }
                    if($row[csf('trans_type')]==6)
                    {
                        $trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']-= $row[csf('quantity')];
                        if($row[csf('trans_id')]!=0) $finish_trns_out=$row[csf('quantity')];
                    }
                }
                
                if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==66) $finish_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
                if($row[csf('entry_form')]==18 || $row[csf('entry_form')]==71) $finish_issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
                if($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) $finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
                if($row[csf('entry_form')]==52 && $row[csf('trans_type')]==4) $finish_issue_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
                $finish_avail=0; $finish_rec=0; $finish_purchase=0; $finish_rec_return=0; $net_finish_trns=0;
                if($row[csf('trans_id')]!=0) 
                {
                    if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==37) 
                    {
                        $finish_rec= $row[csf('quantity')];
                        if($row[csf('entry_form')]==7)
                        {
                            $finish_purchase_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
                        }
                    }
                    if($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) $finish_rec_return= $row[csf('quantity')];
                }

                $net_finish_trns=$finish_trns_in-$finish_trns_out;

                $finish_avail=$finish_rec+$net_finish_trns - $finish_rec_return;
                $finish_available_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+=$finish_avail;
                //finish color arr

                if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==18 || $row[csf('entry_form')]==37)
                {
                    $po_color_arr[$row[csf('po_breakdown_id')]].=$row[csf('color_id')].',';
                }
            }
        }
        unset($dataArrayTrans);
        // echo "**<pre>";print_r($trans_qnty_arr);die;



        $batch_qnty_arr = return_library_array("select b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.batch_against<>2 and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_po_cond group by b.po_id", "po_id", "batch_qnty");

        $dye_qnty_arr = array();
        $sql_dye = "select b.po_id, a.color_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $dye_po_cond group by b.po_id, a.color_id";
        $resultDye = sql_select($sql_dye);
        foreach ($resultDye as $dyeRow) {
            $dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('color_id')]] = $dyeRow[csf('dye_qnty')];
        }
        unset($resultDye);

        $dataArrayWo = array();
       
        $sql_wo = "SELECT b.po_break_down_id, a.id, a.booking_no, a.booking_no_prefix_num, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search $wo_po_cond group by b.po_break_down_id, a.id, a.booking_no, a.booking_no_prefix_num, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id";
        $resultWo = sql_select($sql_wo);
        foreach ($resultWo as $woRow) 
        {
            $dataArrayWo[$woRow[csf('po_break_down_id')]] .= $woRow[csf('id')] . "**" . $woRow[csf('booking_no')] . "**" . $woRow[csf('insert_date')] . "**" . $woRow[csf('item_category')] . "**" . $woRow[csf('fabric_source')] . "**" . $woRow[csf('company_id')] . "**" . $woRow[csf('booking_type')] . "**" . $woRow[csf('booking_no_prefix_num')] . "**" . $woRow[csf('job_no')] . "**" . $woRow[csf('is_short')] . "**" . $woRow[csf('is_approved')] . "**" . $woRow[csf('fabric_color_id')] . "**" . $woRow[csf('req_qnty')] . "**" . $woRow[csf('grey_req_qnty')] . "**" . $woRow[csf('booking_no_prefix_num')] . ",";
            //$colorWiseReqQty_arr[$woRow[csf('po_break_down_id')]][$woRow[csf('fabric_color_id')]] += $woRow[csf('grey_req_qnty')];
            //$colorWiseReqQty_arr[$woRow[csf('po_break_down_id')]][$woRow[csf('fabric_color_id')]] += $woRow[csf('req_qnty')];
        }



        unset($resultWo);
        $tot_order_qnty = 0;
        $tot_required_qnty = 0;
        $tot_mkt_required = 0;
        $tot_yarn_issue_qnty = 0;
        $tot_balance = 0;
        $tot_fabric_req = 0;
        $tot_grey_recv_qnty = 0;
        $tot_grey_balance = 0;
        $tot_grey_available = 0;
        $tot_grey_issue = 0;
        $tot_batch_qnty = 0;
        $tot_color_wise_req = 0;
        $tot_dye_qnty = 0;
        $tot_fabric_recv = 0;
        $tot_fabric_purchase = 0;
        $tot_fabric_balance = 0;
        $tot_issue_to_cut_qnty = 0;
        $tot_fabric_available = 0;
        $tot_fabric_left_over = 0;
        $tot_knit_balance_qnty = 0;
        $tot_grey_inhand = 0;
        $tot_grey_issue_bl = 0;

        $buyer_name_array = array();
        $order_qty_array = array();
        $grey_required_array = array();
        $yarn_issue_array = array();
        $grey_issue_array = array();
        $fin_fab_Requi_array = array();
        $fin_fab_recei_array = array();
        $issue_to_cut_array = array();
        $yarn_balance_array = array();
        $grey_balance_array = array();
        $fin_balance_array = array();
        $knitted_array = array();
        $dye_qnty_array = array();
        $batch_qnty_array = array();

        $tot_sub_order_qnty=0;
        $tot_sub_required_qnty=0;

        //echo $sql;die;
        $template_id_arr = return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id", "po_number_id", "template_id");
        ob_start();
        ?>
        <fieldset style="width:<? echo $table_width + 30; ?>px;">   
            <table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $colspan ; ?>" style="font-size:16px"><strong><?php echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $colspan ; ?>" style="font-size:16px"><strong><? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date) . " To " . change_date_format($end_date); ?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header_1">
                <thead>
                    <tr>
                        <th colspan="13">Order Details</th>
                        <th colspan="4">Yarn Status</th>
                        
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        
                        <th width="130">Yarn Requisition NO</th>
                        <th width="80">Requisition Date</th>
                        <th width="100">Job Number</th>
                        <th width="120">Order Number</th>
                       
                        <th width="100">Order Status</th>
                        
                        
                        <th width="80">Buyer Name</th>
                        <th width="130">Style Ref.</th>
                       
                        <th width="140">Item Name</th>
                        <th width="100">Order Qnty</th>
                        <th width="80">Shipment Date</th>
                       
                        <th width="80">PO Received Date</th>
                        <th width="80">PO Entry Date</th>
                       
                            
                        <th width="70">Count</th>
                        <th width="110">Composition</th>
                        <th width="80">Type</th>
                        <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                      
                    </tr>
                </thead>
            </table>
            <?
            $colspan_excel = $colspan + 28;
            $colspan_excel_dtls = $colspan - 1;
           
           



            ?>
            <div style="width:<? echo $table_width + 20; ?>px; overflow-y:scroll; max-height:400px" id="scroll_body">
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                    <?
                    //echo 3333333;die;
                    
                    $nameArray = sql_select($sql);
                   

                    $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
                    $gmtsitemRatioArray = array();
                    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
                    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
                    {
                        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
                    }
                    unset($gmtsitemRatioSql);

                    $budget_cond = " and a.company_name=$cbo_company_name";
                    $budget_cond .= (str_replace("'", "", $cbo_buyer_name) > 0) ? " and a.buyer_name=$cbo_buyer_name" : "";
                    $budget_cond .= (str_replace("'", "", $txt_file_no) != '') ? " and b.file_no=$txt_file_no" : "";
                    $budget_cond .= (str_replace("'", "", $txt_ref_no) != '') ? " and b.grouping=$txt_ref_no" : "";
                    $budget_cond .= (str_replace("'", "", $txt_job_no) != '') ? " and a.job_no_prefix_num=$txt_job_no" : "";
                    if ($start_date != "" && $end_date != "") {
                        $budget_cond .= " and b.pub_shipment_date between '$start_date' and '$end_date'";
                    }

                    $sql_budget_data = "SELECT a.job_no AS JOB_NO ,b.id AS ID,c.item_number_id AS ITEM_NUMBER_ID,c.country_id AS COUNTRY_ID,c.color_number_id AS COLOR_NUMBER_ID,c.size_number_id AS SIZE_NUMBER_ID,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,c.country_ship_date AS COUNTRY_SHIP_DATE,d.id AS PRE_COST_DTLS_ID,d.fab_nature_id AS FAB_NATURE_ID,d.construction AS CONSTRUCTION, d.gsm_weight AS GSM_WEIGHT,e.cons AS CONS,e.requirment AS REQUIRMENT,f.id AS YARN_ID,f.count_id AS COUNT_ID,f.copm_one_id AS COPM_ONE_ID,f.percent_one AS PERCENT_ONE,f.type_id AS TYPE_ID,f.color AS COLOR,f.cons_ratio AS CONS_RATIO,f.cons_qnty AS CONS_QNTY,f.avg_cons_qnty AS AVG_CONS_QNTY,f.rate AS RATE,f.amount AS AMOUNT from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 $budget_cond";
                    //echo $sql_budget_data;
                    $budget_res = sql_select($sql_budget_data);
                    $yarn_des_data = array();
                    foreach ($budget_res as $val) 
                    {
                        $costingPer = $costing_per_arr[$val['JOB_NO']];
                        if($costingPer==1) $pcs_value=1*12;
                        else if($costingPer==2) $pcs_value=1*1;
                        else if($costingPer==3) $pcs_value=2*12;
                        else if($costingPer==4) $pcs_value=3*12;
                        else if($costingPer==5) $pcs_value=4*12;

                        $gmtsitemRatio  = $gmtsitemRatioArray[$val['JOB_NO']][$val['ITEM_NUMBER_ID']];
                        $consRatio      = $val['CONS_RATIO'];
                        $requirment     = $val['REQUIRMENT'];
                        $consQnty       = $requirment*$consRatio/100;
                        $reqQty         = ($val['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value); 
                        $yarn_des_data[$val['ID']][$val['COUNT_ID']][$val['COPM_ONE_ID']][$val['PERCENT_ONE']][$val['TYPE_ID']] += $reqQty;
                    }

                    $k = 1;
                    $i = 1;
                    
                    
                    
                   
                        /* ###########
                          all code repeated both if and else
                          if($chk_no_boking==1) display only no booking Order
                          else display All data
                          ############## */
                         // echo $chk_no_boking;

                        if ($chk_no_boking == 1) 
                        {   // check no booking 
                            $job_arr_sub=[];
                            foreach ($po_data_arr as $po_id=>$po_data) 
                            {
                                $nobooking_check = array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)));
                                
                                
                                    $template_id = $template_id_arr[$po_id];
                                    $ex_data=explode("##",$po_data);
                                    $company_id=$ex_data[0];
                                    $buyer_name=$ex_data[1];
                                    $job_no_prefix_num=$ex_data[2];
                                    $job_no=$ex_data[3];
                                    $style_ref_no=$ex_data[4];
                                    $gmts_item_id=$ex_data[5];
                                    $order_uom=$ex_data[6];
                                    $ratio=$ex_data[7];
                                    $po_number=$ex_data[8];
                                    $po_qnty=$ex_data[9];
                                    $pub_shipment_date=$ex_data[10];
                                    $shiping_status=$ex_data[11];
                                    $insert_date=$ex_data[12];
                                    $po_received_date=$ex_data[13];
                                    $plan_cut=$ex_data[14];
                                    $grouping=$ex_data[16];
                                    $file_no=$ex_data[15];
                                    $is_confirmed=$ex_data[17];
                                    $requ_no=$ex_data[18];
                                    $requ_date=$ex_data[19];
                                    
                                    $template_id=$template_id_arr[$po_id];
                                    
                                    $order_qnty_in_pcs=$po_qnty*$ratio;
                                    $plan_cut_qnty=$plan_cut*$ratio;
                                    $order_qty_array[$buyer_name]+=$order_qnty_in_pcs;
                                    
                                    $gmts_item='';
                                    $gmts_item_id=explode(",",$gmts_item_id);
                                    foreach($gmts_item_id as $item_id)
                                    {
                                        if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                                    }
                                    

                                   

                                    $dzn_qnty = 0;
                                    $balance = 0;
                                    $job_mkt_required = 0;
                                    $yarn_issued = 0;
                                    if ($costing_per_id_library[$job_no] == 1)
                                        $dzn_qnty = 12;
                                    else if ($costing_per_id_library[$job_no] == 3)
                                        $dzn_qnty = 12 * 2;
                                    else if ($costing_per_id_library[$job_no] == 4)
                                        $dzn_qnty = 12 * 3;
                                    else if ($costing_per_id_library[$job_no] == 5)
                                        $dzn_qnty = 12 * 4;
                                    else
                                        $dzn_qnty = 1;

                                    $dzn_qnty = $dzn_qnty * $ratio;

                                    $yarn_data_array = array();
                                    $mkt_required_array = array();
                                    $yarn_desc_array_for_popup = array();
                                    $yarn_desc_array = array();
                                    $yarn_iss_qnty_array = array();
                                    $s = 1;
                                   
                                    $yarn_descrip_data = $yarn_des_data[$po_id];

                                    $qnty = 0;
                                    foreach ($yarn_descrip_data as $count => $count_value) 
                                    {
                                        foreach ($count_value as $Composition => $composition_value) {
                                            foreach ($composition_value as $percent => $percent_value) {
                                                foreach ($percent_value as $typee => $type_value) {
                                                    //$yarnRow=explode("**",$yarnRow);
                                                    $count_id = $count; //$yarnRow[0];
                                                    $copm_one_id = $Composition; //$yarnRow[1];
                                                    $percent_one = $percent; //$yarnRow[2];
                                                    $copm_two_id = 0;
                                                    $percent_two = 0;
                                                    $type_id = $typee; //$yarnRow[5];
                                                    $qnty = $type_value; //$yarnRow[6];

                                                    $mkt_required = $qnty; //$plan_cut_qnty*($qnty/$dzn_qnty);
                                                    $mkt_required_array[$s] = $mkt_required;
                                                    $job_mkt_required += $mkt_required;

                                                    $yarn_data_array['count'][$s] = $yarn_count_details[$count_id];
                                                    $yarn_data_array['type'][$s] = $yarn_type[$type_id];

                                                  
                                                    $compos = $composition[$copm_one_id] . " " . $percent_one . " %" . " " . $composition[$copm_two_id];

                                                    $yarn_data_array['comp'][] = $compos;

                                                    $yarn_desc_array[$s] = $yarn_count_details[$count_id] . " " . $compos . " " . $yarn_type[$type_id];

                                                    $yarn_desc_for_popup = $count_id . "__" . $copm_one_id . "__" . $percent_one . "__" . $copm_two_id . "__" . $percent_two . "__" . $type_id;
                                                    $yarn_desc_array_for_popup[$s] = $yarn_desc_for_popup;

                                                    $s++;
                                                }
                                            }
                                        }
                                    }

                                    $dataYarnIssue = explode(",", substr($dataArrayYarnIssue[$po_id], 0, -1));
                                    foreach ($dataYarnIssue as $yarnIssueRow) {
                                        $yarnIssueRow = explode("**", $yarnIssueRow);
                                        $yarn_count_id = $yarnIssueRow[0];
                                        $yarn_comp_type1st = $yarnIssueRow[1];
                                        $yarn_comp_percent1st = $yarnIssueRow[2];
                                        $yarn_comp_type2nd = $yarnIssueRow[3];
                                        $yarn_comp_percent2nd = $yarnIssueRow[4];
                                        $yarn_type_id = $yarnIssueRow[5];
                                        $issue_qnty = $yarnIssueRow[6];
                                        $return_qnty = $yarnIssueRow[7];

                                        if ($yarn_comp_percent2nd != 0) {
                                            $compostion_not_req = $composition[$yarn_comp_type1st] . " " . $yarn_comp_percent1st . " % " . $composition[$yarn_comp_type2nd] . " " . $yarn_comp_percent2nd . " %";
                                        } else {
                                            $compostion_not_req = $composition[$yarn_comp_type1st] . " " . $yarn_comp_percent1st . " % " . $composition[$yarn_comp_type2nd];
                                        }

                                        $desc = $yarn_count_details[$yarn_count_id] . " " . $compostion_not_req . " " . $yarn_type[$yarn_type_id];

                                        $net_issue_qnty = $issue_qnty - $return_qnty;
                                        $yarn_issued += $net_issue_qnty;
                                        if (!in_array($desc, $yarn_desc_array)) {
                                            $yarn_iss_qnty_array['not_req'] += $net_issue_qnty;
                                        } else {
                                            $yarn_iss_qnty_array[$desc] += $net_issue_qnty;
                                        }
                                    }

                                    $grey_purchase_qnty = $greyPurchaseQntyArray[$po_id] - $grey_receive_return_qnty_arr[$po_id];
                                    $grey_recv_qnty = $grey_receive_qnty_arr[$po_id];
                                    $grey_fabric_issue = $grey_issue_qnty_arr[$po_id] - $grey_issue_return_qnty_arr[$po_id];

                                    if (($cbo_discrepancy == 1 && $grey_recv_qnty > $yarn_issued) || ($cbo_discrepancy == 0)) {
                                        if ($i % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";
                                        $buyer_name_array[$buyer_name] = $buyer_short_name_library[$buyer_name];

                                        $booking_array = array();
                                        $color_data_array = array();  //$colorGreyReqQty_arr = array();
                                        $required_qnty = 0;
                                        $req_purc_qnty = 0;
                                        $main_booking = '';
                                        $sample_booking = '';
                                        $main_booking_excel = '';
                                        $sample_booking_excel = '';
                                        $all_book_prefix_no = '';
                                        $dataArray = array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)));
                             
                                        $all_book_prefix_no = implode(",", array_unique(explode(",", chop($all_book_prefix_no, ","))));
                                        $finish_color = array_unique(explode(",", $po_color_arr[$po_id]));
                                        foreach ($finish_color as $color_id) {
                                            if ($color_id > 0) {
                                                $color_data_array[$color_id] += 0;
                                            }
                                        }

                                        $yarn_issue_array[$buyer_name] += $yarn_issued;

                                        $grey_required_array[$buyer_name] += $required_qnty;

                                        $net_trans_yarn = $trans_qnty_arr[$po_id]['yarn_trans'];
                                        $yarn_issue_array[$buyer_name] += $net_trans_yarn;

                                        $balance = $required_qnty - ($yarn_issued + $net_trans_yarn + $req_purc_qnty);

                                        $yarn_balance_array[$buyer_name] += $balance;

                                        $knitted_array[$buyer_name] += $grey_recv_qnty + $grey_purchase_qnty;
                                        $net_trans_knit = $trans_qnty_arr[$po_id]['knit_trans'];
                                        $knitted_array[$buyer_name] += $net_trans_knit;

                                        $grey_balance = $required_qnty - ($grey_recv_qnty + $net_trans_knit + $grey_purchase_qnty);

                                        $grey_balance_array[$buyer_name] += $grey_balance;
                                        $grey_issue_array[$buyer_name] += $grey_fabric_issue;

                                        $batch_qnty = $batch_qnty_arr[$po_id];
                                        $batch_qnty_array[$buyer_name] += $batch_qnty;

                                        $grey_available = $grey_recv_qnty + $grey_purchase_qnty + $net_trans_knit;

                                        $knit_balance_qnty = $required_qnty - ($grey_recv_qnty + $req_purc_qnty);
                                        $grey_inhand_qnty = $grey_available - $grey_fabric_issue;
                                        $grey_iss_balance_qnty = $required_qnty - $grey_fabric_issue;

                                       

                                        $tot_mkt_required += $job_mkt_required;
                                        $tot_yarn_issue_qnty += $yarn_issued;
                                        $tot_fabric_req += $required_qnty;
                                        $tot_balance += $balance;
                                        $tot_grey_recv_qnty += $grey_recv_qnty;
                                        $tot_knit_balance_qnty += $knit_balance_qnty;
                                        $tot_grey_purchase_qnty += $grey_purchase_qnty;
                                        $tot_grey_available += $grey_available;
                                        $tot_grey_balance += $grey_balance;
                                        $tot_grey_issue += $grey_fabric_issue;
                                        $tot_grey_inhand += $grey_inhand_qnty;
                                        $tot_grey_issue_bl += $grey_iss_balance_qnty;
                                        $tot_batch_qnty += $batch_qnty;

                                        if ($required_qnty > $job_mkt_required)
                                            $bgcolor_grey_td = '#FF0000';
                                        $bgcolor_grey_td = '';

                                       // $po_entry_date = date('d-m-Y', strtotime($row[csf('insert_date')]));
                                        $po_entry_date = date('d-m-Y', strtotime($insert_date));
                                        $costing_date = $costing_date_library[$job_no];
                                        $tot_color = count($color_data_array);
                                        
                                        //15.03.2020 added by zaman
                                        $bgColorArr['color'] = $discrepancy_td_color;
                                        $bgColorArr['req_qty'] = $required_qnty;
                                        $bgColorArr['rcv_qty'] = $grey_recv_qnty;
                                        $discrepancy_td_color_zs = getBgColorZs($bgColorArr);
                                        //end
                                        if(count($job_arr_sub) && !in_array($job_no, $job_arr_sub))
                                        {
                                            ?>
                                           <tr bgcolor="#ddd">
                                               <td width="40"></td>
                                               <td width="130"></td>
                                               <td width="80"></td>
                                              
                                               <td width="100"></td>
                                               <td width="120"></td>
                                              
                                               <td width='100'></td>
                                             
                                               <td width="80"></td>
                                               <td width="130" title='here iam'></td>
                                              
                                              
                                               <td width="140" align="right">Total</td>
                                               <td width="100" align="right"><? echo number_format($tot_sub_order_qnty, 0); ?></td>
                                               <td width="80"></td>
                                             
                                               <td width="80"></td>
                                               <td width="80"></td>
                                             
                                                   
                                               <td width="70"></td>
                                               <td width="110"></td>
                                               <td width="80"></td>
                                               <td width="100" align="right"><? echo number_format($tot_sub_required_qnty, 2); ?></td>
                                              
                                           </tr>
                                           <?
                                            $tot_sub_order_qnty=0;
                                            $tot_sub_required_qnty=0;
                                        }
                                        $tot_order_qnty += $order_qnty_in_pcs;
                                        $tot_sub_order_qnty+= $order_qnty_in_pcs;
                                        if ($tot_color > 0) 
                                        {
                                            $z = 1;
                                            foreach ($color_data_array as $key => $value) {
                                                if ($z == 1) {
                                                    $display_font_color = "";
                                                    $font_end = "";
                                                } else {
                                                    $display_font_color = "<font style='display:none' color='$bgcolor'>";
                                                    $font_end = "</font>";
                                                }

                                                ?>
                                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                                                    <td width="40"><? echo $display_font_color . $i . $font_end; ?></td>
                                                    
                                                    <td width="130"><p><? echo $display_font_color . $requ_no . $font_end; ?></p></td>
                                                    <td width="80"><p><? echo $display_font_color . change_date_format($requ_date) . $font_end; ?></p></td>
                                                    <td width="100" align="center"><p><? echo $display_font_color . $job_no . $font_end; ?></p></td>
                                                    <td width="120">
                                                        <p>
                                                            <a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>', '<? echo $po_id; ?>', '<? echo $template_id; ?>', '<? echo $tna_process_type; ?>');"><? echo $display_font_color .$po_number. $font_end; ?></a>
                                                        </p>
                                                    </td>
                                                    <td width="100"><p><? echo $display_font_color . $order_status[$is_confirmed] . $font_end ?></p></td>
                                                    <td width="80"><p><? echo $display_font_color . $buyer_short_name_library[$buyer_name] . $font_end; ?></p></td>
                                                    <td width="130"><p><? echo $display_font_color . $style_ref_no . $font_end; ?></p></td>
                                                   
                                                    <td width="140"><p><? echo $display_font_color . $gmts_item . $font_end; ?></p></td>
                                                    <td width="100" align="right"><? if ($z == 1) echo number_format($order_qnty_in_pcs, 0, '.', ''); ?></td>
                                                    <td width="80" align="center"><? echo $display_font_color . change_date_format($pub_shipment_date) . $font_end; ?></td>
                                                    <td width="80" align="center"><? echo $display_font_color . change_date_format($po_received_date) . $font_end; ?></td>
                                                    <td width="80" align="center"><? echo $display_font_color . $po_entry_date . $font_end; ?></td>
                                                   
                                                    <td width="70">
                                                        <p>
                                                            <?
                                                           
                                                            $d = 1;
                                                            foreach ($yarn_data_array['count'] as $yarn_count_value) {
                                                                if ($d != 1) {
                                                                    echo $display_font_color . "<hr/>" . $font_end;
                                                                    
                                                                }

                                                                echo $display_font_color . $yarn_count_value . $font_end;
                                                               
                                                                $d++;
                                                            }

                                                           
                                                            ?>
                                                        </p>
                                                    </td>
                                                    <td width="110" style="word-break:break-all;">
                                                        <p>
                                                            <?
                                                            $d = 1;
                                                            foreach ($yarn_data_array['comp'] as $yarn_composition_value) {
                                                                if ($d != 1) {
                                                                    echo $display_font_color . "<hr/>" . $font_end;
                                                                   
                                                                }
                                                                echo $display_font_color . $yarn_composition_value . $font_end;
                                                               
                                                                $d++;
                                                            }

                                                           
                                                            ?>
                                                        </p>
                                                    </td>
                                                    <td width="80">
                                                        <p>
                                                            <?
                                                            $d = 1;
                                                            foreach ($yarn_data_array['type'] as $yarn_type_value) {
                                                                if ($d != 1) {
                                                                    echo $display_font_color . "<hr/>" . $font_end;
                                                                   
                                                                }

                                                                echo $display_font_color . $yarn_type_value . $font_end;
                                                                
                                                                $d++;
                                                            }

                                                           
                                                            ?>
                                                        </p>
                                                    </td>
                                                    <td width="100" align="right">
                                                        <?
                                                        if ($z == 1) {
                                                            echo "<font color='$bgcolor' style='display:none'>" . number_format(array_sum($mkt_required_array), 2, '.', '') . "</font>\n";
                                                            $d = 1;
                                                            foreach ($mkt_required_array as $mkt_required_value) {
                                                                if ($d != 1) {
                                                                    echo "<hr/>";
                                                                   
                                                                }

                                                                $yarn_desc_for_popup_req = explode("__", $yarn_desc_array_for_popup[$d]);
                                                                 $tot_required_qnty+=$mkt_required_value;
                                                                  $tot_sub_required_qnty+=$mkt_required_value;
                                                                ?>
                                                                <a href="##" onClick="openmypage('<? echo $po_id; ?>', 'yarn_req', '<? echo $yarn_desc_for_popup_req[0]; ?>', '<? echo $yarn_desc_for_popup_req[1]; ?>', '<? echo $yarn_desc_for_popup_req[2]; ?>', '<? echo $yarn_desc_for_popup_req[3]; ?>', '<? echo $yarn_desc_for_popup_req[4]; ?>', '<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value, 2, '.', ''); ?></a>
                                                                <?
                                                               
                                                                $d++;
                                                            }
                                                        }

                                                        
                                                        

                                                       
                                                        ?>
                                                    </td>
                                                    
                                                </tr>
                                                <?
                                               
                                                $z++;
                                                $k++;
                                            }
                                        }
                                        else 
                                        {
                                           
                                            ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                                                <td width="40"><? echo $i; ?></td>
                                                
                                                <td width="130"><p><? echo $requ_no; ?></p></td>
                                                <td width="80"><p><? echo change_date_format($requ_date); ?></p></td>

                                                <td width="100" align="center"><? echo $job_no; ?></td>
                                                <td width="120">
                                                    <p>
                                                        <a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>', '<? echo $po_id; ?>', '<? echo $template_id; ?>', '<? echo $tna_process_type; ?>');"><? echo $po_number; //$display_font_color.$row[csf('po_number')].$font_end;         ?></a>
                                                    </p>
                                                </td>
                                                <td width="100"><p><? echo $order_status[$is_confirmed] ?></p></>
                                                <td width="80"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
                                                <td width="130"><p><? echo $style_ref_no; ?></p></td>
                                                
                                                <td width="140"><p><? echo $gmts_item; ?></p></td>
                                                <td width="100" align="right"><? echo number_format($order_qnty_in_pcs, 0, '.', ''); ?></td>
                                                <td width="80" align="center"><? echo change_date_format($pub_shipment_date); ?></td>
                                                <td width="80" align="center"><? echo change_date_format($po_received_date); ?></td>
                                                <td width="80" align="center"><? echo $po_entry_date; ?></td>
                                               
                                                <td width="70">
                                                    <?
                                                  
                                                    $d = 1;
                                                    foreach ($yarn_data_array['count'] as $yarn_count_value) {
                                                        if ($d != 1) {
                                                            echo "<hr/>";
                                                            
                                                        }

                                                        echo $yarn_count_value;
                                                       

                                                        $d++;
                                                    }

                                                  
                                                    ?>
                                                </td>
                                                <td width="110" style="word-break:break-all;">
                                                    <p>
                                                        <?
                                                        $d = 1;
                                                        foreach ($yarn_data_array['comp'] as $yarn_composition_value) {
                                                            if ($d != 1) {
                                                                echo "<hr/>";
                                                               
                                                            }

                                                            echo $yarn_composition_value;
                                                           

                                                            $d++;
                                                        }

                                                       
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="80">
                                                    <p>
                                                        <?
                                                        $d = 1;
                                                        foreach ($yarn_data_array['type'] as $yarn_type_value) {
                                                            if ($d != 1) {
                                                                echo "<hr/>";
                                                                
                                                            }

                                                            echo $yarn_type_value;
                                                           

                                                            $d++;
                                                        }

                                                      
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="100" align="right">
                                                    <?
                                                    echo "<font color='$bgcolor' style='display:none'>" . number_format(array_sum($mkt_required_array), 2, '.', '') . "</font>\n";
                                                    $d = 1;
                                                    foreach ($mkt_required_array as $mkt_required_value) {
                                                        if ($d != 1) {
                                                            echo "<hr/>";
                                                           
                                                        }

                                                        $yarn_desc_for_popup_req = explode("__", $yarn_desc_array_for_popup[$d]);
                                                        $tot_required_qnty+=$mkt_required_value;
                                                         $tot_sub_required_qnty+=$mkt_required_value;
                                                        ?>
                                                        <a href="##" onClick="openmypage('<? echo $po_id; ?>', 'yarn_req', '<? echo $yarn_desc_for_popup_req[0]; ?>', '<? echo $yarn_desc_for_popup_req[1]; ?>', '<? echo $yarn_desc_for_popup_req[2]; ?>', '<? echo $yarn_desc_for_popup_req[3]; ?>', '<? echo $yarn_desc_for_popup_req[4]; ?>', '<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value, 2, '.', ''); ?></a>
                                                        <?
                                                       
                                                        $d++;
                                                    }

                                                   
                                                    ?>
                                                </td>
                                               
                                            </tr>
                                            <?
                                           
                                            $k++;
                                        }
                                        $i++;
                                        $job_arr_sub[]=$job_no;
                                    }
                                
                            }
                            if(count($job_arr_sub))
                            {
                                ?>
                                <tr bgcolor="#ddd">
                                    <td width="40"></td>
                                    <td width="130"></td>
                                    <td width="80"></td>
                                   
                                    <td width="100"></td>
                                    <td width="120"></td>
                                   
                                    <td width='100'></td>
                                  
                                    <td width="80"></td>
                                    <td width="130" title='here iam'></td>
                                   
                                   
                                    <td width="140" align="right">Total</td>
                                    <td width="100" align="right"><? echo number_format($tot_sub_order_qnty, 0); ?></td>
                                    <td width="80"></td>
                                  
                                    <td width="80"></td>
                                    <td width="80"></td>
                                  
                                        
                                    <td width="70"></td>
                                    <td width="110"></td>
                                    <td width="80"></td>
                                    <td width="100" align="right"><? echo number_format($tot_sub_required_qnty, 2); ?></td>
                                   
                                </tr>
                                <?
                                 $tot_sub_order_qnty=0;
                                 $tot_sub_required_qnty=0;
                            }
                        } 
                        else 
                        {
                            //print_r($po_data_arr);
                            $job_arr_sub=[];
                            foreach ($po_data_arr as $po_id=>$po_data) 
                            {
                                $template_id = $template_id_arr[$po_id];
                                
                                $ex_data=explode("##",$po_data);
                                $company_id=$ex_data[0];
                                $buyer_name=$ex_data[1];
                                $job_no_prefix_num=$ex_data[2];
                                $job_no=$ex_data[3];
                                $style_ref_no=$ex_data[4];
                                $gmts_item_id=$ex_data[5];
                                $order_uom=$ex_data[6];
                                $ratio=$ex_data[7];
                                $po_number=$ex_data[8];
                                $po_qnty=$ex_data[9];
                                $pub_shipment_date=$ex_data[10];
                                $shiping_status=$ex_data[11];
                                $insert_date=$ex_data[12];
                                $po_received_date=$ex_data[13];
                                $plan_cut=$ex_data[14];
                                $grouping=$ex_data[16];
                                $file_no=$ex_data[15];
                                $is_confirmed=$ex_data[17];
                                $requ_no=$ex_data[18];
                                $requ_date=$ex_data[19];
                                

                                $order_qnty_in_pcs = $po_qnty * $ratio;
                                $plan_cut_qnty = $plan_cut * $ratio;
                                $order_qty_array[$buyer_name] += $order_qnty_in_pcs;

                                $gmts_item = '';
                                $gmts_item_id = explode(",", $gmts_item_id);
                                foreach ($gmts_item_id as $item_id) 
                                {
                                    if ($gmts_item == "")
                                        $gmts_item = $garments_item[$item_id];
                                    else
                                        $gmts_item .= "," . $garments_item[$item_id];
                                }

                                $dzn_qnty = 0;
                                $balance = 0;
                                $job_mkt_required = 0;
                                $yarn_issued = 0;
                                if ($costing_per_id_library[$job_no] == 1)
                                    $dzn_qnty = 12;
                                else if ($costing_per_id_library[$job_no] == 3)
                                    $dzn_qnty = 12 * 2;
                                else if ($costing_per_id_library[$job_no] == 4)
                                    $dzn_qnty = 12 * 3;
                                else if ($costing_per_id_library[$job_no] == 5)
                                    $dzn_qnty = 12 * 4;
                                else
                                    $dzn_qnty = 1;

                                $dzn_qnty = $dzn_qnty * $ratio;

                                $yarn_data_array = array();
                                $mkt_required_array = array();
                                $yarn_desc_array_for_popup = array();
                                $yarn_desc_array = array();
                                $yarn_iss_qnty_array = array();
                                $s = 1;
                                $dataYarn = explode(",", substr($dataArrayYarn[$job_no], 0, -1));

                                //echo $po_id;
                                $yarn_descrip_data = $yarn_des_data[$po_id];
                                $qnty = 0;
                                foreach ($yarn_descrip_data as $count => $count_value) 
                                {
                                    foreach ($count_value as $Composition => $composition_value) 
                                    {
                                        foreach ($composition_value as $percent => $percent_value) 
                                        {
                                            foreach ($percent_value as $typee => $type_value) 
                                            {
                                                //$yarnRow=explode("**",$yarnRow);
                                                $count_id = $count; //$yarnRow[0];
                                                $copm_one_id = $Composition; //$yarnRow[1];
                                                $percent_one = $percent; //$yarnRow[2];
                                                $copm_two_id = 0;
                                                $percent_two = 0;
                                                $type_id = $typee; //$yarnRow[5];
                                                $qnty = $type_value; //$yarnRow[6];

                                                $mkt_required = $qnty; //$plan_cut_qnty*($qnty/$dzn_qnty);
                                                $mkt_required_array[$s] = $mkt_required;
                                                $job_mkt_required += $mkt_required;

                                                $yarn_data_array['count'][$s] = $yarn_count_details[$count_id];
                                                $yarn_data_array['type'][$s] = $yarn_type[$type_id];

                                                //if($percent_two!=0)
                                                //{$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";}
                                                //else
                                                //{$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];}
                                                $compos = $composition[$copm_one_id] . " " . $percent_one . " %" . " " . $composition[$copm_two_id];

                                                $yarn_data_array['comp'][] = $compos;

                                                $yarn_desc_array[$s] = $yarn_count_details[$count_id] . " " . $compos . " " . $yarn_type[$type_id];

                                                $yarn_desc_for_popup = $count_id . "__" . $copm_one_id . "__" . $percent_one . "__" . $copm_two_id . "__" . $percent_two . "__" . $type_id;
                                                $yarn_desc_array_for_popup[$s] = $yarn_desc_for_popup;

                                                $s++;
                                            }
                                        }
                                    }
                                }

                                $dataYarnIssue = explode(",", substr($dataArrayYarnIssue[$po_id], 0, -1));
                                foreach ($dataYarnIssue as $yarnIssueRow) 
                                {
                                    $yarnIssueRow = explode("**", $yarnIssueRow);
                                    $yarn_count_id = $yarnIssueRow[0];
                                    $yarn_comp_type1st = $yarnIssueRow[1];
                                    $yarn_comp_percent1st = $yarnIssueRow[2];
                                    $yarn_comp_type2nd = $yarnIssueRow[3];
                                    $yarn_comp_percent2nd = $yarnIssueRow[4];
                                    $yarn_type_id = $yarnIssueRow[5];
                                    $issue_qnty = $yarnIssueRow[6];
                                    $return_qnty = $yarnIssueRow[7];

                                    if ($yarn_comp_percent2nd != 0) {
                                        $compostion_not_req = $composition[$yarn_comp_type1st] . " " . $yarn_comp_percent1st . " % " . $composition[$yarn_comp_type2nd] . " " . $yarn_comp_percent2nd . " %";
                                    } else {
                                        $compostion_not_req = $composition[$yarn_comp_type1st] . " " . $yarn_comp_percent1st . " % " . $composition[$yarn_comp_type2nd];
                                    }

                                    $desc = $yarn_count_details[$yarn_count_id] . " " . $compostion_not_req . " " . $yarn_type[$yarn_type_id];

                                    $net_issue_qnty = $issue_qnty - $return_qnty;
                                    $yarn_issued += $net_issue_qnty;
                                    if (!in_array($desc, $yarn_desc_array)) {
                                        $yarn_iss_qnty_array['not_req'] += $net_issue_qnty;
                                    } else {
                                        $yarn_iss_qnty_array[$desc] += $net_issue_qnty;
                                    }
                                }

                                $grey_purchase_qnty = $greyPurchaseQntyArray[$po_id] - $grey_receive_return_qnty_arr[$po_id];
                                $grey_recv_qnty = $grey_receive_qnty_arr[$po_id];
                                $grey_fabric_issue = $grey_issue_qnty_arr[$po_id] - $grey_issue_return_qnty_arr[$po_id];

                                if (($cbo_discrepancy == 1 && $grey_recv_qnty > $yarn_issued) || ($cbo_discrepancy == 0)) 
                                {
                                    if ($i % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";
                                    $buyer_name_array[$buyer_name] = $buyer_short_name_library[$buyer_name];

                                    $booking_array = array();
                                    $color_data_array = array();
                                    $required_qnty = 0;
                                    $req_purc_qnty = 0;
                                    $main_booking = '';
                                    $sample_booking = '';
                                    $main_booking_excel = '';
                                    $sample_booking_excel = '';
                                    $all_book_prefix_no = '';
                                    $dataArray = array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)));
                                   

                                  
                                    $finish_color = array_unique(explode(",", $po_color_arr[$po_id]));
                                    foreach ($finish_color as $color_id) {
                                        if ($color_id > 0) {
                                            $color_data_array[$color_id] += 0;
                                        }
                                    }

                                    $yarn_issue_array[$buyer_name] += $yarn_issued;

                                    $grey_required_array[$buyer_name] += $required_qnty;

                                    $net_trans_yarn = $trans_qnty_arr[$po_id]['yarn_trans'];
                                    $yarn_issue_array[$buyer_name] += $net_trans_yarn;

                                    $balance = $required_qnty - ($yarn_issued + $net_trans_yarn + $req_purc_qnty);

                                    $yarn_balance_array[$buyer_name] += $balance;
                                    
                                    $grey_available =0;
                                    //$grey_available =$grey_available_arr[$po_id];

                                    //$knitted_array[$buyer_name] += $grey_available;//$grey_recv_qnty + $grey_purchase_qnty;
                                    $net_trans_knit = $trans_qnty_arr[$po_id]['knit_trans'];
                                    //$knitted_array[$buyer_name] += $net_trans_knit;

                                    //$grey_balance = $required_qnty - ($grey_recv_qnty + $net_trans_knit + $grey_purchase_qnty);
                                    $grey_available = $grey_purchase_qnty + $net_trans_knit;
                                    $grey_balance = $required_qnty -$grey_available;

                                    $grey_balance_array[$buyer_name] += $grey_balance;
                                    $grey_issue_array[$buyer_name] += $grey_fabric_issue;

                                    $batch_qnty = $batch_qnty_arr[$po_id];
                                    $batch_qnty_array[$buyer_name] += $batch_qnty;

                                    $knitted_array[$buyer_name] += $grey_available;

                                    //if($auto_store_arr[13]==1) $grey_available = $grey_recv_qnty + $grey_purchase_qnty + $net_trans_knit; else $grey_available = $grey_purchase_qnty + $net_trans_knit;

                                    $knit_balance_qnty = $required_qnty - ($grey_recv_qnty + $req_purc_qnty);
                                    $grey_inhand_qnty = $grey_available - $grey_fabric_issue;
                                    $grey_iss_balance_qnty = $required_qnty - $grey_fabric_issue;

                                   
                                    $tot_mkt_required += $job_mkt_required;
                                    $tot_yarn_issue_qnty += $yarn_issued;
                                    $tot_fabric_req += $required_qnty;
                                    $tot_balance += $balance;
                                    $tot_grey_recv_qnty += $grey_recv_qnty;
                                    $tot_knit_balance_qnty += $knit_balance_qnty;
                                    $tot_grey_purchase_qnty += $grey_purchase_qnty;
                                    $tot_grey_available += $grey_available;
                                    $tot_grey_balance += $grey_balance;
                                    $tot_grey_issue += $grey_fabric_issue;
                                    $tot_grey_inhand += $grey_inhand_qnty;
                                    $tot_grey_issue_bl += $grey_iss_balance_qnty;
                                    $tot_batch_qnty += $batch_qnty;

                                    if ($required_qnty > $job_mkt_required)
                                        $bgcolor_grey_td = '#FF0000';
                                    $bgcolor_grey_td = '';

                                    $po_entry_date = date('d-m-Y', strtotime($insert_date));
                                    $costing_date = $costing_date_library[$job_no];
                                    $tot_color = count($color_data_array);
                                    
                                    //15.03.2020 added by zaman
                                    $bgColorArr['color'] = $discrepancy_td_color;
                                    $bgColorArr['req_qty'] = $required_qnty;
                                    $bgColorArr['rcv_qty'] = $grey_recv_qnty;
                                    $discrepancy_td_color_zs = getBgColorZs($bgColorArr);
                                    //end
                                     if(count($job_arr_sub) && !in_array($job_no, $job_arr_sub))
                                    {
                                        ?>
                                       <tr bgcolor="#ddd">
                                           <td width="40"></td>
                                           <td width="130"></td>
                                           <td width="80"></td>
                                          
                                           <td width="100"></td>
                                           <td width="120"></td>
                                          
                                           <td width='100'></td>
                                         
                                           <td width="80"></td>
                                           <td width="130" title='here iam'></td>
                                          
                                          
                                           <td width="140" align="right">Total</td>
                                           <td width="100" align="right"><? echo number_format($tot_sub_order_qnty, 0); ?></td>
                                           <td width="80"></td>
                                         
                                           <td width="80"></td>
                                           <td width="80"></td>
                                         
                                               
                                           <td width="70"></td>
                                           <td width="110"></td>
                                           <td width="80"></td>
                                           <td width="100" align="right"><? echo number_format($tot_sub_required_qnty, 2); ?></td>
                                          
                                       </tr>
                                       <?
                                        $tot_sub_order_qnty=0;
                                        $tot_sub_required_qnty=0;
                                    }
                                    $tot_order_qnty += $order_qnty_in_pcs;
                                    $tot_sub_order_qnty += $order_qnty_in_pcs;
                                    if ($tot_color > 0) 
                                    {
                                        $z = 1;
                                        foreach ($color_data_array as $key => $value) 
                                        {
                                            if ($z == 1) {
                                                $display_font_color = "";
                                                $font_end = "";
                                            } else {
                                                $display_font_color = "<font style='display:none' color='$bgcolor'>";
                                                $font_end = "</font>";
                                            }

                                           
                                            //if( $main_booking=='No Booking' ) { echo "ada"; }
                                            ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                                                <td width="40"><? echo $display_font_color . $i . $font_end; ?></td>
                                               
                                                <td width="130"><p><? echo $display_font_color . $requ_no . $font_end; ?></p></td>
                                                <td width="80"><p><? echo $display_font_color . change_date_format($requ_date) . $font_end; ?></p></td>
                                                <td width="100" align="center"><? echo $display_font_color . $job_no . $font_end; ?></td>
                                                <td width="120">
                                                    <p>
                                                        <a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>', '<? echo $po_id; ?>', '<? echo $template_id; ?>', '<? echo $tna_process_type; ?>');"><? echo $display_font_color . $po_number . $font_end; ?></a>
                                                    </p>
                                                </td>
                                                <td width="100"><? echo $display_font_color . $order_status[$is_confirmed] . $font_end ?></td>
                                                <td width="80"><p><? echo $display_font_color . $buyer_short_name_library[$buyer_name] . $font_end; ?></p></td>
                                                <td width="130"><p><? echo $display_font_color . $style_ref_no . $font_end; ?></p></td>
                                                
                                                <td width="140"><p><? echo $display_font_color . $gmts_item . $font_end; ?></p></td>
                                                <td width="100" align="right"><? if ($z == 1) echo number_format($order_qnty_in_pcs, 0, '.', ''); ?></td>
                                                <td width="80" align="center"><? echo $display_font_color . change_date_format($pub_shipment_date) . $font_end; ?></td>
                                                <td width="80" align="center"><? echo $display_font_color . change_date_format($po_received_date) . $font_end; ?></td>
                                                <td width="80" align="center"><? echo $display_font_color . $po_entry_date . $font_end; ?></td>
                                               
                                                <td width="70">
                                                    <p>
                                                        <?
                                                        $html .= "<td>";
                                                        $d = 1;
                                                        foreach ($yarn_data_array['count'] as $yarn_count_value) {
                                                            if ($d != 1) {
                                                                echo $display_font_color . "<hr/>" . $font_end;
                                                               
                                                            }

                                                            echo $display_font_color . $yarn_count_value . $font_end;
                                                            
                                                            $d++;
                                                        }

                                                       
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="110" style="word-break:break-all;">
                                                    <p>
                                                        <?
                                                        $d = 1;
                                                        foreach ($yarn_data_array['comp'] as $yarn_composition_value) {
                                                            if ($d != 1) {
                                                                echo $display_font_color . "<hr/>" . $font_end;
                                                                
                                                                    
                                                            }
                                                            echo $display_font_color . $yarn_composition_value . $font_end;
                                                            
                                                            $d++;
                                                        }

                                                       
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="80">
                                                    <p>
                                                        <?
                                                        $d = 1;
                                                        foreach ($yarn_data_array['type'] as $yarn_type_value) {
                                                            if ($d != 1) {
                                                                echo $display_font_color . "<hr/>" . $font_end;
                                                               
                                                            }

                                                            echo $display_font_color . $yarn_type_value . $font_end;
                                                            
                                                            $d++;
                                                        }

                                                    
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="100" align="right">
                                                    <?
                                                    if ($z == 1) {
                                                        echo "<font color='$bgcolor' style='display:none'>" . number_format(array_sum($mkt_required_array), 2, '.', '') . "</font>\n";
                                                        $d = 1;
                                                        foreach ($mkt_required_array as $mkt_required_value) {
                                                            if ($d != 1) {
                                                                echo "<hr/>";
                                                                
                                                            }

                                                            $yarn_desc_for_popup_req = explode("__", $yarn_desc_array_for_popup[$d]);
                                                            $tot_required_qnty+=$mkt_required_value;
                                                            $tot_sub_required_qnty+=$mkt_required_value;
                                                            ?>
                                                            <a href="##" onClick="openmypage('<? echo $po_id; ?>', 'yarn_req', '<? echo $yarn_desc_for_popup_req[0]; ?>', '<? echo $yarn_desc_for_popup_req[1]; ?>', '<? echo $yarn_desc_for_popup_req[2]; ?>', '<? echo $yarn_desc_for_popup_req[3]; ?>', '<? echo $yarn_desc_for_popup_req[4]; ?>', '<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value, 2, '.', ''); ?></a>
                                                            <?
                                                           
                                                            $d++;
                                                        }
                                                    }

                                                   
                                                    ?>
                                                </td>
                                               
                                            </tr>
                                            <?
                                           
                                            $z++;
                                            $k++;
                                        }
                                    }
                                    else 
                                    {
                                        
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                                            <td width="40"><? echo $i; ?></td>
                                           
                                            <td width="130"><p><? echo $requ_no; ?></p></td>
                                            <td width="80"><p><? echo change_date_format($requ_date); ?></p></td>
                                            <td width="100" align="center"><? echo $job_no; ?></td>
                                            <td width="120">
                                                <p>
                                                    <a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>', '<? echo $po_id; ?>', '<? echo $template_id; ?>', '<? echo $tna_process_type; ?>');"><? echo $po_number; //$display_font_color.$po_number.$font_end;      ?></a>
                                                </p>
                                            </td>
                                            <td width="100"><p><? echo $order_status[$is_confirmed] ?></p></td>
                                            <td width="80"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
                                            <td width="130"><p><? echo $style_ref_no; ?></p></td>
                                           
                                             <td width="140"><p><? echo $display_font_color . $gmts_item . $font_end; ?></p></td>
                                            <td width="100" align="right"><? echo number_format($order_qnty_in_pcs, 0, '.', ''); ?></td>
                                            <td width="80" align="center"><? echo change_date_format($pub_shipment_date); ?></td>
                                            <td width="80" align="center"><? echo change_date_format($po_received_date); ?></td>
                                            <td width="80" align="center"><? echo $po_entry_date; ?></td>
                                            
                                            <td width="70">
                                                <?
                                                
                                                $d = 1;
                                                foreach ($yarn_data_array['count'] as $yarn_count_value) {
                                                    if ($d != 1) {
                                                        echo "<hr/>";
                                                        
                                                    }

                                                    echo $yarn_count_value;
                                                   

                                                    $d++;
                                                }

                                               
                                                ?>
                                            </td>
                                            <td width="110" style="word-break:break-all;">
                                                <p>
                                                    <?
                                                    $d = 1;
                                                    foreach ($yarn_data_array['comp'] as $yarn_composition_value) {
                                                        if ($d != 1) {
                                                            echo "<hr/>";
                                                            
                                                        }

                                                        echo $yarn_composition_value;
                                                       

                                                        $d++;
                                                    }

                                                    
                                                    ?>
                                                </p>
                                            </td>
                                            <td width="80">
                                                <p>
                                                    <?
                                                    $d = 1;
                                                    foreach ($yarn_data_array['type'] as $yarn_type_value) {
                                                        if ($d != 1) {
                                                            echo "<hr/>";
                                                           
                                                        }

                                                        echo $yarn_type_value;
                                                       

                                                        $d++;
                                                    }

                                                    
                                                    ?>
                                                </p>
                                            </td>
                                            <td width="100" align="right">
                                                <?
                                                echo "<font color='$bgcolor' style='display:none'>" . number_format(array_sum($mkt_required_array), 2, '.', '') . "</font>\n";
                                                $d = 1;
                                                foreach ($mkt_required_array as $mkt_required_value) {
                                                    if ($d != 1) {
                                                        echo "<hr/>";
                                                       
                                                    }

                                                    $yarn_desc_for_popup_req = explode("__", $yarn_desc_array_for_popup[$d]);
                                                    $tot_required_qnty+=$mkt_required_value;
                                                    $tot_sub_required_qnty+=$mkt_required_value;
                                                    ?>
                                                    <a href="##" onClick="openmypage('<? echo $po_id; ?>', 'yarn_req', '<? echo $yarn_desc_for_popup_req[0]; ?>', '<? echo $yarn_desc_for_popup_req[1]; ?>', '<? echo $yarn_desc_for_popup_req[2]; ?>', '<? echo $yarn_desc_for_popup_req[3]; ?>', '<? echo $yarn_desc_for_popup_req[4]; ?>', '<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value, 2, '.', ''); ?></a>
                                                    <?
                                                   
                                                    $d++;
                                                }

                                               
                                                ?>
                                            </td>
                                           
                                        </tr>
                                        <?
                                       
                                        $k++;
                                    }
                                    $job_arr_sub[]=$job_no;
                                    $i++;
                                }
                            }

                            if(count($job_arr_sub) )
                            {
                                ?>
                               <tr bgcolor="#ddd">
                                   <td width="40"></td>
                                   <td width="130"></td>
                                   <td width="80"></td>
                                  
                                   <td width="100"></td>
                                   <td width="120"></td>
                                  
                                   <td width='100'></td>
                                 
                                   <td width="80"></td>
                                   <td width="130" title='here iam'></td>
                                  
                                  
                                   <td width="140" align="right">Total</td>
                                   <td width="100" align="right"><? echo number_format($tot_sub_order_qnty, 0); ?></td>
                                   <td width="80"></td>
                                 
                                   <td width="80"></td>
                                   <td width="80"></td>
                                 
                                       
                                   <td width="70"></td>
                                   <td width="110"></td>
                                   <td width="80"></td>
                                   <td width="100" align="right"><? echo number_format($tot_sub_required_qnty, 2); ?></td>
                                  
                               </tr>
                               <?
                                $tot_sub_order_qnty=0;
                                $tot_sub_required_qnty=0;
                            }
                        }

                        // end main query
                    
                    
                    ?>
                </table>
            </div>
            <?
           
           

           

            ?>
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <tfoot>
                    
                    <tr>
                        <th width="40"></th>
                        <th width="130"></th>
                        <th width="80"></th>
                       
                        <th width="100"></th>
                        <th width="120"></th>
                       
                        <th width='100'></th>
                      
                        <th width="80"></th>
                        <th width="130" title='here iam'></th>
                       
                       
                        <th width="140">Grand Total</th>
                        <th width="100" id="value_tot_order_qnty"><? echo number_format($tot_order_qnty, 0); ?></th>
                        <th width="80"></th>
                      
                        <th width="80"></th>
                        <th width="80"></th>
                      
                            
                        <th width="70"></th>
                        <th width="110"></th>
                        <th width="80"></th>
                        <th width="100" id="value_tot_yarn_rec"><? echo number_format($tot_required_qnty, 2); ?></th>
                       
                    </tr>
                </tfoot>
            </table>
            <br />
            <?

          

            ?>
           
        </fieldset>
        <? //echo "string";die;
    }

    foreach (glob("$user_id*.xls") as $filename) 
        {
            if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename=$user_id."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc,ob_get_contents());
        //$filename=$user_id."_".$name.".xls";
        echo "$total_data####$filename####h####1";
   
    exit();
}



if ($action == "Shipment_date")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <div align="center">
        <fieldset style="width:670px">
            <table border="1" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" width="640">
                <thead>
                    <tr>
                        <th colspan="6">Order Details</th>
                    </tr>
                    <tr>
                        <th width="130">PO No</th>
                        <th width="120">PO Qnty</th>
                        <th width="90">Shipment Date</th>
                        <th width="90">PO Receive Date</th>
                        <th width="90">PO Entry Date</th>
                        <th>Shipping Status</th>
                    </tr>
                </thead>
                <?
                $i = 1;
                $total_order_qnty = 0;
                $sql = "select a.job_no, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(" . str_replace("'", "", $order_id) . ") order by b.pub_shipment_date, b.id";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $order_qnty = $row[csf('po_qnty')] * $row[csf('ratio')];
                    $total_order_qnty += $order_qnty;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td width="130"><p><? echo $row[csf('po_number')]; ?></p> </td>
                        <td width="120" align="right"><?
                            echo number_format($order_qnty, 0);
                            ;
                            ?></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <td width="90" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                        <td width="90" align="center"><? echo date('d-m-Y', strtotime($row[csf('insert_date')])); ?></td>
                        <td><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tfoot>
                <th>Total</th>
                <th><? echo number_format($total_order_qnty, 2); ?></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                </tfoot>
            </table>
        </fieldset>  
    </div> 
    <?
    exit();
}

if ($action == "yarn_req") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:850px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:845px; margin-left:10px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="8"><b>Required Qnty Info</b></th>
                </thead>
                <thead>
                <th width="40">SL</th>
                <th width="120">Order No.</th>
                <th width="120">Buyer Name</th>
                <th width="90">Cons/Dzn</th>
                <th width="110">Order Qnty</th>
                <th width="110">Plan Cut Qnty</th>
                <th width="110">Required Qnty</th>
                <th>Shipment Date</th>
                </thead>
            </table>
            <div style="width:830px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
                    <?
                    if ($yarn_count != 0)
                        $yarn_count_cond = "and c.count_id='$yarn_count'";
                    else
                        $yarn_count_cond = "";
                    if ($yarn_comp_type1st != 0)
                        $yarn_comp_type1st_cond = "and c.copm_one_id='$yarn_comp_type1st'";
                    else
                        $yarn_comp_type1st_cond = "";
                    if ($yarn_comp_percent1st != 0 || $yarn_comp_percent1st != '')
                        $yarn_comp_percent1st_cond = "and c.percent_one='$yarn_comp_percent1st'";
                    else
                        $yarn_comp_percent1st_cond = "";
                    if ($yarn_comp_type2nd != 0 || $yarn_comp_type2nd != "")
                        $yarn_comp_type2nd_cond = "and c.copm_two_id='$yarn_comp_type2nd'";
                    else
                        $yarn_comp_type2nd_cond = "";
                    if ($yarn_type_id != 0)
                        $yarn_type_id_cond = "and c.type_id='$yarn_type_id'";
                    else
                        $yarn_type_id_cond = "";

                    $i = 1;
                    $tot_req_qnty = 0;
                    $sql = "select a.buyer_name, a.job_no, a.total_set_qnty as ratio, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut, sum(c.avg_cons_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id in($order_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $yarn_count_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond $yarn_comp_type2nd_cond  $yarn_type_id_cond group by b.id, a.buyer_name, a.job_no, a.total_set_qnty, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut"; //sum(c.cons_qnty)
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $dzn_qnty = 0;
                        $required_qnty = 0;
                        $order_qnty = 0;
                        if ($costing_per_id_library[$row[csf('job_no')]] == 1)
                            $dzn_qnty = 12;
                        else if ($costing_per_id_library[$row[csf('job_no')]] == 3)
                            $dzn_qnty = 12 * 2;
                        else if ($costing_per_id_library[$row[csf('job_no')]] == 4)
                            $dzn_qnty = 12 * 3;
                        else if ($costing_per_id_library[$row[csf('job_no')]] == 5)
                            $dzn_qnty = 12 * 4;
                        else
                            $dzn_qnty = 1;

                        $order_qnty = $row[csf('po_quantity')] * $row[csf('ratio')];
                        $plan_cut_qnty = $row[csf('plan_cut')];
                        $required_qnty = $plan_cut_qnty * ($row[csf('qnty')] / $dzn_qnty);
                        $tot_req_qnty += $required_qnty;
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="120"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="90" align="right"><p><? echo number_format($row[csf('qnty')], 2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($order_qnty, 0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($plan_cut_qnty, 0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($required_qnty, 2); ?></p></td>
                            <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <th align="right" colspan="6">Total</th>
                    <th align="right"><? echo number_format($tot_req_qnty, 2); ?></th>
                    <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>   
    <?
    exit();
}

if ($action == "yarn_issue") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    ?>
    <script>

        function print_window()
        {
            // document.getElementById('scroll_body').style.overflow = "auto";
            // document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            // document.getElementById('scroll_body').style.overflowY = "scroll";
            // document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:1150px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:1145px; margin-left:3px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="12"><b>Yarn Issue</b></th>
                </thead>
                <thead>
                <th width="105">Issue Id</th>
                <th width="90">Issue To</th>
                <th width="105">Booking No</th>
                <th width="80">Challan No</th>
                <th width="70">Brand</th>
                <th width="60">Lot No</th>
                <th width="75">Issue Date</th>
                <th width="80">Yarn Type</th>
                <th width="90">Issue Qnty (In)</th>
                <th width="90">Issue Qnty (Out)</th>
                <th width="90">&nbsp;</th>
                <th>&nbsp;</th>
                </thead>
                <?
                $i = 1;
                $total_yarn_issue_qnty = 0;
                $total_yarn_issue_qnty_out = 0;
                $sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    if ($row[csf('knit_dye_source')] == 1) {
                        $issue_to = $company_library[$row[csf('knit_dye_company')]];
                    } else if ($row[csf('knit_dye_source')] == 3) {
                        $issue_to = $supplier_details[$row[csf('knit_dye_company')]];
                    } else
                        $issue_to = "&nbsp;";

                    $yarn_issued = $row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
                            <?
                            if ($row[csf('knit_dye_source')] != 3) {
                                echo number_format($yarn_issued, 2);
                                $total_yarn_issue_qnty += $yarn_issued;
                            } else
                                echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right"  width="90">
                            <?
                            if ($row[csf('knit_dye_source')] == 3) {
                                echo number_format($yarn_issued, 2);
                                $total_yarn_issue_qnty_out += $yarn_issued;
                            } else
                                echo "&nbsp;";
                            ?>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty, 2); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <thead>
                <th colspan="12"><b>Yarn Return</b></th>
                </thead>
                <thead>
                <th width="105">Return Id</th>
                <th width="90">Return From</th>
                <th width="105">Booking No</th>
                <th width="80">Challan No</th>
                <th width="70">Brand</th>
                <th width="60">Lot No</th>
                <th width="75">Return Date</th>
                <th width="80">Yarn Type</th>
                <th width="90">Return Qnty (In)</th>
                <th width="90">Return Qnty (Out)</th>
                <th width="90">Return Reject Qty(In)</th>
                <th>Return Reject Qty(Out)</th>
                </thead>
                <?
                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
                $sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, sum(b.reject_qty) as returned_rej_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    if ($row[csf('knitting_source')] == 1) {
                        $return_from = $company_library[$row[csf('knitting_company')]];
                    } else if ($row[csf('knitting_source')] == 3) {
                        $return_from = $supplier_details[$row[csf('knitting_company')]];
                    } else
                        $return_from = "&nbsp;";

                    $yarn_returned = $row[csf('returned_qnty')];
                    $yarn_returned_reject=$row[csf('returned_rej_qnty')];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
                            <?
                            if ($row[csf('knitting_source')] != 3) {
                                echo number_format($yarn_returned, 2);
                                $total_yarn_return_qnty += $yarn_returned;
                            } else
                                echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
                            <?
                            if ($row[csf('knitting_source')] == 3) {
                                echo number_format($yarn_returned, 2);
                                $total_yarn_return_qnty_out += $yarn_returned;
                            } else
                                echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right" width="90">
                            <?
                            if ($row[csf('knitting_source')] != 3) {
                                echo number_format($yarn_returned_reject, 2, '.', '');
                                $total_yarn_return_reject_qnty += $yarn_returned_reject;
                            } else
                                echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
                            <?
                            if ($row[csf('knitting_source')] == 3) {
                                echo number_format($yarn_returned_reject, 2, '.', '');
                                $total_yarn_return_reject_qnty_out += $yarn_returned_reject;
                            } else
                                echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty - $total_yarn_return_qnty, 2); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out - $total_yarn_return_qnty_out, 2); ?></td>
                    <td></td>
                    <td></td>

                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>    
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "yarn_issue_not_backup") // 27-08-2020
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $yarn_desc_array = explode(",", $yarn_count);
    //print_r($yarn_desc_array);
    ?>
    <script>

        function print_window()
        {

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>   
    <div style="width:1150px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:1150px; margin-left:3px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="12"><b>Yarn Issue</b></th>
                </thead>
                <thead>
                <th width="105">Issue Id</th>
                <th width="90">Issue To</th>
                <th width="105">Booking No</th>
                <th width="70">Challan No</th>
                <th width="75">Issue Date</th>
                <th width="70">Brand</th>
                <th width="60">Lot No</th>
                <th width="180">Yarn Description</th>
                <th width="90">Issue Qnty (In)</th>
                <th width="90">Issue Qnty (Out)</th>
                <th width="90">&nbsp;</th>
                <th>&nbsp;</th>
                </thead>
                <?
                $i = 1;
                $total_yarn_issue_qnty = 0;
                $total_yarn_issue_qnty_out = 0;
                $yarn_desc_array_for_return = array();
                $sql_yarn_iss = "select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in ($order_id) and a.entry_form in(3,9) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
                $dataArrayIssue = sql_select($sql_yarn_iss);
                foreach ($dataArrayIssue as $row_yarn_iss) {
                    if ($row_yarn_iss[csf('yarn_comp_percent2nd')] != 0) {
                        $compostion_not_req = $composition[$row_yarn_iss[csf('yarn_comp_type1st')]] . " " . $row_yarn_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row_yarn_iss[csf('yarn_comp_type2nd')]] . " " . $row_yarn_iss[csf('yarn_comp_percent2nd')] . " %";
                    } else {
                        $compostion_not_req = $composition[$row_yarn_iss[csf('yarn_comp_type1st')]] . " " . $row_yarn_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row_yarn_iss[csf('yarn_comp_type2nd')]];
                    }

                    $desc = $yarn_count_details[$row_yarn_iss[csf('yarn_count_id')]] . " " . $compostion_not_req . " " . $yarn_type[$row_yarn_iss[csf('yarn_type')]];

                    $yarn_desc_for_return = $row_yarn_iss[csf('yarn_count_id')] . "__" . $row_yarn_iss[csf('yarn_comp_type1st')] . "__" . $row_yarn_iss[csf('yarn_comp_percent1st')] . "__" . $row_yarn_iss[csf('yarn_comp_type2nd')] . "__" . $row_yarn_iss[csf('yarn_comp_percent2nd')] . "__" . $row_yarn_iss[csf('yarn_type')];

                    $yarn_desc_array_for_return[$desc] = $yarn_desc_for_return;

                    if (!in_array($desc, $yarn_desc_array)) {
                        $sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='" . $row_yarn_iss[csf('yarn_count_id')] . "' and c.yarn_comp_type1st='" . $row_yarn_iss[csf('yarn_comp_type1st')] . "' and c.yarn_comp_percent1st='" . $row_yarn_iss[csf('yarn_comp_percent1st')] . "' and c.yarn_comp_type2nd='" . $row_yarn_iss[csf('yarn_comp_type2nd')] . "' and c.yarn_comp_percent2nd='" . $row_yarn_iss[csf('yarn_comp_percent2nd')] . "' and c.yarn_type='" . $row_yarn_iss[csf('yarn_type')] . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                        $result = sql_select($sql);
                        foreach ($result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knit_dye_source')] == 1) {
                                $issue_to = $company_library[$row[csf('knit_dye_company')]];
                            } else if ($row[csf('knit_dye_source')] == 3) {
                                $issue_to = $supplier_details[$row[csf('knit_dye_company')]];
                            } else {
                                $issue_to = "&nbsp;";
                            }

                            $yarn_issued = $row[csf('issue_qnty')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                                <td width="90"><p><? echo $issue_to; ?></p></td>
                                <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                                <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                                <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                                <td align="right" width="90">
                                    <?
                                    if ($row[csf('knit_dye_source')] != 3) {
                                        echo number_format($yarn_issued, 2, '.', '');
                                        $total_yarn_issue_qnty += $yarn_issued;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right" width="90">
                                    <?
                                    if ($row[csf('knit_dye_source')] == 3) {
                                        echo number_format($yarn_issued, 2, '.', '');
                                        $total_yarn_issue_qnty_out += $yarn_issued;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right" width="90">&nbsp;</td>
                                <td align="right">&nbsp;</td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <thead>
                <th colspan="12"><b>Yarn Return</b></th>
                </thead>
                <thead>
                <th width="105">Return Id</th>
                <th width="90">Return From</th>
                <th width="105">Booking No</th>
                <th width="70">Challan No</th>
                <th width="75">Return Date</th>
                <th width="70">Brand</th>
                <th width="60">Lot No</th>
                <th width="180">Yarn Description</th>
                <th width="90">Return Qnty (In)</th>
                <th width="90">Return Qnty (Out)</th>
                <th width="90">Return Reject Qty(In)</th>
                <th>Return Reject Qty(Out)</th>
                </thead>
                <?
                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
                foreach ($yarn_desc_array_for_return as $key => $value) {
                    if (!in_array($key, $yarn_desc_array)) {
                        $desc = explode("__", $value);
                        $yarn_count = $desc[0];
                        $yarn_comp_type1st = $desc[1];
                        $yarn_comp_percent1st = $desc[2];
                        $yarn_comp_type2nd = $desc[3];
                        $yarn_comp_percent2nd = $desc[4];
                        $yarn_type_id = $desc[5];

                        $sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, sum(b.reject_qty) as returned_rej_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                        $result = sql_select($sql);
                        foreach ($result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knitting_source')] == 1) {
                                $return_from = $company_library[$row[csf('knitting_company')]];
                            } else if ($row[csf('knitting_source')] == 3) {
                                $return_from = $supplier_details[$row[csf('knitting_company')]];
                            } else
                                $return_from = "&nbsp;";

                            $yarn_returned = $row[csf('returned_qnty')];
                            $yarn_returned_reject = $row[csf('returned_rej_qnty')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                                <td width="90"><p><? echo $return_from; ?></p></td>
                                <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                                <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                                <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                                <td align="right" width="90">
                                    <?
                                    if ($row[csf('knitting_source')] != 3) {
                                        echo number_format($yarn_returned, 2, '.', '');
                                        $total_yarn_return_qnty += $yarn_returned;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right"  width="90">
                                    <?
                                    if ($row[csf('knitting_source')] == 3) {
                                        echo number_format($yarn_returned, 2, '.', '');
                                        $total_yarn_return_qnty_out += $yarn_returned;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                 <td align="right" width="90">
                                    <?
                                    if ($row[csf('knitting_source')] != 3) {
                                        echo number_format($yarn_returned_reject, 2, '.', '');
                                        $total_yarn_return_reject_qnty += $yarn_returned_reject;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right">
                                    <?
                                    if ($row[csf('knitting_source')] == 3) {
                                        echo number_format($yarn_returned_reject, 2, '.', '');
                                        $total_yarn_return_reject_qnty_out += $yarn_returned_reject;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty - $total_yarn_return_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out - $total_yarn_return_qnty_out, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_return_reject_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_return_reject_qnty_out, 2, '.', ''); ?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>    
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "yarn_issue_not") 
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $yarn_desc_array = explode(",", $yarn_count);
    //print_r($yarn_desc_array);
    ?>
    <script>

        function print_window()
        {

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>   
    <div style="width:1150px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:1150px; margin-left:3px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="12"><b>Yarn Issue</b></th>
                </thead>
                <thead>
                <th width="105">Issue Id</th>
                <th width="90">Issue To</th>
                <th width="105">Booking No</th>
                <th width="70">Challan No</th>
                <th width="75">Issue Date</th>
                <th width="70">Brand</th>
                <th width="60">Lot No</th>
                <th width="180">Yarn Description</th>
                <th width="90">Issue Qnty (In)</th>
                <th width="90">Issue Qnty (Out)</th>
                <th width="90">&nbsp;</th>
                <th>&nbsp;</th>
                </thead>
                <?
                $i = 1;
                $total_yarn_issue_qnty = 0;
                $total_yarn_issue_qnty_out = 0;
                $yarn_desc_array_for_return = array();
                $sql_yarn_iss = "select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in ($order_id) and a.entry_form in(3,9) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
                $dataArrayIssue = sql_select($sql_yarn_iss);
                $yarn_info_array = array();
                foreach ($dataArrayIssue as $row_yarn_iss) 
                {
                    if ($row_yarn_iss[csf('yarn_comp_percent2nd')] != 0) {
                        $compostion_not_req = $composition[$row_yarn_iss[csf('yarn_comp_type1st')]] . " " . $row_yarn_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row_yarn_iss[csf('yarn_comp_type2nd')]] . " " . $row_yarn_iss[csf('yarn_comp_percent2nd')] . " %";
                    } else {
                        $compostion_not_req = $composition[$row_yarn_iss[csf('yarn_comp_type1st')]] . " " . $row_yarn_iss[csf('yarn_comp_percent1st')] . " %" . " " . $composition[$row_yarn_iss[csf('yarn_comp_type2nd')]];
                    }

                    $desc = $yarn_count_details[$row_yarn_iss[csf('yarn_count_id')]] . " " . $compostion_not_req . " " . $yarn_type[$row_yarn_iss[csf('yarn_type')]];

                    $yarn_desc_for_return = $row_yarn_iss[csf('yarn_count_id')] . "__" . $row_yarn_iss[csf('yarn_comp_type1st')] . "__" . $row_yarn_iss[csf('yarn_comp_percent1st')] . "__" . $row_yarn_iss[csf('yarn_comp_type2nd')] . "__" . $row_yarn_iss[csf('yarn_comp_percent2nd')] . "__" . $row_yarn_iss[csf('yarn_type')];

                    $yarn_desc_array_for_return[$desc] = $yarn_desc_for_return;

                    if (!in_array($desc, $yarn_desc_array)) 
                    {
                        $yarn_info_array['yarn_count_id'][$row_yarn_iss[csf('yarn_count_id')]] = $row_yarn_iss[csf('yarn_count_id')];
                        $yarn_info_array['comp_type1st'][$row_yarn_iss[csf('yarn_comp_type1st')]] = $row_yarn_iss[csf('yarn_comp_type1st')];
                        $yarn_info_array['comp_percent1st'][$row_yarn_iss[csf('yarn_comp_percent1st')]] = $row_yarn_iss[csf('yarn_comp_percent1st')];
                        $yarn_info_array['comp_type2nd'][$row_yarn_iss[csf('yarn_comp_type2nd')]] = $row_yarn_iss[csf('yarn_comp_type2nd')];
                        $yarn_info_array['comp_percent2nd'][$row_yarn_iss[csf('yarn_comp_percent2nd')]] = $row_yarn_iss[csf('yarn_comp_percent2nd')];
                        $yarn_info_array['yarn_type'][$row_yarn_iss[csf('yarn_type')]] = $row_yarn_iss[csf('yarn_type')];
                    }

                }
                $yarn_count_id      = implode(",", $yarn_info_array['yarn_count_id']);
                $comp_type1st       = implode(",", $yarn_info_array['comp_type1st']);
                $comp_percent1st    = implode(",", $yarn_info_array['comp_percent1st']);
                $comp_type2nd       = implode(",", $yarn_info_array['comp_type2nd']);
                $comp_percent2nd    = implode(",", $yarn_info_array['comp_percent2nd']);
                $yarn_type          = implode(",", $yarn_info_array['yarn_type']);

                    
                $sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id in($yarn_count_id) and c.yarn_comp_type1st in($comp_type1st) and c.yarn_comp_percent1st in($comp_percent1st) and c.yarn_comp_type2nd in($comp_type2nd) and c.yarn_comp_percent2nd in($comp_percent2nd) and c.yarn_type in($yarn_type) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    if ($row[csf('knit_dye_source')] == 1) {
                        $issue_to = $company_library[$row[csf('knit_dye_company')]];
                    } else if ($row[csf('knit_dye_source')] == 3) {
                        $issue_to = $supplier_details[$row[csf('knit_dye_company')]];
                    } else {
                        $issue_to = "&nbsp;";
                    }

                    $yarn_issued = $row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
                            <?
                            if ($row[csf('knit_dye_source')] != 3) {
                                echo number_format($yarn_issued, 2, '.', '');
                                $total_yarn_issue_qnty += $yarn_issued;
                            } else
                                echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right" width="90">
                            <?
                            if ($row[csf('knit_dye_source')] == 3) {
                                echo number_format($yarn_issued, 2, '.', '');
                                $total_yarn_issue_qnty_out += $yarn_issued;
                            } else
                                echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right" width="90">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <?
                    $i++;
                }
                    
                                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <thead>
                <th colspan="12"><b>Yarn Return</b></th>
                </thead>
                <thead>
                <th width="105">Return Id</th>
                <th width="90">Return From</th>
                <th width="105">Booking No</th>
                <th width="70">Challan No</th>
                <th width="75">Return Date</th>
                <th width="70">Brand</th>
                <th width="60">Lot No</th>
                <th width="180">Yarn Description</th>
                <th width="90">Return Qnty (In)</th>
                <th width="90">Return Qnty (Out)</th>
                <th width="90">Return Reject Qty(In)</th>
                <th>Return Reject Qty(Out)</th>
                </thead>
                <?
                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
                foreach ($yarn_desc_array_for_return as $key => $value) {
                    if (!in_array($key, $yarn_desc_array)) {
                        $desc = explode("__", $value);
                        $yarn_count = $desc[0];
                        $yarn_comp_type1st = $desc[1];
                        $yarn_comp_percent1st = $desc[2];
                        $yarn_comp_type2nd = $desc[3];
                        $yarn_comp_percent2nd = $desc[4];
                        $yarn_type_id = $desc[5];

                        $sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, sum(b.reject_qty) as returned_rej_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                        $result = sql_select($sql);
                        foreach ($result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf('knitting_source')] == 1) {
                                $return_from = $company_library[$row[csf('knitting_company')]];
                            } else if ($row[csf('knitting_source')] == 3) {
                                $return_from = $supplier_details[$row[csf('knitting_company')]];
                            } else
                                $return_from = "&nbsp;";

                            $yarn_returned = $row[csf('returned_qnty')];
                            $yarn_returned_reject = $row[csf('returned_rej_qnty')];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                                <td width="90"><p><? echo $return_from; ?></p></td>
                                <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                                <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                                <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                                <td align="right" width="90">
                                    <?
                                    if ($row[csf('knitting_source')] != 3) {
                                        echo number_format($yarn_returned, 2, '.', '');
                                        $total_yarn_return_qnty += $yarn_returned;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right"  width="90">
                                    <?
                                    if ($row[csf('knitting_source')] == 3) {
                                        echo number_format($yarn_returned, 2, '.', '');
                                        $total_yarn_return_qnty_out += $yarn_returned;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                 <td align="right" width="90">
                                    <?
                                    if ($row[csf('knitting_source')] != 3) {
                                        echo number_format($yarn_returned_reject, 2, '.', '');
                                        $total_yarn_return_reject_qnty += $yarn_returned_reject;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                                <td align="right">
                                    <?
                                    if ($row[csf('knitting_source')] == 3) {
                                        echo number_format($yarn_returned_reject, 2, '.', '');
                                        $total_yarn_return_reject_qnty_out += $yarn_returned_reject;
                                    } else
                                        echo "&nbsp;";
                                    ?>
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty - $total_yarn_return_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out - $total_yarn_return_qnty_out, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_return_reject_qnty, 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_return_reject_qnty_out, 2, '.', ''); ?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>    
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "grey_receive") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
    ?>
    <script>

        var tableFilters = {
            col_operation: {
                id: ["value_receive_qnty_in", "value_receive_qnty_out", "value_receive_qnty_tot"],
                col: [7, 8, 9],
                operation: ["sum", "sum", "sum"],
                write_method: ["innerHTML", "innerHTML", "innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1, tableFilters);
        });

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            $('#tbl_list_search tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";

            $('#tbl_list_search tr:first').show();
        }

    </script>   
    <div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:1037px;">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="12"><b>Grey Receive Info</b></th>
                </thead>
                <thead>
                <th width="30">SL</th>
                <th width="115">Receive Id</th>
                <th width="95">Receive Basis</th>
                <th width="110">Product Details</th>
                <th width="100">Booking / Program No</th>
                <th width="60">Machine No</th>
                <th width="75">Production Date</th>
                <th width="80">Inhouse Production</th>
                <th width="80">Outside Production</th>
                <th width="80">Production Qnty</th>
                <th width="70">Challan No</th>
                <th>Kniting Com.</th>
                </thead>
            </table>
            <div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i = 1;
                    $total_receive_qnty = 0;
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

                    $sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $total_receive_qnty += $row[csf('quantity')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
                                <?
                                if ($row[csf('knitting_source')] != 3) {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_receive_qnty_in += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
                                <?
                                if ($row[csf('knitting_source')] == 3) {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_receive_qnty_out += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><?
                                    if ($row[csf('knitting_source')] == 1)
                                        echo $company_library[$row[csf('knitting_company')]];
                                    else if ($row[csf('knitting_source')] == 3)
                                        echo $supplier_details[$row[csf('knitting_company')]];
                                    ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">  
                <tfoot>
                <th width="30">&nbsp;</th>
                <th width="115">&nbsp;</th>
                <th width="95">&nbsp;</th>
                <th width="110">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="75" align="right">Total</th>
                <th width="80" align="right" id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
                <th width="80" align="right" id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
                <th width="80" align="right" id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
                <th width="70">&nbsp;</th>
                <th>&nbsp;</th>
                </tfoot>
            </table>    
        </div>
    </fieldset>


    <?
    exit();
}

if ($action == "grey_purchase") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:1037px; margin-left:2px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="11"><b>Grey Receive / Purchase Info</b></th>
                </thead>
                <thead>
                <th width="30">SL</th>
                <th width="115">Receive Id</th>
                <th width="95">Receive Basis</th>
                <th width="160">Product Details</th>
                <th width="110">Booking/PI/ Production No</th>
                <th width="75">Production Date</th>
                <th width="80">Inhouse Production</th>
                <th width="80">Outside Production</th>
                <th width="80">Production Qnty</th>
                <th width="65">Challan No</th>
                <th>Kniting Com.</th>
                </thead>
            </table>
            <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_receive_qnty = 0;
                    $receive_data_arr = array();
                    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

                    $sql = "select a.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=22 and c.entry_form=22 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, a.id"; //and a.receive_basis<>9
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $total_receive_qnty += $row[csf('quantity')];

                        $knit_com = '';
                        if ($row[csf('knitting_source')] == 1)
                            $knit_com = $company_library[$row[csf('knitting_company')]];
                        else if ($row[csf('knitting_source')] == 3)
                            $knit_com = $supplier_details[$row[csf('knitting_company')]];
                        else
                            $knit_com = "&nbsp;";

                        $recv_data_arr[$row[csf('id')]]['source'] = $row[csf('knitting_source')];
                        $recv_data_arr[$row[csf('id')]]['com'] = $knit_com;
                        $recv_data_arr[$row[csf('id')]]['basis'] = $receive_basis_arr[$row[csf('receive_basis')]];
                        $recv_data_arr[$row[csf('id')]]['booking'] = $row[csf('booking_no')];
                        $recv_data_arr[$row[csf('id')]]['challan_no'] = $row[csf('challan_no')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
                                <?
                                if ($row[csf('knitting_source')] != 3) {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_receive_qnty_in += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
                                <?
                                if ($row[csf('knitting_source')] == 3) {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_receive_qnty_out += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
                            <td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? echo $knit_com; ?>&nbsp;</p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <th colspan="6" align="right">Total</th>
                    <th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
                    <th align="right"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
                    <th align="right"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset> 

    <!-- Grey Received Return Info -->   

    <fieldset style="width:1037px; margin-top:10px; margin-left:2px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="11"><b>Grey Return Info</b></th>
                </thead>
                <thead>
                <th width="30">SL</th>
                <th width="115">Return Id</th>
                <th width="95">Return Basis</th>
                <th width="160">Product Details</th>
                <th width="110">Booking/PI/ Production No</th>
                <th width="75">Return Date</th>
                <th width="80">Inhouse Return</th>
                <th width="80">Outside Return</th>
                <th width="80">Return Qnty</th>
                <th width="65">Challan No</th>
                <th>Kniting Com.</th>
                </thead>
            </table>
            <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_return_qnty = 0;
                    $sql = "select a.issue_number, a.issue_date, a.received_id, a.challan_no, b.prod_id, sum(c.quantity) as quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=45 and c.entry_form=45 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, a.received_id, a.challan_no, b.prod_id";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $total_return_qnty += $row[csf('quantity')];

                        $source = $recv_data_arr[$row[csf('received_id')]]['source'];
                        $knit_com = $recv_data_arr[$row[csf('received_id')]]['com'];
                        $receive_basis = $recv_data_arr[$row[csf('received_id')]]['basis'];
                        $booking_no = $recv_data_arr[$row[csf('received_id')]]['booking'];
                        $challan_no = $recv_data_arr[$row[csf('received_id')]]['challan_no'];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis; ?></p></td>
                            <td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="110"><p><? echo $booking_no; ?>&nbsp;</p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td align="right" width="80">
                                <?
                                if ($source != 3) {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_return_qnty_in += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
                                <?
                                if ($source == 3) {
                                    echo number_format($row[csf('quantity')], 2, '.', '');
                                    $total_return_qnty_out += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
                            <td width="65"><p><? echo $challan_no; ?>&nbsp;</p></td>
                            <td><p><? echo $knit_com; ?>&nbsp;</p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="6" align="right">Total</th>
                            <th align="right"><? echo number_format($total_return_qnty_in, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty_out, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty, 2, '.', ''); ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="6" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_receive_qnty_in - $total_return_qnty_in, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($total_receive_qnty_out - $total_return_qnty_out, 2, '.', ''); ?></th>
                            <th align="right"><? echo number_format($total_receive_qnty - $total_return_qnty, 2, '.', ''); ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>

                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>    
    <?
    exit();
}

if ($action == "batch_qnty") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>
        var tableFilters = {
            col_operation: {
                id: ["value_batch_total_id"],
                col: [4],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('table_body_popup', -1, tableFilters);
        });

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:670px; margin-left:7px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="5"><b>Batch Info</b></th>
                </thead>
                <thead>
                <th width="50">SL</th>
                <th width="100">Batch Date</th>
                <th width="170">Batch No</th>
                <th width="150">Batch Color</th>
                <th>Batch Qnty</th>
                </thead>
            </table>
            <div style="width:667px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" id="table_body_popup">
                    <?
                    $i = 1;
                    $total_batch_qnty = 0;
                    $sql = "select a.batch_no, a.batch_date, a.color_id, sum(b.batch_qnty) as quantity from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($order_id) and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.batch_date, a.color_id";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $total_batch_qnty += $row[csf('quantity')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
                            <td width="170"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="150"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')], 2); ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                   
                </table>
            </div> 
             <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">  
                <tfoot>
                <th width="50">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="170">&nbsp;</th>
                <th width="150" align="right">Total</th>
                <th  align="right" id="value_batch_total_id"><? echo number_format($total_batch_qnty, 2, '.', ''); ?></th>
                </tfoot>
            </table>     
        </div>
    </fieldset>  

   <!--  <script>setFilterGrid("table_body_popup", -1);</script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script> -->
    <?
    exit();
}

if ($action == "grey_issue") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    //echo $color;die;
    ?>
    <script>

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:880px; margin-left:3px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="9"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="100">Issue Purpose</th>
                        <th width="120">Issue To</th>
                        <th width="105">Booking No</th>
                        <th width="80">Batch No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qnty (In)</th>
                        <th>Issue Qnty (Out)</th>
                    </tr>
                </thead>
            </table>
            <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $issue_to = '';
                    if ($color != "")
                        $color_cond = " and b.color_id='$color'";
                    else
                        $color_cond = "";
                    $sql = "select a.id,a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity 
                    from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (16,61) and c.entry_form in (16,61) and c.po_breakdown_id in($order_id) $color_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        $issue_id_arr[] = $row[csf('id')];
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        if ($row[csf('knit_dye_source')] == 1) {
                            $issue_to = $company_library[$row[csf('knit_dye_company')]];
                        } else if ($row[csf('knit_dye_source')] == 3) {
                            $issue_to = $supplier_details[$row[csf('knit_dye_company')]];
                        } else
                            $issue_to = "&nbsp;";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="105"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
                                <?
                                if ($row[csf('knit_dye_source')] != 3) {
                                    echo number_format($row[csf('quantity')], 2);
                                    $total_issue_qnty += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                if ($row[csf('knit_dye_source')] == 3) {
                                    echo number_format($row[csf('quantity')], 2);
                                    $total_issue_qnty_out += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty, 2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_out, 2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty + $total_issue_qnty_out, 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>

    <!-- Grey Issue Return Info -->

    <fieldset style="width:880px; margin-left:3px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="9"><b>Grey Issue Return Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Return Id</th>
                        <th width="100">Return Purpose</th>
                        <th width="120">Issue To</th>
                        <th width="105">Booking No</th>
                        <th width="80">Batch No</th>
                        <th width="80">Return Date</th>
                        <th width="100">Return Qnty (In)</th>
                        <th>Return Qnty (Out)</th>
                    </tr>
                </thead>
            </table>
            <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $issue_to = '';
                    $sql = "select a.id, a.recv_number, a.receive_date, a.receive_purpose, a.knitting_source, a.knitting_company, a.booking_no, a.batch_id, sum(c.quantity) as quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (51,84) and c.entry_form in (51,84) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.receive_purpose, a.knitting_source, a.knitting_company, a.booking_no, a.batch_id";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        if ($row[csf('knitting_source')] == 1) {
                            $issue_to = $company_library[$row[csf('knitting_company')]];
                        } else if ($row['knitting_source'] == 3) {
                            $issue_to = $supplier_details[$row[csf('knitting_company')]];
                        } else
                            $issue_to = "&nbsp;";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('receive_purpose')]]; ?></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="105"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80"><p><? echo $batch_details[$row[csf('batch_id')]]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100" align="right">
                                <?
                                if ($row[csf('knitting_source')] != 3) {
                                    echo number_format($row[csf('quantity')], 2);
                                    $total_return_qnty += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                if ($row[csf('knitting_source')] == 3) {
                                    echo number_format($row[csf('quantity')], 2);
                                    $total_return_qnty_out += $row[csf('quantity')];
                                } else
                                    echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_return_qnty, 2); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty_out, 2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format((($total_issue_qnty + $total_issue_qnty_out) - ($total_return_qnty + $total_return_qnty_out)), 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>

    <?
    exit();
}

if ($action == "dye_qnty") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    $machine_arr = return_library_array("select id, machine_no as machine_name from lib_machine_name", "id", "machine_name");
    ?>
    <script>

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:880px; margin-left:3px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="10"><b>Dyeing Info</b></th>
                </thead>
                <thead>
                <th width="30">SL</th>
                <th width="70">System Id</th>
                <th width="80">Process End Date</th>
                <th width="100">Batch No</th>
                <th width="70">Dyeing Source</th>
                <th width="120">Dyeing Company</th>
                <th width="90">Receive Qnty</th>
                <th width="190">Fabric Description</th>
                <th>Machine Name</th>
                </thead>
            </table>
            <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_dye_qnty = 0;
                    $dye_company = '';
                    $sql = "select a.batch_no, b.item_description as febric_description, sum(b.batch_qnty) as quantity, c.id, c.company_id, c.process_end_date, c.machine_id,c.service_company from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id='$color' and c.load_unload_id=2 and c.entry_form=35 and b.po_id in($order_id) and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.batch_no, b.item_description, c.id, c.company_id, c.process_end_date, c.machine_id,c.service_company";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $dye_company = $company_library[$row[csf('service_company')]];
                        $total_dye_qnty += $row[csf('quantity')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70"><p><? echo $row[csf('id')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('process_end_date')]); ?>&nbsp;</td>
                            <td width="100"><p><? echo $row[csf('batch_no')]; //$batch_details[$row[csf('batch_id')]];      ?></p></td>
                            <td width="70"><? echo "Inhouse"; //echo $knitting_source[$row[csf('dyeing_source')]];       ?></td>
                            <td width="120"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
                            <td width="190"><p><? echo $row[csf('febric_description')]; ?></p></td>
                            <td><p>&nbsp;<? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <th colspan="6" align="right">Total</th>
                    <th align="right"><? echo number_format($total_dye_qnty, 2); ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "fabric_receive") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $product_details = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
    ?>
    <script>

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:880px; margin-left:3px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="9"><b>Fabric Receive Info</b></th>
                </thead>
                <thead>
                <th width="30">SL</th>
                <th width="120">System Id</th>
                <th width="75">Rec. Date</th>
                <th width="80">Rec. Basis</th>
                <th width="90">Batch No</th>
                <th width="90">Dyeing Source</th>
                <th width="100">Dyeing Company</th>
                <th width="90">Receive Qnty</th>
                <th>Fabric Description</th>
                </thead>
            </table>
            <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_fabric_recv_qnty = 0;
                    $dye_company = '';
                    $sql = "select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=7 and c.entry_form=7 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        if ($row[csf('knitting_source')] == 1) {
                            $dye_company = $company_library[$row[csf('knitting_company')]];
                        } else if ($row['knitting_source'] == 3) {
                            $dye_company = $supplier_details[$row[csf('knitting_company')]];
                        } else
                            $dye_company = "&nbsp;";

                        $total_fabric_recv_qnty += $row[csf('quantity')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <th colspan="7" align="right">Total</th>
                    <th align="right"><? echo number_format($total_fabric_recv_qnty, 2); ?></th>
                    <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>   
    <?
    exit();
}

if ($action == "fabric_purchase") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $product_details = return_library_array("select id, product_name_details from product_details_master", "id", "product_name_details");
    ?>
    <script>

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:880px; margin-left:3px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="9"><b>Fabric Receive Info</b></th>
                </thead>
                <thead>
                <th width="30">SL</th>
                <th width="120">System Id</th>
                <th width="75">Rec. Date</th>
                <th width="80">Rec. Basis</th>
                <th width="90">Batch No</th>
                <th width="90">Dyeing Source</th>
                <th width="100">Dyeing Company</th>
                <th width="90">Receive Qnty</th>
                <th>Fabric Description</th>
                </thead>
            </table>
            <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_fabric_recv_qnty = 0;
                    $dye_company = '';
                    $recv_data_arr = array();
                    $sql = "select a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=37 and c.entry_form=37 and c.po_breakdown_id in($order_id) and c.color_id='$color'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id"; //and a.receive_basis<>9
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        if ($row[csf('knitting_source')] == 1) {
                            $dye_company = $company_library[$row[csf('knitting_company')]];
                        } else if ($row['knitting_source'] == 3) {
                            $dye_company = $supplier_details[$row[csf('knitting_company')]];
                        } else
                            $dye_company = "&nbsp;";

                        $total_fabric_recv_qnty += $row[csf('quantity')];

                        //$recv_data_arr[$row[csf('id')]]['sor']=$knitting_source[$row[csf('knitting_source')]];
                        //$recv_data_arr[$row[csf('id')]]['com']=$dye_company;
                        //$recv_data_arr[$row[csf('id')]]['basis']=$receive_basis_arr[$row[csf('receive_basis')]];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <th colspan="7" align="right">Total</th>
                    <th align="right"><? echo number_format($total_fabric_recv_qnty, 2); ?></th>
                    <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>

    <!--Fabric Receive return info--> 

    <fieldset style="width:880px; margin-left:3px; margin-top:10px;">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="9"><b>Fabric Receive Return Info</b></th>
                </thead>
                <thead>
                <th width="30">SL</th>
                <th width="120">System Id</th>
                <th width="75">Ret. Date</th>
                <th width="80">Ret. Basis</th>
                <th width="90">Batch No</th>
                <th width="90">Dyeing Source</th>
                <th width="100">Dyeing Company</th>
                <th width="90">Return Qnty</th>
                <th>Fabric Description</th>
                </thead>
            </table>
            <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $sql_prod = "select a.id, a.receive_basis, a.knitting_source, a.knitting_company from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(7,37) and c.entry_form in(7,37) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.receive_basis, a.knitting_source, a.knitting_company";
                    $resultProd = sql_select($sql_prod);
                    foreach ($resultProd as $row) {
                        if ($row[csf('knitting_source')] == 1) {
                            $dye_company = $company_library[$row[csf('knitting_company')]];
                        } else if ($row['knitting_source'] == 3) {
                            $dye_company = $supplier_details[$row[csf('knitting_company')]];
                        } else
                            $dye_company = "&nbsp;";

                        $recv_data_arr[$row[csf('id')]]['sor'] = $knitting_source[$row[csf('knitting_source')]];
                        $recv_data_arr[$row[csf('id')]]['com'] = $dye_company;
                        $recv_data_arr[$row[csf('id')]]['basis'] = $receive_basis_arr[$row[csf('receive_basis')]];
                    }

                    $i = 1;
                    $total_fabric_return_qnty = 0;
                    $sql = "select a.issue_number, a.issue_date, a.issue_basis, a.knit_dye_source, a.knit_dye_company, a.received_id, b.batch_id_from_fissuertn as batch_id, b.prod_id, sum(c.quantity) as quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=46 and c.entry_form=46 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.issue_basis, a.received_id, a.knit_dye_source, a.knit_dye_company, b.batch_id_from_fissuertn, b.prod_id";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $source = $recv_data_arr[$row[csf('received_id')]]['sor'];
                        $dye_company = $recv_data_arr[$row[csf('received_id')]]['com'];
                        $basis = $recv_data_arr[$row[csf('received_id')]]['basis'];
                        $batch = $batch_details[$row[csf('batch_id')]];

                        $total_fabric_return_qnty += $row[csf('quantity')];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="80"><? echo $basis; ?></td>
                            <td width="90"><p><? echo $batch; ?></p></td>
                            <td width="90"><? echo $source; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_fabric_return_qnty, 2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_fabric_recv_qnty - $total_fabric_return_qnty, 2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>    

    <?
    exit();
}

if ($action == "issue_to_cut") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }

    </script>   
    <div style="width:740px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:740px; margin-left:7px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="7"><b>Issue To Cutting Info</b></th>
                </thead>
                <thead>
                <th width="40">SL</th>
                <th width="110">Issue No</th>
                <th width="80">Challan No</th>
                <th width="80">Issue Date</th>
                <th width="100">Batch No</th>
                <th width="90">Issue Qnty</th>
                <th>Fabric Description</th>
                </thead>
            </table>
            <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_issue_to_cut_qnty = 0;
                    $issue_data_arr = array();
                    $sql = "select a.id,a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=18 and c.entry_form=18 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.challan_no, b.batch_id, b.prod_id,a.id";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $total_issue_to_cut_qnty += $row[csf('quantity')];

                        $issue_data_arr[$row[csf('id')]] = $batch_details[$row[csf('batch_id')]];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <th colspan="5" align="right">Total</th>
                    <th align="right"><? echo number_format($total_issue_to_cut_qnty, 2); ?></th>
                    <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>

    <!-- Issue To Cutting return Info -->

    <fieldset style="width:740px; margin-left:7px; margin-top:10px;">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="7"><b>Issue Return Info</b></th>
                </thead>
                <thead>
                <th width="40">SL</th>
                <th width="110">Issue No</th>
                <th width="80">Challan No</th>
                <th width="80">Return Date</th>
                <th width="100">Batch No</th>
                <th width="90">Return Qnty</th>
                <th>Fabric Description</th>
                </thead>
            </table>
            <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $i = 1;
                    $total_issue_to_cut_ret_qnty = 0;
                    $sql = "select a.id,a.recv_number, a.receive_date, a.challan_no, b.prod_id, a.issue_id, sum(c.quantity) as quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=52 and c.entry_form=52 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.challan_no, b.prod_id,a.id,a.issue_id";
                    $result = sql_select($sql);
                    foreach ($result as $row) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        $total_issue_to_cut_ret_qnty += $row[csf('quantity')];
                        $batch = $issue_data_arr[$row[csf('issue_id')]];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="rtr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100"><p><? echo $batch; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_to_cut_ret_qnty, 2); ?></th>
                            <th>&nbsp;</th>
                        </tr>

                        <tr>
                            <th colspan="5" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_issue_to_cut_qnty - $total_issue_to_cut_ret_qnty, 2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>  
        </div>
    </fieldset>

    <?
    exit();
}

if ($action == "yarn_trans") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
    ?>
    <script>

        function print_window()
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>   
    <div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:670px; margin-left:7px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
                </thead>
                <?
                $i = 1;
                $total_trans_in_qnty = 0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_in_qnty += $row[csf('transfer_qnty')];
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
                </tr>
                <thead>
                    <tr>
                        <th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
                </thead>
                <?
                $total_trans_out_qnty = 0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_out_qnty += $row[csf('transfer_qnty')];
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
                </tr>
                <tfoot>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Net Transfer</th>
                <th><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
                </tfoot>
            </table>    
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "knit_trans") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
    ?>
    <script>

        function print_window()
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>   
    <div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:670px; margin-left:7px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
                </thead>
                <?
                $i = 1;
                $total_trans_in_qnty = 0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type =5 and c.entry_form in (13,83) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_in_qnty += $row[csf('transfer_qnty')];
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
                </tr>
                <thead>
                    <tr>
                        <th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
                </thead>
                <?
                $total_trans_out_qnty = 0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form in (13,83) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_out_qnty += $row[csf('transfer_qnty')];
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
                </tr>
                <tfoot>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Net Transfer</th>
                <th><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
                </tfoot>
            </table>    
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "finish_trans") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
    ?>
    <script>

        function print_window()
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>   
    <div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:670px; margin-left:7px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
                </thead>
                <?
                $i = 1;
                $total_trans_in_qnty = 0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form in (14,15,134) and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_in_qnty += $row[csf('transfer_qnty')];
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
                </tr>
                <thead>
                    <tr>
                        <th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
                </thead>
                <?
                $total_trans_out_qnty = 0;
                $sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form in (14,15,134) and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result = sql_select($sql);
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
                    </tr>
                    <?
                    $total_trans_out_qnty += $row[csf('transfer_qnty')];
                    $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
                </tr>
                <tfoot>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Net Transfer</th>
                <th><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
                </tfoot>
            </table>    
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "show_image")
{
    echo load_html_head_contents("Image View", "../../../", 1, 1, $unicode);
    extract($_REQUEST);
    //echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
    ?>
    <table>
        <tr>
            <?
            foreach ($data_array as $row) {
                ?>
                <td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
                <?
            }
            ?>
        </tr>
    </table>
    <?
}

//getBgColorZs added by zaman
function getBgColorZs($data)
{
	$bgColor = $data['color'];
	$ninetyFivePercent = (($data['req_qty']*95)/100);
	if($data['rcv_qty'] == 0)
	{
		$bgColor = '#FF4F4F';
	}
	else
	{
		if($data['rcv_qty'] >= $ninetyFivePercent)
		{
			$bgColor = '#27ae60';
		}
		else
		{
			$bgColor = '#f4d03f';
		}
	}
	return $bgColor;
}
?>