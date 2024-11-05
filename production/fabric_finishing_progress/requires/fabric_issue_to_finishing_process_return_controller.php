<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_knitting_com")
{
    $data = explode("**",$data);
    $company_id=$data[1];

    if($data[0]==1)
    {
        echo create_drop_down( "cbo_service_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
    }
    else if($data[0]==3)
    {   
        $suppId="";
        $supplier_array= return_library_array("select id, tag_company from lib_supplier ",'id','tag_company');
        foreach ($supplier_array as $supplierId => $val) 
        {
            $tagCom=explode(',', $val);
            foreach ($tagCom as $keyx => $valx) 
            {
                if($company_id==$valx)
                {
                     $suppId.=$supplierId.",";
                }
            }
        }
        $suppId=chop($suppId,",");
        $suppIds="";

        $supplierIDs=explode(',', $suppId);
        if($db_type==2 && count($supplierIDs)>999)
        {
            $supplierIDs_chunk=array_chunk($supplierIDs,999) ;
            $supplierIDs_cond = " and (";

            foreach($supplierIDs_chunk as $chunk_arr)
            {
                $supplierIDs_cond.=" id in(".implode(",",$chunk_arr).") or ";
            }

            $supplierIDs_cond = chop($supplierIDs_cond,"or ");
            $supplierIDs_cond .=")";

        }
        else
        {
            $supplierIDs_cond=" and id in($suppId)";
        }
        //echo "select id, party_type from lib_supplier where status_active=1 $supplierIDs_cond ";
        $party_type_array= return_library_array("select id, party_type from lib_supplier where status_active=1 $supplierIDs_cond ",'id','party_type');
        //$party_type_array= return_library_array("select id, party_type from lib_supplier where id in($suppId) ",'id','party_type');
        
        $partyTpeArr=array(25,90,21,23,9,24,22,95,20,2);
        foreach ($party_type_array as $supplierIds => $vals) 
        {
            $tagPartyType=explode(',', $vals);
            foreach ($tagPartyType as $keyxx => $valxx) 
            {
                if(in_array($valxx, $partyTpeArr))
                //if($valxx==9)
                {
                     $suppIds.=$supplierIds.",";
                }
            }
        }
        $suppIds=chop($suppIds,",");
        $suppIds=implode(",", array_unique(explode(",", $suppIds)));

        echo create_drop_down( "cbo_service_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.id in($suppIds)  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
    }
    else
    {
        echo create_drop_down( "cbo_service_company", 152, $blank_array,"",1, "-- Select --", 0, "" );
    }
    exit();
}

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if($action=="check_booking_no______bk")
{
    $data=explode("**",$data);
    
    $sql= "select a.id,a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,c.job_no_prefix_num, a.job_no, a.po_break_down_id, a.process,a.item_category, a.fabric_source, a.supplier_id from wo_booking_mst a ,wo_po_details_master c where $company $buyer $booking_date and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and a.job_no=c.job_no  $booking_cond $job_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.job_no, a.booking_date, a.company_id, a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source, c.job_no_prefix_num, a.supplier_id,a.process order by booking_no_prefix_num desc"; 
    
    $sql="select id, booking_no,job_no from wo_booking_mst where booking_no='".trim($data[0])."' and company_id='".$data[1]."' and is_deleted=0 and status_active=1";
    $data_array=sql_select($sql,1);
    if(count($data_array)>0)
    {
        echo $data_array[0][csf('id')]."**".$data_array[0][csf('booking_no')]."**".$data_array[0][csf('job_no')];
    }
    else
    {
        echo "0";
    }
    exit(); 
}

if($action=="issue_popup")
{
    // echo load_html_head_contents("Issue Info", "../../../", 1, 1,'','','');
    echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?> 

    <script>
        function js_set_value(id)
        {
            $('#hidden_system_id').val(id); 
            //return;
            parent.emailwindow.hide();
        }
    </script>

    </head>

    <body>
        <div align="center" style="width:760px;">
            <form name="searchwofrm"  id="searchwofrm">
                <fieldset style="width:760px; margin-left:2px">
                    <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                        <thead>
                            <th class="must_entry_caption">Company</th>
                            <th>Issue To Process Date</th>
                            <th>Search By</th>
                            <th id="search_by_td_up" width="180">Please Enter Issue No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                <input type="hidden" name="hidden_system_id" id="hidden_system_id">  
                            </th> 
                        </thead>
                        <tr class="general">
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "",0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                            </td>
                            <td align="center"> 
                                <?
                                $search_by_arr=array(1=>"Issue To Fin. Process",2=>"Work Order No");
                                $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";                         
                                echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
                                ?>
                            </td>     
                            <td align="center" id="search_by_td">               
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />   
                            </td>                       
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_challan_search_list_view', 'search_div', 'fabric_issue_to_finishing_process_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </table>
                    <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_challan_search_list_view")
{
    $data = explode("_",$data);
    
    $search_string="%".trim($data[0])."";
    $search_by=$data[1];
    $start_date =$data[2];
    $end_date =$data[3];
    $company_id =$data[4];
    $chalan_year =$data[5];    

    if ($company_id==0) 
    {
        echo 'Please Select Company';die;
    }
    
    if($start_date!="" && $end_date!="")
    {
        if($db_type==0)
        {
            $date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
        }
    }
    else
    {
        $date_cond="";
    }

    $search_field_cond="";
    if(trim($data[0])!="")
    {
        if($search_by==1) $search_field_cond="and a.recv_number like '$search_string'";
        if($search_by==2) $search_field_cond="and b.booking_no like '$search_string'";
    }

    if($db_type==0) 
    {
        $year_field="YEAR(a.insert_date) as year,";
        $year_cond=" and YEAR(a.insert_date)= '$chalan_year'";
    }
    else if($db_type==2) 
    {
        $year_field="to_char(a.insert_date,'YYYY') as year,";
        $year_cond=" and to_char(a.insert_date,'YYYY') = '$chalan_year'";
    }
    else $year_field="";//defined Later


    $sql = "SELECT a.id, $year_field a.recv_number_prefix_num, a.recv_number, a.company_id, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order, sum(b.batch_issue_qty) as issue_qty
    from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.company_id=$company_id and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond  $year_cond
    group by a.id, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.company_id , a.dyeing_source, a.dyeing_company, a.receive_date,b.booking_without_order
    order by a.id";

    //echo $sql;//die;
    $result = sql_select($sql);

    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="100">Company</th>
            <th width="110">System No</th>
            <th width="130">Dyeing Source</th>
            <th width="160">Dyeing Company</th>
            <th width="50">Issue date</th>
            <th>Issue Qty</th>
        </thead>
    </table>
    <div style="width:700px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_list_search">  
            <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   

                $dye_comp="&nbsp;";
                if($row[csf('dyeing_source')]==1)
                    $dye_comp=$company_arr[$row[csf('dyeing_company')]]; 
                else
                    $dye_comp=$supllier_arr[$row[csf('dyeing_company')]]; 
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'*'.$row[csf('booking_without_order')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="100"><p>&nbsp;<? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="110"><p>&nbsp;<? echo $row[csf('recv_number')]; ?></p></td>
                    <td width="130"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                    <td width="160"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                    <td width="50" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td align="right"><p><? echo number_format($row[csf('issue_qty')],2,".",""); ?></p></td>
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

if($action=="populate_data_from_data_from_issue") // master part populate here
{
    $data = explode("*",$data);
    //$sql = "select id, company_id, recv_number, dyeing_source, dyeing_company, receive_date,receive_basis,gate_pass_no,do_no,car_no from inv_receive_mas_batchroll where id=$data[0] and entry_form=91";

    $sql = "SELECT a.id, a.company_id, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, a.receive_basis, a.gate_pass_no, a.do_no, a.car_no, b.booking_no  
    from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.id=$data[0] and a.entry_form=91
    group by a.id, a.company_id, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, a.receive_basis, a.gate_pass_no, a.do_no, a.car_no, b.booking_no";

    //echo $sql;
    $res = sql_select($sql);    
    foreach($res as $row)
    {       
        echo "$('#txt_issue_no').val('".$row[csf("recv_number")]."');\n";
        echo "$('#txt_issue_id').val('".$row[csf("id")]."');\n";
        echo "$('#txt_woorder_no').val('".$row[csf("booking_no")]."').attr('disabled','true');\n";

        echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
        echo "$('#cbo_company_id').attr('disabled','true')".";\n";
        echo "$('#cbo_service_source').attr('disabled','true')".";\n";
        echo "$('#txt_issue_date').val('".change_date_format($row[csf("receive_date")])."');\n";
        echo "$('#cbo_service_source').val(".$row[csf("dyeing_source")].");\n";

        echo "load_drop_down( 'requires/fabric_issue_to_finishing_process_return_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
        echo "$('#cbo_service_company').val(".$row[csf("dyeing_company")].");\n";
        echo "$('#cbo_service_company').attr('disabled','true')".";\n";
    }
    exit(); 
}

// new  *******************************************************************************************************

if ($action=="grey_item_details_from_issue") // issue update list view // grey_item_details_update
{
    $data=explode("_",$data); 
    $datas=explode("*",$data[0]);
    $floor_name_array=return_library_array( "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1", "id", "floor_name");

    $sql=sql_select("SELECT b.id, b.mst_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.buyer_id, b.job_no, b.order_id, b.width_dia_type, b.color_range_id, b.batch_id,a.dyeing_source, a.batch_no, b.color_id, b.process_id, b.batch_wgt, b.batch_issue_qty, b.outbound_batchname, b.fin_dia, b.fin_gsm, b.roll_no,a.receive_basis, b.booking_no,b.booking_dtls_id, b.booking_date,b.booking_without_order,b.rate, b.remarks, c.fabric_color_id 
    from pro_grey_batch_dtls b left join wo_booking_dtls c on b.booking_dtls_id=c.id, inv_receive_mas_batchroll a 
    where b.mst_id=$datas[0] and b.mst_id=a.id and a.entry_form=91 and b.status_active=1 and b.is_deleted=0 order by b.id"  );
    
    $body_part_ids="";$order_ids="";$prod_ids="";$color_ids="";$booking_nos="";$febric_description_ids="";$booking_dtls_id="";
    foreach($sql as $row)
    {
        $booking_without_order_status=$row[csf('booking_without_order')];
        $body_part_ids.=$row[csf('body_part_id')].',';
        $order_ids.=$row[csf('order_id')].',';
        $prod_ids.=$row[csf('prod_id')].',';
        $color_ids.=$row[csf('color_id')].',';
        $booking_dtls_id.=$row[csf('booking_dtls_id')].',';
        $booking_nos.="'".$row[csf('booking_no')]."'".',';
        $febric_description_ids.="'".$row[csf('febric_description_id')]."'".',';

        if($row[csf('booking_without_order')] == 0){
            $color_ids.=$row[csf('fabric_color_id')].',';
        }
    }

    $body_part_id_all=chop($body_part_ids,",");
    $order_id_all=chop($order_ids,",");
    $prod_id_all=chop($prod_ids,",");
    $color_id_all=chop($color_ids,",");
    $booking_no_all=chop($booking_nos,",");
    $booking_dtls_id=chop($booking_dtls_id,",");
    $febric_description_id_all=chop($febric_description_ids,",");
    //-----

    $color_arr = return_library_array("select id, color_name from lib_color where id in($color_id_all)","id","color_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
    $order_arr = return_library_array("select id, po_number from wo_po_break_down where id in($order_id_all)","id","po_number");
    $internalRef_arr = return_library_array("select id, grouping from wo_po_break_down where id in($order_id_all)","id","grouping");

    $composition_arr=array(); $constructtion_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($febric_description_id_all)";
    $data_array=sql_select($sql_deter);
    foreach( $data_array as $row )
    {
        if(array_key_exists($row[csf('id')],$composition_arr))
        {
            $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
        }
        else
        {
            $composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
        }
    }

    
    $feb_des_data = sql_select("SELECT b.id as dtls_id, a.wo_no as booking_no,b.fabric_description as fab_des_id,b.fabric_source
    from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c where a.id = b.wo_id and b.fab_booking_no = c.booking_no and a.company_id = $data[1] and  a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and c.status_active =1 and a.wo_no in($booking_no_all) and b.fabric_description in($febric_description_id_all)  
    union all
    select b.id as dtls_id, a.booking_no, b.fab_des_id,b.fabric_source
    from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c 
    where a.id = b.mst_id and b.fab_des_id =  c.id and a.company_id = $data[1]  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and c.status_active =1 and a.booking_no in($booking_no_all) and b.fab_des_id in($febric_description_id_all)");

    $feb_description_datas="";
    foreach ($feb_des_data as $value) 
    {
        $feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["fabric_source"]= $value[csf("fabric_source")];
        $feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["feb_des_id"]= $value[csf("fab_des_id")];
        $feb_description_datas.=$value[csf("fab_des_id")].',';
    }

    $feb_description_data=chop($feb_description_datas,',');
    if($feb_description_data!=""){$feb_description_data_cond=" and a.id in($feb_description_data)";}else{$feb_description_data_cond="";}
    if($feb_description_data!=""){$feb_description_data_cond_2=" and c.id in($feb_description_data)";}else{$feb_description_data_cond_2="";}


    $lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13 and id in($prod_id_all)",'id','product_name_details');

    if($febric_description_id_all!="")
    {
        $sql_order_feb=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 and c.id in($febric_description_id_all) $feb_description_data_cond_2 order by c.id");
        foreach($sql_order_feb as $row)
        {
            $fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];  
        }
    }

    if($datas[1]==1 && $febric_description_id_all!="")
    {
        $sql_non_order=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in($febric_description_id_all) $feb_description_data_cond order by a.id");
        foreach($sql_non_order as $row)
        {
            $fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
        }
    }

    //==============================================
    
    $previousIssueArrNew = array();
    $previousIssueRes=sql_select("SELECT a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id 
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
    where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91  and a.booking_dtls_id in($booking_dtls_id) ");
    foreach($previousIssueRes as $row2)
    {
        $previousIssueArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];
    }

    $previousReceiveArrNew = array();
    $previousReceiveRes=sql_select("SELECT a.grey_used,a.order_id,a.booking_no,a.body_part_id,a.color_id,a.febric_description_id as deter_id,a.booking_dtls_id,a.id  
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b
    where a.mst_id = b.id  and a.booking_dtls_id in($booking_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.entry_form = 92");
    foreach($previousReceiveRes as $row2)
    {
        $previousReceiveArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('grey_used')];
    }

    $previousReturnArrNew = array();
    $previousReturn=sql_select("SELECT a.batch_issue_qty,a.order_id,a.booking_no,a.body_part_id,a.color_id,a.febric_description_id as deter_id,a.booking_dtls_id,a.id  
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b
    where a.mst_id = b.id  and a.booking_dtls_id in($booking_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.entry_form = 554");
    foreach($previousReturn as $row)
    {
        $previousReturnArrNew[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]+=$row[csf('batch_issue_qty')];
    }

    $total_row=count($sql);
    $current_row_array=array();
    $i=1;
    foreach($sql as $val)
    {
        if ($i%2==0)  
            $bgcolor="#E9F3FF";
        else
            $bgcolor="#FFFFFF";

        $batch_name=$val[csf("batch_no")];
        $gsm=$val[csf("gsm")];
        $dia=$val[csf("width")];

        $previousIssueQty=$previousIssueArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];
        $previousReceiveQty=$previousReceiveArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];
        $previous_total_return_qty=$previousReturnArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];

        $balance = (($previousIssueQty - $previousReceiveQty) - $previous_total_return_qty);
        // $balance = $val[csf("batch_wgt")] - $previousIssueArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];
        /*issue = 100
        recv = 10
        balance = 90 (issue - recv)
        return = 20
        new balance = 90-20 = 70 ((issue - recv)-return)*/

        // $balance = $previousIssueArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]]-$previousReceiveArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];
        // echo  $previousIssueArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]].'-'. $previousReceiveArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]].'<br>';

        

        $feb_des_id = $feb_des_array[$val[csf("booking_no")]][$val[csf("booking_dtls_id")]]["feb_des_id"];
        $feb_des_source = $feb_des_array[$val[csf("booking_no")]][$val[csf("booking_dtls_id")]]["fabric_source"];

        if($val[csf("order_id")] != "")
        {
            $fabric_details = $composition_arr[$val[csf("febric_description_id")]];
        }
        else
        {

            if($feb_des_id == "")
            {
                $fabric_details = $composition_arr[$val[csf("febric_description_id")]];
            }
            else
            {
                if($feb_des_source == 1)
                {
                    $fabric_details = $fabric_description[$feb_des_id];
                }else{
                    $fabric_details = $fabric_description2[$feb_des_id];
                }
            //$fabric_details = $composition_arr[$val[csf("febric_description_id")]];
            }
        }

        if ($balance>0) 
        {
        ?>
        <tr id="tr_<? echo $i; ?>" align="center" valign="middle">
            <td width="25" id="sl_<? echo $i; ?>"><?=$i;?></td>
            <td width="60" style="word-break:break-all;"  name="txtBatchNo[]" id="txtBatchNo_<? echo $i; ?>"><?=$val[csf('outbound_batchname')];?></td>
            <td style="word-break:break-all;" width="80" id="bodyPart_<? echo $i; ?>"><? echo $body_part[$val[csf("body_part_id")]]; ?></td>
            <td style="word-break:break-all;" width="120" id="<? echo $i; ?>" align="left"><? echo $fabric_details;//$composition_arr[$val[csf("febric_description_id")]]; ?></td>
            <td style="word-break:break-all;" width="50" id="gsm_<? echo $i; ?>"><? echo  $gsm; ?></td>
            <td style="word-break:break-all;" width="50" id="dia_<? echo $i; ?>"><? echo $dia;?></td>
            <td style="word-break:break-all;" width="70" id="color_<? echo $i; ?>"><? echo $color_arr[$val[csf("color_id")]]; ?></td>
            <td style="word-break:break-all;" width="70" id="fabColor_<? echo $i; ?>"><?
                if($row[csf('booking_without_order')] !=1)
                {
                    echo $color_arr[$val[csf("fabric_color_id")]]; 
                }else{
                    echo $color_arr[$val[csf("color_id")]]; 
                }?>
            </td>
            <td style="word-break:break-all;" width="60" id="diaType_<? echo $i; ?>"><? echo $fabric_typee[$val[csf("width_dai_type")]]; ?></td>            
            <td width="120" align="right" id="">
                <? 
                    echo create_drop_down( "cboProcess_$i", 120, $conversion_cost_head_array,"", 1, "-- Select Process --",$val[csf('process_id')] , "",1,"","","","","","","cboProcess[]" );
                ?>
            </td>
            <td width="60" style="word-break:break-all;" id="batchWeight_<? echo $i; ?>"><? echo $val[csf("batch_wgt")]; ?></td>
            <td width="60" id="txtRollNo_<? echo $i; ?>"><? echo  $val[csf("roll_no")]; ?></td>
            
            <td width="60" align="center" id=""><input type="text" id="txtReturnQty_<? echo $i; ?>" name="txtReturnQty[]" style=" width:40px" title="<? echo $val[csf("batch_issue_qty")];?>" class="text_boxes_numeric" onKeyUp="calculate_amount(<?echo $i?>);" placeholder="<? echo $balance;?>"/></td>
            
            <td style="word-break:break-all;" width="90" id="bookingNo_<? echo $i; ?>" align="left"><? echo $val[csf("booking_no")]; ?></td>
            <td style="word-break:break-all;" width="60" id="buyer_<? echo $i; ?>"><? echo $buyer_arr[$val[csf("buyer_id")]]; ?></td>
            <td style="word-break:break-all;" width="80" id="job_<? echo $i; ?>"><? echo $val[csf("job_no")]; ?></td>
            <td style="word-break:break-all;" width="100" id="order_<? echo $i; ?>" align="left"><? echo $order_arr[$val[csf("order_id")]]; ?></td>
            <td style="word-break:break-all;" width="100" id="internalRef_<? echo $i; ?>" align="left"><? echo $internalRef_arr[$val[csf("order_id")]]; ?></td>
            <td style="word-break:break-all;" width="" align="left"><input type="text" id="txtRemarks_<?php echo $i; ?>" name="txtRemarks[]"  class="text_boxes">
                <input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_<? echo $i; ?>" value="<? echo $val[csf("job_no")]?>"/>
                <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $val[csf("order_id")]; ?>"/>
                <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $val[csf("color_id")]; ?>"/>
                <input type="hidden" name="batchWgt[]" id="batchWgt_<? echo $i; ?>" value="<? echo $val[csf("batch_wgt")]; ?>"/>
                <input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<? echo $val[csf("batch_id")]; ?>"/>
                <input type="hidden" name="bodypartId[]" id="bodypartId_<? echo $i; ?>" value="<? echo $val[csf("body_part_id")]; ?>"/>
                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" value="<? echo $val[csf("buyer_id")]; ?>"/>
                <input type="hidden" name="determinationId[]" id="determinationId_<? echo $i; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
                <input type="hidden" name="widthTypeId[]" id="widthTypeId_<? echo $i; ?>" value="<? echo $val[csf("width_dai_type")]; ?>"/>
                <input type="hidden" name="finDia[]" id="finDia_<? echo $i; ?>" value="<? echo $dia; ?>"/>
                <input type="hidden" name="finGsm[]" id="finGsm_<? echo $i; ?>" value="<? echo $gsm; ?>"/>
                <input type="hidden" name="txtBookingNo[]" id="txtBookingNo_<? echo $i; ?>" value="<? echo $val[csf("booking_no")]; ?>"/>
                <input type="hidden" name="woRate[]" id="woRate_<? echo $i; ?>" value="<? echo $val[csf("rate")]; ?>"/>
                <?  $bookingType =  ($val[csf("booking_without_order")] == 1) ? "2" : "";?> 
                <input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $i; ?>" value="<? echo $bookingType; ?>"/>                
                <input type="hidden" name="bookingDtlsId[]" id="bookingDtlsId_<? echo $i; ?>" value="<? echo $val[csf("booking_dtls_id")]; ?>"/>
                <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"/>

                <!-- <input type="hidden" name="privCurrentQty[]" id="privCurrentQty_<? echo $i; ?>" value="<? //echo $prev_recv_balance_qty; ?>"/> -->
                <input type="hidden" name="totalReturnQty[]" id="totalReturnQty_<? echo $i; ?>" value="<? echo $previous_total_return_qty; ?>"/>
                <input type="hidden" name="txtReturnQtyHidden[]" id="txtReturnQtyHidden_<? echo $i; ?>" value=""/>
            </td>            
        </tr>
        <?
        }
        $i++;
    }
    exit();
}

if($action=="grey_item_details_update")
{
    $data=explode("_",$data); 
    $datas=explode("*",$data[0]);
    $floor_name_array=return_library_array( "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1", "id", "floor_name");

    $sql=sql_select("SELECT b.id, b.mst_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.buyer_id, b.job_no, b.order_id, b.width_dia_type, b.color_range_id, b.batch_id,a.dyeing_source, a.batch_no, b.color_id, b.process_id, b.batch_wgt, b.batch_issue_qty, b.outbound_batchname, b.fin_dia, b.fin_gsm, b.roll_no,a.receive_basis, b.booking_no,b.booking_dtls_id, b.booking_date,b.booking_without_order,b.rate, b.remarks, c.fabric_color_id 
    from pro_grey_batch_dtls b left join wo_booking_dtls c on b.booking_dtls_id=c.id, inv_receive_mas_batchroll a 
    where b.mst_id=$datas[0] and b.mst_id=a.id and a.entry_form=554 and b.status_active=1 and b.is_deleted=0 order by b.id"  );
    
    $body_part_ids="";$order_ids="";$prod_ids="";$color_ids="";$booking_nos="";$febric_description_ids="";$booking_dtls_id="";
    foreach($sql as $row)
    {
        $booking_without_order_status=$row[csf('booking_without_order')];
        $body_part_ids.=$row[csf('body_part_id')].',';
        $order_ids.=$row[csf('order_id')].',';
        $prod_ids.=$row[csf('prod_id')].',';
        $color_ids.=$row[csf('color_id')].',';
        $booking_dtls_id.=$row[csf('booking_dtls_id')].',';
        $booking_nos.="'".$row[csf('booking_no')]."'".',';
        $febric_description_ids.="'".$row[csf('febric_description_id')]."'".',';

        if($row[csf('booking_without_order')] == 0){
            $color_ids.=$row[csf('fabric_color_id')].',';
        }
    }

    $body_part_id_all=chop($body_part_ids,",");
    $order_id_all=chop($order_ids,",");
    $prod_id_all=chop($prod_ids,",");
    $color_id_all=chop($color_ids,",");
    $booking_no_all=chop($booking_nos,",");
    $booking_dtls_id=chop($booking_dtls_id,",");
    $febric_description_id_all=chop($febric_description_ids,",");
    //-----

    $color_arr = return_library_array("select id, color_name from lib_color where id in($color_id_all)","id","color_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
    $order_arr = return_library_array("select id, po_number from wo_po_break_down where id in($order_id_all)","id","po_number");
    $internalRef_arr = return_library_array("select id, grouping from wo_po_break_down where id in($order_id_all)","id","grouping");

    $composition_arr=array(); $constructtion_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($febric_description_id_all)";
    $data_array=sql_select($sql_deter);
    foreach( $data_array as $row )
    {
        if(array_key_exists($row[csf('id')],$composition_arr))
        {
            $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
        }
        else
        {
            $composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
        }
    }

    $feb_des_data = sql_select("SELECT b.id as dtls_id, a.wo_no as booking_no,b.fabric_description as fab_des_id,b.fabric_source
    from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c where a.id = b.wo_id and b.fab_booking_no = c.booking_no and a.company_id = $data[1] and  a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and c.status_active =1 and a.wo_no in($booking_no_all) and b.fabric_description in($febric_description_id_all)  
    union all
    select b.id as dtls_id, a.booking_no, b.fab_des_id,b.fabric_source
    from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c 
    where a.id = b.mst_id and b.fab_des_id =  c.id and a.company_id = $data[1]  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and c.status_active =1 and a.booking_no in($booking_no_all) and b.fab_des_id in($febric_description_id_all)");
    $feb_description_datas="";
    foreach ($feb_des_data as $value) 
    {
        $feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["fabric_source"]= $value[csf("fabric_source")];
        $feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["feb_des_id"]= $value[csf("fab_des_id")];
        $feb_description_datas.=$value[csf("fab_des_id")].',';
    }

    $feb_description_data=chop($feb_description_datas,',');
    if($feb_description_data!=""){$feb_description_data_cond=" and a.id in($feb_description_data)";}else{$feb_description_data_cond="";}
    if($feb_description_data!=""){$feb_description_data_cond_2=" and c.id in($feb_description_data)";}else{$feb_description_data_cond_2="";}


    $lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13 and id in($prod_id_all)",'id','product_name_details');

    if($febric_description_id_all!="")
    {
        $sql_order_feb=sql_select("SELECT c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 and c.id in($febric_description_id_all) $feb_description_data_cond_2 order by c.id");
        foreach($sql_order_feb as $row)
        {
            $fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];  
        }
    }

    if($datas[1]==1 && $febric_description_id_all!="")
    {
        $sql_non_order=sql_select("SELECT a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width 
        from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a 
        where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in($febric_description_id_all) $feb_description_data_cond order by a.id");
        
        //echo "select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $feb_description_data_cond order by a.id";
        foreach($sql_non_order as $row)
        {
            $fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
        }
    }

    //==============================================

    $previousIssueArrNew = array();
    $previousIssueRes=sql_select("SELECT a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id 
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
    where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91  and a.booking_dtls_id in($booking_dtls_id) ");
    foreach($previousIssueRes as $row)
    {
        $previousIssueArrNew[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]+=$row[csf('batch_issue_qty')];
    }

    $previousReceiveArrNew = array();
    $previousReceiveRes=sql_select("SELECT a.grey_used,a.order_id,a.booking_no,a.body_part_id,a.color_id,a.febric_description_id as deter_id,a.booking_dtls_id,a.id  
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b
    where a.mst_id = b.id  and a.booking_dtls_id in($booking_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.entry_form = 92");
    foreach($previousReceiveRes as $row)
    {
        $previousReceiveArrNew[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]+=$row[csf('grey_used')];
    }

    $previousReturnArrNew = array();
    $previousReturn=sql_select("SELECT a.batch_issue_qty,a.order_id,a.booking_no,a.body_part_id,a.color_id,a.febric_description_id as deter_id,a.booking_dtls_id,a.id  
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b
    where a.mst_id = b.id  and a.booking_dtls_id in($booking_dtls_id) and b.id !=$datas[0] and a.status_active=1 and a.is_deleted=0 and b.entry_form = 554");
    foreach($previousReturn as $row)
    {
        $previousReturnArrNew[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]+=$row[csf('batch_issue_qty')];
    }

    $total_row=count($sql);
    $current_row_array=array();
    $i=1;
    foreach($sql as $val)
    {
        if ($i%2==0)  
            $bgcolor="#E9F3FF";
        else
            $bgcolor="#FFFFFF";
        $batch_name=$val[csf("batch_no")];
        $gsm=$val[csf("gsm")];
        $dia=$val[csf("width")];

        $feb_des_id = $feb_des_array[$val[csf("booking_no")]][$val[csf("booking_dtls_id")]]["feb_des_id"];
        $feb_des_source = $feb_des_array[$val[csf("booking_no")]][$val[csf("booking_dtls_id")]]["fabric_source"];

        if($val[csf("order_id")] != "")
        {
            $fabric_details = $composition_arr[$val[csf("febric_description_id")]];
        }
        else
        {

            if($feb_des_id == "")
            {
                $fabric_details = $composition_arr[$val[csf("febric_description_id")]];
            }
            else
            {
                if($feb_des_source == 1)
                {
                    $fabric_details = $fabric_description[$feb_des_id];
                }else{
                    $fabric_details = $fabric_description2[$feb_des_id];
                }
            //$fabric_details = $composition_arr[$val[csf("febric_description_id")]];
            }
        }        

        $previousIssueQty=$previousIssueArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];
        $previousReceiveQty=$previousReceiveArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];
        $previous_total_return_qty=$previousReturnArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];

        $balance = (($previousIssueQty - $previousReceiveQty) - $previous_total_return_qty);
        // $balance = ($previousIssueQty - $previousReceiveQty)-$previous_total_return_qty;
        /*issue = 100
        recv = 10
        balance = 90 (issue - recv)
        return = 20
        new balance = 90-20 = 70 ((issue - recv)-return)*/

        ?>
        <tr id="tr_<? echo $i; ?>" align="center" valign="middle">
            <td width="25" id="sl_<? echo $i; ?>"><?=$i;?></td>
            <td width="60" style="word-break:break-all;"  name="txtBatchNo[]" id="txtBatchNo_<? echo $i; ?>"><?=$val[csf('outbound_batchname')];?></td>
            <td style="word-break:break-all;" width="80" id="bodyPart_<? echo $i; ?>"><? echo $body_part[$val[csf("body_part_id")]]; ?></td>
            <td style="word-break:break-all;" width="120" id="<? echo $i; ?>" align="left"><? echo $fabric_details;//$composition_arr[$val[csf("febric_description_id")]]; ?></td>
            <td style="word-break:break-all;" width="50" id="gsm_<? echo $i; ?>"><? echo  $gsm; ?></td>
            <td style="word-break:break-all;" width="50" id="dia_<? echo $i; ?>"><? echo $dia;?></td>
            <td style="word-break:break-all;" width="70" id="color_<? echo $i; ?>"><? echo $color_arr[$val[csf("color_id")]]; ?></td>
            <td style="word-break:break-all;" width="70" id="fabColor_<? echo $i; ?>"><?
                if($row[csf('booking_without_order')] !=1)
                {
                    echo $color_arr[$val[csf("fabric_color_id")]]; 
                }else{
                    echo $color_arr[$val[csf("color_id")]]; 
                }?>
            </td>
            <td style="word-break:break-all;" width="60" id="diaType_<? echo $i; ?>"><? echo $fabric_typee[$val[csf("width_dai_type")]]; ?></td>            
            <td width="120" align="right" id="">
                <? 
                    echo create_drop_down( "cboProcess_$i", 120, $conversion_cost_head_array,"", 1, "-- Select Process --",$val[csf('process_id')] , "",1,"","","","","","","cboProcess[]" );
                ?>
            </td>
            <td width="60" style="word-break:break-all;" id="batchWeight_<? echo $i; ?>"><? echo $val[csf("batch_wgt")]; ?></td>
            <td width="60" id="txtRollNo_<? echo $i; ?>"><? echo  $val[csf("roll_no")]; ?></td>
            
            <td width="60" align="center" id=""><input type="text" id="txtReturnQty_<? echo $i; ?>" name="txtReturnQty[]" style=" width:40px" class="text_boxes_numeric" onKeyUp="calculate_amount(<?echo $i?>);" placeholder="<? echo $balance;?>" value="<? echo $val[csf("batch_issue_qty")]; ?>"/></td>
            
            <td style="word-break:break-all;" width="90" id="bookingNo_<? echo $i; ?>" align="left"><? echo $val[csf("booking_no")]; ?></td>
            <td style="word-break:break-all;" width="60" id="buyer_<? echo $i; ?>"><? echo $buyer_arr[$val[csf("buyer_id")]]; ?></td>
            <td style="word-break:break-all;" width="80" id="job_<? echo $i; ?>"><? echo $val[csf("job_no")]; ?></td>
            <td style="word-break:break-all;" width="100" id="order_<? echo $i; ?>" align="left"><? echo $order_arr[$val[csf("order_id")]]; ?></td>
            <td style="word-break:break-all;" width="100" id="internalRef_<? echo $i; ?>" align="left"><? echo $internalRef_arr[$val[csf("order_id")]]; ?></td>
            <td style="word-break:break-all;" width="" align="left"><input type="text" id="txtRemarks_<?php echo $i; ?>" name="txtRemarks[]" value="<? echo $val[csf("remarks")]; ?>"  class="text_boxes">
                <input type="hidden" name="hiddnJobNo[]" id="hiddnJobNo_<? echo $i; ?>" value="<? echo $val[csf("job_no")]?>"/>
                <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $val[csf("order_id")]; ?>"/>
                <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $val[csf("color_id")]; ?>"/>
                <input type="hidden" name="batchWgt[]" id="batchWgt_<? echo $i; ?>" value="<? echo $val[csf("batch_wgt")]; ?>"/>
                <input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<? echo $val[csf("batch_id")]; ?>"/>
                <input type="hidden" name="bodypartId[]" id="bodypartId_<? echo $i; ?>" value="<? echo $val[csf("body_part_id")]; ?>"/>
                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" value="<? echo $val[csf("buyer_id")]; ?>"/>
                <input type="hidden" name="determinationId[]" id="determinationId_<? echo $i; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
                <input type="hidden" name="widthTypeId[]" id="widthTypeId_<? echo $i; ?>" value="<? echo $val[csf("width_dai_type")]; ?>"/>
                <input type="hidden" name="finDia[]" id="finDia_<? echo $i; ?>" value="<? echo $dia; ?>"/>
                <input type="hidden" name="finGsm[]" id="finGsm_<? echo $i; ?>" value="<? echo $gsm; ?>"/>
                <input type="hidden" name="txtBookingNo[]" id="txtBookingNo_<? echo $i; ?>" value="<? echo $val[csf("booking_no")]; ?>"/>
                <input type="hidden" name="woRate[]" id="woRate_<? echo $i; ?>" value="<? echo $val[csf("rate")]; ?>"/>
                <?  $bookingType =  ($val[csf("booking_without_order")] == 1) ? "2" : "";?> 
                <input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $i; ?>" value="<? echo $bookingType; ?>"/>                
                <input type="hidden" name="bookingDtlsId[]" id="bookingDtlsId_<? echo $i; ?>" value="<? echo $val[csf("booking_dtls_id")]; ?>"/>
                <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $val[csf("id")]; ?>"/>

                <input type="hidden" name="totalReturnQty[]" id="totalReturnQty_<? echo $i; ?>" value="<? echo $previous_total_return_qty; ?>"/>
                <input type="hidden" name="txtReturnQtyHidden[]" id="txtReturnQtyHidden_<? echo $i; ?>" value="<? echo $val[csf("batch_issue_qty")]; ?>"/>
            </td>            
        </tr>
        <?
        $i++;
    }
    exit();
}

if($action=="return_popup")
{
    echo load_html_head_contents("Issue Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?> 

    <script>
        function js_set_value(id,order_type)
        {
            $('#hidden_system_id').val(id);
            $('#hidden_order_type').val(order_type);
            parent.emailwindow.hide();
        }
    </script>

    </head>

    <body>
    <div align="center" style="width:760px;">
        <form name="searchwofrm"  id="searchwofrm">
            <fieldset style="width:760px; margin-left:2px">
                <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>Return. Date Range</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="180">Please Enter Recv No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_system_id" id="hidden_system_id">  
                            <input type="hidden" name="hidden_order_type" id="hidden_order_type"> 
                        </th> 
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>
                        <td align="center"> 
                            <?
                                $search_by_arr=array(1=>"Return No",2=>"Wo No");
                                $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";                         
                                echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
                            ?>
                        </td>     
                        <td align="center" id="search_by_td">               
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />   
                        </td>                       
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_return_challan_search_list_view', 'search_div', 'fabric_issue_to_finishing_process_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                         </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
               </table>
               <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_return_challan_search_list_view")
{
    $data = explode("_",$data);
    
    $search_string="%".trim($data[0]);
    $search_by=$data[1];
    $start_date =$data[2];
    $end_date =$data[3];
    $company_id =$data[4];
	//echo $search_by.'DDSSA';


    if($start_date!="" && $end_date!="")
    {
        if($db_type==0)
        {
            $date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
        }
    }
    else
    {
        $date_cond="";
    }
    
    $search_field_cond="";
    if(trim($data[0])!="")
    {
        if($search_by==1) $search_field_cond="and a.recv_number like '$search_string'";
    }
    
    if($db_type==0) 
    {
        $year_field="YEAR(a.insert_date) as year,";
    }
    else if($db_type==2) 
    {
        $year_field="to_char(a.insert_date,'YYYY') as year,";
    }
    else $year_field="";//defined Later
	
	$booking_cond="";
	if($search_by==2 && trim($data[0])!="")
	{
			$booking_cond="and b.booking_no like '%".trim($data[0])."%'";
	}
    
    $sql = "SELECT a.id, a.challan_no, $year_field a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order 
    from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
    where a.id=b.mst_id and a.entry_form=554 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $booking_cond
    group by a.id, a.challan_no, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order
    order by id"; 
    
    $result = sql_select($sql);

    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    $batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Return No</th>
            <th width="60">Year</th>
            <th width="140">Service Source</th>
            <th width="160">Service Company</th>
            <th width="100">Challan No</th>
            <th>Return Date</th>
        </thead>
    </table>
    <div style="width:720px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_list_search">  
            <?
            $i=1;
            foreach ($result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
                 
                $dye_comp="&nbsp;";
                if($row[csf('dyeing_source')]==1)
                    $dye_comp=$company_arr[$row[csf('dyeing_company')]]; 
                else
                    $dye_comp=$supllier_arr[$row[csf('dyeing_company')]];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('booking_without_order')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="140"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                    <td width="160"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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


if($action=="populate_data_from_data")
{
    $sql = "SELECT id, company_id, recv_number, dyeing_source, dyeing_company, receive_date, challan_no, gray_issue_challan_no as issue_no, issue_id, wo_no, remarks
    from inv_receive_mas_batchroll where id=$data and entry_form=554 and status_active=1 and is_deleted=0";
    //echo $sql;
    $res = sql_select($sql);    
    foreach($res as $row)
    {       
        echo "$('#txt_return_no').val('".$row[csf("recv_number")]."');\n";
        echo "$('#txt_issue_no').val('".$row[csf("issue_no")]."');\n";
        echo "$('#txt_issue_no').attr('disabled','true')".";\n";
        echo "$('#txt_issue_id').val('".$row[csf("issue_id")]."');\n";
        echo "$('#txt_woorder_no').val('".$row[csf("wo_no")]."');\n";
        echo "$('#txt_woorder_no').attr('disabled','true')".";\n";
        echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
        echo "$('#cbo_company_id').attr('disabled','true')".";\n";
        
        echo "$('#txt_return_date').val('".change_date_format($row[csf("receive_date")])."');\n";
        echo "$('#cbo_service_source').val(".$row[csf("dyeing_source")].");\n";
        echo "$('#cbo_service_source').attr('disabled','true')".";\n";
        echo "load_drop_down( 'requires/fabric_issue_to_finishing_process_return_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
        echo "$('#cbo_service_company').val(".$row[csf("dyeing_company")].");\n";
        echo "$('#cbo_service_company').attr('disabled','true')".";\n";
        echo "$('#txt_return_challan').val('".$row[csf("challan_no")]."');\n";
        echo "$('#update_id').val(".$row[csf("id")].");\n";
        echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
    }
    exit(); 
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    //echo "10**".$operation;die;

    for($j=1;$j<=$tot_row;$j++)
    {
        $ReturnQty="txtReturnQty_".$j;
        $bookingDtlsId="bookingDtlsId_".$j;
        $allBookingDtlsId.=$$bookingDtlsId.",";
    }
    $allBookingDtlsId=chop($allBookingDtlsId,',');
    
    $without_current_return="";
    if (str_replace("'","",$update_id)!="") 
    {
        $without_current_return=" and b.id != $update_id ";
    }

    // ======== Issue =======================
    $previousIssueArrNew = array();
    $previousIssueRes=sql_select("SELECT a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id 
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
    where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91  and a.booking_dtls_id in($allBookingDtlsId) ");
    foreach($previousIssueRes as $row)
    {
        $previousIssueArrNew[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]+=$row[csf('batch_issue_qty')];
    }

    // ======== Receive ======================
    $previousReceiveArrNew = array();
    $previousReceiveRes=sql_select("SELECT a.grey_used,a.order_id,a.booking_no,a.body_part_id,a.color_id,a.febric_description_id as deter_id,a.booking_dtls_id,a.id  
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b
    where a.mst_id = b.id  and a.booking_dtls_id in($allBookingDtlsId) and a.status_active=1 and a.is_deleted=0 and b.entry_form = 92");
    foreach($previousReceiveRes as $row)
    {
        $previousReceiveArrNew[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]+=$row[csf('grey_used')];
    }

    // ======== Return ======================
    $previousReturnArrNew = array();
    $previousReturn=sql_select("SELECT a.batch_issue_qty,a.order_id,a.booking_no,a.body_part_id,a.color_id,a.febric_description_id as deter_id,a.booking_dtls_id,a.id  
    from pro_grey_batch_dtls a, inv_receive_mas_batchroll b
    where a.mst_id = b.id  and a.booking_dtls_id in($allBookingDtlsId) $without_current_return and a.status_active=1 and a.is_deleted=0 and b.entry_form = 554");
    foreach($previousReturn as $row)
    {
        $previousReturnArrNew[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]+=$row[csf('batch_issue_qty')];
    }

    if ($operation==0)  // Insert Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        // ======================================================================================
        
        if($db_type==0) $year_cond="YEAR(insert_date)"; 
        else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
        else $year_cond="";//defined Later
        
        $id= return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll", $con);
	    $new_mrr_number = explode("*", return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'FIFPR',554,date("Y",time()) ));
                 
        $field_array="id,recv_number_prefix,recv_number_prefix_num,recv_number,entry_form, company_id, dyeing_source,batch_id, dyeing_company, receive_date, gray_issue_challan_no, issue_id, wo_no, challan_no, remarks, inserted_by, insert_date";
        if(str_replace("'","",$txt_return_challan)=="") $txt_return_challan=$new_mrr_number[0];
        $data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',554,".$cbo_company_id.",".$cbo_service_source.","."0".",".$cbo_service_company.",".$txt_return_date.",".$txt_issue_no.",".$txt_issue_id.",".$txt_woorder_no.",'".str_replace("'","",$txt_return_challan)."',".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
        
        $field_array_dtls="id, mst_id, outbound_batchname, batch_id, booking_no, booking_without_order, body_part_id, febric_description_id, color_id, process_id, batch_wgt, batch_issue_qty, rate, buyer_id, job_no, order_id, fin_dia, fin_gsm, booking_dtls_id, remarks, inserted_by, insert_date";     
        $all_detailsId='';
        for($j=1;$j<=$tot_row;$j++)
        {
            $dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			$process_id="cboProcess_".$j;
            $deterId="determinationId_".$j;
            $buyerId="buyerId_".$j;
            $orderId="orderId_".$j;
            $batchName="txtBatchNo_".$j;
            $batchId="txtbatchId_".$j;
            $jobNo="jobNo_".$j;
            $colorId="colorId_".$j;
            $txtRate="txtRate_".$j;
			
            $bodyparyId="bodypartId_".$j;
            $ReturnQty="txtReturnQty_".$j;
            $txtRate="txtRate_".$j;
            $bookingNo="bookingNo_".$j;
            $batchQty="batchQty_".$j;
            $finDia="finDia_".$j;
            $finGsm="finGsm_".$j;
            $bookWithoutOrder="bookWithoutOrder_".$j;
            $bookingDtlsId="bookingDtlsId_".$j;
            $txtRemarks="txtRemarks_".$j;

            $trId="tr_".$j;

            $previousIssueQty=$previousIssueArrNew[$$bookingNo][$$bookingDtlsId];
            $previousReceiveQty=$previousReceiveArrNew[$$bookingNo][$$bookingDtlsId];
            $previous_total_return_qty=$previousReturnArrNew[$$bookingNo][$$bookingDtlsId];
            $balance = (($previousIssueQty - $previousReceiveQty) - $previous_total_return_qty);
            // echo $previousIssueQty .'-'. $previousReceiveQty .'-'. $previous_total_return_qty.'<br>';
            // echo $$ReturnQty.'>'.$balance.'<br>';
            if ($$ReturnQty>$balance) 
            {
                echo "20**Sorry! Actual balance qty ". number_format($balance,2,".","");
                disconnect($con);
                die;
            }
        
            if($$ReturnQty!="")
            {
                if($data_array_dtls!="") $data_array_dtls.=",";
                $data_array_dtls.="(".$dtls_id.",".$id.",'".$$batchName."','".$$batchId."','".$$bookingNo."','".$$bookWithoutOrder."','".$$bodyparyId."','".$$deterId."','".$$colorId."','".$$process_id."','".$$batchQty."','".$$ReturnQty."','".$$txtRate."','".$$buyerId."','".$$jobNo."','".$$orderId."','".$$finDia."','".$$finGsm."','".$$bookingDtlsId."','".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
                $all_detailsId.=$$trId."__".$dtls_id.",";
            }          
        }
        
        // echo "10**insert into pro_grey_batch_dtls($field_array_dtls) values".$data_array_dtls;die;
        
        $rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);
        $rID2=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
        // echo "10**$rID && $rID2";die;
        if($db_type==0)
        {
            if($rID && $rID2)
            {
                mysql_query("COMMIT");  
                echo "0**".$id."**".$new_mrr_number[0]."**".substr($all_detailsId,0,-1);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "5**0**0";
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID && $rID2)
            {
                oci_commit($con);  
                echo "0**".$id."**".$new_mrr_number[0]."**".substr($all_detailsId,0,-1);
            }
            else
            {
                oci_rollback($con);
                echo "5**0**0";
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==1) // Update Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        $field_array="receive_date*challan_no*remarks*updated_by*update_date";
        $data_array=$txt_return_date."*".$txt_return_challan."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
	   
        $field_array_dtls="id, mst_id, outbound_batchname, batch_id, booking_no, booking_without_order, body_part_id, febric_description_id, color_id, process_id, batch_wgt, batch_issue_qty, rate, buyer_id, job_no, order_id, fin_dia, fin_gsm, booking_dtls_id, remarks, inserted_by, insert_date";
        $field_array_updatedtls="batch_issue_qty*remarks*updated_by*update_date";        
        
        $all_detailsId='';
        for($j=1;$j<=$tot_row;$j++)
        {
            $dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
		    $process_id="cboProcess_".$j;
            $deterId="determinationId_".$j;
            $buyerId="buyerId_".$j;
            $orderId="orderId_".$j;
            $batchName="txtBatchNo_".$j;
            $batchId="txtbatchId_".$j;
            $jobNo="jobNo_".$j;
            $colorId="colorId_".$j;
            $txtRate="txtRate_".$j;
            
            $bodyparyId="bodypartId_".$j;
            $ReturnQty="txtReturnQty_".$j;
            $txtRate="txtRate_".$j;
            $bookingNo="bookingNo_".$j;
            $batchQty="batchQty_".$j;
            $finDia="finDia_".$j;
            $finGsm="finGsm_".$j;
            $bookWithoutOrder="bookWithoutOrder_".$j;
            $bookingDtlsId="bookingDtlsId_".$j;			  
            $trId="tr_".$j;
            $update_dtls="dtlsId_".$j;
            $txtRemarks="txtRemarks_".$j;

            $previousIssueQty=$previousIssueArrNew[$$bookingNo][$$bookingDtlsId];
            $previousReceiveQty=$previousReceiveArrNew[$$bookingNo][$$bookingDtlsId];
            $previous_total_return_qty=$previousReturnArrNew[$$bookingNo][$$bookingDtlsId];
            $balance = (($previousIssueQty - $previousReceiveQty) - $previous_total_return_qty);
            // echo $previousIssueQty .'-'. $previousReceiveQty .'-'. $previous_total_return_qty.'<br>';
            // echo $$ReturnQty.'>'.$balance.'<br>';
            if ($$ReturnQty>$balance) 
            {
                echo "20**Sorry! Actual balance qty ". number_format($balance,2,".","");
                disconnect($con);
                die;
            }

            if($$ReturnQty!="")
            {
                if($$update_dtls!="")
                {
                    $dtlsId_arr[]=str_replace("'","",$$update_dtls);
                    $data_array_update_dtls[str_replace("'","",$$update_dtls)]=explode("*",("'".$$ReturnQty."'*'".$$txtRemarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
                    $all_detailsId.=$$trId."__".str_replace("'","",$$update_dtls).",";
                }
                else
                {
                    if($data_array_dtls!="") $data_array_dtls.=",";
                    $data_array_dtls.="(".$dtls_id.",".$id.",'".$$batchName."','".$$batchId."','".$$bookingNo."','".$$bookWithoutOrder."','".$$bodyparyId."','".$$deterId."','".$$colorId."','".$$process_id."','".$$batchQty."','".$$ReturnQty."','".$$txtRate."','".$$buyerId."','".$$jobNo."','".$$orderId."','".$$finDia."','".$$finGsm."','".$$bookingDtlsId."','".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
                    $all_detailsId.=$$trId."__".$dtls_id.",";
                }
            }
        }
        // echo '10**string';die;
        $rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
        $rID2=true; $rID3=true;
        if($data_array_dtls!="")
        {
            $rID2=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
        }
        
        if(count($data_array_update_dtls)>0)
        {
            // echo bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr );die;
            $rID3=execute_query(bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
        }
        // echo "10**$rID && $rID3";die;
        if($db_type==0)
        {
            if($rID && $rID2 && $rID3)
            {
                mysql_query("COMMIT");  
                echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_return_no)."**".substr($all_detailsId,0,-1);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "6**".str_replace("'", '', $update_id)."**";
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID && $rID2 && $rID3)
            {
                oci_commit($con);  
                echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_return_no)."**".substr($all_detailsId,0,-1);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id)."**";
            }
        }
        disconnect($con);
        die;
    }
}


if($action=="fabric_receive_print") // Print
{
    extract($_REQUEST);
    $data=explode('*',$data);
    echo 'Need to develop';die;
    $company_id=$data[0];
    $update_id=$data[1];
    $company_array= return_library_array("select id, company_name from lib_company",'id','company_name');
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
    $buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    //print_r($supplier_arr[266]);die;
    $job_array=array();
    $job_sql="select a.buyer_name, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
    $job_sql_result=sql_select($job_sql);
    foreach($job_sql_result as $row)
    {
        $job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
        $job_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
        $job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
    }

    $composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
    $data_array=sql_select($sql_deter);
    foreach( $data_array as $row )
    {
        if(array_key_exists($row[csf('id')],$composition_arr))
        {
            $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
        }
        else
        {
            $composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
        }
    }
        
    ?>
        <div>
            <table width="1000" cellspacing="0">
                <tr>
                    <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$company_id]; ?></strong></td>
                </tr>
                <!-- <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:14px">  
                        <?
                            $company_array=array();
                            $nameArray=sql_select( "select id, company_name, company_short_name,plot_no,level_no,road_no,block_no,country_id, province,city, zip_code,email,website from lib_company where id=$company_id"); 
                            foreach ($nameArray as $result)
                            { 
                                $company_array['shortname']=$result[csf('company_short_name')];
                                $company_array['name']=$result[csf('company_name')];
                            ?>
                                Plot No: <? echo $result['plot_no']; ?> 
                                Level No: <? echo $result['level_no']?>
                                Road No: <? echo $result['road_no']; ?> 
                                Block No: <? echo $result['block_no'];?> 
                                City No: <? echo $result['city'];?> 
                                Zip Code: <? echo $result['zip_code']; ?> 
                                Province No: <?php echo $result['province'];?> 
                                Country: <? echo $country_arr[$result['country_id']]; ?><br> 
                                Email Address: <? echo $result['email'];?> 
                                Website No: <? echo $result['website'];
                            }
                        ?> 
                    </td>  
                </tr> -->

                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:14px">  
                        <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id"); 
                            foreach ($nameArray as $result)
                            { 
                                if($result[csf('plot_no')]!='') echo $result[csf('plot_no')].', ';
                                if($result[csf('level_no')]!='') echo $result[csf('level_no')].', ';
                                if($result[csf('road_no')]!='') echo $result[csf('road_no')].', ';
                                if($result[csf('block_no')]!='') echo $result[csf('block_no')].', ';
                                if($result[csf('city')]!='') echo $result[csf('city')].', ';
                                if($result[csf('zip_code')]!='') echo $result[csf('zip_code')].', ';
                                if($result[csf('province')]!='') echo $result[csf('province')].', ';
                                if($result[csf('country_id')]!=0) echo "&nbsp;".$country_arr[$result[csf('country_id')]].'<br>';
                                if($result[csf('email')]!='') echo "Email No: ".$result[csf('email')].', ';
                                if($result[csf('website')]!='') echo "Website: ".$result[csf('website')];
                            }
                        ?> 
                    </td>  
                </tr>
                <?php 
                     $mst_sql=sql_select( "select id,challan_no, $year_field recv_number_prefix_num, recv_number, dyeing_source, dyeing_company, receive_date from inv_receive_mas_batchroll where id=$update_id and entry_form=92 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"); 
                
                //print_r($mst_sql);
                ?>
                
                <tr>
                    <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
                </tr>
                <tr>
                     <td width="100"><strong>Company</strong> </td>
                     <td width="100">: <? echo $company_array['name'] ;  ?></td>
                    <td width="120"><strong>Service Source </strong></td>
                    <td width="160px">:<? echo $knitting_source[$mst_sql[0][csf('dyeing_source')]]; ?></td>
                    <td width="125"><strong>Service Company:</strong></td>
                    <td width="175px">:
                     <?
                        echo $supplier_arr[$mst_sql[0][csf('dyeing_company')]]; 
                     ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Receive Date</strong></td><td width="175px">:<? echo change_date_format($mst_sql[0][csf('receive_date')]); ?></td>
                    <td><strong>Service Receive No</strong></td><td width="175px">:<? echo $mst_sql[0][csf('recv_number')]; ?></td>
                     <td><strong>Receive Challan No</strong></td><td width="175px">:<? echo $mst_sql[0][csf('challan_no')]; ?></td>
                </tr>
            </table>
            <br>
            <table cellspacing="0" width="1030"  border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="90">Body Part</th>
                    <th width="130">Const./compo</th>
                    <th width="50">Fin Dia/Color</th>
                    <th width="50">Fab. Color</th>
                    <th width="70">Process </th>
                    <th width="100">Booking No</th>
                    <th width="50">Wo. Qty</th>
                    <th width="50">Cur. Rcv. Qty </th>
                    <th width="50">Grey Used Qty </th>
                    <th width="60"> Amount</th>
                    <th width="100">Order</th>
                    <th width="70">Buyer/Job</th>
                </thead>
                <?
                    //$sql="select id, mst_id, outbound_batchname, booking_no, booking_id, body_part_id, febric_description_id, width, color_id, process_id,wo_qty, batch_issue_qty, rate, amount, currency_id, exchange_rate, buyer_id, job_no, order_id, grey_used from pro_grey_batch_dtls where mst_id=$update_id order by id ";
                    $sql="select a.id,a.mst_id,a.outbound_batchname,a.booking_no,a.booking_id,a.body_part_id,a.febric_description_id,a.width,a.color_id, a.process_id,a.wo_qty, a.batch_issue_qty,a.rate,a.amount,a.currency_id,a.exchange_rate,a.buyer_id, a.job_no, a.order_id, a.grey_used , b.fabric_color_id, a.booking_without_order from pro_grey_batch_dtls a left join wo_booking_dtls b on a.booking_dtls_id=b.id where a.mst_id=$update_id order by a.id ";
                    //echo $sql;
                    $i=1;
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
                        if($row[csf("booking_without_order")] !=1){
                            $fabric_color = $color_arr[$row[csf("fabric_color_id")]];
                        }else{
                            $fabric_color ="";
                        }
                    ?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                            <td style="word-break:break-all;"><? echo $composition_arr[$row[csf("febric_description_id")]]; ?></td>
                            <td style="word-break:break-all;"><? echo $row[csf('width')]."<hr/>".$color_arr[$row[csf("color_id")]]; ?></td>
                            <td style="word-break:break-all;"><? echo $fabric_color; ?></td>
                            <td><? echo $conversion_cost_head_array[$row[csf("process_id")]];?></td>
                            <td><? echo $row[csf("booking_no")]; ?></td>
                            <td style="word-break:break-all;" align="right"><? echo $row[csf("wo_qty")]; ?></td>
                            <td style="word-break:break-all;" align="right"><? echo $row[csf("batch_issue_qty")];?></td>
                            <td style="word-break:break-all;" align="right"><? echo  $row[csf("grey_used")] ?></td>
                            <td style="word-break:break-all;" align="right"><? echo  $row[csf("amount")] ?></td>
                            <td style="word-break:break-all;"><? echo $job_array[$row[csf('order_id')]]['po']; ?></td>
                            <td style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]."<hr/>".$row[csf('job_no')]; ?></td>
                        </tr>
                    <?
                        $i++;
                        $total_wo+=$row[csf("wo_qty")];
                        $total_rece+=$row[csf("batch_issue_qty")];
                        $total_grey_used+=$row[csf("grey_used")];
                        $total_amount+=$row[csf("amount")];
                    }
                ?>
                <tfoot>
                    <th width=""></th>
                    <th width=""></th>
                    <th width=""></th>
                    <th width=""></th>
                    <th width=""></th>
                    <th width=""> </th>
                    <th width="">Total</th>
                    <th width="" align="right"><?php echo number_format($total_wo,4);?></th>
                    <th width="" align="right"><?php echo number_format($total_rece,4);?></th>
                    <th width="" align="right"><?php echo number_format($total_grey_used,4);?></th>
                    <th width="" align="right"><?php echo number_format($total_amount,4);?></th>
                    <th width=""></th>
                    <th width=""></th>
                
                </tfoot>
            </table>
        </div>
        <? echo signature_table(17, $company_id, "900px"); ?>
       
    <?
    exit();
}

?>



