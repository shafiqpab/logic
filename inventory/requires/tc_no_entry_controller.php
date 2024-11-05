<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = $_SESSION['logic_erp']['supplier_id'];
$user_comp_location_ids = $_SESSION['logic_erp']['company_location_id'];


if ($action=="load_drop_down_location")
{
    if($user_comp_location_ids) $user_comp_location_cond = " and id in ($user_comp_location_ids)"; else $user_comp_location_cond = "";
    echo create_drop_down("cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $user_comp_location_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "reset_form('','','cbo_store_name*txt_pi_no*pi_id*txt_btbLc_no*btbLc_id','','','');load_drop_down( 'requires/tc_no_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_store', 'store_td' );load_multistore();", 0);
    exit();
}   

if ($action=="load_drop_down_store")
{
    $data_arr = explode("**",$data);
    $location_id = $data_arr[0];
    $company_id = $data_arr[1];
    if($user_store_ids) $user_store_cond = " and a.store_id in ($user_store_ids)"; else $user_store_cond = "";

    echo create_drop_down( "cbo_store_name", 150, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$company_id' and a.location_id=$location_id and b.category_type in(1) and a.status_active=1 and a.is_deleted=0 $user_store_cond order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );

    exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

if($action=="pinumber_popup")
{
    echo load_html_head_contents("PI Number Popup Info","../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>

    <script>
    function js_set_value(str)
    {
        var splitData = str.split("_");
        $("#pi_id").val(splitData[0]);
        $("#pi_no").val(splitData[1]);
        parent.emailwindow.hide();
    }
    </script>

    </head>

    <body>
    <div align="center" style="width:100%; margin-top:5px" >
    <form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
        <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th id="search_by_td_up">Enter PI Number</th>
                        <th>Enter PI Date</th>
                        <th>
                            <input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                            <input type="hidden" id="pi_id" value="" />
                            <input type="hidden" id="pi_no" value="" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr align="center">
                        <td>
                            <?
                                echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                            ?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date" readonly />
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;" placeholder="To Date" readonly />
                        </td>
                         <td align="center">
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'tc_no_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
            <div align="center" style="margin-top:10px" id="search_div"> </div>
            </form>
       </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_pi_search_list_view")
{
    $ex_data = explode("_",$data);

    if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
    $txt_search_common = trim($ex_data[1]);
    $company = $ex_data[2];
    $from_date = $ex_data[3];
    $to_date = $ex_data[4];
    if( $from_date!="" && $to_date!="")
    {
        if($db_type==0)
        {
            $pi_date_cond= " and pi_date between '".change_date_format($from_date,"yyyy-mm-dd")."' and '".change_date_format($to_date,"yyyy-mm-dd")."'";
        }
        else
        {
            $pi_date_cond= " and pi_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
        }
    }
    else $pi_date_cond="";

    $sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and entry_form=165 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";

    $arr=array(1=>$company_arr,2=>$supplier_arr);
    echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, Last Shipment Date, PI Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,last_shipment_date,total_amount", "",'','0,0,0,3,3,2') ;
    exit();
}

if($action=="btbLc_popup")
{
    echo load_html_head_contents("BTB LC Popup Info","../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>

    <script>
        function js_set_value(str)
        {
            var splitData = str.split("_");
            $("#btbLc_id").val(splitData[0]);
            $("#btbLc_no").val(splitData[1]);
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <div align="center" style="width:100%; margin-top:5px" >
            <form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th id="search_by_td_up">Enter BTB LC Number</th>
                        <th>LC Date</th>
                        <th>
                            <input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                            <input type="hidden" id="btbLc_id" value="" />
                            <input type="hidden" id="btbLc_no" value="" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr align="center">
                        <td>
                            <?
                                echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                            ?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:180px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" placeholder="From Date" style="width:80px"/>
                            To
                            <input type="text" name="txt_date_to" placeholder="To Date" id="txt_date_to" value="" class="datepicker" style="width:80px"/>
                        </td>
                         <td align="center">
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_lc_search_list_view', 'search_div', 'tc_no_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <div align="center" style="margin-top:10px" id="search_div"> </div>
            </form>
       </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_lc_search_list_view")
{
    $ex_data = explode("_",$data);
    $cbo_supplier = "";
    if($ex_data[0]>0)  $cbo_supplier = " and supplier_id = ".$ex_data[0];
    $txt_search_common = trim($ex_data[1]);
    $company = $ex_data[2];
    $date_cond = "";
    if ($db_type == 0) {
        if ($ex_data[3] != "" && $ex_data[4] != "")
            $date_cond = " and lc_date  between '" . change_date_format($ex_data[3], 'yyyy-mm-dd') . "' and '" . change_date_format($ex_data[4], 'yyyy-mm-dd') . "'";
    }
    else {
        if ($ex_data[3] != "" && $ex_data[4] != "")
            $date_cond = " and lc_date between '" . date("j-M-Y", strtotime($ex_data[3])) . "' and '" . date("j-M-Y", strtotime($ex_data[4]. ' +1 day')) . "'";
    }

    $sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and pi_entry_form=165  $cbo_supplier and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $date_cond";
    //echo  $sql;
    $arr=array(1=>$company_arr,2=>$supplier_arr);
    echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;
    exit();
}

if($action=="tc_no_popup")
{
    echo load_html_head_contents("TC No Popup Info","../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>

    <script>
        var selected_name = new Array();
        var selected_id = new Array();

        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
            tbl_row_count = tbl_row_count-1;
            for( var i = 1; i <= tbl_row_count; i++ )
            {
                js_set_value( i );
            }
        }

        function toggle( x, origColor )
        {
            var newColor = 'yellow';
            if ( x.style )
            {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function set_all()
        {
            var old=document.getElementById('selected_ids').value;
            if(old!="")
            {
                old=old.split(",");
                for(var k=0; k<old.length; k++)
                {
                    js_set_value( old[k] )
                }
            }
        }


        function js_set_value( str )
        {
            toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
            if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
            {
                selected_id.push( $('#txt_individual_id' + str).val() );
                selected_name.push( $('#txt_individual' + str).val() );

            }
            else
            {
                for( var i = 0; i < selected_id.length; i++ )
                {
                    if( selected_id[i] == $('#txt_individual_id' + str).val() )
                        break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }

            var id = ''; var name = '';
            for( var i = 0; i < selected_id.length; i++ )
            {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }

            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );

            $('#selected_id').val(id);
            $('#selected_name').val(name);
        }

        function js_set_value_qnty() // TC popup close
        {
            var selected_row = $('#selected_id').val(); 
             
            if(selected_row!="")
            {
                selected_row = selected_row.split(",");
                //console.log(selected_row);
                var qnty_breck_down = "";
                for(var i=0; i<selected_row.length; i++)
                {
                    var tcRcvQty = $('#txtTcRcvQty_' + selected_row[i]).val()*1;                     
                    var txtUsedQty = $('#txtUsedQty_' + selected_row[i]).val()*1; 
                    var cumUsedQty = $('#txtCumUsedQty_' + selected_row[i]).val()*1;
                    var tcBalanceQty = $('#txtTcBbalanceQty_' + selected_row[i]).val()*1; 
                    
                    if (qnty_breck_down == "") 
                    {
                        qnty_breck_down = $('#txtTcRcvTtransId_' + selected_row[i]).val() + "-XXXX-" + $('#txtTcNo_' + selected_row[i]).val() + "_" +  $('#txtGmtsTcNo_' + selected_row[i]).val() + "_" + $('#txtUsedQty_' + selected_row[i]).val();
                    }
                    else 
                    {
                        qnty_breck_down += '-YYYYYYY-' + $('#txtTcRcvTtransId_' + selected_row[i]).val() + "-XXXX-" + $('#txtTcNo_' + selected_row[i]).val() + "_" + $('#txtGmtsTcNo_' + selected_row[i]).val() + "_" + $('#txtUsedQty_' + selected_row[i]).val();
                    }

                    //alert(qnty_breck_down);

                    //alert(txtUsedQty +">"+ tcBalanceQty);return;
                    if ( txtUsedQty > tcBalanceQty )
                    {
                        alert("Used quantity can not greater than received quantity\nBalance quantity="+tcBalanceQty);
                        return;
                    }

                } // end loof

                document.getElementById('selected_tc_data').value = qnty_breck_down;
                parent.emailwindow.hide();       
            }
            else
            {
                parent.emailwindow.hide();
            }               
        }
        
        function generate_tc_no_popup_data()
        {
            if(document.getElementById('txt_tc_number').value=="")
            {
                if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
                {
                    alert('Please fill up Trans Date')
                    return;
                }
                else
                {
                    show_list_view ( document.getElementById('txt_tc_number').value+'_'+<? echo $companyID; ?>+'_'+<? echo $prod_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $issue_trans_id; ?>+'_'+1, 'create_tc_no_search_list_view', 'search_div', 'tc_no_entry_controller', 'setFilterGrid(\'list_view\',-1)');
                }
            }
            else
            {
                show_list_view ( document.getElementById('txt_tc_number').value+'_'+<? echo $companyID; ?>+'_'+<? echo $prod_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $issue_trans_id; ?>+'_'+1, 'create_tc_no_search_list_view', 'search_div', 'tc_no_entry_controller', 'setFilterGrid(\'list_view\',-1)');
            }    
        }
    </script>

    </head>

    <body>
        <div align="center" style="width:100%; margin-top:5px" >
            <form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
            <table width="430" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th id="search_by_td_up">Enter TC Number</th>
                        <th width="150" class="must_entry_caption">Trans Date</th>
                        <th>
                            <input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                            <input type="hidden" id="txt_tc_no" value="" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr align="center">
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_tc_number" id="txt_tc_number" />
                        </td>
                        <td align="center" width="150">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" placeholder="From Date" class="datepicker" style="width:50px"/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" placeholder="To Date" class="datepicker" style="width:50px"/>
                        </td>
                         <td align="center">
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="generate_tc_no_popup_data();" style="width:100px;" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <div align="center" style="margin-top:10px" id="search_div"> </div>
            </form>


       </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
        var issue_trans_id =  '<? echo $issue_trans_id; ?>';
        if(issue_trans_id!='')
        {
           show_list_view ( document.getElementById('txt_tc_number').value+'_'+<? echo $companyID; ?>+'_'+<? echo $prod_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $issue_trans_id; ?>+'_'+2, 'create_tc_no_search_list_view', 'search_div', 'tc_no_entry_controller', 'setFilterGrid(\'list_view\',-1)'); 
        }    
    </script>
    </html>
    <?
}

if($action=="create_tc_no_search_list_view")
{
    $ex_data = explode("_",$data);
    $tc_number = trim($ex_data[0]);
    $company_id = $ex_data[1];
    $prod_id = $ex_data[2];
    $from_date = $ex_data[3];
    $to_date = $ex_data[4];
    $issue_trans_id = $ex_data[5];
    $list_view_type = $ex_data[6];
    
    $sql_cond = "";
    
    if(str_replace("'","",$tc_number)!="")
    {
        $sql_cond .= " and b.rd_no like '%".$tc_number."%' ";
    }

    if( str_replace("'","",$from_date)!="" &&  str_replace("'","",$to_date)!="")
    {
        $sql_cond .= " and b.transaction_date between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
    }     

   $sql = "select listagg (b.id,',') within group (order by b.id ASC) as rcv_trans_id,sum(b.cons_quantity) as tc_rcv_qty,b.rd_no as tc_no,b.fabric_ref as tc_remarks,b.reason_of_change as used_qty_break_str from inv_receive_master a,  inv_transaction b where a.id=b.mst_id and b.company_id=$company_id and b.transaction_type = 1 and b.item_category= 1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 AND b.rd_no is not null $sql_cond group by b.rd_no,b.fabric_ref,b.reason_of_change";
    //echo $sql; die();
    $sql_result = sql_select($sql);
    $tc_used_data = array();
    $row_count = 0;
    $rcv_tc_no_data = array();
    foreach($sql_result as $row)
    {   
        $tc_no = $row[csf("tc_no")];
        $remarks = $row[csf("tc_remarks")];
        $used_qty_breakdown = $row[csf("used_qty_break_str")];
        $rcv_trans_id = $row[csf("rcv_trans_id")];

        $rcv_tc_no_data[$tc_no][$remarks][$used_qty_breakdown]['rcv_trans_id'] = $rcv_trans_id;   
        $rcv_tc_no_data[$tc_no][$remarks][$used_qty_breakdown]['tc_rcv_qty'] = $row[csf("tc_rcv_qty")];  

        if($row[csf("used_qty_break_str")]!="")
        {
            $used_qty_break_arr = explode("_", $row[csf("used_qty_break_str")] );
            $tc_used_data[$used_qty_break_arr[1]]['issue_trans_id'] = $used_qty_break_arr[0];
            $tc_used_data[$used_qty_break_arr[1]]['gmtc_no'] = $used_qty_break_arr[2];
            
            if( $used_qty_break_arr[0] == str_replace("'","",$issue_trans_id)*1 )
            {
                $tc_used_data[$used_qty_break_arr[1]]['current_used_qty'] = $used_qty_break_arr[3];  
            }else{
                $tc_used_data[$used_qty_break_arr[1]]['cum_used_qty'] += $used_qty_break_arr[3];  
            }
            
            if($list_view_type==2)
            {
                if( $used_qty_break_arr[0] == str_replace("'","",$issue_trans_id)*1 && $used_qty_break_arr[3]>0)
                {
                    $rcv_tc_no_data[$tc_no][$remarks][$used_qty_breakdown]['showing_status']=1;
                    $row_count++;
                }
                else
                {
                    $rcv_tc_no_data[$tc_no][$remarks][$used_qty_breakdown]['showing_status']=0;
                }
            }
            else
            {
                $rcv_tc_no_data[$tc_no][$remarks][$used_qty_breakdown]['showing_status']=1;
            }
        }
        else
        { 
            if($list_view_type==1)
            {
                $rcv_tc_no_data[$tc_no][$remarks][$used_qty_breakdown]['showing_status']=1;
                $row_count++;
            }
        }   
    }

    //echo "<pre>";
    //print_r($row);  

    //echo "<pre>";
    //print_r($tc_used_data);
    //echo $sql;
    if( $row_count<1)
    { 
        if($list_view_type==1){
            $msg = "Data not found.";
        }
        else
        {
            $msg = "Existing Save Data not found.";
        }
        ?>
        <table>
        <tr bgcolor="orrange">
            <td align="center" colspan="16" style="text-align: center;" valign="middle">
                <p style="color: white; font-size: 24px;"> <? echo $msg;?> </p>
            </td>
        </tr>
        </table>
        <?
        exit();
    }   
    ?>
    <input type="hidden" name="selected_id" id="selected_id" value="">
    <input type="hidden" name="selected_name" id="selected_name" value="">
    <input type="hidden" name="selected_tc_data" id="selected_tc_data" value="">
    <table class="rpt_table" id="rpt_tablelist_view" rules="all" width="790" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="50">SL No</th>
                <th width="100">TC No</th>
                <th width="100">Gmts TC No</th>
                <th width="100">TC Rcv Qty</th>
                <th width="100">Used Qty</th>
                <th width="100">Cum. Used Qty</th>
                <th width="100">TC Balance Qty</th>
                <th>Remarks</th>
            </tr>
        </thead>
    </table>

    <div style="max-height:260px; width:788px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" id="list_view" rules="all" width="768" height="" cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <?
                
                $i = 1;
                $issue_trans_id = "";
                $tc_no = "";
                $gmts_tc_no = "";
                $used_qty = 0;
                foreach($rcv_tc_no_data as $tc_no=>$remarks_arr)
                {
                    foreach($remarks_arr as $remarks=>$used_qty_breakdown_arr)
                    {
                        foreach($used_qty_breakdown_arr as $used_qty_breakdown=>$row)
                        {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                           
                            //if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;                            

                            $gmtc_no = $tc_used_data[$tc_no]['gmtc_no'];
                            $current_used_qty = $tc_used_data[$tc_no]['current_used_qty']*1;
                            $cum_used_qty = $tc_used_data[$tc_no]['cum_used_qty']*1;
                            $tc_balance_qty = ($row['tc_rcv_qty']-$cum_used_qty);

                            if($row['showing_status']==1)
                            {
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer; height: 20px;"  id="search<? echo $i;?>" >
                                    <td width="50" align="center">
                                        <? echo $i; ?>
                                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $i; ?>"/>
                                        <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $tc_no; ?>"/>
                                    </td>
                                    <td width="100" align="center"><? echo $tc_no; ?>
                                        <input type="hidden" id="txtTcRcvTtransId_<? echo $i; ?>" value="<? echo $row['rcv_trans_id']; ?>" />
                                        <input type="hidden" id="txtTcNo_<? echo $i; ?>" value="<? echo $tc_no; ?>" />
                                    </td>
                                    <td width="100" align="left">
                                        <input type="text" name="txtGmtsTcNo[]" id="txtGmtsTcNo_<? echo $i; ?>" class="text_boxes" style="width:90px" placeholder="Write" value="<? echo $gmtc_no = ($gmtc_no!="")?$gmtc_no:"";?>">
                                    </td>
                                    <td width="100" align="right"><? echo number_format($row['tc_rcv_qty'], 2, '.' , ''); ?>
                                        <input type="hidden" id="txtTcRcvQty_<? echo $i; ?>" value="<? echo number_format($row['tc_rcv_qty'], 2, '.' , ''); ?>" />  
                                    </td>
                                    <td width="100" align="right">
                                        <input type="text" name="txtUsedQty[]" id="txtUsedQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px" placeholder="Write" value="<? echo $current_used_qty = ($current_used_qty>0)?number_format($current_used_qty, 2, '.' , ''):"";?>">
                                    </td>
                                    <td width="100" align="right">
                                        <? echo number_format($cum_used_qty, 2, '.', '');?>
                                        <input type="hidden" id="txtCumUsedQty_<? echo $i; ?>" value="<? echo number_format($cum_used_qty, 2, '.' , ''); ?>" />
                                    </td>
                                    <td width="100" align="right">
                                        <? echo number_format($tc_balance_qty, 2, '.', '');?>
                                        <input type="hidden" id="txtTcBbalanceQty_<? echo $i; ?>" value="<? echo number_format($tc_balance_qty, 2, '.' , ''); ?>" />
                                    </td>
                                    <td align="left"><p><? echo $remarks; ?></p></td>
                                </tr>
                                <?
                                $i++;
                            }
                        }
                    }
                }
                ?>
                <input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
            </tbody>
        </table>

                
        <table width="768" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%;" align="center">
                        <input type="button" name="close" onClick="js_set_value_qnty()" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </td>
            </tr>
        </table>
    </div>  

    <script type="text/javascript">
        setFilterGrid('table_body',-1);
        set_all();
    </script>
    <?
    exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $con = connect();

    $location_id=str_replace("'","",$cbo_location);
    $pi_no=str_replace("'","",$txt_pi_no);
    $pi_id=str_replace("'","",$pi_id);
    $btbLc_id=str_replace("'","",$btbLc_id);
    $txt_mrr_no=str_replace("'","",$txt_mrr_no);
    $tc_no_type=str_replace("'","",$cbo_tc_no_type);
    $tc_no=str_replace("'","",$txt_tc_no);
    $transaction_type_id=str_replace("'","",$cbo_transaction_type);  


    if(str_replace("'","",$cbo_location)==0) $location_cond=""; else $location_cond= " and b.location_id=$location_id";
    if(str_replace("'","",$cbo_store_name)==0) $store_cond=""; else $store_cond= " and b.store_id in(".str_replace("'","",$cbo_store_name).")";
    if(str_replace("'","",$txt_pi_no)=='') $pi_nunber_cond=""; else $pi_nunber_cond=" and a.booking_no='$pi_no'";
    if(str_replace("'","",$txt_pi_no)=='' && str_replace("'","",$pi_id)=='') $pi_id_cond=""; else $pi_id_cond="and b.pi_wo_batch_no='$pi_id'"; 
    
    $tc_no_cond="";
    if(str_replace("'","",$tc_no)!="") $tc_no_cond .=" and b.rd_no='$tc_no'";
    if($tc_no_type==2) $tc_no_cond .=" and b.rd_no is null"; else $tc_no_cond .="and b.rd_no is not null";   

    if($transaction_type_id==1)
    {
        if(str_replace("'","",$btbLc_id)=='') $lc_no_cond=""; else $lc_no_cond=" and a.lc_no=$btbLc_id";

        if(str_replace("'","",$txt_mrr_no)=='') $mrr_no_cond=""; else $mrr_no_cond="and a.recv_number_prefix_num like '%".trim($txt_mrr_no)."%'";
        if(str_replace("'","",$txt_lot_no)=='') $lot_no_cond=""; else $lot_no_cond="and c.lot=".trim($txt_lot_no);
    }   
    else if($transaction_type_id==3)
    {
        $pi_id = return_field_value("pi_id", "com_btb_lc_pi", "com_btb_lc_master_details_id=$btbLc_id", "pi_id");
        if(str_replace("'","",$btbLc_id)=='') $lc_no_cond=""; else $lc_no_cond=" and b.pi_wo_batch_no='$pi_id'";
        if(str_replace("'","",$txt_mrr_no)=='') $mrr_no_cond=""; else $mrr_no_cond="and a.issue_number_prefix_num like '%".trim($txt_mrr_no)."%'";
        if(str_replace("'","",$txt_lot_no)=='') $lot_no_cond=""; else $lot_no_cond="and c.lot=".trim($txt_lot_no);
    }else{
        if(str_replace("'","",$txt_mrr_no)=='') $mrr_no_cond=""; else $mrr_no_cond="and a.issue_number_prefix_num like '%".trim($txt_mrr_no)."%'";
        if(str_replace("'","",$txt_int_ref)=='') $int_ref_cond=""; else $int_ref_cond="and f.grouping=".trim($txt_int_ref);
        if(str_replace("'","",$txt_lot_no)=='') $lot_no_cond=""; else $lot_no_cond="and e.lot=".trim($txt_lot_no);
    }

    $from_date=str_replace("'","",$txt_date_from);
    $to_date=str_replace("'","",$txt_date_to);
    
    $trans_date_cond = "";
    if ($db_type == 0) {
        if ($from_date != "" && $to_date != "")
            $trans_date_cond .= " and b.transaction_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
    }
    else {
        if ($from_date != "" && $to_date != "")
            $trans_date_cond .= " and b.transaction_date between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
    }

    $buyer_id_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $supplier_id_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');

    if($transaction_type_id==1) // Receive
    {
        $sql_trans="SELECT a.id as mst_id,b.id as trans_id, a.recv_number AS mrr_number, a.receive_date AS mrr_date, a.supplier_id,a.booking_no,a.lc_no AS lc_no_id, d.lc_number, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot, b.prod_id, SUM (b.cons_quantity)     AS mrr_qty, b.rd_no as tc_no,b.fabric_ref as tc_remarks FROM inv_receive_master a LEFT JOIN com_btb_lc_master_details d ON a.lc_no = d.id, inv_transaction b, product_details_master c WHERE  a.item_category = 1 AND a.entry_form IN (1) AND a.company_id = $cbo_company_name AND a.id = b.mst_id AND b.item_category = 1 AND b.transaction_type IN (1) AND b.prod_id = c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 $location_cond $store_cond $pi_nunber_cond $pi_id_cond $lc_no_cond $trans_date_cond $tc_no_cond $mrr_no_cond $lot_no_cond GROUP BY a.id,b.id,a.recv_number, a.receive_date, a.supplier_id,a.booking_no,a.lc_no, d.lc_number, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot, b.prod_id,b.rd_no,b.fabric_ref";
        //echo $sql_trans;

        $dataArray=sql_select($sql_trans);
    }
    else if($transaction_type_id==3) // Receive Return
    {
        $sql_trans="SELECT a.id as mst_id,b.id as trans_id, a.issue_number AS mrr_number, a.issue_date AS mrr_date, d.lc_no AS lc_no_id, e.lc_number, a.received_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot, b.prod_id, SUM (b.cons_quantity) AS mrr_qty, (SELECT ( max(invoice_no) || '**' | | max(invoice_date))  AS lc_inv_no_date FROM com_import_invoice_mst x, com_import_invoice_dtls y WHERE x.ID = y.import_invoice_id AND d.lc_no = y.btb_lc_id) AS lc_inv_no_date, (SELECT CASE WHEN i.is_lc_sc = 1 THEN j.buyer_name ELSE k.buyer_name END FROM com_btb_export_lc_attachment i, com_export_lc j, com_sales_contract k WHERE i.lc_sc_id = j.id AND i.lc_sc_id = k.id AND d.lc_no = i.import_mst_id) AS lc_buyer_id, b.rd_no as tc_no,b.fabric_ref as tc_remarks FROM inv_issue_master a, inv_transaction b, product_details_master c,inv_receive_master d LEFT JOIN com_btb_lc_master_details e ON d.lc_no = e.id WHERE a.item_category = 1 AND a.entry_form IN (8) AND a.company_id = $cbo_company_name AND a.id = b.mst_id AND a.received_id=d.id AND b.item_category = 1 AND b.transaction_type IN (3) AND b.prod_id = c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 $location_cond $store_cond $pi_nunber_cond $pi_id_cond $lc_no_cond $trans_date_cond $tc_no_cond $mrr_no_cond $lot_no_cond GROUP BY a.id,b.id,a.issue_number, a.issue_date, d.lc_no, e.lc_number, a.received_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot, b.prod_id,b.rd_no,b.fabric_ref";

        $dataArray=sql_select($sql_trans);
    }
    else // Issue 
    {
       $r_id=execute_query("delete from tmp_prod_id where userid=$user_id",0);
       $r_id2=execute_query("delete from tmp_issue_id where user_id=$user_id",0);
       if($r_id){oci_commit($con);}
       if($r_id2){oci_commit($con);}

        $sql_trans="SELECT a.id AS mst_id, b.id AS trans_id, g.buyer_name AS buyer_id, e.yarn_count_id, e.yarn_comp_type1st, e.yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type, e.color, e.lot, b.prod_id, g.sustainability_standard, f.GROUPING  AS int_ref_numbers, b.cons_quantity AS mrr_qty, h.id AS allocation_id, h.qnty AS allocated_qty, b.rd_no AS tc_no, b.manufacture_date AS qcs_submit_date, b.fabric_ref  AS tc_remarks, b.cutable_width AS qcs_no FROM inv_issue_master a, inv_transaction b, order_wise_pro_details c,inv_material_allocation_dtls  d, product_details_master e, wo_po_break_down f, wo_po_details_master g, inv_material_allocation_mst h WHERE a.id = b.mst_id AND b.id = c.trans_id AND b.prod_id = c.prod_id AND c.prod_id = d.item_id AND d.item_id = e.id AND c.po_breakdown_id = d.po_break_down_id AND d.po_break_down_id = f.id AND f.job_id = g.id AND d.mst_id = h.id AND d.item_id = h.item_id  AND a.issue_basis = 3 AND a.issue_purpose = 1 AND a.item_category = 1 AND a.entry_form = 3 AND a.company_id = '2'   AND c.trans_type = 2 AND c.entry_form = 3 AND b.item_category = 1 AND b.transaction_type = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND f.status_active = 1 AND f.is_deleted = 0 $location_cond $store_cond $trans_date_cond $tc_no_cond $mrr_no_cond $lot_no_cond $int_ref_cond"; 

        //echo $sql_trans; die;

        $dataArray=sql_select($sql_trans);
        
        $issue_data=$trans_id_check=$allocation_id_check = $item_id_check= $issue_id_check= array(); 
        foreach ($dataArray as $row) 
        {
            $int_ref = $row[csf("int_ref_numbers")];
            $prod_id = $row[csf("prod_id")];

            if($item_id_check[$row[csf("prod_id")]]=="")
            {
                $item_id_check[$row[csf("prod_id")]]=$row[csf("prod_id")];
                $r_id1=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,".$prod_id.")",0);
                if($r_id1) $r_id1=1; else {echo "insert into tmp_prod_id (userid, prod_id) values ($user_id,".$prod_id.")";oci_rollback($con);die;}
            }

            if($issue_id_check[$row[csf("mst_id")]]=="")
            {
                $issue_id_check[$row[csf("mst_id")]]=$row[csf("mst_id")];
                $r_id3=execute_query("insert into tmp_issue_id (user_id, issue_id) values ($user_id,".$row[csf("mst_id")].")",0); 
                if($r_id3) $r_id3=1; else {echo "insert into tmp_issue_id (user_id, issue_id) values ($user_id,".$row[csf("mst_id")].")";oci_rollback($con);die;}
            }

            if($trans_id_check[$row[csf("mst_id")]][$row[csf("trans_id")]]=="")
            {
                
                $trans_id_check[$row[csf("mst_id")]][$row[csf("trans_id")]] = $row[csf("trans_id")]; 

                $issue_data[$prod_id][$int_ref][trans_id]       = $row[csf("trans_id")];               
                $issue_data[$prod_id][$int_ref][yarn_count_id]  = $row[csf("yarn_count_id")];
                $issue_data[$prod_id][$int_ref][yarn_comp1st]   = $row[csf("yarn_comp_type1st")];
                $issue_data[$prod_id][$int_ref][comp_type2nd]   = $row[csf("yarn_comp_type2nd")];
                $issue_data[$prod_id][$int_ref][percent1st]     = $row[csf("yarn_comp_percent1st")];
                $issue_data[$prod_id][$int_ref][percent2nd]     = $row[csf("yarn_comp_percent2nd")];
                $issue_data[$prod_id][$int_ref][yarn_type]      = $row[csf("yarn_type")];
                $issue_data[$prod_id][$int_ref][color]          = $row[csf("color")];
                $issue_data[$prod_id][$int_ref][lot]            = $row[csf("lot")];
                $issue_data[$prod_id][$int_ref][sustainability] = $row[csf("sustainability_standard")];
                $issue_data[$prod_id][$int_ref][buyer_id]       = $row[csf("buyer_id")];
                $issue_data[$prod_id][$int_ref][tc_no]          = $row[csf("tc_no")];
                $issue_data[$prod_id][$int_ref][qcs_submit_date]= $row[csf("qcs_submit_date")];
                $issue_data[$prod_id][$int_ref][tc_remarks]     = $row[csf("tc_remarks")];
                $issue_data[$prod_id][$int_ref][qcs_no]         = $row[csf("qcs_no")];
                $issue_data[$prod_id][$int_ref][mrr_qty]       += $row[csf("mrr_qty")];
            } 

            if( $allocation_id_check[$row[csf("allocation_id")]][$int_ref]=="" )
            {
                $allocation_id_check[$row[csf("allocation_id")]][$int_ref] = $row[csf("allocation_id")];
                $issue_data[$prod_id][$int_ref][allocated_qty] += $row[csf("allocated_qty")];
            }
        }

        $sql_return = "Select f.grouping AS int_ref_numbers, b.prod_id,c.quantity as issue_return_qty from tmp_prod_id d,tmp_issue_id g, inv_receive_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down  f where d.prod_id=b.prod_id and g.issue_id=a.issue_id and  a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id =f.id and c.trans_type=4 and a.entry_form=9  and b.item_category=1 and a.receive_basis=3 and b.receive_basis=3 and c.issue_purpose = 1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and f.status_active=1 $int_ref_cond";
        //echo $sql_return; die();
        $result_return = sql_select($sql_return);
        $issue_return_data = array();
        foreach ($result_return as $row) 
        {   
            $int_ref = $row[csf("int_ref_numbers")];
            $prod_id = $row[csf("prod_id")];
            $issue_return_data[$prod_id][$int_ref][rtn_qty] += $row[csf("issue_return_qty")];
        }
    }

    //echo "<pre>";
    //print_r($issue_return_data); 

    foreach($dataArray as $row)
    {
        if($row[csf('lc_no_id')] != "")
        {
            $all_lc_no_id.=$row[csf("lc_no_id")].",";
        }     
    }

    //echo $all_lc_no_id."test"; die();

    $all_lc_no_id_arr=array_unique(explode(",",chop($all_lc_no_id,",")));

    $lc_no_ids=$lc_no_ids_cond="";
    if($db_type==2 && count($all_lc_no_id_arr)>999)
    {
        $all_lc_no_id_chunk=array_chunk($all_lc_no_id_arr,999) ;
        foreach($all_lc_no_id_chunk as $chunk_arr)
        {
            $lc_no_ids.=" y.btb_lc_id in(".implode(",",$chunk_arr).") or ";    
        }
                
        $lc_no_ids_cond=" and (".chop($lc_no_ids,'or ').")";            
    }
    else
    {   
        $all_lc_no_id= implode(",",array_unique(explode(",",chop($all_lc_no_id,","))));
        $lc_no_ids_cond=" and y.btb_lc_id in($all_lc_no_id)"; 
    }

    $sql_lc = "SELECT y.btb_lc_id, z.is_lc_sc, listagg (x.invoice_no,',') within group (order by x.invoice_no) as invoice_no , x.invoice_date, j.buyer_name, k.buyer_name as sales_contract_buye FROM com_import_invoice_mst  x, com_import_invoice_dtls y, com_btb_export_lc_attachment z, com_export_lc j left join com_sales_contract k on j.id = k.id AND K.status_active = 1 AND K.is_deleted =  0 WHERE  x.ID = y.import_invoice_id AND y.btb_lc_id = z.import_mst_id AND z.lc_sc_id = j.id  AND X.status_active = 1 AND X.is_deleted = 0 AND y.status_active = 1 AND y.is_deleted = 0 AND Z.status_active = 1 AND Z.is_deleted = 0 AND J.status_active = 1 AND J.is_deleted = 0 $lc_no_ids_cond GROUP BY  x.invoice_date, y.btb_lc_id, z.is_lc_sc, j.buyer_name, k.buyer_name ORDER BY y.btb_lc_id";

    $sql_lc_result = sql_select($sql_lc);
    $lc_data_arr = array();
    foreach($sql_lc_result as $row)
    {
        $lc_data_arr[$row[csf("btb_lc_id")]]['invoice_no'] = implode(",",array_unique(explode(",",$row[csf("invoice_no")])));

        $lc_data_arr[$row[csf("btb_lc_id")]]['invoice_date'] = $row[csf("invoice_date")];

        if($row[csf("is_lc_sc")] ==1 || $row[csf("sales_contract_buye")]=="")
        {
            $lc_data_arr[$row[csf("btb_lc_id")]]['buyer_id'] = $row[csf("buyer_name")];
        }
        else{
            $lc_data_arr[$row[csf("btb_lc_id")]]['buyer_id'] = $row[csf("sales_contract_buye")];
        }       
    }
                  
    ob_start();
    if ($transaction_type_id==1 || $transaction_type_id==3) // Rcv and Rcv Rtn
    {
        ?>
        <div style="width:100%; margin-left:10px;" align="center">            
            <table width="1830" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
                <thead>
                    <tr>
                      <th colspan="18">Yarn <? echo $transaction_type[$transaction_type_id]; ?></th>
                    </tr>
                    <tr>
                        <th width="60">Check All <input id="all_check" onClick="check_all('all_check')" type="checkbox"> </th>
                        <th width="50">SL</th>
                        <th width="120">MRR No</th>
                        <th width="80">MRR Date</th>
                        <th width="100">Supplier</th>
                        <th width="100">PI/WO No</th>
                        <th width="100">LC No</th>
                        <th width="100">Count</th>
                        <th width="150">Composition</th>
                        <th width="80">Type</th>
                        <th width="80">Color</th>
                        <th width="100">Lot</th>
                        <th width="80">MRR Qty.</th>
                        <th width="80">Inv No</th>
                        <th width="80">Inv Date</th>
                        <th width="100">Buyer</th>                       
                        <th width="100">TC No</th>                                         
                        <th width="150">Remarks</th>
                    </tr>
                </thead>
            </table>

            <div style="width:1850px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
                <table width="1830" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">  
                    <tbody>
                        <? 
                        if(!empty($dataArray))
                        {
                            $sl=1;
                            $compos=''; 
                            foreach($dataArray as $row)
                            {
                                if ($sl%2==0)
                                    $bgcolor="#E9F3FF";
                                else
                                    $bgcolor="#FFFFFF";

                                $compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%";

                                if($row[csf('yarn_comp_percent2nd')]>0)
                                {
                                    $compos.=" ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]."%";
                                }

                                $buyer_id = $lc_data_arr[$row[csf("lc_no_id")]]['buyer_id']; 
                                $lc_invoice_no = $lc_data_arr[$row[csf("lc_no_id")]]['invoice_no'];
                                $lc_invoice_date = $lc_data_arr[$row[csf("lc_no_id")]]['invoice_date'];  
                                $update_id = $row[csf('trans_id')];  
                                
                                if( $row[csf('tc_no')]!="" && $tc_no_type==1)
                                {
                                    $checkedRow = "checked='checked'";
                                }else {
                                    $checkedRow = "";
                                } 
                            
                                ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $sl;?>','<? echo $bgcolor;?>')" id="tr<? echo $sl;?>" >
                                    <td width="60" align="center">
                                    <input id="check_<? echo  $update_id;?>"  type="checkbox"  <? echo $checkedRow;?> value="<? echo $update_id."**".$row[csf('prod_id')]."**".$transaction_type_id;?>">
                                    </td>
                                    <td width="50" align="center"><? echo $sl; ?></td>
                                    <td width="120"><p>&nbsp;<? echo $row[csf('mrr_number')]; ?></p></td>
                                    <td width="80" align="center">&nbsp;<? echo change_date_format($row[csf('mrr_date')]); ?></td>
                                    <td width="100"><p>&nbsp;<? echo $supplier_id_arr[$row[csf('supplier_id')]]; ?></p></td>
                                    <td width="100"><p>&nbsp;<? echo $row[csf('booking_no')]; ?></p></td>
                                    <td width="100" title="<? echo $row[csf('lc_no_id')];?>"><p>&nbsp;<? echo $row[csf('lc_number')]; ?></p></td>
                                    <td width="100"><p>&nbsp;<? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
                                    <td width="150"style="word-break:break-all;min-width: 150px;"><p>&nbsp;<? echo $compos; ?></p></td>
                                    <td width="80" align="center"><p>&nbsp;<? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                                    <td width="80"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
                                    <td width="100" title="<? echo $row[csf('prod_id')];?>"><p>&nbsp;<? echo $row[csf('lot')]; ?></p></td>
                                    <td width="80"><p>&nbsp;<? echo number_format($row[csf('mrr_qty')],2,'.',''); ?></p></td>
                                    <td width="80">&nbsp;<? echo $lc_invoice_no;?></td>
                                    <td width="80">&nbsp;<? echo change_date_format($lc_invoice_date);?></td>
                                    <td width="100" title="<? echo $buyer_id; ?>">&nbsp;<? echo $buyer_id_arr[$buyer_id];?></td>
                                    <td width="100"><input type="text" name="tc_no[]" id="tc_no_<? echo $update_id;?>" style="width:85px" class="text_boxes"  placeholder="Write" value="<? echo $tcNo= ($row[csf('tc_no')]=="")?'':$row[csf('tc_no')]; ?>" /></td>
                                    <td width="150"><input type="text" name="tc_remarks[]" id="tc_remarks_<? echo $update_id;?>" class="text_boxes"  placeholder="Write" style="width:90%;" value="<? echo $tc_remarks= ($row[csf('tc_remarks')]=="")?'':$row[csf('tc_remarks')]; ?>" /></td>
                                </tr>
                                <?
                                $tot_recv_qnty+=$row[csf('mrr_qty')];
                                $sl++;
                            }
                        }
                        else
                        {  
                            ?>
                            <tr bgcolor="orrange">
                                <td align="center" colspan="18" style="text-align: center;" valign="middle">
                                    <p style="color: white; font-size: 24px;"> Data not found. </p>
                                </td>
                            </tr>
                            <?
                        }                                                                        
                        ?>
                    </tbody>  
                </table> 
            </div>

            <table width="1830" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
                <tr class="tbl_bottom">
                    <td align="center" colspan="18" style="text-align: center;" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_yarn_tc_no_entry", 0, 0, "fnResetForm()", 1); ?>
                    </td>
                </tr>
            </table>
        </div>
        <?
    }
    else
    {
       ?>
        <div style="width:100%; margin-left:10px;" align="center">

            <table width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
                <thead>
                    <tr>
                      <th colspan="16">Yarn <? echo $transaction_type[$transaction_type_id]; ?></th>
                    </tr>
                    <tr>
                        <th width="60">Check All <input id="all_check" onClick="check_all('all_check')" type="checkbox"> </th>
                        <th width="50">SL</th>
                        <th width="100">Count</th>
                        <th width="150">Composition</th>
                        <th width="80">Type</th>
                        <th width="80">Color</th>
                        <th width="100">Lot</th>
                        <th width="100">Sustainability Standard</th>
                        <th width="150">Allocated Order No</th>
                        <th width="80">Allocated Qty.</th>
                        <th width="80">MRR Qty.</th>
                        <th width="100">Buyer</th>                       
                        <th width="100">TC No</th>
                        <th width="80">QCS Submit Date</th>
                        <th width="100">QCS No</th>                                         
                        <th width="150">Remarks</th>
                    </tr>
                </thead>
            </table>

            <div style="width:1720px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
                <table width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">  
                    <tbody>
                        <? 
                        if(!empty($dataArray))
                        {
                            $sl=1;
                            $compos=''; 
                            foreach($issue_data as $prodid=>$prod_arr)
                            {
                                foreach($prod_arr as $int_ref=>$row)
                                {                                  
                                    if ($sl%2==0)
                                    $bgcolor="#E9F3FF";
                                    else
                                        $bgcolor="#FFFFFF";

                                    $compos=$composition[$row['yarn_comp1st']]." ".$row['percent1st']."%";

                                    if($row['percent2nd']>0)
                                    {
                                        $compos.=" ".$composition[$row['comp_type2nd']]." ".$row['percent2nd']."%";
                                    }

                                    $qcs_submit_date = change_date_format($row['qcs_submit_date'], 'dd-mm-yy');
                                    
                                    $update_id = $row['trans_id'];  
                                    
                                    if( $row['tc_no']!="" && $tc_no_type==1)
                                    {
                                        $checkedRow = "checked='checked'";
                                    }else {
                                        $checkedRow = "";
                                    }
                                    
                                    $row['mrr_qty'] = ($row['mrr_qty'] - $issue_return_data[$prodid][$int_ref][rtn_qty]);
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $sl;?>','<? echo $bgcolor;?>')" id="tr<? echo $sl;?>" >
                                        <td width="60" align="center">
                                        <input id="check_<? echo  $update_id;?>"  type="checkbox"  <? echo $checkedRow;?> value="<? echo $update_id."**".$prodid."**".$transaction_type_id;?>">
                                        </td>
                                        <td width="50" align="center"><? echo $sl; ?></td>
                                        
                                        <td width="100"><p><? echo $count_arr[$row['yarn_count_id']]; ?></p></td>
                                        <td width="150"style="word-break:break-all;min-width: 150px;"><p><? echo $compos; ?></p></td>
                                        <td width="80" align="center"><p><? echo $yarn_type[$row['yarn_type']]; ?></p></td>
                                        <td width="80"><p><? echo $color_arr[$row['color']]; ?></p></td>
                                        <td width="100" title="<? echo $prodid;?>"><p><? echo $row['lot']; ?></p></td>
                                         
                                        <td width="100"><p><? echo $sustainability_standard[$row['sustainability']]; ?></p></td>
                                        <td width="150"><p><? echo $int_ref; ?></p></td>
                                        <td width="80"><p><? echo number_format($row['allocated_qty'],2,'.',''); ?></p></td>
                                        <td width="80"><p><? echo number_format($row['mrr_qty'],2,'.',''); ?></p></td>
                                        <td width="100" title="<? echo $buyer_id; ?>"><? echo $buyer_id_arr[$row['buyer_id']];?></td>

                                        <td width="100">
                                            <input type="text" name="tc_no[]" id="tc_no_<? echo $update_id;?>" onDblClick="openmypage_tc_no(<? echo $prodid;?>,<? echo $update_id;?>)" style="width:80px" class="text_boxes"  placeholder="Write or Browse" value="<? echo $tcNo= ($row['tc_no']=="")?'':$row['tc_no']; ?>" />

                                            <input type="hidden" name="tc_used_data" id="tc_used_data_<? echo $update_id;?>" value="" readonly />
                                        </td>

                                        <td width="80"><input type="text" name="qcs_submit_date[]" id="qcs_submit_date_<? echo $update_id;?>" class="datepicker" placeholder="Select Date" style="width:70px;" value="<? echo $submiteDate= ($qcs_submit_date=="")?'':$qcs_submit_date; ?>"/></td>

                                        <td width="100"><input type="text" name="qcs_no[]" id="qcs_no_<? echo $update_id;?>" style="width:80px" class="text_boxes"  placeholder="Write" value="<? echo $qcsNo= ($row['qcs_no']=="")?'':$row['qcs_no']; ?>" /></td>
                                        <td width="150"><input type="text" name="tc_remarks[]" id="tc_remarks_<? echo $update_id;?>" class="text_boxes"  placeholder="Write" style="width:140;" value="<? echo $tc_remarks= ($row['tc_remarks']=="")?'':$row['tc_remarks']; ?>" /></td>
                                    </tr>
                                    <?
                                    $tot_recv_qnty+=$row['mrr_qty'];
                                    $sl++;
                                }

                                
                            }
                        }
                        else
                        {  
                            ?>
                            <tr bgcolor="orrange">
                                <td align="center" colspan="16" style="text-align: center;" valign="middle">
                                    <p style="color: white; font-size: 24px;"> Data not found. </p>
                                </td>
                            </tr>
                            <?
                        }                                                                        
                        ?>
                    </tbody>  
                </table> 
            </div>

            <table width="1700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
                <tr class="tbl_bottom">
                    <td align="center" colspan="16" style="text-align: center;" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_yarn_tc_no_entry", 0, 0, "fnResetForm()", 1); ?>
                    </td>
                </tr>
            </table>
        </div>
        <?
    }
    ?>
    <script type="text/javascript">
        
        /* Name : Md. Didarul Alam 
           Date : 20-06-2022           
        */
        $(function() 
        {
            $( ".datepicker" ).datepicker({
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true
            });
        }); 
    </script>
    <?
    exit();
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0 || $operation==1)   // Update Alwasy
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        if(str_replace("'","",$tcdatastr)!="")
        {
            $tcDataArr = explode("___",$tcdatastr);
            $field_arrary_update = "rd_no*manufacture_date*fabric_ref";
            foreach ($tcDataArr as $data_string)
            {
                $dataArr = explode("**",$data_string);
                //echo "<pre>";
                //print_r($dataArr);
                $update_id =  $dataArr[0]; // $transaction_type_id = 1 or 3 then $dataArr[0] = trans_id else $dataArr[0] = mst_id
                $prod_Id =  $dataArr[1];
                $transaction_type = (int) $dataArr[2];
                $tc_no =  $dataArr[3];
                $qcs_submit_date = ($dataArr[4]==="undefined")?'' : date("d-M-Y", strtotime($dataArr[4]));
                $tc_remarks =  $dataArr[5];
                $qcs_no =  $dataArr[6];                
                $tc_used_qty_break_down_str = $dataArr[7];
                

                if($transaction_type == 1 || $transaction_type == 3)
                {
                    if( str_replace("'","",$tc_no) !="" )
                    {
                        $rID =execute_query(" UPDATE inv_transaction set rd_no='".$tc_no."', manufacture_date='".$qcs_submit_date."', fabric_ref='".$tc_remarks."' where id=$update_id");
                    }
                    // echo "10** UPDATE inv_transaction set rd_no='".$tc_no."', manufacture_date='".$qcs_submit_date."', fabric_ref='".$tc_remarks."' where id=$update_id"."<br>";
                }
                else
                {   
                    if( str_replace("'","",$tc_no) !="" )
                    {
                        $rID =execute_query(" UPDATE inv_transaction set rd_no='".$tc_no."', manufacture_date='".$qcs_submit_date."', fabric_ref='".$tc_remarks."', cutable_width='".$qcs_no."'  where id=$update_id and prod_id=$prod_Id");
                         //echo "10** UPDATE inv_transaction set rd_no='".$tc_no."', manufacture_date='".$qcs_submit_date."', fabric_ref='".$tc_remarks."', cutable_width='".$qcs_no."'  where id=$update_id and prod_id=$prod_Id"."<br>"; die();
                    }
                     
                    $used_qty_break_down_arr = explode("-YYYYYYY-",$tc_used_qty_break_down_str);

                    //echo "10**";
                    //echo "<pre>";
                   // print_r($used_qty_break_down_arr);

                    if(!empty($used_qty_break_down_arr))
                    {
                        foreach($used_qty_break_down_arr as  $tc_used_data_str)
                        {
                            $tc_used_data_arr = explode("-XXXX-",$tc_used_data_str);
                            $tc_rcv_trans_ids = $tc_used_data_arr[0];
                            
                            if(str_replace("'"," ",$tc_used_data_arr[1])!="")
                            {
                                $tc_used_qty_break_down = $update_id."_". str_replace("'"," ",$tc_used_data_arr[1]);  
                                $rID2 =execute_query(" UPDATE inv_transaction set reason_of_change='".$tc_used_qty_break_down."' where id in ($tc_rcv_trans_ids) ");
                            }
                            
                        }
                    }
                    
                }               
                 
            }          
        }

        //echo "10**". $rID ."##".$rID2; die();

        if($db_type==0)
        {
            if($transaction_type == 1 || $transaction_type == 3)
            {
                if($rID==1)
                {
                    mysql_query("COMMIT");
                    echo "0**";
                }
                else
                {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            }
            else
            {
                if( $rID==1 && $rID==1 )
                {
                    mysql_query("COMMIT");
                    echo "0**";
                }
                else
                {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($transaction_type == 1 || $transaction_type == 3)
            {
                if( $rID==1 )
                {
                    oci_commit($con);
                    echo "0**";
                }
                else
                {
                    oci_rollback($con);
                    echo "10**";
                }
            }
            else
            {
                if( $rID==1 && $rID==1 )
                {
                    oci_commit($con);
                    echo "0**";
                }
                else
                {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }
        disconnect($con);
        die;
    }  
}
?>
